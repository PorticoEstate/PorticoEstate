<?php
	/**
	* phpGroupWare Setup - http://phpGroupWare.prg
	* @author Joseph Engo<jengo@phpgroupware.org>
	* @author Dan Kuykendall<seek3r@phpgroupware.org>
	* @author Mark Peters<skeeter@phpgroupware.org>
	* @author Miles Lott<milosch@phpgroupware.org>
	* @copyright Portions Copyright (C) 2001-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*/

	/**
	* Setup
	*
	* @package phpgwapi
	* @subpackage application
	*/
	class phpgwapi_setup
	{
		var $db;
		var $oProc;
		var $hooks;

		var $detection = '';
		var $process = '';
		var $lang = '';
		var $html = '';
		var $appreg = '';

		/* table name vars */
		var $tbl_apps;
		var $tbl_config;
		var $tbl_hooks;
		private $hack_file_name;

		public function __construct($html = False, $translation = False)
		{
			ini_set('session.use_cookies', true);
			$GLOBALS['phpgw_info']['server']['default_lang'] = !empty($GLOBALS['phpgw_info']['server']['default_lang']) ? $GLOBALS['phpgw_info']['server']['default_lang'] : 'en';

			/*
			 * FIXME - do not take effect
			 */
			ini_set('session.cookie_samesite', 'Strict');
			$this->detection = createObject('phpgwapi.setup_detection');
			$this->process   = createObject('phpgwapi.setup_process');
			$_translation    = &$this->process->translation;

			/* The setup application needs these */
			$this->html	= $html ? CreateObject('phpgwapi.setup_html') : null;
			$this->translation = $translation ? $_translation : null ; //CreateObject('phpgwapi.setup_translation') : null;

			//$this->tbl_apps    = $this->get_apps_table_name();
			//$this->tbl_config  = $this->get_config_table_name();
			$this->tbl_hooks   = $this->get_hooks_table_name();

			$temp_dir = sys_get_temp_dir();
			$this->hack_file_name = "$temp_dir/setup_login_hack_prevention.json";
		}

		/**
		 * include api db class for the ConfigDomain and connect to the db
		*
		 */
		function loaddb()
		{
			$form_domain = phpgw::get_var('FormDomain', 'string', 'POST', '');
			$ConfigDomain = phpgw::get_var('ConfigDomain', 'string', 'REQUEST', $form_domain);
			$ConfigDomain   = $ConfigDomain ? $ConfigDomain : phpgw::get_var('ConfigDomain', 'string', 'COOKIE');

			$GLOBALS['phpgw_info']['server']['db_type'] = $GLOBALS['phpgw_domain'][$ConfigDomain]['db_type'];
			$GLOBALS['phpgw_info']['server']['db_host']	= $GLOBALS['phpgw_domain'][$ConfigDomain]['db_host'];
			$GLOBALS['phpgw_info']['server']['db_port']	= $GLOBALS['phpgw_domain'][$ConfigDomain]['db_port'];
			$GLOBALS['phpgw_info']['server']['db_name'] = $GLOBALS['phpgw_domain'][$ConfigDomain]['db_name'];
			$GLOBALS['phpgw_info']['server']['db_user'] = $GLOBALS['phpgw_domain'][$ConfigDomain]['db_user'];
			$GLOBALS['phpgw_info']['server']['db_pass'] = $GLOBALS['phpgw_domain'][$ConfigDomain]['db_pass'];

			$GLOBALS['phpgw_info']['server']['db_abstraction'] = $GLOBALS['phpgw_domain'][$ConfigDomain]['db_abstraction'];
			$this->db	  = createObject('phpgwapi.db', null, null, true);
			$this->db->fetchmode= 'BOTH';
			$GLOBALS['phpgw']->db =& $this->db;

			$GLOBALS['ConfigDomain'] = $ConfigDomain;
		}

		private function _store_login_attempts( $data )
		{
			$fp	= fopen($this->hack_file_name, 'w');
			fputs($fp, json_encode($data));
			fclose($fp);
		}

		private function _get_login_attempts( )
		{
			if(is_file($this->hack_file_name))
			{
				$data = (array)json_decode(file_get_contents($this->hack_file_name), true);
			}
			else
			{
				$data = array();
			}

			return $data;
		}

		/**
		 * authenticate the setup user
		*
		 * @param	$auth_type	???
		 */
		function auth($auth_type='Config')
		{
			$remoteip     = $_SERVER['REMOTE_ADDR'];

			$FormLogout   = phpgw::get_var('FormLogout');
			$ConfigLogin  = phpgw::get_var('ConfigLogin',	'string', 'POST');
			$HeaderLogin  = phpgw::get_var('HeaderLogin',	'string', 'POST');
			$FormDomain   = phpgw::get_var('FormDomain',	'string', 'POST');
			$FormPW       = phpgw::get_var('FormPW',		'string', 'POST');

			$ConfigDomain = phpgw::get_var('ConfigDomain');
			$ConfigPW     = phpgw::get_var('ConfigPW');
			$HeaderPW     = phpgw::get_var('HeaderPW');
			$ConfigLang   = phpgw::get_var('ConfigLang');

			// In case the cookies are not included in $_REQUEST
			$FormLogout   = $FormLogout ? $FormLogout : phpgw::get_var('FormLogout',	'string', 'COOKIE');
			$ConfigDomain = $ConfigDomain ? $ConfigDomain: phpgw::get_var('ConfigDomain',	'string', 'COOKIE');
			$ConfigPW     = $ConfigPW ? $ConfigPW : phpgw::get_var('ConfigPW',	'string', 'COOKIE');
			$HeaderPW     = $HeaderPW ? $HeaderPW : phpgw::get_var('HeaderPW',	'string', 'COOKIE');
			$ConfigLang   = $ConfigLang ? $ConfigLang : phpgw::get_var('ConfigLang',	'string', 'COOKIE');


			/*
			if(!empty($remoteip) && !$this->checkip($remoteip))
			{
				return False;
			}
			*/

			/* 6 cases:
				1. Logging into header admin
				2. Logging into config admin
				3. Logging out of config admin
				4. Logging out of header admin
				5. Return visit to config OR header
				6. None of the above
			*/

			$expire = time() + 1200; /* Expire login if idle for 20 minutes. */

			/**
			 * Block more than 4 failed login attempts within one hour
			 */
			$hack_prevention = $this->_get_login_attempts();

			$ip = phpgw::get_ip_address();

			if(!$ip)
			{
				return false;
			}

			$now = date('Y-m-d:H');

			if(isset($hack_prevention[$ip]['denied'][$now]) && $hack_prevention[$ip]['denied'][$now] > 3)
			{
				$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = $auth_type == 'Header' ? 'To many failed attempts' : '';
				$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = $auth_type == 'Config' ? 'To many failed attempts' : '';
				return False;
			}

			if(!empty($HeaderLogin) && $auth_type == 'Header')
			{
				/* header admin login */
				if($FormPW == $GLOBALS['phpgw']->crypto->decrypt($GLOBALS['phpgw_info']['server']['header_admin_password']))
				{
					$hash = password_hash($FormPW, PASSWORD_BCRYPT);
					setcookie('HeaderPW',$hash,$expire);
					setcookie('ConfigLang',$ConfigLang,$expire);
					if(isset($hack_prevention[$ip]['accepted'][$now]))
					{
						$hack_prevention[$ip]['accepted'][$now] +=1;
					}
					else
					{
						$hack_prevention[$ip]['accepted'][$now] =1;
					}

					$this->_store_login_attempts($hack_prevention);

					return True;
				}
				else
				{
					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = lang('Invalid password');
					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = '';
					if(isset($hack_prevention[$ip]['denied'][$now]))
					{
						$hack_prevention[$ip]['denied'][$now] +=1;
					}
					else
					{
						$hack_prevention[$ip]['denied'][$now] =1;
					}

					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] .= " ({$hack_prevention[$ip]['denied'][$now]})";

					$this->_store_login_attempts($hack_prevention);

					return False;
				}
			}
			elseif(!empty($ConfigLogin) && $auth_type == 'Config')
			{
				/* config login */
				if($FormPW == $GLOBALS['phpgw']->crypto->decrypt($GLOBALS['phpgw_domain'][$FormDomain]['config_passwd']))
				{
					$hash = password_hash($FormPW, PASSWORD_BCRYPT);
					setcookie('ConfigPW', $hash, $expire);
					setcookie('ConfigDomain', $FormDomain, $expire);
					setcookie('ConfigLang', $ConfigLang, $expire);
					if(isset($hack_prevention[$ip]['accepted'][$now]))
					{
						$hack_prevention[$ip]['accepted'][$now] +=1;
					}
					else
					{
						$hack_prevention[$ip]['accepted'][$now] =1;
					}

					$this->_store_login_attempts($hack_prevention);

					return True;
				}
				else
				{
					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = lang('Invalid password');
					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = '';
					if(isset($hack_prevention[$ip]['denied'][$now]))
					{
						$hack_prevention[$ip]['denied'][$now] +=1;
					}
					else
					{
						$hack_prevention[$ip]['denied'][$now] =1;
					}

					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] .= " ({$hack_prevention[$ip]['denied'][$now]})";

					$this->_store_login_attempts($hack_prevention);

					return False;
				}
			}
			elseif(!empty($FormLogout))
			{
				/* logout */
				if($FormLogout == 'config')
				{
					/* config logout */
					setcookie('ConfigPW','');
					$GLOBALS['phpgw_info']['setup']['LastDomain'] = isset($_COOKIE['ConfigDomain']) ? $_COOKIE['ConfigDomain'] : '';
					setcookie('ConfigDomain','');
					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = lang('You have successfully logged out');
					setcookie('ConfigLang','');
					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = '';

					return False;
				}
				elseif($FormLogout == 'header')
				{
					/* header admin logout */
					setcookie('HeaderPW','');
					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = lang('You have successfully logged out');
					setcookie('ConfigLang','');
					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = '';

					return False;
				}
			}
			elseif(!empty($ConfigPW) && $auth_type == 'Config')
			{
				/* Returning after login to config */
				$config_passwd = $GLOBALS['phpgw']->crypto->decrypt($GLOBALS['phpgw_domain'][$ConfigDomain]['config_passwd']);
				if(password_verify($config_passwd, $ConfigPW))
				{
					setcookie('ConfigPW', $ConfigPW,  $expire);
					setcookie('ConfigDomain', $ConfigDomain, $expire);
					setcookie('ConfigLang', $ConfigLang, $expire);
					return True;
				}
				else
				{
					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = lang('Invalid password');
					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = '';
					return False;
				}
			}
			elseif(!empty($HeaderPW) && $auth_type == 'Header')
			{
				/* Returning after login to header admin */
				$header_admin_password = $GLOBALS['phpgw']->crypto->decrypt($GLOBALS['phpgw_info']['server']['header_admin_password']);
				if(password_verify($header_admin_password, $HeaderPW))
				{
					setcookie('HeaderPW', $HeaderPW , $expire);
					setcookie('ConfigLang', $ConfigLang, $expire);
					return True;
				}
				else if(password_verify(stripslashes($GLOBALS['phpgw_info']['server']['header_admin_password']), $HeaderPW))
				{
					setcookie('HeaderPW', $HeaderPW , $expire);
					setcookie('ConfigLang', $ConfigLang, $expire);
					return True;
				}
				else
				{
					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = lang('Invalid password');
					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = '';
					return False;
				}
			}
			else
			{
				$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = '';
				$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = '';
				return False;
			}
		}

		function checkip($remoteip='')
		{
			$allowed_ips = explode(',',$GLOBALS['phpgw_info']['server']['setup_acl']);
			if(is_array($allowed_ips))
			{
				$foundip = False;
				//while(list(,$value) = @each($allowed_ips))
				foreach($allowed_ips as $key => $value)
				{
					$test = preg_split("/\./",$value);
					if(count($test) < 3)
					{
						$value .= ".0.0";
						$tmp = preg_split("/\./",$remoteip);
						$tmp[2] = 0;
						$tmp[3] = 0;
						$testremoteip = join('.',$tmp);
					}
					elseif(count($test) < 4)
					{
						$value .= ".0";
						$tmp = preg_split("/\./",$remoteip);
						$tmp[3] = 0;
						$testremoteip = join('.',$tmp);
					}
					elseif(count($test) == 4 &&
						intval($test[3]) == 0)
					{
						$tmp = preg_split("/\./",$remoteip);
						$tmp[3] = 0;
						$testremoteip = join('.',$tmp);
					}
					else
					{
						$testremoteip = $remoteip;
					}

					//echo '<br>testing: ' . $testremoteip . ' compared to ' . $value;

					if($testremoteip == $value)
					{
						//echo ' - PASSED!';
						$foundip = True;
					}
				}
				if(!$foundip)
				{
					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = '';
					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = lang('Invalid IP address');
					return False;
				}
			}
			return True;
		}

		/**
		 * Return X.X.X major version from X.X.X.X versionstring
		*
		 * @param	$
		 */
		function get_major($versionstring)
		{
			if(!$versionstring)
			{
				return False;
			}

			$version = str_replace('pre','.',$versionstring);
			$varray  = explode('.',$version);
			$major   = implode('.',array($varray[0],$varray[1],$varray[2]));

			return $major;
		}

		/**
		 * Clear system/user level cache so as to have it rebuilt with the next access
		*
		 * @param	None
		 */
		function clear_session_cache()
		{
			$tables = Array();
			$tablenames = $this->db->table_names();
			foreach($tablenames as $key => $val)
			{
				$tables[] = $val;
			}
			if(in_array('phpgw_app_sessions',$tables))
			{
				$this->db->lock(array('phpgw_app_sessions'));
				@$this->db->query("DELETE FROM phpgw_app_sessions WHERE sessionid = '0' and loginid = '0' and app = 'phpgwapi' and location = 'config'",__LINE__,__FILE__);
				@$this->db->query("DELETE FROM phpgw_app_sessions WHERE app = 'phpgwapi' and location = 'phpgw_info_cache'",__LINE__,__FILE__);
				$this->db->unlock();
			}
		}

		/**
		 * Add an application to the phpgw_applications table
		*
		 * @param	$appname	Application 'name' with a matching $setup_info[$appname] array slice
		 * @param	$enable		 * optional, set to True/False to override setup.inc.php setting
		 */
		function register_app($appname,$enable=99)
		{
			$setup_info = $GLOBALS['setup_info'];

			if(!$appname)
			{
				return False;
			}

			if ( $enable == 99 )
			{
				$enable = (int) $setup_info[$appname]['enable'];
			}
			else
			{
				$enable = 0;
			}

			if($GLOBALS['DEBUG'])
			{
				echo '<br>register_app(): ' . $appname . ', version: ' . $setup_info[$appname]['version'] . ', table: phpgw_applications<br>';
			}

			$tables = '';
			if($setup_info[$appname]['version'])
			{
				if ( isset($setup_info[$appname]['tables'])
					&& is_array($setup_info[$appname]['tables']) )
				{
					$tables = implode(',',$setup_info[$appname]['tables']);
				}
				if ( isset($setup_info[$appname]['tables_use_prefix'])
					&& $setup_info[$appname]['tables_use_prefix'] )
				{
					echo $setup_info[$appname]['name'] . ' uses tables_use_prefix, storing '
					. $setup_info[$appname]['tables_prefix']
						. ' as prefix for ' . $setup_info[$appname]['name'] . " tables\n";

					$sql = "INSERT INTO phpgw_config (config_app,config_name,config_value) "
						."VALUES ('".$setup_info[$appname]['name']."','"
						.$appname."_tables_prefix','".$setup_info[$appname]['tables_prefix']."')";
					$this->db->query($sql,__LINE__,__FILE__);
				}
				$this->db->query('INSERT INTO phpgw_applications '
					. '(app_name,app_enabled,app_order,app_tables,app_version) '
					. 'VALUES ('
					. "'{$setup_info[$appname]['name']}', "
					. "$enable, "
					. intval($setup_info[$appname]['app_order']) . ", "
					. "'$tables', "
					. "'{$setup_info[$appname]['version']}')"
					,__LINE__,__FILE__, true
				);
				$this->clear_session_cache();
			}
			// hack to make phpgwapi_applications::name2id to work properly
			unset($GLOBALS['phpgw_info']['apps']);
			$GLOBALS['phpgw']->locations->add('run', "Automatically added on install - run {$appname}", $appname, false);
			$GLOBALS['phpgw']->locations->add('admin', "Allow app admins - {$appname}", $appname, false);
		}

		/**
		 * Check if an application has info in the db
		*
		 * @param	$appname	Application 'name' with a matching $setup_info[$appname] array slice
		 * @param	$enabled	optional, set to False to not enable this app
		 */
		function app_registered($appname)
		{
			$setup_info = $GLOBALS['setup_info'];

			if(!$appname)
			{
				return False;
			}

			if ( isset($GLOBALS['DEBUG']) && $GLOBALS['DEBUG'] )
			{
				echo '<br>app_registered(): checking ' . $appname . ', table: ' . $appstbl;
				// _debug_array($setup_info[$appname]);
			}

			$this->db->query("SELECT COUNT(app_name) as cnt FROM phpgw_applications WHERE app_name='".$appname."'",__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('cnt'))
			{
				if(@$GLOBALS['DEBUG'])
				{
					echo '... app previously registered.';
				}
				return True;
			}
			if(@$GLOBALS['DEBUG'])
			{
				echo '... app not registered';
			}
			return False;
		}

		/**
		 * Update application info in the db
		*
		 * @param	$appname	Application 'name' with a matching $setup_info[$appname] array slice
		 * @param	$enabled	optional, set to False to not enable this app
		 */
		function update_app($appname)
		{
			$setup_info = $GLOBALS['setup_info'];

			if(!$appname)
			{
				return False;
			}

			if($this->alessthanb($setup_info['phpgwapi']['currentver'],'0.9.10pre8') && ($setup_info['phpgwapi']['currentver'] != ''))
			{
				$appstbl = 'applications';
			}
			else
			{
				$appstbl = 'phpgw_applications';
			}

			if($GLOBALS['DEBUG'])
			{
				echo '<br>update_app(): ' . $appname . ', version: ' . $setup_info[$appname]['currentver'] . ', table: ' . $appstbl . '<br>';
				// _debug_array($setup_info[$appname]);
			}

			$this->db->query("SELECT COUNT(app_name) as cnt FROM $appstbl WHERE app_name='".$appname."'",__LINE__,__FILE__);
			$this->db->next_record();
			if(!$this->db->f('cnt'))
			{
				return False;
			}

			if($setup_info[$appname]['version'])
			{
				//echo '<br>' . $setup_info[$appname]['version'];
				$tables = '';
				if ( isset($setup_info[$appname]['tables'])
					&& is_array($setup_info[$appname]['tables']) )
				{
					$tables = implode(',',$setup_info[$appname]['tables']);
				}

				$sql = "UPDATE $appstbl "
					. "SET app_name='{$setup_info[$appname]['name']}',"
					. " app_enabled=" . intval($setup_info[$appname]['enable']) . ","
					. " app_order=" . intval($setup_info[$appname]['app_order']) . ","
					. " app_tables = '$tables',"
					. " app_version = '{$setup_info[$appname]['currentver']}'"
					. " WHERE app_name='" . $appname . "'";
				//echo $sql; exit;

				$this->db->query($sql,__LINE__,__FILE__);
			}
		}

		/**
		 * Update application version in applications table, post upgrade
		*
		 * @param	$setup_info		 * Array of application information (multiple apps or single)
		 * @param	$appname		 * Application 'name' with a matching $setup_info[$appname] array slice
		 * @param	$tableschanged	???
		 */
		function update_app_version($setup_info, $appname, $tableschanged = True)
		{
			if(!$appname)
			{
				return False;
			}

			if($this->alessthanb($setup_info['phpgwapi']['currentver'],'0.9.10pre8') && ($setup_info['phpgwapi']['currentver'] != ''))
			{
				$appstbl = 'applications';
			}
			else
			{
				$appstbl = 'phpgw_applications';
			}

			if($tableschanged == True)
			{
				$GLOBALS['phpgw_info']['setup']['tableschanged'] = True;
			}
			if($setup_info[$appname]['currentver'])
			{
				$this->db->query("UPDATE $appstbl SET app_version='" . $setup_info[$appname]['currentver'] . "' WHERE app_name='".$appname."'",__LINE__,__FILE__);
			}
			return $setup_info;
		}

		/**
		 * de-Register an application
		 *
		 * @param string $appname Application 'name' with a matching $setup_info[$appname] array slice
		 */
		function deregister_app($appname)
		{
			if(!$appname)
			{
				return false;
			}
			$appname = $this->db->db_addslashes($appname);
			$setup_info =& $GLOBALS['setup_info'];

			// Clean up locations, custom fields and ACL
			$this->db->query("SELECT app_id FROM phpgw_applications WHERE app_name = '{$appname}'",__LINE__,__FILE__);
			$this->db->next_record();
			$app_id = (int)$this->db->f('app_id');

			$this->db->query("SELECT location_id FROM phpgw_locations WHERE app_id = {$app_id}",__LINE__,__FILE__);

			$locations = array();
			while ($this->db->next_record())
			{
				$locations[] = $this->db->f('location_id');
			}

			if(count($locations))
			{
				$this->db->query('DELETE FROM phpgw_cust_choice WHERE location_id IN ('. implode (',',$locations) . ')',__LINE__,__FILE__);
				$this->db->query('DELETE FROM phpgw_cust_attribute WHERE location_id IN ('. implode (',',$locations). ')',__LINE__,__FILE__);
				$this->db->query('DELETE FROM phpgw_acl  WHERE location_id IN ('. implode (',',$locations) . ')',__LINE__,__FILE__);

				$this->db->query('SELECT id FROM phpgw_config2_section WHERE location_id IN ('. implode (',',$locations) . ')',__LINE__,__FILE__);
				$sections = array();
				while ($this->db->next_record())
				{
					$sections[] = $this->db->f('id');
				}
				if($sections)
				{
					$this->db->query('DELETE FROM phpgw_config2_value WHERE section_id IN ('. implode (',',$sections) . ')',__LINE__,__FILE__);
					$this->db->query('DELETE FROM phpgw_config2_choice WHERE section_id IN ('. implode (',',$sections) . ')',__LINE__,__FILE__);
					$this->db->query('DELETE FROM phpgw_config2_attrib WHERE section_id IN ('. implode (',',$sections) . ')',__LINE__,__FILE__);
					$this->db->query('DELETE FROM phpgw_config2_section WHERE location_id IN ('. implode (',',$locations) . ')',__LINE__,__FILE__);
				}
			}

			$this->db->query("DELETE FROM phpgw_locations WHERE app_id = {$app_id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_config WHERE config_app='{$appname}'",__LINE__,__FILE__);
			//echo 'DELETING application: ' . $appname;
			$this->db->query("DELETE FROM phpgw_applications WHERE app_name='{$appname}'",__LINE__,__FILE__);
			$this->clear_session_cache();
		}

		/**
		 * Register an application's hooks
		*
		 * @param	$appname	Application 'name' with a matching $setup_info[$appname] array slice
		 */
		function register_hooks($appname)
		{
			$setup_info = $GLOBALS['setup_info'];

			if( !$appname
				|| !isset($setup_info[$appname]['hooks']) )
			{
				return False;
			}

			if ( !isset($this->hooks) || !is_object($this->hooks))
			{
				$this->hooks = createObject('phpgwapi.hooks',$this->db);
			}
			$this->hooks->register_hooks($appname,$setup_info[$appname]['hooks']);
			return true; //i suppose
		}

		/**
		 * Update an application's hooks
		*
		 * @param	$appname	Application 'name' with a matching $setup_info[$appname] array slice
		 */
		function update_hooks($appname)
		{
			$this->register_hooks($appname);
		}

		/**
		 * de-Register an application's hooks
		*
		 * @param	$appname	Application 'name' with a matching $setup_info[$appname] array slice
		 */
		function deregister_hooks($appname)
		{
			if(isset($setup_info['phpgwapi']['currentver']) && $this->alessthanb($setup_info['phpgwapi']['currentver'],'0.9.8pre5'))
			{
				/* No phpgw_hooks table yet. */
				return False;
			}

			if(!$appname)
			{
				return False;
			}

			//echo "DELETING hooks for: " . $setup_info[$appname]['name'];
			if (!is_object($this->hooks))
			{
				$this->hooks = createObject('phpgwapi.hooks',$this->db);
			}
			$this->hooks->register_hooks($appname);
		}

		/**
		  * call the hooks for a single application
		 *
		  * @param $location hook location - required
		  * @param $appname application name - optional
		 */
		function hook($location, $appname='')
		{
			if (!is_object($this->hooks))
			{
				$this->hooks = createObject('phpgwapi.hooks',$this->db);
			}
			return $this->hooks->single($location,$appname,True,True);
		}

		/**
		* phpgw version checking, is param 1 < param 2 in phpgw versionspeak?
		* @param string $a phpgw version number to check if less than $b
		* @param sting $b phpgw version number to check $a against
		* @return bool True if $a < $b
		*/
		function alessthanb($a, $b, $DEBUG=False)
		{
			$num = array('1st','2nd','3rd','4th');

			if($DEBUG)
			{
				echo'<br>Input values: '
					. 'A="'.$a.'", B="'.$b.'"';
			}
			$newa = str_replace('pre','.',$a);
			$newb = str_replace('pre','.',$b);
			$testa = explode('.',$newa);
			if(empty($testa[1]))
			{
				$testa[1] = 0;
			}
			if(empty($testa[3]))
			{
				$testa[3] = 0;
			}
			$testb = explode('.',$newb);
			if(empty($testb[1]))
			{
				$testb[1] = 0;
			}
			if(empty($testb[3]))
			{
				$testb[3] = 0;
			}
			$less = 0;

			for($i=0;$i<count($testa);$i++)
			{
				if($DEBUG) { echo'<br>Checking if '. intval($testa[$i]) . ' is less than ' . intval($testb[$i]) . ' ...'; }
				if(intval($testa[$i]) < intval($testb[$i]))
				{
					if ($DEBUG) { echo ' yes.'; }
					$less++;
					if($i<3)
					{
						/* Ensure that this is definitely smaller */
						if($DEBUG) { echo"  This is the $num[$i] octet, so A is definitely less than B."; }
						$less = 5;
						break;
					}
				}
				elseif(intval($testa[$i]) > intval($testb[$i]))
				{
					if($DEBUG) { echo ' no.'; }
					$less--;
					if($i<2)
					{
						/* Ensure that this is definitely greater */
						if($DEBUG) { echo"  This is the $num[$i] octet, so A is definitely greater than B."; }
						$less = -5;
						break;
					}
				}
				else
				{
					if($DEBUG) { echo ' no, they are equal.'; }
					$less = 0;
				}
			}
			if($DEBUG) { echo '<br>Check value is: "'.$less.'"'; }
			if($less>0)
			{
				if($DEBUG) { echo '<br>A is less than B'; }
				return True;
			}
			elseif($less<0)
			{
				if($DEBUG) { echo '<br>A is greater than B'; }
				return False;
			}
			else
			{
				if($DEBUG) { echo '<br>A is equal to B'; }
				return False;
			}
		}

		/**
		 * phpgw version checking, is param 1 > param 2 in phpgw versionspeak?
		*
		 * @param	$a	phpgw version number to check if more than $b
		 * @param	$b	phpgw version number to check $a against
		 * #return	True if $a < $b
		 */
		function amorethanb($a,$b,$DEBUG=False)
		{
			$num = array('1st','2nd','3rd','4th');

			if($DEBUG)
			{
				echo'<br>Input values: '
					. 'A="'.$a.'", B="'.$b.'"';
			}
			$newa = str_replace('pre','.',$a);
			$newb = str_replace('pre','.',$b);
			$testa = explode('.',$newa);
			if( !isset($testa[3]) || $testa[3] == '')
			{
				$testa[3] = 0;
			}
			$testb = explode('.',$newb);
			if( !isset($testb[3]) || $testb[3] == '')
			{
				$testb[3] = 0;
			}
			$less = 0;

			for($i=0;$i<count($testa);$i++)
			{
				if($DEBUG)
				{
					echo'<br>Checking if '. intval($testa[$i]) . ' is more than ' . intval($testb[$i]) . ' ...';
				}

				if ( isset($testa[$i]) &&  isset($testb[$i])
					&& (int)$testa[$i] > (int)$testb[$i] )
				{
					if($DEBUG) { echo ' yes.'; }
					$less++;
					if($i<3)
					{
						/* Ensure that this is definitely greater */
						if($DEBUG) { echo"  This is the $num[$i] octet, so A is definitely greater than B."; }
						$less = 5;
						break;
					}
				}
				else if ( isset($testa[$i]) &&  isset($testb[$i])
					&& (int)$testa[$i] < (int)$testb[$i] )
				{
					if($DEBUG) { echo ' no.'; }
					$less--;
					if($i<2)
					{
						/* Ensure that this is definitely smaller */
						if($DEBUG) { echo"  This is the $num[$i] octet, so A is definitely less than B."; }
						$less = -5;
						break;
					}
				}
				else
				{
					if($DEBUG) { echo ' no, they are equal.'; }
					$less = 0;
				}
			}
			if($DEBUG) { echo '<br>Check value is: "'.$less.'"'; }
			if($less>0)
			{
				if($DEBUG) { echo '<br>A is greater than B'; }
				return True;
			}
			elseif($less<0)
			{
				if($DEBUG) { echo '<br>A is less than B'; }
				return False;
			}
			else
			{
				if($DEBUG) { echo '<br>A is equal to B'; }
				return False;
			}
		}

		function get_hooks_table_name()
		{

			if ( isset($GLOBALS['setup_info']['phpgwapi']['currentver'])
				&& $this->alessthanb($GLOBALS['setup_info']['phpgwapi']['currentver'], '0.9.8pre5')
				&& ($GLOBALS['setup_info']['phpgwapi']['currentver'] != ''))
			{
				/* No phpgw_hooks table yet. */
				return False;
			}
			return 'phpgw_hooks';
		}
}

