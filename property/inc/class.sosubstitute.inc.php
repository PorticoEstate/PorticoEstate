<?php
	/**
	 * phpGroupWare - registration
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package registration
	 * @version $Id: class.sodimb_role_user.inc.php 16610 2017-04-21 14:21:03Z sigurdne $
	 */
	phpgw::import_class('phpgwapi.datetime');

	class property_sosubstitute
	{

		var $total_records = 0;

		function __construct()
		{
			$this->account_id	 = (int)$GLOBALS['phpgw_info']['user']['account_id'];
			$this->db			 = & $GLOBALS['phpgw']->db;
			$this->db2			 = clone($this->db);
			$this->join			 = & $this->db->join;
			$this->left_join	 = & $this->db->left_join;
			$this->like			 = & $this->db->like;
		}

		function read( $data )
		{
			if (!empty($data['user_id']))
			{
				$user_id = (int)$data['user_id'];
			}

			$substitute_user_id = (int)$data['substitute_user_id'];

			$filtermethod = 'WHERE 1=1';

			if ($user_id)
			{
				$filtermethod .= " AND user_id = $user_id";
			}
			if ($substitute_user_id)
			{
				$filtermethod .= " AND substitute_user_id = $substitute_user_id";
			}

			$sql = "SELECT id, start_time, user_id, substitute_user_id FROM fm_ecodimb_role_user_substitute {$filtermethod}"
				. " ORDER BY user_id, start_time";

//_debug_array($sql);
			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			while ($this->db->next_record())
			{
				$values[] = array(
					'id'				 => $this->db->f('id'),
					'user_id'			 => $this->db->f('user_id'),
					'substitute_user_id' => $this->db->f('substitute_user_id'),
					'start_time'		 => $this->db->f('start_time'),
				);
			}

			return $values;
		}

		public function delete( $data = array() )
		{
			if ($data)
			{
				$now = time();
				return $this->db->query("DELETE FROM fm_ecodimb_role_user_substitute WHERE id IN (" . implode(', ', $data) . ')', __LINE__, __FILE__);
			}
		}

		/**
		 * A user can only have one substitute
		 * @param int $user_id
		 * @param int $substitute_user_id
		 * @return boolean true on success
		 */
		public function update_substitute( $user_id, $substitute_user_id = 0, $start_time )
		{
			if (!$substitute_user_id || !$start_time)
			{
				return false;
			}

			$error	 = false;
			$this->db->transaction_begin();
			/*
			 * Check for circle reference
			 */
			$sql	 = 'SELECT id FROM fm_ecodimb_role_user_substitute WHERE substitute_user_id =' . (int)$user_id . ' AND user_id = ' . (int)$substitute_user_id;
			$this->db->query($sql, __LINE__, __FILE__);
			if ($this->db->next_record() || $user_id == $substitute_user_id)
			{
				phpgwapi_cache::message_set(lang('substitute') . ' ' . lang('circle reference'), 'error');
				$error = true;
			}
			else
			{
				/**
				 * Avoid duplicates
				 */
				$sql = 'SELECT id FROM fm_ecodimb_role_user_substitute WHERE user_id =' . (int)$user_id
					. ' AND substitute_user_id = ' . (int)$substitute_user_id
					. ' AND start_time = ' . (int)$start_time;
				$this->db->query($sql, __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$this->db->transaction_abort();
					return false;
				}

				$this->db->query('INSERT INTO fm_ecodimb_role_user_substitute (user_id, substitute_user_id, start_time ) VALUES ('
					. (int)$user_id
					. ',' . (int)$substitute_user_id
					. ',' . (int)$start_time
					. ')', __LINE__, __FILE__);
			}

			if ($this->db->transaction_commit() && !$error)
			{
				return true;
			}
		}

		/**
		 * Get the substitute for a user
		 * @param int $user_id
		 * @return int $substitute_user_id
		 */
		public function get_substitute( $user_id )
		{
			$this->db->query('SELECT substitute_user_id FROM fm_ecodimb_role_user_substitute WHERE user_id = ' . (int)$user_id
				. ' AND start_time < ' . time()
				. ' ORDER BY start_time DESC', __LINE__, __FILE__);
			$this->db->next_record();
			return (int)$this->db->f('substitute_user_id');
		}

		/**
		 * Get the users that the substitute is given responsibility for
		 * @param int $substitute_user_id
		 * @return array $users
		 */
		public function get_users_for_substitute( $substitute_user_id )
		{
			$this->db->query('SELECT DISTINCT user_id FROM (SELECT user_id FROM fm_ecodimb_role_user_substitute'
				. ' WHERE substitute_user_id = ' . (int)$substitute_user_id
				. ' AND start_time < ' . time()
				. ' ORDER BY start_time DESC) as t', __LINE__, __FILE__);

			$users = array();
			while ($this->db->next_record())
			{
				$users[] = $this->db->f('user_id');
			}
			return $users;
		}

		public function get_substitute_list( $user_id )
		{

			$active_user_subsitute_id = $this->get_substitute($user_id);

			$this->db->query('SELECT * FROM fm_ecodimb_role_user_substitute WHERE user_id = ' . (int)$user_id . ' ORDER BY start_time ASC', __LINE__, __FILE__);
			$values = array();

			while ($this->db->next_record())
			{
				$user_subsitute_id	 = $this->db->f('substitute_user_id');
				$values[]			 = array
					(
					'id'				 => $this->db->f('id'),
					'start_time'		 => $this->db->f('start_time'),
					'substitute_user_id' => $user_subsitute_id,
					'active'			 => $active_user_subsitute_id == $user_subsitute_id ? 'X' : ''
				);
			}

			return $values;
		}
	}