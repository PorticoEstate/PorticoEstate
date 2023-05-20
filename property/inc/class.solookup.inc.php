<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @subpackage core
	 * @version $Id$
	 */

	/**
	 * Description
	 * @package property
	 */
	class property_solookup
	{

		private $db;
		private $join, $left_join;
		private $like;
		private $account_id;
		public $total_records;

		function __construct()
		{
			$this->db			 = & $GLOBALS['phpgw']->db;
			$this->join			 = & $this->db->join;
			$this->left_join	 = & $this->db->left_join;
			$this->like			 = & $this->db->like;
			$this->account_id	 = (int)$GLOBALS['phpgw_info']['user']['account_id'];
		}

		function read_b_account( $data )
		{
			$start	 = isset($data['start']) && $data['start'] ? (int)$data['start'] : 0;
			$query	 = isset($data['query']) ? $data['query'] : '';
			$sort	 = isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order	 = isset($data['order']) ? $data['order'] : '';
			$allrows = isset($data['allrows']) ? $data['allrows'] : '';
			$role	 = isset($data['role']) ? $data['role'] : '';
			$parent	 = isset($data['parent']) && $data['parent'] ? (int)$data['parent'] : 0;
			$results = isset($data['results']) ? (int)$data['results'] : 0;

			$join			 = '';
			$filter_parent	 = '';
			if ($role == 'group')
			{
				$table = 'fm_b_account_category';
			}
			else
			{
				$table	 = 'fm_b_account';
				$join	 = " {$this->join} fm_b_account_category ON (fm_b_account.category = fm_b_account_category.id AND fm_b_account_category.active = 1)";
			}

			if ($order)
			{
				$ordermethod = " ORDER BY {$table}.{$order} $sort";
			}
			else
			{
				$ordermethod = " ORDER BY {$table}.id DESC";
			}

			$filtermethod	 = " WHERE {$table}.active = 1";
			$where			 = 'AND';

			if ($parent)
			{
				$filtermethod	 .= " {$where} category = {$parent}";
				$where			 = 'AND';
			}

			if ($query)
			{
				$query = $this->db->db_addslashes($query);

				if ($role == 'group')
				{
					$querymethod = " $where ({$table}.id = " . (int)$query . " OR {$table}.descr $this->like '%$query%')";
				}
				else
				{
					$querymethod = " $where ({$table}.id $this->like '%$query%' OR {$table}.descr $this->like '%$query%')";
				}
			}

			$sql = "SELECT {$table}.* FROM {$table}{$join}{$filtermethod}{$querymethod}";

			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();

			if (!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__, $results);
			}
			else
			{
				$this->db->query($sql . $ordermethod, __LINE__, __FILE__);
			}

			$b_account = array();
			while ($this->db->next_record())
			{
				$b_account[] = array
					(
					'id'	 => $this->db->f('id'),
					'descr'	 => $this->db->f('descr', true)
				);
			}

			return $b_account;
		}

		function read_phpgw_user( $data )
		{
			if (is_array($data))
			{
				$start	 = isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter	 = isset($data['filter']) ? $data['filter'] : 'none';
				$query	 = isset($data['query']) ? $data['query'] : '';
				$sort	 = isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
				$order	 = isset($data['order']) ? $data['order'] : '';
				$cat_id	 = isset($data['cat_id']) ? $data['cat_id'] : 0;
				$results = isset($data['results']) && $data['results'] ? (int)$data['results'] : 0;
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by account_lastname DESC';
			}

			if ($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " AND ( account_lastname $this->like '%$query%' or account_firstname $this->like '%$query%')";
			}

			$sql = "SELECT * FROM phpgw_accounts WHERE account_status = 'A' AND account_type = 'u' $querymethod  ";

			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();
			$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__, $results);

			$phpgw_user = array();
			while ($this->db->next_record())
			{
				$phpgw_user[] = array
					(
					'id'		 => $this->db->f('account_id'),
					'last_name'	 => $this->db->f('account_lastname', true),
					'first_name' => $this->db->f('account_firstname', true)
				);
			}
			return $phpgw_user;
		}

		function read_outside_contact( $data )
		{
			if (is_array($data))
			{
				$start	 = isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter	 = isset($data['filter']) ? $data['filter'] : 'none';
				$query	 = isset($data['query']) ? $data['query'] : '';
				$sort	 = isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
				$order	 = isset($data['order']) ? $data['order'] : '';
				$cat_id	 = isset($data['cat_id']) ? $data['cat_id'] : 0;
				$results = isset($data['results']) && $data['results'] ? (int)$data['results'] : 0;
			}
		
			switch ($order)
			{
				case 'contact_id':
					$ordermethod = " ORDER BY phpgw_contact.contact_id $sort";
					break;
				default:
					$ordermethod = " ORDER BY $order $sort";
					break;
			}
			
			if (!$order)
			{
				$ordermethod = ' ORDER BY last_name DESC';
			}

			$filtermethod = '';

			if($cat_id)
			{
				$filtermethod .= "AND (cat_id $this->like '%,$cat_id,%' OR cat_id = '{$cat_id}') ";
			}

			if ($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " AND ( last_name $this->like '%$query%' OR first_name $this->like '%$query%' OR department $this->like '%$query%' OR comm_data $this->like '%$query%')";
			}

			$sql = "SELECT phpgw_contact.contact_id,first_name, last_name, department, comm_data AS email FROM phpgw_contact"
				. " $this->join phpgw_contact_person ON phpgw_contact.contact_id = phpgw_contact_person.person_id"
				. " $this->join phpgw_contact_comm ON phpgw_contact.contact_id = phpgw_contact_comm.contact_id AND comm_descr_id = 2"
				. " $this->left_join phpgw_accounts ON phpgw_accounts.person_id = phpgw_contact.contact_id"
				. " WHERE phpgw_accounts.person_id IS NULL"
				. " AND contact_type_id = 1"
				. " AND (access = 'public' OR owner = {$this->account_id})"
				. " $filtermethod $querymethod";

			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();
			$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__, $results);

			$contacts = array();
			while ($this->db->next_record())
			{
				$contacts[] = array
				(
					'contact_id' => $this->db->f('contact_id'),
					'last_name'	 => $this->db->f('last_name', true),
					'first_name' => $this->db->f('first_name', true),
					'department' => $this->db->f('department', true),
					'email'		 => $this->db->f('email', true)
				);
			}
			return $contacts;
		}
	}