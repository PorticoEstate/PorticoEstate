<?php
	/***************************************************************************\
	* eGroupWare - FeLaMiMail                                                   *
	* http://www.linux-at-work.de                                               *
	* http://www.phpgw.de                                                       *
	* http://www.egroupware.org                                                 *
	* Written by : Lars Kneschke [lkneschke@linux-at-work.de]                   *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/
	/* $Id$ */

	require_once(PHPGW_INCLUDE_ROOT.'/felamimail/inc/class.sopreferences.inc.php');
	 
	class bopreferences extends sopreferences
	{
		var $public_functions = array
		(
			'getPreferences'	=> True,
		);
		
		// stores the users profile
		var $profileData;
		
		function __construct()
		{
			parent::__construct();
			$this->boemailadmin = CreateObject('emailadmin.emailadmin_bo');
		//	$this->boemailadmin = new emailadmin_bo();
		}

		// get the first active user defined account		
		function getAccountData(&$_profileData, $_accountID=NULL)
		{
			if(!is_a($_profileData, 'ea_preferences'))
				die(__FILE__.': '.__LINE__);
			$accountData = parent::getAccountData($GLOBALS['phpgw_info']['user']['account_id'],$_accountID);

			// currently we use only the first profile available
			$accountData = array_shift($accountData);
			#_debug_array($accountData);

			$icServer = CreateObject('emailadmin.defaultimap');
			$icServer->encryption	= isset($accountData['ic_encryption']) ? $accountData['ic_encryption'] : 1;
			$icServer->host		= $accountData['ic_hostname'];
			$icServer->port 	= isset($accountData['ic_port']) ? $accountData['ic_port'] : 143;
			$icServer->validatecert	= isset($accountData['ic_validatecertificate']) ? (bool)$accountData['ic_validatecertificate'] : 1;
			$icServer->username 	= $accountData['ic_username'];
			$icServer->loginName 	= $accountData['ic_username'];
			$icServer->password	= $accountData['ic_password'];
			$icServer->enableSieve	= isset($accountData['ic_enable_sieve']) ? (bool)$accountData['ic_enable_sieve'] : 1;
			$icServer->sieveHost	= $accountData['ic_sieve_server'];
			$icServer->sievePort	= isset($accountData['ic_sieve_port']) ? $accountData['ic_sieve_port'] : 2000;

//			$ogServer =& CreateObject('emailadmin.defaultsmtp');
			$ogServer = CreateObject('emailadmin.defaultsmtp');
			$ogServer->host		= $accountData['og_hostname'];
			$ogServer->port		= isset($accountData['og_port']) ? $accountData['og_port'] : 25;
			$ogServer->smtpAuth	= (bool)$accountData['og_smtpauth'];
			if($ogServer->smtpAuth) {
				$ogServer->username 	= $accountData['og_username'];
				$ogServer->password 	= $accountData['og_password'];
			}

//			$identity =& CreateObject('emailadmin.ea_identity');
			$identity = CreateObject('emailadmin.ea_identity');
			$identity->emailAddress	= $accountData['emailaddress'];
			$identity->realName	= $accountData['realname'];
			//$identity->default	= true;
			$identity->default = (bool)$accountData['active'];
			$identity->organization	= $accountData['organization'];
			$identity->signature = $accountData['signatureid'];
			$identity->id  = $accountData['id'];

			$isActive = (bool)$accountData['active'];

			return array('icServer' => $icServer, 'ogServer' => $ogServer, 'identity' => $identity, 'active' => $isActive);
		}

		function getAllAccountData(&$_profileData)
		{
			if(!is_a($_profileData, 'ea_preferences'))
				die(__FILE__.': '.__LINE__);
			$AllAccountData = parent::getAccountData($GLOBALS['phpgw_info']['user']['account_id'],'all');
			#_debug_array($accountData);
			foreach ($AllAccountData as $key => $accountData)
			{
//				$icServer =& CreateObject('emailadmin.defaultimap');
				$icServer = CreateObject('emailadmin.defaultimap');
				$icServer->encryption	= isset($accountData['ic_encryption']) ? $accountData['ic_encryption'] : 1;
				$icServer->host		= $accountData['ic_hostname'];
				$icServer->port 	= isset($accountData['ic_port']) ? $accountData['ic_port'] : 143;
				$icServer->validatecert	= isset($accountData['ic_validatecertificate']) ? (bool)$accountData['ic_validatecertificate'] : 1;
				$icServer->username 	= $accountData['ic_username'];
				$icServer->loginName 	= $accountData['ic_username'];
				$icServer->password	= $accountData['ic_password'];
				$icServer->enableSieve	= isset($accountData['ic_enable_sieve']) ? (bool)$accountData['ic_enable_sieve'] : 1;
				$icServer->sieveHost	= $accountData['ic_sieve_server'];
				$icServer->sievePort	= isset($accountData['ic_sieve_port']) ? $accountData['ic_sieve_port'] : 2000;

//				$ogServer =& CreateObject('emailadmin.defaultsmtp');
				$ogServer = CreateObject('emailadmin.defaultsmtp');
				$ogServer->host		= $accountData['og_hostname'];
				$ogServer->port		= isset($accountData['og_port']) ? $accountData['og_port'] : 25;
				$ogServer->smtpAuth	= (bool)$accountData['og_smtpauth'];
				if($ogServer->smtpAuth) {
					$ogServer->username 	= $accountData['og_username'];
					$ogServer->password 	= $accountData['og_password'];
				}

//				$identity =& CreateObject('emailadmin.ea_identity');
				$identity = CreateObject('emailadmin.ea_identity');
				$identity->emailAddress	= $accountData['emailaddress'];
				$identity->realName	= $accountData['realname'];
				//$identity->default	= true;
				$identity->default = (bool)$accountData['active'];
				$identity->organization	= $accountData['organization'];
				$identity->signature = $accountData['signatureid'];
				$identity->id  = $accountData['id'];
				$isActive = (bool)$accountData['active'];
				$out[] = array('icServer' => $icServer, 'ogServer' => $ogServer, 'identity' => $identity, 'active' => $isActive);
			}
			return $out;
		}

		function getUserDefinedIdentities()
		{
			$profileData        = $this->boemailadmin->getUserProfile('felamimail');
			if(!is_a($profileData, 'ea_preferences') || !is_a($profileData->ic_server[0], 'defaultimap')) {
				return false;
			}
			if($profileData->userDefinedAccounts) {
				// get user defined accounts
				$allAccountData = $this->getAllAccountData($profileData);
				if ($allAccountData) {
					foreach ($allAccountData as $tmpkey => $accountData)
					{
						$accountArray[] = $accountData['identity'];
					}
					return $accountArray;
				}
			}
			return array();
		}	

		function getPreferences()
		{
			if(!is_a($this->profileData,'ea_preferences '))
			{

				$imapServerTypes	= $this->boemailadmin->getIMAPServerTypes();
				$profileData		= $this->boemailadmin->getUserProfile('felamimail');

				if(!is_a($profileData, 'ea_preferences') || !is_a($profileData->ic_server[0], 'defaultimap')) {

	//				throw new Exception('No preferences or Incoming server defined');
					return false;
				}
				if($profileData->userDefinedAccounts) {
					// get user defined accounts
					$accountData = $this->getAccountData($profileData);
					
					if($accountData['active']) {
					
						// replace the global defined IMAP Server
						if(is_a($accountData['icServer'],'defaultimap'))
							$profileData->setIncomingServer($accountData['icServer'],0);
					
						// replace the global defined SMTP Server
						if(is_a($accountData['ogServer'],'defaultsmtp'))
							$profileData->setOutgoingServer($accountData['ogServer'],0);
					
						// replace the global defined identity
						if(is_a($accountData['identity'],'ea_identity')) {
							$profileData->setIdentity($accountData['identity'],0);
							$rememberID = $accountData['identity']->id;
						}
					}
					$allUserIdentities = $this->getUserDefinedIdentities();
					if (is_array($allUserIdentities)) {
						$i=count($allUserIdentities);
						foreach ($allUserIdentities as $tmpkey => $id)
						{
							if ($id->id != $rememberID) {
								$profileData->setIdentity($id,$i);
								$i++;
							}
						}
					}
				}
				
				$GLOBALS['phpgw']->preferences->read();
				$userPrefs = $GLOBALS['phpgw_info']['user']['preferences']['felamimail'];
				if(empty($userPrefs['deleteOptions']))
					$userPrefs['deleteOptions'] = 'mark_as_deleted';
				
				#$data['trash_folder']		= $userPrefs['felamimail']['trashFolder'];
				if (!empty($userPrefs['trash_folder'])) 
					$userPrefs['move_to_trash'] 	= True;
				if (!empty($userPrefs['sent_folder'])) 
					$userPrefs['move_to_sent'] 	= True;
				$userPrefs['signature']		= isset($userPrefs['email_sig']) ? $userPrefs['email_sig'] : '';
				
	 			unset($userPrefs['email_sig']);
 			
 				$profileData->setPreferences($userPrefs);

				#_debug_array($profileData);exit;
			
				$this->profileData = $profileData;
				
				#_debug_array($this->profileData);
			} 
			return $this->profileData;
		}
		
		function ggetSignature($_signatureID, $_unparsed = false) 
		{
			if($_signatureID == -1) {
				$profileData = $this->boemailadmin->getUserProfile('felamimail');
				
				$systemSignatureIsDefaultSignature = !parent::getDefaultSignature($GLOBALS['phpgw_info']['user']['account_id']);

				$systemSignature = array(
					'signatureid'		=> -1,
					'description'		=> 'eGroupWare '. lang('default signature'),
					'signature'		=> ($_unparsed === true ? $profileData->ea_default_signature : $GLOBALS['phpgw']->preferences->parse_notify($profileData->ea_default_signature)),
					'defaultsignature'	=> $systemSignatureIsDefaultSignature,
				);
				
				return $systemSignature;
				
			} else {
				require_once('class.felamimail_signatures.inc.php');
				$signature = new felamimail_signatures($_signatureID);
				if($_unparsed === false) {
					$signature->fm_signature = $GLOBALS['phpgw']->preferences->parse_notify($signature->fm_signature);
				}
				return $signature;
			}
		}
		
		function ggetDefaultSignature() 
		{
			return parent::getDefaultSignature($GLOBALS['phpgw_info']['user']['account_id']);
		}
		
		function ddeleteSignatures($_signatureID) 
		{
			if(!is_array($_signatureID)) {
				return false;
			}
			return parent::deleteSignatures($GLOBALS['phpgw_info']['user']['account_id'], $_signatureID);
		}
		
		function saveAccountData($_icServer, $_ogServer, $_identity) 
		{
			if(is_object($_icServer) && !isset($_icServer->validatecert)) {
				$_icServer->validatecert = true;
			}
			if(isset($_icServer->host)) {
				$_icServer->sieveHost = $_icServer->host;
			}
			return parent::saveAccountData($GLOBALS['phpgw_info']['user']['account_id'], $_icServer, $_ogServer, $_identity);
		}
	
		function deleteAccountData($_identity)
		{
			if (is_array($_identity)) {
				foreach ($_identity as $tmpkey => $id)
				{
					if ($id->id) {
						$identity[] = $id->id;
					} else {
						$identity[] = $id;
					}
				}
			} else {
				$identity = $_identity;
			} 
	
			parent::deleteAccountData($GLOBALS['phpgw_info']['user']['account_id'], $identity);
		}

		function ssaveSignature($_signatureID, $_description, $_signature, $_isDefaultSignature) 
		{
			return parent::saveSignature($GLOBALS['phpgw_info']['user']['account_id'], $_signatureID, $_description, $_signature, (bool)$_isDefaultSignature);
		}

		function setProfileActive($_status, $_identity=NULL) 
		{
			parent::setProfileActive($GLOBALS['phpgw_info']['user']['account_id'], $_status, $_identity);
		}
	}

