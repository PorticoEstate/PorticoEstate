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
		private $status;
		private $status_text;
		private $deadline_date;
		private $info_text;

		public function __construct(){}
		
		public function set_check_list_id($check_list_id)
		{
			$this->check_list_id = $check_list_id;
		}
		
		public function get_check_list_id() { return $this->check_list_id; }
		
		public function set_status($status)
		{
			$this->status = $status;
		}
		
		public function get_status() { return $this->status; }
		
		public function set_status_text($status_text)
		{
			$this->status_text = $status_text;
		}
		
		public function get_status_text() { return $this->status_text; }
		
		public function set_deadline_date($deadline_date)
		{
			$this->deadline_date = $deadline_date;
		}
		
		public function get_deadline_date() { return $this->deadline_date; }
		
		public function set_info_text($info_text)
		{
			$this->info_text = $info_text;
		}
		
		public function get_info_text() { return $this->info_text; }
		
		
		public function serialize()
		{
			return array(
				'check_list_id' => $this->get_check_list_id(),
				'status' => $this->get_status(),
				'status_text' => $this->get_status_text(),
				'deadline_date' => $this->get_deadline_date(),
				'info_text' => $this->get_info_text()
			);
		}
	}
