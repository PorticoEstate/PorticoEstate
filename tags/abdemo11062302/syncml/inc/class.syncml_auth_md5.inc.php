<?php
	/**
	 * phpGroupWare (http://phpgroupware.org/)
	 * SyncML interface
	 *
	 * @author    Johan Gunnarsson <johang@phpgroupware.org>
	 * @copyright Copyright (c) 2007 Free Software Foundation, Inc.
	 * @license   GNU General Public License 3 or later
	 * @package   syncml
	 * @version   $Id$
	 */

	/**
	 * SyncML md5 challenge style authentication.
	 */
	class syncml_auth_md5
	{
		var $session;

		var $locname;

		function syncml_auth_md5(&$session, $locname)
		{
			$this->session = &$session;
			$this->locname = $locname;
		}

		/**
		 * Check credentials.
		 *
		 * @param $digest Credentials as sent from client. 128-bit binary
		 *                format.
		 * @return mixed  False on failure. New session ID as string on
		 *                success.
		 */
		function authenticate($digest)
		{
			$nonce = $this->session->next_nonce;

			$GLOBALS['phpgw']->db->query(sprintf("
				SELECT
					a.account_pwd,
					a.account_lid
				FROM phpgw_syncml_hashes h
				JOIN phpgw_accounts a ON
					a.account_id = h.account_id
				WHERE
					a.account_lid = '%s' AND
					md5(concat_ws(':', h.hash, '%s')) = '%s'",
				$GLOBALS['phpgw']->db->db_addslashes($this->locname),
				$GLOBALS['phpgw']->db->db_addslashes($nonce),
				bin2hex($digest)),
				__LINE__, __FILE__);

			if(!$GLOBALS['phpgw']->db->next_record())
				return false;

			$passwd = $GLOBALS['phpgw']->db->f('account_pwd');
			$username = $GLOBALS['phpgw']->db->f('account_lid');

			return $GLOBALS['phpgw']->session->create($username, $passwd, true);
		}
	}
?>
