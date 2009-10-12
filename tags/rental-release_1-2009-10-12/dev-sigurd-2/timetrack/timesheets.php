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

  /* $Id$ */

  // Update complete for phpgroupware 0.9.10 - 4/18/2001 (api calls for accounts and contacts)

  if ($friendly) {
     $GLOBALS['phpgw_info']["flags"]["noheader"] = True;
  }

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

  if ($friendly) {
     $GLOBALS['phpgw_info']["apps"]["timetrack"]["ismanager"] = 0;
     $GLOBALS['phpgw_info']["apps"]["timetrack"]["ispayroll"] = 0;

     $userGroups = $GLOBALS['phpgw']->accounts->membership();
     for ($i=0; $i<count($userGroups); $i++)
     {
       $gname = $userGroups[$i]['account_name'];
       if ($gname == "TTrack_Managers")
       {
         $GLOBALS['phpgw_info']["apps"]["timetrack"]["ismanager"] = 1;
       }
       if ($gname == "TTrack_Payroll")
       {
         $GLOBALS['phpgw_info']["apps"]["timetrack"]["ispayroll"] = 1;
       }
     }
  }
  if(!$n_employee) {
     $n_employee = $GLOBALS['phpgw_info']["user"]["account_id"];
  } else {
    // Code here to verify that the user is allowed
    // Next line MAY not work for printer friendly version
    if ( !$GLOBALS['phpgw_info']["apps"]["timetrack"]["ismanager"] || !$GLOBALS['phpgw_info']["apps"]["timetrack"]["ispayroll"])
    {
      if($n_employee != $GLOBALS['phpgw_info']["user"]["account_id"])
      {
	//Need to write to a logfile here
	$hack_log = fopen("/tmp/groupware_breakin_log","a");
	if($hack_log)
	{
	  $employee_name = get_fullname($GLOBALS['phpgw_info']["user"]["account_id"]);
	  $spyon_name = get_fullname($n_employee);
	  $h_tstr = date("F j, Y, g:i a");
	  fputs($hack_log, "$h_tstr : Intrusion attempt by $employee_name against $spyon_name\n");
	  fclose($hack_log);
	}
	$n_employee = $GLOBALS['phpgw_info']["user"]["account_id"];
	echo "<h1><center>You are not authorized to view other employee's Timesheets</center></h1>";
	echo "<h2><center>Your inappropriate behaviour has been logged!</center></h2>";
      }
    }
  }
  $employee_name = get_fullname($n_employee);

  // Initial Goal: We will need "today's date, use that to determine the date of the beginning
  // of the week, all weeks will start on Monday and end on Sunday. We should only allow editing
  // for the current week until it is "POSTED" by an admin in payroll which should occur no
  // later than the following Monday, but we will need some mechanism to determine when the data
  // for the last week has been processed and lock out any changes to it after that. So for now
  // we are only going to concern ourselves with entering data for the current week.
  if (strlen($date) > 0) {
     $thisyear  = substr($date, 0, 4);
     $thismonth = substr($date, 4, 2);
     $thisday   = substr($date, 6, 2);
  } else {
     if ($day == 0)
        $thisday = date("d");
     else
        $thisday = $day;
     if ($month == 0)
        $thismonth = date("m");
     else
        $thismonth = $month;
     if ($year == 0)
        $thisyear = date("Y");
     else
        $thisyear = $year;
  }

  $next = mktime(2, 0, 0, $thismonth, $thisday + 7, $thisyear);
  $nextyear = date("Y", $next);
  $nextmonth = date("m", $next);
  $nextday = date("d", $next);
 
  $prev = mktime(2, 0, 0, $thismonth, $thisday - 7, $thisyear);
  $prevyear = date("Y", $prev);
  $prevmonth = date("m", $prev);
  $prevday = date("d", $prev);

  // We add 2 hours on to the time so that the switch to DST doesn't
  // throw us off.  So, all our dates are 2AM for that day.
  $sun = get_sunday_before($thisyear, $thismonth, $thisday) + 7200;
  $sat = $sun + (3600 * 24 * 7);
  // Our work week starts on Monday and ends on Sunday. Try to compensate for that:
  $mon = $sun + (3600 * 24);
  $endweek = $sun + (3600 * 24 * 7);
  $nextmon = $mon + (3600 * 24 * 7);
  // For sql queries:
  $start_of_week = date("Y-m-d", $sun); //we are checking for dates greater than this (i.e. Monday)
  $end_of_week = date("Y-m-d", $nextmon); // we are checking for dates less than this

  // Concept for viewing multiple timesheets by Sten During.
  if(!$friendly && $GLOBALS['phpgw_info']["apps"]["timetrack"]["ispayroll"])
  {
    echo'<form method="POST" name="empname" action="' . $GLOBALS['phpgw']->link("/timetrack/timesheets.php") . '">';
    echo '<b>&nbsp;' . lang("Choose a timesheet") . '</b><br>&nbsp;';

    echo '<select name="n_employee" onChange="document.empname.submit.click()">';
    $names = $GLOBALS['phpgw']->accounts->get_list('accounts');
    for ($i=0; $i<count($names); $i++) {
	$n_employee_id = $names[$i]['account_id'];
	$n_empname = $names[$i]['account_firstname'] . " " . $names[$i]['account_lastname'];
	echo '<option value="' . $n_employee_id . '"';
	if ($n_employee == $n_employee_id) echo " selected";
	echo '>' . $n_empname . '</option>';
    }
    echo '</select>';
    echo '<input type="hidden" name="year" value="' . $thisyear . '">';
    echo '<input type="hidden" name="month" value="' . $thismonth . '">';
    echo '<input type="hidden" name="day" value="' . $thisday . '">';
    echo'<input type="submit" name="submit" value="' . lang("submit") . '">';
    echo'</form>';
  }

