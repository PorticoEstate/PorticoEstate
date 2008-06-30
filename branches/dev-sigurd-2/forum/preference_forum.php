<?php
	/*****************************************************************************\
	* phpGroupWare - Forums                                                       *
	* http://www.phpgroupware.org                                                 *
	* Written by Jani Hirvinen <jpkh@shadownet.com>                               *
	* -------------------------------------------                                 *
	*  This program is free software; you	can redistribute it and/or modify it   *
	*  under the terms of	the GNU	General	Public License as published by the  *
	*  Free Software Foundation; either version 2	of the License,	or (at your *
	*  option) any later version.                                                 *
	\*****************************************************************************/

	/* $Id$ */

  $phpgw_info["flags"] = array("currentapp" => "forum", "enable_nextmatchs_class" => True);
  if($action) {
    $phpgw_info["flags"]["noheader"] = True;
    $phpgw_info["flags"]["nonavbar"] = True;
  }
  include("../header.inc.php");

  $actiontype = "addforum";
  $buttontext = lang("Add Forum");

  if($act == "edit") {
    if(!$phpgw->db->query("select * from f_forums where id=$for_id")) {
      print "Error in reading database<br>\n";
      $phpgw->common->phpgw_exit();
    } else {
      $phpgw->db->next_record(); 
      $forname = $phpgw->db->f("name");
      $fordescr = $phpgw->db->f("descr");
      $cat_id = $phpgw->db->f("cat_id");
      if ($cat_id > 0) {
        if(!$phpgw->db->query("select * from f_categories where id=$cat_id")) {
          print "Error in readindg database<br>\n";
          $phpgw->common->phpgw_exit();
        } else $phpgw->db->next_record();
        $catname = $phpgw->db->f("name");
      } else $catname = lang("No Category");
      $extrahidden = "<input type=\"hidden\" name=\"for_id\" value=\"$for_id\">";
      $buttontext = lang("Update Forum");
      $actiontype = "updforum";
    }
  }

  
  if($action) {
   if($action == "addforum") {
    if(!$phpgw->db->query("insert into f_forums (name,descr,cat_id) values ('$forname','$fordescr',$goestocat)")) {
     print "Error in adding forum to database<br>\n";
     $phpgw->common->phpgw_exit();
    } else {
     Header("Location: " . $phpgw->link("/forum/preference_index.php"));
     $phpgw->common->phpgw_exit();
    }
   } elseif ($action == "updforum" && $for_id) {
    if(!$phpgw->db->query("update f_forums set name='$forname',descr='$fordescr',cat_id=$goestocat where id=$for_id ")) {
     print "Error in updating forum on database<br>\n";
     $phpgw->common->phpgw_exit();
    } else {
     Header("Location: " . $phpgw->link("/forum/preference_index.php"));
     $phpgw->common->phpgw_exit();
    }
   }
  }



?>

<p>
<table border="0" width=100%>
<tr>
<?php echo "<td bgcolor=\"" . $phpgw_info["theme"]["th_bg"] . "\" align=\"left\"><b>" . lang("Forums") . " " . lang("Admin") . "</b></td>" . "</tr>"; ?>

<tr>
 <td>
  <font size=-1>
<?php
echo "<a href=\"" . $phpgw->link("/forum/preference_category.php") . "\">" . lang("New Category") ."</a>";
echo " | ";
echo "<a href=\"" . $phpgw->link("/forum/preference_forum.php") . "\">" . lang("New Forum") ."</a>";   
echo " | ";
echo "<a href=\"" . $phpgw->link("/forum/preference_index.php") . "\">" . lang("Return to Admin") ."</a>";  
echo " | ";
echo "<a href=\"" . $phpgw->link("/forum") . "\">" . lang("Return to Forums") ."</a>";
  
?>
  </font>
  <br><br>
  <center>
  <table border="0" width=80% bgcolor="<?php echo $phpgw_info["theme"]["table_bg"]?>">
   <tr>
    <td colspan=2 bgcolor="<?php echo $phpgw_info["theme"]["th_bg"]?>">
     <center><?php echo $buttontext ?></center>
    </td>
   </tr>
   <tr>
    <form method="POST" action="<?php echo $phpgw->link("/forum/preference_forum.php"); ?>">
    <?php echo $extrahidden ?> 
    <input type="hidden" name="action" value="<?php echo $actiontype?>">

    <td><?php echo lang("Belongs to Category") ?>:</td>
    <td>
     <select name="goestocat">
<?php
    $q = $phpgw->db->query("select * from f_categories");

    while($phpgw->db->next_record($q)) {
      $cat_id = $phpgw->db->f("id");
      $cat_name = $phpgw->db->f("name");
      if ($catname==$cat_name) { echo "<option value=\"$cat_id\" selected>$cat_name</option>\n"; }
      else { echo "<option value=\"$cat_id\">$cat_name</option>\n"; }
    }
?>
    <option value=-1><?php echo lang("No Category") ?></option>
    </select>
   </td>
   <tr>
    <td><?php echo lang("Forum Name") ?>:</td>
    <td><input type="text" name="forname" size=40 maxlength=49 value="<?php echo $forname ?>"></td>
   </tr>  
   <tr>
    <td><?php echo lang("Forum Description") ?>:</td>
    <td><textarea rows="3" cols="40" name="fordescr" virtual-wrap maxlength=240><?php echo $fordescr ?></textarea></td>
   </tr>
   <tr>
    <td colspan=2 align=right>

     <input type="submit" value="<?php echo $buttontext?>">
    </td>
   </tr>

  </table>
  </center>
  <br>
 </td>
</tr>

   </tr>
  </table>
  </center>
  <br>




 </td>
</tr>
</table>


<?php
  $phpgw->common->phpgw_footer();
?>
