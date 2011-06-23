<?php
	/***************************************************************************\
	* EGroupWare - EMailAdmin                                                   *
	* http://www.egroupware.org                                                 *
	* Written by : Lars Kneschke [lkneschke@linux-at-work.de]                   *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/
	/* $Id$ */

	class defaultsmtp
	{
		var $smtpAuth = false;
		
		var $editForwardingAddress = false;		

		var $host;
		
		var $port;
		
		var $username;
		
		var $password;
		
		var $defaultDomain;
		
		// the constructor
		function defaultsmtp($defaultDomain=null)
		{
			$this->defaultDomain = $defaultDomain ? $defaultDomain : $GLOBALS['phpgw_info']['server']['mail_suffix'];
		}
		
		// add a account
		function addAccount($_hookValues)
		{
			return true;
		}
		
		// delete a account
		function deleteAccount($_hookValues)
		{
			return true;
		}
		
		function getAccountEmailAddress($_accountName)
		{
			$accountID = $GLOBALS['phpgw']->accounts->name2id($_accountName);
			$emailAddress = $GLOBALS['phpgw']->accounts->id2name($accountID,'account_email');
			if(empty($emailAddress))
				$emailAddress = $_accountName.'@'.$this->defaultDomain;

			$realName = trim($GLOBALS['phpgw_info']['user']['firstname'] . (!empty($GLOBALS['phpgw_info']['user']['firstname']) ? ' ' : '') . $GLOBALS['phpgw_info']['user']['lastname']);

			return array(
				array(
					'name'		=> $realName, 
					'address'	=> $emailAddress, 
					'type'		=> 'default'
				)
			);
		}

		function getUserData($_uidnumber) {
			$userData = array();
			
			return $userData;
		}

		function saveSMTPForwarding($_accountID, $_forwardingAddress, $_keepLocalCopy) {
			return true;
		}

		function setUserData($_uidnumber, $_mailAlternateAddress, $_mailForwardingAddress, $_deliveryMode) {
			return true;
		}
		
		// update a account
		function updateAccount($_hookValues) {
			return true;
		}
	}
?>
