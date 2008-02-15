<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id$
	* $Source: /sources/phpgroupware/projects/inc/class.bostatistics.inc.php,v $
	*/

	class bostatistics
	{
		var $debug;
		var $start;
		var $query;
		var $order;
		var $sort;
		var $type;

		var $public_functions = array
		(
			'get_userstat_pro'	=> True,
			'get_stat_hours'	=> True,
			'get_userstat_all'	=> True,
			'get_users'			=> True,
			'get_employees'		=> True
		);

		function bostatistics()
		{
			$action				= get_var('action',array('GET'));
			$this->debug		= False;
			$this->sostatistics	= CreateObject('projects.sostatistics');
			$this->boprojects	= CreateObject('projects.boprojects',True,$action);

			$this->start		= $this->boprojects->start;
			$this->query		= $this->boprojects->query;
			$this->filter		= $this->boprojects->filter;
			$this->order		= $this->boprojects->order;
			$this->sort			= $this->boprojects->sort;
			$this->cat_id		= $this->boprojects->cat_id;

			$this->date_diff	= 0;
		}

		function read_gantt_data()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','projects_gantt');
			return explode(',',$data);
		}

		function save_gantt_data($parent_id = 0,$action = 'add')
		{
			$data = $this->read_gantt_data();

			if(is_array($data) && $parent_id > 0)
			{
				switch($action)
				{
					case 'del':
						for($i=0;$i<count($data);++$i)
						{
							if($parent_id==$data[$i])
							{
								unset($data[$i]);
							}
						}
						reset($data);
						break;
					default:
						if(!in_array($parent_id,$data))
						{
							$data[count($data)] = $parent_id;
						}
						break;
				}
			}
			else
			{
				if($parent_id > 0)
				{
					switch($action)
					{
						case 'add':
							$data = array($parent_id);
							break;
					}
				}
			}

			if(is_array($data))
			{
				$GLOBALS['phpgw']->session->appsession('session_data','projects_gantt',implode(',',$data));
			}
		}

		function get_users($type, $start, $sort, $order, $query)
		{
			$pro_employees = $this->boprojects->read_projects_acl();

			//_debug_array($pro_employees);

			if(is_array($pro_employees))
			{
				$users = $GLOBALS['phpgw']->accounts->get_list('accounts', $start, $sort, $order, $query);

				if(is_array($users))
				{
					foreach($users as $user)
					{
						if(in_array($user['account_id'],$pro_employees))
						{
							$rights[] = $user;
						}
						else
						{
							$norights[] = $user;
						}
					}
				}
				$this->total_records = ($GLOBALS['phpgw']->accounts->total - count($norights));
				return $rights;
			}
			return False;
		}

		function get_userstat_pro($account_id, $values)
		{
			return $this->sostatistics->user_stat_pro($account_id, $values);
		}

		function get_stat_hours($type, $account_id, $project_id, $values)
		{
			return $this->sostatistics->stat_hours($type, $account_id, $project_id, $values);
		}

		function get_employees($project_id, $values)
		{
			return $this->sostatistics->pro_stat_employees($project_id, $values);
		}

		function show_graph($params)
		{
			//_debug_array($params);
			$project_array	= $params['project_array'];
			$sdate			= $params['sdate'];
			$edate			= $params['edate'];
			$width			= $params['width'];
			$height			= $params['height'];
			$gantt_popup	= $params['gantt_popup'];
			$parent_array	= $params['parent_array'];
			$viewreal = (isset($params['viewtype']) && ($params['viewtype'] == 'planned')) ? false : true;

			if(!is_array($parent_array))
			{
				$parent_array = array();
			}

			$this->graph				= CreateObject('phpgwapi.gdgraph',$this->debug);

			$this->graph->graph_width	= $width?$width-50:800;
			$this->graph->graph_height	= $height?$height-100:400;

			$this->graph->title_font_size	= $gantt_popup?5:3;
			$this->graph->line_font_size	= $gantt_popup?4:2;
			$this->graph->x_font_size		= $gantt_popup?3:1;

			$this->boprojects->limit		= False;
			$this->boprojects->html_output	= False;

			$hwd = $this->boprojects->siteconfig['hwday'];

			if(is_array($project_array))
			{
				$projects = array();
				foreach($project_array as $pro)
				{
					$project = $this->boprojects->list_projects(array('function' => 'gantt','project_id' => $pro,'mstones_stat' => True,'page' => 'hours',
																	'parent_array' => $params['parent_array']));

					//_debug_array($project);

					if(is_array($project))
					{
						$i = count($projects);
						for($k=0;$k<count($project);$k++)
						{
							$projects[$i+$k] = $project[$k];
						}
					}
				}
			}

			if(is_array($projects))
			{
				$num_pro = count($projects) - 1;

				$k = 0;
				for($i=$num_pro;$i>=0;--$i)
				{
					$sopro[$k] = $projects[$i];
					$k++;
				}

				$color_legend['progress'] = array('title'	=> lang('progress'),
											'extracolor'	=> 'grey');

				foreach($sopro as $pro)
				{
					if(is_array($pro['mstones']))
					{
						$color_legend['milestone'] = array('title'	=> lang('milestone'),
													'extracolor'	=> 'yellow');
					}

					$previous = '';
					if($pro['previous'] > 0)
					{
						$previous = $this->boprojects->read_single_project($pro['previous']);
						$spro[] = array
						(
							'title'			=> str_repeat(' ',$spro['level']) . '[!]' . $previous['title'],
							'extracolor'	=> 'darkorange',
							'sdate'			=> $viewreal?$previous['sdate']:$previous['psdate'],
							'edate'			=> $viewreal?$previous['edate']:$previous['pedate'],
							'pro_id'		=> $previous['project_id'],
							'f_sdate'		=> $viewreal?$pro['sdate']:$pro['psdate']
						);

						$color_legend['previous'] = array('title'	=> '[!]' . lang('previous project'),
													'extracolor'	=> 'darkorange');
					}

					if($viewreal)
					{
						$pedate = $pro['edate']?$pro['edate']:mktime(12,0,0,date('m'),date('d'),date('Y'));
						$psdate = $pro['sdate'];
					}
					else
					{
						$pedate = $pro['pedate']?$pro['pedate']:mktime(12,0,0,date('m'),date('d'),date('Y'));
						$psdate = $pro['psdate'];
					}
					$period = $this->graph->date_format($psdate,$pedate);
					//echo 'PERIOD: ' . $period . "\n";

					if($pro['uhours_jobs'] > 0 && $pro['phours'] > 0)
					{
						$hprogress	= $pro['uhours_jobs']/$pro['phours'];
					}
					else
					{
						$hprogress = 0;
					}
					//echo 'hprogress: ' . $hprogress . "\n";
					$progress	= $hprogress*$period;
					//echo 'progress: ' . $progress . "\n";
					$progress_date = mktime(12,0,0,date('m',$pro['sdate']),date('d',$pro['sdate'])+$progress,date('Y',$pro['sdate']));


					if($this->boprojects->exists('','par','',$pro['project_id']))
					{
						$usemap = (in_array($pro['project_id'],$parent_array)?'open':'closed');
					}
					else
					{
						$usemap = 'unused';
					}

					$spro[] = array
					(
						'title'			=> $pro['title'],
						'project_id'	=> $pro['project_id'],
						'sdate'			=> $pro['sdate'],
						'edate'			=> $pedate,
						'progress'		=> $progress_date,
						'color'			=> $pro['level'],
						'pro_id'		=> $pro['project_id'],
						'previous'		=> $pro['previous'],
						'mstones'		=> $pro['mstones'],
						'use_map'		=> $usemap
					);
					//set_y_text
					$this->graph->line_captions_y[$i] = $pro['title'];

					$color_legend[$pro['level']] = array('title'	=> $pro['level']==0?lang('main project'):lang('sub project level %1',$pro['level']),
															'color'	=> $pro['level']);
				}

				$num_legend = count($color_legend);
				$k = 0;
				for($i=0;$i<$num_legend;++$i)
				{
					if(is_array($color_legend[$i]))
					{
						$color[$k] = $color_legend[$i];
						$k++;
					}
				}
				if(is_array($color_legend['previous']))
				{
					$num = count($color);
					$color[$num] = $color_legend['previous'];
				}

				if(is_array($color_legend['milestone']))
				{
					$num = count($color);
					$color[$num] = $color_legend['milestone'];
				}
				$num = count($color);
				$color[$num] = $color_legend['progress'];

				$this->graph->color_legend = $color;

				//set_x_text
				$this->graph->format_data($sdate,$edate);

				$sdate = $sdate + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
				$sdateout = $GLOBALS['phpgw']->common->show_date($sdate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

				$edate = $edate + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
				$edateout = $GLOBALS['phpgw']->common->show_date($edate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

				$this->graph->title = lang('Gantt chart from %1 to %2',$sdateout,$edateout);
				$this->graph->legend_title = lang('color legend'); 
				if(is_array($spro))
				{
					$this->graph->data = $spro;
				}

				$this->graph->num_lines_y = count($spro)+2;
				return $this->graph->render();
			}
		}
	}
?>
