<?php
	/**
	* EMail - POP3 Mail Wrapper for Imap Enabled PHP
	*
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package email
	* @version $Id$
	*/


	/**
	* POP3 Mail Wrapper for Imap Enabled PHP
	*
	* @package email
	* @ignore
	*/	
	class mail_dcom extends mail_dcom_base
	{
		function base64($text)
		{
			return imap_base64($text);
		}

		function close($stream,$flags='')
		{
			return imap_close($stream,$flags);
		}

		function createmailbox($stream,$mailbox) 
		{
			// N/A for pop3
			return true;
		}

		function deletemailbox($stream,$mailbox)
		{
			// N/A for pop3
			return true;
		} 

		function delete($stream,$msg_num,$flags='',$currentfolder='')
		{
			return imap_delete($stream,$msg_num);
		}
     
		function expunge($stream)
		{
			// N/A for pop3
			return true;
		}
     
		function fetchbody($stream,$msgnr,$partnr,$flags='')
		{
			return imap_fetchbody($stream,$msgnr,$partnr,$flags);
		}

		function header($stream,$msg_nr,$fromlength='',$tolength='',$defaulthost='')
		{
			return imap_header($stream,$msg_nr,$fromlength,$tolength,$defaulthost);
		}

		function fetch_raw_mail($stream,$msg_num)
		{
			return imap_fetchheader($stream,$msg_num,FT_PREFETCHTEXT);
		}

		function fetchheader($stream,$msg_num)
		{
			return imap_fetchheader($stream,$msg_num);
		}

		function get_header($stream,$msg_num)
		{
			// alias for compatibility with some old code
			return $this->fetchheader($stream,$msg_num);
		}

		function fetchstructure($stream,$msg_num,$flags='')
		{
			return imap_fetchstructure($stream,$msg_num);
		}

		function get_body($stream,$msg_num,$flags='')
		{
			return imap_body($stream,$msg_num,$flags);
		}

		function listmailbox($stream,$ref,$pattern)
		{
			// N/A for pop3
			return False;
		}

		function num_msg($stream) // returns number of messages in the mailbox
		{ 
			return imap_num_msg($stream);
		}

		function mailboxmsginfo($stream) 
		{
			return imap_mailboxmsginfo($stream);
		}

		function mailcopy($stream,$msg_list,$mailbox,$flags)
		{
			// N/A for pop3
			return False;
		}

		function mail_move($stream,$msg_list,$mailbox)
		{
			// N/A for pop3
			return False;
		}

		function open($mailbox,$username,$password,$flags='')
		{
			return imap_open($mailbox,$username,$password,$flags);
		}

		function qprint($message)
		{
			// return quoted_printable_decode($message);
			$str = quoted_printable_decode($message);
			return str_replace("=\n",'',$str);
		} 

		function reopen($stream,$mailbox,$flags='')
		{
			// N/A for pop3
			return False;
		}

		function sort($stream,$criteria,$reverse='',$options='',$msg_info='')
		{
			return imap_sort($stream,$criteria,$reverse,$options);
		}

		function status($stream,$mailbox,$options)
		{
			return imap_status($stream,$mailbox,$options);
			//return imap_num_recent($stream);
		}

		function append($stream,$folder='Sent',$header,$body,$flags='')
		{
			// N/A for pop3
			return False;
		}

		function login($folder='INBOX')
		{
			//$debug_logins = True;
			$debug_logins = False;
			if($debug_logins)
			{
				echo 'CALL TO LOGIN IN CLASS MSG POP3'.'<br />'.'userid='.$GLOBALS['phpgw_info']['user']['preferences']['email']['userid'];
			}
	
			error_reporting(error_reporting() - 2);
			if($folder!='INBOX')
			{
				// pop3 has only 1 "folder" - inbox
				$folder='INBOX';
			}

			// WORKAROUND FOR BUG IN EMAIL CUSTOM PASSWORDS (PHASED OUT 7/2/01)
			// $pass = $this->get_email_passwd();
			// === ISSET CHECK ==
			if ( (isset($GLOBALS['phpgw_info']['user']['preferences']['email']['userid']))
				&& ($GLOBALS['phpgw_info']['user']['preferences']['email']['userid'] != '')
				&& (isset($GLOBALS['phpgw_info']['user']['preferences']['email']['passwd']))
				&& ($GLOBALS['phpgw_info']['user']['preferences']['email']['passwd'] != '') )
			{
				$user = $GLOBALS['phpgw_info']['user']['preferences']['email']['userid'];
				$pass = $GLOBALS['phpgw_info']['user']['preferences']['email']['passwd'];
			}
			else
			{
				// problem - invalid or nonexistant info for userid and/or passwd
				return False;
			}

			$server_str = $GLOBALS['phpgw']->msg->get_mailsvr_callstr();
			$mbox = $this->open($server_str.$folder, $user, $pass);

			error_reporting(error_reporting() + 2);
			return $mbox;
		}

		function construct_folder_str($folder)
		{
			$folder_str = $GLOBALS['phpgw']->msg->get_folder_long($folder);
			return $folder_str;
		}

		function deconstruct_folder_str($folder)
		{
			$folder_str = $GLOBALS['phpgw']->msg->get_folder_short($folder);
			return $folder_str;
		}
	}
?>