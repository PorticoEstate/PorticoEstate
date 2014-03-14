<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
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
 	* @version $Id: class.socontrol_item_option.inc.php 11194 2013-06-24 04:38:09Z sigurdne $
	*/	

	phpgw::import_class('controller.socommon');

	include_class('controller', 'control_item_option', 'inc/model/');

	class controller_socontrol_item_option extends controller_socommon
	{
		protected static $so;

		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('controller.socontrol_item_option');
			}
			return self::$so;
		}

		/**
		 * Inserts a new control item option in database  
		 * 
		 * @param	$control_item_option object to be inserted
		 * @return true if task was successful, false otherwise  
		*/
		function add(&$control_item_option)
		{
			$cols = array(
					'option_value',
					'control_item_id'
			);

			$values = array(
				$this->marshal($control_item_option->get_option_value(), 'string'),
				$this->marshal($control_item_option->get_control_item_id(), 'int')
			);

			$result = $this->db->query('INSERT INTO controller_control_item_option (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);

			if($result)
			{
				// return the new control item ID
				return $this->db->get_last_insert_id('controller_control_item_option', 'id');
			}
			else
			{
				return 0;
			}
		}
		
		/**
		 * Updates an existing control item option in database  
		 * 
		 * @param	$control_item_option object to be updated
		 * @return true if task was successful, false otherwise  
		*/
		function update($control_item_option)
		{
			$id = intval($control_item_option->get_id());

			$values = array(
				'option_value = ' . $this->marshal($control_item_option->get_option_value(), 'string'),
				'control_item_id = ' . $this->marshal($control_item->get_control_item_id(), 'int')
			);

			$result = $this->db->query('UPDATE controller_control_item_option SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			return $result;
		}
		
		/**
		 * Get a single control item option from database  
		 * 
		 * @param	$id id of control item option to be fetched
		 * @return control item option object  
		*/
		function get_single($id)
		{
			$id = (int)$id;
			$sql = "SELECT p.* FROM controller_control_item_option p {$joins} WHERE p.id = " . $id;
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();

			$control_item_option = new controller_control_item_option($this->unmarshal($this->db->f('id'), 'int'));
			$control_item_option->set_option_value($this->unmarshal($this->db->f('option_value', true), 'string'));
			$control_item_option->set_control_item_id($this->unmarshal($this->db->f('control_item_id'), 'int'));
						
			return $control_item_option;
		}
		
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('controller.socontrol_item_option');
			}
			
			return self::$so;
		}
		
		function get_id_field_name(){}
		function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count){}
		function populate(int $object_id, &$object){}
	}
