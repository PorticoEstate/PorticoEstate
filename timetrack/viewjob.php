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

  /* $Id: viewjob.php 9782 2002-03-18 03:18:05Z rschader $ */

  // Update complete for phpgroupware 0.9.10 - 4/18/2001 (api calls for accounts and contacts)

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

	inc_cal();
        if ($error) {
            echo "<center>" . lang("Error") . ":$error</center>";
        }
	echo "<center><h3>" . lang("View Job Entry") . "</h3></center>";
	// Print the next option only if user is a manager
	if ($GLOBALS['phpgw_info']["apps"]["timetrack"]["ismanager"] == 1)
		echo "<center>" . lang("Internal Job ID is") . ": " . $jobid . "</center>";
	$GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_jobs where job_id=" . $jobid);
        $GLOBALS['phpgw']->db->next_record();
        $n_company_id = $GLOBALS['phpgw']->db->f("company_id");
	$n_contact_id = $GLOBALS['phpgw']->db->f("contact_id");
	$n_account_id = $GLOBALS['phpgw']->db->f("account_id");
	$n_job_number = $GLOBALS['phpgw']->db->f("job_number");
	$n_job_revision = $GLOBALS['phpgw']->db->f("job_revision");
	$n_description = $GLOBALS['phpgw']->db->f("description");
	$n_quote_date = $GLOBALS['phpgw']->db->f("quote_date");
	$n_quoted_hours = $GLOBALS['phpgw']->db->f("quoted_hours");
	$n_opened_date = $GLOBALS['phpgw']->db->f("opened_date");
	$n_deadline = $GLOBALS['phpgw']->db->f("deadline");
	$n_approved_by = $GLOBALS['phpgw']->db->f("approved_by");
	$n_status_id = $GLOBALS['phpgw']->db->f("status_id");
	$n_billable = $GLOBALS['phpgw']->db->f("billable");
	$n_summary = $GLOBALS['phpgw']->db->f("summary");
	$n_completed_date = $GLOBALS['phpgw']->db->f("completed_date");
	$n_paid_date = $GLOBALS['phpgw']->db->f("paid_date");
	$n_cancelled_date = $GLOBALS['phpgw']->db->f("cancelled_date");
	// Info to get from other tables: customers.company_name
	// other table info should be able to get when doing the SELECT dropdowns:
	// contact_id(name), account_id(employee), status_name, approved_by.
	$GLOBALS['phpgw']->db->query("select company_name from phpgw_ttrack_customers where company_id=" . $n_company_id);
	$GLOBALS['phpgw']->db->next_record();
	$n_customer = $GLOBALS['phpgw']->db->f("company_name");
	// Use api for this
	$contacts = CreateObject('phpgwapi.contacts');
   	$qfields = array(
	   'id' => 'id',
	   'n_given' => 'n_given',
	   'n_family' => 'n_family'
	);
	$entry = $contacts->read_single_entry($n_contact_id,$qfields);
	$n_contact_name = $entry[0]['n_given'] . " " . $entry[0]['n_family'];

	// Need api call for this too.
	$n_employee_name = get_fullname($n_account_id);

	$GLOBALS['phpgw']->db->query("select status_name from phpgw_ttrack_job_status where status_id=" . $n_status_id);
	if($GLOBALS['phpgw']->db->num_rows() > 0)
	{
	 $GLOBALS['phpgw']->db->next_record();
	 $n_status_name = $GLOBALS['phpgw']->db->f("status_name");
	}
	$n_approvedby_name = get_fullname($n_approved_by);

	if($n_job_number == "") $n_job_number 		= "&nbsp;";
	if($n_job_revision == "") $n_job_revision 	= "&nbsp;";
	if($n_description == "") $n_description 	= "&nbsp;";
	if($n_quoted_hours == "") $n_quoted_hours 	= "&nbsp;";

	if(($n_quote_date == "") || ($n_quote_date == "0000-00-00"))
		$n_quote_date = "&nbsp;";
	if(($n_opened_date == "") || ($n_opened_date == "0000-00-00"))
		$n_opened_date = "&nbsp;";
	if(($n_deadline == "") || ($n_deadline == "0000-00-00"))
		$n_deadline = "&nbsp;";
	if(($n_completed_date == "") || ($n_completed_date == "0000-00-00"))
		$n_completed_date = "&nbsp;";
	if(($n_paid_date == "") || ($n_paid_date == "0000-00-00"))
		$n_paid_date = "&nbsp;";
	if(($n_cancelled_date == "") || ($n_cancelled_date == "0000-00-00"))
		$n_cancelled_date = "&nbsp;";

	if($n_summary == "") $n_summary 		= "&nbsp;";
	if($n_customer == "") $n_customer 		= "&nbsp;";
	if($n_contact_name == "") $n_contact_name 	= "&nbsp;";
	if($n_employee_name == "") $n_employee_name 	= "&nbsp;";
	if($n_status_name == "") $n_status_name 	= "&nbsp;";
	if($n_approvedby_name == "") $n_approvedby_name = "&nbsp;";
       ?>
        <center>
         <table border=0 width=65%>
           <tr>
            <?php
             echo '<th colspan="2" bgcolor="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '">'
		. lang("Job Details") . '</th>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Customer") . '</td>';
	     echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_customer . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Contact") . '</td>';
             echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_contact_name . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Assigned To") . '</td>';
             echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_employee_name . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Job Number") . '</td>';
             echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_job_number . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Revision") . '</td>';
             echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_job_revision . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Summary Description") . '</td>';
             echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_summary . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Detailed Description") . '</td>';
	     echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_description . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Quote Date") . '</td>';
	     echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_quote_date . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Quoted Hours") . '</td>';
             echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_quoted_hours . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Opened Date") . '</td>';
	     echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_opened_date . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Deadline") . '</td>';
	     echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_deadline . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Date Completed") . '</td>';
             echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_completed_date . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Paid in Full") . '</td>';
             echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_paid_date . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Date Cancelled") . '</td>';
             echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_cancelled_date . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Assigned By") . '</td>';
             echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_approvedby_name . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Status") . '</td>';
             echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_status_name . '</td>';
           echo '</tr>';
           echo '<tr>';
             $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
             echo '<td width="30%" bgcolor="' . $tr_color . '">'
		. lang("Billable") . '</td>';
             echo '<td width="70%" bgcolor="' . $tr_color . '">' . $n_billable . '</td>';
           echo '</tr>';
          ?>
         </table>
        </center>
     <?php
  // add form button for generating detail reports
  $thismonth = date("n") - 1;
  $thisyear = date("Y");
  echo '<form name="Report" method=POST action="' . $GLOBALS['phpgw']->link("/timetrack/detail_report1.php")
	. '">';
  echo '<input type="hidden" name="job_id" value="' . $jobid . '">';
  echo '<center><table width="65%" border="0">'
	. '<th colspan="4" bgcolor="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] . '">'
	. lang("Activity Report") . '</th>'
	. '<tr>'; //<td width="20%"><input type="submit" value="Generate"</td>';

  echo '<td width="40%">&nbsp;' . lang("Start Date") . ':';
  // Set the beginning date to automatically be the same as the quote date here.
  $yr=strval(substr($n_quote_date,0,4));
  $mo=strval(substr($n_quote_date,5,2));
  $da=strval(substr($n_quote_date,8,2));
  CalDateSelector("Report","startdate",0,"",$mo,$da,$yr);
  echo '</td>';

  echo '<td width="40%">&nbsp;' . lang("End Date") . ':';
  CalDateSelector("Report","enddate",0,"");
  echo '</td>';

  echo '<td>';
  //cal_layer();
  echo '</td>';

  echo '<td align="center"><input type="submit" value="'
	. lang("Generate") . '"</td></tr>';

  echo '</table></form></center>';

  $GLOBALS['phpgw']->common->phpgw_footer();
?>
