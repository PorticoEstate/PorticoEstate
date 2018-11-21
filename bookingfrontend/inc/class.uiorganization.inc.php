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

			$bouser = CreateObject('bookingfrontend.bouser');

			if($bouser->is_logged_in())
			{
				$orgs = (array)phpgwapi_cache::session_get($bouser->get_module(), $bouser::ORGARRAY_SESSION_KEY);

				$orgs_map = array();
				foreach ($orgs as $org)
				{
					$orgs_map[] = $org['orgnumber'];
				}

				$session_org_id = phpgw::get_var('session_org_id','int', 'GET');

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

			$id = phpgw::get_var('id', 'int');

			if(!$id && $session_org_id)
			{
				$id = CreateObject('bookingfrontend.uiorganization')->get_orgid($session_org_id);
				$_GET['id'] = $id;
			}

			$organization = $this->bo->read_single($id);

			if (isset($organization['permission']['write']))
			{
				parent::edit();
			}
			else
			{
				self::render_template_xsl('access_denied', array());
			}
		}

		public function show()
		{
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$id = phpgw::get_var('id', 'int');

			$session_org_id = phpgw::get_var('session_org_id');

			if(!$id && $session_org_id)
			{
				$id = CreateObject('bookingfrontend.uiorganization')->get_orgid($session_org_id);
			}

			$organization = $this->bo->read_single($id);
			$organization['organizations_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.index'));
			$organization['edit_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.edit',
					'id' => $organization['id']));
			$organization['start'] = self::link(array('menuaction' => 'bookingfrontend.uisearch.index',
					'type' => "organization"));

			$organization['contact_info'] = "";
			$contactdata = array();
			foreach (array('homepage','email','phone') as $field)
			{
				if (!empty(trim($organization[$field])))
				{
					$value = trim($organization[$field]);
					if ($field == 'homepage')
					{
						if (!preg_match("/^(http|https):\/\//",$value))
						{
							$value = 'http://' . $value;
						}
						$value = sprintf('<a href="%s" target="_blank">%s</a>', $value, $value);
					}
					$contactdata[] = sprintf('%s: %s', lang($field), $value);
				}
			}
			if (!empty($contactdata))
			{
				$organization['contact_info'] = sprintf('<p>%s</p>', join('<br/>',$contactdata));
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
			{
				$organization['logged_on'] = true;
			}

			phpgwapi_jquery::load_widget("core");

			self::render_template_xsl('organization', array('organization' => $organization, 'config_data' => $config->config_data));
		}
	}