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

	require_once 'inc/class.syncml_command_alert.inc.php';
	require_once 'inc/class.syncml_command_put.inc.php';
	require_once 'inc/class.syncml_command_get.inc.php';
	require_once 'inc/class.syncml_command_map.inc.php';
	require_once 'inc/class.syncml_command_sync.inc.php';

	require_once 'inc/class.syncml_command_add.inc.php';
	require_once 'inc/class.syncml_command_replace.inc.php';
	require_once 'inc/class.syncml_command_delete.inc.php';

	/**
	 * Parent class for SyncML commands.
	 */
	class syncml_command
	{
		/**
		 * Build a command object from XML data.
		 *
		 * @param $xml_array XML array data of command.
		 * @return object    Command object. Returns NULL if command was not
		 *                   recognized.
		 */
		function build($xml_array)
		{
			if(isset($xml_array[SYNCML_XML_TAG_NAME]) &&
				$xml_array[SYNCML_XML_TAG_NAME])
			{
				switch($xml_array[SYNCML_XML_TAG_NAME])
				{
					case 'ALERT':
					case 'PUT':
					case 'GET':
					case 'MAP':
					case 'SYNC':
					case 'STATUS':
						$class_name = 'syncml_command_' .
							strtolower($xml_array[SYNCML_XML_TAG_NAME]);
						return new $class_name($xml_array);
				}
			}

			return NULL;
		}

		/**
		 * Rip out and shuffle around interesting command data from the
		 * XML array. According to SyncML 1.1 DTD. This makes the subclasses
		 * much cleaner.
		 *
		 * @param $xml_array XML array data.
		 */
		function parse_xml_array($xml_array)
		{
			// CmdID
			if(isset($xml_array['CMDID'][0][SYNCML_XML_DATA]))
			{
				$this->cmdid = $xml_array['CMDID'][0][SYNCML_XML_DATA];
			}

			// Cmd
			if(isset($xml_array['CMD'][0][SYNCML_XML_DATA]))
			{
				$this->cmd = $xml_array['CMD'][0][SYNCML_XML_DATA];
			}

			// MsgRef?
			if(isset($xml_array['MSGREF'][0][SYNCML_XML_DATA]))
			{
				$this->msgref = $xml_array['MSGREF'][0][SYNCML_XML_DATA];
			}

			// CmdRef
			if(isset($xml_array['CMDREF'][0][SYNCML_XML_DATA]))
			{
				$this->cmdref = $xml_array['CMDREF'][0][SYNCML_XML_DATA];
			}

			// TargetRef?
			if(isset($xml_array['TARGETREF'][0][SYNCML_XML_DATA]))
			{
				$this->targetref = $xml_array['TARGETREF'][0][SYNCML_XML_DATA];
			}

			// SourceRef
			if(isset($xml_array['SOURCEREF'][0][SYNCML_XML_DATA]))
			{
				$this->sourceref = $xml_array['SOURCEREF'][0][SYNCML_XML_DATA];
			}

			// Target?
			if(isset($xml_array['TARGET'][0]))
			{
				$this->target = array(
					'locuri' => @$xml_array['TARGET'][0]['LOCURI'][0]
						[SYNCML_XML_DATA]
				);

				if(isset($xml_array['TARGET'][0]['LOCNAME']) &&
					is_array($xml_array['TARGET'][0]['LOCNAME']))
				{
					$this->target['locname'] =
						$xml_array['TARGET'][0]['LOCNAME'][0]
						[SYNCML_XML_DATA];
				}
			}

			// Source?
			if(isset($xml_array['SOURCE'][0]))
			{
				$this->source = array(
					'locuri' => @$xml_array['SOURCE'][0]['LOCURI'][0]
						[SYNCML_XML_DATA]
				);

				if(isset($xml_array['SOURCE'][0]['LOCNAME'][0]))
				{
					$this->source['locname'] =
						$xml_array['SOURCE'][0]['LOCNAME'][0]
						[SYNCML_XML_DATA];
				}
			}

			// Data?
			if(isset($xml_array['DATA'][0][SYNCML_XML_DATA]))
			{
				$this->data = $xml_array['DATA'][0][SYNCML_XML_DATA];
			}

			// NoResp?
			if(isset($xml_array['NORESP']))
			{
				$this->noresp = NULL;
			}

			// (Add | Atomic | Copy | Delete | Sequence | Replace)*
			if(isset($xml_array[SYNCML_XML_ORIGINAL_ORDER]) &&
				is_array($xml_array[SYNCML_XML_ORIGINAL_ORDER]))
			{
				$this->_modifications = array();

				foreach($xml_array[SYNCML_XML_ORIGINAL_ORDER] as $command)
				{
					switch($command[SYNCML_XML_TAG_NAME])
					{
						case 'ADD':
						case 'DELETE':
						case 'REPLACE':
							$class_name = 'syncml_command_' .
								strtolower($command[SYNCML_XML_TAG_NAME]);
							$this->_modifications[] =
								new $class_name($command);
					}
				}
			}

			// Meta?
			if(isset($xml_array['META'][0]))
			{
				$this->meta = $this->parse_meta($xml_array['META'][0]);
			}

			// Chal?
			if(isset($xml_array['CHAL'][0]))
			{
				$this->chal = array();

				// Meta?
				if(isset($xml_array['CHAL'][0]['META'][0]))
				{
					$this->chal['meta'] = $this->parse_meta(
						$xml_array['CHAL'][0]['META'][0]);
				}
			}

			// Cred?
			if(isset($xml_array['CRED'][0]))
			{
				$this->cred = array();

				// Meta?
				if(isset($xml_array['CRED'][0]['META'][0]))
				{
					$this->cred['meta'] = $this->parse_meta(
						$xml_array['CRED'][0]['META'][0]);
				}

				// Data
				$this->cred['data'] = @$xml_array['CRED'][0]['DATA'][0]
					[SYNCML_XML_DATA];
			}

			// MapItem*
			if(isset($xml_array['MAPITEM']) && is_array($xml_array['MAPITEM']))
			{
				$this->mapitem = $this->parse_items($xml_array['MAPITEM']);
			}

			// Item*
			if(isset($xml_array['ITEM']) && is_array($xml_array['ITEM']))
			{
				$this->item = $this->parse_items($xml_array['ITEM']);
			}
		}

		/**
		 * Parse ITEM elements. This function takes *many* elements.
		 *
		 * @param $xml_array XML array data.
		 */
		function parse_items($xml_array)
		{
			$itemx = array();

			foreach($xml_array as $item)
			{
				$tmp = array();

				// Target?
				if(isset($item['TARGET'][0]))
				{
					$tmp['target'] = array(
						'locuri' => @$item['TARGET'][0]['LOCURI'][0]
							[SYNCML_XML_DATA]
					);

					if(isset($item['TARGET'][0]['LOCNAME']) &&
						is_array($item['TARGET'][0]['LOCNAME']))
					{
						$tmp['target']['locname'] =
							$item['TARGET'][0]['LOCNAME'][0]
							[SYNCML_XML_DATA];
					}
				}

				// Source?
				if(isset($item['SOURCE'][0]))
				{
					$tmp['source'] = array(
						'locuri' => @$item['SOURCE'][0]['LOCURI'][0]
							[SYNCML_XML_DATA]
					);

					if(isset($item['SOURCE'][0]['LOCNAME'][0]))
					{
						$tmp['source']['locname'] =
							$item['SOURCE'][0]['LOCNAME'][0]
							[SYNCML_XML_DATA];
					}
				}

				// Meta?
				if(isset($item['META'][0]) && is_array($item['META'][0]))
				{
					$tmp['meta'] = $this->parse_meta($item['META'][0]);
				}

				// Data?
				if(isset($item['DATA']))
				{
					/**
					 * This one is special since we don't know if it
					 * contains more XML (as in devinf PUT/RESULTSs) or
					 * CDATA. So I just save the raw data and deal with
					 * it later.
					 */
					$tmp['data'] = $item['DATA'][0];
				}

				// MoreData?
				if(isset($item['MOREDATA']))
				{
					$tmp['moredata'] = TRUE;
				}
				else
				{
					$tmp['moredata'] = FALSE;
				}

				$itemx[] = $tmp;
			}

			return $itemx;
		}

		/**
		 * Parse a META element. This function takes *one* element.
		 */
		function parse_meta($xml_array)
		{
			$meta = array();

			// Type?
			if(isset($xml_array['TYPE'][0]))
			{
				$meta['type'] = $xml_array['TYPE'][0][SYNCML_XML_DATA];
			}

			// Size?
			if(isset($xml_array['SIZE'][0]))
			{
				$meta['size'] = $xml_array['SIZE'][0][SYNCML_XML_DATA];
			}

			// Format?
			if(isset($xml_array['FORMAT'][0]))
			{
				$meta['format'] = $xml_array['FORMAT'][0][SYNCML_XML_DATA];
			}

			// MaxObjSize?
			if(isset($xml_array['MAXOBJSIZE'][0]))
			{
				$meta['maxobjsize'] =
					$xml_array['MAXOBJSIZE'][0][SYNCML_XML_DATA];
			}

			// MaxMsgSize?
			if(isset($xml_array['MAXMSGSIZE'][0]))
			{
				$meta['maxmsgsize'] =
					$xml_array['MAXMSGSIZE'][0][SYNCML_XML_DATA];
			}

			// Anchor?
			if(isset($xml_array['ANCHOR'][0]))
			{
				// Next
				$meta['anchor'] = array(
					'next' => $xml_array['ANCHOR'][0]['NEXT']
						[0][SYNCML_XML_DATA]
				);

				// Last?
				if(isset($xml_array['ANCHOR'][0]['LAST']))
				{
					$meta['anchor']['last'] =
						$xml_array['ANCHOR'][0]['LAST'][0][SYNCML_XML_DATA];
				}
			}

			return $meta;
		}

		/**
		 * Save a chunk.
		 *
		 * @param $meta    This object's meta information array.
		 * @param $item    The item to be saved as XML array. First item chunk
		 *                 should contain SIZE and TYPE meta elements.
		 * @param $session Session object.
		 */
		function save_chunk($meta, $item, &$session)
		{
			if(isset($meta['size']) && (int) $meta['size'])
			{
				$session->set_var(
					SYNCML_ITEMSIZE, (int) $meta['size']);
			}

			if(isset($meta['type']) && $meta['type'])
			{
				$session->set_var(
					SYNCML_ITEMTYPE, $meta['type']);
			}

			// if buffer is not empty, check if LUID match
			if(strlen($session->get_var(SYNCML_ITEMBUFFER)) &&
				$item['source']['locuri'] != $session->get_var(SYNCML_ITEMLUID))
			{
				return SYNCML_ALERT_NOENDOFDATA;
			}

			if(isset($item['source']['locuri']) && $item['source']['locuri'])
			{
				$session->set_var(
					SYNCML_ITEMLUID, $item['source']['locuri']);
			}

			$session->append_var(
				SYNCML_ITEMBUFFER, $item['data'][SYNCML_XML_DATA]);

			if(isset($item['moredata']) && $item['moredata'])
			{
				return SYNCML_STATUS_CHUNKEDITEMACCEPTEDANDBUFFERED;
			}
			else
			{
				$data = $session->get_var(SYNCML_ITEMBUFFER);
				$size = $session->get_var(SYNCML_ITEMSIZE);

				if(!is_null($size) && $size != strlen($data))
				{
					return SYNCML_STATUS_SIZEMISMATCH;
				}
			}

			return SYNCML_STATUS_OK;
		}

		/**
		 * Reset everything connected to chunking.
		 *v
		 * @param $session Session object.
		 */
		function reset_chunking(&$session)
		{
			$session->unset_var(SYNCML_ITEMTYPE);
			$session->unset_var(SYNCML_ITEMBUFFER);
			$session->unset_var(SYNCML_ITEMSIZE);
			$session->unset_var(SYNCML_ITEMLUID);
		}
	}
?>
