<?php
	phpgw::import_class('booking.uicommon');

	class bookingfrontend_uibuilding extends booking_uicommon
	{
		public $public_functions = array(
			 'index'		=> true,
			 'schedule'		=> true,
			 'show'         => true,
		);

		function __construct()
		{
			$this->bo = CreateObject('booking.bobuilding');
			parent::__construct();
			$old_top = array_pop($this->tmpl_search_path);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/booking/templates/base');
			array_push($this->tmpl_search_path, $old_top);
		}
		
		function index()
		{
			$search = array();
			$target_id = phpgw::get_var('id');
			$results = $this->bo->read_single($target_id);
			$results['schedule_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id' => $results['id']));
			$search["results"] = $results;
			$search['results']['start'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookingfrontend.uisearch.index'));

			self::render_template('building', array('search' => $search));
		}
		public function schedule()
		{
			$building = $this->bo->get_schedule(phpgw::get_var('id', 'GET'), 'bookingfrontend.uibuilding');

			self::add_javascript('booking', 'booking', 'schedule.js');
			self::render_template('building_schedule', array('building' => $building));
		}
		
		public function show()
		{
			$this->check_active('booking.uibuilding.show');
			$building = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$building['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$building['edit_link'] = self::link(array('menuaction' => 'booking.uibuilding.edit', 'id' => $building['id']));
			$building['schedule_link'] = self::link(array('menuaction' => 'booking.uibuilding.schedule', 'id' => $building['id']));
			self::render_template('building', array('search' => array("results" => $building)));
		}
		
	}
