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

	/* $Id: class.nntp.inc.php 15912 2005-05-05 14:32:50Z powerstat $ */

	include(PHPGW_APP_INC.'/message.inc.php');
	include(PHPGW_APP_INC.'/class.mail.inc.php');

	class nntp extends mail
	{
		var $db;
		var $con;
		var $folder;
		var $groupname;
		var $messagecount;
		var $lowmsg;
		var $highmsg;
		var $active;
		var $lastread;
		var $msgnum;
		var $display;

		function nntp($params=False)
		{
			$folder = '';
			$msgnum = '';
			if(is_array($params))
			{
				@reset($params);
				while(list($key,$value) = each($params))
				{
					$$key = $value;
				}
			}

			if (!$this->mail('nntp'))
			{
				return 0;
			}
			$this->db = $GLOBALS['phpgw']->db;

			if(!isset($GLOBALS['phpgw_info']['server']['nntp_server']) || $GLOBALS['phpgw_info']['server']['nntp_server']=='')
			{
				return $this->set_error('ERROR','Configuration Error','The administrator has not configured the NNTP Server.');
			}

			if(!isset($GLOBALS['phpgw_info']['server']['nntp_port']) || intval($GLOBALS['phpgw_info']['server']['nntp_port'])==0)
			{
				$GLOBALS['phpgw_info']['server']['nntp_port'] = 119;
			}
			if (!$this->mail_open($GLOBALS['phpgw_info']['server']['nntp_server'],$GLOBALS['phpgw_info']['server']['nntp_port'],$GLOBALS['phpgw_info']['server']['nntp_login_username'],$GLOBALS['phpgw_info']['server']['nntp_login_password']))
			{
				if($GLOBALS['phpgw_info']['flags']['noheader'] == True)
				{
					$GLOBALS['phpgw']->common->phpgw_header();
					echo parse_navbar();
				}

				echo '<p><center><b>' . lang('There was an error trying to connect to your news server.<br>Please contact your admin to check the news servername, username or password.').'</b></center>';
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			if ($folder<>'' && $msgnum<>'')
			{
				if (!$this->read_table($folder,$msgnum))
				{
					return 0;
				}
				$this->mail_header($this->msgnum);
				$this->mail_fetchstructure($this->msgnum);
				return 1;
			}
		}

		function update_lastread_info()
		{
			$this->db->query('UPDATE newsgroups SET lastread = '.time().' WHERE con='.$this->con);
		}

		function save_to_db($groupdb)
		{
			$this->db->query("SELECT con FROM newsgroups WHERE name='".addslashes($groupdb['name'])."'");
			if ($this->db->num_rows() > 0)
			{
				$this->db->next_record();
				$con = $this->db->f('con');
				$this->db->query('UPDATE newsgroups SET messagecount='.$groupdb['count'].',lastmessage='.(int)$groupdb['last'][$i].',lastread='.time().' where con='.$con);
			}
			else
			{
				$this->db->query("insert into newsgroups (name,messagecount,lastmessage,lastread) values('".addslashes($groupdb['name'])."',".(int)$groupdb["count"].','.(int)$groupdb['last'].','.time().')');
			}
		}

		function load_table()
		{
			$retval = Array();
			$ret = Array();
			$retval['name'] = Array();
			$retval['count'] = Array();
			$retval['last'] = Array();

			if (!$this->msg2socket('LIST','^215',$response))
			{
				return 0;
			}

			/* Default is: GROUP HIGH LOW FLAGS */
			while ($line = $this->read_port())
			{
				$line = chop($line);
				if ($line == '.')
				{
					break;
				}
				if (!ereg('^control',$line) && !ereg('^junk',$line) && !ereg('^to ',$line))
				{
					if (!$template || ereg($template, $line))
					{
						$fields = explode(' ', $line);
						$count = ($fields[1] >= $fields[2] ? (int)$fields[1] - (int)$fields[2] + 1 : 0);
						$ret['name'] = $fields[0];
						$ret['count'] = $count;
						$ret['last'] = $fields[1];
						//$retval[] = $ret;
						if (strlen($ret['name']) <= 255)
						{
							$this->save_to_db($ret);
						}
					}
				}
			}
			//	return $retval;
		}

		function noexist()
		{
			$this->con          = 0;
			$this->groupname    = '';
			$this->mailbox      = '';
			$this->messagecount = 0;
			$this->lowmsg       = 0;
			$this->highmsg      = 0;
			$this->active       = 'N';
			$this->lastread     = 0;
			$this->msgnum       = 0;
		}

		function read_table($newsgroup,$msgnum=0)
		{
			$this->db->query('SELECT name,messagecount,active,lastread FROM newsgroups WHERE con='.$newsgroup);
			if ($this->db->num_rows() > 0)
			{
				$this->db->next_record();
				$this->con          = $newsgroup;
				$this->folder       = stripslashes($this->db->f('name'));
				$this->mailbox      = $this->db->f('name');
				$this->messagecount = $this->db->f('messagecount');
				$this->lowmsg       = $this->mail_first_msg();
				$this->highmsg      = $this->mail_last_msg();
				$this->active       = $this->db->f('active');
				$this->lastread     = $this->db->f('lastread');
				$this->msgnum       = $msgnum;
				if ($this->active == 'N')
				{
					$GLOBALS['phpgw']->preferences->delete('nntp',$newsgroup);
					$GLOBALS['phpgw']->preferences->save_repository();
					return $this->set_error('ERROR','Automatic Disabling','The newsgroup '.$this->groupname.' is not activated by the Administrator.');
				}
				elseif ($this->msgnum <> 0)
				{
					if (($this->msgnum < $this->lowmsg) || ($this->msgnum > $this->highmsg))
					{
						return $this->set_error('ERROR','Message does not exist',$this->mailbox.':'.$this->msgnum.' does not exist.');
					}
					else
					{
						return 1;
					}
				}
				else
				{
					return 1;
				}
			}
			else
			{
				$this->noexist();
				$this->set_error('ERROR','Not valid','The news folder '.$newsgroup.' is not available.');
				return 0;
			}
		}

		function xhdr($field,$first,$last)
		{
			if (!$this->msg2socket('XHDR '.$field.' '.$first.'-'.$last,'^221',$response))
			{
				return 0;
			}

			$retval = Array();
			while ($line = $this->read_port())
			{
				$line = chop($line);
				if ($line == '.')
				{
					break;
				}
				$breakpos = strpos($line,' ');
				$retval[substr($line,0,$breakpos)] = substr($line,$breakpos+1,strlen($line)-$breakpos);
			}
			return $retval;
		}

		function get_objects($field,$length)
		{
			$newarray = Array();
			$start = $this->highmsg - $this->display;
			$stop = $this->highmsg;
			if($start < 0)
			{
				$start = $this->lowmsg;
			}
			$retval = $this->xhdr($field,$start,$this->highmsg);
			if (!$retval)
			{
				return 0;
			}
			while (list($nr,$fieldval) = each($retval))
			{
				$fieldval = $this->decode->phpGW_quoted_printable_decode($fieldval);
				if ($length && (strlen($fieldval) > $length))
				{
					$fieldval = substr($fieldval, 0, $length).'...';
				}
				$newarray[$nr] = $fieldval;
			}
			return $newarray;
		}

		function get_subjects()
		{
			$this->update_lastread_info();
			return $this->get_objects('Subject',35);
		}

		function get_list($field,$msgnum,$totaltodisplay)
		{
			$newarray = Array();
			$fields = Array(
				0 => 'From',
				1 => 'Subject',
				2 => 'Date'
			);
			$i=0;
			while($i < $totaltodisplay)
			{
				if ($this->mail_fetch_overview($msgnum))
				{
					$newarray[$i]['Msgnum'] = $msgnum;
					for($j=0;$j<2;$j++)
					{
						$newarray[$i][$fields[$j]] = $this->decode->phpGW_quoted_printable_decode($this->header[$fields[$j]]);
					}
					$newarray[$i]['Date'] = $this->convert_date($this->header['Date']);
					$i++;
				}
				$msgnum++;
			}
			return $newarray;
		}

		function get_next_article_number($msgnum)
		{
			$retval = array();
			if (!$this->msg2socket('STAT '.$msgnum,'^223',$response))
			{
				return 0;
			}
			if (!$this->msg2socket('NEXT','^223',$response))
			{
				return 0;
			}
			//		echo 'Next_Msg = '.$response.'<br>'."\n";
			$retval = explode(' ',$response);
			return $retval[1];    
		}

		function get_prev_article_number($msgnum)
		{
			$retval = array();
			if (!$this->msg2socket('STAT '.$msgnum,'^223',$response))
			{
				return 0;
			}
			if (!$this->msg2socket('LAST','^223',$response))
			{
				return 0;
			}
			//		echo 'Prev_Msg = '.$response.'<br>'."\n";
			$retval = explode(' ',$response);
			return $retval[1];    
		}

		function get_to()
		{
			for($i=0;$i<count($this->msg->to);$i++)
			{
				$to = (!$this->msg->to[$i] ? $this->msg->to : $this->msg->to[$i]);
				$this->db->query("SELECT con FROM newsgroups WHERE name='".$to->personal."' and active='Y'");
				if ($this->db->num_rows() > 0)
				{
					$this->db->next_record();
					$con = $this->db->f('con');
					$str[$i] = send_to($to,$con);
					if ($GLOBALS['phpgw_info']['user']['preferences']['nntp'][$con])
					{
						$str[$i] .= monitor(1,$con);
					}
					else
					{
						$str[$i] .= monitor(0,0);
					}
				}
				else
				{
					$str[$i] = '<tr><td align="left">'.$to->adl.monitor(0,0);
				}
			}
			return implode('',$str);
		}
	}
?>
