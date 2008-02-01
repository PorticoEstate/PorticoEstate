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

	/* $Id: index.php 8324 2001-11-19 15:50:05Z milosch $ */

	$server_id = $HTTP_GET_VARS['server_id'];

	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'eldaptir';
	include('../header.inc.php');

	check_code($cd);

	echo '<br>'.lang('eldaptir').'<br><br>';

	$servers = servers();
	if ($servers)
	{
		if (!$server_id)
		{
			while(list($key,$test)=each($servers))
			{
				if ($test['default'])
				{
					$server_id = $test['id'];
				}
			}
			if (!$server_id)
			{
				echo'No default set, using phpgw configured server';
			}
		}
		echo '<form method="post" action="'.$GLOBALS['phpgw']->link('/eldaptir/index.php').'">';
		echo server_option($servers,$server_id);
		echo '</form>';
	}

	$ldapobj = CreateObject('eldaptir.ldap',$servers[$server_id]);
	$info = $ldapobj->search();

	echo lang('Organizational Units')."<br>";
	for ($i=0;$i<$ldapobj->total_entries;$i++)
	{
		$ou = $ldapobj->get_ou($info[$i]['dn']);
		//echo $info[$i]['dn'];
		echo '&nbsp;<a href="' . $GLOBALS['phpgw']->link('/eldaptir/viewou.php',$ou.'&server_id='.$server_id) . '">'
			. $ou."</a><br>\n";
	}
	echo "<br>",lang('Browse')."<br>";
	echo '&nbsp;<a href="' . $GLOBALS['phpgw']->link('/eldaptir/browser.php','server_id='.$server_id). '">' . lang('LDAP Browser') . '</a>';

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
