<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.account_helper');

	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');

//	phpgw::import_class('phpgwapi.uicommon_jquery');

	class booking_uiapplication extends booking_uicommon
	{

		const COMMENT_TYPE_OWNERSHIP = 'ownership';
		const ORGNR_SESSION_KEY = 'orgnr';

		public $public_functions = array
			(
			'index' => true,
			'query' => true,
			'add' => true,
			'show' => true,
			'edit' => true,
			'associated' => true,
			'toggle_show_inactive' => true,
			'custom_fields_example' => true
		);
		protected $customer_id,
			$default_module = 'bookingfrontend',
			$module;

		public function __construct()
		{
			parent::__construct();

			phpgwapi_jquery::load_widget('autocomplete');

			$this->set_module();
//			Analizar esta linea self::process_booking_unauthorized_exceptions();
			$this->bo = CreateObject('booking.boapplication');
			$this->customer_id = CreateObject('booking.customer_identifier');
			$this->event_bo = CreateObject('booking.boevent');
			$this->activity_bo = CreateObject('booking.boactivity');
			$this->audience_bo = CreateObject('booking.boaudience');
			$this->assoc_bo = new booking_boapplication_association();
			$this->agegroup_bo = CreateObject('booking.boagegroup');
			$this->resource_bo = CreateObject('booking.boresource');
			$this->building_bo = CreateObject('booking.bobuilding');
			$this->organization_bo = CreateObject('booking.boorganization');
			$this->document_building = CreateObject('booking.bodocument_building');
			$this->document_resource = CreateObject('booking.bodocument_resource');

			self::set_active_menu('booking::applications');
			$this->fields = array('description', 'equipment', 'resources', 'activity_id',
				'building_id', 'building_name', 'contact_name',
				'contact_email', 'contact_phone', 'audience',
				'active', 'accepted_documents');
		}
		// --- SIGURD::START EXAMPLE -- //

		/**
		 * Example on how to retrieve custom fields organized in nested groups
		 */
		public function custom_fields_example()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$locations = $GLOBALS['phpgw']->locations->get_locations($grant = false, 'booking', $allow_c_attrib = true, $allow_c_functions = false);
			echo "Available locations within Booking:</br>";

			foreach ($locations as $location => $name)
			{
				if (!preg_match('/(^.application|^.resource)/i', $location))
				{
					continue;
				}

				echo "<b>{$location}::{$name}:</b></br>";
				echo "Custom fields:</br>";

				// THIS ONE IS WHAT YOU WANT
				$organized_fields = $this->get_attributes($location);

				$url_acl = self::link(array('menuaction' => 'preferences.uiadmin_acl.list_acl',
						'acl_app' => 'booking', 'module' => $location, 'cat_id' => 'groups'));
				$url_groups = self::link(array('menuaction' => 'admin.ui_custom.list_attribute_group',
						'appname' => 'booking', 'location' => $location, 'menu_selection' => 'booking::settings::custom_field_groups'));
				$url_fields = self::link(array('menuaction' => 'admin.ui_custom.list_attribute',
						'appname' => 'booking', 'location' => $location, 'menu_selection' => 'booking::settings::custom_field_groups'));

				if (count($organized_fields) > 1)
				{
					_debug_array($organized_fields);
					echo "<a href='{$url_groups}'>Make more groups here</a></br>";
					echo "<a href='{$url_fields}'>And fields here</a></br>";
				}
				else
				{
					echo "No custom fields is defined... yet..</br>";
					echo "<a href='{$url_acl}'>Define your acl rights here</a></br>";
					echo "<a href='{$url_groups}'>Make groups here</a></br>";
					echo "<a href='{$url_fields}'>And fields here</a></br>";
				}
			}
		}

		/**
		 *
		 * @param type $location
		 * @return  array the grouped attributes
		 */
		private function get_attributes( $location )
		{
			$appname = 'booking';
			$attributes = $GLOBALS['phpgw']->custom_fields->find($appname, $location, 0, '', 'ASC', 'attrib_sort', true, true);
			return $this->get_attribute_groups($appname, $location, $attributes);
		}

		/**
		 * Arrange attributes within groups
		 *
		 * @param string  $location    the name of the location of the attribute
		 * @param array   $attributes  the array of the attributes to be grouped
		 *
		 * @return array the grouped attributes
		 */
		private function get_attribute_groups( $appname, $location, $attributes = array() )
		{
			return $GLOBALS['phpgw']->custom_fields->get_attribute_groups($appname, $location, $attributes);
		}

		// --- END EXAMPLE -- //

		protected function set_module( $module = null )
		{
			$this->module = is_string($module) ? $module : $this->default_module;
		}

		public function get_module()
		{
			return $this->module;
		}

		protected function is_assigned_to_current_user( &$application )
		{
			$current_account_id = $this->current_account_id();
			if (empty($current_account_id) || !isset($application['case_officer_id']))
			{
				return false;
			}
			return $application['case_officer_id'] == $current_account_id;
		}

		protected function check_application_assigned_to_current_user( &$application )
		{
			if (!$this->is_assigned_to_current_user($application))
			{
				throw new booking_unauthorized_exception('write', 'current user is not assigned to application');
			}

			return true;
		}

		protected function assign_to_current_user( &$application )
		{
			$current_account_id = $this->current_account_id();

			if (!empty($current_account_id) && is_array($application) &&
				!isset($application['case_officer_id']) || $application['case_officer_id'] != $current_account_id)
			{
				$application['case_officer_id'] = $current_account_id;
				$this->add_ownership_change_comment($application, sprintf(lang("User '%s' was assigned"), $this->current_account_fullname()));
				return true;
			}

			return false;
		}

		protected function unassign_current_user( &$application )
		{
			$current_account_id = $this->current_account_id();

			if (!empty($current_account_id) && is_array($application) && array_key_exists('case_officer_id', $application) && $application['case_officer_id'] == $current_account_id)
			{
				$application['case_officer_id'] = null;
				$this->add_ownership_change_comment($application, sprintf(lang("User '%s' was unassigned"), $this->current_account_fullname()));
				return true;
			}

			return false;
		}

		protected function set_display_in_dashboard( &$application, $bool, $options = array() )
		{
			if (!is_bool($bool) || $application['display_in_dashboard'] === $bool)
			{
				return false;
			}
			$options = array_merge(
				array('force' => false), $options
			);

			if ($options['force'] === false &&
				(!isset($application['case_officer_id']) || $application['case_officer_id'] != $this->current_account_id())
			)
			{
				return false;
			}

			$application['display_in_dashboard'] = ($bool === true ? 1 : 0);
			return true;
		}

		protected function add_comment( &$application, $comment, $type = 'comment' )
		{
			$application['comments'][] = array(
				'time' => 'now',
				'author' => $this->current_account_fullname(),
				'comment' => $comment,
				'type' => $type
			);
		}

		protected function add_ownership_change_comment( &$application, $comment )
		{
			$this->add_comment($application, $comment, self::COMMENT_TYPE_OWNERSHIP);
		}

		/**
		 * Filters application comments based on their types.
		 * 
		 *
		 */
		protected function filter_application_comments( array &$application, array $types )
		{
			$types = array_fill_keys($types, true); //Convert to associative array with types as keys and values as true

			if (count($types) == 0 || !array_key_exists('comments', $application) || !is_array($application['comments']))
			{
				return;
			}

			$filtered_comments = array();
			foreach ($application['comments'] as &$comment)
			{
				isset($types[$comment['type']]) AND $filtered_comments[] = $comment;
			}
			$application['comments'] = $filtered_comments;
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
			phpgwapi_jquery::load_widget('autocomplete');

			$data = array(
				'datatable_name' => lang('application'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'filter',
								'name' => 'status',
								'text' => lang('Status') . ':',
								'list' => array(
									array(
										'id' => 'none',
										'name' => lang('Not selected')
									),
									array(
										'id' => 'NEW',
										'name' => lang('NEW'),
										'selected' => 1
									),
									array(
										'id' => 'PENDING',
										'name' => lang('PENDING')
									),
									array(
										'id' => 'REJECTED',
										'name' => lang('REJECTED')
									),
									array(
										'id' => 'ACCEPTED',
										'name' => lang('ACCEPTED')
									)
								)
							),
/*							array('type' => 'filter',
								'name' => 'buildings',
								'text' => lang('Building') . ':',
								'list' => $this->bo->so->get_buildings(),
							),
*/
							array('type' => 'autocomplete',
								'name' => 'building',
								'ui' => 'building',
								'text' => lang('Building') . ':',
								'onItemSelect' => 'updateBuildingFilter',
								'onClearSelection' => 'clearBuildingFilter'
							),
							array('type' => 'filter',
								'name' => 'activities',
								'text' => lang('Activity') . ':',
								'list' => $this->bo->so->get_activities_main_level(),
							),
						/*	array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix . '.toggle_show_inactive'))
							),*/
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uiapplication.index',
						'phpgw_return_as' => 'json')),
					'sorted_by' => array('key' => 0, 'dir' => 'desc'),//id
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'status',
							'label' => lang('Status')
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building')
						),
						array(
							'key' => 'what',
							'label' => lang('What'),
							'sortable' => false
						),
						array(
							'key' => 'created',
							'label' => lang('Created')
						),
						array(
							'key' => 'modified',
							'label' => lang('last modified')
						),
						array(
							'key' => 'from_',
							'label' => lang('From'),
							'sortable' => false
						),
						array(
							'key' => 'activity_name',
							'label' => lang('Activity')
						),
						array(
							'key' => 'contact_name',
							'label' => lang('Contact')
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);

			$data['datatable']['new_item'] = self::link(array('menuaction' => 'booking.uiapplication.add'));
			$data['datatable']['actions'][] = array();

			self::render_template_xsl('datatable_jquery', $data);
