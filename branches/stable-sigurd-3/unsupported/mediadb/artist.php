<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB Artist Admin                                      *
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
    include("inc/artist.inc.php");

    printf("<center><h2>%s</h2></center>\n<p>\n", lang("Artists"));

    switch($act)
    {
      case "edit":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_artist");
            $phpgw->db->query("update phpgw_mediadb_artist set "
                              ."artist_fname='".$artist_fname."',"
                              ."artist_lname='".$artist_lname."' "
                              ."where artist_id='".$artist_id."'");
            $phpgw->db->unlock();

            list_artist($order, $sort, $filter, $start, $query, $qfield);
            add_artist_entry($order, $sort, $filter, $start, $query, $qfield);
        }
        else
        {
            list_artist($order, $sort, $filter, $start, $query, $qfield);
            add_artist_entry($order, $sort, $filter, $start, $query, $qfield);
            modify_artist_entry($con,$act, $order, $sort, $filter, $start, $query, $qfield);
        }
        break;
      case "delete":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_artist");
            $phpgw->db->query("delete from phpgw_mediadb_artist where artist_id='"
                              .$artist_id."'");
            $phpgw->db->unlock();

            list_artist($order, $sort, $filter, $start, $query, $qfield);
            add_artist_entry($order, $sort, $filter, $start, $query, $qfield);
        }
        else
        {
            list_artist($order, $sort, $filter, $start, $query, $qfield);
            add_artist_entry($order, $sort, $filter, $start, $query, $qfield);
            modify_artist_entry($con,$act, $order, $sort, $filter, $start, $query, $qfield);
        }
        break;
      case "add":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_artist");
            $phpgw->db->query("insert into phpgw_mediadb_artist (artist_fname, artist_lname)"
                              ."values ('"
                              .$artist_fname."','"
                              .$artist_lname."')");
            $phpgw->db->unlock();
        }
        list_artist($order, $sort, $filter, $start, $query, $qfield);
        add_artist_entry($order, $sort, $filter, $start, $query, $qfield);
        break;
      default:
        list_artist($order, $sort, $filter, $start, $query, $qfield);
        add_artist_entry($order, $sort, $filter, $start, $query, $qfield);
        break;
    }

    $phpgw->common->phpgw_footer();
}

?>
