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
			if (self::$so == null)
			{
				self::$so = CreateObject('controller.socheck_item');
			}
			return self::$so;
		}

		/**
		 * Add a new check item object to database
		 * 
		 * @param	$check_item check item oject to be added
		 * @return id of the inserted check item, 0 otherwise 
		*/
		function add(&$check_item)
		{
			$cols = array(
					'control_item_id',
					'check_list_id'
			);

			$values = array(
				$this->marshal($check_item->get_control_item_id(), 'int'),
				$this->marshal($check_item->get_check_list_id(), 'int')
			);

			$result = $this->db->query('INSERT INTO controller_check_item (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);

			return $result ? $this->db->get_last_insert_id('controller_check_item', 'id') : 0;
		}

		/**
		 * Update existing check item object in database  
		 * 
		 * @param	$check_item check item oject to be updated
		 * @return  id of the inserted check item, 0 otherwise 
		*/
		function update($check_item)
		{
			$id = $check_item->get_id();

			$values = array(
				'control_item_id = ' . $this->marshal($check_item->get_control_item_id(), 'int'),
				'check_list_id = ' . $this->marshal($check_item->get_check_list_id(), 'int')
			);

			$result = $this->db->query('UPDATE controller_check_item SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			if( $result )
			{
				return $id;
			}
			else
			{
				return 0;
			}
		}

		/**
		 * Get single check item object from database including related control item  
		 * 
		 * @param	$check_item_id id to check item to be fetched from database
		 * @return  check item object 
		*/
		public function get_single($check_item_id)
		{
			$check_item_id = (int) $check_item_id;

			$sql = "SELECT ci.*, ci.id as c_id, coi.id as coi_id, coi.* ";
			$sql .= "FROM controller_check_item ci, controller_control_item coi "; 
			$sql .= "WHERE ci.id = {$check_item_id} ";
			$sql .= "AND ci.control_item_id=coi.id";

			$this->db->query($sql, __LINE__, __FILE__);

			if($this->db->next_record())
			{
				$check_item = new controller_check_item($this->unmarshal($this->db->f('c_id'), 'int'));
				$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id'), 'int'));
				$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id'), 'int'));

				$control_item = new controller_control_item($this->unmarshal($this->db->f('coi_id'), 'int'));
				$control_item->set_title($this->db->f('title', true), 'string');
				$control_item->set_required($this->db->f('required', true), 'string');
				$control_item->set_what_to_do($this->db->f('what_to_do', true), 'string');
				$control_item->set_how_to_do($this->db->f('how_to_do', true), 'string');
				$control_item->set_control_group_id($this->db->f('control_group_id'), 'int');

				$check_item->set_control_item($control_item->toArray());

				return $check_item;
			}
			else
			{
				return null;
			}
		}
		
		/**
		 * Get single check item object from database including related cases and control item 
		 * 
		 * @param	$check_item_id id to check item to be fetched from database
		 * @return  check item object 
		*/
    /*
		public function get_single_with_cases($check_item_id)
		{
			$check_item_id = (int) $check_item_id;

			$sql  = "SELECT ci.id as ci_id, control_item_id, check_list_id, ";
			$sql .= "cic.id as cic_id, cic.status as cic_status, cic.*, ";
			$sql .= "coi.id as coi_id, coi.* ";
			$sql .= "FROM controller_check_item ci "; 
			$sql .= "LEFT JOIN controller_control_item as coi ON ci.control_item_id = coi.id ";
			$sql .= "LEFT JOIN controller_check_item_case as cic ON ci.id = cic.check_item_id ";
			$sql .= "WHERE ci.id = {$check_item_id} ";
											
			$this->db->query($sql);
			
			$counter = 0;
			$check_item = null;
			while ($this->db->next_record())
			{
				if( $counter == 0 )
				{
					$check_item = new controller_check_item($this->unmarshal($this->db->f('ci_id'), 'int'));
					$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id'), 'int'));
					$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id'), 'int'));
					
					$control_item = new controller_control_item($this->unmarshal($this->db->f('coi_id'), 'int'));
					$control_item->set_title($this->db->f('title', true), 'string');
					$control_item->set_required($this->db->f('required', true), 'string');
					$control_item->set_what_to_do($this->db->f('what_to_do', true), 'string');
					$control_item->set_how_to_do($this->db->f('how_to_do', true), 'string');
					$control_item->set_control_group_id($this->db->f('control_group_id'), 'int');
					$control_item->set_type($this->db->f('type', true), 'string');
									
					$check_item->set_control_item($control_item);
						
					$cases_array = array();
				}
				
				if($this->db->f('cic_id'))
				{
					$case = new controller_check_item_case($this->unmarshal($this->db->f('cic_id'), 'int'));
					$case->set_check_item_id($this->unmarshal($this->db->f('check_item_id'), 'int'));
					$case->set_status($this->unmarshal($this->db->f('cic_status'), 'int'));
					$case->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
					$case->set_location_item_id($this->unmarshal($this->db->f('location_item_id'), 'int'));
					$case->set_descr($this->unmarshal($this->db->f('descr', true), 'string'));
					$case->set_user_id($this->unmarshal($this->db->f('user_id'), 'int'));	
					$case->set_entry_date($this->unmarshal($this->db->f('entry_date'), 'int'));
					$case->set_modified_date($this->unmarshal($this->db->f('modified_date'), 'int'));
					$case->set_modified_by($this->unmarshal($this->db->f('modified_by'), 'int'));
				
          $cases_array[] = $case;
				}
				
				$check_item_id =  $check_item->get_id();
				$counter++;
			}
			
			if($check_item != null)
			{
				$check_item->set_cases_array($cases_array);
				return $check_item;
			}
			else
			{
				return null;
			}
		}
     */
		
		/**
		 * Get single check item object from database including related control item
		 * 
		 * @param	$check_list_id check list id
		 * @param	$check_item_id control item id
		 * @return check item object 
		*/
		public function get_check_item_by_check_list_and_control_item($check_list_id, $control_item_id)
		{
			$check_list_id = (int) $check_list_id;
			$control_item_id = (int) $control_item_id;

			$sql  = "SELECT ci.*, ci.id as c_id, coi.id as coi_id, coi.* ";
			$sql .= "FROM controller_check_item ci, controller_control_item coi "; 
			$sql .= "WHERE ci.check_list_id = {$check_list_id} ";
			$sql .= "AND ci.control_item_id = coi.id ";
			$sql .= "AND ci.control_item_id = {$control_item_id}";

			$this->db->query($sql, __LINE__, __FILE__);

			if($this->db->next_record())
			{
				$check_item = new controller_check_item($this->unmarshal($this->db->f('c_id'), 'int'));
				$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id'), 'int'));
				$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id'), 'int'));

				$control_item = new controller_control_item($this->unmarshal($this->db->f('coi_id'), 'int'));
				$control_item->set_title($this->db->f('title', true), 'string');
				$control_item->set_required($this->db->f('required', true), 'string');
				$control_item->set_what_to_do($this->db->f('what_to_do', true), 'string');
				$control_item->set_how_to_do($this->db->f('how_to_do', true), 'string');
				$control_item->set_control_group_id($this->db->f('control_group_id'), 'int');

				$check_item->set_control_item($control_item->toArray());

				return $check_item;
			}
			else
			{
				return null;
			}
		}
				
		/**
		 * Get check item objects from database including control item and related cases 
		 * 
		 * @param	$check_list_id check list id
		 * @param	$type control item registration type COMMENT/TEXTFIELD/CHECKLIST/RADIOBUTTONS 
		 * @param	$status status for cases OPEN/CLOSED/PENDING
		 * @param	$messageStatus is there a message registered for the case
		 * @return check item objects
		*/
		public function get_check_items_with_cases($check_list_id, $type = "control_item_type_1", $status = "open", $messageStatus = null, $location_code = null)
		{
			$check_list_id = (int) $check_list_id;
			$sql  = "SELECT ci.id as ci_id, control_item_id, check_list_id, cic.component_location_id,";
			$sql .= "cic.id as cic_id, cic.status as cic_status, cic.*, ";
			$sql .= "coi.id as coi_id, coi.* ";
		//	$sql .= "FROM controller_check_item ci "; 

			$sql .= "FROM controller_control_group JOIN controller_control_item ON controller_control_item.control_group_id=controller_control_group.id ";
			$sql .= "JOIN controller_check_item ci ON ci.control_item_id = controller_control_item.id "; 

			$sql .= "LEFT JOIN controller_control_item as coi ON ci.control_item_id = coi.id ";
			$sql .= "LEFT JOIN controller_check_item_case as cic ON ci.id = cic.check_item_id ";
			$sql .= "WHERE ci.check_list_id = {$check_list_id} ";
			
			if($status == 'open')
			{
				$sql .= "AND cic.status = 0 ";
			}
			else if($status == 'closed')
			{
				$sql .= "AND cic.status = 1 ";
			}
			else if($status == 'waiting')
			{
				$sql .= "AND cic.status = 2 ";
			}
			else if($status == 'open_or_waiting')
			{
				$sql .= "AND (cic.status = 0 OR cic.status = 2) ";
			}
			
			if($type != null)
			{
				$sql .= "AND coi.type = '$type' ";
			}
										
			if($messageStatus != null & $messageStatus == 'no_message_registered')
			{
				$sql .= "AND cic.location_item_id IS NULL ";
			}
			else if($messageStatus != null &  $messageStatus == 'message_registered')
			{
				$sql .= "AND cic.location_item_id > 0 ";
			}
      
      		if($location_code != null)
			{
				$sql .= "AND cic.location_code = '$location_code' ";
			}
			
			$sql .= "ORDER BY ci.id";
											
			$this->db->query($sql);
			
			$check_item_id = 0;
			$check_item = null;
			$check_items_array=array();

			while ($this->db->next_record())
			{
				if( $this->db->f('ci_id') != $check_item_id )
				{	
					if($check_item_id)
					{
						$check_item->set_cases_array($cases_array);
										
						$check_items_array[] = $check_item;
					}
				
					$check_item = new controller_check_item($this->unmarshal($this->db->f('ci_id'), 'int'));
					$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id'), 'int'));
					$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id'), 'int'));
					
					$control_item = new controller_control_item($this->unmarshal($this->db->f('coi_id'), 'int'));
					$control_item->set_title($this->db->f('title', true), 'string');
					$control_item->set_required($this->db->f('required', true), 'string');
					$control_item->set_what_to_do($this->db->f('what_to_do', true), 'string');
					$control_item->set_how_to_do($this->db->f('how_to_do', true), 'string');
					$control_item->set_control_group_id($this->db->f('control_group_id'), 'int');
					$control_item->set_type($this->db->f('type', true), 'string');
				
					$check_item->set_control_item($control_item);
												
					$cases_array = array();
				}
				
				if( $this->db->f('cic_id') )
				{
					$case = new controller_check_item_case($this->unmarshal($this->db->f('cic_id'), 'int'));
					$case->set_check_item_id($this->unmarshal($this->db->f('check_item_id'), 'int'));
					$case->set_status($this->unmarshal($this->db->f('cic_status'), 'int'));
					$case->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
					$case->set_location_item_id($this->unmarshal($this->db->f('location_item_id'), 'int'));
					$case->set_descr($this->unmarshal($this->db->f('descr', true), 'string'));
					$case->set_user_id($this->unmarshal($this->db->f('user_id'), 'int'));	
					$case->set_entry_date($this->unmarshal($this->db->f('entry_date'), 'int'));
					$case->set_modified_date($this->unmarshal($this->db->f('modified_date'), 'int'));
					$case->set_modified_by($this->unmarshal($this->db->f('modified_by'), 'int'));
					$case->set_measurement($this->unmarshal($this->db->f('measurement', true), 'string'));
					$case->set_component_location_id($this->db->f('component_location_id'), 'int');
					$case->set_component_id($this->unmarshal($this->db->f('component_id'), 'int'));								
					$cases_array[] = $case;
					
				}
				
				$check_item_id = $check_item->get_id();
			}
			
			if($check_item != null)
			{
				$check_item->set_cases_array($cases_array);
							
				$check_items_array[] = $check_item;
			}

			return $check_items_array;
		}
		
		/**
		 * Get check item objects from database including related control item and cases
		 * 
		 * @param	$message_ticket_id get check items and cases for this message
		 * @return check item objects 
		*/
		public function get_check_items_with_cases_by_message($message_ticket_id)
		{
			$message_ticket_id = (int) $message_ticket_id;

			$sql  = "SELECT ci.id as ci_id, control_item_id, cic.component_location_id,"; 
			$sql .= "check_list_id, cic.id as cic_id, cic.status as cic_status, cic.*, ";
			$sql .= "coi.id as coi_id, coi.* ";
			$sql .= "FROM controller_control_group JOIN controller_control_item ON controller_control_item.control_group_id=controller_control_group.id ";
			$sql .= "JOIN controller_check_item ci ON ci.control_item_id = controller_control_item.id "; 
			$sql .= "LEFT JOIN controller_control_item as coi ON ci.control_item_id = coi.id ";
			$sql .= "LEFT JOIN controller_check_item_case as cic ON ci.id = cic.check_item_id ";
			$sql .= "WHERE cic.location_item_id = {$message_ticket_id}";
											
			$this->db->query($sql);
			
			$check_item_id = 0;
			$check_item = null;
			while ($this->db->next_record())
			{
				if( $this->db->f('ci_id') != $check_item_id )
				{
					if($check_item_id)
					{
						$check_item->set_cases_array($cases_array);
						
						$check_items_array[] = $check_item;
					}
				
					$check_item = new controller_check_item($this->unmarshal($this->db->f('ci_id'), 'int'));
					$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id'), 'int'));
					$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id'), 'int'));
					
					$control_item = new controller_control_item($this->unmarshal($this->db->f('coi_id'), 'int'));
					$control_item->set_title($this->db->f('title', true), 'string');
					$control_item->set_required($this->db->f('required', true), 'string');
					$control_item->set_what_to_do($this->db->f('what_to_do', true), 'string');
					$control_item->set_how_to_do($this->db->f('how_to_do', true), 'string');
					$control_item->set_control_group_id($this->db->f('control_group_id'), 'int');
					$control_item->set_component_location_id($this->db->f('component_location_id'), 'int');
					$control_item->set_type($this->db->f('type', true), 'string');
									
					$check_item->set_control_item($control_item);
														
					$cases_array = array();
				}
				
				if($this->db->f('cic_id'))
				{
					$case = new controller_check_item_case($this->unmarshal($this->db->f('cic_id'), 'int'));
					$case->set_status($this->unmarshal($this->db->f('cic_status'), 'int'));
					$case->set_check_item_id($this->unmarshal($this->db->f('check_item_id'), 'int'));
					$case->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
					$case->set_location_item_id($this->unmarshal($this->db->f('location_item_id'), 'int'));
					$case->set_descr($this->unmarshal($this->db->f('descr', true), 'string'));
					$case->set_user_id($this->unmarshal($this->db->f('user_id'), 'int'));	
					$case->set_entry_date($this->unmarshal($this->db->f('entry_date'), 'int'));
					$case->set_modified_date($this->unmarshal($this->db->f('modified_date'), 'int'));
					$case->set_modified_by($this->unmarshal($this->db->f('modified_by'), 'int'));
				
					$cases_array[] = $case;
				}
				
				$check_item_id =  $check_item->get_id();
			}
			
			if($check_item != null)
			{
				$check_item->set_cases_array($cases_array);
				$check_items_array[] = $check_item;
				
				return $check_items_array;
			}
			else
			{
				return null;
			}
		}
		 /* Later ikke til at vi bruker denne: Torstein 10.07.12
		public function get_check_items_by_message($location_id, $location_item_id, $return_type = "return_array" )
		{
			$location_id		= (int)$location_id;
			$location_item_id	= (int)$location_item_id;
			$sql  = "SELECT ci.* "; 
			$sql .= "FROM controller_check_item ci "; 
			$sql .= "LEFT JOIN controller_check_item_case as cic ON ci.id = cic.check_item_id ";
			$sql .= "WHERE cic.location_id = {$location_id} AND cic.location_item_id = {$location_item_id} ";
								
			$this->db->query($sql);
			
			while ($this->db->next_record()) {
				$check_item = new controller_check_item($this->unmarshal($this->db->f('id', true), 'int'));
				$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));
				$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id', true), 'int'));
				
				if($return_type == "return_array")
					$check_items_array[] = $check_item->toArray();
				else
					$check_items_array[] = $check_item;
			}
			
			return $check_items_array;
		}
		*/
		
		function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count){}

		function populate(int $object_id, &$object){}

		function get_id_field_name(){}
	}
