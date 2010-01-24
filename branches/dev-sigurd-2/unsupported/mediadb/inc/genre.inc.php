<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB Genre Functions                                   *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

function add_genre_entry($order, $sort, $filter, $start, $query, $qfield)
{
    global $phpgw, $phpgw_info;

    $color = $phpgw_info["theme"]["th_bg"];
    
    printf("<form method=POST action=\"%s\">\n",
           $phpgw->link("/mediadb/genre.php",
                        "act=add"
                        ."&start=$start&order=$order&filter=$filter"
                        ."&sort=$sort"
                        ."&query=".urlencode($query)
			."&qfield=$qfield"));
    printf("  <table border=\"0\" cellpadding=\"0\""
           . " cellspacing=\"0\" width=75%% align=center>\n");
    printf("    <tr bgcolor=%s>\n", $color);
    printf("      <td align=left width=15%%>\n");
    printf("        <input type=\"submit\" name=\"submit\" value=\"%s\">\n",lang("Add"));
    printf("      </td>\n");
    printf("      <td align=right width=10%%>%s:</td>\n",lang("Desc"));
    printf("      <td align=left>\n");
    printf("        <input type=\"text\" name=\"genre_desc\""
           ." value=\"%s\" maxlength=30>\n", $fdesc);
    printf("      </td>\n");
    printf("      <td align=center>\n");
    printf("        <select name=\"cat_id\" size=1>\n");
    
    $phpgw->db->query("select * from phpgw_mediadb_cat where cat_id > 1");
    while ($phpgw->db->next_record())
    {
        printf("          <option value=\"%s\"", $phpgw->db->f("cat_id"));
        
        printf(">%s</option>\n", lang($phpgw->db->f("cat_name")));
    }
    
    printf("        </select>\n");
    printf("      </td>\n");

    printf("    </tr>\n");
    printf("  </table>\n");
    printf("</form>\n");
}

function modify_genre_entry($con, $act, $order, $sort, $filter, $start, $query, $qfield)
{
    global $phpgw, $phpgw_info;

    $phpgw->db->query("select * from phpgw_mediadb_genre where genre_id=$con");
    $phpgw->db->next_record();

    $fdesc  = $phpgw->db->f("genre_desc");
    $cat    = $phpgw->db->f("cat_id");
    
    switch($act)
    {
      case "delete":
        {
            $color = $phpgw_info["theme"]["bg07"];
        }
        break;
      default:
        {
            $color = $phpgw_info["theme"]["table_bg"];
        }
        break;
    }
    
    printf("<form method=POST action=\"%s\">\n",
           $phpgw->link("/mediadb/genre.php",
                        "act=$act"
                        ."&start=$start&order=$order&filter=$filter"
                        ."&sort=$sort"
                        ."&query=".urlencode($query)
			."&qfield=$qfield"));
    printf("<input type=\"hidden\" name=\"genre_id\" value=\"%s\">\n",$con);
    printf("  <table border=\"0\" cellpadding=\"0\""
           . " cellspacing=\"0\" width=75%% align=center>\n");
    printf("    <tr bgcolor=%s>\n",$color);
    printf("      <td align=left width=15%%>\n");
    printf("        <input type=\"submit\" name=\"submit\" value=\"%s\">\n",lang($act));
    printf("      </td>\n");
    printf("      <td align=right width=10%%>%s:</td>\n",lang("Desc"));
    printf("      <td align=left>\n");
    printf("        <input type=\"text\" name=\"genre_desc\""
           ." value=\"%s\" maxlength=30>\n", $fdesc);
    printf("      </td>\n");
    printf("      <td align=center>\n");
    printf("        <select name=\"cat_id\" size=1>\n");

    $phpgw->db->query("select * from phpgw_mediadb_cat where cat_id > 1");
    while ($phpgw->db->next_record())
    {
        printf("          <option value=\"%s\"", $phpgw->db->f("cat_id"));

        if ($cat == $phpgw->db->f("cat_id"))
        {
            printf(" selected");
        }
        
        printf(">%s</option>\n", lang($phpgw->db->f("cat_name")));
    }
    
    printf("        </select>\n");
    printf("      </td>\n");
    printf("    </tr>\n");
    printf("  </table>\n");
    printf("</form>\n");
}
?>
