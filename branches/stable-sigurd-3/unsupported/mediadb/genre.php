<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB Genre Admin                                       *
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
    include("inc/genre.inc.php");
    include("inc/functions.inc.php");

    printf("<center><h2>%s</h2></center>\n<p>\n", lang("Genres"));

    switch($act)
    {
      case "add":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_genre");
            $phpgw->db->query("insert into phpgw_mediadb_genre (genre_desc, cat_id)"
                              ."values ('"
                              .$genre_desc."','"
                              .$cat_id."')");
            $phpgw->db->unlock();
        }
        list_catid("genre", $order, $sort, $filter, $start, $query, $qfield);
        add_genre_entry($order, $sort, $filter, $start, $query, $qfield);
        break;
      case "edit":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_genre");
            $phpgw->db->query("update phpgw_mediadb_genre set "
                              ."genre_desc='".$genre_desc."',"
                              ."cat_id='".$cat_id."' "
                              ."where genre_id='".$genre_id."'");
            $phpgw->db->unlock();

            list_catid("genre", $order, $sort, $filter, $start, $query, $qfield);
            add_genre_entry($order, $sort, $filter, $start, $query, $qfield);
        }
        else
        {
            list_catid("genre", $order, $sort, $filter, $start, $query, $qfield);
            add_genre_entry($order, $sort, $filter, $start, $query, $qfield);
            modify_genre_entry($con,$act, $order, $sort, $filter, $start, $query, $qfield);
        }
        break;
      case "delete":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_genre");
            $phpgw->db->query("delete from phpgw_mediadb_genre where genre_id='"
                              .$genre_id."'");
            $phpgw->db->unlock();

            list_catid("genre", $order, $sort, $filter, $start, $query, $qfield);
            add_genre_entry($order, $sort, $filter, $start, $query, $qfield);
        }
        else
        {
            list_catid("genre", $order, $sort, $filter, $start, $query, $qfield);
            add_genre_entry($order, $sort, $filter, $start, $query, $qfield);
            modify_genre_entry($con,$act, $order, $sort, $filter, $start, $query, $qfield);
        }
        break;
      default:
        list_catid("genre",$order, $sort, $filter, $start, $query, $qfield);
        add_genre_entry($order, $sort, $filter, $start, $query, $qfield);
        break;
    }

    $phpgw->common->phpgw_footer();
}

?>
