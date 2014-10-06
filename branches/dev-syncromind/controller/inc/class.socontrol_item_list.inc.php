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

	include_class('controller', 'control_item_list', 'inc/model/');
	include_class('controller', 'control_item_option', 'inc/model/');

	class controller_socontrol_item_list extends controller_socommon
	{
		protected static $so;

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return controller_socontrol_item_list the storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('controller.socontrol_item_list');
			}
			return self::$so;
		}

		/**
		 * Inserts a new control item list to database
		 *
		 * @param $control_item_list control item list object to be inserted
		 * @return id of inserted control item list if successful, 0 if not successful
		 */
		function add(&$control_item_list)
		{
			$cols = array(
					'control_id',
					'control_item_id',
			);

			$values = array(
				$this->marshal($control_item_list->get_control_id(), 'int'),
				$this->marshal($control_item_list->get_control_item_id(), 'int')
			);

			$result = $this->db->query( 'INSERT INTO controller_control_item_list (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);

			if($result)
			{
				// return the new control item ID
				return $this->db->get_last_insert_id('controller_control_item_list', 'id');
			}
			else
			{
				return 0;
			}
		}

		/**
		 * Updates an existing control item list in database
		 *
		 * @param $control_item_list control item list object to be updated
		 * @return id of inserted control item list if successful, 0 if not successful
		 */
		function update($control_item_list)
		{
			$id = intval($control_item_list->get_id());

			$values = array(
				'control_id = ' . $this->marshal($control_item_list->get_control_id(), 'int'),
				'control_item_id = '. $this->marshal($control_item_list->get_control_item_id(), 'int'),
				'order_nr = ' . $this->marshal($control_item_list->get_order_nr(), 'int')
			);

			$result = $this->db->query('UPDATE controller_control_item_list SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			return $result;
		}

		/**
		 * Get single control_item_list object
		 * 
		 * @param	$id	id of the control_item_list to return
		 * @return  control item list object
		 */
		function get_single($id)
		{
			$id = (int)$id;

			$sql = "SELECT p.* FROM controller_control_item_list p WHERE p.id = " . $id;
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();

			$control_item_list = new controller_control_item_list($this->unmarshal($this->db->f('id'), 'int'));
			$control_item_list->set_control_id($this->unmarshal($this->db->f('control_id'), 'int'));
			$control_item_list->set_control_item_id($this->unmarshal($this->db->f('control_item_id'), 'int'));
			$control_item_list->set_order_nr($this->unmarshal($this->db->f('order_nr'), 'int'));

			return $control_item_list;
		}

		/**
		 * Get single control_item_list object
		 * 
		 * @param	$control_id	control id
		 * @param	$control_item_id	control id
		 * @return  control item list object
		 */
		function get_single_2($control_id, $control_item_id)
		{
			$control_id = (int) $control_id;
			$control_item_id = (int) $control_item_id;

			$sql = "SELECT cil.* FROM controller_control_item_list cil WHERE cil.control_id = " . $control_id . " AND cil.control_item_id = " . $control_item_id;
			$this->db->query($sql, __LINE__, __FILE__);
			$result = $this->db->next_record();

			if( $result )
			{
				$control_item_list = new controller_control_item_list($this->unmarshal($this->db->f('id'), 'int'));
				$control_item_list->set_control_id($this->unmarshal($this->db->f('control_id'), 'int'));
				$control_item_list->set_control_item_id($this->unmarshal($this->db->f('control_item_id'), 'int'));
				$control_item_list->set_order_nr($this->unmarshal($this->db->f('order_nr'), 'int'));
			
				return $control_item_list;
			}
			else
			{
				return null;	
			}
		}
		
		/**
		 * Get control item objects from database as objects or as arrays 
		 * 
		 * @param	$control_group_id	control group id
		 * @return  array with control items
		*/
		function get_control_items($control_group_id)
		{
			$control_group_id = (int) $control_group_id;

			$results = array();

			$sql  = "SELECT * ";
			$sql .= "FROM controller_control_item ";
			$sql .= "WHERE control_group_id={$control_group_id}";
			
			$this->db->query($sql);

			while ($this->db->next_record())
			{
				$control_item = new controller_control_item($this->unmarshal($this->db->f('id'), 'int'));
				$control_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$control_item->set_required($this->unmarshal($this->db->f('required'), 'boolean'));
				$control_item->set_what_to_do($this->unmarshal($this->db->f('what_to_do', true), 'string'));
				$control_item->set_how_to_do($this->unmarshal($this->db->f('how_to_do', true), 'string'));
				$control_item->set_control_group_id($this->unmarshal($this->db->f('control_group_id'), 'int'));

				$results[] = $control_item;
			}

			return $results;
		}

		/**
		 * Get control item objects from database as objects or as arrays 
		 * 
		 * @param	$control_id	control id
		 * @param $return_type return data as objects or as arrays
		 * @return  array with control items
		*/
		function get_control_items_by_control($control_id, $returnType = "return_object")
		{
			$control_id = (int) $control_id;

			$results = array();

			$sql  = "SELECT ci.* ";
			$sql .= "FROM controller_control_item ci, controller_control_item_list cl ";
			$sql .= "WHERE cl.control_id=$control_id ";
			$sql .= "AND cl.control_item_id = ci.id ";
									
			$this->db->query($sql);

			while ($this->db->next_record())
			{
				$control_item = new controller_control_item($this->unmarshal($this->db->f('id'), 'int'));
				$control_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$control_item->set_required($this->unmarshal($this->db->f('required'), 'boolean'));
				$control_item->set_what_to_do($this->unmarshal($this->db->f('what_to_do', true), 'string'));
				$control_item->set_how_to_do($this->unmarshal($this->db->f('how_to_do', true), 'string'));
				$control_item->set_control_group_id($this->unmarshal($this->db->f('control_group_id'), 'int'));
				$control_item->set_type($this->unmarshal($this->db->f('type', true), 'string'));

				if($returnType == "return_array")
				{
					$results[] = $control_item->toArray();
				}
				else
				{
					$results[] = $control_item;
				}
			}
			
			return $results;
		}

		/**
		 * Get control item objects from database as objects or as arrays 
		 * 
		 * @param	$control_id	control id
		 * @param	$control_group_id	control group id
		 * @param $return_type return data as objects or as arrays
		 * @return  array with control items
		*/
		function get_control_items_by_control_and_group($control_id, $control_group_id, $returnType = "return_array")
		{
			$control_id = (int) $control_id;
			$control_group_id = (int) $control_group_id;

			$results = array();

			$sql  =	"SELECT ci.* ";
			$sql .= "FROM controller_control_item ci, controller_control_item_list cl, controller_control c ";
			$sql .= "WHERE c.id=$control_id ";
			$sql .= "AND c.id=cl.control_id "; 
			$sql .= "AND cl.control_item_id=ci.id ";
			$sql .= "AND ci.control_group_id=$control_group_id ";
			$sql .= "ORDER BY cl.order_nr";
			
			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$control_item = new controller_control_item($this->unmarshal($this->db->f('id'), 'int'));
				$control_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$control_item->set_required($this->unmarshal($this->db->f('required'), 'boolean'));
				$control_item->set_what_to_do($this->unmarshal($this->db->f('what_to_do', true), 'string'));
				$control_item->set_how_to_do($this->unmarshal($this->db->f('how_to_do', true), 'string'));
				$control_item->set_control_group_id($this->unmarshal($this->db->f('control_group_id'), 'int'));
				$control_item->set_type($this->unmarshal($this->db->f('type', true), 'string'));

				if($returnType == "return_array")
				{
					$results[] = $control_item->toArray();
				}
				else
				{
					$results[] = $control_item;
				}
			}

			return $results;
		}

		/**
		 * Get control item objects with control item options from database as objects or as arrays 
		 * 
		 * @param	$control_id	control id
		 * @param	$control_group_id	control group id
		 * @param $return_type return data as objects or as arrays
		 * @return array with control items
		*/
		function get_control_items_and_options_by_control_and_group($control_id, $control_group_id, $return_type = "return_array")
		{
			$control_id = (int) $control_id;
			$control_group_id = (int) $control_group_id;

			$results = array();

			$sql  =	"SELECT ci.id as ci_id, ci.*, cio.id as cio_id, cio.* ";
			$sql .= "FROM controller_control_item ci ";
			$sql .= "LEFT JOIN controller_control_item_list cl ON cl.control_item_id = ci.id ";
			$sql .= "LEFT JOIN controller_control c ON c.id = cl.control_id ";
			$sql .= "LEFT JOIN controller_control_item_option cio ON ci.id = cio.control_item_id ";
			$sql .= "WHERE c.id=$control_id ";
			$sql .= "AND ci.control_group_id=$control_group_id ";
			$sql .= "ORDER BY cl.order_nr";
			
			$this->db->query($sql, __LINE__, __FILE__);
			
			$control_item_id = 0;
			$control_item = null;
			$control_item_array = array();
			while ($this->db->next_record())
			{
				if( $this->db->f('ci_id') != $control_item_id )
				{
					if($control_item_id)
					{
						$control_item->set_options_array($options_array);
						
						if($return_type == "return_array")
						{
							$control_item_array[] = $control_item->toArray();
						}
						else
						{
							$control_item_array[] = $control_item;
						}
					}
						
					$control_item = new controller_control_item($this->unmarshal($this->db->f('ci_id'), 'int'));
					$control_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
					$control_item->set_required($this->unmarshal($this->db->f('required'), 'boolean'));
					$control_item->set_what_to_do($this->unmarshal($this->db->f('what_to_do', true), 'string'));
					$control_item->set_how_to_do($this->unmarshal($this->db->f('how_to_do', true), 'string'));
					$control_item->set_control_group_id($this->unmarshal($this->db->f('control_group_id'), 'int'));
					$control_item->set_type($this->unmarshal($this->db->f('type', true), 'string'));

					$options_array = array();
				}
				
				$control_item_option = new controller_control_item_option($this->db->f('option_value', true), $this->db->f('control_item_id'));
				$control_item_option->set_id($this->db->f('cio_id'));
				
				if($return_type == "return_array")
				{
					$options_array[] = $control_item_option->toArray();
				}
				else
				{
					$options_array[] = $control_item_option;
				}

				$control_item_id = $control_item->get_id();
			}
			
			if($control_item != null)
			{
				$control_item->set_options_array($options_array);

				if($return_type == "return_array")
				{
					$control_item_array[] = $control_item->toArray();
				}
				else
				{
					$control_item_array[] = $control_item;
				}
				
				return $control_item_array;
			}
			else
			{
				return null;
			}
		}
		
		/**
		 * Delete a control item list from database 
		 * 
		 * @param	$control_id	control id
		 * @param	$control_group_id	control group id
		 * @return true if successful, false otherwise
		*/
		function delete($control_id, $control_item_id)
		{
			$control_id = (int) $control_id;
			$control_item_id = (int) $control_item_id;

			$result = $this->db->query("DELETE FROM controller_control_item_list WHERE control_id = $control_id AND control_item_id = $control_item_id", __LINE__,__FILE__);

			return $result;
		}
		
		/**
		 * Deletes all control items related to a control 
		 * 
		 * @param	$control_id	control id
		 * @return true if successful, false otherwise
		*/
		function delete_control_items($control_id)
		{
			$control_id = (int) $control_id;
			$result = $this->db->query("DELETE FROM controller_control_item_list WHERE control_id = $control_id");

			return $result;
		}

		/**
		 * Deletes all control items within a group related to a control 
		 * 
		 * @param	$control_id	control id
		 * @param	$control_group_id	control group id
		 * @return true if successful, false otherwise
		*/
		function delete_control_items_for_group_list($control_id, $control_group_id)
		{
 			$control_id = (int) $control_id;
			$control_group_id = (int) $control_group_id;

  			$sql  = "DELETE FROM controller_control_item_list "; 
  			$sql .= "USING controller_control_item ";
  			$sql .= "WHERE control_id = $control_id ";
  			$sql .= "AND control_item_id = controller_control_item.id ";
  			$sql .= "AND controller_control_item.control_group_id = $control_group_id";
			
			$result = $this->db->query($sql);
			
			return $result;
		}

		function get_id_field_name($extended_info = false){}

		protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count){}

		function populate(int $control_item_id, &$control_item){}

	}
