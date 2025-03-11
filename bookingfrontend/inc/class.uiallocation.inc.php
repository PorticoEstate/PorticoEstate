	<?php
	phpgw::import_class('booking.uiallocation');
	phpgw::import_class('booking.soallocation');
	phpgw::import_class('booking.uiapplication');
	phpgw::import_class('booking.boapplication');
	use Kigkonsult\Icalcreator\Vcalendar;

	class bookingfrontend_uiallocation extends booking_uiallocation
	{

		public $public_functions = array
			(
			'info'	 => true,
			'info_json'	 => true,
			'cancel' => true,
			'show'	 => true,
			'edit'	 => true
		);

		var $org_bo, $system_message_bo,$booking_bo,$allocation_so,$application_ui,$application_bo;
		public function __construct()
		{
			parent::__construct();
			$this->org_bo			 = CreateObject('booking.boorganization');
			$this->resource_bo		 = CreateObject('booking.boresource');
			$this->building_bo		 = CreateObject('booking.bobuilding');
			$this->system_message_bo = CreateObject('booking.bosystem_message');
			$this->organization_bo	 = CreateObject('booking.boorganization');
			$this->booking_bo		 = CreateObject('booking.bobooking');
			$this->allocation_so     = new booking_soallocation();
			$this->application_ui 	 = new booking_uiapplication();
			$this->application_bo 	 = new booking_boapplication();
		}

		public function building_users( $building_id, $type = false, $activities = array() )
		{
			$contacts		 = array();
			$organizations	 = $this->organization_bo->find_building_users($building_id, $type, $activities);
			foreach ($organizations['results'] as $org)
			{
				if ($org['email'] != '' && strstr($org['email'], '@'))
				{
					if (!in_array($org['email'], $contacts))
					{
						$contacts[] = $org['email'];
					}
				}
				if ($org['contacts'][0]['email'] != '' && strstr($org['contacts'][0]['email'], '@'))
				{
					if (!in_array($org['contacts'][0]['email'], $contacts))
					{
						$contacts[] = $org['contacts'][0]['email'];
					}
				}
				if ($org['contacts'][1]['email'] != '' && strstr($org['contacts'][1]['email'], '@'))
				{
					if (!in_array($org['contacts'][1]['email'], $contacts))
					{
						$contacts[] = $org['contacts'][1]['email'];
					}
				}
				$grp_con = $this->booking_bo->so->get_group_contacts_of_organization($org['id']);
				foreach ($grp_con as $grp)
				{
					if (!in_array($grp['email'], $contacts) && strstr($grp['email'], '@'))
					{
						$contacts[] = $grp['email'];
					}
				}
			}
			return $contacts;
		}

		public function cancel()
		{
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();

			$from_org = phpgw::get_var('from_org', 'boolean', "REQUEST", false);

			if ($config->config_data['user_can_delete_allocations'] != 'yes')
			{

				phpgwapi_cache::message_set('user can not delete allocations', 'error');

				$allocation		 = $this->bo->read_single(intval(phpgw::get_var('allocation_id', 'int')));
				$original_from	 = $allocation['from_'];
				$organization	 = $this->organization_bo->read_single($allocation['organization_id']);
				$errors			 = array();
				if ($_SERVER['REQUEST_METHOD'] == 'POST')
				{

					$outseason		 = $_POST['outseason'];
					$recurring		 = $_POST['recurring'];
					$repeat_until	 = $_POST['repeat_until'];
					$field_interval	 = $_POST['field_interval'];

					$maildata					 = array();
					$maildata['outseason']		 = $outseason;
					$maildata['recurring']		 = $recurring;
					$maildata['repeat_until']	 = $repeat_until;
					$maildata['field_interval']	 = $field_interval;

					date_default_timezone_set("Europe/Oslo");
					$date							 = new DateTime(phpgw::get_var('date'));
					$system_message					 = array();
					$system_message['building_id']	 = intval($allocation['building_id']);
					$system_message['building_name'] = $this->bo->so->get_building($system_message['building_id']);
					$system_message['created']		 = $date->format('Y-m-d  H:m');
//					$system_message					 = array_merge($system_message, extract_values($_POST, array('message')));
					$system_message['message']		 = phpgw::get_var('message', 'html');
					$system_message['type']			 = 'cancelation';
					$system_message['status']		 = 'NEW';
					$system_message['name']			 = $allocation['organization_name'] . ' - ' . $organization['contacts'][0]['name'];
					$system_message['phone']		 = $organization['contacts'][0]['phone'];
					$system_message['email']		 = $organization['contacts'][0]['email'];
					$system_message['title']		 = lang('Cancelation of allocation from') . " " . $allocation['organization_name'];
					$link							 = self::link(array('menuaction'	 => 'booking.uiallocation.delete',
							'id'	 => $allocation['id'], 'outseason'		 => $outseason, 'recurring'		 => $recurring,
							'repeat_until'	 => $repeat_until, 'field_interval' => $field_interval));
					if (strpos($link, '/portico/bookingfrontend') !== false)
					{
						$link	 = mb_strcut($link, 24, strlen($link));
						$link	 = "/portico" . $link;
					}
					else
					{
						$link = mb_strcut($link, 16, strlen($link));
					}
					$system_message['link']		 = $link;
					$system_message['message']	 = $system_message['message'] . "<br /><br />" . lang('To cancel allocation use this link') . " - <a href='" . $link . "'>" . lang('Delete') . "</a>";
					$this->bo->send_admin_notification($allocation, $maildata, $system_message);
					$this->system_message_bo->add($system_message);

					if ($from_org)
					{
						self::redirect(array('menuaction' => 'bookingfrontend.uiorganization.show',
							'id' => $allocation['organization_id'], 'date' => date("Y-m-d", strtotime($original_from))));
					}
					else
					{
						self::redirect(array('menuaction' => 'bookingfrontend.uibuilding.show',
							'id'		 => $system_message['building_id'], 'date'		 => date("Y-m-d", strtotime($original_from))));
					}
				}

				$this->flash_form_errors($errors);

				if ($from_org)
				{
					$allocation['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show',
						'id' => $allocation['organization_id'], 'date' => date("Y-m-d", strtotime($original_from))));
				}
				else
				{
					$allocation['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show',
						'id'		 => $allocation['building_id'], 'date'		 => date("Y-m-d", strtotime($original_from))));
				}

				$allocation['from_'] = pretty_timestamp($allocation['from_']);
				$allocation['to_']	 = pretty_timestamp($allocation['to_']);
				$GLOBALS['phpgw']->jqcal2->add_listener('field_repeat_until', 'date');

				self::rich_text_editor('field-message');
				self::render_template_xsl('allocation_cancel', array('allocation' => $allocation));
			}
			else
			{

				$id				 = phpgw::get_var('allocation_id', 'int');
				$from_date		 = phpgw::get_var('from_', 'string');
				$to_date		 = phpgw::get_var('to_', 'string');
				$outseason		 = phpgw::get_var('outseason', 'string');
				$recurring		 = phpgw::get_var('recurring', 'string');
				$repeat_until	 = phpgw::get_var('repeat_until', 'string');
				$field_interval	 = phpgw::get_var('field_interval', 'int');
				$allocation		 = $this->bo->read_single($id);
				$original_from	 = $allocation['from_'];
				$organization	 = $this->organization_bo->read_single($allocation['organization_id']);
				$season			 = $this->season_bo->read_single($allocation['season_id']);
				$step			 = phpgw::get_var('step', 'string', 'REQUEST', 1);
				$errors			 = array();
				$invalid_dates	 = array();
				$valid_dates	 = array();

				if ($config->config_data['split_pool'] == 'yes')
				{
					$split = 1;
				}
				else
				{
					$split = 0;
				}
				$resources		 = $allocation['resources'];
				$activity		 = $this->organization_bo->so->get_resource_activity($resources);

				$maildata					 = array();
				$maildata['outseason']		 = $outseason;
				$maildata['recurring']		 = $recurring;
				$maildata['repeat_until']	 = $repeat_until;
				$maildata['field_interval']	 = $field_interval;

				if ($_SERVER['REQUEST_METHOD'] == 'POST')
				{
					$mailadresses	 = $this->building_users($allocation['building_id'], $split, $activity);

					$_POST['from_']			 = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['from_']));
					$_POST['to_']			 = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['to_']));
					$_POST['repeat_until']	 = date("Y-m-d", phpgwapi_datetime::date_to_timestamp($_POST['repeat_until']));

					$from_date	 = $_POST['from_'];
					$to_date	 = $_POST['to_'];

					if ($_POST['recurring'] != 'on' && $_POST['outseason'] != 'on')
					{
						$err = $this->bo->so->check_for_booking($id);
						if ($err)
						{
							$errors['booking'] = lang('Could not delete allocation due to a booking still use it');
						}
						else
						{
							$res_names						 = '';
							date_default_timezone_set("Europe/Oslo");
							$date							 = new DateTime(phpgw::get_var('date'));
							$system_message					 = array();
							$system_message['building_id']	 = intval($allocation['building_id']);
							$system_message['building_name'] = $this->bo->so->get_building($system_message['building_id']);
							$system_message['created']		 = $date->format('Y-m-d  H:m');
//							$system_message					 = array_merge($system_message, extract_values($_POST, array('message')));
							$system_message['message']		 = phpgw::get_var('message', 'html');
							$system_message['type']			 = 'cancelation';
							$system_message['status']		 = 'NEW';
							$system_message['name']			 = $allocation['organization_name'] . ' - ' . $organization['contacts'][0]['name'];
							$system_message['phone']		 = $organization['contacts'][0]['phone'];
							$system_message['email']		 = $organization['contacts'][0]['email'];
							$system_message['title']		 = lang('Cancelation of allocation from') . " " . $allocation['organization_name'];
							foreach ($allocation['resources'] as $res)
							{
								$res_names = $res_names . $this->bo->so->get_resource($res) . " ";
							}
							$info_deleted				 = lang("Allocation deleted on") . " " . $system_message['building_name'] . ":<br />" . $res_names . " - " . pretty_timestamp($allocation['from_']) . " - " . pretty_timestamp($allocation['to_']);
							$system_message['message']	 = $system_message['message'] . "<br />" . $info_deleted;
							$this->system_message_bo->add($system_message);
							$this->bo->send_admin_notification($allocation, $maildata, $system_message);
							$this->bo->send_notification($allocation, $maildata, $mailadresses);
							$this->bo->so->delete_allocation($id);

							if ($from_org)
							{
								self::redirect(array('menuaction' => 'bookingfrontend.uiorganization.show',
									'id' => $allocation['organization_id'], 'date' => date("Y-m-d", strtotime($original_from))));
							}
							else
							{
								self::redirect(array('menuaction' => 'bookingfrontend.uibuilding.show',
									'id'		 => $allocation['building_id'], 'date'		 => date("Y-m-d", strtotime($original_from))));
							}
						}
					}
					else
					{
						$step++;
						if ($_POST['recurring'] == 'on')
						{
							$repeat_until = strtotime($_POST['repeat_until']) + 60 * 60 * 24;
						}
						else
						{
							$repeat_until			 = strtotime($season['to_']) + 60 * 60 * 24;
							$_POST['repeat_until']	 = $season['to_'];
						}

						$max_dato	 = strtotime($_POST['to_']); // highest date from input
						$interval	 = $_POST['field_interval'] * 60 * 60 * 24 * 7; // weeks in seconds
						$i			 = 0;
						// calculating valid and invalid dates from the first booking's to-date to the repeat_until date is reached
						// the form from step 1 should validate and if we encounter any errors they are caused by double bookings.

						while (($max_dato + ($interval * $i)) <= $repeat_until)
						{
							$fromdate			 = date('Y-m-d H:i', strtotime($_POST['from_']) + ($interval * $i));
							$todate				 = date('Y-m-d H:i', strtotime($_POST['to_']) + ($interval * $i));
							$allocation['from_'] = $fromdate;
							$allocation['to_']	 = $todate;

							$id = $this->bo->so->get_allocation_id($allocation);
							if ($id)
							{
								$err = $this->bo->so->check_for_booking($id);
							}
							else
							{
								$err = true;
							}

							if ($err)
							{
								$invalid_dates[$i]['from_']	 = $fromdate;
								$invalid_dates[$i]['to_']	 = $todate;
							}
							else
							{
								$valid_dates[$i]['from_']	 = $fromdate;
								$valid_dates[$i]['to_']		 = $todate;
								if ($step == 3)
								{

									$this->bo->so->delete_allocation($id);
								}
							}
							$i++;
						}
						if ($step == 3)
						{
							$maildata					 = array();
							$maildata['outseason']		 = phpgw::get_var('outseason', 'string');
							$maildata['recurring']		 = phpgw::get_var('recurring', 'string');
							$maildata['repeat_until']	 = phpgw::get_var('repeat_until', 'string');
							$maildata['delete']			 = $valid_dates;

							$res_names						 = '';
							date_default_timezone_set("Europe/Oslo");
							$date							 = new DateTime(phpgw::get_var('date'));
							$system_message					 = array();
							$system_message['building_id']	 = intval($allocation['building_id']);
							$system_message['building_name'] = $this->bo->so->get_building($system_message['building_id']);
							$system_message['created']		 = $date->format('Y-m-d  H:m');
//							$system_message					 = array_merge($system_message, extract_values($_POST, array('message')));
							$system_message['message']		 = phpgw::get_var('message', 'html');
							$system_message['type']			 = 'cancelation';
							$system_message['status']		 = 'NEW';
							$system_message['name']			 = ' ';
							$system_message['phone']		 = ' ';
							$system_message['email']		 = ' ';
							$system_message['title']		 = lang('Cancelation of allocation from') . " " . $allocation['organization_name'];
							foreach ($allocation['resources'] as $res)
							{
								$res_names = $res_names . $this->bo->so->get_resource($res) . " ";
							}
							$info_deleted = lang("Allocations deleted on ") . $system_message['building_name'] . ":<br />";
							foreach ($valid_dates as $valid_date)
							{
								$info_deleted = $info_deleted . "<br />" . $res_names . " - " . pretty_timestamp($valid_date['from_']) . " - " . pretty_timestamp($valid_date['to_']);
							}
							$system_message['message'] = $system_message['message'] . "<br />" . $info_deleted;
							$this->bo->send_admin_notification($allocation, $maildata, $system_message);
							$this->bo->send_notification($allocation, $maildata, $mailadresses);
							$this->system_message_bo->add($system_message);

							if ($from_org)
							{
								self::redirect(array('menuaction' => 'bookingfrontend.uiorganization.show',
									'id' => $allocation['organization_id'], 'date' => date("Y-m-d", strtotime($original_from))));
							}
							else
							{
								self::redirect(array('menuaction' => 'bookingfrontend.uibuilding.show',
									'id'		 => $allocation['building_id'], 'date'		 => date("Y-m-d", strtotime($original_from))));
							}
						}
					}
				}
				$this->flash_form_errors($errors);
