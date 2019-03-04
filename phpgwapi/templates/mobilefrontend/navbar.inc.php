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
		$movein_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uimovein.index'));
		$movein_text = $GLOBALS['phpgw']->translation->translate('movein', array(), false, 'rental');
		$moveout_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uimoveout.index'));
		$moveout_text = $GLOBALS['phpgw']->translation->translate('moveout', array(), false, 'rental');


		$acl = & $GLOBALS['phpgw']->acl;

		$app_menu = '';

		$topmenu = <<<HTML
		<div class="pure-menu pure-menu-horizontal pure-menu-scrollable">
			<ul class="pure-menu-list">
				<li class="pure-menu-heading pure-menu-link pure-menu-selected">
					<a href="{$site_url}" class="pure-menu-link bigmenubutton"><i class="fa fa-home fa-fw" aria-hidden="true"></i>{$GLOBALS['phpgw_info']['user']['fullname']}</a>
				</li>
HTML;

		if($acl->check('run', PHPGW_ACL_READ, 'controller'))
		{
			$topmenu .= <<<HTML
				<li class="pure-menu-item">
					<a href="{$controller_url}" class="pure-menu-link bigmenubutton"><i class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp;{$controller_text}</a>
				</li>
HTML;

		}
		if($acl->check('.ticket', PHPGW_ACL_READ, 'property'))
		{
			$topmenu .= <<<HTML
				<li class="pure-menu-item">
					<a href="{$tts_url}" class="pure-menu-link bigmenubutton"><i class="fa fa-bolt" aria-hidden="true"></i>&nbsp;{$tts_text}</a>
				</li>
HTML;

		}
		if($acl->check('.project.condition_survey', PHPGW_ACL_READ, 'property'))
		{
			$topmenu .= <<<HTML
				<li class="pure-menu-item">
					<a href="{$condition_survey_url}" class="pure-menu-link bigmenubutton"><i class="fa fa-thermometer-three-quarters" aria-hidden="true"></i>&nbsp;{$condition_survey_text}</a>
				</li>
HTML;

		}
		if($acl->check('.movein', PHPGW_ACL_READ, 'rental'))
		{
			$topmenu .= <<<HTML
				<li class="pure-menu-item">
					<a href="{$movein_url}" class="pure-menu-link bigmenubutton"><i class="fa fa-suitcase" aria-hidden="true"></i>&nbsp;{$movein_text}</a>
				</li>
HTML;

		}
		if($acl->check('.moveout', PHPGW_ACL_READ, 'rental'))
		{
			$topmenu .= <<<HTML
				<li class="pure-menu-item">
					<a href="{$moveout_url}" class="pure-menu-link bigmenubutton"><i class="fa fa-suitcase" aria-hidden="true"></i>&nbsp;{$moveout_text}</a>
				</li>
HTML;

		}

		if($acl->check('run', PHPGW_ACL_READ, 'frontend'))
		{
			$rental_frontend_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'frontend.uihelpdesk.index'));
			$rental_frontend_text = $GLOBALS['phpgw']->translation->translate('rental', array(), false, 'rental frontend');

			$topmenu .= <<<HTML
				<li class="pure-menu-item">
					<a href="{$rental_frontend_url}" class="pure-menu-link bigmenubutton"><i class="fa fa-home fa-fw" aria-hidden="true"></i>&nbsp;{$rental_frontend_text}</a>
				</li>
HTML;

		}

		if($acl->check('run', PHPGW_ACL_READ, 'helpdesk'))
		{
			$helpdesk_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uitts.index'));
			$helpdesk_text = $GLOBALS['phpgw']->translation->translate('helpdesk', array(), false, 'helpdesk');

			$topmenu .= <<<HTML
				<li class="pure-menu-item pure-menu-allow-hover">
					<a href="{$helpdesk_url}" class="pure-menu-link bigmenubutton"><i class="fa fa-bolt" aria-hidden="true"></i>&nbsp;{$helpdesk_text}</a>
				</li>
HTML;
				if ('helpdesk' == $GLOBALS['phpgw_info']['flags']['currentapp'])
				{
					$menu_gross = execMethod("helpdesk.menu.get_menu");
					$selection = explode('::', $GLOBALS['phpgw_info']['flags']['menu_selection']);
					$level = 0;
					$navigation = get_sub_menu($menu_gross['navigation'], $selection, $level);

					$app_menu = <<<HTML
					<div class="pure-menu pure-menu-horizontal pure-menu-scrollable">
						<ul class="pure-menu-list">
HTML;
				}
				else
				{
					$navigation = array();
				}

				foreach ($navigation as $menu_item)
				{
					$app_menu .= <<<HTML
							<li class="pure-menu-item">
								<a href="{$menu_item['url']}" class="pure-menu-link bigmenubutton"><i class="fa fa-folder-open-o" aria-hidden="true"></i>&nbsp;{$menu_item['text']}</a>
							</li>
HTML;
				}

				$app_menu .= <<<HTML
					</ul>
				</div>
HTML;


		}

		$topmenu .= <<<HTML
			</ul>
		</div>
HTML;

		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
		$GLOBALS['phpgw']->template->set_file('navbar', 'navbar.tpl');

		$flags = &$GLOBALS['phpgw_info']['flags'];
		$var['current_app_title'] = isset($flags['app_header']) ? $flags['app_header'] : lang($GLOBALS['phpgw_info']['flags']['currentapp']);
//		$flags['menu_selection'] = isset($flags['menu_selection']) ? $flags['menu_selection'] : '';

		$var['topmenu'] = $topmenu;
		$var['app_menu'] = $app_menu;

		$GLOBALS['phpgw']->template->set_var($var);

		$GLOBALS['phpgw']->template->pfp('out','navbar');
		if( phpgw::get_var('phpgw_return_as') != 'json' && $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
		{
			phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			foreach($msgbox_data as & $message)
			{
				echo "<div class='{$message['msgbox_class']}'>";
				echo $message['msgbox_text'];
				echo '</div>';
			}
		}

		$GLOBALS['phpgw']->hooks->process('after_navbar');
		register_shutdown_function('parse_footer_end');
	}

		function get_sub_menu( $children = array(), $selection = array(), $level = '' )
		{
			$level++;
			$i = 0;
			foreach ($children as $key => $vals)
			{
				$menu[] = $vals;
				if ($key == $selection[$level])
				{
					$menu[$i]['this'] = true;
					if (isset($menu[$i]['children']))
					{
						$menu[$i]['children'] = $this->get_sub_menu($menu[$i]['children'], $selection, $level);
					}
				}
				else
				{
					if (isset($menu[$i]['children']))
					{
						unset($menu[$i]['children']);
					}
				}
				$i++;
			}
			return $menu;
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
