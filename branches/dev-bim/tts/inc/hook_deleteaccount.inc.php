<?php
	/**
	* Trouble Ticket System
	*
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @subpackage hooks
	* @version $Id$
	*/

	$account_id = phpgw::get_var('account_id', 'int');
	$new_owner = phpgw::get_var('new_owner', 'int');

	$GLOBALS['phpgw']->db->query("UPDATE phpgw_tts_tickets SET ticket_owner = {new_owner}"
		. " WHERE ticket_owner = {$account_id}", __LINE__, __FILE__);

	$GLOBALS['phpgw']->db->query("UPDATE phpgw_tts_tickets SET ticket_assignedto = {new_owner}"
		. " WHERE ticket_owner = {$account_id}", __LINE__, __FILE__);

	$GLOBALS['phpgw']->db->query("UPDATE phpgw_tts_views SET ticket_views = {new_owner}"
		. " WHERE ticket_owner = {$account_id}", __LINE__, __FILE__);
