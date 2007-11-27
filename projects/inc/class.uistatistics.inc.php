<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: class.uistatistics.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	* $Source: /sources/phpgroupware/projects/inc/class.uistatistics.inc.php,v $
	*/

	class uistatistics
	{
		var $action;
		var $grants;
		var $start;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $ui_base;

		var $public_functions = array
		(
			'list_projects'				=> True,
			'list_users'				=> True,
			'list_users_worktimes'		=> True,
			'user_stat'					=> True,
			'project_gantt'				=> True,
			'show_stat'					=> True,
			'get_screen_size'			=> True,
			'list_project_employees'	=> true
		);

		function uistatistics()
		{
			$action = get_var('action',array('POST','GET'));

			$this->ui_base              	= CreateObject('projects.uiprojects_base');
			$this->boprojects				= $this->ui_base->boprojects;

			$this->bostatistics				= CreateObject('projects.bostatistics');

			$this->nextmatchs				= CreateObject('phpgwapi.nextmatchs');
			$this->sbox						= CreateObject('phpgwapi.sbox');
			$this->cats						= CreateObject('phpgwapi.categories');
			$this->account					= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->grants					= $GLOBALS['phpgw']->acl->get_grants('projects');
			$this->grants[$this->account]	= PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;

			$this->start					= $this->bostatistics->start;
			$this->query					= $this->bostatistics->query;
			$this->filter					= $this->bostatistics->filter;
			$this->order					= $this->bostatistics->order;
			$this->sort						= $this->bostatistics->sort;
			$this->cat_id					= $this->bostatistics->cat_id;
			$this->status					= $this->bostatistics->status;

			$this->siteconfig				= $this->bostatistics->boprojects->siteconfig;
		}

		function save_sessiondata($action)
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'filter'	=> $this->filter,
				'order'		=> $this->order,
				'sort'		=> $this->sort,
				'cat_id'	=> $this->cat_id,
				'status'	=> $this->status
			);
			$this->boprojects->save_sessiondata($data, $action);
		}

		function list_projects()
		{
			$action		= get_var('action',array('POST','GET'));
			$pro_main	= get_var('pro_main',array('POST','GET'));
			$pro_users	= get_var('pro_users',array('POST','GET'));
			$values		= get_var('values',array('POST','GET'));

			if($_POST['userstats'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=>'projects.uistatistics.list_users'));
			}

			if($_POST['worktimestats'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=>'projects.uistatistics.list_users_worktimes'));
			}

			$pro_user = array();
			if($_POST['viewuser'])
			{
				if(is_array($values['project_id']))
				{
					$i = 0;
					foreach($values['project_id'] as $pro_id => $val)
					{
						$pro_user[$i] = $pro_id;
						$i++;
					}
				}
				else
				{
					$msg = lang('you have no projects selected');
				}
			}

			if($_POST['viewgantt'])
			{
				if(is_array($values['gantt_id']))
				{
					$i = 0;
					foreach($values['gantt_id'] as $pro_id => $val)
					{
						$gantt_user[$i] = $pro_id;
						$i++;
					}
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=>'projects.uistatistics.project_gantt',
																		'project_id'=> implode(',',$gantt_user)));
				}
				else
				{
					$msg = lang('you have no projects selected');
				}
			}

/*
      $GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('list projects')
                              . $this->admin_header_info();
      $this->display_app_header();
*/
      $this->ui_base->display_app_header();

      $GLOBALS['phpgw']->template->set_file(array('projects_list_t' => 'stats_projectlist.tpl'));
      $GLOBALS['phpgw']->template->set_block('projects_list_t','projects_list','list');
      $GLOBALS['phpgw']->template->set_block('projects_list_t','user_list','users');
      $GLOBALS['phpgw']->template->set_block('projects_list_t','user_cols','cols');
      $GLOBALS['phpgw']->template->set_block('projects_list_t','project_main','main');

      $GLOBALS['phpgw']->template->set_var('msg',$msg);

      if($pro_main)
      {
        $main = $this->boprojects->read_single_project($pro_main);
        $GLOBALS['phpgw']->template->set_var('title_main',$main['title']);
        $GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.view_project',
        																							'action'=>'mains',
        																							'project_id'=>$pro_main)));
        $GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
        $GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
        $GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
        $GLOBALS['phpgw']->template->set_var('url_main',$main['url']);
        $GLOBALS['phpgw']->template->parse('main','project_main',True);
      }

      if (!$action)
      {
        $action = 'mains';
      }

      $link_data = array
      (
        'menuaction'	=> 'projects.uistatistics.list_projects',
        'pro_main'		=> $pro_main,
        'action'		=> $action,
        'cat_id'		=> $this->cat_id
      );

      if (!$this->start)
      {
        $this->start = 0;
      }

      $pro = $this->boprojects->list_projects(array('action' => $action,'parent' => $pro_main));

// --------------------- nextmatch variable template-declarations ------------------------

      $left = $this->nextmatchs->left('/index.php',$this->start,$this->boprojects->total_records,$link_data);
      $right = $this->nextmatchs->right('/index.php',$this->start,$this->boprojects->total_records,$link_data);
      $GLOBALS['phpgw']->template->set_var('left',$left);
      $GLOBALS['phpgw']->template->set_var('right',$right);

      $GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boprojects->total_records,$this->start));

// ------------------------- end nextmatch template --------------------------------------

      if ($action == 'mains')
      {
        $action_list = '<select name="cat_id" onChange="this.form.submit();"><option value="none">' . lang('Select category') . '</option>' . "\n"
              . $this->boprojects->cats->formatted_list('select','all',$this->cat_id,True) . '</select>';
      }
      else
      {
        $action_list= '<select name="pro_main" onChange="this.form.submit();"><option value="">' . lang('Select main project') . '</option>' . "\n"
              . $this->boprojects->select_project_list(array('status' => $status, 'selected' => $pro_main)) . '</select>';
      }

      $GLOBALS['phpgw']->template->set_var('action_list',$action_list);
      $GLOBALS['phpgw']->template->set_var('filter_list',$this->nextmatchs->new_filter($this->filter));
      $GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
      $GLOBALS['phpgw']->template->set_var('status_list',$this->ui_base->status_format($this->status));

      $GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

