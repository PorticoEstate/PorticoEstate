<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Miles Lott <milosch@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id: soap.php 17801 2006-12-28 04:55:14Z skwashd $
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
	$GLOBALS['server'] = CreateObject('phpgwapi.soap_server');

	/* _debug_array($GLOBALS['server']);exit; */
	/* include(PHPGW_API_INC . '/soaplib.soapinterop.php'); */

	$headers = getallheaders();

	if(ereg('Basic',$headers['Authorization']))
	{
		//this seems silly to me - why not just use the SERVER vars? skwashd
		$tmp = $headers['Authorization'];
		$tmp = ereg_replace(' ','',$tmp);
		$tmp = ereg_replace('Basic','',$tmp);
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

	$GLOBALS['server']->add_to_map(
		'system_login',
		array('soapstruct'),
		array('soapstruct')
	);
	$GLOBALS['server']->add_to_map(
		'system_logout',
		array('soapstruct'),
		array('soapstruct')
	);

	if(function_exists('system_listapps'))
	{
		$GLOBALS['server']->add_to_map(
			'system_listApps',
			array(),
			array('soapstruct')
		);
	}

	if ( isset($HTTP_RAW_POST_DATA) )
	{
		$request_xml = $HTTP_RAW_POST_DATA;
	}
	else
	{
		$request_xml = implode("\r\n", file('php://input'));
	}

	$GLOBALS['server']->service($request_xml);
?>
