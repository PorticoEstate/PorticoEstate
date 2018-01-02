<?php
	/**
	* IPC Layer
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2003-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgroupware
	* @subpackage bookmarks
	* @version $Id$
	*/

	/**
	* Fassade of the bookmarks application.
	* @package phpgroupware
	* @subpackage  bookmarks
	*/
	class bookmarks_ipc_bookmarks extends phpgwapi_ipc_
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
		function bookmarks_ipc_bookmarks()
		{
			$this->bo =& CreateObject('bookmarks.bo');

			// define the map
			$this->map = array(
				'x-phpgroupware/bookmarks' => array(
					// extern              <> intern
					'bookmark_id'          => 'id',
					'bookmark_url'         => 'url',
					'bookmark_title'       => 'name',
					'bookmark_description' => 'desc',
					'bookmark_keywords'    => 'keywords',
					'bookmark_rating'      => 'rating',
					'bookmark_visits'      => 'visits',
					'bookmark_access'      => 'access',
					'bookmark_owner'       => 'owner',
					'bookmark_category'    => 'category',
					'bookmark_timestamp_added'      => 'timestamp_added',
					'bookmark_timestamp_lastvisit'  => 'timestamp_lastvisit',
					'bookmark_timestamp_lastmod'    => 'timestamp_lastmod'
				),
				'text/plain' => array('url'),
				'x-phpgroupware/search-index-data-item' => array(
					// extern              <> intern
					'bookmark_id'          => 'id',
					'bookmark_url'         => 'url',
					'bookmark_title'       => 'name',
					'bookmark_description' => 'desc',
					'bookmark_keywords'    => 'keywords',
					'bookmark_rating'      => 'rating',
					'bookmark_visits'      => 'visits',
					'bookmark_access'      => 'access',
					'bookmark_owner'       => 'owner',
					'bookmark_category'    => 'category',
					'bookmark_timestamp_added'      => 'timestamp_added',
					'bookmark_timestamp_lastvisit'  => 'timestamp_lastvisit',
					'bookmark_timestamp_lastmod'    => 'timestamp_lastmod'
				)
			);
		}


		/**
		* Add data in a certain mime type format to the application.
		* @param mixed $data data for adding to the application, the datatype depends on the mime type
		* @param string $type specifies the mime type version of the passed data
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
			return $this->bo->add($dataIntern);
			
		}

		/**
		* Get data from the application in a certain mime type format.
		* @param integer $id id of data to get from the application
		* @param string $type specifies the mime type of the returned data
		* @param string $version specifies the mime type version of the passed data
		* @return mixed data from application, the datatype depends on the passed mime type, false if no data exists for the passed id
		*/
		function getData($id, $type, $versiom='')
		{
			$id = intval($id);
			if($id == false)
				return false;
			
			// 1: get data
			$dataIntern = $this->bo->so->read($id);
			if ($dataIntern == false)
				return false;

			$dataIntern['id'] = $id;
			if(isset($dataIntern['info']))
			{
				$times = explode(',', $dataIntern['info'], 3);

				if(isset($times[0]) == true)
					$dataIntern['timestamp_added'] = $times[0];
				else
					$dataIntern['timestamp_added'] = 0;

				if(isset($times[1]) == true)
					$dataIntern['timestamp_lastvisit'] = $times[1];
				else
					$dataIntern['timestamp_lastvisit'] = 0;

				if(isset($times[2]) == true)
					$dataIntern['timestamp_lastmod']   = $times[2];
				else
					$dataIntern['timestamp_lastmod'] = 0;
			}

			// 2: mapping internal data to the output mime type
			return $this->_exportData($dataIntern, $type, $version);
		}

		/**
		* Return a list with the available id's in the application.
		* The optional lastmod parameter allows a limitations of the data id list.
		* The list contains all the id's of the modified data since the passed lastmod timestamp.
		* @param integer $lastmod last modification time, default is -1 and means return all data id's (NOT SUPPORTED YET)
		* @param string $restriction restrict the result for a special use of the id list. The possible restrictions are 'syncable' or 'searchable'. When using 'syncable' only person ids will be returned in the result. 'searchable' returns all ids for both persons and orgs without check the owner. Otherwise no restriction will be used and the result contains all ids for both persons and orgs from the owner.
		* @return array list of data id's, if $lastmod is set, only modified entry id's, else all id's returned
		*/
		function getIdList($lastmod=-1, $restriction='')
		{
			$lastmod = intval($lastmod);
			// could not use this because the bm_info field contains a separated list of timestamps
			//if($lastmod>=0)
			//	$where_clause = 'bm_info > '.$lastmod;
			//else
			//	$where_clause = false;
			if($lastmod != -1)
				die('lastmod not supported yet');

			$list = $this->bo->_list(false, false, $where_clause, true);
			return array_keys($list);
		}

		/**
		* Remove data of the passed id.
		* @param integer $id id of data to remove from the application
		* @return boolean true if the data is removed, otherwise false
		*/
		function removeData($id)
		{
			return $this->bo->delete($id);
		}

		/**
		* Replace the existing data of the passed id with the passed data in a certain mime type format.
		* @param integer $id id of data to replace
		* @param mixed $data the new data, the datatype depends on the passed mime type
		* @param string $type specifies the mime type of the passed data
		* @param string $version specifies the mime type version of the passed data
		* @return boolean true if the data is replaced, otherwise false
		*/
		function replaceData($id, $data, $type, $version='')
		{
			// 1: mapping the passed input data to application internal data
			$dataIntern = $this->_importData($data, $type, $version);
			if ($dataIntern == false)
				return false;

			// 2: replace data
			return $this->bo->update($id, $dataIntern);
		}

		/**
		* Checks if data for the passed id exists.
		* @param integer $id id to check
		* @return boolean true if the data with id exist, otherwise false
		*/
		function existData($id)
		{
			if($this->bo->read($id))
				return true;
			else
				return false;
		}

		/**
		* Convert data from a certain mime type format to the internal application data structure.
		* @access private
		* @param mixed $dataExtern data to convert, the datatype depends on the passed mime type
		* @param string $type specifies the mime type of the passed data
		* @return array data as application internal array
		*/
		function _importData($dataExtern, $type, $version='')
		{
			$dataIntern = array();

			switch ($type)
			{
				case 'x-phpgroupware/bookmarks':
					if (is_array($dataExtern) == false)
						return false;

					if(isset($dataExtern['bookmark_timestamp_added']) == true)
						$dataIntern['info'] = $dataExtern['bookmark_timestamp_added'];
					else
						$dataIntern['info'] = '0';
						
					if(isset($dataExtern['bookmark_timestamp_lastvisit']) == true)
						$dataIntern['info'] .= ','.$dataExtern['bookmark_timestamp_lastvisit'];
					else
						$dataIntern['info'] .= ',0';
	
					if(isset($dataExtern['bookmark_timestamp_lastmod']) == true)
						$dataIntern['info'] .= ','.$dataExtern['bookmark_timestamp_lastmod'];
					else
						$dataIntern['info'] .= ',0';
	
					foreach($this->map[$type] as $keyExtern => $keyIntern)
					{
						if (isset($dataExtern[$keyExtern]) == true)
						  $dataIntern[$keyIntern] = $dataExtern[$keyExtern];
						else
							$dataIntern[$keyIntern] = null;
					}
				break;
				case 'text/plain':
					if (is_string($dataExtern) == false)
						return false;
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
		* @return mixed data in certain mime type format, the datatype depends on the passed mime type
		*/
		function _exportData($dataIntern, $type, $version='')
		{
			if (is_array($dataIntern) == false)
				return false;

			$dataExtern = null;

			switch ($type)
			{
				case 'x-phpgroupware/bookmarks':
					$dataExtern = array();
					foreach($this->map[$type] as $keyExtern => $keyIntern)
					{
						if (isset($dataIntern[$keyIntern]) == true)
						  $dataExtern[$keyExtern] = $dataIntern[$keyIntern];
						else
							$dataExtern[$keyExtern] = null;
					}

					// extend the internal data with link informtion
					$keyExtern_id = $this->_getKeyExtern('id', $type);
					$id = $dataExtern[$keyExtern_id];
					// info needed to generate a view link
					$dataExtern['link_view'] = array('menuaction' => 'bookmarks.bookmarks_ui.view',
				                                 'bm_id'      => $id);
					// info needed to generate a edit link
					$dataExtern['link_edit'] = array('menuaction' => 'bookmarks.bookmarks_ui.edit',
					                                 'bm_id'      => $id);
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
					$dataExtern =& $this->_export_index_data_item($dataExtern);
				break;
				default:
					return false;
			}

			return $dataExtern;
		}

		/**
		* Get the external key for the passed internal key.
		* @access private
		* @param string $keyIntern data as application internal array
		* @param string $type specifies the mime type of the returned data
		* @return string name of external key
		*/
		function _getKeyExtern($keyIntern, $type)
		{
			$keyExtern = false;
			switch ($type)
			{
				case 'x-phpgroupware/bookmarks':
					foreach($this->map[$type] as $keyEx => $keyIn)
					{
						if ($keyIn == $keyIntern)
						{
							$keyExtern = $keyEx;
							break;
						}
					}
				break;
				case 'text/plain':
					$keyIntern = $this->map[$type][0];
					$dataExtern = $dataIntern[$keyIntern];
				break;
				case 'text/xml':
					return false;
				break;
				default:
					return false;
				break;
			}

			return $keyExtern;
		}

		/**
		* Get the internal key for the passed external key.
		* @access private
		* @param string $keyExtern data as application internal array
		* @param string $type specifies the mime type of the returned data
		* @return string name of internal key
		*/
		function _getKeyIntern($keyExtern, $type)
		{
			$keyIntern = false;
			switch ($type)
			{
				case 'x-phpgroupware/bookmarks':
					if (isset($this->map[$type][$keyExtern]) == true)
						$keyIntern = $this->map[$type][$keyExtern];
				break;
				case 'text/plain':
					$keyIntern = $this->map[$type][0];
				break;
				case 'text/xml':
					return false;
				break;
				default:
					return false;
				break;
			}

			return $keyExtern;
		}

		function &_export_index_data_item($fields)
		{
			$index_xml_item = CreateObject('search.index_xml_item', 'bookmarks', $fields['bookmark_id']);
			$index_xml_item->setDisplayName($fields['bookmark_title']);
			$index_xml_item->setPriority($fields['bookmark_rating']);

			$catId   = $fields['bookmark_category']?$fields['bookmark_category']:'';
			$catName = '';
			$index_xml_item->setCategory($catId, $catName);

			$ownerId = $fields['bookmark_owner'];
			$groupId = '';
			$visibilty = $fields['bookmark_access'];
			$index_xml_item->setAccess($ownerId, $groupId, $visibilty);

			$created    = $fields['bookmark_timestamp_added']?$fields['bookmark_timestamp_added']:'';
			$modified   = $fields['bookmark_timestamp_lastmod']?$fields['bookmark_timestamp_lastmod']:'';
			$lastAccess = $fields['bookmark_timestamp_lastvisit']?$fields['bookmark_timestamp_lastvisit']:'';
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
