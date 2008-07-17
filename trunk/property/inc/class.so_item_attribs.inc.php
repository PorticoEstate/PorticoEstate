<?php
	/**
	 * Property - Item Attributes Storage Class
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright (c) 2008 Dave Hall http://davehall.com.au
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
	 * @version $Id$
	 * @package phpgroupware
	 * @subpackage property
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * Property - Item Attributes Storage Class
	 *
	 * @package phpgroupware
	 * @subpackage property
	 */
	class property_so_item_attribs
	{

		/**
		 * @var phpgwapi_db $_db Reference to global database object
		 */
		protected $_db;

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			$this->_db = & $GLOBALS['phpgw']->db;
		}

		/**
		 * Add a new attribute to the database
		 *
		 * @param property_attribute $attrib
		 */
		public function add(property_attribute $attrib)
		{
			// prepare object and store it in the database
		}

		/**
		 * Disable an attribute in the database
		 *
		 * @param integer $attrib_id the attribute id
		 *
		 * @return bool was the attribute disabled
		 */
		public function disable($attrib_id)
		{
			$sql = 'UPDATE property_attributes SET is_active = 0'
				. ' WHERE attrib_id = ' . (int) $attrib_id;

			$this->_db->query($sql);

			return $this->_db->affected_rows == 1;
		}

		/**
		 * Edit an existing attribute
		 *
		 * @param property_attribute $attrib the new values for the attribute
		 *
		 * @return bool was the item updated?
		 */
		public function edit(property_attribute $attrib)
		{
			// prepare and store the new attribute values
		}

		/**
		 * Find attributes in the database
		 *
		 * @param array $criteria the search criteria
		 *
		 * @return array list of property_attributes - empty array if none found
		 */
		public function find(array $criteria)
		{
			$attribs = array();

			// process criteria

			// execute query

			return $attribs;
		}

		/**
		 * Fetch and attribute from the database
		 *
		 * @param integer $attrib_id the attribute to fetch
		 *
		 * @return property_attribute
		 */
		public function get($attrib_id)
		{
			$attrib_id = (int) $attrib_id;

			$sql = 'SELECT * FROM property_attributes'
				. " WHERE attribute_id = {$attrib_id}";

			$this->_db->query($sql);
			if ( ! $this->_db->next_record() )
			{
				throw new InvalidAttributeException("Invalid attribute id: {$attrib_id}");
			}

			$record = array(); // record values here

			$attrib = new property_attribute($record);

			return $attrib;
		}

		/**
		 * Compare the values of property_attribute object
		 *
		 * @param property_attribute $attrib the new object
		 *
		 * @return array the changed values
		 */
		protected function _diff(property_attribute $attrib)
		{
			$changes = array();

			$old_attrib = $this->get($attrib->id);

			// process changes

			return $changes;
		}
	}