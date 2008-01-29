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
	/* $Id: class.socaching.inc.php 18005 2007-02-27 13:15:36Z sigurdne $ */

	class socaching
	{
		var $public_functions = array
		(
			'addAtachment'	=> True,
			'action'	=> True
		);
		
		function socaching($_hostname, $_accountname, $_foldername, $_accountid)
		{
			$this->hostname		= $_hostname;
			$this->accountname	= $_accountname;
			$this->foldername	= $_foldername;
			$this->accountid	= $_accountid;
			
			$this->db		= $GLOBALS['phpgw']->db;
			$this->like	= $this->db->like;
		}
		
		function addToCache($_data)
		{
			$values = array
			(
				$this->accountid,
				$this->db->db_addslashes($this->hostname),
				$this->db->db_addslashes($this->foldername),
				$this->db->db_addslashes($this->accountname), 
				$_data['uid'],
				$_data['date'],
				$this->db->db_addslashes(utf8_encode($_data['subject'])),
				$this->db->db_addslashes(utf8_encode($_data['sender_name'])),
				$this->db->db_addslashes(utf8_encode($_data['sender_address'])),
				$this->db->db_addslashes(utf8_encode($_data['to_name'])),
				$this->db->db_addslashes(utf8_encode($_data['to_address'])),
				$_data['size'],
				$_data['attachments']
			);

 	        $values = $this->db->validate_insert($values);

			$query = "INSERT INTO phpgw_felamimail_cache ".
					 "(accountid, hostname, foldername, accountname, uid, date, subject, sender_name, sender_address, to_name, to_address, size, attachments) ".
					 "values($values)";

			$this->db->query($query,__LINE__,__FILE__);

			#print "$query<br>";
		}
		
		// create sql from the filter array
		function getFilterSQL($_filter)
		{
			if(is_array($_filter))
			{
			$filter = '';
			while(list($key,$value) = @each($_filter))
			{
					if($filter != '') $filter .= " or ";
				switch($key)
				{
					case "from":
							$filter .= "(sender_name {$this->like} '%$value%' or sender_address {$this->like} '%$value%') ";
						break;
					case "to":
							$filter .= "(to_name {$this->like} '%$value%' or to_address {$this->like} '%$value%') ";
						break;
					case "subject":
							$filter .= "subject {$this->like} '%$value%' ";
						break;
				}
			}
				if($filter != '') $filter = " and ($filter) ";
				return $filter;
			}
			return '';
			
		}
		
		function getHeaders($_firstMessage='', $_numberOfMessages='', $_sort='', $_filter='')
		{
			$sort = $this->getSortSQL($_sort);
			$filter = $this->getFilterSQL($_filter);
			
			$query = sprintf("select uid, date, subject, sender_name, sender_address, to_name, to_address, size, attachments from phpgw_felamimail_cache ".
					 "where accountid='%s' and hostname='%s' and foldername = '%s' and accountname='%s' %s $sort",
					 $this->accountid, $this->db->db_addslashes($this->hostname),
					 $this->db->db_addslashes($this->foldername), $this->db->db_addslashes($this->accountname),
					 $filter);
			
			if($_firstMessage == '' && $_numberOfMessages == '')
			{
				$this->db->query("$query",__LINE__,__FILE__);
			}
			else
			{
				$this->db->limit_query("$query",$_firstMessage-1,__LINE__,__FILE__,$_numberOfMessages);
			}
			$retValue = array();
			while($this->db->next_record())
			{
				$retValue[] = array(
						'uid'			=> $this->db->f('uid'),
						'sender_name'		=> $this->db->f('sender_name'), 
						'sender_address'	=> $this->db->f('sender_address'), 
						'to_name'		=> $this->db->f('to_name'), 
						'to_address'		=> $this->db->f('to_address'),
						'attachments'		=> $this->db->f('attachments'),
						'date'			=> $this->db->f('date')
						);
			}
			return $retValue;
		}
		
		//return the cached status numbers
		// 
		// return values
		// 0 : nothing cached for this folder so far
		// array with the currently cached infos
		function getImapStatus()
		{
			$query = sprintf("select messages,recent,unseen,uidnext,uidvalidity ".
					 "from phpgw_felamimail_folderstatus where ".
					 "hostname='%s' and ".
					 "accountname='%s' and ".
					 "foldername='%s' and ".
					 "accountid='%s'",
					 $this->hostname, 
					 $this->accountname,
					 $this->foldername,
					 $this->accountid);
			$this->db->query($query);
			if ($this->db->next_record())
			{
				$retValue = array
				(
					'messages'	=> $this->db->f("messages"),
					'recent'	=> $this->db->f("recent"),
					'unseen'	=> $this->db->f("unseen"),
					'uidnext'	=> $this->db->f("uidnext"),
					'uidvalidity'	=> $this->db->f("uidvalidity")
				);
				return $retValue;
			}
			else
			{
				return 0;
			}
		}
		
		// return the numbers of messages in cache currently
		// but use the use filter
		function getMessageCounter($_filter)
		{
			$filter = '';
			if(isset($_filter) && is_array($_filter))
			{
				while(list($key,$value) = @each($_filter))
				{
						if($filter != '') $filter .= " or ";
					switch($key)
					{
						case "from":
								$filter .= "(sender_name {$this->like} '%$value%' or sender_address {$this->like} '%$value%') ";
							break;
						case "to":
								$filter .= "(to_name {$this->like} '%$value%' or to_address {$this->like} '%$value%') ";
							break;
						case "subject":
								$filter .= "subject {$this->like} '%$value%' ";
							break;
					}
				}
				if($filter !='') $filter = " and ($filter) ";
			}
			
			$query = sprintf("select count(*) as count from phpgw_felamimail_cache ".
					 "where accountid='%s' and hostname='%s' and foldername = '%s' and accountname='%s' %s",
					 $this->accountid, $this->db->db_addslashes($this->hostname),
					 $this->db->db_addslashes($this->foldername), $this->db->db_addslashes($this->accountname),
					 $filter);
			#print "<br>$query<br>";
			
			$this->db->query("$query",__LINE__,__FILE__);
			
			$this->db->next_record();
			
			return $this->db->f("count");
		}
		
		// get the next message
		function getNextMessage($_uid, $_sort='', $_filter='')
		{
			$sort = $this->getSortSQL($_sort);
			$filter = $this->getFilterSQL($_filter);
			
			$query = sprintf("select uid, date, subject, sender_name, sender_address, to_name, to_address from phpgw_felamimail_cache ".
					 "where accountid='%s' and hostname='%s' and foldername = '%s' and accountname='%s' %s $sort",
					 $this->accountid, $this->db->db_addslashes($this->hostname),
					 $this->db->db_addslashes($this->foldername), $this->db->db_addslashes($this->accountname),
					 $filter);

			$this->db->query($query,__LINE__,__FILE__);
			
			while($this->db->next_record())
			{
				// we found the current message
				if($this->db->f('uid') == $_uid)
				{
					// jump to the next messages
					if($this->db->next_record())
					{
						$retValue['next'] = $this->db->f('uid');
					}
					// we are done
					if($retValue) return $retValue;
					
					// we should never get here
					return false;
				}
				else
				{
					// we found (maybe!) the previous message
					$retValue['previous'] = $this->db->f('uid');
				}
			}
			
			// we should never get here
			return false;
		}
		
		function getSortSQL($_sort)
		{
			switch($_sort)
			{
				case "0":
					$sort = "order by date desc";
					break;
				case "1":
					$sort = "order by date asc";
					break;
				case "2":
					$sort = "order by sender_address desc";
					break;
				case "3":
					$sort = "order by sender_address asc";
					break;
				case "4":
					$sort = "order by subject desc";
					break;
				case "5":
					$sort = "order by subject asc";
					break;
				default:
					$sort = "order by date desc";
			}
			return $sort;
		}
		
		function removeFromCache($_uid)
		{
			$query = sprintf("delete from phpgw_felamimail_cache ".
					 "where accountid='%s' and hostname='%s' and foldername = '%s' and accountname='%s' ".
					 "and uid='%s'",
					 $this->accountid, $this->db->db_addslashes($this->hostname),
					 $this->db->db_addslashes($this->foldername), $this->db->db_addslashes($this->accountname),
					 $_uid);
			$this->db->query($query);
			
			#print "$query<br>";
		}
		
		function updateImapStatus($_status, $firstUpdate)
		{
			$query = sprintf("delete from phpgw_felamimail_folderstatus where ".
				 "accountid='%s' and hostname='%s' and foldername='%s' and accountname='%s'",
				 $this->accountid, $this->db->db_addslashes($this->hostname),
				 $this->db->db_addslashes($this->foldername), $this->db->db_addslashes($this->accountname));
			$this->db->query($query);

 			$values = array
			(
				$this->accountid,
				$this->db->db_addslashes($this->hostname),
				$this->db->db_addslashes($this->foldername),
				$this->db->db_addslashes($this->accountname),
				$_status->messages,
				$_status->recent,
				$_status->unseen,
				$_status->uidnext,
				$_status->uidvalidity
  	           );
  	 
  	        $values = $this->db->validate_insert($values);
  	        $query = "INSERT INTO phpgw_felamimail_folderstatus ".
				"(accountid,hostname,foldername,accountname,messages,recent,unseen,uidnext,uidvalidity) ".
				"values($values)";
				 
			$this->db->query($query,__LINE__,__FILE__);
		}
	}
?>
