<?php
	/**
	* addRepoExchange
	* @author Philipp Kamps <pkamps@probsuiness.de>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @version
	*/
	include_once('projects/inc/addressrepositories/class.addRepoLDAP.inc.php');

	class addRepoExchange extends addRepoLDAP
	{

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
		function addRepoExchange($DSN)
		{
			parent::addRepoLDAP($DSN);
			$this->filter    = array('(&(objectclass=organizationalPerson)(|(cn=a*)(cn=b*)(cn=c*)(cn=d*)(cn=e*)(cn=f*)(cn=g*)(cn=h*)(cn=i*)))',
		                           '(&(objectclass=organizationalPerson)(|(cn=j*)(cn=k*)(cn=l*)(cn=m*)(cn=n*)(cn=o*)(cn=p*)(cn=q*)(cn=r*)))',
		                           '(&(objectclass=organizationalPerson)(|(cn=s*)(cn=t*)(cn=u*)(cn=v*)(cn=w*)(cn=x*)(cn=y*)(cn=z*)))',
		                           utf8_encode('(&(objectclass=organizationalPerson)(|(cn=ä*)(cn=ö*)(cn=ü*)))')
		                          );
		}

		/**
		* Get list
		*
		*/
		function get_list($category = 'ou=CIS,o=Probusiness', $filter = null, $attributes = false)
		{
			if(!$this->connection)
			{
				if(!$this->connection = $this->LDAPConnect())
				{
					return false;
				}
			}
			if($filter)
			{
				$this->filter = array($filter);
			}
			else
			{
				$allValues = array();
				for($i=0; $i < count($this->filter); $i++)
				{
					if(!$attributes)
					{
						$sri = ldap_search($this->connection, $category, $this->filter[$i]);
					}
					else
					{
						$sri = ldap_search($this->connection, $category, $this->filter[$i],$attributes);
					}
					$values = ldap_get_entries($this->connection, $sri);
					unset($values['count']);
					$allValues = array_merge($allValues, $values);
				}
			}
			return $allValues;
 		}

		function get_categories()
		{
		}
		
		function get_details($id)
		{	
		}
	}
?>
