<?php
	/****************************************************************************\
	* phpGroupWare - FUDforum 2.6.0 equivalent                                   *
	* http://fud.prohost.org/                                                    *
	* Written by Ilia Alshanetsky <ilia@prohost.org>                             *
	* -------------------------------------------                                *
	*  This program is free software; you can redistribute it and/or modify it   *
	*  under the terms of the GNU General Public License as published by the     *
	*  Free Software Foundation; either version 2 of the License, or (at your    *
	*  option) any later version.                                                *
	\****************************************************************************/

class fud_sidebox_hooks
{
	function all_hooks($args)
	{
		if (!function_exists('db_saq')) {
			fud_use('db.inc');
		}
		$GLOBALS['adm_file'] = array();
		list($GLOBALS['fudh_uopt'], $theme_name) = db_saq("SELECT u.users_opt, t.name FROM phpgw_fud_users u INNER JOIN phpgw_fud_themes t ON t.id=u.theme WHERE u.id!=1 AND u.phpgw_id=".(int)$GLOBALS['phpgw_info']['user']['account_id']);
		$GLOBALS['fudh_uopt'] = (int) $GLOBALS['fudh_uopt'];
		if (!empty($GLOBALS['phpgw_info']['user']['apps']['admin'])) {
			$GLOBALS['fudh_uopt'] |= 1048576;
		}
		include_once($GLOBALS['DATA_DIR'].'include/theme/'.str_replace(' ', '_', $theme_name).'/usercp.inc');

		/* regular user links */
		if (!empty($GLOBALS['t'])) {
			display_sidebox('fudforum', lang('Preferences'), $GLOBALS['usr_file']);
		}

		/* admin stuff */
		if ($GLOBALS['adm_file']) {
			display_sidebox('fudforum', lang('Administration'), $GLOBALS['adm_file']);
		}
	}
}

?>