// ---------------- list header variable template-declarations --------------------------

      $GLOBALS['phpgw']->template->set_var('sort_number',$this->nextmatchs->show_sort_order($this->sort,'num',$this->order,'/index.php',lang('Project ID'),$link_data));
      $GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('Title'),$link_data));
      $GLOBALS['phpgw']->template->set_var('sort_sdate',$this->nextmatchs->show_sort_order($this->sort,'start_date',$this->order,'/index.php',lang('Start date'),$link_data));
      $GLOBALS['phpgw']->template->set_var('sort_edate',$this->nextmatchs->show_sort_order($this->sort,'end_date',$this->order,'/index.php',lang('Date due'),$link_data));
      $GLOBALS['phpgw']->template->set_var('sort_coordinator',$this->nextmatchs->show_sort_order($this->sort,'coordinator',$this->order,'/index.php',lang('Coordinator'),$link_data));
      $GLOBALS['phpgw']->template->set_var('user_img',$GLOBALS['phpgw']->common->image('phpgwapi','users'));
      $GLOBALS['phpgw']->template->set_var('user_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

// -------------- end header declaration ---------------------------------------

      for ($i=0;$i<count($pro);$i++)
            {
        if(in_array($pro[$i]['project_id'],$pro_user))
        {
          //$emps[$pro[$i]['project_id']] = $this->boprojects->get_employee_roles(array('project_id' => $pro[$i]['project_id'],'formatted' => True));
          $emps[$pro[$i]['project_id']] = $this->boprojects->selected_employees(array('project_id' => $pro[$i]['project_id'],'roles_included' => True,
                                                'admins_included' => True));
        }

        $this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);
        //_debug_array($emps);
// --------------- template declaration for list records -------------------------------------

        if ($action == 'mains')
        {
          $projects_url = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uistatistics.list_projects',
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
          'number'		=> $pro[$i]['number'],
          'title'			=> ($pro[$i]['title']?$pro[$i]['title']:lang('browse')),
          'projects_url'	=> $projects_url,
          'sdate'			=> $pro[$i]['sdateout'],
          'edate'			=> $pro[$i]['edateout'],
          'coordinator'	=> $pro[$i]['coordinatorout'],
          'view_img'		=> $GLOBALS['phpgw']->common->image('phpgwapi','view'),
          'radio_user_checked'	=> $_POST['viewuser']?(in_array($pro[$i]['project_id'],$pro_user)?' checked':''):'',
          'project_id'	=> $pro[$i]['project_id']
        ));

        $link_data['project_id'] = $pro[$i]['project_id'];
        $link_data['pro_users']	= $pro[$i]['project_id'];
        $link_data['menuaction'] = 'projects.uistatistics.list_projects';
        $GLOBALS['phpgw']->template->set_var('user_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

        $link_data['menuaction'] = 'projects.uiprojects.view_project';
        $GLOBALS['phpgw']->template->set_var('view_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

        $link_data['menuaction'] = 'projects.uistatistics.project_stat';
        $GLOBALS['phpgw']->template->set_var('stat',$GLOBALS['phpgw']->link('/index.php',$link_data));
        $GLOBALS['phpgw']->template->set_var('lang_gantt_entry',lang('gantt chart'));

        $GLOBALS['phpgw']->template->set_var('employee_list','');
        $GLOBALS['phpgw']->template->set_var('users','');
        if(is_array($emps[$pro[$i]['project_id']]))
        {
          foreach($emps[$pro[$i]['project_id']] as $e)
          {
            //_debug_array($e);
            $GLOBALS['phpgw']->template->set_var('emp_name',$e['account_fullname']);
            $GLOBALS['phpgw']->template->set_var('emp_role',$e['role_name']);
            $GLOBALS['phpgw']->template->fp('users','user_list',True);
          }
          $GLOBALS['phpgw']->template->set_var('lang_name',lang('name'));
          $GLOBALS['phpgw']->template->set_var('lang_role',lang('role'));
          $GLOBALS['phpgw']->template->fp('employee_list','user_cols',True);
        }
        $GLOBALS['phpgw']->template->fp('list','projects_list',True);
      }

// ------------------------- end record declaration ------------------------

      $GLOBALS['phpgw']->template->set_var('lang_view_gantt',lang('view gantt chart'));
      $GLOBALS['phpgw']->template->set_var('lang_view_users',lang('view users'));

      $this->save_sessiondata('pstat');
      $GLOBALS['phpgw']->template->set_var('cols','');
      $GLOBALS['phpgw']->template->pfp('out','projects_list_t',True);
    }


    function list_project_employees()
    {
      $project_id		= get_var('project_id',array('POST','GET'));

      //$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('list project employees')
      //												. $this->admin_header_info();

      $this->ui_base->display_app_header();
      $pro_main = $this->ui_base->pro_main;

      $GLOBALS['phpgw']->template->set_file(array('projects_list_t' => 'stats_project_employees.tpl'));
      $GLOBALS['phpgw']->template->set_block('projects_list_t','projects_list','list');
      $GLOBALS['phpgw']->template->set_block('projects_list_t','user_list','users');
      $GLOBALS['phpgw']->template->set_block('projects_list_t','user_cols','cols');
      $GLOBALS['phpgw']->template->set_block('projects_list_t','project_main','main');

      $GLOBALS['phpgw']->template->set_var('msg',$msg);

      if($pro_main)
      {
        $main = $this->boprojects->read_single_project($pro_main);
        $GLOBALS['phpgw']->template->set_var('title_main',$main['title']);
        $GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.view_project',
        																							'action'=>'mains',
        																							'project_id'=> $pro_main)));
        $GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
        $GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
        $GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
        $GLOBALS['phpgw']->template->set_var('url_main',$main['url']);
        $GLOBALS['phpgw']->template->parse('main','project_main',True);
      }

      $link_data = array
      (
        'menuaction'	=> 'projects.uistatistics.list_project_employees',
        'project_id'	=> $project_id,
        'cat_id'		=> $this->cat_id
      );

      if (!$this->start)
      {
        $this->start = 0;
      }

      $this->boprojects->limit = false;
      $this->boprojects->status = false; // workaround for full tree view support
      $pro = $this->boprojects->list_projects(array('action' => 'mainsubsorted','project_id' => $project_id));

// --------------------- nextmatch variable template-declarations ------------------------

      $left = $this->nextmatchs->left('/index.php',$this->start,$this->boprojects->total_records,$link_data);
      $right = $this->nextmatchs->right('/index.php',$this->start,$this->boprojects->total_records,$link_data);
      $GLOBALS['phpgw']->template->set_var('left',$left);
      $GLOBALS['phpgw']->template->set_var('right',$right);

      $GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boprojects->total_records,$this->start));

// ------------------------- end nextmatch template --------------------------------------

      //if ($action == 'mains')
      //{
      //	$action_list = '<select name="cat_id" onChange="this.form.submit();"><option value="none">' . lang('Select category') . '</option>' . "\n"
      //				. $this->boprojects->cats->formatted_list('select','all',$this->cat_id,True) . '</select>';
      //}
      //else
      //{
      //	$action_list= '<select name="pro_main" onChange="this.form.submit();"><option value="">' . lang('Select main project') . '</option>' . "\n"
      //				. $this->boprojects->select_project_list(array('status' => $status, 'selected' => $project_id)) . '</select>';
      //}
      if($pro_main)
      {
        $cat_id = $this->boprojects->return_value('cat', $pro_main);
        $action_list = lang('category').': '.$this->boprojects->cats->id2name($cat_id);
        $action_list = '<input style="border: solid 2px #d0d0d0;" readonly="readonly" size="60" type="text" value="&nbsp;'.$action_list.'">';
      }

      $GLOBALS['phpgw']->template->set_var('action_list',$action_list);
      $GLOBALS['phpgw']->template->set_var('filter_list',$this->nextmatchs->new_filter($this->filter));
      $GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
      $GLOBALS['phpgw']->template->set_var('status_list',$this->ui_base->status_format($this->status));

      $GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

