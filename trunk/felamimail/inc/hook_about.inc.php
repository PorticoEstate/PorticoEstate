<?php
    /***************************************************************************\
    * phpGroupWare - Notes                                                      *
    * http://www.phpgroupware.org                                               *
    * -----------------------------------------------                           *
    * This program is free software; you can redistribute it and/or modify it   *
    * under the terms of the GNU General Public License as published by the     *
    * Free Software Foundation; either version 2 of the License, or (at your    *
    * option) any later version.                                                *
    \***************************************************************************/
	/* $Id$ */

	function about_app($tpl,$handle)
	{
		$s  = '<b>' . lang('Squirrelmail') . '</b><p>' . lang('ported to PHPGroupware by:') . ' Lars Kneschke<br><br>';
		$s .= lang('This port is based on Squirrelmail, which is a standalone IMAP client.<br>');
		$s .= lang('Have a look at <a href="http://www.felamimail.org" target="_new">www.felamimail.org</a> to learn more about Squirrelmail.<br>');
		return $s;
	}
?>
