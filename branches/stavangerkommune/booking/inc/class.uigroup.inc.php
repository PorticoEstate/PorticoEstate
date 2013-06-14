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
			$this->activity_bo = CreateObject('booking.boactivity');
			self::set_active_menu('booking::groups');

            $this->module = "booking";
		}
		
		public function link_to_parent_params($action = 'show', $params = array())
		{
			return array_merge(array('menuaction' => sprintf($this->module.'.ui%s.%s', $this->get_current_parent_type(), $action), 'id' => $this->get_parent_id()), $params);
		}
		
		public function link_to_parent($action = 'show', $params = array())
		{
			return $this->link($this->link_to_parent_params($action, $params));
		}
		
		public function get_current_parent_type()
		{
			if (!$this->is_inline()) { return null; }
			$parts = explode('_', key($a = $this->get_inline_params()));
			return $parts[1];
		}
		
		public function get_parent_id()
		{
			$inlineParams = $this->get_inline_params();
			return $inlineParams['filter_organization_id'];
		}
		
		public function get_parent_if_inline()
		{
			if (!$this->is_inline()) return null;
			return CreateObject('booking.bo'.$this->get_current_parent_type())->read_single($this->get_parent_id());
		}
		
		public function redirect_to_parent_if_inline()
		{
			if ($this->is_inline())
			{
				$this->redirect($this->link_to_parent_params());
			}
			
			return false;
		}
		
		public function link_to($action, $params = array())
		{
			return $this->link($this->link_to_params($action, $params));
		}
		
		public function link_to_params($action, $params = array())
		{
			if (isset($params['ui'])) {
				$ui = $params['ui'];
				unset($params['ui']);
			} else {
				$ui = 'group';
				$this->apply_inline_params($params);
			}
			
			$action = sprintf($this->module.'.ui%s.%s', $ui, $action);
			return array_merge(array('menuaction' => $action), $params);
		}
		
		public function apply_inline_params(&$params)
		{
			if($this->is_inline()) {
				$params['filter_organization_id'] = intval(phpgw::get_var('filter_organization_id'));
			}
			return $params;
		}
		
		public function get_inline_params()
		{
			return array('filter_organization_id' => intval(phpgw::get_var('filter_organization_id', 'any', false)));
		}
		
		public function is_inline()
		{
			return false != phpgw::get_var('filter_organization_id', 'any', false);
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
							'key' => 'shortname',
							'label' => lang('Group shortname'),
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
			array_walk($groups["results"], array($this, "_add_links"), $this->module.".uigroup.show");
			foreach($groups["results"] as &$group) {
				
				$contact = (isset($group['contacts']) && isset($group['contacts'][0])) ? $group['contacts'][0] : null;
				$contact2 = (isset($group['contacts']) && isset($group['contacts'][1])) ? $group['contacts'][1] : null;
				
				if ($contact) {
					$group += array(
								"primary_contact_name"  => ($contact["name"])  ? $contact["name"] : '',
								"primary_contact_phone" => ($contact["phone"]) ? $contact["phone"] : '',
								"primary_contact_email" => ($contact["email"]) ? $contact["email"] : '',
					);
				}
				if ($contact2) {
					$group += array(
								"secondary_contact_name"  => ($contact2["name"])  ? $contact2["name"] : '',
								"secondary_contact_phone" => ($contact2["phone"]) ? $contact2["phone"] : '',
								"secondary_contact_email" => ($contact2["email"]) ? $contact2["email"] : '',
					);
				}
			}
			
			$results = $this->yui_results($groups);
			
			if (is_array($parent_entity = $this->get_parent_if_inline())) {
				if ($this->bo->allow_create(array($this->get_current_parent_type().'_id' => $parent_entity['id']))) {
					$results['Actions']['add'] = array('text' => lang('Add Group'), 'href' => $this->link_to('edit'));
				}
			}
			
			return $results;
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			if ($id)
			{
				$group = $this->bo->read_single($id);
				$group['id'] = $id;
				$group['organization_link'] = $this->link_to('show', array('ui' => 'organization', 'id' => $group['organization_id']));
				
				$group['cancel_link'] = $this->link_to('show', array('id' => $id));
				
				if ($this->is_inline())
				{
					$group['cancel_link'] = $this->link_to_parent();
				}
					
			} else {
				$group = array();
				$group['cancel_link'] = $this->link_to('index', array('ui' => 'organization'));
				
				if ($this->is_inline())
				{
					$group['organization_link'] = $this->link_to_parent();
					$group['cancel_link'] = $this->link_to_parent();
					$this->apply_inline_params($group);
				}
			}

			$group['organizations_link'] = $this->link_to('index', array('ui' => 'organization'));

			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$group = array_merge($group, extract_values($_POST, array('name', 'shortname', 'organization_id', 'organization_name', 'description', 'contacts', 'active', 'activity_id', 'show_in_portal')));
				if (!isset($group["active"]))
				{
					$group['active'] = '1';
				}
	
				$errors = $this->bo->validate($group);
				if(strlen($_POST['name']) > 50){
					$errors['name'] = lang('Lengt of name is to long, max 50 characters long');
				}
				if(strlen($_POST['shortname']) > 11){
					$errors['shortname'] = lang('Lengt of shortname is to long, max 11 characters long');
				}
				if(!$errors)
				{
					if ($id)
					{
						$receipt = $this->bo->update($group);
					} else {
						$receipt = $this->bo->add($group);
					}
					
					$this->redirect_to_parent_if_inline();
					$this->redirect($this->link_to_params('show', array('id'=>$receipt['id'])));
				}
			}
			$this->flash_form_errors($errors);
			
			if (is_array($parent_entity = $this->get_parent_if_inline()))
			{
				$group[$this->get_current_parent_type().'_id'] = $parent_entity['id'];
				$group[$this->get_current_parent_type().'_name'] = $parent_entity['name'];
			}

			$this->use_yui_editor();
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			self::render_template('group_edit', array('group' => $group, 'module' => $this->module, 'activities' => $activities));
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
