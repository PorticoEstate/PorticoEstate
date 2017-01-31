<?php
	phpgw::import_class('eventplanner.uicustomer_report');

	class eventplannerfrontend_uicustomer_report extends eventplanner_uicustomer_report
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
			array_walk($values["results"], array($this, "_add_links"), "eventplannerfrontend.uicustomer_report.edit");

			return $this->jquery_results($values);
		}

	}
