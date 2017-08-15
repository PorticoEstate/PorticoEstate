<?php

	/**
	 * phpGroupWare - controller: a part of a Facilities Management System.
	 *
	 * @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @author Torstein Vadla <torstein.vadla@bouvet.no>
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
	 * @subpackage controller
	 * @version $Id$
	 */
	class check_list_status_info
	{

		private $check_list_id;
		private $control_id;
		private $status;
		private $status_text;
		private $deadline_date_ts;
		private $deadline_date_txt;
		private $original_deadline_date_ts;
		private $info_text;
		private $location_code;
		private $component_id;
		private $location_id;
		private $type;
		private $num_open_cases;
		private $serie_id;
		private $assigned_to;
		private $billable_hours;

		public function __construct()
		{
			
		}

		public function set_check_list_id( $check_list_id )
		{
			$this->check_list_id = $check_list_id;
		}

		public function get_check_list_id()
		{
			return $this->check_list_id;
		}

		public function set_control_id( $control_id )
		{
			$this->control_id = $control_id;
		}

		public function get_control_id()
		{
			return $this->control_id;
		}

		public function set_status( $status )
		{
			$this->status = $status;
		}

		public function get_status()
		{
			return $this->status;
		}

		public function set_status_text( $status_text )
		{
			$this->status_text = $status_text;
		}

		public function get_status_text()
		{
			return $this->status_text;
		}

		public function set_deadline_date_ts( $deadline_date_ts )
		{
			$this->deadline_date_ts = $deadline_date_ts;
		}

		public function get_deadline_date_ts()
		{
			return $this->deadline_date_ts;
		}
		
		public function set_original_deadline_date_ts( $original_deadline_date_ts )
		{
			$this->original_deadline_date_ts = $original_deadline_date_ts;
		}

		public function get_original_deadline_date_ts()
		{
			return $this->original_deadline_date_ts;
		}

		public function set_deadline_date_txt( $deadline_date_txt )
		{
			$this->deadline_date_txt = $deadline_date_txt;
		}

		public function get_deadline_date_txt()
		{
			return $this->deadline_date_txt;
		}

		public function set_info_text( $info_text )
		{
			$this->info_text = $info_text;
		}

		public function get_info_text()
		{
			return $this->info_text;
		}

		public function set_location_code( $location_code )
		{
			$this->location_code = $location_code;
		}

		public function get_location_code()
		{
			return $this->location_code;
		}

		public function set_location_id( $location_id )
		{
			$this->location_id = $location_id;
		}

		public function get_location_id()
		{
			return $this->location_id;
		}

		public function set_component_id( $component_id )
		{
			$this->component_id = $component_id;
		}

		public function get_component_id()
		{
			return $this->component_id;
		}

		public function get_type()
		{
			return $this->type;
		}

		public function set_type( $type )
		{
			$this->type = $type;
		}

		public function set_num_open_cases( $num_open_cases )
		{
			$this->num_open_cases = $num_open_cases;
		}

		public function get_num_open_cases()
		{
			return $this->num_open_cases;
		}

		public function set_assigned_to( $assigned_to )
		{
			$this->assigned_to = $assigned_to;
		}

		public function get_assigned_to()
		{
			return $this->assigned_to;
		}

		public function set_billable_hours( $billable_hours )
		{
			$this->billable_hours = $billable_hours;
		}

		public function get_billable_hours()
		{
			return $this->billable_hours;
		}

		public function set_serie_id( $serie_id )
		{
			$this->serie_id = $serie_id;
		}

		public function get_serie_id()
		{
			return $this->serie_id;
		}

		public function serialize()
		{
			return array(
				'check_list_id' => $this->get_check_list_id(),
				'control_id' => $this->get_control_id(),
				'status' => $this->get_status(),
				'status_text' => $this->get_status_text(),
				'deadline_date_ts' => $this->get_deadline_date_ts(),
				'deadline_date_txt' => $this->get_deadline_date_txt(),
				'original_deadline_date_ts' => $this->get_original_deadline_date_ts(),
				'info_text' => $this->get_info_text(),
				'location_code' => $this->get_location_code(),
				'location_id' => $this->get_location_id(),
				'component_id' => $this->get_component_id(),
				'type' => $this->get_type(),
				'num_open_cases' => $this->get_num_open_cases(),
				'assigned_to' => $this->get_assigned_to(),
				'billable_hours' => $this->get_billable_hours(),
				'serie_id' => $this->get_serie_id()
			);
		}
	}