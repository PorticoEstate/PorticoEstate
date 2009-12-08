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

	$headers = getallheaders();
	if(isset($headers['Authorization']) && ereg('Basic',$headers['Authorization']))
	{
		$tmp = $headers['Authorization'];
		$tmp = str_replace(' ','',$tmp);
		$tmp = str_replace('Basic','',$tmp);
		$auth = base64_decode(trim($tmp));
		list($userId,$password) = split(':',$auth);
	}

	require_once PHPGW_SERVER_ROOT.'/bookingfrontend/inc/custom/default/BrukerService.php';

	$options = array();
	$options['soap_version'] = SOAP_1_1;
	$options['location']	= 'http://soat1a.srv.bergenkom.no:8888/gateway/services/BrukerService-v1';
	$options['uri']			= 'http://soat1a.srv.bergenkom.no';
	$options['trace']		= 1;
	$options['proxy_host']	= '';
	$options['proxy_port']	= '';
	$options['encoding']	= 'UTF-8';
	$options['login']		= 'portal';
	$options['password']	= 'ocean9';

	$wsdl = 'http://soat1a.srv.bergenkom.no:8888/gateway/services/BrukerService-v1?wsdl';

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

		$ctx->Appid = 'portico';
		$ctx->onBehalfOfId= $userId;
		$ctx->userId = $userId;
		$ctx->Transactionid = $GLOBALS['phpgw_info']['server']['install_id']; // KAN UTELATES. BENYTTES I.F.M SUPPORT. LEGG INN EN FOR DEG UNIK ID.

		$Bruker = $BrukerService->retrieveBruker($ctx);
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
