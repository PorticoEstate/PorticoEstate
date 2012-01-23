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

	phpgw::import_class('controller.uicommon');
	phpgw::import_class('property.boevent');
	phpgw::import_class('controller.socontrol');
	phpgw::import_class('controller.socontrol_group');
	phpgw::import_class('controller.socontrol_area');
	phpgw::import_class('controller.socontrol_item');
	phpgw::import_class('controller.socontrol_item_list');
	phpgw::import_class('controller.soprocedure');
	
	include_class('controller', 'control', 'inc/model/');
	include_class('controller', 'control_area', 'inc/model/');
	include_class('controller', 'control_item_list', 'inc/model/');
	include_class('controller', 'control_group_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	
	class controller_uicontrol extends controller_uicommon
	{
		private $bo;
		private $so;
		private $so_procedure;
		private $so_control_group;
		private $so_control_area; 
		private $so_control_item;
		private $so_control_item_list;
		private $so_control_group_list;
		private $so_check_list_list;
		private $so_check_item;
		private $_category_acl;		

		public $public_functions = array
		(
			'index'	=>	true,
			'control_list'	=>	true,
			'view'	=>	true,
			'view_control_details'				=>	true,
			'save_control_details'				=>	true,
			'view_control_groups'				=>	true,
			'save_control_groups'				=>	true,
			'view_control_items'				=>	true,
			'save_control_items'				=>	true,
			'view_check_list'					=>	true,
			'generate_check_lists_for_control'	=>	true,
			'view_check_lists_for_control'		=>	true,
			'get_controls_by_control_area'		=>	true,
			'get_locations_for_control'			=>	true,
			'add_location_to_control'			=>	true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so = CreateObject('controller.socontrol');
			$this->bo = CreateObject('property.boevent',true);
			$this->so_procedure = CreateObject('controller.soprocedure');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->so_control_area = CreateObject('controller.socontrol_area');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_control_item_list = CreateObject('controller.socontrol_item_list');
			$this->so_control_group_list = CreateObject('controller.socontrol_group_list');
			$this->so_check_list = CreateObject('controller.socheck_list');
			$this->so_check_item = CreateObject('controller.socheck_item');
			
			$config	= CreateObject('phpgwapi.config','controller');
			$config->read();
			$this->_category_acl = isset($config->config_data['acl_at_control_area']) && $config->config_data['acl_at_control_area'] == 1 ? true : false;

			self::set_active_menu('controller::control');
		}
		
		public function control_list()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			self::add_javascript('controller', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			// Sigurd: Start categories
			$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;

			$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => $control_area_id,'globals' => true,'use_acl' => $this->_category_acl));
			array_unshift($control_areas['cat_list'],array ('cat_id'=>'','name'=> lang('select value')));
			$control_areas_array2 = array();
			foreach($control_areas['cat_list'] as $cat_list)
			{
				$control_areas_array2[] = array
				(
					'id' 	=> $cat_list['cat_id'],
					'name'	=> $cat_list['name'],
				);		
			}
			// END categories

			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter', 
								'name' => 'status',
                                'text' => lang('Status'),
                                'list' => array(
                                    array(
                                        'id' => 'none',
                                        'name' => lang('Not selected')
                                    ), 
                                    array(
                                        'id' => 'NEW',
                                        'name' => lang('NEW')
                                    ), 
                                    array(
                                        'id' => 'PENDING',
                                        'name' =>  lang('PENDING')
                                    ), 
                                    array(
                                        'id' => 'REJECTED',
                                        'name' => lang('REJECTED')
                                    ), 
                                    array(
                                        'id' => 'ACCEPTED',
                                        'name' => lang('ACCEPTED')
                                    )
                                )
                            ),
							array('type' => 'filter',
								'name' => 'control_areas',
                                'text' => lang('Control_area'),
                                'list' => $this->so_control_area->get_control_area_select_array(),
							),
							//as categories
							array('type' => 'filter',
								'name' => 'control_areas',
								'text' => lang('Control_area') . 2,
								'list' => $control_areas_array2,
							),
							array('type' => 'filter',
								'name' => 'responsibilities',
                                'text' => lang('Responsibility'),
                                'list' => $this->so->get_roles(),
							),
							array('type' => 'text', 
                                'text' => lang('searchfield'),
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix.'.toggle_show_inactive'))
							),
							array(
								'type' => 'link',
								'value' => lang('New control'),
								'href' => self::link(array('menuaction' => 'controller.uicontrol.view_control_details')),
								'class' => 'new_item'
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'controller.uicontrol.control_list', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable'	=> true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key'	=>	'title',
							'label'	=>	lang('Control title'),
							'sortable'	=>	false
						),
						array(
							'key' => 'description',
							'label' => lang('description'),
							'sortable'	=> false
						),
						array(
							'key' => 'control_area_name',
							'label' => lang('Control area'),
							'sortable'	=> false
						),
						array(
							'key' => 'responsibility_name',
							'label' => lang('Responsibility'),
							'sortable'	=> false
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);
//_debug_array($data);

			self::render_template_xsl('datatable', $data);
		}
		
		public function view_control_details()
		{
			$control_id = phpgw::get_var('id');
		
			if(isset($control_id) && $control_id > 0)
			{
				$control = $this->so->get_single($control_id);
			}

			// Sigurd: START as categories
			$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;

			$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => $control->get_control_area_id(),'globals' => true,'use_acl' => $this->_category_acl));
			
			$control_areas_array2 = array();
			foreach($control_areas['cat_list'] as $cat_list)
			{
				$control_areas_array2[] = array
				(
					'id' 	=> $cat_list['cat_id'],
					'name'	=> $cat_list['name'],
				);		
			}
			// END as categories
		
			$procedures_array = $this->so_procedure->get_procedures_by_control_area($control->get_control_area_id());
			$role_array = $this->so->get_roles();
			
			$repeat_type_array = array(
									array('id' 	=> "0", 'value'	=> "Dag"),
									array('id' 	=> "1", 'value'	=> "Uke"),
									array('id' 	=> "2", 'value'	=> "Måned"),
									array('id' 	=> "3", 'value'	=> "År")
								);
								
			if( $control != null )
			{
				$tabs = array(
						array(
							'label' => "1: " . lang('Details'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_details', 
																				   'id' => $control->get_id()))
						),array(
							'label' => "2: " . lang('Choose_control_groups'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_groups', 
																				   'control_id' => $control->get_id())) 
						),array(
							'label' => "3: " . lang('Choose_control_items'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_items', 
																				   'control_id' => $control->get_id()))
						),array('label' => "4: " . lang('Sort_check_list')
						)
						
					);	
			}
			else
			{
				$tabs = array( array(
							'label' => "1: " . lang('Details')
						), array(
							'label' => "2: " . lang('Choose_control_groups')
						), array(
							'label' => "3: " . lang('Choose_control_items')
						), array(
							'label' => "4: " . lang('Sort_check_list')
						));	
			}
			
			$data = array
			(
				'tabs'						=> $GLOBALS['phpgw']->common->create_tabs($tabs, 0),
				'view'						=> "control_details",
				'editable' 					=> true,
				'control'					=> $control->toArray(),
				'control_areas_array2'		=> $control_areas_array2,
				'procedures_array'			=> $procedures_array,
				'role_array'				=> $role_array,
				'repeat_type_array'			=> $repeat_type_array
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl(array('control/control_tabs', 'control/control'), $data);
			
			$this->use_yui_editor(array('description'));
		}
		
		public function save_control_details(){
			$control_id = phpgw::get_var('control_id');		
			
			// Update control details
			if(isset($control_id) && $control_id > 0 )
			{
				$control = $this->so->get_single($control_id);
			}
			// Add details for control
			else {
				$control = new controller_control();
			}
			
			$control->populate();
									
			if( $control->get_title() == "" ){
				$this->redirect(array('menuaction' => 'controller.uicontrol.view_control_details', 'control_id'=>$control_id, 'control_area_id'=>$control->get_control_area_id()));	
			}
			else
			{
				$control_id = $this->so->store($control);
				$this->redirect(array('menuaction' => 'controller.uicontrol.view_control_groups', 'control_id'=>$control_id, 'control_area_id'=>$control->get_control_area_id()));
			}
		}
						
		// Displays control groups based on previously chosen control area
		public function view_control_groups(){
			$control_id = phpgw::get_var('control_id');
			$control = $this->so->get_single($control_id);	
									
			// Fetches saved control groups from db
			$saved_control_groups = $this->so_control_group_list->get_control_groups_by_control($control_id);
			$saved_control_group_ids = array();
			
			foreach($saved_control_groups as $control_group){
				$saved_control_group_ids[] = $control_group->get_id();
			}
			
			// Fetches control groups based on selected control area						
			$control_area = $this->so_control_area->get_single( $control->get_control_area_id );		
			$control_groups_as_array = $this->so_control_group->get_control_groups_as_array($control->get_control_area_id());
			
			$control_groups = array();
			foreach($control_groups_as_array as $control_group){
				$control_group_id = $control_group['id'];
				
				if( in_array($control_group_id, $saved_control_group_ids )){
					$control_groups[] = array("checked" => 1, "control_group" => $control_group);
				}
				else
				{
					$control_groups[] = array("checked" => 0, "control_group" => $control_group);
				}
			}
			
			$tabs = array(
						array(
							'label' => "1: " . lang('Details'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_details', 
																				   'view' => "view_control_details", 'id' => $control_id))
						), 
						array(
							'label' => "2: " . lang('Choose_control_groups')
						), 
						array(
							'label' => "3: " . lang('Choose_control_items')
						), 
						array(
							'label' => "4: " . lang('Sort_check_list')
						));
			
			$data = array
			(
				'tabs'							=> $GLOBALS['phpgw']->common->create_tabs($tabs, 1),
				'view'							=> "control_groups",
				'editable' 						=> true,
				'control'						=> $control->toArray(),
				'control_area'					=> $control_area->toArray(),
				'control_groups'				=> $control_groups,
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::render_template_xsl(array('control/control_tabs', 'control_group/control_groups'), $data);
		}
		
		public function save_control_groups(){
			$control_id = phpgw::get_var('control_id');
			$control_group_ids = phpgw::get_var('control_group_ids');		

			// Deleting earlier saved control groups
			$this->so_control_group_list->delete_control_groups($control_id);
			
			$group_order_nr = 1;

			// Saving control groups 
			foreach ($control_group_ids as $control_group_id)
			{
				$control_group_list = new controller_control_group_list();
				$control_group_list->set_control_id($control_id);
				$control_group_list->set_control_group_id($control_group_id);
				$control_group_list->set_order_nr($group_order_nr);
							
				$this->so_control_group_list->add($control_group_list);
				$group_order_nr++;
			}

			// Redirect: view_control_items
			$this->redirect(array('menuaction' => 'controller.uicontrol.view_control_items', 
								  'control_id'=>$control_id, 'control_group_ids'=>$control_group_ids));	
		}
		
		// Gets a comma separated list of control groups, and displays control items for these groups
		public function view_control_items(){
			$control_id = phpgw::get_var('control_id', 'int');
			$control = $this->so->get_single($control_id);
			
			$control_group_ids = phpgw::get_var('control_group_ids');
		
			// Fetches saved control items from db
			$saved_control_items = $this->so_control_item->get_control_items_by_control($control_id);
			$saved_control_item_ids = array();
			
			foreach($saved_control_items as $control_item){
				$saved_control_item_ids[] = $control_item->get_id();
			}
			
			// Array with selected control groups and items
			$groups_with_control_items = array();
			
			// Fetches control items for control group and populates groups_with_control_items with groups and chosen control items
			foreach ($control_group_ids as $control_group_id)
			{	
				$group_control_items_array = $this->so_control_item->get_control_items($control_group_id, "return_array");
				
				$control_items_for_group_array = array();
				
				foreach($group_control_items_array as $control_item){
					$control_item_id = $control_item['id'];
					
					if( in_array($control_item_id, $saved_control_item_ids )){
						$control_items_for_group_array[] = array("checked" => 1, "control_item" => $control_item);
					}
					else
					{
						$control_items_for_group_array[] = array("checked" => 0, "control_item" => $control_item);
					}
				}
				
				$control_group = $this->so_control_group->get_single($control_group_id);
				
				$groups_with_control_items[] = array("control_group" => $control_group->toArray(), "group_control_items" => $control_items_for_group_array);
			}			
			
			//print_r( $groups_with_control_items[0] );
			
			$tabs = array(
						array(
							'label' => "1: " . lang('Details'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_details', 'view' => "view_control_details", 
																				   'id' => $control_id))
						), 
							array(
							'label' => "2: " . lang('Choose_control_groups'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_groups', 'view' => "view_control_groups", 
																			       'control_id' => $control_id, 'control_group_ids' => $control_group_ids, 
																			       'control_area_id' => $control->get_control_area_id()))
						),
						array('label' => "3: " . lang('Choose_control_items')),
						array(
							'label' => "4: " . lang('Sort_check_list')
						));
					
			$data = array
			(
				'tabs'						=> $GLOBALS['phpgw']->common->create_tabs($tabs, 2),
				'view'						=> 'control_items',
				'control_group_ids'			=> implode($control_group_ids, ","),
				'control'				    => $control->toArray(),
				'groups_with_control_items'	=> $groups_with_control_items			
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::render_template_xsl(array('control/control_tabs', 'control_item/choose_control_items'), $data); 
		}
		
		// Saves chosen control items through receiving a comma separated list of control tags (1:2, control_group_id:control_item_id) 
		public function save_control_items(){
			$control_id = phpgw::get_var('control_id');
			$control_group_ids = explode(",", phpgw::get_var('control_group_ids'));
			
			// Fetching selected control items. Tags are on the format 1:2 (group:item). 
			$control_tag_ids = phpgw::get_var('control_tag_ids');
			
			// Deleting earlier saved control groups
			$this->so_control_group_list->delete_control_groups($control_id);
			
			// Deleting earlier saved control items
			$this->so_control_item_list->delete_control_items($control_id);
						
			$group_order_nr = 1;
			
			// Saving control groups 
			foreach ($control_group_ids as $control_group_id)
			{
				$control_group_list = new controller_control_group_list();
				$control_group_list->set_control_id($control_id);
				$control_group_list->set_control_group_id($control_group_id);
				$control_group_list->set_order_nr($group_order_nr);
							
				$this->so_control_group_list->add($control_group_list);
				$group_order_nr++;
			}

			// Saving control items if submit save control items is clicked 
			foreach ($control_tag_ids as $control_item_tag)
			{	
				// Fetch control_item_id from tag string
				$control_item_id = substr($control_item_tag, strpos($control_item_tag, ":")+1, strlen($control_item_tag));
							
				// Saves control item
				$control_item_list = new controller_control_item_list();
				$control_item_list->set_control_id($control_id);
				$control_item_list->set_control_item_id($control_item_id);
				$this->so_control_item_list->add($control_item_list);
			}	
	
			$this->redirect(array('menuaction' => 'controller.uicontrol.view_check_list', 'control_id'=>$control_id, 'control_title'=>$control_title, 'control_group_ids'=>$control_group_ids, 'control_tag_ids'=>$control_tag_ids ));	
		}
		
		public function view_check_list(){
			$control_id = phpgw::get_var('control_id');
			$control = $this->so->get_single($control_id);
			
			$control_group_ids = phpgw::get_var('control_group_ids');
			
			// Fetching selected control tag items. Tags are on the format 1:2 (control_group_id:control_item_id) 
			$control_tag_ids = phpgw::get_var('control_tag_ids');
			
			$control_group_ids = array();
			
			// Putting control_group_ids in array control_group_ids
			foreach ($control_tag_ids as $control_tag)
			{	
				// Fetching group id from tag	
				$control_group_id = substr($control_tag, 0, strpos($control_tag, ":"));

				if(!in_array($control_group_id, $control_group_ids))
					$control_group_ids[] = $control_group_id;  
			}
			
			$saved_groups_with_items_array = array();
			
			//Populating array with saved control items for each group
			foreach ($control_group_ids as $control_group_id)
			{	
				$saved_control_items = $this->so_control_item->get_control_items_by_control_and_group($control_id, $control_group_id);
				
				$control_group = $this->so_control_group->get_single($control_group_id);
				
				$control_item = $this->so_control_item->get_single($control_item_id);
				
				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}
			
			$tabs = array(
						array(
							'label' => "1: " . lang('Details'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_details', 
																				   'view' => "view_control_details", 'id' => $control_id))
						),array(
							'label' => "2: " . lang('Choose_control_groups'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_groups', 
																				   'view' => "view_control_groups", 'control_id' => $control_id, 
																				   'control_group_ids' => $control_group_ids, 
																				   'control_area_id' => $control->get_control_area_id()))
						),array(
							'label' => "3: " . lang('Choose_control_items'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_items', 
																				   'view' => "view_control_items", 'control_id' => $control_id, 
																				   'control_group_ids' => $control_group_ids))
						),array('label' => "4: " . lang('Sort_check_list')
						)
						
					);
			
			$data = array
			(
				'tabs'					=> $GLOBALS['phpgw']->common->create_tabs($tabs, 3),
				'view'					=> "sort_check_list",
				'control'				=> $control->toArray(),
				'saved_groups_with_items_array'	=> $saved_groups_with_items_array
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'yui_min_3_4_3.js');
			self::add_javascript('controller', 'controller', 'custom_drag_drop.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::render_template_xsl(array('control/control_tabs', 'control_item/sort_check_list'), $data);
		}
		
		
		public function generate_check_lists_for_control(){
			$control_id = phpgw::get_var('control_id');
			$control = $this->so->get_single($control_id);

			$start_date = $control->get_start_date();
			$end_date = $control->get_end_date();
			$repeat_type = $control->get_repeat_type();
			$repeat_interval = $control->get_repeat_interval();
			
			$status = FALSE;
			$comment = "Kommentar for sjekkliste";
			$deadline = $start_date;
			
			// Saving check_list
			$new_check_list = new controller_check_list();
			$new_check_list->set_control_id( $control_id );
			$new_check_list->set_status( $status );
			$new_check_list->set_comment( $comment );
			$new_check_list->set_deadline( $deadline );
			
			$check_list_id = $this->so_check_list->store( $new_check_list );
			
			$control_items_list = $this->so_control_item->get_control_items_by_control($control_id);
			
			foreach($control_items_list as $control_item){
				
				$status = '0';
				$comment = "Kommentar for sjekk item";
				
				// Saving check_items for a list
				$new_check_item = new controller_check_item();
				$new_check_item->set_check_list_id( $check_list_id );
				
				$new_check_item->set_control_item_id( $control_item->get_id() );
				$new_check_item->set_status( $status );
				$new_check_item->set_comment( $comment );
								
				$saved_check_item = $this->so_check_item->store( $new_check_item );
			}	
			
			$this->redirect(array('menuaction' => 'controller.uicontrol.view_check_lists_for_control', 'control_id'=>$control_id ));
		}
		
		/* Kommentert ut av Torstein 21.01.2012 - Tror ikke vi bruker denne... Kanskje den bør slettes...
		 * 
		public function view_check_lists_for_control()
		{
			$control_id = phpgw::get_var('control_id');
			$control = $this->so->get_single($control_id);
			
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		
			$check_list_array = $this->so_check_list->get_check_lists_for_control( $control_id );	
			
			$tabs = array(
						array(
							'label' => "1: " . lang('Details'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_details', 
																				   'view' => "view_control_details", 'id' => $control_id))
						),array(
							'label' => "2: " . lang('Choose_control_groups'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_groups', 
																				   'view' => "view_control_groups", 'control_id' => $control_id, 
																				   'control_group_ids' => $control_group_ids, 
																				   'control_area_id' => $control->get_control_area_id()))
						),array(
							'label' => "3: " . lang('Choose_control_items'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_items', 
																				   'view' => "view_control_items", 'control_id' => $control_id, 
																				   'control_group_ids' => $control_group_ids))
						),array('label' => "4: " . lang('Sort_check_list')
						)
					);
			
			$data = array
			(
				'tabs'				=> $GLOBALS['phpgw']->common->create_tabs($tabs, 4),
				'view'				=> "view_check_lists",
				'control_as_array'	=> $control->toArray(),
				'check_list_array'	=> $check_list_array,
				'date_format' 		=> $date_format
			);
			
			self::render_template_xsl(array('control/control_tabs', 'tab_view_check_lists'), $data);
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
		}
		*/	
		
		// Returns control list info as JSON
		public function get_controls_by_control_area()
		{
			$control_area_id = phpgw::get_var('control_area_id');
			
			$controls_array = $this->so->get_controls_by_control_area($control_area_id);
			
			if(count($controls_array)>0)
				return json_encode( $controls_array );
			else
				return null;
		}
		
		// Returns locations for a control
		public function get_locations_for_control()
		{
			$control_id = phpgw::get_var('control_id');
			$locations_for_control_array = $this->so->get_locations_for_control($control_id);
			
			foreach($locations_for_control_array as $location)
			{
				$results['results'][]= $location;	
			}
			
			$results['total_records'] = count( $locations_for_control_array );
			$results['start'] = 1;
			$results['sort'] = 'location_code';
						
			array_walk($results['results'], array($this, 'add_actions'), array($type));
							
			return $this->yui_results($results);
		}
		
		public function add_actions(&$value, $key, $params)
		{
			unset($value['query_location']);
			
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();
			$value['parameters'] = array();
			
			$value['ajax'][] = false;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uicheck_list_for_location.add_check_list', 'location_code' => $value['location_code'])));
			$value['labels'][] = lang('add_check_list_to_location');
			$value['parameters'][] = "control_id";
		}
		
		public function add_location_to_control()
		{
			$control_id = phpgw::get_var('control_id');
			$location_code = phpgw::get_var('location_code');
			
			$this->so->add_location_to_control($control_id, $location_code);
		}
		
		public function query()
		{
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', null),
				'query'	=> phpgw::get_var('query'),
				'sort'	=> phpgw::get_var('sort'),
				'dir'	=> phpgw::get_var('dir'),
				'filters' => $filters
			);
			
			$ctrl_area = phpgw::get_var('control_areas');
			if(isset($ctrl_area) && $ctrl_area > 0)
			{
				$filters['control_areas'] = $ctrl_area; 
			}
			$responsibility = phpgw::get_var('responsibilities');
			if(isset($responsibility) && $responsibility > 0)
			{
				$filters['responsibilities'] = $responsibility; 
			}
										
			$search_for = phpgw::get_var('query');

			if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else {
				$user_rows_per_page = 10;
			}
			
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
			$sort_field		= phpgw::get_var('sort');
			if($sort_field == null)
			{
				$sort_field = 'control_group_id';
			}
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			//Create an empty result set
			$records = array();
			
			//Retrieve a contract identifier and load corresponding contract
			$control_id = phpgw::get_var('control_id');
			if(isset($control_id))
			{
				$control = $this->so->get_single($control_id);
			}

			$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$object_count = $this->so->get_count($search_for, $search_type, $filters);
			//var_dump($result_objects);
								
			$results = array();
			
			foreach($result_objects as $control_obj)
			{
				$results['results'][] = $control_obj->serialize();	
			}
			
			$results['total_records'] = $object_count;
			$results['start'] = $params['start'];
			$results['sort'] = $params['sort'];
			$results['dir'] = $params['dir'];

			array_walk($results["results"], array($this, "_add_links"), "controller.uicontrol.view_control_details");

			return $this->yui_results($results);
		}
	}
