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
	/* $Id: class.bocompose.inc.php 17993 2007-02-24 21:42:10Z sigurdne $ */

	class bocompose
	{
		var $public_functions = array
		(
			'addAtachment'	=> True,
			'action'	=> True
		);
		
		var $attachments;	// Array of attachments
		var $preferences;	// the prefenrences(emailserver, username, ...)

		function bocompose($_composeID = '')
		{
			$this->bopreferences	= CreateObject('felamimail.bopreferences');
			$this->preferences = $this->bopreferences->getPreferences();
			
			if (!empty($_composeID))
			{
				$this->composeID = $_composeID;
				$this->restoreSessionData();
			}
		}
		
		function addAttachment($_formData)
		{
			$this->sessionData['to']	= $_formData['to'];
			$this->sessionData['cc']	= $_formData['cc'];
			$this->sessionData['bcc']	= $_formData['bcc'];
			$this->sessionData['reply_to']	= $_formData['reply_to'];
			$this->sessionData['subject']	= $_formData['subject'];
			$this->sessionData['body']	= $_formData['body'];
			$this->sessionData['priority']	= $_formData['priority'];
			$this->sessionData['signature'] = $_formData['signature'];
			
			#while(list($key,$value) = each($GLOBALS['phpgw_info']['user']))
			#{
			#	print "$key: $value<br>";
			#}
			
			if ($_formData['size'] != 0)
			{
				// ensure existance of PHPGROUPWARE temp dir
				// note: this is different from apache temp dir, 
				// and different from any other temp file location set in php.ini
				if (!file_exists($GLOBALS['phpgw_info']['server']['temp_dir']))
				{
					@mkdir($GLOBALS['phpgw_info']['server']['temp_dir'],0700);
				}
				
				// if we were NOT able to create this temp directory, then make an ERROR report
				if (!file_exists($GLOBALS['phpgw_info']['server']['temp_dir']))
				{
					$alert_msg .= 'Error:'.'<br>'
						.'Server is unable to access phpgw tmp directory'.'<br>'
						.$phpgw_info['server']['temp_dir'].'<br>'
						.'Please check your configuration'.'<br>'
						.'<br>';
				}
				
				// sometimes PHP is very clue-less about MIME types, and gives NO file_type
				// rfc default for unknown MIME type is:
				$mime_type_default = 'application/octet-stream';
				// so if PHP did not pass any file_type info, then substitute the rfc default value
				if (trim($_formData['type']) == '')
				{
					$_formData['type'] = $mime_type_default;
				}
				
				$tmpFileName = $GLOBALS['phpgw_info']['server']['temp_dir'].
					SEP.
					$GLOBALS['phpgw_info']['user']['account_id'].
					$this->composeID.
					basename($_formData['file']);
				move_uploaded_file($_FILES['attachfile']['tmp_name'],$tmpFileName);
				
				$this->sessionData['attachments'][]=array
				(
					'name'	=> $_formData['name'],
					'type'	=> $_formData['type'],
					'file'	=> $tmpFileName,
					'size'	=> $_formData['size']
				);
			}
			
			$this->saveSessionData();
			#print"<pre>";print_r($this->sessionData);print"</pre>";
		}
		
		function getAttachmentList()
		{
		}
		
		// create a hopefully unique id, to keep track of different compose windows
		// if you do this, you are creating a new email
		function getComposeID()
		{
			mt_srand((float) microtime() * 1000000);
			$this->composeID = mt_rand (100000, 999999);
			
			$this->setDefaults();
			
			return $this->composeID;
		}
		
		function getErrorInfo()
		{
			if(isset($this->errorInfo))
			{
				$errorInfo = $this->errorInfo;
				unset($this->errorInfo);
				return $errorInfo;
			}
			return false;
		}
		
		function getForwardData($_uid)
		{
			$bofelamimail    = CreateObject('felamimail.bofelamimail');
			$bofelamimail->openConnection();
			
			// get message headers for specified message
			$headers	= $bofelamimail->getMessageHeader($_uid);
			
			// check for Re: in subject header
			$this->sessionData['subject'] = "[FWD: " . $bofelamimail->decode_header($headers->Subject)."]";

			#$structure     = $bofelamimail->getMessageStructure($_uid, ST_UID);
			#if(sizeof($structure->parts) > 1)
			#{
			#	$sections = $bofelamimail->parse($structure);
			#	$attachments = $bofelamimail->get_attachments($sections);
			#}
			
			$this->sessionData['body']	 = "                  -----------".lang('Forwarded Message')."-----------\n\n";
			$this->sessionData['body']	.= lang('subject').": ".$bofelamimail->decode_header($headers->Subject)."\n";
			$this->sessionData['body']	.= lang('to').": ".$bofelamimail->decode_header(isset($headers->toaddress)?$headers->toaddress:'')."\n";
			$this->sessionData['body']	.= lang('from').": ".$bofelamimail->decode_header($headers->fromaddress)."\n\n";
		
			// iterate through message parts
			// get the body
			$bodyParts = $bofelamimail->getMessageBody($_uid, 'only_if_no_text');
			for($i=0; $i<count($bodyParts); $i++)
			{
		//		$this->sessionData['body']	.= $bodyParts[$i]['body'];
				$this->sessionData['body'] .= htmlentities( $bodyParts[$i]['body'],ENT_COMPAT,'UTF-8');
			}
		
			$this->sessionData['body']	 .= "\n\n                  -----------".lang('Forwarded Message')."-----------\n\n";
					
			$attachments = $bofelamimail->getMessageAttachments($_uid);
			if(is_array($attachments))
			{
				// ensure existance of PHPGROUPWARE temp dir
				// note: this is different from apache temp dir, 
				// and different from any other temp file location set in php.ini
				if (!file_exists($GLOBALS['phpgw_info']['server']['temp_dir']))
				{
					@mkdir($GLOBALS['phpgw_info']['server']['temp_dir'],0700);
				}
				
				// if we were NOT able to create this temp directory, then make an ERROR report
				if (!file_exists($GLOBALS['phpgw_info']['server']['temp_dir']))
					{
					$alert_msg .= 'Error:'.'<br>'
						.'Server is unable to access phpgw tmp directory'.'<br>'
						.$phpgw_info['server']['temp_dir'].'<br>'
						.'Please check your configuration'.'<br>'
						.'<br>';
					}

				while(list($partID, $partData) = each($attachments))
				{
					$attachmentData = $bofelamimail->getAttachment($_uid, $partID);
					#_debug_array($attachmentData);
					
					$tmpFileName = $GLOBALS['phpgw_info']['server']['temp_dir'].
						SEP.
						$GLOBALS['phpgw_info']['user']['account_id'].
						$this->composeID.
						basename($attachmentData['filename']);
				
					if ($handle = fopen($tmpFileName, 'w')) 
					{
						fwrite($handle, $attachmentData['attachment']);
						fclose($handle);
				
						$this->sessionData['attachments'][]=array
						(
							'name'	=> $attachmentData['filename'],
							'type'	=> $attachmentData['type'],
							'file'	=> $tmpFileName,
							'size'	=> filesize($tmpFileName)
						);
						
				}
			}
			}
					
			$bofelamimail->closeConnection();
			
			$this->saveSessionData();
		}

		// $_mode can be:
		// single: for a reply to one address
		// all: for a reply to all
		function getReplyData($_mode, $_uid)
		{
			$bofelamimail    = CreateObject('felamimail.bofelamimail');
			$bofelamimail->openConnection();
			
			// get message headers for specified message
			$headers	= $bofelamimail->getMessageHeader($_uid);

			$this->sessionData['uid'] = $_uid;
			
			// check for Reply-To: header and use if available
			if($headers->reply_toaddress)
			{
				$this->sessionData['to'] = $bofelamimail->decode_header(trim($headers->reply_toaddress));
			}
			else
			{
				$this->sessionData['to'] = $bofelamimail->decode_header(trim($headers->fromaddress));
			}
			
			if($_mode == 'all')
			{
				#_debug_array($this->preferences);
				// reply to any address which is cc, but not to my self
				$oldCC = $bofelamimail->decode_header(trim($headers->ccaddress));
				$addressParts = imap_rfc822_parse_adrlist($oldCC, '');
				if (count($addressParts)>0)
				{
					while(list($key,$val) = each($addressParts))
					{
						if($val->mailbox.'@'.$val->host == $this->preferences['emailAddress'])
						{
							continue;
						}
						if(!empty($this->sessionData['cc'])) $this->sessionData['cc'] .= ",";
						if(!empty($val->personal))
						{
							$this->sessionData['cc'] .= sprintf('"%s" <%s@%s>',
											$val->personal,
											$val->mailbox,
											$val->host);
						}
						else
						{
							$this->sessionData['cc'] .= sprintf("%s@%s",
											$val->mailbox,
											$val->host);
						}
					}
				}
				
				// reply to any address which is to, but not to my self
				$oldTo = $bofelamimail->decode_header(trim($headers->toaddress));
				$addressParts = imap_rfc822_parse_adrlist($oldTo, '');
				if (count($addressParts)>0)
				{
					while(list($key,$val) = each($addressParts))
					{
						if($val->mailbox.'@'.$val->host == $this->preferences['emailAddress'])
						{
							continue;
						}
						#print $val->mailbox.'@'.$val->host."<br>";
						if(!empty($this->sessionData['to'])) $this->sessionData['to'] .= ", ";
						if(!empty($val->personal))
						{
							$this->sessionData['to'] .= sprintf('"%s" <%s@%s>',
											$val->personal,
											$val->mailbox,
											$val->host);
						}
						else
						{
							$this->sessionData['to'] .= sprintf("%s@%s",
											$val->mailbox,
											$val->host);
						}
					}
				}
			}
		
			// check for Re: in subject header
			if(strtolower(substr(trim($headers->Subject), 0, 3)) == "re:")
			{
				$this->sessionData['subject'] = $bofelamimail->decode_header($headers->Subject);
			}
			else
			{
				$this->sessionData['subject'] = "Re: " . $bofelamimail->decode_header($headers->Subject);
			}

			#$structure = $bofelamimail->getMessageStructure($_uid);
			#if(sizeof($structure->parts) > 1)
			#{
			#	$sections = $bofelamimail->parse($structure);
			#	$attachments = $bofelamimail->get_attachments($sections);
			#}
			
			$this->sessionData['body']	= $bofelamimail->decode_header($headers->fromaddress) . " ".lang("wrote").": \n>";

			// get the body
			$bodyParts = $bofelamimail->getMessageBody($_uid, 'only_if_no_text');

			for($i=0; $i<count($bodyParts); $i++)
			{
				if(!empty($this->sessionData['body'])) $this->sessionData['body'] .= "\n\n";
				// add line breaks to $bodyParts
				$newBody        = explode("\n",$bodyParts[$i]['body']);
				
				// create it new, with good line breaks
				reset($newBody);
				while(list($key,$value) = @each($newBody))
				{
					$value .= "\n";
					$bodyAppend = wordwrap($value,70,"\n",1);
					$bodyAppend = str_replace("\n", "\n>", $bodyAppend);
					$this->sessionData['body'] .= htmlentities($bodyAppend,ENT_COMPAT,'UTF-8');
				}
			}
																
			$bofelamimail->closeConnection();
			
			$this->saveSessionData();
		}
		
		function getSessionData()
		{
			return $this->sessionData;
		}

		// get the user name, will will use for the FROM field
		function getUserName()
		{
			$retData = sprintf("%s <%s>",$this->preferences['realname'],$this->preferences['emailAddress']);
			return $retData;
		}
		
		function removeAttachment($_formData)
		{
			$this->sessionData['to']	= $_formData['to'];
			$this->sessionData['cc']	= $_formData['cc'];
			$this->sessionData['bcc']	= $_formData['bcc'];
			$this->sessionData['reply_to']	= $_formData['reply_to'];
			$this->sessionData['subject']	= $_formData['subject'];
			$this->sessionData['body']	= $_formData['body'];
			$this->sessionData['priority']	= $_formData['priority'];
			$this->sessionData['signature']	= $_formData['signature'];

			while(list($key,$value) = each($_formData['removeAttachments']))
			{
				#print "$key: $value<br>";
				unlink($this->sessionData['attachments'][$key]['file']);
				unset($this->sessionData['attachments'][$key]);
			}
			reset($this->sessionData['attachments']);
			
			// if it's empty, clear it totaly
			if (count($this->sessionData['attachments']) == 0) 
			{
				$this->sessionData['attachments'] = '';
			}
			
			$this->saveSessionData();
		}
		
		function restoreSessionData()
		{
			$this->sessionData = $GLOBALS['phpgw']->session->appsession('compose_session_data_'.$this->composeID);
			#print "bocompose after restore<pre>";print_r($this->sessionData);print"</pre>";
		}
		
		function saveSessionData()
		{
			$GLOBALS['phpgw']->session->appsession('compose_session_data_'.$this->composeID,'',$this->sessionData);
		}

		function send($_formData)
		{
			$bofelamimail    = CreateObject('felamimail.bofelamimail');
			
			$this->sessionData['to']	= $_formData['to'];
			$this->sessionData['cc']	= $_formData['cc'];
			$this->sessionData['bcc']	= $_formData['bcc'];
			$this->sessionData['reply_to']	= $_formData['reply_to'];
			$this->sessionData['subject']	= $_formData['subject'];
			$this->sessionData['body']	= $_formData['body'];
			$this->sessionData['priority']	= $_formData['priority'];
			$this->sessionData['signature']	= $_formData['signature'];

			$mail = CreateObject('phpgwapi.mailer_smtp');
			
			#print $this->sessionData['uid']."<bR>";
			#print $this->sessionData['folder']."<bR>";
			
			#_debug_array($_formData);
			#exit;
			
			#include(PHPGW_APP_ROOT . "/config/config.php");
				
			$mail->IsSMTP();
			$mail->From 	= $this->preferences['emailAddress'];
			$mail->FromName = $bofelamimail->encodeHeader($this->preferences['realname']);
			$mail->Host 	= $this->preferences['smtpServerAddress'];
			$mail->Priority = $this->sessionData['priority'];
			$mail->Encoding = '8bit';
			$mail->PluginDir = PHPGW_SERVER_ROOT."/phpgwapi/inc/phpmailer/";
			$mail->AddCustomHeader("X-Mailer: FeLaMiMail version 0.9.4");
			if(isset($this->preferences['organizationName']))
				$mail->AddCustomHeader("Organization: ".utf8_decode($this->preferences['organizationName']));

			if (!empty($this->sessionData['to']))
			{
				$address_array	= imap_rfc822_parse_adrlist($this->sessionData['to'],'');
				if(count($address_array)>0)
				{
					for($i=0;$i<count($address_array);$i++)
					{
						$emailAddress = $address_array[$i]->mailbox."@".$address_array[$i]->host;
						$emailName = isset($address_array[$i]->personal)?$bofelamimail->encodeHeader($address_array[$i]->personal):'';
						$mail->AddAddress($emailAddress,$emailName);
					}
				}
			}

			if (!empty($this->sessionData['cc']))
			{
				$address_array	= imap_rfc822_parse_adrlist($this->sessionData['cc'],'');
				if(count($address_array)>0)
				{
					for($i=0;$i<count($address_array);$i++)
					{
						$emailAddress = $address_array[$i]->mailbox."@".$address_array[$i]->host;
						$emailName = $bofelamimail->encodeHeader($address_array[$i]->personal);
						$mail->AddCC($emailAddress,$emailName);
					}
				}
			}
			
			if (!empty($this->sessionData['bcc']))
			{
				$address_array	= imap_rfc822_parse_adrlist($this->sessionData['bcc'],'');
				if(count($address_array)>0)
				{
					for($i=0;$i<count($address_array);$i++)
					{
						$emailAddress = $address_array[$i]->mailbox."@".$address_array[$i]->host;
						$emailName = $bofelamimail->encodeHeader($address_array[$i]->personal);
						$mail->AddBCC($emailAddress,$emailName);
					}
				}
			}
			
			if (!empty($this->sessionData['reply_to']))
			{
				$address_array	= imap_rfc822_parse_adrlist($this->sessionData['reply_to'],'');
				if(count($address_array)>0)
				{
					$emailAddress = $address_array[0]->mailbox."@".$address_array[0]->host;
					$emailName = $bofelamimail->encodeHeader($address_array[0]->personal);
					$mail->AddReplyTo($emailAddress,$emailName);
				}
			}
			
			$mail->WordWrap = 76;
			$mail->Subject = $bofelamimail->encodeHeader(utf8_decode($this->sessionData['subject']),'q');

			if(!isset($GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor']) || $GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor']=='none')
			{
				$mail->IsHTML(false);
			}
			else
			{
				$mail->IsHTML(true);
			}
			$mail->Body    = utf8_decode($this->sessionData['body']);
			if (!empty($this->sessionData['signature']))
			{
				$mail->Body	.= "\r\n--\r\n";
				$mail->Body	.= utf8_decode($this->sessionData['signature']);
			}
			if (isset($this->sessionData['attachments']) && is_array($this->sessionData['attachments']))
			{
				while(list($key,$value) = each($this->sessionData['attachments']))
				{
					$mail->AddAttachment
					(
						$value['file'],
						utf8_decode($value['name']),
						'base64',
						$value['type']
					);
				}
			}
			#$mail->AltBody = $this->sessionData['body'];
			
			if(isset($this->preferences['smtpPort']) && $this->preferences['smtpPort'])
			{
				$mail->Port = $this->preferences['smtpPort'];
			}
			// SMTP Auth??
			if($this->preferences['smtpAuth'] == 'yes')
			{
				$mail->SMTPAuth	= true;
				$mail->Username	= $this->preferences['smtpUser'];
				$mail->Password	= $this->preferences['smtpPassword'];
			}
			
			// set a higher timeout for big messages
			@set_time_limit(120);
			#$mail->SMTPDebug = 10;
			if(!$mail->Send())
			{
				$this->errorInfo = $mail->ErrorInfo;
				return false;
			}

			if (isset($this->preferences['sentFolder']))
			{
				// mark message as answered
				$bofelamimail = CreateObject('felamimail.bofelamimail');
				$bofelamimail->openConnection($this->preferences['sentFolder']);
				$bofelamimail->appendMessage($this->preferences['sentFolder'],$mail->sentHeader,$mail->sentBody);
				$bofelamimail->closeConnection();
			}

			if(isset($this->sessionData['uid']))
			{
				// mark message as answered
				$bofelamimail = CreateObject('felamimail.bofelamimail',isset($this->sessionData['folder'])?$this->sessionData['folder']:'');
				$bofelamimail->openConnection();
				$bofelamimail->flagMessages("answered",array('0' => $this->sessionData['uid']));
				$bofelamimail->closeConnection();
			}

			if(isset($this->sessionData['attachments']) && is_array($this->sessionData['attachments']))
			{
				reset($this->sessionData['attachments']);
				while(list($key,$value) = @each($this->sessionData['attachments']))
				{
					#print "$key: ".$value['file']."<br>";
					@unlink($value['file']);
				}
			}
			
			$this->sessionData = '';
			$this->saveSessionData();

			return true;
		}
		
		function setDefaults()
		{
			$this->sessionData['signature']	= $this->preferences['signature'];
			
			$this->saveSessionData();
		}
		
		function stripSlashes($_string) 
		{
			if (get_magic_quotes_gpc()) 
			{
				return stripslashes($_string);
			}
			else
			{
				return $_string;
			}
		}
                              

}