//			self::render_template('datatable', $data);
		}

		public function query()
		{
			$building_id = phpgw::get_var('filter_building_id', 'int', 'REQUEST', null);
			// users with the booking role admin should have access to all buildings
			// admin users should have access to all buildings
			if (!isset($GLOBALS['phpgw_info']['user']['apps']['admin']) && !$this->bo->has_role(booking_sopermission::ROLE_MANAGER))
			{
				$filters['id'] = $this->bo->accessable_applications($GLOBALS['phpgw_info']['user']['id'], $building_id);
			}
			else if($building_id)
			{
				$filters['id'] = $this->bo->accessable_applications(null, $building_id);
			}

			$activity_id = phpgw::get_var('activities', 'int', 'REQUEST', null);
			if ($activity_id)
			{
				$filters['activity_id'] = $this->bo->so->get_activities($activity_id);
			}
			else
			{
				unset($filters['activity_id']);
			}
			$filters['status'] = 'NEW';
//			if (isset($_SESSION['showall']))
//			{
//				$filters['status'] = array('NEW', 'PENDING', 'REJECTED', 'ACCEPTED');
//			}
//			else
			{
				$test = phpgw::get_var('status', 'string', 'REQUEST', null);
				if (phpgw::get_var('status') == 'none')
				{
					$filters['status'] = array('NEW', 'PENDING', 'REJECTED', 'ACCEPTED');
				}
				elseif (isset($test))
				{
					$filters['status'] = $test;
				}
				else
				{
					$filters['status'] = 'NEW';
				}
			}

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', null),
				'query' => $search['value'],
				'sort' => $columns[$order[0]['column']]['data'],
				'dir' => $order[0]['dir'],
				'filters' => $filters
			);

			$applications = $this->bo->so->read($params);
