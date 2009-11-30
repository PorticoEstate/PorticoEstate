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

 $GLOBALS['phpgw_info']["apps"]["timetrack"]["ismanager"] = 0;
 $GLOBALS['phpgw_info']["apps"]["timetrack"]["ispayroll"] = 0;

 $userGroups = $phpgw->accounts->membership();
 for ($i=0; $i<count($userGroups); $i++) 
 {
   $gname = $userGroups[$i]['account_name'];
   if ($gname == "TTrack_Managers")
   {
     $GLOBALS['phpgw_info']["apps"]["timetrack"]["ismanager"] = 1;
   }
   if ($gname == "TTrack_Payroll")
   {
     $GLOBALS['phpgw_info']["apps"]["timetrack"]["ispayroll"] = 1;
   }
 }

/*********** End of check groups code section ****************/

//$t = new Template($GLOBALS['phpgw_info']["server"]["app_tpl"]);
$t = new Template($phpgw->common->get_tpl_dir('timetrack'));
$t->set_file(array("app_header" => "header.tpl"));
$app_info = lang("Time Tracking");
$t->set_var("app_info", "<td bgcolor=\"" . $GLOBALS['phpgw_info']["theme"]["th_bg"]
	. "\" align=\"left\"><b>" . $app_info);

$t->set_var("link_inout","<a href=\"" . $phpgw->link("/timetrack/index.php") . "\">" 
	. lang("In/Out") ."</a> &nbsp;|");
$t->set_var("link_newjob","<a href=\"" . $phpgw->link("/timetrack/newjob.php") . "\">" 
	. lang("New Job") ."</a> &nbsp;|");
$t->set_var("link_jobstatus","<a href=\"" . $phpgw->link("/timetrack/jobslist.php") . "\">" 
	. lang("Job List") ."</a> &nbsp;|");
$t->set_var("link_entertime","<a href=\"" . $phpgw->link("/timetrack/addjobdetail.php") . "\">" 
	. lang("Enter Time") ."</a> &nbsp;|");
$t->set_var("link_timesheets","<a href=\"" . $phpgw->link("/timetrack/timesheets.php") . "\">" 
	. lang("Time Sheets") ."</a> &nbsp;|");
$t->set_var("link_custlist","<a href=\"" . $phpgw->link("/timetrack/customers.php") . "\">" 
	. lang("Customer List") ."</a> &nbsp;|");
if($GLOBALS['phpgw_info']["apps"]["timetrack"]["ismanager"]) {
  $t->set_var("link_editprofiles","<a href=\"" . $phpgw->link("/timetrack/profiles.php") . "\">" 
	. lang("Edit Profiles") ."</a> &nbsp;|");
} else {
  $t->set_var("link_editprofiles","");
}
$t->set_var("link_viewprofiles","<a href=\"" . $phpgw->link("/timetrack/hr-profiles.php") . "\">" 
	. lang("View Profiles") ."</a> &nbsp;|");
$t->set_var("link_yourprofile","<a href=\"" . $phpgw->link("/timetrack/changeprofile.php") . "\">" 
	. lang("Your Profile") ."</a>");

$t->pparse("out","app_header");

?>
