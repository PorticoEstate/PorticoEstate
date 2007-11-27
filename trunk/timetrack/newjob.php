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

  /* $Id: newjob.php 9782 2002-03-18 03:18:05Z rschader $ */
  // Update complete for phpgroupware 0.9.10 - 4/17/2001 (api calls for accounts and contacts)

 if ($submit_job) {
  $GLOBALS['phpgw_info']["flags"] = array("noheader" => True, "nonavbar" => True);
 }

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");
?>

<?php
  if ($submit_job) {
  $quote_date_sql = $quotedate;
  $opened_date_sql = $opendate;
  $deadline_date_sql = $deadlinedate;

   // What so we need to validate before inserting this job into the db?

   // 1. Double check (re-query) db to make sure jobnum/rev isn't already taken.
   // 2. Quoted Hours should be allowed to have a T&M flag somehow?
   // 3. Opened date will not be set if staus is only "Quoted".
   // 4. Deadline date MUST be later than Quoted or Opened Date.

   // Main integrity check:
   if($n_customer && $n_jobnum) {
     // First, lets make sure the job doesn't already exist:
     $GLOBALS['phpgw']->db->query("SELECT * from phpgw_ttrack_jobs where company_id='$n_customer' AND "
	. "job_number='$n_jobnum' AND job_revision='$n_rev'");
     if($GLOBALS['phpgw']->db->num_rows() > 0){ // The job already exists
       echo lang("Error: The specified job already exists") . "<br>"
         . lang("Please use the back button to re-examine entry");
       exit;
     }

     if ($n_billable == "True") { //null value passed
      $billit = "Y";
     } else {
       $billit = "N";
     }
     $newjob_sql = "insert into phpgw_ttrack_jobs "
	. "(company_id, contact_id, account_id, job_number, job_revision, summary, "
	. "description, quote_date, quoted_hours, opened_date, deadline, "
	. "approved_by, status_id, billable) "
	. "VALUES ('$n_customer','$n_contact','$n_employee','$n_jobnum','$n_rev','"
        . addslashes($n_summary) . "','" . addslashes($n_detail) . "',"
	. "'$quote_date_sql','$n_quoted_hours','$opened_date_sql',"
	. "'$deadline_date_sql','$n_approvedby',"
	. "'$n_status','$billit')";
    $GLOBALS['phpgw']->db->query($newjob_sql);

    echo '<script LANGUAGE="JavaScript">';
    echo 'window.location="' . $GLOBALS['phpgw']->link("/timetrack/index.php") . '"';
    echo '</script>';
  } else { // report error
    echo lang("The Company Name and Job Number fields are required!") . "<br>";
    echo lang("Please use the back button to correct these entries and re-submit");
  } // end main integrity check

  } // end submit
