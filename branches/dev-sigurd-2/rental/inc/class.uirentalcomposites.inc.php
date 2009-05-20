<?php
	phpgw::import_class('rental.uicommon');

	class rental_uirentalcomposites extends rental_uicommon
	{
		public $public_functions = array
		(
			'index'	=> true
		);

		public function __construct()
		{
			parent::__construct();			
			$this->bo = CreateObject('rental.borentalcomposites');
			// XXX: Make  the keys match the keys from the database
			$this->fields = array('name', 'resources',
								  'building_id', 'building_name', 
								  'season_id', 'season_name', 
			                      'group_id', 'group_name', 
			                      'from_', 'to_', 'audience');
			self::set_active_menu('rental::rentalcomposites');
		}

		public function index_json()
		{
			$bookings = $this->bo->read();
			array_walk($bookings["results"], array($this, "_add_links"), "booking.uibooking.show");
			return $this->yui_results($bookings);
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
				// XXX: Make  the keys match the keys from the database
					'field' => array(
						array(
							'key' => 'number',
							'label' => lang('Number'),
							'formatter' => 'YAHOO.rental.formatLink'
						),
						array(
							'key' => 'address1',
							'label' => lang('Address')
						),
						array(
							'key' => 'property_name',
							'label' => lang('Property name')
						),
						array(
							'key' => 'property_id',
							'label' => lang('Property id') // 'Gårdsnummer'
						),
						array(
							'key' => 'type',
							'label' => lang('Type')
						)
					)
				)
			);
			self::render_template('datatable', $data);
		}						

	}
?>