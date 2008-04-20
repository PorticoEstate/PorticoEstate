<?php

	require_once(dirname(__FILE__) . '/' . 'class.bo_user_forms.inc.php');

	class ui_userinstances extends bo_user_forms
	{
		var $public_functions = array(
			'form'	=> true,
		);

		// communication with the workflow engine
		var $GUI;
		//phpgw's categories object
		var $cat;
		//filters
		var $filter_process;
		var $filter_activity;
		var $filter_activity_name;
		var $filter_user;
		var $advanced_search;
		var $show_advanced_actions;
		var $add_exception_instances;
		var $add_completed_instances;
		var $add_aborted_instances;
		var $remove_active_instances;
		var $filter_act_status;
		var $filter_instance;
		//used by nextmatchs
		var $total_records;
		//all preferences
		var $myPrefs;
		//columns preferences
		var $show_id_column;
		var $show_instStatus_column;
		var $show_instName_column;
		var $show_cat_column;
		var $show_priority_column;
		var $show_procname_column;
		var $show_actStatus_column;
		var $show_owner_column;
		var $show_started_column;
		var $nb_columns;

		function ui_userinstances()
		{
			parent::bo_user_forms('user_instances');
			$this->GUI =& CreateObject('workflow.workflow_gui');
			$this->cat =& CreateObject('phpgwapi.categories');
		}

		function form()
		{
			//enable preferences
			$GLOBALS['phpgw']->preferences->read_repository();
			$this->myPrefs =& $GLOBALS['phpgw_info']['user']['preferences']['workflow'];

			//Retrieve form args
			// FILTER INSTANCE
			//this filter make all other filters unavaible
			$this->filter_instance	= get_var('filter_instance','any','');
			if ((int)$this->filter_instance > 0)
			{
				$this->filter_process 		= '';
				$this->filter_activity 		= 0;
				$this->filter_activity_name 	= '';
				$this->filter_user		= '';
				$this->filter_category		= 'all';
				$this->search_str		= '';
				$this->advanced_search  	= true;
				$this->show_advanced_actions	= get_var('show_advanced_actions', 'any', false);
				if (!$this->show_advanced_actions)
				{
					// check the Preferences of the workflow where the user can ask for theses actions
					$this->show_advanced_actions= $this->myPrefs['wf_instances_show_advanced_actions'];
				}
				//we want this instance no matter in what state
				$this->add_exception_instances 	= true;
				$this->add_completed_instances 	= true;
				$this->add_aborted_instances	= true;
				$this->remove_active_instances 	= false;
				$this->filter_act_status	= false;
			}
			else //we have no filter_instance, we check the real form args
			{
				$this->filter_process		= get_var('filter_process', 'any', '');
					// we can filter activity by id (the list of activity call us with it) or by name
				$this->filter_activity		= get_var('filter_activity', 'any', 0);
				$this->filter_activity_name	= get_var('filter_activity_name', 'any', '');
				$this->filter_user		= get_var('filter_user', 'any', '');
				$this->filter_category		= get_var('filter_category', 'any', 'all');
				$this->advanced_search  	= get_var('advanced_search', 'any', false);
				if (!$this->advanced_search)
				{
					// check the Preferences of the workflow where the user can ask for the advanced mode
					$this->advanced_search = $this->myPrefs['wf_instances_show_advanced_mode'];
				}
				if ($this->advanced_search)
				{
					$this->add_exception_instances	= get_var('add_exception_instances', 'any', false);
					$this->add_completed_instances	= get_var('add_completed_instances', 'any', false);
					$this->add_aborted_instances	= get_var('add_aborted_instances', 'any', false);
					$this->remove_active_instances	= get_var('remove_active_instances', 'any', false);
					$this->filter_act_status	= get_var('filter_act_status', 'any', '');
					$this->show_advanced_actions	= get_var('show_advanced_actions', 'any', false);
					if (!$this->show_advanced_actions)
					{
						// check the Preferences of the workflow where the user can ask for theses actions
						$this->show_advanced_actions= $this->myPrefs['wf_instances_show_advanced_actions'];
					}
				}
				else
				{
					$this->add_exception_instances 	= false;
					$this->add_completed_instances 	= false;
					$this->add_aborted_instances 	= false;
					$this->remove_active_instances 	= false;
					$this->filter_act_status	= false;
					$this->show_advanced_actions	= false;
				}
			}

			//2nd form vars
			$activity_id		= get_var('aid', 'any', 0);
			$instance_id		= get_var('iid', 'any', 0);
			// get user actions on the form
			$askGrab=get_var('grab','any',0);
			$askRelease=get_var('release','any',0);
			$askAbort=get_var('abort','any',0);
			$askSend=get_var('send','any',0);
			$askRestart=get_var('restart','any',0);
			$askException=get_var('exception','any',0);
			$askResume=get_var('resume','any',0);

			// check preferences where the user can disable some columns
			// we need to know which one for order in queries
			$this->read_column_preferences();

			//overwrite default sort order behaviour
			// get sort mode data, done after preferences to handle priority yes/no
			$this->order		= get_var('order', 'any', ($this->show_priority_column)? 'wf_priority' : 'wf_started');
			$this->sort		= get_var('sort', 'any', ($this->show_priority_column)? 'desc' : 'asc');
			$this->sort_mode	= $this->order . '__' . $this->sort;

						// we have 2 different filters on activities, keeping only one
						// we keep only activity_name as a valid filter, when asking for a particular id we assume that de process id
			// is set as well and we only keep the activity name
			// if someone sends us an activity id without the process id we could show him activities with the same name
			// on other processes (so with other id).
			if ($this->filter_activity != 0)
			{
				$tmpactivity =& CreateObject('workflow.workflow_baseactivity');
				$tmpact = $tmpactivity->getActivity($this->filter_activity);
				$this->filter_activity_name = $tmpact->getName();
				unset($tmpact);unset($tmpactivity);
			}
			$this->link_data = array
			(
				'menuaction'			=> 'workflow.ui_userinstances.form',
				'filter_process' 		=> $this->filter_process,
				'filter_activity_name' 		=> $this->filter_activity_name,
				'filter_user' 			=> $this->filter_user,
				'filter_category'           	=> $this->filter_category,
				'advanced_search' 		=> $this->advanced_search,
				'add_exception_instances' 	=> $this->add_exception_instances,
				'add_completed_instances' 	=> $this->add_completed_instances,
				'add_aborted_instances' 	=> $this->add_aborted_instances,
				'remove_active_instances' 	=> $this->remove_active_instances,
				'filter_act_status'		=> $this->filter_act_status,
				'show_advanced_actions'		=> $this->show_advanced_actions,
				'search_str' 			=> $this->search_str,
				'activity_id' 			=> $activity_id,
				'instance_id' 			=> $instance_id,
			);

												// handling actions asked by the user on the form---------------------

												//getting user name in $user_fname and $user_lname
			$GLOBALS['phpgw']->accounts->get_account_name($GLOBALS['phpgw_info']['user']['account_id'],$lid,$user_fname,$user_lname);

			//$this->message contains an array of ui error messages
			if ($askException)
			{
							//TODO: add  a $system_comments = lang('exception raised by %1 %2: %3',$user_fname, $user_lname,$exception_comment);
							// to the instance activity history
				if (!$this->GUI->gui_exception_instance($activity_id, $instance_id))
				{
					$this->message[]=$this->GUI->get_error(false, _DEBUG);
					$this->message[]=lang("You are not allowed to exception instance %1",$instance_id);
				}
			}

			// resume an exception instance
			if ($askResume)
			{
				//TODO: add a $system_comments = lang('exception resumed by %1 %2: %3',$user_fname, $user_lname,$exception_comment);
				// to the instance activity history
				if (!$this->GUI->gui_resume_instance($activity_id, $instance_id))
				{
					$this->message[]=$this->GUI->get_error(false, _DEBUG);
					$this->message[]=lang("You are not allowed to resume instance %1",$instance_id);
				}
			}

			// abort instance
			if ($askAbort)
			{
				if (!$this->GUI->gui_abort_instance($activity_id, $instance_id))
				{
					$this->message[]=$this->GUI->get_error(false, _DEBUG);
					$this->message[]=lang("You are not allowed to abort instance %1",$instance_id);
				}
			}

			// release instance
			if ($askRelease)
			{
				if (!$this->GUI->gui_release_instance($activity_id, $instance_id))
				{
					$this->message[]=$this->GUI->get_error(false, _DEBUG);
					$this->message[]=lang("You are not allowed to release instance %1",$instance_id);
				}
			}

			// grab instance
			if ($askGrab)
			{
				if (!$this->GUI->gui_grab_instance($activity_id, $instance_id)) {
					$this->message[]=$this->GUI->get_error(false, _DEBUG);
					$this->message[]=lang("You are not allowed to grab instance %1",$instance_id);
				}
			}

			// send instance (needed when an activity is not autorouted)
			if ($askSend)
			{
				if (!$this->GUI->gui_send_instance($activity_id, $instance_id))
				{
					$this->message[]=$this->GUI->get_error(false, _DEBUG);
					$this->message[]=lang("You are not allowed to send instance %1",$instance_id);
				}
			}
			// restart non interactive activity (this is an admin function)
			if ($askRestart)
			{
				if (!$this->GUI->gui_restart_instance($activity_id, $instance_id))
				{
					$this->message[]=$this->GUI->get_error(false, _DEBUG);
					$this->message[]=lang("You are not allowed to restart instance %1",$instance_id);
				}
			}


				// handling widgets on the form -------------------------------------------
				$this->where = '';
				// retrieve all user processes info - used by the 'select processes'
				$all_processes =& $this->GUI->gui_list_user_processes($GLOBALS['phpgw_info']['user']['account_id'],0, -1, 'wf_procname__asc', '', $this->where);

				//(regis) adding a request for data in a select activity block
				// we want only activities avaible for the selected process (filter on process to limit number of results)
				// but when we are in advanced search mode we are not recomputing the search at every change on the processes select
				// or on the activity select, so we can't recompute the select activity list every time the process changes
				// in fact in this case we need __All__ avaible activities, but only in this case.
				$this->where = '';
				$wheres = array();
				if (!($this->advanced_search))
				{
					if(!($this->filter_process==''))
					{
						$wheres[] = "gp.wf_p_id=" .(int)$this->filter_process. "";
					}
				}
				if( count($wheres) > 0 )
				{
			 	$this->where = implode(' and ', $wheres);
				}


				// retrieve all user activities info (with the selected process) for the select
				$all_activities =& $this->GUI->gui_list_user_activities_by_unique_name($GLOBALS['phpgw_info']['user']['account_id'], 0, -1, 'ga.wf_name__asc', '', $this->where);

				//filling our query special string with all our filters
				$this->fill_where_data();

				// retrieve user instances
				$instances =& $this->GUI->gui_list_user_instances($GLOBALS['phpgw_info']['user']['account_id'], $this->start, $this->offset, $this->sort_mode, $this->search_str,$this->where,false,(int)$this->filter_process,(!($this->remove_active_instances=='on')),($this->add_completed_instances=='on'), ($this->add_exception_instances=='on'), ($this->add_aborted_instances=='on'));
				$this->total_records = $instances['cant'];
				//echo "instances: <pre>";print_r($instances);echo "</pre>";


				//fill selection zones and vars------------------------------------------
				// 3 selects
				$this->show_select_user($this->filter_user);
				$this->show_select_process($all_processes['data'], $this->filter_process);
				$this->show_select_activity($all_activities['data'], $this->filter_activity_name);
				// the filter on instance_id, depends on preferences
				$this->show_filter_instance($this->myPrefs['wf_instances_show_instance_search']);
				// to keep informed of the 5 select values the second form (actions in the list)
				// need additional vars
				// and the same for all checkboxes
				$this->t->set_var(array(
					'filter_instance_id'		=> $this->filter_instance,
					'filter_user_id_set'		=> $this->filter_user,
					'filter_process_id_set'		=> $this->filter_process,
					'filter_activity_name_set'	=> $this->filter_activity_name,
					'filter_act_status_set'		=> $this->filter_act_status,
					'filter_category_set'		=> $this->filter_category,
					'advanced_search_set'		=> $this->advanced_search,
					'add_exception_instances_set'	=> $this->add_exception_instances,
					'add_completed_instances_set'	=> $this->add_completed_instances,
					'add_aborted_instances_set'	=> $this->add_aborted_instances,
					'remove_active_instances_set'	=> $this->remove_active_instances,
			'show_advanced_actions_set'	=> $this->show_advanced_actions,
				));
				//category filter
				if ($this->show_cat_column)
				{
					$this->t->set_var(array(
						'filter_category_select' 	=> '</td><td>'.$this->cat_option($this->filter_category,False),
						'filter_category_label' 	=> '</td><td>'.lang('Category'),
						'category_css'			=>  '<LINK href="'.$this->get_css_link('category').'" type="text/css" rel="StyleSheet">' ,
			));
				}
				else
				{
					$this->t->set_var(array(
						'filter_category_select' 	=> '',
						'filter_category_label' 	=> '',
						'category_css'			=> '',
			));
				}
				// a LINK css for showing priority levels
				$this->t->set_var('priority_css', ($this->show_priority_column)? '<LINK href="'.$this->get_css_link('priority').'" type="text/css" rel="StyleSheet">' : '');
				// back to the first form, the advanced zone
				if ($this->advanced_search)
				{
					$this->t->set_var(array(
						'advanced_search'	=> 'checked="checked"',
						'filters_on_change'     => '',
			));
						$this->t->set_file('Advanced_table_tpl','user_instances_advanced.tpl');
				 	$this->t->set_var(array(
				 		'add_exception_instances'	=> ($this->add_exception_instances)? 'checked="checked"' : '',
				 		'add_completed_instances'	=> ($this->add_completed_instances)? 'checked="checked"' : '',
				 		'add_aborted_instances'		=> ($this->add_aborted_instances)? 'checked="checked"' : '',
				 		'remove_active_instances'	=> ($this->remove_active_instances)? 'checked="checked"' : '',
				 		'show_advanced_actions'		=> ($this->show_advanced_actions)? 'checked="checked"' : '',
			));
				 	$this->show_select_act_status($this->filter_act_status);
				 	$this->translate_template('Advanced_table_tpl');
				 	$this->t->parse('Advanced_table', 'Advanced_table_tpl');
				}
				else
				{
					$this->t->set_var(array(
						'advanced_search' 	=> '',
						'filters_on_change'	=> 'onChange="this.form.submit();"',
						'Advanced_table'	=> '',
			));
				}
				//some lang text in javascript
				$this->t->set_var('lang_Confirm_delete',lang('Confirm Delete'));
				$this->t->set_var('start',0);// comming back again to start point

				// Fill the final list of the instances we choosed in the template
				$this->show_list_instances($instances['data'], $this->show_advanced_actions);

				$this->show_user_tabs($this->class_name);
				//check last GUI errors messages if any
				$this->message[]=$this->GUI->get_error(false, _DEBUG);
				$this->fill_form_variables();
				$this->finish();
		}

		//! handle the table containing all instances
		function show_list_instances(&$instances_data, $show_advanced_actions = false)
		{
			//------------------------------------------- nextmatch --------------------------------------------
			//block for the header of the table
			$this->t->set_block('user_instances', 'block_list_headers', 'list_headers');
			//block for colums in the header
			$this->t->set_block('user_instances', 'block_header_column', 'header_column');
			//warning header names are header_[name or alias of the column in the query without a dot]
			//this is necessary for sorting
			$header_array =& $this->get_instance_header();

			$this->fill_nextmatchs($header_array,$this->total_records);

			//block for each row
			$this->t->set_block('user_instances', 'block_list_instances', 'list_instances');
			//block for colums in row
			$this->t->set_block('user_instances', 'block_instance_column', 'instance_column');

			foreach ($instances_data as $instance)
			{
			// all theses actions (most of them --monitor, view and run are GET links--) are handled by a javascript function
			// 'submitAnInstanceLine' on the template which permit to send the activity and instance Ids
			// (as we could do with a link) AND kepping all the others data filled in the form (using submit())

				// ask the engine what actions are avaible for each line
				$actions = $this->GUI->getUserActions(
						$GLOBALS['phpgw_info']['user']['account_id'],
						$instance['wf_instance_id'],
				 		$instance['wf_activity_id'],
				 		$instance['wf_readonly'],
						$instance['wf_p_id'],
						$instance['wf_type'],
						$instance['wf_is_interactive'],
						$instance['wf_is_autorouted'],
						$instance['wf_act_status'],
						$instance['wf_owner'],
						$instance['wf_status'],
						$instance['wf_user']);

				// Run instance
				// run the instance, the grab stuff is done in the run function
				if (isset($actions['run']))
				{
					$this->t->set_var('run',
						'<a href="'. $GLOBALS['phpgw']->link('/index.php',
						'menuaction=workflow.run_activity.go&iid='.$instance['wf_instance_id']
						.'&activity_id='.$instance['wf_activity_id']).'"><img src="'
						.$GLOBALS['phpgw']->common->image('workflow', 'runform').'" alt="'.$actions['run']
						.'" title="'.$actions['run'].'"></a>');
				}
				else
				{
					$this->t->set_var('run', '');
				}
				// View instance
				// launch the view activity associated with this process if any
				//and the ui_userviewinstance if not
				if (isset($actions['viewrun']))
				{
					$this->t->set_var('view',
						'<a href="'.$GLOBALS['phpgw']->link('/index.php',array(
							'menuaction'	=> 'workflow.run_activity.go',
							'iid'		=> $instance['wf_instance_id'],
							'activity_id'	=> $actions['viewrun']['link'],
							)).'"><img src="'.$GLOBALS['phpgw']->common->image('phpgwapi', 'view').'" alt="'.$actions['viewrun']['lang'].'" title="'.$actions['viewrun']['lang'].'"></a>'
					);
				}
				elseif (isset($actions['view']))
				{
					$this->t->set_var('view',
						'<a href="'.$GLOBALS['phpgw']->link('/index.php',array(
						'menuaction'	=> 'workflow.ui_userviewinstance.form',
						'iid'		=> $instance['wf_instance_id'],
						)).'"><img src="'.$GLOBALS['phpgw']->common->image('phpgwapi', 'view').'" alt="'.$actions['view'].'" title="'.$actions['view'].'"></a>'
					);
				}
				else
				{
					$this->t->set_var('view', '');
				}


			// Send instance (no automatic routage)
				if (isset($actions['send']))
				{
					$this->t->set_var('send',
						'<input type="image" src="'. $GLOBALS['phpgw']->common->image('workflow', 'linkto')
						.'" name="send_instance" alt="'.$actions['send'].'" title="'.$actions['send']
						.'" width="16" onClick="submitAnInstanceLine('. $instance['wf_instance_id'] .','
						.((empty($instance['wf_activity_id']))? '0':$instance['wf_activity_id']).',\'send\')">');
				}
				else
				{
					$this->t->set_var('send', '');
				}

				if ($this->show_advanced_actions) {
				// Resume exception instance
					if (isset($actions['resume']))
					{
						$this->t->set_var('resume',
							'<input type="image" src="'. $GLOBALS['phpgw']->common->image('workflow', 'resume')
							.'" name="resume_instance" alt="'.$actions['resume'].'" title="'.$actions['resume']
							.'" width="16" onClick="submitAnInstanceLine('. $instance['wf_instance_id'] .','
							.((empty($instance['wf_activity_id']))? '0':$instance['wf_activity_id']).',\'resume\')">');
					} else {
						$this->t->set_var('resume', '');
					}

				// Exception instance
					if (isset($actions['exception']))
					{
						$this->t->set_var('exception',
							'<input type="image" src="'. $GLOBALS['phpgw']->common->image('workflow', 'tostop')
							.'" name="exception_instance" alt="'.$actions['exception'].'" title="'.$actions['exception']
							.'" width="16" onClick="submitAnInstanceLine('. $instance['wf_instance_id'] .','
							.((empty($instance['wf_activity_id']))? '0':$instance['wf_activity_id']).',\'exception\')">');
					}
					else
					{
						$this->t->set_var('exception', '');
					}


				// Abort instance
					// aborting an instance is avaible for the owner or the user of an instance
					if (isset($actions['abort']))
					{
						$this->t->set_var('abort',
							'<input type="image" src="'. $GLOBALS['phpgw']->common->image('workflow', 'totrash')
							.'" name="abort_instance" alt="'.$actions['abort'].'" title="'.$actions['abort']
							.'" width="16" onClick="submitAnInstanceLine('. $instance['wf_instance_id'] .','
							.((empty($instance['wf_activity_id']))? '0':$instance['wf_activity_id']).',\'abort\')">');
					}
					else
					{
						$this->t->set_var('abort', '');
					}

				// Grabb or Release instance
					if (isset($actions['grab']))
					{//the instance is not yet grabbed by anyone and we have rights to grabb it (if we don't we wont be able to do it)
						//(regis) seems better for me to show a float status when you want to grab, cause this is the actual state
						// and the user understand better this way the metaphore
						$this->t->set_var('grab_or_release',
							'<input type="image" src="'. $GLOBALS['phpgw']->common->image('workflow', 'float')
							.'" name="grab_instance" alt="'.$actions['grab'].'" title="'.$actions['grab']
							.'" width="16" onClick="submitAnInstanceLine('. $instance['wf_instance_id'] .','
							.((empty($instance['wf_activity_id']))? '0':$instance['wf_activity_id']).',\'grab\')">');
					}
					elseif (isset($actions['release']))
					{
						//(regis) seems better for me to show a fix status when you want to release, cause this is the actual state
						$this->t->set_var('grab_or_release',
							'<input type="image" src="'. $GLOBALS['phpgw']->common->image('workflow', 'fix')
							.'" name="release_instance" alt="'.$actions['release'].'" title="'.$actions['release']
							.'" width="16" onClick="submitAnInstanceLine('. $instance['wf_instance_id'] .','
							.((empty($instance['wf_activity_id']))? '0':$instance['wf_activity_id']).',\'release\')">');
					}
					else
					{
						$this->t->set_var('grab_or_release', '');
					}

					// Monitor instances
					if (isset($actions['monitor']))
					{
						$this->t->set_var('monitor',
							'<a href="'. $GLOBALS['phpgw']->link('/index.php',
							'menuaction=workflow.ui_admininstance.form&iid='. $instance['wf_instance_id'])
							.'"><img src="'. $GLOBALS['phpgw']->common->image('workflow', 'monitorinstance')
							.'" alt="'. lang('monitor instance') .'" title="'
							. lang('monitor instance') .'" /></a>');
					}
					else
					{
						$this->t->set_var('monitor','');
					}
				} else //not in advanced_actions mode
				{
					$this->t->set_var('grab_or_release', '');
					$this->t->set_var('exception', '');
					$this->t->set_var('resume', '');
					$this->t->set_var('abort', '');
					$this->t->set_var('monitor', '');
				}

				$this->show_instance_row($instance);


				// finally parse this row
				$this->t->parse('list_instances', 'block_list_instances', true);
			}
			//hide our working header and row columns
			$this->t->set_var(array(
				'instance_column'	=> '',
				'header_column'		=> '',
			));

			if ($this->total_records==0) $this->t->set_var('list_instances', '<tr><td colspan="'.$this->nb_columns.'" align="center">'. lang('There are no instances available') .'</td></tr>');
		}

		//! read user preferences and set all $this->show_xxxx_column vars, the nb_columns var and force some columns if needed
		function read_column_preferences()
		{
			//_debug_array($this->myPrefs);
			$this->show_id_column = $this->myPrefs['wf_instances_show_instance_id_column'];
			$this->show_instStatus_column = $this->myPrefs['wf_instances_show_instance_status_column'];
			$this->show_instName_column = $this->myPrefs['wf_instances_show_instance_name_column'];
			$this->show_priority_column = $this->myPrefs['wf_instances_show_priority_column'];
			$this->show_procname_column = $this->myPrefs['wf_instances_show_process_name_column'];
			$this->show_actStatus_column = $this->myPrefs['wf_instances_show_activity_status_column'];
			$this->show_owner_column = $this->myPrefs['wf_instances_show_owner_column'];
			$this->show_cat_column = $this->myPrefs['wf_instances_show_category_column'];
			$this->show_started_column = $this->myPrefs['wf_instances_show_started_column'];

			// now we must check actual filters and force certain columns, for example if we show aborted instances
			// we must show instance status
			if (($this->advanced_search) && ($this->add_exception_instances || $this->add_completed_instances
				|| $this->add_aborted_instances || $this->remove_active_instances))
			{
				$this->show_instStatus_column = true;
			}
			// with filter activity status better to see activity status
			if ($this->filter_act_status)
			{
				$this->show_actStatus_column = true;
			}
			// with filter instance better to see instance id
			if ($this->filter_instance)
			{
				$this->show_id_column = true;
			}
			// total number of columns is user+activity+actions+others
			$this->nb_columns = 3 + $this->show_owner_column + $this->show_actStatus_column
				+ $this->show_procname_column + $this->show_priority_column + $this->show_instName_column + $this->show_instStatus_column
				+ $this->show_id_column + $this->show_cat_column + $this->show_started_column;
			// if recent change was made (column added) test it to prevent the user
			if (!(isset($this->myPrefs['wf_instances_show_category_column'])))
			{
				$preferences = lang('preferences');
				$preferenceslink = '<a href="'.$GLOBALS['phpgw']->link('/preferences/preferences.php','appname=workflow').'" />'.$preferences.'</a>';
				$this->message[] = lang('there are some undefined preferences associated with this form : %1', $preferenceslink);
			}
		}

		//! set the headers columns in the template and return an array containing columns avaible for nextmatchs sorting functions.
		function get_instance_header()
		{
			$result = Array();

			//Id
			if($this->show_id_column)
			{
				$result['wf_instance_id'] = lang('id');
				$this->t->set_var(array(
					'column_header'	=> 'wf_instance_id',
				));
				$this->t->parse('columns_header','block_header_column',true);
			}

			// Status Instance
			if($this->show_instStatus_column)
			{
				$result['wf_status'] = lang('Inst. Status');
				$this->t->set_var(array(
					'column_header'	=> 'wf_status',
				));
				$this->t->parse('columns_header','block_header_column',true);
			}


			//Started date
			if($this->show_started_column)
			{
				$result['wf_started'] =  lang('Started');
				$this->t->set_var(array(
					'column_header'	=> 'wf_started',
				));
				$this->t->parse('columns_header','block_header_column',true);
			}

			//Priority
			if($this->show_priority_column)
			{
				$result['wf_priority'] =  lang('Pr.');
				$this->t->set_var(array(
					'column_header'	=> 'wf_priority',
				));
				$this->t->parse('columns_header','block_header_column',true);
			}

			// Instance Name
			if($this->show_instName_column)
			{
				$result['insname'] = lang('Name');
				$this->t->set_var(array(
					'column_header'	=> 'insname',
				));
				$this->t->parse('columns_header','block_header_column',true);
			}


			// Process Name
			if($this->show_procname_column)
			{
				$result['wf_procname'] = lang('Process');
				$this->t->set_var(array(
					'column_header'	=> 'wf_procname',
				));
				$this->t->parse('columns_header','block_header_column',true);
			}

			// Activity. Always show this information.
			$result['wf_name'] = lang('Activity');
			$this->t->set_var(array(
					'column_header'	=> 'wf_name',
			));
			$this->t->parse('columns_header','block_header_column',true);

			// Category
			if($this->show_cat_column)
			{
				$result['wf_category'] = lang('Category');
				$this->t->set_var(array(
					'column_header'	=> 'wf_category',
				));
				$this->t->parse('columns_header','block_header_column',true);
			}

			// Activity Status
			if($this->show_actStatus_column)
			{
				$result['wf_act_status'] = lang('Act. Status');
				$this->t->set_var(array(
					'column_header'	=> 'wf_act_status',
				));
				$this->t->parse('columns_header','block_header_column',true);
			}

			// Owner
			if($this->show_owner_column)
			{
				$result['wf_owner'] = lang('Owner');
				$this->t->set_var(array(
					'column_header'	=> 'wf_owner',
				));
				$this->t->parse('columns_header','block_header_column',true);
			}

			// User. Always show this information.
			$result['wf_user'] = lang('User');
			$this->t->set_var(array(
				'column_header'	=> 'wf_user',
			));
			$this->t->parse('columns_header','block_header_column',true);

			// parse the header row
			$this->translate_template('block_list_headers');
			$this->t->parse('list_headers', 'block_list_headers', true);

			//retrun the array containing headers avaible for sorting/nextmatchs
			return $result;
		}

		//! complete an instance row depending on preferences variables. Activity name and user are always shown
		function show_instance_row(&$instance)
		{
			// Re-Init variables in template
			$this->t->set_var(array(
				'column_value'	=> '',
				'class_column'	=> '',
				'columns'	=> '',
			));

			//Id
			if($this->show_id_column)
			{
				$this->t->set_var(array(
					'column_value'	=> $instance['wf_instance_id'],
					'class_column'	=> 'class="col_Id"',
				));
				$this->t->parse('columns','block_instance_column',true);
			}

			// Status Instance
			if($this->show_instStatus_column)
			{
				// managing 4 types of instance status in a graphical manner
				if ($instance['wf_status'] == 'active')
				{
					$graphical_status = '<img src="'.$GLOBALS['phpgw']->common->image('workflow', 'ok') .'" alt="'. lang('active') .'" title="'. lang('active') .'" />';
				}
				elseif ($instance['wf_status'] == 'exception')
				{
					$graphical_status = '<img src="'.$GLOBALS['phpgw']->common->image('workflow', 'stop') .'" alt="'. lang('exception') .'" title="'. lang('exception') .'" />';
				}
				elseif ($instance['wf_status'] == 'aborted')
				{
					$graphical_status = '<img src="'.$GLOBALS['phpgw']->common->image('workflow', 'trash') .'" alt="'. lang('aborted') .'" title="'. lang('aborted') .'" />';
				}
				elseif ($instance['wf_status'] == 'completed')
				{
					$graphical_status = '<img src="'.$GLOBALS['phpgw']->common->image('workflow', 'completed') .'" alt="'. lang('completed') .'" title="'. lang('completed') .'" />';
				}
				$this->t->set_var(array(
					'column_value'	=> $graphical_status,
					'class_column'	=> 'class="inst_status_'.$instance['wf_status'].'"',
				));
				$this->t->parse('columns','block_instance_column',true);
			}

			//Started date
			if($this->show_started_column)
			{
				$this->t->set_var(array(
					'column_value'	=> $GLOBALS['phpgw']->common->show_date($instance['wf_started']),
					'class_column'	=> 'class="col_date"',
				));
				$this->t->parse('columns','block_instance_column',true);
			}

			//Priority
			if($this->show_priority_column)
			{
				$this->t->set_var(array(
					'column_value'	=> $instance['wf_priority'],
					'class_column'	=> 'class="priority_'.$instance['wf_priority'].'"',
				));
				$this->t->parse('columns','block_instance_column',true);
			}

			// Instance Name
			if($this->show_instName_column)
			{
				$this->t->set_var(array(
					'column_value'	=> $instance['insname'],
					'class_column'	=> 'class="col_name"',
				));
				$this->t->parse('columns','block_instance_column',true);
			}

			// Process Name
			if($this->show_procname_column)
			{
				$css_procname = $instance['wf_normalized_name'];
				$this->t->set_var(array(
					'column_value'	=> '<span class="'.$css_procname.'">'.$instance['wf_procname'].':'.$instance['wf_version'].'</span>',
					'class_column'	=> 'class="row_'.$css_procname.'"',
				));
				$this->t->parse('columns','block_instance_column',true);
			}

			// Activity. Always show this information.
			$act_icon = $this->act_icon($instance['wf_type'],$instance['wf_is_interactive']);
			$this->t->set_var(array(
				'column_value'	=> $act_icon.$instance['wf_name'],
				'class_column'	=> 'class="activity_'.$instance['wf_name'].'"',
			));
			$this->t->parse('columns','block_instance_column',true);

			//Category
			if($this->show_cat_column)
			{
				$cat_name = $this->cat->id2name($instance['wf_category'],'name');
				$this->t->set_var(array(
					'column_value'	=> $cat_name,
					'class_column'	=> 'class="category_'.$instance['wf_category'].'"',
				));
				$this->t->parse('columns','block_instance_column',true);
			}

			// Activity Status
			if($this->show_actStatus_column)
			{
				$this->t->set_var(array(
					'column_value'	=> $act_icon.$instance['wf_act_status'],
					'class_column'	=> 'class="activity_status_'.$instance['wf_act_status'].'"',
				));
				$this->t->parse('columns','block_instance_column',true);
			}

			// Owner
			if($this->show_owner_column)
			{
				$GLOBALS['phpgw']->accounts->get_account_name($instance['wf_owner'],$lid,$fname_owner,$lname_owner);
				$this->t->set_var(array(
					'column_value'	=> $fname_owner . ' ' . $lname_owner,
					'class_column'	=> 'class="instance_owner_'.$instance['wf_owner'].'"',
				));
				$this->t->parse('columns','block_instance_column',true);
			}

			// User. Always show this information.
			$GLOBALS['phpgw']->accounts->get_account_name($instance['wf_user'],$lid,$fname_user,$lname_user);
			if ($instance['wf_user'] == "*")
			{ // case for non assigned instances
				$shownuser = "*";
			}
			elseif ($instance['wf_user'] == "")
			{ // case for aborted instances
				$shownuser = lang('none');
			}
			else
			{ // all others
				$shownuser = $fname_user . ' ' . $lname_user;
			}
			$this->t->set_var(array(
				'column_value'	=> $shownuser,
				'class_column'	=> 'class="instance_user_'.$instance['wf_user'].'"',
			));
			$this->t->parse('columns','block_instance_column',true);

 			$this->t->set_var(array(
				'color_line'		=> $this->nextmatchs->alternate_row_color($tr_color,true)
			));

		}


		function show_select_process($all_processes_data, $filter_process)
		{
			$this->t->set_block('user_instances', 'block_select_process', 'select_process');
			$this->t->set_var('selected_filter_process_all', (!$filter_process)? 'selected="selected"' : '');

			foreach ($all_processes_data as $process_data)
			{
				$this->t->set_var(array(
					'selected_filter_process'	=> ($filter_process == $process_data['wf_p_id'])? 'selected="selected"' : '',
					'filter_process_id'		=> $process_data['wf_p_id'],
					'filter_process_name'		=> $process_data['wf_procname'],
					'filter_process_version'	=> $process_data['version']
				));
				$this->t->parse('select_process', 'block_select_process', true);
			}
		}

		function show_select_activity($all_activitys_data, $filter_activity)
		{
			$this->t->set_block('user_instances', 'block_select_activity', 'select_activity');
			$this->t->set_var('selected_filter_activity_all', (!($filter_activity))? 'selected="selected"' : '');

			foreach ($all_activitys_data as $activity_data)
			{
				$this->t->set_var(array(
					'selected_filter_activity'	=> ($filter_activity == $activity_data['wf_name'])? 'selected="selected"' : '',
					'filter_activity_name'		=> $activity_data['wf_name']
				));
				$this->t->parse('select_activity', 'block_select_activity', true);
			}
		}

		function show_select_user($filter_user)
		{
			$GLOBALS['phpgw']->accounts->get_account_name($GLOBALS['phpgw_info']['user']['account_id'], $lid, $fname, $lname);

			$this->t->set_var(array(
				'filter_user_all'	=> ($filter_user == '')? 'selected="selected"' : '',
				'filter_user_star'	=> ($filter_user == '*')? 'selected="selected"' : '',
				'filter_user_user'	=> ($filter_user == $GLOBALS['phpgw_info']['user']['account_id'])? 'selected="selected"' : '',
				'filter_user_id'	=> $GLOBALS['phpgw_info']['user']['account_id'],
				'filter_user_name'	=> $fname . ' ' . $lname
			));
		}

		function show_select_act_status($filter_act_status)
		{
			$this->t->set_var(array(
				'filter_act_status_all'	=> ($filter_act_status == '')? 'selected="selected"' : '',
				'filter_act_status_running'	=> ($filter_act_status == 'running')? 'selected="selected"' : '',
				'filter_act_status_completed'	=> ($filter_act_status == 'completed')? 'selected="selected"' : '',
				'filter_act_status_empty'	=> ($filter_act_status == 'empty')? 'selected="selected"' : '',
			));
		}

		//! show the 'filter instances by id' button in the last row of the list of instances
		function show_filter_instance($show_it=false)
		{
			$this->t->set_block('user_instances', 'block_filter_instances', 'filter_instance_zone');

			if ($show_it)
			{
				$this->t->set_var(array(
					'filter_instance_id'	=> $this->filter_instance,
					'nb_columns'		=> $this->nb_columns,
				));
				$this->translate_template('block_filter_instances');
				$this->t->parse('filter_instance_zone', 'block_filter_instances', true);
			}
			else
			{
				$this->t->set_var(array( 'filter_instance_zone' => ''));
			}
		}

		//! fill the $this->where string taking care of all the filters and checkboxes actionned
		function fill_where_data()
		{
				// there're 5 principal filters, process, activity (id/name), user, category and search --------------
				// nothing to prepare for search, let's look the 4 others...
				$this->where = '';
				$wheres = array();
				$or_wheres = array();

				if(!($this->filter_activity_name==''))
				{
					$wheres[] = "ga.wf_name='" .$this->GUI->security_cleanup($this->filter_activity_name, false, true). "'";
				}
				if(!($this->filter_user==''))
				{
					$wheres[] = "gia.wf_user='".(int)$this->filter_user."'";
				}
				if (!( ($this->filter_category == '') || ($this->filter_category == 'all')) )
				{
					$childs = $this->cat->return_all_children((int)$this->filter_category);
					$cats_list = implode(',',$childs);
					$wheres[] = "gi.wf_category in (".$cats_list.")";
				}

				// now adding special advanced search options or default values--------------------
				// TODO this should maybe go elsewhere, in a bo_ something or the engine

				//if we want only one instance
				if ($this->filter_instance)
				{
					$wheres[] = "(gi.wf_instance_id='".(int)$this->filter_instance."')";
				}

				//activities selection :: activities are running OR completed OR NULL (for aborted instances for example)
				// and by default we keep all activities
				if ($this->filter_act_status =='running')
				{
					$wheres[] = "(gia.wf_status='running')";
				}
				elseif ($this->filter_act_status =='completed')
				{
					$wheres[] = "(gia.wf_status='completed')";
				}
				elseif ($this->filter_act_status =='empty')
				{
					// we do not want completed or running activities
					$wheres[] = "(gia.wf_status is NULL)";
				}

				if( count($wheres) > 0 )
				{
					$this->where = implode(' and ', $wheres);
					//echo "<hr>where: <pre>";print_r($this->where);echo "</pre>";
				}
			}

			/* Return a select form element with the categories option dialog in it */
			function cat_option($cat_id='',$notall=False,$java=True,$multiple=False)
			{
				if($java)
				{
					$jselect = ' {filters_on_change}';
				}
				/* Setup all and none first */
				$cats_link  = "\n" .'<select name="filter_category'.(($multiple)? '[]':'').'"' .$jselect . (($multiple)? 'multiple ' : '') . ">\n";
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
