<?php

	function parse_navbar($force = False)
	{
		$nonavbar = false;
		if(isset($GLOBALS['phpgw_info']['flags']['nonavbar']) && $GLOBALS['phpgw_info']['flags']['nonavbar'])
		{
			$nonavbar	= true;
		}

		if($GLOBALS['phpgw_info']['flags']['currentapp'] == 'home')
		{
			$nonavbar	= true;
		}

		$config_controller = CreateObject('phpgwapi.config', 'controller')->read();
		if( isset($config_controller['home_alternative']) && $config_controller['home_alternative'] == 1 )
		{
			$controller_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicomponent.index'));
		}
		else
		{
			$controller_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.control_list'));
		}

		$controller_test_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicalendar_planner.start_inspection'));

		$extra_vars = array();
		foreach($_GET as $name => $value)
		{
			$extra_vars[$name] = phpgw::clean_value($value);
		}

		$site_url	= $GLOBALS['phpgw']->link('/home.php', array());
		$user = $GLOBALS['phpgw']->accounts->get( $GLOBALS['phpgw_info']['user']['id'] );

		$controller_text = lang('controller');
		$tts_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.index'));
		$tts_text = lang('ticket');
		$condition_survey_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicondition_survey.index'));
		$condition_survey_text = $GLOBALS['phpgw']->translation->translate('condition survey', array(), false, 'property');
		$movein_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uimovein.index'));
		$movein_text = $GLOBALS['phpgw']->translation->translate('movein', array(), false, 'rental');
		$moveout_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uimoveout.index'));
		$moveout_text = $GLOBALS['phpgw']->translation->translate('moveout', array(), false, 'rental');
		$logout_url	= $GLOBALS['phpgw']->link('/logout.php');

		$acl = & $GLOBALS['phpgw']->acl;
		$anonymous = $acl->check('anonymous', 1, 'phpgwapi');

		if($anonymous)
		{
			$user_fullname	= lang('home');
			$logout_text	= lang('login');
			$var['user_fullname'] = '';	
		}
		else
		{
			$user_fullname	= $user->__toString();
			$logout_text	= lang('logout');
			$var['user_fullname'] = $user_fullname;		
		}

		$landing = <<<HTML
		<div class="mt-5 row">
			<div class="container">
				<div class="row">
HTML;

		$app_menu = '';

		$topmenu = <<<HTML
			<ul class="nav navbar-nav ml-auto">
				<li class="nav-item">
					<a href="{$site_url}" class="nav-link"><i class="fa fa-home fa-fw" aria-hidden="true"></i>{$user_fullname}</a>
				</li>
HTML;

		if($acl->check('run', PHPGW_ACL_READ, 'controller'))
		{
			$topmenu .= <<<HTML
				<li class="nav-item">
					<a href="{$controller_url}" class="nav-link"><i class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp;{$controller_text}</a>
				</li>
HTML;
			$landing .= <<<HTML
				<!-- CARD #1 -->
					 <div class="col">
						<div class="mb-5 card" style="width: 18rem;">
						  <div class="text-center card-body">
							<h5 class="mx-auto card-title">{$controller_text}</h5>
							<a href="{$controller_url}" class="btn btn-primary">Gå til {$controller_text}</a>
						  </div>
						</div>
					 </div>
					 <div class="col">
						<div class="mb-5 card" style="width: 18rem;">
						  <div class="text-center card-body">
							<h5 class="mx-auto card-title">Kontroll av utstyr og lekeplasser</h5>
							<a href="{$controller_test_url}" class="btn btn-warning">Gå til kontroll</a>
						  </div>
						</div>
					 </div>
HTML;

			if ('controller' == $GLOBALS['phpgw_info']['flags']['currentapp'])
			{
				$menu_gross = execMethod("controller.menu.get_menu");
				$selection = explode('::', $GLOBALS['phpgw_info']['flags']['menu_selection']);
				$level = 0;
				$navigation = get_sub_menu($menu_gross['navigation'], $selection, $level);
			}
			else
			{
				$navigation = array();
			}

			foreach ($navigation as $menu_item)
			{
				$app_menu .= render_item($menu_item);
			}

		}
		if($acl->check('.ticket', PHPGW_ACL_READ, 'property'))
		{
			$topmenu .= <<<HTML
				<li class="nav-item">
					<a href="{$tts_url}" class="nav-link"><i class="fa fa-bolt" aria-hidden="true"></i>&nbsp;{$tts_text}</a>
				</li>
HTML;
			$landing .= <<<HTML
				<!-- CARD #2 -->
					 <div class="col">
						<div class="mb-5 card" style="width: 18rem;">
						  <div class="text-center card-body">
							<h5 class="mx-auto card-title">{$tts_text}</h5>
							<a href="{$tts_url}" class="btn btn-primary">Gå til {$tts_text}</a>
						  </div>
						</div>
					 </div>
HTML;

		}
		if($acl->check('.project.condition_survey', PHPGW_ACL_READ, 'property'))
		{
			$topmenu .= <<<HTML
				<li class="nav-item">
					<a href="{$condition_survey_url}" class="nav-link"><i class="fa fa-thermometer-three-quarters" aria-hidden="true"></i>&nbsp;{$condition_survey_text}</a>
				</li>
HTML;
			$landing .= <<<HTML
				<!-- CARD #3 -->
					 <div class="col">
						<div class="mb-5 card" style="width: 18rem;">
						  <div class="text-center card-body">
							<h5 class="mx-auto card-title">{$condition_survey_text}</h5>
							<a href="{$condition_survey_url}" class="btn btn-primary">Gå til {$condition_survey_text}</a>
						  </div>
						</div>
					 </div>
HTML;

		}
		if($acl->check('.movein', PHPGW_ACL_READ, 'rental'))
		{
			$topmenu .= <<<HTML
				<li class="nav-item">
					<a href="{$movein_url}" class="nav-link"><i class="fa fa-suitcase" aria-hidden="true"></i>&nbsp;{$movein_text}</a>
				</li>
HTML;
			$landing .= <<<HTML
				<!-- CARD #3 -->
					 <div class="col">
						<div class="mb-5 card" style="width: 18rem;">
						  <div class="text-center card-body">
							<h5 class="mx-auto card-title">{$movein_text}</h5>
							<a href="{$movein_url}" class="btn btn-primary">Gå til {$movein_text}</a>
						  </div>
						</div>
					 </div>
HTML;

		}
		if($acl->check('.moveout', PHPGW_ACL_READ, 'rental'))
		{
			$topmenu .= <<<HTML
				<li class="nav-item">
					<a href="{$moveout_url}" class="nav-link"><i class="fa fa-suitcase" aria-hidden="true"></i>&nbsp;{$moveout_text}</a>
				</li>
HTML;
			$landing .= <<<HTML
				<!-- CARD #4 -->
					 <div class="col">
						<div class="mb-5 card" style="width: 18rem;">
						  <div class="text-center card-body">
							<h5 class="mx-auto card-title">{$moveout_text}</h5>
							<a href="{$moveout_url}" class="btn btn-primary">Gå til {$moveout_text}</a>
						  </div>
						</div>
					 </div>
HTML;

		}

		if($acl->check('run', PHPGW_ACL_READ, 'frontend'))
		{
			$rental_frontend_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'frontend.uihelpdesk.index'));
			$rental_frontend_text = $GLOBALS['phpgw']->translation->translate('rental', array(), false, 'rental frontend');

			$topmenu .= <<<HTML
				<li class="nav-item">
					<a href="{$rental_frontend_url}" class="nav-link"><i class="fa fa-home fa-fw" aria-hidden="true"></i>&nbsp;{$rental_frontend_text}</a>
				</li>
