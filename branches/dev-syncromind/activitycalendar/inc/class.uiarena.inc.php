<?php
	phpgw::import_class('activitycalendar.uicommon');
	phpgw::import_class('activitycalendar.soarena');

	include_class('activitycalendar', 'arena', 'inc/model/');

	class activitycalendar_uiarena extends activitycalendar_uicommon
	{

		public $public_functions = array
			(
			'index'				 => true,
			'query'				 => true,
			'view'				 => true,
			'add'				 => true,
			'save'				 => true,
			'edit'				 => true,
			'download'			 => true,
			'get_address_search' => true
		);

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('activitycalendar::arena');
			$config = CreateObject('phpgwapi.config', 'activitycalendar');
			$config->read();
		}

		/**
		 * Public method. Forwards the user to edit mode.
		 */
		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiarena.edit'));
		}

		/**
		 * Public method.
		 */
		public function get_address_search()
		{
			$search_string = phpgw::get_var('query');
			//var_dump($search_string);
			return activitycalendar_soarena::get_instance()->get_address($search_string);
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$appname = lang('arenas');

			$function_msg = lang('list %1', $appname);
			$type = 'all_arenas';

			$data = array(
				'datatable_name'	=> $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type'   => 'filter',
								'name'   => 'active',
								'text'   => lang('marked_as'),
								'list'   => array
											(
												array('id' => 'all', 'name' => lang('all')),
												array('id' => 'active', 'name' => lang('active')),
												array('id' => 'inactive', 'name' => lang('inactive'))
											)
							),		
							array(
								'type'   => 'link',
								'value'  => lang('new'),
								'href'   => self::link(array(
									'menuaction'	=> 'activitycalendar.uiarena.add'
								)),
								'class'  => 'new_item'
							)							
						)
					)
				),
				'datatable' => array(
					'source'	=> self::link(array(
						'menuaction'	=> 'activitycalendar.uiarena.index', 
						'type'			=> $type,
						'phpgw_return_as' => 'json'
					)),
					'download'	=> self::link(array('menuaction' => 'activitycalendar.uiarena.download',
							'type'		=> $type,
							'export'    => true,
							'allrows'   => true
					)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array(
							array('key'=>'id', 'label'=>lang('id'), 'sortable'=>true, 'hidden'=>false),
							array('key'=>'arena_name', 'label'=>lang('name'), 'sortable'=>true, 'hidden'=>false),
							array('key'=>'address', 'label'=>lang('address'), 'sortable'=>true, 'hidden'=>false)
					)
				)
			);

			$data['datatable']['actions'][] = array
				(
					'my_name'		=> 'show',
					'text'			=> lang('show'),
					'action'		=> self::link(array(
							'menuaction'	=> 'activitycalendar.uiarena.view'
					)),
					'parameters'	=> json_encode(array('parameter'=>array(array('name'=>'id', 'source'=>'id'))))		
				);

			$data['datatable']['actions'][] = array
				(
					'my_name'		=> 'edit',
					'text'			=> lang('edit'),			
					'action'		=> self::link(array(
							'menuaction'	=> 'activitycalendar.uiarena.edit'
					)),
					'parameters'	=> json_encode(array('parameter'=>array(array('name'=>'id', 'source'=>'id'))))
				);
			
			self::render_template_xsl('datatable_jquery', $data);
		}

		/**
		 * Displays info about one single arena.
		 */
		public function view()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('view');
			// Get the contract part id
			$arena_id	 = (int)phpgw::get_var('id');

			$arena = activitycalendar_soarena::get_instance()->get_single($arena_id);
			
			if (empty($arena))
			{
				phpgwapi_cache::message_set(lang('Could not find specified arena.'), 'error');
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiarena.index'));
			}
			
			$tabs = array();
			$tabs['arena']	= array('label' => lang('arena'), 'link' => '#arena');
			$active_tab = 'arena';

			$data = array
			(
				'tabs'							=> phpgwapi_jquery::tabview_generate($tabs, $active_tab),		
				'edit_url'						=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'activitycalendar.uiarena.edit', 'id' => $arena->get_id())),
				'cancel_url'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'activitycalendar.uiarena.index')),
				'lang_edit'						=> lang('edit'),
				'lang_cancel'					=> lang('cancel'),
				
				'arena_name'					=> $arena->get_arena_name(),
				'address'						=> $arena->get_address(),
				'address_no'					=> $arena->get_addressnumber(),				
				'active_value'					=> ($arena->is_active() ? lang('active_arena') : lang('inactive_arena'))
			);
			
			self::render_template_xsl(array('arena'), array('view' => $data));
		}

		public function edit()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');
			// Get the contract part id
			$arena_id	 = (int)phpgw::get_var('id');

			if(isset($arena_id) && $arena_id > 0)
			{
				$arena = activitycalendar_soarena::get_instance()->get_single($arena_id);
			}
			else
			{
				$arena = new activitycalendar_arena();
			}
			
			$is_active = $arena->is_active(); 
			$active_options = array
			(
				array('id'=>'yes', 'name'=>lang('active'), 'selected'=>(($is_active) ? 1 : 0)),
				array('id'=>'no', 'name'=>lang('inactive'), 'selected'=>((!$is_active) ? 1 : 0))
			);
			
			$tabs = array();
			$tabs['arena']	= array('label' => lang('arena'), 'link' => '#arena');
			$active_tab = 'arena';

			$data = array
			(
				'tabs'							=> phpgwapi_jquery::tabview_generate($tabs, $active_tab),		
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'activitycalendar.uiarena.save')),
				'cancel_url'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'activitycalendar.uiarena.index')),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				
				'arena_id'						=> $arena->get_id(),
				'arena_name'					=> $arena->get_arena_name(),
				'address'						=> $arena->get_address(),
				'address_no'					=> $arena->get_addressnumber(),				
				'list_active_options'			=> array('options' => $active_options),

				'validator'				=> phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security', 'file'))
			);
			
			phpgwapi_jquery::load_widget('autocomplete');

			$_autocomplete = <<<JS

				$(document).ready(function () 
				{
					var oArgs = {menuaction:'activitycalendar.uiarena.get_address_search'};
					var strURL = phpGWLink('index.php', oArgs);
					JqueryPortico.autocompleteHelper(strURL, 'address', '', 'address_container');
				});
