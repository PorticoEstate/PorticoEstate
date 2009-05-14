<?php
	phpgw::import_class('booking.uigroup');

	class bookingfrontend_uigroup extends booking_uigroup
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'edit'			=>	true,
            'show'          =>  true,
		);

        protected $module;
		public function __construct()
		{
			parent::__construct();
            $this->module = "bookingfrontend";
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('New group'),
								'href' => self::link(array('menuaction' => 'bookingfrontend.uigroup.edit'))
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
					'source' => self::link(array('menuaction' => 'bookingfrontend.uigroup.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'organization_name',
							'label' => lang('Organization name')
						),
						array(
							'key' => 'name',
							'label' => lang('Group Name'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			$groups = $this->bo->read();
			array_walk($groups["results"], array($this, "_add_links"), "bookingfrontend.uigroup.show");
			return $this->yui_results($groups);
		}

		
	}

