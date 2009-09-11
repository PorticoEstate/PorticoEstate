<?php
	/**
	* phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
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

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_template_class' => true,
		'login'                  => true,
		'currentapp'             => 'login',
		'noheader'               => true
	);

	include('../header.inc.php');

	$domain = 'default';
	$location = "http://{$_SERVER['HTTP_HOST']}" . parse_url($GLOBALS['phpgw_info']['server']['webserver_url'], PHP_URL_PATH) . "/soap2.php?domain={$domain}";

	$client = new SoapClient(null, array(
		'location'		=> $location,
		'uri'			=> "urn://www.sigurd.testing/soap",
		'soap_version'	=> SOAP_1_2,
		'trace'			=> 1,
		'login'			=> 'anonymous',
		'password'		=> 'anonymous1'
 	));

	$login_data = array
	(
		'domain'	=> 'bbb',
		'username'	=> 'anonymous',
		'password'	=> 'anonymous1'
	);


//	$return = $client->__soapCall("system_login",array($login_data));
//_debug_array($return);

//	$return = $client->__soapCall("displayheaders",array());
//_debug_array($return);


	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'	=> 'soap',
		'noheader'		=> true,
		'noappheader'	=> true,
		'nonavbar'		=> true
	);

//	$return = $client->__soapCall("hello",array("world"));
//_debug_array($return);
//	$return = $client->__soapCall("system_listApps",array());
//_debug_array($return);
//	$return = $client->__soapCall("system_logout",$login_data);
//_debug_array($return);

	$data = array
	(
		'app'	=> 'property',
		'class'	=> 'sotts',
		'method'=> 'read',	
		'input'	=> array('user_id' => $accound_id)
	);

	$return = $client->__soapCall("execute",array($data));
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
