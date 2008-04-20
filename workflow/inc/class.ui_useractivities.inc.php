<?php
	require_once(dirname(__FILE__) . '/' . 'class.bo_user_forms.inc.php');

	class ui_useractivities extends bo_user_forms
	{
		var $public_functions = array(
			'form'	=> true
		);

		// true or false, decide if the form is the 'show global activities form' or 'show avaible acvtivities with instances form'
		var $show_globals;

		//communication with the workflow engine
		var $GUI;

		var $filter_process;

		var $filter_activity;


		function ui_useractivities()
		{
			parent::bo_user_forms('user_activities');
			$this->GUI =& CreateObject('workflow.workflow_gui');
		}

		function form($force_show_globals=false)
		{
			// when show_globals is on we show only standalone activities
			// else we only show activities with instances avaible
			// force_show_globals can be true when called with ExecMethod, for example by ../index.php
			if ($force_show_globals)
			{
				$this->show_globals = true;
			}
			else
			{
				$this->show_globals   = get_var('show_globals', 'any', 0);
			}
			//echo '<br>show_globals:'.$this->show_globals;
			$this->filter_process   = get_var('filter_process', 'any', '');
			//echo '<br>filter_process:'.$this->filter_process;
			$this->filter_activity  = get_var('filter_activity', 'any', '');
			//echo '<br>filter_activity:'.$this->filter_activity;

			if ($this->filter_process) $this->wheres[] = 'gp.wf_p_id=' . (int)$this->filter_process;
			if ($this->filter_activity) $this->wheres[] = "ga.wf_name='" . $this->GUI->security_cleanup($this->filter_activity, false, true)."'";
			$remove_non_pseudo = false; //remove 'classical' activities, other are pseudo-activities because not related to instances
			$select_standalone = false; //add standalone activities, not a classical one becaus no instance is associated to it
			$select_start = false; //idem with start
			$select_view = false; //idem no real activity-instance association
			if ($this->show_globals)
			{
				//we want only standalone activities
				//this will filter the activities select list
				$remove_non_pseudo = true;
				$select_standalone = true;
				//we need activities without instances
				$remove_zero = false;
			}
			else
			{
				//we do not need activities without instances
				$remove_zero = true;
			}
			$this->wheres = implode(' and ', $this->wheres);
			//echo "<br>wheres:".$this->wheres;
			$this->link_data = array(
				'show_globals'		=> $this->show_globals,
				'find'			=> $this->search_str,
				'filter_process'	=> $this->filter_process,
				'filter_activity'	=> $this->filter_activity,
			);

			$all_processes =& $this->GUI->gui_list_user_processes($GLOBALS['phpgw_info']['user']['account_id'], 0, -1, 'wf_procname__asc', '', '');
			$all_activities =&  $this->GUI->gui_list_user_activities_by_unique_name($GLOBALS['phpgw_info']['user']['account_id'], 0, -1, 'ga.wf_name__asc', '', '',$remove_non_pseudo, $select_start, $select_standalone, $select_view);
			$activities =& $this->GUI->gui_list_user_activities($GLOBALS['phpgw_info']['user']['account_id'], $this->start, $this->offset, $this->sort_mode, $this->search_str, $this->wheres, $remove_zero, $remove_non_pseudo, $select_start, $select_standalone, $select_view);

			// show process select box
			$this->show_process_select_box($all_processes['data']);
			//show activities select box
			$this->show_select_activities($all_activities['data']);

			// show activities list
			$this->show_activities_list($activities['data'], $activities['cant']);

			$this->t->set_var(array(
				'show_globals'		=> $this->show_globals,
				'filter_activity'	=> $this->filter_activity,
				'filter_process'	=> $this->filter_process,
			));

			//for the tabs this form can be viewed as two different forms
			if ($this->show_globals)
			{
				$this->show_user_tabs('userglobalactivities');
			}
			else
			{
				$this->show_user_tabs($this->class_name);
			}

			//collect error messages
			$this->message[] = $this->GUI->get_error(false, _DEBUG);

			$this->fill_form_variables();
			$this->finish();
		}

		function show_activities_list(&$activities_data, $total_number)
		{
			//_debug_array($activities_data);
			//warning header names are header_[name or alias of the column in the query without a dot]
			//this is necessary for sorting
			$header_array = array(
				'wf_procname'	=> lang('Process'),
				'wf_name'  	=> lang('Activity'),
			);
			$this->fill_nextmatchs($header_array,$total_number);

			$this->t->set_block('user_activities', 'block_activities_list', 'activities_list');
			foreach ($activities_data as $activity)
			{
				// for standalone or start activities we make arrows to execute the activity
				if ($activity['wf_is_interactive'] == 'y' && ($activity['wf_type'] == 'start' || $activity['wf_type'] == 'standalone'))
				{
					$arrow = '<a href="'. $GLOBALS['phpgw']->link('/index.php', array(
							'menuaction'	=> 'workflow.run_activity.go',
							'activity_id'	=> $activity['wf_activity_id'],
						)) .'"><img src="'. $GLOBALS['phpgw']->common->image('workflow', 'runform') .'" alt="'. lang('run activity') .'" title="'. lang('run activity') .'" /></a>';
				}
				else
				{
					//we use the normally empty arrow to show number of instances
					$arrow = '('.$activity['wf_instances'].')';
				}
				//create the activity name with a link if there are some instances to see
				$act_name = '';
				if ($activity['wf_instances'] > 0) $act_name = '<a href="'. $GLOBALS['phpgw']->link('/index.php', array(
						'menuaction'		=> 'workflow.ui_userinstances.form',
						'filter_process'	=> $activity['wf_p_id'],
						'filter_activity'	=> $activity['wf_activity_id'],
					)) .'">';
				$act_name .= $activity['wf_name'];
				if ($activity['wf_instances'] > 0) $act_name .= '</a>';
				$this->t->set_var(array(
					'process_css_name'	=> $activity['wf_normalized_name'],
					'act_wf_procname'	=> $activity['wf_procname'],
					'act_proc_version'	=> $activity['wf_version'],
					'act_icon'		=> $this->act_icon($activity['wf_type'],$activity['wf_is_interactive']),
					'act_name'		=> $act_name,
					'run_act'		=> $arrow,
					'color_line'		=> $this->nextmatchs->alternate_row_color($tr_color, true),
				));
				$this->t->parse('activities_list', 'block_activities_list', true);
			}
			if (!($total_number)) $this->t->set_var('activities_list', '<tr><td colspan="3" align="center">'. lang('There are no user activites available') .'</td></tr>');
		}

		function show_process_select_box(&$processes_data)
		{
			if (!$this->filter_process)
			{
				$this->t->set_var('filter_process_all_selected', 'selected="selected"');
			}
			else
			{
				$this->t->set_var('filter_process_all_selected', '');
			}

			$this->t->set_block('user_activities', 'block_select_process', 'select_process');
			//echo "processes_data: <pre>";print_r($processes_data);echo "</pre>";
			foreach ($processes_data as $process_data)
			{
				//echo "process_data: <pre>";print_r($process_data);echo "</pre>";
				$this->t->set_var(array(
					'filter_process_selected'	=> ($process_data['wf_p_id'] == $this->filter_process)? 'selected="selected"' : '',
					'filter_process_value'		=> $process_data['wf_p_id'],
					'filter_process_name'		=> $process_data['wf_procname'],
					'filter_process_version'	=> $process_data['wf_version'],
				));
				$this->t->parse('select_process', 'block_select_process', true);
			}
			if (!count($processes_data)) $this->t->set_var('select_process', '');
		}

		function show_select_activities(&$all_activities_data)
		{
			$this->t->set_block('user_activities', 'block_filter_activity', 'select_activity');
			$this->t->set_var('filter_activity_selected_all', ($this->filter_activity=='')? 'selected="selected"' : '');

			foreach ($all_activities_data as $activity_data)
			{
				$this->t->set_var(array(
					'filter_activity_selected'	=> ($this->filter_activity == $activity_data['wf_name'])? 'selected="selected"' : '',
					'filter_activity_name'		=> $activity_data['wf_name']
				));
				$this->t->parse('select_activity', 'block_filter_activity', true);
			}
		}

	}
?>
