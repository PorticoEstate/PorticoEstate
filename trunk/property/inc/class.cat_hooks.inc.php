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
	class property_cat_hooks
	{

		/**
		 * Handle a new category being added, create location to hold ACL-data
		 */
		function cat_add( $data )
		{
			if (isset($data['cat_owner']) && $data['cat_owner'] != -1)
			{
				return false; //nothing needed to be done, we only care about global cats
			}

			$location = '';
			if ($data['location_id'])
			{
				$location_info = $GLOBALS['phpgw']->locations->get_name($data['location_id']);
				$location = $location_info['location'];
			}
			$GLOBALS['phpgw']->locations->add("{$location}.category.{$data['cat_id']}", $data['cat_name'], 'property');
		}

		/**
		 * Handle a category being deleted, remove the location 
		 */
		function cat_delete( $data )
		{
			if (isset($data['cat_owner']) && $data['cat_owner'] != -1)
			{
				return false; //nothing needed to be done, we only care about global cats
			}
			if ($data['location_id'])
			{
				$location_info = $GLOBALS['phpgw']->locations->get_name($data['location_id']);
				$location = "{$location_info['location']}.category.{$data['cat_id']}";
				$GLOBALS['phpgw']->locations->delete('property', $location, false);
			}
		}

		/**
		 * Handle a category being edited, update the location info
		 */
		function cat_edit( $data )
		{
			if (isset($data['cat_owner']) && $data['cat_owner'] != -1)
			{
				return false; //nothing needed to be done, we only care about global cats
			}

			if ($data['location_id'])
			{
				$location_info = $GLOBALS['phpgw']->locations->get_name($data['location_id']);
				$location = "{$location_info['location']}.category.{$data['cat_id']}";
				$GLOBALS['phpgw']->locations->update_description($location, $data['cat_name'], 'property');
			}
		}
	}