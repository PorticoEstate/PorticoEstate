<?php
/**************************************************************************\
* phpGroupWare - XML-RPC Test App                                          *
* http://www.phpgroupware.org                                              *
* --------------------------------------------                             *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

/* $Id$ */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_template_class' => true,
		'login'                  => true,
		'currentapp'             => 'login',
		'noheader'               => true
	);

	include('../header.inc.php');

	$location = "http://{$_SERVER['HTTP_HOST']}" . parse_url($GLOBALS['phpgw_info']['server']['webserver_url'], PHP_URL_PATH) . '/soap2.php';

	$client = new SoapClient(null, array(
		'location'		=> $location,
		'uri'			=> "urn://www.sigurd.testing/soap",
		'soap_version'	=> SOAP_1_2,
		'trace'			=> 1,
		'login'			=> 'mylogin',
		'password'		=> 'secret'
 	));

	$login_data = array
	(
		'domain'	=> 'default',
		'username'	=> 'anonymous',
		'password'	=> 'anonymous1'
	);


	$return = $client->__soapCall("system_login",array($login_data));
_debug_array($return);

	$return = $client->__soapCall("displayheaders",array());
_debug_array($return);


	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'	=> 'soap',
		'noheader'		=> True,
		'noappheader'	=> True,
		'nonavbar'		=> True
	);

	$return = $client->__soapCall("hello",array("world"));
_debug_array($return);
	$return = $client->__soapCall("system_listApps",array());
_debug_array($return);
	$return = $client->__soapCall("system_logout",$login_data);
_debug_array($return);

/*

	echo("\nReturning value of __soapCall() call: ".$return);

	echo("\nDumping request headers:\n" 
		.$client->__getLastRequestHeaders());

	echo("\nDumping request:\n".$client->__getLastRequest());

	echo("\nDumping response headers:\n"
		.$client->__getLastResponseHeaders());

	echo("\nDumping response:\n".$client->__getLastResponse());
*/
	$GLOBALS['phpgw']->common->phpgw_exit();
?>
