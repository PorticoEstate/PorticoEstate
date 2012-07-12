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
	 * Takes care of incoming SYNC commands.
	 */
	class syncml_command_sync extends syncml_command
	{
		function syncml_command_sync($xml_array = array())
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
					$this->cmdid, $session->msgid, 'Sync',
					NULL, NULL,
					$response->get_global_status_code());
			}
			else
			{
				$response->add_status(
					$this->cmdid, $session->msgid, 'Sync',
					$this->target['locuri'], $this->source['locuri'],
					SYNCML_STATUS_OK);
			}

			$open_channel = $session->get_open_channel(
				$this->target['locuri'], $this->source['locuri']);

			if($open_channel)
			{
				$database = new syncml_database($open_channel['channel_id']);
			}
			else
			{
				$database = NULL;
				
				// todo: break execution here.
			}

			foreach($this->_modifications as $modification)
			{
				$modification->execute($response, $session, $database);
			}

			if($response->is_final())
			{
				$session->set_var(SYNCML_NOMOREMODIFICATIONS, TRUE);
			}
		}
	}
?>
