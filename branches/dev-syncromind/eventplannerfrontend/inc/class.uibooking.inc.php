<?php
	phpgw::import_class('eventplanner.uibooking');
	include_class('eventplanner', 'application', 'inc/model/');
	class eventplannerfrontend_uibooking extends eventplanner_uibooking
	{

		public function __construct()
		{
			$GLOBALS['phpgw']->translation->add_app('eventplanner');
			parent::__construct();
		}

		public function index( )
		{
			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				phpgw::no_access();
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::set_active_menu("{$this->currentapp}::customer::booking");
			parent::index();
		}

		public function query()
		{
			$params = $this->bo->build_default_read_params();
	//		$params['filters']['status'] = eventplanner_application::STATUS_APPROVED;
			$values = $this->bo->read($params);
			array_walk($values["results"], array($this, "_add_links"), "eventplannerfrontend.uibooking.edit");

			return $this->jquery_results($values);
		}

		public function query_relaxed()
		{
			$params = $this->bo->build_default_read_params();
			$params['relaxe_acl'] = true;
			$params['filters']['status'] = eventplanner_application::STATUS_APPROVED;
			$values = $this->bo->read($params);
			array_walk($values["results"], array($this, "_add_links"), "eventplannerfrontend.uibooking.edit");

			return $this->jquery_results($values);
		}

		public function edit()
		{
			parent::edit();
		}

	}
