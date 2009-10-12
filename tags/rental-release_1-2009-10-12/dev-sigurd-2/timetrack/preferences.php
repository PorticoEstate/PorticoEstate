<?php
  /**************************************************************************\
  * phpGroupWare - Time Tracking                                             *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

  // Update complete for phpgroupware 0.9.10 - 4/17/2001 (api calls for accounts and contacts)
  // Nothing for contacts or accounts to update.

 if($submit) {
  $GLOBALS['phpgw_info']["flags"] = array("noheader" => True, "nonavbar" => True);
 }

  $GLOBALS['phpgw_info']["flags"]["enable_nextmatchs_class"] = "True";
  $GLOBALS['phpgw_info']["flags"]["currentapp"] = "timetrack";
  include("../header.inc.php");

  if ($submit) {
     //$GLOBALS['phpgw']->common->preferences_delete("byapp",$GLOBALS['phpgw_info']["user"]["account_id"],"timetrack");
     //$GLOBALS['phpgw']->common->preferences_add($GLOBALS['phpgw_info']["user"]["account_id"],"cnamesize","timetrack");
     $GLOBALS['phpgw']->preferences->change("timetrack","cnamesize",$cnamesize);
     $GLOBALS['phpgw']->preferences->commit();

     Header("Location: " . $GLOBALS['phpgw']->link("/preferences/index.php"));
     exit;
  }

  if ($totalerrors) {  
     echo "<p><center>" . $GLOBALS['phpgw']->common->error_list($errors) . "</center>";
  }

  echo "<p><b>" . lang("time tracking preferences") . ":" . "</b><hr><p>";
  // Preference items for this app:
  // 1. Definitely need a "company_name_dropdown_size" field for unix browsers.
  //    Use drop downs for this to limit entries (1, 5, 10, 15, 20)
  // 2. Other dropdown sizes?
  // 3. Columns to display for jobslist, customerlist.
  // 4. Clock increment values for time entry will be a global set by
  //    and admin menu.
  // 5. Inline calendar or floating, if inline, custom layer positioning.
  // 6. Default lunch time start end end.
?>
 <form action="<?php echo $GLOBALS['phpgw']->link("/timetrack/preferences.php"); ?>" method="POST">
  <table border="0" align="center" width="50%">
  <tr bgcolor="<?php echo $GLOBALS['phpgw_info']["theme"]["th_bg"]; ?>">
   <td colspan="2">&nbsp;</td>
  </tr>

  <?php
    $current_csize = $GLOBALS['phpgw_info']["user"]["preferences"]["timetrack"]["cnamesize"];
    $tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
  ?>
  <tr bgcolor="<?php echo $tr_color; ?>">
   <td><?php echo lang("Height for Company Name Dropdown"); ?></td>
   <td align="center">
    <?php
      echo '<select name="cnamesize">';
      $sizes = array(1,5,10,15,20);
      for($i=0; $i<5; $i++){
        echo '<option value="' . $sizes[$i] . '"';
        if($sizes[$i] == $current_csize) echo " selected";
        echo '>' . $sizes[$i] . '</option>';
      }
      echo '</select>';
    ?>
  </td>

  </tr>
  <tr><td align="center"><input type="submit" name="submit" value="<?php echo lang("submit"); ?>"></td></tr>
 </table>
</form>

<?php 
    $GLOBALS['phpgw']->common->phpgw_footer();
?>
