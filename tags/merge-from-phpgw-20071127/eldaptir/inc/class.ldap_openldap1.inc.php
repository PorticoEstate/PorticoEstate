<?php
  /**************************************************************************\
  * phpGroupWare - eLDAPtir - LDAP Administration                            *
  * http://www.phpgroupware.org                                              *
  * Sections of code were taken from PHP TreeMenu 1.1                        *
  *  by Bjorge Dijkstra - bjorge@gmx.net                                     *
  * ------------------------------------------------------------------------ *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  
  /* $Id: class.ldap_openldap1.inc.php 6387 2001-06-30 06:07:04Z milosch $ */

	/*
		OpenLDAP 1.x has no schema information available via search, so the functions
		below are empty.
	*/
	class ldap_schema extends ldap
	{
		var $orgunits = array(
			'Aliases'   => array('aliasobjects','cn'),
			'Contacts'  => array('contactobjects','uid'),
			'Group'     => array('groupobjects','cn'),
			'Hosts'     => array('hostobjects','cn'),
			'Mounts'    => array('mountobjects','cn'),
			'Netgroup'  => array('nisobjects',''),
			'Networks'  => array('networkobjects','cn'),
			'People'    => array('personobjects','uid'),
			'Protocols' => array('protocolobjects','cn'),
			'Roaming'   => array('roamingobjects','nsLIProfile'),
			'Rpc'       => array('rpcobjects','cn'),
			'Services'  => array('serviceobjects','cn')
		);

		/* NIS STUFF HERE IS VERY BROKEN */
		var $nismapnames = array(
			'netgroup_byhost',array('',''),
			'netgroup_byuser',array('','')
		);

		/*
			objectclasses typical for each type of object
		*/
		var $personobjects   = array('top','person','organizationalPerson','inetOrgPerson','posixAccount','shadowAccount');
		var $contactobjects  = array('top','person','organizationalPerson','inetOrgPerson','posixAccount','residentialPerson');
		var $groupobjects    = array('top','posixGroup');
		var $aliasobjects    = array('top','nisMailAlias');
		var $hostobjects     = array('top','ipHost','device');
		var $networkobjects  = array('top','ipProtocol','ipService');
		var $protocolobjects = array('top','ipProtocol');
		var $serviceobjects  = array('top','ipService');
		var $mountobjects    = array('top','mount');
		var $nisobjects      = array('top','nisMap');
		var $rpcobjects      = array('top','oncRpc');
		var $roamingobjects  = array('top','nsLIProfile');

		/*
			list of all objectclasses
		*/
		var $objectclasses = array(
			'top',
			'account',
			'posixaccount',
			'shadowaccount',
			'posixGroup',
			'ipservice',
			'ipprotocol',
			'iphost',
			'ipnetwork',
			'mount',
			'inetorgperson',
			'organization',
			'organizationalunit',
			'organizationalperson',
			'organizationalrole',
			'groupofnames',
			'residentialperson',
			'device',
			'oncrpc',
			'nsliprofile',
			'nsliprofileelement'
		);

		/*
			attributes for each objectclass
			if True, it is a required attribute
			if False, it is an allowed attribute
		*/
		var $top = array();

		var $account = array(
			'userid'                 => True,
			'description'            => False,
			'seeAlso'                => False,
			'localityName'           => False,
			'organizationName'       => False,
			'organizationalUnitName' => False,
			'host'                   => False
		);

		var $posixaccount = array(
			'cn'            => True,
			'uid'           => True,
			'uidNumber'     => True,
			'gidNumber'     => True,
			'homeDirectory' => True,
			'userPassword'  => False,
			'loginShell'    => False,
			'gecos'         => False,
			'description'   => False
		);

		var $shadowaccount = array(
			'uid'              => True,
			'userPassword'     => False,
			'shadowLastChange' => False,
			'shadowMin'        => False,
			'shadowMax'        => False,
			'shadowWarning'    => False,
			'shadowInactive'   => False,
			'shadowExpire'     => False,
			'shadowFlag'       => False,
			'description'      => False
		);

		var $posixgroup= array(
			'cn'            => True,
			'gidNumber'     => True,
			'userPassword'  => False,
			'memberUid'     => False,
			'description'   => False
		);
/*
		var $nismailalias = array(
			'rfc822MailMember' => True
		);
*/
		var $ipservice = array(
			'cn'            => True,
			'ipServicePort' => True,
			'description'   => False
		);

		var $ipprotocol = array(
			'cn'               => True,
			'ipProtocolNumber' => True,
			'description'      => True
		);

		var $iphost = array(
			'cn'           => True,
			'ipHostNumber' => True,
			'l'            => False,
			'description'  => False,
			'manager'      => False
		);

		var $ipnetwork = array(
			'cn'              => True,
			'ipNetworkNumber' => True,
			'ipNetmaskNumber' => False,
			'l'               => False,
			'description'     => False,
			'manager'         => False
		);

		var $organization = array(
			'o'                          => True,
			'userPassword'               => False,
			'searchGuide'                => False,
			'seeAlso'                    => False,
			'businessCategory'           => False,
			'x121Address'                => False,
			'registeredAddress'          => False,
			'destinationIndicator'       => False,
			'preferredDeliveryMethod'    => False,
			'telexNumber'                => False,
			'teletexTerminalIdentifier'  => False,
			'telephoneNumber'            => False,
			'internationaliSDNNumber'    => False,
			'facsimileTelephoneNumber'   => False,
			'street'                     => False,
			'postOfficeBox'              => False,
			'postalCode'                 => False,
			'postalAddress'              => False,
			'physicalDeliveryOfficeName' => False,
			'st'                         => False,
			'l'                          => False,
			'description'                => False
		);

		var $organizationalunit = array(
			'ou'                         => True,
			'userPassword'               => False,
			'searchGuide'                => False,
			'seeAlso'                    => False,
			'businessCategory'           => False,
			'x121Address'                => False,
			'registeredAddress'          => False,
			'destinationIndicator'       => False,
			'preferredDeliveryMethod'    => False,
			'telexNumber'                => False,
			'teletexTerminalIdentifier'  => False,
			'telephoneNumber'            => False,
			'internationaliSDNNumber'    => False,
			'facsimileTelephoneNumber'   => False,
			'street'                     => False,
			'postOfficeBox'              => False,
			'postalCode'                 => False,
			'postalAddress'              => False,
			'physicalDeliveryOfficeName' => False,
			'st'                         => False,
			'l'                          => False,
			'description'                => False
		);

		var $person = array(
			'sn'              => True,
			'cn'              => True,
			'userPassword'    => False,
			'telephoneNumber' => False,
			'seeAlso'         => False,
			'description'     => False
		);

		var $organizationalperson = array(
			'title'                      => False,
			'x121Address'                => False,
			'registeredAddress'          => False,
			'destinationIndicator'       => False,
			'preferredDeliveryMethod'    => False,
			'telexNumber'                => False,
			'teletexTerminalIdentifier'  => False,
			'telephoneNumber'            => False,
			'internationaliSDNNumber'    => False,
			'facsimileTelephoneNumber'   => False,
			'street'                     => False,
			'postOfficeBox'              => False,
			'postalCode'                 => False,
			'postalAddress'              => False,
			'physicalDeliveryOfficeName' => False,
			'ou'                         => False,
			'st'                         => False,
			'l'                          => False
		);

		var $inetorgperson = array(
			'audio'                => False,
			'businessCategory'     => False,
			'carLicense'           => False,
			'departmentNumber'     => False,
			'displayName'          => False,
			'employeeNumber'       => False,
			'employeeType'         => False,
			'givenName'            => False,
			'homePhone'            => False,
			'homePostalAddress'    => False,
			'initials'             => False,
			'jpegPhoto'            => False,
			'labeledURI'           => False,
			'mail'                 => False,
			'manager'              => False,
			'mobile'               => False,
			'o'                    => False,
			'pager'                => False,
			'photo'                => False,
			'roomNumber'           => False,
			'secretary'            => False,
			'uid'                  => False,
			'userCertificate'      => False,
			'x500uniqueIdentifier' => False,
			'preferredLanguage'    => False,
			'userSMIMECertificate' => False,
			'userPKCS12'           => False
		);

		var $organizationalrole = array(
			'cn'                         => True,
			'x121Address'                => False,
			'registeredAddress'          => False,
			'destinationIndicator'       => False,
			'preferredDeliveryMethod'    => False,
			'telexNumber'                => False,
			'teletexTerminalIdentifier'  => False,
			'telephoneNumber'            => False,
			'internationaliSDNNumber'    => False,
			'facsimileTelephoneNumber'   => False,
			'seeAlso'                    => False,
			'roleOccupant'               => False,
			'preferredDeliveryMethod'    => False,
			'street'                     => False,
			'postOfficeBox'              => False,
			'postalCode'                 => False,
			'postalAddress'              => False,
			'physicalDeliveryOfficeName' => False,
			'ou'                         => False,
			'st'                         => False,
			'l'                          => False,
			'description'                => False
		);

		var $groupofnames = array(
			'member'           => True,
			'cn'               => True,
			'businessCategory' => False,
			'seeAlso'          => False,
			'owner'            => False,
			'ou'               => False,
			'o'                => False,
			'description'      => False
		);

		var $residentialperson = array(
			'l'                         => True,
			'businessCategory'          => False,
			'x121Address'               => False,
			'registeredAddress'         => False,
			'destinationIndicator'      => False,
			'preferredDeliveryMethod'   => False,
			'telexNumber'               => False,
			'teletexTerminalIdentifier' => False,
			'telephoneNumber'           => False,
			'internationaliSDNNumber'   => False,
			'facsimileTelephoneNumber'  => False,
			'preferredDeliveryMethod'   => False,
			'street'                    => False,
			'postOfficeBox'             => False,
			'postalCode'                => False,
			'postalAddress'             => False
		);

		var $mount = array(
			'mountDirectory'     => True,
			'mountType'          => True,
			'mountDumpFrequency' => False,
			'mountPassNumber'    => False,
			'mountOption'        => False
		);

		var $device = array(
			'cn'           => True,
			'serialNumber' => False,
			'seeAlso'      => False,
			'owner'        => False,
			'ou'           => False,
			'o'            => False,
			'l'            => False,
			'description'  => False
		);

		var $oncrpc = array(
			'cn'           => True,
			'oncRpcNumber' => True,
			'description'  => False
		);

		var $netgroup_byhost = array(
			'cn'                => True,
			'nisNetgroupTriple' => False,
			'memberNisNetgroup' => False,
			'description'       => False
		);

		var $netgroup_byuser = array(
			'cn'                => True,
			'nisNetgroupTriple' => False,
			'memberNisNetgroup' => False,
			'description'       => False
		);

		var $nsliprofile = array(
			'nsLIProfileName' => True,
			'nsLIPrefs'       => False,
			'uid'             => False,
			'owner'           => False
		);

		var $nsliprofileelement = array(
			'objectClass'     => True,
			'nsLIElementType' => True,
			'owner'           => False,
//			'nsLIData'        => False, // This is huge, and really doesn't need to be seen?
			'nsLIVersion'     => False
		);

		function ldap_schema()
		{
		}

		function read($server_id,$data='')
		{
			return True;
		}

		function save($server_id,$data='')
		{
			return True;
		}

		function delete($server_id,$data='')
		{
			return True;
		}	

		function fetch($server_id,$data='')			
		{
			return True;
		}
	}
?>
