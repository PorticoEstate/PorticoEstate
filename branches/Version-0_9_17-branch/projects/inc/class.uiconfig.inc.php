<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: class.uiconfig.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	* $Source: /sources/phpgroupware/projects/inc/class.uiconfig.inc.php,v $
	*/

	class uiconfig
	{
		var $action;
		var $start;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		var $public_functions = array
		(
			'edit_activity'					=> true,
			'list_activities'				=> true,
			'list_admins'					=> true,
			'list_roles'					=> true,
			'config_accounting'				=> true,
			'edit_admins'					=> true,
			//'abook'							=> true,
			'preferences'					=> true,
			'delete_pa'						=> true,
			'list_events'					=> true,
			'edit_employee_factor'			=> true,
			'list_surcharges'				=> true,
			'config_worktime_statusmail'  => true,
			'config_workhours_booking'    => true,
			'config_worktime_warnmail'    => true,
			'config_proid_help_msg'       => true,
			'config_employees'            => true,
			'config_locations'            => true
		);

		function uiconfig()
		{
			$this->boconfig		= CreateObject('projects.boconfig');
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');

			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->start		= $this->boconfig->start;
			$this->query		= $this->boconfig->query;
			$this->filter		= $this->boconfig->filter;
			$this->order		= $this->boconfig->order;
			$this->sort			= $this->boconfig->sort;
			$this->cat_id		= $this->boconfig->cat_id;

			$this->siteconfig	= $this->boconfig->boprojects->siteconfig;

			if( !is_object($GLOBALS['phpgw']->js) )
			{
				$GLOBALS['phpgw']->js = createObject('phpgwapi.javascript');
			}

			$GLOBALS['phpgw']->js->validate_file('common','popup');
		}

		function save_sessiondata( $action )
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'filter'	=> $this->filter,
				'order'		=> $this->order,
				'sort'		=> $this->sort,
				'cat_id'	=> $this->cat_id
			);
			$this->boconfig->boprojects->save_sessiondata($data, $action);
		}

		function set_app_langs()
		{
			$GLOBALS['phpgw']->template->set_var('th_bg', $GLOBALS['phpgw_info']['user']['preferences']['common']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('row_on', $GLOBALS['phpgw_info']['user']['preferences']['common']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off', $GLOBALS['phpgw_info']['user']['preferences']['common']['theme']['row_off']);

			$GLOBALS['phpgw']->template->set_var('lang_category',lang('Category'));
			$GLOBALS['phpgw']->template->set_var('lang_select',lang('Select'));
			$GLOBALS['phpgw']->template->set_var('lang_select_category',lang('Select category'));

			$GLOBALS['phpgw']->template->set_var('lang_descr',lang('Description'));
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('lang_none',lang('None'));
			$GLOBALS['phpgw']->template->set_var('lang_number',lang('Project ID'));

			$GLOBALS['phpgw']->template->set_var('lang_start_date',lang('Start Date'));
			$GLOBALS['phpgw']->template->set_var('lang_end_date',lang('End Date'));
			$GLOBALS['phpgw']->template->set_var('lang_cdate',lang('Date created'));
			$GLOBALS['phpgw']->template->set_var('lang_last_update',lang('last update'));
			$GLOBALS['phpgw']->template->set_var('lang_date_due',lang('Date due'));
			$GLOBALS['phpgw']->template->set_var('lang_access',lang('access'));

			$GLOBALS['phpgw']->template->set_var('lang_projects',lang('Projects'));
			$GLOBALS['phpgw']->template->set_var('lang_jobs',lang('Jobs'));
			$GLOBALS['phpgw']->template->set_var('lang_ttracker',lang('time tracker'));
			$GLOBALS['phpgw']->template->set_var('lang_archiv',lang('archive'));
			$GLOBALS['phpgw']->template->set_var('lang_statistics',lang('Statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_roles',lang('roles'));
			$GLOBALS['phpgw']->template->set_var('lang_event',lang('event'));

			$GLOBALS['phpgw']->template->set_var('lang_act_number',lang('Activity ID'));
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('lang_status',lang('Status'));
			$GLOBALS['phpgw']->template->set_var('lang_budget',lang('Budget'));

			$GLOBALS['phpgw']->template->set_var('lang_investment_nr',lang('investment nr'));
			$GLOBALS['phpgw']->template->set_var('lang_customer',lang('Customer'));
			$GLOBALS['phpgw']->template->set_var('lang_coordinator',lang('Coordinator'));
			$GLOBALS['phpgw']->template->set_var('lang_employees',lang('Employees'));
			$GLOBALS['phpgw']->template->set_var('lang_creator',lang('creator'));
			$GLOBALS['phpgw']->template->set_var('lang_processor',lang('processor'));
			$GLOBALS['phpgw']->template->set_var('lang_previous',lang('previous project'));
			$GLOBALS['phpgw']->template->set_var('lang_bookable_activities',lang('Bookable activities'));
			$GLOBALS['phpgw']->template->set_var('lang_billable_activities',lang('Billable activities'));
			$GLOBALS['phpgw']->template->set_var('lang_edit',lang('edit'));
			$GLOBALS['phpgw']->template->set_var('lang_view',lang('View'));
			$GLOBALS['phpgw']->template->set_var('lang_hours',lang('Work hours'));
			$GLOBALS['phpgw']->template->set_var('lang_remarkreq',lang('Remark required'));

			$GLOBALS['phpgw']->template->set_var('lang_customer_nr',lang('customer nr'));
			$GLOBALS['phpgw']->template->set_var('lang_url',lang('project url'));
			$GLOBALS['phpgw']->template->set_var('lang_reference',lang('external reference'));

			$GLOBALS['phpgw']->template->set_var('lang_stats',lang('Statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_ptime',lang('time planned'));
			$GLOBALS['phpgw']->template->set_var('lang_utime',lang('time used'));
			$GLOBALS['phpgw']->template->set_var('lang_month',lang('month'));

			$GLOBALS['phpgw']->template->set_var('lang_done',lang('done'));
			$GLOBALS['phpgw']->template->set_var('lang_save',lang('save'));
			$GLOBALS['phpgw']->template->set_var('lang_apply',lang('apply'));
			$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('cancel'));
			$GLOBALS['phpgw']->template->set_var('lang_search',lang('search'));
			$GLOBALS['phpgw']->template->set_var('lang_delete',lang('delete'));

			$GLOBALS['phpgw']->template->set_var('lang_add_milestone',lang('add milestone'));
			$GLOBALS['phpgw']->template->set_var('lang_milestones',lang('milestones'));

			$GLOBALS['phpgw']->template->set_var('lang_result',lang('result'));
			$GLOBALS['phpgw']->template->set_var('lang_test',lang('test'));
			$GLOBALS['phpgw']->template->set_var('lang_quality',lang('quality check'));

			$GLOBALS['phpgw']->template->set_var('lang_period',lang('period'));
			$GLOBALS['phpgw']->template->set_var('lang_sdate',lang('start date'));
			$GLOBALS['phpgw']->template->set_var('lang_edate',lang('end date'));

			$GLOBALS['phpgw']->template->set_var('lang_per_hour',lang('per hour'));
			$GLOBALS['phpgw']->template->set_var('lang_per_day',lang('per day'));
			$GLOBALS['phpgw']->template->set_var('lang_employee',lang('employee'));

			$GLOBALS['phpgw']->template->set_var('lang_descr',lang('description'));
			$GLOBALS['phpgw']->template->set_var('lang_surcharge',lang('surcharge'));

			$GLOBALS['phpgw']->template->set_var('opt_off_desc',lang('off'));
			$GLOBALS['phpgw']->template->set_var('opt_weekly_desc',lang('weekly'));
			$GLOBALS['phpgw']->template->set_var('opt_monthly_desc',lang('monthly'));

			$GLOBALS['phpgw']->template->set_var('cc_receiver',lang('cc-reciever (separating through commas)'));
		}

		function display_app_header()
		{
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] != 'idots')
			{
				$GLOBALS['phpgw']->template->set_file(array('header' => 'header.tpl'));
				$GLOBALS['phpgw']->template->set_block('header','projects_header');
				$GLOBALS['phpgw']->template->set_block('header','projects_admin_header');

				if ($this->boconfig->boprojects->isprojectadmin('pad') || $this->boconfig->boprojects->isprojectadmin('pmanager'))
				{
					switch($this->siteconfig['accounting'])
					{
						case 'activity':
							$GLOBALS['phpgw']->template->set_var('link_accounting',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_activities','action'=>'act')));
							$GLOBALS['phpgw']->template->set_var('lang_accounting',lang('Activities'));
							break;
						default:
							//$GLOBALS['phpgw']->template->set_var('link_accounting',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.config_accounting','action'=>'accounting')));
							//$GLOBALS['phpgw']->template->set_var('lang_accounting',lang('Accounting'));
					}
					$GLOBALS['phpgw']->template->fp('admin_header','projects_admin_header');
				}

				$GLOBALS['phpgw']->template->set_var('link_budget',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.list_budget','action'=>'mains')));
				$GLOBALS['phpgw']->template->set_var('link_jobs',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.list_projects','action'=>'subs')));
				$GLOBALS['phpgw']->template->set_var('link_hours',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojecthours.list_projects','action'=>'mains')));
				$GLOBALS['phpgw']->template->set_var('link_ttracker',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojecthours.ttracker')));
				$GLOBALS['phpgw']->template->set_var('link_statistics',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uistatistics.list_projects','action'=>'mains')));
				$GLOBALS['phpgw']->template->set_var('link_projects',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.list_projects','action'=>'mains')));

				$GLOBALS['phpgw']->template->fp('app_header','projects_header');
			}
			$this->set_app_langs();
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
		}

		function accounts_popup()
		{
			$GLOBALS['phpgw']->accounts->accounts_popup('projects');
		}

		function e_accounts_popup()
		{
			$GLOBALS['phpgw']->accounts->accounts_popup('e_projects');
		}

		function employee_format($selected = '')
		{
			$emps = $this->boconfig->selected_employees();

			//_debug_array($employees);
			//_debug_array($selected);
			while (is_array($emps) && (list($null,$account) = each($emps)))
			{
				$s .= '<option value="' . $account['account_id'] . '"';
				if($selected == $account['account_id'])
				{
					$s .= ' selected="selected"';
				}
				$s .= '>';
				$s .= $GLOBALS['phpgw']->common->display_fullname($account['account_lid'],$account['account_firstname'],$account['account_lastname'])
						. '</option>' . "\n";
			}
			return $s;
		}

		function config_accounting()
		{
			$id			= $_GET['id'];
			$values		= $_POST['values'];
			$done		= $_POST['done'];
			$sdate		= get_var('sdate',array('GET','POST'));
			$edate		= get_var('edate',array('GET','POST'));

			if($done)
			{
				$GLOBALS['phpgw']->redirect_link('/admin/index.php');
			}

			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.config_accounting',
				'action'		=> 'accounting'
			);

			$jscal = CreateObject('phpgwapi.jscalendar');

			if(!$values['sdate'])
			{
				$values['sdate'] = mktime(0, 0, 0);
			}

			if (!is_numeric($values['cost_centre']))
			{
				$values['cost_centre'] = 0;
			}

			if (!is_numeric($values['weekly_workhours']))
			{
				$values['weekly_workhours'] = 40;
			}

			if ($values['save'])
			{
				//_debug_array($values);
				if(is_array($sdate))
				{
					$start_array		= $jscal->input2date($sdate['str']);
					$values['sdate']	= $start_array['raw'];
				}

				//_debug_array($start_array);
				if(isset($edate['str']) && ($edate['str'] != ''))
				{
					$end_array       = $jscal->input2date($edate['str']);
					$values['edate'] = intval($end_array['raw']) + 86399; // 23:59:59 for enddate
				}
				else
				{
					$values['edate'] = '';
				}

				$error = $this->boconfig->check_pa_values($values,'accounting');
				if(is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->boconfig->save_accounting_factor($values);
					$GLOBALS['phpgw']->template->set_var('message',lang('factor has been saved'));
				}
			}

			if ($_GET['delete'])
			{
				$this->boconfig->delete_pa('accounting',$id);
				$GLOBALS['phpgw']->template->set_var('message',lang('factor has been deleted'));
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('accounting');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('emp_list_t' => 'list_employees.tpl'));
			$GLOBALS['phpgw']->template->set_block('emp_list_t','emp_list','list');
			$GLOBALS['phpgw']->template->set_block('emp_list_t','emp_tframe','flist');

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$emps = $this->boconfig->read_accounting_factors();

//--------------------------------- nextmatch --------------------------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			//$GLOBALS['phpgw']->template->set_var('left',$left);
			//$GLOBALS['phpgw']->template->set_var('right',$right);

    	//$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boconfig->total_records,$this->start));

// ------------------------------ end nextmatch ------------------------------------------

//------------------- list header variable template-declarations -------------------------

			$GLOBALS['phpgw']->template->set_var('sort_name',$this->nextmatchs->show_sort_order($this->sort,'account_id',$this->order,'/index.php',lang('employee'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_sdate',$this->nextmatchs->show_sort_order($this->sort,'sdate',$this->order,'/index.php',lang('start date'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_edate',$this->nextmatchs->show_sort_order($this->sort,'edate',$this->order,'/index.php',lang('end date'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_per_hour',$this->nextmatchs->show_sort_order($this->sort,'accounting',$this->order,'/index.php',lang('per hour'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_per_day',$this->nextmatchs->show_sort_order($this->sort,'d_accounting',$this->order,'/index.php',lang('per day'),$link_data));
			$GLOBALS['phpgw']->template->set_var('weekly_workhours',$this->nextmatchs->show_sort_order($this->sort,'weekly_workhours',$this->order,'/index.php',lang('weekly workhours'),$link_data));
			$GLOBALS['phpgw']->template->set_var('cost_centre',$this->nextmatchs->show_sort_order($this->sort,'cost_centre',$this->order,'/index.php',lang('cost centre'),$link_data));
			$GLOBALS['phpgw']->template->set_var('currency',$GLOBALS['phpgw_info']['user']['preferences']['common']['currency']);

			$GLOBALS['phpgw']->template->set_var('lang_accounting',lang('accounting'));
			$GLOBALS['phpgw']->template->set_var('lang_location', lang('location'));

// -------------------------- end header declaration --------------------------------------
			$emp_exists = array();

			if(is_array($emps))
			{
				for ($i=0;$i<count($emps);$i++)
				{
					$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

					$emp_exists[$emps[$i]['account_id']] = true;
					if(isset($emps[$i]['location']['location_id']))
					{
						$location_name = $emps[$i]['location']['location_name'];
					}
					else
					{
						$location_name = '';
					}

					$GLOBALS['phpgw']->template->set_var(array
					(
						'emp_name'				=> $emps[$i]['account_name'],
						'factor'				=> $emps[$i]['accounting'],
						'd_factor'				=> $emps[$i]['d_accounting'],
						'sdate_formatted'		=> $emps[$i]['sdate_formatted'],
						'edate_formatted'		=> $emps[$i]['edate_formatted'],
						'weekly_workhours_num'	=> $emps[$i]['weekly_workhours'],
						'location_name'        => $location_name,
						'cost_centre_num'		=> $emps[$i]['cost_centre'],
						'delete_emp'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.config_accounting',
																							'id'=> $emps[$i]['id'],
																							'delete'=>'True')),
						'delete_img'			=> $GLOBALS['phpgw']->common->image('phpgwapi','delete'),
						'lang_delete_factor'	=> lang('delete factor'),
						'edit_emp'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.edit_employee_factor',
																							'id'=> $emps[$i]['id'])),
						'edit_img'				=> $GLOBALS['phpgw']->common->image('phpgwapi','edit'),
						'lang_edit_factor'		=> lang('edit factor')
					));
					$GLOBALS['phpgw']->template->fp('list','emp_list',True);
				}
			}
			$GLOBALS['phpgw']->template->set_var('accounting',$values['accounting']);
			$GLOBALS['phpgw']->template->set_var('d_accounting',$values['d_accounting']);
			$GLOBALS['phpgw']->template->set_var('weekly_workhours_num',$values['weekly_workhours']);
			$GLOBALS['phpgw']->template->set_var('cost_centre_num',$values['cost_centre']);
			$GLOBALS['phpgw']->template->set_var('lang_add_factor',lang('add'));
			$GLOBALS['phpgw']->template->set_var('emp_select',$this->employee_format($values['account_id']));

			$location_select = '<option value="0"></option>';
			$locations = $this->boconfig->get_locations();
			foreach($locations as $location)
			{
				if($values['location_id'] == $location['location_id'])
				{
					$selected = ' selected="selected"';
				}
				else
				{
					$selected = '';
				}
				$location_select .= '<option value="'.$location['location_id'].'"'.$selected.'>'.$location['location_name'].'</option>';
			}

			$GLOBALS['phpgw']->template->set_var('location_select', $location_select);

			$GLOBALS['phpgw']->template->set_var('sdate_select',$jscal->input('sdate[str]',$values['sdate']));
			$GLOBALS['phpgw']->template->set_var('edate_select',$jscal->input('edate[str]',$values['edate']));

			$all_emps = $this->boconfig->selected_employees();
			$missing_emps = '';
			while (is_array($all_emps) && (list($null,$account) = each($all_emps)))
			{
				if(isset($emp_exists[$account['account_id']]))
				{
					continue;
				}

				$fullname = $GLOBALS['phpgw']->common->display_fullname($account['account_lid'],$account['account_firstname'],$account['account_lastname']);
				$missing_emps .= '<option value="'.$account['account_id'].'">'.$fullname.'</option>';
			}
			$GLOBALS['phpgw']->template->set_var('lang_employees_not_in_list', lang('employee without entry'));
			$GLOBALS['phpgw']->template->set_var('employees_not_in_list', $missing_emps);

			$this->save_sessiondata('accounting');
			$GLOBALS['phpgw']->template->set_var('flist','');
			$GLOBALS['phpgw']->template->pfp('out','emp_list_t',True);
		}

		function edit_employee_factor()
		{
			$id			= get_var('id',array('GET','POST'));
			$values		= $_POST['values'];
			$cancel		= $_POST['cancel'];
			$sdate		= get_var('sdate',array('GET','POST'));
			$edate		= get_var('edate',array('GET','POST'));
			$location	= get_var('location',array('GET','POST'));

			if($cancel)
			{
				$link_data = array
				(
					'menuaction'	=> 'projects.uiconfig.config_accounting'
				);
				$action_url = $GLOBALS['phpgw']->link('/index.php',$link_data);
				$GLOBALS['phpgw']->redirect($action_url);
			}

			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.edit_employee_factor',
				'id'			=> $id
			);

			$jscal = CreateObject('phpgwapi.jscalendar');
			if ($values['save'])
			{
				if( is_array($sdate) )
				{
					$start_array		= $jscal->input2date($sdate['str']);
					$values['sdate']	= $start_array['raw'];
				}

				//_debug_array($start_array);

				if( isset($edate['str']) && ($edate['str'] != '') )
				{
					$end_array			= $jscal->input2date($edate['str']);
					$values['edate']	= $end_array['raw'] + 86399; // 23:59:59 for enddate;
				}
				else
				{
					$values['edate'] = '';
				}
				$values['id'] = $id;

				//_debug_array($values);

				$error = $this->boconfig->check_pa_values($values,'accounting');
				if( is_array($error) )
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->boconfig->save_accounting_factor($values);
					$link_data['menuaction'] = 'projects.uiconfig.config_accounting';
					$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
				}
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('accounting');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('emp_form' => 'form_emp_factor.tpl'));
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			if( $id )
			{
				$values = $this->boconfig->read_single_afactor($id);
			}

			$GLOBALS['phpgw']->template->set_var('accounting', $values['accounting']);
			$GLOBALS['phpgw']->template->set_var('d_accounting', $values['d_accounting']);
			$GLOBALS['phpgw']->template->set_var('lang_save_factor', lang('save factor'));
			$GLOBALS['phpgw']->template->set_var('weekly_workhours', lang('weekly workhours'));
			$GLOBALS['phpgw']->template->set_var('cost_centre', lang('cost centre'));
			$GLOBALS['phpgw']->template->set_var('weekly_workhours_num', $values['weekly_workhours']);
			$GLOBALS['phpgw']->template->set_var('cost_centre_num', $values['cost_centre']);
			$GLOBALS['phpgw']->template->set_var('lang_location', lang('location'));

			$location_select = '<option value="0"></option>';
			$locations = $this->boconfig->get_locations();

			foreach( $locations as $location )
			{
				if( $values['location_id'] == $location['location_id'] )
				{
					$selected = ' selected="selected"';
				}
				else
				{
					$selected = '';
				}
				$location_select .= '<option value="' . $location['location_id'] . '"' . $selected . '>' . $location['location_name'] . '</option>';
			}

			$GLOBALS['phpgw']->template->set_var('location_select', $location_select);

			$GLOBALS['phpgw']->accounts->get_account_name($values['account_id'], $lid, $fname, $lname);
			$fullname = $GLOBALS['phpgw']->common->display_fullname($lid, $fname, $lname);
			$GLOBALS['phpgw']->template->set_var('employee', $fullname);
			$GLOBALS['phpgw']->template->set_var('account_id', $values['account_id']);
			$GLOBALS['phpgw']->template->set_var('sdate_select',$jscal->input('sdate[str]',$values['sdate']));

			if( $values['edate'] == 0 )
			{
				$values['edate'] = '';
			}

			$GLOBALS['phpgw']->template->set_var('edate_select', $jscal->input('edate[str]', $values['edate']));
			$GLOBALS['phpgw']->template->pfp('out', 'emp_form', true);
		}

		function delete_pa()
		{
			$action	= isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
			$pa_id	= isset($_REQUEST['pa_id']) ? intval($_REQUEST['pa_id']) : '';

			if( $action == 'act' )
			{
				$menu			= 'projects.uiconfig.list_activities';
				$deleteheader	= lang('are you sure you want to delete this activity');
				$header			= lang('delete activity');
			}

			/*switch( $action )
			{
				case 'act':
					$menu = 'projects.uiconfig.list_activities';
					$deleteheader = lang('are you sure you want to delete this activity');
					$header = lang('delete activity');
					break;
			}*/

			$link_data = array
			(
				'menuaction'	=> $menu,
				'pa_id'			=> $pa_id,
				'action'		=> $action
			);

			//if($_POST['yes'])
			if( isset($_REQUEST['yes']) && $_REQUEST['yes'] )
			{
				$del = $pa_id;

				if ($subs)
				{
					$this->boconfig->delete_pa($action, $del, True);
				}
				else
				{
					$this->boconfig->delete_pa($action, $del, False);
				}
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ($_POST['no'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . $header;

			$this->display_app_header();
			$GLOBALS['phpgw']->template->set_file(array('pa_delete' => 'delete.tpl'));

			$GLOBALS['phpgw']->template->set_var('lang_subs','');
			$GLOBALS['phpgw']->template->set_var('subs', '');

			$GLOBALS['phpgw']->template->set_var('deleteheader',$deleteheader);
			$GLOBALS['phpgw']->template->set_var('lang_no',lang('No'));
			$GLOBALS['phpgw']->template->set_var('lang_yes',lang('Yes'));

			$link_data['menuaction'] = 'projects.uiconfig.delete_pa';
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->pfp('out','pa_delete');
		}

		function list_activities()
		{
			$action = 'act';

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('list activities');
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('activities_list_t' => 'listactivities.tpl'));
			$GLOBALS['phpgw']->template->set_block('activities_list_t','activities_list','list');

			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.list_activities',
				'action'		=> 'act'
			);

			$act = $this->boconfig->list_activities();

// --------------------- nextmatch variable template-declarations ------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boconfig->total_records,$this->start));

// ------------------------- end nextmatch template --------------------------------------

            $GLOBALS['phpgw']->template->set_var('cat_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('categories_list',$this->boconfig->boprojects->cats->formatted_list('select','all',$this->cat_id,'True'));
            $GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
            $GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));

			switch($prefs['bill'])
			{
				case 'wu':	$bill = lang('Bill per workunit'); break;
				case 'h':	$bill = lang('Bill per hour'); break;
				default :	$bill = lang('Bill per hour'); break;
			}

// ----------------- list header variable template-declarations ---------------------------

			$GLOBALS['phpgw']->template->set_var('currency',$GLOBALS['phpgw_info']['user']['preferences']['common']['currency']);
			$GLOBALS['phpgw']->template->set_var('sort_num',$this->nextmatchs->show_sort_order($this->sort,'num',$this->order,'/index.php',lang('Activity ID')));
			$GLOBALS['phpgw']->template->set_var('sort_descr',$this->nextmatchs->show_sort_order($this->sort,'descr',$this->order,'/index.php',lang('Description')));
			$GLOBALS['phpgw']->template->set_var('sort_billperae',$this->nextmatchs->show_sort_order($this->sort,'billperae',$this->order,'/index.php',$bill));

			if ($prefs['bill'] == 'wu')
			{
				$GLOBALS['phpgw']->template->set_var('sort_minperae','<td width="10%" align="right">' . $this->nextmatchs->show_sort_order($this->sort,'minperae',
									$this->order,'/index.php',lang('Minutes per workunit') . '</td>'));
			}

// ---------------------------- end header declaration -------------------------------------

            for ($i=0;$i<count($act);$i++)
            {
				$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);
				$descr = $GLOBALS['phpgw']->strip_html($act[$i]['descr']);
				if (! $descr)
				{
					$descr  = '&nbsp;';
				}

// ------------------- template declaration for list records -------------------------

				$GLOBALS['phpgw']->template->set_var(array('num'	=> $GLOBALS['phpgw']->strip_html($act[$i]['number']),
										'descr' => $descr,
									'billperae' => $act[$i]['billperae']));

				if ($prefs['bill'] == 'wu')
				{
					$GLOBALS['phpgw']->template->set_var('minperae','<td align="right">' . $act[$i]['minperae'] . '</td>');
				}

				$link_data['menuaction']	= 'projects.uiconfig.edit_activity';
				$link_data['activity_id']	= $act[$i]['activity_id'];
				$GLOBALS['phpgw']->template->set_var('edit',$GLOBALS['phpgw']->link('/index.php',$link_data));

				$link_data['menuaction']	= 'projects.uiconfig.delete_pa';
				$link_data['pa_id']	= $act[$i]['activity_id'];
				$GLOBALS['phpgw']->template->set_var('delete',$GLOBALS['phpgw']->link('/index.php',$link_data));

				$GLOBALS['phpgw']->template->fp('list','activities_list',True);

// ------------------------------- end record declaration --------------------------------

			}

// ------------------------- template declaration for Add Form ---------------------------

			$link_data['menuaction'] = 'projects.uiconfig.edit_activity';
			unset($link_data['activity_id']);
			$GLOBALS['phpgw']->template->set_var('add_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->set_var('lang_add',lang('Add'));
			$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));
			$this->save_sessiondata('act');
			$GLOBALS['phpgw']->template->pfp('out','activities_list_t',True);

// -------------------------------- end Add form declaration ------------------------------

		}

		function edit_activity()
		{
			$activity_id	= get_var('activity_id',array('POST','GET'));
			$values			= get_var('values',array('POST'));

			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.list_activities',
				'action'		=> 'act'
			);

			if ($_POST['save'])
			{
				$this->cat_id			= ($values['cat']?$values['cat']:'');
				$values['activity_id']	= $activity_id;

				$error = $this->boconfig->check_pa_values($values);
				if (is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->boconfig->save_activity($values);
					$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
				}
			}

			if($_POST['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($activity_id?lang('edit activity'):lang('add activity'));

			$this->display_app_header();

			$form = ($activity_id?'edit':'add');

			$GLOBALS['phpgw']->template->set_file(array('edit_activity' => 'formactivity.tpl'));

			$GLOBALS['phpgw']->template->set_var('done_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.edit_activity','activity_id'=> $activity_id)));

			$GLOBALS['phpgw']->template->set_var('currency',$GLOBALS['phpgw_info']['user']['preferences']['common']['currency']);

			if ($activity_id)
			{
				$values = $this->boconfig->read_single_activity($activity_id);
				$this->cat_id = $values['cat'];
				$GLOBALS['phpgw']->template->set_var('lang_choose','');
				$GLOBALS['phpgw']->template->set_var('choose','');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('lang_choose',lang('Generate Activity ID ?'));
				$GLOBALS['phpgw']->template->set_var('choose','<input type="checkbox" name="values[choose]" value="True">');
			}

			$GLOBALS['phpgw']->template->set_var('cats_list',$this->boconfig->boprojects->cats->formatted_list('select','all',$this->cat_id,True));
			$GLOBALS['phpgw']->template->set_var('num',$GLOBALS['phpgw']->strip_html($values['number']));
			$descr  = $GLOBALS['phpgw']->strip_html($values['descr']);
			if (! $descr) $descr = '&nbsp;';
			$GLOBALS['phpgw']->template->set_var('descr',$descr);

			if ($values['remarkreq'] == 'N'):
				$stat_sel[0]=' selected';
			elseif ($values['remarkreq'] == 'Y'):
				$stat_sel[1]=' selected';
			endif;

			$remarkreq_list = '<option value="N"' . $stat_sel[0] . '>' . lang('No') . '</option>' . "\n"
					. '<option value="Y"' . $stat_sel[1] . '>' . lang('Yes') . '</option>' . "\n";

			$GLOBALS['phpgw']->template->set_var('remarkreq_list',$remarkreq_list);

			if ($prefs['bill'] == 'wu')
			{
    			$GLOBALS['phpgw']->template->set_var('lang_billperae',lang('Bill per workunit'));
				$GLOBALS['phpgw']->template->set_var('lang_minperae',lang('Minutes per workunit'));
				$GLOBALS['phpgw']->template->set_var('minperae','<input type="text" name="values[minperae]" value="' . $values['minperae'] . '">');
			}
			else
			{
    			$GLOBALS['phpgw']->template->set_var('lang_billperae',lang('Bill per hour'));
			}

			$GLOBALS['phpgw']->template->set_var('billperae',$values['billperae']);

			$link_data['menuaction']	= 'projects.uiconfig.delete_pa';
			$link_data['pa_id']	= $values[$i]['activity_id'];
			$GLOBALS['phpgw']->template->set_var('deleteurl',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));

			$this->save_sessiondata('act');
			$GLOBALS['phpgw']->template->pfp('out','edit_activity');
		}

		function list_admins()
		{
			$action = get_var('action',array('POST','GET'));

			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.edit_admins',
				'action'		=> $action
			);

			if ($_POST['add'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ($_POST['done'])
			{
				$GLOBALS['phpgw']->redirect_link('/admin/index.php');
			}

			switch($action)
			{
				case 'psale':		$header_info = lang('salesmen list'); break;
				case 'pmanager':	$header_info = lang('manager list'); break;
				default:			$header_info = lang('administrator list'); break;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . $header_info;
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('admin_list_t' => 'list_admin.tpl'));
			$GLOBALS['phpgw']->template->set_block('admin_list_t','admin_list','list');
			$GLOBALS['phpgw']->template->set_block('admin_list_t','group_list','glist');

			$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
			$link_data['menuaction'] = 'projects.uiconfig.list_admins';
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$admins = $this->boconfig->list_admins($action);

			//_debug_array($admins);

//--------------------------------- nextmatch --------------------------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

    		$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boconfig->total_records,$this->start));

// ------------------------------ end nextmatch ------------------------------------------

//------------------- list header variable template-declarations -------------------------

			$GLOBALS['phpgw']->template->set_var('sort_lid',lang('Username'));
			$GLOBALS['phpgw']->template->set_var('sort_lastname',lang('Lastname'));
			$GLOBALS['phpgw']->template->set_var('sort_firstname',lang('Firstname'));
			$GLOBALS['phpgw']->template->set_var('lang_group',lang('group'));
// -------------------------- end header declaration --------------------------------------

			for ($i=0;$i<count($admins);$i++)
			{
				$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);
				$lid = $admins[$i]['lid'];

				if ($admins[$i]['type']=='u')
				{
					$GLOBALS['phpgw']->template->set_var(array
					(
						'lid'		=> $admins[$i]['lid'],
						'firstname'	=> $admins[$i]['firstname'],
						'lastname'	=> $admins[$i]['lastname']
					));
					$GLOBALS['phpgw']->template->fp('list','admin_list',True);
				}
				if ($admins[$i]['type']=='g')
				{
					$GLOBALS['phpgw']->template->set_var('lid',$admins[$i]['lid']);
					$GLOBALS['phpgw']->template->fp('glist','group_list',True);
				}
			}

			$GLOBALS['phpgw']->template->pfp('out','admin_list_t',True);
			$this->save_sessiondata($action);
		}

		function edit_admins()
		{
			$users	= get_var('users',array('POST'));
			$groups = get_var('groups',array('POST'));
			$action = get_var('action',array('POST','GET'));

			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.list_admins',
				'action'		=> $action
			);

			if ($_POST['save'])
			{
				$this->boconfig->edit_admins($action,$users,$groups);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ($_POST['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			switch($action)
			{
				case 'psale':		$header_info = lang('edit salesmen list'); break;
				case 'pmanager':	$header_info = lang('edit manager list'); break;
				default:			lang('edit administrator list'); break;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . $header_info;
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);

			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('admin_add' => 'form_admin.tpl'));

			$link_data['menuaction'] = 'projects.uiconfig.edit_admins';
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->set_var('users_list',$this->boconfig->selected_admins($action));
			$GLOBALS['phpgw']->template->set_var('groups_list',$this->boconfig->selected_admins($action,'group'));
			$GLOBALS['phpgw']->template->set_var('lang_users_list',lang('Select users'));
			$GLOBALS['phpgw']->template->set_var('lang_groups_list',lang('Select groups'));

			$GLOBALS['phpgw']->template->pfp('out','admin_add');
		}

		function list_roles()
		{
			$role_id	= get_var('role_id',array('POST','GET'));
			$role_name	= $_POST['role_name'];

			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.list_roles',
				'role_id'		=> $role_id,
				'action'		=> 'role'
			);

			if ($_POST['save'])
			{
				$error = $this->boconfig->check_pa_values(array('role_name' => $role_name),'role');
				if(is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->boconfig->save_role($role_name);
					$GLOBALS['phpgw']->template->set_var('message',($role_id?lang('role %1 has been updated',$role_name):lang('role %1 has been saved',$role_name)));
				}
			}

			if ($_GET['delete'])
			{
				$this->boconfig->delete_pa('role',$role_id);
				$GLOBALS['phpgw']->template->set_var('message',lang('role has been deleted'));
			}

			if ($_POST['done'])
			{
				$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('roles list');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('roles_list_t' => 'list_roles.tpl'));
			$GLOBALS['phpgw']->template->set_block('roles_list_t','roles_list','list');

			$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$roles = $this->boconfig->list_roles();

//--------------------------------- nextmatch --------------------------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boconfig->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

    		$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boconfig->total_records,$this->start));

