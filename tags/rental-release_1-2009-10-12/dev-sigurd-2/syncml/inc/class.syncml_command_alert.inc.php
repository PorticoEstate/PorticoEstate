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

	require_once 'inc/class.sochannel.inc.php';
	require_once 'inc/class.sodatabase.inc.php';

	require_once 'inc/constants.inc.php';

	require_once 'inc/class.syncml_command.inc.php';

	/**
	 * Takes care of incoming ALERT commands.
	 */
	class syncml_command_alert extends syncml_command
	{
		function syncml_command_alert($xml_array = array())
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
		function execute(&$response, &$session)
		{
			if($response->has_global_status_code())
			{
				$response->add_status(
					$this->cmdid, $session->msgid, 'Alert',
					$this->item[0]['target']['locuri'],
					$this->item[0]['source']['locuri'],
					$response->get_global_status_code());
				return;
			}

			switch($this->data)
			{
				case SYNCML_ALERT_TWOWAY:
				case SYNCML_ALERT_SLOWSYNC:
				case SYNCML_ALERT_ONEWAYFROMCLIENT:
				case SYNCML_ALERT_REFRESHFROMCLIENT:
				case SYNCML_ALERT_ONEWAYFROMSERVER:
				case SYNCML_ALERT_REFRESHFROMSERVER:
					$this->_process_sync_init($response, $session);
					break;
				case SYNCML_ALERT_RESULTALERT:
					break;
				case SYNCML_ALERT_NEXTMESSAGE:
					break;
				case SYNCML_ALERT_NOENDOFDATA:
					break;
			}
		}

		function _process_sync_init(&$response, &$session)
		{
			$sodatabase = new syncml_sodatabase();
			$sochannel = new syncml_sochannel();

			syncml_logger::get_instance()->log_data("_process_sync_init item", $this->item[0]);
			$database = $sodatabase->get_database_by_uri(
				$this->item[0]['target']['locuri']);

			$database_id = $database['database_id'];
			$owner_id = $database['account_id'];
			syncml_logger::get_instance()->log("_process_sync_init user $database_id $owner_id ");
			$status = $this->validate_database(
				$database_id, $owner_id, $session->account_id);
			syncml_logger::get_instance()->log("_process_sync_init status $status ");

			if(!$status)
			{
				list($channel_id, $device_last, $phpgw_last) =
					$sochannel->get_channel_by_database_and_device(
						$this->item[0]['target']['locuri'],
						$session->get_var('device_uri'));

				$status = $this->validate_channel($device_last);
			}
			syncml_logger::get_instance()->log_data("_process_sync_init status", $status);

			$response->add_status_with_anchor(
				$this->cmdid, $session->msgid, 'Alert',
				$this->item[0]['target']['locuri'],
				$this->item[0]['source']['locuri'],
				$status[0], $this->item[0]['meta']['anchor']['next']
			);

			if($status[0] == SYNCML_STATUS_OK ||
				$status[0] == SYNCML_STATUS_REFRESHREQUIRED)
			{
			syncml_logger::get_instance()->log_data("_process_sync_init ok channel ", $channel_id);
				if(!$channel_id)
				{
					$channel_id = $sochannel->insert_channel(
						$database_id, $session->get_var('device_uri'));
				}

			syncml_logger::get_instance()->log_data("_process_sync_init channel ", $channel_id);

				$database = new syncml_database($channel_id);
				$database->merge_changes();
				unset($database);

				// todo: make iso 8601 instead.
				$phpgw_next = time();

				// save phpgw and device next anchors
				$session->save_next_device_anchor(
					$channel_id, $this->item[0]['meta']['anchor']['next']);

				$session->save_next_phpgw_anchor($channel_id, $phpgw_next);

				// save this suggested sync
				$session->set_open_channel(
					$this->item[0]['target']['locuri'],
					$this->item[0]['source']['locuri'],
					$channel_id, $status[1],
					min(
						isset($this->item[0]['meta']['maxobjsize']) ?
							$this->item[0]['meta']['maxobjsize'] : 0,
						SYNCML_MAXOBJSIZE)
				);

				// output alert with phpgw next anchor
				$cmdid = $response->add_alert(
					$status[1],
					array(
						'trg_uri' => $this->item[0]['source']['locuri'],
						'src_uri' => $this->item[0]['target']['locuri'],
						'meta' => array(
							'last' => empty($phpgw_last) ?
								NULL : $phpgw_last,
							'next' => $phpgw_next,
							'maxobjsize' => SYNCML_MAXOBJSIZE
						)
					),
					(bool) $session->get_var(SYNCML_SUPPORTLARGEOBJS)
				);
			}
		}

		function validate_database($database_id, $owner_id, $session_owner_id)
		{
			if(!$database_id)
			{
				return array(SYNCML_STATUS_NOTFOUND, 0);
			}
			else if($owner_id != $session_owner_id)
			{
				return array(SYNCML_STATUS_FORBIDDEN, 0);
			}
			else
			{
				return FALSE;
			}
		}

		/**
		 * Compare sync anchors in ITEM with channel of device URI and database
		 * URI in ITEM.
		 *
		 * @param $device_source_locuri Device's LOCURI.
		 * @param $item                 ITEM element of ALERT command.
		 * @return array                Array containing STATUS response and
		 *                              ALERT command reply code.
		 */
		function validate_channel($device_last)
		{
			$last = isset($this->item[0]['meta']['anchor']['last']) ?
				$this->item[0]['meta']['anchor']['last'] : NULL;

			if($last !== $device_last)
			{
				// sanity check failed. suggest slow sync if not already
				// suggested.

				if((int)($this->data) != SYNCML_ALERT_SLOWSYNC)
				{
					return array(
						SYNCML_STATUS_REFRESHREQUIRED,
						SYNCML_ALERT_SLOWSYNC);
				}

				return array(
					SYNCML_STATUS_OK,
					SYNCML_ALERT_SLOWSYNC);
			}
			else
			{
				// everything is fine. accept sync alert.
				// but we only do slowsync and twoway right now.

				switch((int)($this->data))
				{
					case SYNCML_ALERT_SLOWSYNC:
					case SYNCML_ALERT_TWOWAY:
						return array(
							SYNCML_STATUS_OK,
							(int)($this->data));
					default:
						return array(
							SYNCML_STATUS_REFRESHREQUIRED,
							SYNCML_ALERT_SLOWSYNC);
				}
			}
		}
	}
?>
