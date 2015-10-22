<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('phpgwapi.send');
	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');

	class booking_uireports extends booking_uicommon
	{

		public $public_functions = array(
			'index'			 => true,
			'query'			 => true,
			'participants'	 => true,
			'freetime'		 => true,
			'add'			 => true,
			'get_custom'	 => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->building_bo = CreateObject('booking.bobuilding');
			self::set_active_menu('booking::reportcenter');

			$this->activity_bo = CreateObject('booking.boactivity');
			$this->agegroup_bo = CreateObject('booking.boagegroup');
			$this->audience_bo = CreateObject('booking.boaudience');
			//remove
			$this->organization_bo = CreateObject('booking.boorganization');
			$this->resource_bo = CreateObject('booking.boresource');

		}

		public function query()
		{

		}

		public function index()
		{
			$reports[]	 = array('name' => lang('Participants Per Age Group Per Month'), 'url' => self::link(array(
					'menuaction' => 'booking.uireports.participants')));
			$reports[]	 = array('name' => lang('Free time'), 'url' => self::link(array('menuaction' => 'booking.uireports.freetime')));
                        
                        $tabs = array();
			$tabs['generic'] = array('label' => lang('Reports'), 'link' => '#reports');
			$active_tab = 'generic';
                        $tabs = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			
                        self::render_template_xsl('report_index', array('reports' => $reports, 'tabs' => $tabs));
		}

		public function add()
		{
			self::set_active_menu('booking::reportcenter::add_generic');
			_debug_array($_POST);
			$errors	 = array();
			$report	 = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{

				array_set_default($_POST, 'from_', array());
				array_set_default($_POST, 'to_', array());
				$report['dates'] = array_map(array(self, '_combine_dates'), $_POST['from_'], $_POST['to_']);

				array_set_default($_POST, 'resources', array());
				$report['active']	 = '1';
				$report['completed']	 = '0';

				array_set_default($report, 'audience', array());
				array_set_default($report, 'agegroups', array());
				$report['secret']		 = $this->generate_secret();
				$report['is_public']		 = 1;
				$report['building_name']	 = $_POST['building_name'];

				if(!$_POST['application_id'])
				{
					$temp_errors = array();
					foreach($report['dates'] as $checkdate)
					{
						$report['from_']	 = $checkdate['from_'];
						$_POST['from_']	 = $checkdate['from_'];
						$report['to_']	 = $checkdate['to_'];
						$_POST['to_']	 = $checkdate['to_'];
						list($report, $errors) = $this->extract_and_validate($report);
						$time_from		 = explode(" ", $_POST['from_']);
						$time_to		 = explode(" ", $_POST['to_']);
						if($time_from[0] == $time_to[0])
						{
							if($time_from[1] >= $time_to[1])
							{
								$errors['time'] = lang('Time is set wrong');
							}
						}
						if($errors != array())
						{
							$temp_errors = $errors;
						}
					}
					$errors = $temp_errors;
				}
				else
				{
					list($report, $errors) = $this->extract_and_validate($report);
					$time_from	 = explode(" ", $_POST['from_']);
					$time_to	 = explode(" ", $_POST['to_']);
					if($time_from[0] == $time_to[0])
					{
						if($time_from[1] >= $time_to[1])
						{
							$errors['time'] = lang('Time is set wrong');
						}
					}
				}

				if($_POST['cost'] != 0 and ! $report['customer_organization_number'] and ! $report['customer_ssn'])
				{
					$errors['invoice_data'] = lang('There is set a cost, but no invoice data is filled inn');
				}
				if(($_POST['organization_name'] != '' or $_POST['org_id2'] != '') and isset($errors['contact_name']))
				{
					$errors['contact_name'] = lang('Organization is missing booking charge');
				}
				if(!$errors['report'] && !$errors['from_'] && !$errors['time'] && !$errors['invoice_data'] && !$errors['resource_number'] && !$errors['organization_number'] && !$errors['contact_name'] && !$errors['cost'] && !$errors['activity_id'])
				{
					if(!$_POST['application_id'])
					{
						$allids = array();
						foreach($report['dates'] as $checkdate)
						{
							$report['from_']	 = $checkdate['from_'];
							$report['to_']	 = $checkdate['to_'];

							unset($report['comments']);
							if(count($report['dates']) < 2)
							{
								$this->add_comment($report, lang('Event was created'));
								$receipt = $this->bo->add($report);
							}
							else
							{
								$this->add_comment($report, lang('Multiple Events was created'));
								$receipt	 = $this->bo->add($report);
								$allids[]	 = array($receipt['id']);
							}
						}
						if($allids)
						{
							$this->bo->so->update_comment($allids);
							$this->bo->so->update_id_string();
						}
					}
					else
					{
						$this->add_comment($report, lang('Event was created'));
						$receipt = $this->bo->add($report);
						$this->bo->so->update_id_string();
					}
	//				$this->redirect(array('menuaction' => 'booking.uireports.edit', 'id' => $receipt['id'],
	//					'secret' => $report['secret'], 'warnings' => $errors));
				}
			}
			if($errors['report'])
			{
				$errors['warning'] = lang('NB! No data will be saved, if you navigate away you will loose all.');
			}
			$default_dates = array_map(array(self, '_combine_dates'), '', '');
			array_set_default($report, 'dates', $default_dates);

			if(!phpgw::get_var('from_report', 'POST'))
			{
				$this->flash_form_errors($errors);
			}

			self::add_javascript('booking', 'booking', 'report.js');
			array_set_default($report, 'resources', array());
			$report['resources_json'] = json_encode(array_map('intval', $report['resources']));
			$report['cancel_link']	 = self::link(array('menuaction' => 'booking.uireports.index'));
			array_set_default($report, 'cost', '0');
			$activities				 = $this->activity_bo->get_top_level();
//			$agegroups				 = $this->agegroup_bo->fetch_age_groups();
//			$agegroups				 = $agegroups['results'];
//			$audience				 = $this->audience_bo->fetch_target_audience();
//			$audience				 = $audience['results'];
			$report['days'] = array(
				array('id' => 1, 'name' => lang('Monday')),
				array('id' => 2, 'name' => lang('Tuesday')),
				array('id' => 3, 'name' => lang('Wednesday')),
				array('id' => 4, 'name' => lang('Thursday')),
				array('id' => 5, 'name' => lang('Friday')),
				array('id' => 6, 'name' => lang('Saturday')),
				array('id' => 7, 'name' => lang('Sunday'))
			);
			$report['variables'] = array(
				array('id' => 'resource', 'name' => lang('resource')),
				array('id' => 'audience', 'name' => lang('audience')),
				array('id' => 'agegroup', 'name' => lang('agegroup')),
				array('id' => 'activity', 'name' => lang('activities')),
			);


			$GLOBALS['phpgw']->jqcal->add_listener('start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('end_date');

			$tabs			 = array();
			$tabs['generic'] = array('label' => lang('Report New'), 'link' => '#report_new');
			$active_tab		 = 'generic';

			$report['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			self::adddatetimepicker();

			$this->add_template_helpers();
			self::render_template_xsl('report_new', array('report' => $report, 'activities' => $activities,
				'agegroups' => $agegroups, 'audience' => $audience));
		}

		public function get_custom()
		{
			$activity_id = phpgw::get_var('activity_id', 'int');
			$activity_path	 = $this->activity_bo->get_path($activity_id);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : 0;


			$location = ".application.{$top_level_activity}";
			$organized_fields = $this->get_attributes($location);
			$variable_vertical = '';
			$variable_horizontal = '';
			foreach($organized_fields as $group)
			{
				if($group[id] > 0)
				{
					$header_level = $group['level'] + 2;
					if(isset($group['attributes']) && is_array($group['attributes']))
					{
						foreach ($group['attributes'] as $attribute)
						{
							$variable_vertical .= <<<HTML

								<li><input type = "radio" name = "variable_vertical" value ="{$attribute['id']}" ></input>
								{$attribute['input_text']} [{$attribute['trans_datatype']}] </li>
HTML;
							$variable_horizontal .= <<<HTML

								<li><input type = "radio" name = "variable_horizontal" value ="{$attribute['id']}" ></input>
								{$attribute['input_text']} [{$attribute['trans_datatype']}] </li>
HTML;
						}
					}
				}
			}

			$fields = print_r($organized_fields, true);


	//		$path = print_r($activity_path, true);

		
			return array
			(
				'status'	=> 'ok',
				'message'	=> 'melding',
				'variable_vertical'	=> $variable_vertical,
				'variable_horizontal'	=> $variable_horizontal
			);
		}
		/**
		 *
		 * @param type $location
		 * @return  array the grouped attributes
		 */
		private function get_attributes($location)
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

		private function get_attribute_groups($appname, $location, $attributes = array())
		{
			return $GLOBALS['phpgw']->custom_fields->get_attribute_groups($appname, $location, $attributes);
		}


		public function participants()
		{
			self::set_active_menu('booking::reportcenter::participants');
			$errors		 = array();
			$buildings	 = $this->building_bo->read();

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$to		 = phpgw::get_var('to', 'POST');
				$from	 = phpgw::get_var('from', 'POST');

				$to_	 = date("Y-m-d", phpgwapi_datetime::date_to_timestamp($to));
				$from_	 = date("Y-m-d", phpgwapi_datetime::date_to_timestamp($from));
				
				$output_type	 = phpgw::get_var('otype', 'POST');
				$building_list	 = phpgw::get_var('building', 'POST');

				if(!count($building_list))
				{
					$errors[] = lang('No buildings selected');
				}

				if(!count($errors))
				{
					$jasper_parameters = sprintf("\"BK_DATE_FROM|%s;BK_DATE_TO|%s;BK_BUILDINGS|%s\"", $from_, $to_, implode(",", $building_list));
					// DEBUG
					//print_r($jasper_parameters);
					//exit(0);

					$jasper_wrapper	 = CreateObject('phpgwapi.jasper_wrapper');
					$report_source	 = PHPGW_SERVER_ROOT . '/booking/jasper/templates/participants.jrxml';
					try
					{
						$jasper_wrapper->execute($jasper_parameters, $output_type, $report_source);
					}
					catch(Exception $e)
					{
						$errors[] = $e->getMessage();
					}
				}
			}
			else
			{
				$to		 = date($this->dateFormat, time());
				$from	 = date($this->dateFormat, time());
			}
			
			phpgwapi_cache::message_set($errors, 'error'); 
			//$this->flash_form_errors($errors);

			$GLOBALS['phpgw']->jqcal->add_listener('from', 'date');
			$GLOBALS['phpgw']->jqcal->add_listener('to', 'date');

			$tabs			 = array();
			$tabs['generic'] = array('label' => lang('Report Participants'), 'link' => '#report_part');
			$active_tab		 = 'generic';

			$data			 = array();
			$data['tabs']	 = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
                        $data['validator'] = phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security', 'file'));

			self::render_template_xsl('report_participants', array('data' => $data, 'from' => $from,
				'to' => $to, 'buildings' => $buildings['results']));
		}

		public function freetime()
		{
			self::set_active_menu('booking::reportcenter::free_time');
			$errors		 = array();
			$buildings	 = $this->building_bo->read();

			$show = '';
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$to		 = phpgw::get_var('to', 'POST');
				$from	 = phpgw::get_var('from', 'POST');

				$to_	 = date("Y-m-d", phpgwapi_datetime::date_to_timestamp($to));
				$from_	 = date("Y-m-d", phpgwapi_datetime::date_to_timestamp($from));
				
				$show		 = 'report';
				$allocations = $this->get_free_allocations(
				phpgw::get_var('building', 'POST'), $from_, $to_, phpgw::get_var('weekdays', 'POST')
				);

				$counter = 0;
				foreach($allocations['results'] as &$allocation)
				{
					$temp						 = array();
					$temp[]						 = array('from_', $allocation['from_']);
					$temp[]						 = array('to_', $allocation['to_']);
					$temp[]						 = array('building_id', $allocation['building_id']);
					$temp[]						 = array('building_name', $allocation['building_name']);
					$temp[]						 = array('resources[]', array($allocation['resource_id']));
					$temp[]						 = array('reminder', 0);
					$temp[]						 = array('from_report', true); // indicate that no error messages should be shown
					$allocation['counter']		 = $counter;
					$allocation['event_params']	 = json_encode($temp);
					$counter++;
				}
				if(count($allocations['results']) == 0)
				{
					$show		 = 'gui';
					$errors[]	 = lang('no records found.');
				}
				$allocations['cancel_link'] = self::link(array('menuaction' => 'booking.uireports.freetime'));
			}
			else
			{
				$to		 = date($this->dateFormat, time());
				$from	 = date($this->dateFormat, time());
				$show	 = 'gui';
			}
			
			phpgwapi_cache::message_set($errors, 'error'); 
			//$this->flash_form_errors($errors);

			$GLOBALS['phpgw']->jqcal->add_listener('from', 'date');
			$GLOBALS['phpgw']->jqcal->add_listener('to', 'date');

			$tabs			 = array();
			$tabs['generic'] = array('label' => lang('Report FreeTime'), 'link' => '#report_freetime');
			$active_tab		 = 'generic';

			$data			 = array();
			$data['tabs']	 = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			self::render_template_xsl('report_freetime', array('data' => $data, 'show' => $show,
				'from' => $from, 'to' => $to, 'buildings' => $buildings['results'], 'allocations' => $allocations['results']));
		}

		private function get_free_allocations($buildings, $from, $to, $weekdays)
		{
			$db = & $GLOBALS['phpgw']->db;

			$buildings	 = implode(",", $buildings);
			$weekdays	 = implode(",", $weekdays);

			$sql = "select distinct al.id, al.from_, al.to_, EXTRACT(DOW FROM al.to_) as day_of_week, bu.id as building_id, bu.name as building_name, br.id as resource_id, br.name as resource_name
				from bb_allocation al
				inner join bb_allocation_resource ar on ar.allocation_id = al.id
				inner join bb_resource br on br.id = ar.resource_id and br.active = 1
				inner join bb_building bu on bu.id = br.building_id
				left join bb_booking bb on bb.allocation_id = al.id
				where bb.id is null 
				and al.from_ >= '" . $from . " 00:00:00'
				and al.to_ <= '" . $to . " 23:59:59' ";

			if($buildings)
				$sql .= "and building_id in (" . $buildings . ") ";

			if($weekdays)
				$sql .= "and EXTRACT(DOW FROM al.from_) in (" . $weekdays . ") ";

			$sql .= "order by building_name, from_, to_, resource_name";
			$db->query($sql);

			$result = $db->resultSet;

			$retval					 = array();
			$retval['total_records'] = count($result);
			$retval['results']		 = $result;
			$retval['start']		 = 0;
			$retval['sort']			 = null;
			$retval['dir']			 = 'asc';

			return $retval;
		}
	}