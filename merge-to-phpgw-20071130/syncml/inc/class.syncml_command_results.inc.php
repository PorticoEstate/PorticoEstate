<?php
	/**
	 * phpGroupWare (http://phpgroupware.org/)
	 * SyncML interface
	 *
	 * @author    Johan Gunnarsson <johang@phpgroupware.org>
	 * @copyright Copyright (c) 2007 Free Software Foundation, Inc.
	 * @license   GNU General Public License 3 or later
	 * @package   syncml
	 * @version   $Id: class.syncml_command_results.inc.php 18223 2007-08-16 23:58:02Z johang $
	 */

	require_once 'inc/class.syncml_command.inc.php';

	/**
	 * Takes care of incoming RESULTS commands.
	 */
	class syncml_command_results extends syncml_command
	{
		function syncml_command_results($xml_array = array())
		{
			if(is_array($xml_array))
			{
				$this->parse_xml_array($xml_array);
			}
		}

		function execute(&$response, &$session)
		{
			if($response->has_global_status_code())
			{
				$response->add_status(
					$this->cmdid, $session->msgid, 'Results',
					NULL, NULL,
					$response->get_global_status_code());
				return;
			}
		}
	}
?>
