<?php
  /**************************************************************************\
  * phpGroupWare - Calendar                                                  *
  * http://www.phpgroupware.org                                              *
  * Based on Webcalendar by Craig Knudsen <cknudsen@radix.net>               *
  *          http://www.radix.net/~cknudsen                                  *
  * Modified by Mark Peters <skeeter@phpgroupware.org>                       *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id$ */

	/*
	* Import required classes class
	*/
	phpgw::import_class('phpgwapi.datetime');	
	
	class uicalendar
	{
		var $template;
		var $template_dir;

		var $bo;
		var $cat;

		var $holidays;
		var $holiday_class;

		var $debug = False;
//		var $debug = True;

		var $cat_id;
		var $link_tpl;

		var $inserted_tooltip_js = false;

		// planner related variables
		var $planner_html;

		var $planner_header;
		var $planner_rows;

		var $planner_group_members;

		var $planner_firstday;
		var $planner_lastday;
		var $planner_days;

		var $planner_end_month;
		var $planner_end_year;
		var $planner_days_in_end_month; 

		var $planner_intervals = array(	// conversation hour and interval depending on intervals_per_day
					//                                  1 1 1 1 1 1 1 1 1 1 2 2 2 2
					//              0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1 2 3
						'1' => array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0), // 0=0-23h
						'2' => array(0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,0,0,0,0,0), // 0=0-12h, 1=12-23h
						'3' => array(0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,2,2,2,2,2,2), // 0=0-12h, 2=12-18h, 3=18-23h
						'4' => array(0,0,0,0,0,0,0,1,1,1,1,1,2,2,2,2,2,2,3,3,3,3,3,3)  // 0=0-7, 7-12h, 3=12-18h, 4=18-23h
					);
		var $theme;
		var $header_included;
		var $week_type;

		var $public_functions = array
			(
				'mini_calendar'	=> true,
				'index'			=> true,
				'month'			=> true,
				'get_month'		=> true,
				'week'			=> true,
				'get_week'		=> true,
				'week_new'		=> true,
				'get_week_new'	=> true,
				'year'			=> true,
				'view'			=> true,
				'edit'			=> true,
				'export'		=> true,
				'reinstate_list'=> true,
				'reinstate'		=> true,
				'add'			=> true,
				'delete'		=> true,
				'preferences'	=> true,
				'day'			=> true,
				'edit_status'	=> true,
				'set_action'	=> true,
				'planner'		=> true,
				'modify_ext_partlist' => true,
				'matrixselect'	=> true,
				'viewmatrix'	=> true,
				'search' 		=> true,
				'header'		=> true,
				'footer'		=> true,
				'participants_popup'	=> true
			);

		function __construct()
		{
			$GLOBALS['phpgw']->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			$GLOBALS['phpgw']->browser    = CreateObject('phpgwapi.browser');
			
			$this->bo = CreateObject('calendar.bocalendar',1);
			$this->cat = &$this->bo->cat;

			print_debug('BO Owner',$this->bo->owner);

			$this->template = $GLOBALS['phpgw']->template;
			$this->template_dir = $GLOBALS['phpgw']->common->get_tpl_dir('calendar');

			$this->holiday_class = 'holiday';
			
			$this->cat_id   = $this->bo->cat_id;

			$this->link_tpl = CreateObject('phpgwapi.template',$this->template_dir);
			$this->link_tpl->set_unknowns('remove');
			
			$this->link_tpl->set_file('link_picture', 'link_pict.tpl');
			
			$this->link_tpl->set_block('link_picture','link_pict','link_pict');
			$this->link_tpl->set_block('link_picture','pict','pict');
			$this->link_tpl->set_block('link_picture','link_open','link_open');
			$this->link_tpl->set_block('link_picture','link_close','link_close');
			$this->link_tpl->set_block('link_picture','link_text','link_text');

			if($this->bo->use_session)
			{
				// save return-fkt for add, view, ...
				if ( isset($GLOBALS['phpgw_info']['menuaction']) 
					&& $GLOBALS['phpgw_info']['menuaction'] )
				{
					list(,,$fkt) = explode('.',$GLOBALS['phpgw_info']['menuaction']);
					if ($fkt == 'day' || $fkt == 'week' || $fkt == 'week_new' || $fkt == 'month' || $fkt == 'year' || $fkt == 'planner')
					{
						$this->bo->return_to = array
						(
							'menuaction'	=> $GLOBALS['phpgw_info']['menuaction'],
							'date'			=> sprintf('%04d%02d%02d', $this->bo->year, $this->bo->month, $this->bo->day)
						);
					}
				}
				$this->bo->save_sessiondata();
			}
			$this->always_app_header = $this->bo->prefs['common']['template_set'] == 'idots';

			print_debug('UI',$this->_debug_sqsof());

			$this->cat_colors = $this->bo->get_cat_colors();

			if ( $this->bo->debug )
			{
				echo "<!-- Cat Colors\n" . print_r($this->cat_colors, true) . "\n-->\n";
			}
		}

		/* Public functions */

		function mini_calendar($params)
		{
			static $mini_cal_tpl;
			if(!is_array($params))
			{
				return;
			}

			if($params['month'] == 0)
			{
				$params['month'] = 12;
				$params['year'] = $params['year'] - 1;
			}
			elseif($params['month'] == 13)
			{
				$params['month'] = 1;
				$params['year'] = $params['year'] + 1;
			}

			$this->bo->store_to_cache(
				array(
					'smonth'	=> $params['month'],
					'sday'	=> 1,
					'syear'	=> $params['year']
				)
			);
			
			$params['link']			= !isset($params['link']) ? '' : $params['link'];
			$params['buttons']		= !isset($params['buttons']) ? 'none' : $params['buttons'];
			$params['outside_month']	= !isset($params['outside_month']) ? false : $params['outside_month'];

			$this->bo->read_holidays($params['year']);

			$date = phpgwapi_datetime::makegmttime(0,0,0,$params['month'],$params['day'],$params['year']);
			$month_ago = intval(date('Ymd',mktime(0,0,0,$params['month'] - 1,$params['day'],$params['year'])));
			$month_ahead = intval(date('Ymd',mktime(0,0,0,$params['month'] + 1,$params['day'],$params['year'])));
			$monthstart = intval(date('Ymd',mktime(0,0,0,$params['month'],1,$params['year'])));
			$monthend = intval(date('Ymd',mktime(0,0,0,$params['month'] + 1,0,$params['year'])));

			$weekstarttime = phpgwapi_datetime::get_weekday_start($params['year'],$params['month'],1);

			print_debug('mini_calendar:monthstart',$monthstart);
			print_debug('mini_calendar:weekstarttime',date('Ymd H:i:s',$weekstarttime));

			if(!is_object($mini_cal_tpl))
			{
				$mini_cal_tpl = CreateObject('phpgwapi.template',$this->template_dir);
				$mini_cal_tpl->set_unknowns('remove');
				$mini_cal_tpl->set_file(
					array(
						'mini_calendar'	=> 'mini_cal.tpl'
					)
				);
				$mini_cal_tpl->set_block('mini_calendar','mini_cal','mini_cal');
				$mini_cal_tpl->set_block('mini_calendar','mini_week','mini_week');
				$mini_cal_tpl->set_block('mini_calendar','mini_day','mini_day');
			}


			$month = '<a href="' . $this->page('month',array('month'=>$GLOBALS['phpgw']->common->show_date($date['raw'],'m'),'year'=>$GLOBALS['phpgw']->common->show_date($date['raw'],'Y'))). '" class="minicalendar">' . lang($GLOBALS['phpgw']->common->show_date($date['raw'],'F')).' '.$GLOBALS['phpgw']->common->show_date($date['raw'],'Y').'</a>';

			$var = array(
				'cal_img_root'		=>	$GLOBALS['phpgw']->common->image('calendar','mini-calendar-bar'),
				'bgcolor'			=>	$this->theme['bg_color'],
				'bgcolor1'			=>	$this->theme['bg_color'],
				'month'				=>	$month,
				'bgcolor2'			=>	$this->theme['cal_dayview'],
				'holiday_class'	=> $this->holiday_class
			);

			$mini_cal_tpl->set_var($var);

			switch(strtolower($params['buttons']))
			{
				case 'right':
					$var = array(
						'nextmonth'			=>	'<a href="'.$this->page('month',array('date'=>$month_ahead)).'"><img src="'.$GLOBALS['phpgw']->common->image('phpgwapi','right').'"></a>'
					);
					break;
				case 'left':
					$var = array(
						'prevmonth'			=>	'<a href="'.$this->page('month',array('date'=>$month_ago)).'"><img src="'.$GLOBALS['phpgw']->common->image('phpgwapi','left').'"></a>'
					);					
					break;
				case 'both':
					$var = array(
						'prevmonth'			=>	'<a href="'.$this->page('month',array('date'=>$month_ago)).'"><img src="'.$GLOBALS['phpgw']->common->image('phpgwapi','left').'"></a>',
						'nextmonth'			=>	'<a href="'.$this->page('month',array('date'=>$month_ahead)).'"><img src="'.$GLOBALS['phpgw']->common->image('phpgwapi','right').'"></a>'
					);
					break;
				case 'none':
				default:
					$var = array(
						'prevmonth'			=>	'',
						'nextmonth'			=>	''
					);
					break;
			}
			$mini_cal_tpl->set_var($var);

			if(!$mini_cal_tpl->get_var('daynames'))
			{
				for($i=0;$i<7;$i++)
				{
					$var = array(
						'dayname'	=> '<b>' . lang(phpgwapi_datetime::$dow_shortnames[$i]) . '</b>',
						'day_image'	=> ''
					);
					$this->output_template_array($mini_cal_tpl,'daynames','mini_day',$var);
				}
			}
			$today = date('Ymd', phpgwapi_datetime::user_localtime());
			$user_timezone = phpgwapi_datetime::user_timezone();
			unset($date);
			for($i=$weekstarttime + $user_timezone;date('Ymd',$i)<=$monthend;$i += (24 * 3600 * 7))
			{
				unset($var);
//				$daily = $this->set_week_array($i - $user_timezone,$cellcolor,$weekly); // $cellcolor and $weekly not set
				$daily = $this->set_week_array($i - $user_timezone,false,false);
				foreach($daily as $date => $day_params)
				{
					print_debug('Mini-Cal Date',$date);
					$year = intval(substr($date,0,4));
					$month = intval(substr($date,4,2));
					$day = intval(substr($date,6,2));
					$str = '';
					if(($date >= $monthstart && $date <= $monthend) || $params['outside_month'] == True)
					{
						$str = '<a href="'.$this->page($params['link'],array('date'=>$date)).'" class="'.$day_params['class'].'">'.$day.'</a>';
					}
					else
					{
						$day_params['day_image'] = '';
					}
					$var[] = array(
						'day_image'	=> $day_params['day_image'],
						'dayname'	=> $str
					);
				}
				for($l=0;$l<count($var);$l++)
				{
					$this->output_template_array($mini_cal_tpl,'monthweek_day','mini_day',$var[$l]);
				}
				$mini_cal_tpl->parse('display_monthweek','mini_week',True);
				$mini_cal_tpl->set_var('dayname','');
				$mini_cal_tpl->set_var('monthweek_day','');
			}
		
			$return_value = $mini_cal_tpl->fp('out','mini_cal');
			$mini_cal_tpl->set_var('display_monthweek','');
//			$mini_cal_tpl->set_var('daynames','');
//			unset($p);
			return $return_value;
		}

		function index($params='')
		{
			$GLOBALS['phpgw']->redirect($this->page('', $params, true));
		}

		//TODO Replace this with better CSS driven print mode
		function printer_friendly($body, $app_header='')
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			unset($GLOBALS['phpgw_info']['flags']['noappheader']);
			unset($GLOBALS['phpgw_info']['flags']['noappfooter']);
			if ($app_header && $this->always_app_header)
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['calendar']['title'].' - '.$app_header;
			}
			$this->header();
			//$GLOBALS['phpgw']->common->phpgw_header();
			$new_body = $this->bo->debug_string.$body;
			return $new_body;
		}

		function month()
		{
			echo $this->printer_friendly($this->get_month(),lang('Monthview'));
		}

		function get_month()
		{
			$m = mktime(0,0,0,$this->bo->month,1,$this->bo->year);

			$minical_prev = $this->mini_calendar(
				array(
					'day'	=> 1,
					'month'	=> $this->bo->month - 1,
					'year'	=> $this->bo->year,
					'link'	=> 'day'
				)
			);
			
			$minical_next = $this->mini_calendar(
				array(
					'day'	=> 1,
					'month'	=> $this->bo->month + 1,
					'year'	=> $this->bo->year,
					'link'	=> 'day'
				)
			);
			
			$param = array('year'=>$this->bo->year,'month'=>$this->bo->month,'friendly'=>1);

			$this->bo->read_holidays();

			$var = array(
				'small_calendar_prev'	=> $minical_prev,
				'month_identifier'		=> lang(strftime("%B",$m)).' '.$this->bo->year,
				'username'				=> ( $this->bo->is_group
											? $GLOBALS['phpgw']->common->grab_owner_name($this->bo->owner)
											: $this->bo->contacts->get_name_of_person_id($this->bo->owner) ),
				'small_calendar_next'	=> $minical_next,
				'large_month'			=> $this->display_month($this->bo->month,$this->bo->year,True,$this->bo->owner),
			);

			$p = CreateObject('phpgwapi.template',$this->template_dir);
			$p->set_unknowns('remove');
			$p->set_file(
				array(
					'index_t'	=>	'index.tpl'
				)
			);
			
			$var = $this->insert_tooltip_js($var);
			
			$p->set_var($var);
			return $p->fp('out','index_t');
		}

		function week()
		{
			$this->week_type = 'old';
			echo $this->printer_friendly($this->get_week(),lang('Weekview'));
		}

		function get_week()
		{
			$week_type_name = ($this->week_type == 'new') ? 'week_new' : 'week';
			
			$this->bo->read_holidays();

			$next = phpgwapi_datetime::makegmttime(0,0,0,$this->bo->month,$this->bo->day + 7,$this->bo->year);
			$prev = phpgwapi_datetime::makegmttime(0,0,0,$this->bo->month,$this->bo->day - 7,$this->bo->year);

			$minical_this = $this->mini_calendar(array
			(
				'day'	=> $this->bo->day,
				'month'	=> $this->bo->month,
				'year'	=> $this->bo->year,
				'link'	=> 'day',
				'butons'	=> 'none',
				'outside_month'	=> False
			));

			$minical_prev = $this->mini_calendar(array
			(
				'day'	=> $this->bo->day,
				'month'	=> $this->bo->month - 1,
				'year'	=> $this->bo->year,
				'link'	=> 'day',
				'butons'	=> 'left',
				'outside_month'	=> False
			));

			$minical_next = $this->mini_calendar(array
			(
				'day'	=> $this->bo->day,
				'month'	=> $this->bo->month + 1,
				'year'	=> $this->bo->year,
				'link'	=> 'day',
				'butons'	=> 'right',
				'outside_month'	=> False
			));
			

			$param = array('friendly'=>1,'date'=>sprintf("%04d%02d%02d",$this->bo->year,$this->bo->month,$this->bo->day));
			$prev_week_link = '<a href="'. $this->page($week_type_name,array('date'=>$prev['full'])).'">&lt;&lt;</a>';
			$next_week_link = '<a href="'. $this->page($week_type_name,array('date'=>$next['full'])).'">&gt;&gt;</a>';

			$var = array(
				'small_calendar_prev'	=> $minical_prev,
				'prev_week_link'		=> $prev_week_link,
				'small_calendar_this'	=> $minical_this,
				'week_identifier'		=> $this->bo->get_week_label(),
				'next_week_link'		=> $next_week_link,
				'username'				=> ( $this->bo->is_group
											? $GLOBALS['phpgw']->common->grab_owner_name($this->bo->owner)
											: $this->bo->contacts->get_name_of_person_id($this->bo->owner) ),
				'small_calendar_next'	=> $minical_next,
				'week_display'			=> $this->display_weekly(
					array(
						'date'			=> sprintf("%04d%02d%02d",$this->bo->year,$this->bo->month,$this->bo->day),
						'showyear'		=> true,
						'owners'		=> $this->bo->owner
					)
				),
			);

			$p = CreateObject('phpgwapi.template',$this->template_dir);
			$p->set_file('week_t', 'week.tpl');

			$var = $this->insert_tooltip_js($var);

			$p->set_var($var);
			return $p->fp('out','week_t');
		}

		function week_new()
		{
			$this->week_type = 'new';
			echo $this->printer_friendly($this->get_week(), lang('Weekview'));
		}

		function get_week_new()
		{
			$this->week_type = 'new';
			return $this->get_week();
		}

		function year()
		{
			echo $this->printer_friendly($this->get_year(),lang('Yearview'));
		}

		function get_year()
		{

			$param = array('friendly'=>1,'year'=>$this->bo->year);
			$left_link = '<a href="'.$this->page('year',array('year'=>($this->bo->year - 1))).'">&lt;&lt;</a>';
			$right_link = '<a href="'.$this->page('year',array('year'=>($this->bo->year + 1))).'">&gt;&gt;</a>';
			$link = 'day';

			$var = array
			(
				'left_link' => $left_link,
				'year_text' => $this->bo->year,
				'right_link'=> $right_link,
			);

			$p = CreateObject('phpgwapi.template',$this->template_dir);
			$p->set_file(
				array(
					'year_t' => 'year.tpl'
				)
			);
			$p->set_block('year_t','year','year');
			$p->set_block('year_t','month','month_handle');
			$p->set_block('year_t','month_sep','month_sep_handle');
			$p->set_var($var);

			for($i=1;$i<=12;$i++)
			{
				if(($i % 3) == 1)
				{
					$p->parse('row','month_sep',True);
				}
				$p->set_var('mini_month',$this->mini_calendar(
						array(
							'day'	=> 1,
							'month'	=> $i,
							'year'	=> $this->bo->year,
							'link'	=> $link,
							'buttons'	=> 'none',
							'outside_month'	=> False
						)
					)
				);
				$p->parse('row','month',True);
				$p->set_var('mini_month','');
			}
			return $p->fp('out','year_t');
		}
		
		function view($vcal_id=0, $cal_date = 0)
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['calendar']['title'].' - '.lang('View');
			$this->header(false);

			$cal_id = phpgw::get_var('cal_id', 'int', 'REQUEST', $vcal_id);

			if ( $cal_date )
			{
				$date = $cal_date;
			}
			else
			{
				$date = phpgw::get_var('date', 'int', 'GET');
			}

			// First, make sure they have permission to this entry
			if ($cal_id < 1)
			{
				echo '<div class="err">'.lang('Invalid entry id.') . "</div>\n";
				$GLOBALS['phpgw']->common->phpgw_exit(True);
			}

			if(!$this->bo->check_perms(PHPGW_ACL_READ,$cal_id))
			{
				echo '<div class="err">'.lang('You do not have permission to read this record!') . "</div>\n";
				$GLOBALS['phpgw']->common->phpgw_exit(True);
			}

			$event = $this->bo->read_entry($cal_id);

			if(!isset($event['id']))
			{
				echo '<div class="err">'.lang('Sorry, this event does not exist') . ".</div>\n";
				$GLOBALS['phpgw']->common->phpgw_exit(True);
			}

			$this->bo->repeating_events = array();
			$this->bo->cached_events = array();
			$this->bo->repeating_events[0] = $event;
			$datetime = mktime(0,0,0,$this->bo->month,$this->bo->day,$this->bo->year) - phpgwapi_datetime::user_timezone();
			$this->bo->check_repeating_events($datetime);
			$check_date = $GLOBALS['phpgw']->common->show_date($datetime,'Ymd');
			if(isset($this->bo->cached_events[$check_date][0])
				&& is_array($this->bo->cached_events[$check_date][0])
				&& $this->bo->cached_events[$check_date][0]['id'] == $event['id'])
			{
				$starttime = $this->bo->maketime($event['start']);
				$endtime = $this->bo->maketime($event['end']);
				$event['start']['month'] = $this->bo->month;
				$event['start']['mday'] = $this->bo->day;
				$event['start']['year'] = $this->bo->year;
				$temp_end =  $this->bo->maketime($event['start']) + ($endtime - $starttime);
				$event['end']['month'] = date('m',$temp_end);
				$event['end']['mday'] = date('d',$temp_end);
				$event['end']['year'] = date('Y',$temp_end);
			}

			if(!$this->view_event($event,True))
			{
				echo '<div class="err">'.lang('You do not have permission to read this record!') . "<div>\n";
				$GLOBALS['phpgw']->common->phpgw_exit(True);
			}

			$p = $GLOBALS['phpgw']->template;
			$p->set_file(
				array(
					'form_button'	=> 'form_button_script.tpl'
				)
			);

			$button_left = $button_center = $button_right = '';

			if($this->bo->check_perms(PHPGW_ACL_EDIT,$event))
			{
				if($event['recur_type'] != MCAL_RECUR_NONE)
				{
					$var = array(
						'action_url_button'	=> $this->page('edit',array('cal_id'=>$cal_id)),
						'action_text_button'	=> lang('Edit Single'),
						'action_confirm_button'	=> '',
						'action_extra_field'	=> '<input type="hidden" name="edit_type" value="single">'."\n"
							. '<input type="hidden" name="date" value="'.sprintf('%04d%02d%02d',$this->bo->year,$this->bo->month,$this->bo->day).'">'
					);
					$p->set_var($var);
					$button_left .= '<td>'.$p->fp('button','form_button').'</td>';

					$var = array(
						'action_url_button'	=> $this->page('edit',array('cal_id'=>$cal_id)),
						'action_text_button'	=> lang('Edit Series'),
						'action_confirm_button'	=> '',
						'action_extra_field'	=> '<input type="hidden" name="edit_type" value="series">'
					);
					$p->set_var($var);
					$button_left .= '<td>'.$p->fp('button','form_button').'</td>';
				}
				else
				{
					$var = array(
						'action_url_button'	=> $this->page('edit',array('cal_id'=>$cal_id)),
						'action_text_button'	=> lang('Edit'),
						'action_confirm_button'	=> '',
						'action_extra_field'	=> ''
					);
					$p->set_var($var);
					$button_left .= '<td>'.$p->fp('button','form_button').'</td>';
				}

				$var = array(
					'action_url_button'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'calendar.uialarm.manager')),
					'action_text_button'	=> lang('Alarm Management'),
					'action_confirm_button'	=> '',
					'action_extra_field'	=> '<input type="hidden" name="cal_id" value="'.$cal_id.'">'
				);
				$p->set_var($var);
				$button_center .= '<td>'.$p->fp('button','form_button').'</td>';
			}

			if ($this->bo->check_perms(PHPGW_ACL_DELETE,$event))
			{
				if($event['recur_type'] != MCAL_RECUR_NONE)
				{
					$var = array(
						'action_url_button'	=> $this->page('delete', array('cal_id' => $cal_id)),
						'action_text_button'	=> lang('Delete Single'),
						'action_confirm_button'	=> "onClick=\"return confirm('".lang("Are you sure\\nyou want to\\ndelete this single occurence ?\\n\\nThis will delete\\nthis entry for all users.")."')\"",
						'action_extra_field'	=> '<input type="hidden" name="delete_type" value="single">'
							. '<input type="hidden" name="date" value="'.sprintf('%04d%02d%02d',$this->bo->year,$this->bo->month,$this->bo->day).'">'
					);
					$p->set_var($var);
					$button_right .= '<td>'.$p->fp('button','form_button').'</td>';

					$var = array(
						'action_url_button'	=> $this->page('delete', array('cal_id' => $cal_id)),
						'action_text_button'	=> lang('Delete Series'),
						'action_confirm_button'	=> "onClick=\"return confirm('".lang("Are you sure\\nyou want to\\ndelete this entry ?\\n\\nThis will delete\\nthis entry for all users.")."')\"",
						'action_extra_field'	=> '<input type="hidden" name="delete_type" value="series">'
					);
					$p->set_var($var);
					$button_right .= '<td>'.$p->fp('button','form_button').'</td>';

					if($event['recur_exception'])
					{
						$var = array(
							'action_url_button'	=> $this->page('reinstate_list',array('cal_id' => $cal_id)),
							'action_text_button'	=> lang('Reinstate'),
							'action_confirm_button'	=> '',
							'action_extra_field'	=> ''
						);
						$p->set_var($var);
						$button_center .= '<td>'.$p->fp('button','form_button').'</td>';
					}
				}
				else
				{
					$var = array(
						'action_url_button'	=> $this->page('delete',array('cal_id' => $cal_id)), 
						'action_text_button'	=> lang('Delete'),
						'action_confirm_button'	=> "onClick=\"return confirm('".lang("Are you sure\\nyou want to\\ndelete this entry ?\\n\\nThis will delete\\nthis entry for all users.")."')\"",
						'action_extra_field'	=> ''
					);
					$p->set_var($var);
					$button_right .= '<td>'.$p->fp('button','form_button').'</td>';
				}
			}
			else
			{
				// allow me (who I am logged in as) to set up an alarm
				// if I am a participant, but not the owner
				//reset($event['participants']);
				//while (list($user,$short_status) = each($event['participants']))
                                if (is_array($event['participants']))
                                {
                                    foreach($event['participants'] as $user => $short_status)
				{
					if ($GLOBALS['phpgw_info']['user']['account_id'] == $user)
					{
						$var = array(
							'action_url_button'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'calendar.uialarm.manager')),
							'action_text_button'	=> lang('Alarm Management'),
							'action_confirm_button'	=> '',
							'action_extra_field'	=> '<input type="hidden" name="cal_id" value="'.$cal_id.'">'
						);
						$p->set_var($var);
						echo $p->fp('out','form_button');
					}
				}
			}
			}

			$var = array(
				'action_url_button'	=> $this->page('export'),
				'action_text_button'	=> lang('Export'),
				'action_confirm_button'	=> '',
				'action_extra_field'	=> '<input type="hidden" name="cal_id" value="'.$cal_id.'">'
			);
			$p->set_var($var);
			$button_center .= '<td>'.$p->fp('button','form_button').'</td>';

			if ($this->bo->return_to)
			{
				$var = array(
					'action_url_button'	=> $GLOBALS['phpgw']->link('/index.php', $this->bo->return_to),
					'action_text_button'	=> lang('Done'),
					'action_confirm_button'	=> '',
					'action_extra_field'	=> ''
				);
				$p->set_var($var);
				$button_left .= '<td>'.$p->fp('button','form_button').'</td>';
			}
			$p->set_var(array(
				'button_left'	=> $button_left,
				'button_center'	=> $button_center,
				'button_right'	=> $button_right
			));
			$p->pfp('phpgw_body','view_event');
			
			$GLOBALS['phpgw']->hooks->process(array
			(
				'location' => 'calendar_view',
				'cal_id'   => $cal_id
			));
		}

		function edit($params='')
		{
			if($this->debug)
			{
				echo '<!-- params[readsess] = '.$params['readsess'].' -->'."\n";
				echo '<!-- params[cd] = '.$params['cd'].' -->'."\n";
			}

			$readsess = phpgw::get_var('readsess', 'bool', 'GET');

			if ( $readsess )
			{
				$params['readsess'] = $readsess;
				$params['cd'] = 0;
			}

			if($this->debug)
			{
				echo '<!-- params[readsess] = '.$params['readsess'].' -->'."\n";
				echo '<!-- params[cd] = '.$params['cd'].' -->'."\n";
			}

			if ( $params && is_array($params) && $readsess )
			{
				$can_edit = True;
				$this->edit_form(array
				(
					'event' => $this->bo->restore_from_appsession(),
					'cd' => $params['cd']
				));
			}
			else if ( $cal_id = phpgw::get_var('cal_id', 'int', 'GET') )
			{
				$event = $this->bo->read_entry($cal_id);

				if(!$this->bo->check_perms(PHPGW_ACL_EDIT,$event))
				{
					//FIXME
					Header('Location: '.$this->page('view', array('cal_id' => $cal_id), true) );
					$GLOBALS['phpgw']->common->phpgw_exit();
				}

				$edit_type = phpgw::get_var('edit_type', 'string', 'POST');
				if ( $edit_type == 'single' )
				{
					$event['id'] = 0;
					$this->bo->set_recur_date($event, phpgw::get_var('date', 'int', 'POST') );
					$event['recur_type'] = MCAL_RECUR_NONE;
					$event['recur_interval'] = 0;
					$event['recur_data'] = 0;
					$event['recur_enddate']['month'] = 0;
					$event['recur_enddate']['mday'] = 0;
					$event['recur_enddate']['year'] = 0;
					$event['recur_exception'] = array();
				}
				$this->edit_form(
					array(
						'event' => $event,
						'cd'	=> (isset($cd)?$cd:'') //not set - missing something ?
					)
				);
			}
 		}

		function export($vcal_id=0)
		{
			$cal_id = phpgw::get_var('cal_id', 'int', 'POST');
			if( !$cal_id )
			{
				//FIXME
				Header('Location: ' . $this->index());
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
			
			$browser = createObject('phpgwapi.browser');
			$browser->content_header("phpgw-cal-{$cal_id}.ics", 'text/calendar');
			$content = ExecMethod('calendar.boicalendar.export', array
			(
				'l_event_id'	=> $cal_id,
				'chunk_split'	=> False,
			));
			echo $content;
			exit;
		}

		function reinstate_list($params='')
		{
			if(!$this->bo->check_perms(PHPGW_ACL_EDIT))
			{
			   $this->no_edit();
			}
			elseif(!$this->bo->check_perms(PHPGW_ACL_ADD))
			{
				$this->index();
			}

			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['calendar']['title'].' - '.lang('Reinstate');
			//$GLOBALS['phpgw']->common->phpgw_header();
			$this->header();

			$cal_id = phpgw::get_var('cal_id', 'int', 'GET', $params['cal_id']);

			if ($cal_id < 1)
			{
				echo '<div class="err">'.lang('Invalid entry id.') . "</div>\n";
				$GLOBALS['phpgw']->common->phpgw_exit(True);
			}

			if(!$this->bo->check_perms(PHPGW_ACL_READ))
			{
				echo '<center>'.lang('You do not have permission to read this record!').'</center>'."\n";
				$GLOBALS['phpgw']->common->phpgw_exit(True);
			}

			$event = $this->bo->read_entry($cal_id);

			if(!isset($event['id']))
			{
				echo '<center>'.lang('Sorry, this event does not exist').'.'.'</center>'."\n";
				$GLOBALS['phpgw']->common->phpgw_exit(True);
			}
			elseif(!isset($event['recur_exception']))
			{
				echo '<center>'.lang('Sorry, this event does not have exceptions defined').'.'.'</center>'."\n";
				$GLOBALS['phpgw']->common->phpgw_exit(True);
			}

			if(!$this->view_event($event,True))
			{
				echo '<center>'.lang('You do not have permission to read this record!').'</center>';
				$GLOBALS['phpgw']->common->phpgw_exit(True);
			}

			$p = &$GLOBALS['phpgw']->template;
			$p->set_file(
				array(
					'form_button'	=> 'form_button_script.tpl'
				)
			);

			$str = '';

			for($i=0;$i<count($event['recur_exception']);$i++)
			{
				$str .= '    <option value="'.$i.'">'.$GLOBALS['phpgw']->common->show_date($event['recur_exception'][$i]).'</option>'."\n";
			}
			$this->output_template_array($p,'row','list',array(
				'field'	=> lang('Exceptions'),
				'data'	=> '<select name="reinstate_index[]" multiple size="5">'."\n".$str.'</select>'
			));

			$var = array(
				'action_url_button'	=> $this->page('reinstate',array('cal_id'=>$cal_id)),
				'action_text_button'	=> lang('Reinstate'),
				'action_confirm_button'	=> '',
				'action_extra_field'	=> ''
			);
			$p->set_var($var);
			$button_left = '<td>'.$p->fp('out','form_button').'</td>';

			$var = array(
				'action_url_button'		=> $this->bo->return_to ? $GLOBALS['phpgw']->link('/index.php', $this->bo->return_to) : $this->page(''),
				'action_text_button'	=> lang('Cancel'),
				'action_confirm_button'	=> '',
				'action_extra_field'	=> ''
			);
			$p->set_var($var);
			$button_left .= '<td>'.$p->fp('out','form_button').'</td>';
			
			$p->set_var('button_left',$button_left);
			$p->pfp('phpgw_body','view_event');
		}

		function reinstate($params='')
		{
			if(!$this->bo->check_perms(PHPGW_ACL_EDIT))
			{
			   $this->no_edit();
			}
			elseif(!$this->bo->check_perms(PHPGW_ACL_ADD))
			{
				$this->index();
			}

			if ( isset($params['cal_id']) && $params['cal_id'] )
			{
				$cal_id = (int) $params['cal_id'];
			}
			else
			{
				$cal_id = phpgw::get_var('cal_id', 'int', 'GET');
			}

			$reinstate_index = 0;
			if ( isset($params['reinstate_index']) && $params['reinstate_index'] )
			{
				$reinstate_index = (int) $params['reinstate_index'];
			}
			else if ( $ri = phpgw::get_var('reinstate_index', 'int', 'POST') )
			{
				$reinstate_index = $ri;
				unset($ri);
			}
			if($this->debug)
			{
				echo '<!-- Calling bo->reinstate -->'."\n";
			}
			$cd = $this->bo->reinstate(
				array(
					'cal_id'	=> $cal_id,
					'reinstate_index'	=> $reinstate_index
				)
			);
			if($this->debug)
			{
				echo '<!-- Return Value = '.$cd.' -->'."\n";
			}
			if ($this->bo->return_to)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', $this->bo->return_to);
			}			
			else
			{
				//FIXME
				Header('Location: ' . $this->page('', ($cd ? array('cd' => $cd) : array()), true) );
			}
			$GLOBALS['phpgw']->common->phpgw_exit();	
		}

		function add($cd=0,$readsess=0)
		{
			if(!$this->bo->check_perms(PHPGW_ACL_ADD))
			{
				$this->index();
			}
			
			if($readsess)
			{
				$event = $this->bo->restore_from_appsession;
				if(!$event['owner'])
				{
					$this->bo->add_attribute('owner',$this->bo->owner);
				}
				$can_edit = True;
			}
			else
			{
				$this->bo->event_init();
				$this->bo->add_attribute('id',0);

				$can_edit = True;

				$starthour = phpgw::get_var('hour', 'int', 'GET', $this->bo->prefs['calendar']['workdaystarts']);
				$startmin  = phpgw::get_var('minute', 'int', 'GET');
				$endmin    = $startmin + intval($this->bo->prefs['calendar']['defaultlength']);
	//			$endhour   = $starthour + $this->bo->normalizeminutes($endmin);
				$end_time = $this->bo->normalizeminutes($endmin);
				$endhour   = $starthour + $end_time['hour'];
				unset($end_time);

				$this->bo->set_start($this->bo->year,$this->bo->month,$this->bo->day,$starthour,$startmin,0);
				$this->bo->set_end($this->bo->year,$this->bo->month,$this->bo->day,$endhour,$endmin,0);
				$this->bo->set_title('');
				$this->bo->set_description('');
				$this->bo->add_attribute('location','');
				$this->bo->add_attribute('priority',2);
				if(@$this->bo->prefs['calendar']['default_private'])
				{
					$this->bo->set_class(False);
				}
				else
				{
					$this->bo->set_class(True);
				}
				$this->bo->add_attribute('participants','A',$this->bo->owner);
				$this->bo->set_recur_none();
				$event = $this->bo->get_cached_event();
			}
			$this->edit_form(
				array(
					'event' => $event,
					'cd' => $cd
				)
			);
		}

		function delete()
		{
			if ( phpgw::get_var('cal_id', 'int', 'GET') )
			{
				//FIXME
				Header('Location: '.$this->page('', array('date' => sprintf('%04d%02d%02d', $this->bo->year, $this->bo->month, $this->bo->day) ), true) );
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			$date = sprintf('%04d%02d%02d', $this->bo->year, $this->bo->month, $this->bo->day);
			$cal_id = phpgw::get_var('cal_id', 'int', 'GET');
			if ( $this->bo->check_perms(PHPGW_ACL_DELETE, $cal_id) )
			{
				$delete_type = phpgw::get_var('delete_type', 'string', 'POST');
				if ( $delete_type == 'single' )
				{
					$date = phpgw::get_var('date', 'int', 'POST');
					$cd = $this->bo->delete_single(array
					(
						'id'	=> $cal_id,
						'year'	=> substr($date,0,4),
						'month'	=> substr($date,4,2),
						'day'	=> substr($date,6,2)
					));
				}

				else if ( !$delete_type || $delete_type = 'series' )
				{
					$cd = $this->bo->delete_entry($cal_id);
					$this->bo->expunge();
				}
			}
			else
			{
				$cd = '0';
			}
			if ($this->bo->return_to)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', $this->bo->return_to );
			}
			else
			{
				//FIXME
				Header('Location: ' . $this->page('', array('date' => $date, 'cd' => ($cd ? $cd : '0')), true));
			}
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function day()
		{
			$this->bo->read_holidays();
			
			$minical = $this->mini_calendar(array
			(
				'day'	=> $this->bo->day,
				'month'	=> $this->bo->month,
				'year'	=> $this->bo->year,
				'link'	=> 'day'
			));
			
			$now	= phpgwapi_datetime::makegmttime(0, 0, 0, $this->bo->month, $this->bo->day, $this->bo->year);
			$now['raw'] += phpgwapi_datetime::user_timezone();

			$p = $GLOBALS['phpgw']->template;
			$p->set_root(PHPGW_APP_TPL);
			$p->set_file('day_t', 'day.tpl');

			$p->set_block('day_t','day','day');
			$p->set_block('day_t','day_event','day_event');

			$param = array('date'=>sprintf("%04d%02d%02d",$this->bo->year,$this->bo->month,$this->bo->day),'friendly'=>1);

			$var = array
			(
				'daily_events'		=> $this->print_day(
								array
								(
									'year'	=> $this->bo->year,
									'month'	=> $this->bo->month,
									'day'	=> $this->bo->day
								)),
				'small_calendar'	=> $minical,
				'date'			=> $this->bo->long_date($now),
				'username'		=> ( $this->bo->is_group ? $GLOBALS['phpgw']->common->grab_owner_name($this->bo->owner) : $this->bo->contacts->get_name_of_person_id($this->bo->owner) ),
			);

			if ( $this->bo->debug )
			{
				echo "\n<!-- Daily Events\n" . print_r($var['daily_events'], true) . "\n-->\n";
			}
			
			$var = $this->insert_tooltip_js($var);
			
			$p->set_var($var);

			if ($this->week_type != 'new')
			{
				$p->parse('day_events','day_event');
				echo $this->printer_friendly($p->fp('out','day'),lang('Dayview'));
			}
			elseif ($this->week_type == 'new')
			{
				return $var['daily_events'];
			}
		}

		function edit_status()
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['calendar']['title'].' - '.lang('Change Status');
			//$GLOBALS['phpgw']->common->phpgw_header();
			$this->header(false);
			
			$cal_id = phpgw::get_var('cal_id', 'int', 'GET');
			$event = $this->bo->read_entry($cal_id);

			reset($event['participants']);

			if(!$event['participants'][$this->bo->owner])
			{
				echo '<center>'.lang('The user %1 is not participating in this event!',$GLOBALS['phpgw']->common->grab_owner_name($this->bo->owner)).'</center>';
				return;
			}

			if(!$this->bo->check_perms(PHPGW_ACL_EDIT))
			{
			   $this->no_edit();
			   return;
			}

			$freetime = phpgwapi_datetime::localdates(mktime(0,0,0,$event['start']['month'],$event['start']['mday'],$event['start']['year']) - phpgwapi_datetime::user_timezone());
			echo $this->timematrix(
				array(
					'date'		=> $freetime,
					'starttime'	=> $this->bo->splittime('000000',False),
					'endtime'	=> 0,
					'participants'	=> $event['participants']
				)
			).'<br />';

			$event = $this->bo->read_entry($cal_id);
			$this->view_event($event);
			$GLOBALS['phpgw']->template->pfp('phpgw_body','view_event');

			echo $this->get_response($event['id']);
		}

		function set_action()
		{
			if(!$this->bo->check_perms(PHPGW_ACL_EDIT))
			{
				$this->no_edit();
				return;
			}
			$cal_id = phpgw::get_var('cal_id', 'int', 'GET');
			$action = phpgw::get_var('action', 'int', 'GET');
			$this->bo->set_status($cal_id, $action);

			if ($this->bo->return_to)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', $this->bo->return_to);
			}
			else
			{
				//FIXME
				Header('Location: ' . $this->page('','', true)); 
			}
			$GLOBALS['phpgw']->common->phpgw_exit();
		}


		function planner()
		{
			$home = strstr($_SERVER['PHP_SELF'],'home') !== False;
			// generate header and set global/member variables
			//
			$this->planner_prepare($home);

			// process events within selected interval
			//
			$this->planner_process_interval();

			// generate the planner view
			//
			if (!$home)
			{
				echo '<p>'.$this->planner_print_rows();
			}
			else
			{
				return $this->planner_print_rows();
			}
		}

		function set_planner_group_members()
		{
			$this->planner_group_members = array();
			if (is_array($this->bo->g_owner) && count($this->bo->g_owner) ) // display schedule of all group members
			{
				foreach ( $this->bo->g_owner as $id)
				{
					if ($this->bo->check_perms(PHPGW_ACL_READ,0,$id))
					{
						$this->planner_group_members[$this->bo->contacts->get_name_of_person_id($id)] = $id;
					}
				}
				ksort($this->planner_group_members);
			}
			else
			{
				$this->planner_group_members[$this->bo->contacts->get_name_of_person_id($this->bo->owner)] = $this->bo->owner;
			}
		}

		/**
		 * planner_prepare - prepare the planner view
		 *
		 * - sets global environment variables
		 * - initializes class member variables used in multiple planner related functions
		 * - generates header lines for the planner view (month, calendar week, days)
		 */
		function planner_prepare($no_header = False)
		{
			// set some globals
			//
			if (!$no_header)
			{
				unset($GLOBALS['phpgw_info']['flags']['noheader']);
				unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
				if ($this->always_app_header)
				{
					$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['calendar']['title'].' - '.lang('Group Planner');
				}
				//$GLOBALS['phpgw']->common->phpgw_header();
				$this->header();
			}

			// intervals_per_day can be configured in preferences now :-)
			//
			if (! $this->bo->prefs['calendar']['planner_intervals_per_day'])
			{
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add('calendar','planner_intervals_per_day',3);
				$GLOBALS['phpgw']->preferences->save_repository();
				$this->bo->prefs['calendar']['planner_intervals_per_day'] = 3;
			}
			$intervals_per_day = $this->bo->prefs['calendar']['planner_intervals_per_day'];
			$this->bo->save_sessiondata();	// need to save $this->bo->save_owner

			// set title for table and rows of planner view
			//
			if ($this->bo->sortby == 'category')
			{
				$title = lang('Category');
			}
			else
			{
				$title = lang('User');

				$this->set_planner_group_members();
			}

			// create/initialize variables directly used for HTML code generation
			//
			$this->planner_html   = CreateObject('calendar.html');
			$this->planner_header = array();
			$this->planner_rows   = array();

			// generate header lines with days and associated months
			//
			$hdr = &$this->planner_header;
			$hdr[0]['0']  = $title;
			$hdr[0]['.0'] = 'rowspan="3"';

			$this->planner_days = 0; // reset

			$m = $this->bo->month;
			$y = $this->bo->year;
			$this->bo->read_holidays($y);
			for ($i=1; $i<=$this->bo->num_months; $i++,$m++)
			{
				if ($m == 13)
				{
					$m = 1; $y++; // "wrap-around" into new year
					$this->bo->read_holidays($y);
				}
				$days = phpgwapi_datetime::days_in_month($m,$y);

				$d     = mktime(0,0,0,$m,1,$y);
				$month = lang(date('F', $d)).strftime(' %Y', $d);
				$cssclass = $m % 2 ? 'row_on' : 'row_off';
				$cols  = $days * $intervals_per_day;

				$hdr[0]['.'.$i] = 'classr="'.$cssclass.'" colspan="'.$cols.'" align ="center"';
				$prev_month = sprintf('%04d%02d01',$y-($m==1),$m > 1?$m-1:12);
				$next_month = sprintf('%04d%02d01',$y+($m==12),$m < 12?$m+1:1);
				$prev_link = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'calendar.uicalendar.planner','date'=>$prev_month));
				$next_link = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'calendar.uicalendar.planner','date'=>$next_month));
				$hdr[0][$i] = "<b><a href=\"$prev_link\">&lt;&lt;</a> &nbsp $month &nbsp <a href=\"$next_link\">&gt;&gt;</a></b>";

				$add_owner = array();	// if no add-rights on the showed cal use own cal
				if ( (!isset($this->bo->save_owner) || !$this->bo->save_owner)
					&& !$this->bo->check_perms(PHPGW_ACL_ADD) 
					|| !$this->bo->check_perms(PHPGW_ACL_ADD,0,(isset($this->bo->save_owner)?$this->bo->save_owner:'')))
				{
					$add_owner = array('owner' => $this->bo->contacts->is_contact($GLOBALS['phpgw_info']['user']['account_id']));
				}
				else
				{
					$add_owner = array('owner' => (isset($this->bo->save_owner)&& $this->bo->save_owner?$this->bo->save_owner:$this->bo->contacts->is_contact($GLOBALS['phpgw_info']['user']['account_id'])));
				}
				
				for ($d=1; $d<=$days; $d++)
				{
					$dayname = substr(lang(date('D',mktime(0,0,0,$m,$d,$y))),0,2);
					$index = $d + $this->planner_days;

					$hdr[2]['.'.$index] = 'colspan="'.$intervals_per_day.'" align="center"';

					// highlight today, saturday, sunday and holidays
					//
					$dow = phpgwapi_datetime::day_of_week($y,$m,$d);
					$date = sprintf("%04d%02d%02d",$y,$m,$d);
					if ($date == date('Ymd'))
					{
						$cssclass = 'cal_today';
					}
					elseif (isset($this->bo->cached_holidays[$date]) && $this->bo->cached_holidays[$date])
					{
						$cssclass = $this->bo->holiday_class;
						$hdr[2]['.'.$index] .= ' title="'.$this->bo->cached_holidays[$date][0]['name'].'"';
					}
					elseif ($dow == 0 || $dow == 6)
					{
						$cssclass = 'th';
					}

					$hdr[2]['.'.$index] .= " class=\"$cssclass\"";

					$hdr[2][$index] = '<a href="'.$this->planner_html->link('/index.php',
								array
								(
									'menuaction'	=> 'calendar.uicalendar.add',
									'date'		=> $date
								) + $add_owner
							).'">'.$dayname.'<br />'.$d.'</a>';
				}
				$this->planner_days += $days;
			}

			// create/initialize member variables describing the time interval to be displayed
			//
			$this->planner_end_month = $m - 1;
			$this->planner_end_year  = $y;
			$this->planner_days_in_end_month = phpgwapi_datetime::days_in_month($this->planner_end_month,$this->planner_end_year);
			$this->planner_firstday = intval(date('Ymd',mktime(0,0,0,$this->bo->month,1,$this->bo->year)));
			$this->planner_lastday  = intval(date('Ymd',mktime(0,0,0,$this->planner_end_month,$this->planner_days_in_end_month,$this->planner_end_year)));

			// generate line with calendar weeks in observed interval
			//
			$d      = mktime(0,0,0,$this->bo->month,1,$this->bo->year);
			$w      = date('W', $d);
			if ($w == 'W')	// php < 4.1
			{
				$w = 1 + intval(date('z',$d) / 7);	// a bit simplistic
			}
			$offset = (7-date("w", $d)+1)%7;
			$offset = $offset == 0 ? 7 : $offset;
			$cssclass = $w % 2 ? 'th' : 'row_on';

			$hdr[1]['.'.$w] = 'class="'.$cssclass.'" colspan="'.$intervals_per_day * $offset.'" align="left"';
			$hdr[1][$w] = '';
			if ($offset >= 3)
			{
				$hdr[1][$w] .= '<font size="-2"> '.lang('week').' '.$w.' </font>';
			}
			$days_left = $this->planner_days - $offset;

			$colspan = 7 * $intervals_per_day;
			while ($days_left > 0)
			{
				$colspan = ($days_left < 7) ? $days_left*$intervals_per_day : $colspan;
				$d += 604800; // 7 days whith 24 hours (1h == 3600 seconds) each
				$w = date('W', $d);
				if ($w == 'W')	// php < 4.1
				{
					$w = 1 + intval(date('z',$d) / 7);	// a bit simplistic
				}
				$w += (isset($hdr[1][$w]))?1:0; // bug in "date('W')" ?

				$cssclass = $w % 2 ? 'th' : 'row_on';
				$hdr[1]['.'.$w] = 'cssclass="'.$cssclass.'" colspan="'.$colspan.'" align="left"';
				$hdr[1][$w] = '';
				if ($days_left >= 3)
				{
					$hdr[1][$w] .= '<font size="-2"> '.lang('week').' '.$w.' </font>';
				}

				$days_left -= 7;
			}
			return $hdr;
		}

		/**
		 * planner_update_row - update a row of the planner view
		 *
		 * parameters are:
		 *   - index (e.g. user id, category id, ...) of the row
		 *   - name/title of the row (e.g. user name, category name)
		 *   - the event to be integrated
		 *   - list of categories associated with the event
		 *   - first and last cell of the row
		 */
		function planner_update_row($index,$name,$event,$cat,$start_cell,$end_cell)
		{
			$rows              = &$this->planner_rows;
			$intervals_per_day = $this->bo->prefs['calendar']['planner_intervals_per_day'];
			$is_private        = !$this->bo->check_perms(PHPGW_ACL_READ,$event);

			$view = $this->planner_html->link('/index.php',
				array(
					'menuaction' => 'calendar.uicalendar.view',
					'cal_id' => $event['id'],
					'date' => date('Ymd',$this->bo->maketime($event['start']))
				)
			);

			// check how many lines are needed for this "row" (currently: user or category)

			$i = 0;
			do {
				++$i;

				$k = $index.'_'.$i;
				$ka = '.nr_'.$k;

				if (!isset($rows[$k]))
				{
					if ($i > 1)				// further line - no name
					{
						$rows[$k] = array();
						$rows[$index.'_1']['._name'] = 'rowspan="'.$i.'"';
					}
					else
					{
						$rows[$k]['_name'] = $name;
					}
					$rows[$ka] = 0;
				}
				$rows[$index.'_1']['._name'] = (isset($rows[$index.'_1']['._name']) && $rows[$index.'_1']['._name'] ? $rows[$index.'_1']['._name'] . ' nowrap': ' nowrap');
//				$rows[$index.'_1']['._name'] .= ' nowrap'; // title must be one row

				$row = &$rows[$k];
				$akt_cell = &$rows[$ka];
			} while ($akt_cell > $start_cell);

			$id = $event['id'].'-'.date('Ymd',$this->bo->maketime($event['start']));
			if ($akt_cell < $start_cell)
			{
				$row[$id.'_1'] = '&nbsp;';
				$row['.'.$id.'_1'] = 'colspan="'.($start_cell-$akt_cell).'"';
			}
			$opt = &$row['.'.$id.'_2'];
			$cel = &$row[$id.'_2'];

			// if possible, display information about event within cells representing it
			//
			if ($start_cell < $end_cell)
			{
				$colspan = $end_cell - $start_cell;
				$opt .= "colspan=".(1 + $colspan);

				if (!$is_private)
				{
					$max_chars = intval(6*$colspan/$intervals_per_day-2);

					$min_chars = 3; // minimum for max_chars to display -> this should be configurable
					if ($max_chars >= $min_chars)
					{
						$len_title = strlen($event['title']);

						if ($len_title <= $max_chars)
						{
							$title = $event['title'];
							$max_chars -= $len_title + 3; // 3 chars for separator: " - "
							$len_descr = strlen($event['description']);

							if ($len_descr > 0 && $len_descr <= $max_chars)
							{
								$event['print_description'] = 'yes';
							}
						}
						else
						{
							$has_amp = strpos($event['title'],'&amp;');
							$title = substr($event['title'], 0 , $max_chars-1+($has_amp!==False&&$has_amp<$max_chars?4:0)).'...';
						}
						$event['print_title'] = 'yes';
					}
				}
			}

			if ( isset($cat['color']) && ($bgcolor = $cat['color']) ) //yes this is an assignment hack
			{
				$opt .= " style=\"background-color: $bgcolor; \"";
			}
			if (!$is_private)
			{
				$opt .= ' title="'.lang('Title').": ".$event['title'];
				if ($event['description'])
				{
					$opt .= "\n".lang('Description').": ".$event['description'];
				}
			}
			else
			{
				$opt .= ' title="'.lang('You do not have permission to read this record!');
			}

			$user_timezone = phpgwapi_datetime::user_timezone();
			$start = $GLOBALS['phpgw']->common->show_date($this->bo->maketime($event['start']) - $user_timezone);
			$end = $GLOBALS['phpgw']->common->show_date($this->bo->maketime($event['end']) - $user_timezone);
			$opt .= "\n".lang('Start Date/Time').": ".$start."\n".lang('End Date/Time').": ".$end;

			if ($event['location'] && !$is_private)
			{
				$opt .= " \n".lang('Location').": ".$event['location'];
			}

			if (!$is_private)
			{
				$opt .= '" onClick="location=\''.$view.'\'" class="planner-cell"';
				$cel = '<a href="'.$view.'">';
			}
			else
			{
				$opt .= '"';
				$cel = '';
			}

			if ($event['priority'] >= 3)
			{
				$cel .= $this->planner_html->image('calendar','mini-calendar-bar.gif','','border="0"');
			}
			if ($event['recur_type'])
			{
				$cel .= $this->planner_html->image('calendar','recur.gif','','border="0"');
			}
			$cel .= $this->planner_html->image('calendar',count($event['participants'])>1?'multi_3.gif':'single.gif',$this->planner_participants($event['participants']),'border="0"');
			$cel .= '</a>';

			if (isset($event['print_title']) && $event['print_title'] == 'yes')
			{
				$cel .= '<font size="-2"> '.$title.' </font>';
			}
			if (isset($event['print_description']) && $event['print_description'] == 'yes')
			{
				$cel .= '<font size="-2"> - '.$event['description'].' </font>';
			}

			$akt_cell = $end_cell + 1;

			return $rows;
		}

		function planner_process_event($event)
		{
			$intervals_per_day = $this->bo->prefs['calendar']['planner_intervals_per_day'];
			$interval = $this->planner_intervals[$intervals_per_day];
			$last_cell = $intervals_per_day * $this->planner_days - 1;

			$rows = &$this->planner_rows;

			// caluculate start and end of event
			//
			$event_start = intval(date('Ymd',mktime(0,0,0,$event['start']['month'],
																			$event['start']['mday'],
																			$event['start']['year'])));
			$event_end   = intval(date('Ymd',mktime(0,0,0,$event['end']['month'],
																			$event['end']['mday'],
																			$event['end']['year'])));

			// calculate first cell of event within observed interval
			//
			if ($event_start >= $this->planner_firstday)
			{
				$days_between = phpgwapi_datetime::days_between($this->bo->month,1,$this->bo->year,$event['start']['month'],$event['start']['mday'],$event['start']['year']);

				$start_cell = $intervals_per_day * $days_between + $interval[$event['start']['hour']];
			}
			else
			{
				$start_cell = 0;
			}

			// calculate last cell of event within observed interval
			//
			if ($event_end <= $this->planner_lastday)
			{
				$days_between = phpgwapi_datetime::days_between($this->bo->month,1,$this->bo->year,$event['end']['month'],$event['end']['mday'],$event['end']['year']);
				$end_cell = $intervals_per_day * $days_between + $interval[$event['end']['hour']];
				if ($end_cell == $start_cell && $end_cell < $last_cell)
				{
					$end_cell++;	// min. width 1 interval
				}
			}
			else
			{
				$end_cell = $last_cell;
			}
			// get the categories associated with event
			//
			if ($c = $event['category'])
			{
				list($cat)   = $this->planner_category($event['category']);
				if ($cat['parent'])
				{
					list($pcat) = $this->planner_category($c = $cat['parent']);
				}
				else
				{
					$pcat = $cat;
				}
			}
			else
			{
				$cat = $pcat = array( 'name' => lang('none'));
			}

			// add the event to it`s associated row(s)
			//
			if ($this->bo->sortby == 'category')
			{
				// event needs to show up in it`s category`s row
				//
				$this->planner_update_row($c,$pcat['name'],$event,$cat,$start_cell,$end_cell);
			}
			elseif ($this->bo->sortby == 'user')
			{
				// event needs to show up in rows of all participants that are also owners
				//
				//reset($this->planner_group_members);
				//while(list($user_name,$id) = each($this->planner_group_members))
                                if (is_array($this->planner_group_members))
                                {
                                    foreach($this->planner_group_members as $user_name => $id)
				{
					$status = (isset($event['participants'][$id])?$event['participants'][$id]:'');
					if ($status && $status != 'R')
					{
						$this->planner_update_row($user_name,$user_name,$event,$cat,$start_cell,$end_cell);
					}
				}
			}
		}
		}

		function planner_pad_rows()
		{
			$rows = &$this->planner_rows;

			if ($this->bo->sortby == 'user')
			{
				// add empty rows for users that do not participante in any event
				//
				//reset($this->planner_group_members);
				//while(list($user_name,$id) = each($this->planner_group_members))
                                if (is_array($this->planner_group_members))
                                {
                                    foreach($this->planner_group_members as $user_name => $id)
				{
					$k  = $user_name.'_1';
					$ka = '.nr_'.$k;
					if (!isset($rows[$k]))
					{
						$rows[$k]['_name'] = $user_name;
				//		$rows[$k]['._name'] .= ' nowrap';
						$rows[$k]['._name'] = ' nowrap';
						$rows[$ka] = 0;
					}
				}
			}
			}

			// fill the remaining cols
			//
			$last_cell = $this->bo->prefs['calendar']['planner_intervals_per_day'] * $this->planner_days - 1;

			ksort($rows);
			$cnt = 0;;
			foreach ( $rows as $k => $r )
			{
				if (is_array($r))
				{
					$rows['.'.$k] = 'class="' . ($cnt % 2 ? 'row_on' : 'row_off') . '"';
					$row = &$rows[$k];
					$akt_cell = &$rows['.nr_'.$k];
					if ($akt_cell < $last_cell)
					{
						$row['3'] = '&nbsp';
						$row['.3'] = 'colspan="'.(1+$last_cell-$akt_cell).'"';
					}
				}
				++$cnt;
			}
		}

		function planner_print_rows()
		{
			$cssclass = "class=\"th\"";
			$intervals_per_day = $this->bo->prefs['calendar']['planner_intervals_per_day'];

			if ($this->debug)
			{
				_debug_array($this->planner_rows);
				reset($this->planner_rows);
			}
			return $this->planner_html->table(
				array(
					'_hdr0' => $this->planner_header[0],
					'._hdr0' => $cssclass,
					'_hdr1' => $this->planner_header[1],
					'._hdr1' => $cssclass,
					'_hdr2' => $this->planner_header[2],
					'._hdr2' => $cssclass
				)+$this->planner_rows,
				'width="100%" cols="'.(1+$this->planner_days_in_end_month*$intervals_per_day).'"');
		}

		function planner_process_interval()
		{
			// generate duplicate free list of events within observed interval
			//
			$this->bo->store_to_cache(
				array(
					'syear'	=> $this->bo->year,
					'smonth'	=> $this->bo->month,
					'sday'	=> 1,
					'eyear'	=> $this->planner_end_year,
					'emonth'	=> $this->planner_end_month,
					'eday'	=> $this->planner_days_in_end_month
				)
			);
			$this->bo->remove_doubles_in_cache($this->planner_firstday,$this->planner_lastday);

			// process all events within observed interval
			//
			for($v=$this->planner_firstday;$v<=$this->planner_lastday;$v++)
			{
				$daily = (isset($this->bo->cached_events[$v])?$this->bo->cached_events[$v]:'');

				print_debug('For Date',$v);
				print_debug('Count of items',count($daily));

				// process all events on day $v
				//
				if (is_array($daily)) foreach($daily as $event)
				{
					if ($event['recur_type'])	// calculate start- + end-datetime for recuring events
					{
						$this->bo->set_recur_date($event,$v);
					}
					if (!$this->bo->rejected_no_show($event))
					{
						$this->planner_process_event($event);
					}
				}
			}
			$this->planner_pad_rows();
		}

		function matrixselect()
		{
			$datetime = mktime(0,0,0,$this->bo->month,$this->bo->day,$this->bo->year) - phpgwapi_datetime::user_timezone();

			$sb = CreateObject('phpgwapi.sbox');
			$jscal = CreateObject('phpgwapi.jscalendar');

			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			if ($this->always_app_header)
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['calendar']['title'].' - '.lang('Matrixview');
			}
			//$GLOBALS['phpgw']->common->phpgw_header();
			$this->header();

			$p = &$GLOBALS['phpgw']->template;
			$p->set_file(
				array(
					'mq'		=> 'matrix_query.tpl',
					'form_button'	=> 'form_button_script.tpl'
				)
			);
			$p->set_block('mq','matrix_query','matrix_query');
			$p->set_block('mq','list','list');

			$p->set_var(array(
				'title'			=> lang('Daily Matrix View'),
				'th_class'		=> 'th',
				'action_url'	=> $this->page('viewmatrix')
			));

