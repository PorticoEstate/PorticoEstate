<?php
	/**
	* Property - Item Groups Data Class
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
	* Property - Item Groups Data Class
	*
	* @package phpgroupware
	* @subpackage property
	*/

	class property_item_group
	{
		/**
		 * @var integer $id item group identifier
		 */
		public $id;

		/**
		 * @var string $name name of item group
		 */
		public $name;

		/**
		 * @var string $nat_std_no national building standard group identifier
		 */
		public $nat_std_no;

		/**
		 * @var string $part_no the building part the item is connected to
		 */
		public $part_no;

		/**
		 * Constructor
		 *
		 * @param array $values the values for the item group
		 *
		 * @return void
		 *
		 * @throws InvalidItemGroupException
		 */
		public function __construct(array $values)
		{
			foreach ( $values as $key => $value )
			{
				switch ( $key )
				{
					default:
						throw new InvalidItemGroupException("Invalid key: {$key}");

					case 'id':
						if ( $value != (int) $value )
						{
							throw new InvalidItemGroupException("Invalid value for {$key}: {$value}");
						}
						break;

					case 'name':
					case 'nat_std_no':
					case 'part_no':
						//validation
				}

				$this->$key = $value;
			}
		}
	
		/**
		 * Magic string caste handler
		 *
		 * @return string the item group name
		 */
		public function __toString()
		{
			return (string) $this->name;
		}
	}
