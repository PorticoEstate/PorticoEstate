<?php
	/**
	* Todo - delete account hook
	*
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package todo
	* @subpackage hooks
	* @version $Id: hook_deleteaccount.inc.php 17078 2006-09-05 10:53:09Z skwashd $
	*/
	
	// Delete all records for a user
	$db =& $GLOBALS['phpgw']->db;
	$db->lock('phpgw_todo');

	$new_owner = intval(get_var('new_owner',Array('POST')));
	$account_id = intval(get_var('account_id',Array('POST')));
	if($new_owner==0)
	{
		$db->query('DELETE FROM phpgw_todo WHERE todo_owner=' . (int) $_POST['account_id'], __LINE__, __FILE__);
	}
	else
	{
		$db->query('UPDATE phpgw_todo SET todo_owner=' . (int) $_POST['new_owner']
			. ' WHERE todo_owner=' . (int) $_POST['account_id'], __LINE__, __FILE__);
	}
	$db->unlock();
?>
