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
			
			parent::__construct();
			$this->bo = CreateObject('bookingfrontend.bosearch');
			$old_top = array_pop($this->tmpl_search_path);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/booking/templates/base');
			array_push($this->tmpl_search_path, $old_top);
		}
		
		function index()
		{
			phpgwapi_jquery::load_widget('autocomplete');

			self::add_javascript('bookingfrontend', 'bookingfrontend', 'search.js');
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			$searchterm = trim(phpgw::get_var('searchterm', 'string', null));
			$type = phpgw::get_var('type','string', 'GET', null);
			$activity_top_level = phpgw::get_var('activity_top_level', 'int', 'REQUEST', null);
			$building_id = phpgw::get_var('building_id', 'int', 'REQUEST', null);

			$search = null;
			if ($config->config_data['frontpagetext'] != '')
			{
				$frontpagetext = $config->config_data['frontpagetext'];
			}
			else
			{
				$frontpagetext = 'Velkommen til AktivBy.<br />Her finner du informasjon om idrettsanlegg som leies ut<br />av idrettsavdelingen.';
			}
			
			if ($building_id || $type || $activity_top_level)
			{
				$search = array(
					'results'    => $this->bo->search($searchterm, $activity_top_level, $building_id),
					'searchterm' => $searchterm,
					'activity_top_level'=> $activity_top_level
				);
			}
			
			$params		= is_null($search) ? array('baseurl' => "{$GLOBALS['phpgw_info']['server']['webserver_url']}", 'frontimage' => "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/bkbooking/images/newlayout/forsidebilde.jpg", 'frontpagetext' => $frontpagetext) : array('search' => $search);
			$params['activity_top_level'] = $activity_top_level;

			$bobuilding = CreateObject('booking.bobuilding');
			$building = $bobuilding->read_single($building_id);
			$params['building_name'] = $building['name'];

			$activities	= ExecMethod('booking.boactivity.get_top_level');

			foreach($activities as &$activity)
			{
				$activity['search_url'] = self::link(array(
					'menuaction'			=> 'bookingfrontend.uisearch.index',
					'activity_top_level'	=> $activity['id'],
					'building_id'			=> $building_id
				));
			}

			$params['activities']	= $activities;

//			self::render_template('search', $params);
            self::render_template_xsl('search', $params);

		}

		function query()
		{
			
		}	
		
	}
