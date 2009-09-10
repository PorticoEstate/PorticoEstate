<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Miles Lott <milosch@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id$
	*/


	$GLOBALS['phpgw_info'] = array();

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_Template_class'	=> true,
		'currentapp'				=> 'login',
		'noheader'					=> true
	);

	/**
	* Include phpgroupware header
	*/
	include_once('header.inc.php');

	/**
	* Include the SOAP specific functions
	*/
	include_once(PHPGW_API_INC . '/soap_functions.inc.php');

	/**
	* @global object $GLOBALS['server']
	*/
	$GLOBALS['server'] = new SoapServer(null, array('uri' => "http://test-uri/"));

	//_debug_array($GLOBALS['server']);exit;
	//include(PHPGW_API_INC . '/soaplib.soapinterop.php');

	$headers = getallheaders();

	if(ereg('Basic',$headers['Authorization']))
	{
		$tmp = $headers['Authorization'];
		$tmp = str_replace(' ','',$tmp);
		$tmp = str_replace('Basic','',$tmp);
		$auth = base64_decode(trim($tmp));
		list($sessionid,$kp3) = split(':',$auth);

		if($GLOBALS['phpgw']->session->verify($sessionid,$kp3))
		{
			$GLOBALS['server']->authed = True;
		}
		elseif($GLOBALS['phpgw']->session->verify_server($sessionid,$kp3))
		{
			$GLOBALS['server']->authed = True;
		}
	}

	$functions = array('system_login', 'system_logout');
	if(function_exists('system_listapps'))
	{
		$functions[] = 'system_listApps';
	}

	$GLOBALS['server']->addFunction($functions);
//	$GLOBALS['server']->addFunction(SOAP_FUNCTIONS_ALL);



	if ( isset($HTTP_RAW_POST_DATA) )
	{
		$request_xml = $HTTP_RAW_POST_DATA;
	}
	else
	{
		$request_xml = implode(" ", file('php://input'));
	}
//	$GLOBALS['server']->handle($request_xml);
	
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$GLOBALS['server']->handle();
	}
	else
	{
		echo "This SOAP server can handle following functions: ";
		$functions = $GLOBALS['server']->getFunctions();
		foreach($functions as $func)
		{
			echo $func . "\n";
		}
	}

	
?>
