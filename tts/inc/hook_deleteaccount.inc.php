<?php
	/**
	* Trouble Ticket System
	*
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @subpackage hooks
	* @version $Id: hook_deleteaccount.inc.php 15932 2005-05-10 16:12:38Z powerstat $
	*/

	$GLOBALS['phpgw']->db->query("UPDATE phpgw_tts_tickets SET ticket_owner = " 
		. intval($GLOBALS['HTTP_POST_VARS']['new_owner'])
		. " WHERE ticket_owner=" . intval($GLOBALS['HTTP_POST_VARS']['account_id']) 
		);

	$GLOBALS['phpgw']->db->query("UPDATE phpgw_tts_tickets SET ticket_assignedto = "
		. intval($GLOBALS['HTTP_POST_VARS']['new_owner'])
		. " WHERE ticket_assignedto=" . intval($GLOBALS['HTTP_POST_VARS']['account_id'])
	);

	$GLOBALS['phpgw']->db->query("UPDATE phpgw_tts_views SET view_account_id = "
		. intval($GLOBALS['HTTP_POST_VARS']['new_owner'])
		. " WHERE view_account_id=" . intval($GLOBALS['HTTP_POST_VARS']['account_id'])
	);
?>
