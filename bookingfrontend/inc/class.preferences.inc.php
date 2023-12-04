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

			if ($template_set)
			{
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

			if (phpgw::get_var('lang', 'bool', 'POST'))
			{
				$selected_lang = phpgw::get_var('lang', 'string', 'POST');
				$GLOBALS['phpgw']->session->phpgw_setcookie('selected_lang', $selected_lang, (time() + (60 * 60 * 24 * 14)));
			}
		}
	}