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

  /* $Id$ */

	/* I think this can go - skwashd Nov 2007
	if( isset($GLOBALS['phpgw_info']['server']['calendar_type'])
		&& $GLOBALS['phpgw_info']['server']['calendar_type'] == 'mcal'
		&& !extension_loaded('mcal') )
	{
		$GLOBALS['phpgw_info']['server']['calendar_type'] = 'sql';
	}
	else
	{
		$GLOBALS['phpgw_info']['server']['calendar_type'] = 'sql';
	}
	*/

	$GLOBALS['phpgw_info']['server']['calendar_type'] = 'sql';

	phpgw::import_class('calendar.socalendar__');
	phpgw::import_class('calendar.socalendar_sql');
