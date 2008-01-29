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

  /* $Id: newprofile.php 9782 2002-03-18 03:18:05Z rschader $ */

  // Update complete for phpgroupware 0.9.10 - 4/17/2001 (api calls for accounts and contacts)
  // Note: The ideal way to handle all this would be to use a hook into the create account pages

if($submit) {
  $GLOBALS['phpgw_info']["flags"] = array("noheader" => True, "nonavbar" => True);
}

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

  if ($submit) {

     if (! $n_loginid)
        $error = "<br>" . lang("You must enter a loginid");

     //$GLOBALS['phpgw']->db->query("select account_id,account_lid from phpgw_accounts where account_lid='$n_loginid'");
     //$GLOBALS['phpgw']->db->next_record();
     //$uid = $GLOBALS['phpgw']->db->f("account_id");
     //$uname = $GLOBALS['phpgw']->db->f("account_lid");
     $uid = $GLOBALS['phpgw']->accounts->name2id($n_loginid);
     // Next lines are to check if the profile already exists
     $GLOBALS['phpgw']->db->query("select lid from phpgw_ttrack_emplyprof where lid='$n_loginid'");
     $GLOBALS['phpgw']->db->next_record();
     $uname2 = $GLOBALS['phpgw']->db->f("lid");
     if (($uid) && (! $uname2))
      {
	//$loc = implode(",",$n_location);
	$loc = $n_location;
	if (! $n_vhours) $n_vhours = 0;
	if (! $n_vhours_used) $n_vhours_used = 0;

	//$table_locks = array('employee_profiles');
      //  $GLOBALS['phpgw']->db->lock($table_locks);

	$sql = "INSERT INTO phpgw_ttrack_emplyprof (id,lid,title,phone_number,comments,mobilephn,pager,"
	    . "hire_date,yearly_vacation_hours,vacation_hours_used_todate,location_id,inorout)"
		. " values ($uid,'$n_loginid','" . addslashes($n_title) . "','"
		. addslashes($n_phone) . "','" . addslashes($n_comments) . "','"
		. addslashes($n_mobilephn) ."','" . addslashes($n_pager) . "','"
		. addslashes($n_hiredate) . "',$n_vhours,$n_vhours_used,$loc,'Out')";

	//echo "sql statement 1 is <br>" . $sql . "<br>";

	$GLOBALS['phpgw']->db->query($sql);
 
      //$GLOBALS['phpgw']->db->unlock();

	Header("Location: " . $GLOBALS['phpgw']->link("/timetrack/profiles.php"));

      } else {
	if (! $uid)
	 {
	  echo "The user account for " . $n_loginid . " has to be created before<br>";
	  echo "adding their profile entries<br>";
	 }
	if ($uname2)
	 {
	  echo "The user " . $uname2 . " already has an entry in the profiles table, please<br>";
	  echo "use the update method instead<br>";
	 }
      }
  }
else
  {
// END of submit form code, start of form code
// NOTE: We are going to assume and use the convention that a user's email address
// equals his loginid + "@domainname.com

     ?>
       <form method="POST" action="<?php echo $GLOBALS['phpgw']->link("/timetrack/newprofile.php");?>">
       <?php
         if ($error) {
            echo "<center>" . lang("Error") . ":$error</center>";
         }
       ?>
        <center>
         <table border=0 width=65%>
           <tr>
             <td><?php echo lang("LoginID"); ?></td>
             <td><input name="n_loginid" value="<?php echo $n_loginid; ?>"></td>
           </tr>
           <tr>
             <td><?php echo lang("Title"); ?></td>
             <td><input name="n_title" value="<?php echo $n_title; ?>"></td>
           </tr>
           <tr>
             <td><?php echo lang("Phone Number"); ?></td>
             <td><input name="n_phone" value="<?php echo $n_phone; ?>"></td>
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
             <td><?php echo lang("Hire Date"); ?></td>
             <td><input name="n_hiredate" value="<?php echo $n_hiredate; ?>"></td>
           </tr>
           <tr>
             <td><?php echo lang("Vacation Hours"); ?></td>
             <td><input name="n_vhours" value="<?php echo $n_vhours; ?>"></td>
           </tr>
           <tr>
             <td><?php echo lang("Vacation Hours Used"); ?></td>
             <td><input name="n_vhours_used" value="<?php echo $n_vhours_used; ?>"></td>
           </tr>
           <tr>
             <td><?php echo lang("Location"); ?></td>
             <td><select name="n_location"><?php
                   $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_locations");
                   while ($GLOBALS['phpgw']->db->next_record()) {
                    $locid = $GLOBALS['phpgw']->db->f("location_id");
                    echo "<option value=\"" . $locid . "\"";
                    if ($ $locid == $n_location_id ) {
                     echo " selected";
                    }
		    echo ">" . $GLOBALS['phpgw']->db->f("location_name") . "</option>";
		   }
		?>
           </select></tr>
           </tr>
	   <tr>
	     <td><?php echo lang("Comments"); ?></td>
	     <td><textarea cols="30" rows="4" name="n_comments" 
		wrap="virtual"><?php echo $n_comments; ?></textarea></td>
           </tr>
           <tr>
             <td colspan=2>
              <input type="submit" name="submit" value="<?php echo lang("submit"); ?>">
             </td>
           </tr>
         </table>
        </center>
       </form>
     <?php
     $GLOBALS['phpgw']->common->phpgw_footer();
  }
?>
