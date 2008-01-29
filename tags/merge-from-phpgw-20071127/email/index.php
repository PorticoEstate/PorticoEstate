<?php
	/**
	* EMail
	*
	* @author Mark C3ushman <mark@cushman.net>
	* @author Angles <angles@phpgroupware.org>
	* @copyright Copyright (C) xxxx Mark C3ushman
	* @copyright Copyright (C) xxxx Angles
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id: index.php 17706 2006-12-17 11:21:02Z sigurdne $
	* @internal Based on Aeromail http://the.cushman.net/
	*/

	Header('Cache-Control: no-cache');
	Header('Pragma: no-cache');
	Header('Expires: Sat, Jan 01 2000 01:01:01 GMT');
  
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'  => 'email',
		'noheader'    => True,
		'nofooter'    => True,
		'nonavbar'    => True,
		'noappheader' => True,
		'noappfooter' => True
	);
	
	/**
	* Include phpgroupware header
	*/
	include('../header.inc.php');

	/*
	time limit should be controlled elsewhere
	@set_time_limit(0);

	this index page is acting like a calling app which wants the HTML produced by mail.uiindex.index
	but DOES NOT want mail.uiindex.index to actually echo or print out any HTML
	we, the calling app, will handle the outputting of the HTML
	$is_modular = True;
	*/
	
	$simple_redirect = True;
	//$simple_redirect = False;
	
	if ($simple_redirect == True)
	{
		$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'email.uiindex.index'));
		if (is_object($GLOBALS['phpgw']->msg))
		{
			// close down ALL mailserver streams
			$GLOBALS['phpgw']->msg->end_request();
			// destroy the object
			$GLOBALS['phpgw']->msg = '';
			unset($GLOBALS['phpgw']->msg);
		}
		// shut down this transaction
		$GLOBALS['phpgw']->common->phpgw_exit(False);
	}
	else
	{
		/* 
		// OBSOLETED CODE
		// pretend we are a calling app outputting some HTML, including the header and navbar
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
		// retrieve the html data from class uiindex
		$obj = CreateObject('email.uiindex');
		$obj->set_is_modular(True);
		$retured_html = $obj->index();
		// time for us to output the returned html data
		echo $retured_html;
		// now as the calling app, it's time to output the bottom of the page
		$GLOBALS['phpgw']->common->phpgw_footer();
		*/
		
		/*
		// NOTE: this does NOT WORK
		// make a uiinex object and make it do its job
		// it will output the header, navbar, class HTML data, and footer
		class uiindex_holder
		{
			var $uiindex_obj = '';
		}
		
		$my_msg_bootstrap = '';
		$my_msg_bootstrap = CreateObject('email.msg_bootstrap');
		$my_msg_bootstrap->ensure_mail_msg_exists('index.php', 3);
		
		echo 'calling CreateObject email.uiindex <br />';
		$GLOBALS['phphw_uiindex'] = new uiindex_holder;
		$GLOBALS['phphw_uiindex']->uiindex_obj = CreateObject('email.uiindex');
		echo 'done calling CreateObject email.uiindex <br />';
		$GLOBALS['phphw_uiindex']->uiindex_obj->index();
		// STRANGEly enough, menuaction=email.uiindex.index as non-module STILL requires an
		// outside-the-class entity to call common->phpgw_footer(), eventhough the class itself will
		// output the header and navbar, but it may not output common->phpgw_footer() else page gets 2 footers
		//$GLOBALS['phpgw']->common->phpgw_footer();
		*/
	}
	
?>
