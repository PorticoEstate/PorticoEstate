<?php
	/**
	 * Property - Item Attributes Logic Class
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
	 * Property - Item Attributes Logic Class
	 *
	 * @package phpgroupware
	 * @subpackage property
	 */
	class property_bo_item_attribs
	{
		/**
		 * @var property_so_item_attribs $_so the storage object
		 */
		protected $_so;

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			$this->_so = createObject('property.so_item_attribs');
		}

		/**
		 * Disable an attribute
		 *
		 * @param integer $attrib_id the identifier for the attribute to disable
		 *
		 * @return boolean was the attribute disabled
		 */
		public function disable($attrib_id)
		{
			// ACL check using phpgwapi_acl::DELETE
			return $this->_so->disable($attrib_id);
		}

		/**
		 * Grab a list of attributes from the database
		 *
		 * @param array $criteria the search criteria
		 *
		 * @return array list of found attributes - empty if none found
		 */
		public function find(array $criteria)
		{
			// ACL check using phpgwapi_acl::READ

			$attribs = $this->_so->find($criteria);

			// post processing

			return $attribs;
		}

		/**
		 * Get a attribute record
		 *
		 * @param integer $attrib_id the attribute identifier
		 *
		 * @return property_attribute the attribute fetched
		 */
		public function get($attrib_id)
		{
			// ACL check using phpgwapi_acl::READ

			$attrib = $this->_so->get($attrib_id);

			// any other processing here

			return $attrib;
		}

		/**
		 * Store an attribute
		 *
		 * @param property_attribute $attrib the attribute to be saved
		 *
		 * @return integer the unique id for the attribute
		 */
		public function save(property_attribute $attrib)
		{
			// ACL check using phpgwapi_acl::ADD | EDIT

			if ( $attrib->id )
			{
				return $this->_so->edit($attrib);
			}

			return $this->_so->save($attrib);
		}
	}
