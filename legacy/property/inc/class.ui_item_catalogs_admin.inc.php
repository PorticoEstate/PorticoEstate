<?php
	/**
	 * Property - Item Catalogues Admin UI Class
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
	 * Property - Item Catalogs Admin UI Class
	 *
	 * @package phpgroupware
	 * @subpackage property
	 */
	class property_ui_item_catalogs_admin
	{

		/**
		 * @var $_bo item catalogs business logic
		 */
		protected $_bo;

		/**
		 * @var $_nm next matches paging object
		 */
		protected $_nm;

		/**
		 * @var $_xslt reference to global XSLT template class
		 */
		protected $_xslt;

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$this->_bo = createObject('property.bo_items');

			$this->_nm = createObject('phpgwapi.phpgwapi.nextmatchs');

			$this->_xslt =& $GLOBALS['phpgw']->xslttpl;
		}

		/**
		 * Delete an item catalog
		 *
		 * @return void
		 */
		public function delete()
		{
		}

		/**
		 * Render an item catalog in a form
		 *
		 * @return void
		 */
		public function form()
		{
		}

		/**
		 * Display an item catalog
		 *
		 * @return void
		 */
		public function show()
		{
		}

		/**
		 * Render a list of item catalogs
		 *
		 * @return void
		 */
		public function show_list()
		{
		}

		/**
		 * Prepare an item group for being added to a form
		 *
		 * @param property_item_catalog $item_catalog the catalog to be displayed
		 *
		 * @return void
		 */

		protected function _object_to_form(property_item_catalog $item_catalog)
		{
		}
	}
