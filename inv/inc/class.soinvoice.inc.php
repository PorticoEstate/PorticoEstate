<?php
	/*******************************************************************\
	* phpGroupWare - Inventory                                          *
	* http://www.phpgroupware.org                                       *
	*                                                                   *
	* Inventar Manager                                                  *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	*        and Joseph Engo <jengo@phpgroupware.org>                   *
	* -----------------------------------------------                   *
	* Copyright (C) 2000,2001,2002 Bettina Gille                        *
	*                                                                   *
	* This program is free software; you can redistribute it and/or     *
	* modify it under the terms of the GNU General Public License as    *
	* published by the Free Software Foundation; either version 2 of    *
	* the License, or (at your option) any later version.               *
	*                                                                   *
	* This program is distributed in the hope that it will be useful,   *
	* but WITHOUT ANY WARRANTY; without even the implied warranty of    *
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU  *
	* General Public License for more details.                          *
	*                                                                   *
	* You should have received a copy of the GNU General Public License *
	* along with this program; if not, write to the Free Software       *
	* Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.         *
	\*******************************************************************/
	/* $Id$ */

	class soinvoice
	{
		var $db;
		var $account;

		function soinvoice() 
		{
			$this->db		= $GLOBALS['phpgw']->db;
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function read_orders($start, $limit = True, $query = '', $filter = '', $sort = '', $order = '',$status = 'active') 
		{
			if ($status == 'archive')
			{
				$statussort = " AND status='archive'";
			}
			else
			{
				$statussort = " AND status != 'archive'";
			}

			if (!$sort)
			{
				$sort = "ASC";
			}

			if ($order) 
			{
				$ordermethod = " order by $order $sort"; 
			}
			else 
			{
				$ordermethod = "order by num asc"; 
			}

			if (! $filter) 
			{
				$filter = 'none'; 
			}

			if ($filter != 'private') 
			{
				if ($filter != 'none') 
				{ 
					$filtermethod = " access like '%,$filter,%' "; 
				}
				else 
				{
					$filtermethod = " ( owner=" . $this->account;
					if (is_array($this->grants))
					{
						$grants = $this->grants;
						while (list($user) = each($grants)) 
						{
							$public_user_list[] = $user;
						}
						reset($public_user_list);
						$filtermethod .= " OR (access='public' AND owner in(" . implode(',',$public_user_list) . ")))"; 
					}
					else 
					{
						$filtermethod .= ' )';
					}
				}
			}
			else
			{
				$filtermethod = ' owner=' . $this->account . ' ';
			}

			if ($query)
			{
				$querymethod = " AND (num like '%$query%' OR descr like '%$query%')";
			}

			$sql = "select * from phpgw_inv_orders WHERE $filtermethod $querymethod $statussort";

			$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			while ($this->db->next_record())
			{
				$orders[] = array
				(
					'order_id'	=> $this->db->f('id'),
					'owner'		=> $this->db->f('owner'),
					'access'	=> $this->db->f('access'),
					'num'		=> $this->db->f('num'),
					'date'		=> $this->db->f('date'),
					'customer'	=> $this->db->f('customer'),
					'descr'		=> $this->db->f('descr'),
					'status'	=> $this->db->f('status')
				);
			}
			return $orders;
		}
	}
?>
