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

	/**
	* copyright notice for the functions highlightQuotes and _countQuoteChars
	*
	* The Text:: class provides common methods for manipulating text.
	*
	* $Horde: horde/lib/Text.php,v 1.80 2003/09/16 23:06:15 jan Exp $
	*
	* Copyright 1999-2003 Jon Parise <jon@horde.org>
	*
	* See the enclosed file COPYING for license information (LGPL). If you
	* did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
	*
	*/

	/* $Id$ */

	phpgw::import_class('felamimail.html');

	class uidisplay
	{

		var $public_functions = array
		(
			'display'	=> 'True',
			'displayBody'	=> 'True',
			'displayHeader'	=> 'True',
			'displayImage'	=> 'True',
			'printMessage'	=> 'True',
			'saveMessage'	=> 'True',
			'showHeader'	=> 'True',
			'getAttachment'	=> 'True',
		);

		var $icServerID=0;

		// the object storing the data about the incoming imap server
		var $icServer=0;

		// the non permanent id of the message
		var $id;

		// the permanent id of the message
		var $uid;

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw']->js->validate_file('jsapi', 'jsapi', 'felamimail');
			/* Having this defined in just one spot could help when changes need
			 * to be made to the pattern
			 * Make sure that the expression is evaluated case insensitively
			 *
			 * RFC2822 (and RFC822) defines the left side of an email address as (roughly):
			 *  1*atext *("." 1*atext)
			 * where atext is: a-zA-Z0-9!#$%&'*+-/=?^_`{|}~
			 *
			 * Here's pretty sophisticated IP matching:
			 * $IPMatch = '(2[0-5][0-9]|1?[0-9]{1,2})';
			 * $IPMatch = '\[?' . $IPMatch . '(\.' . $IPMatch . '){3}\]?';
			 */
			/* Here's enough: */
			global $IP_RegExp_Match, $Host_RegExp_Match, $Email_RegExp_Match;
			$IP_RegExp_Match = '\\[?[0-9]{1,3}(\\.[0-9]{1,3}){3}\\]?';
			$Host_RegExp_Match = '('.$IP_RegExp_Match.'|[0-9a-z]([-.]?[0-9a-z])*\\.[a-z][a-z]+)';
			$atext = '([a-z0-9!#$&%*+/=?^_`{|}~-]|&amp;)';
			$dot_atom = $atext.'+(\.'.$atext.'+)*';
			$Email_RegExp_Match = $dot_atom.'(%'.$Host_RegExp_Match.')?@'.$Host_RegExp_Match;

			$this->t 		=& CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
			$this->displayCharset   = 'utf-8';
			$this->bofelamimail	=& CreateObject('felamimail.bofelamimail',$this->displayCharset);
			$this->bopreferences	=& CreateObject('felamimail.bopreferences');
			$this->botranslation	= CreateObject('felamimail.translation');

			$this->mailPreferences	= $this->bopreferences->getPreferences();

			$this->bofelamimail->openConnection($this->icServerID);

			$this->mailbox		= $this->bofelamimail->sessionData['mailbox'];
			if (!empty($_GET['mailbox'])) $this->mailbox  = base64_decode($_GET['mailbox']);

			$this->sort		= $this->bofelamimail->sessionData['sort'];

			if(isset($_GET['uid'])) {
				$this->uid	= (int)$_GET['uid'];
			}

			if(isset($_GET['id'])) {
				$this->id	= (int)$_GET['id'];
			}

			if(isset($this->id) && !isset($this->uid)) {
				if($uid = $this->bofelamimail->idToUid($this->mailbox, $this->id)) {
					$this->uid = $uid;
				}
			}

			if(isset($_GET['part'])) {
				$this->partID = (int)$_GET['part'];
			}

			$this->rowColor[0] = $GLOBALS['phpgw_info']["theme"]["bg01"];
			$this->rowColor[1] = $GLOBALS['phpgw_info']["theme"]["bg02"];

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			$GLOBALS['phpgw']->css->validate_file('app', 'felamimail');
		}

		/**
		 * Parses a body and converts all found email addresses to clickable links.
		 *
		 * @param string body the body to process, by ref
		 * @return int the number of unique addresses found
		 */
		function parseEmail (&$body) {
			global $Email_RegExp_Match;
			$sbody     = $body;
			$addresses = array();

			/* Find all the email addresses in the body */
			while(preg_match("/$Email_RegExp_Match/i", $sbody, $regs)) {
				$addresses[$regs[0]] = strtr($regs[0], array('&amp;' => '&'));
				$start = strpos($sbody, $regs[0]) + strlen($regs[0]);
				$sbody = substr($sbody, $start);
			}

			/* Replace each email address with a compose URL */
			$lmail='';
			foreach ($addresses as $text => $email) {
				if ($lmail == $email) next($addresses);
				#echo $text."#<br>";
				#echo $email."#<br>";
				$comp_uri = $this->makeComposeLink($email, $text);
				$body = str_replace($text, $comp_uri, $body);
				$lmail=$email;
			}

			/* Return number of unique addresses found */
			return count($addresses);
		}

		function makeComposeLink($email,$text)
		{
			if (!$email || $email == '' || $email == ' ') return '';
			if (!$text) $text = $email;
			// create links for email addresses
			$linkData = array
			(
				'menuaction'    => 'felamimail.uicompose.compose'
			);
			$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
			return '<a href="'.$link.'&send_to='.base64_encode($email).'" target="_top"><font color="blue">'.$text.'</font></a>';
		}

		function highlightQuotes($text, $level = 5)
		{
			// Use a global var since the class is called statically.
			$GLOBALS['_tmp_maxQuoteChars'] = 0;

			// Tack a newline onto the beginning of the string so that we
			// correctly highlight when the first character in the string
			// is a quote character.
			$text = "\n$text";

			preg_replace_callback("/^\s*((&gt;\s?)+)/m", array(&$this, '_countQuoteChars'), $text);

			// Go through each level of quote block and put the
			// appropriate style around it. Important to work downwards so
			// blocks with fewer quote chars aren't matched until their
			// turn.
			for ($i = $GLOBALS['_tmp_maxQuoteChars']; $i > 0; $i--)
			{
				$text = preg_replace(
				// Finds a quote block across multiple newlines.
				"/(\n)( *(&gt;\s?)\{$i}(?! ?&gt;).*?)(\n|$)(?! *(&gt; ?)\{$i})/s",
				'\1<span class="quoted' . ((($i - 1) % $level) + 1) . '">\2</span>\4',$text);
			}

			/* Unset the global variable. */
			unset($GLOBALS['_tmp_maxQuoteChars']);

			/* Remove the leading newline we added above. */
			return substr($text, 1);
		}

		function _countQuoteChars($matches)
		{
			$num = count(preg_split('/&gt;\s?/', $matches[1])) - 1;
			if ($num > $GLOBALS['_tmp_maxQuoteChars'])
			{
				$GLOBALS['_tmp_maxQuoteChars'] = $num;
			}
		}

		function display()
		{
			$partID		= $_GET['part'];
			if (!empty($_GET['mailbox'])) $this->mailbox  = base64_decode($_GET['mailbox']);
			
			$transformdate	=& CreateObject('felamimail.transformdate');
			$htmlFilter	=& CreateObject('felamimail.htmlfilter');
			$uiWidgets	=& CreateObject('felamimail.uiwidgets');
			// (regis) seems to be necessary to reopen...
			$this->bofelamimail->reopen($this->mailbox);
			#print "$this->mailbox, $this->uid, $partID<br>";
			$headers	= $this->bofelamimail->getMessageHeader($this->uid, $partID);
			#_debug_array($headers);exit;
			$rawheaders	= $this->bofelamimail->getMessageRawHeader($this->uid, $partID);
			$attachments	= $this->bofelamimail->getMessageAttachments($this->uid, $partID);
			#_debug_array($attachments); exit;
			$envelope	= $this->bofelamimail->getMessageEnvelope($this->uid, $partID);
			#_debug_array($envelope); exit;
			// if not using iFrames, we need to retrieve the messageBody here
			// by now this is a fixed value and controls the use/loading of the template and how the vars are set.
			// Problem is: the iFrame Layout provides the scrollbars.
			#$bodyParts  = $this->bofelamimail->getMessageBody($this->uid,'',$partID);
			#_debug_array($bodyParts); exit;
			$nextMessage	= $this->bofelamimail->getNextMessage($this->mailbox, $this->uid);

			$webserverURL	= $GLOBALS['phpgw_info']['server']['webserver_url'];

			$nonDisplayAbleCharacters = array('[\016]','[\017]',
					'[\020]','[\021]','[\022]','[\023]','[\024]','[\025]','[\026]','[\027]',
					'[\030]','[\031]','[\032]','[\033]','[\034]','[\035]','[\036]','[\037]');

			#print "<pre>";print_r($rawheaders);print"</pre>";exit;

			// add line breaks to $rawheaders
			$newRawHeaders = explode("\n",$rawheaders);
			reset($newRawHeaders);

			if(isset($headers['ORGANIZATION'])) {
				$organization = $this->bofelamimail->decode_header(trim($headers['ORGANIZATION']));
			}

			if ( isset($headers['DISPOSITION-NOTIFICATION-TO']) ) {
				$sent_not = $this->bofelamimail->decode_header(trim($headers['DISPOSITION-NOTIFICATION-TO']));
			} else if ( isset($headers['RETURN-RECEIPT-TO']) ) {
				$sent_not = $this->bofelamimail->decode_header(trim($headers['RETURN-RECEIPT-TO']));
			} else if ( isset($headers['X-CONFIRM-READING-TO']) ) {
				$sent_not = $this->bofelamimail->decode_header(trim($headers['X-CONFIRM-READING-TO']));
			} else $sent_not = "";

			// reset $rawheaders
			$rawheaders 	= "";
			// create it new, with good line breaks
			reset($newRawHeaders);
			while(list($key,$value) = @each($newRawHeaders)) {
				$rawheaders .= wordwrap($value, 90, "\n     ");
			}

			$this->display_app_header();
			if(!isset($_GET['printable'])) {
				$this->t->set_file(array("displayMsg" => "view_message.tpl"));
			} else {
				$this->t->set_file(array("displayMsg" => "view_message_printable.tpl"));
				$this->t->set_var('charset','utf-8');
			}
			if ( $sent_not != "" && $this->bofelamimail->getNotifyFlags($this->uid) === null ) {
				$this->t->set_var('sentNotify','sendNotify("'.$this->uid.'");');
				$this->t->set_var('lang_sendnotify',lang('The message sender has requested a response to indicate that you have read this message. Would you like to send a receipt?'));
			} else {
				$this->t->set_var('sentNotify','');
			}
			$this->bofelamimail->closeConnection();

			$this->t->set_block('displayMsg','message_main');
			$this->t->set_block('displayMsg','message_main_attachment');
			$this->t->set_block('displayMsg','message_header');
			$this->t->set_block('displayMsg','message_raw_header');
			$this->t->set_block('displayMsg','message_navbar');
			$this->t->set_block('displayMsg','message_onbehalfof');
			$this->t->set_block('displayMsg','message_cc');
			$this->t->set_block('displayMsg','message_bcc');
			$this->t->set_block('displayMsg','message_org');
			$this->t->set_block('displayMsg','message_attachement_row');
			$this->t->set_block('displayMsg','previous_message_block');
			$this->t->set_block('displayMsg','next_message_block');

			$this->t->egroupware_hack = False;

			$this->translate();

//			if(!isset($_GET['printable']))
//			{
			// navbar start
			// compose as new URL
			$linkData = array (
				'menuaction'    => 'felamimail.uicompose.composeAsNew',
				'icServer'  => $this->icServer,
				'folder'    => base64_encode($this->mailbox),
				'reply_id'  => $this->uid,
			);
			if($partID != '') {
				$linkData['part_id'] = $partID;
			}
			$asnewURL = $GLOBALS['phpgw']->link('/index.php',$linkData);

			// reply url
			$linkData = array (
				'menuaction'	=> 'felamimail.uicompose.reply',
				'icServer'	=> $this->icServer,
				'folder'	=> base64_encode($this->mailbox),
				'reply_id'	=> $this->uid,
			);
			if($partID != '') {
				$linkData['part_id'] = $partID;
			}
			$replyURL = $GLOBALS['phpgw']->link('/index.php',$linkData);

			// reply all url
			$linkData = array (
				'menuaction'	=> 'felamimail.uicompose.replyAll',
				'icServer'	=> $this->icServer,
				'folder'	=> base64_encode($this->mailbox),
				'reply_id'	=> $this->uid,
			);
			if($partID != '') {
				$linkData['part_id'] = $partID;
			}
			$replyAllURL = $GLOBALS['phpgw']->link('/index.php',$linkData);

			// forward url
			$linkData = array (
				'menuaction'	=> 'felamimail.uicompose.forward',
				'reply_id'	=> $this->uid,
				'folder'	=> base64_encode($this->mailbox),
			);
			if($partID != '') {
				$linkData['part_id'] = $partID;
			}
			$forwardURL = $GLOBALS['phpgw']->link('/index.php',$linkData);

			//delete url
			$linkData = array (
				'menuaction'	=> 'felamimail.uifelamimail.deleteMessage',
				'icServer'	=> $this->icServer,
				'folder'	=> base64_encode($this->mailbox),
				'message'	=> $this->uid,
			);
			$deleteURL = $GLOBALS['phpgw']->link('/index.php',$linkData);

			$navbarImages = array(
				'new'	=> array(
					'action'    => "window.location.href = '$asnewURL'",
					'tooltip'   => lang('compose as new'),
				),
				'mail_reply'	=> array(
					'action'	=> "window.location.href = '$replyURL'",
					'tooltip'	=> lang('reply'),
				),
				'mail_replyall'	=> array(
					'action'	=> "window.location.href = '$replyAllURL'",
					'tooltip'	=> lang('reply all'),
				),
				'mail_forward'	=> array(
					'action'	=> "window.location.href = '$forwardURL'",
					'tooltip'	=> lang('forward'),
				),
				'delete'	=> array(
					'action'	=> "window.location.href = '$deleteURL'",
					'tooltip'	=> lang('delete'),
				),
			);
			foreach($navbarImages as $buttonName => $buttonInfo) {
				$navbarButtons .= $uiWidgets->navbarButton($buttonName, $buttonInfo['action'], $buttonInfo['tooltip']);
			}
			$navbarButtons .= $uiWidgets->navbarSeparator();

			// print url
			$linkData = array (
				'menuaction'	=> 'felamimail.uidisplay.printMessage',
				'uid'		=> $this->uid,
				'folder'    => base64_encode($this->mailbox),
			);
			if($partID != '') {
				$linkData['part'] = $partID;
			}
			$printURL = $GLOBALS['phpgw']->link('/index.php',$linkData);

			// infolog URL
			$linkData = array(
				'menuaction' => 'infolog.uiinfolog.import_mail',
				'uid'    => $this->uid,
				'mailbox' =>  base64_encode($this->mailbox)
			);
			if($partID != '') {
				$linkData['part'] = $partID;
			}
			$to_infologURL = $GLOBALS['phpgw']->link('/index.php',$linkData);

			// viewheader url
			$linkData = array (
				'menuaction'	=> 'felamimail.uidisplay.displayHeader',
				'uid'		=> $this->uid,
				'mailbox'	=> base64_encode($this->mailbox)
			);
			if($partID != '') {
				$linkData['part'] = $partID;
			}
			$viewHeaderURL = $GLOBALS['phpgw']->link('/index.php',$linkData);

			$navbarImages = array();

			// save message url
			$linkData = array (
				'menuaction'	=> 'felamimail.uidisplay.saveMessage',
				'uid'		=> $this->uid,
				'mailbox'	=> base64_encode($this->mailbox)
			);
			if($partID != '') {
				$linkData['part'] = $partID;
			}
			$saveMessageURL = $GLOBALS['phpgw']->link('/index.php',$linkData);

			$navbarImages = array();

			//print email
			$navbarImages = array(
				'fileprint' => array(
					'action'	=> "window.location.href = '$printURL'",
					'tooltip'	=> lang('print it'),
				),
				'to_infolog' => array(
					'action'	=> "window.open('$to_infologURL','_blank','dependent=yes,width=750,height=550,scrollbars=yes,status=yes')",
					'tooltip'	=> lang('save as infolog'),
				),
			);

			// save email as
			$navbarImages['fileexport'] = array(
				'action'	=> "window.location.href = '$saveMessageURL'",
				'tooltip'	=> lang('save message to disk'),
			);

			// view header lines
			$navbarImages['kmmsgread'] = array(
				'action'	=> "fm_displayHeaderLines('$viewHeaderURL')",
				'tooltip'	=> lang('view header lines'),
			);

			foreach($navbarImages as $buttonName => $buttonData) {
				$navbarButtons .= $uiWidgets->navbarButton($buttonName, $buttonData['action'], $buttonData['tooltip']);
			}
			$this->t->set_var('navbarButtonsLeft',$navbarButtons);

			$navbarButtons = '';
			$navbarImages  = array();
			#_debug_array($nextMessage); exit;

			if($nextMessage['previous']) {
				$linkData = array (
					'menuaction'	=> 'felamimail.uidisplay.display',
					'showHeader'	=> 'false',
					'uid'		=> $nextMessage['previous'],
					'mailbox'	=> base64_encode($this->mailbox)
				);
				$previousURL = $GLOBALS['phpgw']->link('/index.php',$linkData);
				$previousURL = "window.location.href = '$previousURL'";
				$navbarImages['up.button']	= array(
					'action'	=> $previousURL,
					'tooltip'	=> lang('previous message'),
				);
			} else {
				$previousURL = '';
			}

			if($nextMessage['next']) {
				$linkData = array (
					'menuaction'	=> 'felamimail.uidisplay.display',
					'showHeader'	=> 'false',
					'uid'		=> $nextMessage['next'],
					'mailbox'	=> base64_encode($this->mailbox)
				);
				$nextURL = $GLOBALS['phpgw']->link('/index.php',$linkData);
				$nextURL = "window.location.href = '$nextURL'";
				$navbarImages['down.button']	= array(
					'action'	=> $nextURL,
					'tooltip'	=> lang('next message'),
				);
			} else {
				$nextURL = '';
			}


			foreach($navbarImages as $buttonName => $buttonData) {
				$navbarButtons .= $uiWidgets->navbarButton($buttonName, $buttonData['action'], $buttonData['tooltip'], 'right');
			}

			$this->t->set_var('navbarButtonsRight',$navbarButtons);

			$this->t->parse('navbar','message_navbar',True);

			// navbar end
			// header
			// sent by a mailinglist??
			// parse the from header
			if($envelope['FROM'][0] != $envelope['SENDER'][0]) {
				$senderAddress = $this->emailAddressToHTML($envelope['SENDER']);
				$fromAddress   = $this->emailAddressToHTML($envelope['FROM'], $organization);
				$this->t->set_var("from_data",$senderAddress);
				$this->t->set_var("onbehalfof_data",$fromAddress);
				$this->t->parse('on_behalf_of_part','message_onbehalfof',True);
			} else {
				$fromAddress   = $this->emailAddressToHTML($envelope['FROM'], $organization);
				$this->t->set_var("from_data", $fromAddress);
				$this->t->set_var('on_behalf_of_part','');
			}

			// parse the to header
			$toAddress = $this->emailAddressToHTML($envelope['TO']);
			$this->t->set_var("to_data",$toAddress);

			// parse the cc header
			if(count($envelope['CC'])) {
				$ccAddress = $this->emailAddressToHTML($envelope['CC']);
				$this->t->set_var("cc_data",$ccAddress);
				$this->t->parse('cc_data_part','message_cc',True);
			} else {
				$this->t->set_var("cc_data_part",'');
			}

			// parse the bcc header
			if(count($envelope['BCC'])) {
				$bccAddress = $this->emailAddressToHTML($envelope['BCC']);
				$this->t->set_var("bcc_data",$bccAddress);
				$this->t->parse('bcc_data_part','message_bcc',True);
			} else {
				$this->t->set_var("bcc_data_part",'');
			}

			$this->t->set_var("date_received",
				@htmlspecialchars($GLOBALS['phpgw']->common->show_date(strtotime($headers['DATE'])),
				ENT_QUOTES,$this->displayCharset));

			$this->t->set_var("subject_data",
				@htmlspecialchars($this->bofelamimail->decode_subject(preg_replace($nonDisplayAbleCharacters,'',$envelope['SUBJECT'])),
				ENT_QUOTES,$this->displayCharset));

			$this->t->parse("header","message_header",True);

			$this->t->set_var("rawheader",@htmlentities(preg_replace($nonDisplayAbleCharacters,'',$rawheaders),ENT_QUOTES,$this->displayCharset));

			$linkData = array (
					'menuaction'	=> 'felamimail.uidisplay.displayBody',
					'uid'		=> $this->uid,
					'part'		=> $partID,
					'mailbox'	=>  base64_encode($this->mailbox) 
				);
			$this->t->set_var('url_displayBody', $GLOBALS['phpgw']->link('/index.php',$linkData));

			// attachments
			if(is_array($attachments) && count($attachments) > 0) {
				$this->t->set_var('attachment_count',count($attachments));
			} else {
				$this->t->set_var('attachment_count','0');
			}

			if (is_array($attachments) && count($attachments) > 0) {
				$this->t->set_var('row_color',$this->rowColor[0]);
				$this->t->set_var('name',lang('name'));
				$this->t->set_var('type',lang('type'));
				$this->t->set_var('size',lang('size'));
				$this->t->set_var('url_img_save',html::image('felamimail','fileexport', lang('save')));
				#$this->t->parse('attachment_rows','attachment_row_bold',True);

				$detectedCharSet=$charset2use=$this->displayCharset;
				foreach ($attachments as $key => $value)
				{
					#$detectedCharSet = mb_detect_encoding($value['name'].'a',strtoupper($this->displayCharset).",UTF-8, ISO-8559-1");
					mb_convert_variables("UTF-8","ISO-8559-1",$value['name']); # iso 2 UTF8
					//if (mb_convert_variables("ISO-8859-1","UTF-8",$value['name'])){echo "Juhu utf8 2 ISO\n";};
					//echo $value['name']."\n";
					$filename=htmlentities($value['name'], ENT_QUOTES, $detectedCharSet);

					$this->t->set_var('row_color',$this->rowColor[($key+1)%2]);
					$this->t->set_var('filename',($value['name'] ? ( $filename ? $filename : $value['name'] ) : lang('(no subject)')));
					$this->t->set_var('mimetype',$value['mimeType']);
					$this->t->set_var('size',$value['size']);
					$this->t->set_var('attachment_number',$key);

					switch($value['mimeType'])
					{
						case 'MESSAGE/RFC822':
							$linkData = array
							(
								'menuaction'	=> 'felamimail.uidisplay.display',
								'uid'		=> $this->uid,
								'part'		=> $value['partID'],
								'mailbox'	=> base64_encode($this->mailbox),
								'is_winmail'    => $value['is_winmail']
							);
							$windowName = 'displayMessage_'. $this->uid;
							$linkView = "egw_openWindowCentered('".$GLOBALS['phpgw']->link('/index.php',$linkData)."','$windowName',700,egw_getWindowOuterHeight());";
							break;
						case 'IMAGE/JPEG':
						case 'IMAGE/PNG':
						case 'IMAGE/GIF':
						#case 'application/pdf':
							$linkData = array
							(
								'menuaction'	=> 'felamimail.uidisplay.getAttachment',
								'uid'		=> $this->uid,
								'part'		=> $value['partID'],
								'is_winmail'    => $value['is_winmail'],
								'mailbox'   => base64_encode($this->mailbox),
							);
							$windowName = 'displayAttachment_'. $this->uid;
							$linkView = "egw_openWindowCentered('".$GLOBALS['phpgw']->link('/index.php',$linkData)."','$windowName',800,600);";
							break;
						default:
							$linkData = array
							(
								'menuaction'	=> 'felamimail.uidisplay.getAttachment',
								'uid'		=> $this->uid,
								'part'		=> $value['partID'],
								'is_winmail'    => $value['is_winmail'],
								'mailbox'   => base64_encode($this->mailbox),
							);
							$linkView = "window.location.href = '".$GLOBALS['phpgw']->link('/index.php',$linkData)."';";
							break;
					}
					$this->t->set_var("link_view",$linkView);
					$this->t->set_var("target",$target);

					$linkData = array
					(
						'menuaction'	=> 'felamimail.uidisplay.getAttachment',
						'mode'		=> 'save',
						'uid'		=> $this->uid,
						'part'		=> $value['partID'],
						'is_winmail'    => $value['is_winmail'],
						'mailbox'   => base64_encode($this->mailbox),
					);
					$this->t->set_var("link_save",$GLOBALS['phpgw']->link('/index.php',$linkData));

					$this->t->parse('attachment_rows','message_attachement_row',True);
				}
			} else {
				$this->t->set_var('attachment_rows','');
			}

			#$this->t->pparse("out","message_attachment_rows");

			// print it out
			if(is_array($attachments) && count($attachments) > 0) {
				$this->t->pparse('out','message_main_attachment');
			} else {
				$this->t->pparse('out','message_main');
			}

		}

		function displayBody()
		{
			$partID		= $_GET['part'];
			if (!empty($_GET['mailbox'])) $this->mailbox  = base64_decode($_GET['mailbox']);
			
			$this->bofelamimail->reopen($this->mailbox);
			$bodyParts	= $this->bofelamimail->getMessageBody($this->uid,'',$partID);
			$this->bofelamimail->closeConnection();

			$this->display_app_header();
			$this->showBody($this->getdisplayableBody($bodyParts), true);
		}

		function showBody(&$body, $print=true)
		{
			$BeginBody = '<style type="text/css">
				body,html {
			        height:100%;
			        width:100%;
			        padding:0px;
			        margin:0px;
			}
			.td_display {
			        font-family: Verdana, Arial, Helvetica, sans-serif;
			        font-size: 110%;
			        background-color: #FFFFFF;
			}
			</style>
			<div style="height:100%;width:100%; background-color:white; padding:0px; margin:0px;"><table width="100%" style="table-layout:fixed"><tr><td class="td_display">';

            $EndBody = '</td></tr></table>';
			$EndBody .= "</body></html>";
			if ($print)	{
				print $BeginBody. $body .$EndBody;
			} else {
				return $BeginBody. $body .$EndBody;
			}
		}

		function displayHeader()
		{
			$partID		= $_GET['part'];
			if (!empty($_GET['mailbox'])) $this->mailbox  = base64_decode($_GET['mailbox']);

			$transformdate	=& CreateObject('felamimail.transformdate');
			$htmlFilter	=& CreateObject('felamimail.htmlfilter');
			$uiWidgets	=& CreateObject('felamimail.uiwidgets');
			// (regis) seems to be necessary to reopen...
			$this->bofelamimail->reopen($this->mailbox);
			#$headers	= $this->bofelamimail->getMessageHeader($this->mailbox, $this->uid, $partID);
			$rawheaders	= $this->bofelamimail->getMessageRawHeader($this->uid, $partID);

			$webserverURL	= $GLOBALS['phpgw_info']['server']['webserver_url'];

			#$nonDisplayAbleCharacters = array('[\016]','[\017]',
			#		'[\020]','[\021]','[\022]','[\023]','[\024]','[\025]','[\026]','[\027]',
			#		'[\030]','[\031]','[\032]','[\033]','[\034]','[\035]','[\036]','[\037]');

			#print "<pre>";print_r($rawheaders);print"</pre>";exit;

			// add line breaks to $rawheaders
			$newRawHeaders = explode("\n",$rawheaders);
			reset($newRawHeaders);

			// reset $rawheaders
			$rawheaders 	= "";
			// create it new, with good line breaks
			reset($newRawHeaders);
			while(list($key,$value) = @each($newRawHeaders)) {
				$rawheaders .= wordwrap($value, 90, "\n     ");
			}

			$this->bofelamimail->closeConnection();

			header('Content-type: text/html; charset=iso-8859-1');
			print '<pre>'. htmlspecialchars($rawheaders, ENT_NOQUOTES, 'iso-8859-1') .'</pre>';

		}

		function displayImage()
		{
			$cid	= base64_decode($_GET['cid']);
			$partID = urldecode($_GET['partID']);
			if (!empty($_GET['mailbox'])) $this->mailbox  = base64_decode($_GET['mailbox']);

			$this->bofelamimail->reopen($this->mailbox);

			$attachment 	= $this->bofelamimail->getAttachmentByCID($this->uid, $cid, $partID);

			$this->bofelamimail->closeConnection();

			session_write_close();

			if(is_array($attachment)) {
				//error_log("Content-Type: ".$attachment['type']."; name=\"". $attachment['filename'] ."\"");
				header ("Content-Type: ". strtolower($attachment['type']) ."; name=\"". $attachment['filename'] ."\"");
				header ('Content-Disposition: inline; filename="'. $attachment['filename'] .'"');
				header("Expires: 0");
				// the next headers are for IE and SSL
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Pragma: public");

				echo trim($attachment['attachment']);
				exit;
			}

			session_write_close();

			exit;
		}

		function display_app_header($printing = NULL)
		{
			if($_GET['menuaction'] != 'felamimail.uidisplay.printMessage' &&
				$_GET['menuaction'] != 'felamimail.uidisplay.displayBody') {
				$GLOBALS['phpgw']->js->validate_file('tabs','tabs');
				$GLOBALS['phpgw']->js->validate_file('jscode','view_message','felamimail');
				$GLOBALS['phpgw']->js->set_onload('javascript:initAll();');
			}
			
			if(($_GET['menuaction'] == 'felamimail.uidisplay.printMessage') || (!empty($printing) && $printing == 1)) {
				$GLOBALS['phpgw']->js->set_onload('javascript:window.print()');
			}

			if($_GET['menuaction'] == 'felamimail.uidisplay.printMessage' || (!empty($printing) && $printing == 1) ||
				$_GET['menuaction'] == 'felamimail.uidisplay.displayBody') {
				$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			}

			$GLOBALS['phpgw']->common->phpgw_header();
		}

		function emailAddressToHTML($_emailAddress, $_organisation='', $allwaysShowMailAddress=false, $showAddToAdrdessbookLink=true) {
			#_debug_array($_emailAddress);
			// create some nice formated HTML for senderaddress
			#if($_emailAddress['EMAIL'] == 'undisclosed-recipients: ;')
			#	return $_emailAddress['EMAIL'];

			#$addressData = imap_rfc822_parse_adrlist
			#		($this->bofelamimail->decode_header($_emailAddress),'');
			if(is_array($_emailAddress)) {
				$senderAddress = '';
				foreach($_emailAddress as $addressData) {
					#_debug_array($addressData);
					if($addressData['MAILBOX_NAME'] == 'NIL') {
						continue;
					}

					if(!empty($senderAddress)) $senderAddress .= ', ';

					if(strtolower($addressData['MAILBOX_NAME']) == 'undisclosed-recipients') {
						$senderAddress .= 'undisclosed-recipients';
						continue;
					}

					if($addressData['PERSONAL_NAME'] != 'NIL') {
						$newSenderAddress = $addressData['RFC822_EMAIL'] != 'NIL' ? $addressData['RFC822_EMAIL'] : $addressData['EMAIL'];
						$newSenderAddress = $this->bofelamimail->decode_header($newSenderAddress);
						$decodedPersonalName = $this->bofelamimail->decode_header($addressData['PERSONAL_NAME']);

						$realName =  $decodedPersonalName;
						// add mailaddress
						if ($allwaysShowMailAddress) {
							$realName .= ' <'.$addressData['EMAIL'].'>';
						}
						// add organization
						if(!empty($_organisation)) {
							$realName .= ' ('. $_organisation . ')';
						}

						$linkData = array (
							'menuaction'	=> 'felamimail.uicompose.compose',
							'send_to'	=> base64_encode($newSenderAddress)
						);
						$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
						$senderAddress .= sprintf('<a href="%s" title="%s">%s</a>',
									$link,
									@htmlentities($newSenderAddress,ENT_QUOTES,$this->displayCharset),
									@htmlentities($realName, ENT_QUOTES, $this->displayCharset));

						$linkData = array (
							'menuaction'		=> 'addressbook.addressbook_ui.edit',
							'presets[email]'	=> $addressData['EMAIL'],
							'presets[org_name]'	=> $_organisation,
							'referer'		=> $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']
						);
						if (!empty($decodedPersonalName)) {
							if($spacePos = strrpos($decodedPersonalName, ' ')) {
								$linkData['presets[n_family]']	= substr($decodedPersonalName, $spacePos+1);
								$linkData['presets[n_given]'] 	= substr($decodedPersonalName, 0, $spacePos);
							} else {
								$linkData['presets[n_family]']	= $decodedPersonalName;
							}
							$linkData['presets[n_fn]']	= $decodedPersonalName;
						}
						if ($showAddToAdrdessbookLink) {
							$urlAddToAddressbook = $GLOBALS['phpgw']->link('/index.php',$linkData);
							$onClick = "window.open(this,this.target,'dependent=yes,width=850,height=440,location=no,menubar=no,toolbar=no,scrollbars=yes,status=yes'); return false;";
							$image = $GLOBALS['phpgw']->common->image('felamimail','sm_envelope');
							$senderAddress .= sprintf('<a href="%s" onClick="%s">
								<img src="%s" width="10" height="8" border="0"
								align="absmiddle" alt="%s"
								title="%s"></a>',
								$urlAddToAddressbook,
								$onClick,
								$image,
								lang('add to addressbook'),
								lang('add to addressbook'));
						}
					} else {
						$linkData = array (
							'menuaction'	=> 'felamimail.uicompose.compose',
							'send_to'	=> base64_encode($addressData['EMAIL'])
						);
						$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
						$senderAddress .= sprintf('<a href="%s">%s</a>',
									$link,@htmlentities($addressData['EMAIL'], ENT_QUOTES, $this->displayCharset));
						//TODO: This uses old addressbook code, which should be removed in Version 1.4
						//Please use addressbook.addressbook_ui.edit with proper paramenters
						$linkData = array
						(
							'menuaction'		=> 'addressbook.addressbook_ui.edit',
							'presets[email]'	=> $addressData['EMAIL'],
							'presets[org_name]'	=> $_organisation,
							'referer'		=> $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']
						);

						if ($showAddToAdrdessbookLink) {
							$urlAddToAddressbook = $GLOBALS['phpgw']->link('/index.php',$linkData);
							$onClick = "window.open(this,this.target,'dependent=yes,width=850,height=440,location=no,menubar=no,toolbar=no,scrollbars=yes,status=yes'); return false;";
							$image = $GLOBALS['phpgw']->common->image('felamimail','sm_envelope');
							$senderAddress .= sprintf('<a href="%s" onClick="%s">
								<img src="%s" width="10" height="8" border="0"
								align="absmiddle" alt="%s"
								title="%s"></a>',
								$urlAddToAddressbook,
								$onClick,
								$image,
								lang('add to addressbook'),
								lang('add to addressbook'));
						}
					}
				}

				return $senderAddress;
			}

			// if something goes wrong, just return the original address
			return $_emailAddress;
		}

		function getAttachment()
		{

			$part		= $_GET['part'];
			$is_winmail = $_GET['is_winmail'] ? $_GET['is_winmail'] : 0;
			if (!empty($_GET['mailbox'])) $this->mailbox  = base64_decode($_GET['mailbox']);

			$this->bofelamimail->reopen($this->mailbox);
			#$attachment 	= $this->bofelamimail->getAttachment($this->uid,$part);
			$attachment = $this->bofelamimail->getAttachment($this->uid,$part,$is_winmail);
			$this->bofelamimail->closeConnection();

			session_write_close();

			header ("Content-Type: ".$attachment['type']."; name=\"". $attachment['filename'] ."\"");
			if($_GET['mode'] == "save") {
				// ask for download
				header ("Content-Disposition: attachment; filename=\"". $attachment['filename'] ."\"");
			} else {
				// display it
				header ("Content-Disposition: inline; filename=\"". $attachment['filename'] ."\"");
			}
			header("Expires: 0");
			// the next headers are for IE and SSL
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: public");

			echo $attachment['attachment'];

			session_write_close();
			exit;
		}

		function &getdisplayableBody($_bodyParts)
		{
			$bodyParts	= $_bodyParts;

			$webserverURL	= $GLOBALS['phpgw_info']['server']['webserver_url'];

			$nonDisplayAbleCharacters = array('[\016]','[\017]',
					'[\020]','[\021]','[\022]','[\023]','[\024]','[\025]','[\026]','[\027]',
					'[\030]','[\031]','[\032]','[\033]','[\034]','[\035]','[\036]','[\037]');

			$body = '';

			#_debug_array($bodyParts); exit;

			foreach($bodyParts as $singleBodyPart) {
				if(!empty($body)) {
					$body .= '<hr style="border:dotted 1px silver;">';
				}
				#_debug_array($singleBodyPart['charSet']);
				$singleBodyPart['body'] = $this->botranslation->convert(
					$singleBodyPart['body'],
					strtolower($singleBodyPart['charSet'])
				);
				//error_log($singleBodyPart['body']);
				#$CharSetUsed = mb_detect_encoding($singleBodyPart['body'] . 'a' , strtoupper($singleBodyPart['charSet']).','.strtoupper($this->displayCharset).',UTF-8, ISO-8859-1');

				if($singleBodyPart['mimeType'] == 'text/plain')
				{
					$newBody	= $singleBodyPart['body'];

					$newBody	= @htmlentities($singleBodyPart['body'],ENT_QUOTES, strtoupper($this->displayCharset));
					// if the conversion to htmlentities fails somehow, try without specifying the charset, which defaults to iso-
					if (empty($newBody)) $newBody    = htmlentities($singleBodyPart['body'],ENT_QUOTES);
					#$newBody	= $this->bofelamimail->wordwrap($newBody, 90, "\n");

					// search http[s] links and make them as links available again
					// to understand what's going on here, have a look at
					// http://www.php.net/manual/en/function.preg-replace.php

					// create links for websites
					$newBody = preg_replace("/((http(s?):\/\/)|(www\.))([\w,\-,\/,\?,\=,\.,&amp;,!\n,!&gt;,\%,@,\*,#,:,~,\+]+)/ie",
						"'<a href=\"$webserverURL/redirect.php?go='.@htmlentities(urlencode('http$3://$4$5'),ENT_QUOTES,\"$this->displayCharset\").'\" target=\"_blank\"><font color=\"blue\">$2$4$5</font></a>'", $newBody);

					// create links for ftp sites
					$newBody = preg_replace("/((ftp:\/\/)|(ftp\.))([\w\.,-.,\/.,\?.,\=.,&amp;]+)/i",
						"<a href=\"ftp://$3$4\" target=\"_blank\"><font color=\"blue\">ftp://$3$4</font></a>", $newBody);

					// create links for email addresses
					$this->parseEmail($newBody);

					$newBody	= $this->highlightQuotes($newBody);
					$newBody	= nl2br($newBody);
				}
				else
				{
					$newBody	= $singleBodyPart['body'];
					$newBody	= $this->highlightQuotes($newBody);
					bofelamimail::getCleanHTML($newBody);
					// create links for websites
					#$newBody = preg_replace("/(?<!\>)((http(s?):\/\/)|(www\.))([\w,\-,\/,\?,\=,\.,&amp;,!\n,\%,@,\*,#,:,~,\+]+)/ie",
					#	"'<a href=\"$webserverURL/redirect.php?go='.htmlentities(urlencode('http$3://$4$5'),ENT_QUOTES,\"$this->displayCharset\").'\" target=\"_blank\"><font color=\"blue\">$2$4$5</font></a>'", $newBody);
					$newBody = preg_replace("/(?<!>|\/|\")((http(s?):\/\/)|(www\.))([\w,\-,\/,\?,\=,\.,&amp;,!\n,\%,@,\*,#,:,~,\+]+)/ie",
						"'<a href=\"$webserverURL/redirect.php?go='.@htmlentities(urlencode('http$3://$4$5'),ENT_QUOTES,\"$this->displayCharset\").'\" target=\"_blank\"><font color=\"blue\">$2$4$5</font></a>'", $newBody);

					// create links for websites
					$newBody = preg_replace("/href=(\"|\')((http(s?):\/\/)|(www\.))([\w,\-,\/,\?,\=,\.,&amp;,!\n,\%,@,\(,\),\*,#,:,~,\+]+)(\"|\')/ie",
						"'href=\"$webserverURL/redirect.php?go='.@htmlentities(urlencode('http$4://$5$6'),ENT_QUOTES,\"$this->displayCharset\").'\" target=\"_blank\"'", $newBody);

					// create links for ftp sites
					$newBody = preg_replace("/href=(\"|\')((ftp:\/\/)|(ftp\.))([\w\.,-.,\/.,\?.,\=.,&amp;]+)(\"|\')/i",
						"href=\"ftp://$4$5\" target=\"_blank\"", $newBody);

					// create links for inline images
					$linkData = array (
						'menuaction'    => 'felamimail.uidisplay.displayImage',
						'uid'		=> $this->uid,
						'mailbox'	=> base64_encode($this->mailbox)
					);
					$imageURL = $GLOBALS['phpgw']->link('/index.php', $linkData);
					$newBody = preg_replace("/(\"|\')cid:(.*)(\"|\')/iUe",
						"'\"$imageURL&cid='.base64_encode('$2').'&partID='.urlencode($this->partID).'\"'", $newBody);

					// create links for email addresses
					$linkData = array
					(
						'menuaction'    => 'felamimail.uicompose.compose'
					);
					$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
					$newBody = preg_replace("/href=(\"|\')mailto:([\w,\-,\/,\?,\=,\.,&amp;,!\n,\%,@,\*,#,:,~,\+]+)(\"|\')/ie",
						"'href=\"$link&send_to='.base64_encode('$2').'\"'", $newBody);
					#print "<pre>".htmlentities($newBody)."</pre><hr>";

					// replace emails within the text with clickable links.
					$this->parseEmail($newBody);
				}
				$body .= $newBody;
				#print "<hr><pre>$body</pre><hr>";
			}

			// create links for windows shares
			// \\\\\\\\ == '\\' in real life!! :)
			$body = preg_replace("/(\\\\\\\\)([\w,\\\\,-]+)/i",
				"<a href=\"file:$1$2\" target=\"_blank\"><font color=\"blue\">$1$2</font></a>", $body);

			$body = preg_replace($nonDisplayAbleCharacters,'',$body);

			return $body;
		}

		function printMessage($messageId = NULL, $callfromcompose = NULL)
		{
			if (!empty($messageId) && empty($this->uid)) $this->uid = $messageId;
			$partID		= $_GET['part'];
			if (!empty($_GET['folder'])) $this->mailbox  = base64_decode($_GET['folder']);

			$transformdate	=& CreateObject('felamimail.transformdate');
			$htmlFilter	=& CreateObject('felamimail.htmlfilter');
			$uiWidgets	=& CreateObject('felamimail.uiwidgets');
			// (regis) seems to be necessary to reopen...
			$folder = $this->mailbox;
			// the folder for callfromcompose is hardcoded, because the message to be printed from the compose window is saved as draft, and can be
			// reopened for composing (only) from there
			if ($callfromcompose) $folder = $GLOBALS['phpgw_info']['user']['preferences']['felamimail']['draftFolder'];
			$this->bofelamimail->reopen($folder);
#			print "$this->mailbox, $this->uid, $partID<br>";
			$headers	= $this->bofelamimail->getMessageHeader($this->uid, $partID);
			$envelope   = $this->bofelamimail->getMessageEnvelope($this->uid, $partID);
#			_debug_array($headers);exit;
			$rawheaders	= $this->bofelamimail->getMessageRawHeader($this->uid, $partID);
			$bodyParts	= $this->bofelamimail->getMessageBody($this->uid,'',$partID);
			$attachments	= $this->bofelamimail->getMessageAttachments($this->uid,$partID);
#			_debug_array($nextMessage); exit;

			$webserverURL	= $GLOBALS['phpgw_info']['server']['webserver_url'];

			$nonDisplayAbleCharacters = array('[\016]','[\017]',
					'[\020]','[\021]','[\022]','[\023]','[\024]','[\025]','[\026]','[\027]',
					'[\030]','[\031]','[\032]','[\033]','[\034]','[\035]','[\036]','[\037]');

			#print "<pre>";print_r($rawheaders);print"</pre>";exit;

			// add line breaks to $rawheaders
			$newRawHeaders = explode("\n",$rawheaders);
			reset($newRawHeaders);

			// find the Organization header
			// the header can also span multiple rows
			while(is_array($newRawHeaders) && list($key,$value) = each($newRawHeaders)) {
				#print $value."<br>";
				if(preg_match("/Organization: (.*)/",$value,$matches)) {
					$organization = $this->bofelamimail->decode_header(chop($matches[1]));
					continue;
				}
				if(!empty($organization) && preg_match("/^\s+(.*)/",$value,$matches)) {
					$organization .= $this->bofelamimail->decode_header(chop($matches[1]));
					break;
				} elseif(!empty($organization)) {
					break;
				}
			}

			$this->bofelamimail->closeConnection();

			$this->display_app_header($callfromcompose);
			$this->t->set_file(array("displayMsg" => "view_message_printable.tpl"));
		#	$this->t->set_var('charset',$GLOBALS['phpgw']->translation->charset());

			$this->t->set_block('displayMsg','message_main');
		#	$this->t->set_block('displayMsg','message_main_attachment');
			$this->t->set_block('displayMsg','message_header');
		#	$this->t->set_block('displayMsg','message_raw_header');
		#	$this->t->set_block('displayMsg','message_navbar');
			$this->t->set_block('displayMsg','message_onbehalfof');
			$this->t->set_block('displayMsg','message_cc');
			$this->t->set_block('displayMsg','message_attachement_row');
		#	$this->t->set_block('displayMsg','previous_message_block');
		#	$this->t->set_block('displayMsg','next_message_block');
			$this->t->set_block('displayMsg','message_org');

		#	$this->t->egroupware_hack = False;

			$this->translate();

			if($envelope['FROM'][0] != $envelope['SENDER'][0]) {
				$senderAddress = $this->emailAddressToHTML($envelope['SENDER'], '', true, false);
				$fromAddress   = $this->emailAddressToHTML($envelope['FROM'], $organization, true, false);
				$this->t->set_var("from_data",$senderAddress);
				$this->t->set_var("onbehalfof_data",$fromAddress);
				$this->t->parse('on_behalf_of_part','message_onbehalfof',True);
			} else {
				$fromAddress   = $this->emailAddressToHTML($envelope['FROM'], $organization, true, false);
				$this->t->set_var("from_data", $fromAddress);
				$this->t->set_var('on_behalf_of_part','');
			}

			// parse the to header
			$toAddress = $this->emailAddressToHTML($envelope['TO'], '', true, false);
			$this->t->set_var("to_data",$toAddress);

			// parse the cc header
			if(count($envelope['CC'])) {
				$ccAddress = $this->emailAddressToHTML($envelope['CC'], '', true, false);
				$this->t->set_var("cc_data",$ccAddress);
				$this->t->parse('cc_data_part','message_cc',True);
			} else {
				$this->t->set_var("cc_data_part",'');
			}

			$this->t->set_var("date_data",
				@htmlspecialchars($GLOBALS['phpgw']->common->show_date(strtotime($headers['DATE'])), ENT_QUOTES,$this->displayCharset));
			
			// link to go back to the message view. the link differs if the print was called from a normal viewing window, or from compose 
			$subject = @htmlspecialchars($this->bofelamimail->decode_subject(preg_replace($nonDisplayAbleCharacters, '', $envelope['SUBJECT'])), ENT_QUOTES, $this->displayCharset);
			$this->t->set_var("subject_data", $subject);
			$this->t->set_var("full_subject_data", $subject);
			$linkData = array (
				'menuaction'    => 'felamimail.uidisplay.display',
				'showHeader'    => 'false',
				'mailbox'	=> base64_encode($folder),
				'uid'       => $this->uid,
				'id'        => $this->id,
			);
			if ($callfromcompose) {
				$linkData['menuaction'] = 'felamimail.uicompose.composeFromDraft';
				$linkData['folder'] = base64_encode($folder);
			}
			$_readInNewWindow = $this->mailPreferences->preferences['message_newwindow'];
			$this->t->set_var('url_read_message', $GLOBALS['phpgw']->link('/index.php',$linkData));

			$target = 'displayMessage';
			$windowName = ($_readInNewWindow == 1 ? $target : $target.'_'.$this->uid);
			#if ($callfromcompose) $target = 'composeFromDraft';
			if ($callfromcompose) $windowName = '_top';
			$this->t->set_var('read_message_windowName', $windowName);

			//if(isset($organization)) exit;
			$this->t->parse("header","message_header",True);

			$this->t->set_var('body', $this->getdisplayableBody($bodyParts));

			// attachments
			if(is_array($attachments))
				$this->t->set_var('attachment_count',count($attachments));
			else
				$this->t->set_var('attachment_count','0');

			if (is_array($attachments) && count($attachments) > 0) {
				$this->t->set_var('row_color',$this->rowColor[0]);
				$this->t->set_var('name',lang('name'));
				$this->t->set_var('type',lang('type'));
				$this->t->set_var('size',lang('size'));
				$this->t->set_var('url_img_save',$GLOBALS['phpgw']->common->image('felamimail','fileexport'));
				#$this->t->parse('attachment_rows','attachment_row_bold',True);
				foreach ($attachments as $key => $value) {
					$this->t->set_var('row_color',$this->rowColor[($key+1)%2]);
					$this->t->set_var('filename',@htmlentities($this->bofelamimail->decode_header($value['name']),ENT_QUOTES,$this->displayCharset));
					$this->t->set_var('mimetype',$value['mimeType']);
					$this->t->set_var('size',$value['size']);
					$this->t->set_var('attachment_number',$key);

					switch($value['mimeType'])
					{
						case 'message/rfc822':
							$linkData = array
							(
								'menuaction'	=> 'felamimail.uidisplay.display',
								'uid'		=> $this->uid,
								'part'		=> $value['partID'],
								'mailbox'   => base64_encode($folder),
							);
							$windowName = 'displayMessage_'.$this->uid;
							$linkView = "egw_openWindowCentered('".$GLOBALS['phpgw']->link('/index.php',$linkData)."','$windowName',700,egw_getWindowOuterHeight());";
							break;
						case 'image/jpeg':
						case 'image/png':
						case 'image/gif':
						#case 'application/pdf':
							$linkData = array
							(
								'menuaction'	=> 'felamimail.uidisplay.getAttachment',
								'uid'		=> $this->uid,
								'part'		=> $value['partID'],
								'mailbox'   => base64_encode($folder),
							);
							$windowName = 'displayAttachment_'.$this->uid;
							$linkView = "egw_openWindowCentered('".$GLOBALS['phpgw']->link('/index.php',$linkData)."','$windowName',800,600);";
							break;
						default:
							$linkData = array
							(
								'menuaction'	=> 'felamimail.uidisplay.getAttachment',
								'uid'		=> $this->uid,
								'part'		=> $value['partID'],
								'mailbox'   => base64_encode($folder),
							);
							$linkView = "window.location.href = '".$GLOBALS['phpgw']->link('/index.php',$linkData)."';";
							break;
					}
					$this->t->set_var("link_view",$linkView);
					$this->t->set_var("target",$target);

					$linkData = array
					(
						'menuaction'	=> 'felamimail.uidisplay.getAttachment',
						'mode'		=> 'save',
						'uid'		=> $this->uid,
						'part'		=> $value['partID'],
						'mailbox'   => base64_encode($folder),
					);
					$this->t->set_var("link_save",$GLOBALS['phpgw']->link('/index.php',$linkData));

					$this->t->parse('attachment_rows','message_attachement_row',True);
				}
			}
			else
			{
				$this->t->set_var('attachment_rows','');
			}

			#$this->t->pparse("out","message_attachment_rows");

			// print it out
		#	if(is_array($attachments)) {
		#		$this->t->pparse('out','message_main_attachment');
		#	} else {
				$this->t->pparse('out','message_main');
		#	}
			print "</body></html>";

		}

		function saveMessage()
		{
			$partID		= $_GET['part'];
			if (!empty($_GET['mailbox'])) $this->mailbox  = base64_decode($_GET['mailbox']);

			// (regis) seems to be necessary to reopen...
			$this->bofelamimail->reopen($this->mailbox);

			$message = $this->bofelamimail->getMessageRawBody($this->uid, $partID);
			$headers = $this->bofelamimail->getMessageHeader($this->uid, $partID);

			$this->bofelamimail->closeConnection();

			session_write_close();

			header ("Content-Type: message/rfc822; name=\"". $headers['SUBJECT'] .".eml\"");
			header ("Content-Disposition: attachment; filename=\"". $headers['SUBJECT'] .".eml\"");
			header("Expires: 0");
			// the next headers are for IE and SSL
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: public");

			echo $message;

			session_write_close();
			exit;
		}

		function showHeader()
		{
			if($this->bofelamimail->sessionData['showHeader'] == 'True')
			{
				$this->bofelamimail->sessionData['showHeader'] = 'False';
			}
			else
			{
				$this->bofelamimail->sessionData['showHeader'] = 'True';
			}
			$this->bofelamimail->saveSessionData();

			$this->display();
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
			$this->t->set_var("lang_compose",lang('compose'));
			$this->t->set_var("lang_date",lang('date'));
			$this->t->set_var("lang_view",lang('view'));
			$this->t->set_var("lang_organization",lang('organization'));
			$this->t->set_var("lang_save",lang('save'));
			$this->t->set_var("lang_printable",lang('print it'));
			$this->t->set_var("lang_reply",lang('reply'));
			$this->t->set_var("lang_reply_all",lang('reply all'));
			$this->t->set_var("lang_forward",lang('forward'));
			$this->t->set_var("lang_delete",lang('delete'));
			$this->t->set_var("lang_previous_message",lang('previous message'));
			$this->t->set_var("lang_next_message",lang('next message'));
			$this->t->set_var("lang_organisation",lang('organisation'));
			$this->t->set_var("lang_on_behalf_of",lang('on behalf of'));
			$this->t->set_var("lang_Message", lang('Message'));
			$this->t->set_var("lang_Attachment", lang('attachments'));
			$this->t->set_var("lang_Header_Lines", lang('Header Lines'));

			$this->t->set_var("th_bg",$GLOBALS['phpgw_info']["theme"]["th_bg"]);
			$this->t->set_var("bg01",$GLOBALS['phpgw_info']["theme"]["bg01"]);
			$this->t->set_var("bg02",$GLOBALS['phpgw_info']["theme"]["bg02"]);
			$this->t->set_var("bg03",$GLOBALS['phpgw_info']["theme"]["bg03"]);
		}
}

