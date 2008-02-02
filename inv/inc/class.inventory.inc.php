<?php
	/**************************************************************************\
	* phpGroupWare - Inventory                                                 *
	* http://www.phpgroupware.org                                              *
	* Written by Joseph Engo <jengo@phpgroupware.org>                          *
	*            Bettina Gille [ceb@phpgroupware.org]                          *
	* -----------------------------------------------                          *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id$ */

	class inventory
	{
		var $db;
		var $grants;
		var $stock;

		function inventory() 
		{
			global $phpgw;

			$this->db		= $phpgw->db;
			$this->db2		= $this->db;
			$this->grants	= $phpgw->acl->get_grants('inv');
			$this->stock	= $this->get_stock($product_id);
		}

		function check_perms($has, $needed) 
		{
			return (!!($has & $needed) == True);
		}

		function read_orders($start, $limit = True, $query = '', $filter = '', $sort = '', $order = '',$status = 'active') 
		{
			global $phpgw,$phpgw_info;

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
					$filtermethod = " ( owner=" . $phpgw_info['user']['account_id'];
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
				$filtermethod = ' owner=' . $phpgw_info['user']['account_id'] . ' ';
			}

			if ($query)
			{
				$querymethod = " AND (num like '%$query%' OR descr like '%$query%')";
			}

			$sql = "select * from phpgw_inv_orders WHERE $filtermethod $querymethod $statussort";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();
			$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);

			$i = 0;
			while ($this->db->next_record())
			{
				$orders[$i]['id']		= $this->db->f('id');
				$orders[$i]['owner']	= $this->db->f('owner');
				$orders[$i]['access']	= $this->db->f('access');
				$orders[$i]['num']		= $this->db->f('num');
				$orders[$i]['date']		= $this->db->f('date');
				$orders[$i]['customer']	= $this->db->f('customer');
				$orders[$i]['descr']	= $this->db->f('descr');
				$orders[$i]['status']	= $this->db->f('status');
				$i++;
			}
			return $orders;
		}

		function get_status_id($status_name='archive') 
		{
			global $phpgw;

			$this->db->query("SELECT status_id from phpgw_inv_statuslist WHERE status_name='archive'",__LINE__,__FILE__);
			$this->db->next_record();
			$status_id = $this->db->f('status_id');
			return $status_id;
		}

		function read_products($start, $limit = True, $query = '', $object = 'category',$filter = '', $sort = '', $order = '',$status = 'active') 
		{
			global $phpgw;

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = " order by id asc";
			}

			if ($query)
			{ 
				$querymethod = " AND (id like '%$query%' OR serial like '%$query%' OR name like '%$query%' OR descr like '%$query%' "
							. "OR cost like '%$query%' OR price like '%$query%' OR stock like '%$query%' OR mstock like '%$query%' OR url like '%$query%' "
							. "OR ftp like '%$query%' OR pdate like '%$query%' OR sdate like '%$query%' OR product_note like '%$query%')";
			}

			if ($status == 'active') : $status_sort = " status !='" . $this->status_id . "'";
			elseif ($status == 'minstock') : $status_sort = " status !='" . $this->status_id . "' AND mstock >= stock AND mstock != '0'";
			elseif ($status == 'archive') : $status_sort = " status ='" . $this->status_id . "'";
			endif;

			$sql = "SELECT * from phpgw_inv_products WHERE $object='$filter' AND $status_sort $querymethod";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();
			$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);

			$i = 0;
			while($this->db->next_record())
			{
				$products[$i]['con']		= $this->db->f('con');
				$products[$i]['id']			= $this->db->f('id');
				$products[$i]['serial']		= $this->db->f('serial');
				$products[$i]['name']		= $this->db->f('name');
				$products[$i]['descr']		= $this->db->f('descr');
				$products[$i]['category']	= $this->db->f('category');
				$products[$i]['status']		= $this->db->f('status');
				$products[$i]['weight']		= $this->db->f('weight');
				$products[$i]['cost']		= $this->db->f('cost');
				$products[$i]['price']		= $this->db->f('price');
				$products[$i]['retail']		= $this->db->f('retail');
				$products[$i]['stock']		= $this->db->f('stock');
				$products[$i]['mstock']		= $this->db->f('mstock');
				$products[$i]['url']		= $this->db->f('url');
				$products[$i]['ftp']		= $this->db->f('ftp');
				$products[$i]['dist']		= $this->db->f('dist');
				$products[$i]['pdate']		= $this->db->f('pdate');
				$products[$i]['sdate']		= $this->db->f('sdate');
				$products[$i]['bin']		= $this->db->f('bin');
				$products[$i]['note']		= $this->db->f('product_note');
				$i++;
			}
			return $products;
		}

		function read_rooms($start, $limit = True, $query = '', $filter = '', $sort = '', $order = '')
		{
			global $phpgw,$phpgw_info;

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
				$ordermethod = "order by room_name asc";
			}

			if (! $filter)
			{
				$filter = 'none';
			}

			if ($filter != 'private')
			{
				if ($filter != 'none')
				{
					$filtermethod = " room_access like '%,$filter,%' ";
				}
				else
				{
					$filtermethod = " ( room_owner=" . $phpgw_info['user']['account_id'];
					if (is_array($this->grants))
					{
						$grants = $this->grants;
						while (list($user) = each($grants))
						{
							$public_user_list[] = $user;
						}
						reset($public_user_list);
						$filtermethod .= " OR (room_access='public' AND room_owner in(" . implode(',',$public_user_list) . ")))";
					}
					else
					{
						$filtermethod .= ' )';
					}
				}
			}
			else
			{
				$filtermethod = ' room_owner=' . $phpgw_info['user']['account_id'] . ' ';
			}

			if ($query)
			{
				$querymethod = " AND (room_name like '%$query%' OR room_note like '%$query%')";
			}

			$sql = "select * from phpgw_inv_stockrooms WHERE $filtermethod $querymethod";

			$this->db2->query($sql,__LINE__,__FILE__);

			$this->total_records = $this->db2->num_rows();

			if ($limit)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$i = 0;

			while ($this->db->next_record())
			{
				$rooms[$i]['id']		= $this->db->f('id');
				$rooms[$i]['owner']		= $this->db->f('room_owner');
				$rooms[$i]['access']	= $this->db->f('room_access');
				$rooms[$i]['name']		= $this->db->f('room_name');
				$rooms[$i]['note']		= $this->db->f('room_note');
				$i++;
			}
			return $rooms;
		}

		function select_room_list($selected = '')
		{
			global $phpgw;

			$rooms = $this->read_rooms($start,False,$query,$filter,$sort,$order);

			for ($i=0;$i<count($rooms);$i++)
			{
				$s .= '<option value="' . $rooms[$i]['id'] . '"';
				if ($rooms[$i]['id'] == $selected)
				{
					$s .= ' selected';
				}
				$s .= '>' . $phpgw->strip_html($rooms[$i]['name']);
				$s .= '</option>' . "\n";
			}
			return $s;
		}

		function one_room($id = '')
		{
			$this->db->query("select * from phpgw_inv_stockrooms where id='$id'",__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$room[0]['id']		= $this->db->f('id');
				$room[0]['owner']	= $this->db->f('room_owner');
				$room[0]['access']	= $this->db->f('room_access');
				$room[0]['name']	= $this->db->f('room_name');
				$room[0]['note']	= $this->db->f('room_note');
			}
			return $room;
		}

		function get_stock($product_id = '')
		{
			$this->db->query("select stock from phpgw_inv_products where con='$product_id'",__LINE__,__FILE__);
			$this->db->next_record();
			$stock = $this->db->f('stock');
			return $stock;
		}

		function check_stock($product_id = '',$piece = '')
		{
			$stock = $this->get_stock($product_id);
			if ($stock < $piece)
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		function update_stock($action = 'add',$product_id = '',$piece = '')
		{
			$stock = $this->get_stock($product_id);

			if ($action == 'add')
			{
				$newstock = $stock + $piece;
			}

			if ($action == 'delete')		
			{
				$newstock = $stock - $piece;
			}

			$this->db->query("update phpgw_inv_products set stock='$newstock' where con='$product_id'",__LINE__,__FILE__);
		}
	}
?>
