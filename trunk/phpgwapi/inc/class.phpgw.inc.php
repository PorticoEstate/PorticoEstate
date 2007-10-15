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
				echo "\n<HTML>\n<HEAD>\n<TITLE>Redirecting to $url</TITLE>";
				echo "\n<META HTTP-EQUIV=REFRESH CONTENT=\"0; URL=$url\">";
				echo "\n</HEAD><BODY>";
				echo "<H3>Please continue to <a href=\"$url\">this page</a></H3>";
				echo "\n</BODY></HTML>";
				exit;
			}
			else
			{
				Header('Location: ' . $url);
				//print("\n\n");
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
	} /* end of class */

?>
