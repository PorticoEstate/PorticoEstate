<?php
  /**************************************************************************\
  * phpGroupWare app (NNTP)                                               *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <mpeters@satx.rr.com>                             *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: class.mail.inc.php 15913 2005-05-05 14:59:34Z powerstat $ */
	$d1 = strtolower(substr(PHPGW_APP_INC,0,3));
	$d2 = strtolower(substr(PHPGW_API_INC,0,3));
	if($d1 == 'htt' || $d1 == 'ftp' || $d2 == 'htt' || $d2 == 'ftp')
	{
		echo 'Failed attempt to break in via an old Security Hole!<br>'."\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	unset($d1);unset($d2);

	/*
	// description
	(i)nternal/
	(e)xternal
	function name
	------------------------------------------------------------------------------
	// Initializer Can't have contructors in base classes
	* E function init($type='')

	// Set error codes
	* I function set_error($code,$msg,$desc)

	// Open port to server
	* E function open_port($server,$port,$timeout)

	// Read port and return response
	* I function read_port()

	// Write to port
	* I function write_port($str)

	// write to port and evaluate response for expected result
	* I function msg2socket($str,$expected_response,$response)

	// Open mailbox
	* E function mail_open($server,$port,$user,$pass)

	// Only valid for NNTP
	* E function mode_reader()

	// Return number of messages for associated mailbox
	* E function mail_num_msg()

	// Returns first message for associated mailbox (NNTP only)
	* E function mail_first_msg()

	// Returns last message for associated mailbox (NNTP only)
	* E function mail_last_msg()

	// Splits the line of text and saves the $key, $value to the structure
	* I function create_header($line,&$header)

	// Read an overview of the information in the headers of the given msgnum
	// into an array
	* E function mail_fetch_overview($msgnum)

	// Build address structure
	* I function build_address_structure($key)

	// Read the header of the message and populate the msg structure
	* E function mail_header($msgnum)

	// An alias for mail_header
	* E function mail_headerinfo($msgnum)

	// Read the message body
	* E function mail_body($msgnum)

	// An alias for mail_body
	* E function mail_fetchtext($msgnum)

	// Determines if message is uu encoded and decodes
	* I function is_uu_encoded($body)

	// Determines the boundary for mime encoded messages
	* I function parse_boundary($header)

	// Determines how many mime attachments
	* I function how_many_mime_attchs($body)

	// Determines if message is mime encoded
	* I function is_mime_encoded($body)

	// Determines the content type of mime encoded parts
	* I function get_content_type($header,$struct)

	// Determines the encoding type of mime encoded parts
	* I function get_encoding_type($header,$struct)

	// Build the $struct type from the specified header and body
	* I function build_mime_structure($header,$struct)

	// This splits the body of the mime message into the sub-parts
	* I function split_mime_msg_parts($body,$boundary,$part,&$sub_header,
	&$sub_body)

	// Slowly being built...
	//
	// Still need to write the routine to split the uuencoded branch...
	//
	// I really need to study up on the layout of the structure...
	//
	// This will call mail_body(), if not previously called, and populate
	// the structure from the stream of text
	* E function mail_fetchstructure($msgnum)
	*/

	class parameter
	{
		var $attribute;
		var $value;
	}

	class struct
	{
		var $encoding;
		var $type;
		var $subtype;
		var $ifsubtype;
		var $parameters;
		var $ifparameters;
		var $description;
		var $ifdescription;
		var $disposition;
		var $ifdisposition;
		var $id;
		var $ifid;
		var $lines;
		var $bytes;
	}

	class address
	{
		var $personal;
		var $mailbox;
		var $host;
		var $adl;
	}

	class msg
	{
		var $from;
		var $fromaddress;
		var $to;
		var $toaddress;
		var $cc;
		var $ccaddress;
		var $bcc;
		var $bccaddress;
		var $reply_to;
		var $reply_toaddress;
		var $sender;
		var $senderaddress;
		var $return_path;
		var $return_pathaddress;
		var $udate;
		var $subject;
		var $lines;
	}

	CreateObject('phpgwapi.network');

	class mail extends network
	{
		var $decode;
		var $type;
		var $header=array();
		var $msg;
		var $struct;
		var $body;
		var $mailbox;
		var $numparts;

		var $sparts;
		var $hsub=array();
		var $bsub=array();

		function mail($type='')
		{
			$this->errorset = 0;
			if ($type == '')
			{
				return $this->set_error('Programming error',
					'Improper Intiailization',
					'Please consult manual for instructions on use of MAIL.INC.PHP');
			}
			else
			{
				$this->network(True);
				$this->decode = CreateObject('nntp.decode');
				$this->type = $type;
				return 1;
			}
		}

		function mode_reader()
		{
			return $this->msg2socket('mode reader',"^20[01]",$response);
		}

		function mail_open($server,$port,$user='',$pass='')
		{
			if (!$this->open_port($server,$port,15))
			{
				return 0;
			}
			$this->read_port();

			if ($user <> '' && $pass <> '')
			{
				if (!$this->msg2socket('authinfo user '.$user,"^381",$response))
				{
					return 0;
				}
				if (!$this->msg2socket('authinfo pass '.$pass,"^281",$response))
				{
					return 0;
				}
			}
			if (!$this->mode_reader())
			{
				return 0;
			}
			if(isset($this->mailbox) && $this->mailbox)
			{
				return $this->msg2socket('group '.$this->mailbox,"^211",$response);
			}
			else
			{
				return 1;
			}
		}

		function mail_num_msg()
		{
			$str = 'group '.$this->mailbox;
			$expected_response = "^211";

			if (!$this->msg2socket($str,$expected_response,$response))
			{
				return 0;
			}

			$temp_array = explode(' ',$response);

			return $temp_array[1];
		}

		function mail_first_msg()
		{
			if ($this->type == 'nntp')
			{
				if (!$this->msg2socket('group '.$this->mailbox,"^211",$response))
				{
					return 0;
				}
				$temp_array = array();
				$temp_array = explode(' ',$response);

				return $temp_array[2];
			}
		}

		function mail_last_msg()
		{
			if ($this->type == 'nntp')
			{
				if (!$this->msg2socket('group '.$this->mailbox,"^211",$response))
				{
					return 0;
				}
				$temp_array = array();
				$temp_array = explode(' ',$response);

				return $temp_array[3];
			}
		}

		function create_header($line,&$header,$line2='')
		{
			$thead = explode(':',$line);
			$key = trim($thead[0]);
			switch(count($thead))
			{
				case 1:
					$value = TRUE;
					break;
				case 2:
					$value = trim($thead[1]);
					break;
				default: 
					$thead[0] = '';
					$value = '';
					for($i=1,$j=count($thead);$i<$j;$i++)
					{
						$value .= $thead[$i].':';
					}
					//	$value = trim($value.$thead[$j++]);
					//	$value = trim($value);
					break;
			}
			$header[$key] = $value;
			if (ereg("^multipart/mixed;",$value))
			{
				if (! ereg('boundary',$header[$key]))
				{
					if ($line2 == 'True') $line2 = $this->read_port();
					{
						$header[$key] .= chop($line2);
					}
				}
			}
			//    echo 'Header['.$key.'] = '.$header[$key].'<br>'."\n";
		}

		function mail_fetch_overview($msgnum)
		{
			switch (strtolower($this->type))
			{
				case 'pop':
					$str = 'top '.$msgnum;
					$expected_response = "^+OK";
					break;
				case 'nntp':
					$str = 'HEAD '.$msgnum;
					$expected_response = "^221";
					break;
			}

			if (!$this->msg2socket($str,$expected_response,$response))
			{
				return 0;
			}

			while ($line = $this->read_port())
			{
				if (chop($line) == '.' || chop($line) == '')
				{
					break;
				}
				$this->create_header($line,$this->header,'True');
			}
			if ($this->type == 'pop')
			{
				$lines = 0;
				while (chop($this->read_port()) <> '.')
				{
					$lines++;
				}
				$this->header['Lines'] = $lines;
			}
			return 1;
		}

		function build_address_structure($key)
		{
			$address = array(new address);
			// Build Address to Structure
			$temp_array = explode(';',$this->header[$key]);
			for ($i=0;$i<count($temp_array);$i++)
			{
				$this->decode->decode_author($temp_array[$i],$email,$name);
				$temp = explode('@',$email);
				$address[$i]->personal = $this->decode->decode_header($name);
				$address[$i]->mailbox = $temp[0];
				if (count($temp) == 2)
				{
					$address[$i]->host = $temp[1];
				}
				$address[$i]->adl = $email;
			}
			return $address;
		}

		function convert_date($msg_date)
		{
			// if date is of type "Sat, 15 Jul 2000 20:50:22 +0200"
			// strip to "15 Jul 2000 20:50:22 +0200"
			$comma_pos = strpos($msg_date,',');
			if($comma_pos)
			{
				$msg_date = substr($msg_date,$comma_pos+1);
			}

//			echo 'NNTP: class.mail.inc.php: convert_date = '.$msg_date.'<br>'."\n";
			
			// This may need to be a reference to the different months in native tongue....
			$month= array(
				'Jan' => 1,
				'Feb' => 2,
				'Mar' => 3,
				'Apr' => 4,
				'May' => 5,
				'Jun' => 6,
				'Jul' => 7,
				'Aug' => 8,
				'Sep' => 9,
				'Oct' => 10,
				'Nov' => 11,
				'Dec' => 12
			);
			$dta = array();
			$ta = array();

			// Convert "15 Jul 2000 20:50:22 +0200" to unixtime
			$dta = explode(' ',$msg_date);
			$ta = explode(':',$dta[4]);

			if(substr($dta[5],0,3) <> 'GMT')
			{
				$tzoffset = substr($dta[5],0,1);
				$tzhours = intval(substr($dta[5],1,2));
				$tzmins = intval(substr($dta[5],3,2));
				switch ($tzoffset)
				{
					case '+':
						(int)$ta[0] += $tzhours;
						(int)$ta[1] += $tzmins;
						break;
					case '-':
						(int)$ta[0] -= $tzhours;
						(int)$ta[1] -= $tzmins;
						break;
				}
			}
			echo 'NNTP: class.mail.inc.php: Date = ('.$dta[1].') ('.$month[$dta[2]].') ('.$dta[3].') TIME: ('.$ta[0].':'.$ta[1].':'.$ta[2].')<br>'."\n";
			return mktime($ta[0],$ta[1],$ta[2],$month[$dta[2]],$dta[1],$dta[3]);
		}

		function mail_header($msgnum)
		{
			$this->msg = new msg;
			$this->mail_fetch_overview($msgnum);

			// From:
			$this->msg->from = array(new address);
			$this->msg->from = $this->build_address_structure('From');
			$this->msg->fromaddress = $this->header['From'];

			// To:
			$this->msg->to = array(new address);
			if (strtolower($this->type) == 'nntp')
			{
				$temp = explode(',',$this->header['Newsgroups']);
				$to = array(new address);
				for($i=0;$i<count($temp);$i++)
				{
					$to[$i]->mailbox = '';
					$to[$i]->host = '';
					$to[$i]->personal = $temp[$i];
					$to[$i]->adl = $temp[$i];
				}
				$this->msg->to = $to;
			}
			else
			{
				$this->msg->to = $this->build_address_structure('To');
				$this->msg->toaddress = $this->header['To'];
			}

			// Cc:
			$this->msg->cc = array(new address);
			if(isset($this->header['Cc']))
			{
				$this->msg->cc[] = $this->build_address_structure('Cc');
				$this->msg->ccaddress = $this->header['Cc'];
			}

			// Bcc:
			$this->msg->bcc = array(new address);
			if(isset($this->header['bcc']))
			{
				$this->msg->bcc = $this->build_address_structure('bcc');
				$this->msg->bccaddress = $this->header['bcc'];
			}

			// Reply-To:
			$this->msg->reply_to = array(new address);
			if(isset($this->header['Reply-To']))
			{
				$this->msg->reply_to = $this->build_address_structure('Reply-To');
				$this->msg->reply_toaddress = $this->header['Reply-To'];
			}

			// Sender:
			$this->msg->sender = array(new address);
			if(isset($this->header['Sender']))
			{
				$this->msg->sender = $this->build_address_structure('Sender');
				$this->msg->senderaddress = $this->header['Sender'];
			}

			// Return-Path:
			$this->msg->return_path = array(new address);
			if(isset($this->header['Return-Path']))
			{
				$this->msg->return_path = $this->build_address_structure('Return-Path');
				$this->msg->return_pathaddress = $this->header['Return-Path'];
			}

			// UDate
			$this->msg->udate = $this->convert_date($this->header['Date']);

			// Subject
			$this->msg->subject = $this->decode->phpGW_quoted_printable_decode($this->header['Subject']);

			// Lines
			// This represents the number of lines contained in the body
			$this->msg->lines = $this->header['Lines'];
		}

		function mail_headerinfo($msgnum)
		{
			$this->mail_header($msgnum);
		}

		function mail_body($msgnum)
		{
			switch (strtolower($this->type))
			{
				case 'pop':
					$str = 'retr '.$msgnum;
					$expected_response = "^+OK";
					break;
				case 'nntp':
					$str = 'BODY '.$msgnum;
					$expected_response = "^222";
					break;
			}

			if (!$this->msg2socket($str,$expected_response,$response))
			{
				return 0;
			}

			if (strtolower($this->type) == 'pop')
			{
				while (chop($this->read_port()) <> '')
				{
				}
			}
			$retval = '';
			while ($line = $this->read_port())
			{
				$end = chop($line);
				if ($end == '.')
				{
					if($retval == '')
					{
						$retval = 'Body Not Found!'."\n";
					}
					break;
				}
				$retval .= $line . "\n";
			}
			$this->body = $retval;
		}

		function mail_fetchtext($msgnum)
		{
			$this->mail_body($msgnum);
		}

		function getMimeType($file)
		{
			$file=basename($file);
			$mimefile=PHPGW_API_INC.'/phpgw_mime.types';
			$fp=fopen($mimefile,'r');
			$contents = explode("\n",fread ($fp, filesize($mimefile)));
			fclose($fp);

			$parts=explode('.',$file);
			$ext=$parts[(sizeof($parts)-1)];

			for($i=0;$i<sizeof($contents);$i++)
			{
				if (! ereg("^#",$contents[$i]))
				{
					$line=split("[[:space:]]+", $contents[$i]);
					if (sizeof($line) >= 2)
					{
						for($j=1;$j<sizeof($line);$j++)
						{
							if ($line[$j] == $ext)
							{
								$mimetype=$line[0];
								return $mimetype;
							}
						}
					}
				}
			}
			return 'text/plain';
		}

		function is_uu_encoded($body)
		{
			$lines = explode("\n",$body);
			$found_begin = 0;
			$found_end = 0;
			for($i=0;$i<count($lines);$i++)
			{
				if (ereg("^begin",$lines[$i]))
				{
					//	echo 'Found the begin!<br>'."\n";
					$tempvar = explode(' ',$lines[$i]);
					//	echo 'Begin Statement : '.$lines[$i].' (count)='.count($tempvar).'<br>'."\n";
					//	if (count($tempvar) == 3 && strpos($tempvar[2],'.')) $found_begin = 1;
					if (count($tempvar) > 2 && is_long((int)$tempvar[1]))
					{
						$found_begin = 1;
					}
					//	echo 'Found_Begin = '.$found_begin.'  is_long='.is_long((int)$tempvar[1]).'<br>'."\n";
				}
				elseif ($found_begin)
				{
					if (ereg("^end",$lines[$i]))
					{
						$found_end = 1;
					}
				}
			}
			//   echo 'is_uu_encoded = '.$found_end.'<br>'."\n";
			return $found_end;
		}

		function split_uuencoded_into_parts(&$body,&$boundary)
		{
			$binary = Array();
			$body = ereg_replace('<br>',"\n",$body);
			$lines = explode("\n",$body);
			$parts=0;
			$tempbody='';
			if(!$boundary) $boundary = uniqid('');
			$mime_text_header = '--'.$boundary."\n".'Content-Type: text/plain; charset=us-ascii'."\n".'Content-Transfer-Encoding: 7bit'."\n\n";
			$newpart = 1;
			$found_begin = 0;
			$j=0;
			$binary = '';
			for($i=0;$i<count($lines);$i++)
			{
				if($newpart && !ereg("^begin",strtolower($lines[$i])))
				{
					$tempbody .= $mime_text_header."\n".$lines[$i]."\n";
					$newpart = 0;
					$parts++;
				}
				elseif ($found_begin)
				{
					if (!ereg("^end",$lines[$i]))
					{
						if($lines[$i]<>'' || $lines[$i]<>"\n")
						{
							$binary[$j] = $lines[$i];
							$j++;
						}
					}
					else
					{
						$attach = base64_encode($this->decode->uudecode($binary));
						$content_type=$this->getMimeType(strtolower($filename));
						$tempbody .= "\n".'--'.$boundary."\r\n".'Content-Type: '.$content_type.'; name="'.$filename.'"'."\n"
							. 'Content-Transfer-Encoding: base64'."\n".'Content-Disposition: inline; '
							. 'filename="'.$filename.'"'."\n".$attach."\n".'--'.$boundary."\n";
						$binary = Array();
						$filename = '';
						$found_begin = 0;
						if (chop($lines[$i + 1]) <> '' && chop($lines[$i + 1]) <> '.')
						{
							$newpart = 1;
							$i += 2;
						}
					}
				}
				elseif (ereg("^begin",$lines[$i]))
				{
					$lines[$i] = ereg_replace("\n",'',$lines[$i]);
					$temparray = explode(' ',$lines[$i]);
					if (is_int((int)$temparray[1]))
					{
						$newpart = 0;
						for($k=2,$filename='';$k<count($temparray);$k++)
						{
							$filename .= $temparray[$k];
						}
						$filename = substr($filename,0,strlen($filename)-1);
						$found_begin = 1;
						$j=0;
						$parts++;
					}
				}
				else
				{
					$tempbody .= $lines[$i];
				}
			}
			//    return $parts;
			//    echo 'Created '.$parts.' MIME parts!<br>'."\n";
			//    echo str_replace("\n",'<br>',$tempbody);
			$body = $tempbody;
		}

		function parse_boundary($header)
		{
			//    echo $header.'<br>'."\n";
			$temp = explode(';',$header);

			$tboundary = explode('boundary="',$temp[1]);
			//    return quoted_printable_decode(substr($tboundary[1],0,strlen($tboundary[1])-1));
			if(count($tboundary) > 1)
			{
				return quoted_printable_decode(substr($tboundary[1],0,strpos($tboundary[1],'"')));
			}
			else
			{
				return '';
			}
		}

		function how_many_mime_attchs($body)
		{
			$boundary_found = 0;
			if (!$this->header['Content-Type'])
			{
				return 0;
			}
			$this->boundary = $this->parse_boundary($this->header['Content-Type']);
			if($this->boundary <> '')
			{
				$boundary_found = count(explode($this->boundary,$body));
			}
			//    echo $this->boundary."<br>".'Boundary Found = '.$boundary_found.'<br>'."\n";
			if(!$boundary_found)
			{
				$end = strpos($body,'----=_NextPart');
				if($end)
				{
					$start = $end;
					while(substr($body,$end,1) <> ' ')
					{
						$end++;
						$boundary = substr($body,$start+7,$end - $start);
						$this->boundary = $this->create_header('Content-Type: multipart/mixed;',$this->header,' boundary="'.$boundary.'"');
					}
					$boundary_found = count(explode($boundary,$body));
				}
				else
				{
					$boundary=uniqid('----=_NextPart');
					$mime_text_header = '--'.$boundary."\n".'Content-Type: text/plain; charset=us-ascii'."\n".'Content-Transfer-Encoding: 7bit'."\n\n";
					$body = $mime_text_header."\n".$body."\n".'--'.$boundary;
					$this->create_header('Content-Type: multipart/mixed;',$this->header,' boundary="'.$boundary.'"');
					$this->create_header('Mime-Version: 1.0',$this->header);
					$this->boundary = $boundary;
					$boundary_found = 4;
				}
			}
			//    echo $this->boundary.'<br>'."\n";
			//    echo $this->header['Content-Type'].'<br>'."\n";
			return $boundary_found - 2;
		}

		function is_mime_encoded()
		{
			$is = ($this->header['Content-Type'] && $this->header['Content-Type'] <> '' ? 1 : 0);
			return $is;
		}

		function get_content_type($header,&$struct)
		{
			if(strpos($header,';') > 0)
			{
				$tarray = explode(';',$header);
			}
			else
			{
				$tarray = array();
				$tarray[0] = $header;
			}
			$content_type = explode('/',$tarray[0]);
			switch (strtolower($content_type[0]))
			{
				case 'text':
					$struct->type = 0;		// TYPETEXT;
					break;
				case 'multipart':
					$struct->type = 1;		// TYPEMULTIPART;
					break;
				case 'message':
					$struct->type = 2;		// TYPEMESSAGE;
					break;
				case 'application':
					$struct->type = 3;		// TYPEAPPLICATION;
					break;
				case 'audio':
					$struct->type = 4;		// TYPEAUDIO;
					break;
				case 'image':
					$struct->type = 5;		// TYPEIMAGE;
					break;
				case 'video':
					$struct->type = 6;		// TYPEVIDEO;
					break;
				default:
					$struct->type = 7;		// TYPEOTHER;
					break;
			}
			if (count($content_type) >= 1)
			{
				$struct->subtype = strtolower($content_type[1]);
				$struct->ifsubtype = true;
			}
			$tttarray = new parameter;
			for ($i=1;$i<count($tarray);$i++)
			{
				$params = explode('=',$tarray[$i]);
				if (strtolower(trim($params[0])) == 'name')
				{
					$ttarray = explode('"',$params[1]);
					$params[1] = trim($ttarray[1]);
					while(substr($params[1],strlen($params[1]),1) == '"')
					{
						$params[1] = trim(substr($params[1],0,strlen($params[1])-1));
					}
				}
				$tttarray->attribute = trim($params[0]);
				$tttarray->value = $params[1];
				$struct->parameters[] = $tttarray;
			}
			unset($tttarray);
		}

		function get_encoding_type($header,&$struct)
		{
			switch (strtolower($header))
			{
				case '7bit':
					$struct->encoding = 0;		// ENC7BIT;
					break;
				case '8bit':
					$struct->encoding = 1;		// ENC8BIT;
					break;
				case 'binary':
					$struct->encoding = 2;		// ENCBINARY;
					break;
				case 'base64':
					$struct->encoding = 3;		// ENCBASE64;
					break;
				case 'quoted-printable':
					$struct->encoding = 4;		// ENCQUOTEDPRINTABLE;
					break;
				default:
					$struct->encoding = 5;		// ENCOTHER;
					break;
			}
		}

		function build_mime_structure($header)
		{
			$struct = new struct;
			$tempvar = True;
			if (isset($header['Content-Type']) && $header['Content-Type'])
			{
				$this->get_content_type($header['Content-Type'],$struct);
			}
			if (isset($header['Content-Transfer-Encoding']) && $header['Content-Transfer-Encoding'])
			{
				$this->get_encoding_type($header['Content-Transfer-Encoding'],$struct);
			}
			else
			{
				$tempvar1 = 0;
				$struct->encoding = $tempvar1;
			}
			if (isset($header['Content-Description']) && $header['Content-Description'])
			{
				$struct->description = $header['Content-Description'];
				$struct->ifdescription = $tempvar;
			}
			if (isset($header['Content-Identifier']) && $header['Content-Identifier'])
			{
				$struct->id = $header['Content-Identifier'];
				$struct->ifid = $tempvar;
			}
			if (isset($header['Lines']) && $header['Lines'])
			{
				(int)$struct->lines = (int)$header['Lines'];
			}
			if (isset($header['Content-Length']) && $header['Content-Length'])
			{
				(int)$struct->bytes = (int)$header['Content-Length'];
			}
			if (isset($header['Content-Disposition']) && $header['Content-Disposition'])
			{
				$temparray = explode(';',$header['Content-Disposition']);
				$struct->disposition = $temparray[0];
				$struct->ifdisposition = $tempvar;
			}
			$ttarray = new parameter;
			if (isset($header['Mime-Version']) && $header['Mime-Version'])
			{
				$tempvar2 = 'Mime-Version';
				$ttarray->attribute = $tempvar2;
				$ttarray->value = $header['Mime-Version'];
				$struct->parameters[] = $ttarray;
				$struct->ifparameters = $tempvar;
			}
			unset($ttarray);
			return $struct;
		}

		function split_mime_msg_parts($body,$boundary,$part,&$sub_header,&$sub_body)
		{
			$parts = explode('--'.$boundary,$body);
			$lines = explode("\n",$parts[$part]);
			$sub_body = '';
			for ($i=0,$j=0;$i<count($lines);$i++)
			{
				//      echo $lines[$i].'<br>'."\n";
				$lines[$i] = ereg_replace("\r",'',$lines[$i]);
				//    $lines[$i] = ereg_replace("\n",'',$lines[$i]);
				//      echo 'Line: '.$i.' : '.$lines[$i].'<br>'."\n";
				if (ereg("^Content-",$lines[$i]) || 
					ereg("^x-no-archive:",$lines[$i]))
				{
					while(substr($lines[$i],strlen(chop($lines[$i]))-1) == ';')
					{
						$lines[$i] = strip_tags($lines[$i]);
						$tline = $lines[$i];
						$i++;
						while($lines[$i] == '' || $lines[$i] == "\n")
						{
							$i++;
						}
						$lines[$i] = strip_tags($lines[$i]);
						$lines[$i] = $tline . chop($lines[$i]);
						//  echo 'Lines = '.$lines[$i].'<br>'."\n";
					}
					//	if(ereg("[[:space:]]x-mac-type=",ltrim($lines[$i + 1]))) {
						//	  $i++;
						//	  $lines[$i] = $lines[$i - 1] . ';' . $lines[$i];
						//	}
						//        $this->create_header($lines[$i],&$sub_header,$lines[$i+1]);
						$this->create_header($lines[$i],$sub_header);
				}
				else
				{
					if($lines[$i]<>'')
					{
						$sub_body .= $lines[$i] . "\n";
						$j++;
					}
				}
			}
			if($this->is_uu_encoded($sub_body))
			{
				$this->split_uuencoded_into_parts($sub_body,$boundary);
			}
			$this->create_header('Lines: '.$j,$sub_header);
		}

		function mail_fetchstructure($msgnum)
		{
			$this->mail_body($msgnum);
			$this->struct = new struct;
			$this->sparts = array(new struct);
			$this->struct->parameters = array(new parameter);
			if ($this->is_uu_encoded($this->body))
			{
				//  echo 'This is a UUEncoded message!<br>'."\n";
				$this->split_uuencoded_into_parts($this->body,$boundary);
				$this->create_header('Content-Type: multipart/mixed;',$this->header,' boundary="'.$boundary.'"');
				$this->create_header('Mime-Version: 1.0',$this->header);
			}
			if (!$this->is_mime_encoded())
			{
				$boundary=uniqid('----=_NextPart');
				$mime_text_header = '--'.$boundary."\n".'Content-Type: text/plain; charset=us-ascii'."\n".'Content-Transfer-Encoding: 7bit'."\n\n";
				$this->body = $mime_text_header."\n".$this->body."\n".'--'.$boundary;
				$this->create_header('Content-Type: multipart/mixed;',$this->header,' boundary="'.$boundary.'"');
				$this->create_header('Mime-Version: 1.0',$this->header);
			}
			if ($this->is_mime_encoded())
			{
				$this->numparts = $this->how_many_mime_attchs($this->body);
				$this->struct = $this->build_mime_structure($this->header);
				for($i=0;$i<=$this->numparts;$i++)
				{
					$this->split_mime_msg_parts($this->body,$this->parse_boundary(
						$this->header['Content-Type']
					),
					$i,$this->hsub[$i],
					$this->bsub[$i]);
					$this->sparts[$i] = $this->build_mime_structure($this->hsub[$i]);
				}
			}
		}

		function build_body_to_print()
		{
			$str = '';
			for ($i=0;$i<=$this->numparts;$i++)
			{
				$part = (!$this->sparts[$i] ? $this->struct : $this->sparts[$i]);

				$str .= $this->decode->inline_display($part,$this->bsub[$i],'Section',$this->folder);
				$str .= "\n".'<p>';
			}
			return $str;
		}
	}
?>
