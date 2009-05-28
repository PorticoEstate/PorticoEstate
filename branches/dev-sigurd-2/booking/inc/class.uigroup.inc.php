<?php
	phpgw::import_class('booking.uicommon');

	class booking_uigroup extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
			'toggle_show_inactive'	=>	true,
		);

        protected $module;
		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bogroup');
			self::set_active_menu('booking::groups');

            $this->module = "booking";
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
								'href' => self::link(array('menuaction' => 'booking.uigroup.edit'))
							),
							array('type' => 'text', 
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix.'.toggle_show_inactive'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uigroup.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'organization_name',
							'label' => lang('Organization')
						),
						array(
							'key' => 'name',
							'label' => lang('Group'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'primary_contact_name',
							'label' => lang('Primary contact'),
						),
						array(
							'key' => 'primary_contact_phone',
							'label' => lang('Phone'),
						),
						array(
							'key' => 'primary_contact_email',
							'label' => lang('Email'),
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
			array_walk($groups["results"], array($this, "_add_links"), "booking.uigroup.show");
			foreach($groups["results"] as &$group) {
				$group += array(
							"primary_contact_name"  => (@$person['contacts'][0]["name"])  ? $person['contacts'][0]["name"] : '',
							"primary_contact_phone" => (@$person['contacts'][0]["phone"]) ? $person['contacts'][0]["phone"] : '',
							"primary_contact_email" => (@$person['contacts'][0]["email"]) ? $person['contacts'][0]["email"] : '',
				);
			}
			return $this->yui_results($groups);
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			if ($id)
			{
				$group = $this->bo->read_single($id);
				$group['id'] = $id;
				$group['organizations_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.index'));
				$group['organization_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.show', 'id' => $group['organization_id']));
			} else {
				$group = array();
			}

			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$group = array_merge($group, extract_values($_POST, array('name', 'organization_id', 'organization_name', 'description', 'contacts', 'active')));
				if (!isset($group["active"]))
				{
					$group['active'] = '1';
				}
				
				$errors = $this->bo->validate($group);
				if(!$errors)
				{
					if ($id)
					{
						$receipt = $this->bo->update($group);
					} else {
						$receipt = $this->bo->add($group);
					}
					$this->redirect(array('menuaction' => $this->module . '.uigroup.show', 'id'=>$receipt['id']));
				}
			}
			$this->flash_form_errors($errors);
			
			$group['cancel_link'] = $id ? self::link(array('menuaction' => $this->module . '.uigroup.show', 'id'=> $id)) :
										  self::link(array('menuaction' => $this->module . '.uigroup.index'));

			self::add_stylesheet('phpgwapi/js/yahoo/assets/skins/sam/skin.css');
			self::add_javascript('yahoo', 'yahoo/yahoo-dom-event', 'yahoo-dom-event.js');
			self::add_javascript('yahoo', 'yahoo/element', 'element-min.js');
			self::add_javascript('yahoo', 'yahoo/container', 'container_core-min.js');
			self::add_javascript('yahoo', 'yahoo/editor', 'simpleeditor-min.js');

			self::render_template('group_edit', array('group' => $group, 'module' => $this->module));
		}
		
		public function show()
		{
			$group = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$group['organizations_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.index'));
			$group['organization_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.show', 'id' => $group['organization_id']));
			$group['edit_link'] = self::link(array('menuaction' => $this->module . '.uigroup.edit', 'id' => $group['id']));
			
			$data = array(
				'group'	=>	$group
			);
			$loggedin = (int) true; // FIXME: Some sort of authentication!
			$edit_self_link   = self::link(array('menuaction' => 'bookingfrontend.uigroup.edit', 'id' => $group['id']));

			self::render_template('group', array('group' => $group, 'loggedin' => $loggedin, 'edit_self_link' => $edit_self_link));
		}
	}