// Date
			$var[] = array
				(
					'tr_color' => 'row_on',
					'field'	=> lang('Date'),
					'data'	=> $jscal->input('date_select', $datetime)
				);

// View type
			$var[] = array(
				'tr_color' => 'row_off',
				'field'	=>	lang('View'),
				'data'	=>	'<select name="matrixtype">'."\n"
					. '<option value="free/busy" selected>'.lang('free/busy').'</option>'."\n"
					. '<option value="weekly">'.lang('Weekly').'</option>'."\n"
					. '</select>'."\n"
			);

// Participants
			$accounts = $GLOBALS['phpgw']->acl->get_ids_for_location('run', 1, 'calendar');
			if ( !is_array($accounts) )
			{
				$accounts = array();
			}
			$users = array();
			foreach ( $accounts as $user )
			{
				if ( $GLOBALS['phpgw']->accounts->get_type($user) == 'g' )
				{
					if ( !isset($users["g_{$user}"]) )
					{
						$users["g_{$user}"] = $GLOBALS['phpgw']->common->grab_owner_name($user);
						$group_members = $GLOBALS['phpgw']->acl->get_ids_for_location($user,1,'phpgw_group');
						if ( is_array($group_members) && count($group_members) )
						{
							foreach ( $group_members as $member )
							{
								$person_id = intval($this->bo->contacts->is_contact($member));
								if ( $person_id > 0  && !isset($users[$person_id]) )
								{
									$users[$person_id] = $this->bo->contacts->get_name_of_person_id($person_id);
								}
							}
						}
					}
				}
				else
				{
					$person_id = intval($this->bo->contacts->is_contact($user));
					if ( $person_id > 0 && !isset($users[$person_id]) )
					{
						$users[$person_id] = $this->bo->contacts->get_name_of_person_id($person_id);
					}
				}
				echo '<br />';
			}

			$num_users = count($users);

			if ($num_users > 50)
			{
				$size = 15;
			}
			elseif ($num_users > 5)
			{
				$size = 5;
			}
			else
			{
				$size = $num_users;
			}
			$groups = $usrs = '';
			@asort($users);
			foreach ( $users as $user_id => $user )
			{
				if( strpos($user_id, 'g') !== false )
				{
					$groups .= "\t<option value=\"$user_id\">$user [g]</option>\n";
				}
				else if( $this->bo->check_perms(PHPGW_ACL_READ, 0, $user_id) )
				{
					$usrs .= "\t<option value=\"$user_id\">$user</option>\n";
				}
			}
			$var[] = array
				(
					'tr_color' => 'row_on',
					'field'	=> lang('Participants'),
					'data'	=> "\n<select name=\"participants[]\" multiple size=\"$size\">\n{$groups}{$usrs}\n</select>\n"
				);

			for($i=0;$i<count($var);$i++)
			{
				$this->output_template_array($p,'rows','list',$var[$i]);
			}

			$vars = array(
				'submit_button'		=> lang('View'),
				'action_url_button'	=> $this->bo->return_to ? $GLOBALS['phpgw']->link('/index.php', $this->bo->return_to) : $this->page(''),
				'action_text_button'	=> lang('Cancel'),
				'action_confirm_button'	=> '',
				'action_extra_field'	=> ''
			);

			$p->set_var($vars);
			$p->parse('cancel_button','form_button');
			$p->pparse('out','matrix_query');
		}

		function viewmatrix()
		{
			if ( phpgw::get_var('cancel', 'bool', 'POST') )
			{
				$this->index();
			}
			$participants = phpgw::get_var('participants', 'string', 'POST');

			$parts = array();
			$acct = CreateObject('phpgwapi.accounts',$this->bo->owner);
			
			if (is_array($participants))
			{
				foreach($participants as $participant)
				{
					switch (substr($participant, 0, 2))
					{
						case 'g_':
							if ($members = $acct->member((int) substr($participant, 2)) )
							{
								foreach($members as $member)
								{
									$person_id = $this->bo->contacts->is_contact($member['account_id']);
									if ( $this->bo->check_perms(PHPGW_ACL_READ, 0, $person_id) )
									{
										$parts[$person_id] = True;
									}
								}
							}
							
							break;
						default:
							if($this->bo->check_perms(PHPGW_ACL_READ, 0, $this->bo->contacts->is_contact($participant) ) )
							{
								$parts[$participant] = True;
							}
							break;
					}
				}
			}
			unset($acct);

			$participants = array_keys($parts);	// get id's as values and a numeric index

			$date_select = phpgw::get_var('date_select', 'string', 'POST');
			if ( $date_select )
			{
				$jscal = CreateObject('phpgwapi.jscalendar', false);
				$date_selected = $jscal->input2date($date_select);
				$this->bo->year = $date_selected['year'];
				$this->bo->month = $date_selected['month'];
				$this->bo->day = $date_selected['day'];
			}

			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			if ($this->always_app_header)
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['calendar']['title'].' - '.lang('Matrixview');
			}
			//$GLOBALS['phpgw']->common->phpgw_header();
			$this->header();

