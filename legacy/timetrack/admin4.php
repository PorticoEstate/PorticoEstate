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
  /* This one will most likely be eliminated or changed in order
   * to use acl's instead, which might just be assignable on a user's
   * main account editing page.
   */
 
// This file is no longer used for phpgroupware 0.9.10. Will be deleted in good time.
  
 if($submit) {
  $GLOBALS['phpgw_info']["flags"] = array("noheader" => True, "nonavbar" => True);
 } 

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

if ($submit) {
 //echo "Not implemented just yet. Check back on 1/17/2001";

 if ($man_grp) {
  $GLOBALS['phpgw']->db->query("delete from phpgw_config where config_name='ttrack_mangrp'");
  $GLOBALS['phpgw']->db->query("insert into phpgw_config (config_name,config_value) VALUES ('ttrack_mangrp',$man_grp)");
 }

 if ($pay_grp) {
  $GLOBALS['phpgw']->db->query("delete from phpgw_config where config_name='ttrack_paygrp'");
  $GLOBALS['phpgw']->db->query("insert into phpgw_config (config_name,config_value) VALUES ('ttrack_paygrp',$pay_grp)");
 }

 $GLOBALS['phpgw']->redirect($GLOBALS['phpgw_info']["server"]["webserver_url"] . "/admin");

} else {

  echo "<center><h3>" . lang("Assign Group Permissions") . "</h3>";
  echo '<form method="POST" name="grpperms" action="' . $GLOBALS['phpgw']->link("/timetrack/admin4.php") . '">';

  echo '<table border=0>';
  echo '<tr><td>' . lang("Timetrack Managers Group") . '</td><td>';
  echo '<select name="man_grp" >';
  // Need to fill the options list with the names and values of groups here
  echo '<option value="">Select Group...</option>';
 // $GLOBALS['phpgw']->db->query("select group_id, group_name from groups order by group_id");
$GLOBALS['phpgw']->db->query("select account_id, account_lid from phpgw_accounts WHERE account_type='g'");
while ($GLOBALS['phpgw']->db->next_record()) {
    $gname = $GLOBALS['phpgw']->db->f("account_lid");
    $gid = $GLOBALS['phpgw']->db->f("account_id");
    echo '<option value="' . $gid . '"';
    if ($gid == $GLOBALS['phpgw_info']["apps"]["timetrack"]["manager_gid"]) {
      echo " selected";
    }
    echo ">" . $gname . "</option>";
  }
  echo '</select></td></tr>';

  echo '<tr><td>' . lang("Timetrack Payroll Group") . '</td><td>';
  echo '<select name="pay_grp" >';
  // Need to fill the options list with the names and values of groups here
  echo '<option value="">Select Group...</option>';
  

$GLOBALS['phpgw']->db->query("select account_id, account_lid from phpgw_accounts WHERE account_type='g'");
while ($GLOBALS['phpgw']->db->next_record()) {
    $pgname = $GLOBALS['phpgw']->db->f("account_lid");
    $pgid = $GLOBALS['phpgw']->db->f("account_id");
    echo '<option value="' . $pgid . '"';
    if ($pgid == $GLOBALS['phpgw_info']["apps"]["timetrack"]["payroll_gid"]) {
      echo " selected";
    }
    echo ">" . $pgname . "</option>";
  }
  echo '</select></td></tr>';

  echo '<tr><td colspan=2 align="center"><input type="submit" name="submit" value="'
	. lang("update") . '">';
  echo '&nbsp;&nbsp;<A HREF="' . $GLOBALS['phpgw']->link("/admin") . '">' . lang("Cancel") . '</a>';
  echo '</td></tr>';

  echo '</table></form></center>';

  $GLOBALS['phpgw']->common->phpgw_footer();
}
