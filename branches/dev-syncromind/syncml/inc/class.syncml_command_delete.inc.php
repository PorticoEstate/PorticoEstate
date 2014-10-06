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
	 * Takes care of incoming DELETE commands.
	 */
	class syncml_command_delete extends syncml_command
	{
		function syncml_command_delete($xml_array = array())
		{
			if(is_array($xml_array))
			{
				$this->parse_xml_array($xml_array);
			}
		}

		function execute(&$response, &$session, &$database)
		{
			if($response->has_global_status_code())
			{
				$response->add_status(
					$this->cmdid, $session->msgid, 'Delete',
					NULL, NULL,
					$response->get_global_status_code());
				return;
			}

			if(isset($this->item) && is_array($this->item))
			{
				foreach($this->item as $item)
				{
					$h = $database->delete_item($item['source']['locuri']);
					
					if($h[0])
					{
						$response->add_status(
							$this->cmdid, $session->msgid, 'Delete',
							NULL, $item['source']['locuri'],
							SYNCML_STATUS_OK);
					}
					else
					{
						$response->add_status(
							$this->cmdid, $session->msgid, 'Delete',
							NULL, $item['source']['locuri'],
							SYNCML_STATUS_NOTDELETED);
					}
				}
			}
		}
	}
?>
