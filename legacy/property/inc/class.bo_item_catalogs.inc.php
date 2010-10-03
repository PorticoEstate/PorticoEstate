<?php
	/**
	 * Property - Item Catalog Logic Class
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
	 * Property - Item Catalogs Logic Class
	 *
	 * @package phpgroupware
	 * @subpackage property
	 */
	class property_bo_item_catalogs
	{
		/**
		 * @var property_item_catalogs $_so storage object
		 */
		protected $_so;

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			$this->_so = createObject('property.item_catalogs');
		}

		/**
		 * Delete a catalog
		 *
		 * @param integer $catalog_id the identifier for the catalog
		 *
		 * @return boolean was the catalog deleted?
		 */
		public function delete($catalog_id)
		{
			return $this->_so->delete($catalog_id);
		}

		/**
		 * Fecth a list of item catalogs
		 *
		 * @param array $criteria the search criteria
		 *
		 * @return array list of catalogs or empty array if none found
		 */
		public function find(array $criteria)
		{
			$list = $this->_so->find($criteria);

			// further processing

			return $list;
		}

		/**
		 * Get an item catalog
		 *
		 * @param integer $catalog_id the item catalog sought
		 *
		 * @return property_item_catalog the item catalog
		 */
		public function get($catalog_id)
		{
			$catalog = $this->_so->get($catalog_id);

			// any lookups here

			return $catalog;
		}

		/**
		 * Store an item catalog
		 *
		 * @param property_item_catalog $catalog the catalog to store
		 *
		 * @return integer the identifier for the catalog stored
		 */
		public function save(property_item_catalog $catalog)
		{
			if ( $catalog->id )
			{
				return $this->_so->edit($catalog);
			}

			return $this->_so->add($catalog);
		}
	}
