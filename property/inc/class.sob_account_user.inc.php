<?php
	/**
	 * phpGroupWare - registration
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @package registration
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.datetime');

	class property_sob_account_user
	{

		var $total_records = 0;

		function __construct()
		{
			$this->account_id	 = (int)$GLOBALS['phpgw_info']['user']['account_id'];
			$this->db			 = & $GLOBALS['phpgw']->db;
			$this->join			 = & $this->db->join;
			$this->left_join	 = & $this->db->left_join;
		}

		function read( $data )
		{
			$b_account_id = $data['b_account_id'];
			if (!empty($data['user_id']))
			{
				$check_user_id = (int)$data['user_id'];
			}
			else if (!$b_account_id)
			{
				$check_user_id = $this->account_id;
			}

			$filtermethod	 = ' WHERE active = 1';
			$where			 = 'AND';
			if ($check_user_id)
			{
				$filtermethod	 .= "{$where} ( fm_b_account_user.user_id = {$check_user_id} OR fm_b_account_user.user_id IS NULL )";
				$where			 = 'AND';
			}
			if ($b_account_id)
			{
				$filtermethod	 .= "{$where} fm_b_account.id = '{$b_account_id}'";
				$where			 = 'AND';
			}

			$sql = "SELECT fm_b_account.id AS b_account_id, descr, fm_b_account_user.user_id"
				. " FROM fm_b_account"
				. " {$this->left_join} fm_b_account_user ON fm_b_account.id = fm_b_account_user.b_account_id"
				. " {$filtermethod}"
				. " ORDER BY b_account_id ASC ";

//_debug_array($sql);
			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			while ($this->db->next_record())
			{
				$user_id = $this->db->f('user_id');

				$values[] = array
					(
					'b_account_id'	 => $this->db->f('b_account_id'),
					'descr'			 => $this->db->f('descr', true),
					'user_id'		 => $check_user_id,
					'active'		 => !!$user_id,
				);
			}

			return $values;
		}

		public function get_favorite( $user_id )
		{
			$user_id = (int)$user_id;

			$sql = "SELECT fm_b_account.id AS b_account_id, descr, fm_b_account_user.user_id"
				. " FROM fm_b_account"
				. " {$this->join} fm_b_account_user ON fm_b_account.id = fm_b_account_user.b_account_id"
				. " WHERE active = 1 AND fm_b_account_user.user_id = {$user_id}"
				. " ORDER BY b_account_id ASC ";

			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			while ($this->db->next_record())
			{
				$user_id = $this->db->f('user_id');

				$values[] = array
					(
					'id'	 => $this->db->f('b_account_id'),
					'name'	 => $this->db->f('descr', true),
				);
			}

			return $values;
		}

		public function edit( $data )
		{
			$delete	 = isset($data['delete']) && is_array($data['delete']) ? $data['delete'] : array();
			$add	 = isset($data['add']) && is_array($data['add']) ? $data['add'] : array();

			$this->db->transaction_begin();

			foreach ($add as $info)
			{
				$user_arr	 = explode('_', $info);
				$value_set	 = array
					(
					'b_account_id'	 => $user_arr[0],
					'user_id'		 => $user_arr[1],
					'modified_on'	 => time(),
					'modified_by'	 => $this->account_id
				);

				$sql = 'INSERT INTO fm_b_account_user (' . implode(',', array_keys($value_set)) . ') VALUES (' . $this->db->validate_insert(array_values($value_set)) . ')';
				$this->db->query($sql, __LINE__, __FILE__);
			}

			unset($info);

			foreach ($delete as $info)
			{
				$user_arr		 = explode('_', $info);
				$b_account_id	 = $user_arr[0];
				$user_id		 = (int)$user_arr[1];

				$sql = "DELETE FROM fm_b_account_user WHERE b_account_id = '{$b_account_id}' AND user_id = {$user_id}";
				$this->db->query($sql, __LINE__, __FILE__);
			}

			$ok = false;
			if ($this->db->transaction_commit())
			{
				$ok = true;

				if ($delete)
				{
					phpgwapi_cache::message_set(lang('%1 roles deleted', count($delete)), 'message');
				}
				if ($add)
				{
					phpgwapi_cache::message_set(lang('%1 roles added', count($add)), 'message');
				}
			}

			return $ok;
		}
	}