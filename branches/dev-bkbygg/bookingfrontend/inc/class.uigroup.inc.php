<?php
	phpgw::import_class('booking.uigroup');

	class bookingfrontend_uigroup extends booking_uigroup
	{

		public $public_functions = array
			(
			'index' => true,
			'edit' => true,
			'show' => true,
			'toggle_show_inactive' => true,
		);
		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->module = "bookingfrontend";
		}

		public function show()
		{
			$group = $this->bo->read_single(phpgw::get_var('id', 'int'));
			$group['organizations_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.index'));
			$group['organization_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.show',
					'id' => $group['organization_id']));
			$group['edit_link'] = self::link(array('menuaction' => $this->module . '.uigroup.edit',
					'id' => $group['id']));

			$data = array(
				'group' => $group
			);

			$edit_self_link = self::link(array('menuaction' => 'bookingfrontend.uigroup.edit',
					'id' => $group['id']));

			$bouser = CreateObject('bookingfrontend.bouser');
			$auth_forward = "?redirect_menuaction={$this->module}.uigroup.show&redirect_id={$group['id']}";
			$group['login_link'] = 'login.php' . $auth_forward;
			$group['logoff_link'] = 'logoff.php' . $auth_forward;
			if ($bouser->is_organization_admin())
				$group['logged_on'] = true;

			self::render_template_xsl('group', array('group' => $group, 'loggedin' => $loggedin,
				'edit_self_link' => $edit_self_link));
		}
	}