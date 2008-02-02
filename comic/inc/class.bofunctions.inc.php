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

class bofunctions
{
	var $so;

	function bofunctions()
	{
		$this->so = CreateObject('comic.sofunctions');
	}

	function select_box($var) 
	{
		switch ($var)
		{
			case g_censor_level:
				$array = array(
					0 => 'G',
					1 => 'PG',
					2 => 'R'
				);
				break;
			case g_image_source:
				$array = array(
					0 => 'Remote',
					1 => 'Local'
				);
				break;
			default:
				$array[0] = lang('no_entries');
		}
		return ($array);
	}

	function row_color()
	{
		global $phpgw_info;
		static $color;

		if ($color == $phpgw_info['theme']['row_off'])
		{
			$color = $phpgw_info['theme']['row_on'];
		}
		else
		{
			$color = $phpgw_info['theme']['row_off'];
		}
		return ($color);
	}
}

?>
