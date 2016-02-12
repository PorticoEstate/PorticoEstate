<?php
	/**
	 * property - Hook helper
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2015 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package booking
	 * @version $Id: class.hook_helper.inc.php 13774 2015-08-25 13:29:40Z sigurdne $
	 */
	/*
	  This program is free software: you can redistribute it and/or modify
	  it under the terms of the GNU General Public License as published by
	  the Free Software Foundation, either version 2 of the License, or
	  (at your option) any later version.

	  This program is distributed in the hope that it will be useful,
	  but WITHOUT ANY WARRANTY; without even the implied warranty of
	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	  GNU General Public License for more details.

	  You should have received a copy of the GNU General Public License
	  along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Hook helper
	 *
	 * @package booking
	 */
	class booking_hook_helper
	{
		/*
		  $args = array
		  (
		  'id'		=> $category['id'],
		  'location'	=> $function_name,
		  );

		  $GLOBALS['phpgw']->hooks->single($args, 'booking');
		 */

		/**
		 * Handle a new activity being added, create location to hold ACL-data
		 */
		function activity_add( $data )
		{
			$GLOBALS['phpgw']->locations->add(".application.{$data['id']}", $data['name'], 'booking', false, null, false, true);
			$GLOBALS['phpgw']->locations->add(".resource.{$data['id']}", $data['name'], 'booking', false, null, false, true);
		}

		/**
		 * Handle a activity being deleted, remove the location
		 */
		function activity_delete( $data )
		{
			$GLOBALS['phpgw']->locations->delete('booking', ".application.{$data['id']}", false);
			$GLOBALS['phpgw']->locations->delete('booking', ".resource.{$data['id']}", false);
		}

		/**
		 * Handle a activity being edited, update the location info
		 */
		function activity_edit( $data )
		{
			$GLOBALS['phpgw']->locations->update_description(".application.{$data['id']}", $data['name'], 'booking');
			$GLOBALS['phpgw']->locations->update_description(".resource.{$data['id']}", $data['name'], 'booking');
		}
	}