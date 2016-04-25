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
	 * Takes care of incoming STATUS commands.
	 */
	class syncml_command_status extends syncml_command
	{
		function syncml_command_status($xml_array = array())
		{
			if(is_array($xml_array))
			{
				$this->parse_xml_array($xml_array);
			}
		}

		function execute(&$response, &$session)
		{
			switch(strtoupper($this->cmd))
			{
				case 'DELETE':
				case 'REPLACE':
				case 'ADD':
					$method_name = 'process_' . strtolower($this->cmd);
					$this->$method_name($response, $session);
					break;
			}
		}
		
		function process_add(&$response, &$session)
		{
			$somappings = new syncml_somappings();

			$channel_id = $session->get_channel_id_from_cmd(
				$this->cmdref, $this->msgref);

			if(!is_null($channel_id)) {
				switch($this->data)
				{
					case SYNCML_STATUS_OK:
						// insert a temp mapping so we don't send this item
						// again if client caches the MAP commands
						$somappings->insert_mapping(
							$channel_id,  NULL, $this->sourceref, 0);
				}
			}
		}

		function process_delete(&$response, &$session)
		{
			$somappings = new syncml_somappings();

			$channel_id = $session->get_channel_id_from_cmd(
				$this->cmdref, $this->msgref);
			
			if(!is_null($channel_id)) {
				$somappings->delete_mapping(
					$channel_id, $this->targetref, NULL, NULL);
			}
		}

		function process_replace(&$response, &$session)
		{
			$somappings = new syncml_somappings();

			$channel_id = $session->get_channel_id_from_cmd(
				$this->cmdref, $this->msgref);

			if(!is_null($channel_id)) {
				switch($this->data)
				{
					case SYNCML_STATUS_OK:
						$somappings->update_mapping(
							$channel_id, $this->targetref, NULL, 0);
						break;
					case SYNCML_STATUS_ITEMADDED:
						// delete mapping now -- it will come back in MAP
						$somappings->delete_mapping(
							$channel_id, $this->targetref, NULL, NULL);
						break;
				}
			}
		}
	}
?>
