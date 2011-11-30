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
      array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/bookingfrontend/templates/' . $GLOBALS['phpgw_info']['server']['template_set']);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/booking/templates/base');
		}
		
		function index()
		{
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			$layout = $config->config_data['layout_settings'];
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
			
      // Should of course be replaced with some config option for the image
      // or using the tmpl_search_path. Need to work a little mor on this system
      // to find the right option. - thomasez
			$params = is_null($search) ? array('frontimage' => "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/{$GLOBALS['phpgw_info']['server']['template_set']}/images/nsf/forsidebilde.png") : array('search' => $search,'layout' => $layout);

			self::render_template('search', $params);
		}
		
		
	}
