<?php
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('property.boevent');
	phpgw::import_class('controller.socontrol');
	phpgw::import_class('controller.socontrol_item');
	phpgw::import_class('controller.socontrol_group');
	
	include_class('controller', 'control', 'inc/model/');

	class controller_uicontrol_item2 extends controller_uicommon
	{
		private $bo; 
		private $so;
		private $so_proc; 
		
		public $public_functions = array
		(
			'index'					=> true,
			'display_control_items'	=> true,
			'separate_tabs'			=> true,
			'delete'				=> true,
			'js_poll'				=> true
		);

		public function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('controller.socontrol');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->bo = CreateObject('property.boevent',true);
		}
		
		public function index()
		{
			self::set_active_menu('controller::control_item2');			
			$repeat_type = $this->bo->get_rpt_type_list();
			$repeat_day = $this->bo->get_rpt_day_list();

			if(isset($_POST['save_control'])) // The user has pressed the save button
			{
				if(isset($control)) // Edit control
				{
					$control->set_title(phpgw::get_var('title'));
					$control->set_description(phpgw::get_var('description'));
					$control->set_start_date( strtotime( phpgw::get_var('start_date')  ) );
					$control->set_end_date( strtotime( phpgw::get_var('end_date') ) );
					$control->set_repeat_day( strtotime( phpgw::get_var('repeat_day') ) );
					$control->set_repeat_type( strtotime( phpgw::get_var('repeat_type') ) );
					$control->set_repeat_interval( strtotime( phpgw::get_var('repeat_interval') ) );
					$control->set_enabled( true );
									
					$this->so->add($control);
				}
				else // Add new control
				{

					$control = new controller_control();
					
					$control->set_title(phpgw::get_var('title'));
					$control->set_description(phpgw::get_var('description'));
					$control->set_start_date( strtotime( phpgw::get_var('start_date')  ) );
					$control->set_end_date( strtotime( phpgw::get_var('end_date') ) );
					$control->set_repeat_day( strtotime( phpgw::get_var('repeat_day') ) );
					$control->set_repeat_type( strtotime( phpgw::get_var('repeat_type') ) );
					$control->set_repeat_interval( strtotime( phpgw::get_var('repeat_interval') ) );
					$control->set_enabled( true );
									
					$this->so->add($control);
				}
			}
			
			$control_item_array = $this->so_control_item->get_control_item_array();
			$control_group_array = $this->so_control_group->get_control_group_array();
			

			if($this->flash_msgs)
			{
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			}

			foreach ($control_area_array as $control_area)
			{
				$control_area_options = array
				(
					'id'	=> $control_area->get_id(),
					'name'	=> $control_area->get_name()
					 
				);
			}

			foreach ($control_group_array as $control_group)
			{
				$control_group_options = array
				(
					'id'	=> $control_group->get_id(),
					'name'	=> $control_group->get_name()
					 
				);
			}

			$data = array
			(
				'value_id'				=> !empty($control) ? $control->get_id() : 0,
				'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
				'editable' 				=> true,
				'control_item'			=> array('options' => $control_area_options),
				'control_group'			=> array('options' => $control_group_options),
			);


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Control_item');

/*
			$GLOBALS['phpgw']->richtext->replace_element('what_to_do');
			$GLOBALS['phpgw']->richtext->replace_element('how_to_do');
			$GLOBALS['phpgw']->richtext->generate_script();
*/

//			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'controller.item', 'controller' );

			self::render_template_xsl('control_item', $data);
		}


		public function separate_tabs()
		{
			self::set_active_menu('controller::control_item2::separate_tabs');

            $type =  phpgw::get_var('type', 'string', 'REQUEST', null);

			$tabs = array();
			$tabs[] = array(
				'label' => lang('Your preferences'),
				'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol_item2.separate_tabs', 'type' => 'user'))
			);
			$tabs[] = array(
				'label' => lang('Default preferences'),
				'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol_item2.separate_tabs', 'type' => 'default'))
			);
			$tabs[] = array(
				'label' => lang('Forced preferences'),
				'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol_item2.separate_tabs', 'type' => 'forced'))
			);

			switch($type)
			{
				case 'default':
					$selected = 1;
					$resource_id = 81;
					break;
				case 'forced':
					$selected = 2;
					$resource_id = 46;
					break;
				case 'user':
				default:
					$selected = 0;
					$resource_id = 80;
			}

			$add_document_link = $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uicontrol_item2.index') );
			$resource = array('id' => $resource_id, 'add_document_link' => $add_document_link, 'permission' => array('write' => true ) );

			$data = array
			(
				'tabs'	=> $GLOBALS['phpgw']->common->create_tabs($tabs, $selected),
				'resource'	=> $resource
			);
			self::render_template_xsl('example_separate_tabs', $data);
		}


		public function display_control_items()
		{
			self::set_active_menu('controller::control_item2::control_item_list2');
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->display_control_items_json();
			}
			$this->bo = CreateObject('booking.boapplication');
			$GLOBALS['phpgw_info']['apps']['manual']['section'] = 'booking_manual';
			self::add_javascript('controller', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('New application'),
								'href' => self::link(array('menuaction' => 'controller.uicontrol_item2.index'))
							),
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
								'name' => 'buildings',
                                'text' => lang('Building').':',
                                'list' => $this->bo->so->get_buildings(),
							),
							array('type' => 'filter', 
								'name' => 'activities',
                                'text' => lang('Activity').':',
                                'list' => $this->bo->so->get_activities_main_level(),
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
						),
					),
				),
				'datatable' => array
				(
					'source' => self::link(array('menuaction' => 'controller.uicontrol_item2.display_control_items', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable'	=> true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key' => 'status',
							'label' => lang('Status'),
							'sortable'	=> false
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building'),
							'sortable'	=> true
						),
						array(
							'key' => 'what',
							'label' => lang('What'),
							'sortable'	=> false
						),
						array(
							'key' => 'created',
							'label' => lang('Created'),
							'sortable'	=> true
						),
						array(
							'key' => 'modified',
							'label' => lang('last modified'),
							'sortable'	=> true
						),
						array(
							'key' => 'activity_name',
							'label' => lang('Activity'),
							'sortable'	=> true
						),
						array(
							'key' => 'contact_name',
							'label' => lang('Contact'),
							'sortable'	=> true
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);
//_debug_array($data);
			$parameters = array
			(
				'parameter' => array
				(
					array
					(
						'name'		=> 'id',
						'source'	=> 'id'
					),
				)
			);

			$actions = array
			(
				array
				(
					'my_name'		=> 'view',
					'text' 			=> lang('view'),
				//	'confirm_msg'	=> lang('do you really want to view this entry'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'controller.uicontrol_item2.index',
					)),
					'parameters'	=> $parameters
				),
				array
				(
					'my_name'		=> 'edit',
					'text' 			=> lang('edit'),
					'confirm_msg'	=> lang('do you really want to edit this entry'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'controller.uicontrol_item2.index',
					)),
					'parameters'	=> $parameters
				),
				array
				(
					'my_name'		=> 'delete',
					'text' 			=> lang('delete'),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'controller.uicontrol_item2.delete',
					)),
					'parameters'	=> $parameters
				)
			);

			$data['actions'] = json_encode($actions);

