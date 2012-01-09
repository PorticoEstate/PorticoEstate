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
 	* @version $Id: class.check_item.inc.php 8478 2012-01-03 12:36:37Z vator $
	*/

	include_class('controller', 'model', 'inc/model/');
	
	class controller_check_item_case extends controller_model
	{
		public static $so;

                    'user_id' => array('type' => 'int','precision' => '4','nullable' => true),
                    'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => false),
                    'modified_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
                    'modified_by' => array('type' => 'int', 'precision' => 4,'nullable' => True),
              ),
		
		
		protected $id;
		protected $check_item_id;
		protected $status;
		protected $location_id;
		protected $location_item_id;
		protected $descr;
		protected $user_id;
		protected $entry_date;
		protected $entry_date;
						
		/**
		 * Constructor.  Takes an optional ID.  If a contract is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this composite
		 */
		public function __construct(int $id = null)
		{
			$this->id = (int)$id;
		}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_id() { return $this->id; }

		public function set_control_item_id($control_item_id)
		{
			$this->control_item_id = $control_item_id;
		}
		
		public function get_control_item_id() { return $this->control_item_id; }
				
		public function set_status($status)
		{
			$this->status = (int)$status;
		}
		
		public function get_status() { return (int)$this->status; }
		
		public function set_comment($comment)
		{
			$this->comment = $comment;
		}
		
		public function get_comment() { return $this->comment; }
		
		public function set_check_list_id($check_list_id)
		{
			$this->check_list_id = $check_list_id;
		}
		
		public function get_check_list_id() { return $this->check_list_id; }
		
		public function set_message_ticket_id($message_ticket_id)
		{
			$this->message_ticket_id = $message_ticket_id;
		}
		
		public function get_message_ticket_id() { return $this->message_ticket_id; }
		
		public function set_measurement($measurement)
		{
			$this->measurement = $measurement;
		}
		
		public function get_measurement() { return $this->measurement; }
		
		
		// =================  Getters and setters for objects =================
		
		public function set_control_item($control_item)
		{
			$this->control_item = $control_item;
		}
		
		public function get_control_item() { return $this->control_item; }
			
			
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
/*			if (self::$so == null) {
				self::$so = CreateObject('controller.socontrol_item');
			}
			
			return self::$so;*/
		}
	}
