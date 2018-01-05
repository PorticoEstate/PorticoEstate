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
		function append($stream, $folder = 'Sent', $header, $body, $flags=0)
		{
			// N/A for pop3
			return False;
		}

		function base64($text)
		{
			return imap_base64($text);
		}

		function close($stream,$flags=0)
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

		function delete($stream,$msg_num,$flags=0)
		{
			// do we force use of msg UID's 
			if ( ($this->force_msg_uids == True)
			&& (!($flags & FT_UID)) )
			{
				$flags |= FT_UID;
			}
			$retval = imap_delete($stream,$msg_num,$flags);
			// some lame pop3 servers need this extra call to expunge, but RFC says not necessary
			imap_expunge($stream);
			return $retval;
		}
     
		function expunge($stream)
		{
			// N/A for pop3
			return true;
		}
     
		function fetchbody($stream,$msgnr,$partnr,$flags=0)
		{
			// do we force use of msg UID's 
			if ( ($this->force_msg_uids == True)
			&& (!($flags & FT_UID)) )
			{
				$flags |= FT_UID;
			}
			return imap_fetchbody($stream,$msgnr,$partnr,$flags);
		}

		function fetchheader($stream,$msg_num,$flags=0)
		{
			// do we force use of msg UID's 
			if ( ($this->force_msg_uids == True)
			&& (!($flags & FT_UID)) )
			{
				$flags |= FT_UID;
			}
			return imap_fetchheader($stream,$msg_num,$flags);
		}

		function fetch_raw_mail($stream,$msg_num,$flags=0)
		{
			$flags |= FT_PREFETCHTEXT;
			// do we force use of msg UID's 
			if ( ($this->force_msg_uids == True)
			&& (!($flags & FT_UID)) )
			{
				$flags |= FT_UID;
			}
			return imap_fetchheader($stream,$msg_num,$flags);
		}

		function fetchstructure($stream,$msg_num,$flags=0)
		{
			// do we force use of msg UID's 
			if ( ($this->force_msg_uids == True)
			&& (!($flags & FT_UID)) )
			{
				$flags |= FT_UID;
			}
			return imap_fetchstructure($stream,$msg_num,$flags);
		}

		function get_body($stream,$msg_num,$flags=0)
		{
			// do we force use of msg UID's 
			if ( ($this->force_msg_uids == True)
			&& (!($flags & FT_UID)) )
			{
				$flags |= FT_UID;
			}
			return imap_body($stream,$msg_num,$flags);
		}

		function get_header($stream,$msg_num,$flags)
		{
			// alias for compatibility with some old code
			return $this->fetchheader($stream,$msg_num,$flags);
		}

		function header($stream,$msg_nr,$fromlength='',$tolength='',$defaulthost='')
		{
			// do we need to temporarily switch to regular msg num sequence for this function?
			if ($this->force_msg_uids == True)
			{
				// this function can nothandle UIDs, switch to sequence number
				$new_msg_nr = imap_msgno($stream,$msg_nr);
				if ($new_msg_nr)
				{
					$msg_nr = $new_msg_nr;
				}
			}
			return imap_header($stream,$msg_nr,$fromlength,$tolength,$defaulthost);
		}

		function listmailbox($stream,$ref,$pattern)
		{
			// N/A for pop3
			return False;
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

		function mail_move($stream,$msg_list,$mailbox,$flags)
		{
			// N/A for pop3
			return False;
		}

		function num_msg($stream) // returns number of messages in the mailbox
		{ 
			return imap_num_msg($stream);
		}
		
		function noop_ping_test($stream)
		{ 
			return imap_ping($stream);
		}

		function open($mailbox,$username,$password,$flags=0)
		{
			return imap_open($mailbox,$username,$password,$flags);
		}

		function qprint($message)
		{
			//      return quoted_printable_decode($message);
			$str = quoted_printable_decode($message);
			return str_replace("=\n",'',$str);
		}

		function reopen($stream,$mailbox,$flags='')
		{
			// N/A for pop3
			return False;
		}

		function server_last_error()
		{
			// supported in PHP >= 3.0.12
			//UNKNOWN if POP3 server errors also get put here
			return imap_last_error();
		}

		// does this work for pop3?
		function i_search($stream,$criteria,$flags=0)
		{
			// do we force use of msg UID's 
			if ( ($this->force_msg_uids == True)
			&& (!($flags & SE_UID)) )
			{
				$flags |= SE_UID;
			}
			return imap_search($stream,$criteria,$flags);
		}
		
		//function sort($stream,$criteria,$reverse='',$options='',$msg_info='')
		function sort($stream,$criteria,$reverse='',$flags=0)
		{
			// do we force use of msg UID's 
			if ( ($this->force_msg_uids == True)
			&& (!($flags & SE_UID)) )
			{
				$flags |= SE_UID;
			}
			return imap_sort($stream,$criteria,$reverse,$flags);
		}

		function status($stream,$mailbox,$options)
		{
			// don't forget pop3 has 1 "folder": INBOX, any other folder name will not work
			return imap_status($stream,$mailbox,$options);
		}

		function construct_folder_str($folder)
		{
			// pop3 has only 1 "folder" - inbox
			$folder = 'INBOX';
			$folder_str = $GLOBALS['phpgw']->msg->get_folder_long($folder);
			return $folder_str;
		}

		function deconstruct_folder_str($folder)
		{
			// pop3 has only 1 "folder" - inbox
			$folder = 'INBOX';
			$folder_str = $GLOBALS['phpgw']->msg->get_folder_short($folder);
			return $folder_str;
		}

	} // end of class msg
