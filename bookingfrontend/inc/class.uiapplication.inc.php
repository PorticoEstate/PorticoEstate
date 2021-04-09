<?php
	phpgw::import_class('booking.uiapplication');
	phpgw::import_class('booking.boapplication');
	phpgw::import_class('booking.soapplication');

	class bookingfrontend_uiapplication extends booking_uiapplication
	{

		public $public_functions = array
			(
			'add' => true,
			'add_contact' => true,
            'confirm' => true,
			'delete_partial' => true,
			'edit' => true,
			'show' => true,
			'get_activity_data' => true,
			'get_partials' => true,
			'set_block'		=> true
		);

		function get_activity_data()
		{
			return parent::get_activity_data();
		}

		function show()
		{
			$id = phpgw::get_var('id', 'int');
			$secret =  phpgw::get_var('secret', 'string');
			$config = CreateObject('phpgwapi.config', 'booking')->read();

			/**
			 * check external login - and return here
			 */
			$bouser = CreateObject('bookingfrontend.bouser');

			$bouser->validate_ssn_login(
				array
				(
					'menuaction' => 'bookingfrontend.uiapplication.show',
					'id'		=> $id,
					'secret'	=> $secret
				)
			);

			$application = $this->bo->read_single($id);

			if ($application['secret'] != $secret)
			{
				self::redirect(array('menuaction' => 'bookingfrontend.uisearch.index'));
			}

			$comment = phpgw::get_var('comment', 'html', 'POST');

			if ($_SERVER['REQUEST_METHOD'] == 'POST' && $comment)
			{
				$this->add_comment($application, $comment);
				$this->set_display_in_dashboard($application, true, array('force' => true));
				$application['frontend_modified'] = 'now';
				$this->bo->send_admin_notification($application, $comment);

				$receipt = $this->bo->update($application);
				self::redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $application['id'],
					'secret' => $application['secret']));
			}
			/** Start attachment * */
			if ($_FILES)
			{
				$document_application = createObject('booking.uidocument_application');

				$oldfiles = $document_application->bo->so->read(array('filters' => array('owner_id' => $application['id'])));
				$files = $this->get_files_from_post();
				$file_exist = false;

				if ($oldfiles['results'])
				{
					foreach ($oldfiles['results'] as $old_file)
					{
						if ($old_file['name'] == $files['name']['name'])
						{
							$file_exist = true;
							phpgwapi_cache::message_set(lang('file exists'));
							break;
						}
					}
				}

				$document = array(
					'category' => 'other',
					'owner_id' => $application['id'],
					'files' => $this->get_files_from_post()
				);
				$document_errors = $document_application->bo->validate($document);

				if (!$document_errors && !$file_exist)
				{
					try
					{
						booking_bocommon_authorized::disable_authorization();
						$document_receipt = $document_application->bo->add($document);
					}
					catch (booking_unauthorized_exception $e)
					{
						phpgwapi_cache::message_set(lang('Could not add object due to insufficient permissions'));
					}
				}
			}
			/** End attachment * */
			$building_info = $this->bo->so->get_building_info($id);
			$application['building_id'] = $building_info['id'];
			$application['building_name'] = $building_info['name'];

			if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['print'])
			{
				$output_type = 'PDF';
				$jasper_parameters = sprintf("\"BK_BUILDING_NAME|%s;BK_APPLICATION_ID|%s\"", $application['building_name'], $id);
				// DEBUG
				// print_r($jasper_parameters);
				// exit(0);

				$jasper_wrapper = CreateObject('phpgwapi.jasper_wrapper');
				$report_source = PHPGW_SERVER_ROOT . '/booking/jasper/templates/application.jrxml';
				try
				{
					$jasper_wrapper->execute($jasper_parameters, $output_type, $report_source);
				}
				catch (Exception $e)
				{
					$errors[] = $e->getMessage();
					echo "<pre>\nError: ";
					print_r($errors[0]);
					exit;
				}
			}

			$resource_ids = '';
			foreach ($application['resources'] as $res)
			{
				$resource_ids = $resource_ids . '&filter_id[]=' . $res;
			}

			$simple = false;

			if ($resource_ids)
			{
				$resource_filters			 = array('active' => 1, 'rescategory_active' => 1, 'id' => $application['resources']);
				$_resources					 = $this->resource_bo->so->read(array('filters' => $resource_filters, 'sort' => 'sort', 'results' => -1));
				$_building_simple_booking	 = 0;
				foreach ($_resources['results'] as $_resource)
				{
					if ($_resource['simple_booking'] == 1)
					{
						$_building_simple_booking++;
					}
				}

				if ($_building_simple_booking == count($application['resources']))
				{
					$simple = true;
				}
			}

			//Filter application comments only after update has been attempted unless
			//you wish to delete the comments not matching the specified types
			$this->filter_application_comments($application, array('comment'));

			$activity_path = $this->activity_bo->get_path($application['activity_id']);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : -1;

			$application['resource_ids'] = $resource_ids;
			$application['description'] = html_entity_decode(nl2br($application['description']));
			$application['equipment'] = html_entity_decode(nl2br($application['equipment']));
			$application['copy_link'] = self::link(array('menuaction' => "{$this->module}.uiapplication.add", 'application_id' => $application['id']));

			if(!empty($application['comments']))
			{
				foreach ($application['comments'] as  &$comments)
				{
					$comments['comment'] = html_entity_decode(nl2br($comments['comment']));
				}
			}

			$agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity);
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience($top_level_activity);
			$audience = $audience['results'];

			$this->assoc_bo = new booking_boapplication_association();
			$associations = $this->assoc_bo->so->read(array('filters' => array('application_id' => $application['id']),
				'sort' => 'from_', 'dir' => 'asc', 'results' =>'all'))['results'];

			$lang_expired = lang('expired for edit');
			$lang_edit = lang('edit');
			$lang_cancel = lang('Cancel booking');
			$lang_rights = lang('Missing rights');

			foreach ($associations as &$association)
			{
				if ($association['active'] === 1)
				{
					if ($association['type'] === 'allocation')
					{
						if ($association['from_'] > Date('Y-m-d H:i:s'))
						{
							$association['edit_link'] = self::link(array('menuaction' => "{$this->module}.uiallocation.edit", 'allocation_id' => $association['id']));
						}
						else
						{
							$association['edit_link'] = '#';
							$association['edit_text'] = $lang_expired;
						}
						$association['edit_text'] = $lang_edit;
						if ($config['user_can_delete_allocations'] == 'yes')
						{
							if ($association['from_'] > Date('Y-m-d H:i:s'))
							{
								$association['cancel_link'] = self::link(array('menuaction' => "{$this->module}.uiallocation.cancel", 'allocation_id' => $association['id']));
								$association['cancel_text'] = $lang_cancel;
							}
							else
							{
								$association['cancel_link'] = '#';
								$association['cancel_text'] = $lang_expired;
							}
						}
						else
							{
							$association['cancel_link'] = '#';
							$association['cancel_text'] = $lang_rights;
						}
					}
					if ($association['type'] === 'event')
					{
						if ($association['from_'] > Date('Y-m-d H:i:s'))
						{
							$association['edit_link'] = self::link(array('menuaction' => "{$this->module}.uievent.edit",
								'id' => $association['id'],
								'resource_ids' => $application['resources']));
							$association['edit_text'] = $lang_edit;
						}
						else
						{
							$association['edit_link'] = '#';
							$association['edit_text'] = $lang_expired;
						}

						if ($config['user_can_delete_events'] == 'yes')
						{
							if ($association['from_'] > Date('Y-m-d H:i:s'))
							{
								$association['cancel_link'] = self::link(array('menuaction' => "{$this->module}.uievent.cancel",
									'id' => $association['id'],
									'resource_ids' => $application['resources']));
								$association['cancel_text'] = $lang_cancel;
							}
							else
							{
								$association['cancel_link'] = '#';
								$association['cancel_text'] = $lang_expired;
							}

						}
						else
						{
							$association['cancel_link'] = '#';
							$association['cancel_text'] = $lang_rights;
						}
					}
					if ($association['type'] === 'booking')
					{
						if ($association['from_'] > Date('Y-m-d H:i:s'))
						{
							$association['edit_link'] = self::link(array('menuaction' => "{$this->module}.uibooking.edit",
								'id' => $association['id'],
								'resource_ids' => $application['resources']));
							$association['edit_text'] = $lang_edit;
						}
						else
						{
							$association['edit_link'] = '#';
							$association['edit_text'] = $lang_expired;
						}

						if ($config['user_can_delete_bookings'] == 'yes')
						{
							if ($association['from_'] > Date('Y-m-d H:i:s'))
							{
								$association['cancel_link'] = self::link(array('menuaction' => "{$this->module}.uibooking.cancel",
									'id' => $association['id'],
									'resource_ids' => $application['resources']));
								$association['cancel_text'] = $lang_cancel;
							}
							else
							{
								$association['cancel_link'] = '#';
								$association['cancel_text'] = $lang_expired;
							}
						}
						else
						{
							$association['cancel_link'] = '#';
							$association['cancel_text'] = $lang_rights;
						}
					}
				}
				else
				{
					$association['edit_link'] = '#';
					$association['edit_text'] = $lang_expired;
					$association['cancel_link'] = '#';
					$association['cancel_text'] = $lang_expired;
				}

				$association['type'] = lang($association['type'].' show');
				$association['from_'] = DateTime::createFromFormat('Y-m-d H:i:s', $association['from_'])->format('d.m.Y H:i');
				$association['to_'] = DateTime::createFromFormat('Y-m-d H:i:s', $association['to_'])->format('d.m.Y H:i');
			}

			phpgwapi_jquery::formvalidator_generate(array('file'), 'file_form');

			self::render_template_xsl('application', array(
				'application'	 => $application,
				'audience'		 => $audience,
				'agegroups'		 => $agegroups,
				'frontend'		 => 'true',
				'simple'		 => $simple,
				'associations'	 => $associations,
				'config'		 => CreateObject('phpgwapi.config', 'booking')->read()

				)
			);
		}

	}
