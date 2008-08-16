<?php
	/**
	* IPC Test Suite
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2003-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package ipc_test_suite
	* @version $Id$
	*/

	/**
	* IPC test class for the addressbook application
	* @package ipc_test_suite
	*/
	class ipc_test_suite_addressbook extends ipc_test_suite
	{
		/**
		* @var object $ipc addressbook ipc object
		* @access private
		*/
		var $ipc;

		/**
		* @var integer $last_insert_id last inserted id
		* @access private
		*/
		var $last_insert_id;


	  /**
	  * Constructor
		* @param object $$ipcManager ipc manager object
	  */
		function ipc_test_suite_addressbook($params)
		{
			$this->ipc =& $params['ipcManager']->getIPC('addressbook');

			// test the following methods
			// the test variable and test method is defined in the parent class!
			$this->test = array('test_addData',
			                    'test_getData',
			                    'test_getIdList',
			                    'test_replaceData',
			                    'test_getData',
			                    'test_existData',
			                    'test_removeData',
			                    'test_getIdList'
			                   );
		}

	  /**
	  * Test the ipc addData method
	  */
		function test_addData()
		{

			$data_vcard_1 = "BEGIN:VCARD
VERSION:2.1
X-PHPGROUPWARE-FILE-AS:phpGroupWare.org
N:###_LASTNAME;###_FIRSTNAME;###_MIDDLENAME;###_PREFIX;###_SUFIX
FN:###_FIRSTNAME ###_LASTNAME
BDAY:1980-06-19
URL:###_WEBSITE
A.TEL;WORK;VOICE:###_WORK_PHONE
B.TEL;HOME;VOICE:###_HOME_PHONE
TEL;VOICE:###_VOICE_PHONE
A.TEL;WORK;FAX:###_WORK_FAX
B.TEL;HOME;FAX:###_HOME_FAX
TEL;MSG:###_MSG_PHONE
TEL;CELL:###_CELL_PHONE
TEL;PAGER:###_PAGER
TEL;BBS:###_BBS
TEL;MODEM:###_MODEM
TEL;CAR:###_CAR_PHONE
TEL;ISDN:###_ISDN
TEL;VIDEO:###_VIDEO
A.EMAIL;WORK;INTERNET:###_WORK_EMAIL
B.EMAIL;HOME;INTERNET:###_HOME_EMAIL
ORG:###_ORG
A.ADR;TYPE=WORK:;Deutscher Pavilion;Expo Plaza 1;Hannover;Niedersachsen;30539;Germany
B.ADR;TYPE=HOME:;TEST;Musterweg 1;Musterstadt;Niedersachsen;30539;Germany
LABEL;TYPE=WORK;ENCODING=QUOTED-PRINTABLE:=0D=0ADeutscher Pavilion=0D=0AExpo=
 Plaza 1=0D=0AHannover=0D=0ANiedersachsen=0D=0A30539=0D=0AGermany
LABEL;TYPE=HOME;ENCODING=QUOTED-PRINTABLE:=0D=0ATEST=0D=0AMusterweg 1=0D=0AMusterstadt=0D=0ANiedersachsen=0D=0A30539=0D=0AGermany
END:VCARD
";


			$data_vcard_2 = "BEGIN:VCARD
VERSION:3.0
FN:Jörg Muster
N:Wehling;Jörg
TITLE:Berater
ORG:probusiness AG
BDAY;VALUE=DATE:19610120
NOTE:Handy-Nummer an Kunden nur nach Absprache weitergeben!\n\n
CLASS:PUBLIC
ADR;LANGUAGE=DE;TYPE=WORK:;;Expo Plaza 1;Hannover;;30539;Deutschland
ADR;LANGUAGE=DE;TYPE=HOME:;;Höfen 25;Musterstadt;;31191;Deutschland
EMAIL;TYPE=INTERNET,PREF:joerg.muster@muster.de
TEL;TYPE=WORK,PREF:+49 (511) 123 468 7845
TEL;TYPE=HOME:+49 (546) 135465
TEL;TYPE=CELL:+49 (12311233464
TEL;TYPE=WORK,FAX:+49 (511) 1654645
TEL;TYPE=HOME,FAX:+49 (511) 46534384
CATEGORIES:probusiness
REV:20040120T150607Z
END:VCARD
";

			$data_ldap = Array
				(
            "objectclass" => Array
                (
                    "count" => 3,
                    0 => "organizationalPerson",
                    1 => "person",
                    2 => "Top"
                ),
            0 => "objectclass",
            "rdn" => Array
                (
                    "count" => 1,
                    0 => "BMustermann"
                ),
            1 => "rdn",
            "cn" => Array
                (
                    "count" => 1,
                    0 => "Bernd Musterman"
                ),
            2 => "cn",
            "distinguishedname" => Array
                (
                    "count" => 1,
                    0 => "cn=BMustermann,cn=Recipients,ou=ABC,o=Probusiness"
                ),
            3 => "distinguishedname",
            "rfc822mailbox" => Array
                (
                    "count" => 1,
                    0 => "BMustermann@test.de"
                ),
            4 => "rfc822mailbox",
            "mail" => Array
                (
                    "count" => 1,
                    0 => "BMustermann@test.de"
                ),
            5 => "mail",
            "textencodedoraddress" => Array
                (
                    "count" => 1,
                    0 => "c=DE;a= ;p=Probusiness;o=CIS;s=Mustermann;g=Bernd;i=BM;"
                ),
            6 => "textencodedoraddress",
            "othermailbox" => Array
                (
                    "count" => 3,
                    0 => "CCMAIL$Mustermann, Bernd at ABC",
                    1 => "MS$PROBUSINES/ABC/BMUSTERMANN",
                    2 => "smtp$BMustermann@test-ABC.DE"
                ),
            7 => "othermailbox",
            "conferenceinformation" => Array
                (
                    "count" => 1,
                    0 => "/"
                ),
            8 => "conferenceinformation",
            "givenname" => Array
                (
                    "count" => 1,
                    0 => "Bernd"
                ),
            9 => "givenname",
            "initials" => Array
                (
                    "count" => 1,
                    0 => "BM"
                ),
            10 => initials,
            "uid" => Array
                (
                    "count" => 1,
                    0 => "Bernd"
                ),
            11 => "uid",
            "mapi-recipient" => Array
                (
                    "count" => 1,
                    0 => TRUE
                ),
            12 => "mapi-recipient",
            "sn" => Array
                (
                    "count" => 1,
                    0 => "Mustermann"
                ),
            13 => "sn",
            "facsimiletelephonenumber" => Array
                (
                    "count" => 1,
                    0 => "01323 1564 3453"
                ),
            14 => "facsimiletelephonenumber",
            "homephone" => Array
                (
                    "count" => 1,
                    0 => "0165 46464 435 6"
                ),
            15 => "homephone",
            "title" => Array
                (
                    "count" => 1,
                    0 => "Abteilungsleiter ABCDEF"
                ),
            16 => "title",
            "count" => 17,
            "dn" => "cn=BMustermann,cn=Recipients,ou=ABC,o=Probusiness"
			);

			$type_ldap    = 'x-phpgroupware/addressbook-ldap';
			$type_vcard_1 = 'text/x-vcard';
			$type_vcard_2 = 'text/x-vcard';
			
			$testArray = array(
			               'ldap'    => array('data' => $data_ldap,    'type' => $type_ldap),
			               'vcard_1' => array('data' => $data_vcard_1, 'type' => $type_vcard_1),
			               'vcard_2' => array('data' => $data_vcard_2, 'type' => $type_vcard_2)
			             );
			        
			// set the type to test (ldap|vcard_1|vcard_2)
			$test = 'ldap';
			
			$this->last_insert_id = $this->ipc->addData($testArray[$test]['data'], $testArray[$test]['type']);
			return $this->last_insert_id;
		}

		/**
		* Test the ipc getData method
		*/
		function test_getData()
		{
			$id = $this->last_insert_id;
			$type = 'text/x-vcard';
			//$type = 'text/xml';
			$version = '3.0';
			return $this->ipc->getData($id, $type, $version);
		}

		/**
		* Test the ipc getIdList method
		*/
		function test_getIdList()
		{
			return $this->ipc->getIdList(); // get all data id's
			//return $this->ipc->getIdList(mktime(13,00,00,2,25,2004));
		}

		/**
		* Test the ipc replaceData method
		*/
		function test_replaceData()
		{
			$id = $this->last_insert_id;
			$type = 'text/x-vcard';
			$data = "BEGIN:VCARD
VERSION:2.1
X-PHPGROUPWARE-FILE-AS:phpGroupWare.org
N:###_LASTNAME;###_FIRSTNAME;###_MIDDLENAME;###_PREFIX;###_SUFIX
FN:###_FIRSTNAME ###_LASTNAME
BDAY:1980-06-19
URL:###_WEBSITE
A.TEL;WORK;VOICE:###_WORK_PHONE
B.TEL;HOME;VOICE:###_HOME_PHONE
TEL;VOICE:###_VOICE_PHONE
A.TEL;WORK;FAX:###_WORK_FAX
B.TEL;HOME;FAX:###_HOME_FAX
TEL;MSG:###_MSG_PHONE
TEL;CELL:###_CELL_PHONE
TEL;PAGER:###_PAGER
TEL;BBS:###_BBS
TEL;MODEM:###_MODEM
TEL;CAR:###_CAR_PHONE
TEL;ISDN:###_ISDN
TEL;VIDEO:###_VIDEO
A.EMAIL;WORK;INTERNET:###_WORK_EMAIL
B.EMAIL;HOME;INTERNET:###_HOME_EMAIL
ORG:###_ORG
A.ADR;TYPE=WORK:;Deutscher Pavilion;Expo Plaza 1;Hannover;Niedersachsen;30539;Germany
B.ADR;TYPE=HOME:;TEST;Musterweg 2;Hannover;Niedersachsen;30539;Germany
LABEL;TYPE=WORK;ENCODING=QUOTED-PRINTABLE:=0D=0ADeutscher Pavilion=0D=0AExpo=
 Plaza 1=0D=0AHannover=0D=0ANiedersachsen=0D=0A30539=0D=0AGermany
LABEL;TYPE=HOME;ENCODING=QUOTED-PRINTABLE:=0D=0ATEST=0D=0AMusterweg 1=0D=0AHannover=0D=0ANiedersachsen=0D=0A30539=0D=0AGermany
END:VCARD
";
$data = Array
			(
            "objectclass" => Array
                (
                    "count" => 3,
                    0 => "organizationalPerson",
                    1 => "person",
                    2 => "Top"
                ),
            0 => "objectclass",
            "rdn" => Array
                (
                    "count" => 1,
                    0 => "BMustermann2"
                ),
            1 => "rdn",
            "cn" => Array
                (
                    "count" => 1,
                    0 => "Bernd Horst Mustermann"
                ),
            2 => "cn",
            "distinguishedname" => Array
                (
                    "count" => 1,
                    0 => "cn=BMustermann2,cn=Recipients,ou=CIS,o=Probusiness"
                ),
            3 => "distinguishedname",
            "rfc822mailbox" => Array
                (
                    "count" => 1,
                    0 => "BMustermann2_2_@test.de"
                ),
            4 => "rfc822mailbox",
            "mail" => Array
                (
                    "count" => 1,
                    0 => "BMustermann2_2_@test.de"
                ),
            5 => "mail",
            "textencodedoraddress" => Array
                (
                    "count" => 1,
                    0 => "c=DE;a= ;p=Probusiness;o=CIS;s=BMustermann2;g=Andreas;i=AH;"
                ),
            6 => "textencodedoraddress",
            "othermailbox" => Array
                (
                    "count" => 3,
                    0 => "CCMAIL$Mustermann_2, Bernd at CIS",
                    1 => "MS$PROBUSINES/CIS/BMUSTERMANN2",
                    2 => "smtp_2_$BMustermann2@TEST-CIS.DE"
                ),
            7 => "othermailbox",
            "conferenceinformation" => Array
                (
                    "count" => 1,
                    0 => "/"
                ),
            8 => "conferenceinformation",
            "givenname" => Array
                (
                    "count" => 1,
                    0 => "Bernd #2#"
                ),
            9 => "givenname",
            "initials" => Array
                (
                    "count" => 1,
                    0 => "BM #2#"
                ),
            10 => initials,
            "uid" => Array
                (
                    "count" => 1,
                    0 => "Bernd  #2#"
                ),
            11 => "uid",
            "mapi-recipient" => Array
                (
                    "count" => 1,
                    0 => TRUE
                ),
            12 => "mapi-recipient",
            "sn" => Array
                (
                    "count" => 1,
                    0 => "Mustermann2 #2#"
                ),
            13 => "sn",
            "facsimiletelephonenumber" => Array
                (
                    "count" => 1,
                    0 => "0164 464 64 464 #2#"
                ),
            14 => "facsimiletelephonenumber",
            "homephone" => Array
                (
                    "count" => 1,
                    0 => "0154 3434 6 46 4646 #2#"
                ),
            15 => "homephone",
            "mobile" => Array
                (
                    "count" => 1,
                    0 => "01654 434 564 64 #2#"
                ),
            16 => "mobile",
            "title" => Array
                (
                    "count" => 1,
                    0 => "Teamleiter #2#"
                ),
            17 => "title",
            "postalcode" => Array
                (
                    "count" => 1,
                    0 => "01234"
                ),
            18 => "postalcode",
            "postaladdress" => Array
                (
                    "count" => 1,
                    0 => "Expo Plaza 15 #2#"
                ),
            19 => "postaladdress",
            "l" => Array
                (
                    "count" => 1,
                    0 => "Hannover #2#"
                ),
            20 => "l",
            "count" => 21,
            "dn" => "cn=BMustermann2,cn=Recipients,ou=CIS,o=Probusiness"
			);

			$type = 'x-phpgroupware/addressbook-ldap';
			return $this->ipc->replaceData($id, $data, $type);
		}

		/**
		* Test the ipc removeData method
		*/
		function test_removeData()
		{
			$id = $this->last_insert_id;
			return $this->ipc->removeData($id);
		}

		/**
		* Test the ipc existData method
		*/
		function test_existData()
		{
			$id = $this->last_insert_id;
			return $this->ipc->existData($id);
		}
	}
?>