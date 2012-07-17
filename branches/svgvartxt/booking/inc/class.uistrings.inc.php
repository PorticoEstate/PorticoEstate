<?php
	phpgw::import_class('booking.uicommon');

	class booking_uistrings extends booking_uicommon
	{
		protected
			$module;
		
		public 
			$public_functions = array(
				'index'			=> true,
				'show'			=> true,
				'add'			=> true,
				'edit'			=> true,
				'download'		=> true,
				'delete'		=> true,
			);
		
		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bostrings');
			$this->fields = array('id', 'name', 'contents' );
			$this->module = 'booking';
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			
			// if($_SESSION['showall'])
			// {
			// 	$active_botton = lang('Show only active');
			// }else{
			// 	$active_botton = lang('Show all');
			// }
			
						
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
							// array(
							// 	'type' => 'link',
							// 	'value' => $active_botton,
							// 	'href' => self::link(array('menuaction' => $this->get_owner_typed_link('active')))
							// ),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uidocumentation.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Document Name'),
							'formatter' => 'YAHOO.booking.formatLink',
						),
						array(
							'key' => 'description',
							'label' => lang('Description'),
						),
						array(
							'key' => 'category',
							'label' => lang('Category'),
						),
						array(
							'key' => 'actions',
							'label' => lang('Actions'),
							'formatter' => 'YAHOO.booking.'.sprintf('formatGenericLink(\'%s\', \'%s\')', lang('edit'), lang('delete')),
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
			
			
				array_unshift($data['form']['toolbar']['item'], array(
					'type' => 'link',
					'value' => lang('New document'),
					'href' => self::link(array('menuaction' => $this->module.'.uidocumentation.add')),
			 	));
			
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
		}
	}