// ------------------------------ end nextmatch ------------------------------------------

//------------------- list header variable template-declarations -------------------------

			$GLOBALS['phpgw']->template->set_var('sort_name',$this->nextmatchs->show_sort_order($this->sort,'role_name',$this->order,'/index.php',lang('name'),$link_data));

// -------------------------- end header declaration --------------------------------------

			for ($i=0;$i<count($roles);$i++)
			{
				$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

				$GLOBALS['phpgw']->template->set_var('role_name',$roles[$i]['role_name']);
				$GLOBALS['phpgw']->template->set_var('delete_role',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_roles',
																								'role_id'=> $roles[$i]['role_id'],
																								'delete'=>'True')));

				$GLOBALS['phpgw']->template->fp('list','roles_list',True);
			}
			$GLOBALS['phpgw']->template->set_var('lang_add_role',lang('add role'));
			$this->save_sessiondata('role');
			$GLOBALS['phpgw']->template->pfp('out','roles_list_t',True);
		}

		function list_events()
		{
			//$event_id	= get_var('event_id',array('POST','GET'));
			$values		= $_POST['values'];

			$link_data = array
			(
				'menuaction'	=> 'projects.uiconfig.list_events'
			);

			if ($_POST['save'])
			{
				$this->boconfig->save_event($values);
				$GLOBALS['phpgw']->template->set_var('message',lang('event extra has been saved'));
			}

			if ($_POST['done'])
			{
				$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('edit events');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('event_list_t' => 'list_events.tpl'));
			$GLOBALS['phpgw']->template->set_block('event_list_t','event_list','list');

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$events = $this->boconfig->boprojects->list_events();

			for ($i=0;$i<count($events);$i++)
			{
				$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

				if($events[$i]['event_type'] == 'limits')
				{
					$extra = $events[$i]['event_extra'] . '&nbsp;' . lang('days before');
					$values['limit'] = $values['limit']?$values['limit']:$events[$i]['event_extra'];
				}
				if($events[$i]['event_type'] == 'percent')
				{
					$extra = $events[$i]['event_extra']==0?100:$events[$i]['event_extra'] . '&nbsp;' . lang('% from');
					$values['percent'] = $values['percent']?$values['percent']:$events[$i]['event_extra'];
				}
				$GLOBALS['phpgw']->template->set_var('event_name',lang($events[$i]['event_name']));
				$GLOBALS['phpgw']->template->set_var('event_extra',$extra);
				$GLOBALS['phpgw']->template->fp('list','event_list',True);
			}

			$GLOBALS['phpgw']->template->set_var('event_select_limit',$this->boconfig->boprojects->action_format($selected = $values['event_id_limit'],$action = 'event',$type = 'limits'));
			$GLOBALS['phpgw']->template->set_var('event_select_percent',$this->boconfig->boprojects->action_format($selected = $values['event_id_percent'],$action = 'event',$type = 'percent'));

			$GLOBALS['phpgw']->template->set_var('lang_days',lang('days'));
			$GLOBALS['phpgw']->template->set_var('lang_before',lang('before'));
			$GLOBALS['phpgw']->template->set_var('lang_alarm',lang('alarm'));

			$GLOBALS['phpgw']->template->set_var('limit',$values['limit']);
			$GLOBALS['phpgw']->template->set_var('percent',$values['percent']);

			$GLOBALS['phpgw']->template->pfp('out','event_list_t',True);
		}

