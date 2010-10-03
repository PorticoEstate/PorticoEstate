<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB Feature Admin                                     *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

    $phpgw_info["flags"] = array("currentapp" => "admin",
                                 "enable_nextmatchs_class" => True);
   include("../header.inc.php");

   if ($phpgw_info["user"]["apps"]["admin"])
   {
      switch($act)
      {
        case "add":
          break;
        case "edit":
          break;
        case "delete":
          break;
        default:
          list_feature($order, $sort, $filter, $start, $query);
          break;
      }
   }
   $phpgw->common->phpgw_footer();
?>