else
  {
   inc_cal();
   inc_myutil();
?>
       <center><h3><?php echo lang("New Job Entry");?></h3></center>
       <form method="POST" name="jobform" 
	action="<?php echo $GLOBALS['phpgw']->link("/timetrack/newjob.php");?>">
       <?php
	 $cust = $n_customer;
	 $cnamesize = 'SIZE="' . $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack"]["cnamesize"] . '"';
         if ($error) {
            echo "<center>" . lang("Error") . ":$error</center>";
         }
       ?>
        <center>
         <table border=0 width=65%>
           <tr>
             <td>
		<?php echo lang("Company"); 
		  if ($GLOBALS['phpgw_info']["user"]["preferences"]["timetrack"]["cnamesize"] > 1 && $cust)
		   {
		    echo '<br><i>' . lang("Selected") . ':</i><br>';
		    $GLOBALS['phpgw']->db->query("select company_name from phpgw_ttrack_customers where "
			  . "company_id = $cust");
		    $GLOBALS['phpgw']->db->next_record();
		    echo "<b>" . $GLOBALS['phpgw']->db->f(0) . "</b>";
		   }
	 	?></td>
		<?php // need to populate a drop down list here
		   // May want to add a where clause later to only present customers whose
		   // who are current to keep list short (use some kind of active flag in table)
		?>
		<td><select name="n_customer" 
		<?php echo $cnamesize; ?>
		 onChange="this.form.submit()">
		 <!-- Let's do our empty option first -->
		 <option value="">
		 <?php echo lang("Select Customer") . "...";?></option>
		 <?php
		  $GLOBALS['phpgw']->db->query("select company_id,company_name "
			. "from phpgw_ttrack_customers where active='Y' "
			. "order by company_name");
		  while ($GLOBALS['phpgw']->db->next_record()) {
                    $ncust = $GLOBALS['phpgw']->db->f("company_id");
			echo '<option value="' . $ncust . '"';
                         if ( $cust == $ncust ) {
                           echo " selected";
                         }
			echo ">" . $GLOBALS['phpgw']->db->f("company_name") . "</option>";
		   } 
 		  ?>	
                  </select></td>
           </tr>
           <tr>
             <td><?php echo lang("Contact"); ?></td>
             <td><select name="n_contact">
                
		<?php 
		// Check value of $cust, if it is empty, just put a null option telling user to
		// pick the customer first.
		if (! $cust) {
                  echo '<option value="">^-'
			. lang("Pick Customer First") . '-^</option>';
		} else {
			// The following query should never fail due to lack of data:
			$GLOBALS['phpgw']->db->query("SELECT company_name from phpgw_ttrack_customers "
				. "where company_id=$cust");
			$GLOBALS['phpgw']->db->next_record();
			$compname = $GLOBALS['phpgw']->db->f("company_name");

			$contacts = CreateObject('phpgwapi.contacts');
			$qfields = array(
			  'id' => 'id',
			  'n_given' => 'n_given',
			  'n_family' => 'n_family'
			);
			$start = 0;
			$offset = 0;
			$query = addslashes($compname);
			//$query = "";
			//$filter = "org_name=" . addslashes($compname);
			$filter='tid=n';
			$sort = "asc";
			$order = "n_given,n_family";
			$entries = 
			  $contacts->read($start,$offset,$qfields,$query,$filter,$sort,$order);
			if (count($entries) == 0) {
			  echo'<option value="">'
				. lang("No Match") . '</option>';
			}
			for ($i=0; $i<count($entries); $i++)
			{
			  $ncontact = $entries[$i]['id'];
			  $contact_name = $entries[$i]['n_given'] 
				. " " . $entries[$i]['n_family'];
	    		  echo '<option value="' . $ncontact . '"';
			  if ($ncontact == $n_contact_id) echo " selected";
			  echo '>' . $contact_name . '</option>';
			}
		}
                ?>
             </select></td>
           </tr>
           <tr>
             <td><?php echo lang("Assigned To"); ?></td>
             <td>
		<?php
		  echo '<select name="n_employee">';
		  echo '<option value="">'
			. lang("Select Employee") . '...</option>';
		  $names = $GLOBALS['phpgw']->accounts->get_list('accounts');
		  for ($i=0; $i<count($names); $i++) {
			$n_employee_id = $names[$i]['account_id'];
			$n_empname = $names[$i]['account_firstname'] . " " 
			  . $names[$i]['account_lastname'];
			echo '<option value="' . $n_employee_id . '"';
			// Preselect the current user here: (maybe)
			if ($GLOBALS['phpgw_info']["user"]["account_id"] == $n_employee_id) echo " selected";
			echo '>' . $n_empname . '</option>';
		   }
		  echo '</select>';
		?>
	     </td>
           </tr>
           <tr>
	     <!-- This should query the jobs db for the highest job number and increment it 1 -->
             <td><?php echo lang("Job Number"); ?></td>
		<?php
		if ($cust) {
		  $jobnum_sql = "select MAX(job_number) from phpgw_ttrack_jobs "
			. "where company_id='$cust'";
		  $GLOBALS['phpgw']->db->query($jobnum_sql);
		  $GLOBALS['phpgw']->db->next_record();
		  $n_jobnum = $GLOBALS['phpgw']->db->f(0) + 1;
		  //$n_jobnum = 100;
		}
		?>
             <td><input name="n_jobnum" size="5" maxlength="5"
		  onBlur="CheckNum(this,0,99999);"
		  value="<?php echo $n_jobnum; ?>">
             &nbsp;&nbsp;<?php echo "Revision"; ?> &nbsp;
             <input name="n_rev" size="2" maxlength="2" 
		  onBlur="this.value = capitalizeAll(this.value);"
		  value="<?php echo $n_rev; ?>"></td>
           </tr>
           <tr>
             <td><?php echo lang("Summary Description"); ?></td>
             <td><input name="n_summary" value="<?php echo $n_summary; ?>" size="40" maxlength="40"
		  onBlur="this.value = capitalizeFirstWord(this.value);"></td>
           </tr>
           <tr>
             <td><?php echo lang("Detailed Description"); ?></td>
             <td><textarea  name="n_detail" cols="40" rows="4"
		onBlur="this.value = capitalizeFirstWord(this.value);"
		wrap="virtual"><?php echo $n_detail; ?></textarea></td>
           </tr>
           <tr>
             <td><?php echo lang("Quote Date"); ?></td>
	     <td><?php 
		 	   //DateSelector("quote",time());
			   CalDateSelector("jobform","quotedate",0,"");
			 ?></td>
           </tr>
           <tr>
             <td><?php echo lang("Quoted Hours"); ?></td>
             <td><input name="n_quoted_hours" 
		  onBlur="CheckNum(this,0,99999);"
		  value="<?php echo $n_quoted_hours; ?>"></td>
           </tr>
           <tr>
             <td><?php echo lang("Opened Date"); ?></td>
	     <td><?php 
		       //DateSelector("opened");
			   CalDateSelector("jobform","opendate",0,"");
			 ?></td>
           </tr>
           <tr>
             <td><?php echo lang("Deadline"); ?></td>
	     <td><?php 
		 	   //DateSelector("deadline");
			   CalDateSelector("jobform","deadlinedate",0,"");
			 ?></td>
           </tr>
           <tr>
	     <!-- Could be a dropdown list -->
             <td><?php echo lang("Assigned By"); ?></td>
             <td><select name="n_approvedby">
		<option value="">
		  <?php echo lang("Select") . '...';?>
		</option>
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
             <td><?php echo lang("Status"); ?></td>
             <td><select name="n_status">
		<option value="">
		  <?php echo lang("Select Status") . '...';?>
		</option>
		<?php
		  $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_job_status order by status_id");
		  while ($GLOBALS['phpgw']->db->next_record()) {
			echo '<option value="' . $GLOBALS['phpgw']->db->f("status_id") . '">' . 
				$GLOBALS['phpgw']->db->f("status_name") . '</option>';
	    	  }
		?>
	     </select></td>
           </tr>
           <tr>
             <td><?php echo lang("Billable"); ?></td>
              <!-- This could just be a checkbox, default to True for billable -->
	     <td><input type="checkbox" name="n_billable" value="True" CHECKED></td>
           </tr>
           <tr>
             <td colspan=2>
              <input type="submit" name="submit_job" value="<?php echo lang("submit"); ?>">
             </td>
           </tr>
         </table>
        </center>
       </form>
     <?php
     $GLOBALS['phpgw']->common->phpgw_footer();
  }
?>
