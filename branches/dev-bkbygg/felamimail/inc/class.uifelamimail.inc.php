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
	if(! is_file(PHPGW_INCLUDE_ROOT.'/felamimail/inc/egw-pear/Net/IMAP.php'))
	{
		throw new Exception('missing egw-pear package');
	}

	class uifelamimail
	{
		var $public_functions = array
		(
			'addVcard'		=> True,
			'changeFilter'		=> True,
			'changeFolder'		=> True,
			'changeSorting'		=> True,
			'compressFolder'	=> True,
			'deleteMessage'		=> True,
			'handleButtons'		=> True,
			'hookAdmin'		=> True,
			'toggleFilter'		=> True,
			'viewMainScreen'	=> True
		);
		
		var $mailbox;		// the current folder in use
		var $startMessage;	// the first message to show
		var $sort;		// how to sort the messages
		var $moveNeeded;	// do we need to move some messages?

		var $timeCounter;
		
		// the object storing the data about the incoming imap server
		var $icServerID=0;
		
		function __construct()
		{
			require_once(PHPGW_SERVER_ROOT.'/felamimail/inc/xajax.inc.php');

			$xajax =& new xajax($GLOBALS['phpgw']->link('/felamimail/xajax.php',false,true), 'xajax_', 'utf-8');
			$xajax->waitCursorOff();
			$xajax->registerFunction("doXMLHTTP");

			$GLOBALS['phpgw_info']['flags']['java_script'] .= $xajax->getJavascript($GLOBALS['phpgw_info']['server']['webserver_url'] . '/felamimail/js/');

			$GLOBALS['phpgw']->js->validate_file('jsapi', 'jsapi', 'felamimail');
			$this->timeCounter = microtime(true);

			$this->displayCharset	= 'utf-8';
			$this->bofelamimail     =& CreateObject('felamimail.bofelamimail',$this->displayCharset);

			$this->bofilter		=& CreateObject('felamimail.bofilter');
			$this->bopreferences	=& CreateObject('felamimail.bopreferences');
			$this->preferences	= $this->bopreferences->getPreferences();
			$this->botranslation	=& CreateObject('phpgwapi.translation');

			$this->bofelamimail->saveSessionData();

			$this->mailbox 		= $this->bofelamimail->sessionData['mailbox'];
			$this->startMessage 	= $this->bofelamimail->sessionData['startMessage'];
			$this->sort 		= $this->bofelamimail->sessionData['sort'];
			$this->sortReverse 	= $this->bofelamimail->sessionData['sortReverse'];
	#		$this->filter 		= $this->bofelamimail->sessionData['activeFilter'];

			$this->t			=& CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
			#$this->grants[$this->account]	= PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;
			// this need to fixed
			// this does not belong to here

			if($_GET['menuaction'] != 'felamimail.uifelamimail.hookAdmin' &&
				 $_GET['menuaction'] != 'felamimail.uifelamimail.changeFolder') {
				$this->connectionStatus = $this->bofelamimail->openConnection($this->icServerID);
			}

			$this->rowColor[0] = $GLOBALS['phpgw_info']["theme"]["row_on"];
			$this->rowColor[1] = $GLOBALS['phpgw_info']["theme"]["row_off"];

			$this->dataRowColor[0] = $GLOBALS['phpgw_info']["theme"]["bg01"];
			$this->dataRowColor[1] = $GLOBALS['phpgw_info']["theme"]["bg02"];
			#print __LINE__ . ': ' . (microtime(true) - $this->timeCounter) . '<br>';
			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			$GLOBALS['phpgw']->css->validate_file('app', 'felamimail');
		}

		function addVcard()
		{
			$messageID 	= $_GET['messageID'];
			$partID 	= $_GET['partID'];
			$attachment = $this->bofelamimail->getAttachment($messageID,$partID);
			
			$tmpfname = tempnam ($GLOBALS['phpgw_info']['server']['temp_dir'], "phpgw_");
			$fp = fopen($tmpfname, "w");
			fwrite($fp, $attachment['attachment']);
			fclose($fp);
			
			$vcard =& CreateObject('phpgwapi.vcard');
			$entry = $vcard->in_file($tmpfname);
			$entry['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
			$entry['access'] = 'private';
			$entry['tid'] = 'n';
			
			print quoted_printable_decode($entry['fn'])."<br>";
			
			unlink($tmpfname);
			
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
		
		function changeFilter()
		{
			if(isset($_POST["filter"]))
			{
				$data['quickSearch']	= $_POST["quickSearch"];
				$data['filter']		= $_POST["filter"];
				$this->bofilter->updateFilter($data);
			}
			elseif(isset($_GET["filter"]))
			{
				$data['filter']		= $_GET["filter"];
				$this->bofilter->updateFilter($data);
			}
			$this->viewMainScreen();
		}
		
		function changeFolder()
		{
			// change folder
			$this->bofelamimail->sessionData['mailbox']	= urldecode($_GET["mailbox"]);
			$this->bofelamimail->sessionData['startMessage']= 1;
			$this->bofelamimail->sessionData['sort']	= $this->preferences->preferences['sortOrder'];
			$this->bofelamimail->sessionData['activeFilter']= -1;

			$this->bofelamimail->saveSessionData();
			
			$this->mailbox 		= $this->bofelamimail->sessionData['mailbox'];
			$this->startMessage 	= $this->bofelamimail->sessionData['startMessage'];
			$this->sort 		= $this->bofelamimail->sessionData['sort'];
			
			$this->connectionStatus = $this->bofelamimail->openConnection();
			
			$this->viewMainScreen();
		}

		function changeSorting()
		{
			// change sorting
			if(isset($_GET["sort"]))
			{
				$this->bofelamimail->sessionData['sort']	= $_GET["sort"];
				$this->sort					= $_GET["sort"];
	
				$this->bofelamimail->saveSessionData();
			}
			
			$this->viewMainScreen();
		}

/*		function compressFolder()
		{
			$this->bofelamimail->compressFolder();
			$this->viewMainScreen();
		} */


		function deleteMessage()
		{
			$preferences		= ExecMethod('felamimail.bopreferences.getPreferences');

			$message[] = $_GET["message"];
			$mailfolder = NULL;
			if (!empty($_GET['folder'])) $mailfolder  = base64_decode($_GET['folder']);
	
			$this->bofelamimail->deleteMessages($message,$mailfolder);

			// set the url to open when refreshing
			$linkData = array
			(
				'menuaction'	=> 'felamimail.uifelamimail.viewMainScreen'
			);
			$refreshURL = $GLOBALS['phpgw']->link('/index.php',$linkData);

			print "<script type=\"text/javascript\">
			opener.location.href = '" .$refreshURL. "';
			window.close();</script>";
		}
		
		function display_app_header()
		{
			#$GLOBALS['phpgw']->js->validate_file('foldertree','foldertree');
			$GLOBALS['phpgw']->js->validate_file('dhtmlxtree','js/dhtmlXCommon', 'felamimail');
			$GLOBALS['phpgw']->js->validate_file('dhtmlxtree','js/dhtmlXTree', 'felamimail');
			$GLOBALS['phpgw']->js->validate_file('jscode','viewMainScreen','felamimail');
			$GLOBALS['phpgw_info']['flags']['include_xajax'] = True;

			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();
		}
	
		function handleButtons()
		{
			if($this->moveNeeded == "1")
			{
				$this->bofelamimail->moveMessages($_POST["mailbox"],
									$_POST["msg"]);
			}
			
			elseif(!empty($_POST["mark_deleted"]) &&
				is_array($_POST["msg"]))
			{
				$this->bofelamimail->deleteMessages($_POST["msg"]);
			}
			
			elseif(!empty($_POST["mark_unread"]) &&
				is_array($_POST["msg"]))
			{
				$this->bofelamimail->flagMessages("unread",$_POST["msg"]);
			}
			
			elseif(!empty($_POST["mark_read"]) &&
				is_array($_POST["msg"]))
			{
				$this->bofelamimail->flagMessages("read",$_POST["msg"]);
			}
			
			elseif(!empty($_POST["mark_unflagged"]) &&
				is_array($_POST["msg"]))
			{
				$this->bofelamimail->flagMessages("unflagged",$_POST["msg"]);
			}
			
			elseif(!empty($_POST["mark_flagged"]) &&
				is_array($_POST["msg"]))
			{
				$this->bofelamimail->flagMessages("flagged",$_POST["msg"]);
			}
			

			$this->viewMainScreen();
		}

		function hookAdmin()
		{
			if(!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				echo lang('access not permitted');
				$GLOBALS['phpgw']->log->message('F-Abort, Unauthorized access to felamimail.uifelamimail.hookAdmin');
				$GLOBALS['phpgw']->log->commit();
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			
			if(!empty($_POST['profileID']) && is_int(intval($_POST['profileID'])))
			{
				$profileID = intval($_POST['profileID']);
				$this->bofelamimail->setEMailProfile($profileID);
			}
			
			$boemailadmin = new emailadmin_bo();
			
			$profileList = $boemailadmin->getProfileList();
			$profileID = $this->bofelamimail->getEMailProfile();
			
			$this->display_app_header();
			
			$this->t->set_file(array("body" => "selectprofile.tpl"));
			$this->t->set_block('body','main');
			$this->t->set_block('body','select_option');
			
			$this->t->set_var('lang_select_email_profile',lang('select emailprofile'));
			$this->t->set_var('lang_site_configuration',lang('site configuration'));
			$this->t->set_var('lang_save',lang('save'));
			$this->t->set_var('lang_back',lang('back'));

			$linkData = array
			(
				'menuaction'	=> 'felamimail.uifelamimail.hookAdmin'
			);
			$this->t->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$linkData));
			
			$linkData = array
			(
				'menuaction'	=> 'emailadmin.emailadmin_ui.listProfiles'
			);
			$this->t->set_var('lang_go_emailadmin', lang('use <a href="%1">EmailAdmin</a> to create profiles', $GLOBALS['phpgw']->link('/index.php',$linkData)));
			
			$this->t->set_var('back_url',$GLOBALS['phpgw']->link('/admin/index.php'));
			
			if(isset($profileList) && is_array($profileList))
			{
				foreach($profileList as $key => $value)
				{
					#print "$key => $value<br>";
					#_debug_array($value);
					$this->t->set_var('profileID',$value['profileID']);
					$this->t->set_var('description',$value['description']);
					if(is_int($profileID) && $profileID == $value['profileID'])
					{
						$this->t->set_var('selected','selected');
					}
					else
					{
						$this->t->set_var('selected','');
					}
					$this->t->parse('select_options','select_option',True);
				}
			}
			
			$this->t->parse("out","main");
			print $this->t->get('out','main');
			
		}

		function viewMainScreen()
		{
			#printf ("this->uifelamimail->viewMainScreen() start: %s<br>",date("H:i:s",mktime()));
			$bopreferences		=& CreateObject('felamimail.bopreferences');
			$bofilter		=& CreateObject('felamimail.bofilter');
			$uiwidgets		=& CreateObject('felamimail.uiwidgets');

			$preferences		=& $bopreferences->getPreferences();
			$urlMailbox		=  urlencode($this->mailbox);

			$imapServer =& $preferences->getIncomingServer(0);
			$activeIdentity =& $preferences->getIdentity(0);
			#_debug_array($activeIdentity);
			$maxMessages		=&  $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			if (empty($maxMessages)) $maxMessages = 23; // this seems to be the number off messages that fit the height of the folder tree
			$userPreferences	=&  $GLOBALS['phpgw_info']['user']['preferences']['felamimail'];
			
			$this->display_app_header();

			$this->t->set_file(array("body" => 'mainscreen.tpl'));
			$this->t->set_block('body','main');
			$this->t->set_block('body','status_row_tpl');
			$this->t->set_block('body','table_header_felamimail');
			$this->t->set_block('body','table_header_outlook');
			$this->t->set_block('body','error_message');
			$this->t->set_block('body','quota_block');
			$this->t->set_block('body','subject_same_window');
			$this->t->set_block('body','subject_new_window');

			$this->translate();
			
			$this->t->set_var('oldMailbox',$urlMailbox);
			$this->t->set_var('image_path',PHPGW_IMAGES);
			#printf ("this->uifelamimail->viewMainScreen() Line 272: %s<br>",date("H:i:s",mktime()));
			$linkData = array
			(
				'menuaction'    => 'felamimail.uifelamimail.viewMainScreen'
			);
			$refreshURL = $GLOBALS['phpgw']->link('/index.php',$linkData);
			$this->t->set_var('reloadView',$refreshURL);
			// display a warning if vacation notice is active
			if(is_a($imapServer,'defaultimap') && $imapServer->enableSieve) {
				$this->bosieve		=& CreateObject('felamimail.bosieve',$imapServer);
				$this->bosieve->retrieveRules($this->bosieve->scriptName);
				$vacation = $this->bosieve->getVacation($this->bosieve->scriptName);
			}
			if(is_array($vacation) && $vacation['status'] == 'on')
			{
				$this->t->set_var('vacation_warning',
					html::image('phpgwapi','dialog_warning',false,'style="vertical-align: middle; width: 16px;"').lang('Vacation notice is active'));
			}
			else
			{
				$this->t->set_var('vacation_warning','&nbsp;');
			}

			// ui for the quotas
			if($this->connectionStatus !== false) {
				$quota = $this->bofelamimail->getQuotaRoot();
			} else {
				$quota['limit'] = 'NOT SET';
			}

			if($quota !== false && $quota['limit'] != 'NOT SET') {
				$quotaDisplay = $uiwidgets->quotaDisplay($quota['usage'], $quota['limit']);
				$this->t->set_var('quota_display', $quotaDisplay);
			} else {
				$this->t->set_var('quota_display','&nbsp;');
			}

			$linkData = array (
				'menuaction'    => 'felamimail.uicompose.compose'
			);
			$urlCompose = "egw_openWindowCentered('".$GLOBALS['phpgw']->link('/index.php',$linkData)."','compose', 700, egw_getWindowOuterHeight());";

			$navbarImages = array(
				'new'			=> array(
					'action'	=> $urlCompose,
					'tooltip'	=> lang('compose'),
				),
				'read_small'		=> array(
					'action'	=> "flagMessages('read')",
					'tooltip'	=> lang('mark selected as read'),
				),
				'unread_small'		=> array(
					'action'	=> "flagMessages('unread')",
					'tooltip'	=> lang('mark selected as unread'),
				),
				'unread_flagged_small'	=> array(
					'action'	=> "flagMessages('flagged')",
					'tooltip'	=> lang('mark selected as flagged'),
				),
				'read_flagged_small'	=> array(
					'action'	=> "flagMessages('unflagged')",
					'tooltip'	=> lang('mark selected as unflagged'),
				),
				'delete'		=> array(
					'action'	=> "deleteMessages(xajax.getFormValues('formMessageList'))",
					'tooltip'	=> lang('mark as deleted'),
				),
			);
			
			foreach($navbarImages as $buttonName => $buttonInfo) {
				$navbarButtons .= $uiwidgets->navbarButton($buttonName, $buttonInfo['action'], $buttonInfo['tooltip']);
			}
			$this->t->set_var('navbarButtonsLeft',$navbarButtons);

			$navbarImages = array(
				'last'		=> array(
					'action'	=> "jumpEnd(); return false;",
					'tooltip'	=> '',
				),
				'right'			=> array(
					'action'	=> "skipForward(); return false;",
					'tooltip'	=> '',
				),
				'left'		=> array(
					'action'	=> "skipPrevious(); return false;",
					'tooltip'	=> '',
				),
				'first'			=> array(
					'action'	=> "jumpStart(); return false;",
					'tooltip'	=> '',
				),
			);
			$navbarButtons  = '';
			foreach($navbarImages as $buttonName => $buttonInfo) {
				$navbarButtons .= $uiwidgets->navbarButton($buttonName, $buttonInfo['action'], $buttonInfo['tooltip'],'right');
			}
			$this->t->set_var('navbarButtonsRight',$navbarButtons);

			// set the images
			$listOfImages = array(
				'read_small',
				'unread_small',
				'unread_flagged_small',
				'read_flagged_small',
				'trash',
				'sm_envelope',
				'write_mail',
				'manage_filter',
				'msg_icon_sm',
				'mail_find',
				'new',
				'start_kde',
				'previous_kde',
				'next_kde',
				'finnish_kde',
				'ajax-loader',
			);

			foreach ($listOfImages as $image) {
				$this->t->set_var($image, $GLOBALS['phpgw']->common->image('felamimail', $image));
			}
			$this->t->set_var('img_clear_left', html::image('felamimail', 'clear_left', lang('clear search'), 'style="margin-left:5px; cursor: pointer;" onclick="fm_clearSearch()"'));
			// refresh settings
			$refreshTime = $userPreferences['refreshTime'];
			$this->t->set_var('refreshTime',$refreshTime*60*1000);
			// other settings
			$prefaskformove = intval($userPreferences['prefaskformove']) ? intval($userPreferences['prefaskformove']) : 0;
			$this->t->set_var('prefaskformove',$prefaskformove);	
			#// set the url to open when refreshing
			#$linkData = array
			#(
			#	'menuaction'	=> 'felamimail.uifelamimail.viewMainScreen'
			#);
			#$this->t->set_var('refresh_url',$GLOBALS['phpgw']->link('/index.php',$linkData));
			
			// define the sort defaults
			$dateSort	= '0';
			$dateCSS	= 'text_small';
			$fromSort	= '3';
			$fromCSS	= 'text_small';
			$subjectSort	= '5';
			$subjectCSS	= 'text_small';
			$sizeSort	= '6';
			$sizeCSS	= 'text_small';

			// and no overwrite the defaults
			switch($this->sort)
			{
				// sort by date newest first
				case '0':
					$dateCSS	= 'text_small_bold';
					break;

				// sort by from z->a
				case '2':
					$fromCSS	= 'text_small_bold';
					break;
				// sort by from a->z
				case '3':
					$subjectCSS	= 'text_small_bold';
					break;
				// sort by size z->a
				case '6':
					$sizeCSS	= 'text_small_bold';
					break;
			}

			// sort by date
			$this->t->set_var('css_class_date', $dateCSS);
		
			// sort by from
			$this->t->set_var('css_class_from', $fromCSS);
		
			// sort by subject
			$this->t->set_var('css_class_subject', $subjectCSS);
			
			// sort by size
			$this->t->set_var('css_class_size', $sizeCSS);
			
			#_debug_array($this->bofelamimail->sessionData['messageFilter']);
			if(!empty($this->bofelamimail->sessionData['messageFilter']['string'])) {
				$this->t->set_var('quicksearch', $this->bofelamimail->sessionData['messageFilter']['string']);
			}
			
			$defaultSearchType = (isset($this->bofelamimail->sessionData['messageFilter']['type']) ? $this->bofelamimail->sessionData['messageFilter']['type'] : 'quick');
			$defaultSelectStatus = (isset($this->bofelamimail->sessionData['messageFilter']['status']) ? $this->bofelamimail->sessionData['messageFilter']['status'] : 'any');

			$searchTypes = array(
				'quick'		=> 'quicksearch',
				'subject'	=> 'subject',
				'body'		=> 'message',
				'from'		=> 'from',
				'to'		=> 'to',
				'cc'		=> 'cc',
			);
			$selectSearchType = html::select('searchType', $defaultSearchType, $searchTypes, false, "style='width:100%;' id='searchType' onchange='document.getElementById(\"quickSearch\").focus(); document.getElementById(\"quickSearch\").value=\"\" ;return false;'");
			$this->t->set_var('select_search', $selectSearchType);
			
			$statusTypes = array(
				'any'		=> 'any status',
				'flagged'	=> 'flagged',
				'unseen'	=> 'unread',
				'answered'	=> 'replied',
				'seen'		=> 'read',
				'deleted'	=> 'deleted',
			);
			$selectStatus = html::select('status', $defaultSelectStatus, $statusTypes, false, "style='width:100%;' onchange='javascript:quickSearch();' id='status'");
			$this->t->set_var('select_status', $selectStatus);

			$selectedID = 0;
			if($this->preferences->userDefinedAccounts) $allAccountData = $this->bopreferences->getAllAccountData($this->preferences);
			if ($allAccountData) {
				foreach ($allAccountData as $tmpkey => $accountData)
				{
					$identity =& $accountData['identity'];
					$icServer =& $accountData['icServer'];
					//_debug_array($identity);
					//_debug_array($icServer);
					if (empty($icServer->host)) continue;
					$identities[$identity->id]=$identity->realName.' '.$identity->organization.' <'.$identity->emailAddress.'>';
					if (!empty($identity->default)) $selectedID = $identity->id;
				}
			}
			if (!isset($activeIdentity->id) && $selectedID == 0) {
				$identities[0] = $activeIdentity->realName.' '.$activeIdentity->organization.' <'.$activeIdentity->emailAddress.'>';
			}
			// if you use user defined accounts you may want to access the profile defined with the emailadmin available to the user
			if ($activeIdentity->id) {
				$boemailadmin = new emailadmin_bo();
				$defaultProfile = $boemailadmin->getUserProfile() ;
				#_debug_array($defaultProfile);
				$identitys =& $defaultProfile->identities;
				$icServers =& $defaultProfile->ic_server;
				foreach ($identitys as $tmpkey => $identity)
				{
					if (empty($icServers[$tmpkey]->host)) continue;
					$identities[0] = $identity->realName.' '.$identity->organization.' <'.$identity->emailAddress.'>';
				}
				#$identities[0] = $defaultIdentity->realName.' '.$defaultIdentity->organization.' <'.$defaultIdentity->emailAddress.'>';
			}
			$selectAccount = html::select('accountSelect', $selectedID, $identities, true, "style='width:100%;' onchange='changeActiveAccount(this);'");
			$this->t->set_var('accountSelect', $selectAccount);


			if($this->connectionStatus === false) {
				$this->t->set_var('connection_error_message', lang($this->bofelamimail->getErrorMessage()));
				$this->t->set_var('message', '&nbsp;');
				$this->t->parse('header_rows','error_message',True);
			} else {
				$headers = $this->bofelamimail->getHeaders($this->mailbox, $this->startMessage, $maxMessages, $this->sort, $this->sortReverse, $this->bofelamimail->sessionData['messageFilter']);

 				$headerCount = count($headers['header']);
					
 				// if there aren't any messages left (eg. after delete or move) 
 				// adjust $this->startMessage  
 				if ($headerCount==0 && $this->startMessage > $maxMessages) {
 					$this->startMessage = $this->startMessage - $maxMessages;
					$headers = $this->bofelamimail->getHeaders($this->startMessage, $maxMessages, $this->sort);
					$headerCount = count($headers['header']);
				}
				
				if ($this->bofelamimail->isSentFolder($this->mailbox) 
					|| $this->bofelamimail->isDraftFolder($this->mailbox) 
					|| $this->bofelamimail->isTemplateFolder($this->mailbox)) {
					$this->t->set_var('lang_from',lang("to"));
				} else {
					$this->t->set_var('lang_from',lang("from"));
				}
				$msg_icon_sm = $GLOBALS['phpgw']->common->image('felamimail','msg_icon_sm');
				
				$this->t->set_var('header_rows',
					$uiwidgets->messageTable(
						$headers,
						($this->bofelamimail->isSentFolder($this->mailbox) 
							|| $this->bofelamimail->isDraftFolder( $this->mailbox) 
							|| $this->bofelamimail->isTemplateFolder($this->mailbox)),
						$this->mailbox,
						$userPreferences['message_newwindow'],
						$userPreferences['rowOrderStyle']
					)
				);

				$firstMessage = $headers['info']['first'];
				$lastMessage = $headers['info']['last'];
				$totalMessage = $headers['info']['total'];
				$langTotal = lang("total");		
			

				$this->t->set_var('maxMessages',$i);
				if($_GET["select_all"] == "select_all") {
					$this->t->set_var('checkedCounter',$i);
				} else {
					$this->t->set_var('checkedCounter','0');
				}
			
				// set the select all/nothing link
				if($_GET["select_all"] == "select_all") {
					// link to unselect all messages
					$linkData = array
					(
						'menuaction'	=> 'felamimail.uifelamimail.viewMainScreen'
					);
					$selectLink = sprintf("<a class=\"body_link\" href=\"%s\">%s</a>",
								$GLOBALS['phpgw']->link('/index.php',$linkData),
								lang("Unselect All"));
					$this->t->set_var('change_folder_checked','');
					$this->t->set_var('move_message_checked','checked');
				} else {
					// link to select all messages
					$linkData = array
					(
						'select_all'	=> 'select_all',
						'menuaction'	=> 'felamimail.uifelamimail.viewMainScreen'
					);
					$selectLink = sprintf("<a class=\"body_link\" href=\"%s\">%s</a>",
								$GLOBALS['phpgw']->link('/index.php',$linkData),
								lang("Select all"));
					$this->t->set_var('change_folder_checked','checked');
					$this->t->set_var('move_message_checked','');
				}
				$this->t->set_var('select_all_link',$selectLink);
			
				$this->t->set_var('message',lang("Viewing messages")." <b>$firstMessage</b> - <b>$lastMessage</b> ($totalMessage $langTotal)");
				if($firstMessage > 1) {
					$linkData = array
					(
						'menuaction'	=> 'felamimail.uifelamimail.viewMainScreen',
						'startMessage'	=> $this->startMessage - $maxMessages
					);
					$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
					$this->t->set_var('link_previous',"<a class=\"body_link\" href=\"$link\">".lang("previous")."</a>");
				} else {
					$this->t->set_var('link_previous',lang("previous"));
				}

				if($totalMessage > $lastMessage) {
					$linkData = array (
						'menuaction'	=> 'felamimail.uifelamimail.viewMainScreen',
						'startMessage'	=> $this->startMessage + $maxMessages
					);
					$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
					$this->t->set_var('link_next',"<a class=\"body_link\" href=\"$link\">".lang("next")."</a>");
				} else {
					$this->t->set_var('link_next',lang("next"));
				}
				$this->t->parse('status_row','status_row_tpl',True);
				//print __LINE__ . ': ' . (microtime(true) - $this->timeCounter) . '<br>';
			
				$folderObjects = $this->bofelamimail->getFolderObjects(true, false);
				//print __LINE__ . ': ' . (microtime(true) - $this->timeCounter) . '<br>';
				$folderStatus = $this->bofelamimail->getFolderStatus($this->mailbox);
				//print __LINE__ . ': ' . (microtime(true) - $this->timeCounter) . '<br>';

				$folderTree = $uiwidgets->createHTMLFolder
				(
					$folderObjects, 
					$this->mailbox, 
					$folderStatus['unseen'],
					lang('IMAP Server'), 
					$imapServer->username.'@'.$imapServer->host,
					'divFolderTree',
					FALSE
				);
				//print __LINE__ . ': ' . (microtime(true) - $this->timeCounter) . '<br>';
				$this->bofelamimail->closeConnection();

			}
			$this->t->set_var('current_mailbox',$current_mailbox);
			$this->t->set_var('folder_tree',$folderTree);

			$this->t->set_var('options_folder',$options_folder);
			
			$linkData = array
			(
				'menuaction'    => 'felamimail.uicompose.compose'
			);
			$this->t->set_var('url_compose_empty',"egw_openWindowCentered('".$GLOBALS['phpgw']->link('/index.php',$linkData)."','test',700,egw_getWindowOuterHeight());");


			$linkData = array
			(
				'menuaction'    => 'felamimail.uifilter.mainScreen'
			);
			$this->t->set_var('url_filter',$GLOBALS['phpgw']->link('/index.php',$linkData));

			$linkData = array
			(
				'menuaction'    => 'felamimail.uifelamimail.handleButtons'
			);
			$this->t->set_var('url_change_folder',$GLOBALS['phpgw']->link('/index.php',$linkData));

			$linkData = array
			(
				'menuaction'    => 'felamimail.uifelamimail.changeFilter'
			);
			$this->t->set_var('url_search_settings',$GLOBALS['phpgw']->link('/index.php',$linkData));

			$this->t->set_var('lang_mark_messages_as',lang('mark messages as'));
			$this->t->set_var('lang_delete',lang('delete'));

			switch($GLOBALS['phpgw_info']['user']['preferences']['felamimail']['rowOrderStyle']) {
				case 'outlook':
					$this->t->parse('messageListTableHeader','table_header_outlook',True);
					break;
				default:
					$this->t->parse('messageListTableHeader','table_header_felamimail',True);
					break;
			}
			//print __LINE__ . ': ' . (microtime(true) - $this->timeCounter) . '<br>';
			
			$this->t->parse("out","main");
			print $this->t->get('out','main');
			
			$GLOBALS['phpgw']->common->phpgw_footer();
		}

		function array_merge_replace( $array, $newValues ) 
		{
			foreach ( $newValues as $key => $value ) 
			{
				if ( is_array( $value ) ) 
				{
					if ( !isset( $array[ $key ] ) ) 
					{
						$array[ $key ] = array();
					}
					$array[ $key ] = $this->array_merge_replace( $array[ $key ], $value );
				} 
				else 
				{
					if ( isset( $array[ $key ] ) && is_array( $array[ $key ] ) ) 
					{
						$array[ $key ][ 0 ] = $value;
					} 
					else 
					{
						if ( isset( $array ) && !is_array( $array ) ) 
						{
							$temp = $array;
							$array = array();
							$array[0] = $temp;
						}
						$array[ $key ] = $value;
					}
				}
			}
			return $array;
		}

		/* Returns a string showing the size of the message/attachment */
		function show_readable_size($bytes, $_mode='short')
		{
			$bytes /= 1024;
			$type = 'k';
			
			if ($bytes / 1024 > 1)
			{
				$bytes /= 1024;
				$type = 'M';
			}
			
			if ($bytes < 10)
			{
				$bytes *= 10;
				settype($bytes, 'integer');
				$bytes /= 10;
			}
			else
				settype($bytes, 'integer');
			
			return $bytes . '&nbsp;' . $type ;
		}
		
		function toggleFilter()
		{
			$this->bofelamimail->toggleFilter();
			$this->viewMainScreen();
		}

		function translate()
		{
			$this->t->set_var('th_bg',$GLOBALS['phpgw_info']["theme"]["th_bg"]);
			$this->t->set_var('bg_01',$GLOBALS['phpgw_info']["theme"]["bg01"]);
			$this->t->set_var('bg_02',$GLOBALS['phpgw_info']["theme"]["bg02"]);

			$this->t->set_var('lang_compose',lang('compose'));
			$this->t->set_var('lang_edit_filter',lang('edit filter'));
			$this->t->set_var('lang_move_selected_to',lang('move selected to'));
			$this->t->set_var('lang_doit',lang('do it!'));
			$this->t->set_var('lang_change_folder',lang('change folder'));
			$this->t->set_var('lang_move_message',lang('move messages'));
			$this->t->set_var('desc_read',lang("mark selected as read"));
			$this->t->set_var('desc_unread',lang("mark selected as unread"));
			$this->t->set_var('desc_important',lang("mark selected as flagged"));
			$this->t->set_var('desc_unimportant',lang("mark selected as unflagged"));
			$this->t->set_var('desc_deleted',lang("delete selected"));
			$this->t->set_var('lang_date',lang("date"));
			$this->t->set_var('lang_status',lang('status'));
			$this->t->set_var('lang_size',lang("size"));
			$this->t->set_var('lang_search',lang("search"));
			$this->t->set_var('lang_replied',lang("replied"));
			$this->t->set_var('lang_read',lang("read"));
			$this->t->set_var('lang_unread',lang("unread"));
			$this->t->set_var('lang_deleted',lang("deleted"));
			$this->t->set_var('lang_recent',lang("recent"));
			$this->t->set_var('lang_flagged',lang("flagged"));
			$this->t->set_var('lang_unflagged',lang("unflagged"));
			$this->t->set_var('lang_subject',lang("subject"));
			$this->t->set_var('lang_add_to_addressbook',lang("add to addressbook"));
			$this->t->set_var('lang_no_filter',lang("no filter"));
			$this->t->set_var('lang_connection_failed',lang("The connection to the IMAP Server failed!!"));
			$this->t->set_var('lang_select_target_folder',lang("Simply click the target-folder"));
			$this->t->set_var('lang_updating_message_status',lang("updating message status"));
			$this->t->set_var('lang_loading',lang('loading'));
			$this->t->set_var('lang_deleting_messages',lang('deleting messages'));
			$this->t->set_var('lang_open_all',lang("open all"));
			$this->t->set_var('lang_close_all',lang("close all"));
			$this->t->set_var('lang_moving_messages_to',lang('moving messages to'));
			$this->t->set_var('lang_askformove',lang('Do you really want to move the selected messages to folder:'));
			$this->t->set_var('lang_empty_trash',lang('empty trash'));
			$this->t->set_var('lang_compress_folder',lang('compress folder'));
			$this->t->set_var('lang_skipping_forward',lang('skipping forward'));
			$this->t->set_var('lang_skipping_previous',lang('skipping previous'));
			$this->t->set_var('lang_jumping_to_start',lang('jumping to start'));
			$this->t->set_var('lang_jumping_to_end',lang('jumping to end'));
			$this->t->set_var('lang_updating_view',lang('updating view'));
		}
	}
