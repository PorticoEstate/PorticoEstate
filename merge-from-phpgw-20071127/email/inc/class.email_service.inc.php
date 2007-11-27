<?php
	/**
	* EMail - Service
	*
	* @author @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id $
	*/


	/**
	* Service
	*
	* @package email
	*/	
class email_service
{
	var $msg_bootstrap; //email.msg_bootstrap
	var $bopreferences; 
	var $debug;

	/**
	 * Constructor
	 */ 
	function email_service()
	{
		$this->bopreferences = CreateObject('email.bopreferences');
	}
	

/**
* The method provides the list of email folders
* 
* @return array
* @access public
*/
	function getFolderContent()
	{
		$tpl_set = $GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'];
	
		// make sure we have msg object and a server stream
		$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
		$this->msg_bootstrap->ensure_mail_msg_exists('email.bofolder.folder', $this->debug);
	
		$standard_account = array ('acctnum'        => 0,
		                           'status'         => 'enabled',
		                           'display_string' => '[0] standard'
		                           );

		$extra_account_list = $this->bopreferences->ex_accounts_list();

		array_unshift($extra_account_list, $standard_account);
		$account_list = $extra_account_list;

		$return = array();
		for ($i=0; $i < count($account_list); $i++)
		{
			if ($account_list[$i]['status'] == 'enabled')
			{
				$account_name = substr($account_list[$i]['display_string'],(strrpos($account_list[$i]['display_string'],']')+2));
				$account_name = lang('mailbox').' \''.$account_name.'\'';
				$id = strval('email_'.$account_list[$i]['acctnum']);
				$return[$id] = array('text'      => $account_name,
				                     'parent_id' => '0',
				                     'icon'      => ''
					                  );

				$folder_list_i = $GLOBALS['phpgw']->msg->get_arg_value('folder_list',$account_list[$i]['acctnum']);

				for ($j=0; $j < count($folder_list_i); $j++)
				{
					$separator = $this->get_IMAP_folder_separator($folder_list_i[$j]['folder_long']);
					$path = explode($separator, $folder_list_i[$j]['folder_long']);

					// calculate parent folder
					if (count($path) == 1)
					{
						$parent = 'email_'.$account_list[$i]['acctnum'];
					}
					else
					{
						//special handling for the courir server
						if ( $path[count($path) - 2] == 'INBOX' && $separator == '.' )
						{
							$parent = 'email_'.$account_list[$i]['acctnum'];
						}
						else
						{
							$parent = 'email_'.$account_list[$i]['acctnum'].'_'.$path[count($path) - 2];
						}
					}
						
					// parse link to view email folder
					$folderName = $GLOBALS['phpgw']->msg->prep_folder_out($folder_list_i[$j]['folder_long']);
					$folderLink = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.uiindex.index',
					                                      'fldball[folder]'=>$folderName,
					                                      'fldball[acctnum]'=>$account_list[$i]['acctnum']
					                                      ));

					$id = 'email_'.$account_list[$i]['acctnum'].'_'.$path[count($path)-1];
					$return[$id] = array('text'      => $path[count($path)-1],
					                     'title'     => $folder_list_i[$j]['folder_long'],
					                     'icon'      => 'email/templates/'.$tpl_set.'/images/folders.png',
					                     'parent_id' => $parent,
					                     'href'      => $folderLink,
					                     'target'    => '_parent'
					                    );
				}
			}
		}
		return array('content' => $return);
	}
	

/**
* Get the IMAP folder separator
* 
* @return string
* @access private
*/
	function get_IMAP_folder_separator($string)
	{
		//exchange
		if (strpos($string, '/'))
		{
			$separator = '/';
		}
		//courir
		elseif (strpos($string, '.'))
		{
			$separator = '.';
		}
		else
		{
			$separator = 'I doubt this string is used somewhere';
		}
		return $separator;
	}
}
?>	
