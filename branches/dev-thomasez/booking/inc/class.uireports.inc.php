<?php

phpgw::import_class('booking.uicommon');
phpgw::import_class('phpgwapi.send');

class booking_uireports extends booking_uicommon
{
	public $public_functions = array(
			'index'                 =>      true,
			'participants'          =>      true,
			'freetime'              =>      true,
			'searchterms'              =>      true,
		);

	public function __construct()
	{
		parent::__construct();

		$this->building_bo = CreateObject('booking.bobuilding');
		self::set_active_menu('booking::reportcenter');
	}

	public function index()
	{
		$reports[] = array('name' => lang('Participants Per Age Group Per Month'), 'url' => self::link(array('menuaction' => 'booking.uireports.participants')));
		$reports[] = array('name' => lang('Free time'), 'url' => self::link(array('menuaction' => 'booking.uireports.freetime')));
		$reports[] = array('name' => lang('Search terns'), 'url' => self::link(array('menuaction' => 'booking.uireports.searchterms')));

		self::render_template('report_index',
		array('reports' => $reports));
	}

	public function participants()
	{
		self::set_active_menu('booking::reportcenter::participants');
		$errors = array();
		$buildings = $this->building_bo->read();
		$to = '2009-01-01';
		$from = '2009-01-01';

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{

			$to = phpgw::get_var('to', 'POST');
			$from = phpgw::get_var('from', 'POST');

			$output_type = phpgw::get_var('otype', 'POST');
			$building_list = phpgw::get_var('building', 'POST');

			if (!count($building_list))
			{
				$errors[] = lang('No buildings selected');
			}

			if (!count($errors))
			{
				$jasper_parameters = sprintf("\"BK_DATE_FROM|%s;BK_DATE_TO|%s;BK_BUILDINGS|%s\"",
					$from,
					$to,
					implode(",", $building_list));
				// DEBUG
				//print_r($jasper_parameters);
				//exit(0);

				$jasper_wrapper 	= CreateObject('phpgwapi.jasper_wrapper');
				$report_source		= PHPGW_SERVER_ROOT.'/booking/jasper/templates/participants.jrxml';
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
			$to = date("Y-m-d", time());
			$from = date("Y-m-d", time());
		}

		$this->flash_form_errors($errors);
		self::render_template('report_participants',
		array('from' => $from, 'to' => $to, 'buildings' => $buildings['results']));
	}

	public function freetime()
	{
		self::set_active_menu('booking::reportcenter::free_time');
		$errors = array();
		$buildings = $this->building_bo->read();

		$show = '';
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$show = 'report';
			$allocations = $this->get_free_allocations(
					phpgw::get_var('building', 'POST'),
					phpgw::get_var('from', 'POST'),
					phpgw::get_var('to', 'POST'),
					phpgw::get_var('weekdays', 'POST')
					);

			$counter = 0;
			foreach($allocations['results'] as &$allocation)
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
				$to = phpgw::get_var('to', 'POST');
				$from = phpgw::get_var('from', 'POST');
			}
		}
		else
		{
			$to = date("Y-m-d", time());
			$from = date("Y-m-d", time());
			$show = 'gui';
		}

		$this->flash_form_errors($errors);

		self::render_template('report_freetime',
				array('show' => $show, 'from' => $from, 'to' => $to, 'buildings' => $buildings['results'], 'allocations' => $allocations['results']));
	}

	// Merge similar terms from different months. Used when displaying search counters from several months and/or years.
	private function mergeSimilarTerms( $terms ) {
		$newTerms = array();
		$newTermsIntKey = array();
		foreach( $terms as $id => $data ) {
//			error_log( $id );
			if( array_key_exists( $data['term'], $newTerms ) ) {
				$newTerms[$data['term']]['count'] += $data['count'];
			} else {
				$newTerms[$data['term']] = array( 'term' => $data['term'], 'count' => $data['count'] );
			}
		}

		// Convert to integer keys. phpGroupware needs keys to be integer in order to traverse them
		$i = 0;
		foreach( $newTerms as $key => $data ) {
			$newTermsIntKey[$i] = $data;
			$i++;
		}
		return $newTermsIntKey;
	}


	public function searchterms()
	{
		self::set_active_menu('booking::reportcenter::search_terms');

		$errors = array();
		$db = & $GLOBALS['phpgw']->db;

		// Set period
		$form_period = ( $_POST['period'] ? $_POST['period'] : date("Y-m-d" ) );
		$year = date( "Y", strtotime( $form_period ) );
		$month = date( "m", strtotime( $form_period ) );
		$database_period = $year . $month;

		// Get search terms
		switch( $_POST['dimension'] ) {
			case "forever":
				$sql = "SELECT term,count FROM bb_searchcount ORDER BY count DESC";
				break;
			case "year":
				$sql = "SELECT term,count FROM bb_searchcount WHERE period>=" . $year . "01 AND period<=" . $year . "12 ORDER BY count DESC";
				break;
			default: // Month
				$sql = "SELECT term,count FROM bb_searchcount WHERE period=" . $database_period . " ORDER BY count DESC";
				break;
		}
		$db->query( $sql );
		$terms = $db->resultSet;
//		error_log( var_export( $terms, true ) );
		$terms = $this->mergeSimilarTerms(  $terms );
//		error_log( var_export( $terms, true ) );
		self::render_template('report_searchterms', array(
			"period" => $form_period,
			"terms" => $terms,
			"dimension" => $_POST['dimension']
		) );
	}

	private function get_free_allocations($buildings, $from, $to, $weekdays)
	{
		$db = & $GLOBALS['phpgw']->db;

		$buildings = implode(",", $buildings);
		$weekdays = implode(",", $weekdays);

		$sql = "select distinct al.id, al.from_, al.to_, EXTRACT(DOW FROM al.to_) as day_of_week, bu.id as building_id, bu.name as building_name, br.id as resource_id, br.name as resource_name
				from bb_allocation al
				inner join bb_allocation_resource ar on ar.allocation_id = al.id
				inner join bb_resource br on br.id = ar.resource_id and br.active = 1
				inner join bb_building bu on bu.id = br.building_id
				left join bb_booking bb on bb.allocation_id = al.id
				where bb.id is null 
				and al.from_ >= '".$from." 00:00:00'
				and al.to_ <= '".$to." 23:59:59' ";

		if ($buildings)
			$sql .= "and building_id in (".$buildings.") ";

		if ($weekdays)
			$sql .= "and EXTRACT(DOW FROM al.from_) in (".$weekdays.") ";
		
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
