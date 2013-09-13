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

	class logistic_requirement_resource_allocation extends logistic_model
	{
		public static $so;

		protected $id;
		protected $requirement_id;
		protected $resource_id;
		protected $inventory_id;
		protected $inventory_amount;
		protected $location_id;
		protected $create_user;
		
		protected $resource_type_descr;
		protected $location_code;
		protected $fm_bim_item_address;
		protected $fm_bim_item_name;
		protected $start_date;
		protected $end_date;
		protected $allocated_amount;
		protected $allocated_amount_orig;
		protected $ticket_id;

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

		public function set_requirement_id($requirement_id)
		{
			$this->requirement_id = $requirement_id;
		}

		public function get_requirement_id()
		{
			return $this->requirement_id;
		}

		public function set_resource_id($resource_id)
		{
			$this->resource_id = $resource_id;
		}

		public function get_resource_id()
		{
			return $this->resource_id;
		}


		public function set_inventory_id($inventory_id)
		{
			$this->inventory_id = $inventory_id;
		}

		public function get_inventory_id()
		{
			return $this->inventory_id;
		}

		public function set_inventory_amount($inventory_amount)
		{
			$this->inventory_amount = $inventory_amount;
		}

		public function get_inventory_amount()
		{
			return $this->inventory_amount;
		}

		public function set_location_id($location_id)
		{
			$this->location_id = $location_id;
		}

		public function get_location_id()
		{
			return $this->location_id;
		}
		
		public function set_create_user($create_user)
		{
			$this->create_user = $create_user;
		}

		public function get_create_user()
		{
			return $this->create_user;
		}
		
		public function set_resource_type_descr($resource_type_descr)
		{
			$this->resource_type_descr = $resource_type_descr;
		}

		public function get_resource_type_descr()
		{
			return $this->resource_type_descr;
		}

		public function set_location_code($location_code)
		{
			$this->location_code = $location_code;
		}

		public function get_location_code()
		{
			return $this->location_code;
		}
		
		public function set_fm_bim_item_address($fm_bim_item_address)
		{
			$this->fm_bim_item_address = $fm_bim_item_address;
		}

		public function get_fm_bim_item_address()
		{
			return $this->fm_bim_item_address;
		}
		
		public function set_fm_bim_item_name($fm_bim_item_name)
		{
			$this->fm_bim_item_name = $fm_bim_item_name;
		}

		public function get_fm_bim_item_name()
		{
			return $this->fm_bim_item_name;
		}


		public function set_end_date($end_date)
		{
			$this->end_date = $end_date;
		}

		public function get_end_date()
		{
			return $this->end_date;
		}

		public function set_start_date($start_date)
		{
			$this->start_date = $start_date;
		}

		public function get_start_date()
		{
			return $this->start_date;
		}

		public function set_allocated_amount($allocated_amount)
		{
			$this->allocated_amount = $allocated_amount ? $allocated_amount : 1;
		}

		public function get_allocated_amount()
		{
			return $this->allocated_amount;
		}

		public function set_allocated_amount_orig($allocated_amount)
		{
			$this->allocated_amount_orig = $allocated_amount ? $allocated_amount : 1;
		}

		public function get_allocated_amount_orig()
		{
			return $this->allocated_amount_orig;
		}



		public function set_ticket_id($ticket_id)
		{
			$this->ticket_id = $ticket_id;
		}

		public function get_ticket_id()
		{
			return $this->ticket_id;
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
			$values = array
			(
				'id' 					=> $this->get_id(),
				'resource_type_descr'	=> $this->get_resource_type_descr(),
				'requirement_id' 		=> $this->get_requirement_id(),
				'resource_id' 			=> $this->get_resource_id(),
				'inventory_id' 			=> $this->get_inventory_id(),
				'ticket_id' 			=> $this->get_ticket_id(),
				//FIXME
				'inventory_amount' 		=> $this->get_inventory_amount(),
				'allocated_amount' 		=> $this->get_allocated_amount(),
				'location_id'	 		=> $this->get_location_id(),
				'location_code' 		=> $this->get_location_code(),
				'fm_bim_item_address'	=> $this->get_fm_bim_item_address(),
				'fm_bim_item_name'		=> $this->get_fm_bim_item_name(),
				'start_date'			=> $this->get_start_date(),
				'end_date'				=> $this->get_end_date()
			);

			return $values;
		}
	}
