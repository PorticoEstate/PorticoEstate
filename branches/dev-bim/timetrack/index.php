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

$GLOBALS['phpgw_info'] = array();
$GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
$GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";

include("../header.inc.php");

// Add simple test here to check for complete installation of timetrack
$n_group = "TTrack_Managers";
if (!$GLOBALS['phpgw']->accounts->exists($n_group))
{
	echo '<center><br><br><b>Warning: Timetrack has detected that required post installation<br>'
			. "steps have not been performed yet. Please <a href=\""
			. $GLOBALS['phpgw']->link("/timetrack/admin5.php") . "\">"
			. lang("Click Here") . "</a> to complete installation.";
	$GLOBALS['phpgw']->common->phpgw_footer();
	$GLOBALS['phpgw']->common->phpgw_exit();

}

// Add test here to check version of phpgwtimetrack
//$GLOBALS['phpgw']->db->query("SELECT app_version from phpgw_applications where app_name='timetrack'");
//$GLOBALS['phpgw']->db->next_record();
//if($GLOBALS['phpgw']->db->f(0) == "0.1") {
// We need to upgrade.
//	echo "<center>Your Timetrack tables need to be updated for this version.<br>";
//	echo "If you are a member of the 'Admin' group, you may <a href=\"" 
//		. $GLOBALS['phpgw']->link("/timetrack/setup/index.php") . "\">"
//		. lang("Click Here") . "</a> to upgrade";
//	$GLOBALS['phpgw']->common->phpgw_footer();
//	$GLOBALS['phpgw']->common->phpgw_exit();
//}

if ($submit) {
	//echo "Status is: " . $status;
	//echo " UID is: " . $uid;
	$GLOBALS['phpgw']->db->query("UPDATE phpgw_ttrack_emplyprof SET inorout='$status' "
		. "WHERE id=$uid");
}
	?>
	 <center><h3><?php echo lang("Employee In/Out Board"); ?></h3></center>
	 <? /* Here I have to do my first query, to check the user's checkin status */
	 $uid = $GLOBALS['phpgw_info']["user"]["account_id"];
	 /* Now use the uid to reference the employee_stats.inorout field */
	 $GLOBALS['phpgw']->db->query("select inorout from phpgw_ttrack_emplyprof"
	  . " where id= " . $uid);
	 $GLOBALS['phpgw']->db->next_record();
	 $status = $GLOBALS['phpgw']->db->f("inorout");
	 $prefix = lang("you are currently checked");
	 switch ($status) {
		case "I":
		 $stat_word = lang("In");
		 $action = lang("Check Out");
		 $suffix = lang("when you leave work");
		 $togval = "O";
		 break;
		default:
		 $stat_word = lang("Out");
		 $action = lang("Check In");
		 $suffix = lang("while you are at work");
		 $togval = "I";
		 break;
	 }
	 // This part will display basically a form with only a submit
	 // button with the appropriate text to set in or out status.
	 ?>
	 <form method="POST" action="<?php echo $GLOBALS['phpgw']->link("/timetrack/index.php");?>">
	  <input type=hidden name=uid value="<?php echo $uid; ?>">
	  <input type=hidden name=status value="<?php echo $togval; ?>">
	  <center><h4>
	  <?php echo $GLOBALS['phpgw_info']["user"]["firstname"] . ", " . $prefix . " " . $stat_word
		. ', '.lang('Please').' '; ?>
	  <input type="submit" name="submit" value="<?php echo $action;?>">
	  <?php echo " " . $suffix; ?>
		</center></h4>
	 </form>

<?php
 $main_locations=2; // All the rest of locations will eventually be treated as "Other"

 // For this to work right, the first thing I need to do is fill an array with the location names.
 $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_locations");
  while ($GLOBALS['phpgw']->db->next_record()) {
	$loc_id = $GLOBALS['phpgw']->db->f("location_id");
	$n_location[$loc_id] = $GLOBALS['phpgw']->db->f("location_name");
   }
 $total_locations = $loc_id; // tested, works

 $locations_per_row = 3; //should probably be in a config or preference file later.
 // Inner table width percentages:
 $itable_width = floor(100 / $locations_per_row);
 // Following needed to handle odd number of locations:
 $num_rows = ceil($total_locations / $locations_per_row);
 ?>
 <table border="0" width="100%">
  <?php 
    for ($row = 0; $row < $num_rows; $row++)
	 {
	  echo '<tr valign="top">';
	  for ($loc = 1; $loc <= $locations_per_row; $loc++)
		{
		 $loc_id = $loc + ($row * $locations_per_row);
	     echo '<td width="' . $itable_width . '%">';
	     echo '<table border="0" valign="top" width="100%">';
	     echo '<tr><th colspan="3" bgcolor="' . $GLOBALS['phpgw_info']["theme"]["th_bg"] 
		. '" align="center">'
	  	   . $n_location[$loc_id] . '</th></tr>';
		 // more td's for this location table go here
		 // sql query for selecting all users from one location, need to access both
		 // the accounts and profiles tables using a join, order by, and ?
		 $loc_sql = "select id,lid,inorout "
		 	      . "from phpgw_ttrack_emplyprof "
				  . "where location_id = " . $loc_id
				  . " order by lid";
		 // Now do a while loop on the resultset and print a 
		 // row (name, inorout) for each result
		 $GLOBALS['phpgw']->db->query($loc_sql);
		 $t = 0;
		 while ($GLOBALS['phpgw']->db->next_record()) {
		   $id[$t] = $GLOBALS['phpgw']->db->f("id");
		   $in_out[$t] = $GLOBALS['phpgw']->db->f("inorout");
		   switch($in_out[$t]){
			case "I":
			  $inout_word[$t] = lang("In");
			  break;
			default:
			  $inout_word[$t] = lang("Out");
			  break;
		   }
		   $t++;
		 }
		 for($i=0; $i<$t; $i++) {
		   $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);

		   echo '<tr>';
		   echo ' <td colspan="2" bgcolor="' . $tr_color . '">' .
			get_fullname($id[$i]) . '</td>'; 
		   echo ' <td bgcolor="' . $tr_color . '" align="center">' 
			. $inout_word[$i] . '</td>';
		 }
		 // End it with end td and end table when done:
		 echo '</table></td>';
		}
	  echo '</tr>';
	 }
    echo '</table>';
  ?>

<?php
$GLOBALS['phpgw']->common->phpgw_footer();
?>
