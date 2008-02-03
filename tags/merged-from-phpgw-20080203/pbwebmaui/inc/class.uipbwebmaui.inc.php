<?php
/**
 * pbwebmaui
 *
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @copyright Copyright (C) 2003,2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package pbwebmaui
 * @version $Id$
 */


/**
* pbwebmaui user interface
*
* @package pbwebmaui
*/
class uipbwebmaui
{
	
	var $public_functions = array
		(
			'list_domain'           => true,
			'list_maildrops'        => true,
			'show_adminMailserver'  => true,
			'show_adminSiteConf'    => true,
			'add_mailAccount'       => true,
			'add_maildrop'          => true,
			'edit_mailAccount'      => true,
			'edit_mailDrop'         => true,
			'list_filter'           => true,
			'edit_mailFilter'       => true,
		);
	
	var $t;
	var $bo;
	
	function uipbwebmaui()
	{
		$this->t           = $GLOBALS['phpgw']->template;
		$this->bo          = CreateObject('pbwebmaui.bopbwebmaui');
	}
	
	function list_domain($domain = '')
	{
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar(); // phpgw_header is need, otherwise an error comes up

		$_GET['action']   = 'ViewDomain';

		$this->bo->run_pbwebmaui();
		$GLOBALS['phpgw']->common->phpgw_footer();
	}
	
	function list_maildrops($domain = '')
	{
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();

		$_GET['action'] = 'ViewDrops';

		$this->bo->run_pbwebmaui();
	}

	function add_mailAccount($domain = '')
	{
		if(!$_REQUEST['btnOk'])
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}
		$_GET['action']   = 'EditAccount';
		
