<?php
	phpgw::import_class('booking.uiallocation');

	class bookingfrontend_uiallocation extends booking_uiallocation
	{

		public $public_functions = array
			(
			'info' => true,
			'cancel' => true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->org_bo = CreateObject('booking.boorganization');
			$this->resource_bo = CreateObject('booking.boresource');
			$this->building_bo = CreateObject('booking.bobuilding');
			$this->system_message_bo = CreateObject('booking.bosystem_message');
			$this->organization_bo = CreateObject('booking.boorganization');
			$this->booking_bo = CreateObject('booking.bobooking');
		}

		public function building_users( $building_id, $type = false, $activities = array() )
		{
			$contacts = array();
			$organizations = $this->organization_bo->find_building_users($building_id, $type, $activities);
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

			if ($config->config_data['user_can_delete_allocations'] != 'yes')
			{

				$allocation = $this->bo->read_single(intval(phpgw::get_var('allocation_id', 'int')));
				$organization = $this->organization_bo->read_single($allocation['organization_id']);
				$errors = array();
				if ($_SERVER['REQUEST_METHOD'] == 'POST')
				{

					$outseason = $_POST['outseason'];
					$recurring = $_POST['recurring'];
					$repeat_until = $_POST['repeat_until'];
					$field_interval = $_POST['field_interval'];

					$maildata = array();
					$maildata['outseason'] = $outseason;
					$maildata['recurring'] = $recurring;
					$maildata['repeat_until'] = $repeat_until;
					$maildata['field_interval'] = $field_interval;

					date_default_timezone_set("Europe/Oslo");
					$date = new DateTime(phpgw::get_var('date'));
					$system_message = array();
					$system_message['building_id'] = intval($allocation['building_id']);
					$system_message['building_name'] = $this->bo->so->get_building($system_message['building_id']);
					$system_message['created'] = $date->format('Y-m-d  H:m');
					$system_message = array_merge($system_message, extract_values($_POST, array(
						'message')));
					$system_message['type'] = 'cancelation';
					$system_message['status'] = 'NEW';
					$system_message['name'] = $allocation['organization_name'] . ' - ' . $organization['contacts'][0]['name'];
					$system_message['phone'] = $organization['contacts'][0]['phone'];
					$system_message['email'] = $organization['contacts'][0]['email'];
					$system_message['title'] = lang('Cancelation of allocation from') . " " . $allocation['organization_name'];
					$link = self::link(array('menuaction' => 'booking.uiallocation.delete',
							'allocation_id' => $allocation['id'], 'outseason' => $outseason, 'recurring' => $recurring,
							'repeat_until' => $repeat_until, 'field_interval' => $field_interval));
					if (strpos($link, '/portico/bookingfrontend') !== false)
					{
						$link = mb_strcut($link, 24, strlen($link));
						$link = "/portico" . $link;
					}
					else
					{
						$link = mb_strcut($link, 16, strlen($link));
					}
					$system_message['link'] = $link;
					$system_message['message'] = $system_message['message'] . "<br /><br />" . lang('To cancel allocation use this link') . " - <a href='" . $link . "'>" . lang('Delete') . "</a>";
					$this->bo->send_admin_notification($allocation, $maildata, $system_message);
					$this->system_message_bo->add($system_message);
					$this->redirect(array('menuaction' => 'bookingfrontend.uibuilding.schedule',
						'id' => $system_message['building_id']));
				}

				$this->flash_form_errors($errors);
				$allocation['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule',
						'id' => $allocation['building_id']));

				$allocation['from_'] = pretty_timestamp($allocation['from_']);
				$allocation['to_'] = pretty_timestamp($allocation['to_']);
				$GLOBALS['phpgw']->jqcal2->add_listener('field_repeat_until', 'date');

				self::rich_text_editor('field-message');
				self::render_template_xsl('allocation_cancel', array('allocation' => $allocation));
			}
			else
			{

				$id = phpgw::get_var('allocation_id', 'int');
				$from_date = phpgw::get_var('from_', 'string');
				$to_date = phpgw::get_var('to_', 'string');
				$outseason = phpgw::get_var('outseason', 'string');
				$recurring = phpgw::get_var('recurring', 'string');
				$repeat_until = phpgw::get_var('repeat_until', 'string');
				$field_interval = phpgw::get_var('field_interval', 'int');
				$allocation = $this->bo->read_single($id);
				$organization = $this->organization_bo->read_single($allocation['organization_id']);
				$season = $this->season_bo->read_single($allocation['season_id']);
				$step = phpgw::get_var('step', 'string', 'REQUEST', 1);
				$errors = array();
				$invalid_dates = array();
				$valid_dates = array();

				if ($config->config_data['split_pool'] == 'yes')
				{
					$split = 1;
				}
				else
				{
					$split = 0;
				}
				$resources = $allocation['resources'];
				$activity = $this->organization_bo->so->get_resource_activity($resources);
				$mailadresses = $this->building_users($allocation['building_id'], $split, $activity);

				$maildata = array();
				$maildata['outseason'] = $outseason;
				$maildata['recurring'] = $recurring;
				$maildata['repeat_until'] = $repeat_until;
				$maildata['field_interval'] = $field_interval;

				if ($_SERVER['REQUEST_METHOD'] == 'POST')
				{
					$_POST['from_'] = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['from_']));
					$_POST['to_'] = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['to_']));
					$_POST['repeat_until'] = date("Y-m-d", phpgwapi_datetime::date_to_timestamp($_POST['repeat_until']));

					$from_date = $_POST['from_'];
					$to_date = $_POST['to_'];

					if ($_POST['recurring'] != 'on' && $_POST['outseason'] != 'on')
					{
						$err = $this->bo->so->check_for_booking($id);
						if ($err)
						{
							$errors['booking'] = lang('Could not delete allocation due to a booking still use it');
						}
						else
						{
							$res_names = '';
							date_default_timezone_set("Europe/Oslo");
							$date = new DateTime(phpgw::get_var('date'));
							$system_message = array();
							$system_message['building_id'] = intval($allocation['building_id']);
							$system_message['building_name'] = $this->bo->so->get_building($system_message['building_id']);
							$system_message['created'] = $date->format('Y-m-d  H:m');
							$system_message = array_merge($system_message, extract_values($_POST, array(
								'message')));
							$system_message['type'] = 'cancelation';
							$system_message['status'] = 'NEW';
							$system_message['name'] = $allocation['organization_name'] . ' - ' . $organization['contacts'][0]['name'];
							$system_message['phone'] = $organization['contacts'][0]['phone'];
							$system_message['email'] = $organization['contacts'][0]['email'];
							$system_message['title'] = lang('Cancelation of allocation from') . " " . $allocation['organization_name'];
							foreach ($allocation['resources'] as $res)
							{
								$res_names = $res_names . $this->bo->so->get_resource($res) . " ";
							}
							$info_deleted = lang("Allocation deleted on") . " " . $system_message['building_name'] . ":<br />" . $res_names . " - " . pretty_timestamp($allocation['from_']) . " - " . pretty_timestamp($allocation['to_']);
							$system_message['message'] = $system_message['message'] . "<br />" . $info_deleted;
							$this->system_message_bo->add($system_message);
							$this->bo->send_admin_notification($allocation, $maildata, $system_message);
							$this->bo->send_notification($allocation, $maildata, $mailadresses);
							$this->bo->so->delete_allocation($id);
							$this->redirect(array('menuaction' => 'bookingfrontend.uibuilding.schedule',
								'id' => $allocation['building_id']));
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
							$repeat_until = strtotime($season['to_']) + 60 * 60 * 24;
							$_POST['repeat_until'] = $season['to_'];
						}

						$max_dato = strtotime($_POST['to_']); // highest date from input
						$interval = $_POST['field_interval'] * 60 * 60 * 24 * 7; // weeks in seconds
						$i = 0;
						// calculating valid and invalid dates from the first booking's to-date to the repeat_until date is reached
						// the form from step 1 should validate and if we encounter any errors they are caused by double bookings.

						while (($max_dato + ($interval * $i)) <= $repeat_until)
						{
							$fromdate = date('Y-m-d H:i', strtotime($_POST['from_']) + ($interval * $i));
							$todate = date('Y-m-d H:i', strtotime($_POST['to_']) + ($interval * $i));
							$allocation['from_'] = $fromdate;
							$allocation['to_'] = $todate;
							$fromdate = pretty_timestamp($fromdate);
							$todate = pretty_timestamp($todate);

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
								$invalid_dates[$i]['from_'] = $fromdate;
								$invalid_dates[$i]['to_'] = $todate;
							}
							else
							{
								$valid_dates[$i]['from_'] = $fromdate;
								$valid_dates[$i]['to_'] = $todate;
								if ($step == 3)
								{

									$this->bo->so->delete_allocation($id);
								}
							}
							$i++;
						}
						if ($step == 3)
						{
							$maildata = array();
							$maildata['outseason'] = phpgw::get_var('outseason', 'string');
							$maildata['recurring'] = phpgw::get_var('recurring', 'string');
							$maildata['repeat_until'] = phpgw::get_var('repeat_until', 'string');
							$maildata['delete'] = $valid_dates;

							$res_names = '';
							date_default_timezone_set("Europe/Oslo");
							$date = new DateTime(phpgw::get_var('date'));
							$system_message = array();
							$system_message['building_id'] = intval($allocation['building_id']);
							$system_message['building_name'] = $this->bo->so->get_building($system_message['building_id']);
							$system_message['created'] = $date->format('Y-m-d  H:m');
							$system_message = array_merge($system_message, extract_values($_POST, array(
								'message')));
							$system_message['type'] = 'cancelation';
							$system_message['status'] = 'NEW';
							$system_message['name'] = ' ';
							$system_message['phone'] = ' ';
							$system_message['email'] = ' ';
							$system_message['title'] = lang('Cancelation of allocation from') . " " . $allocation['organization_name'];
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
							$this->redirect(array('menuaction' => 'bookingfrontend.uibuilding.schedule',
								'id' => $allocation['building_id']));
						}
					}
				}
				$this->flash_form_errors($errors);
