<?php
	/**
	* Logging helper functions
	* @author Doug Dicks <doug@revelanttech.com>
	* @copyright Copyright (C) 2000,2001 Mark Peters
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*
	* This is just an alternative API to the logging methods in
	* class.errorlog2.inc.php.  They allow you to call the logging methods
	* without having to always use a  $GLOBALS['phpgw']->log-> with all of
	* your logging statements.  For example, instead of
	* $GLOBALS['phpgw']->log->debug(); you can use log_debug();
	* The goal is to make it easier to add logging to your code.
	*/

	/**
	* Log a message at DEBUG level
	*/
	function log_debug()
	{
		if ( isset($GLOBALS['phpgw']->log) )
		{
			$arg_array = func_get_args();
			$GLOBALS['phpgw']->log->log_if_level('D', $GLOBALS['phpgw']->log->make_parms($arg_array));
		}
	}
	
	function log_info()
	{
		if ( isset($GLOBALS['phpgw']->log) )
		{
			$arg_array = func_get_args();
			$GLOBALS['phpgw']->log->log_if_level('I', $GLOBALS['phpgw']->log->make_parms($arg_array));
		}
	}

	function log_warn()
	{
		if ( isset($GLOBALS['phpgw']->log) )
		{
		$arg_array = func_get_args();
		$GLOBALS['phpgw']->log->log_if_level('W', $GLOBALS['phpgw']->log->make_parms($arg_array));
		}
	}

	function log_error()
	{
		if ( isset($GLOBALS['phpgw']->log) )
		{
			$arg_array = func_get_args();
			$GLOBALS['phpgw']->log->log_if_level('E', $GLOBALS['phpgw']->log->make_parms($arg_array));
		}
	}

	function log_fatal()
	{
		if ( isset($GLOBALS['phpgw']->log) )
		{
			$arg_array = func_get_args();
			$GLOBALS['phpgw']->log->log_if_level('F', $GLOBALS['phpgw']->log->make_parms($arg_array));
		}
	}
	
	
	/* For backward compatibility with some of the existing debugging statements */
	function print_debug($message,$var = 'messageonly',$part = 'app', $level = 3)
	{
		if ( $var == 'messageonly' )
		{
			log_debug($message);
		}
		else
		{
			log_debug($message, $var);
		}
	}
	
?>
