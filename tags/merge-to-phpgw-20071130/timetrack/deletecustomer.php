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

  /* $Id: deletecustomer.php 15851 2005-04-18 09:03:28Z powerstat $ */

  if ($confirm) {
     $GLOBALS['phpgw_info']["flags"] = array("noheader" => True, "nonavbar" => True);
  }

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

  if (($cid) && (! $confirm)) {
     ?>
     <center>
     <?php
      // Get the company name
      $GLOBALS['phpgw']->db->query("SELECT company_name from phpgw_ttrack_customers WHERE company_id=$cid");
      $GLOBALS['phpgw']->db->next_record();
      $cname = $GLOBALS['phpgw']->db->f("company_name");
      echo "<h3>" . lang("Confirm deletion of customer") . ": $cname</h3>";
     ?>
      <table border=0 with=65%>
       <tr colspan=2>
         <?php 
	   echo "<td>";
	   echo "<center>" . lang("Are you sure you want to delete this customer") . "?</center><br>"; 
	   // Get count of number of jobs for this customer and job details for jobs
	   $GLOBALS['phpgw']->db->query("SELECT job_id, job_number, job_revision from phpgw_ttrack_jobs WHERE company_id=$cid");
	   $numjobs = $GLOBALS['phpgw']->db->num_rows();
	   if ($numjobs > 0) {
	     echo "<b>" . lang("Deletion Details") . ":</b><br>";
	     echo "<p>" . lang("Number of associated jobs that will also be deleted") . ": $numjobs<hr>";
	     // Get all the job_id's so we can find out how many job_details to delete also.
	     $i = 0;
	     while ($GLOBALS['phpgw']->db->next_record())
	     {
	      $job_id[$i] = $GLOBALS['phpgw']->db->f("job_id");
	      $job_number[$i] = $GLOBALS['phpgw']->db->f("job_number");
	      $job_revision[$i] = $GLOBALS['phpgw']->db->f("job_revision");
	      $i++;
	     }
	     $total_jdetails = 0;
	     for ($t=0; $t<$i; $t++)
	     {
	      $GLOBALS['phpgw']->db->query("SELECT count(*) from phpgw_ttrack_job_details WHERE job_id=$job_id[$t]");
	      $GLOBALS['phpgw']->db->next_record();
	      $num_details_this_job = $GLOBALS['phpgw']->db->f(0);
	      $total_jdetails += $num_details_this_job;
	      //echo "<b>" . lang("Deletion Details") . ":<br></b>";
	      echo lang("job no.") . " $cname:$job_number[$t] $job_revision[$t] - $num_details_this_job " .lang("job details") 
		. " " . lang("will be deleted") . "<br>";
	     }
	     echo lang("Total number of job details which will be deleted") . ": $total_jdetails<br>";
	   } else {
	     echo "<p>" . lang("there are no jobs or job details for this customer") . "<br>";
	   }
	 ?>
        <td>
       </tr>
       <tr>
         <td>
           <a href="<?php echo $GLOBALS['phpgw']->link("/timetrack/customers.php") . "\">" . lang("No"); ?></a>
         </td>
         <td>
           <a href="<?php echo $GLOBALS['phpgw']->link("/timetrack/deletecustomer.php","cid=$cid&confirm=true") . "\">" . lang("Yes"); ?></a>
         </td>
       </tr>
      </table>
     </center>
     <?php
     $GLOBALS['phpgw']->common->phpgw_footer();
  }

  if ($confirm) {
     $GLOBALS['phpgw']->db->query("delete from phpgw_ttrack_customers where company_id='$cid'");
     // Note: techinically, we should also delete all jobs that reference this customer too,
     // plus, all associate job_details entries. We don't have to delete assoc contacts, because
     // they are not referenced by an ID.
     $GLOBALS['phpgw']->db->query("SELECT job_id from phpgw_ttrack_jobs WHERE company_id=$cid");
     $i=0;
     while($GLOBALS['phpgw']->db->next_record()){
      $job_id[$i] = $GLOBALS['phpgw']->db->f("job_id");
      $i++;
     }
     for($t=0; $t<$i; $t++){
      $GLOBALS['phpgw']->db->query("DELETE from phpgw_ttrack_job_details WHERE job_id=$job_id[$t]");
     }
     $GLOBALS['phpgw']->db->query("DELETE from phpgw_ttrack_jobs WHERE company_id=$cid");
     Header("Location: " . $GLOBALS['phpgw']->link("/timetrack/customers.php"));
  }
?>
