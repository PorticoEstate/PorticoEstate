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

  Header("Cache-Control: no-cache");
  Header("Pragma: no-cache");
  //Header("Expires: Sat, Jan 01 2000 01:01:01 GMT");

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");
  if ($GLOBALS['phpgw_info']["user"]["permissions"]["anonymous"]) {
     Header("Location: " . $GLOBALS['phpgw']->link("/"));
     $GLOBALS['phpgw']->common->phpgw_exit();
  }

  if ($submit) {
     // I have changed this code so that pictures must be submitted to one of the
	 // administrators, who can then put it in the hr/images directory. Convention
	 // is that each picture will be in gif format and will be named for the userid.
	 // This makes view_image.php obsolete fro now, and saves us the hassle involved
	 // with blobs (at least for me). Have to still devide a method where the blank_pic
	 // would replace anyone's missing picture, or just copy the blank_pic.gif multiple
	 // times to all loginid names.

        $phone_number = addslashes($phone_number);
        $comments     = addslashes($comments);
        $title        = addslashes($title);
		$mobilephn    = addslashes($mobilephn);
		$pager        = addslashes($pager);
        // Shouldn't need addslashes for the following:
		// $hired, $vacationtime, $vac_hours_used, $location
		// We do also need to get and update the user's "con" #,
		// as I have changed this field so that it always is the
		// same as the accounts.con field.

        $GLOBALS['phpgw']->db->query("update phpgw_ttrack_emplyprof set title='$title',phone_number='$phone_number',"
		       . "mobilephn='$mobilephn',pager='$pager',comments='$comments'"
			   . "where lid='" . $GLOBALS['phpgw_info']["user"]["userid"] . "'");
     echo "<center>Your profile has been updated</center>";
  } // End of submit section

  $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_emplyprof where lid='" . $GLOBALS['phpgw_info']["user"]["userid"] . "'");
  $GLOBALS['phpgw']->db->next_record();
  // Note: We need to store all these values in reference vars so other queries won't
  // screw things up
  $n_lid = $GLOBALS['phpgw']->db->f("lid");
  $n_title = $GLOBALS['phpgw']->db->f("title");
  $n_phone_number = $GLOBALS['phpgw']->db->f("phone_number");
  $n_comments = stripslashes($GLOBALS['phpgw']->db->f("comments"));
  $n_mobilephn = $GLOBALS['phpgw']->db->f("mobilephn");
  $n_pager = $GLOBALS['phpgw']->db->f("pager");
  $n_hire_date = $GLOBALS['phpgw']->db->f("hire_date");
  $n_yvac_hours = $GLOBALS['phpgw']->db->f("yearly_vacation_hours");
  $n_vhours_utd = $GLOBALS['phpgw']->db->f("vacation_hours_used_todate");
  $n_location_id = $GLOBALS['phpgw']->db->f("location_id");
  $n_inout = $GLOBALS['phpgw']->db->f("inorout");
?>

  <form method="POST" action="<?php echo $GLOBALS['phpgw']->link("/timetrack/changeprofile.php");?>">

   <table border="0">
    <tr>
     <td colspan="2"><center><b><?php echo lang("Employee Profile for"); 
	echo ' '.get_fullname($GLOBALS['phpgw_info']["user"]["userid"]);?>
	</b></center></td>
     <td>&nbsp;</td>
    </tr>
    <tr>
     <td><?php echo lang('Title'); ?>:</td>
     <td><input name="title" value="<?php echo $n_title; ?>"></td>
     <td rowspan="2">
	  <img src=
	  "<?php 
	   if (file_exists(PHPGW_SERVER_ROOT . "/timetrack/images/" . $n_lid . ".gif"))
	    {
	     echo $GLOBALS['phpgw_info']["server"]["webserver_url"] . "/timetrack/images/" . $n_lid . ".gif"; 
		} else {
		 echo $GLOBALS['phpgw_info']["server"]["webserver_url"] . "/timetrack/images/blank_pic.jpg";
		}
	   ?>"
	   width="100" height="120" border="1">
     </td>
    </tr>

    <tr>
     <td><?php echo lang('Work Phone'); ?>:</td>
     <td><input name="phone_number" value="<?php echo $n_phone_number; ?>"></td>
    </tr>

	<tr>
	 <td><?php echo lang('Mobile Phone'); ?>:</td>
	 <td><input name="mobilephn" value="<?php echo $n_mobilephn; ?>"></td>
	</tr>

    <tr>
	 <td><?php echo lang('Pager'); ?>:</td>
     <td><input name="pager" value="<?php echo $n_pager; ?>"></td>
    </tr>

    <!-- Probably shouldn't give the users access to the next thre items, but I should
	     be able to disable it easy enough later -->
    <tr>
	 <td colspan="2"><hr>
	 <?php echo lang("The following info can only be updated by an Administrator");?>.
	 </td>
	</tr> 
    <tr>
     <td><?php echo lang("Hire Date") . ":";?></td>
     <td><?php echo $n_hire_date; ?></td>
    </tr>

    <tr>
     <td><?php echo lang("Yearly Vacation Hours");?>:</td>
     <td><?php echo $n_yvac_hours; ?></td>
    </tr>

    <tr>
     <td><?php echo lang("Vacation Hours Used");?>:</td>
     <td><?php echo $n_vhours_utd; ?></td>
    </tr>

	<tr>
	 <td><?php echo lang("Location");?>:</td>
	 <td><?php
	  $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_locations where location_id='$n_location_id'");
	  $GLOBALS['phpgw']->db->next_record();
      echo $GLOBALS['phpgw']->db->f("location_name");
	  ?>
     </td>
	</tr>
	<tr>
	 <td colspan="2"><hr></td>
	</tr>

    <tr>
     <td><?php echo lang("Comments");?>:</td>
     <td><textarea cols="60" name="comments" rows="4" wrap="virtual"><?php echo $n_comments; ?></textarea></td>
    </tr>

    <tr>
     <td><?php echo lang("Picture");?>:</td>
     <td><?php echo lang("Note") . ":<br>";
	 echo lang("To update your picture, please submit a gif");
	 echo "<br>" . lang("file to one of the managers via email") . ".";
	 echo "<br>" . lang("Pictures will be resized to 100x120");?>
    .</td>
    </tr>

    <tr>
     <td colspan="3" align="center"><input type="submit" name="submit" value="Submit">
    </td></tr>
   </table>

  </form>

<?php
  $GLOBALS['phpgw']->common->phpgw_footer();
