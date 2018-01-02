<?php
	/**
	 * phpGroupWare - property: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package helpdesk
	 * @subpackage email_out
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.socommon');

	class helpdesk_soemail_out extends phpgwapi_socommon
	{

		protected static $so;

		public function __construct()
		{
			parent::__construct('phpgw_helpdesk_email_out', helpdesk_email_out::get_fields());
		}

		/**
		 * Implementing classes must return an instance of itself.
		 *
		 * @return the class instance.
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('helpdesk.soemail_out');
			}
			return self::$so;
		}


		protected function populate( array $data )
		{
			$object = new helpdesk_email_out();
			foreach ($this->fields as $field => $field_info)
			{
				$object->set_field($field, $data[$field]);
			}

			return $object;
		}

		protected function update( $object )
		{
			$this->db->transaction_begin();
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$lang_active = lang('active');
			$lang_inactive = lang('inactive');

			$original = $this->read_single($object->get_id());//returned as array()
			foreach ($this->fields as $field => $params)
			{
				$new_value = $object->$field;
				$old_value = $original[$field];
				if (!empty($params['history']) && ($new_value != $old_value))
				{
					$label = !empty($params['label']) ? lang($params['label']) : $field;
					$value_set = array
					(
						'email_out_id'	=> $object->get_id(),
						'time'		=> time(),
						'author'	=> $GLOBALS['phpgw_info']['user']['fullname'],
						'comment'	=> $label . ':: ' . lang('old value') . ': ' . $this->db->db_addslashes($old_value) . ', ' .lang('new value') . ': ' . $this->db->db_addslashes($new_value),
						'type'	=> 'history',
					);

					$this->db->query( 'INSERT INTO helpdesk_email_out_comment (' .  implode( ',', array_keys( $value_set ) )   . ') VALUES ('
					. $this->db->validate_insert( array_values( $value_set ) ) . ')',__LINE__,__FILE__);
				}

			}

			parent::update($object);

			return	$this->db->transaction_commit();
		}

		public function get_recipient_candidates( $recipient_set_id , $email_out_id)
		{
			$recipient_set_id = (int) $recipient_set_id;
			$email_out_id = (int) $email_out_id;

			$recipients = array(-1);
			$sql = "SELECT recipient_id FROM phpgw_helpdesk_email_out_recipient WHERE email_out_id = {$email_out_id}";
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$recipients[] = $this->db->f('recipient_id');
			}

			$values = array();

			$sql = "SELECT phpgw_helpdesk_email_out_recipient_list.id as id,"
				. " email, phpgw_helpdesk_email_out_recipient_list.name as name"
				. " FROM phpgw_helpdesk_email_out_recipient_set"
				. " {$this->join} phpgw_helpdesk_email_out_recipient_list ON phpgw_helpdesk_email_out_recipient_list.set_id = phpgw_helpdesk_email_out_recipient_set.id"
				. " WHERE phpgw_helpdesk_email_out_recipient_set.id = {$recipient_set_id}"
				. " AND phpgw_helpdesk_email_out_recipient_list.id NOT IN (" . implode(',', $recipients) . ")";

			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$values[] = array(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('name', true),
					'email'	=> $this->db->f('email',true),
				);
			}
			return $values;
		}

		public function get_recipients( $email_out_id )
		{

			$status_list = helpdesk_email_out::get_status_list();

			$email_out_id = (int) $email_out_id;
			$values = array();

			$sql = "SELECT email, phpgw_helpdesk_email_out_recipient_list.name as name,"
				. " phpgw_helpdesk_email_out_recipient_list.id, status"
				. " FROM phpgw_helpdesk_email_out"
				. " {$this->join} phpgw_helpdesk_email_out_recipient ON phpgw_helpdesk_email_out.id = phpgw_helpdesk_email_out_recipient.email_out_id"
				. " {$this->join} phpgw_helpdesk_email_out_recipient_list ON phpgw_helpdesk_email_out_recipient_list.id = phpgw_helpdesk_email_out_recipient.recipient_id"
				. " WHERE phpgw_helpdesk_email_out.id = {$email_out_id}";

			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$values[] = array(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('name',true),
					'email'	=> $this->db->f('email',true),
					'status'	=> $status_list[$this->db->f('status')]
				);
			}
			return $values;
		}

		function set_candidates($id, $ids)
		{
			$recipients = $this->get_recipients($id);

			$check_duplicates = array();
			foreach ($recipients as $entry)
			{
				$check_duplicates[] = $entry['id'];
			}


			$sql = 'INSERT INTO phpgw_helpdesk_email_out_recipient (email_out_id, recipient_id)'
				. ' VALUES(?, ?)';
			foreach ($ids as $recipient_id)
			{
				if(in_array($recipient_id, $check_duplicates))
				{
					continue;
				}

				$valueset[] = array
					(
					1 => array(
						'value' => (int)$id,
						'type' => PDO::PARAM_INT
					),
					2 => array(
						'value' => $recipient_id,
						'type' => PDO::PARAM_INT
					)
				);
			}
			if($valueset)
			{
				return $GLOBALS['phpgw']->db->insert($sql, $valueset, __LINE__, __FILE__);
			}
		}

		function delete_recipients($id, $ids = array())
		{
			$id = (int) $id;
			if($ids)
			{
				$parties = implode(',', $ids);
				$sql = "DELETE FROM phpgw_helpdesk_email_out_recipient WHERE email_out_id = {$id} AND recipient_id IN ({$parties})"
				. " AND (status IS NULL OR status = 0 )";
				return $this->db->query($sql,__LINE__,__FILE__);
			}
		}

		function set_status($id, $recipient_id, $status)
		{
			$id = (int) $id;
			$recipient_id = (int) $recipient_id;
			$status = (int) $status;
			$sql = "UPDATE phpgw_helpdesk_email_out_recipient SET status = {$status} WHERE email_out_id = {$id} AND recipient_id = {$recipient_id}";
			return $this->db->query($sql,__LINE__,__FILE__);
		}
	}