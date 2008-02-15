<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id$
	* $Source: /sources/phpgroupware/projects/inc/class.soprojecthours.inc.php,v $
	*/

	class soprojecthours
	{
		var $db;
		var $db2;
		var $account;

		function soprojecthours()
		{
			$this->db			= $GLOBALS['phpgw']->db;
			$this->db2			= $this->db;
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->column_array = array();
		}

		function db2hours($column = False)
		{
			$i = 0;
			$hours = array();
			while ($this->db->next_record())
			{
				if($column)
				{
					$hours[$i] = array();
					for($k=0;$k<count($this->column_array);++$k)
					{
						$hours[$i][$this->column_array[$k]] = $this->db->f($this->column_array[$k]);
					}
					++$i;
				}
				else
				{
					$hours[] = array
					(
						'hours_id'		=> $this->db->f('id'),
						'project_id'	=> $this->db->f('project_id'),
						'pro_parent'	=> $this->db->f('pro_parent'),
						'pro_main'		=> $this->db->f('pro_main'),
						'hours_descr'	=> $this->db->f('hours_descr'),
						'status'		=> $this->db->f('status'),
						'minutes'		=> $this->db->f('minutes'),
						'sdate'			=> $this->db->f('start_date'),
						'edate'			=> $this->db->f('end_date'),
						'employee'		=> $this->db->f('employee'),
						'activity_id'	=> $this->db->f('activity_id'),
						'remark'		=> $this->db->f('remark'),
						'billable'		=> $this->db->f('billable'),
						'km_distance'	=> $this->db->f('km_distance'),
						't_journey'		=> $this->db->f('t_journey'),
						'booked'		=> $this->db->f('booked'),
						'surcharge'		=> $this->db->f('surcharge')
					);
				}
			}
			return $hours;
		}

		function read_hours($values)
		{		
			$start         = intval($values['start']);			
			$limit         = $values['limit']?$values['limit']:false;
			$filter        = $values['filter']?$values['filter']:'none';
			$sort          = $values['sort']?$values['sort']:'ASC';
			$order         = $values['order']?$values['order']:'start_date';
			$status        = $values['status']?$values['status']:'all';
			$project_id	   = intval($values['project_id']);			
			$query         = $this->db->db_addslashes($values['query']);
			$column        = (isset($values['column'])?$values['column'] : false);
			$parent_select = isset($values['parent_select']) ? true : false;
			$period_start  = isset($values['period_start']) ? intval($values['period_start']) : 0;
			$period_end    = isset($values['period_end']) ? intval($values['period_end']) : 0;
			
			if(isset($values['employee']))
			{
				$employee = $values['employee'];
			}
			elseif(isset($this->employee))
			{
				$employee = $this->employee;
			}
			else
			{
				$employee = $this->account;
			}

			//_debug_array($values);

			$ordermethod = " order by $order $sort";

			$filtermethod = ($parent_select?' pro_parent=' . $project_id:' project_id=' . $project_id);

			if ($status != 'all')
			{
				$filtermethod .= " AND status='$status'";
			}

			switch ($filter)
			{
				case 'yours':
					$filtermethod .= ' AND employee=' . $this->account;
				break;
				case 'employee':
					$filtermethod .= ' AND employee=' . $employee; //dirty hack - filter should be more flexible: filter[field] and filter[value]
				break;
			}

			if ($query)
			{
				$querymethod = " AND (remark like '%$query%' OR minutes like '%$query%' OR hours_descr like '%$query%')";
			}
			else
			{
				$querymethod = "";
			}

			if($period_end - $period_start > 0 )
			{
				$sqlperiod = " AND start_date < $period_end AND start_date > $period_start ";
			}
			else
			{
				$sqlperiod = "";
			}

			$column_select = ((is_string($column) && $column != '')?$column:'*');
			$this->column_array = explode(',',$column);

			$sql = "SELECT $column_select FROM phpgw_p_hours WHERE $filtermethod $querymethod $sqlperiod";

			if($limit)
			{
				$this->db2->query($sql,__LINE__,__FILE__);
				$this->total_records = $this->db2->num_rows();
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}
			//echo '<pre>'.$sql.$ordermethod.'</pre>';
			return $this->db2hours();
		}

		function read_single_hours($hours_id)
		{
			$this->db->query('SELECT * from phpgw_p_hours WHERE id=' . intval($hours_id),__LINE__,__FILE__);
			list($hours) = $this->db2hours();

			return $hours;
		}

		function add_hours($values)
		{
			$values['hours_descr']	= $this->db->db_addslashes($values['hours_descr']);
			$values['remark']		= $this->db->db_addslashes($values['remark']);
			$values['km_distance']	= $values['km_distance'] + 0.0;
			$values['t_journey']	= intval($values['t_journey']);

			$this->db->query('INSERT into phpgw_p_hours (project_id,activity_id,entry_date,start_date,end_date,hours_descr,remark,billable,minutes,'
							. 'status,employee,pro_parent,pro_main,km_distance,t_journey,surcharge,booked) VALUES (' . intval($values['project_id']) . ','
							. intval($values['activity_id']) . ',' . time() . ',' . intval($values['sdate']) . ',' . intval($values['edate']) . ",'"
							. $values['hours_descr'] . "','" . $values['remark'] . "','" . (isset($values['billable'])?'N':'Y') . "'," . intval($values['w_minutes'])
							. ",'" . $values['status'] . "'," . intval($values['employee']) . ',' . intval($values['pro_parent']) . ',' . intval($values['pro_main'])
							. ',' . $values['km_distance'] . ',' . $values['t_journey'] . ',' . intval($values['surcharge']) . ',"N")',__LINE__,__FILE__); 
			// auto update project start date if it is not sset
			$this->db->query('UPDATE phpgw_p_projects SET start_date = '.intval($values['sdate']).' WHERE project_id = '.intval($values['project_id']).' AND start_date = 0');
		}

		function edit_hours($values)
		{
			$values['hours_descr']	= $this->db->db_addslashes($values['hours_descr']);
			$values['remark']		= $this->db->db_addslashes($values['remark']);
			$values['km_distance']	= $values['km_distance'] + 0.0;
			$values['t_journey']	= $values['t_journey'] + 0.0;

			$this->db->query('UPDATE phpgw_p_hours SET project_id='.$values['project_id'].', activity_id=' . intval($values['activity_id']) . ',entry_date=' . time() . ',start_date='
							. intval($values['sdate']) . ',end_date=' . intval($values['edate']) . ",hours_descr='" . $values['hours_descr'] . "',remark='"
							. $values['remark'] . "', billable='" . (isset($values['billable'])?'N':'Y') . "', minutes=" . intval($values['w_minutes'])
							. ",status='" . $values['status'] . "',employee=" . intval($values['employee']) . ', pro_parent='.$values['pro_parent'].',pro_main='.$values['pro_main'].', km_distance=' . $values['km_distance']
							. ', t_journey=' . $values['t_journey'] . ', surcharge=' . intval($values['surcharge']) . ' where id=' . intval($values['hours_id']),__LINE__,__FILE__);
		}

		function delete_hours($values)
		{
			switch($values['action'])
			{
				case 'track':	$h_table = 'phpgw_p_ttracker'; $column = 'track_id'; break;
				default:		$h_table = 'phpgw_p_hours'; $column = 'id'; break;
			}

			$this->db->query("Delete from $h_table where $column=" . intval($values['id']),__LINE__,__FILE__);
		}

		/*function update_hours_act($activity_id, $minperae)
		{
			$this->db->query('SELECT id,minperae from phpgw_p_hours where activity_id=' . intval($activity_id),__LINE__,__FILE__); 

			while ($this->db->next_record())
			{
				if ($this->db->f('minperae') == 0)
				{
					$hours[] = $this->db->f('id');
				}
			}

			if (is_array($hours))
			{
				for ($i=0;$i<=count($hours);$i++)
				{
					$this->db->query('UPDATE phpgw_p_hours set minperae=' . intval($minperae) . ' WHERE id=' . intval($hours[$i]),__LINE__,__FILE__);
				}
			}
		}*/

		function format_wh($minutes = 0)
		{
			if($minutes)
			{
				$wh = array
				(
					'whours_formatted'	=> intval($minutes/60),
					'wmin_formatted'	=> abs( $minutes- ( (int)($minutes / 60) *60 ) ),
					'wminutes'			=> $minutes
				);

				if((0 <= abs($wh['wmin_formatted'])) && (abs($wh['wmin_formatted']) <= 9))
				{
					$wh['wmin_formatted']	= '0' . abs($wh['wmin_formatted']);
				}

				$wh['whwm']	= $wh['whours_formatted'] . ':' . $wh['wmin_formatted'];
			}
			else
			{
				$wh = array
				(
					'whours_formatted'	=> 0,
					'wmin_formatted'	=> '00',
					'wminutes'			=> 0,
					'whwm'				=> '0:00'
				);
			}
			return $wh;
		}
		
		function min2str($min) {
			return sprintf('%s%d:%02d', $min<0?'-':'', abs($min)/60, abs($min)%60);
		}

		function str2min($s) {
			if (ereg('(-?)([0-9]+):([0-9][0-9])', $s, $h)) {
				return $h[1].($h[2]*60+$h[3]);
			}
			else {
				return(0);
			}
		}
		
		function calculate_activity_budget($params = 0)
		{
			$project_id		= intval($params['project_id']);
			$project_array	= $params['project_array'];

			if(is_array($project_array))
			{
				$select = ' project_id in(' . implode(',',$project_array) . ')';
			}
			else
			{
				$select = ' project_id=' . $project_id;
			}

			$this->db->query('SELECT id,activity_id,billable from phpgw_p_projectactivities where ' . $select,__LINE__,__FILE__);

			while($this->db->next_record())
			{
				$act[] = array
				(
					'activity_id'	=> $this->db->f('activity_id'),
					'id'			=> $this->db->f('id'),
					'billable'		=> $this->db->f('billable')
				);
			}

			if(is_array($act))
			{
				$i = 0;
				foreach($act as $a)
				{
					$this->db->query('SELECT minperae, billperae from phpgw_p_activities where id=' . $a['activity_id'],__LINE__,__FILE__);
					$this->db->next_record();
					$activity[$i] = array
					(
						'minperae'		=> $this->db->f('minperae'),
						'billperae'		=> $this->db->f('billperae'),
						'activity_id'	=> $a['activity_id'],
						'id'			=> $a['id'],
						'billable'		=> $a['billable']
					);

					$this->db->query('SELECT SUM(minutes) as utime from phpgw_p_hours where' . $select . ' AND activity_id=' . $a['id'],__LINE__,__FILE__);
					$this->db->next_record();
					$activity[$i]['utime'] = $this->db->f('utime');
					$i++;
				}

				if(is_array($activity))
				{
					$bbudget = $budget = 0;
					foreach($activity as $activ)
					{
						$factor_per_minute = $activ['billperae']/60;
						if($activ['billable'] == 'Y')
						{
							$bbudget += round($factor_per_minute*$activ['utime'],2);
						}
						$budget += round($factor_per_minute*$activ['utime'],2);
					}
					return array('bbuget' => $bbudget,'budget' => $budget);
				}
			}
		}

		function get_activity_time_used($params = 0)
		{
			$project_id		= intval($params['project_id']);
			$project_array	= $params['project_array'];
			$no_billable	= isset($params['no_billable'])?$params['no_billable']:False;
			$is_billable	= isset($params['is_billable'])?$params['is_billable']:False;

			$sql = 'SELECT SUM(minutes) as utime from phpgw_p_hours where';

			if(is_array($project_array))
			{
				$select = ' project_id in(' . implode(',',$project_array) . ')';
			}
			else
			{
				$select = ' project_id=' . $project_id;
			}

			if($no_billable || $is_billable)
			{
				$this->db->query('SELECT activity_id from phpgw_p_projectactivities where ' . $select . " AND billable='" . ($no_billable?'N':'Y') . "'",__LINE__,__FILE__);
				$i = 0;
				while($this->db->next_record())
				{
				 	$act[$i] = $this->db->f('activity_id');
					$i++;
				}

				if(is_array($act))
				{
					$select .= ' AND activity_id in(' . implode(',',$act) . ')';
				}
			}
			$this->db->query($sql . $select,__LINE__,__FILE__);

			if($this->db->next_record())
			{
				return $this->db->f('utime');
				//return $this->format_wh($hours);
			}
			return False;
		}

		function get_time_used($params = 0)
		{
			$project_id		= intval($params['project_id']);
			$project_array	= $params['project_array'];
			$hours			= isset($params['hours'])?$params['hours']:True;
			$action			= $params['action']?$params['action']:'subs';

			$columns = 'id,minutes,t_journey,billable,surcharge,employee';
			$this->column_array = explode(',',$columns);

			$sql = 'SELECT id,minutes,t_journey,billable,surcharge,employee from phpgw_p_hours where ' . "status='done' and";

			switch($action)
			{
				case 'mains':
					$select = ' pro_main=' . $project_id;
					break;
				default:
					if(is_array($project_array))
					{
						$select = ' project_id in(' . implode(',',$project_array) . ')';
					}
					else
					{
						$select = ' project_id=' . $project_id;
					}
					break;
			}

			$this->db->query($sql . $select,__LINE__,__FILE__);
			return $this->db2hours(True);
		}

		function get_project_employees($params = 0)
		{
			$project_id		= intval($params['project_id']);
			$project_array	= $params['project_array'];
			$action			= $params['action']?$params['action']:'subs';

			switch($action)
			{
				case 'mains':
					$select = ' pro_main=' . $project_id;
					break;
				default:
			if(is_array($project_array))
			{
				$select = ' project_id in(' . implode(',',$project_array) . ')';
			}
			else
			{
				$select = ' project_id=' . $project_id;
			}
					break;
			}

			$sql = 'SELECT employee from phpgw_p_hours where ' . $select;

			$this->db->query($sql,__LINE__,__FILE__);

			$emps = array();
			$i = 0;
			while($this->db->next_record())
			{
				if(!in_array($this->db->f('employee'),$emps))
				{
					$emps[$i] = $this->db->f('employee');
					$i++;
				}
			}
			return $emps;
		}

		function get_employee_time_used($params = 0)
		{
			$project_id		= intval($params['project_id']);
			$project_array	= $params['project_array'];

			$emps = $this->get_project_employees($params);

			if(is_array($project_array))
			{
				$select = ' and project_id in(' . implode(',',$project_array) . ')';
			}
			else
			{
				$select = ' and project_id=' . $project_id;
			}

			for($i=0;$i<count($emps);$i++)
			{
				$bemp[$i] = array
				(
					'employee'	=> $emps[$i]
				);

				$sql = 'SELECT id,minutes,start_date,end_date,billable,surcharge from phpgw_p_hours where ' . "status='done'" . ' and employee='
						. $emps[$i] . $select;
				$this->db->query($sql,__LINE__,__FILE__);

				while($this->db->next_record())
				{
					$bemp[$i]['hours'][] = array
					(
						'minutes'	=> $this->db->f('minutes'),
						'billable'	=> $this->db->f('billable'),
						'sdate'		=> $this->db->f('start_date'),
						'edate'		=> $this->db->f('end_date'),
						'surcharge'	=> $this->db->f('surcharge')
					);
				}
			}
			return $bemp;
		}

		function db2track()
		{
			while ($this->db->next_record())
			{
				$track[] = array
				(
					'track_id'		=> $this->db->f('track_id'),
					'project_id'	=> $this->db->f('project_id'),
					'hours_descr'	=> $this->db->f('hours_descr'),
					'status'		=> $this->db->f('status'),
					'minutes'		=> $this->db->f('minutes'),
					'sdate'			=> $this->db->f('start_date'),
					'edate'			=> $this->db->f('end_date'),
					'employee'		=> $this->db->f('employee'),
					'activity_id'	=> $this->db->f('activity_id'),
					'remark'		=> $this->db->f('remark'),
					'km_distance'	=> $this->db->f('km_distance'),
					't_journey'		=> $this->db->f('t_journey'),
					'surcharge'		=> $this->db->f('surcharge'),
					'billable'		=> $this->db->f('billable')
				);
			}
			return $track;
		}

		function list_ttracker()
		{
			$ordermethod = ' order by project_id,start_date ASC';
			$sql = 'SELECT * from phpgw_p_ttracker where employee=' . $this->account;

			$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			return $this->db2track();
		}

		function read_single_track($track_id)
		{
			$this->db->query('SELECT * from phpgw_p_ttracker WHERE track_id=' . intval($track_id),__LINE__,__FILE__);
			list($hours) = $this->db2track();
			return $hours;
		}

		function format_ttime($diff)
		{
			$tdiff = array();
			$tdiff['days'] = intval($diff/60/60/24);
			$diff -= $tdiff['days']*60*60*24;
			$tdiff['hrs'] = intval($diff/60/60);
			$diff -= $tdiff['hrs']*60*60;
			$tdiff['mins'] = round($diff/60);
			//$diff -= $minsDiff*60;
			//$secsDiff = $diff;
			return $tdiff;
		}

		function get_max_track($project_id = '',$status = False)
		{
			if($status)
			{
				$status_select = " and status != 'apply'";
			}

			$this->db->query('SELECT max(track_id) as max from phpgw_p_ttracker where project_id=' . intval($project_id) . ' and employee='
							. $this->account . $status_select,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('max');
		}

		function check_ttracker($params)
		{
			$project_id	= intval($params['project_id']);
			$track_id	= $this->get_max_track($project_id);
			$status		= $params['status']?$params['status']:'active';
			//echo 'MAX: ' . $track_id;

			switch($status)
			{
				case 'active':		$status_select = " and (status='start' or status='continue') and end_date = 0"; break;
				case 'inactive':	$status_select = " and (status='stop' or status='pause')"; break;
			}
			$this->db->query('SELECT minutes from phpgw_p_ttracker where track_id=' . intval($track_id) . $status_select,__LINE__,__FILE__);
			if($this->db->next_record())
			{
				return True;
			}
			return False;
		}

		function ttracker($values)
		{	
			$values['hours_descr']	= $this->db->db_addslashes($values['hours_descr']);
			$values['remark']		= $this->db->db_addslashes($values['remark']);
			$values['km_distance']	= $values['km_distance'] + 0.0;
			$values['t_journey']	= intval($values['t_journey']);
			$values['surcharge']	= intval($values['surcharge']);
			$project_id				= intval($values['project_id']);

			if(!isset($values['billable']))
			{
				$values['billable'] = $this->return_value('billable', $project_id);
			}
			elseif(($values['billable'] != 'N') && ($values['billable'] != 'Y'))
			{
				$values['billable'] = 'Y';
			}

			//_debug_array($values);

			switch($values['action'])
			{
				case 'start':
				case 'continue':
				$this->db2->query('SELECT track_id,start_date,project_id,hours_descr from phpgw_p_ttracker where employee=' . $this->account . ' and project_id !=' . $project_id
									. " and (status='start' or status='continue') and minutes=0",__LINE__,__FILE__);
					while($this->db2->next_record())
					{
						$wtime = $this->format_ttime(time() - $this->db2->f('start_date'));
						$work_time = ($wtime['hrs']*60)+$wtime['mins'];
						$this->db->query('UPDATE phpgw_p_ttracker set end_date=' . time() . ',minutes=' . $work_time . ' where track_id='
										. $this->db2->f('track_id'),__LINE__,__FILE__);

						$this->db->query('INSERT into phpgw_p_ttracker (project_id,activity_id,start_date,end_date,employee,status,hours_descr,remark,billable) '
										.' values(' . $this->db2->f('project_id') . ',0,' . time() . ',0,' . $this->account . ",'pause','"
										. $this->db2->f('hours_descr') . "','','" . $values['billable'] . "')", __LINE__,__FILE__);
					}
					break;
			}

			switch($values['action'])
			{
				case 'start':
				case 'pause':
				case 'stop':
				case 'continue':
					$max = intval($this->get_max_track($project_id));
					$this->db->query('UPDATE phpgw_p_ttracker set end_date=' . time() . ' where track_id=' . $max,__LINE__,__FILE__);

					$this->db->query('SELECT start_date,end_date,hours_descr from phpgw_p_ttracker where track_id=' . $max,__LINE__,__FILE__);
					$this->db->next_record();
					$sdate = $this->db->f('start_date');
					$edate = $this->db->f('end_date');
					$hours_descr = $this->db->f('hours_descr');
					if(!$hours_descr)
					{
						$hours_descr = $values['hours_descr']?$values['hours_descr']:$values['action'];
					}
					$wtime		= $this->format_ttime($edate - $sdate);
					$work_time	= ($wtime['hrs']*60)+$wtime['mins'];

					$this->db->query('UPDATE phpgw_p_ttracker set minutes=' . $work_time . ' where track_id=' . $max,__LINE__,__FILE__);

					$this->db->query('INSERT into phpgw_p_ttracker (project_id,activity_id,start_date,end_date,employee,status,hours_descr,remark,billable) '
										.' values(' . $project_id . ',' . intval($values['activity_id']) . ',' . time() . ',0,' . $this->account . ",'" . $values['action']
										. "','" . $hours_descr . "','" . $values['remark'] . "','" . $values['billable'] ."')",__LINE__,__FILE__);

					if($values['action'] == 'stop')
					{
						$this->db->query("UPDATE phpgw_p_ttracker set stopped='Y' where employee=" . $this->account . ' and project_id=' . $project_id,__LINE__,__FILE__);
					}
					break;
				case 'edit':
					$this->db->query('UPDATE phpgw_p_ttracker set activity_id=' . intval($values['activity_id']) . ',start_date=' . intval($values['sdate']) . ',end_date='
									. intval($values['edate']) . ',minutes=' . intval($values['w_minutes']) . ", hours_descr='" . $values['hours_descr'] . "',remark='"
									. $values['remark'] . "', t_journey=" . $values['t_journey'] . ', km_distance=' . $values['km_distance'] . ', surcharge='
									. $values['surcharge'] . ' where track_id=' . intval($values['track_id']),__LINE__,__FILE__);
					break;
			}

			if($values['action'] == 'apply')
			{
					$this->db->query('INSERT into phpgw_p_ttracker (project_id,activity_id,employee,start_date,end_date,minutes,hours_descr,status,'
									.'remark,t_journey,km_distance,stopped,surcharge,billable) values(' . $project_id . ',' . intval($values['activity_id'])
									. ',' . $this->account . ',' . intval($values['sdate']) . ',' . intval($values['edate']) . ','
									. intval($values['w_minutes']) . ",'" . $values['hours_descr'] . "','" . $values['action'] . "','" . $values['remark']
									. "'," . $values['t_journey'] . ',' . $values['km_distance'] . ",'Y'," . intval($values['surcharge']) . ",'" . $values['billable'] . "')", __LINE__,__FILE__);

					//return $this->db->get_last_insert_id('phpgw_p_ttracker','track_id');
			}
		}

		function save_ttracker()
		{
			$this->db->query("SELECT * from phpgw_p_ttracker where status != 'pause' and status != 'stop' and end_date > 0 and minutes > 0 and stopped='Y' and employee="
							. $this->account,__LINE__,__FILE__);
			$hours = $this->db2track();

			while(is_array($hours) && (list($no_use,$hour) = each($hours)))
			{
				$hour['pro_parent']  = $this->return_value('pro_parent',$hour['project_id']);
				$hour['pro_main']	   = $this->return_value('pro_main',$hour['project_id']);
				$hour['km_distance'] = $hour['km_distance'] + 0.0;
				$hour['t_journey']   = intval($hour['t_journey']);

				$this->db->query('INSERT into phpgw_p_hours (project_id,activity_id,entry_date,start_date,end_date,hours_descr,remark,minutes,'
							. 'status,employee,pro_parent,pro_main,billable,t_journey,km_distance,surcharge,booked) VALUES (' . intval($hour['project_id']) . ','
							. intval($hour['activity_id']) . ',' . time() . ',' . intval($hour['sdate']) . ',' . intval($hour['edate']) . ",'"
							. $hour['hours_descr'] . "','" . $hour['remark'] . "'," . intval($hour['minutes']) . ",'done'," . intval($hour['employee'])
							. ',' . intval($hour['pro_parent']) . ',' . intval($hour['pro_main']) . ",'".$hour['billable']."'," . $hour['t_journey'] . ','
							. $hour['km_distance'] . ',' . intval($hour['surcharge']) .",'N'" . ')',__LINE__,__FILE__);

				$this->db->query('DELETE from phpgw_p_ttracker where track_id=' . intval($hour['track_id']),__LINE__,__FILE__);
			}
			$this->db->query('DELETE from phpgw_p_ttracker where employee=' . $this->account . " and (status='pause' or status='stop') and stopped='Y'",__LINE__,__FILE__);
		}

		function return_value($action,$pro_id)
		{
			switch ($action)
			{
				case 'pro_main':	$column = 'main'; break;
				case 'pro_parent':	$column = 'parent'; break;
				case 'billable':	$column = 'billable'; break;
			}

			$this->db->query("SELECT $column from phpgw_p_projects where project_id=$pro_id",__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				return $GLOBALS['phpgw']->strip_html($this->db->f($column));
			}
		}
		
		function unbook_hours()
		{
			$hours_id	= get_var('hours_id',array('POST','GET'));
			$this->db->query("UPDATE phpgw_p_hours SET booked='N' where id='$hours_id'",__LINE__,__FILE__);
		}
		
		function set_booked($values)
		{
			$this->db->query('UPDATE phpgw_p_hours SET booked="Y" where (start_date <= ' . $values['edate'] . ')',__LINE__,__FILE__);
		}
		
		function get_dayhours($employee, $start_date, $end_date)
		{
			// Nice SQL-Statement Kai
//			$sql  = 'SELECT UNIX_TIMESTAMP(FROM_UNIXTIME(start_date,"%Y%m%d")) as day, project_id, sum(minutes) + sum(t_journey) ';
			$sql  = 'SELECT start_date as day, project_id, sum(minutes) + sum(t_journey) ';
			$sql .= 'FROM phpgw_p_hours ';
			$sql .= 'WHERE employee='.(int)$employee.' AND start_date >= '.(int)$start_date.' AND start_date <= '.((int)$end_date + 86400).' ';
			$sql .= 'GROUP BY project_id, day ';
			$sql .= 'ORDER BY day, project_id';
			//echo $sql;
			$this->db->query($sql);
			return $this->db;
		}

		function get_emp_worktimes($employee, $start_date=0, $end_date=0)
		{
			$start_date = mktime( 0, 0, 0, date("m", $start_date), date("d", $start_date), date("y", $start_date));
			$end_date   = mktime(23,59,59, date("m", $end_date)  , date("d", $end_date)  , date("y", $end_date  ));

			$sql  = 'SELECT phpgw_p_hours.project_id AS project_id, ';
			$sql .= '       SUM(minutes) AS sum_minutes_worktime, ';
			$sql .= '       SUM(t_journey) AS sum_minutes_journey, ';
			$sql .= '       SUM(minutes+t_journey) AS sum_minutes_all ';
			$sql .= 'FROM phpgw_p_hours, phpgw_p_projects ';
			$sql .= 'WHERE phpgw_p_hours.start_date >= '.(int)$start_date.' AND phpgw_p_hours.start_date <= '.((int)$end_date).' ';
			$sql .= 'AND employee='.(int)$employee.' ';
			$sql .= 'AND phpgw_p_hours.project_id = phpgw_p_projects.project_id ';
			$sql .= 'GROUP BY phpgw_p_hours.project_id ';

			//echo $sql;
			$this->db->query($sql);
			return $this->db;
		}

	}
?>
