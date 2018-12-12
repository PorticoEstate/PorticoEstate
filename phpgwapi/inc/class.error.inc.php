<?php
	/**
	* Event log
	* @author Jerry Westrick <jerry@westrick.com>
	* @copyright Copyright (C) Jerry Westrick <jerry@westrick.com>
	* @copyright Portions Copyright (C) 2003-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*/

	/**
	* Event log
	* 
	* @package phpgwapi
	* @subpackage application
	*/
	class phpgwapi_error
	{
		var $severity = 'E';
		var $code = 'Unknown';
		var $msg  = 'Unknown error';
		var $parms = array();
		var $ismsg = 0;
		var $timestamp;
		var $fname;
		var $line;
		var $app;

		var $public_functions = array();

		/**
		* Translate message into language
		*
		* Uses $this->msg and $this->params
		* @return string Translated message
		*/
		function langmsg()
		{
			return lang($this->msg,$this->parms);
		}

		function __construct($parms)
		{
			if ($parms == '')
			{
				return;
			}
			$etext = $parms['text'];
			$parray = Array();
			for($counter=1;$counter<=10;$counter++)
			{
				$str = 'p_'.$counter;
				if(isset($parms[$str]) && !empty($parms[$str]))
				{
					$parray[$counter] = $parms[$str];
				}
				else
				{
					$str = 'p'.$counter;
					if(isset($parms[$str]) && !empty($parms[$str]))
					{
						$parray[$counter] = $parms[$str];
					}
				}
			}
			$fname = $parms['file'];
			$line  = $parms['line'];
			if (preg_match('/([DIWEF])-([[:alnum:]]*)\, (.*)/i',$etext,$match))
			{
				$this->severity = strtoupper($match[1]);
				$this->code     = $match[2];
				$this->msg      = trim($match[3]);
			}
			else
			{
				$this->msg = trim($etext);
			}

			//@reset($parray);
			//while( list($key,$val) = each( $parray ) )
			foreach($parray as $key => $val)
			{
				$this->msg = preg_replace( "/%$key/", "'".$val."'", $this->msg );
			}
			@reset($parray);

			$this->timestamp = time();
			$this->parms = $parray;
			$this->ismsg = $parms['ismsg'];
			$this->fname = $fname;
			$this->line  = $line;
			$this->app   = $GLOBALS['phpgw_info']['flags']['currentapp'];

			if (!$this->fname or !$this->line)
			{
				$GLOBALS['phpgw']->log->error(array(
					'text'=>'W-PGMERR, Programmer failed to pass __FILE__ and/or __LINE__ in next log message',
					'file'=>__FILE__,'line'=>__LINE__
				));
			}

			$GLOBALS['phpgw']->log->errorstack[] = $this;
			if ($this->severity == 'F')
			{
				// This is it...  Don't return
				// do rollback!
				// Hmmm this only works if UI!!!!
				// What Do we do if it's a SOAP/XML?
				echo "<center>";
				echo "<h1>Fatal Error</h1>";
				echo "<h2>Error Stack</h2>";
				echo $GLOBALS['phpgw']->log->astable();
				echo "</center>";
				// Commit stack to log
				$GLOBALS['phpgw']->log->commit();
				$GLOBALS['phpgw']->common->phpgw_exit(True);
			}
		}
	}
