<?php
	/**
	 * Property - Item Logic Class
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
	 * Property - Items Logic Class
	 *
	 * @package phpgroupware
	 * @subpackage property
	 */
	class property_bo_items
	{

		/**
		 * @var proprty_so_items $_so reference to storage object
		 */
		protected $_so;

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			$this->_so = createObject('property.so_items');
		}

		/**
		 * Delete an item
		 *
		 * @param integer $item_id The id of the item to be deleted
		 *
		 * @return boolean was the item deleted?
		 */
		public function delete($item_id)
		{
			if ( !$GLOBALS['phpgw']->acl->check_rights('property', 'items', phpgwapi_acl::DELETE) )
			{
				throw new AccessDeniedException('No Rights');
			}

			return $this->_so->delete($item_id);
		}

		/**
		 * Search for an item
		 *
		 * @param array $criteria the search criteria
		 *
		 * @return array list of property_item which were found
		 */
		public function find(array $criteria)
		{
			if ( !$GLOBALS['phpgw']->acl->check_rights('property', 'items', phpgwapi_acl::READ) )
			{
				throw new AccessDeniedException('No Rights');
			}

			return $this->_so->find($criteria);
		}

		/**
		 * Fetch an item
		 *
		 * @param integer $item_id the item identifier being fetched
		 *
		 * @return property_item the property item sought
		 *
		 * @throws InvalidItemException if not found
		 */
		public function get($item_id)
		{
			if ( !$GLOBALS['phpgw']->acl->check_rights('property', 'items', phpgwapi_acl::READ) )
			{
				throw new AccessDeniedException('No Rights');
			}

			return $this->_so->get($item_id);
		}

		/**
		 * Import an item list from a file
		 *
		 * @param string $file the full path to the file to import the items from
		 *
		 * @return boolean were the entries imported sucessfully?
		 */
		public function import_from_file($file)
		{
			if ( preg_match('/\.\./', $file) )
			{
				// path traversal?
				return false;
			}

			$fp = fopen($file);

			//read field format info from top line of file
			$cols = fgetcsv($fp);
			// process more here
			$columns = array(); //final processed version

			while ( ($row = fgetcsv($fp)) !== false )
			{
				if ( !$this->_process_row($row, $columns) )
				{
					fclose($fp);
					// bail out invalid data - throw Exception?
					return false;
				}
			}

			fclose($fp);

			return true;
		}

		/**
		 * Save an item
		 *
		 * @param property_item $item the item to save
		 *
		 * @return integer the item id
		 */
		public function save(property_item $item)
		{
			$rights = phpgwapi_acl::READ;
			if ( $item->id )
			{
				$rights = phpgwapi_acl::EDIT;
			}

			if ( !$GLOBALS['phpgw']->acl->check_rights('property', 'items', $rights) )
			{
				throw new AccessDeniedException('No Rights');
			}

			if ( $item->id )
			{
				return $this->_so->edit($item);
			}

			return $this->_so->add($item);
		}

		/**
		 * Convert an item row array to an object and add it to the database
		 *
		 * @param array $row     the row to be processed
		 * @param array $columns the column names and formats
		 *
		 * @return boolean was the row added?
		 */
		protected function _process_row($row, $columns)
		{
			$data = array();
			// big switch/case block to process data

			$item = new property_item($data);

			return (boolean) $this->save($item);
		}
	}
