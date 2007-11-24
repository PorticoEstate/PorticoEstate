<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @author Philipp Kamps [pkamps@probusinesss.de]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: class.uiprojecthours.inc.php,v 1.94 2006/12/05 19:40:45 sigurdne Exp $
	* $Source: /sources/phpgroupware/projects/inc/class.uiprojecthours.inc.php,v $
	*/

	class uiprojecthours
	{
		var $grants;
		var $start;
		var $filter;
		var $sort;
		var $order;
		var $state;
		var $cat_id;
		var $project_id;
		var $ui_base;

		var $public_functions = array
		(
			'list_hours'				=> True,
			'edit_hours'				=> True,
			'delete_hours'				=> True,
			'view_hours'				=> True,
			'list_projects'				=> True,
			'ttracker'					=> True,
			'edit_ttracker'				=> True,
			'unbook_hours'				=> True,
			'controlling_sheet'			=> True,
			'import_controlling_sheet'	=> True
		);

		function uiprojecthours()
		{
			$this->ui_base					= CreateObject('projects.uiprojects_base');
			$this->boprojects				= $this->ui_base->boprojects;

			$this->bohours					= CreateObject('projects.boprojecthours');

			$this->nextmatchs				= CreateObject('phpgwapi.nextmatchs');
			$this->account					= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->grants					= $GLOBALS['phpgw']->acl->get_grants('projects');
			$this->grants[$this->account]	= PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;

			$this->attached_files			= CreateObject('projects.attached_files');

			$this->start					= $this->bohours->start;
			$this->query					= $this->bohours->query;
			$this->filter					= $this->bohours->filter;
			$this->order					= $this->bohours->order;
			$this->sort						= $this->bohours->sort;
			$this->status					= $this->bohours->status;
			$this->state					= $this->bohours->state;
			$this->cat_id					= $this->bohours->cat_id;
			$this->project_id				= $this->bohours->project_id;

			$this->siteconfig				= $this->bohours->siteconfig;
		}

		function save_sessiondata($action)
		{
			$data = array
			(
				'start'      => $this->start,
				'query'      => $this->query,
				'filter'     => $this->filter,
				'order'      => $this->order,
				'sort'       => $this->sort,
				'status'     => $this->status,
				'state'      => $this->state,
				'project_id' => $this->project_id,
				'cat_id'     => $this->cat_id
			);
			$this->boprojects->save_sessiondata($data,$action);
		}

		function list_projects()
		{
			$action		= get_var('action',array('POST','GET'));
			$pro_main	= get_var('pro_main',array('POST','GET'));
			$project_id	= get_var('project_id',array('POST','GET'));

			if ($_GET['cat_id'])
			{
				$this->cat_id = $_GET['cat_id'];
			}

			if (!$action)
			{
				if (strlen($project_id))
				{
					$action = 'mainsubsorted';
					$pro_main = $project_id;
				}
				else
				{
					$action = 'mains';
				}
			}

			$link_data = array
			(
				'menuaction' => 'projects.uiprojecthours.list_projects',
				'pro_main'   => $pro_main,
				'action'     => $action,
				'status'     => $this->status,
				'project_id' => $project_id
			);

			if ($action == 'mainsubsorted')
			{
				$this->boprojects->status = false; // workaround for full tree view support
				$pro = $this->boprojects->list_projects(array('action' => $action,'project_id' => $pro_main,'page' => 'hours', 'limit' => false));
			}
			else
			{
				$pro = $this->boprojects->list_projects(array('action' => $action,'parent' => $pro_main,'page' => 'hours'));
			}

			//if($action=='subs' && !is_array($pro))
			//{
			//		$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=>'projects.uiprojecthours.list_hours',
			//														'project_id'=> $pro_main,
			//														'action'=>'hours'));
			//}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('list projects')
			//												. $this->admin_header_info();

			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('projects_list_t' => 'list_pro_hours.tpl'));
			$GLOBALS['phpgw']->template->set_block('projects_list_t','projects_list','list');
			$GLOBALS['phpgw']->template->set_block('projects_list_t','project_main','main');

			if($pro_main && $action != 'mainsubsorted')
			{
				$main = $this->boprojects->read_single_project($pro_main,'hours');
				$GLOBALS['phpgw']->template->set_var('title_main',$main['title']);
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.view_project',
																									'action'=>'mains',
																									'project_id'=>$pro_main)));
				$GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
				$GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
				$GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
				$GLOBALS['phpgw']->template->set_var('url_main',$main['url']);
				$GLOBALS['phpgw']->template->set_var('utime_main',str_replace(".", ":", $main['uhours_jobs']));
				$GLOBALS['phpgw']->template->set_var('ptime_main',str_replace(".", ":", sprintf("%1.02f",$main['ptime'])));
				$GLOBALS['phpgw']->template->set_var('atime_main',str_replace(".", ":", sprintf("%1.02f",$main['ahours_jobs'])));

				$GLOBALS['phpgw']->template->parse('main','project_main',True);
			}

// --------------------- nextmatch variable template-declarations ------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boprojects->total_records,$this->start));

// ------------------------- end nextmatch template --------------------------------------

			//if ($action == 'mains')
			//{
			//	$action_list= '<select name="cat_id" onChange="this.form.submit();"><option value="none">' . lang('Select category') . '</option>' . "\n"
			//				. $this->boprojects->cats->formatted_list('select','all',$this->cat_id,True) . '</select>';
			//}
			//else
			//{
			//	$action_list= '<select name="pro_main" onChange="this.form.submit();"><option value="">' . lang('Select main project') . '</option>' . "\n"
			//				. $this->boprojects->select_project_list(array('status' => $this->status, 'selected' => $pro_main)) . '</select>';
			//}
			if($pro_main)
			{
				$cat_id = $this->boprojects->return_value('cat', $pro_main);
				$action_list = lang('category').': '.$this->boprojects->cats->id2name($cat_id);
				$action_list = '<input style="border: solid 2px #d0d0d0;" readonly="readonly" size="60" type="text" value="&nbsp;'.$action_list.'">';
			}

			$GLOBALS['phpgw']->template->set_var('action_list',$action_list);
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('filter_list',$this->nextmatchs->new_filter($this->filter));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
			$GLOBALS['phpgw']->template->set_var('status_list',$this->ui_base->status_format($this->status));

// ---------------- list header variable template-declarations --------------------------

			$GLOBALS['phpgw']->template->set_var('sort_number',$this->nextmatchs->show_sort_order($this->sort,'p_number',$this->order,'/index.php',lang('project id'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_planned',$this->nextmatchs->show_sort_order($this->sort,'time_planned',$this->order,'/index.php',lang('planned'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('Title'),$link_data));

// -------------- end header declaration ---------------------------------------

			for ($i=0;$i<count($pro);$i++)
			{
				$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

// --------------- template declaration for list records -------------------------------------


				$link_data['project_id'] = $pro[$i]['project_id'];
				if ($action == 'mains')
				{
					$projects_url = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojecthours.list_projects',
																				'pro_main'=> $pro[$i]['project_id'],
																				'action'=>'subs'));
				}
				else
				{
					$projects_url = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojecthours.list_hours',
																				'project_id'=> $pro[$i]['project_id'],
																				'action'=>'hours',
																				'pro_main'=>$pro_main));
				}

				$GLOBALS['phpgw']->template->set_var(array
				(
					'number'				=> $pro[$i]['number'],
					'title'					=> $pro[$i]['title']?$pro[$i]['title']:lang('browse'),
					'projects_url'			=> $projects_url,
					'phours_pro'			=> $this->bohours->sohours->min2str($pro[$i]['item_planned_time']),
					'phours'				=> $this->bohours->sohours->min2str($pro[$i]['sum_planned_time']),
					'uhours_pro'			=> $this->bohours->sohours->min2str($pro[$i]['item_used_time']),
					'uhours_jobs'			=> $this->bohours->sohours->min2str($pro[$i]['sum_used_time']),
					'uhours_pro_nobill'		=> $this->bohours->sohours->min2str($pro[$i]['item_nobill_time']),
					'uhours_jobs_nobill'	=> $this->bohours->sohours->min2str($pro[$i]['sum_nobill_time']),
					'uhours_pro_bill'		=> $this->bohours->sohours->min2str($pro[$i]['item_bill_time']),
					'uhours_jobs_bill'		=> $this->bohours->sohours->min2str($pro[$i]['sum_bill_time']),
					'ahours_pro'			=> $this->bohours->sohours->min2str($pro[$i]['item_avail_time']),
					'ahours_jobs'			=> $this->bohours->sohours->min2str($pro[$i]['sum_avail_time']),

					'list_class_sum'		=> $pro[$i]['is_leaf']?'leaf_sum':'node_sum',
					'list_class_item'		=> $pro[$i]['is_leaf']?'leaf_item':'node_item',
					'value_class_sum'		=> 'value_'.$pro[$i]['sum_time_status'],
					'value_class_item'		=> 'value_'.$pro[$i]['item_time_status']
				));
				$GLOBALS['phpgw']->template->parse('list','projects_list',True);
			}

// ------------------------- end record declaration ------------------------

