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
 
  /* $Id: detail_report1.php 9782 2002-03-18 03:18:05Z rschader $ */

  // Update complete for phpgroupware 0.9.10 - 4/14/2001 (api calls for accounts and contacts)
  if ($friendly) {
     $GLOBALS['phpgw_info']["flags"]["noheader"] = True;
     $bw = 1; // Borderwidth for tables
     $bc = "000000"; // bordercolor
     $cs = 0; // cellspacing
     $cp = 4; // cellpadding
  } else {
     $bw = 0;
     $bc = "FFFFFF"; // bordercolor
     $cs = 2; // cellspacing
     $cp = 2; // cellpadding
  }

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

  // Friendly var setting after config:
  if ($friendly) {
     $thbg = "FFFFFF";
  } else {
     $thbg = $GLOBALS['phpgw_info']["theme"]["th_bg"];
  }

  if ($error) {
     echo "<center>" . lang("Error") . ":$error</center>";
  }
// Testing:
//echo "<center>job_id=$job_id, startdate=$startdate, enddate=$enddate<br></center>";
if(! $enddate) {
 $enddate = "2999-12-30";
}
if(! $startdate) {
 $startdate = "1900-01-01";
 $heading = $GLOBALS['phpgw']->lang("Complete Job Activity Report");
} else {
  $heading = $GLOBALS['phpgw']->lang("Job Activity Report from")
	. " " . $startdate . " " . $GLOBALS['phpgw']->lang("to") . " " . $enddate;
  $passdate = 1; // we need to pass the start and end dates to printer friendly link
}

if($friendly) {
  echo '<table border=0 width="100%">';
  echo '<tr>';
  echo ' <td width="200"><img src="images/timesheet_logo.gif" border=0></td>';
  echo ' <td><center><h2>' . $heading . '</h2></center></td>';
  echo ' <td width="200">&nbsp;</td>';
  echo '</tr></table>';
} else {
 echo "<h2><center>$heading</center></h2>";
}

  $GLOBALS['phpgw']->db->query("select c.company_name,j.job_number,j.job_revision,s.status_name,"
	. "j.summary,j.description,j.contact_id "
	. "from phpgw_ttrack_jobs as j "
	. "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
	. "left join phpgw_ttrack_job_status as s on j.status_id = s.status_id "
	. "WHERE j.job_id='$job_id'");
  $GLOBALS['phpgw']->db->next_record();
  $company_name = $GLOBALS['phpgw']->db->f("company_name");
  $job_number = $GLOBALS['phpgw']->db->f("job_number");
  $job_revision = $GLOBALS['phpgw']->db->f("job_revision");
  $description = $GLOBALS['phpgw']->db->f("description");
  $status_name = $GLOBALS['phpgw']->db->f("status_name");
  $summary = $GLOBALS['phpgw']->db->f("summary");
  $contact_id = $GLOBALS['phpgw']->db->f("contact_id");

  if($description == "") $description = "&nbsp;";

  // Added code here to get and print the contact name from addressbook
  $contacts = CreateObject('phpgwapi.contacts');
  $qfields = array(
     'id' => 'id',
     'n_given' => 'n_given',
     'n_family' => 'n_family'
  );
  $entry = $contacts->read_single_entry($contact_id,$qfields);
  $n_contact_name = $entry[0]['n_given'] . " " . $entry[0]['n_family'];

echo '<br><CENTER>';
echo '<TABLE WIDTH="90%" BORDER="' . $bw . '" bordercolor="' . $bc 
  . '" cellspacing="' . $cs . '" cellpadding="' . $cp . '. ">';
echo '<TR>';

echo '<TH WIDTH=20% BGCOLOR="' . $thbg 
  . '"><FONT COLOR="#000000">'
  . $GLOBALS['phpgw']->lang("Company") . '</FONT></TH>';
