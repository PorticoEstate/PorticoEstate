<?php
	require_once(dirname(__FILE__) . '/' . 'class.bo_workflow_forms.inc.php');

	class bo_user_forms extends bo_workflow_forms
	{
		function bo_user_forms($template)
		{
			parent::bo_workflow_forms($template);
		}

		function show_user_tabs($activtab)
		{
			$this->t->set_file('user_tabs_tpl', 'user_tabs.tpl');
			//stylesheet
			$this->t->set_var('link_tab_css', $this->get_css_link('user_tabs'));
			//tab class, depends on active form
			$this->t->set_var(array(
				'class_tab_new_instances' 	=> ($activtab=='useropeninstance')? 'active_tab': 'inactive_tab',
				'class_tab_global_activities'	=> ($activtab=='userglobalactivities')? 'active_tab': 'inactive_tab',
				'class_tab_my_processes'	=> ($activtab=='userprocesses')? 'active_tab': 'inactive_tab',
				'class_tab_my_activities'	=> ($activtab=='useractivities')? 'active_tab': 'inactive_tab',
				'class_tab_my_instances'	=> (($activtab=='userinstances') || ($activtab=='userviewinstance'))? 'active_tab': 'inactive_tab',
				'link_new_instances'		=> $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_useropeninstance.form'),
				'link_global_activities'	=> $GLOBALS['phpgw']->link('/index.php',array(
									'menuaction'	=> 'workflow.ui_useractivities.form',
									'show_globals'	=> 1,)),
				'link_my_processes'		=> $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_userprocesses.form'),
				'link_my_activities'		=> $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_useractivities.form'),
				'link_my_instances'		=> $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_userinstances.form'),
			));
			$this->translate_template('user_tabs_tpl');
												return $this->t->parse('user_tabs', 'user_tabs_tpl');
		}
	}
?>
