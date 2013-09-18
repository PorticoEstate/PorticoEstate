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
	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU Lesser General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
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
	if ( isset($GLOBALS['phpgw_info']['flags']['session_name']) && $GLOBALS['phpgw_info']['flags']['session_name'] )
	{
		session_name($GLOBALS['phpgw_info']['flags']['session_name']);
	}
	else
	{
		session_name('sessionphpgwsessid');
	}

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
			$this->_db			=& $GLOBALS['phpgw']->db;
			$use_cookies = false;
			if ( isset($GLOBALS['phpgw_info']['server']['usecookies'])
				&& $GLOBALS['phpgw_info']['server']['usecookies'] == 'True' )
			{
				$use_cookies = true;
				$this->_sessionid	= phpgw::get_var(session_name(), 'string', 'COOKIE');
			}
			else
			{
				$this->_sessionid	= phpgw::get_var(session_name()); // GET or POST
			}

			$this->_phpgw_set_cookie_params();

			//respect the config option for cookies
			ini_set('session.use_cookies', $use_cookies);

			//don't rewrite URL, as we have to do it in link - why? cos it is buggy otherwise
			ini_set('url_rewriter.tags', '');
		}

		/**
		 * Cache data for the user's current session
		 *
		 * @param string $id     the unique id within the module for the data
		 * @param string $module the module name that the data is stored for
		 * @param mixed  $data   the data to store - use ##NOTHING## to retreive data
		 *
		 * @return mixed the data - even if storing
		 *
		 * @deprecated see phpgwapi_cache::session_set/session_get
		 */
		public function appsession($id = 'default', $module = '', $data = '##NOTHING##')
		{
			if ( !$module )
			{
				$module = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			/* This allows the user to put '' as the value. */
			if ($data == '##NOTHING##')
			{
				return phpgwapi_cache::session_get($module, $id);
			}
			phpgwapi_cache::session_set($module, $id, $data);
			return $data;
		}

		/**
		* Clears the appsession cache, should be called before any actions which would invalidate the cache
		*
		* @return void
		*/
		public function clear_phpgw_info_cache()
		{
			phpgwapi_cache::session_clear('phpgwapi', 'phpgw_info');
		}

		/**
		* Create a new session
		*
		* @param string  $login     user login
		* @param string  $passwd    user password
		* @param boolean $skip_auth create a sesison without authenticating the user?
		*
		* @return string session id
		*/
		public function create($login, $passwd = '', $skip_auth = false)
		{
			phpgw::import_class('phpgwapi.globally_denied');
			$accounts =& $GLOBALS['phpgw']->accounts;

			if (is_array($login))
			{
				$this->_login	= $login['login'];
				$this->_passwd	= $login['passwd'];
				$login			= $this->_login;
			}
			else
			{
				$this->_login	= $login;
				$this->_passwd	= $passwd;
			}

			$now = time();

			$this->_set_login($login);
			$user_ip	= $this->_get_user_ip();

			if ( $this->_login_blocked($login, $this->_get_user_ip()) )
			{
				$this->reason		= 'blocked, too many attempts';
				$this->cd_reason	= 99;

				// log unsuccessfull login
				$this->log_access($this->reason, $login, $user_ip, 0);

				return false;
			}

			if ( phpgwapi_globally_denied::user($this->_account_lid)
				|| !$accounts->name2id($this->_account_lid)
				|| ( !$skip_auth && !$GLOBALS['phpgw']->auth->authenticate($this->_account_lid, $this->_passwd) )
				|| get_class($accounts->get($accounts->name2id($this->_account_lid)))
					== phpgwapi_account::CLASS_TYPE_GROUP )
			{
				$this->reason		= 'bad login or password';
				$this->cd_reason	= 5;

				// log unsuccessfull login
				$this->log_access($this->reason, $login, $user_ip, 0);
				return false;
			}

			if ( !$accounts->exists($this->_account_lid)
					&& $GLOBALS['phpgw_info']['server']['auto_create_acct'] )
			{
				$this->_account_id = $accounts->auto_add($this->_account_lid, $passwd);
			}
			else
			{
				$this->_account_id = $accounts->name2id($this->_account_lid);
			}
			$GLOBALS['phpgw_info']['user']['account_id'] = $this->_account_id;
			$accounts->set_account($this->_account_id);

			session_start();
			$this->_sessionid = session_id();

			if ( isset($GLOBALS['phpgw_info']['server']['usecookies'])
				&& $GLOBALS['phpgw_info']['server']['usecookies'] )
			{
				$this->phpgw_setcookie(session_name(), $this->_sessionid);
				$this->phpgw_setcookie('domain', $this->_account_domain);
			}

			if ( ( isset($GLOBALS['phpgw_info']['server']['usecookies'])
					&& $GLOBALS['phpgw_info']['server']['usecookies'] )
				|| isset($_COOKIE['last_loginid']))
			{
				// Create a cookie which expires in 14 days
				$cookie_expires = $now + (60 * 60 * 24 * 14);
				$this->phpgw_setcookie('last_loginid', $this->_account_lid, $cookie_expires);
				$this->phpgw_setcookie('last_domain', $this->_account_domain, $cookie_expires);
			}
			/* we kill this for security reasons */
			unset($GLOBALS['phpgw_info']['server']['default_domain']);

			/* init the crypto object */
			$this->_key = md5($this->_sessionid . $GLOBALS['phpgw_info']['server']['encryptkey']);
			$this->_iv  = $GLOBALS['phpgw_info']['server']['mcrypt_iv'];
			$GLOBALS['phpgw']->crypto->init(array($this->_key, $this->_iv));

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
				return false;
			}

			$GLOBALS['phpgw_info']['user']  = $this->_data;
	//		$GLOBALS['phpgw_info']['hooks'] = $this->hooks;

			phpgwapi_cache::session_set('phpgwapi', 'password', base64_encode($this->_passwd));
			if ( $GLOBALS['phpgw']->acl->check('anonymous', 1, 'phpgwapi') )
			{
				$session_flags = 'A';
			}
			else
			{
				$session_flags = 'N';
			}

			$GLOBALS['phpgw']->db->transaction_begin();
			$this->register_session($login, $user_ip, $now, $session_flags);
			$this->log_access($this->_sessionid, $login, $user_ip, $this->_account_id);
			$GLOBALS['phpgw']->auth->update_lastlogin($this->_account_id, $user_ip);
			$GLOBALS['phpgw']->db->transaction_commit();

			return $this->_sessionid;
		}

		/**
		* Validate a peer server login request
		*
		* @param string	$login  login name
		* @param string	$passwd password
		*
		* @return bool login ok?
		*/
		public function create_server($login, $passwd)
		{
			$GLOBALS['phpgw']->interserver = createObject('phpgwapi.interserver');
			$this->_login  = $login;
			$this->_passwd = $passwd;
			$login_array = explode('#', $login);
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
				return false;
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
			$GLOBALS['phpgw']->crypto->init(array($this->_key, $this->_iv));

			//$this->read_repositories(false);

			$GLOBALS['phpgw_info']['user']  = $this->_data;
	//		$GLOBALS['phpgw_info']['hooks'] = $this->hooks;

			$this->appsession('password', 'phpgwapi', base64_encode($this->_passwd));
			$session_flags = 'S';

			$user_ip = $this->_get_user_ip();

			$GLOBALS['phpgw']->db->transaction_begin();
			$this->register_session($login, $user_ip, $now, $session_flags);

			$this->log_access($this->_sessionid, $login, $user_ip, $this->_account_id);

			$GLOBALS['phpgw']->auth->update_lastlogin($this->_account_id, $user_ip);
			$GLOBALS['phpgw']->db->transaction_commit();

			return array($this->_sessionid);
		}

		/**
		 * Delete the phpgw_info cache data for a user
		 *
		 * @param integer $ignored this value isn't used
		 *
		 * @return void
		 * @deprecated see phpgwapi_cache::session_clear()
		 */
		public function delete_cache($ignored = null)
		{
			phpgwapi_cache::session_clear('phpgwapi', 'phpgw_info');
		}

		/**
		 * Terminate a session
		 *
		 * @param string $sessionid the session to terminate
		 *
		 * @return boolean was the session terminated?
		 */
		public function destroy($sessionid)
		{
			if ( !$sessionid )
			{
				return false;
			}

			$this->log_access($this->_sessionid);	// log logout-time

			// Only do the following, if where working with the current user
			if ($sessionid == $GLOBALS['phpgw_info']['user']['sessionid'])
			{
				session_unset();
				session_destroy();
				$this->phpgw_setcookie(session_name());
			}
			else if ( $GLOBALS['phpgw_info']['server']['sessions_type'] == 'php' )
			{
				$sessions = $this->list_sessions(0, '', '', true);

				if ( isset($sessions[$sessionid]) )
				{
					unlink($sessions[$sessionid]['session_file']);
				}
			}
			else
			{
				phpgwapi_session_handler_db::destroy($sessionid);
			}

			return true;
		}

		/**
		* Additional tracking of user actions - prevents reposts/use of back button
		*
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
		* @param string	$varname name of variable to check
		*
		* @return bool was the variable found?
		*/
		public function is_registered($varname)
		{
			return $this->variableNames[$varname] == 'registered';
		}

		/**
		* Detects if the page has already been called before - good for forms
		*
		* @param boolean $display_error when implemented will use the generic error handler code
		*
		* @return boolean true if called previously, else false - call ok
		*/
		public function is_repost($display_error = false)
		{
			$history		= phpgwapi_cache::session_get('phpgwapi', 'history');
			$click_history	= phpgw::get_var('click_history', 'string', 'GET');

			if ( isset($history[$click_history]) )
			{
				if($display_error)
				{
					//more on this later :)
					$GLOBALS['phpgw']->redirect_link('/error.php', array('type' => 'repost'));
				}
				else
				{
					 //handled by the app
					return true;
				}
			}
			else
			{
				$history[$click_history] = true;
				phpgwapi_cache::session_set('phpgwapi', 'history', $history);
				return false;
			}
		}

		/**
		* Generate a url which supports url or cookies based sessions
		*
		* @param string  $url       a url relative to the phpgroupware install root
		* @param array   $extravars query string arguements
		* @param boolean $redirect  is this for a redirect link ?
		* @param boolean $external is the resultant link being used as external access (i.e url in emails..)
		*
		* @return string generated url
		*/
		public function link($url, $extravars = array(), $redirect=false, $external = false)
		{

			$custom_frontend = isset($GLOBALS['phpgw_info']['flags']['custom_frontend']) && $GLOBALS['phpgw_info']['flags']['custom_frontend'] ? $GLOBALS['phpgw_info']['flags']['custom_frontend'] : '';

			if($custom_frontend && substr($url, 0, 4) != 'http')
			{
				$url = '/' . $custom_frontend . '/' . ltrim($url, '/');
			}

			//W3C Compliant in markup	
			$term = '&amp;'; 
			if ( $redirect )
			{
				// RFC Compliant for Header('Location: ...
				$term = '&'; 
			}

			/* first we process the $url to build the full scriptname */
			$full_scriptname = true;

			$url_firstchar = substr($url, 0, 1);
			if ( $url_firstchar == '/'
				&& $GLOBALS['phpgw_info']['server']['webserver_url'] == '/' )
			{
				$full_scriptname = false;
			}

			if ( $url_firstchar != '/')
			{
				$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
				if ($app != 'home' && $app != 'login' && $app != 'logout')
				{
					$url = $app.'/'.$url;
				}
			}

			if($full_scriptname)
			{
				$webserver_url_count = strlen($GLOBALS['phpgw_info']['server']['webserver_url']) - 1;

				if ( substr($GLOBALS['phpgw_info']['server']['webserver_url'], $webserver_url_count, 1) != '/'
					&& $url_firstchar != '/' )
				{
					$url = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/{$url}";
				}
				else
				{
					$url = "{$GLOBALS['phpgw_info']['server']['webserver_url']}{$url}";
				}
			}

			if($external)
			{
				if(substr($url, 0, 4) != 'http')
				{
					$url = "http://{$GLOBALS['phpgw_info']['server']['hostname']}{$url}";
				}
			}

			if ( isset($GLOBALS['phpgw_info']['server']['enforce_ssl'])
				&& $GLOBALS['phpgw_info']['server']['enforce_ssl'])
			{
				if(substr($url, 0, 4) != 'http')
				{
					$url = "https://{$GLOBALS['phpgw_info']['server']['hostname']}{$url}";
				}
				else
				{
					$url = preg_replace('/http:/', 'https:', $url);
				}
			}

			/*
				If an app sends the extrvars as a string we covert the extrvars into an array for proper processing
				This also helps prevent any duplicate values in the query string.
			*/
			if (!is_array($extravars) && $extravars != '')
			{
				trigger_error("String used for extravar in sessions::link(url, extravar) call, use an array",
								E_USER_WARNING);
				$vars = explode('&', $extravars);
				foreach( $vars as $v )
				{
					$b = explode('=', $v);
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

			if(!$external)
			{
				/* add session params if not using cookies */
				if ( !isset($GLOBALS['phpgw_info']['server']['usecookies'])
					|| !$GLOBALS['phpgw_info']['server']['usecookies'])
				{
					if ( is_array($extravars) )
					{
						$extravars = array_merge($extravars, $this->_get_session_vars());
					}
					else
					{
						$extravars = $this->_get_session_vars();
					}
				}

				//used for repost prevention
				$extravars['click_history'] = $this->generate_click_history();

				/* enable easy use of xdebug */
				if ( isset($_REQUEST['XDEBUG_PROFILE']) )
				{
					$extravars['XDEBUG_PROFILE'] = 1;
				}
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
		 *
		 * @param integer $start       the record to start at
		 * @param string  $order       the "field" to sort by
		 * @param string  $sort        the direction to sort the data
		 * @param boolean $all_no_sort get all records unsorted?
		 *
		 * @return array the list of session records
		 */
		public function list_sessions($start, $order, $sort, $all_no_sort = false)
		{
			// We cache the data for 5mins system wide as this is an expensive operation
			$last_updated = 0; //phpgwapi_cache::system_get('phpgwapi', 'session_list_saved');

			if ( is_null($last_updated) 
				|| $last_updated < 60 * 5 )
			{
				$data = array();
				switch ( $GLOBALS['phpgw_info']['server']['sessions_type'] )
				{
					case 'db':
						$data = phpgwapi_session_handler_db::get_list();
						break;

					case 'php':
					default:
						$data = self::_get_list();
				}
				phpgwapi_cache::system_set('phpgwapi', 'session_list', $data);
				phpgwapi_cache::system_set('phpgwapi', 'session_list_saved', time());
			}
			else
			{
				$data = phpgwapi_cache::system_get('phpgwapi', 'session_list');
			}

			if ( $all_no_sort )
			{
				return $data;
			}

			$GLOBALS['phpgw']->session->sort_by = $sort;
			$GLOBALS['phpgw']->session->sort_order = $order;

			uasort($data, array('self', 'session_sort'));

			$maxmatches = 25;
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'])
				&& (int) $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] )
			{
				$maxmatches = (int) $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}

			return array_slice($data, $start, $maxmatches);
		}

		/**
		* Introspection for XML-RPC/SOAP
		*
		* @param string $_type tpye of introspection being sought
		*
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
							'signature' => array(array
											(
												$GLOBALS['xmlrpcStruct'],
												$GLOBALS['xmlrpcString']
											)),
							'docstring' => lang('Read this list of methods.')
						),
						'update_dla' => array(
							'function'  => 'update_dla',
							'signature' => array(array($GLOBALS['xmlrpcBoolean'])),
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
		* @param string  $sessionid  id of session or 0 for unsuccessful logins
		* @param string  $login      account_lid (evtl. with domain) or '' for settion the logout-time
		* @param string  $user_ip    ip to log
		* @param integer $account_id the user's account_id
		*
		* @return void
		*/
		public function log_access($sessionid, $login='', $user_ip='', $account_id='')
		{
			$now		= time();
			$sessionid	= $this->_db->db_addslashes($sessionid);
			$login		= $this->_db->db_addslashes($login);
			$user_ip	= $this->_db->db_addslashes($user_ip);
			$account_id	= (int) $account_id;

			if ($login != '')
			{
				$sql = 'INSERT INTO phpgw_access_log(sessionid,loginid,ip,li,lo,account_id)'
					. " VALUES ('{$sessionid}', '{$login}', '{$user_ip}', {$now}, 0, {$account_id})";
				$this->_db->query($sql, __LINE__, __FILE__);
			}
			else
			{
				$sql = "UPDATE phpgw_access_log SET lo ={$now}"
					. " WHERE sessionid='{$sessionid}'";
				$this->_db->query($sql, __LINE__, __FILE__);
			}
			if ($GLOBALS['phpgw_info']['server']['max_access_log_age'])
			{
				$max_age = $now - ($GLOBALS['phpgw_info']['server']['max_access_log_age'] * 24 * 60 * 60);

				$this->_db->query("DELETE FROM phpgw_access_log WHERE li < {$max_age}");
			}
		}

		/**
		* Set a cookie
		*
		* @param string  $cookiename  name of cookie to be set
		* @param string  $cookievalue value to be used, if unset cookie is cleared (optional)
		* @param integer $cookietime  when cookie should expire, 0 for session only (optional)
		*
		* @return void
		*/
		public function phpgw_setcookie($cookiename, $cookievalue='', $cookietime=0)
		{
/*			$secure = phpgw::get_var('HTTPS', 'bool', 'SERVER');

			if( isset( $GLOBALS['phpgw_info']['server']['webserver_url'] ) )
			{
				$webserver_url = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/';
			}
			else
			{
				$webserver_url = '/';
			}
*/
//			setcookie($cookiename, $cookievalue, $cookietime, parse_url($webserver_url, PHP_URL_PATH),
//					$this->_cookie_domain, $secure, true);
			setcookie($cookiename, $cookievalue, $cookietime);
		}


		/**
		* Set the current user id
		*
		* @param int $account_id the account id - 0 = current user's id
		*/
		public function set_account_id($account_id = 0)
		{
			$this->_account_id = get_account_id($account_id);
			$this->_account_lid = $GLOBALS['phpgw']->accounts->id2lid($this->_account_id);
		}

		/**
		* Read session data
		*
		* @param boolean $cached      use cached data
		* @param boolean $write_cache write data to cache
		*
		* @return void
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
	//		$this->hooks = $GLOBALS['phpgw']->hooks->read();
		}

		/**
		 * Read a session
		 *
		 * @param string $sessionid the session id
		 *
		 * @return array session data - empty array when not found
		 */
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
		* @param string $varname name of variable to be registered
		*
		* @return void
		*/
		public function register($varname)
		{
			$this->variableNames[$varname]='registered';
		}

		/**
		 * Store user specific data in the session array
		 *
		 * @param string  $login         the user's login id
		 * @param string  $user_ip       the IP address the user connected from
		 * @param integer $now           current unix timestamp
		 * @param string  $session_flags the flags associated with the session
		 *
		 * @return void
		 */
		public function register_session($login, $user_ip, $now, $session_flags)
		{
			if ( $this->_sessionid )
			{
				session_id($this->_sessionid);
			}

			if ( !strlen(session_id()) )
			{
				throw new Exception("sessions::register_session() - No value for session_id()");
//				session_start();
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

		/**
		 * Restore session data into the global scope
		 *
		 * This function is really crappy and shouldn't be used
		 *
		 * @return void
		 */
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
				}
			}
		}

		/**
		* Save the current values of all registered variables
		*
		 * This function is really crappy and shouldn't be used
		 *
		* @return void
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
				$this->appsession('sessiondata', '', $sessionData);
			}
		}

		/**
		* Cache the session data
		*
		* This will capture everything in the $GLOBALS['phpgw_info'] including server info,
		* and store it in appsessions.  This is really incompatible with any type of restoring
		* from appsession as the saved user info is really in ['user'] rather than the root of
		* the structure, which is what this class likes.
		*
		* @return void
		* @internal this looks to be rather useless in its current state - skwashd may08
		*/
		protected function save_repositories()
		{
			$phpgw_info_temp = $GLOBALS['phpgw_info'];
			$phpgw_info_temp['flags'] = array();

			phpgwapi_cache::session_set('phpgwapi', 'phpgw_info', $phpgw_info_temp);
		}

		/**
		 * Sort 2 session entries
		 *
		 * @param array $a the first session entry
		 * @param array $b the second session entry
		 *
		 * @return integer comparison result based on strcasecmp
		 * @see strcasecmp
		 */
		public static function session_sort($a, $b)
		{
			$sort_by =& $GLOBALS['phpgw']->session->sort_by;
			$sign = strcasecmp($GLOBALS['phpgw']->session->sort_order, 'ASC') ? 1 : -1;

			return strcasecmp($a[$sort_by], $b[$sort_by]) * $sign;
		}

		/**
		 * get number of normal / non-anonymous sessions
		 *
		 * @return integer the total number of sessions
		 */
		public function total()
		{
			return count($this->list_sessions(0, '', '', true));
		}

		/**
		 * Update the last active timestamp for this session
		 *
		 * This prevents sessions timing out - not really needed anymore
		 *
		 * @return boolean was the timestamp updated?
		 */
		public function update_dla()
		{
			session_id($this->_sessionid);

			$menuaction = phpgw::get_var('menuaction');

			if ( $menuaction )
			{
				$action = $menuaction;
			}
			else
			{
				$action = $_SERVER['PHP_SELF'];
			}

			$_SESSION['phpgw_session']['session_dla'] = time();
			$_SESSION['phpgw_session']['session_action'] = $action;

			return true;
		}

		/**
		* Mark variable as unregistered
		*
		* @param string $varname name of variable to deregister
		*
		* @return void
		*/
		public function unregister($varname)
		{
			$this->variableNames[$varname] = 'unregistered';
		}

		/**
		* Check to see if a session is still current and valid
		*
		* @param string $sessionid session id to be verfied
		*
		* @return bool is the session valid?
		*/
		public function verify($sessionid = '')
		{
			if(empty($sessionid) || !$sessionid)
			{
				$sessionid = phpgw::get_var(session_name());
			}

			if(!$sessionid)
			{
				return false;
			}

			$this->_sessionid = $sessionid;

			$session = $this->read_session($sessionid);
			$this->_session_flags = $session['session_flags'];

			$lid_data = explode('#', $session['session_lid']);
			$this->_account_lid = $lid_data[0];

			if ($GLOBALS['phpgw_info']['server']['auth_type'] != 'ntlm') //Timeout make no sense for SSO
			{
				$timeout = time() - $GLOBALS['phpgw_info']['server']['sessions_timeout'];
				if ( !isset($session['session_dla'])
					|| $session['session_dla'] <= $timeout )
				{
					if(isset($session['session_dla']))
					{
						if(is_object($GLOBALS['phpgw']->log))
						{
							$GLOBALS['phpgw']->log->message(array(
								'text' => 'W-VerifySession, session for %1 is expired by %2 sec, inactive for %3 sec',
								'p1'   => $this->_account_lid,
								'p2'   => ($timeout - $session['session_dla']),
								'p3'   => (time() - $session['session_dla']),
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

						$this->cd_reason = 10;
					}
					return false;
				}
			}


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
			//$this->_key = md5($this->_sessionid . $GLOBALS['phpgw_info']['server']['encryptkey']); //Sigurd: not good for permanent data
			$this->_key = $GLOBALS['phpgw_info']['server']['encryptkey'];
			$this->_iv  = $GLOBALS['phpgw_info']['server']['mcrypt_iv'];
			$GLOBALS['phpgw']->crypto->init(array($this->_key, $this->_iv));

			$use_cache = false;
			if ( isset($GLOBALS['phpgw_info']['server']['cache_phpgw_info']) )
			{
				$use_cache = !!$GLOBALS['phpgw_info']['server']['cache_phpgw_info'];
			}

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
				return false;
			}

			$GLOBALS['phpgw_info']['user']  = $this->_data;
	//		$GLOBALS['phpgw_info']['hooks'] = $this->hooks;

			$GLOBALS['phpgw_info']['user']['session_ip'] = $session['session_ip'];
			$GLOBALS['phpgw_info']['user']['passwd']     = phpgwapi_cache::session_get('phpgwapi', 'password');

			if ($this->_account_domain != $GLOBALS['phpgw_info']['user']['domain'])
			{
				if(is_object($GLOBALS['phpgw']->log))
				{
					$GLOBALS['phpgw']->log->message(array(
						'text' => 'W-VerifySession, the domains %1 and %2 don\'t match',
						'p1'   => $this->_account_domain,
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
				return false;
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
				return false;
			}

			$check_ip = false;
			if ( isset($GLOBALS['phpgw_info']['server']['sessions_checkip']) )
			{
				$check_ip = !!$GLOBALS['phpgw_info']['server']['sessions_checkip'];
			}

			if ($check_ip)
			{
				if (PHP_OS != 'Windows' &&
					( !$GLOBALS['phpgw_info']['user']['session_ip']
						|| $GLOBALS['phpgw_info']['user']['session_ip'] != $this->_get_user_ip()) )
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
					return false;
				}
			}
/*
			$GLOBALS['phpgw']->acl->set_account_id($this->_account_id);
			$GLOBALS['phpgw']->accounts->set_account($this->_account_id);
			$GLOBALS['phpgw']->preferences->set_account_id($this->_account_id);
			$GLOBALS['phpgw']->applications->set_account_id($this->_account_id);
*/
			$GLOBALS['phpgw']->translation->populate_cache();

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
				return false;
			}
			return true;
		}

		/**
		* Verfy a peer server access request -DISABLED needs to be audited!
		*
		* @param string $sessionid session id to verfiy
		*
		* @return bool verfied?
		*/
		public function verify_server($sessionid)
		{
			// this is currently broken and unused
			return false;
		}

		/**
		* Get userinfo to pass into $GLOBALS['phpgw_info']['user'] for asyncservice
		*
		* @return array user
		*/
		public function get_user()
		{
			return $this->_data;
		}

		/**
		 * Get a list of currently logged in sessions
		 *
		 * @return array list of sessions
		 */
		protected function _get_list()
		{
			$values = array();

			/*
			   Yes recursive - from the manual
			   There is an optional N argument to this [session.save_path] that determines 
			   the number of directory levels your session files will be spread around in.
			 */
			$path = session_save_path();

			// debian/ubuntu set the perms to /var/lib/php5 and so the sessions can't be read
			if ( !is_readable($path) )
			{
				// FIXME we really should throw an exception here
				$values[] = array
				(
					'id'		=> 'Unable to read sessions',
					'lid'		=> 'invalid',
					'ip'		=> '0.0.0.0',
					'action'	=> 'Access denied by underlying filesystem',
					'dla'		=> 0,
					'logints'	=> 0
				);
				return $values;
			}

			$dir = new RecursiveDirectoryIterator($path);
			foreach ( $dir as $file )
			{
				$filename = $file->getFilename();
				// only try php session files
				if ( !preg_match('/^sess_([a-z0-9]+)$/', $filename) )
				{
					continue;
				}

				$rawdata = file_get_contents("{$path}/{$filename}");

				//taken from http://no.php.net/manual/en/function.session-decode.php#79244
				$vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/',
				$rawdata, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
				$data = array();

		/*		for($i=0; $vars[$i]; $i++)
				{
					$data[$vars[$i++]]=unserialize($vars[$i]);
				}
		*/
				if(isset($vars[3]))
				{
					$data[$vars[0]]=unserialize($vars[1]);
					$data[$vars[2]]=unserialize($vars[3]);
				}

				// skip invalid or anonymous sessions
				if ( !isset($data['phpgw_session'])
					|| $data['phpgw_session']['session_install_id'] != $GLOBALS['phpgw_info']['server']['install_id']
					|| !isset($data['phpgw_session']['session_flags'])
					|| $data['phpgw_session']['session_flags'] == 'A' )
				{
					continue;
				}

				$values[$data['phpgw_session']['session_id']] = array
				(
					'id'				=> $data['phpgw_session']['session_id'],
					'lid'				=> $data['phpgw_session']['session_lid'],
					'ip'				=> $data['phpgw_session']['session_ip'],
					'action'			=> $data['phpgw_session']['session_action'],
					'dla'				=> $data['phpgw_session']['session_dla'],
					'logints'			=> $data['phpgw_session']['session_logintime'],
					'session_file'		=> "{$path}/{$filename}"
				);
			}
			return $values;
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
		* @param string $ip    the ip that made the request
		*
		* @return boolean login blocked?
		*/
		protected function _login_blocked($login, $ip)
		{
			$blocked	= false;
			$block_time = time() - $GLOBALS['phpgw_info']['server']['block_time'] * 60;
			$ip			= $this->_db->db_addslashes($ip);

			if ( isset($GLOBALS['phpgw_info']['server']['sessions_checkip']) && $GLOBALS['phpgw_info']['server']['sessions_checkip'] )
			{
				$sql = 'SELECT COUNT(*) AS cnt FROM phpgw_access_log'
							. " WHERE account_id = 0 AND ip = '{$ip}' AND li > {$block_time}";

				$this->_db->query($sql, __LINE__, __FILE__);
				$this->_db->next_record();

				$false_ip = $this->_db->f('cnt');
				if ( $false_ip > $GLOBALS['phpgw_info']['server']['num_unsuccessful_ip'] )
				{
					$blocked = true;
				}
			}

			$login	= $this->_db->db_addslashes($login);
			$sql	= 'SELECT COUNT(*) AS cnt FROM phpgw_access_log'
					. " WHERE account_id = 0 AND (loginid='{$login}' OR loginid LIKE '$login#%')"
						. " AND li > {$block_time}";
			$this->_db->query($sql, __LINE__, __FILE__);

			$this->_db->next_record();
			$false_id = $this->_db->f('cnt');
			if ( $false_id > $GLOBALS['phpgw_info']['server']['num_unsuccessful_id'] )
			{
				$blocked = true;
			}

			if ( $blocked && isset($GLOBALS['phpgw_info']['server']['admin_mails'])
				&& $GLOBALS['phpgw_info']['server']['admin_mails']
				// max. one mail each 5mins
				&& $GLOBALS['phpgw_info']['server']['login_blocked_mail_time'] < ((time() - 5) * 60) )
			{
				// notify admin(s) via email
				$from    = 'phpGroupWare@'.$GLOBALS['phpgw_info']['server']['mail_suffix'];
				$subject = lang("phpGroupWare: login blocked for user '%1', IP: %2", $login, $ip);
				$body    = lang('Too many unsuccessful attempts to login: '
							. "%1 for the user '%2', %3 for the IP %4", $false_id, $login, $false_ip, $ip);

				if(!is_object($GLOBALS['phpgw']->send))
				{
					$GLOBALS['phpgw']->send = createObject('phpgwapi.send');
				}
				$subject = $GLOBALS['phpgw']->send->encode_subject($subject);
				$admin_mails = explode(',', $GLOBALS['phpgw_info']['server']['admin_mails']);
				foreach($admin_mails as $to)
				{
					$GLOBALS['phpgw']->send->msg('email', $to, $subject,
												$body, '', '', '', $from, $from);
				}
				// save time of mail, to not send to many mails
				$config = createObject('phpgwapi.config', 'phpgwapi');
				$config->read_repository();
				$config->value('login_blocked_mail_time', time());
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
				$parts = explode(':', phpgw::get_var('HTTP_HOST', 'string', 'SERVER')); // strip portnumber if it exists in url (as in 'http://127.0.0.1:8080/')
				$this->_cookie_domain = $parts[0];
			}

			if($this->_cookie_domain == 'localhost')
			{
				$this->_cookie_domain = ''; // Sigurd august 08: somehow setcookie does not accept localhost as a valid domain.
			}
			$secure = phpgw::get_var('HTTPS', 'bool', 'SERVER');

			if( isset( $GLOBALS['phpgw_info']['server']['webserver_url'] ) )
			{
				$webserver_url = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/';
			}
			else
			{
				$webserver_url = '/';
			}

			session_set_cookie_params(0, parse_url($webserver_url, PHP_URL_PATH), $this->_cookie_domain, $secure, true);
			return $this->_cookie_domain;
		}

		/**
		 * setup the user data cache
		 *
		 * @param boolean $write_cache should the cached data be stored?
		 *
		 * @return void
		 */
		protected function _setup_cache($write_cache = true)
		{
			$this->_data                = $GLOBALS['phpgw']->accounts->read()->toArray();
			$this->_data['fullname']	= $GLOBALS['phpgw']->accounts->read()->__toString();
//			$this->_data['acl']         = $GLOBALS['phpgw']->acl->read(); // This one is never used
			$this->_data['preferences'] = $GLOBALS['phpgw']->preferences->read();
			$this->_data['apps']        = $GLOBALS['phpgw']->applications->read();

			$this->_data['domain']      = $this->_account_domain;
			$this->_data['sessionid']   = $this->_sessionid;
			$this->_data['session_ip']  = $this->_get_user_ip();
			$this->_data['session_lid'] = $this->_account_lid.'#'.$this->_account_domain;
			$this->_data['account_id']  = $this->_account_id;
			$this->_data['account_lid'] = $this->_account_lid;
			$this->_data['userid']      = $this->_account_lid;
			$this->_data['passwd']      = $this->_passwd;

			if ( $write_cache )
			{
				phpgwapi_cache::session_set('phpgwapi', 'phpgw_info', $this->_data);
			}
		}

		/**
		 * Set the user's login details
		 *
		 * @param string $login the user login to parse
		 *
		 * @return void
		 */
		protected function _set_login($login)
		{
			$m = array();
			if ( preg_match('/(.*)#(.*)/', $login, $m) )
			{
				$this->_account_lid = $m[1];
				$this->_account_domain = $m[2];
				return;
			}

			$this->_account_lid = $login;
			$this->_account_domain = $GLOBALS['phpgw_info']['server']['default_domain'];
		}

		/**
		* commit the sessiondata to the session handler
		*
		* @return bool
		*/
		function commit_session()
		{
			session_write_close();
			return true;
		}
	}
