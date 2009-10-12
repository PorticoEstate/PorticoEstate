<?php
	/**************************************************************************\
	* phpGroupWare Application - phonelog                                      *
	* http://www.phpgroupware.org                                              *
	* Written by Mathieu van Loon <mathieu@playcollective.com>                 *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	function printSelectList($inSelected, $inValues)
	{
		$str = '';
		for($i=0;$i<sizeof($inValues);$i++)
		{
			if(!strcmp($inSelected,$inValues[$i][0])) // String safe comparison
			{
				$str .= "<option value=\"".$inValues[$i][0]."\" SELECTED>".$inValues[$i][1]."</option>\n";
			}
			else
			{
				$str .= "<option value=\"".$inValues[$i][0]."\">".$inValues[$i][1]."</option>\n";
			}
		}
		return $str;
	}
?>
