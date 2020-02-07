<?php
	phpgw::import_class('booking.uiuser');

	class bookingfrontend_uiuser extends booking_uiuser
	{

		public $public_functions = array
			(
			'add' => true,
			'show' => true,
			'edit' => true,
		);
		protected $module,$external_login_info;
		private $ssn;

		public function __construct()
		{
			parent::__construct();
			$this->module = "bookingfrontend";
			
			/**
			 * check external login
			 */
			$bouser = CreateObject('bookingfrontend.bouser');
			$this->external_login_info = $bouser->validate_ssn_login();
			
			$this->ssn = $external_login_info['ssn'];
		}

		public function show()
		{			
			$id = $this->bo->so->get_user_id($this->ssn);
			
			if(!$id)
			{
				$this->bo->so->collect_users();
				$id = $this->bo->so->get_user_id($this->ssn);
				if(!$id)
				{
					$this->redirect(array('menuaction' => 'bookingfrontend.uiuser.add'));
				}
			}

			$user = $this->bo->read_single($id);

			$tabs = array();
			$tabs['generic'] = array('label' => lang('user'), 'link' => '#user');
			$active_tab = 'generic';

			if (trim($user['homepage']) != '' && !preg_match("/^http|https:\/\//", trim($user['homepage'])))
			{
				$user['homepage'] = 'http://' . $user['homepage'];
			}
			$user['users_link'] = self::link(array('menuaction' => $this->module . '.uiuser.index'));
			$user['edit_link'] = self::link(array('menuaction' => $this->module . '.uiuser.edit'));
//			$user['delete_link'] = self::link(array('menuaction' => $this->module . '.uiuser.delete'));
			$user['cancel_link'] = self::link(array());
			$user['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			self::render_template_xsl('user', array('user' => $user));
		}

		public function add()
		{
			$errors = array();
			$user = array('customer_ssn' => $this->ssn);

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				
				list($user, $errors) = $this->extract_and_validate(array('active' => 1, 'customer_ssn' => $this->ssn));
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

			$user['cancel_link'] = self::link(array());

			self::rich_text_editor('field_description');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('New user'), 'link' => '#user_edit');
			$active_tab = 'generic';

			$user['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$user['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			self::render_template_xsl('user_edit', array('user' => $user,
				"new_form" => "1", 'module' => $this->module, 'currentapp' => $GLOBALS['phpgw_info']['flags']['currentapp']));
		}

		public function edit()
		{
			$id = $this->bo->so->get_user_id($this->ssn);
			
			if(!$id)
			{
				$this->redirect(array('menuaction' => 'bookingfrontend.uiuser.add'));
			}

			$user = $this->bo->read_single($id);
			$user['id'] = $id;

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

		public function index()
		{
			phpgw::no_access();
		}
	}