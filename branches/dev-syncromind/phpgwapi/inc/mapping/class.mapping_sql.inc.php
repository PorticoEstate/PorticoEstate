<?php
        /**
	* Mapping REMOTE_USER to account_lid
	* @author DANG Quang Vu <quang_vu.dang@int-evry.fr>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage mapping
	* @version $Id$
	*/
	
	/**
	* this class manage trivial mapping between REMOTE_USER variable (user SSO) and 
	* phpGroupware account using unique ID
	* using with Single Sign-On(Shibboleth,CAS,...)
	* Account repository using SQL DB
	*/
															
	class mapping_sql extends mapping_
	{
	
		/**
		* constructor, sets up variables
		*
		**/
		function mapping_sql($auth_info='')
		{
			parent::mapping($auth_info);
		}
		
		/**
		* mapping_uniqueid
		* function private
		* this function find a mapping between REMOTE_USER variable and phpgw account using unique ID
		* @param string $ext_user the REMOTE_USER of user SSO
		* @return string account_lid if mapping success otherwise ''
		*/										
		function mapping_uniqueid($ext_user)
		{
			if(!isset($GLOBALS['phpgw_info']['server']['mapping_field']) || $GLOBALS['phpgw_info']['server']['mapping_field']=='')
			{
				$GLOBALS['phpgw_info']['server']['mapping_field'] = 'account_lid';
			}											
			$db =& $GLOBALS['phpgw']->db;
			$db->query("SELECT * FROM phpgw_accounts WHERE " . $GLOBALS['phpgw_info']['server']['mapping_field'] 
					. " = '$ext_user'",__LINE__,__FILE__);
			$db->next_record();
			if ($db->f('account_lid'))
			{
				return $db->f('account_lid');
			}
			else
			{
				return '';
			}
		}
		
		/**
		* valid_user
		* function public
		* this function valid an user using login and password
		* @param string $uid 
		* @param string $password
		* @return true if login and password is correct otherwise false
		*/
		function valid_user($uid,$password)
		{
			$auth_type = $GLOBALS['phpgw_info']['server']['auth_type'];
			$GLOBALS['phpgw_info']['server']['auth_type'] = 'sql';
			$auth=CreateObject('phpgwapi.auth');
			$GLOBALS['phpgw_info']['server']['auth_type'] = $auth_type;
			return $auth->authenticate($uid,$password,'text');
		}
	}
?>
