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
			'add'		=> true
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('rental.bocomposite');
			self::set_active_menu('rental::composite');
		}

		protected function index_json()
		{
			$compositeArray = $this->bo->read();
			
			array_walk($compositeArray['results'], array($this, '_add_actions'));
			return $this->yui_results($compositeArray);
		}

		protected function view_json($composite_id)
		{
			$params = array
			(
				'id' => $composite_id
			);
			$composite = $this->bo->read_single($params);
			//var_dump($composite);
			return $this->yui_results($composite);
		}

		/*
		 * Add action links for the context menu of the list item
		 */
		public function _add_actions(&$value, $key)
		{
			// TODO: Should check permissions
			$value['actions'] = array(
				// Remove &amp; from the link before storing it since it will be used in a Javascript forward
				'view' => html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.view', 'id' => $value['id']))),
				'edit' => html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.edit', 'id' => $value['id'])))
			);
		}
		
		/**
		 * Frontpage for the rental composites. Displays the list of all rental composites.
		 * 
		 */
		public function index()
		{			
			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->index_json();
			}
			
			self::add_javascript('rental', 'rental', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			// User stored columns:
			$columnArray = $GLOBALS['phpgw_info']['user']['preferences']['rental']['rental_columns_composite'];
			$data = array(
				'form' => array(
					'toolbar4' => array(
						'toolbar' => true,
						'label' => lang('rental_rc_toolbar_new'),
						'control6' => array(
					 			'control' => 'input',
					 			'id' => 'ctrl_add_rental_composite_name',
								'type' => 'text',
					 			'name' => 'rental_composite_name',
								'text' => lang('rental_rc_name')
						),
						'control1' => array(
					 			'control' => 'input',
					 			'id' => 'ctrl_add_rental_composite',
								'type' => 'button',
					 			'name' => 'name',
								'value' => lang('rental_rc_toolbar_functions_new_rc'),
						),
					),
					'toolbar1' => array(
						'toolbar' => true,
						'label' => lang('rental_rc_toolbar_functions'),
						'control5' => array(
				 			'control' => 'input',
							'id' => 'dt-options-link',
							'type' => 'button',
							'value' => lang('rental_rc_toolbar_functions_select_columns'),
							'href' => '#'
						)
					),
					'toolbar2' => array(
						'toolbar' => true,
						'label' => lang('rental_rc_search_options'),
						'control2' => array(
								'control' => 'input',
								'id' => 'ctrl_search_query',
								'type' => 'text', 
								'name' => 'query',
								'text' => lang('rental_rc_search_for')
							),
						'control3' => array(
								'control' => 'select',
								'id' => 'ctrl_search_option',
								'name' => 'search_option',
								'keys' => array('all','id','name','address','gab','ident','property_id'),
								'values' => array(lang('rental_rc_all'),lang('rental_rc_serial'),lang('rental_rc_name'),lang('rental_rc_address'),lang('rental_rc_gab'),lang('rental_rc_land_title'),lang('rental_rc_property_id')),
								'default' => 'all',
								'text' => lang('rental_rc_search_where')
							),
						'control4' => array(
								'control' => 'input',
								'id' => 'ctrl_search_button',
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('rental_rc_search')
							)
					),
					'toolbar3' => array(
						'toolbar' => true,
						'label' => lang('rental_rc_toolbar_filters'),
						'control1' => array(
					 			'control' => 'select',
					 			'id' => 'ctrl_toggle_active_rental_composites',
								'name' => 'is_active',
								'keys' => array('active','non_active','both'),
								'values' => array(lang('rental_rc_available'),lang('rental_rc_not_available'),lang('rental_rc_all')),
								'default' => 'both',
								'text' => lang('rental_rc_availibility')
						)
						)
					),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'rental.uicomposite.index', 'phpgw_return_as' => 'json')),
					'columns' => self::link(array('menuaction' => 'rental.uicomposite.columns', 'phpgw_return_as' => 'json')), // URL to store select columns
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('rental_rc_serial'),
							'sortable' => true,
							'hidden' => (!isset($columnArray) ? false : (!is_array($columnArray) ? false : !in_array('id', $columnArray))) // Not hidden if setting isn't set or if the user has selected the column earlier
						),
						array(
							'key' => 'actions',
							'label' => 'unselectable', // To hide it from the column selector
							'hidden' => true
						),
						array(
							'key' => 'name',
							'label' => lang('rental_rc_name'),
							'sortable' => true,
							'hidden' => (!isset($columnArray) ? false : (!is_array($columnArray) ? false : !in_array('name', $columnArray))) // Not hidden if setting isn't set or if the user has selected the column earlier
						),
						array(
							'key' => 'adresse1',
							'label' => lang('rental_rc_address'),
							'sortable' => false,
							'hidden' => (!isset($columnArray) ? false : (!is_array($columnArray) ? false : !in_array('adresse1', $columnArray))) // Not hidden if setting isn't set or if the user has selected the column earlier
						),
						array(
							'key' => 'gab_id',
							'label' => lang('rental_rc_propertyident'), // 'Gårds-/bruksnummer'
							'sortable' => true,
							'hidden' => (!isset($columnArray) ? false : (!is_array($columnArray) ? false : !in_array('gab_id', $columnArray))) // Not hidden if setting isn't set or if the user has selected the column earlier
						)
					)
				)
			);
