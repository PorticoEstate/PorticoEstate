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

	require_once 'inc/class.syncml_command.inc.php';

	/**
	 * Takes care of incoming FINAL commands.
	 */
	class syncml_command_final extends syncml_command
	{
		function syncml_command_final($xml_array = array())
		{
			if(is_array($xml_array))
			{
				$this->parse_xml_array($xml_array);
			}
		}

		function execute(&$response, &$session)
		{
			$response->set_final(TRUE);
		}
	}
?>