// --------------- template declaration for Add Form --------------------------
/*
			if ($action=='subs' && $pro_main && is_array($main) && $this->bohours->add_perms(array('main' => $pro_main,'main_co' => $main['coordinator'])))
			{
				$link_data['menuaction'] = 'projects.uiprojecthours.edit_hours';
				$link_data['project_id'] = $pro_main;
				$link_data['action'] = 'hours';
				unset($link_data['hours_id']);
				$GLOBALS['phpgw']->template->set_var('add','<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data)
																. '"><input type="submit" value="' . lang('add work hours to the main project') . '"></form>');
			}

			if ($action=='subs' && $pro_main)
			{
				unset($link_data);
				$link_data['menuaction'] = 'projects.uiprojecthours.list_hours';
				$link_data['project_id'] = $pro_main;
				$link_data['pro_main'] = $pro_main;
				$link_data['action'] = 'hours';
				$GLOBALS['phpgw']->template->set_var('view_hours','<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data)
																. '"><input type="submit" value="' . lang('View work hours of the main project') . '"></form>');
			}

			unset($link_data);
			$link_data['menuaction'] = 'projects.uiprojecthours.controlling_sheet';
			$GLOBALS['phpgw']->template->set_var('view_controlling_sheet','<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data)
															. '"><input type="hidden" name="datum[start]" value="'.$start.'"><input type="hidden" name="datum[end]" value="'.$end.'"><input type="submit" name="view" value="' . lang('View controlling sheet') . '"></form>');

			$this->project_id = $pro_main;
			if ($this->bohours->add_perms(array('main' => $pro_main,'main_co' => $main['coordinator'])) && $pro_main > 0)
			{
				$link_data['menuaction']	= 'projects.uiprojecthours.edit_hours';
				$link_data['project_id']	= $pro_main;
				$GLOBALS['phpgw']->template->set_var('action','<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data)
																. '"><input type="submit" value="' . lang('Add hours to main project') . '"></form>');
			}

// ----------------------- end Add form declaration ----------------------------
*/
			$this->save_sessiondata($action);
			$GLOBALS['phpgw']->template->pfp('out','projects_list_t',True);
		}

		function list_hours()
		{
			$action			= get_var('action',array('POST','GET'));
			$project_id		= get_var('project_id',array('POST','GET'));
			$pro_main		= get_var('pro_main',array('POST','GET'));
			$this->state	= get_var('state',array('POST','GET'));
			$values			= get_var('values',array('POST','GET'));

			$jscal = CreateObject('phpgwapi.jscalendar');	// before phpgw_header() !!!

			$sdate = get_var('sdate',array('POST','GET'));
			$edate = get_var('edate',array('POST','GET'));

			if($_REQUEST['submit'])
			{
				$GLOBALS['phpgw']->session->appsession('session_data', 'projectsStartDate', $jscal->input2date($sdate));
				$GLOBALS['phpgw']->session->appsession('session_data', 'projectsEndDate',  $jscal->input2date($edate));
			}

			$sdateSession =  $GLOBALS['phpgw']->session->appsession('session_data','projectsStartDate');

			$sdate = $sdateSession['raw'];

			if(is_numeric($sdate))
			{
				$start = $sdate;
			}
			elseif($sdate && !is_numeric($sdate))
			{
				$start_array = $jscal->input2date($sdate);
				$start = mktime(0,0,0,$start_array['month'],$start_array['day'],$start_array['year']);
			}
			else
			{
				$start = mktime(0,0,0,date('m'),1,date('Y'));
			}

			$edateSession =  $GLOBALS['phpgw']->session->appsession('session_data','projectsEndDate');
			$edate = $edateSession['raw'];

			if(is_numeric($edate))
			{
				$end = $edate;
			}
			elseif($edate && !is_numeric($edate))
			{
				$end_array = $jscal->input2date($edate);
				$end = mktime(23,59,59,$end_array['month'],$end_array['day'],$end_array['year']);
			}
			else
			{
				$end = mktime(23,59,59,date('m')+1,0,date('Y'));
			}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('list work hours') . $this->admin_header_info();

			$this->ui_base->display_app_header();

			$this->project_id = intval($project_id);

			$GLOBALS['phpgw']->template->set_file(array('hours_list_t' => 'hours_listhours.tpl'));
			$GLOBALS['phpgw']->template->set_block('hours_list_t','hours_list','list');
			$GLOBALS['phpgw']->template->set_block('hours_list_t','project_main','main');

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojecthours.list_hours',
				'project_id'	=> $this->project_id,
				'pro_main'		=> $pro_main,
				'action'		=> 'hours'
			);

			if($this->project_id)
			{
				$this->attached_files = CreateObject('projects.attached_files');
				$main = $this->boprojects->read_single_project($this->boprojects->return_value('main',$this->project_id),'hours');
				$GLOBALS['phpgw']->template->set_var('title_main',$main['title']);
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.view_project',
																											'action'=>'mains',
																											'project_id'=>$main['project_id'])));
				$GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
				$GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
				$GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
				$GLOBALS['phpgw']->template->set_var('url_main',$main['url']);
				$GLOBALS['phpgw']->template->set_var('utime_main',$main['uhours_jobs']);
				$GLOBALS['phpgw']->template->set_var('ptime_main',str_replace(".", ":", sprintf("%1.02f",$main['ptime'])));
				$GLOBALS['phpgw']->template->set_var('atime_main',str_replace(".", ":", sprintf("%1.02f",$main['ahours_jobs'])));
				$GLOBALS['phpgw']->template->parse('main','project_main',True);
				$GLOBALS['phpgw']->template->set_var('attachment',$this->attached_files->get_files($this->project_id));
				$GLOBALS['phpgw']->template->set_var('lang_files',lang('Files'));
			}

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('sdate_select',$jscal->input('sdate',$start));
			$GLOBALS['phpgw']->template->set_var('edate_select',$jscal->input('edate',$end));
			$GLOBALS['phpgw']->template->set_var('project_list',$this->boprojects->select_project_list(array('action' => 'all','status' => $this->status,'selected' => $this->project_id)));
			$GLOBALS['phpgw']->template->set_var('lang_update',lang('update'));

			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));

			if ( ($main['coordinator'] == $GLOBALS['phpgw_info']['user']['account_id'])
				|| ($this->boprojects->return_value('coordinator',$this->project_id) == $GLOBALS['phpgw_info']['user']['account_id'])
				|| ($this->boprojects->isprojectadmin('pad'))
				|| ($this->boprojects->isprojectadmin('pmanager'))
				 )
			{
				//$GLOBALS['phpgw']->template->set_var('filter_list',$this->nextmatchs->new_filter(array('format' => 'yours','filter' => 'employee')));

				$employee	= get_var('employee', array('POST','GET'));
				if(!$employee)
				{
					$employee	= $GLOBALS['phpgw_info']['user']['account_id'];
				}

				$format_data = array(
					'project_only' => True,
					'project_id'   => $this->project_id,
					'selected'     => array($employee)
				);

				$filter_employee  = '<select name="employee">';
				$filter_employee .= '<option value="-1">' . lang('show all') . '</option>';
				$filter_employee .=	$this->ui_base->employee_format($format_data);
				$filter_employee .= '</select>';

				if($employee > 0)
				{
					$this->filter   = $this->bohours->filter = 'employee';
					$this->employee = $this->bohours->sohours->employee = $employee;
				}
				else
				{
					$this->filter = $this->bohours->filter = 'none';
				}
			}
			else
			{
				$this->filter = $this->bohours->filter = 'yours';
				$employee = $GLOBALS['phpgw_info']['user']['account_id'];
				$filter_employee = $GLOBALS['phpgw_info']['user']['fullname'];
			}

			$GLOBALS['phpgw']->template->set_var('filter_list', $filter_employee);

			switch($this->state)
			{
				case 'all': $state_sel[0]=' selected';break;
				case 'open': $state_sel[1]=' selected';break;
				case 'done': $state_sel[2]=' selected';break;
				case 'billed': $state_sel[3]=' selected';break;
				default: $state_sel[0]=' selected'; $this->state = 'all';
			}

			$state_list = '<option value="all"' . $state_sel[0] . '>' . lang('Show all') . '</option>' . "\n"
									. '<option value="open"' . $state_sel[1] . '>' . lang('Open') . '</option>' . "\n"
									. '<option value="done"' . $state_sel[2] . '>' . lang('Done') . '</option>' . "\n"
									. '<option value="billed"' . $state_sel[3] . '>' . lang('Billed') . '</option>' . "\n";

			$GLOBALS['phpgw']->template->set_var('state_list',$state_list);
			$this->bohours->state = $this->state;
			$this->bohours->limit = false;

			$hours = $this->bohours->list_hours($start, $end);

// --------------------- nextmatch variable template-declarations ------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->bohours->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->bohours->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->bohours->total_records,$this->start));

// ------------------------- end nextmatch template --------------------------------------

