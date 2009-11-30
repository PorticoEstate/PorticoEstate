<?php
	/**
	* IPC Class for Projects
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2003-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage communication
	* @version $Id$
	*/

	/**
	* Fassade of the projects application.
	* @package projects
	*/
	class projects_ipc_projects extends phpgwapi_ipc_
	{
		/**
		* @var object $bo application storage object
		* @access private
		*/
		protected $boprojects;


		/**
		* Constructor
		*/
		public function __construct()
		{
			$this->boprojects =& CreateObject('projects.boprojects');
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

			if($type == 'x-phpgroupware/projects-view-url')
			{
				$link_data['menuaction'] = 'projects.uiprojects.view_project';
				$link_data['action'] = 'mains';
				$link_data['project_id'] = $id;
				return $GLOBALS['phpgw']->link('/index.php',$link_data);
			}
			
			$pro = $this->boprojects->read_single_project($id);
			if(!$pro)
			{
				return false;
			}
			else
			{
				// project data are 'x-phpgroupware/projects-appl-data-array'
				$convertTypeIn     = 'x-phpgroupware/projects-appl-data-array';
				$convertVersionIn  = '';
				$convertTypeOut    = $type;
				$convertVersionOut = $version;

				// convert fields to mime type format
				return $this->convertData($pro, $convertTypeIn, $convertVersionIn, $convertTypeOut, $convertVersionOut);
			}

		}
		
		/**
		* Return a list with the available id's in the application.
		* The optional lastmod parameter allows a limitations of the data id list.
		* The list contains all the id's of the modified data since the passed lastmod timestamp.
		* @param integer $lastmod last modification time, default is -1 and means return all data id's.
		* @param string $restriction restrict the result for a special use of the id list. The possible restrictions are 'syncable' or 'searchable'. When using 'syncable' only person ids will be returned in the result. 'searchable' returns all ids for both persons and orgs without check the owner. Otherwise no restriction will be used and the result contains all ids for both persons and orgs from the owner.
		* @return array list of data id's
		*/
		function getIdList($lastmod=-1, $restriction='')
		{
			$idList = array();
			$lastmod = intval($lastmod);
			
			$sql = 'SELECT DISTINCT project_id FROM phpgw_p_projects';
			$this->boprojects->soprojects->db->query($sql,__LINE__,__FILE__);
			while ($this->boprojects->soprojects->db->next_record())
			{
				$idList[] =	$this->boprojects->soprojects->db->Record['project_id'];
			}

			return $idList;
		}

		/**
		* Checks if data for the passed id exists.
		* @param integer $id id to check
		* @return boolean true if the data with id exist, otherwise false
		*/
		function existData($id)
		{
			return $this->boprojects->soprojects->reallyexists($id);
		}

		/**
		* Convert the given data from a mime type into another.
		* @param mixed $data data for converting
		* @param string $typeIn specifies the mime type of the passed data
		* @param string $versionIn specifies the mime type version of the passed data
		* @param string $typeOut specifies the mime type of the returned data
		* @param string $versionOut specifies the mime type version of the returned data
		* @return mixed data from application, the datatype depends on the passed typeOut and versionOut parameters
		*/
		function convertData($data, $typeIn, $versionIn, $typeOut, $versionOut)
		{
			switch($typeIn)
			{
				case 'x-phpgroupware/projects-data-array':
					$appl_data_array = $this->_import_projects_data_array($data, $versionIn);
				break;
				case 'x-phpgroupware/projects-serialized-appl-data-array':
					$appl_data_array = unserialize(trim($data));
				break;
				case 'x-phpgroupware/projects-appl-data-array':
					// no import convert
					$appl_data_array = $data;
				break;
				default:
					return false;
			}

			if(!$appl_data_array)
				return false;

			switch($typeOut)
			{
				case 'x-phpgroupware/projects-data-array':
					$ret_data = $this->_export_projects_data_array($appl_data_array, $versionOut);
				break;
				case 'x-phpgroupware/search-index-data-item':
					$ret_data = $this->_export_index_data_item($appl_data_array, $versionOut);
				break;
				case 'x-phpgroupware/projects-serialized-appl-data-array':
					$ret_data = serialize($appl_data_array);
				break;
				case 'x-phpgroupware/projects-appl-data-array':
					// no export convert
					$ret_data = $appl_data_array;
				break;
				default:
					return false;
			}
			
			return $ret_data;
		}

		/**
		* Convert the extern data array structure into intern application array
		* @access private
		* @param array $fields extern data array
		* @return string xml
		*/
		function _import_projects_data_array($fields, $versionIn)
		{
		}


		function _export_projects_data_array($fields, $versionOut)
		{
		}
		

		function &_export_index_data_item($fields, $version='1.0')
		{
			$indexitem = CreateObject('pbsearch.IndexBaseDataItem', 'projects', $fields['project_id']);

			$indexitem->setValue('DataDisplayName', $fields['number'].' '.$fields['title']);
			$indexitem->setValue('DataOwnerId', $fields['owner']);
			$indexitem->setValue('DataAccess', $fields['access']);
			$indexitem->setValue('DataTimeCreated', '');
			$indexitem->setValue('DataTimeModified', '');
			$indexitem->setValue('DataCategoryId', $fields['cat']);
			
			$data['title']          = $fields['title'];
			$data['number']         = $fields['number'];
			$data['investment_nr']  = $fields['investment_nr'];
			$data['descr']          = $fields['descr'];
			$data['coordinatorout'] = $fields['coordinatorout'];
			$data['status']         = $fields['status'];
			$data['url']            = $fields['url'];
			$data['reference']      = $fields['reference'];
			$data['customer_nr']    = $fields['customer_nr'];
			$data['test']           = $fields['test'];
			$data['quality']        = $fields['quality'];
			$data['result']         = $fields['result'];
			$data['customerout']    = $fields['customerout'];
			$data['customerorgout'] = $fields['customerorgout'];
			$indexitem->setValue('Data', $data);

			return $indexitem;
		}

	}
?>