// ---------------- list header variable template-declarations --------------------------

      $GLOBALS['phpgw']->template->set_var('sort_number',$this->nextmatchs->show_sort_order($this->sort,'p_number',$this->order,'/index.php',lang('project id'),$link_data));
      $GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('Title'),$link_data));
      $GLOBALS['phpgw']->template->set_var('sort_sdate',$this->nextmatchs->show_sort_order($this->sort,'start_date',$this->order,'/index.php',lang('Start date'),$link_data));
      $GLOBALS['phpgw']->template->set_var('sort_edate',$this->nextmatchs->show_sort_order($this->sort,'end_date',$this->order,'/index.php',lang('Date due'),$link_data));
      $GLOBALS['phpgw']->template->set_var('sort_coordinator',$this->nextmatchs->show_sort_order($this->sort,'coordinator',$this->order,'/index.php',lang('Coordinator'),$link_data));
      $GLOBALS['phpgw']->template->set_var('user_img',$GLOBALS['phpgw']->common->image('phpgwapi','users'));
      $GLOBALS['phpgw']->template->set_var('user_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

// -------------- end header declaration ---------------------------------------

      for ($i=0;$i<count($pro);$i++)
            {
        $emps[$pro[$i]['project_id']] = $this->boprojects->selected_employees(array('project_id' => $pro[$i]['project_id'],'roles_included' => True,
                                                'admins_included' => True));

        $this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

// --------------- template declaration for list records -------------------------------------

        /*$projects_url = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uistatistics.list_project_employees',
        															'project_id'=>$pro[$i]['project_id']));
        */
        $projects_url = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojecthours.list_hours',
        															'project_id'=> $pro[$i]['project_id'],
        															'action'=>'hours',
        															'pro_main'=> $pro[$i]['main']));

        if($pro[$i]['project_id']==$project_id)
        {
          $node_style = "block";
        }
        else
        {
          $node_style = "none";
        }

        $style = '<style type="text/css">div.node_view_' . $i . ' { display:' . $node_style . ';}</style>';

        $GLOBALS['phpgw']->template->set_var(array
        (
          'number'		=> $pro[$i]['number'],
          'title'			=> ($pro[$i]['title']?$pro[$i]['title']:lang('browse')),
          'projects_url'	=> $projects_url,
          'sdate'			=> $pro[$i]['sdateout'],
          'edate'			=> $pro[$i]['edateout'],
          'coordinator'	=> $pro[$i]['coordinatorout'],
          'view_img'		=> $GLOBALS['phpgw']->common->image('project','users'),
          'radio_user_checked'	=> $_POST['viewuser']?(in_array($pro[$i]['project_id'],$pro_user)?' checked':''):'',
          'project_id'	=> $pro[$i]['project_id'],
          'node_style'	=> $node_style,
          'node_nr'		=> $i
        ));


        $link_data['project_id'] = $pro[$i]['project_id'];
        $link_data['pro_users']	= $pro[$i]['project_id'];
        $link_data['menuaction'] = 'projects.uistatistics.list_project_employees';
        $GLOBALS['phpgw']->template->set_var('user_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

        $link_data['menuaction'] = 'projects.uiprojects.view_project';
        $GLOBALS['phpgw']->template->set_var('view_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

        $link_data['menuaction'] = 'projects.uistatistics.project_stat';
        $GLOBALS['phpgw']->template->set_var('stat',$GLOBALS['phpgw']->link('/index.php',$link_data));
        $GLOBALS['phpgw']->template->set_var('lang_gantt_entry',lang('gantt chart'));
        $GLOBALS['phpgw']->template->set_var('lang_project_employees',lang('project employees'));
        $GLOBALS['phpgw']->template->set_var('lang_view_employees',lang('view employees'));

        $GLOBALS['phpgw']->template->set_var('employee_list','');
        $GLOBALS['phpgw']->template->set_var('users','');
        if(is_array($emps[$pro[$i]['project_id']]))
        {
          foreach($emps[$pro[$i]['project_id']] as $e)
          {
            //_debug_array($e);
            $GLOBALS['phpgw']->template->set_var('emp_name',$e['account_fullname']);
            $GLOBALS['phpgw']->template->set_var('emp_role',$e['role_name']);
            $GLOBALS['phpgw']->template->fp('users','user_list',True);
          }
          $GLOBALS['phpgw']->template->set_var('lang_name',lang('name'));
          $GLOBALS['phpgw']->template->set_var('lang_role',lang('role'));
          $GLOBALS['phpgw']->template->fp('employee_list','user_cols',True);
        }
        $GLOBALS['phpgw']->template->fp('list','projects_list',True);
      }

// ------------------------- end record declaration ------------------------

      $GLOBALS['phpgw']->template->set_var('lang_view_gantt',lang('view gantt chart'));
      $GLOBALS['phpgw']->template->set_var('lang_view_users',lang('view users'));

      $this->save_sessiondata('pstat');
      $GLOBALS['phpgw']->template->set_var('cols','');
      $GLOBALS['phpgw']->template->pfp('out','projects_list_t',True);
    }



    function coordinator_format($employee = '')
    {
      if (! $employee)
      {
        $employee = $this->account;
      }

      $employees = $this->boprojects->employee_list();

      while (list($null,$account) = each($employees))
      {
        $coordinator_list .= '<option value="' . $account['account_id'] . '"';
        if($account['account_id'] == $employee)
        $coordinator_list .= ' selected';
        $coordinator_list .= '>' . $account['account_firstname'] . ' ' . $account['account_lastname']
                    . ' [ ' . $account['account_lid'] . ' ]' . '</option>' . "\n";
      }
      return $coordinator_list;
    }

    function list_users()
    {
      $values	= $_POST['values'];

      $pro_user = array();
      if(is_array($values['account_id']))
      {
        $i = 0;
        foreach($values['account_id'] as $a_id => $val)
        {
          $pro_user[$i] = $a_id;
          $i++;
        }
      }

      //$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('User statistics')
      //												. $this->admin_header_info();
      //$this->display_app_header();
      $this->ui_base->display_app_header();

      $GLOBALS['phpgw']->template->set_file(array('user_list_t' => 'stats_userlist.tpl'));
      $GLOBALS['phpgw']->template->set_block('user_list_t','user_list','list');
      $GLOBALS['phpgw']->template->set_block('user_list_t','pro_list','pro');
      $GLOBALS['phpgw']->template->set_block('user_list_t','pro_cols','cols');


      $link_data = array
      (
        'menuaction'	=> 'projects.uistatistics.list_users',
        'action'		=> 'ustat'
      );

      $GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
      $GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(1));

      if (!$this->start)
      {
        $this->start = 0;
      }

      $users = $this->bostatistics->get_users('accounts', $this->start, $this->sort, $this->order, $this->query);

// ------------- nextmatch variable template-declarations -------------------------------

      $left = $this->nextmatchs->left('/index.php',$this->start,$this->bostatistics->total_records,$link_data);
      $right = $this->nextmatchs->right('/index.php',$this->start,$this->bostatistics->total_records,$link_data);
      $GLOBALS['phpgw']->template->set_var('left',$left);
      $GLOBALS['phpgw']->template->set_var('right',$right);

      $GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->bostatistics->total_records,$this->start));

// ------------------------ end nextmatch template --------------------------------------

// --------------- list header variable template-declarations ---------------------------

      $GLOBALS['phpgw']->template->set_var('sort_lid',$this->nextmatchs->show_sort_order($this->sort,'account_lid',$this->order,'/index.php',lang('Username'),$link_data));
      $GLOBALS['phpgw']->template->set_var('sort_firstname',$this->nextmatchs->show_sort_order($this->sort,'account_firstname',$this->order,'/index.php',lang('Firstname'),$link_data));
      $GLOBALS['phpgw']->template->set_var('sort_lastname',$this->nextmatchs->show_sort_order($this->sort,'account_lastname',$this->order,'/index.php',lang('Lastname'),$link_data));

// ------------------------- end header declaration -------------------------------------

      for ($i=0;$i<count($users);$i++)
      {
        if(in_array($users[$i]['account_id'],$pro_user))
        {
          $pro[$users[$i]['account_id']] = $this->boprojects->get_employee_projects($users[$i]['account_id']);
        }

        $this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

// --------------------- template declaration for list records ---------------------------

        $GLOBALS['phpgw']->template->set_var(array
        (
          'lid'			=> $users[$i]['account_lid'],
          'firstname'		=> $users[$i]['account_firstname'],
          'lastname'		=> $users[$i]['account_lastname'],
          'radio_checked'	=> (in_array($users[$i]['account_id'],$pro_user)?' checked':''),
          'account_id'	=> $users[$i]['account_id']
        ));

        $GLOBALS['phpgw']->template->set_var('project_list','');
        $GLOBALS['phpgw']->template->set_var('pro','');
        if(is_array($pro[$users[$i]['account_id']]))
        {
          foreach($pro[$users[$i]['account_id']] as $p)
          {
            $GLOBALS['phpgw']->template->set_var('pro_name',$p['pro_name']);
            $GLOBALS['phpgw']->template->fp('pro','pro_list',True);
          }
          $GLOBALS['phpgw']->template->set_var('lang_name',lang('name'));
          $GLOBALS['phpgw']->template->fp('project_list','pro_cols',True);
        }

        $GLOBALS['phpgw']->template->set_var('lang_view_projects',lang('view projects'));
        $GLOBALS['phpgw']->template->fp('list','user_list',True);
      }

