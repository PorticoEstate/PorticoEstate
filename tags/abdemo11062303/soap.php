<?php
	/**
	* phpGroupWare
	*
	* @author Miles Lott <milosch@phpgroupware.org>
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2000-2009 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package soap
	* @subpackage communication
 	* @version $Id$
	*/


	$GLOBALS['phpgw_info'] = array();

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_Template_class'	=> true,
		'currentapp'				=> 'login',
		'noheader'					=> true,
		'noapi'						=> true		// this stops header.inc.php to include phpgwapi/inc/function.inc.php
	);

	/**
	* Include phpgroupware header
	*/

	include_once('header.inc.php');

	unset($GLOBALS['phpgw_info']['flags']['noapi']);
	$GLOBALS['phpgw_info']['flags']['authed'] = false;
	$GLOBALS['phpgw_info']['message']['errors'] = array();

	if(!isset($_GET['domain']) || !$_GET['domain'])
	{
		$GLOBALS['phpgw_info']['message']['errors'][] = 'domain not given as input';
	}
	else
	{
		$_REQUEST['domain'] = $_GET['domain'];
		$_domain_info = isset($GLOBALS['phpgw_domain'][$_GET['domain']]) ? $GLOBALS['phpgw_domain'][$_GET['domain']] : '';
		if(!$_domain_info)
		{
			$GLOBALS['phpgw_info']['message']['errors'][] = 'not a valid domain';
		}
		else
		{
			$GLOBALS['phpgw_domain'] = array();
			$GLOBALS['phpgw_domain'][$_GET['domain']] = $_domain_info;
		}
	}

	include(PHPGW_API_INC.'/functions.inc.php');

	$headers = getallheaders();
	if(ereg('Basic',$headers['Authorization']))
	{
		$tmp = $headers['Authorization'];
		$tmp = str_replace(' ','',$tmp);
		$tmp = str_replace('Basic','',$tmp);
		$auth = base64_decode(trim($tmp));
		list($login,$password) = split(':',$auth);

		if($GLOBALS['phpgw']->session->create($login, $password))
		{
			$GLOBALS['phpgw_info']['flags']['authed'] = true;
		}
		else
		{
			$GLOBALS['phpgw_info']['message']['errors'][] = 'not authenticated';
		}
	}

	/**
	* @global object $GLOBALS['server']
	*/
	
	$wdsl = null;
	$options = array
	(
		'uri'          => "http://test-uri/", # the name space of the SOAP service
		'soap_version' => SOAP_1_2,
	//	'actor'        => "...", # the actor
		'encoding'     => "UTF-8", # the encoding name
	//	'classmap'     => "...", # a map of WSDL types to PHP classes
	);

	$GLOBALS['server'] = new SoapServer($wdsl, $options);


	$functions = array();
	/**
	* Include SOAP specific functions - Sigurd: Think these are obsolete.
	*/
//	include_once(PHPGW_API_INC . '/soap_functions.inc.php');
//	$functions = array('system_login', 'system_logout');

	if(function_exists('system_list_apps'))
	{
		$functions[] = 'system_list_apps';
	}

	function hello($someone)
	{
		return "Hello " . $someone . " ! - SOAP 1.2";
	} 

	$functions[] = 'hello';

/*
	function displayheaders($data)
	{
		return getallheaders();
	} 

	$functions[] = 'displayheaders';

*/
	function execute($data)
	{
		if( isset($GLOBALS['phpgw_info']['message']['errors']) && $GLOBALS['phpgw_info']['message']['errors'] )
		{
    		$error = 'Error(s): ' . implode(' ## AND ## ', $GLOBALS['phpgw_info']['message']['errors']);
    		return new SoapFault("phpgw", $error);
		}

		//to be sure...
		if( !$GLOBALS['phpgw_info']['flags']['authed'] )
		{
    		return new SoapFault("phpgw", 'not authenticated');
		}

		$GLOBALS['phpgw_info']['flags'] = array
		(
			'disable_template_class' => true,
			'login'                  => true,
			'currentapp'             => 'login',
			'noheader'               => true
		);

		$invalid_data = false;
		if (! $data['app'] || ! $data['class'] || ! $data['method'])
		{
			$invalid_data = true;
		}

		$obj = CreateObject("{$data['app']}.{$data['class']}");

		if ( !$invalid_data 
			&& is_object($obj)
			&& isset($obj->soap_functions) 
			&& is_array($obj->soap_functions) 
			&& isset($obj->soap_functions[$data['method']])
			&& $obj->soap_functions[$data['method']])
		{
			return $obj->$data['method']($data['input']);
		}
		else
		{
			return 'The method has to be "soap_enabled" - that is - ACL-check for righst for remote users at this particular method';
		}
	} 

	$functions[] = 'execute';

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

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$GLOBALS['server']->handle($request_xml);
	}
	else
	{
		echo "This SOAP server can handle following functions: ";

		_debug_array($functions = $GLOBALS['server']->getFunctions());

	}
	$GLOBALS['phpgw']->common->phpgw_exit();
?>
