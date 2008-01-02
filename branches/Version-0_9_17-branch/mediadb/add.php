<?php
  /**************************************************************************\
  * phpGroupWare - MediaDB                                                   *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

    /* $Id: add.php 6025 2001-06-17 21:21:06Z milosch $ */
{
    
    $phpgw_info["flags"] = array("nonavbar" => True,
                                 "currentapp" => "mediadb");
    
    include("../header.inc.php");

    {
        if (!isset($phase))
        {
            $phase = 1;
        }
            
        printf("<center>\n");
        printf("  <h2>\n");
        
        printf("%s %s %s %s %s",
               lang($cat),
               lang("Media"),
               lang("Wizard"),
               lang("Phase"),
               $phase);

        printf("  </h2>\n");
        printf("</center>\n");
        
        // body
        switch ($phase)
       {
         // input
         case 1:
           add_media_phase1($cat);
           break;
         // manual input
         case 2:
           add_media_phase2($raw);
           break;
         // display
         case 3:
           break;
       }
   }

    $phpgw->common->phpgw_footer();
}

?>