// ------------------------------- end record declaration ---------------------------------

      $GLOBALS['phpgw']->template->pfp('out','user_list_t',True);
      $this->save_sessiondata($action);
    }

	function user_stat()
    {
    	$submit		= isset($_REQUEST['submit']) ? $_REQUEST['submit'] : '';
    	$values		= isset($_REQUEST['values']) ? $_REQUEST['values'] : '';
    	$account_id	= isset($_REQUEST['values']) ? $_REQUEST['values'] : '';

		$link_data = array
		(
			'menuaction'	=> 'projects.uistatistics.user_stat',
			'action'		=> 'ustat',
			'account_id'	=> $account_id
		);

      	if (! $account_id)
      	{
      		$phpgw->redirect_link('/index.php',array('menuaction'=>'projects.uistatistics.list_users','action'=>'ustat'));
      	}

      	// $GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('User statistics')
		//											. $this->admin_header_info();
		//$this->display_app_header();
		$this->ui_base->display_app_header();

		$GLOBALS['phpgw']->template->set_file(array('user_stat_t' => 'stats_userstat.tpl'));
		$GLOBALS['phpgw']->template->set_block('user_stat_t','user_stat','stat');

		$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));

		$cached_data = $this->boprojects->cached_accounts($account_id);
		$employee = $GLOBALS['phpgw']->strip_html($cached_data[$account_id]['firstname']
        			                                . ' ' . $cached_data[$account_id]['lastname'] . ' ['
                    			                    . $cached_data[$account_id]['account_lid'] . ' ]');

		$GLOBALS['phpgw']->template->set_var('employee',$employee);

		//$this->nextmatchs->alternate_row_color($GLOBALS['phpgw']->template);
		$this->nextmatchs->alternate_row_class($GLOBALS['phpgw']->template);

		if (!$values['sdate'])
		{
			$values['smonth']	= 0;
			$values['sday']		= 0;
			$values['syear']	= 0;
		}
		else
		{
			$values['smonth']	= date('m',$values['sdate']);
			$values['sday']		= date('d',$values['sdate']);
			$values['syear']	= date('Y',$values['sdate']);
		}

		if (!$values['edate'])
		{
			$values['emonth']	= 0;
			$values['eday']		= 0;
			$values['eyear']	= 0;
		}
		else
		{
			$values['emonth']	= date('m',$values['edate']);
			$values['eday']		= date('d',$values['edate']);
			$values['eyear']	= date('Y',$values['edate']);
		}

		$GLOBALS['phpgw']->template->set_var('start_date_select',$GLOBALS['phpgw']->common->dateformatorder($this->sbox->getYears('values[syear]',$values['syear']),
												$this->sbox->getMonthText('values[smonth]',$values['smonth']),
												$this->sbox->getDays('values[sday]',$values['sday'])));
		$GLOBALS['phpgw']->template->set_var('end_date_select',$GLOBALS['phpgw']->common->dateformatorder($this->sbox->getYears('values[eyear]',$values['eyear']),
												$this->sbox->getMonthText('values[emonth]',$values['emonth']),
												$this->sbox->getDays('values[eday]',$values['eday'])));

