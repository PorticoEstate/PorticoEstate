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

	include_class('controller', 'control', 'inc/model/');

	class controller_socontrol extends controller_socommon
	{
		protected static $so;

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return controller_soparty the storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null) {
				self::$so = CreateObject('controller.socontrol');
			}
			return self::$so;
		}

		/**
		 * Function for adding a new control to the database. Updates the control object.
		 *
		 * @param activitycalendar_activity $activity the party to be added
		 * @return bool true if successful, false otherwise
		 */
		function add(&$control)
		{
			$title = $control->get_title();

			$sql = "INSERT INTO controller_control (title) VALUES ('$title')";
			$result = $this->db->query($sql, __LINE__,__FILE__);

			if(isset($result)) {

				// Set the new control ID
				$control->set_id($this->db->get_last_insert_id('controller_control', 'id'));

				// Forward this request to the update method
				return $this->update($control);
			}
			else
			{
				return false;
			}
		}

		/**
		 * Update the database values for an existing activity object.
		 *
		 * @param $activity the activity to be updated
		 * @return boolean true if successful, false otherwise
		 */
		function update($control)
		{

			$id = intval($control->get_id());

			$values = array(
				'title = ' . $this->marshal($control->get_title(), 'string'),
				'description = ' . $this->marshal($control->get_description(), 'string'),
				'start_date = ' . $this->marshal($control->get_start_date(), 'int'),
				'end_date = ' . $this->marshal($control->get_end_date(), 'int'),
				'control_area_id = ' . $this->marshal($control->get_control_area_id()),
				'repeat_type = ' . $this->marshal($control->get_repeat_type(), 'string'),
				'repeat_interval = ' . $this->marshal($control->get_repeat_interval(), 'string'),
				'procedure_id = ' . $this->marshal($control->get_procedure_id(), 'int'),
				'responsibility_id = ' . $this->marshal($control->get_responsibility_id(), 'int')
			);

			$result = $this->db->query('UPDATE controller_control SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			if( isset($result) ){
				return $id;
			}else{
				return 0;
			}

			// Kommenterte denne ut midlertidig. 
			//Trenger id-en som ble lagret når controllen blir lagret. 
			//return isset($result);
		}



		function get_controls_by_control_area($control_area_id)
		{
			$controls_array = array();

			$sql = "SELECT * FROM controller_control WHERE control_area_id=$control_area_id";
			$this->db->query($sql);

			while($this->db->next_record()) {
				$control = new controller_control($this->unmarshal($this->db->f('id', true), 'int'));
				$control->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$control->set_description($this->unmarshal($this->db->f('description', true), 'boolean'));
				$control->set_start_date($this->unmarshal($this->db->f('start_date', true), 'int'));
				$control->set_end_date($this->unmarshal($this->db->f('end_date', true), 'int'));
				$control->set_procedure_id($this->unmarshal($this->db->f('procedure_id', true), 'int'));
				$control->set_procedure_name($this->unmarshal($this->db->f('procedure_name', true), 'string'));
				$control->set_requirement_id($this->unmarshal($this->db->f('requirement_id', true), 'int'));
				$control->set_costresponsibility_id($this->unmarshal($this->db->f('costresponsibility_id', true), 'int'));
				$control->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'int'));
				$control->set_control_area_id($this->unmarshal($this->db->f('control_area_id', true), 'int'));
				$control->set_control_area_name($this->unmarshal($this->db->f('control_area_name', true), 'string'));
				$control->set_component_type_id($this->unmarshal($this->db->f('component_type_id', true), 'int'));
				$control->set_component_id($this->unmarshal($this->db->f('component_id', true), 'int'));
				$control->set_location_code($this->unmarshal($this->db->f('location_code', true), 'int'));
				$control->set_repeat_type($this->unmarshal($this->db->f('repeat_type', true), 'int'));
				$control->set_repeat_interval($this->unmarshal($this->db->f('repeat_interval', true), 'int'));

				$controls_array[] = $control->toArray();
			}

			if( count( $controls_array ) > 0 ){
				return $controls_array; 
			}
			else
			{
				return null;
			}
		}

		function get_locations_for_control($control_id)
		{
			$controls_array = array();

			$sql =  "SELECT c.id, c.title, cll.location_code "; 
			$sql .= "FROM controller_control c, controller_control_location_list cll ";
			$sql .= "WHERE cll.control_id = $control_id ";
			$sql .= "AND cll.control_id = c.id";

			$this->db->query($sql);

			while($this->db->next_record()) {
				$control_id = $this->unmarshal($this->db->f('id', true), 'int');
				$title = $this->unmarshal($this->db->f('title', true), 'string');
				$location_code = $this->unmarshal($this->db->f('location_code', true), 'int');

				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));

				$controls_array[] = array("id" => $control_id, "title" => $title, "location_code" => $location_code, "loc1_name" => $location_array["loc1_name"]);
			}

			if( count( $controls_array ) > 0 ){
				return $controls_array; 
			}
			else
			{
				return null;
			}
		}

		function add_location_to_control($control_id, $location_code)
		{
			$sql =  "INSERT INTO controller_control_location_list (control_id, location_code) values($control_id, $location_code)";
			$this->db->query($sql);
		}

		function add_component_to_control($control_id, $component_id)
		{
			$sql =  "INSERT INTO controller_control_component_list (control_id, component_id) values($control_id, $component_id)";
			$this->db->query($sql);
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
					'table'			=> 'control', // alias
					'field'			=> 'id',
					'translated'	=> 'id'
				);
			}

			return $ret;
		}

		protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
		{
			$clauses = array('1=1');

			$filter_clauses = array();

			// Search for based on search type
			if($search_for)
			{
				$search_for = $this->marshal($search_for,'field');
				$like_pattern = "'%".$search_for."%'";
				$like_clauses = array();
				switch($search_type){
					default:
						$like_clauses[] = "controller_control.title $this->like $like_pattern";
						$like_clauses[] = "controller_control.description $this->like $like_pattern";
						break;
				}

				if(count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
			}
			//var_dump($filters);
			if(isset($filters[$this->get_id_field_name()]))
			{
				$filter_clauses[] = "controller_control.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
			}
			if(isset($filters['control_areas']))
			{
				$filter_clauses[] = "controller_control.control_area_id = {$this->marshal($filters['control_areas'],'int')}";
			}
			if(isset($filters['responsibilities']))
			{
				$filter_clauses[] = "controller_control.responsibility_id = {$this->marshal($filters['responsibilities'],'int')}";
			}

			if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}


			$condition =  join(' AND ', $clauses);

			$tables = "controller_control";
			//$joins = " {$this->left_join} rental_document_types ON (rental_document.type_id = rental_document_types.id)";
			$joins = " {$this->left_join} controller_control_area ON (controller_control.control_area_id = controller_control_area.id)";
			$joins .= " {$this->left_join} controller_procedure ON (controller_control.procedure_id = controller_procedure.id)";
			$joins .= " {$this->left_join} fm_responsibility_role ON (controller_control.responsibility_id = fm_responsibility_role.id)";

			if($return_count)
			{
				$cols = 'COUNT(DISTINCT(controller_control.id)) AS count';
			}
			else
			{
				$cols = 'controller_control.id, controller_control.title, controller_control.description, controller_control.start_date, controller_control.end_date, controller_control.procedure_id, controller_control.control_area_id, controller_control.requirement_id, controller_control.costresponsibility_id, controller_control.responsibility_id, controller_control.component_type_id, controller_control.component_id, controller_control.location_code, controller_control.repeat_type, controller_control.repeat_interval, controller_control.enabled, controller_control_area.title AS control_area_name, controller_procedure.title AS procedure_name, fm_responsibility_role.name AS responsibility_name ';
			}

			$dir = $ascending ? 'ASC' : 'DESC';
			if($sort_field == 'title')
			{
				$sort_field = 'controller_control.title';
			}
			else if($sort_field == 'id')
			{
				$sort_field = 'controller_control.id';
			}
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';

			return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";

		}

		function populate(int $control_id, &$control)
		{
			if($control == null) {
				$control = new controller_control((int) $control_id);

				$control->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$control->set_description($this->unmarshal($this->db->f('description', true), 'boolean'));
				$control->set_start_date($this->unmarshal($this->db->f('start_date', true), 'int'));
				$control->set_end_date($this->unmarshal($this->db->f('end_date', true), 'int'));
				$control->set_procedure_id($this->unmarshal($this->db->f('procedure_id', true), 'int'));
				$control->set_procedure_name($this->unmarshal($this->db->f('procedure_name', true), 'string'));
				$control->set_requirement_id($this->unmarshal($this->db->f('requirement_id', true), 'int'));
				$control->set_costresponsibility_id($this->unmarshal($this->db->f('costresponsibility_id', true), 'int'));
				$control->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'int'));
				$control->set_responsibility_name($this->unmarshal($this->db->f('responsibility_name', true), 'string'));
				$control->set_control_area_id($this->unmarshal($this->db->f('control_area_id', true), 'int'));
				$control->set_control_area_name($this->unmarshal($this->db->f('control_area_name', true), 'string'));
	//			$control->set_control_group_id($this->unmarshal($this->db->f('control_group_id', true), 'int'));
				$control->set_component_type_id($this->unmarshal($this->db->f('component_type_id', true), 'int'));
				$control->set_component_id($this->unmarshal($this->db->f('component_id', true), 'int'));
				$control->set_location_code($this->unmarshal($this->db->f('location_code', true), 'int'));
				$control->set_repeat_type($this->unmarshal($this->db->f('repeat_type', true), 'int'));
				$control->set_repeat_interval($this->unmarshal($this->db->f('repeat_interval', true), 'int'));
			}

			return $control;
		}

		/**
		 * Get single control
		 * 
		 * @param	$id	id of the control to return
		 * @return a controller_control
		 */
		function get_single($id)
		{
			$id = (int)$id;

			$joins = " {$this->left_join} controller_control_area ON (c.control_area_id = controller_control_area.id)";
			$joins .= " {$this->left_join} controller_procedure ON (c.procedure_id = controller_procedure.id)";
			$joins .= " {$this->left_join} fm_responsibility_role ON (c.responsibility_id = fm_responsibility_role.id)";

			$sql = "SELECT c.*, controller_control_area.title AS control_area_name, controller_procedure.title AS procedure_name, fm_responsibility_role.name AS responsibility_name FROM controller_control c {$joins} WHERE c.id = " . $id;
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			$this->db->next_record();

			$control = new controller_control((int) $id);

			$control->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$control->set_description($this->unmarshal($this->db->f('description', true), 'boolean'));
			$control->set_start_date($this->unmarshal($this->db->f('start_date', true), 'int'));
			$control->set_end_date($this->unmarshal($this->db->f('end_date', true), 'int'));
			$control->set_procedure_id($this->unmarshal($this->db->f('procedure_id', true), 'int'));
			$control->set_procedure_name($this->unmarshal($this->db->f('procedure_name', true), 'string'));
			$control->set_requirement_id($this->unmarshal($this->db->f('requirement_id', true), 'int'));
			$control->set_costresponsibility_id($this->unmarshal($this->db->f('costresponsibility_id', true), 'int'));
			$control->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'int'));
			$control->set_responsibility_name($this->unmarshal($this->db->f('responsibility_name', true), 'string'));
			$control->set_control_area_id($this->unmarshal($this->db->f('control_area_id', true), 'int'));
			$control->set_control_area_name($this->unmarshal($this->db->f('control_area_name', true), 'string'));
	//			$control->set_control_group_id($this->unmarshal($this->db->f('control_group_id', true), 'int'));
			$control->set_component_type_id($this->unmarshal($this->db->f('component_type_id', true), 'int'));
			$control->set_component_id($this->unmarshal($this->db->f('component_id', true), 'int'));
			$control->set_location_code($this->unmarshal($this->db->f('location_code', true), 'int'));
			$control->set_repeat_type($this->unmarshal($this->db->f('repeat_type', true), 'int'));
			$control->set_repeat_interval($this->unmarshal($this->db->f('repeat_interval', true), 'int'));

			return $control;
		}

		function get_roles()
		{
			$ret_array = array();
			$ret_array[0] = array('id' =>  0,'name' => lang('Not selected'));
			$sql = "select * from fm_responsibility_role ORDER BY name";
			$this->db->query($sql, __LINE__, __FILE__);
			$i = 1;
			while($this->db->next_record())
			{
				$ret_array[$i]['id'] = $this->db->f('id');
				$ret_array[$i]['name'] = $this->db->f('name');
				$i++;
			}
			return $ret_array;
		}

		function get_bim_types($ifc = null)
		{
			$ret_array = array();
			if($ifc != null)
			{
				if($ifc == 1)
					$where_clause = "WHERE is_ifc";
				else
					$where_clause = "WHERE NOT is_ifc";
			}
			$sql = "select * from fm_bim_type {$where_clause} ORDER BY name";
			$this->db->query($sql, __LINE__, __FILE__);
			$i = 1;
			while($this->db->next_record())
			{
				$ret_array[$i]['id'] = $this->db->f('id');
				$ret_array[$i]['name'] = $this->db->f('name');
				$i++;
			}
			return $ret_array;
		}

		public function getAllBimItems($noOfObjects = null, $bim_type = null) {
			$filters = array();
			if($noOfObjects != null && is_numeric($noOfObjects))
			{
				$limit = "LIMIT {$noOfObjects}";
			}
			else
			{
				$limit = "LIMIT 10";
			}
			if($bim_type != null && is_numeric($bim_type))
			{
				$filter = " AND fm_bim_type.id = {$bim_type}";
			}
			$sql  = "SELECT fm_bim_item.id, fm_bim_type.name AS type, fm_bim_item.guid FROM public.fm_bim_item,  public.fm_bim_type WHERE fm_bim_item.type = fm_bim_type.id {$filter} {$limit}";
			$bimItemArray = array();
			$this->db->query($sql, __LINE__, __FILE__);
			$i=1;
			while($this->db->next_record())
			{
				$bimItemArray[$i]['id'] = $this->db->f('id');
				$bimItemArray[$i]['guid'] = $this->db->f('guid');
				$bimItemArray[$i]['type'] = $this->db->f('type');
				//$bimItemArray[$i]['xml_representation'] = $this->db->f('xml_representation',true);
				//$bimItemArray[] = $bimItem;
				$i++;
			}

			return $bimItemArray;
		}

		public function get_control_component($noOfObjects = null, $bim_type = null)
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

			$joins = " {$this->left_join} controller_control_component_list ON (c.id = controller_control_component_list.control_id)";
			$joins .= " {$this->left_join} fm_bim_item ON (controller_control_component_list.component_id = fm_bim_item.id)";
			$joins .= " {$this->left_join} fm_bim_type ON (fm_bim_item.type= fm_bim_type.id)";
			//$joins .= " {$this->left_join} fm_responsibility_role ON (c.responsibility_id = fm_responsibility_role.id)";
			$sql  = "SELECT c.id AS control_id, c.title AS control_title, fm_bim_type.name AS type_name, fm_bim_item.id AS bim_id, fm_bim_item.guid as bim_item_guid FROM controller_control c {$joins} {$limit}";
			$controlArray = array();
			$this->db->query($sql, __LINE__, __FILE__);
			$i=1;
			while($this->db->next_record())
			{
				$controlArray[$i]['id'] = $this->db->f('control_id');
				$controlArray[$i]['title'] = $this->db->f('control_title');
				$controlArray[$i]['bim_id'] = $this->db->f('bim_id');
				$controlArray[$i]['bim_item_guid'] = $this->db->f('bim_item_guid');
				$controlArray[$i]['bim_type'] = $this->db->f('type_name');
				$i++;
			}

			return $controlArray;
		}

		public function getBimItemAttributeValue($bimItemGuid, $attribute) 
		{
			$columnAlias = "attribute_values";
			$sql = "select array_to_string(xpath('descendant-or-self::*[{$attribute}]/{$attribute}/text()', (select xml_representation from fm_bim_item where guid='{$bimItemGuid}')), ',') as $columnAlias";
			$this->db->query($sql,__LINE__,__FILE__);
			if($this->db->num_rows() > 0)
			{
				$this->db->next_record();
				$result = $this->db->f($columnAlias,true);
				return preg_split('/,/', $result);
			}
		}
	}
