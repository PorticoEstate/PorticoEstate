<?php
	/**************************************************************************\
	* phpgwtimetrack - phpGroupWare addon application                          *
	* http://phpgwtimetrack.sourceforge.net                                    *
	* Written by Robert Schader <bobs@product-des.com>                         *
	* Written by Joseph Engo <jengo@phpgroupware.org>                          *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: editdetail.php,v 1.17 2003/03/20 22:04:59 gugux Exp $ */

	// Update complete for phpgroupware 0.9.10 - 4/14/2001 (api calls for accounts and contacts)
	// Could not find where this file needed any significant changes to work in 0.9.10

	// This file is being updated to use the new "TimeSelect2()" function instead
	// of TimeSelector(). Also change to new DateSelector() function.

	if($submit || $submit_new) {
		$GLOBALS['phpgw_info']["flags"] = array("noheader" => True, "nonavbar" => True);
	}

	$GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
	$GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
	include("../header.inc.php");

	if ($submit  || $submit_new) {
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

		if ($n_detail_billable == "True") { //null value passed
			$billit = "Y";
		} else {
			$billit = "N";
		}

		// Simple error checking
		if($n_catagory && $worked_date_sql && $starttime && $n_whours) {
			if($submit) {
				$editjdet_sql = "UPDATE phpgw_ttrack_job_details SET work_catagory_id=$n_catagory,"
				. "work_date='$worked_date_sql',start_time='$starttime',"
				. "end_time='$endtime',num_hours='$n_whours',detail_billable='$billit',"
				. "comments='" . addslashes($n_comments) . "'"
				. " WHERE detail_id=$n_detail_id";
				$GLOBALS['phpgw']->db->query($editjdet_sql);
			} else { // Only alternative is $submit_new
				$newjobdetail_sql = "insert into phpgw_ttrack_job_details (job_id, account_id, "
				. "work_catagory_id, work_date, start_time, "
				. "end_time, num_hours, detail_billable, comments) "
				. "VALUES ('$n_job_id','$n_employee','$n_catagory','$worked_date_sql','$starttime',"
				. "'$endtime','$n_whours','$billit','" . addslashes($n_comments) . "')";
				$GLOBALS['phpgw']->db->query($newjobdetail_sql);
			}
			// Add code here to update the total_hours field in jobs from the SUM'd job_details
			$GLOBALS['phpgw']->db->query("SELECT sum(num_hours) from phpgw_ttrack_job_details "
			. "WHERE job_id=$n_job_id");
			$GLOBALS['phpgw']->db->next_record();
			$total = $GLOBALS['phpgw']->db->f(0);
			$GLOBALS['phpgw']->db->query("UPDATE phpgw_ttrack_jobs set total_hours='$total' "
			. "WHERE job_id=$n_job_id");

			$yr=strval(substr($workdate,0,4));
			$mo=strval(substr($workdate,5,2));
			$da=strval(substr($workdate,8,2));
			$ytext = "year=$yr&month=$mo&day=$da";

			echo '<script LANGUAGE="JavaScript">';
			echo 'window.location="' 
			. $GLOBALS['phpgw']->link("/timetrack/timesheets.php","$ytext&n_employee=$n_employee") . '"'
			. '</script>';
		} else { // Error, go back
			echo "You made a mistake, please use your back button to correct it";
		}
	} // end submit
	else
	{
		inc_cal(); // Init js calendar datepicker
		inc_myutil(); // validation routines, etc for form inputs
		$tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('timetrack'));
		$tpl->set_file(array('body' => 'editdetail.tpl'));

		$tpl->set_var('page_title', lang('Edit Time Entry'));
		$tpl->set_var('detailid_label', lang('Detail ID'));
		$tpl->set_var('detailid_element', $detailid);

		$tpl->set_var('formname', 'jobform');
		$tpl->set_var('postlink', $GLOBALS['phpgw']->link('/timetrack/editdetail.php'));

		$tpl->set_var('hidden_detail_id', $detailid);

		$GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_job_details where detail_id=" . $detailid);
		$GLOBALS['phpgw']->db->next_record();
		$n_job_id = $GLOBALS['phpgw']->db->f("job_id");
		$n_account_id = $GLOBALS['phpgw']->db->f("account_id");
		$n_work_catagory_id = $GLOBALS['phpgw']->db->f("work_catagory_id");
		$n_work_date = $GLOBALS['phpgw']->db->f("work_date");
		$n_start_time = $GLOBALS['phpgw']->db->f("start_time");
		$n_end_time = $GLOBALS['phpgw']->db->f("end_time");
		$n_whours = $GLOBALS['phpgw']->db->f("num_hours");
		$n_detail_billable = $GLOBALS['phpgw']->db->f("detail_billable");
		$n_comments = $GLOBALS['phpgw']->db->f("comments");
		// Need to now get customer_id and job_number,revision from jobs table, then
		// get customers.company_name.
		$GLOBALS['phpgw']->db->query("select company_id,job_number,job_revision,summary "
		. "from phpgw_ttrack_jobs where job_id=" . $n_job_id);
		$GLOBALS['phpgw']->db->next_record();
		$n_company_id = $GLOBALS['phpgw']->db->f("company_id");
		$n_job_number = $GLOBALS['phpgw']->db->f("job_number");
		$n_job_revision = $GLOBALS['phpgw']->db->f("job_revision");
		$n_summary = $GLOBALS['phpgw']->db->f("summary");
		// Info to get from other tables: customers.company_name
		// other table info should be able to get when doing the SELECT dropdowns:
		// contact_id(name), account_id(employee), status_name, approved_by.
		$GLOBALS['phpgw']->db->query("select company_name from phpgw_ttrack_customers "
		. "where company_id=" . $n_company_id);
		$GLOBALS['phpgw']->db->next_record();
		$n_customer = $GLOBALS['phpgw']->db->f("company_name");

		// For passing account_id back to timesheets.php
		$tpl->set_var('hidden_employee_id', $n_account_id);
		// For passing other, non editable items back when submitting using the "Add As New" button
		$tpl->set_var('hidden_job_id', $n_job_id);

		$tpl->set_var('company_label', lang('Company'));
		$tpl->set_var('company_element', $n_customer);

		$tpl->set_var('jobnum_label', lang('Job Number'));
		$tpl->set_var('jobnum_element', $n_job_number);

		$tpl->set_var('jobrev_label', lang('Revision'));
		$tpl->set_var('jobrev_element', $n_job_revision);

		$tpl->set_var('summary_label', lang('Summary'));
		$tpl->set_var('summary_element', $n_summary);

		/********************************************************************/

		$catagory_element = '<select name="n_catagory">'
		. '<option value="">'
		. lang('Select Work Type') . '...'
		. '</option>';
		$GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_wk_cat "
		. "order by work_catagory_id");
		while ($GLOBALS['phpgw']->db->next_record()) {
			$n_catagory_id = $GLOBALS['phpgw']->db->f("work_catagory_id");
			$n_catname = $GLOBALS['phpgw']->db->f("catagory_desc");
			$catagory_element .= '<option value="' . $n_catagory_id . '"';
			if($n_catagory_id == $n_work_catagory_id) {
				$catagory_element .= "selected";
			}
			$catagory_element .= '>' . $n_catname . '</option>';
		}
		$catagory_element .= '</select>';
		$tpl->set_var('catagory_label', lang('Category'));
		$tpl->set_var('catagory_element', $catagory_element);

		/********************************************************************/

		// Need to turn quote date into a unix timestamp here:
		if(($n_work_date != "") && ($n_work_date !="0000-00-00"))
		{
			$work_date = mysql_datetime_to_timestamp($n_work_date);
		} else {
			$work_date = 0;
		}
		$yr=strval(substr($n_work_date,0,4));
		$mo=strval(substr($n_work_date,5,2));
		$da=strval(substr($n_work_date,8,2));
		$tpl->set_var('workdate_label', lang('Date Worked'));
		$tpl->set_var('workdate_element', tcaldateselector('jobform','workdate',0,'',$mo,$da,$yr));

		/********************************************************************/

		$tpl->set_var('starttime_label', lang('Start Time'));
		$tpl->set_var('starttime_element', 
			ttimeselect3("jobform","n_start_time",0,strval(substr($n_start_time,0,2)),strval(substr($n_start_time,3,2))));

		/********************************************************************/

		$tpl->set_var('endtime_label', lang('End Time'));
		$tpl->set_var('endtime_element',
			ttimeselect3("jobform","n_end_time",0,strval(substr($n_end_time,0,2)),strval(substr($n_end_time,3,2))));

		/********************************************************************/

		$hoursworked_element = '<input name="n_whours" size="8" maxlength="6" '
		. 'onBlur="CheckNum(this,0,24);Calc_endtime(\'jobform\',\'n_whours\',\'n_start_time\',\'n_end_time\',4);" '
		. 'value="' . $n_whours . '">';
		$tpl->set_var('hoursworked_label', lang('Hours Worked'));
		$tpl->set_var('hoursworked_element', $hoursworked_element);

		/********************************************************************/

		// This could just be a checkbox, default to True for billable
		$billable_element = '<input type="checkbox" name="n_detail_billable" value="True" ';
		if($n_detail_billable == 'Y') {
			$billable_element .= ' CHECKED';
		}
		$billable_element .= '>';
		$tpl->set_var('billable_label', lang('Billable'));
		$tpl->set_var('billable_element', $billable_element);

		/********************************************************************/

		$comments_element = '<textarea  name="n_comments" cols="40" rows="4" '
		. 'onBlur="this.value = capitalizeFirstWord(this.value);" '
		. 'wrap="virtual">' . $n_comments . '</textarea>';
		$tpl->set_var('comments_label', lang('Work Comments'));
		$tpl->set_var('comments_element', $comments_element);

		/********************************************************************/

		$submit_update_element = '<input type="submit" name="submit" value="' . lang('Update') . '">';
		$tpl->set_var('submit_update', $submit_update_element);

		$submit_new_element = '<input type="submit" name="submit_new" value="' . lang('Add As New') . '">';
		$tpl->set_var('submit_new', $submit_new_element);

		$myyear = date("Y", $work_date);
		$mymonth = date("m", $work_date);
		$myday = date("d", $work_date);

		$cancel_element = '<A HREF="' . $GLOBALS['phpgw']->link('/timetrack/timesheets.php', 
           	"year=$myyear&month=$mymonth&day=$myday&n_employee=$n_account_id")
		. '">' . lang("Cancel") . '</a>';
		$tpl->set_var('cancel_link', $cancel_element);

		$delete_element = '<A HREF="' . $GLOBALS['phpgw']->link('/timetrack/deletedetail.php',
		"jd_id=$detailid&year=$myyear&month=$mymonth&day=$myday&n_employee=$n_account_id&jobid=$n_job_id")
		. '">' . lang("Delete") . '</a>';
		$tpl->set_var('delete_link', $delete_element);

		$tpl->pparse('out','body');

		$GLOBALS['phpgw']->common->phpgw_footer();
	}
?>
