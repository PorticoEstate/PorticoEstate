<?php
phpgw::import_class("booking.uicommon");
phpgw::import_class('bookingfrontend.bosearch');
phpgw::import_class('booking.bobooking');
phpgw::import_class('bookingfrontend.bouser');
phpgw::import_class('booking.boorganization');
phpgw::import_class('booking.bobuilding');
phpgw::import_class('booking.soorganization');



class bookingfrontend_uieventsearch extends booking_uicommon
{

	public $public_functions = array
	(
		'index' => true,
		'show'  => true,
		'upcomingEvents' => true,
		'getOrgsIfLoggedIn' => true,
		'get_facilityTypes' => true
	);

	protected $module;
	protected $bosearch;
	protected $bo_booking;
	protected $boorg;
	protected $bobuilding;
	protected $so_organization;

	public function __construct()
	{
		parent::__construct();

		$this->module= "bookingfrontend";
		$this->bosearch = new bookingfrontend_bosearch();
		$this->bo_booking = new booking_bobooking();
		$this->boorg = new booking_boorganization();
		$this->bobuilding = new booking_bobuilding();
		$this->so_organization = new booking_soorganization();
	}

	public function get_facilityTypes()
	{
		$query = phpgw::get_var('query', 'string', 'REQUEST', null);

		$ret =  $this->bobuilding->get_facilityTypes($query);
		//keep 15 of the first...


		$result_data['start'] = 0;
		$result_data['dir'] = 'asc';
		$result_data['sort'] = null;

		$result_data['results'] = array_slice($ret, 0, 15);
		$result_data['total_records'] = count($ret);

		return $this->jquery_results($result_data);
	}

	public function show()
	{
		$event_search['dickens'] = "test";
		$config = CreateObject('phpgwapi.config', 'booking');
		$config->read();
		phpgwapi_jquery::load_widget("core");
		phpgwapi_jquery::load_widget('daterangepicker');
		$GLOBALS['phpgw']->css->add_external_file("phpgwapi/templates/aalesund/css/rubik-font.css");



		self::add_javascript('bookingfrontend', 'aalesund', 'event_search.js', 'text/javascript', true);
		self::add_javascript('bookingfrontend', 'aalesund', 'util.js', 'text/javascript', true);
		self::render_template_xsl('event_search', array('event_search' => $event_search));

	}

	public function getOrgsIfLoggedIn()
	{
		$bouser = new bookingfrontend_bouser();
		$orgs = null;
		if ($bouser->is_logged_in()) {
			return $bouser;
		}
	}

	/***
	 * Metode for å hente events til søkesiden
	 */
	public function upcomingEvents()
	{
		$orgID = phpgw::get_var('orgID', 'string', 'REQUEST', null);
		$fromDate = phpgw::get_var('fromDate', 'string', 'REQUEST', null);
		$toDate = phpgw::get_var('toDate', 'string', 'REQUEST', null);
		$buildingId = phpgw::get_var('buildingID', 'string', 'REQUEST', null);
		$facilityTypeID = phpgw::get_var('facilityTypeID', 'string', 'REQUEST', null);
		$loggedInOrgs = phpgw::get_var('loggedInOrgs', 'string', 'REQUEST', null);
		$start = phpgw::get_var('start', 'int', 'REQUEST', 0);;
		$end = phpgw::get_var('end', 'int', 'REQUEST', 50);;

		$result_string = '';
		if ($loggedInOrgs != '')
		{
			$result_string = "'" . str_replace(",", "','", $loggedInOrgs) . "'";
		}

		$org_info = array();
		if (isset($orgID) && $orgID != '')
		{
			$org_info = $this->so_organization->read_single($orgID);
		}

		$events = $this->bosearch->soevent->get_events_from_date($fromDate, $toDate, $org_info, $buildingId, $facilityTypeID, $result_string, $start, $end);

		foreach ($events as &$event)
		{
			$organization_info = $this->so_organization->get_organization_info($event['org_num']);

			if ($organization_info['name'] === '')
			{
				$event['org_name'] = ($event['organizer'] === '' ? 'Ingen' : $event['organizer']);
			}
			else
			{
				$event['org_name'] = $organization_info['name'];
			}

			if ($organization_info['id'] !== '' && $event['org_id'] === '')
			{
				$event['org_id'] = $organization_info['id'];
			}
		}
		unset($event);

		return $events;
	}

	public function index()
	{
		_debug_json($GLOBALS);
		phpgwapi_jquery::load_widget('autocomplete');

		if (phpgw::get_var('phpgw_return_as') == 'json')
		{
			return $this->query();
		}

		phpgw::no_access();
	}

	public function query()
	{
		// TODO: Implement query() method.
	}
}
