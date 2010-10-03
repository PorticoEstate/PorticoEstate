<?php
	/**************************************************************************
	* phpGroupWare - ged
	* http://www.phpgroupware.org
	* Written by Pascal Vilarem <pascal.vilarem@steria.org>
	*
	* --------------------------------------------------------------------------
	*  This program is free software; you can redistribute it and/or modify it
	*  under the terms of the GNU General Public License as published by the
	*  Free Software Foundation; either version 2 of the License, or (at your
	*  option) any later version
	***************************************************************************/

	$GLOBALS['phpgw_info']['flags']=array
	(
		'currentapp'=>'ged',
		'noheader'=>True,
		'nonavbar'=>True
	);

	include('../header.inc.php');

	$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=>'ged.ged_ui.browse'));
	$GLOBALS['phpgw']->common->phpgw_exit();
?>
