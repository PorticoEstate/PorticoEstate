<?php
	phpgw::import_class('booking.uicommon');

	class booking_uigroup extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
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
			array_walk($groups["results"], array($this, "_add_links"), "booking.uigroup.show");
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
				$group['contact_primary'] = $this->bo->get_contact_info($group['contact_primary']);
				$group['contact_secondary'] = $this->bo->get_contact_info($group['contact_secondary']);
			} else {
				$group = array();
			}

			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$group = array_merge($group, extract_values($_POST, array('name', 'organization_id', 'organization_name', 'description', 'contact_primary', 'contact_secondary')));
				if (empty($group["contact_primary"]))
				{
					unset($group["contact_primary"]);
				}
				if (empty($group["contact_secondary"]))
				{
					unset($group["contact_secondary"]);
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

			$contact_form_link = self::link(array('menuaction' => $this->module . '.uicontactperson.edit', ));

			self::add_stylesheet('phpgwapi/js/yahoo/assets/skins/sam/skin.css');
			self::add_javascript('yahoo', 'yahoo/yahoo-dom-event', 'yahoo-dom-event.js');
			self::add_javascript('yahoo', 'yahoo/element', 'element-min.js');
			self::add_javascript('yahoo', 'yahoo/container', 'container_core-min.js');
			self::add_javascript('yahoo', 'yahoo/editor', 'simpleeditor-min.js');


			self::add_template_file("contactperson_fields");
			self::add_template_file("contactperson_magic");
			self::render_template('group_edit', array('group' => $group, 'module' => $this->module, 'contact_form_link' => $contact_form_link));
		}
		
		public function show()
		{
			$group = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$group['organizations_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.index'));
			$group['organization_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.show', 'id' => $group['organization_id']));
			$group['edit_link'] = self::link(array('menuaction' => $this->module . '.uigroup.edit', 'id' => $group['id']));
            $group['contact_primary'] = $this->bo->get_contact_info($group['contact_primary']);
            $group['contact_secondary'] = $this->bo->get_contact_info($group['contact_secondary']);

			$data = array(
				'group'	=>	$group
			);
			self::render_template('group', array('group' => $group, ));
		}
	}
