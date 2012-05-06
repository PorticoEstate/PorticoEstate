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


	class property_sodimb_role_user
	{
	
		var $total_records = 0;

		function __construct()
		{
			$this->account_id 	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->db2			= clone($this->db);
			$this->join			= & $this->db->join;
			$this->left_join 	= & $this->db->left_join;
			$this->like			= & $this->db->like;

		}

		function read($data)
		{
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
	}
