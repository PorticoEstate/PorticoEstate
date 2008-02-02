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

  $phpgw_info["flags"]["currentapp"] = "forum";
  if($action) {
    $phpgw_info["flags"]["noheader"] = True;
    $phpgw_info["flags"]["nonavbar"] = True;
  }
  include("../header.inc.php");

  $actiontype = "addcat";
  $buttontext = lang("Add Category");
  $extrahidden = "";

  if($act == "edit") {
    $newold = lang("Update Category");
    if(!$phpgw->db->query("select * from f_categories where id=$cat_id")) {
      print "Error in reading database<br>\n";
      $phpgw->common->phpgw_exit();
    } else {
      $phpgw->db->next_record();
      $catname = $phpgw->db->f("name");
      $catdescr = $phpgw->db->f("descr");
      $cat_id = $phpgw->db->f("id"); 

      $extrahidden = "<input type=\"hidden\" name=\"cat_id\" value=\"$cat_id\">"; 
      $buttontext = lang("Update Category");
      $actiontype = "updcat";
    }
  } else {
    $newold = lang("Create New Category");
  }
  
  if($action) {
   if($action == "addcat") {
    if(!$phpgw->db->query("insert into f_categories (name,descr) values ('$catname','$catdescr')")) {
     print "Error in adding forum to database<br>\n";
     $phpgw->common->phpgw_exit();
    } else {
     Header("Location: " . $phpgw->link("/forum"));
     $phpgw->common->phpgw_exit();
    }
   } elseif ($action == "updcat" && $cat_id) {
    if(!$phpgw->db->query("update f_categories set name='$catname',descr='$catdescr' where id = $cat_id")) {
     print "Error in adding forum to database<br>\n";
     $phpgw->common->phpgw_exit();
    } else {
     Header("Location: " . $phpgw->link("/forum"));  
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
     <center><?php echo $newold?></center>
    </td>
   </tr>
   <tr>
    <form method="POST" action="<?php echo $phpgw->link("/forum/preference_category.php"); ?>">
    <?php echo $extrahidden; ?> 
    <input type="hidden" name="action" value="<?php echo $actiontype?>">
    <td><?php echo lang("Category Name") ?>:</td>
    <td><input type="text" name="catname" size=40 maxlength=49 value="<?php echo $catname ?>"></td>
   </tr>  
   <tr>
    <td><?php echo lang("Category Description") ?>:</td>
    <td><textarea rows="3" cols="40" name="catdescr" virtual-wrap maxlength=240><?php echo $catdescr ?></textarea></td>
   </tr>
   <tr><td colspan=2 align=right><input type="submit" value="<?php echo $buttontext ?>"></td></tr>

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
