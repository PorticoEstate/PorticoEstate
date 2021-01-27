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
		protected $module;
		private $ssn, $orgnr, $orgs, $external_login_info;

		public function __construct()
		{
			parent::__construct();
			$this->module = "bookingfrontend";

			/**
			 * check external login
			 */
			$bouser = CreateObject('bookingfrontend.bouser');
			if($bouser->is_logged_in())
			{
				$this->orgs = (array)phpgwapi_cache::session_get($bouser->get_module(), $bouser::ORGARRAY_SESSION_KEY);

				$orgs_map = array();
				foreach ($this->orgs as $org)
				{
					$orgs_map[] = $org['orgnumber'];
				}

				$session_org_id = phpgw::get_var('session_org_id','string', 'GET');

				if($session_org_id && in_array($session_org_id, $orgs_map))
				{
					try
					{
						$org_number = createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean($session_org_id);
						if($org_number)
						{
							$bouser->change_org($org_number);
						}
					}
					catch (sfValidatorError $e)
					{
						$session_org_id = -1;
					}
				}
			}

			$this->external_login_info = $bouser->validate_ssn_login(array('menuaction' => 'bookingfrontend.uiuser.show'));
			$this->ssn = $this->external_login_info['ssn'];

			$this->orgnr = phpgw::get_var('session_org_id') ? phpgw::get_var('session_org_id') : $bouser->orgnr;
			
			if(!$this->ssn)
			{
				phpgw::no_access();
			}
		}

		public function show()
		{
			$id = $this->bo->so->get_user_id($this->ssn);

			if(!$id)
			{
				$this->bo->so->collect_users($this->ssn);
				$id = $this->bo->so->get_user_id($this->ssn);
				if(!$id)
				{
					$this->redirect(array('menuaction' => 'bookingfrontend.uiuser.add'));
				}
			}

			$user = $this->bo->read_single($id);
			$datatable_def = array();

			$lang_view = lang('view');

			$application_def = array
			(
				array('key' => 'id', 'label' => '#', 'sortable' => true, 'resizeable' => true),
				array('key' => 'date', 'label' => lang('Date'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'lang_status', 'label' => lang('status'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'building_name', 'label' => lang('Where'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'customer_organization_number', 'label' => lang('organization number'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'contact_name', 'label' => lang('contact'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'link', 'label' => $lang_view, 'sortable' => false, 'resizeable' => true)
			);

			$application_data = $this->bo->so->get_applications($this->ssn);

			$dateformat	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach ($application_data as &$entry)
			{
				$entry['lang_status'] = lang($entry['status']);
				$entry['date'] = $GLOBALS['phpgw']->common->show_date(strtotime($entry['created']), $dateformat);
				$entry['link'] = '<a href="' .self::link(array('menuaction' => "{$this->module}.uiapplication.show", 'id' => $entry['id'], 'secret' => $entry['secret'])) . '">' . $lang_view . '</a>';

			}
			unset($entry);

			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = 10;
			$lang = array();
			$this->add_jquery_translation( $lang );

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => $application_def,
				'data'		 => json_encode($application_data),
				'config'	 => array(
		//			array('disableFilter' => true),
		//			array('disablePagination' => true),
		//			array('rows_per_page' => $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']),
					array('order' => json_encode(array(0, 'asc'))),
				)
			);

			$invoice_def = array
			(
				array('key' => 'id', 'label' => '#', 'sortable' => true, 'resizeable' => true),
				array('key' => 'description', 'label' => lang('When'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'article_description', 'label' => lang('What'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'cost', 'label' => lang('cost'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'customer_organization_number', 'label' => lang('organization number'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'invoice_sent', 'label' => 'Fakturert', 'sortable' => true, 'resizeable' => true),
			);


			$invoice_data = $this->bo->so->get_invoices($this->ssn);

			$lang_yes = lang('yes');
			$lang_no = lang('no');
			foreach ($invoice_data as &$entry)
			{
				$entry['invoice_sent'] = $entry['exported'] ? $lang_yes : $lang_no;
			}
			unset($entry);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_1',
				'requestUrl' => "''",
				'ColumnDefs' => $invoice_def,
				'data'		 => json_encode($invoice_data),
				'config'	 => array(
		//			array('disableFilter' => true),
		//			array('disablePagination' => true),
		//			array('rows_per_page' => $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']),
					array('order' => json_encode(array(0, 'asc'))),
				)
			);

			$delegate_def = array
			(
	//			array('key' => 'id', 'label' => '#', 'sortable' => true, 'resizeable' => true),
				array('key' => 'name', 'label' => lang('name'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'organization_number', 'label' => lang('organization number'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'active', 'label' => lang('active'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'link', 'label' => $lang_view, 'sortable' => false, 'resizeable' => true)
			);


			$delegate_data = $this->bo->so->get_delegate($this->ssn, $this->orgnr);

			foreach ($delegate_data as &$entry)
			{
				$entry['link'] = '<a href="' .self::link(array('menuaction' => "{$this->module}.uiorganization.show", 'id' => $entry['id'])) . '">' . $lang_view . '</a>';
			}

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_2',
				'requestUrl' => "''",
				'ColumnDefs' => $delegate_def,
				'data'		 => json_encode($delegate_data),
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true),
					array('order' => json_encode(array(0, 'asc'))),
				)
			);

			$tabs = array();
			$tabs['generic'] = array('label' => lang('user data'), 'link' => '#user');
			$tabs['applications'] = array('label' => lang('applications'), 'link' => '#applications');
			$tabs['invoice'] = array('label' => lang('invoice'), 'link' => '#invoice');

			if($delegate_data)
			{
				$tabs['delegate'] = array('label' => lang('delegate from'), 'link' => '#delegate');
			}

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
			$user['lang'] = $lang;
			$user['delegate_data'] = !!$delegate_data;
			$user['datatable_def'] = $datatable_def;

			self::render_template_xsl(array('user', 'datatable_inline'), array('user' => $user));
		}

		public function add()
		{
			$errors = array();
			$user = array(
				'customer_ssn' => $this->ssn,
				'phone' => $this->external_login_info['phone'],
				'email' => $this->external_login_info['email'],
				'name' => "{$this->external_login_info['first_name']} {$this->external_login_info['last_name']}",
				'street' => $this->external_login_info['street'],
				'zip_code' => $this->external_login_info['zip_code'],
				'city' => $this->external_login_info['city'],
				);

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
					$this->redirect(array('menuaction' => $this->module  . '.uiuser.show', 'id' => $receipt['id']));
				}
			}
			$this->flash_form_errors($errors);

			$user['cancel_link'] = self::link(array());

			self::rich_text_editor('field_description');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('user data'), 'link' => '#user_edit');
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
			$tabs['generic'] = array('label' => lang('user data'), 'link' => '#user_edit');
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