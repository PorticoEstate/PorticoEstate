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
	phpgw::import_class('controller.socontrol_item');
	phpgw::import_class('controller.socontrol_item_list');
	phpgw::import_class('controller.socontrol_group_list');
	phpgw::import_class('controller.socontrol_area');

	include_class('controller', 'control_group', 'inc/model/');

	class controller_uicontrol_group extends controller_uicommon
	{
		private $so;
		private $so_procedure;
		private $so_control_area;
		private $so_control_item;
		private $so_control_item_list;
		private $so_control_group_list;

		public $public_functions = array
		(
			'index'	=>	true,
			'query'	=>	true,
			'edit'	=>	true,
			'view'	=>	true,
			'add'	=>	true,
			'save_group_order'	=>	true,
			'get_control_groups_by_control_area' => true
		);

		public function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('controller.socontrol_group');
			$this->so_procedure = CreateObject('controller.soprocedure');
			$this->so_control_area = CreateObject('controller.socontrol_area');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_control_item_list = CreateObject('controller.socontrol_item_list');
			$this->so_control_group_list = CreateObject('controller.socontrol_group_list');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::control_group";
		}

		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			
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
			self::add_javascript('controller', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'control_areas',
								'text' => lang('Control_area'),
								'list' => $control_areas_array2,
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
								'value' => lang('New control group'),
								'href' => self::link(array('menuaction' => 'controller.uicontrol_group.add')),
								'class' => 'new_item'
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'controller.uicontrol_group.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable'	=> true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key'	=>	'group_name',
							'label'	=>	lang('Control group title'),
							'sotrable'	=>	false
						),
						array(
							'key' => 'procedure',
							'label' => lang('Procedure'),
							'sortable'	=> false
						),
						array(
							'key' => 'building_part',
							'label' => lang('Building part'),
							'sortable'	=> false
						),
						array(
							'key' => 'control_area',
							'label' => lang('Control area'),
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

		/**
	 	* Public method. Forwards the user to edit mode.
	 	*/
		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_group.edit'));
		}


		public function edit()
		{
			$tabs = array
			(
				'control_group'		=> array('label' => lang('Control_group'), 'link' => '#control_group'),
				'control_items'		=> array('label' => lang('Control_items'), 'link' => '#control_items')
			);
			$tab_to_display = 'control_group';
			$control_group_id = phpgw::get_var('id');
			$new_control_group = false;
			if(isset($control_group_id) && $control_group_id > 0)
			{
				$control_group = $this->so->get_single($control_group_id);
			}
			else
			{
				$control_group = new controller_control_group();
				$new_control_group = true;
			}

			if(isset($_POST['save_control_group'])) // The user has pressed the save button
			{
				if(isset($control_group)) // Add new values to the control item
				{
					$control_group->set_group_name(phpgw::get_var('group_name'));
					$control_group->set_procedure_id( phpgw::get_var('procedure') );
					$control_group->set_control_area_id( phpgw::get_var('control_area') );
					$control_group->set_building_part_id( phpgw::get_var('building_part') );

					//$this->so->store($control_item);

					if(isset($control_group_id) && $control_group_id > 0)
					{
						$ctrl_group_id = $control_group_id;
						if($this->so->store($control_group))
						{
							$message = lang('messages_saved_form');
						}
						else
						{
							$error = lang('messages_form_error');
						}
					}
					else
					{
						$ctrl_group_id = $this->so->add($control_group);
						if($ctrl_group_id)
						{
							$message = lang('messages_saved_form');
						}
						else
						{
							$error = lang('messages_form_error');
						}
					}
					if($new_control_group)
					{
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_group.edit', 'id' => $ctrl_group_id));
					}
					else
					{
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_group.view', 'id' => $ctrl_group_id));
					}
				}
			}
			else if(isset($_POST['cancel_control_group'])) // The user has pressed the cancel button
			{
				if(isset($control_group_id) && $control_group_id > 0)
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_group.view', 'id' => $control_group_id));
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_group.index'));
				}
			}
			else if(isset($_POST['remove_control_group_items']))
			{
				$control_item_ids = array();
				// Fetching selected control items
				$control_tag_ids = phpgw::get_var('item_remove_ids');

				foreach ($control_tag_ids as $control_item_id)
				{
					$curr_control_item = $this->so_control_item->get_single($control_item_id);
					$curr_control_item->set_control_group_id(null);
					 
					$this->so_control_item->store($curr_control_item);
				}
				
				// Sigurd: START as categories
				$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
				$cats->supress_info	= true;
				
				$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','globals' => true,'use_acl' => $this->_category_acl));
				array_unshift($control_areas['cat_list'],array ('cat_id'=>'','name'=> lang('select value')));
								
				$control_area_array = array();
				foreach($control_areas['cat_list'] as $cat_list)
				{
					$control_area_array[] = array
					(
						'id' 	=> $cat_list['cat_id'],
						'name'	=> $cat_list['name'],
					);		
				}
				// END as categories

				//$control_area_array = $this->so_control_area->get_control_area_array();
				$procedure_array = $this->so_procedure->get_procedures(null,null,'title','ASC',null,null,null);

				if($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}

/*				foreach ($control_area_array as $control_area)
				{
					if($control_group->get_control_area_id() && $control_area->get_id() == $control_group->get_control_area_id())
					{
						$control_area_options[] = array
						(
							'id'	=> $control_area->get_id(),
							'name'	=> $control_area->get_title(),
							'selected' => 'yes'
						);
					}
					else
					{
						$control_area_options[] = array
						(
							'id'	=> $control_area->get_id(),
							'name'	=> $control_area->get_title()
						);
					}
				}*/

				foreach ($procedure_array as $procedure)
				{
					if($control_group->get_procedure_id() && $procedure->get_id() == $control_group->get_procedure_id())
					{
						$procedure_options[] = array
						(
							'id'	=> $procedure->get_id(),
							'name'	=> $procedure->get_title(),
							'selected' => 'yes'
						);
					}
					else
					{
						$procedure_options[] = array
						(
							'id'	=> $procedure->get_id(),
							'name'	=> $procedure->get_title()
						);
					}
				}
				array_unshift($procedure_options,array ('id'=>'','name'=> lang('select value')));

				$building_part_options = $this->so->get_building_part_select_array($control_group->get_building_part_id());

				$control_group_array = $control_group->toArray();
				$control_items_array = $this->so_control_item->get(null,null,'controller_control_item.control_area_id',true,null,null,array('available' => 'yes'));

				$control_items = array();

				foreach ($control_items_array as $control_item)
				{
					$control_items[] = $control_item->serialize();
				}

				$selected_control_items_array = controller_socontrol_item_list::get_instance()->get_control_items($control_group->get_id());

				$selected_control_items = array();

				foreach ($selected_control_items_array as $ctrl_item)
				{
					$selected_control_items[] = $ctrl_item->serialize();
				}
				phpgwapi_yui::tabview_setup('control_group_tabview');

				$data = array
				(
					'tabs'						=> phpgwapi_yui::tabview_generate($tabs, 'control_items'),
					'value_id'					=> !empty($control_group) ? $control_group->get_id() : 0,
					'img_go_home'				=> 'rental/templates/base/images/32x32/actions/go-home.png',
					'editable' 					=> true,
					'procedure'					=> array('options' => $procedure_options),
					'control_area'				=> array('options' => $control_area_array),
					'control_group'				=> $control_group_array,
					'control_items'				=> $control_items,
					'selected_control_items'	=> $selected_control_items,
					'building_part'				=> array('building_part_options' => $building_part_options),
				);


				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Control_group');

				self::add_javascript('controller', 'yahoo', 'control_tabs.js');
				self::add_javascript('controller', 'controller', 'jquery.js');
				self::add_javascript('controller', 'controller', 'ajax.js');
				self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
				self::render_template_xsl(array('control_group/control_group_tabs','control_group/control_group','control_group/control_group_items'), $data);
			}
			else if(isset($_POST['save_control_group_items']))
			{
				$tab_to_display = 'control_group_items';
				//update control items with control group id
				//$control_group_id = phpgw::get_var('control_group_id');

				$control_item_ids = array();
				// Fetching selected control items
				$control_tag_ids = phpgw::get_var('control_tag_ids');

				foreach ($control_tag_ids as $control_item_id)
				{
					$curr_control_item = $this->so_control_item->get_single($control_item_id);
					$curr_control_item->set_control_group_id($control_group_id);
					 
					$this->so_control_item->store($curr_control_item);
				}

				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_group.view', 'id' => $control_group_id));
			}
			else
			{
				// Sigurd: START as categories
				$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
				$cats->supress_info	= true;
				
				$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','globals' => true,'use_acl' => $this->_category_acl));
				array_unshift($control_areas['cat_list'],array ('cat_id'=>'','name'=> lang('select value')));
								
				$control_area_array = array();
				foreach($control_areas['cat_list'] as $cat_list)
				{
					$control_area_array[] = array
					(
						'id' 	=> $cat_list['cat_id'],
						'name'	=> $cat_list['name'],
					);		
				}
				// END as categories

				//$control_area_array = $this->so_control_area->get_control_area_array();
				$procedure_array = $this->so_procedure->get_procedures(null,null,'title','ASC',null,null,null);

				if($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}

/*				foreach ($control_area_array as $control_area)
				{
					if($control_group->get_control_area_id() && $control_area->get_id() == $control_group->get_control_area_id())
					{
						$control_area_options[] = array
						(
							'id'	=> $control_area->get_id(),
							'name'	=> $control_area->get_title(),
							'selected' => 'yes'
						);
					}
					else
					{
						$control_area_options[] = array
						(
							'id'	=> $control_area->get_id(),
							'name'	=> $control_area->get_title()
						);
					}
				}*/

				foreach ($procedure_array as $procedure)
				{
					if($control_group->get_procedure_id() && $procedure->get_id() == $control_group->get_procedure_id())
					{
						$procedure_options[] = array
						(
							'id'	=> $procedure->get_id(),
							'name'	=> $procedure->get_title(),
							'selected' => 'yes'
						);
					}
					else
					{
						$procedure_options[] = array
						(
							'id'	=> $procedure->get_id(),
							'name'	=> $procedure->get_title()
						);
					}
				}
				array_unshift($procedure_options,array ('id'=>'','name'=> lang('select value')));

				$building_part_options = $this->so->get_building_part_select_array($control_group->get_building_part_id());

				$control_group_array = $control_group->toArray();
				$control_items_array = $this->so_control_item->get(null,null,'controller_control_item.control_area_id',true,null,null,array('available' => 'yes'));

				$control_items = array();

				foreach ($control_items_array as $control_item)
				{
					$control_items[] = $control_item->serialize();
				}

				$selected_control_items_array = controller_socontrol_item_list::get_instance()->get_control_items($control_group->get_id());

				$selected_control_items = array();

				foreach ($selected_control_items_array as $ctrl_item)
				{
					$selected_control_items[] = $ctrl_item->serialize();
				}
				phpgwapi_yui::tabview_setup('control_group_tabview');

				$data = array
				(
					'tabs'						=> phpgwapi_yui::tabview_generate($tabs, $tab_to_display),
					'value_id'					=> !empty($control_group) ? $control_group->get_id() : 0,
					'img_go_home'				=> 'rental/templates/base/images/32x32/actions/go-home.png',
					'editable' 					=> true,
					'procedure'					=> array('options' => $procedure_options),
					'control_area'				=> array('options' => $control_area_array),
					'control_group'				=> $control_group_array,
					'control_items'				=> $control_items,
					'selected_control_items'	=> $selected_control_items,
					'building_part'				=> array('building_part_options' => $building_part_options),
				);


				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Control_group');

/*
				$GLOBALS['phpgw']->richtext->replace_element('what_to_do');
				$GLOBALS['phpgw']->richtext->replace_element('how_to_do');
				$GLOBALS['phpgw']->richtext->generate_script();
*/

	//			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'controller.item', 'controller' );

				self::add_javascript('controller', 'yahoo', 'control_tabs.js');
				self::add_javascript('controller', 'controller', 'jquery.js');
				self::add_javascript('controller', 'controller', 'ajax.js');
				self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
				self::render_template_xsl(array('control_group/control_group_tabs','control_group/control_group','control_group/control_group_items'), $data);
			}
		}

		public function save_group_order(){
			$control_group_id = phpgw::get_var('control_group_id');
			$control_id = phpgw::get_var('control_id');
			$group_order_nr = phpgw::get_var('group_order_nr');

			$status = true;

			$control_group_list = $this->so_control_group_list->get_single_2($control_id, $control_group_id);

			if( $control_group_list == null ){
				$control_group_list = new controller_control_group_list();
				$control_group_list->set_control_id($control_id);
				$control_group_list->set_control_group_id($control_group_id);
				$control_group_list->set_order_nr( $group_order_nr );
				$this->so_control_group_list->add( $control_group_list );
			}else{
				$control_group_list->set_order_nr( $group_order_nr );
				$this->so_control_group_list->update( $control_group_list );
			}

			return status;
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

			$search_for = phpgw::get_var('query');

			$ctrl_area = phpgw::get_var('control_areas');
			if(isset($ctrl_area) && $ctrl_area > 0)
			{
				$filters['control_areas'] = $ctrl_area; 
			}

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
			$control_group_id = phpgw::get_var('control_group_id');
			/*if(isset($control_group_id))
			{
				$control_item = $this->so->get_single($control_group_id);
			}*/
			//var_dump($start_index.'-'.$num_of_objects.'-'.$sort_field.'-'.$sort_ascending.'-'.$search_for.'-'.$search_type.'-'.$filters);
			$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$object_count = $this->so->get_count($search_for, $search_type, $filters);
			//var_dump($result_objects);

			$results = array();

			foreach($result_objects as $control_group_obj)
			{
				$results['results'][] = $control_group_obj->serialize();
			}

			$results['total_records'] = $object_count;
			$results['start'] = $params['start'];
			$results['sort'] = $params['sort'];
			$results['dir'] = $params['dir'];

			array_walk($results["results"], array($this, "_add_links"), "controller.uicontrol_group.view");

			return $this->yui_results($results);
		}

		/**
		 * Public method. Called when a user wants to view information about a control group.
		 * @param HTTP::id	the control_group ID
		 */
		public function view()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('view');

			$tabs = array
			(
				'control_group'		=> array('label' => lang('Control_group'), 'link' => '#control_group'),
				'control_items'		=> array('label' => lang('Control_items'), 'link' => '#control_items')
			);
			//Retrieve the control_group object
			$control_group_id = (int)phpgw::get_var('id');
			if(isset($_POST['edit_control_group']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_group.edit', 'id' => $control_group_id));
			}
			else
			{
				if(isset($control_group_id) && $control_group_id > 0)
				{
					$control_group = $this->so->get_single($control_group_id);
					//var_dump($control_group);
				}
				else
				{
					$this->render('permission_denied.php',array('error' => lang('invalid_request')));
					return;
				}
				//var_dump($control_group);

				if($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}

				$control_group_array = $control_group->toArray();
				//var_dump($control_group_array);

				$control_items_array = $this->so_control_item_list->get_control_items($control_group_id);

				$control_items = array();

				foreach ($control_items_array as $control_item)
				{
					$control_items[] = $control_item->serialize();
				}

				phpgwapi_yui::tabview_setup('control_group_tabview');

				$data = array
				(
					'tabs'						=> phpgwapi_yui::tabview_generate($tabs, 'control_group'),
					'value_id'					=> !empty($control_group) ? $control_group->get_id() : 0,
					'img_go_home'				=> 'rental/templates/base/images/32x32/actions/go-home.png',
					'control_group'				=> $control_group_array,
					'selected_control_items'	=> $control_items,
				);


				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Control group');

				self::add_javascript('controller', 'yahoo', 'control_tabs.js');
				self::render_template_xsl(array('control_group/control_group_tabs','control_group/control_group','control_group/control_group_items'), $data);
			}
		}

		// Returns control group list info as JSON
		public function get_control_groups_by_control_area()
		{
			$control_area_id = phpgw::get_var('control_area_id');
			if($control_area_id == "all")
			{
				//get all control groups
				$control_groups_array = $this->so->get_all_control_groups_array();
			}
			else
				$control_groups_array = $this->so->get_control_groups_by_control_area($control_area_id);
			
			if(count($control_groups_array)>0)
				return json_encode( $control_groups_array );
			else
				return null;
		}
		
		public function get_control_area_by_control_group()
		{
			$control_group_id = phpgw::get_var('control_group_id');
			if($control_group_id)
			{
				$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','globals' => true,'use_acl' => $this->_category_acl));
								
				$control_area_array = array();
				foreach($control_areas['cat_list'] as $cat_list)
				{
					$control_area_array[] = array
					(
						'id' 	=> $cat_list['cat_id'],
						'name'	=> $cat_list['name'],
					);		
				}
			}
			else
			{
				$control_areas_array = $this->so->get_control_areas_by_control_group($control_group_id);
			}
			
			if(count($control_areas_array)>0)
				return json_encode( $control_areas_array );
			else
				return null;
		}

	}
