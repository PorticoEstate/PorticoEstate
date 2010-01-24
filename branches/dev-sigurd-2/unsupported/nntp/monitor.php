<?php
  /**************************************************************************\
  * phpGroupWare application (NNTP)                                          *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <mpeters@satx.rr.com>                             *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	if(substr(phpversion(),0,1) == '4')
	{
	?>
<?php php_track_vars ?>
	<?php
	}
	else
	{
	?>
<?php track_vars ?>
	<?php
	}
	/* $Id$ */

	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'nntp';
	$GLOBALS['phpgw_info']['flags']['noheader'] = True;
	$GLOBALS['phpgw_info']['flags']['nonavbar'] = True;
	include('../header.inc.php');

	$referer = getenv('HTTP_REFERER');

	$GLOBALS['phpgw']->db->query("SELECT active, name FROM newsgroups WHERE con=$folder");

	$GLOBALS['phpgw']->db->next_record();

	$active = $GLOBALS['phpgw']->db->f('active');
	$name = $GLOBALS['phpgw']->db->f('name');
?>
<center>
<?php

	if($active == 'Y')
	{
		$GLOBALS['phpgw']->preferences->add('nntp',$folder);
		$GLOBALS['phpgw']->preferences->save_repository(True);
		echo "Successful in monitoring $name.";
	}
	else
	{
		echo "Cannot monitor $name.<br>Administrator has it labeled as inactive.";
	}
?>
<form>
<input type="button" value="Close" onClick="window.close()">
</form>
</center>
<?php
	$GLOBALS['phpgw']->common->phpgw_exit();
?>

