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

  /* $Id: viewprofile.php 9782 2002-03-18 03:18:05Z rschader $ */

  // Update complete for phpgroupware 0.9.10 - 4/18/2001 (api calls for accounts and contacts)
  // No relevant queries to update to api here

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";

  include("../header.inc.php");
  if (! $id)
     Header("Location: profiles.php?sessionid=" . $GLOBALS['phpgw']->session->id);

  $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_emplyprof where id='$id'");
  $GLOBALS['phpgw']->db->next_record();
  $n_location_id = $GLOBALS['phpgw']->db->f("location_id");


  ?>
   <center>
   <?php
	echo '<h2>' . lang("View Profile") . '</h2>'
   ?>
   <p><table border=0 width=50%>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["th_bg"]; ?>">
     <td colspan="2">&nbsp;</td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_on"]; ?>">
     <td width="40%"><?php echo lang("LoginID"); ?></td>
     <td width="60%"><?php echo $GLOBALS['phpgw']->db->f("lid"); ?></td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_off"]; ?>">
     <td width="40%"><?php echo lang("Title"); ?></td>
     <td width="60%"><?php echo $GLOBALS['phpgw']->db->f("title"); ?></td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_on"]; ?>">
     <td width="40%"><?php echo lang("Work Phone"); ?></td>
     <td width="60%"><?php echo $GLOBALS['phpgw']->db->f("phone_number"); ?></td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_off"]; ?>">
     <td width="40%"><?php echo lang("Mobile phone"); ?></td>
     <td width="60%"><?php echo $GLOBALS['phpgw']->db->f("mobilephn"); ?></td>
    </tr>

    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_on"]; ?>">
	 <td width="40%"><?php echo lang("Pager"); ?></td>
	 <td width="60%"><?php echo $GLOBALS['phpgw']->db->f("pager"); ?></td>
	</tr>
    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_off"]; ?>">
     <td width="40%"><?php echo lang("Comments"); ?></td>
     <td width="60%"><?php echo $GLOBALS['phpgw']->db->f("comments"); ?></td>
    </tr>
    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_on"]; ?>">
     <td width="40%"><?php echo lang("Date Hired"); ?></td>
     <td width="60%"><?php echo $GLOBALS['phpgw']->db->f("hire_date"); ?></td>
    </tr>
    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_off"]; ?>">
     <td width="40%"><?php echo lang("Vacation Hours per year"); ?></td>
     <td width="60%"><?php echo $GLOBALS['phpgw']->db->f("yearly_vacation_hours"); ?></td>
    </tr>
    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_on"]; ?>">
     <td width="40%"><?php echo lang("Vacation Hours Used"); ?></td>
     <td width="60%"><?php echo $GLOBALS['phpgw']->db->f("vacation_hours_used_todate"); ?></td>
    </tr>
    <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["row_off"]; ?>">
	 <td width="40%"><?php echo lang("Location"); ?></td>
	 <td width="60%"><?php
	  $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_locations "
		. "where location_id='$n_location_id'");
	  $GLOBALS['phpgw']->db->next_record();
	  echo $GLOBALS['phpgw']->db->f("location_name");
	  ?>
	  </td>
	 </tr>
    </table>
   </center>

<?php
  $GLOBALS['phpgw']->common->phpgw_footer();

