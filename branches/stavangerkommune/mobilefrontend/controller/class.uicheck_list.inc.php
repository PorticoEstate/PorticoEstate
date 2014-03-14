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
 	* @version $Id: class.uicheck_list.inc.php 11511 2013-12-08 20:57:07Z sigurdne $
	*/
	
	phpgw::import_class('controller.uicheck_list');

	class mobilefrontend_uicheck_list extends controller_uicheck_list
	{
		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = true;			
			//FIXME
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/mobilefrontend/css/base.css');
		}
    
    /**
		 * Public function for displaying the edit check list form  
		 * 
		 * @param HTTP:: check list id
		 * @return data array
		*/
/*
		function edit_check_list( $check_list = null ){
			if($check_list == null)
			{
				$check_list_id = phpgw::get_var('check_list_id');
				$check_list = $this->so->get_single($check_list_id);
			}
			
			$control = $this->so_control->get_single($check_list->get_control_id());
			
			$component_id = $check_list->get_component_id();

			if($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();
				
				$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id, 'id' => $component_id));
				$short_desc = execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));
    		
				$component = new controller_component();
				$component->set_location_code( $component_arr['location_code'] );
    		$component->set_xml_short_desc( $short_desc );
				$component_array = $component->toArray();
				
				$type = 'component';
				$building_location_code = $this->location_finder->get_building_location_code($component_arr['location_code']);
			}
			else
			{
				$location_code = $check_list->get_location_code();
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				$type = 'location';
				$level = $this->location_finder->get_location_level($location_code);
			}
			
			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());
			
      $level = $this->location_finder->get_location_level($location_code);
			$user_role = true;

			// Fetches buildings on property
			$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);
      
      
      
      $saved_control_groups = $this->so_control_group_list->get_control_groups_by_control($control->get_id());
		
			$control_groups_with_items_array = array();
      
      //Populating array with saved control items for each group
			foreach ($saved_control_groups as $control_group)
			{	
				$saved_control_items = $this->so_control_item_list->get_control_items_and_options_by_control_and_group($control->get_id(), $control_group->get_id(), "return_array");

				if(count($saved_control_items) > 0)
				{				
					$control_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
				}
			}
      
			$data = array
			(
				'control'                         => $control,
				'check_list'                      => $check_list,
        'buildings_on_property'           => $buildings_on_property,
				'location_array'                  => $location_array,
				'component_array'                 => $component_array,
				'type'                            => $type,
				'current_year'                    => $year,
				'current_month_nr'                => $month,
				'building_location_code'          => $building_location_code,
				'location_level'                  => $level,
        'control_groups_with_items_array' => $control_groups_with_items_array,
        'cases_view'                      => 'add_case'
			);
			
			$GLOBALS['phpgw']->jqcal->add_listener('planned_date');
			$GLOBALS['phpgw']->jqcal->add_listener('completed_date');
			$GLOBALS['phpgw']->jqcal->add_listener('deadline_date');
      
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
      self::add_javascript('controller', 'controller', 'check_list.js');
			
			self::render_template_xsl(array('check_list/fragments/check_list_menu', 
                                      'check_list/fragments/check_list_top_section', 'case/add_case', 
                                      'check_list/fragments/select_buildings_on_property'), $data);
		}
    */
	}
