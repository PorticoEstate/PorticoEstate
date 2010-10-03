<?php
	/**************************************************************************\
	* phpGroupWare - iCal readonly exporter                                    *
	* http://www.phpgroupware.org                                              *
	* Copyright (c) 2003 Free Software Foundation Inc			   *
	* Written by Dave Hall - dave.hall at mbox.com.au			   *
	* Based on anon_wrapper written by Dan Kuykendall <seek3r@phpgroupware.org>*
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	/**
	* NOTES:
	*
	* Configuration
	* Change only the following 6 lines
	* login 
	* - the login you want to use for the iCal script to establish a session
	* - make sure the user exists and has access to NO apps! this user just establishes a session
	*
	* passwd 
	* - change this password to match what you have setup for this account
	*
	* domain
	* - change this if you want to only allow access to this script from a single domain
	*   for single domains and hostname based domain detection leave as is.
	*
	* prev & adv
	* - how far forwards and back the ical file will list events
	*
	* exit; comment this line below (so it reads //exit;) to enable this script
	*
	* If you don't understand these instructions, you should not use this script
	*
	* Usage
	* Call this script using a url similar to https://phpgw_user:password@server.com/phpgroupware/calendar/ical.php
	* As the password is sent in the url, it should only be run using HTTPS
	*/
	
	$login  = 'user';
	$passwd = 'changeme';
	$domain = $_SERVER['SERVER_NAME']; // if invalid it will default to the default or first domain

	$prev = -1; // number of previous months (-1 unlimited)
	$adv = -1; // number of months in advance (-1 unlimited)
	exit;

	// ** DO NOT EDIT BELOW THIS LINE ** //
	if( $_GET['mode'] != 'freebusy' && !( (isset($_GET['user']) && isset($_GET['pass']) ) || isset($_SERVER['PHP_AUTH_USER']) ) )
	{
		header('WWW-Authenticate: Basic realm="phpGW-iCal"');
		header('HTTP/1.0 401 Unauthorized');
		echo '<p>You must call this script using ';
		list($ignore, $url) = explode('//', $_SERVER['PHP_SELF']);
		echo  "'[webcal|http]://phpgw_user:password@{$url}' OR '[webcal|http]://{$url}?user=phpgw_user&amp;pass=password'</p>";
		exit;
	}
	@set_time_limit(0);
	
	//multiple domain support hack
	$_GET['domain'] = $domain;
	
	$GLOBALS['phpgw_info']['flags'] = array
					(
						'disable_Template_class'	=> True,
						'login'				=> True,
						'currentapp'			=> 'login',
						'noheader'			=> True
					);

	include('../header.inc.php');

	$login  = (isset($_GET['user']) ? $_GET['user'] : $_SERVER['PHP_AUTH_USER']);
	$passwd = (isset($_GET['pass']) ? $_GET['pass'] : $_SERVER['PHP_AUTH_PW']);

	$uid = $GLOBALS['phpgw']->accounts->name2id($login);

	$sessionid = $GLOBALS['phpgw']->session->create($login, $passwd);

	$params = array('owner' => (int)$GLOBALS['phpgw_info']['user']['person_id']);
	$owner = $params['owner'];

	$so = createObject('calendar.socalendar', array('owner' => $uid) );
	$export = createObject('calendar.boicalendar');
	
	$start['d'] = $start['m'] = $start['y'] = 0;
	if ( $prev <> -1 )
	{
		list($start['d'], $start['m'], $start['y']) = explode('-', date('d-n-Y', strtotime("-$prev months")));
	}
	
	$end['d'] = $end['m'] = $end['y'] = 0;
	if ( $adv <> -1 )
	{
		list($end['d'], $end['m'], $end['y']) = explode('-', date('d-n-Y', strtotime("+$adv months")));
	}

	$ids = $so->list_events($start['y'], $start['m'], $start['d'], $end['y'], $end['m'], $end['d']);

	if (  strtoupper($_SERVER['REQUEST_METHOD']) == 'PUT' )
	{
		$putdata = fopen("php://input", "r");
		$cal_data = "";
		
		while ($data = fread($putdata, 1024))
		{
			$cal_data .= $data;
		}
		fclose($putdata);
		
		$mime_msg = explode("\n",$cal_data);
		$export->import($mime_msg);	
	}
	else
	{
		
		$ical_mode = (isset($_GET['mode']) && strlen($_GET['mode']) ) ? $_GET['mode'] : 'freebusy';

		switch ( $ical_mode )
		{
			case 'export':
				export_events();
				break;

			case 'alarms':
				list_alarms();
				break;

			case 'freebusy':
			default:
				//do nothing for now!
		}

		$browser = createObject('phpgwapi.browser');
		$browser->content_header($login . '.ics','text/calendar');
 	}
	@$GLOBALS['phpgw']->session->destroy($sessionid, $GLOBALS['phpgw']->session->kp3);

	function list_alarms()
	{
		if(isset($_GET['time']))
		{
			$minutes = $_GET['time'];
		}
		else
		{
			$minutes = 60;
		}
		 
		// set start list to be now
		list($start['d'], $start['m'], $start['y']) = explode('-', date('d-n-Y', strtotime("now")));
		
		// set end list to be + num minutes, 60 by default
		// this has granularity of days, use something else
		list($end['d'], $end['m'], $end['y']) = explode('-', date('d-n-Y', strtotime("+".$minutes."minutes")));
 
		//$so->cal->debug=true
		// $so->list_events is not returning the correct events, uid is wrong
		$ids = $so->list_events($start['y'], $start['m'], $start['d'], $end['y'], $end['m'], $end['d'], $owner );
 
		// find the appropriate events that have alarms
		echo $export->export( array('l_event_id' => $ids, 'alarms_only' => true, 'minutes' => $minutes) );
	}

	function export_events()
	{
		list($start['d'], $start['m'], $start['y']) = explode('-', date('d-n-Y', strtotime("-$prev months")));
		list($end['d'], $end['m'], $end['y']) = explode('-', date('d-n-Y', strtotime("+$adv months")));
		$ids = $so->list_events($start['y'], $start['m'], $start['d'], $end['y'], $end['m'], $end['d']);

		echo $export->export( array('l_event_id' => $ids) );
	}

	
?>