// -------------- calculate statistics --------------------------

		$GLOBALS['phpgw']->template->set_var('billed','<input type="checkbox" name="values[billed]" value="True"' . ($values['billed'] == 'private' ? ' checked' : '') . '>');

		$pro = $this->bostatistics->get_userstat_pro($account_id, $values);

		if (is_array($pro))
		{
			while (list($null,$userpro) = each($pro))
			{
				$summin = 0;
				$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);
				$GLOBALS['phpgw']->template->set_var('e_project',$GLOBALS['phpgw']->strip_html($userpro['title']) . ' [' . $GLOBALS['phpgw']->strip_html($userpro['num']) . ']');
				$GLOBALS['phpgw']->template->set_var('e_activity','&nbsp;');
				$GLOBALS['phpgw']->template->set_var('e_hours','&nbsp;');
				$GLOBALS['phpgw']->template->fp('stat','user_stat',True);

				$hours = $this->bostatistics->get_stat_hours('both', $account_id, $userpro['project_id'], $values);
				for ($i=0;$i<=count($hours);$i++)
				{
					if ($hours[$i]['num'] != '')
					{
						$GLOBALS['phpgw']->template->set_var('e_project','&nbsp;');
						$GLOBALS['phpgw']->template->set_var('e_activity',$GLOBALS['phpgw']->strip_html($hours[$i]['descr']) . ' [' . $GLOBALS['phpgw']->strip_html($hours[$i]['num']) . ']');
						$summin += $hours[$i]['min'];
						$hrs = intval($hours[$i]['min']/60) . ':' . sprintf ("%02d",(int)($hours[$i]['min']-intval($hours[$i]['min']/60)*60));
						$GLOBALS['phpgw']->template->set_var('e_hours',$hrs);
						$GLOBALS['phpgw']->template->fp('stat','user_stat',True);
					}
				}

				$GLOBALS['phpgw']->template->set_var('e_project','&nbsp;');
				$GLOBALS['phpgw']->template->set_var('e_activity','&nbsp;');
				$hrs = intval($summin/60) . ':' . sprintf ("%02d",(int)($summin-intval($summin/60)*60));
				$GLOBALS['phpgw']->template->set_var('e_hours',$hrs);
				$GLOBALS['phpgw']->template->fp('stat','user_stat',True);
			}
		}

		$allhours = $this->bostatistics->get_stat_hours('account', $account_id, $project_id ='', $values);

		$summin=0;
		$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);
		$GLOBALS['phpgw']->template->set_var('e_project','<b>' . lang('Overall') . '</b>');
		$GLOBALS['phpgw']->template->set_var('e_activity','&nbsp;');
		$GLOBALS['phpgw']->template->set_var('e_hours','&nbsp;');
		$GLOBALS['phpgw']->template->fp('stat','user_stat',True);

		if (is_array($allhours))
		{
			while (list($null,$userall) = each($allhours))
			{
			$GLOBALS['phpgw']->template->set_var('e_project','&nbsp;');
			$GLOBALS['phpgw']->template->set_var('e_activity',$GLOBALS['phpgw']->strip_html($userall['descr']) . ' [' . $GLOBALS['phpgw']->strip_html($userall['num']) . ']');
			$summin += $userall['min'];
			$hrs = intval($userall['min']/60) . ':' . sprintf ("%02d",(int)($userall['min']-intval($userall['min']/60)*60));
			$GLOBALS['phpgw']->template->set_var('e_hours',$hrs);
			$GLOBALS['phpgw']->template->fp('stat','user_stat',True);
			}
		}

		$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);
		$GLOBALS['phpgw']->template->set_var('e_project','<b>' . lang('Sum') . '</b>');
		$GLOBALS['phpgw']->template->set_var('e_activity','&nbsp;');
		$hrs = intval($summin/60) . ':' . sprintf ("%02d",(int)($summin-intval($summin/60)*60));
		$GLOBALS['phpgw']->template->set_var('e_hours',$hrs);
		$GLOBALS['phpgw']->template->fp('stat','user_stat',True);
		$GLOBALS['phpgw']->template->pfp('out','user_stat_t',True);
    }

	function show_stat( $project_id )
	{
		$this->bostatistics->show_graph($project_id);
	}

	function get_screen_size()
	{
		$project_id	= get_var('project_id',array('GET','POST'));
		$start		= get_var('start',array('GET','POST'));
		$end		= get_var('end',array('GET','POST'));
		$action		= get_var('action',array('GET','POST'));
		$sessionid	= get_var('sessionid',array('GET','POST'));

		$link_data = array
		(
			'menuaction'	=> 'projects.uistatistics.project_gantt',
			'action'		=> $action,
			'project_id'	=> $project_id,
			'gantt_popup'	=> true,
			'start'			=> $start,
			'end'			=> $end
		);

		$GLOBALS['phpgw']->template->set_file(array('screen' => 'stats_gant_popup_intro.tpl'));

		$GLOBALS['phpgw']->template->set_var('sessionid',$sessionid);
		$GLOBALS['phpgw']->template->set_var('project_id',$project_id);
		$GLOBALS['phpgw']->template->set_var('action',$action);
		$GLOBALS['phpgw']->template->set_var('redirect_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

		$GLOBALS['phpgw']->template->pfp('out','screen');
	}

	function project_gantt()
	{
		$project_id		= get_var('project_id',array('GET','POST'));
		$sdate			= get_var('sdate',array('GET','POST'));
		$edate			= get_var('edate',array('GET','POST'));
		$start			= get_var('start',array('GET','POST'));
		$end			= get_var('end',array('GET','POST'));
		$gantt_popup	= get_var('gantt_popup',array('POST','GET'));
		$action			= get_var('action',array('POST','GET'));
		$parent			= get_var('parent',array('POST','GET'));
		$expandtree		= get_var('expandtree',array('POST','GET'));
		$screen_width	= get_var('screen_width',array('POST','GET'));
		$screen_height	= get_var('screen_height',array('POST','GET'));

		//echo 'WIDTH=' . $screen_width;
		//echo 'HEIGHT=' . $screen_height;
		//echo 'parent=' . $parent;
		//echo 'start=' . $start;
		//echo 'end=' . $end;

		if (! $project_id)
		{
			$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=>'projects.uistatistics.list_projects','action'=>'mains'));
		}
		else
		{
			$project_array = explode(',',$project_id);
		}

		if($parent > 0)
		{
			$this->bostatistics->save_gantt_data($parent,$expandtree);
		}

		$parent_array = $this->bostatistics->read_gantt_data();

		//_debug_array($parent_array);

		// $GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('gantt chart') . $this->admin_header_info();

		$jscal = CreateObject('phpgwapi.jscalendar');	// before phpgw_header() !!!

		if($gantt_popup)
		{
			$GLOBALS['phpgw']->template->set_file(array('project_stat' => 'stats_gant_popup.tpl'));
			$GLOBALS['phpgw']->template->set_block('project_stat','map','list');

			$GLOBALS['phpgw']->template->set_var('lang_show_chart',lang('show gantt chart'));
			$GLOBALS['phpgw']->template->set_var('lang_start_date',lang('Start Date'));
			$GLOBALS['phpgw']->template->set_var('lang_end_date',lang('End Date'));
			$gantt_popup = True;

			$GLOBALS['phpgw']->template->set_var('jscal_setup_src',$GLOBALS['phpgw']->link('/phpgwapi/js/jscalendar/jscalendar-setup.php'));
			$GLOBALS['phpgw']->template->set_var('server_root',$GLOBALS['phpgw_info']['server']['webserver_url']);
		}
		else
		{
			$this->ui_base->display_app_header();
			$GLOBALS['phpgw']->template->set_file(array('project_stat' => 'stats_gant.tpl'));
			$GLOBALS['phpgw']->template->set_block('project_stat','map','list');

			$gantt_popup = False;
		}

		$link_data = array
		(
			'menuaction'	=> 'projects.uistatistics.project_gantt',
			'action'		=> $action,
			'project_id'	=> $project_id,
			'gantt_popup'	=> $gantt_popup,
			'screen_width'	=> $screen_width,
			'screen_height'	=> $screen_height
		);

		if(is_array($sdate))
		{
			$start_array	= $jscal->input2date($sdate['str']);
			$start_val		= $start_array['raw'];
		}
		elseif($start)
		{
			$start_val = $start;
		}

		if(is_array($edate))
		{
			$end_array	= $jscal->input2date($edate['str']);
			$end_val	= $end_array['raw'];
		}
		elseif($end)
		{
			$end_val = $end;
		}

		$start	= $start_val?$start_val:mktime(12,0,0,date('m'),date('d'),date('Y'));
		$end	= $end_val?$end_val:mktime(12,0,0,date('m'),date('d')+30,date('Y'));

		$GLOBALS['phpgw']->template->set_var('start',$start);
		$GLOBALS['phpgw']->template->set_var('end',$end);

		$GLOBALS['phpgw']->template->set_var('sdate_select',$jscal->input('sdate[str]',$start));
		$GLOBALS['phpgw']->template->set_var('edate_select',$jscal->input('edate[str]',$end));

		$GLOBALS['phpgw']->template->set_var('css_file',$GLOBALS['phpgw_info']['server']['webserver_url'] . SEP . 'phpgwapi' . SEP . 'templates'
		                          . SEP . 'idots' . SEP . 'css' . SEP . 'idots.css');
		$GLOBALS['phpgw']->template->set_var('gantt_link',$GLOBALS['phpgw']->link('/index.php',array
																								(
																									'menuaction'=>'projects.uistatistics.get_screen_size',
		  																							'action'=> $action,
																		                            'project_id'=> $project_id,
																		                            'gantt_popup'=>'True',
																		                            'start'=>$start,
																		                            'end'=> $end
																								)));

		$GLOBALS['phpgw']->template->set_var('project_id',$project_id);
		$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

		//_debug_array($project_array);

		$gantt_data = $this->bostatistics->show_graph( array
		(
			'project_array'	=> $project_array,
			'sdate'			=> $start,
			'edate'			=> $end,
			'width'			=> $screen_width,
			'height'		=> $screen_height,
			'gantt_popup'	=> $gantt_popup,
			'parent_array'	=> $parent_array,
			'viewtype'		=> 'planned'
		));

		$GLOBALS['phpgw']->template->set_var('lang_close_window', lang('close window'));
		$GLOBALS['phpgw']->template->set_var('lang_show_gantt_in_new_window', lang('show gantt chart in new window'));
		$GLOBALS['phpgw']->template->set_var('pix_src', $GLOBALS['phpgw_info']['server']['webserver_url'] . SEP . 'phpgwapi' . SEP . 'images' . SEP . $gantt_data['img_file']);

		//_debug_array($gantt_data);

		for($i=(count($gantt_data['img_map'])-1);$i>=0;--$i)
		{
			$GLOBALS['phpgw']->template->set_var('coords',implode(',',$gantt_data['img_map'][$i]['img_map']));

			$link_data['start'] = $start;
			$link_data['end'] = $end;
			$link_data['parent'] = $gantt_data['img_map'][$i]['project_id'];
			$link_data['expandtree'] = (in_array($gantt_data['img_map'][$i]['project_id'],$parent_array)?'del':'add');
			$GLOBALS['phpgw']->template->set_var('gantt_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->fp('list','map',True);
		}
		$GLOBALS['phpgw']->template->pfp('out','project_stat');
	}

    /*function project_stat()
    {
      $submit		= get_var('submit',array('POST'));
      $values		= get_var('values',array('POST','GET'));
      $project_id	= get_var('project_id',array('POST','GET'));
      $action		= get_var('action',array('POST','GET'));

      $link_data = array
      (
        'menuaction'	=> 'projects.uistatistics.project_stat',
        'action'		=> $action,
        'project_id'	=> $project_id
      );

      if (! $project_id)
      {
        $GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=>'projects.uistatistics.list_projects','action'=>'mains'));
      }

      $GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('project statistic');
      $this->display_app_header();

      $GLOBALS['phpgw']->template->set_file(array('project_stat' => 'stats_projectstat.tpl'));
      $GLOBALS['phpgw']->template->set_block('project_stat','stat_list','list');

      $nopref = $this->boprojects->check_prefs();
      if (is_array($nopref))
      {
        $GLOBALS['phpgw']->template->set_var('pref_message',$GLOBALS['phpgw']->common->error_list($nopref));
      }
      else
      {
        $prefs = $this->boprojects->get_prefs();
      }

      $pro = $this->boprojects->read_single_project($project_id);

      $GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));

      $title = $GLOBALS['phpgw']->strip_html($pro['title']);
      if (! $title) $title = '&nbsp;';
      $GLOBALS['phpgw']->template->set_var('project',$title . ' [' . $GLOBALS['phpgw']->strip_html($pro['number']) . ']');
      $GLOBALS['phpgw']->template->set_var('status',lang($pro['status']));
      $GLOBALS['phpgw']->template->set_var('budget',$pro['budget']);
      $GLOBALS['phpgw']->template->set_var('currency',$prefs['currency']);

      $GLOBALS['phpgw']->template->set_var('lang_account',lang('Account'));
      $GLOBALS['phpgw']->template->set_var('lang_activity',lang('Activity'));
      $GLOBALS['phpgw']->template->set_var('lang_hours',lang('Hours'));

      if (!$values['sdate'])
      {
        if (! $pro['sdate'] || $pro['sdate'] == 0)
        {
          $values['smonth']	= 0;
          $values['sday']		= 0;
          $values['syear']	= 0;
        }
        else
        {
          $values['smonth']	= date('m',$pro['sdate']);
          $values['sday']		= date('d',$pro['sdate']);
          $values['syear']	= date('Y',$pro['sdate']);
        }
      }
      else
      {
        $values['smonth']	= date('m',$values['sdate']);
        $values['sday']		= date('d',$values['sdate']);
        $values['syear']	= date('Y',$values['sdate']);
      }

      if (!$values['edate'])
      {
        if (! $pro['edate'] || $pro['edate'] == 0)
        {
          $values['emonth']	= 0;
          $values['eday']		= 0;
          $values['eyear']	= 0;
        }
        else
        {
          $values['emonth']	= date('m',$pro['edate']);
          $values['eday']		= date('d',$pro['edate']);
          $values['eyear']	= date('Y',$pro['edate']);
        }
      }
      else
      {
        $values['emonth']	= date('m',$values['edate']);
        $values['eday']		= date('d',$values['edate']);
        $values['eyear']	= date('Y',$values['edate']);
      }

      $GLOBALS['phpgw']->template->set_var('start_date_select',$GLOBALS['phpgw']->common->dateformatorder($this->sbox->getYears('values[syear]',$values['syear']),
                                              $this->sbox->getMonthText('values[smonth]',$values['smonth']),
                                              $this->sbox->getDays('values[sday]',$values['sday'])));
      $GLOBALS['phpgw']->template->set_var('end_date_select',$GLOBALS['phpgw']->common->dateformatorder($this->sbox->getYears('values[eyear]',$values['eyear']),
                                              $this->sbox->getMonthText('values[emonth]',$values['emonth']),
                                              $this->sbox->getDays('values[eday]',$values['eday'])));

      $cached_data = $this->boprojects->cached_accounts($pro['coordinator']);
      $coordinatorout = $GLOBALS['phpgw']->strip_html($cached_data[$pro['coordinator']]['account_lid']
                                        . ' [' . $cached_data[$pro['coordinator']]['firstname'] . ' '
                                        . $cached_data[$pro['coordinator']]['lastname'] . ' ]');
      $GLOBALS['phpgw']->template->set_var('coordinator',$coordinatorout);

      if ($pro['customer'] != 0)
      {
        $customer = $this->boprojects->read_single_contact($pro[$i]['customer']);
              if ($customer[0]['org_name'] == '')
        {
          $GLOBALS['phpgw']->template->set_var('customer',$customer[0]['n_given'] . ' ' . $customer[0]['n_family']);
        }
              else
        {
          $GLOBALS['phpgw']->template->set_var('customer',$customer[0]['org_name'] . ' [ ' . $customer[0]['n_given'] . ' ' . $customer[0]['n_family'] . ' ]');
        }
      }
      else
      {
        $GLOBALS['phpgw']->template->set_var('customer','&nbsp;');
      }

      $GLOBALS['phpgw']->template->set_var('billed','<input type="checkbox" name="values[billed]" value="True"'
                    . ($values['billed'] == 'private'?' checked':'') . '>');

// -------------------------------- calculate statistics -----------------------------------------

      $employees = $this->bostatistics->get_employees($project_id, $values);

      if (is_array($employees))
      {
        while (list($null,$employee) = each($employees))
        {
          $account_data = $this->boprojects->cached_accounts($employee['employee']);
          $this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

          $account_id = $account_data[$employee['employee']]['account_id'];

          $summin = 0;
          $GLOBALS['phpgw']->template->set_var('e_account',$GLOBALS['phpgw']->strip_html($account_data[$employee['employee']]['firstname']) . ' '
                      . $GLOBALS['phpgw']->strip_html($account_data[$employee['employee']]['lastname']) . ' ['
                      . $GLOBALS['phpgw']->strip_html($account_data[$employee['employee']]['account_lid']) . ']');

          $GLOBALS['phpgw']->template->set_var('e_activity','&nbsp;');
          $GLOBALS['phpgw']->template->set_var('e_hours','&nbsp;');
          $GLOBALS['phpgw']->template->fp('list','stat_list',True);

          $hours = $this->bostatistics->get_stat_hours('both', $account_id, $project_id, $values);

          for ($i=0;$i<=count($hours);$i++)
          {
            if ($hours[$i]['num'] != '')
            {
              $GLOBALS['phpgw']->template->set_var('e_account','&nbsp;');
              $GLOBALS['phpgw']->template->set_var('e_activity',$GLOBALS['phpgw']->strip_html($hours[$i]['descr']) . ' ['
                            . $GLOBALS['phpgw']->strip_html($hours[$i]['num']) . ']');
              $hrs = intval($hours[$i]['min']/60). ':' . sprintf ("%02d",(int)($hours[$i]['min']-intval($hours[$i]['min']/60)*60));
              $GLOBALS['phpgw']->template->set_var('e_hours',$hrs);
              $summin += $hours[$i]['min'];
              $GLOBALS['phpgw']->template->fp('list','stat_list',True);
            }
          }

          $GLOBALS['phpgw']->template->set_var('e_account','&nbsp;');
          $GLOBALS['phpgw']->template->set_var('e_activity','&nbsp;');
          $sumhours = intval($summin/60). ':' . sprintf ("%02d",(int)($summin-intval($summin/60)*60));
          $GLOBALS['phpgw']->template->set_var('e_hours',$sumhours);
          $GLOBALS['phpgw']->template->fp('list','stat_list',True);
        }
      }

      $prohours = $this->bostatistics->get_stat_hours('project', $account_id = '', $project_id, $values);

      $summin=0;
      $this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);
      $GLOBALS['phpgw']->template->set_var('e_account','<b>' . lang('Overall') . '</b>');
      $GLOBALS['phpgw']->template->set_var('e_activity','&nbsp;');
      $GLOBALS['phpgw']->template->set_var('e_hours','&nbsp;');

      $GLOBALS['phpgw']->template->fp('list','stat_list',True);

      if (is_array($prohours))
      {
        while (list($null,$proall) = each($prohours))
        {
          $GLOBALS['phpgw']->template->set_var('e_account','&nbsp;');
          $GLOBALS['phpgw']->template->set_var('e_activity',$GLOBALS['phpgw']->strip_html($proall['descr']) . ' ['
                        . $GLOBALS['phpgw']->strip_html($proall['num']) . ']');
          $summin += $proall['min'];
          $hrs = intval($proall['min']/60). ':' . sprintf ("%02d",(int)($proall['min']-intval($proall['min']/60)*60));
          $GLOBALS['phpgw']->template->set_var('e_hours',$hrs);

          $GLOBALS['phpgw']->template->fp('list','stat_list',True);
        }
      }
      $this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);
      $GLOBALS['phpgw']->template->set_var('e_account','<b>' . lang('sum') . '</b>');
      $GLOBALS['phpgw']->template->set_var('e_activity','&nbsp;');
      $hrs = intval($summin/60). ':' . sprintf ("%02d",(int)($summin-intval($summin/60)*60));
      $GLOBALS['phpgw']->template->set_var('e_hours',$hrs);

      $GLOBALS['phpgw']->template->fp('list','stat_list',True);
      $GLOBALS['phpgw']->template->pfp('out','project_stat');
    }*/

	function list_users_worktimes()
	{
		$jscal = CreateObject('phpgwapi.jscalendar');	// before phpgw_header() !!!

		$values = get_var('values',array('POST','GET'));
		$sdate  = get_var('sdate',array('POST','GET'));
		$edate  = get_var('edate',array('POST','GET'));

		if($values['sdate'] || $values['edate'])
		{
			$GLOBALS['phpgw']->session->appsession('session_data', 'projectsStartDate', $jscal->input2date($values['sdate']));
			$GLOBALS['phpgw']->session->appsession('session_data', 'projectsEndDate',  $jscal->input2date($values['edate']));
		}

		if(!$sdate && !$edate)
		{
			$sdateSession =  $GLOBALS['phpgw']->session->appsession('session_data','projectsStartDate');
			$sdate = $sdateSession['raw'];

			$edateSession =  $GLOBALS['phpgw']->session->appsession('session_data','projectsEndDate');
			$edate = $edateSession['raw'];
		}

		//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('User statistics')
		//												. $this->admin_header_info();
		//$this->display_app_header();
		$this->ui_base->display_app_header();

		$GLOBALS['phpgw']->template->set_file(array('user_list_worktimes_t' => 'stats_userlist_worktimes.tpl'));
		$GLOBALS['phpgw']->template->set_block('user_list_worktimes_t','pro_list','pro');
		$GLOBALS['phpgw']->template->set_block('user_list_worktimes_t','worktime_list','work');
		$GLOBALS['phpgw']->template->set_block('user_list_worktimes_t','posible_sum','posible');

		$link_data = array
		(
			'menuaction' => 'projects.uistatistics.list_users_worktimes',
			'action'     => 'ustat'
		);

		$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
		$GLOBALS['phpgw']->template->set_var('lang_workhours',lang('work hours'));
		$GLOBALS['phpgw']->template->set_var('lang_workhours_project', lang('Project'));
		$GLOBALS['phpgw']->template->set_var('lang_workhours_journey', lang('travel time'));
		$GLOBALS['phpgw']->template->set_var('lang_workhours_sum', lang('Sum'));
		$GLOBALS['phpgw']->template->set_var('lang_update', lang('update'));

		if(!isset($values['employee']))
		{
			$values['employee'] = $GLOBALS['phpgw_info']['user']['account_id'];
		}

// ---------- employee selectbox variable template-declarations ---------------------

		if ($this->boprojects->isprojectadmin('pad') || $this->boprojects->isprojectadmin('pmanager'))
		{
			$employees = $this->boprojects->read_projects_acl();
			$employees_list = array();

			while (is_array($employees) && (list($no_use,$account_id) = each($employees)))
			{
				$GLOBALS['phpgw']->accounts->get_account_name($account_id,$lid,$fname,$lname);
				if(!$fname && !$lname)
				continue;
				$employees_list[$account_id] = $GLOBALS['phpgw']->common->display_fullname($lid,$fname,$lname);
			}

			if(count($employees_list))
			{
				asort($employees_list);
				reset($employees_list);
			}

			$select_employee_list = "<select name=\"values[employee]\" size=\"1\">\n";
			$select_employee_list .= '<option value="0">'.lang('all')."</option>\n";
			while (list($account_id,$account_name) = each($employees_list))
			{
				$select_employee_list .= '<option value="' . $account_id . '"';
				if ($values['employee'] == $account_id)
				{
					$select_employee_list .= ' selected';
				}
				$select_employee_list .= '>'.$account_name."</option>\n";
			}
			$select_employee_list .= '</select>';
		}
		else
		{
			// show only current user
			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$GLOBALS['phpgw']->accounts->get_account_name($account_id,$lid,$fname,$lname);
			$select_employee_list = '<input type="hidden" name="values[employee]" value="'.$account_id.'">'. $GLOBALS['phpgw']->common->display_fullname($lid,$fname,$lname);
		}

		$GLOBALS['phpgw']->template->set_var('select_employee_list', $select_employee_list);

// --------------- end employee selectbox template ---------------------------------

// ------------ action selectbox variable template-declarations ---------------------

		$select_action_list = "<select name=\"values[stat_action]\" size=\"1\">\n";

		$actions = array(
			'pro_all'      => lang('all'),
			'pro_direct'   => lang('direct work'),
			'pro_indirect' => lang('indirect work')
		);

		while(list($stat, $lang_stat) = each($actions))
		{
			$select_action_list .= '<option value="'.$stat.'"';
			if($values['stat_action'] == $stat)
			{
				$select_action_list .= ' selected';
			}
			$select_action_list .= '>'.$lang_stat."</option>\n";
		}
		$select_action_list .= '</select>';

		$GLOBALS['phpgw']->template->set_var('select_action_list', $select_action_list);

// --------------------- end action selectbox template ------------------------------

// ------------- jscal variable template-declarations -------------------------------

      if($values['sdate'])
      {
        $start_array = $jscal->input2date($values['sdate']);
        //$start_val   = $start_array['raw'];
        $start_val   = mktime(0,0,0,$start_array['month'],$start_array['day'],$start_array['year']);
      }
      elseif($sdate)
      {
        $start_val = $sdate;
      }
      else
      {
        $start_val = false;
      }

      if($values['edate'])
      {
        $end_array = $jscal->input2date($values['edate']);
        //$end_val   = $end_array['raw'];
        $end_val   = mktime(23,59,59,$end_array['month'],$end_array['day'],$end_array['year']);
      }
      elseif($edate)
      {
        $end_val = $edate;
      }
      else
      {
        $end_val = false;
      }

      $start = $start_val?$start_val:mktime(0,0,0,date('m'),1,date('Y'));
      $end   = $end_val?$end_val:mktime(23,59,59,date('m')+1,0,date('Y'));

      $GLOBALS['phpgw']->template->set_var('sdate_select',$jscal->input('values[sdate]',$start));
      $GLOBALS['phpgw']->template->set_var('edate_select',$jscal->input('values[edate]',$end));

// ------------------------ end jscal template --------------------------------------

      if(isset($values['employee']) && ($values['employee']>0))
      {
        $GLOBALS['phpgw']->template->set_var('info_1', lang('projects'));
        $GLOBALS['phpgw']->template->set_var('info_1_1', lang('title'));
        $GLOBALS['phpgw']->template->set_var('info_1_2', lang('number'));

        $worktimes = $this->boprojects->get_emp_worktimes($values['employee'], $start, $end);

        // filter
        $project_list = array();
        for($i=0; $i<count($worktimes['projects']); ++$i)
        {
          $project_id = $worktimes['projects'][$i];
          $project = $worktimes[$project_id]['project_data'];

          switch($values['stat_action'])
          {
            case 'pro_direct':
              if($project['project_direct'] != 'Y')
              {
                continue 2;
              }
            break;
            case 'pro_indirect':
              if($project['project_direct'] != 'N')
              {
                continue 2;
              }
            break;
            case 'pro_all': // fall down
            default:
            break;
          }

          $main_project_id = $project['project_main'];
          if(!isset($project_list[$main_project_id]))
          {
            $main = $this->boprojects->read_single_project($main_project_id);
            if(!$main)
            {
              continue;
            }

            $project_list[$main_project_id] = array();
            $project_list[$main_project_id]['project_title']        = $main['title'];
            $project_list[$main_project_id]['project_number']       = $main['number'];
            $project_list[$main_project_id]['sum_minutes_worktime'] = 0;
            $project_list[$main_project_id]['sum_minutes_journey']  = 0;
            $project_list[$main_project_id]['sum_minutes_all']      = 0;
          }

          $project_list[$main_project_id]['sum_minutes_worktime'] += $project['sum_minutes_worktime'];
          $project_list[$main_project_id]['sum_minutes_journey']  += $project['sum_minutes_journey'];
          $project_list[$main_project_id]['sum_minutes_all']      += $project['sum_minutes_all'];
        }

        $summary_sum_minutes_worktime = 0;
        $summary_sum_minutes_journey  = 0;
        $summary_sum_minutes_all      = 0;

        $action_url = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'projects.uiprojecthours.list_hours' , 'sdate' => $start, 'edate' => $end, 'employee' => $values['employee']));
        reset($project_list);
        while(list($project_id, $project) = each($project_list))
        {
          $summary_sum_minutes_worktime += $project['sum_minutes_worktime'];
          $summary_sum_minutes_journey  += $project['sum_minutes_journey'];
          $summary_sum_minutes_all      += $project['sum_minutes_all'];

          $project_hw  = $this->boprojects->format_minutes($project['sum_minutes_worktime']);
          $project_hj = $this->boprojects->format_minutes($project['sum_minutes_journey']);
          $project_hs = $this->boprojects->format_minutes($project['sum_minutes_all']);

          $link_url = $action_url.'&project_id='.$project_id;
          $GLOBALS['phpgw']->template->set_var('pro_name', '<a href="'.$link_url.'" title="'.lang('list activities').'">'.$project['project_title'].'</a>');
          $GLOBALS['phpgw']->template->set_var('pro_number', '<a href="'.$link_url.'" title="'.lang('list activities').'">'.$project['project_number'].'</a>');
          $GLOBALS['phpgw']->template->set_var('pro_hours', $project_hw);
          $GLOBALS['phpgw']->template->set_var('pro_hours_journey', $project_hj);
          $GLOBALS['phpgw']->template->set_var('pro_hours_sum', $project_hs);

          $GLOBALS['phpgw']->template->fp('pro','pro_list', true);
        }

        if(count($project_list) > 0)
        {
          $GLOBALS['phpgw']->template->fp('project_list','pro', true);
          $GLOBALS['phpgw']->template->set_var('pro', '');

          $GLOBALS['phpgw']->template->set_var('lang_summery', lang('Summary'));
          $GLOBALS['phpgw']->template->set_var('lang_summery_workhours_project', $this->boprojects->format_minutes($summary_sum_minutes_worktime));
          $GLOBALS['phpgw']->template->set_var('lang_summery_workhours_journey', $this->boprojects->format_minutes($summary_sum_minutes_journey));
          $GLOBALS['phpgw']->template->set_var('lang_summery_workhours_sum', $this->boprojects->format_minutes($summary_sum_minutes_all));
        }


        $prefs = CreateObject('phpgwapi.preferences', $values['employee']);
		$prefs->read_repository();

		$sbox = createobject('phpgwapi.sbox');
		$holidays = CreateObject('phpgwapi.calendar_holidays');

        $pref_country = $prefs->data['common']['country'];
		if(!$pref_country)
		{ // no user prefs
			$pref_country = $GLOBALS['phpgw']->preferences->data['common']['country'];
		}
		if(!$pref_country)
		{ // no predefined user prefs
			$pref_country = 'DE';
		}

		$pref_f_state = $prefs->data['common']['federalstate'];
		if(!$pref_f_state)
		{ // no user prefs
			$pref_f_state = $GLOBALS['phpgw']->preferences->data['common']['federalstate'];
		}
		if(!$pref_f_state)
		{ // no predefined user prefs
			$pref_f_state = 8; // Niedersachsen
		}

		$pref_religion = $prefs->data['common']['religion'];
		if(!$pref_religion)
		{ // no user prefs
			$pref_religion = $GLOBALS['phpgw']->preferences->data['common']['religion'];
		}
		if(!$pref_religion)
		{ // no predefined user prefs
			$pref_religion = 0; // Atheistisch
		}

