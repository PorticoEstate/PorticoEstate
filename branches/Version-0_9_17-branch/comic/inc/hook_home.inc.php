<?php
  /**************************************************************************\
  * phpGroupWare - E-Mail                                                    *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: hook_home.inc.php 17800 2006-12-28 04:29:52Z skwashd $ */
{

	$tmp_app_inc = $GLOBALS['phpgw']->common->get_inc_dir('comic');

	$GLOBALS['phpgw']->db->query('SELECT * FROM phpgw_comic'
				. ' WHERE comic_owner = ' . (int) $GLOBALS['phpgw_info']['user']['account_id'], __LINE__, __FILE__);

	if ($GLOBALS['phpgw']->db->num_rows())
	{
		$GLOBALS['phpgw']->db->next_record();

		$data_id      = $GLOBALS['phpgw']->db->f('comic_frontpage');
		$scale        = $GLOBALS['phpgw']->db->f('comic_fpscale');
		$censor_level = $GLOBALS['phpgw']->db->f('comic_censorlvl');

		if ($data_id != -1)
		{
			$title = lang('Comic');

			$portalbox = CreateObject('phpgwapi.listbox',
				Array(
					'title'	=> $title,
				)
			);
			$app_id = $GLOBALS['phpgw']->applications->name2id('comic');
			$GLOBALS['portal_order'][] = $app_id;
			$var = Array(
				'up'		=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
				'down'		=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
				'close'		=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
				'question'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
				'edit'		=> Array('url'	=> '/set_box.php', 'app'	=> $app_id)
			);

			foreach($var as $key => $value)
			{
				$portalbox->set_controls($key,$value);
			}
			include_once($tmp_app_inc . '/functions.inc.php');
			echo "\r\n".'<!-- start Comic info -->'."\r\n"
				.$portalbox->draw(comic_display_frontpage($data_id, $scale, $censor_level))
				.'<!-- ends Comic info -->'."\r\n";
		}
	}
}
?>