//                        var_dump($params);
//                        exit();

			foreach ($applications['results'] as &$application)
			{
				if (strstr($application['building_name'], "%"))
				{
					$search = array('%2C', '%C3%85', '%C3%A5', '%C3%98', '%C3%B8', '%C3%86',
						'%C3%A6');
					$replace = array(',', 'Å', 'å', 'Ø', 'ø', 'Æ', 'æ');
					$application['building_name'] = str_replace($search, $replace, $application['building_name']);
				}

				$dates = array();
				foreach ($application['dates'] as $data)
				{
					$dates[] = $data['from_'];
					break;
				}
				$fromdate = implode(',', $dates);
				$application['from_'] = pretty_timestamp($fromdate);
				$application['status'] = lang($application['status']);
				$application['created'] = pretty_timestamp($application['created']);
				$application['modified'] = pretty_timestamp($application['modified']);
				$application['frontend_modified'] = pretty_timestamp($application['frontend_modified']);
				$application['resources'] = $this->resource_bo->so->read(array('filters' => array(
						'id' => $application['resources'])));
				$application['resources'] = $application['resources']['results'];
				if ($application['resources'])
				{
					$names = array();
					foreach ($application['resources'] as $res)
					{
						$names[] = $res['name'];
					}
					$application['what'] = $application['resources'][0]['building_name'] . ' (' . join(', ', $names) . ')';
				}
			}
			array_walk($applications["results"], array($this, "_add_links"), "booking.uiapplication.show");

			return $this->jquery_results($applications);
		}

		public function associated()
		{
			$associations = $this->assoc_bo->read();
			foreach ($associations['results'] as &$association)
			{
				$association['from_'] = pretty_timestamp($association['from_']);
				$association['to_'] = pretty_timestamp($association['to_']);
				$association['link'] = self::link(array('menuaction' => 'booking.ui' . $association['type'] . '.edit',
						'id' => $association['id']));
				$association['dellink'] = self::link(array('menuaction' => 'booking.ui' . $association['type'] . '.delete',
						'id' => $association['id'], 'application_id' => $association['application_id']));
				$association['type'] = lang($association['type']);
			}
			return $associations;
		}

		private function _combine_dates( $from_, $to_ )
		{
			return array('from_' => $from_, 'to_' => $to_);
		}

		protected function get_customer_identifier()
		{
			return $this->customer_id;
		}

		protected function extract_customer_identifier( &$data )
		{
			$this->get_customer_identifier()->extract_form_data($data);
		}

		protected function validate_customer_identifier( &$data )
		{
			return $this->get_customer_identifier()->validate($data);
		}

		protected function install_customer_identifier_ui( &$entity )
		{
			$this->get_customer_identifier()->install($this, $entity);
		}

		protected function validate( &$entity )
		{
			$errors = array_merge($this->validate_customer_identifier($entity), $this->bo->validate($entity));
			return $errors;
		}

		protected function set_case_officer( &$application )
		{
			if (!empty($application['case_officer_id']))
			{
				$application['case_officer'] = array(
					'id' => $application['case_officer_id'],
					'name' => $application['case_officer_name'],
				);

				if ($application['case_officer_id'] == $this->current_account_id())
				{
					$application['case_officer']['is_current_user'] = true;
				}
			}
		}

		protected function extract_form_data( $defaults = array() )
		{
			$entity = array_merge($defaults, extract_values($_POST, $this->fields));
			$entity['agegroups'] = array();
			$this->agegroup_bo->extract_form_data($entity);
			$this->extract_customer_identifier($entity);
			return $entity;
		}

		protected function create_accepted_documents_comment_text( $application )
		{
			if (count($application['accepted_documents']) < 1)
			{
				return null;
			}
			$comment_text = lang('The user has accepted the following documents') . ': ';
			foreach ($application['accepted_documents'] as $doc)
			{
				$doc_info = explode('::', $doc);
				$doc_type = $doc_info[0];
				$doc_id = $doc_info[1];
				switch ($doc_type)
				{
					default:
					case 'building':
						$document = $this->document_building->read_single($doc_id);
						break;
					case 'resource':
						$document = $this->document_resource->read_single($doc_id);
						break;
				}
				$comment_text .= $document['description'] . ' (' . $document['name'] . '), ';
			}
			$comment_text = substr($comment_text, 0, -2);

			return $comment_text;
		}

		public function add()
		{
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$orgnr = phpgwapi_cache::session_get($this->module, self::ORGNR_SESSION_KEY);
			$application_text = $config->config_data;

			$errors = array();

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$building = $this->building_bo->so->read(array('filters' => array('id' => phpgw::get_var('building_id', 'int'))));

				array_set_default($_POST, 'resources', array());
				array_set_default($_POST, 'accepted_documents', array());
				array_set_default($_POST, 'from_', array());
				array_set_default($_POST, 'to_', array());

				$application = $this->extract_form_data();

				foreach ($_POST['from_'] as &$from)
				{
					$from = ($from) ? date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($from)) : "";
				}
				foreach ($_POST['to_'] as &$to)
				{
					$to = ($to) ? date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($to)) : "";
				}

				$application['dates'] = array_map(array(self, '_combine_dates'), $_POST['from_'], $_POST['to_']);
				$application['active'] = '1';
				$application['status'] = 'NEW';
				$application['created'] = 'now';
				$application['modified'] = 'now';
				$application['secret'] = $this->generate_secret();
				$application['owner_id'] = $GLOBALS['phpgw_info']['user']['account_id'];

				$errors = $this->validate($application);

				if ($_POST['contact_email'] != $_POST['contact_email2'])
				{
					$errors['email'] = lang('The e-mail addresses you entered do not match');
					$application['contact_email2'] = $_POST['contact_email2'];
				}
				else
				{
					$application['contact_email2'] = $_POST['contact_email2'];
				}

				foreach ($application['agegroups'] as $ag)
				{
					if ($ag['male'] > 9999 || $ag['female'] > 9999)
					{
						$errors['agegroups'] = lang('Agegroups can not be larger than 9999 peoples');
					}
				}
				if ($building['results'][0]['deactivate_application'])
				{
					$errors['application_deactivated'] = lang('Application on this building is not possible.');
				}


				if (!$errors)
				{
					$comment_text = $this->create_accepted_documents_comment_text($application);
					if ($comment_text)
					{
						$this->add_comment($application, $comment_text);
					}

					$receipt = $this->bo->add($application);
					$application['id'] = $receipt['id'];


					/** Start attachment * */
					$document_application = createObject('booking.uidocument_application');

					$document = array(
						'category' => 'other',
						'owner_id' => $application['id'],
						'files' => $this->get_files()
					);
					$document_errors = $document_application->bo->validate($document);

					if (!$document_errors)
					{
						try
						{
							booking_bocommon_authorized::disable_authorization();
							$document_receipt = $document_application->bo->add($document);
						}
						catch (booking_unauthorized_exception $e)
						{
							phpgwapi_cache::message_set(lang('Could not add object due to insufficient permissions'),'error');
						}
					}

					/** End attachment * */
					$this->bo->send_notification($application, true);
					$this->bo->so->update_id_string();
					phpgwapi_cache::message_set(lang("Your application has now been registered and a confirmation email has been sent to you.") . "<br />" .
						lang("A Case officer will review your application as soon as possible.") . "<br />" .
						lang("Please check your Spam Filter if you are missing mail."
					));
//					$this->flash(lang("Your application has now been registered and a confirmation email has been sent to you.")."<br />".
//								 lang("A Case officer will review your application as soon as possible.")."<br />".
//								 lang("Please check your Spam Filter if you are missing mail."));
					$this->redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $receipt['id'],
						'secret' => $application['secret']));
				}
			}
			if (phpgw::get_var('resource') == 'null' || !phpgw::get_var('resource'))
			{
				array_set_default($application, 'resources', array());
			}
			else
			{
				$resources = explode(",", phpgw::get_var('resource'));
				if ($resources)
				{
					$resources_id = $resources[0];
					$resource = $this->resource_bo->read_single($resources_id);
					$activity_id = $resource['activity_id'];
				}

				array_set_default($application, 'resources', $resources);
			}
			array_set_default($application, 'building_id', phpgw::get_var('building_id', 'int'));

			array_set_default($application, 'building_name', phpgw::get_var('building_name', 'string'));

			if (strstr($application['building_name'], "%"))
			{
				$search = array('%C3%85', '%C3%A5', '%C3%98', '%C3%B8', '%C3%86', '%C3%A6');
				$replace = array('Å', 'å', 'Ø', 'ø', 'Æ', 'æ');
				$application['building_name'] = str_replace($search, $replace, $application['building_name']);
			}

			if (phpgw::get_var('from_', 'string'))
			{
				$default_dates = array_map(array(self, '_combine_dates'), phpgw::get_var('from_', 'string'), phpgw::get_var('to_', 'string'));
			}
			else
			{
				$default_dates = array_map(array(self, '_combine_dates'), '', '');
			}
			array_set_default($application, 'dates', $default_dates);

			$this->flash_form_errors($errors);
			$application['resources_json'] = json_encode(array_map('intval', $application['resources']));
			$application['accepted_documents_json'] = json_encode($application['accepted_documents']);
			$top_level_activity = false;
			if (!$activity_id)
			{
				$_building = $this->building_bo->so->read_single(phpgw::get_var('building_id', 'int'));
				$activity_id = $_building['activity_id'];
				$top_level_activity = $activity_id;
			}
			if (!$activity_id)
			{
				$activity_id = phpgw::get_var('activity_id', 'int', 'REQUEST', -1);
			}
			if (!$top_level_activity)
			{
				$activity_path = $this->activity_bo->get_path($activity_id);
				$top_level_activity = $activity_path ? $activity_path[0]['id'] : -1;
			}
			$filter_activity_top = 0;
			if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'booking')
			{
				$application['cancel_link'] = self::link(array('menuaction' => 'booking.uiapplication.index'));
			}
			else if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend')
			{
				$application['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule',
						'id' => phpgw::get_var('building_id', 'int')));
				$filter_activity_top = $top_level_activity > 0 ? $top_level_activity : 0;
			}
			array_set_default($application, 'activity_id', $activity_id);
			$activities = $this->activity_bo->fetch_activities($filter_activity_top);
			$activities = $activities['results'];
			$agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity);
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience($top_level_activity);
			$application['audience_json'] = json_encode(array_map('intval', $application['audience']));

			$audience = $audience['results'];

			$this->install_customer_identifier_ui($application);

			$application['customer_identifier_types']['ssn'] = 'Date of birth or SSN';
			if ($orgnr)
			{
				$application['customer_identifier_type'] = 'organization_number';
				$application['customer_organization_number'] = $orgnr;
				$orgid = $this->organization_bo->so->get_orgid($orgnr);
				$organization = $this->organization_bo->read_single($orgid);
				if ($organization['contacts'][0]['name'] != '')
				{
					$application['contact_name'] = $organization['contacts'][0]['name'];
					$application['contact_email'] = $organization['contacts'][0]['email'];
					$application['contact_phone'] = $organization['contacts'][0]['phone'];
				}
				else
				{
					$application['contact_name'] = $organization['contacts'][1]['name'];
					$application['contact_email'] = $organization['contacts'][1]['email'];
					$application['contact_phone'] = $organization['contacts'][1]['phone'];
				}
			}

			foreach ($application['dates'] as &$date)
			{
				$date['from_'] = pretty_timestamp($date['from_']);
				$date['to_'] = pretty_timestamp($date['to_']);
			}

