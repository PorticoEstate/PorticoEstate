<?php
/**************************************************************************\
* phpGroupWare - Antispam                                                  *
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

  /* $Id: hook_manual.inc.php 11589 2002-11-27 14:35:47Z stagno $ */

	// Only Modify the $file variable.....
	$file = array
	(
		'Manual'	=>	'view.php'
	);
	
	//Do not modify below this line
	display_manual_section($appname,$file);

?>
