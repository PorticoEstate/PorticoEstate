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
	 * Updates the table containing username+password hashes.
	 *
	 * @param $account_id  ID of account to update.
	 * @param $account_lid Name of account to update.
	 * @param $password    New password to hash.
	 */
	function syncml_update_hash($account_id, $account_lid, $password)
	{
		$GLOBALS["phpgw"]->db->query(sprintf("
			DELETE FROM phpgw_syncml_hashes
			WHERE
				account_id = '%d'",
			$account_id
		));

		$GLOBALS["phpgw"]->db->query(sprintf("
			INSERT INTO phpgw_syncml_hashes(
				account_id, hash)
			VALUES('%d', '%s')",
			$account_id,
			base64_encode(md5($account_lid . ':' . $password, true))
		));
	}

	/**
	 * Returns a hash calculated according to the specification of md5-auth.
	 *
	 * @param $username User.
	 * @param $password Password.
	 * @param $nonce    Nonce.
	 * @return string   Digest.
	 */
	function syncml_calculate_digest($username, $password, $nonce)
	{
		return md5(
			base64_encode(md5($username . ':' . $password, true)) .
				':' . $nonce,
			true
		);
	}

	/**
	 * Parse the XML definition and look for encoding attribute.
	 *
	 * @param $data   XML string.
	 * @return string Found encoding or NULL of no encoding found. 
	 */
	function syncml_parse_encoding($data)
	{
		if(preg_match('/<?xml.*encoding=[\'"](.*?)[\'"].*?>/m', $data, $m))
		{
			return strtoupper($m[1]);
		}
		
		return NULL;
	}
?>