		$this->bo->run_pbwebmaui();
	}

	function add_maildrop($domain = '')
	{
		if(!$_REQUEST['btnOk'])
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}
		
		$_GET['action']   = 'EditDrop';
		$this->bo->run_pbwebmaui();
	}

	function edit_mailAccount($domain = '')
	{
		if(!($_POST['btnOk'] || $_POST['btnDeleteMail']))
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}
		
		$_GET['action']   = 'EditAccount';
		unset($_GET['domain']);
		$_GET['dn'] = $this->getMailAccountDNforUser();
		
		$this->bo->run_pbwebmaui();
	}

	function edit_mailDrop($domain = '')
	{
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
		
		$_GET['action']   = 'EditDrop';
		
		$this->bo->run_pbwebmaui();
	}
	
	function list_filter($domain = '')
	{
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
		
		$_GET['action']   = 'FilterList';
		$_GET['dn'] = $this->getMailAccountDNforUser();
		
		$this->bo->run_pbwebmaui();
	}

	function list_folders()
	{
		$GLOBALS['phpgw']->common->phpgw_header();
		//echo parse_navbar();
		
		$_GET['action']   = 'FolderList';
		
		$this->bo->run_pbwebmaui();
	}
	
	function edit_mailFilter($domain = '')
	{
		if(!$_POST['btnOk'])
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}
		
		$_GET['action']   = 'EditFilter';
		$_GET['dn'] = $this->getMailAccountDNforUser();
		
		$this->bo->run_pbwebmaui();
	}

	function show_adminSiteConf()
	{
		if($_POST['pbwebmaui_save'])
		{
			$toSave['dsn_mailaccounts']       = 'ldap://'.$_POST['pbwebmaui_username'].':'.$_POST['pbwebmaui_password'].'@'.$_POST['pbwebmaui_host'].':'.$_POST['pbwebmaui_port'].'/'.$_POST['pbwebmaui_accountbasedn'];
			$toSave['dsn_maildrops']          = 'ldap://'.$_POST['pbwebmaui_username'].':'.$_POST['pbwebmaui_password'].'@'.$_POST['pbwebmaui_host'].':'.$_POST['pbwebmaui_port'].'/'.$_POST['pbwebmaui_maildropbasedn'];
			$toSave['courierscript']          = $_POST['pbwebmaui_courierscript'];
			$toSave['mailaccountdir']         = $_POST['pbwebmaui_mailaccountdir'];
			$toSave['mailaccountdir_archive'] = $_POST['pbwebmaui_mailaccountdir_archive'];
			$toSave['syncAcc']                = $_POST['pbwebmaui_syncAcc'];
			$toSave['keepDeleted']            = $_POST['pbwebmaui_keepDeleted'];
			$toSave['syncGroup']              = $_POST['pbwebmaui_syncGroup'];
			$this->bo->save_preferences($toSave);
		}
		
		$GLOBALS['phpgw_info']['flags']['app_header'] = lang('pbWebMAUI site configuration').'<br>';
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar(); // phpgw_header is need, otherwise an error comes up
		
		$preferences = $this->bo->read_pbwebmaui_pref();
		
		$givenValuesAccounts  = $this->bo->parseDSN($preferences['dsn_mailaccounts']);
		$givenValuesMailDrops = $this->bo->parseDSN($preferences['dsn_maildrops']);
		
		$this->t->set_file(array('adminPref' => 'adminSiteConf.tpl'));
		
		$this->t->set_var('action', $GLOBALS['phpgw']->link('/index.php','menuaction=pbwebmaui.uipbwebmaui.show_adminSiteConf'));
		$this->t->set_var('l_save', lang('save'));
		$this->t->set_var('l_mailservertype', lang('Supported Mailserver Types').':');
		//$this->t->set_var('v_mailservertype', lang('Supported Mailserver Types').':'); /still missing
		$this->t->set_var('l_courierscript', lang('Location of courier control script').':');
		$this->t->set_var('v_courierscript', $preferences['courierscript']);
		$this->t->set_var('l_mailaccountdir', lang('Mailaccount spool directory').':');
		$this->t->set_var('v_mailaccountdir', $preferences['mailaccountdir']);
		$this->t->set_var('l_mailaccountdir_archive', lang('Directory for Archives').':');
		$this->t->set_var('v_mailaccountdir_archive', $preferences['mailaccountdir_archive']);
		$this->t->set_var('l_yes', lang('yes'));
		$this->t->set_var('l_no', lang('no'));
		$this->t->set_var('l_syncAcc', lang('Synchronize Accounts').':');
		if ($preferences['syncAcc'])
		{
			$this->t->set_var('v_syncAcc1', 'selected');
		}
		else
		{
			$this->t->set_var('v_syncAcc0', 'selected');
		}

		if ($preferences['keepDeleted'])
		{
			$this->t->set_var('v_keepDeleted1', 'selected');
		}
		else
		{
			$this->t->set_var('v_keepDeleted0', 'selected');
		}

		if ($preferences['syncGroup'])
		{
			$this->t->set_var('v_syncGroup1', 'selected');
		}
		else
		{
			$this->t->set_var('v_syncGroup0', 'selected');
		}
		$this->t->set_var('l_syncGroup', lang('Synchronize Groups').':');
		$this->t->set_var('l_keepDeleted', lang('Keep mail-accounts of deleted phpGroupware Users').':');
		$this->t->set_var('l_save', lang('save'));
		$this->t->set_var('l_cancel', lang('cancel'));
		
		$this->t->pfp('out','adminPref');
		$GLOBALS['phpgw']->common->phpgw_footer();
	}
	
	
	function getMailAccountDNforUser()
	{
		if($_GET['dn'] == '')
		{
			$email = $GLOBALS['phpgw']->session->user['session_lid']; 

			$mailaccount = new Mailaccount($this->pbwebmaui, '', $GLOBALS['hook_values']['account_lid'], $email, $GLOBALS['phpgw_info']['server']['default_domain']);
			if ($mailaccount->exists())
			{
				return $mailaccount->getAttribute("dn");
			} 
		}
		else
		{
			return $_GET['dn'];
		}
	}
		
}
?>