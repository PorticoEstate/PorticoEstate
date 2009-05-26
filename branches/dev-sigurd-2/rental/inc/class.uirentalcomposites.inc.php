<?php
	phpgw::import_class('rental.uicommon');

	class rental_uirentalcomposites extends rental_uicommon
	{
		
		public $public_functions = array
		(
			'index'	=> true,
			'edit' => true
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
			
			// TODO: Use this to add links: array_walk($compositeArray["results"], array($this, "_add_links"), "booking.uibooking.show");
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
			// XXX: Change the 'toolbar' for this module - it's only kept like it is as an example on how we can do it 
			$data = array(
				'form' => array(
					'toolbar' => array(
					 	'control1' => array(
					 			'control' => 'input',
					 			'id' => 'ctrl_add_rental_composite',
								'type' => 'link',
					 			'name' => 'name',
								'value' => lang('New rental composite'),
								'href' => self::link(array('menuaction' => 'rental.uirentalcomposites.add'))
							), 
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
					),
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
						)
					)
				)
			);
			
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
	}
?>