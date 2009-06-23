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

  // Update complete for phpgroupware 0.9.10 - 4/13/2001 (api calls for accounts and contacts)

  // For editing Status ID table, entries are
  // table name: job_status
  // fields: status_id, status_name

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

  if($mode == "accept"){
    if($stat_id){
	  $GLOBALS['phpgw']->db->query("update phpgw_ttrack_job_status set status_name='$stat_name' "
	    . "where status_id='$stat_id'");
	}
  }

  if($mode == "add"){
	$etext = $GLOBALS['phpgw']->lang("Edit This");
	// Attempt to possibly recover used id numbers
	$GLOBALS['phpgw']->db->query("SELECT MAX(status_id) from phpgw_ttrack_job_status");
	$GLOBALS['phpgw']->db->next_record();
	$next_id = $GLOBALS['phpgw']->db->f(0) + 1;
	$GLOBALS['phpgw']->db->query("insert into phpgw_ttrack_job_status (status_id,status_name) "
	  . "VALUES ($next_id,'$etext')");
    $mode="edit";
	$GLOBALS['phpgw']->db->query("select status_id from phpgw_ttrack_job_status where status_name='$etext'");
	$GLOBALS['phpgw']->db->next_record();
	$stat_id = $GLOBALS['phpgw']->db->f("status_id");
  }
  
  if($mode == "delete"){
    if($stat_id){
      if(! $confirm){
        $GLOBALS['phpgw']->db->query("select status_name from phpgw_ttrack_job_status where status_id='$stat_id'");
        $GLOBALS['phpgw']->db->next_record();
        echo '<center><table border=0 with=65%>';
        echo '<tr colspan=2><td align=center>';
        echo lang("Are you sure you want to delete this Status Code") . "?";
        echo '<br>' . $GLOBALS['phpgw']->db->f("status_name");
        echo '<td></tr><tr><td>';
        echo '<a href="' . $GLOBALS['phpgw']->link("/timetrack/admin2.php") . '">' . lang("No") . '</a></td><td>';
        echo '<a href="' . $GLOBALS['phpgw']->link("/timetrack/admin2.php","stat_id=$stat_id&mode=delete&confirm=true")
           . '">' . lang("Yes") . "</a>";
        echo '</td></tr></table></center>';
        $GLOBALS['phpgw']->common->phpgw_footer();
        $GLOBALS['phpgw']->common->phpgw_exit();
      } else { //we have stat_id and confirm
        $GLOBALS['phpgw']->db->query("delete from phpgw_ttrack_job_status where status_id='$stat_id'");
      }
    }
  }

  echo "<p><center><h3>" . lang("Status ID Table") . "</h3><table border=0 width=65%>"
     . "<tr bgcolor=" . $GLOBALS['phpgw_info']["theme"]["th_bg"] . "><th>" . lang("Status ID") . "</th><th>"
     . lang("Status Name") . "</th><th> " . lang("Edit") . " </th> <th> "
     . lang("Delete") . " </th></tr>";

  $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_job_status");

  while ($GLOBALS['phpgw']->db->next_record()) {
    $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);

    $status_id  = $GLOBALS['phpgw']->db->f("status_id");
    $status_name = $GLOBALS['phpgw']->db->f("status_name");

	if(($mode == "edit") && ($stat_id == $status_id)){
	  $status_name = '<form method=POST action="' 
	     . $GLOBALS['phpgw']->link("/timetrack/admin2.php","stat_id=" . $status_id . "&mode=accept")
	     . '"><input name="stat_name" value="' . $status_name . '">'
		 . '</form>';
	}

    echo "<tr valign=\"center\" bgcolor=$tr_color><td>$status_id</td><td>";
	echo $status_name
       . "</td><td width=5%><a href=\"" . $GLOBALS['phpgw']->link("/timetrack/admin2.php",
         "stat_id=" . $status_id . "&mode=edit") . "\"> " . lang("Edit") . " </a></td>";

    echo  "<td width=8%><a href=\"" . $GLOBALS['phpgw']->link("/timetrack/admin2.php",
          "stat_id=" . $status_id . "&mode=delete") . "\"> " . lang("Delete") . " </a> </td></tr>";
  }
  echo '<form method=POST action="' . $GLOBALS['phpgw']->link("/timetrack/admin2.php",
  		"mode=add") . '">'
     . "<tr><td colspan=5><input type=\"submit\" value=\"" . lang("Add")
     . "\"></td></tr></form></table></center>";

  $GLOBALS['phpgw']->common->phpgw_footer();
