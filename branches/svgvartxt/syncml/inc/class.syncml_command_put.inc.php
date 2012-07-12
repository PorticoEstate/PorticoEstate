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
	 * Takes care of incoming PUT commands.
	 */
	class syncml_command_put extends syncml_command
	{
		function syncml_command_put($xml_array = array())
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
					$this->cmdid, $session->msgid, 'Put',
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
							$this->cmdid, $session->msgid, 'Put',
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
				$result = $database->put_item(
					$item['source']['locuri'],
					$item['data'], $this->meta['type']);

				if(!isset($this->noresp) || !$this->noresp)
				{
					$response->add_status(
						$this->cmdid, $session->msgid, 'Put',
						NULL, $item['source']['locuri'],
						SYNCML_STATUS_OK);
				}
			}
		}
	}
?>
