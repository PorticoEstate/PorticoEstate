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
 
  /* $Id: detail_report_bycust.php 11371 2002-10-28 02:36:04Z skeeter $ */

	// Update complete for phpgroupware 0.9.10 - 4/14/2001 (api calls for accounts and contacts)

	$GLOBALS['phpgw_info']['flags'] = Array(
		'enable_nextmatchs_class' => 'True',
		'currentapp' => 'timetrack'
	);

	$friendly = $GLOBALS['HTTP_GET_VARS']['friendly'];
	if ($friendly)
	{
		$GLOBALS['phpgw_info']['flags']['noheader'] = True;
		$bw = 1; // Borderwidth for tables
		$bc = '000000'; // bordercolor
		$cs = 0; // cellspacing
		$cp = 4; // cellpadding
	}
	else
	{
		$bw = 0;
		$bc = 'FFFFFF'; // bordercolor
		$cs = 2; // cellspacing
		$cp = 2; // cellpadding
	}

	include('../header.inc.php');

	// Friendly var setting after config:
	if ($friendly)
	{
		$thbg = 'FFFFFF';
	}
	else
	{
		$thbg = $GLOBALS['phpgw_info']['theme']['th_bg'];
	}

	if ($error)
	{
		echo '<center>' . lang('Error') . ':'.$error.'</center>';
	}
	// Testing:
	//echo "<center>job_id=$job_id, startdate=$startdate, enddate=$enddate<br></center>";
	if(!$enddate)
	{
		$enddate = '2999-12-30';
	}
	if(!$startdate)
	{
		$startdate = '1900-01-01';
		$heading = lang('complete customer activity report');
	}
	else
	{
		$heading = lang('customer activity report from') . ' ' . $startdate . ' ' . lang('to') . ' ' . $enddate;
		$passdate = 1; // we need to pass the start and end dates to printer friendly link
	}

	if ($friendly)
	{
		echo '<table border="0" width="100%">'."\n"
			. '<tr>'."\n"
			. ' <td width="200"><img src="images/timesheet_logo.gif" border="0"></td>'."\n"
			. ' <td><center><h2>' . $heading . '</h2></center></td>'."\n"
			. ' <td width="200">&nbsp;</td>'."\n"
			. '</tr>'."\n"."\n"
			. '</table>'."\n";
	}
	else
	{
		echo '<h2><center>'.$heading.'</center></h2>'."\n";
	}

	// the job list

	$GLOBALS['phpgw']->db->query("select c.company_name,j.job_number,j.job_revision,s.status_name,"
		. "j.summary,j.description,j.contact_id "
		. "from phpgw_ttrack_jobs as j "
		. "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
		. "left join phpgw_ttrack_job_status as s on j.status_id = s.status_id "
		. "WHERE c.company_id='$company_id'");

	//start the table before the loop
	echo '<br><center>'."\n"
		. '<table width="90%" border="' . $bw . '" bordercolor="' . $bc . '" cellspacing="' . $cs . '" cellpadding="' . $cp . '. ">'."\n"
		. '<tr>'."\n"
		. ' <th width="20%" bgcolor="' . $thbg . '"><font color="#000000">'.lang('Company').'</font></th>'."\n"
		. ' <th width="10%" bgcolor="' . $thbg . '"><font color="#000000">'.lang('Job No.').'</font></th>'."\n"
		. ' <th width="60%" bgcolor="' . $thbg . '"><font color="#000000">'.lang('Description').'</font></th>'."\n"
		. ' <th width="10%" bgcolor="' . $thbg . '"><font color="#000000">'.lang('Status').'</font></th>'."\n"
		. '</tr>'."\n";

	$j=0;
	while($GLOBALS['phpgw']->db->next_record())
	{
		$company_name[$j] = $GLOBALS['phpgw']->db->f('company_name');
		$job_number[$j] = $GLOBALS['phpgw']->db->f('job_number');
		$job_revision[$j] = $GLOBALS['phpgw']->db->f('job_revision');
		$description[$j] = $GLOBALS['phpgw']->db->f('description');
		$status_name[$j] = $GLOBALS['phpgw']->db->f('status_name');
		$summary[$j] = $GLOBALS['phpgw']->db->f('summary');
		$contact_id[$j] = $GLOBALS['phpgw']->db->f('contact_id');

		if($description[$j] == '')
		{
			$description[$j] = '&nbsp;';
		}
		$j++;
	}
	for($i=0;$i<$j;$i++)
	{
		// Friendly var setting after config:
		if ($friendly)
		{
			$tr_color = 'FFFFFF';
		}
		else
		{
			$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
		}
		echo '<tr>'."\n"
			. ' <td width="20%" bgcolor="' . $tr_color . '">' . " " . $company_name[$i] . '</td>'."\n"
  			. ' <td width="10%" align="center" bgcolor="' . $tr_color . '">' . $job_number[$i] . $job_revision[$i] . '</td>'."\n"
			. ' <td width="60%" bgcolor="' . $tr_color . '">' . " " . $summary[$i] . '</td>'."\n"
			. ' <td width="10%" align="center" bgcolor="' . $tr_color . '">' . " " . $status_name[$i] . '</td>'."\n"
			. '</tr>'."\n"
			. '<tr>'."\n"
			. ' <td width="20%" bgcolor="' . $tr_color . '"> &nbsp;</td>'."\n"
			. ' <td width="10%" align="center" bgcolor="' . $tr_color . '"> &nbsp;</td>'."\n"
			. ' <td width="60%" bgcolor="' . $tr_color . '"> ' . $description[$i] . '</td>'."\n"
			. ' <td width="10%" align="center" bgcolor="' . $tr_color . '"> &nbsp;</td>'."\n"
			. '</tr>'."\n";
	}
	echo '</table>'."\n";

	// Should add a test here later to sum num_hours for the job, don't print table if num_hours is 0
	// Also need to change this where possible to not use sql for accounts, instead access
	// employee_profiles where possible for needed data. Might want to look at storing Fullname
	// info in employee_profiles as well, to be more independent from core groupware.
	$GLOBALS['phpgw']->db->query("SELECT jj.job_number,jj.job_revision,j.work_date,e.lid,w.catagory_desc,"
		. "j.num_hours,j.comments "
		. "FROM phpgw_ttrack_jobs as jj "
		. "LEFT JOIN phpgw_ttrack_job_details as j on jj.job_id = j.job_id "
		. "LEFT JOIN phpgw_ttrack_emplyprof as e on j.account_id = e.id "
		. "left join phpgw_ttrack_wk_cat as w on j.work_catagory_id = w.work_catagory_id "
		. "WHERE jj.company_id='$company_id' "
		. "AND j.work_date >= '$startdate' AND j.work_date <= '$enddate' "
		. "ORDER by work_date"
	);
	//start the table before the loop
	echo '<br><table width="90%" border="' . $bw . '" bordercolor="' . $bc . '" cellspacing="' . $cs . '" cellpadding="' . $cp . '. ">'."\n"
		. '<tr>'."\n"
		. ' <th width="10%" bgcolor="' . $thbg . '"><font color="#000000">' . lang('Job No.') . '</font></th>'."\n"
		. ' <th width="10%" bgcolor="' . $thbg . '"><font color="#000000">' . lang('Date') . '</font></th>'."\n"
		. ' <th width="15%" bgcolor="' . $thbg . '"><font color="#000000">' . lang('Employee') . '</font></th>'."\n"
		. ' <th width="55%" bgcolor="' . $thbg . '"><font color="#000000">' . lang('Type of Work') . '</font></th>'."\n"
		. ' <th width="10%" bgcolor="' . $thbg . '"><font color="#000000">' . lang('Hours') . '</font></th>'."\n"
		. '</tr>'."\n";

	$total_hours=0;
	$t=0;
	while ($GLOBALS['phpgw']->db->next_record())
	{
		$job_number[$t] = $GLOBALS['phpgw']->db->f('job_number');
		$job_revision[$t] = $GLOBALS['phpgw']->db->f('job_revision');
		$lid[$t] = $GLOBALS['phpgw']->db->f('lid');
		$wdate[$t] = $GLOBALS['phpgw']->db->f('work_date');
		$wcat[$t] = $GLOBALS['phpgw']->db->f('catagory_desc');
		$nhours[$t] = $GLOBALS['phpgw']->db->f('num_hours');
		$comments[$t] = $GLOBALS['phpgw']->db->f('comments');
		if($comments[$t] == '')
		{
			$comments[$t] = '&nbsp;';
		}
		$total_hours = $total_hours + $nhours[$t];
		$t++;
	}
	for ($i=0; $i<$t; $i++)
	{
		$fullname = get_fullname($lid[$i]);
		// The following insures that if the user account has been deleted, we will at least
		// display their historical login name"
		if ($fullname == ' ')
		{
			$employee = $lid[$i];
		}
		else
		{
			$employee = $fullname;
		}

		//$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
		// Friendly var setting after config:
		if ($friendly)
		{
			$tr_color = 'FFFFFF';
		}
		else
		{
			$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
		}
		echo '<tr>'."\n"
			. ' <td width="10%" align="center" bgcolor="' . $tr_color . '"> ' . $job_number[$i] . $job_revision[$i] . '</td>'."\n"
			. ' <td width="10%" align="center" bgcolor="' . $tr_color . '"> ' . $wdate[$i] . '</td>'."\n"
			. ' <td width="15%" align="center" bgcolor="' . $tr_color . '">'  . $employee . '</td>'."\n"
			. ' <td width="55%" bgcolor="' . $tr_color . '"> ' . $wcat [$i]. '</td>'."\n"
			. ' <td width="10%" align="center" bgcolor="' . $tr_color . '"> ' . $nhours[$i] . '</td>'."\n"
			. '</tr>'."\n"
			. '<tr>'."\n"
			. ' <td width="10%" bgcolor="' . $tr_color . '"> &nbsp;</td>'."\n"
			. ' <td width="10%" bgcolor="' . $tr_color . '"> &nbsp;</td>'."\n"
			. ' <td width="15%" align="center" bgcolor="' . $tr_color . '"> &nbsp;</td>'."\n"
			. ' <td width="55%" bgcolor="' . $tr_color . '"> ' . $comments[$i] . '</td>'."\n"
			. ' <td width="10%" align="center" bgcolor="' . $tr_color . '"> &nbsp;</td>'."\n"
			. '</tr>'."\n";
	}
	echo '<tr>'."\n"
		. ' <td width="10%" bgcolor="' . $thbg . '"' . '>&nbsp;</td>'."\n"
		. ' <td width="10%" bgcolor="' . $thbg . '"' . '>&nbsp;</td>'."\n"
		. ' <td width="15%" bgcolor="' . $thbg . '"' . '>&nbsp;</td>'."\n"
		. ' <th width="55%" align="right" bgcolor="' . $thbg . '">' . lang('Total Hours') . ':</th>'."\n"
		. ' <th width="10%" align="center" bgcolor="' . $thbg . '">' . sprintf("%01.2f",$total_hours) . '</th>'."\n"
		. '</tr>'."\n"
		. '</table>'."\n";
	if (!$friendly)
	{
		// add link for printer friendly version
		if ($passdate == 1)
		{
			$passstr = Array(
				'startdate' => $startdate,
				'enddate'   => $enddate
			);
		}
		else
		{
			$passstr = Array();
		}
		echo '<P>&nbsp;<a href="' . $GLOBALS['phpgw']->link('/timetrack/detail_report_bycust.php',
				Array(
					'job_id'	=> $job_id,
					'friendly' => 1
				)+$passstr
			) . '" target="new_printer_friendly"' . " onMouseOver=\"window.status='" . lang('Generate printer-friendly version') . "';\">" . '[' . lang('Printer Friendly') . ']</A>';
		$GLOBALS['phpgw']->common->phpgw_footer();
	}
?>
