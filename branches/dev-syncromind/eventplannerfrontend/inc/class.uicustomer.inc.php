<?php
	phpgw::import_class('eventplanner.uicustomer');

	class eventplannerfrontend_uicustomer extends eventplanner_uicustomer
	{

		public function __construct()
		{
			$GLOBALS['phpgw']->translation->add_app('eventplanner');
			parent::__construct();
		}

		public function query()
		{
			$params = $this->bo->build_default_read_params();
			$values = $this->bo->read($params);
			array_walk($values["results"], array($this, "_add_links"), "eventplannerfrontend.uicustomer.edit");

			return $this->jquery_results($values);
		}

	}
