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
 	* @version $Id: class.uicheck_list.inc.php 8628 2012-01-21 10:42:05Z vator $
	*/
	
	phpgw::import_class('phpgwapi.yui');

	/**
	* Import the jQuery class
	*/
	phpgw::import_class('phpgwapi.jquery');

	phpgw::import_class('controller.uicommon');
	phpgw::import_class('controller.socontrol_area');
	
	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'status_checker', 'inc/helper/');
	include_class('controller', 'date_helper', 'inc/helper/');
		
	class controller_uicontrol_location extends controller_uicommon
	{
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $currentapp;
		var $type_id;
		var $location_code;
		
		private $so_control_area;
		private $so_control;
		private $so_check_list;
		private $so_control_item;
		private $so_check_item;
		private $so_procedure;
	
		var $public_functions = array(
										'index'								=> true,
										'view_locations_for_control' 		=> true,
										'register_control_to_location' 		=> true,
										'register_control_to_location_2'	=> true,
										'register_control_to_component'		=> true,
										'get_locations_for_control' 		=> true,
										'get_location_category'				=> true,
										'get_district_part_of_town'			=> true,
										'entity'							=> true,
										'index2'							=> true,
										'get_category_by_entity'			=> true
									);

		function __construct()
		{
			parent::__construct();
			
			$this->bo					= CreateObject('property.bolocation',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->so_control_area 		= CreateObject('controller.socontrol_area');
			$this->so_control 			= CreateObject('controller.socontrol');
			$this->so_check_list		= CreateObject('controller.socheck_list');
			$this->so_control_item		= CreateObject('controller.socontrol_item');
			$this->so_check_item		= CreateObject('controller.socheck_item');
			$this->so_procedure			= CreateObject('controller.soprocedure');
			
			$this->type_id				= $this->bo->type_id;
			
			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->part_of_town_id		= $this->bo->part_of_town_id;
			$this->district_id			= $this->bo->district_id;
			$this->status				= $this->bo->status;
			$this->allrows				= $this->bo->allrows;
			$this->lookup				= $this->bo->lookup;
			$this->location_code		= $this->bo->location_code;
			
			self::set_active_menu('controller::control::location_for_check_list');
		}	
	
		function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			
			// Sigurd: START as categories
			$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;
			
			$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','globals' => true,'use_acl' => $this->_category_acl));
							
			$control_areas_array = array();
			foreach($control_areas['cat_list'] as $cat_list)
			{
				$control_areas_array[] = array
				(
					'id' 	=> $cat_list['cat_id'],
					'name'	=> $cat_list['name'],
				);		
			}
			// END as categories

			$tabs = array( array(
						'label' => lang('View_locations_for_control')
					), array(
						'label' => lang('Add_locations_for_control'),
						'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol_location.register_control_to_location'))
					));
			
			$data = array(
				'tabs'					=> $GLOBALS['phpgw']->common->create_tabs($tabs, 0),
				'view'					=> "view_locations_for_control",
				'control_areas_array'	=> $control_areas_array,
				'locations_table' => array(
					'source' => self::link(array('menuaction' => 'controller.uicontrol_location.get_locations_for_control', 'control_id' => $control_id ,'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ControlId'),
							'sortable'	=> true,
						),
						array(
							'key'	=>	'title',
							'label'	=>	lang('Control title'),
							'sortable'	=>	false
						),
						array(
							'key' => 'location_code',
							'label' => lang('location_code'),
							'sortable'	=> false
						),
						array(
							'key' => 'loc1_name',
							'label' => lang('Property name'),
							'sortable'	=> false
						),
						array(
							'key' => 'actions',
							'hidden' => true
						),
						array(
							'key' => 'labels',
							'hidden' => true
						),
						array(
							'key' => 'ajax',
							'hidden' => true
						),array(
							'key' => 'parameters',
							'hidden' => true
						)						
					)
				)
			);

			phpgwapi_yui::load_widget('paginator');
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');	

			self::render_template_xsl(array('control_location/control_location_tabs', 'control_location/view_locations_for_control', 'common' ), $data);		
		}
		
		function register_control_to_location()
		{
			$control_id = phpgw::get_var('control_id');
			if(phpgw::get_var('save_location'))
			{
				$values = phpgw::get_var('values');
				//add component to control using component item ID
				$values['control_location'] = isset($values['control_location']) && $values['control_location'] ? array_unique($values['control_location']) : array();
				$values['control_location_orig'] = isset($values['control_location_orig']) && $values['control_location_orig'] ? array_unique($values['control_location_orig']) : array();

				$ok = $this->so_control->register_control_to_location($control_id, $values);

/*				if($ok)
				{
					return json_encode( array( "status" => "saved" ) );
				}
				else
				{
					return json_encode( array( "status" => "not_saved" ) );
				}
*/
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_location.register_control_to_location', 'control_id' => $control_id));

			}
			else
			{
				if(phpgw::get_var('phpgw_return_as') == 'json') {
					return $this->query();
				}
				$building_types  = execMethod('property.soadmin_location.read',array());
				//$type_id=phpgw::get_var('type_id');
				//if(!isset($type_id))
				$type_id = 1;
				
				$category_types = $this->bocommon->select_category_list(array(
																			'format'=>'filter',
																			'selected' => $this->cat_id,
																			'type' =>'location',
																			'type_id' =>$type_id,
																			'order'=>'descr'
																		));
				$default_value = array ('id'=>'','name'=>lang('no category selected'));
				array_unshift($category_types,$default_value);
																		
				$district_list  = $this->bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift($district_list,$default_value);
				
				$part_of_town_list =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no part of town'));
				array_unshift($part_of_town_list,$default_value);
				
				$_role_criteria = array
						(
							'type'		=> 'responsibility_role',
							'filter'	=> array('location_level' => (int)$type_id),
							'order'		=> 'name'
						);
	
				$responsibility_roles_list = execMethod('property.sogeneric.get_list',$_role_criteria);
				$default_value = array ('id'=>'','name'=>lang('no role'));
				array_unshift ($responsibility_roles,$default_value);
				
				$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
				$cats->supress_info	= true;
				
				$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','globals' => true,'use_acl' => $this->_category_acl));
								
				$control_areas_array = array();
				foreach($control_areas['cat_list'] as $cat_list)
				{
					$control_areas_array[] = array
					(
						'id' 	=> $cat_list['cat_id'],
						'name'	=> $cat_list['name'],
					);		
				}

				$control_info = execMethod('controller.socontrol.get_single', $control_id);
				if($control_info)
				{
					$control_array = array
					(
						'id' => $control_id,
						'title'	=> $control_info->get_title()
					);
				}

				$tabs = array
				( 
					array
					(
						'label' => lang('View_locations_for_control'),
						'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol_location.index'))
					),
					array
					(
						'label' => lang('Add_locations_for_control')
					),
					array
					(
						'label' => lang('add components for control'),
						'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol_location.register_control_to_component'))
					)

				);
						
				$data = array(
					'tabs'						=> $GLOBALS['phpgw']->common->create_tabs($tabs, 1),
					'view'						=> "register_control_to_location",
					'control_id'				=> $control_id,
					'control_filters'			=> array(
						'control_areas_array' 	=> $control_areas_array,
						'control_array' 			=> $control_array
					),
					'filter_form' 				=> array(
						'building_types' 			=> $building_types,
						'category_types' 			=> $category_types,
						'district_list' 			=> $district_list,
						'part_of_town_list' 		=> $part_of_town_list
					),
					'datatable' => array(
						'source' => self::link(array('menuaction' => 'controller.uicontrol_location.index', 'phpgw_return_as' => 'json', 'view_type' => 'register_control','control_id_init'	=> $control_id)),
						'field' => array(
							array(
								'key' => 'location_registered',
								'hidden' => true
							),
							array(
								'key' => 'location_code',
								'label' => lang('Property'),
								'sortable'	=> true,
								'formatter' => 'YAHOO.portico.formatLink'
							),
							array(
								'key'	=>	'loc1_name',
								'label'	=>	lang('Property name'),
								'sortable'	=>	false
							),
							array(
								'key' => 'adresse1',
								'label' => lang('Address'),
								'sortable'	=> false
							),
							array(
								'key' => 'postnummer',
								'label' => lang('Zip code'),
								'sortable'	=> false
							),
							array(
								'key' => 'control_name',
								'label' => lang('control'),
								'sortable'	=> false
							),
							array(
									'key' => 'checked',
									'label' => 'Velg',
									'sortable' => false,
									'formatter' => 'formatterCheckLocation',
									'className' => 'mychecks'
							),
							array(
								'key' => 'actions',
								'hidden' => true
							),
							array(
								'key' => 'labels',
								'hidden' => true
							),
							array(
								'key' => 'ajax',
								'hidden' => true
							),array(
								'key' => 'parameters',
								'hidden' => true
							)						
						)
					)
				);
				
				phpgwapi_yui::load_widget('paginator');
				
				self::add_javascript('controller', 'controller', 'jquery.js');
				self::add_javascript('controller', 'controller', 'ajax.js');
				self::add_javascript('controller', 'yahoo', 'register_control_to_location.js');
	
				self::render_template_xsl(array('control_location/control_location_tabs', 'control_location/register_control_to_location', 'common'), $data);
			}		
		}
		


		function register_control_to_component()
		{
			$control_id = phpgw::get_var('control_id');
			if(phpgw::get_var('save_location'))
			{
				$values = phpgw::get_var('values');
				//add component to control using component item ID
				$values['control_location'] = isset($values['control_location']) && $values['control_location'] ? array_unique($values['control_location']) : array();
				$values['control_location_orig'] = isset($values['control_location_orig']) && $values['control_location_orig'] ? array_unique($values['control_location_orig']) : array();

				$ok = $this->so_control->register_control_to_location($control_id, $values);

/*				if($ok)
				{
					return json_encode( array( "status" => "saved" ) );
				}
				else
				{
					return json_encode( array( "status" => "not_saved" ) );
				}
*/
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_location.register_control_to_location', 'control_id' => $control_id));

			}
			else
			{
				if(phpgw::get_var('phpgw_return_as') == 'json') {
					return $this->query();
				}
				$building_types  = execMethod('property.soadmin_location.read',array());
				//$type_id=phpgw::get_var('type_id');
				//if(!isset($type_id))
				$type_id = 1;
				
				$category_types = $this->bocommon->select_category_list(array(
																			'format'=>'filter',
																			'selected' => $this->cat_id,
																			'type' =>'location',
																			'type_id' =>$type_id,
																			'order'=>'descr'
																		));
				$default_value = array ('id'=>'','name'=>lang('no category selected'));
				array_unshift($category_types,$default_value);
																		
				$district_list  = $this->bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift($district_list,$default_value);
				
				$part_of_town_list =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no part of town'));
				array_unshift($part_of_town_list,$default_value);
				
				$_role_criteria = array
						(
							'type'		=> 'responsibility_role',
							'filter'	=> array('location_level' => (int)$type_id),
							'order'		=> 'name'
						);
	
				$responsibility_roles_list = execMethod('property.sogeneric.get_list',$_role_criteria);
				$default_value = array ('id'=>'','name'=>lang('no role'));
				array_unshift ($responsibility_roles,$default_value);
				
				$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
				$cats->supress_info	= true;
				
				$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','globals' => true,'use_acl' => $this->_category_acl));
								
				$control_areas_array = array();
				foreach($control_areas['cat_list'] as $cat_list)
				{
					$control_areas_array[] = array
					(
						'id' 	=> $cat_list['cat_id'],
						'name'	=> $cat_list['name'],
					);		
				}

				$control_info = execMethod('controller.socontrol.get_single', $control_id);
				if($control_info)
				{
					$control_array = array
					(
						'id' => $control_id,
						'title'	=> $control_info->get_title()
					);
				}

				$tabs = array
				( 
					array
					(
						'label' => lang('View_locations_for_control'),
						'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol_location.index'))
					),
					array
					(
						'label' => lang('Add_locations_for_control'),
						'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol_location.register_control_to_location'))
					),
					array
					(
						'label' => lang('add components for control'),
					)

				);
						
				$data = array(
					'tabs'						=> $GLOBALS['phpgw']->common->create_tabs($tabs, 2),
					'view'						=> "register_control_to_location",
					'control_id'				=> $control_id,
					'control_filters'			=> array(
						'control_areas_array' 	=> $control_areas_array,
						'control_array' 			=> $control_array
					),
					'filter_form' 				=> array(
						'building_types' 			=> $building_types,
						'category_types' 			=> $category_types,
						'district_list' 			=> $district_list,
						'part_of_town_list' 		=> $part_of_town_list
					),
					'datatable' => array(
						'source' => self::link(array('menuaction' => 'controller.uicontrol_location.index', 'phpgw_return_as' => 'json', 'view_type' => 'register_control','control_id_init'	=> $control_id)),
						'field' => array(
							array(
								'key' => 'location_registered',
								'hidden' => true
							),
							array(
								'key' => 'location_code',
								'label' => lang('Property'),
								'sortable'	=> true,
								'formatter' => 'YAHOO.portico.formatLink'
							),
							array(
								'key'	=>	'loc1_name',
								'label'	=>	lang('Property name'),
								'sortable'	=>	false
							),
							array(
								'key' => 'adresse1',
								'label' => lang('Address'),
								'sortable'	=> false
							),
							array(
								'key' => 'postnummer',
								'label' => lang('Zip code'),
								'sortable'	=> false
							),
							array(
								'key' => 'control_name',
								'label' => lang('control'),
								'sortable'	=> false
							),
							array(
									'key' => 'checked',
									'label' => 'Velg',
									'sortable' => false,
									'formatter' => 'formatterCheckLocation',
									'className' => 'mychecks'
							),
							array(
								'key' => 'actions',
								'hidden' => true
							),
							array(
								'key' => 'labels',
								'hidden' => true
							),
							array(
								'key' => 'ajax',
								'hidden' => true
							),array(
								'key' => 'parameters',
								'hidden' => true
							)						
						)
					)
				);
				
				phpgwapi_yui::load_widget('paginator');
				
				self::add_javascript('controller', 'controller', 'jquery.js');
				self::add_javascript('controller', 'controller', 'ajax.js');
				self::add_javascript('controller', 'yahoo', 'register_control_to_location.js');
	
				self::render_template_xsl(array('control_location/control_location_tabs', 'control_location/register_control_to_location', 'common'), $data);
			}		
		}



		function entity()
		{
			$bocommon					= CreateObject('property.bocommon');
			$boentity					= CreateObject('property.boentity');
			$boadmin_entity				= CreateObject('property.boadmin_entity');
			$this->start				= $boentity->start;
			$this->query				= $boentity->query;
			$this->sort					= $boentity->sort;
			$this->order				= $boentity->order;
			$this->filter				= $boentity->filter;
			$this->cat_id				= $boentity->cat_id;
			$this->part_of_town_id		= $boentity->part_of_town_id;
			$this->district_id			= $boentity->district_id;
			$this->entity_id			= $boentity->entity_id;
			$this->location_code		= $boentity->location_code;
			$this->criteria_id			= $boentity->criteria_id;

			$second_display = phpgw::get_var('second_display', 'bool');
			$default_district 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');

			if ($default_district && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_district;
				$this->district_id		= $default_district;
			}

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'		=> 'property.uilookup.entity',
						'entity_id'			=> $this->entity_id,
						'cat_id'			=> $this->cat_id,
						'district_id'		=> $this->district_id,
						'query'				=> $this->query,
						'filter'			=> $this->filter,
						'location_code'		=> $this->location_code,
						'criteria_id'		=> $this->criteria_id
					));

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilookup.entity',"
					."second_display:1,"
					."entity_id:'{$this->entity_id}',"
					."cat_id:'{$this->cat_id}',"
					."district_id:'{$this->district_id}',"
					."query:'{$this->query}',"
					."filter:'{$this->filter}',"
					."criteria_id:'{$this->criteria_id}',"
					."location_code:'{$this->location_code}'";

				$values_combo_box[0] = $boentity->select_category_list('filter',$this->cat_id);
				$default_value = array ('id'=>'','name'=>lang('no category'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift ($values_combo_box[1],$default_value);

				$values_combo_box[2]  = $boentity->get_criteria_list($this->criteria_id);
				$default_value = array ('id'=>'','name'=>lang('no criteria'));
				array_unshift ($values_combo_box[2],$default_value);

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uilookup.entity',
								'second_display'	=> $second_display,
								'entity_id'			=> $this->entity_id,
								'cat_id'			=> $this->cat_id,
								'district_id'		=> $this->district_id,
								'query'				=> $this->query,
								'filter'			=> $this->filter
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton 	CATEGORY
									'id' => 'btn_cat_id',
									'name' => 'cat_id',
									'value'	=> lang('Category'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 1
								),
								array
								( //boton 	CATEGORY
									'id' => 'btn_district_id',
									'name' => 'district_id',
									'value'	=> lang('District'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 2
								),
								array
								( //boton 	search criteria
									'id' => 'btn_criteria_id',
									'name' => 'criteria_id',
									'value'	=> lang('search criteria'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 3
								),
								array
								( //boton  SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 5
								),
								array
								( // TEXT IMPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',//'',//$query,
									'type' => 'text',
									'size'    => 28,
									'onkeypress' => 'return pulsar(event)',
									'tab_index' => 4
								)
							),
							'hidden_value' => array
							(
								array
								( //div values  combo_box_0
									'id' => 'values_combo_box_0',
									'value'	=> $bocommon->select2String($values_combo_box[0]) //i.e.  id,value/id,vale/
								),
								array
								( //div values  combo_box_1
									'id' => 'values_combo_box_1',
									'value'	=> $bocommon->select2String($values_combo_box[1]) //i.e.  id,value/id,vale/
								),
								array
								( //div values  combo_box_2
									'id' => 'values_combo_box_2',
									'value'	=> $bocommon->select2String($values_combo_box[2]) //i.e.  id,value/id,vale/
								)
							)
						)
					)
				);
			}

			$entity_list = $boentity->read(array('lookup'=>true));
			$input_name = $GLOBALS['phpgw']->session->appsession('lookup_fields','property');
			$uicols	= $boentity->uicols;

			if (count($uicols['name']) > 0)
			{
				for ($m = 0; $m<count($input_name); $m++)
				{
					if (!array_search($input_name[$m],$uicols['name']))
					{
						$uicols['name'][] 	= $input_name[$m];
						$uicols['descr'][] 	= '';
						$uicols['input_type'][] 	= 'hidden';
					}
				}
			}
			else
			{

				$uicols['name'][] 	= 'num';
				$uicols['descr'][] 	= 'ID';
				$uicols['input_type'][] 	= 'text';
			}

			$content = array();
			$j=0;
			if (isset($entity_list) && is_array($entity_list))
			{
				foreach($entity_list as $entity_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['value'] 	= ($entity_entry[$uicols['name'][$i]] == null ? '' : $entity_entry[$uicols['name'][$i]]);
						$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];

						if(isset($uicols['datatype']) && isset($uicols['datatype'][$i]) && $uicols['datatype'][$i]=='link' && $entity_entry[$uicols['name'][$i]])
						{
							$datatable['rows']['row'][$j]['column'][$i]['format']	= 'link';
							$datatable['rows']['row'][$j]['column'][$i]['value']	= lang('link');
							$datatable['rows']['row'][$j]['column'][$i]['link']		= $entity_entry[$uicols['name'][$i]];
							$datatable['rows']['row'][$j]['column'][$i]['target']	= '_blank';
						}
					}

					/*for ($i=0;$i<count($input_name);$i++)
					{
						$datatable['rows']['row'][$j]['hidden'][$i]['value'] 	= $entity_entry[$input_name[$i]];
						$datatable['rows']['row'][$j]['hidden'][$i]['name'] 	= $input_name[$i];
					}*/
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;

					if($uicols['name'][$i]=='loc1' || $uicols['name'][$i]=='num')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
					}
				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']		= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}

			$function_exchange_values = '';

			for ($i=0;$i<count($input_name);$i++)
			{
				$function_exchange_values .= "opener.document.getElementsByName('{$input_name[$i]}')[0].value = '';\r\n";
			}

			for ($i=0;$i<count($input_name);$i++)
			{
				$function_exchange_values .= "opener.document.getElementsByName('{$input_name[$i]}')[0].value = data.getData('{$input_name[$i]}');\r\n";
			}

			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($entity_list);
			$datatable['pagination']['records_total'] 	= $boentity->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'num'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}


			//-- BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array(),
					'headers'			=> $uicols

				);

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='{$column['link']}' onclick='javascript:filter_data(this.id);'>{$column['value']}</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='{$column['link']}' target='_blank'>{$column['value']}</a>";
						}
						else
						{
							$json_row[$column['name']] = $column['value'];
						}
					}
					$json['records'][] = $json_row;
				}
			}

			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}

			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'),PHPGW_SERVER_ROOT . '/phpgwapi/templates/base');
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if($this->entity_id)
			{
				$entity 	= $boadmin_entity->read_single($this->entity_id,false);
				$appname	= $entity['name'];
			}
			if($this->cat_id)
			{
				$category = $boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$function_msg					= lang('lookup') . ' ' . $category['name'];
			}

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.entity.index', 'property' );

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');


