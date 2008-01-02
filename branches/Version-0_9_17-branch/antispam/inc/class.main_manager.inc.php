<?php
/**************************************************************************\
* phpGroupWare - Antispam                                                  *
* http://www.phpgroupware.org                                              *
* This application written by:                                             *
*                             Marco Andriolo-Stagno <stagno@prosa.it>      *
*                             PROSA <http://www.prosa.it>                  *
* -------------------------------------------------------------------------*
* Funding for this program was provided by http://www.seeweb.com           *
* -------------------------------------------------------------------------*
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

/* $Id: class.main_manager.inc.php 11580 2002-11-26 17:57:08Z ceb $ */

	class main_manager
	{
		var $public_functions = array
		(
			'handle_rules'	=>	True,
			'edit_user'		=>	True
		);

		function row_action($action,$type,$account_id,$account_lid)
		{
			$tmparr = array
			(
				'menuaction'	=>	'antispam.main_manager.' . $action . '_' . $type,
				'account_id'	=>	$account_id,
				'login_id'		=>	$account_lid
			);       
		return '<a href="' . $GLOBALS['phpgw']->link('/index.php',$tmparr) . '"> ' . lang($action) . ' </a>';
		}

		function handle_rules()
		{
  			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$tpl = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
			$tpl->set_file(array('accounts' => 'accounts.tpl'));
			$tpl->set_block('accounts','list','list');
			$tpl->set_block('accounts','row','row');
			$tpl->set_block('accounts','row_empty','row_empty');
			$var = array
			(
				'bg_color'	=>	$GLOBALS['phpgw_info']['theme']['bg_color'],
				'th_bg'	=>	$GLOBALS['phpgw_info']['theme']['th_bg'],
				'lang_loginid'	=>	lang('loginid'),
				'lang_lastname'	=>	lang('last name'),
				'lang_firstname'	=>	lang('first name'),
				'lang_edit'	=>	lang('edit')
			);
			$tpl->set_var($var);
			$accounts = $GLOBALS['phpgw']->accounts->get_list('accounts');
			$total = $GLOBALS['phpgw']->accounts->total;
			$alternate = $GLOBALS['phpgw']->nextmatch = CreateObject('phpgwapi.nextmatchs');
			while(list($null,$account)=each($accounts))
			{
				$color = $alternate->alternate_row_color($bgcolor);
				$var = array
				(
					'tr_color'	=>	$color,
					'row_loginid'	=>	$account['account_lid'],
					'row_firstname'	=>	(!$account['account_firstname']?'&nbsp':$account['account_firstname']),
					'row_lastname'	=>	(!$account['account_lastname']?'&nbsp':$account['account_lastname'])
				);
				$tpl->set_var($var);
				$tpl->set_var('row_edit',$this->row_action('edit','user',$account['account_id'],$account['account_lid']));
				$tpl->parse('rows','row',True);
			}
			$tpl->pfp('out','list');
		}

		function edit_user()
		{
			$edit = CreateObject('antispam.checker');
			$edit->handle_user_settings($GLOBALS['HTTP_GET_VARS']['login_id']);
	  }

	}

?>
