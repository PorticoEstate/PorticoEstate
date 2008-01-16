<?php
	/**
	* Setup
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package setup
	* @version $Id: lang.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	$phpgw_info = array();
	$error = '';
	if ( !isset($included) || !$included )
	{
		$GLOBALS['phpgw_info']['flags'] = array
		(
			'noheader' => True,
			'nonavbar' => True,
			'currentapp' => 'home',
			'noapi' => True
		);
		$included = '';
		$newinstall = false;
		
		/**
		 * Include setup functions
		 */
		include('./inc/functions.inc.php');

		// Authorize the user to use setup app and load the database
		// Does not return unless user is authorized
		if (!$GLOBALS['phpgw_setup']->auth('Config'))
		{
			Header('Location: index.php');
			exit;
		}
		$GLOBALS['phpgw_setup']->loaddb();

		/**
		 * Include API Common class
		 */
		include(PHPGW_API_INC.'/class.common.inc.php');

		$common = new phpgwapi_common;
	}
	elseif ($included != 'from_login')
	{
		$newinstall = true;
		$lang_selected['en'] = 'en';
		$submit = true;
	}

	if (!defined('MAX_MESSAGE_ID_LENGTH'))
	{
		define('MAX_MESSAGE_ID_LENGTH',230);
	}

	if (isset($_POST['submit']) && $_POST['submit'] )
	{
		if ( !isset($GLOBALS['phpgw']->shm) 
			|| !is_object($GLOBALS['phpgw']->shm) )
		{
			$GLOBALS['phpgw']->shm = CreateObject('phpgwapi.shm');
		}
				
		$lang_selected = $_POST['lang_selected'];
		$upgrademethod = $_POST['upgrademethod'];
		if(!isset($GLOBALS['phpgw_info']['server']['lang_ctimes']))
		{
			$GLOBALS['phpgw_info']['server']['lang_ctimes'] = array();
		}
		
		if (!isset($GLOBALS['phpgw_info']['server']) && $upgrademethod != 'dumpold')
		{
			$GLOBALS['phpgw_setup']->db->query("select * from phpgw_config WHERE config_app='phpgwapi' AND config_name='lang_ctimes'",__LINE__,__FILE__);
			if ($GLOBALS['phpgw_setup']->db->next_record())
			{
				$GLOBALS['phpgw_info']['server']['lang_ctimes'] = unserialize(stripslashes($GLOBALS['phpgw_setup']->db->f('config_value')));
			}
		}
		
		$GLOBALS['phpgw_setup']->db->transaction_begin();
		if (count($lang_selected))
		{
			if ($upgrademethod == 'dumpold')
			{
				// dont delete the custom main- & loginscreen messages every time
				$GLOBALS['phpgw_setup']->db->query("DELETE FROM phpgw_lang where app_name != 'mainscreen' AND app_name != 'loginscreen'",__LINE__,__FILE__);
				//echo '<br />Test: dumpold';
				$GLOBALS['phpgw_info']['server']['lang_ctimes'] = array();
			}
			foreach($lang_selected as $lang)
			{
				if ( strlen($lang) != 2 )
				{
					echo 'Invalid lang code: ' . htmlspecialchars($lang) . ' - skipping<br>\n';
					continue;
				}

				$lang = strtolower($lang);
				//echo '<br />Working on: ' . $lang;
				if(function_exists('sem_get') && function_exists('shmop_open'))
				{
					$GLOBALS['phpgw']->shm->delete_key('lang_' . $lang);
				}
				$addlang = False;
				if ($upgrademethod == 'addonlynew')
				{
					//echo "<br />Test: addonlynew - select count(*) from phpgw_lang where lang='".$lang."'";
					$GLOBALS['phpgw_setup']->db->query("SELECT COUNT(*) FROM phpgw_lang WHERE lang='".$lang."'",__LINE__,__FILE__);
					$GLOBALS['phpgw_setup']->db->next_record();

					if ($GLOBALS['phpgw_setup']->db->f(0) == 0)
					{
						//echo '<br />Test: addonlynew - True';
						$addlang = True;
					}
				}
				if (($addlang && $upgrademethod == 'addonlynew') || ($upgrademethod != 'addonlynew'))
				{
					//echo '<br />Test: loop above file()';
					$setup_info = $GLOBALS['phpgw_setup']->detection->get_versions();
					$setup_info = $GLOBALS['phpgw_setup']->detection->get_db_versions($setup_info);
					$raw = array();
					// Visit each app/setup dir, look for a phpgw_lang file
					foreach ( $setup_info as $app )
					{
						if(!array_key_exists('name', $app))
						{
							continue;
						}

						$appfile = PHPGW_SERVER_ROOT . "/{$app['name']}/setup/phpgw_{$lang}.lang";
						//echo '<br />Checking in: ' . $app['name'];
						if($GLOBALS['phpgw_setup']->app_registered($app['name']) && file_exists($appfile))
						{
							//echo '<br />Including: ' . $appfile;
							$lines = file($appfile);
							foreach($lines as $cnt => $line)
							{
								$entry = explode("\t", $line);
								//Make sure the lang files only have valid entries
								if ( count($entry) != 4  || $entry[2] != $lang )
								{
									$err_line = $cnt + 1;
									$error .= "Invalid entry in {$app['name']}/setup/phpgw_{$lang}.lang @ line {$err_line}: <code>" . htmlspecialchars(preg_replace('/\t/', '\\t', $line)) . "</code> - skipping<br>\n";
									continue;
								}

								list($message_id,$app_name,$ignore,$content) = $entry;
								$message_id = $GLOBALS['phpgw_setup']->db->db_addslashes(substr(chop($message_id),0,MAX_MESSAGE_ID_LENGTH));
								$app_name = $GLOBALS['phpgw_setup']->db->db_addslashes(chop($app_name));
								$content = $GLOBALS['phpgw_setup']->db->db_addslashes(chop($content));
								
								$raw[$app_name][$message_id] = $content;
							}
							$GLOBALS['phpgw_info']['server']['lang_ctimes'][$lang][$app['name']] = filectime($appfile);
						}
					}
					foreach($raw as $app_name => $ids)
					{
						foreach($ids as $message_id => $content)
						{
							$addit = False;
							//echo '<br />APPNAME:' . $app_name . ' PHRASE:' . $message_id;
							if ($upgrademethod == 'addmissing')
							{
								//echo '<br />Test: addmissing';
								$GLOBALS['phpgw_setup']->db->query("SELECT COUNT(*) FROM phpgw_lang WHERE message_id='$message_id' and lang='$lang' and (app_name='$app_name' or app_name='common')",__LINE__,__FILE__);
								$GLOBALS['phpgw_setup']->db->next_record();

								if ($GLOBALS['phpgw_setup']->db->f(0) == 0)
								{
									//echo '<br />Test: addmissing - True - Total: ' . $GLOBALS['phpgw_setup']->db->f(0);
									$addit = True;
								}
							}

							if ($addit || (isset($newinstall) && $newinstall) || $upgrademethod == 'addonlynew' || $upgrademethod == 'dumpold')
							{
								if($message_id && $content)
								{
									//echo "<br />adding - insert into phpgw_lang values ('$message_id','$app_name','$lang','$content')";
									$result = $GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_lang (message_id,app_name,lang,content) VALUES('$message_id','$app_name','$lang','$content')",__LINE__,__FILE__);
									if (intval($result) <= 0)
									{
										$error .= "Error inserting record: phpgw_lang values ('$message_id','$app_name','$lang','$content')<br>";
									}
								}
							}
						}
					}
				}
			}
			$GLOBALS['phpgw_setup']->db->transaction_commit();
			
			$GLOBALS['phpgw_setup']->db->query("DELETE from phpgw_config WHERE config_app='phpgwapi' AND config_name='lang_ctimes'",__LINE__,__FILE__);
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_config(config_app,config_name,config_value) VALUES ('phpgwapi','lang_ctimes','".
				$GLOBALS['phpgw_setup']->db->db_addslashes(serialize($GLOBALS['phpgw_info']['server']['lang_ctimes']))."')",__LINE__,__FILE__);
		}

		if ( $error )
		{
			$error = <<<HTML
				<div class="err">
					<h2>ERROR</h2>
					$error
				</div>

HTML;
			if( !$included )
			{
				$tpl_root = $GLOBALS['phpgw_setup']->html->setup_tpl_dir('setup');
				$setup_tpl = CreateObject('phpgwapi.Template',$tpl_root);
				$setup_tpl->set_file(array
				(
					'T_head'		=> 'head.tpl',
					'T_footer'		=> 'footer.tpl',
				));

				$stage_title = lang('Multi-Language support setup');
				$stage_desc  = lang('ERROR');

				$GLOBALS['phpgw_setup']->html->show_header("$stage_title: $stage_desc", false, 'config', $ConfigDomain . '(' . $phpgw_domain[$ConfigDomain]['db_type'] . ')');
				echo $error;
				$return = lang('Return to Multi-Language support setup');
				echo <<<HTML
				<div>
					<a href="./lang.php">$return</a>
				</div>

HTML;
				$GLOBALS['phpgw_setup']->html->show_footer();
				exit;
			}
			else 
			{
				echo $error;
			}
		}

		if ( !$included )
		{
			Header('Location: index.php');
			exit;
		}
	}
	else
	{
		if ( isset($_POST['cancel']) && $_POST['cancel'] )
		{
			Header('Location: index.php');
			exit;
		}

		if ( !$included )
		{
			$tpl_root = $GLOBALS['phpgw_setup']->html->setup_tpl_dir('setup');
			$setup_tpl = CreateObject('phpgwapi.Template',$tpl_root);
			$setup_tpl->set_file(array
			(
				'T_head'		=> 'head.tpl',
				'T_footer'		=> 'footer.tpl',
				'T_alert_msg'	=> 'msg_alert_msg.tpl',
				'T_lang_main'	=> 'lang_main.tpl'
			));

			$setup_tpl->set_block('T_lang_main','B_choose_method','V_choose_method');

			$stage_title = lang('Multi-Language support setup');
			$stage_desc  = lang('This program will help you upgrade or install different languages for phpGroupWare');
			$tbl_width   = $newinstall ? '60%' : '80%';
			$td_colspan  = $newinstall ? '1' : '2';
			$td_align    = $newinstall ? ' align="center"' : '';
			$hidden_var1 = $newinstall ? '<input type="hidden" name="newinstall" value="True">' : '';

			$dir = dir('../phpgwapi/setup');
			while(($file = $dir->read()) !== false)
			{
				if(substr($file, 0, 6) == 'phpgw_')
				{
					$avail_lang[] = "'" . substr($file, 6, 2) . "'";
				}
			}

			if (!$newinstall && !isset($GLOBALS['phpgw_info']['setup']['installed_langs']))
			{
				$GLOBALS['phpgw_setup']->detection->check_lang(false);	// get installed langs
			}
			$select_box_desc = lang('Select which languages you would like to use');
			$select_box = '';

			$GLOBALS['phpgw_setup']->db->query('SELECT lang_id,lang_name, available '
							. 'FROM phpgw_languages '
							. 'WHERE lang_id IN('.implode(',', $avail_lang).') ORDER BY lang_name');

			$select_box_langs = '';
			while ($GLOBALS['phpgw_setup']->db->next_record())
			{
				$id = $GLOBALS['phpgw_setup']->db->f('lang_id');
				$selected = isset($GLOBALS['phpgw_info']['setup']['installed_langs'][$id]) ? ' selected' : '';
				$select_box_langs .= "<option value=\"$id\"{$selected}>" . $GLOBALS['phpgw_setup']->db->f('lang_name') . "</option>\n";
			}

			$GLOBALS['phpgw_setup']->db->query("UPDATE phpgw_languages SET available = 'Yes' WHERE lang_id IN('" . implode("','", $avail_lang) . "'"); 

			if ( !$newinstall )
			{
				$meth_desc = lang('Select which method of upgrade you would like to do');
				$blurb_addonlynew = lang('Only add languages that are not in the database already');
				$blurb_addmissing = lang('Only add new phrases');
				$blurb_dumpold = lang('Delete all old languages and install new ones');

				$setup_tpl->set_var('meth_desc',$meth_desc);
				$setup_tpl->set_var('blurb_addonlynew',$blurb_addonlynew);
				$setup_tpl->set_var('blurb_addmissing',$blurb_addmissing);
				$setup_tpl->set_var('blurb_dumpold',$blurb_dumpold);
				$setup_tpl->parse('V_choose_method','B_choose_method');
			}
			else
			{
				$setup_tpl->set_var('V_choose_method','');
			}

			$setup_tpl->set_var('stage_title',$stage_title);
			$setup_tpl->set_var('stage_desc',$stage_desc);
			$setup_tpl->set_var('tbl_width',$tbl_width);
			$setup_tpl->set_var('td_colspan',$td_colspan);
			$setup_tpl->set_var('td_align',$td_align);
			$setup_tpl->set_var('hidden_var1',$hidden_var1);
			$setup_tpl->set_var('select_box_desc',$select_box_desc);
			$setup_tpl->set_var('select_box_langs',$select_box_langs);

			$setup_tpl->set_var('lang_install',lang('install'));
			$setup_tpl->set_var('lang_cancel',lang('cancel'));

			$ConfigDomain = $_COOKIE['ConfigDomain'] ? $_COOKIE['ConfigDomain'] : $_POST['ConfigDomain'];
			$GLOBALS['phpgw_setup']->html->show_header("$stage_title",False,'config',$ConfigDomain . '(' . $phpgw_domain[$ConfigDomain]['db_type'] . ')');
			$setup_tpl->pparse('out','T_lang_main');
			$GLOBALS['phpgw_setup']->html->show_footer();
		}
	}
?>
