<?php
	/**
	 * Property - Item Storage Class
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

	/*
	 * Import items data class
	 */
	phpgw::import_class('property.item');

	/**
	 * Property - Item Storage Class
	 *
	 * @package phpgroupware
	 * @subpackage property
	 */
	class property_so_items
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
		 * Add a new item entry to the database
		 *
		 * @param property_item $item the item to add to the database
		 *
		 * @return integer the database id for the item
		 */
		public function add(property_item $item)
		{
			// Prepare and store the object in the database
			$id = $this->_db->get_insert_id('property_items', 'item_id');
			return $id;
		}

		/**
		 * Delete an item from the database
		 *
		 * @param integer $item_id the database id for the item
		 *
		 * @return boolean was the item deleted?
		 */
		public function delete($item_id)
		{
			$sql = 'DELETE FROM property_items'
				. ' WHERE item_id = ' . (int) $item_id;

			return (bool) $this->_db->query($sql);
		}

		/**
		 * Edit an existing item record
		 *
		 * @param property_item $item the new values for the item
		 *
		 * @return boolean was the item updated?
		 */
		public function edit(property_item $item)
		{
			// diff object

			$sql = ''; // prepare and update changes
			$this->_db->query($sql);

			return $this->_db->affected_rows() == 1;
		}

		/**
		 * Find items in the database
		 *
		 * @param array $criteria the search criteria
		 *
		 * @return array property_items found - empty array for none found
		 */
		public function find(array $criteria)
		{
			$items = array();

			// process criteria

			// execute query

			return $items;
		}

		/**
		 * Fetch a single item from the database
		 *
		 * @param integer $item_id the database id for the item
		 *
		 * @return property_item the property item found
		 *
		 * @throws InvalidItemException if item not found
		 */
		public function get($item_id)
		{
			$item_id = (int) $item_id;

			$sql = 'SELECT * FROM property_items'
				. " WHERE item_id = {$item_id}";

			$this->_db->query($sql);
			if ( ! $this->_db->next_record() )
			{
				throw new InvalidItemException("Invalid item id: {$item_id}");
			}

			$record = array(); //insert fields here

			$item = new property_item($record);

			// fetch attributes
			$attribs = array();

			$item->attributes = $attribs;

			return $item;
		}

		/**
		 * Find the changed values in the item and return them as an array
		 *
		 * @param property_item $item the new property_item object to diff
		 *
		 * @return array the changed values
		 */
		protected function _diff(property_item $item)
		{
			$old_item = $this->get($item->id);

			$diff = array();

			// check instance variables and add to array if it has changed

			return $diff;
		}
	}