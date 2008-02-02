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

	$ldapobj = CreateObject('eldaptir.ldap');

	echo "<table>\n<form method=\"GET\" action=\"".$GLOBALS['phpgw']->link('/eldaptir/edit.php')."\">\n";
	echo "<br>".lang('Add').' '.lang('objectclasses')." ".lang('to')." ".$dn;
	while(list($key,$oc) = each($ldapobj->objectclasses))
	{
		echo '<tr><td><input type="checkbox" name="' . $oc . '">' . $oc
			. '</td></tr>'."\n";
	}
	echo '<tr><td>'."\n";
	echo '<input type="hidden" name="dn" value="'.$dn.'">'."\n";
	echo '<input type="submit" name="addobj" value="'.lang('Add').'">'."\n";
	echo '</td></tr>'."\n";
	echo "</form>\n</table>\n";

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
