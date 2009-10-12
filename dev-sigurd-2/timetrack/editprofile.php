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

  // Update complete for phpgroupware 0.9.10 - 4/15/2001 (api calls for accounts and contacts)
  // No accounts or contacts api calls needed.

if($submit) {
  $GLOBALS['phpgw_info']["flags"] = array("noheader" => True, "nonavbar" => True);
}

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");
  if (! $id)
     Header("Location: " . $GLOBALS['phpgw']->link("/timetrack/profiles.php"));

  if ($submit) {
	// Leave out any error checking for now, because most fields are not mandatory
	// at this point. I can add error checking later if I run into problems.
	$loc = implode(",",$n_location);
	//$table_locks=array('employee_profiles');
	//$GLOBALS['phpgw']->db->lock($table_locks);
	if($n_hire_date == "") {
	  $p_sql = "UPDATE phpgw_ttrack_emplyprof SET title='" . addslashes($n_title)
		. "',phone_number='" . addslashes($n_phone_number)
		. "',comments='" . addslashes($n_comments)
		. "',mobilephn='" . addslashes($n_mobilephn)
		. "',pager='" . addslashes($n_pager)
		. "',yearly_vacation_hours='$n_yvac_hours'"
		. ",vacation_hours_used_todate='$n_vhours_utd'"
		. ",location_id='$loc'"
		. " WHERE id='$id'";
	} else {
	  $p_sql = "UPDATE phpgw_ttrack_emplyprof SET title='" . addslashes($n_title)
		. "',phone_number='" . addslashes($n_phone_number)
		. "',comments='" . addslashes($n_comments)
		. "',mobilephn='" . addslashes($n_mobilephn)
		. "',pager='" . addslashes($n_pager)
		. "',hire_date='" . addslashes($n_hire_date)
		. "',yearly_vacation_hours='$n_yvac_hours'"
		. ",vacation_hours_used_todate='$n_vhours_utd'"
		. ",location_id='$loc'"
		. " WHERE id='$id'";
	 // test echo the sql:
	}

	//echo "Profile SQL statement is: <br>" . $p_sql . "<br>";
	// Do the sql
	$GLOBALS['phpgw']->db->query($p_sql);
	// Unlock the tables
	//$GLOBALS['phpgw']->db->unlock();

        Header("Location: " . $GLOBALS['phpgw']->link("/timetrack/profiles.php"));
        exit;
  }		// if $submit

  $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_emplyprof where id='$id'");
  $GLOBALS['phpgw']->db->next_record();
  // Get all the required fields so I can query the employee_stats table too
  $n_lid = $GLOBALS['phpgw']->db->f("lid");
  $n_title = $GLOBALS['phpgw']->db->f("title");
  $n_phone_number = $GLOBALS['phpgw']->db->f("phone_number");
  $n_comments = stripslashes($GLOBALS['phpgw']->db->f("comments"));
  $n_mobilephn = $GLOBALS['phpgw']->db->f("mobilephn");
  $n_pager = $GLOBALS['phpgw']->db->f("pager");
  // add support for picture later
  // Note: no picture support needed, just drop pic in hr/images dir named for login
  $n_hire_date = $GLOBALS['phpgw']->db->f("hire_date");
  $n_yvac_hours = $GLOBALS['phpgw']->db->f("yearly_vacation_hours");
  $n_vhours_utd = $GLOBALS['phpgw']->db->f("vacation_hours_used_todate");
  $n_location_id = $GLOBALS['phpgw']->db->f("location_id");
  $n_inout = $GLOBALS['phpgw']->db->f("inorout");
  ?>
  <h2><center>
     <?php echo lang("Edit Profile");?>
     </center></h2>
     <form method="POST" action="<?php echo $GLOBALS['phpgw']->link("/timetrack/editprofile.php");?>">
      <input type="hidden" name="id" value="<?php echo $id; ?>">
       <?php
         if ($error) {
            echo "<center>" . lang("Error") . ":$error</center>";
         }
       ?>
      <center>
       <table border=0 width=65%>
        <tr> 
         <td><?php echo lang("LoginID"); ?></td>
	 <td><?php echo $n_lid; ?></td>
        </tr>
        <tr>
         <td><?php echo lang("Title"); ?></td>
         <td><input name="n_title" value="<?php echo $n_title; ?>"></td>
        </tr>
        <tr>
         <td><?php echo lang("Work Phone"); ?></td>
         <td><input name="n_phone_number" value="<?php echo $n_phone_number; ?>"></td>
        </tr>
	<tr>
         <td><?php echo lang("Mobile Phone"); ?></td>
         <td><input name="n_mobilephn" value="<?php echo $n_mobilephn; ?>"></td>
        </tr>
	<tr>
         <td><?php echo lang("Pager"); ?></td>
         <td><input name="n_pager" value="<?php echo $n_pager; ?>"></td>
        </tr>
	<tr>
         <td><?php echo lang("Date Hired"); ?></td>
         <td><input name="n_hire_date" value="<?php echo $n_hire_date; ?>"></td>
        </tr>
	<tr>
         <td><?php echo lang("vacation hours per Year"); ?></td>
         <td><input name="n_yvac_hours" value="<?php echo $n_yvac_hours; ?>"></td>
        </tr>
	<tr>
         <td><?php echo lang("Vacation hours used"); ?></td>
         <td><input name="n_vhours_utd" value="<?php echo $n_vhours_utd; ?>"></td>
        </tr>
	<tr>
	 <td><?php echo lang("Location"); ?></td>
	 <td><select name="n_location[]"><?php
                   $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_locations");
                   while ($GLOBALS['phpgw']->db->next_record()) {
                    $locid = $GLOBALS['phpgw']->db->f("location_id");
                    echo "<option value=\"" . $locid . "\"";
                    if ( $locid == $n_location_id) {
                     echo " selected";
                    }
                    echo ">" . $GLOBALS['phpgw']->db->f("location_name") . "</option>";
                   }
                ?>
           </select></td>
	</tr>
	<tr>
         <td><?php echo lang("Comments"); ?></td>
         <td><textarea name="n_comments" cols="30" rows="4"
	      wrap="virtual"><?php echo $n_comments; ?></textarea></td>
        </tr>
	</table>
	<input type="submit" name="submit" value="<?php echo lang("submit"); ?>">
      </center>
     </form>
   <?php
   $GLOBALS['phpgw']->common->phpgw_footer();
?>
