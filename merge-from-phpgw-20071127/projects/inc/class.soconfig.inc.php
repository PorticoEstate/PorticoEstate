<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: class.soconfig.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	* $Source: /sources/phpgroupware/projects/inc/class.soconfig.inc.php,v $
	*/

	class soconfig
	{
		var $db;
		var $db2;
		var $currency;
		var $locations;
		var $location_idents;

		function soconfig()
		{
			$this->db		= $GLOBALS['phpgw']->db;
			$this->db2		= $this->db;
			$this->currency = $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'];
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function get_site_config($params = 0)
		{
			$default = isset($params['default']) ? $params['default'] : true;
			$helpmsg = isset($params['helpmsg']) ? $params['helpmsg'] : false;

			$this->config = CreateObject('phpgwapi.config','projects');
			$this->config->read_repository();

			if ($this->config->config_data)
			{
				$items = $this->config->config_data;

				if($default)
				{
					$items['hwday']				= isset($items['hwday'])?$items['hwday']:8;
					$items['accounting']		= isset($items['accounting'])?$items['accounting']:'own';
					$items['activity_bill']		= isset($items['activity_bill'])?$items['activity_bill']:'h';
					$items['dateprevious']		= isset($items['dateprevious'])?$items['dateprevious']:'no';
					$items['hoursbookingday']	= isset($items['hoursbookingday'])?$items['hoursbookingday']:'no';
					$items['hoursbookingnull']	= isset($items['hoursbookingnull'])?$items['hoursbookingnull']:'no';
					$items['projectnr']			= isset($items['projectnr'])?$items['projectnr']:'generate';
				}
				$items['proid_help_msg'] = ($helpmsg==True?$items['proid_help_msg']:'');
				return $items;
			}
			return False;
		}

		function bill_lang()
		{
			$config = $this->get_site_config();

			switch ($config['activity_bill'])
			{
				case 'wu':	$l = lang('per workunit'); break;
				default:	$l = lang('per hour'); break;
			}
			return $l;
		}

		function activities_list($project_id = '',$billable = False)
		{
			if ($billable)
			{
				$bill_filter = " AND billable='Y'";
			}
			else
			{
				$bill_filter = " AND billable='N'";
			}

			$this->db->query('SELECT phpgw_p_activities.id,a_number,descr,billperae,activity_id from phpgw_p_activities,phpgw_p_projectactivities '
							. 'WHERE phpgw_p_projectactivities.project_id=' . $project_id . ' AND phpgw_p_activities.id='
							. 'phpgw_p_projectactivities.activity_id' . $bill_filter,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$act[] = array
				(
					'num'		=> $this->db->f('a_number'),
					'descr'		=> $this->db->f('descr'),
					'billperae'	=> $this->db->f('billperae')
				);
			}
			return $act;
		}

		function select_activities_list($project_id = '',$billable = False)
		{
			if ($billable)
			{
				$bill_filter = " AND billable='Y'";
			}
			else
			{
				$bill_filter = " AND billable='N'";
			}

			$this->db2->query('SELECT activity_id from phpgw_p_projectactivities WHERE project_id=' . intval($project_id) . $bill_filter,__LINE__,__FILE__);
			while ($this->db2->next_record())
			{
				$selected[] = array('activity_id' => $this->db2->f('activity_id'));
			}

			$this->db->query('SELECT id,a_number,descr,billperae FROM phpgw_p_activities ORDER BY descr asc');
			while ($this->db->next_record())
			{
				$activities_list .= '<option value="' . $this->db->f('id') . '"';
				for ($i=0;$i<count($selected);$i++)
				{
					if($selected[$i]['activity_id'] == $this->db->f('id'))
					{
						$activities_list .= ' selected';
					}
				}
				$activities_list .= '>' . $GLOBALS['phpgw']->strip_html($this->db->f('descr')) . ' ['
										. $GLOBALS['phpgw']->strip_html($this->db->f('a_number')) . ']';
				if($billable)
				{
					$activities_list .= ' ' . $this->currency . ' ' . $this->db->f('billperae') . ' ' . $this->bill_lang();
				}

				$activities_list .= '</option>' . "\n";
			}
			return $activities_list;
		}

		function select_pro_activities($project_id = '', $pro_parent, $billable = False)
		{
			if ($billable)
			{
				$bill_filter = " AND billable='Y'";
			}
			else
			{
				$bill_filter = " AND billable='N'";
			}

			$this->db2->query('SELECT activity_id from phpgw_p_projectactivities WHERE project_id=' . intval($project_id) . $bill_filter,__LINE__,__FILE__);
			while ($this->db2->next_record())
			{
				$selected[] = array('activity_id' => $this->db2->f('activity_id'));
			}

			$this->db->query('SELECT a.id, a.a_number, a.descr, a.billperae, pa.activity_id FROM phpgw_p_activities as a, phpgw_p_projectactivities as pa'
							. ' WHERE pa.project_id=' . intval($pro_parent) . $bill_filter . ' AND pa.activity_id=a.id ORDER BY a.descr asc');
			while ($this->db->next_record())
			{
				$activities_list .= '<option value="' . $this->db->f('id') . '"';
				for ($i=0;$i<count($selected);$i++)
				{
					if($selected[$i]['activity_id'] == $this->db->f('id'))
					{
						$activities_list .= ' selected';
					}
				}

				if (! is_array($selected))
				{
					$activities_list .= ' selected';
				}

				$activities_list .= '>' . $GLOBALS['phpgw']->strip_html($this->db->f('descr')) . ' ['
										. $GLOBALS['phpgw']->strip_html($this->db->f('a_number')) . ']';

				if($billable)
				{
					$activities_list .= ' ' . $this->currency . ' ' . $this->db->f('billperae') . ' ' . $this->bill_lang();
				}

				$activities_list .= '</option>' . "\n";
			}
			return $activities_list;
		}

		function select_hours_activities($project_id, $activity = '')
		{
			$this->db->query('SELECT activity_id,a_number,descr,billperae,billable FROM phpgw_p_projectactivities,phpgw_p_activities WHERE project_id ='
							. intval($project_id) . ' AND phpgw_p_projectactivities.activity_id=phpgw_p_activities.id order by descr asc',__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$hours_act .= '<option value="' . $this->db->f('activity_id') . '"';
				if($this->db->f('activity_id') == intval($activity))
				{
					$hours_act .= ' selected';
				}
				$hours_act .= '>' . $GLOBALS['phpgw']->strip_html($this->db->f('descr')) . ' ['
									. $GLOBALS['phpgw']->strip_html($this->db->f('a_number')) . ']';

				if($this->db->f('billable') == 'Y')
				{
					$hours_act .= ' ' . $this->currency . ' ' . $this->db->f('billperae') . ' ' . $this->bill_lang();
				}
				$hours_act .= '</option>' . "\n";
			}
			return $hours_act;
		}

		function return_value($action,$pro_id)
		{
			$pro_id = intval($pro_id);
			switch($action)
			{
				case 'act':
					$sql = 'SELECT a_number,descr from phpgw_p_activities where id=' . $pro_id;

					break;
				case 'acc':
					$sql = 'SELECT accounting,sdate,edate from phpgw_p_projectmembers where account_id=' . $pro_id;
					break;
			}
			$this->db->query($sql,__LINE__,__FILE__);
			while($this->db->next_record())
			{
				switch($action)
				{
					case 'act':
						$bla = $GLOBALS['phpgw']->strip_html($this->db->f('descr')) . ' [' . $GLOBALS['phpgw']->strip_html($this->db->f('a_number')) . ']';
						break;
					case 'acc':
						$bla[] = array
						(
							'factor'	=> $this->db->f('accounting'),
							'sdate'		=> $this->db->f('sdate'),
							'edate'		=> $this->db->f('edate')
						);
						break;
				}
				return $bla;
			}
			return False;
		}

		function exists($values)
		{
			$pa_id	= isset($values['pa_id'])?$values['pa_id']:0;
			$number	= isset($values['number'])?$values['number']:'';
			$action = isset($values['action'])?$values['action']:'activitiy';
			$check	= isset($values['check'])?$values['check']:'';

			$pa_id = intval($pa_id);

			switch ($action)
			{
				case 'activity'		: $p_table = 'phpgw_p_activities'; $column = "a_number='" . $number . "'";break;
				case 'accounting'	: $p_table = 'phpgw_p_projectmembers'; $column = 'account_id=' . $pa_id; break;
			}

			if ($check == 'number' && $pa_id > 0)
			{
				$additon = ' and id !=' . $pa_id;
			}
			else
			{
				$additon = " and type='accounting'";
			}

			$this->db->query("select count(*) from $p_table where $column" . $additon,__LINE__,__FILE__);

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

		function read_admins( $action = 'pad',$type = '' )
		{
			switch($type)
			{
				case 'user':
					switch($action)
					{
						case 'pmanager':	$filter = "type='ma'"; break;
						case 'psale':		$filter = "type='sa'"; break;
						case 'pad':			$filter = "type='aa'"; break;
					}
					break;
				case 'group':
					switch($action)
					{
						case 'pmanager':	$filter = "type='mg'"; break;
						case 'psale':		$filter = "type='sg'"; break;
						case 'pad':			$filter = "type='ag'"; break;
					}
					break;
				case 'all': $filter = "(type != 'accounting' AND type != 'role')"; break;
				default:
					switch($action)
					{
						case 'pmanager':	$filter = "type='ma' or type='mg'"; break;
						case 'psale':		$filter = "type='sa' or type='sg'"; break;
						case 'pad':			$filter = "type='aa' or type='ag'"; break;
					}
					break;
			}

			$sql = 'select account_id,type from phpgw_p_projectmembers WHERE ' . $filter;
			$this->db->query($sql);
			$this->total_records = $this->db->num_rows();

			// TODO: Finn
			$admins = array();

			while( $this->db->next_record() )
			{
				$admins = array
				(
					'account_id' => $this->db->f('account_id'),
					'type' => $this->db->f('type')
				);
			}

			return $admins;
		}

		function isprojectadmin( $action = 'pad' )
		{
			$admin_groups = $GLOBALS['phpgw']->accounts->membership($this->account);
			$admins = $this->read_admins($action);

			//_debug_array($admins);

			for ( $i = 0; $i < count($admins); $i++ )
			{
				switch( $action )
				{
					case 'pmanager':
						$type_a = 'ma';
						$type_g = 'mg';
						break;
					case 'psale':
						$type_a = 'sa';
						$type_g = 'sg';
						break;
					default:
						$type_a = 'aa';
						$type_g = 'ag';
						break;
				}

				if( $admins[$i]['type'] == $type_a && $admins[$i]['account_id'] == $this->account )
				{
					return true;
				}
				elseif( $admins[$i]['type'] == $type_g )
				{
					if ( is_array($admin_groups) )
					{
						for( $j = 0; $j < count($admin_groups); $j++ )
						{
							if ( $admin_groups[$j]['account_id'] == $admins[$i]['account_id'] )
							{
								return true;
							}
						}
					}
				}
				else
				{
					return false;
				}
			}
		}

		function edit_admins( $action,$users = '', $groups = '' )
		{
			switch( $action )
			{
				case 'psale':		$filter = "sa' OR type='sg"; break;
				case 'pmanager':	$filter = "ma' OR type='mg"; break;
				default:			$filter = "aa' OR type='ag"; break;
			}

			$this->db->query("DELETE from phpgw_p_projectmembers WHERE type='" . $filter . "'",__LINE__,__FILE__);

			if ( is_array($users) )
			{
				switch( $action )
				{
					case 'psale':		$type = 'sa'; break;
					case 'pmanager':	$type = 'ma'; break;
					default:			$type = 'aa'; break;
				}

				while( $activ = each($users) )
				{
					$this->db->query('insert into phpgw_p_projectmembers (project_id, account_id,type) values (0,' . $activ[1] . ",'" . $type . "')",__LINE__,__FILE__);
				}
			}

			if ( is_array($groups) )
			{
				switch( $action )
				{
					case 'psale':
						$type = 'sg';
						break;
					case 'pmanager':
						$type = 'mg';
						break;
					default:
						$type = 'ag';
						break;
				}

				while( $activ = each($groups) )
				{
					$this->db->query('insert into phpgw_p_projectmembers (project_id, account_id,type) values (0,' . $activ[1] . ",'" . $type . "')", __LINE__, __FILE__);
				}
			}
		}

		function read_activities( $values )
		{
			$start	= isset($values['start']) ? $values['start'] : 0;
			$limit	= isset($values['limit']) ? $values['limit'] : true;
			$sort	= isset($values['sort']) ? $values['sort'] : 'ASC';
			$order	= isset($values['order']) ? $values['order'] : 'a_number';
			$cat_id	= isset($values['cat_id']) ? $values['cat_id'] : 0;

			$query	= $this->db->db_addslashes($values['query']);

			$ordermethod = " order by $order $sort";

			if ( $query )
			{
				$filtermethod = " where (descr like '%$query%' or a_number like '%$query%' or minperae like '%$query%' or billperae like '%$query%')";

				if ( $cat_id > 0 )
				{
					$filtermethod .= ' AND category=' . $cat_id;
				}
			}
			else
			{
				if ( $cat_id > 0 )
				{
					$filtermethod = ' WHERE category=' . $cat_id;
				}
			}

			$sql = 'select * from phpgw_p_activities' . $filtermethod;
			$this->db2->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db2->num_rows();

			if ( $limit )
			{
				$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod, __LINE__, __FILE__);
			}

			$i = 0;
			while ( $this->db->next_record() )
			{
				$act[$i]['activity_id']	= $this->db->f('id');
				$act[$i]['cat']			= $this->db->f('category');
				$act[$i]['number']		= $this->db->f('a_number');
				$act[$i]['descr']		= $this->db->f('descr');
				$act[$i]['remarkreq']	= $this->db->f('remarkreq');
				$act[$i]['billperae']	= $this->db->f('billperae');
				$act[$i]['minperae']	= $this->db->f('minperae');

				$i++;
			}
			return $act;
		}

		function read_single_activity( $activity_id )
		{
			$this->db->query('SELECT * from phpgw_p_activities WHERE id=' . intval($activity_id), __LINE__, __FILE__);

			if ( $this->db->next_record() )
			{
				$act['activity_id']	= $this->db->f('id');
				$act['cat']			= $this->db->f('category');
				$act['number']		= $this->db->f('a_number');
				$act['descr']		= $this->db->f('descr');
				$act['remarkreq']	= $this->db->f('remarkreq');
				$act['billperae']	= $this->db->f('billperae');
				$act['minperae']	= $this->db->f('minperae');

				return $act;
			}
		}

		function add_activity( $values )
		{
			$values['number']		= $this->db->db_addslashes($values['number']);
			$values['descr'] 		= $this->db->db_addslashes($values['descr']);
			$values['billperae']	= $values['billperae'] + 0.0;

			$this->db->query("insert into phpgw_p_activities (a_number,category,descr,remarkreq,billperae,minperae) values ('"
							. $values['number'] . "'," . intval($values['cat']) . ",'" . $values['descr'] . "','" . $values['remarkreq'] . "',"
							. $values['billperae'] . ','  . intval($values['minperae']) . ')',__LINE__,__FILE__);
		}

		function edit_activity( $values )
		{
			$values['number']		= $this->db->db_addslashes($values['number']);
			$values['descr']		= $this->db->db_addslashes($values['descr']);
			$values['billperae']	= $values['billperae'] + 0.0;

			$this->db->query("update phpgw_p_activities set a_number='" . $values['number'] . "', category=" . intval($values['cat'])
							. ",remarkreq='" . $values['remarkreq'] . "',descr='" . $values['descr'] . "',billperae="
							. $values['billperae'] . ',minperae=' . intval($values['minperae']) . ' where id=' . intval($values['activity_id']),__LINE__,__FILE__);
		}

		function delete_pa( $action, $pa_id )
		{
			$pa_id = intval($pa_id);

			switch ( $action )
			{
				case 'activity':
					$p_table = 'phpgw_p_activities';
					$p_column = 'id';
					break;
				case 'role':
					$p_table = 'phpgw_p_roles';
					$p_column = 'role_id';
					break;
				case 'emp_role':
				case 'accounting':
					$p_table = 'phpgw_p_projectmembers';
					$p_column = 'id';
					break;
				case 'charge':
					$p_table = 'phpgw_p_surcharges';
					$p_column = 'charge_id';
					break;
			}

			$this->db->query("DELETE from $p_table where $p_column=" . $pa_id, __LINE__, __FILE__);

			if ( $action == 'activity' )
			{
				$this->db->query('DELETE from phpgw_p_projectactivities where activity_id=' . $pa_id,__LINE__,__FILE__);
			}
		}

		function db2emps()
		{
			while( $this->db->next_record() )
			{
				$emps[] = array
				(
					'id'				=> $this->db->f('id'),
					'account_id'		=> $this->db->f('account_id'),
					'accounting'		=> $this->db->f('accounting'),
					'd_accounting'		=> $this->db->f('d_accounting'),
					'sdate'				=> $this->db->f('sdate'),
					'edate'				=> $this->db->f('edate'),
					'weekly_workhours'	=> $this->db->f('weekly_workhours'),
					'cost_centre'		=> $this->db->f('cost_centre'),
					'location_id'		=> $this->db->f('location_id')
				);
			}

			return $emps;
		}

		function read_employees( $values )
		{
			$start		= intval($values['start']);
			$limit		= isset($values['limit']) ? $values['limit'] : false;
			$sort		= isset($values['sort']) && $values['sort'] ? $values['sort'] : 'ASC';
			$order		= isset($values['order']) && $values['order'] ? $values['order'] : 'sdate';
			$query		= $this->db->db_addslashes($values['query']);
			$account_id	= intval($values['account_id']);
			$id			= intval($values['id']);
			$date		= intval($values['date']);

			$ordermethod = ' order by ' . ($order!='account_id'?'account_id,' . $order:$order) . ' ' . $sort;

			if( $account_id > 0 )
			{
				$acc_select = ' and account_id=' . $account_id;
			}

			if( $id > 0 )
			{
				$id_select = ' and id !=' . $id;
			}

			if ( $date )
			{
				$date_select = " and sdate <= ".$date." and edate >= ".$date;
			}

			$sql = "SELECT * from phpgw_p_projectmembers WHERE type='accounting'" . $id_select . $date_select . $querymethod;

			if( $limit )
			{
				$this->db2->query($sql,__LINE__,__FILE__);
				$this->total_records = $this->db2->num_rows();
				$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__);
			}
			else
			{
				$this->db->query($sql . $acc_select . $ordermethod, __LINE__, __FILE__);
			}

			return $this->db2emps();
		}

		function save_accounting_factor( $values )
		{
			//$exists = $this->exists(array('action' => 'accounting','pa_id' => $values['account_id']));
			$values['id']           = intval($values['id']);
			$values['accounting']   = $values['accounting'] + 0.0;
			$values['d_accounting']	= $values['d_accounting'] + 0.0;
			$values['location_id']  = intval($values['location_id']);

			if( $values['id'] > 0 )
			{
				$this->db->query('UPDATE phpgw_p_projectmembers set accounting=' . $values['accounting'] . ', d_accounting=' . $values['d_accounting']. ', sdate='
								. intval($values['sdate']) . ', edate=' . intval($values['edate'])
								. ', weekly_workhours=' . $values['weekly_workhours'] . ', cost_centre=' . $values['cost_centre'] . ', location_id='.$values['location_id']
								. ' where account_id=' . intval($values['account_id'])
								. " and type='accounting' and id=" . $values['id'],__LINE__,__FILE__);
			}
			else
			{
				$this->db->query('INSERT into phpgw_p_projectmembers (account_id,type,accounting,d_accounting,sdate,edate,weekly_workhours,cost_centre,location_id) values(' . intval($values['account_id'])
								. ",'accounting'," . $values['accounting'] . ',' . $values['d_accounting'] . ',' . intval($values['sdate']) . ','
								. intval($values['edate'])  . ',' . $values['weekly_workhours'] . ',' . $values['cost_centre'] . ',' . $values['location_id']
								. ')',__LINE__,__FILE__);
			}
		}

		function read_single_afactor( $id = 0 )
		{
			$this->db->query('SELECT * from phpgw_p_projectmembers WHERE id=' . intval($id),__LINE__,__FILE__);
			list($emp) = $this->db2emps();
			return $emp;
		}

		function list_roles( $values )
		{
			$start	= intval($values['start']);
			$limit	= isset($values['limit']) ? $values['limit'] : true;
			$sort	= isset($values['sort']) ? $values['sort'] : 'ASC';
			$order	= isset($values['order']) ? $values['order'] : 'role_name';
			$query	= $this->db->db_addslashes($values['query']);

			$ordermethod = " order by role_name $sort";

			if ( $query )
			{
				$querymethod = " WHERE (role_name like '%$query%') ";
			}

			$sql = 'SELECT * from phpgw_p_roles' . $querymethod;

			if ( $limit )
			{
				$this->db2->query($sql,__LINE__,__FILE__);
				$this->total_records = $this->db2->num_rows();
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			while ( $this->db->next_record() )
			{
				$roles[] = array
				(
					'role_id'	=> $this->db->f('role_id'),
					'role_name'	=> $this->db->f('role_name')
				);
			}

			return $roles;
		}

		function save_role( $role_name )
		{
			$role_name = $this->db->db_addslashes($role_name);
			$this->db->query("INSERT into phpgw_p_roles (role_name) values ('" . $role_name . "')",__LINE__,__FILE__);
		}

		function list_events( $type = '' )
		{
			if( $type )
			{
				$type_select = " where event_type='$type'";
			}

			$this->db->query('SELECT * from phpgw_p_events ' . $type_select . 'order by event_type asc',__LINE__,__FILE__);

			while( $this->db->next_record() )
			{
				$events[] = array
				(
					'event_id'		=> $this->db->f('event_id'),
					'event_name'	=> $this->db->f('event_name'),
					'event_type'	=> $this->db->f('event_type'),
					'event_extra'	=> $this->db->f('event_extra')
				);
			}

			return $events;
		}

		function save_event( $values )
		{
			$this->db->query('UPDATE phpgw_p_events set event_extra=' . intval($values['limit']) . ' where event_id=' . intval($values['event_id_limit']),__LINE__,__FILE__);
			$this->db->query('UPDATE phpgw_p_events set event_extra=' . intval($values['percent']) . ' where event_id=' . intval($values['event_id_percent']),__LINE__,__FILE__);
		}

		function get_event_extra( $event_name )
		{
			$this->db->query('SELECT event_extra from phpgw_p_events where event_name=' . "'" . $event_name . "'",__LINE__,__FILE__);
			$this->db->next_record();

			return $this->db->f(0);
		}

		function list_surcharges( $charge_id = 0 )
		{
			$charge_id = intval($charge_id);

			if( $charge_id > 0 )
			{
				$select = ' where charge_id=' . $charge_id;
			}
			else
			{
				$select = ' order by charge_name asc';
			}

			$this->db->query('SELECT * from phpgw_p_surcharges' . $select, __LINE__, __FILE__);

			while( $this->db->next_record() )
			{
				$charges[] = array
				(
					'charge_id'			=> $this->db->f('charge_id'),
					'charge_name'		=> $this->db->f('charge_name'),
					'charge_percent'	=> $this->db->f('charge_percent')
				);
			}

			return $charges;
		}

		function save_surcharge( $values )
		{
			$values['charge_id']		= intval($values['charge_id']);
			$values['charge_percent']	= $values['charge_percent'] + 0.0;

			if( $values['charge_id'] > 0 )
			{
				$this->db->query('UPDATE phpgw_p_surcharges set charge_name=' . "'" . $values['charge_name'] . "', charge_percent=" . $values['charge_percent']
								. ' where charge_id=' . $values['charge_id'],__LINE__,__FILE__);
			}
			else
			{
				$this->db->query('INSERT into phpgw_p_surcharges (charge_name,charge_percent) values(' . "'" . $values['charge_name'] . "',"
								. $values['charge_percent'] . ')',__LINE__,__FILE__);
			}
		}

		function save_location( $values )
		{
			$values['location_id'] = intval($values['location_id']);

			if( $values['location_id'] > 0 )
			{
				$sql = "UPDATE phpgw_p_locations SET location_name='" . $values['location_name'] . "', location_ident='" . $values['location_ident'] . "', location_custnum='".$values['location_custnum']."' WHERE location_id=".$values['location_id'];
			}
			else
			{
				$sql = "INSERT INTO phpgw_p_locations (location_name, location_ident, location_custnum) VALUES ('" . $values['location_name'] . "', '" . $values['location_ident'] . "', '" . $values['location_custnum'] . "')";
			}

			$this->db->query($sql, __LINE__, __FILE__);
		}

		function get_locations()
		{
			$locations	= array();
			$sql		= 'SELECT * FROM phpgw_p_locations';

			$this->db->query($sql, __LINE__, __FILE__);

			while( $this->db->next_record() )
			{
				$location_id				= $this->db->f('location_id');
				$locations[$location_id]	= array
				(
					'location_id'      => $location_id,
					'location_name'    => $this->db->f('location_name'),
					'location_ident'   => $this->db->f('location_ident'),
					'location_custnum' => $this->db->f('location_custnum')
				);
			}

			return $locations;
		}

		function get_single_location( $location_id )
		{
			$location = array();

			if( $location_id > 0 )
			{
				$this->db->query('SELECT * FROM phpgw_p_locations WHERE location_id = ' . $location_id, __LINE__, __FILE__);
				if($this->db->next_record())
				{
					$location = array
					(
						'location_id'      => $this->db->f('location_id'),
						'location_name'    => $this->db->f('location_name'),
						'location_ident'   => $this->db->f('location_ident'),
						'location_custnum' => $this->db->f('location_custnum')
					);
				}
			}

			return $location;
		}

		function get_location_for_ident( $location_ident )
		{
			$location = array();

			if( $location_ident )
			{
				$sql = "SELECT * FROM phpgw_p_locations WHERE location_ident='".$location_ident."'";
				$this->db->query($sql, __LINE__, __FILE__);

				if( $this->db->next_record() )
				{
					$location = array
					(
						'location_id'      => $this->db->f('location_id'),
						'location_name'    => $this->db->f('location_name'),
						'location_ident'   => $this->db->f('location_ident'),
						'location_custnum' => $this->db->f('location_custnum')
					);
				}
			}

			return $location;
		}

		function delete_location( $location_id )
		{
			$location_id = intval($location_id);

			if( $location_id > 0 )
			{
				$sql = 'DELETE FROM phpgw_p_locations WHERE location_id='.$location_id;
				$this->db->query($sql, __LINE__, __FILE__);
			}
		}

	}
?>
