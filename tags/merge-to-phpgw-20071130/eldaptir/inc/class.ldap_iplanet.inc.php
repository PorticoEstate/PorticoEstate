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
  
  /* $Id: class.ldap_iplanet.inc.php 8324 2001-11-19 15:50:05Z milosch $ */

	class ldap_schema extends ldap
	{
		var $db;
		var $table = 'phpgw_eldaptir_schema';

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
			$this->db = $GLOBALS['phpgw']->db;
		}

		function read($server_id,$data='')
		{
			$this->db->query('SELECT * FROM ' . $this->table . ' WHERE id=' . $server_id);
			while ($this->db->next_record())
			{
				if ($this->db->f('oid'))
				{
					$data[$this->db->f('oid')]= array(
						'id'    => trim(stripslashes($this->db->f('id'))),
						'oid'   => trim(stripslashes($this->db->f('_oid'))),
						'name'  => trim(stripslashes($this->db->f('name'))),
						'extra' => trim(stripslashes($this->db->f('extra'))),
						'descr' => trim(stripslashes($this->db->f('descr'))),
						'must'  => trim(stripslashes($this->db->f('must'))),
						'may'   => trim(stripslashes($this->db->f('may')))
					);
				}
			}
			return $data;
		}

		function save($server_id,$data='')
		{
			if (gettype($data) == 'array')
			{
				while (list($key,$val) = each($data))
				{
					$must = implode(',',$val['must']);
					$may  = implode(',',$val['may']);

					$this->db->query('INSERT INTO ' . $this->table . ' (id,_oid,name,extra,descr,must,may) VALUES ('
						. $server_id . ",'" .$val['oid'] . "','" . addslashes($val['name']) ."',"
						. "'" . addslashes($val['extra']) . "','" . addslashes($val['descr']) ."',"
						. "'" . addslashes($must)  . "','" . addslashes($may) . "')"
					);
				}
				return True;
			}
			return False;
		}

		function delete($server_id,$data='')
		{
			$ret = $this->db->query('DELETE FROM ' . $this->table . ' WHERE id=' . $server_id);
			return $ret;
		}	

		function fetch($server_id,$data='')			
		{
			$servers = $GLOBALS['servers'];

			$ldapobj = CreateObject('eldaptir.ldap',$servers[$server_id]);
			$schema = $ldapobj->schema();
			$obj = $schema[0]['objectclasses'];

			while(list($key, $val) = each($obj))
			{
				if ($key == 'count')
				{
					continue;
				}

				$namesplit = split('NAME',$val);
				$oid = ereg_replace('\(','',$namesplit[0]);
				$list[$oid]['oid'] = $oid;
				
				if (strstr($namesplit[1],'DESC'))
				{
					$descsplit = split('DESC',$namesplit[1]);
					$nametmp = ereg_replace("'",'',$descsplit[0]);
				}
				elseif (strstr($namesplit[1],'AUXILIARY'))
				{
					$descsplit = split('AUXILIARY',$namesplit[1]);
					$nametmp = ereg_replace("'",'',$descsplit[0]);
				}
				elseif (strstr($namesplit[1],'STRUCTURAL'))
				{
					$descsplit = split('STRUCTURAL',$namesplit[1]);
					$nametmp = ereg_replace("'",'',$descsplit[0]);
				}
				elseif (strstr($namesplit[1],'ABSTRACT'))
				{
					$descsplit = split('ABSTRACT',$namesplit[1]);
					$nametmp = ereg_replace("'",'',$descsplit[0]);
				}
				$nametmp = ereg_replace('DESC','',$nametmp);
				$nametmp = ereg_replace('AUXILIARY','',$nametmp);
				$nametmp = ereg_replace('STRUCTURAL','',$nametmp);
				$nametmp = ereg_replace("'",'',$nametmp);
				$list[$oid]['name']  = ereg_replace('ABSTRACT','',$nametmp);

				$mustsplit = split('MUST',$descsplit[1]);
				$extratmp = $mustsplit[0];
				$extratmp = ereg_replace('AUXILIARY','',$extratmp);
				$extratmp = ereg_replace('STRUCTURAL','',$extratmp);
				$extratmp = ereg_replace("'",'',$extratmp);
				$list[$oid]['extra']  = ereg_replace('ABSTRACT','',$extratmp);
				
				//$list[$oid]['descr'] = $mustsplit[1];
				$descr = $mustsplit[1];

				$lastsplit = split('MAY',$descr);

				$mustmp = ereg_replace('\(','',$lastsplit[0]);
				$mustmp = ereg_replace('\)','',$mustmp);
				$mustmp = ereg_replace(" ",'',$mustmp);
				$tmp = explode("$",$mustmp);
				$list[$oid]['must'] = $tmp;

				$maytmp = ereg_replace('\(','',$lastsplit[1]);
				$maytmp = ereg_replace('\)','',$maytmp);
				$maytmp = ereg_replace(" ",'',$maytmp);
				$tmp = explode("$",$maytmp);
				$list[$oid]['may'] = $tmp;
			}
			print_r($list);
			return $list;
		}
	}
?>
