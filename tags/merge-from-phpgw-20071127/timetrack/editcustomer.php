<?php
  /**************************************************************************\
  * phpgwtimetrack - phpGroupWare addon application                          *
  * http://phpgwtimetrack.sourceforge.net                                    *
  * Written by Robert Schader <bobs@product-des.com>                         *
  * Written by Joseph Engo <jengo@phpgroupware.org>                          *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: editcustomer.php 9782 2002-03-18 03:18:05Z rschader $ */

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

  $sql = "UPDATE phpgw_ttrack_customers SET "
	. "company_name='"	. addslashes($company_name)
	. "', website='"	. addslashes($website)
	. "', ftpsite='"	. addslashes($ftpsite)
	. "', industry_type='"	. addslashes($industry_type)
	. "', status='"		. addslashes($status)
	. "', software='"	. addslashes($software)
	. "', lastjobnum='"	. addslashes($lastjobnum)
	. "', lastjobfinished='" . addslashes($lastjobfinished)
	. "', busrelationship='" . addslashes($busrelationship)
	. "', notes='"		. addslashes($notes)
	. "', active='"		. $active
	. "' WHERE company_id=" . $cid;

  $GLOBALS['phpgw']->db->query($sql);
  /* Note: if we allow managers and payroll to change the name for the company,
   * we SHOULD add a procedure that checks if the name changed and go in and
   * find all the contacts with the old value in org_name, and change it to
   * the new name. (arrrrgggh!!!)
   */
  if ($oldcname != $company_name && $autoupdate == "True") {
    // we need to do the above contact update
    $contacts = CreateObject('phpgwapi.contacts');
    $qfields = array(
        'id' => 'id',
	'tid' => 'tid',
	'owner' => 'owner',
	'cat_id' => 'cat_id',
	'access' => 'access',
	'n_given' => 'n_given',
	'n_family' => 'n_family',
	'org_name' => 'org_name'
    );
    $start = 0;
    $offset = 0;
    $query = addslashes($oldcname);
    $filter='tid=n';
    $sort = "asc";
    $order = "n_given,n_family";
    $entries =
        $contacts->read($start,$offset,$qfields,$query,$filter,$sort,$order);
    if (count($entries) > 0) {
      $fields = array();
      $fields['org_name'] = $company_name;
      for ($i=0; $i<count($entries); $i++)
      {
        $ab_id = $entries[$i]['id'];
	$owner = $entries[$i]['owner'];
	$org_name = $entries[$i]['org_name'];
	$access = $entries[$i]['access'];
	$cat_id = $entries[$i]['cat_id'];
	$tid = $entries[$i]['tid'];
	$contact = $entries[$i]['n_given'] . " " . $entries[$i]['n_family'];
	// Make sure we have an exact match on the org_name before updating
	if($org_name == $oldcname) {
	  //$contacts->update($ab_id,$owner,$fields,$access,$cat_id,$tid);
	  $contacts->update($ab_id,$owner,$fields);
	  // This next line is mainly for testing
	  echo "Addressbook Contact $contact Organization Name changed from $oldcname to $company_name...<br>";
	} // end if orgname
      } // end of for loop 
      $printlink = "True";
    } // end if entries > 0
  } // end of if companyname changed
  if($printlink == "True") {
    echo '<br><br>To continue, <a href="' . $GLOBALS['phpgw']->link("/timetrack/customers.php")
        . '">' . lang("Click here") . '</a>';
  } else {
    echo '<script LANGUAGE="JavaScript">';
    echo 'window.location="' . $GLOBALS['phpgw']->link("/timetrack/customers.php") . '"';
    echo '</script>';
  }
 }
