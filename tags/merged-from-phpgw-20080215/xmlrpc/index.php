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

	$GLOBALS['phpgw_info'] = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'  => 'xmlrpc',
		'noheader'    => False,
		'noappheader' => False,
		'nonavbar'    => False
	);

	include('../header.inc.php');

	echo '<br><a href="' . $GLOBALS['phpgw']->link('/xmlrpc/testsuite.php') . '">' . lang('Test Suite') . '</a>' . "\n";
	echo '<br><a href="' . $GLOBALS['phpgw']->link('/xmlrpc/introspect.php') . '">' . lang('Introspection') . '</a>' . "\n";
	echo '<br><a href="' . $GLOBALS['phpgw']->link('/xmlrpc/interserv.php') . '">' . lang('phpgw client/server test') . '</a>' . "\n";
	echo '<br><a href="' . $GLOBALS['phpgw']->link('/xmlrpc/phpgw_test.php') . '">' . lang('phpgw server test') . '</a>' . "\n";
	echo '<br><a href="' . $GLOBALS['phpgw']->link('/xmlrpc/client.php') . '">' . lang('Simple Client') . '</a>' . "\n";

	if ($GLOBALS['phpgw']->acl->check('run',1,'meerkat'))
	{
		echo '<br><a href="' . $GLOBALS['phpgw']->link('/meerkat/index.php') . '">' . lang('Meerkat Browser') . '</a>' . "\n";
	}

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
