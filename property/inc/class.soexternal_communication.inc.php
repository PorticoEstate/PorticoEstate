<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2018 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @subpackage helpdesk
	 * @version $Id$
	 */

	/**
	 * Description
	 * @package property
	 */
	class property_soexternal_communication
	{

		var $fields_updated	 = array();
		var $historylog;
		private $db, $like, $join, $left_join, $account, $currentapp;
		protected $global_lock	 = false;

		public function __construct( $currentapp = 'property' )
		{
			$this->currentapp = $currentapp ? $currentapp : $GLOBALS['phpgw_info']['flags']['currentapp'];

			$this->db			 = & $GLOBALS['phpgw']->db;
			$this->like			 = & $this->db->like;
			$this->join			 = & $this->db->join;
			$this->left_join	 = & $this->db->left_join;
			$this->historylog	 = CreateObject('phpgwapi.historylog', $this->currentapp, 'external_communication');
			$this->account		 = (int)$GLOBALS['phpgw_info']['user']['account_id'];
		}

		function read( $params )
		{
			$start	 = isset($params['start']) && $params['start'] ? (int)$params['start'] : 0;
			$results = isset($params['results']) && $params['results'] ? (int)$params['results'] : null;
			$sort	 = isset($params['sort']) && $params['sort'] ? $params['sort'] : null;
			$dir	 = isset($params['dir']) && $params['dir'] ? $params['dir'] : 'asc';
			$query	 = isset($params['query']) && $params['query'] ? $this->db->db_addslashes($params['query']) : null;
			$filters = isset($params['filters']) && $params['filters'] ? $params['filters'] : array();

			$fields = $this->get_fields();

			$or_conditions	 = array();
			$and_conditions	 = array();
			$joins			 = '';
			$cols			 = '';

			switch ($this->currentapp)
			{
				case 'property':
					$table	 = 'fm_tts_external_communication';
					$joins	 = " {$this->join} fm_tts_tickets ON {$table}.ticket_id = fm_tts_tickets.id";
					//				$joins .= " {$this->left_join} fm_tts_external_communication_msg ON fm_tts_external_communication_msg.excom_id = fm_tts_external_communication.id";
					//				$cols = ",fm_tts_external_communication_msg.message";
					if ($query)
					{
						$or_conditions[] = " location_code {$this->like} '{$query}%'";
						$or_conditions[] = " fm_tts_tickets.subject {$this->like} '%{$query}%'";
						$or_conditions[] = " {$table}.mail_recipients {$this->like} '%{$query}%'";
						$or_conditions[] = " {$table}.id =" . (int)$query;
					}
					foreach ($filters as $key => $val)
					{
						if ($fields[$key]['type'] = 'int')
						{
							$and_conditions[] = " $key = " . (int)$val;
						}
						else
						{
							$and_conditions[] = " $key = '" . $this->db->db_addslashes($val) . "'";
						}
					}


					break;
				case 'helpdesk':
					$table = 'phpgw_helpdesk_external_communication';
					break;
				default:
					throw new Exception("External communication for {$this->currentapp} is not supported");
			}

			if ($sort)
			{
				if (is_array($sort))
				{
					$order = "ORDER BY {$sort[0]} {$dir}, {$sort[1]}";
				}
				else
				{
					$order = "ORDER BY {$table}.{$sort} {$dir}";
				}
			}
			else
			{
				$order = "ORDER BY fm_tts_external_communication.id DESC";
			}

			$filtermethod = 'WHERE 1=1';

			if ($or_conditions)
			{
				$filtermethod .= ' AND (' . implode(' OR ', $or_conditions) . ')';
			}
			if ($and_conditions)
			{
				$filtermethod .= ' AND (' . implode(' AND ', $and_conditions) . ')';
			}


			$this->db->query("SELECT count(1) AS count FROM {$table} {$joins} {$filtermethod}", __LINE__, __FILE__);
			$this->db->next_record();
			$total_records = (int)$this->db->f('count');

			$sql = "SELECT {$table}.* {$cols} FROM {$table} {$joins} {$filtermethod} {$order}";
			if ($results > -1)
			{
				$this->db->limit_query($sql, $start, __LINE__, __FILE__, $results);
			}
			else
			{
				$this->db->query($sql, __LINE__, __FILE__);
			}


			$values = array();

			while ($this->db->next_record())
			{
				foreach ($fields as $field => $field_info)
				{
					$row[$field] = $this->db->f($field, true);
				}

				$mail_recipients		 = trim($row['mail_recipients'], ',');
				$row['mail_recipients']	 = $mail_recipients ? explode(',', $mail_recipients) : array();
				$file_attachments		 = trim($row['file_attachments'], ',');
				$row['file_attachments'] = $file_attachments ? explode(',', $file_attachments) : array();
				$row['created_on']		 = $this->db->f('created_on');
				$row['created_by']		 = $this->db->f('created_by');
				$row['modified_date']	 = $this->db->f('modified_date');
				$values[]				 = $row;
			}
			return array(
				'total_records'	 => $total_records,
				'results'		 => $values,
				'start'			 => $start,
				'sort'			 => is_array($sort) ? $sort[0] : $sort,
				'dir'			 => $dir
			);
		}

		function add( $values )
		{
			$sender	 = !empty($values['sender']) ? $values['sender'] : '';
			$fields	 = $this->get_fields();

			$value_set = array();

			foreach ($fields as $field => $field_info)
			{
				if (($field_info['action'] & PHPGW_ACL_ADD))
				{
					$value				 = $values[$field];
					$value_set[$field]	 = $value;

					if ($field_info['required'] && (($value !== '0' && empty($value)) || empty($value)))
					{
						throw new Exception(lang("Field %1 is required", lang($field_info['label'])));
					}
				}
			}

			$value_set['mail_recipients'] = $this->organize_mail_recipients($values);

			$value_set['created_on']	 = time();
			$value_set['modified_date']	 = time();
			$value_set['created_by']	 = $this->account;

			$new_message = $value_set['message'];

			/*
			 * Stored elsewhere
			 */
			unset($value_set['message']);

			$cols			 = implode(',', array_keys($value_set));
			$values_insert	 = $this->db->validate_insert(array_values($value_set));

			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			switch ($this->currentapp)
			{
				case 'property':
					$table	 = 'fm_tts_external_communication';
					break;
				case 'helpdesk':
					$table	 = 'phpgw_helpdesk_external_communication';
					break;
				default:
					throw new Exception("External communication for {$this->currentapp} is not supported");
			}

			$this->db->query("INSERT INTO {$table} ({$cols}) VALUES ({$values_insert})", __LINE__, __FILE__);
			$id = $this->db->get_last_insert_id($table, 'id');

			$this->add_msg($id, $new_message, $sender);

			if (!$this->global_lock)
			{
				$this->db->transaction_commit();
			}

			$receipt['id'] = $id;

			return $receipt;
		}

		function edit( $values )
		{
			$receipt = array();
			$id		 = (int)$values['id'];

			switch ($this->currentapp)
			{
				case 'property':
					$table	 = 'fm_tts_external_communication';
					break;
				case 'helpdesk':
					$table	 = 'phpgw_helpdesk_external_communication';
					break;
				default:
					throw new Exception("External communication for {$this->currentapp} is not supported");
			}

			$this->db->query("SELECT * FROM {$table} WHERE id={$id}", __LINE__, __FILE__);
			$this->db->next_record();
			$old_subject	 = $this->db->f('subject');
			$_old_message	 = $this->db->f('message');

			$history_values = $this->get_messages($id, 'DESC');

			$old_message = $history_values[0]['message'];

			if (!$old_message)
			{
				$old_message = $_old_message;
			}

			$fields = $this->get_fields();

			$value_set = array();

			foreach ($fields as $field => $field_info)
			{
				if (($field_info['action'] & PHPGW_ACL_EDIT))
				{
					$value				 = $values[$field];
					$value_set[$field]	 = $value;

					if ($field_info['required'] && (($value !== '0' && empty($value)) || empty($value)))
					{
						throw new Exception(lang("Field %1 is required", lang($field_info['label'])));
					}
				}
			}

			$value_set['mail_recipients'] = $this->organize_mail_recipients($values);

			if (isset($values['file_attachments']) && is_array($values['file_attachments']))
			{
				$file_attachments = array();
				foreach ($values['file_attachments'] as $_temp)
				{
					if ($_temp)
					{
						$file_attachments[] = (int)$_temp;
					}
				}
				$value_set['file_attachments'] = implode(',', $file_attachments);
			}

			$this->db->transaction_begin();

			/**
			 * O - Ticket opened
			 * C - Comment appended
			 * S - Subject change
			 * M - Mail sent
			 */
			$value_set['modified_date'] = time();

			$value_set_update = $this->db->validate_update($value_set);
			$this->db->query("UPDATE {$table} SET {$value_set_update} WHERE id={$id}", __LINE__, __FILE__);

			$new_subject = $this->db->db_addslashes($values['subject']);
			if ($old_subject != $new_subject)
			{
				$this->db->query("UPDATE {$table} SET subject='{$new_subject}' WHERE id={$id}", __LINE__, __FILE__);
				$this->historylog->add('S', $id, $new_subject, $old_subject);
				phpgwapi_cache::message_set(lang('Subject has been updated'), 'message');
			}

			$new_message = $values['message'];
			if (($old_message != $new_message) && $new_message)
			{
				$this->fields_updated[] = 'message';
				$this->add_msg($id, $new_message);
			}

			$this->db->transaction_commit();

			$receipt['id'] = $id;

			return $receipt;
		}

		function get_messages( $id, $sort = 'ASC' )
		{
			switch ($this->currentapp)
			{
				case 'property':
					$table	 = 'fm_tts_external_communication_msg';
					break;
				case 'helpdesk':
					$table	 = 'phpgw_helpdesk_external_communication_msg';
					break;
				default:
					throw new Exception("External communication for {$this->currentapp} is not supported");
			}

			$this->db->query("SELECT * FROM {$table} WHERE excom_id = " . (int)$id . " ORDER BY id {$sort}", __LINE__, __FILE__);
			$values = array();
			while ($this->db->next_record())
			{
				$mail_recipients	 = trim($this->db->f('mail_recipients'), ',');
				$mail_recipients	 = $mail_recipients ? explode(',', $mail_recipients) : array();
				$file_attachments	 = trim($this->db->f('file_attachments'), ',');
				$file_attachments	 = $file_attachments ? explode(',', $file_attachments) : array();

				$values[] = array
					(
					'id'					 => $this->db->f('id'),
					'message'				 => $this->db->f('message', true),
					'created_on'			 => $this->db->f('created_on'),
					'created_by'			 => $this->db->f('created_by'),
					'timestamp_sent'		 => $this->db->f('id'),
					'mail_recipients'		 => $mail_recipients,
					'file_attachments'		 => $file_attachments,
					'sender_email_address'	 => $this->db->f('sender_email_address', true),
				);
			}

			return $values;
		}

		function add_msg( $id, $message, $sender = '' )
		{
			$value_set = array
				(
				'excom_id'				 => (int)$id,
				'message'				 => $message,
				'created_on'			 => time(),
				'created_by'			 => $this->account,
				'sender_email_address'	 => $sender
			);

			$cols	 = implode(',', array_keys($value_set));
			$values	 = $this->db->validate_insert(array_values($value_set));

			switch ($this->currentapp)
			{
				case 'property':
					$table	 = 'fm_tts_external_communication_msg';
					break;
				case 'helpdesk':
					$table	 = 'phpgw_helpdesk_external_communication_msg';
					break;
				default:
					throw new Exception("External communication for {$this->currentapp} is not supported");
			}

			$this->db->query("INSERT INTO {$table} ({$cols}) VALUES ({$values})", __LINE__, __FILE__);
			return $this->db->get_last_insert_id($table, 'id');
		}

		function update_msg( $excom_id, $mail_recipients, $file_attachments = '' )
		{
			switch ($this->currentapp)
			{
				case 'property':
					$table	 = 'fm_tts_external_communication_msg';
					break;
				case 'helpdesk':
					$table	 = 'phpgw_helpdesk_external_communication_msg';
					break;
				default:
					throw new Exception("External communication for {$this->currentapp} is not supported");
			}

			$this->db->query("SELECT max(id) as id FROM {$table} WHERE excom_id = " . (int)$excom_id, __LINE__, __FILE__);
			$this->db->next_record();
			$id = (int)$this->db->f('id');

			$value_set = array
				(
				'timestamp_sent'	 => time(),
				'mail_recipients'	 => $mail_recipients,
				'file_attachments'	 => $file_attachments,
			);

			$value_set_update = $this->db->validate_update($value_set);
			return $this->db->query("UPDATE {$table} SET {$value_set_update} WHERE id={$id}", __LINE__, __FILE__);
		}

		function organize_mail_recipients( $values )
		{
			$value_string	 = '';
			$mail_recipients = array();
			if (isset($values['mail_recipients']) && is_array($values['mail_recipients']))
			{

				foreach ($values['mail_recipients'] as $_temp)
				{
					if ($_temp)
					{
						$_temp = str_replace(array(' ', '&amp;#59;', '&#59;', ';'), array('', ',',
							',', ','), $_temp);
						if (preg_match('/,/', $_temp))
						{
							$mail_recipients = array_merge($mail_recipients, explode(',', $_temp));
						}
						else
						{
							$mail_recipients[] = $_temp;
						}
					}
				}
				unset($_temp);

				$vendor_email	 = array();
				$validator		 = CreateObject('phpgwapi.EmailAddressValidator');
				foreach ($mail_recipients as $_temp)
				{
					if ($_temp)
					{
						if ($validator->check_email_address($_temp))
						{
							$vendor_email[] = $_temp;
						}
						else
						{
							phpgwapi_cache::message_set(lang('%1 is not a valid address', $_temp), 'error');
						}
					}
				}
				$value_string = implode(',', $vendor_email);
				unset($_temp);
			}
			return $value_string;
		}

		function get_at_ticket( $ticket_id )
		{

			switch ($this->currentapp)
			{
				case 'property':
					$table	 = 'fm_tts_external_communication';
					break;
				case 'helpdesk':
					$table	 = 'phpgw_helpdesk_external_communication';
					break;
				default:
					throw new Exception("External communication for {$this->currentapp} is not supported");
			}

			$sql	 = "SELECT * FROM {$table} WHERE ticket_id = " . (int)$ticket_id;
			$this->db->query($sql, __LINE__, __FILE__);
			$values	 = array();
			$fields	 = $this->get_fields();

			while ($this->db->next_record())
			{
				foreach ($fields as $field => $field_info)
				{
					$row[$field] = $this->db->f($field, true);
				}

				$mail_recipients		 = trim($row['mail_recipients'], ',');
				$row['mail_recipients']	 = $mail_recipients ? explode(',', $mail_recipients) : array();
				$file_attachments		 = trim($row['file_attachments'], ',');
				$row['file_attachments'] = $file_attachments ? explode(',', $file_attachments) : array();
				$row['created_on']		 = $this->db->f('created_on');
				$row['created_by']		 = $this->db->f('created_by');
				$row['modified_date']	 = $this->db->f('modified_date');
				$values[]				 = $row;
			}
			return $values;
		}

		function read_single( $id )
		{
			switch ($this->currentapp)
			{
				case 'property':
					$table	 = 'fm_tts_external_communication';
					break;
				case 'helpdesk':
					$table	 = 'phpgw_helpdesk_external_communication';
					break;
				default:
					throw new Exception("External communication for {$this->currentapp} is not supported");
			}

			$sql	 = "SELECT * FROM {$table} WHERE id = " . (int)$id;
			$this->db->query($sql, __LINE__, __FILE__);
			$values	 = array();
			$fields	 = $this->get_fields();

			$this->db->next_record();

			foreach ($fields as $field => $field_info)
			{
				$stripslashes	 = !in_array($field_info['type'], array('int'));
				$values[$field]	 = $this->db->f($field, $stripslashes);
			}
			$mail_recipients			 = trim($values['mail_recipients'], ',');
			$values['mail_recipients']	 = $mail_recipients ? explode(',', $mail_recipients) : array();
			$file_attachments			 = trim($values['file_attachments'], ',');
			$values['file_attachments']	 = $file_attachments ? explode(',', $file_attachments) : array();
			$values['created_on']		 = $this->db->f('created_on');
			$values['created_by']		 = $this->db->f('created_by');

			return $values;
		}

		function get_fields()
		{
			$fields = array(
				'id'				 => array('action'	 => PHPGW_ACL_READ,
					'type'		 => 'int',
					'label'		 => 'id',
					'sortable'	 => true,
					'hidden'	 => false,
					'public'	 => true,
					'required'	 => false,
				),
				'type_id'			 => array('action'	 => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'int',
					'label'		 => 'type_id',
					'sortable'	 => true,
					'hidden'	 => true,
					'public'	 => false,
					'required'	 => true,
				),
				'ticket_id'			 => array('action'	 => PHPGW_ACL_ADD,
					'type'		 => 'int',
					'label'		 => 'ticket_id',
					'sortable'	 => false,
					'query'		 => false,
					'public'	 => true,
					'required'	 => true,
				),
				'subject'			 => array('action'	 => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'string',
					'label'		 => 'subject',
					'sortable'	 => false,
					'query'		 => true,
					'public'	 => true,
					'required'	 => true,
				),
				'message'			 => array('action'	 => PHPGW_ACL_ADD,
					'type'		 => 'html',
					'label'		 => 'descr',
					'sortable'	 => false,
					'query'		 => true,
					'public'	 => true,
					'required'	 => false,
				),
				'vendor_id'			 => array('action'	 => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'int',
					'label'		 => 'vendor',
					'sortable'	 => false,
					'query'		 => false,
					'public'	 => true,
					'required'	 => false,
				),
				'mail_recipients'	 => array('action'	 => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'string',
					'label'		 => 'mail recipients',
					'sortable'	 => false,
					'query'		 => true,
					'public'	 => false,
					'required'	 => false,
				),
				'file_attachments'	 => array('action'	 => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'int',
					'label'		 => 'file attachments',
					'sortable'	 => false,
					'query'		 => false,
					'public'	 => true,
					'required'	 => false,
				),
				'cat_id'			 => array('action'	 => null,
					'type'		 => 'int',
					'label'		 => 'category',
					'sortable'	 => false,
					'query'		 => false,
					'public'	 => true,
					'required'	 => false,
				),
				'created_on'	 => array('action'	 => PHPGW_ACL_READ,
					'type'		 => 'date',
					'label'		 => 'date',
					'sortable'	 => true,
					'query'		 => false,
					'public'	 => true,
					'required'	 => false,
				)
			);

			return $fields;
		}

		public function get_sms_recipients( $location_code )
		{
			$sms_recipients = array();

			$filtermethod = '';

			if($location_code === '__get_all__')
			{
				$filtermethod = " WHERE contact_phone IS NOT NULL";
			}
			else
			{
				$filtermethod = " WHERE location_code {$this->like} '$location_code%'"
					. " AND contact_phone IS NOT NULL";

			}


			if ($location_code)
			{
				$sql = "SELECT location_code, contact_phone, concat(last_name || ', ' || first_name) AS name, etasje as floor,"
					. " concat(fm_streetaddress.descr || ' ' || fm_location4.street_number) AS address"
					. " FROM fm_location4"
					. " JOIN fm_tenant ON fm_location4.tenant_id = fm_tenant.id"
					. " JOIN fm_streetaddress ON fm_location4.street_id = fm_streetaddress.id"
					. " {$filtermethod}"
					. " ORDER BY name";

				$this->db->query($sql, __LINE__, __FILE__);

				while ($this->db->next_record())
				{
					$sms_recipients[] = array(
						'location_code'	 => $this->db->f('location_code', true),
						'name'			 => $this->db->f('name', true),
						'contact_phone'	 => $this->db->f('contact_phone', true),
						'floor'			 => $this->db->f('floor', true),
						'address'		 => $this->db->f('address', true),
					);
				}
			}

			return $sms_recipients;
		}
	}