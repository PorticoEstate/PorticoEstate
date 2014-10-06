<?php
	/**
	* phpGroupWare Preferences - delete account hook
	*
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @author Mark Peters <skeeter@phpgroupware.org>
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

	// Delete all records for a user
	$account_id = phpgw::get_var('account_id', 'int');
	$sql = "DELETE FROM phpgw_preferences WHERE preference_owner = {$account_id}";
	$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
