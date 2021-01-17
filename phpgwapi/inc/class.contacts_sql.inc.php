<?php
	/**
	 * View and manipulate contact records using SQL
	 * @author Jonathan Alberto Rivera Gomez <jarg@co.com.mx>
	 * @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgwapi
	 * @subpackage contacts
	 * @version $Id$
	 * @internal Development of this application was funded by http://www.sogrp.com
	 */
	/**
	 * Include SQL Builder
	 * @see sql_builder
	 */
	phpgw::import_class('phpgwapi.sql_builder');

	/**
	 * Include SQL criteria
	 * @see phpgwapi_sql_criteria
	 */
	phpgw::import_class('phpgwapi.sql_criteria');

	/**
	 * Include SQL entity
	 * @see sql_entity
	 */
	phpgw::import_class('phpgwapi.sql_entity');

	/**
	 * All categories
	 */
	define('PHPGW_CONTACTS_CATEGORIES_ALL', -3);

	/**
	 * All records
	 */
	define('PHPGW_CONTACTS_ALL', 1);

	/**
	 * Only current user's records
	 */
	define('PHPGW_CONTACTS_MINE', 2);

	/**
	 * Only current user's private records
	 */
	define('PHPGW_CONTACTS_PRIVATE', 3);

	/**
	 * Contact Management System
	 *
	 * This class provides a contact database scheme.
	 * It attempts to be based on the vcard 2.1 standard,
	 * with mods as needed to make for more reasonable sql storage.
	 * Note that changes here must also work in the LDAP version.
	 * @package phpgwapi
	 * @subpackage contacts
	 * @internal example: $contacts = createObject('phpgwapi.contacts');
	 */
	class contacts_ extends sql_builder
	{

		var $db = '';
		var $account_id = 0;
		var $total_records = 0;
		var $grants;
		protected $global_lock = false;
		protected $local_lock = false;

		/**
		 * All exporteds fields
		 *
		 * We used to have a simple array that told us what the false fields were.
		 * this used to be its name.
		 * Now, what this holds is a reference to the $contact_fields['showable'] array that
		 * tells us what false fields can we show and their translation to english
		 * @deprecated New Apps must use new api
		 */
		var $stock_contact_fields;

		/**
		 * This is an array of three elements that are arrays themselves.
		 *
		 * The purpose of the whole thing is to give us all the fields we can manage.
		 * This includes old false fields (deprecated), and all of the new data we can manage.
		 * Most importantly, this tree categorizes the said elements this way:
		 * 1. contact_fields['showable'] are fields we can show in the index page, selectable
		 * from the preferences page...etc.
		 * 2. contact_fields['retreivable'] the rest of the fields that we generaly dont show in
		 * the addressbook ui.
		 * 3. contact_fields['catalogs'] fields that belong to catalogs and, as such, we never
		 * show in raw form as they correspond to the database but we need to query for them.
		 *
		 * All of the array data points to the english translation of the field that should then
		 * be langed to get a label for the field
		 * @access private
		 */
		var $contact_fields = array('showable' =>
			array(
				'per_full_name' => 'full name',
				'per_first_name' => 'first name',
				'per_last_name' => 'last name',
				'per_middle_name' => 'middle name',
				'per_initials' => 'initials',
				'per_prefix' => 'prefix',
				'per_suffix' => 'suffix',
				'per_birthday' => 'birthday',
				'per_pubkey' => 'public key',
				'per_title' => 'title',
				'per_department' => 'department',
				'per_sound' => 'sound',
				'per_active' => 'active',
				//		'per_name'		=> 'full name',
				//		'sel_cat_id'		=> 'categories',
				'org_name' => 'company name',
				'org_active' => 'active company',
				'org_parent' => 'parent organization',
				'addr_type' => 'address type',
				'addr_add1' => 'street first',
				'addr_add2' => 'street second',
				'addr_city' => 'city',
				'addr_state' => 'state',
				'addr_postal_code' => 'postal code',
				'addr_country' => 'country'
			),
			//		'addr_address'		=> 'street address',
			'retrievable' =>
			array(
				//deprecated start
				'adr_one_street' => 'business street',
				'adr_one_locality' => 'business city',
				'adr_one_region' => 'business state',
				'adr_one_postalcode' => 'business zip code',
				'adr_one_countryname' => 'business country',
				'adr_one_type' => 'business address type',
				'adr_two_street' => 'home street',
				'adr_two_locality' => 'home city',
				'adr_two_region' => 'home state',
				'adr_two_postalcode' => 'home zip code',
				'adr_two_countryname' => 'home country',
				'adr_two_type' => 'home address type',
				'tz' => 'time zone',
				'geo' => 'geo',
				'fn' => 'full name',
				'sound' => 'Sound',
				'org_name' => 'company name',
				'org_unit' => 'department',
				'title' => 'title',
				'n_prefix' => 'prefix',
				'n_given' => 'first name',
				'n_middle' => 'middle name',
				'n_family' => 'last name',
				'n_suffix' => 'suffix',
				'label' => 'label',
				'note' => 'notes',
				'email' => 'business email',
				'email_type' => 'business email type',
				'email_home' => 'home email',
				'email_home_type' => 'home email type',
				'address2' => 'address line 2',
				'address3' => 'address line 3',
				'tel_home' => 'home phone',
				'tel_voice' => 'voice phone',
				'tel_msg' => 'message phone',
				'tel_fax' => 'fax',
				'tel_pager' => 'pager',
				'tel_cell' => 'mobile phone',
				'tel_bbs' => 'bbs phone',
				'tel_modem' => 'modem phone',
				'tel_isdn' => 'isdn phone',
				'tel_car' => 'car phone',
				'tel_video' => 'video phone',
				'tel_prefer' => 'preferred phone',
				'ophone' => 'Other Phone',
				'bday' => 'birthday',
				'url' => 'url',
				'pubkey' => 'public key',
				'tel_work' => 'business phone',
				//deprecated end
				'addr_preferred' => 'preffered address',
				'owner' => 'owner',
				'access' => 'access',
				'addr_pref_val' => 'preferred address value',
				'other_name' => 'custom name',
				'other_value' => 'custom value',
				'other_owner' => 'custom owner',
				'other_count_row' => 'custom number',
				'org_creaton' => 'created on',
				'org_creatby' => 'created by',
				'org_modon' => 'modified on',
				'org_modby' => 'modified by',
				'count_orgs' => 'number of organizations',
				//'account_id'		=> 'account id',
				'count_persons' => 'number of persons',
				'contact_id' => 'contact id',
				'cat_id' => 'category id',
				'count_contacts' => 'number of contacts',
				'org_id' => 'organization id',
				'person_id' => 'person id',
				'my_org_id' => 'private organization id',
				'my_person_id' => 'private person id',
				'my_addr_id' => 'private address id',
				'my_preferred' => 'private preferred address',
				'my_creaton' => 'private created on',
				'my_creatby' => 'private created by',
				'key_addr_id' => 'address id',
				'addr_creaton' => 'created on',
				'addr_creatby' => 'created by',
				'addr_modon' => 'modified on',
				'addr_modby' => 'modified by',
				'addr_contact_id' => 'address contact id',
				'key_other_id' => 'custom id',
				'other_contact_id' => 'custom contact id',
				'addr_type_id' => 'address type id',
				//'account_id'		=> 'account id',
				//'account_person_id'	=> 'account person id',
				'person_only' => 'only persons',
				'per_active' => 'active person',
				'per_creaton' => 'created on',
				'per_creatby' => 'created by',
				'per_modon' => 'modified on',
				'per_modby' => 'modified by',
				'key_cat_id' => 'category id'
			),
			'catalogs' =>
			array(
				'comm_descr' => 'communication type description',
				'comm_preferred' => 'communication type preferred',
				'comm_data' => 'communication type data',
				'contact_type_descr' => 'contact type description',
				'contact_type_table' => 'contact type table',
				'comm_description' => 'communication media description',
				'comm_type' => 'communication media type',
				'comm_find_descr' => 'find communication media description',
				'comm_type_description' => 'communication media type description',
				'comm_active' => 'active communication media type',
				'comm_class' => 'communication media class',
				'addr_description' => 'address type description',
				'comm_creaton' => 'created on',
				'comm_creatby' => 'created by',
				'comm_modon' => 'modified on',
				'comm_modby' => 'modified by',
				'contact_type_id' => 'contact type id',
				'comm_descr_id' => 'communication media description id',
				'comm_contact_id' => 'communication type contact id',
				'comm_type_id' => 'communication media type id',
				'key_comm_id' => 'communication type id',
				'note_type_id' => 'note type id',
				'note_type' => 'note type',
				'note_text' => 'note text',
				'note_description' => 'note type description',
				'key_note_id' => 'note id',
				'note_contact_id' => 'note contact id',
				'note_creaton' => 'created on',
				'note_creatby' => 'created by',
				'note_modon' => 'modified on',
				'note_modby' => 'modified by',
				'contact_type' => 'contact type'
			)
		);

		/**
		 * Maps database columns to contact classes
		 * @access private
		 */
		var $map = array('contact_id' => array('phpgwapi.contact_central', '0'),
			'owner' => array('phpgwapi.contact_central', '0'),
			'access' => array('phpgwapi.contact_central', '0'),
			'cat_id' => array('phpgwapi.contact_central', '0'),
			'contact_type' => array('phpgwapi.contact_central', '0'),
			'count_contacts' => array('phpgwapi.contact_central', '0'),
			'max_contacts' => array('phpgwapi.contact_central', '0'),
			'sel_cat_id' => array('phpgwapi.contact_central', '0'),
			'organizations_contact' => array('phpgwapi.contact_central', '0'),
			'people_contact' => array('phpgwapi.contact_central', '0'),
			'org_id' => array('phpgwapi.contact_org', '1'),
			'org_name' => array('phpgwapi.contact_org', '1'),
			'org_active' => array('phpgwapi.contact_org', '1'),
			'org_parent' => array('phpgwapi.contact_org', '1'),
			'org_creaton' => array('phpgwapi.contact_org', '1'),
			'org_creatby' => array('phpgwapi.contact_org', '1'),
			'org_modon' => array('phpgwapi.contact_org', '1'),
			'org_modby' => array('phpgwapi.contact_org', '1'),
			'name' => array('phpgwapi.contact_org', '1'),
			'count_orgs' => array('phpgwapi.contact_org', '1'),
			'organizations' => array('phpgwapi.contact_org', '1', array('organizations_person',
					'organizations_org_person', 'organizations_contact')),
			'orgs_local' => array('phpgwapi.contact_org', '1'),
			'people_org' => array('phpgwapi.contact_org', '1', '', 3),
			'person_id' => array('phpgwapi.contact_person', '1'),
			'per_full_name' => array('phpgwapi.contact_person', '1'),
			'per_first_name' => array('phpgwapi.contact_person', '1'),
			'per_last_name' => array('phpgwapi.contact_person', '1'),
			'per_middle_name' => array('phpgwapi.contact_person', '1'),
			'per_prefix' => array('phpgwapi.contact_person', '1'),
			'per_suffix' => array('phpgwapi.contact_person', '1'),
			'per_birthday' => array('phpgwapi.contact_person', '1'),
			'per_pubkey' => array('phpgwapi.contact_person', '1'),
			'per_title' => array('phpgwapi.contact_person', '1'),
			'per_department' => array('phpgwapi.contact_person', '1'),
			'per_initials' => array('phpgwapi.contact_person', '1'),
			'per_sound' => array('phpgwapi.contact_person', '1'),
			'per_active' => array('phpgwapi.contact_person', '1'),
			'per_creaton' => array('phpgwapi.contact_person', '1'),
			'per_creatby' => array('phpgwapi.contact_person', '1'),
			'per_modon' => array('phpgwapi.contact_person', '1'),
			'per_modby' => array('phpgwapi.contact_person', '1'),
			'per_name' => array('phpgwapi.contact_person', '1'),
			//'account_id'		=> array('phpgwapi.contact_person', '1'),
			'count_persons' => array('phpgwapi.contact_person', '1'),
			'people' => array('phpgwapi.contact_person', '1', array('people_org', 'people_org_person',
					'people_contact')),
			'people_local' => array('phpgwapi.contact_person', '1'),
			'organizations_person' => array('phpgwapi.contact_person', '1', '', 3),
			//deprecated start
			'fn' => array('phpgwapi.contact_person', '1'),
			'n_given' => array('phpgwapi.contact_person', '1'),
			'n_family' => array('phpgwapi.contact_person', '1'),
			'n_middle' => array('phpgwapi.contact_person', '1'),
			'n_prefix' => array('phpgwapi.contact_person', '1'),
			'n_suffix' => array('phpgwapi.contact_person', '1'),
			'sound' => array('phpgwapi.contact_person', '1'),
			'bday' => array('phpgwapi.contact_person', '1'),
			'note' => array('phpgwapi.contact_person', '1'),
			'tz' => array('phpgwapi.contact_person', '1'),
			'geo' => array('phpgwapi.contact_person', '1'),
			'url' => array('phpgwapi.contact_person', '1'),
			'pubkey' => array('phpgwapi.contact_person', '1'),
			'org_unit' => array('phpgwapi.contact_person', '1'),
			'title' => array('phpgwapi.contact_person', '1'),
			//deprecated end
			'my_org_id' => array('phpgwapi.contact_org_person', '2'),
			'my_person_id' => array('phpgwapi.contact_org_person', '2'),
			'my_addr_id' => array('phpgwapi.contact_org_person', '2'),
			'my_preferred' => array('phpgwapi.contact_org_person', '2'),
			'my_creaton' => array('phpgwapi.contact_org_person', '2'),
			'my_creatby' => array('phpgwapi.contact_org_person', '2'),
			'people_org_person' => array('phpgwapi.contact_org_person', '2'),
			'organizations_org_person' => array('phpgwapi.contact_org_person', '2'),
			'key_addr_id' => array('phpgwapi.contact_addr', '3'),
			'addr_contact_id' => array('phpgwapi.contact_addr', '3'),
			'addr_type' => array('phpgwapi.contact_addr', '3'),
			'addr_add1' => array('phpgwapi.contact_addr', '3'),
			'addr_add2' => array('phpgwapi.contact_addr', '3'),
			'addr_add3' => array('phpgwapi.contact_addr', '3'),
			'addr_city' => array('phpgwapi.contact_addr', '3'),
			'addr_state' => array('phpgwapi.contact_addr', '3'),
			'addr_postal_code' => array('phpgwapi.contact_addr', '3'),
			'addr_country' => array('phpgwapi.contact_addr', '3'),
			'addr_preferred' => array('phpgwapi.contact_addr', '3'),
			'addr_creaton' => array('phpgwapi.contact_addr', '3'),
			'addr_creatby' => array('phpgwapi.contact_addr', '3'),
			'addr_modon' => array('phpgwapi.contact_addr', '3'),
			'addr_modby' => array('phpgwapi.contact_addr', '3'),
			'addr_address' => array('phpgwapi.contact_addr', '3'),
			//deprecated start
			'adr_one_street' => array('phpgwapi.contact_addr', '3'),
			'adr_one_locality' => array('phpgwapi.contact_addr', '3'),
			'adr_one_region' => array('phpgwapi.contact_addr', '3'),
			'adr_one_postalcode' => array('phpgwapi.contact_addr', '3'),
			'adr_one_countryname' => array('phpgwapi.contact_addr', '3'),
			'adr_one_type' => array('phpgwapi.contact_addr', '3'),
			'adr_two_street' => array('phpgwapi.contact_addr', '3'),
			'adr_two_locality' => array('phpgwapi.contact_addr', '3'),
			'adr_two_region' => array('phpgwapi.contact_addr', '3'),
			'adr_two_postalcode' => array('phpgwapi.contact_addr', '3'),
			'adr_two_countryname' => array('phpgwapi.contact_addr', '3'),
			'adr_two_type' => array('phpgwapi.contact_addr', '3'),
			//deprecated end
			'addr_pref_val' => array('phpgwapi.contact_addr', '3'),
			'key_note_id' => array('phpgwapi.contact_note', '4'),
			'note_contact_id' => array('phpgwapi.contact_note', '4'),
			'note_type' => array('phpgwapi.contact_note', '4'),
			'note_text' => array('phpgwapi.contact_note', '4'),
			'note_creaton' => array('phpgwapi.contact_note', '4'),
			'note_creatby' => array('phpgwapi.contact_note', '4'),
			'note_modon' => array('phpgwapi.contact_note', '4'),
			'note_modby' => array('phpgwapi.contact_note', '4'),
			//deprecated start
			'note' => array('phpgwapi.contact_note', '4'),
			//deprecated end
			'key_other_id' => array('phpgwapi.contact_others', '5'),
			'other_contact_id' => array('phpgwapi.contact_others', '5'),
			'other_name' => array('phpgwapi.contact_others', '5'),
			'other_value' => array('phpgwapi.contact_others', '5'),
			'other_owner' => array('phpgwapi.contact_others', '5'),
			'other_count_row' => array('phpgwapi.contact_others', '5'),
			//deprecated start
			'label' => array('phpgwapi.contact_others', '5'),
			'email_type' => array('phpgwapi.contact_others', '5'),
			'email_home_type' => array('phpgwapi.contact_others', '5'),
			'adr_one_type' => array('phpgwapi.contact_others', '5'),
			'adr_two_type' => array('phpgwapi.contact_others', '5'),
			//deprecated end
			'key_comm_id' => array('phpgwapi.contact_comm', '6'),
			'comm_contact_id' => array('phpgwapi.contact_comm', '6'),
			'comm_descr' => array('phpgwapi.contact_comm', '6'),
			'comm_preferred' => array('phpgwapi.contact_comm', '6'),
			'comm_data' => array('phpgwapi.contact_comm', '6'),
			'comm_creaton' => array('phpgwapi.contact_comm', '6'),
			'comm_creatby' => array('phpgwapi.contact_comm', '6'),
			'comm_modon' => array('phpgwapi.contact_comm', '6'),
			'comm_modby' => array('phpgwapi.contact_comm', '6'),
			//deprecated start
			'tel_work' => array('phpgwapi.contact_comm', '6'),
			'tel_home' => array('phpgwapi.contact_comm', '6'),
			'tel_voice' => array('phpgwapi.contact_comm', '6'),
			'tel_fax' => array('phpgwapi.contact_comm', '6'),
			'tel_msg' => array('phpgwapi.contact_comm', '6'),
			'tel_cell' => array('phpgwapi.contact_comm', '6'),
			'tel_pager' => array('phpgwapi.contact_comm', '6'),
			'tel_bbs' => array('phpgwapi.contact_comm', '6'),
			'tel_modem' => array('phpgwapi.contact_comm', '6'),
			'tel_car' => array('phpgwapi.contact_comm', '6'),
			'tel_isdn' => array('phpgwapi.contact_comm', '6'),
			'tel_video' => array('phpgwapi.contact_comm', '6'),
			'tel_prefer' => array('phpgwapi.contact_comm', '6'),
			'email' => array('phpgwapi.contact_comm', '6'),
			'email_type' => array('phpgwapi.contact_comm', '6'),
			'email_home' => array('phpgwapi.contact_comm', '6'),
			'email_home_type' => array('phpgwapi.contact_comm', '6'),
			'url' => array('phpgwapi.contact_comm', '6'),
			//deprecated end
			'contact_type_id' => array('phpgwapi.contact_types', '9'),
			'contact_type_descr' => array('phpgwapi.contact_types', '9'),
			'contact_type_table' => array('phpgwapi.contact_types', '9'),
			'comm_descr_id' => array('phpgwapi.contact_comm_descr', '7'),
			'comm_description' => array('phpgwapi.contact_comm_descr', '7'),
			'comm_type' => array('phpgwapi.contact_comm_descr', '7'),
			'comm_find_descr' => array('phpgwapi.contact_comm_descr', '7'),
			'comm_type_id' => array('phpgwapi.contact_comm_type', '8'),
			'comm_type_description' => array('phpgwapi.contact_comm_type', '8'),
			'comm_active' => array('phpgwapi.contact_comm_type', '8'),
			'comm_class' => array('phpgwapi.contact_comm_type', '8'),
			'addr_type_id' => array('phpgwapi.contact_addr_type', '10'),
			'addr_description' => array('phpgwapi.contact_addr_type', '10'),
			'note_type_id' => array('phpgwapi.contact_note_type', '11'),
			'note_description' => array('phpgwapi.contact_note_type', '11'),
			'account_id' => array('phpgwapi.contact_accounts', '12'),
			'account_person_id' => array('phpgwapi.contact_accounts', '12'),
			'person_only' => array('phpgwapi.contact_accounts', '12'),
			'key_cat_id' => array('phpgwapi.contact_categories', '13'));

		/**
		 * Database columns for import/export
		 * @access private
		 */
		var $import_export_fields = array('contact_id',
			'access',
			'owner',
			'per_full_name',
			'per_first_name',
			'per_middle_name',
			'org_name',
			'people',
			'per_last_name',
			'per_suffix',
			'per_prefix',
			'per_birthday',
			'per_pubkey',
			'per_title',
			'per_department',
			'per_initials',
			'per_sound',
			'per_active',
			'per_createon',
			'per_createby',
			'per_modby',
			'per_modon',
			'key_addr_id',
			'addr_description',
			'addr_add1',
			'addr_add2',
			'addr_add3',
			'addr_address',
			'addr_postal_code',
			'addr_city',
			'addr_state',
			'addr_country',
			'addr_preferred',
			'comm_description',
			'comm_type_description',
			'comm_data',
			'key_note_id',
			'note_text',
			'note_description',
			'other_value',
			'other_name');

		/**
		 * Database columns for import
		 * @access private
		 */
		var $import_fields = array('contact_id' => 'contact id',
			'first_name' => 'first name',
			'last_name' => 'last name',
			'middle_name' => 'middle name',
			'sufix' => 'sufix',
			'prefix' => 'prefix',
			'birthday' => 'birthday',
			'pubkey' => 'public key',
			'title' => 'title',
			'department' => 'department',
			'initials' => 'initials',
			'sound' => 'sound',
			'active' => 'active',
			'organizations' => 'organizations',
			'categories' => 'Categories',
			'locations' => array('add1' => 'Address 1',
				'add2' => 'Address 2',
				'add3' => 'Address 3',
				'city' => 'City',
				'state' => 'State',
				'postal_code' => 'Postal code',
				'country' => 'Country',
				'preferred' => 'Preferred'));


		//'parent_id'		=> array('categories_parent', '14'));

		/**
		 * Adresses fields array for mantain backward compatibility.
		 *
		 * As soon as csv and vcard import/export, had been fixed, this could be removed.
		 * @access private
		 * @deprecated Just for old add function.
		 */
		var $adr_old = array(array('adr_one_street',
				'adr_one_locality',
				'adr_one_region',
				'adr_one_postalcode',
				'adr_one_countryname'),
			array('adr_two_street',
				'adr_two_locality',
				'adr_two_region',
				'adr_two_postalcode',
				'adr_two_countryname'));

		/**
		 * Phone/Emails fields array for mantain backward compatibility.
		 *
		 * As soon as csv and vcard import/export, had been fixed, this could be removed.
		 * @access private
		 * @deprecated Just for old add function.
		 */
		var $comm_old = array('tel_work' => array('Bussiness Phone', 'phone'),
			'tel_home' => array('Home Phone', 'phone'),
			'tel_voice' => array('Voice', 'phone'),
			'tel_fax' => array('Fax', 'fax'),
			'tel_msg' => array('Message Service', 'phone'),
			'tel_cell' => array('Mobille Phone', 'mobil'),
			'tel_pager' => array('Pager', 'phone'),
			'tel_bbs' => array('BBS', 'phone'),
			'tel_modem' => array('Modem', 'phone'),
			'tel_car' => array('Car', 'phone'),
			'tel_isdn' => array('ISDN', 'phone'),
			'tel_video' => array('Video', 'phone'),
			'email' => array('Bussiness', 'email'),
			'email_home' => array('Home', 'email'));

		/**
		 * @var array $contact_type the types of contacts available (person/org)
		 */
		var $contact_type = array();

		/**
		 * Describe the type of contact, this var know the type of contact.
		 * @access private
		 */
		var $_contact_person;
		var $_contact_org;

		/**
		 * @var bool $LDAPSyncEnabled synchronise contact with LDAP on add/edit/delete
		 */
		var $LDAPSyncEnabled = false;

		/**
		 * @var array $locked list of currently locked tables
		 */
		var $locked = array();

		/**
		 * @var bool $trans is internal for transaction
		 * @access private
		 */
		var $trans = False;

		function __construct( $session = True )
		{
			$this->db = clone($GLOBALS['phpgw']->db);

			$this->stock_contact_fields = & $this->contact_fields['showable'];
			$this->account_id = isset($GLOBALS['phpgw_info']['user']['account_id']) ? $GLOBALS['phpgw_info']['user']['account_id'] : 0;
			if ($session)
			{
				if (!is_object($GLOBALS['phpgw']->session))
				{
					$GLOBALS['phpgw']->session = createObject('phpgwapi.sessions');
				}
				$this->read_sessiondata();
				$this->save_sessiondata();
			}

			if (isset($GLOBALS['phpgw_info']['server']['contact_repository']) && $GLOBALS['phpgw_info']['server']['contact_repository'] == 'ldap')
			{
				$this->LDAPSyncEnabled = true;
				$this->LDAPResource = $GLOBALS['phpgw']->common->ldapConnect($GLOBALS['phpgw_info']['server']['ldap_contact_host'], $GLOBALS['phpgw_info']['server']['ldap_contact_dn'], $GLOBALS['phpgw_info']['server']['ldap_contact_pw']);
			}
			$this->_contact_person = 'Persons';
			$this->_contact_org = 'Organizations';
		}

		/**
		 * Read data from session into this object
		 *
		 * @access private
		 */
		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data', 'phpgwapi');

			if (!is_array($data))
			{
				$data = array();
			}

			if (isset($data['comm_descr_flag']) && $data['comm_descr_flag'] == 'cache')
			{
				$this->comm_descr = $data['comm_descr'];
				$this->comm_descr_flag = 'cache';
			}
			else
			{
				$this->comm_descr = $this->get_contact_comm_descr();
				$this->comm_descr_flag = 'cache';
			}

			if (isset($data['comm_type_flag']) && $data['comm_type_flag'] == 'cache')
			{
				$this->comm_type = $data['comm_type'];
				$this->comm_type_flag = 'cache';
			}
			else
			{
				$this->comm_type = $this->get_contact_comm_type();
				$this->comm_type_flag = 'cache';
			}

			if (isset($data['addr_type_flag']) && $data['addr_type_flag'] == 'cache')
			{
				$this->addr_type = $data['addr_type'];
				$this->addr_type_flag = 'cache';
			}
			else
			{
				$this->addr_type = $this->get_contact_addr_type();
				$this->addr_type_flag = 'cache';
			}

			if (isset($data['note_type_flag']) && $data['note_type_flag'] == 'cache')
			{
				$this->note_type = $data['note_type'];
				$this->note_type_flag = 'cache';
			}
			else
			{
				$this->note_type = $this->get_contact_note_type();
				$this->note_type_flag = 'cache';
			}

			if (isset($data['contact_type_flag']) && $data['contact_type_flag'] == 'cache')
			{
				$this->contact_type = $data['contact_type'];
				$this->contact_type_flag = 'cache';
			}
			else
			{
				$this->contact_type = $this->get_contact_types();
				$this->contact_type_flag = 'cache';
			}
		}

		/**
		 * Save this object into session
		 *
		 * @access private
		 */
		function save_sessiondata()
		{
			$data = array
				(
				'comm_descr' => $this->comm_descr,
				'comm_type' => $this->comm_type,
				'addr_type' => $this->addr_type,
				'note_type' => $this->note_type,
				'contact_type' => $this->contact_type,
				'comm_descr_flag' => $this->comm_descr_flag,
				'comm_type_flag' => $this->comm_type_flag,
				'addr_type_flag' => $this->addr_type_flag,
				'note_type_flag' => $this->note_type_flag,
				'contact_type_flag' => $this->contact_type_flag
			);
			$GLOBALS['phpgw']->session->appsession('session_data', 'phpgwapi', $data);
		}

		function delete_sessiondata( $var = '' )
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data', 'phpgwapi');
			if ($var != '')
			{
				unset($data[$var]);
			}
			else
			{
				$data = '';
			}
			$GLOBALS['phpgw']->session->appsession('session_data', 'phpgwapi', $data);
		}

		/**
		 * Get the information about contact of type 'person'
		 *
		 * @return string Contact person name
		 */
		function get_person_name()
		{
			return $this->_contact_person;
		}

		/**
		 * Get the information about contact of type `Organization'
		 *
		 * @return string Organization name
		 */
		function get_org_name()
		{
			return $this->_contact_org;
		}

		/**
		 * Read one contact entry and return the wanted fields
		 *
		 * @param integer $id ID for contact entry to read
		 * @param array $fields Wanted fields filter
		 * @return array Array with ine contact entry
		 * @deprecated
		 * @access private
		 * @internal send this the id and whatever fields you want to see
		 */
		function read_single_entry( $id, $fields = '' )
		{
			$person_data = $this->person_complete_data($id);
			$entry = array
				(
				'fn' => $person_data['full_name'],
				'sound' => $person_data['sound'],
				'org_name' => '',
				'org_unit' => $person_data['department'],
				'title' => $person_data['title'],
				'n_prefix' => $person_data['prefix'],
				'n_given' => $person_data['first_name'],
				'n_middle' => $person_data['middle_name'],
				'n_family' => $person_data['last_name'],
				'n_suffix' => $person_data['suffix'],
				'label' => '',
				'adr_one_street' => isset($person_data['locations'][1]['add1']) ? $person_data['locations'][1]['add1'] : '',
				'adr_one_locality' => isset($person_data['locations'][1]['city']) ? $person_data['locations'][1]['city'] : '',
				'adr_one_region' => isset($person_data['locations'][1]['state']) ? $person_data['locations'][1]['state'] : '',
				'adr_one_postalcode' => isset($person_data['locations'][1]['postal_code']) ? $person_data['locations'][1]['postal_code'] : '',
				'adr_one_countryname' => isset($person_data['locations'][1]['country']) ? $person_data['locations'][1]['country'] : '',
				'adr_one_type' => isset($person_data['locations'][1]['type']) ? $person_data['locations'][1]['type'] : '',
				'adr_two_street' => isset($person_data['locations'][2]['add1']) ? $person_data['locations'][2]['add1'] : '',
				'adr_two_locality' => isset($person_data['locations'][2]['city']) ? $person_data['locations'][2]['city'] : '',
				'adr_two_region' => isset($person_data['locations'][2]['state']) ? $person_data['locations'][2]['state'] : '',
				'adr_two_postalcode' => isset($person_data['locations'][2]['postal_code']) ? $person_data['locations'][2]['postal_code'] : '',
				'adr_two_countryname' => isset($person_data['locations'][2]['country']) ? $person_data['locations'][2]['country'] : '',
				'adr_two_type' => isset($person_data['locations'][2]['type']) ? $person_data['locations'][2]['type'] : '',
				'tz' => isset($person_data['locations'][1]['tz']) ? $person_data['locations'][1]['tz'] : '',
				'geo' => '',
				'tel_work' => isset($person_data['comm_media']['work phone']) ? $person_data['comm_media']['work phone'] : '',
				'tel_home' => isset($person_data['comm_media']['home phone']) ? $person_data['comm_media']['home phone'] : '',
				'tel_voice' => isset($person_data['comm_media']['voice phone']) ? $person_data['comm_media']['voice phone'] : '',
				'tel_msg' => isset($person_data['comm_media']['msg phone']) ? $person_data['comm_media']['msg phone'] : '',
				'tel_fax' => isset($person_data['comm_media']['work fax']) ? $person_data['comm_media']['work fax'] : '',
				'tel_pager' => isset($person_data['comm_media']['pager']) ? $person_data['comm_media']['pager'] : '',
				'tel_cell' => isset($person_data['comm_media']['mobile (cell) phone']) ? $person_data['comm_media']['mobile (cell) phone'] : '',
				'tel_bbs' => isset($person_data['comm_media']['bbs']) ? $person_data['comm_media']['bbs'] : '',
				'tel_modem' => isset($person_data['comm_media']['modem']) ? $person_data['comm_media']['modem'] : '',
				'tel_isdn' => isset($person_data['comm_media']['isdn']) ? $person_data['comm_media']['isdn'] : '',
				'tel_car' => isset($person_data['comm_media']['car phone']) ? $person_data['comm_media']['car phone'] : '',
				'tel_video' => isset($person_data['comm_media']['video']) ? $person_data['comm_media']['video'] : '',
				'tel_prefer' => '',
				'email' => isset($person_data['comm_media']['work email']) ? $person_data['comm_media']['work email'] : '',
				'email_type' => isset($person_data['comm_type']['work email']) ? $person_data['comm_type']['work email'] : '',
				'email_home' => isset($person_data['comm_media']['home email']) ? $person_data['comm_media']['home email'] : '',
				'email_home_type' => isset($person_data['comm_type']['home email']) ? $person_data['comm_type']['home email'] : '',
				'address2' => isset($person_data['locations'][1]['add2']) ? $person_data['locations'][1]['add2'] : '',
				'address3' => isset($person_data['locations'][1]['add3']) ? $person_data['locations'][1]['add3'] : '',
				'ophone' => '',
				'bday' => isset($person_data['birthday']) ? $person_data['birthday'] : '',
				'url' => isset($person_data['comm_media']['website']) ? $person_data['comm_media']['website'] : '',
				'pubkey' => isset($person_data['pubkey']) ? $person_data['pubkey'] : '',
				'note' => isset($person_data['notes'][1]['text']) ? $person_data['notes'][1]['text'] : ''
			);

			if (is_array($fields))
			{
				foreach ($fields as $field)
				{
					$entry_data[0][$field] = $entry[$field];
				}
			}
			else
			{
				$entry_data[0] = $entry;
			}
			return $entry_data;
		}

		/**
		 * Read last contact entry and return wanted fields
		 *
		 * @param array $fields Wanted fields
		 * @return array Array with contact fields
		 * @deprecated
		 * @access private
		 */
		function read_last_entry( $fields = '' )
		{
			$last_contact = $this->get_max_contact();
			$person_data = $this->read_single_entry($last_contact, $fields);
			return $person_data;
		}

		/**
		 * Read fields from database
		 *
		 * @param integer $start Start position in database result
		 * @param integer $limit Number of values wanted
		 * @param array $fields Fields to read from database
		 * @param string $query Unused
		 * @param string $filter Unused
		 * @param string $sort Unused
		 * @param string $order Unused
		 * @param integer $lastmod Unused
		 * @return array Array with database fields
		 * @deprecated
		 * @access private
		 */
		function read( $start = 0, $limit = 0, $fields = '', $query = '', $filter = '', $sort = '', $order = '', $lastmod = -1 )
		{
			throw new Exception('deprecated method called (contatcts_sql::read)');

			foreach ($fields as $field)
			{
				// we need search for a this communication media
				if (@array_key_exists($field, $this->comm_old))
				{
					$comms[] = array($field, 'comm_descr');
					unset($fields[$field]);
				}
			}
			$first_adr = array();
			foreach ($fields as $field)
			{
				// We'll get first address
				if (@in_array($field, $adr_old[0]))
				{
					$adr[0] = $adr[0] + $field;
					unset($fields[$field]);
				}

				if (@in_array($field, $adr_old[1]))
				{
					$adr[1] = $adr[1] + $field;
					unset($fields[$field]);
				}
			}

			$this->request($fields);
			//$this->limit($limit, $start);
			$sql = $this->get_sql();
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
			while ($this->db->next_record())
			{
				$return_fields[] = $this->db->Record;
			}
			return $return_fields;
		}

		/**
		 * Add contact by importing it and adding a new organization when required
		 *
		 * @param string $owner Unused
		 * @param array $fields Fields to import
		 * @param mixed $access Access rights
		 * @param integer $cat_id Category ID
		 * @param integer $tid Unused
		 * @deprecated This method is for old import only, I ask help to code a new one
		 * @access private
		 */
		function add( $owner, $fields, $access = '', $cat_id = '', $tid = 'n' )
		{
			foreach ($fields as $field => $value)
			{
				$this->explode_field_name($field, $value, $fields);
			}

			if (in_array($fields['note'], $fields))
			{
				$note['type'] = 'general';
				$note['note'] = $fields['note'];
				unset($fields['note']);
			}

			if ($fields['organization'])
			{
				$criteria = $this->criteria_for_index($GLOBALS['phpgw_info']['user']['account_id'], PHPGW_CONTACTS_CATEGORIES_ALL, array(
					'org_name'), $fields['organization']);
				$records = $this->get_orgs(array('contact_id'));
				if (is_array($records))
				{
					foreach ($record as $org)
					{
						$orgs[] = $org['contact_id'];
					}
				}
				else
				{
					$this->add_org(array('org_name' => $fields['organization']));
					$orgs = array($this->last_id('contact', 'contact_id'));
				}
			}

			$fields['notes'] = $note;
			$fields['organizations'] = $orgs;
			$fields['categories'] = $cat_id;
			$fields['access'] = $access;
			$this->contact_import($fields);
		}

		/**
		 * Test existence of a field in a contact
		 *
		 * @param integer $id Contact ID
		 * @param string $field_name Field name which should be tested
		 * @return integer Number of existing rows
		 * @deprecated
		 * @access private
		 */
		function field_exists( $id, $field_name )
		{
			$this->request('other_count_row');
			$this->criteria(array('other_contact_id' => $id));
			$this->criteria(array('other_name' => $field_name));
			$sql_select = $this->map['other_count_row']->select();
			$this->db->query($sql_select, __LINE__, __FILE__);
			$this->db->next_record();
			return $this->db->f(0);
		}

		/**
		 * Add one extra field to a contact
		 *
		 * @param integer $id Contact ID
		 * @param integer $owner Unused
		 * @param string $field_name Field name
		 * @param string $field_value Field value
		 * @deprecated
		 * @access private
		 */
		function add_single_extra_field( $id, $owner, $field_name, $field_value )
		{
			$this->other_fields = createObject('phpgwapi.contact_others');
			$this->other_fields->insert(array('other_contact_id' => $id, 'other_name' => $field_name,
				'other_value' => $field_value));
		}

		/**
		 * Delete one extra field from a contact
		 *
		 * @param $id Contact ID
		 * @param $field_name Field name to delete
		 * @return array Empty array
		 * @deprecated
		 * @access private
		 */
		function delete_single_extra_field( $id, $field_name )
		{
			$this->delete_others($cid);
			return array();
		}

		/**
		 * Update contact entry
		 *
		 * @param integer $id Contact ID
		 * @param integer $owner Owner of contact
		 * @param array $fields Fields to update
		 * @param mixed $access Unused
		 * @param integer $cat_id Unused
		 * @param integer $tid Unused
		 * @deprecated
		 * @access private
		 */
		//FIXME Sigurd: This function used to be named "update" - but that conflicts with the parent class - is it used?
		function update_contact( $id, $owner, $fields, $access = '', $cat_id = '', $tid = 'n' )
		{
			$this->update($fields);
			$this->criteria('contact_id', $id);
			$sql_update = $this->run_update();

			foreach ($sql_update as $update)
			{
				$this->db->query($update, __LINE__, __FILE__);
			}

			foreach ($extra_fields as $name => $value)
			{
				if ($this->field_exists($id, $x_name))
				{
					if (!$value)
					{
						$this->delete_single_extra_field($id, $name);
					}
					else
					{
						$this->update_single_extra_field($id, $name);
					}
				}
				else
				{
					$this->add_single_extra_field($id, $owner, $name, $value);
				}
			}
		}

		/**
		 * Echoes "don't use this"
		 *
		 * @param integer $id Unused
		 * @deprecated
		 * @access private
		 */
		function read_extra_fields( $id )
		{
			echo "don't use this";
		}

		/**
		 * Update one extra field of a contact
		 *
		 * @param integer $id Contact ID
		 * @param string $name Field name
		 * @param string $value Field value
		 * @deprecated
		 * @access private
		 */
		function update_single_extra_field( $id, $name, $value )
		{
			$other = createObject('phpgwapi.contact_others');
			$criteria = phpgwapi_sql_criteria::token_and(phpgwapi_sql_criteria::_equal('other_name', $name), phpgwapi_sql_criteria::_equal('contact_id', $id));
			$other->update($id, array('other_value' => $value), $criteria);
		}

		/**
		 * Retrieve all persons data which you specify, this can use limit and order.
		 *
		 * @param array $fields The fields that you can see from person
		 * @param integer $limit Limit of records that you want
		 * @param integer $ofset Ofset of record that you want start
		 * @param string $orderby The field which you want order
		 * @param string $sort ASC | DESC depending what you want
		 * @param mixed $criteria All criterias what you want
		 * @param mixed $criteria_token same like $criteria but builded<br>with phpgwapi_sql_criteria class, more powerfull
		 * @return array Array person with records
		 */
		function get_persons( $fields, $start = '', $limit = '', $orderby = '', $sort = '', $criteria = '', $criteria_token = '' )
		{
			$this->request($fields);
			if (in_array('org_name', $fields))
			{
				$this->request(array('people'));
			}

			if ($criteria != '')
			{
				$this->criteria($criteria);
				$this->criteria(array('contact_type' => $this->search_contact_type($this->_contact_person)));
			}
			else
			{
				if ($criteria_token == '')
				{
					$this->criteria(array('contact_type' => $this->search_contact_type($this->_contact_person)));
				}
				else
				{
					$criteria_token = phpgwapi_sql_criteria::token_and(phpgwapi_sql_criteria::_equal('contact_type', $this->search_contact_type($this->_contact_person)), $criteria_token);
					$this->criteria_token($criteria_token);
				}
			}

			if ($orderby)
			{
				$this->order(array($orderby), $sort);
			}
			$sql = $this->get_sql();
			if ($limit)
			{
				$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
			}
			else
			{
				$this->db->query($sql, __LINE__, __FILE__);
			}

			$this->total_records = $this->db->num_rows();
			$persons = array();

			while ($this->db->next_record())
			{
				$persons[] = $this->db->Record;
			}
			return $persons;
		}

		/**
		 * Get all the `principal' information related for organization
		 *
		 * @param integer|array $contact_id id of the contact or array of the same
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @param boolean $get_org True: Also get the organization; False: get only the person data
		 * @return array|string Array with records or string with sql query
		 */
		function get_principal_persons_data( $contact_id, $action = PHPGW_SQL_RUN_SQL, $get_org = True )
		{
			$data = array('contact_id',
				'owner',
				'access',
				'cat_id',
				'per_full_name',
				'per_first_name',
				'per_last_name',
				'per_middle_name',
				'per_prefix',
				'per_suffix',
				'per_birthday',
				'per_pubkey',
				'per_title',
				'per_department',
				'per_initials',
				'per_active',
				'per_sound');
			$this->request($data);
			$this->criteria(array('contact_id' => $contact_id));
			if ($get_org == True)
			{
				$this->request(array('org_name', 'people', 'org_id'));
				$this->criteria(array('my_preferred' => 'Y'));
			}
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Retrieve Data for Id, taking care that are person.
		 *
		 * @param mixed $person_id Organizations Id which want information.
		 * @param string $criteria
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return array Asociative array with id and all data that you requested
		 */
		function get_organizations_by_person( $person_id, $criteria = '', $action = PHPGW_SQL_RUN_SQL )
		{
			$data = array('my_org_id',
				'my_addr_id',
				'my_preferred',
				'org_name',
				'people');

			$this->request($data);
			$this->criteria(array('my_person_id' => $person_id));
			if (!empty($criteria))
			{
				$this->criteria($criteria);
			}
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Retrieve all organizations data which you specify, this can use limit and order.
		 *
		 * @param array $fields The fields that you can see from person
		 * @param integer $limit Limit of records that you want
		 * @param integer $ofset Ofset of record that you want start
		 * @param string $orderby The field which you want order
		 * @param string $sort ASC | DESC depending what you want
		 * @param array $criteria All criterias what you want
		 * @param mixed $criteria_token same like $criteria but builded<br>with phpgwapi_sql_criteria class, more powerfull
		 * @return array Array with organization with records
		 */
		function get_orgs( $fields, $limit = '', $ofset = '', $orderby = '', $sort = '', $criteria = '', $criteria_token = '' )
		{
			$orgs = array();
			$this->request($fields);
			$person_fields = array('per_full_name', 'per_first_name', 'per_last_name');
			if (count(array_intersect($person_fields, $fields)) > 0)
			{
				$this->request(array('organizations'));
			}

			if ($criteria != '')
			{
				$this->criteria($criteria);
				$this->criteria(array('contact_type' => $this->search_contact_type($this->_contact_org)));
			}
			else
			{
				if ($criteria_token == '')
				{
					$this->criteria(array('contact_type' => $this->search_contact_type($this->_contact_org)));
				}
				else
				{
					$criteria_token = phpgwapi_sql_criteria::token_and(phpgwapi_sql_criteria::_equal('contact_type', $this->search_contact_type($this->_contact_org)), $criteria_token);
					$this->criteria_token($criteria_token);
				}
			}

			if ($orderby)
			{
				$this->order(array($orderby), $sort);
			}

			$sql = $this->get_sql();

			if ($limit)
			{
				$this->db->limit_query($sql, $ofset, __LINE__, __FILE__, $limit);
			}
			else
			{
				$this->db->query($sql, __LINE__, __FILE__);
			}

			$this->total_records = $this->db->num_rows();
			while ($this->db->next_record())
			{
				$orgs[] = $this->db->Record;
			}

			return $orgs;
		}

		/**
		 * Get all the `principal' information related for contacts (when is/are person)
		 *
		 * @param integer|array $contact_id id of the contact or array of the same
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return array|string Array with records or string with sql query
		 */
		function get_principal_organizations_data( $contact_id, $action = PHPGW_SQL_RUN_SQL )
		{
			$data = array('contact_id',
				'owner',
				'access',
				'cat_id',
				'org_active',
				'org_name');
			$this->request($data);
			$this->criteria(array('contact_id' => $contact_id));
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Retrieve Data for Id, taking care that are organizations.
		 *
		 * @param mixed $organizations_id Organizations Id which want information.
		 * @param string $criteria
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return array Asociative array with id and all data that we requested
		 */
		function get_people_by_organizations( $organizations_id, $criteria = '', $action = PHPGW_SQL_RUN_SQL )
		{
			$data = array('my_person_id',
				'per_first_name',
				'per_last_name',
				'my_addr_id',
				'my_preferred');
			$this->request($data);

			$this->criteria(array('my_org_id' => $organizations_id));
			$this->criteria($criteria);
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Retrieve communications data for contact id.
		 *
		 * @param integer $contact_id Id which want information.
		 * @param array $fields_to_search Description what you want to find.
		 * This parameter is an array and you can use it when you want get
		 * information of especific comm_description (based in descriptions
		 * from comm_description catalog).
		 * @return array Asociative with id and all data that you requested
		 */
		function get_comm_contact_data( $contact_id, $fields_to_search = '' )
		{
			$comms = array();
			$data = array('comm_contact_id',
				'key_comm_id',
				'comm_preferred',
				'comm_data',
				'comm_descr',
				'comm_description');
			$this->request($data);
			$this->criteria(array('comm_contact_id' => $contact_id));
			if (!empty($fields_to_search))
			{
				$this->criteria(array('comm_find_descr' => $fields_to_search));
			}

			$entry = $this->get_records(__LINE__, __FILE__);
			if ($entry)
			{
				foreach ($entry as $k => $v)
				{
					$comms[] = $entry[$k];
				}
			}
			return $comms;
		}

		/**
		 * Retrieve locations data for contact id.
		 *
		 * @param integer $contact_id Id which want information.
		 * @param array $criteria Others criterias what you want.
		 * @return array Asociative with id and all data that you requested
		 */
		function get_addr_contact_data( $contact_id, $criteria = '' )
		{
			$data = array
				(
				'addr_contact_id',
				'key_addr_id',
				'addr_type',
				'addr_add1',
				'addr_add2',
				'addr_city',
				'addr_state',
				'addr_postal_code',
				'addr_country',
				'addr_preferred',
				'addr_description'
			);
			$this->request($data);
			$this->criteria(array('addr_contact_id' => $contact_id));
			if ($criteria != '')
			{
				$this->criteria($criteria);
			}

			$locations = array();

			$entries = $this->get_records(__LINE__, __FILE__);
			if (is_array($entries) && count($entries))
			{
				foreach ($entries as $entry)
				{
					$locations[] = $entry;
				}
			}
			return $locations;
		}

		/**
		 * Retrieve others data for contact id.
		 *
		 * @param integer $contact_id Id which want information.
		 * @param string $criteria
		 * @return array Asociative with id and all data that you requested
		 */
		function get_others_contact_data( $contact_id, $criteria = '' )
		{
			$others = array();
			$data = array('other_contact_id',
				'key_other_id',
				'other_owner',
				'other_name',
				'other_value');
			$this->request($data);
			$this->criteria(array('other_contact_id' => $contact_id));
			if ($criteria != '')
			{
				$this->criteria($criteria);
			}
			$entry = $this->get_records(__LINE__, __FILE__);
			if ($entry)
			{
				foreach ($entry as $k => $v)
				{
					$others[] = $entry[$k];
				}
			}
			return $others;
		}

		/**
		 * Get the records from contact type catalog
		 *
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return array|string Array with records or string with sql query
		 */
		function get_contact_types( $action = PHPGW_SQL_RUN_SQL )
		{
			$fields = array('contact_type_id', 'contact_type_descr', 'contact_type_table');
			$this->request($fields);
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Get the records from addr type catalog
		 *
		 * @param integer $action PHPGW_SQL_RETURN_SQL | RUN_SQL depending what we want
		 * @return array|string Array with records or string with sql query
		 */
		function get_contact_addr_type( $action = PHPGW_SQL_RUN_SQL )
		{
			$fields = array('addr_type_id', 'addr_description');
			$this->request($fields);
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Get the records from comm type catalog
		 *
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return array|string Array with records or string with sql query
		 */
		function get_contact_comm_type( $action = PHPGW_SQL_RUN_SQL )
		{
			$fields = array('comm_type_id', 'comm_type_description', 'comm_active', 'comm_class');
			$this->request($fields);
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Get the records from comm descr catalog
		 *
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return array|string Array with records or string with sql query
		 */
		function get_contact_comm_descr( $action = PHPGW_SQL_RUN_SQL )
		{
			$fields = array('comm_descr_id', 'comm_description', 'comm_type');
			$this->request($fields);
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Get the records from note descr catalog
		 *
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return mixed SQL string or records with note_type_id and not_description
		 */
		function get_contact_note_type( $action = PHPGW_SQL_RUN_SQL )
		{
			$fields = array('note_type_id', 'note_description');
			$this->request($fields);
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Count the persons and return the number
		 *
		 * @param string $criteria The criterias
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return integer The record count number
		 */
		function get_count_persons( $criteria = '', $action = PHPGW_SQL_RUN_SQL )
		{
			$this->request('count_persons');
			if ($criteria != '')
			{
				$this->criteria_token($criteria);
			}
			$count = $this->get_query($action, __LINE__, __FILE__);
			return $count[0]['count_persons'];
		}

		/**
		 * Count the organizations and return the number
		 *
		 * @param string $criteria The criterias
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return integer The record count number
		 */
		function get_count_orgs( $criteria = '', $action = PHPGW_SQL_RUN_SQL )
		{
			$this->request('count_orgs');
			if ($criteria != '')
			{
				$this->criteria_token($criteria);
			}
			$count = $this->get_query($action, __LINE__, __FILE__);
			return $count[0]['count_orgs'];
		}

		/**
		 * Count the contacts and return the number
		 *
		 * @param string $criteria The criterias
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return integer The record count number
		 */
		function get_count_contacts( $criteria = '', $action = PHPGW_SQL_RUN_SQL )
		{
			$this->request('count_contacts');
			if ($criteria != '')
			{
				$this->criteria($criteria);
			}
			$count = $this->get_query($action, __LINE__, __FILE__);
			return $count[0]['count_contacts'];
		}

		/**
		 * Get the max contact_id
		 *
		 * @param string $criteria The criterias
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return integer The record count number
		 */
		function get_max_contact( $criteria = '', $action = PHPGW_SQL_RUN_SQL )
		{
			$this->request('max_contacts');
			if ($criteria != '')
			{
				$this->criteria($criteria);
			}
			$max = $this->get_query($action, __LINE__, __FILE__);
			return $max[0]['max_contacts'];
		}

		/**
		 * Retrieve all the people that is owned by $owner (but I have read access).
		 *
		 * @param integer $owner The account_id of the owner.
		 * @param array $data This is the information that want to retrieve.
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return array Asociative with id and all data that we requested
		 */
		function get_people_by_owner( $owner, $data, $action = PHPGW_SQL_RUN_SQL )
		{
			$this->request('contact_id');
			$this->request($data);
			$this->criteria(array('owner' => $owner));
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Retrieve all the people that is part from specified category.
		 *
		 * @param integer $cat_id The cat_id of the owner.
		 * @param boolean $sub_cat
		 * @return array Asociative with id and all data that we requested
		 */
		function get_persons_by_cat( $cat_id, $sub_cat = True )
		{
			if ($sub_cat && $cat_id)
			{
				$cat_id = array_merge(array($cat_id), (array)$this->get_sub_cats($cat_id));
			}

			$this->request('person_id');
			if (is_array($cat_id))
			{
				$this->criteria(array('sel_cat_id' => $cat_id));
			}
			else
			{
				$this->criteria(array('cat_id' => $cat_id));
			}

			$sql = $this->get_sql();

			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$persons[] = $this->db->f('person_id');
			}
			return $persons;
		}

		/**
		 * Get all the contacts id, and data if it is dessired, from the contacts that are users of phpGroupWare
		 *
		 * @param array $data Data that we want for each contact that we retrieve.
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return array Contacts that are users and his data.
		 */
		function get_system_contacts( $data, $action = PHPGW_SQL_RUN_SQL )
		{

			$accounts = $GLOBALS['phpgw']->accounts->get_list();
			foreach ($accounts as $account_data)
			{
				if ($account_data->person_id)
				{
					$people[] = $account_data->person_id;
				}
			}

			if (is_array($data))
			{
				$this->request($data);
			}

			$this->request('person_id');
			// phpgwapi_sql::in() is better than make a select by each account
			$this->criteria(array('person_id' => $people));
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Retrieve all contacts of user that are marked as private.
		 *
		 * @param mixed $data List of fields what we want retrieve
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return array Asociative with id and all data that we requested
		 */
		function get_private_contacts( $data, $action = PHPGW_SQL_RUN_SQL )
		{
			$this->request('contact_id');
			$this->request($data);
			$this->criteria(array('access' => 'private'));
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Retrieve the bussines email by contact
		 *
		 * @param integer $contacts_id The contact id that will be used for search his email.
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return array Asociative with id and email
		 */
		function get_email( $contacts_id, $action = PHPGW_SQL_RUN_SQL )
		{
			$this->request(array('comm_data'));
			$this->criteria(array('comm_descr' => $this->search_comm_descr('work email'),
				'comm_contact_id' => $contacts_id));
			$email = $this->get_records_by_field('comm_data', __LINE__, __FILE__);
			return $email[0];
		}

		/**
		 * Retrieve the bussines phone by contact
		 *
		 * @param integer $contacts_id The contact id that will be used for search his phone.
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return array Asociative with id and phone
		 */
		function get_phone( $contacts_id, $action = PHPGW_SQL_RUN_SQL )
		{
			$this->request(array('comm_data'));
			$this->criteria(array('comm_descr' => $this->search_comm_descr('work phone'),
				'comm_contact_id' => $contacts_id));
			$phone = $this->get_records_by_field('comm_data', __LINE__, __FILE__);
			return $phone[0];
		}

		/**
		 * Get all sub categories from cat_id
		 *
		 * @param integer $cat_id The category id to find
		 * @return array All sub categories
		 * @access private
		 */
		function get_sub_cats( $cat_id )
		{
			$sql = 'SELECT cat2.cat_id FROM phpgw_categories as cat '
				. 'INNER JOIN phpgw_categories as cat2 ON cat.cat_id=cat2.cat_parent '
				. 'WHERE ' . phpgwapi_sql::in('cat.cat_id', $cat_id);

			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$cats[] = $this->db->f('cat_id');
			}

			if (is_array($cats))
			{
				return array_merge($cats, (array)$this->get_sub_cats($cats));
			}
		}

		/**
		 * This function get all the Communication media from one contact.
		 *
		 * Or the first if there is no one marked as preferred.
		 * @param integer $contact_id id to locate the contact
		 * @return array Form: description => value, ...
		 */
		function get_all_comms( $contact_id )
		{
			if (intval($contact_id) != 0)
			{
				// get all comm_descriptions from api function
				$this->request(array('comm_data', 'comm_descr'));
				$this->criteria(array('comm_contact_id' => $contact_id));
				// get all comms from db
				$records = $this->get_query(PHPGW_SQL_RUN_SQL, __LINE__, __FILE__);
				if (count($records) == 0)
				{
					return;
				}
				foreach ($records as $comm)
				{
					$descr = $this->search_comm_descr_id($comm['comm_descr']);
					$comms[$descr] = $comm['comm_data'];
				}
			}
			return $comms;
		}

		/**
		 * This function get the contact name.
		 *
		 * @param integer $contact_id id The contact what you want
		 * @return string The name of the contact
		 */
		function get_contact_name( $contact_id )
		{
			$type = $this->search_contact_type_id($this->get_type_contact($contact_id));
			if ($type == $this->_contact_person)
			{
				$field_name = 'per_full_name';
			}
			elseif ($type == $this->_contact_org)
			{
				$field_name = 'org_name';
			}

			$this->request(array($field_name));
			$this->criteria(array('contact_id' => $contact_id));
			$contact_name = $this->get_records_by_field($field_name, __LINE__, __FILE__);
			return $contact_name[0];
		}

		function get_name_of_person_id( $person_id )
		{
			$this->request(array('per_full_name'));
			$this->criteria(array('person_id' => $person_id));
			$person_name = $this->get_records_by_field('per_full_name', __LINE__, __FILE__);
			return $person_name[0];
		}

		/**
		 * Get communication description string.
		 *
		 * When there are no communication types available create them.
		 * @param string $comm_type Communication Type
		 * @param string $comm_description Communication location
		 * @return string Communication description
		 * @access private
		 */
		function comms_fixed( $comm_type, $comm_description )
		{
			// In this array add all the label that you have on an import method, an make the
			// association with the corresponding description on database
			if (!$this->hash_comms_import)
			{
				$this->hash_comms_import = array('email' => array('home' => 'home email',
							'work' => 'work email',
							'other' => 'other email'),
						'phone' => array('home' => 'home phone',
							'work' => 'work phone',
							'other' => 'other phone',
							'msg' => 'msg phone',
							'home_two' => 'second home phone',
							'work_two' => 'second work phone',
							'isdn' => 'isdn',
							'other_two' => 'second other phone',
							'pager' => 'pager'),
						'fax' => array('work' => 'work fax',
							'home' => 'home fax',
							'other' => 'other fax'),
						'mobile' => array('car' => 'car phone',
							'cell' => 'mobile (cell) phone'));
			}
			return $this->hash_comms_import[$comm_type][$comm_description];
		}

		/**
		 * Explode a field name by `_' so parts of the field name have a meaning, not only the whole name
		 *
		 * @param string $field meta-field name, with form: {kind}_{key_{real_field_name}
		 * @param mixed $value to assing to field
		 * @param mixed &$field_list reference to the list that contain $field
		 * @access private
		 */
		function explode_field_name( $field, $value, &$fields_list )
		{
			// ugly hack
			$addr_keys = array('work', 'home', 'other', 'other', 'other', 'other');

			list($kind, $key, $name) = explode('_', $field, 3);
			echo "$kind : $key : $name<br />\n";
			switch ($kind)
			{
				case 'addr':
					// Code for locations
					$fields_list['location'][$key][$name] = $value;
					unset($fields_list[$field]);
					$fields_list['location'][$key]['type'] = $addr_keys[$key];
					break;
				case 'comm':
					// Code for comms
					$fields_list['comm_media'][$this->comms_fixed($key, $name)] = $value;
					unset($fields_list[$field]);
					break;
				case 'org':
					list($org_key, $name) = explode('_', $name, 2);
					switch ($key)
					{
						case 'addr':
							// Code for locations
							$fields_list['org_data']['location'][$org_key][$name] = $value;
							unset($fields_list[$field]);
							$fields_list['org_data']['location'][$org_key]['type'] = $addr_keys[$org_key];
							break;
						case 'comm':
							// Code for comms
							$fields_list['org_data']['comm_media'][$this->comms_fixed($org_key, $name)] = $value;
							unset($fields_list[$field]);
							break;
						default:
							// code for default (principal/others)
							$fields_list['org_data'][$field] = $value;
							unset($fields_list[$field]);
					}
			}
		}

		/**
		 * Get all the data for any contact or contacts that are part of $optional_criteria.
		 *
		 * This function try to be a comprensive way to get data from contacts
		 * convert the contacts database output on a multidimensional array
		 * @param integer|array $contact_id
		 * @param mixed $optional_criteria criteria built with phpgwapi_sql_criteria
		 * @param integer $line where this function is called, usefull for debug
		 * @param integer $file where this function is called, usefull for debug
		 * @return array All the data of contact (contacts).
		 */
		function person_complete_data( $contact_id, $optional_criteria = '', $line = __LINE__, $file = __FILE__ )
		{
			$this->request($this->import_export_fields);
			if (empty($optional_criteria))
			{
				if (intval($contact_id) == 0)
				{
					// this is an error
					return;
				}
				$this->criteria_token(phpgwapi_sql_criteria::_equal('contact_id', $contact_id));
			}
			else
			{
				$this->criteria_token($optional_criteria);
			}

			$sql = $this->get_sql();

			$this->execute_query($sql, $line, $file);

			while ($this->db->next_record())
			{
				$record = $this->db->Record;
				$contact['contact_id'] = $record['contact_id'];
				$contact['access'] = $record['access'];
				$contact['owner'] = $record['owner'];
				$contact['full_name'] = $record['per_full_name'];
				$contact['first_name'] = $record['per_first_name'];
				$contact['last_name'] = $record['per_last_name'];
				$contact['middle_name'] = $record['per_middle_name'];
				$contact['suffix'] = $record['per_suffix'];
				$contact['prefix'] = $record['per_prefix'];
				$contact['birthday'] = $record['per_birthday'];
				$contact['pubkey'] = $record['per_pubkey'];
				$contact['title'] = $record['per_title'];
				$contact['department'] = $record['per_department'];
				$contact['initials'] = $record['per_initials'];
				$contact['sound'] = $record['per_sound'];
				$contact['active'] = $record['per_active'];
				$contact['createon'] = isset($record['per_createon']) ? $record['per_createon'] : ''; //Sigurd sept 08: not included in sql - but who knows - it might?
				$contact['createby'] = isset($record['per_createby']) ? $record['per_createby'] : ''; // not in sql
				$contact['modby'] = $record['per_modby'];
				$contact['modon'] = $record['per_modon'];
				$contact['account_id'] = isset($record['account_id']) ? $record['account_id'] : ''; // not in sql
				$contact['org_name'] = $record['org_name'];
				// Locations info
				$loc_id = $record['key_addr_id'];
				if ($loc_id)
				{
					$contact['locations'][$loc_id]['type'] = $record['addr_description'];
					$contact['locations'][$loc_id]['add1'] = $record['addr_add1'];
					$contact['locations'][$loc_id]['add2'] = $record['addr_add2'];
					$contact['locations'][$loc_id]['add3'] = $record['addr_add3'];
					$contact['locations'][$loc_id]['address'] = $record['addr_address'];
					$contact['locations'][$loc_id]['city'] = $record['addr_city'];
					$contact['locations'][$loc_id]['state'] = $record['addr_state'];
					$contact['locations'][$loc_id]['postal_code'] = $record['addr_postal_code'];
					$contact['locations'][$loc_id]['country'] = $record['addr_country'];
					$contact['locations'][$loc_id]['preferred'] = $record['addr_preferred'];
				}
				// Notes
				$note_id = $record['key_note_id'];
				if ($note_id)
				{
					$contact['notes'][$note_id]['text'] = $record['note_text'];
					$contact['notes'][$note_id]['type'] = $record['note_description'];
				}
				// Communcation media fields
				if ($record['comm_data'])
				{
					$comm_descr = $record['comm_description'];
					//$contact[$comm_descr]			= $record['comm_data'];
					$contact['comm_media'][$comm_descr] = $record['comm_data'];
				}
				// Other fields
				if ($record['other_value'])
				{
					$contact[$record['other_name']] = $record['other_value'];
				}
			}
			return $contact;
		}

		/**
		 * Search location id in location catalog
		 *
		 * @param integer $id The location id to find
		 * @return string The description of id
		 */
		function search_location_type_id( $id )
		{
			return $this->search_catalog('addr_type_id', $id, 'addr_description', 'addr_type');
		}

		/**
		 * Search location type in location catalog
		 *
		 * @param string $description The location description to find
		 * @return integer The id of description
		 */
		function search_location_type( $description )
		{
			return $this->search_catalog('addr_description', $description, 'addr_type_id', 'addr_type');
		}

		/**
		 * Search location id in location catalog
		 *
		 * @param integer $id The location id to find
		 * @return string The description of id
		 */
		function search_note_type_id( $id )
		{
			return $this->search_catalog('note_type_id', $id, 'note_description', 'note_type');
		}

		/**
		 * Search location type in location catalog
		 *
		 * @param string $description The location description to find
		 * @return integer The id of description
		 */
		function search_note_type( $description )
		{
			return $this->search_catalog('note_description', $description, 'note_type_id', 'note_type');
		}

		/**
		 * Search communication type id in communications catalog
		 *
		 * @param integer $id The communication id to find
		 * @return string The description type of id
		 */
		function search_comm_type_id( $id )
		{
			return $this->search_catalog('comm_type_id', $id, 'comm_type_description', 'comm_type');
		}

		/**
		 * Search communication type in location catalog
		 *
		 * @param string $description The communication type to find
		 * @return integer The id of description
		 */
		function search_comm_type( $description )
		{
			return $this->search_catalog('comm_type_description', $description, 'comm_type_id', 'comm_type');
		}

		/**
		 * Search communication id in communications description catalog
		 *
		 * @param integer $id The communication description id to find
		 * @return string The description text of id
		 */
		function search_comm_descr_id( $id )
		{
			return $this->search_catalog('comm_descr_id', $id, 'comm_description', 'comm_descr');
		}

		/**
		 * Search communication description in communications description catalog
		 *
		 * @param string $description The communication type to find
		 * @return integer The id of description
		 */
		function search_comm_descr( $description )
		{
			return $this->search_catalog('comm_description', $description, 'comm_descr_id', 'comm_descr');
		}

		/**
		 * Search contact type id in contact type catalog
		 *
		 * @param integer $id The contact type id to find
		 * @return string The description text of id
		 */
		function search_contact_type_id( $id )
		{
			return $this->search_catalog('contact_type_id', $id, 'contact_type_descr', 'contact_type');
		}

		/**
		 * Search contact type description in contact type catalog
		 *
		 * @param string $description The contact type description to find
		 * @return integer The id of description
		 */
		function search_contact_type( $description )
		{
			return $this->search_catalog('contact_type_descr', $description, 'contact_type_id', 'contact_type');
		}

		/**
		 * Allow edit information of an existent contact
		 *
		 * Allow edit many communications media, locations, categories and others fields, that already exist for a given contact.
		 * @param integer $cid Contact Id that want to be edited.
		 * @param array $principal Principal information for contact, its depends on $type, but is according which each definition
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return string SQL update string
		 */
		function edit_contact( $cid, $principal, $action = PHPGW_SQL_RETURN_SQL )
		{
			$contact = createObject('phpgwapi.contact_central');
			if (isset($principal['cat_id']))
			{
				$principal['cat_id'] = $this->get_categories($principal['cat_id']);
			}
			if (!isset($principal['owner']))
			{
				$principal['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
			}
			return $contact->update($principal, phpgwapi_sql_criteria::_equal('contact_id', phpgwapi_sql::integer($cid)), $action);
		}

		/**
		 * Allow edit information of an person
		 *
		 * @param integer $id Person Id that want to be edited.
		 * @param array $data Principal information for person
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return string SQL update string
		 */
		function edit_person( $id, $data, $action = PHPGW_SQL_RUN_SQL )
		{
			$person = createObject('phpgwapi.contact_person');
			if (!isset($data['per_modon']))
			{
				$data['per_modon'] = $this->get_mkdate();
			}
			if (!isset($data['per_modby']))
			{
				$data['per_modby'] = $this->get_user_id();
			}
			return $person->update($data, phpgwapi_sql_criteria::_equal('person_id', phpgwapi_sql::integer($id)), $action);
		}

		/**
		 * Allow edit information of an org
		 *
		 * @param integer $id Org Id that want to be edited.
		 * @param array $data Information for contact
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return string SQL update string
		 */
		function edit_org( $id, $data, $action = PHPGW_SQL_RETURN_SQL )
		{
			$org = createObject('phpgwapi.contact_org');
			if (!isset($data['org_modon']))
			{
				$data['org_modon'] = $this->get_mkdate();
			}
			if (!isset($data['org_modby']))
			{
				$data['org_modby'] = $this->get_user_id();
			}
			return $org->update($data, phpgwapi_sql_criteria::_equal('org_id', phpgwapi_sql::integer($id)), $action);
		}

		/**
		 * Allow edit location information of an contact
		 *
		 * @param integer $id Contact location Id that want to be edited.
		 * @param array $data Information for contact
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return string SQL update string
		 */
		function edit_location( $id, $data, $action = PHPGW_SQL_RETURN_SQL )
		{
			$loc = createObject('phpgwapi.contact_addr');
			if (!isset($data['addr_modon']))
			{
				$data['addr_modon'] = $this->get_mkdate();
			}
			if (!isset($data['addr_modby']))
			{
				$data['addr_modby'] = $this->get_user_id();
			}
			return $loc->update($data, phpgwapi_sql_criteria::_equal('contact_addr_id', phpgwapi_sql::integer($id)), $action);
		}

		/**
		 * Allow edit all location information of an contact
		 *
		 * @param integer $contact_id Contact Id that want to be edited.
		 * @param array $data Information for contact
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return string SQL update string
		 */
		function edit_location_by_contact( $contact_id, $data, $action = PHPGW_SQL_RETURN_SQL )
		{
			$loc = CreateObject('phpgwapi.contact_addr');
			if (!isset($data['addr_modon']))
			{
				$data['addr_modon'] = $this->get_mkdate();
			}
			if (!isset($data['addr_modby']))
			{
				$data['addr_modby'] = $this->get_user_id();
			}
			return $loc->update($data, phpgwapi_sql_criteria::_equal('contact_id', phpgwapi_sql::integer($contact_id)), $action);
		}

		/**
		 * Allow edit communication information of an contact
		 *
		 * @param integer $id Contact comm Id that want to be edited.
		 * @param array $data Information for contact
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return string SQL update string
		 */
		function edit_comms( $id, $data, $action = PHPGW_SQL_RETURN_SQL )
		{
			$comm = createObject('phpgwapi.contact_comm');
			if (!isset($data['comm_modon']))
			{
				$data['comm_modon'] = $this->get_mkdate();
			}
			if (!isset($data['comm_modby']))
			{
				$data['comm_modby'] = $this->get_user_id();
			}
			return $comm->update($data, phpgwapi_sql_criteria::_equal('comm_id', phpgwapi_sql::integer($id)), $action);
		}

		/**
		 * Allow edit other information of an contact
		 *
		 * @param integer $id Contact other Id that want to be edited.
		 * @param array $data Information for contact
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return string SQL update string
		 */
		function edit_other( $id, $data, $action = PHPGW_SQL_RETURN_SQL )
		{
			$comm = createObject('phpgwapi.contact_others');
			return $comm->update($data, phpgwapi_sql_criteria::_equal('other_id', phpgwapi_sql::integer($id)), $action);
		}

		/**
		 * Allow edit communication information of an contact
		 *
		 * @param integer $id Contact Id that want to be edited.
		 * @param array $data Information for contact
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return string SQL update string
		 */
		function edit_comms_by_contact( $id, $data, $action = PHPGW_SQL_RETURN_SQL )
		{
			$comm = createObject('phpgwapi.contact_comm');
			return $comm->update($data, phpgwapi_sql_criteria::_equal('contact_id', phpgwapi_sql::integer($id)), $action);
		}

		/**
		 * Allow edit org-person relation of an contact
		 *
		 * @param integer $org_id Organization Id that want to be edited.
		 * @param integer $person_id Person Id that want to be edited.
		 * @param array $data Information for contact
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return string SQL update string
		 */
		function edit_org_person_relation( $org_id = '', $person_id = '', $data = array(), $action = PHPGW_SQL_RETURN_SQL )
		{
			$criteria = array();
			$relation = createObject('phpgwapi.contact_org_person');
			if ($org_id && $person_id)
			{
				$criteria = phpgwapi_sql_criteria::and_(phpgwapi_sql_criteria::equal('org_id', phpgwapi_sql::integer($org_id)), phpgwapi_sql_criteria::equal('person_id', phpgwapi_sql::integer($person_id)));
			}
			elseif ($org_id)
			{
				$criteria = phpgwapi_sql_criteria::equal('org_id', phpgwapi_sql::integer($org_id));
			}
			elseif ($person_id)
			{
				$criteria = phpgwapi_sql_criteria::equal('person_id', phpgwapi_sql::integer($person_id));
			}

			return $relation->update($data, $criteria, $action);
		}

		/**
		 * Allow to change the current owner for the new
		 *
		 * @param integer $old_owner Current owner
		 * @param integer $new_owner New owner, wath you want to have now.
		 * @return string SQL update string
		 */
		function change_owner( $old_owner = '', $new_owner = '' )
		{
			if (!($new_owner && $old_owner))
			{
				return False;
			}
			$contact = createObject('phpgwapi.contact_central');
			return $contact->update(array('owner' => $new_owner), phpgwapi_sql_criteria::_equal('owner', phpgwapi_sql::integer($old_owner)), PHPGW_SQL_RUN_SQL);
		}

		/**
		 * Allow to change the current owner for the new
		 *
		 * @param integer $old_owner Current owner
		 * @param integer $new_owner New owner, wath you want to have now.
		 * @return string SQL update string
		 */
		function change_owner_others( $old_owner = '', $new_owner = '' )
		{
			if (!($new_owner && $old_owner))
			{
				return False;
			}
			$contact = createObject('phpgwapi.contact_others');
			return $contact->update(array('contact_owner' => $new_owner), phpgwapi_sql_criteria::_equal('contact_owner', phpgwapi_sql::integer($old_owner)), PHPGW_SQL_RUN_SQL);
		}

		/**
		 * Allow to edit the current other data for the owner, you can modify other_name or other_value fields
		 *
		 * @param integer $id the owner id
		 * @param string $new_data The new value which you want to update.
		 * @param string $old_data The current value which you want to update.
		 * @param string $field_data The field is can be other_name or other_value
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return string SQL update string
		 */
		function edit_other_by_owner( $id, $new_data, $old_data, $field_data, $action = PHPGW_SQL_RUN_SQL )
		{
			$other = createObject('phpgwapi.contact_others');
			$criteria = phpgwapi_sql_criteria::append_and(array(phpgwapi_sql_criteria::equal('contact_owner', phpgwapi_sql::integer($id)),
					phpgwapi_sql_criteria::equal($field_data, phpgwapi_sql::string($old_data))));
			return $other->update(array($field_data => $new_data), $criteria, $action);
		}

		/**
		 * Allow to change the current cat_id by the new
		 *
		 * @param integer $cid The contact_id what you want to edit
		 * @param array $categories The new categories
		 * @param $action
		 * @return string SQL update string
		 */
		function edit_category( $cid, $categories = array(), $action = PHPGW_SQL_RETURN_SQL )
		{
			$contact = createObject('phpgwapi.contact_central');
			$principal['cat_id'] = $this->get_categories($categories);
			return $contact->update($principal, phpgwapi_sql_criteria::_equal('contact_id', phpgwapi_sql::integer($cid)), $action);
		}

		/**
		 * Add a contact and it's data
		 *
		 * If account_person_id will be null for each person that is not a user.
		 * @param mixed $type Type of contact, for either `organzation' or `person'
		 * @param array $principal Principal information for contact, its depends on $type, but is according which each definition
		 * @param array $comms Information relative to communication media
		 * @param array $location Information relative to locations.
		 * @param array $categeries Catagories in which contact must be added.
		 * @param array $others Others fields to be added.
		 * @param array $contact_relations the org_id or the person_id for this person or organzation
		 * @param array $notes Notes to be added.
		 * @return integer New contact ID
		 */
		function add_contact( $type, $principal = array(), $comms = array(), $locations = array(), $categories = array(), $others = array(), $contact_relations = array(), $notes = array() )
		{
			$this->contact = createObject('phpgwapi.contact_central');

			// addressmaster is a sane default
			$owner = -3;
			if (isset($principal['owner']) && $principal['owner'])
			{
				$owner = (int)$principal['owner'];
			}
			else if (isset($GLOBALS['phpgw_info']['user']['account_id']))
			{
				$owner = (int)$GLOBALS['phpgw_info']['user']['account_id'];
			}

			if (!isset($principal['preferred_org']))
			{
				$principal['preferred_org'] = '';
			}

			if (!isset($principal['preferred_address']))
			{
				$principal['preferred_address'] = '';
			}

			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->lock_table($this->contact->table, '', true);
			}

			$this->contact->insert(array
				(
				'owner' => $owner,
				'access' => $principal['access'],
				'cat_id' => $this->get_categories($categories),
				'contact_type' => $type
				), PHPGW_SQL_RUN_SQL);

			$cid = $this->last_id('contact', 'contact_id');
			$this->unlock_table();
			$add_type = $this->search_contact_type_id($type);
			switch ($add_type)
			{
				case $this->_contact_person:
					$this->add_person($principal, $cid, PHPGW_SQL_RUN_SQL);
					$this->add_orgs_for_person($contact_relations, $principal['preferred_org'], $principal['preferred_address'], $cid, PHPGW_SQL_RUN_SQL);
					break;
				case $this->_contact_org:
					$this->add_org($principal, $cid, PHPGW_SQL_RUN_SQL);
					$this->add_people_for_organzation($contact_relations, $cid, PHPGW_SQL_RUN_SQL);
					break;
			}

			if (is_array($comms) && count($comms))
			{
				foreach ($comms as $comm)
				{
					$comm['comm_preferred'] = isset($comm['comm_preferred']) && $comm['comm_preferred'] ? $comm['comm_preferred'] : 'N';
					$this->add_communication_media($comm, $cid, PHPGW_SQL_RUN_SQL);
					$this->unlock_table();
				}
			}
			if (is_array($locations) && count($locations))
			{
				foreach ($locations as $location)
				{
					$this->add_location($location, $cid, PHPGW_SQL_RUN_SQL);
					$this->unlock_table();
				}
			}

			if (is_array($others) && count($others))
			{
				foreach ($others as $other)
				{
					$this->add_others($other, $cid, PHPGW_SQL_RUN_SQL);
					$this->unlock_table();
				}
			}
			if (is_array($notes) && count($notes))
			{
				foreach ($notes as $note)
				{
					$this->add_note($note, $cid, PHPGW_SQL_RUN_SQL);
					$this->unlock_table();
				}
			}

			if ($add_type == $this->_contact_person)
			{
				$this->finalize_add($cid);
			}
			return $cid;
		}

		/**
		 * Add an organization and it's data
		 *
		 * @param array $principal Principal information for organization
		 * @param integer $cid Organization id
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return SQL insert string or nothing
		 */
		function add_org( $principal, $cid, $action = PHPGW_SQL_RETURN_SQL )
		{
			$this->org = createObject('phpgwapi.contact_org');
			if ($action == PHPGW_SQL_RUN_SQL)
			{
				if ($this->db->get_transaction())
				{
					$this->global_lock = true;
				}
				else
				{
					$this->lock_table($this->org->table, '', true);
				}
			}

			$principal['org_creaton'] = $this->get_mkdate();
			$principal['org_creatby'] = $this->get_user_id();
			$principal['org_modon'] = $this->get_mkdate();
			$principal['org_modby'] = $this->get_user_id();

			$execute = $this->_add($principal, 'org', 'org_id', $cid, $action);
			$this->unlock_table();
			return $execute;
		}

		/**
		 * Add a person and it's data
		 *
		 * @param array $principal Principal information for person
		 * @param integer $cid person id
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return SQL insert string or nothing
		 */
		function add_person( $principal, $cid, $action = PHPGW_SQL_RETURN_SQL )
		{
			$this->person = createObject('phpgwapi.contact_person');
			if ($action == PHPGW_SQL_RUN_SQL)
			{
				if ($this->db->get_transaction())
				{
					$this->global_lock = true;
				}
				else
				{
					$this->lock_table($this->person->table, '', true);
				}
			}

			$principal['per_creaton'] = $this->get_mkdate();
			$principal['per_creatby'] = $this->get_user_id();
			$principal['per_modon'] = $this->get_mkdate();
			$principal['per_modby'] = $this->get_user_id();

			$execute = $this->_add($principal, 'person', 'person_id', $cid, $action);
			$this->unlock_table();
			//$this->finalize_add($cid);
			return $execute;
		}

		/**
		 * Add a people for a organizations
		 *
		 * @param array $people People id which you want to insert
		 * @param integer $cid Organization id
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return SQL insert string or nothing
		 */
		function add_people_for_organzation( $people, $cid, $action = PHPGW_SQL_RETURN_SQL )
		{
			if (!is_array($people))
			{
//					echo "There is no any person to add in your array";
//					This must be a critical error to stop everything?
//					$GLOBALS['phpgw']->exit();
				return;
			}

			$sql = array();
			foreach ($people as $person)
			{
				$orgs = $this->has_preferred_org($person);
				$this->relations = createObject('phpgwapi.contact_org_person');
				$data['my_person_id'] = $person;
				$data['my_creaton'] = $this->get_mkdate();
				$data['my_creatby'] = $this->get_user_id();
				if (count($orgs[0]) == 0)
				{
					$data['my_preferred'] = 'Y';
				}
				else
				{
					$data['my_preferred'] = 'N';
				}

				if ($action == PHPGW_SQL_RUN_SQL)
				{
					if ($this->db->get_transaction())
					{
						$this->global_lock = true;
					}
					else
					{
						$this->lock_table($this->relations->table, '', true);
					}
				}
				$sql[] = $this->_add($data, 'relations', 'my_org_id', $cid, $action);
				$this->unlock_table();
			}
			return $sql;
		}

		/**
		 * Add a organizations for a person
		 *
		 * @param array $oragnizations Organizations id which you want to insert
		 * @param integer $preferred_org Organization id from preferred organization
		 * @param integer $addr_id Address id from preferred organization
		 * @param integer $cid Person id
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return SQL insert string or nothing
		 */
		function add_orgs_for_person( $organizations, $preferred_org, $addr_id, $cid, $action = PHPGW_SQL_RETURN_SQL )
		{
			$sql = array();
			if (is_array($organizations))
			{
				foreach ($organizations as $org)
				{
					$this->relations = createObject('phpgwapi.contact_org_person');
					$data['my_org_id'] = $org;
					$data['my_addr_id'] = $addr_id;
					if ($preferred_org == $org)
					{
						$data['my_preferred'] = 'Y';
					}
					else
					{
						$data['my_preferred'] = 'N';
					}
					$data['my_creaton'] = $this->get_mkdate();
					$data['my_creatby'] = $this->get_user_id();
					if ($action == PHPGW_SQL_RUN_SQL)
					{
						if ($this->db->get_transaction())
						{
							$this->global_lock = true;
						}
						else
						{
							$this->lock_table($this->relations->table, '', true);
						}
					}
					$sql[] = $this->_add($data, 'relations', 'my_person_id', $cid, $action);
					$this->unlock_table();
				}
			}
			return $sql;
		}

		/**
		 * Add a locations for a contact
		 *
		 * @param array $location Locations information for contact
		 * @param integer $cid contact id
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return SQL insert string or nothing
		 */
		function add_location( $location, $cid, $action = PHPGW_SQL_RETURN_SQL )
		{
			$addr = $this->has_preferred_location($cid);
			$this->location = createObject('phpgwapi.contact_addr');
			if ($action == PHPGW_SQL_RUN_SQL)
			{
				if ($this->db->get_transaction())
				{
//					$this->global_lock = true;
				}
				else
				{
//					$this->lock_table($this->location->table, '', true);
				}
			}
			if (count($addr[0]) == 0)
			{
				$location['addr_preferred'] = 'Y';
			}
			else
			{
				$location['addr_preferred'] = 'N';
			}
			$location['addr_creatby'] = $this->get_user_id();
			$location['addr_modby'] = $this->get_user_id();
			$location['addr_creaton'] = $this->get_mkdate();
			$location['addr_modon'] = $this->get_mkdate();

			unset($location['key_addr_id']);

			return $this->_add($location, 'location', 'addr_contact_id', $cid, $action);
		}

		/**
		 * Add a communications for a contact
		 *
		 * @param array $comm Communications information for contact
		 * @param integer $cid contact id
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return SQL insert string or nothing
		 */
		function add_communication_media( $comm, $cid = '', $action = PHPGW_SQL_RETURN_SQL )
		{
			$this->comm = createObject('phpgwapi.contact_comm');
			if ($action == PHPGW_SQL_RUN_SQL)
			{
				if ($this->db->get_transaction() && !$this->local_lock)
				{
					$this->global_lock = true;
				}
				else
				{
					$this->lock_table($this->comm->table, '', true);
				}
			}

			$comm['comm_creatby'] = $this->get_user_id();
			$comm['comm_modby'] = $this->get_user_id();
			$comm['comm_creaton'] = $this->get_mkdate();
			$comm['comm_modon'] = $this->get_mkdate();

			unset($comm['key_comm_id']);

			return $this->_add($comm, 'comm', 'comm_contact_id', $cid, $action);
		}

		/**
		 * Add a note for a contact
		 *
		 * @param array $note Note information for contact
		 * @param integer $cid contact id
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return SQL insert string or nothing
		 */
		function add_note( $note, $cid = '', $action = PHPGW_SQL_RETURN_SQL )
		{
			$this->note = createObject('phpgwapi.contact_note');
			if ($action == PHPGW_SQL_RUN_SQL)
			{
				if ($this->db->get_transaction())
				{
					$this->global_lock = true;
				}
				else
				{
					$this->lock_table($this->note->table, '', true);
				}
			}

			$note['note_creatby'] = $this->get_user_id();
			$note['note_modby'] = $this->get_user_id();
			$note['note_creaton'] = $this->get_mkdate();
			$note['note_modon'] = $this->get_mkdate();

			//unset($comm['key_note_id']);

			return $this->_add($note, 'note', 'note_contact_id', $cid, $action);
		}

		/**
		 * Add a others for a contact
		 *
		 * @param array $others Others information for contact
		 * @param integer $cid contact id
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return SQL insert string or nothing
		 */
		function add_others( $others, $cid = '', $action = PHPGW_SQL_RETURN_SQL )
		{
			$this->others = createObject('phpgwapi.contact_others');
			if ($action == PHPGW_SQL_RUN_SQL)
			{
				if ($this->db->get_transaction())
				{
					$this->global_lock = true;
				}
				else
				{
					$this->lock_table($this->others->table, '', true);
				}
			}

			unset($others['key_other_id']);

			return $this->_add($others, 'others', 'other_contact_id', $cid, $action);
		}

		/**
		 * Delete all data of this contact_id
		 *
		 * @param mixed $cid The contact_id or any list of contacts_id
		 * @param string $contact_type Contact type
		 * @param boolean $transaction Use transaction
		 * @return boolean|array Error reason strings from different applications
		 */
		function delete( $cid, $contact_type = '', $transaction = True )
		{
			// check for hooks saying "do not delete"
			if (!is_object($GLOBALS['phpgw']->hooks))
			{
				$GLOBALS['phpgw']->hooks = createObject('phpgwapi.hooks');
			}
			$hook_response = $GLOBALS['phpgw']->hooks->process(array(
				'location' => 'delete_addressbook',
				'contact_id' => $cid)
			);

			$negative_apps = false;
			foreach ($hook_response as $application => $response)
			{
				if (is_array($response))
				{
					if (!$response['can_delete'])
					{
						$negative_apps[$application] = $response['reason'];
					}
				}
			}

			if ($negative_apps)
			{
				$return_value = $negative_apps;
			}
			else
			{
				$type = ($contact_type) ? $contact_type : $this->search_contact_type_id($this->get_type_contact($cid));
				$type = ($type == $this->_contact_person) ? 'person' : 'org';


				if (empty($entity_keys))
				{
					$entity_keys = array('contact_id',
						// $type= org | person therefore: here goes person_id or org_id
						$type . '_id',
						// in addition: here goes my_person_id or my_org_id
						'my_' . $type . '_id',
						'addr_contact_id',
						'note_contact_id',
						'other_contact_id',
						'comm_contact_id');
				}
				if ($transaction)
				{
					$this->transaction_begin();
				}
				foreach ($entity_keys as $key)
				{
					$return_value = $this->_delete(phpgwapi_sql_criteria::_equal($key, $cid), PHPGW_SQL_RUN_SQL);
				}
				if ($transaction)
				{
					$this->transaction_end();
				}

				if ($contact_type == $this->_contact_person)
				{
					$this->finalize_delete($cid);
				}
			}
			return $return_value;
		}

		/**
		 * Delete principal data of this contact_id
		 *
		 * @param mixed $cid The contact_id or any list of contacts_id
		 * @param mixed $contact_type Contact type
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL
		 * @return boolean|array Error reason strings from different applications
		 */
		function delete_contact( $cid, $contact_type = '', $action = PHPGW_SQL_RUN_SQL )
		{
			// check for hooks saying "do not delete"
			$hook_response = $GLOBALS['phpgw']->hooks->process(array(
				'location' => 'delete_addressbook',
				'contact_id' => $cid)
			);
			$negative_apps = false;
			foreach ($hook_response as $application => $response)
			{
				if (is_array($response))
				{
					if (!$response['can_delete'])
					{
						$negative_apps[$application] = $response['reason'];
					}
				}
			}

			if ($negative_apps)
			{
				$return_value = $negative_apps;
			}
			else
			{
				$type = ($contact_type) ? $contact_type : $this->search_contact_type_id($this->get_type_contact($cid));
				$type = ($type == $this->_contact_person) ? 'person' : 'org';
				if ($action == PHPGW_SQL_RUN_SQL)
				{
					$this->transaction_begin();
				}
				$return_value[] = $this->_delete(phpgwapi_sql_criteria::_equal('contact_id', $cid), $action);
				$return_value[] = $this->_delete(phpgwapi_sql_criteria::_equal($type . '_id', $cid), $action);
				if ($action == PHPGW_SQL_RUN_SQL)
				{
					$this->transaction_end();
				}
			}
			return $return_value;
		}

		/**
		 * Delete relations between a person and their related orgs
		 *
		 * @param mixed $cid The contact_id or any list of contacts_id
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL
		 * @return string Delete SQL string
		 */
		function delete_orgs_by_person( $cid, $action = PHPGW_SQL_RUN_SQL )
		{
			return $this->_delete(phpgwapi_sql_criteria::_equal('my_person_id', $cid), $action);
		}

		/**
		 * Delete relations between an org and their related person
		 *
		 * @param mixed $cid The contact_id or any list of contacts_id
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL
		 * @return string Delete SQL string
		 */
		function delete_people_by_org( $cid, $action = PHPGW_SQL_RUN_SQL )
		{
			return $this->_delete(phpgwapi_sql_criteria::_equal('my_org_id', $cid), $action);
		}

		/**
		 * Delete relations between an org and their related person
		 *
		 * @param mixed $org_id
		 * @param mixed $person_id
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL
		 * @return string Delete SQL string
		 */
		function delete_org_person_relation( $org_id, $person_id, $action = PHPGW_SQL_RUN_SQL )
		{
			$relations = createObject('phpgwapi.contact_org_person');
			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->lock_table($relations->table, '', true);
			}

			$criteria = $relations->entity_criteria(phpgwapi_sql_criteria::token_and(phpgwapi_sql_criteria::_equal('my_org_id', $org_id), phpgwapi_sql_criteria::_equal('my_person_id', $person_id)));
			$sql = $relations->delete($criteria, $action);
			$this->unlock_table();
			return $sql;
		}

		/**
		 * Delete the address information of contact.
		 *
		 * @param integer|array $cid contact_id or list of contact_ids
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL
		 * @return string Delete SQL string
		 */
		function delete_locations( $cid, $action = PHPGW_SQL_RETURN_SQL )
		{
			return $this->_delete(phpgwapi_sql_criteria::_equal('addr_contact_id', $cid), $action);
		}

		/**
		 * Delete the communication media for this contact.
		 *
		 * @param integer|array $cid contact_id or list of contact_ids
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL
		 * @return string Delete SQL string
		 */
		function delete_comms( $cid, $action = PHPGW_SQL_RETURN_SQL )
		{
			return $this->_delete(phpgwapi_sql_criteria::_equal('comm_contact_id', $cid), $action);
		}

		/**
		 * Delete the others fields for this contact.
		 *
		 * @param integer|array $cid contact_id or list of contact_ids
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL
		 * @return string Delete SQL string
		 */
		function delete_others( $cid, $action = PHPGW_SQL_RETURN_SQL )
		{
			return $this->_delete(phpgwapi_sql_criteria::_equal('other_contact_id', $cid), $action);
		}

		/**
		 * Delete the notes for this contact.
		 *
		 * @param integer|array $cid Contact_id or list of contact_ids
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL
		 * @return string Delete SQL string
		 */
		function delete_notes( $cid, $action = PHPGW_SQL_RETURN_SQL )
		{
			return $this->_delete(phpgwapi_sql_criteria::_equal('note_contact_id', $cid), $action);
		}

		/**
		 * Delete the specified communication media.
		 *
		 * @param integer|array $id Key of the comm media what you want
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL
		 * @return string Delete SQL string
		 */
		function delete_specified_comm( $id, $action = PHPGW_SQL_RETURN_SQL )
		{
			return $this->_delete(phpgwapi_sql_criteria::_equal('key_comm_id', $id), $action);
		}

		/**
		 * Delete the specified address.
		 *
		 * @param integer|array $id Key of the address what you want
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL
		 * @return string Delete SQL string
		 */
		function delete_specified_location( $id, $action = PHPGW_SQL_RETURN_SQL )
		{
			return $this->_delete(phpgwapi_sql_criteria::_equal('key_addr_id', $id), $action);
		}

		/**
		 * Delete the specified others field.
		 *
		 * @param integer|array $id Key of the other field what you want
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL
		 * @return string Delete SQL string
		 */
		function delete_specified_other( $id, $action = PHPGW_SQL_RETURN_SQL )
		{
			return $this->_delete(phpgwapi_sql_criteria::_equal('key_other_id', $id), $action);
		}

		/**
		 * Delete the specified note.
		 *
		 * @param integer|array $id Key of the note what you want
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL
		 * @return string Delete SQL string
		 */
		function delete_specified_note( $id, $action = PHPGW_SQL_RETURN_SQL )
		{
			return $this->_delete(phpgwapi_sql_criteria::_equal('key_note_id', $id), $action);
		}

		/**
		 * Delete all contacts of an owner
		 *
		 * @param integer $owner Ownder of contacts
		 * @internal This is for the admin script deleteaccount.php
		 */
		function delete_all( $owner = 0 )
		{
			$owner = intval($owner);
			if ($owner)
			{
				$contacts = $this->get_contacts_by_owner($owner);
				if (is_array($contacts))
				{
					foreach ($contacts as $row)
					{
						$this->delete($row['contact_id']);
					}
				}
			}
		}

		/**
		 * This function is the wrapper for getting queries or the sql string
		 *
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @param integer $line where this function is called, usefull for debug
		 * @param integer $file where this function is called, usefull for debug
		 * @return array|string Array with records or string with sql query
		 * @access private
		 */
		function get_query( $action = PHPGW_SQL_RUN_SQL, $line = __LINE__, $file = __FILE__ )
		{
			switch ($action)
			{
				case PHPGW_SQL_RETURN_RECORDS:
				case PHPGW_SQL_RUN_SQL:
					return $this->get_records($line, $file);

				case PHPGW_SQL_RETURN_SQL:
					return $this->get_sql();
			}
		}

		/**
		 * This make a common task in many functions. Get the records resulted by get_sql method.
		 *
		 * @param integer $line where this function is called, usefull for debug
		 * @param integer $file where this function is called, usefull for debug
		 * @return array Recordset with all records in the database.
		 * @access private
		 */
		function get_records( $line, $file )
		{
			$records = array();
			$this->execute_query($this->get_sql(), $line, $file);
			while ($this->db->next_record())
			{
				$records[] = $this->db->Record;
			}
			return $records;
		}

		/**
		 * Get records from database for one field
		 *
		 * @param string $field Database field name
		 * @param integer $line Program line number that executes this method
		 * @param string $file File name from which this method was called
		 * @return array Database results for given field name
		 * @access private
		 */
		function get_records_by_field( $field, $line = __LINE__, $file = __FILE__ )
		{
			$record = false;
			$this->execute_query($this->get_sql(), $line, $file);
			while ($this->db->next_record())
			{
				$record[] = $this->db->f($field);
			}
			return $record;
		}

		/**
		 * Execute SQL query string
		 *
		 * @param string $query SQL query string
		 * @param integer $line Program line number that executes this method
		 * @param string $file File name from which this method was called
		 * @access private
		 */
		function execute_query( $query, $line = __LINE__, $file = __FILE__ )
		{
			if (!isset($this->db))
			{
				$this->db = &$GLOBALS['phpgw']->db;
			}
			$this->db->query($query, $line, $file);
		}

		/**
		 * Execute a list of SQL queries
		 *
		 * @param array $queries List of SQL queries
		 * @access private
		 */
		function execute_queries( $queries = array() )
		{
			if (is_array($queries))
			{
				foreach ($queries as $query)
				{
					$this->db->query($query, -1, -1);
				}
			}
		}

		/**
		 * Retrieve all the locations of $contacts_id
		 *
		 * @param integer|array $contact_id The contact id that will be used for search his data.
		 * @param array $data This is the information that want to retrieve, according whith phpGroupware Address Specification.
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return array Asociative with id and all data that we requested
		 */
		function get_locations( $contact_id, $data, $action = PHPGW_SQL_RUN_SQL )
		{
			$this->request('contact_id');
			$this->request($data);
			$this->criteria(array('addr_contact_id' => $contact_id));
			return $this->get_query($action, __LINE__, __FILE__);
		}

		function get_orgs_by_cat( $cat_id )
		{
			$this->request('org_id');
			$this->request('org_name');
			$this->criteria(array('cat_id' => $cat_id));
			$sql = $this->get_sql();
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$orgs = $this->db->Record;
			}
			return $orgs;
		}

		function get_cats_by_person( $person_id )
		{
			$this->request('cat_id');
			$this->criteria(array('contact_id' => $person_id));
			$cats = $this->get_records_by_field('cat_id', __LINE__, __FILE__);

			if ($cats[0])
			{
				$cats_array = explode(',', $cats[0]);
			}
			else
			{
				$cats_array = array();
			}

			return $cats_array;
		}

		function get_cats_by_org( $org_id )
		{
			$this->request('cat_id');
			$this->criteria(array('org_id' => $org_id));
			$cats = $this->get_records_by_field('cat_id', __LINE__, __FILE__);
			if ($cats[0])
			{
				$cats_array = explode(',', $cats[0]);
			}
			else
			{
				$cats_array = array();
			}
			return $cats_array;
		}

		function get_cats( $fields )
		{
			$this->request($fields);
			$this->db->query($this->get_sql(), __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$cats[] = $this->db->Record;
			}

			return $cats;
		}

		function get_categories( $categories )
		{
			if (is_array($categories) && !count($categories))
			{
				return '';
			}
			if (is_array($categories) && count($categories))
			{
				return count($categories) > 1 ? ',' . implode(',', $categories) . ',' : $categories[0];
			}
			else
			{
				return $categories;
			}
		}

		function get_addr_by_contact( $contact_id )
		{
			$this->request('addr_address');
			$this->criteria(array('addr_contact_id' => $contact_id));
			return $this->get_query(PHPGW_SQL_RUN_SQL, __LINE__, __FILE__);
		}

		/**
		 * Read phone number for one contact
		 *
		 * @param $contacts_id Contact ID
		 * @deprecated
		 */
		function get_phone_by_contact( $contacts_id )
		{
			$this->get_phone($contacts_id);
		}

		/**
		 * Create a criteria with or and like operators for all the fields
		 *
		 * @param array $fields Database field names
		 * @return string Search result
		 */
		function search_by_any( $fields )
		{
			foreach ($fields as $field => $value)
			{
				if ($value == NULL)
				{
					continue;
				}
				if (is_array($value))
				{
					if (in_array('append_or', $value) || in_array('append_and', $value))
					{
						$elements[] = $value;
					}
					else
					{
						$elements[] = phpgwapi_sql_criteria::in($field, $value);
					}
				}
				else
				{
					$elements[] = phpgwapi_sql_criteria::token_has($field, $value);
				}
			}
			return phpgwapi_sql_criteria::_append_or($elements);
		}

		/**
		 * Create a criteria with `and' and the corresponding operator of each field.
		 *
		 * @param array $fields Database field names
		 * @return string Search result
		 */
		function search_by_all( $fields )
		{
			foreach ($fields as $field => $value)
			{
				if ($value == NULL)
				{
					continue;
				}

				if (is_array($value))
				{
					if (in_array('append_or', $value) || in_array('append_and', $value))
					{
						$elements[] = $value;
					}
					else
					{
						if (count($value) == 1)
						{
							$elements[] = phpgwapi_sql_criteria::_equal($field, current($value));
						}
						else
						{
							$elements[] = phpgwapi_sql_criteria::_in($field, $value);
						}
					}
				}
				else
				{
					$elements[] = phpgwapi_sql_criteria::_equal($field, $value);
				}
			}
			return phpgwapi_sql_criteria::_append_and($elements);
		}

		/**
		 * Get the all contacts which are from specified owner
		 *
		 * @param integer $owner The owner what you can find
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return mixed SQL string or records with note_type_id and not_description
		 */
		function get_contacts_by_owner( $owner, $action = PHPGW_SQL_RUN_SQL )
		{
			$this->request('contact_id');
			$this->criteria(array('owner' => $owner));
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Retrieve Data for categories given, taking care that are person.
		 *
		 * @param mixed $categories_id
		 * @param mixed $data
		 * @param mixed $contact_type
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return array Asociative with id and all data that we requested
		 */
		function get_contact_by_categories( $categories_id, $data, $contact_type, $action = PHPGW_SQL_RUN_SQL )
		{
			$this->request('contact_id');
			$this->request($data);
			if ($contact_type)
			{
				$this->criteria(array('cat_cat_id' => $categories_id, 'contact_type' => $this->get_type($contact_type)));
			}
			else
			{
				$this->criteria(array('cat_cat_id' => $categories_id));
			}
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Retrieve data from the categories that the contact_id belongs
		 *
		 * @param mixed $person_id Contacts Id which want his categories.
		 * @param mixed $data list of fields that we want retrieve
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return array|string Array with records or string with SQL query
		 * @access private
		 */
		function _get_categories( $person_id, $data, $action = PHPGW_SQL_RUN_SQL )
		{
			$this->request('contact_id');
			$this->request($data);
			$this->criteria(array('my_person_id' => $person_id));
			return $this->get_query($action, __LINE__, __FILE__);
		}

		/**
		 * Decide if contact is a user or not
		 *
		 * @param integer $contact_id Contact id which want to check.
		 * @return boolean|integer Account id or false
		 */
		function is_user( $contact_id )
		{
			$account_id = $GLOBALS['phpgw']->accounts->search_person($contact_id);

			if ($account_id == 0)
			{
				return FALSE;
			}
			else
			{
				return $account_id;
			}
		}

		function get_account_id( $contact_id )
		{

			return (int)$this->is_user($contact_id);
			/*
			  $accounts = $GLOBALS['phpgw']->accounts->get_list();
			  foreach($accounts as $account_data)
			  {
			  if($account_data->id == $contact_id)
			  {
			  $account_id = $account_data->id;
			  break;
			  }
			  }

			  return $account_id;
			 */
		}

		/**
		 * Decide if the person has preferred organization
		 *
		 * @param integer $person_id Person id which want to check.
		 * @return array|boolean False if has't preferred org or array with all id which has preferred org.
		 */
		function has_preferred_org( $person_id )
		{
			$this->request('my_person_id');
			$this->request('my_org_id');
			$this->criteria(array('my_person_id' => $person_id,
				'my_preferred' => 'Y'));
			$persons = $this->get_records(__LINE__, __FILE__);
			return $persons;
		}

		/**
		 * Decide if the contact has preferred location
		 *
		 * @param integer $contact_id Contact id which want to check.
		 * @return array|boolean False if has't preferred org or array with all id which has preferred org.
		 */
		function has_preferred_location( $contact_id )
		{
			$this->request('addr_contact_id');
			$this->request('key_addr_id');
			$this->criteria(array('addr_contact_id' => $contact_id,
				'addr_preferred' => 'Y'));
			$locations = $this->get_records(__LINE__, __FILE__);
			return $locations;
		}

		/**
		 * Decide if contact exist
		 *
		 * @param integer $contact_id Contact id which want to check.
		 * @return boolean TRUE if contact exist false otherwise
		 */
		function exist_contact( $contact_id )
		{
			$this->request('count_contacts');
			$this->criteria(array('contact_id' => $contact_id));
			$contact = $this->get_records(__LINE__, __FILE__);
			if ($contact[0]['count_contacts'] == 0)
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}

		function exist_org_person_relation( $org_id, $person_id )
		{
			$this->request('my_org_id');
			$this->request('my_person_id');
			$this->criteria(array('my_org_id' => $org_id,
				'my_person_id' => $person_id));

			$contact = $this->get_records(__LINE__, __FILE__);
			if ($contact[0]['my_org_id'] == 0)
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}

		/**
		 * Decide if contacts are user or not, and retrieve its data.
		 *
		 * If account_person_id will be null for each person that is not a user.
		 * @param mixed $person_id Person id which want to check.
		 * @param array $data the info that you want of this contact
		 * @return array With person_id, account_person_id and all data of contacts that we wanted.
		 */
		function are_users( $person_id, $data = '' )
		{
			if ($data != '')
			{
				$this->request($data);
			}
			$this->request(array('contact_id'));
			$this->criteria(array('contact_id' => $person_id));
			$query = $this->get_query(PHPGW_SQL_RETURN_SQL);
			$this->db->query($query, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$contact = $this->db->Record;
				$contact['account_id'] = $GLOBALS['phpgw']->accounts->search_person($contact['contact_id']);
				$contacts[] = $contact;
			}
			return $contacts;
		}

		/**
		 * Get the organizations from one person
		 *
		 * @param integer $person_id Id of the contact type person
		 * @return array Asociative array with id and all data that you requested
		 */
		function get_orgs_by_person( $person_id )
		{
			return $this->get_organizations_by_person($person_id);
		}

		/**
		 * Get the all contacts which are users of phpGroupWare
		 *
		 * @return mixed SQL string or records with note_type_id and not_description
		 */
		function get_contacts_which_are_users()
		{
			return $this->get_system_contacts();
		}

		function get_person_properties()
		{
			$person = createObject('phpgwapi.contact_person');
			return $person->get_false_fields();
		}

		function get_organization_properties()
		{
			$org = createObject('phpgwapi.contact_org');
			return $org->get_false_fields();
		}

		/**
		 * Get all persons of specified organization
		 *
		 * @param integer $org_id ID of the organization what you want
		 * @return array Array with records
		 */
		function get_persons_by_org( $org_id )
		{
			$this->request(array('organizations', 'person_id', 'per_full_name', 'per_first_name',
				'per_middle_name', 'per_last_name'));
			$this->criteria(array('org_id' => $org_id));
			return $this->get_query(PHPGW_SQL_RETURN_SQL, __LINE__, __FILE__);
		}

		/**
		 * Search a value into an array
		 *
		 * @param string $field_to_search Field into what you want to find
		 * @param string $value_to_search Value what you want
		 * @param string $field Field what you want return
		 * @param string $catalog Catalog name into you want to find
		 * @return string The value which you requiere in $field
		 */
		function search_catalog( $field_to_search, $value_to_search, $field, $catalog )
		{
			if (is_array($this->$catalog))
			{
				foreach ($this->$catalog as $values)
				{
					if ($values[$field_to_search] == $value_to_search)
					{
						return $values[$field];
					}
				}
			}
		}

		/**
		 * Retrieve the criteria for index addressbook implementation.
		 *
		 * Could be used in others parts but thinks that it was created
		 * with that propouse. This looks for the categories and for the acl perms.
		 *
		 * @param integer $owner This is the actual user of phpgroupware
		 *
		 * @param mixed $access This is limit the search for private only
		 * (with PHPGW_CONTACTS_PRIVATE), all my contacts(PHPGW_CONTACTS_MINE)
		 * or all the records I have access (PHPGW_CONTACTS_ALL).
		 *
		 * @param integer $categories if have any value then limit the result
		 * to contacts that belongs to this categories (and childs)
		 * PHPGW_CONTACTS_CATEGORIES_ALL if want not use categories criterias.
		 *
		 * @param array $search_fields This is used in search, is the<br />
		 * list of fields to search. Also this list is used to know if we<br />
		 * need to set the criteria for search the prefered addresses for contacts.
		 *
		 * @param string $pattern Is the string that will be used to search in
		 * all fields of $search_fields, set value to this param without
		 * setting $search_field is useless.
		 *
		 * @param array $show_fields Database fields to show
		 * @return string SQL string
		 */
		function criteria_for_index( $owner, $access = PHPGW_CONTACTS_ALL, $categories = PHPGW_CONTACTS_CATEGORIES_ALL, $search_fields = array(), $pattern = '', $show_fields = array() )
		{
			if (!is_numeric($owner) || intval($owner) == 0)
			{
				return;
			}
			switch ($access)
			{
				case PHPGW_CONTACTS_MINE:
					$criteria = phpgwapi_sql_criteria::_equal('owner', $owner);
					break;
				case PHPGW_CONTACTS_PRIVATE:
					$criteria = phpgwapi_sql_criteria::token_and(phpgwapi_sql_criteria::_equal('access', 'private'), phpgwapi_sql_criteria::_equal('owner', $owner));
					break;
				case PHPGW_CONTACTS_ALL:
				default:
					$criteria = phpgwapi_sql_criteria::token_or(phpgwapi_sql_criteria::token_and(phpgwapi_sql_criteria::_equal('access', 'public'), phpgwapi_sql_criteria::_in('owner', $this->get_contacts_shared($owner, PHPGW_ACL_READ))), phpgwapi_sql_criteria::_equal('owner', $owner));
			}
			if ($categories != PHPGW_CONTACTS_CATEGORIES_ALL)
			{
				if (!is_array($categories))
				{
					$categories = array($categories);
				}

				$categories_array = array_merge($categories, (array)$this->get_sub_cats($categories));
				if (count($categories_array) > 0)
				{
					foreach ($categories_array as $cat_id)
					{
						$search_categories[] = phpgwapi_sql_criteria::token_or(phpgwapi_sql_criteria::_equal('sel_cat_id', $cat_id), phpgwapi_sql_criteria::token_has('sel_cat_id', ',' . $cat_id . ','));
					}
					$categories_criteria = phpgwapi_sql_criteria::_append_or($search_categories);
					$criteria = phpgwapi_sql_criteria::token_and($criteria, $categories_criteria);
				}
			}

			$location = createObject('phpgwapi.contact_addr');
			$search_fields = (empty($search_fields) || !is_array($search_fields)) ? array() : $search_fields;
			$show_fields = (empty($show_fields) || !is_array($show_fields)) ? array() : $show_fields;
			$search_count = count(array_intersect($location->get_false_fields(), $search_fields));
			$show_count = count(array_intersect($location->get_false_fields(), $show_fields));

			if ($search_count <= 0 && $show_count > 0)
			{
				$addr_preferred_criteria = phpgwapi_sql_criteria::token_or(phpgwapi_sql_criteria::_equal('addr_pref_val', 'Y'), phpgwapi_sql_criteria::_is_null('key_addr_id'));
				$criteria = phpgwapi_sql_criteria::token_and($criteria, $addr_preferred_criteria);
			}

			if (isset($search_fields['comm_media']) && is_array($search_fields['comm_media']) && count($search_fields['comm_media']) > 0)
			{
				$search_fields_comms = $search_fields['comm_media'];
			}
			unset($search_fields['comm_media']);

			$index = array_search('per_full_name', $search_fields);
			if ($index !== False && $index !== Null)
			{
				unset($search_fields[$index]);
				$search_fields[] = 'per_first_name';
				$search_fields[] = 'per_last_name';
				$search_fields[] = 'per_middle_name';
			}

			if (count($search_fields) > 0 && $pattern)
			{
				foreach ($search_fields as $field)
				{
					$search_array[] = phpgwapi_sql_criteria::token_has($field, $pattern);
				}

				$criteria = phpgwapi_sql_criteria::token_and($criteria, phpgwapi_sql_criteria::_append_or($search_array));
			}

			if ($pattern && isset($search_fields_comms) && is_array($search_fields_comms) && count($search_fields_comms) > 0)
			{
				foreach ($search_fields_comms as $field)
				{
					$search_array_comm[] = phpgwapi_sql_criteria::token_and(phpgwapi_sql_criteria::token_has('comm_data', $pattern), phpgwapi_sql_criteria::_equal('comm_descr', $this->search_comm_descr($field)));
				}
				$criteria = phpgwapi_sql_criteria::token_and($criteria, phpgwapi_sql_criteria::_append_or($search_array_comm));
			}
			return $criteria;
		}

		function search( $search_fields, $pattern, $data = 'contact_id' )
		{
			$criteria = array();
			if (count($search_fields) > 0 && $pattern)
			{
				$index = array_search('per_full_name', $search_fields);
				if ($index !== False && $index !== Null)
				{
					unset($search_fields[$index]);
					$search_fields[] = 'per_first_name';
					$search_fields[] = 'per_last_name';
					$search_fields[] = 'per_middle_name';
				}

				foreach ($search_fields as $field)
				{
					$search_array[] = phpgwapi_sql_criteria::token_has($field, $pattern);
				}
				$criteria = phpgwapi_sql_criteria::_append_or($search_array);
			}

			$this->request('contact_id');
			$this->criteria_token($criteria);
			$records = $this->get_records(__LINE__, __FILE__);
			if (is_array($records))
			{
				foreach ($records as $value)
				{
					$info[] = $value['contact_id'];
				}
			}
			return $info;
		}

		function get_contacts_shared( $owner_id, $acl_type = PHPGW_ACL_READ )
		{
			$required_grants = array();
			$this->grants = $GLOBALS['phpgw']->acl->get_grants('addressbook');
			if (isset($GLOBALS['phpgw_info']['server']['addressmaster']) && $GLOBALS['phpgw']->acl->check('addressmaster', 7, 'addressbook'))
			{
				$required_grants[] = $GLOBALS['phpgw_info']['server']['addressmaster'];
			}
			foreach ($this->grants as $owner => $perm)
			{
				if ($this->check_perms($perm, $acl_type))
				{
					if ($owner)
					{
						$required_grants[] = $owner;
					}
				}
			}
			return $required_grants;
		}

		function check_perms( $has, $needed )
		{
			return !!($has & $needed);
		}

		/**
		 * Check if the contact has a specified permission or if is addressmaster
		 *
		 * @param integer $contact_id The contact_id which you want to find
		 * @param integer $needed The permission what you need
		 * @param integer $owner_id The owner_id of the contact
		 * @return boolean True when access is allowed otherwise false
		 */
		function check_acl( $contact_id, $needed, $owner_id = '' )
		{
			$grants = $GLOBALS['phpgw']->acl->get_grants('addressbook');
			if ($owner_id == '')
			{
				$owner_id = $this->get_contact_owner($contact_id);
			}

			if (!isset($grants[$owner_id]))
			{
				$grants[$owner_id] = 0;
			}

			return ($this->check_perms($grants[$owner_id], $needed) || $GLOBALS['phpgw']->acl->check('addressmaster', 7, 'addressbook'));
		}

		/**
		 * Check if the contact has add permissions.
		 *
		 * @param integer $contact_id The contact_id which you want to check
		 * @param integer $owner_id The owner_id of the contact which you want to check
		 * @return boolean True when when adding to this contact is allowed otherwise false
		 */
		function check_add( $contact_id, $owner_id = '' )
		{
			return $this->check_acl($contact_id, PHPGW_ACL_ADD, $owner_id);
		}

		/**
		 * Check if the contact has edit permissions.
		 *
		 * @param integer $contact_id The contact_id which you want to check
		 * @param integer $owner_id The owner_id of the contact which you want to check
		 * @return boolean True when editing this contact is allowed otherwise false
		 */
		function check_edit( $contact_id, $owner_id = '' )
		{
			return $this->check_acl($contact_id, PHPGW_ACL_EDIT, $owner_id);
		}

		/**
		 * Check if the contact has read permissions.
		 *
		 * @param integer $contact_id The contact_id which you want to check
		 * @param integer $owner_id The owner_id of the contact which you want to check
		 * @return boolean TRue when reading this contact is allowed otherwise false
		 */
		function check_read( $contact_id, $owner_id = '' )
		{
			return $this->check_acl($contact_id, PHPGW_ACL_READ, $owner_id);
		}

		/**
		 * Check if the contact has delete permissions.
		 *
		 * @param integer $contact_id The contact_id which you want to check
		 * @param integer $owner_id The owner_id of the contact which you want to check
		 * @return boolean True when deleting this contact is allowed otherwise false
		 */
		function check_delete( $contact_id, $owner_id = '' )
		{
			return $this->check_acl($contact_id, PHPGW_ACL_DELETE, $owner_id);
		}

		/**
		 * Get the owner of the contact.
		 *
		 * @param integer $contact_id The contact_id which you want to find
		 * @return integer Owner of the given contact
		 */
		function get_contact_owner( $contact_id )
		{
			$this->request('owner');
			$this->criteria(array('contact_id' => $contact_id));
			$owner = $this->get_records(__LINE__, __FILE__);
			return (isset($owner[0]['owner']) ? $owner[0]['owner'] : false);
		}

		/**
		 * Copy all contact data to new contact
		 *
		 * @param integer $contact_id Id of the contact what you want to copy
		 * @param integer $type Type Id of the contact what you want to copy
		 * @return integer Id of the new contact
		 */
		function copy_contact( $contact_id, $type = '' )
		{
			$type = ($contact_type) ? $contact_type : $this->get_type_contact($contact_id);
			$copy_type = ($this->search_contact_type_id($type) == $this->_contact_person) ? 'persons' : 'organizations';

			$get_data_type = 'get_principal_' . $copy_type . '_data';

			$principal = $this->$get_data_type($contact_id);
			unset($principal[0]['owner']);
			unset($principal[0]['per_full_name']);

			$cats = explode(",", $principal[0]['cat_id']);
			foreach ($cats as $cat)
			{
				if ($cat)
				{
					$categories[] = $cat;
				}
			}

			$comms = $this->get_comm_contact_data($contact_id);
			$locations = $this->get_addr_contact_data($contact_id);
			$others = $this->get_others_contact_data($contact_id);

			$new_contact_id = $this->add_contact($type, $principal[0], $comms, $locations, $categories, $others);

			switch ($copy_type)
			{
				case $this->tab_main_persons:
					$this->copy_organizations_by_person($contact_id, $new_contact_id);
					break;
				case $this->tab_main_organizations:
					$this->copy_people_by_organizations($contact_id, $new_contact_id);
					break;
			}

			return $new_contact_id;
		}

		/**
		 * Copy all organizatons from person_id to new person_id
		 *
		 * @param integer $person_id Id of the person what you want to copy
		 * @param integer $new_person_id Id of the new person
		 */
		function copy_organizations_by_person( $person_id, $new_person_id )
		{
			$records = $this->get_organizations_by_person($person_id);
			if (is_array($records))
			{
				foreach ($records as $data)
				{
					$this->relations = createObject('phpgwapi.contact_org_person');
					if ($this->db->get_transaction())
					{
						$this->global_lock = true;
					}
					else
					{
						$this->lock_table($this->relations->table, '', true);
					}

					$data['my_creaton'] = $this->get_mkdate();
					$data['my_creatby'] = $this->get_user_id();
					$sql[] = $this->_add($data, 'relations', 'my_person_id', $new_person_id, PHPGW_SQL_RUN_SQL);
					$this->unlock_table();
				}
			}
		}

		/**
		 * Copy all persons from org_id to new org_id
		 *
		 * @param integer $organization_id Id of the organization what you want to copy
		 * @param integer $new_organization_id Id of the new organization
		 */
		function copy_people_by_organizations( $organization_id, $new_organization_id )
		{
			$records = $this->get_people_by_organizations($organization_id);

			if (is_array($records))
			{
				foreach ($records as $data)
				{
					if (!$this->exist_org_person_relation($new_organization_id, $data['my_person_id']))
					{
						$this->relations = createObject('phpgwapi.contact_org_person');
						if ($this->db->get_transaction())
						{
							$this->global_lock = true;
						}
						else
						{
							$this->lock_table($this->relations->table, '', true);
						}

						$data['my_creaton'] = $this->get_mkdate();
						$data['my_creatby'] = $this->get_user_id();
						$sql[] = $this->_add($data, 'relations', 'my_org_id', $new_organization_id, PHPGW_SQL_RUN_SQL);
						$this->unlock_table();
					}
				}
			}
		}

		/**
		 * Create insert SQL statement to insert data from object
		 *
		 * @param mixed $data Data
		 * @param mixed $object Object
		 * @param string $key_field Key for associatove array
		 * @param integer $cid Value for key
		 * @param mixed $action Unknown
		 * @return string Insert SQL string or nothing
		 * @access private
		 */
		function _add( $data, $object, $key_field, $cid = '', $action = PHPGW_SQL_RETURN_SQL )
		{
			// If dont get the $cid, then I hope is in $data array.
			$this->ldebug('_add', array('object' => $object, 'cid' => $cid, 'data' => $data));
			$cid = ($cid) ? $cid : $data[$key_field];
			// Do nothing without contact
			if (empty($cid))
			{
				$this->abort($this->$object->table);
			}
			else
			{
				$data[$key_field] = $cid;
				return $this->$object->insert($data, $action);
			}
		}

		/**
		 * Display the correct translation for the field
		 *
		 * @param string $field The field what you want
		 * @return string Translated field name for display
		 */
		function display_name( $field )
		{
			if (isset($this->contact_fields['showable']) && isset($this->contact_fields['showable'][$field]))
			{
				return lang($this->contact_fields['showable'][$field]);
			}

			if (isset($this->contact_fields['retreivable']) && isset($this->contact_fields['retreivable'][$field]))
			{
				return lang($this->contact_fields['retrievable'][$field]);
			}

			if (isset($this->contact_fields['catalogs']) && isset($this->contact_fields['catalogs'][$field]))
			{
				return lang($this->contact_fields['catalogs'][$field]);
			}
			return lang($field);
		}

		/**
		 * Begin a Transaction to database.
		 *
		 * @internal Create database object if necesary
		 * @access private
		 */
		function transaction_begin()
		{
			if (is_null($this->db))
			{
				$this->db = &$GLOBALS['phpgw']->db;
			}
			if (!$this->trans)
			{
				$this->db->transaction_begin();
				$this->trans = TRUE;
			}
		}

		/**
		 * End actual Transaction
		 *
		 * @access private
		 */
		function transaction_end()
		{
			if (!is_null($this->db))
			{
				$this->db->transaction_commit();
				$this->trans = FALSE;
			}
		}

		function last_id( $entity, $field )
		{
			return $this->db->get_last_insert_id($this->$entity->table, $this->$entity->real_field($field));
		}

		function lock_table( $table, $action = PHPGW_SQL_RUN_SQL, $local_lock = false )
		{
			if ((!isset($this->locked[$table]) || !$this->locked[$table] ) && $action == PHPGW_SQL_RUN_SQL)
			{
				$this->db->lock($table);
				$this->locked[$table] = TRUE;
				if ($local_lock)
				{
					$this->local_lock = true;
				}
			}
		}

		/**
		 * Unlock database table
		 *
		 * @access private
		 */
		function unlock_table()
		{
			if (!$this->global_lock && $this->db->get_transaction())
			{
				$this->db->transaction_commit();
				$this->locked = NULL;
			}

			/*
			  $this->ldebug('unlock_table', array($this->locked), 'dump');
			  if(count($this->locked))
			  {
			  $this->ldebug('unlock_table', array('count' => count($this->locked)));
			  if ( !$this->global_lock )
			  {
			  $this->db->unlock();
			  }
			  $this->locked = NULL;
			  }

			 */
		}

		/**
		 * Unknown
		 *
		 * @param array $fields Database field names
		 * @param string $data_type Unknown
		 * @return array Array with 'comms','locations' and 'others'
		 * @access private
		 */
		function slice_old_fields( $fields, $data_type )
		{
			foreach ($this->comm_old as $old_field => $value)
			{
				if ($fields[$old_field])
				{
					if ($fields['tel_pref'] == $old_field)
					{
						$comms[] = array($old_field => $value,
							'comm_descr' => $this->search_comm_id($this->comm_old[$old_field][0]),
							'comm_preferred' => $old_field);
						unset($fields['tel_pref']);
					}
					else
					{
						$comms[] = array($old_field => $value, 'comm_descr' => $this->search_comm_id($this->comm_old[$old_field][0]));
					}
					unset($fields[$old_field]);
				}
			}
			foreach ($this->adr_old[0] as $old_field)
			{
				$location[0][$old_field] = $fields[$old_field];
				unset($fields[$old_field]);
			}
			foreach ($this->adr_old[1] as $old_field)
			{
				$location[1][$old_field] = $fields[$old_field];
				unset($fields[$old_field]);
			}
			$new_fields = $this->split_stock_and_extras($fields);
			if (count($new_fields[0]) > 0)
			{
				// This fields are send to /dev/null
				// There is no need of they?
				// _debug_array($new_fields[0]);
			}
			foreach ($new_fields[2] as $field => $value)
			{
				$others[] = array('other_name' => $field, 'other_value' => $value);
			}
			return array('comms' => $comms, 'locations' => $location, 'others' => $others);
		}

		function is_contact( $account_id )
		{
			$account = $GLOBALS['phpgw']->accounts->get($account_id);
			if (!is_object($account))
			{
				return 0;
			}
			return $account->person_id;
		}

		/**
		 * Get the contact_type for contact_id.
		 *
		 * @param integer $contact_id Contact id which want to check.
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return integer The contact_type_id.
		 */
		function get_type_contact( $contact_id, $action = PHPGW_SQL_RUN_SQL )
		{
			$this->request(array('contact_type'));
			$this->criteria(array('contact_id' => $contact_id));
			$type = $this->get_query($action, __LINE__, __FILE__);
			if (is_array($type) && isset($type[0]['contact_type']))
			{
				return $type[0]['contact_type'];
			}
			return '';
		}

		/**
		 * Get the preferred address of organization.
		 *
		 * @param integer $org_id The organization id which want to check.
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL depending what we want
		 * @return integer The addr_id.
		 */
		function get_location_pref_org( $org_id, $action = PHPGW_SQL_RUN_SQL )
		{
			$this->request(array('key_addr_id'));
			$this->criteria(array('org_id' => $org_id,
				'addr_pref_val' => 'Y'));
			$addr_id = $this->get_query($action, __LINE__, __FILE__);
			return $addr_id[0]['key_addr_id'];
		}

		function get_mkdate()
		{
			$date = time();
			return $date;
		}

		function get_user_id()
		{
			if (isset($GLOBALS['phpgw_info']['user']['account_id']))
			{
				return $GLOBALS['phpgw_info']['user']['account_id'];
			}
			return 0;
		}

		function finalize_add( $id )
		{
			$this->finalize_edit($id);
		}

		function finalize_edit( $id )
		{
			if (isset($this->LDAPSyncEnabled) && $this->LDAPSyncEnabled)
			{
				$this->LDAPSync($id);
			}
		}

		function finalize_delete( $id )
		{
			if ($this->LDAPSyncEnabled)
			{
				$this->LDAPSyncDelete($id);
			}
		}

		function LDAPSyncDelete( $id )
		{
			$result = ldap_search($this->LDAPResource, $GLOBALS['phpgw_info']['server']['ldap_contact_context'], 'employeeNumber=' . $id, array(
				'dn'));
			$count = ldap_get_entries($this->LDAPResource, $result);
			if ((int)$count['count'] > 0)
			{
				ldap_delete($this->LDAPResource, $count[0]['dn']);
			}
		}

		function LDAPSync( $id )
		{
			$this->LDAPSyncDelete($id);

			$person = $this->get_principal_persons_data($id);

			//$time = gettimeofday();
			$uid = $person[0]['contact_id'] . ':' . $person[0]['per_full_name'];
			$dn = 'uid=' . utf8_encode($uid) . ',' . $GLOBALS['phpgw_info']['server']['ldap_contact_context'];

			/* Requerid attributes */
			$attributes['objectClass'][] = 'person';
			$attributes['objectClass'][] = 'organizationalPerson';
			$attributes['objectClass'][] = 'inetOrgPerson';
			$attributes['cn'][] = utf8_encode($person[0]['per_full_name']) ? utf8_encode($person[0]['per_full_name']) : ' ';
			$attributes['sn'][] = utf8_encode($person[0]['per_last_name']) ? utf8_encode($person[0]['per_last_name']) : ' ';
			/* Optional attributes */
			$attributes['uid'][] = utf8_encode($uid);
			if ($person[0]['org_name'])
			{
				$attributes['o'][] = utf8_encode($person[0]['org_name']);
			}
			if ($person[0]['per_title'])
			{
				$attributes['title'][] = utf8_encode($person[0]['per_title']);
			}
			if ($person[0]['per_first_name'])
			{
				$attributes['givenName'][] = utf8_encode($person[0]['per_first_name']);
			}
			/* if($person[0]['per_last_name'])
			  {
			  } */
			if ($person[0]['per_initials'])
			{
				$attributes['initials'][] = utf8_encode($person[0]['per_initials']);
			}
			if ($person[0]['per_department'])
			{
				$attributes['ou'][] = utf8_encode($person[0]['per_department']);
			}
			if ($person[0]['contact_id'])
			{
				$attributes['employeeNumber'][] = utf8_encode($person[0]['contact_id']);
			}
			unset($person);

			$address_pref = $this->get_addr_contact_data($id, array('addr_pref_val' => 'Y'));

			if ($address_pref[0]['addr_add1'] || $address_pref[0]['addr_add2'] || $address_pref[0]['addr_add3'])
			{
				$attributes['street'][] = utf8_encode($address_pref[0]['addr_add1'] .
					$address_pref[0]['addr_add2'] .
					$address_pref[0]['addr_add3']);
				$attributes['postalAddress'][] = utf8_encode($address_pref[0]['addr_add1'] .
					$address_pref[0]['addr_add2'] .
					$address_pref[0]['addr_add3']);
			}

			if ($address_pref[0]['addr_state'])
			{
				$attributes['st'][] = utf8_encode($address_pref[0]['addr_state']);
			}
			if ($address_pref[0]['addr_postal_code'])
			{
				$attributes['postalCode'][] = utf8_encode($address_pref[0]['addr_postal_code']);
			}
			if ($address_pref[0]['addr_city'])
			{
				$attributes['l'][] = utf8_encode($address_pref[0]['addr_city']);
			}
			//$attributes['homePostalAddress'][]			= ''; //we can use the address with type home
			unset($address_pref);

			$db2LDAP_map['home email'] = 'mail';
			$db2LDAP_map['work email'] = 'mail';
			$db2LDAP_map['home phone'] = 'homePhone';
			$db2LDAP_map['work phone'] = 'telephoneNumber';
			$db2LDAP_map['pager'] = 'pager';
			$db2LDAP_map['isdn'] = 'internationaliSDNNumber';
			$db2LDAP_map['home fax'] = 'facsimileTelephoneNumber';
			$db2LDAP_map['work fax'] = 'facsimileTelephoneNumber';
			$db2LDAP_map['mobile (cell) phone'] = 'mobile';
			$db2LDAP_map['car phone'] = 'telephoneNumber';
			$db2LDAP_map['website'] = 'labeledURI';

			$comms = $this->get_comm_contact_data($id, '');
			$validator = createObject('phpgwapi.validator');
			for ($i = 0; $i < count($comms); $i++)
			{
				$key = $db2LDAP_map[$comms[$i]['comm_description']];
				if ($comms[$i]['comm_data'] && $key)
				{
					$write = true;
					switch ($key)
					{
						case 'mail':
							$write = $validator->is_email($comms[$i]['comm_data']);
							break;
					}

					if ($write)
					{
						if ($comms[$i]['comm_preferred'] == 'Y' && count($attributes[$key]) > 0)
						{
							array_unshift($attributes[$key], utf8_encode($comms[$i]['comm_data']));
						}
						else
						{
							$attributes[$key][] = utf8_encode($comms[$i]['comm_data']);
						}
					}
				}
			}

			$success = @ldap_add($this->LDAPResource, $dn, $attributes);
			if (!$success)
			{
				echo 'ldap_add FAILED: [' . ldap_errno($this->LDAPResource) . '] ' . ldap_error($this->LDAPResource) . '<br /><br />';
				echo "<strong>Adds: " . $dn . "</strong><br />";
				echo "<pre>";
				print_r($attributes);
				echo "</pre>";
				echo "<br />";
			}
		}

		/**
		 * Method that could be used to import fields from an array with the specified form.
		 *
		 * This is the only one method that provide the fullfill fields for contact.
		 * @param array $fields Field names to import
		 * @param string $type Type of given communications
		 * @param boolean $update True: update existing entry; False: overwrite
		 * @return integer New contact ID or -1 on error
		 */
		function contact_import( $fields, $type = '', $update = false )
		{
			$type = empty($type) ? $this->search_contact_type($this->_contact_person) : $type;
			if ((isset($fields['first_name']) && !isset($fields['org_name'])) || $type)
			{
				$contact['contact_id'] = isset($fields['contact_id']) ? $fields['contact_id'] : '';
				$contact['access'] = isset($fields['access']) ? $fields['access'] : '';
				$contact['owner'] = isset($fields['owner']) ? $fields['owner'] : $GLOBALS['phpgw_info']['user']['account_id'];
				$contact['per_first_name'] = isset($fields['first_name']) ? $fields['first_name'] : '';
				$contact['per_last_name'] = isset($fields['last_name']) ? $fields['last_name'] : '';
				$contact['per_middle_name'] = isset($fields['middle_name']) ? $fields['middle_name'] : '';
				$contact['per_suffix'] = isset($fields['suffix']) ? $fields['suffix'] : '';
				$contact['per_prefix'] = isset($fields['prefix']) ? $fields['prefix'] : '';
				$contact['per_birthday'] = isset($fields['birthday']) ? $fields['birthday'] : '';
				$contact['per_pubkey'] = isset($fields['pubkey']) ? $fields['pubkey'] : '';
				$contact['per_title'] = isset($fields['title']) ? $fields['title'] : '';
				$contact['per_department'] = isset($fields['department']) ? $fields['department'] : '';
				$contact['per_initials'] = isset($fields['initials']) ? $fields['initials'] : '';
				$contact['per_sound'] = isset($fields['sound']) ? $fields['sound'] : '';
				$contact['per_active'] = isset($fields['active']) ? $fields['active'] : '';
				$contact['preferred_org'] = isset($fields['preferred_org']) ? $fields['preferred_org'] : '';
				$contact['preferred_address'] = isset($fields['preferred_address']) ? $fields['preferred_address'] : '';
				$contact['relations'] = isset($fields['organizations']) ? $fields['organizations'] : array();

				if ($contact['preferred_org'] != '')
				{
					$this->request(array('contact_id'));
					$this->criteria(array('org_name' => $contact['preferred_org']));
					$result = $this->get_query(PHPGW_SQL_RUN_SQL, __LINE__, __FILE__);
					if ($result && isset($result[0]))
					{
						$contact['relations'][] = $result[0]['contact_id'];
					}
				}

				unset($fields['contact_id'], $fields['first_name'], $fields['last_name'], $fields['middle_name'], $fields['suffix'], $fields['prefix'], $fields['birthday'], $fields['pubkey'], $fields['title'], $fields['department'], $fields['initials'], $fields['sound'], $fields['active'], $fields['preferred_org'], $fields['preferred_address'], $fields['organizations'], $fields['access'], $fields['full_name'], $fields['owner'], $fields['createon'], $fields['createby'], $fields['modon'], $fields['modby'], $fields['account_id'], $fields['org_name']);
			}
			else
			{
				$contact['org_name'] = isset($fields['org_name']) ? $fields['org_name'] : '';
				$contact['org_active'] = isset($fields['active']) ? $fields['active'] : '';
				$contact['org_parent'] = isset($fields['parent']) ? $fields['parent'] : '';
				$contact['relations'] = isset($fields['people']) ? $fields['people'] : '';

				unset($fields['org_name'], $fields['active'], $fields['parent'], $fields['people']);
			}

			$contact['categories'] = isset($fields['categories']) ? $fields['categories'] : '';
			unset($fields['categories'], $fields['access']);

			// Locations info
			$locations = array();
			if (is_array($fields['locations']))
			{
				foreach ($fields['locations'] as $location_input)
				{
					// Go for a _good_ address type
					$addr_type = $this->search_location_type($location_input['type']);
					if (!empty($addr_type))
					{
						$location['addr_type'] = $addr_type;
					}
					else
					{
						$addr_type = $this->search_location_type('work');
						if (!empty($addr_type))
						{
							$location['addr_type'] = $addr_type;
						}
						else
						{
							//return PHPGW_CONTACTS_ERROR_LOCATION_TYPE_MISSING;
							return -1;
						}
					}
					$location['addr_add1'] = isset($location_input['add1']) ? $location_input['add1'] : '';
					$location['addr_add2'] = isset($location_input['add2']) ? $location_input['add2'] : '';
					$location['addr_add3'] = isset($location_input['add3']) ? $location_input['add3'] : '';
					$location['addr_city'] = isset($location_input['city']) ? $location_input['city'] : '';
					$location['addr_state'] = isset($location_input['state']) ? $location_input['state'] : '';
					$location['addr_postal_code'] = isset($location_input['postal_code']) ? $location_input['postal_code'] : '';
					$location['addr_country'] = isset($location_input['country']) ? $location_input['country'] : '';
					$location['addr_preferred'] = isset($location_input['preferred']) ? $location_input['preferred'] : '';
					$locations[] = $location;
				}
			}
			unset($fields['locations']);

			// Notes
			$notes = array();
			if (is_array($fields['notes']))
			{
				if (isset($fields['notes']['type']) && isset($fields['notes']['note']))
				{
					$fields['notes'] = array($fields['notes']);
				}

				foreach ($fields['notes'] as $note_input)
				{
					$note_type = $this->search_note_type($note_input['type']);
					if (!empty($note_type))
					{
						$note['note_type'] = $note_type;
					}
					else
					{
						// FIXME: what is the default value for note_type?
						$note_type = $this->search_note_type('general');
						if (!empty($note_type))
						{
							$note['note_type'] = $note_type;
						}
						else
						{
							//return PHPGW_CONTACTS_ERROR_NOTE_TYPE_MISSING;
							return -1;
						}
					}
					$note['note_text'] = $note_input['note'];
					$notes[] = $note;
				}
			}
			unset($fields['notes']);

			// Communcation media fields
			$comm_media = array();
			if (is_array($fields['comm_media']))
			{
				foreach ($fields['comm_media'] as $description_input => $comm_input)
				{
					$description_id = $this->search_comm_descr($description_input);
					if (!empty($description_id))
					{
						$comm['comm_descr'] = $description_id;
						$comm['comm_data'] = $comm_input;
						$comm_media[] = $comm;
					}
					else
					{
						// Promote to others
						$fields[$description_input] = $comm_input;
					}
				}
			}
			unset($fields['comm_media']);

			// Other fields
			$others = array();
			if (count($fields) > 0)
			{
				foreach ($fields as $field_name => $field_value)
				{
					$other['other_name'] = $field_name;
					$other['other_value'] = $field_value;
					$other['other_owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
					$others[] = $other;
				}
			}

			if (($update == true) && (isset($contact['contact_id']) == true))
			{
				$cid = $contact['contact_id'];
				$ret = array();
				$ret['contact_id'] = $cid;

				$this->edit_contact($cid, $contact, PHPGW_SQL_RUN_SQL);
				if ($GLOBALS['phpgw']->db->adodb->ErrorMsg())
					$ret['edit_contact'] = false;
				else
					$ret['edit_contact'] = true;

				$this->edit_person($cid, $contact, PHPGW_SQL_RUN_SQL);
				if ($GLOBALS['phpgw']->db->adodb->ErrorMsg())
					$ret['edit_person'] = false;
				else
					$ret['edit_person'] = true;


				// update comm media data
				if (is_array($comm_media) && (count($comm_media) > 0))
				{
					$ret_comm = array();
					$ret['comm'] = & $ret_comm;

					$old_comms_media = $this->get_comm_contact_data($cid);
					if (is_array($old_comms_media) && (count($old_comms_media) > 0))
					{
						foreach ($old_comms_media as $old_comm)
						{
							$key_comm_id = $old_comm['key_comm_id'];
							$ret_comm['key_comm_id'][] = $key_comm_id;
							$is_edited = false;

							reset($comm_media);
							//while (list($key, $comm) = each($comm_media))
							foreach($comm_media as $key => $comm)
							{
								if ($comm['comm_descr'] == $old_comm['comm_descr'])
								{
									// replace old comm data
									$this->edit_comms($key_comm_id, $comm, PHPGW_SQL_RUN_SQL);
									if ($GLOBALS['phpgw']->db->adodb->ErrorMsg())
										$ret_comm['edit_comms'][$key_comm_id] = false;
									else
										$ret_comm['edit_comms'][$key_comm_id] = true;
									// unset comm array
									unset($comm_media[$key]);
									$is_edited = true;
									break;
								}
							}

							// if old entry was not set by new data (edited), delete them
							if ($is_edited == false)
							{
								// delete old comm data
								$this->delete_specified_comm($key_comm_id, PHPGW_SQL_RUN_SQL);
								if ($GLOBALS['phpgw']->db->adodb->ErrorMsg())
									$ret_comm[$key_comm_id]['delete_specified_comm'] = false;
								else
									$ret_comm[$key_comm_id]['delete_specified_comm'] = true;
							}
						}
					}
					// add new comms (rest of the comms array)
					foreach ($comm_media as $comm)
					{
						$this->add_communication_media($comm, $cid, PHPGW_SQL_RUN_SQL);
						$this->unlock_table();
						if ($GLOBALS['phpgw']->db->adodb->ErrorMsg())
							$ret_comm['add_communication_media'][] = false;
						else
							$ret_comm['add_communication_media'][] = true;
					}
				}

				// update location data (delete old and add new locs)
				$ret_loc = array();
				$ret['loc'] = & $ret_loc;
				$old_locations = $this->get_addr_contact_data($cid);
				if (is_array($old_locations) && (count($old_locations) > 0))
				{
					// 1. delete old locs
					$this->delete_locations($cid);
					if ($GLOBALS['phpgw']->db->adodb->ErrorMsg())
						$ret_loc['delete_locations'] = false;
					else
						$ret_loc['delete_locations'] = true;
				}
				// 2. add new locs
				if (is_array($locations) && (count($locations) > 0))
				{
					foreach ($locations as $loc)
					{
						$this->add_location($loc, $cid, PHPGW_SQL_RUN_SQL);
						$this->unlock_table();
						if ($GLOBALS['phpgw']->db->adodb->ErrorMsg())
							$ret_loc['add_location'][] = false;
						else
							$ret_loc['add_location'][] = true;
					}
				}

				// todo: updare $contact['relations'] ???
				// update other data (delete old and add new others)
				$ret_other = array();
				$ret['other'] = & $ret_other;
				$old_others = $this->get_others_contact_data($cid);
				if (is_array($old_others) && (count($old_others) > 0))
				{
					$ret['other'] = $old_others;
					// 1. delete old others
					$this->delete_others($cid);
					if ($GLOBALS['phpgw']->db->adodb->ErrorMsg())
						$ret_other['delete_others'] = false;
					else
						$ret_other['delete_others'] = true;
				}
				// 2. add new others
				if (is_array($others) && (count($others) > 0))
				{
					foreach ($others as $other)
					{
						$this->add_others($other, $cid, PHPGW_SQL_RUN_SQL);
						$this->unlock_table();
						if ($GLOBALS['phpgw']->db->adodb->ErrorMsg())
							$ret_other['add_other'][] = false;
						else
							$ret_other['add_other'][] = true;
					}
				}

				return $ret;
			}
			else
			{
				return $this->add_contact($type, $contact, $comm_media, $locations, $contact['categories'], $others, $contact['relations'], $notes);
			}
		}
	}