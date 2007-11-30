<?php
  /**************************************************************************\
  * phpGroupWare - Polls                                                     *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_mainscreen.inc.php 8700 2001-12-21 14:59:46Z milosch $ */

	if (! $inindex)
	{
		if (! $GLOBALS['phpgw_info']['user']['preferences']['polls']['show_on_mainscreen'])
		{
			return False;
		}
	}

	display_poll();
?>
