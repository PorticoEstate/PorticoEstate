<?php
	/**
	* Trouble Ticket System
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @version $Id$
	*/


	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'  => 'tts', 
		'noheader'    => True, 
		'nonavbar'    => True, 
		'noappheader' => True,
		'noappfooter' => True,
		'enable_config_class'     => True,
		'enable_nextmatchs_class' => True
	);

	/**
	 * Include phpgroupware header
	 */
	include('../header.inc.php');

	$option_names = array(lang('Disabled'), lang('Users choice'), lang('Force'));
	$owner_selected = array ();
	$group_selected = array ();
	$assigned_selected = array ();

	$GLOBALS['phpgw']->config->read_repository();

	if ($_POST['submit'])
	{
		if ($_POST['usemailnotification'])
		{
			$GLOBALS['phpgw']->config->config_data['mailnotification'] = true;
		}
		else
		{
			$GLOBALS['phpgw']->config->config_data['mailnotification'] = false;
		}

		if ($_POST['ownernotification'])
		{
			$GLOBALS['phpgw']->config->config_data['ownernotification'] = $_POST['ownernotification'];
		}
		else
		{
			unset($GLOBALS['phpgw']->config->config_data['ownernotification']);
		}

		if ($_POST['groupnotification'])
		{
			$GLOBALS['phpgw']->config->config_data['groupnotification'] = $_POST['groupnotification'];
		}
		else
		{
			unset($GLOBALS['phpgw']->config->config_data['groupnotification']);
		}

		if ($_POST['assignednotification'])
		{
			$GLOBALS['phpgw']->config->config_data['assignednotification'] = $_POST['assignednotification'];
		}
		else
		{
			unset($GLOBALS['phpgw']->config->config_data['assignednotification']);
		}

		$GLOBALS['phpgw']->config->save_repository(True);
		Header('Location: ' . $GLOBALS['phpgw']->link('/admin/index.php'));
	}
	$GLOBALS['phpgw']->config->config_data['mailnotification'];

	$GLOBALS['phpgw']->common->phpgw_header();
	echo parse_navbar();

	$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
	$GLOBALS['phpgw']->template->set_file(array('admin' => 'admin.tpl'));
	$GLOBALS['phpgw']->template->set_block('admin', 'tts_select_options','tts_select_options');

	$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/tts/admin.php'));

	$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
	$GLOBALS['phpgw']->template->set_var('tr_color',$tr_color);

	$GLOBALS['phpgw']->template->set_var('lang_mailnotification',lang('Use email notification'));
	if ($GLOBALS['phpgw']->config->config_data['mailnotification'])
	{
		$GLOBALS['phpgw']->template->set_var('mailnotification',' checked');
	}
	else
	{
		$GLOBALS['phpgw']->template->set_var('mailnotification','');
	}

	$GLOBALS['phpgw']->template->set_var('lang_ownernotification',lang('Owner'));
	if ($GLOBALS['phpgw']->config->config_data['ownernotification'])
	{
		$owner_selected[$GLOBALS['phpgw']->config->config_data['ownernotification']]=' selected';
	//	$GLOBALS['phpgw']->template->set_var('ownernotification',' checked');
	}
	else
	{
	//	$GLOBALS['phpgw']->template->set_var('ownernotification','');
	}

	$GLOBALS['phpgw']->template->set_var('lang_groupnotification',lang('Group'));
	if ($GLOBALS['phpgw']->config->config_data['groupnotification'])
	{
		$group_selected[$GLOBALS['phpgw']->config->config_data['groupnotification']]=' selected';
	//	$GLOBALS['phpgw']->template->set_var('groupnotification',' checked');
	}
	else
	{
		//	$GLOBALS['phpgw']->template->set_var('groupnotification','');
	}
	$GLOBALS['phpgw']->template->set_var('lang_assignednotification',lang('Assigned to'));
	if ($GLOBALS['phpgw']->config->config_data['assignednotification'])
	{
		$assigned_selected[$GLOBALS['phpgw']->config->config_data['assignednotification']]=' selected';
	//	$GLOBALS['phpgw']->template->set_var('assignednotification',' checked');
	}
	else
	{
		//	$GLOBALS['phpgw']->template->set_var('assignednotification','');
	}

	for ($i=0; $i<3; $i++)
	{
		$GLOBALS['phpgw']->template->set_var('tts_optionname', $option_names[$i]);
		$GLOBALS['phpgw']->template->set_var('tts_optionvalue', $i);
		$GLOBALS['phpgw']->template->set_var('tts_optionselected', $owner_selected[$i]);
		$GLOBALS['phpgw']->template->parse('tts_owneroptions','tts_select_options',true);
	}

	for ($i=0; $i<3; $i++)
	{
		$GLOBALS['phpgw']->template->set_var('tts_optionname', $option_names[$i]);
		$GLOBALS['phpgw']->template->set_var('tts_optionvalue', $i);
		$GLOBALS['phpgw']->template->set_var('tts_optionselected', $group_selected[$i]);
		$GLOBALS['phpgw']->template->parse('tts_groupoptions','tts_select_options',true);
	}

	for ($i=0; $i<3; $i++)
	{
		$GLOBALS['phpgw']->template->set_var('tts_optionname', $option_names[$i]);
		$GLOBALS['phpgw']->template->set_var('tts_optionvalue', $i);
		$GLOBALS['phpgw']->template->set_var('tts_optionselected', $assigned_selected[$i]);
		$GLOBALS['phpgw']->template->parse('tts_assignedoptions','tts_select_options',true);
	}

	$GLOBALS['phpgw']->template->set_var('lang_admin',lang('TTS').' '.lang('Admin'));
	$GLOBALS['phpgw']->template->set_var('lang_submit',lang('submit'));
	$GLOBALS['phpgw']->template->set_var('tts_select_options','');

	$GLOBALS['phpgw']->template->pparse('out','admin');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
