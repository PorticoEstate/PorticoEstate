<?php
	phpgw::import_class('eventplanner.uievents');

	class eventplannerfrontend_uievents extends eventplanner_uievents
	{

		public function __construct()
		{
			$GLOBALS['phpgw']->translation->add_app('eventplanner');
			parent::__construct();
		}

		public function query()
		{
			$params = $this->bo->build_default_read_params();
			$params['filters']['status'] = eventplanner_application::STATUS_APPROVED;
			$values = $this->bo->read($params);
			array_walk($values["results"], array($this, "_add_links"), "eventplannerfrontend.uievents.edit");

			return $this->jquery_results($values);
		}

	}