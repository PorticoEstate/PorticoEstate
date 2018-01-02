<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Jonas Borgström jonas.borgstrom@redpill.se
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
	 * @package property
	 * @version $Id: index.php 14959 2016-04-30 21:09:01Z sigurdne $
	 */
	/**
	 * Start page
	 *
	 * This script will check if there is defined a startpage in the users
	 * preferences - and then forward the user to this page
	 */

	include_once('session.php');

	$invalid_data = false;

	if (isset($_GET['menuaction']))
	{
		list($app, $class, $method) = explode('.', $_GET['menuaction']);
		if($app != 'eventplannerfrontend')
		{
			$invalid_data = true;
		}
	}
	else
	{
		$GLOBALS['phpgw']->redirect_link('/eventplannerfrontend/home.php');
	}


	$GLOBALS[$class] = CreateObject("{$app}.{$class}");

	if (!$invalid_data && is_object($GLOBALS[$class]) && isset($GLOBALS[$class]->public_functions) && is_array($GLOBALS[$class]->public_functions) && isset($GLOBALS[$class]->public_functions[$method]) && $GLOBALS[$class]->public_functions[$method])
	{
		if (phpgw::get_var('X-Requested-With', 'string', 'SERVER') == 'XMLHttpRequest'
			// deprecated
			|| phpgw::get_var('phpgw_return_as', 'string', 'GET') == 'json')
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
		}
		unset($app);
		unset($class);
		unset($method);
		unset($invalid_data);
	}
	else
	{
		if (! $app || ! $class || ! $method)
		{
			$GLOBALS['phpgw']->log->message(array(
				'text' => 'W-BadmenuactionVariable, menuaction missing or corrupt: %1',
				'p1'   => $menuaction,
				'line' => __LINE__,
				'file' => __FILE__
			));
		}

		if ( ( !isset($GLOBALS[$class]->public_functions)
			|| !is_array($GLOBALS[$class]->public_functions)
			|| !isset($GLOBALS[$class]->public_functions[$method])
			|| !$GLOBALS[$class]->public_functions[$method] )
			&& $method)
		{
			$GLOBALS['phpgw']->log->message(array(
				'text' => 'W-BadmenuactionVariable, attempted to access private method: %1',
				'p1'   => $method,
				'line' => __LINE__,
				'file' => __FILE__
			));
		}

		$GLOBALS['phpgw']->log->commit();
		phpgw::no_access();

		//$GLOBALS['phpgw']->redirect_link('/eventplannerfrontend/');
	}
	$GLOBALS['phpgw']->common->phpgw_footer();
