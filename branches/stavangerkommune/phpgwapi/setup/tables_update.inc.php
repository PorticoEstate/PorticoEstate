<?php
	/**
	* Setup
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage setup
	* @version $Id$
	* @internal $Source$
	*/

	// Must be running 0.9.14.007 or later in order to upgrade to 0.9.18
	$test[] = '0.9.14.007';
	function phpgwapi_upgrade0_9_14_007()
	{
		// 0.9.14.5xx are the development-versions of the 0.9.16 release (based on the 0.9.14 api)
		// as 0.9.15.xxx are already used in HEAD
		
		// this is the 0.9.15.003 update, needed for the new filemanager and vfs-classes in the api
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_vfs','content', array ('type' => 'text', 'nullable' => True));

		// this is the 0.9.15.004 update, needed for the polish translations
		$GLOBALS['phpgw_setup']->db->query("UPDATE languages set available='Yes' WHERE lang_id='pl'");

		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.500';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}

	$test[] = '0.9.14.500';
	function phpgwapi_upgrade0_9_14_500()
	{
		// this is the 0.9.15.001 update
		$GLOBALS['phpgw_setup']->oProc->RenameTable('lang','phpgw_lang');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('languages','phpgw_languages');

		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.501';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}

	$test[] = '0.9.14.501';
	function phpgwapi_upgrade0_9_14_501()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_async',array(
			'fd' => array(
				'id' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'next' => array('type' => 'int','precision' => '4','nullable' => False),
				'times' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'method' => array('type' => 'varchar','precision' => '80','nullable' => False),
				'data' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_applications',array(
			'fd' => array(
				'app_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'app_name' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'app_enabled' => array('type' => 'int','precision' => '4'),
				'app_order' => array('type' => 'int','precision' => '4'),
				'app_tables' => array('type' => 'text'),
				'app_version' => array('type' => 'varchar','precision' => '20','nullable' => False,'default' => '0.0')
			),
			'pk' => array('app_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('app_name')
		),'app_title');
		
		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.502';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}


	$test[] = '0.9.14.502';
	function phpgwapi_upgrade0_9_14_502()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameTable('phpgw_preferences','old_preferences');

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_preferences',array(
			'fd' => array(
				'preference_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'preference_app' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'preference_value' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('preference_owner','preference_app'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));
		$db2 =& $GLOBALS['phpgw_setup']->db;	// we need a 2. result-set
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM old_preferences");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$owner = intval($GLOBALS['phpgw_setup']->oProc->f('preference_owner'));
			$prefs = unserialize($GLOBALS['phpgw_setup']->oProc->f('preference_value'));
			
			if (is_array($prefs))
			{
				foreach ($prefs as $app => $pref)
				{
					if (!empty($app) && count($pref))
					{
						$app = $db2->db_addslashes($app);
						$pref = serialize($pref);
						$db2->query('INSERT INTO phpgw_preferences '
						. '(preference_owner,preference_app,preference_value) '
						. "VALUES ($owner,'$app','$pref')");
					}
				}
			}
		}
		$GLOBALS['phpgw_setup']->oProc->DropTable('old_preferences');

		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.503';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}

	$test[] = '0.9.14.503';
	function phpgwapi_upgrade0_9_14_503()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_addressbook','last_mod',array(
				'type' => 'int',
				'precision' => '4',
				'default' => '0',
				'nullable' => False
			));

		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.504';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}

	$test[] = '0.9.14.504';
	function phpgwapi_upgrade0_9_14_504()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_categories','last_mod',array(
				'type' => 'int',
				'precision' => '4',
				'default' => '0',
				'nullable' => False
			));

		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.505';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}


	$test[] = '0.9.14.505';
	function phpgwapi_upgrade0_9_14_505()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_access_log','lo',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True,
			'default' => '0'
		));


		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.506';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}


	$test[] = '0.9.14.506';
	function phpgwapi_upgrade0_9_14_506()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_vfs','content',array(
			'type' => 'text',
			'nullable' => True
		));


		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.507';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}


	$test[] = '0.9.14.507';
	function phpgwapi_upgrade0_9_14_507()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_async','account_id',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => False,
			'default' => '0'
		));


		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.508';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}
