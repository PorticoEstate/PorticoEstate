<?php
	/*	 * ************************************************************************\
	 * phpGroupWare -Messenger                                                  *
	 * http://www.phpgroupware.org                                              *
	 * This file written by Chris Weiss <cweiss@gmail.com>                      *
	 * --------------------------------------------                             *
	 *  This program is free software; you can redistribute it and/or modify it *
	 *  under the terms of the GNU General Public License as published by the   *
	 *  Free Software Foundation; either version 2 of the License, or (at your  *
	 *  option) any later version.                                              *
	  \************************************************************************* */

	class somessenger extends somessenger_
	{

		var $imap;
		var $stat;
		var $owner;
		var $imap_host;

		/**
		 * @constructor
		 */
		function __construct()
		{
			$this->imap_host = $GLOBALS['phpgw']->config->config_data['imap_message_host'];
			$this->imap = @imap_open("\{{$this->imap_host}:143/imap/notls}INBOX", $GLOBALS['phpgw_info']['user']['account_lid'], $GLOBALS['phpgw_info']['user']['passwd']);
			if (is_resource($this->imap))
			{
				$this->stat = imap_status($this->imap, "\{{$this->imap_host}}INBOX", SA_MESSAGES);
				$this->connected = true;
			}
			parent::__construct();
		}

		/**
		 * Delete a message
		 *
		 * @param int $message_id the message to be deleted
		 */
		function delete_message( $message_id )
		{
			imap_delete($this->imap, $message_id, FT_UID);
			imap_expunge($this->imap);
		}

		function read_inbox( $params )
		{
			$sort = $params['sort'] == 'DESC' ? 1 : 0;
			switch ($params['order'])
			{
				case 'message_date' :
					$idlist = imap_sort($this->imap, SORTARRIVAL, $sort, SE_UID);
					break;
				case 'message_from' :
					$idlist = imap_sort($this->imap, SORTFROM, $sort, SE_UID);
					break;
				case 'message_subject' :
					$idlist = imap_sort($this->imap, SORTSUBJECT, $sort, SE_UID);
					break;
				default :
					$idlist = imap_sort($this->imap, SORTARRIVAL, 1, SE_UID);
			}
			foreach ($idlist as $uid)
			{
				$msg = imap_headerinfo($this->imap, imap_msgno($this->imap, $uid));
				$messages[] = array('id' => $uid, 'from' => $msg->from[0]->mailbox, 'status' => $this->_msg_status($msg),
					'date' => $msg->udate, 'subject' => $msg->Subject);
			}
			return $messages;
		}

		function read_message( $message_id )
		{
			$msg = imap_headerinfo($this->imap, imap_msgno($this->imap, $message_id));
			$message = array('id' => $message_id, 'from' => $msg->from[0]->mailbox, 'status' => $this->_msg_status($msg),
				'date' => $msg->udate, 'subject' => $msg->Subject, 'content' => $this->_get_text_part($message_id));
			return $message;
		}

		function send_message( $message, $global_message = false )
		{
			$contacts = CreateObject('phpgwapi.contacts');
			$mailer = CreateObject('phpgwapi.mailer_smtp');
			if ($global_message)
			{
				$this->owner = -1;
			}
			//save then send
			$mailer->From = $contacts->get_email($GLOBALS['phpgw_info']['user']['person_id']);
			$mailer->FromName = $GLOBALS['phpgw']->common->display_fullname();
			$mailer->AddAddress($contacts->get_email($contacts->is_contact($message['to'])), $GLOBALS['phpgw']->accounts->id2name($message['to']));
			$mailer->Subject = $message['subject'];
			$mailer->Body = $message['content'];
			$mailer->Send();
			//imap_append($this->imap, '{localhost}Sent', $mailer->getHeader()."\r\n".$mailer->getBody());
		}

		function total_messages( $extra_where_clause = '' )
		{
			return $this->stat->messages;
		}

		function update_message_status( $status, $message_id )
		{
			$flags = "\\Seen";
			switch ($status)
			{
				case 'R' :
					$flags += " \\Answered";
					break;
			}
			imap_setflag_full($this->imap, $message_id, $flags, ST_UID);
		}

		/**
		 * Get the primary text body of a message
		 *
		 * @internal inspired by comment by cleong at organic dot com at http://php.net/imap_fetchbody
		 * @param int $message_id the message number sought
		 * @param object $structure php imap message structure to be passed
		 * @param int $partno the message part number
		 * @return string the message body
		 */
		function _get_text_part( $message_id, $structure = null, $partno = 0 )
		{
			if (!is_object($structure))
			{
				$structure = imap_fetchstructure($this->imap, $message_id, FT_UID);
			}
			if (!is_object($structure))
			{
				return lang('error: invalid message structure.  message contents can not be displayed');
			}

			if ($structure->type == 0 /* text */ && strtolower($structure->subtype) == 'plain')
			{
				if (!$partno)
				{
					$part = "1";
				}

				$text = imap_fetchbody($this->imap, $message_id, $partno);

				if ($structure->encoding == 3)
				{
					$text = imap_base64($text);
				}
				elseif ($structure->encoding == 4)
				{
					$text = imap_qprint($text);
				}

				if (isset($structure->parameters) && count($structure->parameters))
				{
					foreach ($structure->parameters as $key => $vals)
					{
						if ($vals['attribute'] == 'charset' && !( $vals['value'] == 'ascii' || $vals['value'] == 'utf-8' ))
						{
							$test = iconv($vals['value'], 'utf-8', $text);
						}
					}
				}
				return $text;
			}

			if ($structure->type == 1) /* multipart */
			{
				$body = '';
				if ($partno)
				{
					$prefix = "{$partno}.";
				}
				while (list ($index, $sub_structure) = each($structure->parts))
				{
					$body = $this->_get_text_part($message_id, $sub_structure, $prefix . ($index + 1));
					if (strlen($body))
					{
						return $body;
					}
				}
			}
			return lang('error: invalid message structure.  message contents can not be displayed');
		}

		/**
		 * Get the status of a message
		 *
		 * @param object $msg php imap message object to be analysed
		 * @return char the message status code
		 */
		function _msg_status( $msg )
		{
			/*
			 * N - New
			 * R - Replied
			 * O - Old (read)
			 */
			if ($msg->Unseen == 'U')
			{
				return 'N';
			}
			elseif ($msg->Answered == 'A')
			{
				return 'R';
			}
			else
			{
				return 'O';
			}
		}
	}