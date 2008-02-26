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
	 * SyncML basic style authentication.
	 */
	class syncml_auth_basic
	{
		/**
		 * Check credentials.
		 *
		 * @param $data  Credentials as sent from client.
		 * @return mixed False on failure. New session ID as string on
		 *               success.
		 */
		function authenticate($data)
		{
			// $data is in the form username:password.
			// neither username nor password should contain colon.
			@list($username, $passwd) = explode(':', $data, 2);

			return $GLOBALS['phpgw']->session->create($username, $passwd);
		}
	}
?>
