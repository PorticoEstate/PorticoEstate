<?php
	/**************************************************************************\
	* phpgwtimetrack - phpGroupWare addon application                          *
	* http://phpgwtimetrack.sourceforge.net                                    *
	* Written by Robert Schader <bobs@product-des.com>                         *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/***************************************************************\
	* Function:TimeSelector v1.0                                    *
	* Code: PHP 3                                                   *
	* Author: Bob Schader <bobs@product-des.com>                    *
	* Creates an easy to format and read time select box.           *
	* Input: Prefix to name of field, default date                  *
	* Output: HTML to define the time select element                *
	\***************************************************************/

	// Note that if a "unix timestamp" is passed in the useTime variable,
	// This function assumes that it's minute value is in accord with the
	// 15 minute intervals that this routine uses. Since I am using this
	// from stored db values which are limited to these values during input,
	// it should be of no concern for my purposes.
	// This functionm is OBSOLETE?
	function TimeSelector($inName, $useTime=0)
	{
		if($useTime == 0)
		{
			$useTime = Time();
			$selected_minute = 0;
		} else {
			$selected_minute = date("i",$useTime);
		}
		$selectedhour = date("G",$useTime);
		echo "<SELECT NAME=" . $inName . ">\n";
		for($curhour = 0; $curhour < 24; $curhour++)
		{
			for($curmin = 0; $curmin < 60; $curmin+=15)
			{
				echo "<OPTION VALUE=\"";
				// Note that for my purposes, I really don't care right now about the month, day and year
				$mytstamp = mktime($curhour, $curmin, 0, 1, 1, 2000);
				// If this format gives me problems, maybe I can just preface the value with "1":
				echo date("Hi",$mytstamp);
				echo "\"";
				if(($curhour==$selectedhour) && ($curmin==intval($selected_minute)))
				{
					echo " SELECTED";
				}
				echo ">" . date("H:i",$mytstamp);
			}
		}
		echo "</SELECT>";
	}

	/**************************************************************\
	* Function:DateSelector v1.1                                   *
	* Code: PHP 3                                                  *
	* Author: Leon Atkinson <leon@clearink.com>                    *
	* Creates three form fields for get month/day/year             *
	* Input: Prefix to name of field, default date                 *
	* Output: HTML to define three date fields                     *
	\**************************************************************/

	/****************************************************************\
	* Modified by Bob Schader, 9/13/2000 so that it must be passed   *
	* a date, using "Time()" to pass current date, otherwise it will *
	* not have any date preselected. This is so I can use these in   *
	* forms which edit databases and allow the date entry to only    *
	* be updated if the user specifically enters a date.             *
	* Additional Mod: Current mods default to displaying 10 year     *
	* period surrounding Unix date 0 (1970). Fix to always display   *
	* 10 year period centered around current year (9/15/2000)        *
	\****************************************************************/

	// Also Obsolete (no longer used?)
	function DateSelector($inName, $useDate=0)
	{
		/* create array so we can name months */
		$monthName = array(1=>"January", "February", "March",
		"April", "May", "June", "July", "August",
		"September", "October", "November", "December");
	 
		/* make month selector */
		echo "<SELECT NAME=" . $inName . "Month>\n";
		// add blank option line for NO DATE:
		echo '<OPTION VALUE="" </OPTION>';
		for($currentMonth = 1; $currentMonth <= 12; $currentMonth++)
		{
			echo "<OPTION VALUE=\"";
			echo intval($currentMonth);
			echo "\"";
			if(($useDate != 0) && (intval(date("m", $useDate))==$currentMonth))
			{
				echo " SELECTED";
			}
			echo ">" . $monthName[$currentMonth] . "\n";
		}
		echo "</SELECT>";

		/* make day selector */
		echo "<SELECT NAME=" . $inName . "Day>\n";
		// add blank option line for NO DATE:
		echo '<OPTION VALUE="" </OPTION>';
		for($currentDay=1; $currentDay <= 31; $currentDay++)
		{
			echo "<OPTION VALUE=\"$currentDay\"";
			if(($useDate != 0) && (intval(date("d", $useDate))==$currentDay))
			{
				echo " SELECTED";
			}
			echo ">$currentDay\n";
		}
		echo "</SELECT>";

		/* make year selector */
		echo "<SELECT NAME=" . $inName . "Year>\n";
		// add blank option line for NO DATE:
		echo '<OPTION VALUE="" </OPTION>';
		$startYear = date("Y", time());
		for($currentYear = $startYear - 5; $currentYear <= $startYear+5;$currentYear++)
		{
			echo "<OPTION VALUE=\"$currentYear\"";
			if(($useDate != 0) && (intval(date("Y", $useDate))==$currentYear))
			{
				echo " SELECTED";
			}
			echo ">$currentYear\n";
		}
		echo "</SELECT>";
	}

	// No idea who wrote these mysql conversion functions for dates,
	// found them on phpcodexchange:
	//we use UNIX's time specification as the base specification

	function mysql_datetime_to_human($dt)
	{
		$yr=strval(substr($dt,0,4));
		$mo=strval(substr($dt,5,2));
		$da=strval(substr($dt,8,2));
		$hr=strval(substr($dt,11,2));
		$mi=strval(substr($dt,14,2));
		//$se=strval(substr($dt,17,2));
		return date("M/d/Y H:i", mktime ($hr,$mi,0,$mo,$da,$yr))." MST";
	}

	function mysql_timestamp_to_human($dt)
	{
		$yr=strval(substr($dt,0,4));
		$mo=strval(substr($dt,4,2));
		$da=strval(substr($dt,6,2));
		$hr=strval(substr($dt,8,2));
		$mi=strval(substr($dt,10,2));
		//$se=strval(substr($dt,12,2));
		return date("M/d/Y H:i", mktime ($hr,$mi,0,$mo,$da,$yr))." MST";
	}

	function mysql_timestamp_to_timestamp($dt)
	{
		$yr=strval(substr($dt,0,4));
		$mo=strval(substr($dt,4,2));
		$da=strval(substr($dt,6,2));
		$hr=strval(substr($dt,8,2));
		$mi=strval(substr($dt,10,2));
		$se=strval(substr($dt,10,2));
		return mktime($hr,$mi,$se,$mo,$da,$yr);
	}

	function mysql_datetime_to_timestamp($dt)
	{
		$yr=strval(substr($dt,0,4));
		$mo=strval(substr($dt,5,2));
		$da=strval(substr($dt,8,2));
		$hr=strval(substr($dt,11,2));
		$mi=strval(substr($dt,14,2));
		$se=strval(substr($dt,17,2));
		return mktime($hr,$mi,$se,$mo,$da,$yr);
	}

	function timestamp_to_mysql($ts)
	{
		$d=getdate($ts);
		$yr=$d["year"];
		$mo=$d["mon"];
		$da=$d["mday"];
		$hr=$d["hours"];
		$mi=$d["minutes"];
		$se=$d["seconds"];
		return sprintf("%04d%02d%02d%02d%02d%02d",$yr,$mo,$da,$hr,$mi,$se);
	}


	function timeleft($begin,$end)
	{
		//for two timestamp format dates, returns the plain english difference between them.
		//note these dates are UNIX timestamps

		$dif=$end-$begin;

		$years=intval($dif/(60*60*24*365));
		$dif=$dif-($years*(60*60*24*365));

		$months=intval($dif/(60*60*24*30));
		$dif=$dif-($months*(60*60*24*30));

		$weeks=intval($dif/(60*60*24*7));
		$dif=$dif-($weeks*(60*60*24*7));

		$days=intval($dif/(60*60*24));
		$dif=$dif-($days*(60*60*24));

		$hours=intval($dif/(60*60));
		$dif=$dif-($hours*(60*60));

		$minutes=intval($dif/(60));
		$seconds=$dif-($minutes*60);

		$s="";

		if ($weeks<>0) $s.= $weeks." weeks ";
		if ($days<>0) $s.= $days." days ";
		if ($hours<>0) $s.= $hours." hours ";
		if ($minutes<>0) $s.= $minutes." minutes ";

		return $s;
	}

	/* borrowed from calendar include: */
	function get_sunday_before($year,$month,$day)
	{
		$weekday = date("w", mktime(0,0,0,$month,$day,$year) );
		$newdate = mktime(0,0,0,$month,$day - $weekday,$year);
		return $newdate;
	} 

	/* added functions to support the new calendar based dateselector */
	// This first one merely includes the calendar.js file into the page.
	// I would have added it inline in my header.inc.php file, but since
	// not every page uses it, I decided not to.
	function inc_cal()
	{
		echo '<script language="JavaScript" src="inc/Calendar1-3.js"></script>';
	}

	function inc_myutil()
	{
		echo '<script language="JavaScript" src="inc/myutil.js"></script>';
	}

	/* Variable definitions:
	$fname: The name of the form this item belongs to.
	$iname: The name of the text element that will contain the read-only date value.
	$imonth: The month to use, default to current month if null or zero
	$iday: The day to use, defaults to current if 0 or null
	$iyear: The year to use, defaults to current of null or 0
	$inline: If "INLINE", popup will be displayed, need to also call cal_layer().
	$no_init: If $no_init is true (i.e. 1) then do NOT put the date value in $iname
	NOTE on date values: pass months and days as 2digit values, year as 4
	*/
	function CalDateSelector($fname,$iname,$no_init=0,$inline="",$imonth=0,$iday=0,$iyear=0)
	{
		if (!$imonth) $imonth = date("m");
		if (!$iday) $iday = date("d");
		if (!$iyear) $iyear = date("Y");
		if(!$no_init) $ivalue = "$iyear-$imonth-$iday";
		$jmonth = intval($imonth) - 1;
		echo '<input type=text value="' . $ivalue . '" name="' . $iname 
		. '" SIZE="10" onFocus="this.blur()">';
		echo "<a href=\"javascript:show_calendar('$fname.$iname',$jmonth,$iyear,'YYYY-MM-DD' ,'$inline')\"";
		echo "onMouseOver=\"window.status='Popup Calendar';return true;\"";
		echo "onMouseOut=\"window.status='';return true;\">";
		echo '<img src="images/cal1.gif" WIDTH="26" HEIGHT="22" ALIGN="ABSMIDDLE" BORDER="0"></A>';
	}

	//template version
	function tcaldateselector($fname,$iname,$no_init=0,$inline="",$imonth=0,$iday=0,$iyear=0)
	{
		if (!$imonth) $imonth = date("m");
		if (!$iday) $iday = date("d");
		if (!$iyear) $iyear = date("Y");
		if(!$no_init) $ivalue = "$iyear-$imonth-$iday";
		$jmonth = intval($imonth) - 1;
		$retstr = '<input type=text value="' . $ivalue . '" name="' . $iname 
		. '" SIZE="10" onFocus="this.blur()">';
		$retstr .= "<a href=\"javascript:show_calendar('$fname.$iname',$jmonth,$iyear,'YYYY-MM-DD' ,'$inline')\"";
		$retstr .= "onMouseOver=\"window.status='Popup Calendar';return true;\"";
		$retstr .= "onMouseOut=\"window.status='';return true;\">";
		$retstr .= '<img src="images/cal1.gif" WIDTH="26" HEIGHT="22" ALIGN="ABSMIDDLE" BORDER="0"></A>';
		return $retstr;
	}

	// This function needs to be called somewhere in the page with a calendar to create the layer for it.
	function cal_layer($x=500, $y=300, $t="")
	{
		echo '<SCRIPT Language="Javascript" TYPE="text/javascript">';
		echo "Calendar_CreateCalendarLayer($x, $y, \"$t\");";
		echo '</SCRIPT>';
	}

	// New time entry, 2 input boxes, hours, minutes, and a toggle button for AM, or PM.
	// Note that the use of the onBlur event requires that inc_myutil() be called by the page
	// prior to using this.
	// Obsolete!
	function TimeSelect2($name_prefix,$no_init=0,$hour=-1,$min=-1,$ampm="am")
	{
		if(intval($hour) < 0) {
			$hour = date("h"); // values 01-12
			$ampm = date("a"); // values: am or pm
		}
		if(intval($hour) > 12) {
			$thour = intval($hour) - 12;
			$hour = sprintf("%02d", $thour);
			$ampm = "pm";
		}
		if($min < 0) $min = date("i"); // values 00-59
		echo '<input type=text size=2 maxlength="2" name="' 
		. $name_prefix . '_hour' . '" value="' . $hour . '"'
		. 'onBlur="CheckNum(this,0,12);">';
		echo '<b>:</b>';
		echo '<input type=text size=2 maxlength="2" name="' 
		. $name_prefix . '_min' . '" value="' . $min . '"'
		. 'onBlur="CheckNum(this,0,59);">';
		echo '<input type=radio name="' . $name_prefix . '_ampm' . '"value="am"';
		if($ampm == "am") echo " CHECKED";
		echo '>A.M.';
		echo '<input type=radio name="' . $name_prefix . '_ampm' . '"value="pm"';
		if($ampm == "pm") echo " CHECKED";
		echo '>P.M.';
	}

	// I am trying to add plus/minus buttons to control input
	// Note that adding code in this function to call gethours() for auto-updating
	// makes the page called from dependent on a text field existing named "n_whours"
	// Actually, it becomes dependent on all args passed to gethours() that aren't vars!
	// This version is being replace by template version immediately following it.
	function TimeSelect3($fname,$name_prefix,$no_init=0,$hour=-1,$min=-1,$ampm="am")
	{
		$hidden_name = $name_prefix . "_wfocus";
		$hidden_inc = $name_prefix . "_step";
		$hour_name = $name_prefix . "_h";
		$min_name = $name_prefix . "_m";
		$hour_step = 1;
		$min_step = 15; // this will be configurable later via admin hook

		if(intval($hour) < 0) {
			$hour = date("h"); // values 01-12
			$ampm = date("a"); // values: am or pm
		}
		if(intval($hour) > 12) {
			$thour = intval($hour) - 12;
			$hour = sprintf("%02d", $thour);
			$ampm = "pm";
		}
		if(intval($hour) == 12) {
			$ampm = "pm";
		}
		if($min < 0) $min = date("i"); // values 00-59
		// Normalize the minute value to be a multiple of the min_step value:
		$min = floor($min / $min_step) * $min_step;
		$min = sprintf("%02d", $min);
		echo '<table border=0 cellspacing=0 cellpadding=0><tr><td>';
		// the following hidden element will store the name of the focused hour or minute for determining
		// which box to increment or decrement. Will probably need to pass the form name to this routine
		// now too.
		echo '<input type=hidden name="' . $hidden_name . '" value="' . $hour_name . '">';
		echo '<input type=hidden name="' . $hidden_inc . '" value="' . $hour_step . '">';
		echo '<input type=text size=2 maxlength="2" name="'
		. $hour_name . '" value="' . $hour . '"'
		. " onFocus=\"$fname.$hidden_name.value='$hour_name';$fname.$hidden_inc.value=$hour_step;this.blur();\""
		. '">';
		//. ' onBlur="CheckNum(this,0,12);">';
		echo '<b>:</b>';
		echo '<input type=text size=2 maxlength="2" name="'
		. $min_name . '" value="' . $min . '"'
		. " onFocus=\"$fname.$hidden_name.value='$min_name';$fname.$hidden_inc.value=$min_step;this.blur();\""
		. '">';
		//. ' onBlur="CheckNum(this,0,59);">';
		// plus/minus buttons go here
		echo '</td><td>';
		echo '<table border=0 cellspacing=0 cellpadding=0><tr><td>';

		// The javascript void 0 link is to fix an apparent bug in javascript url's that
		// affect turning the pointer into a busy pointer. onclick is then used instead
		echo "<a href=\"javascript: void 0\" "
		. "onclick=\"inc_num('$fname','$hidden_name','$hidden_inc','$name_prefix" . "_ampm');"
		. " gethours('jobform','n_whours','n_start_time','n_end_time'); return false;\""
		. "onMouseOver=\"window.status='Increment Time';return true;\""
		. "onMouseOut=\"window.status='';return true;\">"
		. '<img src="images/plus.gif" border="0"></a><br>';
		echo "<a href=\"javascript: void 0\" "
		. "onclick=\"dec_num('$fname','$hidden_name','$hidden_inc','$name_prefix" . "_ampm');"
		. " gethours('jobform','n_whours','n_start_time','n_end_time'); return false;\""
		. "onMouseOver=\"window.status='Decrement Time';return true;\""
		. "onMouseOut=\"window.status='';return true;\">"
		. '<img src="images/minus.gif" border="0"></a>';
		echo '</td></tr></table></td>';
		echo '<td>';
		echo '<input type=radio name="' . $name_prefix . '_ampm' . '" value="am"';
		if($ampm == "am") echo " CHECKED";
		echo " onClick=\"if(this.checked) {gethours('jobform','n_whours','n_start_time','n_end_time');}\"";
		echo '>A.M.';
		echo '<input type=radio name="' . $name_prefix . '_ampm' . '" value="pm"';
		if($ampm == "pm") echo " CHECKED";
		echo " onClick=\"if(this.checked) {gethours('jobform','n_whours','n_start_time','n_end_time');}\"";
		echo '>P.M.';
		echo '</td></tr></table>';
	} 

	// template version
	function ttimeselect3($fname,$name_prefix,$no_init=0,$hour=-1,$min=-1,$ampm="am")
	{
		$hidden_name = $name_prefix . "_wfocus";
		$hidden_inc = $name_prefix . "_step";
		$hour_name = $name_prefix . "_h";
		$min_name = $name_prefix . "_m";
		$hour_step = 1;
		$min_step = 15; // this will be configurable later via admin hook

		// Maybe replace this mess with switch statements
		switch (intval($hour))
		{
			case -1: //Set using current time
				$hour = date("h"); // values 01-12
				$ampm = date("a"); // values: am or pm
				break;
			case 12:
				$ampm = "pm";
				break;
			case 13:
			case 14:
			case 15:
			case 16:
			case 17:
			case 18:
			case 19:
			case 20:
			case 21:
			case 22:
			case 23:
				$thour = intval($hour) - 12;
				$hour = sprintf("%02d", $thour);
				$ampm = "pm";
				break;
			case 24:
				$thour = 12;
				$hour = sprintf("%02d", $thour);
				$ampm = "am";
				break;
		}
		//if(intval($hour) < 0) {
		//	$hour = date("h"); // values 01-12
		//	$ampm = date("a"); // values: am or pm
		//}
		//if(intval($hour == 24) {
		//	$thour = 12;
		//	$hour = sprintf("%02d", $thour);
		//	$ampm = "am";
		//} else {
		//	if(intval($hour) > 12) {
		//		$thour = intval($hour) - 12;
		//		$hour = sprintf("%02d", $thour);
		//		$ampm = "pm";
		//	}
		//	if(intval($hour) == 12) {
		//		$ampm = "pm";
		//	}
		//}
		if($min < 0) $min = date("i"); // values 00-59
		// Normalize the minute value to be a multiple of the min_step value:
		$min = floor($min / $min_step) * $min_step;
		$min = sprintf("%02d", $min);
		$retstr = '<table border=0 cellspacing=0 cellpadding=0><tr><td>';
		// the following hidden element will store the name of the focused hour or minute for determining
		// which box to increment or decrement. Will probably need to pass the form name to this routine
		// now too.
		$retstr .= '<input type=hidden name="' . $hidden_name . '" value="' . $hour_name . '">';
		$retstr .= '<input type=hidden name="' . $hidden_inc . '" value="' . $hour_step . '">';
		$retstr .= '<input type=text size="2" maxlength="2" name="'
		. $hour_name . '" value="' . $hour . '"'
		. " onFocus=\"$fname.$hidden_name.value='$hour_name';$fname.$hidden_inc.value=$hour_step;this.blur();\""
		. '">';
		$retstr .= '<b>:</b>';
		$retstr .= '<input type=text size="2" maxlength="2" name="'
		. $min_name . '" value="' . $min . '"'
		. " onFocus=\"$fname.$hidden_name.value='$min_name';$fname.$hidden_inc.value=$min_step;this.blur();\""
		. '">';
		// plus/minus buttons go here
		$retstr .= '</td><td>';
		$retstr .= '<table border=0 cellspacing=0 cellpadding=0><tr><td>';

		// The javascript void 0 link is to fix an apparent bug in javascript url's that
		// affect turning the pointer into a busy pointer. onclick is then used instead
		$retstr .= "<a href=\"javascript: void 0\" "
		. "onclick=\"inc_num('$fname','$hidden_name','$hidden_inc','$name_prefix" . "_ampm');"
		. " gethours('jobform','n_whours','n_start_time','n_end_time'); return false;\""
		. "onMouseOver=\"window.status='Increment Time';return true;\""
		. "onMouseOut=\"window.status='';return true;\">"
		. '<img src="images/plus.gif" border="0"></a><br>';
		$retstr .= "<a href=\"javascript: void 0\" "
		. "onclick=\"dec_num('$fname','$hidden_name','$hidden_inc','$name_prefix" . "_ampm');"
		. " gethours('jobform','n_whours','n_start_time','n_end_time'); return false;\""
		. "onMouseOver=\"window.status='Decrement Time';return true;\""
		. "onMouseOut=\"window.status='';return true;\">"
		. '<img src="images/minus.gif" border="0"></a>';
		$retstr .= '</td></tr></table></td>';
		$retstr .= '<td>';
		$retstr .= '<input type=radio name="' . $name_prefix . '_ampm' . '" value="am"';
		if($ampm == "am") $retstr .= " CHECKED";
		$retstr .= " onClick=\"if(this.checked) {gethours('jobform','n_whours','n_start_time','n_end_time');}\"";
		$retstr .= '>A.M.';
		$retstr .= '<input type=radio name="' . $name_prefix . '_ampm' . '" value="pm"';
		if($ampm == "pm") $retstr .= " CHECKED";
		$retstr .= " onClick=\"if(this.checked) {gethours('jobform','n_whours','n_start_time','n_end_time');}\"";
		$retstr .= '>P.M.';
		$retstr .= '</td></tr></table>';
		return $retstr;
	} 


	// Keep in mind that this will ruin the resultsets of any pending queries
	// (Same as grap_owner_name() will)
	function get_fullname($uid)
	{
		$useracct = CreateObject('phpgwapi.accounts',$uid);
		$userInfo = $useracct->read_repository();
		return $userInfo['firstname'] . " " . $userInfo['lastname'];
	}
