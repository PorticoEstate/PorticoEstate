<?php
    /**************************************************************************\
    * phpGroupWare - Daily Comic Admin Link Data                               *
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
    $phpgw_info["flags"] = array("currentapp"   => "comic", 
                                 "noheader"     => True,
                                 "nonavbar"     => True,
                                 "nofooter"     => True,
                                 "admin_header" => True);
    
    include("../header.inc.php");

    if ($confirm)
    {
        $phpgw->db->query("update phpgw_comic_data "
                          ."set data_imageurl='', data_date='' "
                          ."where data_date>'0'");
        
        Header("Location: ".$phpgw->link("/admin/index.php"));
    }
    else
    {
        $phpgw->common->phpgw_header();
        echo parse_navbar();

        $phpgw->template = new Template($phpgw->common->get_tpl_dir("comic"));
        $phpgw->template->set_file(array("comic_reset" => "question.tpl"));
        
        $phpgw->template->
            set_var("question",
                    lang("Are you sure you want to reset all "
                         ."comic dates and resolved image urls?"));
        
        $nolinkf = $phpgw->link("/admin/index.php");
        $nolink = '<a href="' . $nolinkf . '">' . lang("No") ."</a>";
        $phpgw->template->set_var("nolink",$nolink);
        
        $yeslinkf = $phpgw->link("/comic/admin_comics_reset.php",
                                 "confirm=True");
        $yeslink = '<a href="' . $yeslinkf . '">' . lang("Yes") ."</a>";
        $phpgw->template->set_var("yeslink",$yeslink);

        $phpgw->template->set_var("th_bg",$phpgw_info["theme"]["th_bg"]);
        $phpgw->template->set_var("yes_color",$phpgw_info["theme"]["row_on"]);
        $phpgw->template->set_var("no_color",$phpgw_info["theme"]["bg07"]);

        $phpgw->template->pfp("out","comic_reset");
        
        $phpgw->common->phpgw_footer();
        echo parse_navbar_end();
    }
}

?>
