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
		'upcoming_events' => true,
		'get_orgs_if_logged_in' => true,
		'get_facility_types' => true
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

	public function get_facility_types()
	{
		$query = phpgw::get_var('query', 'string', 'REQUEST', null);

		$ret =  $this->bobuilding->get_facility_types($query);
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
		$GLOBALS['phpgw']->css->add_external_file("phpgwapi/templates/base/css/rubik-font.css");

		self::add_javascript('bookingfrontend', 'base', 'event_search.js', 'text/javascript', true);
		self::add_javascript('bookingfrontend', 'base', 'util.js', 'text/javascript', true);
		self::render_template_xsl('event_search', array('event_search' => $event_search));

	}

	public function get_orgs_if_logged_in()
	{
		$bouser = new bookingfrontend_bouser();

		if ($bouser->is_logged_in())
		{
			return $bouser->orgnr;
		}
	}

	/***
	 * Metode for å hente events til søkesiden
	 */
	public function upcoming_events()
	{
		$org_id = phpgw::get_var('orgID', 'string', 'REQUEST', null);
		$from_date = phpgw::get_var('fromDate', 'string', 'REQUEST', null);
		$to_date = phpgw::get_var('toDate', 'string', 'REQUEST', null);
		$building_id = phpgw::get_var('buildingID', 'string', 'REQUEST', null);
		$facility_type_id = phpgw::get_var('facilityTypeID', 'string', 'REQUEST', null);
		$logged_in_orgs = phpgw::get_var('loggedInOrgs', 'bool');
		$start = phpgw::get_var('start', 'int', 'REQUEST', 0);;
		$end = phpgw::get_var('end', 'int', 'REQUEST', 50);;


		$logged_in_as = $this->get_orgs_if_logged_in();

		$filter_organization = null;
		if ($logged_in_orgs && $logged_in_as)
		{
			$filter_organization = true;
		}

		$org_info = array();
		if (isset($org_id) && $org_id != '')
		{
			$org_info = $this->so_organization->read_single($org_id);
		}

		$organizations = array();
		$events = $this->bosearch->soevent->get_events_from_date($from_date, $to_date, $org_info, $building_id, $facility_type_id, $filter_organization, $logged_in_as, $start, $end);

		foreach ($events as &$event)
		{

			//Needed for initate date object in safari-browser
			$event['from'] = str_replace(" ", "T", $event['from']);
			$event['to'] = str_replace(" ", "T", $event['to']);
			if(isset($organizations[$event['org_num']]))
			{
				$organization_info = $organizations[$event['org_num']];
			}
			else
			{
				$organization_info = $this->so_organization->get_organization_info($event['org_num']);
				$organizations[$event['org_num']] = $organization_info;
			}

			if ($organization_info['name'] === '')
			{
				$event['org_name'] = ($event['organizer'] === '' ? 'Ingen' : $event['organizer']);
			}
			else
			{
				$event['org_name'] = $organization_info['name'];
			}

			if (!empty($organization_info['id']) && empty($event['org_id']))
			{
				$event['org_id'] = $organization_info['id'];
			}
		}
		unset($event);

		return $events;
	}

	public function index()
	{
		phpgwapi_jquery::load_widget('autocomplete');

		if (phpgw::get_var('phpgw_return_as') == 'json')
		{
			return $this->query();
		}

		phpgw::no_access();
	}

	public function query()
	{
	}
}
