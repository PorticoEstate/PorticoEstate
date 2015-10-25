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
			$errors	 = array();
			$report	 = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				$activity_id = phpgw::get_var('activity_id', 'int');
				$soactivity = createObject('booking.soactivity');
				$children = $soactivity->get_children($activity_id);
				$activity_ids = array_merge(array($activity_id),$children);
				$report['activity_ids'] = $activity_ids;

				$report['active']	 = '1';
				$report['building_id']			= phpgw::get_var('building_id', 'int');
				$report['building_name']		= phpgw::get_var('building_name', 'string', 'POST');
				$report['activity_id']			= phpgw::get_var('activity_id', 'int');
				$report['description']			= phpgw::get_var('description');
				$report['resources']			= phpgw::get_var('resources');
				$report['weekdays']				= phpgw::get_var('weekdays');

				$report['start_date']			= phpgw::get_var('start_date');
				$report['end_date']				= phpgw::get_var('end_date');
				$report['start_hour']			= phpgw::get_var('start_hour', 'int');
				$report['start_minute']			= phpgw::get_var('start_minute', 'int');
				$report['end_hour']				= phpgw::get_var('end_hour', 'int');
				$report['end_minute']			= phpgw::get_var('end_minute', 'int');
				$report['variable_horizontal']	= phpgw::get_var('variable_horizontal');
				$report['variable_vertical']	= phpgw::get_var('variable_vertical');
				$report['all_buildings']		= phpgw::get_var('all_buildings', 'bool');
	//			_debug_array($report);

				$report_type = phpgw::get_var('report_type');
				switch($report_type)
				{
					case 'participants_per_resource':
						$this->get_participants_per_resource($report);
						break;
					case 'freetime':
						$this->get_freetime($report);
						break;

					default:
						break;
				}

			}
			if($errors['report'])
			{
				$errors['warning'] = lang('NB! No data will be saved, if you navigate away you will loose all.');
			}
			$this->flash_form_errors($errors);

			self::add_javascript('booking', 'booking', 'report.js');
			array_set_default($report, 'resources', array());
			$report['resources_json'] = json_encode(array_map('intval', $report['resources']));
			$report['cancel_link']	 = self::link(array('menuaction' => 'booking.uireports.index'));
			array_set_default($report, 'cost', '0');
			$activities				 = $this->activity_bo->get_top_level();
			$report['days'] = array(
				array('id' => 1, 'name' => lang('Monday')),
				array('id' => 2, 'name' => lang('Tuesday')),
				array('id' => 3, 'name' => lang('Wednesday')),
				array('id' => 4, 'name' => lang('Thursday')),
				array('id' => 5, 'name' => lang('Friday')),
				array('id' => 6, 'name' => lang('Saturday')),
				array('id' => 7, 'name' => lang('Sunday'))
			);
			$report['variables_horizontal'] = array(
				array('id' => 'agegroup', 'name' => lang('agegroup'), 'selected' => 1),
//				array('id' => 'resource', 'name' => lang('resource')),
//				array('id' => 'audience', 'name' => lang('audience')),
//				array('id' => 'activity', 'name' => lang('activities')),
			);
			$report['variables_vertical'] = array(
//				array('id' => 'agegroup', 'name' => lang('agegroup')),
				array('id' => 'resource', 'name' => lang('resource')),
				array('id' => 'audience', 'name' => lang('audience')),
				array('id' => 'activity', 'name' => lang('activities')),
			);

			foreach($report['variables_vertical'] as &$entry)
			{
				$entry['selected'] = $entry['id'] == $report['variable_vertical'] ? 1 : 0;
			}


			$GLOBALS['phpgw']->jqcal->add_listener('start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('end_date');

			$tabs			 = array();
			$tabs['generic'] = array('label' => lang('Report New'), 'link' => '#report_new');
			$active_tab		 = 'generic';

			$report['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			self::adddatetimepicker();
			phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security', 'file'), 'report_form');

			$this->add_template_helpers();
			self::render_template_xsl('report_new', array('report' => $report, 'activities' => $activities,
				'agegroups' => $agegroups, 'audience' => $audience));
		}

		public function get_participants_per_resource($data)
		{

			$output_type = 'PDF';//'XLS';
			$db = & $GLOBALS['phpgw']->db;

			$resources = array();
			if($data['all_buildings'])
			{
				$sql = "SELECT DISTINCT bb_resource.id FROM bb_building"
				. " JOIN bb_resource ON bb_resource.building_id = bb_building.id"
				. " WHERE bb_building.active = 1"
				. " AND activity_id IN (" .implode(',', $data['activity_ids']) . ')';
				$db->query($sql);
				while ($db->next_record())
				{
					$resources[] = $db->f('id');
				}

			}
			else
			{
				$resources = $data['resources'];
			}

			$errors		 = array();
			$from_		 = date($db->date_format(),phpgwapi_datetime::date_to_timestamp($data['start_date']));
			$to_		 = date($db->date_format(),phpgwapi_datetime::date_to_timestamp($data['end_date']));

			switch($data['variable_vertical'])
			{
				case 'resource':
					$jasper_parameters = sprintf("'BK_DATE_FROM|%s;BK_DATE_TO|%s;BK_RESOURCES|%s;BK_WEEKDAYS|%s'", $from_, $to_, implode(",", $resources), implode(',' ,$data['weekdays']));
					$report_source	 = PHPGW_SERVER_ROOT . '/booking/jasper/templates/participants_per_resource.jrxml';
					break;
				case 'audience':
					$jasper_parameters = sprintf("'BK_DATE_FROM|%s;BK_DATE_TO|%s;BK_RESOURCES|%s;BK_WEEKDAYS|%s'", $from_, $to_, implode(",", $resources), implode(',' ,$data['weekdays']));
					$report_source	 = PHPGW_SERVER_ROOT . '/booking/jasper/templates/participants_per_audience.jrxml';
					break;
				case 'activity':
					$jasper_parameters = sprintf("'BK_DATE_FROM|%s;BK_DATE_TO|%s;BK_RESOURCES|%s;BK_WEEKDAYS|%s'", $from_, $to_, implode(",", $resources), implode(',' ,$data['weekdays']));
					$report_source	 = PHPGW_SERVER_ROOT . '/booking/jasper/templates/participants_per_activity.jrxml';
					break;

				default:
					$errors[] = 'Valid variable not selected';

					break;
			}


			if(!count($errors))
			{
				$jasper_wrapper	 = CreateObject('phpgwapi.jasper_wrapper');
				try
				{
					$jasper_wrapper->execute($jasper_parameters, $output_type, $report_source);
				}
				catch(Exception $e)
				{
					$errors[] = $e->getMessage();
				}
			}

			foreach($errors as $error)
			{
				phpgwapi_cache::message_set($error, 'error');
			}
		}
		/**
		 * Testing
		 * @param type $data
		 * @return string
		 */
		private function get_freetime($data)
		{
			$db = & $GLOBALS['phpgw']->db;

			$buildings = array();
			if($data['all_buildings'])
			{
				$sql = "SELECT id FROM bb_building WHERE active = 1";
				$db->query($sql);
				while ($db->next_record())
				{
					$buildings[] = $db->f('id');
				}
			}
			else
			{
				$buildings[] = $data['building_id'];
			}

			$buildings		= implode(',' ,$buildings);
			$weekdays		= implode(',' ,$data['weekdays']);
			$activity_ids	= implode(',' ,$data['activity_ids']);

			$from		 = $db->to_timestamp(phpgwapi_datetime::date_to_timestamp($data['start_date']));
			$to			 = $db->to_timestamp(phpgwapi_datetime::date_to_timestamp($data['end_date']) + 24 * 3600 -1);


			$sql = "select distinct al.id, al.from_, al.to_, EXTRACT(DOW FROM al.to_) as day_of_week, bu.id as building_id, bu.name as building_name, br.id as resource_id, br.name as resource_name
				from bb_allocation al
				inner join bb_allocation_resource ar on ar.allocation_id = al.id
				inner join bb_resource br on br.id = ar.resource_id and br.active = 1
				inner join bb_building bu on bu.id = br.building_id
				left join bb_booking bb on bb.allocation_id = al.id
				WHERE bb.id is null
				AND al.from_ >= '{$from}'
				AND al.to_ <= '{$to}'"
				. " AND br.activity_id IN ({$activity_ids}) ";

			if($buildings)
			{
				$sql .= "and building_id in (" . $buildings . ") ";
			}

			if($weekdays)
			{
				$sql .= "and EXTRACT(DOW FROM al.from_) in ({$weekdays}) ";
			}

			$sql .= "order by building_name, from_, to_, resource_name";
			$db->query($sql);

			$result = $db->resultSet;

			$retval					 = array();
			$retval['total_records'] = count($result);
			$retval['results']		 = $result;
			$retval['start']		 = 0;
			$retval['sort']			 = null;
			$retval['dir']			 = 'asc';
	_debug_array($sql);
	_debug_array($retval);
			die();
			return $retval;
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