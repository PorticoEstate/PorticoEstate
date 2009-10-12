<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB Category Functions                                *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

function list_category($order, $sort, $filter, $start, $query, $qfield)
{
   global $phpgw, $phpgw_info;

   if ($order)
   {
      $ordermethod = "order by $order $sort";
   }
   else
   {
      $ordermethod = "order by cat_id asc";
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
   
   if (! $qfield)
   {
       $qfield = "cat_name";
   }

   if (! $query)
   {
       $phpgw->db->query("select count(*) from phpgw_mediadb_cat "
                         .$ordermethod);
   }
   else
   {
       $phpgw->db->query("select count(*) from phpgw_mediadb_cat "
                         ."WHERE $qfield like '%$query%' "
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
   
   $phpgw->nextmatchs->show_tpl("/mediadb/category.php",$start,$phpgw->db->f(0), "",
                            "75%", $phpgw_info["theme"]["th_bg"],0,0);
   printf("</center>\n");

   printf("<table border=\"0\" width=\"75%%\" align=\"center\">\n");
   printf("  <tr>\n");
   printf("    <td colspan=5>&nbsp;</td>\n");
   printf("  </tr>\n");
   printf("  <tr bgcolor=\"%s\">\n", $phpgw_info["theme"]["th_bg"]);
   printf("    <td>%s</td>\n",
          $phpgw->nextmatchs->show_sort_order($sort,"cat_id",$order,"category.php",
                                              lang("id")));
   printf("    <td>%s</td>\n",
          $phpgw->nextmatchs->show_sort_order($sort,"cat_name",$order,"category.php",
                                              lang("category")));
   printf("    <td>%s</td>\n", lang("Edit"));
   printf("    <td>%s</td>\n", lang("Delete"));
   printf("    <td>%s</td>\n", lang("Enabled"));
   printf("  </tr>\n");

   if (!$query)
   {
       $phpgw->db->query("select * from phpgw_mediadb_cat "
                         .$ordermethod
                         ." ".$limit);
   }
   else
   {
       $phpgw->db->query("select * from phpgw_mediadb_cat "
                         ."WHERE $qfield like '%$query%' "
                         .$ordermethod
                         ." ".$limit);
   }

   while ($phpgw->db->next_record()) 
   {
      $tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);
      $name = $phpgw->db->f("cat_name");

      if (! $name)
      {
         $name = "&nbsp;";
      }

      printf("  <tr bgcolor=$tr_color>\n");
      printf("    <td>%s</td>\n", $phpgw->db->f("cat_id"));
      printf("    <td>%s</td>\n", lang($name));
      printf("    <td width=5%%><a href=\"%s\">%s</a></td>\n",
             $phpgw->link("/mediadb/category.php",
                          "con=" . urlencode($phpgw->db->f("cat_id"))
                          ."&act=edit"
                          ."&start=$start&order=$order&filter=$filter&qfield=$qfield"
                          ."&sort=$sort"
                          ."&query=".urlencode($query)),
             lang("Edit"));
      if ($phpgw->db->f("cat_id") != "1")
      {
         printf("    <td width=5%%><a href=\"%s\">%s</a></td>\n",
                $phpgw->link("/mediadb/category.php",
                             "con=" . urlencode($phpgw->db->f("cat_id"))
                             ."&act=delete"
                             ."&start=$start&order=$order&filter=$filter&qfield=$qfield"
                             ."&sort=$sort"
                             ."&query=".urlencode($query)),
                lang("Delete"));
      }
      else
      {
         printf("    <td width=5%%>&nbsp;</td>\n");
      }
      printf("    <td width=5%%>");

      switch($phpgw->db->f("cat_enabled"))
      {  
        case 1:
          printf("%s</td>\n", lang("Yes"));
          break;
        case 2:
          printf("%s</td>\n", lang("Hidden"));
          break;
        default:
          printf("<b>%s</b></td>\n", lang("No"));
          break;
      }
      printf("  </tr>\n");
   }
   printf("</table>\n");
}

function add_category_entry($order, $sort, $filter, $start, $query, $qfield)
{
    global $phpgw, $phpgw_info;

    $phpgw->db->query("select * from phpgw_mediadb_cat where cat_id=1");
    $phpgw->db->next_record();

    $cat_name = $phpgw->db->f("cat_name");
    $fields   = explode(",", $phpgw->db->f("cat_fname"));
    $fenable  = explode(",", $phpgw->db->f("cat_fenabled"));
    $fwidth   = explode(",", $phpgw->db->f("cat_fwidth"));
    $fsortby  = explode(",", $phpgw->db->f("cat_fsort"));

    $indexcount = count($fields);
    for ($index = 0; $index < $indexcount; $index++)
    {
        $tname = explode(".",$fields[$index]);
        if (count($tname) > 1)
        {
            $fields[$index] = $tname[1];
        }
        else
        {
            $fields[$index] = $tname[0];
        }
    }

    $color = $phpgw_info["theme"]["th_bg"];
    
    printf("<form method=POST action=\"%s\">\n",
           $phpgw->link("/mediadb/category.php",
                        "act=add"
                        ."&start=$start&order=$order&filter=$filter&qfield=$qfield"
                        ."&sort=$sort"
                        ."&query=".urlencode($query)));
    printf("  <table border=\"0\" cellpadding=\"0\""
           . " cellspacing=\"0\" width=75%% align=center>\n");
    printf("    <tr bgcolor=%s>\n", $color);
    printf("      <td align=left width=15%%>\n");
    printf("        <input type=\"submit\" name=\"submit\" value=\"%s\">\n",lang("Add"));
    printf("      </td>\n");
    printf("      <td width=10%%>%s:</td>\n",lang("Category"));
    printf("      <td colspan=2>\n");
    printf("        <input type=\"text\" name=\"cat_name\" maxlength=30>\n");
    printf("      </td>\n");
    printf("      <td valign=\"center\" align=\"right\">%s\n",lang("Enabled"));
    printf("        <input type=\"checkbox\" checked name=\"cat_enabled\" value=\"1\">\n");
    printf("      </td>\n");
    printf("    </tr>\n");

    cat_row($con,  5,  0, $color, $fields, $fenable, $fwidth, $fsortby);
    cat_row($con, 10,  5, $color, $fields, $fenable, $fwidth, $fsortby);
    cat_row($con, 13, 10, $color, $fields, $fenable, $fwidth, $fsortby);

    printf("  </table>\n");
    printf("</form>\n");
}

function modify_category_entry($con, $act, $order, $sort, $filter, $start, $query, $qfield)
{
    global $phpgw, $phpgw_info;

    $phpgw->db->query("select * from phpgw_mediadb_cat where cat_id=$con");
    $phpgw->db->next_record();

    $cat_name = $phpgw->db->f("cat_name");
    $fields   = explode(",", $phpgw->db->f("cat_fname"));
    $fenable  = explode(",", $phpgw->db->f("cat_fenabled"));
    $fwidth   = explode(",", $phpgw->db->f("cat_fwidth"));
    $fsortby  = explode(",", $phpgw->db->f("cat_fsort"));
    $indexlimit = count($fields);
    
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
           $phpgw->link("/mediadb/category.php",
                        "act=$act"
                        ."&start=$start&order=$order&filter=$filter&qfield=$qfield"
                        ."&sort=$sort"
                        ."&query=".urlencode($query)));
    printf("<input type=\"hidden\" name=\"cat_id\" value=\"%s\">\n",$con);
    printf("  <table border=\"0\" cellpadding=\"0\""
           . " cellspacing=\"0\" width=75%% align=center>\n");
    printf("    <tr bgcolor=%s>\n",$color);
    printf("      <td align=left width=15%%>\n");
    printf("        <input type=\"submit\" name=\"submit\" value=\"%s\">\n",lang($act));
    printf("      </td>\n");
    printf("      <td width=10%%>%s:</td>\n",lang("Category"));
    printf("      <td colspan=2>\n");
    printf("        <input type=\"text\" name=\"cat_name\""
           ." value=\"%s\" maxlength=30>\n", $cat_name);
    printf("      </td>\n");
    if ($con != 1)
    {
        printf("      <td valign=\"center\" align=\"right\">%s\n",
               lang("Enabled"));
        printf("        <input type=\"checkbox\" ");
        if ($phpgw->db->f("cat_enabled") == 1)
        {
            printf("checked ");
        }
        printf("name=\"cat_enabled\" value=\"1\">\n");
    }
    else
    {
        printf("      <td valign=\"center\" align=\"right\">%s\n",
               lang("Hidden"));
        printf("        <input type=\"hidden\" name=\"cat_enabled\" value=2>\n");
    }
    
    printf("      </td>\n");
    printf("    </tr>\n");

    cat_row($con,  5,  0, $color, $fields, $fenable, $fwidth, $fsortby);
    cat_row($con, 10,  5, $color, $fields, $fenable, $fwidth, $fsortby);
    cat_row($con, 13, 10, $color, $fields, $fenable, $fwidth, $fsortby);
    
    printf("  </table>\n");
    printf("</form>\n");
}

function cat_row($con, $max, $min, $color, $fields, $fenable, $fwidth, $fsortby)
{
    global $phpgw, $phpgw_info;

    // field names part 1
    printf("    <tr>\n");
    for($index=$min; $index < $max; $index++)
    {  
        $td_color = $phpgw->nextmatchs->alternate_row_color($td_color);
        printf("    <td align=center bgcolor=$td_color>\n");
        if ($con != 1)
        {
            printf("        <input type=\"text\" name=\"cat_fname[$index]\""
                   ." value=\"%s\" size=10 maxlength=18>\n", $fields[$index]);
        }
        else
        {
            printf("        <input type=\"hidden\" name=\"cat_fname[$index]\""
                   ." value=\"%s\" size=10 maxlength=18>\n", $fields[$index]);
            $tname = explode(".",$fields[$index]);
            if (count($tname) > 1)
            {
                printf("%s\n", $tname[1]);
            }
            else
            {
                printf("%s\n", $tname[0]);
            }
        }
        
        printf("    </td>\n");
    }
    printf("    </tr>\n");

    $td_color = "";

    // field width part 1
    printf("    <tr>\n");
    for($index=$min; $index < $max; $index++)
    {  
        $td_color = $phpgw->nextmatchs->alternate_row_color($td_color);
        printf("    <td align=center bgcolor=$td_color>\n");
        printf("        <input type=\"text\" name=\"cat_fwidth[$index]\""
               ." value=\"%s\" size=2 maxlength=2>\n", $fwidth[$index]);
        printf("%s", lang("width"));
        printf("    </td>\n");
    }
    printf("    </tr>\n");

    $td_color = "";

    // field enabled part 1
    printf("    <tr>\n");
    for($index=$min; $index < $max; $index++)
    {  
        $td_color = $phpgw->nextmatchs->alternate_row_color($td_color);
        printf("    <td align=center bgcolor=$td_color>\n");
        printf("      <select name=\"cat_fenabled[$index]\" size=1>");
        printf("        <option value=0 ");
        if ($fenable[$index] == 0)
        {
            printf("selected ");
        }
        printf(">%s</option>\n", lang("disabled"));
        printf("        <option value=1 ");
        if ($fenable[$index] == 1)
        {
            printf("selected ");
        }
        printf(">%s</option>\n", lang("enabled"));
        printf("      </select>\n");
        printf("    </td>\n");
    }
    printf("    </tr>\n");

    $td_color = "";

    // field sorted part 1
    printf("    <tr>\n");
    for($index=$min; $index < $max; $index++)
    {  
        $td_color = $phpgw->nextmatchs->alternate_row_color($td_color);
        printf("    <td align=center bgcolor=$td_color>\n");
        printf("      <select name=\"cat_fsort[$index]\" size=1>");
        printf("        <option value=0 ");
        if ($fsortby[$index] == 0)
        {
            printf("selected ");
        }
        printf(">%s</option>\n", lang("no sort"));
        printf("        <option value=1 ");
        if ($fsortby[$index] == 1)
        {
            printf("selected ");
        }
        printf(">%s</option>\n", lang("sort"));
        printf("      </select>\n");
        printf("    </td>\n");
    }
    printf("    </tr>\n");

    printf("    <tr bgcolor=\"%s\">\n",$color);
    printf("      <td colspan=%s>&nbsp;</td>\n", $max-$min);
    printf("    </tr>\n");
}
?>
