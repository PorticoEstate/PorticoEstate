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

	require_once 'inc/class.syncml_command_final.inc.php';
	require_once 'inc/class.syncml_command_synchdr.inc.php';
	require_once 'inc/class.syncml_command_status.inc.php';

	require_once 'inc/class.syncml_response.inc.php';
	require_once 'inc/class.syncml_session.inc.php';

	/**
	 * Represents a incoming message.
	 */
	class syncml_message
	{
		var $commands = array();

		function process_header($header)
		{
			$this->commands[] = new syncml_command_synchdr($header);
		}

		/**
		 * Build and save command objects from XML array.
		 *
		 * @param $body XML array of BODY element.
		 */
		function process_body($body)
		{
			if(isset($body['FINAL'][0]))
			{
				$this->commands[] = new syncml_command_final();
			}

			if(isset($body['STATUS']) && is_array($body['STATUS']))
			{
				for($i = 0; array_key_exists($i, $body['STATUS']); $i++)
				{
					$this->commands[] = new syncml_command_status(
						$body['STATUS'][$i]);
				}
			}

			if(isset($body[SYNCML_XML_ORIGINAL_ORDER]) &&
				is_array($body[SYNCML_XML_ORIGINAL_ORDER]))
			{
				foreach($body[SYNCML_XML_ORIGINAL_ORDER] as $command)
				{
					$command_obj = syncml_command::build($command);

					if($command_obj)
					{
						$this->commands[] = $command_obj;
					}
				}
			}
		}

		function execute(&$response)
		{
			$session = new syncml_session();

			// process every command in message

			for($i = 0, $c = count($this->commands); $i < $c; $i++)
			{
				$this->commands[$i]->execute($response, $session);
			}

			// output modifications in form of SYNC commands, but only when
			// client has sent all its modifications first

			if($session->get_var(SYNCML_NOMOREMODIFICATIONS))
			{
				$open_channels = $session->get_open_channels();
				
				$response->set_final(TRUE);

				foreach($open_channels as $open_channel)
				{
					$database = new syncml_database(
						$open_channel['channel_id']);
					
					if(!$open_channel['server_modifications_sent'])
					{
						/*
							This code is run *after* all modifications from
							client are sent and *before* any modfifications
							are sent by the server.
						*/

						// LUIDs modified by the *client* during this session
						$all_modified_luids = $session->get_all_modified_luids(
							$open_channel['channel_id']);

						// GUIDs modified by the *client* during this session
						$all_modified_guids = $session->get_all_modified_guids(
							$open_channel['channel_id']);

						// Bring all changed items from phpgw
						$database->merge_changes($all_modified_guids);

						switch($open_channel['type'])
						{
							case SYNCML_ALERT_SLOWSYNC:
								$this->prepare_slowsync(
									$open_channel['channel_id'],
									$all_modified_luids);
								break;
							case SYNCML_ALERT_REFRESHFROMSERVER:
							case SYNCML_ALERT_REFRESHFROMSERVERBYSERVER:
								$somappings->delete_mapping(
									$open_channel['channel_id'], NULL, NULL,
									NULL);
								break;
							case SYNCML_ALERT_REFRESHFROMCLIENT:
							case SYNCML_ALERT_REFRESHFROMCLIENTBYSERVER:
								$this->finish_client_refresh(
									$open_channel['channel_id'],
									$all_client_modified_luids, $database);
								break;
						}
					}

					if(!$open_channel['all_server_modifications_sent'])
					{
						$more_to_send = $this->_send_modifications(
							$response, $session, $open_channel, $database);

						$session->set_open_channel_property(
							$open_channel['source'], $open_channel['target'],
							'server_modifications_sent', TRUE);

						if($more_to_send)
						{
							// there's more to send from this channel. we
							// continue with that in next message.
							
							$response->set_final(FALSE);
							break;
						}
						else
						{
							$session->set_open_channel_property(
								$open_channel['source'],
								$open_channel['target'],
								'all_server_modifications_sent', TRUE);
						}
					}
				}
			}

			// do we have to send a ALERT for more messages?

			if($response->status_commands_only() && !$response->is_final())
			{
				$cmdid = $response->add_alert(SYNCML_ALERT_NEXTMESSAGE,
					array());
			}

			// save the anchors if this is last message in session

			if($response->status_commands_only())
			{
				$session->commit_anchors();
			}

			$session->commit();
		}

		/**
		 * Send modifications in open channels to client in form of SYNC
		 * commands.
		 *
		 * @return bool Returns true if there are more modifications to send
		 *              that didn't fit in this message.
		 */
		function _send_modifications(&$response, &$session, $open_channel,
			$database)
		{
			switch($open_channel['type'])
			{
				case SYNCML_ALERT_SLOWSYNC:
				case SYNCML_ALERT_TWOWAY:
				case SYNCML_ALERT_TWOWAYBYSERVER:
				case SYNCML_ALERT_ONEWAYFROMSERVER:
				case SYNCML_ALERT_ONEWAYFROMSERVERBYSERVER:
				case SYNCML_ALERT_REFRESHFROMSERVER:
				case SYNCML_ALERT_REFRESHFROMSERVERBYSERVER:
					return $this->_send_sync($response, $session, $database,
						$open_channel);
				case SYNCML_ALERT_REFRESHFROMCLIENT:
				case SYNCML_ALERT_REFRESHFROMCLIENTBYSERVER:
				case SYNCML_ALERT_ONEWAYFROMCLIENT:
				case SYNCML_ALERT_ONEWAYFROMCLIENTBYSERVER:
				default:
					return FALSE;
			}
		}

		/**
		 * Remove mappings for items not touched by the client so they can
		 * be sent like regular ADDs during next phase of the session.
		 *
		 * @param $channel_id                ID of channel to prepare the slow
		 *                                   sync for.
		 * @param $all_client_modified_luids All LUIDs client modified.
		 */
		function prepare_slowsync($channel_id, $all_client_modified_luids)
		{
			$somappings = new syncml_somappings();

			// get mappings to items not modified by client
			$luids_to_kill = array_diff(
				$somappings->get_all_mapped_luids($channel_id),
				$all_client_modified_luids);

			// remove mappings not modified by client
			foreach($luids_to_kill as $luid)
			{
				$somappings->delete_mapping($channel_id, $luid, NULL, NULL);
			}
		}

		/**
		 * Remove items not touched by the client.
		 * 
		 * @param $channel_id                ID of channel to prepare the slow
		 *                                   sync for.
		 * @param $all_client_modified_luids All LUIDs client modified.
		 * @param $database                  Database involved in this
		 *                                   session.
		 */
		function finish_client_refresh($channel_id, $all_client_modified_luids,
			$database)
		{
			$somappings = new syncml_somappings();

			$all_luids = $somappings->get_all_luids($channel_id);

			$luids_to_kill = array_diff(
				$all_client_modified_luids, $all_luids);

			foreach($luids_to_kill as $luid)
			{
				$database->delete_item($luid);
			}
		}

		/**
		 * Fetch modifications and insert a SYNC command.
		 * 
		 * @param $response     Response object to write SYNC command to.
		 * @param $session      Session object.
		 * @param $database     Database object to pull changes from.
		 * @param $open_channel Channel involved in this SYNC command.
		 */
		function _send_sync(&$response, &$session, $database, $open_channel)
		{
			$commands = array();

			// get server modification
			$more_to_send = $database->get_modifications(
				$response, $session, $commands, $open_channel['maxobjsize'],
				$response->get_size_left());

			// send modifications, which means all items.
			$response->add_sync(
				$open_channel['source'], $open_channel['target'],
				$commands, $session->get_var(SYNCML_SUPPORTNUMBEROFCHANGES),
				$open_channel['server_modifications_sent'] ?
					NULL : $database->count_modifications());

			return $more_to_send;
		}
	}