//_debug_array($_POST);
			$matrixtype = phpgw::get_var('matrixtype', 'string', 'POST');
			switch( $matrixtype )
			{
				case 'free/busy':
					echo '<br />'.$this->timematrix(
						array
						(
					//		'date'		=> phpgwapi_datetime::gmtdate($date_selected['raw']),
							'date'		=> phpgwapi_datetime::gmtdate(mktime(0,0,0,$this->bo->month,$this->bo->day,$this->bo->year)),
							'starttime'	=> $this->bo->splittime('000000',False),
							'endtime'	=> 0,
							'participants'	=> $parts
						)
					);
					$prev_next_shift = 1;
					break;
				case 'weekly':
					echo "<br />\n<span style=\"font-size: larger; color:#000000;\"><strong>".$this->bo->get_week_label()."</strong></span><br />\n"
						. $this->display_weekly(
							array
							(
								'date'		=> sprintf("%04d%02d%02d",$this->bo->year,$this->bo->month,$this->bo->day),
								'showyear'	=> true,
								'owners'	=> $participants
							)
					);

					$prev_next_shift = 7;
					break;
			}
//_debug_array($date_selected);
 			$next_day = phpgwapi_datetime::makegmttime(0,0,0,$this->bo->month,$this->bo->day + $prev_next_shift,$this->bo->year);
 			$prev_day = phpgwapi_datetime::makegmttime(0,0,0,$this->bo->month,$this->bo->day - $prev_next_shift,$this->bo->year);

 			#echo '<br>prev: '.var_dump($prev_day);
 			#echo '<br>next: '.var_dump($next_day);
 
			

			$form_action = $this->page('viewmatrix');
			$part_inputs = '';
 			foreach ( phpgw::get_var('participants', 'int', 'POST') as $part)
 			{
 				$part_inputs .= <<<HTML
				<input type="hidden" name="participants[]" value="{$part}">

HTML;
 			}
			$other_type = $matrixtype == 'weekly' ? 'free/busy' : 'weekly';
			$lang_othertype = lang($other_type);
			$lang_change_to = lang('change view to');
			$lang_cancel = lang('cancel');
			$lang_refresh = lang('refresh');

			// Prev
			echo <<<HTML
