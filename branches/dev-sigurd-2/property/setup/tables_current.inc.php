<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage setup
 	* @version $Id$
	*/

	$phpgw_baseline = array(
		'fm_part_of_town' => array(
			'fd' => array(
				'part_of_town_id' => array('type' => 'auto','precision' => '2','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'district_id' => array('type' => 'int','precision' => '2','nullable' => True)
			),
			'pk' => array('part_of_town_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),


		'fm_gab_location' => array(
			'fd' => array(
				'location_code' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'gab_id' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc4' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'split' => array('type' => 'int','precision' => '2','nullable' => True),
				'remark' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'owner' => array('type' => 'varchar','precision' => '5','nullable' => True),
				'Spredning' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('gab_id','location_code'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_streetaddress' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '150','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tenant' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'member_of' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'first_name' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'last_name' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'phpgw_account_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'account_lid' => array('type' => 'varchar','precision' => '25','nullable' => True),
				'account_pwd' => array('type' => 'varchar','precision' => '32','nullable' => True),
				'account_status' => array('type' => 'int','precision' => '4','nullable' => True,'default' => '1'),
				'owner_id' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tenant_category' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_vendor' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'org_name' => array('type' => 'varchar','precision' => '100','nullable' => True),
				'email' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'klasse' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'member_of' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'category' => array('type' => 'int','precision' => '2','nullable' => True),
				'mva' => array('type' => 'int','precision' => '4','nullable' => True),
				'owner_id' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_vendor_category' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_district' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '2','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '20','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_standard_unit' => array(
			'fd' => array(
				'id' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_location_type' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'descr' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'pk' => array('type' => 'text','nullable' => True),
				'ix' => array('type' => 'text','nullable' => True),
				'uc' => array('type' => 'text','nullable' => True),
				'list_info' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'list_address' => array('type' => 'int','precision' => '2','nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_locations' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'level' => array('type' => 'int','precision' => '4','nullable' => False),
				'location_code' => array('type' => 'varchar','precision' => '50','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('location_code')
		),
		'fm_location1_category' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location1' => array(
			'fd' => array(
				'location_code' => array('type' => 'varchar','precision' => '16','nullable' => False),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => False),
				'loc1_name' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'part_of_town_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'owner_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'int','precision' => '4','nullable' => True),
				'mva' => array('type' => 'int','precision' => '4','nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'kostra_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'change_type' => array('type' => 'int','precision' => '4','nullable' => True),
				'rental_area' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00')
			),
			'pk' => array('loc1'),
			'fk' => array('fm_location1_category' => array('category' => 'id')),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_location1_history' => array(
			'fd' => array(
				'location_code' => array('type' => 'varchar','precision' => '16','nullable' => False),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => False),
				'loc1_name' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'part_of_town_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'owner_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'int','precision' => '4','nullable' => True),
				'mva' => array('type' => 'int','precision' => '4','nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'kostra_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'change_type' => array('type' => 'int','precision' => '4','nullable' => True),
				'rental_area' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'exp_date' => array('type' => 'timestamp','nullable' => True,'default' => 'current_timestamp')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location2_category' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location2' => array(
			'fd' => array(
				'location_code' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => False),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'loc2_name' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'int','precision' => '4','nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'change_type' => array('type' => 'int','precision' => '4','nullable' => True),
				'rental_area' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00')
			),
			'pk' => array('loc1','loc2'),
			'fk' => array(
				'fm_location1' => array('loc1' =>'loc1'),
				'fm_location2_category' => array('category' => 'id')
			),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_location2_history' => array(
			'fd' => array(
				'location_code' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => False),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'loc2_name' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'int','precision' => '4','nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'change_type' => array('type' => 'int','precision' => '4','nullable' => True),
				'rental_area' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'exp_date' => array('type' => 'timestamp','nullable' => True,'default' => 'current_timestamp')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location3_category' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location3' => array(
			'fd' => array(
				'location_code' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => False),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'loc3_name' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'int','precision' => '4','nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'change_type' => array('type' => 'int','precision' => '4','nullable' => True),
				'rental_area' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00')
			),
			'pk' => array('loc1','loc2','loc3'),
			'fk' => array(
				'fm_location2' => array('loc1' => 'loc1', 'loc2' => 'loc2'),
				'fm_location3_category' => array('category' => 'id')
			),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_location3_history' => array(
			'fd' => array(
				'location_code' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => False),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'loc3_name' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'int','precision' => '4','nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'change_type' => array('type' => 'int','precision' => '4','nullable' => True),
				'rental_area' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'exp_date' => array('type' => 'timestamp','nullable' => True,'default' => 'current_timestamp')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location4_category' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_location4' => array(
			'fd' => array(
				'location_code' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => False),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'loc4' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'loc4_name' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'street_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'street_number' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'tenant_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'int','precision' => '4','nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'change_type' => array('type' => 'int','precision' => '4','nullable' => True),
				'rental_area' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00')
			),
			'pk' => array('loc1','loc2','loc3','loc4'),
			'fk' => array(
				'fm_location3' => array('loc1' => 'loc1', 'loc2' => 'loc2', 'loc3' => 'loc3'),
				'fm_location4_category' => array('category' => 'id')
			),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_location4_history' => array(
			'fd' => array(
				'location_code' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => False),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'loc4' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'loc4_name' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'street_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'street_number' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'tenant_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'int','precision' => '4','nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'change_type' => array('type' => 'int','precision' => '4','nullable' => True),
				'rental_area' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'exp_date' => array('type' => 'timestamp','nullable' => True,'default' => 'current_timestamp')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_location_config' => array(
			'fd' => array(
				'column_name' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'location_type' => array('type' => 'int','precision' => '4','nullable' => False),
				'input_text' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'lookup_form' => array('type' => 'int','precision' => '2','nullable' => True),
				'f_key' => array('type' => 'int','precision' => '2','nullable' => True),
				'ref_to_category' => array('type' => 'int','precision' => '2','nullable' => True),
				'query_value' => array('type' => 'int','precision' => '2','nullable' => True),
				'reference_table' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'reference_id' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'datatype' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'precision_' => array('type' => 'int','precision' => '4','nullable' => True),
				'scale' => array('type' => 'int','precision' => '4','nullable' => True),
				'default_value' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'nullable' => array('type' => 'varchar','precision' => '5','nullable' => False,'default' => 'True')
			),
			'pk' => array('column_name'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_building_part' => array(
			'fd' => array(
				'id' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '50','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_b_account' => array(
			'fd' => array(
				'id' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'category' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'mva' => array('type' => 'int','precision' => '4','nullable' => True),
				'responsible' => array('type' => 'int','precision' => '4','nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_b_account_category' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_workorder' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'num' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'project_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'chapter_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => False),
				'start_date' => array('type' => 'int','precision' => '4','nullable' => False),
				'end_date' => array('type' => 'int','precision' => '4','nullable' => False),
				'coordinator' => array('type' => 'int','precision' => '4','nullable' => True),
				'vendor_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'varchar','precision' => '20','nullable' => False,'default' => 'active'),
				'descr' => array('type' => 'text','nullable' => True),
				'title' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'budget' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'calculation' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'combined_cost' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'deviation' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True),
				'act_mtrl_cost' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'act_vendor_cost' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'addition' => array('type' => 'int','precision' => '4','nullable' => True),
				'rig_addition' => array('type' => 'int','precision' => '4','nullable' => True),
				'account_id' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'key_fetch' => array('type' => 'int','precision' => '4','nullable' => True),
				'key_deliver' => array('type' => 'int','precision' => '4','nullable' => True),
				'integration' => array('type' => 'int','precision' => '4','nullable' => True),
				'charge_tenant' => array('type' => 'int','precision' => '2','nullable' => True),
				'claim_issued' => array('type' => 'int','precision' => '2','nullable' => True),
				'paid' => array('type' => 'int','precision' => '2','nullable' => True,'default' => '1'),
				'ecodimb'=> array('type' => 'int','precision' => 4,'nullable' => True),
				'p_num' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'p_entity_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'p_cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'location_code' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'tenant_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '20','nullable' => True)
 			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_workorder_status' => array(
			'fd' => array(
				'id' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_activities' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'num' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'base_descr' => array('type' => 'text','nullable' => True),
				'unit' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'ns3420' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'remarkreq' => array('type' => 'varchar','precision' => '5','nullable' => True,'default' => 'N'),
				'minperae' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
				'billperae' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'dim_d' => array('type' => 'int','precision' => '4','nullable' => True),
				'descr' => array('type' => 'text','nullable' => True),
				'branch_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'agreement_group_id' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_agreement_group' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'num' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'status' => array('type' => 'varchar','precision' => '15','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_agreement' => array(
			'fd' => array(
				'group_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'vendor_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'descr' => array('type' => 'text','nullable' => True),
				'status' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'start_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'end_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'termination_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('group_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_agreement_status' => array(
			'fd' => array(
				'id' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_activity_price_index' => array(
			'fd' => array(
				'activity_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'agreement_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'index_count' => array('type' => 'int','precision' => '4','nullable' => False),
				'current_index' => array('type' => 'int','precision' => '2','nullable' => True),
				'this_index' => array('type' => 'decimal','precision' => '20','scale' => '4','nullable' => True,'default' => '0.00'),
				'm_cost' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'w_cost' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'total_cost' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'index_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('activity_id','agreement_id','index_count'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_branch' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'num' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_wo_hours' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'record' => array('type' => 'int','precision' => '4','nullable' => True),
				'owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'workorder_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'activity_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'activity_num' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'grouping_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'grouping_descr' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => False),
				'hours_descr' => array('type' => 'text','nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'billperae' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'vendor_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'unit' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'ns3420_id' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'tolerance' => array('type' => 'int','precision' => '4','nullable' => True),
				'building_part' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'quantity' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True),
				'cost' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True),
				'dim_d' => array('type' => 'int','precision' => '4','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'cat_per_cent' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_wo_hours_category' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_wo_h_deviation' => array(
			'fd' => array(
				'workorder_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'hour_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'amount' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'text','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('workorder_id','hour_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_template' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'owner' => array('type' => 'int','precision' => '4','nullable' => True),
				'chapter_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_template_hours' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'template_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'record' => array('type' => 'int','precision' => '4','nullable' => True),
				'owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'activity_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'activity_num' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'grouping_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'grouping_descr' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'hours_descr' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'billperae' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'vendor_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'unit' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'ns3420_id' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'tolerance' => array('type' => 'int','precision' => '4','nullable' => True),
				'building_part' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'quantity' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True),
				'cost' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True),
				'dim_d' => array('type' => 'int','precision' => '4','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_key_loc' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'num' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_chapter' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '50','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_request' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'title' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'project_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'p_num' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'p_entity_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'p_cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'location_code' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc4' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'descr' => array('type' => 'text','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'owner' => array('type' => 'int','precision' => '4','nullable' => True),
				'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
				'floor' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'tenant_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'budget' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'branch_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'coordinator' => array('type' => 'int','precision' => '4','nullable' => True),
				'authorities_demands' => array('type' => 'int','precision' => '2','default' => '0','nullable' => True),
				'score' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
				'start_date' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
				'end_date' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_request_condition' => array(
			'fd' => array(
				'request_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'condition_type' => array('type' => 'int','precision' => '4','nullable' => False),
				'degree' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
				'probability' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
				'consequence' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('request_id','condition_type'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_request_status' => array(
			'fd' => array(
				'id' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_ns3420' => array(
			'fd' => array(
				'id' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'tekst1' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'enhet' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'tekst2' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'tekst3' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'tekst4' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'tekst5' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'tekst6' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'parent' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'type' => array('type' => 'varchar','precision' => '20','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tts_status' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'color' => array('type' => 'varchar','precision' => '10','nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tts_tickets' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'group_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'priority' => array('type' => 'int','precision' => '2','nullable' => False),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'assignedto' => array('type' => 'int','precision' => '4','nullable' => True),
				'subject' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'billable_hours' => array('type' => 'decimal','precision' => '8','scale' => '2','nullable' => True),
				'billable_rate' => array('type' => 'decimal','precision' => '8','scale' => '2','nullable' => True),
				'status' => array('type' => 'varchar','precision' => '2','nullable' => False),
				'details' => array('type' => 'text','nullable' => False),
				'location_code' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'p_num' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'p_entity_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'p_cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc4' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'floor' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'tenant_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'finnish_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'finnish_date2' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_tts_views' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'account_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'time' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array(),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecoart' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '25','nullable' => False)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecoavvik' => array(
			'fd' => array(
				'bilagsnr' => array('type' => 'int','precision' => '4','nullable' => False),
				'belop' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => False),
				'fakturadato' => array('type' => 'timestamp','nullable' => False),
				'forfallsdato' => array('type' => 'timestamp','nullable' => False),
				'artid' => array('type' => 'int','precision' => '2','nullable' => False),
				'godkjentbelop' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True),
				'spvend_code' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'oppsynsmannid' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'saksbehandlerid' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'budsjettansvarligid' => array('type' => 'varchar','precision' => '12','nullable' => False),
				'utbetalingid' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'oppsynsigndato' => array('type' => 'timestamp','nullable' => True),
				'saksigndato' => array('type' => 'timestamp','nullable' => True),
				'budsjettsigndato' => array('type' => 'timestamp','nullable' => True),
				'utbetalingsigndato' => array('type' => 'timestamp','nullable' => True),
				'overftid' => array('type' => 'timestamp','nullable' => True)
			),
			'pk' => array('bilagsnr'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecobilag' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'bilagsnr' => array('type' => 'int','precision' => '4','nullable' => False),
				'kidnr' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'typeid' => array('type' => 'int','precision' => '2','nullable' => False),
				'kildeid' => array('type' => 'int','precision' => '2','nullable' => False),
				'project_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'kostra_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'pmwrkord_code' => array('type' => 'int','precision' => '4','nullable' => True),
				'belop' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => False),
				'fakturadato' => array('type' => 'timestamp','nullable' => False),
				'periode' => array('type' => 'int','precision' => '2','nullable' => True),
				'forfallsdato' => array('type' => 'timestamp','nullable' => False),
				'fakturanr' => array('type' => 'varchar','precision' => '15','nullable' => False),
				'spbudact_code' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'regtid' => array('type' => 'timestamp','nullable' => False),
				'artid' => array('type' => 'int','precision' => '2','nullable' => False),
				'godkjentbelop' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True),
				'spvend_code' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'dima' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'loc1' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'dimb' => array('type' => 'int','precision' => '2','nullable' => True),
				'mvakode' => array('type' => 'int','precision' => '2','nullable' => True),
				'dimd' => array('type' => 'varchar','precision' => '5','nullable' => True),
				'oppsynsmannid' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'saksbehandlerid' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'budsjettansvarligid' => array('type' => 'varchar','precision' => '12','nullable' => False),
				'utbetalingid' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'oppsynsigndato' => array('type' => 'timestamp','nullable' => True),
				'saksigndato' => array('type' => 'timestamp','nullable' => True),
				'budsjettsigndato' => array('type' => 'timestamp','nullable' => True),
				'utbetalingsigndato' => array('type' => 'timestamp','nullable' => True),
				'merknad' => array('type' => 'text','nullable' => True),
				'splitt' => array('type' => 'int','precision' => '2','nullable' => True),
				'kreditnota' => array('type' => 'int','precision' => '2','nullable' => True),
				'pre_transfer' => array('type' => 'int','precision' => '2','nullable' => True),
				'item_type' => array('type' => 'int','precision' => '4','nullable' => True),
				'item_id' => array('type' => 'varchar','precision' => '20','nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecobilagoverf' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'bilagsnr' => array('type' => 'int','precision' => '4','nullable' => False),
				'kidnr' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'typeid' => array('type' => 'int','precision' => '2','nullable' => False),
				'kildeid' => array('type' => 'int','precision' => '2','nullable' => False),
				'project_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'kostra_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'pmwrkord_code' => array('type' => 'int','precision' => '4','nullable' => True),
				'belop' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => False),
				'fakturadato' => array('type' => 'timestamp','nullable' => False),
				'periode' => array('type' => 'int','precision' => '2','nullable' => True),
				'forfallsdato' => array('type' => 'timestamp','nullable' => False),
				'fakturanr' => array('type' => 'varchar','precision' => '15','nullable' => False),
				'spbudact_code' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'regtid' => array('type' => 'timestamp','nullable' => False),
				'artid' => array('type' => 'int','precision' => '2','nullable' => False),
				'godkjentbelop' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True),
				'spvend_code' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'dima' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'loc1' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'dimb' => array('type' => 'int','precision' => '2','nullable' => True),
				'mvakode' => array('type' => 'int','precision' => '2','nullable' => True),
				'dimd' => array('type' => 'varchar','precision' => '5','nullable' => True),
				'oppsynsmannid' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'saksbehandlerid' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'budsjettansvarligid' => array('type' => 'varchar','precision' => '12','nullable' => False),
				'utbetalingid' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'oppsynsigndato' => array('type' => 'timestamp','nullable' => True),
				'saksigndato' => array('type' => 'timestamp','nullable' => True),
				'budsjettsigndato' => array('type' => 'timestamp','nullable' => True),
				'utbetalingsigndato' => array('type' => 'timestamp','nullable' => True),
				'overftid' => array('type' => 'timestamp','nullable' => True),
				'ordrebelop' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => False),
				'merknad' => array('type' => 'text','nullable' => True),
				'splitt' => array('type' => 'int','precision' => '2','nullable' => True),
				'filnavn' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'kreditnota' => array('type' => 'int','precision' => '2','nullable' => True),
				'item_type' => array('type' => 'int','precision' => '4','nullable' => True),
				'item_id' => array('type' => 'varchar','precision' => '20','nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecobilagkilde' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '2','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'description' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecobilag_category' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '2','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '25','nullable' => False)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecodimb' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '2','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '25','nullable' => False)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecodimd' => array(
			'fd' => array(
				'id' => array('type' => 'varchar','precision' => '5','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '25','nullable' => False)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecomva' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '25','nullable' => False)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecologg' => array(
			'fd' => array(
				'batchid' => array('type' => 'int','precision' => '4','nullable' => False),
				'ecobilagid' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'int','precision' => '2','nullable' => True),
				'melding' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'tid' => array('type' => 'timestamp','nullable' => True,'default' => 'current_timestamp')
			),
			'pk' => array(),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecouser' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'lid' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'initials' => array('type' => 'varchar','precision' => '6','nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_request_condition_type' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'priority_key' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_projectbranch' => array(
			'fd' => array(
				'project_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'branch_id' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('project_id','branch_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_project' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => False),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => False),
				'start_date' => array('type' => 'int','precision' => '4','nullable' => False),
				'end_date' => array('type' => 'int','precision' => '4','nullable' => False),
				'coordinator' => array('type' => 'int','precision' => '4','nullable' => False),
				'status' => array('type' => 'varchar','precision' => '20','nullable' => False,'default' => 'active'),
				'descr' => array('type' => 'text','nullable' => True),
				'budget' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'reserve' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'p_num' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'p_entity_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'p_cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'location_code' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc4' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'tenant_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'key_fetch' => array('type' => 'int','precision' => '4','nullable' => True),
				'key_deliver' => array('type' => 'int','precision' => '4','nullable' => True),
				'other_branch' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'key_responsible' => array('type' => 'int','precision' => '4','nullable' => True),
				'project_group' => array('type' => 'int','precision' => '4','nullable' => True),
				'planned_cost' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_project_group' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_event_receipt' => array(
			'fd' => array(
				'cal_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'year_month_date' => array('type' => 'int','precision' => '4','nullable' => False),
				'datetime_done' => array('type' => 'int','precision' => '4','nullable' => True),
				'datetime_reject' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('cal_id','year_month_date'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_investment' => array(
			'fd' => array(
				'entity_id' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'invest_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'entity_type' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'p_num' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'p_entity_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'p_cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'location_code' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc4' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'writeoff_year' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('entity_id','invest_id'),
			'fk' => array(),
			'ix' => array('location_code','entity_id'),
			'uc' => array()
		),
		'fm_investment_value' => array(
			'fd' => array(
				'entity_id' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'invest_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'index_count' => array('type' => 'int','precision' => '4','nullable' => False),
				'current_index' => array('type' => 'int','precision' => '2','nullable' => True),
				'this_index' => array('type' => 'decimal','precision' => '20','scale' => '4','default' => '0','nullable' => True),
				'initial_value' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True),
				'value' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True),
				'index_date' => array('type' => 'timestamp','nullable' => True,'default' => 'current_timestamp')
			),
			'pk' => array('entity_id','invest_id','index_count'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_idgenerator' => array(
			'fd' => array(
				'name' => array('type' => 'varchar','precision' => '30','nullable' => False),
				'value' => array('type' => 'int','precision' => '4','nullable' => False),
				'increment' => array('type' => 'int','precision' => '4','nullable' => True),
				'maxvalue' => array('type' => 'int','precision' => '2','nullable' => True),
				'descr' => array('type' => 'varchar','precision' => '30','nullable' => True)
			),
			'pk' => array('name'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_document' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'title' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'document_name' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'link' => array('type' => 'text','nullable' => True),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'version' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'document_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'p_num' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'p_entity_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'p_cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'location_code' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc4' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'coordinator' => array('type' => 'int','precision' => '4','nullable' => True),
				'vendor_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'branch_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'access' => array('type' => 'varchar','precision' => '7','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_document_status' => array(
			'fd' => array(
				'id' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_request_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'history_record_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_appname' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'history_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_status' => array('type' => 'char','precision' => '2','nullable' => False),
				'history_new_value' => array('type' => 'text','nullable' => False),
				'history_old_value' => array('type' => 'text','nullable' => true),
				'history_timestamp' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_workorder_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'history_record_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_appname' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'history_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_status' => array('type' => 'char','precision' => '2','nullable' => False),
				'history_new_value' => array('type' => 'text','nullable' => False),
				'history_old_value' => array('type' => 'text','nullable' => true),
				'history_timestamp' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_project_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'history_record_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_appname' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'history_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_status' => array('type' => 'char','precision' => '2','nullable' => False),
				'history_new_value' => array('type' => 'text','nullable' => False),
				'history_old_value' => array('type' => 'text','nullable' => true),
				'history_timestamp' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tts_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'history_record_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_appname' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'history_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_status' => array('type' => 'char','precision' => '2','nullable' => False),
				'history_new_value' => array('type' => 'text','nullable' => False),
				'history_old_value' => array('type' => 'text','nullable' => true),
				'history_timestamp' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_document_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'history_record_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_appname' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'history_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_status' => array('type' => 'char','precision' => '2','nullable' => False),
				'history_new_value' => array('type' => 'text','nullable' => False),
				'history_old_value' => array('type' => 'text','nullable' => true),
				'history_timestamp' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_owner' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'abid' => array('type' => 'int','precision' => '4','nullable' => True),
				'org_name' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'contact_name' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => False),
				'member_of' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'remark' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'owner_id' => array('type' => 'int','precision' => '4','nullable' => True) // record owner
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_owner_category' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_cache' => array(
			'fd' => array(
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'value' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('name'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_entity' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'location_form' => array('type' => 'int','precision' => '4','nullable' => True),
				'documentation' => array('type' => 'int','precision' => '4','nullable' => True),
				'lookup_entity' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_entity_category' => array(
			'fd' => array(
				'entity_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '100','nullable' => True),
				'descr' => array('type' => 'text','nullable' => True),
				'prefix' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'lookup_tenant' => array('type' => 'int','precision' => '4','nullable' => True),
				'tracking' => array('type' => 'int','precision' => '4','nullable' => True),
				'location_level' => array('type' => 'int','precision' => '4','nullable' => True),
				'fileupload' => array('type' => 'int','precision' => '4','nullable' => True),
				'loc_link' => array('type' => 'int','precision' => '4','nullable' => True),
				'start_project' => array('type' => 'int','precision' => '4','nullable' => True),
				'start_ticket' => array('type' => 'int','precision' => '2','nullable' => True)
			),
			'pk' => array('entity_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_entity_lookup' => array(
			'fd' => array(
				'entity_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'location' => array('type' => 'varchar','precision' => '15','nullable' => False),
				'type' => array('type' => 'varchar','precision' => '15','nullable' => False)
			),
			'pk' => array('entity_id','location','type'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_entity_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'history_record_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_appname' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'history_attrib_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_status' => array('type' => 'char','precision' => '2','nullable' => False),
				'history_new_value' => array('type' => 'text','nullable' => False),
				'history_old_value' => array('type' => 'text','nullable' => true),
				'history_timestamp' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_entity_1_1' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'num' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'p_num' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'p_entity_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'p_cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'location_code' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc4' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'int','precision' => '4','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => False),
				'ext_system_id' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'ext_meter_id' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'remark' => array('type' => 'varchar','precision' => '255','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array('num')
		),

		'fm_entity_1_2' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'num' => array('type' => 'varchar','precision' => '16','nullable' => False),
				'p_num' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'p_entity_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'p_cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'location_code' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc4' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'tenant_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'int','precision' => '4','nullable' => True),
				'attribute1' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'attribute2' => array('type' => 'timestamp','nullable' => True),
				'attribute3' => array('type' => 'int','precision' => '4','nullable' => True),
				'attribute4' => array('type' => 'text','nullable' => True),
				'attribute5' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_entity_1_3' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'num' => array('type' => 'varchar','precision' => '16','nullable' => False),
				'p_num' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'p_entity_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'p_cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'location_code' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc4' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'tenant_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'int','precision' => '4','nullable' => True),
				'attribute1' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'attribute2' => array('type' => 'timestamp','nullable' => True),
				'attribute3' => array('type' => 'int','precision' => '4','nullable' => True),
				'attribute4' => array('type' => 'text','nullable' => True),
				'attribute5' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_entity_2_1' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'num' => array('type' => 'varchar','precision' => '16','nullable' => False),
				'p_num' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'p_entity_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'p_cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'location_code' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc4' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'tenant_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'int','precision' => '4','nullable' => True),
				'attribute1' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'attribute2' => array('type' => 'timestamp','nullable' => True),
				'attribute3' => array('type' => 'int','precision' => '4','nullable' => True),
				'attribute4' => array('type' => 'text','nullable' => True),
				'attribute5' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_entity_2_2' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'num' => array('type' => 'varchar','precision' => '16','nullable' => False),
				'p_num' => array('type' => 'varchar','precision' => '15','nullable' => True),
				'p_entity_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'p_cat_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'location_code' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'loc1' => array('type' => 'varchar','precision' => '6','nullable' => True),
				'loc2' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc3' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'loc4' => array('type' => 'varchar','precision' => '4','nullable' => True),
				'address' => array('type' => 'varchar','precision' => '150','nullable' => True),
				'tenant_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'status' => array('type' => 'int','precision' => '4','nullable' => True),
				'attribute1' => array('type' => 'varchar','precision' => '12','nullable' => True),
				'attribute2' => array('type' => 'timestamp','nullable' => True),
				'attribute3' => array('type' => 'int','precision' => '4','nullable' => True),
				'attribute4' => array('type' => 'text','nullable' => True),
				'attribute5' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_custom' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'sql_text' => array('type' => 'text','nullable' => False),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_custom_cols' => array(
			'fd' => array(
				'custom_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'sorting' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('custom_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_orders' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'type' => array('type' => 'varchar', 'precision' => 50,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_s_agreement' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'vendor_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'name' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'descr' => array('type' => 'text','nullable' => True),
				'status' => array('type' => 'varchar', 'precision' => 10,'nullable' => True),
				'category' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'member_of' => array('type' => 'text','nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'start_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'end_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'termination_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'actual_cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
				'account_id' => array('type' => 'varchar', 'precision' => 20,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_s_agreement_budget' => array(
			'fd' => array(
				'agreement_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'year' => array('type' => 'int','precision' => 4,'nullable' => False),
				'budget_account' =>  array('type' => 'varchar','precision' => 15,'nullable' => False),
				'ecodimb' => array('type' => 'int','precision' => 4,'nullable' => True),
				'category' => array('type' => 'int','precision' => 4,'nullable' => True),
				'budget' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'actual_cost' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
				'user_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'entry_date' => array('type' => 'int','precision' => 4,'nullable' => True),
				'modified_date' => array('type' => 'int','precision' => 4,'nullable' => True)
			),
			'pk' => array('agreement_id','year'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_s_agreement_category' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'descr' => array('type' => 'varchar', 'precision' => 50,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_s_agreement_detail' => array(
			'fd' => array(
				'agreement_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'location_code' => array('type' => 'varchar', 'precision' => 30,'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => 150,'nullable' => True),
				'p_num' => array('type' => 'varchar', 'precision' => 15,'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'p_cat_id' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'descr' => array('type' => 'text','nullable' => True),
				'unit' => array('type' => 'varchar', 'precision' => 10,'nullable' => True),
				'quantity' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
				'frequency' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'test' => array('type' => 'text','nullable' => True),
				'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True)
			),
			'pk' => array('agreement_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_s_agreement_pricing' => array(
			'fd' => array(
				'agreement_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'item_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'current_index' => array('type' => 'int', 'precision' => 2,'nullable' => True),
				'this_index' => array('type' => 'decimal', 'precision' => 20, 'scale' => 4,'nullable' => True),
				'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
				'index_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True)
			),
			'pk' => array('agreement_id','item_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_s_agreement_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'history_record_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_appname' => array('type' => 'varchar','precision' => '64','nullable' => False),
				'history_detail_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_attrib_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'history_status' => array('type' => 'char','precision' => '2','nullable' => False),
				'history_new_value' => array('type' => 'text','nullable' => False),
				'history_old_value' => array('type' => 'text','nullable' => true),
				'history_timestamp' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_async_method' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'data' => array('type' => 'text','nullable' => True),
				'descr' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_cron_log' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'cron' => array('type' => 'int','precision' => '2','nullable' => True),
				'cron_date' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp'),
				'process' => array('type' => 'varchar','precision' => '255','nullable' => False),
				'message' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tenant_claim' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'project_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'tenant_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'amount' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True),
				'b_account_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => False),
				'status' => array('type' => 'varchar','precision' => '8','nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tenant_claim_category' => array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_r_agreement' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'customer_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'customer_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'name' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'descr' => array('type' => 'text','nullable' => True),
				'status' => array('type' => 'varchar', 'precision' => 10,'nullable' => True),
				'category' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'member_of' => array('type' => 'text','nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'start_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'end_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'termination_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'actual_cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
				'account_id' => array('type' => 'varchar', 'precision' => 20,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),


		'fm_r_agreement_category' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'descr' => array('type' => 'varchar', 'precision' => 50,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_r_agreement_item' => array(
			'fd' => array(
				'agreement_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'location_code' => array('type' => 'varchar', 'precision' => 30,'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => 150,'nullable' => True),
				'p_num' => array('type' => 'varchar', 'precision' => 15,'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'p_cat_id' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'descr' => array('type' => 'text','nullable' => True),
				'unit' => array('type' => 'varchar', 'precision' => 10,'nullable' => True),
				'quantity' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
				'frequency' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'test' => array('type' => 'text','nullable' => True),
				'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
				'rental_type_id' => array('type' => 'int', 'precision' => 4,'nullable' => True)
			),
			'pk' => array('agreement_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_r_agreement_item_history' => array(
			'fd' => array(
				'agreement_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'item_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'current_index' => array('type' => 'int', 'precision' => 2,'nullable' => True),
				'this_index' => array('type' => 'decimal', 'precision' => 20, 'scale' => 4,'nullable' => True),
				'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
				'index_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'from_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'to_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'tenant_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
			),
			'pk' => array('agreement_id','item_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_r_agreement_common' => array(
			'fd' => array(
				'agreement_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'b_account' => array('type' => 'varchar', 'precision' => 30,'nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True)
			),
			'pk' => array('agreement_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'fm_r_agreement_c_history' => array(
			'fd' => array(
				'agreement_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'c_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'from_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'to_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'current_record' => array('type' => 'int', 'precision' => 2,'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'budget_cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
				'actual_cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
				'fraction' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
				'override_fraction' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
			),
			'pk' => array('agreement_id','c_id','id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_budget_basis' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'b_group' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'district_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'revision' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'budget_cost' => array('type' => 'int', 'precision' => 4,'default' => '0','nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'distribute_year' => array('type' => 'text','nullable' => True),
				'ecodimb'=> array('type' => 'int','precision' => 4,'nullable' => True),
				'category'=> array('type' => 'int','precision' => 4,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('year','b_group','district_id','revision')
		),
		'fm_budget' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'b_account_id' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'district_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'revision' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'budget_cost' => array('type' => 'int', 'precision' => 4,'default' => '0','nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'ecodimb'=> array('type' => 'int','precision' => 4,'nullable' => True),
				'category'=> array('type' => 'int','precision' => 4,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('year','b_account_id','district_id','revision')
		),
		'fm_budget_period' => array(
			'fd' => array(
				'year' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'month' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'b_account_id' => array('type' => 'varchar','precision' => 4,'nullable' => False),
				'per_cent' => array('type' => 'int','precision' => 4,'default' => '0','nullable' => True), //'percent' is reserved for mssql
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'remark' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('year','month','b_account_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_budget_cost' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'month' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'b_account_id' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'amount' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('year','month','b_account_id')
		),
		'fm_responsibility' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'active' => array('type' => 'int','precision' => 2,'nullable' => True),
				'cat_id' => array('type' => 'int','precision' => 4,'nullable' => False),
				'created_on' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4,'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_categories' => array('cat_id' => 'cat_id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'fm_responsibility_contact' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => '4','nullable' => False),
				'responsibility_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'contact_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => 20,'nullable' => True),
				'p_num' => array('type' => 'varchar', 'precision' => 15,'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'p_cat_id' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'priority' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'active_from' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'active_to' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'created_on' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'expired_on' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'expired_by' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(
				'fm_responsibility' => array('responsibility_id' => 'id'),
				'phpgw_contact' => array('contact_id' => 'contact_id')
			),
			'ix' => array('location_code'),
			'uc' => array()
 		)
	);
