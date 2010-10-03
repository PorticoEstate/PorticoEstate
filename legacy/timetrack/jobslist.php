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
  // Update complete for phpgroupware 0.9.10 - 4/17/2001 (api calls for accounts and contacts)
  // Note: I have removed ability to do queries based on Contacts at this time

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";

  include("../header.inc.php");

  $t = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('timetrack'));
  $t->set_file("body", "jobslist.tpl" );
  $t->set_block("body", "header", "header");
  $t->set_block("body", "row", "rows");
  $t->set_block("body", "footer", "footer");
 
  $t->set_var("rows", ""); // Default rows value in case there are no customers.

  // Setup $searchobj array for setting up a listbox on the search form
  // so we can narrow down what field to query on.
  // Note: cname is a sql alias for concat(a.ab_firstname," ",a.ab_lastname)
  //       as cname
  // Note: I can always add more fields later.
  $searchobj = array(array("c.company_name", "Customer"),
                     //array("cname"         , "Contact"),	
                     array("e.account_lid" , "Assigned To"),
                     array("j.description" , "Description"),
                     array("j.summary"     , "Summary")
                     );
	
  $filterobj = $GLOBALS['phpgw']->nextmatchs->filterobj("phpgw_ttrack_job_status", "status_id", "status_name");

  $t->set_var("lang_title", lang("Jobs List"));

  if (! $start){
     $start = 0;
  }

  if ($order){
     $ordermethod = "order by $order $sort";
  }else{
     $ordermethod = "order by c.company_name,j.job_number,j.job_revision asc";
  }

  if (($filter == "") || ($filter == 'none')) {
     $a_filtermethod = "";
     $w_filtermethod = "";
  } else {
     $a_filtermethod = " AND status_id=$filter";
     $w_filtermethod = " WHERE status_id=$filter";
  }

  if ($query) { 
   // assume that $qfield is also set to the field name to query
   // What really sucks about this new phpgwapi limitations is that
   // now I am going to have to come up with multiple queries to match
   // anything under contacts or accounts, and then handle matching
   // multiple results from those into a jobs query. Most likely a
   // switch statement should be used to handle each separate query type.
   switch($qfield) {
	case "c.company_name":
       $GLOBALS['phpgw']->db->query("SELECT count(*) "
        . "from phpgw_ttrack_jobs as j "
        . "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
        . "WHERE $qfield LIKE '%$query%' "
        . "$ordermethod",__LINE__,__FILE__);
	 $GLOBALS['phpgw']->db->next_record();
	 $matches = $GLOBALS['phpgw']->db->f(0);
	 break;
	case "e.account_lid": // for now, this will have to be an exact match on login name
	 $uid = $GLOBALS['phpgw']->accounts->name2id($query);
	 if ($uid > 0){
         $GLOBALS['phpgw']->db->query("SELECT count(*) "
           . "from phpgw_ttrack_jobs as j "
           . "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
           . "WHERE j.account_id=$uid "
           . "$ordermethod",__LINE__,__FILE__);
	   $GLOBALS['phpgw']->db->next_record();
	   $matches = $GLOBALS['phpgw']->db->f(0);
	 } else {
	   $matches = 0;
	 }
	 break;
	case "j.description":
	case "j.summary":
       $GLOBALS['phpgw']->db->query("SELECT count(*) "
        . "from phpgw_ttrack_jobs as j "
        . "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
        . "WHERE $qfield LIKE '%$query%' "
        . "$ordermethod",__LINE__,__FILE__);
	 $GLOBALS['phpgw']->db->next_record();
	 $matches = $GLOBALS['phpgw']->db->f(0);
	 break;
   }

	   /*if($qfield == "cname") {
 	    // Take queries based on Contact Name out of the picture for now!
	     $GLOBALS['phpgw']->db->query("SELECT count(*) "
	       . "from phpgw_ttrack_jobs as j "
	       . "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
	       . "left join phpgw_addressbook as a on j.contact_id = a.id "
	       . "left join phpgw_accounts as e on j.account_id = e.account_id "
	       . "WHERE concat(a.ab_firstname,\" \",a.ab_lastname) LIKE '%$query%' "
	       . "$ordermethod",__LINE__,__FILE__);
	   } else {
	     $GLOBALS['phpgw']->db->query("SELECT count(*) "
	       . "from phpgw_ttrack_jobs as j "
	       . "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
	       . "left join phpgw_addressbook as a on j.contact_id = a.id "
	       . "left join phpgw_accounts as e on j.account_id = e.account_id "
	       . "WHERE $qfield LIKE '%$query%' "
	       . "$ordermethod",__LINE__,__FILE__);
	   }
	  $GLOBALS['phpgw']->db->next_record();*/

   //if ($matches == 1) {
   //     $t->set_var("lang_matches", lang("your search returned 1 match"));
   //} else {
   //  $t->set_var("lang_matches", lang("your search returned %1 matchs",$matches));
   //}
  } else { //no query
     $GLOBALS['phpgw']->db->query("select count(*) from phpgw_ttrack_jobs $w_filtermethod");
     $GLOBALS['phpgw']->db->next_record();
     $matches = $GLOBALS['phpgw']->db->f(0);
  }
  $company_sortorder = "c.company_name";

  if ($matches >= $GLOBALS['phpgw_info']["user"]["preferences"]["common"]["maxmatchs"]){
     $end = $start + $GLOBALS['phpgw_info']["user"]["preferences"]["common"]["maxmatchs"];
     if ($end > $matches) $end = $matches;
     $t->set_var("lang_showing", lang("showing %1 - %2 of %3",($start + 1),$end,$matches));
  } else {
     $t->set_var("lang_showing", lang("showing %1",$matches));
  }

 $t->set_var("next_matchs", $GLOBALS['phpgw']->nextmatchs->show_tpl("/timetrack/jobslist.php",
	$start,$matches,"", "90%", $GLOBALS['phpgw_info']["theme"]["th_bg"],$searchobj, $filterobj));

 $t->set_var("th_bg", $GLOBALS['phpgw_info']["theme"]["th_bg"]);

 // Company Name (c.company_name)
 $t->set_var("lang_customer", $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,$company_sortorder,$order,
	"/timetrack/jobslist.php",lang("Customer")));
 $t->set_var("lang_customer", lang("Customer"));
 // Job No. (j.job_number)
 $t->set_var("lang_job_num", $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"j.job_number",$order,
	"/timetrack/jobslist.php",lang("Job No.")));
 // Rev (j.job_revision)
 $t->set_var("lang_revision", $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"j.job_revision",$order,
	"/timetrack/jobslist.php",lang("Rev")));
 // Summary Description (j.summary)
 $t->set_var("lang_summary", $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"j.summary",$order,
	"/timetrack/jobslist.php",lang("Summary")));
 // Quoted Hours (j.quoted_hours)
 $t->set_var("lang_quoted", $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"j.quoted_hours",$order,
	"/timetrack/jobslist.php",lang("Quoted Hours")));
 // Hours Worked (j.quoted_hours) (i.e. sum(jd.numhours) as hours
 $t->set_var("lang_hours", $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"hours",$order,
	"/timetrack/jobslist.php",lang("Hours Worked")));

 $t->set_var("lang_view", lang("View"));
 $t->set_var("lang_edit", lang("Edit"));
 $t->set_var("lang_delete", lang("Delete"));

 $t->parse("header", "header");

  if ($query) {
    switch($qfield) {
    	case "cname": // No longer implemented for now, leave for possible re-implementation
     	  $GLOBALS['phpgw']->db->limit_query("SELECT sum(jd.num_hours) as hours,"
          . "j.job_id,j.job_number,j.job_revision,j.summary,j.quoted_hours,c.company_name,"
          . "concat(a.n_given,\" \",a.n_family) as cname,"
          . "e.account_lid from phpgw_ttrack_jobs as j "
          . "left join phpgw_ttrack_job_details as jd on jd.job_id = j.job_id "
          . "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
          . "left join phpgw_addressbook as a on j.contact_id = a.id "
          . "left join phpgw_accounts as e on j.account_id = e.account_id "
          . "WHERE concat(a.n_given,\" \",a.n_family) like '%$query%' "
          . "$a_filtermethod "
          . "GROUP BY j.job_id "
          . "$ordermethod", $start,__LINE__,__FILE__);
	  break;
	case "e.account_lid":
 	  $uid = $GLOBALS['phpgw']->accounts->name2id($query);
	  if ($uid > 0){
     	    $GLOBALS['phpgw']->db->limit_query("SELECT j.total_hours,"
             . "j.job_id,j.job_number,j.job_revision,j.summary,j.quoted_hours,c.company_name "
             . "from phpgw_ttrack_jobs as j "
             . "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
             . "WHERE j.account_id=$uid "
             . "$a_filtermethod "
             . "$ordermethod", $start,__LINE__,FILE__);
	  }
	  break;
	case "c.company_name":
	case "j.description":
	case "j.summary":
     	  $GLOBALS['phpgw']->db->limit_query("SELECT j.total_hours,"
          . "j.job_id,j.job_number,j.job_revision,j.summary,j.quoted_hours,c.company_name "
          . "from phpgw_ttrack_jobs as j "
          . "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
          . "WHERE $qfield like '%$query%' "
          . "$a_filtermethod "
          . "$ordermethod", $start,__LINE__,FILE__);
	  break;
    } // End of switch)$qfield)

  } else { // No query, just possible filters
   $GLOBALS['phpgw']->db->limit_query("SELECT j.total_hours,"
     . "j.job_id,j.job_number,j.job_revision,j.summary,j.quoted_hours,c.company_name "
     . "from phpgw_ttrack_jobs as j "
     . "left join phpgw_ttrack_customers as c on j.company_id = c.company_id "
     . "$w_filtermethod "
     . "$ordermethod", $start,__LINE__,__FILE__);
  }

  while ($GLOBALS['phpgw']->db->next_record()) {
    //$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
    $t->set_var("tr_color", $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color));

    $job_id = $GLOBALS['phpgw']->db->f("job_id");
    $job_number = $GLOBALS['phpgw']->db->f("job_number");
    $job_revision = $GLOBALS['phpgw']->db->f("job_revision");
    $summary = $GLOBALS['phpgw']->db->f("summary");
    $quoted_hours = $GLOBALS['phpgw']->db->f("quoted_hours");
    $company_name = $GLOBALS['phpgw']->db->f("company_name");
    $total_hours = $GLOBALS['phpgw']->db->f("total_hours");

    if($job_number == "") $job_number = "&nbsp;";
    if($job_revision == "") $job_revision  = "&nbsp;";
    if($summary == "") $summary     = "&nbsp;";
    if($quoted_hours == "") $quoted_hours   = "&nbsp;";
    if($company_name == "") $company_name    = "&nbsp;";
    if($total_hours == "") $total_hours = "0.00";

    $t->set_var("row_customer", $company_name);
    $t->set_var("row_job_num", $job_number);
    $t->set_var("row_revision", $job_revision);
    $t->set_var("row_summary", $summary);
    $t->set_var("row_quoted", $quoted_hours);
    if ($total_hours > 0)
     {
      $t->set_var("row_hours", "<a href=\"" . $GLOBALS['phpgw']->link("/timetrack/detail_report1.php",
		"job_id=$job_id&start=$start&order=$order&filter="
		. "$filter&query=$job_id&sort=$sort&qfield=j.job_id")."\">$total_hours</a>");
     } else {
      $t->set_var("row_hours", $total_hours);
     }
     $t->set_var("row_view", "<a href=\"" . $GLOBALS['phpgw']->link("/timetrack/viewjob.php",
	"jobid=$job_id&start=$start&order=$order&filter="
	. "$filter&query=$query&sort=$sort&qfield=$qfield") . "\">"
	. lang("View") . "</a>");
     $t->set_var("row_edit", "<a href=\"" . $GLOBALS['phpgw']->link("/timetrack/editjob.php",
	"jobid=$job_id&start=$start&order=$order&filter="
	. "$filter&query=$query&sort=$sort&qfield=$qfield") . "\">"
	. lang("Edit") . "</a>");
     $t->set_var("row_delete", "");
     $t->parse("rows","row",True);
  }

 $t->set_var("actionurl", $GLOBALS['phpgw']->link("/timetrack/newjob.php"));
 $t->set_var("h_sort", $sort);
 $t->set_var("h_order", $order);
 $t->set_var("h_query", $query);
 $t->set_var("h_start", $start);
 $t->set_var("h_filter", $filter);
 $t->set_var("h_qfield", $qfield);

 $t->set_var("lang_add", lang("Add"));

 $t->parse("footer","footer");

 $t->pparse("res","body");

 $GLOBALS['phpgw']->common->phpgw_footer();
?>
