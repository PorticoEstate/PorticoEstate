<?php
	/**
	* phpGroupWare Session management
	*
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Ralf Becker <ralfbecker@outdoor-training.de>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgroupware
	* @subpackage phpgwapi
	* @version $Id: class.sessions_php.inc.php 682 2008-02-01 12:19:55Z dave $
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU Lesser General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU Lesser General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* Set the session name to something unique for phpgw
	*/
	session_name('phpgwsessid');

	/*
	 * Include the db session handler if required
	 */
	if ( $GLOBALS['phpgw_info']['server']['sessions_type'] == 'db' )
	{
		phpgw::import_class('phpgwapi.session_handler_db');
	}

	phpgw::import_class('phpgwapi.cache');

	/**
	* Session Management
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	* @category sessions
	*/
	class phpgwapi_sessions
	{
		/**
		* @var integer $cd_reason contains the error code when checking sessions
		*/
		public $cd_reason;

		/**
		* @var array $public_functions publicly available methods
		*/
		public $public_functions = array
		(
			'list_methods' => true,
			'update_dla'   => true,
			'list'         => true,
			'total'        => true
		);

		/**
		* @var string $account_domain domain for current user
		*/
		protected $_account_domain;

		/**
		* @var integer $account_id current user db/ldap account id
		*/
		protected $_account_id;
		
		/**
		* @var string $account_lid current user account login id - ie user@domain
		*/
		protected $_account_lid;
	
		/**
		* @var array $data session data
		*/
		protected $_data;
		
		/**
		* @var object $db reference to global database object
		*/
		protected $_db;

		/**
		* @var string $cookie_domain domain for cookies
		*/
		protected $_cookie_domain = null;
		
		/**
		* @var string $history_id previous page call id - repost prevention
		*/
		protected $_history_id;

		/**
		* @var string $iv initialization vector for encryption
		*/
		protected $_iv;
		
		/**
		* @var string $key encryption key
		*/
		protected $_key;
		
		/**
		* @var string $login current user login
		*/
		protected $_login;
		
		/**
		* @var string $passwd current user password
		*/
		protected $_passwd;
		
		/**
		* @var string $session_flags session type flag, A - anonymous session, N - None, normal session
		*/
		protected $_session_flags;
		
		/**
		* @var string $sessionid current user session id
		*/
		protected $_sessionid;
		
		/**
		* Constructor just loads up some defaults from cookies
		*/
		public function __construct()
		{
			$this->_db =& $GLOBALS['phpgw']->db;
			$this->_sessionid = phpgw::get_var(session_name());
			
			$this->_phpgw_set_cookie_params();

			$use_cookies = true;
			if ( !isset($GLOBALS['phpgw_info']['server']['usecookies'])
				|| !$GLOBALS['phpgw_info']['server']['usecookies'] )
			{
				$use_cookies = false;
			}
			//respect the config option for cookies
			ini_set('session.use_cookies', $use_cookies);

			//don't rewrite URL, as we have to do it in link - why? cos it is buggy otherwise
			ini_set('url_rewriter.tags', '');
		}

		/**
		 * Cache data for the user's current session
		 *
		 * @deprecated
		 * @param string $id the unique id within the module for the data
		 * @param string $module the module name that the data is stored for
		 * @param mixed $data the data to store - use ##NOTHING## to retreive data - dodgy oldhack
		 * @return mixed the data - even if storing
		 */
		public function appsession($id = 'default', $appname = '', $data = '##NOTHING##')
		{
			if ( !$appname )
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			
			/* This allows the user to put '' as the value. */
			if ($data == '##NOTHING##')
			{
				return phpgwapi_cache::session_get($appname, $id);
			}
			phpgwapi_cache::session_set($appname, $id, $data);
			return $data;
		}

		/**
		* Clears the appsession cache, should be called before saving preferences or other information which will invalidate the cache
		*/
		public function clear_phpgw_info_cache()
		{
			phpgwapi_cache::session_clear('phpgwapi', 'phpgw_info');
		}

		/**
		* Create a new session
		*
		* @param string $login user login
		* @param string $passwd user password
		* @return string session id
		*/
		public function create($login, $passwd = '', $skip_auth = false)
		{
			phpgw::import_class('phpgwapi.globally_denied');
			
			if (is_array($login))
			{
				$this->_login       = $login['login'];
				$this->_passwd      = $login['passwd'];
				$login             = $this->_login;
			}
			else
			{
				$this->_login       = $login;
				$this->_passwd      = $passwd;
			}

			$now = time();

			$lid_parts = $this->_set_login($login);

			//echo "<p>session::create(login='$login'): lid='$this->_account_lid', domain='$this->_account_domain'</p>\n";
			$user_ip = $this->_get_user_ip();

			if ( $this->_login_blocked($login, $this->_get_user_ip() ) ) 
			{
				$this->reason = 'blocked, too many attempts';
				$this->cd_reason = 99;
				$this->log_access($this->reason,$login,$user_ip,0);	// log unsuccessfull login
				return False;
			}

			if ( phpgwapi_globally_denied::user($this->_account_lid) 
				|| ( !$skip_auth && !$GLOBALS['phpgw']->auth->authenticate($this->_account_lid, $this->_passwd) )
				|| $GLOBALS['phpgw']->accounts->get_type($this->_account_lid) == 'g')
			{
				$this->reason = 'bad login or password';
				$this->cd_reason = 5;
				
				$this->log_access($this->reason,$login,$user_ip,0);	// log unsuccessfull login
				return False;
			}

			if ( !$GLOBALS['phpgw']->accounts->exists($this->_account_lid) 
					&& $GLOBALS['phpgw_info']['server']['auto_create_acct'] )
			{
				$this->_account_id = $GLOBALS['phpgw']->accounts->auto_add($this->_account_lid, $passwd);
			}
			else
			{
				$this->_account_id = $GLOBALS['phpgw']->accounts->name2id($this->_account_lid);
			}
			$GLOBALS['phpgw_info']['user']['account_id'] = $this->_account_id;
			$GLOBALS['phpgw']->accounts->set_account($this->_account_id);
			session_start();
			$this->_sessionid = session_id(); //md5($GLOBALS['phpgw']->common->randomstring(15));

			if ( isset($GLOBALS['phpgw_info']['server']['usecookies'])
				&& $GLOBALS['phpgw_info']['server']['usecookies'] )
			{
				//$this->phpgw_setcookie(session_name(), $this->_sessionid);
				$this->phpgw_setcookie('domain', $this->_account_domain);
			}

			if ( ( isset($GLOBALS['phpgw_info']['server']['usecookies']) && $GLOBALS['phpgw_info']['server']['usecookies'] )
				|| isset($_COOKIE['last_loginid']))
			{ 
				$this->phpgw_setcookie('last_loginid', $this->_account_lid ,$now+1209600); /* For 2 weeks */
				$this->phpgw_setcookie('last_domain',$this->_account_domain,$now+1209600);
			}
			unset($GLOBALS['phpgw_info']['server']['default_domain']); /* we kill this for security reasons */

			/* init the crypto object */
			$this->_key = md5($this->_sessionid . $GLOBALS['phpgw_info']['server']['encryptkey']);
			$this->_iv  = $GLOBALS['phpgw_info']['server']['mcrypt_iv'];
			$GLOBALS['phpgw']->crypto->init(array($this->_key,$this->_iv));

			$this->read_repositories();
			if ( $this->_data['expires'] != -1 && $this->_data['expires'] < time() )
			{
				if(is_object($GLOBALS['phpgw']->log))
				{
					$GLOBALS['phpgw']->log->message(array(
						'text' => 'W-LoginFailure, account loginid %1 is expired',
						'p1'   => $this->_account_lid,
						'line' => __LINE__,
						'file' => __FILE__
					));
					$GLOBALS['phpgw']->log->commit();
				}

				$this->cd_reason = 2;
				return False;
			}

			$GLOBALS['phpgw_info']['user']  = $this->_data;
			$GLOBALS['phpgw_info']['hooks'] = $this->hooks;

			phpgwapi_cache::session_set('phpgwapi', 'password', base64_encode($this->_passwd));
			if ($GLOBALS['phpgw']->acl->check('anonymous',1,'phpgwapi'))
			{
				$session_flags = 'A';
			}
			else
			{
				$session_flags = 'N';
			}

			$GLOBALS['phpgw']->db->transaction_begin();
			$this->register_session($login,$user_ip,$now,$session_flags);
			$this->log_access($this->_sessionid, $login, $user_ip, $this->_account_id);
			$GLOBALS['phpgw']->auth->update_lastlogin($this->_account_id,$user_ip);
			$GLOBALS['phpgw']->db->transaction_commit();
			
			return $this->_sessionid;
		}

		/**
		* Validate a peer server login request
		*
		* @param string $login login name
		* @param string $password password
		* @return bool login ok?
		*/
		public function create_server($login,$passwd)
		{
			$GLOBALS['phpgw']->interserver = createObject('phpgwapi.interserver');
			$this->_login  = $login;
			$this->_passwd = $passwd;
			$login_array = explode('@', $login);
			$this->_account_lid = $login_array[0];
			$now = time();

			if ($login_array[1] != '')
			{
				$this->_account_domain = $login_array[1];
			}
			else
			{
				$this->_account_domain = $GLOBALS['phpgw_info']['server']['default_domain'];
			}

			$serverdata = array(
				'server_name' => $this->_account_domain,
				'username'    => $this->_account_lid,
				'password'    => $passwd
			);
			if (!$GLOBALS['phpgw']->interserver->auth($serverdata))
			{
				return False;
				exit;
			}

			if (!$GLOBALS['phpgw']->interserver->exists($this->_account_lid))
			{
				$this->_account_id = $GLOBALS['phpgw']->interserver->name2id($this->_account_lid);
			}
			$GLOBALS['phpgw_info']['user']['account_id'] = $this->_account_id;
			$GLOBALS['phpgw']->interserver->serverid = $this->_account_id;

			$this->_sessionid = md5($GLOBALS['phpgw']->common->randomstring(10));

			/* re-init the crypto object */
			$this->_key = md5($this->_sessionid . $GLOBALS['phpgw_info']['server']['encryptkey']);
			$this->_iv  = $GLOBALS['phpgw_info']['server']['mcrypt_iv'];
			$GLOBALS['phpgw']->crypto->init(array($this->_key,$this->_iv));

			//$this->read_repositories(False);

			$GLOBALS['phpgw_info']['user']  = $this->_data;
			$GLOBALS['phpgw_info']['hooks'] = $this->hooks;

			$this->appsession('password','phpgwapi',base64_encode($this->_passwd));
			$session_flags = 'S';

			$user_ip = $this->_get_user_ip();

			$GLOBALS['phpgw']->db->transaction_begin();
			$this->register_session($login,$user_ip,$now,$session_flags);

			$this->log_access($this->_sessionid,$login,$user_ip,$this->_account_id);

			$GLOBALS['phpgw']->auth->update_lastlogin($this->_account_id,$user_ip);
			$GLOBALS['phpgw']->db->transaction_commit();

			return array($this->_sessionid);
		}

		public function delete_cache($accountid='')
		{
			phpgwapi_cache::session_clear('phpgwapi', 'phpgw_info');
		}

		public function destroy($sessionid)
		{
			if ( !$sessionid )
			{
				return False;
			}

			$this->log_access($this->_sessionid);	// log logout-time

			// Only do the following, if where working with the current user
			if ($sessionid == $GLOBALS['phpgw_info']['user']['sessionid'])
			{
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

		/**
		* Additional tracking of user actions - prevents reposts/use of back button
		*
		* @author skwashd
		* @return string current history id
		*/
		public function generate_click_history()
		{
			if(!isset($this->_history_id))
			{
				$this->_history_id = md5($this->_login . time());
				$history = $this->appsession('history', 'phpgwapi');
				
				if(count($history) >= $GLOBALS['phpgw_info']['server']['max_history'])
				{
					array_shift($history);
					$this->appsession('history', 'phpgwapi', $history);
				}
			}
			return $this->_history_id;
		}
		
		/**
		* Check if we have a variable registred already
		*
		* @param string $_variableName name of variable to check
		* @return bool was the variable found?
		*/
		public function is_registered($_variableName)
		{
			if ($this->variableNames[$_variableName] == 'registered')
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		/**
		* Detects if the page has already been called before - good for forms
		*
		* @author skwashd
		* @param bool $diplay_error when implemented will use the generic error handling code
		* @return True if called previously, else False - call ok
		*/
		public function is_repost($display_error = False)
		{
			$history = $this->appsession($location = 'history', $appname = 'phpgwapi');
			if(isset($history[$_GET['click_history']]))
			{
				if($display_error)
				{
					$GLOBALS['phpgw']->redirect_link('/error.php', array('type' => 'repost'));//more on this later :)
				}
				else
				{
					return True; //handled by the app
				}
			}
			else
			{
				$history[$_GET['click_history']] = True;
				$this->appsession($location = 'history', $appname = 'phpgwapi', $history);
				return False;
			}
		}

		/**
		* Generate a url which supports url or cookies based sessions
		*
		* @param string $url a url relative to the phpgroupware install root
		* @param array $extravars query string arguements
		* @param bool $redirect is this for a redirect link ?
		* @return string generated url
		*/
		public function link($url, $extravars = array(), $redirect=false)
		{
			$term = '&amp;'; //W3C Compliant in markup
			if ( $redirect )
			{
				$term = '&'; // RFC Compliant for Header('Location: ...
			}
			
			/* first we process the $url to build the full scriptname */
			$full_scriptname = True;

			$url_firstchar = substr($url ,0,1);
			if ($url_firstchar == '/' && $GLOBALS['phpgw_info']['server']['webserver_url'] == '/')
			{
				$full_scriptname = False;
			}

			if ($url_firstchar != '/')
			{
				$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
				if ($app != 'home' && $app != 'login' && $app != 'logout')
				{
					$url = $app.'/'.$url;
				}
			}
			
			if($full_scriptname)
			{
				$webserver_url_count = strlen($GLOBALS['phpgw_info']['server']['webserver_url'])-1;
				if(substr($GLOBALS['phpgw_info']['server']['webserver_url'] ,$webserver_url_count,1) != '/' && $url_firstchar != '/')
				{
					$url = $GLOBALS['phpgw_info']['server']['webserver_url'] .'/'. $url;
				}
				else
				{
					$url = $GLOBALS['phpgw_info']['server']['webserver_url'] . $url;
				}
			}

			if(isset($GLOBALS['phpgw_info']['server']['enforce_ssl']) && $GLOBALS['phpgw_info']['server']['enforce_ssl'])
			{
				if(substr($url ,0,4) != 'http')
				{
					$url = 'https://'.$GLOBALS['phpgw_info']['server']['hostname'].$url;
				}
				else
				{
					$url = str_replace ( 'http:', 'https:', $url);
				}
			}

			/*
				If an app sends the extrvars as a string we covert the extrvars into an array for proper processing
				This also helps prevent any duplicate values in the query string.
			*/
			if (!is_array($extravars) && $extravars != '')
			{
				trigger_error("String used for extravar in sessions::link(url, extravar) call, use an array", E_USER_WARNING);
				$vars = explode('&', $extravars);
				foreach( $vars as $v )
				{
					$b = split('=', $v);
					$new_extravars[$b[0]] = $b[1];
				}
				
				unset($extravars);
				
				$extravars = $new_extravars;
				unset($new_extravars);
			}
			
			/* if using frames we make sure there is a framepart */
			if(defined('PHPGW_USE_FRAMES') && PHPGW_USE_FRAMES)
			{
				if (!isset($extravars['framepart']))
				{
					$extravars['framepart'] = 'body';
				}
			}
			
			/* add session params if not using cookies */
			if ( !isset($GLOBALS['phpgw_info']['server']['usecookies']) 
				|| !$GLOBALS['phpgw_info']['server']['usecookies'])
			{
				$extravars = is_array( $extravars ) ? array_merge($extravars, $this->_get_session_vars()) :  $this->_get_session_vars();				
			}
			
			//used for repost prevention
			$extravars['click_history'] = $this->generate_click_history();

			/* enable easy use of xdebug */
			if ( isset($_REQUEST['XDEBUG_PROFILE']) )
			{
				$extravars['XDEBUG_PROFILE'] = 1;
			}

			if ( is_array($extravars) ) //we have something to append
			{
				$url .= '?' . http_build_query($extravars, null, $term);
			}
			return $url;
		}

		/**
		 * get list of normal / non-anonymous sessions
		*
		 * The data form the session-files get cached in the app_session phpgwapi/php4_session_cache
		 * @author ralfbecker
		 */
		public function list_sessions($start,$order,$sort,$all_no_sort = False)
		{
			// FIXME this now only works with php sessions :(
			return array();

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
			
				uasort($values, array('self', 'session_sort'));
				
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
		* Introspection for XML-RPC/SOAP
		*
		* @param string $_type tpye of introspection being sought
		* @return array available methods and args
		*/
		public function list_methods($_type)
		{
			if (is_array($_type))
			{
				$_type = $_type['type'];
			}
			
			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array(xmlrpcStruct,xmlrpcString)),
							'docstring' => lang('Read this list of methods.')
						),
						'update_dla' => array(
							'function'  => 'update_dla',
							'signature' => array(array(xmlrpcBoolean)),
							'docstring' => lang('Returns an array of todo items')
						)
					);
					return $xml_functions;
					break;
				case 'soap':
					return $this->soap_functions;
					break;
				default:
					return array();
					break;
			}
		}

		/**
		* Write or update (for logout) the access_log
		*
		* @param string $sessionid id of session or 0 for unsuccessful logins
		* @param string $login account_lid (evtl. with domain) or '' for settion the logout-time
		* @param string $user_ip ip to log
		* @param int $account_id numerical account_id
		*/
		public function log_access($sessionid,$login='',$user_ip='',$account_id='')
		{
			$now = time();

			if ($login != '')
			{
				$GLOBALS['phpgw']->db->query('INSERT INTO phpgw_access_log(sessionid,loginid,ip,li,lo,account_id)'.
					" VALUES ('" . $this->_db->db_addslashes($sessionid) . "','" . $this->_db->db_addslashes($login). "','" . 
					$this->_db->db_addslashes($user_ip) . "',$now,0,".intval($account_id).")",__LINE__,__FILE__);
			}
			else
			{
				$GLOBALS['phpgw']->db->query("UPDATE phpgw_access_log SET lo=" . $now . " WHERE sessionid='" .
					$this->_db->db_addslashes($sessionid) . "'",__LINE__,__FILE__);
			}
			if ($GLOBALS['phpgw_info']['server']['max_access_log_age'])
			{
				$max_age = $now - $GLOBALS['phpgw_info']['server']['max_access_log_age'] * 24 * 60 * 60;
				
				$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_access_log WHERE li < $max_age");
			}
		}

		/**
		* Set a cookie
		*
		* @param string $cookiename name of cookie to be set
		* @param string $cookievalue value to be used, if unset cookie is cleared (optional)
		* @param int $cookietime when cookie should expire, 0 for session only (optional)
		*/
		public function phpgw_setcookie($cookiename, $cookievalue='', $cookietime=0)
		{
			$secure = phpgw::get_var('HTTPS', 'bool', 'SERVER');
			setcookie($cookiename, $cookievalue, $cookietime, '/', $this->_cookie_domain, $secure, true); 
		}

		/**
		* Someone needs to document me
		*/
		public function read_repositories($cached = true, $write_cache = true)
		{
			$GLOBALS['phpgw']->acl->set_account_id($this->_account_id);
			$GLOBALS['phpgw']->accounts->set_account($this->_account_id);
			$GLOBALS['phpgw']->preferences->set_account_id($this->_account_id);
			$GLOBALS['phpgw']->applications->set_account_id($this->_account_id);
			
			if($cached)
			{
				$this->_data = phpgwapi_cache::session_get('phpgwapi', 'phpgw_info');
				if(!empty($this->_data))
				{
					$GLOBALS['phpgw']->preferences->data = $this->_data['preferences'];
					if (!isset($GLOBALS['phpgw_info']['apps']) || !is_array($GLOBALS['phpgw_info']['apps']))
					{
						$GLOBALS['phpgw']->applications->read_installed_apps();
					}
				}
				else
				{
					$this->_setup_cache($write_cache);
				}
			}
			else
			{
				$this->_setup_cache($write_cache);
			}
			$this->hooks = $GLOBALS['phpgw']->hooks->read();
		}

		public function read_session($sessionid)
		{
			if($sessionid)
			{
				session_id($sessionid);
			}
			session_start();
			if ( isset($_SESSION['phpgw_session']) && is_array($_SESSION['phpgw_session']) ) 
			{
				return $_SESSION['phpgw_session'];
			}
			return array();
		}

		/**
		 * Regenerate the session id
		 *
		 * @internal FIXME make this work properly - some data needs to be updated when this is called
		 * @return string the new session id
		 */
		public function regenerate_id()
		{
			return session_regenerate_id(true);
		}

		/**
		* Create a list a variable names, which data needs to be restored
		*
		* @param string $_variableName name of variable to be registered
		*/
		public function register($_variableName)
		{
			$this->variableNames[$_variableName]='registered';
			#print 'registered '.$_variableName.'<br>';
		}

		public function register_session($login,$user_ip,$now,$session_flags)
		{
			if ( $this->_sessionid )
			{
				session_id($this->_sessionid);
			}

			if ( !strlen(session_id() ) )
			{
				session_start();
			}

			$_SESSION['phpgw_session'] = array
			(
				'session_id'		=> $this->_sessionid,
				'session_lid'		=> $login,
				'session_ip'		=> $user_ip,
				'session_logintime'	=> $now,
				'session_dla'		=> $now,
				'session_action'	=> $_SERVER['PHP_SELF'],
				'session_flags'		=> $session_flags,
				'user_agent'		=> md5(phpgw::get_var('USER_AGENT', 'string', 'SERVER')),
				// we need the install-id to differ between serveral installs shareing one tmp-dir
				'session_install_id'	=> $GLOBALS['phpgw_info']['server']['install_id']
			);
		}

		public function restore()
		{
			$sessionData = $this->appsession('sessiondata');
			
			if (!empty($sessionData) && is_array($sessionData))
			{
				foreach($sessionData as $key => $value)
				{
					global $$key;
					$$key = $value;
					$this->variableNames[$key] = 'registered';
					// echo 'restored: '.$key.', ' . $value . '<br>';
				}
			}
		}

		/**
		* Save the current values of all registered variables
		*/
		public function save()
		{
			if (is_array($this->variableNames))
			{
				reset($this->variableNames);
				while(list($key, $value) = each($this->variableNames))
				{
					if ($value == 'registered')
					{
						global $$key;
						$sessionData[$key] = $$key;
					}
				}
				$this->appsession('sessiondata','',$sessionData);
			}
		}

		/**
		* This looks to be useless
		* This will capture everything in the $GLOBALS['phpgw_info'] including server info,
		* and store it in appsessions.  This is really incompatible with any type of restoring
		* from appsession as the saved user info is really in ['user'] rather than the root of
		* the structure, which is what this class likes.
		*/
		protected function save_repositories()
		{
			$phpgw_info_temp = $GLOBALS['phpgw_info'];
			$phpgw_info_temp['flags'] = array();
			
			phpgwapi_cache::session_set('phpgwapi', 'phpgw_info', $phpgw_info_temp);
		}

		public static function session_sort($a,$b)
		{
			$sign = strcasecmp($GLOBALS['phpgw']->session->sort_order,'ASC') ? 1 : -1;

			return strcasecmp($a[$GLOBALS['phpgw']->session->sort_by],
							  $b[$GLOBALS['phpgw']->session->sort_by]) * $sign;
		}

		/**
		 * get number of normal / non-anonymous sessions
		 *
		 * @author ralfbecker
		 */
		public function total()
		{
			return count($this->list_sessions(0,'','',True));
		}

		// This will update the DateLastActive column, so the login does not expire
		public function update_dla()
		{
			session_id($this->_sessionid);
			session_start();

			if ( isset($GLOBALS['phpgw_info']['menuaction']) )
			{
				$action = $GLOBALS['phpgw_info']['menuaction'];
			}
			else
			{
				$action = $_SERVER['PHP_SELF'];
			}

			$_SESSION['phpgw_session']['session_dla'] = time();
			$_SESSION['phpgw_session']['session_action'] = $action;
		
			return True;
		}

		/**
		* Mark variable as unregistered
		*
		* @param string $_variableName name of variable to deregister
		*/
		public function unregister($_variableName)
		{
			$this->variableNames[$_variableName]='unregistered';
			#print 'unregistered '.$_variableName.'<br>';
		}

		/**
		* Check to see if a session is still current and valid
		*
		* @param string $sessionid session id to be verfied
		* @return bool is the session valid?
		*/
		public function verify($sessionid='')
		{
			if(empty($sessionid) || !$sessionid)
			{
				$sessionid = phpgw::get_var(session_name());
			}
			
			$this->_sessionid = $sessionid;
			
			$session = $this->read_session($sessionid);
			
			if ( !isset($session['session_dla']) || $session['session_dla'] <= (time() - $GLOBALS['phpgw_info']['server']['sessions_timeout']))
			{
				if(isset($session['session_dla']))
				{
					$this->cd_reason = 10;
				}
				return False;
			}

			$this->_session_flags = $session['session_flags'];

			$lid_data = explode('@', $session['session_lid']);
			$this->_account_lid = $lid_data[0];

			if ( isset($lid_data[1]) )
			{
				$this->_account_domain = $lid_data[1];
			}
			else
			{
				$this->_account_domain = $GLOBALS['phpgw_info']['server']['default_domain'];
			}
			unset($lid_data);

			$this->update_dla();
			$this->_account_id = $GLOBALS['phpgw']->accounts->name2id($this->_account_lid);
			if (!$this->_account_id)
			{
				$this->cd_reason = 5;
				return false;
			}

			$GLOBALS['phpgw_info']['user']['account_id'] = $this->_account_id;

			/* init the crypto object before appsession call below */
			$this->_key = md5($this->_sessionid . $GLOBALS['phpgw_info']['server']['encryptkey']);
			$this->_iv  = $GLOBALS['phpgw_info']['server']['mcrypt_iv'];
			$GLOBALS['phpgw']->crypto->init(array($this->_key,$this->_iv));

			$use_cache = isset($GLOBALS['phpgw_info']['server']['cache_phpgw_info']) ? !!$GLOBALS['phpgw_info']['server']['cache_phpgw_info'] : false;
			$this->read_repositories($use_cache);
			
			if ($this->_data['expires'] != -1 && $this->_data['expires'] < time())
			{
				if(is_object($GLOBALS['phpgw']->log))
				{
					$GLOBALS['phpgw']->log->message(array(
						'text' => 'W-VerifySession, account loginid %1 is expired',
						'p1'   => $this->_account_lid,
						'line' => __LINE__,
						'file' => __FILE__
					));
					$GLOBALS['phpgw']->log->commit();
				}
				if(is_object($GLOBALS['phpgw']->crypto))
				{
					$GLOBALS['phpgw']->crypto->cleanup();
					unset($GLOBALS['phpgw']->crypto);
				}
				$this->cd_reason = 2;
				return False;
			}

			$GLOBALS['phpgw_info']['user']  = $this->_data;
			$GLOBALS['phpgw_info']['hooks'] = $this->hooks;

			$GLOBALS['phpgw_info']['user']['session_ip'] = $session['session_ip'];
			$GLOBALS['phpgw_info']['user']['passwd']     = base64_decode($this->appsession('password','phpgwapi'));

			if ($this->_account_domain != $GLOBALS['phpgw_info']['user']['domain'])
			{
				if(is_object($GLOBALS['phpgw']->log))
				{
					$GLOBALS['phpgw']->log->message(array(
						'text' => 'W-VerifySession, the domains %1 and %2 don\'t match',
						'p1'   => $userid_array[1],
						'p2'   => $GLOBALS['phpgw_info']['user']['domain'],
						'line' => __LINE__,
						'file' => __FILE__
					));
					$GLOBALS['phpgw']->log->commit();
				}
				if(is_object($GLOBALS['phpgw']->crypto))
				{
					$GLOBALS['phpgw']->crypto->cleanup();
					unset($GLOBALS['phpgw']->crypto);
				}
				$this->cd_reason = 5;
				return False;
			}

			// verify the user agent in an attempt to stop session hijacking
			if ( $_SESSION['phpgw_session']['user_agent'] != md5(phpgw::get_var('USER_AGENT', 'string', 'SERVER')) )
			{
				if(is_object($GLOBALS['phpgw']->log))
				{
					// This needs some better wording
					$GLOBALS['phpgw']->log->message(array(
						'text' => 'W-VerifySession, User agent hash %1 doesn\'t match user agent hash %2 in session',
						'p1'   => $_SESSION['phpgw_session']['user_agent'],
						'p2'   => md5(phpgw::get_var('USER_AGENT', 'string', 'SERVER')),
						'line' => __LINE__,
						'file' => __FILE__
					));
					$GLOBALS['phpgw']->log->commit();
				}
				if(is_object($GLOBALS['phpgw']->crypto))
				{
					$GLOBALS['phpgw']->crypto->cleanup();
					unset($GLOBALS['phpgw']->crypto);
				}
				// generic session can't be verified error - don't be specific about the problem
				$this->cd_reason = 2; 
				return False;
			}

			$check_ip = isset($GLOBALS['phpgw_info']['server']['sessions_checkip']) ? !!$GLOBALS['phpgw_info']['server']['sessions_checkip'] : false;
			if ($check_ip)
			{
				if (PHP_OS != 'Windows' && (! $GLOBALS['phpgw_info']['user']['session_ip'] || $GLOBALS['phpgw_info']['user']['session_ip'] != $this->_get_user_ip()))
				{
					if(is_object($GLOBALS['phpgw']->log))
					{
						// This needs some better wording
						$GLOBALS['phpgw']->log->message(array(
							'text' => 'W-VerifySession, IP %1 doesn\'t match IP %2 in session',
							'p1'   => $this->_get_user_ip(),
							'p2'   => $GLOBALS['phpgw_info']['user']['session_ip'],
							'line' => __LINE__,
							'file' => __FILE__
						));
						$GLOBALS['phpgw']->log->commit();
					}
					if(is_object($GLOBALS['phpgw']->crypto))
					{
						$GLOBALS['phpgw']->crypto->cleanup();
						unset($GLOBALS['phpgw']->crypto);
					}
					$this->cd_reason = 2;
					return False;
				}
			}

			$GLOBALS['phpgw']->acl->set_account_id($this->_account_id);
			$GLOBALS['phpgw']->accounts->set_account($this->_account_id);
			$GLOBALS['phpgw']->preferences->set_account_id($this->_account_id);
			$GLOBALS['phpgw']->applications->set_account_id($this->_account_id);

			if (! $this->_account_lid)
			{
				if(is_object($GLOBALS['phpgw']->log))
				{
					// This needs some better wording
					$GLOBALS['phpgw']->log->message(array(
						'text' => 'W-VerifySession, account_id is empty',
						'line' => __LINE__,
						'file' => __FILE__
					));
					$GLOBALS['phpgw']->log->commit();
				}
				if(is_object($GLOBALS['phpgw']->crypto))
				{
					$GLOBALS['phpgw']->crypto->cleanup();
					unset($GLOBALS['phpgw']->crypto);
				}
				//echo 'DEBUG: Sessions: account_id is empty!<br>'."\n";
				return false;
			}
			return true;
		}

		/**
		* Verfy a peer server access request -DISABLED needs to be audited!
		* 
		* @param string $sessionid session id to verfiy
		* @return bool verfied?
		*/
		public function verify_server($sessionid)
		{
			return false;

			$GLOBALS['phpgw']->interserver = createObject('phpgwapi.interserver');
			$this->_sessionid = $sessionid;

			$session = $this->read_session($this->_sessionid);
			$this->_session_flags = $session['session_flags'];

			list($this->_account_lid,$this->_account_domain) = explode('@', $session['session_lid']);
			
			if ($this->_account_domain == '')
			{
				$this->_account_domain = $GLOBALS['phpgw_info']['server']['default_domain'];
			}

			$phpgw_info_flags = $GLOBALS['phpgw_info']['flags'];

			$GLOBALS['phpgw_info']['flags'] = $phpgw_info_flags;
			
			$this->update_dla();
			$this->_account_id = $GLOBALS['phpgw']->interserver->name2id($this->_account_lid);

			if (!$this->_account_id)
			{
				return False;
			}

			$GLOBALS['phpgw_info']['user']['account_id'] = $this->_account_id;
			
			$use_cache = isset($GLOBALS['phpgw_info']['server']['cache_phpgw_info']) ? !!$GLOBALS['phpgw_info']['server']['cache_phpgw_info'] : false;
			$this->read_repositories($use_cache);

			/* init the crypto object before appsession call below */
			$this->_key = md5($this->_sessionid . $GLOBALS['phpgw_info']['server']['encryptkey']);
			$this->_iv  = $GLOBALS['phpgw_info']['server']['mcrypt_iv'];
			$GLOBALS['phpgw']->crypto->init(array($this->_key,$this->_iv));

			$GLOBALS['phpgw_info']['user']  = $this->_data;
			$GLOBALS['phpgw_info']['hooks'] = $this->hooks;

			$GLOBALS['phpgw_info']['user']['session_ip'] = $session['session_ip'];
			$GLOBALS['phpgw_info']['user']['passwd'] = base64_decode($this->appsession('password','phpgwapi'));

			if ($userid_array[1] != $GLOBALS['phpgw_info']['user']['domain'])
			{
				if(is_object($GLOBALS['phpgw']->log))
				{
					$GLOBALS['phpgw']->log->message(array(
						'text' => 'W-VerifySession, the domains %1 and %2 don\t match',
						'p1'   => $userid_array[1],
						'p2'   => $GLOBALS['phpgw_info']['user']['domain'],
						'line' => __LINE__,
						'file' => __FILE__
					));
					$GLOBALS['phpgw']->log->commit();
				}

				if(is_object($GLOBALS['phpgw']->crypto))
				{
					$GLOBALS['phpgw']->crypto->cleanup();
					unset($GLOBALS['phpgw']->crypto);
				}
				return False;
			}

			$verify_ip = isset($GLOBALS['phpgw_info']['server']['sessions_checkip']) ? !!$GLOBALS['phpgw_info']['server']['sessions_checkip'] : false;
			if ( $verify_ip )
			{
				if (PHP_OS != 'Windows' && (! $GLOBALS['phpgw_info']['user']['session_ip'] || $GLOBALS['phpgw_info']['user']['session_ip'] != $this->_get_user_ip()))
				{
					if(is_object($GLOBALS['phpgw']->log))
					{
						// This needs some better wording
						$GLOBALS['phpgw']->log->message(array(
							'text' => 'W-VerifySession, IP %1 doesn\'t match IP %2 in session table',
							'p1'   => $this->_get_user_ip(),
							'p2'   => $GLOBALS['phpgw_info']['user']['session_ip'],
							'line' => __LINE__,
							'file' => __FILE__
						));
						$GLOBALS['phpgw']->log->commit();
					}

					if(is_object($GLOBALS['phpgw']->crypto))
					{
						$GLOBALS['phpgw']->crypto->cleanup();
						unset($GLOBALS['phpgw']->crypto);
					}
					return False;
				}
			}

			$GLOBALS['phpgw']->acl->acl($this->_account_id);
			$GLOBALS['phpgw']->accounts->set_account($this->_account_id);
			$GLOBALS['phpgw']->preferences->set_account_id($this->_account_id);
			$GLOBALS['phpgw']->applications->applications($this->_account_id);

			if (! $this->_account_lid)
			{
				if(is_object($GLOBALS['phpgw']->log))
				{
					// This needs some better wording
					$GLOBALS['phpgw']->log->message(array(
						'text' => 'W-VerifySession, account_id is empty',
						'line' => __LINE__,
						'file' => __FILE__
					));
					$GLOBALS['phpgw']->log->commit();
				}

				if(is_object($GLOBALS['phpgw']->crypto))
				{
					$GLOBALS['phpgw']->crypto->cleanup();
					unset($GLOBALS['phpgw']->crypto);
				}
				return False;
			}
			else
			{
				return True;
			}
		}

		/**
		* Get the list of session variables used for non cookie based sessions
		*
		* @return array the variables which are specific to this session type
		*/
		protected function _get_session_vars()
		{
			return array
			(
				session_name()	=> $this->_sessionid,
				'domain'		=> $this->_account_domain
			);
		}

		/**
		* Get the ip address of current users
		*
		* @return string ip address
		*/
		protected function _get_user_ip()
		{
			return phpgw::get_var('HTTP_X_FORWARDED_FOR', 'ip', 'SERVER',
				phpgw::get_var('REMOTE_ADDR', 'ip', 'SERVER'));
		}

		/**
		* Protect against brute force attacks, block login if too many unsuccessful login attmepts
		*
		* @param string $login account_lid (evtl. with domain)
		* @param string $ip the ip that made the request
		* @returns bool login blocked?
		*/
		protected function _login_blocked($login, $ip)
		{
			$blocked = false;
			$block_time = time() - $GLOBALS['phpgw_info']['server']['block_time'] * 60;
			
			$ip = $this->_db->db_addslashes($ip);
			$this->_db->query("SELECT count(*) FROM phpgw_access_log WHERE account_id=0 AND ip='$ip' AND li > $block_time",__LINE__,__FILE__);
			$this->_db->next_record();
			if (($false_ip = $this->_db->f(0)) > $GLOBALS['phpgw_info']['server']['num_unsuccessful_ip'])
			{
				//echo "<p>login_blocked: ip='$ip' ".$this->_db->f(0)." tries (".$GLOBALS['phpgw_info']['server']['num_unsuccessful_ip']." max.) since ".date('Y/m/d H:i',$block_time)."</p>\n";
				$blocked = True;
			}
			$login = $this->_db->db_addslashes($login);
			$this->_db->query("SELECT count(*) FROM phpgw_access_log WHERE account_id=0 AND (loginid='$login' OR loginid LIKE '$login@%') AND li > $block_time",__LINE__,__FILE__);
			$this->_db->next_record();
			if (($false_id = $this->_db->f(0)) > $GLOBALS['phpgw_info']['server']['num_unsuccessful_id'])
			{
				//echo "<p>login_blocked: login='$login' ".$this->_db->f(0)." tries (".$GLOBALS['phpgw_info']['server']['num_unsuccessful_id']." max.) since ".date('Y/m/d H:i',$block_time)."</p>\n";
				$blocked = True;
			}
			if ($blocked && isset($GLOBALS['phpgw_info']['server']['admin_mails']) && $GLOBALS['phpgw_info']['server']['admin_mails']  &&
				// max. one mail each 5mins
				$GLOBALS['phpgw_info']['server']['login_blocked_mail_time'] < time()-5*60)
			{
				// notify admin(s) via email
				$from    = 'phpGroupWare@'.$GLOBALS['phpgw_info']['server']['mail_suffix'];
				$subject = lang("phpGroupWare: login blocked for user '%1', IP %2",$login,$ip);
				$body    = lang("Too many unsuccessful attempts to login: %1 for the user '%2', %3 for the IP %4", $false_id, $login, $false_ip, $ip);
				
				if(!is_object($GLOBALS['phpgw']->send))
				{
					$GLOBALS['phpgw']->send = createObject('phpgwapi.send');
				}
				$subject = $GLOBALS['phpgw']->send->encode_subject($subject);
				$admin_mails = explode(',',$GLOBALS['phpgw_info']['server']['admin_mails']);
				foreach($admin_mails as $to)
				{
					$GLOBALS['phpgw']->send->msg('email',$to,$subject,$body,'','','',$from,$from);
				}
				// save time of mail, to not send to many mails
				$config = createObject('phpgwapi.config','phpgwapi');
				$config->read_repository();
				$config->value('login_blocked_mail_time',time());
				$config->save_repository();
			}
			return $blocked;
		}

		/**
		* Configure cookies to be used properly for this session
		*
		* @return string domain
		*/
		protected function _phpgw_set_cookie_params()
		{
			if ( !is_null($this->_cookie_domain) )
			{
				return $this->_cookie_domain;
			}

			if ( isset($GLOBALS['phpgw_info']['server']['cookie_domain']) )
			{
				$this->_cookie_domain = $GLOBALS['phpgw_info']['server']['cookie_domain'];
			}
			else
			{
				$this->_cookie_domain = phpgw::get_var('HTTP_HOST', 'string', 'SERVER');
			}

			$secure = phpgw::get_var('HTTPS', 'bool', 'SERVER');
			session_set_cookie_params(0, '/', $this->_cookie_domain, $secure, true);
			return $this->_cookie_domain;
		}

		/**
		 * Someone needs to document me
		 */
		protected function _setup_cache($write_cache=True)
		{
			$this->_data                = $GLOBALS['phpgw']->accounts->read_repository()->toArray();
			$this->_data['acl']         = $GLOBALS['phpgw']->acl->read();
			$this->_data['preferences'] = $GLOBALS['phpgw']->preferences->read_repository();
			$this->_data['apps']        = $GLOBALS['phpgw']->applications->read_repository();

			$this->_data['domain']      = $this->_account_domain;
			$this->_data['sessionid']   = $this->_sessionid;
			$this->_data['session_ip']  = $this->_get_user_ip();
			$this->_data['session_lid'] = $this->_account_lid.'@'.$this->_account_domain;
			$this->_data['account_id']  = $this->_account_id;
			$this->_data['account_lid'] = $this->_account_lid;
			$this->_data['userid']      = $this->_account_lid;
			$this->_data['passwd']      = $this->_passwd;

			//echo '<pre>' . print_r($this->_data, true) . '</pre>';

			if ( $write_cache )
			{
				phpgwapi_cache::session_set('phpgwapi', 'phpgw_info', $this->_data);
			}
		}

		/**
		 * Set the user's login details
		 *
		 * @param string $login the user login to parse
		 */
		protected function _set_login($login)
		{
			$m = array();
			if ( preg_match('/(.*)@(.*)/', $login, $m) )
			{
				$this->_account_lid = $m[1];
				$this->_account_domain = $m[2];
			}

			$this->_account_lid = $login;
			$this->_account_domain = $GLOBALS['phpgw_info']['server']['default_domain'];
		}
	}
