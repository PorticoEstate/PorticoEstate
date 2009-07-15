<?php
	/**
	* addRepoPHPGroupware
	* @author Philipp Kamps <pkamps@probsuiness.de>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @version
	*/
	include_once('projects/inc/addressrepositories/class.addressrepository.inc.php');

	class addRepoPHPOrgaContact extends addressrepository
	{

		var $connection;
		
		var $public_functions = array
		(
			'get_list'       => true,
			'get_categories' => true
		);

		/**
		* Adressbook Repository to display organisation to person structure
		*
		* @param $dsn
		*/
		function addRepoPHPOrgaContact($dsn)
		{
			$parsdDSN = $this->parseDSN($dsn);
			$this->type     = 'phpGroupware';
			$this->username = $parsdDSN['username'];
			
			$this->attributes    = array('contact_id', 'per_full_name', 'email', 'owner');
			$this->phpGWcontacts = CreateObject('phpgwapi.contacts');
			$this->category = 'WHERE 1 AND ';
		}

		/**
		* Get list
		*
		*/
		function get_list($category = '', $filter = '', $attributes = false)
		{
			if($filter)
			{
				if(substr($filter, 0, 3) == 'id=')
				{
					$this->set_filter(array(array('id' => substr($filter, 3))));
				}
			}
			if($attributes)
			{
				$this->set_attributes($attributes);
			}
			if($category)
			{
				$this->set_category($category);
			}
			

			/* Tell me how the new contacts backend is working and I use it.
			 * I spend hours to get it to work - noooooo way
			 * - the categorisation for contacts is definitly made by a genius!
			 *
			$this->filter = $this->phpGWcontacts->criteria_for_index($GLOBALS['phpgw']->accounts->account_id);
			$emailRecipient = $this->phpGWcontacts->get_persons($this->attributes, '', '', '', '', $this->filter);
			*/

			$acl = CreateObject('phpgwapi.acl');
			$grants = $acl->get_grants('addressbook');
			$sql_grants = '(';
			foreach($grants as $key => $value)
			{
				if(substr($grants[$key], -1, 1) == '1')
				{
					$sql_grants .= 'phpgw_contact_person.created_by ='.$key.' OR ';
				}
			}
			$sql_grants .= ' 1 ) AND ';
			//die($sql_grants);
			$db  = $GLOBALS['phpgw']->db;
			$db2 = $GLOBALS['phpgw']->db; //poor db abstract layer :-(
			$sql  = 'SELECT phpgw_contact_person.person_id, first_name, last_name, department, add1, city, postal_code ';
			$sql .= 'FROM phpgw_contact ';
			$sql .= 'LEFT JOIN phpgw_contact_org_person ON (phpgw_contact.contact_id = phpgw_contact_org_person.person_id) ';
			$sql .= 'LEFT JOIN phpgw_contact_person ON (phpgw_contact_org_person.person_id = phpgw_contact_person.person_id) ';
			$sql .= 'LEFT JOIN phpgw_contact_addr ON ( phpgw_contact_person.person_id = phpgw_contact_addr.contact_id AND phpgw_contact_addr.preferred = "Y" ) ';
			$sql .= $this->category;
			$sql .= $this->filter;
			$sql .= $sql_grants;
			$sql .= '1';	
			//echo $sql;

			$result = $db->query($sql,__LINE__,__FILE__);
			
			$i = 0;
			while ($db->next_record()) // i still a no clue how this api works
			{
				$return[$i] = array('id'           => $db->f('person_id'),
				                    'fullname'     => $this->parseFullName($db->f('last_name'), $db->f('first_name')),
				                    'department'   => $db->f('department'),
				                    'street'       => $db->f('add1'),
				                    'city'         => $db->f('city'),
				                    'postalcode'   => $db->f('postal_code')
					                 );

				$sql  = 'SELECT  comm_descr_id, comm_data FROM  phpgw_contact_comm ';
				$sql .= 'WHERE (comm_descr_id = 2 OR comm_descr_id = 4) AND contact_id = '.$return[$i]['id'].' ';
				$comms = $db2->query($sql,__LINE__,__FILE__);
				while ($db2->next_record())
				{
					if($db2->f('comm_descr_id') == 2)
					{
						$return[$i]['email'] = $db2->f('comm_data');
					}
					elseif ($db2->f('comm_descr_id') == 4)
					{
						$return[$i]['telefone'] = $db2->f('comm_data');
					}
				}
				$i++;
			}
			$this->sortData($return, 'list');
			return $return;
 		}

		function get_categories()
		{

			$acl = CreateObject('phpgwapi.acl');
			$grants = $acl->get_grants('addressbook');
			$sql_grants = '(';
			foreach($grants as $key => $value)
			{
				if(substr($grants[$key], -1, 1) == '1')
				{
					$sql_grants .= 'phpgw_contact_person.created_by ='.$key.' OR ';
				}
			}
			$sql_grants .= ' 1 ) AND ';
			//die($sql_grants);			

			$db = $GLOBALS['phpgw']->db;
			
			$sql =
			(
				'SELECT org_id, name '.
				'FROM phpgw_contact_org '.
				'WHERE active =  "Y" '.
				' ORDER BY name'
			);
	
			//echo $sql;
			$result = $db->query($sql,__LINE__,__FILE__);
	
			while ($db->next_record()) // i still a no clue how this api works
			{
				$categories[] = array('id'     => $db->f('org_id'),
								              'name'   => $db->f('name')
								             );
			}
			$this->sortData($categories, 'categories');
			return $categories;
		}
		
		function get_details($id)
		{	
		}
		
		function set_attributes($attributes)
		{
			$mappedAttributes = array();
			for ($i=0; $i < count($attributes); $i++)
			{
			}
			$this->attributes = $mappedAttributes;
			return true;
		}
		
		function set_category($category)
		{
			if($category)
			{
				$this->category = 'WHERE phpgw_contact_org_person.org_id = '.$category.' AND ';
			}
		}
		
		function set_filter($filter)
		{
			$this->filter = '';
			for($i = 0; $i < count($filter); $i++)
			{
				$value = str_replace('*', '', $filter[$i][key($filter[$i])]);
				$field = $this->map_attribute(key($filter[$i]));
				if(strlen($value) && $field)
				{
					switch($field)
					{
						case 'phpgw_contact.contact_id':
						$this->filter .= $field.'="'.$value.'" AND ';
						break;
						
						default:
						$this->filter .= $field.' LIKE "%'.$value.'%" AND ';
					}
				}
			}
		}
		
		function map_attribute($attribute)
		{
			switch ($attribute)
			{
				case 'id':
				return 'phpgw_contact.contact_id';
				break;
				
				case 'fullname':
				return 'per_full_name';
				break;

				case 'email':
				return 'email';
				break;

				case 'street':
				return 'addr_add1';
				break;

				case 'postalcode':
				return 'addr_postal_code';
				break;

				case 'city':
				return 'addr_city';
				break;
				
				case 'organization':
				return 'org_name';
				break;

				case 'department':
				return 'per_department';
				break;

				case 'telefone':
				return 'tel_work';
				break;

				case 'lastname':
				return 'last_name';
				break;
			}
		}
	}
?>
