<?php
	/**
	* News Admin Hooks Manager
	* @author Dave Hall - skwashd at phpgroupware.org
	* @copyright Copyright (C) 2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @version $Id$
	*/

	/**
	* Centralise hook management to make life a little easier
	*
	* @package news_admin
	*/
	class news_admin_bo_hooks
	{
		
		/**
		* Handle a new category being added, namely to create the required location 
		* and db table for custom fields
		*/
		function cat_add($cat_data)
		{
			if ( $cat_data['cat_owner'] != -1 )
			{
				return false; //nothing needed to be done, we only care about global cats
			}
			$GLOBALS['phpgw']->acl->add_location("L{$cat_data['cat_id']}", lang('news category: %1', $cat_data['cat_name']), 'news_admin', true);
		}

		/**
		* Handle a category being deleted, namely to remove the location 
		* and table for custom fields
		*/
		function cat_delete($cat_data)
		{
			if ( $cat_data['cat_owner'] != -1 )
			{
				return false; //nothing needed to be done, we only care about global cats
			}
			$GLOBALS['phpgw']->acl->delete_repository('news_admin', "L{$cat_data['cat_id']}");
		}
		
		/**
		* Handle a category being editted, namely to update the location info
		*/
		function cat_edit($cat_data)
		{
			if ( $cat_data['cat_owner'] != -1 )
			{
				return false; //nothing needed to be done, we only care about global cats
			}
			$GLOBALS['phpgw']->acl->update_location_description("L{$cat_data['cat_id']}", lang('news category: %1', $cat_data['cat_name']), 'news_admin');
		}
	}
?>
