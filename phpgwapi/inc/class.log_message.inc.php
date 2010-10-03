<?php
	/**
	* Log message
	* @author ?
	* @copyright Copyright (C) ? ?
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*/

	/**
	* Log message
	* 
	* @package phpgwapi
	* @subpackage application
	*/
	class log_message
	{
		/***************************\
		*	Instance Variables...   *
		\***************************/
		var $severity = 'E';
		var $msg  = 'Unknown error';
		var $timestamp;
		var $fname = '';
		var $line = 0;
		var $app = '';

		var $public_functions = array();

		function log_message($parms)
		{
			if ($parms == '')
			{
				return;
			}
			$etext = $parms['text'];
			$parray = Array();
			for($counter=1;$counter<=10;$counter++)
			{
				// This used to support p_1, etc, but it was not used anywhere.
				// More efficient to standardize on one way.
				$str = 'p'.$counter;
				if(isset($parms[$str]) && !empty($parms[$str]))
				{
					$parray[$counter] = $parms[$str];
				}
			}

			// This code is left in for backward compatibility with the 
			// old log code.  Consider it deprecated.
			if ( !isset( $parms['severity']) && eregi('([DIWEF])-([[:alnum:]]*)\, (.*)',$etext,$match))
			{
				$this->severity = strtoupper($match[1]);
				$this->msg      = trim($match[3]);
			}
			else
			{
				$this->severity = $parms['severity'];
				$this->msg = trim($etext);
			}
			
			foreach ( $parray as $key => $val )
			{
				$val = print_r($val, true);
				$this->msg = preg_replace( "/%$key/", "'".$val."'", $this->msg );
			}

			$this->timestamp = time();
			
			if ( isset($parms['line']) ) 
			{
				$this->line  = $parms['line'];
			}
			if ( isset($parms['file']) ) 
			{
				$this->fname = str_replace(PHPGW_SERVER_ROOT, '/path/to/phpgroupware', $parms['file']);
			}			
			if  ( isset( $GLOBALS['phpgw_info']['flags']['currentapp']) ) 
			{
				$this->app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
		}
	}
