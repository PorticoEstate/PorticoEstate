<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @package property
 	* @version $Id: index.php 977 2008-05-11 08:38:25Z sigurd $
	*/

	/**
	 * Start page
	 *
	 * This script will check if there is defined a startpage in the users
	 * preferences - and then forward the user to this page
	 */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'		=> true,
		'nonavbar'		=> true,
		'currentapp'	=> 'bookingfrontend'
	);

	$GLOBALS['phpgw_info']['flags']['session_name'] = 'bookingfrontendsession';
	include_once('../header.inc.php');

	if (isset($_GET['menuaction']))
	{
		list($app,$class,$method) = explode('.',$_GET['menuaction']);
	}
	else
	{
		$app = 'bookingfrontend';
		$class = 'uisearch';
		$method = 'index';
	}

	$GLOBALS[$class] = CreateObject("{$app}.{$class}");


	if ( !$invalid_data 
		&& is_object($GLOBALS[$class])
		&& isset($GLOBALS[$class]->public_functions) 
		&& is_array($GLOBALS[$class]->public_functions) 
		&& isset($GLOBALS[$class]->public_functions[$method])
		&& $GLOBALS[$class]->public_functions[$method] )

	{
		if ( phpgw::get_var('X-Requested-With', 'string', 'SERVER') == 'XMLHttpRequest'
			 // deprecated
			|| phpgw::get_var('phpgw_return_as', 'string', 'GET') == 'json' )
		{
			// comply with RFC 4627
			header('Content-Type: application/json'); 
			$return_data = $GLOBALS[$class]->$method();
			echo json_encode($return_data);
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
		else
		{
			$GLOBALS[$class]->$method();	
			$GLOBALS['phpgw']->common->phpgw_footer();
		}
	}