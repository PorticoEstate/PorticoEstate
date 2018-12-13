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
	* Free Software Foundation; version 2 of the License.                       *
	\***************************************************************************/
	/* $Id$ */

	phpgw::import_class('felamimail.html');	

	class felamimail_uicompose
	{

		var $public_functions = array
		(
			'action'		=> True,
			'compose'		=> True,
			'composeFromDraft'	=> True,
			'getAttachment'		=> True,
			'fileSelector'		=> True,
			'forward'		=> True,
			'composeAsNew'         => True,
			'reply'			=> True,
			'replyAll'		=> True,
			'selectFolder'		=> True,
		);

		var $destinations = array(
			'to' 		=> 'to',
			'cc'		=> 'cc',
			'bcc'		=> 'bcc',
			'replyto'	=> 'replyto',
			'folder'	=> 'folder'
		);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			require_once(PHPGW_SERVER_ROOT.'/felamimail/inc/xajax.inc.php');

			$xajax = new xajax($GLOBALS['phpgw']->link('/felamimail/xajax.php',false,true), 'xajax_', 'utf-8');
			$xajax->waitCursorOff();
			$xajax->registerFunction("doXMLHTTP");

			$GLOBALS['phpgw_info']['flags']['java_script'] .= $xajax->getJavascript($GLOBALS['phpgw_info']['server']['webserver_url'] . '/felamimail/js/');

			$GLOBALS['phpgw']->js->validate_file('jsapi', 'jsapi', 'felamimail');
			$this->displayCharset	= 'utf-8';
			if (!isset($_POST['composeid']) && !isset($_GET['composeid']))
			{
				// create new compose session
				$this->bocompose   = CreateObject('felamimail.bocompose','',$this->displayCharset);
				$this->composeID = $this->bocompose->getComposeID();
			}
			else
			{
				// reuse existing compose session
				if (isset($_POST['composeid']))
					$this->composeID = $_POST['composeid'];
				else
					$this->composeID = $_GET['composeid'];
				$this->bocompose   = CreateObject('felamimail.bocompose',$this->composeID,$this->displayCharset);
			}
			$this->t 		= CreateObject('phpgwapi.template',PHPGW_APP_TPL);
			$this->bofelamimail	= CreateObject('felamimail.bofelamimail',$this->displayCharset);
			$this->mailPreferences  = ExecMethod('felamimail.bopreferences.getPreferences');

			$this->t->set_unknowns('remove');

			$this->rowColor[0] = $GLOBALS['phpgw_info']["theme"]["bg01"];
			$this->rowColor[1] = $GLOBALS['phpgw_info']["theme"]["bg02"];

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			$GLOBALS['phpgw']->css->validate_file('app', 'felamimail');

		}

		function unhtmlentities ($string)
		{
			$trans_tbl = get_html_translation_table (HTML_ENTITIES);
			$trans_tbl = array_flip ($trans_tbl);
			return strtr ($string, $trans_tbl);
		}

		function action()
		{
			$formData['identity']	= (int)$_POST['identity'];

			foreach($_POST['destination'] as $key => $destination) {
				if(!empty($_POST['address'][$key])) {
					if($destination == 'folder') {
						$formData[$destination][] = $GLOBALS['phpgw']->translation->convert($_POST['address'][$key], $this->charset, 'UTF7-IMAP');
					} else {
						$formData[$destination][] = $_POST['address'][$key];
					}
				}
			}

			$formData['subject'] 	= $this->bocompose->stripSlashes($_POST['subject']);
			$formData['body'] 	= $this->bocompose->stripSlashes($_POST['body']);
			// if the body is empty, maybe someone pasted something with scripts, into the message body
			if(empty($formData['body']))
			{
				// this is to be found with the egw_unset_vars array for the _POST['body'] array
				$name='_POST';
				$key='body';
				#error_log($GLOBALS['egw_unset_vars'][$name.'['.$key.']']);
				if (isset($GLOBALS['egw_unset_vars'][$name.'['.$key.']']))
				{
					$formData['body'] = bocompose::_getCleanHTML( $GLOBALS['egw_unset_vars'][$name.'['.$key.']']);
				}
			}
			$formData['priority'] 	= $this->bocompose->stripSlashes($_POST['priority']);
			$formData['signatureID'] = (int)$_POST['signatureID'];
			$formData['mimeType']	= $this->bocompose->stripSlashes($_POST['mimeType']);
			$formData['disposition'] = (bool)$_POST['disposition'];
			$formData['to_infolog'] = $_POST['to_infolog'];
			//$formData['mailbox']	= $_GET['mailbox'];
			if((bool)$_POST['printit'] == true) {
				$formData['printit'] = 1; 
				$formData['isDraft'] = 1;
				// pint the composed message. therefore save it as draft and reopen it as plain printwindow
				$formData['subject'] = "[".lang('printview').":]".$formData['subject'];
				$messageUid = $this->bocompose->saveAsDraft($formData);
				if (!$messageUid) {
					 print "<script type=\"text/javascript\">alert('".lang("Error: Could not save Message as Draft")."');</script>";
					return;
				} 
				$uidisplay   = CreateObject('felamimail.uidisplay');
				$uidisplay->printMessage($messageUid, $formData['printit']);
				//$GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'felamimail.uidisplay.printMessage','uid'=>$messageUid));
				return;
			}
			if((bool)$_POST['saveAsDraft'] == true) {
				$formData['isDraft'] = 1; 
				// save as draft
				$messageUid = $this->bocompose->saveAsDraft($formData);
				if (!$messageUid) {
					print "<script type=\"text/javascript\">alert('".lang("Error: Could not save Message as Draft")."');</script>";
				}
			} else {
				if(!$this->bocompose->send($formData)) {
					$this->compose();
					return;
				}
			}

			#$GLOBALS['phpgw']->common->phpgw_exit();
			print "<script type=\"text/javascript\">window.close();</script>";
		}

		function compose($_focusElement='to')
		{
			// read the data from session
			// all values are empty for a new compose window
			$sessionData = $this->bocompose->getSessionData();

			if (is_array($_REQUEST['preset']))
			{
				if ($_REQUEST['preset']['file'] && is_readable($_REQUEST['preset']['file']))
				{
					$this->bocompose->addAttachment(array_merge($sessionData,$_REQUEST['preset']));
					$sessionData = $this->bocompose->getSessionData();
				}
				foreach(array('to','cc','bcc','subject','body') as $name)
				{
					if ($_REQUEST['preset'][$name]) $sessionData[$name] = $_REQUEST['preset'][$name];
				}
			}

			// is the to address set already?
			if (!empty($_REQUEST['send_to']))
			{
				$sessionData['to'] = base64_decode($_REQUEST['send_to']);
			}
			//is the MimeType set/requested
			if (!empty($_REQUEST['mimeType'])) 
			{
				$sessionData['mimeType'] = $_REQUEST['mimeType'];
			}
			// is a certain signature requested?
			// only the following values are supported (and make sense)
			// no => means -2
			// system => means -1
			// default => fetches the default, which is standard behavior
			if (!empty($_REQUEST['signature']) && (strtolower($_REQUEST['signature']) == 'no' || strtolower($_REQUEST['signature']) == 'system'))
			{
				$presetSig = (strtolower($_REQUEST['signature']) == 'no' ? -2 : -1);
			}
			$this->display_app_header();

			$this->t->set_file(array("composeForm" => "composeForm.tpl"));
			$this->t->set_block('composeForm','header','header');
			$this->t->set_block('composeForm','body_input');
			$this->t->set_block('composeForm','attachment','attachment');
			$this->t->set_block('composeForm','attachment_row','attachment_row');
			$this->t->set_block('composeForm','attachment_row_bold');
			$this->t->set_block('composeForm','destination_row');
			$this->t->set_block('composeForm','simple_text');

			$this->translate();

	/*		$this->t->set_var("link_addressbook",$GLOBALS['phpgw']->link('/index.php',array(
				'menuaction' => 'addressbook.addressbook_ui.emailpopup'
			),true));
*/
			$this->t->set_var("link_addressbook",$GLOBALS['phpgw']->link('/felamimail/addressbook.php',false,true));

	
			$this->t->set_var("focusElement",$_focusElement);

			$linkData = array
			(
				'menuaction'	=> 'felamimail.uicompose.selectFolder',
			);
			$this->t->set_var('folder_select_url',$GLOBALS['phpgw']->link('/index.php',$linkData,true));

			$linkData = array
			(
				'menuaction'	=> 'felamimail.uicompose.fileSelector',
				'composeid'	=> $this->composeID
			);
			$this->t->set_var('file_selector_url',$GLOBALS['phpgw']->link('/index.php',$linkData,true));

			$linkData = array
			(
				'menuaction'	=> 'felamimail.uicompose.action',
				'composeid'	=> $this->composeID
			);
			$this->t->set_var("link_action",$GLOBALS['phpgw']->link('/index.php',$linkData,true));
			$this->t->set_var('folder_name',$this->bofelamimail->sessionData['mailbox']);
			$this->t->set_var('compose_id',$this->composeID);

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
			$allIdentities = $this->mailPreferences->getIdentity();
			#_debug_array($allIdentities);
			$defaultIdentity = 0;
			foreach($allIdentities as $key => $singleIdentity) {
				#$identities[$singleIdentity->id] = $singleIdentity->realName.' <'.$singleIdentity->emailAddress.'>';
				$identities[$key] = $singleIdentity->realName.' <'.$singleIdentity->emailAddress.'>';
				if(!empty($singleIdentity->default)) {
					#$defaultIdentity = $singleIdentity->id;
					$defaultIdentity = $key;
					$sessionData['signatureID'] = $singleIdentity->signature;
				}
			}
			$selectFrom = html::select('identity', $defaultIdentity, $identities, true, "style='width:100%;' onchange='changeIdentity(this);'");
			$this->t->set_var('select_from', $selectFrom);

			// navbar(, kind of)
			$this->t->set_var('img_clear_left', $GLOBALS['phpgw']->common->image('felamimail','clear_left'));
			$this->t->set_var('img_fileopen', $GLOBALS['phpgw']->common->image('phpgwapi','fileopen'));
			$this->t->set_var('img_mail_send', $GLOBALS['phpgw']->common->image('felamimail','mail_send'));
			$this->t->set_var('img_attach_file', $GLOBALS['phpgw']->common->image('felamimail','attach'));
			$this->t->set_var('ajax-loader', $GLOBALS['phpgw']->common->image('felamimail','ajax-loader'));
			$this->t->set_var('img_fileexport', $GLOBALS['phpgw']->common->image('felamimail','fileexport'));
			// prepare print url/button
			$this->t->set_var('img_print_it', $GLOBALS['phpgw']->common->image('felamimail','fileprint'));
			$this->t->set_var('lang_print_it', lang('print it'));
			$this->t->set_var('print_it', $printURL);
			// from, to, cc, replyto
			$destinationRows = 0;
			foreach(array('to','cc','bcc','replyto','folder') as $destination) {
				foreach((array)$sessionData[$destination] as $key => $value) {
					$selectDestination = html::select('destination[]', $destination, $this->destinations, false, "style='width: 100%;' onchange='fm_compose_changeInputType(this)'");
					$this->t->set_var('select_destination', $selectDestination);
					$this->t->set_var('address', @htmlentities($value, ENT_QUOTES, $this->displayCharset));
					$this->t->parse('destinationRows','destination_row',True);
					$destinationRows++;
				}
			}
			while($destinationRows < 3) {
				// and always add one empty row
				$selectDestination = html::select('destination[]', 'to', $this->destinations, false, "style='width: 100%;' onchange='fm_compose_changeInputType(this)'");
				$this->t->set_var('select_destination', $selectDestination);
				$this->t->set_var('address', '');
				$this->t->parse('destinationRows','destination_row',True);
				$destinationRows++;
			}
			// and always add one empty row
			$selectDestination = html::select('destination[]', 'to', $this->destinations, false, "style='width: 100%;' onchange='fm_compose_changeInputType(this)'");
			$this->t->set_var('select_destination', $selectDestination);
			$this->t->set_var('address', '');
			$this->t->parse('destinationRows','destination_row',True);

			$this->t->set_var("subject",@htmlentities($sessionData['subject'],ENT_QUOTES,$this->displayCharset));
			$this->t->set_var('addressbookImage',$GLOBALS['phpgw']->common->image('phpgwapi/templates/phpgw_website','users'));
	//		$this->t->set_var('infologImage',html::image('felamimail','to_infolog',lang('Save as infolog'),'width="17px" height="17px" valign="middle"' ));
	//		$this->t->set_var('lang_save_as_infolog',lang('Save as infolog'));
			$this->t->set_var('lang_no_recipient',lang('No recipient address given!'));
			$this->t->set_var('lang_no_subject',lang('No subject given!'));
			$this->t->pparse("out","header");


			// body
			if($sessionData['mimeType'] == 'html') {
				$style="border:0px; width:100%; height:400px;";
				$this->t->set_var('tinymce', html::fckEditorQuick('body', 'simple', $sessionData['body']));
				$this->t->set_var('mimeType', 'html');
				$ishtml=1;
			} else {
				$style="border:0px; width:100%; height:400px;";
				$this->t->set_var('tinymce', html::fckEditorQuick('body', 'ascii', $sessionData['body']));
				$this->t->set_var('mimeType', 'text');
				$ishtml=0;
			}

			require_once(PHPGW_INCLUDE_ROOT.'/felamimail/inc/class.felamimail_bosignatures.inc.php');
			$boSignatures = new felamimail_bosignatures();
			$signatures = $boSignatures->getListOfSignatures();
			if (empty($sessionData['signatureID'])) {
				if ($signatureData = $boSignatures->getDefaultSignature()) {
					if (is_array($signatureData)) {
						$sessionData['signatureID'] = $signatureData['signatureid'];
					} else {
						$sessionData['signatureID'] =$signatureData;
					}
				}
			}
	
			$selectSignatures = array(
				'-2' => lang('no signature')
			);
			foreach($signatures as $signature) {
				$selectSignatures[$signature['fm_signatureid']] = $signature['fm_description'];
			}
			$selectBox = html::select('signatureID', ($presetSig ? $presetSig : $sessionData['signatureID']), $selectSignatures, true, "style='width: 70%;' onchange='fm_compose_changeInputType(this)'");
			$this->t->set_var("select_signature", $selectBox);
			$this->t->set_var("lang_editormode",lang("Editor type"));
			$this->t->set_var("toggle_editormode", lang("Editor type").":&nbsp;<span><input name=\"_is_html\" value=\"".$ishtml."\" type=\"hidden\" /><input name=\"_editorselect\" onchange=\"fm_toggle_editor(this)\" ".($ishtml ? "checked=\"checked\"" : "")." id=\"_html\" value=\"html\" type=\"radio\"><label for=\"_html\">HTML</label><input name=\"_editorselect\" onchange=\"fm_toggle_editor(this)\" ".($ishtml ? "" : "checked=\"checked\"")." id=\"_plain\" value=\"plain\" type=\"radio\"><label for=\"_plain\">Plain text</label></span>");
			$this->t->pparse("out","body_input");

			// attachments
			if (is_array($sessionData['attachments']) && count($sessionData['attachments']) > 0)
			{
				$imgClearLeft	=  $GLOBALS['phpgw']->common->image('felamimail','clear_left');
				foreach((array)$sessionData['attachments'] as $id => $attachment) {
					$tempArray = array (
						'1' => $attachment['name'],
						'2' => $attachment['type'], '.2' => "style='text-align:center;'",
						'3' => $attachment['size'],
						'4' => "<img src='$imgClearLeft' onclick=\"fm_compose_deleteAttachmentRow(this,'$_composeID','$id')\">"
					);
					$tableRows[] = $tempArray;
				}

				if(count($tableRows) > 0) {
					$table = html::table($tableRows, "style='width:100%'");
				}
				$this->t->set_var('attachment_rows',$table);
			}
			else
			{
				$this->t->set_var('attachment_rows','');
			}

			$this->t->pparse("out","attachment");
		}

		function composeFromDraft() {
			$icServer = (int)$_GET['icServer'];
			$folder = base64_decode($_GET['folder']);
			$replyID = $_GET['uid'];

			if (!empty($folder) && !empty($replyID) ) {
				// this fill the session data with the values from the original email
				$this->bocompose->getDraftData($icServer, $folder, $replyID);
			}
			$this->compose('body');
		}


		function display_app_header()
		{
			$GLOBALS['phpgw']->js->validate_file('jscode','composeMessage','felamimail');
			$GLOBALS['phpgw']->js->set_onload('javascript:initAll();');
			$GLOBALS['phpgw_info']['flags']['include_xajax'] = True;

			$GLOBALS['phpgw']->common->phpgw_header();
		}

		function fileSelector()
		{
			if(is_array($_FILES["addFileName"])) {
				#phpinfo();
				#_debug_array($_FILES);
				if($_FILES['addFileName']['error'] == $UPLOAD_ERR_OK) {
					$formData['name']	= $_FILES['addFileName']['name'];
					$formData['type']	= $_FILES['addFileName']['type'];
					$formData['file']	= $_FILES['addFileName']['tmp_name'];
					$formData['size']	= $_FILES['addFileName']['size'];
					$this->bocompose->addAttachment($formData);
					print "<script type='text/javascript'>window.close();</script>";
				} else {
					print "<script type='text/javascript'>document.getElementById('fileSelectorDIV1').style.display = 'inline';document.getElementById('fileSelectorDIV2').style.display = 'none';</script>";
				}
			}

			if(!@is_object($GLOBALS['phpgw']->js))
			{
				$GLOBALS['phpgw']->js = CreateObject('phpgwapi.javascript');
			}
			$GLOBALS['phpgw']->js->validate_file('dhtmlxtree','js/dhtmlXCommon');
			$GLOBALS['phpgw']->js->validate_file('dhtmlxtree','js/dhtmlXTree');
			$GLOBALS['phpgw']->js->validate_file('jscode','composeMessage','felamimail');
			$GLOBALS['phpgw']->common->phpgw_header();

			#$uiwidgets		= CreateObject('felamimail.uiwidgets');

			$this->t->set_file(array("composeForm" => "composeForm.tpl"));
			$this->t->set_block('composeForm','fileSelector','fileSelector');

			$this->translate();

			$linkData = array
			(
				'menuaction'	=> 'felamimail.uicompose.fileSelector',
				'composeid'	=> $this->composeID
			);
			$this->t->set_var('file_selector_url', $GLOBALS['phpgw']->link('/index.php',$linkData));

			$maxUploadSize = ini_get('upload_max_filesize');
			$this->t->set_var('max_uploadsize', $maxUploadSize);

			$this->t->set_var('ajax-loader', $GLOBALS['phpgw']->common->image('felamimail','ajax-loader'));

			$this->t->pparse("out","fileSelector");
		}

		function forward() {
			$icServer = (int)$_GET['icServer'];
			$folder = base64_decode($_GET['folder']);
			$replyID = $_GET['reply_id'];
			$partID  = $_GET['part_id'];

			if (!empty($replyID))
			{
				// this fill the session data with the values from the original email
				$this->bocompose->getForwardData($icServer, $folder, $replyID, $partID);
			}
			$this->compose();
		}

		function getAttachment()
		{
			$bocompose  = CreateObject('felamimail.bocompose', $_GET['_composeID']);
			$attachment =  $bocompose->sessionData['attachments'][$_GET['attID']] ;
			header ("Content-Type: ".$attachment['type']."; name=\"". $this->bofelamimail->decode_header($attachment['name']) ."\"");
			header ("Content-Disposition: inline; filename=\"". $this->bofelamimail->decode_header($attachment['name']) ."\"");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: public");
			$fp = fopen($attachment['file'], 'rb');
			fpassthru($fp);
			$GLOBALS['phpgw']->common->phpgw_exit();
			exit;

		}


		function selectFolder()
		{
			$GLOBALS['phpgw']->js->validate_file('dhtmlxtree','js/dhtmlXCommon');
			$GLOBALS['phpgw']->js->validate_file('dhtmlxtree','js/dhtmlXTree');
			$GLOBALS['phpgw']->js->validate_file('jscode','composeMessage','felamimail');
			$GLOBALS['phpgw']->common->phpgw_header();

			$bofelamimail		= CreateObject('felamimail.bofelamimail',$this->displayCharset);
			$uiwidgets		= CreateObject('felamimail.uiwidgets');
			$connectionStatus	= $bofelamimail->openConnection();

			$folderObjects = $bofelamimail->getFolderObjects(false);
			$folderTree = $uiwidgets->createHTMLFolder
			(
				$folderObjects,
				'INBOX',
				0,
				lang('IMAP Server'),
				$mailPreferences['username'].'@'.$mailPreferences['imapServerAddress'],
				'divFolderTree',
				false,
				true
			);
			print '<div id="divFolderTree" style="overflow:auto; width:320px; height:450px; margin-bottom: 0px;padding-left: 0px; padding-top:0px; z-index:100; border : 1px solid Silver;"></div>';
			print $folderTree;
		}

		function composeAsNew() {
			$icServer = (int)$_GET['icServer'];
			$folder = base64_decode($_GET['folder']);
			$replyID = $_GET['reply_id'];
			$partID  = $_GET['part_id'];
			if (!empty($folder) && !empty($replyID) ) {
				// this fill the session data with the values from the original email
				$this->bocompose->getDraftData($icServer, $folder, $replyID, $partID);
			}
			$this->compose('body');
		}

		function reply() {
			$icServer = (int)$_GET['icServer'];
			$folder = base64_decode($_GET['folder']);
			$replyID = $_GET['reply_id'];
			$partID	 = $_GET['part_id'];
			if (!empty($folder) && !empty($replyID) ) {
				// this fill the session data with the values from the original email
				$this->bocompose->getReplyData('single', $icServer, $folder, $replyID, $partID);
			}
			$this->compose('body');
		}

		function replyAll() {
			$icServer = (int)$_GET['icServer'];
			$folder = base64_decode($_GET['folder']);
			$replyID = $_GET['reply_id'];
			$partID	 = $_GET['part_id'];
			if (!empty($folder) && !empty($replyID) ) {
				// this fill the session data with the values from the original email
				$this->bocompose->getReplyData('all', $icServer, $folder, $replyID, $partID);
			}
			$this->compose('body');
		}

		function translate() {
			$this->t->set_var("lang_message_list",lang('Message List'));
			$this->t->set_var("lang_to",lang('to'));
			$this->t->set_var("lang_cc",lang('cc'));
			$this->t->set_var("lang_bcc",lang('bcc'));
			$this->t->set_var("lang_identity",lang('identity'));
			$this->t->set_var("lang_reply_to",lang('reply to'));
			$this->t->set_var("lang_subject",lang('subject'));
			$this->t->set_var("lang_addressbook",lang('addressbook'));
			$this->t->set_var("lang_search",lang('search'));
			$this->t->set_var("lang_send",lang('send'));
			$this->t->set_var('lang_save_as_draft',lang('save as draft'));
			$this->t->set_var("lang_back_to_folder",lang('back to folder'));
			$this->t->set_var("lang_attachments",lang('attachments'));
			$this->t->set_var("lang_add",lang('add'));
			$this->t->set_var("lang_remove",lang('remove'));
			$this->t->set_var("lang_priority",lang('priority'));
			$this->t->set_var("lang_normal",lang('normal'));
			$this->t->set_var("lang_high",lang('high'));
			$this->t->set_var("lang_low",lang('low'));
			$this->t->set_var("lang_signature",lang('signature'));
			$this->t->set_var("lang_select_folder",lang('select folder'));
			$this->t->set_var('lang_max_uploadsize',lang('max uploadsize'));
			$this->t->set_var('lang_adding_file_please_wait',lang('Adding file to message. Please wait!'));
			$this->t->set_var('lang_receive_notification',lang('Receive notification'));
			$this->t->set_var('lang_no_address_set',lang('can not send message. no recipient defined!'));

			$this->t->set_var("th_bg",$GLOBALS['phpgw_info']["theme"]["th_bg"]);
			$this->t->set_var("bg01",$GLOBALS['phpgw_info']["theme"]["bg01"]);
			$this->t->set_var("bg02",$GLOBALS['phpgw_info']["theme"]["bg02"]);
			$this->t->set_var("bg03",$GLOBALS['phpgw_info']["theme"]["bg03"]);
		}

}
