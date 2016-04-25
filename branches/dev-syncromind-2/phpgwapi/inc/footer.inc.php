<?php
	/**
	* Closes out interface and db connections
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage utilities
	* @version $Id$
	*/
	/**************************************************************************\
	* Include the apps footer files if it exists                               *
	\**************************************************************************/

	if ( isset($GLOBALS['phpgw_info']['menuaction']) && $GLOBALS['phpgw_info']['menuaction'])
	{
		list($app,$class,$method) = explode('.',$GLOBALS['phpgw_info']['menuaction']);
		if ( isset($GLOBALS[$class]->public_functions)
			&& is_array($GLOBALS[$class]->public_functions) 
			&& isset($GLOBALS[$class]->public_functions['footer'])
			&& $GLOBALS[$class]->public_functions['footer'] )
		{
			$GLOBALS[$class]->footer();
		}
		elseif(file_exists(PHPGW_APP_INC.'/footer.inc.php'))
		{
			require_once PHPGW_APP_INC . '/footer.inc.php';
		}
	}
	elseif(file_exists(PHPGW_APP_INC.'/footer.inc.php'))
	{
		require_once PHPGW_APP_INC . '/footer.inc.php';
	}

	if (isset($GLOBALS['phpgw_info']['user']['apps']['admin']) && DEBUG_TIMER)
	{
		$debug_timer_stop = perfgetmicrotime();
		echo '<p class="api_timer">' . lang('page prepared in %1 seconds.', $debug_timer_stop - $GLOBALS['debug_timer_start'] ) . "<p>\n";
	}

	if(function_exists('parse_navbar_end'))
	{
		parse_navbar_end();
	}