// --------- SURCHARGES ----------------------

		function list_surcharges()
		{
			$charge_id	= $_GET['charge_id'];
			$values		= $_POST['values'];

			//_debug_array($values);

			if ($_POST['save'])
			{
				if(strlen(trim($values['charge_name'])) == 0)
				{
					$GLOBALS['phpgw']->template->set_var('message', lang('please enter a description'));
				}
				else
				{
					if($_POST['new_charge'])
					{
						$values['charge_id'] = 0;
					}
					$this->boconfig->save_surcharge($values);
					$GLOBALS['phpgw']->template->set_var('message',lang('surcharge %1 has been saved',$values['charge_name']));
				}
			}

			if ($_POST['done'])
			{
				$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
			}

			if($_GET['edit'])
			{
				list($values) = $this->boconfig->boprojects->list_surcharges($charge_id);
			}

			if($_GET['delete'])
			{
				$this->boconfig->delete_pa('charge',$charge_id);
				$GLOBALS['phpgw']->template->set_var('message',lang('surcharge has been deleted'));
			}

			$link_data = array
			(
				'menuaction' => 'projects.uiconfig.list_surcharges'
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('edit surcharges');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('charge_list_t' => 'list_surcharges.tpl'));
			$GLOBALS['phpgw']->template->set_block('charge_list_t','charge_list','list');

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$charges = $this->boconfig->boprojects->list_surcharges();

			if(is_array($charges))
			{
				foreach($charges as $charge)
				{
					$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

					$GLOBALS['phpgw']->template->set_var(array
					(
						'charge_name'			=> $charge['charge_name'],
						'charge_percent'		=> $charge['charge_percent'],
						'delete_url'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_surcharges',
																							'charge_id'=>$charge['charge_id'],
																							'delete'=>'True')),
						'edit_url'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_surcharges',
																							'charge_id'=> $charge['charge_id'],
																							'edit'=>'True')),
						'edit_img'			=> $GLOBALS['phpgw']->common->image('phpgwapi','edit'),
						'lang_edit_surcharge'	=> lang('edit surcharge'),
						'delete_img'			=> $GLOBALS['phpgw']->common->image('phpgwapi','delete'),
						'lang_delete_surcharge'	=> lang('delete surcharge')
					));
					$GLOBALS['phpgw']->template->fp('list','charge_list',True);
				}
			}
			$GLOBALS['phpgw']->template->set_var('new_charge_selected',$_POST['new_charge']?' SELECTED':'');
			$GLOBALS['phpgw']->template->set_var('charge_name',$values['charge_name']);
			$GLOBALS['phpgw']->template->set_var('charge_percent',$values['charge_percent']);
			$GLOBALS['phpgw']->template->set_var('charge_id',$charge_id);
			$GLOBALS['phpgw']->template->set_var('lang_save_surcharge',lang('save surcharge'));
			$GLOBALS['phpgw']->template->set_var('lang_new_surcharge',lang('new surcharge'));
			$GLOBALS['phpgw']->template->pfp('out','charge_list_t',True);
		}

		function config_proid_help_msg()
		{
			if($_POST['save'])
			{
				$this->boconfig->config_proid_help_msg(array('action' => 'save','proid_help_msg' => $_POST['proid_help_msg']));
			}

			if($_POST['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/admin/index.php');
			}

			$link_data = array
			(
				'menuaction' => 'projects.uiconfig.config_proid_help_msg'
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('edit project id help msg');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('proidhelp' => 'config_proid_help.tpl'));
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('helpmsg',stripslashes($this->boconfig->config_proid_help_msg(array('action' => 'get'))));
			$GLOBALS['phpgw']->template->set_var('help_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects_base.proid_help_popup')));
			$GLOBALS['phpgw']->template->set_var('lang_show',lang('show help msg'));
			$GLOBALS['phpgw']->template->pfp('out','proidhelp',True);
		}

		/*function abook()
		{
			$start		= get_var('start',array('POST'));
			$cat_id 	= get_var('cat_id',array('POST'));
			$sort		= get_var('sort',array('POST'));
			$order		= get_var('order',array('POST'));
			$filter		= get_var('filter',array('POST'));
			$qfilter	= get_var('qfilter',array('POST'));
			$query		= get_var('query',array('POST'));

			$GLOBALS['phpgw']->template->set_file(array('abook_list_t' => 'addressbook.tpl'));
			$GLOBALS['phpgw']->template->set_block('abook_list_t','abook_list','list');

			$this->boprojects->cats->app_name = 'addressbook';

			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_var('title',$GLOBALS['phpgw_info']['site_title']);
			$GLOBALS['phpgw']->template->set_var('lang_action',lang('Address book'));
			$GLOBALS['phpgw']->template->set_var('charset',$GLOBALS['phpgw']->translation->translate('charset'));
			$GLOBALS['phpgw']->template->set_var('font',$GLOBALS['phpgw_info']['theme']['font']);

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.abook',
				'start'			=> $start,
				'sort'			=> $sort,
				'order'			=> $order,
				'cat_id'		=> $cat_id,
				'filter'		=> $filter,
				'query'			=> $query
			);

			if (! $start) { $start = 0; }

			if (!$filter) { $filter = 'none'; }

			$qfilter = 'tid=n';

			switch ($filter)
			{
				case 'none': break;
				case 'private': $qfilter .= ',access=private'; break;
				case 'yours': $qfilter .= ',owner=' . $this->account; break;
			}

			if ($cat_id)
			{
				$qfilter .= ',cat_id=' . $cat_id;
			}

			$entries = $this->boprojects->read_abook($start, $query, $qfilter, $sort, $order);

// --------------------------------- nextmatch ---------------------------

			$left = $this->nextmatchs->left('/index.php',$start,$this->boprojects->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$start,$this->boprojects->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boprojects->total_records,$start));

// -------------------------- end nextmatch ------------------------------------

			$GLOBALS['phpgw']->template->set_var('cats_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('cats_list',$this->boprojects->cats->formatted_list('select','all',$cat_id,True));
			$GLOBALS['phpgw']->template->set_var('filter_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('filter_list',$this->nextmatchs->new_filter($filter));
			$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $query)));

// ---------------- list header variable template-declarations --------------------------

// -------------- list header variable template-declaration ------------------------

			$GLOBALS['phpgw']->template->set_var('sort_company',$this->nextmatchs->show_sort_order($sort,'org_name',$order,'/index.php',lang('Company'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_firstname',$this->nextmatchs->show_sort_order($sort,'per_first_name',$order,'/index.php',lang('Firstname'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_lastname',$this->nextmatchs->show_sort_order($sort,'per_last_name',$order,'/index.php',lang('Lastname'),$link_data));
			$GLOBALS['phpgw']->template->set_var('lang_select',lang('Select'));

// ------------------------- end header declaration --------------------------------

			for ($i=0;$i<count($entries);$i++)
			{
				$GLOBALS['phpgw']->template->set_var('tr_color',$this->nextmatchs->alternate_row_color($tr_color));
				$firstname = $entries[$i]['per_first_name'];
				if (!$firstname) { $firstname = '&nbsp;'; }
				$lastname = $entries[$i]['per_last_name'];
				if (!$lastname) { $lastname = '&nbsp;'; }
				$company = $entries[$i]['org_name'];
				if (!$company) { $company = '&nbsp;'; }

// ---------------- template declaration for list records --------------------------

				$GLOBALS['phpgw']->template->set_var(array('company' 	=> $company,
									'firstname' 	=> $firstname,
									'lastname'		=> $lastname,
									'abid'			=> $entries[$i]['contact_id']));

				$GLOBALS['phpgw']->template->parse('list','abook_list',True);
			}

			$GLOBALS['phpgw']->template->parse('out','abook_list_t',True);
			$GLOBALS['phpgw']->template->p('out');

			$GLOBALS['phpgw']->common->phpgw_exit();
		}*/

		function preferences()
		{
			//_debug_array($_POST['prefs']);
			//_debug_array($GLOBALS['phpgw_info']['user']['preferences']);
			//if ($_POST['save'])
			if( isset($_REQUEST['save']) && $_REQUEST['save'] )
			{
				$this->boconfig->save_prefs($_POST['prefs']);
				$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
			}

			//if ($_POST['done'])
			if( isset($_REQUEST['done']) && $_REQUEST['done'] )
			{
				$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
			}

			$link_data = array
			(
				'menuaction' => 'projects.uiconfig.preferences'
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('preferences');

			$GLOBALS['phpgw']->common->phpgw_header();
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			echo parse_navbar();

			$GLOBALS['phpgw']->template->set_file(array('prefs' => 'preferences.tpl'));
			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$prefs = $this->boconfig->boprojects->read_prefs();

			$columns = array(
			                 array('id'   => 'priority',
			                       'name' => lang('priority')
			                      ),
			                 array('id'   => 'number',
			                       'name' => lang('project id')
			                      ),
			                 array('id'   => 'investment_nr',
			                       'name' => lang('investment nr')
			                      ),
			                 array('id'   => 'coordinatorout',
			                       'name' => lang('coordinator')
			                      ),
			                 array('id'   => 'salesmanagerout',
			                       'name' => lang('sales manager')
			                      ),
			                 array('id'   => 'customerout',
			                       'name' => lang('customer')
			                      ),
			                 array('id'   => 'customer_nr',
			                       'name' => lang('customer nr')
			                      ),
			                 array('id'   => 'sdateout',
			                       'name' => lang('start date')
			                      ),
			                 array('id'   => 'edateout',
			                       'name' => lang('date due')
			                      ),
			                 array('id'   => 'psdateout',
			                       'name' => lang('start date planned')
			                      ),
			                 array('id'   => 'pedateout',
			                       'name' => lang('date due planned')
			                      ),
			                 array('id'   => 'previousout',
			                       'name' => lang('previous')
			                      ),
			                 array('id'   => 'phours',
			                       'name' => lang('time planned')
			                      ),
			                 array('id'   => 'budget',
			                       'name' => lang('budget')
			                      ),
			                 array('id'   => 'e_budget',
			                       'name' => lang('extra budget')
			                      ),
			                 array('id'   => 'url',
			                       'name' => lang('url')
			                      ),
			                 array('id'   => 'reference',
			                       'name' => lang('reference')
			                      ),
			                 array('id'   => 'accountingout',
			                       'name' => lang('accounting')
			                      ),
			                 array('id'   => 'project_accounting_factor',
			                       'name' => lang('accounting factor').' '.lang('per hour')
			                      ),
			                 array('id'   => 'project_accounting_factor_d',
			                       'name' => lang('accounting factor').' '.lang('per day')
			                      ),
			                 array('id'   => 'billableout',
			                       'name' => lang('billable')
			                      ),
			                 array('id'   => 'discountout',
			                       'name' => lang('discount')
			                      ),
			                 array('id'   => 'mstones',
			                       'name' => lang('milestones')
			                      )
			                );

			$cscolumns = array(
			                 array('id'   => 'title',
			                       'name' => lang('title')
			                      ),
			                 array('id'   => 'number',
			                       'name' => lang('project id')
			                      ),
			                 array('id'   => 'edateout',
			                       'name' => lang('date due')
			                      )
			                );

			$sel = '';
			for( $i = 0; $i < count($columns); $i++ )
			{
				$selected = '';
				if(is_array($prefs['columns']) && in_array($columns[$i]['id'], $prefs['columns']))
				{
					$selected = 'selected="selected"';
				}
				$sel .= '<option value="' . $columns[$i]['id'] . '" ' . $selected . '>' . $columns[$i]['name'] . '</option>' . "\n";
			}

			$cssel = '';
			for( $i = 0; $i < count($cscolumns); $i++ )
			{
				$selected = '';
				if(is_array($prefs['cscolumns']) && in_array($cscolumns[$i]['id'], $prefs['cscolumns']))
				{
					$selected = 'selected="selected"';
				}
				$cssel .= '<option value="'.$cscolumns[$i]['id'].'" '.$selected.'>'.$cscolumns[$i]['name'].'</option>'."\n";
			}

			$GLOBALS['phpgw']->template->set_var('lang_select_columns',lang('columns to show in the projects list'));
			$GLOBALS['phpgw']->template->set_var('column_select',$sel);
			$GLOBALS['phpgw']->template->set_var('lang_select_cs_columns',lang('columns to show in the controlling sheet'));
			$GLOBALS['phpgw']->template->set_var('column_cs_select',$cssel);
			$GLOBALS['phpgw']->template->set_var('lang_show_projects_on_mainscreen',lang('show projects on mainscreen'));
			$GLOBALS['phpgw']->template->set_var('mainscreen_checked',($prefs['mainscreen_showevents']==True?' checked="checked"':''));
			$GLOBALS['phpgw']->template->set_var('worktime_statusmail_desc',lang('worktime statusmail'));
			$GLOBALS['phpgw']->template->set_var('send_status_mail_checked', ($prefs['send_status_mail']==True?' checked="checked"':''));

			$GLOBALS['phpgw']->template->pfp('out','prefs');
		}

		function config_worktime_statusmail()
		{
			$mail_type = get_var('mail_type',array('POST','GET'));
			$message = '&nbsp;';

			$this->set_app_langs();

			if (isset($_POST['save']))
			{
				$values = array('action' => 'save', 'mail_type' => $mail_type);
				if($this->boconfig->config_worktime_statusmail($values) == True)
					$message = lang('setting has been saved');
				else
					$message = lang('setting has not been saved');
			}

			if(isset($_POST['done']))
			{
				$GLOBALS['phpgw']->redirect_link('/admin/index.php');
			}

			$header_info = lang('config worktime statusmail');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . $header_info;
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);

			$GLOBALS['phpgw']->template->set_file(array('config' => 'config_worktime_statusmail.tpl'));

			$link_data['menuaction'] = 'projects.uiconfig.config_worktime_statusmail';
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('worktime_statusmail_desc',lang('worktime statusmail'));
			$GLOBALS['phpgw']->template->set_var('opt_off_desc',lang('off'));
			$GLOBALS['phpgw']->template->set_var('opt_weekly_desc',lang('weekly'));
			$GLOBALS['phpgw']->template->set_var('opt_monthly_desc',lang('monthly'));
			$GLOBALS['phpgw']->template->set_var('message',$message);

			$mail_type_off = '';
			$mail_type_weekly = '';
			$mail_type_monthly = '';

			$values = array('action' => 'get');
			$mail_type_selected = $this->boconfig->config_worktime_statusmail($values);

			switch($mail_type_selected)
			{
				case 'off':     $mail_type_off     = ' selected'; break;
				case 'weekly':  $mail_type_weekly  = ' selected'; break;
				case 'monthly': $mail_type_monthly = ' selected'; break;
				default:        $mail_type_off     = ' selected'; break;
			}

			$GLOBALS['phpgw']->template->set_var('selected_off',     $mail_type_off);
			$GLOBALS['phpgw']->template->set_var('selected_weekly',  $mail_type_weekly);
			$GLOBALS['phpgw']->template->set_var('selected_monthly', $mail_type_monthly);

			$GLOBALS['phpgw']->template->pfp('out','config');
		}

		function config_workhours_booking()
		{
			$book_type = get_var('book_type',array('POST','GET'));
			$message = '&nbsp;';

			$this->set_app_langs();

			if (isset($_POST['save']))
			{
				$values = array('action' => 'save', 'book_type' => $book_type);
				if($this->boconfig->config_workhours_booking($values) == True)
					$message = lang('setting has been saved');
				else
					$message = lang('setting has not been saved');
			}

			if(isset($_POST['done']))
			{
				$GLOBALS['phpgw']->redirect_link('/admin/index.php');
			}

			$header_info = lang('config workhours booking');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . $header_info;
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);

			$GLOBALS['phpgw']->template->set_file(array('config' => 'config_workhours_booking.tpl'));

			$link_data['menuaction'] = 'projects.uiconfig.config_workhours_booking';
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('workhours_booking_desc',lang('booking workhours on the specified work day of a month'));
			$GLOBALS['phpgw']->template->set_var('opt_workday_0_desc',lang('off'));
			$GLOBALS['phpgw']->template->set_var('opt_workday_1_desc', 1);
			$GLOBALS['phpgw']->template->set_var('opt_workday_2_desc', 2);
			$GLOBALS['phpgw']->template->set_var('opt_workday_3_desc', 3);
			$GLOBALS['phpgw']->template->set_var('opt_workday_4_desc', 4);
			$GLOBALS['phpgw']->template->set_var('opt_workday_5_desc', 5);
			$GLOBALS['phpgw']->template->set_var('message',$message);

			$book_type_0 = '';
			$book_type_1 = '';
			$book_type_2 = '';
			$book_type_3 = '';
			$book_type_4 = '';
			$book_type_5 = '';

			$values = array('action' => 'get');
			$book_type_selected = $this->boconfig->config_workhours_booking($values);

			switch($book_type_selected)
			{
				case 0:  $book_type_0 = ' selected'; break;
				case 1:  $book_type_1 = ' selected'; break;
				case 2:  $book_type_2 = ' selected'; break;
				case 3:  $book_type_3 = ' selected'; break;
				case 4:  $book_type_4 = ' selected'; break;
				case 5:  $book_type_5 = ' selected'; break;
				default: $book_type_0 = ' selected'; break;
			}

			$GLOBALS['phpgw']->template->set_var('selected_0', $book_type_0);
			$GLOBALS['phpgw']->template->set_var('selected_1', $book_type_1);
			$GLOBALS['phpgw']->template->set_var('selected_2', $book_type_2);
			$GLOBALS['phpgw']->template->set_var('selected_3', $book_type_3);
			$GLOBALS['phpgw']->template->set_var('selected_4', $book_type_4);
			$GLOBALS['phpgw']->template->set_var('selected_5', $book_type_5);
			$GLOBALS['phpgw']->template->pfp('out','config');
		}

		function config_worktime_warnmail()
		{
			$warnmail_type = get_var('warnmail_type',array('POST','GET'));
			$warnmail_email_address = get_var('email_warnmail_address', array('POST','GET'));

			$message = '&nbsp;';

			$this->set_app_langs();

			if (isset($_POST['save']))
			{
				$values = array('action' => 'save', 'warnmail_type' => $warnmail_type, 'warnmail_email_address' => $warnmail_email_address);

				if($this->boconfig->config_worktime_warnmail($values) == true)
					$message = lang('setting has been saved');
				else
					$message = lang('setting has not been saved');
			}

			if(isset($_POST['done']))
			{
				$GLOBALS['phpgw']->redirect_link('/admin/index.php');
			}

			$header_info = lang('config worktime warnmail');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . $header_info;
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);

			$GLOBALS['phpgw']->template->set_file(array('config' => 'config_worktime_warnmail.tpl'));

			$link_data['menuaction'] = 'projects.uiconfig.config_worktime_warnmail';
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			//$GLOBALS['phpgw']->template->set_var('worktime_warnmail_desc',lang('Specify how many work days before monthly allowance you would like to send a warning'));
			$GLOBALS['phpgw']->template->set_var('worktime_warnmail_desc',lang('Send a warning mail at the end of a month if not enough worktime was captured.'));
			$GLOBALS['phpgw']->template->set_var('message',$message);

			$values = array('action' => 'get');

			$warnmail = $this->boconfig->config_worktime_warnmail($values);
			$warnmail_type_selected = $warnmail['type'];
			$warnmail_email_address = $warnmail['warnmail_email_address'];

			// create option list for selectbox
			// first option for disable warnmail
			if($warnmail_type_selected == -1)
			{
				$option_list = '<option value="-1" selected>'.lang('off').'</option>';
			}
			else
			{
				$option_list = '<option value="-1">'.lang('off').'</option>';
			}

			/*
			// build other elements of option list
			//  0 = last work day of month (day of monthly allowance)
			//  1 = one work day before monthly allowance
			//  2 = two work days before monthly allowance
			// ...
			for($i=0; $i<=7; ++$i)
			{
				if($warnmail_type_selected == $i)
					$selected = ' selected';
				else
					$selected = '';

				$option_list .= "<option value=\"".$i."\"".$selected.">".$i."</option>\n";
			}
			*/
			if($warnmail_type_selected == 1)
				$selected = ' selected';
			else
				$selected = '';
			$option_list .= "<option value=\"1\"".$selected.">".lang('active')."</option>\n";

			$selectbox = "<select name=\"warnmail_type\">\n".$option_list."</select>";
			$GLOBALS['phpgw']->template->set_var('warnmail_type_selectbox', $selectbox);
			$GLOBALS['phpgw']->template->set_var('warnmail_email_address', $warnmail_email_address);

			$GLOBALS['phpgw']->template->pfp('out','config');
		}

		function config_locations()
		{
			$save	= get_var('save',array('POST'));
			$done	= get_var('done',array('POST'));
			$edit	= get_var('edit',array('GET'));
			$delete	= get_var('delete',array('GET'));
			$location_id = intval(get_var('location_id',array('GET')));
			$message = '';
			$error = array();
			$submit = false;

			if($done)
			{
				$GLOBALS['phpgw']->redirect_link('/admin/index.php');
			}

			$link_data = array('menuaction' => 'projects.uiconfig.config_locations');
			$action_url = $GLOBALS['phpgw']->link('/index.php',$link_data);

			if($save)
			{
				$values	= get_var('values',array('POST'));
				$values['location_id'] = intval($values['location_id']);
				$values['location_name'] = trim($values['location_name']);
				$values['location_ident'] = trim($values['location_ident']);
				$values['location_custnum'] = trim($values['location_custnum']);

				if(!$values['location_name'])
				{
					$error[] = lang('please insert a location name');
				}

				$exist_location = $this->boconfig->get_location_for_ident($values['location_ident']);

				if(isset($exist_location['location_id']) && ($exist_location['location_id'] != $values['location_id']))
				{
					$exist_location_id = $exist_location['location_id'];
					$error[] = lang('location ident exists');
				}
				else
				{
					$exist_location_id = 0;
				}

				if(count($error) > 0)
				{ // error
					$message = $GLOBALS['phpgw']->common->error_list($error);
					$submit = true;
					if($values['location_id'] > 0)
					{ // edit existing location
						$location_id = $values['location_id'];
						$save = 0;
						$edit = 1;
					}
				}
				else
				{
					$this->boconfig->save_location($values);
					//$GLOBALS['phpgw']->redirect($action_url);
					$message = lang('location saved');
					$values = array(
						'location_name' => '',
						'location_ident' => '',
						'location_custnum' => '',
						'location_id' => 0
					);
				}
			}
			elseif($delete && ($location_id > 0))
			{
				$this->boconfig->delete_location($location_id);
				$message = lang('location deleted');
			}
			else
			{
				$values = array(
					'location_name' => '',
					'location_ident' => '',
					'location_custnum' => '',
					'location_id' => 0
				);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('edit locations');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('location_list_t' => 'config_locations.tpl'));
			$GLOBALS['phpgw']->template->set_block('location_list_t','location_list','list');
			$GLOBALS['phpgw']->template->set_var('message', $message);

			$GLOBALS['phpgw']->template->set_var('action_url', $action_url);

			$GLOBALS['phpgw']->template->set_var('lang_location', lang('location'));
			$GLOBALS['phpgw']->template->set_var('lang_ident', 'ID');
			$GLOBALS['phpgw']->template->set_var('lang_custnum', lang('customer nr'));

			$locations = $this->boconfig->get_locations();

			if(is_array($locations))
			{
				foreach($locations as $location)
				{
					$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

					if($location_id == $location['location_id'])
					{
						$location['location_name'] = '<i>'.$location['location_name'].'</i>';
						$location['location_ident'] = '<i>'.$location['location_ident'].'</i>';
						$location['location_custnum'] = '<i>'.$location['location_custnum'].'</i>';
					}

					if($exist_location_id == $location['location_id'])
					{
						$location['location_name'] = '<b>'.$location['location_name'].'</b>';
						$location['location_ident'] = '<b>'.$location['location_ident'].'</b>';
						$location['location_custnum'] = '<B>'.$location['location_custnum'].'</b>';
					}

					$GLOBALS['phpgw']->template->set_var(array
					(
						'location_id'     => $location['location_id'],
						'location_name'   => $location['location_name'],
						'location_ident'  => $location['location_ident'],
						'location_custnum' => $location['location_custnum'],
						'delete_url'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.config_locations',
																							'location_id'=> $location['location_id'],
																							'delete'=>1)),
						'edit_url'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.config_locations',
																							'location_id'=> $location['location_id'],
																							'edit'=>1)),
						'edit_img'			=> $GLOBALS['phpgw']->common->image('phpgwapi','edit'),
						'lang_edit_location'	=> lang('edit location'),
						'delete_img'			=> $GLOBALS['phpgw']->common->image('phpgwapi','delete'),
						'lang_delete_location'	=> lang('delete location')
					));
					$GLOBALS['phpgw']->template->fp('list','location_list',True);
				}
			}

			if($edit && !$submit && ($location_id > 0))
			{
				$values = $this->boconfig->get_single_location($location_id);
			}

			$GLOBALS['phpgw']->template->set_var('input_location_name',$values['location_name']);
			$GLOBALS['phpgw']->template->set_var('input_location_ident',$values['location_ident']);
			$GLOBALS['phpgw']->template->set_var('input_location_custnum',$values['location_custnum']);
			$GLOBALS['phpgw']->template->set_var('input_location_id',$values['location_id']);

			if($edit)
			{
				$GLOBALS['phpgw']->template->set_var('lang_submit_action',lang('edit'));
				$GLOBALS['phpgw']->template->set_var('lang_location_button',lang('save'));
				$GLOBALS['phpgw']->template->set_var('cancel_button', '<input type="button" value="'.lang('cancel').'" onClick="location.href=\''.$action_url.'\'">');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('lang_submit_action',lang('add'));
				$GLOBALS['phpgw']->template->set_var('lang_location_button',lang('add'));
				$GLOBALS['phpgw']->template->set_var('cancel_button', '<input type="reset" value="'.lang('reset').'">');
			}

			$GLOBALS['phpgw']->template->pfp('out','location_list_t',True);
		}

	}
?>
