<?php
	/**
	* Authentication based on MS Active Directory Service
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Portions Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id$
	*/

	/**
	* Authentication based on MS Active Directory Service
	*
	* @package phpgwapi
	* @subpackage accounts
	*/
	class phpgwapi_auth_ads extends phpgwapi_auth_exchange
	{
		/**
		* @var string $base_dn the base DN for the LDAP server
		*/
		private $base_dn = ''; //'DC=pbgroup,DC=lan';

		/**
		* @var string $ads_host the Active Directory host to connect to
		*/
		private $ads_host = ''; // example: '192.168.100.1';

		/**
		* @var string $ads_pass The password to use when binding to Active Directory
		*/
		private $bind_password = '';

		public function __construct()
		{
			parent::__construct();
		}
		
		function transform_username($username)
		{
			// see this code as an example
			ldap_bind($this->ads_host, $this->get_base_dn(), $this->bind_password);
			$sr = ldap_search($this->ads_host, $this->get_base_dn(), "mailNickname={$username}", array('cn'));
			$entries = ldap_get_entries($this->ads_host, $sr);
			return $entries[0]['cn'][0];
		}
		
		function get_base_dn()
		{
			return 'CN=Users,'.$this->base_dn;
		}
	}
?>
