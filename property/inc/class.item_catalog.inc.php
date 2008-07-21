<?php
	/**
	 * Property - Item Catalog Data Class
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
	 * Property - Item Catalog Data Class
	 *
	 * @package phpgroupware
	 * @subpackage property
	 */
	class property_item_catalog
	{

		/**
		 * @var string $descrt description of the item catalog
		 */
		public $descr;

		/**
		 * @var integer $group_id the group the catalog belongs to
		 */
		public $group_id;

		/**
		 * @var integer $id the identifier for the item catalog
		 */
		public $id;

		/**
		 * @var string $name the name of the item catalog
		 */
		public $name;

		/**
		 * Constructor
		 *
		 * @param array $values the item catalog values
		 *
		 * @return void
		 *
		 * @throws InvalidItemCatalogException
		 */
		public function __construct(array $values)
		{
			foreach ( $values as $key => $value )
			{
				switch ( $key )
				{
					default:
						throw new InvalidItemCatalogException("Invalid key: {$key}");

					case 'group_id':
					case 'id':
						if ( $value != (int) $value )
						{
							throw new InvalidItemCatalogException("Invalid value for {$key}: {$value}");
						}
						break;

					case 'descr':
					case 'name':
					//validation
				}
				$this->$key = $value;
			}
		}

		/**
		 * Magic string caste handler
		 *
		 * @return string name of the item catalog
		 */
		public function __toString()
		{
			return (string) $this->name;
		}
	}