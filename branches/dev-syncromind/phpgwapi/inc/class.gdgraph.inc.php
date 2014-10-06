<?php
	/**
	* Creates graphical statistics using GD graphics library
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	* @internal This class based on boGraph.php3 - Double Choco Latte - Source Configuration Management System Copyright (C) 1999  Michael L. Dean & Tim R. Norman
	*/


	/**
	* Creates graphical statistics using GD graphics library
	*
	* @package phpgwapi
	* @subpackage gui
	*/
	class gdgraph
	{
		var $debug;
		var $popup_view;
		var $title;
		var $caption_x;
		var $caption_y;
		var $lines_x;
		var $lines_y;
		var $line_captions_x;
		var $data;
		var $colors;
		var $color_legend;
		var $graph_width;
		var $graph_height;
		var $margin_top;
		var $margin_left;
		var $margin_bottom;
		var $margin_right;
		var $img;
	
		function gdgraph($debug = False)
		{
			$this->debug			= $debug;

			$this->title_font_size	= 3;
			$this->line_font_size	= 2;
			$this->x_font_size		= 1;

			$this->title			= 'Gantt Chart';
			$this->legend_title		= 'Color Legend';

			$this->caption_x		= 'x';
			$this->caption_y		= 'y';

			$this->num_lines_x 		= 30;
			$this->num_lines_y 		= 10;

			$this->split_val		= 1;

			$this->line_captions_x	= array();
			$this->line_captions_y	= array();

			$this->data				= array();

			$this->colors			= array('red','green','blue','bright green','bright blue','dark green','dark blue','olivedrab4','dove',
											'seagreen','midnightblue');

			$this->color_legend		= array();
			$this->color_extra		= 'yellow';
			$this->legend_bottom	= 40;
	
			$this->graph_width		= 800;
			$this->graph_height		= 400;

			$this->margin_top		= 70;
			$this->margin_left		= 150;
			$this->margin_bottom	= 40;
			$this->margin_right		= 20;

			$this->img				= createObject('phpgwapi.gdimage');
			$this->temp_file		= $this->img->temp_file;
		}
	
		function rRender()
		{
			// Initialize image - map white since it's our background
			$this->img->width = $this->graph_width;
			$this->img->height = $this->graph_height;
			$this->img->Init();
			$this->img->SetColor(255, 255, 0);
			$this->img->ToBrowser();
			$this->img->Done();
		}

		function set_locale()
		{
			switch($GLOBALS['phpgw_info']['user']['preferences']['common']['lang'])
			{
				case 'de':
					if(phpversion() >= '4.3.0')
					{
						$loc = setlocale(LC_ALL,'de_DE@euro','de_DE','de','ge','de_DE.ISO-8859-1','de_DE.UTF-8');
					}
					else
					{
						$loc = setlocale(LC_ALL,'de_DE');
					}
					break;
				default:
					$loc = setlocale(LC_ALL,0); break;
			}
			//echo 'PREF_LOC: ' . $loc;
		}

		function date_format($sdate = 0,$edate = 0)
		{
			$day_from = date('d',$sdate);
			$mon_from = date('m',$sdate);
			$year_from = date('Y',$sdate);

			$day_to = date('d',$edate);
			$mon_to = date('m',$edate);
			$year_to = date('Y',$edate);

			//if date_from is newer than date to, invert dates
			$sign=1;

			if ($year_from>$year_to)
			{
				$sign=-1;
			}
			else if ($year_from==$year_to)
			{
				if ($mon_from>$mon_to)
				{
					$sign=-1;
				}
				else if ($mon_from==$mon_to)
				{
					if ($day_from>$day_to)
					{
						$sign=-1;
					}
				}
			}

		/*if ($sign==-1)
		{
			//invert dates
			$day_from = $date_to_parts[0];
			$mon_from = $date_to_parts[1];
			$year_from = $date_to_parts[2];
			$day_to = $date_from_parts[0];
			$mon_to = $date_from_parts[1];
			$year_to = $date_from_parts[2];
		}*/

			$yearfrom1=$year_from;  //actual years
			$yearto1=$year_to;      //(yearfrom2 and yearto2 are used to calculate inside the range "0")   
			//checks ini date
			if ($yearfrom1<1980)
			{//year is under range 0
				$deltafrom=-floor((1999-$yearfrom1)/20)*20; //delta t1
				$yearfrom2=$yearfrom1-$deltafrom;          //year used for calculations
			}
			else if($yearfrom1>1999)
			{//year is over range 0
				$deltafrom=floor(($yearfrom1-1980)/20)*20; //delta t1
				$yearfrom2=$yearfrom1-$deltafrom;          //year used for calculations
			}
			else
			{//year is in range 0
				$deltafrom=0;
				$yearfrom2=$yearfrom1;
			}

			//checks end date
			if ($yearto1<1980)
			{//year is under range 0
				$deltato=-floor((1999-$yearto1)/20)*20; //delta t2
				$yearto2=$yearto1-$deltato;            //year used for calculations
			}
			else if($yearto1>1999)
			{//year is over range 0
				$deltato=floor(($yearto1-1980)/20)*20; //delta t2
				$yearto2=$yearto1-$deltato;            //year used for calculations
			}
			else
			{//year is in range 0
				$deltato=0;
				$yearto2=$yearto1;
			}

			//Calculates the UNIX Timestamp for both dates (inside range 0)
			$ts_from = mktime(0, 0, 0, $mon_from, $day_from, $yearfrom2);
			$ts_to = mktime(0, 0, 0, $mon_to, $day_to, $yearto2);
			$diff = ($ts_to-$ts_from)/86400;
			//adjust ranges
			$diff += 7305 * (($deltato-$deltafrom) / 20);

			$date_diff = round($sign*$diff);
			//echo 'DATE_DIFF: ' . $date_diff;
			return $date_diff;
		}

		function format_data($sdate = 0,$edate = 0)
		{
			$date_diff = $this->date_format($sdate,$edate);

			if($date_diff <= 30)
			{
				$this->split_val	= 1;
				$this->date_diff	= $date_diff;
				$this->num_lines_x	= $date_diff+1;
			}
			else if($date_diff > 30)
			{
				$this->split_val	= round($date_diff/30);
				$this->date_diff	= round($date_diff/$this->split_val);
				$this->num_lines_x	= round($date_diff/$this->split_val)+1;
			}

			//echo 'OLD_SPLIT_VAL: ' . $this->split_val . '<br />';
			if($this->split_val == 14)
			{
				$this->split_val = 15;
			}

			//echo 'SPLIT_VAL: ' . $this->split_val . '<br />';
			//echo 'LINES_X: ' . $this->num_lines_x . '<br />';

			$this->set_locale();

			$this->line_captions_x[0] = array
			(
				'date'				=> mktime(12,0,0,date('m',$sdate),date('d',$sdate),date('Y',$sdate)),
				'date_formatted'	=> date('w',$sdate)==1?date('d/m',$sdate):date('d',$sdate),
				'date_day'			=> substr(strftime ('%a',$sdate),0,1), //substr(date('D',$sdate),0,1),
				'month'				=> strftime('%b',$sdate), //date('M',$sdate),
				'year'				=> date('Y',$sdate)
			);

			for($i=1;$i<=$this->date_diff;$i++)
			{
				$add		= $this->split_val*$i;
				$curr_date	= mktime(12,0,0,date('m',$sdate),date('d',$sdate)+$add,date('Y',$sdate));
				$pref_date	= mktime(12,0,0,date('m',$curr_date),date('d',$curr_date)-$this->split_val,date('Y',$curr_date));

				$this->line_captions_x[$i] = array
				(
					'date'				=> $curr_date,
					'date_formatted'	=> date('w',$curr_date)==1?date('d/m',$curr_date):date('d',$curr_date),
					'date_day'			=> substr(strftime ('%a',$curr_date),0,1)
				);

				/* if graph->height = 750 -> split_val > 13 */
				if($this->split_val > 15)
				{
					$this->line_captions_x[$i]['month'] = date('m',$curr_date);
				}
				else if(date('n',$pref_date) < date('n',$curr_date) || (date('n',$pref_date) > date('n',$curr_date) && date('Y',$pref_date) < date('Y',$curr_date)))
				{
					$this->line_captions_x[$i]['month'] = strftime('%b',$curr_date); //date('M',$curr_date);
				}

				if(date('Y',$pref_date) < date('Y',$curr_date))
				{
					$this->line_captions_x[$i]['year'] = date('Y',$curr_date);
				}
			}

			if($this->line_captions_x[$this->date_diff]['date'] < $edate)
			{
				$last_date	= $this->line_captions_x[$this->date_diff]['date'];
				$curr_date	= mktime(12,0,0,date('m',$last_date),date('d',$last_date)+$this->split_val,date('Y',$last_date));
				$pref_date	= mktime(12,0,0,date('m',$curr_date),date('d',$curr_date)-$this->split_val,date('Y',$curr_date));

				$this->line_captions_x[$this->date_diff+1] = array
				(
					'date'				=> $curr_date,
					'date_formatted'	=> date('w',$curr_date)==1?date('d/m',$curr_date):date('d',$curr_date),
					'date_day'			=> substr(strftime ('%a',$curr_date),0,1)  //substr(date('D',$curr_date),0,1)
				);

				if(date('m',$pref_date) < date('m',$curr_date))
				{
					$this->line_captions_x[$this->date_diff+1]['month'] = strftime('%b',$curr_date);  //date('M',$curr_date);
				}

				if(date('Y',$pref_date) < date('Y',$curr_date))
				{
					$this->line_captions_x[$this->date_diff+1]['year'] = date('Y',$curr_date);
				}
				$this->num_lines_x = $this->num_lines_x+1;
			}

			if($this->num_lines_x < 2)
			{
				$this->num_lines_x = 2;
			}

			/*case 'y': case 'Y': //calculates difference in years
			$diff=$year_to-$year_from;
			$adjust=0;
	 	  	if ($mon_from>$mon_to) $adjust=-1;
			else if ($mon_from==$mon_to)
		   	if ($day_from>$day_to) $adjust=-1;
			return $sign*($diff+$adjust);
			break;
			}*/
		}

		function Render($sdate = 0, $edate = 0)
		{
			if(count($this->color_legend) > 0)
			{
				$this->margin_bottom = $this->margin_bottom + (count($this->color_legend)*25) + 25;
			}

			// get the graph height
			if(count($this->data) > 0)
			{
				$this->graph_height = (count($this->data)*30) + $this->margin_top + $this->margin_bottom;
			}

			// Initialize image - map white since it's our background
			$this->img->width = $this->graph_width;
			$this->img->height = $this->graph_height;
			$this->img->Init();
			$this->img->SetColor(255, 255, 255);

			// Draw the title
			$this->img->SetFont($this->title_font_size);
			$this->img->SetColor(0, 0, 0);
			$this->img->MoveTo($this->graph_width / 2, 2);
			$this->img->DrawText(array('text' => $this->title));

			// line under title 
			$this->img->Line($this->margin_left - 4, $this->img->GetFontHeight() + 4, $this->graph_width - $this->margin_right, $this->img->GetFontHeight() + 4);

			// Draw the x axis text plus month plus dashed lines for x axis
			$linespace = ($this->graph_width - $this->margin_left - $this->margin_right) / ($this->num_lines_x - 1);

			reset($this->line_captions_x);
			$i = 0;

			//_debug_array($this->line_captions_x);

			foreach($this->line_captions_x as $day_text)
			{
				$this->img->SetColor(0, 0, 0);
				if(isset($day_text['year']))
				{
					$this->img->SetFont($this->line_font_size);
					$this->img->MoveTo($i * $linespace + $this->margin_left, $this->img->GetFontHeight() + 10);
					$this->img->DrawText(array('text' => $day_text['year']));
				}

				if(isset($day_text['month']))
				{
					$this->img->SetFont($this->line_font_size);
					$this->img->MoveTo($i * $linespace + $this->margin_left, $this->img->GetFontHeight() + 25);
					$this->img->DrawText(array('text' => $day_text['month']));
				}

				$this->img->SetFont($this->x_font_size);
				$this->img->MoveTo($i * $linespace + $this->margin_left, $this->img->GetFontHeight() + 45);

				if(date('w',$day_text['date']) == 0 || date('w',$day_text['date']) == 6)
				{
					$this->img->SetColor(190, 190, 190);
				}
				else if(date('w',$day_text['date']) == 1)
				{
					$this->img->SetColor(0, 150, 255);
				}
				$this->img->DrawText(array('text' => $day_text['date_day']));

				$x = $i * $linespace + $this->margin_left;

				$this->img->SetColor(190, 190, 190);
				if(date('w',$day_text['date']) == 0 || date('w',$day_text['date']) == 6)
				{
					$this->img->Line($x, $this->margin_top, $x, $this->graph_height - $this->margin_bottom - 4);
				}
				else if(date('w',$day_text['date']) == 1)
				{
					$this->img->SetColor(0, 150, 255);
					$this->img->Line($x, $this->margin_top, $x, $this->graph_height - $this->margin_bottom - 4);
				}
				else
				{
					$this->img->Line($x, $this->margin_top, $x, $this->graph_height - $this->margin_bottom - 4, 'dashed');
				}

				$this->img->SetColor(0, 0, 0);
				$this->img->Line($x, $this->graph_height - $this->margin_bottom - 4, $x, $this->graph_height - $this->margin_bottom + 4);
				$i++;
			}

			//$this->img->MoveTo($this->graph_width / 2, $this->graph_height - $this->img->GetFontHeight() - 2);
			//$this->img->MoveTo(2, $this->graph_height / 2);
			//$this->img->DrawText($this->caption_y, 'up', 'center');
			//$this->img->MoveTo($this->graph_width / 2, $this->graph_height - $this->img->GetFontHeight() - 2);
			//$this->img->DrawText($this->caption_x, '', 'center');

			// Draw the two axis
			$this->img->SetColor(0, 0, 0);
			$this->img->Line($this->margin_left, $this->margin_top, $this->margin_left, $this->graph_height - $this->margin_bottom + 4);
			$this->img->Line($this->margin_left - 4, $this->graph_height - $this->margin_bottom, $this->graph_width - $this->margin_right, $this->graph_height - $this->margin_bottom);

			// Draw dashed lines for y axis
			$linespace = ($this->graph_height - $this->margin_top - $this->margin_bottom) / ($this->num_lines_y - 1);
			for ($i = 1; $i < $this->num_lines_y; $i++)
			{
				$y = $this->graph_height - $this->margin_bottom - ($i * $linespace);
				$this->img->SetColor(0, 0, 0);
				$this->img->Line($this->margin_left - 4, $y, $this->margin_left + 4, $y);
				$this->img->SetColor(200, 200, 200);
				$this->img->Line($this->margin_left + 4, $y, $this->graph_width - $this->margin_right, $y, 'dashed');
			}

			/* Find the largest numeric value in data (an array of arrays representing data)
			$largest = 0;
			reset($this->data);
			while (list($junk, $line) = each($this->data))
			{
				reset($line);
				while (list($junk2, $value) = each($line))
				{
					if ($value > $largest)
					$largest = $value;
				}
			}

			while ($largest < ($this->num_lines_y - 1))
				$largest = ($this->num_lines_y - 1);

			$spread = ceil($largest / ($this->num_lines_y - 1));
			$largest = $spread * ($this->num_lines_y - 1);*/

			$largest = $this->num_lines_x - 1;

			// Draw the x axis text
			$this->img->SetColor(0, 0, 0);
			$this->img->SetFont($this->x_font_size);
			$linespace = ($this->graph_width - $this->margin_left - $this->margin_right) / ($this->num_lines_x - 1);
			reset($this->line_captions_x);
			$i = 0;
			while (list(,$text) = each($this->line_captions_x))
			{
				$this->img->MoveTo($i * $linespace + $this->margin_left, $this->graph_height - $this->margin_bottom + 8);
				$this->img->DrawText(array('text' => $text['date_formatted']));
				$i++;
			}

			// Draw the lines for the data
			$this->img->SetColor(255, 0, 0);
			reset($this->data);

			if($this->debug)
			{
				_debug_array($this->data);
			}
			//_debug_array($this->data);
			$i = 1;
			$px = $py = 0;
			if(is_array($this->data))
			{
			foreach($this->data as $line)
			{
				if(isset($line['extracolor']) && $line['extracolor'])
				{
					$this->img->SetColorByName($line['extracolor']);
				}
				else
				{
					$this->img->SetColorByName($this->colors[$line['color']]);
				}

				$x1 = $x2 = $y1 = $y2 = 0;
				$gx = 0; // progress

				$linespace = ($this->graph_height - $this->margin_top - $this->margin_bottom) / ($this->num_lines_y - 1);

				$y1 = $y2 = $this->graph_height - $this->margin_bottom - ($i * $linespace);
				$py = isset($line['f_sdate'])?$y2:$py; // previous

				$linespace = ($this->graph_width - $this->margin_left - $this->margin_right) / ($this->num_lines_x - 1);

				if ($line['sdate'] <= $this->line_captions_x[0]['date'] && $line['edate'] >= $this->line_captions_x[0]['date'])
				{
					if($this->debug)
					{
						echo 'PRO sdate <= x sdate | PRO edate > x sdate<br>';
					}
					$x1 = $this->margin_left;
				}
				else if($line['sdate'] >= $this->line_captions_x[0]['date'])  //&& $line['edate'] <= $this->line_captions_x[$largest]['date'])
				{
					if($this->debug)
					{
						echo 'PRO sdate >= date! pro_sdate = ' . $line['sdate'] . ', pro_edate = ' . $line['edate'] . '<br>';
						echo 'PRO sdate >= date! pro_sdate_formatted = ' . $GLOBALS['phpgw']->common->show_date($line['sdate'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) . ', pro_edate_formatted = ' . $GLOBALS['phpgw']->common->show_date($line['edate'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) . '<br>';
						echo 'x sdate: ' . $this->line_captions_x[0]['date'] . ', x edate: ' . $this->line_captions_x[$largest]['date'] . '<br><br>';
					}

					for($y=0;$y<$largest;$y++)
					{
						if($line['sdate'] == $this->line_captions_x[$y]['date'])
						{
							if($this->debug)
							{
								echo 'PRO sdate == date! pro_sdate = ' . $line['sdate'] . ', date = ' . $this->line_captions_x[$y]['date'] . '<br>';
							}
							$x1 = $y * $linespace + $this->margin_left;
						}
						else if($line['sdate'] >= $this->line_captions_x[$y]['date'] && $line['sdate'] <= $this->line_captions_x[$y+1]['date'])
						{
							$diff = ($line['sdate'] - $this->line_captions_x[$y]['date'])/86400;
							if($this->debug)
							{
								echo 'PRO sdate >= date! pro_sdate = ' . $line['sdate'] . ', date = ' . $this->line_captions_x[$y]['date'] . '<br>';
								echo 'diff: ' . ($line['sdate'] - $this->line_captions_x[$y]['date'])/86400 . '<br />';
							}
							$x1 = $y * $linespace + $this->margin_left + (($linespace/$this->split_val)*$diff);
						}
					}
				}
				else if(($line['sdate'] <= $this->line_captions_x[0]['date'] && $line['edate'] <= $this->line_captions_x[0]['date']) || 
						($line['sdate'] >= $this->line_captions_x[$largest]['date'] && $line['edate'] >= $this->line_captions_x[$largest]['date']))
				{
					$x1 = 0;
				}
				else
				{
					$x1 = $largest * $linespace + $this->margin_left;
				}

				if ($line['edate'] >= $this->line_captions_x[$largest]['date'])
				{
					if($this->debug)
					{
						echo 'PRO edate >= x edate! pro_edate = ' . $line['edate'] . '<br>';
						echo 'PRO edate >= x edate! pro_edate_formatted = ' . $GLOBALS['phpgw']->common->show_date($line['edate'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) . '<br>';
						echo 'x edate: ' . $this->line_captions_x[$largest]['date'] . '<br>';
					}
					$x2 = $this->graph_width - $this->margin_right;
					$px = $line['f_sdate']?$x2:$px;
				}
				else if($line['edate'] <= $this->line_captions_x[$largest]['date'] && $line['edate'] >= $this->line_captions_x[0]['date'])
				{
					for($y=0;$y<$largest;$y++)
					{
						if($line['edate'] == $this->line_captions_x[$y]['date'])
						{
							if($this->debug)
							{
								echo 'PRO edate == x edate! pro_edate = ' . $line['edate'] . '<br>';
							}
							$x2 = $y * $linespace + $this->margin_left;
							$px = isset($line['f_sdate'])?$x2:$px;
						}
						else if($line['edate'] >= $this->line_captions_x[$y]['date'] && $line['edate'] <= $this->line_captions_x[$y+1]['date'])
						{
							$diff = ($line['edate'] - $this->line_captions_x[$y]['date'])/86400;
							if($this->debug)
							{
								echo 'PRO edate >= x edate! pro_edate = ' . $line['edate'] . '<br>';
								echo 'diff: ' . ($line['edate'] - $this->line_captions_x[$y]['date'])/86400 . '<br />';
							}
							$x2 = $y * $linespace + $this->margin_left + (($linespace/$this->split_val)*$diff);
							$px = $line['f_sdate']?$x2:$px;
						}
					}
				}
				else if($line['edate'] >= $this->line_captions_x[$largest]['date'] && $line['edate'] >= $this->line_captions_x[0]['date'] ||
						$line['edate'] <= $this->line_captions_x[$largest]['date'] && $line['edate'] <= $this->line_captions_x[0]['date'])
				{
					$x2 = 0;
				}
				else
				{
					$x2 = $largest * $linespace + $this->margin_left;
					$px = $line['f_sdate']?$x2:$px;
				}

				// progress

				if ($line['progress'] >= $this->line_captions_x[$largest]['date'])
				{
					if($this->debug)
					{
						echo 'PRO edate >= x edate! pro_edate = ' . $line['edate'] . '<br>';
						echo 'PRO edate >= x edate! pro_edate_formatted = ' . $GLOBALS['phpgw']->common->show_date($line['edate'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) . '<br>';
						echo 'x edate: ' . $this->line_captions_x[$largest]['date'] . '<br>';
					}
					$gx = $this->graph_width - $this->margin_right;
				}
				else if($line['progress'] <= $this->line_captions_x[$largest]['date'] && $line['progress'] >= $this->line_captions_x[0]['date'])
				{
					for($y=0;$y<$largest;$y++)
					{
						if($line['progress'] == $this->line_captions_x[$y]['date'])
						{
							if($this->debug)
							{
								echo 'PRO edate == x edate! pro_edate = ' . $line['edate'] . '<br>';
							}
							$gx = $y * $linespace + $this->margin_left;
						}
						else if($line['progress'] >= $this->line_captions_x[$y]['date'] && $line['progress'] <= $this->line_captions_x[$y+1]['date'])
						{
							$diff = ($line['progress'] - $this->line_captions_x[$y]['date'])/86400;
							if($this->debug)
							{
								echo 'PRO edate >= x edate! pro_edate = ' . $line['edate'] . '<br>';
								echo 'diff: ' . ($line['edate'] - $this->line_captions_x[$y]['date'])/86400 . '<br />';
							}
							$gx = $y * $linespace + $this->margin_left + (($linespace/$this->split_val)*$diff);
						}
					}
				}
				else if($line['progress'] >= $this->line_captions_x[$largest]['date'] && $line['progress'] >= $this->line_captions_x[0]['date'] ||
						$line['progress'] <= $this->line_captions_x[$largest]['date'] && $line['progress'] <= $this->line_captions_x[0]['date'])
				{
					$gx = 0;
				}
				else
				{
					$gx = $largest * $linespace + $this->margin_left;
				}

				if($x1 > 0 && $x2 > 0 && $y1 > 0 && $y2 > 0)
				{
					/*for($w=-3;$w<4;$w++)
					{
						$this->img->Line(1+$x1,$y1+$w,$x2,$y2+$w);
					}*/
					$this->img->draw_rectangle(array($x1,$y1-5,$x2,$y2+5));
				}

				// progress
				$this->img->SetColor(180, 180, 180);
				if($x1 > 0 && $x2 > 0 && $gx > 0 && $y1 > 0 && $y2 > 0)
				{
					$this->img->draw_rectangle(array($x1,$y1-2,$gx,$y2+2));
				}

				//$color_index++;
				$i++;

				if(is_array($line['mstones']))
				{
					foreach($line['mstones'] as $ms)
					{
						for($y=0;$y<$largest;$y++)
						{
							if($ms['edate'] == $this->line_captions_x[$y]['date'])
							{
								if($this->debug)
								{
									echo 'PRO sdate == date! pro_sdate = ' . $line['sdate'] . ', date = ' . $this->line_captions_x[$y]['date'] . '<br>';
								}
								$mx1 = $y * $linespace + $this->margin_left;
							}
							else if($ms['edate'] >= $this->line_captions_x[$y]['date'] && $ms['edate'] <= $this->line_captions_x[$y+1]['date'])
							{
								$diff = ($ms['edate'] - $this->line_captions_x[$y]['date'])/86400;
								if($this->debug)
								{
									echo 'PRO sdate >= date! pro_sdate = ' . $line['sdate'] . ', date = ' . $this->line_captions_x[$y]['date'] . '<br>';
									echo 'diff: ' . ($line['sdate'] - $this->line_captions_x[$y]['date'])/86400 . '<br />';
								}
								$mx1 = $y * $linespace + $this->margin_left + (($linespace/$this->split_val)*$diff);
							}
						}

						if($mx1>0)
						{
							$this->img->SetColorByName('yellow');

							$mx2 = $mx1-5;
							$mx3 = $mx1+5;
							$my2 = $my3 = $y1-11;

							$this->img->draw_triangle(array($mx1,$y1,$mx2,$my2,$mx3,$my3));
						}
					}
				}

				if($line['previous'] > 0 && $px > 0 && $py > 0 && $x1 > 0 && $x2 > 0 && $y1 > 0 && $y2 > 0)
				{
					$this->img->SetColor(0, 0, 0);

					for($w=-1;$w<1;$w++)
					{
						$this->img->Line(1+$px,$py+$w,$x1,$y1+$w);
					}
				}
			}

			// Draw the y axis text
			$this->img->SetFont($this->line_font_size);
			$linespace = ($this->graph_height - $this->margin_top - $this->margin_bottom) / ($this->num_lines_y - 1);
			$space = 1;
			foreach($this->data as $ytext)
			{
				if(isset($ytext['extracolor']) && $ytext['extracolor'])
				{
					$this->img->SetColorByName($ytext['extracolor']);
				}
				else
				{
					$this->img->SetColorByName($this->colors[$ytext['color']]);
				}

				$y = $this->graph_height - $this->margin_bottom - ($space * $linespace);
				$this->img->MoveTo(1,$y);

				$strlen = $this->line_font_size==4?15:20;

				if (strlen($ytext['title']) > $strlen)
				{
					$ytext['title'] = substr($ytext['title'],0,$strlen) . '.';
				} 
				$this->img->DrawText(array('text' => $ytext['title'],'justification' => 'left','margin_left' => $this->margin_left));
				$space++;

				if($ytext['use_map'] && $ytext['use_map'] != 'unused')
				{
					$this->img->SetColor(0,0,0);
					$map_start = (($this->img->GetFontWidth())*$strlen) + 10;
					$map = '';
					$map = array($map_start,$y + 2,$map_start + 10,$y + 12);
					$this->img->draw_rectangle($map,$ytext['use_map']);

					$gantt_map[] = array('project_id'	=> $ytext['project_id'],
											'img_map'	=> $map);
				}
			}
			}

			if(count($this->color_legend)>0)
			{
				// color legend
				reset($this->color_legend);
				$this->img->SetFont($this->title_font_size);
				$this->img->SetColor(0, 0, 0);

				$legend_start = $this->graph_height - $this->margin_bottom + 25 + $this->img->GetFontHeight();

				$this->img->MoveTo($this->margin_left + (($this->img->GetFontWidth()*strlen($this->legend_title))/2), $legend_start);
				$this->img->DrawText(array('text' => $this->legend_title));

				$this->img->Line($this->margin_left - 4, $legend_start + $this->img->GetFontHeight(),$this->margin_left + ($this->img->GetFontWidth()*strlen($this->legend_title)), $legend_start + $this->img->GetFontHeight());

				$this->img->SetFont($this->line_font_size);
				$linespace = ($this->margin_bottom - $this->legend_bottom) / (count($this->color_legend)+1);
				$space = 1;

				//_debug_array($this->color_legend);

				foreach($this->color_legend as $legend)
				{
					if(isset($legend['extracolor']) && $legend['extracolor'])
					{
						$this->img->SetColorByName($legend['extracolor']);
					}
					else
					{
						$this->img->SetColorByName($this->colors[$legend['color']]);
					}

					$y = $legend_start + ($space * $linespace);
					$this->img->MoveTo($this->margin_left + 4,$y);
					$this->img->DrawText(array('text' => $legend['title'],'justification' => 'left','margin_left' => $this->margin_left));
					$space++;
				}
			}
			$img_file = $this->img->save_img();
			$this->img->Done();
			return array('img_file' => $img_file,
						'img_map'	=> (isset($gantt_map)?$gantt_map:array()));
		}

		function Open()
		{
		print('<script language="JavaScript">');
		print('window.open(\'main.php3?menuAction=boGraph.Show&');
		if (ereg('MSIE', $GLOBALS['HTTP_USER_AGENT']))
			print('DCLINFO=' . $GLOBALS['DCLINFO'] . '&');
		print($this->ToURL() . '\', \'graph\', \'width=' . ($this->graph_width + 20) . ',height=' . ($this->graph_height + 20) . ',resizable=yes,scrollbars=yes\');');
		print('</script>');
	}

	function Show()
	{
		$this->FromURL();
		$this->Render();
	}

	function FromURL()
	{
		$this->title = $GLOBALS['title'];
		$this->caption_x = $GLOBALS['caption_x'];
		$this->caption_y = $GLOBALS['caption_y'];
		$this->num_lines_x = $GLOBALS['num_lines_x'];
		$this->num_lines_y = $GLOBALS['num_lines_y'];
		$this->line_captions_x = explode(',', $GLOBALS['line_captions_x']);
		
		$dataURL = explode('~', $GLOBALS['data']);
		$this->data = array();
		while (list($junk, $line) = each($dataURL))
			$this->data[] = explode(',', $line);
		
		$this->colors = explode(',', $GLOBALS['colors']);
		$this->color_legend = explode(',', $GLOBALS['color_legend']);
		$this->graph_width = $GLOBALS['graph_width'];
		$this->graph_height = $GLOBALS['graph_height'];
		$this->margin_top = $GLOBALS['margin_top'];
		$this->margin_left = $GLOBALS['margin_left'];
		$this->margin_bottom = $GLOBALS['margin_bottom'];
		$this->margin_right = $GLOBALS['margin_right'];
	}
	
	function ToURL()
	{
		$url = 'title=' . rawurlencode($this->title) . '&';
		$url .= 'caption_x=' . rawurlencode($this->caption_x) . '&';
		$url .= 'caption_y=' . rawurlencode($this->caption_y) . '&';
		$url .= 'num_lines_x=' . $this->num_lines_x . '&';
		$url .= 'num_lines_y=' . $this->num_lines_y . '&';
		$url .= 'line_captions_x=' . rawurlencode(implode(',', $this->line_captions_x)) . '&';
		reset($this->data);
		$dataURL = '';
		while(list($junk, $line) = each($this->data))
		{
			if ($dataURL != '')
				$dataURL .= '~';
			$dataURL .= implode(',', $line);
		}
		$url .= 'data=' . $dataURL . '&';
		$url .= 'colors=' . implode(',', $this->colors) . '&';
		$url .= 'color_legend=' . rawurlencode(implode(',', $this->color_legend)) . '&';
		$url .= 'graph_width=' . $this->graph_width . '&';
		$url .= 'graph_height=' . $this->graph_height . '&';
		$url .= 'margin_top=' . $this->margin_top . '&';
		$url .= 'margin_left=' . $this->margin_left . '&';
		$url .= 'margin_bottom=' . $this->margin_bottom . '&';
		$url .= 'margin_right=' . $this->margin_right;

		return $url;
	}
	}
?>
