<?php
	/***************************************************************************\
	* eGroupWare                                                                *
	* http://www.egroupware.org                                                 *
	* http://www.linux-at-work.de                                               *
	* Written by : Lars Kneschke [lkneschke@linux-at-work.de]                   *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/
	/* $Id$ */

	class uiuserdata
	{

		var $public_functions = array
		(
			'editUserData'	=> True,
			'saveUserData'	=> True
		);

		function __construct()
		{
			$this->t			=& CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
			$this->boemailadmin		= new emailadmin_bo();
		}
	
		function display_app_header()
		{
			$GLOBALS['phpgw']->js->validate_file('jscode','editUserdata','emailadmin');
			$GLOBALS['phpgw_info']['flags']['include_xajax'] = True;

			$GLOBALS['phpgw']->common->egw_header();
			echo parse_navbar();
		}

		function editUserData($_useCache='0')
		{
			$accountID = $_GET['account_id'];			
			$GLOBALS['account_id'] = $accountID;

			$this->display_app_header();

			$this->translate();

			$this->t->set_file(array("editUserData" => "edituserdata.tpl"));
			$this->t->set_block('editUserData','form','form');
			$this->t->set_block('editUserData','link_row','link_row');
			$this->t->set_var("th_bg",$GLOBALS['phpgw_info']["theme"]["th_bg"]);
			$this->t->set_var("tr_color1",$GLOBALS['phpgw_info']["theme"]["row_on"]);
			$this->t->set_var("tr_color2",$GLOBALS['phpgw_info']["theme"]["row_off"]);

			$this->t->set_var("lang_email_config",lang("edit email settings"));
			$this->t->set_var("lang_emailAddress",lang("email address"));
			$this->t->set_var("lang_emailaccount_active",lang("email account active"));
			$this->t->set_var("lang_mailAlternateAddress",lang("alternate email address"));
			$this->t->set_var("lang_mailRoutingAddress",lang("forward email's to"));
			$this->t->set_var("lang_forward_also_to",lang("forward also to"));
			$this->t->set_var("lang_button",lang("save"));
			$this->t->set_var("lang_deliver_extern",lang("deliver extern"));
			$this->t->set_var("lang_deliver_extern",lang("deliver extern"));
			$this->t->set_var("lang_edit_email_settings",lang("edit email settings"));
			$this->t->set_var("lang_ready",lang("Done"));
			$this->t->set_var("link_back",$GLOBALS['phpgw']->link('/admin/accounts.php'));

			$linkData = array
			(
				'menuaction'	=> 'emailadmin.uiuserdata.saveUserData',
				'account_id'	=> $accountID
			);
			$this->t->set_var("form_action", $GLOBALS['phpgw']->link('/index.php',$linkData));

			$this->t->set_var('url_image_add',$GLOBALS['phpgw']->common->image('phpgwapi','new'));
			$this->t->set_var('url_image_edit',$GLOBALS['phpgw']->common->image('phpgwapi','edit'));
			$this->t->set_var('url_image_delete',$GLOBALS['phpgw']->common->image('phpgwapi','delete'));
			
			// only when we show a existing user
			if($userData = $this->boemailadmin->getUserData($accountID)) {
				$addresses = array();
				foreach((array)$userData['mailAlternateAddress'] as $data) {
					$addresses[$data] = $data;
				}
				$this->t->set_var('selectbox_mailAlternateAddress', html::select(
					'mailAlternateAddress',
					'',
					$addresses, 
					true, 
					"style='width: 100%;' id='mailAlternateAddress'",
					5)
				);
			
				$addresses = array();
				foreach((array)$userData['mailForwardingAddress'] as $data) {
					$addresses[$data] = $data;
				}
				$this->t->set_var('selectbox_mailRoutingAddress', html::select(
					'mailForwardingAddress',
					'',
					$addresses, 
					true, 
					"style='width: 100%;' id='mailRoutingAddress'",
					5)
				);
				
				$this->t->set_var("quotaLimit",$userData["quotaLimit"]);
			
				$this->t->set_var("mailLocalAddress",$userData["mailLocalAddress"]);
				$this->t->set_var("mailAlternateAddress",'');
				$this->t->set_var("mailRoutingAddress",'');
				$this->t->set_var("selected_".$userData["qmailDotMode"],'selected');
				$this->t->set_var("deliveryProgramPath",$userData["deliveryProgramPath"]);
				
				$this->t->set_var("uid",rawurlencode($_accountData["dn"]));
				if ($userData["accountStatus"] == "active")
					$this->t->set_var("account_checked","checked");
				if ($userData["deliveryMode"] == "forwardOnly")
					$this->t->set_var("forwardOnly_checked","checked");
				if ($_accountData["deliverExtern"] == "active")
					$this->t->set_var("deliver_checked","checked");
			} else {
				$this->t->set_var("mailLocalAddress",'');
				$this->t->set_var("mailAlternateAddress",'');
				$this->t->set_var("mailRoutingAddress",'');
				$this->t->set_var("options_mailAlternateAddress",lang('no alternate email address'));
				$this->t->set_var("options_mailRoutingAddress",lang('no forwarding email address'));
				$this->t->set_var("account_checked",'');
				$this->t->set_var("forwardOnly_checked",'');

				$this->t->set_var('selectbox_mailAlternateAddress', html::select(
					'mailAlternateAddress',
					'',
					array(), 
					true, 
					"style='width: 100%;' id='mailAlternateAddress'",
					5)
				);
			
				$this->t->set_var('selectbox_mailRoutingAddress', html::select(
					'mailForwardingAddress',
					'',
					array(), 
					true, 
					"style='width: 100%;' id='mailRoutingAddress'",
					5)
				);
				
				$this->t->set_var('quotaLimit','');
			}
		
			// create the menu on the left, if needed		
			$menuClass =& CreateObject('admin.uimenuclass');
			$this->t->set_var('rows',$menuClass->createHTMLCode('edit_user'));

			$this->t->pparse("out","form");

		}
		
		function saveUserData()
		{
			if($_POST["accountStatus"] == "on") {
				$accountStatus = "active";
			}
			
			if($_POST["forwardOnly"] == "on") {
				$deliveryMode = "forwardOnly";
			}

			$formData = array (
				'mailLocalAddress'		=> $_POST["mailLocalAddress"],
				'mailAlternateAddress'		=> $_POST["mailAlternateAddress"],
				'mailForwardingAddress'		=> $_POST["mailForwardingAddress"],
				'quotaLimit'			=> $_POST["quotaLimit"],
				'qmailDotMode'			=> $_POST["qmailDotMode"],
				'deliveryProgramPath'		=> $_POST["deliveryProgramPath"],
				'accountStatus'			=> $accountStatus, 
				'deliveryMode'			=> $deliveryMode
			);

			$this->boemailadmin->saveUserData($_GET['account_id'], $formData);

			// read date fresh from ldap storage
			$this->editUserData();
		}
		
		function translate()
		{
			$this->t->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);

			$this->t->set_var('lang_add',lang('add'));
			$this->t->set_var('lang_done',lang('Done'));
			$this->t->set_var('lang_remove',lang('remove'));
			$this->t->set_var('lang_remove',lang('remove'));
			$this->t->set_var('lang_advanced_options',lang('advanced options'));
			$this->t->set_var('lang_qmaildotmode',lang('qmaildotmode'));
			$this->t->set_var('lang_default',lang('default'));
			$this->t->set_var('lang_quota_settings',lang('quota settings'));
			$this->t->set_var('lang_qoutainmbyte',lang('qouta size in MByte'));
			$this->t->set_var('lang_inmbyte',lang('in MByte'));
			$this->t->set_var('lang_0forunlimited',lang('leave empty for no quota'));
			$this->t->set_var('lang_forward_only',lang('forward only'));
			$this->t->set_var('lang_enter_new_address',lang('Add new email address:'));
			$this->t->set_var('lang_update_current_address',lang('Update current email address:'));
		}
	}
