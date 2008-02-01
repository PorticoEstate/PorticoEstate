<?php
	/**
	* Session management - Native php handler
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Ralf Becker <ralfbecker@outdoor-training.de>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage sessions
	* @version $Id$
	*/

	/**
	* Track the name of the php session variable used in GPC for non cookie based sessions
	*/
	define('PHPGW_PHPSESSID', ini_get('session.name'));

	/**
	* Session management - Native php handler
	* 
	* @package phpgwapi
	* @subpackage sessions
	*/
	class sessions_php extends sessions
	{
		function sessions_php()
		{
			//Call parent constructor
			parent::__construct();
			
			//respect the config option for cookies
			ini_set('session.use_cookies',!!@$GLOBALS['phpgw_info']['server']['usecookies']);
			//don't rewrite URL, as we have to do it in link - why? cos it is buggy otherwise
			ini_set('url_rewriter.tags', '');
			
			//controls the time out for php sessions
			ini_set('session.gc_maxlifetime', $GLOBALS['phpgw_info']['server']['sessions_timeout']);
		}
		
		function read_session($sessionid)
		{
			if($sessionid)
			{
				session_id($sessionid);
			}
			session_start();
			if ( isset($_SESSION['phpgw_session']) && is_array($_SESSION['phpgw_session']) )
			{
				$GLOBALS['phpgw_session'] = $_SESSION['phpgw_session'];
				return $GLOBALS['phpgw_session'];
			}
			return array();
		}

		/**
		* Set the paramaters for the cookie
		*
		* @param string $domain the domain for the cookie
		*/
		function set_cookie_params($domain)
		{
			session_set_cookie_params(0,'/',$domain);
		}

		function register_session($login,$user_ip,$now,$session_flags)
		{
			if(isset($this->sessionid))
			{
				session_id($this->sessionid);
			}

			if ( !strlen(session_id() ) )
			{
				session_start();
			}

			$GLOBALS['phpgw_session']['session_id'] = $this->sessionid;
			$GLOBALS['phpgw_session']['session_lid'] = $login;
			$GLOBALS['phpgw_session']['session_ip'] = $user_ip;
			$GLOBALS['phpgw_session']['session_logintime'] = $now;
			$GLOBALS['phpgw_session']['session_dla'] = $now;
			$GLOBALS['phpgw_session']['session_action'] = $_SERVER['PHP_SELF'];
			$GLOBALS['phpgw_session']['session_flags'] = $session_flags;
			// we need the install-id to differ between serveral installs shareing one tmp-dir
			$GLOBALS['phpgw_session']['session_install_id'] = $GLOBALS['phpgw_info']['server']['install_id'];

			session_register('phpgw_session');
			$_SESSION['phpgw_session'] = $GLOBALS['phpgw_session'];
		}

		// This will update the DateLastActive column, so the login does not expire
		function update_dla()
		{
			if (@isset($GLOBALS['phpgw_info']['menuaction']))
			{
				$action = $GLOBALS['phpgw_info']['menuaction'];
			}
			else
			{
				$action = $_SERVER['PHP_SELF'];
			}

			$GLOBALS['phpgw_session']['session_dla'] = time();
			$GLOBALS['phpgw_session']['session_action'] = $action;
		
			session_register('phpgw_session');
			$_SESSION['phpgw_session'] = $GLOBALS['phpgw_session'];

			return True;
		}

		function destroy($sessionid, $kp3)
		{
			if (! $sessionid && $kp3)
			{
				return False;
			}

			$this->log_access($this->sessionid);	// log logout-time

			// Only do the following, if where working with the current user
			if ($sessionid == $GLOBALS['phpgw_info']['user']['sessionid'])
			{
				$this->clean_sessions();
				session_unset();
				session_destroy();
				$this->phpgw_setcookie(session_name());
			}
			else
			{
				$sessions = $this->list_sessions(0,'','',True);
				
				if (isset($sessions[$sessionid]))
				{
					//echo "<p>session_php4::destroy($session_id): unlink('".$sessions[$sessionid]['php_session_file'].")</p>\n";
					unlink($sessions[$sessionid]['php_session_file']);
				}
			}

			return True;
		}

		/*************************************************************************\
		* Functions for appsession data and session cache                         *
		\*************************************************************************/
		function delete_cache($accountid='')
		{
			$account_id = get_account_id($accountid,$this->account_id);

			$GLOBALS['phpgw_session']['phpgw_app_sessions']['phpgwapi']['phpgw_info_cache'] = '';
	
			session_register('phpgw_session');
			$_SESSION['phpgw_session'] = $GLOBALS['phpgw_session'];
		}

		function appsession($location = 'default', $appname = '', $data = '##NOTHING##')
		{
			if (! $appname)
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			
			/* This allows the user to put '' as the value. */
			if ($data == '##NOTHING##')
			{
				
				if ( isset($GLOBALS['phpgw_session']['phpgw_app_sessions'][$appname][$location]['content']) )
				{
					// I added these into seperate steps for easier debugging
					$data = $GLOBALS['phpgw_session']['phpgw_app_sessions'][$appname][$location]['content'];

					/* do not decrypt and return if no data (decrypt returning garbage) */
					if($data)
					{
						$data = $GLOBALS['phpgw']->crypto->decrypt($data);
						//echo "appsession returning: location='$location',app='$appname',data=$data"; _debug_array($data);
						return $data;
					}
				}
				return '';
			}
			else
			{
				$encrypteddata = $GLOBALS['phpgw']->crypto->encrypt($data);
				$GLOBALS['phpgw_session']['phpgw_app_sessions'][$appname][$location]['content'] = $encrypteddata;
				session_register('phpgw_session');
				$_SESSION['phpgw_session'] = $GLOBALS['phpgw_session'];
				return $data;
			}
		}

		function session_sort($a,$b)
		{
			$sign = strcasecmp($GLOBALS['phpgw']->session->sort_order,'ASC') ? 1 : -1;

			return strcasecmp($a[$GLOBALS['phpgw']->session->sort_by],
							  $b[$GLOBALS['phpgw']->session->sort_by]) * $sign;
		}
		
		/**
		 * get list of normal / non-anonymous sessions
		*
		 * The data form the session-files get cached in the app_session phpgwapi/php4_session_cache
		 * @author ralfbecker
		 */
		function list_sessions($start,$order,$sort,$all_no_sort = False)
		{
			//echo "<p>session_php4::list_sessions($start,'$order','$sort',$all)</p>\n";
			$session_cache = $this->appsession('php4_session_cache','phpgwapi');

			$values = array();
			$maxmatchs = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$dir = @opendir($path = ini_get('session.save_path'));
			while ($dir && $file = readdir($dir))
			{
				if (substr($file,0,5) != 'sess_')
				{
					continue;
				}
				if (isset($session_cache[$file]))	// use copy from cache
				{
					$session = $session_cache[$file];

					if ($session['session_flags'] == 'A' || !$session['session_id'] ||
						$session['session_install_id'] != $GLOBALS['phpgw_info']['server']['install_id'])
					{
						continue;	// no anonymous sessions or other domains or installations
					}
					if (!$all_no_sort)	// we need the up-to-date data --> unset and reread it
					{
						unset($session_cache[$file]);
					}
				}
				if ( !isset($session_cache[$file]) && is_readable($file) )	// not in cache, read and cache it
				{
					$fd = fopen ($path . '/' . $file,'r');
					$fs = filesize ($path . '/' . $file);
					
					// handle filesize 0 because php recently warns if fread is used on 0byte files 
					if ($fs > 0)
					{
						$session = fread ($fd, filesize ($path . '/' . $file));
					}
					else
					{
						$session = '';
					}
					fclose ($fd);

					if (substr($session,0,14) != 'phpgw_session|')
					{
						continue;
					}
					$session = unserialize(substr($session,14));
					unset($session['phpgw_app_sessions']);	// not needed, saves memory
					$session_cache[$file] = $session;
				}

				if ($session['session_flags'] == 'A' || !$session['session_id'] ||
					$session['session_install_id'] != $GLOBALS['phpgw_info']['server']['install_id'])
				{
					continue;	// no anonymous sessions or other domains or installations
				}
				//echo "file='$file'=<pre>"; print_r($session); echo "</pre>"; 
				
				$session['php_session_file'] = $path . '/' . $file;
				$values[$session['session_id']] = $session;
			}
			@closedir($dir);
			
			if (!$all_no_sort)
			{
				$GLOBALS['phpgw']->session->sort_by = $sort;
				$GLOBALS['phpgw']->session->sort_order = $order;
			
				uasort($values,array($this,'session_sort'));
				
				$i = 0;
				$start = intval($start);
				foreach($values as $id => $data)
				{
					if ($i < $start || $i > $start+$maxmatchs)
					{
						unset($values[$id]);
					}
					++$i;
				}
				reset($values);
			}
			$this->appsession('php4_session_cache','phpgwapi',$session_cache);

			return $values;
		}
		
		/**
		 * get number of normal / non-anonymous sessions
		*
		 * @author ralfbecker
		 */
		function total()
		{
			return count($this->list_sessions(0,'','',True));
		}

		/**
		* Get the list of session variables used for non cookie based sessions
		*
		* @access private
		* @return array the variables which are specific to this session type
		*/
		function _get_session_vars()
		{
			return array
			(
				'sessionid'		=> $this->sessionid,
				'kp3'			=> $this->kp3,
				'domain'		=> $this->account_domain
			);
		}

		/**
		* Remove stale sessions out of the database
		*
		* @internal this does nothing as PHP handles this internally
		*/
		public function clean_sessions()
		{}
	}
