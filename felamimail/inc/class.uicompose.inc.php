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
	/* $Id: class.uicompose.inc.php 18315 2007-10-11 13:36:19Z skwashd $ */

	class uicompose
	{

		var $public_functions = array
		(
			'compose'	=> 'True',
			'reply'		=> 'True',
			'replyAll'	=> 'True',
			'forward'	=> 'True',
			'action'	=> 'True'
		);

		function uicompose()
		{
			if (!isset($_POST['composeid']) && !isset($_GET['composeid']))
			{
				// create new compose session
				$this->bocompose   = CreateObject('felamimail.bocompose');
				$this->composeID = $this->bocompose->getComposeID();
			}
			else
			{
				// reuse existing compose session
				if (isset($_POST['composeid']))
					$this->composeID = $_POST['composeid'];
				else
					$this->composeID = $_GET['composeid'];
				$this->bocompose   = CreateObject('felamimail.bocompose',$this->composeID);
			}			
			
			$this->t 		= CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
			$this->bofelamimail	= CreateObject('felamimail.bofelamimail');

			$this->t->set_unknowns('remove');
			
			$this->rowColor[0] = 'row_on';
			$this->rowColor[1] = 'row_off';

			if ( !isset($GLOBALS['phpgw']->richtext) || !is_object($GLOBALS['phpgw']->richtext) )
			{
				$GLOBALS['phpgw']->richtext =& createObject('phpgwapi.richtext');
			}
			$GLOBALS['phpgw']->richtext->replace_element('body');
			$GLOBALS['phpgw']->richtext->generate_script();
		}
		
		function unhtmlentities ($string)
		{
			$trans_tbl = get_html_translation_table (HTML_ENTITIES);
			$trans_tbl = array_flip ($trans_tbl);
			return strtr ($string, $trans_tbl);
		}

		function action()
		{
			$formData['to'] 	= $this->bocompose->stripSlashes($_POST['to']);
			$formData['cc'] 	= $this->bocompose->stripSlashes($_POST['cc']);
			$formData['bcc'] 	= $this->bocompose->stripSlashes($_POST['bcc']);
			$formData['reply_to'] 	= $this->bocompose->stripSlashes($_POST['reply_to']);
			$formData['subject'] 	= $this->bocompose->stripSlashes($_POST['subject']);
			$formData['body'] 	= $this->bocompose->stripSlashes($_POST['body']);
			$formData['priority'] 	= $this->bocompose->stripSlashes($_POST['priority']);
			$formData['signature'] 	= $this->bocompose->stripSlashes($_POST['signature']);
			$formData['mailbox']	= (isset($_GET['mailbox'])?$_GET['mailbox']:'');

			if (isset($_POST['send'])) 
			{
				$action="send";
			}
			elseif (isset($_POST['addfile'])) 
			{
				$action="addfile";
			}
			elseif (isset($_POST['removefile']))
			{
				$action="removefile";
			}
			
			switch ($action)
			{
				case "addfile":
					$formData['name']	= $GLOBALS['HTTP_POST_FILES']['attachfile']['name'];
					$formData['type']	= $GLOBALS['HTTP_POST_FILES']['attachfile']['type'];
					$formData['file']	= $GLOBALS['HTTP_POST_FILES']['attachfile']['tmp_name'];
					$formData['size']	= $GLOBALS['HTTP_POST_FILES']['attachfile']['size'];
					$this->bocompose->addAttachment($formData);
					$this->compose();
					break;

				case "removefile":
					$formData['removeAttachments']	= $_POST['attachment'];
					$this->bocompose->removeAttachment($formData);
					$this->compose();
					break;
					
				case "send":
					if(!$this->bocompose->send($formData))
					{
						$this->compose();
						return;
					}
					
					$linkData = array
					(
						'menuaction'=> 'felamimail.uifelamimail.index',
						'mailbox'	=> isset($_GET['mailbox']) ? $_GET['mailbox'] : '',
						'startMessage'	=> '1'
					);
					
					$GLOBALS['phpgw']->redirect_link('/index.php',$linkData);
					break;
			}
		}
		
		function compose($_focusElement="to")
		{
			// read the data from session
			// all values are empty for a new compose window
			$sessionData = $this->bocompose->getSessionData();

			// is the to address set already?
			if (!empty($_GET['send_to']))
			{
				$sessionData['to'] = stripslashes(urldecode($_GET['send_to']));
			}
			
			$this->display_app_header();
			
			$this->t->set_file(array("composeForm" => "composeForm.tpl"));
			$this->t->set_block('composeForm','header','header');
			$this->t->set_block('composeForm','body_input');
			$this->t->set_block('composeForm','attachment','attachment');
			$this->t->set_block('composeForm','attachment_row','attachment_row');
			$this->t->set_block('composeForm','attachment_row_bold');
			
			$this->translate();
			
			$this->t->set_var("link_addressbook",$GLOBALS['phpgw']->link('/felamimail/addressbook.php'));
			$this->t->set_var("focusElement",$_focusElement);

			$linkData = array
			(
				'menuaction'	=> 'felamimail.uifelamimail.index'
			);
			$this->t->set_var("link_message_list",$GLOBALS['phpgw']->link('/felamimail/index.php',$linkData));

			$linkData = array
			(
				'menuaction'	=> 'felamimail.uicompose.action',
				'composeid'	=> $this->composeID
			);
			$this->t->set_var("link_action",$GLOBALS['phpgw']->link('/index.php',$linkData));
			$this->t->set_var('folder_name',$this->bofelamimail->sessionData['mailbox']);

			// check for some error messages from last posting attempt
			if($errorInfo = $this->bocompose->getErrorInfo())
			{
				$this->t->set_var('errorInfo',"<font color=\"red\"><b>$errorInfo</b></font>");
			}
			else
			{
				$this->t->set_var('errorInfo','&nbsp;');
			}
			
			// header
			$this->t->set_var("from",htmlentities($this->bocompose->getUserName(),ENT_QUOTES));
			$this->t->set_var("to",htmlentities((isset($sessionData['to'])?$sessionData['to']:''),ENT_QUOTES,'UTF-8'));
			$this->t->set_var("cc",htmlentities((isset($sessionData['cc'])?$sessionData['cc']:''),ENT_QUOTES,'UTF-8'));
			$this->t->set_var("bcc",htmlentities((isset($sessionData['bcc'])?$sessionData['bcc']:''),ENT_QUOTES,'UTF-8'));
			$this->t->set_var("reply_to",htmlentities((isset($sessionData['reply_to'])?$sessionData['reply_to']:''),ENT_QUOTES,'UTF-8'));
			$this->t->set_var("subject",htmlentities((isset($sessionData['subject'])?$sessionData['subject']:''),ENT_QUOTES,'UTF-8'));
			$this->t->pparse("out","header");

			// body
			$this->t->set_var("body",(isset($sessionData['body'])?$sessionData['body']:''));
			$this->t->set_var("signature",(isset($sessionData['signature'])?$sessionData['signature']:''));
			$this->t->pparse("out","body_input");

			// attachments
			if (isset($sessionData['attachments']) && is_array($sessionData['attachments']) && count($sessionData['attachments']) > 0)
			{
				$this->t->set_var('row_color',$this->rowColor[0]);
				$this->t->set_var('name',lang('name'));
				$this->t->set_var('type',lang('type'));
				$this->t->set_var('size',lang('size'));
				$this->t->parse('attachment_rows','attachment_row_bold',True);
				while (list($key,$value) = each($sessionData['attachments']))
				{
					#print "$key : $value<br>";
					$this->t->set_var('row_color',$this->rowColor[($key+1)%2]);
					$this->t->set_var('name',$value['name']);
					$this->t->set_var('type',$value['type']);
					$this->t->set_var('size',$value['size']);
					$this->t->set_var('attachment_number',$key);
					$this->t->parse('attachment_rows','attachment_row',True);
				}
			}
			else
			{
				$this->t->set_var('attachment_rows','');
			}
			
			$this->t->pparse("out","attachment");
		}

		function display_app_header()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}
		
		function forward()
		{
			$replyID = $_GET['reply_id'];
			if (!empty($replyID))
			{
				// this fill the session data with the values from the original email
				$this->bocompose->getForwardData($replyID);
			}
			$this->compose();
		}

		function reply()
		{
			$replyID = $_GET['reply_id'];
			if (!empty($replyID))
			{
				// this fill the session data with the values from the original email
				$this->bocompose->getReplyData('single', $replyID);
			}
			$this->compose('body');
		}
		
		function replyAll()
		{
			$replyID = $_GET['reply_id'];
			if (!empty($replyID))
			{
				// this fill the session data with the values from the original email
				$this->bocompose->getReplyData('all', $replyID);
			}
			$this->compose('body');
		}
		
		function translate()
		{
			$this->t->set_var("lang_message_list",lang('Message List'));
			$this->t->set_var("lang_to",lang('to'));
			$this->t->set_var("lang_cc",lang('cc'));
			$this->t->set_var("lang_bcc",lang('bcc'));
			$this->t->set_var("lang_from",lang('from'));
			$this->t->set_var("lang_reply_to",lang('reply to'));
			$this->t->set_var("lang_subject",lang('subject'));
			$this->t->set_var("lang_addressbook",lang('addressbook'));
			$this->t->set_var("lang_search",lang('search'));
			$this->t->set_var("lang_send",lang('send'));
			$this->t->set_var("lang_back_to_folder",lang('back to folder'));
			$this->t->set_var("lang_attachments",lang('attachments'));
			$this->t->set_var("lang_add",lang('add'));
			$this->t->set_var("lang_remove",lang('remove'));
			$this->t->set_var("lang_priority",lang('priority'));
			$this->t->set_var("lang_normal",lang('normal'));
			$this->t->set_var("lang_high",lang('high'));
			$this->t->set_var("lang_low",lang('low'));
			$this->t->set_var("lang_signature",lang('signature'));
		}
}

?>
