<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('phpgwapi.send');

	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');

	phpgw::import_class('phpgwapi.uicommon_jquery');

	class booking_uievent extends booking_uicommon
	{

		public $public_functions = array
			(
			'index'					 => true,
			'query'					 => true,
			'add'					 => true,
			'edit'					 => true,
			'delete'				 => true,
			'info'					 => true,
			'toggle_show_inactive'	 => true,
			'send_sms_participants'	 => true
		);
		protected
			$account,
			$customer_id,
			$activity_bo,
			$agegroup_bo,
			$audience_bo,
			$organization_bo,
			$resource_bo,
			$sopurchase_order,
			$fields,$display_name;

		public function __construct()
		{
			parent::__construct();
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo = CreateObject('booking.boevent');
			$this->customer_id = CreateObject('booking.customer_identifier');
			$this->activity_bo = CreateObject('booking.boactivity');
			$this->agegroup_bo = CreateObject('booking.boagegroup');
			$this->audience_bo = CreateObject('booking.boaudience');
			$this->organization_bo = CreateObject('booking.boorganization');
			$this->resource_bo = CreateObject('booking.boresource');
			$this->sopurchase_order = createObject('booking.sopurchase_order');
			self::set_active_menu('booking::applications::events');
			$this->fields = array('activity_id', 'name', 'organizer', 'homepage', 'description', 'equipment',
				'resources', 'cost', 'application_id',
				'building_id', 'building_name',
				'contact_name', 'contact_email', 'contact_phone',
				'from_', 'to_', 'active', 'skip_bas', 'audience', 'reminder',
				'is_public', 'sms_total', 'participant_limit','customer_internal', 'include_in_list',
				'customer_organization_name','customer_organization_id',
				'additional_invoice_information');

			$this->display_name = lang('events');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('booking') . "::{$this->display_name}";

		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
			$GLOBALS['phpgw']->jqcal2->add_listener('filter_from');

			$data = array(
				'datatable_name' => $this->display_name,
				'form' => array(
					'toolbar' => array(
						'item' => array(
//							array('type' => 'filter',
//								'name' => 'completed',
//								'text' => lang('completed') . ':',
//								'list' => array(
//									array('id' => 0, 'name' => lang('Not selected')),
//									array('id' => -1, 'name' => lang('Not completed')),
//									array('id' => 1, 'name' => lang('completed'))
//									),
//							),
							array('type' => 'filter',
								'name' => 'buildings',
								'text' => lang('Building') . ':',
								'list' => $this->bo->so->get_buildings(),
							),
							array('type' => 'filter',
								'name' => 'activities',
								'text' => lang('Activity') . ':',
								'list' => $this->bo->so->get_activities_main_level(),
							),
							array(
								'type' => 'date-picker',
								'id' => 'from',
								'name' => 'from',
								'value' => '',
								'text' => lang('from') . ':',
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
					'source' => self::link(array('menuaction' => 'booking.uievent.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'name',
							'label' => lang('Name'),
						),
						array(
							'key' => 'description',
							'label' => lang('Description'),
						),
						array(
							'key' => 'activity_name',
							'label' => lang('Activity'),
						),
						array(
							'key' => 'customer_organization_name',
							'label' => lang('Organization'),
						),
						array(
							'key' => 'contact_name',
							'label' => lang('Contact'),
						),
						array(
							'key' => 'contact_phone',
							'label' => lang('phone')
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
							'key' => 'from_',
							'label' => lang('From')
						),
						array(
							'key' => 'to_',
							'label' => lang('To')
						),
						array(
							'key' => 'active',
							'label' => lang('Active')
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
			$data['datatable']['new_item'] = self::link(array('menuaction' => 'booking.uievent.add'));
			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			$filters = array();
			$testdata = phpgw::get_var('buildings', 'int', 'REQUEST', null);
			if ($testdata != 0)
			{
				$filters['building_name'] = $this->bo->so->get_building(phpgw::get_var('buildings', 'int', 'REQUEST', null));
			}
			else
			{
				unset($filters['building_name']);
			}
			$testdata2 = phpgw::get_var('activities', 'int', 'REQUEST', null);
			if ($testdata2 != 0)
			{
				$filters['activity_id'] = $this->bo->so->get_activities(phpgw::get_var('activities', 'int', 'REQUEST', null));
			}
			else
			{
				unset($filters['activity_id']);
			}

			$completed = phpgw::get_var('completed', 'int', 'REQUEST');

			if($completed === -1)
			{
				$filters['completed'] = 0;
				$filters['active'] = 1;
			}
			else if($completed)
			{
				$filters['completed'] = $completed;
			}

			$filter_from = phpgw::get_var('from', 'string', 'REQUEST', null);
			$from_date = $filter_from ? $filter_from : phpgw::get_var('filter_from', 'string', 'REQUEST', null);

			if ($from_date)
			{
				$filter_from2 = date('Y-m-d', phpgwapi_datetime::date_to_timestamp($from_date));
				$filters['where'][] = "%%table%%" . sprintf(".from_ >= '%s 00:00:00'", $GLOBALS['phpgw']->db->db_addslashes($filter_from2));
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


			$events = $this->bo->so->read($params);

			foreach ($events['results'] as &$event)
			{
				$event['from_'] = pretty_timestamp($event['from_']);
				$event['to_'] = pretty_timestamp($event['to_']);
				$event['cost_history'] = count($this->bo->so->get_ordered_costs($event['id']));

				$resources = $this->resource_bo->so->read(array(
								'sort'    => 'sort',
								'results' =>'all',
								'filters' => array('id' => $event['resources']), 'results' =>'all'
					));
				$resource_names = array();
				if($resources['results'])
				{
					foreach ($resources['results'] as $resource)
					{
						$resource_names[] = $resource['name'];
					}
				}
				$event['resource_names'] = implode(", " , $resource_names);
			}

			array_walk($events["results"], array($this, "_add_links"), "booking.uievent.edit");
			return $this->jquery_results($events);
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

		protected function extract_form_data( $defaults = array() )
		{
			$entity = array_merge($defaults, extract_values($_POST, $this->fields));
			$this->agegroup_bo->extract_form_data($entity);
			$this->extract_customer_identifier($entity);
			return $entity;
		}

		protected function extract_and_validate( $defaults = array() )
		{
			$entity = $this->extract_form_data($defaults);
			$errors = $this->validate($entity);
			return array($entity, $errors);
		}

		protected function add_comment( &$event, $comment, $type = 'comment' )
		{
			$event['comments'][] = array(
				'time' => 'now',
				'author' => $this->current_account_fullname(),
				'comment' => $comment,
				'type' => $type
			);
		}

		protected function add_cost_history( &$event, $comment = '', $cost = '0.00' )
		{
			if (!$comment)
			{
				$comment = lang('cost is set');
			}

			$event['costs'][] = array(
				'time' => 'now',
				'author' => $this->current_account_fullname(),
				'comment' => $comment,
				'cost' => (float)$cost
			);
		}

		protected function create_sendt_mail_notification_comment_text( $event, $errors )
		{
			$data = array();

			foreach ($errors['allocation'][0] as $e)
			{
				foreach ($event['resources'] as $res)
				{
					$time = $this->bo->so->get_overlap_time_info($res, $e, 'allocation');

					$from_ = new DateTime($time['from']);
					$to_ = new DateTime($time['to']);
					$date = $from_->format('d-m-Y');
					$start = $from_->format('H:i');
					$end = $to_->format('H:i');

					if ($start == $end)
					{
						continue;
					}

					$resource = $this->bo->so->get_resource_info($res);
					$_mymail = $this->bo->so->get_contact_mail($e, 'allocation');

					if(!empty($_mymail[0]['email']))
					{
						$a = $_mymail[0]['email'];
						if (array_key_exists($a, $data))
						{
							$data[$a][] = array(
								'phone' => $_mymail[0]['phone'],
								'date' => $date, 'building' => $event['building_name'],
								'resource' => $resource['name'], 'start' => $start, 'end' => $end);
						}
						else
						{
							$data[$a] = array(array(
								'phone' => $_mymail[0]['phone'],
								'date' => $date, 'building' => $event['building_name'],
									'resource' => $resource['name'], 'start' => $start, 'end' => $end));
						}
					}

					if (!empty($_mymail[1]['email']))
					{
						$b = $_mymail[1]['email'];
						if (array_key_exists($b, $data))
						{
							$data[$b][] = array(
								'phone' => $_mymail[1]['phone'],
								'date' => $date, 'building' => $event['building_name'],
								'resource' => $resource['name'], 'start' => $start, 'end' => $end);
						}
						else
						{
							$data[$b] = array(array(
								'phone' => $_mymail[1]['phone'],
								'date' => $date, 'building' => $event['building_name'],
									'resource' => $resource['name'], 'start' => $start, 'end' => $end));
						}
					}
				}
			}

			foreach ($errors['booking'][0] as $e)
			{
				foreach ($event['resources'] as $res)
				{
					$time = $this->bo->so->get_overlap_time_info($res, $e, 'booking');

					$from_ = new DateTime($time['from']);
					$to_ = new DateTime($time['to']);
					$date = $from_->format('d-m-Y');
					$start = $from_->format('H:i');
					$end = $to_->format('H:i');

					if ($start == $end)
					{
						continue;
					}

					$resource = $this->bo->so->get_resource_info($res);
					$_mymail = $this->bo->so->get_contact_mail($e, 'booking');

					if(!empty($_mymail[0]['email']))
					{
						$a = $_mymail[0]['email'];
						if (array_key_exists($a, $data))
						{
							$data[$a][] = array(
								'phone' => $_mymail[0]['phone'],
								'date' => $date, 'building' => $event['building_name'],
								'resource' => $resource['name'], 'start' => $start, 'end' => $end);
						}
						else
						{
							$data[$a] = array(array(
								'phone' => $_mymail[0]['phone'],
								'date' => $date, 'building' => $event['building_name'],
									'resource' => $resource['name'], 'start' => $start, 'end' => $end));
						}
					}

					if (!empty($_mymail[1]['email']))
					{
						$b = $_mymail[1]['email'];
						if (array_key_exists($b, $data))
						{
							$data[$b][] = array(
								'phone' => $_mymail[1]['phone'],
								'date' => $date, 'building' => $event['building_name'],
								'resource' => $resource['name'], 'start' => $start, 'end' => $end);
						}
						else
						{
							$data[$b] = array(array(
								'phone' => $_mymail[1]['phone'],
								'date' => $date, 'building' => $event['building_name'],
								'resource' => $resource['name'], 'start' => $start, 'end' => $end));
						}
					}
				}
			}
			return $data;
		}

		public function add()
		{
			$errors = array();
			$default_is_public = !empty($GLOBALS['phpgw_info']['user']['preferences']['booking']['event_is_public']) && $GLOBALS['phpgw_info']['user']['preferences']['booking']['event_is_public'] == 'public' ?  1 : 0;
			$event = array(
				'customer_internal' => 0,
				'is_public' => $default_is_public
				);

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$event['skip_bas'] = (int)phpgw::get_var('skip_bas', 'int');

				array_set_default($_POST, 'from_', array());
				array_set_default($_POST, 'to_', array());

				if(isset($_POST['from_']))
				{
					if(is_array($_POST['from_']))
					{
						foreach ($_POST['from_'] as &$from)
						{
							$from = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($from));
						}
						foreach ($_POST['to_'] as &$to)
						{
							$to = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($to));
						}
					}
					else
					{
						$_POST['from_'] = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['from_']));
						$_POST['to_'] = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['to_']));
					}
				}

				$event['dates'] = array_map(array($this, '_combine_dates'),(array)$_POST['from_'], (array)$_POST['to_']);

				array_set_default($_POST, 'resources', array());
				$event['active'] = '1';
				$event['completed'] = '0';

				array_set_default($event, 'audience', array());
				array_set_default($event, 'agegroups', array());
				$event['secret'] = $this->generate_secret();
				$event['is_public'] = $default_is_public;
				$event['include_in_list'] = 0;
				$event['building_name'] = $_POST['building_name'];

				if ($_POST['organization_name'] || $_POST['org_id2'])
				{
					if ($_POST['organization_name'])
					{
						$event['customer_organization_name'] = $_POST['organization_name'];
						$event['customer_organization_id'] = $_POST['organization_id'];
						$organization = $this->organization_bo->read_single(intval(phpgw::get_var('organization_id', 'POST')));
					}
					else
					{
						$orgid = $this->bo->so->get_org($_POST['org_id2']);
						$event['org_id2'] = $_POST['org_id2'];
						$event['customer_organization_name'] = $orgid['name'];
						$event['customer_organization_id'] = $orgid['id'];
						$organization = $this->organization_bo->read_single(intval($orgid['id']));
					}

					if ($organization['customer_internal'] == 0)
					{
						$_POST['customer_identifier_type'] = $organization['customer_identifier_type'];
						$_POST['customer_internal'] = $organization['customer_internal'];
						if (strlen($organization['customer_organization_number']) == 9)
						{
							$_POST['customer_organization_number'] = $organization['customer_organization_number'];
						}
						else
						{
							$errors['organization_number'] = lang('The organization number is wrong or not present');
						}
					}
					else
					{
						$_POST['customer_identifier_type'] = 'organization_number';
						$_POST['customer_internal'] = $organization['customer_internal'];
						if ((strlen($organization['customer_number']) == 6) || (strlen($organization['customer_number']) == 5))
						{
							$_POST['customer_organization_number'] = $organization['customer_number'];
						}
						else
						{
							$errors['resource_number'] = lang('The resource number is wrong or not present');
						}
					}
					if ($organization['contacts'][0]['name'] != '')
					{
						$_POST['contact_name'] = $organization['contacts'][0]['name'];
						$_POST['contact_email'] = $organization['contacts'][0]['email'];
						$_POST['contact_phone'] = $organization['contacts'][0]['phone'];
					}
					else
					{
						$_POST['contact_name'] = $organization['contacts'][1]['name'];
						$_POST['contact_email'] = $organization['contacts'][1]['email'];
						$_POST['contact_phone'] = $organization['contacts'][1]['phone'];
					}
				}
				if (is_array($event['dates']))//(!$_POST['application_id'])
				{
					$temp_errors = array();
					foreach ($event['dates'] as $checkdate)
					{
						$event['from_'] = $checkdate['from_'];
						$_POST['from_'] = $checkdate['from_'];
						$event['to_'] = $checkdate['to_'];
						$_POST['to_'] = $checkdate['to_'];
						list($event, $errors) = $this->extract_and_validate($event);
						$time_from = explode(" ", $_POST['from_']);
						$time_to = explode(" ", $_POST['to_']);
						if ($time_from[0] == $time_to[0])
						{
							if ($time_from[1] >= $time_to[1])
							{
								$errors['time'] = lang('Time is set wrong');
							}
						}
						if ($errors != array())
						{
							$temp_errors = $errors;
						}
					}
					$errors = $temp_errors;
				}
				else
				{
					list($event, $errors) = $this->extract_and_validate($event);
					$time_from = explode(" ", $_POST['from_']);
					$time_to = explode(" ", $_POST['to_']);
					if ($time_from[0] == $time_to[0])
					{
						if ($time_from[1] >= $time_to[1])
						{
							$errors['time'] = lang('Time is set wrong');
						}
					}
				}

				if ($_POST['cost'] != 0 and ! $event['customer_organization_number'] and ! $event['customer_ssn'])
				{
					$errors['invoice_data'] = lang('There is set a cost, but no invoice data is filled inn');
				}
				if ($_POST['cost'] != 0)
				{
					$this->add_cost_history($event, lang('cost is set'), phpgw::get_var('cost', 'float'));
				}
				if (($_POST['organization_name'] != '' or $_POST['org_id2'] != '') and isset($errors['contact_name']))
				{
					$errors['contact_name'] = lang('Organization is missing booking charge');
				}
				if (!$errors['event'] && !$errors['from_'] && !$errors['time'] && !$errors['invoice_data'] && !$errors['resource_number'] && !$errors['organization_number'] && !$errors['contact_name'] && !$errors['cost'] && !$errors['activity_id'])
				{
					if (!$_POST['application_id'])
					{
						$allids = array();
						foreach ($event['dates'] as $checkdate)
						{
							$event['from_'] = $checkdate['from_'];
							$event['to_'] = $checkdate['to_'];

							unset($event['comments']);
							if (count($event['dates']) < 2)
							{
								$this->add_comment($event, lang('Event was created'));
								$receipt = $this->bo->add($event);
							}
							else
							{
								$this->add_comment($event, lang('Multiple Events was created'));
								$receipt = $this->bo->add($event);
								$allids[] = array($receipt['id']);
							}
						}
						if ($allids)
						{
							$this->bo->so->update_comment($allids);
							$this->bo->so->update_id_string();
						}

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

						if (!empty($purchase_order['lines']))
						{
							$purchase_order['application_id']	 = -1;
							$purchase_order['reservation_type']	 = 'event';
							$purchase_order['reservation_id']	 = $receipt['id'];
							createObject('booking.sopurchase_order')->add_purchase_order($purchase_order);
						}
					}
					else
					{
						$this->add_comment($event, lang('Event was created'));
						$receipt = $this->bo->add($event);
						$this->sopurchase_order->copy_purchase_order_from_application($event, $receipt['id'], 'event');
						$this->bo->so->update_id_string();

					}
					self::redirect(array('menuaction' => 'booking.uievent.edit', 'id' => $receipt['id'],
						'secret' => $event['secret'], 'warnings' => $errors));
				}
			}
			if ($errors['event'])
			{
				$errors['warning'] = lang('NB! No data will be saved, if you navigate away you will loose all.');
			}
			$default_dates = array_map(array($this, '_combine_dates'), array(''), array(''));
			array_set_default($event, 'dates', $default_dates);

			if (!phpgw::get_var('from_report', 'POST'))
			{
				/**
				 * Translate into text
				 */
				if ($errors['allocation'] && is_array($errors['allocation']))
				{
					$errors['allocation'] = lang('Overlaps with existing allocation %1. Remember to send a notification', " #" . implode(', #',$errors['allocation'][0]));
				}
				if ($errors['booking'] && is_array($errors['booking']))
				{
					$errors['booking'] = lang('Overlaps with existing booking %1. Remember to send a notification', " #" . implode(', #',$errors['booking'][0])) ;
				}
				$this->flash_form_errors($errors);
			}

			self::add_javascript('booking', 'base', 'event.js');
			array_set_default($event, 'resources', array());
			$event['resources_json'] = json_encode(array_map('intval', $event['resources']));
			$event['cancel_link'] = self::link(array('menuaction' => 'booking.uievent.index'));
			array_set_default($event, 'cost', '0');

			$activity_id = phpgw::get_var('activity_id', 'int', 'REQUEST', -1);
			$activity_path = $this->activity_bo->get_path($activity_id);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : -1;
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity);
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience($top_level_activity);
			$audience = $audience['results'];

			$this->install_customer_identifier_ui($event);

			foreach ($event['dates'] as &$date)
			{
				$date['from_'] = pretty_timestamp($date['from_']);
				$date['to_'] = pretty_timestamp($date['to_']);
			}

			$GLOBALS['phpgw']->jqcal2->add_listener('start_date', 'datetime', phpgwapi_datetime::user_localtime(), array('min_date' => phpgwapi_datetime::user_localtime()));
			$GLOBALS['phpgw']->jqcal2->add_listener('end_date', 'datetime', phpgwapi_datetime::user_localtime(), array('min_date' => phpgwapi_datetime::user_localtime()));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Event New'), 'link' => '#event_new');
			$active_tab = 'generic';

			$event['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));
			self::adddatetimepicker();
			self::add_javascript('booking', 'base', 'purchase_order_edit.js');

			self::add_javascript('phpgwapi', 'dateformatter', 'dateformatter.js');
			$GLOBALS['phpgw']->js->validate_file('alertify', 'alertify.min', 'phpgwapi');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/alertify.min.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/themes/bootstrap.min.css');

			$this->add_template_helpers();
			self::render_template_xsl('event_new', array(
				'event'		 => $event,
				'activities' => $activities,
				'agegroups'	 => $agegroups,
				'audience'	 => $audience,
				'tax_code_list'	 => json_encode(execMethod('booking.bogeneric.read', array('location_info' => array('type' => 'tax', 'order' => 'id')))),
				'config'	 => CreateObject('phpgwapi.config', 'booking')->read()
				)
			);
		}

		/**
		 * 
		 * @param array $receiver
		 * @param string $subject
		 * @param string $body
		 */
		private function send_sms_notification( $receiver, $subject, $body )
		{
			if(empty($GLOBALS['phpgw_info']['apps']['sms']))
			{
				return false;
			}

			$sms_service = CreateObject('sms.sms');
			//html -> text..
			$html2text			 = createObject('phpgwapi.html2text', "{$subject}<br/>{$body}");
			$text				 = $html2text->getText();

			if(is_array($receiver))
			{
				$receivers = $receiver;
			}
			else
			{
				$receivers = array($receiver);
			}

			$final_recipients = array_unique($receivers);

			$account_id = $this->current_account_id();
			$log_success = array();
			$log_error = array();
			foreach ($final_recipients as $final_recipient)
			{
				try
				{
					$sms_res = $sms_service->websend2pv($account_id, $final_recipient, $text);
					if (empty($sms_res[0][0]))
					{
						$log_error[] = $final_recipient;
					}
					else
					{
						$log_success[] = $final_recipient;

					}
				}
				catch (Exception $ex)
				{
					$log_error[] = $final_recipient;
				}
			}
			if($log_error)
			{
				$GLOBALS['phpgw']->log->error(array(
					'text'	 => "SMS to %1 failed <br/>content: %2",
					'p1'	 => implode(', ', $log_error),
					'p2'	 => $text,
					'line'	 => __LINE__,
					'file'	 => __FILE__
				));
			}
		}

		private function send_mailnotification( $receiver, $subject, $body )
		{
			$send = CreateObject('phpgwapi.send');

			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";

			if (strlen(trim($body)) == 0)
			{
				return false;
			}

			if (strlen($receiver) > 0)
			{
				try
				{
					return $send->msg('email', $receiver, $subject, $body, '', '', '', $from, 'AktivKommune', 'html');
				}
				catch (Exception $e)
				{
					$GLOBALS['phpgw']->log->error(array(
						'text'	 => "Email to %1 failed <br/>subject: %2 <br/>content: %3 <br/>error: %4",
						'p1'	 => $receiver,
						'p2'	 => $subject,
						'p3'	 => $body,
						'p4'	 => $e->getMessage(),
						'line'	 => __LINE__,
						'file'	 => __FILE__
					));

					// TODO: Inform user if something goes wrong
				}
			}
		}

		public function send_sms_participants()
		{
			if(empty($GLOBALS['phpgw_info']['apps']['sms']))
			{
				phpgwapi_cache::message_set('SMS er deaktivert', 'error');
				return false;
			}

			$type = 'event';;
			$id = phpgw::get_var('id', 'int');
			$send_sms = phpgw::get_var('send_sms', 'bool');
			$sms_content = phpgw::get_var('sms_content', 'string');

			$status = 'error';
			$message = 'Nothing...';
			if($send_sms && $sms_content)
			{
				$message = '';
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
					$message = lang('no records found.');
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
							$log_error[] = $final_recipient;
						}
						else
						{
							$log_success[] = $final_recipient;

						}
					}
					catch (Exception $ex)
					{
						$log_error[] = $final_recipient;
					}

				}

				if($log_success)
				{
					$status = 'ok';
					$message .= 'SMS sendt til: ' . implode(',', $log_success);
					$event = $this->bo->read_single($id);
				}
				if($log_error)
				{
					$message .= "<br/>SMS feilet for: " . implode(',', $log_error);
				}
				$this->add_comment($event, "SMS: $message");
				$this->bo->update($event);

			}
			return array(
				'status' => $status,
				'message' => $message
			);
		}

		public function edit()
		{
			$id = phpgw::get_var('id', 'int');
			if (!$id)
			{
				phpgw::no_access('booking', lang('missing id'));
			}
			$event = $this->bo->read_single($id);
			if(!$event)
			{
				phpgw::no_access('booking', lang('missing entry. Id %1 is invalid', $id));
			}

			$resource_participant_limit_gross = CreateObject('booking.soresource')->get_participant_limit($event['resources'], true);
			$resource_participant_limit = 0;
			if(!empty($resource_participant_limit_gross['results'][0]['quantity']) && $resource_participant_limit_gross['results'][0]['quantity'] > 0)
			{
				$resource_participant_limit = $resource_participant_limit_gross['results'][0]['quantity'];
				phpgwapi_cache::message_set(lang('overridden participant limit is set to %1', $resource_participant_limit),'message');
			}

			if($event['participant_limit'])
			{
				phpgwapi_cache::message_set(lang('overridden participant limit is set to %1', $event['participant_limit']),'message');
			}
			else if($resource_participant_limit)
			{
				phpgwapi_cache::message_set(lang('overridden participant limit is set to %1', $resource_participant_limit),'message');
			}

			$activity_path = $this->activity_bo->get_path($event['activity_id']);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : 0;

			$building_info = $this->bo->so->get_building_info($id);
			$event['building_id'] = $building_info['id'];
			$event['building_name'] = $building_info['name'];
			$config = CreateObject('phpgwapi.config', 'booking')->read();

			$tabs = array();
			$tabs['generic'] = array('label' => lang('edit event'), 'link' => '#event_edit');
			$active_tab = 'generic';

			$external_site_address = isset($config['external_site_address']) && $config['external_site_address'] ? $config['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];
			$link = $external_site_address . '/bookingfrontend/?menuaction=bookingfrontend.uibuilding.schedule&id=' . $event['building_id'] . "&date=" . substr($event['from_'], 0, -9);
			$errors = array();
			$customer = array();

			if ($event['customer_identifier_type'])
			{
				$customer['customer_identifier_type'] = $event['customer_identifier_type'];
				$customer['customer_ssn'] = $event['customer_ssn'];
				$customer['customer_organization_number'] = $event['customer_organization_number'];
				$customer['customer_internal'] = $event['customer_internal'];
				$orginfo = $this->bo->so->get_org($event['customer_organization_number']);
				$customer['customer_organization_id'] = $orginfo['id'];
				$customer['customer_organization_name'] = $orginfo['name'];
			}
			else
			{
				$customer['customer_organization_name'] = $event['customer_organization_name'];
				$customer['customer_organization_id'] = $event['customer_organization_id'];
				$organization = $this->organization_bo->read_single($event['customer_organization_id']);
				$customer['customer_identifier_type'] = 'organization_number';
				$customer['customer_ssn'] = $organization['customer_internal'];
				$customer['customer_organization_number'] = $organization['organization_number'];
				$customer['customer_internal'] = $organization['customer_internal'];
			}

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$event['skip_bas'] = (int)phpgw::get_var('skip_bas', 'int');
				$_POST['from_'] = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['from_']));
				$_POST['to_'] = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['to_']));
			}

			list($event, $errors) = $this->extract_and_validate($event);

			if ($event['description'])
			{
				$event['description'] =  html_entity_decode($event['description']);
			}
			if ($event['customer_organization_number'])
			{
				$orginfo = $this->bo->so->get_org($event['customer_organization_number']);
				$event['customer_organization_id'] = $orginfo['id'];
				$event['customer_organization_name'] = $orginfo['name'];
			}

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				if (!$_POST['organization_name'])
				{
					$event['customer_organization_name'] = Null;
					$event['customer_organization_id'] = Null;
				}
				array_set_default($_POST, 'resources', array());

				/**
				 * Update the bb_event_date - which is used for collision test
				 */
				$event['dates'] = array_map(array($this, '_combine_dates'), array($_POST['from_']), array($_POST['to_']));

				if ($_POST['organization_name'])
				{
					$event['customer_organization_name'] = phpgw::get_var('organization_name', 'string', 'POST');
					$event['customer_organization_id'] = phpgw::get_var('organization_id', 'int', 'POST');
					$organization = $this->organization_bo->read_single(intval(phpgw::get_var('organization_id', 'int')));

					if ($organization['customer_internal'] == 0)
					{
						$event['customer_identifier_type'] = $organization['customer_identifier_type'];
						$event['customer_internal'] = $organization['customer_internal'];
						if (strlen($organization['customer_organization_number']) == 9)
						{
							$event['customer_organization_number'] = $organization['customer_organization_number'];
						}
						else
						{
							$errors['organization_number'] = lang('The organization number is wrong or not present');
						}
					}
					else
					{
						$event['customer_identifier_type'] = 'organization_number';
						$event['customer_internal'] = $organization['customer_internal'];
						if ((strlen($organization['customer_number']) == 6) || (strlen($organization['customer_number']) == 5))
						{
							$event['customer_organization_number'] = $organization['customer_number'];
						}
						else
						{
							$errors['resource_number'] = lang('The resource number is wrong or not present');
						}
					}
				}
				elseif ($_POST['customer_identifier_type'] == 'ssn')
				{
					$event['customer_identifier_type'] = 'ssn';
					$event['customer_ssn'] = phpgw::get_var('customer_ssn');
				}
				elseif ($_POST['customer_identifier_type'] == 'organization_number')
				{
					$event['customer_identifier_type'] = 'organization_number';
					$event['customer_organization_number'] = phpgw::get_var('customer_organization_number', 'string', 'POST');
					$event['customer_organization_name'] = phpgw::get_var('customer_organization_name', 'string', 'POST');
					$event['customer_organization_id'] = phpgw::get_var('customer_organization_id', 'int', 'POST');
				}

				/**
				 * Maker sure: Check if organization is registered
				 */

				$orginfo = $this->bo->so->get_org($event['customer_organization_number']);

				if(empty($orginfo['id']))
				{
					$event['customer_organization_id'] = null;
				}

				if ($_POST['cost'] != 0 and ! $event['customer_organization_number'] and ! $event['customer_ssn'])
				{
					$errors['invoice_data'] = lang('There is set a cost, but no invoice data is filled inn');
				}

				if ($_POST['cost'] != $_POST['cost_orig'])
				{
					$this->add_cost_history($event, phpgw::get_var('cost_comment'), phpgw::get_var('cost', 'float'));
				}

				if (!$errors['event'] and ! $errors['resource_number'] and ! $errors['organization_number'] and ! $errors['invoice_data'] && !$errors['contact_name'] && !$errors['cost'])
				{
		//			if ( phpgw::get_var('sendtorbuilding', 'bool', 'POST') || phpgw::get_var('sendtocontact', 'bool', 'POST') || phpgw::get_var('sendtocollision', 'bool', 'POST') ||  phpgw::get_var('sendsmstocontact', 'bool', 'POST') || phpgw::get_var('sendtorbuilding_email1', 'bool', 'POST') || phpgw::get_var('sendtorbuilding_email2', 'bool', 'POST'))
					{

						if ((phpgw::get_var('sendtocollision', 'bool', 'POST') || phpgw::get_var('sendtocontact', 'bool', 'POST') || phpgw::get_var('sendtorbuilding', 'bool', 'POST') || phpgw::get_var('sendsmstocontact', 'bool', 'POST')  || phpgw::get_var('sendtorbuilding_email1', 'bool', 'POST') || phpgw::get_var('sendtorbuilding_email2', 'bool', 'POST')) && phpgw::get_var('active', 'bool', 'POST'))
						{
							$maildata = $this->create_sendt_mail_notification_comment_text($event, $errors);

							if (phpgw::get_var('sendtocollision', 'bool', 'POST'))
							{

								$subject = $config['event_conflict_mail_subject'];
								$body = "<p>" . $config['event_mail_conflict_contact_active_collision'] . "<br />\n" . phpgw::get_var('mail','html', 'POST') . "\n";
								$body .= '<br /><a href="' . $link . '">Link til ' . $config['application_mail_systemname'] . '</a></p>';
								$body .= "<p>" . $config['application_mail_signature'] . "</p>";
								$sendt = 0;
								$mail_sendt_to = [];
								foreach (array_keys($maildata) as $mail)
								{
									if ($mail == '')
									{
										continue;
									}
									usort($maildata[$mail], function($a, $b)
									{
										$adate = explode('-', $a['date']);
										$bdate = explode('-', $b['date']);
										$astart = $adate[2] . $adate[1] . $adate[0] . str_replace(':', '', $a['start']);
										$bstart = $bdate[2] . $bdate[1] . $bdate[0] . str_replace(':', '', $b['start']);
										return $astart - $bstart;
									});

									$mailbody = '';
									$comment_text_log = "Reserverasjoner som har blitt overskrevet: \n";
									$mail_sendt_to[] = $mail;
									$sms_resipients = array();
									foreach ($maildata[$mail] as $data)
									{
										$comment_text_log .= $data['date'] . ', ' . $data['building'] . ', ' . $data['resource'] . ', Kl. ' . $data['start'] . ' - ' . $data['end'] . " \n";
										if(!empty($data['phone']))
										{
											$sms_resipients[] = $data['phone'];
										}
									}
									$mailbody .= $body . "<pre>" . $comment_text_log . "</pre>";
									$sendt++;
									$this->send_mailnotification($mail, $subject, $mailbody);
									$this->send_sms_notification(array_unique($sms_resipients), $subject, $mailbody);
								}
								if ($sendt)
								{
									/**start log comment**/
									$comment_text_log = "<span style='color: green;'>" . lang('Message sent about the changes in the reservations') . ':</span><br />';
									$res = array();
									$resname = '';
									foreach ($event['resources'] as $resid)
									{
										$res = $this->bo->so->get_resource_info($resid);
										$resname .= $res['name'] . ', ';
									}
									$comment_text_log .= $event['building_name'] . " (" . substr($resname, 0, -2) . ") " . pretty_timestamp($event['from_']) . " - " . pretty_timestamp($event['to_']);
									$this->add_comment($event, $comment_text_log);
									/**End log comment**/

									/**start log comment**/

									$comment = "<p>Melding om konflikt er sendt til" . implode(', ', $mail_sendt_to) . "<br />\n" . phpgw::get_var('mail','html', 'POST') . "</p>";
									$this->add_comment($event, $comment);
									/**End log comment**/
								}
							}
							if (phpgw::get_var('sendtocontact', 'bool', 'POST'))
							{
								$subject = $config['event_change_mail_subject'];
								$body = "<p>" . $config['event_change_mail'] . "\n<br/>Melding: " . phpgw::get_var('mail','html', 'POST');
								$body .= '<br /><a href="' . $link . '">Link til ' . $config['application_mail_systemname'] . '</a></p>';
								$this->send_mailnotification($event['contact_email'], $subject, $body);
								$comment = $comment_text_log . '<br />Denne er sendt til ' . $event['contact_email'] . ':<br />';
								$comment .=  phpgw::get_var('mail','html', 'POST');
								$this->add_comment($event, $comment);
							}
							//sms
                            if (phpgw::get_var('sendsmstocontact', 'bool', 'POST'))
							{
                                $rool = phpgw::get_var('mail','html', 'POST');
                                $phone_number = phpgw::get_var('contact_phone', 'string', 'POST');
                                $text_message  = array('text' => $rool);
                                $newArray = array_map(function($v)
								{
									return trim(strip_tags($v));
                                 }, $text_message);

								$phone_number = str_replace(' ', '', $phone_number);

								if(  preg_match( '/^(\d{2})(\d{2})(\d{2})(\d{2})$/', $phone_number,  $matches ) )
								{
									$phone_number_validated = 1;
									//implement validation
								}

								if (empty($GLOBALS['phpgw_info']['apps']['sms']))
								{
									phpgwapi_cache::message_set('SMS er deaktivert', 'error');
								}
								else
								{
									$sms_res = CreateObject('sms.sms')->websend2pv($this->account, $phone_number, $newArray['text']);

									if($sms_res[0][0])
									{
										$comment = $rool . '<br />Denne er sendt til ' . $phone_number;
										$this->add_comment($event, $comment);
									}
								}
							}

							if (phpgw::get_var('sendtorbuilding', 'bool', 'POST') || phpgw::get_var('sendtorbuilding_email1', 'bool', 'POST') || phpgw::get_var('sendtorbuilding_email2', 'bool', 'POST'))
							{

								$subject = $config['event_mail_building_subject'];

								$body = "<p>" . $config['event_mail_building'] . "<br />\n" . phpgw::get_var('mail','html', 'POST') . "</p>";

								if ($event['customer_organization_name'])
								{
									$username = $event['customer_organization_name'];
								}
								else
								{
									$username = $event['contact_name'];
								}
								$res = array();
								$resname = '';
								foreach ($event['resources'] as $resid)
								{
									$res = $this->bo->so->get_resource_info($resid);
									$resname .= $res['name'] . ', ';
								}
								$resources = $event['building_name'] . " (" . substr($resname, 0, -2) . ") " . pretty_timestamp($event['from_']) . " - " . pretty_timestamp($event['to_']);

								$body .= '<p>' . $username . ' har fått innvilget et arrangement i ' . $resources . ".";
								$body .= '<br /><a href="' . $link . '">Link til ' . $config['application_mail_systemname'] . '</a></p>';
								$body .= "<p>" . $config['application_mail_signature'] . "</p>";

								$sendt = 0;
								$mail_sendt_to = [];
/*
								if ($event['contact_email'])
								{
									$sendt++;
									$mail_sendt_to[] = $event['contact_email'];
									$this->send_mailnotification($event['contact_email'], $subject, $body);
								}
*/
								if (phpgw::get_var('sendtorbuilding', 'bool', 'POST') && $building_info['email'])
								{
									$sendt++;
									$mail_sendt_to[] = $building_info['email'];
									$this->send_mailnotification($building_info['email'], $subject, $body);
								}
								if (phpgw::get_var('sendtorbuilding', 'bool', 'POST') && $building_info['tilsyn_email'])
								{
									$sendt++;
									$mail_sendt_to[] = $building_info['tilsyn_email'];
									$this->send_mailnotification($building_info['tilsyn_email'], $subject, $body);
								}
								if (phpgw::get_var('sendtorbuilding', 'bool', 'POST') && $building_info['tilsyn_email2'])
								{
									$sendt++;
									$mail_sendt_to[] = $building_info['tilsyn_email2'];
									$this->send_mailnotification($building_info['tilsyn_email2'], $subject, $body);
								}
								if (phpgw::get_var('sendtorbuilding_email1', 'bool', 'POST'))
								{
									$sendt++;
									$mail_sendt_to[] = $_POST['sendtorbuilding_email1'];
									$this->send_mailnotification($_POST['sendtorbuilding_email1'], $subject, $body);
								}
								if (phpgw::get_var('sendtorbuilding_email2', 'bool', 'POST'))
								{
									$sendt++;
									$mail_sendt_to[] = $_POST['sendtorbuilding_email2'];
									$this->send_mailnotification($_POST['sendtorbuilding_email2'], $subject, $body);
								}
								if ($sendt <= 0)
								{
									$errors['mailtobuilding'] = lang('Unable to send warning, No mailadresses found');
								}
								else
								{
									$comment_text_log = phpgw::get_var('mail','string', 'POST');
									$comment = 'Melding om endring er sendt til ansvarlig for bygg: ' . implode(', ', $mail_sendt_to) . '<br />' . $comment_text_log;
									$this->add_comment($event, $comment);
								}
							}
						}
						if (!phpgw::get_var('active', 'bool', 'POST') && phpgw::get_var('sendtorbuilding', 'bool', 'POST'))
						{

							$subject = $config['event_canceled_mail_subject'];
							$body = $config['event_canceled_mail'] . "<br />\n" . phpgw::get_var('mail','html', 'POST');

							if ($event['customer_organization_name'])
							{
								$comment_text_log = $event['customer_organization_name'];
							}
							else
							{
								$comment_text_log = $event['contact_name'];
							}
							$res = array();
							$resname = '';
							foreach ($event['resources'] as $resid)
							{
								$res = $this->bo->so->get_resource_info($resid);
								$resname .= $res['name'] . ', ';
							}
							$resources = $event['building_name'] . " (" . substr($resname, 0, -2) . ") " . pretty_timestamp($event['from_']) . " - " . pretty_timestamp($event['to_']);

//							$comment_text_log = $comment_text_log . ' sitt arrangement i ' . $event['building_name'] . ' ' . date('d-m-Y H:i', strtotime($event['from_'])) . " har blitt kansellert.";
							$comment_text_log = $comment_text_log . ' sitt arrangement har blitt kansellert:';
							$comment_text_log .= "<br />\n" . $resources;

							$body .= "<br />\n" . $comment_text_log;
							$body = html_entity_decode($body);

							$sendt = 0;
							$mail_sendt_to = [];
							if ($building_info['email'])
							{
								$sendt++;
								$mail_sendt_to[] = $building_info['email'];
								$this->send_mailnotification($building_info['email'], $subject, $body);
							}
							if ($building_info['tilsyn_email'])
							{
								$sendt++;
								$mail_sendt_to[] = $building_info['tilsyn_email'];
								$this->send_mailnotification($building_info['tilsyn_email'], $subject, $body);
							}
							if ($building_info['tilsyn_email2'])
							{
								$sendt++;
								$mail_sendt_to[] = $building_info['tilsyn_email2'];
								$this->send_mailnotification($building_info['tilsyn_email2'], $subject, $body);
							}
							if ($_POST['sendtorbuilding_email1'])
							{
								$sendt++;
								$mail_sendt_to[] = $_POST['sendtorbuilding_email1'];
								$this->send_mailnotification($_POST['sendtorbuilding_email1'], $subject, $body);
							}
							if ($_POST['sendtorbuilding_email2'])
							{
								$sendt++;
								$mail_sendt_to[] = $_POST['sendtorbuilding_email2'];
								$this->send_mailnotification($_POST['sendtorbuilding_email2'], $subject, $body);
							}
							if ($sendt <= 0)
							{
								$errors['mailtobuilding'] = lang('Unable to send warning, No mailadresses found');
							}
							else
							{
								$comment = '<span style="color:red;">Dette arrangemenet er kanselert</span>. Denne er sendt til ' . implode(', ',$mail_sendt_to) . '<br />' . phpgw::get_var('mail','string', 'POST');
								$this->add_comment($event, $comment);
							}
//						$receipt = $this->bo->update($event);
//						self::redirect(array('menuaction' => 'booking.uievent.edit', 'id'=>$event['id']));
						}
					}

					/**
					 * Tolerate overlap
					 */
					$_errors = $errors;
					unset($_errors['allocation']);
					unset($_errors['booking']);
					if(!$_errors)
					{
						/**
						 * Start dealing with the purchase_order..
						 */
						$purchase_order = array(
							'application_id' => !empty($event['application_id']) ? $event['application_id'] : -1,
							'status' => 0,
							'reservation_type' => 'event',
							'reservation_id' => $id,
							'customer_id' => -1,
							'lines' => array());

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

						if(!empty($purchase_order['lines']))
						{
							$purchase_order_id = $this->sopurchase_order->add_purchase_order($purchase_order);
							$purchase_order_result =  $this->sopurchase_order->get_single_purchase_order($purchase_order_id);
							if($purchase_order_result['sum'] && $purchase_order_result['sum'] != $event['cost'])
							{
								$this->add_cost_history($event, lang('cost is set'), $purchase_order_result['sum']);
								$event['cost'] = $purchase_order_result['sum'];
							}
						}
						/** END purchase order */

						$receipt = $this->bo->update($event);


						if(!$errors)
						{
							if(empty($event['application_id']))
							{
								self::redirect(array('menuaction' => 'booking.uievent.edit', 'id' => $event['id']));
							}
							else
							{
								self::redirect(array('menuaction' => 'booking.uiapplication.show', 'id' => $event['application_id']));
							}
						}
					}
				}
			}

			/**
			 * Translate into text
			 */
			if ($errors['allocation'])
			{
				$errors['allocation'] = lang('Overlaps with existing allocation %1. Remember to send a notification', " #" . implode(', #',$errors['allocation'][0]));
			}
			if ($errors['booking'])
			{
				$errors['booking'] = lang('Overlaps with existing booking %1. Remember to send a notification', " #" . implode(', #',$errors['booking'][0])) ;
			}
			$this->flash_form_errors($errors);
			if ($customer['customer_identifier_type'])
			{
				$event['customer_identifier_type'] = $customer['customer_identifier_type'];
				$event['customer_ssn'] = $customer['customer_ssn'];
				$event['customer_organization_number'] = $customer['customer_organization_number'];
				$event['customer_internal'] = $customer['customer_internal'];
			}

			$event['from_'] = pretty_timestamp($event['from_']);
			$event['to_'] = pretty_timestamp($event['to_']);

			$GLOBALS['phpgw']->jqcal2->add_listener('from_', 'datetime', phpgwapi_datetime::date_to_timestamp($event['from_']));
			$GLOBALS['phpgw']->jqcal2->add_listener('to_', 'datetime', phpgwapi_datetime::date_to_timestamp($event['to_']));
			phpgwapi_jquery::load_widget('datepicker');

			self::add_javascript('booking', 'base', 'event.js');

			$completed_reservations = CreateObject('booking.socompleted_reservation')->read(array(
				'filters'	 => array(
					'reservation_type'	 => 'event',
					'reservation_id'	 => $event['id'],
					'exported'			 => null),
				'results'	 => -1));

			if(!empty($config['activate_application_articles']))
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

			$event['resources_json'] = json_encode(array_map('intval', $event['resources']));
			$event['application_link'] = self::link(array('menuaction' => 'booking.uiapplication.show',
					'id' => $event['application_id']));
			$event['cancel_link'] = self::link(array('menuaction' => 'booking.uiapplication.index'));
			$event['editable'] = true;
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
#			$comments = array_reverse($event['comments']);
			$comments = $this->bo->so->get_ordered_comments($id);

			/**
			 * Start hack
			 */
			$external_site_address = !empty($config['external_site_address']) ? $config['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];
			$internal_site_address = $GLOBALS['phpgw_info']['server']['webserver_url'];

			foreach ($comments as  &$comment)
			{
				$comment['comment'] = str_replace($external_site_address, $internal_site_address, $comment['comment']);
			}
			/**
			 * End hack
			 */

			$cost_history = $this->bo->so->get_ordered_costs($id);
			$agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity, $include_inactive = true);
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience($top_level_activity);
			$audience = $audience['results'];
			$event['audience_json'] = json_encode(array_map('intval', (array)$event['audience']));

			$this->install_customer_identifier_ui($event);
			$this->add_template_helpers();

			phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security',
				'file'));

			$GLOBALS['phpgw']->js->validate_file('alertify', 'alertify.min', 'phpgwapi');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/alertify.min.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/themes/bootstrap.min.css');

			$event['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
//              echo '<pre>'; print_r($event);echo '</pre>';
			self::render_template_xsl('event_edit', array(
				'event'			 => $event,
				'activities'	 => $activities,
				'agegroups'		 => $agegroups,
				'audience'		 => $audience,
				'comments'		 => $comments,
				'cost_history'	 => $cost_history,
				'config'		 => $config,
				'tax_code_list'	 => json_encode(execMethod('booking.bogeneric.read', array('location_info' => array('type' => 'tax', 'order' => 'id')))),
			));
		}

		public function delete()
		{
			$event_id = phpgw::get_var('id', 'int');
			$application_id = phpgw::get_var('application_id', 'int');

			if ($GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'booking'))
			{
				$this->bo->so->delete_event($event_id);
			}
			else
			{
				phpgwapi_cache::message_set('Mangler rettighet for å slette', 'error');
			}
			if (isset($application_id))
			{
				self::redirect(array('menuaction' => 'booking.uiapplication.show', 'id' => $application_id));
			}
			else
			{
				self::redirect(array('menuaction' => 'booking.uievent.index'));
			}
		}

		public function info()
		{
			$event = $this->bo->read_single(phpgw::get_var('id', 'int'));
			$resources = $this->resource_bo->so->read(array('filters' => array('id' => $event['resources']),
				'sort' => 'name'));
			$event['resources'] = $resources['results'];
			$res_names = array();
			foreach ($event['resources'] as $res)
			{
				$res_names[] = $res['name'];
			}
			$event['resource'] = phpgw::get_var('resource');
			$event['resource_info'] = join(', ', $res_names);
			$event['application_link'] = self::link(array('menuaction' => 'booking.uiapplication.show',
					'id' => $event['application_id']));
			$event['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show',
					'id' => $event['resources'][0]['buildings'][0]));
			$event['org_link'] = self::link(array('menuaction' => 'booking.uiorganization.show',
					'id' => $event['organization_id']));
			$event['add_link'] = self::link(array('menuaction' => 'booking.uibooking.add',
					'allocation_id' => $event['id'], 'from_' => $event['from_'], 'to_' => $event['to_'],
					'resource' => $event['resource']));
			$event['when'] = pretty_timestamp($event['from_']) . ' - ' . pretty_timestamp($event['to_']);

			$event['edit_link'] = self::link(array('menuaction' => 'booking.uievent.edit',
					'id' => $event['id']));

			self::render_template_xsl('event_info', array('event' => $event));
			$GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
		}
	}