//_debug_array($data);die();
			self::render_template_xsl('datatable', $data);
		}

		public function display_control_items_json()
		{
			$this->bo = CreateObject('booking.boapplication');
			$this->resource_bo = CreateObject('booking.boresource');

			if ( !isset($GLOBALS['phpgw_info']['user']['apps']['admin']) &&
			     $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'controller') )
			{
				$filters['id'] = $this->bo->accessable_applications($GLOBALS['phpgw_info']['user']['id']);
			}
			$filters['status'] = 'NEW';
			if(isset($_SESSION['showall']))
			{
				$filters['status'] = array('NEW', 'PENDING','REJECTED', 'ACCEPTED');
                $testdata =  phpgw::get_var('buildings', 'int', 'REQUEST', null);
                if ($testdata != 0)
                {
                    $filters['building_name'] = $this->bo->so->get_building(phpgw::get_var('buildings', 'int', 'REQUEST', null));        
                }
                else
                {
                    unset($filters['building_name']);                
                }
                $testdata2 =  phpgw::get_var('activities', 'int', 'REQUEST', null);
                if ($testdata2 != 0)
                {
                    $filters['activity_id'] = $this->bo->so->get_activities(phpgw::get_var('activities', 'int', 'REQUEST', null));        
                }
                else
                {
                    unset($filters['activity_id']);                
                }
                
			}
			else
			{
				if (phpgw::get_var('status') == 'none')
				{
					$filters['status'] = array('NEW', 'PENDING', 'REJECTED', 'ACCEPTED');
				} 
				else
				{
	                $filters['status'] = phpgw::get_var('status');
				}
                $testdata =  phpgw::get_var('buildings', 'int', 'REQUEST', null);
                if ($testdata != 0)
                {
                    $filters['building_name'] = $this->bo->so->get_building(phpgw::get_var('buildings', 'int', 'REQUEST', null));        
                }
                else
                {
                    unset($filters['building_name']);                
                }
                $testdata2 =  phpgw::get_var('activities', 'int', 'REQUEST', null);
                if ($testdata2 != 0)
                {
                    $filters['activity_id'] = $this->bo->so->get_activities(phpgw::get_var('activities', 'int', 'REQUEST', null));        
                }
                else
                {
                    unset($filters['activity_id']);                
                }
            }

			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', null),
				'query'	=> phpgw::get_var('query'),
				'sort'	=> phpgw::get_var('sort'),
				'dir'	=> phpgw::get_var('dir'),
				'filters' => $filters
			);

			$applications = $this->bo->so->read($params);

			foreach($applications['results'] as &$application)
			{
				if (strstr($application['building_name'],"%"))
				{
					$search = array('%2C','%C3%85', '%C3%A5', '%C3%98', '%C3%B8', '%C3%86', '%C3%A6');
					$replace = array (',','Å','å','Ø','ø','Æ','æ');
					$application['building_name'] = str_replace($search, $replace, $application['building_name']);
				}

				$application['status'] = lang($application['status']);
				$application['created'] = pretty_timestamp($application['created']);
				$application['modified'] = pretty_timestamp($application['modified']);
				$application['frontend_modified'] = pretty_timestamp($application['frontend_modified']);
				$application['resources'] = $this->resource_bo->so->read(array('filters'=>array('id'=>$application['resources'])));
				$application['resources'] = $application['resources']['results'];
				if($application['resources'])
				{
					$names = array();
					foreach($application['resources'] as $res)
					{
						$names[] = $res['name'];
					}
					$application['what'] = $application['resources'][0]['building_name']. ' ('.join(', ', $names).')';
				}
			}
			array_walk($applications["results"], array($this, "_add_links"), "controller.uicontrol_item2.index");
//_debug_array($this->yui_results($applications));
			return $this->yui_results($applications);
		}
					

		public function delete()
		{
			return 'deleted';
		}

		public function js_poll()
		{
			if($poll = phpgw::get_var('poll'))
			{
				return $poll;
			}
			return 'hello world';
		}

		public function query()
		{
			var_dump("Er i uicontrol");

		}	
	}