if($friendly)
 {
  echo '<table border=0 width="100%">';
  echo '<tr>';
  echo ' <td width="200"><img src="images/timesheet_logo.gif" border=0></td>';
  echo ' <td><center><h2>Timesheet Entries<br>';
  echo '<font size="-1">for ' . $employee_name . '</font></h2></center></td>';
  echo ' <td width="200">&nbsp;</td>';
  echo '</tr></table>';
 } else {
  echo '<center><h2>Timesheet Entries<br>';
  echo '<font size="-1">for ' . $employee_name . '</font></h2></center>';
 }

?>
<TABLE BORDER=0 WIDTH=100%>
<TR>
<?php
  if (! $friendly) {
     echo '<TD ALIGN="left"><A HREF="' . $GLOBALS['phpgw']->link("/timetrack/timesheets.php") 
	. "?year=$prevyear&month=$prevmonth&day=$prevday&n_employee=$n_employee\">&lt;&lt;</A></TD>";
  }
?>
<TD ALIGN="middle"><FONT SIZE="+2" COLOR="<?php echo $H2COLOR;?>"><B>
<?php
  // Bumped up $sun to $mon, $sat to $endweek
  if (date("m", $mon) == date("m", $endweek)) {
     echo strftime("%b %d", $mon) . " - " . strftime("%d, %Y", $endweek);
  } else {
     if (date("Y", $mon) == date("Y", $endweek)) {
        echo strftime("%b %d", $mon) . " - " .
        strftime("%b %d, %Y", $endweek);
     } else {
        echo strftime("%b %d, %Y", $mon) . " - " .
        strftime("%b %d, %Y", $endweek);
     }
  }
?>
</B></FONT>
<FONT SIZE="+1" COLOR="<?php echo $H2COLOR;?>">
</FONT>
</TD>
<?php
  if (! $friendly) {
     echo '<TD ALIGN="right"><A HREF="' . $GLOBALS['phpgw']->link("/timetrack/timesheets.php")
        . "?year=$nextyear&month=$nextmonth&day=$nextday&n_employee=$n_employee\">&gt;&gt;</A></TD>";
  }
?>
</TR>
</TABLE>

<TABLE WIDTH=100% BORDER=0 bordercolor=FFFFFF cellspacing=2 cellpadding=2>
 
