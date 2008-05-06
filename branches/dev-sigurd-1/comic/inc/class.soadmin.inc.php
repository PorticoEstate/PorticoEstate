<?php

/*************************************************************************\
* Daily Comics (phpGroupWare application)                                 *
* http://www.phpgroupware.org                                             *
* This file is written by: Sam Wynn <neotexan@wynnsite.com>               *
*                          Rick Bakker <r.bakker@linvision.com>           *
* --------------------------------------------                            *
* This program is free software; you can redistribute it and/or modify it *
* under the terms of the GNU General Public License as published by the   *
* Free Software Foundation; either version 2 of the License, or (at your  *
* option) any later version.                                              *
\*************************************************************************/

/* $Id$ */

class soadmin
{
        function admin_global_options_data()
        {
		global $phpgw;

		$phpgw->db->query("select * from phpgw_comic_admin");
		if (!$phpgw->db->num_rows())
		{
			$phpgw->db->query("insert into phpgw_comic_admin values (0,0,0,0,120000)");
			$phpgw->db->query("select * from phpgw_comic_admin");
		}
		$phpgw->db->next_record();
    
		$field['image_source']     = $phpgw->db->f('admin_imgsrc');
		$field['censor_level']     = $phpgw->db->f('admin_censorlvl');
		$field['override_enabled'] = $phpgw->db->f('admin_coverride');
		$field['remote_enabled']   = $phpgw->db->f('admin_rmtenabled');
		$field['filesize']         = $phpgw->db->f('admin_filesize');

		return ($field);
	}

	function update_global_options($field)
	{
		global $phpgw;

		$phpgw->db->lock('phpgw_comic_admin');
		$phpgw->db->query("update phpgw_comic_admin set "
			."admin_imgsrc='".$field['image_source']."', "
			."admin_rmtenabled='".$field['remote_enabled']."', "
			."admin_censorlvl='".$field['censor_level']."', "
			."admin_coverride='".$field['override_enabled']."', "
			."admin_filesize='".$field['filesize']."'");
		$phpgw->db->unlock();
	}
}

?>
