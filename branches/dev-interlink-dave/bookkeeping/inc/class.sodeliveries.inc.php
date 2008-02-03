<?php
	/*******************************************************************\
	* phpGroupWare - Bookkeeping                                        *
	* http://www.phpgroupware.org                                       *
	* This program is part of the GNU project, see http://www.gnu.org/	*
	*                                                                   *
	* Accounting application for the Project Manager                    *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	* -----------------------------------------------                   *
	* Copyright 2000 - 2003 Free Software Foundation, Inc               *
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
	// $Source$

	class sodeliveries
	{
		var $db;

		function sodeliveries()
		{
			$this->db			= $GLOBALS['phpgw']->db;
			$this->db2			= $this->db;
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->soprojects	= CreateObject('projects.soprojects');
		}

		function return_join()
		{
			$dbtype = $GLOBALS['phpgw_info']['server']['db_type'];

			switch ($dbtype)
			{
				case 'pgsql':	$join = ' JOIN '; break;
				case 'mysql':	$join = ' LEFT JOIN '; break;
			}
			return $join;
		}

		function delivery($values,$select)
		{
			$values['delivery_num'] = $this->db->db_addslashes($values['delivery_num']);
			$this->db->query("INSERT INTO phpgw_p_delivery (d_number,project_id,d_date,customer,owner) VALUES ('" . $values['delivery_num'] . "',"
							. intval($values['project_id']) . ',' . time() . ',' . intval($values['customer']) . ',' . $this->account . ')',__LINE__,__FILE__);

			$this->db2->query("SELECT id from phpgw_p_delivery WHERE d_number='" . $values['delivery_num'] . "'",__LINE__,__FILE__);
			$this->db2->next_record();
			$delivery_id = $this->db2->f('id');
			$delivery_id = intval($delivery_id);

			while(is_array($select) && $entry=each($select))
			{
				$this->db->query('INSERT INTO phpgw_p_deliverypos (delivery_id,hours_id) VALUES (' . $delivery_id . ',' . intval($entry[0])
								. ')',__LINE__,__FILE__);
				$this->db2->query("UPDATE phpgw_p_hours set status='closed' WHERE status='done' AND id=" . intval($entry[0]),__LINE__,__FILE__);
				$this->db2->query("UPDATE phpgw_p_hours set dstatus='d' WHERE id=" . intval($entry[0]),__LINE__,__FILE__);
			}
			return $delivery_id;
		}

		function update_delivery($values,$select)
		{
			$values['delivery_id'] = intval($values['delivery_id']);

			$values['delivery_num'] = $this->db->db_addslashes($values['delivery_num']);
			$this->db->query("UPDATE phpgw_p_delivery set d_number='" . $values['delivery_num'] . "',d_date=" . intval($values['date']) . ',customer='
								. intval($values['customer']) . ' where id=' . $values['delivery_id'],__LINE__,__FILE__);

			$this->db2->query('DELETE FROM phpgw_p_deliverypos WHERE delivery_id=' . $values['delivery_id'],__LINE__,__FILE__);

			while(is_array($select) && $entry=each($select))
			{
				$this->db->query('INSERT INTO phpgw_p_deliverypos (delivery_id,hours_id) VALUES (' . $values['delivery_id'] . ','
								. intval($entry[0]) . ')',__LINE__,__FILE__);
				$this->db2->query("UPDATE phpgw_p_hours set status='closed' WHERE status='done' AND id=" . intval($entry[0]),__LINE__,__FILE__);
				$this->db2->query("UPDATE phpgw_p_hours set dstatus='d' WHERE id=" . intval($entry[0]),__LINE__,__FILE__);
			}
		}

		function parent_search($values)
		{
			$project_id = intval($values['project_id']);
			$action		= isset($values['action'])?$values['action']:'mains';
			$table		= isset($values['table'])?$values['table']:'hours';
			$status		= isset($values['status'])?$values['status']:'active';

			switch($action)
			{
				case 'mains':	$type = 'mainandsubs'; break;
				case 'subs':	$type = 'subs'; break;
			}
			$pro_array = $this->soprojects->read_projects(array('type' => $type,'limit' => False,'main' => $project_id,'parent' => $project_id,'column' => 'id', 'status' => $status));

			if(is_array($pro_array))
			{
				switch($table)
				{
					case 'del': $parent_search = ' OR phpgw_p_delivery.project_id in(' . implode(',',$pro_array) . ')'; break;
					default:	$parent_search = ' OR phpgw_p_hours.project_id in(' . implode(',',$pro_array) . ')'; break;
				}
				return $parent_search;
			}
			return False;
		}

		function read_hours($project_id, $action, $status)
		{
			$project_id = intval($project_id);

			$ordermethod = ' order by end_date asc';

			$parent_search = $this->parent_search(array('project_id' => $project_id,'action' => $action,'status' => $status));

			$this->db->query('SELECT phpgw_p_hours.id as id,phpgw_p_hours.hours_descr,phpgw_p_activities.descr,phpgw_p_hours.status,'
							. 'phpgw_p_hours.start_date,phpgw_p_hours.minutes,phpgw_p_hours.minperae FROM phpgw_p_hours' . $this->return_join()
							. "phpgw_p_activities ON phpgw_p_hours.activity_id=phpgw_p_activities.id WHERE (phpgw_p_hours.dstatus='o' "
							. "AND phpgw_p_hours.status != 'open') AND (phpgw_p_hours.project_id=" . $project_id . $parent_search
							. ')' . $ordermethod,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$hours[] = array
				(
					'hours_id'		=> $this->db->f('id'),
					'hours_descr'	=> $this->db->f('hours_descr'),
					'act_descr'		=> $this->db->f('descr'),
					'status'		=> $this->db->f('status'),
					'sdate'			=> $this->db->f('start_date'),
					'edate'			=> $this->db->f('end_date'),
					'minutes'		=> $this->db->f('minutes'),
					'minperae'		=> $this->db->f('minperae')
				);
			}
			return $hours;
		}

		function read_delivery_hours($project_id, $delivery_id, $action, $status)
		{
			$project_id		= intval($project_id);
			$delivery_id	= intval($delivery_id);

			$ordermethod = ' order by end_date asc';

			$parent_search = $this->parent_search(array('project_id' => $project_id,'action' => $action, 'status' => $status));

			$this->db->query('SELECT phpgw_p_hours.id as id,phpgw_p_hours.hours_descr,phpgw_p_activities.descr,phpgw_p_hours.status,'
							. 'phpgw_p_hours.start_date,phpgw_p_hours.minutes,phpgw_p_hours.minperae FROM phpgw_p_hours' . $this->return_join()
							. 'phpgw_p_activities ON phpgw_p_hours.activity_id=phpgw_p_activities.id' . $this->return_join() . 'phpgw_p_deliverypos '
							. 'ON phpgw_p_hours.id=phpgw_p_deliverypos.hours_id WHERE (phpgw_p_hours.project_id=' . $project_id
							. $parent_search  . ') AND phpgw_p_deliverypos.delivery_id=' . $delivery_id . $ordermethod,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$hours[] = array
				(
					'hours_id'		=> $this->db->f('id'),
					'hours_descr'	=> $this->db->f('hours_descr'),
					'act_descr'		=> $this->db->f('descr'),
					'status'		=> $this->db->f('status'),
					'sdate'			=> $this->db->f('start_date'),
					'edate'			=> $this->db->f('end_date'),
					'minutes'		=> $this->db->f('minutes'),
					'minperae'		=> $this->db->f('minperae')
				);
			}
			return $hours;
		}


		function read_deliveries($values)
		{
			$project_id = intval($values['project_id']);
			$sort		= (isset($values['sort'])?$values['sort']:'ASC');
			$order		= $values['order'];
			$query		= $values['query'];
			$owner		= ($values['owner'] == 'yes'?True:False);
			$limit		= (isset($values['limit'])?$values['limit']:True);
			$start		= intval($values['start']);
			$status		= isset($values['status'])?$values['status']:'active';
			$action		= isset($values['action'])?$values['action']:'mains';

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by d_date asc';
			}

			if ($query)
			{
				$querymethod = " AND (d_number like '%$query%' OR title like '%$query%')";
			}

			if ($owner)
			{
				$acl_select = ' AND phpgw_p_delivery.owner=' . $this->account;
			}

			if ($project_id > 0)
			{
				$parent_search = $this->parent_search(array('project_id' => $project_id,'action' => $action, 'status' => $status,'table' => 'del'));

				$sql = 'SELECT phpgw_p_delivery.id as id,d_number,title,d_date,phpgw_p_delivery.project_id,phpgw_p_delivery.customer '
					. 'FROM phpgw_p_delivery,phpgw_p_projects WHERE (phpgw_p_delivery.project_id=' . $project_id . $parent_search
					. ') AND phpgw_p_delivery.project_id=phpgw_p_projects.id';
			}
    		else
			{
				$sql = 'SELECT phpgw_p_delivery.id as id,d_number,title,d_date,phpgw_p_delivery.project_id,phpgw_p_delivery.customer '
					. 'FROM phpgw_p_delivery,phpgw_p_projects WHERE phpgw_p_delivery.project_id=phpgw_p_projects.id';
			}

			$this->db2->query($sql . $acl_select,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

			if ($limit)
			{
				$this->db->limit_query($sql  . $acl_select . $querymethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql  . $acl_select . $querymethod,__LINE__,__FILE__);
			}

			while ($this->db->next_record())
			{
				$del[] = array
				(
					'delivery_id'	=> $this->db->f('id'),
					'project_id'	=> $this->db->f('project_id'),
					'delivery_num'	=> $this->db->f('d_number'),
					'title'			=> $this->db->f('title'),
					'date'			=> $this->db->f('d_date'),
					'customer'		=> $this->db->f('customer')
				);
			}
			return $del;
		}

		function read_single_delivery($delivery_id)
		{
			$this->db->query('SELECT phpgw_p_delivery.customer,d_number,phpgw_p_delivery.project_id,d_date,title,p_number '
							. 'FROM phpgw_p_delivery,phpgw_p_projects WHERE phpgw_p_delivery.id=' . intval($delivery_id)
							. ' AND phpgw_p_delivery.project_id=phpgw_p_projects.id',__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$del['date']			= $this->db->f('date');
				$del['delivery_num']	= $this->db->f('d_number');
				$del['title']			= $this->db->f('title');
				$del['customer']		= $this->db->f('customer');
				$del['project_id']		= $this->db->f('project_id');
				$del['project_num']		= $this->db->f('p_number');
			}
			return $del;
		}

		function exists($values)
		{
			$values['delivery_id'] = intval($values['delivery_id']);

			if ($values['delivery_id'] && ($values['delivery_id'] != 0))
			{
				$editexists = ' and id !=' . $values['delivery_id'];
			}
			$this->db->query("select count(*) from phpgw_p_delivery where d_number='" . $values['delivery_num'] . "'" . $editexists,__LINE__,__FILE__);

			$this->db->next_record();

			if ($this->db->f(0))
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		function read_delivery_pos($delivery_id)
		{
			$this->db->query('SELECT phpgw_p_hours.hours_descr,phpgw_p_hours.minperae,phpgw_p_hours.minutes,'
							. 'phpgw_p_activities.descr,phpgw_p_hours.start_date, phpgw_p_hours.end_date FROM phpgw_p_hours,phpgw_p_activities,'
							. 'phpgw_p_deliverypos WHERE phpgw_p_deliverypos.hours_id=phpgw_p_hours.id AND phpgw_p_deliverypos.delivery_id='
							. intval($delivery_id) . ' AND phpgw_p_hours.activity_id=phpgw_p_activities.id',__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$hours[] = array
				(
					'hours_descr'	=> $this->db->f('hours_descr'),
					'act_descr'		=> $this->db->f('descr'),
					'sdate'			=> $this->db->f('start_date'),
					'edate'			=> $this->db->f('end_date'),
					'minutes'		=> $this->db->f('minutes'),
					'minperae'		=> $this->db->f('minperae')
				);
			}
			return $hours;
		}
	}
?>
