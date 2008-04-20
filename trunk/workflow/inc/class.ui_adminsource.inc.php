<?php

	require_once(dirname(__FILE__) . '/' . 'class.workflow.inc.php');

	class ui_adminsource extends workflow
	{

		var $public_functions = array(
			'form'	=> true,
		);

		var $process_manager;

		var $activity_manager;

		function ui_adminsource()
		{
			parent::workflow();

					 //regis: acl check
			if ( !(($GLOBALS['phpgw']->acl->check('run',1,'admin')) || ($GLOBALS['phpgw']->acl->check('admin_workflow',1,'workflow'))) )
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				echo lang('access not permitted');
				$GLOBALS['phpgw']->log->message('F-Abort, Unauthorized access to workflow.ui_adminsources');
				$GLOBALS['phpgw']->log->commit();
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$this->process_manager =& CreateObject('workflow.workflow_processmanager');
			$this->activity_manager =& CreateObject('workflow.workflow_activitymanager');
		}

		function form()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['workflow']['title'] . ' - ' . lang('Admin Processes Sources');

			// js function to introduce commands in the textarea
			$GLOBALS['phpgw_info']['flags']['java_script_thirst'] = "<script>
				function setSomeElement(fooel, foo1) {\n
					document.getElementById(fooel).value = document.getElementById(fooel).value + foo1;\n
				}\n
			</script>";
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->t->set_file('admin_source', 'admin_source.tpl');
			$this->t->set_block('admin_source', 'block_select_activity', 'select_activity');

			$activity_id		= (int)get_var('activity_id', 'any', 0);
			$switch_to_code		= get_var('switch_to_code', 'POST', false);
			$switch_to_tpl		= get_var('switch_to_tpl', 'POST', false);
			$source_type		= get_var('source_type', 'POST', 'shared');
			$save				= get_var('save', 'POST', false);
			$source				= get_var('source', 'POST', false);

			if (!$this->wf_p_id) die(lang('No process indicated'));
			$proc_info = $this->process_manager->get_process($this->wf_p_id);

			// fetch activity info
			if ($activity_id)
			{
				$activity_info = $this->activity_manager->get_activity($activity_id);
			}
			else
			{
				$activity_info = array(
					'wf_is_interactive'		=> 'n',
					'wf_normalized_name'	=> 'shared',
				);
			}

			//do we need to check validity, warning high load on database
			$checkvalidity=false;

			// save template and stay in same view
			if ($save)
			{
				$this->save_source($proc_info['wf_normalized_name'], $activity_info['wf_normalized_name'], $source_type, $source);
				if ($activity_id)
				{
					$this->activity_manager->compile_activity($this->wf_p_id, $activity_id);
					$errors =&  $this->activity_manager->get_error(true);
					if (count(array_filter($errors))==0)
					{
						$this->message[] = lang('Source saved');
					}
					else
					{
						$this->message[] = lang('They were problems at the compilation of the source:');
						$this->message = array_merge($this->message,$errors);
					}
					$checkvalidity=true;
				}
			}
			elseif($switch_to_code)
			{
				$source_type = 'code';
			}
			elseif($switch_to_tpl)
			{
				$source_type = 'template';
			}
			elseif($activity_id)
			{
				$source_type = 'code';
			}
			else
			{
				$source_type = 'shared';
			}

			// fetch source
			$data = $this->get_source($proc_info['wf_normalized_name'], $activity_info['wf_normalized_name'], $source_type);
			//echo "data: <pre>";print_r($data);echo "</pre>";

			// check process validity and show errors if necessary
			if ($checkvalidity) $proc_info['isValid'] = $this->show_errors($this->activity_manager, $error_str);

			// fill proc_bar
			$this->t->set_var('proc_bar', $this->fill_proc_bar($proc_info));

			// fill activities select box
			// avoid stats queries on roles here with a false parameter
			$process_activities = $this->activity_manager->list_activities($this->wf_p_id, 0, -1, 'wf_name__asc', '','',false);
			foreach ($process_activities['data'] as $process_activity)
			{
				$this->t->set_var(array(
					'activity_id'		=> $process_activity['wf_activity_id'],
					'selected_activity'	=> ($process_activity['wf_activity_id'] == $activity_id)? 'selected="selected"' : '',
					'activity_name'		=> $process_activity['wf_name'],
				));
				$this->t->parse('select_activity', 'block_select_activity', true);
			}

			//collect some messages from used objects
			$this->message[] = $this->activity_manager->get_error(false, _DEBUG);
			$this->message[] = $this->process_manager->get_error(false, _DEBUG);

			// fill the general variables of the template
			$this->t->set_var(array(
				'message'				=> implode('<br>', array_filter($this->message)),
				'errors'				=> $error_str,
				'form_editsource_action'	=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_adminsource.form'),
				'p_id'					=> $this->wf_p_id,
				'selected_sharedcode'	=> ($activity_id == 0)? 'selected="selected"' : '',
				'source_type'			=> $source_type,
			));

			// generate 'template' or 'code' submit button
			if ($source_type == 'template')
			{
				$this->t->set_var('code_or_tpl_btn', '<input type="submit" name="switch_to_code" value="'. lang('show code') .'" />');
			}
			elseif ($activity_info['wf_is_interactive'] == 'y')
			{
				$this->t->set_var('code_or_tpl_btn', '<input type="submit" name="switch_to_tpl" value="'. lang('show template') .'" />');
			}
			else
			{
				$this->t->set_var('code_or_tpl_btn', '');
			}

			$this->show_side_commands($source_type, $activity_info);

			$this->translate_template('admin_source');

			//only now we can insert data, to prevent templating vars used in the source
			$this->t->set_block('admin_source', 'block_datas', 'datas');
			$this->t->set_var(array('data'	=> Htmlspecialchars($data),));
			$this->t->parse('datas', 'block_datas', true);
			$this->t->pparse('output', 'admin_source');
		}

		function show_side_commands($source_type, $activity_info)
		{
			if ($source_type == 'template')
			{
				$side_commands = lang('template');
			}
			else
			{
				$side_commands = "
					<a  href=\"javascript:setSomeElement('src','". '$instance' ."->setNextUser(\\'\\');');\">". lang('Set next user') ."</a><hr/>
					<a  href=\"javascript:setSomeElement('src','". '$instance' ."->get(\\'\\');');\">". lang('Get property') ."</a><hr/>
					<a  href=\"javascript:setSomeElement('src','". '$instance' ."->set(\\'\\',\\'\\');');\">". lang('Set property') ."</a><hr />";
				if ($activity_info['isInteractive'] == 'y')
				{
					$side_commands .= "
					<a href=\"javascript:setSomeElement('src','". '$instance' ."->complete();');\">". lang('Complete') ."</a><hr/>
					<a href=\"javascript:setSomeElement('src','if(isset(". '$_REQUEST' ."[\\'save\\'])){\n ". ' $instance' ."->complete();\n}');\">". lang('Process form'). "</a><hr/>";
				}
				if ($activity_info['type'] == 'switch')
				{
					$side_commands .= "
						<a href=\"javascript:setSomeElement('src','". '$instance' ."->setNextActivity(\\'\\');');\">". lang('Set Next act') ."</a><hr />
						<a href=\"javascript:setSomeElement('src','if() {\n  ". '$instance' ."->setNextActivity(\\'\\');\\n}');\">". lang('If:SetNextact') ."</a><hr />
						<a href=\"javascript:setSomeElement('src','switch(". '$instance' ."->get(\\'\\')){\\n  case:\\'\\':\\n  ". '$instance' ."->setNextActivity(\\'\\');\\n  break;\\n}');\">". lang('Switch construct') ."</a><hr />";
				}

			}
			$this->t->set_var('side_commands', $side_commands);
		}

	}
?>
