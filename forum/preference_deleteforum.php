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
  if($confirm || ! $for_id) {
    $phpgw_info["flags"]["noheader"] = True;
    $phpgw_info["flags"]["nonavbar"] = True;
  }
  include("../header.inc.php");

  if (($for_id) && (! $confirm)) {
    ?>
     <center>
      <table border=0 width=65%>
       <tr colspan=2>
        <td align=center>
         <?php echo lang("Are you sure you want to delete this forum?"); ?>
        <td>
       </tr>
       <tr colspan=2>
        <td align=center>
         <?php echo "<font color=\"red\"><blink>".lang("All user posts and topics will be lost!")."</blink></font>"; ?>
        </td>
       </tr>
       <tr>
         <td>
           <a href="<?php echo $phpgw->link("/forum") . "\">" . lang("No"); ?></a>
         </td>
         <td>
           <a href="<?php echo $phpgw->link('/forum/preference_deleteforum.php',array('for_id' => $for_id, 'confirm' => 'true')) . "\">" . lang("Yes"); ?></a>
         </td>
       </tr>
      </table>
     </center>
    <?php
    $phpgw->common->phpgw_footer();
  }

  if (($for_id) && ($confirm)) {
    $phpgw->db->query("delete from f_threads where for_id=$for_id");
    $phpgw->db->query("delete from f_body where for_id=$for_id");
    $phpgw->db->query("delete from f_forums where id=$for_id");

    Header("Location: " . $phpgw->link("/forum"));
  }
?>
