<?php
	phpgw::import_class('booking.uiorganization');

	class bookingfrontend_uiorganization extends booking_uiorganization
	{

		public $public_functions = array
			(
			'show' => true,
			'edit' => true,
			'index' => true,
			'building_users' => true,
			'get_orgid' => true,
			'toggle_show_inactive' => true,
		);
		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->module = "bookingfrontend";
		}

		protected function indexing()
		{
			return parent::index_json();
		}

		public function get_orgid( $orgnr )
		{
			return $this->bo->so->get_orgid($orgnr);
		}

		public function edit()
		{
			$organization = $this->bo->read_single(phpgw::get_var('id', 'int'));

			if (isset($organization['permission']['write']))
			{
				parent::edit();
			}
			else
			{
				self::render_template_xsl('access_denied');
			}
		}

		public function show()
		{
			$organization = $this->bo->read_single(phpgw::get_var('id', 'int'));
			$organization['organizations_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.index'));
			$organization['edit_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.edit',
					'id' => $organization['id']));
			$organization['start'] = self::link(array('menuaction' => 'bookingfrontend.uisearch.index',
					'type' => "organization"));
			if (trim($organization['homepage']) != '' && !preg_match("/^http|https:\/\//", trim($organization['homepage'])))
			{
				$organization['homepage'] = 'http://' . $organization['homepage'];
			}
			$auth_forward = "?redirect_menuaction={$this->module}.uiorganization.show&redirect_id={$organization['id']}";

			// BEGIN EVIL HACK
			$auth_forward .= '&orgnr=' . $organization['organization_number'];
			// END EVIL HACK

			$bouser = CreateObject('bookingfrontend.bouser');
			$organization['login_link'] = 'login.php' . $auth_forward;
			$organization['logoff_link'] = 'logoff.php' . $auth_forward;
			$organization['new_group_link'] = self::link(array('menuaction' => $this->module . '.uigroup.edit',
					'organization_id' => $organization['id']));
			$organization['new_delegate_link'] = self::link(array('menuaction' => $this->module . '.uidelegate.edit',
					'organization_id' => $organization['id']));
			if ($bouser->is_organization_admin($organization['id']))
				$organization['logged_on'] = true;

			phpgwapi_jquery::load_widget("core");

			self::render_template_xsl('organization', array('organization' => $organization));
		}
	}