<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2014 Free Software Foundation, Inc. http://www.fsf.org/
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
 	* @version $Id: class.cat_hooks.inc.php 8281 2011-12-13 09:24:03Z sigurdne $
	*/

	/**
	* hook management for categories
	* @package admin
	*/
	class admin_cat_hooks
	{
		
		/**
		 * Handle a new category being added, create location to hold ACL-data
		 */
		function cat_add($data)
		{
			if ( isset($data['cat_owner']) && $data['cat_owner'] != -1 )
			{
				return false; //nothing needed to be done, we only care about global cats
			}

			if($data['location_id'])
			{
				$location_info = $GLOBALS['phpgw']->locations->get_name($data['location_id']);
				$location = $location_info['location'];
				if($location == 'vfs_filedata')
				{
					$GLOBALS['phpgw']->locations->add("vfs_filedata.{$data['cat_id']}", $data['cat_name'], 'admin', false, false, false, true);				
				}
			}
		}

		/**
		 * Handle a category being deleted, remove the location 
		 */
		function cat_delete($data)
		{
			if ( isset($data['cat_owner']) && $data['cat_owner'] != -1 )
			{
				return false; //nothing needed to be done, we only care about global cats
			}
			if($data['location_id'])
			{
				$location_info = $GLOBALS['phpgw']->locations->get_name($data['location_id']);
				if($location_info['location'] == 'vfs_filedata')
				{
					$location = "{$location_info['location']}.{$data['cat_id']}";
					$GLOBALS['phpgw']->locations->delete('admin', $location, false);
				}
			}
		}

		/**
		 * Handle a category being edited, update the location info
		 */
		function cat_edit($data)
		{
			if ( isset($data['cat_owner']) && $data['cat_owner'] != -1 )
			{
				return false; //nothing needed to be done, we only care about global cats
			}

			if($data['location_id'])
			{
				$location_info = $GLOBALS['phpgw']->locations->get_name($data['location_id']);
				if($location_info['location'] == 'vfs_filedata')
				{
					$location = "{$location_info['location']}.{$data['cat_id']}";
					$GLOBALS['phpgw']->locations->update_description($location, $data['cat_name'], 'admin');
				}
			}
		}
	}
