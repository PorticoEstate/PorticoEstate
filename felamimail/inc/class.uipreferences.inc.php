<?php
	/***************************************************************************\
	* phpGroupWare - FeLaMiMail                                                 *
	* http://www.linux-at-work.de                                               *
	* http://www.phpgw.de                                                       *
	* http://www.phpgroupware.org                                               *
	* Written by : Lars Kneschke [lkneschke@linux-at-work.de]                   *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/
	/* $Id$ */

	class uipreferences
	{

		var $public_functions = array
		(
			'listFolder'	=> 'True',
			'showHeader'	=> 'True',
			'getAttachment'	=> 'True'
		);

		function uipreferences()
		{
			$this->t 		= $GLOBALS['phpgw']->template;
			$this->bofelamimail	= CreateObject('felamimail.bofelamimail');
			$this->bofelamimail->openConnection('',OP_HALFOPEN);
			
			
			$this->rowColor[0] = 'row_on';
			$this->rowColor[1] = 'row_off';

		}
		
		function display_app_header()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}
		
		function listFolder()
		{
			// check user input BEGIN
		
			// the name of the new current folder
			if(isset($GLOBALS['HTTP_POST_VARS']['foldername']))
			{
				$this->bofelamimail->sessionData['preferences']['mailbox']
					= $GLOBALS['HTTP_POST_VARS']['foldername'];
				$this->bofelamimail->saveSessionData();
			}

			$this->selectedFolder	= isset($this->bofelamimail->sessionData['preferences']['mailbox']) ? $this->bofelamimail->sessionData['preferences']['mailbox']:'';
			
			// (un)subscribe to a folder??
			if(isset($GLOBALS['HTTP_POST_VARS']['folderStatus']))
			{
				$this->bofelamimail->subscribe($this->selectedFolder,$GLOBALS['HTTP_POST_VARS']['folderStatus']);
			}
			
			// rename a mailbox
			if(isset($GLOBALS['HTTP_POST_VARS']['newMailboxName']))
			{
				#print "rename to: ".$GLOBALS['HTTP_POST_VARS']['newMailboxName'];
				
				$oldMailboxName = $this->selectedFolder;
				$newMailboxName = $GLOBALS['HTTP_POST_VARS']['newMailboxName'];

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
		
			// create a new Mailbox
			if(isset($GLOBALS['HTTP_POST_VARS']['newSubFolder']))
			{
				$oldMailboxName = $this->bofelamimail->sessionData['preferences']['mailbox'];
				$newMailboxName = $oldMailboxName.".".$GLOBALS['HTTP_POST_VARS']['newSubFolder'];
				
				$this->bofelamimail->imap_createmailbox($newMailboxName,True);
			}
			
			// delete a Folder
			if(isset($GLOBALS['HTTP_POST_VARS']['deleteFolder']))
			{
				if($this->bofelamimail->imap_deletemailbox($this->bofelamimail->sessionData['preferences']['mailbox']))
				{
					$this->bofelamimail->sessionData['preferences']['mailbox']
						= "INBOX";
					$this->bofelamimail->saveSessionData();
				}
			}

			$this->selectedFolder	= isset($this->bofelamimail->sessionData['preferences']['mailbox'])?$this->bofelamimail->sessionData['preferences']['mailbox']:'';

			// check user input END
			
			
			$folderList	= $this->bofelamimail->getFolderList();
			$folderStatus	= $this->bofelamimail->getFolderStatus($this->selectedFolder);
			#$quota		= $this->bofelamimail->imap_get_quotaroot($this->selectedFolder);
			$mailPrefs	= $this->bofelamimail->getMailPreferences();
			
			$this->display_app_header();

			$this->t->set_root(PHPGW_APP_TPL);
			$this->t->set_file(array("body" => "preferences_manage_folder.tpl"));
			$this->t->set_block('body','main');
			$this->t->set_block('body','select_row');

			$this->translate();
			
			#print "<pre>";print_r($folderList);print "</pre>";
			// set the default values for the sort links (sort by subject)
			$linkData = array
			(
				'menuaction'    => 'felamimail.uipreferences.listFolder'
			);
			$this->t->set_var('form_action',$GLOBALS['phpgw']->link('/index.php',$linkData));
			
			// folder select box
			while(list($key,$value) = @each($folderList))
			{
				$currentFolderStatus = $this->bofelamimail->getFolderStatus($key);
				$this->t->set_var('folder_name',$value);
				$this->t->set_var('folder_value',$key);
				if($this->selectedFolder == $key)
				{
					$this->t->set_var('selected','selected');
				}
				else
				{
					$this->t->set_var('selected','');
				}
				if($currentFolderStatus['subscribed'])
				{
					$this->t->set_var('subscribed','S');
				}
				else
				{
					$this->t->set_var('subscribed','U');
				}
				$this->t->parse('select_rows','select_row',True);
			}
			
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
			
			if(isset($quota) && is_array($quota))
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
			$this->t->set_var('imap_server',$mailPrefs['imapServerAddress']);
			
			$this->t->pparse("out","main");			
			$this->bofelamimail->closeConnection();
		}
		
		function translate()
		{
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
			$this->t->set_var("lang_delete",lang('delete'));
			$this->t->set_var("lang_imap_server",lang('IMAP Server'));
			$this->t->set_var("lang_folder_settings",lang('folder settings'));
			
			$this->t->set_var("bg01",$GLOBALS['phpgw_info']["theme"]["bg01"]);
			$this->t->set_var("bg02",$GLOBALS['phpgw_info']["theme"]["bg02"]);
			$this->t->set_var("bg03",$GLOBALS['phpgw_info']["theme"]["bg03"]);
		}
	}

?>
