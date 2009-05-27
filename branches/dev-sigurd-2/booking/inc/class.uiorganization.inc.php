<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiorganization extends booking_uicommon
	{
		protected $fields;
		
		public $public_functions = array
		(
			'index'			=>	true,
			'index_json'		=>	true,
			'add'			=>	true,
			'edit'			=>	true,
			'show'			=>	true,
			'datatable'		=>	true,
			'toggle_show_inactive'	=>	true,
		);
		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boorganization');
			self::set_active_menu('booking::organizations');
			$this->module = "booking";
			$this->fields = array('name', 'homepage', 'phone', 'email', 'street', 'zip_code', 'city', 'district', 'description', 'contacts', 'active');
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
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix.'.toggle_show_inactive'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uiorganization.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Organization'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'org_number',
							'label' => lang('Organization number')
						),
						array(
							'key' => 'primary_contact_name',
							'label' => lang('Primary contact')
						),
						array(
							'key' => 'phone',
							'label' => lang('Phone')
						),
						array(
							'key' => 'mail',
							'label' => lang('Email')
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
			array_walk($organizations["results"], array($this, "_add_links"), "booking.uiorganization.show");
			return $this->yui_results($organizations);
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$organization = extract_values($_POST, $this->fields);
				$organization['active'] = '1';
				
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
			self::add_javascript('yahoo', 'yahoo/dom', 'dom-min.js');
			self::add_javascript('yahoo', 'yahoo/container', 'container_core-min.js');
			self::add_javascript('yahoo', 'yahoo/editor', 'simpleeditor-min.js');

			self::render_template('organization_edit', array('organization' => $organization, "new_form"=> "1", 'module' => $this->module));
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
				$organization = array_merge($organization, extract_values($_POST, $this->fields));

				$errors = $this->bo->validate($organization);
				if(!$errors)
				{
					$receipt = $this->bo->update($organization);
					if ($this->module == "bookingfrontend") {
						$this->redirect(array('menuaction' => 'bookingfrontend.uiorganization.show', "id" => $receipt["id"]));
					} else {
						$this->redirect(array('menuaction' => 'booking.uiorganization.index'));
					}
				}
			}
			$this->flash_form_errors($errors);
			$this->flash_form_errors($errors);
			$organization['cancel_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.show', 'id' => $id));

			$contact_form_link = self::link(array('menuaction' => $this->module . '.uicontactperson.edit', ));

			self::add_stylesheet('phpgwapi/js/yahoo/assets/skins/sam/skin.css');
			self::add_javascript('yahoo', 'yahoo/yahoo-dom-event', 'yahoo-dom-event.js');
			self::add_javascript('yahoo', 'yahoo/element', 'element-min.js');
			self::add_javascript('yahoo', 'yahoo/dom', 'dom-min.js');
			self::add_javascript('yahoo', 'yahoo/container', 'container_core-min.js');
			self::add_javascript('yahoo', 'yahoo/editor', 'simpleeditor-min.js');

			self::render_template('organization_edit', array('organization' => $organization, "save_or_create_text" => "Save", "module" => $this->module, "contact_form_link" => $contact_form_link));
		}
		
		public function show()
		{
			$organization = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$organization['organizations_link'] = self::link(array('menuaction' => 'booking.uiorganization.index'));
			$organization['edit_link'] = self::link(array('menuaction' => 'booking.uiorganization.edit', 'id' => $organization['id']));
			self::render_template('organization', array('organization' => $organization, ));
		}
	}
