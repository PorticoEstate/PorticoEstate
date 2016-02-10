<?php
	/**
	* IPC Class for Todo
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2003-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage communication
	* @version $Id$
	*/

	/**
	* Fassade of the todo application.
	* @package  todo
	*/
	class todo_ipc_todo extends phpgwapi_ipc_
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
			$this->bo =& CreateObject('todo.bo');

			// define the map
			$this->map = array(
				'x-phpgroupware/todo' => array(
					// extern           <> intern              
					'todo_id'           => 'id',           	// -in	+out
					'todo_id_parent'    => 'parent',       	// +in	+out
					'todo_id_main'      => 'main',         	// +in	+out
					'todo_level'        => 'level',        	// +in	+out
					'todo_title'        => 'title',        	// +in	+out
					'todo_description'  => 'descr',        	// +in	+out
					'todo_status'       => 'status',       	// +in	+out
					'todo_priority'     => 'pri',          	// +in	+out
					'todo_category'     => 'cat',          	// +in	+out
					'todo_start_date'   => 'sdate',        	// +in	+out
					'todo_end_date'     => 'edate',        	// +in	+out
					'todo_create_date'  => 'entry_date',   	// -in	+out
					'todo_access'       => 'access',       	// +in	+out
					'todo_owner'        => 'owner'        	// -in	+out
				),
				'text/plain' => array('title'),
				'x-phpgroupware/search-index-data-item' => array(
					// extern           <> intern              
					'todo_id'           => 'id',           	// -in	+out
					'todo_id_parent'    => 'parent',       	// +in	+out
					'todo_id_main'      => 'main',         	// +in	+out
					'todo_level'        => 'level',        	// +in	+out
					'todo_title'        => 'title',        	// +in	+out
					'todo_description'  => 'descr',        	// +in	+out
					'todo_status'       => 'status',       	// +in	+out
					'todo_priority'     => 'pri',          	// +in	+out
					'todo_category'     => 'cat',          	// +in	+out
					'todo_start_date'   => 'sdate',        	// +in	+out
					'todo_end_date'     => 'edate',        	// +in	+out
					'todo_create_date'  => 'entry_date',   	// -in	+out
					'todo_access'       => 'access',       	// +in	+out
					'todo_owner'        => 'owner'        	// -in	+out
				)
			);
		}

		/**
		* Add data in a certain mime type format to the application.
		* @access public
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
			{
				return false;
			}
	
			// 2: add data to application
			return $this->bo->save($dataIntern);
		}

		/**
		* Checks if data for the passed id exists.
		* @param integer $id id to check
		* @return boolean true if the data with id exist, otherwise false
		*/
		function existData($id)
		{
			// workaround: so have not a method for check this
			// --> sql query
			$sql = "SELECT * FROM phpgw_todo WHERE todo_id=".intval($id);
			$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
			if ($GLOBALS['phpgw']->db->Error)
				return false;
	
			if ($GLOBALS['phpgw']->db->num_rows() == 1)
				return true;
			else
				return false;
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
			{
				return false;
			}
	
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

			$todos = $this->bo->sotodo->read_todos(null, null, null, null, null, null, null, null, null, $lastmod);
			if($todos == false)
			{
				return $idList;
			}
	
			foreach($todos as $todo)
			{
				$idList[] = $todo['id'];
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
	  	$remove_sub_todos = true;
	  	return $this->bo->delete($id, $remove_sub_todos);
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
			{
				return false;
			}

			$dataIntern['id'] = (int) $id;
	
			// 2: replace data
			return $this->bo->save($dataIntern);
		}

		/**
		* Convert data from internal application data structure to a certain mime type format.
		* @param array $dataIntern data as application internal array
		* @param string $type specifies the mime type of the returned data
		* @param string $version specifies the mime type version of the returned data
		* @return mixed data in certain mime type format, the datatype depends on the passed mime type
		*/
		function _exportData($dataIntern, $type, $version)
		{
			if (is_array($dataIntern) == false)
			{
				return false;
			}
	
			$dataExtern = null;
	
			switch ($type)
			{
				case 'x-phpgroupware/todo':
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
				case 'x-phpgroupware/todo':
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
				case 'text/plain':
					if (is_string($dataExtern) == false)
					{
						return false;
					}
					$keyIntern = $this->map[$type][0];
					$dataIntern[$keyIntern] = $dataExtern;
				break;
				default:
					return false;
			}
	
			return $dataIntern;
		}


		function &_export_index_data_item($fields)
		{
			$index_xml_item = CreateObject('search.index_xml_item', 'todo', $fields['todo_id']);

			$index_xml_item->setDisplayName($fields['todo_title']);

			$index_xml_item->setPriority($fields['todo_priority']);

			$catId   = '';
			$catName = '';
			$index_xml_item->setCategory($catId, $catName);

			$ownerId = $fields['todo_owner'];
			$groupId = '';
			$visibilty = $fields['todo_access'];
			$index_xml_item->setAccess($ownerId, $groupId, $visibilty);

			$created    = $fields['todo_create_date'];
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
