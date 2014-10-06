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

	class property_sodimb_role_user
	{
	
		var $total_records = 0;

		function __construct()
		{
			$this->account_id 	= (int) $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->db2			= clone($this->db);
			$this->join			= & $this->db->join;
			$this->left_join 	= & $this->db->left_join;
			$this->like			= & $this->db->like;

		}

		function read($data)
		{
			$query_start =  phpgwapi_datetime::date_to_timestamp($data['query_start']);
			$query_end =  phpgwapi_datetime::date_to_timestamp($data['query_end']);
			$get_netto_list = isset($data['get_netto_list']) && $data['get_netto_list'] ? true : false;


			$dimb_id = (int) $data['dimb_id'];			
			if(isset($data['user_id']) && $data['user_id'])
			{
				$user_id = (int) $data['user_id'];
			}
			else if(!$dimb_id)
			{
				$user_id = $this->account_id;
			}

			$role_id = (int) $data['role_id'];
			$query = $this->db->db_addslashes($data['query']);

			$filtermethod = '';
			$where = 'AND';
			if($user_id)
			{
				$filtermethod .= "{$where} user_id = $user_id";
				$where = 'AND';
			}
			if($role_id)
			{
				$filterrole = "WHERE id = $role_id";
				$filtermethod .= "{$where} role_id = $role_id";
				$where = 'AND';
			}
			if($dimb_id)
			{
				$filterdimb = "WHERE id = $dimb_id";
				$filtermethod .= "{$where} ecodimb = $dimb_id";
				$where = 'AND';
			}

			if($query_start)
			{
				$filtermethod .= "{$where} active_from < $query_start";				
			}

			if($query_end)
			{
				$filtermethod .= "{$where} (active_to > $query_end OR active_to = 0)";				
			}

			$sql = "SELECT fm_ecodimb_role_user.id, fm_ecodimb.id as ecodimb, user_id,role_id, active_from, active_to, default_user, fm_ecodimb_role.name as role"
			. " FROM fm_ecodimb_role_user"
			. " {$this->join} fm_ecodimb ON fm_ecodimb.id = fm_ecodimb_role_user.ecodimb"
			. " {$this->join} fm_ecodimb_role ON fm_ecodimb_role.id = fm_ecodimb_role_user.role_id"
			. " WHERE expired_on IS NULL {$filtermethod}"
			. " ORDER BY ecodimb ASC ";
			
//_debug_array($sql);
			$this->db->query($sql,__LINE__,__FILE__);

			$user_data = array();
			while ($this->db->next_record())
			{
				$user_data[$this->db->f('ecodimb')][ $this->db->f('role_id')][$this->db->f('user_id')] = array
				(
					'id' 			=> $this->db->f('id'),
					'ecodimb' 		=> $this->db->f('ecodimb'),
					'user_id'		=> $this->db->f('user_id'),
					'role_id'		=> $this->db->f('role_id'),
					'role'			=> $this->db->f('role',true),
					'default_user' 	=> $this->db->f('default_user'),
					'active_from' 	=> $this->db->f('active_from'),
					'active_to' 	=> $this->db->f('active_to'),
					'ecodimb' 		=> $this->db->f('ecodimb'),
				);
			}

			if($get_netto_list)
			{
				return $user_data;
			}

			$sql = "SELECT id, name FROM fm_ecodimb_role {$filterrole} ORDER BY id ASC ";
			$this->db->query($sql,__LINE__,__FILE__);
			$roles = array();

			while ($this->db->next_record())
			{
				$roles[] = array
				(
					'id' => $this->db->f('id'),
					'name'	=> $this->db->f('name', true),
				);
			}



			$sql = "SELECT fm_ecodimb.id FROM fm_ecodimb {$filterdimb} ORDER BY id ASC ";
			$this->db->query($sql,__LINE__,__FILE__);
			$dimbs = array();

			while ($this->db->next_record())
			{
				$dimbs[] = $this->db->f('id');
			}


			if($dimb_id && ! $user_id)
			{
				$users = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_READ, '.invoice','property');
			}
			else
			{
				$users = array(array('account_id' => $user_id));
			}

			$values = array();

			foreach($dimbs as $dimb)
			{
				foreach($roles as $role)
				{
					foreach ($users as $dummy => $user)
					{
						if(isset($user_data[$dimb][$role['id']][$user['account_id']]))
						{
							$values[] = $user_data[$dimb][$role['id']][$user['account_id']];
						}
						else
						{
							$values[] = array
							(
								'ecodimb' 		=> $dimb,
								'role_id'		=> $role['id'],
								'role'			=> $role['name'],
								'user_id'		=> $user['account_id'],
								'default_user'	=> ''
							);
						}
					}
				}
			}

			return $values;
		}

		public function edit($data)
		{
			$active_from	= phpgwapi_datetime::date_to_timestamp($data['active_from']);
			$active_to		= phpgwapi_datetime::date_to_timestamp($data['active_to']);
			$delete 		= isset($data['delete']) && is_array($data['delete']) ? $data['delete'] : array();
			$default_user 	= isset($data['default_user']) && is_array($data['default_user']) ? $data['default_user'] : array();
			$alter_date 	= isset($data['alter_date']) && is_array($data['alter_date']) ? $data['alter_date'] : array();
			$add 			= isset($data['add']) && is_array($data['add']) ? $data['add'] : array();

			$this->db->transaction_begin();

			$c_default_user = 0;
			foreach($default_user as $id)
			{
				if( !in_array($id, $delete) )
				{
					$this->db->query("UPDATE fm_ecodimb_role_user SET default_user = 1 WHERE id = '{$id}'",__LINE__,__FILE__);
					$c_default_user ++;
				}
			}

			unset($id);

			$c_alter_date = 0;
			foreach($alter_date as $id)
			{
				if( !in_array($id, $delete) )
				{
					$value_set = array();
					if($active_from)
					{
						$value_set['active_from'] = $active_from;
					}
					if($active_to)
					{
						$value_set['active_to'] = $active_to;
					}
					
					if($value_set)
					{
						$value_set	= $this->db->validate_update($value_set);
						$this->db->query("UPDATE fm_ecodimb_role_user SET {$value_set} WHERE id = '{$id}'",__LINE__,__FILE__);
						unset($value_set);
					}
					$c_alter_date ++;
				}
			}
			unset($id);

			foreach($add as $info)
			{
				$user_arr = explode('_',  $info);
				$value_set = array
				(
					'ecodimb'		=> $user_arr[0],
					'role_id'		=> $user_arr[1],
					'user_id'		=> $user_arr[2],
					'default_user'	=> false,
					'active_from'	=> $active_from ? $active_from : time(),
					'active_to'		=> $active_to ? $active_to : 0,
					'created_on'	=> time(),
					'created_by'	=> $this->account_id
				);
				
				$sql = 'INSERT INTO fm_ecodimb_role_user (' . implode(',', array_keys($value_set)) . ') VALUES (' . $this->db->validate_insert(array_values($value_set)) . ')';
				$this->db->query($sql,__LINE__,__FILE__);				
			}

			$ok = false;
			if($this->db->transaction_commit())
			{
				$ok = true;
				foreach($delete as $id)
				{
					$this->db->query('UPDATE fm_ecodimb_role_user SET expired_on =' . time() . " , expired_by = {$this->account_id} WHERE id = '{$id}'",__LINE__,__FILE__);
				}

				if($delete)
				{
					phpgwapi_cache::message_set(lang('%1 roles deleted', count($delete)), 'message');
				}
				if($c_alter_date)
				{
					phpgwapi_cache::message_set(lang('%1 dates altered', $c_alter_date), 'message');
				}
				if($add)
				{
					phpgwapi_cache::message_set(lang('%1 roles added', count($add)), 'message');
				}

				if($c_default_user)
				{
					phpgwapi_cache::message_set(lang('%1 roles set at default', $c_default_user), 'message');
				}
			}

			return $ok;
		}
	}
