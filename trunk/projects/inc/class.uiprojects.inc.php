<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: class.uiprojects.inc.php,v 1.170 2006/12/05 19:40:45 sigurdne Exp $
	* $Source: /sources/phpgroupware/projects/inc/class.uiprojects.inc.php,v $
	*/

	class uiprojects
	{
		var $action;
		var $grants;
		var $start;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $status;
		var $ui_base;

		var $public_functions = array
		(
			'list_projects'				=> true,
			'list_projects_home'		=> true,
			'edit_project'				=> true,
			'delete_project'			=> true,
			'view_project'				=> true,
			//'abook'					=> true,
			//'accounts_popup'			=> true,
			//'e_accounts_popup'		=> true,
			'list_budget'				=> true,
			'project_mstones'			=> true,
			'assign_employee_roles'		=> true,
			'report'					=> true,
			'export_cost_accounting'	=> true,
			'export_cost_accounting_A'	=> true,
			'view_employee_activity'	=> true,
			'tree_view_projects'		=> true,
			'view_report_list'			=> true
		);

		function uiprojects()
		{
			$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

			$this->ui_base					= CreateObject('projects.uiprojects_base');
			$this->boprojects				= $this->ui_base->boprojects;
			$this->nextmatchs				= CreateObject('phpgwapi.nextmatchs');

			$this->attached_files			= CreateObject('projects.attached_files');
			$this->bohours					= CreateObject('projects.boprojecthours');
			$this->accounts					= CreateObject('phpgwapi.accounts');

			$this->start					= $this->boprojects->start;
			$this->query					= $this->boprojects->query;
			$this->filter					= $this->boprojects->filter;
			$this->order					= $this->boprojects->order;
			$this->sort						= $this->boprojects->sort;
			$this->cat_id					= $this->boprojects->cat_id;
			$this->status					= $this->boprojects->status;

			if( !is_object($GLOBALS['phpgw']->js) )
			{
				$GLOBALS['phpgw']->js = createObject('phpgwapi.javascript');
			}
			$GLOBALS['phpgw']->js->validate_file('tabs','tabs','phpgwapi');
			$GLOBALS['phpgw']->js->validate_file('core','popup','phpgwapi');

			if( !@is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}

			/*if( !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}*/

			$GLOBALS['phpgw']->css->validate_file('tabs','phpgwapi');
			$GLOBALS['phpgw']->css->validate_file('style','projects');
			$GLOBALS['phpgw']->css->validate_file('tooltip','phpgwapi');
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

		function priority_list($selected = 0)
		{
			for($i=1;$i<=10;$i++)
			{
				$list .= '<option value="' . $i . '"' . ($i == $selected?' SELECTED>':'>') . $i . '</option>';
			}
			return $list;
		}

		function list_projects()
		{
			$action			= isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
			$pro_main		= isset( $_REQUEST['pro_main'] ) ? $_REQUEST['pro_main'] : '';
			$this->cat_id	= isset( $_REQUEST['cat_id'] ) ? $_REQUEST['cat_id'] : '';

			$project_id		= ( !isset($project_id) ) ? $this->ui_base->project_id : 0;

			if( $project_id && !$pro_main )
			{
				$pro_main = $this->ui_base->pro_main;
			}

			if ( !$action )
			{
				$action = $this->ui_base->action;
			}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('list projects')
			//												. $this->admin_header_info();

			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file( array( 'projects_list_t' => 'list.tpl' ) );
			$GLOBALS['phpgw']->template->set_block( 'projects_list_t','projects_list','list' );
			$GLOBALS['phpgw']->template->set_block( 'projects_list_t','pro_sort_cols','sort_cols' );
			$GLOBALS['phpgw']->template->set_block( 'projects_list_t','pro_cols','cols' );
			$GLOBALS['phpgw']->template->set_block( 'projects_list_t','project_main','main' );

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.list_projects',
				'pro_main'		=> $pro_main,
				'action'		=> $action,
				'status'		=> $this->boprojects->status
			);

			$main = null;

			if( $pro_main )
			{
				$main = $this->boprojects->read_single_project( $pro_main );

				$GLOBALS['phpgw']->template->set_var( 'title_main', $main['title'] );
				$GLOBALS['phpgw']->template->set_var( 'main_url', $GLOBALS['phpgw']->link( '/index.php', array
				(
					'menuaction'	=> 'projects.uiprojects.view_project',
					'action'		=> 'mains',
					'project_id'	=> $pro_main
				)));

				$GLOBALS['phpgw']->template->set_var( 'coordinator_main', $main['coordinatorout'] );
				$GLOBALS['phpgw']->template->set_var( 'number_main', $main['number'] );
				$GLOBALS['phpgw']->template->set_var( 'customer_main',$main['customerout'] );
				$GLOBALS['phpgw']->template->set_var( 'customer_org_name',$main['customerorgout'] );
				$GLOBALS['phpgw']->template->set_var( 'url_main',$main['url'] );
				$GLOBALS['phpgw']->template->set_var( 'attachment',$this->attached_files->get_files( $pro_main ) );
				$GLOBALS['phpgw']->template->set_var( 'lang_files',lang( 'Files' ) );

				$link = $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'projects.uiprojects.report',
					'project_id'	=> $pro_main,
					'generated'		=> 'true'
				));

				$GLOBALS['phpgw']->template->set_var('report','<a href="' . $link . '"><img src="projects/templates/' . $GLOBALS['phpgw_info']['server']['template_set'] . '/images/document.png" title="' . lang('generate activity report') . '">' . lang('generate activity report') . '</a>');
				$GLOBALS['phpgw']->template->parse( 'main','project_main',true );
			}

			$pro = $this->boprojects->list_projects( array
			(
				'action' => $action,
				'parent' => $pro_main
			));

// --------------------- nextmatch variable template-declarations ------------------------

			$left = $this->nextmatchs->left('/index.php', $this->start, $this->boprojects->total_records, $link_data);
			$right = $this->nextmatchs->right('/index.php', $this->start, $this->boprojects->total_records, $link_data);
			$GLOBALS['phpgw']->template->set_var('left', $left);
			$GLOBALS['phpgw']->template->set_var('right', $right);

			$GLOBALS['phpgw']->template->set_var('lang_showing', $this->nextmatchs->show_hits($this->boprojects->total_records, $this->start));

// ------------------------- end nextmatch template --------------------------------------

			if ( $action == 'mains' )
			{
				$action_list= '<select name="cat_id" onChange="this.form.submit();"><option value="none">' . lang('Select category') . '</option>' . "\n"
							. $this->boprojects->cats->formatted_list('select','all',$this->cat_id,true) . '</select>';
			}
			else
			{
				$action_list= '<select name="pro_main" onChange="this.form.submit();"><option value="">' . lang('Select main project') . '</option>' . "\n"
							. $this->boprojects->select_project_list(array('status' => $this->status, 'selected' => $pro_main)) . '</select>';
			}

			$GLOBALS['phpgw']->template->set_var('action_list', $action_list);
			$GLOBALS['phpgw']->template->set_var('action_url', $GLOBALS['phpgw']->link('/index.php', $link_data));
			$GLOBALS['phpgw']->template->set_var('filter_list', $this->nextmatchs->new_filter($this->filter));
			$GLOBALS['phpgw']->template->set_var('search_list', $this->nextmatchs->search(array('query' => $this->query)));
			$GLOBALS['phpgw']->template->set_var('status_list', $this->ui_base->status_format($this->status));

// ---------------- list header variable template-declarations --------------------------

			$nopref = $this->boprojects->check_prefs();

			if ( is_array($nopref) )
			{
				$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($nopref));
			}

			$prefs = $this->boprojects->read_prefs();

			$GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('title'),$link_data));

			foreach( $prefs['columns'] as $col )
			{
				$col_align = '';

				switch( $col )
				{
					case 'number':
						$cname = lang('project id');
						$db = 'p_number';
						break;
					case 'priority':
						$cname = lang('priority');
						$col_align= 'right';
						break;
					case 'sdateout':
						$cname = lang('start date');
						$db = 'start_date';
						$col_align = 'center';
						break;
					case 'edateout':
						$cname = lang('date due');
						$db = 'end_date';
						$col_align= 'center';
						break;
					case 'phours':
						$cname = lang('time planned');
						$db = 'ptime';
						$col_align = 'right';
						break;
					case 'budget':
						$cname = $prefs['currency'] . ' ' . lang('budget');
						$col_align = 'right';
						break;
					case 'e_budget':
						$cname = $prefs['currency'] . ' ' . lang('extra budget');
						$col_align = 'right';
						break;
					case 'coordinatorout':
						$cname = lang('coordinator');
						$db = 'coordinator';
						break;
					case 'customerout':
						$cname = lang('customer');
						$db = 'customer';
						break;
					case 'investment_nr':
						$cname = lang('investment nr');
						break;
					case 'previousout':
						$cname = lang('previous');
						$db = 'previous';
						break;
					case 'customer_nr':
						$cname = lang('customer nr');
						break;
					case 'url':
						$cname = lang('url');
						break;
					case 'reference':
						$cname = lang('reference');
						break;
					case 'accountingout':
						$cname = lang('accounting');
						$db = 'accounting';
						break;
					case 'project_accounting_factor':
						$cname = $prefs['currency'] . ' ' . lang('project') . ' ' . lang('accounting factor') . ' ' . lang('per hour');
						$db = 'acc_factor';
						$col_align = 'right';
						break;
					case 'project_accounting_factor_d':
						$cname = $prefs['currency'] . ' ' . lang('project') . ' ' . lang('accounting factor') . ' ' . lang('per day');
						$db = 'acc_factor_d';
						$col_align = 'right';
						break;
					case 'billableout':
						$cname = lang('billable');
						$db = 'billable';
						$col_align = 'center';
						break;
					case 'psdateout':
						$cname = lang('start date planned');
						$db = 'psdate';
						$col_align= 'center';
						break;
					case 'pedateout':
						$cname = lang('date due planned');
						$db = 'pedate';
						$col_align = 'center';
						break;
					case 'discountout':
						$cname = lang('discount');
						$db = 'discount';
						$col_align= 'right';
						break;
					case 'salesmanagerout':
						$cname = lang('sales manager');
						$db = 'salesmanager';
						break;
				}

				$sort_column = ($col == 'mstones') ? lang('milestones') : $this->nextmatchs->show_sort_order($this->sort, (isset($db) ? $db : $col), $this->order, '/index.php', $cname ? $cname : lang($col), $link_data);

				/*if( $col=='mstones' )
				{
					$sort_column = lang('milestones');
				}
				else
				{
					$sort_column = $this->nextmatchs->show_sort_order($this->sort, (isset($db) ? $db : $col), $this->order, '/index.php', $cname ? $cname : lang($col), $link_data);
				}*/
				$GLOBALS['phpgw']->template->set_var('col_align', $col_align ? $col_align : 'left');
				$GLOBALS['phpgw']->template->set_var('sort_column', $sort_column);
				$GLOBALS['phpgw']->template->fp('sort_cols', 'pro_sort_cols', true);
			}

// -------------- end header declaration ---------------------------------------

			if( is_array($pro) )
			{
				foreach( $pro as $p )
				{
					echo "testing<br />";
					$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

// --------------- template declaration for list records -------------------------------------

					$link_data['menuaction'] = 'projects.uiprojects.tree_view_projects';
					$link_data['project_id'] = $p['project_id'];

					//if ($action == 'mains')
					//{

						$projects_url = $GLOBALS['phpgw']->link('/index.php', $link_data);
					//}
					/*else
					{
						$projects_url = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojecthours.list_hours',
																					'project_id'=> $p['project_id'],
																					'action'=>'hours',
																					'pro_main'=> $pro_main));
					}
					*/
					$GLOBALS['phpgw']->template->set_var( array
					(
						'title'			=> isset($p['title']) ? $p['title'] : lang('browse'),
						'projects_url'	=> $projects_url
					));

					$GLOBALS['phpgw']->template->set_var('pro_column','');

					foreach( $prefs['columns'] as $col )
					{
						switch($col)
						{
							case 'priority':
							case 'discountout':
							case 'e_budget':
							case 'budget':
							case 'project_accounting_factor':
							case 'project_accounting_factor_d':
							case 'phours':
								$col_align = 'right';
								break;
							case 'sdateout':
							case 'edateout':
							case 'psdateout':
							case 'pedateout':
							case 'billableout':
								$col_align = 'center';
								break;
							default:
								$col_align = 'left';
						}

						$GLOBALS['phpgw']->template->set_var('col_align', $col_align);
						$GLOBALS['phpgw']->template->set_var('column', $p[$col]);
						$GLOBALS['phpgw']->template->fp('pro_column', 'pro_cols', true);
					}
					//$GLOBALS['phpgw']->template->set_var('pro_column',$pdata);

					$edit = '';
					if ( !$this->boprojects->edit_perms( array
					(
						'action'		=> $action,
						'coordinator'	=> $p['coordinator'],
						'main'			=> $p['main'],
						'parent'		=> $p['parent']
					)))
					{
						$edit = 'no';
					}

					$link_data['menuaction'] = 'projects.uiprojects.view_project';
					$GLOBALS['phpgw']->template->set_var('view_url', $GLOBALS['phpgw']->link('/index.php',$link_data));
					$GLOBALS['phpgw']->template->set_var('view_img', $GLOBALS['phpgw']->common->image('phpgwapi','view'));

					$link_data['menuaction'] = 'projects.uiprojects.edit_project';
					$GLOBALS['phpgw']->template->set_var('edit_url',($edit == 'no' ? '' : $GLOBALS['phpgw']->link('/index.php',$link_data)));
					$GLOBALS['phpgw']->template->set_var('edit_img',($edit == 'no' ? '' : '<img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','edit') . '" title="' . lang('edit')
																				. '" border="0">'));

					if ($this->boprojects->add_perms( array
					(
						'action'		=> $action,
						'coordinator'	=> $p['coordinator'],
						'main_co'		=> $main['coordinator'],
						'parent'		=> $p['parent']
					)))
					{
						$GLOBALS['phpgw']->template->set_var('add_job_url',$GLOBALS['phpgw']->link('/index.php', array
															(
																'menuaction'	=>'projects.uiprojects.edit_project',
																'action'		=>'subs',
																'pro_parent'	=> $p['project_id'],
																'pro_main'		=>(isset($pro_main) && $pro_main ? $pro_main : $p['project_id'])
															)));
						$GLOBALS['phpgw']->template->set_var('add_job_img','<img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','new') . '" title="' . lang('add sub project') . '" border="0">');
					}

					$GLOBALS['phpgw']->template->fp('list', 'projects_list', true);
				}
			}
// ------------------------- end record declaration ------------------------

// --------------- Button interactions --------------------------
			$valid_interactions = $this->boprojects->get_interactions( array
			(
				'pro_main'    => $pro_main,
				'project_id'  => isset($p['project_id']) ? $p['project_id'] : '',
				'status'      => $this->status,
				'action'      => $action,
				'coordinator' => $main['coordinator']
			));

			if( in_array('book_hours', $valid_interactions) )
			{
				$link_data['menuaction']	= 'projects.uiprojecthours.edit_hours';
				$link_data['action']		= 'hours';
				$link_data['project_id']	= $pro_main;

				$GLOBALS['phpgw']->template->set_var('addhours','<form method="POST" action="'
											. $GLOBALS['phpgw']->link('/index.php',$link_data)
											. '"><input type="submit" name="addhours" value="'
											. lang('Add work hours to the main project')
											.'"></form>');

				$link_data['menuaction']	= 'projects.uiprojecthours.list_hours';
				$link_data['action']		= 'hours';
				$link_data['project_id']	= $pro_main;

				$GLOBALS['phpgw']->template->set_var('viewhours','<form method="POST" action="'
											. $GLOBALS['phpgw']->link('/index.php',$link_data)
											. '"><input type="submit" name="viewhours" value="'
											. lang('View work hours of the main project')
											.'"></form>');
			}

			unset($link_data['project_id']); // not a good idea

			if( in_array('add_project', $valid_interactions) )
			{
				$link_data['menuaction'] = 'projects.uiprojects.edit_project';
				if( $action == 'subs' )
				{
					$link_data['action']		= 'subs';
					$link_data['pro_parent']	= $pro_main;
					$link_data['pro_main']		= $pro_main;

					$add_desc = lang('add sub project');
				}
				else
				{
					$add_desc = lang('Add project');
				}

				$GLOBALS['phpgw']->template->set_var('add','<form method="POST" action="'
											. $GLOBALS['phpgw']->link('/index.php',$link_data)
											. '"><input type="submit" name="Add" value="'
											. $add_desc
											.'"></form>');
			}

			if( $pro_main && ( ( in_array('view_employee_activity', $valid_interactions) ) || $this->boprojects->isprojectadmin('pad') || $this->boprojects->isprojectadmin('pmanager') ) )
			{
				$link_data['menuaction'] = 'projects.uiprojects.view_employee_activity';
				$link_data['project_id'] = $pro_main;

				$GLOBALS['phpgw']->template->set_var('viewemployeeactivity','<form method="POST" action="'
											. $GLOBALS['phpgw']->link('/index.php',$link_data)
											. '"><input type="submit" name="addhours" value="'
											. lang('View project activities')
											.'"></form>');
			}

