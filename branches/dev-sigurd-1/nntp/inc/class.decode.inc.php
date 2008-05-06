<?php
  /**************************************************************************\
  * phpGroupWare app (NNTP)                                                  *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <mpeters@satx.rr.com>                             *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	class decode
	{
		/* PHP `quoted_printable_decode` function does not work properly:
		it should convert '_' characters into ' '. */
		function phpGW_quoted_printable_decode($string)
		{
			$string = str_replace('_', ' ', $string);
			return quoted_printable_decode($string);
		}

		/* Remove '=' at the end of the lines. `quoted_printable_decode` doesn't do it. */
		function phpGW_quoted_printable_decode2($string)
		{
			$string = quoted_printable_decode($string);
			return preg_replace("/\=\n/", '', $string);
		}

		function decode_base64($string)
		{
			$string = ereg_replace("'", "\'", $string);
			$string = preg_replace("/\=\?(.*?)\?{b,B}\?(.*?)\?\=/ieU",
			base64_decode('\\2'),
			$string);
			return $string;
		}

		function decode_qp($string)
		{
			$string = ereg_replace("'", "\'", $string);
			$string = preg_replace("/\=\?(.*?)\?{q,Q}\?(.*?)\?\=/ieU",
			$this->phpGW_quoted_printable_decode('\\2'),
			$string);
			return $string;
		}

		function decode_header($string)
		{
			/* Decode from qp or base64 form */
			if (preg_match("/\=\?(.*?)\?{b,B}\?/i", $string))
			{
				return $this->decode_base64($string);
			}
			if (preg_match("/\=\?(.*?)\?{q,Q}\?/i", $string))
			{
				return $this->decode_qp($string);
			}
			return $string;
		}

		function decode_author($author,&$email,&$name)
		{
			/* Decode from qp or base64 form */
			$author = $this->decode_header($author);
			/* Extract real name and e-mail address */
			/* According to RFC1036 the From field can have one of three formats:
			1. Real Name <name@domain.name>
			2. name@domain.name (Real Name)
			3. name@domain.name
			*/
			/* 1st case */
			//    if (eregi("(.*) <([-a-z0-9\_\$\+\.]+\@[-a-z0-9\_\.]+[-a-z0-9\_]+)>",
			if (eregi("(.*) <([-a-z0-9_$+.]+@[-a-z0-9_.]+[-a-z0-9_]+)>",$author, $regs))
			{
				$email = $regs[2];
				$name = $regs[1];
				/* 2nd case */
			}
			elseif(eregi("([-a-z0-9_$+.]+@[-a-z0-9_.]+[-a-z0-9_]+) ((.*))",$author, $regs))
			{
				//      if (eregi("([-a-z0-9\_\$\+\.]+\@[-a-z0-9\_\.]+[-a-z0-9\_]+) \((.*)\)", 
				$email = $regs[1];
				$name = $regs[2];
				/* 3rd case */
			}
			else
			{
				$email = $author;
			}
			if ($name == '')
			{
				$name = $email;
			}
			$name = eregi_replace("^\"(.*)\"$", "\\1", $name);
			$name = eregi_replace("^\((.*)\)$", "\\1", $name);
		}

		function get_mime_type($de_part)
		{
			if (!isset($de_part->type))
			{
				return 'unknown';
			}

			switch ($de_part->type)
			{
				case 0:		$mime_type = 'text'; break;
				case 1:		$mime_type = 'multipart'; break;
				case 2:		$mime_type = 'message'; break;
				case 3:		$mime_type = 'application'; break;
				case 4:		$mime_type = 'audio'; break;
				case 5:		$mime_type = 'image'; break;
				case 6:		$mime_type = 'video'; break;
				case 7:		$mime_type = 'other'; break;
				default:		$mime_type = 'unknown';
			}
			return $mime_type;
		}

		function get_mime_encoding($de_part)
		{
			switch ($de_part->encoding)
			{
				case 3:	$mime_encoding = 'base64'; break;
				case 4:	$mime_encoding = 'qprint'; break;
				case 5:	$mime_encoding = 'other';  break;
				default:	$mime_encoding = 'other';
			}
			return $mime_encoding;
		}

		function get_att_name($de_part)
		{
			$param = new parameter;
			$att_name = 'Unknown';
			if (!isset($de_part->parameters))
			{
				return $att_name;
			}
			for ($i=0;$i<count($de_part->parameters);$i++)
			{
				$param = (!$de_part->parameters[$i] ?
				$de_part->parameters :
				$de_part->parameters[$i]);
				if(!$param)
				{
					break;
				}
				$pattribute = $param->attribute;
				if (strtolower($pattribute) == 'name')
				{
					$att_name = $param->value;
				}
			}
			return $att_name;
		}

		function attach_display($de_part,$part_no,$mailbox,$folder,$msgnum)
		{
			$mime_type = $this->get_mime_type($de_part);  
			$mime_encoding = $this->get_mime_encoding($de_part);
			$att_name = 'unknown';
			$param = new parameter;

			for ($i = 0; $i < count($de_part->parameters); $i++)
			{
				if(!$de_part->parameters[$i])
				{
					break;
				}
				$param = $de_part->parameters[$i];
				$pattribute = $param->attribute;
				if (strtoupper($pattribute) == 'NAME')
				{
					$att_name = $param->value;
					$url_att_name = urlencode($att_name);
				}
			}

			$jnk = '<a href="'.$GLOBALS['phpgw']->link('/nntp/get_attach.php','folder='.$folder.'&msgnum='.$msgnum
				. '&part_no='.$partno.'&type='.$mime_type.'&subtype='.$de_part->subtype
				. '&name='.$url_att_name.'&encoding='.$mime_encoding).'">'.$att_name.'</a>';
			return $jnk;
		}

		function inline_display($de_part,$dsp,$mime_section,$folder)
		{
			$mime_type = $this->get_mime_type($de_part);
			$mime_encoding = $this->get_mime_encoding($de_part);

			$tag = "pre";
			//  $jnk = isset($de_part->disposition) ? $de_part->disposition : "unknown";

			//  echo "<!-- MIME disp: $jnk -->\n";
			//  echo "<!-- MIME type: $mime_type -->\n";
			//  echo "<!-- MIME subtype: $de_part->subtype -->\n";
			//  echo "<!-- MIME encoding: $mime_encoding -->\n";
			//  echo "<!-- MIME filename: $att_name -->\n";

			if ($mime_encoding == "qprint")
			{
				$dsp = $this->decode_qp($dsp);
				$tag = "tt";
			}

			// Thanks to Omer Uner Guclu <oquclu@superonline.com> for figuring out
			// a better way to do message wrapping

			if (isset($de_part->subtype) && strtoupper($de_part->subtype) == 'PLAIN')
			{
				// nlbr and htmlentities functions are strip latin5 characters
				$dsp = $GLOBALS['phpgw']->strip_html($dsp);
				$dsp = ereg_replace( "^",'<p>',$dsp);
				$dsp = ereg_replace( "\r\n",'<br>',$dsp);
				$dsp = ereg_replace( "\n",'<br>',$dsp);
				$dsp = ereg_replace( "\t",'    ',$dsp);
				$dsp = ereg_replace( "$",'</p>', $dsp);
				$dsp = $this->make_clickable($dsp,$folder);
				return '<table border="0" align="left" cellpadding="10" width="80%"><tr><td>'.$dsp.'</td></tr></table>';
			}
			elseif (isset($de_part->subtype) && strtoupper($de_part->subtype) == 'HTML')
			{
				$str = $this->output_bound($mime_section.':',$mime_type.'/'.$de_part->subtype);
				return $str.$dsp;
			}
			elseif (isset($de_part->subtype) && 
				(strtoupper($de_part->subtype) == 'JPG' ||
				strtoupper($de_part->subtype) == 'JPEG' ||
				strtoupper($de_part->subtype) == 'PJPEG' ||
				strtoupper($de_part->subtype) == 'GIF' ||
				strtoupper($de_part->subtype) == 'PNG'))
			{
				$att_name = $this->get_att_name($de_part);
				$str = $this->output_bound($mime_section.':',$mime_type.'/'.$de_part->subtype);
				return $str.$this->image_display($dsp,$att_name);
			}
			else
			{
				$str = $this->output_bound($mime_section.':',$mime_type.'/'.$de_part->subtype);
				return "$str<$tag>$dsp</$tag>\n";
			}
		}

		function output_bound($title, $str)
		{
			return '</td></tr></table>'."\n"
				. '<table border="0" cellpadding="4" cellspacing="3" '
				. 'width="700">'."\n".'<tr><td bgcolor"'.$GLOBALS['phpgw_info']['theme']['th_bg'].'" ' 
				. 'valign="top"><font size="2" face="'.$GLOBALS['phpgw_info']['theme']['font'].'">'
				. '<b>'.$title.'</b></td>'."\n".'<td bgcolor="'.$GLOBALS['phpgw_info']['theme']['row_on'].'" '
				. 'width="570"><font size="2" face="'.$GLOBALS['phpgw_info']['theme']['font'].'">'
				. $str.'</td></tr></table>'."\n".'<p>'."\n".'<table border="0" cellpadding="2" '
				. 'cellspacing="0" width="100%"><tr><td>';
		}

		function image_display($bsub,$att_name)
		{
			$bsub = strip_tags($bsub);
			if(!$GLOBALS['phpgw']->vfs->file_exists(
					Array(
						'string'	=> '.nntp',
						'relatives'	=> array(RELATIVE_USER)
					)
				)
			)
			{
				$GLOBALS['phpgw']->vfs->mkdir(
					Array(
						'string'	=> '.nntp',
						'relatives'	=> array(RELATIVE_USER)
					)
				);
			}
			$last_dot = strrpos($att_name,'.');
			$file_ext = substr($att_name,$last_dot);
			$file_name = substr($att_name,0,$last_dot);
			$ext_version = '_1';
			echo 'ATT_NAME: '.$att_name.'<br>'."\n";
			while($GLOBALS['phpgw']->vfs->file_exists(
					Array(
						'string'	=> $att_name,
						'relatives'	=> array(RELATIVE_USER_APP)
					)
				)
			)
			{
				$work_name = str_replace($file_name,'',substr($att_name,0,strrpos($att_name,'.')));
				if($work_name == $ext_version)
				{
					$ext_version = '_'.(intval(substr($ext_version,1)) + 1);
				}
				$att_name = $file_name.$ext_version.$file_ext;
				echo 'ATT_NAME: '.$att_name.'<br>'."\n";
			}
			$GLOBALS['phpgw']->vfs->write(
				Array(
					'string'	=> $att_name,
					'relatives'	=> array(RELATIVE_USER_APP),
					'content'	=> base64_decode($bsub)
				)
			);
			// we want to display images here, even though they are attachments.
			return  '</td></tr><tr align="center"><td align="center"><img src="'.$GLOBALS['phpgw']->link('/nntp/view_attachment.php','file='.urlencode($att_name)).'"><p>';
		}

		// function make_clickable ripped off from PHPWizard.net
		// http://www.phpwizard.net/phpMisc/
		// modified to make mailto: addresses compose in AeroMail
		function make_clickable($text,$folder)
		{
			$ret = eregi_replace("([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])",
			"<a href=\"\\1://\\2\\3\" target=\"_new\">\\1://\\2\\3</a>", str_replace("<br>","\n",$text));
			if($ret == $text)
			{
				$ret = eregi_replace("(([a-z0-9_]|\\-|\\.)+@([^[:space:]]*)([[:alnum:]-]))",
					"<a href=\"".$GLOBALS['phpgw']->link("compose.php","folder=".urlencode($folder))
					."&to=\\1\">\\1</a>", $ret);
			}
			return(str_replace("\n","<br>",$ret));
		}

		//  function make_clickable($text,$folder)
		//  {
			//    $ret = ereg_replace("([a-z0-9\-\_\$\.]+\@([a-z0-9\-]+\.)+[a-z0-9\-\_]+)",
			//			"<a href=\''.$GLOBALS['phpgw']->link("compose.php","folder=".urlencode($folder))."&to=\\0\">\\0</a>",
			//			$text);
			//    return ereg_replace("((http|ftp)\:\/\/([a-z0-9\-\_]+(\.|\/|\/\~|\-))+[a-z0-9\-\_\=\?\/\&]+)",
			//			"<a href=\"\\0\" target=\"_new\">\\0</a>",
			//			$ret);
			////    $ret = eregi_replace("([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])",
			////	   "<a href=\"\\1://\\2\\3\" target=\"_new\">\\1://\\2\\3</a>", $text);
			////    return eregi_replace("(([a-z0-9_]|\\-|\\.)+@([^[:space:]]*)([[:alnum:]-]))",
			////	   "<a href=\''.$GLOBALS['phpgw']->link("compose.php","folder=".urlencode($folder))
			////	 . "&to=\\1\">\\1</a>", $ret);
			//  }

		function uudecode($str)
		{
			$file='';
			for($i=0;$i<count($str);$i++)
			{
				if ($i==count($str)-1 && $str[$i] == "`")
				{
					$GLOBALS['phpgw']->common->phpgw_exit();
				}
				$pos=1;
				$d=0;
				$len=(int)(((ord(substr($str[$i],0,1)) ^ 0x20) - ' ') & 077);
				while (($d+3<=$len) && ($pos+4<=strlen($str[$i])))
				{
					$c0=(ord(substr($str[$i],$pos  ,1)) ^ 0x20);
					$c1=(ord(substr($str[$i],$pos+1,1)) ^ 0x20);
					$c2=(ord(substr($str[$i],$pos+2,1)) ^ 0x20);
					$c3=(ord(substr($str[$i],$pos+3,1)) ^ 0x20);
					$file .= chr(((($c0 - ' ') & 077) << 2) | ((($c1 - ' ') & 077) >> 4));
					$file .= chr(((($c1 - ' ') & 077) << 4) | ((($c2 - ' ') & 077) >> 2));
					$file .= chr(((($c2 - ' ') & 077) << 6) |  (($c3 - ' ') & 077)      );
					$pos+=4;
					$d+=3;
				}
				if (($d+2<=$len) && ($pos+3<=strlen($str[$i])))
				{
					$c0=(ord(substr($str[$i],$pos  ,1)) ^ 0x20);
					$c1=(ord(substr($str[$i],$pos+1,1)) ^ 0x20);
					$c2=(ord(substr($str[$i],$pos+2,1)) ^ 0x20);
					$file .= chr(((($c0 - ' ') & 077) << 2) | ((($c1 - ' ') & 077) >> 4));
					$file .= chr(((($c1 - ' ') & 077) << 4) | ((($c2 - ' ') & 077) >> 2));
					$pos+=3;
					$d+=2;
				}
				if (($d+1<=$len) && ($pos+2<=strlen($str[$i])))
				{
					$c0=(ord(substr($str[$i],$pos  ,1)) ^ 0x20);
					$c1=(ord(substr($str[$i],$pos+1,1)) ^ 0x20);
					$file .= chr(((($c0 - ' ') & 077) << 2) | ((($c1 - ' ') & 077) >> 4));
				}
			}
			return $file;
		}
	}
?>
