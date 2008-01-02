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

  /* $Id: newcustomer.php 9782 2002-03-18 03:18:05Z rschader $ */
  // Update complete for phpgroupware 0.9.10 - 4/17/2001 (api calls for accounts and contacts)
  // Note: This file required no updates regarding accounts and contacts classes

if($submit) {
  $GLOBALS['phpgw_info']["flags"] = array("noheader" => True, "nonavbar" => True);
}

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";

  include("../header.inc.php");

if($submit)
 {
  if($cust_active == "True") {
   // Set active flag to Y
   $active = "Y";
  } else {
   $active = "N";
  }

  if ($lastjobfinished == "")
  {
    // I should add a check here later to make sure the org_name doesn't already exist.
    $sql = "INSERT INTO phpgw_ttrack_customers "
	. "(company_name,website,ftpsite,industry_type,status,software,"
	. "lastjobnum,busrelationship,notes,active) "
	. "VALUES ('" . addslashes($company_name)
	. "','" .addslashes($website)
	. "','" . addslashes($ftpsite)
	. "','" . addslashes($industry_type)
	. "','" . addslashes($status)
	. "','" . addslashes($software)
	. "','" . addslashes($lastjobnum)
	. "','" . addslashes($busrelationship)
	. "','" . addslashes($notes) 
	. "','" . $active
	. "')";
  } else {
    // I should add a check here later to make sure the org_name doesn't already exist.
    $sql = "INSERT INTO phpgw_ttrack_customers "
	. "(company_name,website,ftpsite,industry_type,status,software,"
	. "lastjobnum,lastjobfinished,busrelationship,notes,active) "
	. "VALUES ('" . addslashes($company_name)
	. "','" .addslashes($website)
	. "','" . addslashes($ftpsite)
	. "','" . addslashes($industry_type)
	. "','" . addslashes($status)
	. "','" . addslashes($software)
	. "','" . addslashes($lastjobnum)
	. "','" . addslashes($lastjobfinished)
	. "','" . addslashes($busrelationship)
	. "','" . addslashes($notes) 
	. "','" . $active
	. "')";
  }
  //echo $sql;
  $GLOBALS['phpgw']->db->query($sql);
  echo '<script LANGUAGE="JavaScript">';
  echo 'window.location="' . $GLOBALS['phpgw']->link("/timetrack/customers.php") . '"';
  echo '</script>';
 }
else
 {
   inc_cal(); // Init js calendar datepicker
   inc_myutil(); // validation routines, etc for form inputs

  ?>

   <center>
   <form method="POST" name="addcust" action="<?php 
	echo $GLOBALS['phpgw']->link("/timetrack/newcustomer.php");?>">
   <p><table border=0 width=60%>

    <tr>
     <th colspan="2" align="center">Add Customer</th>
    </tr>

    <tr>
     <td width="30%"><?php echo lang("Company Name"); ?></td>
     <td width="70%"><input name="company_name" value="<?php echo $company_name; ?>"></td>
    </tr>

    <tr>
     <td width="30%"><?php echo lang("Web Site"); ?></td>
     <td width="70%"><input name="website" value="<?php echo $website; ?>"></td>
    </tr>

    <tr>
     <td width="30%"><?php echo lang("FTP Site"); ?></td>
     <td width="70%"><input name="ftpsite" value="<?php echo $ftpsite; ?>"></td>
    </tr>

    <tr>
     <td width="30%"><?php echo lang("Industry Type"); ?></td>
     <td width="60%"><input name="industry_type" value="<?php echo $industry_type; ?>"></td>
    </tr>

    <tr>
     <td width="30%"><?php echo lang("Status"); ?></td>
     <td width="70%"><input name="status" value="<?php echo $status; ?>"></td>
    </tr>

    <tr>
     <td width="30%"><?php echo lang("Software"); ?></td>
     <td width="70%"><input name="software" value="<?php echo $software; ?>"></td>
    </tr>

    <tr>
     <td width="30%"><?php echo lang("Last Job Number"); ?></td>
     <td width="70%"><input name="lastjobnum" onBlur="CheckNum(this,0,99999);" value="<?php echo $lastjobnum; ?>"></td>
    </tr>

    <tr>
     <td width="30%"><?php echo lang("Date Finished"); ?></td>
     <td width="70%">
     <?php 
	   CalDateSelector("addcust","lastjobfinished",0,"");
     ?></td>
    </tr>

    <tr>
     <td width="30%"><?php echo lang("Relationship"); ?></td>
     <td width="70%"><input name="busrelationship" value="<?php echo $busrelationship; ?>"></td>
    </tr>

    <tr>
     <td width="40%"><?php echo lang("Active Jobs"); ?></td>
     <td width="60%"><input type="checkbox" name="cust_active" value="True"
       <?php
          if($cust_active == "Y") echo " CHECKED";
           echo "></td>";
       ?>
     </td>
    </tr>

    <tr>
     <td width="30%"><?php echo lang("Notes"); ?></td>
     <td width="70%"><textarea  name="notes" cols="40" rows="4"
        wrap="virtual"><?php echo $notes; ?></textarea></td>
    </tr>

    <tr>
     <td colspan="2">&nbsp;</td>
    </tr>

    <tr>
      <td colspan=2>
      <input type="submit" name="submit" value="<?php echo lang("submit"); ?>">
      </td>
    </tr>

    </table>
   </form>
   </center>

<?php
  $GLOBALS['phpgw']->common->phpgw_footer();
 }
?>
