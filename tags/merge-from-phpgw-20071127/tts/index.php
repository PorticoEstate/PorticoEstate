<?php
	/**
	* Trouble Ticket System
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @version $Id: index.php 17413 2006-10-14 05:39:42Z skwashd $
	*/

	/* Note to self:
	** Self ... heres the query to use when limiting access to entrys within a group
	** The acl class *might* handle this instead .... not sure
	** select distinct group_ticket_id, phpgw_tts_groups.group_ticket_id, phpgw_tts_tickets.*
	** from phpgw_tts_tickets, phpgw_tts_groups where ticket_id = group_ticket_id and group_id in (14,15);
	*/

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'	=> 'tts',
		'noheader'		=> true
	);
	
	/**
	 * Include phpgroupware header
	 */
	include('../header.inc.php');
	
	$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'tts.uitts.index') );
?>