<TR>
<?php
echo '<TH colspan ="2" WIDTH=22% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '"><FONT COLOR="#000000">Job Number</FONT></TH>';
echo '<TH WIDTH=24% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '"><FONT COLOR="#000000">Comments</FONT></TH>';
echo '<TH WIDTH=4% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '"><FONT COLOR="#000000">C</FONT></TH>';
echo '<TH WIDTH=6% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '"><FONT COLOR="#000000">Mon</FONT></TH>';
echo '<TH WIDTH=6% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '"><FONT COLOR="#000000">Tue</FONT></TH>';
echo '<TH WIDTH=6% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '"><FONT COLOR="#000000">Wed</FONT></TH>';
echo '<TH WIDTH=6% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '"><FONT COLOR="#000000">Thu</FONT></TH>';
echo '<TH WIDTH=6% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '"><FONT COLOR="#000000">Fri</FONT></TH>';
echo '<TH WIDTH=6% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '"><FONT COLOR="#000000">Sat</FONT></TH>';
echo '<TH WIDTH=6% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '"><FONT COLOR="#000000">Sun</FONT></TH>';
echo '<TH WIDTH=8% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '"><FONT COLOR="#000000">Total</FONT></TH>';
echo '</TR>';

// In order to go any further, if I am going to do anything like the grid on our manual timesheet, I will
// most likely need to get all the data laid out for the week before printing the rest of the table.
// At minimum, I will need to iterate thru each job/catagory combo (represented by one row) before
// printing it.

// So, first step will be to select all the job_detail entries for this user that happened during the selected
// week. Store all the job_id's, etc.

