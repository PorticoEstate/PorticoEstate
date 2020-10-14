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
		'upcomingEvents' => true
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

		self::add_javascript('bookingfrontend', 'base', 'event_search.js', 'text/javascript', true);
		self::render_template_xsl('event_search', array('event_search' => $event_search));

	}

	/***
	 * Metode for å hente events til søkesiden
	 */
	public function upcomingEvents()
	{
//	        $allocation['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show',
//		        'id' => $allocation['resources'][0]['building_id']));
		$orgName = phpgw::get_var('orgName', 'string', 'REQUEST', null);

		$currentDate = date('Y-m-d H:i:s');
		$events = $this->bosearch->soevent->get_events_from_date($currentDate, $orgName);
		foreach ($events as $event) {
			$event = $this->addBuildingUrl($event);
		}
		return $events;
	}

	function addBuildingUrl($event)
	{
		//TODO fullfør denne
//		$decoded_event = json_decode($event);
//		$this->bo_booking->read_single($decoded_event[])
	}

	public function query()
	{
		// TODO: Implement query() method.
	}
	public function index()
	{
		if (phpgw::get_var('phpgw_return_as') == 'json')
		{
			return $this->query();
		}

		phpgw::no_access();
	}

}