<?php
	/**
	* Setup
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package setup
	* @version $Id$
	*/

	$DEBUG = $_POST['debug'] || $_GET['debug'];

	if($_POST['cancel'])
	{
		Header("Location: index.php");
		exit;
	}

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'noheader' => True,
		'nonavbar' => True,
		'currentapp' => 'home',
		'noapi' => True
	);
	
	/**
	 * Include setup functions
	 */
	include ('./inc/functions.inc.php');

	set_time_limit(0);

	// Check header and authentication
	if (!$GLOBALS['phpgw_setup']->auth('Header'))
	{
		Header('Location: index.php');
		exit;
	}
	// Does not return unless user is authorized

	$tpl_root = $GLOBALS['phpgw_setup']->html->setup_tpl_dir('setup');
	$setup_tpl = CreateObject('phpgwapi.template',$tpl_root);
	$setup_tpl->set_file(array(
		'T_head' => 'head.tpl',
		'T_footer' => 'footer.tpl',
		'T_alert_msg' => 'msg_alert_msg.tpl',
		'T_login_main' => 'login_main.tpl',
		'T_login_stage_header' => 'login_stage_header.tpl',
		'T_setup_main' => 'bulk.tpl'
	));

	$setup_tpl->set_block('T_login_stage_header','B_multi_domain','V_multi_domain');
	$setup_tpl->set_block('T_login_stage_header','B_single_domain','V_single_domain');
	$setup_tpl->set_block('T_setup_main','header','header');
	$setup_tpl->set_block('T_setup_main','footer','footer');

	if ( phpgw::get_var('submit', 'string', 'POST') )
	{
		$GLOBALS['phpgw_setup']->html->show_header(lang('Bulk Upgrade Management'),False,'config','');
		$setup_tpl->set_var('description',lang('App upgrade') . ':');

		$domains = phpgw::get_var('domains', 'string', 'POST');
		$apps = phpgw::get_var('apps', 'string', 'POST');

		if(!empty($domains) && is_array($domains))
		{
			foreach($domains as $domain)
			{
				$_POST['ConfigDomain'] = $domain;
				echo '<h2>' . lang('processing upgrade for %1', $domain) . '</h2>';
				$GLOBALS['phpgw_setup']->loaddb();
				$GLOBALS['phpgw_info']['setup']['stage']['db'] = $GLOBALS['phpgw_setup']->detection->check_db();

				$setup_info = $GLOBALS['phpgw_setup']->detection->get_versions();
				$setup_info = $GLOBALS['phpgw_setup']->detection->get_db_versions($setup_info);
				$setup_info = $GLOBALS['phpgw_setup']->detection->compare_versions($setup_info);
				$setup_info = $GLOBALS['phpgw_setup']->detection->check_depends($setup_info);
				@ksort($setup_info);

				if(!empty($apps) && is_array($apps) && is_array($setup_info))
				{
					foreach($apps as $key => $appname)
					{
						echo $appname . ' status ' . $setup_info[$appname]['status'] . ' version ' . $setup_info[$appname]['currentver'] .'<br />';
						//echo '<pre>'; print_r($setup_info[$appname]); echo '</pre>';
						if($setup_info[$appname]['status'] != 'U' 
							|| !isset($setup_info[$appname]['currentver']))
						{
							echo lang('ignoring %1 for domain "%2"', $appname, $domain) . '<br />';
							continue;
						}
						$terror = array();
						$terror[] = $setup_info[$appname];

						$GLOBALS['phpgw_setup']->process->upgrade($terror,$DEBUG);
						if ($setup_info[$appname]['tables'])
						{
							echo '<br />' . $setup_info[$appname]['title'] . ' ' . lang('tables upgraded') . '.';
							// The process_upgrade() function also handles registration
						}
						else
						{
							echo '<br />' . $setup_info[$appname]['title'] . ' ' . lang('upgraded') . '.';
						}

						$terror = $GLOBALS['phpgw_setup']->process->upgrade_langs($terror,$DEBUG);
						echo '<br />' . $setup_info[$appname]['title'] . ' ' . lang('Translations upgraded') . '.<br />';
					}//end foreach(apps)
				}//end if(is_array(apps))
				ob_end_flush(); //signs of life
			}//end foreach(domains)
		}//end if(is_array(domains))
		echo '<br /><a href="bulk.php?debug='.$DEBUG.'">' . lang('Go back') . '</a>';
		$setup_tpl->pparse('out','footer');
		exit;
	}

	$GLOBALS['phpgw_setup']->loaddb();
	$GLOBALS['phpgw_info']['setup']['stage']['db'] = $GLOBALS['phpgw_setup']->detection->check_db();
	$setup_info = $GLOBALS['phpgw_setup']->detection->get_versions();
	@ksort($setup_info);

	$GLOBALS['phpgw_setup']->html->show_header(lang('Bulk Upgrade Management'),False,'config','');

	$apps = '<select name="apps[]" size="10" multiple="multiple">';
	@ksort($setup_info);
	foreach($setup_info as $key => $value)
	{
		if($key)
		{
			$apps .= '<option value="'.$value['name'].'" selected="selected">'
				. ($value['title'] ? $value['title'] : $value['name'])
				. "</option>\n";
		}
	}
	$apps .= '</select>';

	$doms = '<select name="domains[]" size="10" multiple="multiple">';
	foreach($GLOBALS['phpgw_domain'] as $domain => $ignored)
	{
		if($key)
		{
			$doms .= '<option value="' . $domain . '" selected="selected">'
				. $domain
				. "</option>\n";
		}
	}
	$doms .= '</select>';

	$setup_tpl->set_var(array('bulk_app'	=> $apps,
				'bulk_dom'	=> $doms,
				'form_action'	=> ($_SERVER['HTTPS'] ? 'https://' : 'http://') 
							. $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
				'lang_apps'	=> lang('applications'),
				'lang_cancel'	=> lang('cancel'),
				'lang_domains'	=> lang('domains'),
				'lang_run'	=> lang('run'),
				'lang_upgrade'	=> lang('upgrade'),
				)
			);
	$setup_tpl->pparse('out', 'T_setup_main');

	$setup_tpl->set_var('submit',lang('Upgrade'));
	$setup_tpl->set_var('cancel',lang('Cancel'));
	$setup_tpl->pparse('out','T_footer');
	$GLOBALS['phpgw_setup']->html->show_footer();