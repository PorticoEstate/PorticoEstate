<?php
	/**
	* phpGroupWare Filemanager - delete account hook
	*
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/ GNU General Public License v2 or later
	* @package phpgroupware
	* @subpackage addressbook
	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	// NOTE: This is untested
	// WIP: it should get all files owned by $account_id, not just in /home/account_id
	// Should also be capable of transfering files to another user

	/*
	$phpgw->vfs->working_id = $account_id;
	$ls_array = $phpgw->vfs->ls ($phpgw->vfs->fakebase . "/" . $account_id, array (RELATIVE_NONE));
	while (list ($num, $entry) = each ($ls_array))
	{
		$phpgw->vfs->rm ($entry["dir"] . "/" . $entry["name"], array (RELATIVE_NONE));
	}
	*/