<table cellpadding="5" width="100%">
	<tr>
		<td>
 			<br>
			<form action="{$form_action}" method="post" name="matrixform">
 				<input type="hidden" name="year" value="{$prev_day['year']}">
 				<input type="hidden" name="month" value="{$prev_day['month']}">
 				<input type="hidden" name="day" value="{$prev_day['day']}">
 				<input type="hidden" name="matrixtype" value="{$matrixtype}">
				$part_inputs
				<input type="submit" name="prev" value="&lt;&lt;">
 			</form>
		</td>
		<td align="right">
			<br>
			<form action="$form_action" method="post" name="matrixform">
				<input type="hidden" name="year" value="{$next_day['year']}">
				<input type="hidden" name="month" value="{$next_day['month']}">
				<input type="hidden" name="day" value="{$next_day['day']}">
				<input type="hidden" name="matrixtype" value="{$matrixtype}">
				$part_inputs
				<input type="submit" name="prev" value="&gt;&gt;">
			</form>'
		</td>
	</tr>
</table>
<table cellpadding="5">
	<tr>
		<td valign="bottom">
			<form action="{$form_action}" method="post" name="matrixform">
				<input type="hidden" name="year" value="{$this->bo->year}">
				<input type="hidden" name="month" value="{$this->bo->month}">'."\n";
				<input type="hidden" name="day" value="{$this->bo->day}">
				<input type="hidden" name="matrixtype" value="{$_POST['matrixtype']}">
				$part_inputs;
				<input type="submit" name="refresh" value="{$lang_refresh}">
				<input type="submit" name="cancel" value="{$lang_cancel}">
			</form>
		</td>
		<td> &nbsp; </td>
		<td align="right" valign="bottom">
			<br>
			<form action="{$form_action}" method="post" name="matrixform">
				<input type="hidden" name="year" value="{$this->bo->year}">
				<input type="hidden" name="month" value="{$this->bo->month}">
				<input type="hidden" name="day" value="{$this->bo->day}">
				<input type="hidden" name="matrixtype" value="{$other_type}">
 				$part_inputs
				$lang_change_to:  <input type="submit" name="change_view" value="{$lang_other_type}">
			</form>
		</td>
	</tr>
</table>

