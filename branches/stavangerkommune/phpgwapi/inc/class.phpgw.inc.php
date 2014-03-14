<?php
	/**
	* Global ugliness class
	* 
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
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
	* Global ugliness class
	*
	* Here lives all the code which makes the API tick and makes any serious 
	* refactoring almost impossible
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	*/
	class phpgw
	{
		public $accounts;
		public $adodb;
		public $acl;
		public $auth;
		public $db; 
		/**
		 * Turn on debug mode. Will output additional data for debugging purposes.
		 * @var	string	$debug
		 * @access public
		 */	
		public $debug = 0;		// This will turn on debugging information.
		public $contacts;
		public $preferences;

		// FIXME find all instances and change to sessions then we can drop this
		public $session;
		public $send;
		public $template;
		public $utilities;
		public $vfs;
		public $calendar;
		public $msg;
		public $addressbook;
		public $todo;
		public $xslttpl;
		public $mapping;

		/**
		* @var array $instance_vars holds most of the public instance variable, so they are only instatiated when needed
		* @internal removes the need for a lot of if ( !isset($var) || !is_object($var)) { $var = createObject("phpgwapi.$var"); } - YAY!
		*/
		private $instance_vars = array();

		/**
		* Handle instance variables better - this way we only load what we need
		*
		* @param string $var the variable name to get
		*/
		public function __get($var)
		{
			if ( !isset($this->instance_vars[$var]) || !is_object($this->instance_vars[$var]) )
			{
				$this->instance_vars[$var] = createObject("phpgwapi.{$var}");
			}
			return $this->instance_vars[$var];
		}

		/**
		* Handle setting instance variables better
		*
		* @internal this will probably validate the variable name at some point in the future to stop typo bugs
		* @param string $var the varliable to set
		* @param mixed $value the value to assign to the variable
		*/
		public function __set($var, $value)
		{
			$this->instance_vars[$var] = $value;
		}

		/**
		* Handle unset()ing of instance variables
		*
		* @param string $var the variable to unset
		*/
		public function __unset($var)
		{
			unset($this->instance_vars[$var]);
		}

		/**
		* Check if an instance variable isset() or not
		*
		* @internal we also check if it an object or not - as that is all we should be storing in here
		* @param string $var the variable to check
		* @return bool is the variable set or not 
		*/
		public function __isset($var)
		{
			return isset($this->instance_vars[$var]) && is_object($this->instance_vars[$var]);
		}

		/**
		 * Strips out html chars
		 *
		 * Used as a shortcut for stripping out html special chars. 
		 *
		 * @param $s string The string to have its html special chars stripped out.
		 * @return string The string with html special characters removed
		 */
		public static function strip_html($s)
		{
			$s = htmlspecialchars(strip_tags($s), ENT_QUOTES, 'UTF-8');
			return $s;
		}

		/**
		 * Clean the inputted HTML to make sure it is free of any nasties
		 *
		 * @param string $html     the HTML to clean
		 * @param string $base_url the base URL for all links - currently not used
		 *
		 * @return string the cleaned html
		 *
		 * @internal uses HTMLPurifier a whitelist based html sanitiser and tidier
		 */
		public static function clean_html($html, $base_url = '')
		{
			if ( !isset($GLOBALS['phpgw_info']['server']['html_filtering']) 
				|| !$GLOBALS['phpgw_info']['server']['html_filtering'])
			{
				return $html;
			}
			
			if ( !$base_url )
			{
				$base_url = $GLOBALS['phpgw_info']['server']['webserver_url'];
			}

			require_once PHPGW_INCLUDE_ROOT . '/phpgwapi/inc/htmlpurifier/HTMLPurifier.auto.php';

		    $config = HTMLPurifier_Config::createDefault();
			$config->set('HTML', 'Doctype', 'HTML 4.01 Transitional');
			$purifier = new HTMLPurifier($config);

			$clean_html = $purifier->purify($html);

			return $clean_html;
		}

		/**
		 * Link url generator
		 *
		 * Used for backwards compatibility and as a shortcut. If no url is passed, it 
		 * will use PHP_SELF. Wrapper to session->link()
		 *
		 * @access public
		 * @param string  $string The url the link is for
		 * @param array   $extravars	Extra params to be passed to the url
		 * @param boolean $redirect is the resultant link being used in a header('Location:' ... redirect?
		 * @param boolean $external is the resultant link being used as external access (i.e url in emails..)
		 * @return string The full url after processing
		 * @see	session->link()
		 */
		public function link($url = '', $extravars = array(), $redirect = false, $external = false)
		{
			return $this->session->link($url, $extravars, $redirect, $external);
		}

		/**
		 * Redirect to another URL
		 *
		 * @param string $string The url the link is for
		 * @param string $extravars	Extra params to be passed to the url
		 * @return null
		 */
		public function redirect_link($url = '', $extravars=array())
		{
			self::redirect($this->session->link($url, $extravars, true));
		}

		/**
		* Safe redirect to external urls
		*
		* Stop session theft for "GET" based sessions
		*
		* @access public
		* @param string $url the target url
		* @returns string safe redirect url
		* @author Dave Hall
		*/
		public static function safe_redirect($url)
		{
			return $GLOBALS['phpgw_info']['server']['webserver_url']
				. '/redirect.php?go=' . urlencode($url);
		}
		
		/**
		* Repsost Prevention Detection
		*
		* Used as a shortcut. Wrapper to session->is_repost()
		*
		* @access public
		* @param bool $display_error	Use common error handler? - not yet implemented
		* @return bool True if called previously, else False - call ok
		* @see session->is_repost()
		* @author Dave Hall
		*/
		public function is_repost($display_error = False)
		{
			return $this->session->is_repost($display_error);
		}
		
		/**
		 * Handles redirects under iis and apache
		 *
		 * This function handles redirects under iis and apache it assumes that $GLOBALS['phpgw']->link() has already been called
		 *
		 * @access public
		 * @param string The url ro redirect to
		 */
		public static function redirect($url = '')
		{
			$iis = strpos($_SERVER['SERVER_SOFTWARE'], 'IIS', 0) !== false;
			
			if ( !$url )
			{
				$url = self::get_var('PHP_SELF', 'string', 'SERVER');
			}

			if ( $iis || headers_sent() )
			{
				echo "<html>\n<head>\n<title>Redirecting to $url</title>";
				echo "\n<meta http-equiv=\"refresh\ content=\"0; URL=$url\">";
				echo "\n</head>\n<body>";
				echo "\n<h1>Please continue to <a href=\"$url\">this page</a></h1>";
				echo "\n</body>\n</html>";
				exit;
			}
			else
			{
				header('Location: ' . $url);
				exit;
			}
		}

		/**
		* Translate a string to a user's prefer language - convience method
		*
		* @param string $key phrase to translate (note: %n are replaces with $mn)
		* @param string $m1 substitution string
		* @param string $m1 substitution string
		* @param string $m2 substitution string
		* @param string $m3 substitution string
		* @param string $m4 substitution string
		* @param string $m5 substitution string
		* @param string $m6 substitution string
		* @param string $m7 substitution string
		* @param string $m8 substitution string
		* @param string $m9 substitution string
		* @param string $m10 substitution string
		* @returns string translated phrase
		*/
		public function lang($key,$m1='',$m2='',$m3='',$m4='',$m5='',$m6='',$m7='',$m8='',$m9='',$m10='')
		{
			if(is_array($m1))
			{
				$vars = $m1;
			}
			else
			{
				$vars = array($m1, $m2, $m3, $m4, $m5, $m6, $m7, $m8, $m9, $m10);
			}
			if ( !isset($this->translation) )
			{
				$str = $key;
				for ( $i = 10; $i > 0; --$i )
				{	
					$var = "m{$i}";
					$str = preg_replace("/(%$i)+/", $$var, $str);
				}
				return "$str*#*";
			}
			return $this->translation->translate("$key", $vars);
		}

		/**
		* Get the value of a variable
		*
		* @param string $var_name the name of the variable sought
		* @param string $value_type the expected data type
		* @param string $var_type the variable type sought
		* @param mixed $default the default value
		* @return mixed the sanitised variable requested
		*/
		public static function get_var($var_name, $value_type = 'string', $var_type = 'REQUEST', $default = null)
		{
				$value = null;
				switch ( strtoupper($var_type) )
				{
					case 'COOKIE':
						if ( isset($_COOKIE[$var_name]) )
						{
							$value = $_COOKIE[$var_name];
						}
						break;

					case 'GET':
						if ( isset($_GET[$var_name]) )
						{
							$value = $_GET[$var_name];
						}
						break;

					case 'POST':

						if ( isset($_POST[$var_name]) )
						{
							$value = $_POST[$var_name];
						}
						break;

					case 'SERVER':
						if ( isset($_SERVER[$var_name]) )
						{
							$value = $_SERVER[$var_name];
						}
						break;

					case 'SESSION':
						if ( isset($_SESSION[$var_name]) )
						{
							$value = $_SESSION[$var_name];
						}
						break;

					case 'REQUEST':
					default:
						if ( isset($_REQUEST[$var_name]) )
						{
							$value = $_REQUEST[$var_name];
						}
				}

				if ( is_null($value) && is_null($default) )
				{
						return null;
				}
				else if ( (is_null($value) || !$value) && !is_null($default) )
				{
						return $default;
				}

				return self::clean_value($value, $value_type, $default);
			}
			
			/**
			* Test (and sanitise) the value of a variable
			*
			* @param mixed $value the value to test
			* @param string $value_type the expected type of the variable
			* @return mixed the sanitised variable
			*/
			public static function clean_value($value, $value_type = 'string', $default = null)
			{
				if ( is_array($value) )
				{
					foreach ( $value as &$val )
					{
						$val = self::clean_value($val, $value_type, $default);
					}
					return $value;
				}

				// Trim whitespace so it doesn't trip us up
				$value = trim($value);
				
				// This won't be needed in PHP 5.4 and later as GPC magic quotes are being removed
				if ( version_compare(PHP_VERSION, '5.3.7') <= 0 && get_magic_quotes_gpc() )
				{
						$value = stripslashes($value);
				}

				switch ( $value_type )
				{
					case 'string':
					default:
						$value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
						$value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
						break;

					case 'boolean':
					case 'bool':
						if ( preg_match('/^[false|0|no]$/', $value) )
						{
							$value = false;
						}
						return !!$value;

					case 'float':
					case 'double':
						$value = str_replace(array(' ',','),array('','.'), $value);
						if ( (float) $value == $value )
						{
								return (float) $value;
						}
						return (float) $default;
					
					case 'int':
					case 'integer':
					case 'number':
						if ( (int) $value == $value )
						{
								return (int) $value;
						}
						return (int) $default;

					/* Specific string types */
					case 'color':
						$regex = array('options' => array('regexp' => '/^#([a-f0-9]{3}){1,2}$/i'));
						$filtered =  strtolower(filter_var($value, FILTER_VALIDATE_REGEXP, $regex));
						if ( $filtered == strtolower($value) )
						{
							return $filtered;
						}
						return (string) $default;
							
					case 'email':
						$filtered = filter_var($value, FILTER_VALIDATE_EMAIL);
						if ( $filtered == $value )
						{
								return $filtered;
						}
						return (string) $default;

					case 'filename':
						if ( $value != '.' || $value != '..' )
						{
							$regex = array('options' => array('regexp' => '/^[a-z0-9_]+$/i'));
							$filtered =  filter_var($value, FILTER_VALIDATE_REGEXP, $regex);
							if ( $filtered == $value )
							{
								return $filtered;
							}
						}
						return (string) $default;

					case 'ip':
						$filtered = filter_var($value, FILTER_VALIDATE_IP);
						if ( $filtered == $value )
						{
								return $filtered;
						}

						// make the default sane
						if ( !$default )
						{
							$default = '0.0.0.0';
						}

						return (string) $default;

					case 'location':
						$regex = array('options' => array('regexp' => '/^([a-z0-9_]+\.){2}[a-z0-9_]+$/i'));
						$filtered =  filter_var($value, FILTER_VALIDATE_REGEXP, $regex);
						if ( $filtered == $value )
						{
								return $filtered;
						}
						return (string) $default;

					case 'url':
						$filtered = filter_var($value, FILTER_VALIDATE_URL);
						if ( $filtered == $value )
						{
								return $filtered;
						}
						return (string) $default;

					/* only use this if you really know what you are doing */
					case 'raw':
						$value = filter_var($value, FILTER_UNSAFE_RAW);
						break;
					
					case 'html':
						$value = self::clean_html($value);
						break;
				}
				return $value;
			}

			/**
			* Import a class, should be used in the top of each class, doesn't instantiate like createObject does
			*
			* @internal when calling static methods, phpgw::import_class() should be called to ensure it is available
			* @param string $clasname the class to import module.class
			*/
			public static function import_class($classname)
			{
				$parts = explode('.', $classname);

				if ( count($parts) != 2 )
				{
					trigger_error(lang('Invalid class: %1', $classname), E_USER_ERROR);
				}

				if ( !include_class($parts[0], $parts[1]) )
				{
					trigger_error(lang('Unable to load class: %1', $classname), E_USER_ERROR);
				}
			}
		}
