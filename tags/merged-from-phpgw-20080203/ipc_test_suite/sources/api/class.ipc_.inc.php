<?php
	/**
	* Abstract IPC Application class for the IPC Layer
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2003-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage communication
	* @version $Id$
	*/


	/**
	* Abstract IPC Application class for the IPC Layer
	* @package phpgwapi
	* @subpackage communication
	* @abstract
	*/
	class ipc_
	{
		/**
		* Constructor
		* @abstract
		*/
		function ipc_()
		{
			die('call abstract method: '.__class__.'::'.__function__);
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
			die('call abstract method: '.__class__.'::'.__function__);
		}


		/**
		* Checks if data for the passed id exists.
		* @abstract
		* @param integer $id id to check
		* @return boolean true if the data with id exist, otherwise false
		*/
		function existData($id)
		{
			die('call abstract method: '.__class__.'::'.__function__);
		}


		/**
		* Get data from the application in a certain mime type format.
		* @abstract
		* @param integer $id id of data to get from the application
		* @param string $type specifies the mime type of the returned data
		* @param string $version specifies the mime type version of the returned data
		* @return mixed data from application, the datatype depends on the passed mime type, false if no data exists for the passed id
		*/
		function getData($id, $type, $version='')
		{
			die('call abstract method: '.__class__.'::'.__function__);
		}


		/**
		* Return a list with the available id's in the application.
		* The optional lastmod parameter allows a limitations of the data id list.
		* The list contains all the id's of the modified data since the passed lastmod timestamp.
		* @abstract
		* @param integer $lastmod last modification time, default is -1 and means return all data id's
		* @param string $restriction restrict the result for a special use of the id list. The possible restrictions are 'syncable' or 'searchable'. When using 'syncable' only person ids will be returned in the result. 'searchable' returns all ids for both persons and orgs without check the owner. Otherwise no restriction will be used and the result contains all ids for both persons and orgs from the owner.
		* @return array list of data id's
		*/
		function getIdList($lastmod=-1, $restriction='')
		{
			die('call abstract method: '.__class__.'::'.__function__);
		}


		/**
		* Remove data of the passed id.
		* @abstract
		* @param integer $id id of data to remove from the application
		* @return boolean true if the data is removed, otherwise false
		*/
		function removeData($id)
		{
			die('call abstract method: '.__class__.'::'.__function__);
		}


		/**
		* Replace the existing data of the passed id with the passed data in a certain mime type format.
		* @abstract
		* @param integer $id id of data to replace
		* @param mixed $data the new data, the datatype depends on the passed mime type
		* @param string $type specifies the mime type of the passed data
		* @param string $version specifies the mime type version of the passed data (still not supported)
		* @return boolean true if the data is replaced, otherwise false
		*/
		function replaceData($id, $data, $type, $version='')
		{
			die('call abstract method: '.__class__.'::'.__function__);
		}
	}
?>