HTML;

			$landing .= <<<HTML
				<!-- CARD #5 -->
					 <div class="col">
						<div class="mb-5 card" style="width: 18rem;">
						  <div class="text-center card-body">
							<h5 class="mx-auto card-title">{$rental_frontend_text}</h5>
							<a href="{$rental_frontend_url}" class="btn btn-primary">Gå til {$rental_frontend_text}</a>
						  </div>
						</div>
					 </div>
HTML;
		}

		if($acl->check('.document.import', PHPGW_ACL_ADD, 'property'))
		{
			$property_documents_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiimport_documents.step_1_import'));
			$property_documents_text = $GLOBALS['phpgw']->translation->translate('import documents', array(), false, 'property');

			$topmenu .= <<<HTML
				<li class="nav-item">
					<a href="{$property_documents_url}" class="nav-link"><i class="fa fa-home fa-fw" aria-hidden="true"></i>&nbsp;{$rental_frontend_text}</a>
				</li>
HTML;

			$landing .= <<<HTML
				<!-- CARD #6 -->
					 <div class="col">
						<div class="mb-5 card" style="width: 18rem;">
						  <div class="text-center card-body">
							<h5 class="mx-auto card-title">{$property_documents_text}</h5>
							<a href="{$property_documents_url}" class="btn btn-primary">Gå til {$property_documents_text}</a>
						  </div>
						</div>
					 </div>
HTML;
		}

		if($acl->check('run', PHPGW_ACL_READ, 'helpdesk'))
		{
			$helpdesk_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uitts.index'));

			$config_helpdesk = CreateObject('phpgwapi.config','helpdesk')->read();
			if (!empty($config_helpdesk['app_name']))
			{
				$helpdesk_text = $config_helpdesk['app_name'];
			}
			else
			{
				$helpdesk_text = $GLOBALS['phpgw']->translation->translate('helpdesk', array(), false, 'helpdesk');
			}

			$topmenu .= <<<HTML
				<li class="nav-item">
					<a href="{$helpdesk_url}" class="nav-link"><i class="fa fa-bolt" aria-hidden="true"></i>&nbsp;{$helpdesk_text}</a>
				</li>
HTML;
			$landing .= <<<HTML
				<!-- CARD #7 -->
					 <div class="col">
						<div class="card" style="width: 18rem;">
						  <div class="text-center card-body">
							<h5 class="mx-auto card-title">{$helpdesk_text}</h5>
							<a href="{$helpdesk_url}" class="btn btn-primary">Gå til {$helpdesk_text}</a>
						  </div>
						</div>
					 </div>
HTML;

			if ('helpdesk' == $GLOBALS['phpgw_info']['flags']['currentapp'])
			{
				$menu_gross = execMethod("helpdesk.menu.get_menu");
				$selection = explode('::', $GLOBALS['phpgw_info']['flags']['menu_selection']);
				$level = 0;
				$navigation = get_sub_menu($menu_gross['navigation'], $selection, $level);
			}
			else
			{
				$navigation = array();
			}

			foreach ($navigation as $menu_item)
			{
				$app_menu .= render_item($menu_item);
			}

		}

		$topmenu .= <<<HTML
			<li class="nav-item">
				<a href="{$logout_url}" class="nav-link">{$logout_text}</a>
			</li>
		</ul>
