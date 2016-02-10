<?php

	/**
	 * phpGroupWare - logistic: a part of a Facilities Management System.
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package property
	 * @subpackage logistic
	 * @version $Id $
	 */
	include_class('logistic', 'model', '/inc/model/');

	class logistic_resource_type_requirement extends logistic_model
	{
		public static $so;

		protected $id;
		protected $location_id;
		protected $project_type_id;
		protected $cust_attribute_id;

		/**
		 * Constructor.  Takes an optional ID.  If a contract is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 *
		 * @param int $id the id of this project
		 */
		public function __construct(int $id = null)
		{
			$this->id = (int) $id;
		}

		public function set_id($id)
		{
			$this->id = $id;
		}

		public function get_id()
		{
			return $this->id;
		}

		public function set_location_id($location_id)
		{
			$this->location_id = $location_id;
		}

		public function get_location_id()
		{
			return $this->location_id;
		}

		public function set_project_type_id($project_type_id)
		{
			$this->project_type_id = $project_type_id;
		}

		public function get_project_type_id()
		{
			return $this->project_type_id;
		}

		public function set_cust_attribute_id($cust_attribute_id)
		{
			$this->cust_attribute_id = $cust_attribute_id;
		}

		public function get_cust_attribute_id()
		{
			return $this->cust_attribute_id;
		}

		/**
		* Get a static reference to the storage object associated with this model object
		*
		* @return the storage object
		*/
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('logistic.soresource_type_requirement');
			}

			return self::$so;
		}

		public function serialize()
		{
			$entity_so	= CreateObject('property.soadmin_entity');
			$project_so = CreateObject('logistic.soproject');
			$loc_arr = $GLOBALS['phpgw']->locations->get_name($this->get_location_id());
			$entity_arr = explode('.',$loc_arr['location']);

			$entity = $entity_so->read_single($entity_arr[2]);
			$category = $entity_so->read_single_category($entity_arr[2],$entity_arr[3]);
			$entity_label = $entity['name'];
			$category_label = $category['name'];
			$project_type_label  = $project_so->get_project_type_label($this->get_project_type_id());

			return array(
				'id' => $this->get_location_id().'-'.$this->get_project_type_id(),
				'location_id' => $this->get_location_id(),
				'entity_label' => $entity_label,
				'category_label' => $category_label,
				'project_type_label' => $project_type_label
			);
		}
	}