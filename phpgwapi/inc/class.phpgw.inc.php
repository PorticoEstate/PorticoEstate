<?php
	/**
	* Parent class. Has a few functions but is more importantly used as a parent class for everything else.
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id: class.phpgw.inc.php,v 1.56 2006/10/14 12:30:11 sigurdne Exp $
	*/

	/**
	* Parent class. Has a few functions but is more importantly used as a parent class for everything else.
	*
	* @package phpgwapi
	* @subpackage application
	*/
	class phpgw
	{
		var $accounts;
		var $applications;
		var $acl;
		var $auth;
		var $db; 
		/**
		 * Turn on debug mode. Will output additional data for debugging purposes.
		 * @var	string	$debug
		 * @access public
		 */	
		var $debug = 0;		// This will turn on debugging information.
		var $crypto;
		var $categories;
		var $common;
		var $contacts;
		var $datetime;
		var $hooks;
		var $js;
		var $network;
		var $nextmatchs;
		var $preferences;
		var $session;
		var $send;
		var $template;
		var $translation;
		var $utilities;
		var $vfs;
		var $calendar;
		var $msg;
		var $addressbook;
		var $todo;
		var $xslttpl;
		var $shm = null;
		var $mapping;

		/**************************************************************************\
		* Core functions                                                           *
		\**************************************************************************/

		/**
		 * Strips out html chars
		 *
		 * Used as a shortcut for stripping out html special chars. 
		 *
		 * @access public
		 * @param $s string The string to have its html special chars stripped out.
		 * @return string The string with html special characters removed
		 */
		function strip_html($s)
		{
			return htmlspecialchars(stripslashes($s));
		}

		/**
		 * Link url generator
		 *
		 * Used for backwards compatibility and as a shortcut. If no url is passed, it 
		 * will use PHP_SELF. Wrapper to session->link()
		 *
		 * @access public
		 * @param string $string The url the link is for
		 * @param string $extravars	Extra params to be passed to the url
		 * @param string $redirect is the resultant link being used in a header('Location:' ... redirect?
		 * @return string The full url after processing
		 * @see	session->link()
		 */
		function link($url = '', $extravars = array(), $redirect = false)
		{
			return $this->session->link($url, $extravars, $redirect);
		}

		function redirect_link($url = '',$extravars=array())
		{
			$this->redirect($this->session->link($url, $extravars, true));
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
		function safe_redirect($url)
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
		function is_repost($display_error = False)
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
		function redirect($url = '')
		{
			$iis = @strpos($_SERVER['SERVER_SOFTWARE'], 'IIS', 0);
			
			if ( !$url )
			{
				$url = $_SERVER['PHP_SELF'];
			}
			if ( $iis )
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
				Header('Location: ' . $url);
				exit;
			}
		}

		/**
		 * Shortcut to translation class
		 *
		 * This function is a basic wrapper to translation->translate()
		 *
		 * @access public
		 * @param string The key for the phrase
		 * @param string The first additional param
		 * @param string The second additional param
		 * @param string The thrid additional param
		 * @param string The fourth additional param
		 * @see	translation->translate()
		 */
		function lang($key, $m1 = '', $m2 = '', $m3 = '', $m4 = '') 
		{
			/*  */
			return $this->translation->translate($key);
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
				else if ( is_null($value) && !is_null($default) )
				{
						return $default;
				}

				return self::clean_value($value, $value_type, $default);

				if ( is_array($value) )
				{
					foreach ( $value as &$val )
					{
						$val = self::clean_value($val, $value_type, $default); 
					}
				}
				else
				{
					$value = self::clean_value($value, $value_type, $default);
				}
				return $value;
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
				
				// This won't be needed in PHP6 as GPC magic quotes are being removed
				if ( get_magic_quotes_gpc() )
				{
						$value = stripslashes($value);
				}

				switch ( $value_type )
				{
					case 'bool':
						if ( preg_match('/^[false|0|no]$/', $value) )
						{
							$value = false;
						}
						return !!$value;
						break;

					case 'float':
					case 'double':
					case 'real':
						if ( (float) $value == $value )
						{
								return (float) $value;
						}
						$value = $default;
						break;	
					
					case 'int':
					case 'integer':
					case 'number':
						if ( (int) $value == $value )
						{
								return (int) $value;
						}
						$value = $default;
						break;

					/* Specific string types */
					case 'color':
						$regex = array('options' => array('regexp' => '/^#([a-f0-9]{3}){1,2}$/i'));
						$filtered =  strtolower(filter_var($value, FILTER_VALIDATE_REGEXP, $regex));
						if ( $filtered == strtolower($value) )
						{
							return $filtered;
						}
						$value = $default;
						break;
							
					case 'email':
						$filtered = filter_var($value, FILTER_VALIDATE_EMAIL);
						if ( $filtered == $value )
						{
								return $filtered;
						}
						$value = $default;
						break;

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
						$value = $default;
						break;

					case 'ip':
						$filtered = filter_var($value, FILTER_VALIDATE_IP);
						if ( $filtered == $value )
						{
								return $filtered;
						}
						$value = $default;
						break;

					case 'location':
						$regex = array('options' => array('regexp' => '/^([a-z0-9_]+\.){2}[a-z0-9_]+$/i'));
						$filtered =  filter_var($value, FILTER_VALIDATE_REGEXP, $regex);
						if ( $filtered == $value )
						{
								return $filtered;
						}
						$value = $default;
						break;

					case 'url':
						$filtered = filter_var($value, FILTER_VALIDATE_URL);
						if ( $filtered == $value )
						{
								return $filtered;
						}
						$value = $default;
						break;

					/* only use this if you really know what you are doing */
					case 'raw':
						$value = filter_var($value, FILTER_UNSAFE_RAW);
						break;
					
					case 'html': // this needs its own handler
					case 'string':
					default:
						$value = htmlspecialchars(filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
				}
				return $value;
			}
		}
