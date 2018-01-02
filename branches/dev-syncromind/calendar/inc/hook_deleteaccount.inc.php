<?php
  /**************************************************************************\
  * phpGroupWare                                                             *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <skeeter@phpgroupware.org>                        *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
	/* $Id$ */

	$account_id = phpgw::get_var('account_id', 'int');
	$new_owner = phpgw::get_var('new_owner', 'int');

	// Delete all records for a user
	if ( !$new_owner )
	{
		ExecMethod('calendar.bocalendar.delete_calendar', $account_id);
	}
	else
	{
		ExecMethod('calendar.bocalendar.change_owner',
			array
			(
				'old_owner'	=> $account_id,
				'new_owner'	=> $new_owner
			));
	}
