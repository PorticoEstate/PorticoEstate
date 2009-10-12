<?php
	/**************************************************************************\
	* phpGroupWare - Chat                                                      *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'chat',
		'noheader'   => True,
		'nonavbar'   => True
	);
	include('../header.inc.php');
?>
<html>
	<head></head>
	<FRAMESET ROWS="*,130" BORDER="0" SCROLLING="NO">
		<?php echo '<FRAME SRC="' . $GLOBALS['phpgw']->link('/chat/messages.php','channel='.$channel.'&action='.$action.'&location='.$location) . '" NAME="messages">';
		echo '<FRAME SRC="' . $GLOBALS['phpgw']->link('/chat/sendmsg.php','channel='.$channel.'&location='.$location.'&user2='.$user2) . '" NAME="sendmsg" SCROLLING="NO">';?>
	</FRAMESET>
</html>
