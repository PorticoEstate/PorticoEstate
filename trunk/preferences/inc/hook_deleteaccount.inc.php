<?php
	/**
	* Preferences - delete account hook
	*
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package preferences
	* @version $Id: hook_deleteaccount.inc.php 17078 2006-09-05 10:53:09Z skwashd $
	*/

	// Delete all records for a user
	$GLOBALS['phpgw']->db->lock('phpgw_preferences');
	$GLOBALS['phpgw']->db->query('DELETE FROM phpgw_preferences WHERE preference_owner=' . (int) $_POST['account_id'],__LINE__,__FILE__);
	$GLOBALS['phpgw']->db->unlock();


?>
