<?php
 /**************************************************************************\
 * phpGroupWare - fax                                                       *
 * http://www.phpgroupware.org                                              *
 * This application written by:                                             *
 *                             Marco Andriolo-Stagno <stagno@prosa.it>      *
 *                             PROSA <http://www.prosa.it>                  *
 * -------------------------------------------------------------------------*
 * Funding for this program was provided by http://www.seeweb.com           *
 * -------------------------------------------------------------------------*
 *  This program is free software; you can redistribute it and/or modify it *
 *  under the terms of the GNU General Public License as published by the   *
 *  Free Software Foundation; either version 2 of the License, or (at your  *
 *  option) any later version.                                              *
 \**************************************************************************/

/* $Id$ */

{
	$title = $appname;
	$file = array
	  (
	   #	'Preferences'     => $GLOBALS['phpgw']->link('/index.php','menuaction=antispam.checker.handlePreferences'),
	   'FAX Settings'	=>	$GLOBALS['phpgw']->link('/index.php','menuaction=fax.manager.preferences'),
	   );
	
//Do not modify below this line
	display_section($appname,$title,$file);
}
?>
	
