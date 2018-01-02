<?php
	phpgw::import_class('booking.uicontactperson');

	class bookingfrontend_uicontactperson extends booking_uicontactperson
	{

		public $public_functions = array
			(
			'index' => true,
			'edit' => true, // Falls back to the backend module
		);
		protected $module;

		public function __construct()
		{
			$this->bo = CreateObject('booking.bocontactperson');
			booking_uicommon::__construct();
			$this->module = "bookingfrontend";
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->index_json();
			}
		}

		public function index_json()
		{
			if ($id = phpgw::get_var('id', 'int'))
			{
				$person = $this->bo->read_single($id);
				return $this->jquery_results(array("total_records" => 1, "results" => $person));
			}

			$persons = $this->bo->read();
			array_walk($persons["results"], array($this, "_add_links"), "bookingfrontend.uicontactperson.show");
			return $this->jquery_results($persons);
		}
	}