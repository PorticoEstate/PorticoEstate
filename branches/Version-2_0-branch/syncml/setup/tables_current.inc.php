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

	$phpgw_baseline = array(
		/**
		 * Maps SyncML sessions to phpgw sessions.
		 */
		'phpgw_syncml_sessions' => array(
			'fd' => array(
				'phpgw_sid' => array(
					'type' => 'varchar', 'precision' => '32',
					'nullable' => False),
				'syncml_hash' => array(
					'type' => 'varchar', 'precision' => '32',
					'nullable' => False),
				'session_dla' => array(
					'type' => 'int', 'precision' => '8',
					'nullable' => False),
				'next_nonce' => array(
					'type' => 'varchar', 'precision' => '32',
					'nullable' => False)
			),
			'pk' => array('syncml_hash'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		/**
		 * Just a way to keep precalculated hashes of users' names and
		 * passwords.
		 */
		'phpgw_syncml_hashes' => array(
			'fd' => array(
				'account_id' => array(
					'type' => 'int', 'precision' => 4),
				'hash' => array( // b64(md5(username:passwd))
					'type' => 'varchar', 'precision' => 32,
					'nullable' => False)
			),
			'pk' => array('account_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		/**
		 * Every device has one channel to each database.
		 */
		'phpgw_syncml_channels' => array(
			'fd' => array(
				'channel_id' => array(
					'type' => 'auto', 'nullable' => False),
				'device_uri' => array(
					'type' => 'varchar', 'precision' => '255',
					'nullable' => False),
				'database_id' => array(
					'type' => 'int', 'precision' => '4', 'nullable' => False),
				'phpgw_anchor_last' => array(
					'type' => 'varchar', 'precision' => '16'),
				'device_anchor_last' => array(
					'type' => 'varchar', 'precision' => '16'),
				'devinf_cache' => array(
					'type' => 'text', 'default' => ''),
				'last_merge' => array(
					'type' => 'int', 'precision' => '8', 'nullable' => False)
			),
			'pk' => array('channel_id'),
			'fk' => array(),
			'ix' => array('device_uri', 'database_id'),
			'uc' => array()
		),
		/**
		 * Each user has synchable databases.
		 */
		'phpgw_syncml_databases' => array(
			'fd' => array(
				'database_id' => array(
					'type' => 'auto', 'nullable' => False),
				'database_uri' => array(
					'type' => 'varchar', 'precision' => '255',
					'nullable' => False),
				'source_id' => array(
					'type' => 'int', 'precision' => '4',
					'nullable' => False),
				'credential_required' => array(
					'type' => 'int', 'precision' => '2',
					'nullable' => False),
				'credential_hash' => array( // b64(md5(username:passwd))
					'type' => 'varchar', 'precision' => '32',
					'nullable' => False),
				'account_id' => array(
					'type' => 'int', 'precision' => '4', 'nullable' => False)
			),
			'pk' => array('database_id'),
			'fk' => array(),
			'ix' => array('database_uri'),
			'uc' => array()
		),
		/**
		 * Maps ID numbers on client devices to ID numbers in phpgw.
		 */
		'phpgw_syncml_mappings' => array(
			'fd' => array(
				'mapping_id' => array(
					'type' => 'auto', 'nullable' => False),
				'channel_id' => array(
					'type' => 'int', 'precision' => '4',
					'nullable' => False),
				'luid' => array(
					'type' => 'varchar', 'precision' => '255',
					'nullable' => True),
				'guid' => array(
					'type' => 'varchar', 'precision' => '255',
					'nullable' => True),
				'dirty' => array(
					'type' => 'int', 'precision' => '2',
					'nullable' => False)
			),
			'pk' => array('mapping_id'),
			'fk' => array(),
			'ix' => array('channel_id'),
			'uc' => array()
		),
		/**
		 * Data sources that databases can use.
		 */
		'phpgw_syncml_sources' => array(
			'fd' => array(
				'source_id' => array(
					'type' => 'auto', 'nullable' => False),
				'name' => array(
					'type' => 'varchar', 'precision' => '50',
					'nullable' => False),
				'modulename' => array(
					'type' => 'varchar', 'precision' => '25',
					'nullable' => False),
				'mimetype' => array(
					'type' => 'varchar', 'precision' => '255',
					'nullable' => False),
				'mimeversion' => array(
					'type' => 'varchar', 'precision' => '8',
					'nullable' => False)
			),
			'pk' => array('source_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
	);
?>
