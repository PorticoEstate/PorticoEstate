<?php
	/**
	 * Property - Item Catalogs Storage Class
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
	 * Property - Item Catalogs Storage Class
	 *
	 * @package phpgroupware
	 * @subpackage property
	 */
	class property_so_item_catalogs
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
		 * Add a new item catalogue to the database
		 *
		 * @param property_item_catalog $catalog the catalog to add
		 *
		 * @return integer the new database id for the catalog
		 */
		public function add(property_item_catalog $catalog)
		{
			//prepare the object for insertion
			return $id;
		}

		/**
		 * Delete an item catalog from the database
		 *
		 * @param integer $catalog_id the database id of the catalog
		 *
		 * @return boolean was the catalog deleted?
		 */
		public function delete($catalog_id)
		{
			$catalog_id = (int) $catalog_id;

			$items = createObject('property.bo_items')->find('catalog_id' => $catalog_id);
			if ( count($items) )
			{
				// items are still attached can't delete
				return false;
			}

			$sql = "DELETE FROM property_item_catalogs WHERE catalog_id = {$catalog_id}";

			return (bool) $this->_db->query($sql);
		}

		/**
		 * Edit an existing item catalog
		 *
		 * @param property_item_catalog $catalog the catalog to update
		 *
		 * @return boolean was the catalog updated?
		 */
		public function edit(property_item_catalog $catalog)
		{
			$update = $this->diff($catalog);

			$sql = ''; // prepare query
			$this->_db->query($sql);

			return $this->_db->affected_rows() == 1;
		}

		/**
		 * Find item catalogs
		 *
		 * @param array $criteria the search criteria
		 *
		 * @return array list of property_item_catalogs - empty array if none found
		 */
		public function find(array $criteria)
		{
			$sql = ''; // prepare query
			$catalogs = array();
			$this->_db->query($sql);
			while ( $this->_db->next_record() )
			{
				$record = array(); // add record here

				$catalogs[] = new property_item_catalog($record);
			}

			return $catalogs;
		}

		/**
		 * Fetch an item catalog from the database
		 *
		 * @param integer $catalog_id the database id of the catalog to retrieve
		 *
		 * @return property_item_catalog the item catalog
		 *
		 * @throws InvalidItemCatalogException
		 */
		public function get($catalog_id)
		{
			$catalog_id = (int) $catalog_id;

			$sql = 'SELECT * FROM property_item_catalog'
				. " WHERE catalog_id = {$catalog_id}";

			$this->_db->query($sql);
			if ( ! $this->_db->next_record() )
			{
				throw new InvalidItemCatalogException("Invalid attribute id: {$attrib_id}");
			}

			$record = array(); // record values here

			$attrib = new property_item_catalog($record);
		}

		/**
		 * Compare the values of property_item_catalog object
		 *
		 * @param property_item_catalog $catalog the new object
		 *
		 * @return array the changed values
		 */
		protected function _diff(property_item_catalog $catalog)
		{
			$diff = array();

			$old_catalog = $this->get($catalog->id);

			// prepare diff

			return $diff;
		}
	}