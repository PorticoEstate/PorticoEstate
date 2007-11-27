<?php
    /**************************************************************************\
    * phpGroupWare - Stock Quotes                                              *
    * http://www.phpgroupware.org                                              *
    * --------------------------------------------                             *
    * This program is free software; you can redistribute it and/or modify it  *
    * under the terms of the GNU General Public License as published by the    *
    * Free Software Foundation; either version 2 of the License, or (at your   *
    * option) any later version.                                               *
    \**************************************************************************/
	/* $Id: hook_add_def_pref.inc.php 9268 2002-01-21 23:03:54Z ceb $ */

	global $pref;
	$pref->change('stocks','mainscreen','disabled');
?>
