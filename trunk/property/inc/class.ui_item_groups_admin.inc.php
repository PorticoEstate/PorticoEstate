<?php
	/**
	 * Property - Item Groups Admin UI Class
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
	 * Property - Item Groups UI Class
	 *
	 * @package phpgroupware
	 * @subpackage property
	 */
	class property_ui_item_groups_admin
	{
		/**
		 * @var $_bo item groups logic layer
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
		 * Assign catalog items to a group
		 *
		 * @return void
		 */
		public function assign()
		{
		}

		/**
		 * Delete an item group
		 *
		 * @return void
		 */
		public function delete()
		{
		}

		/**
		 * Render the add edit form
		 *
		 * @return void
		 */
		public function form()
		{
		}

		/**
		 * Display an item group record
		 *
		 * @return void
		 */
		public function show()
		{
		}

		/**
		 * Render a list of item groups
		 *
		 * @return void
		 */
		public function show_list()
		{
		}

		/**
		 * Prepare an item group before being displayed in a form
		 *
		 * @param
		 *
		 * @return void
		 */
		protected function _object_to_form(property_item_group $item_group)
		{
		}
	}