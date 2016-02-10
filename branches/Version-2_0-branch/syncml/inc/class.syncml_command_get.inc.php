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

	require_once 'inc/class.syncml_database.inc.php';
	require_once 'inc/class.syncml_database_devinf.inc.php';

	/**
	 * Takes care of incoming GET commands.
	 */
	class syncml_command_get extends syncml_command
	{
		function syncml_command_get($xml_array = array())
		{
			if(is_array($xml_array))
			{
				$this->parse_xml_array($xml_array);
			}
		}

		function execute(&$response, &$session, $database = NULL)
		{
			if($response->has_global_status_code())
			{
				$response->add_status(
					$this->cmdid, $session->msgid, 'Get',
					NULL, NULL,
					$response->get_global_status_code());
				return;
			}

			if(!$database)
			{
				// No database or source is given. Try getting one by type.
				
				switch($this->meta['type'])
				{
					case 'application/vnd.syncml-devinf+xml':
					case 'application/vnd.syncml-devinf+wbxml':
						$database = new syncml_database_devinf($session);
						break;
					default:
						$response->add_status(
							$this->cmdid, $session->msgid, 'Get',
							NULL, NULL,
							SYNCML_STATUS_UNSUPPORTEDMEDIATYPEORFORMAT);
						return;
				}
			}
			else
			{
				return;
			}

			foreach($this->item as $item)
			{
				$result = $database->get_item(
					$item['target']['locuri'], $this->meta['type']);

				if(is_null($result))
				{
					// Item not found.

					$response->add_status(
						$this->cmdid, $session->msgid, 'Get',
						NULL, NULL,
						SYNCML_STATUS_NOTFOUND, array(
							'trg_uri' => $item['target']['locuri']
						)
					);
				}
				else
				{
					$response->add_status(
						$this->cmdid, $session->msgid, 'Get',
						NULL, NULL,
						SYNCML_STATUS_OK
					);

					$response->add_result(
						$this->cmdid, $session->msgid,
						NULL, NULL,
						'application/vnd.syncml-devinf+wbxml', array(
							array(
								'src_uri' => $item['target']['locuri'],
								'data' => $result
							)
						)
					);
				}
			}
		}
	}
?>
