<?php
	/**
	 * Property - Items Admin UI Class
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
	 * Import parent class
	 */
	phpgw::import_class('property.ui_items');

	/**
	 * Property - Item Admin UI Class
	 *
	 * @package phpgroupware
	 * @subpackage property
	 */
	class property_ui_items_admin extends property_ui_items 
	{
		public function __construct()
		{
			parent::__construct();
		}

		public function import()
		{
		}

		public function purge_history()
		{
		}
	}
