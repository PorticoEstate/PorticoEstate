<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.boorganization');

	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');

//	phpgw::import_class('phpgwapi.uicommon_jquery');

	class booking_uiallocation extends booking_uicommon
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
			'toggle_show_inactive' => true,
		);
		protected
			$organization_bo, $building_bo, $season_bo, $resource_bo, $sopurchase_order, $fields, $display_name,$export_filters;
		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boallocation');
			$this->organization_bo = CreateObject('booking.boorganization');
			$this->building_bo = CreateObject('booking.bobuilding');
			$this->season_bo = CreateObject('booking.boseason');
			$this->resource_bo = CreateObject('booking.boresource');
			$this->sopurchase_order = createObject('booking.sopurchase_order');
			self::set_active_menu('booking::applications::allocations');
			$this->fields = array('resources', 'cost', 'application_id',
				'building_id', 'building_name',
				'season_id', 'season_name',
				'organization_id', 'organization_name',
				'organization_shortname', 'from_', 'to_', 'active', 'skip_bas',
				'additional_invoice_information');

			$this->display_name = lang('allocations');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('booking') . "::{$this->display_name}";
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
			self::add_javascript('booking', 'base', 'allocation_list.js');

			phpgwapi_jquery::load_widget('menu');
			phpgwapi_jquery::load_widget('autocomplete');
			$build_id = phpgw::get_var('buildings', 'int', 'REQUEST', null);
			$data = array(
				'datatable_name' => $this->display_name,
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
//							array(
//								'type' => 'link',
//								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
//								'href' => self::link(array('menuaction' => $this->url_prefix . '.toggle_show_inactive'))
//							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uiallocation.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'organization_name',
							'label' => lang('Organization'),
						),
						array(
							'key' => 'organization_shortname',
							'label' => lang('Organization shortname')
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building')
						),
						array(
							'key' => 'resource_names',
							'label' => lang('resources'),
							'sortable' => false,
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
							'label' => lang('cost')
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


			if ($this->bo->allow_create())
			{
				$data['datatable']['new_item'] = self::link(array('menuaction' => 'booking.uiallocation.add'));
			}

			$data['datatable']['actions'][] = array(
				'my_name'	 => 'toggle_inactive',
				'className'	 => 'save',
				'type'		 => 'custom',
				'statustext' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
				'text'		 => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
				'custom_code'	 => 'window.open("' .self::link(array('menuaction' => $this->url_prefix . '.toggle_show_inactive')) . '", "_self");',
			);

			$data['filters'] = $this->export_filters;
			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			if (isset($_SESSION['showall']))
			{
				unset($filters['building_name']);
				unset($filters['organization_id']);
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
					$filters['organization_id'] = $this->bo->so->get_organization(phpgw::get_var('organizations', 'int', 'REQUEST', null));
				}
				else
				{
					unset($filters['organization_id']);
				}
				$testdata3 = phpgw::get_var('filter_season_id', 'int', 'REQUEST', null);
				if ($testdata3 != 0)
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
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $columns[$order[0]['column']]['data'],
				'dir' => $order[0]['dir'],
				'filters' => $filters,
			);

			$allocations = $this->bo->so->read($params);
			array_walk($allocations["results"], array($this, "_add_links"), "booking.uiallocation.show");

			foreach ($allocations['results'] as &$allocation)
			{
				$allocation['from_'] = pretty_timestamp($allocation['from_']);
				$allocation['to_'] = pretty_timestamp($allocation['to_']);
				$allocation['cost_history'] = count($this->bo->so->get_ordered_costs($allocation['id']));
				$resources = $this->resource_bo->so->read(array(
								'sort'    => 'sort',
								'results' =>'all',
								'filters' => array('id' => $allocation['resources']), 'results' =>'all'
					));
				$resource_names = array();
				if($resources['results'])
				{
					foreach ($resources['results'] as $resource)
					{
						$resource_names[] = $resource['name'];
					}
				}
				$allocation['resource_names'] = implode(', ', $resource_names);
			}

			return $this->jquery_results($allocations);
		}

		public function index_json()
		{
			if (isset($_SESSION['showall']))
			{
				unset($filters['building_name']);
				unset($filters['organization_id']);
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
					$filters['organization_id'] = $this->bo->so->get_organization(phpgw::get_var('organizations', 'int', 'REQUEST', null));
				}
				else
				{
					unset($filters['organization_id']);
				}
				$testdata3 = phpgw::get_var('filter_season_id', 'int', 'REQUEST', null);
				if ($testdata3 != 0)
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

			$allocations = $this->bo->so->read($params);
			array_walk($allocations["results"], array($this, "_add_links"), "booking.uiallocation.show");

			foreach ($allocations['results'] as &$allocation)
			{
				$allocation['from_'] = pretty_timestamp($allocation['from_']);
				$allocation['to_'] = pretty_timestamp($allocation['to_']);
			}

			return $this->yui_results($allocations);
		}

		protected function add_cost_history( &$allocation, $comment = '', $cost = '0.00' )
		{
			if (!$comment)
			{
				$comment = lang('cost is set');
			}

			$allocation['costs'][] = array(
				'time' => 'now',
				'author' => $this->current_account_fullname(),
				'comment' => $comment,
				'cost' => (float)$cost
			);
		}

		public function add()
		{
			$errors = array();
			$step = phpgw::get_var('step', 'int', 'REQUEST', 1);
			$invalid_dates = array();
			$valid_dates = array();

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$season = $this->season_bo->read_single(phpgw::get_var('season_id', 'int'));
				array_set_default($_POST, 'resources', array());

				if(phpgw::get_var('customer_organization_id', 'bool', 'POST'))
				{
					$_POST['organization_id'] = phpgw::get_var('customer_organization_id', 'int');
					$_POST['organization_name'] = phpgw::get_var('customer_organization_name', 'string');
				}

				if(empty($_POST['organization_id']))
				{
					$application_id = phpgw::get_var('application_id', 'int', 'POST');
					if($application_id)
					{
						$application = createObject('booking.boapplication')->read_single($application_id);
						if($organization_number = $application['customer_organization_number'])
						{
							$organizations = createObject('booking.soorganization')->read(array('results' => -1, 'filters' => array('organization_number' => $organization_number,
								'active' => 1)));

							$_POST['organization_id'] = $organizations['results'][0]['id'];
							$_POST['organization_name'] = $organizations['results'][0]['name'];
						}
					}

				}

				$allocation = extract_values($_POST, $this->fields);
				$allocation['skip_bas'] = (int)phpgw::get_var('skip_bas', 'int');
				if ($_POST['cost'])
				{
					$this->add_cost_history($allocation, phpgw::get_var('cost_comment'), phpgw::get_var('cost', 'float'));
				}
				$allocation['active'] = '1';
				$allocation['completed'] = '0';

				$weekday = phpgw::get_var('weekday', 'string', 'POST');

				if(!$weekday)
				{
					$weekday = strtolower (date('l', phpgwapi_datetime::date_to_timestamp($_POST['from_'])));
				}

				$_POST['weekday'] = $weekday;

				$from_date = $_POST['from_'];
				$to_date = $_POST['to_'];
				$from_date_arr = explode(' ', $_POST['from_']);
				$to_date_arr = explode(' ', $_POST['to_']);
				if(count($from_date_arr) == 2)
				{
					$from_time = $from_date_arr[1];
					if(count($to_date_arr) == 2)
					{
						$to_time = $to_date_arr[1];
					}
					else
					{
						$to_time = $to_date_arr[0];
					}

//					$allocation['from_'] = strftime("%Y-%m-%d %H:%M", phpgwapi_datetime::date_to_timestamp($from_date_arr[0] . " " . $from_time));
					$allocation['from_'] = date('Y-m-d H:i', phpgwapi_datetime::date_to_timestamp($from_date_arr[0] . " " . $from_time));
//					$allocation['to_'] = strftime("%Y-%m-%d %H:%M", phpgwapi_datetime::date_to_timestamp($from_date_arr[0] . " " . $to_time));
					$allocation['to_'] = date('Y-m-d H:i', phpgwapi_datetime::date_to_timestamp($from_date_arr[0] . " " . $to_time));
				}
				else
				{
					$from_time = $_POST['from_'];
					$to_time = $_POST['to_'];
//					$allocation['from_'] = strftime("%Y-%m-%d %H:%M", strtotime($weekday . " " . $from_time));
					$allocation['from_'] = date('Y-m-d H:i', strtotime($weekday . " " . $from_time));
//					$allocation['to_'] = strftime("%Y-%m-%d %H:%M", strtotime($weekday . " " . $to_time));
					$allocation['to_'] = date('Y-m-d H:i', strtotime($weekday . " " . $to_time));

					if (($weekday != 'sunday' && date('w') > date('w', strtotime($weekday))) || (date('w') == '0' && date('w') < date('w', strtotime($weekday))))
					{
						if (!phpgw::get_var('weekday', 'string', 'POST'))
						{
//							$allocation['from_'] = strftime("%Y-%m-%d %H:%M", strtotime($weekday . " " . $from_date_arr[1]) - 60 * 60 * 24 * 7);
//							$allocation['to_'] = strftime("%Y-%m-%d %H:%M", strtotime($weekday . " " . $to_date_arr[1]) - 60 * 60 * 24 * 7);
							$allocation['from_'] = date('Y-m-d H:i', strtotime($weekday . " " . $from_date_arr[1]) - 60 * 60 * 24 * 7);
							$allocation['to_'] = date('Y-m-d H:i', strtotime($weekday . " " . $to_date_arr[1]) - 60 * 60 * 24 * 7);
						}
					}
				}

				$_POST['from_'] = $allocation['from_'];
				$_POST['to_'] = $allocation['to_'];

				$errors = $this->bo->validate($allocation);

				if (!$errors)
				{
					$step++;
				}
				if ($errors  && phpgw::get_var('repeat_until', 'bool'))
				{
					$_POST['repeat_until'] = date("Y-m-d", phpgwapi_datetime::date_to_timestamp($_POST['repeat_until']));
				}
				else if (!$errors && ($_POST['outseason'] != 'on' && !phpgw::get_var('repeat_until', 'bool')))
				{
					try
					{
						$receipt = $this->bo->add($allocation);
						$allocation['id'] = $receipt['id'];

						/**
						 *
						 * Add purchase order from POST
						 */

						$temp_id = 'allocation_'. time();

						$purchase_order = $this->compile_purchase_order($temp_id, true);

						if(!empty($purchase_order['lines']) && empty($allocation['application_id']))
						{
							$purchase_order['application_id'] = -1;
							$purchase_order['reservation_type'] = 'allocation';
							$purchase_order['reservation_id'] = $allocation['id'];

							createObject('booking.sopurchase_order')->add_purchase_order($purchase_order);
						}
						else
						{
							$purchase_order_id = $this->sopurchase_order->copy_purchase_order_from_application($allocation, $receipt['id'], 'allocation');
							if($purchase_order_id)
							{
								$purchase_order_result =  $this->sopurchase_order->get_single_purchase_order($purchase_order_id);
								$this->add_cost_history($allocation, lang('cost is set'), $purchase_order_result['sum']);
								$allocation['cost'] = $purchase_order_result['sum'];
								$this->bo->update($allocation);
							}
						}

						$this->bo->so->update_id_string();
						self::redirect(array('menuaction' => 'booking.uiallocation.show', 'id' => $receipt['id']));
					}
					catch (booking_unauthorized_exception $e)
					{
						$errors['global'] = lang('Could not add object due to insufficient permissions');
					}
				}
				else if (($_POST['outseason'] == 'on' || phpgw::get_var('repeat_until', 'bool')) && !$errors && $step > 1)
				{

					if (phpgw::get_var('repeat_until', 'bool'))
					{
						$repeat_until = phpgwapi_datetime::date_to_timestamp($_POST['repeat_until']) + 60 * 60 * 24;
						/*hack to preserve dateformat for next step*/
						$_POST['repeat_until'] = date("Y-m-d", phpgwapi_datetime::date_to_timestamp($_POST['repeat_until']));
					}
					else
					{
						$repeat_until = strtotime($season['to_']) + 60 * 60 * 24;
						$_POST['repeat_until'] = $season['to_'];
					}

					/**
					 *
					 * Add purchase order from session
					 */

					if ($step == 3)
					{
						$temp_id = phpgw::get_var('temp_id');
						$purchase_order = phpgwapi_cache::session_get('booking', $temp_id);
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
						$err = $this->bo->validate($allocation);
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
								try
								{
									$receipt = $this->bo->add($allocation);
									$allocation['id'] = $receipt['id'];

									if(!empty($purchase_order['lines']) && empty($allocation['application_id']))
									{
										$purchase_order['application_id'] = -1;
										$purchase_order['reservation_type'] = 'allocation';
										$purchase_order['reservation_id'] = $allocation['id'];

										createObject('booking.sopurchase_order')->add_purchase_order($purchase_order);
									}
									else
									{
										$purchase_order_id = $this->sopurchase_order->copy_purchase_order_from_application($allocation, $receipt['id'], 'allocation');
										if($purchase_order_id)
										{
											$purchase_order_result =  $this->sopurchase_order->get_single_purchase_order($purchase_order_id);
											$this->add_cost_history($allocation, lang('cost is set'), $purchase_order_result['sum']);
											$allocation['cost'] = (float)$purchase_order_result['sum'];
											$this->bo->update($allocation);
										}
									}
								}
								catch (booking_unauthorized_exception $e)
								{
									$errors['global'] = lang('Could not add object due to insufficient permissions');
								}
							}
						}
						$i++;
					}
					if ($step == 3)
					{
						$this->bo->so->update_id_string();
						self::redirect(array('menuaction' => 'booking.uiallocation.show', 'id' => $receipt['id']));
					}
				}
			}
			if (phpgw::get_var('building_name', 'string') == '')
			{
				array_set_default($allocation, 'resources', array());
				$weekday = 'monday';
			}
			else
			{
				$dateformat =  phpgw::get_var('dateformat', 'string');
				$dateTimeFrom = phpgw::get_var('from_', 'string');
				$dateTimeTo = phpgw::get_var('to_', 'string');
				if(is_array($dateTimeFrom))
				{
					$dateTimeFrom = $dateTimeFrom[0];
					$dateTimeTo = $dateTimeTo[0];
				}
				$dateTimeFromE = explode(" ", $dateTimeFrom);
				$dateTimeToE = explode(" ", $dateTimeTo);
				if ($dateTimeFrom < 14)
				{
					$timeFrom = $dateTimeFrom;
					$timeTo = $dateTimeTo;
				}
				else
				{
					$timeFrom = end($dateTimeFromE);
					$timeTo = end($dateTimeToE);
				}

				array_set_default($allocation, 'resources', array(phpgw::get_var('resource', 'int')));
				array_set_default($allocation, 'building_id', phpgw::get_var('building_id', 'int'));
				array_set_default($allocation, 'building_name', phpgw::get_var('building_name', 'string'));
				array_set_default($allocation, 'from_', $timeFrom);
				array_set_default($allocation, 'to_', $timeTo);
				$weekday = phpgw::get_var('weekday', 'string');
			}

			$this->flash_form_errors($errors);
			$GLOBALS['phpgw']->js->validate_file('alertify', 'alertify.min', 'phpgwapi');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/alertify.min.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/themes/bootstrap.min.css');
			self::add_javascript('booking', 'base', 'allocation.js');

			$allocation['resources_json'] = json_encode(array_map('intval', $allocation['resources']));
			$allocation['cancel_link'] = self::link(array('menuaction' => 'booking.uiallocation.index'));
			array_set_default($allocation, 'cost', '0');