// ---------------------------------------------------

			$this->save_sessiondata($action);
			$GLOBALS['phpgw']->template->pfp('out', 'projects_list_t', true);
		}

		function tree_view_projects()
		{
			$project_id	= get_var('project_id', array('POST','GET'));
			$pro_main	= get_var('pro_main',   array('POST','GET'));
			$pro_parent	= get_var('pro_parent', array('POST','GET'));
			$action		= get_var('action',     array('POST','GET'));

			$this->ui_base->display_app_header();

			if( $project_id && !$pro_parent )
			{
				$pro_parent = $this->ui_base->pro_parent;
			}

			if( $project_id && !$pro_main )
			{
				$pro_main = $this->ui_base->pro_main;
			}

			if( !$action )
			{
				if( $pro_parent > 0 )
				{
					$action = 'subs';
				}
				else
				{
					$action = 'mains';
				}
			}

			$GLOBALS['phpgw']->template->set_file(array('projects_list_t' => 'list_tree.tpl'));
			$GLOBALS['phpgw']->template->set_block('projects_list_t', 'projects_list', 'list');
			$GLOBALS['phpgw']->template->set_block('projects_list_t', 'pro_sort_cols', 'sort_cols');
			$GLOBALS['phpgw']->template->set_block('projects_list_t', 'pro_cols', 'cols');
			$GLOBALS['phpgw']->template->set_block('projects_list_t', 'project_main', 'main');

			$link_data = array
			(
				'status'		=> $this->boprojects->status,
				'menuaction'	=> 'projects.uiprojects.tree_view_projects',
				'project_id'	=> $project_id
			);

/*
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
				$GLOBALS['phpgw']->template->set_var('customer_org_name',$main['customerorgout']);
				$GLOBALS['phpgw']->template->set_var('url_main',$main['url']);
				$GLOBALS['phpgw']->template->set_var('attachment',$this->attached_files->get_files($pro_main));
				$GLOBALS['phpgw']->template->set_var('lang_files',lang('Files'));
				$link = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.report',
																	'project_id'=> $pro_main,
																	'generated'=>1));
				$GLOBALS['phpgw']->template->set_var('report','<a href="' . $link . '"><img src="projects/templates/' . $GLOBALS['phpgw_info']['server']['template_set'] . '/images/document.png" title="' . lang('generate activity report') . '">' . lang('generate activity report') . '</a>');
				$GLOBALS['phpgw']->template->parse('main','project_main',true);
			}
*/
			$this->boprojects->status = false; // workaround for full tree view support
			$pro = $this->boprojects->list_projects( array
			(
				'action' => 'mainsubsorted',
				'project_id' => $project_id,
				'limit' => false
			));

// --------------------- nextmatch variable template-declarations ------------------------
/*
			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boprojects->total_records,$this->start));
*/
// ------------------------- end nextmatch template --------------------------------------

			//$action_list= '<select name="cat_id" onChange="this.form.submit();"><option value="none">' . lang('Select category') . '</option>' . "\n"
			//			. $this->boprojects->cats->formatted_list('select','all',$this->cat_id,true) . '</select>';
/*
			if($pro_main)
			{
				$cat_id = $this->boprojects->return_value('cat', $pro_main);
				$action_list = lang('category').': '.$this->boprojects->cats->id2name($cat_id);
				$action_list = '<input style="border: solid 2px #d0d0d0;" readonly="readonly" size="60" type="text" value="&nbsp;'.$action_list.'">';
			}

			$GLOBALS['phpgw']->template->set_var('action_list',$action_list);
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			//$GLOBALS['phpgw']->template->set_var('filter_list',$this->nextmatchs->new_filter($this->filter));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
*/
			//workaround for full tree view support
			//$status_list = '<select name="status" onChange="this.form.submit();">'.$this->ui_base->status_format($this->status).'</select>';
			//$GLOBALS['phpgw']->template->set_var('status_list',$status_list);

// ---------------- list header variable template-declarations --------------------------

			$nopref = $this->boprojects->check_prefs();

			if ( is_array($nopref) )
			{
				$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($nopref));
			}

			$prefs = $this->boprojects->read_prefs();

			$GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('title'),$link_data));

			foreach( $prefs['columns'] as $col )
			{
				$col_align = '';

				switch( $col )
				{
					case 'number':
						$cname	= lang('project id');
						$db		= 'p_number';
						break;
					case 'priority':
						$cname		= lang('priority');
						$col_align	= 'right';
						break;
					case 'sdateout':
						$cname		= lang('start date');
						$db			= 'start_date';
						$col_align	= 'center';
						break;
					case 'edateout':
						$cname = lang('date due');
						$db = 'end_date';
						$col_align= 'center';
						break;
					case 'phours':
						$cname = lang('time planned');
						$db = 'ptime';
						$col_align= 'right';
						break;
					case 'budget':
						$cname		= $prefs['currency'] . ' ' . lang('budget');
						$col_align	= 'right';
						break;
					case 'e_budget':
						$cname		= $prefs['currency'] . ' ' . lang('extra budget');
						$col_align	= 'right';
						break;
					case 'coordinatorout':
						$cname	= lang('coordinator');
						$db		= 'coordinator';
						break;
					case 'salesmanagerout':
						$cname	= lang('sales manager');
						// TODO: Finn - huh???
						break;
						$db		= 'salesmanager';
						break;
					case 'customerout':
						$cname	= lang('customer');
						// TODO: Finn - huh???
						break;
						$db		= 'customer';
						break;
					case 'investment_nr':
						$cname = lang('investment nr');
						break;
					case 'previousout':
						$cname	= lang('previous');
						$db		= 'previous';
						break;
					case 'customer_nr':
						$cname = lang('customer nr');
						break;
					case 'url':
						$cname = lang('url');
						break;
					case 'reference':
						$cname = lang('reference');
						break;
					case 'accountingout':
						$cname	= lang('accounting');
						$db		= 'accounting';
						break;
					case 'project_accounting_factor':
						$cname		= $prefs['currency'] . ' ' . lang('project') . ' ' . lang('accounting factor') . ' ' . lang('per hour');
						$db			= 'acc_factor';
						$col_align	= 'right';
						break;
					case 'project_accounting_factor_d':
						$cname		= $prefs['currency'] . ' ' . lang('project') . ' ' . lang('accounting factor') . ' ' . lang('per day');
						$db			= 'acc_factor_d';
						$col_align 	= 'right';
						break;
					case 'billableout':
						$cname		= lang('billable');
						$db			= 'billable';
						$col_align	= 'center';
						break;
					case 'psdateout':
						$cname		= lang('start date planned');
						$db			= 'psdate';
						$col_align	= 'center';
						break;
					case 'pedateout':
						$cname		= lang('date due planned');
						$db			= 'pedate';
						$col_align	= 'center';
						break;
					case 'discountout':
						$cname		= lang('discount');
						$db			= 'discount';
						$col_align	= 'right';
						break;
				}

				if ($col=='mstones')
				{
					$sort_column = lang('milestones');
				}
				else
				{
					$sort_column = $this->nextmatchs->show_sort_order($this->sort,($db?$db:$col),$this->order,'/index.php',$cname?$cname:lang($col),$link_data);
				}
				$GLOBALS['phpgw']->template->set_var('col_align',$col_align?$col_align:'left');
				$GLOBALS['phpgw']->template->set_var('sort_column',$sort_column);
				$GLOBALS['phpgw']->template->fp('sort_cols','pro_sort_cols',true);
			}

// -------------- end header declaration ---------------------------------------

			if(is_array($pro))
			{
				foreach($pro as $p)
				{
					$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

// --------------- template declaration for list records -------------------------------------

					$link_data['project_id'] = $p['project_id'];
					$link_data['action']     = $p['parent']>0?'subs':'mains';

					$projects_url = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojecthours.list_hours',
																				'project_id'=> $p['project_id'],
																				'action'=>'hours',
																				'pro_main'=>$p['main']));

					$GLOBALS['phpgw']->template->set_var(array
					(
						'title'			=> $p['title']?$p['title']:lang('browse'),
						'projects_url'	=> $projects_url
					));

					$GLOBALS['phpgw']->template->set_var('pro_column','');
					foreach($prefs['columns'] as $col)
					{
						switch($col)
						{
							case 'priority':
							case 'discountout':
							case 'e_budget':
							case 'budget':
							case 'project_accounting_factor':
							case 'project_accounting_factor_d':
							case 'phours': $col_align = 'right'; break;
							case 'sdateout':
							case 'edateout':
							case 'psdateout':
							case 'pedateout':
							case 'billableout': $col_align = 'center'; break;
							default:			$col_align = 'left';
						}

						$GLOBALS['phpgw']->template->set_var('col_align',$col_align);
						$GLOBALS['phpgw']->template->set_var('column',$p[$col]);
						$GLOBALS['phpgw']->template->fp('pro_column','pro_cols',true);
					}
					//$GLOBALS['phpgw']->template->set_var('pro_column',$pdata);


					if($p['parent'] == 0)
					{
						$action='mains';
					}
					else
					{
						$action='subs';
						$pro_main = $p['main'];
					}

					$edit = '';
					if (!$this->boprojects->edit_perms(array('action' => $action,'coordinator' => $p['coordinator'],
														'main' => $p['main'], 'parent' => $p['parent'])))
					{
						$edit = 'no';
					}

					$link_data['action']     = $action;
					$link_data['pro_main']   = $p['main'];
					$link_data['pro_parent'] = $p['parent'];

					$link_data['menuaction'] = 'projects.uiprojects.view_project';
					$GLOBALS['phpgw']->template->set_var('view_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
					$GLOBALS['phpgw']->template->set_var('view_img',$GLOBALS['phpgw']->common->image('phpgwapi','view'));

					$link_data['menuaction'] = 'projects.uiprojects.edit_project';
					$GLOBALS['phpgw']->template->set_var('edit_url',($edit=='no'?'':$GLOBALS['phpgw']->link('/index.php',$link_data)));
					$GLOBALS['phpgw']->template->set_var('edit_img',($edit=='no'?'':'<img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','edit') . '" title="' . lang('edit')
																				. '" border="0">'));

					if ($this->boprojects->add_perms(array('action' => $action,'coordinator' => $p['coordinator'],
														'main_co' => $main['coordinator'],'parent' => $p['parent'])))
					{
						$GLOBALS['phpgw']->template->set_var('add_job_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.edit_project',
																														'action'=>'subs',
																														'pro_parent'=> $p['project_id'],
																														'pro_main'=> (isset($pro_main) && $pro_main?$pro_main:$p['project_id']))));
						$GLOBALS['phpgw']->template->set_var('add_job_img','<img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','new') . '" title="' . lang('add sub project')
																		. '" border="0">');
					}
					$GLOBALS['phpgw']->template->fp('list','projects_list',true);
				}
			}
// ------------------------- end record declaration ------------------------

			$this->save_sessiondata($action);
			$GLOBALS['phpgw']->template->pfp('out','projects_list_t',true);
		}

		function list_projects_home()
		{
			$body			= '';
			$action			= isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
			$pro_main		= isset( $_REQUEST['pro_main'] ) ? $_REQUEST['pro_main'] : '';
			$this->cat_id	= isset( $_REQUEST['cat_id'] ) ? $_REQUEST['cat_id'] : '';

			$this->boprojects->cats->app_name = 'projects';

			$this->t = CreateObject('phpgwapi.Template', $GLOBALS['phpgw']->common->get_tpl_dir('projects'));

			$this->t->set_file( array
			(
				'projects_list_t' => 'home_list.tpl'
			));

			$this->t->set_block('projects_list_t', 'projects_list', 'list');

			//$this->t->set_var('th_bg', $GLOBALS['phpgw_info']['theme']['th_bg']);

			$this->t->set_var('th_bg', $GLOBALS['phpgw_info']['user']['preferences']['common']['theme']['th_bg']);

			//_debug_array($GLOBALS['phpgw_info']);

			if ( !$action )
			{
				$action = 'mains';
			}

			$sdate	= isset( $_REQUEST['sdate'] ) ? $_REQUEST['sdate'] : '';
			$edate	= isset( $_REQUEST['edate'] ) ? $_REQUEST['edate'] : '';
			$psdate	= isset( $_REQUEST['psdate'] ) ? $_REQUEST['psdate'] : '';
			$pedate	= isset( $_REQUEST['pedate'] ) ? $_REQUEST['pedate'] : '';

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.list_projects_home',
				'pro_main'		=> $pro_main,
				'action'		=> $action
			);

			$this->status = 'active';

			//$this->boprojects->filter = 'anonym';

			$pro = $this->boprojects->list_projects( array
			(
				'action' => $action,
				'parent' => $pro_main
			));

// --------------------- nextmatch variable template-declarations ------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$this->t->set_var('left',$left);
			$this->t->set_var('right',$right);

			$this->t->set_var('lang_showing',$this->nextmatchs->show_hits($this->boprojects->total_records,$this->start));

// ------------------------- end nextmatch template --------------------------------------

			if ($action == 'mains')
			{
				$action_list= '<select name="cat_id" onChange="this.form.submit();"><option value="none">' . lang('Select category') . '</option>' . "\n"
							. $this->boprojects->cats->formatted_list('select','all',$this->cat_id,true) . '</select>';
				$this->t->set_var('lang_action',lang('sub projects'));
			}
			else
			{
				$action_list= '<select name="pro_main" onChange="this.form.submit();"><option value="">' . lang('Select main project') . '</option>' . "\n"
							. $this->boprojects->select_project_list(array('status' => $this->status,'selected' => $pro_main,'filter' => 'anonym')) . '</select>';
				$this->t->set_var('lang_action',lang('Work hours'));
			}

			$this->t->set_var('action_list',$action_list);
			$this->t->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$this->t->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
			$this->t->set_var('status_list',$this->ui_base->status_format($this->status,False));

// ---------------- list header variable template-declarations --------------------------

			$this->t->set_var('sort_number',$this->nextmatchs->show_sort_order($this->sort,'p_number',$this->order,'/index.php',lang('Project ID'),$link_data));
			$this->t->set_var('lang_milestones',lang('milestones'));
			$this->t->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('Title'),$link_data));
			$this->t->set_var('sort_end_date',$this->nextmatchs->show_sort_order($this->sort,'end_date',$this->order,'/index.php',lang('Date due'),$link_data));
			$this->t->set_var('sort_coordinator',$this->nextmatchs->show_sort_order($this->sort,'coordinator',$this->order,'/index.php',lang('Coordinator'),$link_data));

