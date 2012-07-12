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

	require_once 'inc/class.syncml_session.inc.php';

	/**
	 * Dummy version of syncml_session. Please do not use other than in
	 * testing and/or debugging.
	 */
	class syncml_session_dummy extends syncml_session
	{
		/**
		 * This dummy contructor by-passes the parent constructor.
		 */
		function syncml_session_dummy()
		{
		}
		
		function commit()
		{
		}
	}
?>
