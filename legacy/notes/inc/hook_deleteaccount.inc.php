<?php
	/**
	* Notes
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package notes
	* @version $Id$
	*/

	/*
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 3 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

	// Delete all records for a user
	$db =& $GLOBALS['phpgw']->db;
	$db->lock(array('phpgw_notes'));

	if ( (int) $_POST['new_owner'] == 0 )
	{
		$db->query('DELETE FROM phpgw_notes WHERE note_owner='. (int) $_POST['account_id'], __LINE__, __FILE__);
	}
	else
	{
		$db->query('UPDATE phpgw_notes SET note_owner=' . (int) $_POST['new_owner']
			. ' WHERE note_owner=' . (int) $_POST['account_id'], __LINE__, __FILE__);
	}
	$db->unlock();
?>
