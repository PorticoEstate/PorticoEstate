<?php

	function parse_navbar($force = False)
	{

		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
		$GLOBALS['phpgw']->template->set_file('navbar', 'navbar.tpl');

		$navbar = execMethod('phpgwapi.menu.get', 'navbar');

		$navigation = array();
		prepare_navbar($navbar);

		if (true)
		{
//			$bookmarks = phpgwapi_cache::user_get('phpgwapi', "bookmark_menu", $GLOBALS['phpgw_info']['user']['id']);
			$lang_bookmarks = lang('bookmarks');

			$navigation = execMethod('phpgwapi.menu.get', 'navigation');
			$treemenu = '';
			foreach($navbar as $app => $app_data)
			{
				if($app == $GLOBALS['phpgw_info']['flags']['currentapp'])
				{
					$submenu = isset($navigation[$app]) ? render_submenu($app, $navigation[$app], array()) : '';
			//		$treemenu .= render_item($app_data, "navbar::{$app}", $submenu, $bookmarks);
				}
			}
			$var['treemenu'] = <<<HTML

			<ul id="menutree">
			{$submenu}
			</ul>
HTML;

		}

		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','navbar');

		$GLOBALS['phpgw']->hooks->process('after_navbar');

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

		register_shutdown_function('parse_footer_end');
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

	function render_item($item, $id='', $children='', $bookmarks = array())
	{
		$current_class = '';

		if ( $id == "navbar::{$GLOBALS['phpgw_info']['flags']['menu_selection']}" )
		{
			$current_class = 'Selected';
			$item['selected'] = true;
		}

		$bookmark = '';
		if(preg_match("/(^{$id})/i", "navbar::{$GLOBALS['phpgw_info']['flags']['menu_selection']}"))
		{
			$item['text'] = "<b>[ {$item['text']} ]</b>";
		}
///		_debug_array($GLOBALS);die();
		$link_class = $current_class ? "class=\"{$current_class}\"" : '';

		$out = <<<HTML
				<li {$link_class}>
HTML;
		$target = '';
		if(isset($item['target']))
		{
			$target = "target = '{$item['target']}'";
		}
		if(isset($item['local_files']) && $item['local_files'])
		{
			$item['url'] = 'file:///' . str_replace(':','|',$item['url']);
		}

		return <<<HTML
$out
					<a href="{$item['url']}" id="{$id}" {$target}>{$bookmark} {$item['text']}</a>
{$children}
				</li>

HTML;
	}

	function render_submenu($parent, $menu, $bookmarks = array())
	{
		$out = '';
		foreach ( $menu as $key => $item )
		{
			$children = isset($item['children']) ? render_submenu(	"{$parent}::{$key}", $item['children'], $bookmarks) : '';
			$out .= render_item($item, "navbar::{$parent}::{$key}", $children, $bookmarks);
			//$debug .= "{$parent}::{$key}<br>";
		}

		$out = <<<HTML
			<ul>
{$out}
			</ul>

HTML;
		return $out;
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

		$footer_info = phpgwapi_cache::session_get('phpgwapi', 'footer_info');
		$var = array
		(
			'footer_info'	=> $footer_info, //'Bergen kommune | R&aring;dhusgt 10 | Postboks 7700 | 5020 Bergen',
			'powered_by'	=> lang('Powered by phpGroupWare version %1', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']),
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
		if ( isset($navbar['admin']) && is_array($navbar['admin']) )
		{
			$navbar['admin']['children'] = execMethod('phpgwapi.menu.get', 'admin');
		}
		uasort($navbar, 'sort_navbar');
	}

	/**
	 * Cheat function to collect bookmarks
	 * @staticvar array $bookmarks
	 * @param array $item
	 * @return array bookmarks
	 */
	function set_get_bookmarks($item = array())
	{
		static $bookmarks = array();
		if($item)
		{
			$bookmarks[] = $item;
		}
		return $bookmarks;
	}