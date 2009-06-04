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
			$searchterm = trim(phpgw::get_var('searchterm', 'string', null));
			$search = null;
			
			if (strlen($searchterm))
			{
				$search = array(
					'results'    => strlen($searchterm) ? $this->bo->search($searchterm) : array('total_records_sum' => 0),
					'searchterm' => $searchterm,
				);
			}
			
			$params = is_null($search) ? array() : array('search' => $search);

			self::render_template('search', $params);
		}
		
		
	}
