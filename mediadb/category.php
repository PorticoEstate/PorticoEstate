<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB Category Admin/Preferences                        *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

    /* $Id$ */
{
    
    $phpgw_info["flags"] = array("currentapp" => "admin",
                                 "enable_nextmatchs_class" => True);

    include("../header.inc.php");
    include("inc/category.inc.php");
    
    printf("<center><h2>%s</h2></center>\n<p>\n", lang("Categories"));

    switch($act)
    {
      case "edit":
        if ($submit)
        {
            $cat_fname = implode($cat_fname,",");
            $cat_fenabled = implode($cat_fenabled,",");
            $cat_fwidth = implode($cat_fwidth,",");
            $cat_fsort = implode($cat_fsort,",");
            
            $phpgw->db->lock("phpgw_mediadb_cat");
            $phpgw->db->query("update phpgw_mediadb_cat set "
                              ."cat_name='".$cat_name."',"
                              ."cat_enabled='".$cat_enabled."',"
                              ."cat_fname='".$cat_fname."',"
                              ."cat_fenabled='".$cat_fenabled."',"
                              ."cat_fwidth='".$cat_fwidth."',"
                              ."cat_fsort='".$cat_fsort."' "
                              ."where cat_id='".$cat_id."'");
            $phpgw->db->unlock();

            list_category($order, $sort, $filter, $start, $query, $qfield);
            add_category_entry($order, $sort, $filter, $start, $query, $qfield);
        }
        else
        {
            list_category($order, $sort, $filter, $start, $query, $qfield);
            modify_category_entry($con,$act, $order, $sort, $filter, $start, $query, $qfield);
        }
        break;
      case "delete":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_cat");
            $phpgw->db->query("delete from phpgw_mediadb_cat where cat_id='"
                              .$cat_id."'");
            $phpgw->db->unlock();

            list_category($order, $sort, $filter, $start, $query, $qfield);
            add_category_entry($order, $sort, $filter, $start, $query, $qfield);
        }
        else
        {
            list_category($order, $sort, $filter, $start, $query, $qfield);
            modify_category_entry($con,$act, $order, $sort, $filter, $start, $query, $qfield);
        }
        break;
      case "add":
        if ($submit)
        {
            $cat_fname = implode($cat_fname,",");
            $cat_fenabled = implode($cat_fenabled,",");
            $cat_fwidth = implode($cat_fwidth,",");
            $cat_fsort = implode($cat_fsort,",");

            $phpgw->db->lock("phpgw_mediadb_cat");
            $phpgw->db->query("insert into phpgw_mediadb_cat (cat_name, cat_enabled, "
                              ."cat_fname, cat_fenabled, cat_fwidth, "
                              ."cat_fsort) "
                              ."values ('"
                              .$cat_name."','"
                              .$cat_enabled."','"
                              .$cat_fname."','"
                              .$cat_fenabled."','"
                              .$cat_fwidth."','"
                              .$cat_fsort."')");
            $phpgw->db->unlock();
        }
        list_category($order, $sort, $filter, $start, $query, $qfield);
        add_category_entry($order, $sort, $filter, $start, $query, $qfield);
        break;
      default:
        list_category($order, $sort, $filter, $start, $query, $qfield);
        add_category_entry($order, $sort, $filter, $start, $query, $qfield);
        break;
    }

    $phpgw->common->phpgw_footer();
}

?>
