<?php
	/**
	* EMail - Handles specific operations in dealing with IMAP via Sockets
	*
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2001-2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2001-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/


	/**
	* IMAP module FOR SOCKETS, replaces php IMAP extension
	*
	* In around summer 2001, Mark Peeters "skeeter" emailed me "Angles" some 
	* files which were the skeleton of a sockets replacement for the php IMAP 
	* extensions. I believe skeeter wrote the base "network" class in the phpgwapi, 
	* and these files extended that class, an excellent approach. Skeeter covered 
	* the low level network functionality and also had some higher level sockets 
	* functionality that appeared to be for NNTP. To the extent any function in 
	* this class uses any of skeeters code, even when that code may be just one 
	* line, skeeter is listed as an author for that function.
	* @package email
	* @ignore
	*/	
	class mail_dcom extends mail_dcom_base
	{

		/**
		* This stops users from using email if they do not have php-imap enabled
		*
		* @author Dave Hall
		*/
		function __construct()
		{
			echo "<br />\n" . lang('php-imap support not installed - exiting') . "<br />\n";
			echo lang('contact your system administrator');
			exit;
		}
		
		/**************************************************************************\
		*	data analysis specific to IMAP data communications
		\**************************************************************************/

		/*!
		@function str_begins_with
		@abstract determine if string $haystack begins with string $needle
		@param $haystack (string) data to examine to determine if it starts with $needle
		@param $needle (string) $needle should or should not start at position 0 (zero) of $haystack
		@author Angles
		@result (Boolean) True or False
		@discussion this is a NON-REGEX way to to so this, and is NOT case sensitive
		this *should* be faster then Regular expressions and *should* not be confused by
		regex special chars such as the period "." or the slashes "/" and "\" , etc...
		@access public or private
		*/
		function str_begins_with($haystack,$needle='')
		{
			if ((trim($haystack) == '')
			|| (trim($needle) == ''))
			{
				return False;
			}
			/*
			// now do a case insensitive search for needle as the beginning part of haystack
			if (stristr($haystack,$needle) == False)
			{
				// needle is not anywhere in haystack
				return False;
			}
			// so needle IS in haystack
			// now see if needle is the same as the begining of haystack (case insensitive)
			if (strpos(strtolower($haystack),strtolower($needle)) == 0)
			{
				// in this case we know 0 means "at position zero" (i.e. NOT "could not find")
				// because we already checked for the existance of needle above
				return True;
			}
			else
			{
				return False;
			}
			*/
			// now do a case insensitive search for needle as the beginning part of haystack
			// stristr returns everything in haystack from the 1st occurance of needle (including needle itself)
			//   to the end of haystack, OR returns FALSE if needle is not in haystack
			$stristr_found = stristr($haystack,$needle);
			if ($stristr_found == False)
			{
				// needle is not anywhere in haystack
				return False;
			}
			// so needle IS in haystack
			// if needle starts at the beginning of haystack then stristr will return the entire haystack string
			// thus strlen of $stristr_found and $haystack would be the same length
			if (strlen($haystack) == strlen($stristr_found))
			{
				// needle DOES begin at position zero of haystack
				return True;
			}
			else
			{
				// where ever needle is, it is NOT at the beginning of haystack
				return False;
			}
		}
		
		/*!
		@function imap_read_port
		@abstract reads data from an IMAP server until the line that begins with the specified param "cmd_tag"
		@param $cmd_tag (string) the special string that indicates a server is done sending data
		this is generally the same "tag" identifier that the client sent when initiate the command, ex. "A001"
		@author Angles, skeeter
		@result array where each line of the server data exploded at every CRLF pair into an array
		@discussion IMAP servers send out data that is fairly well "typed", meaning RFC2060
		is pretty strict about what the server may send out, allowing the client (us) to more easily
		interpet this data. See syntax for a description.
		@syntax The important indicator is the string at the beginning of each line of data from the server, it can be
		"*" (astrisk) = "untagged" =  means "this line contains server data and more data will follow"
		"+" (plus sign) means "you, the client, must now finish sending your data to the server"
		"tagged" is the command tag that the client used to initiate this command, such as "A001"
		IMAP server's final line of data for that command will contain that command's tag as sent from the client
		The tagged "command completion" signal is followed by either 
		"OK" = successful command completion
		"NO" = failure of some kind
		"BAD" = protocol error such as unrecognized command or syntax error, client should abort this command processing
		@access private
		*/
		function imap_read_port($cmd_tag='')
		{
			// the $cmd_tag OK, BAD, NO line that marks the completion of server data
			// is not actually considered data
			// to put this line in the return data array may confuse the calling function
			// so it will go in $this->server_last_ok_response
			// for inspection by the calling function if so desired
			// so clear it of any left over value from a previous request
			$this->server_last_ok_response = '';
			
			// we return an array of strings, so initialize an empty array
			$return_me = Array();
			// is we do not know what to look for as an end tag, then abort
			if ($cmd_tag == '')
			{
				return $return_me;
			}
			// read the data until a tagged command completion is encountered
			while ($line = $this->read_port())
			{
				if ($this->str_begins_with($line, $cmd_tag) == False)
				{
					// continue reading from this port
					// each line of data from the server goes into an array
					$next_pos = count($return_me);
					$return_me[$next_pos] = $line;
				}
				// so we have a cmd_tag, is it followed by OK ?
				elseif ($this->str_begins_with($line, $cmd_tag.' OK'))
				{
					// we got a tagged command response OK
					// but if we send an empty array under this test error scheme
					// calling function will think there was an error
					// DECISION: if array is count zero, put this OK line in it
					// otherwise array already had valid server data in it
					// FIXME: and we do not want to add this OK line which is NOT actually data
					// FIXME: OR we ALWAYS add the final OK line and expect calling function
					// to ignore it ????
					if (count($return_me) == 0)
					{
						// add this OK line just to return a NON empty array
						$return_me[0] = $line;
					}
					else
					{
						// valid server data ALREADY exists in the return array
						// to add this final OK line *MAY* confuse the calling function
						// because this final OK line is NOT actually server data
						// THEREFOR: put the OK line in $this->server_last_ok_response for inspection
						// by the calling function if so desired
						$this->server_last_ok_response = $line;
					}
					// END READING THE PORT
					// in any case, we reached the end of server data
					// so we must break out of this loop
					break;
				}
				// not an OK tag, was it an understandable error NO or BAD ?
				elseif (($this->str_begins_with($line, $cmd_tag.' NO'))
				|| ($this->str_begins_with($line, $cmd_tag.' BAD')))
				{
					// error analysis, we have a useful error response from the server
					// put that error string into $this->server_last_error_str
					$this->server_last_error_str = $line;
					// what should we return here IF there was a NO or BAD error ?
					// how about an empty array, how about FALSE ??
						
					// TEST THIS ERROR DETECTION - empty array = error (BAD or NO)
					// empty the array
					$return_me = Array();
					// END READING THE PORT
					// in any case (BAD or NO)
					// we reached the end of server data
					// so we must break out of this loop
					break;
				}
				else
				// so not OK and not a known error, log the unknown error
				{
					// error analysis, generic record of unknown error situation
					// put that error string into $this->server_last_error_str
					$this->server_last_error_str = 'imap unknown error in imap_read_port: "'.$line.'"';
					// what should we return here IF there was a NO or BAD error ?
					// how about an empty array, how about FALSE ??
						
					// TEST THIS ERROR DETECTION - empty array = error (BAD or NO)
					// empty the array
					$return_me = Array();
					// END READING THE PORT
					// in any case (unknown data after $cmd_tag completion)
					// we reached the end of server data
					// so we must break out of this loop
					break;
				}
			}
			return $return_me;
		}
		
		/*!
		@function report_svr_data
		@abstract reports server data array for debugging purposes
		@param $data_array (array) server response data as returned by function "imap_read_port" as an array
		@param $calling_func_name (string) for debugging info, the name of the calling function
		@param $show_ok_msg (boolean) default TRUE, the last line of server data is not really data, just an 
		indication that the server has finished sending its data. Set this to TRUE to include that line in the output.
		@author Angles
		@result  none, this function DIRECTLY echos multiline data, nothing is returned from this function
		@access private
		*/
		function report_svr_data($data_array, $calling_func_name='', $show_ok_msg=True)
		{
			echo 'imap: '.$calling_func_name.': response_array line by line:<br />';
			for ($i=0; $i<count($data_array); $i++)
			{
				echo ' -- ArrayPos['.$i.'] data: ' .htmlspecialchars($data_array[$i]) .'<br />';
			}
			echo 'imap: '.$calling_func_name.': =ENDS= response_array line by line:<br />';
			if ($show_ok_msg == True)
			{
				echo 'imap: '.$calling_func_name.': last server completion line: "'.htmlspecialchars($this->server_last_ok_response).'"<br />';
			}
		}
		
		/*!
		@function server_last_error
		@abstract implements IMAP_LAST_ERROR
		@result string
		@author Angles
		@discussion UNDER CONSTRUCTION
		@access public
		*/
		function server_last_error()
		{
			if ($this->debug_dcom >= 1) { echo 'imap: call to server_last_error<br />'; }
			return $this->server_last_error_str;
		}
		
		
		/**************************************************************************\
		*	Functions NOT YET IMPLEMENTED
		\**************************************************************************/
		/*!
		@function createmailbox
		@abstract not yet implemented in IMAP sockets module
		*/
		function createmailbox($stream,$mailbox) 
		{
			// not yet implemented
			if ($this->debug_dcom >= 1) { echo 'imap: call to unimplemented socket function: createmailbox<br />'; }
			// eventually this will use this function to call for expiration of stale cached data, if any
			//$this->folder_list_did_change();
			return true;
		}
		/*!
		@function deletemailbox
		@abstract not yet implemented in IMAP sockets module
		*/
		function deletemailbox($stream,$mailbox)
		{
			// not yet implemented
			if ($this->debug_dcom >= 1) { echo 'imap: call to unimplemented socket function: deletemailbox<br />'; }
			// eventually this will use this function to call for expiration of stale cached data, if any
			//$this->folder_list_did_change();
			return true;
		}
		/*!
		@function renamemailbox
		@abstract not yet implemented in IMAP sockets module
		*/
		function renamemailbox($stream,$mailbox_old,$mailbox_new)
		{
			// not yet implemented
			if ($this->debug_dcom >= 1) { echo 'imap: call to unimplemented socket function: renamemailbox<br />'; }
			// eventually this will use this function to call for expiration of stale cached data, if any
			//$this->folder_list_did_change();
			return true;
		}
		
		/*!
		@function expunge
		@abstract not yet implemented in IMAP sockets module
		*/
		function expunge($stream)
		{
			// not yet implemented
			if ($this->debug_dcom >= 1) { echo 'imap: call to unimplemented socket function: expunge<br />'; }
			return true;
		}
		/*!
		@function mailcopy
		@abstract not yet implemented in IMAP sockets module
		*/
		function mailcopy($stream,$msg_list,$mailbox,$flags)
		{
			// not yet implemented
			if ($this->debug_dcom >= 1) { echo 'imap: call to unimplemented socket function: mailcopy<br />'; }
			return False;
		}
		/*!
		@function mail_move
		@abstract not yet implemented in IMAP sockets module
		*/
		function mail_move($stream,$msg_list,$mailbox)
		{
			// not yet implemented
			if ($this->debug_dcom >= 1) { echo 'imap: call to unimplemented socket function: mail_move<br />'; }
			return False;
		}
		/*!
		@function noop_ping_test
		@abstract not yet implemented in IMAP sockets module
		*/
		function noop_ping_test($stream)
		{
			// not yet implemented
			if ($this->debug_dcom >= 1) { echo 'imap: call to unimplemented socket function: noop_ping_test<br />'; }
			return False;
		}
		/*!
		@function append
		@abstract not yet implemented in IMAP sockets module
		*/
		function append($stream, $folder = "Sent", $header, $body, $flags = "")
		{
			// not yet implemented
			if ($this->debug_dcom >= 1) { echo 'imap: append NOT YET IMPLEMENTED imap sockets function<br />'; }
			return False;
		}
		/*!
		@function fetch_overview
		@abstract not yet implemented in IMAP sockets module
		*/
		function fetch_overview($stream_notused,$criteria,$flags)
		{
			// not yet implemented
			if ($this->debug_dcom >= 1) { echo 'imap: fetch_overview NOT YET IMPLEMENTED imap sockets function<br />'; }
			return False;
		}
		/*
		@function search
		@abstract  implements IMAP_SEARCH, search the mailbox currently opened for param $criteria args
		@param $stream_notused Stream is automatically handled by the underlying code in this socket class. 
		@param $criteria is a string, delimited by spaces, in which the following keywords are allowed. 
		(See syntax for the keywords). Any multi-word arguments (eg. FROM "joey smith") must be quoted.
		@param  flags  Valid values for flags are SE_UID, which causes the returned array to contain UIDs 
		instead of messages sequence numbers.
		@result array
		@discussion: To match all unanswered messages sent by Mom, you'd use: "UNANSWERED FROM mom".
		Searches appear to be case insensitive.
		@syntax Search Keywords can be 
		ALL - return all messages matching the rest of the criteria
		ANSWERED - match messages with the \\ANSWERED flag set
		BCC "string" - match messages with "string" in the Bcc: field
		BEFORE "date" - match messages with Date: before "date"
		BODY "string" - match messages with "string" in the body of the message
		CC "string" - match messages with "string" in the Cc: field
		DELETED - match deleted messages
		FLAGGED - match messages with the \\FLAGGED (sometimes referred to as Important or Urgent) flag set
		FROM "string" - match messages with "string" in the From: field
		KEYWORD "string" - match messages with "string" as a keyword
		NEW - match new messages
		OLD - match old messages
		ON "date" - match messages with Date: matching "date"
		RECENT - match messages with the \\RECENT flag set
		SEEN - match messages that have been read (the \\SEEN flag is set)
		SINCE "date" - match messages with Date: after "date"
		SUBJECT "string" - match messages with "string" in the Subject:
		TEXT "string" - match messages with text "string"
		TO "string" - match messages with "string" in the To:
		UNANSWERED - match messages that have not been answered
		UNDELETED - match messages that are not deleted
		UNFLAGGED - match messages that are not flagged
		UNKEYWORD "string" - match messages that do not have the keyword "string"
		UNSEEN - match messages which have not been read yet
		*/
		function search($stream_notused,$sequence,$flags)
		{
			$empty_return=array();
			// not yet implemented
			if ($this->debug_dcom >= 1) { echo 'imap: search NOT YET IMPLEMENTED imap sockets function<br />'; }
			return $empty_return;
		}
	
		
		/**************************************************************************\
		*	OPEN and CLOSE Server Connection
		\**************************************************************************/
		/*!
		@function open
		@abstract implements php function IMAP_OPEN
		@param $fq_folder (string) 
		@param $user (string) account name to log into on the server
		@param $pass (string) password for this account on the mail server
		@param $flags (defined int) NOT YET IMPLEMENTED
		@author Angles, skeeter
		@result False on error, SocketPtr on success
		@discussion implements the functionality of php function IMAP_OPEN
		note that php IMAP_OPEN applies to IMAP, POP3 and NNTP servers
		@syntax The param $fq_folder is expected to be like this
		{ServerName:Port/options}NAMESPACE_DELIMITER_FOLDERNAME
		repeat for inline doc parser
		&#123;ServerName:Port/options&#125;NAMESPACE_DELIMITER_FOLDERNAME
		An example of this is this
		&#123;mail.example.net:143/imap&#125;INBOX.Sent
		Where INBOX is the namespace and the dot is the delimiter, which will always preceed any subfolder.
		@access public
		*/
		function open ($fq_folder, $user, $pass, $flags='')
		{
			if ($this->debug_dcom >= 1) { echo 'imap: ENTERING open<br />'; }
			
			// fq_folder is a "fully qualified folder", seperate the parts:
			$svr_data = array();
			$svr_data = $this->distill_fq_folder($fq_folder);
			$folder = $svr_data['folder'];
			$server = $svr_data['server'];
			$port = $svr_data['port'];
			if ($this->debug_dcom >= 1) { echo 'imap: open: svr_data:<br />'.serialize($svr_data).'<br />'; }
			
			if (!$this->open_port($server,$port,15))
			{
				echo '<p><center><b>' .lang('There was an error trying to connect to your IMAP server.<br />Please contact your admin to check the servername, username or password.') .'</b></center>';
				echo('<CENTER><A HREF="'.$GLOBALS['phpgw']->link('/home.php').'">'.lang('Click here to continue').'...</A></CENTER>'); //cbsman
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			else
			{
				$junk = $this->read_port();
				if ($this->debug_dcom >= 2) { echo 'imap: open: open port server hello: "' .htmlspecialchars($this->show_crlf($junk)) .'"<br />'; }
			}


			if ($this->debug_dcom >= 2) { echo 'imap: open: user and pass NO quotemeta: user ['. htmlspecialchars($user).'] pass ['.htmlspecialchars($pass).']<br />'; }
			if ($this->debug_dcom >= 2) { echo 'imap: open: user and pass WITH quotemeta: user ['. htmlspecialchars(quotemeta($user)).'] pass ['.htmlspecialchars(quotemeta($pass)).']<br />'; }
			
			$cmd_tag = 'L001';
			$full_command = $cmd_tag.' LOGIN "'.$user.'" "'.$pass.'"';
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom >= 2) { echo 'imap: open: write_port: '. htmlspecialchars($full_command) .'<br />'; }
			if ($this->debug_dcom >= 2) { echo 'imap: open: expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br />'; }
			
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom >= 1) { echo 'imap: open: LEAVING with error: could not write_port<br />'; }
				$this->error();
				// does $this->error() ever continue onto next line?
				return False;
			}
			// server can spew some b.s. hello messages before the official response
			// read the server data
			$response_array = $this->imap_read_port($expecting);
			
			// TEST THIS ERROR DETECTION - empty array = error (BAD or NO)
			if (count($response_array) == 0)
			{
				if ($this->debug_dcom >= 2)
				{
					echo 'imap: open: error in Open<br />';
					echo 'imap: open: last recorded error:<br />';
					echo  $this->server_last_error().'<br />';
				}
				if ($this->debug_dcom >= 1) { echo 'imap: LEAVING Open with error<br />'; }
				return False;
			}
			else
			{
				if ($this->debug_dcom >= 2) { $this->report_svr_data($response_array, 'open', True); }
				if ($this->debug_dcom >= 1) { echo 'imap: open: Successful IMAP Login<br />'; }
			}
			
			// now that we have logged in, php's IMAP_OPEN would now select the desired folder
			if ($this->debug_dcom >= 2) { echo 'imap: open: php IMAP_OPEN would now select desired folder: "'. htmlspecialchars($folder) .'"<br />'; }
			// php's IMAP_OPEN also selects the desired folder (mailbox) after the connection is established
			if($folder != '')
			{
				$this->reopen('',$fq_folder);
			}
			if ($this->debug_dcom >= 1) { echo 'imap: LEAVING open<br />'; }
			return $this->socket;
		}

		/*!
		@function close
		@abstract implements LOGOUT
		@author Angles, skeeter
		*/
		function close($flags="")
		{
			if ($this->debug_dcom >= 1) { echo 'imap: ENTERING Close<br />'; }
			
			$cmd_tag = 'c001';
			$full_command = $cmd_tag.' LOGOUT';
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom >= 2) { echo 'imap: close: write_port: "'. htmlspecialchars($full_command) .'"<br />'; }
			if ($this->debug_dcom >= 2) { echo 'imap: close: expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br />'; }
			
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom >= 1) { echo 'imap: close: LEAVING with error: could not write_port<br />'; }
				$this->error();
			}
			
			// server can spew some b.s. goodbye message before the official response
			// read the server data
			$response_array = $this->imap_read_port($expecting);
			
			// TEST THIS ERROR DETECTION - empty array = error (BAD or NO)
			if (count($response_array) == 0)
			{
				if ($this->debug_dcom >= 2)
				{
					echo 'imap: close: error in Close<br />';
					echo 'imap: close: last recorded error:<br />';
					echo  $this->server_last_error().'<br />';
				}
				if ($this->debug_dcom >= 1) { echo 'imap: Leaving Close with error<br />'; }
				return False;				
			}
			else
			{
				if ($this->debug_dcom >= 2) { $this->report_svr_data($response_array, 'close', True); }
				if ($this->debug_dcom >= 1) { echo 'imap: LEAVING Close<br />'; }
				return True;
			}
		}

		/*!
		@function reopen
		@abstract implements last part of IMAP_OPEN and all of IMAP_REOPEN
		@param $stream_notused Socket class handles stream reference internally
		@param $fq_folder (string) "fully qualified folder" {SERVER_NAME:PORT/OPTIONS}FOLDERNAME 
		repeat for inline docs &#123;SERVER_NAME:PORT/OPTIONS&#125;NAMESPACE_DELIMITER_FOLDERNAME
		@param $flags (defined int) Not Yet Implemented
		@result boolean True on success or False on error
		@author Angles
		@access public
		*/
		function reopen($stream_notused, $fq_folder, $flags='')
		{
			if ($this->debug_dcom >= 1) { echo 'imap: ENTERING reopen<br />'; }
			
			// fq_folder is a "fully qualified folder", seperate the parts:
			$svr_data = array();
			$svr_data = $this->distill_fq_folder($fq_folder);
			$folder = $svr_data['folder'];
			if ($this->debug_dcom >= 1) { echo 'imap: reopen: folder value is: ['.$folder.']<br />'; }
			
			$cmd_tag = 'r001';
			$full_command = $cmd_tag.' SELECT "'.$folder.'"';
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom >= 2) { echo 'imap: reopen: write_port: "'. htmlspecialchars($full_command) .'"<br />'; }
			if ($this->debug_dcom >= 2) { echo 'imap: reopen: expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br />'; }
			
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom >= 1) { echo 'imap: reopen: could not write_port<br />'; }
				$this->error();
			}
			
			// read the server data
			$response_array = $this->imap_read_port($expecting);
			
			// TEST THIS ERROR DETECTION - empty array = error (BAD or NO)
			if (count($response_array) == 0)
			{
				if ($this->debug_dcom >= 2)
				{
					echo 'imap: reopen: error in reopen<br />';
					echo 'imap: reopen: last recorded error:<br />';
					echo  $this->server_last_error().'<br />';
				}
				if ($this->debug_dcom >= 1) { echo 'imap: LEAVING reopen with error<br />'; }
				return False;				
			}
			else
			{
				if ($this->debug_dcom >= 2) { $this->report_svr_data($response_array, 'reopen', True); }
				if ($this->debug_dcom >= 1) { echo 'imap: LEAVING reopen<br />'; }
				return True;
			}
		}

		/*!
		@function listmailbox
		@abstract implements IMAP_LISTMAILBOX
		@param $stream_notused Socket class handles stream reference internally
		@param $server_str (string) {SERVER_NAME:PORT/OPTIONS} 
		repeat for inline docs &#123;SERVER_NAME:PORT/OPTIONS&#125;
		@param $pattern (string) can be a namespace, or a mailbox name, or a namespace_delimiter, 
		or a namespace_delimiter_mailboxname, AND/OR including either "%" or "*" (see discussion below)
		@result an array containing the names of the mailboxes. 
		@discussion: if param $pattern includes some form of mailbox reference, that tells the server where in the
		mailbox hierarchy to start searching. If neither wildcard "%" nor "*" follows said mailbox reference, then the
		server returns the delimiter and the namespace for said mailbox reference. More typically, either one of the
		wildcards "*" or "%" follows said mailbox reference, in which case the server behaves as such:
		_begin_PHP_MANUAL_quote: There are two special characters you can pass as part of the pattern: '*' and '%'.
		'*' means to return all mailboxes. If you pass pattern as '*', you will get a list of the entire mailbox hierarchy. 
		'%' means to return the current level only. '%' as the pattern parameter will return only the top level mailboxes; 
		'~/mail/%' on UW_IMAPD will return every mailbox in the ~/mail directory, but none in subfolders of that directory.
		_end_quote_
		See RFC 2060 Section 6.3.8 (client specific) and Section 7.2.2 (server specific) for more details.
		The imap LIST command takes 2 params , the first is either blank or a mailbox reference, the second is either blank
		or one of the wildcard tokens "*" or "%". PHP's param $pattern is a combination of the imap LIST command's
		2 params, the difference between the imap and the php param(s) is that the php param $pattern will contain
		both mailbox reference AND/OR one of the wildcaed tokens in the same string, whereas the imap command
		seperates the wildcard token from the mailbox reference. I refer to IMAP_LISTMAILBOX's 2nd param as
		$server_str here while the php manual calls that same param "$ref", which is somewhat misnamed because the php
		manual states "ref should normally be just the server specification as described in imap_open()" which apparently
		means the server string {serverName:port/options} with no namespace, no delimiter, nor any mailbox name.
		@author Angles, skeeter
		@access public
		*/
		function listmailbox($stream_notused,$server_str,$pattern)
		{
			if ($this->debug_dcom >= 1) { echo 'imap: ENTERING listmailbox<br />'; }
			$mailboxes_array = Array();
			
			// prepare params, seperate wildcards "*" or "%" from param $pattern
			// LIST param 1 is empty or is a mailbox reference string withOUT any wildcard
			// LIST param 2 is empty or is the wildcard either "%" or "*"
			if ((strstr($pattern, '*'))
			|| (strstr($pattern, '%')))
			{
				if (($pattern == '*')
				|| ($pattern == '%'))
				{
					// no mailbox reference string, so LIST param 1 is empty
					$list_params = '"" "' .$pattern .'"';
				}
				else
				{
					// just assume the * or % is at the end of the string
					// seperate it from the rest of the pattern
					$boxref = substr($pattern, 0, -1);
					$wildcard = substr($pattern, -1);
					$list_params = '"' .$boxref .'" "' .$wildcard .'"';
				}
			}
			elseif (strlen($pattern) == 0)
			{
				// empty $pattern equates to both LIST params being empty, which IS Valid
				$list_params = '"" ""';
			}
			else
			{
				// we have a string with no wildcard, so LIST param 2 is empty
				$list_params = '"' .$pattern .'" ""';
			}

			$cmd_tag = 'X001';
			$full_command = $cmd_tag.' LIST '.$list_params;
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom >= 2) { echo 'imap: listmailbox: write_port: ['. htmlspecialchars($full_command) .']<br />'; }
			if ($this->debug_dcom >= 2) { echo 'imap: listmailbox: expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br />'; }
			
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom >= 1) { echo 'imap: listmailbox: could not write_port<br />'; }
				$this->error();
			}
			
			// read the server data
			$response_array = $this->imap_read_port($expecting);
			
			// TEST THIS ERROR DETECTION - empty array = error (BAD or NO)
			if (count($response_array) == 0)
			{
				if ($this->debug_dcom >= 2)
				{
					echo 'imap: listmailbox: error in listmailbox<br />';
					echo 'imap: listmailbox: last recorded error:<br />';
					echo  $this->server_last_error().'<br />';
				}
				if ($this->debug_dcom >= 1) { echo 'imap: Leaving listmailbox with error<br />'; }
				return False;				
			}
			else
			{
				if ($this->debug_dcom >= 2) { $this->report_svr_data($response_array, 'reopen', True); }
			}
			
			// delete all text except the folder name
			for ($i=0; $i<count($response_array); $i++)
			{
				// don't include "noselect" folders
				if (stristr($response_array[$i], '\NoSelect'))
				{
					// do nothing
				}
				else
				{
					// get everything to the right of the quote_space " , INCLUDES the quote_space itself
					$folder_name = strstr($response_array[$i],'" ');
					// delete that quote_space and trim
					$folder_name = trim(substr($folder_name, 2));
					// if the folder name includes space(s) then it will be enclosed in quotes
					if ((strlen($folder_name) > 0)
					&& ($folder_name[0] == '"') )
					{
						// delete the opening quote
						$folder_name = substr($folder_name, 1);
						// delete the closing quote
						$folder_name = substr($folder_name, 0, -1);
					}
					// php builtin function returns the server_str before the folder name
					$folder_name = $server_str .$folder_name;
					// add to the result array
					$next_pos = count($mailboxes_array);
					$mailboxes_array[$next_pos] = $folder_name;
				}
			}
			
			if ($this->debug_dcom >= 2) { $this->report_svr_data($mailboxes_array, 'listmailbox INTERNAL_mailboxes_array', False); }
			if ($this->debug_dcom >= 1) { echo 'imap: LEAVING listmailbox<br />'; }
			//return '';
			return $mailboxes_array;
		}
		
		
		/**************************************************************************\
		*	Mailbox Status and Information
		\**************************************************************************/
		
		/*!
		@function mailboxmsginfo
		@abstract not yet implemented in IMAP sockets module
		*/
		function mailboxmsginfo($stream_notused='')
		{
			if ($this->debug_dcom >= 1) { echo 'imap: mailboxmsginfo NOT YET IMPLEMENTED imap sockets function<br />'; }
			return False;
		}
		
		/*
		function mailboxmsginfo($folder='')
		{
			$info = new msg_mb_info;
			if($folder=='' || $folder==$this->folder)
			{
				$info->messages = $this->num_msgs;
				if ($info->messages)
				{
					$info->size = $this->total($this->fetch_field(1,$info->messages,'RFC822.SIZE'));
					return $info;
				}
				else
				{
					return False;
				}
			}
			else
			{
				$mailbox = $folder;
			}
			
			$info->messages = $this->num_msgs($mailbox);
			$info->size  = $this->total($this->fetch_field(1,$info->messages,'RFC822.SIZE'));
			
			if ($info->messages)
			{
				return $info;
			}
			else
			{
				return False;
			}
		}
		*/
		
		/*!
		@function status
		@abstract implements php function IMAP_STATUS
		@param $stream_notused  socket class handles stream reference internally
		@param $fq_folder (string) &#123;SERVER_NAME:PORT/OPTIONS&#125;FOLDERNAME
		@param $flags (defined int) see syntax for available flags
		@author Angles, skeeter
		@discussion implements the functionality of php function IMAP_STATUS. Mailserver 
		*should* return a single line of data, (data does not include the tag completion line) 
		But, in certain cases buggy servers in non-error situations may return 2 lines of data, instead on 1 line, 
		see example for an example.
		This quote from the IMAP RFC summarizes the STATUS command  
		" The STATUS command provides an alternative to opening a second IMAP4rev1 connection 
		and doing an EXAMINE command on a mailbox to query that mailbox's status without 
		deselecting the current mailbox in the first IMAP4rev1 connection".
		@syntax The param flags gives instructions on what info to get, default is SA_ALL, flags are
		SA_MESSAGES - set status->messages to the number of messages in the mailbox
		SA_RECENT - set status->recent to the number of recent messages in the mailbox
		SA_UNSEEN - set status->unseen to the number of unseen (new) messages in the mailbox
		SA_UIDNEXT - set status->uidnext to the next uid to be used in the mailbox
		SA_UIDVALIDITY - set status->uidvalidity to a constant that changes when uids for the mailbox may no longer be valid
		SA_ALL - set all of the above
		@example This is an examle of a buggy imap server returning 2 lines instead of 1 line, in a STATUS reply
		-- ArrayPos[0] data: * NO CLIENT BUG DETECTED: STATUS on selected mailbox: INBOX
		-- ArrayPos[1] data: * STATUS INBOX (MESSAGES 724 RECENT 0 UNSEEN 436 UIDNEXT 843 UIDVALIDITY 1005967489)
		Normally only the line with the actual data would be returned by the server
		@access public
		*/
		function status($stream_notused='', $fq_folder='',$options=SA_ALL)
		{
			if ($this->debug_dcom >= 1) { echo 'imap: ENTERING status<br />'; }
			
			// fq_folder is a "fully qualified folder", seperate the parts:
			$svr_data = array();
			$svr_data = $this->distill_fq_folder($fq_folder);
			$folder = $svr_data['folder'];
			// build the query string
			$query_str = '';
			$available_options = Array(
				SA_MESSAGES	=> 'MESSAGES',
				SA_RECENT	=> 'RECENT',
				SA_UNSEEN	=> 'UNSEEN',
				SA_UIDNEXT	=> 'UIDNEXT',
				SA_UIDVALIDITY	=> 'UIDVALIDITY'
			);
			@reset($available_options);
			while(list($key,$value) = each($available_options))
			{
				if($options & $key)
				{
					$query_str .= $value.' ';
				}
			}
			$query_str = trim($query_str);
			
			$cmd_tag = 's001';
			//$full_command = $cmd_tag.' STATUS '.$svr_data['folder'].' (MESSAGES RECENT UIDNEXT UIDVALIDITY UNSEEN)';
			$full_command = $cmd_tag.' STATUS "'.$svr_data['folder'].'" ('.$query_str.')';
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom >= 2) { echo 'imap: status: write_port: "'. htmlspecialchars($full_command) .'"<br />'; }
			if ($this->debug_dcom >= 2) { echo 'imap: status: expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br />'; }
			
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom >= 1) { echo 'imap: status: LEAVING with error: could not write_port<br />'; }
				$this->error();
				return False;				
			}
			
			// read the server data
			$response_array = $this->imap_read_port($expecting);
			
			
			// TEST THIS ERROR DETECTION - empty array = error (BAD or NO)
			if (count($response_array) == 0)
			{
				if ($this->debug_dcom >= 2)
				{
					echo 'imap: status: error in status<br />';
					echo 'imap: status: last recorded error:<br />';
					echo  $this->server_last_error().'<br />';
				}
				if ($this->debug_dcom >= 1) { echo 'imap: LEAVING status with error<br />'; }
				return False;				
			}
			
			// STATUS should only return 1 line of data
			//if (count($response_array) > 1)
			// BUGGY UWASH IMAP SERVERS RETURN 2 LINES HERE, so increase to > 2
			//if (count($response_array) > 2)
			//{
			//	if ($this->debug_dcom >= 2)
			//	{
			//		echo 'imap: status: error in status, more than (one) TWO lines (for buggy uwash servers) line server response, not normal<br />';
			//		echo 'imap: status: last recorded error:<br />';
			//		echo  $this->server_last_error().'<br />';
			//	}
			//	if ($this->debug_dcom >= 1) { echo 'imap: Leaving status with error<br />'; }
			//	return False;				
			//}
			
			// if we get here we have valid server data
			if ($this->debug_dcom >= 2) { $this->report_svr_data($response_array, 'status', True); }
			
			
			// initialize structure
			$info = new mailbox_status;
			$info->messages = '';
			$info->recent = '';
			$info->unseen = '';
			$info->uidnext = '';
			$info->uidvalidity = '';
			
			// for buggy servers that return 2 lines, the last line has the data
			// for compliant 1 line responses, that single line can also be defined as the "last" line of data
			// so to account for buggy servers, we'll take the LAST array element\
			$last_line = (count($response_array) - 1);
			$response_line_of_data = $response_array[$last_line];
			
			// ERROR CHECK
			if (stristr($response_line_of_data, '* STATUS') == False)
			{
				if ($this->debug_dcom >= 2)
				{
					echo 'imap: status: error in status, $response_line_of_data does not have "* STATUS" so it is not valid data<br />';
					echo 'imap: status: last recorded error:<br />';
					echo  $this->server_last_error().'<br />';
				}
				if ($this->debug_dcom >= 1) { echo 'imap: LEAVING status with error at '.__LINE__.'<br />'; }
				return False;				
			}
			
			
			// ok... 
			//typical server data:
			// * STATUS INBOX (MESSAGES 15 RECENT 1 UNSEEN 2 UIDNEXT 17 UIDVALIDITY 1005967489)
			// data starts after the mailbox name, which could actually have similar strings as the status querey
			// get data the includes and follows the opening paren
			// $status_data_raw = strstr($response_array[0], '(');
			$status_data_raw = strstr($response_array[$last_line], '(');
			
			
			// snarf any of the 5 possible pieces of data if they are present
			$status_data['messages'] = $this->snarf_status_data($status_data_raw, 'MESSAGES');
			$status_data['recent'] = $this->snarf_status_data($status_data_raw, 'RECENT');
			$status_data['unseen'] = $this->snarf_status_data($status_data_raw, 'UNSEEN');
			$status_data['uidnext'] = $this->snarf_status_data($status_data_raw, 'UIDNEXT');
			$status_data['uidvalidity'] = $this->snarf_status_data($status_data_raw, 'UIDVALIDITY');
			
			// fill structure and unset any unfilled data elements
			if ($status_data['messages'] != '')
			{
				$info->messages = $status_data['messages'];
			}
			else
			{
				unset($info->messages);
			}
			if ($status_data['recent'] != '')
			{
				$info->recent = $status_data['recent'];
			}
			else
			{
				unset($info->recent);
			}
			if ($status_data['unseen'] != '')
			{
				$info->unseen = $status_data['unseen'];
			}
			else
			{
				unset($info->unseen);
			}
			if ($status_data['uidnext'] != '')
			{
				$info->uidnext = $status_data['uidnext'];
			}
			else
			{
				unset($info->uidnext);
			}
			if ($status_data['uidvalidity'] != '')
			{
				$info->uidvalidity = $status_data['uidvalidity'];
			}
			else
			{
				unset($info->uidvalidity);
			}
			// function "sort" needs to know "->messages" the total num msgs in (hopefully) this folder
			// so L1 class var cache it so "sort" does not have to call this function if it has ALREADY been run
			$this->mailbox_status = $info;
			if ($this->debug_dcom >= 2) { echo 'imap: status: L1 class var caching: $this->mailbox_status DUMP:<pre>'; print_r($this->mailbox_status); echo '</pre>'; }
			if ($this->debug_dcom >= 1) { echo 'imap: LEAVING status<br />'; }
			return $info;
		}
		
		/*!
		@function snarf_status_data
		@abstract Utility function used by STATUS function
		@author Angles
		@access private
		*/
		function snarf_status_data($status_raw_str='',$snarf_this='')
		{
			// bogus data detection
			if (($status_raw_str == '')
			|| ($snarf_this == ''))
			{
				return '';
			}
			// fallback value
			$return_data = '';
			
			//typical server data:
			// * STATUS INBOX (MESSAGES 15 RECENT 1 UNSEEN 2 UIDNEXT 17 UIDVALIDITY 1005967489)
			
			// see if $snarf_this is in the raw data
			$data_mini_str = stristr($status_raw_str, $snarf_this);
			if ($data_mini_str != False)
			{
				// $data_mini_str has everything including and to the right of $snarf_this
				// integer follows $snarf_this+space
				$delete_len = strlen($snarf_this.' ');
				// delete up to integer
				$data_mini_str = substr($data_mini_str, $delete_len);
				// integer will be followed by (A) a space ' ' or (B) a closing paren ')', or (C) any non-integer char
				for ($i=0; $i< strlen($data_mini_str); $i++)
				{
					if ((ord($data_mini_str[$i]) >= chr(0))
					&& (ord($data_mini_str[$i]) <= chr(9)))
					{
						// continue looking, this is integer data
					}
					else
					{
						// we reached a non-integer, so the position just prior to this ends the integer data
						$data_end = $i - 1;
						break;
					}
				}
				// snarf the data
				$data_mini_str = trim(substr($data_mini_str, 0, $data_end));
				$return_data = (int)$data_mini_str;
				if ($this->debug_dcom >= 2) { echo 'imap: snarf_status_data: '.$snarf_this.' = '.$return_data.'<br />'; }
			}
			return $return_data;
		}
		
		/*!
		@function num_msg
		@abstract OBSOLETED
		*/
		// OBSOLETED
		function num_msg($folder='')
		{
			if($folder == '' || $folder == $this->folder)
			{
				return $this->num_msgs;
			}
			return $this->status_query($folder,'MESSAGES');
		}
		
		/*!
		@function total
		@abstract OBSOLETED
		*/
		// OBSOLETED
		function total($field)
		{
			$total = 0;
			reset($field);
			while(list($key,$value) = each($field))
			{
				$total += intval($value);
			}
			return $total;
		}
					
		/**************************************************************************\
		*	Message Sorting
		\**************************************************************************/
		/*!
		@function sort
		@abstract not yet implemented in IMAP sockets module
		*/
		// options/flags are:
			//SE_UID	Return UIDs instead of sequence numbers
			//SE_NOPREFETCH	Don't prefetch searched messages.
		function sort($stream_notused='',$criteria=SORTARRIVAL,$reverse=False,$options='')
		{
			//if ($this->debug_dcom >= 1) { echo 'imap: sort NOT YET IMPLEMENTED imap sockets function<br />'; }
			//return False;
		//}
		
		//function sort($folder='',$criteria=SORTDATE,$reverse=False,$options='')
		//{
			if ($this->debug_dcom >= 1) { echo 'imap: ENTERING sort<br />'; }
			
			// we need total num message in this folder, is it cached?
			if ($this->mailbox_status != '')
			{
				$num_msgs = $this->mailbox_status->messages;
				if ($this->debug_dcom >= 2) { echo 'imap: sort: using L1 class var cached $this->mailbox_status->messages ['.$this->mailbox_status->messages.']<br />'; }
			}
			else
			{
				// we need an "fq_folder" to call this function
				$fq_folder =	 $GLOBALS['phpgw']->msg->get_arg_value('mailsvr_callstr')
							.$GLOBALS['phpgw']->msg->get_arg_value('folder');
				if ($this->debug_dcom >= 2) { echo 'imap: sort: NO L1 class var cached num msgs data, calling this->status with $fq_folder ['.htmlspecialchars($fq_folder).']<br />'; }
				$status_data = $this->status($stream_notused, $fq_folder,SA_ALL);
				$num_msgs = $status_data->messages;
			}
			
			
			// DEBUG
			//if ($this->debug_dcom >= 1) { echo 'imap: debug QUICK EXIT sort<br />'; }
			//return False;
			
			
			switch($criteria)
			{
				case SORTDATE:
					$old_list = $this->fetch_header(1,$num_msgs,'Date:');
					$field_list = $this->convert_date_array($old_list);
					break;
				case SORTARRIVAL:
					break;
				case SORTFROM:
					$field_list = $this->fetch_header(1,$num_msgs,'From:');
					break;
				case SORTSUBJECT:
					$field_list = $this->fetch_header(1,$num_msgs,'Subject:');
					break;
				case SORTTO:
					$field_list = $this->fetch_header(1,$num_msgs,'To:');
					break;
				case SORTCC:
					$field_list = $this->fetch_header(1,$num_msgs,'cc:');
					break;
				case SORTSIZE:
					$field_list = $this->fetch_field(1,$num_msgs,'RFC822.SIZE');
					break;
			}
			@reset($field_list);
			if($criteria == SORTSUBJECT)
			{
				if(!$reverse)
				{
					uasort($field_list,array($this,"ssort_ascending"));
				}
				else
				{
					uasort($field_list,array($this,"ssort_decending"));
				}
			}
			elseif(!$reverse)
			{
				asort($field_list);
			}
			else
			{
				arsort($field_list);
			}
			$return_array = Array();
			@reset($field_list);
			$i = 1;
			while(list($key,$value) = each($field_list))
			{
				$return_array[] = $key;
				if ($this->debug_dcom >= 2) { echo 'imap: sort: ('.$i++.') Field: <b>'.$value."</b>\t\tMsg Num: <b>".$key."</b><br />\r\n"; } 
			}
			@reset($return_array);
			if ($this->debug_dcom >= 1) { echo 'imap: LEAVING sort<br />'; }
			return $return_array;
		}
		
		
		/*!
		@function fetch_header
		@abstract Under Construction - Used by sort
		*/
		function fetch_header($start,$stop,$element)
		{
			if ($this->debug_dcom >= 1) { echo 'imap: ENTERING fetch_header<br />'; }
			
			if(!$this->write_port('a001 FETCH '.$start.':'.$stop.' RFC822.HEADER'))
			{
				$this->error();
			}
			$field_element = array();
			
			for($i=$start;$i<=$stop;$i++)
			{
				$response = $this->read_port();
				//while(!ereg('FETCH completed',$response))
				while(chop($response)!='')
				{
					if ($this->debug_dcom >= 2) { echo 'imap: fetch_header: Response = '.$response."<br />\r\n"; } 
					if(preg_match('/^\*/',$response))
					{
						$field = explode(' ',$response);
						$msg_num = $field[1];
					}
					if(preg_match("/^$element/",$response))
					{
						$field_element[$msg_num] = $this->phpGW_quoted_printable_decode2(substr($response,strlen($element)+1));
						if ($this->debug_dcom >= 2) { echo 'imap: fetch_header: <b>Field:</b> '.$field_element[$msg_num]."\t = <b>Msg Num</b> ".$msg_num."<br />\r\n"; } 
					}
					elseif(preg_match('/^'.strtoupper($element).'/',$response))
					{
						$field_element[$msg_num] = $this->phpGW_quoted_printable_decode2(substr($response,strlen(strtoupper($element))+1));
						if ($this->debug_dcom >= 2) { echo 'imap: fetch_header: <b>Field:</b> '.$field_element[$msg_num]."\t = <b>Msg Num</b> ".$msg_num."<br />\r\n"; } 
					}
					$response = $this->read_port();
				}
				$response = $this->read_port();
			}
			$response = $this->read_port();
			if ($this->debug_dcom >= 2) { echo 'imap: fetch_header: returning $field_element ['.$field_element.'] <br />'; } 
			if ($this->debug_dcom >= 1) { echo 'imap: LEAVING fetch_header<br />'; }
			return $field_element;
		}
		
		
		/*!
		@function fetch_field
		@abstract Under Construction
		*/
		function fetch_field($start,$stop,$element)
		{
			if(!$this->write_port('a001 FETCH '.$start.':'.$stop.' '.$element))
			{
				$this->error();
			}
			$response = $this->read_port();
			while(!preg_match('/FETCH completed/',$response))
			{
				//echo 'Response = '.$response."<br />\n";
				$field = explode(' ',$response);
				$msg_num = intval($field[1]);
				$field_element[$msg_num] = substr($field[4],0,strpos($field[4],')'));
				//echo '<b>Field:</b> '.substr($field[4],0,strpos($field[4],')'))."\t = <b>Msg Num</b> ".$field_element[substr($field[4],0,strpos($field[4],')'))]."<br />\n";
				$response = $this->read_port();
			}
			return $field_element;
		}		
		
		
		/**************************************************************************\
		*
		*	Message Structural Information
		*
		\**************************************************************************/
		
		/*!
		@function fetchstructure
		@abstract not yet implemented in IMAP sockets module
		*/
		function fetchstructure($stream_notused,$msg_num,$flags="")
		{
			// outer control structure for the multi-pass functions
			if ($this->debug_dcom >= 1) { echo 'imap: fetchstructure NOT YET IMPLEMENTED imap sockets function<br />'; }
			return False;
		}
		
		/*
		function fetchstructure($msgnum)
		{
			
			if(!$this->write_port('a001 FETCH '.$msgnum.' BODY[HEADER]'))
			//if(!$this->write_port('a001 FETCH '.$msgnum.' BODY.PEEK[HEADER.FIELDS (Date To From Cc Subject Message-Id X-Priority Content-Type)]'))
			{
				$this->error();
			}
			$this->header = Null;
			$response = $this->read_port();
			while(!ereg('^a001 OK FETCH completed',$response))
			{
				if(!ereg('^\* '.$msgnum.' FETCH \(BODY\[HEADER',$response) && chop($response) != '' && chop($response) != ')')
				{
					echo 'Response = '.$response."<br />\n";
					$this->create_header($response,&$this->header,"True");
				}
				$response = $this->read_port();
			}
			echo '<b>'.$msgnum.'</b> Completed!'."<br />\n";
			if(!$this->write_port('a001 FETCH '.$msgnum.' BODY[TEXT]'))
			{
				$this->error();
			}
			$response = $this->read_port();
			while(!ereg('^a001 OK FETCH completed',$response))
			{
				echo 'Response = '.$response."<br />\n";
				$response = $this->read_port();
			}
			return $this->header;
		}
		*/
		
		
		/**************************************************************************\
		*	Message Envelope (Header Info) Data
		\**************************************************************************/
		/*!
		@function header
		@abstract not yet implemented in IMAP sockets module
		*/
		function header($stream_notused,$msg_num,$fromlength="",$tolength="",$defaulthost="")
		{
			if ($this->debug_dcom >= 1) { echo 'imap: header NOT YET IMPLEMENTED imap sockets function<br />'; }
			return False;
		}
		
		
		/**************************************************************************\
		*	More Data Communications (dcom) With IMAP Server
		\**************************************************************************/
	
		/**************************************************************************\
		*	DELETE a Message From the Server
		\**************************************************************************/
		/*!
		@function delete
		@abstract not yet implemented in IMAP sockets module
		*/
		function delete($stream_notused,$msg_num,$flags="")
		{
			if ($this->debug_dcom >= 1) { echo 'imap: delete NOT YET IMPLEMENTED imap sockets function<br />'; }
			return False;
		}
		
		
		/**************************************************************************\
		*	Get Message Headers From Server
		\**************************************************************************/
		/*!
		@function fetchheader
		@abstract not yet implemented in IMAP sockets module
		*/
		function fetchheader($stream_notused,$msg_num,$flags='')
		{
			// NEEDED: code for flags: FT_UID; FT_INTERNAL; FT_PREFETCHTEXT
			if ($this->debug_dcom >= 1) { echo 'imap: fetchheader NOT YET IMPLEMENTED imap sockets function<br />'; }
			return False;
		}
		
		
		/**************************************************************************\
		*	Get Message Body (Parts) From Server
		\**************************************************************************/
		/*!
		@function fetchbody
		@abstract not yet implemented in IMAP sockets module
		*/
		function fetchbody($stream_notused,$msg_num,$part_num="",$flags="")
		{
			if ($this->debug_dcom >= 1) { echo 'imap: fetchbody  NOT YET IMPLEMENTED imap sockets function<br />'; }
			return False;
		}
		
		/*!
		@function get_body
		@abstract implements IMAP_BODY
		*/
		function get_body($stream_notused,$msg_num,$flags='',$phpgw_include_header=True)
		{
			// NEEDED: code for flags: FT_UID; maybe FT_INTERNAL; FT_NOT; flag FT_PEEK has no effect on POP3
			if ($this->debug_dcom >= 1) { echo 'imap: get_body  NOT YET IMPLEMENTED imap sockets function<br />'; }
			return False;
		}
		
	}

