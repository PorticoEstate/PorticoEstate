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
			$type = phpgw::get_var('type', 'GET', null);
			$search = null;
			
			if (strlen($searchterm) || $type)
			{
				$search = array(
					'results'    => $this->bo->search($searchterm),
					'searchterm' => $searchterm
				);
			}
			
			$params = is_null($search) ? array('baseurl' => "{$GLOBALS['phpgw_info']['server']['webserver_url']}", 'frontimage' => "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/bkbooking/images/newlayout/forsidebilde.jpg") : array('search' => $search);

			self::render_template('search', $params);
		}
		
		
	}
