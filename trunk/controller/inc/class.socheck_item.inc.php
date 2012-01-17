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

	phpgw::import_class('controller.socommon');

	include_class('controller', 'check_item', 'inc/model/');
	include_class('controller', 'check_item_case', 'inc/model/');

	class controller_socheck_item extends controller_socommon
	{
		protected static $so;

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return controller_socontrol_group the storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null) {
				self::$so = CreateObject('controller.socheck_item');
			}
			return self::$so;
		}

		function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count){}

		function populate(int $object_id, &$object){}

		function add(&$check_item)
		{
			$cols = array(
					'control_item_id',
					'status',
					'comment',
					'check_list_id',
					'message_ticket_id',
					'measurement'
			);

			$values = array(
				$this->marshal($check_item->get_control_item_id(), 'int'),
				$this->marshal($check_item->get_status(), 'int'),
				$this->marshal($check_item->get_comment(), 'string'),
				$this->marshal($check_item->get_check_list_id(), 'int'),
				$this->marshal($check_item->get_measurement(), 'int')
			);

			$result = $this->db->query('INSERT INTO controller_check_item (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);

			return isset($result) ? $this->db->get_last_insert_id('controller_check_item', 'id') : 0;
		}

		function update($check_item)
		{
			$id = $check_item->get_id();

			$values = array(
				'control_item_id = ' . $this->marshal($check_item->get_control_item_id(), 'int'),
				'status = ' . $this->marshal($check_item->get_status(), 'int'),
				'comment = ' . $this->marshal($check_item->get_comment(), 'string'),
				'check_list_id = ' . $this->marshal($check_item->get_check_list_id(), 'int'),
				'measurement = ' . $this->marshal($check_item->get_measurement(), 'string')
			);

			$result = $this->db->query('UPDATE controller_check_item SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			if( isset($result) )
			{
				return $id;
			}
			else
			{
				return 0;
			}
		}

		public function get_single($check_item_id)
		{
			$sql = "SELECT ci.*, ci.id as c_id, coi.id as coi_id, coi.* ";
			$sql .= "FROM controller_check_item ci, controller_control_item coi "; 
			$sql .= "WHERE ci.id = $check_item_id ";
			$sql .= "AND ci.control_item_id=coi.id";

			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);

			if($this->db->next_record()) {
				$check_item = new controller_check_item($this->unmarshal($this->db->f('c_id', true), 'int'));
				$check_item->set_status($this->unmarshal($this->db->f('status', true), 'bool'));
				$check_item->set_comment($this->unmarshal($this->db->f('comment', true), 'string'));
				$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id', true), 'int'));
				$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));

				$control_item = new controller_control_item($this->unmarshal($this->db->f('coi_id', true), 'int'));
				$control_item->set_title($this->db->f('title', true), 'string');
				$control_item->set_required($this->db->f('required', true), 'string');
				$control_item->set_what_to_do($this->db->f('what_to_do', true), 'string');
				$control_item->set_how_to_do($this->db->f('how_to_do', true), 'string');
				$control_item->set_control_group_id($this->db->f('control_group_id', true), 'string');

				$check_item->set_control_item($control_item->toArray());

				return $check_item;
			}
			else
			{
				return null;
			}
		}
		
		public function get_single_with_cases($check_item_id, $return_type = "return_object"){
			$sql  = "SELECT ci.id as ci_id, ci.status as ci_status, control_item_id, ci.comment, ci.measurement, check_list_id, ";
			$sql .= "cic.id as cic_id, cic.status as cic_status, cic.*, ";
			$sql .= "coi.id as coi_id, coi.* ";
			$sql .= "FROM controller_check_item ci "; 
			$sql .= "LEFT JOIN controller_control_item as coi ON ci.control_item_id = coi.id ";
			$sql .= "LEFT JOIN controller_check_item_case as cic ON ci.id = cic.check_item_id ";
			$sql .= "WHERE ci.id = $check_item_id ";
											
			$this->db->query($sql);
			
			$counter = 0;
			$check_item = null;
			while ($this->db->next_record()) {
				
				if( $counter == 0 ){
									
					$check_item = new controller_check_item($this->unmarshal($this->db->f('ci_id', true), 'int'));
					$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));
					$check_item->set_status($this->unmarshal($this->db->f('ci_status', true), 'bool'));
					$check_item->set_comment($this->unmarshal($this->db->f('comment', true), 'string'));
					$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id', true), 'int'));
					$check_item->set_measurement($this->unmarshal($this->db->f('measurement', true), 'int'));
					
					$control_item = new controller_control_item($this->unmarshal($this->db->f('coi_id', true), 'int'));
					$control_item->set_title($this->db->f('title', true), 'string');
					$control_item->set_required($this->db->f('required', true), 'string');
					$control_item->set_what_to_do($this->db->f('what_to_do', true), 'string');
					$control_item->set_how_to_do($this->db->f('how_to_do', true), 'string');
					$control_item->set_control_group_id($this->db->f('control_group_id', true), 'string');
					$control_item->set_type($this->db->f('type', true), 'string');
				
					if($return_type == "return_array")
						$check_item->set_control_item($control_item->toArray());
					else
						$check_item->set_control_item($control_item);
						
					$cases_array = array();
				}
				
				if($this->db->f('cic_id', true) != ''){
					$case = new controller_check_item_case($this->unmarshal($this->db->f('cic_id', true), 'int'));
					$case->set_check_item_id($this->unmarshal($this->db->f('check_item_id', true), 'int'));
					$case->set_status($this->unmarshal($this->db->f('cic_status', true), 'int'));
					$case->set_location_id($this->unmarshal($this->db->f('location_id', true), 'int'));
					$case->set_location_item_id($this->unmarshal($this->db->f('location_item_id', true), 'int'));
					$case->set_descr($this->unmarshal($this->db->f('descr', true), 'string'));
					$case->set_user_id($this->unmarshal($this->db->f('user_id', true), 'int'));	
					$case->set_entry_date($this->unmarshal($this->db->f('entry_date', true), 'int'));
					$case->set_modified_date($this->unmarshal($this->db->f('modified_date', true), 'int'));
					$case->set_modified_by($this->unmarshal($this->db->f('modified_by', true), 'int'));
				
				
					if($return_type == "return_array")
						$cases_array[] = $case->toArray();
					else
						$cases_array[] = $case;
				}
				
				$check_item_id =  $check_item->get_id();
				$counter++;
			}
			
			if($check_item != null){
				$check_item->set_cases_array($cases_array);
				
				if($return_type == "return_array")
					return $check_item->toArray();
				else
					return $check_item;
			}else{
				return null;
			}
		}
		
		public function get_check_item_by_check_list_and_control_item($check_list_id, $control_item_id)
		{
			$sql = "SELECT ci.*, ci.id as c_id, coi.id as coi_id, coi.* ";
			$sql .= "FROM controller_check_item ci, controller_control_item coi "; 
			$sql .= "WHERE ci.check_list_id = $check_list_id ";
			$sql .= "AND ci.control_item_id = coi.id ";
			$sql .= "AND ci.control_item_id = $control_item_id";
			
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);

			if($this->db->next_record()) {
				$check_item = new controller_check_item($this->unmarshal($this->db->f('c_id', true), 'int'));
				$check_item->set_status($this->unmarshal($this->db->f('status', true), 'bool'));
				$check_item->set_comment($this->unmarshal($this->db->f('comment', true), 'string'));
				$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id', true), 'int'));
				$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));

				$control_item = new controller_control_item($this->unmarshal($this->db->f('coi_id', true), 'int'));
				$control_item->set_title($this->db->f('title', true), 'string');
				$control_item->set_required($this->db->f('required', true), 'string');
				$control_item->set_what_to_do($this->db->f('what_to_do', true), 'string');
				$control_item->set_how_to_do($this->db->f('how_to_do', true), 'string');
				$control_item->set_control_group_id($this->db->f('control_group_id', true), 'string');

				$check_item->set_control_item($control_item->toArray());

				return $check_item;
			}
			else
			{
				return null;
			}
		}
		
		public function get_check_items($check_list_id, $status, $type, $return_type = "return_object"){
			$sql  = "SELECT ci.id as ci_id, ci.status, control_item_id, ci.comment, ci.measurement, check_list_id, "; 
			$sql .= "coi.id as coi_id, coi.title, coi.required, coi.what_to_do, coi.how_to_do, coi.control_group_id, coi.type "; 
			$sql .= "FROM controller_check_item ci "; 
			$sql .= "LEFT JOIN controller_control_item as coi ON ci.control_item_id = coi.id ";
			$sql .= "WHERE ci.check_list_id = $check_list_id ";
			
			if($status == 'open')
				$sql .= "AND ci.status = 0 ";
			else if($status == 'handled')
				$sql .= "AND ci.status = 1 ";
				
			if($type != null)
				$sql .= "AND coi.type = '$type'";
								
			$this->db->query($sql);
			
			while ($this->db->next_record()) {
				$check_item = new controller_check_item($this->unmarshal($this->db->f('ci_id', true), 'int'));
				$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));
				$check_item->set_status($this->unmarshal($this->db->f('status', true), 'bool'));
				$check_item->set_comment($this->unmarshal($this->db->f('comment', true), 'string'));
				$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id', true), 'int'));
				$check_item->set_measurement($this->unmarshal($this->db->f('measurement', true), 'int'));
				
				$control_item = new controller_control_item($this->unmarshal($this->db->f('coi_id', true), 'int'));
				$control_item->set_title($this->db->f('title', true), 'string');
				$control_item->set_required($this->db->f('required', true), 'string');
				$control_item->set_what_to_do($this->db->f('what_to_do', true), 'string');
				$control_item->set_how_to_do($this->db->f('how_to_do', true), 'string');
				$control_item->set_control_group_id($this->db->f('control_group_id', true), 'string');
				$control_item->set_type($this->db->f('type', true), 'string');
				
				if($return_type == "return_array"){
					$check_item->set_control_item($control_item->toArray());
					$check_items_array[] = $check_item->toArray();
				}
				else{
					$check_item->set_control_item($control_item);
					$check_items_array[] = $check_item;
				}
			}
			
			return $check_items_array;
		}
		
		public function get_check_items_with_cases($check_list_id, $status = "open", $messageStatus = null, $return_type = "return_object"){
			$sql  = "SELECT ci.id as ci_id, ci.status as ci_status, control_item_id, ci.comment, ci.measurement, check_list_id, ";
			$sql .= "cic.id as cic_id, cic.status as cic_status, cic.*, ";
			$sql .= "coi.id as coi_id, coi.* ";
			$sql .= "FROM controller_check_item ci "; 
			$sql .= "LEFT JOIN controller_control_item as coi ON ci.control_item_id = coi.id ";
			$sql .= "LEFT JOIN controller_check_item_case as cic ON ci.id = cic.check_item_id ";
			$sql .= "WHERE ci.check_list_id = $check_list_id ";
			
			if($status == 'open')
				$sql .= "AND cic.status = 0 ";
			else if($status == 'closed')
				$sql .= "AND cic.status = 1 ";
				
			if($messageStatus != null & $messageStatus == 'no_message_registered')
				$sql .= "AND cic.location_item_id IS NULL ";
			else if($messageStatus != null &  $messageStatus == 'message_registered')
				$sql .= "AND cic.location_item_id > 0 ";
											
			$this->db->query($sql);
			
			$check_item_id = 0;
			$check_item = null;
			while ($this->db->next_record()) {
				
				if( $this->db->f('ci_id', true) != $check_item_id ){
					
					if($check_item_id != 0){
						$check_item->set_cases_array($cases_array);
						
						if($return_type == "return_array")
							$check_items_array[] = $check_item->toArray();
						else
							$check_items_array[] = $check_item;
					}
				
					$check_item = new controller_check_item($this->unmarshal($this->db->f('ci_id', true), 'int'));
					$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));
					$check_item->set_status($this->db->f('ci_status', true), 'int');
					$check_item->set_comment($this->unmarshal($this->db->f('comment', true), 'string'));
					$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id', true), 'int'));
					$check_item->set_measurement($this->unmarshal($this->db->f('measurement', true), 'int'));
					
					$control_item = new controller_control_item($this->unmarshal($this->db->f('coi_id', true), 'int'));
					$control_item->set_title($this->db->f('title', true), 'string');
					$control_item->set_required($this->db->f('required', true), 'string');
					$control_item->set_what_to_do($this->db->f('what_to_do', true), 'string');
					$control_item->set_how_to_do($this->db->f('how_to_do', true), 'string');
					$control_item->set_control_group_id($this->db->f('control_group_id', true), 'string');
					$control_item->set_type($this->db->f('type', true), 'string');
				
					if($return_type == "return_array")
						$check_item->set_control_item($control_item->toArray());
					else
						$check_item->set_control_item($control_item);
							
					$cases_array = array();
				}
				
				if($this->db->f('cic_id', true) != ''){
					$case = new controller_check_item_case($this->unmarshal($this->db->f('cic_id', true), 'int'));
					$case->set_check_item_id($this->unmarshal($this->db->f('check_item_id', true), 'int'));
					$case->set_status($this->unmarshal($this->db->f('cic_status', true), 'int'));
					$case->set_location_id($this->unmarshal($this->db->f('location_id', true), 'int'));
					$case->set_location_item_id($this->unmarshal($this->db->f('location_item_id', true), 'int'));
					$case->set_descr($this->unmarshal($this->db->f('descr', true), 'string'));
					$case->set_user_id($this->unmarshal($this->db->f('user_id', true), 'int'));	
					$case->set_entry_date($this->unmarshal($this->db->f('entry_date', true), 'int'));
					$case->set_modified_date($this->unmarshal($this->db->f('modified_date', true), 'int'));
					$case->set_modified_by($this->unmarshal($this->db->f('modified_by', true), 'int'));
				
				
					if($return_type == "return_array")
						$cases_array[] = $case->toArray();
					else
						$cases_array[] = $case;
				}
				
				$check_item_id = $check_item->get_id();
			}
			
			if($check_item != null){
				$check_item->set_cases_array($cases_array);
				
				if($return_type == "return_array")
					$check_items_array[] = $check_item->toArray();
				else
					$check_items_array[] = $check_item;
				
				return $check_items_array;
			}else {
				return null;
			}
		}
		
		public function get_check_items_with_cases_by_message($message_ticket_id, $return_type = "return_object"){
			$sql  = "SELECT ci.id as ci_id, ci.status as ci_status, control_item_id, ci.comment, ci.measurement, "; 
			$sql .= "check_list_id, cic.id as cic_id, cic.status as cic_status, cic.*, ";
			$sql .= "coi.id as coi_id, coi.* ";
			$sql .= "FROM controller_check_item ci "; 
			$sql .= "LEFT JOIN controller_control_item as coi ON ci.control_item_id = coi.id ";
			$sql .= "LEFT JOIN controller_check_item_case as cic ON ci.id = cic.check_item_id ";
			$sql .= "WHERE cic.location_item_id = $message_ticket_id";
											
			$this->db->query($sql);
			
			$check_item_id = 0;
			$check_item = null;
			while ($this->db->next_record()) {
				
				if( $this->db->f('ci_id', true) != $check_item_id ){
					
					if($check_item_id != 0){
						$check_item->set_cases_array($cases_array);
						
						if($return_type == "return_array")
							$check_items_array[] = $check_item->toArray();
						else
							$check_items_array[] = $check_item;
					}
				
					$check_item = new controller_check_item($this->unmarshal($this->db->f('ci_id', true), 'int'));
					$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));
					$check_item->set_status($this->unmarshal($this->db->f('ci_status', true), 'bool'));
					$check_item->set_comment($this->unmarshal($this->db->f('comment', true), 'string'));
					$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id', true), 'int'));
					$check_item->set_measurement($this->unmarshal($this->db->f('measurement', true), 'int'));
					
					$control_item = new controller_control_item($this->unmarshal($this->db->f('coi_id', true), 'int'));
					$control_item->set_title($this->db->f('title', true), 'string');
					$control_item->set_required($this->db->f('required', true), 'string');
					$control_item->set_what_to_do($this->db->f('what_to_do', true), 'string');
					$control_item->set_how_to_do($this->db->f('how_to_do', true), 'string');
					$control_item->set_control_group_id($this->db->f('control_group_id', true), 'string');
					$control_item->set_type($this->db->f('type', true), 'string');
				
					if($return_type == "return_array")
						$check_item->set_control_item($control_item->toArray());
					else
						$check_item->set_control_item($control_item);
									
					$cases_array = array();
				}
				
				if($this->db->f('cic_id', true) != ''){
					$case = new controller_check_item_case($this->unmarshal($this->db->f('cic_id', true), 'int'));
					$case->set_status($this->unmarshal($this->db->f('cic_status', true), 'int'));
					$case->set_check_item_id($this->unmarshal($this->db->f('check_item_id', true), 'int'));
					$case->set_location_id($this->unmarshal($this->db->f('location_id', true), 'int'));
					$case->set_location_item_id($this->unmarshal($this->db->f('location_item_id', true), 'int'));
					$case->set_descr($this->unmarshal($this->db->f('descr', true), 'string'));
					$case->set_user_id($this->unmarshal($this->db->f('user_id', true), 'int'));	
					$case->set_entry_date($this->unmarshal($this->db->f('entry_date', true), 'int'));
					$case->set_modified_date($this->unmarshal($this->db->f('modified_date', true), 'int'));
					$case->set_modified_by($this->unmarshal($this->db->f('modified_by', true), 'int'));
				
				
					if($return_type == "return_array")
						$cases_array[] = $case->toArray();
					else
						$cases_array[] = $case;
				}
				
				$check_item_id =  $check_item->get_id();
			}
			
			if($check_item != null){
				$check_item->set_cases_array($cases_array);
				
				if($return_type == "return_array")
					$check_items_array[] = $check_item->toArray();
				else
					$check_items_array[] = $check_item;
				
				return $check_items_array;
			}else {
				return null;
			}
		}
		
		public function get_check_items_by_message($message_ticket_id, $return_type = "return_array" ){
			$sql  = "SELECT ci.* "; 
			$sql .= "FROM controller_check_item ci "; 
			$sql .= "LEFT JOIN controller_check_item_case as cic ON ci.id = cic.check_item_id ";
			$sql .= "WHERE cic.location_item_id = $message_ticket_id ";
								
			$this->db->query($sql);
			
			while ($this->db->next_record()) {
				$check_item = new controller_check_item($this->unmarshal($this->db->f('id', true), 'int'));
				$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));
				$check_item->set_status($this->unmarshal($this->db->f('status', true), 'bool'));
				$check_item->set_comment($this->unmarshal($this->db->f('comment', true), 'string'));
				$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id', true), 'int'));
				$check_item->set_measurement($this->unmarshal($this->db->f('measurement', true), 'int'));
				
				if($return_type == "return_array")
					$check_items_array[] = $check_item->toArray();
				else
					$check_items_array[] = $check_item;
			}
			
			return $check_items_array;
		}

		function get_id_field_name(){}
	}