// -------------- end header declaration ---------------------------------------

			for ($i=0;$i<count($pro);$i++)
			{
				$this->nextmatchs->template_alternate_row_class($this->t);

				if ($action == 'mains')
				{
					$td_action  = ($pro[$i]['customerorgout']?$pro[$i]['customerorgout'].'&nbsp;':'');
					$td_action .= ($pro[$i]['customerout']?$pro[$i]['customerout']:'&nbsp;');
				}
				else
				{
					$td_action = ($pro[$i]['sdateout']?$pro[$i]['sdateout']:'&nbsp;');
				}

				if ($pro[$i]['level'] > 0)
				{
					$space = '&nbsp;.&nbsp;';
					$spaceset = str_repeat($space,$pro[$i]['level']);
				}

// --------------- template declaration for list records -------------------------------------

				if ($action == 'mains')
				{
					$projects_url = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.list_projects_home',
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

				$this->t->set_var( array
				(
					'number'		=> $pro[$i]['number'],
					'milestones'	=> isset($pro[$i]['mstones']) ? $pro[$i]['mstones'] : '&nbsp;',
					'title'			=> $spaceset . $pro[$i]['title'] ? $pro[$i]['title'] : '&nbsp;',
					'projects_url'	=> $projects_url,
					'end_date'		=> $pro[$i]['edateout'],
					'coordinator'	=> $pro[$i]['coordinatorout']
				));

				$link_data['project_id'] = $pro[$i]['project_id'];
				$link_data['menuaction'] = 'projects.uiprojects.view_project';
				$this->t->set_var('view', $GLOBALS['phpgw']->link('/index.php', $link_data));
				$this->t->set_var('lang_view_entry', lang('View'));
				$body .= $this->t->fp('list', 'projects_list', true);
			}

// ------------------------- end record declaration ------------------------

			$this->save_sessiondata($action);

			$body .= $this->t->fp('out','projects_list_t',true);
			return $body;
		}

		function edit_project()
		{
			if(!is_object($GLOBALS['phpgw']->js))
			{
				$GLOBALS['phpgw']->js = createObject('phpgwapi.javascript');
			}
			$GLOBALS['phpgw']->js->validate_file('api', 'tabs');

			$jscal = CreateObject('phpgwapi.jscalendar');
			$cssTooltip = CreateObject('phpgwapi.csstooltip');

			$action          = get_var('action',array('GET','POST'));
			$pro_main        = get_var('pro_main',array('GET','POST'));
			$pro_parent      = get_var('pro_parent',array('GET','POST'));
			$book_activities = get_var('book_activities',array('POST'));
			$bill_activities = get_var('bill_activities',array('POST'));
			$project_id      = get_var('project_id',array('GET','POST'));
			$name            = get_var('name',array('POST'));
			$values          = get_var('values',array('POST'));
			$sdate           = get_var('sdate',array('GET','POST'));
			$edate           = get_var('edate',array('GET','POST'));
			$psdate          = get_var('psdate',array('GET','POST'));
			$pedate          = get_var('pedate',array('GET','POST'));
			$budgetradio     = get_var('budgetradio',array('GET','POST'));

			if($pro_parent === '')
			{
				$pro_parent = $this->boprojects->return_value('parent', $project_id);
			}

			if(!$action)
			{
				if($pro_parent > 0)
				{
					$action = 'subs';
				}
				else
				{
					$action = 'mains';
				}
			}

			// only 'Y' would be submitted because it is a checkbox and
			// not checked values wouldnt submitted
			if(!isset($values['plan_bottom_up']))
			{
				// differ project typ
				if($action == 'mains')
				{
					$plan_bottom_up = 'N';
				}
				else
				{ // use parent setting
					$plan_bottom_up = $this->boprojects->return_value('plan_bottom_up', $pro_parent);
				}
			}
			else
			{
				$plan_bottom_up = $values['plan_bottom_up'];
			}

			if(!isset($values['cat']) && $pro_parent>0)
			{
				$pro_main = $this->boprojects->return_value('main', $pro_parent);
				$pro_main_data = $this->boprojects->read_single_project($pro_main);
				$values['cat'] = $pro_main_data['cat'];
			}

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.tree_view_projects',
				'pro_main'		=> $pro_main,
				'action'      => $action,
				'project_id'	=> $project_id,
				'pro_parent'	=> $pro_parent
			);

			if($_POST['mstone'])
			{
				$link_data['menuaction'] = 'projects.uiprojects.project_mstones';
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if($_POST['roles'])
			{
				$link_data['menuaction'] = 'projects.uiprojects.assign_employee_roles';
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if(is_array($sdate))
			{
				$start_array = $jscal->input2date($sdate['str']);
				$start_val   = $start_array['raw'];
			}

			if(is_array($edate))
			{
				$end_array = $jscal->input2date($edate['str']);
				$end_val   = $end_array['raw'];
			}

			if(is_array($psdate))
			{
				$pstart_array = $jscal->input2date($psdate['str']);
				$pstart_val   = $pstart_array['raw'];
			}

			if(is_array($pedate))
			{
				$pend_array = $jscal->input2date($pedate['str']);
				$pend_val   = $pend_array['raw'];
			}

			if ($_POST['save'] || $_POST['apply'])
			{
				//$this->cat_id = ($values['cat'] ? $values['cat'] : ''); // disable because no reason for change selected cat when add/edit project
				$values['billable']  		= isset($values['not_billable']) ? false : true;
				$values['coordinator']  = $_POST['accountid'];
				$values['salesmanager'] = $_POST['salesmanagerid'];
				$values['employees']    = array_merge($_POST['employees'], array($_POST['accountid']));
				$values['project_id']   = $project_id;
				$values['customer']     = $_POST['abid'];
				$values['customer_org'] = $_POST['customer_org'];
				$values['book_activities'] = $book_activities;
				$values['bill_activities'] = $bill_activities;
				$values['sdate']  = $start_val;
				$values['edate']  = $end_val;
				$values['psdate'] = $pstart_val;
				$values['pedate'] = $pend_val;
				$values['plan_bottom_up'] = $plan_bottom_up;

				$old_values = $this->boprojects->read_single_project($project_id);
				if(is_array($old_values))
				{
					$values['ptime'] = $values['ptime'] + $old_values['ptime_childs'];
					$values['budget'] = $values['budget'] + $old_values['budget_childs'];
					$values['e_budget'] = $values['e_budget'] + $old_values['e_budget_childs'];
				}

				switch($budgetradio)
				{
					case 'm': $values['budgetradio'] = 'm'; break;
					case 'h': $values['budgetradio'] = 'h'; break;
					default : $values['budgetradio'] = 'm'; break;
				}

				if ($values['accounting'] == 'project')
				{
					if ($values['project_accounting_factor'] || $values['project_accounting_factor_d'])
					{
						switch($values['radio_acc_factor'])
						{
							case 'day': // only $values['project_accounting_factor'] submitted
								$values['project_accounting_factor_d'] = $values['project_accounting_factor'];
								$values['project_accounting_factor'] = $values['project_accounting_factor'] / $this->boprojects->siteconfig['hwday'];
							break;
							default:
								$values['project_accounting_factor_d'] = $values['project_accounting_factor'] * $this->boprojects->siteconfig['hwday'];
							break;
						}

						if(($values['budgetradio'] == 'm') && ($values['project_accounting_factor'] > 0))
						{
							$values['ptime'] = intval($values['budget'] / $values['project_accounting_factor']);
						}
						elseif($values['budgetradio'] == 'h')
						{
							$values['budget'] = intval($values['ptime']) * $values['project_accounting_factor'];
						}
						else
						{
							$values['ptime'] = 0;
							$values['budget'] = 0.0;
						}
					}
				}

				$error = $this->boprojects->check_values($action, $values);
				if (is_array($error))
				{
					$message = $GLOBALS['phpgw']->common->error_list($error);
				}
				else
				{
					$project_id = $this->boprojects->save_project($action, $values);
					$this->attached_files->save_file($project_id);
					$link_data['project_id'] = $project_id;
					if($_POST['save'])
					{
						unset($jscal);
						$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
					}
					else
					{
						$message = lang('project %1 has been saved',$values['title']);
					}
					$values = $this->boprojects->read_single_project($project_id);
				}
			}

			if($_POST['cancel'])
			{
				if(!$project_id)
				{
					$link_data['project_id'] = $pro_parent;
				}

				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if($_POST['delete'])
			{
				$link_data['menuaction'] = 'projects.uiprojects.delete_project';
				$link_data['pa_id'] = $project_id;
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if($project_id)
			{
				if(!is_array($values))
				{
					$values = $this->boprojects->read_single_project($project_id);
				}

				if(!is_array($values))
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
				}
			}

/*
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($project_id?lang('edit project'):lang('add project'))
															. $this->admin_header_info();
			$this->display_app_header();
*/
			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('edit_form' => 'form.tpl'));
			$GLOBALS['phpgw']->template->set_block('edit_form','main','mainhandle');

			$GLOBALS['phpgw']->template->set_block('edit_form','clist','clisthandle');
			$GLOBALS['phpgw']->template->set_block('edit_form','cfield','cfieldhandle');

			$GLOBALS['phpgw']->template->set_block('edit_form','elist','elisthandle');
			$GLOBALS['phpgw']->template->set_block('edit_form','efield','efieldhandle');

			$GLOBALS['phpgw']->template->set_block('edit_form','accounting_act','accounting_acthandle');
			$GLOBALS['phpgw']->template->set_block('edit_form','accounting_own','accounting_ownhandle');

			$nopref = $this->boprojects->check_prefs();
			if (is_array($nopref) && !$_POST['save'] && !$_POST['apply'])
			{
				$message .= $GLOBALS['phpgw']->common->error_list($nopref);
			}

			$GLOBALS['phpgw']->template->set_var('message',$message);
			$prefs = $this->boprojects->read_prefs();

			$GLOBALS['phpgw']->template->set_var('addressbook_link',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'phpgwapi.pbaddbook_projects.show',
			                                                                                                    'hidecc' => 1,
			                                                                                                    'hidebcc' => 1,
			                                                                                                    'targettagto' => 'customer'
			                                                                                                   )));
			$GLOBALS['phpgw']->template->set_var('accounts_link',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'phpgwapi.pbaddbookaccount_projects.show',
			                                                                                                 'hidecc' => 1,
			                                                                                                 'hidebcc' => 1,
			                                                                                                 'targettagto' => 'cordinator'
			                                                                                                 )));
			$GLOBALS['phpgw']->template->set_var('e_accounts_link',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'phpgwapi.pbaddbookaccounts.show',
			                                                                                                   'hidecc' => 1,
			                                                                                                   'hidebcc' => 1,
			                                                                                                   'targettagto' => 'staff'
			                                                                                                  )));
			$GLOBALS['phpgw']->template->set_var('s_accounts_link',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'phpgwapi.pbaddbookaccount_projects.show',
			                                                                                                   'hidecc' => 1,
			                                                                                                   'hidebcc' => 1,
			                                                                                                   'targettagto' => 'salesmanager'
			                                                                                                  )));

			if($pro_main)
			{
				$main = $this->boprojects->read_single_project($pro_main,'planned');
			}

			if(!$pro_parent && is_array($main) || ($pro_main == $pro_parent && is_array($main)))
			{
				$parent = $main;
			}

			if($pro_parent && !is_array($parent))
			{
				$parent = $this->boprojects->read_single_project($pro_parent,'planned');
			}

			if(!isset($values['plan_bottom_up']))
			{
				$values['plan_bottom_up'] = 'N';
			}

			if ($project_id)
			{
				$values_save = $values; // store the values because it includes some calculated values before check_values (budget, time)
				$values = $this->boprojects->read_single_project($project_id);

				$values['ptime'] = $values['ptime'] - $values['ptime_childs'];
				$values['budget'] = $values['budget'] - $values['budget_childs'];
				$values['e_budget'] = $values['e_budget'] - $values['e_budget_childs'];

				if(($_POST['save'] || $_POST['apply']) && $error)
				{
					$values['coordinator']                = $_POST['accountid'];
					$values['employees']                  = $_POST['employees'];
					$values['customer']                   = $_POST['abid'];
					$values['customer_org']               = $_POST['customer_org'];
					$values['salesmanager']               = $_POST['salesmanagerid'];
					$values['book_activities']            = $book_activities;
					$values['bill_activities']            = $bill_activities;
					$values['number']                     = $_POST['values']['number'];
					$values['investment_nr']              = $_POST['values']['investment_nr'];
					$values['title']                      = $_POST['values']['title'];
					$values['descr']                      = $_POST['values']['descr'];
					$values['previous']                   = $_POST['values']['previous'];
					$values['cat']                        = $_POST['values']['cat'];
					$values['status']                     = $_POST['values']['status'];
					$values['priority']                   = $_POST['values']['priority'];
					$values['url']                        = $_POST['values']['url'];
					$values['access']                     = $_POST['values']['access'];
					$values['reference']                  = $_POST['values']['reference'];
					$values['customer_nr']                = $_POST['values']['customer_nr'];
					$values['ptime']                      = $values_save['ptime']; // use calculated value
					$values['plan_bottom_up']             = $_POST['values']['plan_bottom_up'];
					$values['budget']                     = $values_save['budget']; // use calculated value
					$values['e_budget']                   = $_POST['values']['e_budget'];
					$values['project_accounting_factor']  = $_POST['values']['project_accounting_factor'];
					$values['direct_work']                = $_POST['values']['direct_work'];

					// map not_billable field to billable after edit form submit
					if(isset($_POST['values']['not_billable']) && $_POST['values']['not_billable'])
					{
						$values['billable'] = 'N';
					}
					else
					{
						$values['billable'] = 'Y';
					}

					$values['inv_method']                 = $_POST['values']['inv_method'];
					$values['discount_type']              = $_POST['values']['discount_type'];
					$values['discount']                   = $_POST['values']['discount'];
					$values['result']                     = $_POST['values']['result'];
					$values['test']                       = $_POST['values']['test'];
					$values['quality']                    = $_POST['values']['quality'];
					$values['attachment']                 = $_POST['values']['attachment'];
				}

				$GLOBALS['phpgw']->template->set_var('old_status',$values['status']);
				//$GLOBALS['phpgw']->template->set_var('old_parent',$values['parent']);
				$GLOBALS['phpgw']->template->set_var('old_parent',$pro_parent);
				$GLOBALS['phpgw']->template->set_var('old_edate',$values['edate']);
				$GLOBALS['phpgw']->template->set_var('old_coordinator',$values['coordinator']);

				if($this->boprojects->siteconfig['projectnr'] == 'generate')
				{
					$GLOBALS['phpgw']->template->set_var('choose','<input type="checkbox" name="values[choose]" value="True"' . (isset($values['choose'])?' checked':'') . '>');
					$GLOBALS['phpgw']->template->set_var('lang_choose',lang('generate project id'));
					$GLOBALS['phpgw']->template->set_var('help_img','');
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('lang_choose','');
					$GLOBALS['phpgw']->template->set_var('choose','');
					$GLOBALS['phpgw']->template->set_var('help_img','<a href="#"><img src="' . $GLOBALS['phpgw']->common->image('projects','help') . '" onclick="open_popup(\''
													. $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects_base.proid_help_popup')) . '\');" title="'
													. lang('help') . '" alt="Project-Nr." /></a>');
				}

				//$this->cat_id = $values['cat'];

				$start	= $start_val?$start_val:($values['sdate']?$values['sdate']:'');
				$end	= $end_val?$end_val:($values['edate']?$values['edate']:'');
				$pstart	= $pstart_val?$pstart_val:($values['psdate']?$values['psdate']:mktime(0,0,0,date('m', time()), date('d', time()), date('Y', time())) );
				$pend	= $pend_val?$pend_val:($values['psdate']?$values['pedate']:'');

				$GLOBALS['phpgw']->template->set_var('lang_milestones',lang('milestones').':');
				$GLOBALS['phpgw']->template->set_var('edit_mstones_button','<input type="submit" name="mstone" value="' . lang('edit milestones') . '">');
				$GLOBALS['phpgw']->template->set_var('edit_roles_events_button','<input type="submit" name="roles" value="' . lang('edit roles and events') . '">');
			}
			else
			{
				if($this->boprojects->siteconfig['projectnr'] == 'generate')
				{
					$GLOBALS['phpgw']->template->set_var('choose','<input type="checkbox" name="values[choose]" value="True"' . (isset($values['choose'])?' checked':'') . '>');
					$GLOBALS['phpgw']->template->set_var('lang_choose',lang('generate project id'));
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('help_img','<a href="#"><img src="' . $GLOBALS['phpgw']->common->image('projects','help') . '" onclick="open_popup(\''
													. $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects_base.proid_help_popup')) . '\');" title="'
													. lang('help') . '" alt="Project-Nr." /></a>');
				}

				switch($action)
				{
					case 'mains':
						$start	= $start_val?$start_val:''; //mktime(12,0,0,date('m'),date('d'),date('Y'));
						$end	= $end_val?$end_val:'';
						$pstart	= $pstart_val?$pstart_val:mktime(12,0,0,date('m'),date('d'),date('Y'));
						$pend	= $pend_val?$pend_val:'';

						$values['access']	= isset($values['access'])?$values['access']:'public';
						$values['direct_work']	= isset($values['direct_work'])?$values['direct_work']:'Y';
						break;
					case 'subs':
						if(is_array($parent))
						{
							$start	= $start_val?$start_val:($parent['sdate']?mktime(12,0,0,date('m',$parent['sdate']),date('d',$parent['sdate']),date('Y',$parent['sdate'])):''); //mktime(12,0,0,date('m'),date('d'),date('Y')));
							$end	= $end_val?$end_val:($parent['edate']?mktime(12,0,0,date('m',$parent['edate']),date('d',$parent['edate']),date('Y',$parent['edate'])):'');
							$pstart	= $pstart_val?$pstart_val:($parent['psdate']?mktime(12,0,0,date('m',$parent['psdate']),date('d',$parent['psdate']),date('Y',$parent['psdate'])):'');
							$pend	= $pend_val?$pend_val:($parent['pedate']?mktime(12,0,0,date('m',$parent['pedate']),date('d',$parent['pedate']),date('Y',$parent['pedate'])):'');
							$values['plan_bottom_up']	= $parent['plan_bottom_up']?$parent['plan_bottom_up']:'N';
							$values['direct_work'] = $parent['direct_work']?$parent['direct_work']:'Y';
						}
						break;
				}
			}

			$GLOBALS['phpgw']->template->set_var('start_date_select',$jscal->input('sdate[str]',$start));
			$GLOBALS['phpgw']->template->set_var('end_date_select',$jscal->input('edate[str]',$end));

			$GLOBALS['phpgw']->template->set_var('pstart_date_select',$jscal->input('psdate[str]',$pstart));
			$GLOBALS['phpgw']->template->set_var('pend_date_select',$jscal->input('pedate[str]',$pend));

			if ($action == 'mains')
			{
				if($this->boprojects->siteconfig['categorie_required'] == 'yes')
				{
					$cat_option0_lang = lang('Please select');
				}
				else
				{
					$cat_option0_lang = lang('None');
				}

				$cat = '<select style="width:99%; overflow:visable;" name="values[cat]"><option value="">' . $cat_option0_lang . '</option>'
						.	$this->boprojects->cats->formatted_list('select','all',$values['cat'],true) . '</select>';
				$GLOBALS['phpgw']->template->set_var('cat',$cat);

				//$GLOBALS['phpgw']->template->set_var('pcosts','<input type="text" name="values[pcosts]" value="' . $values['pcosts'] . '"> [' . $prefs['currency'] . $prefs['currency'] . '.cc]');

				// use input field as checkbox
				$GLOBALS['phpgw']->template->set_var('plan_bottom_up_input_type', 'checkbox');
				$GLOBALS['phpgw']->template->set_var('plan_bottom_up_input_value', 'Y');
				$GLOBALS['phpgw']->template->set_var('plan_bottom_up_input_checked', (($values['plan_bottom_up'] == 'Y') ? ' checked' : ''));
				$GLOBALS['phpgw']->template->set_var('plan_bottom_up_text', '');

				// use input field as checkbox
				$GLOBALS['phpgw']->template->set_var('direct_work_input_type', 'checkbox');
				$GLOBALS['phpgw']->template->set_var('direct_work_input_value', 'Y');
				$GLOBALS['phpgw']->template->set_var('direct_work_input_checked', (($values['direct_work'] == 'Y') ? ' checked' : ''));
				$GLOBALS['phpgw']->template->set_var('direct_work_text', '');

				$GLOBALS['phpgw']->template->set_var('lang_parent', lang('Main project'));
				$GLOBALS['phpgw']->template->set_var('parent_select', '');
			}
			elseif($action == 'subs')
			{
/*
				$GLOBALS['phpgw']->template->set_var('pro_main',$main['title'] . ' [' . $main['number'] . ']');
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.view_project',
																											'action'=>'mains',
																											'project_id'=> $pro_main)));
				$GLOBALS['phpgw']->template->set_var('lang_sum_jobs',lang('sum jobs'));
				$GLOBALS['phpgw']->template->set_var('lang_available',lang('available'));

				$GLOBALS['phpgw']->template->set_var('ptime_main',intval($main['ptime']).':00');
				$GLOBALS['phpgw']->template->set_var('ptime_jobs',intval($main['ptime_jobs']).':00');
				$GLOBALS['phpgw']->template->set_var('atime',intval($main['atime']).':00');
				$GLOBALS['phpgw']->template->set_var('lang_budget_main',lang('budget main project') . ':&nbsp;' . $prefs['currency']);
				$GLOBALS['phpgw']->template->set_var('budget_main',$main['budget']);
				$GLOBALS['phpgw']->template->set_var('pbudget_jobs',sprintf("%01.2f",$main['pbudget_jobs']));
				$GLOBALS['phpgw']->template->set_var('apbudget',sprintf("%01.2f",$main['ap_budget_jobs']));

				$GLOBALS['phpgw']->template->fp('mainhandle','main',true);
*/
				$values['coordinator']		= isset($values['coordinator']) ? $values['coordinator'] : $GLOBALS['phpgw_info']['user']['account_id']; // $parent['coordinator'];
				$GLOBALS['phpgw']->accounts->get_account_name($values['coordinator'],$lid,$fname,$lname);
				$values['coordinatorout']	= isset($values['coordinatorout']) ? $values['coordinatorout'] : $GLOBALS['phpgw']->common->display_fullname($lid,$fname,$lname); // $parent['coordinatorout'];
				$values['salesmanager']		= isset($values['salesmanager'])?$values['salesmanager']:$parent['salesmanager'];
				$values['parent']					= isset($values['parent'])?$values['parent']:$parent['project_id'];
				$values['customer']				= isset($values['customer'])?$values['customer']:$parent['customer'];
				$values['customer_org']		= isset($values['customer_org'])?$values['customer_org']:$parent['customer_org'];
				$values['number']					= isset($values['number'])?$values['number']:$parent['number'];
				$values['investment_nr']	= isset($values['investment_nr'])?$values['investment_nr']:$parent['investment_nr'];
				$values['customer_nr']		= isset($values['customer_nr'])?$values['customer_nr']:$parent['customer_nr'];
				$values['url']						= isset($values['url'])?$values['url']:$parent['url'];
				$values['reference']			= isset($values['reference'])?$values['reference']:$parent['reference'];
				$values['budget']					= isset($values['budget'])?$values['budget']:$parent['ap_budget_jobs'];
				$values['ptime']					= isset($values['ptime'])?intval($values['ptime']):intval($parent['atime']);
				$values['e_budget']				= isset($values['e_budget'])?$values['e_budget']:$parent['e_budget'];
				$values['access']					= isset($values['access'])?$values['access']:$parent['access'];
				$values['priority']				= isset($values['priority'])?$values['priority']:$parent['priority'];
				$values['accounting']                  = isset($values['accounting'])?$values['accounting']:$parent['accounting'];
				$values['project_accounting_factor']   = isset($values['project_accounting_factor'])?$values['project_accounting_factor']:$parent['project_accounting_factor'];
				$values['billable']				= isset($values['billable'])?$values['billable']:$parent['billable'];
				$values['inv_method']			= isset($values['inv_method'])?$values['inv_method']:$parent['inv_method'];

				/* disable because we couldnt update parent values
				$GLOBALS['phpgw']->template->set_var('parent_select','<select name="values[parent]">' . $this->boprojects->select_project_list(array('action'	=> 'mainandsubs',
																																					'status'	=> $values['status'],
																																					'self'		=> $project_id,
																																					'selected'	=> $values['parent'],
																																					'main'		=> $pro_main)) . '</select>');
				*/
				$GLOBALS['phpgw']->template->set_var('parent_select', $parent['title'].'<input type="hidden" name="values[parent]" value="'.intval($values['parent']).'">');

				$GLOBALS['phpgw']->template->set_var('cat',$this->boprojects->cats->id2name($main['cat']));
				//$this->cat_id = $main['cat'];

				// use input field as hidden field and show only a text info
				$GLOBALS['phpgw']->template->set_var('plan_bottom_up_input_type', 'hidden');
				$GLOBALS['phpgw']->template->set_var('plan_bottom_up_input_value', (($values['plan_bottom_up'] == 'Y') ? 'Y' : 'N'));
				$GLOBALS['phpgw']->template->set_var('plan_bottom_up_input_checked', '');
				$GLOBALS['phpgw']->template->set_var('plan_bottom_up_text', (($values['plan_bottom_up'] == 'Y') ? lang('Yes') : lang('No')));

				// use input field as hidden field and show only a text info
				$GLOBALS['phpgw']->template->set_var('direct_work_input_type', 'hidden');
				$GLOBALS['phpgw']->template->set_var('direct_work_input_value', (($values['direct_work'] == 'Y') ? 'Y' : 'N'));
				$GLOBALS['phpgw']->template->set_var('direct_work_input_checked', '');
				$GLOBALS['phpgw']->template->set_var('direct_work_text', (($values['direct_work'] == 'Y') ? lang('Yes') : lang('No')));
			}

			$GLOBALS['phpgw']->template->set_var('lang_open_popup',lang('open popup window'));
			$link_data['menuaction'] = 'projects.uiprojects.edit_project';
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			if(($values['ptime'] > 0) && ($values['budget'] == 0))
			{
				$GLOBALS['phpgw']->template->set_var('budget_type', 'h');
				$GLOBALS['phpgw']->template->set_var('budgetradio_check_m', '');
				$GLOBALS['phpgw']->template->set_var('budgetradio_check_h', 'checked');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('budget_type', 'm');
				$GLOBALS['phpgw']->template->set_var('budgetradio_check_m', 'checked');
				$GLOBALS['phpgw']->template->set_var('budgetradio_check_h', '');
			}

			$GLOBALS['phpgw']->template->set_var('lang_budget_type', lang('budget type'));
			$GLOBALS['phpgw']->template->set_var('currency',$prefs['currency']);
			$month = $this->boprojects->return_date();
			$GLOBALS['phpgw']->template->set_var('month',$month['monthformatted']);

			$GLOBALS['phpgw']->template->set_var('status_list',$this->ui_base->status_format($values['status'],(($action == 'mains')?true:False)));
			$GLOBALS['phpgw']->template->set_var('priority_list',$this->priority_list($values['priority']));

			$acces_private = '<option value="private"' . ($values['access'] == 'private'?' selected="selected"':'') . '>' . lang('private') . '</option>';
			$acces_public = '<option value="public"' . ($values['access'] == 'public'?' selected="selected"':'') . '>' . lang('public') . '</option>';
			$acces_anonym = '<option value="anonym"' . ($values['access'] == 'anonym'?' selected="selected"':'') . '>' . lang('anonymous public') . '</option>';

			$GLOBALS['phpgw']->template->set_var('acces_private',$acces_private);
			$GLOBALS['phpgw']->template->set_var('acces_public',$acces_public);
			$GLOBALS['phpgw']->template->set_var('acces_anonym',$acces_anonym);

			$GLOBALS['phpgw']->template->set_var('access',$aradio);

			$GLOBALS['phpgw']->template->set_var('previous_select',$this->boprojects->select_project_list(array('action' => 'all',
																										'status' => $values['status'],
																										'self' => $project_id,
																									'selected' => $values['previous'])));

			$GLOBALS['phpgw']->template->set_var('help_image',$GLOBALS['phpgw']->common->image('projects','help.png'));
			$GLOBALS['phpgw']->template->set_var('help_project_nr','http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['phpgw_info']['server']['webserver_url'] . '/projects/templates/default/projects_nr_help.html');

			if($this->boprojects->siteconfig['accounting'] == 'own')
			{
				// define default setting for new projects
				if(!isset($values['billable']))
				{
					$values['billable'] = 'Y';
				}

				// check billable value
				if(!is_string($values['billable']))
				{	// no string, map to not_billable
					if($values['billable'] === false)
					{
						$values['not_billable'] = 'Y';
					}
					else
					{
						$values['not_billable'] = 'N';
					}
				}
				else
				{ // map to not_billable
					$values['not_billable'] = ($values['billable']=='Y')?'N':'Y';
				}

				$GLOBALS['phpgw']->template->set_var('acc_employee_selected',($values['accounting']=='employee'?' selected="selected"':''));
				$GLOBALS['phpgw']->template->set_var('acc_project_selected',($values['accounting']=='project'?' selected="selected"':''));
				$GLOBALS['phpgw']->template->set_var('project_accounting_factor',sprintf("%01.2f",$values['project_accounting_factor']));
				$GLOBALS['phpgw']->template->set_var('acc_not_billable_checked',($values['not_billable']=='Y'?' checked':''));

				$GLOBALS['phpgw']->template->fp('accounting_ownhandle','accounting_own',true);
			}
			else
			{
				if($action == 'mains')
				{
// ------------ activites bookable ----------------------

					$GLOBALS['phpgw']->template->set_var('book_activities_list',$this->boprojects->select_activities_list($project_id,False));

// -------------- activities billable ----------------------

	    		$GLOBALS['phpgw']->template->set_var('bill_activities_list',$this->boprojects->select_activities_list($project_id,true));
					$GLOBALS['phpgw']->template->fp('accounting_acthandle','accounting_act',true);
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('book_activities_list',$this->boprojects->select_pro_activities($project_id, $pro_main, False));
    			$GLOBALS['phpgw']->template->set_var('bill_activities_list',$this->boprojects->select_pro_activities($project_id, $pro_main, true));
					$GLOBALS['phpgw']->template->fp('accounting_acthandle','accounting_act',true);
				}
			}

			$GLOBALS['phpgw']->template->set_block('edit_form','option_discount','option_discount_handle');
			$GLOBALS['phpgw']->template->set_block('edit_form','option_not_billable','option_not_billable_handle');
			$GLOBALS['phpgw']->template->set_block('edit_form','option_direct_work','option_direct_work_handle');

			if($this->boprojects->siteconfig['show_project_option_discount'] == 'yes')
			{
				$GLOBALS['phpgw']->template->set_var('discount',$values['discount']);
				$GLOBALS['phpgw']->template->set_var('dt_no',$values['discount_type']=='no'?' selected="selected"':'');
				$GLOBALS['phpgw']->template->set_var('dt_amount',$values['discount_type']=='amount'?' selected="selected"':'');
				$GLOBALS['phpgw']->template->set_var('dt_percent',$values['discount_type']=='percent'?' selected="selected"':'');
				$GLOBALS['phpgw']->template->parse('option_discount_handle','option_discount',False);
			}

			if($this->boprojects->siteconfig['show_project_option_not_billable'] == 'yes')
			{
				$GLOBALS['phpgw']->template->parse('option_not_billable_handle','option_not_billable',False);
			}

			if($this->boprojects->siteconfig['show_project_option_direct_work'] == 'yes')
			{
				$GLOBALS['phpgw']->template->parse('option_direct_work_handle','option_direct_work',False);
			}

			$GLOBALS['phpgw']->template->set_var('budget',sprintf("%1.02f", (float) $values['budget']));
			$GLOBALS['phpgw']->template->set_var('e_budget',sprintf("%1.02f", (float) $values['e_budget']));
			$GLOBALS['phpgw']->template->set_var('number',$values['number']);
			$GLOBALS['phpgw']->template->set_var('title',$values['title']);
			$GLOBALS['phpgw']->template->set_var('descr',$values['descr']);
			$GLOBALS['phpgw']->template->set_var('ptime',(intval($values['ptime'])==0?'':intval($values['ptime'])));
			$GLOBALS['phpgw']->template->set_var('investment_nr',$values['investment_nr']);
			$GLOBALS['phpgw']->template->set_var('customer_nr',$values['customer_nr']);

			$GLOBALS['phpgw']->template->set_var('inv_method',$values['inv_method']);
			$GLOBALS['phpgw']->template->set_var('reference',$values['reference']);
			$GLOBALS['phpgw']->template->set_var('url',$values['url']);

			$GLOBALS['phpgw']->template->set_var('result',$values['result']);
			$GLOBALS['phpgw']->template->set_var('test',$values['test']);
			$GLOBALS['phpgw']->template->set_var('quality',$values['quality']);

			$GLOBALS['phpgw']->template->set_var('attachment',$this->attached_files->get_files($project_id, true));
			$GLOBALS['phpgw']->template->set_var('lang_files',lang('Files'));
			$GLOBALS['phpgw']->template->set_var('lang_attach',lang('Attach File'));

//--------- coordinator -------------

			$GLOBALS['phpgw']->template->set_var('lang_coordinator',lang('Coordinator'));
			switch($GLOBALS['phpgw_info']['user']['preferences']['common']['account_selection'])
			{
				case 'popup':
					if ($values['coordinator'])
					{
						$GLOBALS['phpgw']->template->set_var('accountid',$values['coordinator']);
						if(!$values['coordinatorout'])
						{
							$GLOBALS['phpgw']->accounts->get_account_name($values['coordinator'],$lid,$fname,$lname);
							$values['coordinatorout'] = $GLOBALS['phpgw']->common->display_fullname($lid,$fname,$lname);
						}
						$GLOBALS['phpgw']->template->set_var('accountname',$values['coordinatorout']);
					}
					else
					{
						$values['coordinator'] = $GLOBALS['phpgw_info']['user']['account_id'];
						$GLOBALS['phpgw']->template->set_var('accountid', $values['coordinator']);
						$GLOBALS['phpgw']->accounts->get_account_name($values['coordinator'],$lid,$fname,$lname);
						$values['coordinatorout'] = $GLOBALS['phpgw']->common->display_fullname($lid,$fname,$lname);
						$GLOBALS['phpgw']->template->set_var('accountname',$values['coordinatorout']);
					}

					$GLOBALS['phpgw']->template->set_var('lang_salesmanager', lang('sales department'));

					if ($values['salesmanager'])
					{
						$GLOBALS['phpgw']->template->set_var('salesmanagerid',$values['salesmanager']);
						if(!$values['salesmanagerout'])
						{
							$GLOBALS['phpgw']->accounts->get_account_name($values['salesmanager'],$slid,$sfname,$slname);
							$values['salesmanagerout'] = $GLOBALS['phpgw']->common->display_fullname($slid,$sfname,$slname);
						}

						$GLOBALS['phpgw']->template->set_var('salesmanagername', $values['salesmanagerout']);
					}

					$GLOBALS['phpgw']->template->set_var('clisthandle','');
					$GLOBALS['phpgw']->template->fp('cfieldhandle','cfield',true);

					/* disable auto adapt employees fron parent project
					if(($project_id || $parent['project_id']) && !(isset($values['employees']) && (count($values['employees']) > 0)))
					*/
					if($project_id && !(isset($values['employees']) && (count($values['employees']) > 0)))
					{
						$GLOBALS['phpgw']->template->set_var('employee_list',$this->ui_base->employee_format(array('type' => 'popup','project_id' => ($project_id?$project_id:$parent['project_id']))));
					}
					elseif(isset($values['employees']) && (count($values['employees']) > 0))
					{
						$values['employees'] = array_unique($values['employees']);
						$employee_list = '';
						for($i=0; $i<count($values['employees']); ++$i)
						{
							$account_id = $values['employees'][$i];
							if(!$account_id)
								continue;
							$GLOBALS['phpgw']->accounts->get_account_name($account_id,$lid,$fname,$lname);
							$fullname = $GLOBALS['phpgw']->common->display_fullname($lid,$fname,$lname);
							$employee_list .= '<option value="'.$account_id.'" SELECTED>'.$fullname.'</option>' . "\n";
						}
						$GLOBALS['phpgw']->template->set_var('employee_list',$employee_list);
					}
					$GLOBALS['phpgw']->template->set_var('elisthandle','');

					$parent_project_members_string = '';
					$parent_project_members = array();

					if (isset($parent['project_id']) && $parent['project_id'] > 0 && $parent['project_id'] != $project_id)
					{
						$parent_project_members = $this->boprojects->selected_employees(
							array('project_id' => $parent['project_id'])
						);

						while(list($no_use, $adata) = each($parent_project_members))
						{
							echo $aid;
							if($adata['account_id'] <= 0)
							{
								continue;
							}
							else
							{
								$parent_project_members_string .= 'parent_project_members[parent_project_members.length] = new Array("'.$adata['account_fullname'].'", "'.$adata['account_id'].'");'."\n";
							}
						}
						$parent_project_members_disable = '';
					}
					else
					{
						$parent_project_members_disable = ' disabled="disabled"';
					}

					//echo '<pre>'.var_dump($parent_project_members_string).'</pre>';
					$GLOBALS['phpgw']->template->set_var('parent_project_members', $parent_project_members_string);
					$GLOBALS['phpgw']->template->set_var('parent_project_members_button_disable', $parent_project_members_disable);

					$GLOBALS['phpgw']->template->set_var('lang_adapt', lang('adapt'));
					$GLOBALS['phpgw']->template->set_var('lang_remove', lang('remove'));
					$GLOBALS['phpgw']->template->set_var('lang_select', lang('select'));

					$GLOBALS['phpgw']->template->set_var('tooltip_parent_project_members', $cssTooltip->createHelpTooltip(lang('tooltip_parent_project_members')));
					$GLOBALS['phpgw']->template->set_var('tooltip_select_project_members', $cssTooltip->createHelpTooltip(lang('tooltip_select_project_members')));
					$GLOBALS['phpgw']->template->set_var('tooltip_remove_project_members', $cssTooltip->createHelpTooltip(lang('tooltip_remove_project_members')));
					$GLOBALS['phpgw']->template->fp('efieldhandle','efield',true);
					break;
				default:
					$GLOBALS['phpgw']->template->set_var('coordinator_list',$this->ui_base->employee_format(array('selected' => ($values['coordinator']?$values['coordinator']:$this->boprojects->account))));
					$GLOBALS['phpgw']->template->set_var('cfieldhandle','');
					$GLOBALS['phpgw']->template->fp('clisthandle','clist',true);
					$GLOBALS['phpgw']->template->set_var('employee_list',$this->ui_base->employee_format(array('project_id' => ($project_id?$project_id:$parent['project_id']),'action' => $action,
																									'pro_parent' => $parent['project_id'],'selected' => $values['employees'])));
					$GLOBALS['phpgw']->template->set_var('efieldhandle','');
					$GLOBALS['phpgw']->template->fp('elisthandle','elist',true);
			}

			$GLOBALS['phpgw']->template->set_var('tooltip_select_coordinator',  $cssTooltip->createHelpTooltip(lang('tooltip_select_coordinator')));
			$GLOBALS['phpgw']->template->set_var('tooltip_select_salesmanager', $cssTooltip->createHelpTooltip(lang('tooltip_select_salesmanager')));
			$GLOBALS['phpgw']->template->set_var('tooltip_remove_salesmanager', $cssTooltip->createHelpTooltip(lang('tooltip_remove_salesmanager')));

			$abid = $values['customer'];
			$customer = $this->boprojects->read_single_contact($abid);
			$name = $customer[0] ? $customer[0]['per_first_name'] . ' ' . $customer[0]['per_last_name'] : '';
			$GLOBALS['phpgw']->template->set_var('name',$name);
			$GLOBALS['phpgw']->template->set_var('abid',$abid);

			$customer_org_id = $values['customer_org'];
			$customer_org = $this->boprojects->read_single_contact_org($customer_org_id);
			$customer_org_name = $customer_org[0] ? $customer[0]['org_name'] : '';
			$GLOBALS['phpgw']->template->set_var('customer_org_name', $customer_org_name);
			$GLOBALS['phpgw']->template->set_var('customer_org', $customer_org_id);

			$GLOBALS['phpgw']->template->set_var('attachment',$this->attached_files->get_files($project_id,true));

			if ($project_id && $this->boprojects->edit_perms(array('action' => $action,'coordinator' => $values['coordinator'],'main_co' => $main['coordinator'],
													'parent_co' => $parent['coordinator'],'type' => 'delete')))
			{
				$GLOBALS['phpgw']->template->set_var('delete_button','<input type="submit" name="delete" value="' . lang('Delete') .'">');
			}

			$this->save_sessiondata($action);
			$GLOBALS['phpgw']->template->pfp('out','edit_form');
		}

		function view_project()
		{
			//$action		  = get_var('action',    array('POST','GET'));
			//$pro_main	  = get_var('pro_main',  array('POST','GET'));
			$project_id	= get_var('project_id',array('POST','GET'));

			if($project_id)
			{
				$values = $this->boprojects->read_single_project($project_id);
				$pro_main = $values['main'];
				$values['cat'] = $this->boprojects->return_value('cat', $pro_main);

				if($values['parent'] > 0)
				{
					$action = 'subs';
				}
				else
				{
					$action = 'mains';
				}
			}

			$link_data = array
			(
				'menuaction'  => 'projects.uiprojects.view_project',
				'pro_main'    => $pro_main,
				'action'      => $action,
				'project_id'  => $project_id,
				'public_view' => $public_view
			);

			if($_POST['back'])
			{
				$clickhistory = $GLOBALS['phpgw']->session->get_click_path_entry();
				$link_data['menuaction'] = $clickhistory['menuaction'];
				$link_data['project_id'] = $clickhistory['get']['project_id'];
				$link_data['action'] = $clickhistory['get']['action'];
				$link_data['pro_main'] = $clickhistory['get']['pro_main'];
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if($_POST['edit'])
			{
				$link_data['menuaction'] = 'projects.uiprojects.edit_project';
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}
			if($_POST['mstone'])
			{
				$link_data['menuaction'] = 'projects.uiprojects.project_mstones';
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if($_POST['roles'])
			{
				$link_data['menuaction'] = 'projects.uiprojects.assign_employee_roles';
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if($_POST['done'])
			{
				if ($public_view)
				{
					$menu = 'projects.uiprojects.list_projects_home';
				}
				else
				{
					$menu = 'projects.uiprojects.list_projects';
				}
				$link_data['menuaction'] = $menu;
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if (isset($public_view))
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('view project')
																. $this->admin_header_info();
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				$this->set_app_langs();
			}
			else
			{
				//$this->display_app_header();
				$this->ui_base->display_app_header();
			}

			$GLOBALS['phpgw']->template->set_file(array('view' => 'view.tpl'));
			$GLOBALS['phpgw']->template->set_block('view','sub','subhandle');
			$GLOBALS['phpgw']->template->set_block('view','accounting_act','acthandle');
			$GLOBALS['phpgw']->template->set_block('view','accounting_own','ownhandle');
			$GLOBALS['phpgw']->template->set_block('view','accounting_both','bothhandle');

			$GLOBALS['phpgw']->template->set_block('view','nonanonym','nonanonymhandle');

			$GLOBALS['phpgw']->template->set_block('view','mslist','mslisthandle');
			$GLOBALS['phpgw']->template->set_block('view','emplist','emplisthandle');

			$GLOBALS['phpgw']->template->set_block('view', 'attachment_list', 'listhandle');

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$nopref = $this->boprojects->check_prefs();
			if (is_array($nopref))
			{
				$GLOBALS['phpgw']->template->set_var('pref_message',$GLOBALS['phpgw']->common->error_list($nopref));
			}
			else
			{
				$prefs = $this->boprojects->read_prefs();
			}

			if(!isset($values))
			{
				$values = $this->boprojects->read_single_project($project_id);
			}

			//_debug_array($values);

			$GLOBALS['phpgw']->template->set_var('cat',$this->boprojects->cats->id2name($values['cat']));
			if ($action == 'mains' || $action == 'amains')
			{
				$GLOBALS['phpgw']->template->set_var('pcosts',$values['pcosts']);
			}
/*
			else if($pro_main && $action == 'subs')
			{
				$main = $this->boprojects->read_single_project($pro_main);

				$GLOBALS['phpgw']->template->set_var('cat',$this->boprojects->cats->id2name($main['cat']));
				$GLOBALS['phpgw']->template->set_var('pcosts',$main['pcosts']);

				$link_data['project_id'] = $values['parent'];
				$GLOBALS['phpgw']->template->set_var('pro_parent',$this->boprojects->return_value('pro',$values['parent']));
				$GLOBALS['phpgw']->template->set_var('parent_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.view_project',
																											'action'=> ($values['main']==$values['parent']?'mains':'subs'),
																											'project_id'=> $values['parent'],
																											'pro_main'=> $values['main'])));

				$GLOBALS['phpgw']->template->set_var('pro_main',$this->boprojects->return_value('pro',$values['main']));
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.view_project',
																											'action'=>'mains',
																											'project_id'=> $values['main'])));
				$GLOBALS['phpgw']->template->set_var('previous',$this->boprojects->return_value('pro',$values['previous']));
				$GLOBALS['phpgw']->template->fp('subhandle','sub',true);
			}
*/
			$GLOBALS['phpgw']->template->set_var('investment_nr',($values['investment_nr']?$values['investment_nr']:$main['investment_nr']));
			if($values['parent']>0)
			{
				$GLOBALS['phpgw']->template->set_var('parent_select', $this->boprojects->return_value('title', $values['parent']));
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('parent_select', '');
				$GLOBALS['phpgw']->template->set_var('lang_parent', lang('main project'));
			}

			$GLOBALS['phpgw']->template->set_var('number',$values['number']);
			$GLOBALS['phpgw']->template->set_var('title',($values['title']?$values['title']:'&nbsp;'));
			$GLOBALS['phpgw']->template->set_var('descr',($values['descr']?$values['descr']:'&nbsp;'));
			$GLOBALS['phpgw']->template->set_var('status',($values['status']?lang($values['status']):'&nbsp;'));

			$GLOBALS['phpgw']->template->set_var('plan_bottom_up_text', (($values['plan_bottom_up'] == 'Y') ? lang('Yes') : lang('No')));
			$GLOBALS['phpgw']->template->set_var('direct_work_text', (($values['direct_work'] == 'Y') ? lang('Yes') : lang('No')));

			$GLOBALS['phpgw']->template->set_var('budget_item',sprintf("%01.2f",$values['budget']-$values['pbudget_jobs']));
			$GLOBALS['phpgw']->template->set_var('budget_jobs',sprintf("%01.2f",$values['pbudget_jobs']));
			$GLOBALS['phpgw']->template->set_var('budget_sum', sprintf("%01.2f",$values['budget']));

			$GLOBALS['phpgw']->template->set_var('ebudget_item',sprintf("%01.2f",$values['e_budget']-$values['e_budget_chields']));
			$GLOBALS['phpgw']->template->set_var('ebudget_jobs',sprintf("%01.2f",$values['e_budget_chields']));
			$GLOBALS['phpgw']->template->set_var('ebudget_sum', sprintf("%01.2f",$values['e_budget']));

			$GLOBALS['phpgw']->template->set_var('discount',$values['discount']);
			$GLOBALS['phpgw']->template->set_var('discount_type',$values['discount_type']=='amount'?$prefs['currency']:'%');

			$GLOBALS['phpgw']->template->set_var('inv_method',$values['inv_method']);

			$GLOBALS['phpgw']->template->set_var('reference',$values['reference']);
			$GLOBALS['phpgw']->template->set_var('url',$values['url']);

			$GLOBALS['phpgw']->template->set_var('result',$values['result']);
			$GLOBALS['phpgw']->template->set_var('test',$values['test']);
			$GLOBALS['phpgw']->template->set_var('quality',$values['quality']);
			$GLOBALS['phpgw']->template->set_var('priority',$this->boprojects->formatted_priority($values['priority']));

			$GLOBALS['phpgw']->template->set_var('currency',$prefs['currency']);

			$month = $this->boprojects->return_date();
			$GLOBALS['phpgw']->template->set_var('month',$month['monthformatted']);

			$GLOBALS['phpgw']->template->set_var('ptime_item',intval($values['ptime']-$values['ptime_jobs']));
			$GLOBALS['phpgw']->template->set_var('ptime_jobs',intval($values['ptime_jobs']));
			$GLOBALS['phpgw']->template->set_var('ptime_sum', intval($values['ptime']));

			$GLOBALS['phpgw']->template->set_var('uhours_jobs',$values['uhours_jobs_all']);

			$GLOBALS['phpgw']->template->set_var('sdate',$values['sdate_formatted']);
			$GLOBALS['phpgw']->template->set_var('edate',$values['edate_formatted']);

			$GLOBALS['phpgw']->template->set_var('psdate',$values['psdate_formatted']);
			$GLOBALS['phpgw']->template->set_var('pedate',$values['pedate_formatted']);

			$GLOBALS['phpgw']->template->set_var('udate',$values['udate_formatted']);
			$GLOBALS['phpgw']->template->set_var('cdate',$values['cdate_formatted']);

//--------- coordinator -------------

			$GLOBALS['phpgw']->template->set_var('lang_coordinator',lang('Coordinator'));
			$GLOBALS['phpgw']->template->set_var('coordinator',$values['coordinatorout']);
			$GLOBALS['phpgw']->template->set_var('lang_salesmanager',lang('sales manager'));
			$GLOBALS['phpgw']->template->set_var('salesmanager',$values['salesmanagerout']);
			$GLOBALS['phpgw']->template->set_var('owner',$GLOBALS['phpgw']->common->grab_owner_name($values['owner']));
			$GLOBALS['phpgw']->template->set_var('processor',$GLOBALS['phpgw']->common->grab_owner_name($values['processor']));

// ----------------------------------- customer ------

			$GLOBALS['phpgw']->template->set_var('customer',$values['customerout']);
			$GLOBALS['phpgw']->template->set_var('customerorg',$values['customerorgout']);
			$GLOBALS['phpgw']->template->set_var('customer_nr',$values['customer_nr']);

// --------- milestones ------------------------------

			$GLOBALS['phpgw']->template->set_var('lang_milestones',lang('milestones').':');
			$mstones = $this->boprojects->get_mstones($project_id);
			//$link_data['menuaction'] = 'projects.uiprojects.edit_mstone';

			while (is_array($mstones) && (list($no_use,$ms) = each($mstones)))
			{
				//$link_data['s_id'] = $ms['s_id'];
				$GLOBALS['phpgw']->template->set_var('s_title',$ms['title']);
				//$GLOBALS['phpgw']->template->set_var('mstone_edit_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
				$GLOBALS['phpgw']->template->set_var('s_edateout',$this->boprojects->formatted_edate($ms['edate'],true,'ms'));
				$GLOBALS['phpgw']->template->fp('mslisthandle','mslist',true);
			}

// --------- emps & roles ------------------------------

			$all_emps = $this->boprojects->selected_employees(array('project_id' => $project_id));
			if(is_array($all_emps))
			{
				usort($all_emps, array('uiprojects_base', 'cmp_employees'));
			}

			$emps_with_role_exists = array();
			$emps_with_role = $this->boprojects->get_employee_roles(array('project_id' => $project_id,'formatted' => true));

			while (is_array($emps_with_role) && (list($no_use,$emp) = each($emps_with_role)))
			{
				$emps_with_role_exists[$emp['account_id']] = $emp;
			}

			while (is_array($all_emps) && (list(,$emp) = each($all_emps)))
			{
				if(!$emp['account_id'])
				{
					continue;
				}

				if(isset($emps_with_role_exists[$emp['account_id']]))
				{
					$emp = $emps_with_role_exists[$emp['account_id']];
				}
				else
				{
					$GLOBALS['phpgw']->accounts->get_account_name($emp['account_id'],$lid,$fname,$lname);
					$fullname = $GLOBALS['phpgw']->common->display_fullname($lid,$fname,$lname);
					$emp['emp_name'] = $fullname;
					$emp['events'] = '';
					$emp['role_name'] = '';
				}

				$GLOBALS['phpgw']->template->set_var('emp_name',$emp['emp_name']);
				$GLOBALS['phpgw']->template->set_var('events',$emp['events']);
				$GLOBALS['phpgw']->template->set_var('role_name',$emp['role_name']);
				$GLOBALS['phpgw']->template->fp('emplisthandle','emplist',true);
			}

			if (!isset($public_view))
			{
				if($this->boprojects->siteconfig['accounting'] == 'own')
				{
					$GLOBALS['phpgw']->template->set_var('accounting_factor',($values['accounting']=='employee'?lang('factor employee'):lang('factor project')));
					$GLOBALS['phpgw']->template->set_var('project_accounting_factor',sprintf("%01.2f",$values['project_accounting_factor']));
					$GLOBALS['phpgw']->template->set_var('project_accounting_factor_d',sprintf("%01.2f",$values['project_accounting_factor_d']));
					$GLOBALS['phpgw']->template->set_var('billable',($values['billable']=='Y'?lang('yes'):lang('no')));

					$GLOBALS['phpgw']->template->fp('accounting_settings','accounting_own',true);
				}
				else
				{
// ------------ activites bookable ----------------------
					$boact = $this->boprojects->activities_list($project_id,False);
					if (is_array($boact))
					{
						while (list($null,$bo) = each($boact))
						{
							$boact_list .=	$bo['descr'] . ' [' . $bo['num'] . ']' . '<br>';
						}
					}

					$GLOBALS['phpgw']->template->set_var('book_activities_list',$boact_list);
// -------------- activities billable ----------------------

					$billact = $this->boprojects->activities_list($project_id,true);
					if (is_array($billact))
					{
						while (list($null,$bill) = each($billact))
						{
							$billact_list .=	$bill['descr'] . ' [' . $bill['num'] . ']' . "\n";
						}
					}
					$GLOBALS['phpgw']->template->set_var('bill_activities_list',$billact_list);
					$GLOBALS['phpgw']->template->fp('accounting_settings','accounting_act',true);
				}
				$GLOBALS['phpgw']->template->fp('accounting_2settings','accounting_both',true);
				$GLOBALS['phpgw']->template->fp('nonanonymhandle','nonanonym',true);
				/*$GLOBALS['phpgw']->hooks->process(array
				(
					'location'   => 'projects_view',
					'project_id' => $project_id
				));*/

				if ($this->boprojects->edit_perms(array('action' => $action,'coordinator' => $values['coordinator'],'main' => $values['main'],
													'parent' => $values['parent'])))
				{
					$GLOBALS['phpgw']->template->set_var('edit_button','<input type="submit" name="edit" value="' . lang('edit') .'">');
					$GLOBALS['phpgw']->template->set_var('edit_milestones_button','<input type="submit" name="mstone" value="' . lang('edit milestones') .'">');
					$GLOBALS['phpgw']->template->set_var('edit_roles_events_button','<input type="submit" name="roles" value="' . lang('edit roles and events') .'">');
				}
			}

			$GLOBALS['phpgw']->template->set_var('lang_filename',lang('Filename'));
			$GLOBALS['phpgw']->template->set_var('lang_period',lang('Period'));

			$attachments = $this->attached_files->get_files($project_id,true,true,$GLOBALS['phpgw_info']['user']['account_id']);
			if($attachments!="")
			{
				for($x=0;$x<count($attachments);$x++)
				{
					$GLOBALS['phpgw']->template->set_var('attachment_link',$attachments[$x]['link']);
					$comment = explode(";",$attachments[$x]['comment']);
					$attachment_comment = "";
					if($comment[0]>0)
					{
						$attachment_comment = date("d.m.Y", $comment[0]) . " - " . date("d.m.Y", $comment[1]);
					}
					$GLOBALS['phpgw']->template->set_var('attachment_comment',$attachment_comment);
					$GLOBALS['phpgw']->template->set_var('delete',$attachments[$x]['delLink']);
					$GLOBALS['phpgw']->template->parse('files','attachment_list',true);
				}
				//$GLOBALS['phpgw']->template->parse('report_rows','project_row',true);
			}

			$GLOBALS['phpgw']->template->set_var('ownhandle','');
			$GLOBALS['phpgw']->template->set_var('acthandle','');
			$GLOBALS['phpgw']->template->set_var('bothhandle','');
			$GLOBALS['phpgw']->template->pfp('out','view');
		}

		function delete_project()
		{
			$action		= get_var('action',array('POST','GET'));
			$pro_main	= intval(get_var('pro_main',array('POST','GET')));

			$subs	 = get_var('subs',array('POST'));
			$pa_id = intval(get_var('pa_id',array('POST','GET')));

			$link_data = array
			(
				'menuaction' => 'projects.uiprojects.tree_view_projects',
				'pro_main'   => $pro_main,
				'pa_id'      => $pa_id,
				'action'     => $action
			);

			if($pa_id==0 || !($this->boprojects->edit_perms(array('type' => 'delete','project_id' => $pa_id,'action' => $action,'main' => $pro_main))))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ($_POST['yes'])
			{
				$this->attached_files->delete_file($pa_id);
				$this->boprojects->delete_project($pa_id,$subs);
				$link_data['project_id'] = $pro_main;
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ($_POST['no'])
			{
				$link_data['menuaction'] = 'projects.uiprojects.edit_project';
				$link_data['project_id'] = $pa_id;
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			switch($action)
			{
				case 'mains':	$deleteheader = lang('are you sure you want to delete this project');
								$header = lang('delete project');
								break;
				case 'subs':	$deleteheader = lang('are you sure you want to delete this job');
								$header = lang('delete job');
								break;
			}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . $header
			//												. $this->admin_header_info();

			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('pa_delete' => 'delete.tpl'));

			//$GLOBALS['phpgw']->template->set_var('lang_subs','');
			$GLOBALS['phpgw']->template->set_var('subs', '');

			$GLOBALS['phpgw']->template->set_var('deleteheader',$deleteheader);
			$GLOBALS['phpgw']->template->set_var('lang_no',lang('No'));
			$GLOBALS['phpgw']->template->set_var('lang_yes',lang('Yes'));

			$exists = $this->boprojects->exists(array('check' => 'parent','project_id' => $pa_id));

			if ($exists)
			{
				//$GLOBALS['phpgw']->template->set_var('lang_subs',lang('Do you also want to delete all sub projects ?'));
				$GLOBALS['phpgw']->template->set_var('subs','<input type="hidden" name="subs" value="True">');
			}

			$link_data['menuaction'] = 'projects.uiprojects.delete_project';
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->pfp('out','pa_delete');
		}

		function list_budget()
		{
			$action		= get_var('action',array('POST','GET'));
			$pro_main	= get_var('pro_main',array('POST','GET'));
			$project_id	= get_var('project_id',array('POST','GET'));

			/*
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($pro_parent?lang('list budget'):lang('list budget'))
														. $this->admin_header_info();
			*/
			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('projects_list_t' => 'list_budget.tpl'));
			$GLOBALS['phpgw']->template->set_block('projects_list_t','projects_list','list');
//			$GLOBALS['phpgw']->template->set_block('projects_list_t','pcosts','pc');
			$GLOBALS['phpgw']->template->set_block('projects_list_t','project_main','main');

			$nopref = $this->boprojects->check_prefs();
			if (is_array($nopref))
			{
				$GLOBALS['phpgw']->template->set_var('pref_message',$GLOBALS['phpgw']->common->error_list($nopref));
			}
			else
			{
				$prefs = $this->boprojects->read_prefs();
			}
			$GLOBALS['phpgw']->template->set_var('currency',$prefs['currency']);

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
				'menuaction'	=> 'projects.uiprojects.list_budget',
				'pro_main'		=> $pro_main,
				'action'      => $action,
				'status'     => $this->status,
				'project_id' => $project_id
			);

			if($pro_main)
			{
				$main = $this->boprojects->read_single_project($pro_main,'budget','mains');
				$GLOBALS['phpgw']->template->set_var('title_main',$main['title']);
				$GLOBALS['phpgw']->template->set_var('main_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.view_project',
																											'action'=>'mains',
																											'project_id'=> $pro_main)));
				$GLOBALS['phpgw']->template->set_var('coordinator_main',$main['coordinatorout']);
				$GLOBALS['phpgw']->template->set_var('number_main',$main['number']);
				$GLOBALS['phpgw']->template->set_var('customer_main',$main['customerout']);
				$GLOBALS['phpgw']->template->set_var('customerorg_main',$main['customerorgout']);
				$GLOBALS['phpgw']->template->set_var('url_main',$main['url']);

				$GLOBALS['phpgw']->template->set_var('ubudget_main',sprintf("%01.2f",$main['u_budget_jobs']));
				$GLOBALS['phpgw']->template->set_var('abudget_main',trim(sprintf("%01.2f",$main['a_budget_jobs'])));

				$GLOBALS['phpgw']->template->set_var('pbudget_main',$main['budget']);
				$GLOBALS['phpgw']->template->parse('main','project_main',true);
			}

			if ($action == 'mainsubsorted')
			{
				$this->boprojects->status = false; // workaround for full tree view support
				$pro = $this->boprojects->list_projects(array('action' => $action,'project_id' => $pro_main,'page' => 'budget','limit' => false));
			}
			else
			{
				$pro = $this->boprojects->list_projects(array('action' => $action,'parent' => $pro_main,'page' => 'budget'));
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
			//				. $this->boprojects->cats->formatted_list('select','all',$this->cat_id,true) . '</select>';
			//	$GLOBALS['phpgw']->template->set_var('lang_action',lang('sub projects'));
			//}
			//else
			//{
			//	$action_list= '<select name="pro_main" onChange="this.form.submit();"><option value="">' . lang('Select main project') . '</option>' . "\n"
			//				. $this->boprojects->select_project_list(array('status' => $this->status, 'selected' => $pro_main)) . '</select>';
			//	$GLOBALS['phpgw']->template->set_var('lang_action',lang('Work hours'));
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

			$GLOBALS['phpgw']->template->set_var('sort_number',$this->nextmatchs->show_sort_order($this->sort,'p_number',$this->order,'/index.php',lang('Project ID'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('Title'),$link_data));

			$GLOBALS['phpgw']->template->set_var('sort_planned',$this->nextmatchs->show_sort_order($this->sort,'budget',$this->order,'/index.php',lang('planned'),$link_data));

// -------------- end header declaration ---------------------------------------

			for ($i=0;$i<count($pro);$i++)
			{
				if ($i==0)
				{
					$upmost_level = $pro[$i]['level'];
				}

				$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

// --------------- template declaration for list records -------------------------------------

				$link_data['project_id'] = $pro[$i]['project_id'];
				if ($action == 'mains')
				{
					$projects_url = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.list_budget',
																				'action'=>'subs',
																				'pro_main'=> $pro[$i]['project_id']));
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
					'number'        => $pro[$i]['number'],
					'sub_url'       => $projects_url,
					'title'         => $pro[$i]['title']?$pro[$i]['title']:lang('browse'),

					'p_budget'      => sprintf('%01.2f', $pro[$i]['item_planned_budget']),
					'p_budget_jobs' => sprintf('%01.2f', $pro[$i]['sum_planned_budget']),
					'u_budget'      => sprintf('%01.2f', $pro[$i]['item_used_budget']),
					'u_budget_jobs' => sprintf('%01.2f', $pro[$i]['sum_used_budget']),
					'b_budget'      => sprintf('%01.2f', $pro[$i]['item_bill_budget']),
					'b_budget_jobs' => sprintf('%01.2f', $pro[$i]['sum_bill_budget']),
					'n_budget'      => sprintf('%01.2f', $pro[$i]['item_nobill_budget']),
					'n_budget_jobs' => sprintf('%01.2f', $pro[$i]['sum_nobill_budget']),
					'a_budget'      => sprintf('%01.2f', $pro[$i]['item_avail_budget']),
					'a_budget_jobs' => sprintf('%01.2f', $pro[$i]['sum_avail_budget']),

					'list_class_sum'		=> $pro[$i]['is_leaf']?'leaf_sum':'node_sum',
					'list_class_item'		=> $pro[$i]['is_leaf']?'leaf_item':'node_item',
					'value_class_sum'		=> 'value_'.$pro[$i]['sum_budget_status'],
					'value_class_item'		=> 'value_'.$pro[$i]['item_budget_status']
				));
				$GLOBALS['phpgw']->template->parse('list','projects_list',true);

				if ($pro[$i]['level'] == $upmost_level)
				{
					$sum_p_budget += $pro[$i]['item_planned_budget'];
					$sum_a_budget += $pro[$i]['item_avail_budget'];
					$sum_u_budget += $pro[$i]['item_used_budget'];
					$sum_b_budget += $pro[$i]['item_bill_budget'];
					$sum_n_budget += $pro[$i]['item_nobill_budget'];
					$sum_p_budget_jobs += $pro[$i]['sum_planned_budget'];
					$sum_a_budget_jobs += $pro[$i]['sum_avail_budget'];
					$sum_u_budget_jobs += $pro[$i]['sum_used_budget'];
					$sum_b_budget_jobs += $pro[$i]['sum_bill_budget'];
					$sum_n_budget_jobs += $pro[$i]['sum_nobill_budget'];
				}
			}

