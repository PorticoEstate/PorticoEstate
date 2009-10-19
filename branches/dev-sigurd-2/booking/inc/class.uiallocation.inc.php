<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.boorganization');

	class booking_uiallocation extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'add'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
			'toggle_show_inactive'	=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boallocation');
			$this->organization_bo    = CreateObject('booking.boorganization');
			self::set_active_menu('booking::applications::allocations');
			$this->fields = array('resources', 'cost',
								  'building_id', 'building_name', 
								  'season_id', 'season_name', 
			                      'organization_id', 'organization_name', 
			                      'from_', 'to_', 'active');
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
					'source' => self::link(array('menuaction' => 'booking.uiallocation.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'organization_name',
							'label' => lang('Organization'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'season_name',
							'label' => lang('Season')
						),
						array(
							'key' => 'from_',
							'label' => lang('From')
						),
						array(
							'key' => 'to_',
							'label' => lang('To')
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
			
			
			if ($this->bo->allow_create()) {
				array_unshift($data['form']['toolbar']['item'], array(
					'type' => 'link',
					'value' => lang('New allocation'),
					'href' => self::link(array('menuaction' => 'booking.uiallocation.add'))
				));
			}
			
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			$allocations = $this->bo->read();
			array_walk($allocations["results"], array($this, "_add_links"), "booking.uiallocation.show");
			return $this->yui_results($allocations);
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				$allocation = extract_values($_POST, $this->fields);
				$allocation['active'] = '1';
				$allocation['completed'] = '0';
				$errors = $this->bo->validate($allocation);
				if(!$errors)
				{
					try {
						$receipt = $this->bo->add($allocation);
						$this->redirect(array('menuaction' => 'booking.uiallocation.show', 'id'=>$receipt['id']));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not add object due to insufficient permissions');
					}
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'allocation.js');
			array_set_default($allocation, 'resources', array());
			$allocation['resources_json'] = json_encode(array_map('intval', $allocation['resources']));
			$allocation['cancel_link'] = self::link(array('menuaction' => 'booking.uiallocation.index'));
			array_set_default($allocation, 'cost', '0');
			self::render_template('allocation_new', array('allocation' => $allocation));
		}

		private function send_mailnotification_to_organization($organization, $subject, $body)
		{
			$send = CreateObject('phpgwapi.send');

			if (strlen(trim($body)) == 0) 
			{
				return false;
			}

			foreach($organization['contacts'] as $contact) 
			{
				if (strlen($contact['email']) > 0) 
				{
					$send->msg('email', $contact['email'], $subject, $body);
				}
			}
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$allocation = $this->bo->read_single($id);
			$allocation['id'] = $id;
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				$allocation = array_merge($allocation, extract_values($_POST, $this->fields));
				$organization = $this->organization_bo->read_single(intval(phpgw::get_var('organization_id', 'POST')));
				$errors = $this->bo->validate($allocation);
				if(!$errors)
				{
					try {
						$receipt = $this->bo->update($allocation);
						$this->send_mailnotification_to_organization($organization, lang('Allocation changed'), phpgw::get_var('mail', 'POST'));
						$this->redirect(array('menuaction' => 'booking.uiallocation.show', 'id'=>$allocation['id']));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'allocation.js');
			$allocation['resources_json'] = json_encode(array_map('intval', $allocation['resources']));
			$allocation['cancel_link'] = self::link(array('menuaction' => 'booking.uiallocation.show', 'id' => $allocation['id']));
			self::render_template('allocation_edit', array('allocation' => $allocation));
		}
		
		public function show()
		{
			$allocation = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$allocation['allocations_link'] = self::link(array('menuaction' => 'booking.uiallocation.index'));
			$allocation['edit_link'] = self::link(array('menuaction' => 'booking.uiallocation.edit', 'id' => $allocation['id']));
			$resource_ids = '';
			foreach($allocation['resources'] as $res)
			{
				$resource_ids = $resource_ids . '&filter_id[]=' . $res;
			}
			$allocation['resource_ids'] = $resource_ids;
			self::render_template('allocation', array('allocation' => $allocation));
		}
	}
