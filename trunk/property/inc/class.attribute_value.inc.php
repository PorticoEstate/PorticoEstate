<?php
	/**
	* Property - Attribute Value Data Class
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
	* Property - Attribute Value Data Class
	*
	* @package phpgroupware
	* @subpackage property
	*/
	class property_attribute_value
	{
		/**
		 * @param property_attribute attribute object for value
		 */
		public $attribute;

		/**
		 * @var string the value of attribute
		 */
		public $value;

		/**
		 * Constructor
		 *
		 * @param string             $value  value of attribute
		 * @param property_attribute $attrib the attribute for value
		 */
		public function __construct($value, property_attribute $attrib)
		{
			$this->value = $value;
			$this->attribute = $attrib;
		}

		/**
		 * Return object as a string
		 *
		 * @return string attribute value and unit of measure
		 */
		public function __toString()
		{
			return "{$this->value} {$this->attribute->unit}";
		}
	}
