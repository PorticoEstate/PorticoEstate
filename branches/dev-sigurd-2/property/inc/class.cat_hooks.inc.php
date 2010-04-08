<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage entity
 	* @version $Id$
	*/

	/**
	* hook management for categories
	* @package property
	*/
	class property_bo_hooks
	{
		
		/**
		* Handle a new category being added, create location to hold ACL-data
		*/
		function cat_add($cat_data)
		{
			if ( isset($cat_data['cat_owner']) && $cat_data['cat_owner'] != -1 )
			{
				return false; //nothing needed to be done, we only care about global cats
			}
	//		$GLOBALS['phpgw']->acl->add_location("C{$cat_data['cat_id']}", lang('ticket type: %1', $cat_data['cat_name']), 'tts', true, "phpgw_tts_c{$cat_data['cat_id']}");
		}

		/**
		* Handle a category being deleted, remove the location 
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
	//		$GLOBALS['phpgw']->acl->update_location_description("C{$cat_data['cat_id']}", lang('ticket type: %1', $cat_data['cat_name']), 'tts');
		}
	}