//			var_dump((!isset($columnArray) ? false : (!is_array($columnArray) ? false : !in_array('name', $columnArray))));
//			var_dump($columnArray);
			self::render_template('datatable', $data);
		}						

		/**
		 * Show details for a single rental composite
		 */
		public function view() {
			// TODO: authorization check?
			$composite_id = (int)phpgw::get_var('id');
			return $this -> viewedit(false, $composite_id);
		}
		
		/**
		 * Edit details for a single rental composite
		 */
		public function edit(){
			// TODO: authorization check 
			$composite_id = (int)phpgw::get_var('id');
			return $this -> viewedit(true, $composite_id);
		}
		
		public function add()
		{
			$receipt = $this->bo->add(phpgw::get_var('rental_composite_name'));
			$GLOBALS['phpgw']->redirect_link('/index.php?menuaction=rental.uicomposite.edit&id='.$receipt['id']);			
		}
		
		
		/**
		 * Handling details 
		 * @param $access true renders fields editable, false renders fields disabled
		 */
		protected function viewedit($access, $composite_id)
		{
			phpgwapi_yui::load_widget('tabview');
			// TODO: How to check for valid input here?
			if ($composite_id > 0) {
				if(phpgw::get_var('phpgw_return_as') == 'json')
				{
					return $this->view_json($composite_id);
				}
				self::add_javascript('rental', 'rental', 'datatable.js');
				phpgwapi_yui::load_widget('datatable');
				phpgwapi_yui::load_widget('tabview');
				$params['id'] = $composite_id;
				$composite = $this->bo->read_single($params);
				
				$tabs = array();
				
				foreach(array('rental_rc_details', 'rental_rc_elements', 'rental_rc_contracts', 'rental_rc_documents') as $tab) {
					$tabs[$tab] =  array('label' => lang($tab), 'link' => '#' . $tab);
				}
				
				phpgwapi_yui::tabview_setup('composite_edit_tabview');

				
				$documents = array();
				if($location_code)
				{
					$related = $this->bo->read_entity_to_link($location_code);
					$related_link = array();
	
					$document = CreateObject('rental.sodocument');
					$documents = $document->get_files_at_location($location_code);
	
					if($documents)
					{
						$tabs['document']	= array('label' => lang('document'), 'link' => '#document');
						$documents = json_encode($documents);				
					}
					
					if(isset($related['related']))
					{
							$tabs['related']	= array('label' => lang('related'), 'link' => '#related');
					}
	
					foreach($related as $related_key => $related_data)
					{
						if( $related_key == 'gab')
						{
							foreach($related_data as $entry)
							{
								$entities_link[] = array
								(
									'entity_link'				=> $entry['entity_link'],
									'lang_entity_statustext'	=> $entry['descr'],
									'text_entity'				=> $entry['name'],
								);
							}
						}
	
						if( $related_key == 'related')
						{
							foreach($related_data as $entry)
							{
								$related_link[] = array
								(
									'entity_link'				=> $entry['entity_link'],
									'lang_entity_statustext'	=> $entry['descr'],
									'text_entity'				=> $entry['name'],
								);
							}
						}
					}
				}
				
				$data = array
				(
					'data' 	=> $composite,
					'tabs'	=> phpgwapi_yui::tabview_generate($tabs, 'rental_rc_details'),
					'documents' => $documents,
					'access' => $access,
					'datatable' => array(
						'source' => self::link(array('menuaction' => 'rental.uicomposite.view', 'phpgw_return_as' => 'json', 'id' => $composite_id)),
						'field' => array(
							array(
								'key' => 'location_code',
								'label' => lang('rental_rc_id'),
								'sortable' => true
							),
							// $names_to_look_for_array).", {$address_column}, name, fm_location{$unit['level']}.{$area_column_gros}, fm_location{$unit['level']}.{$area_column_net}
							array(
								'key' => 'loc1_name',
								'label' => lang('rental_rc_property'),
								'sortable' => true
							),
							array(
								'key' => 'loc2_name',
								'label' => lang('rental_rc_building'),
								'sortable' => true
							),
							array(
								'key' => 'loc3_name',
								'label' => lang('rental_rc_section'),
								'sortable' => true
							),
							array(
								'key' => 'address',
								'label' => lang('rental_rc_address'),
								'sortable' => true
							),
							array(
								'key' => 'area_gros',
								'label' => lang('rental_rc_area_gros'),
								'sortable' => true
							),
							array(
								'key' => 'area_net',
								'label' => lang('rental_rc_area_net'),
								'sortable' => true
							)
						)
					),
					'cancel_link' => self::link(array('menuaction' => 'rental.uicomposite.index')),
					'submit_link' => self::link(array('menuaction' => 'rental.uicomposite.edit'))
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
				
				self::render_template('rentalcomposite_edit', $data);
			}
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