// ------------------------- end record declaration ------------------------

// --------------- template declaration for sum  --------------------------

			$GLOBALS['phpgw']->template->set_var('lang_sum_budget',lang('sum budget'));
			$GLOBALS['phpgw']->template->set_var('sum_budget_jobs',sprintf("%01.2f",$sum_p_budget_jobs));
			$GLOBALS['phpgw']->template->set_var('sum_budget',$action=='mains'?sprintf("%01.2f",$sum_p_budget):'');

			$GLOBALS['phpgw']->template->set_var('sum_u_budget_jobs',sprintf("%01.2f",$sum_u_budget_jobs));
			$GLOBALS['phpgw']->template->set_var('sum_u_budget',$action=='mains'?sprintf("%01.2f",$sum_u_budget):'');

			$GLOBALS['phpgw']->template->set_var('sum_b_budget_jobs',sprintf("%01.2f",$sum_b_budget_jobs));
			$GLOBALS['phpgw']->template->set_var('sum_b_budget',$action=='mains'?sprintf("%01.2f",$sum_b_budget):'');

			$GLOBALS['phpgw']->template->set_var('sum_n_budget_jobs',sprintf("%01.2f",$sum_n_budget_jobs));
			$GLOBALS['phpgw']->template->set_var('sum_n_budget',$action=='mains'?sprintf("%01.2f",$sum_n_budget):'');

			$GLOBALS['phpgw']->template->set_var('sum_a_budget_jobs',sprintf("%01.2f",$sum_a_budget_jobs));
			$GLOBALS['phpgw']->template->set_var('sum_a_budget',$action=='mains'?sprintf("%01.2f",$sum_a_budget):'');

