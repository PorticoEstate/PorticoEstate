<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('phpgwapi.send');

	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');

//	phpgw::import_class('phpgwapi.uicommon_jquery');

	class booking_uibooking extends booking_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
			'query' => true,
			'add' => true,
			'show' => true,
			'edit' => true,
			'delete' => true,
			'info' => true,
			'building_schedule' => true,
			'resource_schedule' => true,
			'toggle_show_inactive' => true,
		);

		public function __construct()
		{
			parent::__construct();

//			Analizar esta linea self::process_booking_unauthorized_exceptions();

			$this->bo = CreateObject('booking.bobooking');
			$this->activity_bo = CreateObject('booking.boactivity');
			$this->agegroup_bo = CreateObject('booking.boagegroup');
			$this->audience_bo = CreateObject('booking.boaudience');
			$this->building_bo = CreateObject('booking.bobuilding');
			$this->resource_bo = CreateObject('booking.boresource');
			$this->season_bo = CreateObject('booking.boseason');
			$this->allocation_bo = CreateObject('booking.boallocation');
			$this->group_bo = CreateObject('booking.bogroup');
			self::set_active_menu('booking::applications::bookings');
			$this->fields = array('allocation_id', 'activity_id', 'resources',
				'building_id', 'building_name', 'application_id',
				'season_id', 'season_name',
				'group_id', 'group_name', 'group_shortname', 'organization_id', 'organization_name',
				'from_', 'to_', 'audience', 'active', 'cost', 'reminder', 'sms_total');
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
			self::add_javascript('booking', 'base', 'allocation_list.js');

			phpgwapi_jquery::load_widget('autocomplete');
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'autocomplete',
								'name' => 'building',
								'ui' => 'building',
								'text' => lang('Building') . ':',
								'onItemSelect' => 'updateBuildingFilter',
								'onClearSelection' => 'clearBuildingFilter'
							),
							array('type' => 'autocomplete',
								'name' => 'season',
								'ui' => 'season',
								'text' => lang('Season') . ':',
								'depends' => 'building',
								'requestGenerator' => 'requestWithBuildingFilter',
							),
							array('type' => 'filter',
								'name' => 'organizations',
								'text' => lang('Organization') . ':',
								'list' => $this->bo->so->get_organizations(),
							),
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix . '.toggle_show_inactive'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uibooking.index', 'phpgw_return_as' => 'json')),
					'sorted_by' => array('key' => 4, 'dir' => 'desc'),//id
					'field' => array(
						array(
							'key' => 'activity_name',
							'label' => lang('Activity'),
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'group_name',
							'label' => lang('Group')
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building')
						),
						array(
							'key' => 'season_name',
							'label' => lang('Season')
						),
						array(
							'key' => 'from_',
							'label' => lang('From')
						),
						array(
							'key' => 'to_',
							'label' => lang('To')
						),
						array(
							'key' => 'cost',
							'label' => lang('Cost')
						),
						array(
							'key' => 'cost_history',
							'label' => lang('cost history'),
							'sortable' => false,
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);

			$data['datatable']['actions'][] = array();
			if ($this->bo->allow_create())
			{
				$data['datatable']['new_item'] = self::link(array('menuaction' => 'booking.uibooking.add'));
			}
			$data['filters'] = $this->export_filters;

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{

			if (isset($_SESSION['showall']))
			{
				unset($filters['building_name']);
				unset($filters['group_id']);
				unset($filters['season_id']);
			}
			else
			{
				$testdata = phpgw::get_var('filter_building_id', 'int', 'REQUEST', null);
				if ($testdata != 0)
				{
					$filters['building_name'] = $this->bo->so->get_building(phpgw::get_var('filter_building_id', 'int', 'REQUEST', null));
				}
				else
				{
					unset($filters['building_name']);
				}
				$testdata2 = phpgw::get_var('organizations', 'int', 'REQUEST', null);
				if ($testdata2 != 0)
				{
					$filters['group_id'] = $this->bo->so->get_groups_of_organization(phpgw::get_var('organizations', 'int', 'REQUEST', null));
				}
				else
				{
					unset($filters['group_id']);
				}
				$testdata3 = phpgw::get_var('filter_season_id', 'int', 'REQUEST', null);
				if ($testdata3 != 0 and $testdata3 != '')
				{
					$filters['season_id'] = $this->bo->so->get_season(phpgw::get_var('filter_season_id', 'int', 'REQUEST', null));
				}
				else
				{
					unset($filters['season_id']);
				}
			}

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', null),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $columns[$order[0]['column']]['data'],
				'dir' => $order[0]['dir'],
				'filters' => $filters
			);

			$bookings = $this->bo->so->read($params);

			foreach ($bookings['results'] as &$booking)
			{
				$building = $this->building_bo->read_single($booking['building_id']);
				$booking['building_name'] = $building['name'];
				$booking['from_'] = pretty_timestamp($booking['from_']);
				$booking['to_'] = pretty_timestamp($booking['to_']);
				$booking['cost_history'] = count($this->bo->so->get_ordered_costs($booking['id']));
			}

			array_walk($bookings["results"], array($this, "_add_links"), "booking.uibooking.show");
			return $this->jquery_results($bookings);
		}

		public function index_json()
		{
			if (isset($_SESSION['showall']))
			{
				unset($filters['building_name']);
				unset($filters['group_id']);
				unset($filters['season_id']);
			}
			else
			{
				$testdata = phpgw::get_var('filter_building_id', 'int', 'REQUEST', null);
				if ($testdata != 0)
				{
					$filters['building_name'] = $this->bo->so->get_building(phpgw::get_var('filter_building_id', 'int', 'REQUEST', null));
				}
				else
				{
					unset($filters['building_name']);
				}
				$testdata2 = phpgw::get_var('organizations', 'int', 'REQUEST', null);
				if ($testdata2 != 0)
				{
					$filters['group_id'] = $this->bo->so->get_group_of_organization(phpgw::get_var('organizations', 'int', 'REQUEST', null));
				}
				else
				{
					unset($filters['group_id']);
				}
				$testdata3 = phpgw::get_var('filter_season_id', 'int', 'REQUEST', null);
				if ($testdata3 != 0 and $testdata3 != '')
				{
					$filters['season_id'] = $this->bo->so->get_season(phpgw::get_var('filter_season_id', 'int', 'REQUEST', null));
				}
				else
				{
					unset($filters['season_id']);
				}
			}

			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', null),
				'query' => phpgw::get_var('query'),
				'sort' => phpgw::get_var('sort'),
				'dir' => phpgw::get_var('dir'),
				'filters' => $filters
			);

			$bookings = $this->bo->so->read($params);

			foreach ($bookings['results'] as &$booking)
			{
				$building = $this->building_bo->read_single($booking['building_id']);
				$booking['building_name'] = $building['name'];
				$booking['from_'] = pretty_timestamp($booking['from_']);
				$booking['to_'] = pretty_timestamp($booking['to_']);
			}

			array_walk($bookings["results"], array($this, "_add_links"), "booking.uibooking.show");
			return $this->yui_results($bookings);
		}

		private function item_link( &$item, $key )
		{
			if (in_array($item['type'], array('allocation', 'booking', 'event')))
				$item['info_url'] = $this->link(array('menuaction' => 'booking.ui' . $item['type'] . '.info',
					'id' => $item['id']));
		}

		public function building_schedule()
		{
			$date = new DateTime(phpgw::get_var('date'));
			$bookings = $this->bo->building_schedule(phpgw::get_var('building_id', 'int'), $date);
			foreach ($bookings['results'] as &$booking)
			{
				$booking['resource_link'] = $this->link(array('menuaction' => 'booking.uiresource.schedule',
					'id' => $booking['resource_id']));
				$booking['link'] = $this->link(array('menuaction' => 'booking.uibooking.show',
					'id' => $booking['id']));
				array_walk($booking, array($this, 'item_link'));
			}
			$data = array
				(
				'ResultSet' => array(
					"totalResultsAvailable" => $bookings['total_records'],
					"Result" => $bookings['results']
				)
			);
			return $data;
		}

		public function building_extraschedule()
		{
			$date = new DateTime(phpgw::get_var('date'));
			$bookings = $this->bo->building_extraschedule(phpgw::get_var('building_id', 'int'), $date);
			foreach ($bookings['results'] as &$booking)
			{
				$booking['resource_link'] = $this->link(array('menuaction' => 'booking.uiresource.schedule',
					'id' => $booking['resource_id']));
				$booking['link'] = $this->link(array('menuaction' => 'booking.uibooking.show',
					'id' => $booking['id']));
				array_walk($booking, array($this, 'item_link'));
			}
			$data = array
				(
				'ResultSet' => array(
					"totalResultsAvailable" => $bookings['total_records'],
					"Result" => $bookings['results']
				)
			);
			return $data;
		}

		public function resource_schedule()
		{
			$date = new DateTime(phpgw::get_var('date'));
			$bookings = $this->bo->resource_schedule(phpgw::get_var('resource_id', 'int'), $date);
			foreach ($bookings['results'] as &$booking)
			{
				$booking['link'] = $this->link(array('menuaction' => 'booking.uibooking.show',
					'id' => $booking['id']));
				array_walk($booking, array($this, 'item_link'));
			}
			$data = array
				(
				'ResultSet' => array(
					"totalResultsAvailable" => $bookings['total_records'],
					"Result" => $bookings['results']
				)
			);
			return $data;
		}

		public function add()
		{
			$errors = array();
			$booking = array();
			$booking['cost'] = 0;
			$allocation_id = phpgw::get_var('allocation_id', 'int');
			if (isset($_POST['application_id']))
			{
				$application_id = phpgw::get_var('application_id', 'int', 'POST');
			}
			$booking['building_id'] = phpgw::get_var('building_id', 'int');
			$booking['resources'] = phpgw::get_var('resources', 'int');
			#The string replace is a workaround for a problem at Bergen Kommune

			$booking['from_'] = str_replace('%3A', ':', phpgw::get_var('from_', 'string'));
			$booking['to_'] = str_replace('%3A', ':', phpgw::get_var('to_', 'string'));
			foreach ($booking['from_'] as $k => $v)
			{
				$booking['from_'][$k] = pretty_timestamp($booking['from_'][$k]);
				$booking['to_'][$k] = pretty_timestamp($booking['to_'][$k]);
			}

			$time_from = explode(" ", phpgw::get_var('from_', 'string'));
			$time_to = explode(" ", phpgw::get_var('to_', 'string'));

			$step = phpgw::get_var('step', 'int', 'REQUEST', 1);

			$invalid_dates = array();
			$valid_dates = array();
			if ($allocation_id)
			{
				$allocation = $this->allocation_bo->read_single($allocation_id);
				$season = $this->season_bo->read_single($allocation['season_id']);
				$building = $this->building_bo->read_single($season['building_id']);
				$booking['season_id'] = $season['id'];
				$booking['building_id'] = $building['id'];
				$booking['building_name'] = $building['name'];
				array_set_default($booking, 'resources', array(get_var('resource', 'int')));
				$booking['organization_id'] = $allocation['organization_id'];
				$booking['organization_name'] = $allocation['organization_name'];
				$noallocation = False;
			}
			else
			{
				$allocation = array();
				$season = $this->season_bo->read_single(phpgw::get_var('season_id','int', 'POST'));
				$booking['organization_id'] = phpgw::get_var('organization_id','int', 'POST');
				$booking['organization_name'] = phpgw::get_var('organization_name','string', 'POST');
				if($application_id && empty($booking['organization_id']))
				{
					$application = createObject('booking.boapplication')->read_single($application_id);
					if($organization_number = $application['customer_organization_number'])
					{
						$organizations = createObject('booking.soorganization')->read(array('filters' => array('organization_number' => $organization_number,
							'active' => 1)));

						$_POST['organization_id'] = $organizations['results'][0]['id'];
						$_POST['organization_name'] = $organizations['results'][0]['name'];
					}
				}
				$noallocation = True;
			}
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{

				$today = getdate();
				$booking = extract_values($_POST, $this->fields);
				if ($_POST['cost'])
				{
					$this->add_cost_history($booking, phpgw::get_var('cost_comment'), phpgw::get_var('cost', 'float'));
				}

				$timestamp = phpgwapi_datetime::date_to_timestamp($booking['from_']);
				$booking['from_'] = date("Y-m-d H:i:s", $timestamp);
				$timestamp = phpgwapi_datetime::date_to_timestamp($booking['to_']);
				$booking['to_'] = date("Y-m-d H:i:s", $timestamp);

				if (strlen($_POST['from_']) < 6)
				{
					$date_from = array($time_from[0], $_POST['from_']);
					$booking['from_'] = join(" ", $date_from);
					$_POST['from_'] = join(" ", $date_from);
					$date_to = array($time_to[0], $_POST['to_']);
					$booking['to_'] = join(" ", $date_to);
					$_POST['to_'] = join(" ", $date_to);
				}
				$booking['active'] = '1';
				$booking['completed'] = '0';
				$booking['reminder'] = '1';
				$booking['secret'] = $this->generate_secret();
				array_set_default($booking, 'audience', array());
				array_set_default($booking, 'agegroups', array());
				array_set_default($_POST, 'resources', array());
				$this->agegroup_bo->extract_form_data($booking);

				$errors = $this->bo->validate($booking);

				if (!$booking['season_id'] && $_POST['outseason'] == 'on')
				{
					$errors['booking'] = lang('This booking is not connected to a season');
				}

				if (!$errors)
				{
					$step++;
				}

				if (!$errors && $_POST['recurring'] != 'on' && $_POST['outseason'] != 'on')
				{
					if ($noallocation)
					{
						$allocation['resources'] = $booking['resources'];
						$allocation['cost'] = $booking['cost'];
						$allocation['building_id'] = $booking['building_id'];
						$allocation['building_name'] = $booking['building_name'];
						$allocation['season_id'] = $booking['season_id'];
						$allocation['organization_id'] = $booking['organization_id'];
						$allocation['organization_name'] = $booking['organization_name'];
						if ($application_id)
						{
							$allocation['application_id'] = $application_id;
						}
						$allocation['from_'] = $booking['from_'];
						$allocation['to_'] = $booking['to_'];
						$allocation['active'] = '1';
						$allocation['completed'] = '0';
						$receipt = $this->allocation_bo->add($allocation);
						$booking['allocation_id'] = $receipt['id'];
						$booking['secret'] = $this->generate_secret();
						$receipt = $this->bo->add($booking);
					}
					else
					{
						$booking['secret'] = $this->generate_secret();
						$receipt = $this->bo->add($booking);
					}
					$this->redirect(array('menuaction' => 'booking.uimassbooking.schedule', 'id' => $booking['building_id']));
				}
				else if (($_POST['recurring'] == 'on' || $_POST['outseason'] == 'on') && !$errors && $step > 1)
				{
					if ($_POST['recurring'] == 'on')
					{
						$repeat_until = phpgwapi_datetime::date_to_timestamp($_POST['repeat_until']) + 60 * 60 * 24;
					}
					else
					{
						$repeat_until = strtotime($season['to_']) + 60 * 60 * 24;
						$_POST['repeat_until'] = pretty_timestamp($season['to_']);
					}

					$max_dato = phpgwapi_datetime::date_to_timestamp($_POST['to_']); // highest date from input
					$interval = $_POST['field_interval'] * 60 * 60 * 24 * 7; // weeks in seconds
					$i = 0;
					// calculating valid and invalid dates from the first booking's to-date to the repeat_until date is reached
					// the form from step 1 should validate and if we encounter any errors they are caused by double bookings.

					while (($max_dato + ($interval * $i)) <= $repeat_until)
					{
						$fromdate = date('Y-m-d H:i', phpgwapi_datetime::date_to_timestamp($_POST['from_']) + ($interval * $i));
						$todate = date('Y-m-d H:i', phpgwapi_datetime::date_to_timestamp($_POST['to_']) + ($interval * $i));
						$booking['from_'] = $fromdate;
						$booking['to_'] = $todate;
						$fromdate = pretty_timestamp($fromdate);
						$todate = pretty_timestamp($todate);

						$err = $this->bo->validate($booking);

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
								if ($noallocation)
								{
									$allocation['resources'] = $booking['resources'];
									$allocation['cost'] = $booking['cost'];
									$allocation['building_id'] = $booking['building_id'];
									$allocation['building_name'] = $booking['building_name'];
									$allocation['season_id'] = $booking['season_id'];
									$allocation['organization_id'] = $booking['organization_id'];
									$allocation['organization_name'] = $booking['organization_name'];
									if ($application_id != '0')
									{
										$allocation['application_id'] = $application_id;
									}
									$allocation['from_'] = $booking['from_'];
									$allocation['to_'] = $booking['to_'];
									$allocation['active'] = '1';
									$allocation['completed'] = '0';
									$receipt = $this->allocation_bo->add($allocation);
									$booking['allocation_id'] = $receipt['id'];
									if ($application_id != '0')
									{
										$booking['application_id'] = $application_id;
									}
									$booking['secret'] = $this->generate_secret();
									$receipt = $this->bo->add($booking);
									$booking['allocation_id'] = '';
									$this->allocation_bo->so->update_id_string();
								}
								else
								{
									if ($application_id != '0')
									{
										$booking['application_id'] = $application_id;
									}
									$booking['secret'] = $this->generate_secret();
									$receipt = $this->bo->add($booking);
								}
							}
						}
						$i++;
					}
					if ($step == 3)
					{
						if ($application_id != '0')
						{
							$this->redirect(array('menuaction' => 'booking.uiapplication.show', 'id' => $application_id));
						}
						else
						{
							$this->redirect(array('menuaction' => 'booking.uimassbooking.schedule', 'id' => $booking['building_id']));
						}
					}
				}
				$booking['from_'] = pretty_timestamp($booking['from_']);
				$booking['to_'] = pretty_timestamp($booking['to_']);
			}
			if ($allocation['cost'] > 0)
			{
				$errors['cost'] = lang('There is a cost of %1 assosiated with the allocation you are useing', $allocation['cost']);
			}
			$this->flash_form_errors($errors);
			unset($errors['cost']);
			self::add_javascript('booking', 'base', 'booking.js');

			if (phpgw::get_var('resource') == 'null')
			{
				array_set_default($application, 'resources', array());
			}
			else
			{
				$resources = explode(",", phpgw::get_var('resource', 'string'));
				array_set_default($booking, 'resources', $resources);
			}
			array_set_default($booking, 'season_id', phpgw::get_var('season_id', 'int'));
			array_set_default($booking, 'group_id', phpgw::get_var('group_id', 'int'));
			array_set_default($booking, 'building_id', phpgw::get_var('building_id', 'int'));
			array_set_default($booking, 'building_name', phpgw::get_var('building_name', 'string'));
			if (strstr($application['building_name'], "%"))
			{
				$search = array('%C3%85', '%C3%A5', '%C3%98', '%C3%B8', '%C3%86', '%C3%A6');
				$replace = array('Å', 'å', 'Ø', 'ø', 'Æ', 'æ');
				$application['building_name'] = str_replace($search, $replace, $application['building_name']);
			}

			$booking['resources_json'] = json_encode(array_map('intval', $booking['resources']));
			$booking['cancel_link'] = self::link(array('menuaction' => 'booking.uimassbooking.index'));

			$activity_id = phpgw::get_var('activity_id', 'int', 'REQUEST', -1);
			$activity_path = $this->activity_bo->get_path($activity_id);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : -1;
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity);
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience($top_level_activity);
			$audience = $audience['results'];
			$booking['audience_json'] = json_encode(array_map('intval', $booking['audience']));

			$groups = $this->group_bo->so->read(array('filters' => array('organization_id' => $allocation['organization_id'],
					'active' => 1)));
			$groups = $groups['results'];

			$resouces_full = $this->resource_bo->so->read(array('filters' => array('id' => $booking['resources']),
				'sort' => 'name'));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Booking New'), 'link' => '#booking_new');
			$active_tab = 'generic';

			$GLOBALS['phpgw']->jqcal2->add_listener('field_repeat_until', 'date');
			$GLOBALS['phpgw']->jqcal2->add_listener('start_date', 'datetime', phpgwapi_datetime::date_to_timestamp($booking['from_']));
			$GLOBALS['phpgw']->jqcal2->add_listener('end_date', 'datetime', phpgwapi_datetime::date_to_timestamp($booking['to_']));

			$booking['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$booking['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			if ($step < 2)
			{
				self::render_template_xsl('booking_new', array('booking' => $booking,
					'activities' => $activities,
					'agegroups' => $agegroups,
					'audience' => $audience,
					'groups' => $groups,
					'step' => $step,
					'interval' => $_POST['field_interval'],
					'repeat_until' => $_POST['repeat_until'],
					'recurring' => $_POST['recurring'],
					'outseason' => $_POST['outseason'],
					'date_from' => $time_from[0],
					'date_to' => $time_to[0],
					'application_id' => $application_id,
					'noallocation' => $noallocation)
				);
			}
			else if ($step == 2)
			{
				self::render_template_xsl('booking_new_preview', array('booking' => $booking,
					'activities' => $activities,
					'agegroups' => $agegroups,
					'audience' => $audience,
					'step' => $step,
					'recurring' => $_POST['recurring'],
					'outseason' => $_POST['outseason'],
					'interval' => $_POST['field_interval'],
//					'repeat_until' => pretty_timestamp($_POST['repeat_until']),
//					'from_date' => pretty_timestamp($_POST['from_']),
//					'to_date' => pretty_timestamp($_POST['to_']),
					'repeat_until' => $_POST['repeat_until'],
					'from_date' => $_POST['from_'],
					'to_date' => $_POST['to_'],
					'valid_dates' => $valid_dates,
					'invalid_dates' => $invalid_dates,
					'groups' => $groups,
					'application_id' => $application_id,
					'noallocation' => $noallocation)
				);
			}
		}

		private function send_mailnotification_to_group( $group, $subject, $body )
		{
			$send = CreateObject('phpgwapi.send');

			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";

			if (strlen(trim($body)) == 0)
			{
				return false;
			}

			foreach ($group['contacts'] as $contact)
			{
				if (strlen($contact['email']) > 0)
				{
					try
					{
						$send->msg('email', $contact['email'], $subject, $body, '', '', '', $from, '', 'html');
					}
					catch (phpmailerException $e)
					{

					}
				}
			}
		}

		public function edit()
		{
			$id = phpgw::get_var('id', 'int');
			$booking = $this->bo->read_single($id);

			$activity_path = $this->activity_bo->get_path($booking['activity_id']);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : 0;

			$booking['group'] = $this->group_bo->so->read_single($booking['group_id']);
			$booking['organization_id'] = $booking['group']['organization_id'];
			$booking['organization_name'] = $booking['group']['organization_name'];
			$booking['building'] = $this->building_bo->so->read_single($booking['building_id']);
			$booking['building_name'] = $booking['building']['name'];
			$errors = array();

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Booking Edit'), 'link' => '#booking_edit');
			$active_tab = 'generic';

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{

				$_POST['from_'] = ($_POST['from_']) ? date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['from_'])) : $_POST['from_'];
				$_POST['to_'] = ($_POST['to_']) ? date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['to_'])) : $_POST['to_'];

				array_set_default($_POST, 'resources', array());
				$booking = array_merge($booking, extract_values($_POST, $this->fields));
				if ($_POST['cost'] != $_POST['cost_orig'])
				{
					$this->add_cost_history($booking, phpgw::get_var('cost_comment'), phpgw::get_var('cost', 'float'));
				}
				$booking['allocation_id'] = $booking['allocation_id'] ? $booking['allocation_id'] : null;
				$this->agegroup_bo->extract_form_data($booking);
				$group = $this->group_bo->read_single(intval(phpgw::get_var('group_id', 'int')));
				$errors = $this->bo->validate($booking);
				if (!$errors)
				{
					try
					{
						$receipt = $this->bo->update($booking);
						$this->send_mailnotification_to_group($group, lang('Booking changed'), phpgw::get_var('mail', 'string', 'POST'));
						$this->redirect(array('menuaction' => 'booking.uibooking.show', 'id' => $booking['id']));
					}
					catch (booking_unauthorized_exception $e)
					{
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
				}
			}

			$booking['from_'] = pretty_timestamp($booking['from_']);
			$booking['to_'] = pretty_timestamp($booking['to_']);

			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'base', 'booking.js');
			$booking['resources_json'] = json_encode(array_map('intval', $booking['resources']));
			$booking['cancel_link'] = self::link(array('menuaction' => 'booking.uibooking.show',
					'id' => $booking['id']));
			$booking['application_link'] = self::link(array('menuaction' => 'booking.uiapplication.show',
					'id' => $booking['application_id']));
			$agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity);
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience($top_level_activity);
			$audience = $audience['results'];
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$cost_history = $this->bo->so->get_ordered_costs($id);
			$booking['audience_json'] = json_encode(array_map('intval', $booking['audience']));

			$GLOBALS['phpgw']->jqcal2->add_listener('field_from', 'datetime');
			$GLOBALS['phpgw']->jqcal2->add_listener('field_to', 'datetime');

			$booking['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$booking['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			self::render_template_xsl('booking_edit', array('booking' => $booking, 'activities' => $activities,
				'agegroups' => $agegroups, 'audience' => $audience, 'cost_history' => $cost_history));
		}

		public function delete()
		{
			$id = phpgw::get_var('id', 'int');
			$outseason = phpgw::get_var('outseason', 'string');
			$recurring = phpgw::get_var('recurring', 'string');
			$repeat_untild = phpgw::get_var('repeat_until', 'string');
			$field_interval = intval(phpgw::get_var('field_interval'));
			$delete_allocation = phpgw::get_var('delete_allocation');
			$booking = $this->bo->read_single($id);
			$allocation = $this->allocation_bo->read_single($booking['allocation_id']);
			$season = $this->season_bo->read_single($booking['season_id']);
			$step = phpgw::get_var('step', 'int', 'REQUEST', 1);
			$errors = array();
			$invalid_dates = array();
			$valid_dates = array();
			$allocation_delete = array();
			$allocation_keep = array();

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$_POST['from_'] = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['from_']));
				$_POST['to_'] = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['to_']));
				$_POST['repeat_until'] = isset($_POST['repeat_until']) && $_POST['repeat_until'] ? date("Y-m-d", phpgwapi_datetime::date_to_timestamp($_POST['repeat_until'])) : false;

				$from_date = $_POST['from_'];
				$to_date = $_POST['to_'];

				if ($_POST['recurring'] != 'on' && $_POST['outseason'] != 'on')
				{
					if ($_POST['delete_allocation'] != 'on')
					{
						$this->bo->so->delete_booking($id);
						$this->redirect(array('menuaction' => 'booking.uimassbooking.schedule', 'id' => $booking['building_id']));
					}
					else
					{
						$allocation_id = $booking['allocation_id'];
						$this->bo->so->delete_booking($id);
						$err = $this->allocation_bo->so->check_for_booking($allocation_id);
						if ($err)
						{
							$errors['booking'] = lang('Could not delete allocation due to a booking still use it');
						}
						else
						{
							$err = $this->allocation_bo->so->delete_allocation($allocation_id);
							$this->redirect(array('menuaction' => 'booking.uimassbooking.schedule', 'id' => $booking['building_id']));
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
						$booking['from_'] = $fromdate;
						$booking['to_'] = $todate;
						$fromdate = pretty_timestamp($fromdate);
						$todate = pretty_timestamp($todate);

						$id = $this->bo->so->get_booking_id($booking);
						if ($id)
						{
							$aid = $this->bo->so->check_allocation($id);
						}
						else
						{
							$aid = $this->bo->so->check_for_booking($booking);
						}

						if ($id)
						{
							$valid_dates[$i]['from_'] = $fromdate;
							$valid_dates[$i]['to_'] = $todate;
							if ($step == 3)
							{
								$stat = $this->bo->so->delete_booking($id);
							}
						}
						if ($_POST['delete_allocation'] == 'on')
						{
//							if (!$aid)
//							{
//								$allocation_keep[$i]['from_'] = $fromdate;
//								$allocation_keep[$i]['to_'] = $todate;
//							}
//							else
							{
								$allocation_delete[$i]['from_'] = $fromdate;
								$allocation_delete[$i]['to_'] = $todate;
								if ($step == 3 && $aid)
								{
									$stat = $this->bo->so->delete_allocation($aid);
								}
							}
						}
						$i++;
					}
					if ($step == 3)
					{
						$application_id = $booking['application_id'] ? $booking['application_id'] : $allocation['application_id'];
						if($application_id)
						{
							$this->redirect(array('menuaction' => 'booking.uiapplication.show', 'id' => $application_id));
						}
						$building_id = $booking['building_id'] ? $booking['building_id'] : $allocation['building_id'];
						$this->redirect(array('menuaction' => 'booking.uimassbooking.schedule', 'id' => $building_id));
					}
				}
			}

			$this->flash_form_errors($errors);
			phpgwapi_jquery::load_widget('autocomplete');
			self::add_javascript('booking', 'base', 'booking.js');

			$booking['from_'] = pretty_timestamp($booking['from_']);
			$booking['to_'] = pretty_timestamp($booking['to_']);

			$booking['resources_json'] = json_encode(array_map('intval', $booking['resources']));
			$booking['cancel_link'] = self::link(array('menuaction' => 'booking.uibooking.show',
					'id' => $booking['id']));
			$booking['booking_link'] = self::link(array('menuaction' => 'booking.uibooking.show',
					'id' => $booking['id']));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Booking Delete'), 'link' => '#booking_delete');
			$active_tab = 'generic';
			$booking['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			$GLOBALS['phpgw']->jqcal2->add_listener('field_repeat_until', 'date');

			if ($step < 2)
			{
				self::render_template('booking_delete', array('booking' => $booking,
					'recurring' => $recurring,
					'outseason' => $outseason,
					'interval' => $field_interval,
					'repeat_until' => $repeat_until,
					'delete_allocation' => $delete_allocation,
				));
			}
			elseif ($step == 2)
			{
				self::render_template('booking_delete_preview', array('booking' => $booking,
					'step' => $step,
					'recurring' => $_POST['recurring'],
					'outseason' => $_POST['outseason'],
					'interval' => $_POST['field_interval'],
					'repeat_until' => pretty_timestamp($_POST['repeat_until']),
					'from_date' => pretty_timestamp($from_date),
					'to_date' => pretty_timestamp($to_date),
					'delete_allocation' => $_POST['delete_allocation'],
					'allocation_keep' => $allocation_keep,
					'allocation_delete' => $allocation_delete,
					'valid_dates' => $valid_dates,
					'invalid_dates' => $invalid_dates
				));
			}
		}

		public function show()
		{
			$id = phpgw::get_var('id', 'int');
			$booking = $this->bo->read_single($id);

			$activity_path = $this->activity_bo->get_path($booking['activity_id']);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : 0;

			$booking['bookings_link'] = self::link(array('menuaction' => 'booking.uibooking.index'));
			$booking['edit_link'] = self::link(array('menuaction' => 'booking.uibooking.edit',
					'id' => $booking['id']));
			$booking['delete_link'] = self::link(array('menuaction' => 'booking.uibooking.delete',
					'id' => $booking['id']));
			$resource_ids = '';

			foreach ($booking['resources'] as $res)
			{
				$resource_ids = $resource_ids . '&filter_id[]=' . $res;
			}
			$booking['resource_ids'] = $resource_ids;
			$cost_history = $this->bo->so->get_ordered_costs($id);

			if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'bookingfrontend')
			{
				$tabs = array();
				$tabs['generic'] = array('label' => lang('Booking'), 'link' => '#booking');
				$active_tab = 'generic';
				$booking['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			}

			self::render_template_xsl('booking', array('booking' => $booking, 'cost_history' => $cost_history));
		}

		public function info()
		{
			$booking = $this->bo->read_single(phpgw::get_var('id', 'int'));
			$booking['group'] = $this->group_bo->read_single($booking['group_id']);
			$resources = $this->resource_bo->so->read(array('filters' => array('id' => $booking['resources']),
				'sort' => 'name'));
			$booking['resources'] = $resources['results'];
			$res_names = array();
			foreach ($booking['resources'] as $res)
			{
				$res_names[] = $res['name'];
			}
			$booking['resource_info'] = join(', ', $res_names);
			$booking['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show',
					'id' => $booking['resources'][0]['building_id']));
			$booking['org_link'] = self::link(array('menuaction' => 'booking.uiorganization.show',
					'id' => $booking['group']['organization_id']));
			$booking['group_link'] = self::link(array('menuaction' => 'booking.uigroup.show',
					'id' => $booking['group']['id']));
			$booking['delete_link'] = self::link(array('menuaction' => 'booking.uibooking.delete',
					'id' => $booking['id']));
			$booking['edit_link'] = self::link(array('menuaction' => 'booking.uibooking.edit',
					'id' => $booking['id']));

			$booking['when'] = pretty_timestamp($booking['from_']) . ' - ' . pretty_timestamp($booking['to_']);
			self::render_template('booking_info', array('booking' => $booking));
			$GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
		}

		protected function add_cost_history( &$booking, $comment = '', $cost = '0.00' )
		{
			if (!$comment)
			{
				$comment = lang('cost is set');
			}

			$booking['costs'][] = array(
				'time' => 'now',
				'author' => $this->current_account_fullname(),
				'comment' => $comment,
				'cost' => $cost
			);
		}
	}