//				self::add_javascript('booking', 'base', 'allocation.js');

				$allocation['resources_json']	 = json_encode(array_map('intval', $allocation['resources']));
#				$allocation['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uiallocation.show', 'id' => $allocation['id']));
				if ($from_org)
				{
					$allocation['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show',
						'id' => $allocation['organization_id'], 'date' => date("Y-m-d", strtotime($original_from))));
				}
				else
				{
					$allocation['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show',
						'id'		 => $allocation['building_id'], 'date'		 => date("Y-m-d", strtotime($original_from))));
				}

				$allocation['application_link']	 = self::link(array('menuaction' => 'bookingfrontend.uiapplication.show',
						'id'		 => $allocation['application_id']));
				$allocation['from_'] = pretty_timestamp($allocation['from_']);
				$allocation['to_']	 = pretty_timestamp($allocation['to_']);

				$GLOBALS['phpgw']->jqcal2->add_listener('field_repeat_until', 'date');

				if ($step < 2)
				{
					self::rich_text_editor('field-message');
					self::render_template_xsl('allocation_delete', array('allocation'	 => $allocation,
						'recurring'		 => $recurring,
						'outseason'		 => $outseason,
						'interval'		 => $field_interval,
						'repeat_until'	 => $repeat_until,
					));
				}
				elseif ($step == 2)
				{
					self::render_template_xsl('allocation_delete_preview', array('allocation'	 => $allocation,
						'step'			 => $step,
						'recurring'		 => $_POST['recurring'],
						'outseason'		 => $_POST['outseason'],
						'interval'		 => $_POST['field_interval'],
						'repeat_until'	 => pretty_timestamp($_POST['repeat_until']),
						'from_date'		 => pretty_timestamp($from_date),
						'to_date'		 => pretty_timestamp($to_date),
						'message'		 => $_POST['message'],
						'valid_dates'	 => $valid_dates,
						'invalid_dates'	 => $invalid_dates
					));
				}
			}
		}

        public function info_json() {
			$config = CreateObject('phpgwapi.config', 'booking')->read();
			$user_can_delete_allocations = $config['user_can_delete_allocations'] === 'yes' ? 1 : ($config['user_can_delete_allocations'] === 'never' ? 2 : 0);

			// Retrieve multiple allocation IDs
            $ids = phpgw::get_var('ids', 'string');
            if ($ids) {
				$ids = explode(',', $ids);
            } elseif (!$ids || !is_array($ids)) {
                $ids = array(phpgw::get_var('id', 'int'));
			}
			$allocations_info = [];
            foreach ($ids as $id) {
				$allocation = $this->bo->read_single((int)$id);
                if (!$allocation) {
					continue; // Skip if the allocation is not found
				}

				// Process each allocation
				$allocation['info_resource_info'] = $this->calculate_resource_info($allocation['resources']);
				$allocation['info_building_link'] = self::link([
					'menuaction' => 'bookingfrontend.uibuilding.show',
					'id' => $allocation['building_id']
				]);
				$allocation['info_org_link'] = self::link([
					'menuaction' => 'bookingfrontend.uiorganization.show',
					'id' => $allocation['organization_id']
				]);
				$allocation['info_when'] = $this->info_format_allocation_time($allocation['from_'], $allocation['to_']);
				$allocation['info_participant_limit'] = $this->info_calculate_participant_limit($allocation, $config);
				$allocation['info_add_link'] = $this->info_determine_add_link($allocation);
				$allocation['info_edit_link'] = $this->info_determine_edit_link($allocation);
				$allocation['info_cancel_link'] = $this->info_determine_cancel_link($allocation, $user_can_delete_allocations);
				$allocation['info_ical_link'] = self::link([
					'menuaction' => 'bookingfrontend.uiparticipant.ical',
					'reservation_type' => 'allocation',
					'reservation_id' => $allocation['id']
				]);
                $allocation['info_show_link'] = self::link(array('menuaction' => 'bookingfrontend.uiallocation.show',
                    'id' => $allocation['id']));

				// Add processed allocation to the array
				$allocations_info[$id] = $allocation;
			}

			return ['allocations' => $allocations_info, 'user_can_delete_allocations' => $user_can_delete_allocations];
		}

        private function info_determine_add_link($allocation) {
            $bouser = CreateObject('bookingfrontend.bouser');
            if ($bouser->is_logged_in() &&  $bouser->is_organization_admin($allocation['organization_id'])) {
				return self::link([
					'menuaction' => 'bookingfrontend.uibooking.add',
					'allocation_id' => $allocation['id'],
					'from_' => $allocation['from_'],
					'to_' => $allocation['to_'],
                    'resource' => phpgw::get_var('resource'),
					'resource_ids' => $allocation['resource_ids'],
                    'from_org' => phpgw::get_var('from_org', 'boolean', "GET", false)
				]);
			}
			return null;
		}

		private function info_determine_cancel_link($allocation, $user_can_delete_allocations) {
			$bouser = CreateObject('bookingfrontend.bouser');

			// First check if the user is logged in and has permission to delete allocations
			if ($bouser->is_logged_in() && $user_can_delete_allocations == 1) {
				// Check if the user is an organization admin for this allocation (ADDING THIS CHECK)
				if ($bouser->is_organization_admin($allocation['organization_id'])) {
					// Then check if the allocation's start date is in the future
					$isFutureAllocation = $allocation['from_'] > date('Y-m-d H:i:s');

					if ($isFutureAllocation) {
						return self::link([
							'menuaction' => 'bookingfrontend.uiallocation.cancel',
							'allocation_id' => $allocation['id'],
							'from_org' => phpgw::get_var('from_org', 'boolean', "GET", false)
						]);
					}
				}
			}

			return null; // Return null if conditions are not met
		}


        private function info_determine_edit_link($allocation) {
            $bouser = CreateObject('bookingfrontend.bouser');

            if(!$bouser->is_logged_in()) {
				return null;
			}

			// Assuming a method similar to is_organization_admin exists and checks if the current user can administer the given organization
			$canEdit = $bouser->is_organization_admin($allocation['organization_id']);

			// Check if the allocation's start date is in the future
			$isFutureAllocation = $allocation['from_'] > date('Y-m-d H:i:s');

			// Combine conditions: user must have permission and the allocation must be in the future
            if ($canEdit && $isFutureAllocation) {
                $from_org = phpgw::get_var('from_org', 'boolean', "GET", false);

				// Generate and return the edit link
				return self::link([
					'menuaction' => 'bookingfrontend.uiallocation.edit',
					'allocation_id' => $allocation['id'],
					'from_org' 		 => $from_org
				]);
			}

			// If conditions are not met, return null or an appropriate alternative
			return null;
		}


        private function calculate_resource_info($resourceIds) {
            $resources = $this->resource_bo->so->read([
                'filters' => ['id' => $resourceIds],
                'sort' => 'name'
            ]);
            $resNames = array_map(function($res) {
                return $res['name'];
            }, $resources['results']);

            return join(', ', $resNames);
        }

        private function info_format_allocation_time($from, $to) {
            $interval = (new DateTime($from))->diff(new DateTime($to));
            $when = "";
            if($interval->days > 0) {
                $when = pretty_timestamp($from) . ' - ' . pretty_timestamp($to);
            } else {
                $end = new DateTime($to);
                $when = pretty_timestamp($from) . ' - ' . $end->format('H:i');
            }
            return $when;
        }
        private function info_calculate_participant_limit($allocation, $config) {
            $resource_participant_limit_gross = $this->resource_bo->so->get_participant_limit($allocation['resources'], true);
            $resource_participant_limit = !empty($resource_participant_limit_gross['results'][0]['quantity']) ? $resource_participant_limit_gross['results'][0]['quantity'] : 0;
            return !$allocation['participant_limit'] ? ($resource_participant_limit ?: (int)$config['participant_limit']) : $allocation['participant_limit'];
        }




        public function info()
		{
			$config = CreateObject('phpgwapi.config', 'booking')->read();
			if ($config['user_can_delete_allocations'] != 'never')
			{
				if ($config['user_can_delete_allocations'] != 'yes')
				{
					$user_can_delete_allocations = 0;
				}
				else
				{
					$user_can_delete_allocations = 1;
				}
			}
			else
			{
				$user_can_delete_allocations = 2;
			}

			$allocation				 = $this->bo->read_single(phpgw::get_var('id', 'int'));
			$resources				 = $this->resource_bo->so->read(array('filters'	 => array('id' => $allocation['resources']),
				'sort'		 => 'name'));
			$allocation['resources'] = $resources['results'];
			$res_names				 = array();
			$res_ids				 = array();
			foreach ($allocation['resources'] as $res)
			{
				$res_names[] = $res['name'];
				$res_ids[] = $res['id'];
			}
			$allocation['resource']		 = phpgw::get_var('resource');
			$allocation['resource_ids']	 = $res_ids;
			$allocation['resource_info'] = join(', ', $res_names);
			$allocation['building_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show',
					'id'		 => $allocation['building_id']));
			$allocation['org_link']		 = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show',
					'id'		 => $allocation['organization_id']));
			$bouser						 = CreateObject('bookingfrontend.bouser');

			$from_org = phpgw::get_var('from_org', 'boolean', "GET", false);
			if ($bouser->is_organization_admin($allocation['organization_id']))
			{
				$allocation['add_link']		 = self::link(array('menuaction'	 => 'bookingfrontend.uibooking.add',
						'allocation_id'	 => $allocation['id'], 'from_'			 => $allocation['from_'],
						'to_'			 => $allocation['to_'],
						'resource'		 => $allocation['resource'],
						'resource_ids'	 => $allocation['resource_ids'],
						'from_org' 		 => $from_org));
				if ($allocation['from_'] > Date('Y-m-d H:i:s'))
				{
					$allocation['edit_link']	 = self::link(array('menuaction'	 => 'bookingfrontend.uiallocation.edit',
						'allocation_id'	 => $allocation['id'],
						'from_org' 		 => $from_org));

					$allocation['cancel_link']	 = self::link(array('menuaction'	 => 'bookingfrontend.uiallocation.cancel',
						'allocation_id'	 => $allocation['id'], 'from_'			 => $allocation['from_'],
						'to_'			 => $allocation['to_'],
						'resource'		 => $allocation['resource'],
						'resource_ids'		 => $allocation['resource_ids'],
						'from_org' 		 => $from_org));
				}

				if ($allocation['application_id'] != null)
				{
					$allocation['copy_link']	 = self::link(array('menuaction'	 => 'bookingfrontend.uiapplication.add',
						'application_id'	 => $allocation['application_id']));
				}

			}
			$interval	 = (new DateTime($allocation['from_']))->diff(new DateTime($allocation['to_']));
			$when		 = "";
			if ($interval->days > 0)
			{
				$when = pretty_timestamp($allocation['from_']) . ' - ' . pretty_timestamp($allocation['to_']);
			}
			else
			{
				$end	 = new DateTime($allocation['to_']);
				$when	 = pretty_timestamp($allocation['from_']) . ' - ' . $end->format('H:i');
			}
			$allocation['when'] = $when;
			$allocation['show_link'] = self::link(array('menuaction' => 'bookingfrontend.uiallocation.show',
						'id' => $allocation['id']));

			$allocation['ical_link'] = self::link(array('menuaction' => 'bookingfrontend.uiparticipant.ical','reservation_type' => 'allocation','reservation_id' => $allocation['id']));

			$resource_participant_limit_gross = $this->resource_bo->so->get_participant_limit($allocation['resources'], true);

			if(!empty($resource_participant_limit_gross['results'][0]['quantity']))
			{
				$resource_participant_limit = $resource_participant_limit_gross['results'][0]['quantity'];
			}

			if(!$allocation['participant_limit'])
			{
				$allocation['participant_limit'] = $resource_participant_limit ? $resource_participant_limit : (int)$config['participant_limit'];
			}

			$allocation['participant_limit'] = $allocation['participant_limit'] ? $allocation['participant_limit'] : (int)$config['participant_limit'];

			self::render_template_xsl('allocation_info', array('allocation'	 => $allocation,
				'user_can_delete_allocations'	 => $user_can_delete_allocations));
			$GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
		}

		public function show()
		{
			$allocation				 = $this->bo->read_single(phpgw::get_var('id', 'int'));
			$resources				 = $this->resource_bo->so->read(array('filters'	 => array('id' => $allocation['resources']),
				'sort'		 => 'name'));
			$allocation['resources'] = $resources['results'];
			$res_names				 = array();
			foreach ($allocation['resources'] as $res)
			{
				$res_names[] = $res['name'];
			}
			$allocation['resource']		 = phpgw::get_var('resource');
			$allocation['resource_info'] = join(', ', $res_names);
			$allocation['building_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show',
					'id'		 => $allocation['building_id']));
			$allocation['org_link']		 = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show',
					'id'		 => $allocation['organization_id']));
			$bouser						 = CreateObject('bookingfrontend.bouser');
			if ($bouser->is_organization_admin($allocation['organization_id']))
			{
				$allocation['add_link']		 = self::link(array('menuaction'	 => 'bookingfrontend.uibooking.add',
						'allocation_id'	 => $allocation['id'], 'from_'			 => $allocation['from_'],
						'to_'			 => $allocation['to_'],
						'resource'		 => $allocation['resource']));
				$allocation['cancel_link']	 = self::link(array('menuaction'	 => 'bookingfrontend.uiallocation.cancel',
						'allocation_id'	 => $allocation['id'], 'from_'			 => $allocation['from_'],
						'to_'			 => $allocation['to_'],
						'resource'		 => $allocation['resource']));
			}
			$interval	 = (new DateTime($allocation['from_']))->diff(new DateTime($allocation['to_']));
			$when		 = "";
			if ($interval->days > 0)
			{
				$when = pretty_timestamp($allocation['from_']) . ' - ' . pretty_timestamp($allocation['to_']);
			}
			else
			{
				$end	 = new DateTime($allocation['to_']);
				$when	 = pretty_timestamp($allocation['from_']) . ' - ' . $end->format('H:i');
			}
			$allocation['when'] = $when;

			$number_of_participants = createObject('booking.boparticipant')->get_number_of_participants('allocation', $allocation['id']);

			$allocation['number_of_participants'] = $number_of_participants;

			$config = CreateObject('phpgwapi.config', 'booking')->read();
			$external_site_address = !empty($config['external_site_address'])? $config['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

			$participant_registration_link = $external_site_address
				. "/bookingfrontend/?menuaction=bookingfrontend.uiparticipant.add"
				. "&reservation_type=allocation"
				. "&reservation_id={$allocation['id']}";

			$allocation['participant_registration_link'] = $participant_registration_link;
			$allocation['participanttext'] = !empty($config['participanttext'])? $config['participanttext'] :'';

			$resource_participant_limit_gross = $this->resource_bo->so->get_participant_limit($allocation['resources'], true);

			if(!empty($resource_participant_limit_gross['results'][0]['quantity']))
			{
				$resource_participant_limit = $resource_participant_limit_gross['results'][0]['quantity'];
			}

			if(!$allocation['participant_limit'])
			{
				$allocation['participant_limit'] = $resource_participant_limit ? $resource_participant_limit : (int)$config['participant_limit'];
			}

			$allocation['participant_limit'] = $allocation['participant_limit'] ? $allocation['participant_limit'] : (int)$config['participant_limit'];

			phpgw::import_class('phpgwapi.phpqrcode');
			$code_text					 = $participant_registration_link;
			$filename					 = $GLOBALS['phpgw_info']['server']['temp_dir'] . '/' . md5($code_text) . '.png';
			QRcode::png($code_text, $filename);
			$allocation['encoded_qr']	 = 'data:image/png;base64,' . base64_encode(file_get_contents($filename));

			$get_participants_link =  $GLOBALS['phpgw']->link('/index.php', array(
				'menuaction'				 => 'booking.uiparticipant.index',
				'filter_reservation_id'		 => $allocation['id'],
				'filter_reservation_type'	 => 'allocation',
			));

			$allocation['get_participants_link'] = $get_participants_link;

			$datatable_def	 = array();
			if(CreateObject('bookingfrontend.bouser')->is_logged_in())
			{
				$datatable_def[] = array
					(
					'container'	 => 'datatable-container_0',
					'requestUrl' => json_encode(self::link(array(
							'menuaction'				 => 'bookingfrontend.uiparticipant.index',
							'filter_reservation_id'		 => $allocation['id'],
							'filter_reservation_type'	 => 'allocation',
							'phpgw_return_as'			 => 'json'))),
					'ColumnDefs' => array(
						array(
							'key'		 => 'phone',
							'label'		 => lang('participants'),
							'sortable'	 => true,
						),
						array(
							'key'		 => 'quantity',
							'label'		 => lang('quantity'),
							'sortable'	 => true,
						)
					),
					'data'		 => json_encode(array()),
					'config'	 => array(
						array('disableFilter' => true),
						array('disablePagination' => true)
					)
				);
			}


			self::render_template_xsl(array(
				'allocation_show',
				'datatable_inline'
				), array(
				'allocation'	 => $allocation,
				'datatable_def'	 => $datatable_def));
		}

		public function edit()
		{
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();

			$allocation = $this->bo->read_single(intval(phpgw::get_var('allocation_id', 'int')));
			$from_org = phpgw::get_var('from_org', 'boolean', "REQUEST", false);
			$original_from = $allocation['from_'];
			$organization = $this->organization_bo->read_single($allocation['organization_id']);
			$application = $this->application_bo->read_single($allocation['application_id']);
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$link =  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiallocation.show','id' => $allocation['id']), false, true, true);
				$outseason = $_POST['outseason'];
				$recurring = $_POST['recurring'];
				$repeat_until = $_POST['repeat_until'];
				$field_interval = $_POST['field_interval'];

				$new_from_ = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $_POST['from_'])));
				$new_to_ = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $_POST['to_'])));

				$system_message = array();
				$system_message['building_id'] = intval($allocation['building_id']);
				$system_message['building_name'] = $this->bo->so->get_building($system_message['building_id']);
				$system_message['created'] =(new DateTime('now', new DateTimeZone('Europe/Oslo')))->format('Y-m-d  H:i');
				$system_message['type'] = 'message';
				$system_message['status'] = 'NEW';
				$system_message['name'] = $allocation['organization_name'] . ' - ' . $organization['contacts'][0]['name'];
				$system_message['phone'] = $organization['contacts'][0]['phone'];
				$system_message['email'] = $organization['contacts'][0]['email'];



				if ($allocation['from_'] > $new_from_ || $allocation['to_'] < $new_to_)
				{
					if ($new_from_ > $new_to_)
					{
						phpgwapi_cache::message_set(lang('Cannot change start time'), 'error');
					}
					else
					{
						$comment = lang('allocation') ." #: " . $allocation['id'] . " " . lang("User has made a request to alter time on existing booking") . ' ' . $new_from_ . ' - ' . $new_to_;
						$message = lang('Request for changed time');

						if ($outseason == 'on')
						{
							$comment .= ' ' . lang('for remaining season');
						}
						elseif ($recurring == 'on' && $repeat_until != '')
						{
							$comment .= ' ' . lang('for allocations until') . ' ' . $repeat_until;
						}

						if ($outseason == 'on' || $recurring = 'on')
						{
							if ($field_interval == '1')
							{
								$field_interval = ' ';
							}
							else
							{
								$field_interval .= '. ';
							}
							$comment .= ' ' . lang('every') . ' ' . $field_interval . lang('week');
							$message .= '</br>' . lang('Follow status');
						}

						if (!is_null($allocation['application_id']) && $allocation['application_id'] != '')
						{
							$this->application_ui->add_comment_to_application($allocation['application_id'], $comment, 'PENDING');
						}

//						$external_site_address = !empty($config->config_data['external_site_address'])? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

						$system_message['title'] = lang("Request to increase time on existing booking") . " " . $allocation['id'];
						$system_message['message'] = $comment . '<br/>' . '<a href="' . $link . '" target="_blank">' . lang('Go to allocation') .'</a>';


						$this->system_message_bo->add($system_message);
						phpgwapi_cache::message_set($message);
					}
				}

				if ($allocation['from_'] < $new_from_)
				{
					$any_bookings = $this->allocation_so->check_for_booking_between_date($allocation['id'], $new_from_, 'from_');

					if (!$any_bookings)
					{
						if ($new_from_ < $new_to_)
						{
							/**
							 * Saksbehandler må håndtere eventuelle endringer
							 */
//							$allocation['from_'] = $new_from_;
//							$this->allocation_so->update($allocation);
//							phpgwapi_cache::message_set(lang('Successfully changed start time') . ' ' . $allocation['from_']);
						}
						else
						{
							phpgwapi_cache::message_set(lang('Cannot change start time'), 'error');
						}
					}
					else
					{
						phpgwapi_cache::message_set(lang('Decrease of from time overlap with existing booking'), 'error');
					}
				}

				if ($allocation['to_'] > $new_to_)
				{
					$any_bookings = $this->allocation_so->check_for_booking_between_date($allocation['id'], $new_to_, 'to_');

					if (!$any_bookings)
					{
						if ($new_to_ > $new_from_)
						{
							/**
							 * Saksbehandler må håndtere eventuelle endringer
							 */
//							$allocation['to_'] = $new_to_;
//							$this->allocation_so->update($allocation);
//							phpgwapi_cache::message_set(lang('Successfully changed end time') . ' ' . $allocation['to_']);
						}
						else
						{
							phpgwapi_cache::message_set(lang('Cannot change end time'), 'error');
						}


					}
					else
					{
						phpgwapi_cache::message_set(lang('Decrease of to time overlap with existing booking'), 'error');
					}
				}

				if ((!empty($application) && $application['equipment'] != $_POST['equipment']) || (empty($application) && $_POST['equipment'] != ''))
				{
					$comment = lang("User has changed field for equipment") . ' ' . $_POST['equipment'];
					$message = lang('Request for equipment has been sent');


					if (!empty($application))
					{
						/**
						 * Read fresh from database
						 */
						$application = $this->application_bo->read_single($allocation['application_id']);
						$application['equipment'] = $_POST['equipment'];
						$this->application_bo->update($application);
						$this->application_ui->add_comment_to_application($allocation['application_id'], $comment , false);
						$message .= '</br>' . lang('Follow status' );
					}

					$system_message['title'] = lang("Request for equipment") . " " . $allocation['id'];
					$system_message['message'] = $comment . '<br/>' . '<a href="' . $link . '" target="_blank">' . lang('Go to allocation') .'</a>';

					$this->system_message_bo->add($system_message);
					phpgwapi_cache::message_set($message);
				}
			}

			$allocation['from_'] = pretty_timestamp($allocation['from_']);
			$allocation['to_'] = pretty_timestamp($allocation['to_']);
			if ($from_org)
			{
				$allocation['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show',
					'id' => $allocation['organization_id'], 'date' => date("Y-m-d", strtotime($original_from))));
			}
			else
			{
				$allocation['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show',
					'id' => $allocation['building_id'], 'date' => date("Y-m-d", strtotime($original_from))));
			}

			self::add_javascript('bookingfrontend', 'base', 'allocation.js', true);
			phpgwapi_jquery::load_widget('daterangepicker');

			self::rich_text_editor('field-message');
			self::render_template_xsl('allocation_edit', array(
				'allocation' => $allocation,
				'application' => $application));
		}
	}
