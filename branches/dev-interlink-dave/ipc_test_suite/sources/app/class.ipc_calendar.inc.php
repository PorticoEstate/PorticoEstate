<?php
	/**
	* IPC Layer
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2003-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage communication
	* @version $Id$
	*/

	/**
	* Fassade of the calendar application.
	* @package calendar
	*/
	class calendar_ipc_calendar extends phpgwapi_ipc_
	{
		/**
		* @var object $bo application business object
		*/
		protected $bo;

		/**
		* @var object $bo_iCal application business object for iCalendar import/export
		*/
		protected $bo_iCal;


		/**
		* Constructor
		*/
		public function __construct()
		{
			$this->bo      =& CreateObject('calendar.bocalendar');
			$this->bo_iCal =& CreateObject('calendar.boicalendar');
		}


		/**
		* Add data in a certain mime type format to the application.
		* @param mixed $data data for adding to the application, the datatype depends on the mime type
		* @param string $type specifies the mime type of the passed data
		* @param string $version specifies the mime type version of the passed data
		* @return integer id of the added data
		*/
		function addData($data, $type, $version='')
		{
			// 1: mapping the mime type to application data
			if(($type != 'text/x-ical') && ($type != 'text/calendar'))
				return false;

			$data = ereg_replace("\n\n", "\r\n", $data); // xml-rpc bug: \r\n -> \n\n
			$data = ereg_replace("\r\n\r\n", "\r\n", $data); // ical from calmeno
			$data_lines = explode("\r\n", $data);
			
			$id = $this->bo_iCal->import($data_lines, true);
			return $id;
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
			if(!is_integer($id) || ($id<=0))
				return false;

			switch($type)
			{
				case 'text/x-ical':
				case 'text/calendar':
					return $this->bo_iCal->export(array('l_event_id' => $id));
				break;
				case 'text/xml':
					return false;
				break;
				case 'x-phpgroupware/search-index-data-item':
					$event = $this->bo->read_entry($id);
					if(!$event)
					{
						return false;
					}

					$event_array = $this->bo->event2array($event);
					$fields = array();
					while(list($key, $value) = each($event_array))
					{
						if(isset($value['data']))
						{
							if(is_array($value['data']))
							{
								$fields[$key] = implode(',', $value['data']);
							}
							else
							{
								$fields[$key] = $value['data'];
							}
						}
					}

					$fields['id']          = $id;
					$fields['owner_id']    = $event['owner'];
					$fields['priority']    = $event['priority'];
					$fields['category_id'] = $event['category'];
					$fields['last_mod']    = $this->bo->maketime($event['modtime']);

					return $this->_export_index_data_item($fields);
				break;
				default:
					return false;
			}
		}


		/**
		* Return a list with the available id's in the application.
		* The optional lastmod parameter allows a limitations of the data id list.
		* The list contains all the id's of the modified data since the passed lastmod timestamp.
		* @param integer $lastmod last modification time, default is -1 and means return all data id's
		* @param string $restriction restrict the result for a special use of the id list. The possible restrictions are 'syncable' or 'searchable'. When using 'syncable' only person ids will be returned in the result. 'searchable' returns all ids for both persons and orgs without check the owner. Otherwise no restriction will be used and the result contains all ids for both persons and orgs from the owner.
		* @return array list of data id's, if $lastmod is set, only modified entry id's, else all id's returned
		*/
		function getIdList($lastmod=-1, $restriction='')
		{
			return $this->bo->so->cal->list_dirty_events($lastmod);
		}


		/**
		* Remove data of the passed id.
		* @param integer $id id of data to remove from the application
		* @return boolean true if the data is removed, otherwise false
		*/
		function removeData($id)
		{
			if(!is_integer($id) || ($id<=0))
				return false;

			if($this->bo->delete_entry($id) == 16)
			{
				$this->bo->expunge();
				return true;
			}
			else
				return false;
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
			if(($type != 'text/x-ical') && ($type != 'text/calendar'))
				return false;

			if(!is_integer($id) || ($id<=0))
				return false;

			$entry = $this->bo->read_entry($id);
			if(!$entry || !isset($entry['uid']))
				return false;

			$data = ereg_replace("\n\n", "\r\n", $data); // xml-rpc bug: \r\n -> \n\n
			$data = ereg_replace("\r\n\r\n", "\r\n", $data); // ical from calmeno
			$data_lines = explode("\r\n", $data);

			return $this->bo_iCal->import($data_lines, true);
		}


		/**
		* Checks if data for the passed id exists.
		* @param integer $id id to check
		* @return boolean true if the data with id exist, otherwise false
		*/
		function existData($id)
		{
			if(!is_integer($id) || ($id<=0))
				return false;

			if(!$this->bo->read_entry($id))
				return false;
			else
				return true;
		}

		function &_export_index_data_item($fields)
		{
			$index_xml_item = CreateObject('search.index_xml_item', 'calendar', $fields['id']);
			$index_xml_item->setDisplayName($fields['title']);
			$index_xml_item->setPriority($fields['priority']);

			$catId   = $fields['category_id']?$fields['category_id']:'';
			$catName = $fields['category']?$fields['category']:'';
			$index_xml_item->setCategory($catId, $catName);

			$ownerId = $fields['owner_id'];
			$groupId = '';
			$visibilty = $fields['access'];
			$index_xml_item->setAccess($ownerId, $groupId, $visibilty);

			$created    = $fields['created']?$fields['created']:'';
			$modified   = $fields['last_mod']?$fields['last_mod']:'';
			$lastAccess = $fields['lastAccess']?$fields['lastAccess']:'';
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
