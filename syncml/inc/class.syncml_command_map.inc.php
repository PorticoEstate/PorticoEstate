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

	require_once 'inc/class.somappings.inc.php';

	/**
	 * Handle a MAP command from client.
	 */
	class syncml_command_map extends syncml_command
	{
		function syncml_command_map($xml_array = array())
		{
			if(is_array($xml_array))
			{
				$this->parse_xml_array($xml_array);
			}
		}

		function execute(&$response, &$session)
		{
			$somappings = new syncml_somappings();

			if($response->has_global_status_code())
			{
				$response->add_status(
					$this->cmdid, $session->msgid, 'Map',
					NULL, NULL,
					$response->get_global_status_code());
				return;
			}

			$open_channel = $session->get_open_channel(
				$this->source['locuri'],
				$this->target['locuri']);

			foreach($this->mapitem as $item)
			{
				$somappings->delete_mapping(
					$open_channel['channel_id'],
					NULL, $item['target']['locuri'], NULL);

				$somappings->insert_mapping(
					$open_channel['channel_id'],
					$item['source']['locuri'], $item['target']['locuri'], 0);
			}

			if(isset($this->noresp) && $this->noresp)
			{
				return;
			}

			$response->add_status(
				$this->cmdid, $session->msgid, 'Map',
				$this->target['locuri'], $this->source['locuri'],
				SYNCML_STATUS_OK);
		}
	}
?>
