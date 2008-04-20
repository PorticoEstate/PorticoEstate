<?php
	require_once(dirname(__FILE__) . '/' . 'class.bo_user_forms.inc.php');

	class ui_useropeninstance extends bo_user_forms
	{
		var $public_functions = array(
			'form'	=> true
		);

		//communication with the workflow engine
		var $GUI;

		function ui_useropeninstance()
		{
			parent::bo_user_forms('user_openinstance');
			$this->GUI =& CreateObject('workflow.workflow_gui');
		}

		function form()
		{
			$activities =& $this->GUI->gui_list_user_start_activities($GLOBALS['phpgw_info']['user']['account_id'], $this->start, $this->offset, $this->sort_mode, $this->search_str, '');

			$this->link_data = array(
				'find'	=> $this->search_str,
			);

			$this->fill_table($activities['data'], $activities['cant']);
			$this->show_user_tabs($this->class_name);

			//collect error messages
			$this->message[] = $this->GUI->get_error(false, _DEBUG);

			$this->fill_form_variables();
			$this->finish();
		}

		function fill_table(&$activities, $total_number)
		{
			//_debug_array($activities);

			//warning header names are header_[name or alias of the column in the query without a dot]
			//this is necessary for sorting
			$header_array = array(
				'wf_procname'		=> lang('Process'),
				'wf_name'		=> lang('Start activity'),
			);
			$this->fill_nextmatchs($header_array,$total_number);

			$this->t->set_var(array(
				'help_info' => lang('by running theses links you will create new instances of the related process.')
			));

			// now the table
			$this->t->set_block('user_openinstance', 'block_table', 'table');
			$arrowimg = '<img src="'.$GLOBALS['phpgw']->common->image('workflow', 'runform') .'" alt="'. lang('start process') .'" title="'. lang('start process') .'" />';
			foreach($activities as $activity_data)
			{
				$runlink = $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.run_activity.go&activity_id=' . $activity_data['wf_activity_id']);
				$this->t->set_var(array(
					'link_starting'		=> $runlink,
					'process_css_name'	=> $activity_data['wf_normalized_name'],
					'wf_procname'		=> $activity_data['wf_procname'].':'.$activity_data['wf_version'],
					'actname'		=> $activity_data['wf_name'],
					'arrow'			=> '<a href="'.$runlink.'">'.$arrowimg.'</a>',
					'color_line'		=> $this->nextmatchs->alternate_row_color($tr_color, true)
				));
				$this->t->parse('table', 'block_table', true);
			}
			if($total_number==0)
			{
				$this->t->set_var('table', '<tr><td colspan="3" align="center">'. lang('There are no process available') .'</td></tr>');
			}
		}

	}
?>
