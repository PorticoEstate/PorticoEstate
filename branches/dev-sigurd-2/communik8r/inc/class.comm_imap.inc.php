<?php
	/**
	 * Communik8r IMAP sockets class
	 *
	 * @author Ryo Chijiiwa <Ryo@IlohaMail.org>
	 * @author Dave Hall skwashd@phpgroupware.org
	 * @copyright Copyright (c) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
	 * @copyright Copyright (c) 2005 Dave Hall <skwashd@phpgroupware.org>
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @internal taken from http://ilohamail.org and converted to OOP by skwashd
	 * @package communik8r
	 * @subpackage comm
	 * @version $Id: class.comm_imap.inc.php,v 1.1.1.1 2005/08/23 05:04:01 skwashd Exp $
	 */

	/**
	 *@see comm_email
	 */
	include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.comm_email.inc.php');

	/**
	 * @see Message
	 */
	include_once(PHPGW_APP_INC . SEP . 'class.mime.inc.php');


	/**
	 * IMAP Connection
	 */
	class imap_conn
	{
		/**
		 * @var resource $fp stream file pointer
		 */
		var $fp;

		/**
		 * @var array $error imap errors
		 */
		var $error;

		/**
		 * @var int? $error_num imap error code?
		 */
		var $error_num;

		/**
		 * @var string $selected currently selected mailbox
		 */
		var $selected;

		/**
		 * @var ??? $message ???
		 */
		var $message;

		/**
		 * @var string $host IMAP host connected to
		 */
		var $host;

		/**
		 * @var object $cache the message cache
		 */
		var $cache;

		/**
		 * @var array $uid_cache unique id mappings cache 
		 */
		var $uid_cache;

		/**
		 * @deprecated
		 * @var bool $do_cache enable caching
		 */
		var $do_cache = True;

		/**
		 * @var bool? $exists ???
		 */
		var $exists;

		/**
		 * @var int $recent number of recent messages
		 */
		var $recent;

		/**
		 * @var string the root directory for the mailboxes
		 */
		var $rootdir;

		/**
		 * @var string $delimeter how folders path components are delimented
		 */
		var $delimiter;
	}

	/**
	 * Basic headers for a message
	 */
	class imap_basic_header
	{
		/**
		 * @var int $id message sequence number
		 */
		var $id;

		/**
		 * @var int $uid message unique identifier
		 */
		var $uid;

		/**
		 * @var string $mailbox the mailbox the message was in
		 */
		var $mailbox;
		
		/**
		 * @var string $subject message subject
		 */
		var $subject;

		/**
		 * @var string $from the sender of the message
		 */
		var $from;

		/**
		 * @var string $to who the message was sent to
		 */
		var $to;

		/**
		 * @var string $cc who the message was cc'd to
		 */
		var $cc;

		/**
		 * @var string $reply_to who should replies be addressed to
		 */
		var $reply_to;

		/**
		 * @var string? $in_reply_to the id of the message that this message responds to (threading meta data)
		 */
		var $in_reply_to;

		/**
		 * @var string $date date the message was sent?
		 */
		var $date;

		/**
		 * @var string $message_id the globally unique message id?
		 */
		var $message_id;

		/**
		 * @var int $size the size of the message in bytes
		 */
		var $size;

		/**
		 * @var string $encoding how the message is encoded utf7|base64|qprint|utf8?
		 */
		var $encoding;

		/**
		 * @var sting $ctype ??
		 */
		var $ctype;

		/**
		 * @var string $flags the raw flags string??
		 */
		var $flags;

		/**
		 * @var ??? $timestamp ???
		 */
		var $timestamp;

		/**
		 * @var ??? $f ???
		 */
		var $f;

		/**
		 * @var bool $seen has the message been seen?
		 */
		var $seen;

		/**
		 * @var bool $deleted has the message been marked for deletion?
		 */
		var $deleted;

		/**
		 * @var bool $draft is the message a draft
		 */
		var $draft;

		/**
		 * @var bool $recent is the message recent?
		 */
		var $recent;

		/**
		 * @var bool $answered has the been answered/replied?
		 */
		var $answered;

		/**
		 * @var string $internaldate the date the message was received
		 */
		var $internaldate;

		/**
		 * @var bool $is_reply is the message a reply?
		 */
		var $is_reply;
	}

	/**
	 * Threaded message header?
	 */ 
	class imap_thread_header
	{
		/**
		 * @var int $id message ID
		 */
		var $id;

		/**
		 * @var string $id subject
		 */
		var $sbj;

		/**
		 * @var ??? $id ???
		 */
		var $irt;

		/**
		 * @var int $id message sequence number
		 */
		var $mid;
	}


	/**
	 * IMAP sockets
	 */
	class comm_imap extends comm_email
	{
		/**
		 * @var object $acct holds the account information object
		 */
		var $acct;

		/**
		 * @var object $conn holds the connection object
		 */
		var $conn;

		/**
		 * @var string $select the currently selected mailbox?
		 */
		 var $select;

		/**
		 * @constructor
		 */
		function comm_imap($acct_info)
		{
			if ( !$this->_validate_account($acct_info) )
			{
				trigger_error('Invalid account information, exiting', E_USER_ERROR);
			}
			$this->comm_email($acct_info);

		}

		/**
		 * Check if connected to imap server, and optionally connect if not
		 *
		 * @param bool $auto_connect automagically connect to server if not connected already
		 * @returns bool are we connected?
		 */
		function is_connected( $auto_connect = true )
		{
			if ( isset($this->conn) && is_object($this->conn) 
				&& isset($this->conn->fp) && is_resource($this->conn->fp) )
			{
				return true;
			}

			if ( $auto_connect )
			{
				$this->connect();
				return $this->is_connected(false);
			}
			return false;
		}

		/**
		 * XOR 2 strings
		 *
		 * @param string $string the first string
		 * @param string $string2 the second string
		 * @results XOR'd string
		 */
		function imap_xor($string, $string2)
		{
			$result = "";
			$size = strlen($string);
			for ($i=0; $i<$size; $i++)
			{
				$result .= chr(ord($string[$i]) ^ ord($string2[$i]));
			}

			return $result;
		}

		/**
		 * Read a line from the socket
		 *
		 * @param int $size the length of the line to read
		 */
		function read_line($size = 2048)
		{
			$line="";
			if ($this->conn->fp)
			{
				do
				{
					$buffer = fgets($this->conn->fp, $size);
					$line .= $buffer;
				} while( $buffer[strlen($buffer)-1] != "\n" );
			}
			return $line;
		}

		/**
		 * Read multiple lines ?
		 *
		 * @param string
		 */
		function read_multi_line($line)
		{
			$line = rtrim($line);
			if (ereg('\{[0-9]+\}$', $line))
			{
				$out = "";
				preg_match_all('/(.*)\{([0-9]+)\}$/', $line, $a);
				$bytes = $a[2][0];
				while ( strlen($out) < $bytes )
				{
					$out .= rtrim($this->read_line(1024));
				}
				$line = $a[1][0]."\"$out\"";
			}
			return $line;
		}

		/**
		 * Read specified number of bytes from socket
		 */
		function read_bytes($bytes)
		{
			$data = "";
			$len = 0;
			do
			{
				$data .= fread($this->conn->fp, $bytes - $len);
				$len = strlen($data);
			} while( $len < $bytes );
			return $data;
		}

		/**
		 * Read reply from server
		 *
		 * @returns string the server response
		 */
		function read_reply()
		{
			do
			{
				$line = rtrim(trim($this->read_line(1024)));
			}while($line[0]=="*");

			return $line;
		}

		/**
		 * Parse a server response string
		 *
		 * @param string $string the response string
		 * @returns int 0 == ok, -1 == no response, -2 == bad request, -3 == fux0r
		 */
		function parse_result($string)
		{
			$a = explode(" ", $string);
			if ( count($a) > 2 )
			{
				if ( $a[1] == "OK" )
				{
					return 0;
				}
				else if ( $a[1] == "NO" )
				{
					return -1;
				}
				else if ( $a[1] == "BAD" )
				{
					return -2;
				}
			}
			return -3;//if it all goes pear shaped
		}

		/**
		 * Test if string starts with specified string
		 *
		 * @param string $haystack the string to search against
		 * @param string $needle the string to search for
		 * @returns bool did haystack start with needle ?
		 */
		function starts_with($haystack, $needle)
		{
			$len = strlen($needle);

			if ( $len == 0 )
			{
				return false;
			}

			if ( substr($haystack, 0, $len) == $needle ) 
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Case insensitive test if string starts with specified string
		 *
		 * @param string $haystack the string to search against
		 * @param string $needle the string to search for
		 * @returns bool did haystack start with needle ?
		 */
		function starts_withi($haystack, $needle)
		{
			$len = strlen($needle);

			if ( $len == 0 )
			{
				return false;
			}

			if ( substr(strtolower($haystack), 0, $len) == strtolower($needle) ) 
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Authenticate user using IMAP AUTHENTICATE mechanism
		 *
		 * @internal see RFC 3501 s6.2.2 for more info
		 * @param string $challenge encrypted challenge string
		 * @returns bool did we authenticate?
		 */
		function authenticate($challenge)
		{
			if ( isset($this->acct_info['password']) && !empty($this->acct_info['password']) )
			{
				$pass = $this->acct_info['password'];
			}
			else
			{
				$pass = $GLOBALS['phpgw_info']['user']['passwd'];
			}
			
			// initialize ipad, opad
			for ($i = 0; $i < 64; ++$i)
			{
				$ipad .=chr (0x36);
				$opad .=chr (0x5C);
			}
			// pad $pass so it's 64 bytes
			$padLen = 64 - strlen($pass);
			for ($i = 0; $i < $padLen; ++$i)
			{
				$pass .= chr(0);
			}

			// generate hash
			$hash = md5($this->imap_xor($pass, $opad) 
					. pack("H*", md5($this->imap_xor($pass, $ipad) . base64_decode($challenge) ) )
				   );

			// generate reply
			$reply = base64_encode("{$this->acct_info['username']} {$hash}");

			// send result, get reply
			fputs("{$reply}\r\n");
			$line = $this->read_line(1024);

			// process result
			if ( $this->parse_result($line) == 0)
			{
				return True;
			}
			else
			{
				trigger_error("Authentication failed (AUTH): {$line}", E_USER_ERROR);
				return false;
			}
		}

		/**
		 * Authenticate user using IMAP LOGIN mechanism
		 *
		 * @internal see RFC 3501 s6.2.3 for more info
		 * @param string $user username
		 * @param string $pass password
		 * @returns bool did we login?
		 */
		function login()
		{
			if ( isset($this->acct_info['password']) && trim($this->acct_info['password']) != '' )
			{
				$pass = $this->acct_info['password'];
			}
			else
			{
				trigger_error('using account password');
				$pass = $GLOBALS['phpgw_info']['user']['passwd'];
			}

			fputs($this->conn->fp, "a001 LOGIN {$this->acct_info['username']} \"{$pass}\"\r\n");

			do
			{
				$line = $this->read_reply();
			} while( !$this->starts_with($line, "a001 ") );

			$a = explode(" ", $line);
			if ( $a[1] == "OK" )
			{
				return True;
			}
			else
			{
				fclose($this->conn->fp);
				trigger_error("Authentication failed (LOGIN): {$line}", E_USER_ERROR);
				return False;
			}
		}

		function parse_namespace2($str, &$i, $len=0, $l)
		{
			if (!$l)
			{
				$str = str_replace("NIL", "()", $str);
			}

			if (!$len)
			{
				$len = strlen($str);
			}

			$data = array();
			$in_quotes = false;
			$elem = 0;
			for($i;$i<$len;$i++)
			{
				$c = (string)$str[$i];
				if ($c=='(' && !$in_quotes)
				{
					$i++;
					$data[$elem] = $this->parse_namespace2($str, $i, $len, $l++);
					$elem++;
				}
				else if ($c==')' && !$in_quotes)
				{
					return $data;
				}
				else if ($c=="\\")
				{
					$i++;
					if ($in_quotes) $data[$elem].=$c.$str[$i];
				}
				else if ($c=='"')
				{
					$in_quotes = !$in_quotes;
					if (!$in_quotes) $elem++;
				}
				else if ($in_quotes)
				{
					$data[$elem].=$c;
				}
			}
			return $data;
		}

		/**
		 * Get the namespaces for the mailbox
		 *
		 * @returns bool did we get the namespaces?
		 */
		function namespace()
		{
			if ($this->acct_info['rootdir'])
			{
				return true;
			}

			fputs($this->conn->fp, "ns1 NAMESPACE\r\n");
			do
			{
				$line = $this->read_line(1024);
				if ($this->starts_with($line, "* NAMESPACE"))
				{
					$i = 0;
					$data = $this->parse_namespace2(substr($line,11), $i, 0, 0);
				}
			}while (!$this->starts_with($line, "ns1"));

			if (!is_array($data))
			{
				return false;
			}

			$user_space_data = $data[0];
			if (!is_array($user_space_data))
			{
				return false;
			}

			$first_userspace = $user_space_data[0];
			if ( count($first_userspace) != 2)
			{
				return false;
			}

			$this->conn->rootdir = $first_userspace[0];
			$this->conn->delimiter = $first_userspace[1];
			$this->acct_info['rootdir'] = substr($this->conn->rootdir, 0, -1);

			return true;
		}

		/**
		 * Connect to an IMAP server
		 *
		 * @return bool did it connect?
		 */
		function connect()
		{	
			$result = false;

			//initialize connection
			$this->conn = new imap_conn();
			$this->conn->selected = "";
			$this->conn->user = $this->acct_info['username'];
			$this->conn->host = $this->acct_info['hostname'];
			$this->conn->cache = array();
			$this->conn->do_cache = True;
			$this->conn->cache_dirty = array();

			//check for SSL
			$port = 143;
			$host = $this->conn->host;
			if ( $this->acct_info['tls'] ) 
			{
				$host = "tls://{$host}";
			}
			if ( $this->acct_info['ssl'] )
			{
				$host = "ssl://{$host}";
				if ( !$this->acct_info['port'] )
				{
					$port = 993;
				}
			}



			//open socket connection
			$this->conn->fp = @fsockopen($host, $port);
			if (!$this->conn->fp)
			{
				trigger_error("Could not connect to {$host}:{$port}", E_USER_ERROR);
				return false;
			}

			trigger_error("Socket connection to {$host}:{$port} established", E_USER_NOTICE);
			$line=$this->read_line(300);

			//default to plain text auth
			$auth_method = "plain";

			//check for CRAM-MD5
			fputs($this->conn->fp, "cp01 CAPABILITY\r\n");
			do {
				$line = trim(rtrim($this->read_line(100)));
				$a = explode(" ", $line);
				if ($line[0]=="*")
				{
					while ( list($k, $w) = each($a) )
					{
						if ((strcasecmp($w, "AUTH=CRAM_MD5")==0)||
								(strcasecmp($w, "AUTH=CRAM-MD5")==0))
						{
							$auth_method = "auth";
						}
					}
				}
			}while ($a[0] != "cp01");

			if (strcasecmp($auth_method, "auth")==0)
			{
				trigger_error('Trying CRAM-MD5 ...', E_USER_NOTICE);
				//do CRAM-MD5 authentication
				fputs($this->conn->fp, "a000 AUTHENTICATE CRAM-MD5\r\n");
				$line = trim(rtrim($this->read_line(1024)));
				if ($line[0]=="+")
				{
					trigger_error("Got challenge: {$line}", E_USER_NOTICE);
					$result = $this->authenticate(substr($line,2));
					trigger_error("Tried CRAM-MD5: {$result}", E_USER_NOTICE);
				}
				else
				{
					trigger_error("No challenge ({$line}), will try PLAIN", E_USER_NOTICE);
					$auth = "plain";
				}
			}

			if ( !$result || $auth == 'plain' )
			{
				//do plain text auth
				$result = $this->login();
				trigger_error("Tried PLAIN: {$result}", E_USER_NOTICE);
			}

			$this->conn->message .= $auth;

			if ($result)
			{
				$this->namespace($this->conn);
				return True;
			}
			else
			{
				return false;
			}
		}

		function close()
		{
			//$this->write_cache($this->conn);
			if (@fputs($this->conn->fp, "I LOGOUT\r\n"))
			{
				fgets($this->conn->fp, 1024);
				fclose($this->conn->fp);
				unset($this->conn->fp);
			}
		}

		function clear_cache()
		{
		}


		function iil_C_WriteCache()
		{
			//echo "<!-- doing iil_C_WriteCache //-->\n";

			if (is_array($this->conn->cache))
			{
				while(list($folder,$data)=each($this->conn->cache))
				{
					if ($folder && is_array($data) && $this->conn->cache_dirty[$folder])
					{
						$key = $folder.".imap";
						$result = cache_write($this->conn->user, $this->conn->host, $key, $data, true);
						//echo "<!-- writing $key $data: $result //-->\n";
					}
				}
			}
		}

		function iil_C_LoadCache($folder)
		{
			$key = $folder.".imap";
			if (!is_array($this->conn->cache[$folder]))
			{
				$this->conn->cache[$folder] = cache_read($this->conn->user, $this->conn->host, $key);
				$this->conn->cache_dirty[$folder] = false;
			}
		}

		function iil_C_ExpireCachedItems($folder, $message_set)
		{

			if ( !is_array($this->conn->cache[$folder]) 
					|| count($this->conn->cache[$folder])==0)
			{
				return;	//cache not initialized|empty
			}

			$uids = $this->fetch_header_index($folder, $message_set, "UID");
			$num_removed = 0;
			if (is_array($uids))
			{
				//echo "<!-- unsetting: ".implode(",",$uids)." //-->\n";
				while(list($n,$uid)=each($uids))
				{
					unset($this->conn->cache[$folder][$uid]);
					//$this->conn->cache[$folder][$uid] = false;
					//$num_removed++;
				}
				$this->conn->cache_dirty[$folder] = true;

				//echo '<!--'."\n";
				//print_r($this->conn->cache);
				//echo "\n".'//-->'."\n";
			}
			else
			{
				echo "<!-- failed to get uids: $message_set //-->\n";
			}

			/*
			   if ($num_removed>0)
			   {
			   $new_cache;
			   reset($this->conn->cache[$folder]);
			   while(list($uid,$item)=each($this->conn->cache[$folder]))
			   {
			   if ($item) $new_cache[$uid] = $this->conn->cache[$folder][$uid];
			   }
			   $this->conn->cache[$folder] = $new_cache;
			   }
			 */
		}

		/**
		 * Explode a string which is quoted
		 *
		 * @param string $delimeter where to explode string
		 * @param string $string string to explode
		 * @returns array exploded string
		 */
		function explode_quoted_string($delimiter, $string)
		{
			$quotes = explode("\"", $string);
			foreach ( $quotes as $key => $val )
			{
				if (($key % 2) == 1) 
				{
					$quotes[$key] = str_replace($delimiter, "_!@!_", $quotes[$key]);
				}
			}
			$string = implode("\"", $quotes);

			$result = explode($delimiter, $string);
			foreach ( $result as $key => $val )
			{
				$result[$key] = str_replace("_!@!_", $delimiter, $result[$key]);
			}
			return $result;
		}

		/**
		 * Check a mailbox for recent messages
		 *
		 * @param string $mailbox target mailbox
		 * @return int number of recent messages, -2 == error 
		 */
		function check_for_recent($mailbox = 'INBOX')
		{
			$this->connect();
			if ($this->conn->fp)
			{
				fputs($this->conn->fp, "a002 EXAMINE \"{$mailbox}\"\r\n");
				do{
					$line = rtrim($this->read_line(300));
					$a = explode(" ", $line);

					if ($a[0] == '*' && $a[2] == 'RECENT')
					{
						$result = intval($a[1]);
					}
				}while (!$this->starts_with($a[0], 'a002'));

				fputs($this->conn->fp, "a003 LOGOUT\r\n");
				fclose($this->conn->fp);
				return $result;
			}
			return -2;
		}

		/**
		 * Select a mailbox
		 *
		 * @param string $mailbox target mailbox
		 * @returns bool was the mailbox found and selected?
		 */
		function select($mailbox)
		{
			if (empty($mailbox))
			{
				return false;
			}

			if ( $this->conn->selected == $mailbox) 
			{
				return true; //already selected
			}

			if (fputs($this->conn->fp, "sel1 SELECT \"{$mailbox}\"\r\n"))
			{
				do{
					$line = rtrim($this->read_line(300));
					$a = explode(' ', $line);
					if (count($a) == 3)
					{
						if ( $a[2] == "EXISTS")
						{
							$this->conn->exists = intval($a[1]);
						}

						if ( $a[2] == "RECENT" )
						{
							$this->conn->recent = intval($a[1]);
						}
					}
				}while (!$this->starts_with($line, 'sel1'));

				$a = explode(" ", $line);

				if ( $a[1] == 'OK' )
				{
					$this->conn->selected = $mailbox;
					return true;
				}
			}
			return false;
		}

		/**
		 * Get count of recent messages
		 *
		 * @param string $mailbox mailbox to check
		 * @returns int number of recent messages, will return 0 on failure
		 */
		function get_recent($mailbox = 'INBOX')
		{
			$this->select($mailbox);
			if ( $this->conn->selected == $mailbox)
			{
				return $this->conn->recent;
			}
			return 0;
		}

		/**
		 * Get the requested properties for the mailbox
		 *
		 * @internal we do it this way cos courier imap is RFC ignorant on returning UIDNEXT when SELECTing
		 * @param string $mbox mailbox for which properties are sought
		 * @param array $props properties sought
		 */
		function get_mailbox_properties($mbox, $props)
		{
			$result = array();
			$i = 0;
			foreach($props as $prop)
			{
				$prop_upper = strtoupper($prop);
				$command = "mp{$i} STATUS {$mbox} ({$prop_upper})\r\n";

				trigger_error($command);
				$line = $data = '';

				if (!fputs($this->conn->fp, $command))
				{
					trigger_error('Unknown error sending STATUS command', E_USER_ERROR);
					return false;
				}

				do{
					$line = trim($this->read_line(1024));
					if ($this->starts_with($line, '* STATUS'))
					{
						preg_match("/\\($prop_upper ([\d]+)\\)/", strtoupper($line), $data);
						$result[$prop] = $data[1];
					}
				} while( substr($line, 0, 2 + strlen($i)) != "mp{$i}" );
				++$i;
			}
			return $result;
		}

		/**
		 * Count number of messages in a mailbox
		 *
		 * @param string $mailbox the name of the mailbox to count the messages in
		 * @param bool $refresh get a new message count?
		 * @return int the number of messages in the mailbox, will return 0 on failure
		 */
		function count_messages($mailbox, $refresh = true)
		{
			if ($refresh)
			{
				$this->conn->selected = '';
			}
			$this->select($mailbox);
			if ($this->conn->selected == $mailbox)
			{
				return $this->conn->exists;
			}
			return 0;
		}

		/**
		 * Split a header string into 2 parts
		 *
		 * @param string $str header string
		 * @returns array header string - key : val becomes 0 => key, 1 => val or 0 => val if not a header string
		 */
		function split_header_line($str)
		{
			$pos = strpos($str, ':');
			if ( $pos > 0 )
			{
				return array
					(
						substr($str, 0, $pos),
						trim(substr($str, $pos + 1) )
					);
			}
			return array($str);
		}

		/**
		 * Convert a date from a string to an int
		 *
		 * @param string the raw date
		 * @returns int the date in seconds since 1-Jan-1970
		 */
		function str2time($str)
		{

			if ( $str )
			{
				$time1 = strtotime($str);
			}
			if ( $time1 && $time1 != -1 )
			{
				return $time1 + ( 0 + $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset']);
			}

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
		 * Sort a mailbox
		 *
		 * @param string $mailbox mailbox to sort
		 * @param string $field the field to sort on
		 * @returns array list of message ids, count() == 0 on failure/no messages
		 */
		function sort($mailbox, $field)
		{
			/*  Do "SELECT" command */
			if ( $this->select($mailbox) )
			{
				trigger_error("Unable to select mailbox: {$mailbox}", E_USER_ERROR);
				return array();
			}

			$field = strtoupper($field);
			if ( $field == 'INTERNALDATE' )
			{
				$field='ARRIVAL';
			}
			$fields = array
				(
				 'ARRIVAL'	=> True,
				 'CC'		=> True,
				 'DATE'		=> True,
				 'FROM'		=> True,
				 'SIZE'		=> True,
				 'SUBJECT'	=> True,
				 'TO'		=> True
				);

			if (!$fields[$field])
			{
				trigger_error("Invalid field \"{$field}\" used for sort, reverting to \"ARRVIAL\"");
				$field = 'ARRIVAL'; //use something sane
			}

			$command = "s UID SORT ({$field}) UTF-8 ALL\r\n";
			$line = $data = '';

			if (!fputs($this->conn->fp, $command))
			{
				trigger_error('Unknown error sending sort command', E_USER_ERROR);
				return false;
			}

			do{
				$line = rtrim($this->read_line(1024));
				if ($this->starts_with($line, '* SORT'))
				{
					$data .= ($data ? ' ' : '' ) . substr($line,7);
				}
			} while( $line[0] != 's' );

			if (empty($data))
			{
				trigger_error("s UID SORT ({$field}) UTF-8 ALL returned invalid result: {$line}");
				return array();
			}

			return explode(' ',$data);
		}

		/**
		 * I don't know what this does
		 *
		 * @internal FIXME proper description needed
		 * @param string $mailbox the target mailbox
		 * @param ??? $message_set ???
		 * @param ??? $index_field ???
		 * @param bool $normalize ???
		 * @returns array
		 */
		function fetch_header_index($mailbox, $message_set, $index_field = 'DATE', $normalize = true )
		{
			$c = 0;
			$result=array();

			$index_field = strtoupper($index_field);

			if (empty($message_set))
			{
				return array();
			}

			$fields_a['DATE'] = 1;
			$fields_a['INTERNALDATE'] = 6;
			$fields_a['FROM'] = 1;
			$fields_a['REPLY-TO'] = 1;
			$fields_a['SENDER'] = 1;
			$fields_a['TO'] = 1;
			$fields_a['SUBJECT'] = 1;
			$fields_a['UID'] = 2;
			$fields_a['SIZE'] = 2;
			$fields_a['SEEN'] = 3;
			$fields_a['RECENT'] = 4;
			$fields_a['DELETED'] = 5;

			$mode = $fields_a[$index_field];
			if ( ($mode <= 0))
			{
				trigger_error("Invalid index field (\"{$index_field}\") supplied to fetch_header_index", E_USER_WARNING);
				return array();
			}

			/*  Do "SELECT" command */
			if (!$this->select($mailbox))
			{
				trigger_error("fetch_header_index unable to select \"{$mailbox}\"", E_USER_ERROR);
				return array();
			}

			/* FETCH date,from,subject headers */
			switch ($mode)
			{
				case 1:
					$key = "fhi" . $c++;
					$request = "{$key} UID FETCH {$message_set} (BODY.PEEK[HEADER.FIELDS ($index_field)])\r\n";
					if (!fputs($this->conn->fp, $request))
					{
						return false;
					}
					do{

						$line = rtrim($this->read_line(200));
						$a = explode(' ', $line);
						if ( $line[0] == '*' && $a[2] == 'FETCH'
								&& $line[strlen($line)-1] != ')' )
						{
							$id=$a[1];

							$str = $line = rtrim($this->read_line(300));

							while ( $line[0] != ')' ) //caution, this line works only in this particular case
							{
								$line = rtrim($this->read_line(300));
								if ( $line[0] != ')' )
								{
									if ( ord($line[0]) <= 32 ) //continuation from previous header line
									{
										$str .= ' ' . trim($line);
									}

									if ( ord($line[0]) > 32 || strlen($line[0]) == 0 )
									{
										list($field, $string) = $this->split_header_line($str);
										if ( $field == 'date')
										{
											$result[$id]=$this->str2time($string);
										}
										else
										{
											$result[$id] = str_replace("\"", "", $string);
											if ($normalize)
											{
												$result[$id] = strtoupper($result[$id]);
											}
										}
										$str = $line;
									}
								}
							}
						}
					}while(!$this->starts_with($line, $key));
					break;

				case 6:
					$key = "fhi" .$c++;
					$request = "{$key} UID FETCH {$message_set} (INTERNALDATE)\r\n";
					if (!fputs($this->conn->fp, $request))
					{
						return array();
					}

					do{
						$line=rtrim($this->read_line(200));
						if ( $line[0] == '*' )
						{
							//original: "* 10 FETCH (INTERNALDATE "31-Jul-2002 09:18:02 -0500")"
							$paren_pos = strpos($line, "(");
							$foo = substr($line, 0, $paren_pos);
							$a = explode(" ", $foo);
							$id = $a[1];

							$open_pos = strpos($line, "\"") + 1;
							$close_pos = strrpos($line, "\"");
							if ($open_pos && $close_pos)
							{
								$len = $close_pos - $open_pos;
								$time_str = substr($line, $open_pos, $len);
								$result[$id] = strtotime($time_str);
							}
						}
						else
						{
							$a = explode(" ", $line);
						}
					}while ( !$this->starts_with($a[0], $key) );
					break;

				default:
					if ( $mode >= 3 )
					{
						$field_name = 'FLAGS';
					}
					else if ( $index_field == 'SIZE')
					{
						$field_name = "RFC822.SIZE";
					}
					else
					{
						$field_name = $index_field;
					}

					/* 			FETCH uid, size, flags		*/
					$key = 'fhi' . $c++;
					$request = "{$key} UID FETCH {$message_set} ($field_name)\r\n";

					if (!fputs($this->conn->fp, $request))
					{
						return array();
					}
					do{
						$line = rtrim($this->read_line(200));
						$a = explode(" ", $line);
						if ( $line[0] == '*' && $a[2] == 'FETCH' )
						{
							$line=str_replace("(", '', $line);
							$line=str_replace(")", '', $line);
							$a=explode(' ', $line);

							$id=$a[1];

							if (isset($result[$id]))
							{
								continue; //if we already got the data, skip forward
							}

							if ($a[3] != $field_name)
							{
								continue;  //make sure it's returning what we requested
							}

							/*  Caution, bad assumptions, next several lines */
							if ( $mode == 2 )
							{
								$result[$id] = $a[4];
							}
							else
							{
								$result[$id] = (strpos(strtoupper($line), $index_field) > 0 ? 'F' : 'N');
							}
						}
					}while(!$this->starts_with($line, $key));
					break;
			}

			//check number of elements...
			list ( $start_mid, $end_mid ) = explode(':', $message_set);
			if ( is_numeric($start_mid) && is_numeric($end_mid) )
			{
				//count how many we should have
				$should_have = $end_mid - $start_mid +1;

				//if we have less, try and fill in the "gaps"
				if (count($result) < $should_have)
				{
					for( $i = $start_mid; $i <= $end_mid; ++$i)
					{
						if ( !isset($result[$i]) )
						{
							$result[$i] = '';
						}
					}
				}
			}
			return $result;	
		}

		/**
		 * Compresses by grouping sequences together a comma delimited list of independent mid's
		 *
		 * @param string $message_set the message ids
		 */
		function compress_message_set($message_set)
		{

			//if less than 255 bytes long, let's not bother
			if ( strlen($message_set) < 255 )
			{
				return $message_set;
			}

			//see if it's already been compress
			if (strpos($message_set, ':') !== false )
			{
				return $message_set;
			}

			//separate, then sort
			$ids = explode(',', $message_set);
			sort($ids);

			$result = array();
			$start = $prev = $ids[0];
			foreach ( $ids as $id )
			{
				$incr = $id - $prev;
				if ( $incr > 1) //found a gap
				{
					if ($start == $prev)
					{
						$result[] = $prev; //push single id
					}
					else
					{
						$result[] = "{$start}:{$prev}"; //push sequence as start_id:end_id
						$start = $id; //start of new sequence
					}
				}
				$prev = $id;
			}
			//handle the last sequence/id
			if ($start == $prev)
			{
				$result[] = $prev;
			}
			else
			{
				$result[] = "{$start}:{$prev}";
			}

			//return as comma separated string
			return implode(',', $result);
		}

		/**
		 * Convert sequence numbers to unique ids
		 *
		 * @param string $mailbox the name of the mailbox
		 * @param string $uids message uids
		 */
		function uids2msgids($mailbox, $uids)
		{
			if ( !is_array($uids) || !count($uids) )
			{
				return array();
			}
			return $this->search($mailbox, 'UID ' . implode(',', $uids) );
		}

		/**
		 * Convert sequence number to unique id
		 *
		 * @param string $mailbox the name of the mailbox
		 * @param int $uid message uid
		 */
		function uid2msgid($mailbox, $uid)
		{
			$result = $this->uids2msgids($mailbox, array($uid) );
			if (count($result) == 1)
			{
				return $result[0];
			}
			return 0;
		}

		/**
		 * Get a list of message uids
		 *
		 * @param string $mailbox mailbox to poll 
		 * @returns array list of message uids
		 */
		function fetch_uids($mailbox)
		{
			$num = $this->count_messages($mailbox);
			if ($num==0)
			{
				return array();
			}
			$message_set = '1:*';

			//otherwise, let's check cache first
			$key = "{$mailbox}.uids";
			$cache_good = true;
			if ($this->conn->uid_cache)
			{
				$data = $this->conn->uid_cache;
			}

			//was anything cached at all?
			if ($data === false)
			{
				$cache_good = false;
			}

			//make sure number of messages were the same
			if ($cache_good && $data['n'] != $num)
			{
				$cache_good = false;
			}

			//if everything's okay so far...
			if ($cache_good)
			{
				//check UIDs of highest mid with current and cached
				$temp = $this->search($mailbox, "UID {$data['d'][$num]}");
				if (!$temp || !is_array($temp) || $temp[0]!=$num)
				{
					$cache_good = false;
				}
			}

			//if cached data's good, return it
			if ($cache_good)
			{
				return $data['d'];
			}

			//otherwise, we need to fetch it
			$data = array
				(
				 'n' => $num,
				 'd'=>array()
				);
			$data['d'] = $this->fetch_header_index($mailbox, $message_set, 'UID');
			//cache_write($key, $data);
			$this->conn->uid_cache = $data;
			return $data['d'];
		}

		/**
		 * Get a list of sorted headers a thread
		 *
		 * @param array $headers message headers
		 * @param array $index_a ???
		 * @param 
		 */
		function sort_thread_headers($headers, $index_a, $uids)
		{
			asort($index_a);
			$result = array();
			foreach($index_a as $mid => $foobar)
			{
				$uid = $uids[$mid];
				$result[$uid] = $headers[$uid];
			}
			return $result;
		}

		/**
		 * Get a list of headers for a threaded mailbox
		 *
		 * @param string $mailbox mailbox name
		 * @param string $message_set messages in thread
		 * @returns array of message headers count() == 0 is empty or fails
		 */
		function fetch_thread_headers($mailbox, $message_set)
		{

			if ( empty($message_set) )
			{
				return array();
			}

			$result = array();
			$uids = $this->fetch_uids($mailbox);

			/* Get cached records where possible */
			$cached = cache_read("{$mailbox}.thhd");
			if ($cached && is_array($uids) && count($uids) > 0)
			{
				$needed_set = "";
				foreach($uids as $id => $uid)
				{
					if ($cached[$uid])
					{
						$result[$uid] = $cached[$uid];
						$result[$uid]->id = $id;
					}
					else
					{
						$needed_set .= ($needed_set ? ',' : '' ) . $id;
					}
				}
				if ($needed_set)
				{
					$message_set = $needed_set;
				}
				else
				{
					$message_set = '';
				}
			}
			$message_set = $this->compress_message_set($message_set);
			trigger_error("Still need: ".$message_set); //debugging code was ere

			/* if we're missing any, get them */
			if ($message_set)
			{
				/* FETCH date,from,subject headers */
				$key = 'fh';
				$request= "{$key} UID FETCH {$message_set} (BODY.PEEK[HEADER.FIELDS (SUBJECT MESSAGE-ID IN-REPLY-TO)])\r\n";
				$mid_to_id = array();
				if (!fputs($this->conn->fp, $request))
				{
					return false;
				}

				do{
					$line = rtrim($this->read_line(1024));
					trigger_error("DEBUG fetch_thread_headers {$line}");
					if (ereg('\{[0-9]+\}$', $line))
					{
						$a = explode(' ', $line);
						$new = array();

						$new_thhd = new imap_thread_header;
						$new_thhd->id = $a[1];
						do{
							$line=rtrim($this->read_line(1024),"\r\n");
							if ($this->starts_withi($line,'Message-ID:') 
									|| ($this->starts_withi($line,'In-Reply-To:')) 
									|| ($this->starts_withi($line,'SUBJECT:')))
							{
								$pos = strpos($line, ':');
								$field_name = substr($line, 0, $pos);
								$field_val = substr($line, $pos+1);
								$new[strtoupper($field_name)] = trim($field_val);
							}
							else if (ereg('^[[:space:]]', $line))
							{
								$new[strtoupper($field_name)].= trim($line);
							}
						} while ( $line[0] != ')' );

						$new_thhd->sbj = $new['SUBJECT'];
						$new_thhd->mid = substr($new['MESSAGE-ID'], 1, -1);
						$new_thhd->irt = substr($new['IN-REPLY-TO'], 1, -1);

						$result[$uids[$new_thhd->id]] = $new_thhd;
					}
				}while(!$this->starts_with($line, 'fh'));
			}

			/* sort headers */
			// FIXME: From what I can tell index_a is crap - skwashd
			if (is_array($index_a))
			{
				$result = $this->sort_thread_headers($result, $index_a, $uids);	
			}

			/* write new set to cache */
			if (count($result)!=count($cached))
			{
				cache_write($this->conn->user, $this->conn->host, $mailbox.'.thhd', $result);
			}

			return $result;
		}

		/**
		 * Build a list of threads
		 *
		 * @param string $mailbox the mailbox containing the messages
		 * @param string $message set the list of message ids
		 * @returns array?
		 */
		function build_threads2($mailbox, $message_set)
		{
			global $index_a;

			if (empty($message_set))
			{
				return false;
			}

			$result = array();
			$roots = array();
			$root_mids = array();
			$sub_mids = array();
			$strays = array();
			$messages = array();

			$sbj_filter_pat = '[a-zA-Z]{2,3}(\[[0-9]*\])?:([[:space:]]*)';

			/*  Do "SELECT" command */
			if (!$this->select($mailbox))
			{
				return array();
			}

			/* FETCH date,from,subject headers */
			$mid_to_id = array();
			$messages = array();
			$headers = $this->fetch_thread_headers($mailbox, $message_set);

			/* go through header records */
			foreach($headers as $header)
			{
				$id = $header->id;
				$new = array
					(
					 'id'		=> $id,
					 'MESSAGE-ID'	=> $header->mid, 
					 'IN-REPLY-TO'	=> $header->irt,
					 'SUBJECT'	=> $header->sbj
					);

				/* add to message-id -> mid lookup table */
				$mid_to_id[$new['MESSAGE-ID']] = $id;

				/* if no subject, use message-id */
				if (empty($new['SUBJECT']))
				{
					$new['SUBJECT'] = $new['MESSAGE-ID'];
				}

				/* if subject contains 'RE:' or has in-reply-to header, it's a reply */
				$sbj_pre = '';
				$has_re = false;
				if (eregi($sbj_filter_pat, $new['SUBJECT']))
				{
					$has_re = true;
				}
				if ( $has_re || $new['IN-REPLY-TO'])
				{
					$sbj_pre = 'RE:';
				}

				/* strip out 're:', 'fw:' etc */
				if ($has_re)
				{
					$sbj = ereg_replace($sbj_filter_pat, '' , $new['SUBJECT']);
				}
				else
				{
					$sbj = $new['SUBJECT'];
				}
				$new['SUBJECT'] = $sbj_pre . $sbj;


				/* if subject not a known thread-root, add to list */
				trigger_error("{$id} {$new['SUBJECT']} {$new['MESSAGE-ID']}");
				$root_id = $roots[$sbj];

				if ($root_id && ($has_re || !$root_in_root[$root_id]))
				{
					trigger_error("found root: {$root_id}");
					$sub_mids[$new['MESSAGE-ID']] = $root_id;
					$result[$root_id][] = $id;
				}
				else if ( !isset($roots[$sbj]) 
						|| ( !$has_re && $root_in_root[$root_id]) )
				{
					/* try to use In-Reply-To header to find root 
					   unless subject contains 'Re:' */
					if ($has_re && $new['IN-REPLY-TO'])
					{
						trigger_error("looking: {$new['IN-REPLY-TO']}");

						//reply to known message?
						$temp = $sub_mids[$new['IN-REPLY-TO']];

						if ($temp)
						{
							//found it, root:=parent's root
							trigger_error("found parent: {$new['SUBJECT']}");
							$result[$temp][] = $id;
							$sub_mids[$new['MESSAGE-ID']] = $temp;
							$sbj = '';
						}
						else
						{
							//if we can't find referenced parent, it's a "stray"
							$strays[$id] = $new['IN-REPLY-TO'];
						}
					}

					//add subject as root
					if ($sbj)
					{
						trigger_error("added to root");
						$roots[$sbj] = $id;
						$root_in_root[$id] = !$has_re;
						$sub_mids[$new['MESSAGE-ID']] = $id;
						$result[$id] = array($id);
					}
					trigger_error("{$new['MESSAGE-ID']} {$sbj}");
				}
			}

			//now that we've gone through all the messages,
			//go back and try and link up the stray threads
			if ( count($strays) > 0)
			{
				foreach( $strays as $id => $irt )
				{
					$root_id = $sub_mids[$irt];
					if (!$root_id || $root_id==$id)
					{
						continue;
					}
					$result[$root_id] = array_merge($result[$root_id],$result[$id]);
					unset($result[$id]);
				}
			}

			trigger_error('roots: ' . print_r($roots,True));
			return $result;
		}


		/**
		 * Sort a threaded list of messages
		 *
		 * @param array $tree structure tree
		 * @param array $index field to sort on
		 * @param string $sort_order the direction to sort messages
		 * @returns sorted threaded messages structure
		 */
		function sort_threads(&$tree, $index, $sort_order = 'ASC')
		{
			if (!is_array($tree) || !is_array($index))
			{
				return false;
			}

			//create an id to position lookup table
			$i = 0;
			foreach ( $index as $id => $val)
			{
				$i++;
				$index[$id] = $i;
			}
			$max = $i + 1;

			//for each tree, set array key to position
			$itree = array();
			foreach($tree as $id => $node)
			{
				if (count($tree[$id])<=1)
				{
					//for "threads" with only one message, key is position of that message
					$n = $index[$id];
					$itree[$n] = array($n=>$id);
				}
				else
				{
					//for "threads" with multiple messages, 
					$min = $max;
					$new_a = array();
					foreach($tree[$id] as $mid)
					{
						$new_a[$index[$mid]] = $mid; //create new sub-array mapping position to id
						$pos = $index[$mid];
						if ( $pos && $pos < $min ) //find smallest position
						{
							$min = $index[$mid];
						}
					}
					$n = $min; //smallest position of child is thread position

					//assign smallest position to root level key
					//set children array to one created above
					ksort($new_a);
					$itree[$n] = $new_a;
				}
			}

			//sort by key, this basically sorts all threads
			ksort($itree);
			$i=0;
			$out=array();
			foreach($itree as $k => $node)
			{
				$out[$i] = $itree[$k];
				$i++;
			}
			return $out;
		}

		/**
		 * creates array mapping mid to thread id
		 *
		 * @param array $tree thread structure
		 * @return array mapped thread structure, count() == 0 for failure
		 */
		function index_threads(&$tree)
		{

			$t_index = array();
			if ( is_array($tree) && count($tree) )
			{
				foreach($tree as $pos => $kids)
				{
					foreach($kids as $kid)
					{
						$t_index[$kid] = $pos;
					}
				}
			}
			return $t_index;
		}

		/**
		 * Get a list of message headers for a mailbox
		 *
		 * @param string $mailbox mailbox name
		 * @param $message_set message ids
		 */
		function fetch_headers($mailbox, $message_set = '1:*')
		{
			$c = 0;
			$result = array();

			if (empty($message_set))
			{
				return array();
			}

			/*  Do "SELECT" command */
			if (!$this->select($mailbox))
			{
				trigger_error("Couldn't select {$mailbox}", E_USER_ERROR);
				return $result;
			}

			/* Get cached records where possible */
			$uids = $this->fetch_header_index($mailbox, $message_set, "UID");
			if (is_array($uids) && count($this->conn->cache[$mailbox] > 0))
			{
				$needed_set = array();
				foreach ( $uids as $id => $uid )
				{
					if ($this->conn->cache[$mailbox][$uid])
					{
						$result[$uid] = $this->conn->cache[$mailbox][$uid];
						$result[$uid]->uid = $uid;
					}
					else
					{
						$needed_set[] = $uid;
					}
				}
				if ( count($needed_set) )
				{
					$message_set = $this->compress_message_set( implode(',', $needed_set) );
				}
				else
				{
					return $result;
				}
			}

			/* FETCH date,from,subject headers */
			$key="fh".($c++);
			$request = "{$key} UID FETCH {$message_set} (BODY.PEEK[HEADER.FIELDS (DATE FROM TO SUBJECT REPLY-TO IN-REPLY-TO CC CONTENT-TRANSFER-ENCODING CONTENT-TYPE MESSAGE-ID)])\r\n";

			trigger_error("comm_imap::fetch_headers({$mailbox}, {$message_set}) caused: {$request} to be executed");

			if (!fputs($this->conn->fp, $request))
			{
				return array();
			}

			do{
				$line = rtrim($this->read_line(200));
				$a = explode(" ", $line);
				if ( $line[0] == '*' && $a[2] == 'FETCH' )
				{
					$id = $a[4]; //UID not seq
					$result[$id] = new imap_basic_header;
					$result[$id]->id = $a[1];
					$result[$id]->uid = $id;
					$result[$id]->mailbox = $mailbox;
					$result[$id]->subject = '';
					/*
					   Start parsing headers.  The problem is, some header "lines" take up multiple lines.
					   So, we'll read ahead, and if the one we're reading now is a valid header, we'll
					   process the previous line.  Otherwise, we'll keep adding the strings until we come
					   to the next valid header line.
					 */
					$i = 0;
					$lines = array();
					do{
						$line = rtrim($this->read_line(300), "\r\n");
						if ( ord($line[0]) <= 32)
						{
							$lines[$i] .= (empty($lines[$i]) ? '': "\n") . trim($line);
						}
						else
						{
							$i++;
							$lines[$i] = trim($line);
						}
					} while ($line[0] != ')');

					/* This is crap which does nothing
					//process header, fill imap_basic_header obj.
					//	initialize
					if ( is_array($headers) )
					{
						foreach( $headers as $k => $bar)
						{
							$headers[$k] = "";
						}
					}
					*/

					//create array with header field:data
					$headers = array();
					foreach( $lines as $lines_key => $str)
					{
						list($field, $string) = $this->split_header_line($str);
						$field = strtolower($field);
						$headers[$field] = $string;
					}
					$result[$id]->date = $headers['date'];
					$result[$id]->timestamp = $this->str2time($headers['date']);
					$result[$id]->from = $headers['from'];
					$result[$id]->to = str_replace("\n", ' ', $headers['to']);
					$result[$id]->subject = str_replace("\n", '', $headers['subject']);
					$result[$id]->replyto = str_replace("\n", ' ', $headers['reply-to']);
					$result[$id]->cc = str_replace("\n", ' ', $headers['cc']);
					$result[$id]->encoding = str_replace("\n", ' ', $headers['content-transfer-encoding']);
					//$result[$id]->ctype = str_replace("\n", ' ', $headers['content-type']);
					//$result[$id]->in_reply_to = ereg_replace("[\n<>]",'', $headers['in-reply-to']);
					list($result[$id]->ctype, $foo) = explode(';', $headers['content-type']);
					$message_id = $headers['message-id'];
					if ($message_id)
					{
						$message_id = substr(substr($message_id, 1), 0, strlen($message_id)-2);
					}
					else
					{
						$message_id = "mid:".$id;
					}
					$result[$id]->message_id = $message_id;
				}
			}while( $a[0] != $key );

			/* 
			   FETCH uid, size, flags
			   Sample reply line: "* 3 FETCH (UID 2417 RFC822.SIZE 2730 FLAGS (\Seen \Deleted))"
			 */
			$command_key = 'fh' . $c++;
			$request= "{$command_key} UID FETCH {$message_set} (UID RFC822.SIZE FLAGS INTERNALDATE)\r\n";
			
			if (!fputs($this->conn->fp, $request))
			{
				return false;
			}
			
			do{
				$line = rtrim($this->read_line(200));
				if ($line[0] == '*')
				{
					//echo "<!-- $line //-->\n";
					//get outter most parens
					$open_pos = strpos($line, '(') + 1;
					$close_pos = strrpos($line, ')');
					if ( $open_pos && $close_pos )
					{
						//extract ID from pre-paren
						$post_str = substr($line, $open_pos);
						$post_a = explode(' ', $post_str);
						$id = $post_a[1];

						//get data
						$len = $close_pos - $open_pos;
						$str = substr($line, $open_pos, $len);

						//swap parents with quotes, then explode
						$str = eregi_replace('[()]', '"', $str);
						$a = $this->explode_quoted_string(' ', $str);

						//did we get the right number of replies?
						if ( ($parts_count = count($a) ) >= 8)
						{
							for ($i = 0; $i < $parts_count; $i = $i + 2)
							{
								switch(strtoupper($a[$i]))
								{
									case 'RFC822.SIZE':
										$result[$id]->size = $a[$i + 1];
										//$i++;
										break;
									
									case 'INTERNALDATE':
										$time_str = $a[$i + 1];
										//$i++;
										break;

									case 'FLAGS':
										$flags_str = $a[$i+1];
										//$i++;
										break;
								}
							}

							// process flags
							$flags_str = eregi_replace('[\\\"]', '', $flags_str);
							$flags_a = explode(' ', $flags_str);
							//echo "<!-- ID: $id FLAGS: ".implode(",", $flags_a)." //-->\n";

							$result[$id]->seen = false;
							$result[$id]->recent = false;
							$result[$id]->deleted = false;
							$result[$id]->draft = false;
							$result[$id]->answered = false;
							if (is_array($flags_a))
							{
								//TODO switch/case me
								foreach( $flags_a as $key => $val)
								{
									if (strcasecmp($val,"Seen")==0)
									{
										$result[$id]->seen = true;
									}
									else if (strcasecmp($val, "Deleted")==0)
									{
										$result[$id]->deleted=true;
									}
									else if (strcasecmp($val, "Draft")==0)
									{
										$result[$id]->draft = true;
									}
									else if (strcasecmp($val, "Recent")==0)
									{
										$result[$id]->recent = true;
									}
									else if (strcasecmp($val, "Answered")==0)
									{
										$result[$id]->answered = true;
									}
								}
								$result[$id]->flags = $flags_str;
							}

							// if time is gmt...	
							$time_str = str_replace('GMT','+0000' , $time_str);

							//get timezone
							$time_str = substr($time_str, 0, -1);
							$time_zone_str = substr($time_str, -5); //extract timezone
							$time_str = substr($time_str, 1, -6); //remove quotes
							$time_zone = (float)substr($time_zone_str, 1, 2); //get first two digits
							if ( $time_zone_str[3] != '0' )
							{
								$time_zone += 0.5;  //handle half hour offset
							}
							if ( $time_zone_str[0]== '-')
							{
								$time_zone = $time_zone * -1.0; //minus?
							}
							$result[$id]->internaldate = $time_str;

							//calculate timestamp
							$timestamp = strtotime($time_str); //return's server's time
							$na_timestamp = $timestamp;
							$timestamp -= $time_zone * 3600; //compensate for tz, get GMT
							$result[$id]->timestamp = $timestamp;

							$uid = $result[$id]->uid;
							$this->conn->cache[$mailbox][$uid] = $result[$id];
							$this->conn->cache_dirty[$mailbox] = true;
							//echo "<!-- ID: $id : $time_str -- local: $na_timestamp (".date("F j, Y, g:i a", $na_timestamp).") tz: $time_zone -- GMT: ".$timestamp." (".date("F j, Y, g:i a", $timestamp).")  //-->\n";
						}
						else
						{
							//echo "<!-- ERROR: $id : $str //-->\n";
						}
					}
				}
			} while(strpos($line, $command_key) === false);
			return $result;
		}

		/**
		 * Get the headers for a message
		 *
		 * @param string $mailbox the mailbox holding the message
		 * @param string $id message id
		 * @returns array message headers, count() == on failure
		 */
		function fetch_header($mailbox, $id)
		{
			$a = $this->fetch_headers($mailbox, $id);
			if (is_array($a))
			{
				return $a[$id];
			}
			return array();
		}

		/**
		 * Sort a list of messages
		 *
		 * @param array $a array of message headers
		 * @param string $field field to sort on
		 * @param string $flag direction to sort ASC|DESC
		 */
		function sort_headers($a, $field = 'uid', $flag = 'ASC')
		{
			$field = strtolower($field);

			if ( $field == 'date' || $field == 'internaldate')
			{
				$field = 'timestamp';
			}

			$flag = strtoupper($flag);

			$c = count($a);
			if ($c > 0)
			{
				/*
				Strategy:
				First, we'll create an "index" array.
				Then, we'll use sort() on that array, 
				and use that to sort the main array.
				*/

				// create "index" array
				$index = array();
				foreach($a as $key => $val)
				{
					$data = $a[$key]->$field;
					if (is_string($data))
					{
						$data=strtoupper(str_replace('"', '', $data));
					}
					$index[$key] = $data;
				}

				// sort index
				if ( $flag == 'ASC')
				{
					asort($index);
				}
				else
				{
					arsort($index);
				}

				// form new array based on index 
				$i = 0;
				$result=array();
				foreach ( $index as $key => $val )
				{
					$result[$i] = $a[$key];
					++$i;
				}
			}
			return $result;
		}

		/**
		 * Permenantly removes deleted items from a mailbox
		 *
		 * @param string $mailbox the mailbox to expunge
		 * @returns int the number of messages expunged, -1 on failure
		 */ 
		function expunge($mailbox)
		{
			$line = 'unable to select';
			if ($this->select($mailbox))
			{
				$c=0;
				fputs($this->conn->fp, "exp1 EXPUNGE\r\n");
				do{
					$line = rtrim($this->read_line(100));
					if ( $line[0] == '*')
					{
						++$c;
					}
				}while ( !$this->starts_with($line, 'exp1') );

				if ( $this->parse_result($line) == 0)
				{
					$this->conn->selected = ''; //state has changed, need to reselect			
					return $c;
				}
			}
			trigger_error("Failed to expunge {$mailbox}: {$line}", E_USER_ERROR);
			return -1;
		}

		/**
		 * Modify a flag on message/s
		 *
		 * @param string $mailbox the mailbox holding the message/s
		 * @param string $messages the message/s
		 * @param string $flag the RFC 3051 flag to modify
		 * @param char $mod action to take + set, - unset
		 * @returns int the number of flags modified, -1 for failure
		 */
		function mod_flag($mailbox, $messages, $flag, $mod)
		{
			trigger_error("mod_flag($mailbox, $messages, $flag, $mod)");
			if ( $mod != '+' && $mod!= '-')
			{
				return -1;
			}

			$flags = array
				(
					'SEEN'		=> "\\Seen",
					'DELETED'	=> "\\Deleted",
					'RECENT'	=> "\\Recent",
					'ANSWERED'	=> "\\Answered",
					'DRAFT'		=> "\\Draft",
					'FLAGGED'	=> "\\Flagged"
				);

			$flag = strtoupper($flag);
			$flag = $flags[$flag];
			if ( $this->select($mailbox) )
			{
				$c=1;
				fputs($this->conn->fp, "flg UID STORE $messages {$mod}FLAGS ({$flag})\r\n");
				do{
					$line = rtrim($this->read_line(100));
					error_log($line);
					if ( $line[0] == '*')
					{
						++$c;
					}
				}while (!$this->starts_with($line, 'flg'));

				if ($this->parse_result($line) == 0)
				{
					//$this->expire_cached_items($mailbox, $messages);
					return $c;
				}
				else
				{
					trigger_error("Failed to set flag {$mod}{$flag} on {$mailbox} msgs ({$messages}): {$line}", E_USER_WARNING);
					return -1;
				}
			}
			trigger_error("Failed to select {$mailbox}", E_USER_ERROR);
			return -1;
		}

		/**
		 * Set a flag on a message
		 *
		 * @param string $mailbox the mailbox the message is in
		 * @param string $messages the message uid/s
		 * @param string $flag RFC 3501 message flag to be set
		 */
		function flag($mailbox, $messages, $flag)
		{
			return $this->mod_flag($mailbox, $messages, $flag, '+');
		}

		/**
		 * Unset a flag on a message
		 *
		 * @param string $mailbox the mailbox the message is in
		 * @param string $messages the message uid/s
		 * @param string $flag RFC 3501 message flag to be unset
		 */
		function unflag($mailbox, $messages, $flag)
		{
			return $this->mod_flag($mailbox, $messages, $flag, '-');
		}

		/**
		 * Mark a message as deleted
		 *
		 * @param string $mailbox the mailbox the message is in
		 * @param string $messages the message uid/s
		 */
		function delete($mailbox, $messages)
		{
			return $this->mod_flag($mailbox, $messages, 'DELETED', '+');
		}

		/**
		 * Mark a message as "undeleted"
		 *
		 * @param string $mailbox the mailbox the message is in
		 * @param string $messages the message uid/s
		 */
		function undelete($mailbox, $messages)
		{
			return $this->mod_flag($mailbox, $messages, 'DELETED', '-');
		}

		/**
		 * Mark a message as unseen
		 *
		 * @param string $mailbox the mailbox the message is in
		 * @param string $messages the message uid/s
		 */
		function unseen($mailbox, $messages)
		{
			return $this->mod_flag($mailbox, $messages, 'SEEN', '-');
		}


		/**
		 * Copy message or messages to another mailbox
		 *
		 * @param string $messages the messages to copy
		 * @param string $from the mailbox to copy from
		 * @param string $to the mailbox to copy to
		 */
		function copy($messages, $from, $to)
		{
			if (empty($from) || empty($to))
			{
				return -1;
			}

			if ($this->select($from))
			{
				$c=0;

				fputs($this->conn->fp, "cpy1 UID COPY {$messages} \"{$to}\"\r\n");
				$line = $this->read_reply();
				return $this->parse_result($line);
			}
			else
			{
				return -1;
			}
		}

		/**
		 * Format a date so it to d-M-Y style
		 *
		 * @param int $month month
		 * @param int $day day
		 * @param int $year year
		 * @returns string the formatted date
		 */
		function format_search_date($month, $day, $year)
		{
			return date('d-M-Y', mktime(0, 0, 0, $month, $day, $year) );
		}

		/**
		 * Count the number of unseen meesages in a mailbox
		 *
		 * @param string $mailbox the mailbox to count messages in
		 * @returns int the number of unseen messages
		 */
		function count_unseen($mailbox)
		{
			$index = $this->search($mailbox, 'ALL UNSEEN');
			if ( is_array($index) )
			{
				$str = implode(",", $index);
				if (empty($str))
				{
					return false;
				}
				else
				{
					return count($index);
				}
			}
			else
			{
				return 0;
			}
		}

		/**
		 * Convert a UID to a sequence number
		 *
		 * @internal TODO remove this - it shouldn't be needed
		 * @param string $mailbox the mailbox holding the message
		 * @param int $uid unique id
		 * @returns sequence number, 0 = not found
		 */
		function uid2seq($mailbox, $uid)
		{
			if ($uid > 0)
			{
				$id_a = $this->search($this->conn, $folder, "UID {$uid}");
				if (is_array($id_a))
				{
					$count = count($id_a);
					if ($count > 1)
					{
						return 0;
					}
					else
					{
						return $id_a[0];
					}
				}
			}
			return 0;
		}

		/**
		 * Search for a message
		 *
		 * @param string $mailbox the mailbox holding the message
		 * @param string $criteria the search criteria
		 * @returns array list of message ids 
		 */
		function search($mailbox, $criteria)
		{
			if ( $this->select($mailbox) )
			{
				$c=0;

				$query = 'srch1 SEARCH ' . rtrim($criteria) . "\r\n";
				fputs($this->conn->fp, $query);
				do{
					$line = trim($this->read_line(10000));
					if ( stripos($line, '* SEARCH') !== False)
					{
						$str = trim(substr($line, 8));
						$messages = explode(' ', $str);
					}
				}while ( !$this->starts_with($line, 'srch1') );

				$result_code = $this->parse_result($line);
				if ( $result_code == 0 )
				{
					return $messages;
				}
				else{
					trigger_error("search failed: {$line}", E_USER_ERROR);
					return false;
				}

			}
			else
			{
				trigger_error("search: Couldn't select \"{$mailbox}\"", E_USER_ERROR);
				return false;
			}
		}

		/**
		 * Move a message from one mailbox to another
		 *
		 * @param int $id message id
		 * @param string $from source mailbox
		 * @param string $to target mailbox
		 * @returns int was the message moved? -1 == invalid 0 == sucess, other == failed
		 */
		function move($id, $from, $to)
		{
			if (!$from || !$to)
			{
				return -1;
			}

			$r= $this->copy($id, $from, $to);
			if ($r == 0)
			{
				return $this->delete($from, $messages);
			}
			return $r;
		}

		/**
		 * Get the folder hierarchy delimeter
		 *
		 * @returns string delimeter
		 */
		function get_hierarchy_delimiter()
		{
			if ($this->conn->delimiter)
			{
				return $this->conn->delimiter;
			}

			$delimiter = '';

			//try (LIST "" ""), should return delimiter (RFC2060 Sec 6.3.8)
			if (!fputs($this->conn->fp, "ghd LIST \"\" \"\"\r\n"))
			{
				return false;
			}
			do{
				$line=$this->read_line(500);
				if ($line[0]=="*")
				{
					$line = rtrim($line);
					$a= $this->explode_quoted_string(" ", $line);
					if ($a[0]=="*")
					{
						$delimiter = str_replace("\"", "", $a[count($a)-2]);
					}
				}
			}while (!$this->starts_with($line, 'ghd'));

			if (strlen($delimiter) > 0)
			{
				return $delimiter;
			}

			//if that fails, try namespace extension
			//try to fetch namespace data
			fputs($this->conn->fp, "ns1 NAMESPACE\r\n");
			do{
				$line = $this->read_line(1024);
				if ($this->starts_with($line, '* NAMESPACE'))
				{
					$i = 0;
					$data = $this->parse_namespace2(substr($line, 11), $i, 0, 0);
				}
			}while(!$this->starts_with($line, 'ns1'));

			if (!is_array($data))
			{
				return '';
			}

			//extract user space data (opposed to global/shared space)
			$user_space_data = $data[0];
			if (!is_array($user_space_data))
			{
				return '';
			}

			//get first element
			$first_userspace = $user_space_data[0];
			if (!is_array($first_userspace))
			{
				return '';
			}

			//extract delimiter
			return $first_userspace[1];	
		}

		/**
		 * List mailboxes
		 *
		 * @param string $ref the root path for all mailboxes - default root
		 * @param string $search the mailbox to match - default *
		 */
		function list_mailboxes($ref, $search)
		{
			
			if (empty($search))
			{
				$search = '*';
			}
			
			if ( empty($ref) && $this->conn->rootdir)
			{
				if ( isset($this->acct_info['extra']['server_prefix']) && $this->acct_info['extra']['server_prefix'] )
				{
					$ref = $this->acct_info['extra']['server_prefix'];
				}
				else
				{
					$ref = $this->conn->rootdir;
				}
			}
			
			// send command
			if (!fputs($this->conn->fp, "lmb LIST \"{$ref}\" \"{$search}\"\r\n"))
			{
				return false;
			}
			$mailboxes = array();
			$i=0;
			// get folder list
			do{
				$line=$this->read_line(500);
				$line=$this->read_multi_line($line);

				$a = explode(" ", $line);
				if ( ($line[0] == '*') && ($a[1]=='LIST') )
				{
					$line = rtrim($line);
					// split one line
					$a = $this->explode_quoted_string(' ', $line);
					// last string is folder name
					$mailbox = str_replace("\"", "", $a[count($a)-1]);
					if (empty($ignore) || (!empty($ignore) && !eregi($ignore, $mailbox)))
					{
						$mailboxes[$i] = $mailbox;
					}
					// second from last is delimiter
					$delim = str_replace("\"", "", $a[count($a)-2]);
					// is it a container?
					$i++;
				}
			}while (!$this->starts_with($line, 'lmb'));

			if (is_array($mailboxes))
			{
				if (!empty($ref))
				{
					// if rootdir was specified, make sure it's the first element
					// some IMAP servers (i.e. Courier) won't return it
					if ($ref[strlen($ref)-1] == $delim)
					{
						$ref = substr($ref, 0, strlen($ref)-1);
					}
					if ($mailboxes[0] != $ref)
					{
						array_unshift($mailboxes, $ref);
					}
				}
				sort($mailboxes, SORT_STRING);
				return $mailboxes;
			}
			else if ($this->parse_result($line) == 0)
			{
				return array('INBOX');
			}
			else
			{
				trigger_error("Error listing mailboxes {$ref} {$mailbox}: {$line}", E_USER_ERROR);
				return false;
			}
		}


		/**
		 * List subscribed mailboxes
		 *
		 * @param string $ref the root path for all mailboxes - default root
		 * @param string $mailbox the mailbox to match - default *
		 */
		function list_subscribed($ref = '', $mailbox = '')
		{
			if (empty($mailbox)) 
			{
				$mailbox = '*';
			}
			if (empty($ref) && $this->conn->rootdir)
			{
				$ref = $this->conn->rootdir;
			}
			$folders = array();

			// send command
			if (!fputs($this->conn->fp, "lsb LSUB \"{$ref}\" \"{$mailbox}\"\r\n"))
			{
				trigger_error("Couldn't send LSUB command", E_USER_ERROR);
				return false;
			}
			$i=0;
			// get folder list
			do{
				$line=$this->read_line(500);
				$line=$this->read_multi_line($line);
				$a = explode(" ", $line);
				if (($line[0] == '*') && ( $a[1]=='LSUB') )
				{
					$line = rtrim($line);
					// split one line
					$a = $this->explode_quoted_string(" ", $line);
					// last string is folder name
					//$folder = UTF7DecodeString(str_replace("\"", "", $a[count($a)-1]));
					$folder = str_replace("\"", "", $a[count($a)-1]);
					if ((!in_array($folder, $folders)) && (empty($ignore) || (!empty($ignore) && !eregi($ignore, $folder)))) $folders[$i] = $folder;
					// second from last is delimiter
					$delim = str_replace("\"", "", $a[count($a)-2]);
					// is it a container?
					$i++;
				}
			}while (!$this->starts_with($line, "lsb"));

			if (is_array($folders))
			{
				if (!empty($ref))
				{
					// if rootdir was specified, make sure it's the first element
					// some IMAP servers (i.e. Courier) won't return it
					if ($ref[strlen($ref)-1] == $delim)
					{
						$ref = substr($ref, 0, strlen($ref)-1);
					}
					if ($folders[0] != $ref)
					{
						array_unshift($folders, $ref);
					}
				}
				return $folders;
			}
			else
			{
				trigger_error("Error listing subscribed mailboxes {$ref} {$mailbox}: {$line}", E_USER_ERROR);
				return false;
			}
		}


		/**
		 * Subscribe to a mailbox
		 *
		 * @param string $mailbox name of mailbox to subscribe to
		 * @returns bool was the mailbox subscribed?
		 */
		function subscribe($mailbox)
		{
			$query = "sub1 SUBSCRIBE \"{$folder}\"\r\n";
			fputs($this->conn->fp, $query);
			$line = trim(rtrim($this->read_line(10000)));
			return $this->parse_result($line);
		}


		/**
		 * Unsubscribe from a mailbox
		 *
		 * @param string $mailbox name of mailbox to unsubscribe from
		 * @returns bool was the mailbox unsubscribed?
		 */
		function unsubscribe($mailbox)
		{
			$query = "usub1 UNSUBSCRIBE \"{$folder}\"\r\n";
			fputs($this->conn->fp, $query);
			$line = trim(rtrim($this->read_line(10000)));
			return $this->parse_result($line);
		}


		/**
		 * Fetch the headers for the specified message part
		 *
		 * @param string $mailbox the where the message is stored
		 * @param int $id the message id
		 * @param int $part the message part
		 */
		function fetch_header_part($mailbox, $id, $part = 0)
		{
			$result=false;
			if ( ($part == 0) || (empty($part) ) ) 
			{
				$part = 'HEADER';
			}
			else
			{
				$part .= '.MIME';
			}

			if ( $this->select($mailbox) )
			{
				$key = "fh".($c++);
				$request = "{$key} UID FETCH {$id} (BODY.PEEK[{$part}])\r\n";
				if (!fputs($this->conn->fp, $request))
				{
					return false;
				}

				do{
					$line = rtrim($this->read_line(200));
					$a = explode(" ", $line);
					if (($line[0]=="*") && ($a[2]=="FETCH") && ($line[strlen($line)-1]!=")"))
					{
						$line = $this->read_line(300);
						while(rtrim($line)!=")")
						{
							$result .= $line;
							$line = $this->read_line(300);
						}
					}
				} while(strcmp($a[0], $key) != 0);
			}

			return $result;
		}


		/**
		 * Get the nominated part of the message
		 *
		 * @param string $mailbox the name of the mailbox the message lives in
		 * @param int $id the message id
		 * @param int $part part number (0 == text body)
		 * @param int $mode the mode the message is requested
		 * @returns message part as a string
		 */
		function handle_body_part($mailbox, $id, $part, $mode = 1)
		{
			/* 
			 * modes:
			 * 1: return string
			 * 2: no longer used
			 * 3: return base64 decode string
			 */
			$result=false;
			if ( ($part == 0) || (empty($part) ) )
			{
				$part = 'TEXT';
			}

			if ( $this->select($mailbox) )
			{
				$reply_key = "* $id";

				$key = "ftch" . $c++ . ' ';
				$request = "{$key} UID FETCH {$id} (BODY.PEEK[$part])\r\n";

				if (!fputs($this->conn->fp, $request))
				{
					return false;
				}

				do{
					$line = rtrim($this->read_line(1000));
					$a = explode(' ', $line);
				} while ( $a[2] != 'FETCH' );

				$len = strlen($line);

				if ($line[$len-1] == ")")
				{
					//one line response, get everything between first and last quotes
					$from = strpos($line, "\"") + 1;
					$to = strrpos($line, "\"");
					$len = $to - $from;
					switch($mode)
					{
						case 1:
							$result = substr($line, $from, $len);
							break;

						case 2:
							trigger_error('comm_imap::handle_body_part called with deprecated mode', E_USER_WARNING);
							$result = substr($line, $from, $len);
							break;

						case 3:
							$result = base64_decode(substr($line, $from, $len));
							break;
						default:
							trigger_error('comm_imap::handle_body_part called with invalid mode', E_USER_WARNING);
					}
				}
				else if ($line[$len-1] == "}")
				{
					//multi-line request, find sizes of content and receive that many bytes
					$from = strpos($line, "{") + 1;
					$to = strrpos($line, "}");
					$len = $to - $from;
					$sizeStr = substr($line, $from, $len);
					$bytes = (int)$sizeStr;
					$received = 0;

					while ($received < $bytes + 1 )
					{
						$remaining = $bytes - $received;
						$line = $this->read_line(1024);
						$len = strlen($line);
						if ($len > $remaining) substr($line, 0, $remaining);
						$received += strlen($line);
						switch($mode)
						{

							case 1:
								$result .= rtrim($line)."\n";
								break;

							case 2:
								trigger_error('comm_imap::handle_body_part called with deprecated mode', E_USER_WARNING);
								$result = substr($line, $from, $len);
								break;

							case 3:
								$result .= base64_decode($line) . "\n";
								break;
							default:
								trigger_error('comm_imap::handle_body_part called with invalid mode', E_USER_WARNING);
						}
					}
				}
				// read in anything up until 'til last line
				do{
					$line = $this->read_line(1024);
				}while(!$this->starts_with($line, $key));

				if ($result)
				{
					$result = rtrim($result);
					return substr($result, 0, strlen($result)-1);
				}
				else
				{
					return false;
				}
			}
			else
			{
				trigger_error("Select mailbox: {$mailbox} failed.", E_USER_ERROR);
			}

			if ( $mode == 1 || $mode == 3 )
			{
				return $result;
			}
			else
			{
				return $received;
			}
		}

		/**
		 * Get the nominated part of the message
		 *
		 * @param string $mailbox the name of the mailbox the message lives in
		 * @param int $id the message id
		 * @param int part number (0 == text body)
		 * @returns message part as a string
		 */
		function fetch_body_part($mailbox, $id, $part)
		{
			return $this->handle_body_part($mailbox, $id, $part);
		}

		/**
		 * Get the nominated part of the message and base64 decode it
		 *
		 * @param string $mailbox the name of the mailbox the message lives in
		 * @param int $id the message id
		 * @param int part number (0 == text body)
		 * @returns message part as a string
		 */
		function fetch_base64_body_part($mailbox, $id, $part)
		{
			return $this->handle_body_part($mailbox, $id, $part, 3);
		}

		/**
		 * @deprecated
		 */
		function print_body_part($mailbox, $id, $part)
		{
			$this->handle_body_part($mailbox, $id, $part, 2);
		}

		/**
		 * @deprecared
		 */
		function print_base64_body($mailbox, $id, $part)
		{
			$this->handle_body_part($mailbox, $id, $part, 3);
		}

		/**
		 * Create a mailbox
		 *
		 * @param string $mailbox name of the new mailbox
		 * @return bool was the mailbox created?
		 */
		function create_mailbox($mailbox)
		{
			if (fputs($this->conn->fp, "c CREATE \"{$mailbox}\"\r\n"))
			{
				do{
					$line=$this->read_line(300);
				} while ($line[0] != 'c');
				trigger_error("Error creating mailbox \"{$mailbox}\" {$line}", E_USER_USER);
				return ($this->parse_result($line)==0);
			}
			else
			{
				return false;
			}
		}

		/**
		 * Rename a mailbox
		 *
		 * @param string $from the current name of the mailbox
		 * @param string $to the new name of the mailbox
		 * @return bool was the mailbox renamed / moved?
		 */
		function rename_mailbox($from, $to)
		{
			if (fputs($this->conn->fp, "r RENAME \"".$from."\" \"".$to."\"\r\n"))
			{
				do{
					$line = $this->read_line(300);
				} while($line[0] != 'r');

				return ($this->parse_result($line)==0);
			}
			else
			{
				return false;
			}	
		}

		function delete_mailbox($mailbox)
		{
			if (fputs($this->conn->fp, "d DELETE \"{$mailbox}\"\r\n"))
			{
				do{
					$line = $this->read_line(300);
				} while( $line[0] != 'd' );

				return ($this->parse_result($line)==0);
			}
			else
			{
				trigger_error("Couldn't send command d DELETE \"{$mailbox}\"", E_USER_ERROR);
				return false;
			}
		}

		/**
		 * Append message to mailbox
		 *
		 * @internal TODO add additional flag support \\Draft etc
		 * @param string $mailbox target mailbox
		 * @param string $message message contents
		 */
		function append($mailbox, $message)
		{
			if (!$folder)
			{
				return false;
			}

			$message = str_replace("\r", "", $message);
			$message = str_replace("\n", "\r\n", $message);		

			$len = strlen($message);
			if (!$len)
			{
				return false;
			}

			$request="A APPEND \"".$folder."\" (\\Seen)		{".$len."}\r\n";
			echo $request.'<br>';
			if (fputs( $this->conn->fp, $request))
			{
				$line=$this->read_line(100);
				//echo $line.'<br>';

				$sent = fwrite($this->conn->fp, $message."\r\n");
				flush();
				do{
					$line = $this->read_line(1000);
					//echo $line.'<br>';
				}while ( $line[0] != "A" );

				$result = ($this->parse_result($line)==0);
				if (!$result)
				{
					trigger_error("Can't append message: {$line}", E_USER_ERROR);
				}
				return $result;

			}
			else
			{
				$this->conn->error .= "Couldn't send command \"$request\"<br>\n";
				return false;
			}
		}


		//TODO Decide if I stay - appears to be useless atm
		function iil_C_AppendFromFile($mailbox, $path)
		{
			if (!$mailbox)
			{
				return false;
			}

			//open message file
			$in_fp = false;				
			if (file_exists(realpath($path))) $in_fp = fopen($path, "r");
			if (!$in_fp)
			{ 
				$this->conn->error .= "Couldn't open $path for reading<br>\n";
				return false;
			}

			$len = filesize($path);
			if (!$len)
			{
				return false;
			}

			//send APPEND command
			$request="A APPEND \"{$mailbox}\" (\\Seen) \{{$len}\}\r\n";
			$bytes_sent = 0;
			if (fputs($this->conn->fp, $request))
			{
				$line=$this->read_line(100);

				//send file
				while(!feof($in_fp))
				{
					$buffer = fgets($in_fp, 4096);
					$bytes_sent += strlen($buffer);
					fputs($this->conn->fp, $buffer);
				}
				fclose($in_fp);

				fputs($this->conn->fp, "\r\n");

				//read response
				do{
					$line=$this->read_line(1000);
					echo $line.'<br>';
				}while($line[0]!="A");

				$result = ($this->parse_result($line)==0);
				if (!$result) $this->conn->error .= $line."<br>\n";
				return $result;

			}else{
				$this->conn->error .= "Couldn't send command \"$request\"<br>\n";
				return false;
			}
		}


		/**
		 * Get the structure of a message
		 *
		 * @param string $mailbox the name of the mailbox containing the message
		 * @param int $id the message id
		 */
		function fetch_structure_string($mailbox, $id)
		{
			$result = false;
			if ($this->select($mailbox))
			{
				$key = 'F1247';
				if (fputs($this->conn->fp, "$key UID FETCH {$id} (BODYSTRUCTURE)\r\n"))
				{
					do{
						$line=rtrim($this->read_line(5000));
						if ($line[0]=="*")
						{
							if (ereg("\}$", $line))
							{
								preg_match('/(.+)\{([0-9]+)\}/', $line, $match);  
								$result = $match[1];
								do{
									$line = rtrim($this->read_line(100));
									if (!preg_match("/^$key/", $line)) $result .= $line;
									else $done = true;
								}while(!$done);
							}else{
								$result = $line;
							}
							list($pre, $post) = explode('BODYSTRUCTURE ', $result);
							$result = substr($post, 0, strlen($post) -1); //truncate last ')' and return
						}
					} while (!preg_match("/^{$key}/",$line));
				}
			}
			return $result;
		}

		/**
		 * Get the source of a message part
		 *
		 * @param string $mailbox the name of the mailbox
		 * @param int $id message id
		 * 
		 */
		function get_source($folder, $id, $part)
		{
			return $this->fetch_part_header($mailbox, $id, $part)
				. $this->print_part_body($mailbox, $id, $part);
		}


		/**
		 * Get the user's account quota
		 *
		 * @returns array quota 'used' => amount used in bytes, 'total' => total b available, 'percent' => % free and b free
		 */
		function get_quota()
		{
			/*
			   b GETQUOTAROOT "INBOX"
			 * QUOTAROOT INBOX user/rchijiiwa1
			 * QUOTA user/rchijiiwa1 (STORAGE 654 9765)
			 b OK Completed
			 */
			$result=false;
			$quota_line = "";

			//get line containing quota info
			if (fputs($this->conn->fp, "QUOT1 GETQUOTAROOT \"INBOX\"\r\n"))
			{
				do{
					$line=rtrim($this->read_line(5000));
					if ($this->starts_with($line, "* QUOTA "))
					{
						$quota_line = $line;
					}
				} while(!$this->starts_with($line, "QUOT1"));
			}

			//return false if not found, parse if found
			if (!empty($quota_line))
			{
				$quota_line = eregi_replace("[()]", "", $quota_line);
				$parts = explode(" ", $quota_line);
				$storage_part = array_search("STORAGE", $parts);
				if ($storage_part>0)
				{
					$result = array();
					$used = $parts[$storage_part+1];
					$total = $parts[$storage_part+2];
					$result["used"] = $used;
					$result["total"] = (empty($total)?"??":$total);
					$result["percent"] = (empty($total)?"??":round(($used/$total)*100));
					$result["free"] = 100 - $result["percent"];
				}
			}

			return $result;
		}


		/**
		 * Remove all message from a mailbox
		 *
		 * @param string $mbox mailbox
		 * @return bool was it emptied sucessfully?
		 */
		function empty_mailbox($mbox)
		{
			$this->delete($mbox, '1:*');
			return ($this->expunge($mbox) >= 0);
		}

		/**
		 * Test that an account object is valid
		 *
		 * @private
		 * @param object $acct the accounts object to test
		 * @returns bool is it valid?
		 */
		function _validate_account($acct)
		{
			return true;
		}
	}
?>
