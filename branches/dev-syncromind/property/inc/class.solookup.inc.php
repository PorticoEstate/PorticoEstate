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
		var $grants;

		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->like			= & $this->db->like;
		}

		function read_addressbook($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter		= isset($data['filter'])?$data['filter']:'none';
				$query		= isset($data['query'])?$data['query']:'';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order'])?$data['order']:'';
				$cat_id		= isset($data['cat_id'])?$data['cat_id']:0;
				$allrows	= isset($data['allrows'])?$data['allrows']:'';
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by last_name DESC';
			}


			$where= 'WHERE';

			if ($cat_id > 0)
			{
				$filtermethod .= " $where cat_id $this->like '%,$cat_id,%' ";
				$where= 'AND';
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " $where org_name $this->like '%$query%'";
			}

			$sql = "SELECT person_id,first_name,last_name FROM phpgw_contact_person $filtermethod $querymethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);			
			}

			$contact = array();
			while ($this->db->next_record())
			{
				$contact[] = array
					(
						'id'			=> $this->db->f('person_id'),
						'contact_name'	=> $this->db->f('last_name',true) . ', ' . $this->db->f('first_name',true),
					);
			}
			//_debug_array($vendor);

			return $contact;
		}


		function read_b_account($data)
		{
			$start		= isset($data['start']) && $data['start'] ? (int)$data['start'] : 0;
			$query		= isset($data['query'])?$data['query']:'';
			$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
			$order		= isset($data['order'])?$data['order']:'';
			$allrows	= isset($data['allrows'])?$data['allrows']:'';
			$role		= isset($data['role'])?$data['role']:'';
			$parent		= isset($data['parent']) && $data['parent'] ? (int)$data['parent'] : 0;

			$join = '';
			$filter_parent = '';
			if($role == 'group')
			{
				$table = 'fm_b_account_category';
			}
			else
			{
				$table = 'fm_b_account';
				$join = " {$this->join} fm_b_account_category ON (fm_b_account.category = fm_b_account_category.id AND fm_b_account_category.active = 1)";
			}

			if ($order)
			{
				$ordermethod = " ORDER BY {$table}.{$order} $sort";
			}
			else
			{
				$ordermethod = " ORDER BY {$table}.id DESC";
			}

			$filtermethod = " WHERE {$table}.active = 1";
			$where = 'AND';

			if($parent)
			{
				$filtermethod .= " {$where} category = {$parent}";
				$where = 'AND';
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				if($role == 'group')
				{
					$querymethod = " $where ({$table}.id = " . (int)$query . " OR {$table}.descr $this->like '%$query%')";
				}
				else
				{
					$querymethod = " $where ({$table}.id $this->like '%$query%' OR {$table}.descr $this->like '%$query%')";
				}
			}

			$sql = "SELECT {$table}.* FROM {$table}{$join}{$filtermethod}{$querymethod}";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);			
			}

			$b_account = array();
			while ($this->db->next_record())
			{
				$b_account[] = array
				(
					'id'			=> $this->db->f('id'),
					'descr'			=> $this->db->f('descr',true)
				);
			}

			return $b_account;
		}


		function read_street($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter		= isset($data['filter'])?$data['filter']:'none';
				$query		= isset($data['query'])?$data['query']:'';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order'])?$data['order']:'';
				$cat_id		= isset($data['cat_id'])?$data['cat_id']:0;
				$allrows	= isset($data['allrows'])?$data['allrows']:'';
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by fm_streetaddress.descr DESC';
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " where ( descr $this->like '%$query%')";
			}

			$sql = "SELECT * FROM fm_streetaddress $querymethod  ";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();
			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$street = array();
			while ($this->db->next_record())
			{
				$street[] = array
					(
						'id'			=> $this->db->f('id'),
						'street_name'	=> $this->db->f('descr',true)
					);
			}

			return $street;
		}

		function read_tenant($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter		= isset($data['filter'])?$data['filter']:'none';
				$query		= isset($data['query'])?$data['query']:'';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order'])?$data['order']:'';
				$cat_id		= isset($data['cat_id'])?$data['cat_id']:0;
				$allrows	= isset($data['allrows'])?$data['allrows']:'';
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by last_name DESC';
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " where ( last_name $this->like '%$query%' or first_name $this->like '%$query%')";
			}

			$sql = "SELECT * FROM fm_tenant $querymethod  ";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$tenant = array();
			while ($this->db->next_record())
			{
				$tenant[] = array
					(
						'id'			=> $this->db->f('id'),
						'last_name'		=> $this->db->f('last_name',true),
						'first_name'	=> $this->db->f('first_name',true)
					);
			}

			return $tenant;
		}

		function read_ns3420($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter		= isset($data['filter'])?$data['filter']:'none';
				$query		= isset($data['query'])?$data['query']:'';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order'])?$data['order']:'';
				$cat_id		= isset($data['cat_id'])?$data['cat_id']:0;
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by tekst1 DESC';
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " where ( tekst1 $this->like '%$query%' or tekst2 $this->like '%$query%' or tekst3 $this->like '%$query%' or tekst4 $this->like '%$query%' or tekst5 $this->like '%$query%' or tekst6 $this->like '%$query%')";
			}

			$sql = "SELECT * FROM fm_ns3420  $querymethod  ";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();
			$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);

			$ns3420 = array();
			while ($this->db->next_record())
			{
				$ns3420[] = array
					(
						'id'			=> $this->db->f('id'),
						'ns3420_descr'	=> $this->db->f('tekst1',true) . ' ' .$this->db->f('tekst2',true) . ' ' .$this->db->f('tekst3',true) . ' ' .$this->db->f('tekst4',true) . ' ' .$this->db->f('tekst5',true) . ' ' .$this->db->f('tekst6',true)
					);
			}

			return $ns3420;
		}

		function read_phpgw_user($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter		= isset($data['filter'])?$data['filter']:'none';
				$query		= isset($data['query'])?$data['query']:'';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order'])?$data['order']:'';
				$cat_id		= isset($data['cat_id'])?$data['cat_id']:0;
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by account_lastname DESC';
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " AND ( account_lastname $this->like '%$query%' or account_firstname $this->like '%$query%')";
			}

			$sql = "SELECT * FROM phpgw_accounts WHERE account_status = 'A' AND account_type = 'u' $querymethod  ";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();
			$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);

			$phpgw_user = array();
			while ($this->db->next_record())
			{
				$phpgw_user[] = array
					(
						'id'			=> $this->db->f('account_id'),
						'last_name'		=> $this->db->f('account_lastname',true),
						'first_name'	=> $this->db->f('account_firstname',true)
					);
			}
			return $phpgw_user;
		}

	}
