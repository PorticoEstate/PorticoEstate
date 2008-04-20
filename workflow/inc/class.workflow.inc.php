<?php
	//set here the global DEBUG level which is actually 0 (nothing) or 1 (all)
	if (!defined('_DEBUG')) define('_DEBUG', 0);
	class workflow
	{
		var $public_functions = array(
			'export'	=> true,
		);
		//the template
		var $t;

		var $wf_p_id;

		var $message = array();

		//TODO: when migration to bo_workflow_forms will be closed erase theses vars--------------
		//nextmatchs (max number of rows per page) and associated vars
		var $nextmatchs;
		var $start; //actual starting row number
		var $total_records; //total number of rows
		var $order; //column used for order
		var $sort; //ASC or DESC
		var $sort_mode; //combination of order and sort
		var $search_str;
		//------------------------------------------------------------------------------------------

		var $stats;

		var $wheres = array();

		function workflow()
		{
			// check version
			if (substr($GLOBALS['phpgw_info']['apps']['workflow']['version'],0,6) != '1.3.00')
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				die("Please upgrade this application to be able to use it");
			}

			$this->t		=& $GLOBALS['phpgw']->template;
			$this->wf_p_id		= (int)get_var('p_id', 'any', 0);
			$this->start		= (int)get_var('start', 'any', 0);
			$this->search_str	= get_var('find', 'any', '');
			$this->nextmatchs	=& CreateObject('phpgwapi.nextmatchs');
		}

		function fill_proc_bar($proc_info)
		{
			//echo "proc_info: <pre>";print_r($proc_info);echo "</pre>";
			$this->t->set_file('proc_bar_tpl', 'proc_bar.tpl');

			if ($proc_info['wf_is_valid'] == 'y')
			{
				$dot_color = 'green';
				$alt_validity = lang('valid');
			}
			else
			{
				$dot_color = 'red';
				$alt_validity = lang('invalid');
			}

			// if process is active show stop button. Else show start button, but only if it is valid. If it's not valid, don't show any activation or stop button.
			if ($proc_info['wf_is_active'] == 'y')
			{
				$start_stop = '<td><a href="'. $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_adminactivities.form&p_id='. $proc_info['wf_p_id'] .'&deactivate_proc='. $proc_info['wf_p_id']) .'"><img border ="0" src="'. $GLOBALS['phpgw']->common->image('workflow', 'stop') .'" alt="'. lang('stop') .'" title="'. lang('stop') .'" />'.lang('stop').'</a></td>';
			}
			elseif ($proc_info['wf_is_valid'] == 'y')
			{
				$start_stop = '<td><a href="'. $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_adminactivities.form&p_id='. $proc_info['wf_p_id'] .'&activate_proc='. $proc_info['wf_p_id']) .'"><img border ="0" src="'. $GLOBALS['phpgw']->common->image('workflow', 'refresh2') .'" alt="'. lang('activate') .'" title="'. lang('activate') .'" />'.lang('activate').'</a></td>';
			}
			else
			{
				$start_stop = '';
			}
			$this->t->set_var(array(
				'proc_name'			=> $proc_info['wf_name'],
				'version'			=> $proc_info['wf_version'],
				'img_validity'			=> $GLOBALS['phpgw']->common->image('workflow', $dot_color.'_dot'),
				'alt_validity'			=> $alt_validity,
				'start_stop'			=> $start_stop,
				'link_admin_activities'		=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_adminactivities.form&p_id='. $proc_info['wf_p_id']),
				'img_activity'			=> $GLOBALS['phpgw']->common->image('workflow', 'Activity'),
				'link_admin_processes'		=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_adminprocesses.form&p_id='. $proc_info['wf_p_id']),
				'img_change'			=> $GLOBALS['phpgw']->common->image('workflow', 'change'),
				'link_admin_shared_source'	=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_adminsource.form&p_id='. $proc_info['wf_p_id']),
				'img_code'			=> $GLOBALS['phpgw']->common->image('workflow', 'code'),
				'link_admin_export'		=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.workflow.export&p_id='. $proc_info['wf_p_id']),
				'link_admin_roles'		=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_adminroles.form&p_id='. $proc_info['wf_p_id']),
				'img_roles'			=> $GLOBALS['phpgw']->common->image('workflow', 'roles'),
				'link_graph'			=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_adminactivities.show_graph&p_id=' . $proc_info['wf_p_id']),
				'img_process'			=> $GLOBALS['phpgw']->common->image('workflow', 'Process'),
				'link_save_process'		=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=workflow.ui_adminprocesses.save_process&id='. $proc_info['wf_p_id']),
				'img_save'			=> $GLOBALS['phpgw']->common->image('workflow', 'save'),
				'link_compile'			=> $GLOBALS['phpgw']->link('/index.php', array(
					'menuaction'	=> 	'workflow.ui_adminactivities.form',
					'p_id'		=>	$proc_info['wf_p_id'],
					'compile'	=>	true,
				)),
				'img_compile'			=> $GLOBALS['phpgw']->common->image('workflow', 'compilation'),
			));

			$this->translate_template('proc_bar_tpl');
			return $this->t->parse('proc_bar', 'proc_bar_tpl');
		}

		function act_icon($type, $interactive)
		{
			switch($type)
			{
				case 'activity':
					$ic = "mini_".(($interactive == 'y')? 'blue_':'')."rectangle.gif";
					break;
				case 'switch':
					$ic = "mini_".(($interactive == 'y')? 'blue_':'')."diamond.gif";
					break;
				case 'start':
					$ic="mini_".(($interactive == 'y')? 'blue_':'')."circle.gif";
					break;
				case 'end':
					$ic="mini_".(($interactive == 'y')? 'blue_':'')."dbl_circle.gif";
					break;
				case 'split':
					$ic="mini_".(($interactive == 'y')? 'blue_':'')."triangle.gif";
					break;
				case 'join':
					$ic="mini_".(($interactive == 'y')? 'blue_':'')."inv_triangle.gif";
					break;
				case 'standalone':
					$ic="mini_".(($interactive == 'y')? 'blue_':'')."hexagon.gif";
					break;
				case 'view':
					$ic="mini_blue_eyes.gif";
					break;
				default:
					$ic="no-activity.gif";
			}
			return '<img src="'. $GLOBALS['phpgw']->common->image('workflow', $ic) .'" alt="'. lang($type) .'" title="'. lang($type) .'" />';
		}

		function translate_template($template_name)
		{
			$undef = $this->t->get_undefined($template_name);
			if ($undef != False)
			{
				foreach ($undef as $value)
				{
					$valarray = explode('_', $value);
					$type = array_shift($valarray);
					$newval = implode(' ', $valarray);
					if ($type == 'lang')
					{
						$this->t->set_var($value, lang($newval));
					}
				}
			}
		}

		function show_errors(&$activity_manager, &$error_str)
		{
			$valid = $activity_manager->validate_process_activities($this->wf_p_id);
			if (!$valid)
			{
				$errors = $activity_manager->get_error(true);
				$error_str = '<b>' . lang('The following items must be corrected to be able to activate this process').':</b><br/><small><ul>';
				foreach ($errors as $error)
				{
					$error_str .= '<li>'. $error . '<br/>';
				}
				$error_str .= '</ul></small>';
				return 'n';
			}
			else
			{
				$error_str = '';
				return 'y';
			}
		}

		function get_source($proc_name, $act_name, $type)
		{
			switch($type)
			{
				case 'code':
					$path =  'activities' . '/' . $act_name . '.php';
					break;
				case 'template':
					$path = 'templates' . '/' . $act_name . '.tpl';
					break;
				default:
					$path = 'shared.php';
					break;
			}
			$complete_path = GALAXIA_PROCESSES . '/' . $proc_name . '/' . 'code' . '/' . $path;
												if (!$file_size = filesize($complete_path)) return '';
			$fp = fopen($complete_path, 'r');
			$data = fread($fp, $file_size);
			fclose($fp);
			return $data;
		}

		function save_source($proc_name, $act_name, $type, $source)
		{
			// in case code was filtered
			if (!$source)
			{
				$source	= phpgw::get_var('source', 'string', 'POST');
			}

			switch($type)
			{
				case 'code':
					$path =  'activities' . '/' . $act_name . '.php';
					break;
				case 'template':
					$path = 'templates' . '/' . $act_name . '.tpl';
					break;
				default:
					$path = 'shared.php';
					break;
			}
			$complete_path = GALAXIA_PROCESSES . '/' . $proc_name . '/' . 'code' . '/' . $path;
			// In case you want to be warned when source code is changed:
			// mail('yourmail@domain.com', 'source changed', "PATH: $complete_path \n\n SOURCE: $source");
			$fp = fopen($complete_path, 'w');
			fwrite($fp, $source);
			fclose($fp);
		}

		function export()
		{
			$this->process_manager	=& CreateObject('workflow.workflow_processmanager');

			// retrieve process info
			$proc_info = $this->process_manager->get_process($this->wf_p_id);
			$filename = $proc_info['wf_normalized_name'].'.xml';
			$out = $this->process_manager->serialize_process($this->wf_p_id);
			$mimetype = 'application/xml';
			// MSIE5 and Opera show allways the document if they recognise. But we want to oblige them do download it, so we use the mimetype x-download:
			if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 5') || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera 7'))
				$mimetype = 'application/x-download';
			// Show appropiate header for a file to be downloaded:
			header("content-disposition: attachment; filename=$filename");
			header("content-type: $mimetype");
			header('content-length: ' . strlen($out));
			echo $out;
		}

		/**
		 *  get the href link for the css file, searching for themes specifics stylesheet if any
		*  * @param $css_name is the name of the css file, without the .css extension
		*  * @param $print_mode is false by default, if true '_print.css' is appended to the name if this
		*  * css print file exists
		*  * @return a string containing the link to a css file that you can use in a href, you'll have at least a link
		*  * to a non-existent css in template/default/css/
		 */
		function get_css_link($css_name, $print_mode=false)
		{
			$actual_template = $GLOBALS['phpgw_info']['server']['template_set'];
			if (!!($print_mode))
			{
				// in print mode:
				//first test template/actual_template/css/foo_print.css
				$css_file = '/'.'workflow'.'/'.'templates'.'/'.$actual_template
						.'/'.'css'.'/'.$css_name.'_print.css';
				if(file_exists(PHPGW_SERVER_ROOT.$css_file))
				{
					return $GLOBALS['phpgw_info']['server']['webserver_url'].$css_file;
				}
				else
				{
					//then test template/default/css/foo_print.css
					$css_file = '/'.'workflow'.'/'.'templates'.'/'.'default'
							.'/'.'css'.'/'.$css_name.'_print.css';
					if(file_exists(PHPGW_SERVER_ROOT.$css_file))
					{
						return $GLOBALS['phpgw_info']['server']['webserver_url'].$css_file;
					}
				}
			}
			// test template/actual_template/css/foo.css
			$css_file = '/'.'workflow'.'/'.'templates'.'/'
					.$actual_template.'/'.'css'.'/'.$css_name.'.css';
			if(file_exists(PHPGW_SERVER_ROOT.$css_file))
			{
				return $GLOBALS['phpgw_info']['server']['webserver_url'].$css_file;
			}
			else
			{
				//finally return template/default/css/foo.css without any test
				return $GLOBALS['phpgw_info']['server']['webserver_url'].'/'.'workflow'
						.'/'.'templates'.'/'.'default'.'/'.'css'.'/'.$css_name.'.css';
			}
		}

		//! return a given duration in human readable form, usefull for workitems duration
		function time_diff($to) {
			$days = (int)($to/(24*3600));
			$to = $to - ($days*(24*3600));
			$hours = (int)($to/3600);
			$to = $to - ($hours*3600);
			$min = date("i", $to);
			$to = $to - ($min*60);
			$sec = date("s", $to);

			return lang('%1 days, %2:%3:%4',$days,$hours,$min,$sec);
		}

	}
?>
