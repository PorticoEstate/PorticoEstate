<?php
	phpgw::import_class('rental.uicommon');

	class rental_uirentalcomposites extends rental_uicommon
	{
		public $public_functions = array
		(
			'index'	=> true,
			'show' => true
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
			
			array_walk($compositeArray['results'], array($this, '_add_actions'), 'rental.uirentalcomposites.show');
			
			// TODO: Use this to add links: array_walk($compositeArray["results"], array($this, "_add_links"), "booking.uibooking.show");
			return $this->yui_results($compositeArray);
		}

		public function _add_actions(&$value, $key, $menuaction)
		{
			$value['actions'] = array(
				"show" => self::link(array('menuaction' => $menuaction, 'id' => $value['composite_id']))
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
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('New booking'),
								'href' => self::link(array('menuaction' => 'booking.uibooking.add'))
							),
							array('type' => 'text', 
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'rental.uirentalcomposites.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'actions',
							'hidden' => true
						),
						array(
							'key' => 'composite_id',
							'hidden' => true
						),
						array(
							'key' => 'name',
							'label' => lang('Name'),
							'sortable' => true
// TODO: Add link:							'formatter' => 'YAHOO.rental.formatLink'
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
		public function show()
		{
			
		}
	}
?>