JS;
			$GLOBALS['phpgw']->js->add_code('', $_autocomplete);
			
			self::render_template_xsl(array('arena'), array('edit' => $data));			
		}

		public function query()
		{
			$search			= phpgw::get_var('search');
			$order			= phpgw::get_var('order');
			$draw			= phpgw::get_var('draw', 'int');
			$columns		= phpgw::get_var('columns');

			$start_index	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$num_of_objects	= (phpgw::get_var('length', 'int') <= 0) ? $this->user_rows_per_page : phpgw::get_var('length', 'int');
			$sort_field		= ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'id'; 
			$sort_ascending	= ($order[0]['dir'] == 'desc') ? false : true;
			// Form variables
			$search_for 	= $search['value'];
			$search_type	= phpgw::get_var('search_option');			

			// Create an empty result set
			$result_objects	 = array();
			$result_count	 = 0;
			//Retrieve the type of query and perform type specific logic
			$query_type		 = phpgw::get_var('type');

			$export			= phpgw::get_var('export','bool');
			if ($export)
			{
				$num_of_objects = null;
			}

			switch($query_type)
			{
				case 'all_arenas':
					$filters		 = array('arena_type' => phpgw::get_var('arena_type'), 'active' => phpgw::get_var('active'));
					$result_objects	 = activitycalendar_soarena::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$result_count	 = activitycalendar_soarena::get_instance()->get_count($search_for, $search_type, $filters);
					break;
			}
			//var_dump($result_objects);
			//Create an empty row set
			$rows = array();
			foreach($result_objects as $result)
			{
				if(isset($result))
				{
					// ... add a serialized result
					$rows[] = $result->serialize();
				}
			}

			if($export)
			{
				return $rows;
			}
			
			$result_data    =   array('results' =>  $rows);
			$result_data['total_records']	= $result_count;
			$result_data['draw']    = $draw;

			return $this->jquery_results($result_data);
		}

		public function save()
		{
			$arena_id = (int)phpgw::get_var('id');
			// Retrieve the activity object or create a new one
			if(isset($arena_id) && $arena_id > 0)
			{
				$arena = activitycalendar_soarena::get_instance()->get_single($arena_id);
			}
			else
			{
				$arena = new activitycalendar_arena();
			}
			
			$arena->set_internal_arena_id(phpgw::get_var('internal_arena_id'));
			$arena->set_arena_name(phpgw::get_var('arena_name'));
			$arena->set_address(phpgw::get_var('address'));
			$arena->set_addressnumber(phpgw::get_var('address_no'));
			$arena->set_zip_code(phpgw::get_var('zip_code'));
			$arena->set_city(phpgw::get_var('city'));
			$arena->set_active(phpgw::get_var('arena_active') == 'yes' ? true : false);

			if(activitycalendar_soarena::get_instance()->store($arena)) // ... and then try to store the object
			{
				phpgwapi_cache::message_set(lang('messages_saved_form'), 'message');
			}
			else
			{
				phpgwapi_cache::message_set(lang('messages_form_error'), 'error');
			}
								
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiarena.view', 'id' => $arena->get_id()));
		}

		public function download_export()
		{
			if(!$this->isExecutiveOfficer())
			{
				$this->render('permission_denied.php');
				return;
			}
			//$browser = CreateObject('phpgwapi.browser');
			//$browser->content_header('export.txt','text/plain');

			$stop = phpgw::get_var('date');

			$cs15 = phpgw::get_var('generate_cs15');
			if($cs15 == null)
			{
				$export_format	 = explode('_', phpgw::get_var('export_format'));
				$file_ending	 = $export_format[1];
				if($file_ending == 'gl07')
				{
					$type = 'intern';
				}
				else if($file_ending == 'lg04')
				{
					$type = 'faktura';
				}
				$date = date('Ymd', $stop);
				header('Content-type: text/plain');
				header("Content-Disposition: attachment; filename=PE_{$type}_{$date}.{$file_ending}");

				$id		 = phpgw::get_var('id');
				$path	 = "/rental/billings/{$id}";

				$vfs				 = CreateObject('phpgwapi.vfs');
				$vfs->override_acl	 = 1;

				print $vfs->read
				(
				array
					(
					'string' => $path,
					RELATIVE_NONE
				)
				);

				//print rental_sobilling::get_instance()->get_export_data((int)phpgw::get_var('id'));
			}
			else
			{
				$file_ending = 'cs15';
				$type		 = 'kundefil';
				$date		 = date('Ymd', $stop);
				header('Content-type: text/plain');
				header("Content-Disposition: attachment; filename=PE_{$type}_{$date}.{$file_ending}");
				print rental_sobilling::get_instance()->generate_customer_export((int)phpgw::get_var('id'));
			}
		}
	}