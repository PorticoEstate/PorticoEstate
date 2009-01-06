<?php
	/**
	* Matrix View Generator - creating matrix like timeframes for items
	* @author Marc Logemann <loge@phpgroupware.org>
	* @copyright Copyright (C) 2000,2001 Marc Logemann
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/

	/**
	* Matrix View Generator - creating matrix like timeframes for items
	* 
	* This matrix is having the days of actual month in the x-axis and the items, 
	* which could be projects, in the y-axis. You will see a top-down view of all 
	* items and their associated timeframes. You probably saw this in projectmanagement apps
	* @package phpgwapi
	* @subpackage gui
	*/
	class matrixview
	{
		var $sumdays = 0;
		var $month = 0;
		var $monthname = '';
		var $year = 0;
		var $day = 0;

		var $items_content = array();
		var $items_count = 0;
	
		var $arr_second_dim = 0;
		var $image1pix = 'images/pix.gif';
	
		var $color_headerfield = '#FFFF33';
		var $color_emptyfield = '#CCCCCC';

		var $selection = 1;

		/**
		*
		* construtor: graphview class
		*
		* constructor waits for the desired month in 
		* integer presentation and the desired year also
		* in integer presentation 4 digits (ex. 2001)
		*
		* @param 	int 	month (for example: 02)
		* @param  int 	year (for example: 2001)
		*
		*/
		function matrixview ($month_int = 0, $year_int = 0)
		{
			if ( !$month_int )
			{
				$month_int = date('n');
				$year_int = date('Y');
			}
			$days = 0;
			for ( $i = 0; $i < 32; ++$i)
			{
				if ( checkdate($month_int, $i, $year_int) )
				{
					++$days;
				}
			}

			$this->month = $month_int;
			$this->year = $year_int;
			$this->set1PixelGif($GLOBALS['phpgw']->common->get_image_path('todo').'/pix.gif');
		}

		/**
		*
		* set a Period for a specified item
		*
		* setting a period for an element means to define
		* a fromDate and and a toDate together with the
		* item itself. This will store a timeframe associated
		* with an item for later usage
		*
		* @param 	string 	item for the timeframe
		* @param  date 	fromdate in format yyyymmdd
		* @param  date		todate in format yyyymmdd
		*
		* @return boolean	false if item cannot be saved
		*				otherwise true
		*/
		function setPeriod ($item, $fromdate, $todate, $color='#990033')
		{
			$fyear = substr($fromdate,0,4);
			$fmonth = substr($fromdate,4,2);
			$fday = substr($fromdate,6,2);

			$tyear = substr($todate,0,4);
			$tmonth = substr($todate,4,2);
			$tday = substr($todate,6,2);

			if(mktime(0,0,0, $tmonth, $tday, $tyear) < mktime(0,0,0, $this->month+1,0,$this->year))
			{
				$this->day = $tday;
			}
			else 
			{
				$dinfo = getdate(mktime(0,0,0, $this->month+1,0,$this->year));
				$this->day = $dinfo['mday'];
			}

			$go = 1;
			$i = 0;
			$z = 0;

			while($go == 1)
			{
				// calculates fromdate
				// echo date("d/m/Y", mktime(0,0,0, $fmonth, $fday+$i, $fyear)); echo "<br>";

				$datinfo = getdate(mktime(0,0,0, $fmonth, $fday+$i, $fyear));
			
				if($datinfo['mon'] == $this->month
					&& $datinfo['year'] == $this->year
					&& $datinfo['mday'] <= $this->day)
				{
					$t = $datinfo['mday'];
					$this->items_content[$this->items_count][$t] = 'x';
				}
				
				if (mktime(0,0,0, $fmonth, $fday+$i, $fyear) >= mktime(0,0,0, $this->month+1, 0, $this->year) ||
					mktime(0,0,0, $fmonth, $fday+$i, $fyear) >= mktime(0,0,0, $tmonth, $tday, $tyear))
				{
					$go = 0;
				}
				$i++;
			}
		
			$this->items_content[$this->items_count][0] = $item;
			$this->items_color[$this->items_count]      = $color;

			// increase number of items in two-dimensional array
			$this->items_count++;
		}

		/**
		*
		* sets the color for empty dayfields
		*
		* @param	string 	color in hexadecimal (ex. "#336699")
		*/
		function setEmptyFieldColor ($color)
		{
			$this->color_emptyfield=$color;
		}

		/**
		*
		* sets the color for calendar day fields
		*
		* @param	string 	color in hexadecimal (ex. "#336699")
		*/
		function setHeaderFieldColor ($color)
		{
			$this->color_headerfield=$color;
		}

		/**
		*
		* sets a new path for 1pixel (pix.gif) gif needed for the table
		* default is set actual script dir + /images
		*
		* @param	string 	path and name to 1pixel gif
		*/
		function set1PixelGif ($filepath)
		{
			$this->image1pix=$filepath;
		}

		/**
		*
		* disable selection of new timeframe
		*
		*/
		function disableSelection ()
		{
			$this->selection=0;
		}

		/**
		*
		* return the html code for the matrix
		*
		* will return the complete html code for the matrix.
		* In the calling program you can do some other
		* operations on it, because it wont be echoed directly
		*
		* @return string	html code for the matrix
		*/
		function out($form_link)
		{
			// get days of desired month (month submitted in constructor)

			$in = getdate(mktime(0,0,0, $this->month+1,0,$this->year));
			$this->sumdays = $in['mday'];
			$this->monthname = $in['month'];

			$this->out_monthyear($form_link);

			echo "<table border=\"0\" align=\"center\">\n";

			$this->out_header();
			$this->out_ruler();
			echo '<tbody>';

			// loop through number of items
			for($z=0;$z<$this->items_count;$z++)
			{
				// seperate color and name from first array element

				$itemname  = $this->items_content[$z][0];
				$itemcolor = $this->items_color[$z];

				echo '<tr>' . "\n";
				echo '<td>' . $itemname . '</td>' . "\n";

				// loop through days of desired month
				for($r=1;$r<$this->sumdays+1;$r++)
				{
					if( isset($this->items_content[$z][$r])
						&& $this->items_content[$z][$r] == 'x')
					{
						$color = $itemcolor;
					}
					else
					{
						$color = $this->color_emptyfield;
					}
					echo '<td bgcolor="' . $color . '" width="20">&nbsp;</td>' . "\n";
				}

				echo '</tr>' . "\n";
			}
			echo '</tbody>';
			echo '</table>';
		}

		/**
		*
		* private class for out method
		*
		* should not be used from external
		*
		*/
		function out_header ()
		{
			echo "<thead>\n";
			echo '<tr>' . "\n";
			echo '<td>' . lang('Title') . '</td>' . "\n";

			for($i=1;$i<$this->sumdays+1;$i++)
			{
				echo "<td>{$i}</td>\n";
			}

			echo '</tr>' . "\n";
			echo '</thead>' . "\n";
		}

		/**
		*
		* private class for out method
		*
		* should not be used from external
		*
		*/
		function out_ruler ()
		{
			$span = $this->sumdays + 1;
			echo "<tfoot>\n<tr>\n<td colspan=\"{$span}\">&nbsp;</td>\n</tr>\n</tfoot>\n";
		}

		/**
		*
		* private class for out method
		*
		* should not be used from external
		*
		*/
		function out_monthyear($form_link)
		{
			echo '<form action="' . $form_link . '" method="post">' . "\n";
			echo '<h2>' . lang($this->monthname) ." $this->year</h2>\n";
			echo '<table border="0" width="100%" cellpadding="0" cellspacing="0">' . "\n";
			echo '<tr>' . "\n";

			if($this->selection == 1)
			{
				echo '<td colspan="2" align="right">' . "\n";
				echo '<select name="month">'; 

				for($i=1;$i<13;$i++)
				{
					if ($this->month == $i)
					{
						$sel = ' selected';
					}
					else
					{
						unset($sel);
					}
					echo "<option value=\"{$i}\" $sel>{$i}</option>";
				}

				echo '</select>' . "\n";
				echo '<select name="year">';

				for($i = date('Y') -2;$i<date('Y')+5;$i++)
				{
					if($this->year == $i)
					{
						$sel = ' selected';
					}
					else
					{
						unset($sel);
					}
					echo "<option value=\"{$i}\" $sel>{$i}</option>";
				}

				echo '</select>' . "\n";
				echo '&nbsp;&nbsp;<input type="submit" name="selection" value="' . lang('Filter') . '">&nbsp;&nbsp;';
				echo '</td>' . "\n";
			}

			echo '</tr>' . "\n";
			echo '</table>' . "\n";
			echo '</form>' . "\n";
		}
	}
?>
