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

  /* $Id: admin1.php 9782 2002-03-18 03:18:05Z rschader $ */

  // Update complete for phpgroupware 0.9.10 - 4/13/2001 (api calls for accounts and contacts)

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

  if($mode == "accept"){
    if($loc_id){
	  $GLOBALS['phpgw']->db->query("update phpgw_ttrack_locations set location_name='$loc_name' "
	    . "where location_id='$loc_id'");
	}
  }

  if($mode == "add"){
	$etext = $GLOBALS['phpgw']->lang("Edit this");
	// Attempt to possibly recover used id numbers
	$GLOBALS['phpgw']->db->query("SELECT MAX(location_id) from phpgw_ttrack_locations");
	$GLOBALS['phpgw']->db->next_record();
	$next_id = $GLOBALS['phpgw']->db->f(0) + 1;
	$GLOBALS['phpgw']->db->query("insert into phpgw_ttrack_locations (location_id,location_name) "
	  . "VALUES ($next_id,'$etext')");
    $mode="edit";
	$GLOBALS['phpgw']->db->query("select location_id from phpgw_ttrack_locations where location_name='$etext'");
	$GLOBALS['phpgw']->db->next_record();
	$loc_id = $GLOBALS['phpgw']->db->f("location_id");
  }
  
  if($mode == "delete"){
    if($loc_id){
      if(! $confirm){
        $GLOBALS['phpgw']->db->query("select location_name from phpgw_ttrack_locations where location_id='$loc_id'");
        $GLOBALS['phpgw']->db->next_record();
        echo '<center><table border=0 with=65%>';
        echo '<tr colspan=2><td align=center>';
        echo lang("Are you sure you want to delete this location") . "?";
        echo '<br>' . $GLOBALS['phpgw']->db->f("location_name");
        echo '<td></tr><tr><td>';
        echo '<a href="' . $GLOBALS['phpgw']->link("/timetrack/admin1.php") . '">' . lang("No") . '</a></td><td>';
        echo '<a href="' . $GLOBALS['phpgw']->link("/timetrack/admin1.php","loc_id=$loc_id&mode=delete&confirm=true")
           . '">' . lang("Yes") . "</a>";
        echo '</td></tr></table></center>';
        $GLOBALS['phpgw']->common->phpgw_footer();
        $GLOBALS['phpgw']->common->phpgw_exit();
      } else { //we have loc_id and confirm
        $GLOBALS['phpgw']->db->query("delete from phpgw_ttrack_locations where location_id='$loc_id'");
      }
    }
  }

  echo "<p><center><h3>" . lang("Locations Table") . "</h3><table border=0 width=65%>"
     . "<tr bgcolor=" . $GLOBALS['phpgw_info']["theme"]["th_bg"] . "><th>" . lang("Location ID") . "</th><th>"
     . lang("Location") . "</th><th> " . lang("Edit") . " </th> <th> "
     . lang("Delete") . " </th></tr>";

  $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_locations");

  while ($GLOBALS['phpgw']->db->next_record()) {
    $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);

    $location_id  = $GLOBALS['phpgw']->db->f("location_id");
    $location_name = $GLOBALS['phpgw']->db->f("location_name");

	if(($mode == "edit") && ($loc_id == $location_id)){
	  $location_name = '<form method=POST action="' 
	     . $GLOBALS['phpgw']->link("/timetrack/admin1.php","loc_id=" . $location_id . "&mode=accept")
	     . '"><input name="loc_name" value="' . $location_name . '">'
		 . '</form>';
	}

    echo "<tr valign=\"center\" bgcolor=$tr_color><td>$location_id</td><td>";
	echo $location_name
       . "</td><td width=5%><a href=\"" . $GLOBALS['phpgw']->link("/timetrack/admin1.php",
         "loc_id=" . $location_id . "&mode=edit") . "\"> " . lang("Edit") . " </a></td>";

    echo  "<td width=8%><a href=\"" . $GLOBALS['phpgw']->link("/timetrack/admin1.php",
          "loc_id=" . $location_id . "&mode=delete") . "\"> " . lang("Delete") . " </a> </td></tr>";
  }
  echo '<form method=POST action="' . $GLOBALS['phpgw']->link("/timetrack/admin1.php",
  		"mode=add") . '">'
     . "<tr><td colspan=5><input type=\"submit\" value=\"" . lang("Add")
     . "\"></td></tr></form></table></center>";

  $GLOBALS['phpgw']->common->phpgw_footer();
