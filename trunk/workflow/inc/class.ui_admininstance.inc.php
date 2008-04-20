<?php

	require_once(dirname(__FILE__) . '/' . 'class.workflow.inc.php');

	// TODO: allow to enter comments

	class ui_admininstance extends workflow
	{
		var $public_functions = array(
			'form'	=> true,
		);

		var $instance_manager;

		var $process_manager;

		var $activity_manager;
		//phpgw category object to handle category in 'human' form
		var $cat;

		function ui_admininstance()
		{
			parent::workflow();

					 //regis: acl check
			if ( !(($GLOBALS['phpgw']->acl->check('run',1,'admin')) || ($GLOBALS['phpgw']->acl->check('admin_workflow',1,'workflow'))) )
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				echo lang('access not permitted');
				$GLOBALS['phpgw']->log->message('F-Abort, Unauthorized access to workflow.ui_admininstance');
				$GLOBALS['phpgw']->log->commit();
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$this->instance_manager	=& CreateObject('workflow.workflow_instancemanager');
			//$this->process_manager	=& CreateObject('workflow.workflow_processmanager');
			//$this->activity_manager	=& CreateObject('workflow.workflow_activitymanager');
			//$this->cat		=& CreateObject('phpgwapi.categories');
		}

		function form()
		{
			// FIXME: active user should be done in a per activity level, not per instance
			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['workflow']['title'] . ' - ' . lang('Admin Instance');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->t->set_file('admin_instance', 'admin_instance.tpl');

			$iid			= (int)get_var('iid', 'any', 0);
			$instance_status	= get_var('status', 'POST', '');
			$instance_name          = get_var('instance_name', 'POST', '');
			$instance_owner		= (int)get_var('owner', 'POST', 0);
			$instance_priority      = (int)get_var('instance_priority', 'POST', 0);
			$instance_category      = (int)get_var('instance_category', 'POST', 0);

			// save changes
			if (isset($_POST['save']))
			{
				//save changes only if the field really changed
				$old_instance_status = get_var('instance_previous_status', 'POST', '');
				$old_instance_name = get_var('instance_previous_name', 'POST', '');
				$old_instance_owner	= (int)get_var('instance_previous_owner', 'POST', 0);
				$old_instance_priority = (int)get_var('instance_previous_priority', 'POST', 0);
				$old_instance_category = (int)get_var('instance_previous_category', 'POST', 0);

				if (!($instance_status == $old_instance_status))
				{
					$this->instance_manager->set_instance_status($iid, $instance_status);
				}
				if (!($instance_owner == $old_instance_owner))
				{
					$this->instance_manager->set_instance_owner($iid, $instance_owner);
				}
				if (!($instance_name == $old_instance_name))
				{
					$this->instance_manager->set_instance_name($iid, $instance_name);
				}
				if (!($instance_priority == $old_instance_priority))
				{
					$this->instance_manager->set_instance_priority($iid, $instance_priority);
				}
				if (!($instance_category == $old_instance_category))
				{
					$this->instance_manager->set_instance_category($iid, $instance_category);
				}

				// user reasignment
				if(count($_POST['acts']) != 0)
				{
				 	$activityusers = get_var('acts', 'POST', array());
				 	$oldactivityusers = get_var('previous_acts', 'POST', array());
				 	foreach (array_keys($activityusers) as $act)
				 	{
				 		$new_user = $activityusers[$act];
				 		if (!($new_user)) $new_user = '*';
							$previous_user =$oldactivityusers[$act];
							if (!($new_user==$previous_user))
							{
								$this->instance_manager->set_instance_user($iid, $act , $new_user);
						}
					}
				}

				if ($_POST['sendto'])
				{
					$this->instance_manager->set_instance_destination($iid, $_POST['sendto']);
				}
			}

			// save changes on properties
			if (isset($_POST['saveprops']))
			{
				//save properties
				//no more serialize here
				$props = $_POST['props'];
				$this->instance_manager->set_instance_properties($iid,$props);
			}

			// delete a property
			if (isset($_GET['unsetprop']))
			{
				//remove one and save properties
				$arrayprops =& $this->instance_manager->get_instance_properties($iid);
				unset($arrayprops[$_GET['unsetprop']]);
				//no more serialize here
				$this->instance_manager->set_instance_properties($iid,$arrayprops);
			}

			// add a property
			if (isset($_POST['addprop']))
			{
				//add one and save properties
				$arrayprops =& $this->instance_manager->get_instance_properties($iid);
				$propname= $this->instance_manager->normalize_name($_POST['name']);
				if (isset($arrayprops[$propname]))
				{
					$this->message[]=lang('property %1 already exists', $propname);
				}
				else
				{
					$arrayprops[$propname]= $_POST['value'];
					//no more serialize here
					$this->instance_manager->set_instance_properties($iid,$arrayprops);
				}
			}

			if (!$iid) die(lang('No instance indicated'));

			//now we use bo_uiinstance to display the form
			$instance =& CreateObject('workflow.workflow_instance');
			$instance->loadInstance($iid);
			$inst_parser	=& CreateObject('workflow.bo_uiinstance', $this->t);
			//this is necessary the CreateObject did not use ref parameters
			$inst_parser->t =& $this->t;
			//we use the bo_uiinstance parser in edit mode with the false parameter
			$inst_parser->parse_instance($instance, false);
			$inst_parser->parse_instance_history($instance->workitems);

			//collect some messages from used objects
                        $this->message[] = $this->instance_manager->get_error(false, _DEBUG);

			// fill the general variables of the template
			$this->t->set_var(array(
				'instance'	=> $this->t->parse('output', 'instance_tpl'),
				'history'	=> $this->t->parse('output', 'history_tpl'),
				'message'		=> implode('<br />', $this->message),
				'iid'			=> $iid,
				'form_action'		=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_admininstance.form'),
							));

			$this->translate_template('admin_instance');
			$this->t->pparse('output', 'admin_instance');
			$GLOBALS['phpgw']->common->phpgw_footer();
		}

	}
?>
