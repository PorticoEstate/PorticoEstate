<?php

	require_once(dirname(__FILE__) . '/' . 'class.monitor.inc.php');

	class ui_monitoractivities extends monitor
	{

		var $public_functions = array(
			'form'	=> true,
		);
		var $filter_is_interactive;
		var $filter_is_autorouted;
		var $filter_type;

		function ui_monitoractivities()
		{
			parent::monitor('monitor_activities');
		}

		function form()
		{
			//override monitor sort values
						$this->order			= get_var('order', 'any', 'wf_flow_num');
						$this->sort_mode		= $this->order . '__'. $this->sort;

						//add new filters
			$this->filter_is_interactive	= get_var('filter_is_interactive', 'any', '');
			$this->filter_is_autorouted	= get_var('filter_is_autorouted', 'any', '');
			$this->filter_type		= get_var('filter_type', 'any', '');
			$this->show_monitor_tabs($this->class_name);

			$this->link_data['search_str'] = $this->search_str;
			if ($this->filter_is_interactive)
			{
				$this->wheres[] = "wf_is_interactive='" . $this->filter_is_interactive . "'";
				$this->link_data['filter_is_interactive'] = $this->filter_is_interactive;
			}
			if ($this->filter_is_autorouted)
			{
				$this->wheres[] = "wf_is_autorouted='" . $this->filter_is_autorouted . "'";
				$this->link_data['filter_is_autorouted'] = $this->filter_is_autorouted;
			}
			if ($this->filter_process)
			{
				$this->wheres[] = "ga.wf_p_id='" .$this->filter_process. "'";
				$this->link_data['filter_process'] = $this->filter_process;
			}
			if ($this->filter_activity)
			{
				$this->wheres[] = "wf_activity_id='" .$this->filter_activity. "'";
				$this->link_data['filter_activity'] = $this->filter_activity;
			}
			if ($this->filter_type)
			{
				$this->wheres[] = "wf_type= '" . $this->filter_type . "'";
				$this->link_data['filter_type'] = $this->filter_type;
			}

			if( count($this->wheres) > 0 )
			{
							$this->where = implode(' and ', $this->wheres);
			}
			else
			{
				$this->where = '';
			}
			$activities	=& $this->process_monitor->monitor_list_activities($this->start, $this->offset, $this->sort_mode, $this->search_str,$this->where);
			//_debug_array($activities);
			$all_types	=& $this->process_monitor->monitor_list_activity_types();

			$this->show_filter_process();
						$this->show_filter_unique_activities($this->where);
						$this->show_filter_types($all_types, $this->filter_type);
						$this->show_filter_is_interactive($this->filter_is_interactive);
						$this->show_filter_is_autorouted($this->filter_is_autorouted);
						$this->show_activities_table($activities['data'], $activities['cant']);

						$this->fill_general_variables();
						$this->finish();
		}

		function show_activities_table(&$activities_data, $total_number)
		{
			//_debug_array($activities_data);

			//warning header names are header_[name or alias of the column in the query without a dot]
			//this is necessary for sorting
			$header_array = array(
				'wf_procname'		=> lang('Process'),
				'wf_name'		=> lang('Name'),
				'wf_type'		=> lang('Type'),
				'wf_is_interactive'	=> lang('Int.'),
				'wf_is_autorouted'	=> lang('Routing'),
			);

			$this->fill_nextmatchs($header_array,$total_number);

			$this->t->set_block('monitor_activities', 'block_act_table', 'act_table');
			if (!$activities_data) {
				$this->t->set_var('act_table', '<tr><td colspan="6" align="center">'. lang('There are no activities available') .'</td></tr>');
			}
			else {
				foreach ($activities_data as $activity)
				{
					if ($activity['wf_type'] == 'standalone')
					{
						$this->t->set_var('act_run', '<a href="'. $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.run_activity.go&activity_id='. $activity['wf_activity_id']) .'"><img src="'. $GLOBALS['phpgw']->common->image('workflow', 'next') .'" alt="'. lang('run activity') .'" title="'. lang('run activity') .'" /></a>');
					}
					elseif ($activity['wf_type'] == 'start')
					{
						$this->t->set_var('act_run', '<a href="'. $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.run_activity.go&activity_id='. $activity['wf_activity_id'] .'&createInstance=1') .'"><img src="'. $GLOBALS['phpgw']->common->image('workflow', 'next') .'" alt="'. lang('run activity') .'" title="'. lang('run activity') .'" /></a>');
					}
					else
					{
						$this->t->set_var('act_run', '');
					}

					$this->t->set_var(array(
						'act_process'			=> $activity['wf_procname'],
						'act_process_version'		=> $activity['wf_version'],
						'process_css_name'		=> $activity['wf_proc_normalized_name'],
						'act_icon'			=> $this->act_icon($activity['wf_type'],$activity['wf_is_interactive']),
						'act_href'			=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_adminactivities.form&p_id='. $activity['wf_p_id'] .'&activity_id='. $activity['wf_activity_id']),
						'act_name'			=> $activity['wf_name'],
						'act_type'			=> $activity['wf_type'],
						'act_is_interactive'		=> $activity['wf_is_interactive'],
						'act_is_autorouted'		=> $activity['wf_is_autorouted'],
						'act_active_href'		=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_monitorinstances.form&filter_process='. $activity['wf_p_id'] .'&filter_status=active&filter_activity='. $activity['wf_activity_id']),
						'act_completed_href'		=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_monitorinstances.form&filter_process='. $activity['wf_p_id'] .'&filter_status=completed&filter_activity='. $activity['wf_activity_id']),
						'act_aborted_href'		=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_monitorinstances.form&filter_process='. $activity['wf_p_id'] .'&filter_status=aborted&filter_activity='. $activity['wf_activity_id']),
						'act_exception_href'		=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_monitorinstances.form&filter_process='. $activity['wf_p_id'] .'&filter_status=exception&filter_activity='. $activity['wf_activity_id']),
						'active_instances'		=> $activity['active_instances'],
						'completed_instances'		=> $activity['completed_instances'],
						'aborted_instances'		=> $activity['aborted_instances'],
						'exception_instances'		=> $activity['exception_instances'],
						'class_alternate_row'		=> $this->nextmatchs->alternate_row_color($tr_color, true),
					));
					$this->t->parse('act_table', 'block_act_table', true);
				}
			}
		}

		function show_filter_types(&$all_types, $filter_type)
		{
			$this->t->set_var('filter_type_selected_all', (!$filter_type)? 'selected="selected"' : '');
			$this->t->set_block('monitor_activities', 'block_filter_type', 'FilterType');
			foreach ($all_types as $type)
			{

				$this->t->set_var(array(
					'filter_type_selected'	=> ($type == $filter_type)? 'selected="selected"' : '',
					'filter_type'			=> $type,
					'filter_types'                  => $type,

				));
				$this->t->parse('FilterType', 'block_filter_type', true);
			}
		}

		function show_filter_is_interactive($filter_is_interactive)
		{
			$this->t->set_var(array(
				'filter_interac_selected_all'	=> ($filter_is_interactive)? '' : 'selected="selected"',
				'filter_interac_selected_y'		=> ($filter_is_interactive == 'y')? 'selected="selected"' : '',
				'filter_interac_selected_n'		=> ($filter_is_interactive == 'n')? 'selected="selected"' : '',
			));
		}

		function show_filter_is_autorouted($filter_is_autorouted)
		{
			$this->t->set_var(array(
				'filter_route_selected_all'	=> ($filter_is_autorouted)? '' : 'selected="selected"',
				'filter_route_selected_y'		=> ($filter_is_autorouted == 'y')? 'selected="selected"' : '',
				'filter_route_selected_n'		=> ($filter_is_autorouted == 'n')? 'selected="selected"' : '',
			));
		}
	}
?>
