<?php
  /**************************************************************************\
  * phpGroupWare                                                             *
  * http://www.phpgroupware.org                                              *
  * Written by Joseph Engo <jengo@phpgroupware.org>                          *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: hook_deleteaccount.inc.php 17078 2006-09-05 10:53:09Z skwashd $ */

	$contacts = CreateObject('phpgwapi.contacts');

	if ( (int) $_POST['new_owner'] == 0 )
	{
		$contacts->delete_all(intval($_POST['account_id']));
	}
	else
	{
		$contacts->change_owner($_POST['account_id'], $_POST['new_owner']);
		$contacts->change_owner_others($_POST['account_id'], $_POST['new_owner']);
	}
?>
