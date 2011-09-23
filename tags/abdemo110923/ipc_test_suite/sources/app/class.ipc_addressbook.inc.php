<?php
	/**
	* IPC Layer
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2003-2004, Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage ipc
	* @version $Id$
	*/

	/**
	* Fassade of the adressbook application.
	* @package addressbook
	*/
	class phpgwapi_ipc_addressbook extends phpgwapi_ipc_
	{
		/**
		* @var object $contacts phpgwapi contacts object
		*/
		protected $contacts;

		/**
		* @var object $vcard import/export vcard object
		*/
		protected $vcard;
		
		/**
		* @var object $validator object of validator class for check email syntax
		*/
		protected $validator;


		/**
		* Constructor
		*/
		public function __construct()
		{
			$this->contacts  = CreateObject('phpgwapi.contacts');
			$this->vcard     = CreateObject('phpgwapi.vcard');
			$this->validator = CreateObject('phpgwapi.validator');
		}
  

		/**
		* Add data in a certain mime type format to the application.
		* @param mixed $data data for adding to the application, the datatype depends on the mime type
		* @param string $type specifies the mime type of the passed data
		* @param string $version specifies the mime type version of the passed data (still no need for this because the version would be recognized automaticly)
		* @return integer id of the added data
		*/
		function addData($data, $type, $version='')
		{
			$fields = array();
			$owner = $GLOBALS['phpgw_info']['user']['account_id'];

			// 1: mapping the mime type to application data
			switch($type)
			{
				case 'text/x-vcard':
				case 'text/vcard':
					// $data contains one vcard
					$fields = $this->_import_vcard($data);
				break;
				case 'x-phpgroupware/addressbook-ldap':
					// $data contains one ldap array structure
					$fields = $this->_import_ldap_array($data);
					if(isset($data['owner']) && $data['owner'])
						$owner = $data['owner'];
				break;
				default:
					return false;
				break;
			}

			if(count($fields) == 0)
				return false;

			$fields['owner'] = $owner;
			$fields['access'] = 'private';

			// 2. Add data to application
			$id = $this->contacts->contact_import($fields);
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
			$owner = $GLOBALS['phpgw_info']['user']['account_id'];

			// First, make sure they have permission to this entry
			if(!$this->contacts->check_read($id, $owner))
			{
				return false;
			}

			$contact_type = $this->contacts->search_contact_type_id($this->contacts->get_type_contact($id));			

			$fields['type']            = $contact_type;
			$fields['id']              = $id;
			$fields['adr_one_type']    = 'WORK';
			$fields['adr_two_type']    = 'HOME';
			$fields['email_type']      = 'INTERNET';
			$fields['email_home_type'] = 'INTERNET';

			if($contact_type == $this->contacts->get_person_name())
			{ // person
				$fieldlist = $this->contacts->person_complete_data($id);

				$fields['owner']    = $fieldlist['owner'];
				$fields['access']   = $fieldlist['access'];
				$fields['createon'] = $fieldlist['createon'];
				$fields['modon']    = $fieldlist['modon'];
	
				/*
				$fields['full_name'] = $fieldlist['full_name'];
				bug: $fieldlist['full_name'] contains two spaces between first and last name, when middle name is empty
				workaround: calculate the fullname like shown below
				*/
				$fields['first_name']   = $fieldlist['first_name'];
				$fields['last_name']    = $fieldlist['last_name'];
				$fields['middle_name']  = $fieldlist['middle_name'];
	
				$fields['full_name']    = $fields['first_name'] . ' ';
				$fields['full_name']   .= ($fields['middle_name'] != '') ? $fields['middle_name'] . ' ' : '';
				$fields['full_name']   .= $fields['last_name'];
	
				$fields['prefix']       = $fieldlist['prefix'];
				$fields['suffix']       = $fieldlist['suffix'];
				$fields['sound']        = $fieldlist['sound'];
				$fields['birthday']     = $fieldlist['birthday'];
				//$fields['note']       = $fieldlist[''];
				//$fields['tz']         = $fieldlist['locations'][$type_work][''];
				//$fields['geo']        = $fieldlist[''];
				$fields['pubkey']       = $fieldlist['pubkey'];
				$fields['org_name']     = $fieldlist['org_name'];
				$fields['org_unit']     = $fieldlist['department'];
				$fields['title']        = $fieldlist['title'];
				//$fields['tel_prefer'] = $fieldlist[''];

				$cats = $this->contacts->get_cats_by_person($id);
				$fields['cat_id'] = implode(",", $cats);
			}
			elseif($contact_type == $this->contacts->get_org_name())
			{ // organization
				$fieldlist = array();

				$data = array('contact_id','owner','access','cat_id','org_name','org_creaton','org_modon');
				$org_data = $this->contacts->get_orgs($data, null, null, null, 'ASC', array('contact_id' => $id), null);

				if(!isset($org_data[0]))
				{
					return false;
				}

				$fields['owner']	  = $org_data[0]['owner'];
				$fields['access']	  = $org_data[0]['access']?$org_data[0]['access']:'';
				$fields['createon']	= $org_data[0]['org_creaton']?$org_data[0]['org_creaton']:'';
				$fields['modon']	  = $org_data[0]['org_modon']?$org_data[0]['org_modon']:'';
				$fields['org_name']	= $org_data[0]['org_name'];
				$fields['cat_id']	  = $org_data[0]['cat_id']?$org_data[0]['cat_id']:'';
				
				$loclist = $this->contacts->get_addr_contact_data($id);
				if(is_array($loclist))
				{
					$fieldlist['locations'] = array();
					while(list($no_use, $loc) = each($loclist))
					{
						$addr_id = $loc['key_addr_id'];
						$fieldlist['locations'][$addr_id] = array(
							'type'        => $loc['addr_description'],
							'add1'        => $loc['addr_add1'],
							'add2'        => $loc['addr_add2'],
							'city'        => $loc['addr_city'],
							'state'       => $loc['addr_state'],
							'postal_code' => $loc['addr_postal_code'],
							'country'     => $loc['addr_country']
						);
					}
				}

				$commlist = $this->contacts->get_comm_contact_data($id);
				if(is_array($commlist))
				{
					$fieldlist['comm_media'] = array();
					while(list($no_use, $comm) = each($commlist))
					{
						$comm_type = $comm['comm_description'];
						$fieldlist['comm_media'][$comm_type] = $comm['comm_data'];
					}
				}

				$cats = $this->contacts->get_cats_by_org($id);
				while(list($no_use, $cat_id) = each($cats))
				{
					if($cat_id)
						$cat_id_list[] = $cat_id;
				}
				$cats = $cat_id_list;
				$fields['cat_id'] = implode(",", $cats);
			}
			else
			{
				return false;
			}

			$type_work = $this->contacts->search_location_type('work');
			$type_home = $this->contacts->search_location_type('home');

			// locations contains a list of loc_id and its date
			if (isset($fieldlist['locations']) && is_array($fieldlist['locations']))
			{
				// locations[loc_id][type] is work or home
				// loc_id is not  interested here, but the type is important!
				while ( list($loc_id, $loc_data) = each($fieldlist['locations']) )
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
						continue;
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

			if (isset($fieldlist['comm_media']) && is_array($fieldlist['comm_media']))
			{
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
			}

			// convert fields to mime type format
			switch($type)
			{
				case 'text/x-vcard':
				case 'text/vcard':
					return $this->_export_vcard($fields, $version);
				break;
				case 'text/xml':
					return $this->_export_xml($fields);
				break;
				case 'x-phpgroupware/search-index-data-item':
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
		* @param integer $lastmod last modification time, default is -1 and means return all data id's.
		* @param string $restriction restrict the result for a special use of the id list. The possible restrictions are 'syncable' or 'searchable'. When using 'syncable' only person ids will be returned in the result. 'searchable' returns all ids for both persons and orgs without check the owner. Otherwise no restriction will be used and the result contains all ids for both persons and orgs from the owner.
		* @return array list of data id's
		*/
		function getIdList($lastmod=-1, $restriction='')
		{
			$idList = array();
			$lastmod = intval($lastmod);
			$owner = $GLOBALS['phpgw_info']['user']['account_id'];

			//$this->contacts->read(null, false, null, null, null, null, null, $lastmod);
			// read_contacts doesnt allow lastmod time -> workaround:

			if($lastmod > 0)
			{
				$sql  = 'SELECT DISTINCT c.contact_id AS contact_id ';
				$sql .=	'FROM phpgw_contact c ';
				$sql .=	'JOIN phpgw_contact_addr ca ON (c.contact_id=ca.contact_id) ';
				$sql .=	'JOIN phpgw_contact_comm cc ON (c.contact_id=cc.contact_id) ';
				$sql .=	'JOIN phpgw_contact_note cn ON (c.contact_id=cn.contact_id) ';
				if($restriction == 'syncable')
				{ // only persons - no organizations
					$sql .=	'JOIN phpgw_contact_person cp ON (c.contact_id=cp.person_id) ';				
				}
				else
				{ // persons and organizations
					$sql .=	'JOIN phpgw_contact_person cp ON (c.contact_id=cp.person_id) ';
					$sql .=	'JOIN phpgw_contact_org co ON (c.contact_id=co.org_id) ';
				}
				$sql .=	'WHERE ';
				if($restriction != 'searchable')
				{ // only owners data
					$sql .=	' (c.owner = '.$owner.')';
					$sql .=	' AND';
				}

				$sql .=	' (';
				$sql .=	'  (ca.modified_on > '.$lastmod.') OR ';
				$sql .=	'  (cc.modified_on > '.$lastmod.') OR ';
				$sql .=	'  (cn.modified_on > '.$lastmod.') OR ';

				if($restriction == 'syncable')
				{ // only persons - no organizations
					$sql .=	'  (cp.modified_on > '.$lastmod.') ';
				}
				else
				{ // persons and organizations
					$sql .=	'  (cp.modified_on > '.$lastmod.') OR ';
					$sql .=	'  (co.modified_on > '.$lastmod.') ';
				}

				$sql .=	' ) ';
				$sql .=	'ORDER BY c.contact_id';
			}
			else
			{
				$sql  = 'SELECT DISTINCT c.contact_id AS contact_id ';
				$sql .=	'FROM phpgw_contact c ';

				if($restriction == 'syncable')
				{ // only persons - no organizations
					$sql .=	'JOIN phpgw_contact_person cp ON (c.contact_id=cp.person_id) ';
				}

				if($restriction != 'searchable')
				{ // only owners data
					$sql .=	'WHERE c.owner = '.$owner.' ';
				}

				$sql .=		'ORDER BY c.contact_id';
			}
			$contacts = $this->contacts->db->query($sql,__LINE__,__FILE__);
			while ($this->contacts->db->next_record())
			{
				$cid = $this->contacts->db->Record['contact_id'];
				if($this->contacts->check_read($cid, $owner))
				{
					$idList[] =	$cid;
				}
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
			$owner = $GLOBALS['phpgw_info']['user']['account_id'];

			if(!$this->contacts->check_delete($id, $owner))
			{
				return false;
			}
			else
			{
				$this->contacts->delete_orgs_by_person($id);
				$this->contacts->delete_contact($id);

				//$this->contacts->delete_contact($id);
				//provide no useable return value -> workaround: check for db error
				if($GLOBALS['phpgw']->db->Error)
					return false;
				else
					return true;
			}
		}


		/**
		* Replace the existing data of the passed id with the passed data in a certain mime type format.
		* @param integer $id id of data to replace
		* @param mixed $data the new data, the datatype depends on the passed mime type
		* @param string $type specifies the mime type of the passed data
		* @param string $version specifies the mime type version of the passed data (still no need for this because the version would be recognized automaticly)
		* @return boolean true if the data is replaced, otherwise false
		*/
		function replaceData($id, $data, $type, $version='')
		{
			$fields = array();
			$owner = $GLOBALS['phpgw_info']['user']['account_id'];

			if(!$this->contacts->check_edit($id, $owner))
			{
				return false;
			}
			
			// 1: mapping the mime type to application data
			switch($type)
			{
				case 'text/x-vcard':
				case 'text/vcard':
					// $data contains one vcard
					$fields = $this->_import_vcard($data);
				break;
				case 'x-phpgroupware/addressbook-ldap':
					// $data contains one ldap array structure
					$fields = $this->_import_ldap_array($data);
					if(isset($data['owner']) && $data['owner'])
						$owner = $data['owner'];
				break;
				default:
					return false;
				break;
			}

			if(count($fields) == 0)
				return false;
			
			$fields['owner'] = $owner;
			$fields['access'] = 'private';
			$fields['contact_id'] = $id;

			// 2. Replace data to application
			return $this->contacts->contact_import($fields, '', true);
		}


		/**
		* Checks if data for the passed id exists.
		* @param integer $id id to check
		* @return boolean true if the data with id exist, otherwise false
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
		
		// below are some private helper methods

		/**
		* Convert the passed vcard into the internal array structure
		* @access private
		* @param string $data vcard data string
		* @return array internal fields
		*/
		function _import_vcard($data)
		{
			$data = ereg_replace("\n\n", "\r\n", $data); // xml-rpc bug: \r\n -> \n\n
			$data_lines = explode("\r\n", $data);

			$buffer = array();
			$temp_line = '';

			while(list(, $line) = each($data_lines))
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
			
			if(count($buffer) == 0)
				return array();
			else
				return $this->vcard->in($buffer);
		}

		/**
		* Convert the internal array structure into a vcard string
		* @access private
		* @param array $fields data array
		* @param string $version specifies the exported vcard version
		* @return string vcard
		*/
		function _export_vcard($fields, $version)
		{
			$vcardString = '';
			$buffer      = array();

			if(count($fields) == 0)
				return $vcardString;

			// set translation variable
			$myexport = $this->vcard->export;
			// check that each $fields exists in the export array and
			// set a new array to equal the translation and original value
			while( list($name,$value) = each($fields) )
			{
				if ($myexport[$name] && ($value != "") )
				{
					//echo '<br>'.$name."=".$fields[$name]."\n";
					$buffer[$myexport[$name]] = $value;
				}
			}

			// create a vcard from this translated array
			if($version == '3.0')
				$vcardString = $this->vcard->out_version_30($buffer);
			else
				$vcardString = $this->vcard->out($buffer);

			return $vcardString;
		}

		/**
		* Convert the passed ldap array into the internal array structure
		* @access private
		* @param string $data vcard data string
		* @return array list of arrays with internal fields
		*/
		function _import_ldap_array($data)
		{
			$fields = array();

			if(!is_array($data) || !isset($data['count']))
				return $fields;

			for($a=0;$a<$data['count'];++$a)
			{
				$attribute = strtolower($data[$a]);
				switch($attribute)
				{
					case 'objectclass':
					break;
					case 'rdn':
					break;
					case 'cn': // common name
						if(isset($data[$attribute][0]) && !(isset($fields['first_name']) || isset($fields['last_name'])))
						{
							$fields['full_name'] = utf8_decode($data[$attribute][0]);
							$names = explode(' ', $fields['full_name'], 3);
							switch(count($names))
							{
								case 2:
									$fields['first_name'] = $names[0];
									$fields['last_name'] = $names[1];
								break;
								case 3:
									$fields['first_name'] = $names[0];
									$fields['middle_name'] = $names[1];
									$fields['last_name'] = $names[2];
								break;
							}
						}
					break;
					case 'sn': // last name
					case 'surname':
						if(isset($data[$attribute][0]))
							$fields['last_name'] = utf8_decode($data[$attribute][0]);
					break;
					case 'givenname': // first name
						if(isset($data[$attribute][0]))
							$fields['first_name'] = utf8_decode($data[$attribute][0]);
					break;
					case 'initials':
						if(isset($data[$attribute][0]) && !isset($fields['initials']))
							$fields['initials'] = utf8_decode($data[$attribute][0]);
					break;
					case 'title':
					case 'personaltitle':
						if(isset($data[$attribute][0]))
							$fields['title'] = utf8_decode($data[$attribute][0]);
					break;
					case 'distinguishedname':
						if(isset($data[$attribute][0]))
						{
							$add_parts = explode(',', utf8_decode($data[$attribute][0]));
							for($k=0;$k<count($add_parts);++$k)
							{
								list($name, $value) = split('=', $add_parts[$k], 2);
								$value = utf8_decode($value);
								switch(strtolower($name))
								{
									case 'cn': // common name
									break;
									case 'ou': // org unit -> department
										if($value && !isset($fields['department']))
											$fields['department'] = $value;
									break;
									case 'o': // org -> preferred_org
										if($value && !isset($fields['preferred_org']))
											$fields['preferred_org'] = $value;
									break;
									case 'c': // country
									break;
								}
							}
						}
					break;
					case 'department':
						if(isset($data[$attribute][0]))
							$fields['department'] = utf8_decode($data[$attribute][0]);
					break;

					// location
					case 'postaladdress':
						if(isset($data[$attribute][0]))
							$fields['locations']['work']['add1'] = utf8_decode($data[$attribute][0]);
						if(isset($data[$attribute][1]))
							$fields['locations']['home']['add1'] = utf8_decode($data[$attribute][1]);
					break;
					case 'postofficebox':
						if(isset($data[$attribute][0]))
							$fields['locations']['work']['add2'] = utf8_decode($data[$attribute][0]);
						if(isset($data[$attribute][1]))
							$fields['locations']['home']['add2'] = utf8_decode($data[$attribute][1]);
					break;
					case 'postalcode':
						if(isset($data[$attribute][0]))
							$fields['locations']['work']['postal_code'] = utf8_decode($data[$attribute][0]);
						if(isset($data[$attribute][1]))
							$fields['locations']['home']['postal_code'] = utf8_decode($data[$attribute][1]);
					break;
					case 'l': // Locality -> city
					case 'locality':
						if(isset($data[$attribute][0]))
							$fields['locations']['work']['city'] = utf8_decode($data[$attribute][0]);
						if(isset($data[$attribute][1]))
							$fields['locations']['home']['city'] = utf8_decode($data[$attribute][1]);
					break;
					case 'countryname':
						if(isset($data[$attribute][0]))
							$fields['locations']['work']['country'] = utf8_decode($data[$attribute][0]);
						if(isset($data[$attribute][1]))
							$fields['locations']['home']['country'] = utf8_decode($data[$attribute][1]);
					break;
					case 'textencodedoraddress':
						if(isset($data[$attribute][0]))
						{
							$add_parts = explode(';', utf8_decode($data[$attribute][0]));
							for($k=0;$k<count($add_parts);++$k)
							{
								list($name, $value) = split('=', $add_parts[$k], 2);
								switch(strtolower($name))
								{
									case 'c':
									break;
									case 'a':
									break;
									case 'p':
									break;
									case 'o':
									break;
									case 's': // sur name -> last name
										if($value && !isset($fields['last_name']))
											$fields['last_name'] = $value;
									break;
									case 'g': // given name -> first name
										if($value && !isset($fields['first_name']))
											$fields['first_name'] = $value;
									break;
									case 'i': // initials
										if($value && !isset($fields['initials']))
											$fields['initials'] = $value;
									break;
								}
							}
						}
					break;

					// comm_media
					case 'facsimiletelephonenumber':
					case 'homephone':
					case 'telephonenumber':
					case 'mail':
					case 'rfc822mailbox':
					case 'othermailbox':
						for($k=0;$k<$data[$attribute]['count'];++$k)
						{
							if(!isset($data[$attribute][$k]))
							{
								break;
							}
							else
							{
								$is_found = false;
								$is_saved = false;
								$continue_next = false;
								$value = $data[$attribute][$k];

								switch($attribute)
								{
									case 'facsimiletelephonenumber':
										$fill_map = array('work fax', 'home fax');
										$save_other = true;
									break;
									case 'homephone':
										$fill_map = array('home phone', 'voice phone', 'car phone', 'msg phone', 'work phone');
										$save_other = true;
									break;
									case 'telephonenumber':
										$fill_map = array('work phone', 'voice phone', 'car phone', 'msg phone', 'home phone');
										$save_other = true;
									break;
									case 'mail':
									case 'rfc822mailbox':
									case 'othermailbox':
										$fill_map = array('work email', 'home email');
										$save_other = true;
										if(!$this->validator->is_email($value))
										{
											$continue_next = true;
										}
									break;
									default:
										$continue_next = true;
									break;
								}
								
								if($continue_next == true)
								{ // continue with next $k in for
									continue;
								}

								foreach($fill_map as $fill)
								{
									if(isset($fields['comm_media'][$fill]))
									{
										if($value == $fields['comm_media'][$fill])
										{
											$is_found = true;
											break;
										}
									}
								}
								
								if(!$is_found)
								{ // value was not found in fields -> save it
									foreach($fill_map as $fill)
									{
										// search the first not set fill field
										if(!isset($fields['comm_media'][$fill]))
										{ // save value in fields
											$fields['comm_media'][$fill] = $value;
											$is_saved = true;
											break;
										}
									}
									
									if(!$is_saved)
									{ // save as other field
										$fields[$attribute] = $value;
									}
								}
							}
						}
					break;
					case 'mobile':
						if(isset($data[$attribute][0]))
							$fields['comm_media']['mobile (cell) phone'] = $data[$attribute][0];
					break;
					case 'internationalisdnnumber':
						if(isset($data[$attribute][0]))
							$fields['comm_media']['isdn'] = $data[$attribute][0];
					break;
					case 'pager':
						if(isset($data[$attribute][0]))
							$fields['comm_media']['pager'] = $data[$attribute][0];
					break;
					case 'homepage':
					case 'url':
						if(isset($data[$attribute][0]))
							$fields['comm_media']['website'] = utf8_decode($data[$attribute][0]);
					break;

					// other
					default:
						// save any other ldap info as other fields
						//if(isset($data[$attribute][0]))
						//	$fields[$attribute] = utf8_decode($data[$attribute][0]);
					break;
				}
			}

			if(isset($fields['locations']))
			{
				if(isset($fields['locations']['work']))
					$fields['locations']['work']['type'] = 'work';
				if(isset($fields['locations']['home']))
					$fields['locations']['home']['type'] = 'home';
			}

			return $fields;
		}

		/**
		* Convert the internal array structure into a xml string
		* @access private
		* @param array $fields data array
		* @return string xml
		*/
		function _export_xml($fields)
		{
			$dom_doc = domxml_new_doc("1.0");

			// contact/
			$elem_contact = $dom_doc->create_element('contact');
			$node_contact = $dom_doc->append_child($elem_contact);
			
			while(list($element, $value) = each($fields))
			{
				// contact/<element>
				$xmlElement = $dom_doc->create_element($element);
				$xmlNode = $node_contact->append_child($xmlElement);
				// contact/<element> text string
				$xmlNode->set_content($value);
			}
			
			$xml_string = $dom_doc->dump_mem(true);
			return $xml_string;
		}

		function &_export_index_data_item($fields)
		{
			$index_xml_item = CreateObject('search.index_xml_item', 'addressbook', $fields['id']);

			if($fields['type'] == 'Persons')
				$index_xml_item->setDisplayName($fields['full_name']);
			elseif($fields['type'] == 'Organizations')
				$index_xml_item->setDisplayName($fields['org_name']);

			$content = $this->_export_xml($fields);
			$index_xml_item->setContent($content, 'text/xml', '1.0');
			$index_xml_item->setContentTransferEncoding('base64');

			$catId   = $fields['cat_id']?$fields['cat_id']:'';
			$catName = $fields['cat']?$fields['cat']:'';
			$index_xml_item->setCategory($catId, $catName);

			$ownerId = $fields['owner'];
			$groupId = '';
			$visibilty = $fields['access'];
			$index_xml_item->setAccess($ownerId, $groupId, $visibilty);

			$created    = $fields['createon'];
			$modified   = $fields['modon'];
			$lastAccess = '';
			$index_xml_item->setTimestamp($created, $modified, $lastAccess);

			return $index_xml_item;
		}
	}
?>
