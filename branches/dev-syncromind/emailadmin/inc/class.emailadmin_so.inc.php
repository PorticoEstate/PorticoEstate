<?php
	/***************************************************************************\
	* EGroupWare - EMailAdmin                                                   *
	* http://www.egroupware.org                                                 *
	* Written by : Lars Kneschke [lkneschke@egroupware.org]                     *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/
	/* $Id$ */

	phpgw::import_class('felamimail.bofelamimail');	
	class emailadmin_so
	{
		var $db;
		var $table = 'egw_emailadmin';
		var $db_cols = array(
			'ea_profile_id'			=> 'profileID',
			'ea_smtp_server'		=> 'smtpServer',
			'ea_smtp_type'			=> 'smtpType',
			'ea_smtp_port'			=> 'smtpPort',
			'ea_smtp_auth'			=> 'smtpAuth',
			'ea_editforwardingaddress'	=> 'editforwardingaddress',
			'ea_smtp_ldap_server'		=> 'smtpLDAPServer',
			'ea_smtp_ldap_basedn'		=> 'smtpLDAPBaseDN',
			'ea_smtp_ldap_admindn'		=> 'smtpLDAPAdminDN',
			'ea_smtp_ldap_adminpw'		=> 'smtpLDAPAdminPW',
			'ea_smtp_ldap_use_default'	=> 'smtpLDAPUseDefault',
			'ea_imap_server'		=> 'imapServer',
			'ea_imap_type'			=> 'imapType',
			'ea_imap_port'			=> 'imapPort',
			'ea_imap_login_type'		=> 'imapLoginType',
			'ea_imap_auth_username'			=> 'imapAuthUsername',
			'ea_imap_auth_password'		=> 'imapAuthPassword',
			'ea_imap_tsl_auth'		=> 'imapTLSAuthentication',
			'ea_imap_tsl_encryption'	=> 'imapTLSEncryption',
			'ea_imap_enable_cyrus'		=> 'imapEnableCyrusAdmin',
			'ea_imap_admin_user'		=> 'imapAdminUsername',
			'ea_imap_admin_pw'		=> 'imapAdminPW',
			'ea_imap_enable_sieve'		=> 'imapEnableSieve',
			'ea_imap_sieve_server'		=> 'imapSieveServer',
			'ea_imap_sieve_port'		=> 'imapSievePort',
			'ea_description'		=> 'description',
			'ea_default_domain'		=> 'defaultDomain',
			'ea_organisation_name'		=> 'organisationName',
			'ea_user_defined_identities'  => 'userDefinedIdentities',
			'ea_user_defined_accounts'	=> 'userDefinedAccounts',
			'ea_imapoldcclient'		=> 'imapoldcclient',
			'ea_order'			=> 'ea_order',
			'ea_active'			=> 'ea_active',
			'ea_group'			=> 'ea_group',
			'ea_user'          => 'ea_user',
			'ea_appname'			=> 'ea_appname',
			'ea_smtp_auth_username'		=> 'ea_smtp_auth_username',
			'ea_smtp_auth_password'		=> 'ea_smtp_auth_password',
			'ea_user_defined_signatures'	=> 'ea_user_defined_signatures',
			'ea_default_signature'		=> 'ea_default_signature',
		);

		function __construct()
		{
			if (isset($GLOBALS['phpgw_setup']->db) && is_object($GLOBALS['phpgw_setup']->db))
			{
				$this->db = clone($GLOBALS['phpgw_setup']->db);
			}
			else
			{
//				$this->db = clone($GLOBALS['phpgw']->db);
				$this->db = createobject('felamimail.egw_db');
			}
//			$this->db->set_app('emailadmin');
		}

		/**
		 * Convert array with internal values/names to db-column-names
		 *
		 * @param array $vals
		 * @return array
		 */
		function vals2db($vals)
		{
			$cols = array();
			foreach($vals as $key => $val)
			{
				if (($k = array_search($key,$this->db_cols)) === false) $k = $key;
				
				$cols[$k] = $val;
			}
			return $cols;
		}

		/**
		 * Convert array with db-columns/-values to internal names
		 *
		 * @param array $vals
		 * @return array
		 */
		function db2vals($cols)
		{
			$vals = array();
			foreach($cols as $key => $val)
			{
				if (isset($this->db_cols[$key])) $key = $this->db_cols[$key];
				
				$vals[$key] = $val;
			}
			return $vals;
		}

		function updateProfile($_globalSettings, $_smtpSettings=array(), $_imapSettings=array())
		{
			$profileID = (int) $_globalSettings['profileID'];
			unset($_globalSettings['profileID']);

			$where = $profileID ? array('ea_profile_id' => $profileID) : false;

			$this->db->insert($this->table,$this->vals2db($_smtpSettings+$_globalSettings+$_imapSettings),$where,__LINE__,__FILE__, 'emailadmin');

			return $profileID ? $profileID : $this->db->get_last_insert_id($this->table,'ea_profile_id');
		}

		function addProfile($_globalSettings, $_smtpSettings, $_imapSettings)
		{
			unset($_globalSettings['profileID']);	// just in case

			return $this->updateProfile($_globalSettings, $_smtpSettings, $_imapSettings);
		}

		function deleteProfile($_profileID)
		{
			$this->db->delete($this->table,array('ea_profile_id' => $_profileID),__LINE__ , __FILE__);
		}

		function getProfile($_profileID, $_fieldNames)
		{
			$_fieldNames = array_keys($this->vals2db(array_flip($_fieldNames)));
			$this->db->select($this->table,$_fieldNames,array('ea_profile_id' => $_profileID), __LINE__, __FILE__);
			
			if (($data = $this->db->row(true))) {
				return $this->db2vals($data);
			}
			return $data;
		}
		
		function getUserProfile($_appName, $_groups, $_user = NULL)
		{
			if(empty($_appName) || !is_array($_groups))
				return false;
			if (!empty($_user)) {	
				$where = $this->db->expression(
					$this->table,'(',
					array('ea_appname'=>$_appName),
					' OR ea_appname IS NULL or ea_appname = \'\') and ',
					'(',
					array('ea_group'=>$_groups),
					' OR ea_group IS NULL or ea_group = \'\') and ',
					'(',
					array('ea_user'=>$_user),
					' OR ea_user IS NULL or ea_user = \'0\' or ea_user = \'\')'
				);
			} else {
				$where = $this->db->expression(
					$this->table,'(',
					array('ea_appname'=>$_appName),
					' OR ea_appname IS NULL or ea_appname = \'\') and ',
					'(',
					array('ea_group'=>$_groups),
					' OR ea_group IS NULL or ea_group = \'\')'
				);
			}
			$anyValues = 0;
			// retrieve the Global/Overall Settings
			$this->db->select($this->table,'ea_profile_id',$where, __LINE__, __FILE__, false, 'ORDER BY ea_order', false, 1);
			if (($data = $this->db->row(true))) {
				$globalDefaults = $this->getProfile($data['ea_profile_id'], $this->db_cols);
				$anyValues++;
			}
			// retrieve application settings if set
			if (strlen($_appName)>0) {
				$this->db->select($this->table,'ea_profile_id',$this->db->expression($this->table,'(',array('ea_appname'=>$_appName),' and ea_active=1)'), __LINE__, __FILE__, false, 'ORDER BY ea_order', false, 1);
				if (($data = $this->db->row(true))) {
					$appDefaults = $this->getProfile($data['ea_profile_id'], $this->db_cols);
					$globalDefaults = self::mergeProfileData($globalDefaults, $appDefaults);
					$anyValues++;
				}
			}
			// retrieve primary-group settings if set
			if (isset($GLOBALS['phpgw_info']['user']['account_primary_group']) && is_array($_groups) && $_groups[1] == $GLOBALS['phpgw_info']['user']['account_primary_group']) {
				$this->db->select($this->table,'ea_profile_id',$this->db->expression($this->table,'(',array('ea_group'=>$_groups[1]),' and ea_active=1)'), __LINE__, __FILE__, false, 'ORDER BY ea_order', false, 1);
				if (($data = $this->db->row(true))) {
					$groupDefaults = $this->getProfile($data['ea_profile_id'], $this->db_cols);
					$globalDefaults = self::mergeProfileData($globalDefaults, $groupDefaults);
					$anyValues++;
				}
			}
			// retrieve usersettings if set
			if (!empty($_user) && $_user != 0) {
				$this->db->select($this->table,'ea_profile_id',$this->db->expression($this->table,'(',array('ea_user'=>$_user),' and ea_active=1)'), __LINE__, __FILE__, false, 'ORDER BY ea_order', false, 1);
				if (($data = $this->db->row(true))) {
					$userDefaults = $this->getProfile($data['ea_profile_id'], $this->db_cols);
					$globalDefaults = self::mergeProfileData($globalDefaults, $userDefaults);
					$anyValues++;
				}
			}
			if ($anyValues) {
				return $globalDefaults;
			} else {
				return false;
			}
		}

		/*
		* merge profile data.
		* for each key of the mergeInTo Array check if there is a value set in the toMerge Array and replace it.
		*/
		static function mergeProfileData($mergeInTo, $toMerge)
		{
			if (is_array($toMerge) && count($toMerge)>0) 
			{
				$allkeys = array_unique(array_keys($mergeInTo)+array_keys($toMerge));
				foreach ($allkeys as $i => $key) {
					if (!array_key_exists($key, $mergeInTo) && array_key_exists($key, $toMerge) && !empty($toMerge[$key])) 
					{
						$mergeInTo[$key]=$toMerge[$key];
					} else {
						if (array_key_exists($key, $toMerge) && !empty($toMerge[$key])) 
						{
							switch ($key) {
								case 'ea_imap_server':
								case 'ea_imap_type':
								case 'ea_imap_port':
								case 'ea_imap_tsl_encryption':
								case 'ea_imap_tsl_auth':
									if (strlen($toMerge['ea_imap_server'])>0) $mergeInTo[$key]=$toMerge[$key];
									break;
								case 'ea_smtp_port':
								case 'ea_smtp_type':
								case 'ea_smtp_server':
									if (strlen($toMerge['ea_smtp_server'])>0) $mergeInTo[$key]=$toMerge[$key];
									break;
								case 'ea_default_signature':
									$testVal = $toMerge['ea_default_signature'];
									bofelamimail::getCleanHTML($testVal);
									if (strlen($testVal)>10 || $testVal != '<br>' || $testVal != '<br />') $mergeInTo[$key]=$toMerge[$key];
									break;
								default:
									$mergeInTo[$key]=$toMerge[$key];
							}
						}
					}
				}
			}
			return $mergeInTo;			
		}
		
		function getProfileList($_profileID=0,$_defaultProfile=false)
		{
			$where = false;
			if ((int) $_profileID)
			{
				$where = array('ea_profile_id' => $_profileID);
			}
			elseif ($_defaultProfile)
			{
				$where['ea_appname'] = '';
				$where['ea_group'] = 0;
				$where['ea_user'] = 0;
			}			
			$this->db->select($this->table,'*',$where, __LINE__,__FILE__,false,(int) $_profileID ? '' : 'ORDER BY ea_order');

			$serverList = false;
			while (($row = $this->db->row(true)))
			{
				$serverList[] = $this->db2vals($row);
			}
			return $serverList;
		}

		function getUserData($_accountID)
		{
			$ldap = $GLOBALS['phpgw']->common->ldapConnect();
			
			if (($sri = @ldap_search($ldap,$GLOBALS['phpgw_info']['server']['ldap_context'],"(uidnumber=$_accountID)")))
			{
				$allValues = ldap_get_entries($ldap, $sri);
				if ($allValues['count'] > 0)
				{
					#print "found something<br>";
					$userData["mailLocalAddress"]		= $allValues[0]["mail"][0];
					$userData["mailAlternateAddress"]	= $allValues[0]["mailalternateaddress"];
					$userData["accountStatus"]		= $allValues[0]["accountstatus"][0];
					$userData["mailRoutingAddress"]		= $allValues[0]["mailforwardingaddress"];
					$userData["qmailDotMode"]		= $allValues[0]["qmaildotmode"][0];
					$userData["deliveryProgramPath"]	= $allValues[0]["deliveryprogrampath"][0];
					$userData["deliveryMode"]		= $allValues[0]["deliverymode"][0];

					unset($userData["mailAlternateAddress"]["count"]);
					unset($userData["mailRoutingAddress"]["count"]);					

					return $userData;
				}
			}
			
			// if we did not return before, return false
			return false;
		}
		
		function saveUserData($_accountID, $_accountData)
		{
			$ldap = $GLOBALS['phpgw']->common->ldapConnect();
			// need to be fixed
			if(is_numeric($_accountID))
			{
				$filter = "uidnumber=$_accountID";
			}
			else
			{
				$filter = "uid=$_accountID";
			}

			$sri = @ldap_search($ldap,$GLOBALS['phpgw_info']['server']['ldap_context'],$filter);
			if ($sri)
			{
				$allValues 	= ldap_get_entries($ldap, $sri);
				$accountDN 	= $allValues[0]['dn'];
				$uid	   	= $allValues[0]['uid'][0];
				$homedirectory	= $allValues[0]['homedirectory'][0];
				$objectClasses	= $allValues[0]['objectclass'];
				
				unset($objectClasses['count']);
			}
			else
			{
				return false;
			}
			
			if(empty($homedirectory))
			{
				$homedirectory = "/home/".$uid;
			}
			
			// the old code for qmail ldap
			$newData = array 
			(
				'mail'			=> $_accountData["mailLocalAddress"],
				'mailAlternateAddress'	=> $_accountData["mailAlternateAddress"],
				'mailRoutingAddress'	=> $_accountData["mailRoutingAddress"],
				'homedirectory'		=> $homedirectory,
				'mailMessageStore'	=> $homedirectory."/Maildir/",
				'gidnumber'		=> '1000',
				'qmailDotMode'		=> $_accountData["qmailDotMode"],
				'deliveryProgramPath'	=> $_accountData["deliveryProgramPath"]
			);
			
			if(!in_array('qmailUser',$objectClasses) &&
				!in_array('qmailuser',$objectClasses))
			{
				$objectClasses[]	= 'qmailuser'; 
			}
			
			// the new code for postfix+cyrus+ldap
			$newData = array 
			(
				'mail'			=> $_accountData["mailLocalAddress"],
				'accountStatus'		=> $_accountData["accountStatus"],
				'objectclass'		=> $objectClasses
			);

			if(is_array($_accountData["mailAlternateAddress"]))
			{	
				$newData['mailAlternateAddress'] = $_accountData["mailAlternateAddress"];
			}
			else
			{
				$newData['mailAlternateAddress'] = array();
			}

			if($_accountData["accountStatus"] == 'active')
			{	
				$newData['accountStatus'] = 'active';
			}
			else
			{
				$newData['accountStatus'] = 'disabled';
			}

			if(!empty($_accountData["deliveryMode"]))
			{	
				$newData['deliveryMode'] = $_accountData["deliveryMode"];
			}
			else
			{
				$newData['deliveryMode'] = array();
			}


			if(is_array($_accountData["mailRoutingAddress"]))
			{	
				$newData['mailForwardingAddress'] = $_accountData["mailRoutingAddress"];
			}
			else
			{
				$newData['mailForwardingAddress'] = array();
			}
			
			#print "DN: $accountDN<br>";
			ldap_mod_replace ($ldap, $accountDN, $newData);
			#print ldap_error($ldap);
			
			// also update the account_email field in egw_accounts
			// when using sql account storage
			if($GLOBALS['phpgw_info']['server']['account_repository'] == 'sql')
			{
				$this->db->update('egw_accounts',array(
						'account_email'	=> $_accountData["mailLocalAddress"]
					),
					array(
						'account_id'	=> $_accountID
					),__LINE__,__FILE__
				);
			}
			return true;
		}
		
		function setOrder($_order)
		{
			foreach($_order as $order => $profileID)
			{
				$this->db->update($this->table,array(
					'ea_order'  => $order,
				),array(
					'ea_profile_id' => $profileID,
				),__LINE__, __FILE__);
			}
		}
	}
