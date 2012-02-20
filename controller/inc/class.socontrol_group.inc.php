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

	include_class('controller', 'control_group', 'inc/model/');

	class controller_socontrol_group extends controller_socommon
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
				self::$so = CreateObject('controller.socontrol_group');
			}
			return self::$so;
		}

		/**
		 * Function for adding a new control group to the database.
		 *
		 * @param controller_control_group $control_group the control group to be added
		 * @return int id of the new control group object
		 */
		function add(&$control_group)
		{
			$cols = array(
					'group_name',
					'procedure_id',
					'control_area_id',
					'building_part_id'
			);

			$values = array(
				$this->marshal($control_group->get_group_name(), 'string'),
				$this->marshal($control_group->get_procedure_id(), 'int'),
				$this->marshal($control_group->get_control_area_id(), 'int'),
				$this->marshal($control_group->get_building_part_id(), 'string'),
			);

			$result = $this->db->query('INSERT INTO controller_control_group (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);

			if(isset($result))
			{
				// Get the new control group ID and return it
				return $this->db->get_last_insert_id('controller_control_group', 'id');
			}
			else
			{
				return 0;
			}

		}

		/**
		 * Update the database values for an existing activity object.
		 *
		 * @param $activity the activity to be updated
		 * @return boolean true if successful, false otherwise
		 */

		function update($control_group)
		{
			$id = intval($control_group->get_id());

			$values = array(
				'group_name = ' . $this->marshal($control_group->get_group_name(), 'string'),
				'procedure_id = '. $this->marshal($control_group->get_procedure_id(), 'int'),
				'control_area_id = ' . $this->marshal($control_group->get_control_area_id(), 'int'),
				'building_part_id = ' . $this->marshal($control_group->get_building_part_id(), 'string')
			);

			//var_dump('UPDATE activity_activity SET ' . join(',', $values) . " WHERE id=$id");
			$result = $this->db->query('UPDATE controller_control_group SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			return isset($result);
		}

		/**
		 * Get single procedure
		 * 
		 * @param	$id	id of the procedure to return
		 * @return a controller_procedure
		 */
		function get_single($id)
		{
			$id = (int)$id;

			$joins = "	{$this->left_join} fm_building_part ON (p.building_part_id = fm_building_part.id)";
			$joins .= "	{$this->left_join} controller_procedure ON (p.procedure_id = controller_procedure.id)";
			//$joins .= "	{$this->left_join} controller_control_area ON (p.control_area_id = controller_control_area.id)";

			$sql = "SELECT p.*, fm_building_part.descr AS building_part_descr, controller_procedure.title as procedure_title FROM controller_control_group p {$joins} WHERE p.id = " . $id;
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			$this->db->next_record();

			$control_group = new controller_control_group($this->unmarshal($this->db->f('id', true), 'int'));
			$control_group->set_group_name($this->unmarshal($this->db->f('group_name', true), 'string'));
			$control_group->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
			$control_group->set_procedure_name($this->unmarshal($this->db->f('procedure_title'), 'string'));
			$control_group->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
			$category = execMethod('phpgwapi.categories.return_single', $this->unmarshal($this->db->f('control_area_id', 'int')));
			$control_group->set_control_area_name($category[0]['name']);
			//$control_group->set_control_area_name($this->unmarshal($this->db->f('control_area_name'), 'string'));
			$control_group->set_building_part_id($this->unmarshal($this->db->f('building_part_id'), 'string'));
			$control_group->set_building_part_descr($this->unmarshal($this->db->f('building_part_descr'), 'string'));

			return $control_group;
		}

		/**
		 * Get a list of procedure objects matching the specific filters
		 * 
		 * @param $start search result offset
		 * @param $results number of results to return
		 * @param $sort field to sort by
		 * @param $query LIKE-based query string
		 * @param $filters array of custom filters
		 * @return list of rental_composite objects
		 */
		function get_control_group_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
		{
			$results = array();

			//$condition = $this->get_conditions($query, $filters,$search_option);
			$order = $sort ? "ORDER BY $sort $dir ": '';

			//$sql = "SELECT * FROM controller_procedure WHERE $condition $order";
			$sql = "SELECT * FROM controller_control_group $order";
			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$control_group = new controller_control_group($this->unmarshal($this->db->f('id', true), 'int'));
				$control_group->set_group_name($this->unmarshal($this->db->f('group_name', true), 'string'));

				$results[] = $control_group;
			}

			return $results;
		}

		function get_control_group_select_array()
		{
				$results = array();
				$results[] = array('id' =>  0,'name' => lang('Not selected'));
				$this->db->query("SELECT id, group_name as name FROM controller_control_group ORDER BY name ASC", __LINE__, __FILE__);
				while ($this->db->next_record())
				{
					$results[] = array('id' => $this->db->f('id', false),
									   'name' => $this->db->f('name', false));
				}
				return $results;
		}

		function get_building_part_select_array($selected_building_part_id)
		{
				$results = array();
				$results[] = array('id' =>  0,'name' => lang('Not selected'));
				$this->db->query("SELECT id, descr as name FROM fm_building_part ORDER BY id ASC", __LINE__, __FILE__);
				while ($this->db->next_record())
				{
					$curr_id = $this->db->f('id', false);
					if($selected_building_part_id && $selected_building_part_id > 0 && $selected_building_part_id == $curr_id)
					{
						$results[] = array('id' => $this->db->f('id', false),
										   'name' => $this->db->f('name', false),
										   'selected' => 'yes');
					}
					else
					{
						$results[] = array('id' => $this->db->f('id', false),
										   'name' => $this->db->f('name', false));
					}
				}
				return $results;
		}

		function get_control_groups_as_objects($control_area_id, $limit)
		{
			$results = array();

			$sql = "SELECT * FROM controller_control_group WHERE control_area_id=$control_area_id";
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);

			while ($this->db->next_record()) {
				$control_group = new controller_control_group($this->unmarshal($this->db->f('id', true), 'int'));
				$control_group->set_group_name($this->unmarshal($this->db->f('group_name', true), 'string'));
				$control_group->set_procedure_id($this->unmarshal($this->db->f('procedure_id', true), 'string'));
				$control_group->set_control_area_id($this->unmarshal($this->db->f('control_area_id', true), 'string'));

				$results[] = $control_group;
			}

			return $results;
		}

		function get_control_groups_as_array($control_area_id)
		{
			$results = array();

			$sql = "SELECT * FROM controller_control_group WHERE control_area_id=$control_area_id";
			$this->db->query($sql);

			while ($this->db->next_record()) {
				$control_group = new controller_control_group($this->unmarshal($this->db->f('id', true), 'int'));
				$control_group->set_group_name($this->unmarshal($this->db->f('group_name', true), 'string'));
				$control_group->set_procedure_id($this->unmarshal($this->db->f('procedure_id', true), 'string'));
				$control_group->set_control_area_id($this->unmarshal($this->db->f('control_area_id', true), 'string'));

				$results[] = $control_group->toArray();
			}

			return $results;
		}

		function get_id_field_name($extended_info = false)
		{

			if(!$extended_info)
			{
				$ret = 'id';
			}
			else
			{
				$ret = array
				(
					'table'			=> 'controller_control_group', // alias
					'field'			=> 'id',
					'translated'	=> 'id'
				);
			}

			return $ret;
		}

		protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
		{
			$clauses = array('1=1');
			if($search_for)
			{
				$like_pattern = "'%" . $this->db->db_addslashes($search_for) . "%'";
				$like_clauses = array();
				switch($search_type){
					default:
						$like_clauses[] = "controller_control_group.group_name $this->like $like_pattern";
						break;
				}
				if(count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
			}

			$filter_clauses = array();

			if(isset($filters[$this->get_id_field_name()]))
			{
				$filter_clauses[] = "controller_control_group.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
			}
			if(isset($filters['control_areas']))
			{
				$filter_clauses[] = "controller_control_group.control_area_id = {$this->marshal($filters['control_areas'],'int')}";
			}

			if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			$condition =  join(' AND ', $clauses);

			$tables = "controller_control_group";
			$joins = "	{$this->left_join} fm_building_part ON (building_part_id = fm_building_part.id)";
			$joins .= "	{$this->left_join} controller_procedure ON (controller_control_group.procedure_id = controller_procedure.id)";
//			$joins .= "	{$this->left_join} controller_control_area ON (controller_control_group.control_area_id = controller_control_area.id)";
			//$joins .= "	{$this->left_join} rental_contract_composite ON (rental_contract_composite.composite_id = rental_composite.id)";
			//$joins .= "	{$this->left_join} rental_contract ON (rental_contract.id = rental_contract_composite.contract_id)";

			if($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(controller_control_group.id)) AS count';
			}
			else
			{
				$cols .= "controller_control_group.id, group_name, controller_control_group.procedure_id, controller_control_group.control_area_id as control_area_id, ";
				$cols .= "building_part_id, fm_building_part.descr AS building_part_descr, controller_procedure.title as procedure_title "; 
				//$cols .= "controller_control_area.title as control_area_name ";
			}
			$dir = $ascending ? 'ASC' : 'DESC';
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';

			//var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");

			return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		}

		function populate(int $control_group_id, &$control_group)
		{
			if($control_group == null) {
				$control_group = new controller_control_group((int) $control_group_id);

				$control_group->set_group_name($this->unmarshal($this->db->f('group_name'), 'string'));
				$control_group->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
				$control_group->set_procedure_name($this->unmarshal($this->db->f('procedure_title'), 'string'));
				$control_group->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
				$category = execMethod('phpgwapi.categories.return_single', $this->unmarshal($this->db->f('control_area_id', 'int')));
				$control_group->set_control_area_name($category[0]['name']);
				//$control_group->set_control_area_name($this->unmarshal($this->db->f('control_area_name'), 'string'));
				$control_group->set_building_part_id($this->unmarshal($this->db->f('building_part_id'), 'string'));
				$control_group->set_building_part_descr($this->unmarshal($this->db->f('building_part_descr'), 'string'));
			}
			//var_dump($control_group);
			return $control_group;
		}
		
		function get_control_groups_by_control_area($control_area_id)
		{
			$controls_array = array();

			$sql = "SELECT * FROM controller_control_group WHERE control_area_id=$control_area_id";
			$this->db->query($sql);

			while($this->db->next_record()) {
				$control_group = new controller_control_group((int) $this->db->f('id'));

				$control_group->set_group_name($this->unmarshal($this->db->f('group_name'), 'string'));
				$control_group->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
				$control_group->set_procedure_name($this->unmarshal($this->db->f('procedure_title'), 'string'));
				$control_group->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
				$category = execMethod('phpgwapi.categories.return_single', $this->unmarshal($this->db->f('control_area_id', 'int')));
				$control_group->set_control_area_name($category[0]['name']);
				//$control_group->set_control_area_name($this->unmarshal($this->db->f('control_area_name'), 'string'));
				$control_group->set_building_part_id($this->unmarshal($this->db->f('building_part_id'), 'string'));
				$control_group->set_building_part_descr($this->unmarshal($this->db->f('building_part_descr'), 'string'));

				$control_groups_array[] = $control_group->toArray();
			}

			if( count( $control_groups_array ) > 0 ){
				return $control_groups_array; 
			}
			else
			{
				return null;
			}
		}
		
		function get_control_areas_by_control_group($control_group_id)
		{
			$sql = "SELECT control_area_id FROM controller_control_group WHERE control_group_id=$control_group_id";
			$this->db->query($sql);

			while($this->db->next_record()) {
				$control_area = $this->unmarshal($this->db->f('control_area_id'), 'int');
				$category = execMethod('phpgwapi.categories.return_single', $this->unmarshal($this->db->f('control_area_id', 'int')));
				
				$control_area_array[] = array($control_area => $category[0]['name']);
			}

			if( count( $control_area_array ) > 0 ){
				return $control_area_array; 
			}
			else
			{
				return null;
			}
		}
		
		public function get_control_group_component($noOfObjects = null, $bim_type = null)
		{
			$filters = array();
			if($noOfObjects != null && is_numeric($noOfObjects))
			{
				$limit = "LIMIT {$noOfObjects}";
			}
			else
			{
				$limit = "LIMIT 10";
			}

			$joins = " {$this->left_join} controller_control_group ON (controller_control_group_component_list.control_group_id = controller_control_group.id)";
			$joins .= " {$this->left_join} fm_bim_item ON (controller_control_group_component_list.component_id = fm_bim_item.id)";
			$joins .= " {$this->left_join} fm_bim_type ON (fm_bim_item.type= fm_bim_type.id)";
			//$joins .= " {$this->left_join} fm_responsibility_role ON (c.responsibility_id = fm_responsibility_role.id)";
			$sql  = "SELECT controller_control_group.id AS control_group_id, controller_control_group.group_name AS control_group_name, fm_bim_type.name AS type_name, fm_bim_item.id AS bim_id, fm_bim_item.guid as bim_item_guid FROM controller_control_group_component_list {$joins} {$limit}";
			$controlGroupArray = array();
			//var_dump($sql);
			$this->db->query($sql, __LINE__, __FILE__);
			$i=1;
			while($this->db->next_record())
			{
				$controlGroupArray[$i]['id'] = $this->db->f('control_group_id');
				$controlGroupArray[$i]['title'] = $this->db->f('control_group_name');
				$controlGroupArray[$i]['bim_id'] = $this->db->f('bim_id');
				$controlGroupArray[$i]['bim_item_guid'] = $this->db->f('bim_item_guid');
				$controlGroupArray[$i]['bim_type'] = $this->db->f('type_name');
				$i++;
			}

			return $controlGroupArray;
		}
		
		function add_component_to_control_group($control_group_id, $component_id)
		{
			$sql =  "INSERT INTO controller_control_group_component_list (control_group_id, component_id) values($control_group_id, $component_id)";
			$this->db->query($sql);
		}
		
		function get_control_group_ids_for_control($control_id)
		{
			$results = array();

			$sql = "select distinct(cg.id) from controller_control_group cg, controller_control_item ci, controller_control_item_list cil where cil.control_id = {$control_id} and ci.id = cil.control_item_id and cg.id = ci.control_group_id";
			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$results[] = $this->db->f('id');
			}

			return $results;
		}
		
		function get_components_for_control_group($control_group_id)
		{
			$results = array();
			
			$sql = "select * from controller_control_group_component_list where control_group_id={$control_group_id}";
			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$results[] = $this->db->f('component_id');
			}

			return $results;
		}
		
		function get_all_control_groups_array()
		{
				$results = array();
				$this->db->query("SELECT id, group_name FROM controller_control_group ORDER BY group_name ASC", __LINE__, __FILE__);
				while ($this->db->next_record())
				{
					$results[] = array('id' => $this->db->f('id', false),
									   'group_name' => $this->db->f('group_name', false));
				}
				return $results;
		}
	}