/*
	$test[] = '0.9.17.001';
	function phpgwapi_upgrade0_9_17_001()
	{
		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.508';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}
*/

	$test[] = '0.9.14.508';
	function phpgwapi_upgrade0_9_14_508()
	{	
		//global $setup_info,$phpgw_setup;
		//$db1 =& $GLOBALS['phpgw_setup']->db; $db1->auto_stripslashes = False; $db1->Halt_On_Error = 'yes';

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_accounts','person_id',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True
			));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_contact',array(
			'fd' => array(
				'contact_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
				'cat_id' => array('type' => 'varchar','precision' => '200','nullable' => True),
				'contact_type_id' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('contact_id'),
			'fk' => array(),
			'ix' => array('owner', 'access', 'contact_type_id', array('contact_id', 'cat_id', 'contact_type_id')),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_contact_person',array(
			'fd' => array(
				'person_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'first_name' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'last_name' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'middle_name' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'prefix' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'suffix' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'birthday' => array('type' => 'varchar','precision' => '32','nullable' => True),
				'pubkey' => array('type' => 'text','nullable' => True),
				'title' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'department' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'initials' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'sound' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'active' => array('type' => 'char','precision' => '1','nullable' => True,'default' => 'Y'),
				'created_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'created_by' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_by' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array('person_id'),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_contact_org',array(
			'fd' => array(
				'org_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '80','nullable' => False),
				'active' => array('type' => 'char','precision' => '1','nullable' => False,'default' => 'Y'),
				'parent' => array('type' => 'int','precision' => '4','nullable' => True),
				'created_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'created_by' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_by' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array('org_id', 'active'),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_contact_org_person',array(
			'fd' => array(
				'org_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'person_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'addr_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'preferred' => array('type' => 'char','precision' => '1','nullable' => False,'default' => 'N'),
				'created_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'created_by' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('org_id','person_id'),
			'fk' => array(),
			'ix' => array('addr_id', 'person_id', 'org_id', 'preferred', array('person_id', 'org_id')),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_contact_addr',array(
			'fd' => array(
				'contact_addr_id' => array('type' => 'auto','nullable' => False),
				'contact_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'addr_type_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'add1' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'add2' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'add3' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'city' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'state' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'postal_code' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'country' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'tz' => array('type' => 'varchar','precision' => '40','nullable' => True),
				'preferred' => array('type' => 'char','precision' => '1','nullable' => False,'default' => 'N'),
				'created_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'created_by' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_by' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('contact_addr_id'),
			'fk' => array(),
			'ix' => array('contact_id', 'addr_type_id', 'preferred'),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_contact_note',array(
			'fd' => array(
				'contact_note_id' => array('type' => 'auto','nullable' => False),
				'contact_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'note_type_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'note_text' => array('type' => 'text','nullable' => False),
				'created_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'created_by' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_by' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('contact_note_id'),
			'fk' => array(),
			'ix' => array('contact_id', 'note_type_id'),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_contact_others',array(
			'fd' => array(
				'other_id' => array('type' => 'auto','nullable' => False),
				'contact_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'contact_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'other_name' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'other_value' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('other_id'),
			'fk' => array(),
			'ix' => array('contact_id','contact_owner','other_name'),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_contact_comm',array(
			'fd' => array(
				'comm_id' => array('type' => 'auto','nullable' => False),
				'contact_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'comm_descr_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'preferred' => array('type' => 'char','precision' => '1','nullable' => False,'default' => 'N'),
				'comm_data' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'created_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'created_by' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_on' => array('type' => 'int','precision' => '4','nullable' => False),
				'modified_by' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('comm_id'),
			'fk' => array(),
			'ix' => array('comm_data', 'preferred', 'comm_descr_id', 'contact_id', array('comm_id', 'contact_id', 'comm_descr_id')),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_contact_comm_descr',array(
			'fd' => array(
				'comm_descr_id' => array('type' => 'auto','nullable' => False),
				'comm_type_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '50','nullable' => True)
			),
			'pk' => array('comm_descr_id'),
			'fk' => array(),
			'ix' => array('descr','comm_type_id'),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_contact_comm_type',array(
			'fd' => array(
				'comm_type_id' => array('type' => 'auto','nullable' => False),
				'type' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'active' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'class' => array('type' => 'varchar','precision' => '30','nullable' => True)
			),
			'pk' => array('comm_type_id'),
			'fk' => array(),
			'ix' => array('type', 'active', 'class'),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_contact_types',array(
			'fd' => array(
				'contact_type_id' => array('type' => 'auto','nullable' => False),
				'contact_type_descr' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'contact_type_table' => array('type' => 'varchar','precision' => '50','nullable' => True)
			),
			'pk' => array('contact_type_id'),
			'fk' => array(),
			'ix' => array('contact_type_descr'),
			'uc' => array()
		));


		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_contact_addr_type',array(
			'fd' => array(
				'addr_type_id' => array('type' => 'auto','nullable' => False),
				'description' => array('type' => 'varchar','precision' => '50','nullable' => False)
			),
			'pk' => array('addr_type_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_contact_note_type',array(
			'fd' => array(
				'note_type_id' => array('type' => 'auto','nullable' => False),
				'description' => array('type' => 'varchar','precision' => '30','nullable' => False)
			),
			'pk' => array('note_type_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_contact_types (contact_type_descr,contact_type_table) VALUES ('Persons','phpgw_contact_person')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_contact_types (contact_type_descr,contact_type_table) VALUES ('Organizations','phpgw_contact_org')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_contact_comm_type (type) VALUES ('email')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_contact_comm_type (type) VALUES ('phone')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_contact_comm_type (type) VALUES ('mobile phone')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_contact_comm_type (type) VALUES ('fax')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_contact_comm_type (type) VALUES ('instant messaging')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_contact_comm_type (type) VALUES ('url')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_contact_comm_type (type) VALUES ('other')");

		$GLOBALS['phpgw_setup']->oProc->query("SELECT comm_type_id FROM phpgw_contact_comm_type WHERE type='email'"); 
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$email_type_id = intval($GLOBALS['phpgw_setup']->oProc->f('comm_type_id'));

		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $email_type_id . ",'home email'" . ')');
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $email_type_id . ",'work email'" . ')');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT comm_type_id FROM phpgw_contact_comm_type WHERE type='phone'"); 
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$phone_type_id = intval($GLOBALS['phpgw_setup']->oProc->f('comm_type_id'));

        $GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $phone_type_id . ",'home phone'" . ')');
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $phone_type_id . ",'work phone'" . ')');
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $phone_type_id . ",'voice phone'" . ')'); 
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $phone_type_id . ",'msg phone'" . ')'); 
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $phone_type_id . ",'pager'" . ')'); 
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $phone_type_id . ",'bbs'" . ')'); 
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $phone_type_id . ",'modem'" . ')'); 
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $phone_type_id . ",'isdn'" . ')'); 
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $phone_type_id . ",'video'" . ')'); 

		$GLOBALS['phpgw_setup']->oProc->query("SELECT comm_type_id FROM phpgw_contact_comm_type WHERE type='fax'"); 
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$fax_type_id = intval($GLOBALS['phpgw_setup']->oProc->f('comm_type_id'));

		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $fax_type_id . ",'home fax'" . ')');
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $fax_type_id . ",'work fax'" . ')');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT comm_type_id FROM phpgw_contact_comm_type WHERE type='mobile phone'"); 
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$mobile_type_id = intval($GLOBALS['phpgw_setup']->oProc->f('comm_type_id'));

		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $mobile_type_id . ",'mobile (cell) phone'" . ')');
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $mobile_type_id . ",'car phone'" . ')');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT comm_type_id FROM phpgw_contact_comm_type WHERE type='instant messaging'"); 
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$instant_type_id = intval($GLOBALS['phpgw_setup']->oProc->f('comm_type_id'));

		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $instant_type_id . ",'msn'" . ')');
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $instant_type_id . ",'aim'" . ')');
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $instant_type_id . ",'yahoo'" . ')');
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $instant_type_id . ",'icq'" . ')');
		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $instant_type_id . ",'jabber'" . ')');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT comm_type_id FROM phpgw_contact_comm_type WHERE type='url'"); 
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$url_type_id = intval($GLOBALS['phpgw_setup']->oProc->f('comm_type_id'));

		$GLOBALS['phpgw_setup']->db->query('INSERT INTO phpgw_contact_comm_descr (comm_type_id,descr) VALUES (' . $url_type_id . ",'website'" . ')');

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_contact_addr_type (description) VALUES('work')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_contact_addr_type (description) VALUES('home')");

		// add three columns to ease migration .. delete later
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_contact','ab_id',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_contact_person','ab_id',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_contact_org','ab_id',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True
		));

		$GLOBALS['phpgw_setup']->oProc->query("SELECT contact_type_id FROM phpgw_contact_types WHERE contact_type_table='phpgw_contact_person'");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$contact_type_person = $GLOBALS['phpgw_setup']->oProc->f('contact_type_id');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT contact_type_id FROM phpgw_contact_types WHERE contact_type_table='phpgw_contact_org'");
                $GLOBALS['phpgw_setup']->oProc->next_record();
		$contact_type_org = $GLOBALS['phpgw_setup']->oProc->f('contact_type_id');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT at.addr_type_id as type_id FROM phpgw_contact_addr_type as at WHERE at.description ='work'");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$addr_work_type = $GLOBALS['phpgw_setup']->oProc->f('type_id');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT at.addr_type_id as type_id FROM phpgw_contact_addr_type as at WHERE at.description ='home'");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$addr_home_type = $GLOBALS['phpgw_setup']->oProc->f('type_id');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT comm_descr_id, descr FROM phpgw_contact_comm_descr");
                while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$comm_descr_id[$GLOBALS['phpgw_setup']->oProc->f('descr')] = $GLOBALS['phpgw_setup']->oProc->f('comm_descr_id');
		}

		// IMPORTANT: Probably you ask why i use an array instand of only use $GLOBALS['phpgw_setup']->oProc well, for some reason for upgrades of many 
		// records (more that 18000 in my case) this process is halted, then i have to do of this way.
		
		// IMPORTANT: This problem is with max_execution_time in php.ini and time_out in http.conf, i set this to 3000 and all works fine, but
		// i not change this process because i think this way is beter.

		$GLOBALS['phpgw_setup']->oProc->query("SELECT contact_id, contact_owner, contact_name, contact_value from phpgw_addressbook_extra where contact_name!='' and contact_value!='' and contact_name!='owner' and contact_name!='access' and contact_name!='cat_id'");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$others[$GLOBALS['phpgw_setup']->oProc->f('contact_id')][$GLOBALS['phpgw_setup']->oProc->f('contact_name')] = array(
				'value' => $GLOBALS['phpgw_setup']->oProc->f('contact_value'),
				'owner' => $GLOBALS['phpgw_setup']->oProc->f('contact_owner'));
		}
		
		// migrate existing data to phpgw_contact_person
		$GLOBALS['phpgw_setup']->oProc->query("SELECT id, owner, access, cat_id, n_given, n_family, n_middle, n_prefix, n_suffix, bday, pubkey, title, org_unit, sound, org_name, note, tz, adr_one_street, adr_one_locality, adr_one_region, adr_one_postalcode, adr_one_countryname, adr_two_street, adr_two_locality, adr_two_region, adr_two_postalcode, adr_two_countryname, url, tel_work, tel_home, tel_voice, tel_fax, tel_msg, tel_cell, tel_pager, tel_bbs, tel_modem, tel_car, tel_isdn, tel_video, email, email_home FROM phpgw_addressbook");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$records[$GLOBALS['phpgw_setup']->oProc->f('id')] = array('owner' => $GLOBALS['phpgw_setup']->oProc->f('owner'),
								       'access' => $GLOBALS['phpgw_setup']->oProc->f('access'),
								       'cat_id' => $GLOBALS['phpgw_setup']->oProc->f('cat_id'),
								       'n_given' => $GLOBALS['phpgw_setup']->oProc->f('n_given'),
								       'n_family' => $GLOBALS['phpgw_setup']->oProc->f('n_family'),
								       'n_middle' => $GLOBALS['phpgw_setup']->oProc->f('n_middle'),
								       'n_prefix' => $GLOBALS['phpgw_setup']->oProc->f('n_prefix'),
								       'n_suffix' => $GLOBALS['phpgw_setup']->oProc->f('n_suffix'),
								       'bday' => $GLOBALS['phpgw_setup']->oProc->f('bday'),
								       'pubkey' => $GLOBALS['phpgw_setup']->oProc->f('pubkey'),
								       'title' => $GLOBALS['phpgw_setup']->oProc->f('title'),
								       'org_unit' => $GLOBALS['phpgw_setup']->oProc->f('org_unit'),
								       'sound' => $GLOBALS['phpgw_setup']->oProc->f('sound'),
								       'org_name' => $GLOBALS['phpgw_setup']->oProc->f('org_name'),

								       'note' => $GLOBALS['phpgw_setup']->oProc->f('note'),
								       'tz' => $GLOBALS['phpgw_setup']->oProc->f('tz'),

								       'adr_one_street' => $GLOBALS['phpgw_setup']->oProc->f('adr_one_street'),
								       'adr_one_locality' => $GLOBALS['phpgw_setup']->oProc->f('adr_one_locality'),
								       'adr_one_region' => $GLOBALS['phpgw_setup']->oProc->f('adr_one_region'),
								       'adr_one_postalcode' => $GLOBALS['phpgw_setup']->oProc->f('adr_one_postalcode'),
								       'adr_one_countryname' => $GLOBALS['phpgw_setup']->oProc->f('adr_one_countryname'),
								       'adr_two_street' => $GLOBALS['phpgw_setup']->oProc->f('adr_two_street'),
								       'adr_two_locality' => $GLOBALS['phpgw_setup']->oProc->f('adr_two_locality'),
								       'adr_two_region' => $GLOBALS['phpgw_setup']->oProc->f('adr_two_region'),
								       'adr_two_postalcode' => $GLOBALS['phpgw_setup']->oProc->f('adr_two_postalcode'),
								       'adr_two_countryname' => $GLOBALS['phpgw_setup']->oProc->f('adr_two_countryname'),

								       'url' => $GLOBALS['phpgw_setup']->oProc->f('url'),
								       'tel_work' => $GLOBALS['phpgw_setup']->oProc->f('tel_work'),
								       'tel_home' => $GLOBALS['phpgw_setup']->oProc->f('tel_home'),
								       'tel_voice' => $GLOBALS['phpgw_setup']->oProc->f('tel_voice'),
								       'tel_fax' => $GLOBALS['phpgw_setup']->oProc->f('tel_fax'),
								       'tel_msg' => $GLOBALS['phpgw_setup']->oProc->f('tel_msg'),
								       'tel_cell' => $GLOBALS['phpgw_setup']->oProc->f('tel_cell'),
								       'tel_pager' => $GLOBALS['phpgw_setup']->oProc->f('tel_pager'),
								       'tel_bbs' => $GLOBALS['phpgw_setup']->oProc->f('tel_bbs'),
								       'tel_modem' => $GLOBALS['phpgw_setup']->oProc->f('tel_modem'),
								       'tel_car' => $GLOBALS['phpgw_setup']->oProc->f('tel_car'),
								       'tel_isdn' => $GLOBALS['phpgw_setup']->oProc->f('tel_isdn'),
								       'tel_video' => $GLOBALS['phpgw_setup']->oProc->f('tel_video'),
								       'email' => $GLOBALS['phpgw_setup']->oProc->f('email'),
								       'email_home' => $GLOBALS['phpgw_setup']->oProc->f('email_home'));
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT max(contact_id) as contact_id FROM phpgw_contact");
		if($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$contact_id = $GLOBALS['phpgw_setup']->oProc->f('contact_id');
		}
		else
		{
			$contact_id=0;
		}

		if(is_array($records))
		{
			foreach($records as $key => $data)
			{
				$time = time();
				$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact (owner,access,cat_id,contact_type_id,ab_id) VALUES ("
					    . $data['owner']
					    . "," . ($data['access']?"'".$data['access']."'":"null") 
					    . "," . ($data['cat_id']?"'".$data['cat_id']."'":"null")
					    . "," . $contact_type_person
					    . "," . $key 
					    . ')');
				
				$contact_id++;
				$person_id = $contact_id;
				
				$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_person (person_id,first_name,last_name,
				middle_name,prefix,suffix,birthday,pubkey,title,department,
				sound,created_by,modified_by,created_on,modified_on,ab_id) VALUES ("
					    . $person_id
					    . ",'" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['n_given']) . "'"
					    . ",'" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['n_family']) . "'"
					    . ",'" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['n_middle']) . "'"
					    . ",'" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['n_prefix']) . "'"
					    . ",'" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['n_suffix']) . "'"
					    . ",'" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['bday']) . "'" 
					    . ",'" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['pubkey']) . "'"
					    . ",'" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['title']) . "'"
					    . ",'" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['org_unit']) . "'"
					    . ",'" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['sound']) . "'"
					    . "," . $data['owner'] 
					    . "," . $data['owner'] 
					    . "," . $time
					    . "," . $time
					    . "," . $key 
					    . ')' 
					);

				if($data['org_name'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact (owner,access,cat_id,contact_type_id,ab_id) VALUES ("
						    . $data['owner']
						    . "," . ($data['access']?"'".$data['access']."'":"null") 
						    . "," . ($data['cat_id']?"'".$data['cat_id']."'":"null")
						    . "," . $contact_type_org
						    . "," . $key 
						    . ')');
				
					$contact_id++;
					$org_id = $contact_id;

					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_org (org_id,name,
					created_by,modified_by,created_on,modified_on,ab_id) VALUES ("
						    . $org_id
						    . ",'" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['org_name']) . "'"
						    . "," . $data['owner']
						    . "," . $data['owner'] 
						    . "," . $time
						    . "," . $time
						    . "," . $key 
						    .')');

					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_org_person (org_id,person_id, preferred,
					created_on,created_by) VALUES (" 
						    . $org_id 
						    . ", " . $person_id 
						    . ", 'Y'"	    
						    . ", " . $time
						    . ", " . $time 
						    .  ")");
				}

				if($data['addr_one_stret']!='' || $data['adr_one_locality']!='' || $data['adr_one_region']!='' || $data['adr_one_postalcode']!='' || $data['adr_one_countryname']!='')
				{
					$addr_preferred = 'Y';
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_addr (contact_id,add1,add2,add3,preferred,
					city, state, postal_code, country, tz, addr_type_id,
					created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id 
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['adr_one_street']) ." '"
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($others[$key]['address2']['value']) ." '"
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($others[$key]['address3']['value']) ." '"
						    . ", '" . $addr_preferred . " '"
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['adr_one_locality']) ." '"
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['adr_one_region']) ." '"
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['adr_one_postalcode']) ." '"
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['adr_one_countryname']) ." '"
						    . ", " . intval($data['tz'])
						    . ", " . $addr_work_type
						    . ", " . $time 
						    . ", " . $data['owner'] 
						    . ", " . $time
						    . ", " . $data['owner']
						    .  ")");
				}

				if($data['addr_two_stret']!='' || $data['adr_two_locality']!='' || $data['adr_two_region']!='' || $data['adr_two_postalcode']!='' || $data['adr_two_countryname']!='')
				{
					$addr_preferred = $addr_preferred=='Y'?'N':'Y';					
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_addr (contact_id,add1,preferred,
					city, state, postal_code, country, tz, addr_type_id,
					created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id 
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['adr_two_street']) ." '"
						    . ", '" . $addr_preferred . " '"
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['adr_two_locality']) ." '"
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['adr_two_region']) ." '"
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['adr_two_postalcode']) ." '"
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['adr_two_countryname']) ." '"
						    . ", " . intval($data['tz'])
						    . ", " . $addr_home_type
						    . ", " . $time 
						    . ", " . $data['owner'] 
						    . ", " . $time
						    . ", " . $data['owner']
						    .  ")");
				}
				
				if($data['url'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['website']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['url']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}
				if($data['tel_work'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['work phone']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['tel_work']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}
				if($data['tel_home'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['home phone']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['tel_home']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}
				if($data['tel_voice'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['voice phone']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['tel_voice']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}
				if($data['tel_fax'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['work fax']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['tel_fax']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}
				if($data['tel_msg'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['msg phone']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['tel_msg']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}
				if($data['tel_cell'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['mobile (cell) phone']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['tel_cell']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}
				if($data['tel_pager'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['pager']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['tel_pager']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}
				if($data['tel_bbs'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['bbs']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['tel_bbs']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}
				if($data['tel_modem'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['modem']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['tel_modem']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}
				if($data['tel_car'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['car phone']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['tel_car']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}
				if($data['tel_isdn'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['isdn']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['tel_isdn']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}
				if($data['tel_video'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['video']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['tel_video']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}
				if($data['email'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['work email']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['email']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}
				if($data['email_home'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_comm 
					(contact_id,comm_descr_id,comm_data,created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", "  . $comm_descr_id['home email']
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['email_home']) . "'"
						    . ", "  . $time 
						    . ", "  . $data['owner']
						    . ", "  . $time
						    . ", "  . $data['owner']
						    . ")");
				}

				if($data['note'])
				{
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_note (contact_id,note_text, 
					created_on,created_by,modified_on,modified_by) VALUES (" 
						    . $person_id
						    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($data['note']) . "'"
						    . ", " . $time 
						    . ", " . $data['owner'] 
						    . ", " . $time
						    . ", " . $data['owner']
						    .  ")");
				}

				if($others[$key])
				{
					foreach($others[$key] as $name => $value)
					{
						$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_others (contact_id, contact_owner, other_name, other_value) VALUES("
							    . $person_id
							    . ", " . $value['owner']
							    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($name)  . "' "
							    . ", '" . $GLOBALS['phpgw_setup']->db->db_addslashes($value['value']) . "' "
							    .")");
					}
				}
			}
		}
                
		unset($records);
		unset($others);

		//maybe leave these until a future version to catch any data migration problems that are reported
		// when all data migrated, delete phpgw_addressbook and phpgw_addressbook_extra tables
 		//$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_addressbook');
 		//$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_addressbook_extra');
		
		// remove temporary ab_id field in phpgw_contact, phpgw_contact_person, and phpgw_contact_org
		//OR NOT! On 512!

		// Paste this below!!!
		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.509';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}

	$test[] = '0.9.14.509';
	//fix some languages to be ISO 639 compliant
	//Source: http://www.geo-guide.de/info/tools/languagecode.html
	function phpgwapi_upgrade0_9_14_509()
	{
		//Indonesian code fix
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_languages SET lang_id='id' WHERE lang_id='in'");
		//bn name fix
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_languages SET lang_name='Bengali' WHERE lang_id='bn'");
		//English is English not English US - the US didn't invent it!
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_languages SET lang_name='English' WHERE lang_id='en'");
		//gd name fix
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_languages SET lang_name='Scots Gaelic' WHERE lang_id='gd'");
		//Hebrew code fix
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_languages SET lang_id='he' WHERE lang_id='iw'");
		//Yiddish code fix
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_languages SET lang_id='yi' WHERE lang_id='ji'");
		//Make Chinese names clearer
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_languages SET lang_name='Chinese (Simplified)' WHERE lang_id='zh'");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_languages SET lang_name='Chinese (Traditional)' WHERE lang_id='zt'");
		//Add Missing Languages
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_languages(lang_id, lang_name, available) VALUES('iu', 'Inuktitut', 'No')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_languages(lang_id, lang_name, available) VALUES('ug', 'Uigur', 'No')");

		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.510';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}

	$test[] = '0.9.14.510';
	//fix some languages to be ISO 639 compliant
	//Source: http://www.geo-guide.de/info/tools/languagecode.html
	function phpgwapi_upgrade0_9_14_510()
	{
		// Set up the new logging tables	
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_log_msg');
		// just drop and re-create.  We don't need to save it and the structure has changed alot
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_log');
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_log',array(
			'fd' => array(
				'log_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'log_date' => array('type' => 'timestamp','nullable' => False),
				'log_account_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'log_account_lid' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'log_app' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'log_severity' => array('type' => 'char','precision' => '1','nullable' => False),
				'log_file' => array('type' => 'varchar','precision' => '255','nullable' => False, 'default' => ''),
				'log_line' => array('type' => 'int','precision' => '4','nullable' => False, 'default' => '0'),
				'log_msg' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('log_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM phpgw_config WHERE config_name = 'log_levels'");
		if (!$GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app, config_name, config_value) 
							VALUES ('phpgwapi','log_levels', '" 
							. serialize(array( 'global_level' => 'E', 'module' => array(), 'user' => array())) ."')");
		}
		else
		{
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_config SET config_app='phpgwapi', "
							      ."config_name='log_levels', "
							      ."config_value='". serialize(array('global_level' => 'E', 
												'module' => array(), 
												'user' => array())) ."'");
		}		
		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.511';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}

	$test[] = '0.9.14.511';
	function phpgwapi_upgrade0_9_14_511()
	{
		//global $setup_info,$phpgw_setup;
		//$db1 =& $GLOBALS['phpgw_setup']->db; $db1->auto_stripslashes = False; $db1->Halt_On_Error = 'yes';

		/* Check if addressmaster exist  */
		$GLOBALS['phpgw_setup']->db->query("SELECT config_name, config_value FROM phpgw_config WHERE config_name = 'addressmaster'");
		$GLOBALS['phpgw_setup']->db->next_record();
		if($GLOBALS['phpgw_setup']->db->f('config_name'))
		{
			$addressmaster_id = $GLOBALS['phpgw_setup']->db->f('config_value');
		}
		else
		{
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_config (config_app, config_name, config_value) 
				VALUES ('phpgwapi','addressmaster','-3')");
			$addressmaster_id = -3;
		}

		/* Get the contact type id  */
		$GLOBALS['phpgw_setup']->oProc->query("SELECT contact_type_id FROM phpgw_contact_types WHERE contact_type_table='phpgw_contact_person'");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$contact_type_id = $GLOBALS['phpgw_setup']->oProc->f('contact_type_id');

		/* Get the contact_id  */
		$GLOBALS['phpgw_setup']->oProc->query("SELECT max(contact_id) as contact_id FROM phpgw_contact");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$contact_id = $GLOBALS['phpgw_setup']->oProc->f('contact_id');

		/* Get all user accounts for create his contact record   */
		$GLOBALS['phpgw_setup']->oProc->query("SELECT account_id, account_lid, account_firstname, account_lastname, person_id FROM phpgw_accounts WHERE account_type='u' AND person_id IS NULL order by account_id");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			/* Insert in phpgw_contact  */
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact (owner, access, contact_type_id) VALUES(-3"
							   . ", '".'public'. "', ". $contact_type_id.")");
			
			/* Get the contact_id  */
			$contact_id++;
			
			/* Insert in phpgw_contact_person */
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_person (person_id,first_name,last_name,
				prefix,created_by,modified_by,created_on,modified_on) VALUES ("
				. $contact_id
				. ",'" . $GLOBALS['phpgw_setup']->db->db_addslashes($GLOBALS['phpgw_setup']->oProc->f('account_firstname')) . "'"
				. ",'" . $GLOBALS['phpgw_setup']->db->db_addslashes($GLOBALS['phpgw_setup']->oProc->f('account_lastname')) . "'"
				. ",'" . $GLOBALS['phpgw_setup']->db->db_addslashes($GLOBALS['phpgw_setup']->oProc->f('account_lid')) . "'"
				. "," . $addressmaster_id 
				. "," . $addressmaster_id 
				. "," . time()
				. "," . time(). ')');

 			$GLOBALS['phpgw_setup']->db->query('UPDATE phpgw_accounts SET person_id=' . $contact_id . ' WHERE account_id='. intval($GLOBALS['phpgw_setup']->oProc->f('account_id')));
		}
		
		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.512';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}

	$test[] = '0.9.14.512';
	function phpgwapi_upgrade0_9_14_512()
	{
		$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_note_type (description) VALUES ('general')");
		$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_note_type (description) VALUES ('vcard')");
		$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_contact_note_type (description) VALUES ('system')");

		$GLOBALS['phpgw_setup']->oProc->query("SELECT note_type_id FROM phpgw_contact_note_type WHERE description='general'");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$note_type = $GLOBALS['phpgw_setup']->oProc->f('note_type_id');

		$GLOBALS['phpgw_setup']->db->query("UPDATE phpgw_contact_note SET note_type_id=".$note_type);
		
		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.513';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}

	$test[] = '0.9.14.513';
	function phpgwapi_upgrade0_9_14_513()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_accounts','account_quota',array('type' => 'int','precision' => '4','default' => -1,'nullable' => True));
		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.14.514';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}

	$test[] = '0.9.14.514';
	function phpgwapi_upgrade0_9_14_514()
	{
		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.16.000';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}

	$test[] = '0.9.16.000';
        function phpgwapi_upgrade0_9_16_000()
        {
                $GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.16.001';
                return $GLOBALS['setup_info']['phpgwapi']['currentver'];
        }

	$test[] = '0.9.16.001';
        function phpgwapi_upgrade0_9_16_001()
        {
                $GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.16.002';
                return $GLOBALS['setup_info']['phpgwapi']['currentver'];
        }

	$test[] = '0.9.16.002';
        function phpgwapi_upgrade0_9_16_002()
        {
                $GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.16.003';
                return $GLOBALS['setup_info']['phpgwapi']['currentver'];
        }

	$test[] = '0.9.16.003';
	function phpgwapi_upgrade0_9_16_003()
	{
		return $GLOBALS['setup_info']['phpgwapi']['currentver']= '0.9.16.004';
	}

	$test[] = '0.9.16.004';
	function phpgwapi_upgrade0_9_16_004()
	{
		return $GLOBALS['setup_info']['phpgwapi']['currentver']= '0.9.16.005';
	}

	$test[] = '0.9.16.005';
	function phpgwapi_upgrade0_9_16_005()
	{
		return $GLOBALS['setup_info']['phpgwapi']['currentver']= '0.9.16.006';
	}

	$test[] = '0.9.16.006';
	function phpgwapi_upgrade0_9_16_006()
	{
		return $GLOBALS['setup_info']['phpgwapi']['currentver']= '0.9.16.007';
	}

	$test[] = '0.9.16.007';
	function phpgwapi_upgrade0_9_16_007()
	{
		return $GLOBALS['setup_info']['phpgwapi']['currentver']= '0.9.16.008';
	}

	$test[] = '0.9.16.008';
	function phpgwapi_upgrade0_9_16_008()
	{
		return $GLOBALS['setup_info']['phpgwapi']['currentver']= '0.9.16.009';
	}

	$test[] = '0.9.16.009';
	function phpgwapi_upgrade0_9_16_009()
	{
		return $GLOBALS['setup_info']['phpgwapi']['currentver']= '0.9.16.010';
	}

	$test[] = '0.9.16.010';
	function phpgwapi_upgrade0_9_16_010()
	{
		return $GLOBALS['setup_info']['phpgwapi']['currentver']= '0.9.16.011';
	}

	$test[] = '0.9.16.011';
	function phpgwapi_upgrade0_9_16_011()
	{
		return $GLOBALS['setup_info']['phpgwapi']['currentver']= '0.9.16.012';
	}

	$test[] = '0.9.16.012';
	/**
	 * Bump the version
	 *
	 * @return string the new phpGW version
	 */
	function phpgwapi_upgrade0_9_16_012()
	{
		return $GLOBALS['phpgw_info']['phpgwapi']['currentver'] = '0.9.16.014';
	}

	$test[] = '0.9.16.014';
	function phpgwapi_upgrade0_9_16_014()
	{
		return $GLOBALS['phpgw_info']['phpgwapi']['currentver'] = '0.9.16.015';
	}

	$test[] = '0.9.16.015';
	function phpgwapi_upgrade0_9_16_015()
	{
		// Fix the ipv6 issue - allready taken care of
		// $GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_access_log','ip',array('type' => 'char', 'precision' => 100));

		return $GLOBALS['phpgw_info']['phpgwapi']['currentver'] = '0.9.16.016';
	}

	$test[] = '0.9.16.016';
	function phpgwapi_upgrade0_9_16_016()
	{
		return $GLOBALS['phpgw_info']['phpgwapi']['currentver'] = '0.9.16.017';
	}

	$test[] = '0.9.16.017';
	function phpgwapi_upgrade0_9_16_017()
	{
		return $GLOBALS['phpgw_info']['phpgwapi']['currentver'] = '0.9.17.000';
	}

	/**
	* Update phpgwapi from intermadiate
	*/
	$test[] = '0.9.17.000';
	function phpgwapi_upgrade0_9_17_000()
	{
		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.001';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}
	$test[] = '0.9.17.001';
	function phpgwapi_upgrade0_9_17_001()
	{
		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.002';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}
	$test[] = '0.9.17.002';
	function phpgwapi_upgrade0_9_17_002()
	{
		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.003';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}
	$test[] = '0.9.17.003';
	function phpgwapi_upgrade0_9_17_003()
	{
		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.004';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}
	$test[] = '0.9.17.004';
	function phpgwapi_upgrade0_9_17_004()
	{
		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.500';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}

	$test[] = '0.9.17.500';
	function phpgwapi_upgrade0_9_17_500()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_cust_fields',array(
			'fd' => array(
				'cust_field_id' => array('type' => 'auto','nullable' => False),
				'cust_field_name' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'cust_field_type_id' => array('type' => 'int','precision' => '8','nullable' => False),
				'cust_field_label' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'appname' => array('type' => 'varchar', 'precision' => 25, 'nullable' => True),
				'cust_field_active' => array('type' => 'varchar','precision' => '255','nullable' => False,'default' => '1')
			),
			'pk' => array('cust_field_id'),
			'fk' => array(),
			'ix' => array('cust_field_name','cust_field_type_id'),
			'uc' => array()
		));


		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_cust_field_types',array(
			'fd' => array(
				'cust_field_type_id' => array('type' => 'auto','nullable' => False),
				'cust_field_type_descr' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'cust_field_type_active' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '1')
			),
			'pk' => array('cust_field_type_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_cust_field_values',array(
			'fd' => array(
				'cust_field_value_id' => array('type' => 'auto','nullable' => False),
				'cust_field_value_val' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'cust_field_id' => array('type' => 'int','precision' => '8','nullable' => False),
				'appname' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'location' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'rec_id' => array('type' => 'int','precision' => '8','nullable' => False)
			),
			'pk' => array('cust_field_value_id'),
			'fk' => array(),
			'ix' => array('cust_field_value_val','cust_field_id','appname','location','rec_id'),
			'uc' => array()
		));
		
		$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_field_types(cust_field_type_descr) VALUES(\'text\')');
		$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_field_types(cust_field_type_descr) VALUES(\'number\')');
		$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_field_types(cust_field_type_descr) VALUES(\'date\')');
		$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_field_types(cust_field_type_descr) VALUES(\'list\')');
		$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_field_types(cust_field_type_descr) VALUES(\'db lookup\')');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.501';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.501';
	function phpgwapi_upgrade0_9_17_501()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_acl','acl_grantor',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True,
			'default' 	=> -1,
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_acl','acl_type',array(
			'type' => 'int',
			'precision' => '2',
			'nullable' => True,
			'default' 	=> '0',
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_acl_location', array(
				'fd' => array(
					'appname' => array('type' => 'varchar','precision' => '25','nullable' => False),
					'id' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'descr' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'allow_grant' => array('type' => 'int','precision' => '4','nullable' => True)
				),
				'pk' => array('appname','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.502';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}


	$test[] = '0.9.17.502';
	function phpgwapi_upgrade0_9_17_502()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_cust_attribute', array(
				'fd' => array(
					'appname' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'location' => array('type' => 'varchar','precision' => '30','nullable' => False),
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'column_name' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'input_text' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'statustext' => array('type' => 'varchar','precision' => '150','nullable' => False),
					'datatype' => array('type' => 'varchar','precision' => '10','nullable' => False),
					'search' => array('type' => 'int','precision' => '2','nullable' => True),
					'history' => array('type' => 'int','precision' => '2','nullable' => True),
					'list' => array('type' => 'int','precision' => '4','nullable' => True),
					'attrib_sort' => array('type' => 'int','precision' => '4','nullable' => True),
					'size' => array('type' => 'int','precision' => '4','nullable' => True),
					'precision_' => array('type' => 'int','precision' => '4','nullable' => True),
					'scale' => array('type' => 'int','precision' => '4','nullable' => True),
					'default_value' => array('type' => 'varchar','precision' => '20','nullable' => True),
					'nullable' => array('type' => 'varchar','precision' => '5','nullable' => True)
				),
				'pk' => array('appname','location','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_cust_choice', array(
				'fd' => array(
					'appname' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'location' => array('type' => 'varchar','precision' => '30','nullable' => False),
					'attrib_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'value' => array('type' => 'text','nullable' => False)
				),
				'pk' => array('appname','location','attrib_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);


		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_acl_location','allow_c_attrib',array(
			'type' => 'int',
			'precision' => '2',
			'nullable' => True
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_acl_location','c_attrib_table',array(
			'type' => 'varchar',
			'precision' => '25',
			'nullable' => True
		));

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl_location WHERE appname = 'tts' AND id = '.'");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr,allow_grant,allow_c_attrib,c_attrib_table) VALUES ('tts', '.', 'Top',1,1,'phpgw_tts_tickets')");
		
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.503';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.503';
	function phpgwapi_upgrade0_9_17_503()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_cust_function', array(
				'fd' => array(
					'appname' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'location' => array('type' => 'varchar','precision' => '30','nullable' => False),
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'descr' => array('type' => 'text','nullable' => True),
					'file_name ' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'active' => array('type' => 'int','precision' => '2','nullable' => True),
					'custom_sort' => array('type' => 'int','precision' => '4','nullable' => True)
				),
				'pk' => array('appname','location','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
	
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.504';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}
	
	$test[] = '0.9.17.504';
	function phpgwapi_upgrade0_9_17_504()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_lang','app_name',array(
			'type' => 'varchar',
			'precision' => 25,
			'default' => 'common',
			'nullable' => False
		));
	
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.505';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.505';
	function phpgwapi_upgrade0_9_17_505()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_access_log','ip',array(
			'type' => 'varchar',
			'precision' => '50',
			'nullable' => False,
			'default' => '::1'
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.506';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.506';
	function phpgwapi_upgrade0_9_17_506()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		// size of pk fields were modified, due to problems with some SQL server not able to handle more than 1000 caracters size for pk
		// so as using utf8, (100 + 25 + 200) * 3 < 1000, it shouldn't complain anymore.
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_mapping', array(
				'fd' => array(
					'ext_user' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
					'auth_type' => array('type' => 'varchar', 'precision' => 25, 'nullable' => false),
					'status' => array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'A'),
					'location' => array('type' => 'varchar', 'precision' => 200, 'nullable' => false),
					'account_lid' => array('type' => 'varchar', 'precision' => 25, 'nullable' => false)
				),
				'pk' => array('ext_user', 'location', 'auth_type'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
				)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.507';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.507';
	function phpgwapi_upgrade0_9_17_507()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_cust_attribute');
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_cust_attribute_table', array(
				'fd' => array(
					'table_id' => array('type' => 'auto','nullable' => False),
					'appname' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'location' => array('type' => 'varchar','precision' => '30','nullable' => False),
					'table_name' => array('type' => 'varchar','precision' => '50','nullable' => False)
				),
				'pk' => array('table_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('appname','location')
				)
		);


		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_cust_attribute', array(
				'fd' => array(
					'table_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'column_name' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'input_text' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'statustext' => array('type' => 'varchar','precision' => '150','nullable' => False),
					'datatype' => array('type' => 'varchar','precision' => '10','nullable' => False),
					'search' => array('type' => 'int','precision' => '2','nullable' => True),
					'history' => array('type' => 'int','precision' => '2','nullable' => True),
					'list' => array('type' => 'int','precision' => '4','nullable' => True),
					'attrib_sort' => array('type' => 'int','precision' => '4','nullable' => True),
					'size' => array('type' => 'int','precision' => '4','nullable' => True),
					'precision_' => array('type' => 'int','precision' => '4','nullable' => True),
					'scale' => array('type' => 'int','precision' => '4','nullable' => True),
					'default_value' => array('type' => 'varchar','precision' => '20','nullable' => True),
					'nullable' => array('type' => 'varchar','precision' => '5','nullable' => True)
				),
				'pk' => array('table_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
				)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.508';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.508';
	function phpgwapi_upgrade0_9_17_508()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_cust_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_cust_attribute_table');		

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_cust_attribute', array(
				'fd' => array(
					'appname' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'location' => array('type' => 'varchar','precision' => '30','nullable' => False),
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'column_name' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'input_text' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'statustext' => array('type' => 'varchar','precision' => '150','nullable' => False),
					'datatype' => array('type' => 'varchar','precision' => '10','nullable' => False),
					'search' => array('type' => 'int','precision' => '2','nullable' => True),
					'history' => array('type' => 'int','precision' => '2','nullable' => True),
					'list' => array('type' => 'int','precision' => '4','nullable' => True),
					'attrib_sort' => array('type' => 'int','precision' => '4','nullable' => True),
					'size' => array('type' => 'int','precision' => '4','nullable' => True),
					'precision_' => array('type' => 'int','precision' => '4','nullable' => True),
					'scale' => array('type' => 'int','precision' => '4','nullable' => True),
					'default_value' => array('type' => 'varchar','precision' => '20','nullable' => True),
					'nullable' => array('type' => 'varchar','precision' => '5','nullable' => True)
				),
				'pk' => array('appname','location','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
				)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.509';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}
	
	$test[] = '0.9.17.509';
	function phpgwapi_upgrade0_9_17_509()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_acl set acl_type = 0 WHERE acl_type is NULL");
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_acl','acl_type',array(
			'type' => 'int',
			'precision' => '2',
			'nullable' => True,
			'default' => '0'
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.510';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.510';
	function phpgwapi_upgrade0_9_17_510()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_cust_fields');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_cust_field_types');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_cust_field_values');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.511';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}	


	$test[] = '0.9.17.511';
	function phpgwapi_upgrade0_9_17_511()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_mail_handler',array(
			'fd' => array(
				'handler_id' => array('type' => 'auto','nullable' => False),
				'target_email' => array('type' => 'varchar','precision' => '75','nullable' => False),
				'handler' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'is_active' => array('type' => 'int','precision' => '4','nullable' => False),
				'lastmod' => array('type' => 'int','precision' => '8','nullable' => False),
				'lastmod_user' => array('type' => 'int','precision' => '8','nullable' => False)
			),
			'pk' => array('handler_id'),
			'fk' => array(),
			'ix' => array('target_email','is_active'),
			'uc' => array()
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.512';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}
	
	$test[] = '0.9.17.512';
	function phpgwapi_upgrade0_9_17_512()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_cust_attribute','disabled',array(
			'type' => 'int',
			'precision' => '2',
			'nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_cust_attribute','helpmsg',array(
			'type' => 'text',
			'nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_cust_attribute','lookup_form',array(
			'type' => 'int',
			'precision' => '2',
			'nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_cust_attribute','custom',array(
			'type' => 'int',
			'precision' => '2',
			'default' => 1,
			'nullable' => True
		));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_cust_attribute SET custom = 1");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.513';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.513';
	function phpgwapi_upgrade0_9_17_513()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_contact_addr','addr_type_id',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True
		));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_contact_org','active',array(
			'type' => 'char',
			'precision' => '1',
			'nullable' => True,
			'default' => 'Y'
		));

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.514';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.514';
	/**
	* Drop the old and unneeded addressbook tables
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_514()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_addressbook');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_addressbook_extra');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.515';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.515';
	/**
	* Implement new interlink and ACL systems - quite a few related changes here too
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_515()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_accounts', 'account_pwd', array
		(
			'type'		=> 'varchar',
			'precision' => '40',
			'nullable'	=> false,
			'default'	=> ''
		));

		// Convert the SQL accounts hashes to the new format
		$accounts = array();
		$sql = 'SELECT account_id, account_pwd FROM phpgw_accounts';
		$GLOBALS['phpgw_setup']->oProc->m_odb->query($sql, __LINE__, __FILE__);
		while ( $GLOBALS['phpgw_setup']->oProc->m_odb->next_record() )
		{
			$accounts[$GLOBALS['phpgw_setup']->oProc->m_odb->f('account_id')] = $GLOBALS['phpgw_setup']->oProc->m_odb->f('account_pwd');
		}

		foreach ( $accounts as $id => $pwd )
		{
			$new_hash = '{MD5}' . base64_encode(pack('H*', $pwd));
			$sql = 'UPDATE phpgw_accounts'
				. " SET account_pwd = '{$new_hash}'"
				. " WHERE account_id = {$id}";
			$GLOBALS['phpgw_setup']->oProc->m_odb->query($sql, __LINE__, __FILE__);
		}

		unset($accounts);

		// New table for handling groups - only used for SQL accounts - LDAP will store it in LDAP - memberUID
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_group_map', array
		(
			'fd' => array
			(
				'group_id'		=> array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'account_id'	=> array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'arights'		=> array('type' => 'int', 'precision' => 4, 'nullable' => false, 'default' => 1)
			),
			'pk' => array('group_id', 'account_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$rows = array();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query('SELECT DISTINCT acl_location, acl_account, acl_rights'
			. " FROM phpgw_acl WHERE acl_appname = 'phpgw_group'", __LINE__, __FILE__);
		while ( $GLOBALS['phpgw_setup']->oProc->m_odb->next_record() )
		{
			$rows[] = array
			(
				'group'	=> $GLOBALS['phpgw_setup']->oProc->m_odb->f('acl_location'),
				'user'	=> $GLOBALS['phpgw_setup']->oProc->m_odb->f('acl_account'),
				'rights'=> $GLOBALS['phpgw_setup']->oProc->m_odb->f('acl_rights')
			);
		}

		foreach ( $rows as $row )
		{
			$GLOBALS['phpgw_setup']->oProc->m_odb->query("INSERT INTO phpgw_group_map VALUES({$row['group']}, {$row['user']}, {$row['rights']})", __LINE__, __FILE__);
		}
		unset($rows);

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM phpgw_acl WHERE acl_appname = 'phpgw_group'", __LINE__, __FILE__);

		$apps = array();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query('SELECT app_name FROM phpgw_applications', __LINE__, __FILE__);
		while ( $GLOBALS['phpgw_setup']->oProc->next_record() )
		{
			$apps[] = $GLOBALS['phpgw_setup']->oProc->m_odb->f('app_name', true);
		}

		foreach ( $apps as $app )
		{
			$sql = 'INSERT INTO phpgw_acl_location(appname, id, descr)'
				. " VALUES ('{$app}', 'run', 'app run rights created during 0.9.17.516 migration')";
			
			$GLOBALS['phpgw_setup']->oProc->m_odb->query($sql, __LINE__, __FILE__);
		}
		unset($apps);

		$location = array();

		// First get all the current locations
		$GLOBALS['phpgw_setup']->oProc->m_odb->query('SELECT DISTINCT app_id, acl_location FROM phpgw_acl'
			. "{$GLOBALS['phpgw_setup']->oProc->m_odb->join} phpgw_applications ON phpgw_applications.app_name = phpgw_acl.acl_appname", __LINE__, __FILE__);
		while ( $GLOBALS['phpgw_setup']->oProc->next_record() )
		{
			$location[$GLOBALS['phpgw_setup']->oProc->f('app_id') . '::' . $GLOBALS['phpgw_setup']->oProc->f('acl_location')] = array
			(
					'app_id'			=> $GLOBALS['phpgw_setup']->oProc->f('app_id'),
					'name'				=> $GLOBALS['phpgw_setup']->oProc->f('acl_location'),
					'descr'				=> 'Automatically migrated location - created during 0.9.17.515 upgrade',
					'allow_grant'		=> false,
					'allow_c_attrib'	=> false,
					'c_attrib_table'	=> ''
			);
		}

		// If they have proper locations already, override the basic location entry for that location
		$GLOBALS['phpgw_setup']->oProc->query('SELECT phpgw_acl_location.*, phpgw_applications.app_id '
			. ' FROM phpgw_acl_location' 
			. " {$GLOBALS['phpgw_setup']->oProc->m_odb->join} phpgw_applications ON phpgw_acl_location.appname = phpgw_applications.app_name" 
			, __LINE__, __FILE__); 
		while ( $GLOBALS['phpgw_setup']->oProc->next_record() )
		{
			$location[$GLOBALS['phpgw_setup']->oProc->f('app_id') . '::' . $GLOBALS['phpgw_setup']->oProc->f('id')] = array
			(
					'app_id'			=> $GLOBALS['phpgw_setup']->oProc->f('app_id'),
					'name'				=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'descr'				=> $GLOBALS['phpgw_setup']->oProc->f('descr'),
					'allow_grant'		=> !!$GLOBALS['phpgw_setup']->oProc->f('allow_grant'),
					'allow_c_attrib'	=> !!$GLOBALS['phpgw_setup']->oProc->f('allow_c_attrib'),
					'c_attrib_table'	=> $GLOBALS['phpgw_setup']->oProc->f('c_attrib_table'),
			);
		}


		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_locations', array
		(
			'fd' => array
			(
				'location_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'app_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'allow_grant' => array('type' => 'int','precision' => '2','nullable' => True),
				'allow_c_attrib' => array('type' => 'int','precision' => '2','nullable' => True),
				'c_attrib_table' => array('type' => 'varchar','precision' => '25','nullable' => True)
			),
			'pk' => array('location_id'),
			'fk' => array(),
			'ix' => array('app_id', 'name'),
			'uc' => array()
		));

		foreach ($location as $entry)
		{
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_locations(' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')', __LINE__, __FILE__);
		}
		
		unset($location);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_acl', 'location_id', array
		(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True
		));

		$locations = array();
		$GLOBALS['phpgw_setup']->oProc->query('SELECT DISTINCT phpgw_acl.acl_location, phpgw_locations.location_id, phpgw_applications.app_name'
			. ' FROM phpgw_acl'
			. " {$GLOBALS['phpgw_setup']->oProc->m_odb->join} phpgw_locations ON phpgw_acl.acl_location = phpgw_locations.name"
			. " {$GLOBALS['phpgw_setup']->oProc->m_odb->join} phpgw_applications ON phpgw_acl.acl_appname = phpgw_applications.app_name"
			. ' WHERE phpgw_locations.app_id = phpgw_applications.app_id'
			, __LINE__, __FILE__); 
		while ( $GLOBALS['phpgw_setup']->oProc->next_record() )
		{
			$locations[] = array
			(
					'location_id'		=> $GLOBALS['phpgw_setup']->oProc->f('location_id'),
					'acl_location'		=> $GLOBALS['phpgw_setup']->oProc->f('acl_location'),
					'app_name'			=> $GLOBALS['phpgw_setup']->oProc->f('app_name')
			);
		}

		foreach ( $locations as $entry )
		{
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_acl SET location_id = {$entry['location_id']} WHERE acl_location = '{$entry['acl_location']}' AND acl_appname = '{$entry['app_name']}'", __LINE__, __FILE__);
		}
		unset($locations);

		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_acl_location');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_acl',null,'acl_location');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_acl',null,'acl_appname');

//---------- history_log

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_history_log', 'app_id', array
		(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_history_log', 'location_id', array
		(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True
		));

		$apps = array();
		$GLOBALS['phpgw_setup']->oProc->query('SELECT phpgw_history_log.history_appname, phpgw_applications.app_id'
			. ' FROM phpgw_history_log'
			. " {$GLOBALS['phpgw_setup']->oProc->m_odb->join} phpgw_applications ON phpgw_history_log.history_appname = phpgw_applications.app_name"
			. ' GROUP BY history_appname, phpgw_applications.app_id'
			, __LINE__, __FILE__);
		while ( $GLOBALS['phpgw_setup']->oProc->next_record() )
		{
			$apps[]=array
			(
					'app_id'				=> $GLOBALS['phpgw_setup']->oProc->f('app_id'),
					'history_appname'		=> $GLOBALS['phpgw_setup']->oProc->f('history_appname')
			);
		}

		foreach ( $apps as $entry )
		{
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_history_log SET app_id = {$entry['app_id']} WHERE history_appname = '{$entry['history_appname']}'");
		}

		unset($apps);
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_history_log',null,'history_appname');


//-------------interlink


		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_interlink',array
		(
			'fd' => array
			(
				'interlink_id'		=> array('type' => 'auto','precision' => '4','nullable' => False),
				'location1_id'		=> array('type' => 'int','precision' => '4','nullable' => False),
				'location1_item_id'	=> array('type' => 'int','precision' => '4','nullable' => False),
				'location2_id'		=> array('type' => 'int','precision' => '4','nullable' => False),
				'location2_item_id'	=> array('type' => 'int','precision' => '4','nullable' => False),
				'is_private'		=> array('type' => 'int','precision' => '2','nullable' => False),
				'account_id'		=> array('type' => 'int','precision' => '4','nullable' => False),
				'entry_date'		=> array('type' => 'int','precision' => '4','nullable' => False),
				'start_date'		=> array('type' => 'int','precision' => '4','nullable' => False),
				'end_date'			=> array('type' => 'int','precision' => '4','nullable' => False),
			),
			'pk' => array('interlink_id'), // not sure about the pk
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		// Sessions changes
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_app_sessions'); // no longer needed
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_sessions');
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_sessions', array
		(
			'fd' => array(
				'session_id'	=> array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'ip'			=> array('type' => 'varchar', 'precision' => 100), // optional security
				'data'			=> array('type' => 'longtext'), // gives us more room to move
				'lastmodts'		=> array('type' => 'int', 'precision' => 4),
			),
			'pk' => array('session_id'),
			'fk' => array(),
			'ix' => array('lastmodts'),
			'uc' => array()
		));

		$lookups = array
		(
			'max_access_log_age'	=> 90,
			'block_time'			=> 30,
			'num_unsuccessful_id'	=> 3,
			'num_unsuccessful_ip'	=> 3,
			'install_id'			=> sha1(uniqid(rand(), true)),
			'max_history'			=> 20
		);
		foreach ( $lookups as $name => $val )
		{
			$sql = "SELECT * FROM phpgw_config WHERE config_name = '{$name}' AND config_app = 'phpgwapi'";
			$GLOBALS['phpgw_setup']->oProc->m_odb->query($sql, __LINE__, __FILE__);
			if ( $GLOBALS['phpgw_setup']->oProc->m_odb->next_record() )
			{
				unset($lookups[$name]);
			}
		}
		
		foreach ( $lookups as $name => $val )
		{
			$sql = "INSERT INTO phpgw_config VALUES('phpgwapi', '{$name}', '{$val}')";
			$GLOBALS['phpgw_setup']->oProc->m_odb->query($sql, __LINE__, __FILE__);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.516';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.516';
	/**
	* Upgrade the phpgw_cust* tables to use the new location code
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_516()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$db =& $GLOBALS['phpgw_setup']->oProc->m_odb;

		$attribs = array();

		$sql = 'SELECT * FROM phpgw_cust_attribute';
		$db->query($sql, __LINE__, __FILE__);
		while ( $db->next_record() )
		{
			$attribs[] = array
			(
				'appname'		=> $db->f('appname', true),
				'location'		=> $db->f('location', true),
				'id'			=> $db->f('id'),
				'column_name'	=> $db->f('column_name', true),
				'input_text'	=> $db->f('input_text', true),
				'statustext'	=> $db->f('statustext', true),
				'datatype'		=> $db->f('datatype', true),
				'search'		=> $db->f('search'),
				'history'		=> $db->f('history'),
				'list'			=> $db->f('list'),
				'attrib_sort'	=> $db->f('attrib_sort'),
				'size'			=> $db->f('size'),
				'precision_'	=> $db->f('precision'),
				'scale'			=> $db->f('scale'),
				'default_value'	=> $db->f('default_value', true),
				'nullable'		=> $db->f('nullable'),
				'disabled'		=> $db->f('disabled'),
				'lookup_form'	=> $db->f('lookup_form', true),
				'custom'		=> $db->f('custom', true),
				'helpmsg'		=> $db->f('helpmsg', true)
			);
		}

		// New PK so drop the table
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_cust_attribute');
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_cust_attribute', array
		(
			'fd' => array
			(
				'location_id' => array('type' => 'int','precision' => 2,'nullable' => false),
				'id' => array('type' => 'int','precision' => 2,'nullable' => false),
				'column_name' => array('type' => 'varchar','precision' => 20,'nullable' => false),
				'input_text' => array('type' => 'varchar','precision' => 50,'nullable' => false),
				'statustext' => array('type' => 'varchar','precision' => '150','nullable' => false),
				'datatype' => array('type' => 'varchar','precision' => '10','nullable' => false),
				'search' => array('type' => 'int','precision' => 2,'nullable' => true),
				'history' => array('type' => 'int','precision' => 2,'nullable' => true),
				'list' => array('type' => 'int','precision' => 4,'nullable' => true),
				'attrib_sort' => array('type' => 'int','precision' => 4,'nullable' => true),
				'size' => array('type' => 'int','precision' => 4,'nullable' => true),
				'precision_' => array('type' => 'int','precision' => 4,'nullable' => true),
				'scale' => array('type' => 'int','precision' => 4,'nullable' => true),
				'default_value' => array('type' => 'varchar','precision' => 20,'nullable' => true),
				'nullable' => array('type' => 'varchar','precision' => 5,'nullable' => true),
				'disabled' => array('type' => 'int','precision' => 2,'nullable' => true),
				'lookup_form' => array('type' => 'int','precision' => 2,'nullable' => true),
				'custom' => array('type' => 'int','precision' => 2,'nullable' => true,'default' => 1),
				'helpmsg' => array('type' => 'text','nullable' => true)
			),
			'pk' => array('location_id', 'id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		foreach ( $attribs as $attrib )
		{
			$attrib['location_id'] = $GLOBALS['phpgw']->locations->get_id($attrib['appname'], $attrib['location']);
			unset($attrib['appname'], $attrib['location']);

			$sql = 'INSERT INTO phpgw_cust_attribute(' . implode(',',array_keys($attrib)) . ') '
				 . ' VALUES (' . $db->validate_insert($attrib) . ')';
			$db->query($sql, __LINE__, __FILE__);
		}
		
		unset($attribs);

		$choices = array();

		$sql = 'SELECT * FROM phpgw_cust_choice';
		$db->query($sql, __LINE__, __FILE__);
		while ( $db->next_record() )
		{
			$choices[] = array
			(
				'appname'		=> $db->f('appname', true),
				'location'		=> $db->f('location', true),
				'attrib_id'		=> $db->f('attrib_id'),
				'id'			=> $db->f('id'),
				'value'			=> $db->f('value', true)
			);
		}

		// New PK so drop the table
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_cust_choice');
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_cust_choice', array
		(
			'fd' => array
			(
				'location_id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'attrib_id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'value' => array('type' => 'text','nullable' => false)
			),
			'pk' => array('location_id', 'attrib_id', 'id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		foreach ( $choices as $choice )
		{
			$choice['location_id'] = $GLOBALS['phpgw']->locations->get_id($choice['appname'], $choice['location']);
			if(!$choice['location_id'])
			{
				echo "The location <b>{$choice['location']}</b> for Appname <b>{$choice['appname']}</b> is not defined<br>";
				echo "You may try to fix it manually by adding it to the table <b>phpgw_locations</b> if it is really needed (check the table <b>phpgw_cust_choicez</b>)";
				$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_abort();
				die();
			}
			unset($choice['appname'], $choice['location']);

			$sql = 'INSERT INTO phpgw_cust_choice(' . implode(',',array_keys($choice)) . ') '
				 . ' VALUES (' . $db->validate_insert($choice) . ')';
			$db->query($sql, __LINE__, __FILE__);
		}

		unset($choices);

		$functions = array();

		$sql = 'SELECT * FROM phpgw_cust_function';
		$db->query($sql, __LINE__, __FILE__);
		while ( $db->next_record() )
		{
			$functions[] = array
			(
				'appname'		=> $db->f('appname', true),
				'location'		=> $db->f('location', true),
				'id'			=> $db->f('id'),
				'descr'			=> $db->f('descr', true),
				'file_name'		=> $db->f('file_name', true),
				'active'		=> $db->f('active'),
				'custom_sort'	=> $db->f('custom_sort')
			);
		}

		// New PK so drop the table
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_cust_function');
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_cust_function', array
		(
			'fd' => array
			(
				'location_id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'descr' => array('type' => 'text','nullable' => true),
				'file_name ' => array('type' => 'varchar','precision' => 50,'nullable' => false),
				'active' => array('type' => 'int','precision' => 2,'nullable' => true),
				'custom_sort' => array('type' => 'int','precision' => 4,'nullable' => true)
			),
			'pk' => array('location_id', 'id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		foreach ( $functions as $cfunc )
		{
			$cfunc['location_id'] = $GLOBALS['phpgw']->locations->get_id($cfunc['appname'], $cfunc['location']);
			unset($cfunc['appname'], $cfunc['location']);

			$sql = 'INSERT INTO phpgw_cust_function(' . implode(',', array_keys($cfunc)) . ') '
				 . ' VALUES (' . $db->validate_insert($cfunc) . ')';
			$db->query($sql, __LINE__, __FILE__);
		}

		unset($functions);

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.517';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.517';
	/**
	* Upgrade the phpgw_locations table to mark where custom functions can be applied
	* and add missing table for user cache.
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_517()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_locations','allow_c_function',array(
			'type' => 'int',
			'precision' => '2',
			'nullable' => True
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_cache_user', array
		(
			'fd' => array
			(
				'item_key' => array('type' => 'varchar','precision' => 100,'nullable' => false),
				'user_id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'cache_data' => array('type' => 'text','nullable' => false),
				'lastmodts' => array('type' => 'int','precision' => 4,'nullable' => false)
			),
			'pk' => array('item_key'),
			'fk' => array(),
			'ix' => array('user_id', 'lastmodts'),
			'uc' => array()
		));

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.518';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.518';
	/**
	* Replace the primary key of the phpgw_cache_user table
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_518()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		// New PK so drop the table
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_cache_user');

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_cache_user', array
		(
			'fd' => array
			(
				'item_key' => array('type' => 'varchar','precision' => 100,'nullable' => false),
				'user_id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'cache_data' => array('type' => 'text','nullable' => false),
				'lastmodts' => array('type' => 'int','precision' => 4,'nullable' => false)
			),
			'pk' => array('item_key','user_id'),
			'fk' => array(),
			'ix' => array('lastmodts'),
			'uc' => array()
		));

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.519';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.519';
	/**
	* Add attribute group
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_519()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_cust_attribute','group_id',array(
			'type' => 'int',
			'precision' => '2',
			'nullable' => True,
			'default'	=> 0
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_cust_attribute_group', array
		(
			'fd' => array
			(
				'location_id'	=> array('type' => 'int','precision' => 4,'nullable' => false),
				'id'			=> array('type' => 'int','precision' => 2,'nullable' => false),
				'name'			=> array('type' => 'varchar','precision' => 100,'nullable' => false),
				'group_sort'	=> array('type' => 'int','precision' => 2,'nullable' => false),
				'descr'			=> array('type' => 'varchar','precision' => 150,'nullable' => true),
				'remark'		=> array('type' => 'text','nullable' => true)
			),
			'pk' => array('location_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("SELECT DISTINCT location_id FROM phpgw_cust_attribute");
		$locations = array();
		while ($GLOBALS['phpgw_setup']->oProc->m_odb->next_record())
		{
			$locations[] = $GLOBALS['phpgw_setup']->oProc->f('location_id');
		}

		foreach ($locations as $location_id)
		{
			$GLOBALS['phpgw_setup']->oProc->m_odb->query("INSERT INTO phpgw_cust_attribute_group (location_id, id, name, group_sort, descr)"
			." VALUES ({$location_id}, 1, 'Default group', 1, 'Auto created from db-update')", __LINE__, __FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE phpgw_cust_attribute SET group_id = 1");

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.520';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.520';
	/**
	* Add primary key to phpgw_contact_person and phpgw_config
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_520()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$db =& $GLOBALS['phpgw_setup']->oProc->m_odb;
		$persons = array();
		$sql = 'SELECT * FROM phpgw_contact_person';
		$db->query($sql, __LINE__, __FILE__);
		while ( $db->next_record() )
		{
			if($db->f('first_name'))
			{
				$persons[] = array
				(
					'person_id'			=> $db->f('person_id'),
					'first_name'		=> $db->f('first_name', true),
					'last_name'			=> $db->f('last_name', true),
					'middle_name'		=> $db->f('middle_name', true),
					'prefix'			=> $db->f('prefix', true),
					'suffix'			=> $db->f('suffix', true),
					'birthday'			=> $db->f('birthday', true),
					'pubkey'			=> $db->f('pubkey', true),
					'title'				=> $db->f('title', true),
					'department'		=> $db->f('department', true),
					'initials'			=> $db->f('initials', true),
					'sound'				=> $db->f('sound', true),
					'active'			=> $db->f('active'),
					'created_on'		=> $db->f('created_on'),
					'created_by'		=> $db->f('created_by'),
					'modified_on'		=> $db->f('modified_on'),
					'modified_by'		=> $db->f('modified_by')
				);
			}
		}

		// New PK so drop the table
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_contact_person');
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_contact_person', array(
				'fd' => array(
					'person_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'first_name' => array('type' => 'varchar','precision' => '64','nullable' => False),
					'last_name' => array('type' => 'varchar','precision' => '64','nullable' => False),
					'middle_name' => array('type' => 'varchar','precision' => '64','nullable' => True),
					'prefix' => array('type' => 'varchar','precision' => '64','nullable' => True),
					'suffix' => array('type' => 'varchar','precision' => '64','nullable' => True),
					'birthday' => array('type' => 'varchar','precision' => '32','nullable' => True),
					'pubkey' => array('type' => 'text','nullable' => True),
					'title' => array('type' => 'varchar','precision' => '64','nullable' => True),
					'department' => array('type' => 'varchar','precision' => '64','nullable' => True),
					'initials' => array('type' => 'varchar','precision' => '10','nullable' => True),
					'sound' => array('type' => 'varchar','precision' => '64','nullable' => True),
					'active' => array('type' => 'char','precision' => 1,'nullable' => True,'default' => 'Y'),
					'created_on' => array('type' => 'int','precision' => 4,'nullable' => False),
					'created_by' => array('type' => 'int','precision' => 4,'nullable' => False),
					'modified_on' => array('type' => 'int','precision' => 4,'nullable' => False),
					'modified_by' => array('type' => 'int','precision' => 4,'nullable' => False)
				),
				'pk' => array('person_id'),
				'fk' => array(),
				'ix' => array(array('first_name'),array('last_name')),
				'uc' => array()
			)
		);

		foreach ( $persons as $person )
		{
			$sql = 'INSERT INTO phpgw_contact_person(' . implode(',', array_keys($person)) . ') '
				 . ' VALUES (' . $db->validate_insert($person) . ')';
			$db->query($sql, __LINE__, __FILE__);
		}
		unset($persons);

		$config = array();
		$sql = 'SELECT * FROM phpgw_config';
		$db->query($sql, __LINE__, __FILE__);
		while ( $db->next_record() )
		{
			$config[] = array
			(
				'config_app'		=> $db->f('config_app', true),
				'config_name'		=> $db->f('config_name', true),
				'config_value'		=> $db->f('config_value', true)
			);
		}

		// New PK so drop the table
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_config');
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_config', array(
				'fd' => array(
					'config_app' => array('type' => 'varchar','precision' => 50),
					'config_name' => array('type' => 'varchar','precision' => 255,'nullable' => False),
					'config_value' => array('type' => 'text')
				),
				'pk' => array('config_app','config_name'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		foreach ( $config as $entry )
		{
			$sql = 'INSERT INTO phpgw_config(' . implode(',', array_keys($entry)) . ') '
				 . ' VALUES (' . $db->validate_insert($entry) . ')';
			$db->query($sql, __LINE__, __FILE__);
		}
		unset($config);


		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.521';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.521';
	/**
	* Allow 50 characters in column-name for custom attributes.
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_521()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_cust_attribute', 'column_name', array
		(
			'type'		=> 'varchar',
			'precision' => '50',
			'nullable'	=> false
		));


		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.522';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.522';
	/**
	* Restore support for anonymous sessions.
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_522()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('anonymous', 'allow anonymous sessions for public modules', 'phpgwapi', false);

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.523';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.523';
	/**
	* Clean out cache and session as the scheme for compress data has been altered.
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_523()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_cache_user",__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_sessions",__LINE__,__FILE__);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT config_value FROM phpgw_config WHERE config_app = 'phpgwapi' AND config_name = 'temp_dir'");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$temp_dir = $GLOBALS['phpgw_setup']->oProc->f('config_value');

		if($temp_dir && is_dir($temp_dir))
		{
			$dir = new DirectoryIterator($temp_dir); 
			if ( is_object($dir) )
			{
				foreach ( $dir as $file )
				{
					if ( $file->isDot()
						|| !$file->isFile()
						|| !$file->isReadable()
						|| !strpos($file->getbaseName(), 'hpgw_cache_') == 1)
					{
						continue;
					}
					unlink((string) "{$temp_dir}/{$file}");
				}
			}
		}

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.524';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.524';
	/**
	* support per application admin
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_524()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$apps = array();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query('SELECT app_name FROM phpgw_applications', __LINE__, __FILE__);
		while ( $GLOBALS['phpgw_setup']->oProc->next_record() )
		{
			$apps[] = $GLOBALS['phpgw_setup']->oProc->m_odb->f('app_name', true);
		}

		foreach ( $apps as $app )
		{
			$GLOBALS['phpgw']->locations->add('admin', "Allow app admins - {$app}", $app, false);
		}

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.525';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.525';
	/**
	* Add sorting to attribute choice
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_525()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_cust_choice','choice_sort',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True,
			'default'	=> 0
		));

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.526';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.526';
	/**
	* Add location_id to categories
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_526()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_categories','location_id',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True,
			'default'	=> 0
		));

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.527';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.527';
	/**
	* Add delegates - let users manage other users to represent them
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_527()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_account_delegates', array(
				'fd' => array(
					'account_id' => array('type' => 'int','precision' => 4,'nullable' => false),
					'owner_id' => array('type' => 'int','precision' => 4,'nullable' => false),
					'active_from' => array('type' => 'int', 'precision' => 4,'nullable' => true),
					'active_to' => array('type' => 'int', 'precision' => 4,'nullable' => true),
					'created_on' => array('type' => 'int', 'precision' => 4,'nullable' => false),
					'created_by' => array('type' => 'int', 'precision' => 4,'nullable' => false),
				),
				'pk' => array('account_id','owner_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.528';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.528';
	/**
	* Add delegates - let users manage other users to represent them
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_528()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_account_delegates'); // new primary key
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_account_delegates', array(
				'fd' => array(
					'delegate_id' => array('type' => 'auto','precision' => 4,'nullable' => false),
					'account_id' => array('type' => 'int','precision' => 4,'nullable' => false),
					'owner_id' => array('type' => 'int','precision' => 4,'nullable' => false),
					'location_id' => array('type' => 'int','precision' => 4,'nullable' => false),
					'data' => array('type' => 'text','nullable' => true),
					'active_from' => array('type' => 'int', 'precision' => 4,'nullable' => true),
					'active_to' => array('type' => 'int', 'precision' => 4,'nullable' => true),
					'created_on' => array('type' => 'int', 'precision' => 4,'nullable' => false),
					'created_by' => array('type' => 'int', 'precision' => 4,'nullable' => false),
				),
				'pk' => array('delegate_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()//array('account_id','owner_id','location_id','data') //FIXME - MySQL needs a length on the data-field
			)
		);

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.529';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.529';
	/**
	* Fix a ipv6 issue
	*
	* @return string the new version number
	*/

	function phpgwapi_upgrade0_9_17_529()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_access_log','ip',array('type' => 'varchar', 'precision' => 100, 'nullable' => False,'default' => '::1'));

		$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.530';
		return $GLOBALS['setup_info']['phpgwapi']['currentver'];
	}

	$test[] = '0.9.17.530';
	/**
	* Add new config schema
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_530()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_config2_section', array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'location_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
					'descr' => array('type' => 'varchar', 'precision' => 200,'nullable' => true),
					'data' => array('type' => 'text','nullable' => true)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_config2_attrib', array(
				'fd' => array(
					'section_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'input_type' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
					'descr' => array('type' => 'varchar', 'precision' => 200,'nullable' => true)
				),
				'pk' => array('section_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_config2_choice', array(
				'fd' => array(
					'section_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'attrib_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'value' => array('type' => 'varchar', 'precision' => 50,'nullable' => False)
				),
				'pk' => array('section_id','attrib_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('section_id','attrib_id','value')
			)
		);
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_config2_value', array(
				'fd' => array(
					'section_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'attrib_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'value' => array('type' => 'text','nullable' => False)
				),
				'pk' => array('section_id','attrib_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.531';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}


	$test[] = '0.9.17.531';
	/**
	* Add publishing flag to history log
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_531()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_history_log','publish',array('type' => 'int','precision' => 2,'nullable' => True));


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.532';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.532';
	/**
	* Add mod-info to acl
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_532()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_acl','modified_on',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_acl','modified_by',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True,
			'default' 	=> -1
		));

		$now = time();

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_acl SET modified_on = {$now}, modified_by = -1");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.533';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.533';
	/**
	* Add index to acl
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_533()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		if($GLOBALS['phpgw_info']['server']['db_type'] == 'postgres')
		{
			$create_index = true;
			$metadata = $GLOBALS['phpgw']->db->metaindexes('phpgw_acl');

			foreach($metadata as $index_name => $index)
			{
				if(preg_match('/^location_id/i', $index_name))
				{
					$create_index = false;
				}
			}

			if($create_index)
			{
				$GLOBALS['phpgw_setup']->oProc->query("CREATE INDEX location_id_phpgw_acl_idx ON phpgw_acl USING btree (location_id)");
				$GLOBALS['phpgw_setup']->oProc->query("REINDEX TABLE phpgw_acl");
			}
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.534';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.534';
	/**
	* Need more space for filename
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_534()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_cust_function','file_name', array('type' => 'varchar','precision' => 255,'nullable' => false));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.535';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.535';
	/**
	* Add custom attibute type that allows call to function of choice
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_535()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_cust_attribute','get_list_function', array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => true
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_cust_attribute','get_list_function_input', array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => true
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_cust_attribute','get_single_function', array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => true
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_cust_attribute','get_single_function_input', array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.536';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.536';
	/**
	* Add custom attibute type that allows call to function of choice
	*
	* @return string the new version number
	*/

	function phpgwapi_upgrade0_9_17_536()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_notification', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
					'location_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'location_item_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'contact_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'is_active' => array('type' => 'int', 'precision' => 2,'nullable' => true),
					'notification_method' => array('type' => 'varchar', 'precision' => 20,'nullable' => true),
					'user_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'entry_date' => array('type' => 'int','precision' => 4,'nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array('phpgw_contact' => array('contact_id' => 'contact_id')),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.537';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}
	$test[] = '0.9.17.537';
	/**
	* change datatype to bigint
	*
	* @return string the new version number
	*/

	function phpgwapi_upgrade0_9_17_537()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_notification','location_item_id',array(
			'type' => 'int',
			'precision' => '8',
			'nullable' => False
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.538';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}


	/**
	* change datatype to bigint
	*
	* @return string the new version number
	*/
	$test[] = '0.9.17.538';
	function phpgwapi_upgrade0_9_17_538()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_categories','active', array('type' => 'int','precision' => '2','default' => '1','nullable' => True));				
		$GLOBALS['phpgw_setup']->oProc->query('UPDATE phpgw_categories SET active = 1',__LINE__,__FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.539';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.539';
	/**
	* Add custom attibute type that allows attribute used as part of short description
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_539()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_cust_attribute','short_description', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true
		));


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.540';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.540';
	/**
	* Add custom attibute type that allows attribute used as part of short description
	* Add support for clien-side custom functions
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_540()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_cust_attribute','input_text',array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => false,
		));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_cust_attribute','statustext',array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => false,
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.541';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.541';
	/**
	* Allow groups within groups
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_541()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_cust_attribute_group','parent_id', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));


		//otherwise: server-side
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_cust_function','client_side', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.542';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}


	$test[] = '0.9.17.542';
	/**
	* Enable external integration with vfs
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_542()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_vfs','external_id', array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.543';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

	$test[] = '0.9.17.543';
	/**
	* Enable external integration with vfs
	*
	* @return string the new version number
	*/
	function phpgwapi_upgrade0_9_17_543()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('vfs_filedata', 'config section for VFS filedata - file backend', 'admin', false);

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_vfs_filedata',array(
			'fd' => array(
				'file_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'location_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'metadata' => array('type' => 'xml','nullable' => False),
			),
			'pk' => array('file_id'),
			'fk' => array('phpgw_vfs' => array('file_id' => 'file_id')),
			'ix' => array(),
			'uc' => array()
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['phpgwapi']['currentver'] = '0.9.17.544';
			return $GLOBALS['setup_info']['phpgwapi']['currentver'];
		}
	}