HTML;
		}

		function search()
		{
			$keywords = phpgw::get_var('keywords', 'string', 'POST');
			if ( !$keywords )
			{
				// If we reach this, it is because they didn't search for anything,
				// attempt to send them back to where they where.
				$GLOBALS['phpgw']->redirect_link('/index.php',array
				(
					'menuaction' => phpgw::get_var('from', 'string', 'POST'),
					'date' => phpgw::get_var('year', 'int', 'POST') 
							. phpgw::get_var('monnth', 'int', 'POST') 
							. phpgw::get_var('day', 'int', 'POST')
				));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['calendar']['title'].' - '.lang('Search Results');
			//$GLOBALS['phpgw']->common->phpgw_header();
			$this->header();

			$error = '';

			$matches = 0;

			// There is currently a problem searching in with repeated events.
			// It spits back out the date it was entered.  I would like to to say that
			// it is a repeated event.

			// This has been solved by the little icon indicator for recurring events.

			$event_ids = $this->bo->search_keywords($keywords);
			foreach($event_ids as $key => $id)
			{
				$event = $this->bo->read_entry($id);

				if(!$this->bo->check_perms(PHPGW_ACL_READ,$event))
				{
					continue;
				}

				$datetime = $this->bo->maketime($event['start']) - phpgwapi_datetime::user_timezone();

				$info[strval($event['id'])] = array(
					'tr_color'	=> $GLOBALS['phpgw']->nextmatchs->alternate_row_class(),
					'date'		=> $GLOBALS['phpgw']->common->show_date($datetime),
					'link'		=> $this->link_to_entry($event,$event['start']['month'],$event['start']['mday'],$event['start']['year'])
				);

			}
			$matches = count($event_ids);

			if ($matches == 1)
			{
				$quantity = lang('1 match found').'.';
			}
			elseif ($matches > 0)
			{
				$quantity = lang('%1 matches found',$matches).'.';
			}
			else
			{
				echo '<b>'.lang('Error').':</b>'.lang('no matches found');
				return;
			}

			$p = $GLOBALS['phpgw']->template;
			$p->set_file(
				array(
					'search_form'	=> 'search.tpl'
				)
			);
			$p->set_block('search_form','search','search');
			$p->set_block('search_form','search_list_header','search_list_header');
			$p->set_block('search_form','search_list','search_list');
			$p->set_block('search_form','search_list_footer','search_list_footer');

			$var = array(
				'th_bg'		=> $this->theme['th_bg'],
				'search_text'	=> lang('Search Results'),
				'quantity'	=> $quantity
			);
			$p->set_var($var);

			if($matches > 0)
			{
				$p->parse('rows','search_list_header',True);
			}
			foreach($info as $id => $data)
			{
				$p->set_var($data);
				$p->parse('rows','search_list',True);
			}

			if($matches > 0)
			{
				$p->parse('rows','search_list_footer',True);
			}

			$p->pparse('out','search');
		}

		/* Private functions */
		function _debug_sqsof()
		{
			$data = array(
				'filter'     => $this->bo->filter,
				'cat_id'     => $this->bo->cat_id,
				'owner'      => $this->bo->owner,
				'year'       => $this->bo->year,
				'month'      => $this->bo->month,
				'day'        => $this->bo->day,
				'sortby'     => $this->bo->sortby,
				'num_months' => $this->bo->num_months
			);
			return _debug_array($data,False);
		}

		function output_template_array(&$p,$row,$list,$var)
		{
			if (!isset($var['hidden_vars']))
			{
				$var['hidden_vars'] = '';
			}
			if (!isset($var['tr_color']))
			{
				$var['tr_color'] = $GLOBALS['phpgw']->nextmatchs->alternate_row_class();
			}
			$p->set_var($var);
			$p->parse($row,$list,True);
		}

		function page($_page='',$params='', $redirect = false)
		{
			if($_page == '')
			{
				$page_ = explode('.',$this->bo->prefs['calendar']['defaultcalendar']);
				$_page = $page_[0];

				if ($_page=='planner_cat' || $_page=='planner_user')
				{
					$_page = 'planner';
				}
				elseif ($_page=='index' || ($_page != 'day' && $_page != 'week' && $_page != 'week_new' && $_page != 'month' && $_page != 'year' && $_page != 'planner'))
				{
					$_page = 'month';
					$GLOBALS['phpgw']->preferences->add('calendar','defaultcalendar','month');
					$GLOBALS['phpgw']->preferences->save_repository();
				}
			}
			if($GLOBALS['phpgw_info']['flags']['currentapp'] == 'home' ||
			   strpos($GLOBALS['phpgw_info']['flags']['currentapp'],'mail') !== false) // email, felamimail, ...
			{
				$page_app = 'calendar';
			}
			else
			{
				$page_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			if ( !is_array($params) )
			{
//				parse_str($params, $new_params);
//				$params =& $new_params;
				$params = explode('&', $params);
				foreach( $params as $v )
				{
					$b = explode('=', $v);
					if(isset($b[0]) && $b[0])
					{
						$new_params[$b[0]] = $b[1];
					}
				}
				unset($params);
				$params = (isset($new_params)?$new_params:'');
				unset($new_params);
			}
//_debug_array($params);
			$params['menuaction'] = "{$page_app}.ui{$page_app}.{$_page}";
			return $GLOBALS['phpgw']->link('/index.php', $params, $redirect);
		}

		function header($local_header = true)
		{
			if ( $this->header_included )
			{
				return false;
			}
			$this->header_included = true;

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$cols = 8;
			if($this->bo->check_perms(PHPGW_ACL_PRIVATE) == True)
			{
				$cols++;
			}

			$tpl =& $GLOBALS['phpgw']->template;
			$tpl->set_root(PHPGW_APP_TPL);
			$tpl->set_unknowns('remove');
			
			if (!file_exists($file = $this->template_dir.'/header.inc.php'))
			{
				$file = PHPGW_SERVER_ROOT . '/calendar/templates/base/header.inc.php';
			}
			if($local_header)
			{
				include($file);
			}
		}

		function footer()
		{
			$menuaction = $GLOBALS['phpgw_info']['menuaction'];
			list(,,$method) = explode('.',$menuaction);
		
			$p = $GLOBALS['phpgw']->template;
	
			$p->set_file(array
				(
					'foot'		=> 'footer.tpl',
					'form_button'	=> 'form_button_script.tpl'
				)
			);
			$p->set_block('foot','footer_table','footer_table');
			$p->set_block('foot','footer_row','footer_row');
			$p->set_block('foot','blank_row','blank_row');

			$m = $this->bo->month;
			$y = $this->bo->year;

			$thisdate = date('Ymd',mktime(0,0,0,$m,1,$y));
			$y--;

			$str = '';
			for ($i = 0; $i < 25; $i++)
			{
				$m++;
				if ($m > 12)
				{
					$m = 1;
					$y++;
				}
				$d = mktime(0,0,0,$m,1,$y);
				$d_ymd = date('Ymd',$d);
				$str .= '<option value="'.$d_ymd.'"'.($d_ymd == $thisdate?' selected':'').'>'.lang(date('F', $d)).strftime(' %Y', $d).'</option>'."\n";
			}

			$var = array(
				'action_url'	=> $this->page($method,''),
				'form_name'	=> 'SelectMonth',
				'label'		=> lang('Month'),
				'form_label'	=> 'date',
				'form_onchange'	=> 'document.SelectMonth.submit()',
				'row'		=> $str,
				'go'		=> lang('Go!')
			);
			$this->output_template_array($p,'table_row','footer_row',$var);

			if($menuaction == 'calendar.uicalendar.week')
			{
				$user_timezone = phpgwapi_datetime::user_timezone();
				unset($thisdate);
				$thisdate = mktime(0,0,0,$this->bo->month,$this->bo->day,$this->bo->year) - $user_timezone;
				$sun = phpgwapi_datetime::get_weekday_start($this->bo->year,$this->bo->month,$this->bo->day) - $user_timezone;

				$str = '';
				for ($i = -7; $i <= 7; $i++)
				{
					$begin = $sun + (7*24*60*60 * $i) + 12*60*60;	// we use midday, that changes in daylight-saveing does not effect us
					$end = $begin + 6*24*60*60;
//					echo "<br />$i: ".date('d.m.Y H:i',$begin).' - '.date('d.m.Y H:i',$end);
					$str .= '<option value="' . $GLOBALS['phpgw']->common->show_date($begin,'Ymd') . '"'.($begin <= $thisdate && $end >= $thisdate?' selected':'').'>'
					   . $GLOBALS['phpgw']->common->show_date($begin,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) . ' - '
					   . $GLOBALS['phpgw']->common->show_date($end,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				}

				$var = array(
					'action_url'	=> $this->page($method,''),
					'form_name'	=> 'SelectWeek',
					'label'		=> lang('Week'),
					'form_label'	=> 'date',
					'form_onchange'	=> 'document.SelectWeek.submit()',
					'row'		=> $str,
					'go'		=> lang('Go!')
				);

				$this->output_template_array($p,'table_row','footer_row',$var);
			}

			$str = '';
			for ($i = ($this->bo->year - 3); $i < ($this->bo->year + 3); $i++)
			{
				$str .= '<option value="'.$i.'"'.($i == $this->bo->year?' selected':'').'>'.$i.'</option>'."\n";
			}

			$var = array(
				'action_url'	=> $this->page($method,''),
				'form_name'	=> 'SelectYear',
				'label'		=> lang('Year'),
				'form_label'	=> 'year',
				'form_onchange'	=> 'document.SelectYear.submit()',
				'row'		=> $str,
				'go'		=> lang('Go!')
			);
			$this->output_template_array($p,'table_row','footer_row',$var);

			if($menuaction == 'calendar.uicalendar.planner')
			{
				$str = '';
				$date_str = '';

				$date = phpgw::get_var('date', 'int', 'GET');
				if ( $date ) 
				{
					$date_str .= '    <input type="hidden" name="date" value="'.$date . "\">\n";
				}
				$date_str .= '    <input type="hidden" name="month" value="'.$this->bo->month.'">'."\n";
				$date_str .= '    <input type="hidden" name="day" value="'.$this->bo->day.'">'."\n";
				$date_str .= '    <input type="hidden" name="year" value="'.$this->bo->year.'">'."\n";

				for($i=1; $i<=6; $i++)
				{
					$str .= '<option value="'.$i.'"'.($i == $this->bo->num_months?' selected':'').'>'.$i.'</option>'."\n";
				}

				$var = array(
					'action_url'	=> $this->page($method,''),
					'form_name'	=> 'SelectNumberOfMonths',
					'label'		=> lang('Number of Months'),
					'hidden_vars' => $date_str,
					'form_label'	=> 'num_months',
					'form_onchange'	=> 'document.SelectNumberOfMonths.submit()',
					'action_extra_field'	=> $date_str,
					'row'		=> $str,
					'go'		=> lang('Go!')
				);
				$this->output_template_array($p,'table_row','footer_row',$var);
			}

			$var = array(
				'submit_button'		=> lang('Submit'),
				'action_url_button'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'calendar.uiicalendar.import')),
				'action_text_button'	=> lang('Import'),
				'action_confirm_button'	=> '',
				'action_extra_field'	=> ''
			);
			$this->output_template_array($p,'b_row','form_button',$var);
			$p->parse('table_row','blank_row',True);

			$p->pparse('out','footer_table');
			unset($p);
		}

		function no_edit()
		{
			if(!isset($GLOBALS['phpgw_info']['flags']['noheader']))
			{
				unset($GLOBALS['phpgw_info']['flags']['noheader']);
				unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
				$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
				$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
				$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['calendar']['title'].' - '.lang('Permission denied');
				//$GLOBALS['phpgw']->common->phpgw_header();
				$this->header();
			}
			echo '<center>You do not have permission to edit this appointment!</center>';
			return;
		}

		function link_to_entry($event,$month,$day,$year)
		{
			$user_timezone = phpgwapi_datetime::user_timezone();
			$str = '';
			$is_private = '';
			if(!$event['public']==1 || !$this->bo->check_perms(PHPGW_ACL_READ,$event))
			{
				$is_private = true;
			}
		//	$is_private = !$event['public'] || !$this->bo->check_perms(PHPGW_ACL_READ,$event);
			$editable = $this->bo->check_perms(PHPGW_ACL_READ,$event);

			$starttime = $this->bo->maketime($event['start']) - $user_timezone;
			$endtime = $this->bo->maketime($event['end']) - $user_timezone;
			$rawdate = mktime(0,0,0,$month,$day,$year);
			$rawdate_offset = $rawdate - $user_timezone;
			$nextday = mktime(0,0,0,$month,$day + 1,$year) - $user_timezone;
			if (intval($GLOBALS['phpgw']->common->show_date($starttime,'Hi')) && $starttime == $endtime)
			{
				$time = $GLOBALS['phpgw']->common->show_date($starttime,$this->bo->users_timeformat);
			}
			elseif ($starttime <= $rawdate_offset && $endtime >= $nextday - 60)
			{
				$time = '[ '.lang('All Day').' ]';
			}
			elseif (intval($GLOBALS['phpgw']->common->show_date($starttime,'Hi')) || $starttime != $endtime)
			{
				if($starttime < $rawdate_offset && isset($event['recur_type']) && $event['recur_type']==MCAL_RECUR_NONE)
				{
					$start_time = $GLOBALS['phpgw']->common->show_date($rawdate_offset,$this->bo->users_timeformat);
				}
				else
				{
					$start_time = $GLOBALS['phpgw']->common->show_date($starttime,$this->bo->users_timeformat);
				}

				if($endtime >= ($rawdate_offset + phpgwapi_datetime::SECONDS_IN_DAY))
				{
					$end_time = $GLOBALS['phpgw']->common->show_date(mktime(23,59,59,$month,$day,$year) - $user_timezone,$this->bo->users_timeformat);
				}
				else
				{
					$end_time = $GLOBALS['phpgw']->common->show_date($endtime,$this->bo->users_timeformat);
				}
				$time = $start_time.'-'.$end_time;
			}
			else
			{
				$time = '';
			}
			$text = '';
			if(!$is_private && isset($event['users_status']) && $event['users_status'])
			{
				$text .= $this->bo->display_status($event['users_status']);
				$status_long = $this->bo->get_long_status($event['users_status']);
			}

			$status = $text;

			$tmp_cats = explode(',',$event['category']);
			$tmp_cat  = $tmp_cats[0];
			$cat_color = isset($this->cat_colors[$tmp_cat]) ? $this->cat_colors[$tmp_cat] : 'transparent';
			$cat_color_div_start = '<div style="border-left:3px solid '.$cat_color.';padding:3px;">';
			$cat_color_div_end = '</div>';

			if(isset($event['participants']) && is_array($event['participants']))
			{
				foreach ($event['participants'] as $participant => $accept)
				{
					$participants[] = $this->bo->contacts->get_name_of_person_id($participant);
				}
				$str_participants = implode(', ',$participants);
			}

			switch ($this->bo->prefs['calendar']['show_descrpt_ovr'])
			{
				case '0':
					$text = '<span style="font-size: x-small; font-family:'.$this->theme['font'].'"><pre>' . $str_participants
						. $GLOBALS['phpgw']->browser->br
						. '<span style="white-space: nowrap;">'.$time.' </span> '
						. wordwrap($this->bo->get_short_field($event,$is_private,'title'), 30, $GLOBALS['phpgw']->browser->br, True)
						. $text
						. $GLOBALS['phpgw']->browser->br;
					break;

				default:
					$text = '<span style="font-size: x-small; font-family:'.$this->theme['font'].'">' . $str_participants 
						. $GLOBALS['phpgw']->browser->br
						. '<span style="white-space: nowrap;">'.$time.' </span> '
						. wordwrap($this->bo->get_short_field($event,$is_private,'title'), 30, $GLOBALS['phpgw']->browser->br, True)
						. $text.': <em>'
						. wordwrap($this->bo->get_short_field($event,$is_private,'description'), 30,  $GLOBALS['phpgw']->browser->br, True)
						. '</em></span>'
						. $GLOBALS['phpgw']->browser->br;
					break;
			}

			switch ($this->bo->prefs['calendar']['show_descrpt_title'])
			{
				case '0':
				        break;

				case '23':
					$participants = '';
					if ( is_array($event['participants'])  && count($event['participants'])>0 )
					{
						foreach($event['participants'] as $user => $short_status)
						{
							if($GLOBALS['phpgw']->accounts->exists($user))
							{
								$participants .= $this->bo->contacts->get_name_of_person_id($user).' ('.$this->bo->get_long_status($short_status).')<br>';
							}
						}
					}
					
					$text = "<span onmouseover=\"this.T_TITLE='{$time}';"
						. "this.T_BGCOLOR='{$this->theme['bg02']}';"
						. "this.T_BORDERCOLOR='{$this->theme['navbar_bg']}';"
						. "this.T_TITLECOLOR='{$this->theme['table_text']}';"
						. "return escape('"
						. addslashes("<span style='font-size: x-small; font-family: {$this->theme['font']};'>"
							. $this->my_nl2br($this->bo->get_short_field($event,$is_private,'title'))
							. " ($status_long):<br /><em>"
							. $this->my_nl2br($this->bo->get_short_field($event,$is_private,'description'))
							. '</em>'
							. $GLOBALS['phpgw']->browser->br
							. '<hr color='.$this->theme['navbar_bg'].'> '.lang('Participants').': <br><i>'
							. $participants
							.'</em></span>')
						. "');\">"
						. $text . '</span>';
					break;

				case '1':
				default:
					$text = '<span title="'.$time.' '
						. $this->bo->get_short_field($event,$is_private,'title')
						. $status.': '
						. $this->bo->get_short_field($event,$is_private,'description')
						. "\">{$text}</span>\n";
			        break;
			}

			//$text = '<font size="-2" face="'.$this->theme['font'].'"><nobr>&nbsp;'.$time.'&nbsp;</nobr> '.$this->bo->get_short_field($event,$is_private,'title').$text.': <I>'.$this->bo->get_short_field($event,$is_private,'description').'</I></font>'.$GLOBALS['phpgw']->browser->br;

			if ($cat_color)
			{
				$var = array(
					'cat_color_div_start' => $cat_color_div_start
				);
				$this->output_template_array($this->link_tpl,'picture','cat_color_div_start',$var);
			}

			if ($editable)
			{
				$date = sprintf('%04d%02d%02d',$year,$month,$day);
				$this->link_tpl->set_var('link_link',$this->page('view',array('cal_id'=>$event['id'],'date'=>$date)));
				#$this->link_tpl->set_var('lang_view',lang('View this entry'));
				$this->link_tpl->parse('picture','link_open',True);

				if ($event['priority'] >= 3)
				{
					$picture[] = array(
						'pict'	=> $GLOBALS['phpgw']->common->image('calendar','high'),
						'width'	=> 8,
						'height'=> 17,
						'alt' => lang('high priority'),
						'title' => lang('high priority')
					);
				}

				if(isset($event['recur_type']) && $event['recur_type'] == MCAL_RECUR_NONE)
				{
					$picture[] = array(
						'pict'	=> $GLOBALS['phpgw']->common->image('calendar','circle'),
						'width'	=> 5,
						'height'=> 7,
						'alt' => lang('single event'),
						'title' => lang('single event')
					);
				}
				else
				{
					$picture[] = array(
						'pict'	=> $GLOBALS['phpgw']->common->image('calendar','recur'),
						'width'	=> 12,
						'height'=> 12,
						'alt' => lang('recurring event'),
						'title' => lang('recurring event')
					);
				}

				$participants = $this->planner_participants($event['participants']);
				if(count($event['participants']) > 1)
				{
					$picture[] = array(
						'pict'	=> $GLOBALS['phpgw']->common->image('calendar','multi_3'),
						'width'	=> 14,
						'height'=> 14,
						'alt'	=> $participants,
						'title' => $participants,
					);
				}
				else
				{
					$picture[] = array(
						'pict'	=>  $GLOBALS['phpgw']->common->image('calendar','single'),
						'width'	=> 14,
						'height'=> 14,
						'alt' => $participants,
						'title' => $participants,
					);
				}
				if($event['public'] == 0)
				{
					$picture[] = array(
						'pict'	=> $GLOBALS['phpgw']->common->image('calendar','private'),
						'width'	=> 13,
						'height'=> 13,
						'alt' => lang('private'),
						'title' => lang('private')
					);
				}
				if(@isset($event['alarm']) && count($event['alarm']) >= 1)
				{
					// if the alarm is to go off the day before the event
					// the icon does not show up because of 'alarm_today'
					if($this->bo->alarm_today($event,$rawdate_offset,$starttime))
					{
						$picture[] = array(
							'pict'	=> $GLOBALS['phpgw']->common->image('calendar','alarm'),
							'width'	=> 13,
							'height'=> 13,
							'alt' => lang('alarm'),
							'title' => lang('alarm')
						);
					}
				}

				$description = $this->bo->get_short_field($event,$is_private,'description');
				for($i=0;$i<count($picture);$i++)
				{
					$var = array(
						'pic_image'  => $picture[$i]['pict'],
						'width'	     => $picture[$i]['width'],
						'height'     => $picture[$i]['height'],
						'alt'        => $picture[$i]['alt'],
						'title'      => $picture[$i]['title'],
					);
					$this->output_template_array($this->link_tpl,'picture','pict',$var);
				}
			}
			if ($text)
			{
				$var = array(
					'text' => $text
				);
				$this->output_template_array($this->link_tpl,'picture','link_text',$var);
			}



			if ($editable)
			{
				$this->link_tpl->parse('picture','link_close',True);
			}
## by tb -> cat_color_div_end is for end of cat-colors
			if ($cat_color)
			{
				$var = array(
					'cat_color_div_end' => $cat_color_div_end
				);
				$this->output_template_array($this->link_tpl,'picture','cat_color_div_end',$var);
			}
## END - by tb
			$str = $this->link_tpl->fp('out','link_pict');
			$this->link_tpl->set_var('picture','');			
			$this->link_tpl->set_var('out','');
//			unset($p);
			return $str;
		}

		function overlap($params)
		{
			if(!is_array($params))
			{
			}
			else
			{
				$overlapping_events = $params['o_events'];
				$event = $params['this_event'];
			}

			$month = $event['start']['month'];
			$mday = $event['start']['mday'];
			$year = $event['start']['year'];

			$user_timezone = phpgwapi_datetime::user_timezone();
			$start = mktime($event['start']['hour'],$event['start']['min'],$event['start']['sec'],$month,$mday,$year) - $user_timezone;
			$end = $this->bo->maketime($event['end']) - $user_timezone;

			$overlap = '';
			for($i=0;$i<count($overlapping_events);$i++)
			{
				$overlapped_event = $this->bo->read_entry($overlapping_events[$i]);
				if ( is_array($overlapped_event) )
				{
					$overlap .= '<li> ['.$this->bo->contacts->get_name_of_person_id($overlapped_event['owner']).'] ' . $this->link_to_entry($overlapped_event,$month,$mday,$year) . "</li>\n";
				}
				else //must be private
				{
					// this is a hack to get around the limitations of the current code
					// FIXME remove the need for such a nasty hack by adding a better arg to bocal::read_entry
					$overlapped_event = $this->bo->so->read_entry($overlapping_events[$i]);
					$overlap .= '<li> ['.$this->bo->contacts->get_name_of_person_id($overlapped_event['owner']).'] '. lang('private') . "</li>\n";
				}
			}

			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['calendar']['title'].' - '.lang('Scheduling Conflict');
			//$GLOBALS['phpgw']->common->phpgw_header();
			$this->header();

			$p = $GLOBALS['phpgw']->template;
			$p->set_file(
				array(
					'overlap'	=> 'overlap.tpl',
   					'form_button'	=> 'form_button_script.tpl'
				)
			);

			$var = array(
				'color'		=> $this->theme['bg_text'],
				'overlap_title' => lang('Scheduling Conflict'),
				'overlap_text'	=> lang('Your suggested time of <B> %1 - %2 </B> conflicts with the following existing calendar entries:',$GLOBALS['phpgw']->common->show_date($start),$GLOBALS['phpgw']->common->show_date($end)),
				'overlap_list'	=> $overlap
			);
			$p->set_var($var);

			$date = sprintf("%04d%02d%02d",$this->bo->year,$this->bo->month,$this->bo->mday);
			$var = array(
				'action_url_button'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.bocalendar.update', 'readsess' => 1)),
				'action_text_button'	=> lang('Ignore Conflict'),
				'action_confirm_button'	=> '',
				'action_extra_field'	=> ''
			);
			$this->output_template_array($p,'resubmit_button','form_button',$var);

			$var = array(
				'action_url_button'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'calendar.uicalendar.edit','readsess'=>1,'date'=>$date)),
				'action_text_button'	=> lang('Re-Edit Event'),
				'action_confirm_button'	=> '',
				'action_extra_field'	=> ''
			);
			$this->output_template_array($p,'reedit_button','form_button',$var);
			$p->pparse('out','overlap');
		}

		function planner_participants($parts)
		{
			static $id2lid;
			
			$names = '';

			if ( ! (is_array($parts) && count($parts)) )
			{
				return $name;
			}

			foreach ( $parts as $id => $status )
			{
				$status = substr($this->bo->get_long_status($status),0,1);
				
				if ( ! isset($id2lid[$id]) )
				{
					$id2lid[$id] = $this->bo->contacts->get_name_of_person_id($id);
				}
				
				if (strlen($names))
				{
					$names .= ', '; // \n breaks firefox
				}

				$names .= "{$id2lid[$id]} ({$status})";
			}
			if($this->debug)
			{
				echo '<!-- Inside participants() : '.$names.' -->'."\n";
			}
			return $names;
		}
			
		function planner_category($ids)
		{
			static $cats;
			if(!is_array($ids))
			{
				if (strpos($ids,','))
				{
					$id_array = explode(',',$ids);
				}
				else
				{
					$id_array[0] = $ids;
				}
			}
			//@reset($id_array);
			$ret_val = array();
			//while(list($index,$id) = each($id_array))
                        if (is_array($id_array))
                        {
                            foreach($id_array as $index => $id)
			{
				if (!isset($cats[$id]))
				{
					$cat_arr = $this->cat->return_single( $id );
					$cats[$id] = $cat_arr[0];
					$cats[$id]['color'] = strstr($cats[$id]['description'],'#');
				}
				$ret_val[] = $cats[$id];
			}
                        }
			return $ret_val;
		}

		function week_header($month, $year, $display_name = False)
		{
			$this->weekstarttime = phpgwapi_datetime::get_weekday_start($year,$month,1);

			$p = CreateObject('phpgwapi.template',$this->template_dir);
			$p->set_unknowns('remove');
			$p->set_file('month_header', 'month_header.tpl');

			$p->set_block('month_header','monthly_header','monthly_header');
			$p->set_block('month_header','column_title','column_title');

	//		$p->set_var($var);  // no $var is set

			if($display_name == True)
			{
				$p->set_var('col_title',lang('name'));
				$p->parse('column_header','column_title',True);
			}

			for($i=0;$i<7;$i++)
			{
				$p->set_var('col_title',lang(phpgwapi_datetime::$dow_fullnames[$i]));
				$p->parse('column_header','column_title',True);
			}
			return $p->fp('out','monthly_header');
		}

		function display_week_new($startdate,$weekly,$cellclass,$display_name = False,$owner=0,$monthstart=0,$monthend=0)
		{
			$this->week_type = 'new';
			return $this->display_week($startdate,$weekly,$cellclass,$display_name,$owner,$monthstart,$monthend);
		}

		function display_week($startdate, $weekly, $cellclass, $display_name = false, $owner = 0, $monthstart = 0, $monthend = 0)
		{
			if($owner == 0)
			{
				$owner = $GLOBALS['phpgw_info']['user']['account_id'];
			}

			$temp_owner = $this->bo->owner;

			$str = '';
			$p = CreateObject('phpgwapi.template',$this->template_dir);
			$p->set_unknowns('keep');
		
			$p->set_file(
				Array(
					'month_header'	=> 'month_header.tpl',
					'month_day'	=> 'month_day.tpl'
				)
			);

			$p->set_block('month_header','monthly_header','monthly_header');
			$p->set_block('month_header','month_column','month_column');
			$p->set_block('month_day','month_daily','month_daily');
			$p->set_block('month_day','day_event','day_event');
			$p->set_block('month_day','event','event');

			$p->set_var(array
			(
				'extra'		=> '',
				'cssclass'	=> $cellclass
			));

			if($display_name)
			{
				$p->set_var('column_data', $this->bo->contacts->get_name_of_person_id($owner));
				$p->parse('column_header','month_column',True);
			}

			$today = date('Ymd',phpgwapi_datetime::user_localtime());
			$daily = $this->set_week_array($startdate - phpgwapi_datetime::user_timezone(),$cellclass,$weekly);
			foreach($daily as $date => $day_params)
			{
				$year = intval(substr($date,0,4));
				$month = intval(substr($date,4,2));
				$day = intval(substr($date,6,2));
				$var = array(
						'column_data'	=> '',
						'extra'		=> ''
						);
				$p->set_var($var);
				if ($weekly || ($date >= $monthstart && $date <= $monthend))
				{
					if ($day_params['new_event'])
					{
						$new_event_link = ' <a href="'.$this->page('add',array('date'=>$date)).'">'
							. '<img src="'.$GLOBALS['phpgw']->common->image('calendar','new').'" width="10" height="10" alt="'.lang('New Entry').'" title="'.lang('New Entry').'" align="center">'
							. '</a>';
						$day_number = '<a href="'.$this->page('day',array('date'=>$date)).'">'.$day.'</a>';
					}
					else
					{
						$new_event_link = '';
						$day_number = $day;
					}

					$var = array(
							'extra'		=> $day_params['extra'],
							'new_event_link'=> $new_event_link,
							'day_number'	=> $day_number
							);
					if($day_params['week'])
					{
						$tmp_str = ($this->week_type == 'new') ? '&nbsp;' : '&nbsp; ';
						$var['new_event_link'] .= '<font size="-2">'. $tmp_str . '<a href="'.$this->page('week_new',array('date'=>$date)).'">' .$day_params['week'].'</a>';
					}

					$p->set_var($var);

					if(isset($day_params['holidays']) && is_array($day_params['holidays']) )
					{
						foreach($day_params['holidays'] as $key => $value)
						{
							$var = array
							(
								 'day_events' => "<span class=\"holiday\">{$value}</span>"
							);
							$this->output_template_array($p,'daily_events','event', $var);
						}
					}

					if($day_params['appts'] || $this->week_type == 'new')
					{
						$p->set_var(array('events' => ''));
						$events = (isset($this->bo->cached_events[$date])?$this->bo->cached_events[$date]:'');
						if ($this->week_type != 'new')
						{
							foreach($events as $event)
							{
								if ($this->bo->rejected_no_show($event) || !$this->bo->check_perms(PHPGW_ACL_READ))
								{
									continue;	// user does not want to see rejected events
								}
								$p->set_var('day_events',$this->link_to_entry($event,$month,$day,$year));
								$p->parse('events','event',True);
								$p->set_var('day_events','');
							}
						}
						elseif ($this->week_type == 'new')
						{
							$day_to_display =	array(
									'year'	=> $year,
									'month'	=> $month,
									'day'	=> $day
									);
							$p->set_var('day_events',$this->print_day($day_to_display));
							$p->parse('events','event',True);
							$p->set_var('day_events','');

						}
					}

					$p->parse('daily_events','day_event',True);
					$p->parse('column_data','month_daily',True);
					$p->set_var('daily_events','');
					$p->set_var('events','');
				}
				$p->parse('column_header','month_column',True);
				$p->set_var('column_data','');
			}
			$this->bo->owner = $temp_owner;
			return $p->fp('out','monthly_header');
		}
		
		function display_month($month,$year,$showyear,$owner=0)
		{
			$this->bo->store_to_cache(
				array(
					'syear'	=> $year,
					'smonth'=> $month,
					'sday'	=> 1
				)
			);

			$monthstart = intval(date('Ymd',mktime(0,0,0,$month    ,1,$year)));
			$monthend   = intval(date('Ymd',mktime(0,0,0,$month + 1,0,$year)));

			$start = phpgwapi_datetime::get_weekday_start($year, $month, 1);

			if($this->debug)
			{
				echo '<!-- display_month:monthstart = '.$monthstart.' -->'."\n";
				echo '<!-- display_month:start = '.date('Ymd H:i:s',$start).' -->'."\n";
			}

			$p = CreateObject('phpgwapi.template',$this->template_dir);
			$p->set_unknowns('keep');
		
			$p->set_file(
				array(
					'week'	=>	'month_day.tpl'
   				)
			);
			$p->set_block('week','m_w_table','m_w_table');
			$p->set_block('week','event','event');


			$var = array(
				'cols'      => 7,
				'day_events'=> $this->week_header($month,$year,False)
			);
			$this->output_template_array($p,'row','event',$var);

			$cnt = 0;
			$start = (int) $start;
			for ($i = ($start + phpgwapi_datetime::user_timezone()); date('Ymd',$i) <= $monthend; $i += 604800)
			{
				$cellclass = $cnt % 2 ? 'row_on' : 'row_off';
				$var = array
				(
					'day_events' => $this->display_week($i,False,$cellclass,False,$owner,$monthstart,$monthend)
				);
				$this->output_template_array($p,'row','event',$var);
				++$cnt;
			}
			return $p->fp('out','m_w_table');
		}

		function display_weekly($params)
		{
			$user_timezone = phpgwapi_datetime::user_timezone();
			if(!is_array($params))
			{
				$this->index();
			}
//_debug_array($params);
			$year = substr($params['date'],0,4);
			$month = substr($params['date'],4,2);
			$day = substr($params['date'],6,2);
			$showyear = $params['showyear'];
			$owners = $params['owners'];
			
			$p = CreateObject('phpgwapi.template',$this->template_dir);
			$p->set_unknowns('keep');

			$p->set_file(
				array
				(
					'week'	=> 'month_day.tpl'
				)
			);
			$p->set_block('week','m_w_table','m_w_table');
			$p->set_block('week','event','event');
		
			$start = phpgwapi_datetime::get_weekday_start($year, $month, $day) + $user_timezone;

			$cellcolor = $this->theme['row_off'];

			if(is_array($owners))
			{
				$display_name = True;
				$counter = count($owners);
				$owners_array = $owners;
				$cols = 8;
			}
			else
			{
				$display_name = False;
				$counter = 1;
				$owners_array[0] = $owners;
				$cols = 7;
			}

			$var = array(
			   'cols'         => $cols,
			   'day_events'   => $this->week_header($month,$year,$display_name)
			);

			$this->output_template_array($p,'row','event',$var);

			$tstart = $start - $user_timezone;
			$tstop = $tstart + 604800;
			$original_owner = $this->bo->so->owner;
			for($i=0;$i<$counter;$i++)
			{
				$this->bo->so->owner = $owners_array[$i];
				$this->bo->so->open_box($owners_array[$i]);
				$this->bo->store_to_cache(
					array(
						'syear'  => date('Y',$tstart),
						'smonth' => date('m',$tstart),
						'sday'   => date('d',$tstart),
						'eyear'  => date('Y',$tstop),
						'emonth' => date('m',$tstop),
						'eday'   => date('d',$tstop)
					)
				);
				$p->set_var('day_events',$this->display_week($start,True,$cellcolor,$display_name,$owners_array[$i]));
				$p->parse('row','event',True);
			}
			$this->bo->so->owner = $original_owner;
			return $p->fp('out','m_w_table');
		}

		function view_event($event,$alarms=False)
		{
			if(((!isset($event['participants'][$this->bo->owner]) || !$event['participants'][$this->bo->owner]) && !$this->bo->check_perms(PHPGW_ACL_READ,$event)))
			{
				return False;
			}

			$p = &$GLOBALS['phpgw']->template;
			$p->set_root(PHPGW_APP_TPL);
			$p->set_file(
				array(
					'view'	=> 'view.tpl'
				)
			);
			$p->set_block('view','view_event','view_event');
			$p->set_block('view','list','list');
			$p->set_block('view','hr','hr');

			$vars = $this->bo->event2array($event);

			$vars['title']['tr_color'] = $this->theme['th_bg'];
			foreach($vars['participants']['data'] as $user => $str)
			{
				if ($this->bo->check_perms(PHPGW_ACL_EDIT,0,$user) && preg_match('/^(.*) \((.*)\)$/',$str,$parts))
				{
					 $vars['participants']['data'][$user] = $parts[1].' (<a href="'.$this->page('edit_status',array('cal_id'=>$event['id'],'owner'=>$user)).'">'.$parts[2].'</a>)';
				}
			}
			$vars['participants']['data'] = implode("<br />\n",$vars['participants']['data']);
			
			$j =0;
			foreach($vars as $var)
			{
				if (strlen($var['data']))
				{
					$var['class'] = $j % 2 ? 'row_on' : 'row_off';
					$this->output_template_array($p,'row','list',$var);
					$j ++;
				}
			}

			if($alarms && count($event['alarm']))
			{
				$p->set_var('th_bg',$this->theme['th_bg']);
				$p->set_var('hr_text',lang('Alarms'));
				$p->parse('row','hr',True);

				foreach($event['alarm'] as $key => $alarm)
				{
					$icon = '<img src="'.$GLOBALS['phpgw']->common->image('calendar',($alarm['enabled']?'enabled':'disabled')).'" width="13" height="13">';
					$var = array(
						'field'	=> $icon . '&nbsp;' . $GLOBALS['phpgw']->common->show_date($alarm['time']),
						'data'	=> 'Email Notification'
					);
					$this->output_template_array($p,'row','list',$var);
				}
			}
			return True;
		}

		function nm_on_off()
		{
			if($GLOBALS['phpgw']->nextmatchs->alternate_row_class() == $this->theme['row_on'])
			{
				return '_on';
			}
			return '_off';
		}

		function slot_num($time,$set_day_start=0,$set_day_end=0)
		{
			static $day_start, $day_end, $interval=0;
			
			if ($set_day_start) $day_start = $set_day_start;
			if ($set_day_end)   $day_end   = $set_day_end;
			if (!$interval)     $interval  = 60*$this->bo->prefs['calendar']['interval'];
			
			if ($time > $day_end)
			{
				$time = $day_end;
			}
			$slot = intval(($time - $day_start) / $interval);
			
			return $slot < 0 ? 0 : 1+$slot;
		}
		
		function print_day($params)
		{
			if(!is_array($params))
			{
				$this->index();
			}

			print_debug('in print_day()');

			$this->bo->store_to_cache(
				array(
					'syear'  => $params['year'],
					'smonth' => $params['month'],
					'sday'   => $params['day'],
					'eyear'  => $params['year'],
					'emonth' => $params['month'],
					'eday'   => $params['day']
				)
			);

			$p = CreateObject('phpgwapi.template',$this->template_dir);
			$p->set_unknowns('keep');

			$templates = array(
				'day_cal'	=> 'day_cal.tpl'
			);

			$p->set_file($templates);
			$p->set_block('day_cal','day','day');
			$p->set_block('day_cal','day_row','day_row');
			$p->set_block('day_cal','day_event_on','day_event_on');
			$p->set_block('day_cal','day_event_off','day_event_off');
			$p->set_block('day_cal','day_event_holiday','day_event_holiday');
			$p->set_block('day_cal','day_time','day_time');
			
			$date_to_eval = sprintf("%04d%02d%02d",$params['year'],$params['month'],$params['day']);

//			$day_start = mktime(intval($this->bo->prefs['calendar']['workdaystarts']),0,0,$params['month'],$params['day'],$params['year']);
//			$day_end = mktime(intval($this->bo->prefs['calendar']['workdayends']),0,1,$params['month'],$params['day'],$params['year']);
			$day_start = mktime(intval($this->bo->prefs['calendar']['workdaystarts']),0,0,0,0,0);
			$day_end = mktime(intval($this->bo->prefs['calendar']['workdayends']),0,1,0,0,0);

			$daily = $this->set_week_array(phpgwapi_datetime::get_weekday_start($params['year'],$params['month'],$params['day']),$this->theme['row_on'],True);
			print_debug('Date to Eval',$date_to_eval);
			$rows = array();
			$last_slot = $this->slot_num($day_end,$day_start,$day_end);
			if($daily[$date_to_eval]['appts'])
			{
				$events = $this->bo->cached_events[$date_to_eval];
				print_debug('Date',$date_to_eval);
				print_debug('Count',count($events));
				$last_slot_end = -1;
				foreach($events as $event)
				{

## by tb: 	we don't want a user to be able to see a event if he has no permission to read it !!!
## 				this shoud be in class.bocalendar->get_cached_events() !!!
					#echo '<br><b>check_perms: |'.$this->bo->check_perms(PHPGW_ACL_READ).'|</b><br>';
					if(!$this->bo->check_perms(PHPGW_ACL_READ))
					{
						continue;
##						echo '<center>'.lang('You do not have permission to read this record!').'</center>'."\n";
##						$GLOBALS['phpgw']->common->phpgw_exit(True);
					}
## by tb
					if ($this->bo->rejected_no_show($event))
					{
						continue;	// user does not want to see rejected events
					}
					if (isset($event['recur_type']) && $event['recur_type'])	// calculate start- + end-datetime for recuring events
					{
						$this->bo->set_recur_date($event,$date_to_eval);
					}
					$starttime = $this->bo->maketime($event['start']);
					$endtime = $this->bo->maketime($event['end']);
					$slot = $this->slot_num($starttime,mktime(intval($this->bo->prefs['calendar']['workdaystarts']),0,0,$event['start']['month'],$event['start']['mday'],$event['start']['year']),mktime(intval($this->bo->prefs['calendar']['workdaystarts']),0,0,$event['end']['month'],$event['end']['mday'],$event['end']['year']));
					$slot_end = $this->slot_num($endtime-1);	// -1 to no occupy eg. the 18.00 slot for a 17-18h date

					if ($slot <= $last_slot_end)
					{
						$slot = $last_slot;
						$slot_end = max($last_slot_end,$slot_end);
					}
					if(isset($rows[$slot]))
					{
						$rows[$slot] .= $this->link_to_entry($event,$params['month'],$params['day'],$params['year']);
					}
					else
					{
						$rows[$slot] = $this->link_to_entry($event,$params['month'],$params['day'],$params['year']);					
					}

## by tb  --> old cat-colors: has problems with overlaying events
#					$tmp_cats = explode(',',$event['category']);
#					$tmp_cat  = $tmp_cats[0];
#					$color[$slot] = ( $this->cat_colors[$tmp_cat]) ? $this->cat_colors[$tmp_cat] : 'noColor';
## by tb
					print_debug('slot',$slot);
					print_debug('row',$rows[$slot]);

					$row_span[$slot] = 1 + $slot_end - $slot;
					
					$last_slot = $slot;
					$last_slot_end = $slot_end;
					$user_timezone = phpgwapi_datetime::user_timezone();
					print_debug('Time',$GLOBALS['phpgw']->common->show_date($this->bo->maketime($event['start']) - $user_timezone).' - '.$GLOBALS['phpgw']->common->show_date($this->bo->maketime($event['end']) - $user_timezone));
					print_debug('Slot',$slot);
				}
				//echo "rows=<pre>"; print_r($rows); echo "<br />row_span="; print_r($row_span); echo "</pre>\n";
			}
			$holiday_names = $daily[$date_to_eval]['holidays'];
			if(!$holiday_names)
			{
				$row_to_print = $this->nm_on_off();
			}
			else
			{
				$row_to_print = '_holiday';
				foreach($holiday_names as $name)
				{
					$rows[0] = '<center>'.$name.'</center>' . $rows[0];
				}
			}
			$last_slot = $this->slot_num($day_end,$day_start,$day_end);
			$rowspan = 0;
			for ($slot = 0; $slot <= $last_slot; ++$slot)
			{
				$p->set_var('extras','');
				if ($rowspan > 1)
				{
					$p->set_var('item','');
					$rowspan--;
				}
				elseif (!isset($rows[$slot]))
				{
					$p->set_var('event','&nbsp;');
					$row_to_print = $this->nm_on_off();
					$p->parse('item','day_event'.$row_to_print,False);
				}
				else
				{
					$rowspan = intval($row_span[$slot]);
					if ($rowspan > 1)
					{
## by tb --> old catcolors: has problems with overlaying event
#						$p->set_var('extras',' rowspan="'.$rowspan.'"'.' style="border-left:3px solid '.$color[$slot].';padding:3px;vertical-align:top;" ');
						$p->set_var('extras',' rowspan="'.$rowspan.'"'.' style="vertical-align:top;" ');
## END - by tb
					}
## by tb --> old catcolors: has problems with overlaying event
					else
					{
#						$p->set_var('extras',' style="border-left:3px solid '.$color[$slot].';padding:3px;vertical-align:top;" ');
						$p->set_var('extras',' style="border-left:vertical-align:top;" ');
					}
## END - by tb

					$p->set_var('event',$rows[$slot]);
					$row_to_print = $this->nm_on_off();
					$p->parse('item','day_event'.$row_to_print,False);
				}
				$open_link = $close_link = '';
				$time = '&nbsp;';

				if (0 < $slot && $slot < $last_slot)	// normal time-slot not before or after day_start/end
				{
					$time = $day_start + ($slot-1) * 60 * $this->bo->prefs['calendar']['interval'];
					$hour = date('H',$time);
					$min  = date('i',$time);
					$time = phpgwapi_datetime::formattime($hour,$min);

## by tb
					$open_link = ($this->week_type != 'new') ? ' - ' : '';
## by tb
					if ( $this->bo->check_perms(PHPGW_ACL_ADD) )
					{
						$open_link .= '<a href="'.$this->page('add',array('date'=>$date_to_eval,'hour'=>$hour,'minute'=>$min)).'">';
						$close_link = '</a>';
					}
				}
				$var = array(
					'open_link'  => $open_link,
					'time'       => $time,
					'close_link' => $close_link,
					'tr_color'   => ''	// dummy to stop output_template_array to set it
				);
				

## by tb
				if ($this->week_type != 'new' ||
					( $this->week_type == 'new' && $this->bo->prefs['calendar']['show_time_line'] == '1' )
				   )
				{
					$this->output_template_array($p,'item','day_time',$var);
    			}
				$p->parse('row','day_row',True);
			}

			$this->time_line_saved = TRUE;
## END - by tb
			return $p->fp('out','day');
		}	// end funct. - print_day($params)

		function timematrix($param)
		{
			if(!is_array($param))
			{
				$this->index();
			}

			$date = $param['date'];
			$starttime = $param['starttime'];
			$endtime = $param['endtime'];
			$participants = $param['participants'];
			foreach($participants as $part => $nul)
			{
				$participants[$part] = $this->bo->contacts->get_name_of_person_id($part);
			}
			uasort($participants,'strnatcasecmp');	// sort them after their fullname

			if(!isset($this->bo->prefs['calendar']['interval']))
			{
				$this->bo->prefs['calendar']['interval'] = 15;
				$GLOBALS['phpgw']->preferences->add('calendar','interval',15);
				$GLOBALS['phpgw']->preferences->save_repository();
			}
			$increment = $this->bo->prefs['calendar']['interval'];
			$interval = (int)(60 / $increment);

	//		$pix = $GLOBALS['phpgw']->common->image('calendar','pix');

			$str = '<center><font size="+1" color="#000000"><b>'.lang($GLOBALS['phpgw']->common->show_date($date['raw'],'l'))
				. ', '.$this->bo->long_date($date).'</b></font><br />'
				. '<table width="85%" cellspacing="0" cellpadding="0" cols="'.((24 * $interval) + 1).'">'
	//			. '<tr><td height="1" colspan="'.((24 * $interval) + 1).'" bgcolor="black"><img src="'.$pix.'"></td></tr>'
				. '<tr><td height="1" colspan="'.((24 * $interval) + 1).'" bgcolor="black"></td></tr>'
				. '<tr><td width="15%"><font color="'.$this->theme['bg_text'].'" face="'.$this->theme['font'].'" size="-2">'.lang('Participant').'</font></td>';
			for($i=0;$i<24;$i++)
			{
				for($j=0;$j<$interval;$j++)
				{
					$k = ($j == 0 ? sprintf('%02d',$i).'<br />':'').sprintf('%02d',$j*$increment);
					
					$str .= '<td align="left" bgcolor="'.$this->theme['bg_color'].'">'
						. '<a href="'.$this->page('add',array('date'=>$date['full'],'hour'=>$i,'minute'=>($interval * $j))) ."\" onMouseOver=\"window.status='".$i.':'.(($increment * $j)<=9?'0':'').($increment * $j)."'; return true;\">"
						. $k."</a></td>\n";
				}
			}
			$str .= '</tr>'
//				. '<tr><td height="1" colspan="'.((24 * $interval) + 1).'" bgcolor="black"><img src="'.$pix.'"></td></tr>';
				. '<tr><td height="1" colspan="'.((24 * $interval) + 1).'" bgcolor="black"></td></tr>';
			if(!$endtime)
			{
				$endtime = $starttime;
			}
			$owner = $this->bo->owner;
			foreach($participants as $part => $fullname)
			{
				$str .= '<tr align="center">'
					. '<td width="15%" align="left"><font color="'.$this->theme['bg_text'].'" face="'.$this->theme['font'].'" size="-2">'.$fullname.'</font></td>';

				$this->bo->cached_events = array();
				$this->bo->so->owner = $part;
				$this->bo->so->open_box($part);
				$this->bo->store_to_cache(
					array(
						'syear'	=> $date['year'],
						'smonth'=> $date['month'],
						'sday'	=> $date['day'],
						'eyear'	=> 0,
						'emonth'=> 0,
						'eday'	=> $date['day'] + 1
					)
				);

				if(!isset($this->bo->cached_events[$date['full']]) || !$this->bo->cached_events[$date['full']])
				{
					for($j=0;$j<24;$j++)
					{
						for($k=0;$k<$interval;$k++)
						{
							$str .= '<td height="1" align="left" bgcolor="'.$this->theme['bg_color'].'" color="#999999">&nbsp;</td>';
						}
						$str .= "\n";
					}
				}
				else
				{
					$time_slice = $this->bo->prepare_matrix($interval,$increment,$part,$date['full']);
					for($h=0;$h<24;$h++)
					{
						$hour = $h * 10000;
						for($m=0;$m<$interval;$m++)
						{
							$index = ($hour + (($m * $increment) * 100));
							switch($time_slice[$index]['marker'])
							{
								case '&nbsp':
								//	$time_slice[$index]['color'] = $this->theme['bg_color'];
									$time_slice[$index]['class'] = '';
									$extra = '';
									break;
								case '-':
								//	$time_slice[$index]['color'] = $this->theme['bg01'];
									$time_slice[$index]['class'] = 'th';
									$link = $this->page('view',array('cal_id'=>$time_slice[$index]['id'],'date'=>$date['full']));
									$extra =' title="'.$time_slice[$index]['description'].'" onClick="location.href=\''.$link.'\';" style="cursor:pointer; cursor:hand;"';
									break;
							}
							$str .= '<td class="'.$time_slice[$index]['class'].'" color="#999999"'.$extra.'>'.$time_slice[$index]['marker'].'</td>';
						}
						$str .= "\n";
					}
				}
				$str .= '</tr>'
	//				. '<tr><td height="1" colspan="'.((24 * $interval) + 1).'" bgcolor="#999999"><img src="'.$pix.'"></td></tr>';
					. '<tr><td height="1" colspan="'.((24 * $interval) + 1).'" bgcolor="#999999"></td></tr>';
			}
			$this->bo->owner = $owner;
			$this->bo->so->owner = $owner;
			$this->bo->so->open_box($owner);
			return $str.'</table></center>'."\n";
		}      

		function get_response($cal_id)
		{
			$p = &$GLOBALS['phpgw']->template;
			$p->set_file(
				array(
  					'form_button'	=> 'form_button_script.tpl'
				)
			);

			$ev = $this->bo->get_cached_event();
			$response_choices = array(
				ACCEPTED	=> lang('Accept'),
				REJECTED	=> lang('Reject'),
				TENTATIVE	=> lang('Tentative'),
				NO_RESPONSE	=> lang('No Response')
			);
			$str = '';
			//while(list($param,$text) = each($response_choices))
                        foreach($response_choices as $param => $text)
			{
				$var = array(
					'action_url_button'	=> $this->page('set_action',array('cal_id'=>$cal_id,'action'=>$param)),
					'action_text_button'	=> '  '.$text.'  ',
					'action_confirm_button'	=> '',
					'action_extra_field'	=> ''
				);
				$p->set_var($var);
				$str .= '<td>'.$p->fp('out','form_button').'</td>'."\n";
			}
			if ($this->bo->return_to)
			{
				$var = array(
					'action_url_button'	=> $GLOBALS['phpgw']->link('/index.php', $this->bo->return_to),
					'action_text_button'	=> lang('cancel'),
					'action_confirm_button'	=> '',
					'action_extra_field'	=> ''
				);
				$p->set_var($var);
				$str .= '<td>'.$p->fp('out','form_button').'</td>'."\n";
			}
			$str = '<td><b>'.$GLOBALS['phpgw']->common->grab_owner_name($this->bo->owner).":</b></td>\n".$str;

			return '<table width="100%"><tr align="center">'."\n".$str.'</tr></table>'."\n";
		}

		function participants_popup()
		{
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$t =& $GLOBALS['phpgw']->template;
			$t->set_unknowns('remove');

			$lookup = trim(phpgw::get_var('lookup', 'string', 'GET'));

			if ( strlen($lookup) > 2 || $lookup == '*' )
			{
				$lookup = preg_replace('/\*/', '/%/', $lookup);

				$cat_id = phpgw::get_var('cat_id', 'int', 'GET');

				if ( $cat_id == -1 ) //groups
				{
					$contacts = $this->bo->get_groups($lookup);
					foreach ( $contacts as $contact )
					{
						echo <<<HTML
							<option value="{$contact['contact_id']}">{$contact['per_first_name']} {$contact['per_last_name']} (g)</option>

HTML;
					}
				}
				else
				{
					//FIXME define constants somewhere and use them skwashd Nov07
					switch( phpgw::get_var('con_type', 'int', 'GET') )
					{
						
						case 2: //orgs
							$contacts = $this->bo->get_org_contacts($lookup, $cat_id);
							foreach ( $contacts as $contact )
							{
								echo <<<HTML
								<option value="{$contact['contact_id']}">{$contact['org_name']} (o)</option>

HTML;
							}
							break;
						default: //assume person
							$contacts = $this->bo->get_per_contacts($lookup, $cat_id);
							if ( !is_array($contacts) || !count($contacts) )
							{
								return '';
							}
							
							foreach ( $contacts as $contact )
							{
								echo <<<HTML
								<option value="{$contact['contact_id']}">{$contact['per_first_name']} {$contact['per_last_name']}</option>

HTML;
							}
					}
				}
				

				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			
			$t->set_root(PHPGW_APP_TPL);
			$t->set_file( array('popup' => 'contacts_popup.tpl') );
			$t->set_block('popup', 'cat_option', 'cat_options');

			$cats = createObject('phpgwapi.categories');
			$cats->app_name = 'addressbook';
			$cat_list = $cats->return_array('all', 0, false);

			if ( !is_array($cat_list) ) // deal with warning when no cats exist
			{
				$cat_list = array();
			}
			
			foreach ( $cat_list as $cat )
			{
				$t->set_var(
					array
					(
						'cat_id'	=> $cat['id'],
						'cat_name'	=> stripslashes($cat['name'])
					));
				$t->parse('cat_options', 'cat_option', true);
			}
			
			$t->set_var(
				array
				(
					'cat_id'	=> -1,
					'cat_name'	=> lang('phpgroupware groups')
				));
			$t->parse('cat_options', 'cat_option', true);
			
			$t->set_var(array
			(
				'webserver_url'		=> $GLOBALS['phpgw_info']['server']['webserver_url'],
				'base_url'			=> $GLOBALS['phpgw']->link('/'),
				'lang_all'			=> lang('all'),
				'lang_add'			=> lang('add'),
				'lang_category'		=> lang('category'),
				'lang_close'		=> lang('close'),
				'lang_contacts'		=> lang('contacts'),
				'lang_org'			=> lang('organization'),
				'lang_person'		=> lang('person'),
				'lang_remove'		=> lang('remove'),
				'lang_show_contacts'=> lang('show contacts'),
				'lang_search'		=> lang('search'),
				'lang_title'		=> lang('calendar event participants selector'),
				'lang_type'			=> lang('type'),
				'img_add'			=> $GLOBALS['phpgw']->common->image('calendar', 'right-24x24'),
				'img_remove'		=> $GLOBALS['phpgw']->common->image('calendar', 'left-24x24'),
				'img_close'			=> $GLOBALS['phpgw']->common->image('calendar', 'close-24x24')
			));
			$t->pparse('out', 'popup');
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function edit_form($param)
		{
			if(!is_array($param))
			{
				$this->index();
			}

			$str = '';
			if(isset($param['event']))
			{
				$event = $param['event'];
			}

			$hourformat = substr($this->bo->users_timeformat,0,1);
			
			// $sb = CreateObject('phpgwapi.sbox');
			$sb = CreateObject('phpgwapi.sbox');
			$jscal = CreateObject('phpgwapi.jscalendar');

			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
			$GLOBALS['phpgw_info']['flags']['app_header'] = $event['id'] ? lang('Calendar - Edit') : lang('Calendar - Add');
			$this->header(false);

			$p = &$GLOBALS['phpgw']->template;
			$p->set_file(array(
				'edit'		=> 'edit.tpl',
				'form_button'	=> 'form_button_script.tpl'
			));
			$p->set_block('edit','edit_entry','edit_entry');
			$p->set_block('edit','list','list');
			$p->set_block('edit','hr','hr');

// Recurrence Exeptions
			$recur_exception = '';
			if (isset($event['recur_exception']) && $event['recur_exception']>0)
			{
				$recur_exception = implode (",", $event['recur_exception']);
			}
	
			$cal_id = phpgw::get_var('cal_id', 'int', 'GET');
			//FIXME this is ugly and should use templates better

			$common_hidden = <<<HTML
				<input type="hidden" name="cal[id]" value="{$event['id']}" />
				<input type="hidden" name="cal[owner]" value="{$event['owner']}" />
				<input type="hidden" name="recur_exception" value="{$recur_exception}" />
HTML;

			if (  $cal_id && $event['id'] == 0 )
			{
				$common_hidden .= <<<HTML
				<input type="hidden" name="cal[reference]" value="{$cal_id}">

HTML;
			}
			else if ( isset($event['reference']) )
			{
				$common_hidden .= <<<HTML
				<input type="hidden" name="cal[reference]" value="{$event['reference']}">

HTML;
			}

			if ( isset($GLOBALS['phpgw_info']['server']['deny_user_grants_access']) && $GLOBALS['phpgw_info']['server']['deny_user_grants_access'] )
			{
				$common_hidden .= <<<HTML
				<input type="hidden" name="participants[]" value="{$this->bo->owner}">

HTML;
			}

			$vars = array
			(
				'action_url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'calendar.bocalendar.update')),
				'popup_url'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.participants_popup'), true),
				'common_hidden'	=> $common_hidden,
				'errormsg'		=> $param['cd'] ? $GLOBALS['phpgw']->common->check_code($param['cd']) : ''
			);
			$p->set_var($vars);

// Brief Description
			$var['title'] = array(
				'tr_color' => $this->theme['th_bg'],
				'field'	=> lang('Title'),
				'data'	=> '<input name="cal[title]" size="45" maxlength="80" value="'.$event['title'].'">'
			);

// Full Description
			$var['description'] = array(
				'field'	=> lang('Full Description'),
				'data'	=> '<textarea name="cal[description]" rows="5" cols="40" wrap="virtual" maxlength="2048">'.$event['description'].'</textarea>'
			);

// Display Categories
			if(isset($event['category']) && strpos($event['category'],','))
			{
				$temp_cats = explode(',', $event['category']);
				foreach ( $temp_cats as $value )
				{
					$check_cats[] = (int) $value;
				}
			}
			elseif(isset($event['category']) && $event['category'])
			{
				$check_cats[] = (int) $event['category'];
			}
			else
			{
				$check_cats[] = 0;
			}
			$var['category'] = array(
				'field'	=> lang('Category'),
				'data'	=> '<select name="categories[]" multiple size="5">'.$this->cat->formated_list('select','all',$check_cats,True).'</select>'
			);

// Location
			$var['location'] = array(
				'field'	=> lang('Location'),
				'data'	=> '<input name="cal[location]" size="45" maxlength="255" value="'.$event['location'].'">'
			);

// Date
			$user_timezone = phpgwapi_datetime::user_timezone();

			$start = $this->bo->maketime($event['start']) - $user_timezone;
			$var['startdate'] = array(
				'field'	=> lang('Start Date'),
/*
				'data'	=> $GLOBALS['phpgw']->common->dateformatorder(
				   $sb->getYears('start[year]',intval($GLOBALS['phpgw']->common->show_date($start,'Y'))),
				   $sb->getMonthText('start[month]',intval($GLOBALS['phpgw']->common->show_date($start,'n'))),
				   $sb->getDays('start[mday]',intval($GLOBALS['phpgw']->common->show_date($start,'d')))
				)
*/
				'data' => $jscal->input('start[str]',$start)
			);

// Time
			if ($this->bo->prefs['common']['timeformat'] == '12')
			{
				$str .= '<input type="radio" name="start[ampm]" value="am"'.($event['start']['hour'] >= 12?'':' checked').'>am'."\n"
					. '<input type="radio" name="start[ampm]" value="pm"'.($event['start']['hour'] >= 12?' checked':'').'>pm'."\n";
			}
			$var['starttime'] = array(
				'field'	=> lang('Start Time'),
				'data'	=> '<input name="start[hour]" size="2" VALUE="'.$GLOBALS['phpgw']->common->show_date($start,$hourformat).'" maxlength="2">:<input name="start[min]" size="2" value="'.$GLOBALS['phpgw']->common->show_date($start,'i').'" maxlength="2">'."\n".$str
			);

// End Date
			$end = $this->bo->maketime($event['end']) - $user_timezone;
			$var['enddate'] = array(
				'field'	=> lang('End Date'),
/*
				'data'	=> $GLOBALS['phpgw']->common->dateformatorder(
				   $sb->getYears('end[year]',intval($GLOBALS['phpgw']->common->show_date($end,'Y'))),
				   $sb->getMonthText('end[month]',intval($GLOBALS['phpgw']->common->show_date($end,'n'))),
				   $sb->getDays('end[mday]',intval($GLOBALS['phpgw']->common->show_date($end,'d')))
				)
*/
				'data' => $jscal->input('end[str]',$end)
			);

// End Time
			if ($this->bo->prefs['common']['timeformat'] == '12')
			{
				$str = '<input type="radio" name="end[ampm]" value="am"'.($event['end']['hour'] >= 12?'':' checked').'>am'."\n"
					. '<input type="radio" name="end[ampm]" value="pm"'.($event['end']['hour'] >= 12?' checked':'').'>pm'."\n";
			}
			$var['endtime'] = array(
				'field'	=> lang('End Time'),
				'data'	=> '<input name="end[hour]" size="2" VALUE="'.$GLOBALS['phpgw']->common->show_date($end,$hourformat).'" maxlength="2">:<input name="end[min]" size="2" value="'.$GLOBALS['phpgw']->common->show_date($end,'i').'" maxlength="2">'."\n".$str
			);

// Priority
			$var['priority'] = array(
				'field'	=> lang('Priority'),
				'data'	=> $sb->getPriority('cal[priority]',$event['priority'])
			);

// Access
			$var['access'] = array(
				'field'	=> lang('Private'),
				'data'	=> '<input type="checkbox" name="cal[private]" value="private"'.(!$event['public']?' checked':'').'>'
			);

// Participants
			if ( !is_array($event['participants']) )
			{
				$event['participants'] = array();
			}
			
			$str = '';
			foreach ( $event['participants'] as $id => $status )
			{
				if($id != intval($event['owner']))
				{
					$str .= '<option value="' . $id . $status . '" selected="selected">'
							. $this->bo->contacts->get_name_of_person_id($id) . '</option>' . "\n"; 
				}
			}
			$var['participants'] = array
			(
				'field'	=> '<input type="button" value="' . lang('Participants') . '" onClick="accounts_popup();">',
				//		. '<input type="hidden" name="accountid" value="' . $accountid . '" />',
				'data'	=> "\n".'   <select id="event_participants" name="participants[]" multiple="multiple" size="7">' 
						. "\n{$str}</select>"
			);
			
// I Participate
			if((($event['id'] > 0) && isset($event['participants'][$event['owner']])) || !$event['id'])
			{
				$checked = ' checked="checked"';
			}
			else
			{
				$checked = '';
			}
			$var['owner'] = array(
				'field'	=> lang('%1 participates', $this->bo->contacts->get_name_of_person_id($event['owner']) ),
				'data'	=> '<input type="checkbox" name="participants[]" value="'.$event['owner'].'A"'.$checked.'>'
			);
// Reminder
			// The user must use "Alarm Management" to change/modify an alarm
			// so only display the email reminder fields if this is a new event
			// i.e. not editing an existing event

			if ($event['id'] == 0) {
				// get defaults
				$days = (isset($this->bo->prefs['calendar']['default_email_days'])?$this->bo->prefs['calendar']['default_email_days']:'');
				$hours = (isset($this->bo->prefs['calendar']['default_email_hours'])?$this->bo->prefs['calendar']['default_email_hours']:'');
				$min = (isset($this->bo->prefs['calendar']['default_email_min'])?$this->bo->prefs['calendar']['default_email_min']:'');
				if (isset($event['alarm']) && count($event['alarm']) > 1)
				{
					// this should not happen because when creating a new event
					// only 1 alarm is displayed on the screen
					// if the user wants more than 1 alarm they should
					// use "Alarm Management"
					echo '<!-- how did this happen, too many alarms -->'."\n";
				}
				// if there was an error pick up what the user entered
				if (is_array($event['alarm']))
				{
					@reset($event['alarm']);
					// just get the first one see above!!!
					//list($key,$alarm) = @each($event['alarm']);
					$key = key($event['alarm']);
					$alarm = current($event['alarm']);
					
					$diff = $start - $alarm['time'];
					$days = intval($diff / (24*3600));
					$hours = intval(($diff - ($days * 24 * 3600))/3600);
					$min = intval(($diff - ($days * 24 * 3600) - ($hours * 3600))/60);
				} 

				// days
				$dout = '<select name="cal[alarmdays]">'."\n";
				for($i=0;$i<32;$i++)
				{
					$dout .= '<option value="'.$i.'"'.($i==$days?' selected':'').'>'.$i.'</option>'."\n";
				}
				$dout .= '</select>'."\n".' '.lang('days').' ';
				// hours
				$hout = '<select name="cal[alarmhours]">'."\n";
				for($i=0;$i<25;$i++)
				{
					$hout .= '<option value="'.$i.'"'.($i==$hours?' selected':'').'>'.$i.'</option>'."\n";
				}
				$hout .= '</select>'."\n".' '.lang('hours').' ';
				// minutes
				$mout = '<select name="cal[alarmminutes]">'."\n";
				for($i=0;$i<61;$i++)
				{
					$mout .= '<option value="'.$i.'"'.($i==$min?' selected':'').'>'.$i.'</option>'."\n";
				}
				$mout .= '</select>'."\n".' '.lang('minutes').' ';

				$var['alarm'] = array(
					'field' => lang('Alarm'),
					'data'	=> $dout.$hout.$mout.lang('before the event')
				);

			}

// Repeat Type
			$str = '';
			foreach($this->bo->rpt_type as $type => $label)
			{
				$str .= '<option value="'.$type.'"'.($event['recur_type']==$type?' selected':'').'>'.lang($label).'</option>';
			}
			$var['recure_type'] = array(
				'field'	=> lang('Repeat Type'),
				'data'	=> '<select name="cal[recur_type]">'."\n".$str.'</select>'."\n"
			);

			if((isset($event['recur_enddate']['year']) && $event['recur_enddate']['year'] != 0)
				&& (isset($event['recur_enddate']['month']) && $event['recur_enddate']['month'] != 0)
				&& (isset($event['recur_enddate']['mday']) && $event['recur_enddate']['mday'] != 0))
			{
				$checked = ' checked';
				$recur_end = $this->bo->maketime($event['recur_enddate']) - $user_timezone;
			}
			else
			{
				$checked = '';
				$recur_end = $this->bo->maketime($event['start']) + phpgwapi_datetime::SECONDS_IN_DAY - $user_timezone;
			}
	
			$var['recure_enddate'] = array(
				'field'	=> lang('Repeat End Date'),
				'data'	=> '<input type="checkbox" name="cal[rpt_use_end]" value="y"'.$checked.'>'.lang('Use End Date').'  '.
/*
					$GLOBALS['phpgw']->common->dateformatorder(
						$sb->getYears('recur_enddate[year]',intval($GLOBALS['phpgw']->common->show_date($recur_end,'Y'))),
						$sb->getMonthText('recur_enddate[month]',intval($GLOBALS['phpgw']->common->show_date($recur_end,'n'))),
						$sb->getDays('recur_enddate[mday]',intval($GLOBALS['phpgw']->common->show_date($recur_end,'d')))
					)
*/
					$jscal->input('recur_enddate[str]',$recur_end)
			);

			$i = 0; $boxes = '';
			foreach ($this->bo->rpt_day as $mask => $name)
			{
				$boxes .= '<input type="checkbox" name="cal[rpt_day][]" value="'.$mask.'"'.(isset($event['recur_data']) && $event['recur_data'] & $mask ? ' checked' : '').'>&nbsp;'.lang($name)."\n";
				if (++$i == 5) $boxes .= '<br />';
			}
			$var['recure_day'] = array(
				'field'	=> lang('Repeat Day').'<br />'.lang('(for weekly)'),
				'data'	=> $boxes
			);

			$var['recure_interval'] = array(
				'field'	=> lang('Interval'),
				'data'	=> '<input name="cal[recur_interval]" size="4" maxlength="4" value="'.(isset($event['recur_interval'])?$event['recur_interval']:'').'">'
			);

			if (!isset($this->fields))
			{
				$this->custom_fields = CreateObject('calendar.bocustom_fields');
				$this->fields = &$this->custom_fields->fields;
				$this->stock_fields = &$this->custom_fields->stock_fields;
			}
			$preserved = False;
			foreach($this->fields as $field => $data)
			{
				if (!isset($data['disabled']) || !$data['disabled'])
				{
					if (isset($var[$field]))
					{
						switch($field)
						{
							case 'startdate':
								$this->output_template_array($p,'row','list',$var['startdate']);
								$this->output_template_array($p,'row','list',$var['starttime']);
								break;
							case 'enddate':
								$this->output_template_array($p,'row','list',$var['enddate']);
								$this->output_template_array($p,'row','list',$var['endtime']);
								break;
							case 'recure_type':
								$p->set_var('tr_color',$this->theme['th_bg']);
								$p->set_var('hr_text','<center><b>'.lang('Repeating Event Information').'</b></center>');
								$p->parse('row','hr',True);
								$this->output_template_array($p,'row','list',$var['recure_type']);
								$this->output_template_array($p,'row','list',$var['recure_enddate']);
								$this->output_template_array($p,'row','list',$var['recure_day']);
								$this->output_template_array($p,'row','list',$var['recure_interval']);
								break;
							default:
								$this->output_template_array($p,'row','list',$var[$field]);
						}
					}
					elseif (!isset($this->stock_fields[$field]))	// Custom field
					{
						$lang = lang($name = substr($field,1));
						$size = 'SIZE='.($data['shown'] ? $data['shown'] : ($data['length'] ? $data['length'] : 30)).
							' MAXLENGTH='.($data['length'] ? $data['length'] : 255);
						$v = array(
							'field'	=> $lang == $name.'*' ? $name : $lang,
							'data'	=> '<input name="cal['.htmlspecialchars($field).']" '.$size.' value="'.$event['#'.$name].'">'
						);
						if ($data['title'])
						{
							$v['tr_color'] = $this->theme['th_bg'];
						}
						if (!$data['length'] && $data['title'])
						{
							$p->set_var('tr_color',$this->theme['th_bg']);
							$p->set_var('hr_text','<center><b>'.$v['field'].'</b></center>');
							$p->parse('row','hr',True);
						}
						else
						{
							$this->output_template_array($p,'row','list',$v);
						}
					}
				}
				else	// preserve disabled fields
				{
					switch ($field)
					{
						case 'owner':
							$preserved[$field] = $event['id'] ? $event['participants'][$event['owner']] : 'A';
							break;
						case 'recure_type':
							foreach(array('recur_type','recur_enddate','recur_data','recur_interval') as $field)
							{
								$preserved[$field] = $event[$field];
							}
							break;
						case 'startdate':
						case 'enddate':
							$field = substr($field,0,-4);
						default:
							$preserved[$field] = $event[$field];
					}
				}
			}
			unset($var);
			if (is_array($preserved))
			{
				//echo "preserving<pre>"; print_r($preserved); echo "</pre>\n";
				$p->set_var('common_hidden',$p->get_var('common_hidden').'<input type="hidden" name="preserved" value="'.htmlspecialchars(serialize($preserved)).'">'."\n");
			}
			$p->set_var('submit_button',lang('Save'));

			$delete_button = $cancel_button = '';
			if ($event['id'] > 0)
			{
				$var = array(
					'action_url_button'	=> $this->page('delete',array('cal_id'=>$event['id'])),
					'action_text_button'	=> lang('Delete'),
					'action_confirm_button'	=> "onClick=\"return confirm('".lang("Are you sure\\nyou want to\\ndelete this entry ?\\n\\nThis will delete\\nthis entry for all users.")."')\"",
					'action_extra_field'	=> ''
				);
				$p->set_var($var);
				$delete_button = $p->fp('out','form_button');
			}
			$p->set_var('delete_button',$delete_button);

			if ($this->bo->return_to)
			{
				$var = array(
					'action_url_button'	=> $GLOBALS['phpgw']->link('/index.php', $this->bo->return_to),
					'action_text_button'	=> lang('Cancel'),
					'action_confirm_button'	=> '',
					'action_extra_field'	=> ''
				);
				$p->set_var($var);
				$cancel_button = $p->fp('out','form_button');
			}
			$p->set_var('cancel_button',$cancel_button);
			$p->pparse('out','edit_entry');
		}

		// modify list of an event's external participants (i.e. non pgpgw users)
		//
		function modify_ext_partlist()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = True;
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = True;
			$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;

			$total_contacts = 0;
			$participant = array();
			$control_data= array();

			$control_data['action'] = '';
			$control_data['delete'] = array();
			$control_data['part'] = array();

			$p = CreateObject('phpgwapi.template',$this->template_dir);
			$p->set_file(
				array(
					'T_edit_partlist' => 'edit_partlist.tpl',
					'T_edit_partlist_blocks' => 'edit_partlist_blocks.tpl'
				)
			);

			$p->set_block('T_edit_partlist_blocks','B_alert_msg','V_alert_msg');
			$p->set_block('T_edit_partlist_blocks','B_partlist','V_partlist');
			$p->set_block('T_edit_partlist_blocks','B_participants_none','V_participants_none');
			$p->set_block('T_edit_partlist_blocks','B_delete_btn','V_delete_btn');
			
			global $query_addr;
			$sb = CreateObject('phpgwapi.sbox');
			$addy = $sb->getAddress('addr','',$query_addr);

			$add_ext  = $addy['doSearchFkt'];
			$add_ext .= $addy['addr_title']!=lang('Address Book')?$addy['addr_title']:'';
			$add_ext .= "&nbsp;".$addy['addr'].$addy['addr_nojs'];

			$p->set_var('text_add_name',$add_ext);

			$part = phpgw::get_var('part', 'string', 'GET');
			if ( $part )
			{
				$control_data['part'] = explode(',', $part);
			}
			else
			{
				$control_data['part'] = phpgw::get_var('participant', 'int', 'POST');
				$control_data['action'] = phpgw::get_var('action', 'string', 'POST');
				$control_data['delete'] = phpgw::get_var('delete', 'bool', 'POST');
			}

			
			foreach ( $control_data['part'] as $cd_part )
			{
				$id = $cd_part[$i];
				list($contact) = $this->read_contact($id);

				$participant[$id] = array
				(
					'name' => "{$contact['n_given']} {$contact['n_family']}"
				);
			}

			if ($control_data['action'] == lang('Delete selected contacts'))
			{
				for ($i=0; $i<count($control_data['delete']); $i++)
				{
					$id = $control_data['delete'][$i];
					unset($participant[$id]);
				}
			}
			
			if ($control_data['action'] == lang('Add Contact'))
			{
				$id = phpgw::get_var('id_addr', 'int', 'POST');
				if ( $id )
				{
					list($contact) = $this->read_contact($id);
					$participant[$id] = array();
					$participant[$id]['name'] = $contact['n_given'].' '.$contact['n_family'];
				}
			}

			// create list of currently selected contacts
			//
			//while(list($id,$contact) = each($participant))
                        if (is_array($participant))
                        {
                            foreach($participant as $id => $contact)
			{
				$p->set_var('hidden_delete_name','participant[]');
				$p->set_var('hidden_delete_value',$id);
				$p->set_var('ckbox_delete_name','delete[]');
				$p->set_var('ckbox_delete_value',$id);
				$p->set_var('ckbox_delete_participant',$contact['name']);
				$p->parse('V_partlist','B_partlist',True);
				$total_contacts++;
			}
                        }

			if ($total_contacts == 0)
			{
				// no contacts have been selected
				// => clear the delete form, remove delete button and show the none block
				//
				$p->set_var('V_partlist','');
				$p->set_var('V_delete_btn','');
				$p->set_var('text_none',lang('None'));
				$p->parse('V_participants_none','B_participants_none');
			}
			else
			{
				// at least one contact has been selected
				// => clear the none block, fill the delete form and add delete button
				//
				$p->set_var('V_participants_none','');
				$p->set_var('btn_delete_name','action');
				$p->set_var('btn_delete_value',lang('Delete selected contacts'));
				$p->parse('V_delete_btn','B_delete_btn');
			}

			$form_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.modify'));
			
			$charset = lang('charset');
			$p->set_var('charset',$charset);
			$p->set_var('page_title',$GLOBALS['phpgw_flags']['currentapp'] 
															 . ' - ' .lang('External Participants'));
			$p->set_var('font_family',$GLOBALS['phpgw_info']['theme']['font']);
			$p->set_var('body_tags',$body_tags);
			$p->set_var('form_method','POST');
			$p->set_var('form_action',$form_action);
			$p->set_var('text_add_contact',lang('External Participants'));
			$p->set_var('text_contacts_selected',lang('Selected contacts (%1)',$total_contacts));
			$p->set_var('btn_add_name','action');
			$p->set_var('btn_add_value',lang('Add Contact'));
			$p->set_var('btn_done_name','done');
			$p->set_var('btn_done_value',lang('Done'));
			$p->set_var('btn_done_js','copyback()');
			$p->set_var('form1_name','ext_form');

			$p->pfp('out','T_edit_partlist');			
		}

		function read_contact($id)
		{
			$query_fields = array(
				'n_given' => 'n_given',
				'n_family' => 'n_family',
				'email' => 'email',
				'email_home' => 'email_home'
			);

		  /*
			if ($this->rights & PHPGW_ACL_READ)
			{
				return $this->contacts->read_single_entry($id,$fields);
			}
			else
			{
				$rtrn = array(0 => array('No access' => 'No access'));
				return $rtrn;
			}
		  */

		  $contacts = CreateObject('phpgwapi.contacts', False);
		  return $contacts->read_single_entry($id,$query_fields);
		}

		function build_part_list(&$users,$accounts,$owner)
		{
			if(!is_array($accounts))
			{
				return;
			}
			foreach($accounts as $id)
			{
				$id = intval($id);
				if($id == $owner)
				{
					continue;
				}
				elseif(!isset($users[$id]))
				{
					if($GLOBALS['phpgw']->accounts->exists($id) == True)
					{
						$users[$id] = array(
							'name'	=> $GLOBALS['phpgw']->common->grab_owner_name($id),
							'type'	=> $GLOBALS['phpgw']->accounts->get_type($id)
						);
					}
					if($GLOBALS['phpgw']->accounts->get_type($id) == 'g')
					{
						$this->build_part_list($users,$GLOBALS['phpgw']->acl->get_ids_for_location($id,1,'phpgw_group'),$owner);
					}
				}
			}
			uasort($users,'strcmp_name');
		}

		function set_week_array($startdate,$cellcolor,$weekly)
		{
			for ($j=0,$datetime=$startdate;$j<7;$j++,$datetime += phpgwapi_datetime::SECONDS_IN_DAY)
			{
				$cellclass = $j % 2 ? 'row_on' : 'row_off';
				
				$date = date('Ymd',$datetime + (60 * 60 * 2)); // +2h to be save when switching to and from dst, $datetime is alreay + TZ-Offset
				print_debug('set_week_array : Date ',$date);

				if(isset($this->bo->cached_events[$date]) && $events = $this->bo->cached_events[$date])
				{
					print_debug('Date',$date);
					print_debug('Appointments Found',count($events));

					if (!$this->bo->prefs['calendar']['show_rejected'])
					{
						$appts = False;
						foreach($events as $event)	// check for a not-rejected event
						{
							if (!$this->bo->rejected_no_show($event))
							{
								$appts = True;
								break;
							}
						}
					}
					else
					{
						$appts = True;
					}
				}
				else
				{
					$appts = False;
				}

				$holidays = (isset($this->bo->cached_holidays[$date]) && $this->bo->cached_holidays[$date] ? $this->bo->cached_holidays[$date]:'');
				if($weekly)
				{
					$cellcolor = $GLOBALS['phpgw']->nextmatchs->alternate_row_class($cellcolor);
				}
				
				$day_image = '';
				if($holidays)
				{
					$extra = ' class="'.$this->bo->holiday_class.'"';
					$class = ($appts?'b':'').'minicalhol';
					if ($date == $this->bo->today)
					{
						$day_image = ' background="'.$GLOBALS['phpgw']->common->image('calendar','mini_day_block').'"';
					}
				}
				elseif ($date != $this->bo->today)
				{
					$extra = ' class="'.$cellclass.'"';
					$class = ($appts?'b':'').'minicalendar';
				}
				else
				{
					$extra = ' class="cal_today"';
					$class = ($appts?'b':'').'minicalendar';
					$day_image = ' background="'.$GLOBALS['phpgw']->common->image('calendar','mini_day_block').'"';
				}

				$new_event = $this->bo->check_perms(PHPGW_ACL_ADD);
				$holiday_name = array();
				if($holidays)
				{
					for($k=0;$k<count($holidays);$k++)
					{
						$holiday_name[] = $holidays[$k]['name'];
					}
				}
				$week = '';
				if (!$j || (!$weekly && $j && substr($date,6,2) == '01'))
				{
					$week = lang('week').' '.(int)((date('z',($startdate+(24*3600*4)))+7)/7);
				}
				$daily[$date] = array
				(
					'extra'		=> $extra,
					'new_event'	=> $new_event,
					'holidays'	=> $holiday_name,
					'appts'		=> $appts,
					'week'		=> $week,
					'day_image'	=> $day_image,
					'class'		=> $class
				);
			}

			if($this->debug)
			{
				_debug_array($daily);
			}
			
			return $daily;
		}

		/**
		* @author Thomas Bott
		*/
		function insert_tooltip_js($var)
		{
			if ( !is_array($var) )
			{
				$var = array();
			}

			if ( $this->inserted_tooltip_js == FALSE )
			{
				$tooltip_js = '<script type="text/javascript" src="' . $GLOBALS['phpgw_info']['server']['webserver_url'] . '/calendar/js/wz_tooltip/wz_tooltip.js"></script>';
				$var = array_merge($var, array('tooltip_js' => $tooltip_js) );
				$this->inserted_tooltip_js = TRUE;
			}
			return $var;
		}

		/**
		* @author Thomas Bott
		*/
		function my_nl2br($text)
		{
			$retvalue="";
			for ($i=0;$i< strlen($text);$i++)
			{
				if ($text[$i]!=chr(13))
				{
					$text[$i]=="\n" ? $retvalue .= "<BR>" : $retvalue .= $text[$i];
				}
			}
			return $retvalue;
		}
	}

	//Moved this here for performance reasons and cos it didn't belong in the middle of a method - skwashd
	if (!function_exists('strcmp_name'))
	{
		function strcmp_name($arr1, $arr2)
		{
			if ($diff = strcmp($arr1['type'], $arr2['type']))
			{
				return $diff;	// groups before users
			}
			return strnatcasecmp($arr1['name'], $arr2['name']);
		}
	}