//				self::add_javascript('booking', 'base', 'allocation.js');

				$allocation['resources_json'] = json_encode(array_map('intval', $allocation['resources']));
#				$allocation['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uiallocation.show', 'id' => $allocation['id']));
				$allocation['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule',
						'id' => $allocation['building_id'], 'date' => $allocation['from_']));
				$allocation['application_link'] = self::link(array('menuaction' => 'bookingfrontend.uiapplication.show',
						'id' => $allocation['application_id']));

				$allocation['from_'] = pretty_timestamp($allocation['from_']);
				$allocation['to_'] = pretty_timestamp($allocation['to_']);

				$GLOBALS['phpgw']->jqcal2->add_listener('field_repeat_until', 'date');

				if ($step < 2)
				{
					self::rich_text_editor('field-message');
					self::render_template_xsl('allocation_delete', array('allocation' => $allocation,
						'recurring' => $recurring,
						'outseason' => $outseason,
						'interval' => $field_interval,
						'repeat_until' => $repeat_until,
					));
				}
				elseif ($step == 2)
				{
					self::render_template_xsl('allocation_delete_preview', array('allocation' => $allocation,
						'step' => $step,
						'recurring' => $_POST['recurring'],
						'outseason' => $_POST['outseason'],
						'interval' => $_POST['field_interval'],
						'repeat_until' => pretty_timestamp($_POST['repeat_until']),
						'from_date' => pretty_timestamp($from_date),
						'to_date' => pretty_timestamp($to_date),
						'message' => $_POST['message'],
						'valid_dates' => $valid_dates,
						'invalid_dates' => $invalid_dates
					));
				}
			}
		}

		public function info()
		{
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			if ($config->config_data['user_can_delete_allocations'] != 'never')
			{
				if ($config->config_data['user_can_delete_allocations'] != 'yes')
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

			$allocation = $this->bo->read_single(phpgw::get_var('id', 'int'));
			$resources = $this->resource_bo->so->read(array('filters' => array('id' => $allocation['resources']),
				'sort' => 'name'));
			$allocation['resources'] = $resources['results'];
			$res_names = array();
			foreach ($allocation['resources'] as $res)
			{
				$res_names[] = $res['name'];
			}
			$allocation['resource'] = phpgw::get_var('resource');
			$allocation['resource_info'] = join(', ', $res_names);
			$allocation['building_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show',
					'id' => $allocation['building_id']));
			$allocation['org_link'] = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show',
					'id' => $allocation['organization_id']));
			$bouser = CreateObject('bookingfrontend.bouser');
			if ($bouser->is_organization_admin($allocation['organization_id']))
			{
				$allocation['add_link'] = self::link(array('menuaction' => 'bookingfrontend.uibooking.add',
						'allocation_id' => $allocation['id'], 'from_' => $allocation['from_'], 'to_' => $allocation['to_'],
						'resource' => $allocation['resource']));
				$allocation['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uiallocation.cancel',
						'allocation_id' => $allocation['id'], 'from_' => $allocation['from_'], 'to_' => $allocation['to_'],
						'resource' => $allocation['resource']));
			}
			$allocation['when'] = pretty_timestamp($allocation['from_']) . ' - ' . pretty_timestamp($allocation['to_']);
			self::render_template_xsl('allocation_info', array('allocation' => $allocation,
				'user_can_delete_allocations' => $user_can_delete_allocations));
			$GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
		}
	}