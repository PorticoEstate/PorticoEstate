<?php
	$GLOBALS['phpgw_info']['flags'] = array
		(
		'noheader'	 => true,
		'nonavbar'	 => true,
		'currentapp' => 'property'
	);

	include_once('../header.inc.php');

	function get_status()
	{
		//	$webservicehost = !empty($this->config->config_data['webservicehost']) ? $this->config->config_data['webservicehost'] : '';
		//	$webservicehost = "https://akres.stavanger.kommune.no/api/ui/";
		$webservicehost = "https://akres.stavanger.kommune.no/api/resources";

		$post_data = array
			(
			"resid"	 => 83,
		//	"reserved" => 1,
			"system" => 1,
		);


		$post_string = http_build_query($post_data);

		$url = "{$webservicehost}?{$post_string}";
		_debug_array($url);

//Basic Auth:
		$login		 = 'apiuser';
		$password	 = 'SD0UB02kQQVEPtk1Ar4';


		$proxy = 'proxy.bergen.kommune.no:8080';


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "{$login}:{$password}");
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		if ($proxy)
		{
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		}
//		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json Accept: application/json'));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$result = curl_exec($ch);

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$ret = json_decode($result, true);

		_debug_array($ret);
		if (isset($ret['orgnr']))
		{
			return array($ret);
		}
		else
		{
			return $ret;
		}
	}

	function resources_create()
	{
		//	$webservicehost = !empty($this->config->config_data['webservicehost']) ? $this->config->config_data['webservicehost'] : '';
		//	$webservicehost = "https://akres.stavanger.kommune.no/api/ui/";
		$webservicehost = "https://akres.stavanger.kommune.no/api/resources";

		$post_data = array
			(
			"desc"	 => "Sigurd test",
			"email"	 => "sigurdne@online.no",
			"from"	 => "2019-10-18T13:54:11.394Z",
	//		"from"	 => "2019-10-18 13:54:11",
			"mobile" => "90665164",
			"to"	 => "2019-10-18T13:54:11.394Z",
	//		"to"	 => "2019-10-18 13:54:11",
			"resid"	 => 1721,
			"system" => 1,
		);

		$post_string = json_encode($post_data);

		$url = "{$webservicehost}";

		$login		 = 'apiuser';
		$password	 = 'SD0UB02kQQVEPtk1Ar4';


		$proxy = 'proxy.bergen.kommune.no:8080';


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "{$login}:{$password}");
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		if ($proxy)
		{
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json Accept: application/json'));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);

		$result = curl_exec($ch);

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$ret = json_decode($result, true);

		_debug_array($httpCode);
		_debug_array($ret);
		if (isset($ret['orgnr']))
		{
			return array($ret);
		}
		else
		{
			return $ret;
		}
	}

	function resources_delete()
	{
		//	$webservicehost = !empty($this->config->config_data['webservicehost']) ? $this->config->config_data['webservicehost'] : '';
		//	$webservicehost = "https://akres.stavanger.kommune.no/api/ui/";
		$webservicehost = "https://akres.stavanger.kommune.no/api/resources";

		$post_data = array
			(
			"resid"	 => 1721,
			"system" => 1,
		);


		$post_string = json_encode($post_data);

		$url = "{$webservicehost}";

		$login		 = 'apiuser';
		$password	 = 'SD0UB02kQQVEPtk1Ar4';


		$proxy = 'proxy.bergen.kommune.no:8080';


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "{$login}:{$password}");
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		if ($proxy)
		{
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json Accept: application/json'));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);

		$result = curl_exec($ch);

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$ret = json_decode($result, true);

		_debug_array($httpCode);
		_debug_array($ret);
		if (isset($ret['orgnr']))
		{
			return array($ret);
		}
		else
		{
			return $ret;
		}
	}

	function resources_update()
	{
		//	$webservicehost = !empty($this->config->config_data['webservicehost']) ? $this->config->config_data['webservicehost'] : '';
		//	$webservicehost = "https://akres.stavanger.kommune.no/api/ui/";
		$webservicehost = "https://akres.stavanger.kommune.no/api/resources";

		$post_data = array
		(
			"key"		 => "Sigurd test " . date('Y-m-d H:i'),
			"reserved"	 => 1,
			"resid"		 => 1721,
			"system"	 => 1,
		);

		$post_string = json_encode($post_data);

		$url = "{$webservicehost}";

		$login		 = 'apiuser';
		$password	 = 'SD0UB02kQQVEPtk1Ar4';


		$proxy = 'proxy.bergen.kommune.no:8080';


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "{$login}:{$password}");
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		if ($proxy)
		{
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json Accept: text/html'));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);

		$result = curl_exec($ch);

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$ret = json_decode($result, true);

		_debug_array($httpCode);
		_debug_array($ret);
		if (isset($ret['orgnr']))
		{
			return array($ret);
		}
		else
		{
			return $ret;
		}
	}
//	resources_create();
//	resources_update();
//	resources_delete();
	get_status();
