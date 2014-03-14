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
 	* @version $Id: class.socase.inc.php 11501 2013-12-01 14:27:58Z sigurdne $
	*/

	phpgw::import_class('controller.socommon');

	include_class('controller', 'check_item_case', 'inc/model/');

	class controller_socase extends controller_socommon
	{
		protected static $so;
		protected $global_lock = false;

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return controller_socase the storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('controller.socase');
			}
			return self::$so;
		}

		function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count){}

		function populate(int $object_id, &$object){}

		/**
		 * Get single case object from database  
		 * 
		 * @param	$case id id of case to be returned
		 * @return case object
		*/
		public function get_single($case_id)
		{
			$case_id = (int) $case_id;
			
			$sql = "SELECT * FROM controller_check_item_case "; 
			$sql .= "WHERE id = {$case_id}";
			

			$this->db->query($sql, __LINE__, __FILE__);

			if($this->db->next_record())
			{
				$case = new controller_check_item_case($this->unmarshal($this->db->f('id'), 'int'));
				$case->set_check_item_id($this->unmarshal($this->db->f('check_item_id'), 'int'));
				$case->set_status($this->unmarshal($this->db->f('status'), 'int'));
				$case->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
				$case->set_location_item_id($this->unmarshal($this->db->f('location_item_id'), 'int'));
				$case->set_descr($this->unmarshal($this->db->f('descr', true), 'string'));
				$case->set_user_id($this->unmarshal($this->db->f('user_id'), 'int'));	
				$case->set_entry_date($this->unmarshal($this->db->f('entry_date'), 'int'));
				$case->set_modified_date($this->unmarshal($this->db->f('modified_date'), 'int'));
				$case->set_modified_by($this->unmarshal($this->db->f('modified_by'), 'int'));
				$case->set_measurement($this->unmarshal($this->db->f('measurement'), 'string'));
				$case->set_location_code($this->unmarshal($this->db->f('location_code'), 'string'));
				$case->set_component_location_id($this->unmarshal($this->db->f('component_location_id'), 'int'));
				$case->set_component_id($this->unmarshal($this->db->f('component_id'), 'int'));
					
				return $case;
			}
			else
			{
				return null;
			}
		}
		
		/**
		 * Get cases for message  
		 * 
		 * @param	$location_id location id
		 * @param	$location_item_id location item id
		 * @return array of case object represented as objects or arrays
		*/
		public function get_cases_by_message($location_id, $location_item_id)
		{
			$location_id		= (int) $location_id;
			$location_item_id	= (int) $location_item_id;

			$sql = "SELECT * FROM controller_check_item_case "; 
			$sql .= "WHERE location_id = {$location_id} AND location_item_id = {$location_item_id}";

			$this->db->query($sql);

			$cases_array = array();

			while ($this->db->next_record())
			{
				$case = new controller_check_item_case($this->unmarshal($this->db->f('id'), 'int'));
				$case->set_check_item_id($this->unmarshal($this->db->f('check_item_id'), 'int'));
				$case->set_status($this->unmarshal($this->db->f('status'), 'int'));
				$case->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
				$case->set_location_item_id($this->unmarshal($this->db->f('location_item_id'), 'int'));
				$case->set_descr($this->unmarshal($this->db->f('descr', true), 'string'));
				$case->set_user_id($this->unmarshal($this->db->f('user_id'), 'int'));	
				$case->set_entry_date($this->unmarshal($this->db->f('entry_date'), 'int'));
				$case->set_modified_date($this->unmarshal($this->db->f('modified_date'), 'int'));
				$case->set_modified_by($this->unmarshal($this->db->f('modified_by'), 'int'));
				$case->set_measurement($this->unmarshal($this->db->f('measurement'), 'string'));
				$case->set_location_code($this->unmarshal($this->db->f('location_code'), 'string'));
				$case->set_component_location_id($this->unmarshal($this->db->f('component_location_id'), 'int'));
				$case->set_component_id($this->unmarshal($this->db->f('component_id'), 'int'));				

				$cases_array[] = $case;
			}

			return $cases_array;
		}


		/**
		 * Get cases for component  
		 * 
		 * @param	$location_id location id
		 * @param	$location_item_id location item id
		 * @return array of case object represented as objects or arrays
		*/
		public function get_cases_by_component($component_location_id, $component_id, $control_item_id = 0, $check_list_id = 0)
		{
			$component_location_id	= (int) $component_location_id;
			$component_id			= (int) $component_id;
			$control_item_id		= (int) $control_item_id;
			$check_list_id			= (int) $check_list_id;

			$sql = "SELECT controller_check_item_case.*, check_list_id FROM controller_check_item_case " 
			. " {$this->join} controller_check_item ON controller_check_item_case.check_item_id = controller_check_item.id"
			. " WHERE controller_check_item_case.component_location_id = {$component_location_id} AND controller_check_item_case.component_id = {$component_id}";

			if($control_item_id)
			{
				$sql .= " AND controller_check_item.control_item_id = {$control_item_id}";
			}

			if($check_list_id)
			{
				$sql .= " AND check_list_id = {$check_list_id}";
			}

			$this->db->query($sql);

			$values = array();
			while ($this->db->next_record())
			{
				$values[] = array
				(
					'id'			=> $this->db->f('id'),
					'check_list_id'	=> $this->db->f('check_list_id'),
					'descr'			=> $this->db->f('descr', true),
					'user_id'		=> $this->db->f('user_id'),
					'status'		=> $this->db->f('status'),
					'modified_date'	=> $this->db->f('modified_date')
				);
			}

			return $values;
		}


		
		/**
		 * Inserts a new case in database  
		 * 
		 * @param	$case to inserted
		 * @return true or false
		*/
		function add(&$case)
		{
			$this->db->transaction_begin();

			$cols = array(
					'check_item_id',
					'status',
					'location_id',
					'location_item_id',
					'descr',
					'user_id',
					'entry_date',
					'modified_date',
					'modified_by',
					'measurement',
					'location_code',
					'component_location_id',
					'component_id'
			);

			$values = array(
				$this->marshal($case->get_check_item_id(), 'int'),
				$case->get_status(),
				$this->marshal($case->get_location_id(), 'int'),
				$this->marshal($case->get_location_item_id(), 'int'),
				$this->marshal($case->get_descr(), 'string'),
				$this->marshal($case->get_user_id(), 'int'),
				$this->marshal($case->get_entry_date(), 'int'),
				$this->marshal($case->get_modified_date(), 'int'),
				$this->marshal($case->get_modified_by(), 'int'),
				$this->marshal($case->get_measurement(), 'string'),
				$this->marshal($case->get_location_code(), 'string'),
				$this->marshal($case->get_component_location_id(), 'int'),				
				$this->marshal($case->get_component_id(), 'int'),
			);

			$sql = 'INSERT INTO controller_check_item_case (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')';
			$this->db->query( $sql, __LINE__,__FILE__);
			$case_id = $this->db->get_last_insert_id('controller_check_item_case', 'id');
			
