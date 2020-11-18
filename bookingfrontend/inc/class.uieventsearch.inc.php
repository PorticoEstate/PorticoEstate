<?php
phpgw::import_class("booking.uicommon");
phpgw::import_class('bookingfrontend.bosearch');
phpgw::import_class('booking.bobooking');


class bookingfrontend_uieventsearch extends booking_uicommon
{

	public $public_functions = array
	(
		'index' => true,
		'show'  => true,
		'upcomingEvents' => true,
	);

	protected $module;
	protected $bosearch;
	protected $bo_booking;

	public function __construct()
	{
		parent::__construct();
		$this->module= "bookingfrontend";
		$this->bosearch = new bookingfrontend_bosearch();
		$this->bo_booking = new booking_bobooking();
	}

	public function show()
	{
		phpgwapi_jquery::load_widget('autocomplete');

		$event_search['dickens'] = "test";
		$config = CreateObject('phpgwapi.config', 'booking');
		$config->read();
		phpgwapi_jquery::load_widget("core");

		self::add_javascript('bookingfrontend', 'aalesund', 'event_search.js', 'text/javascript', true);
		self::render_template_xsl('event_search', array('event_search' => $event_search));

	}

	/***
	 * Metode for å hente events til søkesiden
	 */
	public function upcomingEvents()
	{
		$orgName = phpgw::get_var('orgName', 'string', 'REQUEST', null);
		$fromDate = phpgw::get_var('fromDate', 'string', 'REQUEST', null);
		$toDate = phpgw::get_var('toDate', 'string', 'REQUEST', null);
		$buildingId = phpgw::get_var('buildingID', 'string', 'REQUEST', null);

		$events = $this->bosearch->soevent->get_events_from_date($fromDate, $toDate, $orgName, $buildingId);
		return $events;
	}

	public function index()
	{
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