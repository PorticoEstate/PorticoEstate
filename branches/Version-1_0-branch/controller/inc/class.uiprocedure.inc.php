<?php
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('controller.soprocedure');
	
	include_class('controller', 'procedure', 'inc/model/');

	class controller_uiprocedure extends controller_uicommon
	{
		private $so;
		
		public $public_functions = array
		(
			'index'	=>	true,
			'query'	=>	true,
			'edit'	=>	true,
			'view'	=>	true,
			'add'	=>	true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so = CreateObject('controller.soprocedure');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::procedure";
			//$this->bo = CreateObject('property.boevent',true);
		}
		
		public function index()
		{
			//self::set_active_menu('controller::control_item2::control_item_list2');
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
							array(
								'type' => 'link',
								'value' => lang('f_new_procedure'),
								'href' => self::link(array('menuaction' => 'controller.uiprocedure.add'))
							),
							array('type' => 'text', 
                                'text' => lang('search'),
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'controller.uiprocedure.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable'	=> true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key' => 'title',
							'label' => lang('Procedure title'),
							'sortable'	=> false
						),
						array(
							'key' => 'purpose',
							'label' => lang('Procedure purpose'),
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

		public function edit()
		{
			$procedure_id = phpgw::get_var('id');
			if(isset($procedure_id) && $procedure_id > 0)
			{
				$procedure = $this->so->get_single($procedure_id);
			}
			else
			{
				$procedure = new controller_procedure();
			}
			

			if(isset($_POST['save_procedure'])) // The user has pressed the save button
			{
				if(isset($procedure)) // Edit procedure
				{
					$procedure->set_title(phpgw::get_var('title'));
					$procedure->set_purpose(phpgw::get_var('purpose','html'));
					$procedure->set_responsibility(phpgw::get_var('responsibility'));
					$procedure->set_description(phpgw::get_var('description','html'));
					$procedure->set_reference(phpgw::get_var('reference'));
					$procedure->set_attachment(phpgw::get_var('attachment'));
					$procedure->set_start_date(strtotime(phpgw::get_var('start_date_hidden')));
					$procedure->set_end_date(strtotime(phpgw::get_var('end_date_hidden')));
					
					if(isset($procedure_id) && $procedure_id > 0)
					{
						$proc_id = $procedure_id;
						if($this->so->store($procedure))
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
						$proc_id = $this->so->add($procedure);
						if($proc_id)
						{
							$message = lang('messages_saved_form');
						}
						else
						{
							$error = lang('messages_form_error');
						}
					}
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.view', 'id' => $proc_id));
				}
			}
			else if(isset($_POST['revisit_procedure'])) // The user has pressed the revisit button
			{
				$old_procedure = $this->so->get_single($procedure_id);
				if(isset($procedure)) // Edit procedure
				{
					$revision = $procedure->get_revision_no();
					if($revision && is_numeric($revision))
					{
						$revision = (int)$revision;
						$new_revision = $revision++;
						$procedure->set_revision_no($new_revision);
					}
					$procedure->set_title(phpgw::get_var('title'));
					$procedure->set_purpose(phpgw::get_var('purpose','html'));
					$procedure->set_responsibility(phpgw::get_var('responsibility'));
					$procedure->set_description(phpgw::get_var('description','html'));
					$procedure->set_reference(phpgw::get_var('reference'));
					$procedure->set_attachment(phpgw::get_var('attachment'));
					$procedure->set_start_date(strtotime(phpgw::get_var('start_date_hidden')));
					$procedure->set_end_date(strtotime(phpgw::get_var('end_date_hidden')));
					
					if(isset($procedure_id) && $procedure_id > 0)
					{
						$proc_id = $procedure_id;
						$old_procedure->set_id(null);
						$old_procedure->set_end_date(time());
						$old_procedure->set_procedure_id($proc_id);
						if($this->so->add($old_procedure)) //add old revision of procedure to history
						{
							if($this->so->store($procedure))
							{
								$message = lang('messages_saved_form');
							}
							else
							{
								$error = lang('messages_form_error');
							}
						}
					}

					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.view', 'id' => $proc_id));
				}
			}
			else if(isset($_POST['cancel_procedure'])) // The user has pressed the cancel button
			{
				if(isset($procedure_id) && $procedure_id > 0)
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.view', 'id' => $procedure_id));					
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.index'));
				}
			}
			else
			{
				if($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}
				
				$procedure_array = $procedure->toArray();
				//_debug_array($procedure_array);
	
				$data = array
				(
					'value_id'				=> !empty($procedure) ? $procedure->get_id() : 0,
					'start_date'			=> $GLOBALS['phpgw']->yuical->add_listener('start_date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], ($procedure->get_start_date())?$procedure->get_start_date():time())),
					'end_date'				=> $GLOBALS['phpgw']->yuical->add_listener('end_date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], ($procedure->get_end_date())?$procedure->get_end_date():'')),
					'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
					'editable' 				=> true,
					'procedure'				=> $procedure_array,
				);
	
	
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Procedure');
	
	
				$GLOBALS['phpgw']->richtext->replace_element('purpose');
				$GLOBALS['phpgw']->richtext->replace_element('description');
				$GLOBALS['phpgw']->richtext->generate_script();
	
	
	//			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'controller.item', 'controller' );
	
				self::render_template_xsl('procedure_item', $data);
			}
		}
		
		/**
	 	* Public method. Forwards the user to edit mode.
	 	*/
		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.edit'));
		}
		
		/**
		 * Public method. Called when a user wants to view information about a procedure.
		 * @param HTTP::id	the procedure ID
		 */
		public function view()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('view');
			//Retrieve the procedure object
			$procedure_id = (int)phpgw::get_var('id');
			if(isset($_POST['edit_procedure']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.edit', 'id' => $procedure_id));
			}
			else
			{
				if(isset($procedure_id) && $procedure_id > 0)
				{
					$procedure = $this->so->get_single($procedure_id);
				}
				else
				{
					$this->render('permission_denied.php',array('error' => lang('invalid_request')));
					return;
				}
				
				if($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}
				
				$procedure_array = $procedure->toArray();
				if($procedure->get_start_date() && $procedure->get_start_date() != null)
					$procedure_start_date = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $procedure->get_start_date());
				if($procedure->get_end_date() && $procedure->get_end_date() != null)
					$procedure_end_date	= date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $procedure->get_end_date());
				//_debug_array($procedure_array);
	
				$data = array
				(
					'value_id'				=> !empty($procedure) ? $procedure->get_id() : 0,
					'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
					'procedure'				=> $procedure_array,
					'start_date'			=> $procedure_start_date,
					'end_date'				=> $procedure_end_date
				);
	
	
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Procedure');
	
				self::render_template_xsl('procedure_item', $data);
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
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			// Form variables
			$search_for 	= phpgw::get_var('query');
			$search_type	= phpgw::get_var('search_option');
			// Create an empty result set
			$result_objects = array();
			$result_count = 0;
			
			//Retrieve a contract identifier and load corresponding contract
			$procedure_id = phpgw::get_var('procedure_id');
			
			$exp_param 	= phpgw::get_var('export');
			$export = false;
			if(isset($exp_param)){
				$export=true;
				$num_of_objects = null;
			}
			
			//Retrieve the type of query and perform type specific logic
			$query_type = phpgw::get_var('type');
			switch($query_type)
			{
				default: // ... all composites, filters (active and vacant)
					phpgwapi_cache::session_set('controller', 'procedure_query', $search_for);
					$filters = array();
					$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = $this->so->get_count($search_for, $search_type, $filters);
					break;
			}

			//Create an empty row set
			$rows = array();
			foreach($result_objects as $result) {
				if(isset($result))
				{
					$rows[] = $result->serialize();
				}
			}
			
			// ... add result data
			$result_data = array('results' => $rows);
			
			$result_data['total_records'] = $object_count;
			$result_data['start'] = $params['start'];
			$result_data['sort'] = $params['sort'];
			$result_data['dir'] = $params['dir'];
			
			$editable = phpgw::get_var('editable') == 'true' ? true : false;
			
			if(!$export){
				//Add action column to each row in result table
				array_walk(
					$result_data['results'],
					array($this, '_add_links'),
					"controller.uiprocedure.view");
			}
//_debug_array($result_data);
			return $this->yui_results($result_data);

		}

			public function add_actions(&$value, $key, $params)
		{
			//Defining new columns
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();

			// Get parameters
			$procedure_id = $params[0];
			$editable = $params[1];
			
			// Depending on the type of query: set an ajax flag and define the action and label for each row
			switch($type)
			{
				default:
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uiprocedure.view', 'id' => $value['id'])));
					$value['labels'][] = lang('show');
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uiprocedure.edit', 'id' => $value['id'])));
					$value['labels'][] = lang('edit');
			}
		}
	}