//		$country = ucfirst($GLOBALS['phpgw']->translation->retranslate($sbox->country_array[$pref_country]));
		$country = ucfirst(lang($sbox->country_array[$pref_country]));

		$federal_state = $this->holidays->federal_states[$country][$pref_f_state]; // Achtung: bisher existiert nur germany!
		$religion = $this->holidays->religions[$pref_religion];

        $workdays = $holidays->get_number_of_workdays(date("d",$start),date("m",$start),date("Y",$start),date("d",$end),date("m",$end),date("Y",$end),$country,$federal_state,$religion);
        $GLOBALS['phpgw']->template->set_var('summery_workhours_posible', ($workdays*8));
        $GLOBALS['phpgw']->template->set_var('lang_summery_workhours_posible', lang('Posible workhours'));

		$GLOBALS['phpgw']->template->parse('ps_sum','posible_sum',True);

        $GLOBALS['phpgw']->template->fp('work','worktime_list', true);

      }

      if(isset($values['employee']) && ($values['employee']==0))
      {
        reset($employees_list);
        $summary_sum_minutes_worktime = 0;
        $summary_sum_minutes_journey  = 0;
        $summary_sum_minutes_all      = 0;

        $GLOBALS['phpgw']->template->set_var('info_1', lang('employees'));
        $GLOBALS['phpgw']->template->set_var('info_1_1', lang('name'));
        $GLOBALS['phpgw']->template->set_var('info_1_2', '');
        $GLOBALS['phpgw']->template->set_var('posible_sum', '');

        $link_data = array
        (
          'menuaction' => 'projects.uistatistics.list_users_worktimes',
          'action'     => 'ustat',
          'sdate'      => $start,
          'edate'      => $end
        );
        $action_url = $GLOBALS['phpgw']->link('/index.php',$link_data);

        while(list($emp_id, $emp_name) = each($employees_list))
        {
          $worktimes = $this->boprojects->get_emp_worktimes($emp_id, $start, $end);

          $summary_sum_minutes_worktime += $worktimes['sum_minutes_worktime'];
          $summary_sum_minutes_journey  += $worktimes['sum_minutes_journey'];
          $summary_sum_minutes_all      += $worktimes['sum_minutes_all'];

          $emp_hw = $this->boprojects->format_minutes($worktimes['sum_minutes_worktime']);
          $emp_hj = $this->boprojects->format_minutes($worktimes['sum_minutes_journey']);
          $emp_hs = $this->boprojects->format_minutes($worktimes['sum_minutes_all']);

          $GLOBALS['phpgw']->template->set_var('pro_name', '<a href="'.$action_url.'&values[employee]='.$emp_id.'">'.$emp_name.'</a>');
          $GLOBALS['phpgw']->template->set_var('pro_number', '');
          $GLOBALS['phpgw']->template->set_var('pro_hours', $emp_hw);
          $GLOBALS['phpgw']->template->set_var('pro_hours_journey', $emp_hj);
          $GLOBALS['phpgw']->template->set_var('pro_hours_sum', $emp_hs);

          $GLOBALS['phpgw']->template->fp('pro','pro_list', true);
        }

        if(count($employees_list) > 0)
        {
          $GLOBALS['phpgw']->template->fp('project_list','pro', true);
          $GLOBALS['phpgw']->template->set_var('pro', '');

          $GLOBALS['phpgw']->template->set_var('lang_summery', lang('Summary'));
          $GLOBALS['phpgw']->template->set_var('lang_summery_workhours_project', $this->boprojects->format_minutes($summary_sum_minutes_worktime));
          $GLOBALS['phpgw']->template->set_var('lang_summery_workhours_journey', $this->boprojects->format_minutes($summary_sum_minutes_journey));
          $GLOBALS['phpgw']->template->set_var('lang_summery_workhours_sum', $this->boprojects->format_minutes($summary_sum_minutes_all));
        }
        $GLOBALS['phpgw']->template->fp('work','worktime_list', true);
      }


      $GLOBALS['phpgw']->template->pfp('out','user_list_worktimes_t',True);
    }
  }
?>