//			$_timeFrom = $timeFrom ? $timeFrom : '';
			$_timeTo = $timeTo ? $timeTo : '';

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Allocation New'), 'link' => '#allocation_new');
			$active_tab = 'generic';

			$allocation['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$allocation['validator'] = phpgwapi_jquery::formvalidator_generate(array('date', 'security'));

			if ($step < 2)
			{
				if($dateformat == 'Y-m-d' && $_SERVER['REQUEST_METHOD'] == 'GET')
				{
					$_dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
					$allocation['from_'] = date("{$_dateformat} H:i",strtotime($dateTimeFrom));
					$_timeFrom = strtotime($dateTimeFrom);
					$allocation['to_'] = date("{$_dateformat} H:i",strtotime($dateTimeTo));
					$_timeTo = strtotime($dateTimeTo);
				}
				else
				{
					$allocation['from_'] = $dateTimeFrom;
					$_timeFrom = phpgwapi_datetime::date_to_timestamp($dateTimeFrom);
					$allocation['to_'] = $dateTimeTo;
					$_timeTo = phpgwapi_datetime::date_to_timestamp($dateTimeTo);
				}
				if ($_SERVER['REQUEST_METHOD'] == 'POST' && $errors)
				{
	//				$allocation['from_'] = strftime("%H:%M", strtotime($_POST['weekday'] . " " . $_POST['from_']));
	//				$allocation['to_'] = strftime("%H:%M", strtotime($_POST['weekday'] . " " . $_POST['to_']));
	//				$_timeFrom = $allocation['from_'];
	//				$_timeTo = $allocation['to_'];
				}

				$GLOBALS['phpgw']->jqcal2->add_listener('field_repeat_until', 'date');
				$GLOBALS['phpgw']->jqcal2->add_listener('field_from', 'datetime', $_timeFrom);
				$GLOBALS['phpgw']->jqcal2->add_listener('field_to', 'datetime', $_timeTo);

				$config = CreateObject('phpgwapi.config', 'booking')->read();

				if( !empty($config['activate_application_articles']))
				{
					self::add_javascript('booking', 'base', 'purchase_order_edit.js');
				}

				self::render_template_xsl('allocation_new', array(
					'allocation'	 => $allocation,
					'step'			 => $step,
					'interval'		 => $_POST['field_interval'],
					'outseason'		 => $_POST['outseason'],
					'repeat_until'	 => pretty_timestamp($_POST['repeat_until']),
					'outseason'		 => $_POST['outseason'],
					'weekday'		 => $weekday,
					'tax_code_list'	 => json_encode(execMethod('booking.bogeneric.read', array('location_info' => array('type' => 'tax', 'order' => 'id')))),
					'config'		 => $config
				));
			}
			else if ($step == 2)
			{

				$temp_id = 'allocation_'. time();

				$this->compile_purchase_order($temp_id);

				self::render_template_xsl('allocation_new_preview', array('allocation' => $allocation,
					'step' => $step,
					'temp_id' => $temp_id,
					'outseason' => $_POST['outseason'],
					'interval' => $_POST['field_interval'],
					'repeat_until' => pretty_timestamp($_POST['repeat_until']),
					'weekday' => $weekday,
					'from_date' => $from_date,
					'to_date' => $to_date,
					'valid_dates' => $valid_dates,
					'invalid_dates' => $invalid_dates
				));
			}
		}

		private function compile_purchase_order($temp_id, $ret = false)
		{
			/**
			 * Start dealing with the purchase_order..
			 */
			$purchase_order = array('status' => 0, 'customer_id' => -1, 'lines' => array());
			$selected_articles = (array)phpgw::get_var('selected_articles');

			foreach ($selected_articles as $selected_article)
			{
				$_article_info = explode('_', $selected_article);

				if(empty($_article_info[0]))
				{
					continue;
				}

				/**
				 * the value selected_articles[]
				 * <mapping_id>_<quantity>_<tax_code>_<ex_tax_price>_<parent_mapping_id>
				 */
				$purchase_order['lines'][] = array(
					'article_mapping_id'	=> $_article_info[0],
					'quantity'				=> $_article_info[1],
					'tax_code'				=> $_article_info[2],
					'ex_tax_price'			=> $_article_info[3],
					'parent_mapping_id'		=> !empty($_article_info[4]) ? $_article_info[4] : null
				);
			}

			if($ret)
			{
				return $purchase_order;
			}
			else
			{
				phpgwapi_cache::session_set('booking', $temp_id , $purchase_order);
			}

		}

		private function send_mailnotification_to_organization( $organization, $subject, $body )
		{
			$send = CreateObject('phpgwapi.send');

			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";

			if (strlen(trim($body)) == 0)
			{
				return false;
			}

			foreach ($organization['contacts'] as $contact)
			{
				if (strlen($contact['email']) > 0)
				{
					try
					{
						$send->msg('email', $contact['email'], $subject, $body, '', '', '', $from, 'AktivKommune', 'html');
					}
					catch (Exception $e)
					{

					}
				}
			}
		}

		public function edit()
		{
			$id = phpgw::get_var('id', 'int');
			if (!$id)
			{
				phpgw::no_access('booking', lang('missing id'));
			}
			$allocation = $this->bo->read_single($id);

			if(!$allocation)
			{
				phpgw::no_access('booking', lang('missing entry. Id %1 is invalid', $id));
			}

			$allocation['building'] = $this->building_bo->so->read_single($allocation['building_id']);
			$allocation['building_name'] = $allocation['building']['name'];
			$errors = array();
			$tabs = array();
			$tabs['generic'] = array('label' => lang('edit allocation'), 'link' => '#allocations_edit');
			$active_tab = 'generic';

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$allocation['skip_bas'] = (int)phpgw::get_var('skip_bas', 'int');
				$_POST['from_'] = ($_POST['from_']) ? date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['from_'])) : $_POST['from_'];
				$_POST['to_'] = ($_POST['to_']) ? date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['to_'])) : $_POST['to_'];
				array_set_default($_POST, 'resources', array());
				$allocation = array_merge($allocation, extract_values($_POST, $this->fields));
				$organization = $this->organization_bo->read_single(intval(phpgw::get_var('organization_id', 'int', 'POST')));

				if ($_POST['cost'] != $_POST['cost_orig'])
				{
					$this->add_cost_history($allocation, phpgw::get_var('cost_comment'), phpgw::get_var('cost', 'float'));
				}

				$errors = $this->bo->validate($allocation);
				if (!$errors)
				{
					try
					{
						/**
						 * Start dealing with the purchase_order..
						 */
						$purchase_order = $this->compile_purchase_order(0, true);
						$purchase_order['application_id'] = !empty($allocation['application_id']) ? $allocation['application_id'] : -1;
						$purchase_order['reservation_type'] = 'allocation';
						$purchase_order['reservation_id'] = $id;

						if(!empty($purchase_order['lines']))
						{
							$purchase_order_id = $this->sopurchase_order->add_purchase_order($purchase_order);
							$purchase_order_result =  $this->sopurchase_order->get_single_purchase_order($purchase_order_id);
							if($purchase_order_result['sum'] && $purchase_order_result['sum'] != $allocation['cost'])
							{
								$this->add_cost_history($allocation, lang('cost is set'), $purchase_order_result['sum']);
								$allocation['cost'] = $purchase_order_result['sum'];
							}
						}

						/** END purchase order */
						$receipt = $this->bo->update($allocation);

						$this->bo->so->update_id_string();
						$this->send_mailnotification_to_organization($organization, lang('Allocation changed'), phpgw::get_var('mail', 'html', 'POST'));
						self::redirect(array('menuaction' => 'booking.uiallocation.show', 'id' => $allocation['id']));
					}
					catch (booking_unauthorized_exception $e)
					{
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
				}
			}

			$allocation['from_'] = pretty_timestamp($allocation['from_']);
			$allocation['to_'] = pretty_timestamp($allocation['to_']);

			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'base', 'allocation.js');

			$config = CreateObject('phpgwapi.config', 'booking')->read();

			$purchase_order = $this->sopurchase_order->get_purchase_order(0, 'allocation', $allocation['id']);


			$completed_reservations = CreateObject('booking.socompleted_reservation')->read(array(
				'filters'	 => array(
					'reservation_type'	 => 'allocation',
					'reservation_id'	 => $allocation['id'],
					'exported'			 => null),
				'results'	 => -1));

			if($purchase_order && !empty($config['activate_application_articles']))
			{
				if(!empty($completed_reservations['results'][0]['exported']))
				{
					self::add_javascript('bookingfrontend', 'base', 'purchase_order_show.js');
				}
				else
				{
					self::add_javascript('booking', 'base', 'purchase_order_edit.js');

				}
			}

			$GLOBALS['phpgw']->js->validate_file('alertify', 'alertify.min', 'phpgwapi');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/alertify.min.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/themes/bootstrap.min.css');

			$allocation['resources_json'] = json_encode(array_map('intval', $allocation['resources']));
			$allocation['cancel_link'] = self::link(array('menuaction' => 'booking.uiallocation.show',
					'id' => $allocation['id']));
			$allocation['application_link'] = self::link(array('menuaction' => 'booking.uiapplication.show',
					'id' => $allocation['application_id']));
			$allocation['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$allocation['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));
			$cost_history = $this->bo->so->get_ordered_costs($id);

			$GLOBALS['phpgw']->jqcal2->add_listener('field_from', 'datetime', phpgwapi_datetime::date_to_timestamp($allocation['from_']));
			$GLOBALS['phpgw']->jqcal2->add_listener('field_to', 'datetime', phpgwapi_datetime::date_to_timestamp($allocation['to_']));

			self::render_template_xsl('allocation_edit', array(
				'allocation' => $allocation,
				'cost_history' => $cost_history,
				'tax_code_list'	=> json_encode(execMethod('booking.bogeneric.read', array('location_info' => array('type' => 'tax', 'order' => 'id')))),
				'config'		 => $config,

				));
		}

		public function delete()
		{
			$id = phpgw::get_var('id', 'int');
			$outseason = phpgw::get_var('outseason', 'string');
			$recurring = phpgw::get_var('recurring', 'string');
			$repeat_until = phpgw::get_var('repeat_until', 'string');
			$field_interval = phpgw::get_var('field_interval', 'int');
			$allocation = $this->bo->read_single($id);
			$season = $this->season_bo->read_single($allocation['season_id']);
			$step = phpgw::get_var('step', 'string', 'REQUEST', 1);
			$errors = array();
			$invalid_dates = array();
			$valid_dates = array();


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
						$err = $this->bo->so->delete_allocation($id);
						self::redirect(array('menuaction' => 'booking.uimassbooking.schedule', 'id' => $allocation['building_id']));
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
								$stat = $this->bo->so->delete_allocation($id);
							}
						}
						$i++;
					}
					if ($step == 3)
					{
						self::redirect(array('menuaction' => 'booking.uimassbooking.schedule', 'id' => $allocation['building_id']));
					}
				}
			}

			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'base', 'allocation.js');

			$allocation['from_'] = pretty_timestamp($allocation['from_']);
			$allocation['to_'] = pretty_timestamp($allocation['to_']);

			$allocation['resources_json'] = json_encode(array_map('intval', (array)$allocation['resources']));
			$allocation['cancel_link'] = self::link(array('menuaction' => 'booking.uiallocation.show',
					'id' => $allocation['id']));
			$allocation['application_link'] = self::link(array('menuaction' => 'booking.uiapplication.show',
					'id' => $allocation['application_id']));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Allocation Delete'), 'link' => '#allocation_delete');
			$active_tab = 'generic';
			$allocation['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			$GLOBALS['phpgw']->jqcal2->add_listener('field_repeat_until', 'date');

			if ($step < 2)
			{
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
					'valid_dates' => $valid_dates,
					'invalid_dates' => $invalid_dates
				));
			}
		}

		private function send_sms_participants($id, $type = 'allocation')
		{
			$send_sms = phpgw::get_var('send_sms', 'bool');
			$sms_content = phpgw::get_var('sms_content', 'string');

			if($send_sms && $sms_content)
			{
				$soparticipant = createObject('booking.soparticipant');
				$params = array(
					'results' => -1,
					'filters' => array(
						'reservation_id' => $id,
						'reservation_type' => $type
						)
				);

				$participants = $soparticipant->read($params);
				if(!$participants['results'])
				{
					phpgwapi_cache::message_set(lang('no records found.'), 'error');
				}

				$sms_service = CreateObject('sms.sms');
				$sms_recipients = array();
				foreach ($participants['results'] as $participant)
				{
					$sms_recipients[] = $participant['phone'];
				}
				$final_recipients = array_unique($sms_recipients);

				$account_id = $this->current_account_id();
				$log_success = array();
				$log_error = array();
				foreach ($final_recipients as $final_recipient)
				{
					try
					{
						$sms_res = $sms_service->websend2pv($account_id, $final_recipient, $sms_content);
						if (empty($sms_res[0][0]))
						{
							$error_message = 'SMS-melding feilet til ' . $final_recipient;
							phpgwapi_cache::message_set($error_message, 'error');
							$log_error[] = $final_recipient;
						}
						else
						{
							$comment = 'SMS-melding er sendt til ' . $final_recipient;
							phpgwapi_cache::message_set($comment, 'message');
							$log_success[] = $final_recipient;

						}
					}
					catch (Exception $ex)
					{
						$error_message = 'SMS-melding feilet til ' . $final_recipient;
						phpgwapi_cache::message_set($error_message, 'error');
						$log_error[] = $final_recipient;
					}

				}

				self::redirect(array('menuaction' => "booking.ui{$type}.show", 'id' => $id));
			}
		}

		public function show()
		{
			$id = phpgw::get_var('id', 'int');

			if (!$id)
			{
				phpgw::no_access('booking', lang('missing id'));
			}

			$this->send_sms_participants($id, 'allocation');

			$allocation = $this->bo->read_single($id);

			if(!$allocation)
			{
				phpgw::no_access('booking', lang('missing entry. Id %1 is invalid', $id));
			}

			$allocation['allocations_link'] = self::link(array('menuaction' => 'booking.uiallocation.index'));
			$allocation['delete_link'] = self::link(array('menuaction' => 'booking.uiallocation.delete',
					'id' => $allocation['id'], 'from_' => $allocation['from_'], 'to_' => $allocation['to_'],
					'resource' => $allocation['resource']));
			$allocation['edit_link'] = self::link(array('menuaction' => 'booking.uiallocation.edit',
					'id' => $allocation['id']));
			$allocation['application_link'] = self::link(array('menuaction' => 'booking.uiapplication.show',
					'id' => $allocation['application_id']));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Allocations'), 'link' => '#allocations');
			$active_tab = 'generic';

			$resource_ids = '';
			foreach ($allocation['resources'] as $res)
			{
				$resource_ids = $resource_ids . '&filter_id[]=' . $res;
			}
			$allocation['resource_ids'] = $resource_ids;
			$allocation['resources_json'] = json_encode(array_map('intval', $allocation['resources']));
			$allocation['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$config = CreateObject('phpgwapi.config', 'booking')->read();

			if (!empty($config['activate_application_articles']))
			{
				self::add_javascript('bookingfrontend', 'base', 'purchase_order_show.js');
			}

			self::render_template_xsl('allocation', array(
				'allocation' => $allocation,
				'config'	 => $config
			));
		}

		public function info()
		{
			$allocation = $this->bo->read_single(phpgw::get_var('id', 'int'));
			$resources = $this->resource_bo->so->read(array('filters' => array('id' => $allocation['resources']),
				'sort' => 'name','results' => -1));
			$allocation['resources'] = $resources['results'];
			$res_names = array();
			foreach ($allocation['resources'] as $res)
			{
				$res_names[] = $res['name'];
			}

			$allocation['resource'] = phpgw::get_var('resource');
			$allocation['application_link'] = self::link(array('menuaction' => 'booking.uiapplication.show',
					'id' => $allocation['application_id']));
			$allocation['resource_info'] = join(', ', $res_names);
			$allocation['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show',
					'id' => $allocation['building_id']));
			$allocation['org_link'] = self::link(array('menuaction' => 'booking.uiorganization.show',
					'id' => $allocation['organization_id']));
			$allocation['delete_link'] = self::link(array('menuaction' => 'booking.uiallocation.delete',
					'id' => $allocation['id'], 'from_' => $allocation['from_'], 'to_' => $allocation['to_'],
					'resource' => $allocation['resource']));
			$allocation['add_link'] = self::link(array('menuaction' => 'booking.uibooking.add',
					'allocation_id' => $allocation['id'], 'from_' => $allocation['from_'], 'to_' => $allocation['to_'],
					'resource' => $allocation['resource']));
			$allocation['when'] = pretty_timestamp($allocation['from_']) . ' - ' . pretty_timestamp($allocation['to_']);
			self::render_template_xsl('allocation_info', array('allocation' => $allocation));
			$GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
		}
	}