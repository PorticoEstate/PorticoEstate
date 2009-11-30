<?php

class module_calendar extends Module 
{
    function module_calendar()  
    {
        $this->arguments = array();
		$this->session = array('timestamp');
		$this->post = array(
			'prevmonth' => array(
				'type' => 'image',
				'src' => $GLOBALS['phpgw']->common->image('phpgwapi','left')
			),
			'nextmonth' => array(
				'type' => 'image',
				'src' => $GLOBALS['phpgw']->common->image('phpgwapi','right')
			)
		);
        $this->title = lang('Calendar');
        $this->description = lang('This module displays the current month');
		$this->bo = CreateObject('calendar.bocalendar');
   }

    function get_content(&$arguments,$properties)
    {
		$t = Createobject('phpgwapi.Template');
		$t->set_root($this->find_template_dir());
		$t->set_file('mini_calendar','mini_cal.tpl');

		if (!$arguments['timestamp'])
		{
			$arguments['timestamp'] = time();
		}
		if ($arguments['prevmonth'])
		{
			$arguments['timestamp'] = mktime(0,0,0,date("m",$arguments['timestamp'])-1,date("d",$arguments['timestamp']),  date("Y",$arguments['timestamp']));
		}
		elseif ($arguments['nextmonth'])
		{
			$arguments['timestamp'] = mktime(0,0,0,date("m",$arguments['timestamp'])+1,date("d",$arguments['timestamp']),  date("Y",$arguments['timestamp']));
		}

		$date = $GLOBALS['phpgw']->datetime->gmtdate($arguments['timestamp']);
		$month = $date['month'];
		$day = $date['day'];
		$year = $date['year'];
		$this->bo->read_holidays($year);

		$month_ago = intval(date('Ymd',mktime(0,0,0,$month - 1,$day,$year)));
		$month_ahead = intval(date('Ymd',mktime(0,0,0,$month + 1,$day,$year)));
		$monthstart = intval(date('Ymd',mktime(0,0,0,$month,1,$year)));
		$monthend = intval(date('Ymd',mktime(0,0,0,$month + 1,0,$year)));

		$weekstarttime = $GLOBALS['phpgw']->datetime->get_weekday_start($year,$month,1);

		$t->set_block('mini_calendar','mini_cal','mini_cal');
		$t->set_block('mini_calendar','mini_week','mini_week');
		$t->set_block('mini_calendar','mini_day','mini_day');

		$linkdata['menuaction'] = 'calendar.uicalendar.month';
		$linkdata['month'] = $GLOBALS['phpgw']->common->show_date($date['raw'],'m');
		$linkdata['year'] = $GLOBALS['phpgw']->common->show_date($date['raw'],'Y');

		$month = '<a href="' .$GLOBALS['phpgw']->link('/index.php',$linkdata)  . '" class="minicalendar">' . lang($GLOBALS['phpgw']->common->show_date($date['raw'],'F')).' '.$GLOBALS['phpgw']->common->show_date($date['raw'],'Y').'</a>';

		$var = Array(
			'cal_img_root'		=>	$GLOBALS['phpgw']->common->image('calendar','mini-calendar-bar'),
			'bgcolor'			=>	$calui->theme['bg_color'],
			'bgcolor1'			=>	$calui->theme['bg_color'],
			'month'				=>	$month,
			'bgcolor2'			=>	$calui->theme['cal_dayview'],
			'holiday_color'	=> $calui->holiday_color,
			'navig' => '<form method="post">' .
				$this->build_post_element('prevmonth') . '&nbsp;&nbsp;' .
				$this->build_post_element('nextmonth') . '</form>'
		);

		$t->set_var($var);

		if(!$t->get_var('daynames'))
		{
			for($i=0;$i<7;$i++)
			{
				$var = Array(
					'dayname'	=> '<b>' . substr(lang($GLOBALS['phpgw']->datetime->days[$i]),0,2) . '</b>',
					'day_image'	=> ''
				);
				$this->output_template_array($t,'daynames','mini_day',$var);
			}
		}
		$today = date('Ymd',$GLOBALS['phpgw']->datetime->users_localtime);
		unset($date);
		for($i=$weekstarttime + $GLOBALS['phpgw']->datetime->tz_offset;date('Ymd',$i)<=$monthend;$i += (24 * 3600 * 7))
		{
			unset($var);
			$daily = $this->set_week_array($i - $GLOBALS['phpgw']->datetime->tz_offset,$cellcolor,$weekly);
			@reset($daily);
			while(list($date,$day_params) = each($daily))
			{
				$year = intval(substr($date,0,4));
				$month = intval(substr($date,4,2));
				$day = intval(substr($date,6,2));
				$str = '';

				unset($linkdata);
				$linkdata['menuaction'] = 'calendar.uicalendar.day';
				$linkdata['date']= $date;
				$str = '<a href="' .$GLOBALS['phpgw']->link('/index.php',$linkdata) .'" class="' .$day_params['class'] .'">' .$day .'</a>';

				$var[] = Array(
					'day_image'	=> $day_params['day_image'],
					'dayname'	=> $str
				);
			}
			for($l=0;$l<count($var);$l++)
			{
				$this->output_template_array($t,'monthweek_day','mini_day',$var[$l]);
			}
			$t->parse('display_monthweek','mini_week',True);
			$t->set_var('dayname','');
			$t->set_var('monthweek_day','');
		}
		
		$return_value = $t->fp('out','mini_cal');
		$t->set_var('display_monthweek','');
//			$t->set_var('daynames','');
//			unset($p);
		return $return_value;
	}

	function output_template_array(&$p,$row,$list,$var)
	{
		if (!isset($var['hidden_vars']))
		{
			$var['hidden_vars'] = '';
		}
		$p->set_var($var);
		$p->parse($row,$list,True);
	}

	function set_week_array($startdate,$cellcolor,$weekly)
	{
		for ($j=0,$datetime=$startdate;$j<7;$j++,$datetime += 86400)
		{
			$date = date('Ymd',$datetime + (60 * 60 * 2));

			if($this->bo->cached_events[$date])
			{
				$appts = True;
			}
			else
			{
				$appts = False;
			}

			$holidays = $this->bo->cached_holidays[$date];
			if($weekly)
			{
				$cellcolor = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($cellcolor);
			}
				
			$day_image = '';
			if($holidays)
			{
				$extra = ' bgcolor="'.$this->bo->holiday_color.'"';
				$class = ($appts?'b':'').'minicalhol';
				if ($date == $this->bo->today)
				{
					$day_image = ' background="'.$GLOBALS['phpgw']->common->image('calendar','mini_day_block').'"';
				}
			}
			elseif ($date != $this->bo->today)
			{
				$extra = ' bgcolor="'.$cellcolor.'"';
				$class = ($appts?'b':'').'minicalendar';
			}
			else
			{
				$extra = ' bgcolor="'.$GLOBALS['phpgw_info']['theme']['cal_today'].'"';
				$class = ($appts?'b':'').'minicalendar';
				$day_image = ' background="'.$GLOBALS['phpgw']->common->image('calendar','mini_day_block').'"';
			}

			if($this->bo->check_perms(PHPGW_ACL_ADD))
			{
				$new_event = True;
			}
			else
			{
				$new_event = False;
			}
			$holiday_name = Array();
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
			$daily[$date] = Array(
				'extra'		=> $extra,
				'new_event'	=> $new_event,
				'holidays'	=> $holiday_name,
				'appts'		=> $appts,
				'week'		=> $week,
				'day_image'	=> $day_image,
				'class'		=> $class
			);
		}

		return $daily;
	}
}
