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
	 * Manage input/output to/from the mappings table.
	 */
	class syncml_somappings
	{
		/**
		 * Get a mapping.
		 *
		 * @param $ch_id  Channel ID of mapping. NULL to include all.
		 * @param $luid   LUID of mapping. NULL to include all.
		 * @param $guid   GUID of mapping. NULL to include all.
		 * @param $flag   Flag of mapping. NULL to include all.
		 * @return array  Multidimensional array of mappings matched.
		 */
		function get_mapping($ch_id, $luid, $guid, $flag)
		{
			$GLOBALS['phpgw']->db->query('
				SELECT *
				FROM phpgw_syncml_mappings m
				WHERE ' .
					(!is_null($ch_id) ?
						'channel_id = \'' . $ch_id . '\' AND ' : '') .
					(!is_null($luid) ? 'luid = \'' . $luid . '\' AND ' : '') .
					(!is_null($guid) ? 'guid = \'' . $guid . '\' AND ' : '') .
					(!is_null($flag) ? 'dirty = \'' . $flag . '\' AND ' : '') .
					'1 = 1',
				__LINE__, __FILE__);

			$mappings = array();

			while($GLOBALS['phpgw']->db->next_record())
			{
				$mappings[] = array(
					0 => $GLOBALS['phpgw']->db->f('channel_id'),
					'channel_id' => $GLOBALS['phpgw']->db->f('channel_id'),
					1 => $GLOBALS['phpgw']->db->f('luid'),
					'luid' => $GLOBALS['phpgw']->db->f('luid'),
					2 => $GLOBALS['phpgw']->db->f('guid'),
					'guid' => $GLOBALS['phpgw']->db->f('guid'),
					3 => $GLOBALS['phpgw']->db->f('dirty'),
					'flag' => $GLOBALS['phpgw']->db->f('dirty')
				);
			}

			return $mappings;
		}
		
		/**
		 * Get all mapped LUIDs in this database.
		 *
		 * @param $ch_id Channel ID. NULL to include all.
		 * @return array List of LUID numbers of mappings to this channel.
		 */
		function get_all_mapped_luids($ch_id)
		{
			$mappings = $this->get_mapping($ch_id, NULL, NULL, NULL);

			$all_mapped_luids = array();

			foreach($mappings as $mapping)
			{
				$all_mapped_luids[] = $mapping['luid'];
			}

			return $all_mapped_luids;
		}		

		/**
		 * Delete a mapping.
		 *
		 * @param $ch_id  Channel ID of mapping. NULL to include all.
		 * @param $luid   LUID of mapping. NULL to include all.
		 * @param $guid   GUID of mapping. NULL to include all.
		 * @param $flag   Flag of mapping. NULL to include all.
		 * @return int    Number of mappings deleted.
		 */
		function delete_mapping($ch_id, $luid, $guid, $flag)
		{
			$GLOBALS['phpgw']->db->query('
				DELETE FROM phpgw_syncml_mappings
				WHERE ' .
					(!is_null($ch_id) ?
						'channel_id = \'' . $ch_id . '\' AND ' : '') .
					(!is_null($luid) ? 'luid = \'' . $luid . '\' AND ' : '') .
					(!is_null($guid) ? 'guid = \'' . $guid . '\' AND ' : '') .
					(!is_null($flag) ? 'dirty = \'' . $flag . '\' AND ' : '') .
					'1 = 1',
				__LINE__, __FILE__);

			return $GLOBALS['phpgw']->db->affected_rows();
		}

		/**
		 * Insert a mapping.
		 *
		 * @param $ch_id  Channel ID of mapping. NULL to include all.
		 * @param $luid   LUID of mapping. NULL to include all.
		 * @param $guid   GUID of mapping. NULL to include all.
		 * @param $flag   Flag of mapping. Optional. Defaults to 0.
		 */
		function insert_mapping($ch_id, $luid, $guid, $flag = 0)
		{
			$GLOBALS['phpgw']->db->query(sprintf("
				INSERT INTO phpgw_syncml_mappings(
					channel_id, luid, guid, dirty)
				VALUES('%d', '%s', '%s', '%d')",
				$ch_id, $luid, $guid, $flag),
				__LINE__, __FILE__);
		}

		/**
		 * Update a mapping.
		 *
		 * @param $ch_id  Channel ID of mapping. NULL to include all.
		 * @param $luid   LUID of mapping. NULL to include all.
		 * @param $guid   GUID of mapping. NULL to include all.
		 * @return int    Number of mappings updated.
		 */
		function update_mapping($ch_id, $luid, $guid, $flag)
		{
				syncml_logger::get_instance()->log("update_mapping($ch_id, $luid, $guid, $flag)");
				syncml_logger::get_instance()->log(" update_mapping :". '
				UPDATE phpgw_syncml_mappings SET dirty = \'' . intval($flag) . '\' WHERE ' .
					(!is_null($ch_id) ?
						'channel_id = \'' . $ch_id . '\' AND ' : '') .
					(!is_null($luid) ? 'luid = \'' . $luid . '\' AND ' : '') .
					(!is_null($guid) ? 'guid = \'' . $guid . '\' AND ' : '') .
					'1 = 1');
			$GLOBALS['phpgw']->db->query('
				UPDATE phpgw_syncml_mappings SET dirty = \'' . intval($flag) . '\' WHERE ' .
					(!is_null($ch_id) ?
						'channel_id = \'' . $ch_id . '\' AND ' : '') .
					(!is_null($luid) ? 'luid = \'' . $luid . '\' AND ' : '') .
					(!is_null($guid) ? 'guid = \'' . $guid . '\' AND ' : '') .
					'1 = 1',
				__LINE__, __FILE__);

			return $GLOBALS['phpgw']->db->affected_rows();
		}
	}
