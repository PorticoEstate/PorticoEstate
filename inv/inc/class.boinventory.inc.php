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

	class boinventory
	{
		var $action;
		var $start;
		var $query;
		var $filter;
		var $order;
		var $sort;
		var $cat_id;
		var $product_id;
		var $status;
		var $selection;

		var $public_functions = array
		(
			'cached_accounts'		=> True,
			'read_products'			=> True,
			'check_perms'			=> True,
			'check_values'			=> True,
			'return_value'			=> True,
			'select_status_list'	=> True,
			'select_dist_list'		=> True
		);

		function boinventory($session=False)
		{
			$this->soinv	= CreateObject('inv.soinventory');
			$this->cats		= CreateObject('phpgwapi.categories');
			$this->contacts	= CreateObject('phpgwapi.contacts');
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start		= get_var('start',Array('GET','POST'));
			$query		= get_var('query',Array('GET','POST'));
			$sort		= get_var('sort',Array('GET','POST'));
			$order		= get_var('order',Array('GET','POST'));
			$cat_id		= get_var('cat_id',Array('GET','POST'));
			$action		= get_var('action',Array('GET','POST'));
			$product_id	= get_var('product_id',Array('GET','POST'));
			$status		= get_var('status',Array('GET','POST'));
			$dist		= get_var('dist',Array('GET','POST'));
			$selection	= get_var('selection',Array('GET','POST'));

			if(!empty($start) || $start == '0' || $start == 0)
			{
				$this->start = $start;
			}
			if((empty($query) && !empty($this->query)) || !empty($query))
			{
				$this->query = $query;
			}

			if (isset($cat_id) && !empty($cat_id))
			{
				$this->cat_id = $cat_id;
			}

			if($cat_id == '0' || $cat_id == 0 || $cat_id == '')
			{
				$prefs = $this->read_prefs();
				if ($prefs['cat_id'])
				{
					$this->cat_id = $prefs['cat_id'];
				}
				else
				{
					$this->cat_id = 999;
				}
			}

	/*		$this->oldcat = $this->cat_id;

			if ($this->oldcat != $cat_id)
			{ */
				$data = $this->select_cats_data($this->cat_id);
				$this->taxpercent	= $data['taxpercent'];
				$this->number		= $data['number'];
		//	}

			if(isset($product_id))
			{
				$this->product_id = $product_id;
			}
			if($product_id == '')
			{
				unset($this->product_id);
			}

			if(isset($sort) && !empty($sort))
			{
				$this->sort = $sort;
			}
			if(isset($order) && !empty($order))
			{
				$this->order = $order;
			}
			if(isset($action) && !empty($action))
			{
				$this->action = $action;
			}
			if(isset($status) && !empty($status))
			{
				$this->status = $status;
			}
			if(isset($dist) && !empty($dist))
			{
				$this->dist = $dist;
			}
			if(isset($selection) && !empty($selection))
			{
				$this->selection = $selection;
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$data['oldcat'] = $data['cat_id'];
				$cat_data = $this->select_cats_data($data['cat_id']);
				$data['taxpercent']	= $cat_data['taxpercent'];
				$data['number']		= $cat_data['number'];

				$GLOBALS['phpgw']->session->appsession('session_data','inv',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','inv');

			$this->start		= $data['start'];
			$this->query		= $data['query'];
			$this->sort			= $data['sort'];
			$this->order		= $data['order'];
			$this->action		= $data['action'];
			$this->status		= $data['status'];
			$this->dist			= $data['dist'];
			$this->selection	= $data['selection'];
			$this->cat_id 		= $data['cat_id'];
			$this->oldcat 		= $data['oldcat'];
			$this->taxpercent	= $data['taxpercent'];
			$this->number		= $data['number'];
			if(isset($data['product_id']))
			{
				$this->product_id = $data['product_id'];
			}
		}

		function formatted_cat_list()
		{
			return $this->cats->formatted_list(array('format' => 'select','type' => 'all','selected' => $this->cat_id));
		}

		function return_single_cat()
		{
			return $this->cats->return_single($this->cat_id);
		}

		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == True);
		}

		function get_status_id($sname)
		{
			return $this->soinv->get_status_id($sname);
		}

		function status_list()
		{
			return $this->soinv->status_list();
		}

		function read_status()
		{
			return $this->soinv->read_status();
		}

		function select_status_list($selected = '') 
		{
			$sl = $this->status_list();

			while ($status = each($sl)) 
			{
				$html_status_list .= '<option value="' . $status[0] . '"';
				if ($status[0] == $selected)
				{
					$html_status_list .= ' selected';
				}
				$html_status_list .= '>' . $status[1] . '</option>';
			}
			return $html_status_list;
		}

		function select_dist_list() 
		{
			$cols = array('org_name' => 'org_name');

			$entries = $this->contacts->read($start = '',$offset = '',$cols,$query = '','tid=n',$sort = '',$order = '',$this->account);
			for ($i=0;$i<count($entries);$i++) 
			{
				$html_dist_list .= '<option value="' . $entries[$i]['id'] . '"';
				if ($entries[$i]['id'] == $this->dist)
				{
					$html_dist_list .= ' selected';
				}
				$html_dist_list .= '>'	. $entries[$i]['org_name'] . '</option>';
			}
			return $html_dist_list;
		}

		function return_value($item)
		{
			return $this->soinv->return_value($item);
		}

// selects the tax from products category

		function select_cats_data($cat_id)
		{
			$cat_data = $this->cats->id2name($cat_id,'data');
			$data = unserialize($cat_data);
			$tax = $data['tax'];
			$cat['taxpercent'] = ($data['tax']/100);
			$cat['number'] = $data['number'];
			return $cat;
		}

		function select_tax($cat_id)
		{
			$cat_data = $this->cats->id2name($cat_id,'data');
			$data = unserialize($cat_data);
			$tax = $data['tax'];
			$taxpercent = ($tax/100);
			return $taxpercent;
		}

		function select_number($cat_id)
		{
			$cat_data = $this->cats->id2name($cat_id,'data');
			$data = unserialize($cat_data);

			if ($data['number'])
			{
				return $data['number'];
			}
			else
			{
				return False;
			}
		}

		function read_products()
		{
			switch ($this->selection)
			{
				case 'category':	$object_id = $this->cat_id; break;
				case 'dist':		$object_id = $this->dist; break;
			}

			$pro = $this->soinv->read_products($this->start,True,$this->query,$this->selection,$object_id,$this->sort,$this->order,$this->status);
			$this->total_records = $this->soinv->total_records;
			return $pro;
		}

		function read_single_product()
		{
			return $this->soinv->read_single_product($this->product_id);
		}

		function add_leading_zero($num,$idtype = 'hex') 
		{
			if ($idtype == "hex") 
			{
				$num = hexdec($num);
				$num++;
				$num = dechex($num);
			}
			else
			{
				$num++; 
			}

			if (strlen($num) == 4)
				$return = $num;
			if (strlen($num) == 3)
				$return = "0$num";
			if (strlen($num) == 2)
				$return = "00$num";
			if (strlen($num) == 1)
				$return = "000$num";
			if (strlen($num) == 0)
				$return = "0001";

			return strtoupper($return);
		}

		function create_productid($cat_id) 
		{
			if ($this->select_number($cat_id))
			{
				$number = $this->select_number($cat_id);
				$maxnum = $this->soinv->max_product_number($number);
				$sub = strlen($number);
				$max = $this->add_leading_zero(substr($maxnum,$sub));
				return $number . $max;
			}
			else
			{
				return False;
			}
		}

		function read_rooms($start, $limit = True, $query = '', $filter = '', $sort = '', $order = '')
		{
			return $this->soinv->read_rooms($start,$limit,$query,$filter,$sort,$order);
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
				$s .= '>' . $GLOBALS['phpgw']->strip_html($rooms[$i]['name']);
				$s .= '</option>' . "\n";
			}
			return $s;
		}

		function one_room($id = '')
		{
			return $room;
		}

		function get_stock($product_id = '')
		{
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

		function exists($values)
		{
			return $this->soinv->exists($values);
		}

		function check_values($values)
		{
			if ($values['action'] == 'status')
			{
				if (!$values['status_name'])
				{
					$error[] = lang('Please enter a name !');
				}
				else
				{
					$exists = $this->exists(array('action' => 'status','status_name' => $values['status_name'],
													'status_id' => $values['status_id']));
					if ($exists)
					{
						$error[] = lang('That name has been used already !');
					}
				}
			}
			else
			{
				if (!$values['cat_id'])
				{
    	        	$error[] = lang('Please select a category for that product !');
				}

				if (!$values['choose'])
				{
					if (!$values['num'])
					{
						$error[] = lang('Please enter an ID !');
					}
					else
					{
						$exists = $this->exists(array('action' => 'num','num' => $values['num'],'cat_id' => $values['cat_id'],
														'product_id' => $this->product_id));
						if ($exists)
						{
							$error[] = lang('That ID has been used already !');
						}

						if (strlen($values['num']) > 20)
						{
							$error[] = lang('ID can not exceed 20 characters in length !');
						}
					}
				}

				if (!$values['serial'])
				{
					if ($values['name'])
					{
						$exists = $this->exists(array('action' => 'name','name' => $values['name'],'cat_id' => $values['cat_id'],
														'product_id' => $this->product_id));
						if ($exists)
						{
							$error[] = lang('That name has been used already !');
						}

						if (strlen($values['name']) > 255)
						{
							$error[] = lang('name can not exceed 255 characters in length !');
						}
					}
				}

				if ($values['pmonth'] || $values['pday'] || $values['pyear'])
				{
					if (! checkdate($values['pmonth'],$values['pday'],$values['pyear']))
					{
						$error[] = lang('You have entered an invalid purchase date !');
					}
				}

				if ($values['smonth'] || $values['sday'] || $values['syear'])
				{
					if (! checkdate($values['smonth'],$values['sday'],$values['syear']))
					{
						$error[] = lang('You have entered an invalid selling date !');
					}
				}
			}

			if (is_array($error))
			{
				return $error;
			}
		}

		function get_retail($cat_id,$price)
		{
			$taxpercent = $this->select_tax($cat_id);
			return round($price*(1+$taxpercent),2);			
		}

		function save_product($values)
		{
			if ($values['choose'])
			{
				$values['num'] = $this->create_productid($values['cat_id']);
			}

			if ($values['url'] == 'http://')
			{
				$values['url'] = '';
			}

			if ($values['ftp'] == 'ftp://')
			{
				$values['ftp'] = '';
			}

			if ($values['smonth'] || $values['sday'] || $values['syear'])
			{
				$values['sdate'] = mktime(0,0,0,$values['smonth'], $values['sday'], $values['syear']);
			}

			if ($values['pmonth'] || $values['pday'] || $values['pyear'])
			{
				$values['pdate'] = mktime(0,0,0,$values['pmonth'],$values['pday'],$values['pyear']);
			}

            if (!$values['pdate'])
            {
                $values['pdate'] = time();
            }

			if (!$values['cost'])
			{
				$values['cost'] = 0;
			}

			if (!$values['price'])
			{
				$values['price'] = 0;
			}

			$values['retail'] = $this->get_retail($values['cat_id'],$values['price']);

			if ($this->product_id && $this->product_id != 0)
			{
				$values['product_id'] = $this->product_id;
				$this->soinv->edit_product($values);
			}
			else
			{
				$this->soinv->add_product($values);
			}
			return array('retail' => $values['retail'],'num' => $values['num']);
		}

		function read_prefs()
		{
			$GLOBALS['phpgw']->preferences->read_repository();

			$prefs = array();

			if ($GLOBALS['phpgw_info']['user']['preferences']['inv'])
			{
				$prefs['print_format']	= $GLOBALS['phpgw_info']['user']['preferences']['inv']['print_format'];
				$prefs['abid']			= $GLOBALS['phpgw_info']['user']['preferences']['inv']['abid'];
				$prefs['cat_id']		= $GLOBALS['phpgw_info']['user']['preferences']['inv']['cat_id'];
			}
			return $prefs;
		}

		function save_status($values)
		{
			if ($values['status_id'] && $values['status_id'] != 0)
			{
				$this->soinv->edit_status($values);
			}
			else
			{
				$this->soinv->add_status($values['status_name']);
			}
		}

		function save_prefs($prefs)
		{
			$GLOBALS['phpgw']->preferences->read_repository();

			if ($prefs)
			{
				$GLOBALS['phpgw']->preferences->change('inv','print_format',$prefs['print_format']);
				$GLOBALS['phpgw']->preferences->change('inv','abid',$prefs['abid']);
				$GLOBALS['phpgw']->preferences->change('inv','cat_id',$prefs['cat_id']);
				$GLOBALS['phpgw']->preferences->save_repository(True);
			}
		}

		function check_prefs()
		{
			$prefs = $this->get_prefs();

			if (! isset($prefs['country']) || (! isset($prefs['currency'])))
			{
				$error[] = lang('Please set your global preferences !');
			}

			if (! isset($prefs['abid']) || (! isset($prefs['print_format'])) || (! isset($prefs['cat_id'])))
			{
				$error[] = lang('Please set your preferences for this application !');
			}
			return $error;
		}

		function get_prefs()
		{
			$GLOBALS['phpgw']->preferences->read_repository();

			$prefs = array();

			$prefs['currency']	= $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'];
			$prefs['country']	= $GLOBALS['phpgw_info']['user']['preferences']['common']['country'];

			if ($GLOBALS['phpgw_info']['user']['preferences']['inv'])
			{
				$prefs['abid']			= $GLOBALS['phpgw_info']['user']['preferences']['inv']['abid'];
				$prefs['print_format']	= $GLOBALS['phpgw_info']['user']['preferences']['inv']['print_format'];
				$prefs['cat_id']		= $GLOBALS['phpgw_info']['user']['preferences']['inv']['cat_id'];
			}
			return $prefs;
		}

		function delete($values)
		{
			$this->soinv->delete($values);
		}
	}
?>
