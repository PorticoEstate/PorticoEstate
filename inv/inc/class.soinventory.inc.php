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

	class soinventory
	{
		var $db;
		var $account;

		function soinventory() 
		{
			$this->db		= $GLOBALS['phpgw']->db;
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function get_status_id() 
		{
			$this->db->query("SELECT status_id from phpgw_inv_statuslist WHERE status_name='archive'",__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('status_id');
		}

		function status_list()
		{
			$this->db->query("select * from phpgw_inv_statuslist",__LINE__,__FILE__);
			while ($this->db->next_record()) 
			{
				$status_list[$this->db->f('status_id')] = lang($this->db->f('status_name'));
			}
			return $status_list;
		}

		function read_status()
		{
			$this->db->query("select * from phpgw_inv_statuslist order by status_name asc",__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$sta[] = array
				(
					'status_id'		=> $this->db->f('status_id'),
					'status_name'	=> $this->db->f('status_name')
				);
			}
			return $sta;
		}

		function add_status($status_name)
		{
			$this->db->query("insert into phpgw_inv_statuslist (status_name) values ('" . $this->db->db_addslashes($status_name) . "')",__LINE__,__FILE__);
		}

		function edit_status($values)
		{
			$this->db->query("UPDATE phpgw_inv_statuslist set status_name='" . $this->db->db_addslashes($values['status_name']) . "' where status_id='"
							. intval($values['status_id']) . "'",__LINE__,__FILE__);
		}

		function read_products($start, $limit = True, $query = '', $object = 'category',$object_id = '', $sort = '', $order = '',$status = 'active') 
		{
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

			switch ($status)
			{
				case 'active':		$status_sort = " status !='" . $this->get_status_id() . "'"; break;
				case 'receipt':		$status_sort = " status !='" . $this->get_status_id() . "'"; break;
				case 'minstock':	$status_sort = " status !='" . $this->get_status_id() . "' AND mstock >= stock AND mstock != '0'"; break;
				case 'archive':		$status_sort = " status ='" . $this->get_status_id() . "'"; break;
			}

			$sql = "SELECT * from phpgw_inv_products WHERE $object='$object_id' AND $status_sort $querymethod";

			$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			$i = 0;
			while($this->db->next_record())
			{
				$products[$i]['con']		= $this->db->f('con');
				$products[$i]['num']		= $this->db->f('id');
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

		function read_single_product($con)
		{
			$this->db->query("SELECT * from phpgw_inv_products WHERE con='$con'",__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$product['con']		= $this->db->f('con');
				$product['num']		= $this->db->f('id');
				$product['serial']	= $this->db->f('serial');
				$product['name']	= $this->db->f('name');
				$product['descr']	= $this->db->f('descr');
				$product['cat_id']	= $this->db->f('category');
				$product['status']	= $this->db->f('status');
				$product['weight']	= $this->db->f('weight');
				$product['cost']	= $this->db->f('cost');
				$product['price']	= $this->db->f('price');
				$product['retail']	= $this->db->f('retail');
				$product['stock']	= $this->db->f('stock');
				$product['mstock']	= $this->db->f('mstock');
				$product['url']		= $this->db->f('url');
				$product['ftp']		= $this->db->f('ftp');
				$product['dist']	= $this->db->f('dist');
				$product['pdate']	= $this->db->f('pdate');
				$product['sdate']	= $this->db->f('sdate');
				$product['bin']		= $this->db->f('bin');
				$product['note']	= $this->db->f('product_note');
			}
			return $product;
		}


		function read_rooms($start, $limit = True, $query = '', $filter = '', $sort = '', $order = '')
		{
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
					$filtermethod = " ( room_owner=" . $this->account;
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
				$filtermethod = ' room_owner=' . $this->account . ' ';
			}

			if ($query)
			{
				$querymethod = " AND (room_name like '%$query%' OR room_note like '%$query%')";
			}

			$sql = "select * from phpgw_inv_stockrooms WHERE $filtermethod $querymethod";

			if ($limit)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$this->total_records = $this->db->num_rows();

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

		function return_value($item)
		{
			$this->db->query("SELECT status_name from phpgw_inv_statuslist where status_id='" . $item . "'",__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				return $this->db->f('status_name');
			}
		}

		function exists($values)
		{
			if ($values['action'] == 'status')
			{
				$this->db->query("select count(*) from phpgw_inv_statuslist where status_name='" . $values['status_name']);		
			}
			else
			{
				if (!$values['cat_id'])
				{
					$values['cat_id'] = 0;
				}

				switch($values['action'])
				{
					case 'num':		$column = 'id'; $item = $values['num']; break;
					case 'name':	$column = 'name'; $item = $values['name']; break;
				}

				if ($values['product_id'] && ($values['product_id'] != 0))
				{
					$editexists = " and con !='" . $values['product_id'] . "'";
				}

				$this->db->query("select count(*) from phpgw_inv_products where $column ='" . $item . "' AND category='"
								. $values['cat_id'] . "'" . $editexists,__LINE__,__FILE__);
			}

			if ($this->db->f(0))
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		function add_product($values)
		{
			$values['num']		= $this->db->db_addslashes($values['num']);
			$values['serial']	= $this->db->db_addslashes($values['serial']);
			$values['name']		= $this->db->db_addslashes($values['name']);
			$values['descr']	= $this->db->db_addslashes($values['descr']);
			$values['note']		= $this->db->db_addslashes($values['note']);
			$values['url']		= $this->db->db_addslashes($values['url']);
			$values['ftp']		= $this->db->db_addslashes($values['ftp']);

			$this->db->query("insert into phpgw_inv_products (id,serial,name,descr,category,status,cost,price,retail,stock,mstock,url,ftp,dist,"
							. "pdate,sdate,bin,product_note) values ('" . $values['num'] . "','" . $values['serial'] . "','" . $values['name']
							. "','" . $values['descr'] . "','" . $values['cat_id'] . "','" . $values['status'] . "','" . $values['cost']
							. "','" . $values['price'] . "','" . $values['retail'] . "','" . $values['stock'] . "','" . $values['mstock'] . "','"
							. $values['url'] . "','" . $values['ftp'] . "','" . $values['dist'] . "','" . $values['pdate'] . "','" . $values['sdate']
							. "','" . $values['bin'] . "','" . $values['note'] . "')",__LINE__,__FILE__);
		}

		function edit_product($values)
		{
			$values['num']		= $this->db->db_addslashes($values['num']);
			$values['serial']	= $this->db->db_addslashes($values['serial']);
			$values['name']		= $this->db->db_addslashes($values['name']);
			$values['descr']	= $this->db->db_addslashes($values['descr']);
			$values['note']		= $this->db->db_addslashes($values['note']);
			$values['url']		= $this->db->db_addslashes($values['url']);
			$values['ftp']		= $this->db->db_addslashes($values['ftp']);

			$this->db->query("update phpgw_inv_products set id='" . $values['num'] . "', serial='" . $values['serial'] . "',name='"
							. $values['name'] . "', descr='" . $values['descr'] . "', category='" . $values['cat_id'] . "',status='"
							. $values['status'] . "',cost='" . $values['cost'] . "', price='" . $values['price'] . "', retail='"
							. $values['retail'] . "', stock='" . $values['stock'] . "', mstock='" . $values['mstock'] . "',url='"
							. $values['url'] . "',ftp='" . $values['ftp'] . "',dist='" . $values['dist'] . "',pdate='" . $values['pdate']
							. "',sdate='" . $values['sdate'] . "',bin='" . $values['bin'] . "',product_note='" . $values['note']
							. "' where con='" . $values['product_id'] . "'",__LINE__,__FILE__);
		}

		function max_product_number($number) 
		{
			$this->db->query("select max(id) from phpgw_inv_products where id like ('$number%')",__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				return $this->db->f(0);
			}
		}

		function delete($values)
		{
			if ($values['action'] == 'pro')
			{
				$this->db->query("delete from phpgw_inv_products where con='" . $values['product_id'] . "'",__LINE__,__FILE__);
			}
			else
			{
				$this->db->query("delete from phpgw_inv_statuslist where status_id='" . $values['status_id'] . "'",__LINE__,__FILE__);
			}
		}
	}
?>
