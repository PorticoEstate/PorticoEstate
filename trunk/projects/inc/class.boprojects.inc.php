<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: class.boprojects.inc.php,v 1.168 2006/12/05 19:40:45 sigurdne Exp $
	* $Source: /sources/phpgroupware/projects/inc/class.boprojects.inc.php,v $
	*/

	class boprojects
	{
		var $action;
		var $start;
		var $query;
		var $filter;
		var $order;
		var $sort;
		var $cat_id;
		var $status;
		var $html_output;
		var $check;

		var $public_functions = array
		(
			'save_sessiondata'					=> true,
			'cached_accounts'					=> true,
			'list_projects'						=> true,
			'check_perms'						=> true,
			'check_values'						=> true,
			'select_project_list'				=> true,
			'save_project'						=> true,
			'read_single_project'				=> true,
			'delete_pa'							=> true,
			'exists'							=> true,
			'employee_list'						=> true,
			//'read_abook'						=> true,
			'read_single_contact'				=> true,
			'read_single_contact_org'			=> true,
			'return_value'						=> true,
			'change_owner'						=> true,
			'async_worktime_statusmail'			=> true,
			'async_worktime_warnmail'			=> true,
			'async_workhours_booking'			=> true,
			'test_async_worktime_statusmail'	=> true,
			'test_async_worktime_warnmail'		=> true,
			'test_async_workhours_booking'		=> true
		);

		function boprojects( $is_active=false, $action = '' )
		{
			$this->soprojects	= CreateObject('projects.soprojects');

			$this->sohours		= CreateObject('projects.soprojecthours');
			$this->soconfig		= $this->soprojects->soconfig;

			$this->contacts		= CreateObject('phpgwapi.contacts');
			$this->cats			= CreateObject('phpgwapi.categories');

			$this->debug		= false;

			$this->siteconfig	= $this->soprojects->siteconfig;

			$this->account					= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->grants					= $GLOBALS['phpgw']->acl->get_grants('projects');
			$this->grants[$this->account]	= PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;

			$this->html_output	= true;

			if ( $is_active )
			{
				$this->read_sessiondata($action);
				$this->use_session = true;

				$_start			= isset( $_REQUEST['start'] ) ? $_REQUEST['start'] : '';
				$_query			= isset( $_REQUEST['query'] ) ? $_REQUEST['query'] : '';
				$_sort			= isset( $_REQUEST['sort'] ) ? $_REQUEST['sort'] : '';
				$_order			= isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : '';
				$_cat_id		= isset( $_REQUEST['cat_id'] ) ? $_REQUEST['cat_id'] : '';
				$_filter		= isset( $_REQUEST['filter'] ) ? $_REQUEST['filter'] : '';
				$_status		= isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';
				$_state			= isset( $_REQUEST['state'] ) ? $_REQUEST['state'] : '';
				$_project_id	= isset( $_REQUEST['project_id'] ) ? $_REQUEST['project_id'] : 0;

				if( !empty($_start) || ($_start == '0') || ($_start == 0) )
				{
					if( $this->debug )
					{
						echo '<br>overriding $start: "' . $this->start . '" now "' . $_start . '"';
					}

					$this->start = $_start;
				}

				if( ( empty($_query) && !empty($this->query) ) || !empty($_query) )
				{
					$this->query  = $_query;
				}

				if( isset($_status) && !empty($_status) )
				{
					$this->status = $_status;
				}

				if( isset($_status) && !empty($_status) )
				{
					$this->status = $_status;
				}

				if( isset($_state) && !empty($_state) )
				{
					$this->state = $_state;
				}

				if( isset($_cat_id) && !empty($_cat_id) )
				{
					$this->cat_id = $_cat_id;
				}

				if( isset($_project_id) && !empty($_project_id) )
				{
					$this->project_id = $_project_id;
				}
				else
				{
					unset( $this->project_id );
				}

				/*if($_project_id)
				{
					$this->project_id = $_project_id;
				}*/

				if(isset($_sort) && !empty($_sort))
				{
					if($this->debug)
					{
						echo '<br>overriding $sort: "' . $this->sort . '" now "' . $_sort . '"';
					}
					$this->sort   = $_sort;
				}

				if(isset($_order) && !empty($_order))
				{
					if($this->debug)
					{
						echo '<br>overriding $order: "' . $this->order . '" now "' . $_order . '"';
					}
					$this->order  = $_order;
				}

				if(isset($_filter) && !empty($_filter))
				{
					if($this->debug) { echo '<br>overriding $filter: "' . $this->filter . '" now "' . $_filter . '"'; }
					$this->filter = $_filter;
				}
				$this->limit = true;
			}
		}

		function type( $action )
		{
			$column = '';

			switch ( $action )
			{
				case 'mains':
					$column = 'projects_mains';
					break;
				case 'subs':
					$column = 'projects_subs';
					break;
				case 'pad':
					$column = 'projects_pad';
					break;
				case 'amains':
					$column = 'projects_amains';
					break;
				case 'asubs':
					$column = 'projects_asubs';
					break;
				case 'ustat':
					$column = 'projects_ustat';
					break;
				case 'pstat':
					$column = 'projects_pstat';
					break;
				case 'act':
					$column = 'projects_act';
					break;
				case 'pad':
					$column = 'projects_pad';
					break;
				case 'role':
					$column = 'projects_role';
					break;
				case 'accounting':
					$column = 'projects_accounting';
					break;
				case 'hours':
					$column = 'projects_hours';
					break;
			}

			return $column;
		}

		function save_sessiondata( $data, $action )
		{
			if ( $this->use_session )
			{
				$column = $this->type($action);
				$GLOBALS['phpgw']->session->appsession('session_data', $column, $data);
			}
		}

		function read_sessiondata( $action )
		{
			$column = $this->type($action);
			$data = $GLOBALS['phpgw']->session->appsession('session_data', $column);

			$this->start		= isset( $data['start'] ) && $data['start'] ? $data['start'] : '';
			$this->query		= isset( $data['query'] ) && $data['query'] ? $data['query'] : '';
			$this->filter		= isset( $data['filter'] ) && $data['filter'] ? $data['filter'] : '';
			$this->order		= isset( $data['order'] ) && $data['order'] ? $data['order'] : '';
			$this->sort			= isset( $data['sort'] ) && $data['sort'] ? $data['sort'] : '';
			$this->cat_id		= isset( $data['cat_id'] ) && $data['cat_id'] ? $data['cat_id'] : '';
			$this->status		= isset( $data['status'] ) && $data['status'] ? $data['status'] : '';
			$this->state		= isset( $data['state'] ) && $data['state'] ? $data['state'] : '';
			$this->project_id	= isset( $data['project_id'] ) && $data['project_id'] ? $data['project_id'] : '';
		}

		function check_perms( $has, $needed )
		{
			return ( !!($has & $needed) == true );
		}

		function edit_perms( $pro )
		{
			$type = isset( $pro['type'] ) ? $pro['type'] : 'edit';

			switch( $type )
			{
				case 'delete':
					$acl = PHPGW_ACL_DELETE;
				default:
					$acl = PHPGW_ACL_EDIT;
			}

			if( $pro['project_id'] && !$pro['coordinator'] )
			{
				$pro['coordinator']	= $this->soprojects->return_value('co', $pro['project_id']);
			}

			if( $this->check_perms($this->grants[$pro['coordinator']],$acl) || $pro['coordinator'] == $this->account )
			{
				return true;
			}

			if( $this->isprojectadmin('pad') || $this->isprojectadmin('pmanager') )
			{
				return true;
			}

			switch( $pro['action'] )
			{
				case 'subs':
					if( $pro['main_co'] )
					{
						$main_co = $pro['main_co'];
					}
					else
					{
						if( $pro['project_id'] && !$pro['main'] )
						{
							$pro['main'] = $this->soprojects->return_value('main', $pro['project_id']);
						}
						$main_co = $this->soprojects->return_value('co',$pro['main']);
					}
					if( $this->check_perms($this->grants[$main_co],$acl) || $main_co == $this->account )
					{
						return true;
					}
					if( $pro['parent_co'] )
					{
						$parent_co = $pro['parent_co'];
					}
					else
					{
						if( $pro['project_id'] && !$pro['parent'] )
						{
							$pro['parent'] = $this->soprojects->return_value('parent', $pro['project_id']);
						}
						$parent_co = $this->soprojects->return_value('co',$pro['parent']);
					}
					if( $this->check_perms($this->grants[$parent_co],$acl) || $parent_co == $this->account )
					{
						return true;
					}
					break;
			}
			return false;
		}

		function add_perms( $pro )
		{
			if( $this->status == 'archive' )
			{
				return false;
			}

			switch( $pro['action'] )
			{
				case 'mains':
					if( intval($this->cat_id) > 0 )
					{
						$cat = $this->cats->return_single($this->cat_id);

						if( $cat[0]['app_name'] == 'phpgw' || $cat[0]['owner'] == -1 )
						{
							return true;
						}
						else if( $this->check_perms($this->grants[$cat[0]['owner']],PHPGW_ACL_ADD) || $cat[0]['owner'] == $this->account )
						{
							return true;
						}
					}
					else if( intval($this->cat_id) == 0 )
					{
						return true;
					}
					else if( $this->check_perms($this->grants[$pro['coordinator']],PHPGW_ACL_ADD) || $pro['coordinator'] == $this->account && !is_array($cat) )
					{
						return true;
					}
					else if( $this->isprojectadmin('pad') || $this->isprojectadmin('pmanager') && !is_array($cat) )
					{
						return true;
					}
					break;
				case 'subs':
					if( $this->check_perms($this->grants[$pro['coordinator']],PHPGW_ACL_ADD) || $pro['coordinator'] == $this->account )
					{
						return true;
					}
					//$main_co = $this->soprojects->return_value('co',$pro['main']);
					if( $this->check_perms($this->grants[$pro['main_co']],PHPGW_ACL_ADD) || $pro['main_co'] == $this->account )
					{
						return true;
					}
					$parent_co = $this->soprojects->return_value('co',$pro['parent']);
					if( $this->check_perms($this->grants[$parent_co],PHPGW_ACL_ADD) || $parent_co == $this->account )
					{
						return true;
					}
					if( $this->isprojectadmin('pad') || $this->isprojectadmin('pmanager') )
					{
						return true;
					}
					break;
			}
			return false;
		}

		function cached_accounts( $account_id )
		{
			$this->accounts = CreateObject('phpgwapi.accounts',$account_id);

			$this->accounts->read_repository();

			$cached_data[$this->accounts->data['account_id']]['account_id']		= $this->accounts->data['account_id'];
			$cached_data[$this->accounts->data['account_id']]['account_lid']	= $this->accounts->data['account_lid'];
			$cached_data[$this->accounts->data['account_id']]['firstname']		= $this->accounts->data['firstname'];
			$cached_data[$this->accounts->data['account_id']]['lastname']		= $this->accounts->data['lastname'];

			return $cached_data;
		}

		function return_date()
		{
			$date = array
			(
				'month'		=> $GLOBALS['phpgw']->common->show_date(time(), 'n'),
				'day'		=> $GLOBALS['phpgw']->common->show_date(time(), 'd'),
				'year'		=> $GLOBALS['phpgw']->common->show_date(time(), 'Y')
			);

			$date['daydate']		= mktime(12, 0, 0, $date['month'], $date['day'], $date['year']);
			$date['monthdate']		= mktime(12, 0, 0, $date['month']+2, 0, $date['year']);
			$date['monthformatted'] = $GLOBALS['phpgw']->common->show_date($date['monthdate'],'n/Y');

			return $date;
		}

		/*function read_abook($start, $query, $filter, $sort, $order)
		{
			$cols = array('contact_id', 'per_first_name','per_last_name','org_name','people');
			//$criteria = array('my_preferred' => 'Y');

			$entries = $this->contacts->get_persons($cols, $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'], $start, $order, $sort);//, $criteria);

			$this->total_records = $this->contacts->total_records;
			return $entries;
		}*/

		function read_single_contact( $abid )
		{
			$cols = array('contact_id', 'per_first_name','per_last_name','org_name','people');
			$criteria = array('contact_id' => intval($abid));//, 'my_preferred' => 'Y');

			return $this->contacts->get_persons($cols, $limit='',$start = '',$order='', $sort='',$criteria);
			//_debug_array($co);
		}

		function read_single_contact_org( $customer_org )
		{
			$cols = array('contact_id', 'org_name');
			$criteria = array('contact_id' => intval($customer_org));

			return $this->contacts->get_orgs($cols, $limit='',$start = '',$order='', $sort='', $criteria);
		}

		function return_value( $action,$item )
		{
			return $this->soprojects->return_value($action,$item);
		}

		function read_projects_acl( $useronly = true )
		{
			$aclusers	= $GLOBALS['phpgw']->acl->get_ids_for_location('run',1,'projects');
			$acl_users	= $GLOBALS['phpgw']->accounts->return_members($aclusers);

			if( $useronly )
			{
				$employees	= $acl_users['users'];
				return $employees;
			}
			else
			{
				return $acl_users;
			}
		}

		function read_projectsmembers_acl( $project_id = false )
		{
			$members = $this->soprojects->get_acl_project_members($project_id);
			return $members;
		}

		// a lot of work-arounds added - fips
		function get_acl_for_project( $project_id = 0 )
		{
			while( !count($empl) && $project_id )
			{
				$myproject = $this->soprojects->read_single_project($project_id);

				$empl = $GLOBALS['phpgw']->acl->get_ids_for_location($project_id, 7, 'project_members');
				if(!count($empl) || $empl[0] == '')
				{
					$empl = null;
					$project_id = $myproject['parent'];
				}
			}
			if( count($empl) )
			{
				return $empl;
			}
			return false;
		}

		function get_employee_projects( $account_id = 0 )
		{
			if( intval($account_id) > 0 )
			{
				return $this->soprojects->get_employee_projects($account_id);
			}
			return false;
		}

		function selected_employees( $data = 0 )
		{
			$project_id = intval($data['project_id']);
			$pro_parent = intval($data['pro_parent']);

			if( intval($project_id) > 0 )
			{
				$emps = $this->get_acl_for_project($project_id);
			}
			else
			{
				$emps = $this->read_projects_acl();
			}

			if( isset($data['action']) && $data['action'] == 'subs' )
			{
				$parent_select = $this->get_acl_for_project($pro_parent);

				$k = 0;
				if( is_array($parent_select) )
				{
					for( $i=0; $i < count($emps); $i++ )
					{
						if( in_array($emps[$i], $parent_select) )
						{
							$emp[$k] = $emps[$i];
							$k++;
						}
					}
				}
				if( is_array($emp) )
				{
					$emps = array();
					$emps = $emp;
				}
			}

			if( $data['admins_included'] == true )
			{
				$co = $this->soprojects->return_value('co',$project_id?$project_id:$pro_parent);

				//echo 'CO:' . $co;
				if( is_array($emps) && !in_array($co,$emps) )
				{
					$i = count($emps);
					$emps[$i] = $co;
				}
				// BUG: doppelte Eintr�ge f�r Projekt-Leiter
				// daher wurde nachfolgender Code auskommentieert
				//else
				//{
				//	$emps[0] = $co;
				//}
			}
			//_debug_array($emps);

			for( $i = 0; $i < count($emps); $i++ )
			{
				if( !$emps[$i] )
					continue;

				//$this->accounts = CreateObject('phpgwapi.accounts',$emps[$i]);
				//$this->accounts->read_repository();

				if( $data['roles_included'] == true )
				{
					$role_name = $this->soprojects->return_value('role',$project_id,$emps[$i]);
				}

				$GLOBALS['phpgw']->accounts->get_account_name($emps[$i],$lid,$fname,$lname);

				$empl[] = array
				(
					'account_id'		=> $emps[$i],
					'account_lid'		=> $lid,
					'account_firstname'	=> $fname,
					'account_lastname'	=> $lname,
					'account_fullname'	=> $GLOBALS['phpgw']->common->display_fullname($lid,$fname,$lname),
					'role_name'			=> isset($role_name)?$role_name:''
				);
			}

			if( count($empl) )
			{
				asort($empl);
				reset($empl);
			}
			//_debug_array($empl);
			return $empl;
		}

		function get_time_used( $data )
		{
			if( $this->siteconfig['accounting'] == 'activity' )
			{
				return $this->sohours->get_activity_time_used($data);
			}
			else
			{
				return $this->sohours->get_time_used($data);
			}
		}

		function calculate_budget( $data = 0 )
		{
			if( $this->siteconfig['accounting'] == 'activity' )
			{
				$budget = $this->sohours->calculate_activity_budget(array('project_id' => $data['project_id'],'project_array' => $data['project_array']));
				return $data['is_billable']?$budget['bbudget']:$budget['budget'];
			}
			else
			{
				$factor_per_minute = $data['factor']/60;

				$surcharge = $data['surcharge']>0 ? $this->return_value('charge',$data['surcharge']) : 0;
				$budget = round($factor_per_minute*$data['minutes'],2);
				if( $surcharge > 0 )
				{
					$add_surcharge = ($budget*$surcharge)/100;
					return $budget + $add_surcharge;
				}
				else
				{
					return $budget;
				}
			}
		}

// BUDGET FOR ACTIVIES

		function get_activity_budget( $params )
		{
			$subs = $this->get_sub_projects($params);
			if( is_array($subs) )
			{
				$i = 0;
				foreach( $subs as $sub )
				{
					$sub_pro[$i] = $sub['project_id'];
					$i++;
					if($sub['parent'] == $params['project_id'])
					{
						$sum_budget += $sub['budget'];
						$sum_ptime += $sub['time_planned'];
					}
				}

				$acc = array();

				if( $params['page'] == 'planned' )
				{
					$acc['pbudget_jobs']	= $sum_budget;
					$ptimejobs				= $this->sohours->format_wh($sum_ptime);
					$acc['ptime_jobs']		= $ptimejobs['whwm'];
					$acc['ptime_jobs_min']	= $sum_ptime;

					return $acc;
				}
			}
			$uhours_pro			= $this->sohours->get_activity_time_used(array('project_id' => $params['project_id']));
			$uhours_pro_nobill	= $this->sohours->get_activity_time_used(array('project_id' => $params['project_id'],'no_billable' => true));
			$uhours_pro_bill	= $uhours_pro - $uhours_pro_nobill;

			$formatted_uhours_pro			= $this->sohours->format_wh($uhours_pro);
			$formatted_uhours_pro_bill		= $this->sohours->format_wh($uhours_pro_bill);
			$formatted_uhours_pro_nobill	= $this->sohours->format_wh($uhours_pro_nobill);

			$acc['uhours_pro']				= $formatted_uhours_pro['whwm'];
			$acc['uhours_pro_nobill']		= $formatted_uhours_pro_nobill['whwm'];
			$acc['uhours_pro_bill']			= $formatted_uhours_pro_bill['whwm'];
			$acc['uhours_pro_wminutes']		= $uhours_pro;

			$formatted_ahours_pro			= $this->sohours->format_wh($params['ptime'] - $uhours_pro);
			$acc['ahours_pro']				= $formatted_ahours_pro['whwm'];

			$uhours_jobs		= $this->sohours->get_activity_time_used(array('project_array' => $sub_pro));
			$uhours_jobs_nobill	= $this->sohours->get_activity_time_used(array('project_array' => $sub_pro,'no_billable' => true));
			$uhours_jobs_bill	= $uhours_jobs - $uhours_jobs_nobill;

			$formatted_uhours_jobs			= $this->sohours->format_wh($uhours_jobs);
			$formatted_uhours_jobs_bill		= $this->sohours->format_wh($uhours_jobs_bill);
			$formatted_uhours_jobs_nobill	= $this->sohours->format_wh($uhours_jobs_nobill);

			$acc['uhours_jobs']				= $formatted_uhours_jobs['whwm'];
			$acc['uhours_jobs_nobill']		= $formatted_uhours_jobs_nobill['whwm'];
			$acc['uhours_jobs_bill']		= $formatted_uhours_jobs_bill['whwm'];
			$acc['uhours_jobs_wminutes']	= $uhours_jobs;

			$formatted_ahours_jobs			= $this->sohours->format_wh($params['ptime'] - $uhours_jobs);
			$acc['ahours_jobs']				= $formatted_ahours_jobs['whwm'];

			if( $params['page'] == 'budget' )
			{
				$acc['u_budget'] = $this->calculate_budget(array('project_id' => $params['project_id']));
				$acc['b_budget'] = $this->calculate_budget(array('project_id' => $params['project_id'],'is_billable' => true));

				$acc['u_budget_jobs'] = $this->calculate_budget(array('project_array' => $sub_pro));
				$acc['b_budget_jobs'] = $this->calculate_budget(array('project_array' => $sub_pro,'is_billable' => true));
			}

			return $acc;
		}

		function get_budget( $params )
		{
			if($this->siteconfig['accounting'] == 'activity')
			{
				return $this->get_activity_budget($params);
			}
			else
			{
				if( !$params['billable'] )
				{
					$params['billable'] = $this->return_value('billable', $params['project_id']);
				}

				$subs = $this->get_sub_projects($params);

				$acc = array();

				$acc['is_leaf'] = true;
				if( is_array($subs) )
				{
					$i = 0;
					$sum_budget = 0;
					$sum_ptime = 0;

					foreach( $subs as $sub )
					{
						switch( $sub['billable'] )
						{
							case 'N':
								$sub_pro_nobill[$i]	= $sub['project_id'];
								break;
							case 'Y':
								$sub_pro_bill[$i] = $sub['project_id'];
								break;
						}

						$sub_pro[$i] = $sub['project_id'];

						++$i;
						if( $sub['parent'] == $params['project_id'] )
						{
							$sum_budget += $sub['budget'] + $sub['budget_childs'];
							$sum_ptime += $sub['time_planned'] + $sub['time_planned_childs'];
						}
					}

					$acc['is_leaf'] = ($i==1);
				}

				if( $params['page'] == 'planned' )
				{
					$acc['pbudget_jobs']	= $sum_budget;
					$ptimejobs				= $this->sohours->format_wh($sum_ptime);
					$acc['ptime_jobs']		= $ptimejobs['whwm'];
					$acc['ptime_jobs_min']	= $sum_ptime;

					return $acc;
				}

				$acc['pbudget_jobs']	= $sum_budget;
				$ptimejobs				= $this->sohours->format_wh($sum_ptime);
				$acc['ptime_jobs']		= $ptimejobs['whwm'];
				$acc['ptime_jobs_min']	= $sum_ptime;

				// ------ project only -------

				$h_pro = $this->sohours->get_time_used(array('project_id' => $params['project_id']));
				//echo 'boprojects->get_budget: time used project only:';
				//_debug_array($h_pro);

				$uhours_pro_bill = $uhours_pro_nobill = $uhours_pro = 0;
				if( is_array($h_pro) )
				{
					foreach( $h_pro as $hp )
					{
						if( $hp['billable'] == 'Y' )
						{
							$uhours_pro_bill += $hp['minutes'];
						}
						elseif( $hp['billable'] == 'N' )
						{
							$uhours_pro_nobill += $hp['minutes'];
						}
					}
					$uhours_pro = $uhours_pro_bill + $uhours_pro_nobill;
				}
				else
				{
					$uhours_pro_bill = $uhours_pro_nobill = $uhours_pro = 0;
				}

				if( $params['billable'] == 'N' )
				{
					$uhours_pro_nobill	= $uhours_pro;
					$uhours_pro_bill	= 0;
				}

				$formatted_uhours_pro			= $this->sohours->format_wh($uhours_pro);
				$formatted_uhours_pro_bill		= $this->sohours->format_wh($uhours_pro_bill);
				$formatted_uhours_pro_nobill	= $this->sohours->format_wh($uhours_pro_nobill);

				$acc['uhours_pro']			= $this->sohours->min2str($uhours_pro);
				$acc['uhours_pro_nobill']	= $this->sohours->min2str($uhours_pro_nobill);
				$acc['uhours_pro_bill']		= $this->sohours->min2str($uhours_pro_bill);
				$acc['uhours_pro_wminutes']	= $uhours_pro;

				$acc['utime_item']			= $uhours_pro;
				$acc['utime_item_nobill']	= $uhours_pro_nobill;
				$acc['utime_item_bill']		= $uhours_pro_bill;

				$formatted_ahours_pro		= $this->sohours->format_wh($params['ptime'] - $uhours_pro);
				$acc['ahours_pro']			= $formatted_ahours_pro['whwm'];

				$acc['atime_item']			= $params['ptime'] - $acc['ptime_jobs_min']- $uhours_pro;

				//echo 'uhours_pro:' . $uhours_pro;
				//_debug_array($acc);

				// ------ jobs included ------

				$h_jobs_bill = $this->sohours->get_time_used(array('project_array' => $sub_pro_bill)); // project is billable

				if( is_array($h_jobs_bill) )
				{
					foreach( $h_jobs_bill as $hjb )
					{
						if( $hjb['billable'] == 'Y' )
						{
							$uhours_jobs_bill += $hjb['minutes'];
						}
						elseif( $hjb['billable'] == 'N' )
						{
							$uhours_jobs_nobill += $hjb['minutes'];
						}
					}
				}
				else
				{
					$uhours_jobs_bill = $uhours_jobs_nobill = 0;
				}

				$h_jobs_nobill = $this->sohours->get_time_used(array('project_array' => $sub_pro_nobill)); // project is not billable

				if( is_array($h_jobs_nobill) )
				{
					foreach( $h_jobs_nobill as $hjnb )
					{
						$uhours_jobs_nobill += $hjnb['minutes'];
					}
				}

				$uhours_jobs = $uhours_jobs_bill + $uhours_jobs_nobill;

				$formatted_uhours_jobs			= $this->sohours->format_wh($uhours_jobs);
				$formatted_uhours_jobs_bill		= $this->sohours->format_wh($uhours_jobs_bill);
				$formatted_uhours_jobs_nobill	= $this->sohours->format_wh($uhours_jobs_nobill);

				$acc['uhours_jobs']				= $formatted_uhours_jobs['whwm'];
				$acc['uhours_jobs_nobill']		= $formatted_uhours_jobs_nobill['whwm'];
				$acc['uhours_jobs_bill']		= $formatted_uhours_jobs_bill['whwm'];
				$acc['uhours_jobs_wminutes']	= $uhours_jobs;

				$acc['utime_sum']				= $uhours_jobs;
				$acc['utime_sum_nobill']		= $uhours_jobs_nobill;
				$acc['utime_sum_bill']			= $uhours_jobs_bill;

				$formatted_ahours_jobs		= $this->sohours->format_wh($params['ptime'] - $uhours_jobs);
				$acc['ahours_jobs']			= $formatted_ahours_jobs['whwm'];

				$acc['atime_sum']			= $params['ptime'] - $uhours_jobs;

				$acc['b_budget'] = 0;
				$acc['nb_budget'] = 0;
				$acc['u_budget'] = 0;

				if( $params['page'] == 'budget' )
				{
					switch( $params['accounting'] )
					{
						case 'project':
							if( is_array($h_pro) )
							{
								foreach( $h_pro as $hp )
								{
									if( $params['billable'] == 'Y' && $hp['billable'] == 'Y' )
									{
										$acc['b_budget'] += $this->calculate_budget(array('factor' => $params['project_accounting_factor'],'minutes' => $hp['minutes'],
																							'surcharge' => $hp['surcharge']));
									}
									else
									{
										$acc['nb_budget'] += $this->calculate_budget(array('factor' => $params['project_accounting_factor'],'minutes' => $hp['minutes'],
																							'surcharge' => $hp['surcharge']));
									}
								}
								$acc['u_budget'] = $acc['nb_budget'] + $acc['b_budget'];
							}
							break;
						case 'employee':
							$emps_pro = $this->sohours->get_employee_time_used(array('project_id' => $params['project_id']));
							//_debug_array($emps_pro);

							for( $i=0; $i < count($emps_pro); $i++ )
							{
								$factor	= $this->soconfig->return_value('acc',$emps_pro[$i]['employee']);
								//_debug_array($factor);
								if( is_array($emps_pro[$i]['hours']) )
								{
									foreach( $emps_pro[$i]['hours'] as $wh )
									{
										$wh['factor'] = 0;
										if( is_array($factor) )
										{
											for( $j=0; $j < count($factor); ++$j )
											{
												if( $wh['sdate']>=$factor[$j]['sdate'] && $wh['edate']<=$factor[$j]['edate'] )
												{
													$wh['factor'] = $factor[$j]['factor'];
												}
												else if( $factor[$j]['sdate']==0 )
												{
													$wh['factor'] = $factor[$j]['factor'];
												}
											}
										}
										$bill = $this->calculate_budget(array('factor' => $wh['factor'],'minutes' => $wh['minutes'],'surcharge' => $wh['surcharge']));

										if( $wh['billable'] == 'Y' )
										{
											$acc['b_budget'] += $bill;
										}
										else
										{
											$acc['nb_budget'] += $bill;
										}
									}
								}
							}

							$acc['u_budget'] = $acc['b_budget'] + $acc['nb_budget'];
							if($params['billable'] == 'N')
							{
								$acc['b_budget'] = 0;
							}
							//_debug_array($emps_pro);
							break;
					}

					for( $i=0; $i < count($subs); $i++ )
					{
						$sub_b_budget = $sub_nb_budget = 0;
						switch( $subs[$i]['accounting'] )
						{
							case 'project':
								$h_sub = $this->sohours->get_time_used(array('project_id' => $subs[$i]['project_id']));

								if( is_array($h_sub) )
								{
									foreach( $h_sub as $hs )
									{
										if( $subs[$i]['billable'] == 'Y' && $hs['billable'] == 'Y' )
										{
											$sub_b_budget += $this->calculate_budget(array('factor' => $subs[$i]['acc_factor'],'minutes' => $hs['minutes'],
																							'surcharge' => $hs['surcharge']));
										}
										else
										{
											$sub_nb_budget += $this->calculate_budget(array('factor' => $subs[$i]['acc_factor'],'minutes' => $hs['minutes'],
																							'surcharge' => $hs['surcharge']));
										}
									}
								}
								break;
							case 'employee':
								$emps_sub = $this->sohours->get_employee_time_used(array('project_id' => $subs[$i]['project_id']));

								for( $k=0; $k < count($emps_sub); $k++ )
								{
									$factor	= $this->soconfig->return_value('acc',$emps_sub[$k]['employee']);
									if( is_array($emps_sub[$k]['hours']) )
									{
										foreach( $emps_sub[$k]['hours'] as $wh )
										{
											$wh['factor'] = 0;
											if( is_array($factor) )
											{
												for( $j=0; $j < count($factor); ++$j )
												{
													if( $wh['sdate']>=$factor[$j]['sdate'] && $wh['edate']<=$factor[$j]['edate'] )
													{
														$wh['factor'] = $factor[$j]['factor'];
													}
													else if( $factor[$j]['sdate']==0 )
													{
														$wh['factor'] = $factor[$j]['factor'];
													}
												}
											}

											if( $subs[$i]['billable'] == 'Y' && $wh['billable'] == 'Y' )
											{
												$sub_b_budget += $this->calculate_budget(array('factor' => $wh['factor'],'minutes' => $wh['minutes'],
																								'surcharge' => $wh['surcharge']));
											}
											else
											{
												$sub_nb_budget += $this->calculate_budget(array('factor' => $wh['factor'],'minutes' => $wh['minutes'],
																								'surcharge' => $wh['surcharge']));
											}
										}
									}
								}
								break;
						}
						$nb_budget_jobs += $sub_nb_budget;
						$acc['b_budget_jobs'] += $sub_b_budget;
					}
					$acc['u_budget_jobs'] = $acc['b_budget_jobs'] + $nb_budget_jobs;
					$acc['nb_budget_jobs'] = $nb_budget_jobs;
				}

				//_debug_array($acc);
				return $acc;
			}
		}

		function get_sub_projects( $params )
		{
			switch($params['page'])
			{
				case 'planned':
					$column = 'project_id,parent,level,budget,time_planned';
					break;
				case 'hours':
//				case 'budget':			$column = 'project_id,accounting,acc_factor,billable,level'; break;
				case 'budget':
					$column = 'project_id,parent,accounting,acc_factor,billable,level,budget,time_planned';
					break;
			}
			$subs = $this->soprojects->read_projects( array
			(
				'column'	=> $column,
				'limit'		=> false,
				'action'	=> 'subs',
				'parent'	=> $params['project_id']
			));

			$i = count($subs);
			$subs[$i]['project_id']	= $params['project_id'];
			$subs[$i]['accounting']	= $params['accounting'];
			$subs[$i]['billable']	= $params['billable'];
			$subs[$i]['acc_factor']	= $params['project_accounting_factor'];

			//_debug_array($subs);
			return $subs;
		}

		function colored( $value, $limit = 0, $used = 0, $action = 'budget' )
		{
			$event_extra = $this->soconfig->get_event_extra($action == 'budget' ? 'budget limit' : 'hours limit');

			//echo 'EXTRA: ' . $event_extra . '<br>';
			//echo 'limit: ' . $limit . '<br>';
			$used_percent = ($limit*intval($event_extra))/100;
			//echo 'percent: ' . $used_percent . '<br>';

			//echo 'used: ' . $used . '<br>';
			if( $this->html_output && ($action == 'hours') )
			{
				$val = explode(".", $value);
				if( isset($val[1]) && $val[1] < 10 )
				{
					$value = $val[0].'.0'.$val[1];
				}

				$value = str_replace(".", ":", $value);
			}


			if( $this->html_output && ($used > $used_percent) )
			{
			//echo 'used > $used_percent: ' . $used . '>' . $used_percent . '<br><br>';
				//return '<font color="CC0000"><b>' . sprintf("%01.2f",$value) . '</b></font>';
				return '<font color="CC0000"><b>' . $value . '</b></font>';
			}
			return $value;
		}

		function is_red( $limit = 0, $used = 0, $action = 'budget' )
		{
			$event_extra = $this->soconfig->get_event_extra($action=='budget'?'budget limit':'hours limit');

			$used_percent = ($limit*intval($event_extra))/100;
			return ($used > $used_percent);
		}

		function formatted_priority( $pri = 0 )
		{
			$green	= $pri <= 3 ? true : false;
			$yel	= ($pri > 3 && $pri <= 7) ? true : false;
			$red	= $pri > 7 ? true : false;

			$color = ($green ? '38BB00' : ($yel ? 'ECC200' : 'CC0000'));

			return '<font color="' . $color . '">' . $pri . '</font>';
		}

		function list_projects( $params )
		{
			if( isset( $params['function'] ) && $params['function'] == 'gantt' )
			{
				$pro_list = $this->soprojects->read_gantt_projects( array
				(
					'project_id' => $params['project_id'],
					'parent_array' => $params['parent_array']
				));
			}
			else
			{
				$pro_list = $this->soprojects->read_projects( array
				(
					'start'			=> $this->start,
					'limit'			=> isset($params['limit']) ? $params['limit'] : $this->limit,
					'query'			=> $this->query,
					'filter'		=> $this->filter,
					'sort'			=> $this->sort,
					'order'			=> $this->order,
					'status'		=> $this->status,
					'cat_id'		=> $params['action'] == 'mains' ? $this->cat_id : 0,
					'action'		=> $params['action'],
					'parent'		=> $params['parent'],
					'main'			=> isset($params['main']) ? $params['main'] : 0,
					'project_id'	=> isset($params['project_id']) ? $params['project_id'] : 0
				));
			}

			$this->total_records = $this->soprojects->total_records;

			if( is_array($pro_list) )
			{
				$projects = array();

				foreach( $pro_list as $pro )
				{
					/*$cached_data = $this->cached_accounts($pro['coordinator']);
					$coordinatorout = $GLOBALS['phpgw']->common($cached_data[$pro['coordinator']]['account_lid']
                                        . ' [' . $cached_data[$pro['coordinator']]['firstname'] . ' '
                                        . $cached_data[$pro['coordinator']]['lastname'] . ' ]');*/

					$customerout = '';
					if ( $pro['customer'] )
					{
						$customer = $this->read_single_contact($pro['customer']);
						if( $customer[0] )
						{
							$customerout = $customer[0]['per_first_name'] . ' ' . $customer[0]['per_last_name'];
						}
					}

					$customerorgout = '';
					if ( $pro['customer_org'] )
					{
						$customer_org = $this->read_single_contact_org($pro['customer_org']);
						if ( $customer_org[0] )
						{
							$customerorgout = $customer[0]['org_name'];
						}
					}

					$mstones = $this->get_mstones($pro['project_id']);

					if ( !isset($params['mstones_stat']) )
					{
						$mlist = '';
						if ( is_array($mstones) )
						{
							$mlist = '<table width="100%" border="0" cellpadding="0" cellspacing="0">' . "\n";
							for ( $i=0; $i < count($mstones); $i++ )
							{
								$mlist .= '<tr><td width="50%">' . $mstones[$i]['title'] . '</td><td width="50%" align="right">' . $this->formatted_edate($mstones[$i]['edate']) . '</td></tr>' . "\n";
							}
							$mlist .= '</table>';
						}
					}

					if( $params['page'] == 'budget' || $params['page'] == 'hours' )
					{
						$params['project_id']					= $pro['project_id'];
						$params['accounting']					= $pro['accounting'];
						$params['project_accounting_factor']	= $pro['project_accounting_factor'];
						$params['billable']						= $pro['billable'];
						$params['ptime']						= $pro['ptime'];

						$acc = $this->get_budget($params);
					}

					$ptime_pro		= $pro['ptime'] - $acc['ptime_jobs_min'];
					$phours_pro		= $this->colored($this->sohours->min2str($ptime_pro), $ptime_pro, $acc['uhours_pro_wminutes'], 'hours');
					$uhours_pro		= $this->colored($this->sohours->min2str($acc['uhours_pro_wminutes']), $ptime_pro, $acc['uhours_pro_wminutes'], 'hours');
					$uhours_jobs	= $this->colored(str_replace(".", ":",sprintf("%01.2f",$acc['uhours_jobs'])),$pro['ptime'],$acc['uhours_jobs_wminutes'],'hours');
					$ubudget_pro	= $acc['u_budget'];
					$ubudget_jobs	= $acc['u_budget_jobs'];

					$space = '';
					if ($pro['level'] > 0 && !isset($params['no_formatted_level']))
					{
						$space = ($this->html_output?'&nbsp;.&nbsp;':'.');
						$spaceset = str_repeat($space,$pro['level']);
					}

					$projects[] = array
					(
						'project_id'		=> $pro['project_id'],
						'priority'			=> $this->formatted_priority($pro['priority']),
						'title'				=> $spaceset . $GLOBALS['phpgw']->strip_html($pro['title']),
						'number'			=> $GLOBALS['phpgw']->strip_html($pro['number']),
						'investment_nr'		=> $GLOBALS['phpgw']->strip_html($pro['investment_nr']),
						'coordinator'		=> $pro['coordinator'],
						'coordinatorout'	=> $GLOBALS['phpgw']->common->grab_owner_name($pro['coordinator']),
						'salesmanager'		=> $pro['salesmanager'],
						'salesmanagerout'	=> $GLOBALS['phpgw']->common->grab_owner_name($pro['salesmanager']),
						'customerout'		=> $customerout,
						'customerorgout'	=> $customerorgout,
						'customer_nr'		=> $GLOBALS['phpgw']->strip_html($pro['customer_nr']),
						'sdateout'			=> $this->formatted_edate($pro['sdate'],false),
						'edateout'			=> $this->formatted_edate($pro['edate']),
						'sdate'				=> $pro['sdate'],
						'edate'				=> $pro['edate'],
						'psdate'			=> $pro['psdate'],
						'pedate'			=> $pro['pedate'],
						'psdateout'			=> $this->formatted_edate($pro['psdate'],false),
						'pedateout'			=> $this->formatted_edate($pro['pedate'],false),
						'previousout'		=> $this->return_value('pro',$pro['previous']),
//						'phours'			=> intval($pro['ptime']/60) . ':00',
						'phours_childs'		=> intval($pro['ptime_childs']/60) . ':00',
						'budget'			=> $pro['budget'],
						'budget_childs'		=> $pro['budget_childs'],
						'e_budget'			=> $pro['e_budget'],
						'e_budget_childs'	=> $pro['e_budget_childs'],
						'url'				=> $GLOBALS['phpgw']->strip_html($pro['url']),
						'reference'			=> $GLOBALS['phpgw']->strip_html($pro['reference']),
						'accountingout'		=> lang('per') . ' ' . lang($pro['accounting']),

						'project_accounting_factor'		=> $pro['project_accounting_factor'],
						'project_accounting_factor_d'	=> $pro['project_accounting_factor_d'],

						'billableout'		=> $pro['billable']=='Y'?lang('yes'):lang('no'),
						'discountout'		=> $pro['discount_type']=='percent'?'%':$GLOBALS['phpgw_info']['user']['preferences']['common']['currency'] . ' ' . $pro['discount'],
						'mstones'			=> isset( $params['mstones_stat'] ) ? $mstones : $mlist,
						'main'				=> $pro['main'],
						'parent'			=> $pro['parent'],
						'previous'			=> $pro['previous'],
						'status'			=> $pro['status'],
						'level'				=> $pro['level'],
						'cat'				=> $pro['cat'],
						'uhours_pro'		=> $uhours_pro, //$acc['uhours_pro']?$acc['uhours_pro']:'0:00',
						'uhours_pro_nobill'	=> $acc['uhours_pro_nobill'] ? $acc['uhours_pro_nobill'] : '0:00',
						'uhours_pro_bill'	=> $acc['uhours_pro_bill'] ? $acc['uhours_pro_bill'] : '0:00',
						'uhours_jobs'		=> $uhours_jobs, //$acc['uhours_jobs']?$acc['uhours_jobs']:'0:00',
						'uhours_jobs_nobill'=> $acc['uhours_jobs_nobill'] ? str_replace(".", ":", sprintf("%01.2f", $acc['uhours_jobs_nobill'])) : '0:00',
						'uhours_jobs_bill'	=> $acc['uhours_jobs_bill'] ? str_replace(".", ":", sprintf("%01.2f",$acc['uhours_jobs_bill']) ) : '0:00',
						'ahours_pro'		=> $ahours_pro,
						'ahours_jobs'		=> $ahours_jobs,
						'u_budget'			=> $ubudget_pro, //$acc['u_budget']?$acc['u_budget']:'0.00',
						'u_budget_jobs'		=> $ubudget_jobs, //$acc['u_budget_jobs']?$acc['u_budget_jobs']:'0.00',
						'a_budget'			=> $pro['budget']-$acc['u_budget'],
						'a_budget_jobs'		=> $pro['budget']-$acc['u_budget_jobs'],
						'b_budget'			=> $acc['b_budget'] ? $acc['b_budget'] : '0.00',
						'b_budget_jobs'		=> $acc['b_budget_jobs'] ? $acc['b_budget_jobs'] : '0.00',

						/* AS: new version values with a stricter naming scheme (others should be deprecated but may still be in use somewhere) */
						'item_planned_time'	=> $pro['ptime'] - $acc['ptime_jobs_min'],
						'item_used_time'	=> $acc['utime_item'],
						'item_bill_time'	=> $acc['utime_item_bill'],
						'item_nobill_time'	=> $acc['utime_item_nobill'],
						'item_avail_time'	=> $acc['atime_item'],
						'sum_planned_time'	=> $pro['ptime'],
						'sum_used_time'		=> $acc['utime_sum'],
						'sum_bill_time'		=> $acc['utime_sum_bill'],
						'sum_nobill_time'	=> $acc['utime_sum_nobill'],
						'sum_avail_time'	=> $acc['atime_sum'],
						'is_leaf'			=> $acc['is_leaf'],

						'item_planned_budget'	=> $pro['budget']-$acc['pbudget_jobs'],
						'item_used_budget'		=> $ubudget_pro,
						'item_bill_budget'		=> $acc['b_budget'] ? $acc['b_budget'] : '0.00',
						'item_nobill_budget'	=> $acc['nb_budget'] ? $acc['nb_budget'] : '0.00',
						'item_avail_budget'		=> $pro['budget']-$acc['pbudget_jobs']-$acc['u_budget'],
						'sum_planned_budget'	=> $pro['budget'],
						'sum_used_budget'		=> $ubudget_jobs,
						'sum_bill_budget'		=> $acc['b_budget_jobs'] ? $acc['b_budget_jobs'] : '0.00',
						'sum_nobill_budget'		=> $acc['nb_budget_jobs'] ? $acc['nb_budget_jobs'] : '0.00',
						'sum_avail_budget'		=> $pro['budget']-$acc['u_budget_jobs'],

/*
planned_subs=planned_sum-planned_item
used_subs=used_sum-used_item
*/
						'sum_time_status'	=> $this->is_red($pro['ptime'], $acc['utime_sum'], 'time') ? 'red' : ($this->is_red($acc['ptime_jobs_min'], $acc['utime_sum']-$acc['utime_item'], 'time') ? 'yellow':'green'),
						'item_time_status'	=> $this->is_red($pro['ptime'] - $acc['ptime_jobs_min'], $acc['utime_item'], 'time') ? 'red' : ($this->is_red($acc['ptime_jobs_min'], $acc['utime_sum']-$acc['utime_item'], 'time') ? 'yellow':'green'),

						'sum_budget_status'	=> $this->is_red($pro['budget'], $ubudget_jobs, 'budget') ? 'red' : ($this->is_red($acc['pbudget_jobs'], $ubudget_jobs-$ubudget_pro, 'budget') ? 'yellow':'green'),
						'item_budget_status'=> $this->is_red($pro['budget']-$acc['pbudget_jobs'], $ubudget_pro, 'budget') ? 'red' : ($this->is_red($acc['pbudget_jobs'], $ubudget_jobs-$ubudget_pro, 'budget') ? 'yellow':'green')
					);
				}
			}

			switch( $this->order )
			{
				case 'coordinator':
					usort($projects, array('boprojects', 'cmp_projects_coordinator'));
					if( $this->sort == 'DESC' )
					{
						$projects = array_reverse($projects);
					}
					break;
				case 'customer':
					usort($projects, array('boprojects', 'cmp_projects_customer'));
					if( $this->sort == 'DESC' )
					{
						$projects = array_reverse($projects);
					}
					break;
			}

			return $projects;
		}

		function cmp_projects_coordinator($a, $b)
		{
			return strcasecmp($a['coordinatorout'], $b['coordinatorout']);
		}

		function cmp_projects_customer( $a, $b )
		{
			return strcasecmp($a['coordinatorout'], $b['coordinatorout']);
		}

		function format_date( $date = 0 )
		{
			$d = array();
			if( $date > 0 )
			{
				$d['date'] = $date + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
				$d['date_formatted'] = $GLOBALS['phpgw']->common->show_date($date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}
			return $d;
		}

		function formatted_edate( $edate = 0, $colored = true, $type = 'project' )
		{
			$edate = intval($edate);

			$month  = $GLOBALS['phpgw']->common->show_date(time(),'n');
			$day    = $GLOBALS['phpgw']->common->show_date(time(),'d');
			$year   = $GLOBALS['phpgw']->common->show_date(time(),'Y');

			if ( $edate > 0 )
			{
				$edate = $edate + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
				$edateout = $GLOBALS['phpgw']->common->show_date($edate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}
			if( $this->html_output && $colored )
			{
				switch( $type )
				{
					case 'ms':
						$event = 'milestone date due';
						break;
					default:
						$event = 'project date due';
						break;
				}

				$event_extra = $this->soconfig->get_event_extra($event);

				/*if (mktime(2,0,0,$month,$day+($event_extra*2),$year) >= $edate)
				{
					$edateout = '<font color="ECC200"><b>' . $edateout . '</b></font>';
				}*/
				if ( mktime(12,0,0,$month,$day+$event_extra,$year) >= $edate )
				{
					$edateout = '<font color="CC0000"><b>' . $edateout . '</b></font>';
				}
			}
			return $edateout;
		}

		function read_single_project( $project_id, $page = 'bla', $action = 'subs' )
		{
			$pro = $this->soprojects->read_single_project($project_id);

			if ( !is_array($pro) )
			{
				return false;
			}

/* fix empty field in view project
			$check_pro = array
			(
				'coordinator'	=> $pro['coordinator'],
				'main'			=> $pro['main'],
				'parent'		=> $pro['parent'],
				'action'		=> $pro['parent'] > 0 ? 'subs' : 'mains'
			);

			$perms = $this->edit_perms($check_pro);

			if ( !$perms )
			{
				return false;
			}
*/

			if( $page == 'budget' || $page == 'hours' || $page = 'planned' )
			{
				$acc = $this->get_budget( array
				(
					'project_accounting_factor'	=> $pro['project_accounting_factor'],
					'accounting'				=> $pro['accounting'],
					'project_id'				=> $project_id,
					'page'						=> $page,
					'action'					=> $action,
					'ptime'						=> $pro['ptime']
				));

				$atime = $this->sohours->format_wh($pro['ptime']-$acc['ptime_jobs_min']);
			}

			$uhours_pro		= $this->colored($acc['uhours_pro'],$pro['ptime'],$acc['uhours_pro_wminutes'],'hours');
			$uhours_jobs	= $this->colored($acc['uhours_jobs'],$pro['ptime'],$acc['uhours_jobs_wminutes'],'hours');

			$ubudget_pro	= $this->colored($acc['u_budget'],$pro['budget'],$acc['u_budget']);
			$ubudget_jobs	= $this->colored($acc['u_budget_jobs'],$pro['budget'],$acc['u_budget_jobs']);
			$ubudget_pro	= $acc['u_budget'];
			$ubudget_jobs	= $acc['u_budget_jobs'];

			$project = array
			(
				'ptime'							=> intval( $pro['ptime']/60 ),
				'ptime_childs'					=> intval( $pro['ptime_childs']/60 ),
				'ptime_min'						=> $pro['ptime'],
				'ptime_min_childs'				=> $pro['ptime_childs'],
				'ptime_jobs'					=> $acc['ptime_jobs'],
				'atime'							=> $atime['whwm'],
				'title'							=> $GLOBALS['phpgw']->strip_html($pro['title']),
				'number'						=> $GLOBALS['phpgw']->strip_html($pro['number']),
				'investment_nr'					=> $GLOBALS['phpgw']->strip_html($pro['investment_nr']),
				'descr'							=> $GLOBALS['phpgw']->strip_html($pro['descr']),
				'budget'						=> $pro['budget'],
				'budget_childs'					=> $pro['budget_childs'],
				'e_budget'						=> $pro['e_budget'],
				'e_budget_childs'				=> $pro['e_budget_childs'],
				'pbudget_jobs'					=> $acc['pbudget_jobs']?$acc['pbudget_jobs']:'0.00',
				'ap_budget_jobs'				=> $pro['budget']-$acc['pbudget_jobs'],
				'a_budget'						=> $pro['budget']-$acc['u_budget'],
				'a_budget_jobs'					=> $pro['budget']-$acc['u_budget_jobs'],
				'u_budget'						=> $ubudget_pro,       //$acc['u_budget']?$acc['u_budget']:'0.00',
				'u_budget_jobs'					=> $ubudget_jobs,      //$acc['u_budget_jobs']?$acc['u_budget_jobs']:'0.00',
				'project_id'					=> $pro['project_id'],
				'parent'						=> $pro['parent'],
				'main'							=> $pro['main'],
				'cat'							=> $pro['cat'],
				'access'						=> $pro['access'],
				'coordinator'					=> $pro['coordinator'],
				'coordinatorout'				=> $GLOBALS['phpgw']->common->grab_owner_name($pro['coordinator']),
				'salesmanager'					=> $pro['salesmanager'],
				'salesmanagerout'				=> $pro['salesmanager']?$GLOBALS['phpgw']->common->grab_owner_name($pro['salesmanager']):'',
				'customer'						=> $pro['customer'],
				'customer_org'					=> $pro['customer_org'],
				'status'						=> $pro['status'],
				'owner'							=> $pro['owner'],
				'processor'						=> $pro['processor'],
				'previous'						=> $pro['previous'],
				'url'							=> $GLOBALS['phpgw']->strip_html($pro['url']),
				'reference'						=> $GLOBALS['phpgw']->strip_html($pro['reference']),
				'customer_nr'					=> $GLOBALS['phpgw']->strip_html($pro['customer_nr']),
				'test'							=> $GLOBALS['phpgw']->strip_html($pro['test']),
				'quality'						=> $GLOBALS['phpgw']->strip_html($pro['quality']),
				'result'						=> $GLOBALS['phpgw']->strip_html($pro['result']),
				'accounting'					=> $pro['accounting'],
				'project_accounting_factor'		=> $pro['project_accounting_factor'],
				'project_accounting_factor_d'	=> $pro['project_accounting_factor_d'],
				'billable'						=> $pro['billable'],
				'plan_bottom_up' 				=> (($pro['plan_bottom_up'] == 'Y') ? 'Y' : 'N'),
				'direct_work'					=> (($pro['direct_work'] == 'Y') ? 'Y' : 'N'),
				'uhours_pro'					=> $uhours_pro, //$acc['uhours_pro']?$acc['uhours_pro']:'0:00',
				'uhours_pro_nobill'				=> $acc['uhours_pro_nobill'] ? $acc['uhours_pro_nobill'] : '0:00',
				'uhours_pro_bill'				=> $acc['uhours_pro_bill'] ? $acc['uhours_pro_bill'] : '0:00',
				'uhours_jobs'					=> $uhours_jobs, //$acc['uhours_jobs']?$acc['uhours_jobs']:'0:00',
				'uhours_jobs_nobill'			=> $acc['uhours_jobs_nobill'] ? $acc['uhours_jobs_nobill'] : '0:00',
				'uhours_jobs_bill'				=> $acc['uhours_jobs_bill'] ? $acc['uhours_jobs_bill'] : '0:00',
				'uhours_jobs_wminutes'			=> $acc['uhours_jobs_wminutes'] ? $acc['uhours_jobs_wminutes'] : 0,
				'ahours_pro'					=> $acc['ahours_pro'] ? $acc['ahours_pro'] : '0:00',
				'ahours_jobs'					=> $acc['ahours_jobs'] ? $acc['ahours_jobs'] : '0:00',
				'priority'						=> $pro['priority'],
				'inv_method'					=> $GLOBALS['phpgw']->strip_html($pro['inv_method']),
				'discount'						=> $pro['discount'],
				'discount_type'					=> $pro['discount_type'],
				'level'							=> $pro['level']
			);

			$date = $this->format_date($pro['edate']);
			$project['edate']			= $date['date'];
			$project['edate_formatted'] = $date['date_formatted'];

			$date = $this->format_date($pro['sdate']);
			$project['sdate']			= $date['date'];
			$project['sdate_formatted'] = $date['date_formatted'];

			$date = $this->format_date($pro['udate']);
			$project['udate']			= $date['date'];
			$project['udate_formatted'] = $date['date_formatted'];

			$date = $this->format_date($pro['cdate'] == 0?$pro['sdate']:$pro['cdate']);
			$project['cdate']			= $date['date'];
			$project['cdate_formatted'] = $date['date_formatted'];

			$date = $this->format_date($pro['pedate']);
			$project['pedate']				= $date['date'];
			$project['pedate_formatted']	= $date['date_formatted'];

			$date = $this->format_date($pro['psdate']);
			$project['psdate']				= $date['date'];
			$project['psdate_formatted']	= $date['date_formatted'];

			$customerout = '';
			if ($pro['customer'] > 0)
			{
				$customer = $this->read_single_contact($pro['customer']);
				if($customer[0])
				{
					$customerout = $customer[0]['per_first_name'] . ' ' . $customer[0]['per_last_name'];
				}
			}
			$project['customerout'] = $customerout;

			$customerorgout = '';
			if ( $pro['customer_org'] > 0 )
			{
				$customer_org = $this->read_single_contact_org($pro['customer_org']);
				if ( $customer_org[0] )
				{
					$customerorgout = $customer[0]['org_name'];
				}
			}
			$project['customerorgout'] = $customerorgout;

			//_debug_array($project);
			return $project;
		}

		function sum_budget( $values )
		{
			$retval = $this->soprojects->sum_budget( array
			(
				'start'		=> $this->start,
				'limit'		=> false,
				'query'		=> $this->query,
				'filter'	=> $this->filter,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'status'	=> $this->status,
				'cat_id'	=> $values['action'] == 'mains' ? $this->cat_id : 0,
				'action'	=> $values['action'],
				'parent'	=> $values['parent'],
				'main'		=> $values['main'],
				'bcolumn'	=> $values['bcolumn']
			));

			return $retval;
		}

		function exists( $params )
		{
			return $this->soprojects->exists($params);
		}

		function check_values( $action, $values )
		{
			$prefs = $this->read_prefs();

			if( $values['psdate'] == '' )
				$error[] = lang('please enter planned start date');

			if( $values['pedate'] == '' )
				$error[] = lang('please enter planned end date');

			if ( strlen($values['descr'] ) > 8000)
			{
				$error[] = lang('Description can not exceed 8000 characters in length');
			}

			if ( !$values['coordinator'] )
			{
				$error[] = lang('please choose a project coordinator');
			}

			if ( strlen(trim($values['title'])) == 0 )
			{
				$error[] = lang('please enter a title');
			}
			else if( strlen($values['title']) > 250 )
			{
				$error[] = lang('title can not exceed 250 characters in length');
			}

			if ( !$values['choose'] )
			{
				$is_error = false;

				if( !$this->isprojectadmin('pad') )
				{
					$this->check = CreateObject('projects.checker');

					if( !$this->check->checkProjectNr($values['number']) )
					{
						$error[] = $this->check->getLastErrorMsg();
						$is_error = true;
					}
				}

				if( !$is_error && $this->siteconfig['permit_double_project_id'] == 'no' )
				{
					// check if a main project with the same projects number exists
					if( $action == 'subs' )
					{
						$main = $this->return_value('main',$values['parent']);
					}
					else
					{
						if( isset($values['project_id']) && ($values['project_id'] > 0) )
						{
							$main = $this->return_value('main', $values['project_id']);
						}
						else
						{
							$main = 0;
						}
					}

					$check_project_number = array(
						'check'      => 'main_project_number',
						'column_val' => '"' . $values['number'] . '"',
						'project_id' => $main
					);

					if( !$this->isprojectadmin('pad') )
					{
						$project_number_exists = $this->exists($check_project_number);

						if($project_number_exists)
						{
							$error[] = lang('project id already exists');
						}
					}
				}
			}

			if( $this->siteconfig['categorie_required'] == 'yes' )
			{
				if( !$values['cat'] )
				{
					$error[] = lang('please select a categorie for the project');
				}
			}

			if( $this->siteconfig['accounting'] == 'activity' )
			{
				if ( ( !$values['book_activities'] ) && ( !$values['bill_activities'] ) )
				{
					$error[] = lang('please choose activities for the project');
				}
			}
			//else if(!$values['billable'])
			else
			{
				if( !$values['accounting'] )
				{
					$error[] = lang('please choose the accounting system for the project');
				}
				else
				{
					if( $values['accounting'] == 'project' && ($values['project_accounting_factor'] == 0) && ($values['project_accounting_factor_d'] == 0) )
					{
						$error[] = lang('please set the accounting factor for the project');
					}
				}
			}

			if( isset($values['project_id']) && ($values['project_id'] > 0) )
			{
				$project = $this->read_single_project($values['project_id']);
			}
			else
			{
				$project = false;
			}

			if( isset($values['parent']) && ($values['parent'] > 0) )
			{
				$parent = $this->read_single_project($values['parent']);
			}
			else
			{
				$parent = false;
			}

			$values['discount'] = ($values['discount']=='0.00')?0:$values['discount'];

			if( $values['discount'] > 0 && !$values['discount_type'] )
			{
				$error[] = lang('please choose the discount type');
			}

			if ( $values['previous'] )
			{
				$edate = $this->return_value('edate',$values['previous']);

				if ( intval($edate) == 0 )
				{
					$error[] = lang('the choosen previous project does not have an end date specified');
				}
			}

			if ( ($action == 'subs') && ($values['plan_bottom_up'] == 'N') && $parent )
			{
				$main_edate = $parent['edate']; //$this->return_value('edate',$values['parent']);

				if ( $main_edate > 0 )
				{
					if ( $values['edate'] > $main_edate )
					{
						$error[] = lang('end date can not be after parent projects date due');
					}
				}

				$main_sdate = $parent['sdate']; //$this->return_value('sdate',$values['parent']);

				if ( $main_sdate > 0 && $values['sdate'] > 0 )
				{
					if ( $values['sdate'] < $main_sdate )
					{
						$error[] = lang('start date can not be before parent projects start date');
					}
				}

				$main_pedate = $parent['pedate']; //$this->return_value('pedate',$values['parent']);

				if ( $main_pedate > 0 )
				{
					if ( $values['pedate'] > $main_pedate )
					{
						$error[] = lang('end date planned can not be after parent projects end date planned');
					}
				}

				$main_psdate = $parent['psdate']; //$this->return_value('psdate',$values['parent']);

				if ( $main_psdate > 0 && $values['psdate'] > 0 )
				{
					if ( $values['psdate'] < $main_psdate )
					{
						$error[] = lang('start date planned can not be before parent projects start date planned');
					}
				}

				if( $values['sdate'] > 0 && $values['edate'] > 0 )
				{
					if( $values['edate'] < $values['sdate'] )
					{
						$error[] = lang('end date can not be before start date');
					}
				}

				if( $values['psdate'] > 0 && $values['pedate'] > 0 )
				{
					if( $values['pedate'] < $values['psdate'] )
					{
						$error[] = lang('end date planned can not be before start date planned');
					}
				}

				// use given values for project and project child instead of extra sql queries
				$ptime_parent = $parent['ptime_min']-$parent['ptime_min_childs']+$project['ptime_min']; // +pro[] because this is the old project value that become free when saving the project data
				$pminutes = intval($values['ptime'])*60;

				if ( $pminutes > $ptime_parent )
				{
					$error[] = lang('planned time is bigger than the planned time of the parent project').' ('.intval($pminutes/60).':'.sprintf("%02d",intval($pminutes%60)).' > '.intval($ptime_parent/60).':'.sprintf("%02d",intval($ptime_parent%60)).')';
				}

				// use given values for project and project child instead of extra sql queries
				$budget_parent = $parent['budget'] - $parent['budget_childs'] + $project['budget']; // +pro[] because this is the old project value that become free when saving the project data

				if ( $values['budget'] > $budget_parent )
				{
					$error[] = lang('budget is bigger than the budget of the parent project').' ('.sprintf("%1.02f", (float) $values['budget']).' '.$prefs['currency'].' > '.sprintf("%1.02f", (float) $budget_parent).' '.$prefs['currency'].')';
				}

				// use given values for project and project child instead of extra sql queries
				$ebudget_parent	= $parent['e_budget'] - $parent['e_budget_childs'] + $project['e_budget']; // +pro[] because this is the old project value that become free when saving the project data
				if ( $values['e_budget'] > $ebudget_parent )
				{
					$error[] = lang('extra budget is bigger than the extra budget of the parent project').' ('.sprintf("%1.02f", (float) $values['e_budget']).' '.$prefs['currency'].' > '.sprintf("%1.02f", (float) $ebudget_parent).' '.$prefs['currency'].')';
				}
			}

			if( $values['sdate'] > 0 && $values['edate'] > 0 )
			{
				if( $values['edate'] < $values['sdate'] )
				{
					$error[] = lang('end date can not be before start date');
				}
			}

			if( $values['psdate'] > 0 && $values['pedate'] > 0 )
			{
				if( $values['pedate'] < $values['psdate'] )
				{
					$error[] = lang('end date planned can not be before start date planned');
				}
			}

			// check values against sub project data
			$sdate    = $values['sdate'];  // start date
			$psdate   = $values['psdate']; // planed start date
			$edate    = $values['edate'];  // end date
			$pedate   = $values['pedate']; // planed end date
			$ptime    = 0.0;    // planed time
			$budget   = 0.0;    // budget
			$e_budget = 0.0;    // extra budget

			// get sub jobs
			$subs = $this->get_sub_projects( array
			(
				'project_id' => $values['project_id']
			));

			// for each sub project
			while( $values['project_id'] && ( list($subNum, $subData) = each($subs) ) )
			{
				// get planned dates (earliest start and latest end date) and workhours and budget
				if( isset($subData['sdate']) && ($subData['sdate'] < $sdate) )
				{
					$sdate = $subData['sdate'];
				}
				if( isset($subData['psdate']) && ($subData['psdate'] < $psdate) )
				{
					$psdate = $subData['psdate'];
				}
				if( isset($subData['edate']) && ($subData['edate'] > $edate) )
				{
					$edate = $subData['edate'];
				}
				if( isset($subData['pedate']) && ($subData['pedate'] > $pedate) )
				{
					$pedate = $subData['pedate'];
				}

				$ptime    += $subData['ptime'];
				$budget   += $subData['budget'];
				$e_budget += $subData['e_budget'];
			}

			if( $values['sdate'] )
			{
				if( $sdate < $values['sdate'] )
				{
					$error[] = lang('start date can not be after sub projects start date');
				}
			}

			if( $values['psdate'] )
			{
				if( $psdate < $values['psdate'] )
				{
					$error[] = lang('planned start date can not be after sub projects planned start date');
				}
			}

			if( $values['edate'] )
			{
				if( $edate > $values['edate'] )
				{
					$error[] = lang('end date can not be before sub projects end date');
				}
			}

			if( $values['pedate'] )
			{
				if( $pedate > $values['pedate'] )
				{
					$error[] = lang('planned end date can not be before sub projects planned end date');
				}
			}

			if( $values['plan_bottom_up'] == 'N' )
			{
				$value_ptime_min = intval($values['ptime'])*60;

				if( $value_ptime_min < 0 )
				{
					$error[] = lang('planned time can not be lesser then 0');
				}
				elseif( $ptime > $value_ptime_min )
				{
					$error[] = lang('planned time can not be lesser then planned time sum of all sub projects').' ('.intval($value_ptime_min/60).':'.sprintf("%02d",intval($value_ptime_min%60)).' < '.intval($ptime/60).':'.sprintf("%02d",intval($ptime%60)).')';
				}

				if( $values['budget'] < 0 )
				{
					$error[] = lang('budget can not be lesser then 0');
				}
				elseif( $budget > $values['budget'] )
				{
					$error[] = lang('budget can not be lesser then budget sum of all sub projects').' ('.$values['budget'].' '.$prefs['currency'].' < '.$budget.' '.$prefs['currency'].')';
				}

				if( $values['e_budget'] < 0 )
				{
					$error[] = lang('extra budget can not be lesser then 0');
				}
				elseif( $e_budget > $values['e_budget'] )
				{
					$error[] = lang('extra budget can not be lesser then extra budget sum of all sub projects').' ('.$values['e_budget'].' '.$prefs['currency'].' < '.$e_budget.' '.$prefs['currency'].')';
				}
			}

			if ( is_array($error) )
			{
				return $error;
			}
		}

		function save_project( $action, $values )
		{
			if ( $values['choose'] )
			{
				switch( $action )
				{
					case 'mains':
						$values['number'] = $this->soprojects->create_projectid();
						break;
					default:
						$values['number'] = $this->soprojects->create_jobid($values['parent']);
						break;
				}
			}

			/*
			if (!$values['sdate'])
			{
			    $values['sdate'] = time();
			}
			*/

			$values['sdate'] = intval($values['sdate']);
			$values['edate'] = intval($values['edate']);

			if ( !$values['previous'] && $values['parent'] )
			{
				$values['previous'] = $this->return_value('previous',$values['parent']);
			}

			$values['ptime']  = intval($values['ptime']);
			$values['budget'] = round($values['budget'], 2);

			$values['ptime'] = $values['ptime'] * 60;

			//echo 'start boprojects: save_project ->';
			//_debug_array($values);
			//echo 'end boprojects: save_project';

			if( isset($values['plan_bottom_up']) && ($values['plan_bottom_up'] == 'Y') )
			{
				$values['plan_bottom_up'] = 'Y';
			}
			else
			{
				$values['plan_bottom_up'] = 'N';
			}

			if( isset($values['direct_work']) && ($values['direct_work'] == 'Y') )
			{
				$values['direct_work'] = 'Y';
			}
			else
			{
				$values['direct_work'] = 'N';
			}

			if ( isset($values['discount_type']) && ($values['discount_type'] == 'no') )
			{
				$values['discount'] = 0.0;
			}

			if ( !isset($values['salesmanager']) )
			{
				$values['salesmanager'] = 0;
			}

			$values['project_name'] = $values['title'] . ' [' . $values['number'] . ']';

			if ( intval($values['project_id']) > 0 )
			{
				// get old project values for later calculation
				$old_pro = $this->soprojects->read_single_project($values['project_id']);
				$values['ptime_childs']    = $old_pro['ptime_childs'];
				$values['budget_childs']   = $old_pro['budget_childs'];
				$values['e_budget_childs'] = $old_pro['e_budget_childs'];

				// calc new_value - old_value
				$changed_values = array(
					'ptime'    => $values['ptime']    - $old_pro['ptime'],
					'budget'   => $values['budget']   - $old_pro['budget'],
					'e_budget' => $values['e_budget'] - $old_pro['e_budget']
				);

				$following = $this->soprojects->edit_project($values);
				$this->update_parent($old_pro['parent'], $changed_values);

				if( is_array($following) )
				{
					$return = $this->send_alarm( array
					(
						'project_name'	=> $values['project_name'],
						'event_type'	=> 'project dependencies',
						'project_id'	=> $values['project_id'],
						'following'		=> $following,
						'edate'			=> $values['edate'],
						'old_edate'		=> $values['old_edate'],
						'is_previous'	=> true
					));

					if( $return )
					{
						foreach( $following as $fol )
						{
							$fol['previous_name']		= $values['project_name'];
							$fol['previous_edate']		= $values['edate'];
							$fol['previous_old_edate']	= $values['old_edate'];
							$fol['project_name']		= $fol['title'] . ' [' . $fol['number'] . ']';
							$fol['event_type']			= 'project dependencies';
							$this->send_alarm($fol);
						}
					}
				}

				$this->send_alarm( array
				(
					'project_name' => $values['project_name'],
					'event_type' => 'changes of project data',
					'project_id' => $values['project_id']
				));

				if( $values['coordinator'] != $values['old_coordinator'] )
				{
					$this->send_alarm( array
					(
						'account_id'	=> $values['coordinator'],
						'events'		=> array($event_id),
						'project_name'	=> $values['project_name'],
						'event_type'	=> 'assignment to role',
						'project_id'	=> $values['project_id']
					));
				}
			}
			else
			{
				// new project -> no old project values available

				$values['ptime_childs']		= 0;
				$values['budget_childs']	= 0;
				$values['e_budget_childs']	= 0;
				$values['project_id']		= $this->soprojects->add_project($values);

				// if parent isset we its a new sub project else its a new main project
				if( isset($values['parent']) && ($values['parent'] > 0) )
				{	// for a new subproject update the parent project
					// calc new_value - old_value (old values doesnt exists!)
					$changed_values = array
					(
						'ptime'    => $values['ptime'],
						'budget'   => $values['budget'],
						'e_budget' => $values['e_budget']
					);
					$this->update_parent($values['parent'], $changed_values);
				}
			}

			$values['project_id']	= intval($values['project_id']);
			$values['old_edate']	= intval($values['old_edate']);

			$async = CreateObject('phpgwapi.asyncservice');

			if( $values['edate'] > 0 && $values['old_edate'] != $values['edate'] )
			{
				$event_extra = $this->soconfig->get_event_extra('project date due');
				$next = mktime( date('H',time()), date('i',time()) + 5, 0, $values['emonth'], $values['eday'] - $event_extra, $values['eyear'] );

				$edate = $this->format_date($values['edate']);
				$async->write( array
				(
					'id'			=> 'projects-' . $values['project_id'],
					'next'			=> $next,
					'times'			=> array
										(
											'year'	=> date('Y',$next),
											'month'	=> date('m',$next),
											'day'	=> date('d',$next),
											'hour'	=> date('H',$next),
											'min'	=> date('i',$next)
										),
					'account_id'	=> $values['coordinator'],
					'method'		=> 'projects.boprojects.send_alarm',
					'data'			=> array
										(
											'project_id'	=> $values['project_id'],
											'event_type'	=> 'project date due',
											'edate'			=> $edate['date_formatted'],
											'project_name'	=> $values['project_name']
										)
				));

				/*$async->write(array('id' => 'projects-' . $values['project_id'], 'next' => 0,'times' => array('min' => '5')
									,'account_id' => $values['coordinator'],'method' => 'projects.boprojects.send_alarm',
									'data' => array('project_id' => $values['project_id'],'action' => 'prodatedue')));*/
			}

			if( $values['edate'] == 0 )
			{
				$aid = 'projects-' . $values['project_id'];
				$async->delete($aid);
			}

			unset($async);

			if ( is_array($values['employees']) )
			{
				$tmp = $values['employees'];
				$values['employees'] = array_unique($tmp);
				$this->soprojects->delete_acl($values['project_id']);

				for( $i=0; $i < count($values['employees']); $i++ )
				{
					$GLOBALS['phpgw']->acl->add_repository('project_members',$values['project_id'],$values['employees'][$i],7);
				}
			}

			if( $action == 'mains' )
			{
				// update plan bottom up setting for all jobs of this project
				$this->plan_bottom_up_set_job_setting($values['project_id'], $values['plan_bottom_up']);

				// update direct work setting for all jobs of this project
				$this->direct_work_set_job_setting($values['project_id'], $values['direct_work']);
			}

			return $values['project_id'];
		}

		function select_project_list( $values )
		{
			return $this->soprojects->select_project_list($values);
		}

		function delete_project( $pa_id, $subs, $action = 'pro' )
		{
			$project = $this->read_single_project($pa_id);

			if ( $action == 'account' )
			{
				$this->soprojects->delete_account_project_data($pa_id);
			}
			else
			{
				$this->soprojects->delete_project($pa_id, $subs);
			}

			// update parent
			if( isset($project['parent']) && ($project['parent'] > 0) )
			{	// update the parent project
				// calc new_value - old_value (when deleting the project the new values are zero)
				$changed_values = array
				(
					'ptime'    => 0 - 60 * ( $project['ptime'] + $project['ptime_childs'] ),
					'budget'   => 0 - ( $project['budget'] + $project['budget_childs'] ),
					'e_budget' => 0 - ( $project['e_budget'] + $project['e_budget_childs'] )
				);
				$this->update_parent($project['parent'], $changed_values);
			}
		}

		function change_owner( $old, $new )
		{
			$this->soprojects->change_owner($old, $new);
		}

		function get_mstones( $project_id )
		{
			$mstones = $this->soprojects->get_mstones($project_id);

			if( is_array($mstones) )
			{
				foreach( $mstones as $ms )
				{
					$stones[] = array
					(
						'title'		=> $GLOBALS['phpgw']->strip_html($ms['title']),
						'edate'		=> $ms['edate'],
						's_id'		=> $ms['s_id']
					);
				}
				return $stones;
			}
			return false;
		}

		function get_single_mstone( $s_id )
		{
			return $this->soprojects->get_single_mstone($s_id);
		}

		function check_mstone( $values )
		{
			if ( strlen(trim($values['title'])) == 0 )
			{
				$error[] = lang('please enter a title');
			}

			if ( strlen($values['title']) > 250 )
			{
				$error[] = lang('title can not exceed 250 characters in length');
			}

			if ( intval($values['edate']) == 0 )
			{
				$error[] = lang('please specify the date due');
			}
			else
			{
				$pro_sdate = $this->return_value('sdate',$values['project_id']);
				$pro_edate = $this->return_value('edate',$values['project_id']);

				if ( $pro_edate > 0 )
				{
					if ( $values['edate'] > $pro_edate )
					{
						$error[] = lang('milestone date can not be after projects date due');
					}
				}
				if ( $pro_sdate > 0 )
				{
					if ( $values['edate'] < $pro_sdate )
					{
						$error[] = lang('milestone date can not be before projects date due');
					}
				}
			}

			if( is_array($error) )
			{
				return $error;
			}
			else
			{
				return true;
			}
		}

		function save_mstone( $values )
		{
			if ( $values['emonth'] || $values['eday'] || $values['eyear'] )
			{
				$values['edate'] = mktime(12, 0, 0, $values['emonth'], $values['eday'], $values['eyear']);
			}
			$values['edate'] = intval($values['edate']);

			if ( intval($values['s_id']) > 0 )
			{
				$this->soprojects->edit_mstone($values);
			}
			else
			{
				$values['s_id'] = $this->soprojects->add_mstone($values);
			}

			$values['old_edate'] = intval($values['old_edate']);
			$async = CreateObject('phpgwapi.asyncservice');

			if( $values['edate'] > 0 && $values['old_edate'] != $values['edate'] )
			{
				$co				= $this->soprojects->return_value('co',$values['project_id']);
				$event_extra	= $this->soconfig->get_event_extra('milestone date due');
				$next			= mktime( date('H',time()), date('i', time())+5, 0, $values['emonth'], $values['eday'] - $event_extra,$values['eyear'] );
				$edate			= $this->format_date($values['edate']);
				$async->write( array
				(
					'id'			=> 'ms-' . $values['s_id'] . '-project-' . $values['project_id'],
					'next'			=> $next,
					'times'			=> array
										(
											'year'		=> date('Y',$next),
											'month'		=> date('m',$next),
											'day'		=> date('d',$next),
											'hour'		=> date('H',$next),
											'min'		=> date('i',$next)
										),
					'account_id'	=> $co,
					'method'		=> 'projects.boprojects.send_alarm',
					'data'			=> array
										(
											'project_id'=> $values['project_id'],
											'event_type'=> 'milestone date due',
											'edate'		=> $edate['date_formatted'],
											'ms_title'	=> $values['title']
										)
				));
			}

			if( $values['edate'] == 0 )
			{
				$aid = 'ms-' . $values['s_id'] . '-project-' . $values['project_id'];
				$async->delete($aid);
			}

			unset($async);

			return $values['s_id'];
		}

		function delete_item( $values )
		{
			switch( $values['action'] )
			{
				case 'emp_role':
					$this->soprojects->soconfig->delete_pa($values['action'],$values['id']);
					break;
				default:
					$this->soprojects->delete_mstone($values['id']);
			}
		}

		function member($project_id = '')
		{
			return $this->soprojects->member($project_id);
		}


// ------------ ALARM ----------------

		function send_alarm( $values )
		{
			$event_type		= isset($values['event_type'])?$values['event_type']:'assignment to role';
			$project_name	= isset($values['project_name'])?$values['project_name']:$this->soprojects->return_value('pro',$values['project_id']);

			switch( $event_type )
			{
				case 'assignrolepro':
					$values['event_type'] = 'assignment to project,assignment to role';
					$emp_events	= $this->soprojects->read_employee_roles($values);
					break;
				default:
					$emp_events = $this->soprojects->read_employee_roles($values);
					break;
			}

			//echo 'BOPROJECTS->alarm EVENTS: ';
			//_debug_array($emp_events);

			$notify_hours	= $this->soprojects->check_alarm($values['project_id'],'hours');
			$notify_budget	= $this->soprojects->check_alarm($values['project_id'],'budget');

			for( $k=0; $k < count($emp_events); $k++ )
			{
				for( $i=0; $i < count($emp_events[$k]['events']); $i++ )
				{
					$event		= $this->soprojects->id2item(array('action' => 'event','item_id' => $emp_events[$k]['events'][$i],'item' => 'event_name'));
					$co			= $this->soprojects->return_value('co',$values['project_id']);
					$subject	= lang('project') .  ': ' . $project_name . ': ' . lang($event) . ' ';

					switch( $event_type )
					{
						case 'project date due':
						case 'milestone date due':
						case 'budget limit':
						case 'hours limit':
							$subject .= lang('has reached');
							break;
						case 'project dependencies':
							$subject .=  ', ' . ($values['is_previous'] ? lang('end date has changed') : lang('previous projects end date has changed'));
							break;
					}

					switch( $event )
					{
						case 'changes of project data':
							$send_alarm = true;
							$msg = $subject;
							break;
						case 'assignment to role':
							$send_alarm = true;
							if( $co == $emp_events[$k]['account_id'] )
							{
								$role_name = lang('coordinator');
							}
							else
							{
								$role_name = $this->soprojects->id2item( array
								(
									'action'	=> 'role',
									'item_id'	=> $emp_events[$k]['role_id'],
									'item'		=> 'role_name'
								));
							}
							$msg = lang($event) . ': ' . $role_name;
							break;
						case 'project dependencies':
							$send_alarm = true;
							$changedate = $this->siteconfig['dateprevious'] == 'yes' ? true : false;
							if( $values['is_previous'] )
							{
								$edate = $this->format_date($values['edate']);
								$oedate = $this->format_date($values['old_edate']);
								$msg = lang('previous project') . ': ' . $project_name . "\n"
									. lang('old end date') . ': ' . $oedate['date_formatted'] . "\n"
									. lang('new end date') . ': ' . $edate['date_formatted'] . "\n\n"
							 		. lang('projects, which are assigned as sequencing') . ':' . "\n"
									. ($changedate?lang('changed start date and end date of projects bellow'):'') . "\n\n";

								if( is_array($values['following']) )
								{
									foreach( $values['following'] as $fol )
									{
										$sdate	= $this->format_date($fol['sdate']);
										$nsdate	= ($changedate?$this->format_date($fol['nsdate']):'');
										$edate	= $this->format_date($fol['edate']);
										$nedate	= ($changedate?$this->format_date($fol['nedate']):'');
										$msg .= $fol['title'] . ' [' . $fol['number'] . '] ' . "\n"
											. ($changedate?lang('old start date'):lang('start date')) . ': ' . $sdate['date_formatted'] . ' '
											. ($changedate?lang('new start date') . ': ' . $nsdate['date_formatted']:'') . "\n"
											. ($changedate?lang('old end date'):lang('end date')) . ': ' . $edate['date_formatted'] . ' '
											. ($changedate?lang('new end date') . ': ' . $nedate['date_formatted']:'') . "\n";

										if( is_array($fol['mstones']) )
										{
											foreach( $fol['mstones'] as $stone )
											{
												$sedate		= $this->format_date($stone['edate']);
												$snedate	= ($changedate?$this->format_date($stone['snedate']):'');
												$msg .= lang('milestone') . ' ' . $stone['title'] . "\n"
														. ($changedate?lang('old end date'):lang('end date')) . ': ' . $sedate['date_formatted'] . ' '
														. ($changedate?lang('new end date') . ': ' . $snedate['date_formatted']:'') . "\n";
											}
										}
										$msg .= "\n";
									}
								}
							}
							else
							{
								$previous_edate		= $this->format_date($values['previous_edate']);
								$previous_oedate	= $this->format_date($values['previous_old_edate']);

								$sdate	= $this->format_date($values['sdate']);
								$nsdate	= ($changedate?$this->format_date($values['nsdate']):'');
								$edate	= $this->format_date($values['edate']);
								$nedate	= ($changedate?$this->format_date($values['nedate']):'');

								$msg = lang('previous project') . ': ' . $values['previous_name'] . "\n"
										. lang('old end date') . ': ' . $previous_oedate['date_formatted'] . "\n"
										. lang('new end date') . ': ' . $previous_edate['date_formatted'] . "\n\n"

										. lang('sequencing project') . ': ' . $project_name . "\n"
										. ($changedate ? lang('changed start date and end date') : '') . "\n"
										. ($changedate ? lang('old start date') : lang('start date')) . ': ' . $sdate['date_formatted'] . ' '
										. ($changedate ? lang('new start date') . ': ' . $nsdate['date_formatted'] : '') . "\n"
										. ($changedate ? lang('old end date') : lang('end date')) . ': ' . $edate['date_formatted'] . ' '
										. ($changedate ? lang('new end date') . ': ' . $nedate['date_formatted'] : '') . "\n";

								if( is_array($values['mstones']) )
								{
									foreach( $values['mstones'] as $stone )
									{
										$sedate		= $this->format_date($stone['edate']);
										$snedate	= ($changedate?$this->format_date($stone['snedate']):'');
										$msg .= lang('milestone') . ' ' . $stone['title'] . "\n"
												. ($changedate?lang('old end date'):lang('end date')) . ': ' . $sedate['date_formatted'] . ' '
												. ($changedate?lang('new end date') . ': ' . $snedate['date_formatted']:'') . "\n";
									}
								}
							}
							break;
						case 'hours limit':
							$send_alarm = $notify_hours ? true : false;
							$msg = lang($event) . ': ' . $values['ptime'] . "\n"
									. lang('hours used total') . ': ' . $values['uhours_jobs_all'];
							break;
						case 'budget limit':
							$send_alarm = $notify_budget ? true : false;
							$msg = lang($event) . ': ' . $values['budget'] . "\n"
									. lang('budget used total') . ': ' . $GLOBALS['phpgw_info']['user']['preferences']['common']['currency']
									. ' ' . $values['u_budget_jobs'];
							break;
						case 'assignment to project':
							$send_alarm = true;
							$msg = lang($event) . ': ' . $project_name;
							break;
						case 'project date due':
							$send_alarm = $event_type=='project date due' ? true : false;
							$msg = lang($event) . ': ' . $values['edate'];
							break;
						case 'milestone date due':
							$send_alarm = $event_type=='milestone date due' ? true : false;
							$msg = lang($event) . ': ' . $values['edate'] . "\n";
							$msg .= lang('milestone') . ': ' . $values['ms_title'] . "\n";
							$msg .= lang('project') . ':' . $values['project_name'];
							break;
					}

					//create the url for automatic login
					$link_data = array
					(
						'phpgw_forward'		=> '/index.php',
						'phpgw_menuaction'	=> 'projects.uiprojects.view_project',
						'phpgw_project_id'	=> $values['project_id'],
						'phpgw_action'		=> $values['action']
					);

					$param_list = '';
					$is_first_param = true;

					foreach( $link_data as $param_name => $param_val )
					{
						$param_val = urlencode($param_val);

						$param_list .= ($is_first_param?'?':'&') . $param_name . '=' . $param_val;
						$is_first_param = false;
					}

					$_SERVER['HTTP_HOST'] = 'projektmanagement.hannover';
					$msg .= "\n\n" . 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['phpgw_info']['server']['webserver_url'] . '/login.php' . $param_list;
					//$msg .= "\n\n" . $GLOBALS['phpgw']->link('/index.php',$link_data);

					if( $send_alarm )
					{
						$prefs_co = CreateObject('phpgwapi.preferences',$co);
						$prefs_co->read_repository();
						$sender = $prefs_co->email_address($co);

						unset($prefs_co);

						$prefs = CreateObject('phpgwapi.preferences', $emp_events[$k]['account_id']);
						$prefs->read_repository();

						$msgtype = '"projects";';

						if( !is_object($GLOBALS['phpgw']->send) )
						{
							$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
						}

						//print_debug('UserID',$emp['account_id']);

						$to = $prefs->email_address($emp_events[$k]['account_id']);

						/*if (empty($to) || $to[0] == '@' || $to[0] == '$')	// we have no valid email-address
						{
							//echo "<p>boprojects::send_update: Empty email adress for user '".$emp_events[$k]['emp_name']."' ==> ignored !!!</p>\n";
							continue;
						}*/
						//echo 'Email being sent to ' . $to;

						$subject = $GLOBALS['phpgw']->send->encode_subject($subject);

						$returncode = $GLOBALS['phpgw']->send->msg('email',$to,$subject,$msg,''/*$msgtype*/,'','','',$sender);
						//echo "<p>send(to='$to', sender='$sender'<br>subject='$subject') returncode=$returncode<br>".nl2br($body)."</p>\n";

						if ( !$returncode )	// not nice, but better than failing silently
						{
							echo '<p><b>boprojects::send_alarm</b>: ' . lang("Failed sending message to '%1' #%2 subject='%3', sender='%4' !!!", $to, $emp['account_id'], htmlspecialchars($subject), $sender) . "<br>\n";
							echo '<i>'.$GLOBALS['phpgw']->send->err['desc'] . "</i><br>\n";
							echo lang('This is mostly caused by a not or wrongly configured SMTP server. Notify your administrator.') . "</p>\n";
							echo '<p>' . lang('Click %1here%2 to return to projects.','<a href="'.$GLOBALS['phpgw']->link('/projects/').'">','</a>') . "</p>\n";
						}

						unset($prefs);
					}
				}
			}
			return $returncode;
		}

		function activities_list( $project_id, $billable )
		{
			$activities_list = $this->soprojects->soconfig->activities_list($project_id, $billable);
			return $activities_list;
		}

		function select_activities_list( $project_id, $billable )
		{
			$activities_list = $this->soprojects->soconfig->select_activities_list($project_id, $billable);
			return $activities_list;
		}

		function select_pro_activities( $project_id, $pro_parent, $billable )
		{
			$activities_list = $this->soprojects->soconfig->select_pro_activities($project_id, $pro_parent, $billable);
			return $activities_list;
		}

		function select_hours_activities( $project_id, $act )
		{
			$activities_list = $this->soprojects->soconfig->select_hours_activities($project_id, $act);
			return $activities_list;
		}

		function isprojectadmin( $action = 'pad' )
		{
			return $this->soprojects->soconfig->isprojectadmin($action);
		}

		function read_prefs( $default = true )
		{
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['projects']['columns']) )
			{
				$cols = $GLOBALS['phpgw_info']['user']['preferences']['projects']['columns'];
				$prefs['columns'] = explode(',',$cols);
			}
			else if( $default )
			{
				$prefs['columns'] = array('priority','number','customerout','coordinatorout','edateout');
			}

			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['projects']['cscolumns']) )
			{
				$prefs['cscolumns'] = explode(',',$GLOBALS['phpgw_info']['user']['preferences']['projects']['cscolumns']);
			}
			else if( $default )
			{
				$prefs['cscolumns'] = array('title');
			}

			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['projects']['send_status_mail']) )
			{
				$prefs['send_status_mail'] = (bool) ($GLOBALS['phpgw_info']['user']['preferences']['projects']['send_status_mail']);
			}
			else if( $default )
			{
				$prefs['send_status_mail'] = true;
			}

			$prefs['currency'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'];
			$prefs['mainscreen_showevents'] = $GLOBALS['phpgw_info']['user']['preferences']['projects']['mainscreen_showevents'];

			return $prefs;
		}

		function check_prefs()
		{
			$error = array();
			$prefs = $this->read_prefs(false);

			if ( !isset($prefs['currency']) )
			{
				$error[] = lang('please specify the currency in the global preferences section');
			}

			if( !isset($prefs['columns']) )
			{
				$error[] = lang('please choose the columns to list in the projects preferences section');
			}

			$config = $this->soprojects->soconfig->get_site_config(array('default' => false));

			if ( !isset($config['accounting']) )
			{
				$error[] = lang('if you are an administrator, please edit the site configuration for projects in the admin section');
				$error[] = lang('if you are not an administrator, please inform the administrator to configure projects');
			}

			if( is_array($error) && !empty($error) )
			{
				return $error;
			}
			else
			{
				return true;
			}
			//return $error;
		}

		function get_prefs()
		{
			return $this->read_prefs();
		}

		function get_employee_roles( $data )
		{
			$formatted = isset($data['formatted']) ? $data['formatted'] : false;

			$emp_roles = $this->soprojects->read_employee_roles($data);

			//_debug_array($emp_roles);

			if( is_array($emp_roles) )
			{
				foreach( $emp_roles as $emp )
				{
					if ( is_array($emp['events']) && $formatted )
					{
						$eformatted = '';
						$eformatted = '<table width="100%" border="0" cellpadding="0" cellspacing="0">' . "\n";

						for ( $i=0; $i < count($emp['events']); $i++ )
						{
							$e = $this->soprojects->id2item( array
							(
								'action'	=> 'event',
								'item_id'	=> $emp['events'][$i],
								'item'		=> 'event_name'
							));

							$eformatted .= '<tr><td width="100%">' . lang($e) . '</td></tr>' . "\n";
						}
						$eformatted .= '</table>';
					}

					$user[] = array
					(
						'r_id'			=> $emp['r_id'],
						'account_id'	=> $emp['account_id'],
						'emp_name'		=> $GLOBALS['phpgw']->common->grab_owner_name($emp['account_id']),
						'role_id'		=> $emp['role_id'],
						'role_name'		=> $GLOBALS['phpgw']->strip_html($this->soprojects->id2item(array
											(
												'item_id'	=> $emp['role_id'],
												'item'		=> 'role_name',
												'action'	=> 'role'
											))),
						'events'		=> $formatted ? $eformatted : $emp['events']
					);
				}
				return $user;
			}
			return false;
		}

		function save_employee_role( $values )
		{
			$old_roles = $this->soprojects->read_employee_roles( array
			(
				'project_id' => $values['project_id'],
				'account_id' => $values['account_id']
			));

			if( is_array($old_roles) )
			{
				list($old_roles) = $old_roles;
				$values['r_id'] = $old_roles['r_id'];
			}

			$this->soprojects->save_employee_role($values,( is_array($old_roles) ? true : false ));

			if( is_array($old_roles['events']) && is_array($values['events']) )
			{
				$event_role_id = $this->soprojects->item2id(array('item' => 'assignment to role'));
				$values['role_id'] = intval($values['role_id']);

				if( !in_array($event_role_id,$old_roles['events']) && in_array($event_role_id,$values['events']) && $values['role_id'] > 0 )
				{
					$send_role = true;
				}
				if( in_array($event_role_id,$values['events']) && intval($old_roles['role_id']) != $values['role_id'] && $values['role_id'] > 0 )
				{
					$send_role = true;
				}

				if( $send_role )
				{
					$values['event_type'] = 'assignment to role';
					$this->send_alarm($values);
				}

				$event_assignpro_id = $this->soprojects->item2id(array('item' => 'assignment to project'));

				if( !in_array($event_assignpro_id,$old_roles['events']) && in_array($event_assignpro_id,$values['events']) )
				{
					$values['event_type'] = 'assignment to project';
					$this->send_alarm($values);
				}
			}

			if( !is_array($old_roles['events']) && is_array($values['events']) )
			{
				$values['event_type'] = 'assignrolepro';
				$this->send_alarm($values);
			}
		}

		function list_roles()
		{
			$roles = $this->soprojects->soconfig->list_roles(array
			(
				'start'	=> $this->start,
				'sort'	=> $this->sort,
				'order'	=> $this->order,
				'query'	=> $this->query,
				'limit'	=> $this->limit
			));

			$this->total_records = $this->soprojects->soconfig->total_records;

			if( is_array($roles) )
			{
				foreach( $roles as $role )
				{
					$emp_roles[] = array
					(
						'role_id'	=> $role['role_id'],
						'role_name'	=> $GLOBALS['phpgw']->strip_html($role['role_name'])
					);
				}
				return $emp_roles;
			}
			return false;
		}

		function get_granted_roles( $project_id )
		{
			$emps = $this->selected_employees($project_id);
			$roles	= $this->get_employee_roles($project_id);

			if( is_array($emps) )
			{
				foreach( $emps as $emp )
				{
					$assigned_role = '';

					for( $i=0; $i < count($roles); $i++ )
					{
						if( $roles[$i]['account_id'] == $emp['account_id'] )
						{
							$assigned_role = $roles[$i]['role_name'];
						}
					}

					$assigned[] = array
					(
						'emp_name'	=> $GLOBALS['phpgw']->common->display_fullname($emp['account_lid'], $emp['account_firstname'], $emp['account_lastname']),
						'role_name'	=> $assigned_role
					);
				}
				return $assigned;
			}
			return false;
		}

		function list_events( $type = '' )
		{
			return $this->soprojects->soconfig->list_events($type);
		}

		function list_surcharges( $charge_id = 0 )
		{
			return $this->soprojects->soconfig->list_surcharges($charge_id);
		}

		function get_event_extra( $type = '' )
		{
			return $this->soprojects->get_event_extra($type);
		}

		function action_format( $selected = 0,$action = 'role',$type = '' )
		{
			$this->limit = false;

			switch( $action )
			{
				case 'event':
					$list = $this->list_events($type);
					break;
				case 'charge':
					$list = $this->list_surcharges();
					break;
				default:
					$list = $this->list_roles();
					break;
			}

			if( !is_array($selected) )
			{
				$selected = explode(',', $selected);
			}

			//_debug_array($selected);

			$id		= $action . '_id';
			$name	= $action . '_name';

			if( is_array($list) )
			{
				foreach( $list as $li )
				{
					$list_list .= '<option value="' . $li[$id] . '"';

					if( in_array($li[$id], $selected) )
					{
						$list_list .= ' selected';
					}

					$list_list .= '>' . ($action=='event' ? lang($li[$name]) : $li[$name]) . '</option>' . "\n";
				}

				return $list_list;
			}
			return false;
		}

		function get_folder_linkdata()
		{
			$ui_base = CreateObject('projects.uiprojects_base');
			$this->status = $ui_base->status;

			$data = array(
				'targetView' => $ui_base->getTargetView(),
				'status'     => $this->status,
				'project_id' => $ui_base->project_id
			);

			return $data;
		}

		function get_folder_content()
		{
			$bofolders = CreateObject('folders.bofolders');

			if( !$bofolders )
			{
				return false;
			}

			$ui_base = CreateObject('projects.uiprojects_base');

			$projects_linkdata = $bofolders->getAppLinkData('projects');
			//_debug_array($projects_linkdata);

			if( isset($projects_linkdata['targetView']) )
			{
				$targetView = $projects_linkdata['targetView'];
			}
			else
			{
				$targetView = false;
			}

			if( !$this->status && isset($projects_linkdata['status']) )
			{
				$this->status = $projects_linkdata['status'];
			}

			if( isset($projects_linkdata['project_id']) )
			{
				$active_project_id = $projects_linkdata['project_id'];
			}
			else
			{
				$active_project_id = 0;
			}

			$projects = $this->list_projects(array
			(
				'limit' => false,
				'action' => 'all',
				'no_formatted_level' => true
			));

			if( is_array($projects) )
			{
				usort($projects, array('boprojects', 'cmp_projects_folders_content'));

				foreach( $projects as $pro )
				{
					if($pro['project_id'] == $active_project_id)
					{ // selected project
						$text = '<b>'.$pro['title'].'</b>';
					}
					else
					{
						$text = $pro['title'];
					}

					$return['projects_'.$pro['project_id']] = array
					(
						'text'      => $text,
						'title'     => $pro['title'],
						'parent_id' => 'projects_'.$pro['parent'],
						'href'      => $ui_base->createViewUrl($targetView, array('project_id' => $pro['project_id'], 'pro_main' => $pro['main'], 'pro_parent' => $pro['parent'])),
						'target'    => '_parent'
					);
				}

				$return['projects_0'] = array
				(
					'text'      => ($active_project_id==0? '<b>'.lang('projects').'</b>' : lang('projects')),
					'parent_id' => '0',
					'href'      => $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.list_projects','status'=>'active')),
					'target'    => '_parent',
					'icon'      => ''
				);
			}

			return array('content' => $return);
		}

		function cmp_projects_folders_content($a, $b)
		{
			return strcasecmp($a['title'], $b['title']);
		}

		/**
		* Update a project plan bottom up setting in sub projects.
		* @param integer $main_project_id main project to update the plan bottom up setting
		* @param string $plan_bottom_up the plan bottom up setting to set
		* @return void
		*/
		function plan_bottom_up_set_job_setting( $main_project_id, $plan_bottom_up )
		{
			if( $plan_bottom_up != 'Y' )
			{
				$plan_bottom_up = 'N';
			}

			$this->soprojects->plan_bottom_up_set_job_setting($main_project_id, $plan_bottom_up);
		}

		/**
		* Update a project and its parents.
		* @param integer $project_id project to update
		* @param array   $update_values value for the update
		* @return bool true if update was successful, false if update fail
		*/
		function update_parent( $project_id, $update_values )
		{
			if( $project_id <= 0 )
			{
				return true;
			}

			if( !isset($update_values['ptime']) || !isset($update_values['budget']) || !isset($update_values['e_budget']) )
			{
				return false;
			}

			// get project
			$pro = $this->soprojects->read_single_project($project_id);

			if( !$pro || !is_array($pro) )
			{
				return false;
			}

			// 1. update ptime
			// 1.1 bottom up and top down: update the chield value
			$pro['ptime_childs'] = $pro['ptime_childs'] + $update_values['ptime'];
			// 1.2 bottom up: update the item sum value
			if( $pro['plan_bottom_up'] == 'Y' )
			{
				$pro['ptime'] = $pro['ptime'] + $update_values['ptime'];
			}

			// 2. update budget

			// 2.1 bottom up and top down: update the chield value
			$pro['budget_childs'] = $pro['budget_childs'] + $update_values['budget'];

			// 2.2 bottom up: update the item sum value
			if($pro['plan_bottom_up'] == 'Y')
			{
				$pro['budget'] = $pro['budget'] + $update_values['budget'];
			}

			// 3. update budget

			// 3.1 bottom up and top down: update the chield value
			$pro['e_budget_childs'] = $pro['e_budget_childs'] + $update_values['e_budget'];

			// 3.2 bottom up: update the item sum value
			if($pro['plan_bottom_up'] == 'Y')
			{
				$pro['e_budget'] = $pro['e_budget'] + $update_values['e_budget'];
			}

			// 4. bottom up: update accounting factor if factor project is active
			if( ($pro['plan_bottom_up'] == 'Y') && ($pro['accounting'] == 'project') && (intval($pro['ptime']) > 0) )
			{
				$pro['project_accounting_factor']   = $pro['budget']/intval($pro['ptime']/60);
				$pro['project_accounting_factor_d'] = $pro['project_accounting_factor']*$this->siteconfig['hwday'];
			}

			// 5. bottom up: update (p)sdate (p)edate
			if( $pro['plan_bottom_up'] == 'Y' )
			{
				$time     = time();
				$sdate    = $time;  // start date
				$psdate   = $time;  // planed start date
				$edate    = $time;  // end date
				$pedate   = $time;  // planed end date

				// get sub projects
				$subs = $this->get_sub_projects(array('project_id' => $project_id));

				// for each sub project calculate calculate budget and planned time
				while( list($subNum, $subData) = each($subs) )
				{
					// get planned dates (earliest start and latest end date)
					// and sum of workhours and budget
					if( isset($subData['sdate']) && ($subData['sdate'] < $sdate) )
					{
						$sdate = $subData['sdate'];
					}
					if( isset($subData['psdate']) && ($subData['psdate'] < $psdate) )
					{
						$psdate = $subData['psdate'];
					}
					if( isset($subData['edate']) && ($subData['edate'] > $edate) )
					{
						$edate = $subData['edate'];
					}
					if( isset($subData['pedate']) && ($subData['pedate'] > $pedate) )
					{
						$pedate = $subData['pedate'];
					}
				}

				// update values
				$pro['sdate']  = ($sdate  != $time) ? $sdate  : $pro['sdate'];
				$pro['psdate'] = ($psdate != $time) ? $psdate : $pro['psdate'];
				$pro['edate']  = ($edate  != $time) ? $edate  : $pro['edate'];
				$pro['pedate'] = ($pedate != $time) ? $pedate : $pro['$pedate'];
			}

			// save project
			$this->soprojects->edit_project($pro);

			// if not bottom up or current project is the root skip updating
			if( ($pro['plan_bottom_up'] == 'N') || ($pro['parent'] <= 0) )
			{
				return true;
			}
			else
			{ 	// bottom up project -> update till root
				return $this->update_parent($pro['parent'], $update_values);
			}
		}

		/**
		* Update a project direct work setting in sub projects.
		* @param integer $main_project_id main project to update the direct work setting
		* @param string $direct_work the direct work setting to set
		* @return void
		*/
		function direct_work_set_job_setting( $main_project_id, $direct_work )
		{
			if( $direct_work != 'N' )
				$direct_work = 'Y';

			$this->soprojects->direct_work_set_job_setting($main_project_id, $direct_work);
		}

		/**
		* Send all employees a project status mail. This method is called by async servoce.
		* @param array $date contains the last async date
		* @return boolean true if successfully send mails, otherwise false
		*/
		function async_workhours_booking( $data=array() )
		{
			set_time_limit(0);

			if( !$data || !is_array($data) || !isset($data['book_type']) || !isset($data['book_month']) || !isset($data['book_year']) )
			{
				return false;
			}

			$book_type = $data['book_type'];

			if( $book_type == 0 )
			{
				return false;
			}

			$book_year  = $data['book_year'];
			$book_month = $data['book_month'];

			$book_start = mktime(0, 0, 0, $book_month, 1, $book_year);
			$book_month_days = cal_days_in_month(CAL_GREGORIAN, $book_month, $book_year);
			$book_end = mktime(23, 59, 59, $book_month, $book_month_days, $book_year);

			//echo date('Y-m-d H:i:s', $book_start). ' - '.date('Y-m-d H:i:s', $book_end).'<br>';

			// call booking method
			$book_values = array
			(
				'sdate'	=>	$book_start,
				'edate' =>	$book_end
			);

			$this->sohours->set_booked($book_values);

			// calculate next booking date
			$holidays		= CreateObject('phpgwapi.calendar_holidays');
			$sbox			= CreateObject('phpgwapi.sbox');
//			$country		= ucfirst($GLOBALS['phpgw']->translation->retranslate($sbox->country_array[$GLOBALS['phpgw']->preferences->data['common']['country']]));
			$country		= ucfirst(lang($sbox->country_array[$GLOBALS['phpgw']->preferences->data['common']['country']]));
			$federal_state	= $holidays->federal_states[$country][$GLOBALS['phpgw']->preferences->data['common']['federalstate']]; // Achtung: bisher existiert nur germany!
			$religion		= $holidays->religions[$GLOBALS['phpgw']->preferences->data['common']['religion']];

			// calc next booking month and year
			if( $book_month == 12 )
			{
				$next_book_month = 1;
				$next_book_year  = $book_year + 1;
			}
			else
			{
				$next_book_month = $book_month + 1;
				$next_book_year  = $book_year;
			}

			// calc next async date for run booking
			if( $next_book_month == 12 )
			{
				$next_async_month = 1;
				$next_async_year  = $next_book_year + 1;
			}
			else
			{
				$next_async_month = $next_book_month + 1;
				$next_async_year  = $next_book_year;
			}

			$workdays = $book_type;
			//echo '<br>add_number_of_workdays(1,'.$next_async_month.','.$next_async_year.','.$workdays.','.$country.','.$federal_state.','.$religion.',&$new_d,&$new_m,&$new_y)<br>';

			$new_days	= $holidays->add_number_of_workdays(1,$next_async_month,$next_async_year,$workdays,$country,$federal_state,$religion);
			$new_d		= $new_days['newday'];
			$new_m		= $new_days['newmonth'];
			$new_y		= $new_days['newyear'];

			unset($new_days);

			$ts_book = mktime(0, 0, 0, $new_m, $new_d, $new_y) - 1;

			//echo date('Y-m-d H:i:s', $ts_book).'<br>';

			$async		= CreateObject('phpgwapi.asyncservice');
			$aid		= 'projects-workhours-booking-';
			$async_data	= array
			(
				'id'			=> $aid.$next_book_year.'-'.$next_book_month,
				'next'			=> $ts_book,
				'times'			=> $ts_book,
				'account_id'	=> $GLOBALS['phpgw_info']['user']['account_id'],
				'method'		=> 'projects.boprojects.async_workhours_booking',
				'data' 			=> array
									(
										'book_type'  => $book_type,
										'book_year'  => $next_book_year,
										'book_month' => $next_book_month
									)
			);

			$async->write($async_data);

			return true;
		}

		/**
		* Send all employees a project status mail. This method is called by async service.
		* @param array $date contains the last async date
		* @return boolean true if successfully send mails, otherwise false
		*/
		function async_worktime_statusmail( $data=array() )
		{
			set_time_limit(0);
			$accounts = CreateObject('phpgwapi.accounts');

			if( ($data == false) || !is_array($data) || !isset($data['mail_type']) || !isset($data['last_date']) )
			{
				return false;
			}

			$start_date	= $data['last_date'];
			$end_date	= time();
			$mail_type	= $data['mail_type'];

			// create list of employees
			$employees	= $this->read_projectsmembers_acl();

			if( is_array($employees) )
			{
				// for each employee crceate and send status mail
				while( list($employee_id, $employee_projects) = each($employees) )
				{
					$prefs = CreateObject('phpgwapi.preferences', $employee_id);
					$prefs->read_repository();

					if( (isset($prefs->data['projects']['send_status_mail']) ) && ($prefs->data['projects']['send_status_mail'] == false) )
					{
						continue;
					}

					$employee_lid = '';
					$employee_pname = '';
					$employee_lname = '';

					$accounts->get_account_name($employee_id, $employee_lid, $employee_pname, $employee_lname);
					$fullname = $employee_pname.' '.$employee_lname;

					$employee_email = $prefs->email_address($employee_id);

					if( !$employee_email )
					{
						error_log("No email address found for " . $fullname . " [" . $employee_lid . "]");
						continue;
					}

					// get worktimes for employee
					$worktimes = $this->get_emp_worktimes($employee_id, $start_date, $end_date);
					if( !$worktimes || !(count($worktimes['projects']) > 0) || ($worktimes['sum_minutes_all']==0) )
					{
						continue;
					}

					$proList = array();
					while( list($no_use,$project_id) = each($worktimes['projects']) )
					{
						$project_data  = $worktimes[$project_id]['project_data'];
						if( !$project_data )
						{
							continue;
						}

						$proList[$project_id] = $project_data;
					}

					$proTree = $this->buildTree($proList, 0);

					// create mail
					$body		= '';
					$timelen	= date('d.m.Y', $start_date).' - '.date('d.m.Y', $end_date);
					$subject	= lang('time tracker').': '.$timelen;
					$newline	= "\r\n";

					$body .= str_repeat('=',75).$newline;
					$headline = 'Stundenbersicht ber alle Projekte im Zeitraum ' . $timelen;
					$body .= $this->format_string($headline, 75, '', ' ', STR_PAD_BOTH) . $newline;
					$body .= $this->format_string($fullname, 75, '', ' ', STR_PAD_BOTH) . $newline;
					$body .= str_repeat('=',75) . $newline;
					$body .= $newline;

					$body .= $this->format_string(lang('Projects'), 51, ' ', ' ', STR_PAD_BOTH);
					$body .= $this->format_string(lang('Work Hours'), 24, ' ', ' ', STR_PAD_BOTH);
					$body .= $newline;

					$body .= $this->format_string(lang('Number'), 16, ' ', ' ', STR_PAD_BOTH);
					$body .= $this->format_string(lang('Name'),   35, ' ', ' ', STR_PAD_BOTH);
					$body .= $this->format_string(lang('Project').' ', 8, ' ', ' ', STR_PAD_LEFT);
					$body .= $this->format_string(lang('Travel').' ',  8, ' ', ' ', STR_PAD_LEFT);
					$body .= $this->format_string(lang('Sum').' ',     8, ' ', ' ', STR_PAD_LEFT);
					$body .= $newline.str_repeat('-',75).$newline;

					while( list($project_id,$project_data) = each($proTree) )
					{
						if( !$project_data )
						{
							continue;
						}

						$body .= $this->format_string($project_data['project_number'], 16);
						$body .= $this->format_string($project_data['project_title'], 35);
						$body .= $this->format_string($this->format_minutes($project_data['sum_minutes_worktime']).' ', 8, ' ', ' ', STR_PAD_LEFT);
						$body .= $this->format_string($this->format_minutes($project_data['sum_minutes_journey']).' ',  8, ' ', ' ', STR_PAD_LEFT);
						$body .= $this->format_string($this->format_minutes($project_data['sum_minutes_all']).' ',      8, ' ', ' ', STR_PAD_LEFT);
						$body .= $newline;
					}

					$body .= $newline.str_repeat('=',75).$newline;
					$body .= $this->format_string(lang('total').' '.lang('all').' '.lang('projects'), 51);
					$body .= $this->format_string($this->format_minutes($worktimes['sum_minutes_worktime']).' ', 8, ' ', ' ', STR_PAD_LEFT);
					$body .= $this->format_string($this->format_minutes($worktimes['sum_minutes_journey']).' ',  8, ' ', ' ', STR_PAD_LEFT);
					$body .= $this->format_string($this->format_minutes($worktimes['sum_minutes_all']).' ',      8, ' ', ' ', STR_PAD_LEFT);
					$body .= $newline.str_repeat('=',75).$newline;

					// send mail
					if( !is_object($GLOBALS['phpgw']->send) )
					{
						$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
					}

					if( !isset($GLOBALS['phpgw_info']['server']['admin_mails']) )
					{ // only as workaround, if no admin mail specified
						$sender = $employee_email;
					}
					else
					{ // admin mails are a comma separated list of mail addresses
						$admin_mails = explode(",", $GLOBALS['phpgw_info']['server']['admin_mails']);
						if( isset($admin_mails[0]) )
						{
							$sender = $admin_mails[0];
						}
						else
						{
							$sender = $employee_email;
						}
					}

					$to = $employee_email;
					$subject = $GLOBALS['phpgw']->send->encode_subject($subject);

					$returncode = $GLOBALS['phpgw']->send->msg('email',$to,$subject,$body,'','','','',$sender);
					if ( !$returncode )	// not nice, but better than failing silently
					{
						error_log('fail to send status mail (' . $employee_id . ' - ' . $employee_email . ') for ' . $fullname);
					}
					else
					{
						error_log('send status mail to (' . $employee_id . ' - ' . $employee_email . ') ' . $fullname);
					}

				} // end while employee
			} // end if employee

			$this->update_async($mail_type, $end_date);

			return true;
		}

		function buildTree( $proList, $proParent )
		{
			$retProList = array();

			while( list($pro_id, $pro_data) = each($proList) )
			{
				if( $pro_data['project_parent'] == $proParent )
				{
					$level_title = $pro_data['project_title'];

					if( $pro_data['project_level'] > 0 )
					{
						$level_title = str_repeat(' ', $pro_data['project_level']).$level_title;
					}

					$pro_data['project_title'] = $level_title;
					$retProList[$pro_id] = $pro_data;

					unset($proList[$pro_id]);

					$subProList = $this->buildTree($proList, $pro_id);

					while( list($sub_pro_id, $sub_pro_data) = each($subProList) )
					{
						$retProList[$sub_pro_id] = $sub_pro_data;
					}
				}
			}
			return $retProList;
		}

		function async_worktime_warnmail( $data=array() )
		{
			set_time_limit(0);

			if( ($data == false) || !is_array($data) || !isset($data['warnmail_type']) || !isset($data['warnmail_month']) || !isset($data['warnmail_year']) )
			{
				return false;
			}

			$accounts	= CreateObject('phpgwapi.accounts');
			$holidays	= CreateObject('phpgwapi.calendar_holidays');
			$sbox		= createobject('phpgwapi.sbox');

			$warnmail_type  = $data['warnmail_type'];
			$warnmail_year  = $data['warnmail_year'];
			$warnmail_month = $data['warnmail_month'];

			$num_month_days = cal_days_in_month(CAL_GREGORIAN, $warnmail_month, $warnmail_year);
			$sdate = mktime(0, 0, 0, $warnmail_month, 1, $warnmail_year);
			$edate = mktime(23, 59, 59, $warnmail_month, $num_month_days, $warnmail_year);

			$acl_projectmembers = $this->read_projectsmembers_acl();

			//echo '<pre>';
			//print_r($acl_projectmembers);

			while( list($employee_id, $employee_projects) = each($acl_projectmembers) )
			{
				$prefs = CreateObject('phpgwapi.preferences', $employee_id);
				$prefs->read_repository();

				$employee_lid = $employee_pname = $employee_lname = '';
				$accounts->get_account_name($employee_id, $employee_lid, $employee_pname, $employee_lname);
				$fullname = $employee_pname . ' ' . $employee_lname;

				$employee_email = $prefs->email_address($employee_id);

				if( !$employee_email || (strstr($employee_email, "@") == $employee_email) || ($fullname == ' ') )
				{
					error_log("No email address found for " . $fullname . " [" . $employee_lid . "]");
					continue;
				}

				$pref_country = $prefs->data['common']['country'];
				if( !$pref_country )
				{ // no user prefs
					$pref_country = $GLOBALS['phpgw']->preferences->data['common']['country'];
				}
				if( !$pref_country )
				{ // no predefined user prefs
					$pref_country = 'DE';
				}

				$pref_f_state = $prefs->data['common']['federalstate'];
				if( !$pref_f_state )
				{ // no user prefs
					$pref_f_state = $GLOBALS['phpgw']->preferences->data['common']['federalstate'];
				}
				if( !$pref_f_state )
				{ // no predefined user prefs
					$pref_f_state = 8; // Niedersachsen
				}

				$pref_religion = $prefs->data['common']['religion'];
				if( !$pref_religion )
				{ // no user prefs
					$pref_religion = $GLOBALS['phpgw']->preferences->data['common']['religion'];
				}
				if( !$pref_religion )
				{ // no predefined user prefs
					$pref_religion = 0; // Atheistisch
				}

//				$country = ucfirst($GLOBALS['phpgw']->translation->retranslate($sbox->country_array[$pref_country]));
				$country		= ucfirst(lang($sbox->country_array[$pref_country]));
				$federal_state	= $holidays->federal_states[$country][$pref_f_state]; // Achtung: bisher existiert nur germany!
				$religion		= $holidays->religions[$pref_religion];

				// number of workdays in month, depands on employees location
				$num_work_days	= $holidays->get_number_of_workdays(1, $warnmail_month, $warnmail_year, $num_month_days, $warnmail_month, $warnmail_year, $country, $federal_state, $religion);

				// get employee weekly worktime
				$acc_data = $this->soconfig->read_employees( array
				(
					'start'=>0,
					'limit'=>false,
					'account_id'=>$employee_id,
					'date' => mktime(0, 0, 0, $warnmail_month, 1, $warnmail_year)
				));

				//echo '<pre>';
				//print_r($acc_data);

				if( !$acc_data )
				{
					error_log("No weekly workhours specified for " . $fullname . " [" . $employee_lid . "]");
					$weekly_workhours = 40.0;
				}
				else
				{
					$weekly_workhours = $acc_data[0]['weekly_workhours'];
				}

				$daily_workhours = round($weekly_workhours/5, 2);
				$needed_workhours_min = $daily_workhours * $num_work_days * 60;

				// get employee booked workhours
				$worktimes = $this->get_emp_worktimes($employee_id, $sdate, $edate);
				if( !$worktimes || !(count($worktimes['projects']) > 0) || ($worktimes['sum_minutes_all']==0) )
				{
					continue;
				}

				$booked_workhours_min = $worktimes['sum_minutes_all'];
				$rest_workhours_min   = $needed_workhours_min - $booked_workhours_min;

				if( $booked_workhours_min >= $needed_workhours_min )
				{
					continue; // has enough workhours booked -> no warnmail needed
				}

				// create mail
				$body		= '';
				$timelen	= date('d.m.Y', $sdate).' - '.date('d.m.Y', $edate);
				$subject	= 'Zeiterfassung '.$fullname.' : '.$timelen;
				$newline	= "\r\n";

				$needed_workhours_formated = $this->format_minutes($needed_workhours_min);
				$booked_workhours_formated = $this->format_minutes($booked_workhours_min);
				$rest_workhours_formated   = $this->format_minutes($rest_workhours_min);
				$weekly_workhours_formated = $this->format_minutes($weekly_workhours * 60);

				$body .= 'Hallo '.$fullname.', '.$newline;
				$body .= 'bitte die Stunden f�r den Zeitraum '.$timelen.' nachtragen.'.$newline.$newline;
				$body .= '   w�chentliche Arbeitszeit:'.$this->format_string($weekly_workhours_formated, 10, ' ', ' ', STR_PAD_LEFT).$newline;
				$body .= 'x  Anzahl Werktage im Monat:'.$this->format_string($num_work_days, 10, ' ', ' ', STR_PAD_LEFT).$newline;
				$body .= str_repeat('-',38).$newline;
				$body .= '=  monatliche Arbeitszeit  :'.$this->format_string($needed_workhours_formated, 10, ' ', ' ', STR_PAD_LEFT).$newline;
				$body .= '-  erfasste Arbeitszeit    :'.$this->format_string($booked_workhours_formated, 10, ' ', ' ', STR_PAD_LEFT).$newline;
				$body .= str_repeat('=',38).$newline;
				$body .= '=  fehlende Arbeitszeit    :'.$this->format_string($rest_workhours_formated, 10, ' ', ' ', STR_PAD_LEFT).$newline;
				$body .= str_repeat('=',38).$newline;
				$body .= $newline.$newline;
				$body .= 'Diese Mail wurde automatisch vom Projektmanagementsystem generiert.';
				$body .= $newline.$newline;

				// send mail
				if( !is_object($GLOBALS['phpgw']->send) )
				{
					$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
				}

				$admins = $GLOBALS['phpgw_info']['server']['admin_mails'];
				if( !$admins )
				{ // only as workaround, if no admin mail specified
					$sender = $employee_email;
				}
				else
				{	// admin mails are a comma separated list of mail addresses
					$admin_mails = explode(",", $admins);
					if( isset($admin_mails[0]) )
					{
						$sender = $admin_mails[0];
					}
					else
					{
						$sender = $employee_email;
					}
				}

				$to			= $employee_email;
				$subject	= $GLOBALS['phpgw']->send->encode_subject($subject);

				$boconfig	= CreateObject('projects.boconfig');

				$values['action'] = 'get';
				$warnmail = $boconfig->config_worktime_warnmail($values);

				$cc = $warnmail['warnmail_email_address'];
				$bcc = '';

				$returncode  = $GLOBALS['phpgw']->send->msg('email',$to,$subject,$body,'',$cc,$bcc,'',$sender);
				if ( !$returncode )	// not nice, but better than failing silently
				{
					error_log('fail to send warn mail ('.$employee_id.' - '.$employee_email.') for '.$fullname);
				}
				else
				{
					error_log('send warn mail to ('.$employee_id.' - '.$employee_email.') '.$fullname);
				}
			}

			if( $warnmail_month == 12 )
			{
				$warnmail_month = 1;
				$warnmail_year = $warnmail_year+1;
			}
			else
			{
				$warnmail_month = $warnmail_month+1;
			}

			$next = $this->update_async_warnmail($warnmail_month, $warnmail_year, $warnmail_type);
			//echo date("d. m. Y", $next);
		}

		function update_async_warnmail( $warnmail_month, $warnmail_year, $warnmail_type )
		{
			// set async service
			$async	= CreateObject('phpgwapi.asyncservice');
			$aid	= 'projects-worktime-warnmail-';

			$warnmail_date = $this->calculate_warnmail_date($warnmail_month, $warnmail_year, $warnmail_type);

			if( !$warnmail_date )
			{
				return false;
			}

			$async_data = array
			(
				'id'			=> $aid.$warnmail_year.'-'.$warnmail_month,
				'next'			=> $warnmail_date,
				'times'			=> $warnmail_date,
				'account_id'	=> $GLOBALS['phpgw_info']['user']['account_id'],
				'method'		=> 'projects.boprojects.async_worktime_warnmail',
				'data'			=> array
									(
										'warnmail_type'  => $warnmail_type,
										'warnmail_year'  => $warnmail_year,
										'warnmail_month' => $warnmail_month
									)
			);

			$async->write($async_data);

			return $warnmail_date;
		}

		// calculate calculate_warnmail_date
		function calculate_warnmail_date( $warnmail_month, $warnmail_year, $warnmail_type )
		{
			/* no use until need or KH fix add_number_of_workdays_
			$holidays = CreateObject('phpgwapi.calendar_holidays');
			$sbox = createobject('phpgwapi.sbox');
//			$country = ucfirst($GLOBALS['phpgw']->translation->retranslate($sbox->country_array[$GLOBALS['phpgw']->preferences->data['common']['country']]));
			$country = ucfirst(lang($sbox->country_array[$GLOBALS['phpgw']->preferences->data['common']['country']]));
			$federal_state = $holidays->federal_states[$country][$GLOBALS['phpgw']->preferences->data['common']['federalstate']]; // Achtung: bisher existiert nur germany!
			$religion = $holidays->religions[$GLOBALS['phpgw']->preferences->data['common']['religion']];

			// calculate number of month and work days
			$num_month_days = cal_days_in_month(CAL_GREGORIAN, $warnmail_month, $warnmail_year);
			$num_work_days  = $holidays->get_number_of_workdays(1,$warnmail_month,$warnmail_year,$num_month_days,$warnmail_month,$warnmail_year,$country,$federal_state,$religion);

			// reduce number of work days by number of days before monthly allowance and -1 because we
			// start on first workday of the month and have to reduce this day from the number of workdays
			$num_work_days = $num_work_days - $warnmail_type;

			// calculate day for warnmail sending by add number of workdays to first day of month

			$new_days = $holidays->add_number_of_workdays(1,$warnmail_month,$warnmail_year,$num_work_days,$country,$federal_state,$religion);
			$warnmail_day = $new_days['newday'];
			$warnmail_month = $new_days['newmonth'];
			$warnmail_year = $new_days['newyear'];
			unset($new_days);

			*/
			// use first day of next month for send mail
			if( $warnmail_month == 12 )
			{
				$warnmail_month = 1;
				$warnmail_year = $warnmail_year+1;
			}
			else
			{
				$warnmail_month = $warnmail_month+1;
				$warnmail_year  = $warnmail_year;
			}

			$warnmail_day = 1;

			return mktime(23, 59, 59, $warnmail_month, $warnmail_day, $warnmail_year);
		}

		/**
		* Gets the projects and tracked worktimes of en employee between a start and end date
		* @param integer $employee_id the account id of the employee
		* @param integer $start_date timestamp of start date
		* @param integer $end_date timestamp of end date
		* @return array contains all projects of employee and the tracked worktimes
		*/
		function get_emp_worktimes( $employee_id, $start_date=0, $end_date=0 )
		{
			$worktimes = array();
			$worktimes['sum_minutes_worktime'] = 0;
			$worktimes['sum_minutes_journey'] = 0;
			$worktimes['sum_minutes_all'] = 0;
			$worktimes['projects'] = array();

			$bohours = CreateObject('projects.boprojecthours');
			$emp_worktimes = $bohours->get_emp_worktimes($employee_id, $start_date, $end_date);
			if ( $emp_worktimes === false )
			{
				return $worktimes;
			}

			$this->soprojects->account = $employee_id; // will be used in soprojects
			// get list of employee projects
			$projects_empl = $this->soprojects->get_acl_projects();

			while( is_array($projects_empl) && (list($no_use, $project_id) = each($projects_empl)) )
			{
				$project = $this->soprojects->read_single_project($project_id);
				if( !$project || !is_array($project) )
				{
					continue;
				}

				// create project output
				$project_data = array
				(
					'project_main'			=> $project['main'],
					'project_parent'		=> $project['parent'],
					'project_direct'		=> $project['direct_work'],
					'project_number'		=> $project['number'],
					'project_title'			=> $project['title'],
					'project_sdate'			=> $project['sdate'],
					'project_edate'			=> $project['edate'],
					'project_coord'			=> $project['coordinator'],
					'project_prio'			=> $project['priority'],
					'project_level'			=> $project['level'],
					'sum_minutes_worktime'	=> 0,
					'sum_minutes_journey'	=> 0,
					'sum_minutes_all'		=> 0
				);

				$worktimes['projects'][] = $project_id;
				$worktimes[$project_id]['project_data'] = $project_data;

				if(isset($emp_worktimes[$project_id]))
				{
					$worktimes[$project_id]['project_data']['sum_minutes_worktime'] = $emp_worktimes[$project_id]['sum_minutes_worktime'];
					$worktimes[$project_id]['project_data']['sum_minutes_journey']  = $emp_worktimes[$project_id]['sum_minutes_journey'];
					$worktimes[$project_id]['project_data']['sum_minutes_all']      = $emp_worktimes[$project_id]['sum_minutes_all'];
				}

				// sum up tracked worktime for all projects
				$worktimes['sum_minutes_worktime'] += $worktimes[$project_id]['project_data']['sum_minutes_worktime'];
				$worktimes['sum_minutes_journey']  += $worktimes[$project_id]['project_data']['sum_minutes_journey'];
				$worktimes['sum_minutes_all']      += $worktimes[$project_id]['project_data']['sum_minutes_all'];
			}// end for each project

			return $worktimes;
		}

		/**
		* Update the async service for the worktime status mail.
		* @param string $mail_type specifies the type of status mail repetition (off | weekly | monthly).
		* @param integer $last_date timestamp of the last async.
		* @return boolean true if update successful, otherwise false
		*/
		function update_async( $mail_type, $last_date )
		{
			$async = CreateObject('phpgwapi.asyncservice');
			$aid = 'projects-worktime-statusmail';

			if( $mail_type == 'off' )
			{
				// delete old async
				$async->delete($aid);
			}
			else
			{
				switch( $mail_type )
				{
					case 'weekly':
						$times = array('dow' => '1');
					break;
					case 'monthly':
						// first day in each month 0am
						$times = array('day' => 1);
					break;
					default:
						return false;
					break;
				}

				$async_data = array
				(
					'id'			=> $aid,
					'next'			=> $async->next_run($times),
					'times'			=> $times,
					'account_id'	=> $GLOBALS['phpgw_info']['user']['account_id'],
					'method'		=> 'projects.boprojects.async_worktime_statusmail',
					'data'			=> array
										(
											'last_date' => $last_date,
											'mail_type' => $mail_type
										)
				);

				$async->write($async_data);
			}

			return true;
		}

		/**
		* Convert a minutes in a hours and minutes string like hh:mm
		* @param integer $minutes the minutes to convert
		* @return string the formated string with hh:mm
		*/
		function format_minutes( $minutes = 0 )
		{
			$h = intval($minutes / 60);
			$m = intval($minutes % 60);
			$time_str = sprintf("%02.2d:%02.2d", $h, $m); // hh:mm
			return $this->format_string($time_str, 7, '', ' ', STR_PAD_LEFT);
		}

		/**
		* Pad and format a given string on a certain position. If string is leesser than padlen the string will be filled with the specified padstr.
		* @param string $str the given string
		* @param integer $padlen specifies the pad position
		* @param string $trim specifies the string which is used when reduce the string
		* @param string $padstr specifies the string for filling the given string up to the specified padlen
		* @return string the padded and formated string
		*/
		function format_string( $str, $padlem, $trim='...', $padstr=' ', $padtype=STR_PAD_RIGHT )
		{
			$strlen  = strlen($str);
			$trimlen = strlen($trim);
			$sublen  = $padlem-$trimlen;

			if( $strlen > $sublen )
			{
				$str = substr($str,0,$sublen).$trim;
			}
			else
			{
				$str = str_pad($str, $padlem, $padstr, $padtype);
			}
			return $str;
		}

		/**
		* Get cost accounting information in Diamant format
		*
		* @param integer $month Month for which to get the cost accounting
		* @param integer $year Year for which to get the cost accounting
		* @param integer $voucher_number Voucher_number
		* @param integer $credit_cost_centre Cost centre to credit (employee)
		* @param integer $charge_cost_centre
		* @param integer $cost_unit Cost unit (project)
		* @param integer $amount Amount (hours)
		* @param string $posting_text Posting text
		* @return string Diamant "Betriebsdatenerfassung (BDE)" record format
		*/
		function generate_cost_accounting_record_diamant( $month, $year, $voucher_number, $credit_cost_centre, $charge_cost_centre, $cost_unit, $amount, $posting_text )
		{
			$bde = 'BD,0,' . date('dmY') . ',' . sprintf('%02u%04u',$month,$year) . ',' . $voucher_number . ',';
			$bde .= $credit_cost_centre . ',' . $charge_cost_centre . ',' . $cost_unit . ',';
			$bde .= 'LSTD,,,,,' . number_format($amount,4,'.','') . ',,,' . $posting_text . "\n";
			return($bde);
		}

		/**
		* Get cost accounting information in Diamant format
		*
		* @param integer $month Month for which to get the cost accounting
		* @param integer $year Year for which to get the cost accounting
		* @param integer $location_id primary key of location for which to get the cost accounting
		* @return string Diamant "Betriebsdatenerfassung (BDE)" import format
		*/
		function get_cost_accounting_diamant( $month,$year,$location_id )
		{
			$bde			= '';
			$serial			= 1;
			$location_data	= $this->soconfig->get_single_location($location_id);
			$list			= $this->soprojects->get_project_hours($month,$year,$location_id);
			$max			= count($list);

			for ( $i = 0; $i < $max; ++$i )
			{
				if ( ereg('^I', $list[$i]['p_number']) ) // Intern
				{
					$credit_cost_centre = $list[$i]['cost_centre']; // Kostenstelle (Mitarbeiter)
					if ( intval($credit_cost_centre) != 0 )
					{
						$cost_unit = ''; // Kostentr�ger (Projekt)
						$charge_cost_centre	= 902; // Interne-IT

						// AG �bergreifende Buchungen?
						if ( $list[$i]['minutes'] > 0 )
						{
							$voucher_number	= date('Ymd') . $serial; // Belegnummer YYYYMMDDnum
							$amount			= $list[$i]['minutes']; // Menge (Stunden)
							$posting_text	= substr('"Arbeit ' . $list[$i]['employee'],0,31) . '"'; // Buchungstext (Arbeitszeit vs. Reisezeit)
							$bde			.= $this->generate_cost_accounting_record_diamant($month, $year, $voucher_number, $credit_cost_centre, $charge_cost_centre, $cost_unit, $amount, $posting_text);
							++$serial;
						}
						if ( $list[$i]['journey'] > 0 )
						{
							$voucher_number	= date('Ymd') . $serial; // Belegnummer YYYYMMDDnum
							$amount			= $list[$i]['journey']; // Menge (Stunden)
							$posting_text	= substr('"Reise ' . $list[$i]['employee'],0,31) . '"'; // Buchungstext (Arbeitszeit vs. Reisezeit)
							$bde			.= $this->generate_cost_accounting_record_diamant($month,$year,$voucher_number,$credit_cost_centre,$charge_cost_centre,$cost_unit,$amount,$posting_text);
							++$serial;
						}
					}
				}
				else if ( ereg('^S',$list[$i]['p_number']) ) // PreSales
				{
					$credit_cost_centre = $list[$i]['cost_centre']; // Kostenstelle (Mitarbeiter)
					if ( intval($credit_cost_centre) != 0 )
					{
						$cost_unit = ''; // Kostentr�ger (Projekt)
						$charge_cost_centre = 220; // Presales
						// AG �bergreifende Buchungen?
						if ( $list[$i]['minutes'] > 0 )
						{
							$voucher_number	= date('Ymd') . $serial; // Belegnummer YYYYMMDDnum
							$amount			= $list[$i]['minutes']; // Menge (Stunden)
							$posting_text	= substr('"Arbeit ' . $list[$i]['employee'],0,31) . '"'; // Buchungstext (Arbeitszeit vs. Reisezeit)
							$bde			.= $this->generate_cost_accounting_record_diamant($month,$year,$voucher_number,$credit_cost_centre,$charge_cost_centre,$cost_unit,$amount,$posting_text);
							++$serial;
						}
						if ( $list[$i]['journey'] > 0 )
						{
							$voucher_number	= date('Ymd') . $serial; // Belegnummer YYYYMMDDnum
							$amount			= $list[$i]['journey']; // Menge (Stunden)
							$posting_text	= substr('"Reise ' . $list[$i]['employee'],0,31) . '"'; // Buchungstext (Arbeitszeit vs. Reisezeit)
							$bde			.= $this->generate_cost_accounting_record_diamant($month,$year,$voucher_number,$credit_cost_centre,$charge_cost_centre,$cost_unit,$amount,$posting_text);
							++$serial;
						}
					}
				}
				else if ( ereg('^P',$list[$i]['p_number']) ) // Projekt
				{
					$credit_cost_centre = $list[$i]['cost_centre']; // Kostenstelle (Mitarbeiter)

					if ( intval($credit_cost_centre) != 0 )
					{
						$charge_cost_centre	= '';
						$cost_unit			= substr($list[$i]['p_number'],1); // Kostentr�ger (Projekt)
						$office				= substr($cost_unit,2,2); // Niederlassungsnummer

						if ($office != $location_data['location_ident'])
						{
							$location	= $this->soconfig->get_location_for_ident($office);
							$cost_unit	=  '04' . $location['location_ident'] . '9' . $location['location_custnum']; // Problem mit Jahreszahl bei Gesch�ftsjahres�bergreifenden Projekten!
						}
						if ( ( (int)substr($cost_unit,0,2) ) < 4 ) // Altes Nummernsystem
						{
							$cost_unit = substr($cost_unit,4);
						}

						$cost_unit = (substr($cost_unit,0,1) == '0') ? substr($cost_unit,1) : $cost_unit;

						if ( $list[$i]['minutes'] > 0 )
						{
							$voucher_number	= date('Ymd') . $serial; // Belegnummer YYYYMMDDnum
							$amount			= $list[$i]['minutes']; // Menge (Stunden)
							$posting_text	= substr('"Arbeit ' . $list[$i]['employee'],0,31) . '"'; // Buchungstext (Arbeitszeit vs. Reisezeit)
							$bde			.= $this->generate_cost_accounting_record_diamant($month,$year,$voucher_number,$credit_cost_centre,$charge_cost_centre,$cost_unit,$amount,$posting_text);
							++$serial;
						}
						if ( $list[$i]['journey'] > 0 )
						{
							$voucher_number	= date('Ymd') . $serial; // Belegnummer YYYYMMDDnum
							$amount			= $list[$i]['journey']; // Menge (Stunden)
							$posting_text	= substr('"Reise ' . $list[$i]['employee'],0,31) . '"'; // Buchungstext (Arbeitszeit vs. Reisezeit)
							$bde 			.= $this->generate_cost_accounting_record_diamant($month,$year,$voucher_number,$credit_cost_centre,$charge_cost_centre,$cost_unit,$amount,$posting_text);
							++$serial;
						}
					}
				}
				else if ( ereg('^D',$list[$i]['p_number']) ) // Dienstleistung
				{
					$credit_cost_centre = $list[$i]['cost_centre']; // Kostenstelle (Mitarbeiter)

					if ( intval($credit_cost_centre) != 0 )
					{
						$charge_cost_centre	= '';
						$cost_unit			= substr($list[$i]['p_number'],1); // Kostentr�ger (Kunde)
						$office				= substr($cost_unit,2,2); // Niederlassungsnummer

					    if ( $office != $location_data['location_ident'] )
						{
							$location = $this->soconfig->get_location_for_ident($office);
							$cost_unit =  '04' . $location['location_ident'] . '9' . $location['location_custnum']; // Problem mit Jahreszahl bei Gesch�ftsjahres�bergreifenden Projekten!
						}

						if ( ((int)substr($cost_unit,0,2) ) < 4 ) // Altes Nummernsystem
						{
							$cost_unit = substr($cost_unit,4);
						}

						$cost_unit = (substr($cost_unit,0,1) == '0') ? substr($cost_unit,1) : $cost_unit;

						if ( $list[$i]['minutes'] > 0 )
						{
							$voucher_number	= date('Ymd') . $serial; // Belegnummer YYYYMMDDnum
							$amount			= $list[$i]['minutes']; // Menge (Stunden)
							$posting_text	= substr('"Arbeit ' . $list[$i]['employee'],0,31) . '"'; // Buchungstext (Arbeitszeit vs. Reisezeit)
							$bde			.= $this->generate_cost_accounting_record_diamant($month,$year,$voucher_number,$credit_cost_centre,$charge_cost_centre,$cost_unit,$amount,$posting_text);
							++$serial;
						}
						if ( $list[$i]['journey'] > 0 )
						{
							$voucher_number	= date('Ymd') . $serial; // Belegnummer YYYYMMDDnum
							$amount			= $list[$i]['journey']; // Menge (Stunden)
							$posting_text	= substr('"Reise ' . $list[$i]['employee'],0,31) . '"'; // Buchungstext (Arbeitszeit vs. Reisezeit)
							$bde			.= $this->generate_cost_accounting_record_diamant($month,$year,$voucher_number,$credit_cost_centre,$charge_cost_centre,$cost_unit,$amount,$posting_text);
							++$serial;
						}
					}
				}
				else
				{
					/* Indirekte T�tigkeit */
				}
			}
			return($bde);
		}

		function get_interactions( $env )
		{
			/*
			$env['account_id']
			$env['status']
			$env['project_id']
			$env['pro_main']
			$env['coordinator']
			$env['action']
			*/
			$return = array();
			/*
			$return:
			- book_hours
			- add_project
			- view_employee_activity
			*/

			if( $env['status'] == 'active' && (int) $env['pro_main'] && (int) $env['project_id'] != (int) $env['pro_main'] )
			{
				$return[] = 'book_hours';
			}

			if( ($env['status'] == 'active') && !($env['action'] == 'subs' && !((int) $env['pro_main'])) && $this->add_perms(array('action'  => $env['action'], 'main_co' => $env['coordinator'] )) )
			{
				$return[] = 'add_project';
			}

			if( (int) $env['pro_main'] && (int) $env['project_id'] != (int) $env['pro_main'] && $this->add_perms(array('action'  => $env['action'], 'main_co' => $env['coordinator'])) )
			{
				$return[] = 'view_employee_activity';
			}
			return $return;
		}


		/**
		* Get cost accounting information in Diamant format
		*
		* @param integer $month Month for which to get the cost accounting
		* @param integer $year Year for which to get the cost accounting
		* @param integer $location_id primary key of location for which to get the cost accounting
		* @return string Diamant "Betriebsdatenerfassung (BDE)" import format
		*/
		function get_cost_accounting_diamant_A( $month,$year,$location_id )
		{
			$bde 			= '';
			$serial			= 1;
			$location_data	= $this->soconfig->get_single_location($location_id);
			$list			= $this->soprojects->get_project_hours($month,$year,$location_id);
			$max			= count($list);

			for ($i = 0; $i < $max; ++$i)
			{
				if ( ereg('^A',$list[$i]['p_number']) ) // Projekt
				{
					$credit_cost_centre = $list[$i]['cost_centre']; // Kostenstelle (Mitarbeiter)

					if ( intval($credit_cost_centre) != 0 )
					{
						$charge_cost_centre	= '';
						$cost_unit			= substr($list[$i]['p_number'],1); // Kostentr�ger (Projekt)
						$office				= substr($cost_unit,2,2); // Niederlassungsnummer

						if ( $office != $location_data['location_ident'] )
						{
							$location	= $this->soconfig->get_location_for_ident($office);
							$cost_unit	=  '04' . $location['location_ident'] . '9' . $location['location_custnum']; // Problem mit Jahreszahl bei Gesch�ftsjahres�bergreifenden Projekten!
						}
						if ( ( (int)substr($cost_unit,0,2) ) < 4 ) // Altes Nummernsystem
						{
							$cost_unit = substr($cost_unit,4);
						}

						$cost_unit = (substr($cost_unit,0,1) == '0') ? substr($cost_unit,1) : $cost_unit;

						if ( $list[$i]['minutes'] > 0 )
						{
							$voucher_number	= date('Ymd') . $serial; // Belegnummer YYYYMMDDnum
							$amount			= $list[$i]['minutes']; // Menge (Stunden)
							$posting_text	= substr('"Arbeit ' . $list[$i]['employee'],0,31) . '"'; // Buchungstext (Arbeitszeit vs. Reisezeit)
							$bde			.= $this->generate_cost_accounting_record_diamant($month,$year,$voucher_number,$credit_cost_centre,$charge_cost_centre,$cost_unit,$amount,$posting_text);
							++$serial;
						}
						if ( $list[$i]['journey'] > 0 )
						{
							$voucher_number	= date('Ymd') . $serial; // Belegnummer YYYYMMDDnum
							$amount			= $list[$i]['journey']; // Menge (Stunden)
							$posting_text	= substr('"Reise ' . $list[$i]['employee'],0,31) . '"'; // Buchungstext (Arbeitszeit vs. Reisezeit)
							$bde			.= $this->generate_cost_accounting_record_diamant($month,$year,$voucher_number,$credit_cost_centre,$charge_cost_centre,$cost_unit,$amount,$posting_text);
							++$serial;
						}
					}
				}
				else
				{
					/* alle anderen T�tigkeiten */
				}
			}
			return($bde);
		}

		function test_async_worktime_statusmail()
		{
			$y = isset($_REQUEST['year']) ? $_REQUEST['year'] : date("Y");
			$m = isset($_REQUEST['month']) ? $_REQUEST['month'] : date("m");
			$d = isset($_REQUEST['day']) ? $_REQUEST['day'] : date("d");

			$data = array
			(
				'mail_type'  => 'weekly',
				'last_date'  => ( mktime(0, 0, 0, $m, $d, $y) - (60 * 60 * 24 * 7) )
			);

			$this->async_worktime_statusmail($data);
		}

		function test_async_worktime_warnmail()
		{
			$m = isset($_REQUEST['month']) ? $_REQUEST['month'] : date("m");
			$y = isset($_REQUEST['year']) ? $_REQUEST['year'] : date("Y");

			$data = array
			(
				'warnmail_type'  => 1,
				'warnmail_year'  => $y,
				'warnmail_month' => $m
			);

			$this->async_worktime_warnmail($data);
		}

		function test_async_workhours_booking()
		{
			$m = isset($_REQUEST['month']) ? $_REQUEST['month'] : date("m") - 1;
			$y = isset($_REQUEST['year']) ? $_REQUEST['year'] : date("Y");
			$t = isset($_REQUEST['type']) ? $_REQUEST['type'] : 3;

			$data = array
			(
				'book_type'  => $t,
				'book_year'  => $y,
				'book_month' => $m
			);

			$this->async_workhours_booking($data);
		}

		function get_site_config( $params = 0 )
		{
			return $this->soconfig->get_site_config($params);
		}

		function array_natsort_list( $array )
		{
			// for all arguments without the first starting at end of list
			for ( $i = func_num_args(); $i > 1; $i-- )
			{
				// get column to sort by
				$sort_by = func_get_arg($i-1);

				// clear arrays
				$new_array = array();
				$temporary_array = array();

				// walk through original array
				foreach( $array as $original_key => $original_value )
				{
					// and save only values
					$temporary_array[] = $original_value[$sort_by];
				}

				// sort array on values
				natsort($temporary_array);

				// delete double values
				$temporary_array = array_unique($temporary_array);

				// walk through temporary array
				$x = 0;
				foreach( $temporary_array as $temporary_value )
				{
					// walk through original array
					foreach( $array as $original_key => $original_value )
					{
						// and search for entries having the right value
						if( $temporary_value == $original_value[$sort_by] )
						{
							// save in new array
							$new_array[$x] = $original_value;
							$x++;
						}
					}
				}

				// update original array
				$array = $new_array;
			}

			return $array;
		}
	}
?>
