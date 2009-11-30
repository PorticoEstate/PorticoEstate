<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB Publisher Functions                               *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */


function list_publisher($order, $sort, $filter, $start, $query)
{
    global $phpgw, $phpgw_info;

    if ($order)
    {
        $ordermethod = "order by $order $sort";
    }
    else
    {
        $ordermethod = "order by publisher_name asc";
    }
    
    if (! $sort)
    {
        $sort = "desc";
    }

    if (! $start)
    {
        $start = 0;
    }
    
    if (! $filter)
    {
        $filter = "none";
    }
    
    $limit =$phpgw->db->limit($start);
    
    if (!$query)
    {
        $phpgw->db->query("select count(*) from phpgw_mediadb_publisher "
                          .$ordermethod);
    }
    else
    {
        $phpgw->db->query("select count(*) from phpgw_mediadb_publisher "
                          ."WHERE publisher_name like '%$query%' "
                          .$ordermethod);
    }
    
    $phpgw->db->next_record(); 
    if ($phpgw->db->f(0) > $phpgw_info["user"]["preferences"]["common"]["maxmatchs"])
    {
        printf("<center>%s</center>\n",
               lang("showing %1 - %2 of %3",($start + 1),
                    ($start + $phpgw_info["user"]["preferences"]["common"]["maxmatchs"]),
                    $phpgw->db->f(0)));
    }
    else
    {
        printf("<center>%s</center>\n", lang("showing %1",$phpgw->db->f(0)));
    }
    
    printf("<center>\n");
   
    $phpgw->nextmatchs->show_tpl("publisher.php",$start,$phpgw->db->f(0), "",
                             "75%", $phpgw_info["theme"]["th_bg"],0,0);
    printf("</center>\n");

    printf("<table border=\"0\" width=\"75%%\" align=\"center\">\n");
    printf("  <tr>\n");
    printf("    <td colspan=3>&nbsp;</td>\n");
    printf("  </tr>\n");
    printf("  <tr bgcolor=\"%s\">\n", $phpgw_info["theme"]["th_bg"]);
    printf("    <td>%s</td>\n",
           $phpgw->nextmatchs->show_sort_order($sort,"publisher_name",$order,"publisher.php",
                                               lang("Name")));
    printf("    <td>%s</td>\n", lang("Edit"));
    printf("    <td>%s</td>\n", lang("Delete"));
    printf("  </tr>\n");
    
    $phpgw->db->query("select * from phpgw_mediadb_publisher $ordermethod");
   
    if (!$query)
    {
        $phpgw->db->query("select * from phpgw_mediadb_publisher "
                          .$ordermethod
                          ." ".$limit);
    }
    else
    {
        $phpgw->db->query("select * from phpgw_mediadb_publisher "
                          ."WHERE publisher_name like '%$query%' "
                          .$ordermethod
                          ." ".$limit);
    }
    
    while ($phpgw->db->next_record()) 
    {
        $tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);
        
        $name = $phpgw->db->f("publisher_name");
        if (! $name)
        {
            $name = "&nbsp;";
        }
        
        printf("  <tr bgcolor=$tr_color>\n");
        printf("    <td>%s</td>\n", $name);
        printf("    <td width=5%%><a href=\"%s\">%s</a></td>\n",
               $phpgw->link("/mediadb/publisher.php",
                            "con=" . urlencode($phpgw->db->f("publisher_id"))
                            ."&act=edit"
                            ."&start=$start&order=$order&filter=$filter"
                            ."&sort=$sort"
                            ."&query=".urlencode($query)),
               lang("Edit"));
        printf("    <td width=5%%><a href=\"%s\">%s</a></td>\n",
               $phpgw->link("/mediadb/publisher.php",
                            "con=" . urlencode($phpgw->db->f("publisher_id"))
                            ."&act=delete"
                            ."&start=$start&order=$order&filter=$filter"
                            ."&sort=$sort"
                            ."&query=".urlencode($query)),
               lang("Delete"));
        
        printf("  </tr>\n");
    }
    printf("</table>\n");
}

function add_publisher_entry($order, $sort, $filter, $start, $query)
{
    global $phpgw, $phpgw_info;

    $color = $phpgw_info["theme"]["th_bg"];
    
    printf("<form method=POST action=\"%s\">\n",
           $phpgw->link("/mediadb/publisher.php",
                        "act=add"
                        ."&start=$start&order=$order&filter=$filter"
                        ."&sort=$sort"
                        ."&query=".urlencode($query)));
    printf("  <table border=\"0\" cellpadding=\"0\""
           . " cellspacing=\"0\" width=75%% align=center>\n");
    printf("    <tr bgcolor=%s>\n", $color);
    printf("      <td align=left width=15%%>\n");
    printf("        <input type=\"submit\" name=\"submit\" value=\"%s\">\n",lang("Add"));
    printf("      </td>\n");
    printf("      <td width=10%%>%s:</td>\n",lang("Publisher"));
    printf("      <td>\n");
    printf("        <input type=\"text\" name=\"publisher_name\""
           ." value=\"%s\" maxlength=30>\n", $afname);
    printf("      </td>\n");
    printf("    </tr>\n");
    printf("  </table>\n");
    printf("</form>\n");
}

function modify_publisher_entry($con, $act, $order, $sort, $filter, $start, $query)
{
    global $phpgw, $phpgw_info;

    $phpgw->db->query("select * from phpgw_mediadb_publisher where publisher_id=$con");
    $phpgw->db->next_record();
    $afname = $phpgw->db->f("publisher_name");

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
           $phpgw->link("/mediadb/publisher.php",
                        "act=$act"
                        ."&start=$start&order=$order&filter=$filter"
                        ."&sort=$sort"
                        ."&query=".urlencode($query)));
    printf("<input type=\"hidden\" name=\"publisher_id\" value=\"%s\">\n",$con);
    printf("  <table border=\"0\" cellpadding=\"0\""
           . " cellspacing=\"0\" width=75%% align=center>\n");
    printf("    <tr bgcolor=%s>\n",$color);
    printf("      <td align=left width=15%%>\n");
    printf("        <input type=\"submit\" name=\"submit\" value=\"%s\">\n",lang($act));
    printf("      </td>\n");
    printf("      <td width=10%%>%s:</td>\n",lang("Publisher"));
    printf("      <td>\n");
    printf("        <input type=\"text\" name=\"publisher_name\""
           ." value=\"%s\" maxlength=30>\n", $afname);
    printf("      </td>\n");
    printf("    </tr>\n");
    printf("  </table>\n");
    printf("</form>\n");
}
?>
