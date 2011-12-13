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
			$this->bbresource = CreateObject('booking.soresource');
			parent::__construct();
      array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/bookingfrontend/templates/' . $GLOBALS['phpgw_info']['server']['template_set']);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/booking/templates/base');
		}


		protected function resource_types()
		{
			$types = array();
			foreach($this->bbresource->allowed_types() as $type) { $types[$type] = self::humanize($type); }
			return $types;
		}

		protected function bedspaces()
		{
			return array( "one" => "1-10",
				"two" => "10-25",
				"three" => "25-50",
				"Four" => "50-100",
				"Five" => "100+");
		}	

		protected function regions()
		{
			return array( "all" => lang("All"),
				"east" => lang("East"),
				"south" => lang("South"),
				"west" => lang("West"),
				"middle" => lang("Middle"),
				"north" => lang("North"));
		}	

		protected function fylker()
		{
			return array( "akerhus" => "Akershus",
				"austagder" => "Aust-Agder",
				"buskerud" => "Buskerud",
				"finnmark" => "Finnmark",
				"hedemark" => "Hedmark",
				"hordaland" => "Hordaland",
				"moreogromsdal" => "Møre og Romsdal",
				"nordland" => "Nordland",
				"nordtrodelag" => "Nord-Trøndelag",
				"oppland" => "Oppland",
				"rogaland" => "Rogaland",
				"songogfjordane" => "Sogn og Fjordane",
				"sortrondelag" => "Sør-Trøndelag",
				"telemark" => "Telemark",
				"troms" => "Troms",
				"vestagder" => "Vest-Agder",
				"vestfold" => "Vestfold",
				"ostfold" => "Østfold");
		}	
		function index()
		{
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			$layout = $config->config_data['layout_settings'];
			$resource = array();
			$resource['types'] = $this->resource_types();
			$resource['fylker'] = $this->fylker();
			$resource['regions'] = $this->regions();
			$resource['bedspaces'] = $this->bedspaces();
			$searchterm = trim(phpgw::get_var('searchterm', 'string', null));
			$resource['type'] = phpgw::get_var('type', 'GET', null);
			$resource['res'] = phpgw::get_var('res', 'GET', null);
			$resource['fylke'] = phpgw::get_var('fylker', 'GET', null);
			$resource['campsite'] = phpgw::get_var('campsites', 'GET', null);
			$resource['beds'] = phpgw::get_var('bedspaces', 'GET', null);
			$resource['region']['all'] = phpgw::get_var('all', 'GET', null);
			$resource['region']['east'] = phpgw::get_var('east', 'GET', null);
			$resource['region']['south'] = phpgw::get_var('south', 'GET', null);
			$resource['region']['west'] = phpgw::get_var('west', 'GET', null);
			$resource['region']['middle'] = phpgw::get_var('middle', 'GET', null);
			$resource['region']['north'] = phpgw::get_var('north', 'GET', null);
	
			$search = null;
			
			if (strlen($searchterm) || $type || $resource['res'] || $resource['fylke'])
			{
				$search = array(
					'results'    => $this->bo->search($searchterm,$resource),
					'searchterm' => $searchterm
				);
			}
			self::add_javascript('bookingfrontend', 'bookingfrontend', 'search.js');
      // Should of course be replaced with some config option for the image
      // or using the tmpl_search_path. Need to work a little mor on this system
      // to find the right option. - thomasez
			$params = is_null($search) ? array('frontimage' => "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/{$GLOBALS['phpgw_info']['server']['template_set']}/images/nsf/forsidebilde.png",'layout' => $layout,'resource' => $resource) : array('search' => $search,'layout' => $layout,'resource' => $resource);
//			echo "<pre>";print_r($resource);exit;
			
			self::render_template('search', $params);
		}
		
		
	}
