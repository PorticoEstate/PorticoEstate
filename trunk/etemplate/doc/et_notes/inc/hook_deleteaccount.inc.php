<?php
 /**********************************************************************\
 * phpGroupWare - eTemplate						*
 * http://www.phpgroupware.org						*
 * This program is part of the GNU project, see http://www.gnu.org/	*
 *									*
 * Copyright 2002, 2003 Free Software Foundation, Inc.			*
 *									*
 * Originally Written by Mark Peters - <skeeter at phpgroupware.org>	*
 * --------------------------------------------				*
 * This program is Free Software; you can redistribute it and/or modify *
 * it under the terms of the GNU General Public License as published by *
 * the Free Software Foundation; either version 2 of the License, or 	*
 * at your option) any later version.					*
 \**********************************************************************/
 /* $Id$ */
	
	// Delete all records for a user
	$table_locks = Array('phpgw_et_notes');
	$db2 = $GLOBALS['phpgw']->db;
	$db2->lock($table_locks);

	if ( $_POST['new_owner'] == 0 )
	{
		$db2->query('DELETE FROM phpgw_et_notes WHERE note_owner = ' . intval($_POST['account_id']), __LINE__, __FILE__);
	}
	else
	{
		$db2->query('UPDATE phpgw_et_notes SET note_owner=' . intval($_POST['new_owner'])
			. ' WHERE note_owner=' . intval($_POST['account_id']), __LINE__, __FILE__);
	}
	$db2->unlock();
?>
