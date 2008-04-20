<?php

	require_once(dirname(__FILE__) . '/' . 'class.bo_user_forms.inc.php');

	class ui_userprocesses extends bo_user_forms
	{
		var $public_functions = array(
			'form'	=> true
		);

		//communication with the workflow engine
		var $GUI;

		function ui_userprocesses()
		{
			parent::bo_user_forms('user_processes');
			$this->GUI	=& CreateObject('workflow.workflow_gui');
		}

		function form()
		{
			$this->t->set_block('user_processes', 'block_table', 'table');

			$this->link_data	= array(
				'find'	=> $this->search_str,
			);

			$processes =& $this->GUI->gui_list_user_processes($GLOBALS['phpgw_info']['user']['account_id'], $this->start, $this->offset, $this->sort_mode, $this->search_str, '');

			// fill the table
			$this->fill_table($processes['data'],$processes['cant']);
			$this->show_user_tabs($this->class_name);

			//collect error messages
			$this->message[] = $this->GUI->get_error(false, _DEBUG);

			$this->fill_form_variables();
			$this->finish();
		}

		function fill_table(&$processes_list_data, $total_number)
		{
			//warning header names are header_[name or alias of the column in the query without a dot
			//this is necessary for sorting
			$header_array = array(
							'wf_procname'       => lang('Name'),
			);
			$this->fill_nextmatchs($header_array,$total_number);

			foreach ($processes_list_data as $process_data)
			{
				$this->t->set_var(array(
					'process_css_name'	=> $process_data['normalized_name'],
					'link_wf_procname'	=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_useractivities.form&filter_process='. $process_data['wf_p_id']),
					'item_wf_procname'	=> $process_data['wf_procname'],
					'item_version'		=> $process_data['wf_version'],
					'link_activities'	=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_useractivities.form&filter_process='. $process_data['wf_p_id']),
					'item_activities'	=> $process_data['wf_activities'],
					'link_instances'	=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_userinstances.form&filter_process='. $process_data['wf_p_id']),
					'item_instances'	=> $process_data['wf_instances'],
					'color_line'		=> $this->nextmatchs->alternate_row_color($tr_color, true),
				));
				$this->t->parse('table', 'block_table', true);
			}
			if (!($total_number)) $this->t->set_var('table', '<tr><td colspan="3" align="center">'. lang('There are no processes available') .'</td></tr>');
		}

	}
?>
