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

	/**
	* Import the jQuery class
	*/
	phpgw::import_class('phpgwapi.jquery');

  phpgw::import_class('phpgwapi.uicommon');
  phpgw::import_class('property.boevent');
  phpgw::import_class('controller.socontrol');
  phpgw::import_class('controller.socontrol_group');
  phpgw::import_class('controller.socontrol_item');
  phpgw::import_class('controller.socontrol_item_list');
  phpgw::import_class('controller.soprocedure');
	
  phpgw::import_class('phpgwapi.yui');
	
  include_class('controller', 'control', 'inc/model/');
  include_class('controller', 'control_item_list', 'inc/model/');
  include_class('controller', 'control_group_list', 'inc/model/');
  include_class('controller', 'check_item', 'inc/model/');
	
  class controller_uicontrol extends phpgwapi_uicommon
  {
    private $bo;
    private $so;
    private $so_procedure;
    private $so_control_group; 
    private $so_control_item;
    private $so_control_item_list;
    private $so_control_group_list;
    private $so_check_item;
    private $_category_acl;		

    private $read;
    private $add;
    private $edit;
    private $delete;

    public $public_functions = array
    (
			'index'							=>	true,
			'control_list'					=>	true,
			'view'							=>	true,
			'view_control_details'			=>	true,
			'save_control_details'			=>	true,
			'view_control_groups'			=>	true,
			'save_control_groups'			=>	true,
			'view_control_items'			=>	true,
			'save_control_items'			=>	true,
			'view_check_list'				=>	true,
			'get_controls_by_control_area'	=>	true,
	    	'get_control_details'			=>	true
		);

		public function __construct()
		{
			parent::__construct('controller');

			$this->read    = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_READ, 'controller');//1 
			$this->add     = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_ADD, 'controller');//2 
			$this->edit    = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_EDIT, 'controller');//4 
			$this->delete  = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_DELETE, 'controller');//8 
			
			$this->manage  = $GLOBALS['phpgw']->acl->check('.control', 16, 'controller');//16

			//if(!$manage)
			
			$this->so = CreateObject('controller.socontrol');
			$this->bo = CreateObject('property.boevent',true);
			$this->so_procedure = CreateObject('controller.soprocedure');
			$this->so_control_group = CreateObject('controller.socontrol_group');
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
		

		/**
		 * Wrapper for control_list
		 *
		 * @return void
		 */

		public function index()
		{
			$this->control_list();
		}

		/**
		 * Fetches controls and returns to datatable 
		 *
		 * @param HTTP::phpgw_return_as	specifies how data should be returned
		 * @return data array
		 */
		public function control_list()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			self::add_javascript('phpgwapi', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			// Sigurd: Start categories
			$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;

			$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => $control_area_id,'globals' => true,'use_acl' => $this->_category_acl));
			array_unshift($control_areas['cat_list'],array ('cat_id'=>'','name'=> lang('select value')));
			$control_areas_array = array();
			foreach($control_areas['cat_list'] as $cat_list)
			{
				$control_areas_array[] = array
				(
					'id' 	=> $cat_list['cat_id'],
					'name'	=> $cat_list['name'],
				);		
			}
			// END categories

			// start district
			$property_bocommon		= CreateObject('property.bocommon');
			$district_list  = $property_bocommon->select_district_list('dummy',$this->district_id);
			array_unshift ($district_list,array ('id'=>'','name'=>lang('no district')));
			// end district


			$data = array(
				'datatable_name'	=> 'Kontroller',//lang('controls'),
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
							//as categories
							array('type' => 'filter',
								'name' => 'control_areas',
								'text' => lang('Control_area'),
								'list' => $control_areas_array,
							),
							array('type' => 'filter',
								'name' => 'responsibilities',
                                'text' => lang('Responsibility'),
                                'list' => $this->so->get_roles(),
							),
							array('type' => 'filter',
								'name' => 'district_id',
                                'text' => lang('district'),
                                'list' => $district_list,
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
							'sortable'	=>	true,
              'formatter' => 'YAHOO.portico.formatLink'
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
						),
						array(
							'key' => 'show_locations',
							'label' => '',
							'sortable' => false,
							'formatter' => 'YAHOO.portico.formatGenericLink'
						)
					)
				),
			);

			self::render_template_xsl(array( 'datatable_common' ), $data);
		}
		
		/**
		 * Fetches control details from db and returns to view 
		 *
		 * @param HTTP:: control id
		 * @return data array 
		 */
		public function view_control_details($control = null)
		{
			if($control == null)
			{
				$control_id = phpgw::get_var('id');
		
				if(isset($control_id) && $control_id > 0)
				{
					$control = $this->so->get_single($control_id);
				}
			}
			
			$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;
			
			$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','globals' => true,'use_acl' => $this->_category_acl));
			$control_areas_array = $control_areas['cat_list'];
		
			if($control != null)
			{
				$procedures_array = $this->so_procedure->get_procedures_by_control_area($control->get_control_area_id());
			}
			
			$role_array = $this->so->get_roles();
			
			$repeat_type_array = array(
									array('id' 	=> "0", 'value'	=> "Dag"),
									array('id' 	=> "1", 'value'	=> "Uke"),
									array('id' 	=> "2", 'value'	=> "Måned"),
									array('id' 	=> "3", 'value'	=> "År")
								);
				
			$tabs = $this->make_tab_menu($control_id);
			
			$data = array
			(
				'tabs'									=> $GLOBALS['phpgw']->common->create_tabs($tabs, 0),
				'view'									=> "control_details",
				'editable' 							=> true,
				'control'								=> ($control != null) ? $control : null,
				'control_areas_array'		=> $control_areas_array,
				'procedures_array'			=> $procedures_array,
				'role_array'						=> $role_array,
				'repeat_type_array'			=> $repeat_type_array
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
		
			$this->use_yui_editor(array('description'));
			$GLOBALS['phpgw']->jqcal->add_listener('start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('end_date');

			self::render_template_xsl(array('control/control_tabs', 'control/control'), $data);
		}
		
		/**
		 * Public function for saving control details 
		 *
		 * @param HTTP:: control id, control details fields
		 * @return redirect to function view_control_groups
		 */
		public function save_control_details()
		{
			if(!$this->add && !$this->edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'controller.uicontrol.index'));
			}

			$control_id = phpgw::get_var('control_id');
			
			// Update existing control details
			$delete_control_groups = false;
			if(isset($control_id) && $control_id > 0 )
			{
				$control = $this->so->get_single($control_id);
				$control_area_id_in_db = $control->get_control_area_id();
				$control->populate();
				$control_area_id_from_req = $control->get_control_area_id();
			
				// DELETE EARLIER SAVED CONTROL GROUPS
				// If control are is different from a previous registration - delete related groups 			
				if( ($control_area_id_in_db > 0) & ($control_area_id_in_db != $control_area_id_from_req) )
				{								
					$delete_control_groups = true;
				}
			}
			// Add control details
			else 
			{
				$control = new controller_control();
				$control->populate();
			}
		
			// SAVE CONTROL DETAILS
			if( $control->validate() )
			{
				if($delete_control_groups)
				{
					// Deleting earlier saved control groups
					$this->so_control_group_list->delete_control_groups($control_id);
					$saved_control_items = $this->so_control_item_list->get_control_items_by_control($control_id);
				
					foreach($saved_control_items as $control_item)
					{
						$this->so_control_item_list->delete($control->get_id(), $control_item->get_id());
					}
				}
				
				$control_id = $this->so->store($control);
				$this->redirect(array('menuaction' => 'controller.uicontrol.view_control_groups', 'control_id' => $control_id));	
			}
			else
			{
					$this->view_control_details($control);
			}
		}
						
		/**
		 * Public function for viewing control groups 
		 * Displays control groups by chosen control area  
		 *
		 * @param HTTP:: control id 
		 * @return data array 
		 */
		public function view_control_groups()
		{
			$control_id = phpgw::get_var('control_id');
			$control = $this->so->get_single($control_id);	
									
			// Fetches saved control groups from db
			$saved_control_groups = $this->so_control_group_list->get_control_groups_by_control($control_id);

			$saved_control_group_ids = array();
			
			foreach($saved_control_groups as $control_group)
			{
				$saved_control_group_ids[] = $control_group->get_id();
			}
			
			// Fetches control groups based on selected control area						
			$control_area = execMethod('phpgwapi.categories.return_single', $control->get_control_area_id());			
			$control_groups_as_array = $this->so_control_group->get_control_groups_as_array($control->get_control_area_id());
			
			$control_groups = array();
			foreach($control_groups_as_array as $control_group)
			{
				$control_group_id = $control_group['id'];
				
				if( in_array($control_group_id, $saved_control_group_ids ))
				{
					$control_groups[] = array("checked" => 1, "control_group" => $control_group);
				}
				else
				{
					$control_groups[] = array("checked" => 0, "control_group" => $control_group);
				}
			}
			
			$tabs = $this->make_tab_menu($control_id);
			
			$data = array
			(
				'tabs'				=> $GLOBALS['phpgw']->common->create_tabs($tabs, 1),
				'view'				=> "control_groups",
				'editable' 			=> true,
				'control'			=> $control,
				'control_area'		=> $control_area,
				'control_groups'	=> $control_groups,
			);
			
			phpgwapi_jquery::load_widget('core');
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::render_template_xsl(array('control/control_tabs', 'control_group/control_groups'), $data);
		}
		
		/**
		 * Public function for saving control groups. 
		 * 
		 * @param HTTP::id	the control_id, and a comma seperated list of group ids
		 * @return redirect to function view_control_items
		 */
		public function save_control_groups()
		{
			if(!$this->add && !$this->edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'controller.uicontrol.index'));
			}

			$control_id = phpgw::get_var('control_id');
			$control_group_ids = phpgw::get_var('control_group_ids');

			// Fetches saved control groups 
			$saved_control_groups = $this->so_control_group_list->get_control_groups_by_control($control_id);
			
			// Deletes groups from control that's not among the chosen groups

			foreach($saved_control_groups as $group)
			{
				// If saved group id not among chosen control ids, delete the group for the control    
				if( !in_array($group->get_id(), $control_group_ids) )
				{
					$this->so_control_group_list->delete($control_id, $group->get_id());
					// Deletes control items for group
					$this->so_control_item_list->delete_control_items_for_group_list($control_id, $group->get_id());
				}
			}

			$group_order_nr = 1;

			// Saving control groups 
			foreach ($control_group_ids as $control_group_id)
			{
				$control_group = $this->so_control_group_list->get_group_list_by_control_and_group($control_id, $control_group_id);
				
				if($control_group == null)
				{
					$control_group_list = new controller_control_group_list();
					$control_group_list->set_control_id($control_id);
					$control_group_list->set_control_group_id($control_group_id);
					$control_group_list->set_order_nr($group_order_nr);
							
					$this->so_control_group_list->add($control_group_list);
					$group_order_nr++;	
				}
			}

			// Redirect: view_control_items
			$this->redirect(array('menuaction' => 'controller.uicontrol.view_control_items', 'control_id'=>$control_id));	
		}
		
		/**
		 * Public function for viewing control items 
		 * 
		 * @param HTTP::id	the control_id
		 * @return redirect to function view_control_items
		*/
		public function view_control_items(){
			$control_id = phpgw::get_var('control_id', 'int');
			$control = $this->so->get_single($control_id);
			
			// Array with selected control groups and items
			$groups_with_control_items = array();
			
			$saved_control_groups = $this->so_control_group_list->get_control_groups_by_control($control_id);
			
			// Fetches control items for control group and populates groups_with_control_items with groups and chosen control items
			foreach ($saved_control_groups as $control_group)
			{	
				// Fetches control items for group
				$control_items_for_group = $this->so_control_item_list->get_control_items($control_group->get_id());
				
				// Fetches saved, ordered control items for group
				$saved_control_items_for_group = $this->so_control_item_list->get_control_items_by_control_and_group($control_id, $control_group->get_id(), "return_object");
				
				// Array that contains saved and unsaved control items for the group 
				$control_items_for_group_array = array();
				
				foreach($saved_control_items_for_group as $saved_control_item){
					$control_items_for_group_array[] = array("checked" => 1, "control_item" => $saved_control_item->toArray()); 
				}
				
				// Loops through all control items for the group and add those control items that is not saved 
				foreach($control_items_for_group as $control_item){
					$status_control_item_saved = false; 
					
					foreach($saved_control_items_for_group as $saved_control_item){
						if( $control_item->get_id() == $saved_control_item->get_id()){
							$status_control_item_saved = true;
						} 
					}
					
					// Adds control item to saved 
					if(!$status_control_item_saved){
						$control_items_for_group_array[] = array("checked" => 0, "control_item" => $control_item->toArray());
					}
				}
				
				$groups_with_control_items[] = array("control_group" => $control_group->toArray(), "group_control_items" => $control_items_for_group_array);
			}			
			
			$tabs = $this->make_tab_menu($control_id);
					
			$data = array
			(
				'tabs'											=> $GLOBALS['phpgw']->common->create_tabs($tabs, 2),
				'view'											=> 'control_items',
				'control_group_ids'					=> implode($control_group_ids, ","),
				'control'				    				=> $control,
				'groups_with_control_items'	=> $groups_with_control_items			
			);
			
			phpgwapi_jquery::load_widget('core');

			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::render_template_xsl(array('control/control_tabs', 'control_item/choose_control_items'), $data); 
		}
		
		/**
		 * Public function for saving control items 
		 * 
		 * @param HTTP::id	the control_id and a comma separated list of tags (1:2, control_group_id:control_item_id)
		 * @return redirect to function view_control_items
		 */ 
		public function save_control_items(){
			$control_id = phpgw::get_var('control_id');

			// Fetching selected control items. Tags are on the format 1:2 (group:item). 
			$control_tag_ids = phpgw::get_var('control_tag_ids');
			
			$saved_control_items = $this->so_control_item_list->get_control_items_by_control($control_id, "return_object");
			
			// Deleting formerly saved control items
			foreach ($saved_control_items as $saved_control_item)
			{
				$exists = false;
				$saved_control_item_id = $saved_control_item->get_id();
				  
				foreach ($control_tag_ids as $control_item_tag)
				{
					$control_item_id = substr($control_item_tag, strpos($control_item_tag, ":")+1, strlen($control_item_tag));
					
					if($control_item_id == $saved_control_item_id)
					{
						$exists = true;
					}
				}
				
				if($exists == false)
				{
					$exists = false;
					$status = $this->so_control_item_list->delete($control_id, $saved_control_item_id);
				}
			}
						
			$order_nr = 1;
			// Saving new control items 
			foreach ($control_tag_ids as $control_item_tag)
			{
				// Fetch control_item_id from tag string
				$control_item_id = substr($control_item_tag, strpos($control_item_tag, ":")+1, strlen($control_item_tag));
				
				$saved_control_list_item = $this->so_control_item_list->get_single_2($control_id, $control_item_id);
				
				if( $saved_control_list_item == null )
				{
					// Saves control item
					$control_item_list = new controller_control_item_list();
					$control_item_list->set_control_id($control_id);
					$control_item_list->set_control_item_id($control_item_id);
					$control_item_list->set_order_nr($order_nr);
					$this->so_control_item_list->add($control_item_list);
					
					$order_nr++;
				}
			}
			
			$this->redirect(array('menuaction' => 'controller.uicontrol.view_check_list', 'control_id'=>$control_id ));	
		}

		/**
		 * Public function for viewing chosen control items
		 * 
		 * @param HTTP::id the control_id
		 * @return data array 
		*/ 
		public function view_check_list(){
			$control_id = phpgw::get_var('control_id');
			$control = $this->so->get_single($control_id);
			
			// Fetches saved control groups from DB for this control 
			$saved_control_groups = $this->so_control_group_list->get_control_groups_by_control($control_id);
		
			$saved_groups_with_items_array = array();
			
			// Populating array with saved control items for each group
			foreach ($saved_control_groups as $control_group)
			{	
				// Fetches saved control items for group
				$saved_control_items = $this->so_control_item_list->get_control_items_by_control_and_group($control_id, $control_group->get_id());
				
				if(count($saved_control_items) > 0)				
					$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}
			
			$tabs = $this->make_tab_menu($control_id);
			
			$data = array
			(
				'tabs'													=> $GLOBALS['phpgw']->common->create_tabs($tabs, 3),
				'view'													=> "sort_check_list",
				'control'												=> $control,
				'saved_groups_with_items_array'	=> $saved_groups_with_items_array
			);
			
			phpgwapi_jquery::load_widget('core');

			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'custom_drag_drop.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::render_template_xsl(array('control/control_tabs', 'control_item/sort_check_list'), $data);
		}
		
		public function get_control_details()
		{
			$control_id = phpgw::get_var('control_id');
			$control = $this->so->get_single($control_id);

			$data = array
			(
				'control'	=> $control
			);
		  
			self::render_template_xsl('control/control_details', $data);
		}
		
		function make_tab_menu($control_id){
			$tabs = array();
			
			if($control_id > 0){
				
				$control = $this->so->get_single($control_id);
				
				$tabs[] = array(
							'label' => "1: " . lang('Details'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_details', 
																				   'id' => $control->get_id()))
						);
				
				$saved_control_groups = $this->so_control_group_list->get_control_groups_by_control( $control->get_id() );
				
				if(count($saved_control_groups) > 0)
				{
					$tabs[] = array(
								'label' => "2: " . lang('Choose_control_groups'),
								'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_groups', 
																					   'control_id' => $control->get_id())) 
							);
							
					$saved_control_items = $this->so_control_item_list->get_control_items_by_control( $control->get_id() );
					
					if(count($saved_control_items) > 0)
					{
						$tabs[] = array(
									'label' => "3: " . lang('Choose_control_items'),
									'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_items', 
																						   'control_id' => $control->get_id())));
						$tabs[] = array('label' => "4: " . lang('Sort_check_list'),
									'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_check_list', 
																						   'control_id' => $control->get_id()))); 
					}else{
						$tabs[] = array('label' => "3: " . lang('Choose_control_items'));
						$tabs[] = array('label' => "4: " . lang('Sort_check_list'));			 
					}
				}else{
					$tabs[] = array('label' => "2: " . lang('Choose_control_groups'));
					$tabs[] = array('label' => "3: " . lang('Choose_control_items'));
					$tabs[] = array('label' => "4: " . lang('Sort_check_list'));
				}
			}else{
				$tabs = array( 
						   array(
							'label' => "1: " . lang('Details')
						), array(
							'label' => "2: " . lang('Choose_control_groups')
						), array(
							'label' => "3: " . lang('Choose_control_items')
						), array(
							'label' => "4: " . lang('Sort_check_list')
						));
			}
			
			return $tabs;
		} 

		/**
		 * Public function for retrieving controls that has a certain control area  
		 * 
		 * @param HTTP:: control area id
		 * @return array of controls as json 
		*/ 
		public function get_controls_by_control_area()
		{
			$control_area_id = phpgw::get_var('control_area_id');
			
			$controls_array = $this->so->get_controls_by_control_area($control_area_id);
			
			if(count($controls_array)>0)
				return json_encode( $controls_array );
			else
				return null;
		}
		
		/**
		 * Public function for retrieving locations that is assigned to a control  
		 * 
		 * @param HTTP:: control id
		 * @return array of locations as json 
		*/
		public function get_locations_for_control()
		{
			$control_id = phpgw::get_var('control_id');
			
			if(is_numeric($control_id) & $control_id > 0)
			{
				$locations_for_control_array = $this->so->get_locations_for_control($control_id);
			
				foreach($locations_for_control_array as $location)
				{
					$results['results'][]= $location;	
				}
				
				$results['total_records'] = count( $locations_for_control_array );
				$results['start'] = 1;
				$results['sort'] = 'location_code';
							
				array_walk($results['results'], array($this, 'add_actions'), array($type));
			}
			else
			{
				$results['total_records'] = 0;
			}				
			
			return $this->yui_results($results);
		}
		
		/**
		 * Add data for context menu
		 *
		 * @param $value pointer to
		 * @param $key ?
		 * @param $params [type of query, editable]
		 */
		public function add_actions(&$value, $key, $params)
		{
			unset($value['query_location']);
			
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();
			
			$value['ajax'][] = false;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uicontrol.view_control_details', 'id' => $value['control_id'])));
			$value['labels'][] = lang('View control');
			
			$value['ajax'][] = false;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uicontrol.view_locations_for_control', 'id' => $value['control_id'])));
			$value['labels'][] = lang('View locations for control');
			
			$value['ajax'][] = false;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uicheck_list.add_check_list', 'location_code' => $value['location_code'])));
			$value['labels'][] = lang('add_check_list_to_location');
		}
		
		public function register_control_to_location()
		{
			$control_id = phpgw::get_var('control_id');
			$location_code = phpgw::get_var('location_code');
			
			$this->so->register_control_to_location($control_id, $location_code);
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

			$filters['district_id'] = phpgw::get_var('district_id', 'int', 'REQUEST', null);
										
			$search_for = phpgw::get_var('query');

			if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$user_rows_per_page = 10;
			}
			
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
			$sort_field		= phpgw::get_var('sort');
			
			if($sort_field == null)
			{
				$sort_field = 'controller_control.id';
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
			
			foreach($results["results"] as &$res) {
				$res['show_locations'] = array(
					'href' => self::link(array('menuaction' => 'controller.uicalendar.view_calendar_year_for_locations', 'control_id' => $res['id'])),
					'label' => lang('show_controls_for_location'),
				);
			}

			return $this->yui_results($results);
		}
	}