//			$this->save_sessiondata();
		}


		// Returns locations for a control
		public function get_locations_for_control()
		{
			$control_id = phpgw::get_var('control_id');
			
			if(is_numeric($control_id) & $control_id > 0)
			{
				$locations_for_control_array = $this->so_control->get_locations_for_control($control_id);
			
				foreach($locations_for_control_array as $location)
				{
					$results['results'][]= $location;	
				}
				
				$results['total_records'] = count( $locations_for_control_array );
				$results['start'] = 1;
				$results['sort'] = 'location_code';
			}
			else
			{
				$results['total_records'] = 0;
			}				
			
			return $this->yui_results($results);
		}
		
		public function query()
		{
			$type_id = phpgw::get_var('type_id', 'int');
			$control_id = phpgw::get_var('control_id', 'int');
			$control_id_init = phpgw::get_var('control_id_init', 'int');
			$control_area_id = phpgw::get_var('control_area_id', 'int');

			$control_id = $control_id ? $control_id : $control_id_init;
			
			if($control_area_id && !execMethod('controller.socontrol.get_controls_by_control_area',$control_area_id))
			{
				$control_id = 0;
			}

			$control_info = execMethod('controller.socontrol.get_single', $control_id);
			$control_name = '';
			if($control_info)
			{
				$control_name = $control_info->get_title();
			}

			$view_type = phpgw::get_var('view_type');
			$return_results	= phpgw::get_var('results', 'int', 'REQUEST', 0);
			
			$type_id = $type_id ? $type_id : 1;
			
			$location_list = array();

			$this->bo->sort = "ASC";
			$this->bo->start = phpgw::get_var('startIndex');
			
			$location_list = $this->bo->read(array('user_id' => $user_id, 'role_id' =>$role_id, 'type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,
												   'lookup'=>$lookup,'allrows'=>$this->allrows,'dry_run' =>$dry_run, 'results' => $return_results));

			foreach($location_list as &$location)
			{
				$location['control_name'] = $control_name;
				$location['location_registered'] = !!$this->so_control->get_control_location($control_id, $location['location_code']);
				$results['results'][]= $location;	
			}
			
			$results['total_records'] = $this->bo->total_records;
			$results['start'] = $this->start;
			$results['sort'] = 'location_code';
			$results['dir'] = "ASC";
						
			array_walk($results['results'], array($this, 'add_links'), array($type));
							
			return $this->yui_results($results);
		}

		public function register_control_to_location_2()
		{
			$control_id = phpgw::get_var('control_id');
			$location_code = phpgw::get_var('location_code');
			
			$control_location  = null;
			$control_location_id = 0;
			
			$control_location = $this->so_control->get_control_location($control_id, $location_code);
			
			if($control_location == null ){
				
				$control_location_id = $this->so_control->register_control_to_location($control_id, $location_code);
			}
			
			if($control_location_id > 0)
				return json_encode( array( "status" => "saved" ) );
			else
				return json_encode( array( "status" => "not_saved" ) );
		}
		
		public function add_actions(&$value, $key, $params)
		{
			unset($value['query_location']);
			
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();
			$value['parameters'] = array();
/*			
			$value['ajax'][] = true;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uicontrol_location.register_control_to_location_2','location_code' => $value['location_code'], 'phpgw_return_as' => 'json')));
			$value['labels'][] = lang('add_location');
			$value['parameters'][] = "control_id";
			*/
		}
		
		/*
		 * Return categories based on chosen location
		 */
		public function get_location_category()
		{
			$type_id = phpgw::get_var('type_id');
		 	$category_types = $this->bocommon->select_category_list(array(
																		'format'=>'filter',
																		'selected' => 0,
																		'type' =>'location',
																		'type_id' =>$type_id,
																		'order'=>'descr'
																	));
			$default_value = array ('id'=>'','name'=>lang('no category selected'));
			array_unshift($category_types,$default_value);
			return json_encode( $category_types );
		}
		
		/*
		 * Return parts of town based on chosen district
		 */
		public function get_district_part_of_town()
		{
			$district_id = phpgw::get_var('district_id');
			$part_of_town_list =  $this->bocommon->select_part_of_town('filter',null,$district_id);
			$default_value = array ('id'=>'','name'=>lang('no part of town'));
			array_unshift($part_of_town_list,$default_value);

			return json_encode( $part_of_town_list );
		}


		/*

		 * Return parts of town based on chosen district
		 */
		public function get_category_by_entity()
		{
			$entity_id		= phpgw::get_var('entity_id');
			$entity			= CreateObject('property.soadmin_entity');

			$category_list = $entity->read_category(array('allrows'=>true,'entity_id'=>$entity_id));

/*			$default_value = array ('id'=>'','name'=>lang('select'));
			array_unshift($category_list,$default_value);
*/
			return $category_list;
		}

		function index2()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$receipt = array();

			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query2();
			}

			$msgbox_data = array();
			if( phpgw::get_var('phpgw_return_as') != 'json' && $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
			{
				phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			}

			$myColumnDefs = array();
			$datavalues = array();
			$myButtons	= array();

			$datavalues[] = array
			(
				'name'				=> "0",
				'values' 			=> $this->query2(),//json_encode(array()),
				'total_records'		=> 0,
				'permission'   		=> "''",
				'is_paginator'		=> 0,
				'edit_action'		=> "''",
				'footer'			=> 0
			);

			$datatable = array
			(
				array
				(
				'key' => 'id',
				'hidden' => true
				),
				array
				(
					'key' => 'user',
					'label' => lang('user'),
					'sortable' => false
				),
				array
				(
					'key' => 'ecodimb',
					'label' => lang('dim b'),
					'sortable' => false,
					'formatter' => 'FormatterRight',
				),
				array
				(
					'key'	=>	'role',
					'label'	=>	lang('role'),
					'formatter' => 'FormatterRight',
					'sortable'	=>	true
				),
				array
				(
					'key' => 'default_user',
					'label' => lang('default'),
					'sortable'	=> false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key' => 'active_from',
					'label' => lang('date from'),
					'sortable'	=> true,
					'formatter' => 'FormatterRight',
				),
				array
				(
					'key' => 'active_to',
					'label' => lang('date to'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key' => 'add',
					'label' => lang('add'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key' => 'delete',
					'label' => lang('delete'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key' => 'alter_date',
					'label' => lang('alter_date'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
			);

			$myColumnDefs[0] = array
			(
				'name'		=> "0",
				'values'	=>	json_encode($datatable)
			);	



			$GLOBALS['phpgw']->translation->add_app('property');
			$entity			= CreateObject('property.soadmin_entity');
			$entity_list 	= $entity->read(array('allrows' => true));

			$district_list  = $this->bocommon->select_district_list('filter',$this->district_id);

			$part_of_town_list = execMethod('property.bogeneric.get_list', array('type'=>'part_of_town', 'selected' => $part_of_town_id ));

			array_unshift($entity_list ,array ('id'=>'','name'=>lang('select')));
			array_unshift($district_list ,array ('id'=>'','name'=>lang('select')));



			array_unshift ($role_list ,array ('id'=>'','name'=>lang('select')));
			array_unshift ($dimb_list ,array ('id'=>'','name'=>lang('select')));



			$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;

			$control_area = $cats->formatted_xslt_list(array('format'=>'filter','globals' => true,'use_acl' => $this->_category_acl));

								
			$control_area_list = array();
			foreach($control_area['cat_list'] as $cat_list)
			{
				$control_area_list[] = array
				(
					'id' 	=> $cat_list['cat_id'],
					'name'	=> $cat_list['name'],
				);		
			}

			array_unshift ($control_area_list ,array ('id'=>'','name'=>lang('select')));

			$data = array
			(
				'td_count'						=> '""',
				'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'datatable'						=> $datavalues,
				'myColumnDefs'					=> $myColumnDefs,
				'myButtons'						=> $myButtons,

				'msgbox_data'					=> $msgbox_data,
				'filter_form' 					=> array
													(
														'control_area_list'	=> array('options' => $control_area_list),
														'entity_list' 		=> array('options' => $entity_list),
														'district_list' 	=> array('options' => $district_list),
														'part_of_town_list'	=> array('options' => $part_of_town_list),
													),
				'update_action'					=> self::link(array('menuaction' => 'controller.uicontrol_location.edit'))
			);

			$GLOBALS['phpgw']->jqcal->add_listener('query_start');
			$GLOBALS['phpgw']->jqcal->add_listener('query_end');
			$GLOBALS['phpgw']->jqcal->add_listener('active_from');
			$GLOBALS['phpgw']->jqcal->add_listener('active_to');

			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			phpgwapi_jquery::load_widget('core');

			self::add_javascript('controller', 'controller', 'ajax_control_to_component.js');
			self::add_javascript('controller', 'yahoo', 'register_control_to_component.js');

			$GLOBALS['phpgw']->xslttpl->add_file(array('control_location/register_control_to_component'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('data' => $data));
		}
	

		public function query2()
		{
			$user_id =	phpgw::get_var('user_id', 'int');
			$dimb_id =	phpgw::get_var('dimb_id', 'int');
			$role_id =	phpgw::get_var('role_id', 'int');
			$query_start =	phpgw::get_var('query_start');
			$query_end =	phpgw::get_var('query_end');

//			$this->bo->allrows = true;
			$values = $this->bo->read(array('user_id' => $user_id, 'dimb_id' => $dimb_id, 'role_id' => $role_id, 'query_start' => $query_start, 'query_end' => $query_end));

			foreach($values as &$entry)
			{
				if($entry['active_from'])
				{
					$default_user_checked = $entry['default_user'] == 1 ? 'checked = "checked"' : '';
					$entry['default_user'] = "<input id=\"default_user\" type =\"checkbox\" $default_user_checked name=\"values[default_user][]\" value=\"{$entry['id']}\">";
					$entry['delete'] = "<input id=\"delete\" type =\"checkbox\" name=\"values[delete][]\" value=\"{$entry['id']}\">";
					$entry['alter_date'] = "<input id=\"alter_date\" type =\"checkbox\" name=\"values[alter_date][]\" value=\"{$entry['id']}\">";
					$entry['add'] = '';
				}
				else
				{
					$entry['default_user'] = '';
					$entry['delete'] = '';
					$entry['alter_date'] = '';
					$entry['add'] = "<input id=\"add\" type =\"checkbox\" name=\"values[add][]\" value=\"{$entry['ecodimb']}_{$entry['role_id']}_{$entry['user_id']}\">";				
				}
				$results['results'][]= $entry;
			}

			return json_encode($values);
		}

		public function edit()
		{
			$user_id =	phpgw::get_var('user_id', 'int');
			$dimb_id =	phpgw::get_var('dimb_id', 'int');
			$role_id =	phpgw::get_var('role_id', 'int');
			$query =	phpgw::get_var('query');

			if($values = phpgw::get_var('values'))
			{
				if(!$GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_EDIT, 'property'))
				{
					$receipt['error'][]=true;
					phpgwapi_cache::message_set(lang('you are not approved for this task'), 'error');
				}
				if(!$receipt['error'])
				{
					if($this->bo->edit($values))
					{
						$result =  array
						(
							'status'	=> 'updated'
						);
					}
					else
					{
						$result =  array
						(
							'status'	=> 'error'
						);
					}
				}
			}

			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				if( $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
				{
					phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
					$result['receipt'] = $receipt;
				}
				else
				{
					$result['receipt'] = array();
				}
				return $result;
			}
			else
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_location.index2', 'user_id' => $user_id, 'dimb_id' => $dimb_id, 'role_id' => $role_id, 'query' => $query));
			}
		}
	}
