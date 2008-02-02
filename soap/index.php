<?php
/**************************************************************************\
* phpGroupWare - XML-RPC Test App                                          *
* http://www.phpgroupware.org                                              *
* --------------------------------------------                             *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

/* $Id$ */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'  => 'soap',
		'noheader'    => True,
		'noappheader' => True,
		'nonavbar'    => True,
		'disable_Template_class' => True
	);
	include('../header.inc.php');

	$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'soap.test_methods.uitest_methods'));
	
	$GLOBALS['phpgw']->common->phpgw_exit();
?>
