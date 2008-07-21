<?php
	/**
	 * Property - Item Groups Storage Data Class
	 *
	 * @author Dave Hall <dave.hall@skwashd.com>
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
	 * Property - Item Groups Storage Data Class
	 *
	 * @package phpgroupware
	 * @subpackage property
	 */
	class property_so_item_groups
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
		 * Add a new item group to the database
		 *
		 * @param property_item_group $group the item group to add
		 *
		 * @return integer the new item group id
		 */
		public function add(property_item_group $group)
		{
			$sql = ''; // use instance variables to create SQL for insert

			$this->_db->query($sql);
			if ( $this->_db->query($sql) )
			{
				return 0;
			}

			// process and insert group into database

			$id = $this->_db->get_insert_id('property_item_groups', 'group_id');
			return $id;
		}

		/**
		 * Delete a group from the database
		 *
		 * @param integer $group_id the ID of the group to delete
		 *
		 * @return boolean was the group deleted?
		 */
		public function delete($group_id)
		{
			$group_id = (int) $group_id;

			$lookup = array('group_id' => $group_id);

			$catalogs = createObject('property.bo_item_catalogs')->find($lookup);

			if ( count($catalogs) )
			{
				// item catalogs are still attached can't delete
				return false;
			}

			$sql = "DELETE FROM property_item_groups WHERE group_id = {$group_id}";
			return (bool) $this->_db->query($sql);
		}

		/**
		 * Edit an existing item group
		 *
		 * @param property_item_group $group the new group values
		 *
		 * @return boolean was the item updated?
		 */
		public function edit(property_item_group $group)
		{
			// diff object

			// prepare and update group

			return $this->_db->affected_rows() == 1;
		}

		/**
		 * Find a list of item groups
		 *
		 * @param array $criteria the search criteria
		 *
		 * @return array list of property_item_groups - empty array if none found
		 */
		public function find(array $criteria)
		{
			$sql = ''; // prepare query

			$list = array();

			$this->_db->query($sql);
			while ( $this->_db->next_record() )
			{
				$record = array
				(
					//fields here
				);

				$list[] = $record;
			}

			return $list;
		}

		/**
		 * Fetch a single item group record from the database
		 *
		 * @param integer $group_id the id of the item group to fetch
		 *
		 * @return property_item_group the item sought
		 *
		 * @throws InvalidItemGroupException
		 */
		public function get($group_id)
		{
			$group_id = (int) $group_id;

			$sql = 'SELECT * FROM property_item_groups'
				. " WHERE group_id = {$group_id}";

			$this->_db - query($sql);
			if ( ! $this->_db->next_record() )
			{
				throw new InvalidItemGroupException("Invalid item group id: {$group_id}");
			}

			$record = array(); // fetch record

			$group = new property_item_group($record);

			return $group;
		}

		/**
		 * Compare a property_item_group object to the existing object
		 *
		 * @param property_item_group $group the new item group values
		 *
		 * @return array the changed values
		 */
		protected function _diff(property_item_group $group)
		{
			$diff = array();

			$old_group = $this->get($group->id);

			// diff objects

			return $diff;
		}
	}
