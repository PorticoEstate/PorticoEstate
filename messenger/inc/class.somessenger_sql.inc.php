<?php
	/*	 * ************************************************************************\
	 * phpGroupWare - Messenger                                                 *
	 * http://www.phpgroupware.org                                              *
	 * This application written by Joseph Engo <jengo@phpgroupware.org>         *
	 * --------------------------------------------                             *
	 * Funding for this program was provided by http://www.checkwithmom.com     *
	 * --------------------------------------------                             *
	 *  This program is free software; you can redistribute it and/or modify it *
	 *  under the terms of the GNU General Public License as published by the   *
	 *  Free Software Foundation; either version 2 of the License, or (at your  *
	 *  option) any later version.                                              *
	  \************************************************************************* */

	/* $Id$ */

	class somessenger extends somessenger_
	{

		var $db, $connected, $like;

		function __construct()
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$this->like = $this->db->like;
			$this->connected = true;
			parent::__construct();
		}

		function update_message_status( $status, $message_id )
		{
			$this->db->query("update phpgw_messenger_messages set message_status='$status' where message_id='"
				. $message_id . "' and message_owner='" . $this->owner . "'", __LINE__, __FILE__);
		}

		function read_inbox( $params )
		{
			$filtermethod = '';
			if (!empty($params['query']))
			{
				$query = $this->db->db_addslashes($params['query']);
				$filtermethod = " AND (message_subject {$this->like} '%$query%'"
					. " OR message_content {$this->like} '%$query%')";
			}
			if (!empty($params['status']) && in_array($params['status'], array('N', 'O')))
			{
				$status = $this->db->db_addslashes(strtoupper($params['status']));
				$filtermethod .= " AND message_status = '$status'";
			}
			$sortmethod = '';
			if (!empty($params['sort']) && !empty($params['order']))
			{
				$sortmethod = " ORDER BY {$params['order']} {$params['sort']}";
			}

			$this->db->limit_query("SELECT * FROM phpgw_messenger_messages"
				. " WHERE message_owner='{$this->owner}'"
				. "{$filtermethod}{$sortmethod}", $params['start'], __LINE__, __FILE__);

			$messages = array();
			while ($this->db->next_record())
			{
				$messages[] = array
					(
					'id' => $this->db->f('message_id'),
					'from' => $this->db->f('message_from'),
					'status' => $this->db->f('message_status'),
					'date' => $this->db->f('message_date'),
					'subject' => $this->db->f('message_subject', true)
				);
			}
			return $messages;
		}

		function read_message( $message_id )
		{
			$this->db->query("SELECT * FROM phpgw_messenger_messages WHERE message_id='"
				. $message_id . "' and message_owner='" . $this->owner . "'", __LINE__, __FILE__);
			$this->db->next_record();
			$message = array(
				'id' => $this->db->f('message_id'),
				'from' => $this->db->f('message_from'),
				'status' => $this->db->f('message_status'),
				'date' => $this->db->f('message_date'),
				'subject' => $this->db->f('message_subject', true),
				'content' => htmlspecialchars_decode($this->db->f('message_content', true))
			);
			if ($this->db->f('message_status') == 'N')
			{
				$this->update_message_status('O', $message_id);
			}
			return $message;
		}

		function send_message( $message, $global_message = False )
		{
			if ($global_message)
			{
				$this->owner = -1;
			}

			if (!preg_match('/^[0-9]+$/', $message['to']))
			{
				$message['to'] = $GLOBALS['phpgw']->accounts->name2id($message['to']);
			}

			$this->db->query("insert into phpgw_messenger_messages (message_owner, message_from, message_status, "
				. "message_date, message_subject, message_content) values ('"
				. $message['to'] . "','" . $this->owner . "','N','" . time() . "','"
				. addslashes($message['subject']) . "','" . addslashes($message['content'])
				. "')", __LINE__, __FILE__);
		}

		function total_messages( $extra_where_clause = '' )
		{
			$this->db->query("select count(*) as cnt from phpgw_messenger_messages where message_owner='"
				. $this->owner . "' " . $extra_where_clause, __LINE__, __FILE__);
			$this->db->next_record();
			return $this->db->f('cnt');
		}

		function delete_message( $message_id )
		{
			$this->db->query("delete from phpgw_messenger_messages where message_id='$message_id' and "
				. "message_owner='" . $this->owner . "'", __LINE__, __FILE__);
		}

		function transaction_begin()
		{
			$this->db->transaction_begin();
		}

		function transaction_commit()
		{
			$this->db->transaction_commit();
		}
	}