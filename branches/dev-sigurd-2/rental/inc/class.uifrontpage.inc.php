<?php
	phpgw::import_class('rental.uicommon');

	class rental_uifrontpage extends rental_uicommon
	{
		public $public_functions = array
		(
			'index'	=> true,
		);

		public function __construct()
		{
            parent::__construct();
			self::set_active_menu('rental::frontpage');
		}

		public function index()
		{
			$data = array
			(
			);
			self::render_template('frontpage', $data);
		}
	}
?>