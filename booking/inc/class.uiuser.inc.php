<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiuser extends booking_uicommon
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
			'delete' => true,
			'datatable' => true,
			'toggle_show_inactive' => true,
		);
		protected $module;
		protected $customer_id;

		public function __construct()
		{
			parent::__construct();
			$this->activity_bo = CreateObject('booking.boactivity');
			$this->bo = CreateObject('booking.bouser');
			$this->customer_id = CreateObject('booking.customer_identifier');

			self::set_active_menu('booking::users');
			$this->module = "booking";
			$this->fields = array(
				'name' => 'string',
				'homepage' => 'url',
				'phone' => 'string',
				'email' => 'email',
				'street' => 'string',
				'zip_code' => 'string',
				'city' => 'string',
				'active' => 'int',
				'customer_ssn' => 'string',
				'customer_number' => 'string',
			);
			$this->display_name = lang('users');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('booking') . "::{$this->display_name}";
		}

		public function building_users()
		{
			if (!phpgw::get_var('phpgw_return_as') == 'json')
			{
				return;
			}

			if (($building_id = phpgw::get_var('building_id', 'int', 'REQUEST', null)))
			{
				$users = $this->bo->find_building_users($building_id);
				array_walk($users["results"], array($this, "_add_links"), "bookingfrontend.uiuser.show");
				return $this->yui_results($users);
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
				'datatable_name' => $this->display_name,
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
					'source' => self::link(array('menuaction' => $this->module . '.uiuser.index',
						'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('user'),
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'customer_number',
							'label' => lang('Customer number')
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
			$data['datatable']['new_item'] = self::link(array('menuaction' => $this->module . '.uiuser.add'));
			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			$users = $this->bo->read();
			array_walk($users["results"], array($this, "_add_links"), $this->module . ".uiuser.show");

			foreach ($users["results"] as &$user)
			{
				unset($user['customer_ssn']);
			}

			return $this->jquery_results($users);
		}

		protected function extract_form_data( $defaults = array() )
		{
			$user = array_merge($defaults, extract_values($_POST, $this->fields));
			return $user;
		}

		protected function extract_and_validate( $defaults = array() )
		{
			$user = $this->extract_form_data($defaults);
			$errors = $this->bo->validate($user);
			return array($user, $errors);
		}

		public function add()
		{
			$errors = array();
			$user = array();

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				list($user, $errors) = $this->extract_and_validate(array('active' => 1));
				if (strlen($_POST['name']) > 50)
				{
					$errors['name'] = lang('Lengt of name is to long, max 50 characters long');
				}
				if (!$errors)
				{
					$receipt = $this->bo->add($user);
					$this->redirect(array('menuaction' => 'booking.uiuser.show', 'id' => $receipt['id']));
				}
			}
			$this->flash_form_errors($errors);

			$user['cancel_link'] = self::link(array('menuaction' => 'booking.uiuser.index',));

			self::rich_text_editor('field_description');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('User New'), 'link' => '#user_edit');
			$active_tab = 'generic';

			$user['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$user['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			self::render_template_xsl('user_edit', array('user' => $user,
				"new_form" => "1", 'module' => $this->module, 'currentapp' => $GLOBALS['phpgw_info']['flags']['currentapp']));
		}

		public function edit()
		{
			$id = phpgw::get_var('id', 'int');

			$user = $this->bo->read_single($id);
			$user['id'] = $id;
			$user['users_link'] = self::link(array('menuaction' => 'booking.uiuser.index'));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Generic'), 'link' => '#user_edit');
			$active_tab = 'generic';

			$user['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			$errors = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				list($user, $errors) = $this->extract_and_validate($user);
				if (strlen($_POST['name']) > 50)
				{
					$errors['name'] = lang('Lengt of name is to long, max 50 characters long');
				}
				if ((strlen($_POST['customer_number']) != 5) && (strlen($_POST['customer_number']) != 6) && ($_POST['customer_number'] != ''))
				{
					$errors['customer_number'] = lang('Resourcenumber is wrong, 5 or 6 characters long');
				}
				if (!$errors)
				{
					$receipt = $this->bo->update($user);
					if ($this->module == "bookingfrontend")
					{
						$this->redirect(array('menuaction' => 'bookingfrontend.uiuser.show',
							'id' => $receipt["id"]));
					}
					else
					{
						$this->redirect(array('menuaction' => 'booking.uiuser.show', 'id' => $receipt["id"]));
					}
				}
			}
			$this->flash_form_errors($errors);
			$user['user_link'] = self::link(array('menuaction' => $this->module . '.uiuser.show',
					'id' => $id));
			$user['cancel_link'] = $user['user_link'];
			$user['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			$contact_form_link = self::link(array('menuaction' => $this->module . '.uicontactperson.edit',));

			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];

			self::rich_text_editor('field_description');

			$this->add_template_helpers();
			self::render_template_xsl('user_edit', array('user' => $user,
				"save_or_create_text" => "Save", "module" => $this->module, "contact_form_link" => $contact_form_link,
				'activities' => $activities, 'currentapp' => $GLOBALS['phpgw_info']['flags']['currentapp']));
		}

		public function show()
		{
			$user = $this->bo->read_single(phpgw::get_var('id', 'int'));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('user'), 'link' => '#user');
			$active_tab = 'generic';

			if (trim($user['homepage']) != '' && !preg_match("/^http|https:\/\//", trim($user['homepage'])))
			{
				$user['homepage'] = 'http://' . $user['homepage'];
			}
			$user['users_link'] = self::link(array('menuaction' => $this->module . '.uiuser.index'));
			$user['edit_link'] = self::link(array('menuaction' => $this->module . '.uiuser.edit',
					'id' => $user['id']));
			$user['delete_link'] = self::link(array('menuaction' => $this->module . '.uiuser.delete',
					'id' => $user['id']));
			$user['cancel_link'] = self::link(array('menuaction' => $this->module . '.uiuser.index'));
			$user['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			self::render_template_xsl('user', array('user' => $user));
		}

		public function delete()
		{
			$id = phpgw::get_var('id', 'int');

			$this->bo->delete($id);

			$this->redirect(array('menuaction' => $this->module . '.uiuser.show',
				'id' => $id));
		}
	}