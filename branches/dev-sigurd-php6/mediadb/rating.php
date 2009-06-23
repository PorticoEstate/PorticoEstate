<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB Rating Admin                                      *
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
    include("inc/rating.inc.php");
    include("inc/functions.inc.php");

    printf("<center><h2>%s</h2></center>\n<p>\n", lang("Ratings"));

    switch($act)
    {
      case "add":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_rating");
            $phpgw->db->query("insert into phpgw_mediadb_rating (rating_desc, rating_efiles, "
                              ."rating_pages, rating_regions, rating_hscores, cat_id)"
                              ."values ('"
                              .$rating_desc."','"
                              .$rating_efiles."','"
                              .$rating_pages."','"
                              .$rating_regions."','"
                              .$rating_hscores."','"
                              .$cat_id."')");
            $phpgw->db->unlock();
        }
        list_catid("rating", $order, $sort, $filter, $start, $query, $qfield);
        add_rating_entry($order, $sort, $filter, $start, $query, $qfield);
        break;
      case "edit":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_rating");
            $phpgw->db->query("update phpgw_mediadb_rating set "
                              ."rating_desc='".$rating_desc."',"
                              ."rating_efiles='".$rating_efiles."',"
                              ."rating_pages='".$rating_pages."',"
                              ."rating_regions='".$rating_regions."',"
                              ."rating_hscores='".$rating_hscores."',"
                              ."cat_id='".$cat_id."' "
                              ."where rating_id='".$rating_id."'");
            $phpgw->db->unlock();

            list_catid("rating", $order, $sort, $filter, $start, $query, $qfield);
            add_rating_entry($order, $sort, $filter, $start, $query, $qfield);
        }
        else
        {
            list_catid("rating", $order, $sort, $filter, $start, $query, $qfield);
            add_rating_entry($order, $sort, $filter, $start, $query, $qfield);
            modify_rating_entry($con,$act, $order, $sort, $filter, $start, $query, $qfield);
        }
        break;
      case "delete":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_rating");
            $phpgw->db->query("delete from phpgw_mediadb_rating where rating_id='"
                              .$rating_id."'");
            $phpgw->db->unlock();

            list_catid("rating", $order, $sort, $filter, $start, $query, $qfield);
            add_rating_entry($order, $sort, $filter, $start, $query, $qfield);
        }
        else
        {
            list_catid("rating", $order, $sort, $filter, $start, $query, $qfield);
            add_rating_entry($order, $sort, $filter, $start, $query, $qfield);
            modify_rating_entry($con,$act, $order, $sort, $filter, $start, $query, $qfield);
        }
        break;
      default:
        list_catid("rating",$order, $sort, $filter, $start, $query, $qfield);
        add_rating_entry($order, $sort, $filter, $start, $query, $qfield);
        break;
    }

    $phpgw->common->phpgw_footer();
}

?>
