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

	class logistic_requirement extends logistic_model
	{

		public static $so;
		
		protected $id;
		protected $activity_id;
		protected $date_from;
		protected $date_to;
		protected $no_of_elements;
		protected $location_id;
		
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

		public function set_no_of_elements($no_of_elements)
		{
			$this->no_of_elements = $no_of_elements;
		}

		public function get_no_of_elements()
		{
			return $this->no_of_elements;
		}
		
		public function set_location_id($location_id)
		{
			$this->location_id = $location_id;
		}

		public function get_location_id()
		{
			return $this->location_id;
		}

		public function set_activity_id($activity_id)
		{
			$this->activity_id = $activity_id;
		}

		public function get_activity_id()
		{
			return $this->activity_id;
		}

		public function set_date_from($date_from)
		{
			$this->date_from = $date_from;
		}

		public function get_date_from()
		{
			return $this->date_from;
		}

		public function set_date_to($date_to)
		{
			$this->date_to = $date_to;
		}

		public function get_date_to()
		{
			return $this->date_to;
		}

		/**
		* Get a static reference to the storage object associated with this model object
		*
		* @return the storage object
		*/
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('logistic.sorequirement');
			}

			return self::$so;
		}

		public function serialize()
		{
			return array(
				'requirement_id' => $this->get_requirement_id(),
				'activity_id' => $this->get_activity_id(),
				'date_from' => $this->get_date_from(),
				'date_to' => $this->get_date_to()
			);
		}
	}