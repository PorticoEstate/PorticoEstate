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

  /* $Id: editjob.php 9782 2002-03-18 03:18:05Z rschader $ */

  // Update complete for phpgroupware 0.9.10 - 4/15/2001 (api calls for accounts and contacts)

 if($submit) {
  $GLOBALS['phpgw_info']["flags"] = array("noheader" => True, "nonavbar" => True);
 }

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");
?>

<?php
  if ($submit) {

   $quote_date_sql = $quotedate;
   $opened_date_sql = $opendate;
   $deadline_date_sql = $deadlinedate;
   //$completed_date_sql = $completedate;
   //$paidinfull_date_sql = $paidinfulldate;
   //$cancelled_date_sql = $cancelleddate;

   if ($n_billable == "True") { //null value passed
    $billit = "Y";
   } else {
    $billit = "N";
   }

   //$editjob_sqlmain  = "UPDATE phpgw_ttrack_jobs SET contact_id='$n_contact',account_id='$n_employee',"
	//. "summary='" . addslashes($n_summary) . "',description='" . addslashes($n_detail)
      //  . "',quoted_hours='$n_quoted_hours',"
	//. "approved_by='$n_approvedby',status_id='$n_status',billable='$billit',"
	//. "quote_date='$quote_date_sql',opened_date='$opened_date_sql',deadline='$deadline_date_sql',"
	//. "completed_date='$completed_date_sql',paid_date='$paidinfull_date_sql',"
	//. "cancelled_date='$cancelled_date_sql'"
	//. " WHERE job_id='$n_jobid'";

   $editjob_sqlmain  = "UPDATE phpgw_ttrack_jobs SET contact_id='$n_contact',account_id='$n_employee',"
	. "summary='" . addslashes($n_summary) . "',description='" . addslashes($n_detail)
        . "',quoted_hours='$n_quoted_hours',"
	. "approved_by='$n_approvedby',status_id='$n_status',billable='$billit',"
	. "quote_date='$quote_date_sql',opened_date='$opened_date_sql',deadline='$deadline_date_sql' "
	. " WHERE job_id='$n_jobid'";

  $GLOBALS['phpgw']->db->query($editjob_sqlmain);

  echo '<script LANGUAGE="JavaScript">';
  echo 'window.location="' . $GLOBALS['phpgw']->link("/timetrack/jobslist.php", "start=$start&order=$order&filter=$filter"
	. "&query=$query&sort=$sort&qfield=$qfield") . '"';
  echo '</script>';

  } // end submit
