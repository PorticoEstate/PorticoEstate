<?php
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
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			self::add_javascript('controller', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter', 
								'name' => 'status',
								'text' => lang('Status').':',
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
								'name' => 'control_groups',
								'text' => lang('Control_group').':',
								'list' => $this->so_control_group->get_control_group_select_array(),
							),
							array('type' => 'filter',
								'name' => 'control_areas',
								'text' => lang('Control_area').':',
								'list' => $this->so_control_area->get_control_area_select_array(),
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
								'value' => lang('New control item'),
								'href' => self::link(array('menuaction' => 'controller.uicontrol_item.add')),
								'class' => 'new_item'
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'controller.uicontrol_item.index', 'phpgw_return_as' => 'json')),
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
			$order_nr = phpgw::get_var('order_nr');
			
			$status = true;
			foreach($order_nr as $order_tag){
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
			
			return status;			
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
					$control_item->set_title(phpgw::get_var('title'));
					$control_item->set_required(phpgw::get_var('required') == 'on' ? true : false);
					$control_item->set_what_to_do( phpgw::get_var('what_to_do','html') );
					$control_item->set_how_to_do( phpgw::get_var('how_to_do','html') );
					$control_item->set_control_group_id( phpgw::get_var('control_group_id') );
					$control_item->set_control_area_id( phpgw::get_var('control_area_id') );
									
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
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_item.view', 'id' => $ctrl_item_id));
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
			
				$control_area_array = $this->so_control_area->get_control_area_array();
				$control_group_array = $this->so_control_group->get_control_group_array();
				
	
				if($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}
	
				foreach ($control_area_array as $control_area)
				{
					$control_area_options[] = array
					(
						'id'	=> $control_area->get_id(),
						'name'	=> $control_area->get_title()
						 
					);
				}
	
				foreach ($control_group_array as $control_group)
				{
					$control_group_options[] = array
					(
						'id'	=> $control_group->get_id(),
						'name'	=> $control_group->get_group_name()
						 
					);
				}
				
				$control_item_array = $control_item->toArray();
	
				$data = array
				(
					'value_id'				=> !empty($control_item) ? $control_item->get_id() : 0,
					'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
					'editable' 				=> true,
					'control_item'			=> $control_item_array,
					'control_area'			=> array('options' => $control_area_options),
					'control_group'			=> array('options' => $control_group_options),
				);
	
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Control_item');
	
				$GLOBALS['phpgw']->richtext->replace_element('what_to_do');
				$GLOBALS['phpgw']->richtext->replace_element('how_to_do');
				$GLOBALS['phpgw']->richtext->generate_script();

				self::render_template_xsl('control_item', $data);
			}
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
			$results['start'] = $params['start'];
			$results['sort'] = $params['sort'];
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
				
				$control_item_array = $control_item->toArray();
	
				$data = array
				(
					'value_id'				=> !empty($control_item) ? $control_item->get_id() : 0,
					'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
					'control_item'			=> $control_item_array,
				);
	
	
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Control item');
	
				self::render_template_xsl('control_item', $data);
			}
		}
		
		
	}
