<!-- confirmation template portion -->
<!-- Idea: use javascript dialog box instead? -->
<center><table border=0 with=65%>
<tr colspan=2>
<td align=center>
{lang_confirm_delete_location}
<br>{location_display}
</td>
</tr>
<tr>
  <td><a href="/timetrack/admin1.php">No</a></td>
  <td><a href="/timetrack/admin1.php?loc_id=$loc_id&mode=delete&confirm=true">Yes</a></td>
</tr></table></center>
<!-- End confirmation portion template -->


<p><center><h3>Locations Table</h3><table border=0 width=65%>
<tr bgcolor="$phpgw_info["theme"]["th_bg"]"><th>Location ID</th><th>
Location</th><th>Edit</th><th>Delete</th></tr>

  $phpgw->db->query("select * from phpgw_ttrack_locations");

  while ($phpgw->db->next_record()) {
    $tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);

    $location_id  = $phpgw->db->f("location_id");
    $location_name = $phpgw->db->f("location_name");

	if(($mode == "edit") && ($loc_id == $location_id)){
	  $location_name = '<form method=POST action="' 
	     . $phpgw->link("/timetrack/admin1.php","loc_id=" . $location_id . "&mode=accept")
	     . '"><input name="loc_name" value="' . $location_name . '">'
		 . '</form>';
	}

    echo "<tr valign=\"center\" bgcolor=$tr_color><td>$location_id</td><td>";
	echo $location_name
       . "</td><td width=5%><a href=\"" . $phpgw->link("/timetrack/admin1.php",
         "loc_id=" . $location_id . "&mode=edit") . "\"> " . lang("Edit") . " </a></td>";

    echo  "<td width=8%><a href=\"" . $phpgw->link("/timetrack/admin1.php",
          "loc_id=" . $location_id . "&mode=delete") . "\"> " . lang("Delete") . " </a> </td></tr>";
  }
  echo '<form method=POST action="' . $phpgw->link("/timetrack/admin1.php",
  		"mode=add") . '">'
     . "<tr><td colspan=5><input type=\"submit\" value=\"" . lang("Add")
     . "\"></td></tr></form></table></center>";

  $phpgw->common->phpgw_footer();
