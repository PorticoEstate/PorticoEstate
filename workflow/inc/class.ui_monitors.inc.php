<?php

	require_once(dirname(__FILE__) . '/' . 'class.monitor.inc.php');

	class ui_monitors extends monitor
	{

		var $public_functions = array(
			'form'	=> true,
		);
		var $filter_process_cleanup_aborted;
		var $cleanup_aborted_instances;

		function ui_monitors()
		{
			//parent monitor with template name
			parent::monitor('monitors');
		}

		function form()
		{
			$this->filter_process_cleanup_aborted = (int)get_var('filter_process_cleanup_aborted', 'POST', '');
			$this->filter_process_cleanup = (int)get_var('filter_process_cleanup', 'POST', '');
			$this->cleanup_aborted_instances = get_var('cleanup_aborted_instances', 'POST', '');
			$this->cleanup_process = get_var('cleanup_process', 'POST', '');

			$this->show_monitor_tabs($this->class_name);
			$this->show_filter_process_cleanup_aborted();
			$this->show_filter_process_cleanup();

			if (!($this->cleanup_aborted_instances==''))
			{
				//process removing of aborted instances
				$this->perform_aborted_cleanup();
			}
			if (!($this->cleanup_process==''))
			{
				//process removing of all instances and workitems of the choosen process
				$this->perform_process_cleanup();
			}

			$this->fill_local_variables();
			$this->fill_monitor_stats($this->stats);
												$this->t->set_var(array('message' => implode('<br>', $this->message)));
												$this->fill_general_variables();
			$this->finish();
		}

		function perform_aborted_cleanup()
		{
			if (!($this->filter_process_cleanup_aborted))
			{
				$this->message[] = lang('removing instances and workitems for aborted instances on all processes');
			}
			else
			{
				$this->message[] = lang('removing instances and workitems for aborted instances on process #%1',$this->filter_process_cleanup_aborted);
			}
			if ($this->process_monitor->remove_aborted($this->filter_process_cleanup_aborted))
			{
				$this->message[] = lang('complete cleanup of aborted instances done');
				// we need to reload the monitor stats, we just changed them, in fact
				$this->stats =& $this->process_monitor->monitor_stats();
			}
			else
			{
				$this->message[] = lang('error: removing was not done');
			}
		}

		function perform_process_cleanup()
		{
			$this->message[] = lang('removing instances and workitems for all instances on process #%1',$this->filter_process_cleanup);
			if ($this->process_monitor->remove_all($this->filter_process_cleanup))
			{
				$this->message[] = lang('complete cleanup of all instances done');
				// we need to reload the monitor stats, we just changed them, in fact
				$this->stats =& $this->process_monitor->monitor_stats();
			}
			else
			{
				$this->message[] = lang('error: removing was not done');
			}
		}

		function show_filter_process_cleanup_aborted()
		{
			$this->t->set_var(array(
				'filter_process_cleanup_aborted_selected_all' => (!$this->filter_process_cleanup_aborted)? 'selected="selected"' : ''
			));
			$this->t->set_block($this->template_name, 'block_filter_process_cleanup_aborted', 'filter_process_cleanup_aborted');
			foreach ($this->all_processes['data'] as $process)
			{
				$this->t->set_var(array(
					'filter_process_cleanup_aborted_selected'       => ($process['wf_p_id'] == $this->filter_process_cleanup_aborted)? 'selected':'',
					'filter_process_cleanup_aborted_value'          => $process['wf_p_id'],
					'filter_process_cleanup_aborted_name'           => $process['wf_name'],
					'filter_process_cleanup_aborted_version'        => $process['wf_version'],
				));
				$this->t->parse('filter_process_cleanup_aborted', 'block_filter_process_cleanup_aborted', true);
			}
		}

		function show_filter_process_cleanup()
		{
			$this->t->set_block($this->template_name, 'block_filter_process_cleanup', 'filter_process_cleanup');
			foreach ($this->all_processes['data'] as $process)
			{
				$this->t->set_var(array(
					'filter_process_cleanup_selected'       => ($process['wf_p_id'] == $this->filter_process_cleanup)? 'selected':'',
					'filter_process_cleanup_value'          => $process['wf_p_id'],
					'filter_process_cleanup_name'           => $process['wf_name'],
					'filter_process_cleanup_version'        => $process['wf_version'],
				));
				$this->t->parse('filter_process_cleanup', 'block_filter_process_cleanup', true);
			}
		}

		//! fill help messages
		function fill_local_variables()
		{
			$this->t->set_var(array(
					'help_monitor_processes'	=> lang('list of processes with status and validity and, for each, counters of instances by status'),
					'help_monitor_activities'	=> lang('list of all activities with, for each,  counters of instances by status'),
					'help_monitor_instances'	=> lang('list of all instances with info about current status and activities and link to administration of theses instances'),
					'help_monitor_workitems'	=> lang('list of all history items made by instances while they travel in the workflow with information about duration and date'),
					'help_cleanup_aborted'		=> lang('warning: by using this button you will definitively remove all aborted instances AND their history (workitems) for the selected process.'),
					'help_cleanup'			=> lang('warning: by using this button you will definitively remove all instances AND their history (workitems) for the selected process, whatever the state they are.'),
			));
		}

	}
?>
