<?php
	/**
	* Todo - delete account hook
	*
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package todo
	* @subpackage hooks
	* @version $Id$
	*/

	$account_id = phpgw::get_var('account_id', 'int');
	$new_owner = phpgw::get_var('new_owner', 'int');

	// Delete all records for a user
	$db =& $GLOBALS['phpgw']->db;

	if ( !$new_owner )
	{
		$db->query("DELETE FROM phpgw_todo WHERE todo_owner = {$account_id}", __LINE__, __FILE__);
	}
	else
	{
		$db->query("UPDATE phpgw_todo SET todo_owner = {$new_owner}"
			. " WHERE todo_owner = {$account_id}", __LINE__, __FILE__);
	}
