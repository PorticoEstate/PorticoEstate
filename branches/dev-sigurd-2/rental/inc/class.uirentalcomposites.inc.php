<?php
	phpgw::import_class('rental.uicommon');

	class rental_uirentalcomposites extends rental_uicommon
	{
		
		public $public_functions = array
		(
			'index'		=> true,
			'edit'		=> true,
			'columns'	=> true,
		);

		public function __construct()
		{
			parent::__construct();			
			$this->bo = CreateObject('rental.borentalcomposites');
			self::set_active_menu('rental::rentalcomposites');
		}

		public function index_json()
		{
			$compositeArray = $this->bo->read();
			
			array_walk($compositeArray['results'], array($this, '_add_actions'), 'rental.uirentalcomposites.edit');
			return $this->yui_results($compositeArray);
		}

		/*
		 * Add action links for the context menu of the list item
		 */
		public function _add_actions(&$value, $key, $menuaction)
		{
			$value['actions'] = array(
				// Remove &amp; from the link before storing it since it will be used in a Javascript forward
				'edit' => html_entity_decode(self::link(array('menuaction' => $menuaction, 'id' => $value['composite_id'])))
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
			$columnArray = $GLOBALS['phpgw_info']['user']['preferences']['rental']['rental_columns_rentalcomposites'];
			$data = array(
				'form' => array(
					'toolbar1' => array(
						'toolbar' => true,
						'label' => lang('Functions'),
						'control1' => array(
					 			'control' => 'input',
					 			'id' => 'ctrl_add_rental_composite',
								'type' => 'button',
					 			'name' => 'name',
								'value' => lang('New rental composite'),
								'href' => self::link(array('menuaction' => 'rental.uirentalcomposites.add'))
						)
					),
					'toolbar2' => array(
						'toolbar' => true,
						'label' => lang('Search options'),
						'control2' => array(
								'control' => 'input',
								'id' => 'ctrl_search_query',
								'type' => 'text', 
								'name' => 'query'
							),
						'control3' => array(
								'control' => 'select',
								'id' => 'ctrl_search_option',
								'name' => 'search_option',
								'keys' => array('all','id','name','address','gab'),
								'values' => array(lang('All'),lang('Id'),lang('Name'),lang('Address'),lang('GAB')),
								'default' => 'all',
								'text' => '',
							),
						'control4' => array(
								'control' => 'input',
								'id' => 'ctrl_search_button',
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							)
					),
					'toolbar3' => array(
						'toolbar' => true,
						'label' => lang('Filters'),
						'control1' => array(
					 			'control' => 'select',
					 			'id' => 'ctrl_toggle_active_rental_composites',
								'name' => 'is_active',
								'keys' => array('active','non_active','both'),
								'values' => array(lang('Active'),lang('Not active'),lang('Both')),
								'default' => 'active',
								'text' => '',
						)
						)
					),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'rental.uirentalcomposites.index', 'phpgw_return_as' => 'json')),
					'columns' => self::link(array('menuaction' => 'rental.uirentalcomposites.columns', 'phpgw_return_as' => 'json')), // URL to store select columns
					'field' => array(
						array(
							'key' => 'composite_id',
							'label' => lang('Number'),
							'sortable' => true,
							'hidden' => (!isset($columnArray) ? false : (!is_array($columnArray) ? false : !in_array('composite_id', $columnArray))) // Not hidden if setting isn't set or if the user has selected the column earlier
						),
						array(
							'key' => 'actions',
							'label' => 'unselectable', // To hide it from the column selector
							'hidden' => true
						),
						array(
							'key' => 'name',
							'label' => lang('Name'),
							'sortable' => true,
							'hidden' => (!isset($columnArray) ? false : (!is_array($columnArray) ? false : !in_array('name', $columnArray))) // Not hidden if setting isn't set or if the user has selected the column earlier
						),
						array(
							'key' => 'adresse1',
							'label' => lang('Address'),
							'sortable' => false,
							'hidden' => (!isset($columnArray) ? false : (!is_array($columnArray) ? false : !in_array('adresse1', $columnArray))) // Not hidden if setting isn't set or if the user has selected the column earlier
						),
						array(
							'key' => 'gab_id',
							'label' => lang('Property id'), // 'Gårds-/bruksnummer'
							'sortable' => true,
							'hidden' => (!isset($columnArray) ? false : (!is_array($columnArray) ? false : !in_array('gab_id', $columnArray))) // Not hidden if setting isn't set or if the user has selected the column earlier
					'datatable' => array(
						'source' => self::link(array('menuaction' => 'rental.uirentalcomposites.index', 'phpgw_return_as' => 'json')),
						'field' => array(
							array(
								'key' => 'composite_id',
								'label' => lang('Number'),
								'sortable' => true
							),
							array(
								'key' => 'actions',
								'hidden' => true
							),
							array(
								'key' => 'name',
								'label' => lang('Name'),
								'sortable' => true
							),
							array(
								'key' => 'adresse1',
								'label' => lang('Address'),
								'sortable' => false
							),
							array(
								'key' => 'gab_id',
								'label' => lang('Property id'), // 'Gårds-/bruksnummer'
								'sortable' => true
>>>>>>> .r2827
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
		 * 
		 */
		public function edit()
		{
			phpgwapi_yui::load_widget('tabview');

			$composite_id = phpgw::get_var('id');
			// TODO: How to check for valid input here?
			if ($composite_id) {
				$composite = $this->bo->read_single($composite_id);
				
				$tabs = array();
				
				foreach(array('details', 'elements', 'contracts', 'document') as $tab) {
					$tabs[$tab] =  array('label' => lang($tab), 'link' => '#' . $tab);
				}
				
				phpgwapi_yui::tabview_setup('composite_edit_tabview');

				$data = array
				(
					'data' 	=> $composite,
					'tabs'	=> phpgwapi_yui::tabview_generate($tabs, 'details')
				);

				self::render_template('rentalcomposite_edit', $data);
			}
		}
		
		/**
		 * 
		 */
		function columns()
		{
			$values = phpgw::get_var('values');
			if (isset($values['save']) && $values['save'])
			{
				$GLOBALS['phpgw']->preferences->account_id=$GLOBALS['phpgw_info']['user']['account_id'];
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add('rental','rental_columns_rentalcomposites',$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();
			}
		}
	}
?>