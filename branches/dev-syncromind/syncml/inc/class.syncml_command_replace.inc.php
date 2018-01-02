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

	require_once 'inc/constants.inc.php';

	require_once 'inc/class.syncml_command.inc.php';

	/**
	 * A REPLACE command.
	 */
	class syncml_command_replace extends syncml_command
	{
		function syncml_command_replace($xml_array = array())
		{
			if(is_array($xml_array))
			{
				$this->parse_xml_array($xml_array);
			}
		}

		/**
		 * Handle and answer this alert call.
		 *
		 * @param $response Response object to answer to.
		 * @param $session  Current session.
		 */
		function execute(&$response, &$session, &$database)
		{
			if($response->has_global_status_code())
			{
				$response->add_status(
					$this->cmdid, $session->msgid, 'Replace',
					NULL, NULL,
					$response->get_global_status_code());
				return;
			}

			if(isset($this->item) && is_array($this->item))
			{
				foreach($this->item as $item)
				{
					$status = $this->save_chunk(
						isset($this->meta) ? $this->meta : array(),
						$item, $session);

					switch($status)
					{
						case SYNCML_ALERT_NOENDOFDATA:
							// send alert
							$response->add_alert(
								SYNCML_ALERT_NOENDOFDATA, array());

							// reset all chunking stuff when we're done
							$this->reset_chunking($session);

							// rerun the command
							$this->execute($response, $session, $database);
							return;
						case SYNCML_STATUS_SIZEMISMATCH:
							$this->reset_chunking($session);
						case SYNCML_STATUS_CHUNKEDITEMACCEPTEDANDBUFFERED:
							$code = $status;
							break;
						default:
							$data = $session->get_var(SYNCML_ITEMBUFFER);
							$type = $session->get_var(SYNCML_ITEMTYPE);

							list($code, $guid) = $database->replace_item(
								$item['source']['locuri'], $data, $type);

							$session->save_modified_luid(
								$database->channel_id,
								$item['source']['locuri']);
								
							$session->save_modified_guid(
								$database->channel_id, $guid);

							// reset all chunking stuff when we're done
							$this->reset_chunking($session);
					}

					$response->add_status(
						$this->cmdid, $session->msgid, 'Replace',
						NULL, $item['source']['locuri'],
						$code);
				}
			}
			else
			{
				$response->add_status(
					$this->cmdid, $session->msgid, 'Replace',
					NULL, NULL,
					SYNCML_STATUS_OK);
			}
		}
	}
?>
