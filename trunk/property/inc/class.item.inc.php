<?php
	/**
	* Property - Item Data Class
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
	* Property - Item Data Class
	*
	* @package phpgroupware
	* @subpackage property
	*/
	class property_item
	{
		/**
		 * @var array $attribute array of property_attribute_values for tem
		 */
		public $attributes = array();

		/**
		 * @var integer $catalog_id the catalog the item belongs to
		 */
		public $catalog_id;

		/**
		 * @var integer $id property item identifier
		 */
		public $id;

		/**
		 * @var array $location the location the item is installed at
		 */
		public $location;

		/**
		 * @var integer the parent item identifier
		 */
		public $parent;

		/**
		 * @var array $vendor the vendor who supplied the item as a phpgw_contacts array
		 */
		public $vendor;

		/**
		 * Constructor
		 *
		 * @param array $values the values for the item
		 *
		 * @return void
		 *
		 * @throws InvalidItemException
		 */
		public function __construct(array $values)
		{
			foreach ( $values as $key => $value )
			{
				switch ( $key )
				{
					default:
						throw new InvalidItemException("Invalid Key: {$key}");

					case 'id':
					case 'catalog_id':
					case 'parent':
						if ( $value != (int) $value )
						{
							throw new InvalidItemException("Invalid value for {$key}: {$value}");
						}
						break;

					case 'attributes':
					case 'location':
					case 'vendor':
						//validation
				}

				$this->$key = $value;
			}
		}
		
		/**
		 * Magic string casting handler
		 *
		 * @return string something
		 */
		public function __toString()
		{
			// Need to decide what goes here
		}
	}
