<?php
	phpgw::import_class('booking.uicommon');

	class booking_uigroup extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'add'			=>	true,
			'show'			=>	true,
			'edit'			=>	true
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bogroup');
			self::set_active_menu('booking::groups');
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
								'href' => self::link(array('menuaction' => 'booking.uigroup.add'))
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
					'source' => self::link(array('menuaction' => 'booking.uigroup.index', 'phpgw_return_as' => 'json')),
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
			foreach($groups['results'] as &$group)
			{
				$group['link'] = $this->link(array('menuaction' => 'booking.uigroup.show', 'id' => $group['id']));
			}
			$data = array
			(
				'ResultSet' => array(
					"totalResultsAvailable" => $groups['total_records'], 
					"Result" => $groups['results']
				)
			);
			return $data;
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$group = extract_values($_POST, array('name', 'organization_id', 'organization_name'));
				$errors = $this->bo->validate($group);
				if(!$errors)
				{
					$receipt = $this->bo->add($group);
					$this->redirect(array('menuaction' => 'booking.uigroup.show', 'id'=>$receipt['id']));
				}
			}
			$this->flash_form_errors($errors);
			$lang['title'] = lang('New Group');
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
			self::add_javascript('booking', 'booking', 'group_new.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('autocomplete');
			self::render_template('group_new', array('group' => $group, 'lang' => $lang));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$group = $this->bo->read_single($id);
			$group['id'] = $id;
			$group['organizations_link'] = self::link(array('menuaction' => 'booking.uiorganization.index'));
			$group['organization_link'] = self::link(array('menuaction' => 'booking.uiorganization.show', 'id' => $group['organization_id']));
            $group['contact_primary'] = $this->bo->get_contact_info($group['contact_primary']);
            $group['contact_secondary'] = $this->bo->get_contact_info($group['contact_secondary']);

			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$group = array_merge($group, extract_values($_POST, array('name', 'organization_id', 'organization_name', 'description', 'contact_primary', 'contact_secondary')));
				$errors = $this->bo->validate($group);
				if(!$errors)
				{
					$receipt = $this->bo->update($group);
					$this->redirect(array('menuaction' => 'booking.uigroup.show', 'id'=>$group['id']));
				}
			}
			$this->flash_form_errors($errors);

			self::add_stylesheet('phpgwapi/js/yahoo/assets/skins/sam/skin.css');
			self::add_javascript('yahoo', 'yahoo/yahoo-dom-event', 'yahoo-dom-event.js');
			self::add_javascript('yahoo', 'yahoo/element', 'element-min.js');
			self::add_javascript('yahoo', 'yahoo/container', 'container_core-min.js');
			self::add_javascript('yahoo', 'yahoo/editor', 'simpleeditor-min.js');

            self::add_javascript('booking', 'booking', 'group_new.js');

			self::render_template('group_edit', array('group' => $group, 'lang' => $lang));
		}
		
		public function show()
		{
			$group = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$group['organizations_link'] = self::link(array('menuaction' => 'booking.uiorganization.index'));
			$group['organization_link'] = self::link(array('menuaction' => 'booking.uiorganization.show', 'id' => $group['organization_id']));
			$group['edit_link'] = self::link(array('menuaction' => 'booking.uigroup.edit', 'id' => $group['id']));
            $group['contact_primary'] = $this->bo->get_contact_info($group['contact_primary']);
            $group['contact_secondary'] = $this->bo->get_contact_info($group['contact_secondary']);

			$data = array(
				'group'	=>	$group
			);
			self::render_template('group', array('group' => $group, ));
		}
	}
