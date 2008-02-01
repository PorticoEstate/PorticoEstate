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

  /* $Id: admin3.php 12082 2003-03-20 22:04:59Z gugux $ */

  // Update complete for phpgroupware 0.9.10 - 4/13/2001 (api calls for accounts and contacts)

  // For editing Work Catagories table, entries are
  // table name: wk_cat
  // fields: work_catagory_id, catagory_desc

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

  if($mode == "accept"){
    if($cat_id){
	  $GLOBALS['phpgw']->db->query("update phpgw_ttrack_wk_cat set catagory_desc='$cat_name' "
	    . "where work_catagory_id='$cat_id'");
	}
  }

  if($mode == "add"){
	$etext = $GLOBALS['phpgw']->lang("Edit this");
	// Attempt to possibly recover used id numbers
	$GLOBALS['phpgw']->db->query("SELECT MAX(work_catagory_id) from phpgw_ttrack_wk_cat");
	$GLOBALS['phpgw']->db->next_record();
	$next_id = $GLOBALS['phpgw']->db->f(0) + 1;
	$GLOBALS['phpgw']->db->query("insert into phpgw_ttrack_wk_cat (work_catagory_id,catagory_desc) "
	  . "VALUES ($next_id,'$etext')");
    $mode="edit";
	$GLOBALS['phpgw']->db->query("select work_catagory_id from phpgw_ttrack_wk_cat where catagory_desc='$etext'");
	$GLOBALS['phpgw']->db->next_record();
	$cat_id = $GLOBALS['phpgw']->db->f("work_catagory_id");
  }
  
  if($mode == "delete"){
    if($cat_id){
      if(! $confirm){
        $GLOBALS['phpgw']->db->query("select catagory_desc from phpgw_ttrack_wk_cat where work_catagory_id='$cat_id'");
        $GLOBALS['phpgw']->db->next_record();
        echo '<center><table border=0 with=65%>';
        echo '<tr colspan=2><td align=center>';
        echo lang("Are you sure you want to delete this Work Category") . "?";
        echo '<br>' . $GLOBALS['phpgw']->db->f("catagory_desc");
        echo '<td></tr><tr><td>';
        echo '<a href="' . $GLOBALS['phpgw']->link("/timetrack/admin3.php") . '">' . lang("No") . '</a></td><td>';
        echo '<a href="' . $GLOBALS['phpgw']->link("/timetrack/admin3.php","cat_id=$cat_id&mode=delete&confirm=true")
           . '">' . lang("Yes") . "</a>";
        echo '</td></tr></table></center>';
        $GLOBALS['phpgw']->common->phpgw_footer();
        $GLOBALS['phpgw']->common->phpgw_exit();
      } else { //we have cat_id and confirm
        $GLOBALS['phpgw']->db->query("delete from phpgw_ttrack_wk_cat where work_catagory_id='$cat_id'");
      }
    }
  }

  echo "<p><center><h3>" . lang("Work Category Table") . "</h3><table border=0 width=65%>"
     . "<tr bgcolor=" . $GLOBALS['phpgw_info']["theme"]["th_bg"] . "><th>" . lang("Category ID") . "</th><th>"
     . lang("Category Name") . "</th><th> " . lang("Edit") . " </th> <th> "
     . lang("Delete") . " </th></tr>";

  $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_wk_cat");

  while ($GLOBALS['phpgw']->db->next_record()) {
    $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);

    $catagory_id  = $GLOBALS['phpgw']->db->f("work_catagory_id");
    $catagory_name = $GLOBALS['phpgw']->db->f("catagory_desc");

	if(($mode == "edit") && ($cat_id == $catagory_id)){
	  $catagory_name = '<form method=POST action="' 
	     . $GLOBALS['phpgw']->link("/timetrack/admin3.php","cat_id=" . $catagory_id . "&mode=accept")
	     . '">'
		 . '<input name="cat_name" value="' . $catagory_name . '">'
		 . '</form>';
	}

    echo "<tr valign=\"center\" bgcolor=$tr_color><td>$catagory_id</td><td>";
	echo $catagory_name
       . "</td><td width=5%><a href=\"" . $GLOBALS['phpgw']->link("/timetrack/admin3.php",
         "cat_id=" . $catagory_id . "&mode=edit") . "\"> " . lang("Edit") . " </a></td>";

    echo  "<td width=8%><a href=\"" . $GLOBALS['phpgw']->link("/timetrack/admin3.php",
          "cat_id=" . $catagory_id . "&mode=delete") . "\"> " . lang("Delete") . " </a> </td></tr>";
  }
  echo '<form method=POST action="' . $GLOBALS['phpgw']->link("/timetrack/admin3.php",
  		"mode=add") . '">'
     . "<tr><td colspan=5><input type=\"submit\" value=\"" . lang("Add")
     . "\"></td></tr></form></table></center>";

  $GLOBALS['phpgw']->common->phpgw_footer();
