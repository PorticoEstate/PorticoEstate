<?php
	/**
	* phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @author Joseph Engo <jengo@phpgroupware.org>
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


	$client = CreateObject('phpgwapi.soap_client');

	
	$arguments = array
	(
		'app'	=> 'property',
		'class'	=> 'sotts',
		'method'=> 'read',	
		'input'	=> array('user_id' => $accound_id)
	);

	$result = $client->call("execute", $arguments);
	_debug_array($result);

//	$return = $client->call("hello",array("world"));
//_debug_array($return);
//	$return = $client->call("system_listApps",array());
//_debug_array($return);
//	$return = $client->call("system_logout",$login_data);
//_debug_array($return);


	echo("<H1>Dumping request headers:</H1></br>"
		.$client->getLastRequestHeaders());

	echo("</br><H1>Dumping request:</H1></br>".$client->getLastRequest());

	echo("</br><H1>Dumping response headers:</H1></br>"
		.$client->getLastResponseHeaders());

	echo("</br><H1>Dumping response:</H1></br>".$client->getLastResponse());

	$GLOBALS['phpgw']->common->phpgw_exit();
?>
