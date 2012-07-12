<?php
	/**
	* IPC Class for Notes
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2003-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage communication
	* @version $Id$
	*/

	/**
	* Fassade of the notes application.
	* @package notes
	*/
	class notes_ipc_notes extends phpgwapi_ipc_
	{
		/**
		* @var object $bo application storage object
		*/
		protected $bo;

		/**
		* @var array $map contains for each mime type the mapping keys
		*/
		protected $map;


		/**
		* Constructor
		*/
		public function __construct()
		{
			$this->bo =& CreateObject('notes.bonotes');

			// define the map
			$this->map = array(
				'x-phpgroupware/notes' => array(
					// extern           <> intern
					'note_id'           => 'id',
					'note_owner'        => 'owner',
					'note_access'       => 'access',
					'note_createdate'   => 'date',
					'note_category'     => 'category',
					'note_description'  => 'content'
				),
				'text/x-vnote' => array(
					// extern           <> intern
					'LAST-MODIFIED'     => 'date',
					// 'DCREATED'          => 'date',
					// BODY; has to be the last!
					'BODY'              => 'content'
				),
				'text/plain' => array('content'),
				'x-phpgroupware/search-index-data-item' => array(
					// extern           <> intern
					'note_id'           => 'id',
					'note_owner'        => 'owner',
					'note_access'       => 'access',
					'note_createdate'   => 'date',
					'note_category'     => 'category',
					'note_description'  => 'content'
				)
			);
		}


		/**
		* Add data in a certain mime type format to the application.
		* @abstract
		* @param mixed $data data for adding to the application, the datatype depends on the mime type
		* @param string $type specifies the mime type of the passed data
		* @param string $version specifies the mime type version of the passed data
		* @return integer id of the added data
		*/
		function addData($data, $type, $version='')
		{
			// 1: mapping the mime type to application data
			$dataIntern = $this->_importData($data, $type, $version);
			if ($dataIntern == false)
				return false;

			// 2: add data to application
			$id = $this->bo->save($dataIntern);
			// set date (sonotes doesn't support this itselfs)
	  	$date = $dataIntern["date"];
			if ($date != '')
				$GLOBALS['phpgw']->db->query("UPDATE phpgw_notes set note_date='" . $date . "' WHERE note_id=" . $id,__LINE__,__FILE__);
			return $id;
		}

		/**
		* Checks if data for the passed id exists.
		* @param integer $id id to check
		* @return boolean true if the data with id exist, otherwise false
		*/
		function existData($id)
		{
			$note = $this->bo->read($id);
			if($note == false)
				return false;
			else
				return true;
		}


		/**
		* Get data from the application in a certain mime type format.
		* @param integer $id id of data to get from the application
		* @param string $type specifies the mime type of the returned data
		* @param string $version specifies the mime type version of the returned data
		* @return mixed data from application, the datatype depends on the passed mime type, false if no data exists for the passed id
		*/
		function getData($id, $type, $version='')
		{
			// 1: get data
			$dataIntern = $this->bo->read($id);
			if ($dataIntern == false)
				return false;

			// 2: mapping internal data to the output mime type
			return $this->_exportData($dataIntern, $type, $version);
		}


		/**
		* Return a list with the available id's in the application.
		* The optional lastmod parameter allows a limitations of the data id list.
		* The list contains all the id's of the modified data since the passed lastmod timestamp.
		* @param integer $lastmod last modification time, default is -1 and means return all data id's
		* @param string $restriction restrict the result for a special use of the id list. The possible restrictions are 'syncable' or 'searchable'. When using 'syncable' only person ids will be returned in the result. 'searchable' returns all ids for both persons and orgs without check the owner. Otherwise no restriction will be used and the result contains all ids for both persons and orgs from the owner.
		* @return array list of data id's
		*/
		function getIdList($lastmod=-1, $restriction='')
		{
			$idList = array();

			$notes = $this->bo->_list(null, null, null, null, false, $lastmod);
			if($notes == false)
				return $idList;

			foreach($notes as $note)
			{
				$idList[] = $note['id'];
			}

			return $idList;
		}


		/**
		* Remove data of the passed id.
		* @param integer $id id of data to remove from the application
		* @return boolean true if the data is removed, otherwise false
		*/
		function removeData($id)
		{
			$this->bo->delete($id);
			// return status workaround: delete() returns always true --> check for sql error
			if ($GLOBALS['phpgw']->db->Error)
				return false;
			else
				return true;
		}


		/**
		* Replace the existing data of the passed id with the passed data in a certain mime type format.
		* @param integer $id id of data to replace
		* @param mixed $data the new data, the datatype depends on the passed mime type
		* @param string $type specifies the mime type of the passed data
		* @param string $version specifies the mime type version of the passed data (still not supported)
		* @return boolean true if the data is replaced, otherwise false
		*/
		function replaceData($id, $data, $type, $version='')
		{
			// 1: mapping the passed input data to application internal data
			$dataIntern = $this->_importData($data, $type, $version);
			if ($dataIntern == false)
				return false;

			$dataIntern['id'] = (int) $id;

			// 2: replace data
			$id = $this->bo->save($dataIntern);
			// return status workaround: save() returns always note_id --> check for sql error
			if ($GLOBALS['phpgw']->db->Error)
				$result = false;
			else
				$result = true;
			/*
			// set date (sonotes doesn't support this for itselfs)
			$date = $dataIntern["date"];
			if ($date != '')
				$GLOBALS['phpgw']->db->query("UPDATE phpgw_notes set note_date='" . $date . "' WHERE note_id=" . $id,__LINE__,__FILE__);
			*/
			return $result;
		}

		/**
		* Convert data from a certain mime type format to the internal application data structure.
		* @access private
		* @param mixed $dataExtern data to convert, the datatype depends on the passed mime type
		* @param string $type specifies the mime type of the passed data
		* @param string $version specifies the mime type version of the passed data
		* @return array data as application internal array
		*/
		function _importData($dataExtern, $type, $version)
		{
			$dataIntern = array();

			switch ($type)
			{
				case 'x-phpgroupware/notes':
					if (is_array($dataExtern) == false)
					{
						return false;
					}

					foreach($this->map[$type] as $keyExtern => $keyIntern)
					{
						if (isset($dataExtern[$keyExtern]) == true)
						{
							$dataIntern[$keyIntern] = $dataExtern[$keyExtern];
						}
						else
						{
							$dataIntern[$keyIntern] = null;
						}
					}
				break;
				case 'text/x-vnote':
					// handle buggy P800 version-string
					$dataExtern = addcslashes($dataExtern, "\000");
					$dataExtern = ereg_replace('\\\000', "", $dataExtern);

					// recheck if it is really a vnote...
					if (! preg_match('/\<\!\[CDATA\[BEGIN\:VNOTE/i', $dataExtern))
					{
						return false;
					}
					// get lines
					$datalines = preg_split("/\r\n|\n/", $dataExtern);

					// reset variables;
					$body = '';
					$dataExtern = array();
					$in_body = false;

					for($i=0; $i<count($datalines); $i++) 
					{
						// parse line
						$line_array = $this->vNote_parseline($datalines[$i]);
						if (is_array($line_array))
						{
							// review filled array
							$key = strtoupper($line_array["key"]);
							$value = $this->vNote_convertData($key, $line_array["data"], $line_array["encoding"]);

							// special BODY-handling - because it can have more than 1 line
							if ($key == "BODY")
							{
								$body .= $value;
								$in_body = true;
							}
							else
							{
								$in_body = false;
								$dataExtern[$key] = $value;
							}
						}
						else
						{
							// add if body consists of more then 1 line - 
							// then $line_array is a string!
							if ($in_body)
								$body .= $line_array;
						}
					}
					$dataExtern["BODY"] = $body;

					foreach($this->map[$type] as $keyExtern => $keyIntern)
					{
						if (isset($dataExtern[$keyExtern]) == true)
						{
							$dataIntern[$keyIntern] = $dataExtern[$keyExtern];
						}
						else
						{
							$dataIntern[$keyIntern] = null;
						}
					}
				// endof 'text/x-vnote'
				break;
				case 'text/plain':
					if (is_string($dataExtern) == false)
					{
						return false;
					}
					$keyIntern = $this->map[$type][0];
					$dataIntern[$keyIntern] = $dataExtern;
				break;
				case 'text/xml':
					return false;
				break;
				default:
					return false;
				break;
			}
			return $dataIntern;
		}


		/**
		* Convert data from internal application data structure to a certain mime type format.
		* @access private
		* @param array $dataIntern data as application internal array
		* @param string $type specifies the mime type of the returned data
		* @param string $version specifies the mime type version of the returned data
		* @return mixed data in certain mime type format, the datatype depends on the passed mime type
		*/
		function _exportData($dataIntern, $type, $version)
		{
			if(is_array($dataIntern) == false)
			{
				return false;
			}

			$dataExtern = null;

			switch($type)
			{
				case 'x-phpgroupware/notes':
					$dataExtern = array();
					foreach($this->map[$type] as $keyExtern => $keyIntern)
					{
						if (isset($dataIntern[$keyIntern]) == true)
						  $dataExtern[$keyExtern] = $dataIntern[$keyIntern];
						else
							$dataExtern[$keyExtern] = null;
					}
				break;
	      case 'text/x-vnote':
	        $header  = '<![CDATA[BEGIN:VNOTE' . "\r\n";
	        $header .= 'VERSION:1.1' . "\r\n";
	        $dataExternString = $header;
	        reset($this->map[$type]);
	        foreach($this->map[$type] as $keyExtern => $keyIntern)
	        {
						if (isset($dataIntern[$keyIntern]) == true)
						{
							// review filled array
							$key = strtoupper($keyIntern);
							$value = $this->vNote_convertData($key, $dataIntern[$keyIntern], '');
							$dataExtern[$keyExtern] = $value;
							$dataExternString .= "$keyExtern:" . $dataExtern[$keyExtern] . "\r\n";
						}
						else
						{
							$dataExtern[$keyExtern] = null;
						}
					}

					$footer = 'END:VNOTE'."\r\n".']]>';
					$dataExternString .= $footer;
					return $dataExternString;
				break;
				case 'text/plain':
					$keyIntern = $this->map[$type][0];
					$dataExtern = $dataIntern[$keyIntern];
				break;
				case 'x-phpgroupware/search-index-data-item':
					$dataExtern = array();
					foreach($this->map[$type] as $keyExtern => $keyIntern)
					{
						if (isset($dataIntern[$keyIntern]) == true)
						{
						  $dataExtern[$keyExtern] = $dataIntern[$keyIntern];
						}
						else
						{
							$dataExtern[$keyExtern] = null;
						}
					}
					$dataExtern = $this->_export_index_data_item($dataExtern);
				break;
				default:
					return false;
			}
			return $dataExtern;
		}

		/**
		* Parse a vnote message line
		* @access private
		* @param string $line vnote message line
		* @return mixed parsed line as array or string
		*/
		function vNote_parseline($line)
		{
			//split key:value
			if (preg_match('/(.*)\:(.*)/', $line, $matches))
			{
				//get key only
				if (preg_match('/(.*)\;(.*)/', $matches[1], $key_array))
				{
	        $key = $key_array[1];
				}
				else
				{
					$key = $matches[1];
				}

				//get encoding if available
				$encoding = '';
				if (preg_match('/(.*)\;(.*)/', $matches[1], $enc_matches))
				{
					if (preg_match('/encoding\=(.*)/i', $enc_matches[2], $encoding_array));
					{
						$encoding = $encoding_array[1];
					}
				}

				$result_array = array("key" => $key,
				                      "encoding" => $encoding,
				                      "data" => $matches[2]);
				return $result_array;
	    }
			else
	    {
				return $line;
			}
		}

		/**
		* Decode if we have a valid decode-routine else passthrough
		* @access private
		* @param string $value data to decode
		* @param string $encoding encoding type (at the moment support 'QUOTED-PRINTABLE')
		* @return string value decoded
		*/
		function multi_decode($value, $encoding)
		{
			switch (strtoupper($encoding))
			{
				case 'QUOTED-PRINTABLE':
					return quoted_printable_decode($value);
				break;
				default:
					return $value;
				break;
			}
		}

		/**
		* Convert and decode vnote field to internal array field.
		* @access private
		* @param string $key name of vnote field
		* @param string $data vnote field
		* @param string $encoding encoding type of data
		* @return string data decoded
		*/
		function vNote_convertData($key, $data, $encoding)
		{
			$data = $this->multi_decode($data, $encoding); 
			switch ($key) 
			{
				case 'DCREATED':
				case 'LAST-MODIFIED':
					//DCREATED:20030906T194315Z
					if (preg_match("/(\d{4})(\d{2})(\d+)T(\d{2})(\d{2})(\d{2})Z/", $data, $matches))
						$data = mktime( 
						        $matches[4],  // hour
						        $matches[5],  // min
						        $matches[6],  // sec
						        $matches[2],  // day
						        $matches[3],  // month
						        $matches[1]);  // year
				case 'DATE':
					//LAST-MODIFIED:20030906T194315Z
					$data = date("Ymj\THis\Z", $data);
				break;
			}
			return $data;
		}

		function &_export_index_data_item($fields)
		{
			$index_xml_item = CreateObject('search.index_xml_item', 'notes', $fields['note_id']);

			$index_xml_item->setDisplayName($fields['note_description']);

			$index_xml_item->setPriority($fields['todo_priority']);

			$catId   = $fields['note_category']?$fields['note_category']:'';
			$catName = '';
			$index_xml_item->setCategory($catId, $catName);

			$ownerId = $fields['note_owner'];
			$groupId = '';
			$visibilty = $fields['note_access'];
			$index_xml_item->setAccess($ownerId, $groupId, $visibilty);

			$created    = $fields['note_createdate'];
			$modified   = '';
			$lastAccess = '';
			$index_xml_item->setTimestamp($created, $modified, $lastAccess);

			// create csv file string
			$csv = implode(',', array_keys($fields));
			$csv .= "\r\n";

			$values = array_values($fields);
			for($i=0; $i<count($values); ++$i)
			{
				if($i>0)
					$csv .= ",";
				$csv .= str_replace(",", "\,", addslashes($values[$i]));
			}
			$csv .= "\r\n";

			$index_xml_item->setContent($csv, 'text/csv', '1.0');
			$index_xml_item->setContentTransferEncoding('base64');

			return $index_xml_item;
		}
	}
?>
