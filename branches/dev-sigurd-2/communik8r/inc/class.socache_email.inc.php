<?php
	/**
	 * Communik8r email caching class
	 *
	 * @author Dave Hall skwashd at communik8r.org
	 * @copyright Copyright (C) 2005 Dave Hall skwashd at communik8r.org
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @package communik8r
	 * @package communik8r
	 * @subpackage cache
	 * @version $Id: class.socache_email.inc.php,v 1.3 2005/08/25 09:25:57 skwashd Exp $
	 */

	/**
	 * Communik8r email caching class
	 */
	class socache_email /*extends socache*/
	{
		/**
		 * @var array $acct_info information about account
		 */
		var $acct_info;

		/**
		 * @var object $db database abstraction object
		 */
		var $db;

		/**
		 * @var array $cache_data cache status data
		 */
		var $cache_data;

		/**
		 * @var object $mail mail abstraction library
		 */
		var $mail;

		/**
		 * @var object $session reference to sessions object
		 */
		var $session;

		/**
		 * @var string Unique message identifier column name
		 */
		var $uid_col;

		/**
		 * @var object $vfs reference to virtual file system obj
		 */
		var $vfs;

		/**
		 * @var string $vfs_base the base directory for all vfs operations
		 */
		var $vfs_base = '/communik8r/email/';

		/*
		 * @var string $vfs_pwd the current working dir in the vfs
		 */
		var $vfs_pwd = '';

		/**
		 * @constructor
		 *
		 * @param array $acct_info account information
		 */
		function socache_email($acct_info)
		{
			$valid_types = array
				(
				 'imap'	=> True,
				 'pop3'	=> True
				);
			if ( !$acct_info['type_name'] || !$valid_types[$acct_info['type_name']] )
			{
				trigger_error("Invalid account type_name: '{$acct_info['type_name']}'", E_USER_ERROR);
			}

			$this->acct_info = $acct_info;
			$this->db =& $GLOBALS['phpgw']->db;
			$this->mail = createObject("communik8r.comm_{$acct_info['type_name']}", $this->acct_info);
			$this->session = &$GLOBALS['phpgw']->session;
			$cached = $this->session->appsession('cache_data');
			$this->cache_data = ( isset($cached[$this->acct_info['acct_id']]) ? $cached[$this->acct_info['acct_id']] : array() );
			$this->uid_col = $this->_get_uid_col();

			$this->vfs = createObject('phpgwapi.vfs');
			$this->vfs->override_acl = 1;

			$this->vfs_base .= $acct_info['acct_id'] . '/';

			$this-> _vfs_mkdir_recursive($this->vfs_base);
		}

		/**
		 * Append a message to a mailbox
		 *
		 * @param string $mbox mailbox name
		 * @param string $msg the message to be stored
		 * @param array $flags imap flags
		 * @returns bool did it suceed?
		 */
		function append_msg($mbox, $msg, $flags = array() )
		{
			if( $uid = $this->mail->append_msg($mbox, $mg, implode(' ', $flags) ) )
			{
				return !!$this->_cache_msg($mbox, $uid);
			}
			return False;
		}

		/**
		 * Check a mailbox
		 */
		function check($mbox)
		{
			//compare last request to last update for mailbox?
		}

		/**
		 * Clear all RFC 2060 flags from a message
		 *
		 * @param string $mbbox the mailbox name
		 * @param int $uid message unique id
		 * @param string flag to be removed - must be one of Seen, Answered, Flagged Deleted or Draft
		 * @returns bool was the flag sucessfully removed
		 */
		function clear_flag($mbox, $uid, $flag)
		{
			$safe_flags = array('seen', 'answered', 'flagged', 'deleted', 'draft');

			if ( !in_array(strtolower($flag), $safe_flags) )
			{
				return false;
			}

			if( !$this->mail->unset_flags($mbox, $uid, $flag) )
			{
				return $ok;
			}

			$sql = 'UPDATE phpgw_communik8r_email_msgs SET flag_' . strtolower($flag) . ' = 0'
				. ' WHERE acct_id = ' . intval($this->acct_info['acct_id'])
				. " AND mailbox = '" . $this->db->db_addslashes($mbox) . "'"
				. ' AND msg_uid = ' . intval($uid);

			return !!$this->db->query($sql, __LINE__, __FILE__);
		}

		/**
		 * Copy message/s
		 *
		 * TODO Make this work
		 * Allows any user accessible message to be copied to a folder
		 *
		 * @param array $src list of db ids for the source message/s
		 * @param string $target target mailbox
		 * @return bool did it complete sucessfully?
		 */
		function copy_msg($src, $target)
		{
			return False;
		}

		/**
		 * Create a new mailbox
		 *
		 * @param string $mbox name of mailbox to create
		 * @returns bool was the new mailbox created?
		 */
		function create_mailbox($mbox)
		{
			if( count($this->_get_mailbox_info($mbox) ) )//already exists!
			{
				return True; //is usable so return True
			}

			if ( $this->mail->create_mailbox($mbox) )
			{
				return $this->subscribe($mbox);
			}
			return False;
		}

		/**
		 * Delete a mailbox
		 *
		 * @param string $mbox mailbox to be deleted
		 * @returns bool was the mailbox deleted?
		 */
		function delete_mailbox($mbox)
		{
			if ( $this->mail->mail_delete_mailbox($mbox) )
			{
				return $this->_delete_mailbox($mbox);
			}
			return false;
		}

		/**
		 * Marks message/s for deletition
		 *
		 * This function does not delete the message from the mailbox,
		 * use cache_expunge to do that.
		 *
		 * @param array $uri_parts the message to be deleted
		 * @param int|string message UIDs to be marked for deleted
		 * @returns bool was the message/s marked for deletion
		 */
		function delete_msg($msg)
		{
			$minfo = $this->_db2store($msg);
			$this->mail->is_connected();
			if ( !$this->mail->delete($minfo['mbox'], $minfo['uid']) )
			{
				trigger_error("\$this->mail->delete_msg({$minfo['mbox']}, {$minfo['uid']}) FAILED!");
				$this->mail->close();
				return False;//continuing is pointless
			}
			$this->mail->close();

			/*
			//FIXME handle n,n:n,n,n etc
			if ( ($pos = strpos(':', $uid) ) !== False )
			{
			$uids = explode(':', $uid);
			if ( count($uids) == 2 )
			{
			$uid_where = '(msg_uid >= ' . intval($uids[0]) 
			. 'AND msg_uid <= ' . intval($uids[1]) . ')';
			}
			else if ( count($uids) == 1 )
			{
			if ( $pos ) //nnn- 
			{
			$uid_where = 'msg_uid >= ' . intval($uids[0]);
			}
			else//must be -nnn
			{
			$uid_where = 'msg_uid <= ' . intval($uids[1]);
			}
			}
			else//invalid
			{
			return False;
			}
			}
			else
			{
			 */
			$uid_where = 'msg_uid = ' . intval($minfo['uid']);
			//}
			$sql = 'UPDATE phpgw_communik8r_email_msgs SET flag_deleted = 1'
				. ' WHERE acct_id = ' . intval($this->acct_info['acct_id'])
				. " AND mailbox = '" . $this->db->db_addslashes($mbox) . "'"
				. ' AND ' . $uid_where;

			return !!$this->db->query($sql, __LINE__, __FILE__);
		}

		/**
		 * Delete all messages marked for deletion
		 *
		 * @param string $mbox
		 * @returns bool were all messages deleted?
		 */
		function expunge_mailbox($mbox)
		{
			if ( $this->mail->expunge_mailbox($mbox) )
			{
				$this->_expunge($mbox);
			}
			return false;
		}

		/**
		 * Fetch the headers of a message
		 *
		 * @param string $mbox the mailbox which holds the message
		 * @param int $uid unique message uid
		 * @returns string message headers
		 */
		function fetch_headers($mbox, $uid)
		{
			return $this->_fetch_headers($mbox, $uid);
		}

		/**
		 * Fetch the structure of a message
		 * @param string $mbox the mailbox which holds the message
		 * @param int $uid unique message uid
		 * @returns object structure of the message
		 */
		function fetch_structure($mbox, $uid)
		{
			// FIXME!!! This won't work
			if ( !$this->_is_cached($mbox, $uid) )
			{
				$this->_cache_msg($mbox, $uid);
			}
			return $this->_db2obj($mbox, $uid);

		}				

		/**
		 * Get a list of mailboxes for account
		 *
		 * @return array list of folders
		 */
		function get_mailboxes($only_subd = True, $criteria = '')
		{
			if ( $this->_is_mailboxes_stale() )
			{
				$this->_update_mailboxes();
			}

			$sql = 'SELECT phpgw_communik8r_email_mboxes.mbox_id,'
				. ' phpgw_communik8r_email_mboxes.mbox_name,'
				. ' phpgw_communik8r_email_mboxes.seperator,'
				. ' phpgw_communik8r_email_mboxes.open_state'
				. ' FROM phpgw_communik8r_email_mboxes'
				. ' WHERE phpgw_communik8r_email_mboxes.acct_id = ' . intval($this->acct_info['acct_id']);

			if ( $only_subd )
			{
				$sql .= ' AND phpgw_communik8r_email_mboxes.subscribed = 1';
			}

			if ( strlen($criteria) )
			{
				$criteria = $this->db->db_addslashes( str_replace('*', '%', $criteria) );
				$sql .= " AND phpgw_communik8r_email_mboxes.mbox_name LIKE '" . $criteria . "'";
			}

			$sql .= ' ORDER BY phpgw_communik8r_email_mboxes.mbox_name';

			$this->db->query($sql, __LINE__, __FILE__);

			$mboxes = array();

			while ( $this->db->next_record() )
			{
				$mboxes[$this->db->f('mbox_id')] = array
					(
					 'name'		=> $this->db->f('mbox_name'),
					 'sep'		=> $this->db->f('seperator'),
					 'unread_msgs'	=> $this->db->f('unread_msgs'),
					 'open'		=> $this->db->f('open_state')
					);
			}

			if ( count($mboxes) )
			{
				$mboxes = $this->_mboxes2array($mboxes);
			}

			$this->cache_data['last_mailbox_poll'] = time();
			$this->_update_session();

			return $mboxes;
		}

		/**
		 * Retreive the main body of a message
		 *
		 * @param int $id Database primary key of message
		 * @param bool $mark_read mark message as read if not already read
		 * @param int $part_no the message part to retreive - 0 == main body
		 * @returns string message body
		 */
		function get_msg($id, $mark_read = True, $part_no = 0 )
		{	
			trigger_error("socache_email::get_msg({$id}, {$mark_read}, {$part_no})");
			if ( $part_no != 0 )
			{
				return $this->_get_part($id, $part_no);
			}

			$sql = "SELECT phpgw_communik8r_email_msgs.{$this->uid_col},"
				. ' phpgw_communik8r_email_msgs.structure,'
				. ' phpgw_communik8r_email_mboxes.mbox_name'
				. ' FROM phpgw_communik8r_email_msgs, phpgw_communik8r_email_mboxes'
				. ' WHERE phpgw_communik8r_email_msgs.mbox_id = phpgw_communik8r_email_mboxes.mbox_id'
				. ' AND phpgw_communik8r_email_mboxes.acct_id = ' . intval($this->acct_info['acct_id'])
				. ' AND phpgw_communik8r_email_msgs.msg_id=' . intval($id);

			$this->db->query($sql, __LINE__, __FILE__);
			if ( !$this->db->next_record() )
			{
				return '';
			}

			$msg = unserialize($this->db->f('structure', True));
//	$msg->mailbox = '"'.$msg->mailbox.'"';
			$mime = createObject('communik8r.mail_mime', '');
			$mime->struct = $msg->structure;
			$body_part = $mime->get_first_text_part(0);
			$msg->body_meta = $mime->get_part_array($body_part);
			$msg->parts = $mime->get_part_list(0);
//_debug_array($msg);
			unset($mime);

			$this->mail->is_connected();
			$msg->body = $this->mail->handle_body_part($msg->mailbox, $msg->uid, $body_part);
			$this->mail->close();

			if ( $mark_read )
			{
				$this->set_flags($msg->mailbox, $msg->uid, array('Seen') );
			}

			return $msg;
		}

		/**
		 * Fetch an overview of a mailbox
		 *
		 * @param string $mbox mailbox to get an overview of
		 * @returns array info about all messages
		 */
		function get_msg_list($mbox)
		{
			trigger_error("socache_email::get_msg_list({$mbox}) called");

			$this->_update($mbox);

			$msgs = array();
			$sql = 'SELECT phpgw_communik8r_email_msgs.msg_id,'
				. ' phpgw_communik8r_email_msgs.mbox_id,'
				. ' phpgw_communik8r_email_mboxes.mbox_name,'
				. ' phpgw_communik8r_email_msgs.msg_uid,'
				. ' phpgw_communik8r_email_msgs.msg_uidl,'
				. ' phpgw_communik8r_email_msgs.subject,'
				. ' phpgw_communik8r_email_msgs.sender,'
				. ' phpgw_communik8r_email_msgs.date_sent,'
				. ' phpgw_communik8r_email_msgs.msg_size,'
				. ' phpgw_communik8r_email_msgs.flag_seen,'
				. ' phpgw_communik8r_email_msgs.flag_answered,'
				. ' phpgw_communik8r_email_msgs.flag_deleted,'
				. ' phpgw_communik8r_email_msgs.flag_flagged,'
				. ' phpgw_communik8r_email_msgs.flag_draft,'
				. ' phpgw_communik8r_email_msgs.structure'
				. ' FROM phpgw_communik8r_email_msgs,'
				. ' phpgw_communik8r_email_mboxes'
				. ' WHERE phpgw_communik8r_email_msgs.mbox_id = phpgw_communik8r_email_mboxes.mbox_id'
				. " AND phpgw_communik8r_email_mboxes.mbox_name = '" . $this->db->db_addslashes($mbox) . "'"
				. ' AND phpgw_communik8r_email_mboxes.acct_id = ' . intval($this->acct_info['acct_id'])
				. ' ORDER BY phpgw_communik8r_email_msgs.date_sent DESC';

			$this->db->query($sql, __LINE__, __FILE__);

			while ( $this->db->next_record() )
			{
				$unique = ($this->db->f('msg_uid') ? $this->db->f('msg_uid') : $this->db->f('msg_uidl'));
				$msgs[$this->db->f('msg_id')] = array
					(
					 'mbox_id'	=> $this->db->f('mbox_id'),
					 'mbox_name'	=> $this->db->f('mbox_name'),
					 'unique'	=> $unique,
					 'sender'	=> $this->db->f('sender', True),
					 'subject'	=> $this->db->f('subject', True),
					 'date_sent'	=> $this->db->f('date_sent'),
					 'msg_size'	=> $this->db->f('msg_size'),
					 'flag_seen'	=> !!$this->db->f('flag_seen'),
					 'flag_answered'	=> !!$this->db->f('flag_answered'),
					 'flag_deleted'	=> !!$this->db->f('flag_deleted'),
					 'flag_flagged'	=> !!$this->db->f('flag_flagged'),
					 'flag_draft'	=> !!$this->db->f('flag_draft'),
					);
				$mime = createObject('communik8r.mail_mime', '' );
				$tmp = unserialize($this->db->f('structure', True) );
				$msgs[$this->db->f('msg_id')]['attachments'] = !!$mime->get_num_parts(0, $tmp->structure);
				unset($mime); unset($tmp);
			}
			return $msgs;
		}

		/**
		 * Get raw message as a string
		 */
		function get_raw_msg($msg_id)
		{
			$sql = 'SELECT phpgw_communik8r_email_mboxes.mbox_name,'
				. ' phpgw_communik8r_email_msgs.msg_uid'
				. ' FROM phpgw_communik8r_email_msgs,'
				. ' phpgw_communik8r_email_mboxes'
				. ' WHERE phpgw_communik8r_email_msgs.mbox_id = phpgw_communik8r_email_mboxes.mbox_id'
				. ' AND phpgw_communik8r_email_msgs.msg_id = ' . intval($msg_id);

			$this->db->query($sql, __LINE__, __FILE__);

			if( $this->db->next_record() )
			{
				return $this->mail->get_raw_msg($this->db->f('mbox_name', True), $this->db->f('msg_uid', True) );
			}
			return '';
		}

		/**
		 * Convert a mailbox db id to a name 
		 *
		 * @param int $id mailbox id
		 * @returns string mailbox name '' for fail
		 */
		function mboxid2name($id)
		{
			$sql = 'SELECT phpgw_communik8r_email_mboxes.mbox_name,'
				. ' FROM phpgw_communik8r_email_mboxes'
				. ' WHERE phpgw_communik8r_email_mboxes.acct_id = ' . intval($this->acct_info['acct_id'])
				. ' AND phpgw_communik8r_email_mboxes.mbox_id = ' . intval($id);

			$this->db->query($sql, __LINE__, __FILE__);
			if ( $this->db->next_record() )
			{
				$this->db->f(mbox_name, true);
			}
			return '';
		}

		/**
		 * Move message/s
		 * @see cache_copy
		 */
		function move_msg()
		{
			return False;
		}

		/**
		 * Get the number of messages in a mailbox
		 *
		 * @param string $mbox mailbox
		 * @returns int number of messages in folder (-1 if invalid mailbox)
		 */
		function num_msgs($mbox)
		{
			$sql = 'SELECT COUNT(phpgw_communik8r_email_msgs.msg_id) as cnt_msgs'
				. ' FROM phpgw_communik8r_email_msgs, phpgw_communik8r_email_mboxes'
				. ' WHERE phpgw_communik8r_email_msgs.mbox_id = phpgw_communik8r_email_mboxes.mbox_id'
				. " AND phpgw_communik8r_email_mboxes.mbox_name = '" . $this->db->db_addslashes($mbox) . "'"
				. ' AND phpgw_communik8r_email_mboxes.acct_id = ' . intval($this->acct_info['acct_id'])
				. ' AND phpgw_communik8r_email_msgs.flag_deleted = 0';

			$this->db->query($sql, __LINE__, __FILE__);
			if ( $this->db->next_record() )
			{
				return $this->db->f('cnt_msgs');
			}
			return 0;
		}

		/**
		 * Get the number of unread messages in a mailbox
		 *
		 * @param string $mbox mailbox
		 * @returns int number of unread messages in folder (-1 if invalid mailbox)
		 */
		function num_unread($mbox)
		{
			$sql = 'SELECT COUNT(phpgw_communik8r_email_msgs.msg_id) as cnt_msgs'
				. ' FROM phpgw_communik8r_email_msgs, phpgw_communik8r_email_mboxes'
				. ' WHERE phpgw_communik8r_email_msgs.mbox_id = phpgw_communik8r_email_mboxes.mbox_id'
				. " AND phpgw_communik8r_email_mboxes.mbox_name = '" . $this->db->db_addslashes($mbox) . "'"
				. ' AND phpgw_communik8r_email_mboxes.acct_id = ' . intval($this->acct_info['acct_id'])
				. ' AND phpgw_communik8r_email_msgs.flag_seen = 0'
				. ' AND phpgw_communik8r_email_msgs.flag_deleted = 0';

			$this->db->query($sql, __LINE__, __FILE__);
			if ( $this->db->next_record() )
			{
				return $this->db->f('cnt_msgs');
			}
			return 0;
		}

		/**
		 * Get address list as an array
		 *
		 * @param string $addrs addresses
		 * @returns array list of addresses
		 * TODO REMOVE - no longer needed
		 */
		function parse_adrlist($addrs)
		{
			$addresses = array();
			$addr_array = explode(',', $addrs);
			foreach ( $addr_array as $addr )
			{
				$cur_addr = array();
				$addr = trim($addr);
				if ( ($pos = strpos($addr, '<') ) === False)
				{
					list($cur_addr['mailbox'], $cur_addr['host']) = explode('@', $addr);
				}
				else
				{
					$cur_addr['personal'] = trim(substr($addr, 0, $pos));
					list($cur_addr['mailbox'], $cur_addr['host']) = explode('@', trim(substr($addr, ($pos + 1), -1) ) );
				}
				$addresses[] = $cur_addr;
			}
			return $addresses;
		}

		/**
		 * Rename a mailbox
		 *
		 * @param string $mbox current name of mailbox
		 * @param string $new_name new name of mailbox
		 * @returns bool was the mailbox renamed?
		 */
		function rename_mailbox($mbox, $new_name)
		{
			if ( $this->mail->rename_mailbox($mbox, $new_name) )
			{
				$sql = 'UPDATE phpgw_communik8r_email_mboxes'
					. " SET mbox_name = '" . $this->db->db_addslashes($new_name) . "',"
					. ' last_updated = ' . time()
					. " WHERE mbox_name = '"  . $this->db->db_addslashes($mbox) . "'"
					. ' AND acct_id = ' . intval($this->acct_info['acct_id']);

				$this->db->query($sql, __LINE__, __FILE__);
				return !!$this->db->affected_rows();
			}
			return False;
		}

		/**
		 * Search for messages meeting criteria
		 *
		 * @param string $criteria search criteria
		 * @param string $mbox mailbox to search on - all if ''
		 * @returns array summaries of messages found
		 * TODO Make this work
		 */
		function search($criteria, $mbox = '')
		{
		}

		/** 
		 * Set RFC 2060 flags from a message
		 *
		 * @param string $mbbox the mailbox name
		 * @param string $uid message unique id
		 * @param array $flags flag to be removed - must be one of Seen, Answered, Flagged, Deleted or Draft
		 * @returns bool was the flag sucessfully removed
		 */
		function set_flags($mbox, $uid, $flags)
		{
			if ( $this->mail->set_flags($mbox, $uid, $flags ) )
			{
				return !!$this->_set_flags($mbox, $uid, $flags);
			}
			return False;
		}

		/**
		 * Set a mailbox's "open" state in the mailbox tree view in the UI
		 *
		 * @param string $mailbox to set state of
		 * @param int $state the state of the mailbox
		 */
		function set_open($mailbox, $state)
		{
			trigger_error("setting {$mailbox} to {$state}");
			$state = intval($state);
			$mailbox = $this->db->db_addslashes($mailbox);

			$sql = 'UPDATE phpgw_communik8r_email_mboxes'
				. " SET open_state = {$state}"
				. " WHERE mbox_name = '{$mailbox}'"
				. ' AND phpgw_communik8r_email_mboxes.acct_id = ' . intval($this->acct_info['acct_id']);
			$this->db->query($sql, __FILE__, __LINE__);
		}

		/**
		 * Subscribe to specified mailbox
		 *
		 * @param sting $mbox mailbox to delete
		 * @return bool was the mailbox subscribed?
		 */
		function subscribe_mailbox($mbox)
		{
			if ( $this->mail->subscribe($mbox) )
			{
				return $this->_update($mbox);
			}
			return false;
		}

		/**
		 * Not yet implemented
		 * TODO Make this work!???
		 */
		function threaded()
		{}

		/**
		 * Undelete a message marked for deletion
		 *
		 * @param string $mbox mailbox
		 * @param int $uid message UID
		 * @returns bool was message undeleted?
		 */
		function undelete_msg($mbox, $uid)
		{
			if ( $this->mail->clear_flag($mbox, $uid, '\\Deleted') )
			{
				return $this->clear_flag($mbox, $uid, '\\Deleted');
			}
			return false;
		}

		/**
		 * Unsubscribe from specified mailbox
		 *
		 * @param sting $mbox mailbox to delete
		 * @return bool was the mailbox unsubscribed?
		 */
		function unsubscribe_mailbox($mbox)
		{
			if ( $this->mail->mail_unsubscribe($mbox) )
			{
				return $this->_delete_mailbox($mbox);//we don't need to cache unsub'd folders
			}
			return false;
		}

		/**
		 * Add messages to the cache
		 *
		 * @private
		 * @param string $mbox mailbox name
		 * @param string $uids message ids to cache
		 */
		function _cache_msgs($mbox, $uids)
		{
			$response = true;
			$msgs = $this->mail->fetch_headers($mbox, $uids);

			trigger_error("_cache_msgs({$mbox}, {$uids}) returned: " . print_r($msgs, True));
			foreach( $msgs as $uid => $msg)
			{
				foreach ( $msg as $prop => $val )
				{
					if ( strpos($val, '=') !== false )
					{
						$msg->$prop = $this->_decode($val);
					}
				}

				$mime = createObject('communik8r.mail_mime', $this->mail->fetch_structure_string($msg->mailbox, $msg->uid));
				$msg->structure = $mime->struct;
				unset($mime);

				trigger_error("attempting to cache: $uid => " . print_r($msg, true) );
				$sql = 'INSERT INTO phpgw_communik8r_email_msgs(mbox_id, ' . $this->uid_col . ', subject, sender, date_sent, '
						. ' msg_size, flag_seen, flag_answered, flag_deleted, flag_flagged, flag_draft, structure)'
					. 'VALUES(' . $this->_mailbox2id($msg->mailbox) . ', '
							. $this->_get_safe_uid($uid) . ', '
							. "'" . $this->db->db_addslashes($msg->subject) . "', "
							. "'" . $this->db->db_addslashes($msg->from) . "', "
							. intval($msg->timestamp) . ', '
							. intval($msg->size) . ', '
							. intval($msg->seen) . ', '
							. intval($msg->answered) . ', '
							. intval($msg->deleted) . ', '
							. intval($msg->flagged) . ', '
							. intval($msg->draft) . ', '
							. "'" . $this->db->db_addslashes(serialize($msg)) . "'"
							. ')';

							$this->db->query($sql, __LINE__, __FILE__);

							if ( ! $this->db->get_last_insert_id('phpgw_communik8r_email_msgs', 'msg_id') )
							{
							$response = false;
							trigger_error('Attempted to insert invalid message: ' . serialize($msg) );
							}

			}
			return $response;
		}

		/**
		 * Convert a database ID to the data needed to access the message in the mail store
		 *
		 * @private
		 * @param int $db_id db pk
		 * @returns array local store data
		 */
		function _db2store($db_pk)
		{
			$store = array
				(
				 'uid'	=> '',
				 'mbox'	=> ''
				);

			$sql = "SELECT {$this->uid_col}, mbox_name, structure"
				. ' FROM phpgw_communik8r_email_msgs, phpgw_communik8r_email_mboxes'
				. ' WHERE msg_id = ' . intval($db_pk) 
				. ' AND phpgw_communik8r_email_msgs.mbox_id = phpgw_communik8r_email_mboxes.mbox_id'
				. ' AND phpgw_communik8r_email_mboxes.acct_id = ' . intval($this->acct_info['acct_id']);

			$this->db->query($sql, __LINE__, __FILE__);

			if ( $this->db->next_record() )
			{
				$store['uid']		= $this->db->f($this->uid_col, True);
				$store['mbox']		= $this->db->f('mbox_name', True);
				$store['structure']	= unserialize($this->db->f('structure', True));
			}
			trigger_error("socache_email::_db2store({$db_pk}) results: {$sql} resulted in : " . implode(' ', $store));
			return $store;
		}

		/**
		 * Decode an encoded string
		 *
		 * @private
		 * @param string $str the encoded string
		 * @returns string decoded string
		 */
		function _decode($str)
		{
			//fix up email addreesses
			if ( strpos($str, '>') == strlen($str) -1 )
			{
				$tmp_str = explode('<', $str);
			}

			if( count($tmp_str) )
			{
				$str = trim($tmp_str[0]);
			}

			if(preg_match('/(=\?([^?]+)\?(Q|B)\?([^?]*)\?=)/i', $str, $matches) )
			{
				/*
				   array
				   (
				   raw		=> $matches[1],
				   charset		=> $matches[2],
				   encoding	=> $matches[3],
				   string		=> $matches[4]
				   )
				 */

				switch (strtoupper($matches[3]) )
				{
					case 'B':
						$matches[4] = base64_decode($matches[4]);
						break;
					case 'Q':
						$matches[4] = quoted_printable_decode($matches[4]);
				}

				$str = $matches[4];
				if ( strtolower($s) != 'utf-8' )
				{
					$str = mb_convert_encoding($str, 'utf-8'); //, $matches[2]);
				}

				//error_log("Converted {$matches[1]} (charset: {$matches[2]}) to {$s} " . __FILE__ . __LINE__);
			}

			if( count($tmp_str) )
			{
				return "{$str} <{$tmp_str[1]}";
			}

			return $str;
		}

		/**
		 * Extract the nominated part of the message
		 *
		 * @private
		 * @deprecated
		 */
		function _extract_part(&$mailparse, $part_no = 0)
		{
			if ( !strpos( $part_no, '.') )
			{
				if ( ($part_count = $mailparse->get_child_count() ) > 0 )
				{
					return $this->_extract_part($mailparse->get_child($part_no), $part_no);
				}
				else
				{
					return $mailparse->extract_body(MAILPARSE_EXTRACT_RETURN);
				}
			}
			else
			{
				$new_part_no = explode('.', $part_no);
				$part_no = array_shift($new_part_no);
				return $this->_extract_part($mailparse->get_child($part_no), implode('.', $new_part_no) );
			}
		}

		/**
		 * Get a part of a message
		 *
		 * @private
		 * @param int $id db pk for message
		 * @param string $part_no the part number for the message
		 * @returns string message part contents
		 */
		function _get_part($id, $part_no)
		{
			$part = array();
			$msg_info = $this->_db2store($id);
//_debug_array((array)$msg_info['structure']);
			if ( !($part_no && $msg_info['mbox'] && $msg_info['uid']) )
			{
				trigger_error('Invalid Part Request');
				return $part;
			}

		//	$part['structure'] = $mime->struct;
			$part['structure'] = $msg_info['structure'];

//_debug_array($part);die();
			$this->mail->is_connected();
			$part['content'] = $this->mail->handle_body_part($msg_info['mbox'], $msg_info['uid'], $part_no);
			$this->mail->close();
			return $part;
		}


		/**
		 * Make sure the supplied value is a database safe and valid Unique ID
		 *
		 * @private
		 * @param int|string|array $uid unique identifier
		 * @param bool quote the value if it is a string?
		 * @returns int|string unique identifier
		 */
		function _get_safe_uid($uid, $quote_string = True)
		{	
			if ( $this->uid_col == 'msg_uidl' )//string
			{
				if ( is_array($uid) )
				{
					$ids = array();
					foreach ( $uid as $id )
					{
						$ids[] = $this->_get_safe_uid($id);
					}
					return $ids;
				}
				else
				{
					return ($quote_string ? "'" . $this->db->db_addslashes($uid) . "'" : $this->db->db_addslashes($uid));
				}
			}
			else //int
			{
				if ( is_array($uid) )
				{
					$ids = array();
					foreach ( $uid as $id )
					{
						$ids[] = $this->_get_safe_uid($id);
					}
					return $ids;
				}
				else
				{
					return intval($uid);
				}
			}
		}

		/**
		 * Get the column name for the unique message identifier
		 *
		 * @private
		 * @returns string column name
		 */
		function _get_uid_col()
		{
			switch ( $this->acct_info['type_name'] )
			{
				case 'pop3':
					return 'msg_uidl';
				default:
					return 'msg_uid';
			}
		}


		/**
		 * Convert a header date to an UTC epoch
		 *
		 * @private
		 * @param array $date date parts
		 * @returns int date as epoch
		 */
		function _hdr_date2epoch($date)
		{	
			$months = array
				(
				 'jan' => 1,
				 'feb' => 2,
				 'mar' => 3,
				 'apr' => 4,
				 'may' => 5,
				 'jun' => 6,
				 'jul' => 7,
				 'aug' => 8,
				 'sep' => 9,
				 'oct' => 10,
				 'nov' => 11,
				 'dec' => 12
				);


			/* key mappings
			   0 => dow,
			   1 => day,
			   2 => month as string,
			   3 => year
			   4 => time
			   5 => tzoffset
			 */
			$date_parts = array();
			list(/* $date_parts['dow'] */, $date_parts['day'], $date_parts['month'], $date_parts['year'], 
					$date_parts['time'], $date_parts['tz_offet']) = $date;

			$date_parts['month'] = $months[strtolower($date_parts['month'])];

			$tmp_time = array();
			list($date_parts['hr'], $date_parts['min'], $date_parts['sec']) = explode(':', $date_parts['time']);
			unset($date_parts['time']);

			if ( strlen($date_parts['tz_offset']) == 5)
			{
				$hrs = intval( substr($date_parts['tz_offset'], 1, 2) );
				$mins = intval( substr($date_parts['tz_offset'], 3) );
				if ( (substr($date_parts['tz_offset'], 0, 1) == '+') )
				{
					$date_parts['hr'] -= $hrs;
					$date_parts['mins'] -= $mins;
				}
				else
				{
					$date_parts['hr'] += $hrs;
					$date_parts['mins'] += $mins;
				}
			}
			return mktime($date_parts['hr'], $date_parts['min'], $date_parts['sec'], 
					$date_parts['month'], $date_parts['day'], $date_parts['year']);
		}

		/**
		 * Check if a message is cached
		 *
		 * @private
		 * @internal TODO Make this work!
		 * @param int db pk for message
		 * @returns bool is the message cached?
		 */
		function _is_cached($id)
		{
			return True;
		}

		/**
		 * Check if mailbox list is stale
		 *
		 * @private
		 * @returns bool is list stale?
		 */
		function _is_mailboxes_stale()
		{
			if ( !isset($this->cache_data['last_mailbox_poll']) )
			{
				return True;
			}
			return ($this->cache_data['last_mailbox_poll'] + 3600) < time(); //good for 10mins
		}

		/**
		 * Convert a mailbox name to a DB PK
		 *
		 * @private
		 * @param string $mbox mailbox name
		 * @returns int db pk - 0 == not found
		 */
		function _mailbox2id($mbox)
		{
			$this->db->query('SELECT phpgw_communik8r_email_mboxes.mbox_id'
					. ' FROM phpgw_communik8r_email_mboxes'
					. " WHERE phpgw_communik8r_email_mboxes.mbox_name = '" . $this->db->db_addslashes($mbox) . "'"
					. ' AND phpgw_communik8r_email_mboxes.acct_id = ' . intval($this->acct_info['acct_id']),
					__LINE__, __FILE__);

			if ( $this->db->next_record() )
			{
				return $this->db->f('mbox_id');
			}
			return 0;
		}

		/**
		 * Convert mboxes db results to a nicely formatted array
		 *
		 * @private
		 * @param array $mboxes mailboxes result from db
		 * @param string $parent parent folder name
		 * @returns array mailboxes
		 */
		function _mboxes2array($mboxes, $parent = '')
		{
			$nu_mboxes = array();
			foreach ( $mboxes as $m_id => $minfo)
			{
				$mparts = explode($minfo['sep'], $minfo['name']);
				$ref =& $nu_mboxes;
				foreach ( $mparts as $mpart)
				{
					//echo "$mpart ";
					if ( !isset($ref['children'][$mpart]) )
					{
						$ref['children'][$mpart] = array
							(
							 'id'		=> $m_id,
							 'sep'		=> $minfo['sep'],
							 'unread_msgs'	=> $minfo['unread_msgs'],
							 'acct_id'	=> $this->acct_info['acct_id'],
							 'acct_handler'	=> $this->acct_info['handler'],
							 'open'		=> $minfo['open'],
							 'children'	=> array()
							);
					}
					$ref = &$ref['children'][$mpart];
				}
			}
			trigger_error('socache_email::_mboxes2array(' . print_r($mboxes, true) . ", {$parent}) resulted in : " .  print_r($nu_mboxes['children'], true) );
			return $nu_mboxes['children'];
		}

		/**
		 * Convert raw message to an imap_fetchstructure style object
		 *
		 * @private
		 * @param string $msg the raw message string
		 * @returns object message object
		 */
		function _msg_str2structure(&$msg)
		{
			if ( ! $GLOBALS['phpgw_info']['flags']['mailparse'] )
			{
				$structure = Mail_mimeDecode::decode( 
					array
					(
					 'input'		=> &$msg,
					 'crlf'		=> "\r\n",
					 'include_bodies'=> False,
					 'decode_bodies'	=> False
					)
				);
				if(isset($structure->headers['date']))
				{
					$structure->headers['date'] = $this->_hdr_date2epoch($structure->headers['date']);
				}
			}
			else
			{
				$handle = mailparse_msg_create();
				mailparse_msg_parse($handle, $msg);
				$structure = mailparse_msg_get_part_data($handle);
			}
			return $structure;
		}

			/**
			 * Clear all RFC 2060 flags from a message
			 *
			 * @private
			 * @param string $mbbox the mailbox name
			 * @param string $uid message unique id
			 * @param array $flags flag to be removed - must be one of Seen, Answered, Flagged, Deleted or Draft
			 * @returns bool was the flag sucessfully removed
			 */
			function _set_flags($mbox, $uid, $flags)
			{
				if ( !count($flags) )
				{
					return False;
				}

				$set_flags = array();
				foreach($flags as $flag)
				{
					$set_flags[] = 'flag_' . $this->db->db_addslashes(strtolower($flag)) . ' = 1';
				}

			$sql = 'SELECT mbox_id FROM phpgw_communik8r_email_mboxes '
				. " WHERE mbox_name = '" . $this->db->db_addslashes($mbox) . "'"
				. ' AND acct_id = ' . intval($this->acct_info['acct_id']);

			$this->db->query($sql, __LINE__, __FILE__);

			if ( !$this->db->next_record() )
			{
				return false;
			}
			$mbox_id = intval($this->db->f('mbox_id'));

			$sql = 'UPDATE phpgw_communik8r_email_msgs'
					. ' SET ' . implode(', ', $set_flags)
					. " WHERE {$this->uid_col} = " . $this->_get_safe_uid($uid)
					. " AND phpgw_communik8r_email_msgs.mbox_id = {$mbox_id}";

				$this->db->query($sql, __LINE__, __FILE__);
				return ($this->db->affected_rows() == 1 || $this->db->affected_rows() == 0);
			}

			/**
			 * Switch to another mailbox - best to call before any VFS functions are called
			 * Will automagically create any path components which don't exist
			 *
			 * @private
			 * @param string $mbox mailbox name
			 * @returns bool did the switch suceed?
			 */
			function _switch_mailbox($mbox)
			{
				$path = "{$this->vfs_base}{$mbox}";
				//error_log("switching from: {$this->vfs_pwd} - Switching to: $path");
				if( $this->vfs_pwd == $path )
				{
					return True;
				}

				if ( !$this->vfs->file_exists( array( 'string' => $path, 'relatives' => array(RELATIVE_ROOT) ) ) )
				{
					if ( $this->_vfs_mkdir_recursive($path) )
					{
						$this->vfs_pwd = $path;
						return True;
					}
				}
				$this->vfs_pwd = $path;
				return True;
			}

			/**
			 * Update the cache for the specified mailbox
			 *
			 * @private
			 * @param string $mbox db primary key for mailbox
			 */
			function _update($mbox)
			{
				trigger_error("socache_email::_update({$mbox}) called");
				$mbox_slash =  $this->db->db_addslashes($mbox);
				$acct_id = intval($this->acct_info['acct_id']);

				$this->mail->is_connected();
				$tmp = $this->mail->get_mailbox_properties($mbox, array('uidnext', 'uidvalidity') );

				$m_uidnext = (int)$tmp['uidnext'];
				$m_uidvalidity = (int)$tmp['uidvalidity'];
				unset($tmp);

				$this->db->query('SELECT mbox_id, uidvalidity, uidnext FROM phpgw_communik8r_email_mboxes'
						. " WHERE mbox_name = '{$mbox_slash}' AND acct_id = {$acct_id}",
						__LINE__, __FILE__);

				if ( !$this->db->next_record() )
				{
					return false; //invalid mailbox
				}

				// Cross RDBMS compat, and makes DELETEs faster
				$c_mbox_id = $this->db->f('mbox_id');

				if(  $m_uidnext == ($c_uidnext = $this->db->f('uidnext') )
						&& $m_uidvalidity == ( $c_uidvalidity = $this->db->f('uidvalidity') )
						&& ($m_msg_cnt = $this->mail->count_messages($mbox, True) ) == ($c_msg_cnt = $this->num_msgs($mbox) ) )
				{
					return True;//nothing to sync :)
				}

				trigger_error("mbox: {$mbox} UIDNEXT cache:{$c_uidnext} server:{$m_uidnext} UIDVALIDITY cache:{$c_uidvalidity} server:{$m_uidvalidity} COUNT cache:{$c_msg_cnt} server:{$m_msg_cnt}");

				if ( $m_uidvalidity != $c_uidvalidity )//UIDs can't be syncd so rebuild cache
				{
					$this->db->query('DELETE FROM phpgw_communik8r_email_msgs'
							. " WHERE mbox_id = $c_mbox_id", __LINE__, __FILE__);

				}

				$uids = $this->mail->fetch_uids($mbox);

				//echo '<pre>' . print_r($uids, True) . '</pre>';
				if ( !count($uids) )//mailbox is empty!
				{
					$sql = 'DELETE FROM phpgw_communik8r_email_msgs'
						. " WHERE phpgw_communik8r_email_msgs.mbox_id = {$c_mbox_id}";

					$this->db->query($sql, __LINE__, __FILE__);

					$this->mail->close();
					return array();
				}

				$uids_str = implode(',', $this->_get_safe_uid( $uids ) );
				//ditch the old messages which no longer exist

				$sql = 'DELETE FROM phpgw_communik8r_email_msgs'
					. " WHERE {$this->uid_col} NOT IN({$uids_str})"
					. " AND phpgw_communik8r_email_msgs.mbox_id = {$c_mbox_id}";

				//$this->db->query($sql, __LINE__, __FILE__);

				$sql = "SELECT phpgw_communik8r_email_msgs.{$this->uid_col}"
					. ' FROM phpgw_communik8r_email_msgs, phpgw_communik8r_email_mboxes'
					. ' WHERE phpgw_communik8r_email_msgs.mbox_id = phpgw_communik8r_email_mboxes.mbox_id'
					. " AND phpgw_communik8r_email_mboxes.mbox_name = '" . $this->db->db_addslashes($mbox) . "'"
					. ' AND phpgw_communik8r_email_mboxes.acct_id = ' . intval($this->acct_info['acct_id']);

				$this->db->query($sql, __LINE__, __FILE__);
				$uid_vals = array_flip($uids);
				while ( $this->db->next_record() )
				{
					unset($uid_vals[$this->db->f($this->uid_col)]);
				}

				if ( count($uid_vals) )
				{
					$this->_cache_msgs($mbox, implode(',', array_keys($uid_vals) ) );
				}

				//make sure UIDs are in sync
				$sql = 'UPDATE phpgw_communik8r_email_mboxes '
					. " SET uidnext = {$m_uidnext},"
					. " uidvalidity = {$m_uidvalidity}"
					. " WHERE mbox_name = '"  . $this->db->db_addslashes($mbox) . "'"
					. " AND acct_id = {$this->acct_info['acct_id']}";

				$this->db->query($sql, __LINE__, __FILE__);
				$this->mail->close();
				return True;
			}

			/**
			 * Update the mailboxes stored in the cache
			 * @private
			 */
			function _update_mailboxes()
			{
				if ( $this->acct_info['type_name'] != 'pop3' )//FIXME make POP3 class return "INBOX" $sep = '.'
				{
					if ( !$this->mail->is_connected() )
					{
						trigger_error('Unable to connect to IMAP server', E_USER_ERROR);
					}

					$this->mail->get_namespace();
					$sep = $this->db->db_addslashes($this->mail->get_hierarchy_delimiter());

					$mboxes = $this->mail->list_mailboxes();
					$this->mail->close();
					array_walk($mboxes, 'dbslashes');

					//error_log('mailboxes: ' . print_r($mbox_keys, True) . 'Line: ' . __LINE__ . ' in ' . __FILE__);

					//Delete the CRUD
					$sql = 'DELETE FROM phpgw_communik8r_email_mboxes'
						. " WHERE acct_id = {$this->acct_info['acct_id']}"
						. " AND mbox_name NOT IN('" . implode("','",  $mboxes) . "')";

					$this->db->query($sql, __LINE__, __FILE__);

					foreach($mboxes as $mbox)
					{
						$sql = 'SELECT mbox_id '
							. ' FROM phpgw_communik8r_email_mboxes'
							. " WHERE acct_id = {$this->acct_info['acct_id']}"
							. " AND mbox_name = '{$mbox}'";

						$this->db->query($sql, __LINE__, __FILE__);
						if ( !$this->db->next_record() )
						{
							$sql = 'INSERT INTO phpgw_communik8r_email_mboxes'
								. '(mbox_name, seperator, acct_id)'
								. "VALUES('{$mbox}', "
								. "'{$sep}', "
								. $this->acct_info['acct_id'] . ')';

							$this->db->query($sql, __LINE__, __FILE__);
						}
					}
				}
			}

			/**
			 * Save the session cache data
			 * @private
			 */
			function _update_session()
			{
				$cached = $this->session->appsession('cache_data');
				$cached[$this->acct_info['acct_id']] = $this->cache_data;
				$this->session->appsession('cache_data', 'communik8r', $cached);
			}

			/**
			 * Check that a mailbox is valid
			 *
			 * @private
			 * @param string $mbox mailbox name
			 * @param bool $update_list update list of mailboxes first?
			 * @returns bool is the mailbox valid?
			 */
			function _verify_mbox($mbox, $update_list = True)
			{
				return True;
				$sql = 'SELECT mbox_id, uidvalidity, uidnext'
					. ' FROM phpgw_communik8r_email_mboxes'
					. " WHERE mbox_name = '"  . $this->db->db_addslashes($mbox) . "'"
					. ' AND acct_id = ' . intval($this->acct_info['acct_id']);


				$this->db->query($sql, __LINE__, __FILE__);
				if ( !$this->db->next_record() )
				{
					if ( false ) //$update_list ) //FIXME Drop this?
					{
						$this->_update_mailboxes();
						return $this->_verify_mbox($mbox, False);
					}
					return False;
				}
				return True;
			}

			/**
			 * Recursively creates a directory
			 *
			 * @private
			 * @param string $path the name of the path to create
			 */
			function _vfs_mkdir_recursive($path)
			{
				$this->vfs->cd(
						array
						(
						 'string'	=> False,
						 'relatives'	=> array(RELATIVE_ROOT),
						)
					      );

				$path_bits = explode('/', $path);
				array_pop($path_bits);
				$new_path = implode('/', $path_bits);
				unset($path_bits);

				if ( $new_path && !$this->vfs->file_exists( array( 'string' => $new_path, 'relatives' => array(RELATIVE_ROOT) ) ) )
				{
					$this->_vfs_mkdir_recursive($new_path);
				}

				return $this->vfs->mkdir( array( 'string' => $path, 'relatives' => array (RELATIVE_ROOT) ) );
			}

			/**
			 * Save a raw message in the VFS
			 *
			 * @private
			 * @param string $mbox the mailbox name
			 * @param string $msg the raw message
			 * @param int $msg_id the database pk for the message
			 */
			function _write2vfs($mbox, &$msg, $msg_id)
			{
				$this->_switch_mailbox($mbox);
				return $this->vfs->write(array
						(
						 'content'	=> $msg,
						 'string'	=> "{$this->vfs_pwd}/{$msg_id}.eml",
						 'relatives'	=> RELATIVE_ALL
						)
						);
			}
	}

	/**
	 * Simple wrapper for phpgw::db::db_addslashes()
	 *
	 * @internal used by array walk
	 * @param string $str string to escape for use in the db
	 * @returns string escaped string
	 */
	function dbslashes($str)
	{
		return $GLOBALS['phpgw']->db->db_addslashes($str);
	}
?>
