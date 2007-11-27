<?php
  /**************************************************************************\
  * phpGroupWare - eLDAPtir - LDAP Administration                            *
  * http://www.phpgroupware.org                                              *
  * Written by Miles Lott <milosch@phpgroupware.org>                         *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: browser.php 8325 2001-11-19 16:08:46Z milosch $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'eldaptir',
		'enable_nextmatchs_class' => True
	);
	include('../header.inc.php');

	$GLOBALS['phpgw']->template->set_file(array(
		'browser' => 'browser.tpl'
	));

	$servers = servers();

	echo '<a href="'.$GLOBALS['phpgw']->link('/eldaptir','server_id='.$server_id).'">'.lang('eldaptir').'</a>&nbsp;'.lang('Browser');

	$GLOBALS['phpgw']->template->set_var('list',show($servers[$server_id]));
	$GLOBALS['phpgw']->template->pparse('out','browser');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
