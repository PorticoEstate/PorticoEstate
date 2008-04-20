<?php

	require_once(dirname(__FILE__) . '/' . 'class.bo_workflow_forms.inc.php');

	class monitor extends bo_workflow_forms
	{
		var $process_monitor;

		var $all_processes;

		var $all_activities;

		var $filter_process;
		var $filter_activity;

		function monitor($template_name)
		{
			parent::bo_workflow_forms($template_name);

						//regis: acl check
			if(!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
			{
				if(!$GLOBALS['phpgw']->acl->check('monitor_workflow',1,'workflow'))
				{
					$GLOBALS['phpgw']->common->phpgw_header();
					echo parse_navbar();
					echo lang('access not permitted');
					$GLOBALS['phpgw']->log->message('F-Abort, Unauthorized access to workflow.monitor');
					$GLOBALS['phpgw']->log->commit();
					$GLOBALS['phpgw']->common->phpgw_exit();
				}
			}

			//retrieving common filters and stats common for all monitor forms
			$this->process_monitor	=& CreateObject('workflow.workflow_processmonitor');
			//do not forget the false to avoid unnecessary queries
			$this->all_processes	=& $this->process_monitor->monitor_list_processes(0, -1, 'wf_name__desc', '', '', false);
			//do not forget the false to avoid unnecessary queries
			$this->all_activities	=& $this->process_monitor->monitor_list_activities(0, -1, 'wf_name__desc', '', '', false);
			$this->stats 		=& $this->process_monitor->monitor_stats();
			$this->filter_process	= get_var('filter_process', 'any', '');
			$this->filter_activity	= get_var('filter_activity', 'any', '');
		}

		function show_filter_process()
		{
			//for other forms wanting the actual filter:
			$this->t->set_var('filter_process_up', $this->filter_process);
			// now show the filter process select
			$this->t->set_var('filter_process_selected_all', (!$this->filter_process)? 'selected="selected"' : '');
			$this->t->set_block($this->template_name, 'block_filter_process', 'filter_process');
			foreach ($this->all_processes['data'] as $process)
			{
				$this->t->set_var(array(
					'filter_process_selected'	=> ($process['wf_p_id'] == $this->filter_process)? 'selected="selected"' : '',
					'filter_process_value'		=> $process['wf_p_id'],
					'filter_process_name'		=> $process['wf_name'],
					'filter_process_version'	=> $process['wf_version'],

				));
				$this->t->parse('filter_process', 'block_filter_process', true);
			}
		}

		function show_filter_activities()
		{
			$this->t->set_var('filter_activity_selected_all', (!$this->filter_activity)? 'selected="selected"' : '');
			$this->t->set_block($this->template_name, 'block_filter_activity', 'filter_activity');
			foreach ($this->all_activities['data'] as $activity)
			{
				$this->t->set_var(array(
					'filter_activity_selected'	=> ($activity['wf_activity_id'] == $this->filter_activity)? 'selected="selected"' : '',
					'filter_activity_value'		=> $activity['wf_activity_id'],
					'filter_activity_name'		=> $activity['wf_name'],

				));
				$this->t->parse('filter_activity', 'block_filter_activity', true);
			}
		}


		function show_filter_unique_activities($where = '')
		{
			//echo "where: <pre>";print_r($where);echo "</pre>";

			//do not forget the false to avoid unnecessary queries
				$unique_activities = $this->process_monitor->monitor_list_activities(0, -1, 'wf_name__desc', '', $where, false);

			//echo "unique_activities: <pre>";print_r($unique_activities);echo "</pre>";

			$this->t->set_var('filter_activity_selected_all', (!$this->filter_activity)? 'selected="selected"' : '');
			$this->t->set_block($this->template_name, 'block_filter_activity', 'filter_activity');
			foreach ($unique_activities['data'] as $activity)
			{
				$this->t->set_var(array(
					'filter_activity_selected'	=> ($activity['wf_activity_id'] == $this->filter_activity)? 'selected="selected"' : '',
					'filter_activity_value'		=> $activity['wf_activity_id'],
					'filter_activity_name'		=> $activity['wf_name'].' ('.$activity['wf_procname']. ' '.$activity['wf_version'].')'
				));
				$this->t->parse('filter_activity', 'block_filter_activity', true);
			}
		}

		//!fill general datas of monitor forms
		/**
		 * theses datas are:
		 * 	$monitor_stats	: stats about the current monitor
		 * 	others: all datas defined in bo_workflow_form->fill_form_variables
		 */
		function fill_general_variables()
		{
			//collect error messages before
			$this->message[] = $this->process_monitor->get_error(false, _DEBUG);
			$this->fill_form_variables();
			$this->t->set_var(array(
				'monitor_stats'			=> $this->fill_monitor_stats($this->stats),
			));
		}


		function fill_monitor_stats($stats)
		{
			$this->t->set_file('monitor_stats_tpl', 'monitor_stats.tpl');
			$numprocs = $stats['processes'];
			$actprocs = $stats['active_processes'];
			$runprocs = $stats['running_processes'];
			$this->t->set_var(array(
				'stats_processes_info'		=> lang('%1 processes (%2 active) (%3 being_run)',$numprocs, $actprocs, $runprocs),
				'href_active_instances'		=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_monitorinstances.form&filter_status=active'),
				'stats_active_instances'	=> lang('%1 active', $stats['active_instances']),
				'href_completed_instances'	=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_monitorinstances.form&filter_status=completed'),
				'stats_completed_instances'	=> lang('%1 completed',$stats['completed_instances']),
				'href_aborted_instances'	=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_monitorinstances.form&filter_status=aborted'),
				'stats_aborted_instances'	=> lang('%1 aborted',$stats['aborted_instances']),
				'href_exception_instances'	=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_monitorinstances.form&filter_status=exception'),
				'stats_exception_instances'	=> lang('%1 exception',$stats['exception_instances']),
			));
			//stylesheet
			$this->t->set_var(array(
 			 	'monitors_css_link' => $this->get_css_link('monitors'),
			));

			$this->translate_template('monitor_stats_tpl');
			return $this->t->parse('monitor_stats', 'monitor_stats_tpl');
		}

		function show_monitor_tabs($activtab)
		{
			$this->t->set_file('monitor_tabs_tpl', 'monitor_tabs.tpl');
			//stylesheet
			$this->t->set_var(array(
 			 	'monitor_css_link' => $this->get_css_link('monitor_tabs'),
			));

			$improc	= $GLOBALS['phpgw']->common->image('workflow', 'monitorprocess');
			$imacti	= $GLOBALS['phpgw']->common->image('workflow', 'monitoractivity');
			$iminst	= $GLOBALS['phpgw']->common->image('workflow', 'monitorinstance');
			$imwork	= $GLOBALS['phpgw']->common->image('workflow', 'monitor');

			//special case for ui_viewworkitem, we set ui_monitor_workitems as active tab
			if ($activtab=='viewworkitem') $activtab='monitorworkitems';

			//tab class, depends on active form
			$this->t->set_var(array(
				'class_tab_monitor_processes' 	=> ($activtab=='monitorprocesses')? 'active_tab': 'inactive_tab',
				'class_tab_monitor_activities'	=> ($activtab=='monitoractivities')? 'active_tab': 'inactive_tab',
				'class_tab_monitor_instances'	=> ($activtab=='monitorinstances')? 'active_tab': 'inactive_tab',
				'class_tab_monitor_workitems'	=> ($activtab=='monitorworkitems')? 'active_tab': 'inactive_tab',
				'class_tab_monitors'		=> ($activtab=='monitors')? 'active_tab': 'inactive_tab',
				'link_monitors'			=> $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_monitors.form'),
				'link_monitor_processes'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_monitorprocesses.form'),
				'link_monitor_workitems'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_monitorworkitems.form'),
				'link_monitor_activities'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_monitoractivities.form'),
				'link_monitor_instances'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_monitorinstances.form'),
				'img_monitor_processes'		=> '<img src="'.$improc.'" alt="{lang_monitor_processes}" title="{lang_monitor_processes}">',
				'img_monitor_activities'	=> '<img src="'.$imacti.'" alt="{lang_monitor_activities}" title="{lang_monitor_activities}">',
				'img_monitor_instances'		=> '<img src="'.$iminst.'" alt="{lang_monitor_instances}" title="{lang_monitor_instances}">',
				'img_monitor_workitems'		=> '<img src="'.$imwork.'" alt="{lang_monitor_workitems}" title="{lang_monitor_workitems}">',
			));
			$this->translate_template('monitor_tabs_tpl');
												return $this->t->parse('monitor_tabs', 'monitor_tabs_tpl');
		}

	}
?>
