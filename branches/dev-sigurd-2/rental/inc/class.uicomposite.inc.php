<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.uidocument_composite');
	
	class rental_uicomposite extends rental_uicommon
	{	
		public $public_functions = array
		(
			'index'		=> true,
			'view'		=> true,
			'edit'		=> true,
			'columns'	=> true,
			'add'		=> true,
			'add_unit' => true,
			'remove_unit' => true,
			'query'		=> true
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('rental.bocomposite');
			self::set_active_menu('rental::composite');
		}

		/**
		 * Return a JSON result of rental composite related data
		 * 
		 * @param $composite_id  rental composite id
		 * @param $type	type of details
		 * @param $field_total the field name that holds the total number of records
		 * @param $field_result the field name that holds the query result
		 * @return 
		 */
		protected function json_query($composite_id = null, $type = 'index', $field_total = 'total_records', $field_results = 'results')
		{	
			/*  HTTP get variables:
			 * 
			 * sort: column to sort
			 * dir: direction (ascending, descending)
			 * startIndex: the index to start from in result
			 * results: number of rows to return
			 * level: (1-5) property to room
			 * contract_status: filter for contract status
			 * contract_date: filter for contract dates
			 */
			
			switch($type)
			{
				case 'index':
					$composite_data = $this->bo->read();
					break;
				case 'details':
					$composite_data = $this->bo->read_single(array('id' => $composite_id));
					break;
				case 'included_areas':
					$composite_data = $this->bo->get_included_rental_units(array('id' => $composite_id, 'sort' => phpgw::get_var('sort'), 'dir' => phpgw::get_var('dir'), 'start' => phpgw::get_var('startIndex'), 'results' => phpgw::get_var('results')));
					break;
				case 'available_areas':
					$composite_data = $this->bo->get_available_rental_units(array('id' => $composite_id, 'sort' => phpgw::get_var('sort'), 'dir' => phpgw::get_var('dir'), 'start' => phpgw::get_var('startIndex'), 'results' => phpgw::get_var('results'), 'level' => phpgw::get_var('level')));
					break;
				case 'contracts':
					$composite_data = $this->bo->get_contracts(array('id' => $composite_id, 'sort' => phpgw::get_var('sort'), 'dir' => phpgw::get_var('dir'), 'start' => phpgw::get_var('startIndex'), 'results' => phpgw::get_var('results'), 'contract_status' => phpgw::get_var('contract_status'), 'contract_date' => phpgw::get_var('contract_date')));
					break;
				
			}
			
			//Add action column to each row in result table
			array_walk($composite_data[$field_results], array($this, '_add_actions'), array($composite_id,$type));
			return $this->yui_results($composite_data, $field_total, $field_results);
		}

		/**
		 * Add action links for the context menu of the list item
		 * 
		 * @param $value pointer to 
		 * @param $key ?
		 * @param $params [composite_id, type of query]
		 */
		public function _add_actions(&$value, $key, $params)
		{
			switch($params[1])
			{
				case 'index':
					$value['actions'] = array(
						'view' => html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.view', 'id' => $value['id']))),
						'edit' => html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.edit', 'id' => $value['id'])))
					);
					break;
				case 'included_areas':
					$value['actions'] = array(
						'remove_unit' => html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.remove_unit', 'id' => $params[0], 'location_id' => $value['location_id'])))
					);
					break;
				case 'available_areas':
					$value['actions'] = array(
						'add_unit' => html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.add_unit', 'id' => $params[0], 'location_id' => $value['location_id'], 'location_code' => $value['location_code'])))
					);
					break;
				case 'contracts':
					$value['actions'] = array(
						'view_contract' => html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id']))),
						'edit_contract' => html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])))
					);
					break;	
			}
			
		}
		
		///View all rental composites
		public function index()
		{			
			self::add_javascript('rental', 'rental', 'rental.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			self::render_template('composite_list',$data);
		}

		//View rental composite
		public function view() {
			$composite_id = (int)phpgw::get_var('id');
			return $this -> viewedit(false, $composite_id);
		}
		
		//Edit rental composite
		public function edit(){
			$composite_id = (int)phpgw::get_var('id');
			return $this -> viewedit(true, $composite_id);
		}
		
		//Create new rental composite
		public function add()
		{
			$receipt = $this->bo->add(phpgw::get_var('rental_composite_name'));
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicomposite.edit', 'id' => $receipt['id'], 'message' => lang('rental_messages_new_composite')));	
		}
		
		//Common method for JSON queries
		public function query()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				if(phpgw::get_var('id') && $type = phpgw::get_var('type'))
				{
					$id = phpgw::get_var('id');
					$type = phpgw::get_var('type');
					return $this->json_query($id,$type);	
				} 
				else 
				{
					return $this->json_query();
				}
			}
		} 
		
		/**
		 * View or edit rental composite
		 * 
		 * @param $editable true renders fields editable, false renders fields disabled
		 * @param $composite_id	the rental composite id	
		 */
		protected function viewedit($editable, $composite_id)
		{
			if ($composite_id > 0) {

				$message = phpgw::get_var('message');
				$error = phpgw::get_var('error');
				
				self::add_javascript('rental', 'rental', 'rental.js');
				phpgwapi_yui::load_widget('datatable');
				phpgwapi_yui::load_widget('tabview');
				$params['id'] = $composite_id;
				$composite = $this->bo->read_single($params);
				
				$tabs = array();
				
				foreach(array('rental_rc_details', 'rental_rc_area', 'rental_rc_contracts') as $tab) {
					$tabs[$tab] =  array('label' => lang($tab), 'link' => '#' . $tab);
				}
				
				phpgwapi_yui::tabview_setup('composite_edit_tabview');

				$documents = array();
				
				$active_tab = phpgw::get_var('active_tab');
				if (($active_tab == null) || ($active_tab == '')) {
					$active_tab = 'rental_rc_details';
				}
				
				$data = array
				(
					'composite' 	=> $composite,
					'composite_id' => $composite_id,
					'tabs'	=> phpgwapi_yui::tabview_generate($tabs, $active_tab),
					'documents' => $documents,
					'access' => $editable,
					'message' => $message,
					'error' => $error,
					'cancel_link' => self::link(array('menuaction' => 'rental.uicomposite.index'))
				);
				
				$errors = array();
				if($_SERVER['REQUEST_METHOD'] == 'POST')
				{
					$composite = array_merge($composite, extract_values($_POST, array('name', 'gab_id', 'address_1', 'house_number', 'address_2', 'postcode', 'place', 'is_active', 'description')));
					$composite['is_active'] = $composite['is_active'] == 'on' ? true : false;

					if (isset($composite['address_1']) && trim($composite['address_1']) != '') {
						$composite['has_custom_address'] = '1';
					} else {
						$composite['has_custom_address'] = '0';
					}

					$errors = $this->bo->validate($composite);
					
					if(!$errors)
					{
						$receipt = $this->bo->update($composite);
						$this->redirect(array('menuaction' => 'rental.uicomposite.index'));
					}
				}
				$this->flash_form_errors($errors);

				$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/examples/treeview/assets/css/folders/tree.css');
				phpgwapi_yui::load_widget('treeview');
				
				self::render_template('composite', $data);
			}
		}
		
		//Add a unit to a rental composite
		function add_unit()
		{
			$composite_id = (int)phpgw::get_var('id');
			$composite = $this->bo->read_single(array('id' => $composite_id));
			
			if (($composite) != null) {
				$location_id = (int)phpgw::get_var('location_id');
				$loc1 = (int)phpgw::get_var('location_code');
				$this->bo->add_unit($composite_id, $location_id, $loc1);
			}
			
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicomposite.edit', 'id' => $composite_id, 'active_tab' => 'rental_rc_area'));
		}
		
		//Remove a unit from a rental composite
		function remove_unit()
		{
			$composite_id = (int)phpgw::get_var('id');
			$composite = $this->bo->read_single(array('id' => $composite_id));

			$location_id = (int)phpgw::get_var('location_id');
						
			if ($composite != null) {
				$this->bo->remove_unit($composite_id, $location_id);
			}
			
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicomposite.edit', 'id' => $composite_id, 'active_tab' => 'rental_rc_area'));
			
		}
				
		/**
		 * Stores which columns that should be displayed in index(). The data
		 * is stored per user.
		 *  
		 */
		function columns()
		{
			$values = phpgw::get_var('values');
			if (isset($values['save']) && $values['save'])
			{
				$GLOBALS['phpgw']->preferences->account_id=$GLOBALS['phpgw_info']['user']['account_id'];
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add('rental','rental_columns_composite',$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();
			}
		}
		
		
	}
?>