// Note: I have redone this query to select company_name so I can order by that, then job_number
// and work_catagory (hope it works).

    $GLOBALS['phpgw']->db->query("select jd.detail_id,jd.job_id,jd.work_catagory_id,jd.work_date,"
      . "jd.num_hours,c.company_name,j.job_number,j.job_revision "
      . "from phpgw_ttrack_job_details as jd "
      . "left join phpgw_ttrack_jobs as j on jd.job_id = j.job_id "
      . "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
      . "WHERE jd.account_id='$n_employee' "
      . "AND work_date > '$start_of_week' AND work_date < '$end_of_week' "
      . "ORDER BY c.company_name,j.job_number,j.job_revision,jd.work_catagory_id,jd.work_date asc "); 

    $counter = 1;
    while ($GLOBALS['phpgw']->db->next_record()) {
      $detail_id[$counter] = $GLOBALS['phpgw']->db->f("detail_id");
      $job_id[$counter] = $GLOBALS['phpgw']->db->f("job_id");
      $workcat[$counter] = $GLOBALS['phpgw']->db->f("work_catagory_id");
      $work_date[$counter] = $GLOBALS['phpgw']->db->f("work_date");
      $num_hours[$counter] = $GLOBALS['phpgw']->db->f("num_hours");
      $comments[$counter] = $GLOBALS['phpgw']->db->f("comments");
      $counter = $counter + 1;
    }
    $num_details = $counter; // -1
    
    // Loop through all the details
    for($mydetails=1; $mydetails < $num_details; $mydetails++)
    {
     // After we've printed out the first job detail...
     if($mydetails > 1)
      {
       // If this detail is for the same job and same work category as the previous detail
       // we skip to the next detail.
       if(($job_id[$mydetails] == $job_id[$mydetails - 1])&&($workcat[$mydetails] == $workcat[$mydetails - 1]))
	continue; //skip rest of for loop?
      }
     // Start by printing the Job Number/Customer, Comments and Catagory.
     $GLOBALS['phpgw']->db->query("SELECT company_id,job_number,job_revision,summary FROM phpgw_ttrack_jobs WHERE job_id =" 
        . $job_id[$mydetails]); // should only return one entry.
     $GLOBALS['phpgw']->db->next_record();
     $company_id[$mydetails] = $GLOBALS['phpgw']->db->f("company_id");
     $job_number[$mydetails] = $GLOBALS['phpgw']->db->f("job_number");
     $job_revision[$mydetails] = $GLOBALS['phpgw']->db->f("job_revision");
     $summary[$mydetails] = $GLOBALS['phpgw']->db->f("summary");
     $GLOBALS['phpgw']->db->query("SELECT company_name FROM phpgw_ttrack_customers WHERE company_id =" . $company_id[$mydetails]);
     $GLOBALS['phpgw']->db->next_record();
     $company_name[$mydetails] = $GLOBALS['phpgw']->db->f("company_name");
     $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
     echo '<tr>';
     echo ' <td width="16%" bgcolor="' . $tr_color . '">' . " " . $company_name[$mydetails] . '</td>';
     echo ' <td width="6%" align="right" bgcolor="' . $tr_color . '">' . $job_number[$mydetails] 
 	. $job_revision[$mydetails] . '</td>';
     echo ' <td width="24%" bgcolor="' . $tr_color . '">' . $summary[$mydetails] . '</td>';
     echo ' <td width="4%" align="center" bgcolor="' . $tr_color . '">' . $workcat[$mydetails] . '</td>';
     // The best idea I can come up with so far is to maybe now go back for each date of the week
     // and re-query the database for entries for that particular job_id and catagory, filling
     // in the dates as we get hits, then we can also do a SUM query for the whole week, but we
     // will also need a method to track which entries we have already done. Since I have sorted
     // the first query results by job_id, work_date and catagory (perhaps it should be job_id, catagory)
     // I should just be able to test the current index job_id with the last one (also catagory) and
     // skip it if it is the same.
     for($dayofweek = 1; $dayofweek < 8; $dayofweek++)
      {
       $mydate = $sun + (3600 * 24 * $dayofweek);
       $mydatesql = date("Y-m-d", $mydate);
       // Since I am going to assume there will only be ONE entry per job_id per day, I have changed
       // the "SUM(num_hours)" from the select statement below to just "num_hours". This makes more
       // sense for the added link to edit the job_detail for a particular day and detail_id.

       $GLOBALS['phpgw']->db->query("SELECT num_hours,detail_id,start_time,end_time"
	  . " from phpgw_ttrack_job_details WHERE job_id = " . $job_id[$mydetails]
          . " AND account_id ='" . $n_employee . "'"
          . " AND work_catagory_id = " . $workcat[$mydetails]
          . " AND work_date = '" . $mydatesql . "'");
          
       $GLOBALS['phpgw']->db->next_record();
       $tothours = $GLOBALS['phpgw']->db->f("num_hours");
       $detail_id = $GLOBALS['phpgw']->db->f("detail_id");
       $start_time = $GLOBALS['phpgw']->db->f("start_time");
       $end_time = $GLOBALS['phpgw']->db->f("end_time");
       
       echo ' <td width="6%" align="center" bgcolor="' . $tr_color . '">';
       if ($tothours == "")
       {
         echo "&nbsp;";
       } else {
         if(! $friendly)
         {
	  $st = date("h:i A",mktime(substr($start_time,0,2),substr($start_time,3,2)));
          $et = date("h:i A",mktime(substr($end_time,0,2),substr($end_time,3,2)));
       	  echo '<a href="' . $GLOBALS['phpgw']->link("/timetrack/editdetail.php", "detailid=" . $GLOBALS['phpgw']->db->f("detail_id")) . '"'

          . " title=\"Start Time: $st, End Time: $et\""
          . " onMouseOver=\"window.status='Start Time: $st, End Time: $et';return true;\" "
          . "onMouseOut=\"window.status='';return true;\" "
          . '>'
       	  . $GLOBALS['phpgw']->db->f("num_hours")
       	  . '</a><br>';
          while ($GLOBALS['phpgw']->db->next_record()) {
            $start_time = $GLOBALS['phpgw']->db->f("start_time");
            $end_time = $GLOBALS['phpgw']->db->f("end_time");
            $st = date("h:i A",mktime(substr($start_time,0,2),substr($start_time,3,2)));
            $et = date("h:i A",mktime(substr($end_time,0,2),substr($end_time,3,2)));
       	    echo '<a href="' . $GLOBALS['phpgw']->link("/timetrack/editdetail.php", "detailid=" . $GLOBALS['phpgw']->db->f("detail_id")) . '"'
            . " title = \"Start Time: $st, End Time: $et\" "
            . " onMouseOver=\"window.status='Start Time: $st, End Time: $et';return true;\" "
            . "onMouseOut=\"window.status='';return true;\" "
             . '>'
       	     . $GLOBALS['phpgw']->db->f("num_hours")
       	     . '</a><br>';
       	  }
         } else {
          // The printer friendly version is not used for editing hours, so we don't need
          // to provide links to editdetail.php
          $tothours = $GLOBALS['phpgw']->db->f("num_hours");
          while ($GLOBALS['phpgw']->db->next_record()) {
       	     $tothours = $tothours + $GLOBALS['phpgw']->db->f("num_hours");
       	  }
       	  //echo $tothours;
          printf("%2.2f", $tothours);
         }
       }
       echo "</td>";
      } //end of for loop (days)

     // Weekly Total Hours
     
     $GLOBALS['phpgw']->db->query("SELECT SUM(num_hours) from phpgw_ttrack_job_details WHERE job_id = " . $job_id[$mydetails]
        . " AND account_id ='" . $n_employee . "'"
 	  . " AND work_catagory_id = " . $workcat[$mydetails]
	  . " AND work_date > '" . $start_of_week . "' AND work_date < '" . $end_of_week . "'");

     $GLOBALS['phpgw']->db->next_record();
     $weeklyhours = $GLOBALS['phpgw']->db->f(0);
     echo ' <td width="8%" align="center" bgcolor="' . $tr_color . '">' . $weeklyhours . '</td>';
     echo "</tr>\n";
   } //end for loop($mydetails)

  // Now we need to do the bottom row which totals each day and then the grand total for the week.
  echo '<tr>';
  $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
  echo ' <td colspan="4" align="right" bgcolor="' . $tr_color . '">' . 'Daily Totals' . '</td>'; 
  $mypayhours = 0.0;
  for($dayofweek = 1; $dayofweek < 8; $dayofweek++)
   {
    $mydate = $sun + (3600 * 24 * $dayofweek);
    $mydatesql = date("Y-m-d", $mydate);
    $GLOBALS['phpgw']->db->query("SELECT SUM(num_hours) from phpgw_ttrack_job_details WHERE "
       . " account_id ='" . $n_employee . "'"
       . " AND work_date = '" . $mydatesql . "'");
    $GLOBALS['phpgw']->db->next_record();
    $tothours = $GLOBALS['phpgw']->db->f(0);
    if($tothours) $mypayhours += $tothours;
    if ($tothours == "")
      echo ' <td width="6%" align="center" bgcolor="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '">' . "&nbsp;" . '</td>';
    else
      echo ' <td width="6%" align="center" bgcolor="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '">'
         . $tothours . '</td>';
   } //end of for loop (days)
  echo ' <td width="8%" align="center" bgcolor="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '">' . "&nbsp;" . '</td>';
  echo '</tr>';
  echo '<tr>';
  $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
  echo ' <td colspan="11" align="right" bgcolor="' . $tr_color . '">' . 'Weekly Total' . '</td>';
  echo ' <td width="8%" align="center" bgcolor="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '">'
         . $mypayhours . '</td>';
?>
</TABLE>

<?php

//echo "<br><center>Start of week is: " . $start_of_week ;
//echo ", End of week is: " . $end_of_week . "</center>";

?>


<?php
  if ($thisyear) {
     $yeartext = "year=$thisyear&month=$thismonth&day=$thisday";
  }
  if (! $friendly) {
     echo "<P>&nbsp;<A HREF=\"" . $GLOBALS['phpgw']->link("/timetrack/timesheets.php","$yeartext&friendly=1&n_employee=$n_employee") . '"';
     echo ' TARGET="new_printer_friendly"'
      . " onMouseOver=\"window.status='" . lang("Generate printer-friendly version") . "';\">"
      . '[' . lang("Printer Friendly") . ']</A>';
     echo " (" . lang("Use landscape mode") . ")";
     $GLOBALS['phpgw']->common->phpgw_footer();
  } else {
   // Add code for signature lines.
   echo '<table width="90%" border="0">';
   echo '<tr><td align="right">';
   echo '<br><pre>  Employee Signature: _________________________________</pre>';
   echo '</td></tr></table>';
  }
?>
