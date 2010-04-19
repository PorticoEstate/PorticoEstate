<?php
	/**
	* Communik8r email abstraction layer
	*
	* @author Dave Hall skwashd@phpgroupware.org
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package communik8r
	* @subpackage comm
	* @version $Id: class.comm_email.inc.php,v 1.1.1.1 2005/08/23 05:03:51 skwashd Exp $
	*/

	/**
	* @see Mail_mimeDecode
	*/
	include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . './class.Mail_mimeDecode.inc.php');

	/**
	* Email abstraction library
	*
	* @abstract
	*/
	class comm_email
	{
		/**
		* @var array $acct_info Account information for the connection
		*/
		var $acct_info;
		
		/**
		*@constructor
		*/
		function comm_email($acct_info)
		{
			$this->acct_info = $acct_info;
		}
		
		/**
		* Append a message to a mailbox
		*
		* @internal not supported by POP3
		* @param string $mbox mailbox to store message in
		* @param string $content the contents as a RFC 2822 format message
		* @param array $flags the RFC 3501 flags to be used for the message ie \Seen
		* @returns int the UID of the new message - 0 on failure
		*/
		function append_msg($mbox, $content, $flags = array() )
		{
			return 0;
		}
		
		/**
		* Create a new mailbox
		*
		* @internal not supported by POP3
		* @param string $mbox the name of the new mailbox
		* @returns bool was the mailbox created?
		*/
		function create_mailbox($mbox)
		{
			return True;
		}

		/**
		* Copy a message from a folder to another
		*
		* @interal not supported by POP3
		* @param string $src_mbox the source mailbox
		* @param int|string $unique_id the UID|UIDL of the message
		* @param string $target the target mailbox
		* @returns bool was the message copied?
		*/
		function copy_msg($src_mbox, $unique_id, $target)
		{
			return True;
		}

		/**
		* Delete the specified mailbox
		*
		* @param string $mbox mailbox to delete
		* @bool was the mailbox deleted?
		*/
		function delete_mailbox($mbox)
		{
			return True;
		}

		/**
		* Delete a message from the mailbox
		*
		* @param string $mbox mailbox containing the message to delete
		* @param int|string $unique_id the UID|UIDL of the message
		* @param bool $expunge expunge message after delete?
		* @returns bool was the message deleted?
		*/
		function delete_msg($mbox, $unique_id)
		{}

		/**
		* Expunge the specified message
		*
		* @internal not implemented in POP3
		* @param string $mbox mailbox contianing the message
		* @param int|string $unique_id the UID|UIDL of the message
		* @bool was the message expunaged
		*/
		function expunge_msg($mbox, $unique_id)
		{
			return True;
		}

		/**
		* Expunge whole mailbox
		*
		* @internal POP3 should reconnect to server to force expunge on QUIT
		* @param string $mbox mailbox to expunge
		* @return bool was the mailbox expunaged?
		*/
		function expunge_mailbox($mailbox)
		{}

		/**
		* Get the epoch the mailbox was last updated
		*
		* @param string $mbox the mailbox to check
		* @returns int epoch of last mod
		*/
		function get_lastmod($mbox)
		{
			return time() + 10000; //make the mailbox always invalid
		}

		/**
		* Get a list of mailboxes available to the user
		*
		* @param bool $only_subd only get list of subscribed folders?
		* @returns array list of folders and some information about them key is folder name
		*/
		function get_mailbox_list($only_subd = True)
		{}

		/**
		* Get the requested properties for the mailbox
		*
		* @internal we do it this way cos courier imap is RFC ignorant on returning UIDNEXT when SELECTing
		* @param string $mbox mailbox for which properties are sought
		* @param array $props properties sought
		*/ 
		function get_mailbox_properties($mbox, $properties)
		{}

		/**
		* Convert generic mailbox name to a server specific mailbox string
		*
		* @internal default is for POP3
		* @param string $mbox mailbox name to convert
		* @returns server specific mailbox name
		*/
		function get_mailbox_string($mbox)
		{
			if ( strlen($mbox) )
			{
				return "INBOX.$mbox";
			}
			return 'INBOX';
		}

		/**
		* Get a list of unique ids for a mailbox
		*
		* @param string $mbox the mailbox to get list from
		* @returns array list of unique ids
		*/
		function get_list_unique_ids($mbox)
		{}

		/**
		* Retreive a message from the server
		*
		* @param string $mbox mailbox containing the message to retreive
		* @param int|string $unique_id the UID|UIDL of the message
		* @returns string the raw message
		*/
		function get_msg($mbox, $unique_id)
		{}

		/**
		* Rename a mailbox
		*
		* @internal not implemented in POP3
		* @param string $old_name the old name of the mailbox
		* @param string $new_name the new name of the mailbox
		* @returns bool was the mailbox renamed?
		*/
		function rename_mailbox($old_name, $new_name)
		{
			return True;
		}
		

		/**
		* Set RFC 3501 flag/s on a message
		*
		* @internal not supported by POP3
		* @param string $mbox mailbox to store message in
		* @param @param int|string $unique_id the UID|UIDL of the message
		* @param array $flags the RFC 3501 flags to be used for the message ie \Seen
		* @param bool $kill_old remove current flags on message? calls unset_flags()
		* @return bool was the flag/s set on the message?
		*/
		function set_flags($mbox, $unique_id, $flags, $kill_old = False)
		{
			return True;
		}

		/**
		* Subscribe to a mailbox
		*
		* @internal not supported by POP3
		* @param string $mbox name of mailbox to subscribe to
		* @return bool was subscription completed?
		*/
		function subscribe($mbox)
		{
			return True;
		}

		/**
		* Convert a string from UTF7
		*
		* @param string $str the string to convert
		* @param string $charset target charset
		*/
		function utf7_decode($str, $charset = '')
		{
			if( !$charset )
			{
				$charset = lang($charset);
			}

			if ( !trim($str) )
			{
				return '';
			}
			return mb_convert_encoding($str, $charset, 'UTF-7');
		}

		/**
		* Convert a string to UTF7
		*
		* @param string $str the string to convert
		* @param string $charset source charset
		*/
		function utf7_encode($str, $charset = '')
		{
			if( !$charset )
			{
				$charset = lang($charset) . ',utf-8';
			}
			mb_detect_order($charset);

			if ( !trim($str) )
			{
				return '';
			}
			return mb_convert_encoding($str, 'UTF-7');
		}
		
		/**
		* Map UID|UIDL to sequence number
		*
		* @private
		* @internal really only needed for POP3
		* @param int|string $unique_id the UID|UIDL to be mapped
		* @return int sequence number
		*/
		function unique2seq($unique_id)
		{}

		/**
		* Remove RFC 3501 flag/s on a message
		*
		* @internal not supported by POP3
		* @param string $mbox mailbox to store message in
		* @param @param int|string $unique_id the UID|UIDL of the message
		* @param array $flags the RFC 3501 flags to be used for the message - if count() == 0 then all removed
		* @bool was the flag/s removed from the message?
		*/
		function unset_flags($mbox, $unique_id, $flags = array() )
		{
			return True;
		}

		/**
		* Unsubscribe from a mailbox
		*
		* @internal not supported by POP3
		* @param string $mbox name of mailbox to unsubscribe from
		* @return bool was unsubscription completed?
		*/
		function unsubscribe($mbox)
		{
			return True;
		}

		/**
		* Connect to mailserver
		* @protected
		*/
		function _connect()
		{}

		/**
		* Disconnect from mailserver
		* @protected
		*/
		function _disconnect()
		{}

		/**
		* Change to selected mailbox
		* 
		* @protected
		* @param string $mbox mailbox to switch to
		* @returns bool was the change sucessful?
		*/
		function _switch_mailbox($mbox)
		{}
	}
?>
