<?php
	/**
	* Project Manager
	*
	* @author Dirk Schaller [dschaller@probusiness.de]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id$
	* $Source: /sources/phpgroupware/projects/inc/class.uiprojects_base.inc.php,v $
	*/

	define( 'PROJECT_LIST','pro_list' );
	define( 'PROJECT_VIEW','pro_view' );
	define( 'PROJECT_EDIT','pro_edit' );
	define( 'PROJECT_ADD','pro_add' );
	define( 'PROJECT_ADD_SUB','pro_add_sub' );

	define( 'PROJECT_HOURS','pro_hours' );
	define( 'PROJECT_BUDGET','pro_budget' );
	define( 'PROJECT_ACTIVITIES','pro_activities' );
	define( 'PROJECT_GANTTCHART','pro_ganttchart' );
	define( 'PROJECT_EMPLOYEES','pro_employees' );

	define( 'PROJECT_PARENT','pro_parent' );

	define( 'WORKTIME_LIST','wt_list' );
	define( 'WORKTIME_ADD','wt_add' );
	define( 'WORKTIME_VIEW','wt_view' );
	define( 'WORKTIME_EDIT','wt_edit' );
	define( 'WORKTIME_CONTROLLINGSHEET','wt_cs' );
	define( 'WORKTIME_TIMETRACKER','wt_tt' );
	define( 'WORKTIME_STATISTIC','wt_stat' );

	define( 'ACT_REPORT','act_report' );
	define( 'EXPORT_DIAMANT','export_diamant' );

	class uiprojects_base
	{
		var $activeView	= null;
		var $boprojects	= null;
		var $project_id	= 0;
		var $pro_parent	= 0;
		var $pro_main	= 0;
		var $pro_data	= null;
		var $action		= '';
		var $menuaction	= '';
		var $status		= '';
		var $headline	= '';

		var $public_functions = array
		(
			'proid_help_popup'	=> true
		);

		function uiprojects_base()
		{
			$action				= get_var('action',array('GET'));
			$this->boprojects	= CreateObject('projects.boprojects', True,$action);

			$this->menuaction	= get_var('menuaction',array('POST','GET'));
			$this->action		= get_var('action',array('GET','POST'));
			$this->project_id	= get_var('project_id',array('POST','GET'));
			$this->pro_main		= get_var('pro_main',array('POST','GET'));
			$this->pro_parent	= get_var('pro_parent',array('POST','GET'));
			$this->status		= get_var('status',array('POST','GET'));

			if($this->project_id > 0)
			{
				$this->pro_data = $this->boprojects->read_single_project($this->project_id);
			}
			else
			{
				$this->pro_data = False;
			}

			if($this->pro_data && !$this->pro_main)
			{
				$this->pro_main = $this->pro_data['main'];
			}

			if($this->pro_data && !$this->pro_parent)
			{
				$this->pro_parent = $this->pro_data['parent'];
			}

			if($this->pro_parent > 0)
			{
				$this->action = 'subs';
			}
			else
			{
				$this->action = 'mains';
			}

			if($this->pro_data && !$this->status)
			{
				$this->status = $this->pro_data['status'];
			}

			switch($this->menuaction)
			{
				case 'projects.uiprojects.list_projects':
					if($this->project_id > 0)
					{
						$link_data = array
						(
							'menuaction'	=> 'projects.uiprojects.tree_view_projects',
							'pro_main'		=> $this->pro_main,
							'action'		=> $this->action,
							'project_id'	=> $this->project_id,
							'pro_parent'	=> $this->pro_parent
						);
						$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
					}
					$this->activeView = PROJECT_LIST;
				break;
				case 'projects.uiprojects.tree_view_projects':
					if(!$this->project_id)
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'	=> 'projects.uiprojects.list_projects'));
					}
					$this->activeView = PROJECT_LIST;
				break;
				case 'projects.uiprojects.delete_project':
				case 'projects.uiprojects.edit_project':
				case 'projects.uiprojects.project_mstones':
				case 'projects.uiprojects.assign_employee_roles':
					if($this->project_id > 0)
					{ // edit
						$this->activeView = PROJECT_EDIT;
					}
					else
					{ // add project or add job
						if($this->pro_main > 0)
						{
							$this->activeView = PROJECT_ADD_SUB;
							$this->project_id = $this->pro_parent;
							$this->pro_parent = $this->boprojects->return_value('parent', $this->project_id);
						}
						else
						{
							$this->activeView = PROJECT_ADD;
						}
					}
				break;
				case 'projects.uiprojects.view_project':
					if(!$this->project_id)
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'	=> 'projects.uiprojects.list_projects'));
					}
					$this->activeView = PROJECT_VIEW;
				break;
				case 'projects.uiprojecthours.list_projects':
					if(!$this->project_id)
					{
						if($this->pro_main)
						{
							$GLOBALS['phpgw']->redirect_link('/index.php',array('project_id' => $this->pro_main, 'menuaction' => $this->menuaction));
						}
						else
						{
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => 'projects.uiprojects.list_projects'));
						}
					}
					$this->activeView = PROJECT_HOURS;
				break;
				case 'projects.uiprojects.list_budget':
					if(!$this->project_id)
					{
						if($this->pro_main)
						{
							$GLOBALS['phpgw']->redirect_link('/index.php',array('project_id' => $this->pro_main, 'menuaction' => $this->menuaction));
						}
						else
						{
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => 'projects.uiprojects.list_projects'));
						}
					}
					$this->activeView = PROJECT_BUDGET;
				break;
				case 'projects.uiprojects.view_employee_activity':
					$this->activeView = PROJECT_ACTIVITIES;
					if(!$this->project_id)
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'	=> 'projects.uiprojects.list_projects'));
					}
				break;
				case 'projects.uistatistics.project_gantt':
					if(!$this->project_id)
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'	=> 'projects.uiprojects.list_projects'));
					}
					$this->activeView = PROJECT_GANTTCHART;
				break;
				case 'projects.uistatistics.list_project_employees':
					if(!$this->project_id)
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'	=> 'projects.uiprojects.list_projects'));
					}
					$this->activeView = PROJECT_EMPLOYEES;
				break;
				case 'projects.uiprojecthours.list_hours':
					if(!$this->project_id)
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'	=> 'projects.uiprojects.list_projects'));
					}
					$this->activeView = WORKTIME_LIST;
				break;
				case 'projects.uiprojecthours.delete_hours':
				case 'projects.uiprojecthours.edit_hours':
					if(!$this->project_id)
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'	=> 'projects.uiprojects.list_projects'));
					}
					$hours_id = get_var('hours_id',array('POST','GET'));
					if(!$hours_id)
					{
						$this->activeView = WORKTIME_ADD;
					}
					else
					{ // no icon for WORKTIME_EDIT, use
						$this->activeView = WORKTIME_EDIT;
					}

				break;
				case 'projects.uiprojecthours.view_hours':
					if(!$this->project_id)
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'	=> 'projects.uiprojects.list_projects'));
					}
					$this->activeView = WORKTIME_VIEW;
				break;
				case 'projects.uiprojecthours.controlling_sheet':
				case 'projects.uiprojecthours.import_controlling_sheet':
					$this->activeView = WORKTIME_CONTROLLINGSHEET;
				break;
				case 'projects.uistatistics.list_users_worktimes':
					$this->activeView = WORKTIME_STATISTIC;
				break;
				case 'projects.uiprojecthours.ttracker':
					$this->activeView = WORKTIME_TIMETRACKER;
				break;
				case 'projects.uiprojects.view_report_list':
					if(!$this->project_id)
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'	=> 'projects.uiprojects.list_projects'));
					}
					$this->activeView = ACT_REPORT;
				break;
				case 'projects.uiprojects.report':
					if(!$this->project_id)
					{
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'	=> 'projects.uiprojects.list_projects'));
					}
					$this->activeView = ACT_REPORT_WIZARD;
				break;
				case 'projects.uiprojects.export_cost_accounting':
					$this->activeView = EXPORT_DIAMANT;
				break;
				default: // default is chosen when projects starts
					$this->activeView = PROJECT_LIST;
			}

			$this->headline = $this->getProjectPath();
		}

		function display_app_header()
		{

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			// stop irritating double entry of navbar_footer in idot
			unset($GLOBALS['phpgw']->template->file);
			unset($GLOBALS['phpgw']->template->varkeys);
			unset($GLOBALS['phpgw']->template->varvals);

			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);

			//echo $this->activeView;
			$GLOBALS['phpgw']->template->set_file(array('header' => 'projects_header.tpl'));
			$GLOBALS['phpgw']->template->set_block('header','projects_header');
			$GLOBALS['phpgw']->template->set_block('header','projects_menu_toolbar', 'toolbars');

			$icon_sep = '<div style="float:left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>';

			// 2. view toolbar
			$icons  = $this->CreateButton(PROJECT_LIST);
			$icons .= $this->CreateButton(PROJECT_VIEW);
			$icons .= $icon_sep;
			$icons .= $this->CreateButton(PROJECT_HOURS);
			$icons .= $this->CreateButton(PROJECT_BUDGET);
			$icons .= $this->CreateButton(PROJECT_GANTTCHART);
			$icons .= $icon_sep;
			$icons .= $this->CreateButton(PROJECT_ACTIVITIES);
			$icons .= $this->CreateButton(PROJECT_EMPLOYEES);

			$GLOBALS['phpgw']->template->set_var('toolbar_name',  lang('Views'));
			$GLOBALS['phpgw']->template->set_var('toolbar_icons', $icons);

			$GLOBALS['phpgw']->template->fp('projects_menu','projects_menu_toolbar', true);

			// 3. controlling toolbar
			$icons  = $this->CreateButton(WORKTIME_LIST);
			$icons .= $icon_sep;
			$icons .= $this->CreateButton(WORKTIME_ADD);
			$icons .= $icon_sep;
			$icons .= $this->CreateButton(ACT_REPORT);
			$icons .= $icon_sep;
			$icons .= $this->CreateButton(WORKTIME_TIMETRACKER);
			$icons .= $this->CreateButton(WORKTIME_CONTROLLINGSHEET);
			$icons .= $icon_sep;
			$icons .= $this->CreateButton(WORKTIME_STATISTIC);

			$GLOBALS['phpgw']->template->set_var('toolbar_name',  lang('Controlling'));
			$GLOBALS['phpgw']->template->set_var('toolbar_icons', $icons);

			$GLOBALS['phpgw']->template->fp('projects_menu','projects_menu_toolbar', true);

			// 1. projectmanagement toolbar
			$icons  = $this->CreateButton(PROJECT_ADD);
			$icons .= $this->CreateButton(PROJECT_EDIT);
			$icons .= $this->CreateButton(PROJECT_ADD_SUB);

			if ( $this->boprojects->isprojectadmin('pad') || $this->boprojects->isprojectadmin('pmanager') )
			{
				$icons .= $icon_sep;
				$icons .= $this->CreateButton(EXPORT_DIAMANT);
			}

			$GLOBALS['phpgw']->template->set_var('toolbar_name', lang('Actions'));
			$GLOBALS['phpgw']->template->set_var('toolbar_icons', $icons);

			$GLOBALS['phpgw']->template->fp('projects_menu','projects_menu_toolbar', true);

			$selectBox = $this->createSelectBox();
			$GLOBALS['phpgw']->template->set_var('select_pro_action', $selectBox['action']);
			$GLOBALS['phpgw']->template->set_var('select_pro_options', $selectBox['options']);

			$GLOBALS['phpgw']->template->set_var('headline', $this->headline);
			$GLOBALS['phpgw']->template->set_var('up_button', $this->createIcon(PROJECT_PARENT));

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('projects');
			$GLOBALS['phpgw']->template->fp('app_header','projects_header');
			$this->set_app_langs();
		}

		function display_app_menu()
		{
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] != 'idots' && $GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] != 'probusiness')
			{
				$GLOBALS['phpgw']->template->set_file(array('header' => 'header.tpl'));
				$GLOBALS['phpgw']->template->set_block('header','projects_header');
				$GLOBALS['phpgw']->template->set_block('header','projects_admin_header');

				if ( $this->boprojects->isprojectadmin('pad') || $this->boprojects->isprojectadmin('pmanager') )
				{
					switch( $this->siteconfig['accounting'] )
					{
						case 'activity':
							$GLOBALS['phpgw']->template->set_var('link_accounting',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_activities','action'=>'act')));
							$GLOBALS['phpgw']->template->set_var('lang_accounting',lang('Activities'));
							break;
						default:
						  	//open accounting page from admin section
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
				$GLOBALS['phpgw']->template->set_var('link_archiv',$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.archive','action'=>'amains')));
				$GLOBALS['phpgw']->template->fp('app_header','projects_header');
			}
		}

		function admin_header_info()
		{
			if ( $this->boprojects->isprojectadmin('pad') )
			{
				$pa = true;
			}

			if ( $this->boprojects->isprojectadmin('pmanager') )
			{
				$pm = true;
			}

			if ( $this->boprojects->isprojectadmin('psale') )
			{
				$ps = true;
			}
			return ($pa ? '&nbsp;&gt;&nbsp;' . lang('administrator') : '') . ($pm ? '&nbsp;&gt;&nbsp;' . lang('manager') : '') . ($ps ? '&nbsp;&gt;&nbsp;' . lang('salesman') : '');
		}


		function createIcon( $targetView, $showToolTip = true, $showText = false )
		{
			switch( $targetView )
			{
				case PROJECT_PARENT:
					if(!$this->pro_main)
					{
						$buttonInActive = true;
					}
					break;
				default:
					return false;
			}

			$guiId = 'button_'.$targetView;

			if( $showToolTip )
			{
				$linkToolTip = $this->getText($guiId.'_tooltip');
			}
			else
			{
				$linkToolTip = '';
			}

			if( $showText )
			{
				$linkText = $this->getText($guiId . '_text');
			}
			else
			{
				$linkText = '';
			}

			if( $buttonInActive )
			{
				$buttonClass = 'menu_icon_inactive';
			}
			elseif( $buttonActive )
			{
				$buttonClass = 'menu_icon_active';
			}
			else
			{
				$buttonClass = 'menu_icon';
			}

			if( $buttonInActive )
			{
				$link = '<div class="' . $buttonClass . '"><a class="' . $buttonClass . '" title="' . $linkToolTip . '"><img src="' . $GLOBALS['phpgw']->common->image('projects', $guiId) . '" class="' . $buttonClass . '" alt="' . $linkToolTip . '">' . $linkText . '</a></div>';
			}
			else
			{
				$viewUrl = $this->createViewUrl($targetView);
				$link = '<div class="' . $buttonClass . '"><a class="' . $buttonClass . '" href="' . $viewUrl . '" title="' . $linkToolTip . '"><img src="' . $GLOBALS['phpgw']->common->image('projects', $guiId) . '" class="' . $buttonClass . '" alt="' . $linkToolTip . '">' . $linkText . '</a></div>';
			}

			return $link;
		}

		function createSelectBox()
		{
			// switch the list type:
			// if a main project is selected show all sub projects and differ the view
			// else show a list with all main projects

			if( $this->pro_main > 0 )
			{
				$targetView = $this->getTargetView();
				$selectbox['action']	= $this->createViewUrl($targetView, array('status'=>$this->status));
				$selectbox['options']	= '<option value="">' . lang('Select project') . '</option>' . "\n";
				$selectbox['options']	.= $this->boprojects->select_project_list( array
				(
					'filter'	=> 'none',
					'action'	=> 'all',
					'limit'		=> false,
					'status'	=> 'active',
					'selected'	=> $this->project_id
				));
			}
			else
			{
				$selectbox['action']   = $this->createViewUrl(PROJECT_LIST, array('status'=>$this->status));
				$selectbox['options']  = '<option value="">' . lang('Select project') . '</option>' . "\n";
				$selectbox['options'] .= $this->boprojects->select_project_list( array
				(
					'action' => 'all',
					'status' => 'active',
					'limit'  => False)
				);
			}

			return $selectbox;
		}

		function createFoldersViewUrl( $project_id )
		{
			$targetView = $this->getTargetView();
			return $this->createViewUrl($targetView, array('project_id'=>$project_id));
		}


		function createButton($targetView, $showToolTip=true, $showText=false)
		{
			if( $targetView == $this->activeView )
			{
				$buttonActive = True;
			}
			else
			{
				$buttonActive = False;
			}

			switch( $targetView )
			{
				case PROJECT_ADD:
				case WORKTIME_CONTROLLINGSHEET:
				case WORKTIME_TIMETRACKER:
				case WORKTIME_STATISTIC:
					break;
				case PROJECT_PARENT:
					if(!$this->pro_main || ($this->activeView == PROJECT_ADD_SUB))
					{
						$buttonInActive = True;
					}
					break;
				case PROJECT_EDIT:
					if(!$this->pro_main)
					{
						$buttonInActive = True;
					}
					else
					{
						$check = array
						(
							'action' => $this->action,
							'coordinator' => $this->pro_data['coordinator'],
							'main' => $this->pro_main,
							'parent' => $this->pro_parent
						);
						if( !$this->boprojects->edit_perms($check) )
						{
							$buttonInActive = True;
						}
					}
					break;
				case PROJECT_ADD_SUB:
					if(!$this->pro_main)
					{
						$buttonInActive = true;
					}
					else
					{
						$check = array
						(
							'action' => $this->action,
							'coordinator' => $this->pro_data['coordinator'],
							'main' => $this->pro_main,
							'parent' => $this->pro_parent
						);
						if( !$this->boprojects->add_perms($check) )
						{
							$buttonInActive = True;
						}
					}
					break;
				case PROJECT_ACTIVITIES:
					if( !$this->pro_main )
					{
						$buttonInActive = true;
					}
					else
					{
						$check = array
						(
							'action' => $this->action,
							'coordinator' => $this->pro_data['coordinator'],
							'main' => $this->pro_main,
							'parent' => $this->pro_parent
						);
						if( !$this->boprojects->edit_perms($check) && !$this->boprojects->isprojectadmin('pad') && !$this->boprojects->isprojectadmin('pmanager') )
						{
							$buttonInActive = True;
						}
					}
					break;
				case PROJECT_LIST:
				case PROJECT_VIEW:
				case PROJECT_HOURS:
				case PROJECT_BUDGET:
				case PROJECT_GANTTCHART:
				case PROJECT_EMPLOYEES:
				case WORKTIME_LIST:
				case WORKTIME_ADD:
				case WORKTIME_VIEW:
				case WORKTIME_EDIT:
				case ACT_REPORT:
					if(!$this->pro_main)
					{
						$buttonInActive = True;
					}
					break;
			}

			$guiId = 'button_' . $targetView;

			if( isset($showToolTip) && $showToolTip )
			{
				$linkToolTip = $this->getText($guiId . '_tooltip');
			}
			else
			{
				$linkToolTip = '';
			}

			if( isset($showText) && $showText )
			{
				$linkText = $this->getText($guiId . '_text');
			}
			else
			{
				$linkText = '';
			}

			if( isset($buttonInActive) && $buttonInActive )
			{
				$buttonClass = 'menu_button_inactive';
			}
			elseif( isset($buttonActive) && $buttonActive )
			{
				$buttonClass = 'menu_button_active';
			}
			else
			{
				$buttonClass = 'menu_button';
			}

			if( isset($buttonInActive) && $buttonInActive )
			{
				$link = '<div class="'.$buttonClass.'"><a class="'.$buttonClass.'" title="'.$linkToolTip.'"><img src="'.$GLOBALS['phpgw']->common->image('projects', $guiId).'" class="'.$buttonClass.'" alt="'.$linkToolTip.'">'.$linkText.'</a></div>';
			}
			else
			{
				$linkData['project_id'] = $this->project_id;
				$linkData['pro_main']   = $this->pro_main;
				$linkData['pro_parent'] = $this->pro_parent;
				if( $this->status )
				{
					$linkData['status'] = $this->status;
				}
				$viewUrl = $this->createViewUrl($targetView, $linkData);
				$link ='<div class="'.$buttonClass.'"><a class="'.$buttonClass.'" href="'.$viewUrl.'" title="'.$linkToolTip.'"><img src="'.$GLOBALS['phpgw']->common->image('projects', $guiId).'" class="'.$buttonClass.'" alt="'.$linkToolTip.'">'.$linkText.'</a></div>';
			}

			return $link;
		}


		function createViewUrl( $targetView = False, $linkData=array() )
		{
			if( !$targetView )
			{
				$targetView = PROJECT_LIST;
			}

			switch( $targetView )
			{
				case PROJECT_LIST:
					$linkData['menuaction'] = 'projects.uiprojects.tree_view_projects';
					break;
				case PROJECT_ADD:
					unset($linkData);
					$linkData['menuaction'] = 'projects.uiprojects.edit_project';
					$linkData['action']     = 'mains';
					break;
				case PROJECT_PARENT:
					unset($linkData);
					if( $this->pro_parent > 0 )
					{
						switch( $this->activeView )
						{
							case PROJECT_EDIT:
							case PROJECT_ADD_SUB:
								$linkData['menuaction'] = 'projects.uiprojects.tree_view_projects';
								$linkData['project_id'] = $this->project_id;
								$linkData['pro_main']   = $this->pro_main;
								$linkData['pro_parent'] = $this->pro_parent;
								break;
							default:
								$linkData['menuaction'] = $this->menuaction;
								$linkData['pro_main']   = $this->pro_main;
								$linkData['project_id'] = $this->pro_parent;
						}
					}
					else
					{
						$linkData['menuaction'] = 'projects.uiprojects.list_projects';
						$linkData['action']     = 'mains';
					}
					break;
				case PROJECT_ADD_SUB:
					$linkData['menuaction'] = 'projects.uiprojects.edit_project';
					$linkData['action']     = 'subs';
					$linkData['pro_parent'] = $this->project_id;
					$linkData['project_id'] = 0;
					break;
				case PROJECT_VIEW:
					$linkData['menuaction'] = 'projects.uiprojects.view_project';
					break;
				case PROJECT_EDIT:
					$linkData['menuaction'] = 'projects.uiprojects.edit_project';
					break;
				case PROJECT_HOURS:
					$linkData['menuaction'] = 'projects.uiprojecthours.list_projects';
					break;
				case PROJECT_BUDGET:
					$linkData['menuaction'] = 'projects.uiprojects.list_budget';
					break;
				case PROJECT_ACTIVITIES:
					$linkData['menuaction'] = 'projects.uiprojects.view_employee_activity';
					break;
				case PROJECT_GANTTCHART:
					$linkData['menuaction'] = 'projects.uistatistics.project_gantt';
					$linkData['parent']     = $this->pro_parent;
					break;
				case PROJECT_EMPLOYEES:
					$linkData['menuaction'] = 'projects.uistatistics.list_project_employees';
					break;
				case WORKTIME_LIST:
					$linkData['menuaction'] = 'projects.uiprojecthours.list_hours';
					$linkData['action']     = 'hours';
					break;
				case WORKTIME_ADD:
					$linkData['menuaction'] = 'projects.uiprojecthours.edit_hours';
					$linkData['action']     = 'hours';
					break;
				case WORKTIME_VIEW:
					$linkData['menuaction'] = 'projects.uiprojecthours.list_hours';
					$linkData['action']     = 'hours';
					break;
				case WORKTIME_EDIT:
					$linkData['menuaction'] = 'projects.uiprojecthours.list_hours';
					$linkData['action']     = 'hours';
					break;
				case WORKTIME_CONTROLLINGSHEET:
					unset($linkData);
					$linkData['menuaction'] = 'projects.uiprojecthours.controlling_sheet';
					break;
				case WORKTIME_TIMETRACKER:
					$linkData['menuaction'] = 'projects.uiprojecthours.ttracker';
					$linkData['values[project_id]'] = $this->project_id;
					break;
				case WORKTIME_STATISTIC:
					$linkData['menuaction'] = 'projects.uistatistics.list_users_worktimes';
					break;
				case ACT_REPORT:
					$linkData['menuaction'] = 'projects.uiprojects.view_report_list';
					$linkData['project_id'] = $this->project_id;
					break;
				case 'ACT_REPORT_WIZARD':
					$linkData['menuaction'] = 'projects.uiprojects.report';
					$linkData['project_id'] = $this->project_id;
					break;
				case EXPORT_DIAMANT:
					$linkData['menuaction'] = 'projects.uiprojects.export_cost_accounting';
					break;
				default:
					return false;
			}

			if( $this->status )
			{
				$linkData['status'] = $this->status;
			}

			$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
			return $link;
		}

		function createActiveViewUrl( $linkData=array() )
		{
			return $this->createViewUrl($this->activeView, $linkData);
		}

		function getTargetView()
		{
			// depend on active view set the target view for selectbox and folders
			switch( $this->activeView )
			{
				case PROJECT_ADD:
				case PROJECT_EDIT:
				case PROJECT_ADD_SUB:
				case WORKTIME_CONTROLLINGSHEET:
				case WORKTIME_STATISTIC:
					$targetView = PROJECT_LIST;
					break;
				default:
					$targetView = $this->activeView;
			}

			return $targetView;
		}

		function getProjectPath()
		{
			$path = '';
			$space = "&nbsp;&gt;&nbsp;";
			$view = $this->getTargetView();

			if( !$this->pro_data && $this->project_id )
			{
				$this->pro_data = $this->boprojects->read_single_project($this->project_id);
			}

			$level = $level_start = intval($this->pro_data['level']);

			$project_id = $this->project_id;
			while( $project_id > 0 )
			{
				if( $level < ($level_start-1) && ($level > 1) )
				{
					$title = '&nbsp;...';
				}
				else
				{
					$title = $this->boprojects->return_value('title', $project_id);
					if( strlen($title) > 50 )
					{
						$title = substr($title, 0, 35);
						$str_break = strrchr($title, 32); // ASCII 32 = space
						if( $str_break !== false )
						{
							$pos_break = strlen($title) - strlen($str_break);
							$title = substr($title, 0, $pos_break);
						}
						$title .= '&nbsp;...';
					}
				}

				$link = $this->createViewUrl($view, array('project_id' => $project_id));
				$menu = '<a href="'.$link.'">'.$title.'</a>';
				$path = $menu.$space.$path;
				$parent = $this->boprojects->return_value('parent', $project_id);
				$project_id = $parent;
				$level--;
			}

			$id = 'button_'.$this->activeView.'_text';
			$path .= $this->getText($id);

			return $path;
		}

		function getText( $id )
		{
			$text = '';

			switch( $id )
			{
				case 'button_pro_add_tooltip':
				case 'button_pro_add_text':
					$text = lang('add project');
					break;
				case 'button_pro_parent_tooltip':
				case 'button_pro_parent_text':
					if($this->pro_parent)
					{
						$text = lang('parent project');
					}
					else
					{
						$text = lang('select main project');
					}
					break;
				case 'button_pro_list_tooltip':
				case 'button_pro_list_text':
					if($this->pro_main)
					{
						$text = lang('list');
					}
					else
					{
						$text = lang('select main project');
					}
					break;
				case 'button_pro_view_tooltip':
				case 'button_pro_view_text':
					$text = lang('details');
					break;
				case 'button_pro_edit_tooltip':
				case 'button_pro_edit_text':
					$text = lang('edit');
					break;
				case 'button_pro_add_sub_tooltip':
				case 'button_pro_add_sub_text':
					$text = lang('add sub project');
					break;
				case 'button_pro_activities_tooltip':
				case 'button_pro_activities_text':
					$text = lang('activities');
					break;
				case 'button_pro_hours_tooltip':
				case 'button_pro_hours_text':
					$text = lang('work hours');
					break;
				case 'button_pro_budget_tooltip':
				case 'button_pro_budget_text':
					$text = lang('budget');
					break;
				case 'button_pro_ganttchart_tooltip':
				case 'button_pro_ganttchart_text':
					$text = lang('gantt chart');
					break;
				case 'button_pro_employees_tooltip':
				case 'button_pro_employees_text':
					$text = lang('employees');
					break;
				case 'button_wt_list_tooltip':
				case 'button_wt_list_text':
					$text = lang('list activities');
					break;
				case 'button_wt_add_tooltip':
				case 'button_wt_add_text':
					$text = lang('add activity');
					break;
				case 'button_wt_edit_tooltip':
				case 'button_wt_edit_text':
					$text = lang('edit activity');
					break;
				case 'button_wt_view_tooltip':
				case 'button_wt_view_text':
					$text = lang('activity');
					break;
				case 'button_wt_cs_tooltip':
				case 'button_wt_cs_text':
					$text = lang('controlling sheet');
					break;
				case 'button_wt_stat_tooltip':
				case 'button_wt_stat_text':
					$text = lang('work hours statistics');
					break;
				case 'button_wt_tt_tooltip':
				case 'button_wt_tt_text':
					$text = lang('time tracker');
					break;
				case 'button_act_report_tooltip':
				case 'button_act_report_text':
					$text = lang('activity reports');
					break;
				case 'button_export_diamant_tooltip':
				case 'button_export_diamant_text':
					$text = lang('export diamant accounting');
					break;
			}

			return $text;
		}

		function set_app_langs()
		{
			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']['row_off']);

			$GLOBALS['phpgw']->template->set_var('lang_category',lang('Category'));
			$GLOBALS['phpgw']->template->set_var('lang_select',lang('Select'));
			$GLOBALS['phpgw']->template->set_var('lang_select_category',lang('Select category'));

			$GLOBALS['phpgw']->template->set_var('lang_descr',lang('Description'));
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('lang_none',lang('None'));
			$GLOBALS['phpgw']->template->set_var('lang_number',lang('Project ID'));

			$GLOBALS['phpgw']->template->set_var('lang_start_date',lang('Start Date'));
			$GLOBALS['phpgw']->template->set_var('lang_end_date',lang('End Date'));
			$GLOBALS['phpgw']->template->set_var('lang_date_due',lang('Date due'));
			$GLOBALS['phpgw']->template->set_var('lang_cdate',lang('Date created'));
			$GLOBALS['phpgw']->template->set_var('lang_last_update',lang('last update'));

			$GLOBALS['phpgw']->template->set_var('lang_start_date_planned',lang('start date planned'));
			$GLOBALS['phpgw']->template->set_var('lang_date_due_planned',lang('date due planned'));

			$GLOBALS['phpgw']->template->set_var('lang_access',lang('access'));
			$GLOBALS['phpgw']->template->set_var('lang_projects',lang('Projects'));
			$GLOBALS['phpgw']->template->set_var('lang_project',lang('Project'));
			$GLOBALS['phpgw']->template->set_var('lang_sub_projects',lang('Sub Projects'));
			$GLOBALS['phpgw']->template->set_var('lang_sub_project',lang('Sub Project'));

			$GLOBALS['phpgw']->template->set_var('lang_ttracker',lang('time tracker'));
			$GLOBALS['phpgw']->template->set_var('lang_statistics',lang('Statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_roles',lang('roles'));
			$GLOBALS['phpgw']->template->set_var('lang_role',lang('role'));

			$GLOBALS['phpgw']->template->set_var('lang_act_number',lang('Activity ID'));
			$GLOBALS['phpgw']->template->set_var('lang_status',lang('Status'));
			$GLOBALS['phpgw']->template->set_var('lang_budget',lang('Budget'));

			$GLOBALS['phpgw']->template->set_var('lang_investment_nr',lang('investment nr'));
			$GLOBALS['phpgw']->template->set_var('lang_customer',lang('Customer'));
			$GLOBALS['phpgw']->template->set_var('lang_coordinator',lang('Coordinator'));
			$GLOBALS['phpgw']->template->set_var('lang_employees',lang('Employees'));
			$GLOBALS['phpgw']->template->set_var('lang_person',lang('Person'));
			$GLOBALS['phpgw']->template->set_var('lang_organization',lang('Orga'));
			$GLOBALS['phpgw']->template->set_var('lang_creator',lang('creator'));
			$GLOBALS['phpgw']->template->set_var('lang_processor',lang('processor'));
			$GLOBALS['phpgw']->template->set_var('lang_previous',lang('previous project'));
			$GLOBALS['phpgw']->template->set_var('lang_bookable_activities',lang('Bookable activities'));
			$GLOBALS['phpgw']->template->set_var('lang_billable_activities',lang('Billable activities'));
			$GLOBALS['phpgw']->template->set_var('lang_edit',lang('edit'));
			$GLOBALS['phpgw']->template->set_var('lang_view',lang('View'));
			$GLOBALS['phpgw']->template->set_var('lang_hours',lang('Work hours'));
			$GLOBALS['phpgw']->template->set_var('lang_monetary',lang('monetary'));
			$GLOBALS['phpgw']->template->set_var('lang_timed',lang('timed'));
			$GLOBALS['phpgw']->template->set_var('lang_remarkreq',lang('Remark required'));

			$GLOBALS['phpgw']->template->set_var('lang_customer_nr',lang('customer nr'));
			$GLOBALS['phpgw']->template->set_var('lang_url',lang('project url'));
			$GLOBALS['phpgw']->template->set_var('lang_reference',lang('external reference'));

			$GLOBALS['phpgw']->template->set_var('lang_stats',lang('Statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_ptime',lang('time planned'));
			$GLOBALS['phpgw']->template->set_var('lang_utime',lang('time used'));
			$GLOBALS['phpgw']->template->set_var('lang_month',lang('month'));

			$GLOBALS['phpgw']->template->set_var('lang_save',lang('save'));
			$GLOBALS['phpgw']->template->set_var('lang_apply',lang('apply'));
			$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('cancel'));
			$GLOBALS['phpgw']->template->set_var('lang_search',lang('search'));
			$GLOBALS['phpgw']->template->set_var('lang_delete',lang('delete'));
			$GLOBALS['phpgw']->template->set_var('lang_back',lang('back'));



			$GLOBALS['phpgw']->template->set_var('lang_parent',lang('Parent project'));
			$GLOBALS['phpgw']->template->set_var('lang_main',lang('Main project'));

			$GLOBALS['phpgw']->template->set_var('lang_add_milestone',lang('add milestone'));

			$GLOBALS['phpgw']->template->set_var('lang_result',lang('result'));
			$GLOBALS['phpgw']->template->set_var('lang_test',lang('test'));
			$GLOBALS['phpgw']->template->set_var('lang_quality',lang('quality check'));

			$GLOBALS['phpgw']->template->set_var('lang_accounting',lang('accounting system'));
			$GLOBALS['phpgw']->template->set_var('lang_factor_project',lang('factor project'));
			$GLOBALS['phpgw']->template->set_var('lang_factor_employee',lang('factor employee'));
			$GLOBALS['phpgw']->template->set_var('lang_accounting_factor_for_project',lang('accounting factor for project'));
			$GLOBALS['phpgw']->template->set_var('lang_select_factor',lang('select factor'));
			$GLOBALS['phpgw']->template->set_var('lang_non_billable',lang('not billable'));

			$GLOBALS['phpgw']->template->set_var('lang_pbudget',lang('budget planned'));
			$GLOBALS['phpgw']->template->set_var('lang_ubudget',lang('budget used'));

			$GLOBALS['phpgw']->template->set_var('lang_per_hour',lang('per hour'));
			$GLOBALS['phpgw']->template->set_var('lang_per_day',lang('per day'));

			$GLOBALS['phpgw']->template->set_var('lang_nodiscount',lang('no discount'));
			$GLOBALS['phpgw']->template->set_var('lang_percent',lang('percent'));
			$GLOBALS['phpgw']->template->set_var('lang_amount',lang('amount'));

			$GLOBALS['phpgw']->template->set_var('lang_events',lang('events'));
			$GLOBALS['phpgw']->template->set_var('lang_priority',lang('priority'));

			$GLOBALS['phpgw']->template->set_var('lang_available',lang('available'));
			$GLOBALS['phpgw']->template->set_var('lang_used_billable',lang('used billable'));
			$GLOBALS['phpgw']->template->set_var('lang_planned',lang('planned'));
			$GLOBALS['phpgw']->template->set_var('lang_used_total',lang('used total'));

			$GLOBALS['phpgw']->template->set_var('lang_invoicing_method',lang('invoicing method'));
			$GLOBALS['phpgw']->template->set_var('lang_discount',lang('discount'));
			$GLOBALS['phpgw']->template->set_var('lang_extra_budget',lang('extra budget'));

			$GLOBALS['phpgw']->template->set_var('lang_billable',lang('billable'));
			$GLOBALS['phpgw']->template->set_var('lang_files',lang('files'));
			$GLOBALS['phpgw']->template->set_var('lang_attach',lang('attach file'));
			$GLOBALS['phpgw']->template->set_var('lang_plan_bottom_up',lang('plan bottom up'));
			$GLOBALS['phpgw']->template->set_var('lang_direct_work',lang('direct work'));
			$GLOBALS['phpgw']->template->set_var('lang_sum',lang('Sum'));


			// hours

			$GLOBALS['phpgw']->template->set_var('lang_select',lang('Select'));
			$GLOBALS['phpgw']->template->set_var('lang_none',lang('None'));
			$GLOBALS['phpgw']->template->set_var('lang_start_date',lang('Start Date'));
			$GLOBALS['phpgw']->template->set_var('lang_end_date',lang('End Date'));
			$GLOBALS['phpgw']->template->set_var('lang_date_due',lang('Date due'));

			$GLOBALS['phpgw']->template->set_var('lang_projects',lang('Projects'));
			$GLOBALS['phpgw']->template->set_var('lang_statistics',lang('Statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_ttracker',lang('time tracker'));
			$GLOBALS['phpgw']->template->set_var('lang_archiv',lang('archive'));
			$GLOBALS['phpgw']->template->set_var('lang_roles',lang('roles'));

			$GLOBALS['phpgw']->template->set_var('lang_number',lang('Project ID'));
			$GLOBALS['phpgw']->template->set_var('lang_status',lang('Status'));

			$GLOBALS['phpgw']->template->set_var('lang_save',lang('Save'));
			$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));
			$GLOBALS['phpgw']->template->set_var('lang_done',lang('done'));

			$GLOBALS['phpgw']->template->set_var('lang_view',lang('View'));

			$GLOBALS['phpgw']->template->set_var('lang_budget',lang('Budget'));

			$GLOBALS['phpgw']->template->set_var('lang_date',lang('date'));
			$GLOBALS['phpgw']->template->set_var('lang_time',lang('time'));

			$GLOBALS['phpgw']->template->set_var('lang_activity',lang('Activity'));
			$GLOBALS['phpgw']->template->set_var('lang_project',lang('Project'));
			$GLOBALS['phpgw']->template->set_var('lang_descr',lang('Description'));
			$GLOBALS['phpgw']->template->set_var('lang_remark',lang('Remark'));
			$GLOBALS['phpgw']->template->set_var('lang_status',lang('Status'));
			$GLOBALS['phpgw']->template->set_var('lang_employee',lang('Employee'));
			$GLOBALS['phpgw']->template->set_var('lang_work_date',lang('Work date'));
			$GLOBALS['phpgw']->template->set_var('lang_start_date',lang('Start date'));
			$GLOBALS['phpgw']->template->set_var('lang_end_date',lang('End date'));
			$GLOBALS['phpgw']->template->set_var('lang_work_time',lang('Work time'));
			$GLOBALS['phpgw']->template->set_var('lang_start_time',lang('Start time'));
			$GLOBALS['phpgw']->template->set_var('lang_end_time',lang('End time'));
			$GLOBALS['phpgw']->template->set_var('lang_select_project',lang('Select project'));

			$GLOBALS['phpgw']->template->set_var('lang_minperae',lang('Minutes per workunit'));
			$GLOBALS['phpgw']->template->set_var('lang_billperae',lang('Bill per hour/workunit'));

			$GLOBALS['phpgw']->template->set_var('lang_till',lang('till'));
			$GLOBALS['phpgw']->template->set_var('lang_from',lang('from'));
			$GLOBALS['phpgw']->template->set_var('lang_entry',lang('entry'));

			$GLOBALS['phpgw']->template->set_var('lang_url',lang('project url'));
			$GLOBALS['phpgw']->template->set_var('lang_main',lang('Main project'));

			$GLOBALS['phpgw']->template->set_var('lang_planned',lang('planned'));
			$GLOBALS['phpgw']->template->set_var('lang_used',lang('used'));
			$GLOBALS['phpgw']->template->set_var('lang_used_total',lang('used total'));
			$GLOBALS['phpgw']->template->set_var('lang_available',lang('available'));

			$GLOBALS['phpgw']->template->set_var('lang_hours',lang('Work hours'));

			$GLOBALS['phpgw']->template->set_var('lang_budget_planned',lang('budget planned'));

			$GLOBALS['phpgw']->template->set_var('lang_used_billable',lang('used billable'));
			$GLOBALS['phpgw']->template->set_var('lang_used_not_billable',lang('used not billable'));

			$GLOBALS['phpgw']->template->set_var('lang_utime_billable',lang('time used billable'));

			$GLOBALS['phpgw']->template->set_var('lang_total_time',lang('time used total'));

			$GLOBALS['phpgw']->template->set_var('lang_non_billable',lang('not billable'));
			$GLOBALS['phpgw']->template->set_var('lang_travel_time',lang('travel time'));

			$GLOBALS['phpgw']->template->set_var('lang_distance',lang('distance'));
			$GLOBALS['phpgw']->template->set_var('lang_surcharge',lang('surcharge'));
			$GLOBALS['phpgw']->template->set_var('lang_select_surcharge',lang('select surcharge'));

			$GLOBALS['phpgw']->template->set_var('lang_manual_mode',lang('manual mode'));
			$GLOBALS['phpgw']->template->set_var('lang_live_mode',lang('live mode'));
			$GLOBALS['phpgw']->template->set_var('lang_projects_and_captured_activities',lang('projects and captured activities'));
			$GLOBALS['phpgw']->template->set_var('lang_save_activities',lang('save activities'));
			$GLOBALS['phpgw']->template->set_var('lang_project and activity',lang('project and activity'));

			$GLOBALS['phpgw']->template->set_var('lang_start',lang('start'));
			$GLOBALS['phpgw']->template->set_var('lang_stop',lang('stop'));
			$GLOBALS['phpgw']->template->set_var('lang_pause',lang('pause'));
			$GLOBALS['phpgw']->template->set_var('lang_continue',lang('continue'));
			$GLOBALS['phpgw']->template->set_var('lang_comment',lang('comment'));
			$GLOBALS['phpgw']->template->set_var('lang_action',lang('action'));


			// stats

			$GLOBALS['phpgw']->template->set_var('lang_archiv',lang('archive'));
			$GLOBALS['phpgw']->template->set_var('lang_statistics',lang('Statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_projects',lang('Projects'));
			$GLOBALS['phpgw']->template->set_var('lang_ttracker',lang('time tracker'));
			$GLOBALS['phpgw']->template->set_var('lang_roles',lang('roles'));

			$GLOBALS['phpgw']->template->set_var('lang_calculate',lang('Calculate'));
			$GLOBALS['phpgw']->template->set_var('lang_none',lang('None'));

			$GLOBALS['phpgw']->template->set_var('lang_end_date',lang('End Date'));
			$GLOBALS['phpgw']->template->set_var('lang_date_due',lang('Date due'));
			$GLOBALS['phpgw']->template->set_var('lang_project',lang('Project'));
			$GLOBALS['phpgw']->template->set_var('lang_hours',lang('Hours'));
			$GLOBALS['phpgw']->template->set_var('lang_activity',lang('Activity'));
			$GLOBALS['phpgw']->template->set_var('lang_status',lang('Status'));
			$GLOBALS['phpgw']->template->set_var('lang_budget',lang('Budget'));

			$GLOBALS['phpgw']->template->set_var('lang_firstname',lang('Firstname'));
			$GLOBALS['phpgw']->template->set_var('lang_lastname',lang('Lastname'));
			$GLOBALS['phpgw']->template->set_var('lang_employee',lang('Employee'));
			$GLOBALS['phpgw']->template->set_var('lang_employees',lang('Employees'));
			$GLOBALS['phpgw']->template->set_var('lang_billedonly',lang('Billed only'));
			$GLOBALS['phpgw']->template->set_var('lang_hours',lang('Work hours'));
			$GLOBALS['phpgw']->template->set_var('lang_minperae',lang('Minutes per workunit'));
			$GLOBALS['phpgw']->template->set_var('lang_billperae',lang('Bill per workunit'));
			$GLOBALS['phpgw']->template->set_var('lang_stat',lang('Statistic'));
			$GLOBALS['phpgw']->template->set_var('lang_userstats',lang('User statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_worktimestats',lang('Work Time').' '.lang('statistics'));

			$GLOBALS['phpgw']->template->set_var('lang_view_projects',lang('view projects'));
			$GLOBALS['phpgw']->template->set_var('lang_gantt_chart',lang('gantt chart'));
			$GLOBALS['phpgw']->template->set_var('lang_show_chart',lang('show gantt chart'));
			$GLOBALS['phpgw']->template->set_var('lang_view_employees',lang('view employees'));

			$GLOBALS['phpgw']->template->set_var('lang_main',lang('Main project'));
			$GLOBALS['phpgw']->template->set_var('lang_number',lang('Project ID'));
			$GLOBALS['phpgw']->template->set_var('lang_url',lang('project url'));

			$GLOBALS['phpgw']->template->set_var('lang_gantt_chart','Gantt Chart');

			$GLOBALS['phpgw']->template->set_var('lang_in_out_sum',lang('fade in/blind out sum'));
			$GLOBALS['phpgw']->template->set_var('lang_persons',lang('persons'));
			$GLOBALS['phpgw']->template->set_var('lang_time_and_budget',lang('time and budget'));
			$GLOBALS['phpgw']->template->set_var('lang_documentation',lang('documentation'));
			$GLOBALS['phpgw']->template->set_var('lang_project_team',lang('project team'));
		}

		function status_format( $status = '', $showarchive = true )
		{
			$stat_sel = array('', '', '');
			if ( !$status )
			{
				$status = $this->status = 'active';
			}

			switch ( $status )
			{
				case 'active':					$stat_sel[0]=' selected';					break;
				case 'nonactive':					$stat_sel[1]=' selected';					break;
				case 'archive':					$stat_sel[2]=' selected';					break;
			}

			$status_list = '<option value="active"' . $stat_sel[0] . '>' . lang('Active') . '</option>' . "\n"
						. '<option value="nonactive"' . $stat_sel[1] . '>' . lang('Nonactive') . '</option>' . "\n";

			if ( $showarchive )
			{
				$status_list .= '<option value="archive"' . $stat_sel[2] . '>' . lang('Archive') . '</option>' . "\n";
			}
			return $status_list;
		}

		function employee_format( $data )
		{
			$type				= isset( $data['type'] ) ? $data['type'] : 'selectbox';
			$selected			= isset( $data['selected'] ) ? $data['selected'] : $this->boprojects->get_acl_for_project( $data['project_id'] );
			$project_only		= isset( $data['project_only'] ) ? $data['project_only'] : false;
			$admins_included	= isset( $data['admins_included'] ) ? $data['admins_included'] : false;

			if( $project_only )
			{
				$data['pro_parent']	= $data['project_id'];
				$data['action']		= 'subs';
			}

			if ( !is_array( $selected ) )
			{
				$selected = explode( ',', $selected );
			}

			switch( $type )
			{
				case 'selectbox':
					$employees = $this->boprojects->selected_employees( array
					(
						'action' => $data['action'],
					    'pro_parent' => $data['pro_parent'],
					    'admins_included' => $admins_included,
					    'project_id' => $data['project_id']
					));
					break;
				case 'popup':
					$employees	= $this->boprojects->selected_employees( array
					(
						'project_id' => $data['project_id']
					));
					break;
			}

			if( is_array( $employees ) )
			{
				usort( $employees, array( 'uiprojects_base', 'cmp_employees' ) );
			}

			while ( is_array( $employees ) && ( list( $null,$account ) = each( $employees ) ) )
			{
				if( !$account['account_lid'] )
				{
					continue;
				}
				$s .= '<option value="' . $account['account_id'] . '"';
				if ( in_array( $account['account_id'], $selected) )
				{
					$s .= ' SELECTED';
				}
				$s .= '>';
				$s .= $account['account_fullname'] . '</option>' . "\n";
			}
			return $s;
		}

		function cmp_employees( $a, $b )
		{
			return strcasecmp( $a['account_fullname'], $b['account_fullname'] );
		}

		function proid_help_popup()
		{

			$GLOBALS['phpgw']->template->set_file( array( 'proidhelp' => 'proid_help_popup.tpl' ) );
			$config = $this->boprojects->get_site_config( array('helpmsg' => true ) );
			$GLOBALS['phpgw']->template->set_var( 'helpmsg', stripslashes( $config['proid_help_msg'] ) );
			$GLOBALS['phpgw']->template->pfp( 'out','proidhelp',true );
		}
	}
?>
