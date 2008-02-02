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

	/* $Id$ */

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
		echo '<form method="post" action="'.$GLOBALS['phpgw']->link('/eldaptir/schema.php').'">';
		echo server_option($servers,$server_id);
		echo '</form>';
	}

	$ldap = CreateObject('eldaptir.ldap',$servers[$server_id]);
	//$list = $ldap->schema->parse('all');
	//$ldap->schema->save($server_id,$list);
	$list = $ldap->schema->read($server_id,'attributetypes','type');

	echo '<table>';
	echo '<tr>';
	echo '<td>#</td>';
	echo '<td>OID</td>';
	echo '<td>Type</td>';
	echo '<td>Name</td>';
	echo '<td>Extra</td>';
	echo '</tr>';

	$i=0;
	while (list($key, $val) = each($list))
	{
		echo '<tr>';
		echo '<td>' . $i . '</td><td>' . $val['oid'] . '</td><td>' . $val['type'] . '</td><td>' . $val['name'] . '</td><td>' . $val['extra'] . '</td>';
		echo '</tr>';
		if($val['must'])
		{
			if (gettype($val['must']) == 'array')
			{
				$tmp = implode(',',$val['must']);
			}
			else
			{
				$tmp = $val['must'];
			}
			echo '<tr>';
			echo '<td>&nbsp;</td><td colspan="3">MUST:' . $tmp . '</td>';
			echo '</tr>';
		}
		if ($val['may'])
		{
			if (gettype($val['may']) == 'array')
			{
				$tmp = implode(',',$val['may']);
			}
			else
			{
				$tmp = $val['may'];
			}
			echo '<tr>';
			echo '<td>&nbsp;</td><td colspan="3">MAY:' . $tmp . '</td>';
			echo '</tr>';
		}
		$i++;
	}
	echo '</table>';

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
