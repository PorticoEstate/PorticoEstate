<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.uidocument_organization');

	class booking_uiorganization extends booking_uicommon
	{

		protected $fields;
		public $public_functions = array
			(
			'building_users' => true,
			'index' => true,
			'query' => true,
			'add' => true,
			'edit' => true,
			'show' => true,
			'datatable' => true,
			'toggle_show_inactive' => true,
		);
		protected $module;
		protected $customer_id;

		public function __construct()
		{
			parent::__construct();
			$this->activity_bo = CreateObject('booking.boactivity');
			$this->bo = CreateObject('booking.boorganization');
			$this->customer_id = CreateObject('booking.customer_identifier');

			self::set_active_menu('booking::organizations');
			$this->module = "booking";
			$this->fields = array(
				'name' => 'string',
				'shortname' => 'string',
				'homepage' => 'url',
				'phone' => 'string',
				'email' => 'email',
				'street' => 'string',
				'zip_code' => 'string',
				'city' => 'string',
				'district' => 'string',
				'description' => 'html',
				'contacts' => 'string',
				'active' => 'int',
				'organization_number' => 'string',
				'activity_id' => 'int',
				'customer_number' => 'string',
				'customer_internal' => 'int',
				'show_in_portal' => 'int',
			);
		}

		public function building_users()
		{
			if (!phpgw::get_var('phpgw_return_as') == 'json')
			{
				return;
			}

			if (($building_id = phpgw::get_var('building_id', 'int', 'REQUEST', null)))
			{
				$organizations = $this->bo->find_building_users($building_id);
				array_walk($organizations["results"], array($this, "_add_links"), "bookingfrontend.uiorganization.show");
				return $this->yui_results($organizations);
			}

			return $this->yui_results(null);
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix . '.toggle_show_inactive'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => $this->module . '.uiorganization.index',
						'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Organization'),
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'shortname',
							'label' => lang('Organization shortname'),
						),
						array(
							'key' => 'customer_number',
							'label' => lang('Customer number')
						),
						array(
							'key' => 'organization_number',
							'label' => lang('Organization number')
						),
						array(
							'key' => 'primary_contact_name',
							'label' => lang('Admin 1'),
							'sortable' => false
						),
						array(
							'key' => 'phone',
							'label' => lang('Phone')
						),
						array(
							'key' => 'email',
							'label' => lang('Email')
						),
						array(
							'key' => 'active',
							'label' => lang('Active')
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
			$data['datatable']['actions'][] = array();
			$data['datatable']['new_item'] = self::link(array('menuaction' => $this->module . '.uiorganization.add'));
			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			$organizations = $this->bo->read();
			array_walk($organizations["results"], array($this, "_add_links"), $this->module . ".uiorganization.show");

			foreach ($organizations["results"] as &$organization)
			{

				$contact = (isset($organization['contacts']) && isset($organization['contacts'][0])) ? $organization['contacts'][0] : null;

				if ($contact)
				{
					$organization += array(
						"primary_contact_name" => ($contact["name"]) ? $contact["name"] : '',
						"primary_contact_phone" => ($contact["phone"]) ? $contact["phone"] : '',
						"primary_contact_email" => ($contact["email"]) ? $contact["email"] : '',
					);
				}
			}
			return $this->jquery_results($organizations);
		}

		protected function get_customer_identifier()
		{
			return $this->customer_id;
		}

		protected function extract_customer_identifier( &$data )
		{
			$this->get_customer_identifier()->extract_form_data($data);
		}

		protected function validate_customer_identifier( &$data )
		{
			return $this->get_customer_identifier()->validate($data);
		}

		protected function install_customer_identifier_ui( &$organization )
		{
			$this->get_customer_identifier()->install($this, $organization);
		}

		protected function validate( &$organization )
		{
			$errors = array_merge($this->validate_customer_identifier($organization), $this->bo->validate($organization));
			return $errors;
		}

		protected function extract_form_data( $defaults = array() )
		{
			$organization = array_merge($defaults, extract_values($_POST, $this->fields));
			$this->extract_customer_identifier($organization);
			return $organization;
		}

		protected function extract_and_validate( $defaults = array() )
		{
			$organization = $this->extract_form_data($defaults);
			$errors = $this->validate($organization);
			return array($organization, $errors);
		}

		public function add()
		{
			$errors = array();
			$organization = array('customer_internal' => 1);

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				list($organization, $errors) = $this->extract_and_validate(array('active' => 1));
				if (strlen($_POST['name']) > 50)
				{
					$errors['name'] = lang('Lengt of name is to long, max 50 characters long');
				}
				if (strlen($_POST['shortname']) > 11)
				{
					$errors['shortname'] = lang('Lengt of shortname is to long, max 11 characters long');
				}
				if (!$errors)
				{
					$receipt = $this->bo->add($organization);
					$this->redirect(array('menuaction' => 'booking.uiorganization.show', 'id' => $receipt['id']));
				}
			}
			$this->flash_form_errors($errors);

			$organization['cancel_link'] = self::link(array('menuaction' => 'booking.uiorganization.index',));
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];

			$this->install_customer_identifier_ui($organization);
			self::rich_text_editor('field_description');

			$this->add_template_helpers();

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Organization New'), 'link' => '#organization_edit');
			$active_tab = 'generic';

			$organization['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$organization['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			self::render_template_xsl('organization_edit', array('organization' => $organization,
				"new_form" => "1", 'module' => $this->module, 'activities' => $activities, 'currentapp' => $GLOBALS['phpgw_info']['flags']['currentapp']));
		}

		public function edit()
		{
			$id = phpgw::get_var('id', 'int');

			$session_org_id = phpgw::get_var('session_org_id');

			if(!$id && $session_org_id)
			{
				$id = CreateObject('bookingfrontend.uiorganization')->get_orgid($session_org_id);
			}

			$organization = $this->bo->read_single($id);
			$organization['id'] = $id;
			$organization['organizations_link'] = self::link(array('menuaction' => 'booking.uiorganization.index'));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Generic'), 'link' => '#organization_edit');
			$active_tab = 'generic';

			$organization['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			$errors = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				list($organization, $errors) = $this->extract_and_validate($organization);
				if (strlen($_POST['name']) > 50)
				{
					$errors['name'] = lang('Lengt of name is to long, max 50 characters long');
				}
				if (strlen($_POST['shortname']) > 11)
				{
					$errors['shortname'] = lang('Lengt of shortname is to long, max 11 characters long');
				}
				if ((strlen($_POST['customer_number']) != 5) && (strlen($_POST['customer_number']) != 6) && ($_POST['customer_number'] != ''))
				{
					$errors['customer_number'] = lang('Resourcenumber is wrong, 5 or 6 characters long');
				}
				if (!$errors)
				{
					$organization['shortname'] = $_POST['shortname'];
					$receipt = $this->bo->update($organization);
					if ($this->module == "bookingfrontend")
					{
						$this->redirect(array('menuaction' => 'bookingfrontend.uiorganization.show',
							'id' => $receipt["id"]));
					}
					else
					{
						$this->redirect(array('menuaction' => 'booking.uiorganization.show', 'id' => $receipt["id"]));
					}
				}
			}
			$this->flash_form_errors($errors);
			$organization['organization_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.show',
					'id' => $id));
			$organization['cancel_link'] = $organization['organization_link'];
			$organization['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			$contact_form_link = self::link(array('menuaction' => $this->module . '.uicontactperson.edit',));

			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];

			$this->install_customer_identifier_ui($organization);
			self::rich_text_editor('field_description');

			$this->add_template_helpers();
			self::render_template_xsl('organization_edit', array('organization' => $organization,
				"save_or_create_text" => "Save", "module" => $this->module, "contact_form_link" => $contact_form_link,
				'activities' => $activities, 'currentapp' => $GLOBALS['phpgw_info']['flags']['currentapp']));
		}

		public function show()
		{
			$organization = $this->bo->read_single(phpgw::get_var('id', 'int'));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Organization'), 'link' => '#organization');
			$active_tab = 'generic';

			if (trim($organization['homepage']) != '' && !preg_match("/^http|https:\/\//", trim($organization['homepage'])))
			{
				$organization['homepage'] = 'http://' . $organization['homepage'];
			}
			$organization['organizations_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.index'));
			$organization['edit_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.edit',
					'id' => $organization['id']));
			$organization['new_group_link'] = self::link(array('menuaction' => $this->module . '.uigroup.edit',
					'organization_id' => $organization['id']));
			$organization['new_delegate_link'] = self::link(array('menuaction' => $this->module . '.uidelegate.edit',
					'organization_id' => $organization['id']));
			$organization['cancel_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.index'));
			$organization['add_document_link'] = booking_uidocument::generate_inline_link('organization', $organization['id'], 'add');
			$organization['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$this->install_customer_identifier_ui($organization);
			self::render_template_xsl('organization', array('organization' => $organization));
		}
	}