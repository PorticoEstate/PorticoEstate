<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('phpgwapi.send');
	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');

	class booking_uireports extends booking_uicommon
	{

		public $public_functions = array(
			'index' => true,
			'query' => true,
			'participants' => true,
			'freetime' => true,
			'add' => true,
			'get_custom' => true
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
			$reports[] = array('name' => lang('Participants Per Age Group Per Month'), 'url' => self::link(array(
					'menuaction' => 'booking.uireports.participants')));
			$reports[] = array('name' => lang('Free time'), 'url' => self::link(array('menuaction' => 'booking.uireports.freetime')));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Reports'), 'link' => '#reports');
			$active_tab = 'generic';
			$tabs = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			self::render_template_xsl('report_index', array('reports' => $reports, 'tabs' => $tabs));
		}

		public function add()
		{
			self::set_active_menu('booking::reportcenter::add_generic');
			$errors = array();
			$report = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				$activity_id = phpgw::get_var('activity_id', 'int');
				$soactivity = createObject('booking.soactivity');
				$children = $soactivity->get_children($activity_id);
				$activity_ids = array_merge(array($activity_id), $children);
				$report['activity_ids'] = $activity_ids;

				$report['active'] = '1';
				$report['building_id'] = phpgw::get_var('building_id', 'int');
				$report['building_name'] = phpgw::get_var('building_name', 'string', 'POST');
				$report['activity_id'] = phpgw::get_var('activity_id', 'int');
				$report['description'] = phpgw::get_var('description');
				$report['resources'] = phpgw::get_var('resources');
				$report['weekdays'] = phpgw::get_var('weekdays');

				$report['start_date'] = phpgw::get_var('start_date');
				$report['end_date'] = phpgw::get_var('end_date');
				$report['start_hour'] = phpgw::get_var('start_hour', 'int');
				$report['start_minute'] = phpgw::get_var('start_minute', 'int');
				$report['end_hour'] = phpgw::get_var('end_hour', 'int');
				$report['end_minute'] = phpgw::get_var('end_minute', 'int');
				$report['variable_horizontal'] = phpgw::get_var('variable_horizontal');
				$report['variable_vertical'] = phpgw::get_var('variable_vertical');
				$report['all_buildings'] = phpgw::get_var('all_buildings', 'bool');
				//			_debug_array($report);
				$from_ = phpgw::get_var('start_date', 'date');
				$to_ = phpgw::get_var('end_date', 'date');

				if ($report['all_buildings'] && (($to_ - $from_) > 24 * 3600 * 31))
				{
					$errors[] = lang('Maximum 1 month for all buildings');
				}

				$report_type = phpgw::get_var('report_type');

				if (!$errors)
				{
					switch ($report_type)
					{
						case 'participants_per_agegroupe':
							$this->get_participants_per_agegroupe($report);
							break;
						case 'cover_ratio':
							$this->get_cover_ratio($report);
							break;
						case 'freetime':
							$this->get_freetime($report);
							break;

						default:
							break;
					}
				}
			}
			if ($errors)
			{
				$errors[] = lang('NB! No data will be saved, if you navigate away you will loose all.');
			}


			foreach ($errors as $error)
			{
				phpgwapi_cache::message_set($error, 'error');
			}

			self::add_javascript('booking', 'base', 'report.js');
			array_set_default($report, 'resources', array());
			$report['resources_json'] = json_encode(array_map('intval', $report['resources']));
			$report['cancel_link'] = self::link(array('menuaction' => 'booking.uireports.index'));
			array_set_default($report, 'cost', '0');
			$activities = $this->activity_bo->get_top_level();
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
			$report_types = array(
				array(
					'id' => 'participants_per_agegroupe',
					'name' => lang('participants_per_agegroupe'),
					'selected' => $report_type == 'participants_per_agegroupe' ? 1 : 0
				),
				array(
					'id' => 'cover_ratio',
					'name' => lang('cover ratio'),
					'selected' => $report_type == 'cover_ratio' ? 1 : 0
				),
			);

			foreach ($report['variables_vertical'] as &$entry)
			{
				$entry['selected'] = $entry['id'] == $report['variable_vertical'] ? 1 : 0;
			}

			$GLOBALS['phpgw']->jqcal->add_listener('start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('end_date');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Report New'), 'link' => '#report_new');
			$active_tab = 'generic';

			$report['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
//			self::adddatetimepicker();
			phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security',
				'file'), 'report_form');

			$this->add_template_helpers();
			self::render_template_xsl('report_new', array('report' => $report, 'activities' => $activities,
				'agegroups' => $agegroups, 'audience' => $audience, 'report_types' => $report_types));
		}

		/**
		 *
		 * @param type $data
		 */
		public function get_cover_ratio( $data )
		{
			$db = & $GLOBALS['phpgw']->db;

			$resources = array();
			if ($data['all_buildings'])
			{
				$sql = "SELECT DISTINCT bb_building_resource.resource_id FROM bb_building"
					. " JOIN bb_building_resource ON bb_building_resource.building_id = bb_building.id"
					. " JOIN bb_resource ON bb_building_resource.resource_id = bb_resource.id"
					. " WHERE bb_building.active = 1"
					. " AND bb_resource.active = 1"
					. " AND bb_building.activity_id IN (" . implode(',', $data['activity_ids']) . ')';
				$db->query($sql);
				while ($db->next_record())
				{
					$resources[] = $db->f('resource_id');
				}
			}
			else
			{
				$resources = $data['resources'];
			}

			$db_dateformat = $db->date_format();
			$errors = array();
			$from_ = date($db_dateformat, phpgwapi_datetime::date_to_timestamp($data['start_date']));
			$to_ = date($db_dateformat, phpgwapi_datetime::date_to_timestamp($data['end_date']));

			/*
			 * Get availlable time
			 */
			$begin = new DateTime(date('Y-m-d', phpgwapi_datetime::date_to_timestamp($data['start_date'])));
			$end = new DateTime(date('Y-m-d', phpgwapi_datetime::date_to_timestamp($data['end_date'])));
			$end = $end->modify('+1 day');

			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end);

			$resources_string = $resources ? implode(",", $resources) : -1;
			$weekdays_string = $data['weekdays'] ? implode(",", $data['weekdays']) : -1;
			$time_in = mktime((int)$data['start_hour'], (int)$data['start_minute']);
			$time_out = mktime((int)$data['end_hour'], (int)$data['end_minute']);
			$candidates = array();
			foreach ($period as $dt)
			{

				$check_date = $dt->format($db_dateformat);
				$weekday = $dt->format('N');
				$sql = "SELECT sb.wday AS wday, sb.from_ as boundary_from, sb.to_ as boundary_to,bu.id as building_id,"
					. " bu.name as building_name, re.id AS resource_id, re.name AS resource_name, EXTRACT(EPOCH FROM (sb.to_ - sb.from_)) as timespan"
					. " FROM bb_building bu, bb_season se, bb_season_boundary sb, bb_resource re, bb_season_resource sr, bb_building_resource br"
					. " WHERE bu.id = se.building_id"
					. " AND re.id = br.resource_id"
					. " AND bu.id = br.building_id"
					. " AND sr.season_id = se.id"
					. " AND sr.resource_id = re.id"
					. " AND sb.season_id = se.id"
					. " AND bu.active = 1"
					. " AND sb.wday = {$weekday}"
					. " AND date_trunc('day' ,se.to_) >= to_date('{$check_date}' ,'YYYY-MM-DD')"
					. " AND date_trunc('day' ,se.from_) <= to_date('{$check_date}', 'YYYY-MM-DD')"
					. " AND re.id = ANY (string_to_array('{$resources_string}', ',')::int4[])"
					. " AND sb.wday = ANY (string_to_array('{$weekdays_string}', ',')::int4[])";

				$db->query($sql);
				while ($db->next_record())
				{
					$candidates[$check_date][] = array(
						'date' => $check_date,
						'wday' => $db->f('wday'),
						'timespan' => $db->f('timespan'),
						'boundary_from' => $db->f('boundary_from'),
						'boundary_to' => $db->f('boundary_to'),
						'building_id' => $db->f('building_id'),
						'building_name' => $db->f('building_name'),
						'resource_id' => $db->f('resource_id'),
						'resource_name' => $db->f('resource_name')
					);
				}

//_debug_array($sql);
			}

			unset($check_date);
			$ret = array();
			foreach ($candidates as $check_date => &$data_set)
			{
				foreach ($data_set as &$entry)
				{

					$sql = "SELECT bu.id as building_id, bu.name, re.id AS resource_id, re.name AS resource_name, EXTRACT(EPOCH FROM (bo.to_ - bo.from_)) as timespan,
						EXTRACT(EPOCH FROM (bo.from_)) as from_, EXTRACT(EPOCH FROM (bo.to_)) as to_
					FROM bb_agegroup ag, bb_booking_agegroup ba, bb_booking bo, bb_allocation al, bb_season se, bb_building bu, bb_booking_resource br, bb_resource re
					WHERE ba.agegroup_id = ag.id
					AND ba.booking_id = bo.id
					AND br.booking_id = bo.id
					AND br.resource_id = re.id
					AND bo.allocation_id = al.id
					AND al.season_id = se.id
					AND se.building_id = bu.id
					AND ag.active = 1
					AND date_trunc('day' ,bo.from_) >= to_date('{$check_date}' ,'YYYY-MM-DD')
					AND date_trunc('day' ,bo.from_) <= to_date('$check_date', 'YYYY-MM-DD')
					AND re.id = {$entry['resource_id']}
					AND EXTRACT(DOW FROM bo.from_) = {$entry['wday']}
					AND (ba.male > 0 OR ba.female > 0)
					UNION
					SELECT bu.id as building_id, bu.name, re.id AS resource_id, re.name AS resource_name, EXTRACT(EPOCH FROM (ev.to_ - ev.from_)) as timespan,
						EXTRACT(EPOCH FROM (ev.from_)) as from_, EXTRACT(EPOCH FROM (ev.to_)) as to_
					FROM bb_event ev
					INNER JOIN bb_event_agegroup ea ON ea.event_id = ev.id
					INNER JOIN bb_agegroup ag ON ag.id = ea.agegroup_id and ag.active = 1
					INNER JOIN bb_event_resource er ON er.event_id = ev.id
					INNER JOIN bb_resource re ON re.id = er.resource_id
					INNER JOIN bb_building_resource bre ON re.id = bre.resource_id
					INNER JOIN bb_building bu ON bu.id = bre.building_id
					WHERE date_trunc('day' ,ev.from_) >= to_date('{$check_date}' ,'YYYY-MM-DD')
					AND date_trunc('day' ,ev.from_) <= to_date('{$check_date}', 'YYYY-MM-DD')
					AND EXTRACT(DOW FROM ev.from_) = {$entry['wday']}
					AND er.resource_id = {$entry['resource_id']}
					AND (ea.male > 0 OR ea.female > 0)
					ORDER BY resource_name ASC, timespan";
//_debug_array($sql);
					$timespan = 0;
					$db->query($sql);

					if ($data['start_hour'] && $data['end_hour'])
					{
						$Overlap = new OverlapCalculator($time_in, $time_out);
						$entry['boundary_from'] = date('H:i', $time_in);
						$entry['boundary_to'] = date('H:i', $time_out);
					}

					while ($db->next_record())
					{
						if ($data['start_hour'] && $data['end_hour'])
						{
							$from_ = $db->f('from_');
							$to_ = $db->f('to_');

							$periodStart = mktime(date('H', $from_), date('i', $from_));
							$periodEnd = mktime(date('H', $to_), date('i', $to_));
							$Overlap->addOverlapFrom($periodStart, $periodEnd);
						}
						else
						{
							$timespan += $db->f('timespan');
						}
					}

					if ($data['start_hour'] && $data['end_hour'])
					{
						$timespan = $Overlap->getOverlap();
						$entry['cover_ratio'] = round((($timespan / ($time_out - $time_in)) * 100), 2, PHP_ROUND_HALF_UP);
					}
					else
					{
						$entry['cover_ratio'] = round((($timespan / $entry['timespan']) * 100), 2, PHP_ROUND_HALF_UP);
					}
					$ret[] = $entry;
				}
			}

			if ($ret)
			{
				$bocommon = CreateObject('property.bocommon');
				$bocommon->download($ret, array_keys($ret[0]), array_keys($ret[0]));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
		}

		public function get_participants_per_agegroupe( $data )
		{

			$output_type = 'XHTML';//'XLS';
			$db = & $GLOBALS['phpgw']->db;

			$resources = array();
			if ($data['all_buildings'])
			{
				$sql = "SELECT DISTINCT bb_building_resource.resource_id FROM bb_building"
					. " JOIN bb_building_resource ON bb_building_resource.building_id = bb_building.id"
					. " JOIN bb_resource ON bb_building_resource.resource_id = bb_resource.id"
					. " WHERE bb_building.active = 1"
					. " AND bb_resource.active = 1"
					. " AND bb_building.activity_id IN (" . implode(',', $data['activity_ids']) . ')';
				$db->query($sql);
				while ($db->next_record())
				{
					$resources[] = $db->f('resource_id');
				}
			}
			else
			{
				$resources = $data['resources'];
			}

			$errors = array();
			$from_ = date($db->date_format(), phpgwapi_datetime::date_to_timestamp($data['start_date']));
			$to_ = date($db->date_format(), phpgwapi_datetime::date_to_timestamp($data['end_date']));

			switch ($data['variable_vertical'])
			{
				case 'resource':
					$jasper_parameters = sprintf("'BK_DATE_FROM|%s;BK_DATE_TO|%s;BK_RESOURCES|%s;BK_WEEKDAYS|%s'", $from_, $to_, implode(",", $resources), implode(',', $data['weekdays']));
					$report_source = PHPGW_SERVER_ROOT . '/booking/jasper/templates/participants_per_resource.jrxml';
					break;
				case 'audience':
					$jasper_parameters = sprintf("'BK_DATE_FROM|%s;BK_DATE_TO|%s;BK_RESOURCES|%s;BK_WEEKDAYS|%s'", $from_, $to_, implode(",", $resources), implode(',', $data['weekdays']));
					$report_source = PHPGW_SERVER_ROOT . '/booking/jasper/templates/participants_per_audience.jrxml';
					break;
				case 'activity':
					$jasper_parameters = sprintf("'BK_DATE_FROM|%s;BK_DATE_TO|%s;BK_RESOURCES|%s;BK_WEEKDAYS|%s'", $from_, $to_, implode(",", $resources), implode(',', $data['weekdays']));
					$report_source = PHPGW_SERVER_ROOT . '/booking/jasper/templates/participants_per_activity.jrxml';
					break;

				default:
					$errors[] = 'Valid variable not selected';

					break;
			}


			if (!count($errors))
			{
				$jasper_wrapper = CreateObject('phpgwapi.jasper_wrapper');
				try
				{
					$jasper_wrapper->execute($jasper_parameters, $output_type, $report_source);
				}
				catch (Exception $e)
				{
					$errors[] = $e->getMessage();
				}
			}

			foreach ($errors as $error)
			{
				phpgwapi_cache::message_set($error, 'error');
			}
		}

		/**
		 * Testing
		 * @param type $data
		 * @return string
		 */
		private function get_freetime( $data )
		{
			$db = & $GLOBALS['phpgw']->db;

			$buildings = array();
			if ($data['all_buildings'])
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

			$buildings = implode(',', $buildings);
			$weekdays = implode(',', $data['weekdays']);
			$activity_ids = implode(',', $data['activity_ids']);

			$from = $db->to_timestamp(phpgwapi_datetime::date_to_timestamp($data['start_date']));
			$to = $db->to_timestamp(phpgwapi_datetime::date_to_timestamp($data['end_date']) + 24 * 3600 - 1);


			$sql = "SELECT DISTINCT al.id, al.from_, al.to_, EXTRACT(DOW FROM al.to_) as day_of_week, bu.id as building_id, bu.name as building_name, br.id as resource_id, br.name as resource_name
				FROM bb_allocation al
				INNER JOIN bb_allocation_resource ar on ar.allocation_id = al.id
				INNER JOIN bb_resource br on br.id = ar.resource_id and br.active = 1
				INNER JOIN bb_building_resource bre ON br.id = bre.resource_id
				INNER JOIN bb_building bu on bu.id = bre.building_id
				LEFT JOIN bb_booking bb on bb.allocation_id = al.id
				WHERE bb.id is null
				AND al.from_ >= '{$from}'
				AND al.to_ <= '{$to}'"
				. " AND br.activity_id IN ({$activity_ids}) ";

			if ($buildings)
			{
				$sql .= "and building_id in (" . $buildings . ") ";
			}

			if ($weekdays)
			{
				$sql .= "and EXTRACT(DOW FROM al.from_) in ({$weekdays}) ";
			}

			$sql .= "order by building_name, from_, to_, resource_name";
			$db->query($sql);

			$result = $db->resultSet;

			$retval = array();
			$retval['total_records'] = count($result);
			$retval['results'] = $result;
			$retval['start'] = 0;
			$retval['sort'] = null;
			$retval['dir'] = 'asc';
//			_debug_array($sql);
//			_debug_array($retval);
			return $retval;
		}

		public function get_custom()
		{
			$activity_id = phpgw::get_var('activity_id', 'int');
			$activity_path = $this->activity_bo->get_path($activity_id);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : 0;


			$location = ".application.{$top_level_activity}";
			$organized_fields = ExecMethod('booking.custom_fields.get_fields', $location);
			$variable_vertical = '';
			$variable_horizontal = '';
			foreach ($organized_fields as $group)
			{
				if ($group[id] > 0)
				{
					$header_level = $group['level'] + 2;
					if (isset($group['attributes']) && is_array($group['attributes']))
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
				'status' => 'ok',
				'message' => 'melding',
				'variable_vertical' => $variable_vertical,
				'variable_horizontal' => $variable_horizontal
			);
		}

		public function participants()
		{
			self::set_active_menu('booking::reportcenter::participants');
			$errors = array();
			$buildings = $this->building_bo->read();

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$to = phpgw::get_var('to', 'string');
				$from = phpgw::get_var('from', 'string');

				$to_ = date("Y-m-d", phpgwapi_datetime::date_to_timestamp($to));
				$from_ = date("Y-m-d", phpgwapi_datetime::date_to_timestamp($from));

				$output_type = phpgw::get_var('otype', 'string');
				$building_list = phpgw::get_var('building');

				if (!count($building_list))
				{
					$errors[] = lang('No buildings selected');
				}

				if (!count($errors))
				{
					$jasper_parameters = sprintf("\"BK_DATE_FROM|%s;BK_DATE_TO|%s;BK_BUILDINGS|%s\"", $from_, $to_, implode(",", $building_list));
					// DEBUG
					//print_r($jasper_parameters);
					//exit(0);

					$jasper_wrapper = CreateObject('phpgwapi.jasper_wrapper');
					$report_source = PHPGW_SERVER_ROOT . '/booking/jasper/templates/participants.jrxml';
					try
					{
						$jasper_wrapper->execute($jasper_parameters, $output_type, $report_source);
					}
					catch (Exception $e)
					{
						$errors[] = $e->getMessage();
					}
				}
			}
			else
			{
				$to = date($this->dateFormat, time());
				$from = date($this->dateFormat, time());
			}

			phpgwapi_cache::message_set($errors, 'error');

			$GLOBALS['phpgw']->jqcal2->add_listener('from', 'date');
			$GLOBALS['phpgw']->jqcal2->add_listener('to', 'date');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Report Participants'), 'link' => '#report_part');
			$active_tab = 'generic';

			$data = array();
			$data['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$data['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			self::render_template_xsl('report_participants', array('data' => $data, 'from' => $from,
				'to' => $to, 'buildings' => $buildings['results']));
		}

		public function freetime()
		{
			self::set_active_menu('booking::reportcenter::free_time');
			$errors = array();
			$buildings = $this->building_bo->read();

			$show = '';
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$to = phpgw::get_var('to', 'string');
				$from = phpgw::get_var('from', 'string');

				$to_ = date("Y-m-d", phpgwapi_datetime::date_to_timestamp($to));
				$from_ = date("Y-m-d", phpgwapi_datetime::date_to_timestamp($from));

				$show = 'report';
				$allocations = $this->get_free_allocations(
					phpgw::get_var('building'), $from_, $to_, phpgw::get_var('weekdays')
				);

				$counter = 0;
				foreach ($allocations['results'] as &$allocation)
				{
					$temp = array();
					$temp[] = array('from_', $allocation['from_']);
					$temp[] = array('to_', $allocation['to_']);
					$temp[] = array('building_id', $allocation['building_id']);
					$temp[] = array('building_name', $allocation['building_name']);
					$temp[] = array('resources[]', array($allocation['resource_id']));
					$temp[] = array('reminder', 0);
					$temp[] = array('from_report', true); // indicate that no error messages should be shown
					$allocation['counter'] = $counter;
					$allocation['event_params'] = json_encode($temp);
					$counter++;
				}
				if (count($allocations['results']) == 0)
				{
					$show = 'gui';
					$errors[] = lang('no records found.');
				}
				$allocations['cancel_link'] = self::link(array('menuaction' => 'booking.uireports.freetime'));
			}
			else
			{
				$to = date($this->dateFormat, time());
				$from = date($this->dateFormat, time());
				$show = 'gui';
			}

			phpgwapi_cache::message_set($errors, 'error');
			//$this->flash_form_errors($errors);

			$GLOBALS['phpgw']->jqcal2->add_listener('from', 'date');
			$GLOBALS['phpgw']->jqcal2->add_listener('to', 'date');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Report FreeTime'), 'link' => '#report_freetime');
			$active_tab = 'generic';

			$data = array();
			$data['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			self::render_template_xsl('report_freetime', array('data' => $data, 'show' => $show,
				'from' => $from, 'to' => $to, 'buildings' => $buildings['results'], 'allocations' => $allocations['results']));
		}

		private function get_free_allocations( $buildings, $from, $to, $weekdays )
		{
			$db = & $GLOBALS['phpgw']->db;

			$buildings = implode(",", $buildings);
			$weekdays = implode(",", $weekdays);

			$sql = "SELECT DISTINCT al.id, al.from_, al.to_, EXTRACT(DOW FROM al.to_) as day_of_week, bu.id as building_id, bu.name as building_name, br.id as resource_id, br.name as resource_name
				FROM bb_allocation al
				INNER JOIN bb_allocation_resource ar on ar.allocation_id = al.id
				INNER JOIN bb_resource br on br.id = ar.resource_id and br.active = 1
				INNER JOIN bb_building_resource bre ON br.id = bre.resource_id
				inner join bb_building bu on bu.id = bre.building_id
				LEFT JOIN bb_booking bb on bb.allocation_id = al.id
				WHERE bb.id is null
				AND al.from_ >= '" . $from . " 00:00:00'
				AND al.to_ <= '" . $to . " 23:59:59' ";

			if ($buildings)
				$sql .= "and building_id in (" . $buildings . ") ";

			if ($weekdays)
				$sql .= "and EXTRACT(DOW FROM al.from_) in (" . $weekdays . ") ";

			$sql .= "order by building_name, from_, to_, resource_name";
			$db->query($sql);

			$result = $db->resultSet;

			$retval = array();
			$retval['total_records'] = count($result);
			$retval['results'] = $result;
			$retval['start'] = 0;
			$retval['sort'] = null;
			$retval['dir'] = 'asc';

			return $retval;
		}
	}

	class OverlapCalculator
	{

		/**
		 * @var int
		 */
		private $timeIn;

		/**
		 * @var int
		 */
		private $timeOut;

		/**
		 * @var int
		 */
		private $totalOverlap = 0;

		/**
		 * @param DateTime|int $timeIn
		 * @param DateTime|int $timeOut
		 */
		public function __construct( $timeIn, $timeOut )
		{
			if ($timeIn instanceOf DateTime)
			{
				$this->timeIn = $timeIn->getTimestamp();
			}
			else
			{
				$this->timeIn = $timeIn;
			}

			if ($timeOut instanceOf DateTime)
			{
				$this->timeOut = $timeOut->getTimestamp();
			}
			else
			{
				$this->timeOut = $timeOut;
			}
		}

		/**
		 * @param DateTime|int $periodStart
		 * @param DateTime|int $periodEnd
		 */
		public function addOverlapFrom( $periodStart, $periodEnd )
		{
			if ($periodStart instanceOf DateTime)
			{
				$periodStart = $periodStart->getTimestamp();
			}

			if ($periodEnd instanceOf DateTime)
			{
				$periodEnd = $periodEnd->getTimestamp();
			}

			$this->totalOverlap += $this->calculateOverlap($periodStart, $periodEnd);
		}

		/**
		 * @param $periodStart
		 * @param $periodEnd
		 * @return int
		 */
		private function calculateOverlap( $periodStart, $periodEnd )
		{
			if ($periodStart >= $this->timeIn && $periodEnd <= $this->timeOut)
			{
				// The compared time range can be contained within borders of the source time range, so the over lap is the entire compared time range
				return $periodEnd - $periodStart;
			}
			elseif ($periodStart >= $this->timeIn && $periodStart <= $this->timeOut)
			{
				// The compared time range starts after or at the source time range but also ends after it because it failed the condition above
				return $this->timeOut - $periodStart;
			}
			elseif ($periodEnd >= $this->timeIn && $periodEnd <= $this->timeOut)
			{
				// The compared time range starts before the source time range and ends before the source end time
				return $periodEnd - $this->timeIn;
			}
			elseif ($this->timeIn > $periodStart && $this->timeOut < $periodEnd)
			{
				// The compared time range is actually wider than the source time range, so the overlap is the entirety of the source range
				return $this->timeOut - $this->timeIn;
			}

			return 0;
		}

		/**
		 * @return int
		 */
		public function getOverlap()
		{
			return $this->totalOverlap;
		}
	}