<?php
	/**
	* phpGroupWare
	*
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @author Others <unknown>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	$phpgw_info = array();
	if (!file_exists('header.inc.php'))
	{
		Header('Location: setup/index.php');
		exit;
	}

	/**
	* @global string $GLOBALS['sessionid']
	* @internal FIXME this is ugly and probably not needed - skwashd jan08
	*/
	$GLOBALS['sessionid'] = isset($_REQUEST['sessionid'])? $_REQUEST['sessionid'] : '';

	$invalid_data = false;
	// This is the preliminary menuaction driver for the new multi-layered design
	if (isset($_GET['menuaction']) || isset($_POST['menuaction']))
	{
		if(isset($_GET['menuaction']))
		{
			list($app,$class,$method) = explode('.',$_GET['menuaction']);
		}
		else
		{
			list($app,$class,$method) = explode('.',$_POST['menuaction']);
		}
		if (! $app || ! $class || ! $method)
		{
			$invalid_data = true;
		}
	}
	else
	{
	//$phpgw->log->message('W-BadmenuactionVariable, menuaction missing or corrupt: %1',$menuaction);
	//$phpgw->log->commit();

		$app = 'home';
		$invalid_data = true;
	}

	$api_requested = false;
	if ($app == 'phpgwapi')
	{
		$app = 'home';
		$api_requested = true;
	}

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'   => true,
		'currentapp' => $app
	);
	
	/**
	* Include phpgroupware header
	*/
	require_once('header.inc.php');

	if ($app == 'home' && ! $api_requested)
	{
		$GLOBALS['phpgw']->redirect_link('/home.php');
	}

	if ($api_requested)
	{
		$app = 'phpgwapi';
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

			//If debug info is not triggered elsewhere.
			if (isset($GLOBALS['phpgw_info']['user']['apps']['admin']) && DEBUG_TIMER && !phpgwapi_cache::session_get($app,'id_debug'))
			{
				$debug_timer_stop = perfgetmicrotime();
				//BTW: wil not destroy the json output - click on the 'Debug-link' to view message
				_debug_array(lang('page prepared in %1 seconds.', $debug_timer_stop - $GLOBALS['debug_timer_start'] ));
			}

			$GLOBALS['phpgw']->common->phpgw_exit();
		}
		else
		{
			if(phpgw::get_var('phpgw_return_as', 'string', 'GET') =='noframes')
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
				$GLOBALS['phpgw_info']['flags']['headonly']=true;
			}
			$GLOBALS[$class]->$method();
		}
		unset($app);
		unset($class);
		unset($method);
		unset($invalid_data);
		unset($api_requested);
	}
	else
	{
		//FIXME make this handle invalid data better
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

		$GLOBALS['phpgw']->redirect_link('/home.php');
	}
	$GLOBALS['phpgw']->common->phpgw_footer();
