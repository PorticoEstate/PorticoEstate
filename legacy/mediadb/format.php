<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB Format Admin                                      *
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
    include("inc/format.inc.php");
    include("inc/functions.inc.php");

    printf("<center><h2>%s</h2></center>\n<p>\n", lang("Formats"));

    switch($act)
    {
      case "add":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_format");
            $phpgw->db->query("insert into phpgw_mediadb_format (format_desc, format_efiles, "
                              ."format_pages, format_regions, format_hscores, cat_id)"
                              ."values ('"
                              .$format_desc."','"
                              .$format_efiles."','"
                              .$format_pages."','"
                              .$format_regions."','"
                              .$format_hscores."','"
                              .$cat_id."')");
            $phpgw->db->unlock();
        }
        list_catid("format", $order, $sort, $filter, $start, $query, $qfield);
        add_format_entry($order, $sort, $filter, $start, $query, $qfield);
        break;
      case "edit":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_format");
            $phpgw->db->query("update phpgw_mediadb_format set "
                              ."format_desc='".$format_desc."',"
                              ."format_efiles='".$format_efiles."',"
                              ."format_pages='".$format_pages."',"
                              ."format_regions='".$format_regions."',"
                              ."format_hscores='".$format_hscores."',"
                              ."cat_id='".$cat_id."' "
                              ."where format_id='".$format_id."'");
            $phpgw->db->unlock();

            list_catid("format", $order, $sort, $filter, $start, $query, $qfield);
            add_format_entry($order, $sort, $filter, $start, $query, $qfield);
        }
        else
        {
            list_catid("format", $order, $sort, $filter, $start, $query, $qfield);
            add_format_entry($order, $sort, $filter, $start, $query, $qfield);
            modify_format_entry($con,$act, $order, $sort, $filter, $start, $query, $qfield);
        }
        break;
      case "delete":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_format");
            $phpgw->db->query("delete from phpgw_mediadb_format where format_id='"
                              .$format_id."'");
            $phpgw->db->unlock();

            list_catid("format", $order, $sort, $filter, $start, $query, $qfield);
            add_format_entry($order, $sort, $filter, $start, $query, $qfield);
        }
        else
        {
            list_catid("format", $order, $sort, $filter, $start, $query, $qfield);
            add_format_entry($order, $sort, $filter, $start, $query, $qfield);
            modify_format_entry($con,$act, $order, $sort, $filter, $start, $query, $qfield);
        }
        break;
      default:
        list_catid("format",$order, $sort, $filter, $start, $query, $qfield);
        add_format_entry($order, $sort, $filter, $start, $query, $qfield);
        break;
    }

    $phpgw->common->phpgw_footer();
}

?>
