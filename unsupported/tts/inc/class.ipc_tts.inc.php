<?php
	/**
	* IPC Class for TTS
	* 
	* @author Christian Wederhake <cwederhake@probusiness.de>
	* @copyright Copyright (C) 2003-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package tts
	* @version $Id$
	*/

	/**
	 * Define Folder length
	 */
	define('FOLD_LENGTH',75);

	/**
	* Fassade of the todo application.
	* @package tts
	*/
	class tts_ipc_tts extends phpgwapi_ipc_
	{
		/**
		* @var object $bo application storage object
		* @access private
		*/
		protected $bo;

		/**
		* Constructor
		*/
		public function __construct()
		{
			$this->bo =& CreateObject('tts.botts');
		}

		/**
		* Add data in a certain mime type format to the application.
		* @access public
		* @param mixed $data data for adding to the application, the datatype depends on the mime type
		* @param string $type specifies the mime type of the passed data
		* @param string $version specifies the mime type version of the passed data
		* @param string $timestamp the timestamp is used to set in db instead of current time
		* @return integer id of the added data
		*/
		function addData($data, $type, $version = '', $timestamp=null)
		{
			$decdata = $this->_importData($data, $type, $version);
			if (! $decdata)
			{
				return false;
			}
			if (! is_null($timestamp))
			{
				$decdata['lastmod'] = $timestamp;
			}
			error_log("ipc_tts:addData: ".print_r($decdata, true));
			return $this->bo->save($decdata, false);
		}

		/**
		* Checks if data for the passed id exists.
		* @param integer $id id to check
		* @return boolean true if the data with id exist, otherwise false
		*/
		function existData($id)
		{
			return $this->bo->exists($id);
		}

		/**
		* Get data from the application in a certain mime type format.
		* @param integer $id id of data to get from the application
		* @param string $type specifies the mime type of the returned data
		* @param string $version specifies the mime type version of the returned data
		* @return mixed data from application, the datatype depends on the passed mime type, false if no data exists for the passed id
		*/
		function getData($id, $type, $version = '')
		{
			$data = $this->bo->retrieve($id);
			error_log("ipc_tts:getData: ".print_r($data, true));
			$encdata = $this->_exportData($data, $type, $version);
			return $encdata;
		}

		/**
		* Return a list with the available id's in the application.
		* The optional lastmod parameter allows a limitations of the data id list.
		* The list contains all the id's of the modified data since the passed lastmod timestamp.
		* @param integer $lastmod last modification time, default is -1 and means return all data id's
		* @param string $restriction restrict the result for a special use of the id list. The possible restrictions are 'syncable' or 'searchable'. When using 'syncable' only person ids will be returned in the result. 'searchable' returns all ids for both persons and orgs without check the owner. Otherwise no restriction will be used and the result contains all ids for both persons and orgs from the owner.
		* @return array list of data id's
		*/
		function getIdList($lastmod = -1, $restriction = '')
		{
			// we don't use restrictions here. Afair only persons can be owner of a tts-entry
			return $this->bo->getIDList($lastmod);
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
		* @param string $timestamp the timestamp is used to set in db instead of current time
		* @return boolean true if the data is replaced, otherwise false
		*/
		function replaceData($id, $data, $type, $version = '', $timestamp=null)
		{
			$decdata = $this->_importData($data, $type, $version);
			if (! $decdata) {
				return false;
			}
			if (! is_null($timestamp)) {
				$decdata['lastmod'] = $timestamp;
			}
			error_log("ipc_tts:replaceData: ".print_r($decdata, true));
			return $this->bo->update($decdata, false);
		}

		/**
		 * @access private
		 */
		function _importData($data, $type, $version)
		{
			$decdata = null;
			switch ($type)
			{
				case "x-phpgroupware/tts-serialized-appl-data-array":
					$decdata = unserialize(base64_decode($data));
					break;
				default:
					return false;
			}
			return $decdata;
		}

		/**
		 * @access private
		 */
		function _exportData($data, $type, $version)
		{
			$encdata = null;
			switch ($type)
			{
				case "x-phpgroupware/tts-serialized-appl-data-array":
					$encdata = base64_encode(serialize($data));
					break;
				default:
					return false;
			}
			return $encdata;
		}

	}
