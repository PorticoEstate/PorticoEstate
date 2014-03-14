<?php
	/**
	* Setup
	* @copyright Copyright (C) 2003-2010 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage setup
	* @version $Id$
	* @internal $Source$
	*/

	$phpgw_baseline = array(
		'phpgw_config' => array(
			'fd' => array(
				'config_app' => array('type' => 'varchar','precision' => 50),
				'config_name' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'config_value' => array('type' => 'text')
			),
			'pk' => array('config_app','config_name'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_applications' => array(
			'fd' => array(
				'app_id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'app_name' => array('type' => 'varchar','precision' => 25,'nullable' => False),
				'app_enabled' => array('type' => 'int','precision' => 4,'nullable' => False),
				'app_order' => array('type' => 'int','precision' => 4,'nullable' => False),
				'app_tables' => array('type' => 'text','nullable' => False),
				'app_version' => array('type' => 'varchar','precision' => 20,'nullable' => False,'default' => '0.0')
			),
			'pk' => array('app_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('app_name')
		),
		'phpgw_acl' => array(
			'fd' => array(
				'acl_account' => array('type' => 'int', 'precision' => 4),
				'acl_rights' => array('type' => 'int', 'precision' => 4),
				'acl_grantor' => array('type' => 'int', 'precision' => 4, 'nullable' => true, 'default' => '-1'),
				'acl_type' => array('type' => 'int', 'precision' => 2, 'nullable' => true, 'default' => '0'),
				'location_id' => array('type' => 'int', 'precision' => 4),
				'modified_on' => array('type' => 'int','precision' => 4,'nullable' => False),
				'modified_by' => array('type' => 'int','precision' => 4,'nullable' => False, 'default' => '-1')
			),
			'pk' => array(),
			'ix' => array('location_id','acl_account'),
			'fk' => array(),
			'uc' => array()
		),
		'phpgw_accounts' => array(
			'fd' => array(
				'account_id' => array('type' => 'auto','nullable' => False),
				'account_lid' => array('type' => 'varchar','precision' => 25,'nullable' => False),
				'account_pwd' => array('type' => 'varchar','precision' => '40','nullable' => False),
				'account_firstname' => array('type' => 'varchar','precision' => 50,'nullable' => False),
				'account_lastname' => array('type' => 'varchar','precision' => 50,'nullable' => False),
				'account_permissions' => array('type' => 'text','nullable' => True),
				'account_groups' => array('type' => 'varchar','precision' => 30,'nullable' => True),
				'account_lastlogin' => array('type' => 'int','precision' => 4,'nullable' => True),
				'account_lastloginfrom' => array('type' => 'varchar','precision' => 255,'nullable' => True),
				'account_lastpwd_change' => array('type' => 'int','precision' => 4,'nullable' => True),
				'account_status' => array('type' => 'char','precision' => 1,'nullable' => False,'default' => 'A'),
				'account_expires' => array('type' => 'int','precision' => 4,'nullable' => False),
				'account_type' => array('type' => 'char','precision' => 1,'nullable' => True),
				'person_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'account_quota' => array('type' => 'int','precision' => 4,'default' => '-1','nullable' => True)
			),
			'pk' => array('account_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('account_lid')
		),
		'phpgw_account_delegates' => array(
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
		),
		'phpgw_preferences' => array(
			'fd' => array(
				'preference_owner' => array('type' => 'int','precision' => 4,'nullable' => False),
				'preference_app' => array('type' => 'varchar','precision' => 25,'nullable' => False),
				'preference_value' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('preference_owner','preference_app'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sessions' => array(
			'fd' => array(
				'session_id' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'ip' => array('type' => 'varchar','precision' => 100),
				'data' => array('type' => 'longtext'),
				'lastmodts' => array('type' => 'int','precision' => 4),
			),
			'pk' => array('session_id'),
			'fk' => array(),
			'ix' => array('lastmodts'),
			'uc' => array()
		),
		'phpgw_cache_user' => array(
			'fd' => array(
				'item_key' => array('type' => 'varchar','precision' => 100,'nullable' => false),
				'user_id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'cache_data' => array('type' => 'text','nullable' => false),
				'lastmodts' => array('type' => 'int','precision' => 4,'nullable' => false)
			),
			'pk' => array('item_key','user_id'),
			'fk' => array(),
			'ix' => array('lastmodts'),
			'uc' => array()
		),
		'phpgw_access_log' => array(
			'fd' => array(
				'sessionid' => array('type' => 'char','precision' => '32','nullable' => False),
				'loginid' => array('type' => 'varchar','precision' => 30,'nullable' => False),
				'ip' => array('type' => 'varchar','precision' => 100,'nullable' => False,'default' => '::1'),
				'li' => array('type' => 'int','precision' => 4,'nullable' => False),
				'lo' => array('type' => 'int','precision' => 4,'nullable' => True,'default' => '0'),
				'account_id' => array('type' => 'int','precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_group_map'	=> array
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
		),
		'phpgw_hooks' => array(
			'fd' => array(
				'hook_id' => array('type' => 'auto','nullable' => False),
				'hook_appname' => array('type' => 'varchar','precision' => 255),
				'hook_location' => array('type' => 'varchar','precision' => 255),
				'hook_filename' => array('type' => 'varchar','precision' => 255)
			),
			'pk' => array('hook_id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'phpgw_languages' => array(
			'fd' => array(
				'lang_id' => array('type' => 'varchar','precision' => 2,'nullable' => False),
				'lang_name' => array('type' => 'varchar','precision' => 50,'nullable' => False),
				'available' => array('type' => 'char','precision' => '3','nullable' => False,'default' => 'No')
			),
			'pk' => array('lang_id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'phpgw_lang' => array(
			'fd' => array(
				'message_id' => array('type' => 'varchar','precision' => 255,'nullable' => False,'default' => ''),
				'app_name' => array('type' => 'varchar','precision' => 25,'nullable' => False,'default' => 'common'),
				'lang' => array('type' => 'varchar','precision' => 5,'nullable' => False,'default' => ''),
				'content' => array('type' => 'text')
			),
			'pk' => array('message_id','app_name','lang'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_locations' => array(
			'fd' => array(
				'location_id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'app_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'name' => array('type' => 'varchar','precision' => 50,'nullable' => False),
				'descr' => array('type' => 'varchar','precision' => 100,'nullable' => False),
				'allow_grant' => array('type' => 'int','precision' => 2,'nullable' => True),
				'allow_c_attrib' => array('type' => 'int','precision' => 2,'nullable' => True),
				'c_attrib_table' => array('type' => 'varchar','precision' => 25,'nullable' => True),
				'allow_c_function' => array('type' => 'int','precision' => 2,'nullable' => True)
			),
			'pk' => array('location_id'),
			'fk' => array(),
			'ix' => array('app_id', 'name'),
			'uc' => array()
		),
		'phpgw_nextid' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'appname' => array('type' => 'varchar','precision' => 25,'nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('appname')
		),
		'phpgw_categories' => array(
			'fd' => array(
				'cat_id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'cat_main' => array('type' => 'int','precision' => 4,'default' => '0','nullable' => False),
				'cat_parent' => array('type' => 'int','precision' => 4,'default' => '0','nullable' => False),
				'cat_level' => array('type' => 'int','precision' => 2,'default' => '0','nullable' => False),
				'cat_owner' => array('type' => 'int','precision' => 4,'default' => '0','nullable' => False),
				'cat_access' => array('type' => 'varchar','precision' => '7'),
				'cat_appname' => array('type' => 'varchar','precision' => 50,'nullable' => False),
				'cat_name' => array('type' => 'varchar','precision' => '150','nullable' => False),
				'cat_description' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'cat_data' => array('type' => 'text'),
				'last_mod' => array('type' => 'int','precision' => 4,'default' => '0','nullable' => False),
				'location_id' => array('type' => 'int','precision' => 4,'default' => '0','nullable' => True),
				'active' => array('type' => 'int','precision' => '2','default' => '1','nullable' => True),
			),
			'pk' => array('cat_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_log' => array(
			'fd' => array(
				'log_id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'log_date' => array('type' => 'timestamp','nullable' => False),
				'log_account_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'log_account_lid' => array('type' => 'varchar','precision' => 25,'nullable' => False),
				'log_app' => array('type' => 'varchar','precision' => 25,'nullable' => False),
				'log_severity' => array('type' => 'char','precision' => 1,'nullable' => False),
				'log_file' => array('type' => 'varchar','precision' => 255,'nullable' => False,'default' => ''),
				'log_line' => array('type' => 'int','precision' => 4,'nullable' => False,'default' => '0'),
				'log_msg' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('log_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_interserv' => array(
			'fd' => array(
				'server_id' => array('type' => 'auto','nullable' => False),
				'server_name' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'server_host' => array('type' => 'varchar','precision' => 255,'nullable' => True),
				'server_url' => array('type' => 'varchar','precision' => 255,'nullable' => True),
				'trust_level' => array('type' => 'int','precision' => 4),
				'trust_rel' => array('type' => 'int','precision' => 4),
				'username' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'password' => array('type' => 'varchar','precision' => 255,'nullable' => True),
				'admin_name' => array('type' => 'varchar','precision' => 255,'nullable' => True),
				'admin_email' => array('type' => 'varchar','precision' => 255,'nullable' => True),
				'server_mode' => array('type' => 'varchar','precision' => '16','nullable' => False,'default' => 'xmlrpc'),
				'server_security' => array('type' => 'varchar','precision' => '16','nullable' => True)
			),
			'pk' => array('server_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_vfs' => array(
			'fd' => array(
				'file_id' => array('type' => 'auto','nullable' => False),
				'owner_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'createdby_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'modifiedby_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'created' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp'),
				'modified' => array('type' => 'timestamp','nullable' => True),
				'size' => array('type' => 'int','precision' => 4,'nullable' => True),
				'mime_type' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'deleteable' => array('type' => 'char','precision' => 1,'nullable' => True,'default' => 'Y'),
				'comment' => array('type' => 'text','nullable' => True),
				'app' => array('type' => 'varchar','precision' => 25,'nullable' => True),
				'directory' => array('type' => 'text','nullable' => True),
				'name' => array('type' => 'text','nullable' => False),
				'link_directory' => array('type' => 'text','nullable' => True),
				'link_name' => array('type' => 'text','nullable' => True),
				'version' => array('type' => 'varchar','precision' => 30,'nullable' => False,'default' => '0.0.0.0'),
				'content' => array('type' => 'text','nullable' => True),
				'external_id' => array('type' => 'int','precision' => 8,'nullable' => True),
			),
			'pk' => array('file_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_vfs_filedata' => array(
			'fd' => array(
				'file_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'location_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'metadata' => array('type' => 'xml','nullable' => False),
			),
			'pk' => array('file_id'),
			'fk' => array('phpgw_vfs' => array('file_id' => 'file_id')),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_history_log' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'history_record_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'app_id' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'history_owner' => array('type' => 'int','precision' => 4,'nullable' => False),
				'history_status' => array('type' => 'char','precision' => 2,'nullable' => False),
				'history_new_value' => array('type' => 'text','nullable' => False),
				'history_timestamp' => array('type' => 'timestamp','nullable' => False),
				'history_old_value' => array('type' => 'text','nullable' => False),
				'location_id'	=> array('type' => 'int', 'precision' => 4, 'nullable' => true)
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_interlink'	=> array
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
		),
		'phpgw_async' => array(
			'fd' => array(
				'id' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'next' => array('type' => 'int','precision' => 4,'nullable' => False),
				'times' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'method' => array('type' => 'varchar','precision' => '80','nullable' => False),
				'data' => array('type' => 'text','nullable' => False),
				'account_id' => array('type' => 'int','precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_contact' => array(
			'fd' => array(
				'contact_id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'owner' => array('type' => 'int','precision' => 4,'nullable' => False),
				'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
				'cat_id' => array('type' => 'varchar','precision' => 200,'nullable' => True),
				'contact_type_id' => array('type' => 'int','precision' => 4,'nullable' => False)
			),
			'pk' => array('contact_id'),
			'fk' => array(),
			'ix' => array('owner','access','contact_type_id'),
			'uc' => array()
		),
		'phpgw_contact_person' => array(
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
		),
		'phpgw_contact_org' => array(
			'fd' => array(
				'org_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'name' => array('type' => 'varchar','precision' => '80','nullable' => False),
				'active' => array('type' => 'char','precision' => 1,'nullable' => True,'default' => 'Y'),
				'parent' => array('type' => 'int','precision' => 4,'nullable' => True),
				'created_on' => array('type' => 'int','precision' => 4,'nullable' => False),
				'created_by' => array('type' => 'int','precision' => 4,'nullable' => False),
				'modified_on' => array('type' => 'int','precision' => 4,'nullable' => False),
				'modified_by' => array('type' => 'int','precision' => 4,'nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array('org_id','active'),
			'uc' => array()
		),
		'phpgw_contact_org_person' => array(
			'fd' => array(
				'org_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'person_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'addr_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'preferred' => array('type' => 'char','precision' => 1,'nullable' => False,'default' => 'N'),
				'created_on' => array('type' => 'int','precision' => 4,'nullable' => False),
				'created_by' => array('type' => 'int','precision' => 4,'nullable' => False)
			),
			'pk' => array('org_id','person_id'),
			'fk' => array(),
			'ix' => array('addr_id','person_id','org_id','preferred'),
			'uc' => array()
		),
		'phpgw_contact_addr' => array(
			'fd' => array(
				'contact_addr_id' => array('type' => 'auto','nullable' => False),
				'contact_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'addr_type_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'add1' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'add2' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'add3' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'city' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'state' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'postal_code' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'country' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'tz' => array('type' => 'varchar','precision' => '40','nullable' => True),
				'preferred' => array('type' => 'char','precision' => 1,'nullable' => False,'default' => 'N'),
				'created_on' => array('type' => 'int','precision' => 4,'nullable' => False),
				'created_by' => array('type' => 'int','precision' => 4,'nullable' => False),
				'modified_on' => array('type' => 'int','precision' => 4,'nullable' => False),
				'modified_by' => array('type' => 'int','precision' => 4,'nullable' => False)
			),
			'pk' => array('contact_addr_id'),
			'fk' => array(),
			'ix' => array('contact_id','addr_type_id','preferred'),
			'uc' => array()
		),
		'phpgw_contact_note' => array(
			'fd' => array(
				'contact_note_id' => array('type' => 'auto','nullable' => False),
				'contact_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'note_type_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'note_text' => array('type' => 'text','nullable' => False),
				'created_on' => array('type' => 'int','precision' => 4,'nullable' => False),
				'created_by' => array('type' => 'int','precision' => 4,'nullable' => False),
				'modified_on' => array('type' => 'int','precision' => 4,'nullable' => False),
				'modified_by' => array('type' => 'int','precision' => 4,'nullable' => False)
			),
			'pk' => array('contact_note_id'),
			'fk' => array(),
			'ix' => array('contact_id','note_type_id'),
			'uc' => array()
		),
		'phpgw_contact_others' => array(
			'fd' => array(
				'other_id' => array('type' => 'auto','nullable' => False),
				'contact_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'contact_owner' => array('type' => 'int','precision' => 4,'nullable' => False),
				'other_name' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'other_value' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('other_id'),
			'fk' => array(),
			'ix' => array('contact_id','contact_owner','other_name'),
			'uc' => array()
		),
		'phpgw_contact_comm' => array(
			'fd' => array(
				'comm_id' => array('type' => 'auto','nullable' => False),
				'contact_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'comm_descr_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'preferred' => array('type' => 'char','precision' => 1,'nullable' => False,'default' => 'N'),
				'comm_data' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'created_on' => array('type' => 'int','precision' => 4,'nullable' => False),
				'created_by' => array('type' => 'int','precision' => 4,'nullable' => False),
				'modified_on' => array('type' => 'int','precision' => 4,'nullable' => False),
				'modified_by' => array('type' => 'int','precision' => 4,'nullable' => False)
			),
			'pk' => array('comm_id'),
			'fk' => array(),
			'ix' => array('comm_data','preferred','comm_descr_id','contact_id'),
			'uc' => array()
		),
		'phpgw_contact_comm_descr' => array(
			'fd' => array(
				'comm_descr_id' => array('type' => 'auto','nullable' => False),
				'comm_type_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'descr' => array('type' => 'varchar','precision' => 50,'nullable' => True)
			),
			'pk' => array('comm_descr_id'),
			'fk' => array(),
			'ix' => array('descr','comm_type_id'),
			'uc' => array()
		),
		'phpgw_contact_comm_type' => array(
			'fd' => array(
				'comm_type_id' => array('type' => 'auto','nullable' => False),
				'type' => array('type' => 'varchar','precision' => 50,'nullable' => True),
				'active' => array('type' => 'varchar','precision' => 30,'nullable' => True),
				'class' => array('type' => 'varchar','precision' => 30,'nullable' => True)
			),
			'pk' => array('comm_type_id'),
			'fk' => array(),
			'ix' => array('type','active','class'),
			'uc' => array()
		),
		'phpgw_contact_types' => array(
			'fd' => array(
				'contact_type_id' => array('type' => 'auto','nullable' => False),
				'contact_type_descr' => array('type' => 'varchar','precision' => 50,'nullable' => True),
				'contact_type_table' => array('type' => 'varchar','precision' => 50,'nullable' => True)
			),
			'pk' => array('contact_type_id'),
			'fk' => array(),
			'ix' => array('contact_type_descr'),
			'uc' => array()
		),
		'phpgw_contact_addr_type' => array(
			'fd' => array(
				'addr_type_id' => array('type' => 'auto','nullable' => False),
				'description' => array('type' => 'varchar','precision' => 50,'nullable' => False)
			),
			'pk' => array('addr_type_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_contact_note_type' => array(
			'fd' => array(
				'note_type_id' => array('type' => 'auto','nullable' => False),
				'description' => array('type' => 'varchar','precision' => 30,'nullable' => False)
			),
			'pk' => array('note_type_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_cust_attribute_group' => array
		(
			'fd' => array
			(
				'location_id'	=> array('type' => 'int','precision' => 4,'nullable' => false),
				'id'			=> array('type' => 'int','precision' => 4,'nullable' => false),
				'parent_id'			=> array('type' => 'int','precision' => 4,'nullable' => true),
				'name'			=> array('type' => 'varchar','precision' => 100,'nullable' => false),
				'group_sort'	=> array('type' => 'int','precision' => 2,'nullable' => false),
				'descr'			=> array('type' => 'varchar','precision' => 150,'nullable' => true),
				'remark'		=> array('type' => 'text','nullable' => true)
			),
			'pk' => array('location_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_cust_attribute' => array
		(
			'fd' => array
			(
				'location_id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'group_id' => array('type' => 'int','precision' => 4,'nullable' => true, 'default' => 0),
				'id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'column_name' => array('type' => 'varchar','precision' => 50,'nullable' => false),
				'input_text' => array('type' => 'varchar','precision' => 255,'nullable' => false),
				'statustext' => array('type' => 'varchar','precision' => '255','nullable' => false),
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
				'helpmsg' => array('type' => 'text','nullable' => true),
				'get_list_function' => array('type' => 'varchar','precision' => 255,'nullable' => true),
				'get_list_function_input' => array('type' => 'varchar','precision' => 255,'nullable' => true),
				'get_single_function' => array('type' => 'varchar','precision' => 255,'nullable' => true),
				'get_single_function_input' => array('type' => 'varchar','precision' => 255,'nullable' => true),
				'short_description' => array('type' => 'int','precision' => 2,'nullable' => true)
			),
			'pk' => array('location_id', 'id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_cust_choice' => array
		(
			'fd' => array
			(
				'location_id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'attrib_id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'value' => array('type' => 'text','nullable' => false),
				'choice_sort' => array('type' => 'int','precision' => 4,'nullable' => false, 'default' => 0)
			),
			'pk' => array('location_id', 'attrib_id', 'id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_cust_function' => array
		(
			'fd' => array
			(
				'location_id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'id' => array('type' => 'int','precision' => 4,'nullable' => false),
				'descr' => array('type' => 'text','nullable' => true),
				'file_name' => array('type' => 'varchar','precision' => 255,'nullable' => false),
				'active' => array('type' => 'int','precision' => 2,'nullable' => true),
				'client_side' => array('type' => 'int','precision' => 2,'nullable' => true),//otherwise: server-side
				'custom_sort' => array('type' => 'int','precision' => 4,'nullable' => true)
			),
			'pk' => array('location_id', 'id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_mapping' => array(
			'fd' => array(
				'ext_user' => array('type' => 'varchar','precision' => 100,'nullable' => False),
				'auth_type' => array('type' => 'varchar','precision' => 25,'nullable' => False),
				'status' => array('type' => 'char','precision' => 1,'nullable' => False,'default' => 'A'),
				'location' => array('type' => 'varchar','precision' => 200,'nullable' => False),
				'account_lid' => array('type' => 'varchar','precision' => 25,'nullable' => False)
			),
			'pk' => array('ext_user','location','auth_type'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_mail_handler' => array(
			'fd' => array(
				'handler_id' => array('type' => 'auto','nullable' => False),
				'target_email' => array('type' => 'varchar','precision' => 75,'nullable' => False),
				'handler' => array('type' => 'varchar','precision' => 50,'nullable' => False),
				'is_active' => array('type' => 'int','precision' => 4,'nullable' => False),
				'lastmod' => array('type' => 'int','precision' => 8,'nullable' => False),
				'lastmod_user' => array('type' => 'int','precision' => 8,'nullable' => False)
			),
			'pk' => array('handler_id'),
			'fk' => array(),
			'ix' => array('target_email','is_active'),
			'uc' => array()
		),
		'phpgw_config2_section' => array(
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
		),
		'phpgw_config2_attrib' => array(
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
		),
		'phpgw_config2_choice' => array(
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
		),
		'phpgw_config2_value' => array(
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
		),
		'phpgw_notification' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'location_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'location_item_id' => array('type' => 'int','precision' => 8,'nullable' => False),//bigint
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
