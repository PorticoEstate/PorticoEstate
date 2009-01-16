<?php
	/**
	* TTS Hooks Manager
	* @author Dave Hall - skwashd at phpgroupware.org
	* @copyright Copyright (C) 2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @version $Id$
	*/

	/**
	* Centralise hook management to make life a little easier
	*
	* @package tts
	*/
	class tts_bo_hooks
	{
		
		/**
		* Handle a new category being added, namely to create the required location 
		* and db table for custom fields
		*/
		function cat_add($cat_data)
		{
			if ( isset($cat_data['cat_owner']) && $cat_data['cat_owner'] != -1 )
			{
				return false; //nothing needed to be done, we only care about global cats
			}
			$GLOBALS['phpgw']->acl->add_location("C{$cat_data['cat_id']}", lang('ticket type: %1', $cat_data['cat_name']), 'tts', true, "phpgw_tts_c{$cat_data['cat_id']}");
			
			// we interupt your normal programming for this special annoucement
			$hon = $GLOBALS['phpgw']->db->Halt_On_Error;
			$GLOBALS['phpgw']->db->Halt_On_Error = 'report';
			
			$oProc = CreateObject('phpgwapi.schema_proc',$GLOBALS['phpgw_info']['server']['db_type']);
			$oProc->m_odb =& $GLOBALS['phpgw']->db;
			
			$oProc->CreateTable("phpgw_tts_c{$cat_data['cat_id']}", array
			(
				'fd' => array
				(
					'ticket_id'	=> array('type' => 'int', 'precision' => 4, 'nullable' => false)
				),
				'pk' => array('ticket_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			));
			
			// we now return you to your normal prograaming
			$GLOBALS['phpgw']->db->Halt_On_Error = $hon;
		}

		/**
		* Handle a category being deleted, namely to remove the location 
		* and table for custom fields
		*/
		function cat_delete($cat_data)
		{
			if ( isset($cat_data['cat_owner']) && $cat_data['cat_owner'] != -1 )
			{
				return false; //nothing needed to be done, we only care about global cats
			}
			//TODO add code here to delete the ticket types and to clean up the ACL table
		}
		
		/**
		* Handle a category being editted, namely to update the location info
		*/
		function cat_edit($cat_data)
		{
			if ( isset($cat_data['cat_owner']) && $cat_data['cat_owner'] != -1 )
			{
				return false; //nothing needed to be done, we only care about global cats
			}
			$GLOBALS['phpgw']->acl->update_location_description("C{$cat_data['cat_id']}", lang('ticket type: %1', $cat_data['cat_name']), 'tts');
		}
	}
?>