echo '<TH WIDTH=10% BGCOLOR="' . $thbg . '"><FONT COLOR="#000000">'
  . $GLOBALS['phpgw']->lang("Job No.") . '</FONT></TH>';
echo '<TH WIDTH=60% BGCOLOR="' . $thbg . '"><FONT COLOR="#000000">'
  . $GLOBALS['phpgw']->lang("Description") . '</FONT></TH>';
echo '<TH WIDTH=10% BGCOLOR="' . $thbg . '"><FONT COLOR="#000000">'
  . $GLOBALS['phpgw']->lang("Status") . '</FONT></TH>';
echo '</TR>';
//$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
  // Friendly var setting after config:
  if ($friendly) {
     $tr_color = "FFFFFF";
  } else {
     $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
  }
echo '<tr>';
echo ' <td width="20%" bgcolor="' . $tr_color . '">' . " "
  . $company_name . '</td>';
echo ' <td width="10%" align="center" bgcolor="' . $tr_color . '">'
  . $job_number . $job_revision . '</td>';
echo ' <td width="60%" bgcolor="' . $tr_color . '">' . " "
  . $summary . '</td>';
echo ' <td width="10%" align="center" bgcolor="' . $tr_color . '">' . " "
  . $status_name . '</td></tr>';
echo '<tr>';
echo ' <td width="20%" bgcolor="' . $tr_color . '">' . " "
  . '&nbsp;</td>';
echo ' <td width="10%" align="center" bgcolor="' . $tr_color . '">'
  . '&nbsp;</td>';
echo ' <td width="60%" bgcolor="' . $tr_color . '">' . " "
  . $description . '</td>';
echo ' <td width="10%" align="center" bgcolor="' . $tr_color . '">' . " "
  . '&nbsp;</td></tr>';
echo '</tr>';
echo '<tr>';
echo ' <th width="20%" bgcolor="' . $thbg . '">' . "&nbsp;</th>"; 
echo ' <th width="10%" bgcolor="' . $thbg . '">' . "&nbsp;</th>";
echo ' <th width="60%" bgcolor="' . $thbg . '">' 
  . $GLOBALS['phpgw']->lang("Contact") . ": $n_contact_name</th>";
echo ' <th width="10%" bgcolor="' . $thbg . '">' . "&nbsp;</th>";
echo '</tr>';
echo '</table>';

// Should add a test here later to sum num_hours for the job, don't print table if num_hours is 0
// Also need to change this where possible to not use sql for accounts, instead access
// employee_profiles where possible for needed data. Might want to look at storing Fullname
// info in employee_profiles as well, to be more independent from core groupware.
$GLOBALS['phpgw']->db->query("SELECT j.work_date,e.lid,w.catagory_desc,"
	. "j.num_hours,j.comments "
	. "FROM phpgw_ttrack_job_details as j "
	. "LEFT JOIN phpgw_ttrack_emplyprof as e on j.account_id = e.id "
	. "left join phpgw_ttrack_wk_cat as w on j.work_catagory_id = w.work_catagory_id "
	. "WHERE j.job_id='$job_id' "
	. "AND j.work_date >= '$startdate' AND j.work_date <= '$enddate' "
	. "ORDER by work_date");
//start the table before the loop
echo '<br><TABLE WIDTH=90% BORDER="' . $bw . '" bordercolor="' . $bc
  . '" cellspacing="' . $cs . '" cellpadding="' . $cp . '. ">';
echo '<tr>';
echo '<TH WIDTH=12% BGCOLOR="' . $thbg
  . '"><FONT COLOR="#000000">'
  . $GLOBALS['phpgw']->lang("Date") . '</FONT></TH>';
echo '<TH WIDTH=20% BGCOLOR="' . $thbg
  . '"><FONT COLOR="#000000">'
  . $GLOBALS['phpgw']->lang("Employee") . '</FONT></TH>';
