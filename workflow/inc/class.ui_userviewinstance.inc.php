<?php
	require_once(dirname(__FILE__) . '/' . 'class.bo_user_forms.inc.php');

	class ui_userviewinstance extends bo_user_forms
	{
		var $public_functions = array(
			'form'	=> true
		);

		//communication with the workflow engine
		var $GUI;
		var $instance_manager;
		//communication with the security object of the engine
		var $security;

		function ui_userviewinstance()
		{
			parent::bo_user_forms('user_viewinstance');

			//$this->GUI		=& CreateObject('workflow.workflow_gui');
			//$this->instance_manager	=& CreateObject('workflow.workflow_instancemanager');
			$this->security =& CreateObject('workflow.workflow_wfsecurity');
			$this->instance =& CreateObject('workflow.workflow_instance');
		}

		function form()
		{
			$iid = get_var('iid', 'any', 0);
			if($iid != 0)
			{
				$this->instance->getInstance($iid);
				//Security check
				if (!($this->security->checkUserAction(0,$iid,'view')))
				{
					$this->message[] = $this->security->get_error(false, _DEBUG);
					$this->t->set_var(array(
						'instance'	=> '',
						'history'	=> '',
					));
				}
				else
				{
					$inst_parser	=& CreateObject('workflow.bo_uiinstance', $this->t);
					//this is necessary the CreateObject did not use ref parameters
					$inst_parser->t =& $this->t;

					//$parser->parse_history($instance);
					$inst_parser->parse_instance($this->instance);
					$inst_parser->parse_instance_history($this->instance->workitems);

					$this->t->set_var(array(
						'instance'	=> $this->t->parse('output', 'instance_tpl'),
						'history'	=> $this->t->parse('output', 'history_tpl'),
					));
				}
			}
			else
			{
				//echo lang('no instance given, nothing to show');
				//$GLOBALS['phpgw']->common->phpgw_exit();
				$this->message[] = lang('no instance given, nothing to show');
				$this->t->set_var(array(
						'instance'	=> '',
						'history'	=> '',
				));
			}

			// fill the table
			//$this->fill_table($processes['data'],$processes['cant']);
			$this->show_user_tabs($this->class_name);

			//collect error messages
			if (isset($this->security)) $this->message[] = $this->security->get_error(false, _DEBUG);
			if (isset($this->instance)) $this->message[] = $this->instance->get_error(false, _DEBUG);

			$this->fill_form_variables();
			$this->finish();
		}

	}
?>
