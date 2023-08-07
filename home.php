<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id$
	*/

	/**
	* @global array $phpgw_info
	*/
	$GLOBALS['phpgw_info'] = array();

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'                => true,
		'nonavbar'                => false,
		'currentapp'              => 'home',
//		'enable_network_class'    => true,
//		'enable_contacts_class'   => true,
//		'enable_nextmatchs_class' => true
	);

	/**
	* Include phpgroupware header
	*/
	require_once 'header.inc.php';

	// check if forward parameter is set
	if ( isset($_GET['phpgw_forward']) && is_array($_GET['phpgw_forward']) )
	{
		foreach($_GET as $name => $value)
		{
			// find phpgw_ in the $_GET parameters but skip phpgw_forward because of redirect call below
			if (preg_match('/phpgw_/', $name) && ($name != 'phpgw_forward'))
			{
				$name = substr($name, 6); // cut 'phpgw_'
				$extra_vars[$name] = phpgw::clean_value($value);
			}
		}

		$GLOBALS['phpgw']->redirect_link($_GET['phpgw_forward'], $extra_vars);
		exit;
	}

	if ( isset($GLOBALS['phpgw_info']['server']['force_default_app'])
		&& $GLOBALS['phpgw_info']['server']['force_default_app'] != 'user_choice')
	{
		$GLOBALS['phpgw_info']['user']['preferences']['common']['default_app'] = $GLOBALS['phpgw_info']['server']['force_default_app'];
	}

	if (isset($_GET['cd']) && $_GET['cd']=='yes'
		&& isset($GLOBALS['phpgw_info']['user']['preferences']['common']['default_app'])
		&& $GLOBALS['phpgw_info']['user']['preferences']['common']['default_app']
		&& $GLOBALS['phpgw_info']['user']['apps'][$GLOBALS['phpgw_info']['user']['preferences']['common']['default_app']])
	{
		$GLOBALS['phpgw']->redirect_link('/' . $GLOBALS['phpgw_info']['user']['preferences']['common']['default_app'] . '/' . 'index.php');
		exit;
	}
	else
	{
		phpgw::import_class('phpgwapi.jquery');
		phpgwapi_jquery::load_widget('core');
		phpgwapi_jquery::load_widget('autocomplete');
		$GLOBALS['phpgw']->js->validate_file('jquery', 'common', 'phpgwapi', false, array('combine' => true ));
		$GLOBALS['phpgw']->js->validate_file('tinybox2', 'packed', 'phpgwapi', false, array('combine' => true ));
		$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/tinybox2/style.css');
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
	}

	$bookmarks = phpgwapi_cache::user_get('phpgwapi', "bookmark_menu", $GLOBALS['phpgw_info']['user']['id']);

	$bookmark_section = '';

	if($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] == 'bootstrap')
	{
		$grid_envelope = 'row mt-4';
		$grid_element = 'col-4 mb-3';
	}
	else
	{
		$grid_envelope = 'pure-g';
		$grid_element = 'pure-u-1-8 pure-button pure-button-active';
	}

	if($bookmarks && is_array($bookmarks))
	{
		$bookmark_section = <<<HTML
	<div class="container">
		<div id="container_bookmark" class="{$grid_envelope}">
HTML;		
		foreach ($bookmarks as $bm_key => $bookmark_data)
		{
			if(is_array($bookmark_data))
			{
				
				$icon = $bookmark_data['icon'] ? $bookmark_data['icon'] : 'fas fa-2x fa-file-alt';
				$bookmark_section .= <<<HTML
				<div class="{$grid_element}">
					<a href="{$bookmark_data['href']}" class="text-secondary">
						<div class="card shadow h-100 mb-2">
							<div class="card-block text-center">
								<h1 class="p-3">
									<i class="{$icon} text-secondary"></i>
								</h1>
							</div>
							<div class="card-footer text-center">{$bookmark_data['text']}</div>
						</div>
					</a>
				</div>
HTML;
				
			}

		}
		$bookmark_section .= <<<HTML
		</div>
	</div>
HTML;	
	}

	echo $bookmark_section;
	$GLOBALS['phpgw']->translation->add_app('mainscreen');
	if (lang('mainscreen_message') != '!mainscreen_message')
	{
		echo '<div class="msg">' . lang('mainscreen_message') . '</div>';
	}

	if ((isset($GLOBALS['phpgw_info']['user']['apps']['admin']) &&
		$GLOBALS['phpgw_info']['user']['apps']['admin']) &&
		(isset($GLOBALS['phpgw_info']['server']['checkfornewversion']) &&
		$GLOBALS['phpgw_info']['server']['checkfornewversion']))
	{
		// Create a stream
		$opts = array(
			'http' => array(
				'method' => "GET",
			    'proxy' => 'proxy.bergen.kommune.no:8080',
			)
		);

		if (isset($GLOBALS['phpgw_info']['server']['httpproxy_server']))
		{
			$opts['http']['proxy'] = "{$GLOBALS['phpgw_info']['server']['httpproxy_server']}:{$GLOBALS['phpgw_info']['server']['httpproxy_port']}";
		}

		$context = stream_context_create($opts);

		$contents = file_get_contents('https://raw.githubusercontent.com/PorticoEstate/PorticoEstate/master/setup/currentversion', false, $context);
		if (preg_match('/currentversion/', $contents))
		{
			$line_found = explode(':', rtrim($contents));
		}

		/**
		 * compares for major versions only
		 */
		if($GLOBALS['phpgw']->common->cmp_version($GLOBALS['phpgw_info']['server']['versions']['phpgwapi'],$line_found[1], false))
		{
			echo '<p>There is a new version of PorticoEstate available from <a href="'
				. 'https://github.com/PorticoEstate/PorticoEstate">https://github.com/PorticoEstate/PorticoEstate</a>';
		}

		$_found = False;
		$GLOBALS['phpgw']->db->query("SELECT app_name,app_version FROM phpgw_applications",__LINE__,__FILE__);
		while($GLOBALS['phpgw']->db->next_record())
		{
			$_db_version  = $GLOBALS['phpgw']->db->f('app_version');
			$_app_name    = $GLOBALS['phpgw']->db->f('app_name');
			$_versionfile = $GLOBALS['phpgw']->common->get_app_dir($_app_name) . '/setup/setup.inc.php';
			if(file_exists($_versionfile))
			{
				require_once $_versionfile;
				$_file_version = $setup_info[$_app_name]['version'];
				$_app_title    = $GLOBALS['phpgw_info']['apps'][$_app_name]['title'];
				unset($setup_info);

				if($GLOBALS['phpgw']->common->cmp_version_long($_db_version,$_file_version))
				{
					$_found = True;
					$_app_string .= '<br />' . $_app_title;
				}
				unset($_file_version);
				unset($_app_title);
			}
			unset($_db_version);
			unset($_versionfile);
		}
		if($_found)
		{
			echo '<br>' . lang('The following applications require upgrades') . ':' . "\n";
			echo $_app_string . "\n";
			echo '<br>' . lang('Please run setup to become current') . '.' . "\n";
			unset($_app_string);
		}
	}

	// This initializes the users portal_order preference if it does not exist.
	if ( (!isset($GLOBALS['phpgw_info']['user']['preferences']['portal_order']) || !is_array($GLOBALS['phpgw_info']['user']['preferences']['portal_order']) )
		&& $GLOBALS['phpgw_info']['apps'] )
	{
		$GLOBALS['phpgw']->preferences->delete('portal_order');
		$order = 0;
		foreach ( $GLOBALS['phpgw_info']['apps'] as $p )
		{
			if ( isset($GLOBALS['phpgw_info']['user']['apps'][$p['name']])
				&& $GLOBALS['phpgw_info']['user']['apps'][$p['name']] )
			{
				$GLOBALS['phpgw']->preferences->add('portal_order', ++$order, $p['id']);
			}
		}
		$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->save_repository();
	}

	if ( isset($GLOBALS['phpgw_info']['user']['preferences']['portal_order'])
		&& is_array($GLOBALS['phpgw_info']['user']['preferences']['portal_order']) )
	{
		$app_check = array();
		ksort($GLOBALS['phpgw_info']['user']['preferences']['portal_order']);
		foreach($GLOBALS['phpgw_info']['user']['preferences']['portal_order'] as $app)
		{
			if(!isset($app_check[$app]) || !$app_check[$app])
			{
				$app_check[$app] = true;
				$sorted_apps[] = $GLOBALS['phpgw']->applications->id2name($app);
			}
		}
	}
	else
	{
		$sorted_apps = array
		(
			'email',
			'calendar',
			'news_admin',
			'addressbook',
		);
	}

	$GLOBALS['phpgw']->hooks->process('home', $sorted_apps);

	if ( isset($GLOBALS['portal_order']) && is_array($GLOBALS['portal_order']) )
	{
		$GLOBALS['phpgw']->preferences->delete('portal_order');
		foreach ( $GLOBALS['portal_order']  as $app_order => $app_id )
		{
			$GLOBALS['phpgw']->preferences->add('portal_order', $app_order, $app_id);
		}
		$GLOBALS['phpgw']->preferences->save_repository();
	}
	if( phpgwapi_cache::system_get('phpgwapi', 'phpgw_home_screen_message'))
	{
		echo "<div class='msg_important container'><h2>";
		echo nl2br(phpgwapi_cache::system_get('phpgwapi', 'phpgw_home_screen_message_title'));
		echo "</h2>";
		echo nl2br(phpgwapi_cache::system_get('phpgwapi', 'phpgw_home_screen_message'));
		echo '</div>';
	}
	$GLOBALS['phpgw']->common->phpgw_footer();
