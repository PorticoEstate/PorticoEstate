<?php

	function parse_navbar($force = False)
	{
		$config_controller = CreateObject('phpgwapi.config', 'controller')->read();
		if( isset($config_controller['home_alternative']) && $config_controller['home_alternative'] == 1 )
		{
			$controller_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicomponent.index'));

		}
		else
		{
			$controller_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.control_list'));
		}

		$site_url	= $GLOBALS['phpgw']->link('/home.php', array());

		$controller_text = lang('controller');
		$tts_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.index'));
		$tts_text = lang('ticket');
		$condition_survey_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicondition_survey.index'));
		$condition_survey_text = $GLOBALS['phpgw']->translation->translate('condition survey', array(), false, 'property');
		$moveout_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uimoveout.index'));
		$moveout_text = $GLOBALS['phpgw']->translation->translate('moveout', array(), false, 'rental');

		$topmenu = <<<HTML
		<div class="pure-menu pure-menu-horizontal">
			<ul class="pure-menu-list">
				<li class="pure-menu-heading pure-menu-link pure-menu-selected">
					<a href="{$site_url}" class="pure-menu-link">{$GLOBALS['phpgw_info']['user']['fullname']}</a>
				</li>
				<li class="pure-menu-item">
					<a href="{$controller_url}" class="pure-menu-link">{$controller_text}</a>
				</li>
				<li class="pure-menu-item">
					<a href="{$tts_url}" class="pure-menu-link">{$tts_text}</a>
				</li>
				<li class="pure-menu-item">
					<a href="{$condition_survey_url}" class="pure-menu-link">{$condition_survey_text}</a>
				</li>
				<li class="pure-menu-item">
					<a href="{$moveout_url}" class="pure-menu-link">{$moveout_text}</a>
				</li>
			</ul>
		</div>
HTML;

		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
		$GLOBALS['phpgw']->template->set_file('navbar', 'navbar.tpl');

		$flags = &$GLOBALS['phpgw_info']['flags'];
		$var['current_app_title'] = isset($flags['app_header']) ? $flags['app_header'] : lang($GLOBALS['phpgw_info']['flags']['currentapp']);
//		$flags['menu_selection'] = isset($flags['menu_selection']) ? $flags['menu_selection'] : '';

		$var['topmenu'] = $topmenu;

		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','navbar');

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

		$var = array
		(
			'powered_by'	=> lang('Powered by phpGroupWare version %1', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']),
			'site_title'	=> "{$GLOBALS['phpgw_info']['server']['site_title']}",
			'javascript_end'=> $GLOBALS['phpgw']->common->get_javascript_end()
		);

		$GLOBALS['phpgw']->template->set_var($var);

		$GLOBALS['phpgw']->template->pfp('out', 'footer');

		$footer_included = true;
	}

	/**
	* Callback for usort($navbar)
	*
	* @param array $item1 the first item to compare
	* @param array $item2 the second item to compare
	* @return int result of comparision
	*/
	function sort_navbar($item1, $item2)
	{
		$a =& $item1['order'];
		$b =& $item2['order'];

		if ($a == $b)
		{
			return strcmp($item1['text'], $item2['text']);
		}
		return ($a < $b) ? -1 : 1;
	}

	/**
	* Organise the navbar properly
	*
	* @param array $navbar the navbar items
	* @return array the organised navbar
	*/
	function prepare_navbar(&$navbar)
	{
		// if ( isset($navbar['admin']) )
		// {
		// 	$navbar['admin']['children'] = execMethod('phpgwapi.menu.get', 'admin');
		// }
		// uasort($navbar, 'sort_navbar');
	}
