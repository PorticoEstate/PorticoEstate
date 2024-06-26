<?php
	/**
	 * Preferences class for setting application preferences
	 * @author Joseph Engo <jengo@phpgroupware.org>
	 * @author Mark Peters <skeeter@phpgroupware.org>
	 * @copyright Portions Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	 * @package phpgwapi
	 * @subpackage application
	 * @version $Id$
	 */

	/**
	 * Preferences class for setting application preferences
	 *
	 * @package phpgwapi
	 * @subpackage application
	 * @internal the prefs are read into 4 arrays:
	 * @internal $data the effective prefs used everywhere in phpgw, they are merged from the other 3 arrays
	 * @internal $user the stored user prefs, only used for manipulating and storeing the user prefs
	 * @internal $default the default preferences, always used when the user has no own preference set
	 * @internal $forced forced preferences set by the admin, they take precedence over user or default prefs
	 */
	class phpgwapi_preferences
	{
		/**
		 * @var account_id
		 */
		var $account_id;
		/**
		 * @var account_type
		 */
		var $account_type;
		/**
		 * @var data effectiv user prefs, used by all apps
		 */
		var $data = array();

		/**
		 * @var bool $debug are we running in debug mode
		 */
		var $debug = false;

		/**
		 * @var user set user prefs for saveing (no defaults/forced prefs merged)
		 */
		var $user = array();
		/**
		 * @var default default prefs
		 */
		var $default = array();
		/**
		 * @var forced forced prefs
		 */
		var $forced = array();
		/**
		 * @var db
		 */
		var $db;

		var $values,$vars;	// standard notify substitues, will be set by standard_substitues()

		protected $global_lock = false;

		/**
		 * Standard constructor for setting $this->account_id
		 *
		 * Constructor
		 * @param int $account_id the user id for which preferences are sought - 0 == current user
		 */
		public function __construct($account_id = 0)
		{
			$this->db         =& $GLOBALS['phpgw']->db;
			$this->set_account_id($account_id);
		}

		/**
		* Set the current user id
		*
		* @param int $account_id the account id - 0 = current user's id
		*/
		public function set_account_id($account_id = 0, $read_repo = false)
		{
			$this->account_id = get_account_id($account_id);
			if ( $read_repo )
			{
				$this->read_repository();
			}
		}

		/*
		 * These are the standard $this->account_id specific functions
		 */

		/**
		 * parses a notify and replaces the substitutes
		 *
		 * @param $msg message to parse / substitute
		 * @param $values extra vars to replace in addition to $this->values, vars are in an array with \
		 * 	$key => $value pairs, $key does not include the $'s and is the *untranslated* name
		 * @param $use_standard_values should the standard values are used
		 * @return the parsed notify-msg
		 */
		function parse_notify($msg, $values = '', $use_standard_values = true)
		{
			$vals = $values ? $values : array();

			if ($use_standard_values && is_array($this->values))
			{
				$vals += $this->values;
			}
			foreach($vals as $key => $val)
			{
				$replace[] = '$$'.$key.'$$';
				$with[]    = $val;
			}
			return str_replace($replace,$with,$msg);
		}

		/**
		 * replaces the english key's with translated ones, or if $un_lang the opposite
		 *
		 * @param $msg message to translate
		 * @param $values extra vars to replace in addition to $this->values, vars are in an array with \
		 * 	$key => $value pairs, $key does not include the $'s and is the *untranslated* name
		 * @param $un_lang if true translate back
		 * @return the result
		 */
		function lang_notify($msg,$vals=array(),$un_lang=False)
		{
			foreach($vals as $key => $val)
			{
				$lname = ($lname = lang($key)) == $key.'*' ? $key : $lname;
				if ($un_lang)
				{
					$langs[$lname] = '$$'.$key.'$$';
				}
				else
				{
					$langs[$key] = '$$'.$lname.'$$';
				}
			}
			return $this->parse_notify($msg,$langs,False);
		}

		/**
		 * define some standard substitues-values and use them on the prefs, if needed
		 *
		 */
		function standard_substitutes()
		{
			if ( !isset($GLOBALS['phpgw_info']['server']['mail_suffix'])
					|| !$GLOBALS['phpgw_info']['server']['mail_suffix'] )
			{
				$GLOBALS['phpgw_info']['server']['mail_suffix'] = $_SERVER['HTTP_HOST'];
			}

			if ( !isset($GLOBALS['phpgw_info']['user']['preferences'])
					|| !is_array($GLOBALS['phpgw_info']['user']['preferences']) )
			{
				$GLOBALS['phpgw_info']['user']['preferences'] = $this->data;	// else no lang()
				$GLOBALS['phpgw']->translation->userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			}
			// we cant use phpgw_info/user/fullname, as it's not set when we run
			$user = $GLOBALS['phpgw']->accounts->get($this->account_id);

			// standard notify replacements
			$this->values = array
			(
				'fullname'  => $user->__toString(),
				'firstname' => $user->firstname,
				'lastname'  => $user->lastname,
				'domain'    => $GLOBALS['phpgw_info']['server']['mail_suffix'],
				'email'     => $this->email_address($this->account_id),
				'date'      => $GLOBALS['phpgw']->common->show_date('',$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'])
			);
			// do this first, as it might be already contain some substitues
			//
			$this->values['email'] = $this->parse_notify($this->values['email']);

			// langs have to be in common !!!
			$this->vars = array
			(
				'fullname'  => lang('name of the user, eg. %1',$this->values['fullname']),
				'firstname' => lang('first name of the user, eg. %1',$this->values['firstname']),
				'lastname'  => lang('last name of the user, eg. %1',$this->values['lastname']),
				'domain'    => lang('domain name for mail-address, eg. %1',$this->values['domain']),
				'email'     => lang('email-address of the user, eg. %1',$this->values['email']),
				'date'      => lang('todays date, eg. %1',$this->values['date'])
			);
			// do the substituetion in the effective prefs (data)
			//
			foreach($this->data as $app => $data)
			{
				foreach($data as $key => $val)
				{
					if (!is_array($val) && strstr($val,'$$') !== False)
					{
						$this->data[$app][$key] = $this->parse_notify($val);
					}
					elseif (is_array($val))
					{
						foreach($val as $k => $v)
						{
							if ( !is_array($v) && strstr($v, '$$') !== False)
							{
								$this->data[$app][$key][$k] = $this->parse_notify($v);
							}
						}
					}
				}
			}
		}

		/**
		 * unquote (stripslashes) recursivly the whole array
		 *
		 * @param $arr array to unquote (var-param!)
		 */
		public function unquote(&$arr)
		{
			if (!is_array($arr))
			{
				$arr = stripslashes($arr);
				return;
			}
			foreach($arr as $key => $value)
			{
				if (is_array($value))
				{
					$this->unquote($arr[$key]);
				}
				else
				{
					$arr[$key] = stripslashes($value);
				}
			}
		}

		/**
		 * private - read preferences from the repository
		 *
		 * the function ready all 3 prefs user/default/forced and merges them to the effective ones
		 * private function should only be called from within this class
		 */
		protected function read_repository()
		{
			//FIXME - ever used?
			if($GLOBALS['phpgw']->acl->check('session_only_preferences',1,'preferences') && strlen($GLOBALS['phpgw']->session->appsession('session_prefs','initiated')))
			{
				$this->user		= $GLOBALS['phpgw']->session->appsession('session_prefs','user');
				$this->default	= $GLOBALS['phpgw']->session->appsession('session_prefs','default');
				$this->forced	= $GLOBALS['phpgw']->session->appsession('session_prefs','forced');

				if(!is_array($this->user))
				{
					$this->user = array();
				}
				if(!is_array($this->forced))
				{
					$this->forced = array();
				}
				if(!is_array($this->default))
				{
					$this->default = array();
				}
			}
			else
			{
//				$metadata = $this->db->metadata('phpgw_preferences');
				$this->db->query('SELECT * FROM phpgw_preferences WHERE preference_owner IN (-1,-2,'.intval($this->account_id).')',__LINE__,__FILE__);

				$this->forced = $this->default = $this->user = array();
				while($this->db->next_record())
				{
					// The following ereg is required for PostgreSQL to work
					$app = str_replace(' ','',$this->db->f('preference_app'));

					$value = json_decode($this->db->f('preference_json'), true);

					$this->unquote($value);
					if (!is_array($value))
					{
						continue;
					}
					switch($this->db->f('preference_owner'))
					{
						case -1:	// forced
							$this->forced[$app] = $value;
							break;
						case -2:	// default
							$this->default[$app] = $value;
							break;
						default:	// user
							$this->user[$app] = $value;
							break;
					}
				}

				if ($GLOBALS['phpgw']->acl->check('session_only_preferences',1,'preferences'))
				{
					$GLOBALS['phpgw']->session->appsession('session_prefs', 'initiated', 'yes');
					$GLOBALS['phpgw']->session->appsession('session_prefs', 'forced', $this->forced);
					$GLOBALS['phpgw']->session->appsession('session_prefs', 'default', $this->default);
					$GLOBALS['phpgw']->session->appsession('session_prefs', 'user', $this->user);
				}
			}
			$this->data = $this->user;

			// now use defaults if needed (user-value unset or empty)
			//
			foreach($this->default as $app => $values)
			{
				foreach($values as $var => $value)
				{
					if (!isset($this->data[$app][$var]) || $this->data[$app][$var] === '')
					{
						$this->data[$app][$var] = $value;
					}
				}
			}
			// now set/force forced values
			//
			foreach($this->forced as $app => $values)
			{
				foreach($values as $var => $value)
				{
					$this->data[$app][$var] = $value;
				}
			}
			// setup the standard substitues and substitues the data in $this->data
			//
			$this->standard_substitutes();

			// This is to supress warnings durring login
			if (is_array($this->data))
			{
				reset($this->data);

				// This one should cope with daylight saving time
				// Create two timezone objects, one for UTC (GMT) and one for
				// user pref
				$dateTimeZone_utc = new DateTimeZone('UTC');
				$dateTimeZone_pref = new DateTimeZone(isset($this->data['common']['timezone']) && $this->data['common']['timezone'] ? $this->data['common']['timezone'] : 'UTC');

				// Create two DateTime objects that will contain the same Unix timestamp, but
				// have different timezones attached to them.
				$dateTime_utc = new DateTime("now", $dateTimeZone_utc);
				$dateTime_pref = new DateTime("now", $dateTimeZone_pref);

				// Calculate the GMT offset for the date/time contained in the $dateTime_utc
				// object, but using the timezone rules as defined for Tokyo

				$this->data['common']['tz_offset'] = (int)$dateTimeZone_pref->getOffset($dateTime_utc)/3600;
			}
			if ($this->debug && substr($GLOBALS['phpgw_info']['flags']['currentapp'],0,3) != 'log') 
			{
				echo "user<pre>";    print_r($this->user); echo "</pre>\n";
				echo "forced<pre>";  print_r($this->forced); echo "</pre>\n";
				echo "default<pre>"; print_r($this->default); echo "</pre>\n";
				echo "effectiv<pre>";print_r($this->data); echo "</pre>\n";
			}

			return $this->data;
		}

		/**
		 * public - read preferences from repository and stores in an array
		 *
		 * Syntax array read(); <>
		 * Example1: preferences->read();
		 * @return $data array containing user preferences
		 */
		public function read()
		{
			if (count($this->data) == 0)
			{
				$this->read_repository();
			}
			reset ($this->data);
			return $this->data;
		}

		/**
		 * add preference to $app_name a particular app
		 *
		 * @param $app_name name of the app
		 * @param $var name of preference to be stored
		 * @param $value value of the preference
		 * @param $type of preference to set: forced, default, user
		 * the effective prefs ($this->data) are updated to reflect the change
		 * @return the new effective prefs (even when forced or default prefs are set !)
		 */
		public function add($app_name,$var,$value = '##undef##',$type='user')
		{
			//echo "<p>add('$app_name','$var','$value')</p>\n";
			if ($value == '##undef##')
			{
				global $$var;
				$value = $$var;
			}

			switch ($type)
			{
				case 'forced':
					$this->data[$app_name][$var] = $this->forced[$app_name][$var] = $value;
					break;

				case 'default':
					$this->default[$app_name][$var] = $value;
					if ((!isset($this->forced[$app_name][$var]) || $this->forced[$app_name][$var] === '') &&
							(!isset($this->user[$app_name][$var]) || $this->user[$app_name][$var] === ''))
					{
						$this->data[$app_name][$var] = $value;
					}
					break;

				case 'user':
				default:
					$this->user[$app_name][$var] = $value;
					if (!isset($this->forced[$app_name][$var]) || $this->forced[$app_name][$var] === '')
					{
						$this->data[$app_name][$var] = $value;
					}
					break;
			}
			reset($this->data);
			return $this->data;
		}

		/**
		 * delete preference from $app_name
		 *
		 * @param $app_name name of app
		 * @param $var variable to be deleted
		 * @param $type of preference to set: forced, default, user
		 * the effektive prefs ($this->data) are updated to reflect the change
		 * @return the new effective prefs (even when forced or default prefs are deleted!)
		 */
		public function delete($app_name, $var = false,$type = 'user')
		{
			//echo "<p>delete('$app_name','$var')</p>\n";
			$set_via = array(
					'forced'  => array('user','default'),
					'default' => array('forced','user'),
					'user'    => array('forced','default')
					);
			if (!isset($set_via[$type]))
			{
				$type = 'user';
			}
			if ($all = !$var)
			{
				unset($this->{$type}[$app_name]);
				unset($this->data[$app_name]);
			}
			else
			{
				if ( isset($this->{$type}[$app_name][$var]) )
				{
					unset($this->{$type}[$app_name][$var]);
				}

				if ( isset($this->data[$app_name][$var]) )
				{
					unset($this->data[$app_name][$var]);
				}
			}
			// set the effectiv pref again if needed
			//
			foreach ($set_via[$type] as $set_from)
			{
				if ($all)
				{
					if (isset($this->{$set_from}[$app_name]))
					{
						$this->data[$app_name] = $this->{$set_from}[$app_name];
						break;
					}
				}
				else
				{
					if (isset($this->{$set_from}[$app_name][$var]) && $this->{$set_from}[$app_name][$var] !== '')
					{
						$this->data[$app_name][$var] = $this->{$set_from}[$app_name][$var];
						break;
					}
				}
			}
			reset ($this->data);
			return $this->data;
		}

		/**
		 * add complex array data preference to $app_name a particular app
		 *
		 * Use for sublevels of prefs, such as email app's extra accounts preferences
		 * @param $app_name name of the app
		 * @param $var array keys separated by '/', eg. 'ex_accounts/1'
		 * @param $value value of the preference
		 * the function works on user and data, to be able to save the pref and to have imediate effect
		 */
		public function add_struct($app_name,$var,$value = '')
		{
			/* eval is slow and dangerous
			   $code = '$this->data[$app_name]'.$var.' = $value;';
			   print_debug('class.preferences: add_struct: $code: ', $code,'api');
			   eval($code);
			 */
			$parts = explode('/',str_replace(array('][','[',']','"',"'"),array('/','','','',''),$var));
			$data = &$this->data[$app_name];
			$user = &$this->user[$app_name];
			foreach($parts as $name)
			{
				$data = &$data[$name];
				$user = &$user[$name];
			}
			$data = $user = $value;
			print_debug('class.preferences: add_struct: $this->data[$app_name] dump:', $this->data[$app_name],'api');
			reset($this->data);
			return $this->data;
		}

		/**
		 * delete complex array data preference from $app_name
		 *
		 * Use for sublevels of prefs, such as email app's extra accounts preferences
		 * @param $app_name name of app
		 * @param $var array keys separated by '/', eg. 'ex_accounts/1'
		 * the function works on user and data, to be able to save the pref and to have imediate effect
		 */
		public function delete_struct($app_name, $var = '')
		{
			/* eval is slow and dangerous
			   $code_1 = '$this->data[$app_name]'.$var.' = "";';
			   print_debug('class.preferences: delete_struct: $code_1:', $code_1,'api');
			   eval($code_1);
			   $code_2 = 'unset($this->data[$app_name]'.$var.');' ;
			   print_debug('class.preferences: delete_struct:  $code_2: ', $code_2,'api');
			   eval($code_2);
			 */
			$parts = explode('/',str_replace(array('][','[',']','"',"'"),array('/','','','',''),$var));
			$last = array_pop($parts);
			$data = &$this->data[$app_name];
			$user = &$this->user[$app_name];
			foreach($parts as $name)
			{
				$data = &$data[$name];
				$user = &$user[$name];
			}
			unset($data[$last]);
			unset($user[$last]);
			print_debug('* $this->data[$app_name] dump:', $this->data[$app_name],'api');
			reset ($this->data);
			return $this->data;
		}

		/**
		 * quote (addslashes) recursivly the whole array
		 *
		 * @param $arr array to unquote (var-param!)
		 */
		public function quote(&$arr)
		{
			if (!is_array($arr))
			{
				$arr = $this->db->db_addslashes($arr);
				return;
			}
			foreach($arr as $key => $value)
			{
				if (is_array($value))
				{
					$this->quote($arr[$key]);
				}
				else
				{
					$arr[$key] = $this->db->db_addslashes($value);
				}
			}
		}

		/**
		 * save the the preferences to the repository
		 *
		 * @param $update_session_info old param, seems not to be used
		 * @param $type which prefs to update: user/default/forced
		 * the user prefs for saveing are in $this->user not in $this->data, which are the effectiv prefs only
		 */
		public function save_repository($update_session_info = False,$type='user')
		{
			// Don't get the old values back from the cache on next load
			phpgwapi_cache::session_clear('phpgwapi', 'phpgw_info');

			switch($type)
			{
				case 'forced':
					$account_id = -1;
					$prefs = &$this->forced;
					break;
				case 'default':
					$account_id = -2;
					$prefs = &$this->default;
					break;
				default:
					$account_id = intval($this->account_id);
					$prefs = &$this->user;	// we use the user-array as data contains default values too
					break;
			}
			//echo "<p>preferences::save_repository(,$type): account_id=$account_id, prefs="; print_r($prefs); echo "</p>\n";

			if (! $GLOBALS['phpgw']->acl->check('session_only_preferences',1,'preferences'))
			{
				if ( $this->db->get_transaction() )
				{
					$this->global_lock = true;
				}
				else
				{
					$this->db->transaction_begin();
				}

				$this->db->query("delete from phpgw_preferences where preference_owner='$account_id'",
						__LINE__,__FILE__);

				/**
				 * Temporary
				 */
//				$metadata = $this->db->metadata('phpgw_preferences');

				foreach($prefs as $app => $value)
				{
					if (!is_array($value))
					{
						continue;
					}
					$this->quote($value);

//					if(!isset($metadata['preference_json']))
//					{
//						$value = $this->db->db_addslashes(serialize($value));	// this addslashes is for the database
//						$app = $this->db->db_addslashes($app);
//
//						$this->db->query("INSERT INTO phpgw_preferences".
//								" (preference_owner,preference_app,preference_value)".
//								" VALUES ($account_id,'$app','$value')",__LINE__,__FILE__);
//					}
//					else
					{
						$value_set = array
						(
							'preference_owner' => $account_id,
							'preference_app'	=> $app,
							'preference_json'	=> json_encode($value)
						);

						$cols = implode(',', array_keys($value_set));
						$insert_values = $this->db->validate_insert(array_values($value_set));

						$this->db->query("INSERT INTO phpgw_preferences ({$cols}) VALUES ({$insert_values})", __LINE__, __FILE__);

					}
				}

				if ( !$this->global_lock )
				{
					$this->db->transaction_commit();
				}
			}
			else
			{
				switch($type)
				{
					case 'forced':
						$GLOBALS['phpgw']->session->appsession('session_prefs', 'forced', $this->forced);
						break;
					case 'default':
						$GLOBALS['phpgw']->session->appsession('session_prefs', 'default', $this->default);
						break;
					default:
						$GLOBALS['phpgw']->session->appsession('session_prefs', 'user', $this->user);
						break;
				}

			//	$GLOBALS['phpgw_info']['user']['preferences'] = $this->data;
				$GLOBALS['phpgw']->session->save_repositories();
			}

			$GLOBALS['phpgw_info']['user']['preferences'] = $this->data;

			if (($type == 'user' || !$type) && isset($GLOBALS['phpgw_info']['server']['cache_phpgw_info']) && $GLOBALS['phpgw_info']['server']['cache_phpgw_info']  && $this->account_id == $GLOBALS['phpgw_info']['user']['account_id'])
			{
				$GLOBALS['phpgw']->session->delete_cache($this->account_id);
				$GLOBALS['phpgw']->session->read_repositories(False);
			}

			return $this->data;
		}

		/**
		 * insert a copy of the default preferences for use by real account_id
		 *
		 * @param $account_id numerical id of account for which to create the prefs
		 */
		public function create_defaults($account_id)
		{
			return; // not longer needed, as the defaults are merged in on runtime
			$this->db->query("select * from phpgw_preferences where preference_owner='-2'",__LINE__,__FILE__);
			$this->db->next_record();

			if($this->db->f('preference_value'))
			{
				$this->db->query("insert into phpgw_preferences values ('$account_id','"
						. $this->db->f('preference_value') . "')",__LINE__,__FILE__);
			}

			if ($GLOBALS['phpgw_info']['server']['cache_phpgw_info'] && $account_id == $GLOBALS['phpgw_info']['user']['account_id'])
			{
				$GLOBALS['phpgw']->session->read_repositories(False);
			}

		}

		/**
		 * update the preferences array
		 *
		 *
		 * @param $data array of preferences
		 */
		public function update_data($data)
		{
			if(is_array($data))
			{
				reset($data);
			}
			else
			{
				$data = array();
			}
			$this->data = Array();
			$this->data = $data;
			reset($this->data);
			return $this->data;
		}

		/* legacy support */
		public function change($app_name,$var,$value = "")
		{
			return $this->add($app_name,$var,$value);
		}
		public function commit($update_session_info = True)
		{
			//return $this->save_repository($update_session_info);
		}

		/**************************************************************************\
		 * These are the non-standard $this->account_id specific functions          *
		 \**************************************************************************/

		/**
		 * verify basic settings for a user
		 *
		 */
		function verify_basic_settings()
		{
			$preferences_update = False;
			if ( !is_array($GLOBALS['phpgw_info']['user']['preferences']) 
//				|| isset($GLOBALS['phpgw_info']['server']['cache_phpgw_info'])
				)
			{
				$GLOBALS['phpgw_info']['user']['preferences'] = $this->read_repository();
//				$preferences_update = True;
			}
			/* This takes care of new users who dont have proper default prefs setup */
			if (!isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) ||
					!$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'])
			{
				$this->add('common','maxmatchs',15);
				$preferences_update = True;
			}
			if (!isset($GLOBALS['phpgw_info']['user']['preferences']['common']['theme']) ||
					!$GLOBALS['phpgw_info']['user']['preferences']['common']['theme'])
			{
				$this->add('common','theme','default');
				$preferences_update = True;
			}
			if (!isset($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set']) ||
					!$GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'])
			{
				$this->add('common','template_set','bootstrap');
				$preferences_update = True;
			}
			if (!isset($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) ||
					!$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'])
			{
				$this->add('common','dateformat','d-M-Y');
				$preferences_update = True;
			}
			if (!isset($GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat']) ||
					!$GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'])
			{
				$this->add('common','timeformat',12);
				$preferences_update = True;
			}
			if (!isset($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']) ||
					!$GLOBALS['phpgw_info']['user']['preferences']['common']['lang'])
			{
				$this->add('common','lang',$GLOBALS['phpgw']->common->getPreferredLanguage());
				$preferences_update = True;
			}
			if ($preferences_update)
			{
			//	$this->update_data($user_prefs); // doing nothing?
				$this->save_repository();
				$GLOBALS['phpgw_info']['user']['preferences'] = $this->read_repository();
			}
		}

		/****************************************************\
		 * Email Preferences and Private Support Functions   *
		 \****************************************************/

		/**
		 * Helper function for create_email_preferences, gets mail server port number.
		 *
		 * This will generate the appropriate port number to access a
		 * mail server of type pop3, pop3s, imap, imaps users value from
		 * $GLOBALS['phpgw_info']['user']['preferences']['email']['mail_port'].
		 * if that value is not set, it generates a default port for the given $server_type.
		 * Someday, this *MAY* be
		 * (a) a se4rver wide admin setting, or
		 * (b)user custom preference
		 * Until then, simply set the port number based on the mail_server_type, thereof
		 * ONLY call this function AFTER ['email']['mail_server_type'] has been set.
		 * @param $prefs - user preferences array based on element ['email'][]
		 * @author  Angles
		 * @access Private
		 */
		function sub_get_mailsvr_port($prefs, $acctnum=0)
		{
			$port_number = 0;
			// first we try the port number supplied in preferences
			if ( (isset($prefs['email']['accounts'][$acctnum]['mail_port']))
					&& ($prefs['email']['accounts'][$acctnum]['mail_port'] != '') )
			{
				$port_number = $prefs['email']['accounts'][$acctnum]['mail_port'];
			}
			// preferences does not have a port number, generate a default value
			else if ( isset($prefs['email']['accounts']) && is_array($prefs['email']['accounts']) )
			{
				if (!isset($prefs['email']['accounts'][$acctnum]['mail_server_type']))
				{
					$prefs['email']['accounts'][$acctnum]['mail_server_type'] = $prefs['email']['mail_server_type'];
				}

				switch($prefs['email']['accounts'][$acctnum]['mail_server_type'])
				{
					case 'pop3s':
						// POP3 over SSL
						$port_number = 995;
						break;
					case 'pop3':
						// POP3 normal connection, No SSL
						// ( same string as normal imap above)
						$port_number = 110;
						break;
					case 'nntp':
						// NNTP news server port
						$port_number = 119;
						break;
					case 'imaps':
						// IMAP over SSL
						$port_number = 993;
						break;
					case 'imap':
						// IMAP normal connection, No SSL
					default:
						// UNKNOWN SERVER in Preferences, return a
						// default value that is likely to work
						// probably should raise some kind of error here
						$port_number = 143;
						break;
				}
			}
			return $port_number;
		}

		/**
		 * Helper function for create_email_preferences, gets default userid for email
		 *
		 * This will generate the appropriate userid for accessing an email server.
		 * In the absence of a custom ['email']['userid'], this function should be used to set it.
		 * @param $accountid - as determined in and/or passed to "create_email_preferences"
		 * @access Private
		 */
		function sub_default_userid($account_id='')
		{
			$prefs_email_userid = $GLOBALS['phpgw']->accounts->id2lid($account_id, true);

			if (isset($GLOBALS['phpgw_info']['server']['mail_login_type']) )
			{
				if ( $GLOBALS['phpgw_info']['server']['mail_login_type'] == 'vmailmgr' )
				{
					$prefs_email_userid .= "@{$GLOBALS['phpgw_info']['server']['mail_suffix']}";
				}
				else if ( $GLOBALS['phpgw_info']['server']['mail_login_type'] == 'ispman' )
				{
					$prefs_email_userid .= '_' . str_replace('.', '_', $GLOBALS['phpgw_info']['server']['mail_suffix']);
				}
			}
			return $prefs_email_userid;
		}

		/**
		 * returns the custom email-address (if set) or generates a default one
		 *
		 * This will generate the appropriate email address used as the "From:"
		 email address when the user sends email, the localpert * part. The "personal"
		 part is generated elsewhere.
		 In the absence of a custom ['email']['address'], this function should be used to set it.
		 * @param $accountid - as determined in and/or passed to "create_email_preferences"
		 * @access public
		 */
		public function email_address($account_id='')
		{
			if ( isset($this->data['email']['address']) )
			{
				return $this->data['email']['address'];
			}

			$prefs_email_address = $GLOBALS['phpgw']->accounts->id2lid($account_id);

			if ( !preg_match('/@/', $prefs_email_address) )
			{
				$prefs_email_address .= "@{$GLOBALS['phpgw_info']['server']['mail_suffix']}";
			}
			return $prefs_email_address;
		}

		function sub_default_address($account_id='')
		{
			return $this->email_address($account_id);
		}

		/**
		 * create email preferences
		 *
		 * @param $account_id -optional defaults to : get_account_id()
		 * fills a local copy of ['email'][] prefs array which is then returned to the calling
		 * function, which the calling function generally tacks onto the $GLOBALS['phpgw_info'] array as such:
		 * 	$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->create_email_preferences();
		 * which fills an array based at:
		 * 	$GLOBALS['phpgw_info']['user']['preferences']['email'][prefs_are_elements_here]
		 * Reading the raw preference DB data and comparing to the email preference schema defined in
		 * /email/class.bopreferences.inc.php (see discussion there and below) to create default preference values
		 * for the  in the ['email'][] pref data array in cases where the user has not supplied
		 * a preference value for any particular preference item available to the user.
		 * @access Public
		 */
		public function create_email_preferences($accountid='', $acctnum=0)
		{
			print_debug('class.preferences: create_email_preferences: ENTERING<br>', 'messageonly','api');
			// we may need function "html_quotes_decode" from the mail_msg class
			$email_base = createObject("email.mail_msg");

			$account_id = get_account_id($accountid);
			// If the current user is not the request user, grab the preferences
			// and reset back to current user.
			if($account_id != $this->account_id)
			{
				// Temporarily store the values to a temp, so when the
				// read_repository() is called, it doesn't destory the
				// current users settings.
				$temp_account_id = $this->account_id;
				$temp_data = $this->data;

				// Grab the new users settings, only if they are not the
				// current users settings.
				$this->account_id = $account_id;
				$prefs = $this->read_repository();

				// Reset the data to what it was prior to this call
				$this->account_id = $temp_account_id;
				$this->data = $temp_data;
			}
			else
			{
				$prefs = $this->data;
			}
			// are we dealing with the default email account or an extra email account?
			if ($acctnum != 0)
			{
				// prefs are actually a sub-element of the main email prefs
				// at location [email][ex_accounts][X][...pref names] => pref values
				// make this look like "prefs[email] so the code below code below will do its job transparently

				// store original prefs
				$orig_prefs = array();
				$orig_prefs = $prefs;
				// obtain the desired sub-array of extra account prefs
				$sub_prefs = array();
				$sub_prefs['email'] = $prefs['email']['ex_accounts'][$acctnum];
				// make the switch, make it seem like top level email prefs
				$prefs = array();
				$prefs['email'] = $sub_prefs['email'];
				// since we return just $prefs, it's up to the calling program to put the sub prefs in the right place
			}
			print_debug('class.preferences: create_email_preferences: $acctnum: ['.$acctnum.'] ; raw $this->data dump', $this->data,'api');

			// = = = =  NOT-SIMPLE  PREFS  = = = =
			// Default Preferences info that is:
			// (a) not controlled by email prefs itself (mostly api and/or server level stuff)
			// (b) too complicated to be described in the email prefs data array instructions

			// ---  [server][mail_server_type]  ---
			// Set API Level Server Mail Type if not defined
			// if for some reason the API didnot have a mail server type set during initialization
			if (empty($GLOBALS['phpgw_info']['server']['mail_server_type']))
			{
				$GLOBALS['phpgw_info']['server']['mail_server_type'] = 'imap';
			}

			// ---  [server][mail_folder]  ---
			// ====  UWash Mail Folder Location used to be "mail", now it's changeable, but keep the
			// ====  default to "mail" so upgrades happen transparently
			// ---  TEMP MAKE DEFAULT UWASH MAIL FOLDER ~/mail (a.k.a. $HOME/mail)
			$GLOBALS['phpgw_info']['server']['mail_folder'] = 'mail';
			// ---  DELETE THE ABOVE WHEN THIS OPTION GETS INTO THE SYSTEM SETUP
			// pick up custom "mail_folder" if it exists (used for UWash and UWash Maildir servers)
			// else use the system default (which we temporarily hard coded to "mail" just above here)

			//---  [email][mail_port]  ---
			// These sets the mail_port server variable
			// someday (not currently) this may be a site-wide property set during site setup
			// additionally, someday (not currently) the user may be able to override this with
			// a custom email preference. Currently, we simply use standard port numbers
			// for the service in question.
			$prefs['email']['mail_port'] = $this->sub_get_mailsvr_port($prefs);

			//---  [email][fullname]  ---
			// we pick this up from phpgw api for the default account
			// the user does not directly manipulate this pref for the default email account
			if ((string)$acctnum == '0')
			{
				$prefs['email']['fullname'] = (string) $GLOBALS['phpgw']->accounts->get($account_id);
			}


			// = = = =  SIMPLER PREFS  = = = =

			// Default Preferences info that is articulated in the email prefs schema array itself
			// such email prefs schema array is described and established in /email/class.bopreferences
			// by function "init_available_prefs", see the discussion there.

			// --- create the objectified /email/class.bopreferences.inc.php ---
			$bo_mail_prefs = createObject('email.bopreferences');

			// --- bo_mail_prefs->init_available_prefs() ---
			// this fills object_email_bopreferences->std_prefs and ->cust_prefs
			// we will initialize the users preferences according to the rules and instructions
			// embodied in those prefs arrays, applying those rules to the unprocessed
			// data read from the preferences DB. By taking the raw data and applying those rules,
			// we will construct valid and known email preference data for this user.
			if (is_object($bo_mail_prefs))
			{
				$bo_mail_prefs->init_available_prefs();

				// --- combine the two array (std and cust) for 1 pass handling ---
				// when this preference DB was submitted and saved, it was hopefully so well structured
				// that we can simply combine the two arrays, std_prefs and cust_prefs, and do a one
				// pass analysis and preparation of this users preferences.
				$avail_pref_array = $bo_mail_prefs->std_prefs;
				$c_cust_prefs = count($bo_mail_prefs->cust_prefs);
				for	($i=0;$i<$c_cust_prefs;$i++)
				{
					// add each custom prefs to the std prefs array
					$next_idx = count($avail_pref_array);
					$avail_pref_array[$next_idx] = $bo_mail_prefs->cust_prefs[$i];
				}
				print_debug('class.preferences: create_email_preferences: std AND cust arrays combined:', $avail_pref_array,'api');
			}

			// --- make the schema-based pref data for this user ---
			// user defined values and/or user specified custom email prefs are read from the
			// prefs DB with mininal manipulation of the data. Currently the only change to
			// users raw data is related to reversing the encoding of "database un-friendly" chars
			// which itself may become unnecessary if and when the database handlers can reliably
			// take care of this for us. Of course, password data requires special decoding,
			// but the password in the array [email][paswd] should be left in encrypted form
			// and only decrypted seperately when used to login in to an email server.

			// --- generating a default value if necessary ---
			// in the absence of a user defined custom email preference for a particular item, we can
			// determine the desired default value for that pref as such:
			// $this_avail_pref['init_default']  is a comma seperated seperated string which should
			// be exploded into an array containing 2 elements that are:
			// exploded[0] : an description of how to handle the next string element to get a default value.
			// Possible "instructional tokens" for exploded[0] (called $set_proc[0] below) are:
			//	string
			//	set_or_not
			//	function
			//	init_no_fill
			//	varEVAL
			// tells you how to handle the string in exploded[1] (called $set_proc[1] below) to get a valid
			// default value for a particular preference if one is needed (i.e. if no user custom
			// email preference exists that should override that default value, in which case we
			// do not even need to obtain such a default value as described in ['init_default'] anyway).

			// --- loop thru $avail_pref_array and process each pref item ---
			$c_prefs = count($avail_pref_array);
			for($i=0;$i<$c_prefs;$i++)
			{
				$this_avail_pref = $avail_pref_array[$i];
				//@ is used here as it is needed for this crappy code
				@print_debug('class.preferences: create_email_preferences: value from DB for $prefs[email]['.$this_avail_pref['id'].'] = ['.$prefs['email'][$this_avail_pref['id']].']', 'messageonly','api');
				@print_debug('class.preferences: create_email_preferences: std/cust_prefs $this_avail_pref['.$i.'] dump:', $this_avail_pref,'api');

				// --- is there a value in the DB for this preference item ---
				// if the prefs DB has no value for this defined available preference, we must make one.
				// This occurs if (a) this is user's first login, or (b) this is a custom pref which the user
				// has not overriden, do a default (non-custom) value is needed.
				if (!isset($prefs['email'][$this_avail_pref['id']]))
				{
					// now we are analizing an individual pref that is available to the user
					// AND the user had no existing value in the prefs DB for this.

					// --- get instructions on how to generate a default value ---
					$set_proc = explode(',', $this_avail_pref['init_default']);
					print_debug(' * set_proc=['.serialize($set_proc).']', 'messageonly','api');

					// --- use "instructional token" in $set_proc[0] to take appropriate action ---
					// STRING
					if ($set_proc[0] == 'string')
					{
						// means this pref item's value type is string
						// which defined string default value is in $set_proc[1]
						print_debug('* handle "string" set_proc: ', serialize($set_proc),'api');
						if (trim($set_proc[1]) == '')
						{
							// this happens when $this_avail_pref['init_default'] = "string, "
							$this_string = '';
						}
						else
						{
							$this_string = $set_proc[1];
						}
						$prefs['email'][$this_avail_pref['id']] = $this_string;
					}
					// SET_OR_NOT
					elseif ($set_proc[0] == 'set_or_not')
					{
						// typical with boolean options, True = "set/exists" and False = unset
						print_debug('* handle "set_or_not" set_proc: ', serialize($set_proc),'api');
						if ($set_proc[1] == 'not_set')
						{
							// leave it NOT SET
						}
						else
						{
							// opposite of boolean not_set  = string "True" which simply sets a
							// value it exists in the users session [email][] preference array
							$prefs['email'][$this_avail_pref['id']] = 'True';
						}
					}
					// FUNCTION
					elseif ($set_proc[0] == 'function')
					{
						// string in $set_proc[1] should be "eval"uated as code, calling a function
						// which will give us a default value to put in users session [email][] prefs array
						print_debug(' * handle "function" set_proc: ', serialize($set_proc),'api');
						$evaled = '';
						//eval('$evaled = $this->'.$set_proc[1].'('.$account_id.');');

						$code = '$evaled = $this->'.$set_proc[1].'('.$account_id.');';
						print_debug(' * $code: ', $code,'api');
						eval($code);

						print_debug('* $evaled:', $evaled,'api');
						$prefs['email'][$this_avail_pref['id']] = $evaled;
					}
					// INIT_NO_FILL
					elseif ($set_proc[0] == 'init_no_fill')
					{
						// we have an available preference item that we may NOT fill with a default
						// value. Only the user may supply a value for this pref item.
						print_debug('* handle "init_no_fill" set_proc:', serialize($set_proc),'api');
						// we are FORBADE from filling this at this time!
					}
					// varEVAL
					elseif ($set_proc[0] == 'varEVAL')
					{
						// similar to "function" but used for array references, the string in $set_proc[1]
						// represents code which typically is an array referencing a system/api property
						print_debug('* handle "GLOBALS" set_proc:', serialize($set_proc),'api');
						$evaled = '';
						$code = '$evaled = '.$set_proc[1];
						print_debug(' * $code:', $code,'api');
						@eval($code); //@ to supress errors created during eval - ugly hack i know
						print_debug('* $evaled:', $evaled,'api');
						$prefs['email'][$this_avail_pref['id']] = $evaled;
					}
					else
					{
						// error, no instructions on how to handle this element's default value creation
						echo 'class.preferences: create_email_preferences: set_proc ERROR: '.serialize($set_proc).'<br>';
					}
				}
				else
				{
					// we have a value in the database, do we need to prepare it in any way?
					// (the following discussion is unconfirmed:)
					// DO NOT ALTER the data in the prefs array!!!! or the next time we call
					// save_repository withOUT undoing what we might do here, the
					// prefs will permenantly LOOSE the very thing(s) we are un-doing
					/// here until the next OFFICIAL submit email prefs function, where it
					// will again get this preparation before being written to the database.

					// NOTE: if database de-fanging is eventually handled deeper in the
					// preferences class, then the following code would become depreciated
					// and should be removed in that case.
					if (($this_avail_pref['type'] == 'user_string') &&
							(stristr($this_avail_pref['write_props'], 'no_db_defang') == False))
					{
						// this value was "de-fanged" before putting it in the database
						// undo that defanging now
						$db_unfriendly = $email_base->html_quotes_decode($prefs['email'][$this_avail_pref['id']]);
						$prefs['email'][$this_avail_pref['id']] = $db_unfriendly;
					}
				}
			}
			// users preferences are now established to known structured values...

			// SANITY CHECK
			// ---  [email][use_trash_folder]  ---
			// ---  [email][use_sent_folder]  ---
			// is it possible to use Trash and Sent folders - i.e. using IMAP server
			// if not - force settings to false
			if (stristr($prefs['email']['mail_server_type'], 'imap') == False)
			{
				if (isset($prefs['email']['use_trash_folder']))
				{
					unset($prefs['email']['use_trash_folder']);
				}

				if (isset($prefs['email']['use_sent_folder']))
				{
					unset($prefs['email']['use_sent_folder']);
				}
			}

			// DEBUG : force some settings to test stuff
			//$prefs['email']['p_persistent'] = 'True';

			print_debug('class.preferences: $acctnum: ['.$acctnum.'] ; create_email_preferences: $prefs[email]', $prefs['email'],'api');
			print_debug('class.preferences: create_email_preferences: LEAVING', 'messageonly','api');
			return $prefs;
		}

		/*
		// ==== DEPRECIATED - ARCHIVAL CODE ====
		// used to be part of function this->create_email_preferences()
		// = = = =  SIMPLER PREFS  = = = =
		// Default Preferences info that is:
		// described in the email prefs array itself

		$default_trash_folder = 'Trash';
		$default_sent_folder = 'Sent';

		// ---  userid  ---
		if (!isset($prefs['email']['userid']))
		{
		$prefs['email']['userid'] = $this->sub_default_userid($accountid);
		}
		// ---  address  ---
		if (!isset($prefs['email']['address']))
		{
		$prefs['email']['address'] = $this->email_address($accountid);
		}
		// ---  mail_server  ---
		if (!isset($prefs['email']['mail_server']))
		{
		$prefs['email']['mail_server'] = $GLOBALS['phpgw_info']['server']['mail_server'];
		}
		// ---  mail_server_type  ---
		if (!isset($prefs['email']['mail_server_type']))
		{
		$prefs['email']['mail_server_type'] = $GLOBALS['phpgw_info']['server']['mail_server_type'];
		}
		// ---  imap_server_type  ---
		if (!isset($prefs['email']['imap_server_type']))
		{
		$prefs['email']['imap_server_type'] = $GLOBALS['phpgw_info']['server']['imap_server_type'];
		}
		// ---  mail_folder  ---
		// because of the way this option works, an empty string IS ACTUALLY a valid value
		// which represents the $HOME/* as the UWash mail files location
		// THERFOR we must check the "Use_custom_setting" option to help us figure out what to do
		if (!isset($prefs['email']['use_custom_settings']))
		{
		// we are NOT using custom settings so this MUST be the server default
		$prefs['email']['mail_folder'] = $GLOBALS['phpgw_info']['server']['mail_folder'];
		}
		else
		{
		// we ARE using custom settings AND a BLANK STRING is a valid option, so...
		if ((isset($prefs['email']['mail_folder']))
		&& ($prefs['email']['mail_folder'] != ''))
		{
		// using custom AND a string exists, so "mail_folder" is that string stored in the custom prefs by the user
		// DO NOTING - VALID OPTION VALUE for $prefs['email']['mail_folder']
		}
		else
		{
		// using Custom Prefs BUT this text box was left empty by the user on submit, so no value stored
		// BUT since we are using custom prefs, "mail_folder" MUST BE AN EMPTY STRING
		// which is an acceptable, valid preference, overriding any value which
		// may have been set in ["server"]["mail_folder"]
		// This is one of the few instances in the preference class where an empty, unspecified value
		// actually does NOT get deleted from the repository.
		$prefs['email']['mail_folder'] = '';
		}
		}

		// ---  use_trash_folder  ---
		// ---  trash_folder_name  ---
		// if the option to use the Trash folder is ON, make sure a proper name is specified
		if (isset($prefs['email']['use_trash_folder']))
		{
		if ((!isset($prefs['email']['trash_folder_name']))
		|| ($prefs['email']['trash_folder_name'] == ''))
		{
			$prefs['email']['trash_folder_name'] = $default_trash_folder;
		}
	}

	// ---  use_sent_folder  ---
	// ---  sent_folder_name  ---
	// if the option to use the sent folder is ON, make sure a proper name is specified
	if (isset($prefs['email']['use_sent_folder']))
	{
		if ((!isset($prefs['email']['sent_folder_name']))
				|| ($prefs['email']['sent_folder_name'] == ''))
		{
			$prefs['email']['sent_folder_name'] = $default_sent_folder;
		}
	}

	// ---  layout  ---
	// Layout Template Preference
	// layout 1 = default ; others are prefs
	if (!isset($prefs['email']['layout']))
	{
		$prefs['email']['layout'] = 1;
	}

	//// ---  font_size_offset  ---
	//// Email Index Page Font Size Preference
	//// layout 1 = default ; others are prefs
	//if (!isset($prefs['email']['font_size_offset']))
	//{
	//	$prefs['email']['font_size_offset'] = 'normal';
	//}

	// SANITY CHECK
	// ---  use_trash_folder  ---
	// ---  use_sent_folder  ---
	// is it possible to use Trash and Sent folders - i.e. using IMAP server
	// if not - force settings to false
	if (stristr($prefs['email']['mail_server_type'], 'imap') == False)
	{
		if (isset($prefs['email']['use_trash_folder']))
		{
			unset($prefs['email']['use_trash_folder']);
		}

		if (isset($prefs['email']['use_sent_folder']))
		{
			unset($prefs['email']['use_sent_folder']);
		}
	}

	// DEBUG : force some settings to test stuff
	//$prefs['email']['layout'] = 1;
	//$prefs['email']['layout'] = 2;
	//$prefs['email']['font_size_offset'] = (-1);

	// DEBUG
	//echo "<br>prefs['email']: <br>"
	//	.'<pre>'.serialize($prefs['email']) .'</pre><br>';
	return $prefs;
	*/
	} /* end of preferences class */
