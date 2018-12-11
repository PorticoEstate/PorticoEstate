<?php
	/**
	* Commononly used functions
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage utilities
	* @version $Id$
	*/

	/**
	* Import required classes
	*/
	phpgw::import_class('phpgwapi.datetime');

	/**
	* Commononly used functions
	*
	* @package phpgwapi
	* @subpackage utilities
	*/
	class phpgwapi_common
	{
		/**
		* An array with debugging info from the API
		* @var array Debugging info from the API
		*/
		var $debug_info;

		/**
		* @var array $output array to be converted by XSLT
		*/
		public $output = array();

		protected $webserver_url;

		public function __construct()
		{
			$webserver_url = $GLOBALS['phpgw_info']['server']['webserver_url'];
			$this->webserver_url = $webserver_url;
		}

		/**
		* This function compares for major versions only
		*
		* @param string $str1 Version string 1
		* @param string $str2 Version string 2
		* @param boolean $debug Debug flag
		* @return integer 1 when str2 is newest (bigger version number) than str1
		*/
		public function cmp_version($str1,$str2,$debug=False)
		{
			preg_match("/([0-9]+)\.([0-9]+)\.([0-9]+)[a-z]*([0-9]*)/i", $str1, $regs);
			preg_match("/([0-9]+)\.([0-9]+)\.([0-9]+)[a-z]*([0-9]*)/i", $str2, $regs2);
			if($debug) { echo "<br />$regs[0] - $regs2[0]"; }

			for($i=1;$i<5;++$i)
			{
				if($debug) { echo "<br />$i: $regs[$i] - $regs2[$i]"; }
				if($regs2[$i] == $regs[$i])
				{
					continue;
				}
				if($regs2[$i] > $regs[$i])
				{
					return 1;
				}
				elseif($regs2[$i] < $regs[$i])
				{
					return 0;
				}
			}
		}

		/**
		* This function compares for major and minor versions
		*
		* @param string $str1 Version string 1
		* @param string $str2 Version string 2
		* @param boolean $debug Debug flag
		* @return integer 1 when str2 is newest (bigger version number) than str1
		*/
		public function cmp_version_long($str1, $str2, $debug = false)
		{
			$regs = explode('.', $str1);
			$regs2 = explode('.', $str2);
			if($debug) { echo "<br />$regs[0] - $regs2[0]"; }

			for($i=1;$i<6;++$i)
			{
				if(!isset($regs2[$i]) && !isset($regs[$i]))
				{
					continue;
				}

				if($debug) { echo "<br />$i: $regs[$i] - $regs2[$i]"; }

				if($regs2[$i] == $regs[$i])
				{
					if($debug) { echo ' are equal...'; }
					continue;
				}
				if($regs2[$i] > $regs[$i])
				{
					if($debug) { echo ', and a > b'; }
					return 1;
				}
				elseif($regs2[$i] < $regs[$i])
				{
					if($debug) { echo ', and a < b'; }
					return 0;
				}
			}
			if($debug) { echo ' - all equal.'; }
		}

		/**
		* This function is used for searching the access fields
		*
		* @param string $table Table name
		* @param integer $owner User ID
		* @return string SQL where clause
		* @deprecated Use ACL class instead
		*/
		public function sql_search($table, $owner=0 )
		{
			echo 'common::sql_search() is a deprecated function - use ACL class instead';
			if (!$owner)
			{
				$owner = $GLOBALS['phpgw_info']['user']['account_id'];
			}
			$groups = $GLOBALS['phpgw']->accounts->membership(intval($owner));
			if( is_array($groups) )
			{
				$s = " OR $table IN (0";
				foreach($groups as $group)
				{
					$s .= ", {$group[2]}";
				}
				$s .= ')';
			}
			return $s;
		}

		/**
		* Get list of installed languages
		*
		* @return array List of installed languages
		*/
		public function getInstalledLanguages()
		{
			$GLOBALS['phpgw']->db->query('select distinct lang from phpgw_lang');
			while ($GLOBALS['phpgw']->db->next_record())
			{
				$installedLanguages[$GLOBALS['phpgw']->db->f('lang')] = $GLOBALS['phpgw']->db->f('lang');
			}

			return $installedLanguages;
		}

		/**
		* Get preferred language of the users
		*
		* Uses HTTP_ACCEPT_LANGUAGE (from the users browser) to find out which languages are installed
		* @return string Users preferred language (two character ISO code)
		*/
		public function getPreferredLanguage()
		{
			// create a array of languages the user is accepting
			$userLanguages = explode(',', phpgw::get_var('HTTP_ACCEPT_LANGUAGE', 'string', 'SERVER'));
			$supportedLanguages = $this->getInstalledLanguages();

			// find usersupported language
			foreach($userLanguages as $key => $value)
			{
				// remove everything behind '-' example: de-de
				$value = trim($value);
				$pieces = explode('-', $value);
				$value = $pieces[0];
				# print 'current lang $value<br>';
				if ($supportedLanguages[$value])
				{
					$retValue=$value;
					break;
				}
			}

			// no usersupported language found -> return english
			if (empty($retValue))
			{
				$retValue='en';
			}

			return $retValue;
		}

		/**
		* Connect to the ldap server and return a handle
		*
		* @param string $host LDAP host name
		* @param string $dn LDAP distinguised name
		* @param string $passwd LDAP password
		* @return resource LDAP link identifier
		*/
		public function ldapConnect($host = '', $dn = '', $passwd = '')
		{
			if (! $host)
			{
				$host = $GLOBALS['phpgw_info']['server']['ldap_host'];
			}

			if (! $dn)
			{
				$dn = $GLOBALS['phpgw_info']['server']['ldap_root_dn'];
			}

			if (! $passwd)
			{
				$passwd = $GLOBALS['phpgw_info']['server']['ldap_root_pw'];
			}

			// connect to ldap server
			if (! $ds = ldap_connect($host))
			{
				/* log does not exist in setup(, yet) */
				if(is_object($GLOBALS['phpgw']->log))
				{
					$GLOBALS['phpgw']->log->message('F-Abort, Failed connecting to LDAP server');
					$GLOBALS['phpgw']->log->commit();
				}

				printf("<b>Error: Can't connect to LDAP server %s!</b><br>",$host);
				return False;
			}
			if(! @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3))//LDAP protocol v3 support
			{
				if(is_object($GLOBALS['phpgw']->log))
				{
					//$GLOBALS['phpgw']->log->message('set_option(protocol v3) failed using v2');
					//$GLOBALS['phpgw']->log->commit();
				}
			}
			else
			{
				if(is_object($GLOBALS['phpgw']->log))
				{
					//$GLOBALS['phpgw']->log->message('set_option(protocol v3) succeded using v3');
					//$GLOBALS['phpgw']->log->commit();
				}
			}
			// bind as admin, we not to able to do everything
			if (! ldap_bind($ds,$dn,$passwd))
			{
				if(is_object($GLOBALS['phpgw']->log))
				{
					$GLOBALS['phpgw']->log->message('F-Abort, Failed binding to LDAP server');
					$GLOBALS['phpgw']->log->commit();
				}

				printf("<b>Error: Can't bind to LDAP server: %s!</b><br>",$dn);
				return False;
			}

			return $ds;
		}

		/**
		* Function to stop running an application
		*
		* Used to stop running an application in the middle of execution
		* @internal There may need to be some cleanup before hand
		* @param boolean $call_footer When true then call footer else exit
		*/
		public function phpgw_exit($call_footer = False)
		{
			if (!defined('PHPGW_EXIT'))
			{
				define('PHPGW_EXIT',True);

				if ($call_footer)
				{
					$this->phpgw_footer();
				}
			}
			exit;
		}

		/**
		* Do some cleaning up before we exit
		*
		* @internal called by register_shutdown_function()
		*/
		public function phpgw_final()
		{
			static $final_called = null;
			if ( is_null($final_called) )
			{
				// call the asyncservice check_run function if it is not explicitly set to cron-only
				if ( !isset($GLOBALS['phpgw_info']['server']['asyncservice'])
					|| !$GLOBALS['phpgw_info']['server']['asyncservice'] 
					|| $GLOBALS['phpgw_info']['server']['asyncservice'] == 'fallback')
				{
					ExecMethod('phpgwapi.asyncservice.check_run', 'fallback');
				}
				$GLOBALS['phpgw']->session->commit_session();
				$GLOBALS['phpgw']->db->disconnect();
				$final_called = true;
			}
		}

		/**
		* Get random string of size $size
		*
		* @param integer $size Size of random string to return
		* @return string randomly generated characters
		*/
		public function randomstring($size = 20)
		{
			$s = '';
			$random_char = array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f',
				'g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v',
				'w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L',
				'M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

			for ($i=0; $i<$size; ++$i)
			{
				$s .= $random_char[mt_rand(1,61)];
			}
			return $s;
		}

		/**
		* This is used for reporting errors in a nice format
		*
		* @param array $error List of errors
		* @param string $text Heading error text
		* @return string HTML table with error messages or empty string when there is no error/s
		*/
		public function error_list($errors, $text = 'Error')
		{
			if ( !is_array($errors) || !count($errors) )
			{
				return '';
			}

			$text = lang($text);
			$html_error = <<<HTML
				<h3>$text</h3>
					<ul>

HTML;
			foreach ( $errors as $error )
			{
				$html_error .= <<<HTML
						<li>{$error}</li>

HTML;
			}
			$html_error .= <<<HTML
				</ul>

HTML;
			return $html_error;
		}

		/**
		* Create a link when user is the owner otherwise &nbsp;
		*
		* @param integer $record User or account id
		* @param string $link URL
		* @param string $label Link name
		* @param array $extravars URL parameter
		* @deprecated use ACL instead
		*/
		public function check_owner($record, $link, $label, $extravars = array())
		{
			echo 'check_owner() is a depreciated function - use ACL class instead <br>';
			$s = '<a href="' . $GLOBALS['phpgw']->link($link,$extravars) . '"> ' . lang($label) . ' </a>';
			if ( preg_match('/^[0-9]+$/',$record))
			{
				if ($record != $GLOBALS['phpgw_info']['user']['account_id'])
				{
					$s = '&nbsp;';
				}
			}
			else
			{
				if ($record != $GLOBALS['phpgw_info']['user']['account_lid'])
				{
					$s = '&nbsp;';
				}
			}

			return $s;
		}

		/**
		* Get fullname of a user
		*
		* @param string $lid Account login id
		* @param string $firstname Firstname
		* @param string $lastname Lastname
		* @return Fullname
		*/
		public function display_fullname($lid = '', $firstname = '', $lastname = '')
		{
			if (! $lid && ! $firstname && ! $lastname)
			{
				$lid       = isset($GLOBALS['phpgw_info']['user']['account_lid']) ? $GLOBALS['phpgw_info']['user']['account_lid']	: '';
				$firstname = isset($GLOBALS['phpgw_info']['user']['account_firstname']) ? $GLOBALS['phpgw_info']['user']['account_firstname']	: '';
				$lastname  = isset($GLOBALS['phpgw_info']['user']['account_lastname']) ? $GLOBALS['phpgw_info']['user']['account_lastname']	: '';
			}

			$display = 'firstname';
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['common']['account_display']) )
			{
				$display = $GLOBALS['phpgw_info']['user']['preferences']['common']['account_display'];
			}

			if (!$firstname && !$lastname || $display == 'username')
			{
				return ''; //$lid;
			}
			if ($lastname)
			{
				$a[] = $lastname;
			}

			if ($firstname)
			{
				$a[] = $firstname;
			}

			$name = '';
			switch($display)
			{
				case 'all':
				case 'lastname':
					$name .= implode(', ',$a);
					break;
				case 'firstall':
				case 'firstname':
				default:
					$name = $firstname . ' ' . $lastname;
			}
			return $name;
		}

		/**
		* Shows the applications preferences and admin links
		*
		* @param string $appname the application name
		* @param array menu data
		* @returns array menu data
		*/
		public function display_mainscreen($appname, $file)
		{
			if(is_array($file))
			{
				$icon = $this->image($appname,'navbar','',($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set']=='funkwerk'?True:False));

				if ( count($file) )
				{
					foreach ( $file as $text => $url )
					{
						$link_data[] = array
						(
							'pref_link'	=> $url,
							'pref_text'	=> lang($text)
						);
					}
				}

				$this->output['app_row_icon'][] = array
				(
					'layout'	=> $GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'],
					'app_title' 	=> lang($appname),
					'app_name'	=> $appname,
					'app_icon'	=> $icon,
					'link_row'	=> $link_data
				);
			}
		}

		/**
		* Grab the owner name
		*
		* @param integer $accountid Account id
		*
		* @return string Users fullname
		*/
		public function grab_owner_name($accountid = null)
		{
			return (string) $GLOBALS['phpgw']->accounts->get($accountid);
		}

		/**
		* Create tabs
		*
		* @param array   $tabs      With ($id,$tab) pairs
		* @param integer $selection array key of selected tab
		* @param boolean $lang      Translate label?
		*
		* @return string html snippet for creating tabs in a modern browser
		*/
		public function create_tabs($tabs, $selection, $lang = false)
		{
			phpgw::import_class('phpgwapi.jquery');
			if ( $lang )
			{
				foreach ( $tabs as &$tab )
				{
					$tab = lang($tab);
				}
			}

			return  phpgwapi_jquery::tabview_generate($tabs, $selection);
		}

		/**
		* Get directory of application
		*
		* @param string $appname Name of application defaults to $GLOBALS['phpgw_info']['flags']['currentapp']
		* @return string|boolean Application directory or false
		*/
		public function get_app_dir($appname = '')
		{
			if ($appname == '')
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			if ($appname == 'home' || $appname == 'logout' || $appname == 'login')
			{
				$appname = 'phpgwapi';
			}

			$appdir         = PHPGW_INCLUDE_ROOT . '/'.$appname;
			$appdir_default = PHPGW_SERVER_ROOT . '/'.$appname;

			if (@is_dir ($appdir))
			{
				return $appdir;
			}
			elseif (@is_dir ($appdir_default))
			{
				return $appdir_default;
			}
			else
			{
				return False;
			}
		}

		/**
		* Get include directory of application
		*
		* @param string $appname Name of application, defaults to $GLOBALS['phpgw_info']['flags']['currentapp']
		* @return string|boolean Include directory or false
		*/
		public function get_inc_dir($appname = '')
		{
			if (! $appname)
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			if ($appname == 'home' || $appname == 'logout' || $appname == 'login')
			{
				$appname = 'phpgwapi';
			}

			$incdir         = PHPGW_INCLUDE_ROOT . '/' . $appname . '/inc';
			$incdir_default = PHPGW_SERVER_ROOT . '/' . $appname . '/inc';

			if (@is_dir ($incdir))
			{
				return $incdir;
			}
			elseif (@is_dir ($incdir_default))
			{
				return $incdir_default;
			}
			else
			{
				return False;
			}
		}

		/**
		* List themes available
		*
		* Themes are CSS files stored under the template directory
		* @return array List with available themes
		*/
		public static function list_themes($layout = '')
		{
			$tpl_dir = self::get_tpl_dir('phpgwapi', $layout);

			$css_dir = "$tpl_dir/themes";

			$list = array();
			if ( !is_dir($css_dir) )
			{
				return $list;
			}

			if ( $dh = opendir($css_dir) )
			{
				while ($file = readdir($dh))
				{
					if (preg_match('/^[a-z0-9\-_]+\.css$/', $file) && $file != 'base.css' && $file != 'login.css')
					{
						$list[] = substr($file, 0, strpos($file, '.') );
					}
				}
				closedir($dh);
			}
			sort($list);
			return $list;
		}

		/**
		* List available templates
		*
		* @return array Alphabetically sorted list of available templates
		*/
		public static function list_templates()
		{
			$ignore_list = array('.', '..', 'CVS', '.svn', 'default', 'phpgw_website', 'base');

			$list = array();

			$dirname = PHPGW_SERVER_ROOT . '/phpgwapi/templates';

			$dir = new DirectoryIterator($dirname);
			foreach ( $dir as $file )
			{
				$entry = (string) $file;
				if ( !in_array($entry, $ignore_list) && $file->isDir() )
				{
					$list[$entry]['title'] = $entry;

					$f = "{$dirname}/{$entry}/details.inc.php";
					if ( file_exists($f) )
					{
						require_once $f;
						$list[$entry]['title'] = lang ('Use %s interface', $GLOBALS['phpgw_info']['template'][$entry]['title']);
					}
				}
			}
			ksort($list);
			return $list;
		}

		/**
		* Get template dir of an application
		*
		* @param string $appname application name optional can be derived from $GLOBALS['phpgw_info']['flags']['currentapp'];
		* @param string? $layout optional can force the template set to a specific layout
		*/
		public static function get_tpl_dir($appname = '',$layout = '')
		{
			if (! $appname)
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			if ($appname == 'home' || $appname == 'logout' || $appname == 'login' || $appname == 'about' || $appname == 'help')
			{
				$appname = 'phpgwapi';
			}

			if (!isset($GLOBALS['phpgw_info']['server']['template_set']) && isset($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set']))
			{
				$GLOBALS['phpgw_info']['server']['template_set'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'];
			}

			// Setting this for display of template choices in user preferences
			if (@$GLOBALS['phpgw_info']['server']['template_set'] == 'user_choice')
			{
				$GLOBALS['phpgw_info']['server']['usrtplchoice'] = 'user_choice';
			}

			if ((@$GLOBALS['phpgw_info']['server']['template_set'] == 'user_choice' ||
				!isset($GLOBALS['phpgw_info']['server']['template_set'])) &&
				isset($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set']))
			{
				$GLOBALS['phpgw_info']['server']['template_set'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'];
			}
			elseif (@$GLOBALS['phpgw_info']['server']['template_set'] == 'user_choice' ||
				!isset($GLOBALS['phpgw_info']['server']['template_set']))
			{
				$GLOBALS['phpgw_info']['server']['template_set'] = 'base';
			}

			$tpldir         = PHPGW_SERVER_ROOT . "/{$appname}/templates/{$GLOBALS['phpgw_info']['server']['template_set']}";
			$tpldir_default = PHPGW_SERVER_ROOT . "/{$appname}/templates/base";

			if ($layout)
			{
				$tpldir = $tpldir_default = PHPGW_SERVER_ROOT . "/{$appname}/templates/{$layout}";
			}

			if (is_dir($tpldir))
			{
				return $tpldir;
			}
			elseif (is_dir($tpldir_default))
			{
				return $tpldir_default;
			}
			else
			{
				return False;
			}
		}

		/**
		* Test if image directory exists and has more than just a navbar-icon
		*
		* @param string $dir Image directory
		* @return boolean True when it is an image directory, otherwise false.
		* @internal This is just a workaround for idots, better to use find_image, which has a fallback on a per image basis to the default dir
		*/
		public function is_image_dir($dir)
		{
			if (!@is_dir($dir))
			{
				return False;
			}
			if ($d = opendir($dir))
			{
				while ($f = readdir($d))
				{
					$ext = strtolower(strrchr($f,'.'));
					if ( $ext == '.png' && strstr($f,'navbar') === False)
					{
						return True;
					}
				}
			}
			return False;
		}

		/**
		* Get image directory of an application
		*
		* @param string $appname Application name, defaults to $GLOBALS['phpgw_info']['flags']['currentapp']
		* @return string|boolean Image directory of given application or false
		*/
		public function get_image_dir($appname = '')
		{
			if ($appname == '')
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			if (empty($GLOBALS['phpgw_info']['server']['template_set']))
			{
				$GLOBALS['phpgw_info']['server']['template_set'] = 'base';
			}

			$imagedir			= PHPGW_SERVER_ROOT . '/' . $appname . '/templates/' . $GLOBALS['phpgw_info']['server']['template_set'] . '/images';
			$imagedir_default	= PHPGW_SERVER_ROOT . '/' . $appname . '/templates/base/images';

			if ($this->is_image_dir ($imagedir))
			{
				return $imagedir;
			}
			elseif ($this->is_image_dir ($imagedir_default))
			{
				return $imagedir_default;
			}
			else
			{
				return False;
			}
		}

		/**
		* Get image path of an application
		*
		* @param string $appname Appication name, defaults to $GLOBALS['phpgw_info']['flags']['currentapp']
		* @return string|boolean Image directory path of given application or false
		*/
		public function get_image_path($appname = '')
		{
			if ($appname == '')
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			if (empty($GLOBALS['phpgw_info']['server']['template_set']))
			{
				$GLOBALS['phpgw_info']['server']['template_set'] = 'simple';
			}

			$imagedir			= PHPGW_SERVER_ROOT . '/'.$appname.'/templates/'.$GLOBALS['phpgw_info']['server']['template_set'].'/images';
			$imagedir_default	= PHPGW_SERVER_ROOT . '/'.$appname.'/templates/base/images';

			if ($this->is_image_dir($imagedir))
			{
				return $this->webserver_url.'/'.$appname.'/templates/'.$GLOBALS['phpgw_info']['server']['template_set'].'/images';
			}
			elseif ($this->is_image_dir ($imagedir_default))
			{
				return $this->webserver_url.'/'.$appname.'/templates/base/images';
			}
			else
			{
				return False;
			}
		}

		/**
		* Find an image
		*
		* @internal caches look ups for faster response times on subsequent searches
		* @param string $module the module to check first for the image
		* @param string $image the image to look for - without the extension, this is added during the checks
		* @return string the URL pointing to the image
		*/
		public static function find_image($module, $image)
		{
			static $webserver_url = null;

			if(!$webserver_url)
			{
				$webserver_url = $GLOBALS['phpgw_info']['server']['webserver_url'];
			}

			static $found_files = null;
			if ( !isset($found_files[$module]) || is_array($found_files[$module]) )
			{
				$paths = array
				(
					"/{$module}/templates/base/images",
					"/{$module}/templates/{$GLOBALS['phpgw_info']['user']['preferences']['common']['template_set']}/images"
				);

				foreach ( $paths as $path )
				{
					if ( is_dir(PHPGW_INCLUDE_ROOT . $path) )
					{
						$d = dir(PHPGW_INCLUDE_ROOT . $path);
						while (false != ($entry = $d->read()))
						{
							if ($entry == '.' || $entry == '..')
							{
								continue;
							}
							$found_files[$module][$entry] = $path;
						}
						$d->close();
					}
				}
			}

			$exts = array('.png', '.jpg', '');
			foreach ( array($module, 'phpgwapi') as $module )
			{
				if ( !isset($found_files[$module]) )
				{
					continue;
				}
				foreach ( $exts as $ext )
				{
					if ( isset($found_files[$module]["{$image}{$ext}"]) )
					{
						return "{$webserver_url}{$found_files[$module]["{$image}{$ext}"]}/{$image}{$ext}";
					}
				}
			}
			return '';
		}

		/**
		* Find an individual image
		*
		* @param string $module the module the image is from
		* @param string $image the image to search for
		* @param string $ext the filename extension of the image - should usually be an empty string
		* @param bool $use_lang use a translated verison of the image
		* @return string URL to image
		*/
		public static function image($module, $image = '', $ext = '', $use_lang = true)
		{
			if (!is_array($image))
			{
				if (empty($image))
				{
					return '';
				}
				$image = array($image);
			}

			if ($use_lang)
			{
				foreach ( $image as $img )
				{
					$lang_images[] = $img . '_' . $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
					$lang_images[] = $img;
				}
				$image = $lang_images;
			}

			foreach ( $image as $img )
			{
				$image_found = self::find_image($module, $img.$ext);
				if ( $image_found )
				{
					return $image_found;
				}
			}
			return '';
		}

		/**
		* Find an individual "mouse over" image
		*
		* @param string $module the module the image is for
		* @param string $image the image to search for
		* @param string $ext the extension used to indicate a "mouse ob" image
		* @return string URL to image
		*/
		public function image_on($appname, $image, $extension = '_on')
		{
			$with_extension = $this->image($appname,$image,$extension);
			if ( $with_extension )
			{
				return $with_extension;
			}

			$without_extension = $this->image($appname,$image);
			if ( $without_extension )
			{
				return $without_extension;
			}

			return '';
		}

		/**
		* Load header.inc.php for an application
		*/
		public function app_header()
		{
			if (file_exists(PHPGW_APP_INC . '/header.inc.php'))
			{
				require_once PHPGW_APP_INC . '/header.inc.php';
			}
		}

		/**
		* Load the phpgw header
		*/
		public function phpgw_header($navbar = False)
		{
			// this prevents infinite loops caused by bad code - skwashd jan08
			static $called = false;
			if ( $called )
			{
				return;
			}
			$called = true;

			$tpl_name = $GLOBALS['phpgw_info']['server']['template_set'];
			if ( !is_dir(PHPGW_INCLUDE_ROOT . "/phpgwapi/templates/{$tpl_name}/")
				|| !is_readable(PHPGW_INCLUDE_ROOT . "/phpgwapi/templates/{$tpl_name}/head.inc.php") )
			{
				$tpl_name = 'simple';
			}

			require_once PHPGW_INCLUDE_ROOT . "/phpgwapi/templates/{$tpl_name}/head.inc.php";
			require_once PHPGW_INCLUDE_ROOT . "/phpgwapi/templates/{$tpl_name}/navbar.inc.php";
			if ($navbar)
			{
				echo parse_navbar();
			}

			/* used for xslt apps without xslt framework */
			if ( isset($GLOBALS['phpgw_info']['flags']['xslt_app']) && $GLOBALS['phpgw_info']['flags']['xslt_app'] )
			{
				$GLOBALS['phpgw']->xslttpl->add_file('app_data');
			}
		}

		/**
		* Render the page footer
		*/
		public function phpgw_footer()
		{
			static $footer_rendered = false;
			if ( !$footer_rendered )
			{
				$footer_rendered = true;
				/* used for xslt apps without xslt framework */
				if ( isset($GLOBALS['phpgw_info']['flags']['xslt_app'])
					&& $GLOBALS['phpgw_info']['flags']['xslt_app'] )
				{
					$GLOBALS['phpgw']->xslttpl->pparse();
				}

				if ( !isset($GLOBALS['phpgw_info']['flags']['nofooter'])
					|| !$GLOBALS['phpgw_info']['flags']['nofooter'] )
				{
					require_once PHPGW_API_INC . '/footer.inc.php';
				}
			}
		}

		/**
		* Include CSS in template header
		*
		* This first loads up the basic global CSS definitions, which support
		* the selected user theme colors. Next we load up the app CSS. This is
		* all merged into the selected theme's css.tpl file.
		*
		* @author Dave Hall skwashd at phpgroupware.org
		* @return string Template including CSS definitions
		*/
		public function get_css($cache_refresh_token = '')
		{
			$all_css = '';
			if( isset($GLOBALS['phpgw']->css) && is_object($GLOBALS['phpgw']->css) )
			{
				$all_css .= $GLOBALS['phpgw']->css->get_css_links($cache_refresh_token);
			}

			if ( isset($GLOBALS['phpgw_info']['flags']['css_link']) )
			{
				$all_css .= $GLOBALS['phpgw_info']['flags']['css_link'] . "\n";
			}

			//FIXME drop app_css, use the new css stuff
			$app_css = '';
			if ( isset($GLOBALS['phpgw_info']['menuaction']) )
			{
				list($app,$class,$method) = explode('.',$GLOBALS['phpgw_info']['menuaction']);
				if ( isset($GLOBALS[$class]->public_functions)
					&& is_array($GLOBALS[$class]->public_functions)
					&& isset($GLOBALS[$class]->public_functions['css']) )
				{
					$app_css .= $GLOBALS[$class]->css();
				}
			}

			if ( isset($GLOBALS['phpgw_info']['flags']['css']) )
			{
				$app_css .= $GLOBALS['phpgw_info']['flags']['css'] . "\n";
			}

			$all_css .= "\n<!-- NOTE: This will not be supported in the future -->\n\t\t<style type=\"text/css\">\n\t\t{$app_css}\n\t\t</style>\n";
			return $all_css;
		}

		/**
		* Backwards compatibility method
		* @see get_javascript
		*/
		public function get_java_script()
		{
			return $this->get_javascript();
		}

		/**
		* Include JavaScript in template header
		*
		* The method is included here to make it easier to change the js support
		* in phpgw. One change then all templates will support it (as long as they
		* include a call to this method).
		*
		* @author Dave Hall skwashd at phpgroupware.org
		* @return string The JavaScript code to include
		*/
		public function get_javascript($cache_refresh_token = '')
		{
			$js = '';
			if( isset($GLOBALS['phpgw']->js) && is_object($GLOBALS['phpgw']->js))
			{
				$js .= $GLOBALS['phpgw']->js->get_script_links($cache_refresh_token);
			}
			if( isset($GLOBALS['phpgw']->yuical) && is_object($GLOBALS['phpgw']->yuical))
			{
				if($cal_script = $GLOBALS['phpgw']->yuical->get_script())
				{
					$GLOBALS['phpgw_info']['flags']['java_script'] .= "\n"
						. '<script type="text/javascript">' ."\n"
						. '//<[CDATA[' ."\n"
						. $cal_script ."\n"
						. '//]]' ."\n"
						. "</script>\n";
				}
			}

			if ( isset($GLOBALS['phpgw_info']['menuaction']) )
			{
				list($app, $class, $method) = explode('.',$GLOBALS['phpgw_info']['menuaction']);
				if ( isset($GLOBALS[$class]->public_functions)
					&& is_array($GLOBALS[$class]->public_functions)
					&& isset($GLOBALS[$class]->public_functions['java_script'])
					&& $GLOBALS[$class]->public_functions['java_script'] )
				{
					$js .= $GLOBALS[$class]->java_script();
				}
			}

			if (isset($GLOBALS['phpgw_info']['flags']['java_script']))
			{
				$js .= $GLOBALS['phpgw_info']['flags']['java_script'] . "\n";
			}
			return $js;
		}

		/**
		* Include JavaScript after </body>
		*
		* The method is included here to make it easier to change the js support
		* in phpgw. One change then all templates will support it (as long as they
		* include a call to this method).
		*
		* @author Sigurd Nes
		* @return string The JavaScript code to include
		*/
		public function get_javascript_end()
		{
			$js = '';

			if (isset($GLOBALS['phpgw_info']['flags']['java_script_end']))
			{
				$js .= $GLOBALS['phpgw_info']['flags']['java_script_end'] . "\n";
			}
			return $js;
		}

		/**
		* Get window.on* events from javascript class
		*
		* @author Dave Hall skwashd at phpgroupware.org
		* @return string the wndow events to be used or empty
		*/
		public function get_on_events()
		{
			if(@is_object($GLOBALS['phpgw']->js))
			{
				return $GLOBALS['phpgw']->js->get_win_on_events();
			}
			else
			{
				return '';
			}
		}

		/**
		* Convert hexadecimal data into binary
		*
		* @param string $data hexidecimal data as a string
		* @return string binary value of $data;
		*/
		public static function hex2bin($data)
		{
			$len = strlen($data);
			return pack('H' . $len, $data);
		}

		/**
		* Encrypt data
		*
		* @param string $data Data to be encrypted
		* @return string Encrypted data
		*/
		public function encrypt($data)
		{
			return $GLOBALS['phpgw']->crypto->encrypt($data);
		}

		/**
		* Decrypt data
		* @param string $data Data to be decrypted
		* @return string Decrypted data
		*/
		public function decrypt($data)
		{
			return $GLOBALS['phpgw']->crypto->decrypt($data);
		}

		/**
		* Find the current position of the application in the users portal_order preference
		*
		* @param integer $app Application id to find current position
		* @return integer Applications position or -1
		*/
		public function find_portal_order($app)
		{
			if(!is_array($GLOBALS['phpgw_info']['user']['preferences']['portal_order']))
			{
				return -1;
			}

			foreach($GLOBALS['phpgw_info']['user']['preferences']['portal_order'] as $seq => $appid)
			{
				if($appid == $app)
				{
					@reset($GLOBALS['phpgw_info']['user']['preferences']['portal_order']);
					return $seq;
				}
			}
			@reset($GLOBALS['phpgw_info']['user']['preferences']['portal_order']);
			return -1;
		}

		/**
		* Wrapper to the session->appsession()
		*
		* @param string $data Data
		* @return mixed Result of $GLOBALS['phpgw']->session->appsession()
		* @deprecated
		*/
		public function appsession($data = '##NOTHING##')
		{
			echo 'common::appsession() is a depreciated function'
				. " - use session::appsession() instead<br>\n";

			return $GLOBALS['phpgw']->session->appsession('default','',$data);
		}

		/**
		* Show current date
		*
		* @param integer $t Time, defaults to user preferences
		* @param string $format Date format, defaults to user preferences
		* @return string Formated date
		*/
		public function show_date($t = '', $format = '')
		{
			if (!$t || (substr(php_uname(), 0, 7) == "Windows" && intval($t) <= 0))
			{
				return ''; // return nothing if not valid input
//				$t = phpgwapi_datetime::gmtnow();
			}

			//  + (date('I') == 1?3600:0)
			$t += phpgwapi_datetime::user_timezone();

			if (! $format)
			{
				$format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'] . ' - ';
				if ($GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == '12')
				{
					$format .= 'h:i a';
				}
				else
				{
					$format .= 'H:i';
				}
			}
			return date($format,$t);
		}

		/**
		*
		*
		* @param string $yearstr Year
		* @param string $monthstr Month
		* @param string $day Day
		* @param boolean $add_seperator Use separator, defaults to space
		* @return string Formatted date
		*/
		public function dateformatorder($yearstr,$monthstr,$daystr,$add_seperator = False)
		{
			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = substr($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],1,1);

			$dlarr[strpos($dateformat,'y')] = $yearstr;
			$dlarr[strpos($dateformat,'m')] = $monthstr;
			$dlarr[strpos($dateformat,'d')] = $daystr;
			ksort($dlarr);

			if ($add_seperator)
			{
				return (implode($sep,$dlarr));
			}
			else
			{
				return (implode(' ',$dlarr));
			}
		}

		/**
		* Format the time takes settings from user preferences
		*
		* @param integer $hour Hour
		* @param integer $min Minute
		* @param integer $sec Second
		* @return string Time formatted as hhmmss with am/pm
		*/
		public function formattime($hour, $min = 0, $sec = null)
		{
			die('use phpgwapi_datetime::format_time()');
		}

		/**
		* Create email preferences
		*
		* @param mixed $prefs Unused
		* @param integer $account_id Account id, defaults to phpgw_info['user']['account_id']
		* @internal This is not the best place for it, but it needs to be shared between Aeromail and SM
		*/
		public function create_emailpreferences($prefs='',$accountid='')
		{
			return $GLOBALS['phpgw']->preferences->create_email_preferences($accountid);
			// Create the email Message Class if needed
			if (is_object($GLOBALS['phpgw']->msg))
			{
				$do_free_me = False;
			}
			else
			{
				$GLOBALS['phpgw']->msg = createObject('email.mail_msg');
				$do_free_me = True;
			}

			// this sets the preferences into the phpgw_info structure
			$GLOBALS['phpgw']->msg->create_email_preferences();

			// cleanup and return
			if ($do_free_me)
			{
				unset ($GLOBALS['phpgw']->msg);
			}
		}

		/**
		* Convert application code to HTML text message
		*
		* @param integer $code Code number to convert into HTML string
		* @return string HTML string with code check result message
		* @internal This will be moved into the applications area
		*/
		public function check_code($code)
		{
			$s = '<br />';
			switch ($code)
			{
				case 13:	$s .= lang('Your message has been sent');break;
				case 14:	$s .= lang('New entry added sucessfully');break;
				case 15:	$s .= lang('Entry updated sucessfully');	break;
				case 16:	$s .= lang('Entry has been deleted sucessfully'); break;
				case 18:	$s .= lang('Password has been updated');	break;
				case 38:	$s .= lang('Password could not be changed');	break;
				case 19:	$s .= lang('Session has been killed');	break;
				case 27:	$s .= lang('Account has been updated');	break;
				case 28:	$s .= lang('Account has been created');	break;
				case 29:	$s .= lang('Account has been deleted');	break;
				case 30:	$s .= lang('Your settings have been updated'); break;
				case 31:	$s .= lang('Group has been added');	break;
				case 32:	$s .= lang('Group has been deleted');	break;
				case 33:	$s .= lang('Group has been updated');	break;
				case 34:	$s .= lang('Account has been deleted') . '<p>'
						. lang('Error deleting %1 %2 directory',lang('users'),' '.lang('private').' ')
						. ',<br />' . lang('Please %1 by hand',lang('delete')) . '<br /><br />'
						. lang('To correct this error for the future you will need to properly set the')
						. '<br />' . lang('permissions to the files/users directory')
						. '<br />' . lang('On *nix systems please type: %1','chmod 770 '
						. $GLOBALS['phpgw_info']['server']['files_dir'] . '/users/');
					break;
				case 35:	$s .= lang('Account has been updated') . '<p>'
						. lang('Error renaming %1 %2 directory',lang('users'),
						' '.lang('private').' ')
						. ',<br />' . lang('Please %1 by hand',
						lang('rename')) . '<br /><br />'
						. lang('To correct this error for the future you will need to properly set the')
						. '<br>' . lang('permissions to the files/users directory')
						. '<br>' . lang('On *nix systems please type: %1','chmod 770 '
						. $GLOBALS['phpgw_info']['server']['files_dir'] . '/users/');
					break;
				case 36:	$s .= lang('Account has been created') . '<p>'
						. lang('Error creating %1 %2 directory',lang('users'),
						' '.lang('private').' ')
						. ',<br />' . lang('Please %1 by hand',
						lang('create')) . '<br /><br />'
						. lang('To correct this error for the future you will need to properly set the')
						. '<br />' . lang('permissions to the files/users directory')
						. '<br />' . lang('On *nix systems please type: %1','chmod 770 '
						. $GLOBALS['phpgw_info']['server']['files_dir'] . '/users/');
					break;
				case 37:	$s .= lang('Group has been added') . '<p>'
						. lang('Error creating %1 %2 directory',lang('groups'),' ')
						. ',<br />' . lang('Please %1 by hand',
						lang('create')) . '<br /><br />'
						. lang('To correct this error for the future you will need to properly set the')
						. '<br />' . lang('permissions to the files/users directory')
						. '<br />' . lang('On *nix systems please type: %1','chmod 770 '
						. $GLOBALS['phpgw_info']['server']['files_dir'] . '/groups/');
					break;
				case 38:	$s .= lang('Group has been deleted') . '<p>'
						. lang('Error deleting %1 %2 directory',lang('groups'),' ')
						. ',<br />' . lang('Please %1 by hand',
						lang('delete')) . '<br /><br />'
						. lang('To correct this error for the future you will need to properly set the')
						. '<br />' . lang('permissions to the files/users directory')
						. '<br />' . lang('On *nix systems please type: %1','chmod 770 '
						. $GLOBALS['phpgw_info']['server']['files_dir'] . '/groups/');
					break;
				case 39:	$s .= lang('Group has been updated') . '<p>'
						. lang('Error renaming %1 %2 directory',lang('groups'),' ')
						. ',<br />' . lang('Please %1 by hand',
						lang('rename')) . '<br /><br />'
						. lang('To correct this error for the future you will need to properly set the')
						. '<br />' . lang('permissions to the files/users directory')
						. '<br />' . lang('On *nix systems please type: %1','chmod 770 '
						. $GLOBALS['phpgw_info']['server']['files_dir'] . '/groups/');
					break;
				case 40: $s .= lang('You have not entered a title').'.';
					break;
				case 41: $s .= lang('You have not entered a valid time of day').'.';
					break;
				case 42: $s .= lang('You have not entered a valid date').'.';
					break;
				case 43: $s .= lang('You have not entered participants').'.';
					break;
				default:	return '';
			}
			return $s;
		}

		/**
		* Process error message
		*
		* @param string $error Error message
		* @param integer $line Line number of error
		* @param string $file Filename in which the error occured
		*/
		public function phpgw_error($error,$line = '', $file = '')
		{
			echo '<p><strong>phpGroupWare internal error:</strong><p>'.$error;
			if ($line)
			{
				echo 'Line: '.$line;
			}
			if ($file)
			{
				echo 'File: '.$file;
			}
			echo '<p>Your session has been halted.';
			exit;
		}

		/**
		* Display a list of core functions in the API
		*
		* @internal Works on systems with grep only
		*/
		public function debug_list_core_functions()
		{
			echo 'common::debug_list_core_functions() is deprecated - no output generated!<br />';
		}

		/**
		* Get the next higher value for an integer and increment it in the database
		*
		* @param string $appname Application name to get an id for
		* @param integer $min Minimum of id range
		* @param integer $max Maximum of id range
		* @return integer|boolean Next available id or false
		*/
		public function next_id($appname,$min=0,$max=0)
		{
			if (!$appname)
			{
				return -1;
			}

			$GLOBALS['phpgw']->db->query("SELECT id FROM phpgw_nextid WHERE appname='".$appname."'",__LINE__,__FILE__);
			while( $GLOBALS['phpgw']->db->next_record() )
			{
				$id = $GLOBALS['phpgw']->db->f('id');
			}

			if (empty($id) || !$id)
			{
				$id = 1;
				$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_nextid (appname,id) VALUES ('".$appname."',".$id.")",__LINE__,__FILE__);
			}
			elseif($id<$min)
			{
				$id = $min;
				$GLOBALS['phpgw']->db->query("UPDATE phpgw_nextid SET id=".$id." WHERE appname='".$appname."'",__LINE__,__FILE__);
			}
			elseif ($max && ($id > $max))
			{
				return False;
			}
			else
			{
				$id = $id + 1;
				$GLOBALS['phpgw']->db->query("UPDATE phpgw_nextid SET id=".$id." WHERE appname='".$appname."'",__LINE__,__FILE__);
			}

			return intval($id);
		}

		/**
		* Get the current id in the next_id table for a particular application/class
		*
		* @param string $appname Application name to get the id for
		* @param integer $min Minimum of id range
		* @param integer $max Maximum of id range
		* @return integer|boolean Last used id or false
		*/
		public function last_id($appname,$min=0,$max=0)
		{
			if (!$appname)
			{
				return -1;
			}

			$GLOBALS['phpgw']->db->query("SELECT id FROM phpgw_nextid WHERE appname='".$appname."'",__LINE__,__FILE__);
			if ( $GLOBALS['phpgw']->db->next_record() )
			{
				$id = $GLOBALS['phpgw']->db->f('id');
			}

			if ( empty($id) )
			{
				$id = 1;
				if ( $min )
				{
					$id = $min;
				}

				$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_nextid (appname,id) VALUES ('{$appname}', {$id})", __LINE__, __FILE__);
				return $id;
			}
			else if ( $id < $min )
			{
				$id = $min;
				$GLOBALS['phpgw']->db->query("UPDATE phpgw_nextid SET id = {$id} WHERE appname='{$appname}'", __LINE__, __FILE__);
				return $id;
			}
			else if ( $max && ($id > $max) )
			{
				return 0;
			}
			return (int) $id;
		}

		/**
		* Starts capturing all output so it can be used by the XSLT temaplte engine
		*/
		public function start_xslt_capture()
		{
			if (!isset($GLOBALS['phpgw_info']['xslt_capture']))
			{
				$GLOBALS['phpgw_info']['xslt_capture'] = True;
				ob_start();		// capture the output
			}
		}

		/**
		* Stops capturing all output and uses it in the XSLT temaplte engine by stuffing it
		* into an xml node called "body_data"
		*
		* @internal Note: need to be run BEFORE exit is called, as buffers get flushed automatically before
		* any registered shutdown-functions (eg. phpgw_footer) gets called
		*/
		public function stop_xslt_capture()
		{
			if (isset($GLOBALS['phpgw_info']['xslt_capture']))
			{
				unset($GLOBALS['phpgw_info']['xslt_capture']);
				$output = ob_get_contents();	// get captured output
				ob_end_clean();					// stop capture and clean output-buffer
				if (!empty($output))
				{
					$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('body_data' => $output));
				}
			}
		}
		/**
		* Generate a consistant msgbox for app apps to use
		* makes it easier and more consistant to generate message boxes
		*
		* @param string $text ???
		* @param bool $type ???
		* @param string $base ???
		* @returns ????
		*/
		public function msgbox($text = '', $type = True, $base = '')
		{
			if ($text=='' && @isset($GLOBALS['phpgw_info']['flags']['msgbox_data']))
			{
				$text = $GLOBALS['phpgw_info']['flags']['msgbox_data'];
				unset($GLOBALS['phpgw_info']['flags']['msgbox_data']);
			}
			elseif($text=='')
			{
				return;
			}

			if(!isset($GLOBALS['phpgw']->xslttpl->xslfiles['msgbox']))
			{
				$GLOBALS['phpgw']->xslttpl->add_file('msgbox',$this->get_tpl_dir('phpgwapi','base'));
			}

		//	$prev_helper = $GLOBALS['phpgw']->translation->translator_helper;
		//	$GLOBALS['phpgw']->translation->translator_helper = '';

			$data = array();
			if (is_array($text))
			{
				foreach($text as $key => $value)
				{
					if ($value == True)
					{
						$img	= $this->image('phpgwapi','msgbox_good');
						$alt	= lang('OK');
						$class  = 'msg_good';
					}
					else
					{
						$img	= $this->image('phpgwapi','msgbox_bad');
						$alt	= lang('ERROR');
						$class  = 'error';
					}

					$data[] = array
					(
						'msgbox_text'				=> $key,
						'msgbox_img'				=> $img,
						'msgbox_img_alt'			=> $alt,
						'lang_msgbox_statustext'	=> $alt,
						'msgbox_class'				=> $class
					);
				}
			}
			else
			{
				if ($type == True)
				{
					$img	= $this->image('phpgwapi','msgbox_good');
					$alt	= lang('OK');
				}
				else
				{
					$img	= $this->image('phpgwapi','msgbox_bad');
					$alt	= lang('ERROR');
				}

				$data = array
				(
					'msgbox_text'				=> lang($text),
					'msgbox_img'				=> $img,
					'msgbox_img_alt'			=> $alt,
					'lang_msgbox_statustext'	=> $alt
				);
			}

		//	$GLOBALS['phpgw']->translation->translator_helper = $prev_helper;

			if ($base)
			{
				$GLOBALS['phpgw']->xslttpl->set_var($base,array('msgbox_data' => $data),True);
			}
			else
			{
				return $data;
			}
		}

		/**
		* Prepare data for use with the function msgbox
		* makes it easier and more consistant to generate message boxes
		*
		* @param array $data
		* @returns array for use with msgbox
		*/

		public function msgbox_data($receipt)
		{
			$msgbox_data_error	 = array();
			$msgbox_data_message = array();
			if (isset($receipt['error']) && is_array($receipt['error']))
			{
				foreach($receipt['error'] as $dummy => $error)
				{
					$msgbox_data_error[$error['msg']] = false;
				}
			}
			else if(isset($receipt['error']))
			{
				$msgbox_data_error[$receipt['error']] = false;			
			}

			if (isset($receipt['message']) && is_array($receipt['message']))
			{
				foreach($receipt['message'] as $dummy => $message)
				{
					$msgbox_data_message[$message['msg']] = true;
				}
			}
			else if(isset($receipt['message']))
			{
				$msgbox_data_message[$receipt['message']] = true;			
			}

			$msgbox_data = array_merge($msgbox_data_error, $msgbox_data_message);

			return $msgbox_data;
		}
	}
