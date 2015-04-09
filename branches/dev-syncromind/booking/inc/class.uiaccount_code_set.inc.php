<?php
phpgw::import_class('booking.uicommon');

	class booking_uiaccount_code_set extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
			'add'				=> true,
			'toggle_show_inactive'	=>	true,
		);
		
		protected $fields = array('name', 'object_number', 'responsible_code', 'article', 'service', 'project_number', 'unit_number', 'unit_prefix', 'invoice_instruction', 'active', 'dim_4', 'dim_value_4', 'dim_value_5');

		protected $module = 'booking';
		
		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boaccount_code_set');
			self::set_active_menu('booking::settings::account_code_sets');
			$this->url_prefix = 'booking.uiaccount_code_set';
		}
		
		public function link_to($action, $params = array())
		{
			return $this->link($this->link_to_params($action, $params));
		}
		
		public function redirect_to($action, $params = array())
		{
			return $this->redirect($this->link_to_params($action, $params));
		}
		
		public function link_to_params($action, $params = array())
		{
			if (isset($params['ui'])) {
				$ui = $params['ui'];
				unset($params['ui']);
			} else {
				$ui = 'account_code_set';
			}
			
			$action = sprintf($this->module.'.ui%s.%s', $ui, $action);
			return array_merge(array('menuaction' => $action), $params);
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			
			self::add_javascript('booking', 'booking', 'account_code_set.js');
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'text', 
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
			);
			$data['datatable']['source'][] = $this->link_to('index', array('phpgw_return_as' => 'json'));
			$data['datatable']['field'][] = array('key' => 'name', 'label' => lang('Name'), 'formatter' => 'YAHOO.booking.formatLink');
			if (isset($config->config_data['dim_3'])) $data['datatable']['field'][] = array('key' => 'object_number', 'label' => $config->config_data['dim_3']);
			if (isset($config->config_data['dim_1'])) $data['datatable']['field'][] = array('key' => 'responsible_code', 'label' => $config->config_data['dim_1']);
			if (isset($config->config_data['article'])) $data['datatable']['field'][] = array('key' => 'article', 'label' => lang('Article'));
			if (isset($config->config_data['dim_2'])) $data['datatable']['field'][] = array('key' => 'service', 'label' => $config->config_data['dim_2']);
			if (isset($config->config_data['dim_4'])) $data['datatable']['field'][] = array('key' => 'dim_4', 'label' => $config->config_data['dim_4']);
			if (isset($config->config_data['dim_5'])) $data['datatable']['field'][] = array('key' => 'project_number', 'label' => $config->config_data['dim_5']);
			if (isset($config->config_data['dim_value_1'])) $data['datatable']['field'][] = array('key' => 'unit_number', 'label' => $config->config_data['dim_value_1']);
			if (isset($config->config_data['dim_value_4'])) $data['datatable']['field'][] = array('key' => 'dim_value_4', 'label' => $config->config_data['dim_value_4']);
			if (isset($config->config_data['dim_value_5'])) $data['datatable']['field'][] = array('key' => 'dim_value_5', 'label' => $config->config_data['dim_value_5']);
			if ($config->config_data['external_format'] != 'KOMMFAKT') $data['datatable']['field'][] = array('key' => 'unit_prefix', 'label' => lang('Unit prefix'));
			$data['datatable']['field'][] = array('key' => 'link', 'hidden' => true);
			
			if ($this->bo->allow_create()) {
				array_unshift($data['form']['toolbar']['item'], array(
					'type' => 'link',
					'value' => lang('New Account Codes'),
					'href' => $this->link_to('add'),
				));
			}
			
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			$account_code_sets = $this->bo->read();
			array_walk($account_code_sets["results"], array($this, "_add_links"), $this->module.".uiaccount_code_set.show");
			//foreach($account_code_sets["results"] as &$account_code_set) {}
			
			$results = $this->yui_results($account_code_sets);
			
			return $results;
		}
		
		protected function add_default_display_data(&$account_code_set)
		{
			$account_code_set['edit_link'] = $this->link_to('edit', array('id' => $account_code_set['id']));
			$account_code_set['account_codes_link'] = $this->link_to('index');
		}
		
		public function show()
		{
			$account_code_set = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			$this->add_default_display_data($account_code_set);
			self::render_template('account_code_set', array('account_code_set' => $account_code_set, 'config_data' => $config->config_data));
		}
		
		public function edit() {
			$account_code_set = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$account_code_set = array_merge($account_code_set, extract_values($_POST, $this->fields));
				$errors = $this->bo->validate($account_code_set);
				if(!$errors)
				{
					try {
						$receipt = $this->bo->update($account_code_set);	
						$this->redirect_to('show', array('id' => $account_code_set['id']));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
				}
			}
			
			$this->add_default_display_data($account_code_set);
			$account_code_set['cancel_link'] = $this->link_to('show', array('id' => $account_code_set['id']));
			$this->flash_form_errors($errors);
			self::render_template('account_code_set_form', array('account_code_set' => $account_code_set, 'config_data' => $config->config_data));
		}
	
		public function add() {
			$account_code_set = array();
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();

			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$account_code_set = extract_values($_POST, $this->fields);
				$account_code_set['active'] = '1';
				if ($config->config_data['external_format'] == 'KOMMFAKT') {
					$account_code_set['article'] = '1';
					$account_code_set['service'] = '1';
					$account_code_set['project_number'] = '1';
					$account_code_set['unit_number'] = '1';
					$account_code_set['unit_prefix'] = '1';
				}
				
				$errors = $this->bo->validate($account_code_set);
				if(!$errors)
				{
					try {
						$receipt = $this->bo->add($account_code_set);
						$this->redirect_to('index');
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not add object due to insufficient permissions');
					}
				}
			}
			$this->add_default_display_data($account_code_set);
			if ($config->config_data['external_format'] != 'KOMMFAKT') {
				$account_code_set['project_number'] = '9';
			}
			$account_code_set['cancel_link'] = $this->link_to('index');
			$this->flash_form_errors($errors);
			self::render_template('account_code_set_form', array('new_form' => true, 'account_code_set' => $account_code_set , 'config_data' => $config->config_data));
		}
	}