// ---------------- list header variable template-declarations --------------------------

			$GLOBALS['phpgw']->template->set_var('sort_hours_descr',$this->nextmatchs->show_sort_order($this->sort,$this->siteconfig['accounting']=='own'?'hours_descr':'activity',$this->order,'/index.php',lang('Activity'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_status',$this->nextmatchs->show_sort_order($this->sort,'status',$this->order,'/index.php',lang('Status'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_start_date',$this->nextmatchs->show_sort_order($this->sort,'start_date',$this->order,'/index.php',lang('Work date'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_start_time',$this->nextmatchs->show_sort_order($this->sort,'start_date',$this->order,'/index.php',lang('Start time'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_end_time',$this->nextmatchs->show_sort_order($this->sort,'end_date',$this->order,'/index.php',lang('End time'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_hours',$this->nextmatchs->show_sort_order($this->sort,'minutes',$this->order,'/index.php',lang('Hours'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_employee',$this->nextmatchs->show_sort_order($this->sort,'employee',$this->order,'/index.php',lang('Employee'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_t_journey',$this->nextmatchs->show_sort_order($this->sort,'t_journey',$this->order,'/index.php',lang('travel time'),$link_data));

// -------------- end header declaration ---------------------------------------

			for ($i=0;$i<count($hours);$i++)
			{
				$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

// ---------------- template declaration for list records ------------------------------

				$link_data['hours_id'] = $hours[$i]['hours_id'];

				$hours_desr = $this->siteconfig['accounting']=='own'?$hours[$i]['hours_descr']:$hours[$i]['activity_title'];
				if ($this->bohours->edit_perms(array('main' => $main['project_id'],'main_co' => $main['coordinator'],'status' => $hours[$i]['status'],
													'employee' => $hours[$i]['employee'])))
				{
					$link_data['menuaction'] = 'projects.uiprojecthours.view_hours';
					$descr = '<a href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) . '">'
																. $hours_desr . '</a>';
				}
				else
				{
					$descr = $hours_desr;
				}

				if($this->bohours->is_booked($link_data['hours_id']))
				{
					$link_data['menuaction'] = 'projects.uiprojecthours.unbook_hours';
					if($this->boprojects->isprojectadmin('pad') || $this->boprojects->isprojectadmin('pmanager'))
					{
						$link = $GLOBALS['phpgw']->link('/index.php',$link_data);
						$GLOBALS['phpgw']->template->set_var('booked', '<a href="' . $link . '"><img src="projects/templates/' . $GLOBALS['phpgw_info']['server']['template_set'] . '/images/booked1.png" title="' . lang('booked') . '">&nbsp;</a>');
					}
					else
					{
						$GLOBALS['phpgw']->template->set_var('booked', '<img src="projects/templates/' . $GLOBALS['phpgw_info']['server']['template_set'] . '/images/booked1.png" title="' . lang('booked') . '">');
					}
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('booked', '');
				}

				if($this->siteconfig['hoursbookingday'] == 'yes')
				{
					$start_date = $hours[$i]['sdate_formatted']['date'] . ' - ' . $hours[$i]['edate_formatted']['date'];
				}
				else
				{
					$start_date = $hours[$i]['sdate_formatted']['date'];
				}

				$GLOBALS['phpgw']->template->set_var(array(
													'employee'    => $hours[$i]['employeeout'],
													'hours_descr' => $descr,
													'status'      => $hours[$i]['statusout'],
													'start_date'  => $start_date,
													'start_time'  => $hours[$i]['sdate_formatted']['time'],
													'end_time'    => $hours[$i]['edate_formatted']['time'],
													'wh'          => $hours[$i]['wh']['whwm'],
													't_journey'   => $hours[$i]['t_journey']));

				$link_data['menuaction'] = 'projects.uiprojecthours.view_hours';
				$GLOBALS['phpgw']->template->set_var('view_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
				$GLOBALS['phpgw']->template->set_var('view_img',$GLOBALS['phpgw']->common->image('phpgwapi','view'));
				$GLOBALS['phpgw']->template->set_var('lang_view_hours',lang('view hours'));

				$link_data['menuaction'] = 'projects.uiprojecthours.edit_hours';
				$GLOBALS['phpgw']->template->set_var('edit_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
				$GLOBALS['phpgw']->template->set_var('edit_img',$GLOBALS['phpgw']->common->image('phpgwapi','edit'));
				$GLOBALS['phpgw']->template->set_var('lang_edit_hours',lang('edit hours'));

				$GLOBALS['phpgw']->template->fp('list','hours_list',True);

// --------------------------- end record declaration -----------------------------------

			}

			$ptime_pro = $this->boprojects->return_value('ptime',$this->project_id);
			$acc = $this->boprojects->get_budget(array('project_id' => $this->project_id,'ptime' => $ptime_pro));

			$GLOBALS['phpgw']->template->set_var('uhours_pro',$this->boprojects->colored($acc['uhours_pro'],$ptime_pro,$acc['uhours_pro_wminutes'],'hours'));
			$GLOBALS['phpgw']->template->set_var('uhours_jobs',$this->boprojects->colored($acc['uhours_jobs'],$ptime_pro,$acc['uhours_jobs_wminutes'],'hours'));
			$GLOBALS['phpgw']->template->set_var('ahours_jobs',str_replace(".", ":", sprintf("%1.02f",$acc['ahours_jobs'])));
			$GLOBALS['phpgw']->template->set_var('phours',intval($ptime_pro/60) . ':00');

			if ($this->bohours->add_perms(array('main' => $main['project_id'],'main_co' => $main['coordinator'])) && $this->project_id > 0)
			{
				$link_data['menuaction'] = 'projects.uiprojecthours.edit_hours';
				unset($link_data['hours_id']);
				$GLOBALS['phpgw']->template->set_var('action','<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data)
																. '"><input type="submit" value="' . lang('add work hours') . '"></form>');
			}

			unset($link_data);
			$link_data['menuaction'] = 'projects.uiprojecthours.controlling_sheet';
			$GLOBALS['phpgw']->template->set_var('view_controlling_sheet','<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data)
															. '"><input type="hidden" name="datum[start]" value="'.$start.'"><input type="hidden" name="datum[end]" value="'.$end.'"><input type="submit" name="view" value="' . lang('View controlling sheet') . '"></form>');

			$this->save_sessiondata('hours');
			$GLOBALS['phpgw']->template->pfp('out','hours_list_t',True);
		}

// ------ TTRACKER ----------

		function ttracker()
		{
			//$project_id	= get_var('project_id',array('POST','GET'));
			$sdate	= get_var('sdate',array('POST','GET'));
			$edate	= get_var('edate',array('POST','GET'));

			$values	= get_var('values',array('POST'));
			if(!$values || !isset($values['project_id']))
			{
				$project_id = get_var('project_id',array('POST','GET'));
				if($project_id)
				{
					$values['project_id'] = $project_id;
				}
			}

			$jscal = CreateObject('phpgwapi.jscalendar');

			if(is_array($edate))
			{
				$end_array			= $jscal->input2date($edate['str']);
				$end_val			= $end_array['raw'];
				$values['edate']	= $end_val;
			}

			if(is_array($sdate))
			{
				$start_array		= $jscal->input2date($sdate['str']);
				$start_val			= $start_array['raw'];
				$values['sdate']	= $start_val;

				if($this->siteconfig['hoursbookingday'] == 'no')
				{
					$end_val = $start_val; // use the same worktime start and end time
				}
			}

			$values['t_journey'] = intval($values['t_journey_h']*60 + $values['t_journey_m']);
			//_debug_array($values);

			$this->project_id = intval($values['project_id']);
			if($values['start'] || $values['stop'] || $values['continue'] || $values['pause'])
			{
				$error = $this->bohours->check_ttracker($values);

				if (is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->bohours->ttracker($values);
				}
			}
			elseif($values['apply'])
			{
				$values['action']	= 'apply';
				$values['ttracker'] = True;
				$values['hours']   = abs($values['hours']);
				$values['minutes'] = abs($values['minutes']);

				$error = $this->bohours->check_values($values);

				if (is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->bohours->save_hours($values);
				}
			}
			elseif($values['save'])
			{
				$values['action'] = 'save';
				$this->bohours->ttracker($values);
			}
			elseif($_GET['delete'])
			{
				 $this->bohours->delete_hours(array('action' => 'track','id' => $_GET['track_id']));
			}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('time tracker');

			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('ttracker_t' => 'ttracker.tpl'));
			$GLOBALS['phpgw']->template->set_block('ttracker_t','ttracker','track');
			$GLOBALS['phpgw']->template->set_block('ttracker_t','ttracker_list','listhandle');

			$GLOBALS['phpgw']->template->set_block('ttracker_t','activity','activityhandle');
			$GLOBALS['phpgw']->template->set_block('ttracker_t','act_own','actownhandle');

			$GLOBALS['phpgw']->template->set_block('ttracker_t','booking_date','datehandle');
			$GLOBALS['phpgw']->template->set_block('ttracker_t','booking_time','timehandle');

			$project_list = '<option value="">' . lang('Select project') . '</option>' . "\n"
							. $this->boprojects->select_project_list(array('status' => 'active', 'action' => 'all', 'selected' => $this->project_id)) . '</select>';
			$GLOBALS['phpgw']->template->set_var('select_project', $project_list);

			$curr_date = $this->bohours->format_htime(time());

			$GLOBALS['phpgw']->template->set_var('curr_date',$curr_date['date']);
			$GLOBALS['phpgw']->template->set_var('curr_time',$curr_date['time']);

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojecthours.ttracker',
				'project_id'	=> $this->project_id
			);

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->set_var('remark',$values['remark']);
			$GLOBALS['phpgw']->template->set_var('hours',sprintf("%02d",$values['hours']));
			$GLOBALS['phpgw']->template->set_var('minutes',sprintf("%02d",$values['minutes']));
			$GLOBALS['phpgw']->template->set_var('km_distance',sprintf("%01.2f",$values['km_distance']));
			$GLOBALS['phpgw']->template->set_var('t_journey_h',sprintf("%02d", floor($values['t_journey']/60)));
			$GLOBALS['phpgw']->template->set_var('t_journey_m',sprintf("%02d", intval($values['t_journey']%60)));
			$GLOBALS['phpgw']->template->set_var('surcharge_list',$this->boprojects->action_format($values['surcharge'],'charge'));

			$start = $start_val?$start_val:mktime(12,0,0,date('m'),date('d'),date('Y'));
			$GLOBALS['phpgw']->template->set_var('start_date_select',$jscal->input('sdate[str]',$start));

			if($this->siteconfig['hoursbookingday'] == 'yes')
			{
				$end = $end_val?$end_val:mktime(12,0,0,date('m'),date('d'),date('Y'));
				$GLOBALS['phpgw']->template->set_var('end_date_select',$jscal->input('edate[str]',$end));

				$GLOBALS['phpgw']->template->fp('datehandle','booking_date',True);
			}
			else
			{
				$GLOBALS['phpgw']->template->fp('timehandle','booking_time',True);
			}

			if($this->siteconfig['accounting'] == 'activity')
			{
				$GLOBALS['phpgw']->template->set_var('activity_list',$this->boprojects->select_hours_activities($this->project_id,$values['activity_id']));
				$GLOBALS['phpgw']->template->fp('activityhandle','activity',True);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('hours_descr',$values['hours_descr']);
				$GLOBALS['phpgw']->template->fp('actownhandle','act_own',True);
			}

			$tracking = $this->bohours->list_ttracker();

			//_debug_array($tracking);

			while(is_array($tracking) && (list($no_use,$track) = each($tracking)))
			{
				$level_title = $track['project_title'];
				if($track['project_level'] > 0)
				{
					$level_title = str_repeat('&nbsp;&nbsp;&nbsp;', $track['project_level']).$level_title;
				}

				$GLOBALS['phpgw']->template->set_var('project_title',$level_title);
				$GLOBALS['phpgw']->template->set_var('project_id',$track['project_id']);
				$GLOBALS['phpgw']->template->set_var('radio_checked',($track['project_id']==$this->project_id?' CHECKED':''));

				$GLOBALS['phpgw']->template->set_var('thours_list','');

				for($i=0;$i<count($track['hours']);$i++)
				{
					$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

// ---------------- template declaration for list records ------------------------------

					if($track['hours'][$i]['wh']['whours_formatted'] == 0 && $track['hours'][$i]['wh']['wmin_formatted'] == 0)
					{
						$wh = '';
					}
					else
					{
						$wh = $track['hours'][$i]['wh']['whours_formatted'] . '.' . sprintf("%02d",$track['hours'][$i]['wh']['wmin_formatted']);
					}

					if($track['hours'][$i]['journey']['whours_formatted'] == 0 && $track['hours'][$i]['wh']['wmin_formatted'] == 0)
					{
						$journey = '';
					}
					else
					{
						$journey = $track['hours'][$i]['journey']['whours_formatted'] . '.' . sprintf("%02d",$track['journey'][$i]['wh']['wmin_formatted']);
					}

					switch($track['hours'][$i]['status'])
					{
						case 'apply':	$at = $track['hours'][$i]['sdate_formatted']['date']; break;
						default:		$at = $track['hours'][$i]['sdate_formatted']['time'];
					}

					$GLOBALS['phpgw']->template->set_var(array(
													'hours_descr'	=> $this->siteconfig['accounting']=='own'?$track['hours'][$i]['hours_descr']:$track['hours'][$i]['activity_title'],
													'statusout'		=> lang($track['hours'][$i]['status']),
													'start_date'	=> $track['hours'][$i]['sdate_formatted']['date'],
													'start_time'	=> ($track['hours'][$i]['status']!='apply')?$track['hours'][$i]['sdate_formatted']['time']:$track['hours'][$i]['sdate_formatted']['date'],
													'apply_time'	=> $at,
													'end_time'		=> ($track['hours'][$i]['status']!='apply'?($track['hours'][$i]['edate']>0?$track['hours'][$i]['edate_formatted']['time']:
																		''):$track['hours'][$i]['edate_formatted']['date']),
													'wh'			=> str_replace(".", ":", sprintf("%1.02f", $wh)),
													'journey'     => str_replace(".", ":", sprintf("%1.02f", $journey)),
													'delete_url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojecthours.ttracker',
																													'delete'=>'True',
																													'track_id'=>$track['hours'][$i]['track_id'])),
													'edit_url'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojecthours.edit_ttracker',
																													'track_id'=> $track['hours'][$i]['track_id'])),
													'delete_img'	=> $GLOBALS['phpgw']->common->image('phpgwapi','delete'),
													'lang_delete'	=> lang('delete')));

					$GLOBALS['phpgw']->template->fp('thours_list','ttracker_list',True);
				}
				$GLOBALS['phpgw']->template->fp('track','ttracker',True);
			}

			$GLOBALS['phpgw']->template->set_var('listhandle','');
			$GLOBALS['phpgw']->template->pfp('out','ttracker_t',True);
			$this->save_sessiondata('hours');
		}

		function edit_ttracker()
		{
			$jscal		= CreateObject('phpgwapi.jscalendar');
			$track_id	= get_var('track_id',array('POST','GET'));
			$values		= $_POST['values'];
			$edate		= get_var('edate',array('POST','GET'));
			$sdate		= get_var('sdate',array('POST','GET'));

			if(is_array($edate))
			{
				$end_array	= $jscal->input2date($edate['str']);
				$end_val	= $end_array['raw'];
			}

			if(is_array($sdate))
			{
				$start_array	= $jscal->input2date($sdate['str']);
				$start_val		= $start_array['raw'];

				if($this->siteconfig['hoursbookingday'] == 'no')
				{
					$end_val = $start_val; // use the same worktime start and end time
				}
			}

			if($_POST['save'] || $_POST['cancel'])
			{
				if($_POST['save'])
				{
					$values['t_journey'] = intval($values['t_journey_h']*60 + $values['t_journey_m']);
					$values['hours']   = abs($values['hours']);
					$values['minutes'] = abs($values['minutes']);

					if ($GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == '12')
					{
						if ($values['shour'] && ($values['shour'] != 0) && ($values['shour'] != 12))
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
						}
					}

					if (intval($start_val) > 0)
					{
						$values['smonth']	= date('m',$start_val);
						$values['sday']		= date('d',$start_val);
						$values['syear']	= date('Y',$start_val);
						$values['sdate']	= mktime(($values['shour']?$values['shour']:0),($values['smin']?$values['smin']:0),0,$values['smonth'],$values['sday'],$values['syear']);
					}

					if (intval($end_val) > 0)
					{
						$values['emonth']	= date('m',$end_val);
						$values['eday']		= date('d',$end_val);
						$values['eyear']	= date('Y',$end_val);
						$values['edate']	= mktime(($values['ehour']?$values['ehour']:0),($values['emin']?$values['emin']:0),0,$values['emonth'],$values['eday'],$values['eyear']);
					}

					if(($values['hours'] == 0) && ($values['minutes'] == 0) && ($values['t_journey'] == 0))
					{
						$minutes = (intval($values['ehour'])*60 + intval($values['emin'])) - (intval($values['shour']*60) + intval($values['smin']));
						if($minutes < 0)
						{
							$minutes = 0;
						}
						$values['hours']   = intval($minutes / 60);
						$values['minutes'] = intval($minutes % 60);
					}
					/*else
					{
						$minutes = intval($values['hours'])*60 + intval($values['minutes']);
					}*/

					$values['track_id']	= $track_id;
					$this->bohours->save_hours($values);
				}
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=>'projects.uiprojecthours.ttracker'));
			}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('edit time tracker entry');

			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('ttracker_form' => 'ttracker_form.tpl'));
			$GLOBALS['phpgw']->template->set_block('ttracker_form','activity','activityhandle');
			$GLOBALS['phpgw']->template->set_block('ttracker_form','act_own','actownhandle');

			$GLOBALS['phpgw']->template->set_block('ttracker_form','booking_date','datehandle');
			$GLOBALS['phpgw']->template->set_block('ttracker_form','booking_time','timehandle');

			$values = $this->bohours->read_single_track($track_id);

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojecthours.edit_ttracker',
				'track_id'		=> $track_id
			);

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			if($this->siteconfig['accounting'] == 'activity')
			{
				$GLOBALS['phpgw']->template->set_var('activity_list',$this->boprojects->select_hours_activities($values['project_id'],$values['activity_id']));
				$GLOBALS['phpgw']->template->fp('activityhandle','activity',True);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('hours_descr',$values['hours_descr']);
				$GLOBALS['phpgw']->template->fp('actownhandle','act_own',True);
			}

			$start	= $start_val?$start_val:($values['sdate']?mktime(12,0,0,date('m',$values['sdate']),date('d',$values['sdate']),date('Y',$values['sdate'])):mktime(12,0,0,date('m'),date('d'),date('Y')));
			$shour	= $values['sdate']?date('H',$values['sdate']):date('H',time());
			$smin	= $values['sdate']?date('i',$values['sdate']):date('i',time());

			$GLOBALS['phpgw']->template->set_var('start_date_select',$jscal->input('sdate[str]',$start));

			$amsel = ' checked';
			$pmsel = '';

			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == '12')
			{
				if ($values['sdate_formatted']['hour'] >= 12)
				{
					$amsel = '';
					$pmsel = ' checked';
					if ($values['sdate_formatted']['hour'] > 12)
					{
						$values['sdate_formatted']['hour'] = $values['sdate_formatted']['hour'] - 12;
					}
				}

				if ($values['sdate_formatted']['hour'] == 0)
				{
					$values['sdate_formatted']['hour'] = 12;
				}

				$sradio = '<input type="radio" name="values[sampm]" value="am"' . $amsel . '>am';
				$sradio .= '<input type="radio" name="values[sampm]" value="pm"' . $pmsel . '>pm';
				$GLOBALS['phpgw']->template->set_var('sradio',$sradio);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('sradio','');
			}

			$GLOBALS['phpgw']->template->set_var('shour',$shour);
			$GLOBALS['phpgw']->template->set_var('smin',$smin);

			$ehour	= $values['edate']?date('H',$values['edate']):date('H',time());
			$emin	= $values['edate']?date('i',$values['edate']):date('i',time());

			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == '12')
			{
				if ($ehour >= 12)
				{
					$amsel = '';
					$pmsel = ' checked';

					if ($ehour > 12)
					{
						$ehour = $ehour - 12;
					}
				}
				if ($ehour == 0)
				{
					$ehour = 12;
				}

				$eradio = '<input type="radio" name="values[eampm]" value="am"' . $amsel . '>am';
				$eradio .= '<input type="radio" name="values[eampm]" value="pm"' . $pmsel . '>pm';
				$GLOBALS['phpgw']->template->set_var('eradio',$eradio);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('eradio','');
			}

			$GLOBALS['phpgw']->template->set_var('ehour',$ehour);
			$GLOBALS['phpgw']->template->set_var('emin',$emin);

			if($this->siteconfig['hoursbookingday'] == 'yes')
			{
				$end = $end_val?$end_val:($values['edate']?mktime(12,0,0,date('m',$values['edate']),date('d',$values['edate']),date('Y',$values['edate'])):mktime(12,0,0,date('m'),date('d'),date('Y')));
				$GLOBALS['phpgw']->template->set_var('end_date_select',$jscal->input('edate[str]',$end));

				$GLOBALS['phpgw']->template->fp('datehandle','booking_date',True);
			}
			else
			{
				$GLOBALS['phpgw']->template->fp('timehandle','booking_time',True);
			}

			$GLOBALS['phpgw']->template->set_var('status_list',$this->status_format($values['status']));
			$GLOBALS['phpgw']->template->set_var('remark',$values['remark']);
			$GLOBALS['phpgw']->template->set_var('hours_descr',$values['hours_descr']);

			$GLOBALS['phpgw']->template->set_var('hours',$values['wh']['whours_formatted']);
			$GLOBALS['phpgw']->template->set_var('minutes',$values['wh']['wmin_formatted']);

			$GLOBALS['phpgw']->template->set_var('km_distance',sprintf("%01.2f",$values['km_distance']));
			$GLOBALS['phpgw']->template->set_var('t_journey_h', $values['t_journey']['whours_formatted']);
			$GLOBALS['phpgw']->template->set_var('t_journey_m', $values['t_journey']['wmin_formatted']);
			$GLOBALS['phpgw']->template->set_var('surcharge_list',$this->boprojects->action_format($values['surcharge'],'charge'));

			//$GLOBALS['phpgw']->template->set_var('project_name',$GLOBALS['phpgw']->strip_html($this->boprojects->return_value('pro',$this->project_id)));

			$GLOBALS['phpgw']->template->pfp('out','ttracker_form');
		}

		function status_format($status = '')
		{
			switch ($status)
			{
				case 'open'	:	$stat_sel[0]=' selected'; break;
				case 'done'	:	$stat_sel[1]=' selected'; break;
				default		:	$stat_sel[1]=' selected'; break;
			}

			$status_list = '<option value="open"' . $stat_sel[0] . '>' . lang('Open') . '</option>' . "\n"
						. '<option value="done"' . $stat_sel[1] . '>' . lang('Done') . '</option>' . "\n";

			return $status_list;
		}

		/*function employee_format($employee = '')
		{
			if (! $employee)
			{
				$employee = $this->account;
			}
			$employees = $this->boprojects->selected_employees(array('project_id' => $this->project_id,'admins_included' = True));

			if(!is_array($employees))
			{
				return '';
			}

			while (list($null,$account) = each($employees))
			{
				$employee_list .= '<option value="' . $account['account_id'] . '"';
				if($account['account_id'] == $employee)
				$employee_list .= ' selected';
				$employee_list .= '>' . $account['account_firstname'] . ' ' . $account['account_lastname']
										. ' [ ' . $account['account_lid'] . ' ]' . '</option>' . "\n";
			}
			return $employee_list;
		}*/

		function edit_hours()
		{
			$values					= get_var('values',array('POST'));
			$values['project_id']	= get_var('project_id',array('POST','GET'));
			//$pro_main             = get_var('pro_main',array('POST','GET'));

			$hours_id				= get_var('hours_id',array('POST','GET'));
			$delivery_id			= get_var('delivery_id',array('POST','GET'));
			$invoice_id				= get_var('invoice_id',array('POST','GET'));
			$edate					= get_var('edate',array('POST','GET'));
			$sdate					= get_var('sdate',array('POST','GET'));

			$pro_main				= $this->boprojects->return_value('main',$values['project_id']);
			$jscal					= CreateObject('phpgwapi.jscalendar');

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojecthours.controlling_sheet',
				'hours_id'		=> $hours_id,
				//'project_id'	=> $values['project_id'],
				'pro_main'		=> $pro_main,
				'delivery_id'	=> $delivery_id,
				'invoice_id'	=> $invoice_id
			);

			if(is_array($edate))
			{
				$end_array	= $jscal->input2date($edate['str']);
				$end_val	= $end_array['raw'];
			}

			if(is_array($sdate))
			{
				$start_array	= $jscal->input2date($sdate['str']);
				$start_val		= $start_array['raw'];

				if($this->siteconfig['hoursbookingday'] == 'no')
				{
					$end_val = $start_val; // use the same worktime start and end time
				}
			}

			if ($_POST['save'])
			{
				$values['t_journey'] = intval($values['t_journey_h']*60 + $values['t_journey_m']);
				$values['hours']   = abs($values['hours']);
				$values['minutes'] = abs($values['minutes']);
				if ($GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == '12')
				{
					if ($values['shour'] && ($values['shour'] != 0) && ($values['shour'] != 12))
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
					}
				}

				if (intval($start_val) > 0)
				{
					$values['smonth']	= date('m',$start_val);
					$values['sday']		= date('d',$start_val);
					$values['syear']	= date('Y',$start_val);
					$values['sdate']	= mktime(($values['shour']?$values['shour']:0),($values['smin']?$values['smin']:0),0,$values['smonth'],$values['sday'],$values['syear']);
				}

				if (intval($end_val) > 0)
				{
					$values['emonth']	= date('m',$end_val);
					$values['eday']		= date('d',$end_val);
					$values['eyear']	= date('Y',$end_val);
					$values['edate']	= mktime(($values['ehour']?$values['ehour']:0),($values['emin']?$values['emin']:0),0,$values['emonth'],$values['eday'],$values['eyear']);
				}

				if(($values['hours'] == 0) && ($values['minutes'] == 0) && ($values['t_journey'] == 0))
				{
					$minutes = (intval($values['ehour'])*60 + intval($values['emin'])) - (intval($values['shour']*60) + intval($values['smin']));
					if($minutes < 0)
					{
						$minutes = 0;
					}
					$values['hours']   = intval($minutes / 60);
					$values['minutes'] = intval($minutes % 60);
				}
				else
				{
					$minutes = intval($values['hours'])*60 + intval($values['minutes']);
				}

				$values['pro_main']	= $pro_main;
				$values['hours_id']	= $hours_id;

				$error = $this->bohours->check_values($values);
				if (is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->bohours->save_hours($values);
					$link_data['project_id'] = $values['project_id'];
					if($link_data['menuaction'] == 'projects.uiprojecthours.edit_hours')
					{
						$link_data['menuaction'] = 'projects.uiprojecthours.list_hours';
					}
					$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
				}
			}
			elseif($_POST['cancel'])
			{
				//$link_data['menuaction'] = 'projects.uiprojecthours.list_hours';
				$link_data['project_id'] = $values['project_id'];
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}
			elseif($_POST['delete'])
			{
				$link_data['project_id'] = $values['project_id'];
				$link_data['menuaction'] = 'projects.uiprojecthours.delete_hours';
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($hours_id?lang('edit work hours'):lang('add work hours'))
			//											. $this->admin_header_info();

			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('hours_form' => 'hours_formhours.tpl'));
			$GLOBALS['phpgw']->template->set_block('hours_form','activity','activityhandle');
			$GLOBALS['phpgw']->template->set_block('hours_form','activity_own','actownhandle');
			$GLOBALS['phpgw']->template->set_block('hours_form','booking_date','datehandle');
			$GLOBALS['phpgw']->template->set_block('hours_form','booking_time','timehandle');
			$GLOBALS['phpgw']->template->set_block('hours_form','main','mainhandle');

			$link_data['menuaction'] = 'projects.uiprojecthours.edit_hours';
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$values['billable'] = $this->boprojects->soprojects->return_value('billable', $values['project_id']);

			$pro_main = $this->boprojects->soprojects->return_value('main', $values['project_id']);

			if($pro_main)
			{
				$main = $this->boprojects->read_single_project($pro_main,'hours');
				$GLOBALS['phpgw']->template->set_var('pro_main',$main['title']);
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.view_project',
																											'action'=>'mains',
																											'project_id'=> $pro_main)));
				$GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
				$GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
				$GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
				$GLOBALS['phpgw']->template->set_var('url_main',$main['url']);
				$GLOBALS['phpgw']->template->set_var('utime_main',$main['uhours_jobs']);
				$GLOBALS['phpgw']->template->set_var('ptime_main',$main['ptime']);
				$GLOBALS['phpgw']->template->set_var('atime_main',$main['ahours_jobs']);
				$GLOBALS['phpgw']->template->fp('mainhandle','main',True);
			}

			if ($hours_id && !$_POST['save'])
			{
				$values				= $this->bohours->read_single_hours($hours_id);
				$activity_id		= $values['activity_id'];
				$pro_parent			= $values['pro_parent'];
				$values['hours']	= $values['wh']['whours_formatted'];
				$values['minutes']	= $values['wh']['wmin_formatted'];
				//_debug_array($values);
			}

			if($start_val)
			{
				$start = $start_val;
			}
			elseif(!$start_val && $_REQUEST['day'])
			{
				$start = $_REQUEST['day'];
			}
			else
			{
				$start = ($values['sdate']?mktime(0,0,0,date('m',$values['sdate']),date('d',$values['sdate']),date('Y',$values['sdate'])):mktime(0,0,0,date('m'),date('d'),date('Y')));
			}

			$shour = $values['sdate']?date('H',$values['sdate']):'08';
			$smin  = $values['sdate']?date('i',$values['sdate']):'00';

			$GLOBALS['phpgw']->template->set_var('start_date_select',$jscal->input('sdate[str]',$start));

			$amsel = ' checked';
			$pmsel = '';

			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == '12')
			{
				if ($shour >= 12)
				{
					$amsel = '';
					$pmsel = ' checked';
					if ($shour > 12)
					{
						$shour = $shour - 12;
					}
				}

				if ($shour == 0)
				{
					$shour = 12;
				}

				$sradio = '<input type="radio" name="values[sampm]" value="am"' . $amsel . '>am';
				$sradio .= '<input type="radio" name="values[sampm]" value="pm"' . $pmsel . '>pm';
				$GLOBALS['phpgw']->template->set_var('sradio',$sradio);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('sradio','');
			}

			$GLOBALS['phpgw']->template->set_var('shour', sprintf("%02d", $shour));
			$GLOBALS['phpgw']->template->set_var('smin', sprintf("%02d", $smin));

			$end = $end_val?$end_val:($values['edate']?mktime(0,0,0,date('m',$values['edate']),date('d',$values['edate']),date('Y',$values['edate'])):mktime(0,0,0,date('m'),date('d'),date('Y')));
			if(!intval(date('H',$values['edate'])) && !intval(date('i',$values['edate'])) && ($minutes > 0))
			{
				$s_minutes	= $shour * 60 + $smin;
				$e_minutes	= $s_minutes + $minutes;
				$ehour		= intval($e_minutes/60);
				$emin		= intval($e_minutes%60);
			}
			else
			{
				$ehour	= $values['edate']?date('H',$values['edate']):'17';
				$emin	= $values['edate']?date('i',$values['edate']):'00';
			}

			$GLOBALS['phpgw']->template->set_var('end_date_select',$jscal->input('edate[str]',$end));


			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == '12')
			{
				if ($ehour >= 12)
				{
					$amsel = '';
					$pmsel = ' checked';

					if ($ehour > 12)
					{
						$ehour = $ehour - 12;
					}
				}
				if ($ehour == 0)
				{
					$ehour = 12;
				}

				$eradio = '<input type="radio" name="values[eampm]" value="am"' . $amsel . '>am';
				$eradio .= '<input type="radio" name="values[eampm]" value="pm"' . $pmsel . '>pm';
				$GLOBALS['phpgw']->template->set_var('eradio',$eradio);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('eradio','');
			}

			$GLOBALS['phpgw']->template->set_var('ehour', sprintf("%02d", $ehour));
			$GLOBALS['phpgw']->template->set_var('emin', sprintf("%02d", $emin));

			$GLOBALS['phpgw']->template->set_var('status_list',$this->status_format($values['status']));
			$GLOBALS['phpgw']->template->set_var('remark',$values['remark']);
			$GLOBALS['phpgw']->template->set_var('hours_descr',$values['hours_descr']);

			$GLOBALS['phpgw']->template->set_var('hours', sprintf('%02d',intval($values['hours'])));
			$GLOBALS['phpgw']->template->set_var('minutes', sprintf('%02d',$values['minutes']));

			$GLOBALS['phpgw']->template->set_var('project_name',$GLOBALS['phpgw']->strip_html($this->boprojects->return_value('pro',$this->project_id)));

			$GLOBALS['phpgw']->template->set_var('hours_billable_checked',($values['billable']=='N'?' CHECKED':''));
			$GLOBALS['phpgw']->template->set_var('km_distance',sprintf("%01.2f",$values['km_distance']));

			$GLOBALS['phpgw']->template->set_var('t_journey_h', sprintf('%02d', floor($values['t_journey']/60)));
			$GLOBALS['phpgw']->template->set_var('t_journey_m', sprintf('%02d', intval($values['t_journey']%60)));

			$GLOBALS['phpgw']->template->set_var('surcharge_list',$this->boprojects->action_format($values['surcharge'],'charge'));

			if ($this->siteconfig['hoursbookingday'] == 'yes')
			{
				$GLOBALS['phpgw']->template->fp('datehandle','booking_date',True);
			}
			else
			{
				$GLOBALS['phpgw']->template->fp('timehandle','booking_time',True);
			}

			if($this->siteconfig['accounting'] == 'activity')
			{
				$GLOBALS['phpgw']->template->set_var('activity_list',$this->boprojects->select_hours_activities($this->project_id,$activity_id));
				$GLOBALS['phpgw']->template->fp('activityhandle','activity',True);
			}
			else
			{
				$GLOBALS['phpgw']->template->fp('actownhandle','activity_own',True);
			}

			/*if ($values['pro_parent'] > 0)
			{
				$GLOBALS['phpgw']->template->set_var('pro_parent',$GLOBALS['phpgw']->strip_html($this->boprojects->return_value('pro',$pro_parent)));
				$GLOBALS['phpgw']->template->set_var('lang_pro_parent',lang('Main project:'));
			}*/

			if ($this->bohours->edit_perms(array('adminonly' => True,'status' => $values['status'],'main_co' => $main['coordinator'])))
			{
				$options = $this->ui_base->employee_format(array('type'            => 'selectbox',
																												 'selected'        => ($values['employee']?$values['employee']:$this->account),
																												 'admins_included' => True,
																												 'project_id'      => $values['project_id']
																												)
																									);

				$GLOBALS['phpgw']->template->set_var('employee','<select name="values[employee]">'.$options.'</select>');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('employee',$values['employeeout']?$values['employeeout']:$GLOBALS['phpgw']->common->grab_owner_name($this->account));
			}

			$project_options = $this->boprojects->select_project_list(array(
				'filter' => 'none',
				'action' => 'all',
				'limit' => False,
				'status' => 'active',
				'selected' => $values['project_id']
			));

			$GLOBALS['phpgw']->template->set_var('project_options', $project_options);

			if ($hours_id && $this->bohours->edit_perms(array('action' => 'delete','status' => $values['status'],'main_co' => $main['coordinator'], 'booked' => $values['booked'], 'employee' => $values['employee'])))
			{
				$GLOBALS['phpgw']->template->set_var('delete','<input type="submit" name="delete" value="' . lang('Delete') .'">');
			}

			if ($hours_id && $this->bohours->edit_perms(array('action' => 'edit','status' => $values['status'],'main_co' => $main['coordinator'], 'booked' => $values['booked'], 'employee' => $values['employee'])))
			{
				$GLOBALS['phpgw']->template->set_var('save','<input type="submit" name="save" value="' . lang('Save') .'">');
			}
			elseif ($hours_id)
			{
				$GLOBALS['phpgw']->template->set_var('booked',lang('Activity already booked!'));
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('save','<input type="submit" name="save" value="' . lang('Save') .'">');
			}

			$this->save_sessiondata('hours');
			$GLOBALS['phpgw']->template->pfp('out','hours_form');
		}

		function view_hours()
		{
			$hours_id	= get_var('hours_id',array('GET'));
			$project_id	= get_var('project_id',array('GET'));
			$pro_main	= get_var('pro_main',array('GET'));

			$link_data = array
			(
				'menuaction' => 'projects.uiprojecthours.list_hours',
				'project_id' => $project_id,
				'action'     => 'hours'
			);
			if (!$hours_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('view work hours')
			//											. $this->admin_header_info();

			$this->ui_base->display_app_header();


			$GLOBALS['phpgw']->template->set_file(array('hours_view' => 'hours_view.tpl'));
			$GLOBALS['phpgw']->template->set_block('hours_view','main','mainhandle');
			$GLOBALS['phpgw']->template->set_var('doneurl',$GLOBALS['phpgw']->link('/index.php', $link_data));

			$nopref = $this->boprojects->check_prefs();
			if ($nopref)
			{
				$GLOBALS['phpgw']->template->set_var('pref_message',lang('Please set your preferences for this application !'));
			}
			else
			{
				$prefs = $this->boprojects->get_prefs();
			}

			$values = $this->bohours->read_single_hours($hours_id);
/*
			if($pro_main)
			{
				$main = $this->boprojects->read_single_project($pro_main,'hours');
				$GLOBALS['phpgw']->template->set_var('pro_main',$main['title']);
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.view_project','action'=>'mains','project_id'=> $pro_main)));
				$GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
				$GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
				$GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
				$GLOBALS['phpgw']->template->set_var('url_main',$main['url']);
				$GLOBALS['phpgw']->template->set_var('utime_main',$main['uhours_jobs']);
				$GLOBALS['phpgw']->template->set_var('ptime_main',$main['ptime'].':00');
				$GLOBALS['phpgw']->template->set_var('atime_main',$main['ahours_jobs']);
				$GLOBALS['phpgw']->template->fp('mainhandle','main',True);
			}
*/
			$GLOBALS['phpgw']->template->set_var('status',$values['statusout']);

			$GLOBALS['phpgw']->template->set_var('sdate',$values['stime_formatted']['date']);
			$GLOBALS['phpgw']->template->set_var('stime',$values['stime_formatted']['time']);

			$GLOBALS['phpgw']->template->set_var('edate',$values['etime_formatted']['date']);
			$GLOBALS['phpgw']->template->set_var('etime',$values['etime_formatted']['time']);

			$GLOBALS['phpgw']->template->set_var('remark',$values['remark']);

			$GLOBALS['phpgw']->template->set_var('hours',$values['wh']['whours_formatted']);
			$GLOBALS['phpgw']->template->set_var('minutes',$values['wh']['wmin_formatted']);

			$GLOBALS['phpgw']->template->set_var('currency',$prefs['currency']);
			$GLOBALS['phpgw']->template->set_var('minperae',$values['minperae']);
			$GLOBALS['phpgw']->template->set_var('billperae',$values['billperae']);
			$GLOBALS['phpgw']->template->set_var('employee',$values['employeeout']);
			$GLOBALS['phpgw']->template->set_var('km_distance',$values['km_distance']);
			$GLOBALS['phpgw']->template->set_var('t_journey', sprintf("%02d:%02d", floor($values['t_journey']/60), intval($values['t_journey']%60)));

			$GLOBALS['phpgw']->template->set_var('project_name',$GLOBALS['phpgw']->strip_html($this->boprojects->return_value('pro',$values['project_id'])));

			if($this->siteconfig['accounting'] == 'activity')
			{
				$GLOBALS['phpgw']->template->set_var('activity',$GLOBALS['phpgw']->strip_html($this->boprojects->return_value('act',$values['activity_id'])));
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('activity',$values['hours_descr']);
			}
			$GLOBALS['phpgw']->template->pfp('out','hours_view');
		}

		function delete_hours()
		{
			$hours_id	= get_var('hours_id',array('POST','GET'));
			$project_id	= get_var('project_id',array('POST','GET'));

			$link_data = array
			(
				'hours_id'		=> $hours_id,
				'project_id'	=> $project_id
			);

			if ($_POST['no'])
			{
				$link_data['menuaction'] = 'projects.uiprojecthours.edit_hours';
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ($_POST['yes'])
			{
				$link_data['menuaction'] = 'projects.uiprojecthours.list_hours';
				$this->bohours->delete_hours(array('id' => $hours_id));
				unset($link_data['hours_id']);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('delete work hours')
			//											. $this->admin_header_info();

			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('hours_delete' => 'delete.tpl'));

			$GLOBALS['phpgw']->template->set_var('lang_subs','');
			$GLOBALS['phpgw']->template->set_var('subs', '');
			$GLOBALS['phpgw']->template->set_var('deleteheader',lang('Are you sure you want to delete this entry ?'));
			$GLOBALS['phpgw']->template->set_var('lang_no',lang('No'));
			$GLOBALS['phpgw']->template->set_var('lang_yes',lang('Yes'));

			$link_data['menuaction'] = 'projects.uiprojecthours.delete_hours';
			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->pfp('out','hours_delete');
		}

		function unbook_hours()
		{
			$hours_id	= get_var('hours_id',array('POST','GET'));
			$project_id	= get_var('project_id',array('POST','GET'));

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojecthours.list_hours',
				'project_id'	=> $project_id
			);

			if ($_POST['yes'] || $_POST['no'])
			{
				if($_POST['yes'])
				{
					$this->sohours->unbook_hours($hours_id);
				}
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('edit booked work hours')
			//											. $this->admin_header_info();

			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('hours_booked' => 'booked.tpl'));

			$GLOBALS['phpgw']->template->set_var('unbook',lang('Are you sure you want to make this entry editable?'));
			$GLOBALS['phpgw']->template->set_var('lang_no',lang('No'));
			$GLOBALS['phpgw']->template->set_var('lang_yes',lang('Yes'));

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojecthours.unbook_hours',
				'hours_id'		=> $hours_id
			);
			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->pfp('out','hours_booked');
		}


		function controlling_sheet()
		{
			set_time_limit(120);
			$jscal = CreateObject('phpgwapi.jscalendar');	// before phpgw_header() !!!
			if($_REQUEST['datum'])
			{
				$GLOBALS['phpgw']->session->appsession('session_data', 'projectsCSheetSDate', $jscal->input2date($_REQUEST['datum']['start']));
				$GLOBALS['phpgw']->session->appsession('session_data', 'projectsCSheetEDate', $jscal->input2date($_REQUEST['datum']['end']));
			}
			$start_array = $GLOBALS['phpgw']->session->appsession('session_data','projectsCSheetSDate');
			$end_array = $GLOBALS['phpgw']->session->appsession('session_data','projectsCSheetEDate');
			// Workaround for new actions
			if(get_var('export', array('POST')))
			{
				$this->export_controlling_sheet($start_array, $end_array);
			}
			if(get_var('import', array('POST')))
			{
				$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiprojecthours.import_controlling_sheet')));
			}
			$values	= get_var('view', array('POST', 'GET'));
			$this->ui_base->display_app_header();
			$GLOBALS['phpgw']->template->set_file(array('controlling' => 'hours_controlling.tpl'));
			if ($this->boprojects->isprojectadmin('pad') || $this->boprojects->isprojectadmin('pmanager'))
			{
				$employee	= get_var('employee', array('POST','GET'));
				if(!$employee)
				{
					$employee	= $GLOBALS['phpgw_info']['user']['account_id'];
				}
				$format_data = array('selected' => array($employee));
				$filter_employee  = '<select name="employee">';
				$filter_employee .=	$this->ui_base->employee_format($format_data);
				$filter_employee .= '</select>';
				$GLOBALS['phpgw']->template->set_var('l_employee', $filter_employee);
			}
			else
			{
				$employee = $GLOBALS['phpgw_info']['user']['account_id'];
				$GLOBALS['phpgw']->template->set_var('l_employee', $GLOBALS['phpgw_info']['user']['fullname']);
			}
			$GLOBALS['phpgw']->template->set_var('l_view_sheet', lang('View Sheet'));
			$GLOBALS['phpgw']->template->set_var('l_export_sheet', lang('Export Sheet'));
			$GLOBALS['phpgw']->template->set_var('l_import_sheet', lang('Import Sheet'));
			$start = $start_array['raw'] > 1 ? $start_array['raw'] : mktime(0,0,0,date('m'),date('d') - (date('w')-1),date('Y'));
			$end   = $end_array['raw'] > 1 ?   $end_array['raw']   : mktime(0,0,0,date('m'),date('d') + (7 - date('w')),date('Y'));
			if($start > $end)
			{
				$temp  = $end;
				$end   = $start;
				$start = $temp;
			}

			$GLOBALS['phpgw']->template->set_var(array(
              'sdate_select' => $jscal->input('datum[start]', $start),
              'edate_select' => $jscal->input('datum[end]', $end),
              'view_hours_link' => $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojecthours.edit_hours'))
            ));
			$calholidays = CreateObject('phpgwapi.calendar_holidays');
			$matrix = $this->bohours->build_controlling_matrix($employee, $start, $end);
			$prefs = $this->boprojects->read_prefs(True);
			$rowtitles = $prefs['cscolumns'];
			if ((count($rowtitles) == 0) || !$rowtitles[0])
			{
				$rowtitles[0] = 'title';
			}
			if (count($matrix) > 0)
			{

                /************************* Head/Body *****************/
    			$GLOBALS['phpgw']->template->set_block('controlling','blk_row_title0','blk_row_title0_i');
                $GLOBALS['phpgw']->template->set_block('controlling','matrix_day','matrix_day_i');
                $GLOBALS['phpgw']->template->set_block('controlling','body_row','body_row_i');
                $GLOBALS['phpgw']->template->set_block('body_row','row_title','row_title_j');
                $GLOBALS['phpgw']->template->set_block('body_row','content_cell','content_cell_j');
                //$GLOBALS['phpgw']->template->set_var('th_bg_theme',$GLOBALS['phpgw_info']['theme']['th_bg']);
                $GLOBALS['phpgw']->template->set_var('th_bg_theme',$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']['th_bg']);

                $line = 0;
                $row  = 0;
				for ($i = 0; $i < count($rowtitles); ++$i)
				{
					switch($rowtitles[$i])
					{
						case 'number':
							$row_title = lang('project id');
						    break;
						case 'edateout':
							$row_title = lang('date due');
						    break;
						case 'title':
							$row_title = lang('title');
						    break;
						default:
							$row_title = '';
					}
					$GLOBALS['phpgw']->template->set_var(array(
                      'l_rowTitles' => $row_title,
                      'cell_row' => $row++,

                      'pnumber' => '{pnumber' . $i . '}',
                      'title' => '{title' . $i . '}',
                      'enddate' => '{enddate' . $i . '}'
                    ));
                    $GLOBALS['phpgw']->template->set_var('cell_line',0);
					$GLOBALS['phpgw']->template->parse('blk_row_title0_i','blk_row_title0',true);
                    $GLOBALS['phpgw']->template->unset_var('cell_line');
                    $GLOBALS['phpgw']->template->set_var('cell_row','{cell_row' . $i . '}');
                    $GLOBALS['phpgw']->template->parse('row_title_i','row_title',true);
 				}
                // continue with $i (row)
				foreach ($matrix[0]['days'] as $key => $value)
				{
					if (!$calholidays->is_workday($key))
					{
						$daytotal[$key]['should'] = 0;
						$holidaystyle = 'class="holiday"';
					}
					else
					{
						$daytotal[$key]['should'] = 480;
                        $holidaystyle = '';
					}
					$GLOBALS['phpgw']->template->set_var(array(
                      'holidaystyle' => $holidaystyle,
                      'date' => date('d.m', $key),
                      'cell_row' => $row++,

                      'matrix_value' => '{matrix_value' . $i . '}',
                      'content_value' => '{content_value' . $i . '}',
                      'content_tooltip' => '{content_tooltip' . $i . '}'
                    ));
                    $GLOBALS['phpgw']->template->set_var('cell_line',0);
					$GLOBALS['phpgw']->template->parse('matrix_day_i','matrix_day',true);
                    $GLOBALS['phpgw']->template->unset_var('cell_line');
                    $GLOBALS['phpgw']->template->set_var('cell_row','{cell_row' . $i . '}');
                    $GLOBALS['phpgw']->template->parse('content_cell_i','content_cell',true);
                    ++$i;
				}
				$GLOBALS['phpgw']->template->set_var('l_total', lang('Total'));
                /************************* Body **********************/
                for($i = 0; $i < count($matrix); ++$i)
                {
                  ++$line;
                  $row = 0;
                  // row titles: name, number, endtime
                  $GLOBALS['phpgw']->template->set_var(array(
                    'row_color' => 'background-color:#' . (($i % 2) ? 'FFFFFF' : 'EEEEEE'),
                    'cell_line' => $line,
                    'matrix_link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiprojecthours.list_hours','project_id'=> $matrix[$i]['id']))
                  ));
                  for ($j = 0; $j < count($rowtitles); ++$j)
                  {
                    $GLOBALS['phpgw']->template->set_var('cell_row' . $j,$row++);
                    switch ($rowtitles[$j])
                    {
                      case 'number':
                        $GLOBALS['phpgw']->template->set_var(array(
                          'pnumber' . $j => $matrix[$i]['pnumber'],
                          'title' . $j => '',
                          'enddate' . $j => ''
                        ));
                        break;
                      case 'edateout':
                        $GLOBALS['phpgw']->template->set_var(array(
                          'pnumber' . $j => '',
                          'title' . $j => '',
                          'enddate' . $j => ($matrix[$i]['enddate'] > 0) ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],$matrix[$i]['enddate']) : ''
                        ));
                        break;
                      case 'title':
                      default:
                        $GLOBALS['phpgw']->template->set_var(array(
                          'pnumber' . $j => '',
                          'title' . $j => $matrix[$i]['title'],
                          'enddate' . $j => ''
                        ));
                    }
                  }
                  $GLOBALS['phpgw']->template->parse('row_title_j','row_title_i',false);
                  // row columns
                  // continue with $j (row)
                  foreach($matrix[$i]['days'] as $key => $value)
                  {
                    $daytotal[$key]['booked'] += $value;
                    $matrix[$i]['days']['total'] += $value;
                    if(is_integer($key))
                    {
                      $tooltip = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],$key) . ' : ' . trim(str_replace('&nbsp;',' ',$matrix[$i]['title']));
                    }
                    else
                    {
                      $tooltip = '';
                    }
                    $GLOBALS['phpgw']->template->set_var(array(
                      'matrix_value' . $j => $matrix[$i]['id'] . ', ' . $key,
                      'content_value'. $j => ($this->bohours->format_minutes($value) != '') ? $this->bohours->format_minutes($value) : '&nbsp;',
                      'content_tooltip' . $j => $tooltip,
                      'cell_row' . $j => $row++
                    ));
                    ++$j;
                  }
                  $GLOBALS['phpgw']->template->parse('content_cell_j','content_cell_i',false);
                  $GLOBALS['phpgw']->template->set_var(array(
                    'content_tooltip' => lang('total').' '.lang('project').' '.$matrix[$i]['title'],
                    'row_total_value' => $this->bohours->format_minutes($matrix[$i]['days']['total']),
                    'cell_line' => $line,
                    'cell_row' => $row++
                  ));
                  $GLOBALS['phpgw']->template->parse('body_row_i','body_row',true);
                  $GLOBALS['phpgw']->template->set_var(array(
                    'row_title_j' => '',
                    'content_cell_j' => ''
                  ));
                }
                //************************* Foot **********************/
				$GLOBALS['phpgw']->template->set_var(array(
                  //'theme_th_bg' => $GLOBALS['phpgw_info']['theme']['th_bg'],
                  'theme_th_bg' => $GLOBALS['phpgw_info']['user']['preferences']['common']['theme']['th_bg'],
                  'rowtitles' => count($rowtitles),
                  'l_total' => lang('Total'),
                  'total_cell_line' => 0,
                  'total_cell_row' => --$row,
                ));
				$GLOBALS['phpgw']->template->set_block('controlling', 'daytotal', 'daytotal_i');
                $GLOBALS['phpgw']->template->set_block('controlling', 'daytotal2', 'daytotal2_i');
				$line_total_2 = ++$line;
				$line_total   = ++$line;
				$row = 1;
				foreach($daytotal as $key => $value)
				{
                    // Booked
					$booked_total += $value['booked'];
                    $tooltip = lang('Total').' : '.date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],$key);
					$GLOBALS['phpgw']->template->set_var(array(
                      'format_minutes' => $this->bohours->format_minutes($value['booked']),
                      'content_tooltip' => $tooltip,
                      'cell_line' => $line_total,
                      'cell_row' => $row++
                    ));
					$GLOBALS['phpgw']->template->parse('daytotal_i','daytotal', True);
                    // Difference
                    $should_total += $value['should'];
                    $tooltip = lang('Overtime').' : '.date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],$key);
                    $GLOBALS['phpgw']->template->set_var(array(
                      'format_minutes' => $this->bohours->format_minutes($value['booked'] - $value['should']),
                      'content_tooltip' => $tooltip,
                      'cell_line' => $line_total_2
                    ));
                    $GLOBALS['phpgw']->template->parse('daytotal2_i','daytotal2', True);
				}
				$GLOBALS['phpgw']->template->set_var(array(
                  'booked_total' => $this->bohours->format_minutes($booked_total),
                  //'theme_bg' => $GLOBALS['phpgw_info']['theme']['th_bg'],
                  'theme_bg' => $GLOBALS['phpgw_info']['user']['preferences']['common']['theme']['th_bg'],
                  'rowtitles' => count($rowtitles),
                  'l_overtime' => lang('Overtime'),
                  'booked_cell_line' => $line_total,
                  'booked_cell_row' => 0,
                  'booked_total2' => $this->bohours->format_minutes($booked_total - $should_total),
                  'booked2_cell_line' => $line_total_2,
                  'booked2_cell_row' => 0
                ));
			}
			$GLOBALS['phpgw']->template->pfp('out','controlling',false);
		}


		function export_controlling_sheet($start_array, $end_array)
		{
			$export = $this->bohours->export_controlling_sheet($start_array['raw'],
																												 $end_array['raw']
																												);

			header('Content-Disposition: attachment; filename=controllingsheet_'.$start_array['month'].'_'.$start_array['year'].'.csv');
			echo $export;
			$GLOBALS['phpgw']->common->phpgw_exit();
		}


		function import_controlling_sheet()
		{
			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('import controlling sheet')
			//                                                . $this->admin_header_info();

			$this->ui_base->display_app_header();


			if(get_var('upload', array('POST', 'GET')))
			{
				if($_FILES['file']['name'])
				{
					if(!$_FILES['file']['error'])
					{
						$handle = fopen ($_FILES['file']['tmp_name'], 'r');
						$content = fread($handle, '100000');
						$content = str_replace('"','', $content);
						$lines = explode("\n", $content);
						$savematrix = $this->bohours->build_import_controlling_sheet($lines, $error);
					}
					else
					{
						$error = lang('error while uploading file');
					}
				}
				else
				{
					$error = lang('no file selected');
				}

				$GLOBALS['phpgw']->template->set_file(array('controlling_import_result' => 'controlling_import_result.tpl'));
				$out = '';

				if(!strlen($error) && (count($savematrix)>0))
				{
					$out .= '<tr>';
					$out .= '<td>&nbsp;</td>';
					$out .= '<td>';
					$out .= lang('project');
					$out .= '</td>';
					$out .= '<td>';
					$out .= lang('project id');
					$out .= '</td>';
					$out .= '<td>';
					$out .= lang('customer');
					$out .= '</td>';
					$out .= '<td>';
					$out .= lang('date');
					$out .= '</td>';
					$out .= '<td>';
					$out .= lang('work time');
					$out .= '</td>';
					$out .= '<td>';
					$out .= lang('travel time');
					$out .= '</td>';
					$out .= '<td>';
					$out .= lang('description');
					$out .= '</td>';
					$out .= '<td>';
					$out .= lang('result');
					$out .= '</td>';
					$out .= '</tr>';

					for($i=0; $i < count($savematrix); $i++)
					{
						if(!strlen($savematrix[$i]['error']))
						{
							$hours   = (int) ($savematrix[$i]['time'] / 60);
							$minutes = $savematrix[$i]['time'] % 60;
							$j_hours   = (int) ($savematrix[$i]['journey'] / 60);
							$j_minutes = $savematrix[$i]['journey'] % 60;

							$sum_hm = $savematrix[$i]['time'] + $savematrix[$i]['journey'];
							$sum_h  = (int) ($sum_hm / 60);
							$sum_m = $sum_hm % 60;

							$s_h = (int) ((24 - $sum_h) / 2);
							$s_m = 0;
							$e_h = $s_h + $sum_h;
							$e_m = $sum_m;

							$cebvalues = array('employee'    => $savematrix[$i]['employee'],
																 'hours_descr' => $savematrix[$i]['description'],
																 'remark'      => lang('imported hours on').' '.date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], time()),
																 'hours'       => $hours,
																 'minutes'     => $minutes,
																 'shour'       => $s_h,
																 'smin'        => $s_m,
																 'ehour'       => $e_h,
																 'emin'        => $e_m,
																 't_journey_h' => $j_hours,
																 't_journey_m' => $j_minutes,
																 'km_distance' => 0.00,
																 'status'      => 'done',
																 'surcharge'   => '',
																 'project_id'  => $savematrix[$i]['projectid'],
																 't_journey'   => $savematrix[$i]['journey'],
																 'smonth'      => date('m', $savematrix[$i]['date']),
																 'sday'        => date('d', $savematrix[$i]['date']),
																 'syear'       => date('Y', $savematrix[$i]['date']),
																 'sdate'       => $savematrix[$i]['date'],
																 'emonth'      => date('m', $savematrix[$i]['date']),
																 'eday'        => date('d', $savematrix[$i]['date']),
																 'eyear'       => date('Y', $savematrix[$i]['date']),
																 'edate'       => $savematrix[$i]['date']
																);

							$checkerrors = $this->bohours->check_values($cebvalues);
							if($checkerrors == True)
							{
								$this->bohours->save_hours($cebvalues);
								$result = lang('imported');
								$style = 'passed';
							}
							else
							{
								$result = lang('value check not passed');
								$style = 'notpassed';
							}
						}
						else
						{
							$result = $savematrix[$i]['error'];
							$style = 'notpassed';
						}
						$out .= '<tr class="'.$style.'">';
						$out .= '<td>';
						$out .= sprintf("%d" ,$i);
						$out .= '</td>';
						$out .= '<td>';
						$out .= $savematrix[$i]['projecttitle'];
						$out .= '</td>';
						$out .= '<td>';
						$out .= $savematrix[$i]['projectnumber'];
						$out .= '</td>';
						$out .= '<td>';
						$out .= $savematrix[$i]['customer_org'];
						$out .= '</td>';
						$out .= '<td>';
						$out .= date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $savematrix[$i]['date']);
						$out .= '</td>';
						$out .= '<td align="right">';
						$out .= $cebvalues['hours'].':'.sprintf("%02d", $cebvalues['minutes']);
						$out .= '</td>';
						$out .= '<td align="right">';
						$out .= $cebvalues['t_journey_h'].':'.sprintf("%02d", $cebvalues['t_journey_m']);
						$out .= '</td>';
						$out .= '<td>';
						$out .= $savematrix[$i]['description'];
						$out .= '</td>';
						$out .= '<td>';
						$out .= $result;
						$out .= '</td>';
						$out .= '</tr>';
					}
				}
				else
				{ // nothing to import
						$out .= lang('found no data for import');
				}

				$GLOBALS['phpgw']->template->set_var('action', $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiprojecthours.controlling_sheet')));
				$GLOBALS['phpgw']->template->set_var('import_result', $out);
				$GLOBALS['phpgw']->template->pfp('out','controlling_import_result', True);
				$GLOBALS['phpgw']->common->phpgw_exit();

			}
			$GLOBALS['phpgw']->template->set_file(array('controlling' => 'hours_import_controlling.tpl'));
			$GLOBALS['phpgw']->template->set_var('l_upload', lang('upload'));
			$GLOBALS['phpgw']->template->set_var('l_statement', lang('Please beware').':<br>'
																															 .lang('All correct hours in uploaded CSV file will be added to your account.').'<br>'
																															 .lang('It will NOT check if you already have uploaded hours for a certain day.')
																															);
			$GLOBALS['phpgw']->template->set_var('error', $error);
			$GLOBALS['phpgw']->template->set_var('action', $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiprojecthours.import_controlling_sheet')));

			$GLOBALS['phpgw']->template->pfp('out','controlling',false);
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
	}
?>