//			$GLOBALS['phpgw']->jqcal->add_listener('start_date', 'datetime');
//			$GLOBALS['phpgw']->jqcal->add_listener('end_date', 'datetime');
			$GLOBALS['phpgw']->jqcal2->add_listener('start_date', 'datetime');
			$GLOBALS['phpgw']->jqcal2->add_listener('end_date', 'datetime');

			if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'bookingfrontend')
			{
				$tabs = array();
				$tabs['generic'] = array('label' => lang('Application Add'), 'link' => '#application_add');
				$active_tab = 'generic';
				$application['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

				self::add_javascript('booking', 'base', 'application.js');
			}
			else
			{
				self::add_javascript('bookingfrontend', 'base', 'application.js');
			}

			phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security',
				'file'), 'application_form');

			self::adddatetimepicker();

			self::render_template_xsl('application_new', array('application' => $application,
				'activities' => $activities, 'agegroups' => $agegroups, 'audience' => $audience,
				'config' => $application_text));
		}

		public function edit()
		{
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$application_text = $config->config_data;
			$id = phpgw::get_var('id', 'int');
			$application = $this->bo->read_single($id);
			$activity_path = $this->activity_bo->get_path($application['activity_id']);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : 0;

			$this->check_application_assigned_to_current_user($application);

			$building_info = $this->bo->so->get_building_info($id);
			$application['building_id'] = $building_info['id'];
			$application['building_name'] = $building_info['name'];

			$errors = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				array_set_default($_POST, 'accepted_documents', array());

				$application = array_merge($application, extract_values($_POST, $this->fields));
				$application['message'] = $_POST['comment'];
				$this->agegroup_bo->extract_form_data($application);
				$this->extract_customer_identifier($application);

				if ($application['frontend_modified'] == '')
				{
					unset($application['frontend_modified']);
				}

				foreach ($_POST['from_'] as &$from)
				{
					$from = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($from));
				}
				foreach ($_POST['to_'] as &$to)
				{
					$to = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($to));
				}

				$application['dates'] = array_map(array(self, '_combine_dates'), $_POST['from_'], $_POST['to_']);

				$errors = $this->validate($application);

				if (!$errors)
				{
					$receipt = $this->bo->update($application);
					$this->bo->send_notification($application);
					$this->redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $application['id']));
				}
			}

			foreach ($application['dates'] as &$date)
			{
				$date['from_'] = pretty_timestamp($date['from_']);
				$date['to_'] = pretty_timestamp($date['to_']);
			}

			$this->flash_form_errors($errors);
			$this->set_case_officer($application);
			self::adddatetimepicker();

			$current_app = $GLOBALS['phpgw_info']['flags']['currentapp'] ? $GLOBALS['phpgw_info']['flags']['currentapp'] : 'booking';

			$application['resources_json'] = json_encode(array_map('intval', $application['resources']));
			$application['accepted_documents_json'] = json_encode($application['accepted_documents']);
			$application['cancel_link'] = self::link(array('menuaction' => $current_app . '.uiapplication.index'));
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity);
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience($top_level_activity);
			$audience = $audience['results'];
			$this->install_customer_identifier_ui($application);
			$application['customer_identifier_types']['ssn'] = 'Date of birth or SSN';
			$application['audience_json'] = json_encode(array_map('intval', $application['audience']));
			//test

			$GLOBALS['phpgw']->jqcal2->add_listener('start_date', 'datetime');
			$GLOBALS['phpgw']->jqcal2->add_listener('end_date', 'datetime');
			//			self::render_template('application_edit', array('application' => $application, 'activities' => $activities, 'agegroups' => $agegroups, 'audience' => $audience));

			if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'bookingfrontend')
			{
				$tabs = array();
				$tabs['generic'] = array('label' => lang('Application Edit'), 'link' => '#application_edit');
				$active_tab = 'generic';
				self::add_javascript('booking', 'base', 'application.js');
				$application['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			}
			else
			{
				self::add_javascript('bookingfrontend', 'base', 'application.js');
			}

			phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security',
				'file'), 'application_form');
			//_debug_array($application_text);die();
			self::render_template_xsl('application_edit', array('application' => $application,
				'activities' => $activities, 'agegroups' => $agegroups, 'audience' => $audience,
				'config' => $application_text));
		}

		private function check_date_availability( &$allocation )
		{
			foreach ($allocation['dates'] as &$date)
			{
				$available = $this->bo->check_timespan_availability($allocation['resources'], $date['from_'], $date['to_']);
				$date['status'] = intval($available);
				$date['allocation_params'] = $this->event_for_date($allocation, $date['id']);
				$date['booking_params'] = $this->event_for_date($allocation, $date['id']);
				$date['event_params'] = $this->event_for_date($allocation, $date['id']);
			}
		}

		private function event_for_date( $application, $date_id )
		{
			foreach ($application['dates'] as $d)
			{
				if ($d['id'] == $date_id)
				{
					$date = $d;
					break;
				}
			}
			$event = array();
			$event[] = array('from_', pretty_timestamp($date['from_']));
			$event[] = array('to_', pretty_timestamp($date['to_']));
			$event[] = array('cost', '0');
			$event[] = array('application_id', $application['id']);
			$event[] = array('reminder', '0');
			$copy = array(
				'activity_id', 'description', 'contact_name',
				'contact_email', 'contact_phone', 'activity_id', 'building_id', 'building_name',
				'customer_identifier_type', 'customer_ssn', 'customer_organization_number'
			);
			foreach ($copy as $f)
			{
				$event[] = array($f, htmlentities(html_entity_decode($application[$f])), ENT_QUOTES | ENT_SUBSTITUTE);
			}
			foreach ($application['agegroups'] as $ag)
			{
				$event[] = array('male[' . $ag['agegroup_id'] . ']', $ag['male']);
				$event[] = array('female[' . $ag['agegroup_id'] . ']', $ag['female']);
			}
			foreach ($application['audience'] as $a)
			{
				$event[] = array('audience[]', $a);
			}
			foreach ($application['resources'] as $r)
			{
				$event[] = array('resources[]', $r);
			}
			return json_encode($event);
		}

		protected function extract_display_in_dashboard_value()
		{
			$val = phpgw::get_var('display_in_dashboard', 'int', 'POST', 0);
			if ($val <= 0)
				return false;
			if ($val >= 1)
				return true;
			return false; //Not that I think that it is necessary to return here too, but who knows, I might have overlooked something.
		}

		public function show()
		{
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$application_text = $config->config_data;
			$id = phpgw::get_var('id', 'int');
			$application = $this->bo->read_single($id);

			$activity_path = $this->activity_bo->get_path($application['activity_id']);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : 0;
			$tabs = array();
			$tabs['generic'] = array('label' => lang('Application'), 'link' => '#application');
			$active_tab = 'generic';

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				if ($_POST['create'])
				{
					$this->redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $application['id']));
				}

				$update = false;
				$notify = false;

				if ($application['frontend_modified'] == '')
				{
					unset($application['frontend_modified']);
				}

				if (array_key_exists('assign_to_user', $_POST))
				{
					$update = $this->assign_to_current_user($application);
					if ($application['status'] == 'NEW')
					{
						$application['status'] = 'PENDING';
					}
				}
				elseif (isset($_POST['unassign_user']))
				{
					if ($this->unassign_current_user($application))
					{
						$this->set_display_in_dashboard($application, true, array('force' => true));
						$update = true;
					}
				}
				elseif (isset($_POST['display_in_dashboard']))
				{
					$this->check_application_assigned_to_current_user($application);
					$update = $this->set_display_in_dashboard($application, $this->extract_display_in_dashboard_value());
				}
				elseif ($_POST['comment'])
				{
					$application['comment'] = $_POST['comment'];
					$this->add_comment($application, $_POST['comment']);
					$update = true;
					$notify = true;
				}
				elseif ($_POST['status'])
				{
					$this->check_application_assigned_to_current_user($application);
					$application['status'] = $_POST['status'];

					if ($application['status'] == 'REJECTED')
					{
						$test = $this->assoc_bo->so->read(array('filters' => array('application_id' => $application['id'])));
						foreach ($test['results'] as $app)
						{
							$this->bo->so->set_inactive($app['id'], $app['type']);
						}
					}

					if ($application['status'] == 'ACCEPTED')
					{
						$test = $this->assoc_bo->so->read(array('filters' => array('application_id' => $application['id'])));
						foreach ($test['results'] as $app)
						{
							$this->bo->so->set_active($app['id'], $app['type']);
						}
					}

					$update = true;
					$notify = true;
				}
				else if ($_FILES)
				{
					/** Start attachment * */
					$document_application = createObject('booking.uidocument_application');

					$oldfiles = $document_application->bo->so->read(array('filters' => array('owner_id' => $application['id'])));
					$files = $this->get_files();
					$file_exist = false;

					if ($oldfiles['results'])
					{
						foreach ($oldfiles['results'] as $old_file)
						{
							if ($old_file['name'] == $files['name']['name'])
							{
								$file_exist = true;
								phpgwapi_cache::message_set(lang('file exists'),'error');
								break;
							}
						}
					}

					$document = array(
						'category' => 'other',
						'owner_id' => $application['id'],
						'files' => $this->get_files()
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
							phpgwapi_cache::message_set(lang('Could not add object due to insufficient permissions'),'error');
						}
					}

					/** End attachment * */
				}

				$update AND $receipt = $this->bo->update($application);
				$notify AND $this->bo->send_notification($application);

				$this->redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $application['id']));
			}

			$application['dashboard_link'] = self::link(array('menuaction' => 'booking.uidashboard.index'));
			$application['applications_link'] = self::link(array('menuaction' => 'booking.uiapplication.index'));
			$application['edit_link'] = self::link(array('menuaction' => 'booking.uiapplication.edit',
					'id' => $application['id']));
			$building_info = $this->bo->so->get_building_info($id);
			$application['building_id'] = $building_info['id'];
			$application['building_name'] = $building_info['name'];

			$cal_date = strtotime($application['dates'][0]['from_']);
			$cal_date = date('Y-m-d', $cal_date);

			$application['schedule_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule',
					'id' => $building_info['id'], 'backend' => true, 'date' => $cal_date));

			//manipulating the link. we want to use the frontend module instead of backend for displaying the schedule
			$pos = strpos($application['schedule_link'], '/index.php');
			$application['schedule_link'] = substr_replace($application['schedule_link'], 'bookingfrontend/', $pos + 1, 0);

			$resource_ids = '';
			foreach ($application['resources'] as $res)
			{
				$resource_ids = $resource_ids . '&filter_id[]=' . $res;
			}
			if (count($application['resources']) == 0)
			{
				unset($application['dates']);
			}
			$application['resource_ids'] = $resource_ids;

			$this->set_case_officer($application);

			//	$comments = array_reverse($application['comments']); //fixed in db
			$agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity);