else
 {
  inc_cal(); // Init js calendar datepicker
  inc_myutil(); // validation routines, etc for form inputs

  if (! $cid)
     Header("Location: " . $GLOBALS['phpgw']->link("/timetrack/customers.php")); 

  $GLOBALS['phpgw']->db->query("select * from phpgw_ttrack_customers where company_id='$cid'");
  $GLOBALS['phpgw']->db->next_record();
  $company_id = $GLOBALS['phpgw']->db->f("company_id");
  $company_name = $GLOBALS['phpgw']->db->f("company_name");
  $website = $GLOBALS['phpgw']->db->f("website");
  $ftpsite = $GLOBALS['phpgw']->db->f("ftpsite");
  $industry_type = $GLOBALS['phpgw']->db->f("industry_type");
  $status = $GLOBALS['phpgw']->db->f("status");
  $software = $GLOBALS['phpgw']->db->f("software");
  $lastjobnum = $GLOBALS['phpgw']->db->f("lastjobnum");
  $lastjobfinished = $GLOBALS['phpgw']->db->f("lastjobfinished");
  $busrelationship = $GLOBALS['phpgw']->db->f("busrelationship");
  $notes = $GLOBALS['phpgw']->db->f("notes");
  $cust_active = $GLOBALS['phpgw']->db->f("active");

  echo "<center><h3>" . lang("Customer") . " - $company_name</h3>";
  ?>

   <center>
   <form method="POST" name="editcust" action="<?php echo $GLOBALS['phpgw']->link("/timetrack/editcustomer.php");?>">
   <p><table border=0 width=50%>

    <input type="hidden" name="cid" value="<?php echo $cid;?>">
    <input type="hidden" name="oldcname" value="<?php echo $company_name; ?>">

    <?php
      if($GLOBALS['phpgw_info']["apps"]["timetrack"]["ismanager"] == 1 || $GLOBALS['phpgw_info']["apps"]["timetrack"]["ispayroll"] == 1)
      {
       echo '<tr><td width="40%">' . lang("Company Name") . '</td>';
       echo '<td width="60%"><input name="company_name" value="' . $company_name . '"></td></tr>';
       echo '<tr><td width="40%">' . lang("Auto Update Orgname in Contacts") . '</td>';
       echo '<td width="60%"><input type="checkbox" name="autoupdate" value="True"';
       // Move the following var to a preferance item when I get the chance:
       $tt_auto_update_pref = "Y";
       if ($tt_auto_update_pref == "Y") echo "CHECKED";
       echo "></td>";
      } else {
       echo '<input type="hidden" name="company_name" value="' . $company_name . '">';
      }
    ?>

    <tr>
     <td width="40%"><?php echo lang("Web Site"); ?></td>
     <td width="60%"><input name="website" value="<?php echo $website; ?>"></td>
    </tr>

    <tr>
     <td width="40%"><?php echo lang("FTP Site"); ?></td>
     <td width="60%"><input name="ftpsite" value="<?php echo $ftpsite; ?>"></td>
    </tr>

    <tr>
     <td width="40%"><?php echo lang("Industry Type"); ?></td>
     <td width="60%"><input name="industry_type" value="<?php echo $industry_type; ?>"></td>
    </tr>

    <tr>
     <td width="40%"><?php echo lang("Status"); ?></td>
     <td width="60%"><input name="status" value="<?php echo $status; ?>"></td>
    </tr>

    <tr>
     <td width="40%"><?php echo lang("Software"); ?></td>
     <td width="60%"><input name="software" value="<?php echo $software; ?>"></td>
    </tr>

    <tr>
     <td width="40%"><?php echo lang("Last Job"); ?></td>
     <td width="60%"><input name="lastjobnum" onBlur="CheckNum(this,0,99999);" value="<?php echo $lastjobnum; ?>"></td>
    </tr>

    <tr>
     <td width="40%"><?php echo lang("Date Finished"); ?></td>
     <td width="60%">
     <?php 
	   CalDateSelector("editcust","lastjobfinished",0,"");
     ?></td>
    </tr>

    <tr>
     <td width="40%"><?php echo lang("Relationship"); ?></td>
     <td width="60%"><input name="busrelationship" value="<?php echo $busrelationship; ?>"></td>
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
     <td width="40%"><?php echo lang("Notes"); ?></td>
     <td width="60%"><textarea  name="notes" cols="40" rows="4"
        wrap="virtual"><?php echo $notes; ?></textarea></td>
    </tr>

    <tr>
     <td colspan="2">&nbsp;</td>
    </tr>

    <tr>
      <td colspan=2>
      <input type="submit" name="submit" value="<?php echo lang("update"); ?>">
      </td>
    </tr>

    </table>
   </form>
   </center>

<?php
  $GLOBALS['phpgw']->common->phpgw_footer();
 }
?>
