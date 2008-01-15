<?php

	class phpgwapi_template_newdesign
	{
		var $public_functions = array
		(
			'store'			=> True,
			'retrive'		=> True
		);

		function store()
		{
			$location = phpgw::get_var('location');
			$data = phpgw::get_var('data', 'raw');

			if( $location == null )
			{
				header("HTTP/1.0 406 Not Acceptable");
				return "Missing location parameter";
			}

			$json = execMethod('phpgwapi.Services_JSON.decode', $data);

			if( $json == null )
			{
				header("HTTP/1.0 406 Not Acceptable");
				return "Invalid JSON data parameter";
			}

			$GLOBALS['phpgw']->session->appsession($location,'template_newdesign', $json);
			return $json;
		}

		function retrive()
		{
			$location = phpgw::get_var('location');

			if( $location == null )
			{
				header("HTTP/1.0 406 Not Acceptable");
				return "Missing location parameter";
			}

			$data = $GLOBALS['phpgw']->session->appsession($location,'template_newdesign');

			if($data == null)
			{
				header("HTTP/1.0 404 Not Found");
				return "No data found on that location";
			}

			return $data;
		}

		function retrive_local($location)
		{
			$data = $GLOBALS['phpgw']->session->appsession($location,'template_newdesign');
			return execMethod('phpgwapi.Services_JSON.encode', $data);
		}
	}
?>