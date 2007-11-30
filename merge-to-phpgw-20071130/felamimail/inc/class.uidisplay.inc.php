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
	/* $Id: class.uidisplay.inc.php 17988 2007-02-23 14:45:08Z sigurdne $ */

	class uidisplay
	{

		var $public_functions = array
		(
			'display'	=> 'True',
			'showHeader'	=> 'True',
			'getAttachment'	=> 'True'
		);

		function uidisplay()
		{
			$this->t 		= $GLOBALS['phpgw']->template;
			#$this->t 		= CreateObject('phpgwapi.Template_Smarty',PHPGW_APP_TPL);
			$this->bofelamimail	= CreateObject('felamimail.bofelamimail');
			$this->bofilter 	= CreateObject('felamimail.bofilter');
			$this->bopreferences	= CreateObject('felamimail.bopreferences');
			$this->mailPreferences	= $this->bopreferences->getPreferences();
			
			$this->bofelamimail->openConnection();
			
			$this->mailbox		= $this->bofelamimail->sessionData['mailbox'];
			$this->sort		= $this->bofelamimail->sessionData['sort'];
			
			$this->uid		= $GLOBALS['HTTP_GET_VARS']['uid'];

			$this->bocaching	= CreateObject('felamimail.bocaching',
							$this->mailPreferences['imapServerAddress'],
							$this->mailPreferences['username'],
							$this->mailbox);

			$this->rowColor[0] = 'row_on';
			$this->rowColor[1] = 'row_off';

			if(isset($GLOBALS['HTTP_GET_VARS']['showHeader']) && $GLOBALS['HTTP_GET_VARS']['showHeader'] == "false")
			{
				$this->bofelamimail->sessionData['showHeader'] = 'False';
				$this->bofelamimail->saveSessionData();
			}
			
		}
		
		function createLinks($_data)
		{
			
		}
		
		function display()
		{
			$transformdate	= CreateObject('felamimail.transformdate');
			$htmlFilter	= CreateObject('felamimail.htmlfilter');
			$webserverURL	= $GLOBALS['phpgw_info']['server']['webserver_url'];
			
			$headers	= $this->bofelamimail->getMessageHeader($this->uid);
			$rawheaders	= $this->bofelamimail->getMessageRawHeader($this->uid);
			$bodyParts	= $this->bofelamimail->getMessageBody($this->uid);
			$attachments	= $this->bofelamimail->getMessageAttachments($this->uid);
			$filterList 	= $this->bofilter->getFilterList();
			$activeFilter 	= $this->bofilter->getActiveFilter();
			$filter 	= (isset($filterList[$activeFilter])?$filterList[$activeFilter]:'');
			$nextMessage	= $this->bocaching->getNextMessage($this->uid, $this->sort, $filter);

			//print "<pre>";print_r($rawheaders);print"</pre>";exit;

			// add line breaks to $rawheaders
			$newRawHeaders = explode("\n",$rawheaders);
			reset($newRawHeaders);
			// find the Organization header
			// the header can also span multiple rows
			while(is_array($newRawHeaders) && list($key,$value) = each($newRawHeaders))
			{
				#print $value."<br>";
				if(preg_match("/Organization: (.*)/",$value,$matches))
				{
					$organization = $this->bofelamimail->decode_header(chop($matches[1]));
					#$organization = chop($matches[1]);
					continue;
				}
				if(!empty($organization) && preg_match("/^\s+(.*)/",$value,$matches))
				{
					$organization .= $this->bofelamimail->decode_header(chop($matches[1]));
					break;
				}
				elseif(!empty($organization))
				{
					break;
				}
			}
			
			// reset $rawheaders
			$rawheaders 	= "";
			// create it new, with good line breaks
			reset($newRawHeaders);
			while(list($key,$value) = @each($newRawHeaders))
			{
				$rawheaders .= wordwrap($value,90,"\n     ");
			}
			
			$this->bofelamimail->closeConnection();
			
			if(!isset($GLOBALS['HTTP_GET_VARS']['printable']))
			{
				$this->display_app_header();
			}
			
			$this->t->set_root(PHPGW_APP_TPL);
			$this->t->set_file(array("displayMsg" => "view_message.tpl"));
			$this->t->set_block('displayMsg','message_main');
			$this->t->set_block('displayMsg','message_header');
			$this->t->set_block('displayMsg','message_raw_header');
			$this->t->set_block('displayMsg','message_navbar');
			$this->t->set_block('displayMsg','message_navbar_print');
			$this->t->set_block('displayMsg','message_cc');
			$this->t->set_block('displayMsg','message_organization');
			$this->t->set_block('displayMsg','message_attachement_row');
			$this->t->set_block('displayMsg','previous_message_block');
			$this->t->set_block('displayMsg','next_message_block');
			
			$this->translate();
			
			if(!isset($GLOBALS['HTTP_GET_VARS']['printable']))
			{
				// navbar
				$linkData = array
				(
					'menuaction'	=> 'felamimail.uifelamimail.viewMainScreen'
				);
				$this->t->set_var("link_message_list",$GLOBALS['phpgw']->link('/felamimail/index.php',$linkData));
	
				$linkData = array
				(
					'menuaction'	=> 'felamimail.uicompose.compose'
				);
				$this->t->set_var("link_compose",$GLOBALS['phpgw']->link('/index.php',$linkData));
				$this->t->set_var('folder_name',$this->bofelamimail->sessionData['mailbox']);

				$linkData = array
				(
					'menuaction'	=> 'felamimail.uicompose.reply',
					'reply_id'	=> $this->uid
				);
				$this->t->set_var("link_reply",$GLOBALS['phpgw']->link('/index.php',$linkData));

				$linkData = array
				(
					'menuaction'	=> 'felamimail.uicompose.replyAll',
					'reply_id'	=> $this->uid
				);
				$this->t->set_var("link_reply_all",$GLOBALS['phpgw']->link('/index.php',$linkData));

				$linkData = array
				(
					'menuaction'	=> 'felamimail.uicompose.forward',
					'reply_id'	=> $this->uid
				);
				$this->t->set_var("link_forward",$GLOBALS['phpgw']->link('/index.php',$linkData));	

				$linkData = array
				(
					'menuaction'	=> 'felamimail.uifelamimail.deleteMessage',
					'message'	=> $this->uid
				);
				$this->t->set_var("link_delete",$GLOBALS['phpgw']->link('/index.php',$linkData));

				$linkData = array
				(
					'menuaction'	=> 'felamimail.uidisplay.showHeader',
					'uid'		=> $this->uid
				);
				$this->t->set_var("link_header",$GLOBALS['phpgw']->link('/index.php',$linkData));

				$linkData = array
				(
					'menuaction'	=> 'felamimail.uidisplay.display',
					'printable'	=> 1,
					'uid'		=> $this->uid
				);
				$this->t->set_var("link_printable",$GLOBALS['phpgw']->link('/index.php',$linkData));
	
				if(isset($nextMessage['previous']) && $nextMessage['previous'])
				{
					$linkData = array
					(
						'menuaction'	=> 'felamimail.uidisplay.display',
						'showHeader'	=> 'false',
						'uid'		=> $nextMessage['previous']
					);
					$this->t->set_var('previous_url',$GLOBALS['phpgw']->link('/index.php',$linkData));
					$this->t->parse('previous_message','previous_message_block',True);
				}
				else
				{
					$this->t->set_var('previous_message','&nbsp');
				}
	
				if(isset($nextMessage['next']) && $nextMessage['next'])
				{
					$linkData = array
					(
						'menuaction'	=> 'felamimail.uidisplay.display',
						'showHeader'	=> 'false',
						'uid'		=> $nextMessage['next']
					);
					$this->t->set_var('next_url',$GLOBALS['phpgw']->link('/index.php',$linkData));
					$this->t->parse('next_message','next_message_block',True);
				}
				else
				{
					$this->t->set_var('next_message','&nbsp');
				}
	
				$langArray = array
				(
					'lang_messagelist'      => lang('Message List'),
					'lang_compose'          => lang('Compose'),
					'lang_delete'           => lang('Delete'),
					'lang_forward'          => lang('Forward'),
					'lang_reply'            => lang('Reply'),
					'lang_reply_all'        => lang('Reply All'),
					'lang_back_to_folder'   => lang('back to folder'),
					'app_image_path'        => PHPGW_IMAGES
				);
				$this->t->set_var($langArray);
				$this->t->parse('navbar','message_navbar',True);
			}
			else
			{	
				$langArray = array
				(
					'lang_print_this_page'  => lang('print this page'),
					'lang_close_this_page'  => lang('close this page'),
					'lang_printable'        => '',
					'lang_reply'            => lang('Reply'),
					'lang_reply_all'        => lang('Reply All'),
					'lang_back_to_folder'   => lang('back to folder'),
					'app_image_path'        => PHPGW_IMAGES
				);
				$this->t->set_var($langArray);
				$this->t->parse('navbar','message_navbar_print',True);
			}
			
			
			// rawheader
/*			if($this->bofelamimail->sessionData['showHeader'] == 'True')
			{
				$this->t->set_var("raw_header_data",htmlentities($rawheaders));
				$this->t->parse("rawheader",'message_raw_header',True);
				$this->t->set_var("view_header",lang('hide header'));
			}
			else
			{
				$this->t->set_var("rawheader",'');
				$this->t->set_var("view_header",lang('show header'));
			}
*/
			

			// header
			// sent by a mailinglist??
			// parse the from header
			if($headers->senderaddress != $headers->fromaddress)
			{
				$senderAddress = $this->emailAddressToHTML($headers->senderaddress);
				$fromAddress   = $this->emailAddressToHTML($headers->fromaddress);
				$this->t->set_var("from_data",
					$senderAddress.
					"&nbsp;".lang('on behalf of')."&nbsp;".
					$fromAddress);
			}
			else
			{
				$fromAddress   = $this->emailAddressToHTML($headers->fromaddress);
				$this->t->set_var("from_data",
					$fromAddress);
			}
			
			// parse the to header
			$toAddress = isset($headers->toaddress) ? $this->emailAddressToHTML($headers->toaddress) : '';
			$this->t->set_var("to_data",$toAddress);
			
			// parse the cc header
			if(isset($headers->ccaddress) && $headers->ccaddress)
			{
				$ccAddress = $this->emailAddressToHTML($headers->ccaddress);
				$this->t->set_var("cc_data",$ccAddress);
				$this->t->parse('cc_data_part','message_cc',True);
			}
			else
			{
				$this->t->set_var("cc_data_part",'');
			}

			// parse the cc header
			if(!empty($organization))
			{
				$this->t->set_var("organization_data",$organization);
				$this->t->parse('organization_data_part','message_organization',True);
			}
			else
			{
				$this->t->set_var("organization_data_part",'');
			}

			if (isset($headers->date))
			{
				$headers->date = ereg_replace('  ', ' ', $headers->date);
				$tmpdate = explode(' ', trim($headers->date));
			}
			else
			{
				$tmpdate = $date = array("","","","","","");
			}
                                                                                                                                                                                                                                                                                                                
			$this->t->set_var("date_data",htmlentities($GLOBALS['phpgw']->common->show_date($transformdate->getTimeStamp($tmpdate))));
			$this->t->set_var("subject_data",htmlentities($this->bofelamimail->decode_header($headers->subject),ENT_QUOTES,'UTF-8'));
			//if(isset($organization)) exit;
			$this->t->parse("header","message_header",True);

			// body
			if($this->bofelamimail->sessionData['showHeader'] == 'True')
			{
				$body = "<pre>".htmlentities($rawheaders)."</pre>";
				$this->t->set_var("rawheader",'');
				$this->t->set_var("view_header",lang('hide header'));
			}
			else
			{
				$body = '';
				$this->t->set_var("rawheader",'');
				$this->t->set_var("view_header",lang('show header'));
			}

#$tag_list = Array(
#                  false,
#                  'blink',
#                  'object',
#                  'meta',
#                  'font',
#                  'html',
#                  'link',
#                  'frame',
#                  'iframe',
#                  'layer',
#                  'ilayer'
#                 );

$tag_list = Array(true, "b", "a", "i", "img", "strong", "em", "p");
$tag_list = Array(true, "b", "a", "i", "strong", 'pre', 'ul', 'li', 
			"em", "p", 'td', 'tr', 'table', 
			'font', 'hr', 'br', 'div');

$rm_tags_with_content = Array(
                              'script',
                              'style',
                              'applet',
                              'embed',
                              'head',
                              'frameset',
                              'xml'
                              );

$self_closing_tags =  Array(
                            'img',
                            'br',
                            'hr',
                            'input'
                            );

$force_tag_closing = false;

$rm_attnames = Array(
    '/.*/' =>
        Array(
              '/target/i',
              '/^on.*/i',
              '/^dynsrc/i',
              '/^datasrc/i',
              '/^data.*/i',
              '/^lowsrc/i'
              )
    );

/**
 * Yeah-yeah, so this looks horrible. Check out htmlfilter.inc for
 * some idea of what's going on here. :)
 */

$bad_attvals = Array(
    '/.*/' =>
        Array(
	      '/.*/' =>
	          Array(
	                Array(
                          '/^([\'\"])\s*\S+\s*script\s*:*(.*)([\'\"])/i',
#                          '/^([\'\"])\s*https*\s*:(.*)([\'\"])/i',
                          '/^([\'\"])\s*mocha\s*:*(.*)([\'\"])/i',
                          '/^([\'\"])\s*about\s*:(.*)([\'\"])/i'
			     ),
		        Array(
                      '\\1oddjob:\\2\\3',
#                      '\\1uucp:\\2\\3',
                      '\\1amaretto:\\2\\3',
                      '\\1round:\\2\\3'
		             )
			),     
						
              '/^style/i' =>
                  Array(
                        Array(
                              '/expression/i',
                              '/behaviou*r/i',
                              '/binding/i',
                              '/url\(([\'\"]*)\s*https*:.*([\'\"]*)\)/i',
                              '/url\(([\'\"]*)\s*\S+script:.*([\'\"]*)\)/i'
                             ),
                        Array(
                              'idiocy',
                              'idiocy',
                              'idiocy',
                              'url(\\1http://securityfocus.com/\\2)',
                              'url(\\1http://securityfocus.com/\\2)'
                             )
                        )
              )
    );

$add_attr_to_tag = Array(
                         '/^a$/i' => Array('target' => '"_new"')
                         );
                         $add_attr_to_tag = Array();

			for($i=0; $i<count($bodyParts); $i++ )
			{
				// if($i > 0) $body .= "<br><br>Atachment -------------------<br><br>";
			
				// add line breaks to $bodyParts
				#$newBody	= explode("\n",$bodyParts[$i]);
				#$bodyAppend	= '';
				// create it new, with good line breaks
				#reset($newBody);
				#while(list($key,$value) = @each($newBody))
				#{
				#	$bodyAppend .= wordwrap($value,90,"\n",1);
				#}
				
				#$body .= htmlspecialchars($bodyAppend,ENT_QUOTES);

				// add line breaks to $bodyParts
				#$newBody	= wordwrap($bodyParts[$i],90,"\n",1);
				#$newBody	= wordwrap($bodyParts[$i],90,"<br>",1);
				
				if($bodyParts[$i]['mimeType'] == 'text/plain')
				{
					$newBody	= wordwrap($bodyParts[$i]['body'],90,"\n",1);
					$newBody	= htmlspecialchars($newBody,ENT_QUOTES,'UTF-8');

					// search http[s] links and make them as links available again
					// to understand what's going on here, have a look at 
					// http://www.php.net/manual/en/function.preg-replace.php

					// create links for websites
					$newBody = preg_replace("/((http(s?):\/\/)|(www\.))([\w,\-,\/,\?,\=,\.,&amp;,!\n,!&gt;,\%,@,\*,#,:,~,\+]+)/ie", 
						"'<a href=\"$webserverURL/redirect.php?go='.@htmlentities(urlencode('http$3://$4$5'),ENT_QUOTES,'UTF-8').'\" target=\"_blank\"><font color=\"blue\">$2$4$5</font></a>'", $newBody);
			
					// create links for ftp sites
					$newBody = preg_replace("/((ftp:\/\/)|(ftp\.))([\w\.,-.,\/.,\?.,\=.,&amp;]+)/i", 
						"<a href=\"ftp://$3$4\" target=\"_blank\"><font color=\"blue\">ftp://$3$4</font></a>", $newBody);

					// create links for email addresses
					$linkData = array
					(
						'menuaction'    => 'felamimail.uicompose.compose'
					);
					$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
					$newBody = preg_replace("/(?<=\s{1}|&lt;)(([\w\.,-.,_.,0-9.]+)(@)([\w\.,-.,_.,0-9.]+))/ie", 
						"'<a href=\"$link&send_to='.base64_encode('$0').'\"><font color=\"blue\">$0</font></a>'", $newBody);

					$newBody	= nl2br($newBody);
				}
				else
				{
					$newBody	= $bodyParts[$i]['body'];
//					$htmlFilter->debug = true;
					$newBody	= $htmlFilter->sanitize($newBody,
								$tag_list, $rm_tags_with_content,
								$self_closing_tags, $force_tag_closing,
								$rm_attnames, $bad_attvals, $add_attr_to_tag);

					$this->displayCharset = 'utf-8'; // temporary - fix this below
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
					);
					$imageURL = $GLOBALS['phpgw']->link('/index.php', $linkData);
					$newBody = preg_replace("/(\"|\')cid:(.*)(\"|\')/iUe", 
						"'\"$imageURL&cid='.base64_encode('$2').'\"'", $newBody);

					// create links for email addresses
					$linkData = array
					(
						'menuaction'    => 'felamimail.uicompose.compose'
					);
					$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
					$newBody = preg_replace("/href=(\"|\')mailto:([\w,\-,\/,\?,\=,\.,&amp;,!\n,\%,@,\*,#,:,~,\+]+)(\"|\')/ie", 
						"'href=\"$link&send_to='.base64_encode('$2').'\"'", $newBody);
					#print "<pre>".htmlentities($newBody)."</pre><hr>";

					$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
					#$newBody = preg_replace("/(?<!:)(?<=\s{1}|&lt;)(([\w\.,-.,_.,0-9.]+)(@)([\w\.,-.,_.,0-9.]+))/ie", 
					$newBody = preg_replace("/(?<!:)(([\w\.,-.,_.,0-9.]+)(@)([\w\.,-.,_.,0-9.]+))/ie", 
						"'<a href=\"$link&send_to='.('$0').'\"><font color=\"blue\">$0</font></a>'", $newBody);

				}
				$body .= $newBody;
				#print "<hr><pre>$body</pre><hr>";
			}
			
			// create links for windows shares
			// \\\\\\\\ == '\\' in real life!! :)
			$body = preg_replace("/(\\\\\\\\)([\w,\\\\,-]+)/i", 
				"<a href=\"file:$1$2\" target=\"_blank\"><font color=\"blue\">$1$2</font></a>", $body);
			
			// make the signate light grey
			#$body = preg_replace("/(--)/im","<font color=\"grey\">$1</font>", $body);
			
			// create links for email addresses
	/*		$linkData = array
			(
				'menuaction'    => 'felamimail.uicompose.compose'
			);
			$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
		
			$body = preg_replace("/([\w\.,-.,_.,0-9.]+)(@)([\w\.,-.,_.,0-9.]+)/i", 
				"<a href=\"$link&send_to=$0\"><font color=\"blue\">$0</font></a>", $body);
	*/			
			$this->t->set_var("body",$body);
			$this->t->set_var("signature",(isset($sessionData['signature'])?$sessionData['signature']:''));

			// attachments
			if (is_array($attachments) && count($attachments) > 0)
			{
				$this->t->set_var('row_color',$this->rowColor[0]);
				$this->t->set_var('name',lang('name'));
				$this->t->set_var('type',lang('type'));
				$this->t->set_var('size',lang('size'));
				#$this->t->parse('attachment_rows','attachment_row_bold',True);
				while (list($key,$value) = each($attachments))
				{
					$this->t->set_var('row_color',$this->rowColor[($key+1)%2]);
					$this->t->set_var('filename',htmlentities($this->bofelamimail->decode_header($value['name'])));
					$this->t->set_var('mimetype',$value['mimeType']);
					$this->t->set_var('size',$value['size']);
					$this->t->set_var('attachment_number',$key);
					$linkData = array
					(
						'menuaction'	=> 'felamimail.uidisplay.getAttachment',
						'uid'		=> $this->uid,
						'part'		=> $value['partID']
					);
					$this->t->set_var("link_view",$GLOBALS['phpgw']->link('/index.php',$linkData));

					$linkData = array
					(
						'menuaction'	=> 'felamimail.uidisplay.getAttachment',
						'mode'		=> 'save',
						'uid'		=> $this->uid,
						'part'		=> $value['partID']
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
			$this->t->pparse("out","message_main");

		}

		function display_app_header()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			unset($GLOBALS['phpgw']->template->file);
			unset($GLOBALS['phpgw']->template->varkeys);
			unset($GLOBALS['phpgw']->template->varvals);
		}
		
		function emailAddressToHTML($_emailAddress)
		{		
			// create some nice formated HTML for senderaddress
			$addressData = imap_rfc822_parse_adrlist
					($this->bofelamimail->decode_header($_emailAddress),'');
			if(is_array($addressData))
			{
				$senderAddress = '';
				while(list($key,$val)=each($addressData))
				{
					if(!empty($senderAddress)) $senderAddress .= ", ";
					if(!empty($val->personal))
					{
						$tempSenderAddress = $val->mailbox."@".$val->host;
						$newSenderAddress  = imap_rfc822_write_address($val->mailbox,
									$val->host,
									$val->personal);
						$linkData = array
						(
							'menuaction'	=> 'felamimail.uicompose.compose',
							'send_to'	=> utf8_decode($newSenderAddress)
						);
						$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
						$senderAddress .= sprintf('<a href="%s" title="%s">%s</a>',
									$link,
									htmlentities($newSenderAddress,ENT_QUOTES,'UTF-8'),
									htmlentities($val->personal,ENT_QUOTES,'UTF-8'));
					}
					else
					{
						$tempSenderAddress = $val->mailbox."@".$val->host;
						$linkData = array
						(
							'menuaction'	=> 'felamimail.uicompose.compose',
							'send_to'	=> $tempSenderAddress
						);
						$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
						$senderAddress .= sprintf('<a href="%s">%s</a>',
									$link,htmlentities($tempSenderAddress,ENT_QUOTES,'UTF-8'));
					}
				}
				return $senderAddress;
			}
			
			// if something goes wrong, just return the original address
			return $_emailAddress;
		}
		
		function getAttachment()
		{
			
			$part	= $GLOBALS['HTTP_GET_VARS']['part'];
			
			$attachment 	= $this->bofelamimail->getAttachment($this->uid,$part);
			
			$this->bofelamimail->closeConnection();
			
			header ("Content-Type: ".$attachment['type']."; name=\"".$attachment['filename']."\"");
			if(isset($GLOBALS['HTTP_GET_VARS']['mode']) && $GLOBALS['HTTP_GET_VARS']['mode'] == "save")
			{
				// ask for download
				header ("Content-Disposition: attachment; filename=\"".$attachment['filename']."\"");
			}
			else
			{
				// display it
				header ("Content-Disposition: inline; filename=\"".$attachment['filename']."\"");
			}
			header("Expires: 0");
			// the next headers are for IE and SSL
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: public"); 

			echo $attachment['attachment'];
			
			$GLOBALS['phpgw']->common->phpgw_exit();
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
			
			$this->t->set_var("bg01",$GLOBALS['phpgw_info']["theme"]["bg01"]);
			$this->t->set_var("bg02",$GLOBALS['phpgw_info']["theme"]["bg02"]);
			$this->t->set_var("bg03",$GLOBALS['phpgw_info']["theme"]["bg03"]);
		}
}

?>
