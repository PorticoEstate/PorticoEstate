<?php
	/**************************************************************************\
	* phpGroupWare                                                             *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: hook_deleteaccount.inc.php 17949 2007-02-13 15:02:07Z sigurdne $ */

	// Delete all records for a user
	$info = CreateObject('infolog.soinfolog');
	$info->change_delete_owner($_POST['account_id'], $_POST['new_owner']);
	unset($info);
?>
