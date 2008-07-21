<?php
	/**
	 * Property - Item Groups Class
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
	 * Property - Item Groups Logic Class
	 *
	 * @package phpgroupware
	 * @subpackage property
	 */
	class property_bo_item_groups
	{
		/**
		 * @var property_so_item_groups Holder for storage object
		 */
		protected $_so;

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			$this->_so = createObject('property.so_item_groups');
		}

		/**
		 * Delete an item group
		 *
		 * @param integer $group_id The id of the group to be deleted
		 *
		 * @return boolean was the item group deleted?
		 */
		public function delete($group_id)
		{
			return $this->_so->delete($group_id);
		}

		/**
		 * Search for an item group
		 *
		 * @param array $criteria the search criteria
		 *
		 * @return array list of item groups found
		 */
		public function find(array $criteria)
		{
			$list = $this->_so->find($criteria);

			// any look ups needed go here

			return $list;
		}

		/**
		 * Fetch a single item group
		 *
		 * @param integer $group_id the identifier of the group to fecth
		 *
		 * @return property_item_group the item group
		 */
		public function get($group_id)
		{
			$group = $this->so->get($group_id);

			// any other logic here

			return $group;
		}

		/**
		 * Save an item group
		 *
		 * @param property_item_group $item_group the item group to save
		 *
		 * @return integer the identifier for the item group
		 */
		public function save(property_item_group $item_group)
		{
			if ( $item_group->id )
			{
				return $this->_so->edit($item_group);
			}

			return $this->_so->add($item_group);
		}
	}