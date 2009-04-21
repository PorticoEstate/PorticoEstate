<?php
	phpgw::import_class('booking.uicommon');

	class bookingfrontend_uisearch extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'	=>	true
		);

		function __construct()
		{
			$this->bo = CreateObject('bookingfrontend.bosearch');
			parent::__construct();
			$old_top = array_pop($this->tmpl_search_path);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/booking/templates/base');
			array_push($this->tmpl_search_path, $old_top);
		}
		
		function index()
		{
			$searchterm = phpgw::get_var('searchterm');
			$search = array(
				'results'    => $this->bo->search($searchterm),
				'searchterm' => $searchterm,
			);
			self::render_template('search', array('search' => $search));
		}
		
		
	}
