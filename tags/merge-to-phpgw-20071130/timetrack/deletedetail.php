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

  /* $Id: deletedetail.php 15851 2005-04-18 09:03:28Z powerstat $ */

  // Update complete for phpgroupware 0.9.10 - 4/14/2001 (api calls for accounts and contacts)

  if ($confirm) {
     $GLOBALS['phpgw_info']["flags"] = array("noheader" => True, "nonavbar" => True);
  }

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

  if (($jd_id) && (! $confirm)) {
     ?>
     <center>
      <table border=0 with=65%>
       <tr colspan=2>
        <td align=center>
         <?php echo lang("Are you sure you want to delete this Timesheet Entry") . "?"; ?>
        <td>
       </tr>
       <tr>
         <td>
           <a href="<?php 
	     echo $GLOBALS['phpgw']->link("/timetrack/timesheets.php",
	      "year=$year&month=$month&day=$day&n_employee=$n_employee") 
	      . "\">" . lang("No"); ?></a>
         </td>
         <td>
           <a href="<?php 
	     echo $GLOBALS['phpgw']->link("/timetrack/deletedetail.php",
	      "jd_id=$jd_id&confirm=true&year=$year&month=$month&day=$day&n_employee=$n_employee&jobid=$jobid") 
	      . "\">" . lang("Yes"); ?></a>
         </td>
       </tr>
      </table>
     </center>
     <?php
     $GLOBALS['phpgw']->common->phpgw_footer();
  }

  if ($confirm) {
    $GLOBALS['phpgw']->db->query("delete from phpgw_ttrack_job_details where detail_id='$jd_id'");
    // Add code to recalc total_hours in jobs table.
    // Add code here to update the total_hours field in jobs from the SUM'd job_details
    $GLOBALS['phpgw']->db->query("SELECT sum(num_hours) from phpgw_ttrack_job_details "
	. "WHERE job_id=$jobid");
    $GLOBALS['phpgw']->db->next_record();
    $total = $GLOBALS['phpgw']->db->f(0);
    $GLOBALS['phpgw']->db->query("UPDATE phpgw_ttrack_jobs set total_hours='$total' WHERE job_id=$jobid");

     Header("Location: " . $GLOBALS['phpgw']->link("/timetrack/timesheets.php",
	"year=$year&month=$month&day=$day&n_employee=$n_employee"));
  }
?>
