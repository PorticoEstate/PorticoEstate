<?php
	/**
	* Log
	* @author ?
	* @copyright Copyright (C) ?
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*/

	phpgw::import_class('phpgwapi.datetime');
	/**
	* Log
	*
	* @package phpgwapi
	* @subpackage application
	*/
	class phpgwapi_log
	{

		/***************************\
		*	Instance Variables...   *
		\***************************/
		var $errorstack = array();
		var $public_functions = array
		(
			'debug',
			'info',
			'notice',
			'strict',
			'warn',
			'error',
			'fatal',
			'iserror',
			// the following 3 are for backwards compatibility only
			'write',
			'message',
			'commit'
		);

		var $log_level_table = array
		(
			'F' => 1,
			'E' => 2,
			'W' => 3,
			'N'	=> 4,
			'I' => 5,
			'D' => 6,
			'S'	=> 7,
			'DP'=>8
		);

		// these are used by the admin appliation when showing the log file.

		var $log_level_names = array
		(
			'F' => 'fatal',
			'E' => 'error',
			'W' => 'warn',
			'N'	=> 'notice',
			'I' => 'info',
			'D' => 'debug',
			'S'	=> 'strict',
			'DP'=> 'deprecated'
		);

		/**
		 * Constructor
		 */

		public function __construct()
		{
		}


		function checkprefs()
		{
			//validate defaults
			if (!isset($GLOBALS['phpgw_info']['server']['log_levels']))
			{
				$GLOBALS['phpgw_info']['server']['log_levels']['global_level'] = 'E';
				$GLOBALS['phpgw_info']['server']['log_levels']['module'] = array();
				$GLOBALS['phpgw_info']['server']['log_levels']['user'] = array();
			}
		}

		function get_level_name($level)
		{
			return $this->log_level_names[$level];
		}

		function is_level($level)
		{
			$this->checkprefs();
			if ( $this->log_level_table[$GLOBALS['phpgw_info']['server']['log_levels']['global_level']] >= $this->log_level_table[$level] )
			{
				return true;
			}

			if ( isset($GLOBALS['phpgw_info']['flags']['currentapp'])
				 && @array_key_exists( $GLOBALS['phpgw_info']['flags']['currentapp'] , $GLOBALS['phpgw_info']['server']['log_levels']['module'])
				 && $this->log_level_table[$GLOBALS['phpgw_info']['server']['log_levels']['module'][$GLOBALS['phpgw_info']['flags']['currentapp']]] >= $this->log_level_table[$level] )
			{
					return true;
			}

			if ( isset($GLOBALS['phpgw_info']['user']['account_lid'])
				 && @array_key_exists($GLOBALS['phpgw_info']['user']['account_lid'], $GLOBALS['phpgw_info']['server']['log_levels']['user'])
				 && $this->log_level_table[$GLOBALS['phpgw_info']['server']['log_levels']['user'][$GLOBALS['phpgw_info']['user']['account_lid']]] >= $this->log_level_table[$level] )
			{
				return true;
			}

			return false;
		}


		function log_if_level($level, $parms)
		{
			if ( $this->is_level($level) )
			{
				$parms['severity'] = $level;
				$err = createObject('phpgwapi.log_message',$parms);
				$this->write_error_to_db($err);
 				$this->handle_fatal_error($err);              // this is here instead of in fatal() because I still support
 				                                              // the old methods.
 				return true;
			}
			else
			{
				return false;
			}
		}

		function make_parms($arg_array)
		{
			if ( count($arg_array) == 0 )
			{
				$parms['text'] = 'No message passed to logging function!';
			}
			else
			{
				// if they've passed in an array of parms,
				// just return it.
				if ( is_array($arg_array[0])  )
				{
					return $arg_array[0];
				}
				else
				{
					// otherwise the first arg is the message text and the rest are
					// parameters to that message
					list($k, $v) = each($arg_array);
					$parms['text'] = $v;
					while ( list($k, $v) = each($arg_array) )
					{
						$parms['p'.$k] = $v;
					}
				}
			}
			return $parms;
		}

		function debug()
		{
			$arg_array = func_get_args();
			return $this->log_if_level('D', $this->make_parms($arg_array));
		}

		function info()
		{
			$arg_array = func_get_args();
			return $this->log_if_level('I',  $this->make_parms($arg_array));
		}

		function notice()
		{
			$arg_array = func_get_args();
			return $this->log_if_level('N',  $this->make_parms($arg_array));
		}

		function strict()
		{
			$arg_array = func_get_args();
			return $this->log_if_level('S',  $this->make_parms($arg_array));
		}

		function deprecated()
		{
			$arg_array = func_get_args();
			return $this->log_if_level('DP',  $this->make_parms($arg_array));
		}

		function warn()
		{
			$arg_array = func_get_args();
			return $this->log_if_level('W',  $this->make_parms($arg_array));
		}

		function error()
		{
			$arg_array = func_get_args();
			return $this->log_if_level('E',  $this->make_parms($arg_array));
		}

		function fatal()
		{
			$arg_array = func_get_args();
			return $this->log_if_level('F',  $this->make_parms($arg_array));
		}

		function write_error_to_db($err)
		{
			if ( isset($GLOBALS['phpgw']->db)
				&& is_object($GLOBALS['phpgw']->db))
			{
				$db =& $GLOBALS['phpgw']->db;
			}
			else if ( isset($GLOBALS['phpgw_setup']->oProc->m_odb)
				&& is_object($GLOBALS['phpgw_setup']->oProc->m_odb) ) // during setup
			{
				$db =& $GLOBALS['phpgw_setup']->oProc->m_odb;
				if(!$db->metadata('phpgw_log'))
				{
					echo 'Failed to log error to database.';
					return;
				}
			}
			else
			{
				//trigger_error("Failed to log error to database: no database object available");
				return;
			}

			$values = array
			(
				date($db->datetime_format()),
				$db->db_addslashes($GLOBALS['phpgw_info']['flags']['currentapp']),
				isset($GLOBALS['phpgw_info']['user']['id']) && $GLOBALS['phpgw_info']['user']['id'] ? $GLOBALS['phpgw_info']['user']['id'] : -1,
				$db->db_addslashes(isset($GLOBALS['phpgw_info']['user']['lid']) && $GLOBALS['phpgw_info']['user']['lid'] ? $GLOBALS['phpgw_info']['user']['lid'] : 'not authenticated'),
				$err->severity,
				$err->fname ? $db->db_addslashes($err->fname) : 'dummy',
				(int)$err->line,
				$db->db_addslashes($err->msg),
			);

			$values	= $db->validate_insert($values);

			$db->query("insert into phpgw_log (log_date, log_app, log_account_id, log_account_lid, log_severity,"
					 . "log_file, log_line, log_msg) values ({$values})",__LINE__,__FILE__);
			if ( isset($db->Errno) )
			{
				//trigger_error("Failed to log error to database. DB errno " . $db->Errno . ": message " . $db->Error,  E_USER_NOTICE);
			}
		}

		// I pulled this from the old code, where it's used to display a fatal error and determinate processing..
		// Do I still want to do this?  If so, do I want to translate the error message like it used to?
		//
		function handle_fatal_error($err)
		{
			if ($err->severity == 'F')
			{
				$trace = '<p>' . lang('Please report this incident to your system administrator') . "</p>\n";
				$msg = $err->msg;
				if ( strpos($err->msg, "\n") )
				{
					$msg_array = explode("\n", $err->msg);
					$msg = $msg_array[0];
					unset($msg_array[0]);
					if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
					{
						$trace = '<h2>' .lang('back trace') . "</h2>\n"
									. '<p>' . lang('Please include the following output when you report this incident on our bug tracker - %1',
											'<a href="https://savannah.nongnu.org/bugs/?group=fmsystem" target="_blank">https://savannah.nongnu.org/bugs/?group=fmsystem</a>') . "</p>\n"
									. '<pre>' . implode("\n", $msg_array) . '</pre>';
					}
				}


				$message = '<h1>' . lang('Fatal Error') . "</h1>\n"
					. "<h2>{$msg}</h2>\n"
					. '<p>' . lang('file') . ': ' . $err->fname . "<br>\n"
					. lang('line') . ': ' . $err->line . "</p>\n"
					. $trace;


				if(phpgw::get_var('phpgw_return_as') == 'json')
				{
					echo json_encode($message);
					$call_footer = false;
				}
				else
				{
					echo $message;
					$call_footer = true;
				}
		//		phpgwapi_cache::message_set($message, 'error');

				if ( isset($GLOBALS['phpgw']->common) && is_object($GLOBALS['phpgw']->common) )
				{
					$GLOBALS['phpgw']->common->phpgw_exit($call_footer);
				}
			}
		}


		// write() left in for backward compatibility
		function write($parms)
		{
			$err = createObject('phpgwapi.log_message',$parms);
			$this->write_error_to_db($err);
			return true;
		}
	   // message() left in for backward compatibility
		function message($parms)
		{
			$err = createObject('phpgwapi.log_message',$parms);
			$this->write_error_to_db($err);
			return true;
		}

	   // commit() left in for backward compatibility
		function commit()
		{
			return true;
		}

	   // clearstack() left in for backward compatibility
		function clearstack()
		{
			return true;
		}
	}
