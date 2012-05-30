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

	class syncml_sochannel
	{
		function insert_channel($database_id, $device_uri)
		{
				syncml_logger::get_instance()->log("insert_channel($database_id,$device_uri)");
				syncml_logger::get_instance()->log("insert_channel :" .sprintf('
						                INSERT INTO phpgw_syncml_channels(database_id, device_uri, last_merge) VALUES(\'%d\', \'%s\', 0)', $database_id, $device_uri));
			$GLOBALS['phpgw']->db->query(sprintf('
				INSERT INTO phpgw_syncml_channels(database_id, device_uri, last_merge) VALUES( \'%d\', \'%s\', 0)',
				$database_id, $device_uri),
				__LINE__, __FILE__);
				syncml_logger::get_instance()->log('insert_channel done');

			return $GLOBALS['phpgw']->db->get_last_insert_id(
				'phpgw_syncml_channel', 'channel_id');
		}

		function update_last_merge($channel_id)
		{
			$GLOBALS['phpgw']->db->query(sprintf('
				UPDATE phpgw_syncml_channels
				SET last_merge = %d
				WHERE channel_id = %d',
				time(), $channel_id),
				__LINE__, __FILE__);
		}

		function update_anchors($channel_id, $device_next, $phpgw_next)
		{
			$GLOBALS['phpgw']->db->query('
				UPDATE phpgw_syncml_channels
				SET' .
					(is_null($device_next) ?
						'device_anchor_last = \'' . $device_next . '\',' : '') .
					(is_null($phpgw_next) ?
						'phpgw_anchor_last = \'' . $phpgw_next . '\'' : '') .
				'WHERE
					channel_id = ' . intval($channel_id),
				__LINE__, __FILE__);

			return $GLOBALS['phpgw']->db->_affectedrows() > 0;
		}

		function get_channel($channel_id)
		{
			$GLOBALS['phpgw']->db->query(sprintf('
				SELECT
					c.device_anchor_last,
					c.phpgw_anchor_last,
					c.database_id,
					c.last_merge
				FROM phpgw_syncml_channels c
				WHERE
					c.channel_id = %d',
				$channel_id),
				__LINE__, __FILE__);

			$GLOBALS['phpgw']->db->next_record();

			return array(
				$GLOBALS['phpgw']->db->f('database_id'),
				$GLOBALS['phpgw']->db->f('device_anchor_last'),
				$GLOBALS['phpgw']->db->f('phpgw_anchor_last'),
				$GLOBALS['phpgw']->db->f('last_merge')
			);
		}

		function get_channel_by_database_and_device($database_uri, $device_uri)
		{
			$GLOBALS['phpgw']->db->query(sprintf("
				SELECT
					c.channel_id,
					c.device_anchor_last,
					c.phpgw_anchor_last
				FROM phpgw_syncml_channels c
				JOIN phpgw_syncml_databases d ON
					d.database_id = c.database_id AND
					d.database_uri = '%s'
				WHERE c.device_uri = '%s'",
				$GLOBALS["phpgw"]->db->db_addslashes($database_uri),
				$GLOBALS["phpgw"]->db->db_addslashes($device_uri)),
				__LINE__, __FILE__);

			$GLOBALS['phpgw']->db->next_record();

			return array(
				$GLOBALS['phpgw']->db->f('channel_id'),
				$GLOBALS['phpgw']->db->f('device_anchor_last'),
				$GLOBALS['phpgw']->db->f('phpgw_anchor_last'),
			);
		}
		
		function set_anchors($channel_id, $device_next, $phpgw_next)
		{
			$GLOBALS['phpgw']->db->query("
				UPDATE phpgw_syncml_channels
				SET " .
					($device_next ?
						"device_anchor_last = '" . $device_next . "'," : "") .
					($phpgw_next ?
						"phpgw_anchor_last = '" . $phpgw_next . "'" : "") .
				" WHERE
					channel_id = " . intval($channel_id),
				__LINE__, __FILE__);
		}
	}
