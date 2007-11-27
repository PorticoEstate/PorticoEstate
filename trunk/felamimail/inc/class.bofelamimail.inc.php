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
	/* $Id: class.bofelamimail.inc.php 18280 2007-09-22 14:19:46Z sigurdne $ */

	class bofelamimail
	{
		var $public_functions = array
		(
			'flagMessages'		=> True
		);

		var $mbox;		// the mailbox identifier any function should use

		// define some constants
		// message types
		var $type = array("text", "multipart", "message", "application", "audio", "image", "video", "other");
		
		// message encodings
		var $encoding = array("7bit", "8bit", "binary", "base64", "quoted-printable", "other");

		// set to true, if php is compiled with multi byte string support
		var $mbAvailable = FALSE;

		// what type of mimeTypes do we want from the body(text/html, text/plain)
		var $htmlOptions;

		function bofelamimail()
		{
			$this->restoreSessionData();
			
			// default mailbox INBOX
			$this->sessionData['mailbox'] = isset($this->sessionData['mailbox']) && $this->sessionData['mailbox'] ? $this->sessionData['mailbox'] : "INBOX";
			
			// set some defaults
	//		if(count($this->sessionData) == 0)
			{
				// this should be under user preferences
				// sessionData empty
				// no filter active
				$this->sessionData['activeFilter']	= isset($this->sessionData['activeFilter']) && $this->sessionData['activeFilter'] ? $this->sessionData['activeFilter'] : "-1";
		
				// default start message
				$this->sessionData['startMessage']	= isset($this->sessionData['startMessage']) && $this->sessionData['startMessage'] ? $this->sessionData['startMessage']: 1;
				// default mailbox for preferences pages
				$this->sessionData['preferences']['mailbox']	= isset($this->sessionData['preferences']['mailbox']) && $this->sessionData['preferences']['mailbox'] ? $this->sessionData['preferences']['mailbox'] : "INBOX";
				// default sorting
				if(!empty($GLOBALS['phpgw_info']['user']['preferences']['felamimail']['sortOrder']))
				{
					$this->sessionData['sort']	= $GLOBALS['phpgw_info']['user']['preferences']['felamimail']['sortOrder'];
				}
				else
				{
					$this->sessionData['sort']	= 6;
				}
				$this->saveSessionData();
			}
			
			$this->foldername	= isset($this->sessionData['mailbox'])?$this->sessionData['mailbox']:'';
			$this->accountid	= $GLOBALS['phpgw_info']['user']['account_id'];
			
			$this->bopreferences	= CreateObject('felamimail.bopreferences');
			$this->sofelamimail	= CreateObject('felamimail.sofelamimail');
			
			$this->mailPreferences	= $this->bopreferences->getPreferences();
			$this->imapBaseDir	= '';
			
			if (function_exists('mb_convert_encoding')) $this->mbAvailable = TRUE;

			$this->htmlOptions 	= $this->mailPreferences['htmlOptions'];
			
		}
		
		function appendMessage($_folder, $_header, $_body)
		{
			#print "<pre>$_header.$_body</pre>";
			$mailboxString = $this->createMailboxString($_folder);
			$header = str_replace("\n","\r\n",$_header);
			$body   = str_replace("\n","\r\n",$_body);
			#$result = @imap_append($this->mbox, $mailboxString, "$header"."$_body");
			$result = @imap_append($this->mbox, $mailboxString, "$header"."$body");
			#print imap_last_error();
			return $result;
		}
		
		function closeConnection()
		{
			@imap_close($this->mbox);
		}
		
		// creates the mailbox string needed for the various imap functions
		function createMailboxString($_folderName='')
		{
			switch($this->mailPreferences['imap_server_type'])
			{
				case "imap":
					$mailboxString = sprintf("{%s:%s}%s",
							$this->mailPreferences['imapServerAddress'],
							$this->mailPreferences['imapPort'],
							$_folderName);
					break;
					
				case "imaps-encr-only":
					$mailboxString = sprintf("{%s:%s/ssl/novalidate-cert}%s",
						$this->mailPreferences['imapServerAddress'],
						$this->mailPreferences['imapPort'],
						$_folderName);
					break;
					
				case "imaps-encr-auth":
					$mailboxString = sprintf("{%s:%s/ssl}%s",
						$this->mailPreferences['imapServerAddress'],
						$this->mailPreferences['imapPort'],
						$_folderName);
					break;

				case "imaptls":
					$mailboxString = sprintf("{%s:%s/tls/novalidate-cert}%s",
						$this->mailPreferences['imapServerAddress'],
						$this->mailPreferences['imapPort'],
						$_folderName);
					break;

			}

			return $this->encodeFolderName($mailboxString);
		}

		function compressFolder()
		{
			$prefs	= $this->bopreferences->getPreferences();

			$deleteOptions	= $prefs['deleteOptions'];
			$trashFolder	= $prefs['trash_folder'];
			
			if($this->sessionData['mailbox'] == $trashFolder && $deleteOptions == "move_to_trash")
			{
				// delete all messages in the trash folder
				#$mailboxString = sprintf("{%s:%s}%s",
				#		$this->mailPreferences['imapServerAddress'],
				#		$this->mailPreferences['imapPort'],
				#		imap_utf7_encode($this->sessionData['mailbox']));
				$mailboxString = $this->createMailboxString($this->sessionData['mailbox']);
				$status = imap_status ($this->mbox, $mailboxString, SA_ALL);
				$numberOfMessages = $status->messages;
				$msgList = "1:$numberOfMessages";
				imap_delete($this->mbox, $msgList);
				imap_expunge($this->mbox);
			}
			elseif($deleteOptions == "mark_as_deleted")
			{
				// delete all messages in the current folder which have the deleted flag set 
				imap_expunge($this->mbox);
			}
		}

		function decodeFolderName($_folderName)
		{
			if($this->mbAvailable)
			{
				return $this->ascii2utf(mb_convert_encoding( $_folderName, "ISO_8859-1", "UTF7-IMAP"));
			}
			
			// if not
			return $this->ascii2utf(imap_utf7_decode($_folderName));
		}

		function decode_header($string)
		{
			/* Decode from base64 form */
			if (preg_match_all("/\=\?(.*?)\?b\?(.*?)\?\=/i", $string, $matches, PREG_SET_ORDER))
			{
				for($i=0; $i < count($matches); $i++)
				{
					#print "Match 0:".$matches[$i][0]."<br>";
					#print "Match 1:".$matches[$i][1]."<br>";
					#print "Match 2:".$matches[$i][2]."<br>";
					$string = str_replace($matches[$i][0],base64_decode($matches[$i][2]),$string);
				}
				return $this->ascii2utf($string);
			}
			/* Decode from qouted printable */
			elseif (preg_match_all("/\=\?(.*?)\?q\?(.*?)\?\=/i", $string, $matches, PREG_SET_ORDER))
			{
				for($i=0; $i < count($matches); $i++)
				{
					#print "Match 0:".$matches[$i][0]."<br>";
					#print "Match 1:".$matches[$i][1]."<br>";
					#print "Match 2:".$matches[$i][2]."<br>";
					// replace any _ with " ". You define " " as " " or "_" in qouted printable
					$matches[$i][2] = str_replace("_"," ",$matches[$i][2]);
					switch($matches[$i][1])
					{
						case 'utf-8':
							$string = str_replace($matches[$i][0],utf8_decode(imap_qprint($matches[$i][2])),$string);
							break;
						default:
							$string = str_replace($matches[$i][0],imap_qprint($matches[$i][2]),$string);
							break;
					}
				}
				return $this->ascii2utf($string);
			}
			return $this->ascii2utf($string);
		}
		
		function utf2ascii($text = '')
		{	
			if ($text == utf8_decode($text))
			{
				return $text;
			}
			else
			{
				return utf8_decode($text);
			}
		}

		function ascii2utf($text = '')
		{	
			if ($text == utf8_encode($text))
			{
				return $text;
			}
			else
			{
				return utf8_encode($text);
			}
		}


		function deleteMessages($_messageUID)
		{
			$caching = CreateObject('felamimail.bocaching',
					$this->mailPreferences['imapServerAddress'],
					$this->mailPreferences['username'],
					$this->sessionData['mailbox']);

			reset($_messageUID);
			$msglist = '';
			while(list($key, $value) = each($_messageUID))
			{
				if(!empty($msglist)) $msglist .= ",";
				$msglist .= $value;
			}

			$prefs	= $this->bopreferences->getPreferences();

			$deleteOptions	= $prefs['deleteOptions'];
			$trashFolder	= $prefs['trash_folder'];

			if($this->sessionData['mailbox'] == $trashFolder && $deleteOptions == "move_to_trash")
			{
				$deleteOptions = "remove_immediately";
			}

			switch($deleteOptions)
			{
				case "move_to_trash":
					if(!empty($trashFolder))
					{
						if (imap_mail_move ($this->mbox, $msglist, $this->encodeFolderName($trashFolder), CP_UID))
						{
							imap_expunge($this->mbox);
							reset($_messageUID);
							while(list($key, $value) = each($_messageUID))
							{
								$caching->removeFromCache($value);
							}
						}
						else
						{
							print imap_last_error()."<br>";
						}
					}
					break;

				case "mark_as_deleted":
					imap_delete($this->mbox, $msglist, FT_UID);
					break;

				case "remove_immediately":
					imap_delete($this->mbox, $msglist, FT_UID);
					imap_expunge ($this->mbox);
					reset($_messageUID);
					while(list($key, $value) = each($_messageUID))
					{
						$caching->removeFromCache($value);
					}
					break;
			}
		}
		
		function encodeFolderName($_folderName)
		{
			if($this->mbAvailable)
			{
				return mb_convert_encoding( $_folderName, "UTF7-IMAP", "UTF-8" );
			}
			
			// if not
			return imap_utf7_encode($_folderName);
		}

		function encodeHeader($_string, $_encoding="q")
		{
			$retString = '';
			switch($_encoding)
			{
				case "q":
					if(!preg_match("/[\x80-\xFF]/",$_string))
					{
						// nothing to quote, only 7 bit ascii
						return $_string;
					}
					
					$string = imap_8bit($_string);
					$stringParts = explode("=\r\n",$string);
					while(list($key,$value) = each($stringParts))
					{
						if(!empty($retString)) $retString .= " ";
						$value = str_replace(" ","_",$value);
						// imap_8bit does not convert "?"
						// it does not need, but it should
						$value = str_replace("?","=3F",$value);
						$retString .= "=?ISO_8859-1?Q?".$value."?=";
					}
					#exit;
					return $retString;
					break;
				default:
					return $_string;
			}
		}
		function flagMessages($_flag, $_messageUID)
		{
			reset($_messageUID);
			$msglist = '';
			while(list($key, $value) = each($_messageUID))
			{
				if(!empty($msglist)) $msglist .= ",";
				$msglist .= $value;
			}

			switch($_flag)
			{
				case "flagged":
					$result = imap_setflag_full ($this->mbox, $msglist, "\\Flagged", ST_UID);
					break;
				case "read":
					$result = imap_setflag_full ($this->mbox, $msglist, "\\Seen", ST_UID);
					break;
				case "answered":
					$result = imap_setflag_full ($this->mbox, $msglist, "\\Answered", ST_UID);
					break;
				case "unflagged":
					$result = imap_clearflag_full ($this->mbox, $msglist, "\\Flagged", ST_UID);
					break;
				case "unread":
					$result = imap_clearflag_full ($this->mbox, $msglist, "\\Seen", ST_UID);
					$result = imap_clearflag_full ($this->mbox, $msglist, "\\Answered", ST_UID);
					break;
			}
			
			
			#print "Result: $result<br>";
		}
		
		// this function is based on a on "Building A PHP-Based Mail Client"
		// http://www.devshed.com
		// fetch a specific attachment from a message
		function getAttachment($_uid, $_partID)
		{
			// parse message structure
			$structure = imap_fetchstructure($this->mbox, $_uid, FT_UID);
			$sections = $this->parseMessage($structure);
			
			$type 		= $sections['attachment'][$_partID]["mimeType"];
			$encoding 	= $sections['attachment'][$_partID]["encoding"];
			$filename 	= $sections['attachment'][$_partID]["name"];
			
			$attachment = imap_fetchbody($this->mbox, $_uid, $_partID, FT_UID);
			
			switch ($encoding) 
			{
				case ENCBASE64:
					// use imap_base64 to decode
					$attachment = imap_base64($attachment);
					break;
				case ENCQUOTEDPRINTABLE:
					// use imap_qprint to decode
					$attachment = imap_qprint($attachment);
					break;
				case ENCOTHER:
					// not sure if this needs decoding at all
					break;
				default:
					// it is either not encoded or we don't know about it
			}
			
			return array(
				'type'	=> $type,
				'encoding'	=> $encoding,
				'filename'	=> $filename,
				'attachment'	=> $attachment
				);
		}

		function getFolderStatus($_folderName)
		{
			// now we have the keys as values
			$subscribedFolders = $this->getFolderList(true);
			#print_r($subscribedFolders);
			#print $subscribedFolders[$_folderName]." - $_folderName<br>";
			if(isset($subscribedFolders[$_folderName]))
			{
				$retValue['subscribed']	= true;
			}
			else
			{
				$retValue['subscribed'] = false;
			}
			
			return $retValue;
		}
		
		function getFolderList($_subscribedOnly=false)
		{
			$mailboxString = $this->createMailboxString($this->imapBaseDir);
		
			if($_subscribedOnly == 'true')
			{
				$list = @imap_getsubscribed($this->mbox,$mailboxString,"*");
			}
			else
			{
				$list = @imap_getmailboxes($this->mbox,$mailboxString,"*");
			}

			if(is_array($list))
			{
				// return always the inbox
				$folders['INBOX'] = 'INBOX';
				reset($list);
				while (list($key, $val) = each($list))
				{
					// remove the {host:port/imap/...} part
					$folderNameIMAP = $this->decodeFolderName(preg_replace("/{.*}/","",$val->name));
					$folderParts = explode(".",$folderNameIMAP);
					reset($folderParts);
					$displayName = "";
					#print_r($folderParts);print"<br>";
					for($i=0; $i<count($folderParts); $i++)
					{
						if($i+1 == count($folderParts))
						{
							$displayName .= $folderParts[$i];
						}
						else
						{
							$displayName .= ". . ";
						}
				}
					$folders["$folderNameIMAP"] = $displayName;
				}
				ksort($folders,SORT_STRING);
				return $folders;
			}
			else
			{
				if($_subscribedOnly == 'true' && 
					is_array(@imap_list($this->mbox,$mailboxString,'INBOX')))
				{
					$folders['INBOX'] = 'INBOX';
					return $folders;
				}
				return array();
			}
		}
		
		function getHeaders($_startMessage, $_numberOfMessages, $_sort)
		{
#			printf ("this->bofelamimail->getHeaders start: %s<br>",date("H:i:s",mktime()));

			$caching = CreateObject('felamimail.bocaching',
					$this->mailPreferences['imapServerAddress'],
					$this->mailPreferences['username'],
					$this->sessionData['mailbox']);
			$bofilter = CreateObject('felamimail.bofilter');
			$transformdate = CreateObject('felamimail.transformdate');

			$mailboxString = $this->createMailboxString($this->sessionData['mailbox']);
			$status = imap_status ($this->mbox, $mailboxString, SA_ALL);
			$cachedStatus = $caching->getImapStatus();

			// no data chached already?
			// get all message informations from the imap server for this folder
			if ($cachedStatus['uidnext'] == 0)
			{
				#print "nix gecached!!<br>";
				#print "current UIDnext :".$cachedStatus['uidnext']."<br>";
				#print "new UIDnext :".$status->uidnext."<br>";
				for($i=1; $i<=$status->messages; $i++)
				{
					@set_time_limit();
					$messageData['uid'] = imap_uid($this->mbox, $i);
					$header = imap_headerinfo($this->mbox, $i);
					// parse structure to see if attachments exist
					// display icon if so
					$structure = imap_fetchstructure($this->mbox, $i);
					if(is_array($structure->parameters))
					{
						$sections = $this->parseMessage($structure);
					}
					
					$messageData['date']		= $header->udate;
					$messageData['subject']		= (isset($header->subject)?$header->subject:'');
					$messageData['to_name']		= (isset($header->to[0]->personal)?$header->to[0]->personal:'');
					$messageData['to_address']	= (isset($header->to[0]->mailbox) && $header->to[0]->mailbox ? $header->to[0]->mailbox."@".$header->to[0]->host :'');
					$messageData['sender_name']	= (isset($header->from[0]->personal)?$header->from[0]->personal:'');
					$messageData['sender_address']	= $header->from[0]->mailbox."@".$header->from[0]->host;
					$messageData['size']		= $header->Size;
					
					$messageData['attachments']     = "false";
					if (isset($sections['attachment']) && is_array($sections['attachment']))
					{
						$messageData['attachments']	= "true";
					}
					
					// maybe it's already in the database
					// lets remove it, sometimes the database gets out of sync
					$caching->removeFromCache($messageData['uid']);
					
					$caching->addToCache($messageData);
					
					unset($messageData);
				}
				$caching->updateImapStatus($status);
			}
			// update cache, but only add new emails
			elseif($status->uidnext != $cachedStatus['uidnext'])
			{
				#print "found new messages<br>";
				#print "new uidnext: ".$status->uidnext." old uidnext: ".$cachedStatus['uidnext']."<br>";
				$uidRange = $cachedStatus['uidnext'].":".$status->uidnext;
				#print "$uidRange<br>";
				$newHeaders = imap_fetch_overview($this->mbox,$uidRange,FT_UID);
				for($i=0; $i<count($newHeaders); $i++)
				{
					$messageData['uid'] = $newHeaders[$i]->uid;
					$header = imap_headerinfo($this->mbox, $newHeaders[$i]->msgno);
					// parse structure to see if attachments exist
					// display icon if so
					$structure = imap_fetchstructure($this->mbox, $newHeaders[$i]->msgno);
					$sections = $this->parseMessage($structure);
				
					$messageData['date'] 		= $header->udate;
					$messageData['subject'] 	= $header->subject;
					$messageData['to_name']		= (isset($header->to[0]->personal)?$header->to[0]->personal:'');
					$messageData['to_address']	= $header->to[0]->mailbox."@".$header->to[0]->host;
					$messageData['sender_name'] 	= (isset($header->from[0]->personal)?$header->from[0]->personal:'');
					$messageData['sender_address'] 	= $header->from[0]->mailbox."@".$header->from[0]->host;
					$messageData['size'] 		= $header->Size;

					$messageData['attachments']     = "false";
					if (isset($sections['attachment']) && is_array($sections['attachment']))
					{
						$messageData['attachments']	= "true";
					}
					
					// maybe it's already in the database
					// lets remove it, sometimes the database gets out of sync
					$caching->removeFromCache($messageData['uid']);
					
					$caching->addToCache($messageData);
					
					unset($messageData);
				}
				$caching->updateImapStatus($status);
			}

			// now let's do some clean up
			// if we have more messages in the cache then in the imap box, some external 
			// imap client deleted some messages. It's better to erase the messages from the cache.
			$removedMessages = 0;
			$displayHeaders = $caching->getHeaders();
			if (count($displayHeaders) > $status->messages)
			{
				$messagesToRemove = count($displayHeaders) - $status->messages;
				reset($displayHeaders);
				for($i=0; $i<count($displayHeaders); $i++)
				{
					$header = imap_fetch_overview($this->mbox,$displayHeaders[$i]['uid'],FT_UID);
					if (@count($header[0]) == 0)
					{
						$caching->removeFromCache($displayHeaders[$i]['uid']);
						$removedMessages++;
					}
					if ($removedMessages == $messagesToRemove) break;
				}
			}

			// now lets gets the important messages
			$filterList = $bofilter->getFilterList();
			$activeFilter = $bofilter->getActiveFilter();
			$filter = (isset($filterList[$activeFilter])?$filterList[$activeFilter]:'');
			$displayHeaders = $caching->getHeaders($_startMessage, $_numberOfMessages, $_sort, $filter);

			$count=0;
			for ($i=0;$i<count($displayHeaders);$i++)
			{
				$header = imap_fetch_overview($this->mbox,$displayHeaders[$i]['uid'],FT_UID);
				#print $header[0]->date;print "<br>";
				#print_r($displayHeaders[$i]);print "<br>";
				#print_r($header);exit;

				#$rawHeader = imap_fetchheader($this->mbox,$displayHeaders[$i]['uid'],FT_UID);
				#$headers = $this->sofelamimail->fetchheader($rawHeader);
				
				$retValue['header'][$count]['subject'] = isset($header[0]->subject)?$this->decode_header($header[0]->subject):'';
				$retValue['header'][$count]['sender_name'] 	= $this->decode_header($displayHeaders[$i]['sender_name']);
				$retValue['header'][$count]['sender_address'] 	= $this->decode_header($displayHeaders[$i]['sender_address']);
				$retValue['header'][$count]['to_name'] 		= $this->decode_header($displayHeaders[$i]['to_name']);
				$retValue['header'][$count]['to_address'] 	= $this->decode_header($displayHeaders[$i]['to_address']);
				$retValue['header'][$count]['attachments']	= $displayHeaders[$i]['attachments'];
				$retValue['header'][$count]['size'] 		= $header[0]->size;

				$timestamp = $displayHeaders[$i]['date'];
				$timestamp7DaysAgo = 
					mktime(date("H"), date("i"), date("s"), date("m"), date("d")-7, date("Y"));
				$timestampNow = 
					mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
				// date from the future
				if($timestamp > $timestampNow)
				{
					$retValue['header'][$count]['date'] = date("Y-m-d",$timestamp);
				}
				// email from today, show only time
				elseif (date("Y-m-d") == date("Y-m-d",$timestamp))
				{
					$retValue['header'][$count]['date'] = date("H:i:s",$timestamp);
				}
				// email from the last 7 days, show only weekday
				elseif($timestamp7DaysAgo < $timestamp)
				{
					$retValue['header'][$count]['date'] = lang(date("l",$timestamp));
					#$retValue['header'][$count]['date'] = date("Y-m-d H:i:s",$timestamp7DaysAgo)." - ".date("Y-m-d",$timestamp);
					$retValue['header'][$count]['date'] = date("H:i:s",$timestamp)."(".lang(date("D",$timestamp)).")";
				}
				else
				{
					$retValue['header'][$count]['date'] = date("Y-m-d",$timestamp);
				}
				$retValue['header'][$count]['id'] = $header[0]->msgno;
				$retValue['header'][$count]['uid'] = $displayHeaders[$i]['uid'];
				$retValue['header'][$count]['recent'] = $header[0]->recent;
				$retValue['header'][$count]['flagged'] = $header[0]->flagged;
				$retValue['header'][$count]['answered'] = $header[0]->answered;
				$retValue['header'][$count]['deleted'] = $header[0]->deleted;
				$retValue['header'][$count]['seen'] = $header[0]->seen;
				$retValue['header'][$count]['draft'] = $header[0]->draft;
				
				$count++;
			}

#			printf ("this->bofelamimail->getHeaders done: %s<br>",date("H:i:s",mktime()));

			if(isset($retValue['header']) && is_array($retValue['header']))
			{
				$retValue['info']['total']	= $caching->getMessageCounter($filter);
				$retValue['info']['first']	= $_startMessage;
				$retValue['info']['last']	= $_startMessage + $count - 1 ;
				return $retValue;
			}
			else
			{
				return 0;
			}
		}

		function getMailPreferences()
		{
			return $this->mailPreferences;
		}
		
		function getMessageAttachments($_uid)
		{
			$structure = imap_fetchstructure($this->mbox, $_uid, FT_UID);
			$structure = $this->parseMessage($structure);
			if(isset($structure['attachment']) && is_array($structure['attachment']))
			{
				#_debug_array($structure['attachment']);
				return $structure['attachment'];
			}
			
			return false;

		}
		
		function getMessageBody($_uid, $_htmlOptions = '')
		{
			if($_htmlOptions != '')
				$this->htmlOptions = $_htmlOptions; 
			#'only_if_no_text';

			$structure = imap_fetchstructure($this->mbox, $_uid, FT_UID);
			$sections = $this->parseMessage($structure);
			if(is_array($sections['body']) && !isset($sections['body']['0']))
			{
				reset($sections['body']);
				while(list($key,$value) = each($sections['body']))
				{
					unset($newPart);
					#if(($value["mimeType"] == $wantedMimeType ))
					#{
						#$newPart = stripslashes(trim(imap_fetchbody($this->mbox, $_uid, $value["partID"], FT_UID)));
						$newPart = imap_fetchbody($this->mbox, $_uid, $value["partID"], FT_UID);

						#print $value['charset']."<br>";
					switch ($value['encoding']) 
					{
						case ENCBASE64:
							// use imap_base64 to decode
							$newPart = imap_base64($newPart);
							break;
						case ENCQUOTEDPRINTABLE:
							// use imap_qprint to decode
								switch(strtolower($value['charset']))
								{
									case 'utf-8':
										$newPart = utf8_decode(imap_qprint($newPart));
										break;
									case 'iso-8859-1':
										$newPart = utf8_encode(imap_qprint($newPart));
										break;
									default:
										$newPart = utf8_encode(imap_qprint($newPart));
										break;
								}
								break;
						case ENCOTHER:
							// not sure if this needs decoding at all
							break;
						default:
							// it is either not encoded or we don't know about it
					}
						
						$bodyPart[] = array('body' => $newPart,
								    'mimeType' => $value['mimeType']);
					#}
				}
			}
			else
			{
				#print imap_body($this->mbox, $_uid, FT_UID);
				#_debug_array($structure);
				switch ($structure->encoding) 
				{
					case ENCBASE64:
						// use imap_base64 to decode
						$newPart = stripslashes(trim(imap_body($this->mbox, $_uid, FT_UID)));
						$newPart = imap_base64($newPart);
						break;
					case ENCQUOTEDPRINTABLE:
						// use imap_qprint to decode
						$newPart = imap_body($this->mbox, $_uid, FT_UID);
						$newPart = quoted_printable_decode($newPart);
						break;
					case ENCOTHER:
						$newPart = stripslashes(trim(imap_body($this->mbox, $_uid, FT_UID)));
						// not sure if this needs decoding at all
						break;
					default:
					//	$newPart = stripslashes(trim(imap_body($this->mbox, $_uid, FT_UID)));
						$newPart = imap_body($this->mbox, $_uid, FT_UID);
						if(isset($structure->parameters) && !is_object($structure->parameters) && !is_object($structure->parameters[0]) && $structure->parameters['value'] != 'utf-8')
						{
							$newPart = utf8_encode($newPart);
						}
						else
						{
							$parameters = get_object_vars($structure->parameters); // fix this 
							
							if(!isset($parameters['value']) || strtolower($parameters['value'] != 'utf-8'))
							{
								$newPart = utf8_encode($newPart);
							}
						}
						// it is either not encoded or we don't know about it
				}
				if(strtolower($structure->subtype) == 'html')
				{
					$mimeType = 'text/html';
				}
				else
				{
					$mimeType = 'text/plain';
				}
				 
				$bodyPart[] = array('body' => $newPart,
						    'mimeType' => $mimeType);
			}
			
			return $bodyPart;
		}


		function getMessageHeader($_uid)
		{
			$msgno = imap_msgno($this->mbox, $_uid);
			return imap_header($this->mbox, $msgno);
		}

		function getMessageRawHeader($_uid)
		{
			return imap_fetchheader($this->mbox, $_uid, FT_UID);
		}

		function getMessageStructure($_uid)
		{
			return imap_fetchstructure($this->mbox, $_uid, FT_UID);
		}

		// return the qouta of the users INBOX
		function getQuotaRoot()
		{
			if(isset($this->storageQuota) && is_array($this->storageQuota))
			{
				return $this->storageQuota;
			}
			else
			{
				return false;
			}
		}
		
		function imap_createmailbox($_folderName, $_subscribe = False)
		{
			$mailboxString = $this->createMailboxString($_folderName);
			
			$result = @imap_createmailbox($this->mbox,$mailboxString);
			
			if($_subscribe)
			{
				return @imap_subscribe($this->mbox,$mailboxString);
			}
			
			return $result;
		}
		
		function imap_deletemailbox($_folderName)
		{
			$mailboxString = $this->createMailboxString($_folderName);
			
			$result = imap_deletemailbox($this->mbox, $mailboxString);
			
			#print imap_last_error();
			
			return $result;
		}

		function imapGetQuota($_username)
		{
			$quota_value = @imap_get_quota($this->mbox, "user.".$_username);

			if(is_array($quota_value) && count($quota_value) > 0)
			{
				return array('limit' => $quota_value['limit']/1024);
			}
			else
			{
				return false;
			}
		}		
		
		function imap_get_quotaroot($_folderName)
		{
			return @imap_get_quotaroot($this->mbox, $_folderName);
		}
		
		function imap_renamemailbox($_oldMailboxName, $_newMailboxName)
		{
			if(strcasecmp("inbox",$_oldMailboxName) == 0 || strcasecmp("inbox",$_newMailboxName) == 0)
			{
				return False;
			}
			
			$oldMailboxName = $this->createMailboxString($_oldMailboxName);
			$newMailboxName = $this->createMailboxString($_newMailboxName);
			
			$result =  @imap_renamemailbox($this->mbox,$oldMailboxName, $newMailboxName);
			
			#print imap_last_error();
			
			return $result;
		}
		
		function imapSetQuota($_username, $_quotaLimit)
		{
			if(is_numeric($_quotaLimit) && $_quotaLimit >= 0)
			{
				// enable quota
				$quota_value = imap_set_quota($this->mbox, "user.".$_username, $_quotaLimit*1024);
			}
			else
			{
				// disable quota
				$quota_value = imap_set_quota($this->mbox, "user.".$_username, -1);
			}
		}
		
		function moveMessages($_foldername, $_messageUID)
		{
			$caching = CreateObject('felamimail.bocaching',
					$this->mailPreferences['imapServerAddress'],
					$this->mailPreferences['username'],
					$this->sessionData['mailbox']);
			$deleteOptions  = $GLOBALS['phpgw_info']["user"]["preferences"]["felamimail"]["deleteOptions"];

			reset($_messageUID);
			$msglist = '';
			while(list($key, $value) = each($_messageUID))
			{
				if(!empty($msglist)) $msglist .= ",";
				$msglist .= $value;
			}
			#print $msglist."<br>";
			
			#print "destination folder($_folderName): ".$this->encodeFolderName($_foldername)."<br>";
			
			if (imap_mail_move ($this->mbox, $msglist, $this->encodeFolderName($_foldername), CP_UID))
			{
				#print "allet ok<br>";
				if($deleteOptions != "mark_as_deleted")
				{
					imap_expunge($this->mbox);
					reset($_messageUID);
					while(list($key, $value) = each($_messageUID))
					{
						$caching->removeFromCache($value);
					}
				}
			}
			else
			{
				print imap_last_error()."<br>";
			}
			
		}

		function openConnection($_folderName='', $_options=0, $_adminConnection=false)
		{
			if($_folderName == '')
			{
				$_folderName = $this->sessionData['mailbox'];
			}

			if($_adminConnection)
			{
				$config = CreateObject('phpgwapi.config','qmailldap');
				$config->read_repository();
				$qmailldapConfig = $config->config_data;
				
				$folderName	= '';
				$username	= $qmailldapConfig['imapAdminUser'];
				$password	= $qmailldapConfig['imapAdminPassword'];
				$options	= '';
			}
			else
			{
				$folderName	= $_folderName;
				$username	= $this->mailPreferences['username'];
				$password	= $this->mailPreferences['key'];
				$options	= $_options;
			}		

			$mailboxString = $this->createMailboxString($_folderName);
			                                                                                        
			if(!$this->mbox = @imap_open ($mailboxString, $username, $password, $options))
			{
				return imap_last_error();
			}
			else
			{
				// get the quota for this mailboxbox
				if (function_exists('imap_get_quotaroot'))
				{
					$quota = @imap_get_quotaroot($this->mbox, $_folderName);
					if(isset($quota['STORAGE']) && is_array($quota['STORAGE'])) 
					{
						$storage = $this->storageQuota = $quota['STORAGE'];
					}
				}
				return True;
			}
			
		}
		
		function parseMessage($_structure, $_partID = '')
		{
			//if ($_partID == '') _debug_array($_structure);
			
			switch ($_structure->type)
			{
				case TYPETEXT:
					#print "found text $_partID<br>";
					$mime_type = "text";
					$data['encoding']	= $_structure->encoding;
					$data['size']		= (isset($_structure->bytes)?$_structure->bytes:'');
					$data['partID']	= $_partID;
					$data["mimeType"]	= $mime_type."/". strtolower($_structure->subtype);
					$data["name"]		= lang("unknown");
					if(isset($_structure->parameters) && is_array($_structure->parameters))
					{
						$cnt_params =  count($_structure->parameters);
						for ($lcv = 0; $lcv < $cnt_params; ++$lcv)
						{
							$param = $_structure->parameters->$lcv;
							switch(strtolower($param->attribute))
							{
								case 'name':
									$data["name"] = $param->value;
										break;
								case 'charset':
									$data["charset"] = $param->value;
										break;
							}
						}
					}
					
					// set this to zero, when we have a plaintext message
					// if partID[0] is set, we have no attachments
					if($_partID == '') $_partID = '0';
					
					if (isset($_structure->disposition) && strtolower($_structure->disposition) == "attachment" ||
						$data["name"] != lang("unknown"))
					{
						#print "found a attachment<br>";
						// must be a attachment
						$retData['attachment'][$_partID] = $data;
					}
					else
					{
						#print "found a body part $_partID<br>";
						// must be a body part
						$retData['body']["$_partID"] = $data;
						$retData['body']["$_partID"]['name'] = lang('body part')." $_partID";
					}
					#print "<hr>";
					#_debug_array($retData);
					#print "<hr>";
								break;
					
							case TYPEMULTIPART:
					#print "found multipart $_partID<br>";
					// lets cycle trough all parts
					if($_partID != '') $_partID .= '.';
					$lastPartID = 0;
					for($i = 0; $i < count($_structure->parts); $i++)
					{
						
						$structureData = $this->parseMessage($_structure->parts[$i], $_partID.($i+1));
						if(isset($structureData['body']) && is_array($structureData['body']))
						{
							reset($structureData['body']);
							while(list($partID,$partData) = each($structureData['body']))
							{
								if(strtolower($_structure->subtype) == 'alternative')
								{
									switch($this->htmlOptions)
									{
										case 'always_display':
											$allowedMimeType = 
												array('text/plain' => 1,
											      'text/html'  => 1);
											$orderOfMimeType = 
												array('text/html'  => 2,
												      'text/plain' => 1);
											break;
						
										case 'only_if_no_text':
											$allowedMimeType = 
												array('text/plain' => 1,
												      'text/html'  => 1);
											$orderOfMimeType = 
												array('text/plain' => 2,
												      'text/html'  => 1);
											break;
										default:
											$allowedMimeType = 
											array("text/plain" => 1);
											$orderOfMimeType = 
											array("text/plain" => 1);
											break;
									}
									// add only allowed mime types to the list
									if(isset($allowedMimeType[$partData['mimeType']]) && $allowedMimeType[$partData['mimeType']])
									{
										// now let only the prefered part one survive
										#print $orderOfMimeType[$partData['mimeType']]."<br>".
										#$partData['mimeType']."<br>".
										#$lastPartID."<br>";
										if($orderOfMimeType[$partData['mimeType']] > $lastPartID)
										{
											if(isset($retData['body'][$lastPartID]))
											{
												unset($retData['body'][$lastPartID]);
											}
											$retData['body'][$partID] = $partData;
											$lastPartID = $partID;
										}
									}
								} 
								else 
								{
									$retData['body'][$partID] = $partData;
								}
							}
						}
						if(isset($structureData['attachment']) && is_array($structureData['attachment']))
						{
							reset($structureData['attachment']);
							while(list($partID,$partData) = each($structureData['attachment']))
							{
								$retData['attachment'][$partID] = $partData;
							}
						}
					}
					break;
						case TYPEMESSAGE:
					#print "found message $_partID<br>";
							$mime_type = "message";
					$retData['attachment'][$_partID]['encoding']	= $_structure->encoding;
					$retData['attachment'][$_partID]['size']	= $_structure->bytes;
					$retData['attachment'][$_partID]['partID']	= $_partID;
					$retData['attachment'][$_partID]["mimeType"]	= $mime_type."/". strtolower($_structure->subtype);
					$retData['attachment'][$_partID]["name"]	= lang("unknown");
					if(!empty($_structure->description))
					{
						$retData['attachment'][$_partID]["name"] = lang($_structure->description);
					}
					break;
					
				case TYPEAPPLICATION:
					$mime_type = "application";
					$retData['attachment'][$_partID]['encoding']	= $_structure->encoding;
					$retData['attachment'][$_partID]['size']	= $_structure->bytes;
					$retData['attachment'][$_partID]['partID']	= $_partID;
					$retData['attachment'][$_partID]["mimeType"]	= $mime_type."/". strtolower($_structure->subtype);
					$retData['attachment'][$_partID]["name"]	= lang("unknown");
					if(isset($_structure->dparameters) && is_array($_structure->dparameters))
					{
						for ($lcv = 0; $lcv < count($_structure->dparameters); $lcv++)
						{
							$param = $_structure->dparameters[$lcv];
							switch(strtolower($param->attribute))
							{
								case 'filename':
									$retData['attachment'][$_partID]["name"] = $param->value;
									break;
							}
						}
					}
					break;
					
				case TYPEAUDIO:
					$mime_type = "audio";
					$retData['attachment'][$_partID]['encoding']	= $_structure->encoding;
					$retData['attachment'][$_partID]['size']	= $_structure->bytes;
					$retData['attachment'][$_partID]['partID']	= $_partID;
					$retData['attachment'][$_partID]["mimeType"]	= $mime_type."/". strtolower($_structure->subtype);
					$retData['attachment'][$_partID]["name"]	= lang("unknown");
					for ($lcv = 0; $lcv < count($_structure->dparameters); $lcv++)
					{
						$param = $_structure->dparameters[$lcv];
						switch(strtolower($param->attribute))
						{
							case 'filename':
								$retData['attachment'][$_partID]["name"] = $param->value;
								break;
						}
					}
					break;
					
				case TYPEIMAGE:
					#print "found image $_partID<br>";
					$mime_type = "image";
					$retData['attachment'][$_partID]['encoding']	= $_structure->encoding;
					$retData['attachment'][$_partID]['size']	= $_structure->bytes;
					$retData['attachment'][$_partID]['partID']	= $_partID;
					$retData['attachment'][$_partID]["mimeType"]	= $mime_type."/". strtolower($_structure->subtype);
					$retData['attachment'][$_partID]["name"]	= lang("unknown");
					if(isset($_structure->dparameters) && is_array($_structure->dparameters))
					{
						for ($lcv = 0; $lcv < count($_structure->dparameters); $lcv++)
						{
							$param = $_structure->dparameters[$lcv];
							switch(strtolower($param->attribute))
							{
								case 'filename':
									$retData['attachment'][$_partID]["name"] = $param->value;
									break;
							}
						}
					}
					break;
					
				case TYPEVIDEO:
					$mime_type = "video";
					$retData['attachment'][$_partID]['encoding']	= $_structure->encoding;
					$retData['attachment'][$_partID]['size']	= $_structure->bytes;
					$retData['attachment'][$_partID]['partID']	= $_partID;
					$retData['attachment'][$_partID]["mimeType"]	= $mime_type."/". strtolower($_structure->subtype);
					$retData['attachment'][$_partID]["name"]	= lang("unknown");
					for ($lcv = 0; $lcv < count($_structure->dparameters); $lcv++)
					{
						$param = $_structure->dparameters[$lcv];
						switch(strtolower($param->attribute))
						{
							case 'filename':
								$retData['attachment'][$_partID]["name"] = $param->value;
									break;
						}
					}
					break;
					
				case TYPEMODEL:
					$mime_type = "model";
					$retData['attachment'][$_partID]['encoding']	= $_structure->encoding;
					$retData['attachment'][$_partID]['size']	= $_structure->bytes;
					$retData['attachment'][$_partID]['partID']	= $_partID;
					$retData['attachment'][$_partID]["mimeType"]	= $mime_type."/". strtolower($_structure->subtype);
					$retData['attachment'][$_partID]["name"]	= lang("unknown");
					for ($lcv = 0; $lcv < count($_structure->dparameters); $lcv++)
					{
						$param = $_structure->dparameters[$lcv];
						switch(strtolower($param->attribute))
						{
							case 'filename':
								$retData['attachment'][$_partID]["name"] = $param->value;
							break;
						}
					}
					break;
					
				default:
					break;
				}

			#if ($_partID == '') _debug_array($retData);
			
			return $retData;
		}
		
		function restoreSessionData()
		{
			$this->sessionData = $GLOBALS['phpgw']->session->appsession('session_data');
		}
		
		function saveFilter($_formData)
		{
			if(!empty($_formData['from']))
				$data['from']	= $_formData['from'];
			if(!empty($_formData['to']))
				$data['to']	= $_formData['to'];
			if(!empty($_formData['subject']))
				$data['subject']= $_formData['subject'];
			if($_formData['filterActive'] == "true")
			{
				$data['filterActive']= "true";
			}

			$this->sessionData['filter'] = $data;
			$this->saveSessionData();
		}
		
		function saveSessionData()
		{
			$GLOBALS['phpgw']->session->appsession('session_data','',$this->sessionData);
		}
		
		function subscribe($_folderName, $_status)
		{
			#$this->mailPreferences['imapServerAddress']
			#$this->mailPreferences['imapPort'],
			
			$folderName = $this->encodeFolderName($_folderName);
			$folderName = "{".$this->mailPreferences['imapServerAddress'].":".$this->mailPreferences['imapPort']."}".$folderName;
			
			if($_status == 'unsubscribe')
			{
				return imap_unsubscribe($this->mbox,$folderName);
			}
			else
			{
				return imap_subscribe($this->mbox,$folderName);
			}
		}
		
		function toggleFilter()
		{
			if($this->sessionData['filter']['filterActive'] == 'true')
			{
				$this->sessionData['filter']['filterActive'] = 'false';
			}
			else
			{
				$this->sessionData['filter']['filterActive'] = 'true';
			}
			$this->saveSessionData();
		}
		
		function validate_email($_emailAddress)
		{
			if($val != "")
			{
				$pattern = "/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/";
				if(preg_match($pattern, $val))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
	}
?>
