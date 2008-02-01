<?php
	// INSERT HEADER HERE
	class phpgwapi_template_newdesign
	{
		public $public_functions = array
		(
			'store'			=> True,
			'retrive'		=> True
		);

		// TODO: document me with phpdoc
		public function store()
		{
			$location = phpgw::get_var('location');
			$data = phpgw::get_var('data', 'raw');

			if( $location == null )
			{
				header("HTTP/1.0 406 Not Acceptable");
				return "Missing location parameter";
			}

			$json = json_decode($data, true);

			if( $json == null )
			{
				header("HTTP/1.0 406 Not Acceptable");
				return "Invalid JSON data parameter";
			}

			$GLOBALS['phpgw']->session->appsession("template_newdesign_$location", 'phpgwapi', $json);
			return $json;
		}

		// TODO: document me with phpdoc
		public function retrieve()
		{
			$location = phpgw::get_var('location');

			if( $location == null )
			{
				header("HTTP/1.0 406 Not Acceptable");
				return "Missing location parameter";
			}

			$data = self::retrieve_local($location);

			if ( $data == null )
			{
				header("HTTP/1.0 404 Not Found");
				return "No data found on that location";
			}

			return $data;
		}

		// TODO: document me with phpdoc
		public static function retrieve_local($location)
		{
			return $GLOBALS['phpgw']->session->appsession("template_newdesign_{$location}", 'phpgwapi');
		}

		public static function store_local($location, $data)
		{
			$GLOBALS['phpgw']->session->appsession("template_newdesign_$location", 'phpgwapi', $data);
		}
	}
