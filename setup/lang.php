<?php
	/**
	* Setup
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package setup
	* @version $Id$
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
		$GLOBALS['phpgw']->db =& $GLOBALS['phpgw_setup']->db;

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

	if (isset($_POST['submit']) && $_POST['submit'] )
	{				
		$lang_selected = $_POST['lang_selected'];
		$upgrademethod = $_POST['upgrademethod'];

		$error = $GLOBALS['phpgw']->translation->update_db($lang_selected, $upgrademethod);

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
				$setup_tpl = CreateObject('phpgwapi.template',$tpl_root);
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
			$setup_tpl = CreateObject('phpgwapi.template',$tpl_root);
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

			$checkbox_langs	= '';
			while ($GLOBALS['phpgw_setup']->db->next_record())
			{
				$id = $GLOBALS['phpgw_setup']->db->f('lang_id');
				$name = $GLOBALS['phpgw_setup']->db->f('lang_name');
				$checked = isset($GLOBALS['phpgw_info']['setup']['installed_langs'][$id]) ? ' checked = "checked"' : '';

				$checkbox_langs .="<label><input type=\"checkbox\" name=\"lang_selected[]\" value=\"$id\"$checked>{$name}</label><br>";
			}

			$GLOBALS['phpgw_setup']->db->query("UPDATE phpgw_languages SET available = 'Yes' WHERE lang_id IN('" . implode("','", $avail_lang) . "')");

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
			$setup_tpl->set_var('checkbox_langs',$checkbox_langs);

			$setup_tpl->set_var('lang_install',lang('install'));
			$setup_tpl->set_var('lang_cancel',lang('cancel'));

			$ConfigDomain = $_COOKIE['ConfigDomain'] ? $_COOKIE['ConfigDomain'] : $_POST['ConfigDomain'];
			$GLOBALS['phpgw_setup']->html->show_header("$stage_title",False,'config',$ConfigDomain . '(' . $phpgw_domain[$ConfigDomain]['db_type'] . ')');
			$setup_tpl->pparse('out','T_lang_main');
			$GLOBALS['phpgw_setup']->html->show_footer();
		}
	}
