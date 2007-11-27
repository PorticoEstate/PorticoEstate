<?php
  /**************************************************************************\
  * phpGroupWare app (NNTP)                                                  *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <mpeters@satx.rr.com>                             *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: functions.inc.php 8699 2001-12-21 03:59:13Z milosch $ */

	$d1 = strtolower(substr(PHPGW_APP_INC,0,3));
	if($d1 == 'htt' || $d1 == 'ftp' )
	{
		echo 'Failed attempt to break in via an old Security Hole!<br>'."\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	unset($d1);

	if(isset($GLOBALS['folder']) && isset($GLOBALS['msgnum']))
	{
		$param = Array(
			'folder' => $GLOBALS['folder'],
			'msgnum' => $GLOBALS['msgnum']
		);
		$GLOBALS['nntp'] = CreateObject('nntp.nntp',$param);
	}
	else
	{
		$GLOBALS['nntp'] = CreateObject('nntp.nntp');
	}
?>
