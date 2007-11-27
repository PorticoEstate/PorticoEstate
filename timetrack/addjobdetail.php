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

	/* $Id: addjobdetail.php,v 1.21 2003/03/20 22:04:59 gugux Exp $ */

	if($submit_detail) {
		$GLOBALS['phpgw_info']["flags"] = array("noheader" => True, "nonavbar" => True);
	}

	$GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
	$GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
	include("../header.inc.php");

	if ($submit_detail) {
		$worked_date_sql = $workdate;

		if($n_start_time_ampm == "pm" && $n_start_time_h < 12) {
			$n_start_time_h += 12;
		}
		if($n_start_time_ampm == "am" && $n_start_time_h == 12) {
			$n_start_time_h = 0;
		}
		$starttime = $n_start_time_h . ":" . $n_start_time_m . ":00";

		if($n_end_time_ampm == "pm" && $n_end_time_h < 12) {
			$n_end_time_h += 12;
		}
		if($n_end_time_ampm == "am" && $n_end_time_h == 12) {
			$n_end_time_h = 0;
		}
		$endtime = $n_end_time_h . ":" . $n_end_time_m . ":00";

		if ($n_billable == "True") { //null value passed
			$billit = "Y";
		} else {
			$billit = "N";
		}

   		if($n_jobnum && $n_employee && $n_catagory && $worked_date_sql && $n_whours) {
			$newjobdetail_sql = "insert into phpgw_ttrack_job_details "
			. "(job_id, account_id, work_catagory_id, work_date, start_time, "
			. "end_time, num_hours, detail_billable, comments) "
			. "VALUES ('$n_jobnum','$n_employee','$n_catagory','$worked_date_sql','$starttime',"
			. "'$endtime','$n_whours','$billit','" . addslashes($n_comments) . "')";
			$GLOBALS['phpgw']->db->query($newjobdetail_sql);

			// Add code here to update the total_hours field in jobs from the SUM'd job_details
			$GLOBALS['phpgw']->db->query("SELECT sum(num_hours) from phpgw_ttrack_job_details "
			. "WHERE job_id=$n_jobnum");
			$GLOBALS['phpgw']->db->next_record();
			$total = $GLOBALS['phpgw']->db->f(0);
			$GLOBALS['phpgw']->db->query("UPDATE phpgw_ttrack_jobs set total_hours='$total' WHERE job_id=$n_jobnum");

			echo '<script LANGUAGE="JavaScript">';
			echo 'window.location="' . $GLOBALS['phpgw']->link("/timetrack/timesheets.php") . '"';
			echo '</script>';
		} else {
			// Error:
			inc_myutil();
			echo "You forgot something that was needed! Use your back button to correct.";
			// added a little something to wake-up the user regarding his mistakes.
			echo "<script LANGUAGE=\"JavaScript1.2\">shake(2);</script>";
		}

	} // end submit
	else
	{
		inc_cal(); // Init js calendar datepicker
		inc_myutil(); // validation routines, etc for form inputs
		$tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('timetrack'));
		$tpl->set_file(array('body' => 'addjobdetail.tpl'));

		$tpl->set_var("page_title", lang("Job Detail Entry"));

		// Get the userid so we can put it in a hidden form element
		$n_employee = $GLOBALS['phpgw_info']["user"]["account_id"];
		$tpl->set_var("fullname", lang("for") . ' ' . get_fullname($n_employee));

		$tpl->set_var("formname", "jobform");
		$tpl->set_var("postlink", $GLOBALS['phpgw']->link("/timetrack/addjobdetail.php"));

		$tpl->set_var("hidden_name", $n_employee);

		$cust = $n_customer;
		$cnamesize = 'SIZE="' . $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack"]["cnamesize"] . '"';
		if ($error) {
			echo "<center>" . lang("Error") . ":$error</center>";
		}
		$company_label = lang('Company'); 
		if ($GLOBALS['phpgw_info']["user"]["preferences"]["timetrack"]["cnamesize"] > 1 && $cust)
		{
			$company_label .= '<br><i>' . lang("Selected") . ':</i><br>';
			$GLOBALS['phpgw']->db->query("select company_name from phpgw_ttrack_customers where "
			. "company_id = $cust");
			$GLOBALS['phpgw']->db->next_record();
			$company_label .= '<b>' . $GLOBALS['phpgw']->db->f(0) . "</b>";
		}
		$tpl->set_var("company_label", $company_label);
		// need to populate a drop down list here
		// May want to add a where clause later to only present customers whose
		// who are current to keep list short (use some kind of active flag in table)
		$company_element = '<select name="n_customer" ' . $cnamesize
			. ' onChange="this.form.submit()">'
			. '<option value="">' . lang("Select Customer") . '...</option>';

		$GLOBALS['phpgw']->db->query("select company_id,company_name from "
		. "phpgw_ttrack_customers where active='Y' "
		. "order by company_name");
		while ($GLOBALS['phpgw']->db->next_record()) {
			$ncust = $GLOBALS['phpgw']->db->f('company_id');
			$company_element .= '<option value="' . $ncust . '"';
			if ( $cust == $ncust ) {
				$company_element .= ' selected';
			}
			$company_element .=  '>' . $GLOBALS['phpgw']->db->f("company_name") . "</option>";
		} 
		$company_element .= '</select>';
		$tpl->set_var('company_element', $company_element);

		$tpl->set_var('jobnum_label', lang('Job Number'));

		$jobnum_element = '<select name="n_jobnum">';
                
		// Check value of $cust, if it is empty, just put a null option telling user to
		// pick the customer first.
		if (! $cust) {
                  $jobnum_element .= '<option value="">^-'
			. lang("Pick Customer First") . '-^</option>';
		} else {
			$jobnum_sql = "select job_id,job_number,job_revision,summary "
			. "from phpgw_ttrack_jobs where company_id = '$cust'"
			. " order by job_number desc,job_revision desc";
			$GLOBALS['phpgw']->db->query($jobnum_sql);
			$test_result = $GLOBALS['phpgw']->db->num_rows();
			if ($test_result == 0) {
				$jobnum_element .= '<option value="">'
				. lang("No Match") . '</option>';
			}
			while ($GLOBALS['phpgw']->db->next_record()) {
				$njobid = $GLOBALS['phpgw']->db->f("job_id");
				$job_name = $GLOBALS['phpgw']->db->f("job_number") . $GLOBALS['phpgw']->db->f("job_revision")
				. " - " . $GLOBALS['phpgw']->db->f("summary");
				$jobnum_element .= '<option value="' . $njobid . '">' . $job_name . '</option>';
			}
		}
		$jobnum_element .= '</select>';

		$tpl->set_var('jobnum_element', $jobnum_element);

		$tpl->set_var('catagory_label', lang('Category'));

		$catagory_element = '<select name="n_catagory">'
		. '<option value="">'
		. lang('Select Work Type') . '...'
		. '</option>';
		$GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_wk_cat "
			. "order by work_catagory_id");
		while ($GLOBALS['phpgw']->db->next_record()) {
			$n_catagory_id = $GLOBALS['phpgw']->db->f("work_catagory_id");
			$n_catname = $GLOBALS['phpgw']->db->f("catagory_desc");
			$catagory_element .= '<option value="' . $n_catagory_id . '">' . $n_catname . '</option>';
		}
		$catagory_element .= '</select>';

		$tpl->set_var('catagory_element', $catagory_element);

		$tpl->set_var('workdate_label', lang('Date Worked'));

		$tpl->set_var('workdate_element', tcaldateselector("jobform","workdate",0,""));

		$tpl->set_var('starttime_label', lang('Start Time'));
		$tpl->set_var('starttime_element', ttimeselect3("jobform","n_start_time",0));

		$tpl->set_var('endtime_label', lang('End Time'));
		// Problem here if the added hour is after midnight, the AM_PM radio button is not being set properly.
		// Fixed in ttimeselect3using switch statements?
		$e_hour = sprintf("%02d", date("H") + 1);
		$tpl->set_var('endtime_element', ttimeselect3("jobform","n_end_time",0,$e_hour));
		if(!$n_whours) $n_whours = "1.00";

		$tpl->set_var('hoursworked_label', lang('Hours Worked'));
		$hoursworked_element = '<input name="n_whours" size="8" maxlength="6" '
		. 'onBlur="CheckNum(this,0,24,1);Calc_endtime(\'jobform\',\'n_whours\',\'n_start_time\',\'n_end_time\',4);" '
		. 'value="' . $n_whours . '">';
		$tpl->set_var('hoursworked_element', $hoursworked_element);

		$tpl->set_var('billable_label', lang('Billable'));
		$billable_element = '<input type="checkbox" name="n_billable" value="True" CHECKED>';
		$tpl->set_var('billable_element', $billable_element);

		$tpl->set_var('comments_label', lang('Work Comments'));
		$comments_element = '<textarea  name="n_comments" cols="40" rows="4" '
		. 'onBlur="this.value = capitalizeFirstWord(this.value);" '
		. 'wrap="virtual">' . $n_comments . '</textarea>';
		$tpl->set_var('comments_element', $comments_element);


		$submit_element = '<input type="submit" name="submit_detail" value="' . lang("submit")
		. '" onclick="gethours(\'jobform\',\'n_whours\',\'n_start_time\',\'n_end_time\')";>';
		$tpl->set_var('submit_bar', $submit_element);

		$tpl->pparse('out','body');

		$GLOBALS['phpgw']->common->phpgw_footer();
	}
//}
?>

