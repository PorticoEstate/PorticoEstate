<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiorganization extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'index_json'		=>	true,
			'add'			=>	true,
			'edit'			=>	true,
			'show'			=>	true,
			'datatable'		=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boorganization');
			self::set_active_menu('booking::organizations');
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
								'value' => lang('New organization'),
								'href' => self::link(array('menuaction' => 'booking.uiorganization.add'))
							),
							array(
								'type' => 'text',
								'name' => 'q'
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
					'source' => self::link(array('menuaction' => 'booking.uiorganization.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Name'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'homepage'
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
			$organizations = $this->bo->read();
			foreach($organizations['results'] as &$organization)
			{
				$organization['link'] = $this->link(array('menuaction' => 'booking.uiorganization.show', 'id' => $organization['id']));
			}
			$data = array
			(
				'ResultSet' => array(
					"totalResultsAvailable" => $organizations['total_records'], 
					"Result" => $organizations['results']
				)
			);
			return $data;
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$organization = extract_values($_POST, array('name', 'homepage', 'phone', 'email', 'description'));
				$errors = $this->bo->validate($organization);
				if(!$errors)
				{
					$receipt = $this->bo->add($organization);
					$this->redirect(array('menuaction' => 'booking.uiorganization.index'));
				}
			}
			$this->flash_form_errors($errors);
			$organization['cancel_link'] = self::link(array('menuaction' => 'booking.uiorganization.index',));
			self::add_stylesheet('phpgwapi/js/yahoo/assets/skins/sam/skin.css');
			self::add_javascript('yahoo', 'yahoo/yahoo-dom-event', 'yahoo-dom-event.js');
			self::add_javascript('yahoo', 'yahoo/element', 'element-min.js');
			self::add_javascript('yahoo', 'yahoo/container', 'container_core-min.js');
			self::add_javascript('yahoo', 'yahoo/editor', 'simpleeditor-min.js');

			self::render_template('organization_edit', array('organization' => $organization, "save_or_create_text" => "Create"));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$organization = $this->bo->read_single($id);
			$organization['id'] = $id;
			$organization['organizations_link'] = self::link(array('menuaction' => 'booking.uiorganization.index'));
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$organization = array_merge($organization, extract_values($_POST, array('name', 'homepage', 'phone', 'email', 'description')));
				$errors = $this->bo->validate($organization);
				if(!$errors)
				{
					$receipt = $this->bo->update($organization);
					$this->redirect(array('menuaction' => 'booking.uiorganization.index'));
				}
			}
			$this->flash_form_errors($errors);
			$this->flash_form_errors($errors);
			$organization['cancel_link'] = self::link(array('menuaction' => 'booking.uiorganization.show', 'id' => $id));

			self::add_stylesheet('phpgwapi/js/yahoo/assets/skins/sam/skin.css');
			self::add_javascript('yahoo', 'yahoo/yahoo-dom-event', 'yahoo-dom-event.js');
			self::add_javascript('yahoo', 'yahoo/element', 'element-min.js');
			self::add_javascript('yahoo', 'yahoo/container', 'container_core-min.js');
			self::add_javascript('yahoo', 'yahoo/editor', 'simpleeditor-min.js');

			self::render_template('organization_edit', array('organization' => $organization, "save_or_create_text" => "Save"));
		}
		
		public function show()
		{
			$organization = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$organization['organizations_link'] = self::link(array('menuaction' => 'booking.uiorganization.index'));
			$organization['edit_link'] = self::link(array('menuaction' => 'booking.uiorganization.edit', 'id' => $organization['id']));
			$lang['title'] = lang('Edit Organization');
			$lang['buildings'] = lang('Buildings');
			$lang['name'] = lang('Name');
			$lang['description'] = lang('Description');
			$lang['building'] = lang('Building');
			$lang['organization'] = lang('Organization');
			$lang['group'] = lang('Group');
			$lang['from'] = lang('From');
			$lang['to'] = lang('To');
			$lang['season'] = lang('Season');
			$lang['date'] = lang('Date');
			$lang['resources'] = lang('Resources');
			$lang['select-building-first'] = lang('Select a building first');
			$lang['telephone'] = lang('Telephone');
			$lang['email'] = lang('Email');
			$lang['homepage'] = lang('Homepage');
			$lang['address'] = lang('Address');
			$lang['save'] = lang('Save');
			$lang['create'] = lang('Create');
			$lang['cancel'] = lang('Cancel');
			$lang['edit'] = lang('Edit');
			self::render_template('organization', array('organization' => $organization, 'lang' => $lang));
		}
	}
