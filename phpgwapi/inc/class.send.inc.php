<?php
	/**
	* SMTP mailer
	* @author Itzchak Rehberg <izzysoft@qumran.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000,2001 Itzchak Rehberg
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage communication
	* @version $Id$
	*/

	/**
	* SMTP mailer
	* 
	* @package phpgwapi
	* @package communication
	* This module should replace php's mail() function. It is fully syntax
	* compatible. In addition, when an error occures, a detailed error info
	* is stored in the array $send->err (see ../inc/email/global.inc.php for
	* details on this variable).
	*/
	class send
	{
		var $err    = array('code','msg','desc');
		var $to_res = array();
		var $errorInfo;

		function send()
		{
			$this->err['code'] = ' ';
			$this->err['msg']  = ' ';
			$this->err['desc'] = ' ';
		}

		function msg($service, $to, $subject, $body, $msgtype='', $cc='', $bcc='', $from='', $sender='', $content_type='', $boundary='Message-Boundary',$attachments=array(), $receive_notification = false)
		{
			if ($from == '')
			{
				$from = $GLOBALS['phpgw_info']['user']['fullname'].' <'.$GLOBALS['phpgw_info']['user']['preferences']['email']['address'].'>';
			}
			if ($sender == '')
			{
				$sender = $GLOBALS['phpgw_info']['user']['fullname'];
			}

			switch( $service )
			{
				case 'email':
					return $this->send_email($to, $subject, $body, $msgtype, $cc, $bcc, $from, $sender, $content_type, $boundary='Message-Boundary', $attachments, $receive_notification);
					break;
			}
		}

		function send_email($to, $subject, $body, $msgtype, $cc, $bcc, $from, $sender, $content_type, $ignored,$attachments, $receive_notification)
		{
			$smtp = createObject('phpgwapi.mailer_smtp');
			$from = str_replace(array('[',']'),array('<','>'),$from);
			$from_array = split('<', $from);
			unset($from);
			if ( count($from_array) == 2 )
			{
				$smtp->From = trim($from_array[1],'>');
				$smtp->FromName = $from_array[0];
			}
			else
			{
				$smtp->From = $from_array[0];
				$smtp->FromName = $sender;
			}
/*			
			if(strpos($to,','))
			{
				$delimiter = ',';
			}
			else
			{
				$delimiter = ';';
			}
*/
			$delimiter = ';';
			$to = explode($delimiter, $to);
			
			foreach ($to as $entry)
			{
				$entry = str_replace(array('[',']'),array('<','>'),$entry);
				$to_array = split('<', $entry);
				if ( count($to_array) == 2 )
				{
					$smtp->AddAddress(trim($to_array[1],'>'), $to_array[0]);
				}
				else
				{
					$smtp->AddAddress($to_array[0]);
				}
			}

			if($cc)
			{
/*
				if(strpos($cc,','))
				{
					$delimiter = ',';
				}
				else
				{
					$delimiter = ';';
				}
*/
				$delimiter = ';';
				$cc = explode($delimiter, $cc);
			
				foreach ($cc as $entry)
				{
					$entry = str_replace(array('[',']'),array('<','>'),$entry);
					$cc_array = split('<', $entry);
					if ( count($cc_array) == 2 )
					{
						$smtp->AddCC(trim($cc_array[1],'>'), $cc_array[0]);
					}
					else
					{
						$smtp->AddCC($cc_array[0]);
					}
				}
			}
			if($bcc)
			{
/*
				if(strpos($bcc,','))
				{
					$delimiter = ',';
				}
				else
				{
					$delimiter = ';';
				}
*/
				$delimiter = ';';
				$bcc = explode($delimiter, $bcc);
			
				foreach ($bcc as $entry)
				{
					$entry = str_replace(array('[',']'),array('<','>'),$entry);
					$bcc_array = split('<', $entry);
					if ( count($bcc_array) == 2 )
					{
						$smtp->AddBCC(trim($bcc_array[1],'>'), $bcc_array[0]);
					}
					else
					{
						$smtp->AddBCC($bcc_array[0]);
					}
				}
			}
			$smtp->IsSMTP();
			$smtp->Subject = $subject;
			$smtp->Body    = $body;
			$smtp->AddCustomHeader('X-Mailer: fmsystem (http://www.fmsystem.no)');
			if($receive_notification)
			{
				$smtp->AddCustomHeader("Disposition-Notification-To: {$smtp->From}");
			}

			if($content_type =='html')
			{
				$smtp->IsHTML(true);
			}
			else
			{
				$smtp->IsHTML(false);
				$smtp->WordWrap = 76;
			}

			if($attachments && is_array($attachments))
			{
				foreach($attachments as $key => $value)
				{
					$smtp->AddAttachment
					(
						$value['file'],
						utf8_decode($value['name']),
						'base64',
						$value['type']
					);
				}
			}

			// set a higher timeout for big messages
			@set_time_limit(120);
			#$smtp->SMTPDebug = 10;
			try
			{
				$smtp->Send();
			}
			catch (phpmailerException $e)
			{
				$this->errorInfo = $smtp->ErrorInfo;
				throw $e;
				return false;
			}

			return true;
		}
	
			// ==================================================[ some sub-functions ]===

		/**
		 * encode 8-bit chars in subject-line
		*
		 * @author ralfbecker
		 * the quoted subjects get a header stateing the charset (eg. "=?iso-8859-1?Q?"), the \
		 * 	8-bit chars as '=XX' (XX is the hex-representation of the char) and a trainling '?='.
		 */
		function encode_subject($subject)
		{
			$enc_start = $enc_end = 0;

			$words = explode(' ',$subject);
			foreach($words as $w => $word)
			{
				$str = '';

				for ($i = 0; $i < strlen($word); ++$i)
				{
					if (($n = ord($word[$i])) > 127 || $word[$i] == '=') {
						$str .= sprintf('=%0X',$n);
						if (!$enc_start)
						{
							$enc_start = $w+1;
						}
						$enc_end = $w+1;
					}
					else
					{
						$str .= $word[$i];
					}
				}
				$strs[] = $str;
				//echo "word='$word', start=$enc_start, end=$enc_end, encoded='$str'<br>\n";
			}
			if (!$enc_start)
			{
				return $subject;
			}
			$str = '';
			foreach ($strs as $w => $s)
			{
				$str .= $str != '' ? ' ' : '';

				if ($enc_start == $w+1)	// first word to encode
				{
					$str .= '=?iso-8859-1?Q?';
				}
				$str .= $w+1 > $enc_end ? str_replace('=3D','=',$s) : $s;

				if ($enc_end == $w+1)	// last word to encode
				{
					$str .= '?=';
				}
			}
			//echo "<p>send::encode_subject('$subject')='$str'</p>\n";
			return $str;
		}

		function socket2msg($socket)
		{
			$followme = '-';
			$this->err['msg'] = '';
			do
			{
				$rmsg = fgets($socket,255);
				// echo "< $rmsg<BR>\n";
				$this->err['code'] = substr($rmsg,0,3);
				$followme = substr($rmsg,3,1);
				$this->err['msg'] = substr($rmsg,4);
				if (substr($this->err["code"],0,1) != 2 && substr($this->err["code"],0,1) != 3)
				{
					$rc  = fclose($socket);
					return False;
				}
				if ($followme == ' ')
				{
					break;
				}
			}
			while ($followme == '-');
			return True;
		}

		function msg2socket($socket,$message)
		{
			// send single line\n
			// echo "raw> $message<BR>\n";
			// echo "hex> ".bin2hex($message)."<BR>\n";
			$rc = fputs($socket,"$message");
			if (!$rc)
			{
				$this->err['code'] = '420';
				$this->err['msg']  = 'lost connection';
				$this->err['desc'] = 'Lost connection to smtp server.';
				$rc  = fclose($socket);
				return False;
			}
			return True;
		}

		function put2socket($socket,$message)
		{
			// check for multiple lines 1st
			$pos = strpos($message,"\n");
			if (!is_int($pos))
			{
				// no new line found
				$message .= "\r\n";
				$this->msg2socket($socket,$message);
			}
			else
			{
				// multiple lines, we have to split it
				do
				{
					$msglen = $pos + 1;
					$msg = substr($message,0,$msglen);
					$message = substr($message,$msglen);
					$pos = strpos($msg,"\r\n");
					if (!is_int($pos))
					{
						// line not terminated
						$msg = chop($msg)."\r\n";
					}
					$pos = strpos($msg,'.');  // escape leading periods
					if (is_int($pos) && !$pos)
					{
						$msg = '.' . $msg;
					}
					if (!$this->msg2socket($socket,$msg))
					{
						return False;
					}
					$pos = strpos($message,"\n");
				}
				while (strlen($message)>0);
			}
			return True;
		}

		function check_header($subject,$header)
		{
			// check if header contains subject and is correctly terminated
			$header = chop($header);
			$header .= "\n";
			if (is_string($subject) && !$subject)
			{
				// no subject specified
				return $header;
			}
			$theader = strtolower($header);
			$pos  = strpos($theader,"\nsubject:");
			if (is_int($pos))
			{
				// found after a new line
				return $header;
			}
			$pos = strpos($theader,'subject:');
			if (is_int($pos) && !$pos)
			{
				// found at start
				return $header;
			}
			$pos = substr($subject,"\n");
			if (!is_int($pos))
			{
				$subject .= "\n";
			}
			$subject = 'Subject: ' .$subject;
			$header .= $subject;
			return $header;
		}

		function sig_html_to_text($sig)
		{
			// convert HTML chars for  '  and  "  in the email sig to normal text
			$sig_clean = $sig;
			$sig_clean = str_replace('&quot;', '"', $sig_clean);
			$sig_clean = str_replace('&#039;', '\'', $sig_clean);
			return $sig_clean;
		}

 // ==============================================[ main function: smail() ]===

		function smail($to,$subject,$message,$header)
		{
			$fromuser = $GLOBALS['phpgw_info']['user']['preferences']['email']['address'];
			$mymachine = $GLOBALS['phpgw_info']['server']['hostname'];
			// error code and message of failed connection
			$errcode = '';
			$errmsg = '';
			// timeout in secs
			$timeout = 5;

			// now we try to open the socket and check, if any smtp server responds
			$smtp_port = $GLOBALS['phpgw_info']['server']['smtp_port'] ? $GLOBALS['phpgw_info']['server']['smtp_port'] : 25;
			$socket = fsockopen($GLOBALS['phpgw_info']['server']['smtp_server'],$smtp_port,$errcode,$errmsg,$timeout);
			if (!$socket)
			{
				$this->err['code'] = '420';
				$this->err['msg']  = $errcode . ':' . $errmsg;
				$this->err['desc'] = 'Connection to '.$GLOBALS['phpgw_info']['server']['smtp_server'].':'.$GLOBALS['phpgw_info']['server']['smtp_port'].' failed - could not open socket.';
				return False;
			}
			else
			{
				$rrc = $this->socket2msg($socket);
			}

			// now we can send our message. 1st we identify ourselves and the sender
			$cmds = array (
				"\$src = \$this->msg2socket(\$socket,\"HELO \$mymachine\r\n\");",
				"\$rrc = \$this->socket2msg(\$socket);",
				"\$src = \$this->msg2socket(\$socket,\"MAIL FROM:<\$fromuser>\r\n\");",
				"\$rrc = \$this->socket2msg(\$socket);"
			);
			for ($src=True,$rrc=True,$i=0; $i<count($cmds);$i++)
			{
				eval ($cmds[$i]);
				if (!$src || !$rrc)
				{
					return False;
				}
			}

			// now we've got to evaluate the $to's
			$toaddr = explode(",",$to);
			$numaddr = count($toaddr);
			for ($i=0; $i<$numaddr; $i++)
			{
				$src = $this->msg2socket($socket,'RCPT TO:<'.$toaddr[$i].">\r\n");
				$rrc = $this->socket2msg($socket);
				// for lateron validation
				$this->to_res[$i]['addr'] = $toaddr[$i];
				$this->to_res[$i]['code'] = $this->err['code'];
				$this->to_res[$i]['msg']  = $this->err['msg'];
				$this->to_res[$i]['desc'] = $this->err['desc'];
			}

			//now we have to make sure that at least one $to-address was accepted
			$stop = 1;
			for ($i=0;$i<count($this->to_res);$i++)
			{
				$rc = substr($this->to_res[$i]['code'],0,1);
				if ($rc == 2)
				{
					// at least to this address we can deliver
					$stop = 0;
				}
			}
			if ($stop)
			{
				// no address found we can deliver to
				return False;
			}

			// now we can go to deliver the message!
			if (!$this->msg2socket($socket,"DATA\r\n"))
			{
				return False;
			}
			if (!$this->socket2msg($socket))
			{
				return False;
			}
			if ($header != "")
			{
				$header = $this->check_header($subject,$header);
				if (!$this->put2socket($socket,$header))
				{
					return False;
				}
				if (!$this->put2socket($socket,"\r\n"))
				{
					return False;
				}
			}
			$message  = chop($message);
			$message .= "\n";
			if (!$this->put2socket($socket,$message))
			{
				return False;
			}
			if (!$this->msg2socket($socket,".\r\n"))
			{
				return False;
			}
			if (!$this->socket2msg($socket))
			{
				return False;
			}
			if (!$this->msg2socket($socket,"QUIT\r\n"))
			{
				return False;
			}
			do
			{
				$closing = $this->socket2msg($socket);
			}
			while ($closing);
			return True;
		}
	} /* end of class */

