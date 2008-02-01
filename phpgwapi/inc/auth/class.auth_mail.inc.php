<?php
	/**
	* Authentication based on Mail server
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id$
	*/

	/**
	* Authentication based on Mail server
	*
	* @package phpgwapi
	* @subpackage accounts
	* @ignore
	*/
	class auth_mail extends auth_
	{
		
		function auth_mail()
		{
			parent::auth();
		}
		
		function authenticate($username, $passwd)
		{
			error_reporting(error_reporting() - 2);

			if ($GLOBALS['phpgw_info']['server']['mail_login_type'] == 'vmailmgr')
			{
				$username = $username . '@' . $GLOBALS['phpgw_info']['server']['mail_suffix'];
			}
			if ($GLOBALS['phpgw_info']['server']['mail_login_type'] == 'ispman')
			{
				$username = $username . '_' . str_replace('.', '_', $GLOBALS['phpgw_info']['server']['mail_suffix']);
			}
			if ($GLOBALS['phpgw_info']['server']['mail_server_type']=='imap')
			{
				$GLOBALS['phpgw_info']['server']['mail_port'] = '143';
			}
			elseif ($GLOBALS['phpgw_info']['server']['mail_server_type']=='pop3')
			{
				$GLOBALS['phpgw_info']['server']['mail_port'] = '110';
			}
 			elseif ($GLOBALS['phpgw_info']['server']['mail_server_type']=='imaps')
 			{
 				$GLOBALS['phpgw_info']['server']['mail_port'] = '993';
 			}
 			elseif ($GLOBALS['phpgw_info']['server']['mail_server_type']=='pop3s')
 			{
 				$GLOBALS['phpgw_info']['server']['mail_port'] = '995';
 			}

			if( $GLOBALS['phpgw_info']['server']['mail_server_type']=='pop3')
			{
				$mailauth = imap_open('{'.$GLOBALS['phpgw_info']['server']['mail_server'].'/pop3'
					.':'.$GLOBALS['phpgw_info']['server']['mail_port'].'}INBOX', $username , $passwd);
			}
 			elseif ( $GLOBALS['phpgw_info']['server']['mail_server_type']=='imaps' )
 			{
 				// IMAPS support:
 				$mailauth = imap_open('{'.$GLOBALS['phpgw_info']['server']['mail_server']."/ssl/novalidate-cert"
										 .':993}INBOX', $username , $passwd);
 			}
 			elseif ( $GLOBALS['phpgw_info']['server']['mail_server_type']=='pop3s' )
 			{
 				// POP3S support:
 				$mailauth = imap_open('{'.$GLOBALS['phpgw_info']['server']['mail_server']."/ssl/novalidate-cert"
										 .':995}INBOX', $username , $passwd);
			}
			else
			{
				/* assume imap */
				$mailauth = imap_open('{'.$GLOBALS['phpgw_info']['server']['mail_server']
					.':'.$GLOBALS['phpgw_info']['server']['mail_port'].'}INBOX', $username , $passwd);
			}

			error_reporting(error_reporting() + 2);
			if ($mailauth == False)
			{
				return False;
			}
			else
			{
				imap_close($mailauth);
				return True;
			}
		}

		function change_password($old_passwd, $new_passwd)
		{
			return False;
		}
	}
?>
