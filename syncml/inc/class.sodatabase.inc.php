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
	 * Manage input/output to/from the database table.
	 */
	class syncml_sodatabase
	{
		function remove_database($database_id, $database_uri)
		{
			$GLOBALS['phpgw']->db->query("
				DELETE FROM phpgw_syncml_databases
				WHERE ' .
					(!is_null($database_id) ?
						'database_id = \'' . $database_id . '\' AND ' : '') .
					(!is_null($database_uri) ?
						'database_uri = \'' . $database_uri . '\' AND ' : '') .
					'1 = 1'",
				__LINE__, __FILE__);
		}

		function insert_database($database_id, $database_uri)
		{
			$GLOBALS['phpgw']->db->query(sprintf("
				INSERT INTO phpgw_syncml_databases(
					database_uri, source_id, credential_required,
					credential_hash, account_id)
				VALUES('%s', '%d', '%d', '%s', '%d')",
				$database_uri, $source_id, !is_null($cred_hash), $cred_hash,
				$account_id),
				__LINE__, __FILE__);
		}

		function get_database($database_id, $account_id, $database_uri)
		{
			$GLOBALS['phpgw']->db->query('
				SELECT *
				FROM phpgw_syncml_databases m
				WHERE ' .
					(!is_null($database_id) ?
						'database_id = \'' . $database_id . '\' AND ' : '') .
					(!is_null($account_id) ?
						'account_id = \'' . $account_id . '\' AND ' : '') .
					(!is_null($database_uri) ?
						'database_uri = \'' . $database_uri . '\' AND ' : '') .
					'1 = 1',
				__LINE__, __FILE__);

			$databases = array();

			while($GLOBALS['phpgw']->db->next_record())
			{
				$databases[] = $GLOBALS['phpgw']->db->Record;
			}

			return $databases;
		}
		
		/**
		 * Get a database 
		 */
		function get_database_by_uri($uri)
		{
			return array_shift($this->get_database(NULL, NULL, $uri));
		}
	}
