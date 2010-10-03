<?php
	/**
	* Communik8r email abstraction layer
	*
	* @author Dave Hall skwashd@phpgroupware.org
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package communik8r
	* @subpackage comm
	* @version $Id: class.comm_pop3.inc.php,v 1.1.1.1 2005/08/23 05:03:52 skwashd Exp $
	*/

	/**
	*@see comm_email
	*/

	phpgw::import_class('communik8r.comm_email');

	/**
	* Email abstraction library for POP3
	*/
	class comm_pop3 extends comm_email
	{
		/**
		* @var $conn resource $conn PHP socket connection pointer
		*/
		var $conn;

		/**
		* @var $uidls cache of unique ids and sequence numbers
		*/
		var $uidls;
		
		/**
		*@constructor
		*/
		function comm_pop3($acct_info)
		{
			$this->comm_email($acct_info);
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
			return $this->_generate_uidl($content);
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
		{
			if ( $mbox != 'INBOX' )
			{
				return True;
			}
			
			$seq_id = $this->unique_id2seq($unique_id);
			
			$reponse = $this->_send_command("DELE $seq_id");
			if ( $this->_is_ok($reponse) )
			{
				return True;
			}
			return False;
		}
		
		/**
		* Expunge whole mailbox
		*
		* @internal as POP3 expunges on quit, we just disconnect and reconnect :) 
		* @param string $mbox mailbox to expunge
		* @return bool was the mailbox expunaged?
		*/
		function expunge_mailbox($mailbox)
		{
			$this->_disconnect();
			$this->_connect();
		}

		/**
		* Get a list of mailboxes available to the user
		*
		* @param bool $only_subd only get list of subscribed folders?
		* @returns array list of folders and some information about them key is folder name
		*/
		function get_mailbox_list($only_subd = True)
		{
			return array('');
		}

		/**
		* Get a list of unique ids for a mailbox
		*
		* @param string $mbox the mailbox to get list from
		* @returns array list of unique ids
		*/
		function get_list_unique_ids($mbox)
		{
			
			if ( !$this->_is_connected() )
			{
				return False;
			}

			if ( is_array($this->uidls) && count($this->uidls) ) 
			{
				return $this->uidls;
			}
			
			$this->uidls = array();
			
			if ( $this->_is_ok($this->_send_command('UIDL') ) )
			{
				while ( strpos( ($line = trim(fgets($this->conn, 1024))), '.') !== 0 ) 
				{
					$parts = explode(' ', $line);
					$this->uidls[$parts[0]] = $parts[1];
				}
			}
			return $this->uidls;
		}

		/**
		* Retreive a message from the server
		*
		* @internal we retreive the whole message and throw it into the cache
		* @param string $mbox mailbox containing the message to retreive
		* @param int|string $unique_id the UID|UIDL of the message
		* @returns string the raw message empty string for invalid message
		*/
		function get_msg($mbox, $unique_id)
		{
			$msg_obj = new stdClass;
			if ( $mbox != '' || !($seq = $this->unique2seq($unique_id) ) )
			{
				return $msg_obj;
			}

			if ( $this->_is_ok($this->_send_command("RETR $seq") ) )
			{
				while ( ($line = rtrim( fgets($this->conn, 1024) ) ) != '.' ) 
				{
					$msg .= $line . "\r\n";
				}
				
				$msg_size = strlen(&$msg);
				$msg_obj = $this->_msg_string2obj(&$msg);
				unset($msg);
				$msg_obj->info = array( 'subject'	=> $msg_obj->headers['subject'],
							'sender'	=> $msg_obj->headers['from'],
							'date'		=> $msg_obj->headers['date'],
							'size'		=> $msg_size,
							'read'		=> False,
							'answered'	=> False,
							'deleted'	=> False,
							'flagged'	=> False,
							'draft'		=> False
							);
			}
			return $msg_obj;
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
		{
			if ( !is_array($this->uidls) || !count($this->uidls) )
			{
				$this->get_list_unique_ids('');
			}

			return intval(array_search($unique_id, $this->uidls));

		}

		/**
		* Connect to mailserver
		* @private
		*/
		function _connect()
		{
			$port = ($this->acct_info['port'] ? $this->acct_info['port'] : 110);
			$server = $this->acct_info['server'];
			if ( $this->acct_info['ssl'] )
			{
				$server = 'ssl://' . $server;
				$def_port = 995;
			}
			else if ( $this->acct_info['tls'] )
			{
				$server = 'tls://' . $server;
			}
			
			//This is used for logging
			$acct_info = $this->acct_info;
			$acct_info['password'] = '***';

			$this->conn = @fsockopen($server, $port, $errno, $errstr);

			if ( $this->conn )
			{
				//get the banner (but ignore it for now)
				$banner = fgets($this->conn, 1024);
				if ( $this->_is_ok($this->_send_command('CAPA') ) )//Fail if it doesn't accept CAPA
				{
					$acct_info['capa_ok'] = True;
					while ( ($output = trim(fgets($this->conn, 1024) ) ) && $output != '.' )
					{
						switch ( strtoupper($output) )
						{
							case 'STLS':
							{
								$acct_info['tls_ok'] = True;
							}

							case 'UIDL':
							{
								$acct_info['uidl_ok'] = True;
							}
							
							default:
								//ignored!
						}
					}

					if ( $acct_info['uidl_ok'] 
						&& (!$this->acct_info['tls'] || !( $this->acct_info['tls'] && $acct_info['tls_ok'] != True ) ) )
					{
						$acct_info['connected_ok'] = True;
						
						$reponse = $this->_send_command('USER ' . $this->acct_info['username']);
						if ( $this->_is_ok($reponse) )
						{
							$acct_info['username_accepted'] = True;
							
							$reponse = $this->_send_command('PASS ' . $this->acct_info['password']);
							if ( $this->_is_ok($reponse) )
							{
								return True;
							}
						}
					}
				}
				$this->_disconnect(); //close the connection

			}
			//echo '<pre>' . print_r($acct_info, True); die('</pre>');
			trigger_error(E_USER_ERROR, 'POP Connection failed: ' . serialize($acct_info) . ', check your configuration');
			//Close stream
			return False;
		}

		/**
		* Disconnect from mailserver
		* @private
		*/
		function _disconnect()
		{
			$this->_send_command('QUIT');
			fclose($this->conn);
		}

		/**
		* Generate a dummy UIDL string
		*
		* @param string $content the conent of the message
		* @returns string the new UIDL
		*/
		function _generate_uidl($content)
		{
			$uidl = md5(time() . $this->acct_info['username'] . md5($content)); //why double md5? to increase likelyhood of uniqueness
			return substr($uidl, 0, 72 - (strlen($this->acct_info['hostname']) - 1) ) . '@' . $this->acct_info['hostname'];
		}

		/**
		* Check if connected
		*
		* @param bool $auto_connect automagically connect if not already connected
		* @returns bool are we connected?
		*/
		function is_connected($auto_connect = True)
		{
			return $this->_is_connected($auto_connect);
		}

		function _is_connected($auto_connect = True)
		{
			if ( $this->conn )
			{
				return $this->_ping();
			}
			else if ( !$this->conn && $auto_connect)
			{
				$this->_connect();
				return $this->_is_connected(False);
			}
			return False;
		}

		/**
		* Does the string contain an OK response from the server?
		*
		* @param string $str the string to test
		* @returns bool is the repsonse ok?
		*/
		function _is_ok($str)
		{
			if ( strpos( trim($str), '+OK' ) === 0 )
			{
				return True;
			}
			return False;
		}

		/**
		* Convert a message string to an object
		*
		* @param string $msg raw message string
		* @returns object the message object
		*/
		function _msg_string2obj(&$msg)
		{
			//echo '<pre>' . htmlentities($msg) . '</pre>';
			$mime = new Mail_mimeDecode(&$msg, "\r\n");
			$obj = $mime->decode( array('include_bodies' => True, 'decode_bodies' => True) );
			return $obj;
		}

		/**
		* Ping the current mailserver connection
		*
		* @returns bool is the connection up
		*/
		function _ping()
		{
			if ( $this->conn )
			{
				return $this->_is_ok( $this->_send_command('NOOP') );
			}
			return False;
		}

		/**
		* Send a command to the POP3 server and get a one line response back
		*
		* @param string $cmd command to send to server
		* @returns string reponse from the mail server - returns '' on no response/connection
		*/
		function _send_command($cmd)
		{
			if ( !$this->conn )
			{
				return '';
			}
			fwrite($this->conn, "$cmd\r\n");
			$response = fgets($this->conn, 4096);
			//echo "C: $cmd<br />\n$response<br />\n";
			return $response;
		}
	}
?>
