<?php
	class bo_uiinstance extends workflow
	{
		//template given by ui classes
		var $t;
		//var $activity_t;
		//workflow objects
		var $process;
		//category object
		var $cat;

		function bo_uiinstance($template)
		{
			parent::workflow();

			$this->t	=& $template;
			$this->process  =& CreateObject('workflow.workflow_process');
			$this->cat	=& CreateObject('phpgwapi.categories');
		}

		//! parse common fields of an instance, by default in read-only mode, with the instance.tpl template
		function parse_instance(&$instance, $readonly=true)
		{
			if($this->t == '')
			{
				return false;
			}

			$this->t->set_file('instance_tpl', 'instance.tpl');
			$iid 		= $instance->getInstanceId();
			$pid 		= $instance->getProcessId();
			$this->process->getProcess($pid);

			$this->show_select_owner($instance->getOwner(), $readonly);
			$this->show_select_sendto($pid, $readonly);
			$this->show_instance_acts($iid, $instance->activities, $readonly);
			$this->show_properties($iid, $instance->properties, $readonly);

			// fill the general variables of the template
			$ended = $instance->getEnded();
			$status = $instance->getStatus();
			$category = $instance->getCategory();
			if (!($readonly))
			{
				$selectcategory =  $this->cat_option($instance->getCategory(),False);
			}
			else
			{
				$selectcategory = '<input {input_type} name="instance_category" value="'.$this->cat->id2name($instance->getCategory()).'" />';
			}
			$this->t->set_var(array(
				'iid'			=> $iid,
				'instance_process'	=> lang('Instance: %1 (Process: %2)', $iid, $this->process->getName() . ' ' . $this->process->getVersion()),
				'inst_started'		=> $GLOBALS['phpgw']->common->show_date($instance->getStarted()),
				'inst_ended' 		=> ($ended==0)? '-' : $GLOBALS['phpgw']->common->show_date($ended),
				'instance_name'		=> htmlspecialchars($instance->getName()),
				'status'		=> htmlspecialchars($instance->getStatus()),
				'instance_priority'	=> $instance->getPriority(),
				'instance_category'	=> $category,
				'instance_category_select'=> $selectcategory,
				'status_active'		=> ($status == 'active')? 'selected="selected"' : '',
				'status_exception'	=> ($status == 'exception')? 'selected="selected"' : '',
				'status_completed'	=> ($status == 'completed')? 'selected="selected"' : '',
				'status_aborted'	=> ($status == 'aborted')? 'selected="selected"' : '',
			));

			//showing or not the update button and some fields type
			$this->t->set_block('instance_tpl', 'block_button_update', 'button_update');
			if ($readonly)
			{
				$this->t->set_var(array(
					'button_update'	=> '',
					'input_type'	=> 'readonly="readonly"',
					'textarea_type'	=> 'readonly="readonly"',
					'select_type'	=> 'readonly="readonly" disabled="true"',
				));
			}
			else
			{
				$this->t->set_var(array(
					'input_type' 	=> 'type="text"',
					'textarea_type'	=> '',
					'select_type'	=> '',
				));
				$this->translate_template('block_button_update');
				$this->t->parse('button_update', 'block_button_update', true);
			}
			$this->translate_template('instance_tpl');
		}

		//! parse workitems/history fields of an instance on the history.tpl template
		function parse_instance_history(&$workitems)
		{
			if($this->t == '')
			{
				return false;
			}

			$this->t->set_file('history_tpl', 'history.tpl');
			$this->show_workitems($workitems);
			$this->translate_template('history_tpl');
		}

		function show_select_owner($actual_owner, $readonly)
		{
			if ($readonly)
			{//we just need to read actual owner name
				$GLOBALS['phpgw']->accounts->get_account_name($actual_owner,$lid,$owner_fname,$owner_lname);
				$myselect = '<input {input_type} name="owner" value="'.$owner_fname.' '.$owner_lname.'" />';
			}
			else
			{//we prepare a big select
				if (!is_object($GLOBALS['phpgw']->uiaccountsel))
				{
					$GLOBALS['phpgw']->uiaccountsel =& CreateObject('phpgwapi.uiaccountsel');
				}

				$myselect = $GLOBALS['phpgw']->uiaccountsel->selection('owner', 'owner',$actual_owner,'workflow',0,False,'','',lang('None'),False);
			}

			$this->t->set_var(array(
				'owner'		=> $actual_owner,
				'select_owner'	=> $myselect,
			));
		}

		function show_select_sendto($pid, $readonly)
		{

			$this->t->set_block('instance_tpl','block_select_sendto','select_sendto');
			$this->t->set_block('instance_tpl','block_sendallactivities','sendallactivities');
			if ($readonly)
			{//we show nothing
				$this->t->set_var('sendallactivities','');
			}
			else
			{
				//we need an activity manager to retrieve list of all avaible activities
				$activity_manager =& CreateObject('workflow.workflow_activitymanager');
				$proc_activities =& $activity_manager->list_activities($pid, 0, -1, 'wf_flow_num__asc', '', '');
				foreach ($proc_activities['data'] as $activity)
				{
					$this->t->set_var(array(
						'sendto_act_value'	=> $activity['wf_activity_id'],
						'sendto_act_name'	=> $activity['wf_name'],
					));
					$this->t->parse('select_sendto', 'block_select_sendto', true);
				}
				if (!($proc_activities['cant'])) $this->t->set_var('select_sendto', '');

				$this->translate_template('block_sendallactivities');
				$this->t->parse('sendallactivities', 'block_sendallactivities', true);
			}
		}

		function show_instance_acts($iid, &$instance_acts,$readonly)
		{
			$this->t->set_block('instance_tpl', 'block_instance_acts_table_users', 'instance_acts_table_users');

			if ($instance_acts)
			{
				$this->t->set_block('instance_tpl', 'block_instance_acts_table', 'instance_acts_table');
				foreach ($instance_acts as $activity)
				{
					$aid = $activity['wf_activity_id'];
					$send_button =  '';
					$restart_button = '';
					//handle user or user selection
					if ($readonly)
					{//we just need to read actual user name
						if ($activity['wf_user']=='*')
						{
							$fname = lang('Nobody');
							$lname = '';
						}
						else
						{
							$GLOBALS['phpgw']->accounts->get_account_name($activity['wf_user'],$lid,$fname,$lname);
						}
						$users = '<input {input_type} name="acts['.$aid.']" value="'.$fname.' '.$lname.'" />';
						//no action
					}
					else
					{//we prepare a big select
						if (!is_object($GLOBALS['phpgw']->uiaccountsel))
						{
							$GLOBALS['phpgw']->uiaccountsel =& CreateObject('phpgwapi.uiaccountsel');
						}

						$users = $GLOBALS['phpgw']->uiaccountsel->selection('acts['.$aid.']','acts['.$aid.']',$activity['wf_user'],'workflow',0,False,'','','*',False);
						//for actions there is 2 avaible actions
						//	* send : send after a completed activity, maybe the transitions failed the first time
						//	* restart : restart an automated activity which have meybe failed while running
						if (($activity['wf_status']=='completed') && ($activity['wf_is_autorouted']=='y') )
						{
							//this activity shouldn't be there in completed status, need to send manually the transition
							//and we do it there because no user will have the right to send manually an autorouted activity
							$send_button =  '<a href="'.$GLOBALS['phpgw']->link('/index.php',array(
																		'menuaction'	=> 'workflow.ui_userinstances.form',
																		'iid'		=> $activity['wf_instance_id'],
																		'filter_instance'=> $activity['wf_instance_id'],
																		'aid'		=> $activity['wf_activity_id'],
								'send'		=> true,
								'add_advanced_actions'=>true,
								)).'"><img src="'.$GLOBALS['phpgw']->common->image('workflow', 'linkto')
								.'" name="send_instance" alt="'.lang('send').'">'.lang('send transition').'</a>';
						}
						if (($activity['wf_status']=='running') && ($activity['wf_is_interactive']=='n') )
						{
							//this activity should terminate, this is not the case
							//so we will restart it
							$restart_button = '<a href="'.$GLOBALS['phpgw']->link('/index.php',array(
																		'menuaction'	=> 'workflow.ui_userinstances.form',
																		'iid'		=> $activity['wf_instance_id'],
																		'filter_instance'=> $activity['wf_instance_id'],
																		'aid'		=> $activity['wf_activity_id'],
								'restart'	=> true,
								'add_advanced_actions'=>true,
								)).'"><img src="'.$GLOBALS['phpgw']->common->image('workflow', 'runform')
								.'" name="restart activity" alt="'.lang('restart').'">'.lang('restart activity').'</a>';;
						}
					}
					$this->t->set_var(array(
						'select_user'			=> $users,
						'activity_user'			=> $activity['wf_user'],
						'inst_act_name'			=> $activity['wf_name'],
						'inst_act_status'		=> $activity['wf_status'],
						'inst_act_id'			=> $aid,
						'send'				=> $send_button,
						'restart'			=> $restart_button,
					));


					$this->t->parse('instance_acts_table', 'block_instance_acts_table', true);
				}

				$this->t->set_block('instance_tpl', 'block_instance_acts', 'instance_acts');
				$this->translate_template('block_instance_acts');
				$this->t->parse('instance_acts', 'block_instance_acts');
			}
			else
			{
				$this->t->set_block('instance_tpl', 'block_instance_acts', 'instance_acts');
				$this->t->set_var('instance_acts', '');
			}
		}

		//! display the property list, if readonly is false then it is possible to delete/add/modify the properties
		function show_properties($iid, &$props, $readonly)
		{

			$this->t->set_block('instance_tpl', 'block_properties', 'properties');
			$this->t->set_block('block_properties', 'block_button_delete', 'button_delete');
			if (!(empty($props)))
			{
				$parsed =& $props;
			}
			else
			{
				$parsed = Array('--' => '--');
			}
			foreach ($parsed as $key=>$prop)
			{
				$prop = htmlspecialchars($prop);
				//make textarea for big properties
				if (strlen($prop) > 80)
				{
					$this->t->set_var('prop_value', '<textarea {textarea_type} name="props['. $key .']" cols="80" rows="5">'. $prop .'</textarea>');
				}
				//and bigger input for long properties
				elseif (strlen($prop) > 25)
				{
					$this->t->set_var('prop_value', '<input {input_type} size="80" name="props['. $key .']" value="'. $prop .'" />');
				}
				else
				{
					$this->t->set_var('prop_value', '<input {input_type} size="25" name="props['. $key .']" value="'. $prop .'" />');
				}

				if ($readonly)
				{
					$this->t->set_var(array(
						'button_delete'		=> '',
						'prop_key'		=> $key,
						'color_line'		=> $this->nextmatchs->alternate_row_color($tr_color, true),
					));
				}
				else
				{
					$this->t->set_var(array(
						'prop_href'		=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_admininstance.form&iid='. $iid .'&unsetprop='. $key),
						'img_trash'		=> $GLOBALS['phpgw']->common->image('workflow', 'trash'),
						'prop_key'		=> $key,
						'color_line'		=> $this->nextmatchs->alternate_row_color($tr_color, true),
					));

					$this->t->parse('button_delete', 'block_button_delete', false);
				}
				$this->translate_template('block_properties');
				$this->t->parse('properties', 'block_properties', true);
			}
			//if no property actually
			if (!count($props)) $this->t->set_var('properties', '<tr><td colspan="2" align="center">'. lang('There are no properties available') .'</td></tr>');

			//showing or not the update button and the 'add property' zone
			$this->t->set_block('instance_tpl', 'block_button_update_properties', 'button_update_properties');
			$this->t->set_block('instance_tpl', 'block_add_property', 'add_property');
			if ($readonly)
			{
				$this->t->set_var(array(
					'button_update_properties'	=> '',
					'add_property'			=> '',
				));
			}
			else
			{
				$this->translate_template('block_button_update_properties');
				$this->translate_template('block_add_property');
				$this->t->parse('button_update_properties', 'block_button_update_properties', true);
				$this->t->parse('add_property', 'block_add_property', true);
			}
		}

		//! display the workitems list (history) on history_tpl
		function show_workitems(&$works)
		{
			$this->t->set_block('history_tpl', 'block_history_line', 'history_line');
			$view = $GLOBALS['phpgw']->common->image('workflow', 'view');

			// access granted to the view workitem function?
			// need access to the monitor screens. Workitems contains the whole properties for example
			$access_granted = true;
			if(!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
			{
				if(!$GLOBALS['phpgw']->acl->check('monitor_workflow',1,'workflow'))
				{
					$access_granted = false;
				}
			}
			//fill rows
			foreach ($works as $work)
			{
				if ($work['wf_user']=='*')
				{
					$fname = lang('Nobody');
					$lname = '';
				}
				else
				{
					$GLOBALS['phpgw']->accounts->get_account_name($work['wf_user'],$lid,$fname,$lname);
				}

				if ($access_granted)
				{
					$address = $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_viewworkitem.form&itemId='.$work['wf_item_id']);
					$history_link = '<a href="'.$address.'"><img src="'.$view.'" alt="'.lang('Details').'" /></a>';
				}
				else
				{
					$history_link = '&nbsp;';
				}
				$this->t->set_var(array(
					'act_icon'		=> $this->act_icon($work['wf_type'],$work['wf_is_interactive']),
					'history_activity'	=> $work['wf_name'],
					'history_started'	=> $GLOBALS['phpgw']->common->show_date($work['wf_started']),
					'history_duration'	=> $this->time_diff($work['wf_ended']-$work['wf_started']),
					'history_user'		=> $fname.' '.$lname,
					'history_link'		=> $history_link,
					'color_line'		=> $this->nextmatchs->alternate_row_color($tr_color, true),
				));

				$this->translate_template('block_history_line');
				$this->t->parse('history_line', 'block_history_line', true);
			}
			//if no workitems actually
			if (!count($works)) $this->t->set_var('history_line', '<tr><td colspan="5" align="center">'. lang('There are no workitems available') .'</td></tr>');
		}

		/* Return a select form element with the categories option dialog in it */
		function cat_option($cat_id='',$notall=False,$java=True,$multiple=False)
		{
			if($java)
			{
						$jselect = ' ';
					}
					/* Setup all and none first */
					$cats_link  = "\n" .'<select name="instance_category'.(($multiple)? '[]':'').'"' .$jselect . (($multiple)? 'multiple ' : '') . ">\n";
					if(!$notall)
					{
						$cats_link .= '<option value=""';
						if($cat_id == 'all')
						{
							$cats_link .= ' selected';
				}
				$cats_link .= '>'.lang("all").'</option>'."\n";
			}

			/* Get app-specific category listings */
			$cats_link .= $this->cat->formated_list('select','all',$cat_id,False);
			$cats_link .= '</select>'."\n";
			return $cats_link;
		}

	}
?>
