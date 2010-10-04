<?php
	/**
	* phpGroupWare API - email message parser
	*
	* @author Ilia Alshanetsky ilia at ilia.ws
	* @author Dave Hall skwashd at phpGroupWare.org
	* @copyright Copyright (C) 2001-2006 Advanced Internet Designs Inc.
	* @copyright Portions Copyright (C) 2006-2007 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal a modified version of fud_emsg in FUDForum's scripts/maillist.php - http://cvs.prohost.org/c/index.cgi/FUDforum/getfile/install/forum_data/scripts/maillist.php?v=1.72
	* @package phpgwapi
	* @subpackage mail
	* @version $Id$
	*/

	class phpgwapi_mail2array
	{
		/**
		* @var array $attachments the attachments for the message
		*/
		var $attachments = array();

		/**
		* @var string $body the body of the message
		*/
		var $body;

		/**
		* @var ??? $body_c ???
		*/
		var $body_s;
		
		/**
		* @var object $body_c message sub components (used for message/rfc822 and message/multipart messages)
		*/
		var $body_sc;

		/**
		* @var string $from_email the sender's email address
		*/ 
		var $from_email;

		/**
		* @var string $from_email the sender's name
		*/ 
		var $from_name;

		/**
		* @var int $handler_id the message handler db id
		*/
		var $handler_id;

		/**
		* @var string $headers the raw message headers
		*/
		var $headers;

		/**
		* @var array $inline_files the files embedded in the message
		*/
		var $inline_files = array();

		/**
		* @var string $ip the IP address that the message originated from 
		*/
		var $ip;

		/**
		* @var string $msg_id the unique message identifier
		*/
		var $msg_id;

		/**
		* @var string $phpgw_header the phpGroupWare header, used by some applications, optional
		*/
		var $phpgw_header;

		/**
		* @var string $raw_message the whole message as a raw string
		*/
		var $raw_msg;

		/**
		* @var string $reply_to the address any replies should be directed to
		*/
		var $reply_to;

		/**
		* @var string $reply_to_msg_id the unique message id that this message is replying to
		*/
		var $reply_to_msg_id;

		/**
		* @var string $subjet the subject of the message
		*/
		var $subject;

		/**
		* @var string $thread_id the thread this message belongs to
		*/
		var $thread_id;

		/**
		* @var string $to the email address the message was sent to
		*/
		var $to;

		/**
		* @var int? $user_id - not used in phpgw ?
		*/
		var $user_id;

		function read_data($data='')
		{
			$this->raw_msg = !$data ? file_get_contents("php://stdin") : $data;
		}

		function split_hdr_body()
		{
			if (!preg_match("!^(.*?)\r?\n\r?\n(.*)!s", $this->raw_msg, $m))
			{
				return;
			}

			$this->body = $m[2];
			$this->headers = $m[1];
		}

		function format_header()
		{
			$this->headers = str_replace("\r\n", "\n", $this->headers);
			// cleanup multiline headers
			$this->headers = preg_replace("!\n(\t| )+!", ' ', $this->headers);
			$hdr = explode("\n", trim($this->headers));
			$this->headers = array();
			foreach ($hdr as $v)
			{
				$hk = substr($v, 0, ($p = strpos($v, ':')));
				// Skip non-valid header lines
				if (!$hk || ++$p == strlen($v) || ($v{$p} != ' ' && $v{$p} != "\t"))
				{
					continue;
				}

				$hv = substr($v, $p);
				$hk = strtolower(trim($hk));

				if (!isset($this->headers[$hk]))
				{
					$this->headers[$hk] = decode_header_value($hv);
				}
				else
				{
					$this->headers[$hk] .= ' '.decode_header_value($hv);
				}
			}
		}

		function parse_multival_headers($val, $key)
		{
			if (($p = strpos($val, ';')) !== false)
			{
				$this->headers[$key] = strtolower(trim(substr($val, 0, $p)));
				$val = ltrim(substr($val, $p+1));
				if (!empty($val) && preg_match_all('!([-A-Za-z]+)="?(.*?)"?\s*(?:;|$)!', $val, $m))
				{
					$c = count($m[0]);
					for ($i=0; $i<$c; ++$i)
					{
						$this->headers['__other_hdr__'][$key][strtolower($m[1][$i])] = $m[2][$i];
					}
				}
			}
			else
			{
				$this->headers[$key] = strtolower(trim($val));
			}
		}

		function handle_content_headers()
		{
			// This functions performs special handling needed for parsing message data

			if (isset($this->headers['content-type']))
			{
				$this->parse_multival_headers($this->headers['content-type'], 'content-type');
			}
			else
			{
				$this->headers['content-type'] = 'text/plain';
				$this->headers['__other_hdr__']['content-type']['charset'] = 'us-ascii';
			}

			if (isset($this->headers['content-disposition']))
			{
				$this->parse_multival_headers($this->headers['content-disposition'], 'content-disposition');
			}
			else
			{
				$this->headers['content-disposition'] = 'inline';
			}
			if (isset($this->headers['content-transfer-encoding']))
			{
				$this->parse_multival_headers($this->headers['content-transfer-encoding'], 'content-transfer-encoding');
			}
			else
			{
				$this->headers['content-transfer-encoding'] = '7bit';
			}
		}

		function boudry_split($boundry)
		{
			// Isolate boundry sections
			$this->body_sc = 0;
			foreach (explode('--'.$boundry, $this->body) as $p)
			{
				if (!trim($p)) continue;
				// Parse inidividual body sections
				$this->body_s[$this->body_sc] = new phpgwapi_mail2array;
				$this->body_s[$this->body_sc++]->parse_input($p);
			}
		}

		function decode_body()
		{
			switch ($this->headers['content-type'])
			{
				case 'text/plain':
					$this->decode_message_body();
					break;

				case 'text/html':
					$this->decode_message_body();
					$this->body = $this->body;
					break;

				case 'multipart/parallel': // Apparently same as multipart/mixed but order of body parts does not matter
						case 'multipart/report': // RFC1892 ( 1st part is human readable, identical to multipart/mixed )
						case 'multipart/signed': // PGP or OpenPGP (appear same) ( 1st part is human readable )
				case 'multipart/alternative': // various alternate formats of message most common html or text
				case 'multipart/related': // ignore those, contains urls/links to 'stuff' on the net
				case 'multipart/mixed':
				case 'message/rfc822': // *scary*

					if (!isset($this->headers['__other_hdr__']['content-type']['boundary']))
					{
						$this->body = '';
						return;
					}
					$this->boudry_split($this->headers['__other_hdr__']['content-type']['boundary']);
					// In some cases in multi-part messages there will only be 1 body,
					// in those situations we assing that body and info to the primary message
					// and hide the fact this was multi-part message
					if ($this->body_sc == 1)
					{
						$this->body = $this->body_s[0]->body;
						$this->headers['__other_hdr__'] = $this->body_s[0]->headers['__other_hdr__'];
					}
					else if ($this->body_sc > 1)
					{
						// We got many bodies to pick from, Yey!. Lets find something we can use,
						// preference given to 'text/plain' or if not found go for 'text/html'
						$final_id = $html_id = array();

						for ($i = 0; $i < $this->body_sc; $i++)
						{
							switch ($this->body_s[$i]->headers['content-type'])
							{
								case 'text/html':
									$html_id[] = $i;
									break;

								case 'text/plain':
									$final_id[] = $i;
									break;
							}

							// look if message has any attached files
							if ($this->body_s[$i]->headers['content-disposition'] == 'attachment'
								|| $this->body_s[$i]->headers['content-disposition'] == 'inline'
								|| isset($this->body_s[$i]->headers['content-id']))
							{
								// Determine the file name
								if (isset($this->body_s[$i]->headers['__other_hdr__']['content-disposition']['filename']))
								{
									$file_name = $this->body_s[$i]->headers['__other_hdr__']['content-disposition']['filename'];
								}
								else if (isset($this->body_s[$i]->headers['__other_hdr__']['content-type']['name']))
								{
									$file_name = $this->body_s[$i]->headers['__other_hdr__']['content-type']['name'];
								}
								else// No name for file, skipping
								{ 
									continue;
								}

								$this->attachments[$file_name] = $this->body_s[$i]->body;
								if (isset($this->body_s[$i]->headers['content-id']) && $this->body_s[$i]->headers['content-disposition'] == 'inline')
								{
									$this->inline_files[$file_name] = trim($this->body_s[$i]->headers['content-id'], ' <>');
								}
							}
						}
						if ( !$final_id && $html_id)
						{
							$final_id = $html_id;
						}
						if ($final_id)
						{
							$this->body = '';
							foreach ($final_id as $fid)
							{
								$this->body .= $this->body_s[$fid]->body;
								foreach ($this->body_s[$fid]->attachments as $k => $v)
								{
									$this->attachments[$k] = $v;
								}
								foreach ($this->body_s[$fid]->inline_files as $k => $v)
								{
									$this->inline_files[$k] = $v;
								}
							}
							if (isset($this->body_s[$final_id[0]]->headers['__other_hdr__']))
							{
								$this->headers['__other_hdr__'] = $this->body_s[$final_id[0]]->headers['__other_hdr__'];
							}
							$this->headers['content-type'] = $this->body_s[$final_id[0]]->headers['content-type'];
						}
						else
						{
							$this->body = '';
						}
					}
					else// Bad mail client didn't format message properly. 
					{ 
						$this->body = '';
					}
					break;

				default:
					$this->decode_message_body();
					break;

				// case 'multipart/digest':  will/can contain many messages, ignore for our perpouse
			}
		}

		function decode_message_body()
		{
			$this->body = decode_string($this->body, $this->headers['content-transfer-encoding']);
		}

		function parse_input($data='')
		{
			$this->read_data($data);
			$this->split_hdr_body();
			$this->format_header();
			$this->handle_content_headers();
			$this->decode_body();
		}

		function fetch_useful_headers()
		{
			$this->subject = $this->headers['subject'];

			// Attempt to Get Poster's IP from fields commonly used to store it
			if (isset($this->headers['x-posted-by']))
			{
				$this->ip = parse_ip($this->headers['x-posted-by']);
			}
			else if (isset($this->headers['x-originating-ip']))
			{
				$this->ip = parse_ip($this->headers['x-originating-ip']);
			}
			else if (isset($this->headers['x-senderip']))
			{
				$this->ip = parse_ip($this->headers['x-senderip']);
			}
			else if (isset($this->headers['x-mdremoteip']))
			{
				$this->ip = parse_ip($this->headers['x-mdremoteip']);
			}
			else if (isset($this->headers['received']))
			{
				$this->ip = parse_ip($this->headers['received']);
			}

			// Fetch From email and Possible name
			if (preg_match('!(.*?)<(.*?)>!', $this->headers['from'], $matches))
			{
				$this->from_email = trim($matches[2]);

				if (!empty($matches[1]))
				{
					$matches[1] = trim($matches[1]);
					if ($matches[1][0] == '"' && substr($matches[1], -1) == '"')
					{
						$this->from_name = substr($matches[1], 1, -1);
					}
					else
					{
						$this->from_name = $matches[1];
					}
				}
				else
				{
					$this->from_name = $this->from_email;
				}

				if (preg_match('![^A-Za-z0-9\-_ ]!', $this->from_name))
				{
					$this->from_name = substr($this->from_email, 0, strpos($this->from_email, '@'));
				}
			}
			else
			{
				$this->from_email = trim($this->headers['from']);
				$this->from_name = substr($this->from_email, 0, strpos($this->from_email, '@'));
			}

			if (empty($this->from_email) || empty($this->from_name))
			{
				 trigger_error("no name or email for {$this->headers['from']}\n data: {$this->raw_msg}", E_USER_WARNING);
			}

			if (isset($this->headers['message-id']))
			{
				$this->msg_id = substr(trim($this->headers['message-id']), 1, -1);
			}
			else if (isset($this->headers['x-qmail-scanner-message-id']))
			{
				$this->msg_id = substr(trim($this->headers['x-qmail-scanner-message-id']), 1, -1);
			}
			else
			{
				 trigger_error("No message id\n data: {$this->raw_msg}", E_USER_WARNING);
			}

			// This fetches the id of the message if this is a reply to an existing message
			if (!empty($this->headers['in-reply-to']) && preg_match('!<([^>]+)>$!', trim($this->headers['in-reply-to']), $match))
			{
				$this->reply_to_msg_id = $match[1];
			}
			else if (!empty($this->headers['references']) && preg_match('!<([^>]+)>$!', trim($this->headers['references']), $match))
			{
				$this->reply_to_msg_id = $match[1];
			}

			$this->phpgw_header = '';
			if (isset($this->headers['x-phpgroupware']))
			{
				$this->phpgw_header = $this->headers['x-phpgroupware'];
			}
		}
	}

	/* The following functions are just sitting here for now, until I decide what to do with them - skwashd Jan07 */
	/* Lifted from FUDForum base/include/scripts_common.inc */

	function decode_string($str, $encoding)
	{
		switch ($encoding) {
			case 'quoted-printable':
				// Remove soft line breaks & decode
					return quoted_printable_decode(preg_replace("!=\r?\n!", '', $str));
				break;
			case 'base64':
				return base64_decode($str);
				break;
			default:
				return $str;
				break;
		}
	}

	function decode_header_value($val)
	{
		// check if string needs to be decoded
		if (strpos($val, '?') === false) {
			return trim($val);
		}

		// Decode String
		if (preg_match_all('!(.*?)(=\?([^?]+)\?(Q|B)\?([^?]*)\?=)[[:space:]]*(.*)!i', $val, $m)) {
			$newval = '';

			$c = count($m[4]);
			for ($i = 0; $i < $c; $i++) {
				$ec_type = strtolower($m[4][$i]);

				if ($ec_type == 'q') {
					$newval .= decode_string(str_replace('_', ' ', $m[5][$i]), 'quoted-printable');
				} else if ($ec_type == 'b') {
					$newval .= decode_string($m[5][$i], 'base64');
				}

				if (!empty($m[5][$i])) {
					$newval .= ' '.$m[6][$i];
				}
				if (!empty($m[1][$i])) {
					$newval = $m[1][$i].$newval;
				}
			}
			$val = trim($newval);
		}
		return trim($val);
	}
?>
