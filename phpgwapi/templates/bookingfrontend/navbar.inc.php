<?php

	function parse_navbar($force = False)
	{

		$GLOBALS['phpgw']->hooks->process('after_navbar');

		if( phpgw::get_var('phpgw_return_as') != 'json' && $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
		{
			phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			foreach($msgbox_data as & $message)
			{
				echo "<div class='alert {$message['msgbox_class']}' role='alert'>";
				echo "<p class='msgbox_text'>".$message['msgbox_text']."</p>";
				echo '</div>';
			}
		}

		register_shutdown_function('parse_footer_end');
	}


	function parse_footer_end()
	{
		// Stop the register_shutdown_function causing the footer to be included twice - skwashd dec07
		static $footer_included = false;
		if ( $footer_included )
		{
			return true;
		}

		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
		$GLOBALS['phpgw']->template->set_file('footer', 'footer.tpl');

		$config_frontend	= CreateObject('phpgwapi.config','bookingfrontend')->read();

		$footer_info = phpgwapi_cache::session_get('phpgwapi', 'footer_info');
		$footer_privacy_link = "https://www.aktiv-kommune.no/hva-er-aktivkommune/";
		if (!empty($config_frontend['footer_privacy_link']))
		{
			$footer_privacy_link = $config_frontend['footer_privacy_link'];
		}
		$var = array
		(
			'cart_complete_application' => lang('Complete applications'),
			'cart_confirm_delete'       => lang('Do you want to delete application?'),
			'cart_header'				=> lang('Application cart'),
			'footer_about'         => lang('About the service'),
			'footer_info'	=> $footer_info, //'Bergen kommune | R&aring;dhusgt 10 | Postboks 7700 | 5020 Bergen',
			'footer_privacy_link'  => $footer_privacy_link,
			'footer_privacy_title' => lang('Privacy statement'),
			'powered_by'	=> lang('Powered by phpGroupWare version %1', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']),
			'javascript_end'=> $GLOBALS['phpgw']->common->get_javascript_end()
		);

		$GLOBALS['phpgw']->template->set_var($var);

		$GLOBALS['phpgw']->template->pfp('out', 'footer');

		$footer_included = true;
	}

