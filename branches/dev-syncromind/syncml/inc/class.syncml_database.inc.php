<?php
	/**
	 * phpGroupWare (http://phpgroupware.org/)
	 * SyncML interface
	 *
	 * @author		Johan Gunnarsson <johang@phpgroupware.org>
	 * @copyright	Copyright (c) 2007 Free Software Foundation, Inc.
	 * @license		GNU General Public License 3 or later
	 * @package		phpgroupware
	 * @subpackage	syncml
	 * @version		$Id$
	 */

	phpgw::import_class('syncml.somappings');

	phpgw::import_class('syncml.sochannel');

	/**
	 * Represents a SyncML database. This class wraps calls to the IPC classes
	 * in each source/app.
	 */
	class syncml_database
	{
		/**
		 * @var object $ipc IPC object of this database's source module.
		 * @access private
		 */
		var $ipc;

		/**
		 * @var $somappings SO object for mappings.
		 * @access private
		 */
		var $somappings;

		/**
		 * @var int $channel_id ID of channel.
		 * @access private
		 */
		var $channel_id;

		/**
		 * @var string $mime_type Preferred mime type of source.
		 * @access private
		 */
		var $mime_type;

		/**
		 * @var string $mime_version Preferred mime version of source.
		 * @access private
		 */
		var $mime_version;

		/**
		 * Creates a database object from channel ID.
		 *
		 * @param int $channel_id ID number of synchronization channel.
		 */
		function syncml_database($channel_id)
		{
			$this->channel_id = $channel_id;

			$ipc_manager = CreateObject('phpgwapi.ipc_manager');
			$this->somappings = new syncml_somappings();

			list($module, $this->mime_type, $this->mime_version) =
				$this->get_source_data($channel_id);

			$this->ipc = $ipc_manager->getipc($module);
		}

		/**
		 * Get name of module related to a channel.
		 *
		 * @param int $channel_id Channel ID.
		 * @return string         Name of module.
		 */
		function get_source_data($channel_id)
		{
			$GLOBALS['phpgw']->db->query(sprintf('
				SELECT
					s.modulename as modulename,
					s.mimetype as mimetype,
					s.mimeversion as mineversion
				FROM
					phpgw_syncml_sources s,
					phpgw_syncml_channels c,
					phpgw_syncml_databases d
				WHERE
					c.channel_id = %d AND
					c.database_id = d.database_id AND
					d.source_id = s.source_id',
				$channel_id),
				__LINE__, __FILE__);
				
			$GLOBALS['phpgw']->db->next_record();
			
			return array(
				$GLOBALS['phpgw']->db->f('modulename'),
				$GLOBALS['phpgw']->db->f('mimetype'),
				$GLOBALS['phpgw']->db->f('mimeversion')
			);
		}

		/**
		 * Get type of data used to modify this database.
		 *
		 * @return string Mime type.
		 */
		function get_type()
		{
			return $this->mime_type;
		}

		/**
		 * Get all items in this database.
		 *
		 * @return array List of ID numbers of changed items.
		 */
		function get_all_items()
		{
			return $this->ipc->getidlist();
		}

		/**
		 * Merge information about changes from phpgw to the mappings table.
		 *
		 * @param $except_list Array of GUIDs not to merge.
		 * @return int         Number of changes merged.
		 */
		function merge_changes($except_list = array())
		{
				syncml_logger::get_instance()->log_data("merge_changes : ipc set  ", is_object($this->ipc)? 'true' : 'false');
			if(!$this->ipc)
			{
				return 0;
			}

			$sochannel = new syncml_sochannel();

			list(, , , $last_merge) = $sochannel->get_channel($this->channel_id);
			syncml_logger::get_instance()->log_data("merge_changes last_merge : ", $last_merge);

			$changed_guids = array_diff(
				$this->ipc->getIdList($last_merge), $except_list);
			syncml_logger::get_instance()->log_data("merge_changes changed_guids : ", $changed_guids);

			foreach($changed_guids as $guid)
			{
			syncml_logger::get_instance()->log_data("merge_changes guid : ", $guid);
				$this->somappings->update_mapping(
					$this->channel_id, NULL, $guid, 1);
			syncml_logger::get_instance()->log_data("merge_changes update_mapping done  for guids : ", $guid);
			}

			$sochannel->update_last_merge($this->channel_id);
			syncml_logger::get_instance()->log("merge_changes update_last_merge done for ".$this->channel_id);
		}

		/**
		 * Get item from database.
		 *
		 * @param string  GUID of item to get.
		 * @return string Data in format defined in TYPE instance variable.
		 */
		function get_item_by_guid($uri, $type)
		{
			return $this->ipc->getdata($uri, $type);
		}

		/**
		 * Add item to database.
		 *
		 * @param string $luid     LUID of item to add.
		 * @param string $data     Data of item to add.
		 * @param string $type     Type of data to add.
		 * @param string $override Override conflicts or not.
		 * @return int             Status code.
		 */
		function add_item($luid, $data, $type, $override = FALSE)
		{
			list($status_code, $guid) =
				$this->replace_item($luid, $data, $type, $override);

			switch($status_code)
			{
				case SYNCML_STATUS_OK:
				case SYNCML_STATUS_ITEMADDED:
					return array(SYNCML_STATUS_ITEMADDED, $guid);
				case SYNCML_STATUS_CONFLICTRESOLVEDWITHDUPLICATE:
					return array(
						SYNCML_STATUS_CONFLICTRESOLVEDWITHDUPLICATE, $guid);
			}
		}

		/**
		 * Replace item in database.
		 *
		 * @param $luid string   LUID URI of item to replace.
		 * @param $data string   Raw data to insert.
		 * @param $type string   Mime type of data.
		 * @param $override bool Override any conflict.
		 * @return int           0 if conflict, 1 if replace, 2 if add.
		 */
		function replace_item($luid, $data, $type, $override = FALSE)
		{
			$mappings = $this->somappings->get_mapping(
				$this->channel_id, $luid, NULL, NULL);

			$dirty = FALSE;

			if(count($mappings) > 0)
			{
				list(list(,, $guid, $dirty)) = $mappings;
			}

			if(!$override && $dirty)
			{
				$new_guid = $this->ipc->adddata($data, $type);

				// we want to add the old item to our client, by
				// removing its mapping.
				$this->somappings->delete_mapping(
					$this->channel_id, $luid, $guid, NULL);

				// we don't want this item back, so we insert a mapping.
				$this->somappings->insert_mapping(
					$this->channel_id, $luid, $new_guid, 0);

				return array(SYNCML_STATUS_CONFLICTRESOLVEDWITHDUPLICATE,
					$guid);
			}
			else if(count($mappings) > 0)
			{
				$this->ipc->replacedata($guid, $data, $type);

				$this->somappings->update_mapping(
					$this->channel_id, $guid, NULL, 0);

				return array(SYNCML_STATUS_OK, $guid);
			}
			else
			{
				$guid = $this->ipc->adddata($data, $type);

				$this->somappings->insert_mapping(
					$this->channel_id, $luid, $guid, 0);

				return array(SYNCML_STATUS_ITEMADDED, $guid);
			}
		}

		/**
		 * Delete item from database.
		 *
		 * @param $soft bool  Soft deletion?
		 * @param $uri string URI of item to delete.
		 * @return bool       True if item was deleted, false otherwise.
		 */
		function delete_item($luid, $soft = False)
		{
			$mappings = $this->somappings->get_mapping(
				$this->channel_id, $luid, NULL, NULL);

			if(count($mappings) > 0)
			{
				list(list(,, $guid, $dirty)) = $mappings;

				// here, we don't care about conflicts. delete even if there
				// are changes on phpgw side.

				$deletes = $this->ipc->removedata($guid);

				$this->somappings->delete_mapping(
					$this->channel_id, $luid, NULL, NULL);

				return array($deletes > 0, $guid);
			}

			return array(0, NULL);
		}

		/**
		 * Get added items in this database since last sync from this
		 * channel.
		 *
		 * @return array List of ID numbers of added items.
		 */
		function get_added_items()
		{
			$all_guids = $this->ipc->getidlist();

			// get *all* mappings
			$mappings = $this->somappings->get_mapping(
				$this->channel_id, NULL, NULL, NULL);

			$mapped_guids = array();

			foreach($mappings as $m)
			{
				$mapped_guids[] = $m['guid'];
			}

			return array_values(array_diff($all_guids, $mapped_guids));
		}

		/**
		 * Get changed items in this database since last sync from this
		 * channel.
		 *
		 * @return array List of ID numbers of changed items.
		 */
		function get_changed_items()
		{
			$all_guids = $this->ipc->getidlist();

			// get *dirty* mappings
			$mappings = $this->somappings->get_mapping(
				$this->channel_id, NULL, NULL, 1);

			$guid_to_luid = array();
			$mapped_guids = array();

			foreach($mappings as $m)
			{
				$guid_to_luid[$m['guid']] = $m['luid'];
				$mapped_guids[] = $m['guid'];
			}

			$changed_guids = array_intersect($all_guids, $mapped_guids);

			$changed_luids = array();

			foreach($changed_guids as $guid)
			{
				$changed_luids[] = array(
					'luid' => $guid_to_luid[$guid],
					'guid' => $guid);
			}

			return $changed_luids;
		}

		/**
		 * Get deleted items in this database since last sync from this
		 * channel.
		 *
		 * @return array List of ID numbers of deleted items.
		 */
		function get_deleted_items()
		{
			$all_guids = $this->ipc->getidlist();

			// get *all* mappings
			$mappings = $this->somappings->get_mapping(
				$this->channel_id, NULL, NULL, NULL);

			$guid_to_luid = array();
			$mapped_guids = array();

			foreach($mappings as $m)
			{
				$guid_to_luid[$m['guid']] = $m['luid'];
				$mapped_guids[] = $m['guid'];
			}

			$changed_guids = array_diff($mapped_guids, $all_guids);

			$changed_luids = array();

			foreach($changed_guids as $guid)
			{
				$changed_luids[] = $guid_to_luid[$guid];
			}

			return $changed_luids;
		}

		/**
		 * Count number of modifications to be sent by server.
		 *
		 * @return int Number of modifications.
		 */
		function count_modifications()
		{
			return count($this->get_added_items()) +
				count($this->get_changed_items()) +
				count($this->get_deleted_items());
		}

		/**
		 * Get all pending modifications on phpgw side in form of DELETE,
		 * REPLACE and ADD commands. This method generates XML and respects
		 * max message size and max object size.
		 *
		 * @return array Array containing the three types of modifications.
		 */
		function get_modifications(&$response, &$session, &$commands,
			$maxobjsize, $size_left)
		{
			foreach($this->get_added_items() as $guid)
			{
				if($size_left <= 0)
				{
					return TRUE;
				}

				$data = $this->get_item_by_guid($guid, $this->mime_type);

				list($chunk, $is_first, $is_last) = $this->get_chunk(
					$data, $session, min($maxobjsize, $size_left));

				$commands[] = $last_command = $response->build_add(
					array(
						'type' => $this->mime_type,
						'size' => $is_first && !$is_last ?
							strlen($data) : NULL),
					array(
						'data' => $chunk,
						'src_uri' => $guid
					),
					$is_last
				);

				$size_left -= strlen($last_command);

				$session->set_channel_id_from_cmd(
					$response->get_cmdid(), $this->channel_id);

				if(!$is_last)
				{
					return TRUE;
				}
			}

			foreach($this->get_deleted_items() as $luid)
			{
				if($size_left <= 0)
				{
					return TRUE;
				}

				$commands[] = $last_command = $response->build_delete(
					array(
						'trg_uri' => $luid
					)
				);

				$size_left -= strlen($last_command);

				$session->set_channel_id_from_cmd(
					$response->get_cmdid(), $this->channel_id);
			}

			foreach($this->get_changed_items() as $replace)
			{
				if($size_left <= 0)
				{
					return TRUE;
				}

				$data = $this->get_item_by_guid(
					$replace['guid'], $this->mime_type);

				list($chunk, $is_first, $is_last) = $this->get_chunk(
					$data, $session, min($maxobjsize, $size_left));

				$commands[] = $last_command = $response->build_replace(
					array(
						'type' => $this->mime_type,
						'size' => $is_first && !$is_last ?
							strlen($data) : NULL),
					array(
						'data' => $chunk,
						'trg_uri' => $replace['luid']
					),
					$is_last
				);

				$size_left -= strlen($last_command);

				$session->set_channel_id_from_cmd(
					$response->get_cmdid(), $this->channel_id);

				if(!$is_last)
				{
					return TRUE;
				}
			}

			return FALSE;
		}

		/**
		 * Chunk a piece of data and save the state of the chunking to session.
		 *
		 * @param $data         Data to chunk.
		 * @param $session      Session object.
		 * @param $maxchunksize Max size of this chunk.
		 * @return array        The chunk of data (string), weather this is
		 *                      first chunk (bool), weather this is last
		 *                      chunk (bool).
		 */
		function get_chunk($data, &$session, $maxchunksize)
		{
			if(!$session->get_var(SYNCML_SUPPORTLARGEOBJS))
			{
				return array($data, true, true);
			}

			// offset is start of this chunk

			$offset = (int) $session->get_var(SYNCML_ITEMOFFSET);

			$next_offset = (strlen($data) > $offset + $maxchunksize) ?
				$offset + $maxchunksize : 0;

			$session->set_var(SYNCML_ITEMOFFSET, $next_offset);

			if($maxchunksize > 0)
			{
				return array(
					substr($data, $offset, $maxchunksize),
					$offset == 0, $next_offset == 0
				);
			}
			else
			{
				return array(
					substr($data, $offset),
					$offset == 0, $next_offset == 0
				);
			}
		}
	}
