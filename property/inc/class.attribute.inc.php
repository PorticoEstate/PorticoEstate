<?php
	/**
	 * Property - Attribute Data Class
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
	 * Property - Attribute Data Class
	 *
	 * @package phpgroupware
	 * @subpackage property
	 */
	class property_attribute
	{

		/**
		 * @var string $data_type the type of data stored by the attribute
		 */
		public $data_type;

		/**
		 * @var string $descr Description of the attribute
		 */
		public $descr;

		/**
		 * @var string $display_name Human readable name of attribute
		 */
		public $display_name;

		/**
		 * @var integer $id Atrribute ID
		 */
		public $id;

		/**
		 * @var string $name Attribute Name
		 */
		public $name;

		/**
		 * @var string $unit The unit of measure stored by the attribute
		 */
		public $unit;

		/**
		 * Constructor
		 *
		 * @param array $values attribute values
		 *
		 * @return void
		 *
		 * @throws InvalidAttributeException
		 */
		public function __construct(array $values)
		{
			foreach ( $values as $key => $value )
			{
				switch ( $key )
				{
					default:
						throw new InvalidAttributeException("Invalid key: {$key}");
					case 'id':
						if ( $value != (int) $value )
						{
							throw new InvalidAttributeException("Invalid vvalue for {$key}: {$value}");
						}
						break;
					case 'data_type':
					case 'descr':
					case 'display_name':
					case 'name':
					case 'unit':
					//validation
				}
				$this->$key = $value;
			}
		}

		/**
		 * Magic string caste handler
		 *
		 * @return string the name of the attribute
		 */
		public function __toString()
		{
			return (string) $this->display_name;
		}
}