<?php
  /**************************************************************************\
  * phpgwtimetrack - phpGroupWare addon application                          *
  * http://phpgwtimetrack.sourceforge.net                                    *
  * Written by Robert Schader <bobs@product-des.com>                         *
  * This page written by Camden Spiller <camden@arrowtech.net>		     *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  // As far as I am concerned, this file is no longer in use by anyone, so it
  // has not been updated. (Bob Schader)


  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";

  include("../header.inc.php");

  // Setup $searchobj array for setting up a listbox on the search form
  // so we can narrow down what field to query on.
  // Note: cname is a sql alias for concat(a.ab_firstname," ",a.ab_lastname)
  //       as cname
  // Note: I can always add more fields later.
  $searchobj = array(array("c.company_name", "Customer"),
                     array("e.account_lid" , "Employee"),
                     array("j.summary"     , "Job Name")
                     );
  $filterobj =
      $GLOBALS['phpgw']->nextmatchs->filterobj("work_catagories",
                                    "work_catagory_id",
                                    "catagory_desc");
	
  echo "<center>" . lang("Hours");

  if (! $start){
     $start = 0;
  }

  $limit =$GLOBALS['phpgw']->nextmatchs->sql_limit($start);

  if ($order){
     $ordermethod = "order by $order $sort";
  }else{
     $ordermethod = "order by jd.work_date asc";
  }

  //
  // Apply the filter:
  //

  if ($filter == "" || $filter == "none") {
     $a_filtermethod = "";
     $w_filtermethod = "";
  } elseif ($filter == "billable") {
     $a_filtermethod = " AND detail_billable ='Y'";
     $w_filtermethod = " WHERE detail_billable ='Y'";
  }
  
  //
  // Query to determine the number of records found.
  //
  
  if ($query) { // assume that $qfield is also set to the field name to query

   $GLOBALS['phpgw']->db->query("SELECT count(*) "
     . "FROM phpgw_ttrack_jobs as j, phpgw_ttrack_job_details as jd "
     . "left join phpgw_ttrack_wk_cat as wc on wc.work_catagory_id = jd.work_catagory_id  "
     . "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
     . "left join phpgw_addressbook as a on j.contact_id = a.ab_id "
     . "left join phpgw_accounts as e on jd.account_id = e.account_id "
     . "WHERE $qfield like '%$query%' "
     . "AND jd.job_id = j.job_id  $a_filtermethod ");
     //. "$ordermethod limit $limit");

   $GLOBALS['phpgw']->db->next_record();

   if ($GLOBALS['phpgw']->db->f(0) == 1) {
        echo "<br>" . lang("your search returned 1 match");
   } else {
        echo "<br>" . lang("your search returned %1 matchs",$GLOBALS['phpgw']->db->f(0));
   }
  } else { //no query
     $GLOBALS['phpgw']->db->query("select count(*) from phpgw_ttrack_job_details $w_filtermethod");
     $GLOBALS['phpgw']->db->next_record();
  }

  if ($GLOBALS['phpgw']->db->f(0) >= $GLOBALS['phpgw_info']["user"]["preferences"]["common"]["maxmatchs"]){
     $end = $start + $GLOBALS['phpgw_info']["user"]["preferences"]["common"]["maxmatchs"];
     if ($end > $GLOBALS['phpgw']->db->f(0)) $end = $GLOBALS['phpgw']->db->f(0);
     echo "<br>" . lang("showing %1 - %2 of %3",($start + 1),$end,$GLOBALS['phpgw']->db->f(0));
  } else {
     echo "<br>" . lang("showing %1",$GLOBALS['phpgw']->db->f(0)); 
  }

 $GLOBALS['phpgw']->nextmatchs->show("hourslist.php",$start,$GLOBALS['phpgw']->db->f(0),
                          "", "90%", $GLOBALS['phpgw_info']["theme"]["th_bg"],
                          $searchobj, $filterobj);
			
   //
   // Display the column headers
   //

   //
   // At some point it would be nice to be able to customize the fields shown on this report,
   // but currently the settings are hard coded here:
   //
      
   $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_company"] = 'False';
   $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_job_number"] = 'False';
   $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_job_revision"] = 'False';

   $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_account_lid"] = 'True';
   $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_work_date"] = 'True';


?>

  <table width=90% border=0 cellspacing=1 cellpadding=3>
    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["th_bg"]; ?>">
    <?php
    
       // Date (jd.work_date)
       echo '<td width=12%>';
       echo '<font size="-1" face="Arial, Helvetica, sans-serif">';
       echo $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"jd.work_date",$order,"hourslist.php",lang("Date"));
       echo '</font></td>';

       // Company Name (c.company_name)
       if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_company"] == "True" ) {
          echo '<td>';
          echo '<font size="-1" face="Arial, Helvetica, sans-serif">';
          echo $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"c.company_name",$order,"hourslist.php",lang("Customer"));
          echo '</font></td>';
       }
       // Job No. (j.job_number)
       if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_job_number"] == "True" ) {
           echo '<td>';
           echo '<font size="-1" face="Arial, Helvetica, sans-serif">';
           echo $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"j.job_number",$order,"hourslist.php",
                              lang("Job No."));
           echo '</font></td>';
       }
       // Rev (j.job_revision)
       if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_job_revision"] == "True" ) {
           echo '<td>';
           echo '<font size="-1" face="Arial, Helvetica, sans-serif">';
           echo $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"j.job_revision",$order,"hourslist.php",
                              lang("Rev"));
           echo '</font></td>';
       }
       // Summary Description (j.summary)
       //if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_email"] == "True" ) {
           echo '<td>';
           echo '<font size="-1" face="Arial, Helvetica, sans-serif">';
           echo $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"j.summary",$order,"hourslist.php",
                              lang("Job Name"));
           echo '</font></td>';
       //}
       // Category Description (wc.catagory_desc)
       //if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_email"] == "True" ) {
           echo '<td>';
           echo '<font size="-1" face="Arial, Helvetica, sans-serif">';
           echo $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"wc.catagory_desc",$order,"hourslist.php",
                              lang("Category"));
           echo '</font></td>';
       //}
       // Comments (jd.comments)
       //if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_email"] == "True" ) {
           echo '<td>';
           echo '<font size="-1" face="Arial, Helvetica, sans-serif">';
           echo $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"jd.comments)",$order,"hourslist.php",
                              lang("Comments"));
           echo '</font></td>';
       //}
       // Employee (e.account_lid)
       if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_account_lid"] == "True" ) {
           echo '<td>';
           echo '<font size="-1" face="Arial, Helvetica, sans-serif">';
           echo $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"e.account_lid)",$order,"hourslist.php",
                              lang("Employee"));
           echo '</font></td>';
       }
       // Hours Worked (j.quoted_hours) (i.e. sum(jd.numhours) as hours
       //if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_wphone"] == "True" ) {
           echo '<td width="10%">';
           echo '<font size="-1" face="Arial, Helvetica, sans-serif">';
           echo $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"hours",$order,"hourslist.php",
                              lang("Hours Worked"));
           echo '</font></td>';
       //}
    ?>

      <td></td>
      <td></td>
    </tr>
  </form>


<?php

  //
  // The actual query with the results for display
  //

  if ($query) {
    // A query by cname has to be handled with it's own query because
    // it requires special SQL code.

   $GLOBALS['phpgw']->db->query("SELECT jd.num_hours as hours,wc.catagory_desc,jd.comments, "
     . "j.job_id,j.job_number,j.job_revision,j.summary,c.company_name,"
     . "jd.detail_id,e.account_lid,jd.work_date "
     . "FROM phpgw_ttrack_jobs as j, phpgw_ttrack_job_details as jd "
     . "left join phpgw_ttrack_wk_cat as wc on wc.work_catagory_id = jd.work_catagory_id  "
     . "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
     . "left join phpgw_addressbook as a on j.contact_id = a.ab_id "
     . "left join phpgw_accounts as e on jd.account_id = e.account_id "
     . "WHERE $qfield like '%$query%' "
     . "AND jd.job_id = j.job_id  $a_filtermethod "
      . "$ordermethod limit $limit");

  } else {
   $GLOBALS['phpgw']->db->query("SELECT jd.num_hours as hours,wc.catagory_desc,jd.comments,"
     . "j.job_id,j.job_number,j.job_revision,j.summary,c.company_name,"
     . "jd.detail_id,e.account_lid,jd.work_date "
     . "FROM phpgw_ttrack_jobs as j, job_details as jd "
     . "left join phpgw_ttrack_wk_cat as wc on wc.work_catagory_id = jd.work_catagory_id  "
     . "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
     . "left join phpgw_addressbook as a on j.contact_id = a.ab_id "
     . "left join phpgw_accounts as e on jd.account_id = e.account_id "
     . "where jd.job_id = j.job_id $a_filtermethod "
     . "$ordermethod limit $limit");
  }


  //
  // Display the records found
  //

  while ($GLOBALS['phpgw']->db->next_record()) {
    $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);

    $detail_id = $GLOBALS['phpgw']->db->f("detail_id");
    $job_id = $GLOBALS['phpgw']->db->f("job_id");
    $job_number = $GLOBALS['phpgw']->db->f("job_number");
    $job_revision = $GLOBALS['phpgw']->db->f("job_revision");
    $work_date = $GLOBALS['phpgw']->db->f("work_date");
    $summary = $GLOBALS['phpgw']->db->f("summary");
    $company_name = $GLOBALS['phpgw']->db->f("company_name");
    $account_lid = $GLOBALS['phpgw']->db->f("account_lid");
    $catagory_desc = $GLOBALS['phpgw']->db->f("catagory_desc");
    $comments = $GLOBALS['phpgw']->db->f("comments");
    $hours = $GLOBALS['phpgw']->db->f("hours");
    
    if($hours == "") $hours = "0.00";
    

    ?>
    <?php
     echo '<tr bgcolor="#'.$tr_color.'";>';

     echo '<td valign=top>';
     echo '<font face=Arial, Helvetica, sans-serif size=2>&nbsp;';
     echo $work_date;
     echo '</font></td>';
     if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_company"] == 'True' ) {
         echo '<td valign=top>';
         echo '<font face=Arial, Helvetica, sans-serif size=2>&nbsp;';
         echo $company_name;
         echo '</font></td>';
     };
     if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_job_number"] == 'True' ) {
         echo '<td valign=top>';
         echo '<font face=Arial, Helvetica, sans-serif size=2>&nbsp;';
         echo $job_number;
         echo '</font></td>';
     };
     if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_job_revision"] == 'True' ) {
         echo '<td valign=top>';
         echo '<font face=Arial, Helvetica, sans-serif size=2>&nbsp;';
         echo $job_revision;
         echo '</font></td>';
     };
     //if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_email"] == 'True' ) {
         echo '<td valign=top>';
         echo '<font face=Arial, Helvetica, sans-serif size=2>&nbsp;';
         echo $summary;
         echo '</font></td>';
     //};
     //if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_email"] == 'True' ) {
         echo '<td valign=top>';
         echo '<font face=Arial, Helvetica, sans-serif size=2>&nbsp;';
         echo $catagory_desc;
         echo '</font></td>';
     //};
     //if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_email"] == 'True' ) {
         echo '<td valign=top>';
         echo '<font face=Arial, Helvetica, sans-serif size=2>&nbsp;';
         echo $comments;
         echo '</font></td>';
     //};
     if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_account_lid"] == 'True' ) {
         echo '<td valign=top>';
         echo '<font face=Arial, Helvetica, sans-serif size=2>&nbsp;';
         echo $account_lid;
         echo '</font></td>';
     };
     //if ( $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack_hoursview_view_wphone"] == 'True' ) {
         echo '<td valign=top>';
         echo '<font face=Arial, Helvetica, sans-serif size=2>&nbsp;';
         echo $hours; 
         echo '</font></td>';
     //};

     ?>

       <td valign=top>
        <font face=Arial, Helvetica, sans-serif size=2>
         <a href="<?php echo $GLOBALS['phpgw']->link("/timetrack/editdetail.php","detailid=$detail_id");
         ?>"> <?php echo lang("Edit"); ?> </a>
        </font>
       </td>
       <td valign=top>
	<font face=Arial, Helvetica, sans-serif size=2>
          <a href="<?php echo $GLOBALS['phpgw']->link("/timetrack/deletedetail.php","jd_id=$detail_id") . "\">" . lang("Delete"); ?></a>
        </font>
       </td>
      </tr>
     <?php
  }

?>
  </table>
</center>

<?php
  $GLOBALS['phpgw']->common->phpgw_footer();
?>
