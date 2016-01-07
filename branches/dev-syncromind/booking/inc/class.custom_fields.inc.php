<?php
	/**
	 * phpGroupWare custom fields
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package phpgroupware
	 * @subpackage booking
	 * @version $Id: class.custom_fields.inc.php 14622 2016-01-05 08:54:38Z sigurdne $
	 */
	/*
	  This program is free software: you can redistribute it and/or modify
	  it under the terms of the GNU General Public License as published by
	  the Free Software Foundation, either version 2 of the License, or
	  (at your option) any later version.

	  This program is distributed in the hope that it will be useful,
	  but WITHOUT ANY WARRANTY; without even the implied warranty of
	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	  GNU Lesser General Public License for more details.

	  You should have received a copy of the GNU General Public License
	  along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/*
	 * Import the parent class
	 */
	phpgw::import_class('phpgwapi.custom_fields');

	/**
	 * Custom Fields
	 *
	 * @package phpgroupware
	 * @subpackage booking
	 */
	class booking_custom_fields extends phpgwapi_custom_fields
	{

		/**
		 * Constructor
		 *
		 * @param string $appname the name of the module using the custom fields
		 *
		 * @return void
		 */
		public function __construct($appname = null)
		{
			parent::__construct($appname);
		}

		/**
		 *
		 * @param type $location
		 * @return  array the grouped attributes
		 */
		public function get_fields($location)
		{
			$appname	 = 'booking';
			$attributes	 = parent::find($appname, $location, 0, '', 'ASC', 'attrib_sort', true, true);
			return $this->get_field_groups($appname, $location, $attributes);
		}

		/**
		 * Arrange attributes within groups
		 *
		 * @param string  $location    the name of the location of the attribute
		 * @param array   $attributes  the array of the attributes to be grouped
		 *
		 * @return array the grouped attributes
		 */
		private function get_field_groups($appname, $location, $attributes = array())
		{
			return parent::get_attribute_groups($appname, $location, $attributes);
		}
	}