//			_debug_array($application);
//			_debug_array($agegroups);
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			// Check if any bookings, allocations or events are associated with this application
			$associations = $this->assoc_bo->so->read(array('filters' => array('application_id' => $application['id']),
				'sort' => 'from_', 'dir' => 'asc'));
			$from = array();
			foreach ($associations['results'] as $assoc)
			{
				$from[] = $assoc['from_'];
			}
			$from = array("data" => implode(',', $from));
			$num_associations = $associations['total_records'];
			if ($this->is_assigned_to_current_user($application) || $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'booking'))
			{
				$application['currentuser'] = true;
			}
			else
			{
				$application['currentuser'] = false;
			}

			$collision_dates = array();
			foreach ($application['dates'] as &$date)
			{
				$collision = $this->bo->so->check_collision($application['resources'], $date['from_'], $date['to_']);
				if ($collision)
				{
					$collision_dates[] = $date['from_'];
				}
			}
			$collision_dates = array("data" => implode(',', $collision_dates));
			self::check_date_availability($application);
			$application['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			phpgwapi_jquery::formvalidator_generate(array('file'), 'file_form');

			$application['description'] = html_entity_decode(nl2br($application['description']));
			$application['equipment'] = html_entity_decode(nl2br($application['equipment']));

			if(!empty($application['comments']))
			{
				foreach ($application['comments'] as  &$comments)
				{
					$comments['comment'] = html_entity_decode(nl2br($comments['comment']));
				}
			}

			self::render_template_xsl('application', array('application' => $application,
				'audience' => $audience, 'agegroups' => $agegroups,
				'num_associations' => $num_associations, 'assoc' => $from, 'collision' => $collision_dates,
				'comments' => $comments, 'config' => $application_text));
		}

		function get_activity_data()
		{
			$activity_id = phpgw::get_var('activity_id', 'int', 'REQUEST', -1);
			$activity_path = $this->activity_bo->get_path($activity_id);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : -1;
			$agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity);
			$audience = $this->audience_bo->fetch_target_audience($top_level_activity);
			return array(
				'agegroups' => $agegroups['results'],
				'audience' => $audience['results'],
			);
		}
	}