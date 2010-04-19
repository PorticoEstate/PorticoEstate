<?php
	/**
	* Communik8r basic UI class
	*
	* @author Dave Hall skwashd@phpgroupware.org
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package communik8r
	* @subpackage base
	* @version $Id: class.uibase.inc.php,v 1.1.1.1 2005/08/23 05:03:51 skwashd Exp $
	*/

	class uibase
	{
		/**
		* @var object $bo business logic
		*/
		var $bo;
		
		/**
		* @var object $t reference to template object
		*/
		var $t;
		
		/**
		* @constructor
		*/
		function uibase()
		{
			$this->bo = createObject('communik8r.bobase');
			$this->t =&$GLOBALS['phpgw']->template;
		}

		/**
		* Provide user with a link to communik8r running full screen mode
		* @internal only used for the index page
		*/
		function index()
		{
			$GLOBALS['phpgw']->common->phpgw_header(true);
//			echo parse_navbar();
			$this->t->set_root(PHPGW_APP_TPL);
			
			$this->t->set_file('index', 'index.tpl');
			$this->t->set_var('start_url', $GLOBALS['phpgw']->link('index.php', array('section' => 'start')) );
			$this->t->pfp('out', 'index');
		}
		
		/**
		* Compose a new message
		*
		* @param array $uri_parts the parts of the url used in the page request
		*/
		function compose($uri_parts)
		{
			$msg_types = array('jabber', 'email');
			
			if ( !in_array($uri_parts[2], $msg_types) )
			{
				die('invalid message type - exiting!');
			}

			ExecMethod('communik8r.bo' . $uri_parts[2] . '.compose', $uri_parts);
		}

		/**
		* Get the base URL
		*
		* @returns string base URL
		*/
		function _get_base_url()
		{
			//NOTE: not used?
			return $GLOBALS['phpgw']->link('index.php');
/*
			return ( (strpos('http', $GLOBALS['phpgw_info']['server']['webserver_url']) === 0)
					? $GLOBALS['phpgw_info']['server']['webserver_url']
					: 'http' . ($_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . $GLOBALS['phpgw_info']['server']['webserver_url']
					);
*/
		}
	}
