<?php
	/**
	* Authentication based on NIS maps
	* @author Dylan Adams <dadams@jhu.edu>
	* @copyright Copyright (C) 2001 Dylan Adams
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id: class.auth_nis.inc.php 17062 2006-09-03 06:15:27Z skwashd $
	*/

	/**
	* Authentication based on NIS maps
	*
	* @package phpgwapi
	* @subpackage accounts
	* @ignore
	*/
	class auth_nis extends auth_
	{
		
		function auth_nis()
		{
			parent::auth();
		}
		
		function authenticate($username, $passwd)
		{
			$domain = yp_get_default_domain();
			if( !empty($GLOBALS['phpgw_info']['server']['nis_domain']) )
			{
				$domain = $GLOBALS['phpgw_info']['server']['nis_domain'];
			}

			$map = "passwd.byname";
			if( !empty($GLOBALS['phpgw_info']['server']['nis_map']) )
			{
				$map = $GLOBALS['phpgw_info']['server']['nis_map'];
			}
			$entry = yp_match( $domain, $map, $username );

			/*
			 * we assume that the map is structured in the usual
			 * unix passwd flavor
			 */
			$entry_array = explode( ':', $entry );
			$stored_passwd = $entry_array[1];

			$encrypted_passwd = crypt( $passwd, $stored_passwd );

			return( $encrypted_passwd == $stored_passwd );
		}

		function change_password($old_passwd, $new_passwd, $account_id = '')
		{
			// can't change passwords unless server runs as root (bad idea)
			return( False );
		}

	}
?>
