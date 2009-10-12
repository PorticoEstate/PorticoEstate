<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB Publisher Admin                                   *
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
    include("inc/publisher.inc.php");

    printf("<center><h2>%s</h2></center>\n<p>\n", lang("Publishers"));

    switch($act)
    {
      case "edit":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_publisher");
            $phpgw->db->query("update phpgw_mediadb_publisher set "
                              ."publisher_name='".$publisher_name."' "
                              ."where publisher_id='".$publisher_id."'");
            $phpgw->db->unlock();

            list_publisher($order, $sort, $filter, $start, $query);
            add_publisher_entry($order, $sort, $filter, $start, $query);
        }
        else
        {
            list_publisher($order, $sort, $filter, $start, $query);
            add_publisher_entry($order, $sort, $filter, $start, $query);
            modify_publisher_entry($con, $act, $order, $sort, $filter, $start, $query);
        }
        break;
      case "delete":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_publisher");
            $phpgw->db->query("delete from phpgw_mediadb_publisher where publisher_id='"
                              .$publisher_id."'");
            $phpgw->db->unlock();

            list_publisher($order, $sort, $filter, $start, $query);
            add_publisher_entry($order, $sort, $filter, $start, $query);
        }
        else
        {
            list_publisher($order, $sort, $filter, $start, $query);
            add_publisher_entry($order, $sort, $filter, $start, $query);
            modify_publisher_entry($con, $act, $order, $sort, $filter, $start, $query);
        }
        break;
      case "add":
        if ($submit)
        {
            $phpgw->db->lock("phpgw_mediadb_publisher");
            $phpgw->db->query("insert into phpgw_mediadb_publisher (publisher_name)"
                              ."values ('"
                              .$publisher_name."')");
            $phpgw->db->unlock();
        }
        list_publisher($order, $sort, $filter, $start, $query);
        add_publisher_entry($order, $sort, $filter, $start, $query);
        break;
      default:
        list_publisher($order, $sort, $filter, $start, $query);
        add_publisher_entry($order, $sort, $filter, $start, $query);
        break;
    }

    $phpgw->common->phpgw_footer();
}

?>