HTML;

$landing .= <<<HTML

	</div>
	</div>
</div>
HTML;
		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
		$GLOBALS['phpgw']->template->set_file('navbar', 'navbar.tpl');

		$flags = &$GLOBALS['phpgw_info']['flags'];
		$var['current_app_title'] = isset($flags['app_header']) ? $flags['app_header'] : lang($GLOBALS['phpgw_info']['flags']['currentapp']);
		$flags['menu_selection'] = isset($flags['menu_selection']) ? $flags['menu_selection'] : '';
		$breadcrumb_selection = !empty($flags['breadcrumb_selection']) ? $flags['breadcrumb_selection'] : $flags['menu_selection'];
		// breadcrumbs
		$current_url = array
		(
			'id'	=> $breadcrumb_selection,
			'url'	=> 	"{$_SERVER['PHP_SELF']}?" . http_build_query($extra_vars),
			'name'	=> $var['current_app_title']
		);
		$breadcrumbs = phpgwapi_cache::session_get('phpgwapi','breadcrumbs');
		$breadcrumbs = $breadcrumbs ? $breadcrumbs : array(); // first one



		if($breadcrumbs[0]['id'] != $breadcrumb_selection)
		{
			array_unshift($breadcrumbs, $current_url);
		}
		if(count($breadcrumbs) >= 6)
		{
			array_pop($breadcrumbs);
		}
		phpgwapi_cache::session_set('phpgwapi','breadcrumbs', $breadcrumbs);
		$breadcrumbs = array_reverse($breadcrumbs);

		$var['topmenu'] = $topmenu;

			$sidebar_content = <<<HTML

			<ul id="menutree" class="list-unstyled components">
