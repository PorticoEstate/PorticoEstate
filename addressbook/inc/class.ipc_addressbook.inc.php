<?php
	/**
	* IPC Layer
	*
	* @author		Dirk Schaller <dschaller@probusiness.de>
	* @copyright	Copyright (C) 2003 Free Software Foundation http://www.fsf.org/
	* @license		http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package		phpgroupware
	* @subpackage	phpgwapi
	* @version		$Id$
	*/


	/**
	* Fassade of the adressbook application.
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	* @category  ipc
	*/
	class phpgwapi_ipc_addressbook extends phpgwapi_ipc_
	{
		/**
		* @var     object   $contacts  phpgwapi contacts object
		* @access  private
		*/
		protected $contacts;

		/**
		* @var     object   $vcard  import/export vcard object
		*/
		protected $vcard;


		/**
		* Constructor
		*
		* @access  public
		*/
		function __construct()
		{
			$this->contacts = CreateObject('phpgwapi.contacts');
			$this->vcard    = CreateObject('phpgwapi.vcard');
		}


		/**
		* Add data in a certain mime type format to the application.
		*
		* @access  public
		* @param   mixed    $data  data for adding to the application, the datatype depends on the mime type
		* @param   string   $type  specifies the mime type of the passed data
		* @return  integer         id of the added data
		*/
		function addData($data, $type)
		{
			// 1: mapping the mime type to application data
			if($type != 'text/x-vcard')
				return false;

			$data = str_replace("\n\n", "\r\n", $data);
			$data_lines = explode("\r\n", $data);

			$buffer = array();
			$temp_line = '';

			//while(list(, $line) = each($data_lines))
                        if (is_array($data_lines))
                        {
                            foreach($data_lines as $key => $line)
			{
				$line = trim($line);
				if(substr($line, -1) == '=')
				{
					// '=' at end-of-line --line to be continued with next line
					$temp_line .= substr($line, 0, -1);
					continue;
				}
				else
				{
					$line = $temp_line . $line;
					$temp_line = ''; // important for next line which ends with =
				}

				if (strstr($line, 'BEGIN:VCARD'))
				{
					// added for p800 vcards: problem if vcard starts with "<![CDATA["
					$line = strstr($line, 'BEGIN:VCARD');
				}

				$buffer += $this->vcard->parse_vcard_line($line);
			}
                        }

			$fields = $this->vcard->in($buffer);
			$fields['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
			$fields['access'] = 'private';

			$id = $this->contacts->contact_import($fields);
			return $id;
		}


		/**
		* Convert data from a mime type to another.
		*
		* @access  public
		* @param   mixed    $data     data for converting, the datatype depends on the input mime type
		* @param   string   $typeIn   specifies the input mime type of the passed data
		* @param   string   $typeOut  specifies the output mime type of the passed data
		* @return  mixed              converted data from application, the datatype depends on the passed output mime type
		*/
		function convertData($data, $typeIn, $typeOut)
		{
			return false;
		}


		/**
		* Get data from the application in a certain mime type format.
		*
		* @param   integer  $id    id of data to get from the application
		* @param   string   $type  specifies the mime type of the returned data
		* @return  mixed           data from application, the datatype depends on the passed mime type, false if no data exists for the passed id
		*/
		function getData($id, $type)
		{
			if($type != 'text/x-vcard')
			{
				return false;
			}

			if(!$this->contacts->check_read($id))
			{
				return false;
			}

			// First, make sure they have permission to this entry
			$fieldlist = $this->contacts->person_complete_data($id);
			$type_work = $this->contacts->search_location_type('work');
			$type_home = $this->contacts->search_location_type('home');
			/*
			$fields['full_name']            = $fieldlist['full_name'];
			bug: $fieldlist['full_name'] contains two spaces between first and last name, when middle name is empty
			workaround: calculate the fullname like shown below
			*/
			$fields['first_name']           = $fieldlist['first_name'];
			$fields['last_name']            = $fieldlist['last_name'];
			$fields['middle_name']          = $fieldlist['middle_name'];

			$fields['full_name']            = $fields['first_name'] . ' ';
			$fields['full_name']           .= ($fields['middle_name'] != '') ? $fields['middle_name'] . ' ' : '';
			$fields['full_name']           .= $fields['last_name'];

			$fields['prefix']               = $fieldlist['prefix'];
			$fields['suffix']               = $fieldlist['suffix'];
			$fields['sound']                = $fieldlist['sound'];
			$fields['birthday']             = $fieldlist['birthday'];
			//$fields['note']               = $fieldlist[''];
			//$fields['tz']                 = $fieldlist['locations'][$type_work][''];
			//$fields['geo']                = $fieldlist[''];
			$fields['pubkey']               = $fieldlist['pubkey'];
			$fields['org_name']             = $fieldlist['org_name'];
			$fields['org_unit']             = $fieldlist['department'];
			$fields['title']                = $fieldlist['title'];
			$fields['adr_one_type']         = 'WORK';
			$fields['adr_two_type']         = 'HOME';
			//$fields['tel_prefer']         = $fieldlist[''];
			$fields['email_type']           = 'INTERNET';
			$fields['email_home_type']      = 'INTERNET';

			// locations contains a list of loc_id and its date
			if (isset($fieldlist['locations']) && is_array($fieldlist['locations']))
			{
				// locations[loc_id][type] is work or home
				// loc_id is not  interested here, but the type is important!
				//while ( list($loc_id, $loc_data) = each($fieldlist['locations']) )
                                foreach($fieldlist['locations'] as $loc_id => $loc_data)
				{
					$loc_type_id = $this->contacts->search_location_type($loc_data['type']);
					switch($loc_type_id)
					{
					case $type_work:
						$adr = 'adr_one_';
						break;
					case $type_home:
						$adr = 'adr_two_';
						break;
					default:
						continue 2;
						break;
					}
					$fields[$adr.'street']       = $loc_data['add1'];
					$fields[$adr.'ext']          = $loc_data['add2'];
					$fields[$adr.'locality']     = $loc_data['city'];
					$fields[$adr.'region']       = $loc_data['state'];
					$fields[$adr.'postalcode'] 	 = $loc_data['postal_code'];
					$fields[$adr.'countryname']	 = $loc_data['country'];
				}
			}

			$fields['tel_work']             = $fieldlist['comm_media']['work phone'];
			$fields['tel_home']             = $fieldlist['comm_media']['home phone'];
			$fields['tel_voice']            = $fieldlist['comm_media']['voice phone'];
			$fields['tel_work_fax']         = $fieldlist['comm_media']['work fax'];
			$fields['tel_home_fax']         = $fieldlist['comm_media']['home fax'];
			$fields['tel_msg']              = $fieldlist['comm_media']['msg phone'];
			$fields['tel_cell']             = $fieldlist['comm_media']['mobile (cell) phone'];
			$fields['tel_pager']            = $fieldlist['comm_media']['pager'];
			$fields['tel_bbs']              = $fieldlist['comm_media']['bbs'];
			$fields['tel_modem']            = $fieldlist['comm_media']['modem'];
			$fields['tel_car']              = $fieldlist['comm_media']['car phone'];
			$fields['tel_isdn']             = $fieldlist['comm_media']['isdn'];
			$fields['tel_video']            = $fieldlist['comm_media']['video'];
			$fields['email']                = $fieldlist['comm_media']['work email'];
			$fields['email_home']           = $fieldlist['comm_media']['home email'];
			$fields['url']                  = $fieldlist['comm_media']['website'];

			$email = $fields['email'];
			$emailtype = $fields['email_type'];
			if (!$emailtype)
			{
				$fields['email_type'] = 'INTERNET';
			}
			$hemail       = $fields['email_home'];
			$hemailtype   = $fields['email_home_type'];
			if (!$hemailtype)
			{
				$fields['email_home_type'] = 'INTERNET';
			}

			// set translation variable
			$myexport = $this->vcard->export;
			// check that each $fields exists in the export array and
			// set a new array to equal the translation and original value
			//while( list($name,$value) = each($fields) )
                        if (is_array($fields))
                        {
                            foreach($fields as $name => $value)
			{
				if ($myexport[$name] && ($value != "") )
				{
					//echo '<br />'.$name."=".$fields[$name]."\n";
					$buffer[$myexport[$name]] = $value;
				}
			}
                        }

			// create a vcard from this translated array
			$data = $this->vcard->out($buffer);

			if ($data == false)
			{
				return false;
			}

			return $data;
		}


		/**
		* Return a list with the available id's in the application.
		* The optional lastmod parameter allows a limitations of the data id list.
		* The list contains all the id's of the modified data since the passed lastmod timestamp.
		*
		* @param   integer  $lastmod  last modification time, default is -1 and means return all data id's
		* @return  array              list of data id's
		*/
		function getIdList($lastmod=-1)
		{
			$idList = array();
			$lastmod = intval($lastmod);

			//$this->contacts->read(null, false, null, null, null, null, null, $lastmod);
			// read_contacts doesnt allow lastmod time -workaround:
			$owner = intval($GLOBALS['phpgw_info']['user']['account_id']);

			if($lastmod >= 0)
			{
				$sql = 'SELECT DISTINCT c.contact_id AS contact_id '.
					'FROM phpgw_contact c '.
					'LEFT JOIN phpgw_contact_addr ca ON (c.contact_id=ca.contact_id) '.
					'LEFT JOIN phpgw_contact_comm cc ON (c.contact_id=cc.contact_id) '.
					'LEFT JOIN phpgw_contact_note cn ON (c.contact_id=cn.contact_id) '.
					'LEFT JOIN phpgw_contact_person cp ON (c.contact_id=cp.person_id) '.
					'WHERE '.
					' (c.owner = '.$owner.')'.
					' AND'.
					' ('.
					'  (ca.modified_on > '.$lastmod.') OR '.
					'  (cc.modified_on > '.$lastmod.') OR '.
					'  (cn.modified_on > '.$lastmod.') OR '.
					'  (cp.modified_on > '.$lastmod.') '.
					' ) '.
					'ORDER BY c.contact_id';
			}
			else
			{
				$sql = 'SELECT DISTINCT contact_id '.
					'FROM phpgw_contact '.
					'WHERE owner = '.$owner.' '.
					'ORDER BY contact_id';
			}
			$contacts = $this->contacts->db->query($sql,__LINE__,__FILE__);
			while ($this->contacts->db->next_record())
			{
				$idList[] = $this->contacts->db->Record['contact_id'];
			}

			return $idList;
		}


		/**
		* Remove data of the passed id.
		*
		* @param   integer  $id  id of data to remove from the application
		* @return  boolean       true if the data is removed, otherwise false
		*/
		function removeData($id)
		{
			return $this->contacts->delete_contact($id);
		}


		/**
		* Replace the existing data of the passed id with the passed data in a certain mime type format.
		*
		* @param   integer  $id    id of data to replace
		* @param   mixed    $data  the new data, the datatype depends on the passed mime type
		* @param   string   $type  specifies the mime type of the passed data
		* @return  boolean         true if the data is replaced, otherwise false
		*/
		function replaceData($id, $data, $type)
		{
			if($type != 'text/x-vcard')
			{
				return false;
			}

			if(!$this->contacts->check_read($id))
			{
				return false;
			}

			$data = str_replace("\n\n", "\r\n", $data);
			$data_lines = explode("\r\n", $data);

			$buffer = array();
			$temp_line = '';

			//while(list(, $line) = each($data_lines))
                        if (is_array($data_lines))
                        {
                            foreach($data_lines as $key => $line)
			{
				$line = trim($line);
				if(substr($line, -1) == '=')
				{
					// '=' at end-of-line --line to be continued with next line
					$temp_line .= substr($line, 0, -1);
					continue;
				}
				else
				{
					$line = $temp_line . $line;
					$temp_line = ''; // important for next line which ends with =
				}

				if (strstr($line, 'BEGIN:VCARD'))
				{
					// added for p800 vcards: problem if vcard starts with "<![CDATA["
					$line = strstr($line, 'BEGIN:VCARD');
				}

				$buffer += $this->vcard->parse_vcard_line($line);
			}
                        }

			$fields = $this->vcard->in($buffer);
			$fields['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
			$fields['access'] = 'private';
			$fields['contact_id'] = $id;

			return $this->contacts->contact_import($fields, '', true);
		}


		/**
		* Checks if data for the passed id exists.
		*
		* @param   integer  $id  id to check
		* @return  boolean       true if the data with id exist, otherwise false
		*/
		function existData($id)
		{
			if(!$this->contacts->check_read($id))
			{
				return false;
			}
			else
			{
				return true;
			}
		}

	}
