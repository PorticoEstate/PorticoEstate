<?php

	require_once(dirname(__FILE__) . '/' . 'class.monitor.inc.php');

	class ui_monitorinstances extends monitor
	{

		var $public_functions = array(
			'form'	=> true,
		);

		//new filters forthis monitor child
		var $filter_status;
		var $filter_act_status;
		var $filter_user;

		var $extra;
		var $extra_params;

		function ui_monitorinstances()
		{
			parent::monitor('monitor_instances');
		}

		function form()
		{
			//override default value from monitor
			$this->order			= get_var('order', 'any', 'wf_instance_id');
			$this->sort_mode                = $this->order . '__' . $this->sort;

			$this->filter_status		= get_var('filter_status', 'any', '');
			$this->filter_act_status	= get_var('filter_act_status', 'any', '');
			$this->filter_user		= get_var('filter_user', 'any', '');

			$this->show_monitor_tabs($this->class_name);
			//echo "order: <pre>";print_r($this->order);echo "</pre>";

			$this->link_data['search_str'] = $this->search_str;
			if ($this->filter_status)
			{
				$this->wheres[] = "gi.`wf_status`='" . $this->filter_status . "'";
				$this->link_data['filter_status'] = $this->filter_status;
			}
			if ($this->filter_process) {
				$this->wheres[] = "gp.wf_p_id='" .$this->filter_process. "'";
				$this->link_data['filter_process'] = $this->filter_process;
			}
			if ($this->filter_activity) {
				$this->wheres[] = "ga.wf_activity_id='" .$this->filter_activity. "'";
				$this->link_data['filter_activity'] = $this->filter_activity;
			}
			if ($this->filter_act_status)
			{
				$this->wheres[] = "gia.`wf_status`='" . $this->filter_act_status . "'";
				$this->link_data['filter_act_status'] = $this->filter_act_status;
			}

			if( count($this->wheres) > 0 )
			{
							$this->where = implode(' and ', $this->wheres);
			}
			else
			{
				$this->where = '';
			}

			if( count($this->link_data) == 0 )
			{
				$this->link_data = '';
			}

			//echo "where: <pre>";print_r($this->where);echo "</pre>";
			//echo "link_data: <pre>";print_r($this->link_data_params);echo "</pre>";

			$all_statuses	= array('aborted', 'active', 'completed', 'exception');
			$users		=& $this->process_monitor->monitor_list_users();
			$instances	=& $this->process_monitor->monitor_list_instances($this->start, $this->offset, $this->sort_mode, $this->search_str, $this->where);

			$this->show_filter_process();
			if ($this->filter_process)
			{
				$this->show_filter_unique_activities("ga.wf_p_id=".$this->filter_process);
			}
			else {
				$this->show_filter_unique_activities();
			}
			$this->show_filter_status($all_statuses, $this->filter_status);
			$this->show_filter_act_status($this->filter_act_status);
			$this->show_filter_user($users, $this->filter_user);
			$this->show_instances_table($instances['data'], $instances['cant']);

			$this->fill_general_variables();
			$this->finish();
		}

		function show_instances_table(&$instances_data, $total_number)
		{
			//warning header names are header_[name or alias of the column in the query without a dot]
			//this is necessary for sorting
			$header_array = array(
				'wf_instance_id'	=> lang('Id'),
				'wf_instance_name'	=> lang('Name'),
				'wf_procname'		=> lang('Process'),
				'wf_activity_name'	=> lang('Activity'),
				'wf_status'		=> lang('Inst. Status'),
				'wf_act_status'		=> lang('Act. Status'),
				'wf_owner'		=> lang('Owner'),
				'wf_user'		=> lang('User'),
			);

			$this->fill_nextmatchs($header_array,$total_number);

			$this->t->set_block('monitor_instances', 'block_inst_table', 'inst_table');

			if (!$instances_data) {
				$this->t->set_var('inst_table', '<tr><td colspan="4" align="center">'. lang('There are no instances available') .'</td></tr>');
			}
			else {
				foreach ($instances_data as $instance)
				{
					$this->t->set_var(array(
						'inst_id_href'		=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_admininstance.form&iid='. $instance['wf_instance_id']),
						'inst_id'		=> $instance['wf_instance_id'],
						'instance_name'		=> $instance['wf_instance_name'],
						'activity_name'		=> $instance['wf_activity_name'],
						'inst_status'		=> $instance['wf_status'],
						'inst_owner'		=> $GLOBALS['phpgw']->common->grab_owner_name($instance['wf_owner']),
						'inst_user'		=> $GLOBALS['phpgw']->common->grab_owner_name($instance['wf_user']),
						'process_css_name'	=> $instance['wf_proc_normalized_name'],
						'inst_procname'		=> $instance['wf_procname'],
						'inst_version'		=> $instance['wf_version'],
						'class_alternate_row'	=> $this->nextmatchs->alternate_row_color($tr_color, true),
						'inst_act_status'	=>$instance['wf_act_status']
					));
					$this->t->parse('inst_table', 'block_inst_table', true);
				}
			}
		}

		function show_filter_status($all_statuses, $filter_status)
		{
			$this->t->set_var('filter_status_selected_all', (!$filter_status)? 'selected="selected"' : '');
			$this->t->set_block('monitor_instances', 'block_filter_status', 'filter_status');
			foreach ($all_statuses as $status)
			{
				$this->t->set_var(array(
					'filter_status_selected'	=> ($status == $filter_status)? 'selected="selected"' : '',
					'filter_status_value'		=> $status,
					'filter_status_name'		=> lang($status),
				));
				$this->t->parse('filter_status', 'block_filter_status', true);
			}
		}

		function show_filter_act_status($filter_act_status)
		{
			$this->t->set_var(array(
				'filter_act_status_selected_all'	=> (!$filter_act_status)? 'selected="selected"' : '',
				'filter_act_status_running'			=> ($filter_act_status == 'running')? 'selected="selected"' : '',
				'filter_act_status_completed'		=> ($filter_act_status == 'completed')? 'selected="selected"' : ''
			));
		}

		function show_filter_user($users, $filter_user)
		{
			$this->t->set_var('filter_user_selected_all', (!$this->filter_user)? 'selected="selected"' : '');
			$this->t->set_block('monitor_instances', 'block_filter_user', 'filter_user');
			foreach ($users as $user)
			{
				$this->t->set_var(array(
					'filter_user_selected'	=> ($user == $filter_user)? 'selected="selected"' : '',
					'filter_user_value'		=> $user,
					'filter_user_name'		=> $GLOBALS['phpgw']->common->grab_owner_name($user)
				));
				$this->t->parse('filter_user', 'block_filter_user', true);
			}
		}
	}
?>
