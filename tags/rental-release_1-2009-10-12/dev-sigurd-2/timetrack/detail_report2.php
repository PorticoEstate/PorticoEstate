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

  // Update complete for phpgroupware 0.9.10 - 4/14/2001 (api calls for accounts and contacts)

  $plain_text_report = 1;
  if($plain_text_report) {
   $GLOBALS['phpgw_info']["flags"] = array("noheader" => True, "nonavbar" => True);
  }

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

  if ($error) {
     echo "<center>" . lang("Error") . ":$error</center>";
  }

  if(! $startdate) $startdate = "0000-00-00";
  if(! $enddate) $enddate = "9999-00-00";

  // Testing:
  //echo "<center>job_id=$job_id, startdate=$startdate, enddate=$enddate<br></center>";

  // This will be the report generator for all timesheet activity, sorted by user then date,
  // then work catagory.
  if(! $plain_text_report) {
    echo "<h2><center>Employee Activity Report from $startdate to $enddate</center></h2>";
  } else {
    echo "<PRE>";
  }

    $GLOBALS['phpgw']->db->query("select jd.detail_id,jd.job_id,jd.work_catagory_id,jd.work_date,"
      . "jd.num_hours,jd.comments,c.company_name,j.job_number,j.job_revision,j.summary,j.description,"
      . "js.status_name,e.lid,w.catagory_desc "
      . "from phpgw_ttrack_job_details as jd "
      . "left join phpgw_ttrack_jobs as j on jd.job_id = j.job_id "
      . "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
      . "left join phpgw_ttrack_emplyprof as e on jd.account_id = e.id "
      . "left join phpgw_ttrack_wk_cat as w on jd.work_catagory_id = w.work_catagory_id "
      . "left join phpgw_ttrack_job_status as js on j.status_id = js.status_id "
      . "WHERE jd.work_date >= '$startdate' AND jd.work_date <= '$enddate' "
      . "ORDER BY a.account_lastname,jd.work_date,c.company_name,j.job_number,j.job_revision,jd.work_catagory_id "
      . "asc");

$t=0;
while($GLOBALS['phpgw']->db->next_record()){
  $company_name[$t] = $GLOBALS['phpgw']->db->f("company_name");
  $job_number[$t] = $GLOBALS['phpgw']->db->f("job_number");
  $job_revision[$t] = $GLOBALS['phpgw']->db->f("job_revision");
  $description[$t] = $GLOBALS['phpgw']->db->f("description");
  $status_name[$t] = $GLOBALS['phpgw']->db->f("status_name");
  $summary[$t] = $GLOBALS['phpgw']->db->f("summary");
  $wdate[$t] = $GLOBALS['phpgw']->db->f("work_date");
  $wcat[$t] = $GLOBALS['phpgw']->db->f("catagory_desc");
  $nhours[$t] = $GLOBALS['phpgw']->db->f("num_hours");
  $comments[$t] = $GLOBALS['phpgw']->db->f("comments");
  $lid[$t] = $GLOBALS['phpgw']->db->f("lid");
  $t++;
}
for($i=0; $i<$t; $i++) {
   $useracct = CreateObject('phpgwapi.accounts',$lid[$i]);
   $userInfo = $useracct->read_repository();
   $fullname = $userInfo['firstname'] . " " . $userInfo['lastname'];
   // The following insures that if the user account has been deleted, we will at least
   // display their historical login name"
   if ($fullname == " ")
	$employee[$i] = $lid[$i];
   else
	$employee[$i] = $fullname;

  // If we want to print a comma separated list for Excel import, we can read a flag
  // here and simply loop thru all the entries, printing them out.
  if($plain_text_report)
  {
   echo "\"$employee[$i]\",";
   echo "\"$company_name[$i]\",";
   echo "\"$job_number[$i]\",";
   echo "\"$job_revision[$i]\",";
   echo "\"$summary[$i]\",";
   echo "\"$wdate[$i]\",";
   echo "\"$wcat[$i]\",";
   echo "\"$nhours[$i]\"\n";
  } else {
    // set these if blank after plain text report
    if($comments[$i] == "") $comments[$i] = "&nbsp;";
    if($description[$i] == "") $description[$i] = "&nbsp;";
    ?>
    <br><CENTER><TABLE WIDTH=90% BORDER=0 bordercolor=FFFFFF cellspacing=2 cellpadding=2>
    <TR>
    <?php
    echo '<TH WIDTH=20% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] 
      . '"><FONT COLOR="#000000">Company</FONT></TH>';
    echo '<TH WIDTH=10% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] 
      . '"><FONT COLOR="#000000">Job No.</FONT></TH>';
    echo '<TH WIDTH=60% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] 
      . '"><FONT COLOR="#000000">Description</FONT></TH>';
    echo '<TH WIDTH=10% BGCOLOR="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] 
      . '"><FONT COLOR="#000000">Status</FONT></TH>';
    echo '</TR>';
    $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
    echo '<tr>';
    echo ' <td width="20%" bgcolor="' . $tr_color . '">' . " "
      . $company_name[$i] . '</td>';
    echo ' <td width="10%" align="center" bgcolor="' . $tr_color . '">'
      . $job_number[$i] . $job_revision . '</td>';
    echo ' <td width="60%" bgcolor="' . $tr_color . '">' . " "
      . $summary[$i] . '</td>';
    echo ' <td width="10%" align="center" bgcolor="' . $tr_color . '">' . " "
      . $status_name[$i] . '</td></tr>';
    echo '<tr>';
    echo ' <td width="20%" bgcolor="' . $tr_color . '">' . " "
      . '&nbsp;</td>';
    echo ' <td width="10%" align="center" bgcolor="' . $tr_color . '">'
      . '&nbsp;</td>';
    echo ' <td width="60%" bgcolor="' . $tr_color . '">' . " "
      . $description[$i] . '</td>';
    echo ' <td width="10%" align="center" bgcolor="' . $tr_color . '">' . " "
      . '&nbsp;</td></tr>';
    echo '</tr>';
    //echo '</table>';

    $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
    echo '<tr>';
    echo ' <td width="12%" align="center" bgcolor="' . $tr_color . '">' . " "
      . $wdate[$i] . '</td>';
    echo ' <td width="20%" align="center" bgcolor="' . $tr_color . '">'
      . $employee[$i] . '</td>';
    echo ' <td width="56%" bgcolor="' . $tr_color . '">' . " "
      . $wcat[$i] . '</td>';
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
      . '&nbsp;</td></tr>';
    echo '</tr>';
  } //end else plain_text_report
  //echo '</table>';
 } // end while nextrecord.
if($plain_text_report){
 echo "</PRE>";
} else {
 $GLOBALS['phpgw']->common->phpgw_footer();
}
