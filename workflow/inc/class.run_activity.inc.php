<?php

	require_once(dirname(__FILE__) . '/' . 'class.workflow.inc.php');

	class run_activity extends workflow
	{
		var $public_functions = array(
			'go'	=> true,
		);

		//Runtime Object from the workflow engine
		var $runtime;
		// Activity engine object. This is the object we'll be running to obtain the rigth activity
		//var $base_activity;
		//This is the right activity object
		var $activity;
		//Process engine object. Used to retrieve at least paths and configuration values
		var $process;
		// GUI engine object. Act carefully with it.
		var $GUI;
		//a message array
		var $message = Array();
		// a categorie object for categories
		var $categories;
		// local process configuration cache
		var $conf = array();
		// local activity template
		var $wf_template;
		// The instance object we will manipulate
		var $instance;
		var $activity_type;
		// then we retain all usefull vars as members, to make them avaible in user's source code
		// theses are data which can be set before the user code and which are not likely to change because of the user code
		var $db;
		var $process_id;
		var $activity_id;
		var $process_name;
		var $process_version;
		var $activity_name;
		var $user_name;
		var $view_activity; //activity id of the view activity avaible for this process
		// theses 4 vars aren't avaible for the user code, they're set only after this user code was executed
		var $instance_id=0;
		var $instance_name='';
		var $instance_owner=0;
		var $owner_name='';
		// array used by automatic parsing:
		var $priority_array = Array();
		var $submit_array = Array();
		// vars used by automatic parsing
		var $display_owner=0; // if 0 draw nothing, 1 draw selected owner, else draw a select box for owner, see function descr
		var $display_next_user=0; // if 0 draw nothing, 1 draw selected next user, else draw a select box for next_user, see function descr
		var $display_history=0; //if 0 draw nothing, 1 draw the history table in the bottom of the screen (ignore use_automatic_parsing config value)
		//print mode
		var $print_mode = false;
		var $enable_print_mode = false;
		// array of roles associated with the activity, usefull for lists of users associated with theses roles
		var $act_role_names= Array();
		//Array of ui_agent objects
		var $agents = Array();

		function run_activity()
		{
			parent::workflow();
			$this->runtime		=& CreateObject('workflow.workflow_wfruntime');
			$this->runtime->setDebug(_DEBUG);
			//$this->base_activity	=& CreateObject('workflow.workflow_baseactivity');
			//$this->process		=& CreateObject('workflow.workflow_process');
			$this->GUI		=& CreateObject('workflow.workflow_gui');
			$this->categories 	=& CreateObject('phpgwapi.categories');
			// TODO: open a new connection to the database under a different username to allow privilege handling on tables
			$this->db 		=& $GLOBALS['phpgw']->db->link_id();
		}

		/**
		 * * This function is used to run all activities for specified instances. it could be interactive activities
		 * * or automatic activities. this second case is the reason why we return some values
		*  * @param $activityId is the activity_id it run
		*  * @param $iid is the instance id it run for
		*  * @param $auto is true by default
		*  * @return AN ARRAY, or at least true or false. This array can contain :
		*	* a key 'failure' with an error string the engine will retrieve in instance error messages in case of
		*	failure (this will mark your execution as Bad),
		*	* a key 'debug' with a debug string the engine will retrieve in instance error messages,
				 */
		function go($activity_id=0, $iid=0, $auto=0)
		{
			$result=Array();

			if ($iid)
			{
				$_REQUEST['iid'] = $iid;
			}
			$iid = $_REQUEST['iid'];

			//$activity_id is set when we are in auto mode. In interactive mode we get if from POST or GET
			if (!$activity_id)
			{
				$activity_id	= (int)get_var('activity_id', array('GET','POST'), 0);
			}

			// load activity and instance
			if (!$activity_id)
			{
				$result['failure'] =  $this->runtime->fail(lang('Cannot run unknown activity'), true, _DEBUG, $auto);
				return $result;
			}

			//initalising activity and instance objects inside the WfRuntime object
			if (!($this->runtime->loadRuntime($activity_id,$iid)))
			{
				$result['failure'] = $this->runtime->fail(lang('Cannot run the activity'), true, _DEBUG, $auto);
				return $result;
			}

			$activity =& $this->runtime->getActivity($activity_id, true, true);
			$this->activity =& $activity;
			// the instance is avaible with $instance or $this->instance
			// note that for standalone activities this instance can be an empty instance object, but false is a bad value
			//$this->instance =& $this->runtime->loadInstance($iid);

			// HERE IS A BIG POINT: we map the instance to a runtime object
			// user code will manipulate a stance, thinking it's an instance, but it is
			// in fact a WfRuntime object, mapping all instance functions
			$this->instance =& $this->runtime;
			$instance =& $this->instance;
			if (!($instance))
			{
				$result['failure'] = $this->runtime->fail(lang('Cannot run the activity without instance'), true, _DEBUG, $auto);
				return $result;
			}
			$this->instance_id = $instance->getInstanceId();

			// load process
			$this->process =& $this->runtime->getProcess();
			if (!($this->process))
			{
				$result['failure'] = $this->runtime->fail(lang('Cannot run the activity without her process').$instance, true, _DEBUG, $auto);
				return $result;
			}

			//set some global variables needed
			$GLOBALS['workflow']['__leave_activity']=false;
			$GLOBALS['user'] = $GLOBALS['phpgw_info']['user']['account_id'];

			//load role names, just an information
			$this->act_role_names = $activity->getActivityRoleNames();

			// load code sources
			$source = GALAXIA_PROCESSES . '/' . $this->process->getNormalizedName(). '/' . 'compiled' . '/' . $activity->getNormalizedName(). '.php';
			//$shared = GALAXIA_PROCESSES . '/' . $this->process->getNormalizedName(). '/' . 'code' . '/' . 'shared.php';

			// Activities' code will have at their disposition the $db object to handle database interactions
			// they can access it in 3 ways: $db $this->db or $GLOBALS['workflow']['db']
			$db 				=& $this->db;
			$GLOBALS['workflow']['db']	=& $this->db;
			//set some other usefull vars
			$this->activity_type	= $activity->getType();
			$this->process_id 	= $activity->getProcessId();
			$this->activity_id 	= $activity_id;
			$this->process_name	= $this->process->getName();
			$this->process_version	= $this->process->getVersion();
			$this->activity_name	= $activity->getName();
			$this->user_name	= $GLOBALS['phpgw']->accounts->id2name($GLOBALS['user']);
			$this->view_activity	= $this->GUI->gui_get_process_view_activity($this->process_id);

			//we set them in $GLOBALS['workflow'] as well
			$GLOBALS['workflow']['wf_activity_type']	=& $this->activity_type;
			$GLOBALS['workflow']['wf_process_id'] 		=& $this->process_id;
			$GLOBALS['workflow']['wf_activity_id'] 		=& $this->activity_id;
			$GLOBALS['workflow']['wf_process_name']		=& $this->process_name;
			$GLOBALS['workflow']['wf_process_version']	=& $this->process_version;
			$GLOBALS['workflow']['wf_activity_name']	=& $this->activity_name;
			$GLOBALS['workflow']['wf_user_name']		=& $this->user_name;
			$GLOBALS['workflow']['wf_view_activity']	=& $this->view_activity;

			//FIXed: useless, we remove it
			// run the shared code (just in case because each activity is supposed to include it)
			//include_once($shared);

			// run the activity
			//interactive section
			if (!$auto && $activity->isInteractive())
			{

				$this->print_mode = get_var('print_mode', array('POST','GET'), false);

				//get configuration options with default values if no init was done before
				$myconf = array(
					'display_please_wait_message'		=> 0,
					'use_automatic_parsing' 		=> 1,
					'show_activity_title' 			=> 1,
					'show_instance_name'			=> 0,
					'show_multiple_submit_as_select' 	=> 0,
					'show_activity_info_zone' 		=> 1,
				);
				//this will give use asked options and som others used by WfRuntime
				$this->conf =& $this->runtime->getConfigValues($myconf);
				// if process conf says so we display a please wait message until the activity form is shown
				if ($this->conf['display_please_wait_message'])
				{
					$this->show_wait_message();
				}
				$this->prepare_javascript_submit();

				if (!($this->print_mode))
				{
					$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['workflow']['title'] . ' - ' . lang('Running Activity');
					$GLOBALS['phpgw']->common->phpgw_header();
					echo parse_navbar();
				}
				else
				{
					$GLOBALS['phpgw_info']['flags'] = array(
						'noheader' => True,
						'nonavbar' => True,
						'currentapp' => 'workflow',
						'enable_nextmatchs_class' => True
					);
					$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['workflow']['title'] . ' - ' . lang('Running Activity');
					$GLOBALS['phpgw']->common->phpgw_header();
				}

				// activities' code will have at their disposition the $template object to handle the corresponding activity template,
				// but $GLOBALS['phpgw']->template will also be available, in case global scope for this is needed
				// and we have as well the $this->wf_template for the same template
				$template =& CreateObject('phpgwapi.Template', GALAXIA_PROCESSES.'/');

				$template->set_file('template', $this->process->getNormalizedName().'/'.'code'.'/'.'templates'.'/'.$activity->getNormalizedName().'.tpl');
				$GLOBALS['phpgw']->template =& $template;
				$this->wf_template =& $template;

				// They will also have at their disposition theses array, used for automatic parsing
				$GLOBALS['workflow']['priority_array']	=& $this->priority_array;
				$GLOBALS['workflow']['submit_array']	=& $this->submit_array;
				// and some vars for automatic parsing as well
				$GLOBALS['workflow']['display_owner']    =& $this->display_owner;
				$GLOBALS['workflow']['display_next_user']=& $this->display_next_user;
			}
			//echo "<br><br><br><br><br>Including $source <br>In request: <pre>";print_r($_REQUEST);echo "</pre>";
			//[__leave_activity] is setted if needed in the xxx_pre code or by the user in his code
			// HERE the user code is 'executed'. Note that we do not use include_once or require_once because
			//it could the same code several times with automatic activities looping in the graph and it still
			//need to be executed
			include ($source);
			//Now that the instance is ready and that user code has maybe change some things
			// we can catch some others usefull vars
			$this->instance_id	= $instance->getInstanceId();
			$this->instance_name	= $instance->getName();
			$this->instance_owner	= $instance->getOwner();
			$this->owner_name	= $GLOBALS['phpgw']->accounts->id2name($this->instance_owner);
			if ($this->owner_name == '')
			{
				$this->owner_name = lang('Nobody');
			}
			$GLOBALS['workflow']['wf_instance_id'] 	=& $this->instance_id;
			$GLOBALS['workflow']['wf_instance_name']=& $this->instance_name;
			$GLOBALS['workflow']['wf_instance_owner']=& $this->instance_owner;
			$GLOBALS['workflow']['wf_owner_name']=& $this->owner_name;


			// TODO: process instance comments

			$instructions = $this->runtime->handle_postUserCode(_DEBUG);
			switch($instructions['action'])
			{
				//interactive activity completed
				case 'completed':
					// re-retrieve instance data which could have been modified by an automatic activity
					$this->instance_id      = $instance->getInstanceId();
					$this->instance_name    = $instance->getName();

					$this->show_common_vars();
					// and display completed template
					$this->show_completed_page($instructions['engine_info']);
					break;
				//interactive activity still in interactive mode
				case 'loop':
					$this->show_common_vars();
					$this->show_form();
					break;
				//nothing more
				case 'leaving':
					$this->show_common_vars();
					$this->show_leaving_page();
					break;
				//non-interactive activities, auto-mode
				case 'return':
					$result=Array();
					$this->message[] = $this->GUI->get_error(false, _DEBUG);
					$this->message[] = $this->runtime->get_error(false, _DEBUG);
					//$this->message[] = $this->process->get_error(false, _DEBUG);
					$result =& $instructions['engine_info'];
					$this->message[] = $result['debug'];
					$result['debug'] = implode('<br />',array_filter($this->message));
					return $result;
					break;
				default:
					return $this->runtime->fail(lang('unknown instruction from the workflow engine: %1', $instructions['action']), true, _DEBUG);
					break;
			}
		}

		function prepare_javascript_submit()
		{
			if(!@is_object($GLOBALS['phpgw']->js))
			{
				$GLOBALS['phpgw']->js =& CreateObject('phpgwapi.javascript');
			}
			$GLOBALS['phpgw_info']['flags']['java_script'] .= '<script type="text/javascript">
				function confirmSubmit(submit_name,txt_confirm)
				{
					if(confirm(txt_confirm))
						return true ;
					else
						return false;
				}</script>';
		}

		//! show a waiting message using css and script to hide it on onLoad events.
		/**
		 * You can enable/disable it in process configuration.
		 * Css for the please wait message is defined in app.css, a css automatically included by phpgroupware
		 */
		function show_wait_message()
		{
			if(!@is_object($GLOBALS['phpgw']->js))
			{
				$GLOBALS['phpgw']->js =& CreateObject('phpgwapi.javascript');
			}
			$please_wait=lang('Please wait, task in progress ...');
			$GLOBALS['phpgw_info']['flags']['java_script'] .= '<script type="text/javascript">
				document.write(\'<DIV id="loading"><BR><BR>\'+"'.$please_wait.'" +\'</DIV>\');
				function hide_loading()
				{
					document.getElementById("loading").style.display="none";
				}</script>';
			$GLOBALS['phpgw']->js->set_onload('hide_loading();');
		}

		//! show the page avaible when completing an activity
		function show_completed_page(&$infos)
		{
			$this->t->set_file('activity_completed', 'activity_completed.tpl');
			$this->t->set_block('activity_completed', 'report_row', 'rowreport');
			//build an icon array for show_engine_infos
			$icon_array = Array();

			$icon_array['ok'] 		= '<img src="'.$GLOBALS['phpgw']->common->image('workflow', 'check').'">';
			$icon_array['failure'] 		= '<img src="'.$GLOBALS['phpgw']->common->image('workflow', 'stop').'">';
			$icon_array['transition'] 	= '<img src="'.$GLOBALS['phpgw']->common->image('workflow', 'transition').'">';
			$icon_array['transition_human'] = '<img src="'.$GLOBALS['phpgw']->common->image('workflow', 'transition_human').'">';
			$icon_array['activity'] 	= '<img src="'.$GLOBALS['phpgw']->common->image('workflow', 'auto_activity').'">';
			$icon_array['dot'] 		= '<img src="'.$GLOBALS['phpgw']->common->image('workflow', 'puce').'">&nbsp;';
			$this->show_engine_infos($infos, $icon_array);
			$this->t->set_var(array(
				'wf_procname'	=> $this->process_name,
				'procversion'	=> $this->process_version,
				'actname'	=> $this->activity_name,
				'rowreport'	=> '',
			));

			$this->translate_template('activity_completed');
			$this->t->pparse('output', 'activity_completed');
			$this->show_after_running_page();
		}

		function show_engine_infos(&$infos, &$icon_array, $level=0)
		{
			//_debug_array($infos);
			if (!(is_array($infos)))
			{
				return ;
			}
			foreach ($infos as $infoitem => $content)
			{
				if (is_int($infoitem)) //splitting!
				{
					//recursive call with level increment
					$this->show_engine_infos($content, $icon_array,$level+1);
				}
				else
				{
					switch($infoitem)
					{
						case 'transition':
							$icon_type = $icon_array['transition'];
							if (isset($content['failure']))
							{
								$icon = $icon_array['failure'];
								$comment = lang('failure').'<br />'.$content['failure'];
								if (isset($content['target_name']))
								{
									$report = str_repeat($icon_array['dot'], $level).lang('transition to %1', $content['target_name']);
								}
								else
								{
									$report = str_repeat($icon_array['dot'], $level).lang('transition');
								}
							}
							else
							{
								if ($content['status']=='not autorouted')
								{
									$icon_type = $icon_array['transition_human'];
									$icon = '&nbsp;';
									$report = str_repeat($icon_array['dot'], $level).lang('transition waiting for human interaction');
									$comment = lang('no routage').'<br />';
								}
								else
								{
									if ($content['status']=='waiting')
									{
										$comment = lang('activity is waiting for pending transitions').'<br />';
									}
									else
									{
										$comment = '';
									}
									$icon = $icon_array['ok'];
									$report = str_repeat($icon_array['dot'], $level).lang('transition to %1', $content['target_name']);
								}
								$comment .= $content['debug'];
							}
							$this->t->set_var(array(
								'icon_type_report'	=> $icon_type,
								'icon_report'		=> $icon,
								'label_report'		=> $report,
								'comment_report'	=> $comment,
								'row_class'             => $this->nextmatchs->alternate_row_color($tr_color, true),
							));
							$this->t->parse('report','report_row', true);
							break;
						case 'activity':
							$icon_type = $icon_array['activity'];
							if (isset($content['failure']))
							{
								$icon = $icon_array['failure'];
								$report = str_repeat($icon_array['dot'], $level).lang('activity failure');
								if (isset($content['info']))
								{
									$comment = $content['info']['activity_name'].'<br />'.$content['failure'];
								}
								else
								{
									$comment = $content['failure'];
								}
							}
							else
							{
								if (isset($content['completed']))
								{
									$icon = $icon_array['ok'];
									$report = str_repeat($icon_array['dot'], $level).lang('activity completed');
								}
								else
								{

									$icon = $icon_array['failure'];
									$report = str_repeat($icon_array['dot'], $level).lang('activity failure');
								}
								if (isset($content['info']))
								{
									$comment = $content['info']['activity_name'];
								}
								else
								{
									$comment = '';
								}
							}
							$this->t->set_var(array(
								'icon_type_report'      => $icon_type,
								'icon_report'		=> $icon,
								'label_report'		=> $report,
								'comment_report'	=> $comment,
								'row_class'		=> $this->nextmatchs->alternate_row_color($tr_color, true),
							));
							$this->t->parse('report','report_row', true);
							if (isset($content['next']))
							{
								//recursive call
								$this->show_engine_infos($content['next'], $icon_array,$level);
							}
							break;
					}
				}
			}

		}

		//! show the common variable of interactive forms (like messages)
		function show_common_vars()
		{
			$this->message[] = $this->GUI->get_error(false, _DEBUG);
			$this->message[] = $this->runtime->get_error(false, _DEBUG);
			$this->t->set_var(array(
				'wf_message'	=> implode('<br />',array_filter($this->message)),
				)
			);
		}

		//! show the page avaible when leaving an activity (with a Cancel or Quit button)
		function show_leaving_page()
		{
			$this->t->set_file('leaving_activity', 'leaving_activity.tpl');
			$this->t->set_var(array(
				'wf_procname'	=> $this->process_name,
				'procversion'	=> $this->process_version,
				'actname'	=> $this->activity_name,
			));

			//check real avaible actions on this instance
			//we assume user is not in read-only mode, if he were actions would be blocked anyway, and he should not come
			//here with a read-only right
			$actions = $this->GUI->getUserActions($GLOBALS['user'],$this->instance_id,$this->activity_id, false);
			if (isset($actions['release']))
			{//we can release, it means we were not in auto-release mode
				//prepare a release command on the user_instance form
				$link_array = array(
					'menuaction'		=> 'workflow.ui_userinstances.form',
					'filter_process'	=> $this->process_id,
					'filter_instance'	=> $this->instance_id,
					'iid'			=> $this->instance_id,
					'aid'			=> $this->activity_id,
					'release'		=> 1,
				);
				$releasetxt = lang('release activity for this instance');
				$this->t->set_var(array(
					'release_text'	=> lang('This activity for this instance is actually only avaible for you.'),
					'release_button'=> '<a href="'.$GLOBALS['phpgw']->link('/index.php',$link_array)
						.'"><img src="'. $GLOBALS['phpgw']->common->image('workflow', 'fix')
						.'" alt="'.$releasetxt.'" title="'.$releasetxt.'" width="16" >'
						.$releasetxt.'</a>',
				));
			}
			else
			{//we cannot release, 3 reasons
			 // * already done in auto-release
			 // * standalone or view activity
			 // * multi-user concurrency problem moved some engine objects in other states
				$this->t->set_var(array(
					'release_text'	=> lang('This activity for this instance is not specifically assigned to you.'),
					'release_button'=> '',
				));

			}
			$this->translate_template('leaving_activity');
			$this->t->pparse('output', 'leaving_activity');
			$this->show_after_running_page();
		}

		//! show the bottom of end run_activity interactive pages with links to user_instance form
		/**
		 * for start activities we link back to user_openinstance form
		 * and for standalone activities we loop back to global activities form
		 */
		function show_after_running_page()
		{
			$this->t->set_file('after_running', 'after_running.tpl');

			//prepare the links form
			$link_data_proc = array(
				'menuaction'		=> 'workflow.ui_userinstances.form',
				'filter_process'	=> $this->process_id,
			);
			$link_data_inst = array(
				'menuaction'		=> 'workflow.ui_userinstances.form',
				'filter_instance'	=> $this->instance_id,
			);
			if ($this->activity_type == 'start')
			{
				$activitytxt = lang('get back to instance creation');
				$act_button_name = lang('New instance');
				$link_data_act = array(
					'menuaction'		=> 'workflow.ui_useropeninstance.form',
				);
			}
			elseif  ($this->activity_type == 'standalone')
			{
				$activitytxt = lang('get back to global activities');
				$act_button_name = lang('Global activities');
				$link_data_act = array(
					'menuaction'		=> 'workflow.ui_useractivities.form',
					'show_globals'		=> true,
				);
			}
			else
			{
				$activitytxt = lang('go to same activities for other instances of this process');
				$act_button_name = lang('activity %1', $this->activity_name);
				$link_data_act = array(
					'menuaction'		=> 'workflow.ui_userinstances.form',
					'filter_process'        => $this->process_id,
					'filter_activity_name'	=> $this->activity_name,
				);
			}
			$button='<img src="'. $GLOBALS['phpgw']->common->image('workflow', 'next')
				.'" alt="'.lang('go').'" title="'.lang('go').'" width="16" >';
			$this->t->set_var(array(
				'same_instance_text'	=> ($this->activity_type=='standalone')? '-' : lang('go to the actual state of this instance'),
				'same_activities_text'	=> $activitytxt,
				'same_process_text'	=> lang('go to same process activities'),
				'same_instance_button'	=> ($this->activity_type=='standalone')? '-' : '<a href="'.$GLOBALS['phpgw']->link('/index.php',$link_data_inst).'">'
					.$button.lang('instance %1', ($this->instance_name=='')? $this->instance_id: $this->instance_name).'</a>',
				'same_activities_button'=> '<a href="'.$GLOBALS['phpgw']->link('/index.php',$link_data_act).'">'
					.$button.$act_button_name.'</a>',
				'same_process_button'	=> '<a href="'.$GLOBALS['phpgw']->link('/index.php',$link_data_proc).'">'
					.$button.lang('process %1', $this->process_name).'</a>',
			));
			$this->translate_template('after_running');
			$this->t->pparse('output', 'after_running');
			$GLOBALS['phpgw']->common->phpgw_footer();
		}

		//! show the activity form with automated parts if needed
		function show_form()
		{
			//set a global template for interactive activities
			$this->t->set_file('run_activity','run_activity.tpl');

			//set the css style files links
			$this->t->set_var(array(
				'run_activity_css_link'	=> $this->get_css_link('run_activity', $this->print_mode),
				'run_activity_print_css_link'	=> $this->get_css_link('run_activity', true),
			));


			// draw the activity's title zone
			$this->parse_title($this->activity_name);

			//draw the instance_name input or label
			// init wf_name to the requested one or the stored name
			// the requested one handle the looping in activity form

			$wf_name = get_var('wf_name','post',$this->instance->getName());
			$this->parse_instance_name($wf_name);

			//draw the instance_name input or label
			// init wf_set_owner to the requested one or the stored owner
			// the requested one handle the looping in activity form
			$wf_set_owner = get_var('wf_set_owner','post',$this->instance->getOwner());
			$this->parse_instance_owner($wf_set_owner);

			// draw the activity central user form
			$this->t->set_var(array('activity_template' => $this->wf_template->parse('output', 'template')));

			//draw the select priority box
			// init priority to the requested one or the stored priority
			// the requested one handle the looping in activity form
			$priority = get_var('wf_priority','post',$this->instance->getPriority());
			$this->parse_priority($priority);

			//draw the select next_user box
			// init next_user to the requested one or the stored one
			// the requested one handle the looping in activity form
			$next_user = get_var('wf_next_user','POST',$this->instance->getNextUser());
			$this->parse_next_user($next_user);

			//draw print_mode buttons
			$this->parse_print_mode_buttons();

			//draw the activity submit buttons
			$this->parse_submit();

			//draw the info zone
			$this->parse_info_zone();

			//draw the history zone if user wanted it
			$this->parse_history_zone();

			$this->translate_template('run_activity');
			$this->t->pparse('output', 'run_activity');
			$GLOBALS['phpgw']->common->phpgw_footer();
		}


		/**
		 * * Draw a 'print mode' or 'back to normal mode' button if $this->print_mode is not false
		 * * and if $this->enable_print_mode is true
		 */
		function parse_print_mode_buttons()
		{
			$this->t->set_block('run_activity', 'block_print_mode_zone', 'print_mode_zone');

			if (($this->conf['use_automatic_parsing']) && ($this->enable_print_mode))
			{
				if ($this->print_mode)
				{
					$this->t->set_var(array(
						'print_mode_value'	=> lang('Close Printer friendly mode'),
						'print_mode_name'	=> 'not_print_mode',
					));
				}
				else
				{
					$this->t->set_var(array(
						'print_mode_value'	=> lang('Printer friendly'),
						'print_mode_name'       => 'print_mode',
					));
				}
				$this->t->parse('print_mode_zone', 'block_print_mode_zone', true);
			}
			else
			{
				$this->t->set_var(array( 'print_mode_zone' => ''));
			}
		}

		//!Parse the title in the activity form, the user can decide if he want this title to be shown or not
		/**
		 * * if you do not want thuis to be displayed set your process config value for show_activity_title to false
		*  * @param title is by default empty, You can give a title as a parameter.
		 */
		function parse_title($title='')
		{
			$this->t->set_block('run_activity', 'block_title_zone', 'title_zone');

			if (($this->conf['use_automatic_parsing']) && ($this->conf['show_activity_title']))
			{
				$this->t->set_var(array('activity_title'=> $title));
				$this->t->parse('title_zone', 'block_title_zone', true);
			}
			else
			{
				$this->t->set_var(array( 'title_zone' => ''));
			}
		}

		//!Parse the instance name input in the activity form, the user can decide if he want this name to be shown or not
		/**
		 * * if you do not want this to be displayed set your process config value for show_instance_name to false
		*  * @param instance_name is the name we will display.
		 */
		function parse_instance_name($instance_name)
		{
			$this->t->set_block('run_activity', 'block_instance_name_zone', 'instance_name_zone');

			if (($this->conf['use_automatic_parsing']) && ($this->conf['show_instance_name']))
			{
				$this->t->set_var(array('wf_name'=> $instance_name));
				$this->t->parse('instance_name_zone', 'block_instance_name_zone', true);
			}
			else
			{
				$this->t->set_var(array( 'instance_name_zone' => ''));
			}
		}

		//!Parse the set_owner select/display in the activity form, the user can decide if he want this name to be shown or not
		/**
		 * * if $this->display_owner is 0 we draw nothing (default value)
		 * * if $this->display_owner is 1 the owner is just shown
		 * * if $this->display_owner is anything else we draw a select box
		 * * this 'anything else' can be an associative array containing the 'role' and/or 'activity' key
		 * * the values associated with theses keys can be strings or array of strings containing roles and/or
		 * * activities's names. Users displayed in the select will then be the users having access to theses activities
		 * * and users which are mapped  to theses roles (one match per user is enought to be displayed).
		 * * ie: $this->display_owner = 2; will display all users mapped to roles on the process
		 * * $this->display_owner = array('role' => array('Chiefs','assistant'), 'activity' => 'updating foo'); will
		 * * display users having access to activity 'updating foo' AND which are mapped to 'Chief' OR 'assistant' roles
		 * * of course roles and activities names must be matching the current process's roles and activities names.
		*  * @param actual_owner is the selected owner in the select list we will display or the shown owner.
		 */
		function parse_instance_owner($actual_owner)
		{
			//inside the select
			$this->t->set_block('run_activity', 'block_owner_options', 'owner_options');
			//the select
			$this->t->set_block('run_activity', 'block_select_owner', 'wf_select_owner');
			// the whole area
			$this->t->set_block('run_activity', 'block_set_owner_zone', 'set_owner_zone');
			if ( 	(!$this->conf['use_automatic_parsing'])
				|| ( empty($this->display_owner) || (!($this->display_owner)) ))
			{
				//hide the instance owner zone
				$this->t->set_var(array( 'set_owner_zone' => ''));
			}
			else
			{
				// a little label before the select box
																$this->t->set_var(array('set_owner_text' => lang('Owner:')));
				if ((!(is_array($this->display_owner))) && ($this->display_owner==1))
				{
					//we will just display the owner
					$this->t->set_var(array('wf_select_owner' => $this->owner_name));
				}
				else
				{	//we will display a select

					//prepare retrieval of datas
					$subset=Array();
					if (is_array($this->display_owner))
					{
						foreach($this->display_owner as $key => $value)
						{
							if ($key=='role')
							{
								if (!(is_array($value)))
								{
									$value = explode(';',$value);
								}
								$subset[wf_role_name]= $value;
							}
							elseif ($key=='activity')
							{
								if (!(is_array($value)))
								{
									$value = explode(';',$value);
								}
								$subset[wf_activity_name]= $value;
							}
						}
					}
					//we'll ask the role_manager for it
					$role_manager =& CreateObject('workflow.workflow_rolemanager');
					// we expand groups to real users and want users mapped for a subset of the process
					// which is given by a user defined value
					$authorized_users = $role_manager->list_mapped_users($this->process_id, true, $subset );
					//first line of the select
					$this->t->set_var(array(
						'selected_owner_options_default'=> (!!$actual_owner)? 'selected="selected"' :'',
						'lang_default_owner'	=> lang('Default owner'),
					));
					//other lines
					foreach ($authorized_users as $user_id => $user_name)
					{
						$this->t->set_var(array(
							'owner_option_id'		=> $user_id,
							'owner_option_value'		=> $user_name,
							'selected_owner_options'	=> ($user_id == $actual_owner)? 'selected="selected"' :'',
						));
						//show the select line
						$this->t->parse('owner_options','block_owner_options',true);
					}
					//show the select
					$this->t->parse('wf_select_owner','block_select_owner',true);
				}
				//show the set owner zone
				$this->t->parse('set_owner_zone', 'block_set_owner_zone', true);
			}
		}

		//! Draw the priority select box in the activity form
		/**
		 * * Parse the priority select box in the activity form. The user can decide if he want this select box to be shown or not
		 * * by completing $this->priority_array.
		 * * For example like that : $this->priority_array = array(1 => '1-Low',2 =>'2', 3 => '3-High');
		 * * If the array is empty or the conf values says the user does not want automatic parsing no select box will be shown
		*  * @param actual_priority is by default at 1 and will be the selected activity level.
		 */
		function parse_priority($actual_priority=1)
		{
			$this->t->set_block('run_activity', 'block_priority_options', 'priority_options');
			$this->t->set_block('run_activity', 'block_priority_zone', 'priority_zone');
			if ((!$this->conf['use_automatic_parsing']) || (count($this->priority_array)==0))
			{
				//hide the priority zone
				$this->t->set_var(array( 'priority_zone' => ''));
			}
			else
			{
				if (!is_array($this->priority_array))
				{
					$this->priority_array = explode(" ",$this->priority_array);
				}
				//handling the select box
				foreach ($this->priority_array as $priority_level => $priority_label)
				{
					$this->t->set_var(array(
						'priority_option_name'		=> $priority_level,
 						'priority_option_value'		=> $priority_label,
 						'selected_priority_options'	=> ($priority_level == $actual_priority)? 'selected="selected"' :'',
					));
					//show the select box
					$this->t->parse('priority_options','block_priority_options',true);
				}
				// a little label before the select box
				$this->t->set_var(array('Priority_text' => lang('Priority level:')));
				//show the priority zone
				$this->t->parse('priority_zone', 'block_priority_zone', true);
			}
		}

		//!Parse the next_user select/display in the activity form, the user can decide if he want this to be shown or not
		/**
		 * * if $this->display_next_user is 0 we draw nothing (default value)
		 * * if $this->display_next_user is 1 the next_user is just shown
		 * * if $this->display_next_user is anything else we draw a select box
		 * * this 'anything else' can be an associative array containing the 'role' and/or 'activity' key
		 * * the values associated with theses keys can be strings or array of strings containing roles and/or
		 * * activities's names. Users displayed in the select will then be the users having access to theses activities
		 * * and users which are mapped to theses roles (one match per user is enought to be displayed).
		 * * ie: $this->display_next_user = 2; will display all users mapped to roles on the process
		 * * $this->display_next_user = array('role' => array('Chiefs','assistant'), 'activity' => 'updating foo'); will
		 * * display users having access to activity 'updating foo' AND which are mapped to 'Chief' OR 'assistant' roles
		 * * of course roles and activities names must be matching the current process's roles and activities names.
		*  * @param actual_next_user is the selected next_user in the select list we will display or the shown next_user.
		 */
		function parse_next_user($actual_next_user)
		{
		//echo "DEBUG parse_instance_next_user:actual_next_user:".$actual_next_user.'display_next_user:'
		//_debug_array($this->display_next_user);

			//inside the select
			$this->t->set_block('run_activity', 'block_next_user_options', 'next_user_options');
			//the select
			$this->t->set_block('run_activity', 'block_select_next_user', 'wf_select_next_user');
			// the whole area
			$this->t->set_block('run_activity', 'block_set_next_user_zone', 'set_next_user_zone');
			if ( 	(!$this->conf['use_automatic_parsing'])
				|| ( empty($this->display_next_user) || (!($this->display_next_user)) ))
			{
				//hide the instance next_user zone
				$this->t->set_var(array( 'set_next_user_zone' => ''));
			}
			else
			{
				// a little label before the select box
																$this->t->set_var(array('set_next_user_text' => lang('Next user:')));
				if ((!(is_array($this->display_next_user))) && ($this->display_next_user==1))
				{
					//we will just display the next_user
					$next_user_name = $GLOBALS['phpgw']->accounts->id2name($actual_next_user);
					if ($next_user_name == '')
					{
						$next_user_name = lang('not defined');
					}
					$this->t->set_var(array('wf_select_next_user' => $next_user_name));
				}
				else
				{	//we will display a select

					//prepare retrieval of datas
					$subset=Array();
					if (is_array($this->display_next_user))
					{
						foreach($this->display_next_user as $key => $value)
						{
							if ($key=='role')
							{
								if (!(is_array($value)))
								{
									$value = explode(';',$value);
								}
								$subset[wf_role_name]= $value;
							}
							elseif ($key=='activity')
							{
								if (!(is_array($value)))
								{
									$value = explode(';',$value);
								}
								$subset[wf_activity_name]= $value;
							}
						}
					}
					//we'll ask the role_manager for it
					$role_manager =& CreateObject('workflow.workflow_rolemanager');
					// we expand groups to real users and want users mapped for a subset of the process
					// which is given by a user defined value
					$authorized_users = $role_manager->list_mapped_users($this->process_id, true, $subset );
					//first line of the select
					$this->t->set_var(array(
						'selected_next_user_options_default'=> (!!$actual_next_user)? 'selected="selected"' :'',
						'lang_default_next_user'	=> lang('Default next user'),
					));
					//other lines
					foreach ($authorized_users as $user_id => $user_name)
					{
						$this->t->set_var(array(
							'next_user_option_id'		=> $user_id,
							'next_user_option_value'		=> $user_name,
							'selected_next_user_options'	=> ($user_id == $actual_next_user)? 'selected="selected"' :'',
						));
						//show the select line
						$this->t->parse('next_user_options','block_next_user_options',true);
					}
					//show the select
					$this->t->parse('wf_select_next_user','block_select_next_user',true);
				}
				//show the set next_user zone
				$this->t->parse('set_next_user_zone', 'block_set_next_user_zone', true);
			}
		}

		//! Draw the submit buttons on the activity form
		/**
		 * In this function we'll draw the command buttons asked for this activity.
		 * else we'll check $this->submit_array which should be completed in the activity source
		 * and is an array with the names of the submit options corresponding to the value like this:
		 * $this->submit_array['the_value_you_want']=lang('the label you want');
		 * if this array is empty we'll draw a simple submit button.
		 * The poweruser can decide to handle theses buttons in his own way in the config section
		 * He'll then have to draw it himself in his activity template.
		 * Note that the special value '__Cancel' is automatically handled and set the ['__leaving_activity']
		 * var to true.
		 */
		function parse_submit()
		{
			//inside the select box for submits
			$this->t->set_block('run_activity', 'block_submit_options', 'submit_options');
			//the select submit box
			$this->t->set_block('run_activity', 'block_submit_select_area', 'submit_select_area');
			//submit as buttons
			$this->t->set_block('run_activity', 'block_submit_buttons_area', 'submit_buttons_area');
			//the whole zone
			$this->t->set_block('run_activity', 'block_submit_zone', 'submit_zone');

			if (!($this->conf['use_automatic_parsing']))
			{
				// the user decided he'll do it his own way
				//empty the whole zone
				$this->t->set_var(array('submit_zone' => ''));
			}
			else
			{
				$buttons = '';
				if (count($this->submit_array)==0)
				{
					//the user didn't give us any instruction
					// we draw a simple submit button
					$this->t->set_var(array('submit_area',''));
					$buttons .= '<td class="wf_submit_buttons_button">';
					$buttons .= '<input name="wf_submit" type="submit" value="'.lang('Submit').'"/>';
					$buttons .= '</td>';
					//set the buttons
					$this->t->set_var(array('submit_buttons' => $buttons));
					// hide the select box zone
					$this->t->set_var(array('submit_select_area'=> ''));
					//show the buttons zone
					$this->t->parse('submit_buttons_area', 'block_submit_buttons_area', true);

				}
				else
				{
					//now we have another user choice. he can choose multiple submit buttons
					//or a select with only one submit
					if ( ($this->conf['show_multiple_submit_as_select']) && (count($this->submit_array) > 1) )
					{
						//multiple submits in a select box
						//handling the select box
						foreach ($this->submit_array as $submit_button_name => $submit_button_value)
						{
							$this->t->set_var(array(
								'submit_option_value'	=> $submit_button_value,
								'submit_option_name'	=> $submit_button_name,
							));

							//show the select box
							$this->t->parse('submit_options','block_submit_options',true);
						}
						//we need at least one submit button
						$this->t->set_var(array(
							'submit_button_name'	=> 'wf_submit',
							'submit_button_value'	=> lang('submit'),
						));
						// hide the multiple buttons zone
						$this->t->set_var(array('submit_buttons_area'=> ''));
						//show the select box zone
						$this->t->parse('submit_select_area', 'block_submit_select_area', true);
					}
					else
					{
						//multiple buttons with no select box or just one
						//draw input button for each entry
						foreach ($this->submit_array as $submit_button_name => $submit_button_value)
						{
							//now we can have some special options, like jscode
							if (is_array($submit_button_value))
							{
								$button_val = $submit_button_value['label'];
								$confirm = $submit_button_value['confirm'];

							}
							else
							{
								$button_val = $submit_button_value;
								$confirm = false;
							}
						 	$buttons .= '<td class="wf_submit_buttons_button">';
							$buttons .= '<input name="'.$submit_button_name.'" type="submit" value="'.$button_val.'" ';
							if (!!($confirm))
							{
								$buttons .= 'onClick="return confirmSubmit(\''.$submit_button_name.'\',\''.$confirm.'\')"/>';
							}
							else
							{
								$buttons .= '/>';
							}
							$buttons .= '</td>';
						}
						//set the buttons
						$this->t->set_var(array('submit_buttons' => $buttons));
						// hide the select box zone
						$this->t->set_var(array('submit_select_area'=> ''));
						//show the buttons zone
						$this->t->parse('submit_buttons_area', 'block_submit_buttons_area', true);
					}
				}
				//show the whole submit zone
				$this->t->parse('submit_zone', 'block_submit_zone', true);
			}
		}

		//!Parse the activity info zone in the activity form, the user can decide if he want it or not
		function parse_info_zone()
		{
			$this->t->set_block('run_activity', 'workflow_info_zone', 'info_zone');

			if (($this->conf['use_automatic_parsing']) && ($this->conf['show_activity_info_zone']))
			{
				$this->t->set_var(array(
					'wf_process_name'	=> $this->process_name,
					'wf_process_version'	=> $this->process_version,
					'wf_instance_id'	=> $this->instance_id,
					'wf_instance_name'	=> $this->instance_name,
					'wf_owner'		=> $this->owner_name,
					'wf_activity_name'	=> $this->activity_name,
					'wf_user_name'		=> $this->user_name,
					'wf_started'		=> $GLOBALS['phpgw']->common->show_date($this->instance->getStarted()),
					'wf_date'		=> $GLOBALS['phpgw']->common->show_date(),
				));
				$this->translate_template('workflow_info_zone');
				$this->t->parse('info_zone', 'workflow_info_zone', true);
			}
			else
			{
				$this->t->set_var(array( 'info_zone' => ''));
			}
		}

		//!Parse the history zone in the activity form, the user can decide if he want this name to be shown or not
		/**
		*  * if $this->display_history is 0 we draw nothing (default value)
		*  *
		*  * if $this->display_history is 1 we draw the history table
		*  * this function does not test the use_automatic_parsing configuration value
		 */
		function parse_history_zone()
		{
			if ( (empty($this->display_history)) || (!($this->display_history)))
			{
				//hide the history zone
				$this->t->set_var(array( 'history' => ''));
			}
			else
			{
				$inst_parser    =& CreateObject('workflow.bo_uiinstance', $this->t);
				$inst_parser->t =& $this->t;
				$inst_parser->parse_instance_history($this->instance->workitems);
				$this->t->set_var('history', $this->t->parse('output', 'history_tpl'));
			}
		}

	}
?>