// ----------------------- end sum declaration ----------------------------

			$this->save_sessiondata($action);
			$GLOBALS['phpgw']->template->pfp('out','projects_list_t',true);
		}


// ---- MILESTONES -----

		function project_mstones()
		{
			$action		= get_var('action',array('GET','POST'));
			$project_id	= get_var('project_id',array('GET','POST'));
			$values		= get_var('values',array('POST'));
			$s_id		= get_var('s_id',array('GET','POST'));
			$edate		= get_var('edate',array('POST','GET'));

			if(!$_POST['save'] && !$_GET['delete'] && !$_POST['done'] && !$_GET['edit'])
			{
				$referer = get_var('referer',array('POST'));
			}
			if($_POST['save'] || $_GET['delete'] || $_POST['done'] || $_GET['back'] || $_GET['edit'])
			{
				$referer = get_var('referer',array('GET'));
			}
			if(!$referer)  //$_POST['back'] && !$_POST['done'] && !$_POST['edit'])
			{
				$referer = $_SERVER['HTTP_REFERER'];
			}

			//echo 'REFERER: ' . $referer;

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.project_mstones',
				'action'		=> $action,
				'project_id'	=> $project_id,
				'referer'		=> $referer
			);

			$jscal = CreateObject('phpgwapi.jscalendar');

			if(is_array($edate))
			{
				$end_array	= $jscal->input2date($edate['str']);
				$end_val	= $end_array['raw'];
			}

			if ($_POST['save'])
			{
				$values['s_id']			= $values['new']?'':$s_id;
				$values['project_id']	= $project_id;
				$values['edate']		= $end_val;
				$error = $this->boprojects->check_mstone($values);
				if(is_array($error))
				{
					$message = $GLOBALS['phpgw']->common->error_list($error);
				}
				else
				{
					$this->boprojects->save_mstone($values);
					$message = lang('milestone has been saved');
				}
			}

			if ($_POST['done'])
			{
				unset($jscal);
				$link = array
				(
					'menuaction'	=> 'projects.uiprojects.edit_project',
					'action'		=> $action,
					'project_id'	=> $project_id
				);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link);
				//Header('Location: ' . $referer);
			}

			if ($_GET['delete'])
			{
				$this->boprojects->delete_item(array('id' => $s_id));
				$message = lang('milestone has been deleted');
			}

			if($_GET['edit'])
			{
				$values = $this->boprojects->get_single_mstone($s_id);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('edit milestones');
			//$this->display_app_header();
			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('mstone_list_t' => 'list_mstones.tpl'));
			$GLOBALS['phpgw']->template->set_block('mstone_list_t','mstone_list','list');
			$GLOBALS['phpgw']->template->set_block('mstone_list_t','project_data','pro');

			$pro = $this->boprojects->read_single_project($project_id);
			$GLOBALS['phpgw']->template->set_var('title_pro',$pro['title']);
			$GLOBALS['phpgw']->template->set_var('pro_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.view_project',
																									'action'=> ($pro['level']==0?'mains':'subs'),
																									'project_id'=> $project_id)));
			$GLOBALS['phpgw']->template->set_var('coordinator_pro',$pro['coordinatorout']);
			$GLOBALS['phpgw']->template->set_var('number_pro',$pro['number']);
			$GLOBALS['phpgw']->template->set_var('customer_pro',$pro['customerout']);
			$GLOBALS['phpgw']->template->set_var('customerorg_pro',$pro['customerorgout']);
			$GLOBALS['phpgw']->template->set_var('url_pro',$pro['url']);
			$GLOBALS['phpgw']->template->set_var('sdate',$pro['sdate_formatted']);
			$GLOBALS['phpgw']->template->set_var('edate',$pro['edate_formatted']);


			$GLOBALS['phpgw']->template->parse('pro','project_data',true);

			$GLOBALS['phpgw']->template->set_var('message',$message);
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$mstones = $this->boprojects->get_mstones($project_id);

			if(is_array($mstones))
			{
				for($i=0;$i<count($mstones);$i++)
				{
					$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

					$link_data['s_id']			= $mstones[$i]['s_id'];
					$link_data['edit']			= true;

					$GLOBALS['phpgw']->template->set_var(array
					(
						'datedue'	=> $this->boprojects->formatted_edate($mstones[$i]['edate'],true,'ms'),
						'edit_url'	=> $GLOBALS['phpgw']->link('/index.php',$link_data),
						'title'		=> $mstones[$i]['title']
					));
					$GLOBALS['phpgw']->template->set_var('edit_img','<img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','edit')
																	. '" border="0" title="' . lang('edit') . '">');
					unset($link_data['edit']);

					if ($this->boprojects->edit_perms(array('action' => $action,'main' => $pro['main'],'parent' => $pro['parent'],'type' => 'delete',
															'coordinator' => $pro['coordinator'])))
					{
						$link_data['menuaction']	= 'projects.uiprojects.project_mstones';
						$link_data['delete']		= true;

						$GLOBALS['phpgw']->template->set_var('delete_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
						$GLOBALS['phpgw']->template->set_var('delete_img','<img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','delete')
																		. '" border="0" title="' . lang('delete') . '">');
						unset($link_data['delete']);
					}
					$GLOBALS['phpgw']->template->parse('list','mstone_list',true);
				}
			}
			$GLOBALS['phpgw']->template->set_var('old_edate',$values['edate']);
			$GLOBALS['phpgw']->template->set_var('s_id',$values['s_id']);
			$GLOBALS['phpgw']->template->set_var('lang_new',lang('new milestone'));
			$GLOBALS['phpgw']->template->set_var('lang_save_mstone',lang('save milestone'));
			$GLOBALS['phpgw']->template->set_var('new_checked',$values['new']?' checked':'');
			$GLOBALS['phpgw']->template->set_var('title',$GLOBALS['phpgw']->strip_html($values['title']));

			$end = $end_val?$end_val:($values['edate']?mktime(12,0,0,date('m',$values['edate']),date('d',$values['edate']),date('Y',$values['edate'])):mktime(12,0,0,date('m'),date('d'),date('Y')));
			$GLOBALS['phpgw']->template->set_var('end_date_select',$jscal->input('edate[str]',$end));

			$GLOBALS['phpgw']->template->pfp('out','mstone_list_t',true);
		}

		function assign_employee_roles()
		{
			$action		= get_var('action',array('GET','POST'));
			$r_id		= get_var('r_id',array('GET','POST'));
			$project_id	= get_var('project_id',array('GET','POST'));
			$values		= get_var('values',array('POST'));

			if(!$_POST['save'] && !$_GET['delete'] && !$_POST['done'] && !$_GET['edit'])
			{
				$referer = get_var('referer',array('POST'));
			}
			if($_POST['save'] || $_GET['delete'] || $_POST['done'] || $_GET['edit'])
			{
				$referer = get_var('referer',array('GET'));
			}
			if(!$referer)  //$_POST['back'] && !$_POST['done'] && !$_POST['edit'])
			{
				$referer = $_SERVER['HTTP_REFERER'];
			}

			//echo 'REFERER: ' . $referer;

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.assign_employee_roles',
				'action'		=> 'role',
				'project_id'	=> $project_id,
				'referer'		=> $referer
			);

			if ($_POST['save'])
			{
				$values['project_id']	= $project_id;
				$this->boprojects->save_employee_role($values);
				$GLOBALS['phpgw']->template->set_var('message',lang('assignment has been saved'));
			}

			if ($_POST['done'])
			{
				$link = array
				(
					'menuaction'	=> 'projects.uiprojects.edit_project',
					'action'		=> 'mains',
					'project_id'	=> $project_id
				);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link);
				//Header('Location: ' . $referer);
			}

			if ($_GET['delete'])
			{
				$this->boprojects->delete_item(array('id' => $r_id,'action' => 'emp_role'));
				$message = lang('assignment has been deleted');
			}

			if($_GET['edit'])
			{
				list($values) = $this->boprojects->get_employee_roles(array('project_id' => $project_id,'account_id' => $_GET['account_id']));
			}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('assign roles and events')
			//											. $this->admin_header_info();

			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('role_list_t' => 'form_emp_roles.tpl'));
			$GLOBALS['phpgw']->template->set_block('role_list_t','role_list','list');
			$GLOBALS['phpgw']->template->set_block('role_list_t','project_data','pro');

			$pro = $this->boprojects->read_single_project($project_id);
			$GLOBALS['phpgw']->template->set_var('title_pro',$pro['title']);
			$GLOBALS['phpgw']->template->set_var('pro_url',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.view_project',
																									'action'=> ($pro['level']==0?'mains':'subs'),
																									'project_id'=>$project_id)));
			$GLOBALS['phpgw']->template->set_var('coordinator_pro',$pro['coordinatorout']);
			$GLOBALS['phpgw']->template->set_var('number_pro',$pro['number']);
			$GLOBALS['phpgw']->template->set_var('customer_pro',$pro['customerout']);
			$GLOBALS['phpgw']->template->set_var('customerorg_pro',$pro['customerorgout']);
			$GLOBALS['phpgw']->template->set_var('url_pro',$pro['url']);
			$GLOBALS['phpgw']->template->parse('pro','project_data',true);

			$GLOBALS['phpgw']->template->set_var('message',$message);

			$roles = $this->boprojects->get_employee_roles(array('project_id' => $project_id,'formatted' => true));

			$GLOBALS['phpgw']->template->set_var('sort_name',lang('employee'));
			$GLOBALS['phpgw']->template->set_var('sort_role',lang('role'));

			if (!$this->boprojects->edit_perms(array('action' => $action,'coordinator' => $pro['coordinator'],'main' => $pro['main'],'parent' => $pro['parent'],
													'type' => 'delete')))
			{
				$delete_rights = 'no';
			}

			$emps	= $this->boprojects->get_acl_for_project($project_id);
			$co		= $this->boprojects->return_value('co',$project_id);

			if (is_array($roles))
			{
				foreach($roles as $role)
				{
					$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);

					$GLOBALS['phpgw']->template->set_var('emp_name',$role['emp_name']);

					if(is_array($emps))
					{
						if(in_array($role['account_id'],$emps) || $co == $role['account_id'])
						{
							$link_data['account_id']	= $role['account_id'];
							$link_data['edit']			= true;
							$GLOBALS['phpgw']->template->set_var('edit_link','<a href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) . '">');
							$GLOBALS['phpgw']->template->set_var('end_link','</a>');
							$GLOBALS['phpgw']->template->set_var('edit_img','<img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','edit')
																	. '" . border="0" title="' . lang('edit') . '"></a>');
							$link_data['edit']			= False;

							$link_data['r_id'] = $role['r_id'];
							$link_data['delete'] = true;
							$GLOBALS['phpgw']->template->set_var('delete_role',($delete_rights=='no'?'':'<a href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) . '">'));
							$link_data['delete'] = False;
							$GLOBALS['phpgw']->template->set_var('delete_img',($delete_rights=='no'?'':'<img src="' . $GLOBALS['phpgw']->common->image('phpgwapi','delete')
																	. '" . border="0" title="' . lang('delete') . '"></a>'));
						}
					}
					$GLOBALS['phpgw']->template->set_var('role_name',$role['role_name']);


					$GLOBALS['phpgw']->template->set_var('events',$role['events']);
					$GLOBALS['phpgw']->template->parse('list','role_list',true);
				}
			}

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('role_select',$this->boprojects->action_format($values['role_id']));
			$GLOBALS['phpgw']->template->set_var('event_select',$this->boprojects->action_format($values['events'],'event'));
			$GLOBALS['phpgw']->template->set_var('lang_select_role',lang('select role'));
			$GLOBALS['phpgw']->template->set_var('emp_select',$this->ui_base->employee_format(array('type' => 'selectbox','project_id' => $project_id,'selected' => $values['account_id']
																							,'project_only' => true,'admins_included' => true)));
			$GLOBALS['phpgw']->template->set_var('lang_assign',lang('assign'));
			$GLOBALS['phpgw']->template->pfp('out','role_list_t',true);
		}

		function report()
		{
			$project_id	= get_var('project_id',array('POST','GET'));
			$generated	= get_var('generated',array('POST','GET'));
			$sdate		= get_var('sdate',array('POST','GET'));
			$edate		= get_var('edate',array('POST','GET'));
			$employee	= get_var('employee',array('POST','GET'));
			$template	= get_var('template',array('POST','GET'));
			$deleted	= get_var('deleted',array('POST','GET'));
			$hourid		= get_var('hourid',array('POST','GET'));
			$forward	= get_var('forward',array('POST','GET'));
			$back		= get_var('back',array('POST','GET'));
			$filename	= get_var('filename',array('POST','GET'));

			$this->reportOOo = CreateObject('projects.reportOOo');

			$jscal = CreateObject('phpgwapi.jscalendar');

			if($_POST['yes'] || $_POST['no'])
			{
				if($_POST['yes'])
				{
					$link_data = array(
						'menuaction'	=> 'projects.uiprojects.report',
						'project_id'  => $project_id,
						'sdate'		    => $sdate,
						'edate'		    => $edate
					);

					$generated = $this->reportOOo->generate($project_id, $sdate, $edate, $hourid, $template, $employee, $filename);
					if(!$generated)
					{
						$link_data['generated'] = 0;
					}
					else
					{
						$link_data['generated'] = 1;
						$link_data['menuaction'] = 'projects.uiprojects.view_report_list';
					}
					$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
				}

				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('generate activity report')
			//											. $this->admin_header_info();

			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('report' => 'report_wizard.tpl'));


			if($forward)
			{
				$GLOBALS['phpgw']->template->set_block('report', 'list_activities', 'listhandle');
				$GLOBALS['phpgw']->template->set_block('report', 'activities_handle', 'activitieshandle');
				$GLOBALS['phpgw']->template->set_block('report', 'details_handle', 'detailshandle');
				$GLOBALS['phpgw']->template->set_var('details_handle','');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_block('report', 'details_handle', 'detailshandle');
				$GLOBALS['phpgw']->template->set_block('report', 'list_activities', 'listhandle');
				$GLOBALS['phpgw']->template->set_block('report', 'activities_handle', 'activitieshandle');
				$GLOBALS['phpgw']->template->set_var('activities_handle','');
				$GLOBALS['phpgw']->template->set_var('list_activities','');
			}

			if($generated === '0')
			{
				$GLOBALS['phpgw']->template->set_var('error',lang('no activities in this period!'));
			}
			elseif($generated === '1')
			{
				$GLOBALS['phpgw']->template->set_var('error',lang('activity report generated'));
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('error','');
			}

			if($deleted)
			{
				$GLOBALS['phpgw']->template->set_var('error',lang('activity report deleted'));
			}

			$pro = $this->boprojects->read_single_project($project_id);

			$start = $sdate?$sdate:mktime(0,0,0,date('m'),1,date('Y'));
			$end = $edate?$edate:mktime(23,59,59,date('m')+1,0,date('Y'));

			$GLOBALS['phpgw']->template->set_var('lang_select_data',lang('Please choose details for your new activity report'));
			$GLOBALS['phpgw']->template->set_var('period',lang('please choose the period:'));
			$GLOBALS['phpgw']->template->set_var('lang_no',lang('Cancel'));
			$GLOBALS['phpgw']->template->set_var('lang_yes',lang('generate activity report'));
			$GLOBALS['phpgw']->template->set_var('start_date_select',$jscal->input('sdate',$start));
			$GLOBALS['phpgw']->template->set_var('end_date_select',$jscal->input('edate',$end));
			$GLOBALS['phpgw']->template->set_var('lang_employee',lang('Employee'));
			$GLOBALS['phpgw']->template->set_var('template_name',lang('Select Template'));
			$GLOBALS['phpgw']->template->set_var('template_select','<select name="template"><option>Berlin</option><option>B&ouml;blingen</option><option>Dresden</option><option>D&uuml;sseldorf</option><option>Frankfurt</option><option>Hamburg</option><option>Hannover</option><option>M&uuml;nchen</option></select>');
			$GLOBALS['phpgw']->template->set_var('lang_date',lang('Date'));
			$GLOBALS['phpgw']->template->set_var('lang_description',lang('Description'));
			$GLOBALS['phpgw']->template->set_var('lang_duration',lang('Duration'));
			$GLOBALS['phpgw']->template->set_var('lang_activities',lang('Activities'));
			$GLOBALS['phpgw']->template->set_var('lang_forward',lang('Forward'));

			if($this->boprojects->edit_perms(array('coordinator' => $pro['coordinator'])))
			{
				if(!$employee)
				{
					$employee = $GLOBALS['phpgw_info']['user']['account_id'];
				}

				$GLOBALS['phpgw']->template->set_var('employee','<select name="employee">' . $this->ui_base->employee_format(array('project_only'		=> true,
																																	'admins_included'	=> true,
																																	'project_id'		=> $project_id,
																																	'selected'			=> $employee?$employee:$this->account
				                                                                                                   ))
																. '</select>');
			}
			else
			{
				$employee = $GLOBALS['phpgw_info']['user']['account_id'];
				$GLOBALS['phpgw']->template->set_var('employee','<input type="hidden" name="employee" value="' . $employee . '">' . $GLOBALS['phpgw_info']['user']['fullname']);
			}

			$GLOBALS['phpgw']->template->set_var('lang_activity_reports',lang('Activity reports'));

			if($forward)
			{
				$start_array = $jscal->input2date($sdate);
				$start = mktime(0,0,0,$start_array['month'],$start_array['day'],$start_array['year']);

				$end_array = $jscal->input2date($edate);
				$end   = mktime(23,59,59,$end_array['month'],$end_array['day'],$end_array['year']);

				$filename = "TB_" . $this->accounts->id2name($employee) . "_" . date("Ymd",$start) . "-" . date("Ymd",$end);
				$GLOBALS['phpgw']->template->set_var('filename',$filename);
				$GLOBALS['phpgw']->template->set_var('lang_filename',lang('Filename'));

				$activities = $this->bohours->get_emp_activities($project_id, $start, $end, $employee);

				for($i=0;$i<count($activities);$i++)
				{
					$time = ((int)($activities[$i]['duration'] / 60)) . ":" . (($activities[$i]['duration'] % 60) == "0" ? "00" : sprintf("%02d",($activities[$i]['duration'] % 60)));
					$GLOBALS['phpgw']->template->set_var('id',$i);
					$GLOBALS['phpgw']->template->set_var('activity_date',date("d.m.Y", $activities[$i]['date']));
					$GLOBALS['phpgw']->template->set_var('activity_descr',$activities[$i]['descr']);
					$GLOBALS['phpgw']->template->set_var('activity_duration',$time);
					$this->nextmatchs->template_alternate_row_class($GLOBALS['phpgw']->template);
					$GLOBALS['phpgw']->template->parse('list','list_activities',true);
				}
			}

			if(!$back)
			{
				$link_data = array
				(
					'menuaction'	=> 'projects.uiprojects.report',
					'project_id'	=> $project_id,
					'template'		=> $template,
					'sdate'			=> $start,
					'edate'			=> $end,
					'employee'		=> $employee,
					'hourid'		=> $hourid
				);
			}
			else
			{
				$link_data = array
				(
					'menuaction'	=> 'projects.uiprojects.report',
					'project_id'	=> $project_id
				);
			}

			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));

			if($forward)
			{
				$GLOBALS['phpgw']->template->parse('activities','activities_handle',true);
			}
			else
			{
				$GLOBALS['phpgw']->template->parse('details','details_handle',true);
			}

			$GLOBALS['phpgw']->template->pfp('out','report');

		}

		function view_report_list()
		{
			$project_id	= get_var('project_id',array('POST','GET'));
			$generated	= get_var('generated',array('POST','GET'));
			$sdate		= get_var('sdate',array('POST','GET'));
			$edate		= get_var('edate',array('POST','GET'));
			$employee	= get_var('employee',array('POST','GET'));
			$template	= get_var('template',array('POST','GET'));
			$deleted	= get_var('deleted',array('POST','GET'));

			if($_POST['yes'])
			{
				$link_data = array(
						'menuaction'  => 'projects.uiprojects.report',
						'project_id'  => $project_id,
						'employee'    => $employee);
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}


			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('report' => 'report_list.tpl'));
			$GLOBALS['phpgw']->template->set_block('report', 'project_row', 'project_row');
			$GLOBALS['phpgw']->template->set_block('report', 'attachment_list', 'listhandle');

			if($deleted)
			{
				$GLOBALS['phpgw']->template->set_var('error',lang('activity report deleted'));
			}

			if($generated)
			{
				$GLOBALS['phpgw']->template->set_var('error',lang('activity report generated'));
			}

			$pro = $this->boprojects->read_single_project($project_id);

			$start = $sdate?$sdate:mktime(0,0,0,date('m'),1,date('Y'));
			$end = $edate?$edate:mktime(23,59,59,date('m')+1,0,date('Y'));

			$GLOBALS['phpgw']->template->set_var('period',lang('please choose the period'));
			$GLOBALS['phpgw']->template->set_var('lang_no',lang('Cancel'));
			$GLOBALS['phpgw']->template->set_var('lang_yes',lang('generate activity report'));
			$GLOBALS['phpgw']->template->set_var('lang_filename',lang('Filename'));
			$GLOBALS['phpgw']->template->set_var('lang_period',lang('Period'));
			$GLOBALS['phpgw']->template->set_var('lang_employee',lang('Employee'));
			$GLOBALS['phpgw']->template->set_var('lang_reports_for',lang('Activity reports for'));
			//$GLOBALS['phpgw']->template->set_var('template_name',lang('Select Template'));
			//$GLOBALS['phpgw']->template->set_var('template_select','<select name="template"><option>Berlin</option><option>B&ouml;blingen</option><option>Dresden</option><option>D&uuml;sseldorf</option><option>Frankfurt</option><option>Hamburg</option><option>Hannover</option><option>M&uuml;nchen</option></select>');

			if($this->boprojects->edit_perms(array('coordinator' => $pro['coordinator'])))
			{
				if(!$employee)
				{
					$employee = $GLOBALS['phpgw_info']['user']['account_id'];
				}

				$GLOBALS['phpgw']->template->set_var('employee','<select name="employee" onChange="this.form.submit();">' . $this->ui_base->employee_format(array('project_only'		=> true,
																																	'admins_included'	=> true,
																																	'project_id'		=> $project_id,
																																	'selected'			=> $employee?$employee:$this->account
				                                                                                                   ))
																. '</select>');
			}
			else
			{
				$employee = $GLOBALS['phpgw_info']['user']['account_id'];
				$GLOBALS['phpgw']->template->set_var('employee','<input type="hidden" name="employee" value="' . $employee . '">' . $GLOBALS['phpgw_info']['user']['fullname']);
			}

			$params = array(
					'project_id' => $project_id,
					'filter' => 'employee',
					'status' => 'all',
					'limit' => false,
					'order' => 'end_date',
					'employee' => $account_id
				);

			$subs = $this->boprojects->list_projects(array('action' => 'mainsubsorted','project_id' => $project_id));

			for ($i=0;$i<count($subs);$i++)
			{
				$pr_name = $subs[$i]['title'];
				$attachments = $this->attached_files->get_files($subs[$i]['project_id'],true,true,$employee);
				if($attachments!="")
				{
					$GLOBALS['phpgw']->template->set_var('pr_name',$pr_name);
					$GLOBALS['phpgw']->template->set_var('files','');
					for($x=0;$x<count($attachments);$x++)
					{
						$GLOBALS['phpgw']->template->set_var('attachment_link',$attachments[$x]['link']);
						$comment = explode(";",$attachments[$x]['comment']);
						$attachment_comment = "";
						if($comment[0]>0)
						{
							$attachment_comment = date("d.m.Y", $comment[0]) . " - " . date("d.m.Y", $comment[1]);
						}
						$GLOBALS['phpgw']->template->set_var('attachment_comment',$attachment_comment);
						$GLOBALS['phpgw']->template->set_var('delete',$attachments[$x]['delLink']);
						$GLOBALS['phpgw']->template->parse('files','attachment_list',true);
					}
					$GLOBALS['phpgw']->template->parse('report_rows','project_row',true);
				}
			}

			$GLOBALS['phpgw']->template->set_var('lang_activity_reports',lang('Activity reports'));

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.view_report_list',
				'project_id'	=> $project_id
			);
			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('lang_new',lang('New Report'));
			$GLOBALS['phpgw']->template->set_var('project_row','');
			$GLOBALS['phpgw']->template->set_var('listhandle','');

			$GLOBALS['phpgw']->template->pfp('out','report');

		}


		function export_cost_accounting()
		{
			$export = get_var('export',array('POST','GET'));
			$statistic = get_var('statistic',array('POST','GET'));

			if($export)
			{
				$action = 'export';
			}
			elseif($statistic)
			{
				$action = 'statistic';
			}
			else
			{
				$action = 'select';
			}

			if ($action == 'export')
			{
				$month = get_var('month',array('POST','GET'));
				$year = get_var('year',array('POST','GET'));
				$location_id = get_var('location_id',array('POST','GET'));
				if($month && $year && $location_id)
				{
					$data = $this->boprojects->get_cost_accounting_diamant($month, $year, $location_id);
					header("Content-Type: text/plain");
					echo $data;
					$GLOBALS['phpgw']->common->phpgw_exit();
				}
			}

			if ($action == 'statistic')
			{
				$month = get_var('month',array('POST','GET'));
				$year = get_var('year',array('POST','GET'));
				$location_id = get_var('location_id',array('POST','GET'));
				if($month && $year && $location_id)
				{
					$link_data['menuaction']  = 'projects.uistatistics.list_users_worktimes';
					$link_data['sdate']       = mktime(0,0,0,$month,1,$year);
					$link_data['edate']       = mktime(23,59,59,$month+1,0,$year);
					$link_data['values[employee]'] = 0;
					$link_data['location_id'] = $location_id;
					$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
				}
			}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($pro_parent?lang('list budget'):lang('export diamant accounting'))
			//											. $this->admin_header_info();

			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('export_diamant_t' => 'export_diamant.tpl'));

			$link_data = array
			(
				'menuaction' => 'projects.uiprojects.export_cost_accounting'
			);
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->set_var('text',lang('Select month, year and location'));

			$current_m = date('m', time());
			$current_y = date('Y', time());

			if($current_m == 1)
			{
				$select_m = 12;
				$select_y = $current_y-1;
			}
			else
			{
				$select_m = $current_m-1;
				$select_y = $current_y;
			}

			$optionlist_m = '';
			for($m=1; $m<=12; ++$m)
			{
				if($m == $select_m)
					$optionlist_m .= '<option value="'.$m.'" selected>'.$m.'</option>'."\n";
				else
					$optionlist_m .= '<option value="'.$m.'"/>'.$m.'</option>'."\n";
			}

			$optionlist_y = '';
			for($y=2004; $y<=2010; ++$y)
			{
				if($y == $select_y)
					$optionlist_y .= '<option value="'.$y.'" selected>'.$y.'</option>'."\n";
				else
					$optionlist_y .= '<option value="'.$y.'"/>'.$y.'</option>'."\n";
			}

			$optionlist_l = '';
			$locations = $this->boprojects->soconfig->get_locations();
			foreach($locations as $location)
			{
				$optionlist_l .= '<option value="'.$location['location_id'].'">'.$location['location_name'].'</option>';
			}

			$selectbox_m = '<select name="month">'.$optionlist_m.'</select>';
			$selectbox_y = '<select name="year">'.$optionlist_y.'</select>';
			$selectbox_l = '<select name="location_id">'.$optionlist_l.'</select>';

			$GLOBALS['phpgw']->template->set_var('selectbox_m',$selectbox_m);
			$GLOBALS['phpgw']->template->set_var('selectbox_y',$selectbox_y);
			$GLOBALS['phpgw']->template->set_var('selectbox_l',$selectbox_l);
			$GLOBALS['phpgw']->template->set_var('button_statistic','<input type="submit" name="statistic" value="' . lang('user statistic') . '">');
			$GLOBALS['phpgw']->template->set_var('button_export','<input type="submit" name="export" value="' . lang('Export cost_accounting') . '">');
			$GLOBALS['phpgw']->template->pfp('out','export_diamant_t',true);
		}

		function export_cost_accounting_A()
		{
			$export = get_var('export',array('POST','GET'));
			$statistic = get_var('statistic',array('POST','GET'));

			if($export)
			{
				$action = 'export';
			}
			elseif($statistic)
			{
				$action = 'statistic';
			}
			else
			{
				$action = 'select';
			}

			if ($action == 'export')
			{
				$month = get_var('month',array('POST','GET'));
				$year = get_var('year',array('POST','GET'));
				$location_id = get_var('location_id',array('POST','GET'));
				if($month && $year && $location_id)
				{
					$data = $this->boprojects->get_cost_accounting_diamant_A($month, $year, $location_id);
					header("Content-Type: text/plain");
					echo $data;
					$GLOBALS['phpgw']->common->phpgw_exit();
				}
			}

			if ($action == 'statistic')
			{
				$month = get_var('month',array('POST','GET'));
				$year = get_var('year',array('POST','GET'));
				$location_id = get_var('location_id',array('POST','GET'));
				if($month && $year && $location_id)
				{
					$link_data['menuaction']  = 'projects.uistatistics.list_users_worktimes';
					$link_data['sdate']       = mktime(0,0,0,$month,1,$year);
					$link_data['edate']       = mktime(23,59,59,$month+1,0,$year);
					$link_data['values[employee]'] = 0;
					$link_data['location_id'] = $location_id;
					$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
				}
			}

			//$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . ($pro_parent?lang('list budget'):lang('export diamant accounting'))
			//											. $this->admin_header_info();

			$this->ui_base->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('export_diamant_t' => 'export_diamant.tpl'));

			$link_data = array
			(
				'menuaction' => 'projects.uiprojects.export_cost_accounting_A'
			);
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->set_var('text',lang('Select month, year and location'));

			$current_m = date('m', time());
			$current_y = date('Y', time());

			if($current_m == 1)
			{
				$select_m = 12;
				$select_y = $current_y-1;
			}
			else
			{
				$select_m = $current_m-1;
				$select_y = $current_y;
			}

			$optionlist_m = '';
			for($m=1; $m<=12; ++$m)
			{
				if($m == $select_m)
					$optionlist_m .= '<option value="'.$m.'" selected>'.$m.'</option>'."\n";
				else
					$optionlist_m .= '<option value="'.$m.'"/>'.$m.'</option>'."\n";
			}

			$optionlist_y = '';
			for($y=2004; $y<=2010; ++$y)
			{
				if($y == $select_y)
					$optionlist_y .= '<option value="'.$y.'" selected>'.$y.'</option>'."\n";
				else
					$optionlist_y .= '<option value="'.$y.'"/>'.$y.'</option>'."\n";
			}

			$optionlist_l = '';
			$locations = $this->boprojects->soconfig->get_locations();
			foreach($locations as $location)
			{
				$optionlist_l .= '<option value="'.$location['location_id'].'">'.$location['location_name'].'</option>';
			}

			$selectbox_m = '<select name="month">'.$optionlist_m.'</select>';
			$selectbox_y = '<select name="year">'.$optionlist_y.'</select>';
			$selectbox_l = '<select name="location_id">'.$optionlist_l.'</select>';

			$GLOBALS['phpgw']->template->set_var('selectbox_m',$selectbox_m);
			$GLOBALS['phpgw']->template->set_var('selectbox_y',$selectbox_y);
			$GLOBALS['phpgw']->template->set_var('selectbox_l',$selectbox_l);
			$GLOBALS['phpgw']->template->set_var('button_statistic','<input type="submit" name="statistic" value="' . lang('user statistic') . '">');
			$GLOBALS['phpgw']->template->set_var('button_export','<input type="submit" name="export" value="' . lang('Export cost_accounting') . '">');
			$GLOBALS['phpgw']->template->pfp('out','export_diamant_t',true);
		}

		function view_employee_activity()
		{
			$budget_modus = get_var('budgetmodus', array('POST', 'GET')) ? get_var('budgetmodus', array('POST', 'GET')) : 'h';

			$GLOBALS['phpgw']->template->set_file(array('controlling' => 'project_activity.tpl'));

			$jscal = CreateObject('phpgwapi.jscalendar');	// before phpgw_header() !!!
			$start_array	= $jscal->input2date($_REQUEST['datum']['start']);
			$end_array	  = $jscal->input2date($_REQUEST['datum']['end']);

			$GLOBALS['phpgw']->js->validate_file('api', 'wz_tooltip');

			//$values	= get_var('view', array('POST', 'GET'));
			$GLOBALS['phpgw']->template->set_var('l_update_view', lang('Update'));

			$start = $start_array['raw'] > 1 ? $start_array['raw'] : mktime(0,0,0,date('m'),1,date('Y'));
			$end   = $end_array['raw'] > 1 ?   $end_array['raw']   : mktime(23,59,59,date('m')+1,0,date('Y'));

			$GLOBALS['phpgw']->template->set_var('sdate_select',$jscal->input('datum[start]', $start));
			$GLOBALS['phpgw']->template->set_var('edate_select',$jscal->input('datum[end]', $end));

			$projectID = get_var('project_id', array('POST','GET'));

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.view_employee_activity',
				'project_id'	=> $projectID
			);
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$this->bohours = CreateObject('projects.boprojecthours');
			$matrix = $this->bohours->build_acitivity_matrix($projectID, $start, $end);

			for($i=0; $i < count($matrix); $i++)
			{
				if($i == 0) // head
				{
					$output .= '<thead>';
					$output .= '<tr>';
					$output .= '<td style="padding-left: 5px; background-color: #d3dce3; border-bottom: 2px solid #808080">';
					$output .= lang('Projects');
					$output .= '</td>';
					foreach($matrix[0]['employee'] as $key => $value)
					{
						$allemployees[] = $key;
						$output .= '<td style="background-color: #d3dce3; font-weight: bold; height: 30px; padding-left: 5px; padding-right: 5px; border-bottom: 2px solid #808080; vertical-align: bottom">';
						$GLOBALS['phpgw']->accounts->get_account_name($key, $lid,$fname,$lname);
						$output .= $GLOBALS['phpgw']->common->display_fullname($lid, $fname, $lname);
						$output .= '</td>';
					}
					$output .= '<td style="font-weight: bold; background-color: #d3dce3; border-bottom: 2px solid #808080">';
					$output .= lang('Total');
					$output .= '</td>';
					$output .= '</tr>';
					$output .= '</thead>';
				}

				$output .= '<tbody>';
				$output .= '<tr>';
				$output .= '<td style="font-weight: bold; white-space: nowrap; background-color: #d3dce3; vertical-align: top; border-bottom: 1px solid #808080; padding-left: 5px; padding-right: 5px">';
				$output .= $matrix[$i]['project']['title'];
				$output .= '</td>';
				for($j=0; $j < count($allemployees); $j++)
				{
					$colstyle = $j % 2 ? 'background-color: #eeeeee' : 'background-color: #FFFFFF';
					$output .= '<td style="'.$colstyle.'; vertical-align: bottom; border-bottom: 1px solid #808080">';
					foreach($matrix[$i]['employee'] as $xkey => $xvalue)
					{
						if($allemployees[$j] == $xkey)
						{
							$output .= '<table align="right" cellspacing="0" cellpadding="0" style="font-size: 10px">';
							$totalminutes = 0;
							for($k=0; $k < count($xvalue); $k++)
							{
								$tooltiptitle = $xvalue[$k]['description'];

								$tooltip  = '<table width=100%>';
								$tooltip .= '<tr>';
								$tooltip .= '<td>';
								$tooltip .= lang('Date').':';
								$tooltip .= '</td>';
								$tooltip .= '<td align=right>';
								$tooltip .= $xvalue[$k]['date'];
								$tooltip .= '</td>';
								$tooltip .= '</tr>';
								$tooltip .= '<tr>';
								$tooltip .= '<td>';
								$tooltip .= lang('Begin').':';
								$tooltip .= '</td>';
								$tooltip .= '<td align=right>';
								$tooltip .= $xvalue[$k]['start'];
								$tooltip .= '</td>';
								$tooltip .= '</tr>';
								$tooltip .= '<tr>';
								$tooltip .= '<td>';
								$tooltip .= lang('End').':';
								$tooltip .= '</td>';
								$tooltip .= '<td align=right>';
								$tooltip .= $xvalue[$k]['end'];
								$tooltip .= '</td>';
								$tooltip .= '</tr>';
								$tooltip .= '<tr>';
								$tooltip .= '<td>';
								$tooltip .= lang('Hours').':';
								$tooltip .= '</td>';
								$tooltip .= '<td align=right>';
								$tooltip .= $xvalue[$k]['minutesout'];
								$tooltip .= '</td>';
								$tooltip .= '</tr>';
								$tooltip .= '<tr>';
								$tooltip .= '<td>';
								$tooltip .= lang('Travel time').':';
								$tooltip .= '</td>';
								$tooltip .= '<td align=right>';
								$tooltip .= $xvalue[$k]['t_minutesout'];
								$tooltip .= '</td>';
								$tooltip .= '</tr>';
								$tooltip .= '<tr>';
								$tooltip .= '<td>';
								$tooltip .= lang('Status').':';
								$tooltip .= '</td>';
								$tooltip .= '<td align=right>';
								$tooltip .= $xvalue[$k]['statusout'];
								$tooltip .= '</td>';
								$tooltip .= '</tr>';
								$tooltip .= '</table>';

								$output .= '<tr>';
								$output .= '<td>';
								$output .= "<div style=\"text-align: right;\" onMouseover=\"this.T_TITLE='$tooltiptitle'; this.T_WIDTH=250; return escape('$tooltip'); \" >";
								if($budget_modus == 'h')
								{
									$output .= $this->bohours->format_minutes($xvalue[$k]['minutes']+$xvalue[$k]['t_minutes']).'<br>';
								}
								else
								{
									$output .= number_format(($xvalue[$k]['minutes']+$xvalue[$k]['t_minutes']) / 60 * $matrix[$i]['project']['budget_factor'], 0, ',', '.'). ' &euro;';
								}
								$totalminutes += $xvalue[$k]['minutes']+$xvalue[$k]['t_minutes'];
								$output .= '</div>';
								$output .= '</td>';
								$output .= '</tr>';
							}
							if($totalminutes > 0)
							{
								$matrix[0]['employee'][$xkey]['totalhours'] += $totalminutes;
								$matrix[0]['employee'][$xkey]['totalcosts'] += $totalminutes / 60 * $matrix[$i]['project']['budget_factor'];
								$matrix[$i]['project']['totalhours'] += $totalminutes;
								$output .= '<tr>';
								$output .= '<td style="font-weight: bold; border-top: 1px solid #000000">';
								if($budget_modus == 'h')
								{
									$output .= $this->bohours->format_minutes($totalminutes);
								}
								else
								{
									$output .= number_format($totalminutes / 60 * $matrix[$i]['project']['budget_factor'], 0, ',', '.'). ' &euro;';
								}
								$output .= '</td>';
								$output .= '</tr>';
							}
							$output .= '</table>';
						}
					}
					$output .= '</td>';
				}
				$output .= '<td style="font-weight: bold; background-color: #d3dce3; vertical-align:bottom; text-align: right; border-bottom: 1px solid #808080">';

				$tooltiptitle = lang('Budget');
				$tooltip  = '<table width=100%>';
				$tooltip .= '<tr>';
				$tooltip .= '<td>';
				$tooltip .= lang('Debit').':';
				$tooltip .= '</td>';
				$tooltip .= '<td align=right>';
				if($budget_modus == 'h')
				{
					if($matrix[$i]['project']['budget_factor'] > 0)
					{
						$tooltip .= $this->bohours->format_minutes($matrix[$i]['project']['budget'] / $matrix[$i]['project']['budget_factor'] * 60);
					}
					else
					{
						$tooltip .= '-';
					}
				}
				else
				{
					$tooltip .= number_format($matrix[$i]['project']['budget'], 0, ',', '.'). ' &euro;';
				}
				$tooltip .= '</td>';
				$tooltip .= '</tr>';
				$tooltip .= '<tr>';
				$tooltip .= '<td>';
				$tooltip .= lang('Actual').':';
				$tooltip .= '</td>';
				$tooltip .= '<td align=right>';
				if($budget_modus == 'h')
				{
					$tooltip .= $this->bohours->format_minutes($matrix[$i]['project']['totalhours']);
				}
				else
				{
					$tooltip .= number_format($matrix[$i]['project']['totalhours'] / 60 * $matrix[$i]['project']['budget_factor'], 0, ',', '.'). ' &euro;';
				}
				$tooltip .= '</td>';
				$tooltip .= '</tr>';
				$tooltip .= '<tr>';
				$tooltip .= '<td>';
				$tooltip .= lang('Variance').':';
				$tooltip .= '</td>';
				$tooltip .= '<td align=right>';
				if($budget_modus == 'h')
				{
					if($matrix[$i]['project']['budget_factor'] > 0)
					{
						$tooltip .= $this->bohours->format_minutes($matrix[$i]['project']['budget'] / $matrix[$i]['project']['budget_factor'] * 60 - $matrix[$i]['project']['totalhours']);
					}
					else
					{
						$tooltip .= '-';
					}
				}
				else
				{
					$tooltip .= number_format($matrix[$i]['project']['budget'] - $matrix[$i]['project']['totalhours'] / 60 * $matrix[$i]['project']['budget_factor'], 0, ',', '.'). ' &euro;';
				}
				$tooltip .= '</td>';
				$tooltip .= '</tr>';
				$tooltip .= '</table>';

				$output .= "<div style=\"text-align: right;\" onMouseover=\"this.T_TITLE='$tooltiptitle'; this.T_WIDTH=250; return escape('$tooltip'); \" >";
				if($budget_modus == 'h')
				{
					$output .= $this->bohours->format_minutes($matrix[$i]['project']['totalhours']);
				}
				else
				{
					$output .= number_format($matrix[$i]['project']['totalhours'] / 60 * $matrix[$i]['project']['budget_factor'], 0, ',', '.'). ' &euro;';
				}
				$output .= '</div>';
				$output .= '</td>';
				$output .= '</tr>';
			}

			$GLOBALS['phpgw']->template->set_var('tableContent', $output);
			$GLOBALS['phpgw']->template->set_var('l_total', lang('Total'));

			for($i = 0; $i < count($allemployees); $i++)
			{
				$output2 .= '<td style="font-weight: bold; text-align: right">';
				if($budget_modus == 'h')
				{
					$output2 .= $this->bohours->format_minutes($matrix[0]['employee'][$allemployees[$i]]['totalhours']);
				}
				else
				{
					$output2 .= number_format($matrix[0]['employee'][$allemployees[$i]]['totalcosts'], 0, ',', '.'). ' &euro;';
				}
				$output2 .= '</td>';
			}

			$GLOBALS['phpgw']->template->set_var('tableContent2', $output2);
/*
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects') . ': ' . lang('view project activities')
			                                                . $this->admin_header_info();
			$this->display_app_header();
*/
			$this->ui_base->display_app_header();

			if($budget_modus == 'm')
			{
				$GLOBALS['phpgw']->template->set_var('selected', 'selected');
			}
			$GLOBALS['phpgw']->template->set_var('l_hour', lang('timed'));
			$GLOBALS['phpgw']->template->set_var('l_monetary', lang('monetary'));
			$GLOBALS['phpgw']->template->set_var('l_project', $matrix[0]['project']['title']);

			$GLOBALS['phpgw']->template->pfp('out','controlling', true);
		}

	}
?>
