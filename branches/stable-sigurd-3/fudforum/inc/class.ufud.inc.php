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

	class ufud
	{
		/* array(
			'account_id'  => // numerical user id
		        'account_lid' => // account-name
		        'account_status' => // 'A' on active, else empty
		        'account_firstname' => // guess what ;-)
		        'account_lastname' => //
		        'new_passwd' => //
		        'location' => 'addaccount')
		*/
		function __get_email($id)
		{
			$preferences = CreateObject('phpgwapi.preferences', $id);
			$preferences->read_repository();
			return $preferences->email_address($id);
		}

		function add_account($row)
		{
			$GLOBALS['phpgw']->db->query("SELECT id FROM phpgw_fud_themes WHERE (theme_opt & 2) > 0 LIMIT 1");
			$theme = $GLOBALS['phpgw']->db->Record;
			$theme = $theme['id'] ? (int) $theme['id'] : 1;
			$email = addslashes($this->__get_email($row['account_id']));
			$name = addslashes($row['account_firstname'] . ' ' . $row['account_lastname']);
			$phpgw_id = $row['account_id'];
			$alias = addslashes(htmlspecialchars($row['account_lid']));
			$login = addslashes($row['account_lid']);
			$users_opt = 2|4|16|32|64|128|256|512|2048|4096|8192|16384|131072|4194304;
			if ($row['account_status'] != 'A') {
				$user_opts |= 2097152;
			}
			$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_fud_users (last_visit, join_date, theme, alias, login, email, passwd, name, users_opt, phpgw_id) VALUES(".time().", ".time().", {$theme}, '{$alias}', '{$login}', '{$email}', '{$row['account_pwd']}', '{$name}', {$users_opt}, {$phpgw_id})");
		}

		/* array(
			'account_id'  => // numerical user id
		        'account_lid' => // account-name
		        'account_status' => // 'A' on active, else empty
		        'account_firstname' => // guess what ;-)
		        'account_lastname' => //
		        'location' => 'changepassword'
		) */
		function chg_settings($row)
		{
			$name = addslashes($row['account_firstname'] . ' ' . $row['account_lastname']);
			$email = addslashes($this->__get_email($row['account_id']));
			$login = addslashes($row['account_lid']);
			$alias = addslashes(htmlspecialchars($row['account_lid']));
			$status = ($row['account_status'] != 'A') ? 'users_opt & ~ 2097152' : 'users_opt|2097152';

			$GLOBALS['phpgw']->db->query("UPDATE phpgw_fud_users SET name='{$name}', email='{$email}', login='{$login}', alias='{$alias}', users_opt={$status} WHERE phpgw_id={$row['account_id']}");
		}

		/* array(
			'account_id'  => // numerical user id
		        'account_lid' => // account-name
		) */

		function del_account($row)
		{
			define('plain_page', 1);
			require($GLOBALS['phpgw_info']['server']['files_dir'] . "/fudforum/include/GLOBALS.php");

			fud_use('db.inc');
			fud_use('private.inc');
			fud_use('users_reg.inc');
			fud_use('users_adm.inc', true);

			$id = q_singleval("SELECT id FROM phpgw_fud_users WHERE phpgw_id=".$row['account_id']);
			if ($id) {
				usr_delete($id);
			}
		}
	}
?>
