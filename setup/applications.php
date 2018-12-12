<?php
	/**
	* Setup
	*
	* @copyright Copyright (C) 2000-2015 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package setup
	* @version $Id$
	*/

	/*
	 TODO: We allow a user to hose their setup here, need to make use
	 of dependencies so they are warned that they are pulling the rug
	 out from under other apps.  e.g. if they select to uninstall the api
	 this will happen without further warning.
	*/

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'		=> true,
		'nonavbar'		=> true,
		'currentapp'	=> 'home',
		'noapi'			=> true
	);
	

	/**
	 * Include setup functions
	 */
	include ('./inc/functions.inc.php');

	if ( phpgw::get_var('cancel', 'bool', 'POST') )
	{
		Header('Location: index.php');
		exit;
	}

	@set_time_limit(0);

	$DEBUG = phpgw::get_var('debug', 'bool');


	// Check header and authentication
	if (!$GLOBALS['phpgw_setup']->auth('Config'))
	{
		Header('Location: index.php');
		exit;
	}
	// Does not return unless user is authorized

	$ConfigDomain = phpgw::get_var('ConfigDomain');

	$tpl_root = $GLOBALS['phpgw_setup']->html->setup_tpl_dir('setup');
	$setup_tpl = CreateObject('phpgwapi.template',$tpl_root);
	$setup_tpl->set_file(array
	(
		'T_head' => 'head.tpl',
		'T_footer' => 'footer.tpl',
		'T_alert_msg' => 'msg_alert_msg.tpl',
		'T_login_main' => 'login_main.tpl',
		'T_login_stage_header' => 'login_stage_header.tpl',
		'T_setup_main' => 'applications.tpl'
	));

	$setup_tpl->set_block('T_login_stage_header','B_multi_domain','V_multi_domain');
	$setup_tpl->set_block('T_login_stage_header','B_single_domain','V_single_domain');
	$setup_tpl->set_block('T_setup_main','header','header');
	$setup_tpl->set_block('T_setup_main','app_header','app_header');
	$setup_tpl->set_block('T_setup_main','apps','apps');
	$setup_tpl->set_block('T_setup_main','detail','detail');
	$setup_tpl->set_block('T_setup_main','table','table');
	$setup_tpl->set_block('T_setup_main','hook','hook');
	$setup_tpl->set_block('T_setup_main','dep','dep');
	$setup_tpl->set_block('T_setup_main','app_footer','app_footer');
	$setup_tpl->set_block('T_setup_main','submit','submit');
	$setup_tpl->set_block('T_setup_main','footer','footer');
	$setup_tpl->set_var('lang_cookies_must_be_enabled', lang('<b>NOTE:</b> You must have cookies enabled to use setup and header admin!') );

	/**
	 * Parse dependencies
	 * 
	 * @param array $depends
	 * @param boolean $main Return a string when true otherwise an array
	 * @return string|array Dependency string or array
	 */
	function parsedep($depends,$main=True)
	{
		$depstring = '';
		foreach($depends as $b)
		{
			foreach($b as $c => $d)
			{
				if (is_array($d))
				{
					$depstring .= "($c : " . implode(', ',$d) . ')';
					$depver[] = $d;
				}
				else
				{
					$depstring .= "<br>\n$d ";
					$depapp[] = $d;
				}
			}
		}
		if ($main)
		{
			return $depstring;
		}
		else
		{
			return array($depapp,$depver);
		}
	}

	$GLOBALS['phpgw_setup']->loaddb();


	$GLOBALS['phpgw']->db = &$GLOBALS['phpgw_setup']->db;

	$c = createObject('phpgwapi.config','phpgwapi');
	$c->read();
	foreach ($c->config_data as $k => $v)
	{
		$GLOBALS['phpgw_info']['server'][$k] = $v;
	}

	$GLOBALS['phpgw_info']['setup']['stage']['db'] = $GLOBALS['phpgw_setup']->detection->check_db();

	$setup_info = $GLOBALS['phpgw_setup']->detection->get_versions();
	//var_dump($setup_info);exit;
	$setup_info = $GLOBALS['phpgw_setup']->detection->get_db_versions($setup_info);
	//var_dump($setup_info);exit;
	$setup_info = $GLOBALS['phpgw_setup']->detection->compare_versions($setup_info);
	//var_dump($setup_info);exit;
	$setup_info = $GLOBALS['phpgw_setup']->detection->check_depends($setup_info);
	//var_dump($setup_info);exit;
	ksort($setup_info);


	if ( phpgw::get_var('submit', 'string', 'POST') )
	{
		$GLOBALS['phpgw_setup']->html->show_header(lang('Application Management'),False,'config',$ConfigDomain . '(' . $phpgw_domain[$ConfigDomain]['db_type'] . ')');
		$setup_tpl->set_var('description',lang('App install/remove/upgrade') . ':');
		$setup_tpl->pparse('out','header');

		$appname = phpgw::get_var('appname', 'string', 'POST');
		$remove  = phpgw::get_var('remove', 'string', 'POST');
		$install = phpgw::get_var('install', 'string', 'POST');
		$upgrade = phpgw::get_var('upgrade', 'string', 'POST');

		if( !isset($GLOBALS['phpgw_setup']->oProc) || !$GLOBALS['phpgw_setup']->oProc )
		{
			$GLOBALS['phpgw_setup']->process->init_process();
		}

//$GLOBALS['phpgw_setup']->process->add_credential('property');
		if(!empty($remove) && is_array($remove))
		{
			$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
			foreach($remove as $appname => $key)
			{
				echo '<h3>' . lang('Processing: %1', lang($appname)) . "</h3>\n<ul>";
				$terror = array( $setup_info[$appname] );

				if ( isset($setup_info[$appname]['tables'])
					&& $setup_info[$appname]['tables'] )
				{
					$GLOBALS['phpgw_setup']->process->droptables($terror, $DEBUG);
					echo '<li>' . lang('%1 tables dropped', lang($appname)) . ".</li>\n";
				}

				$GLOBALS['phpgw_setup']->deregister_app($appname);
				echo '<li>' . lang('%1 deregistered', lang($appname)) . ".</li>\n";

				if (isset($setup_info[$appname]['hooks'])
					&& $setup_info[$appname]['hooks'] )
				{
					$GLOBALS['phpgw_setup']->deregister_hooks($appname);
					echo '<li>' . lang('%1 hooks deregistered', lang($appname)) . ".</li>\n";
				}

				$terror = $GLOBALS['phpgw_setup']->process->drop_langs($terror, $DEBUG);
				echo '<li>' . lang('%1 translations removed', $appname) . ".</li>\n</ul>\n";
			}
			$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit();
		}

		if(!empty($install) && is_array($install))
		{
			$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
			foreach($install as $appname => $key)
			{
				echo '<h3>' . lang('Processing: %1', lang($appname)) . "</h3>\n<ul>";
				$terror = array($setup_info[$appname]);

				if ( isset($setup_info[$appname]['tables'])
					&& is_array($setup_info[$appname]['tables']) )
				{
					$terror = $GLOBALS['phpgw_setup']->process->current($terror,$DEBUG);
					echo "<li>{$setup_info[$appname]['name']} "
						. lang('tables installed, unless there are errors printed above') . ".</h3>\n";
					$terror = $GLOBALS['phpgw_setup']->process->default_records($terror,$DEBUG);
					echo '<li>' . lang('%1 default values processed', lang($appname)) . ".</li>\n";
				}
				else
				{
					if ($GLOBALS['phpgw_setup']->app_registered($appname))
					{
						$GLOBALS['phpgw_setup']->update_app($appname);
					}
					else
					{
						$GLOBALS['phpgw_setup']->register_app($appname);
						echo '<li>' . lang('%1 registered', lang($appname)) . ".</li>\n";

						// Default values has be processed - even for apps without tables - after register for locations::add to work
						$terror = $GLOBALS['phpgw_setup']->process->default_records($terror,$DEBUG);
						echo '<li>' . lang('%1 default values processed', lang($appname)) . ".</li>\n";
					}
					if ( isset($setup_info[$appname]['hooks'])
						&& is_array($setup_info[$appname]['hooks']) )
					{
						$GLOBALS['phpgw_setup']->register_hooks($appname);
						echo '<li>' . lang('%1 hooks registered', lang($appname)) . ".</li>\n";
					}
				}
				$force_en = False;
				if($appname == 'phpgwapi')
				{
					$force_en = true;
				}
				$terror = $GLOBALS['phpgw_setup']->process->add_langs($terror,$DEBUG,$force_en);
				echo '<li>' . lang('%1 translations added', lang($appname)) . ".</li>\n</ul>\n";
				// Add credentials to admins
				$GLOBALS['phpgw_setup']->process->add_credential($appname);
			}
			$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit();
		}

		if(!empty($upgrade) && is_array($upgrade))
		{
			foreach($upgrade as $appname => $key)
			{
				echo '<h3>' . lang('Processing: %1', lang($appname)) . "</h3>\n<ul>";
				$terror = array();
				$terror[] = $setup_info[$appname];

				$GLOBALS['phpgw_setup']->process->upgrade($terror,$DEBUG);
				if ($setup_info[$appname]['tables'])
				{
					echo '<li>' . lang('%1 tables upgraded', lang($appname)) . ".</li>";
					// The process_upgrade() function also handles registration
				}
				else
				{
					echo '<li>' . lang('%1 upgraded', lang($appname)) . ".</li>";
				}

			// Sigurd sep 2010: very slow - run 'Manage Languages' from setup instead. 
			//	$terror = $GLOBALS['phpgw_setup']->process->upgrade_langs($terror,$DEBUG);
			//	echo '<li>' . lang('%1 translations upgraded', lang($appname)) . ".</li>\n</ul>\n";
			echo "<li>To updgrade languages - run <b>'Manage Languages'</b> from setup</li>\n</ul>\n";
			}
		}

		echo "<h3><a href=\"applications.php?debug={$DEBUG}\">" . lang('Done') . "</h3>\n";
		$setup_tpl->pfp('out','footer');
		exit;
	}
	else
	{
		$GLOBALS['phpgw_setup']->html->show_header(lang('Application Management'),False,'config',$ConfigDomain . '(' . $phpgw_domain[$ConfigDomain]['db_type'] . ')');
	}

	$detail = phpgw::get_var('detail', 'string', 'GET'); 
	$resolve = phpgw::get_var('resolve', 'string', 'GET'); 
	if($detail)
	{
		ksort($setup_info[$detail]);
		$name = lang($setup_info[$detail]['name']);
		$setup_tpl->set_var('description', "<h2>{$name}</h2>\n<ul>\n");
		$setup_tpl->pparse('out','header');
		$setup_tpl->pparse('out','detail');
	
		$i = 1;
		foreach($setup_info[$detail] as $key => $val)
		{
			switch ($key)
			{
				// ignore these ones
				case 'application':
				case 'app_group':
				case 'app_order':
				case 'enable':
				case 'name':
				case 'title':
				case '':
					continue 2; //switch is a looping structure in php - see php.net/continue - skwashd jan08

				case 'tables':
					$tblcnt = count($setup_info[$detail][$key]);
					if(is_array($val))
					{
						$key = '<a href="sqltoarray.php?appname=' . $detail . '&amp;submit=True">' . $key . '(' . $tblcnt . ')</a>';
						$val = implode(',<br>', $val);
					}
					break;

				case 'depends':
					$val = parsedep($val);
					break;

				case 'hooks':
				default:
					if (is_array($val))
					{
						$val = implode(', ', $val);
					}
			}

			$i = $i % 2;
			$setup_tpl->set_var('name', $key);
			$setup_tpl->set_var('details', $val);
			$setup_tpl->pparse('out','detail');
			++$i;
		}
		$setup_tpl->set_var('footer_text', "</ul>\n<a href=\"applications.php?debug={$DEBUG}\">" . lang('Go back') . '</a>');
		$setup_tpl->pparse('out','footer');
		exit;
	}
	else if ($resolve)
	{
		$version  = phpgw::get_var('version', 'string', 'GET');
		$notables = phpgw::get_var('notables', 'string', 'GET');
		$setup_tpl->set_var('description',lang('Problem resolution'). ':');
		$setup_tpl->pparse('out','header');

		if ( phpgw::get_var('post', 'string', 'GET') )
		{
			echo '"' . $setup_info[$resolve]['name'] . '" ' . lang('may be broken') . ' ';
			echo lang('because an application it depends upon was upgraded');
			echo '<br />';
			echo lang('to a version it does not know about') . '.';
			echo '<br />';
			echo lang('However, the application may still work') . '.';
		}
		else if ( phpgw::get_var('badinstall', 'string', 'GET') )
		{
			echo '"' . $setup_info[$resolve]['name'] . '" ' . lang('is broken') . ' ';
			echo lang('because of a failed upgrade or install') . '.';
			echo '<br />';
			echo lang('Some or all of its tables are missing') . '.';
			echo '<br />';
			echo lang('You should either uninstall and then reinstall it, or attempt manual repairs') . '.';
		}
		elseif (!$version)
		{
			if($setup_info[$resolve]['enabled'])
			{
				echo '"' . $setup_info[$resolve]['name'] . '" ' . lang('is broken') . ' ';
			}
			else
			{
				echo '"' . $setup_info[$resolve]['name'] . '" ' . lang('is disabled') . ' ';
			}

			if (!$notables)
			{
				if($setup_info[$resolve]['status'] == 'D')
				{
					echo lang('because it depends upon') . ':<br />' . "\n";
					list($depapp,$depver) = parsedep($setup_info[$resolve]['depends'],False);
                                $depapp_count = count($depapp);
					for ($i=0; $i<$depapp_count; ++$i)
					{
						echo '<br />' . $depapp[$i] . ': ';
						$list = '';
						foreach($depver[$i] as $x => $y)
						{
							$list .= $y . ', ';
						}
						$list = substr($list,0,-2);
						echo "$list\n";
					}
					echo '<br /><br />' . lang('The table definition was correct, and the tables were installed') . '.';
				}
				else
				{
					echo lang('because it was manually disabled') . '.';
				}
			}
			elseif($setup_info[$resolve]['enable'] == 2)
			{
				echo lang('because it is not a user application, or access is controlled via acl') . '.';
			}
			elseif($setup_info[$resolve]['enable'] == 0)
			{
				echo lang('because the enable flag for this app is set to 0, or is undefined') . '.';
			}
			else
			{
				echo lang('because it requires manual table installation, <br />or the table definition was incorrect') . ".\n"
					. lang("Please check for sql scripts within the application's directory") . '.';
			}
			echo '<br />' . lang('However, the application is otherwise installed') . '.';
		}
		else
		{
			echo $setup_info[$resolve]['name'] . ' ' . lang('has a version mismatch') . ' ';
			echo lang('because of a failed upgrade, or the database is newer than the installed version of this app') . '.';
			echo '<br />';
			echo lang('If the application has no defined tables, selecting upgrade should remedy the problem') . '.';
			echo '<br />' . lang('However, the application is otherwise installed') . '.';
		}

		echo '<br /><a href="applications.php?debug='.$DEBUG.'">' . lang('Go back') . '</a>';
		$setup_tpl->pparse('out','footer');
		exit;
	}
	else if ( phpgw::get_var('globals', 'string', 'GET') )
	{
		$setup_tpl->set_var('description','<a href="applications.php?debug='.$DEBUG.'">' . lang('Go back') . '</a>');
		$setup_tpl->pparse('out','header');

		
		$name = (isset($setup_info[$detail]['title']) ? $setup_info[$detail]['title'] 
			 : lang($setup_info[$detail]['name']));
		$setup_tpl->set_var('name',lang('application'));
		$setup_tpl->set_var('details', $name);
		$setup_tpl->set_var('bg_color', 'th');
		$setup_tpl->pparse('out','detail');
	
		$setup_tpl->set_var('bg_color','row_on');
		$setup_tpl->set_var('details', lang('register_globals_' . $_GET['globals']));
		$setup_tpl->pparse('out','detail');
		$setup_tpl->pparse('out','footer');
		exit;
	}
	else
	{
		$setup_tpl->set_var('description',lang('Select the desired action(s) from the available choices'));
		$setup_tpl->pparse('out','header');

		$setup_tpl->set_var('appdata',lang('Application Data'));
		$setup_tpl->set_var('actions',lang('Actions'));
		$setup_tpl->set_var('action_url','applications.php');
		$setup_tpl->set_var('app_info',lang('Application Name'));
		$setup_tpl->set_var('app_status',lang('Application Status'));
		$setup_tpl->set_var('app_currentver',lang('Current Version'));
		$setup_tpl->set_var('app_version',lang('Available Version'));
		$setup_tpl->set_var('app_install',lang('Install'));
		$setup_tpl->set_var('app_remove',lang('Remove'));
		$setup_tpl->set_var('app_upgrade',lang('Upgrade'));
		$setup_tpl->set_var('app_resolve',lang('Resolve'));
		$setup_tpl->set_var('check','stock_form-checkbox.png');
		$setup_tpl->set_var('install_all',lang('Install All'));
		$setup_tpl->set_var('upgrade_all',lang('Upgrade All'));
		$setup_tpl->set_var('remove_all',lang('Remove All'));
		$setup_tpl->set_var('lang_debug',lang('enable debug messages'));
		$setup_tpl->set_var('debug','<input type="checkbox" name="debug" value="True"' .($DEBUG ? ' checked' : '') . '>');

		$setup_tpl->pparse('out','app_header');

		$i = 0;
		foreach($setup_info as $key => $value)
		{
			if( isset($value['name']) && $value['name'] != 'phpgwapi' && $value['name'] != 'notifywindow')
			{
				++$i;
				$row = $i % 2 ? 'off' : 'on';
				$value['title'] = !isset($value['title']) || !strlen($value['title']) ? str_replace('*', '', lang($value['name'])) : $value['title'];
				$setup_tpl->set_var('apptitle',$value['title']);
				$setup_tpl->set_var('currentver', isset($value['currentver']) ? $value['currentver'] : '');
				$setup_tpl->set_var('version',$value['version']);
				$setup_tpl->set_var('bg_class',  "row_{$row}");
				$setup_tpl->set_var('row_remove', '');
                        
				switch($value['status'])
				{
					case 'C':
						$setup_tpl->set_var('row_remove', "row_remove_{$row}");
						$setup_tpl->set_var('remove','<input type="checkbox" name="remove[' . $value['name'] . ']" />');
						$setup_tpl->set_var('upgrade','&nbsp;');
						if (!$GLOBALS['phpgw_setup']->detection->check_app_tables($value['name']))
						{
							// App installed and enabled, but some tables are missing
							$setup_tpl->set_var('instimg','stock_database.png');
							$setup_tpl->set_var('bg_class', "row_err_table_{$row}");
							$setup_tpl->set_var('instalt',lang('Not Completed'));
							$setup_tpl->set_var('resolution','<a href="applications.php?resolve=' . $value['name'] . '&amp;badinstall=True">' . lang('Potential Problem') . '</a>');
							$status = lang('Requires reinstall or manual repair') . ' - ' . $value['status'];
						}
						else
						{
							$setup_tpl->set_var('instimg','stock_yes.png');
							$setup_tpl->set_var('instalt', lang('%1 status - %2', $value['title'], lang('Completed')));
							$setup_tpl->set_var('install','&nbsp;');
							if($value['enabled'])
							{
								$setup_tpl->set_var('resolution','');
								$status = "[{$value['status']}] " . lang('OK');
							}
							else
							{
								$notables = '';
								if ( isset($value['tables'][0]) 
									&& $value['tables'][0] != '')
								{
									$notables = '&amp;notables=True';
								}
								$setup_tpl->set_var('bg_class', "row_err_gen_{$row}");
								$setup_tpl->set_var('resolution',
									'<a href="applications.php?resolve=' . $value['name'] .  $notables . '">' . lang('Possible Reasons') . '</a>'
								);
								$status = "[{$value['status']}] " . lang('Disabled');
							}
						}
						break;
					case 'U':
						$setup_tpl->set_var('instimg','package-generic.png');
						$setup_tpl->set_var('instalt',lang('Not Completed'));
						if ( !isset($value['currentver']) || !$value['currentver'] )
						{
							$setup_tpl->set_var('bg_class', "row_install_{$row}");
							$status = "[{$value['status']}] " . lang('Please install');
							if ( isset($value['tables']) && is_array($value['tables']) && $value['tables'] && $GLOBALS['phpgw_setup']->detection->check_app_tables($value['name'],True))
							{
								// Some tables missing
								$setup_tpl->set_var('bg_class', "row_err_gen_{$row}");
								$setup_tpl->set_var('instimg','stock_database.png');
								$setup_tpl->set_var('row_remove', 'row_remove_' . ($i ? 'off' : 'on') );
								$setup_tpl->set_var('remove','<input type="checkbox" name="remove[' . $value['name'] . ']" />');
								$setup_tpl->set_var('resolution','<a href="applications.php?resolve=' . $value['name'] . '&amp;badinstall=True">' . lang('Potential Problem') . '</a>');
								$status = "[{$value['status']}] " . lang('Requires reinstall or manual repair');
							}
							else
							{
								$setup_tpl->set_var('remove','&nbsp;');
								$setup_tpl->set_var('resolution','');
								$status = "[{$value['status']}] " . lang('Available to install');
							}
							$setup_tpl->set_var('install','<input type="checkbox" name="install[' . $value['name'] . ']" />');
							$setup_tpl->set_var('upgrade','&nbsp;');
						}
						else
						{
							$setup_tpl->set_var('bg_class', "row_upgrade_{$row}");
							$setup_tpl->set_var('install','&nbsp;');
							// TODO display some info about breakage if you mess with this app
							$setup_tpl->set_var('upgrade','<input type="checkbox" name="upgrade[' . $value['name'] . ']">');
							$setup_tpl->set_var('row_remove', 'row_remove_' . ($i ? 'off' : 'on') );
							$setup_tpl->set_var('remove','<input type="checkbox" name="remove[' . $value['name'] . ']">');
							$setup_tpl->set_var('resolution','');
							$status = "[{$value['status']}] " . lang('Requires upgrade');
						}
						break;
					case 'V':
						$setup_tpl->set_var('instimg','package-generic.png');
						$setup_tpl->set_var('instalt',lang('Not Completed'));
						$setup_tpl->set_var('install','&nbsp;');
						$setup_tpl->set_var('row_remove', 'row_remove_' . ($i ? 'off' : 'on') );
						$setup_tpl->set_var('remove','<input type="checkbox" name="remove[' . $value['name'] . ']">');
						$setup_tpl->set_var('upgrade','<input type="checkbox" name="upgrade[' . $value['name'] . ']">');
						$setup_tpl->set_var('resolution','<a href="applications.php?resolve=' . $value['name'] . '&amp;version=True">' . lang('Possible Solutions') . '</a>');
						$status = "[{$value['status']}] " . lang('Version Mismatch');
						break;
					case 'D':
						$setup_tpl->set_var('bg_class', "row_err_gen_{$row}");
						$depstring = parsedep($value['depends']);
						$setup_tpl->set_var('instimg', 'stock_no.png');
						$setup_tpl->set_var('instalt',lang('Dependency Failure'));
						$setup_tpl->set_var('install','&nbsp;');
						$setup_tpl->set_var('remove','&nbsp;');
						$setup_tpl->set_var('upgrade','&nbsp;');
						$setup_tpl->set_var('resolution','<a href="applications.php?resolve=' . $value['name'] . '">' . lang('Possible Solutions') . '</a>');
						$status = "[{$value['status']}] " . lang('Dependency Failure') . $depstring;
						break;
					case 'P':
						$setup_tpl->set_var('bg_class', "row_err_gen_{$row}");
						$depstring = parsedep($value['depends']);
						$setup_tpl->set_var('instimg', 'stock_no.png');
						$setup_tpl->set_var('instalt',lang('Post-install Dependency Failure'));
						$setup_tpl->set_var('install','&nbsp;');
						$setup_tpl->set_var('remove','&nbsp;');
						$setup_tpl->set_var('upgrade','&nbsp;');
						$setup_tpl->set_var('resolution','<a href="applications.php?resolve=' . $value['name'] . '&post=True">' . lang('Possible Solutions') . '</a>');
						$status = "[{$value['status']}] " . lang('Post-install Dependency Failure') . $depstring;
						break;
					default:
						$setup_tpl->set_var('instimg','package-generic.png');
						$setup_tpl->set_var('instalt',lang('Not Completed'));
						$setup_tpl->set_var('install','&nbsp;');
						$setup_tpl->set_var('remove','&nbsp;');
						$setup_tpl->set_var('upgrade','&nbsp;');
						$setup_tpl->set_var('resolution','');
						$status = '';
						break;
				}
				$setup_tpl->set_var('appinfo', $status);
				$setup_tpl->set_var('appname', $value['name']);

				$setup_tpl->pparse('out','apps');
			}
		}

		$setup_tpl->set_var('submit',lang('Save'));
		$setup_tpl->set_var('cancel',lang('Cancel'));
		$setup_tpl->pparse('out','app_footer');
		$setup_tpl->pparse('out','footer');
		$GLOBALS['phpgw_setup']->html->show_footer();
	}