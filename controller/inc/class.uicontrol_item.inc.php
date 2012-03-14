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
	phpgw::import_class('controller.socontrol_group');
	phpgw::import_class('controller.socontrol_area');

	include_class('controller', 'control_item', 'inc/model/');

	class controller_uicontrol_item extends controller_uicommon
	{
		private $so;
		private $so_control_item;
		private $so_control_group;
		private $so_control_area;

		public $public_functions = array
		(
			'index'	=>	true,
			'query'	=>	true,
			'edit'	=>	true,
			'view'	=>	true,
			'add'	=>	true,
			'display_control_items'	=> true,
			'save_item_order'	=> true,
			'delete_item_list'	=> true
		);

		public function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('controller.socontrol');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_control_item_list = CreateObject('controller.socontrol_item_list');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->so_control_area = CreateObject('controller.socontrol_area');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::control_item";
		}

		public function index()
		{
			$dir = phpgw::get_var('dir');
			if($dir)
			{
				$query_array = array('menuaction' => 'controller.uicontrol_item.index', 'phpgw_return_as' => 'json', 'sort_dir' => 'desc');
			}
			else
			{
				$query_array = array('menuaction' => 'controller.uicontrol_item.index', 'phpgw_return_as' => 'json');
			}
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

			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'control_groups',
								'text' => lang('Control_group').':',
								'list' => $this->so_control_group->get_control_group_select_array(),
							),
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
								'value' => lang('New control item'),
								'href' => self::link(array('menuaction' => 'controller.uicontrol_item.add')),
								'class' => 'new_item'
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link($query_array),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable'	=> true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key'	=>	'title',
							'label'	=>	lang('Control item title'),
							'sotrable'	=>	false
						),
						array(
							'key' => 'what_to_do',
							'label' => lang('Control item what to do'),
							'sortable'	=> false
						),
						array(
							'key' => 'control_group',
							'label' => lang('Control group'),
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
			
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('datatable');
			self::add_javascript('controller', 'yahoo', 'datatable.js');
//_debug_array($data);

			self::render_template_xsl('datatable', $data);
		}

		/**
	 	* Public method. Forwards the user to edit mode.
	 	*/
		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_item.edit'));
		}

		public function save_item_order(){

			$control_id = phpgw::get_var('control_id');
			$control_group_id = phpgw::get_var('control_group_id');
			$order_tags = phpgw::get_var('order_tags');

						
			$status = true;
			foreach($order_tags as $order_tag){
				$control_item_id = 	substr($order_tag, strpos($order_tag, ":")+1, strlen($order_tag));
				$order_nr = substr($order_tag, 0, strpos($order_tag, ":"));

				$control_item_list = $this->so_control_item_list->get_single_2($control_id, $control_item_id);
	
				if($order_nr != $control_item_list->get_order_nr() ){
					$control_item_list->set_order_nr($order_nr);

					if( !$this->so_control_item_list->update( $control_item_list )){
						$status = false;
					}
				}
			}

			return $status;
			
			if($status)
				return json_encode( array( "status" => "order_updated" ) );
			else
				return json_encode( array( "status" => "order_not_updated" ) );
			
		}

		public function delete_item_list(){

			$control_id = phpgw::get_var('control_id');
			$control_item_id = phpgw::get_var('control_item_id');

			$status = $this->so_control_item_list->delete($control_id, $control_item_id);

			return status;
		}

		public function edit()
		{
			$control_item_id = phpgw::get_var('id');
			if(isset($control_item_id) && $control_item_id > 0)
			{
				$control_item = $this->so_control_item->get_single($control_item_id);
			}
			else
			{
				$control_item = new controller_control_item();
			}

			if(isset($_POST['save_control_item'])) // The user has pressed the save button
			{
				if(isset($control_item)) // Add new values to the control item
				{
					$what_to_do_txt = phpgw::get_var('what_to_do','html');
					$what_to_do_txt = str_replace("&nbsp;", " ", $what_to_do_txt);
					$how_to_do_txt = phpgw::get_var('how_to_do','html');
					$how_to_do_txt = str_replace("&nbsp;", " ", $how_to_do_txt);
					$control_item->set_title(phpgw::get_var('title'));
					$control_item->set_required(phpgw::get_var('required') == 'on' ? true : false);
					$control_item->set_type(phpgw::get_var('measurement') == 'on' ? 'control_item_type_2' : 'control_item_type_1');
					$control_item->set_what_to_do( $what_to_do_txt );
					$control_item->set_how_to_do( $how_to_do_txt );
					$control_item->set_control_group_id( phpgw::get_var('control_group') );
					$control_item->set_control_area_id( phpgw::get_var('control_area') );

					//$this->so->store($control_item);

					if(isset($control_item_id) && $control_item_id > 0)
					{
						$ctrl_item_id = $control_item_id;
						if($this->so_control_item->store($control_item))
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
						$ctrl_item_id = $this->so_control_item->add($control_item);
						if($ctrl_item_id)
						{
							$message = lang('messages_saved_form');
						}
						else
						{
							$error = lang('messages_form_error');
						}
					}
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_item.index', 'dir' => 'desc'));
				}
			}
			else if(isset($_POST['cancel_control_item'])) // The user has pressed the cancel button
			{
				if(isset($control_item_id) && $control_item_id > 0)
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_item.view', 'id' => $control_item_id));
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_item.index'));
				}
			}
			else
			{
				//Sigurd: START as categories
				$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
				$cats->supress_info	= true;
				
				$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','globals' => true,'use_acl' => $this->_category_acl));
				array_unshift($control_areas['cat_list'],array ('cat_id'=>'','name'=> lang('select value')));
								
				$control_area_array = array();
				foreach($control_areas['cat_list'] as $cat_list)
				{
					if($cat_list['cat_id'] == $control_item->get_control_area_id())
					{
						$control_area_array[] = array
						(
							'id' 	=> $cat_list['cat_id'],
							'name'	=> $cat_list['name'],
							'selected' => 1,
						);
					}
					else
					{
						$control_area_array[] = array
						(
							'id' 	=> $cat_list['cat_id'],
							'name'	=> $cat_list['name'],
						);
					}
				}				
				// END as categories
				//$control_area_array = $this->so_control_area->get_control_area_array();
				$control_group_array = $this->so_control_group->get_control_group_array();


				if($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}

/*				foreach ($control_area_array as $control_area)
				{
					$control_area_options[] = array
					(
						'id'	=> $control_area->get_id(),
						'name'	=> $control_area->get_title()
						 
					);
				}
*/
				foreach ($control_group_array as $control_group)
				{
					if($control_group->get_id() == $control_item->get_control_group_id())
					{
						$control_group_options[] = array
						(
							'id'	=> $control_group->get_id(),
							'name'	=> $control_group->get_group_name(),
							'selected' => 1
						);
					}
					else
					{
						$control_group_options[] = array
						(
							'id'	=> $control_group->get_id(),
							'name'	=> $control_group->get_group_name()
							 
						);
					}
				}
				
				/*
				 * hack to fix display of &nbsp; char 
				 */
				$control_item->set_what_to_do(str_replace("&nbsp;", " ",$control_item->get_what_to_do()));
				$control_item->set_how_to_do(str_replace('&nbsp;', ' ', $control_item->get_how_to_do()));

				$control_item_array = $control_item->toArray();

				$data = array
				(
					'value_id'				=> !empty($control_item) ? $control_item->get_id() : 0,
					'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
					'editable' 				=> true,
					'control_item'			=> $control_item_array,
					'control_area'			=> array('options' => $control_area_array),
					'control_group'			=> array('options' => $control_group_options),
				);

				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Control_item');

				/*$GLOBALS['phpgw']->richtext->replace_element('what_to_do');
				$GLOBALS['phpgw']->richtext->replace_element('how_to_do');
				$GLOBALS['phpgw']->richtext->generate_script();*/
				$this->use_yui_editor(array('what_to_do','how_to_do'));
				
				self::add_javascript('controller', 'controller', 'jquery.js');
				self::add_javascript('controller', 'controller', 'ajax.js');
				self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');

				self::render_template_xsl('control_item/control_item', $data);
			}
		}

		public function query()
		{
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', null),
				'query'	=> phpgw::get_var('query'),
				'sort'	=> phpgw::get_var('sort'),
				'filters' => $filters
			);
			
			if(phpgw::get_var('sort_dir'))
			{
				$params['dir'] = phpgw::get_var('sort_dir');
			}
			else
			{
				$params['dir'] = phpgw::get_var('dir');
			}

			$ctrl_area = phpgw::get_var('control_areas');
			if(isset($ctrl_area) && $ctrl_area > 0)
			{
				$filters['control_areas'] = $ctrl_area; 
			}
			$ctrl_group = phpgw::get_var('control_groups');
			if(isset($ctrl_group) && $ctrl_group > 0)
			{
				$filters['control_groups'] = $ctrl_group; 
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
				$sort_field = 'control_item_id';
			}
			if(phpgw::get_var('sort_dir') == 'desc')
				$sort_ascending = false;
			else
				$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			//Create an empty result set
			$records = array();

			//Retrieve a contract identifier and load corresponding contract
			$control_item_id = phpgw::get_var('control_item_id');
			if(isset($control_item_id))
			{
				$control_item = $this->so_control_item->get_single($control_item_id);
			}

			$result_objects = $this->so_control_item->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$object_count = $this->so_control_item->get_count($search_for, $search_type, $filters);
			//var_dump($result_objects);

			$results = array();

			foreach($result_objects as $control_item_obj)
			{
				$results['results'][] = $control_item_obj->serialize();
			}

			$results['total_records'] = $object_count;
			$results['start'] = $start_index;
			$results['sort'] = $sort_field;
			$results['dir'] = $params['dir'];

			array_walk($results["results"], array($this, "_add_links"), "controller.uicontrol_item.view");

			return $this->yui_results($results);
		}

		/**
		 * Public method. Called when a user wants to view information about a control item.
		 * @param HTTP::id	the control_item ID
		 */
		public function view()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('view');
			//Retrieve the control_item object
			$control_item_id = (int)phpgw::get_var('id');
			if(isset($_POST['edit_control_item']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_item.edit', 'id' => $control_item_id));
			}
			else
			{
				if(isset($control_item_id) && $control_item_id > 0)
				{
					$control_item = $this->so_control_item->get_single($control_item_id);
				}
				else
				{
					$this->render('permission_denied.php',array('error' => lang('invalid_request')));
					return;
				}
				//var_dump($control_item);

				if($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}
				
				$category = execMethod('phpgwapi.categories.return_single', $control_item->get_control_area_id());
				$control_item->set_control_area_name($category[0]['name']);
				
				/*
				 * hack to fix display of &nbsp; char 
				 */
				$control_item->set_what_to_do(str_replace("&nbsp;", " ",$control_item->get_what_to_do()));
				$control_item->set_how_to_do(str_replace('&nbsp;', ' ', $control_item->get_how_to_do()));
				
				$control_item_array = $control_item->toArray();

				$data = array
				(
					'value_id'				=> !empty($control_item) ? $control_item->get_id() : 0,
					'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
					'control_item'			=> $control_item_array,
				);


				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Control item');

				self::render_template_xsl('control_item/control_item', $data);
			}
		}


	}
