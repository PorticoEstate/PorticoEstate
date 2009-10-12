<?php
  /**************************************************************************\
  * phpGroupWare - Admin                                                     *
  * (http://www.phpgroupware.org)                                            *
  * Written by Bettina Gille [ceb@phpgroupware.org]                          *    
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  /* $Id$ */

	$phpgw_info = array();
	if($confirm)
	{
		$GLOBALS['phpgw_info']["flags"] = array(
			'noheader' => True, 
			'nonavbar' => True
		);
	}

	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'admin';
	$GLOBALS['phpgw_info']['flags']['enable_config_class'] = True;
	include('../header.inc.php');

	if(!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
	{
		echo lang('access not permitted');
		$GLOBALS['phpgw']->common->phpgw_footer();
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	if(!$site_id)
	{
		Header('Location: ' . $GLOBALS['phpgw']->link('/chora/sites.php'));
	}

	if($confirm)
	{
		$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_chora_sites WHERE id=" . $site_id);
		Header('Location: ' . $GLOBALS['phpgw']->link('/chora/sites.php',"start=$start&query=$query&sort=$sort&order=$order&filter=$filter"));
	}
	else
	{
		$hidden_vars = "<input type=\"hidden\" name=\"sort\" value=\"$sort\">\n"
			. "<input type=\"hidden\" name=\"order\" value=\"$order\">\n"
			. "<input type=\"hidden\" name=\"query\" value=\"$query\">\n"
			. "<input type=\"hidden\" name=\"start\" value=\"$start\">\n"
			. "<input type=\"hidden\" name=\"filter\" value=\"$filter\">\n"
			. "<input type=\"hidden\" name=\"site_id\" value=\"$site_id\">\n";

		$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
		$t->set_file(array('site_delete' => 'delete_common.tpl'));
		$t->set_var('messages',lang('Are you sure you want to delete this repository?'));

		$nolinkf = $GLOBALS['phpgw']->link('/chora/sites.php',"site_id=$site_id&start=$start&query=$query&sort=$sort&order=$order&filter=$filter");
		$nolink = "<a href=\"$nolinkf\">" . lang('No') ."</a>";
		$t->set_var('no',$nolink);

		$yeslinkf = $GLOBALS['phpgw']->link('/chora/deletesite.php',"site_id=$site_id&confirm=True");
		$yeslinkf = "<FORM method=\"POST\" name=yesbutton action=\"".$GLOBALS['phpgw']->link('/chora/deletesite.php') . "\">"
			. $hidden_vars
			. "<input type=hidden name=site_id value=$site_id>"
			. "<input type=hidden name=confirm value=True>"
			. "<input type=submit name=yesbutton value=Yes>"
			. "</FORM><SCRIPT>document.yesbutton.yesbutton.focus()</SCRIPT>";

		$yeslink = "<a href=\"$yeslinkf\">" . lang('Yes') ."</a>";
		$yeslink = $yeslinkf;
		$t->set_var('yes',$yeslink);

		$t->pparse('out','site_delete');
	}

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