//--------

			$this->update_cases_on_check_list($case->get_check_item_id());
			

//------

			if($this->db->transaction_commit())
			{
				return $case_id;
			}
			else
			{
				return 0;
			}
		}


		function update_cases_on_check_list($check_item_id = 0)
		{
			$check_item_id = (int) $check_item_id;
			$sql = "SELECT check_list_id  FROM controller_check_item WHERE id = {$check_item_id}";

			$this->db->query($sql, __LINE__,__FILE__);
			$this->db->next_record();
			$check_list_id = (int) $this->db->f('check_list_id');
		
			$so_check_item = CreateObject('controller.socheck_item');
			$check_items = $so_check_item->get_check_items_with_cases($check_list_id, $control_item_type = null, $status = null, $messageStatus = null);
	
			$num_open_cases = 0;
			$num_pending_cases = 0;
					
			foreach($check_items as $check_item)
			{
				foreach($check_item->get_cases_array() as $case)
				{
					
					if($case->get_status() == controller_check_item_case::STATUS_OPEN)
					{
						$num_open_cases++;
					}
					
					if($case->get_status() == controller_check_item_case::STATUS_PENDING)
					{
						$num_pending_cases++;
					}
				}	
			}
			
			$values = array
			(
				'num_open_cases' => $num_open_cases,
				'num_pending_cases' => $num_pending_cases,
			);

			if($num_open_cases > 0)
			{
				$values['status'] = controller_check_list::STATUS_DONE;
			}

			$value_set	= $this->db->validate_update($values);
			return $this->db->query("UPDATE controller_check_list SET {$value_set} WHERE id = '{$check_list_id}'",__LINE__,__FILE__);
		}

		/**
		 * Updates an existing case object in database  
		 * 
		 * @param	$case to updated
		 * @return true or false
		*/
		function update($case)
		{
			if ( $this->db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$id = (int) $case->get_id();
			
			$values = array(
				'check_item_id = ' . $this->marshal($case->get_check_item_id(), 'int'),
				'status = ' . $case->get_status(),
				'location_id = ' . $this->marshal($case->get_location_id(), 'int'),
				'location_item_id = ' . $this->marshal($case->get_location_item_id(), 'int'),
				'descr = ' . $this->marshal($case->get_descr(), 'string'),
				'user_id = ' . $this->marshal($case->get_user_id(), 'int'),
				'entry_date = ' . $this->marshal($case->get_entry_date(), 'int'),
				'modified_date = ' . $this->marshal($case->get_modified_date(), 'int'),
				'modified_by = ' . $this->marshal($case->get_modified_by(), 'int'),
				'measurement = ' . $this->marshal($case->get_measurement(), 'string'),
				'location_code = ' . $this->marshal($case->get_location_code(), 'string')
			);

			$result = $this->db->query('UPDATE controller_check_item_case SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			$ok = $this->update_cases_on_check_list($case->get_check_item_id());

			if ( !$this->global_lock )
			{
				$this->db->transaction_commit();
			}

			if($ok)
			{
				return $id;
			}
			else
			{
				return 0;
			}
		}
		
		/**
		 * Delete case in database  
		 * 
		 * @param	$case_id case to be deleted
		 * @return true or false
		*/
		function delete($case_id)
		{
			$case_id = (int) $case_id;
			return $this->db->query("DELETE FROM controller_check_item_case WHERE id = $case_id");
		}
		
		function get_id_field_name(){}
	}
