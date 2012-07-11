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

	require_once 'inc/class.sochannel.inc.php';

	/**
	 * Maps SyncML sessions to phpgw sessions.
	 */
	class syncml_session
	{
		/**
		 * Session data cache.
		 */
		var $session_data; /* = array(
			'sent_commands' => array(),
			'open_channels' => array(),
			'modified_luids' => array()
		);*/

		var $id;

		var $next_nonce;

		/**
		 * Message ID of current message.
		 */
		// todo: refactor out to syncml_message.
		var $msgid;

		/**
		 * Account ID for current authenticated user.
		 */
		// todo: this property is redundant.
		var $account_id;

		function syncml_session()
		{
		}

		function is_authenticated()
		{
			return !empty($this->account_id);
		}

		function get_account_id()
		{
			return $this->account_id;
		}

		/**
		 * Returns ID of a channel that a previously sent command corresponded
		 * to.
		 *
		 * @param int Command ID of previously sent command.
		 * @return mixed NULL if command ID wasn't recognized, integer command
		 *               ID on success.
		 */
		function get_channel_id_from_cmd($cmdid)
		{
			if(!is_array($this->session_data) &&
				!isset($this->session_data['sent_commands'][$cmdid]))
			{
				return NULL;
			}

			return $this->session_data['sent_commands'][$cmdid];
		}

		function set_channel_id_from_cmd($cmdid, $channel_id)
		{
			return $this->session_data['sent_commands'][$cmdid] = $channel_id;
		}

		function save_next_device_anchor($channel_id, $next_anchor)
		{
			$this->save_next_anchor($channel_id, $next_anchor, 'device');
		}

		function save_next_phpgw_anchor($channel_id, $next_anchor)
		{
			$this->save_next_anchor($channel_id, $next_anchor, 'phpgw');
		}

		function save_next_anchor($channel_id, $next_anchor, $type)
		{
			$next_anchors = $this->get_var('next_anchors');

			if(!is_array($next_anchors))
			{
				$next_anchors = array();
			}

			$next_anchors[$channel_id][$type] = $next_anchor;

			$this->set_var('next_anchors', $next_anchors);
		}

		function commit_anchors()
		{
			$next_anchors = $this->get_var('next_anchors');

			if(is_array($next_anchors))
			{
				foreach($next_anchors as $channel_id => $nexts)
				{
					$sochannel = new syncml_sochannel();

					$sochannel->set_anchors(
						$channel_id,
						isset($nexts['device']) ? $nexts['device'] : NULL,
						isset($nexts['phpgw']) ? $nexts['phpgw'] : NULL);
				}
			}
		}

		function save_modified_luid($channel_id, $luid)
		{
			if(!isset($this->session_data['modified_luids']))
			{
				$this->session_data['modified_luids'] = array();
			}

			$this->session_data['modified_luids'][$channel_id][] = $luid;
		}

		function get_all_modified_luids($channel_id)
		{
			if(!isset($this->session_data['modified_luids'][$channel_id]))
			{
				return array();
			}

			return $this->session_data['modified_luids'][$channel_id];
		}

		function save_modified_guid($channel_id, $guid)
		{
			if(!isset($this->session_data['modified_guids']))
			{
				$this->session_data['modified_guids'] = array();
			}

			$this->session_data['modified_guids'][$channel_id][] = $guid;
		}

		function get_all_modified_guids($channel_id)
		{
			if(!isset($this->session_data['modified_guids'][$channel_id]))
			{
				return array();
			}

			return $this->session_data['modified_guids'][$channel_id];
		}

		/**
		 * Returns array with open channels and their channel ID, sync type,
		 * source database and target database.
		 *
		 * @return array Open channels.
		 */
		function get_open_channels()
		{
			if(!isset($this->session_data['open_channels']))
			{
				return array();
			}

			return array_values($this->session_data['open_channels']);
		}

		function set_open_channel($source, $target, $channel_id, $type,
			$maxobjsize)
		{
			$this->session_data['open_channels'][md5($source . $target)] =
				array(
					'channel_id' => $channel_id,
					'type' => $type,
					'source' => $source,
					'target' => $target,
					'all_server_modifications_sent' => FALSE,
					'server_modifications_sent' => FALSE,
					'maxobjsize' => (int) $maxobjsize
				);
		}

		function set_open_channel_property($source, $target, $key, $value)
		{
			$this->session_data['open_channels'][md5($source . $target)]
				[$key] = $value;
		}

		function get_open_channel($source, $target)
		{
			if(!isset($this->session_data['open_channels']
				[md5($source . $target)]))
			{
				return NULL;
			}

			return $this->session_data['open_channels']
				[md5($source . $target)];
		}

		function append_var($key, $value)
		{
			if(!isset($this->session_data[$key]) ||
				!is_string($this->session_data[$key]))
			{
				$this->session_data[$key] = '';
			}

			$this->session_data[$key] .= $value;
		}

		function set_var($key, $value)
		{
			$this->session_data[$key] = $value;
		}

		function unset_var($key)
		{
			unset($this->session_data[$key]);
		}

		function get_var($key)
		{
			if(!isset($this->session_data[$key]))
			{
				return NULL;
			}
			else
			{
				return $this->session_data[$key];
			}
		}

		/**
		 * Save changes on session data back to storage.
		 *
		 * @access public
		 */
		 function commit()
		 {
			syncml_logger::get_instance()->log_data(
				"saved session data", $this->session_data);

			$GLOBALS['phpgw']->session->appsession(
				'session_data', 'syncml', $this->session_data);

			$sosession = new syncml_sosession();

			$sosession->set_next_nonce($this->id, $this->next_nonce);
		 }
	}
?>
