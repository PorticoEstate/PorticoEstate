<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.sodocument_resource');

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
				"four" => "50+");
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
			$resource['region'] = phpgw::get_var('regions', 'GET', null);
	
			$search = null;

			if (strlen($searchterm) || $type || $resource['res'] || $resource['fylke'] || $resource['region'])
			{
				$search = array(
					'results'    => $this->bo->search($searchterm,$resource),
					'searchterm' => $searchterm
				);
			}
			self::add_javascript('bookingfrontend', 'bookingfrontend', 'search.js');

			// Images are now loaded from bb_document with category frontpage_picture
			if( !is_null( $search ) ) {
				$params = array('search' => $search,'layout' => $layout,'resource' => $resource);
			} else {


				$params = array(
					'frontimage' => "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/{$GLOBALS['phpgw_info']['server']['template_set']}/images/nsf/forsidebilde.png",
					'layout' => $layout,
					'resource' => $resource,
				);
				
				// Get frontpage picture documents - Resources
				$sodocres = CreateObject('booking.sodocument_resource');
				$resource_documents = $sodocres->read( array( "filters" => array( "category" => booking_sodocument::CATEGORY_FRONTPAGE_PICTURE ) ) );
				
				// Insert into $params if there are pictures - Resources
				if( $resource_documents['total_records'] > 0 ) {
					// Convert nl2br on description
					foreach( $resource_documents['results'] as $key => $data ) {
						$resource_documents['results'][$key]['description'] = nl2br( $resource_documents['results'][$key]['description'] );
						$resource_documents['results'][$key]['type'] = "resource";
					}
					$params['frontimages'] = $resource_documents['results'];
				}
			
				// Get frontpage picture documents - Buildings
				$sodocbuild = CreateObject('booking.sodocument_building');
				$building_documents = $sodocbuild->read( array( "filters" => array( "category" => booking_sodocument::CATEGORY_FRONTPAGE_PICTURE ) ) );

				// Insert into $params if there are pictures - Buildings
				if( $building_documents['total_records'] > 0 ) {
					// Convert nl2br on description
					foreach( $building_documents['results'] as $key => $data ) {
						$building_documents['results'][$key]['description'] = nl2br( $building_documents['results'][$key]['description'] );
						$building_documents['results'][$key]['type'] = "building";
					}
					$params['frontimages'] = array_merge( $params['frontimages'], $building_documents['results'] );
				}
			}
//			echo "<pre>";print_r($resource);exit;
			
			self::render_template('search', $params);
		}
		
		
	}