HTML;
			if ( $GLOBALS['phpgw']->acl->check('run', PHPGW_ACL_READ, 'preferences') )
			{
				$preferences_url = $GLOBALS['phpgw']->link('/preferences/index.php');
				$preferences_text = lang('preferences');
				$sidebar_content .= <<<HTML

				<li class="nav-item">
					<a href="{$preferences_url}">{$preferences_text}</a>
				</li>
HTML;
			}
			$sidebar_content .= <<<HTML
			{$app_menu}
			</ul>
HTML;

		if($GLOBALS['phpgw_info']['flags']['currentapp'] == 'home')
		{
			$var['landing'] = $landing;
			$nonavbar	= true;
		}

		if(!$nonavbar)
		{
			$navbar_state = execMethod('phpgwapi.template_portico.retrieve_local', 'menu_state');

			$var['sidebar'] = <<<HTML
			<nav id="sidebar" class="{$navbar_state['menu_state']}">
				<div class="sidebar-header">
			        <h1>{$user_fullname}</h1>
			    </div>
				<div class="sidebar-sticky">
					{$sidebar_content}
				</div>
			</nav>
HTML;
			$var['sidebar_button'] = <<<HTML
				<button type="button" id="sidebarCollapse" class="btn btn-info">
				<i class="fas fa-align-left"></i>
				<span>Sidemeny</span>
			</button>
HTML;
		}

		$breadcrumb_html = "";

		if(phpgw::get_var('phpgw_return_as') != 'json' && $breadcrumbs && is_array($breadcrumbs))// && isset($GLOBALS['phpgw_info']['user']['preferences']['common']['show_breadcrumbs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['show_breadcrumbs'])
		{
			$breadcrumb_html = <<<HTML
				<nav aria-label="breadcrumb">
				  <ol class="breadcrumb">
HTML;
			$history_url = array();
			for($i=0;$i< (count($breadcrumbs) -1); $i++)
			{
				$breadcrumb_html .= <<<HTML
					<li class="breadcrumb-item"><a href="{$breadcrumbs[$i]['url']}">{$breadcrumbs[$i]['name']}</a></li>
HTML;
			}

			$breadcrumb_html .= <<<HTML
				    <li class="breadcrumb-item active" aria-current="page">{$breadcrumbs[$i]['name']}</li>
HTML;

			$breadcrumb_html .= <<<HTML
				</ol>
			  </nav>

HTML;

		}

		$var['breadcrumb'] = $breadcrumb_html;

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

	function render_item($item, $id='')
	{
		$selected_node = false;
		$current_class = 'nav-item';

		if ( !empty($item['this']))
		{
			$current_class .= ' active';
			$item['selected'] = true;
			$selected_node = true;
		}

		$target = '';

		if(isset($item['target']))
		{
			$target = "target = '{$item['target']}'";
		}
		if(isset($item['local_files']) && $item['local_files'])
		{
			$item['url'] = 'file:///' . str_replace(':','|',$item['url']);
		}

		$ret = <<<HTML
			<li class="{$current_class}">
			<a href="{$item['url']}" class="nav-link context-menu-nav" bookmark_id="{$id}" {$target}>{$bookmark}{$item['text']}</a>
			</li>
HTML;

		return  $ret;

	}

	function item_expanded($id)
	{
		static $navbar_state;
		if( !isset( $navbar_state ) )
		{
			$navbar_state = execMethod('phpgwapi.template_portico.retrieve_local', 'navbar_config');
		}
		return isset( $navbar_state[ $id ]);
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

		$cache_refresh_token = '';
		if (!empty($GLOBALS['phpgw_info']['server']['cache_refresh_token']))
		{
			$cache_refresh_token = "?n={$GLOBALS['phpgw_info']['server']['cache_refresh_token']}";
		}

		$var = array
		(
			'powered_by'	=> lang('Powered by phpGroupWare version %1', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']),
			'site_title'	=> "{$GLOBALS['phpgw_info']['server']['site_title']}",
			'javascript_end'=> $GLOBALS['phpgw']->common->get_javascript_end($cache_refresh_token)
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
