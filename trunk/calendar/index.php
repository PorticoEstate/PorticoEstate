<?php
  /**************************************************************************\
  * phpGroupWare - Calendar                                                  *
  * http://www.phpgroupware.org                                              *
  * Based on Webcalendar by Craig Knudsen <cknudsen@radix.net>               *
  *          http://www.radix.net/~cknudsen                                  *
  * Modified by Mark Peters <skeeter@phpgroupware.org>                       *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: index.php,v 1.51 2006/11/10 13:34:30 sigurdne Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'	=> 'calendar',
		'noheader'	=> True,
		'nonavbar'	=> True,
		'noappheader'	=> True,
		'noappfooter'	=> True,
		'nofooter'	=> True
	);
	include('../header.inc.php');

	phpgw::import_class('phpgwapi.datetimefunctions');
	
	$cal = createObject('calendar.uicalendar');
	$cal->index(array
	(
			'menuaction'	=> 'calendar.uicalendar.index',
			'date'		=> date('Ymd', phpgwapi_datetime::user_localtime() )
	));
?>
