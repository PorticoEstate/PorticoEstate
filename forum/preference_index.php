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
  include("../header.inc.php");

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
echo "<a href=\"" . $phpgw->link("/forum") . "\">" . lang("Return to Forums") ."</a>";

?>
  </font>
  <br><br>
  <center>
  <table border="0" width=80% bgcolor="<?php echo $phpgw_info["theme"]["table_bg"]?>">
   <tr>
    <td colspan=3 bgcolor="<?php echo $phpgw_info["theme"]["th_bg"]?>">
     <center><?php echo lang("Current Categories and Sub Forums")?></center>
    </td>
   </tr>
   <tr>
    <td>
    </td>
   </tr>
<?php
  $f_tree = array();
  $phpgw->db->query("select * from f_categories");
  while($phpgw->db->next_record()) {
    $f_tree[$phpgw->db->f("id")] = array("name"=>$phpgw->db->f("name"), "descr"=>$phpgw->db->f("descr"), "forums"=>array());
  }
  $phpgw->db->query("select * from f_forums");
  while($phpgw->db->next_record()) {
    $f_tree[$phpgw->db->f("cat_id")]["forums"][$phpgw->db->f("id")] = array("name"=>$phpgw->db->f("name"), "descr"=>$phpgw->db->f("descr"));
  }
  ksort($f_tree);
  for(reset($f_tree);$id=key($f_tree);next($f_tree)) {
    if($id > 0) {
      echo "<tr><td></td></tr>";
      echo "<tr bgcolor=\"" . $phpgw_info["theme"]["bg06"] . "\">\n";
      echo " <td valign=top align=left width=20%>" . $f_tree[$id]["name"] . "</td>\n";
      echo " <td valign=top align=left width=70%>" . $f_tree[$id]["descr"] . "</td>\n";
      echo "   <td nowrap><a href=\"" . $phpgw->link("/forum/preference_category.php","act=edit&cat_id=$id") ."\">" . lang("Edit") . "</A> | ";
      echo "<A href=\"" . $phpgw->link("/forum/preference_deletecategory.php", "cat_id=$id") . "\">" . lang("Delete") . "</A></td>\n";
      echo "</tr>\n";
      echo "<tr>\n";
      echo " <td colspan=3 align=right valign=top>\n";
      echo "<table border=0 width=95%>\n";
    } else {
      echo "<tr>\n";
      echo " <td colspan=3 align=right valign=top>\n";
      echo "<table border=0 width=100%>\n";
    }

    $tr_color = $phpgw_info["theme"]["row_off"];

    for(reset($f_tree[$id]["forums"]); $fid=key($f_tree[$id]["forums"]); next($f_tree[$id]["forums"])) {
      $tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);
      echo "  <tr bgcolor=\"$tr_color\">\n";
      echo "   <td width=20%>" . $f_tree[$id]["forums"][$fid]["name"] . "</td>\n";
      echo " <td valign=top align=left width=70%>". $f_tree[$id]["forums"][$fid]["descr"] . "</td>\n";
      echo "   <td nowrap><a href=\"" . $phpgw->link("/forum/preference_forum.php","act=edit&for_id=$fid") ."\">" . lang("Edit") . "</A> | ";
      echo "<A href=\"" . $phpgw->link("/forum/preference_deleteforum.php", "for_id=$fid") . "\">" . lang("Delete") . "</A></td>\n";
      echo "  </tr>\n";
    }
    echo "  </table>\n";
    echo " </td>\n";
    echo "</tr>\n";
  }
?>
    </td>
   </tr>
  </table>
  <br>


  <br><br>
  </center>
 </td>
</tr>
</table>
<?php

echo "</center>";
  $phpgw->common->phpgw_footer();
?>

