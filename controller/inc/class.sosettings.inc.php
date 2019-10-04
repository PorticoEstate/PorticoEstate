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
	class controller_sosettings
	{

		private $db, $like, $join, $left_join, $account;

		public function __construct()
		{
			$this->db		 = & $GLOBALS['phpgw']->db;
			$this->like		 = & $this->db->like;
			$this->join		 = & $this->db->join;
			$this->left_join = & $this->db->left_join;
			$this->account	 = (int)$GLOBALS['phpgw_info']['user']['account_id'];
		}

		public function save( $data )
		{
			$this->db->transaction_begin();

			$this->db->query('UPDATE controller_control SET ticket_cat_id = NULL', __LINE__, __FILE__);

			$sql = "UPDATE controller_control SET ticket_cat_id =? WHERE id = ?";

			$valueset = array();

			foreach ($data as $control_id => $cat_id)
			{
				if ($cat_id)
				{
					$valueset[] = array
						(
						1	 => array
							(
							'value'	 => (int)$cat_id,
							'type'	 => PDO::PARAM_INT
						),
						2	 => array
							(
							'value'	 => $control_id,
							'type'	 => PDO::PARAM_INT
						)
					);
				}
			}

			if ($valueset)
			{
				$GLOBALS['phpgw']->db->insert($sql, $valueset, __LINE__, __FILE__);
			}

			return $this->db->transaction_commit();
		}

		public function read()
		{
			$this->db->query('SELECT id, ticket_cat_id FROM controller_control', __LINE__, __FILE__);

			$values = array();
			while ($this->db->next_record())
			{
				$control_id = $this->db->f('id');

				$values[$control_id] = array(
					'cat_id' => $this->db->f('ticket_cat_id')
				);
			}
			return $values;
		}

		public function read_single( $control_id )
		{
			$this->db->query('SELECT ticket_cat_id FROM controller_control WHERE id = ' . (int)$cat_id, __LINE__, __FILE__);
			$this->db->next_record();
			return (int)$this->db->f('ticket_cat_id');
		}


		public function get_roles($control_id)
		{
			if(!$control_id)
			{
				return array();
			}

			$sql = "SELECT * FROM controller_control_user_role WHERE control_id = ?";
			$condition =  array((int)$control_id);

			$this->db->select($sql, $condition, __LINE__, __FILE__);

			$values = array();

			while ($this->db->next_record())
			{
				$user_id = $this->db->f('user_id');
				$values[$user_id] = $this->db->f('roles');
			}

			return $values;
		}

		public function save_users( $data )
		{
//			_debug_array($data);

			$add = array();
			$update = array();
			$delete = array();
			$now = time();

			foreach ($data as $control_id => $user_info)
			{
		//		_debug_array($control_id);
				foreach ($user_info as $user_id => $role_info)
				{
					$new_roles = (int)array_sum($role_info['new']);
					$old_roles = (int)$role_info['original'];

					if($old_roles != $new_roles)
					{

						if($new_roles && $old_roles)
						{
							$update[] = array
							(
								1	=> array
								(
									'value'	=> $new_roles,
									'type'	=> PDO::PARAM_INT
								),
								2	=> array
								(
									'value'	=> $now,
									'type'	=> PDO::PARAM_INT
								),
								3	=> array
								(
									'value'	=> $this->account,
									'type'	=> PDO::PARAM_INT
								),
								4	=> array
								(
									'value'	=> $control_id,
									'type'	=> PDO::PARAM_INT
								),
								5	=> array
								(
									'value'	=> $user_id,
									'type'	=> PDO::PARAM_INT
								)
							);
						}
						else if($new_roles && !$old_roles)
						{
							$add[] = array
							(
								1	=> array
								(
									'value'	=> $control_id,
									'type'	=> PDO::PARAM_INT
								),
								2	=> array
								(
									'value'	=> $user_id,
									'type'	=> PDO::PARAM_INT
								),
								3	=> array
								(
									'value'	=> $new_roles,
									'type'	=> PDO::PARAM_INT
								),
								4	=> array
								(
									'value'	=> $now,
									'type'	=> PDO::PARAM_INT
								),
								5	=> array
								(
									'value'	=> $this->account,
									'type'	=> PDO::PARAM_INT
								)
							);

						}
						else if(!$new_roles)
						{
							$delete[] = array
							(
								1	=> array
								(
									'value'	=> $control_id,
									'type'	=> PDO::PARAM_INT
								),
								2	=> array
								(
									'value'	=> $user_id,
									'type'	=> PDO::PARAM_INT
								)
							);
						}
					}


				}
			}
			$this->db->transaction_begin();

			if($add)
			{
				$add_sql = "INSERT INTO controller_control_user_role (control_id, user_id, roles, modified_on, modified_by) VALUES (?, ?, ? ,? ,?)";
				$GLOBALS['phpgw']->db->insert($add_sql, $add, __LINE__, __FILE__);
			}

			if($update)
			{
				$update_sql = "UPDATE controller_control_user_role SET roles =?, modified_on = ?, modified_by = ? WHERE control_id =? AND user_id = ?";
				$GLOBALS['phpgw']->db->insert($update_sql, $update, __LINE__, __FILE__);
			}

			if($delete)
			{
				$delete_sql = "DELETE FROM controller_control_user_role WHERE control_id =? AND user_id = ?";
				$GLOBALS['phpgw']->db->delete($delete_sql, $delete, __LINE__, __FILE__);
			}

			return $this->db->transaction_commit();
		}

		public function get_inspectors( $check_list_id )
		{

			$sql = "SELECT control_id, controller_check_list_inspector.user_id FROM controller_check_list "
				. " $this->left_join controller_check_list_inspector ON controller_check_list.id = controller_check_list_inspector.check_list_id"
				. " WHERE controller_check_list.id = ?";

			$condition =  array((int)$check_list_id);

			$this->db->select($sql, $condition, __LINE__, __FILE__);

			$_inspectors = array();
			while ($this->db->next_record())
			{
				$control_id = $this->db->f('control_id');
				$_inspectors[] = $this->db->f('user_id');
			}

			/**
			 * Bitwise operator on 2 (inspector)
			 */
			$sql = "SELECT user_id FROM controller_control_user_role WHERE control_id = ? AND (roles & 2) > 0;";
			$condition =  array((int)$control_id);

			$this->db->select($sql, $condition, __LINE__, __FILE__);

			$inspectors = array();
			$__inspectors = array();

			while ($this->db->next_record())
			{
				$__inspectors[] = $this->db->f('user_id');
			}

			$___inspectors = array_unique(array_merge($_inspectors, $__inspectors));

			$sort_names = array();
			foreach ($___inspectors as $user_id)
			{
				$name = $GLOBALS['phpgw']->accounts->get($user_id)->__toString();
				$sort_names[] = $name;
				$inspectors[] = array(
					'id'	=> $user_id,
					'name'	=> $name,
					'selected' => in_array($user_id, $_inspectors)
				);
			}

			array_multisort($sort_names, SORT_ASC, $inspectors);

			return $inspectors;
		}

		public function get_district_users($control_id)
		{
			if(!$control_id)
			{
				return array();
			}

			$sql = "SELECT * FROM controller_control_user_district WHERE control_id = ?";
			$condition =  array((int)$control_id);

			$this->db->select($sql, $condition, __LINE__, __FILE__);

			$values = array();

			while ($this->db->next_record())
			{
				$part_of_town_id = $this->db->f('part_of_town_id');
				$values[$part_of_town_id] = $this->db->f('user_id');
			}

			return $values;
		}
		public function save_district( $data )
		{
			$add = array();
			$update = array();
			$delete = array();
			$now = time();

			foreach ($data as $control_id => $district_info)
			{
				foreach ($district_info as $part_of_town_id => $user_info)
				{
					$new_user = (int)$user_info['new'];
					$old_user = (int)$user_info['original'];

					if($old_user != $new_user)
					{

						if($new_user && $old_user)
						{
							$update[] = array
							(
								1	=> array
								(
									'value'	=> $new_user,
									'type'	=> PDO::PARAM_INT
								),
								2	=> array
								(
									'value'	=> $now,
									'type'	=> PDO::PARAM_INT
								),
								3	=> array
								(
									'value'	=> $this->account,
									'type'	=> PDO::PARAM_INT
								),
								4	=> array
								(
									'value'	=> $control_id,
									'type'	=> PDO::PARAM_INT
								),
								5	=> array
								(
									'value'	=> $part_of_town_id,
									'type'	=> PDO::PARAM_INT
								)
							);
						}
						else if($new_user && !$old_user)
						{
							$add[] = array
							(
								1	=> array
								(
									'value'	=> $control_id,
									'type'	=> PDO::PARAM_INT
								),
								2	=> array
								(
									'value'	=> $part_of_town_id,
									'type'	=> PDO::PARAM_INT
								),
								3	=> array
								(
									'value'	=> $new_user,
									'type'	=> PDO::PARAM_INT
								),
								4	=> array
								(
									'value'	=> $now,
									'type'	=> PDO::PARAM_INT
								),
								5	=> array
								(
									'value'	=> $this->account,
									'type'	=> PDO::PARAM_INT
								)
							);

						}
						else if(!$new_user)
						{
							$delete[] = array
							(
								1	=> array
								(
									'value'	=> $control_id,
									'type'	=> PDO::PARAM_INT
								),
								2	=> array
								(
									'value'	=> $part_of_town_id,
									'type'	=> PDO::PARAM_INT
								)
							);
						}
					}


				}
			}
			$this->db->transaction_begin();

			if($add)
			{
				$add_sql = "INSERT INTO controller_control_user_district (control_id, part_of_town_id, user_id, modified_on, modified_by) VALUES (?, ?, ? ,? ,?)";
				$GLOBALS['phpgw']->db->insert($add_sql, $add, __LINE__, __FILE__);
			}

			if($update)
			{
				$update_sql = "UPDATE controller_control_user_district SET user_id =?, modified_on = ?, modified_by = ? WHERE control_id =? AND part_of_town_id = ?";
				$GLOBALS['phpgw']->db->insert($update_sql, $update, __LINE__, __FILE__);
			}

			if($delete)
			{
				$delete_sql = "DELETE FROM controller_control_user_district WHERE control_id =? AND part_of_town_id = ?";
				$GLOBALS['phpgw']->db->delete($delete_sql, $delete, __LINE__, __FILE__);
			}

			return $this->db->transaction_commit();
		}


	}