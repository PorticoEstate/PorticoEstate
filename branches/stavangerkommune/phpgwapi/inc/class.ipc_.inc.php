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
	*/
	abstract class phpgwapi_ipc_
	{
		/**
		* Constructor
		*
		* @access public
		*/
		abstract public function __construct();

		/**
		* Add data in a certain mime type format to the application.
		*
		* @param   mixed    $data  data for adding to the application, the datatype depends on the mime type
		* @param   string   $type  specifies the mime type of the passed data
		* @return  integer         id of the added data
		*/
		abstract public function addData($data, $type);

		/**
		* Convert data from a mime type to another.
		*
		* @access  public
		* @param   mixed    $data     data for converting, the datatype depends on the input mime type
		* @param   string   $typeIn   specifies the input mime type of the passed data
		* @param   string   $typeOut  specifies the output mime type of the passed data
		* @return  mixed              converted data from application, the datatype depends on the passed output mime type
		*/
		abstract public function convertData($data, $typeIn, $typeOut);

		/**
		* Checks if data for the passed id exists.
		*
		* @param   integer  $id  id to check
		* @return  boolean       true if the data with id exist, otherwise false
		*/
		abstract public function existData($id);

		/**
		* Get data from the application in a certain mime type format.
		*
		* @param   integer  $id    id of data to get from the application
		* @param   string   $type  specifies the mime type of the returned data
		* @return  mixed           data from application, the datatype depends on the passed mime type, false if no data exists for the passed id
		*/
		abstract public function getData($id, $type);

		/**
		* Return a list with the available id's in the application.
		* The optional lastmod parameter allows a limitations of the data id list.
		* The list contains all the id's of the modified data since the passed lastmod timestamp.
		*
		* @param   integer  $lastmod  last modification time, default is -1 and means return all data id's
		* @return  array              list of data id's
		*/
		abstract public function getIdList($time = -1);

		/**
		* Remove data of the passed id.
		*
		* @param   integer  $id  id of data to remove from the application
		* @return  boolean       true if the data is removed, otherwise false
		*/
		abstract public function removeData($id);

		/**
		* Replace the existing data of the passed id with the passed data in a certain mime type format.
		*
		* @param   integer  $id    id of data to replace
		* @param   mixed    $data  the new data, the datatype depends on the passed mime type
		* @param   string   $type  specifies the mime type of the passed data
		* @return  boolean         true if the data is replaced, otherwise false
		*/
		abstract public function replaceData($id, $data, $type);
	}
