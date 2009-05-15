<?php
	phpgw::import_class('booking.uiorganization');

	class bookingfrontend_uiorganization extends booking_uiorganization
	{
		public $public_functions = array
			(
			 'show'			=>	true,
			 'edit'         =>  true,
			 'index'        =>  true,
			);
		protected $module;

		public function __construct()
		{
			booking_uicommon::__construct();
			$this->bo = CreateObject('booking.boorganization');
			$this->module = "bookingfrontend";
		}
		protected function indexing()
		{
			return parent::index_json();
		}

		public function show()
		{
			$organization = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$results = $this->bo->get_groups($organization["id"]);
			array_walk($results["results"], array($this, "_add_links"), "bookingfrontend.uigroup.show");
			$organization["groups"] = $results["results"];

			$edit_self_link   = self::link(array('menuaction' => 'bookingfrontend.uiorganization.edit', 'id' => $organization['id']));
			$edit_groups_link = self::link(array('menuaction' => 'bookingfrontend.uigroup.edit',));

			$loggedin = (int) true; // FIXME: Some sort of authentication!

			self::render_template('organization', array(
				'organization'     => $organization,
				'loggedin'         => $loggedin,
				'edit_self_link'   => $edit_self_link,
				'edit_groups_link' => $edit_groups_link,
			));
		}
	}
