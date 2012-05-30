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

	require_once 'inc/utils/functions.inc.php';

	syncml_update_hash(
		$GLOBALS['phpgw_info']['user']['account_id'],
		$GLOBALS['phpgw_info']['user']['account_lid'],
		base64_decode(
			$GLOBALS['phpgw']->session->appsession('password', 'phpgwapi')
		)
	);
?>
