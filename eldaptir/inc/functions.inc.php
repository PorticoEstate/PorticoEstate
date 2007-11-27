<?php
  /**************************************************************************\
  * phpGroupWare - eLDAPtir - LDAP Administration                            *
  * http://www.phpgroupware.org                                              *
  * Sections of code were taken from PHP TreeMenu 1.1                        *
  *  by Bjorge Dijkstra - bjorge@gmx.net                                     *
  * ------------------------------------------------------------------------ *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  
  /* $Id: functions.inc.php 8324 2001-11-19 15:50:05Z milosch $ */

	function servers()
	{
		$servers = array();

		$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_eldaptir_servers WHERE id>0");
		while ($GLOBALS['phpgw']->db->next_record())
		{
			if ($GLOBALS['phpgw']->db->f('name'))
			{
				$servers[$GLOBALS['phpgw']->db->f('id')]= array(
					'id'     => trim(stripslashes($GLOBALS['phpgw']->db->f('id'))),
					'type'   => trim(stripslashes($GLOBALS['phpgw']->db->f('type'))),
					'host'   => trim(stripslashes($GLOBALS['phpgw']->db->f('name'))),
					'basedn' => trim(stripslashes($GLOBALS['phpgw']->db->f('basedn'))),
					'rootdn' => trim(stripslashes($GLOBALS['phpgw']->db->f('rootdn'))),
					'rootpw' => trim(stripslashes($GLOBALS['phpgw']->db->f('rootpw')))
				);
				if ($GLOBALS['phpgw']->db->f('is_default'))
				{
					$servers[$GLOBALS['phpgw']->db->f('id')]['default'] = True;
				}
			}
		}
		return $servers;
	}

	function server_types($type)
	{
		$selected[$type] = ' selected';
		$s  = "\n" . '<select name="server_type">' . "\n";
		$s .= '<option value="">' . lang('Please Select') . '</option>'."\n";
		$s .= '<option value="openldap1"' . $selected['openldap1'] . '>OpenLDAP 1.X</option>'."\n";
		$s .= '<option value="openldap2"' . $selected['openldap2'] . '>OpenLDAP 2.X</option>'."\n";
		$s .= '<option value="iplanet"'   . $selected['iplanet'] . '>iPlanet/Netscape</option>'."\n";
		$s .= '</select>'."\n";

		return $s;
	}

	/* Return a select form element with the server option dialog in it */
	function server_option($servers,$id='',$java=True)
	{
		if ($java)
		{
			$jselect = " onChange=\"this.form.submit();\"";
		}
		$server_link  = "\n<select name=\"server_id\"$jselect>\n";

		while (list($key,$server) = each($servers))
		{
			if($server['host'])
			{
				$server_link .= '<option value="'.$server['id'].'"';
				if ($server['id'] == $id)
				{
					$server_link .= ' selected';
				}
				$server_link .= '>'.$server['host'].'</option>'."\n";
			}
		}
		$server_link .= '</select>'."\n";
		return $server_link;
	}

	function show($server,$filter="",$action="",$id="",$base="",$andor="")
	{
		global $p,$server_id;

		$ldapobj = CreateObject('eldaptir.ldap',$server);
		$newldap = CreateObject('eldaptir.ldap',$server);

		$top=0; $isentry=0;

		if ($filter=="" || strlen($filter)<2 )
		{
			/* $filter = "(|(objectclass=*))"; */
			$filter = "ou=*";
		}

		if ($base=="")
		{
			$base = $ldapobj->base; $top=1;
		}
		else
		{
			$base = urldecode($base);
		}

		$treeinfo[0] = "." . $ldapobj->base . ' ' . lang('on') . ' ' . $ldapobj->host;

		$info = $ldapobj->search();
		/* echo $ldapobj->total_entries; */
		for ($i=0;$i<$ldapobj->total_entries;$i++)
		{
			$num++;
			$newbase = $ldapobj->get_ou($info[$i]['dn']);
			$subinfo = $newldap->search('','',$newbase.','.$ldapobj->base,"cn=*,uid=*,nslielementtype=*",'','OR');

			$treeinfo[$num] = ".." . $info[$i]['dn']."|". $GLOBALS['phpgw']->link('/eldaptir/view.php',$newbase.'&dn='.$info[$i]['dn']);

			for ($j=0;$j<$newldap->total_entries;$j++)
			{
				$num++;
				if ($subinfo[$j]['dn'] != $info[$i]['dn'])
				{
					$thisdn = urlencode($subinfo[$j]['dn']);
					$treeinfo[$num] = "..." . $subinfo[$j]['dn'] . "|" . $GLOBALS['phpgw']->link('/eldaptir/view.php',$newbase.'&dn='.$thisdn);
				}
			}
		}

		/* This is a sampling of the format used by the treemenu function below */
		$oltreeinfo = array(
			".About|about.html|main",
			".<b>Demo menu</b>|javascript: alert('This is the demo menu for TreeMenu 1.0');",
			"..<b>category 1</b>",
			"...<b>sub category 1.1</b>",
			"....item 1.1.1|javascript: alert('Item 1.1.1');",
			"....item 1.1.2|javascript: alert('Item 1.1.1');",
			"...item 1.2|javascript: alert('Item 1.2');",
			"...item 1.3|javascript: alert('Item 1.3');",
			"..<b>category 2</b>",
			"...item 2.1|javascript: alert('Item 2.1');",
			"...item 2.2|javascript: alert('Item 2.2');",
			"...<b>sub category 2.3</b>",
			"....item 2.3.1|javascript: alert('Item 2.3.1');",
			"....item 2.3.2|javascript: alert('Item 2.3.2');",
			".<i><b>Download</b></i>|treemenu11.zip",
			".<i><b>Email me</b></i>|mailto:bjorge@gmx.net?subject=Tree%20Menu%20Demo"
		);

		$menutree = CreateObject('phpgwapi.menutree','text');
		$menutree->set_lcs(500);
		$out = $menutree->showtree($treeinfo,$p);
		return $out;
	}

	function updateme($dn)
	{
		$ldapobj->update($dn);
	}
?>