else
  {
   inc_cal(); // Init js calendar datepicker
   inc_myutil(); // validation routines, etc for form inputs
 ?>
	 <center><h3>Edit Job Entry</h3></center>
       <form method="POST" name="jobform" action="<?php echo $GLOBALS['phpgw']->link("/timetrack/editjob.php");?>">
	<input type="hidden" name="sort" value="<?php echo $sort; ?>">
	<input type="hidden" name="order" value="<?php echo $order; ?>">
	<input type="hidden" name="query" value="<?php echo $query; ?>">
	<input type="hidden" name="start" value="<?php echo $start; ?>">
	<input type="hidden" name="filter" value="<?php echo $filter; ?>">
	<input type="hidden" name="qfield" value="<?php echo $qfield; ?>">
       <?php
         if ($error) {
            echo "<center>" . lang("Error") . ":$error</center>";
         }
         // Notes for editing jobs:
         // 1. The company name (id) or job_id are not allowed to change.
         //
         // Might as well get all the query info here
	echo "<center>Internal Job ID is: " . $jobid . "</center><br>";
	echo '<input type=hidden name=n_jobid value="' . $jobid . '">';
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
	//$n_completed_date = $GLOBALS['phpgw']->db->f("completed_date");
	//$n_paid_date = $GLOBALS['phpgw']->db->f("paid_date");
	//$n_cancelled_date = $GLOBALS['phpgw']->db->f("cancelled_date");
	// Info to get from other tables: customers.company_name
	// other table info should be able to get when doing the SELECT dropdowns:
	// contact_id(name), account_id(employee), status_name, approved_by.
	$GLOBALS['phpgw']->db->query("select company_name from phpgw_ttrack_customers where company_id=" . $n_company_id);
	$GLOBALS['phpgw']->db->next_record();
	$n_customer = $GLOBALS['phpgw']->db->f("company_name");
       ?>
        <center>
         <table border=0 width=65%>
           <tr>
             <td><?php echo "Company"; ?></td>
	     <td><?php echo $n_customer; ?></td>
           </tr>
           <tr>
             <td><?php echo "Contact"; ?></td>
             <td><select name="n_contact">
		<?php
		  // Need to change to use contacts class here
		$contacts = CreateObject('phpgwapi.contacts');
		$qfields = array(
		  'id' => 'id',
		  'n_given' => 'n_given',
		  'n_family' => 'n_family'
		);
		$start = 0;
		$offset = 0;
		$query = addslashes($n_customer);
		//$filter = "org_name=$n_customer";
		$filter='tid=n';
		$sort = "asc";
		$order = "n_given,n_family";
		$entries = $contacts->read($start,$offset,$qfields,$query,$filter,$sort,$order);
		for ($i=0; $i<count($entries); $i++)
		{
		  $ncontact = $entries[$i]['id'];
		  $contact_name = $entries[$i]['n_given'] . " " . $entries[$i]['n_family'];
	    	  echo '<option value="' . $ncontact . '"';
		  if ($ncontact == $n_contact_id) echo " selected";
		  echo '>' . $contact_name . '</option>';
		}
           ?>
          </select></td>
           </tr>
           <tr>
             <td><?php echo "Assigned To"; ?></td>
             <td>
		<?php
		  echo '<select name="n_employee">';
		  echo '<option value="">Select Employee...</option>';
		  $names = $GLOBALS['phpgw']->accounts->get_list('accounts');
		  for ($i=0; $i<count($names); $i++) {
			$n_employee_id = $names[$i]['account_id'];
			$n_empname = $names[$i]['account_firstname'] . " " . $names[$i]['account_lastname'];
			echo '<option value="' . $n_employee_id . '"';
			if ($n_account_id == $n_employee_id) echo " selected";
			echo '>' . $n_empname . '</option>';
		   }
		  echo '</select>';
		?>
	     </td>
           </tr>
           <tr>
	     <!-- This item is not to be changed on this form. -->
             <td><?php echo "Job Number"; ?></td>
             <td><?php echo $n_job_number; ?></td>
           </tr>
           <tr><!-- Neither is this for now -->
             <td><?php echo "Revision"; ?></td>
             <td><?php echo $n_job_revision; ?></td>
           </tr>
           <tr>
             <td><?php echo "Summary Description"; ?></td>
             <td><input name="n_summary" value="<?php echo $n_summary; ?>" size="40" maxlength="40"></td>
           </tr>
           <tr>
             <td><?php echo "Detailed Description"; ?></td>
             <td><textarea  name="n_detail" cols="40" rows="4" 
		wrap="virtual"><?php echo $n_description ?></textarea></td>
           </tr>
           <tr>
             <td><?php echo "Quote Date"; ?></td><td>
		     <?php 
                  if(($n_quote_date == "") || ($n_quote_date =="0000-00-00"))
                  {
				   CalDateSelector("jobform","quotedate",1,"");
                  } else {
			       $yr=strval(substr($n_quote_date,0,4));
			       $mo=strval(substr($n_quote_date,5,2));
			       $da=strval(substr($n_quote_date,8,2));
			       CalDateSelector("jobform","quotedate",0,"",$mo,$da,$yr);
				  }
			 ?></td>
           </tr>
           <tr>
             <td><?php echo "Quoted Hours"; ?></td>
             <td><input name="n_quoted_hours" 
			 onBlur="CheckNum(this,0,99999);"
			 value="<?php echo $n_quoted_hours; ?>"></td>
           </tr>
           <tr>
             <td><?php echo "Opened Date"; ?></td><td>
                <?php
                  if(($n_opened_date == "") || ($n_opened_date == "0000-00-00"))
                  {
				   CalDateSelector("jobform","opendate",1,"");
                  } else {
			       $yr=strval(substr($n_opened_date,0,4));
			       $mo=strval(substr($n_opened_date,5,2));
			       $da=strval(substr($n_opened_date,8,2));
			       CalDateSelector("jobform","opendate",0,"",$mo,$da,$yr);
				  }
			 ?></td>
           </tr>
           <tr>
             <td><?php echo "Deadline"; ?></td><td>
                <?php
                  if(($n_deadline == "") || ($n_deadline == "0000-00-00"))
                  {
				   CalDateSelector("jobform","deadlinedate",1,"");
                  } else {
			       $yr=strval(substr($n_deadline,0,4));
			       $mo=strval(substr($n_deadline,5,2));
			       $da=strval(substr($n_deadline,8,2));
			       CalDateSelector("jobform","deadlinedate",0,"",$mo,$da,$yr);
			      }
			 ?></td>
           </tr>
		<!-- Removed 3 date entries here because of issues, will be settings the fields internally as audits later -->
           <tr>
             <td><?php echo "Assigned By"; ?></td>
             <td><select name="n_approvedby">
		<option value="">Select...</option>
		<?php
		  // Here we need api calls to get the members of the TTrack_Managers group
              $gid = $GLOBALS['phpgw']->accounts->name2id("TTrack_Managers");
              $members = $GLOBALS['phpgw']->accounts->member($gid);
              for ($i=0; $i<count($members); $i++) 
              {
                $gname = $members[$i]['account_name'];
                $uid = $members[$i]['account_id'];
                $useracct = CreateObject('phpgwapi.accounts',$uid);
                $userInfo = $useracct->read_repository();
                $fullname = $userInfo['firstname'] . " " . $userInfo['lastname'];
		    echo '<option value="' . $uid . '"';
		    if($n_approved_by == $uid) echo " selected";
		    echo ">$fullname</option>";
              }
            ?>
	     </select></td>
           </tr>
           <tr>
	     <!-- Will be a dropdown list -->
	     <!-- I almost wonder if I should change this ENUM to be it's own table? (YES) -->
             <td><?php echo "Status"; ?></td>
             <td><select name="n_status">
		<option value="">Select Status...</option>
		<?php
		 // Note on status and var names used for status change dates: these should
		 // be changed to better reflect the configurable status id's, 1: by 
		 // somehow basing the form elements title on the status_name and 2: by renaming
		 // some of the var's referenced for status changes on forms to be a generic
		 // name (or array) called something like status_change[status_id] or
		 // status_stage_1. This would also neccesitate somehow changing the jobs
		 // table either dynamically or whatever in order to allow it to have a
		 // status change date for every status_id. Once that code change is implemented,
		 // it should be wholly possible to build the dateselector form elements
		 // in a for loop.
		  $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_job_status order by status_id");
		  while ($GLOBALS['phpgw']->db->next_record()) {
			$status_id = $GLOBALS['phpgw']->db->f("status_id");
			echo '<option value="' . $GLOBALS['phpgw']->db->f("status_id") . '"';
			if($status_id == $n_status_id) echo " selected";
			echo '>' . $GLOBALS['phpgw']->db->f("status_name") . '</option>';
	    	  }
		?>
	     </select></td>
           </tr>
           <tr>
             <td><?php echo "Billable"; ?></td>
              <!-- This could just be a checkbox, default to True for billable -->
	     <td><input type="checkbox" name="n_billable" value="True"
		<?php
		  if($n_billable == "Y") echo " CHECKED";
		  echo "></td>";
		?>
           </tr>
           <tr>
             <td colspan=2>
              <input type="submit" name="submit" value="<?php echo lang("submit"); ?>">
             </td>
           </tr>
         </table>
        </center>
       </form>
     <?php
     $GLOBALS['phpgw']->common->phpgw_footer();
  }
?>
