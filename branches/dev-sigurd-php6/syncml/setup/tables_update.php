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

	$test[] = '0.9.17.001';

	function syncml_upgrade0_9_17_001()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameTable(
			'phpgw_syncml_channel', 'phpgw_syncml_channels'
		);

		$GLOBALS['phpgw_setup']->oProc->RenameTable(
			'phpgw_syncml_database', 'phpgw_syncml_databases'
		);

		$GLOBALS['phpgw_setup']->oProc->RenameTable(
			'phpgw_syncml_hash', 'phpgw_syncml_hashes'
		);

		$GLOBALS['phpgw_setup']->oProc->RenameTable(
			'phpgw_syncml_session', 'phpgw_syncml_sessions'
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn(
			'phpgw_syncml_channels', 'last_merge', array(
				'type' => 'int', 'precision' => 8, 'nullable' => False)
		);

		$GLOBALS['setup_info']['syncml']['currentver'] = '0.9.17.002';
		return $GLOBALS['setup_info']['syncml']['currentver'];
	}

	$test[] = '0.9.17.002';

	function syncml_upgrade0_9_17_002()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn(
			'phpgw_syncml_sessions', 'next_nonce', array(
				'type' => 'varchar', 'precision' => '32', 'nullable' => False)
		);

		$GLOBALS['setup_info']['syncml']['currentver'] = '0.9.17.003';
		return $GLOBALS['setup_info']['syncml']['currentver'];
	}
