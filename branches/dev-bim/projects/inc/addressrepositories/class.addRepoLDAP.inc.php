<?php
	/**
	* addRepoLDAP
	* @author Philipp Kamps <pkamps@probsuiness.de>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @version
	*/
	include_once('projects/inc/addressrepositories/class.addressrepository.inc.php');

	class addRepoLDAP extends addressrepository
	{

		var $filter;
		
		var $public_functions = array
		(
			'get_list'       => true,
			'get_categories' => true
		);

		/**
		* exchange
		*
		* @param 
		*/
		function addRepoLDAP($DSN)
		{
			parent::addressrepository($DSN);
			$this->filter = 'objectclass=person';
		}

		function LDAPConnect()
		{
			return ldap_connect($this->hostspec, $this->port);
		}
		
		/**
		* Get list
		*
		*/
		function get_list($category = '', $filter = '', $attributes = false)
		{
			if($attributes)
			{
				$this->set_attributes($attributes);
			}
			if($filter)
			{
				if(substr($filter, 0, 3) == 'id=')
				{
					$category = substr($filter, 3);
					$this->attributes = '';
				}
				else
				{
					$this->filter = $filter;
				}
			}
			if(!$category)
			{
				$category = $this->category;
			}
			if(!$this->connection)
			{
				if(!$this->connection = $this->LDAPConnect())
				{
					return false;
				}
			}
			if($this->filter && $this->attributes)
			{
				$sri = ldap_search($this->connection, $category, $this->filter, $this->attributes);
			}
			elseif($this->filter)
			{
				$sri = ldap_search($this->connection, $category, $this->filter);
			}
			else
			{
				$sri = ldap_search($this->connection, $category);
			}
			ldap_sort($this->connection, $sri, 'cn');
			$allValues = ldap_get_entries($this->connection, $sri);
			$return = array();
			for($i=0; $i < count($allValues); $i++)
			{
				if($allValues[$i]['cn'][0])
				{
					// hack begin
					$fullname = utf8_decode($allValues[$i]['cn'][0]);
					if(strpos($fullname, '@'))
					{
						$fullname = substr($fullname, 0, strpos($fullname, '@'));
					}
					// hack end
					$return[] = array('id'           => $allValues[$i]['dn'],
					                  'fullname'     => $fullname,
					                  'email'        => $allValues[$i]['mail'][0],
					                  'street'       => utf8_decode($allValues[$i]['postaladdress'][0]),
					                  'postalcode'   => $allValues[$i]['postalcode'][0],
					                  'city'         => utf8_decode($allValues[$i]['l'][0]),
					                  'organization' => $allValues[$i]['o'][0],
					                  'department'   => utf8_decode($allValues[$i]['ou'][0]),
					                  'telefone'     => $allValues[$i]['telephonenumber'][0]
					                 );
				}
			}
			return $return;
 		}

		function get_categories()
		{
			if(!$this->connection)
			{
				if(!$this->connection = $this->LDAPConnect())
				{
					return false;
				}
			}
			$sri = ldap_search($this->connection, $this->category, 'objectclass=organizationalunit', array('dn', 'ou'));
			if($sri)
			{
				$allValues = ldap_get_entries($this->connection, $sri);
				$return = array();
				for($i=0; $i < count($allValues); $i++)
				{
					if($allValues[$i]['ou'][0])
					{
						$return[] = array('id'    => $allValues[$i]['dn'],
						                  'name'  => $allValues[$i]['ou'][0]
						                 );
					}
				}
				return $this->keep_hierachy($return);
			}
			return false;
		}
		
		function get_details($id)
		{	
		}
		
		function set_attributes($attributes)
		{
			$mappedAttributes = array();
			for ($i=0; $i < count($attributes); $i++)
			{
				$mappedAttributes[] = $this->map_attribute($attributes[$i]);
			}
			$this->attributes = $mappedAttributes;
		}
		
		function keep_hierachy($values)
		{
			$minlevel = 10000;
			for($i=0; $i < count($values); $i++)
			{
				$level = count(explode(',', $values[$i]['id']));
				if($level < $minlevel)
				{
					$minlevel = $level;
				}
			}
			for($i=0; $i < count($values); $i++)
			{
				$values[$i]['name'] = str_repeat('&nbsp;', count(explode(',', $values[$i]['id'])) - $minlevel ).$values[$i]['name'];
			}
			return $values;
		}
		
		function set_filter($filter)
		{
			$this->filter = 'objectclass=person';
			for($i = 0; $i < count($filter); $i++)
			{
				$value = utf8_encode($filter[$i][key($filter[$i])]);
				$field = $this->map_attribute(key($filter[$i]));
				if(strlen($value) && $field)
				{
					$this->filter = '(&('.$this->filter.')('.$field.'='.$value.'))';
				}
			}
		}
		
		function map_attribute($attribute)
		{
			switch ($attribute)
			{
				case 'id':
				return 'dn';
				break;
				
				case 'fullname':
				return 'cn';
				break;

				case 'email':
				return 'mail';
				break;

				case 'street':
				return 'postaladdress';
				break;

				case 'postalcode':
				return 'postalcode';
				break;

				case 'city':
				return 'l';
				break;
				
				case 'organization':
				return 'o';
				break;

				case 'department':
				return 'ou';
				break;

				case 'telefon':
				return 'telephonenumber';
				break;

				case 'lastname':
				return 'sn';
				break;
			}
		}
	}
?>
