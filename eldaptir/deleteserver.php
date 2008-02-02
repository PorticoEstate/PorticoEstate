<?php
  /**************************************************************************\
  * phpGroupWare - eldaptir                                                  *
  * (http://www.phpgroupware.org)                                            *
  * Written by Miles Lott <milosch@phpgroupware.org>                         *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id$ */

	if ($confirm)
	{
		$GLOBALS['phpgw_info']['flags'] = array(
			'noheader' => True, 
			'nonavbar' => True
		);
	}

	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'eldaptir';
	include('../header.inc.php');

	if(!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
	{
		echo lang('access not permitted');
		$GLOBALS['phpgw']->common->phpgw_footer();
		$GLOBALS['phpgw']->common->phpgw_exit();

	}

	if (!$server_id)
	{
		Header('Location: ' . $GLOBALS['phpgw']->link('/eldaptir/servers.php'));
	}

	if ($confirm)
	{
		$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_eldaptir_servers WHERE id=" . $server_id);
		Header('Location: ' . $GLOBALS['phpgw']->link('/eldaptir/servers.php',"start=$start&query=$query&sort=$sort&order=$order&filter=$filter"));
	}
	else
	{
		$hidden_vars =
			  '<input type="hidden" name="sort" value="' . $sort . '">' . "\n"
			. '<input type="hidden" name="order" value="' . $order . '">' . "\n"
			. '<input type="hidden" name="query" value="' . $query . '">' . "\n"
			. '<input type="hidden" name="start" value="' . $start . '">' . "\n"
			. '<input type="hidden" name="filter" value="' . $filter . '">' . "\n"
			. '<input type="hidden" name="server_id" value="' . $server_id . '">' . "\n";

		$GLOBALS['phpgw']->template->set_file(array('server_delete' => 'delete_common.tpl'));
		$GLOBALS['phpgw']->template->set_var('messages',lang('Are you sure you want to delete this server ?'));

		$nolinkf = $GLOBALS['phpgw']->link('/eldaptir/servers.php',"server_id=$server_id&start=$start&query=$query&sort=$sort&order=$order&filter=$filter");
		$nolink = '<a href="' . $nolinkf . '">' . lang('No') . '</a>';
		$GLOBALS['phpgw']->template->set_var('no',$nolink);

		$yeslinkf = $GLOBALS['phpgw']->link('/eldaptir/deleteserver.php',"server_id=$server_id&confirm=True");
		$yeslinkf = '<FORM method="POST" name="yesbutton" action="' . $GLOBALS['phpgw']->link('/eldaptir/deleteserver.php') . '">'
			. $hidden_vars
			. '<input type="hidden" name="server_id" value="' . $server_id . '">'
			. '<input type="hidden" name="confirm" value="True">'
			. '<input type="submit" name="yesbutton" value="Yes">'
			. '</FORM><SCRIPT>document.yesbutton.yesbutton.focus()</SCRIPT>';

		$yeslink = "<a href=\"$yeslinkf\">" . lang('Yes') ."</a>";
		$yeslink = $yeslinkf;
		$GLOBALS['phpgw']->template->set_var('yes',$yeslink);

		$GLOBALS['phpgw']->template->pparse('out','server_delete');
	}

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
