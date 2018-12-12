<?php
	/***************************************************************************\
	* eGroupWare - FeLaMiMail                                                   *
	* http://www.linux-at-work.de                                               *
	* http://www.phpgw.de                                                       *
	* http://www.egroupware.org                                                 *
	* Written by : Lars Kneschke [lkneschke@linux-at-work.de]                   *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/

	/* $Id$ */

	phpgw::import_class('felamimail.html');	
	require_once(PHPGW_INCLUDE_ROOT.'/felamimail/inc/class.felamimail_bosignatures.inc.php');

	class felamimail_uipreferences
	{

		var $public_functions = array
		(
			'addACL'		=> 'True',
			'editAccountData'	=> 'True',
			'editForwardingAddress'	=> 'True',
			'editSignature'		=> 'True',
			'listFolder'		=> 'True',
			'listSignatures'	=> 'True',
			'listAccountData'	=> 'True',
			'showHeader'		=> 'True',
			'getAttachment'		=> 'True'
		);

		function __construct()
		{
			require_once(PHPGW_SERVER_ROOT.'/felamimail/inc/xajax.inc.php');

			$xajax = new xajax($GLOBALS['phpgw']->link('/felamimail/xajax.php',false,true), 'xajax_', 'utf-8');
			$xajax->waitCursorOff();
			$xajax->registerFunction("doXMLHTTP");

			$GLOBALS['phpgw_info']['flags']['java_script'] .= $xajax->getJavascript($GLOBALS['phpgw_info']['server']['webserver_url'] . '/felamimail/js/');

			$GLOBALS['phpgw']->js->validate_file('jsapi', 'jsapi', 'felamimail');

			$this->t = & $GLOBALS['phpgw']->template;
			$this->charset = 'utf-8';

			$this->bofelamimail	= CreateObject('felamimail.bofelamimail',$this->charset);
			$this->bopreferences	= CreateObject('felamimail.bopreferences');
			$this->uiwidgets	= CreateObject('felamimail.uiwidgets');
			$this->bofelamimail->openConnection();
			
			
			$this->rowColor[0] = $GLOBALS['phpgw_info']["theme"]["bg01"];
			$this->rowColor[1] = $GLOBALS['phpgw_info']["theme"]["bg02"];
			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			$GLOBALS['phpgw']->css->validate_file('app', 'felamimail');
		}
		
		function addACL()
		{
			$this->display_app_header(FALSE);

			$this->t->set_root(PHPGW_APP_TPL);
			$this->t->set_file(array("body" => "preferences_manage_folder.tpl"));
			$this->t->set_block('body','main');
			$this->t->set_block('body','add_acl');

			$this->translate();

			$this->t->pparse("out","add_acl");			

		}
		
		// $_displayNavbar false == don't display navbar
		function display_app_header($_displayNavbar)
		{
			switch($_GET['menuaction'])
			{
				case 'felamimail.uipreferences.editSignature':
					$GLOBALS['phpgw']->js->validate_file('jscode','listSignatures','felamimail');
					#$GLOBALS['phpgw']->js->set_onload('fm_initEditLayout();');
					break;
				case 'felamimail.uipreferences.listAccountData':
				case 'felamimail.uipreferences.editAccountData':
					$GLOBALS['phpgw']->js->validate_file('jscode','editAccountData','felamimail');
					$GLOBALS['phpgw']->js->set_onload('javascript:initEditAccountData();');
					break;

				case 'felamimail.uipreferences.listSignatures':
					$GLOBALS['phpgw']->js->validate_file('jscode','listSignatures','felamimail');
					#$GLOBALS['phpgw']->js->set_onload('javascript:initEditAccountData();');
					break;

				case 'felamimail.uipreferences.listFolder':
				case 'felamimail.uipreferences.addACL':
					$GLOBALS['phpgw']->js->validate_file('tabs','tabs','felamimail');
					$GLOBALS['phpgw']->js->validate_file('dhtmlxtree','js/dhtmlXCommon','felamimail');
					$GLOBALS['phpgw']->js->validate_file('dhtmlxtree','js/dhtmlXTree','felamimail');
					$GLOBALS['phpgw']->js->validate_file('jscode','listFolder','felamimail');
					$GLOBALS['phpgw']->js->set_onload('javascript:initAll();');
					break;
			}
			
			$GLOBALS['phpgw_info']['flags']['include_xajax'] = True;
						
			$GLOBALS['phpgw']->common->phpgw_header();
			if($_displayNavbar == TRUE)
				echo parse_navbar();
		}
		
		function editForwardingAddress()
		{
			$bofelamimail	= CreateObject('felamimail.bofelamimail','utf-8');
			$mailPrefs	= $bofelamimail->getMailPreferences();
			$ogServer	= $mailPrefs->getOutgoingServer(0);
			
			if(!is_a($ogServer, 'defaultsmtp') || !$ogServer->editForwardingAddress) {
				die('You should not be here!');
			}
			
			if($_POST['save']) {
				//_debug_array($_POST);_debug_array($_POST);_debug_array($_POST);
				$ogServer->saveSMTPForwarding($GLOBALS['phpgw_info']['user']['account_id'],$_POST['forwardingAddress'],$_POST['keepLocalCopy']);
			} elseif($_POST['cancel']) {
				ExecMethod('felamimail.uifelamimail.viewMainScreen');
				return;
			}
			
			$userData = $ogServer->getUserData($GLOBALS['phpgw_info']['user']['account_id']);

			$this->display_app_header(TRUE);

			$this->t->set_root(PHPGW_APP_TPL);
			$this->t->set_file(array("body" => "edit_forwarding_address.tpl"));
			$this->t->set_block('body','main');

			$this->translate();

			$linkData = array (
				'menuaction'    => 'felamimail.uipreferences.editForwardingAddress'
			);
			$this->t->set_var('form_action',$GLOBALS['phpgw']->link('/index.php',$linkData));
			$this->t->set_var('forwarding_address',$userData['mailForwardingAddress'][0]);
			
			#deliveryMode checked_keep_local_copy
			if($userData['deliveryMode'] != 'forwardOnly') {
				$this->t->set_var('checked_keep_local_copy','checked');
			}

			$this->t->parse("out","main");
			
			print $this->t->get('out','main');
		}
		
		function editSignature() {
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			if(isset($_GET['signatureID'])) {
				$signatureID = (int)$_GET['signatureID'];
		
				$boSignatures = new felamimail_bosignatures();
				$signatureData = $boSignatures->getSignature($signatureID);
			}
			
			$this->display_app_header(false);
			
			$this->t->set_root(PHPGW_APP_TPL);
			$this->t->set_file(array('body' => 'preferences_edit_signature.tpl'));
			$this->t->set_block('body','main');

			$this->translate();

			$linkData = array (
				'menuaction'    => 'felamimail.uipreferences.editSignature'
			);
			$this->t->set_var('form_action', $GLOBALS['phpgw']->link('/index.php',$linkData));

			if(isset($_GET['signatureID'])) {

				$this->t->set_var('description', @htmlspecialchars($signatureData->fm_description, ENT_QUOTES, $this->charset));
			
				$this->t->set_var('signatureID', $signatureID);

				$this->t->set_var('tinymce',html::fckEditorQuick(
					'signature', 'simple', 
					$signatureData->fm_signature, 
					'150px')
				);

				$this->t->set_var('checkbox_isDefaultSignature',html::checkbox(
					'isDefaultSignature',
					$signatureData->fm_defaultsignature,
					'true',
					'id="isDefaultSignature"'
					)
				);
			} else {
				$this->t->set_var('tinymce',html::fckEditorQuick('signature', 'simple', '', '150px'));

				$this->t->set_var('checkbox_isDefaultSignature',html::checkbox(
					'isDefaultSignature', false, 'true', 'id="isDefaultSignature"'
				));

			}

			$this->t->parse("out","main");
			print $this->t->get('out','main');
		}
		
		function editAccountData()
		{
			$boPreferences	= CreateObject('felamimail.bopreferences');
			$preferences = $boPreferences->getPreferences();
			
			if(!($preferences->userDefinedAccounts || $preferences->userDefinedIdentities)) {
				die('you are not allowed to be here');
			}

			if($_POST['save'] || $_POST['apply']) {
				// IMAP connection settings
				$icServer = CreateObject('emailadmin.defaultimap');
				if(is_array($_POST['ic'])) {
					foreach($_POST['ic'] as $key => $value) {
						switch($key) {
							case 'validatecert':
								$icServer->$key = ($value != 'dontvalidate');
								break;
								
							case 'enableSieve':
								$icServer->$key = ($value == 'enableSieve');
								break;
							
							default:
								$icServer->$key = $value;
								break;
						}
					}
				} else {
					$icServer = NULL;
				}
				
				// SMTP connection settings
				$ogServer = CreateObject('emailadmin.defaultsmtp');
				if(is_array($_POST['og'])) {
					foreach($_POST['og'] as $key => $value) {
						$ogServer->$key = $value;
					}
				} else {
					$ogServer = NULL;
				}

				// identity settings
				$identity = CreateObject('emailadmin.ea_identity');
				if(is_array($_POST['identity'])) {
					foreach($_POST['identity'] as $key => $value) {
						$identity->$key = $value;
					}
				}
				

				$newID = $boPreferences->saveAccountData($icServer, $ogServer, $identity);
				if ($identity->id == 'new') $identity->id = $newID;
				if((int)$_POST['active']) {
					#$boPreferences->saveAccountData($icServer, $ogServer, $identity);
					$boPreferences->setProfileActive(false);
					$boPreferences->setProfileActive(true,$identity->id);
				} else {
					$boPreferences->setProfileActive(false,$identity->id);
				}
				
				if($_POST['save']) {
					ExecMethod('felamimail.uifelamimail.viewMainScreen');
					return;
				}
			} elseif($_POST['cancel']) {
				ExecMethod('felamimail.uifelamimail.viewMainScreen');
				return;
			}
			$this->display_app_header(TRUE);
			
			$this->t->set_root(PHPGW_APP_TPL);
			$this->t->set_file(array("body" => "edit_account_data.tpl"));
			$this->t->set_block('body','main');

			$this->translate();

			// if there is no accountID with the call of the edit method, retrieve an active account
			$account2retrieve = 'active';
			if ((int)$_GET['accountID']) {
				$account2retrieve = $_GET['accountID'];
			}
			if ($_GET['accountID'] == 'new') $account2retrieve = 'new';
			if (!empty($newID) && $newID>0) $account2retrieve = $newID;
			if ($account2retrieve != 'new') $accountData	= $boPreferences->getAccountData($preferences, $account2retrieve);
			$icServer =& $accountData['icServer'];
			$ogServer =& $accountData['ogServer'];
			$identity =& $accountData['identity'];
			#_debug_array($identity);
			if ($icServer) {
				foreach($icServer as $key => $value) {
					if(is_object($value) || is_array($value)) {
						continue;
					}
					switch($key) {
						case 'encryption':
							$this->t->set_var('checked_ic_'. $key .'_'. $value, 'checked');
							break;
						
						case 'enableSieve':
							$this->t->set_var('checked_ic_'.$key,($value ? 'checked' : ''));
							break;

						case 'validatecert':
							$this->t->set_var('checked_ic_'.$key,($value ? '' : 'checked'));
							break;
						
						default:
							$this->t->set_var("ic[$key]", $value);
							break;
					}
				}
			}
			if ($ogServer) {
				foreach($ogServer as $key => $value) {
					if(is_object($value) || is_array($value)) {
						continue;
					}
					#print "$key => $value<bR>";
					switch($key) {
						case 'smtpAuth':
							$this->t->set_var('checked_og_'.$key,($value ? 'checked' : ''));
						default:
							$this->t->set_var("og[$key]", $value);
					}
				}
			}
			$felamimail_bosignatures = new felamimail_bosignatures();
			$signatures = $felamimail_bosignatures->getListOfSignatures();
			$allSignatures = array(
				'-2' => lang('no signature')
			);
			foreach ($signatures as $sigkey => $sig) {
				$allSignatures[$sig['fm_signatureid']] = $sig['fm_description'];
			}
			$sigvalue = -2;
			if ($identity) {
				foreach($identity as $key => $value) {
					if(is_object($value) || is_array($value)) {
						continue;
					}
					switch($key) {
						case 'signature':
							$sigvalue = $value;
							break;
						default:
							$this->t->set_var("identity[$key]", $value);
					}
				}
 				$this->t->set_var('accountID',$identity->id); 
				$this->t->set_var('checked_active',($accountData['active'] ? ($preferences->userDefinedAccounts ? 'checked' : '') : ''));
			} else {
				if ($signatureData = $felamimail_bosignatures->getDefaultSignature()) {
					if (is_array($signatureData)) {
						$sigvalue = $signatureData['signatureid'];
					} else {
						$sigvalue =$signatureData;
					}
				}
				$this->t->set_var('accountID','new');
			}
			$this->t->set_var('allowAccounts',($preferences->userDefinedAccounts ? 1 : 0));
			$this->t->set_var('identity_selectbox', html::select('identity[signature]',$sigvalue,$allSignatures, true, "style='width: 250px;'"));
			
			$linkData = array
			(
				'menuaction'    => 'felamimail.uipreferences.editAccountData'
			);
			$this->t->set_var('form_action',$GLOBALS['phpgw']->link('/index.php',$linkData));

			$this->t->parse("out","main");
			print $this->t->get('out','main');
		}
		
		function listFolder()
		{
			// rename a mailbox
			if(isset($_POST['newMailboxName']))
			{
				$oldMailboxName = $this->bofelamimail->sessionData['preferences']['mailbox'];
				$newMailboxName = $_POST['newMailboxName'];
				
				if($position = strrpos($oldMailboxName,'.'))
				{
					$newMailboxName		= substr($oldMailboxName,0,$position+1).$newMailboxName;
				}
			
				
				if($this->bofelamimail->imap_renamemailbox($oldMailboxName, $newMailboxName))
				{
					$this->bofelamimail->sessionData['preferences']['mailbox']
						= $newMailboxName;
					$this->bofelamimail->saveSessionData();
				}
			}
			
			// delete a Folder
			if(isset($_POST['deleteFolder']) && $this->bofelamimail->sessionData['preferences']['mailbox'] != 'INBOX')
			{
				if($this->bofelamimail->imap_deletemailbox($this->bofelamimail->sessionData['preferences']['mailbox']))
				{
					$this->bofelamimail->sessionData['preferences']['mailbox']
						= "INBOX";
					$this->bofelamimail->saveSessionData();
				}
			}

			// create a new Mailbox
			if(isset($_POST['newSubFolder']))
			{
				$oldMailboxName = $this->bofelamimail->sessionData['preferences']['mailbox'].'.';
				$oldMailboxName	= ($oldMailboxName == '--topfolderselected--.') ? '' : $oldMailboxName;
				$newMailboxName = $oldMailboxName.$_POST['newSubFolder'];

				$this->bofelamimail->imap_createmailbox($newMailboxName,True);
			}
			
			$folderList	= $this->bofelamimail->getFolderObjects();
			// check user input BEGIN
			// the name of the new current folder
			if(get_var('mailboxName',array('POST')) && $folderList[get_var('mailboxName',array('POST'))] ||
			get_var('mailboxName',array('POST')) == '--topfolderselected--')
			{
				$this->bofelamimail->sessionData['preferences']['mailbox']
					= get_var('mailboxName',array('POST'));
				$this->bofelamimail->saveSessionData();
			}

			$this->selectedFolder	= $this->bofelamimail->sessionData['preferences']['mailbox'];
			
			// (un)subscribe to a folder??
			if(isset($_POST['folderStatus']))
			{
				$this->bofelamimail->subscribe($this->selectedFolder,$_POST['folderStatus']);
			}
			

			$this->selectedFolder	= $this->bofelamimail->sessionData['preferences']['mailbox'];

			// check user input END
			
			
			if($this->selectedFolder != '--topfolderselected--')
				$folderStatus	= $this->bofelamimail->getFolderStatus($this->selectedFolder);
			$mailPrefs	= $this->bofelamimail->getMailPreferences();
			
			$this->display_app_header(TRUE);

			$this->t->set_root(PHPGW_APP_TPL);
			$this->t->set_file(array("body" => "preferences_manage_folder.tpl"));
			$this->t->set_block('body','main');
			#$this->t->set_block('body','select_row');
			$this->t->set_block('body','folder_settings');
			$this->t->set_block('body','mainFolder_settings');
			#$this->t->set_block('body','folder_acl');

			$this->translate();
			
			#print "<pre>";print_r($folderList);print "</pre>";
			// set the default values for the sort links (sort by subject)
			$linkData = array
			(
				'menuaction'    => 'felamimail.uipreferences.listFolder'
			);
			$this->t->set_var('form_action',$GLOBALS['phpgw']->link('/index.php',$linkData));

			$linkData = array
			(
				'menuaction'    => 'felamimail.uipreferences.addACL'
			);
			$this->t->set_var('url_addACL',$GLOBALS['phpgw']->link('/index.php',$linkData));
			
			// create the link to show folder settings
			#$linkData = array
			#(
			#	'menuaction'    => 'felamimail.uipreferences.listFolder',
			#	'display'	=> 'settings'
			#);
			#$this->t->set_var('settings_url',$GLOBALS['phpgw']->link('/index.php',$linkData));
			
			// create the link to show folder acl
			#$linkData = array
			#(
			#	'menuaction'    => 'felamimail.uipreferences.listFolder',
			#	'display'	=> 'acl'
			#);
			#$this->t->set_var('acl_url',$GLOBALS['phpgw']->link('/index.php',$linkData));
			
			// folder select box
			$icServer = $mailPrefs->getIncomingServer(0);
			$folderTree = $this->uiwidgets->createHTMLFolder
			(
				$folderList, 
				$this->selectedFolder, 
				0,
				lang('IMAP Server'), 
				$icServer->username.'@'.$icServer->host,
				'divFolderTree',
				TRUE
			);
			$this->t->set_var('folder_tree',$folderTree);
			
			switch($_GET['display'])
			{
				#case 'acl':
				#	$uiBaseClass = CreateObject('felamimail.uibaseclass');
				#	#$uiBaseClass->accounts_popup('calendar');
				#	$this->t->parse('settings_view','folder_acl',True);
				#	break;
					
				case 'settings':
				default:
					// selected folder data
					if($folderStatus['subscribed'])
					{
						$this->t->set_var('subscribed_checked','checked');
						$this->t->set_var('unsubscribed_checked','');
					}
					else
					{
						$this->t->set_var('subscribed_checked','');
						$this->t->set_var('unsubscribed_checked','checked');
					}
			
					if(is_array($quota))
					{
						$this->t->set_var('storage_usage',$quota['STORAGE']['usage']);
						$this->t->set_var('storage_limit',$quota['STORAGE']['limit']);
						$this->t->set_var('message_usage',$quota['MESSAGE']['usage']);
						$this->t->set_var('message_limit',$quota['MESSAGE']['limit']);
					}
					else
					{
						$this->t->set_var('storage_usage',lang('unknown'));
						$this->t->set_var('storage_limit',lang('unknown'));
						$this->t->set_var('message_usage',lang('unknown'));
						$this->t->set_var('message_limit',lang('unknown'));
					}
			
					if($this->selectedFolder != '--topfolderselected--')
					{
						$this->t->parse('settings_view','folder_settings',True);
					}
					else
					{
						$this->t->parse('settings_view','mainFolder_settings',True);
					}
					
					break;
			}
			
			$mailBoxTreeName 	= '';
			$mailBoxName		= $this->selectedFolder;
			if($position = strrpos($this->selectedFolder,'.'))
			{
				$mailBoxTreeName 	= substr($this->selectedFolder,0,$position+1);
				$mailBoxName		= substr($this->selectedFolder,$position+1);
			}
			
			$this->t->set_var('mailboxTreeName',$mailBoxTreeName);
			$this->t->set_var('mailboxNameShort',$mailBoxName);
			$this->t->set_var('mailboxName',$mailBoxName);			
			$this->t->set_var('folderName',$this->selectedFolder);
			$this->t->set_var('imap_server',$icServer->host);
			
			$this->t->pparse("out","main");			
			$this->bofelamimail->closeConnection();
		}
		
		function listSignatures()
		{
			$this->display_app_header(TRUE);

			$this->t->set_root(PHPGW_APP_TPL);
			$this->t->set_file(array("body" => "preferences_list_signatures.tpl"));
			$this->t->set_block('body','main');

			$this->translate();
			
			#print "<pre>";print_r($folderList);print "</pre>";
			// set the default values for the sort links (sort by subject)
			$linkData = array
			(
				'menuaction'    => 'felamimail.uipreferences.listFolder'
			);
			$this->t->set_var('form_action', $GLOBALS['phpgw']->link('/index.php',$linkData));

			$linkData = array
			(
				'menuaction'    => 'felamimail.uipreferences.editSignature'
			);
			$this->t->set_var('url_addSignature', $GLOBALS['phpgw']->link('/index.php',$linkData));
			
			$this->t->set_var('url_image_add',$GLOBALS['phpgw']->common->image('phpgwapi','new'));
			$this->t->set_var('url_image_delete',$GLOBALS['phpgw']->common->image('phpgwapi','delete'));
			
			$felamimail_bosignatures = new felamimail_bosignatures();
			$signatures = $felamimail_bosignatures->getListOfSignatures();

			$this->t->set_var('table', $this->uiwidgets->createSignatureTable($signatures));
			
			$this->t->pparse("out","main");			
			$this->bofelamimail->closeConnection();
		}

		function listAccountData()
		{
			$this->display_app_header(TRUE);
			$boPreferences  = CreateObject('felamimail.bopreferences');
			$preferences =& $boPreferences->getPreferences();
			$allAccountData    = $boPreferences->getAllAccountData($preferences);
			if ($allAccountData) {
				foreach ($allAccountData as $tmpkey => $accountData)
				{
					$identity =& $accountData['identity'];

					#_debug_array($identity);
			
					foreach($identity as $key => $value) {
						if(is_object($value) || is_array($value)) {
							continue;
						}
						switch($key) {
							default:
								$tempvar[$key] = $value;
						}
					}
					$accountArray[]=$tempvar;
				}
			}
			$this->t->set_root(PHPGW_APP_TPL);
			$this->t->set_file(array("body" => "preferences_list_accounts.tpl"));
			$this->t->set_block('body','main');

			$this->translate();
			
			#print "<pre>";print_r($folderList);print "</pre>";
			// set the default values for the sort links (sort by subject)
			#$linkData = array
			#(
			#	'menuaction'    => 'felamimail.uipreferences.listFolder'
			#);
			#$this->t->set_var('form_action', $GLOBALS['phpgw']->link('/index.php',$linkData));

			$linkData = array
			(
				'menuaction'    => 'felamimail.uipreferences.editAccountData',
				'accountID'		=> 'new'
			);
			$this->t->set_var('url_addAccount', $GLOBALS['phpgw']->link('/index.php',$linkData));
			
			$this->t->set_var('url_image_add',$GLOBALS['phpgw']->common->image('phpgwapi','new'));
			$this->t->set_var('url_image_delete',$GLOBALS['phpgw']->common->image('phpgwapi','delete'));
			
			$this->t->set_var('table', $this->uiwidgets->createAccountDataTable($accountArray));
			
			$this->t->pparse("out","main");			
			$this->bofelamimail->closeConnection();
		}
		
		function translate()
		{
			$this->t->set_var('lang_signature',lang('Signatur'));
			$this->t->set_var("lang_folder_name",lang('folder name'));
			$this->t->set_var("lang_folder_list",lang('folderlist'));
			$this->t->set_var("lang_select",lang('select'));
			$this->t->set_var("lang_folder_status",lang('folder status'));
			$this->t->set_var("lang_subscribed",lang('subscribed'));
			$this->t->set_var("lang_unsubscribed",lang('unsubscribed'));
			$this->t->set_var("lang_subscribe",lang('subscribe'));
			$this->t->set_var("lang_unsubscribe",lang('unsubscribe'));
			$this->t->set_var("lang_update",lang('update'));
			$this->t->set_var("lang_rename_folder",lang('rename folder'));
			$this->t->set_var("lang_create_subfolder",lang('create subfolder'));
			$this->t->set_var("lang_delete_folder",lang('delete folder'));
			$this->t->set_var("lang_confirm_delete",addslashes(lang("Do you really want to delete the '%1' folder?",$this->bofelamimail->sessionData['preferences']['mailbox'])));
			$this->t->set_var("lang_really_delete_accountsettings",lang("Do you really want to delete the selected Accountsettings and the assosiated Identity."));
			$this->t->set_var("lang_delete",lang('delete'));
			$this->t->set_var("lang_imap_server",lang('IMAP Server'));
			$this->t->set_var("lang_folder_settings",lang('folder settings'));
			$this->t->set_var("lang_folder_acl",lang('folder acl'));
			$this->t->set_var("lang_anyone",lang('anyone'));
			$this->t->set_var("lang_reading",lang('reading'));
			$this->t->set_var("lang_writing",lang('writing'));
			$this->t->set_var("lang_posting",lang('posting'));
			$this->t->set_var("lang_none",lang('none'));
			$this->t->set_var("lang_rename",lang('rename'));
			$this->t->set_var("lang_create",lang('create'));
			$this->t->set_var('lang_open_all',lang("open all"));
			$this->t->set_var('lang_close_all',lang("close all"));
			$this->t->set_var('lang_add',lang("add"));
			$this->t->set_var('lang_delete_selected',lang("delete selected"));
			$this->t->set_var('lang_cancel',lang("close"));
			$this->t->set_var('lang_ACL',lang('ACL'));
			$this->t->set_var('lang_save',lang('save'));
			$this->t->set_var('lang_cancel',lang('cancel'));
			$this->t->set_var('lang_Overview',lang('Overview'));
			$this->t->set_var('lang_edit_forwarding_address',lang('edit email forwarding address'));
			$this->t->set_var('lang_forwarding_address',lang('email forwarding address'));
			$this->t->set_var('lang_keep_local_copy',lang('keep local copy of email'));
			$this->t->set_var('hostname_address',lang('hostname / address'));
			$this->t->set_var('lang_username',lang('username'));
			$this->t->set_var('lang_password',lang('password'));
			$this->t->set_var('lang_port',lang('port'));
			$this->t->set_var('lang_apply',lang('apply'));
			$this->t->set_var('lang_use_costum_settings',lang('use custom settings'));
			$this->t->set_var('lang_use_custom_ids',lang('use custom identities'));
			$this->t->set_var('lang_identity',lang('identity'));
			$this->t->set_var('lang_name',lang('name'));
			$this->t->set_var('lang_organization',lang('organization'));
			$this->t->set_var('lang_emailaddress',lang('emailaddress'));
			$this->t->set_var('lang_encrypted_connection',lang('encrypted connection'));
			$this->t->set_var('lang_do_not_validate_certificate',lang('do not validate certificate'));
			$this->t->set_var("lang_incoming_server",lang('incoming mail server(IMAP)'));
			$this->t->set_var("lang_outgoing_server",lang('outgoing mail server(SMTP)'));
			$this->t->set_var("auth_required",lang('authentication required'));
			$this->t->set_var('lang_add_acl',lang('add acl'));
			$this->t->set_var('lang_foldername',lang('foldername'));
			$this->t->set_var('lang_description',lang('description'));
			$this->t->set_var('lang_really_delete_signatures',lang('Do you really want to delete the selected signatures?'));
			$this->t->set_var('lang_no_encryption',lang('no encryption'));
			$this->t->set_var('lang_default_signature',lang('default signature'));
			$this->t->set_var('lang_server_supports_sieve',lang('server supports mailfilter(sieve)'));
			
			$this->t->set_var("th_bg",$GLOBALS['phpgw_info']["theme"]["th_bg"]);
			$this->t->set_var("bg01",$GLOBALS['phpgw_info']["theme"]["bg01"]);
			$this->t->set_var("bg02",$GLOBALS['phpgw_info']["theme"]["bg02"]);
			$this->t->set_var("bg03",$GLOBALS['phpgw_info']["theme"]["bg03"]);
		}
	}
