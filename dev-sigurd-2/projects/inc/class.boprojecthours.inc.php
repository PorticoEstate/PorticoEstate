<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id$
	* $Source: /sources/phpgroupware/projects/inc/class.boprojecthours.inc.php,v $
	*/

	/*
	* Import required classes class
	*/
	phpgw::import_class('phpgwapi.datetime');

	class boprojecthours
	{
		var $start;
		var $query;
		var $filter;
		var $order;
		var $sort;
		var $status;
		var $project_id;

		var $public_functions = array
		(
			'list_hours'		=> true,
			'check_values'		=> true,
			'save_hours'		=> true,
			'read_single_hours'	=> true,
			'delete_hours'		=> true
		);

		function boprojecthours()
		{
			$action				= isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';

			$this->boprojects	= CreateObject('projects.boprojects', true, $action);
			$this->sohours		= $this->boprojects->sohours;

			$this->account		= $this->boprojects->account;
			$this->grants		= $this->boprojects->grants;

			$this->start		= $this->boprojects->start;
			$this->query		= $this->boprojects->query;
			$this->filter		= $this->boprojects->filter;
			$this->order		= $this->boprojects->order;
			$this->sort			= $this->boprojects->sort;
			$this->status		= $this->boprojects->status;
			// TODO: Finn - check this one!
			$this->project_id	= isset($this->boprojects->project_id) ? $this->boprojects->project_id : '';
			$this->cat_id		= $this->boprojects->cat_id;
			$this->limit		= true;

			$this->siteconfig	= $this->boprojects->siteconfig;
		}

		function add_perms($pro)
		{
			$coordinator = $this->boprojects->return_value('co',$this->project_id);

			if ( $this->boprojects->check_perms($this->grants[$coordinator],PHPGW_ACL_ADD) || $coordinator == $this->account )
			{
				return true;
			}

			if( $this->member() )
			{
				return true;
			}

			//$main = $this->boprojects->return_value('main',$this->project_id);
			//$main_co = $this->boprojects->return_value('co',intval($pro['main']));

			if( $this->boprojects->check_perms($this->grants[$pro['main_co']],PHPGW_ACL_ADD) || $pro['main_co'] == $this->account )
			{
				return true;
			}

			$parent = $this->boprojects->return_value('parent',$this->project_id);
			$parent_co = $this->boprojects->return_value('co',$parent);

			if( $this->boprojects->check_perms($this->grants[$parent_co],PHPGW_ACL_ADD) || $parent_co == $this->account )
			{
				return true;
			}
			if( $this->boprojects->isprojectadmin('pad') || $this->boprojects->isprojectadmin('pmanager') )
			{
				return true;
			}
			return false;
		}

		function edit_perms( $pro )
		{
			$pro['action'] = isset($pro['action']) ? $pro['action'] : 'edit';

			switch( $pro['action'] )
			{
				case 'delete':
					$acl = PHPGW_ACL_DELETE;
					break;
				default:
					$acl = PHPGW_ACL_EDIT;
					break;
			}

			if ( ($pro['status'] != 'billed') && ($pro['status'] != 'closed') && ($pro['booked'] != 'Y') )
			{
				if ( $pro['employee'] == $this->account && !$pro['adminonly'] )
				{
					return true;
				}

				$coordinator = $this->boprojects->return_value('co',$this->project_id);
				if ( $this->boprojects->check_perms($this->grants[$coordinator],$acl) || $coordinator == $this->account )
				{
					return true;
				}

				//$main_co = $this->boprojects->return_value('co',intval());
				if( $this->boprojects->check_perms($this->grants[$pro['main_co']],$acl) || $pro['main_co'] == $this->account )
				{
					return true;
				}

				$parent = $this->boprojects->return_value('parent',$this->project_id);
				$parent_co = $this->boprojects->return_value('co',$parent);

				if( $this->boprojects->check_perms($this->grants[$parent_co],$acl) || $parent_co == $this->account )
				{
					return true;
				}

				if( $this->boprojects->isprojectadmin('pad') || $this->boprojects->isprojectadmin('pmanager') )
				{
					return true;
				}

				return False;
			}
		}

		function format_htime( $hdate = '' )
		{
			$hdate = (int)$hdate;

			if ( $hdate > 0 )
			{
				$hour			= date('H',$hdate);
				$min			= date('i',$hdate);
				$hdate			= $hdate + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
				$htime['date']	= $GLOBALS['phpgw']->common->show_date($hdate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$htime['time']	= phpgwapi_datetime::formattime($hour,$min);
			}
			else
			{
				$htime['date']	= 0;
				$htime['time']	= 0;
			}

			return $htime;
		}

		function format_minutes( $min = '' ) // should be in a common class - needed almost everywhere
		{
			if( $min != 0 )
			{
				return sprintf('%s%d:%02d', $min < 0 ? '-' : '', abs($min)/60, abs($min)%60);
			}
			else
			{
				return '';
			}
		}

		function hdate_format( $hdate = '' )
		{
			if ( !$hdate )
			{
				$dateval['month']	= date('m',time());
				$dateval['day']		= date('d',time());
				$dateval['year']	= date('Y',time());
				$dateval['hour']	= date('H',time());
				$dateval['min']		= date('i',time());
			}
			else
			{
				$dateval['month']	= date('m',$hdate);
				$dateval['day']		= date('d',$hdate);
				$dateval['year']	= date('Y',$hdate);
				$dateval['hour']	= date('H',$hdate);
				$dateval['min']		= date('i',$hdate);
			}
			return $dateval;
		}

		function list_hours( $start = 0, $end = 0 )
		{
			$filter		= $this->filter;
			$hours_list	= $this->sohours->read_hours( array
			(
				'start'			=> $this->start,
			    'limit'			=> $this->limit,
				'query'			=> $this->query,
				'filter'		=> $filter,
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'status'		=> $this->state,
				'project_id'	=> $this->project_id,
				'period_start'	=> intval($start),
				'period_end'	=> intval($end)
			));

			$this->total_records = $this->sohours->total_records;

			while( is_array($hours_list) && ( list($no_use,$hour) = each($hours_list) ) )
			{
				$hours[] = array
				(
					'hours_id'			=> $hour['hours_id'],
					'project_id'		=> $hour['project_id'],
					'hours_descr'		=> $GLOBALS['phpgw']->strip_html($hour['hours_descr']),
					'activity_title'	=> $this->siteconfig['accounting']=='activity'?$this->boprojects->return_value('act',$hour['activity_id']):'',
					'status'			=> $hour['status'],
					'statusout'			=> lang($hour['status']),
					'sdate'				=> $hour['start_date'],
					'edate'				=> $hour['end_date'],
					'minutes'			=> $hour['minutes'],
					'wh'				=> $this->sohours->format_wh($hour['minutes']),
					'wh_t_journey'		=> $this->sohours->format_wh($hour['t_journey']),
					't_journey'			=> $this->sohours->min2str($hour['t_journey']),
					'wh_all'			=> $this->sohours->format_wh($hour['minutes']+$hour['t_journey']),
					'employee'			=> $hour['employee'],
					'employeeout'		=> $GLOBALS['phpgw']->common->grab_owner_name($hour['employee']),
					'sdate_formatted'	=> $this->format_htime($hour['sdate']),
					'edate_formatted'	=> $this->format_htime($hour['edate'])
				);
			}
			return $hours;
		}

		function read_single_hours( $hours_id )
		{
			$hours = $this->sohours->read_single_hours($hours_id);

			$hour = array
			(
				'hours_id'			=> $hours['hours_id'],
				'project_id'		=> $hours['project_id'],
				'pro_parent'		=> $hours['pro_parent'],
				'pro_main'			=> $hours['pro_main'],
				'hours_descr'		=> $GLOBALS['phpgw']->strip_html($hours['hours_descr']),
				'status'			=> $hours['status'],
				'statusout'			=> lang($hours['status']),
				'minutes'			=> $hours['minutes'],
				'wh'				=> $this->sohours->format_wh($hours['minutes']),
				'sdate'				=> $hours['sdate'],
				'edate'				=> $hours['edate'],
				'employee'			=> $hours['employee'],
				'employeeout'		=> $GLOBALS['phpgw']->common->grab_owner_name($hours['employee']),
				'activity_id'		=> $hours['activity_id'],
				'activity_title'	=> $this->siteconfig['accounting']=='activity'?$this->boprojects->return_value('act',$hours['activity_id']):'',
				'remark'			=> nl2br($GLOBALS['phpgw']->strip_html($hours['remark'])),
				'sdate_formatted'	=> $this->hdate_format($hours['sdate']),
				'edate_formatted'	=> $this->hdate_format($hours['edate']),
				'stime_formatted'	=> $this->format_htime($hours['sdate']),
				'etime_formatted'	=> $this->format_htime($hours['edate']),
				'billable'			=> $hours['billable'],
				'km_distance'		=> $hours['km_distance'],
				't_journey'			=> $hours['t_journey'],
				'booked'			=> $hours['booked'],
				'surcharge'			=> $hours['surcharge']
			);

			return $hour;
		}

		function member()
		{
			return $this->boprojects->member($this->project_id);
		}

		function check_values( $values )
		{
			if( !$values['project_id'] )
			{
				$error[] = lang('please select a project for time tracking');
			}

			if( $this->siteconfig['accounting'] != 'activity' && strlen(trim($values['hours_descr']))==0 )
			{
				$error[] = lang('Description was not set');
			}
			elseif( $this->siteconfig['accounting'] == 'activity' )
			{
				$activity = $this->boprojects->read_single_activity($values['activity_id']);

				if ( ! is_array($activity) )
				{
					$error[] = lang('please select an activity');
				}
				elseif ( $activity['remarkreq']=='Y' && (!$values['remark']) )
				{
					$error[] = lang('Please enter a remark');
				}
			}

			if ( strlen($values['hours_descr']) > 250 )
			{
				$error[] = lang('Description can not exceed 250 characters in length');
			}

			if ( strlen($values['remark']) > 8000 )
			{
				$error[] = lang('Remark can not exceed 8000 characters in length !');
			}

			if( $this->siteconfig['hoursbookingnull'] == 'no' )
			{
				if( (intval($values['hours']) == 0 && intval($values['minutes']) == 0) && (intval($values['t_journey_h']) == 0 && intval($values['t_journey_m']) == 0) )
				{
					$error[] = lang('please enter the work time');
				}
			}

			if( $this->siteconfig['hoursbookingday'] == 'no' )
			{
				if( isset($values['shour']) && isset($values['smin']) && isset($values['ehour']) && isset($values['emin']) )
				{
					if( isset($values['hours']) && isset($values['minutes']) )
					{
						$time_s  = $values['shour']*60+$values['smin'];
						$time_e  = $values['ehour']*60+$values['emin'];
						$time_w  = $values['hours']*60+$values['minutes'];

						$time_se = $time_e - $time_s;

						if( ($time_se > 0) && ($time_w > $time_se) )
						{
							$error[] = lang('work time can not be bigger then time between start time and end time');
						}
					}

					if( mktime(intval($values['shour']), intval($values['smin']), 0, 1, 1, 2000) > mktime(intval($values['ehour']), intval($values['emin']), 0, 1, 1, 2000) )
					{
						$error[] = lang('end time can not be before start time');
					}
				}

				if( isset($values['hours']) && isset($values['minutes']) )
				{
					$minutes = intval($values['hours'])*60 + intval($values['minutes']);
					if( $minutes < 0 || $minutes > 1440 )
					{
						$error[] = lang('You have entered an invalid work time');
					}
				}
			}
			else
			{
				if( $values['sdate'] > 0 && $values['edate'] > 0 )
				{
					if( $values['edate'] < $values['sdate'] )
					{
						$error[] = lang('end date can not be before start date');
					}
				}
			}

			if( $this->siteconfig['accounting'] == 'activity' )
			{
				$activity = $this->boprojects->read_single_activity($values['activity_id']);

				if ( !is_array($activity) )
				{
					$error[] = lang('Please select an activity');
				}
				elseif ( $activity['remarkreq']=='Y' && (!$values['remark']) )
				{
					$error[] = lang('Please enter a remark');
				}
			}

			$async	= CreateObject('phpgwapi.asyncservice');
			$aid	= 'projects-workhours-booking-';
			$data	= $async->read($aid . '%');

			$month	= 1;
			$year 	= 1970;

			if( is_array($data) )
			{
				foreach( $data as $job )
				{
					$asyncdata = $job['data'];

					if( !isset($asyncdata['book_year']) || !isset($asyncdata['book_month']) )
					{
						continue;
					}

					$month	= $asyncdata['book_month'];
					$year	= $asyncdata['book_year'];
				}

				if( $values['sdate'] < mktime(0,0,0,$month,1,$year) )
				{
					$error[] = lang('You entered a worktime for a month that was already booked! Booking is not possible any more.').' ('.date("d.m.Y", $values['sdate']).' < 1.'.$month.'.'.$year.')';
				}
			}

			if ( is_array($error) )
			{
				return $error;
			}
			else
			{
				return true;
			}
		}

		function check_ttracker( $values )
		{
			if( !$values['project_id'] )
			{
				$error[] = lang('please select a project for time tracking');
			}

			if ( strlen($values['remark']) > 8000 )
			{
				$error[] = lang('Remark can not exceed 8000 characters in length !');
			}

			if( strlen($values['hours_descr']) > 250 )
			{
				$error[] = lang('Description can not exceed 250 characters in length');
			}

			if( is_array($error) )
			{
				return $error;
			}

			if( $values['start'] || $values['continue'] )
			{
				$is_active = $this->sohours->check_ttracker( array
				(
					'project_id'	=> $values['project_id'],
					'status'		=> 'active'
				));

				if( $is_active )
				{
					$error[] = lang('time tracking for this project is already active');
				}

				if( $values['start'] )
				{
					if( $this->siteconfig['accounting'] != 'activity' && strlen(trim($values['hours_descr']))==0 )
					{
						$error[] = lang('Description was not set');

					}
					elseif( $this->siteconfig['accounting'] == 'activity' )
					{
						$activity = $this->boprojects->read_single_activity($values['activity_id']);

						if ( !is_array($activity) )
						{
							$error[] = lang('please select an activity');
						}
						elseif ( $activity['remarkreq']=='Y' && (!$values['remark']) )
						{
							$error[] = lang('Please enter a remark');
						}
					}
				}
			}
			elseif( $values['stop'] || $values['pause'] )
			{
				$is_active = $this->sohours->check_ttracker( array
				(
					'project_id'	=> $values['project_id'],
					'status'		=> 'active'
				));

				if( !$is_active )
				{
					$error[] = lang('time tracking for this project is not active');
				}
			}
			return $error;
		}

		function save_hours( $values )
		{
			/*if ($values['shour'] && ($values['shour'] != 0) && ($values['shour'] != 12))
			{
				if ($values['sampm']=='pm')
				{
					$values['shour'] = $values['shour'] + 12;
				}
			}

			if ($values['shour'] && ($values['shour'] == 12))
			{
				if ($values['sampm']=='am')
				{
					$values['shour'] = 0;
				}
			}

			if ($values['ehour'] && ($values['ehour'] != 0) && ($values['ehour'] != 12))
			{
				if ($values['eampm']=='pm')
				{
					$values['ehour'] = $values['ehour'] + 12;
				}
			}

			if ($values['ehour'] && ($values['ehour'] == 12))
			{
				if ($values['eampm']=='am')
				{
					$values['ehour'] = 0;
				}
			}*/

			if ( !$values['sdate'] )
			{
				$values['sdate'] = time();
			}

			$values['smonth']	= date('m',$values['sdate']);
			$values['sday']		= date('d',$values['sdate']);
			$values['syear']	= date('Y',$values['sdate']);
			$values['sdate']	= mktime(($values['shour'] ? $values['shour'] : 0), ($values['smin'] ? $values['smin'] : 0), 0, $values['smonth'],$values['sday'],$values['syear']);

			if ( intval($values['edate']) > 0 )
			{
				$values['emonth']	= date('m',$values['edate']);
				$values['eday']		= date('d',$values['edate']);
				$values['eyear']	= date('Y',$values['edate']);
				$values['edate']	= mktime(($values['ehour'] ? $values['ehour'] : 0), ($values['emin'] ? $values['emin'] : 0), 0, $values['emonth'], $values['eday'], $values['eyear']);
			}
			else
			{
				$values['edate']	= mktime(($values['ehour'] ? $values['ehour'] : 0), ($values['emin'] ? $values['emin'] : 0), 0, $values['smonth'], $values['sday'], $values['syear']);
			}

			if( !$values['t_journey'] && !$values['hours'] && !$values['minutes'] )
			{
				$values['w_minutes'] = ( ( $values['ehour'] * 60 + $values['emin'] ) - ( $values['shour'] * 60 + $values['smin'] ) );
			}
			else
			{
				$values['w_minutes'] = $values['hours'] * 60 + $values['minutes'];
			}

			if($values['track_id'] || $values['action'] == 'apply')
			{
				$this->ttracker($values);
			}
			else
			{
				if ( !$values['employee'] )
				{
					$values['employee'] = $this->sohours->account;
				}

				$values['project_id']	= $values['project_id'] ? $values['project_id'] : $this->project_id;
				$values['pro_parent']	= $this->boprojects->return_value('parent', $values['project_id']);

				if( !$values['pro_main'] )
				{
					$values['pro_main'] = $this->boprojects->return_value('main',$values['project_id']);

					if( !$values['pro_main'] )
					{
						$values['pro_main']	= $values['project_id'];
					}
				}

				if ( intval($values['hours_id']) > 0 )
				{
					$this->sohours->edit_hours($values);
				}
				else
				{
					$this->sohours->add_hours($values);
				}

				$pro = $this->boprojects->read_single_project($this->project_id, 'budget', 'subs');

				// HOURS ALARM

				$hours_percent				= $this->boprojects->soconfig->get_event_extra('hours limit');
				$hours_percent				= $hours_percent>0?$hours_percent:100;
				$pro['ptime_min_percent']	= ( $pro['ptime_min'] * intval($hours_percent) ) / 100;

				//echo 'PTIME_MIN_PERCENT: ' . $pro['ptime_min_percent'];
				//echo 'uhours_jobs_all: ' . $pro['uhours_jobs_all_wminutes'];

				if( $pro['uhours_jobs_all_wminutes'] >= $pro['ptime_min_percent'] )
				{
					//echo 'uhours_jobs_all ' . $pro['uhours_jobs_all_wminutes'] . ' >= ' . $pro['ptime_min_percent'];
					$alarm = $this->boprojects->soprojects->get_alarm(array('project_id' => $this->project_id));

					if( is_array($alarm) )
					{
						$alarm_id = $alarm['alarm_id'];

						if( $pro['ptime_min'] != $alarm['extra'] )
						{
							$this->boprojects->soprojects->update_alarm( array
							(
								'alarm_id'	=> $alarm['alarm_id'],
								'extra'		=> $pro['ptime_min']
							));
						}
					}
					else
					{
						$alarm_id = $this->boprojects->soprojects->add_alarm( array
						(
							'project_id'	=> $this->project_id,
							'extra'			=> $pro['ptime_min']
						));
					}

					$return = $this->boprojects->send_alarm( array
					(
						'project_id'		=> $this->project_id,
						'event_type'		=> 'hours limit',
						'project_name'		=> $pro['title'] . ' [' . $pro['number'] . ']',
						'ptime'				=> $pro['ptime'],
						'uhours_jobs_all'	=> $pro['uhours_jobs_all']
					));

					if( $return )
					{
						$this->boprojects->soprojects->update_alarm( array
						(
							'alarm_id'	=> $alarm_id,
							'send'		=> '0',
							'extra'		=> $pro['ptime_min']
						));
					}
				}

				// BUDGET ALARM

				$budget_percent			= $this->boprojects->soconfig->get_event_extra('budget limit');
				$budget_percent			= $budget_percent > 0 ? $budget_percent : 100;
				$pro['budget_percent']	= ( $pro['budget'] * intval($budget_percent) ) / 100;

				if( $pro['u_budget_jobs'] >= $pro['budget_percent'] )
				{
					//echo 'u_budget_jobs ' . $pro['u_budget_jobs'] . ' >= ' . $pro['budget_percent'];
					$alarm = $this->boprojects->soprojects->get_alarm( array
					(
						'project_id' => $this->project_id,
						'action' => 'budget'
					));

					if( is_array($alarm) )
					{
						$alarm_id = $alarm['alarm_id'];

						if( $pro['budget'] != $alarm['extra'] )
						{
							$this->boprojects->soprojects->update_alarm( array
							(
								'alarm_id' => $alarm['alarm_id'],
								'extra' => $pro['budget']
							));
						}
					}
					else
					{
						$alarm_id = $this->boprojects->soprojects->add_alarm( array
						(
							'project_id'	=> $this->project_id,
							'action'		=> 'budget',
							'extra'			=> $pro['budget']
						));
					}

					$return = $this->boprojects->send_alarm( array
					(
						'project_id'	=> $this->project_id,
						'event_type'	=> 'budget limit',
						'project_name'	=> $pro['title'] . ' [' . $pro['number'] . ']',
						'budget'		=> $pro['budget'],
						'u_budget_jobs'	=> $pro['u_budget_jobs']
					));

					if( $return )
					{
						$this->boprojects->soprojects->update_alarm( array
						(
							'alarm_id'	=> $alarm_id,
							'send'		=> '0',
							'extra'		=> $pro['budget']
						));
					}
				}
			}
		}

		function delete_hours( $values )
		{
			$this->sohours->delete_hours($values);
		}

		function list_ttracker()
		{
			$tracking		= $this->sohours->list_ttracker();
			$project_list	= $this->boprojects->select_project_list( array
			(
				'action'	=> 'all',
				'filter'	=> 'noadmin',
				'formatted'	=> false
			));

			//_debug_array($htracker);

			if( is_array($project_list) )
			{
				foreach( $project_list as $key => $pro )
				{
					$hours[$key] = array
					(
						'project_title'	=> $GLOBALS['phpgw']->strip_html($pro['title']) . ' [' . $GLOBALS['phpgw']->strip_html($pro['p_number']) . ']',
						'project_id'	=> $pro['project_id'],
						'project_level' => $pro['level']

					);

					if( is_array($tracking) )
					{
						foreach($tracking as $track)
						{
							if( $track['project_id'] == $pro['project_id'] )
							{
								$hours[$key]['hours'][] = array
								(
									'track_id'			=> $track['track_id'],
									'activity_title'	=> $this->boprojects->return_value('act',$track['activity_id']),
									'hours_descr'		=> $GLOBALS['phpgw']->strip_html($track['hours_descr']),
									'status'			=> $track['status'],
									'sdate_formatted'	=> $this->format_htime($track['sdate']),
									'edate'				=> $track['edate'],
									'edate_formatted'	=> $this->format_htime($track['edate']),
									'remark'			=> nl2br($GLOBALS['phpgw']->strip_html($track['remark'])),
									'wh'				=> $this->sohours->format_wh($track['minutes']),
									'journey'			=> $this->sohours->format_wh($track['t_journey'])
								);
							}
						}
					}
				}
				//_debug_array($hours);
				return $hours;
			}
		}

		function ttracker( $values )
		{
			if( !isset($values['action']) )
			{
				$values['action'] = isset($values['start']) ? 'start' : (isset($values['stop']) ? 'stop' : (isset($values['pause']) ? 'pause' : (isset($values['continue']) ? 'continue' : 'edit')));
			}

			switch( $values['action'] )
			{
				case 'save':
					$this->sohours->save_ttracker();
					break;
				default:
					$this->sohours->ttracker($values);
					break;
			}
		}

		function read_single_track( $track_id )
		{
			$hours = $this->sohours->read_single_track($track_id);

			//_debug_array($hours);
			$hour = array
			(
				'track_id'			=> $hours['track_id'],
				'project_id'		=> $hours['project_id'],
				'wh'				=> $hours['minutes']>0?$this->sohours->format_wh($hours['minutes']):0,
				't_journey'			=> $hours['t_journey']>0?$this->sohours->format_wh($hours['t_journey']):0,
				'hours_descr'		=> $GLOBALS['phpgw']->strip_html($hours['hours_descr']),
				'sdate'				=> $hours['sdate'],
				'edate'				=> $hours['edate'],
				'activity_id'		=> $hours['activity_id'],
				'remark'			=> nl2br($GLOBALS['phpgw']->strip_html($hours['remark'])),
				'sdate_formatted'	=> $this->hdate_format($hours['sdate']),
				'edate_formatted'	=> $hours['edate']>0?$this->hdate_format($hours['edate']):0,
				'stime_formatted'	=> $this->format_htime($hours['sdate']),
				'etime_formatted'	=> $this->format_htime($hours['edate'])
			);

			return $hour;
		}

		function is_booked( $hours_id )
		{
			$hours = $this->read_single_hours($hours_id);

			if ($hours['booked'] == 'Y')
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function set_booked( $values )
		{
			$this->sohours->set_booked($values);
		}

		function build_controlling_matrix( $account_id, $start_date, $end_date )
		{
			if( $account_id != $GLOBALS['phpgw_info']['user']['account_id'] )
			{	// read projects for account_id
				$this->boprojects->soprojects->account = $account_id;
				$this->boprojects->soprojects->member = $this->boprojects->soprojects->get_acl_projects();
			}

			$projects		= $this->boprojects->soprojects->get_projects_tree();
			$hoursResult	= $this->sohours->get_dayhours($account_id, $start_date, $end_date);
			$days			= array();
			$start_date		= mktime(0, 0, 0, date("m", $start_date), date("d", $start_date), date("Y", $start_date));

			foreach( $projects as $key => $value )
			{
				if( ($value['status'] == 'archive') || ($value['status'] == 'nonactive') )
				{
					continue;
				}

				$j = $start_date;
				while( $j <= $end_date )
				{
					$days[$j] = 0;
					// use +1day instead of +86400s because we could have daylight saving time
					$j = mktime(0, 0, 0, date("m", $j), date("d", $j)+1, date("Y", $j));
				}

				$matrix[] = array
				(
					'id'      => $value['id'],
					'title'   => str_repeat('&nbsp;&nbsp;&nbsp;', (substr_count($key, '.') - 1)).$value['title'],
					'pnumber' => $value['pnumber'],
					'enddate' => $value['enddate'],
					'days'    => $days
				);
			}

			while( $hoursResult->next_record() )
			{
				for( $i = 0; $i < count($matrix); $i++ )
				{
					if( $matrix[$i]['id'] == $hoursResult->f(1) )
					{
				//		$matrix[$i]['days'][$hoursResult->f(0)] = $hoursResult->f(2);
						$matrix[$i]['days'][date("Ymd",$hoursResult->f(0))] = $hoursResult->f(2);
						break;
					}
				}
			}

			if( $account_id != $GLOBALS['phpgw_info']['user']['account_id'] )
			{	// restore projects for current user
				$this->boprojects->soprojects->account = $GLOBALS['phpgw_info']['user']['account_id'];
				$this->boprojects->soprojects->member = $this->boprojects->soprojects->get_acl_projects();
			}

			return $matrix;
		}

		function export_controlling_sheet( $start, $end )
		{
			$projects	= $this->boprojects->soprojects->get_projects_tree();
			$export		= lang('User').":\t".$GLOBALS['phpgw_info']['user']['account_lid']."\t".$GLOBALS['phpgw_info']['user']['account_id']."\n\n\n";
			$export		.= "\t".lang('Project')."\t".lang('project id')."\t".lang('Customer')."\t";

			for( $j = $start; $j <= $end; $j += 86400 )
			{
				$export .= "\t".date('d.m.Y', $j);
			}

			$export .= "\n";

			foreach( $projects as $key => $value )
			{
				if( ($value['status'] == 'archive') || ($value['status'] == 'nonactive') )
				{
					continue;
				}

				$org = '';
				if( $value['customer_org'] != 0 )
				{
					$org_data = $this->boprojects->read_single_contact_org($value['customer_org']);
					if( $org_data && isset($org_data[0]) )
					{
						$org = $org_data[0]['org_name'];
					}
				}

				$export .= $value['id']."\t".str_repeat('   ', (substr_count($key, '.') - 1)).$value['title']."\t";
				$export .= $value['pnumber']."\t".$org."\t".lang('work time');
				$export .= "\n";
				$export .= "\t\t\t\t".lang('travel time');
				$export .= "\n";
				$export .= "\t\t\t\t".lang('description');
				$export .= "\n";
			}

			return $export;
		}

		function _time2minutes( $time_str )
		{
			if( !$time_str )
			{
				return 0;
			}

			if( strpos($time_str, ',') )
			{
				$timeparts = explode(',', $time_str);
				$minfactor = 0.6;
			}
			elseif( strpos($time_str, '.') )
			{
				$timeparts = explode('.', $time_str);
				$minfactor = 0.6;
			}
			elseif( strpos($time_str, ':') )
			{
				$timeparts = explode(':', $time_str);
				$minfactor = 1;
			}
			else
			{ // full hours
				return 60 * intval($time_str);
			}

			if( !is_array($timeparts) || (count($timeparts) < 2) )
			{
				return false;
			}

			if( ($minfactor != 1) && ($timeparts[1] < 10) )
			{	// for inputs like ,1 ... ,9  examble 0,5 h
				$timeparts[1] = $timeparts[1] * 10;
			}

			return intval($timeparts[0]) * 60 + intval($timeparts[1]) * $minfactor;
		}

		function build_import_controlling_sheet( $lines, &$error )
		{
			for( $i = 0; $i < count($lines); $i++)
			{
				//echo $i."->".$lines[$i]."<br>";
				if( $i == 0 ) //get account_id
				{
					$accountinfo = explode("\t", $lines[$i]);

					if( !$this->boprojects->isprojectadmin('pad') && !$this->boprojects->isprojectadmin('pmanager') )
					{
						if( $GLOBALS['phpgw_info']['user']['account_id'] != (int) $accountinfo[2] )
						{
							$error = lang('could not verify account id');
							break;
						}
					}
				}

				if( $i == 3 ) //days
				{
					$days = explode("\t", $lines[$i]);
					for( $j = 5; $j < count($days); $j++)
					{
						$dateparts = explode('.', $days[$j]);
						$days[$j] = mktime(0, 0, 0, $dateparts[1], $dateparts[0], $dateparts[2]);
					}
				}

				if( $i > 3 ) //here we go
				{
					// get 3 lines for one project (worktime, journey, description)
					$project = explode("\t", $lines[$i]);
					++$i;
					$journey = explode("\t", $lines[$i]);
					++$i;
					$descrip = explode("\t", $lines[$i]);

					$validprojectid = $this->boprojects->soprojects->exists(array( 'project_id' => $project[0] ));
					$employees = $this->boprojects->get_acl_for_project($project[0]);

					if( is_array($employees) )
					{
						if( $this->boprojects->isprojectadmin('pad') || $this->boprojects->isprojectadmin('pmanager') )
						{
							$validemployee = in_array($accountinfo[2], $employees);
						}
						else
						{
							$validemployee = in_array($this->account,$employees);
						}
					}
					for( $j=5; $j < count($project); $j++ )
					{
						$entryerror = '';

						if( isset($project[$j]) )
						{
							$project[$j] = $this->_time2minutes($project[$j]);
						}
						else
						{
							$project[$j] = 0;
						}

						if( isset($journey[$j]) )
						{
							$journey[$j] = $this->_time2minutes($journey[$j]);
						}
						else
						{
							$journey[$j] = 0;
						}

						if( !$validprojectid )
						{
							$entryerror = lang('invalid project id');
						}

						if( !$days[$j] )
						{
							$entryerror = lang('invalid date');
						}

						if( !$validemployee )
						{
							$entryerror = lang('employee not on this project');
						}

						if( $journey[$j]+$project[$j] >= 24*60 )
						{
							$entryerror = lang('too many hours for this day');
						}

						if( ($project[$j] > 0) || ($journey[$j] > 0) )
						{
							if( isset($descrip[$j]) && ($descrip[$j]!='') )
							{
								$description = $descrip[$j];
							}
							else
							{
								$description = lang('imported hours');
							}

							$savematrix[] = array
							(
								'employee'		=> $accountinfo[2],
								'projectid'		=> $project[0],
								'projecttitle'	=> $project[1],
								'projectnumber'	=> $project[2],
								'customer_org'	=> $project[3],
								'date'			=> $days[$j],
								'time'			=> $project[$j],
								'journey'		=> $journey[$j],
								'description'	=> $description,
								'error'			=> $entryerror
							);
						}
					}
				}
			}

			return $savematrix;
		}

		function build_acitivity_matrix( $projectID, $start = 0, $end = 0 )
		{
			if ( $this->boprojects->isprojectadmin('pad') || $this->boprojects->isprojectadmin('pmanager') )
			{
				$coordinator = $this->boprojects->return_value('co', $projectID);
			}
			else
			{
				$coordinator = -1;
			}

			$projects = $this->boprojects->soprojects->get_projects_tree($projectID, array('project_id', 'parent', 'title', 'p_number', 'direct_work', 'p_number', 'end_date', 'time_planned', 'e_budget', 'acc_factor', 'acc_factor_d'), $coordinator);
			$this->filter = 'employee';
			$i=0;

			foreach( $projects as $key => $value )
			{
				$matrix[$i]['project']			= $value;
				$matrix[$i]['project']['title']	= str_repeat('&nbsp;&nbsp;&nbsp;', (substr_count($key, '.') - 1)).$value['title'];
				$employees						= $this->boprojects->get_acl_for_project($value['id']);
				$this->project_id				= $value['id'];

				for( $j = 0; $j < count($employees); $j++ )
				{
					$matrix[$i]['employee'][$employees[$j]] = array();
					$this->sohours->employee = $employees[$j]; // dirty hack - list_hours not flexible engough
					$employee_hours = $this->list_hours($start, $end);

					if( is_array($employee_hours) )
					{
						for( $k = 0; $k < count($employee_hours); $k++ )
						{
							$matrix[$i]['employee'][$employees[$j]][] = array('id'          => $employee_hours[$k]['hours_id'],
							                                                  'description' => $employee_hours[$k]['hours_descr'],
							                                                  'statusout'   => $employee_hours[$k]['status'],
							                                                  'minutes'     => $employee_hours[$k]['minutes'],
							                                                  'minutesout'  => $employee_hours[$k]['wh']['whwm'],
							                                                  't_minutes'   => $employee_hours[$k]['wh_t_journey']['wminutes'],
							                                                  't_minutesout'=> $employee_hours[$k]['wh_t_journey']['whwm'],
							                                                  'date'        => $employee_hours[$k]['sdate_formatted']['date'],
							                                                  'start'       => $employee_hours[$k]['sdate_formatted']['time'],
							                                                  'end'         => $employee_hours[$k]['edate_formatted']['time']
							                                                 );
						}
					}
				}

				$i++;
			}
			return $matrix;
		}

		function get_emp_worktimes($employee, $start, $end)
		{
			$emp_worktimes = array();
			$result = $this->sohours->get_emp_worktimes($employee, $start, $end);
			if( $result )
			{
				while( $result->next_record() )
				{
					$emp_worktimes[$result->f('project_id')] = array
					(
						'sum_minutes_worktime' => intval($result->f('sum_minutes_worktime')),
						'sum_minutes_journey'  => intval($result->f('sum_minutes_journey')),
						'sum_minutes_all'      => intval($result->f('sum_minutes_all'))
					);
				}
			}
			return $emp_worktimes;
		}

		function get_emp_activities( $project_id, $sdate, $edate, $account_id )
		{

			$params = array
			(
					'project_id' => $project_id,
					'filter' => 'employee',
					'status' => 'all',
					'limit' => false,
					'order' => 'end_date',
					'employee' => $account_id
			);

			$subs = $this->boprojects->get_sub_projects($params);
			$x = 0;

			for($i = 0; $i <= count($subs); $i++ )
			{
				$values_hours = array
				(
					'project_id'	=> $subs[$i]['project_id'],
					'filter'		=> 'employee',
					'action'		=> 'all',
					'limit'			=> false,
					'order'			=> 'end_date',
					'employee'		=> $account_id
				);

				$hours[$i] = $this->sohours->read_hours($values_hours);

				for( $j = 0; $j <= count($hours[$i]); $j++ )
				{
					if( ($hours[$i][$j]['sdate'] >= $sdate) && ($hours[$i][$j]['edate'] <= $edate) && ($hours[$i][$j]['billable'] == 'Y'))
					{
						$values[$x] = array
						(
							'date'		=> $hours[$i][$j]['sdate'], //date("d.m.Y", $hours[$i][$j]['sdate']),
							'begin'		=> $hours[$i][$j]['sdate'], //date("H:i", $hours[$i][$j]['sdate']),
							'end'		=> $hours[$i][$j]['edate'], //date("H:i", $hours[$i][$j]['edate']),
							'duration'	=> $hours[$i][$j]['minutes'],
							'drivetime'	=> $hours[$i][$j]['t_journey'],
							'distance'	=> $hours[$i][$j]['km_distance'],
							'descr'		=> $hours[$i][$j]['hours_descr'],
							'notes'		=> $hours[$i][$j]['remark'],
							'surcharge' => $hours[$i][$j]['surcharge']
						);
						$x++;
					}
				}
			}

			$activities = $this->boprojects->array_natsort_list($values,'date');

			return $activities;

		}
	}
?>