echo '<TH WIDTH=56% BGCOLOR="' . $thbg
 . '"><FONT COLOR="#000000">'
  . $GLOBALS['phpgw']->lang("Type of Work") . '</FONT></TH>';
echo '<TH WIDTH=12% BGCOLOR="' . $thbg
  . '"><FONT COLOR="#000000">'
  . $GLOBALS['phpgw']->lang("Hours") . '</FONT></TH>';
echo '</TR>';

$total_hours=0;
$t=0;
while ($GLOBALS['phpgw']->db->next_record()) {
 $lid[$t] = $GLOBALS['phpgw']->db->f("lid");
 $wdate[$t] = $GLOBALS['phpgw']->db->f("work_date");
 $wcat[$t] = $GLOBALS['phpgw']->db->f("catagory_desc");
 $nhours[$t] = $GLOBALS['phpgw']->db->f("num_hours");
 $comments[$t] = $GLOBALS['phpgw']->db->f("comments");
 if($comments[$t] == "") $comments[$t] = "&nbsp;";
 $total_hours = $total_hours + $nhours[$t];
 $t++;
}
for ($i=0; $i<$t; $i++) {
 $fullname = get_fullname($lid[$i]);
 // The following insures that if the user account has been deleted, we will at least
 // display their historical login name"
 if ($fullname == " ")
	$employee = $lid[$i];
 else
	$employee = $fullname;

 //$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
  // Friendly var setting after config:
  if ($friendly) {
     $tr_color = "FFFFFF";
  } else {
     $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
  }
 echo '<tr>';
 echo ' <td width="12%" align="center" bgcolor="' . $tr_color . '">' . " "
  . $wdate[$i] . '</td>';
 echo ' <td width="20%" align="center" bgcolor="' . $tr_color . '">'
  . $employee . '</td>';
 echo ' <td width="56%" bgcolor="' . $tr_color . '">' . " "
  . $wcat [$i]. '</td>';
 echo ' <td width="12%" align="center" bgcolor="' . $tr_color . '">' . " "
  . $nhours[$i] . '</td>';
 echo '<tr>';
 echo ' <td width="12%" bgcolor="' . $tr_color . '">' . " "
  . '&nbsp;</td>';
 echo ' <td width="20%" align="center" bgcolor="' . $tr_color . '">'
  . '&nbsp;</td>';
 echo ' <td width="56%" bgcolor="' . $tr_color . '">' . " "
  . $comments[$i] . '</td>';
 echo ' <td width="12%" align="center" bgcolor="' . $tr_color . '">' . " "
  . '&nbsp;</td>';
 echo '</tr>';
}
echo '<tr>';
echo ' <td width="12%" bgcolor="' . $thbg . '"' . '>&nbsp;</td>';
echo ' <td width="20%" bgcolor="' . $thbg . '"' . '>&nbsp;</td>';
echo ' <th width="56%" align="right" bgcolor="' . $thbg . '">' 
  . $GLOBALS['phpgw']->lang("Total Hours") . ':</th>';
$formatted_hours = sprintf("%01.2f",$total_hours);
echo ' <th width="12%" align="center" bgcolor="' . $thbg . '">' 
  . $formatted_hours . '</th>';
echo '</tr>';
echo '</table>';
if (! $friendly) {
  // add link for printer friendly version
  if ($passdate == 1) {
    $passstr = "&startdate=$startdate&enddate=$enddate";
  } else {
    $passstr = "";
  }
  echo "<P>&nbsp;<A HREF=\"" . $GLOBALS['phpgw']->link("/timetrack/detail_report1.php",
    "job_id=$job_id&friendly=1" . $passstr) . '"';
     echo ' TARGET="new_printer_friendly"'
      . " onMouseOver=\"window.status='" . lang("Generate printer-friendly version") . "';\">"
      . '[' . lang("Printer Friendly") . ']</A>';
  $GLOBALS['phpgw']->common->phpgw_footer();
}
