<?php
	$phpgw_info = array();
	
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_template_class' => true,
		'login'                  => true,
		'currentapp'             => 'login',
		'noheader'               => true
	);

	$GLOBALS['phpgw_info']['flags']['session_name'] = 'bookingfrontendsession';
	
	$GLOBALS['phpgw_remote_user'] = 'remoteuser';
	if(file_exists('../header.inc.php'))
	{
		include_once('../header.inc.php');
		$GLOBALS['phpgw']->sessions = createObject('phpgwapi.sessions');
	}

	$config		= CreateObject('phpgwapi.config','bookingfrontend');
	$config->read();
	$header_key = isset($config->config_data['header_key']) && $config->config_data['header_key'] ? $config->config_data['header_key'] : 'OSSO-USER-DN';
	$header_regular_expression = isset($config->config_data['header_regular_expression']) && $config->config_data['header_regular_expression'] ? $config->config_data['header_regular_expression'] : '/^cn=(.*),cn=users.*$/';

	$headers = getallheaders();
//  Test data
//	$headers[$header_key] = 'cn=30034502192,cn=users, dc=bergen,dc=kommune,dc=no';

	if(isset($headers[$header_key]) && $headers[$header_key])
	{
		$matches = array();
		preg_match_all($header_regular_expression,$headers[$header_key], $matches);
//		_debug_array($matches);
		$userId = $matches[1][0];
	}

	require_once PHPGW_SERVER_ROOT.'/bookingfrontend/inc/custom/default/BrukerService.php';

	$options = array();
	$options['soap_version'] = SOAP_1_1;
	$options['location']	= isset($config->config_data['soap_location']) && $config->config_data['soap_location'] ? $config->config_data['soap_location'] : '';// 'http://soat1a.srv.bergenkom.no:8888/gateway/services/BrukerService-v1';
	$options['uri']			= isset($config->config_data['soap_uri']) && $config->config_data['soap_uri'] ? $config->config_data['soap_uri'] : '';// 'http://soat1a.srv.bergenkom.no';
	$options['trace']		= 1;
	$options['proxy_host']	= isset($config->config_data['soap_proxy_host']) && $config->config_data['soap_proxy_host'] ? $config->config_data['soap_proxy_host'] : '';// '';
	$options['proxy_port']	= isset($config->config_data['soap_proxy_port']) && $config->config_data['soap_proxy_port'] ? $config->config_data['soap_proxy_port'] : '';// '';
	$options['encoding']	= isset($config->config_data['soap_encoding']) && $config->config_data['soap_encoding'] ? $config->config_data['soap_encoding'] : '';// 'UTF-8';
	$options['login']		= isset($config->config_data['soap_login']) && $config->config_data['soap_login'] ? $config->config_data['soap_login'] : '';// 'portal';
	$options['password']	= isset($config->config_data['soap_password']) && $config->config_data['soap_password'] ? $config->config_data['soap_password'] : '';// 'ocean9';

	$wsdl = isset($config->config_data['soap_wsdl']) && $config->config_data['soap_wsdl'] ? $config->config_data['soap_wsdl'] : '';// 'http://soat1a.srv.bergenkom.no:8888/gateway/services/BrukerService-v1?wsdl';

	try
	{
		$BrukerService	= new BrukerService($wsdl, $options);
	}
	catch(Exception $e)
	{
		echo $e->getMessage();
	}
	$login = '';
	if(isset($BrukerService) && $BrukerService)
	{
		$ctx			= new UserContext();

		$ctx->appid = 'portico';
		$ctx->onBehalfOfId= $userId;
		$ctx->userid = $userId;
		$ctx->transactionid = $GLOBALS['phpgw_info']['server']['install_id']; // KAN UTELATES. BENYTTES I.F.M SUPPORT. LEGG INN EN FOR DEG UNIK ID.

		$request = new retrieveBruker();
		$request->userContext = $ctx;
		$request->userid = $userId;

		$response = $BrukerService->retrieveBruker($request);
		$Bruker = $response->return;
		$login = $Bruker->ou; // organisasjons nr
	}

	$_SERVER['REMOTE_USER'] = $login;

	$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, $passwd);		

	$GLOBALS['phpgw']->hooks->process('login');

	$bouser = CreateObject('bookingfrontend.bouser');
	$bouser->log_in($login);

	$after = str_replace('&amp;', '&', urldecode(phpgw::get_var('after', 'string')));
	if(!$after)
	{
		$after = array('menuaction' => 'bookingfrontend.uisearch.index');
	}
	$GLOBALS['phpgw']->redirect_link('/bookingfrontend/index.php', $after);
	exit;
