<?php

	class bookingfrontend_preferences
	{

		public $public_functions = array
			(
			'set' => true,
		);

		public function __construct()
		{
		}

		public function set()
		{
			$template_set = phpgw::get_var('template_set', 'string', 'POST');

			switch ($template_set)
			{
				case 'bookingfrontend_2':
				case 'bookingfrontend':
					$GLOBALS['phpgw']->session->phpgw_setcookie('template_set', $template_set, (time() + (60 * 60 * 24 * 14)));
					break;
				default:
					break;
			}

		}
	}