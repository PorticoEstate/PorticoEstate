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
		'fm_district' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '2', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'delivery_address' => array('type' => 'text', 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_part_of_town' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '2', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '150', 'nullable' => false),
				'district_id' => array('type' => 'int', 'precision' => '2', 'nullable' => false),
				'delivery_address' => array('type' => 'text', 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array('fm_district' => array('district_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'fm_gab_location' => array(
			'fd' => array(
				'location_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'gab_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => '150', 'nullable' => True),
				'split' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'remark' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'owner' => array('type' => 'varchar', 'precision' => '5', 'nullable' => True),
				'Spredning' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('gab_id', 'location_code'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_streetaddress' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '150', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tenant' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'member_of' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'first_name' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'last_name' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'contact_phone' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'contact_email' => array('type' => 'varchar', 'precision' => '64', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'phpgw_account_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'account_lid' => array('type' => 'varchar', 'precision' => 100, 'nullable' => True),
				'account_pwd' => array('type' => 'varchar', 'precision' => 115, 'nullable' => True),
				'account_status' => array('type' => 'int', 'precision' => '4', 'nullable' => True,
					'default' => '1'),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tenant_category' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_vendor' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'entry_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				'org_name' => array('type' => 'varchar', 'precision' => '100', 'nullable' => True),
				'email' => array('type' => 'varchar', 'precision' => '64', 'nullable' => True),
				'contact_phone' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'klasse' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True),
				'member_of' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'mva' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'active' => array('type' => 'int', 'precision' => '2', 'nullable' => True, 'default' => 1)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_vendor_category' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_standard_unit' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location_type' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'descr' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'pk' => array('type' => 'text', 'nullable' => True),
				'ix' => array('type' => 'text', 'nullable' => True),
				'uc' => array('type' => 'text', 'nullable' => True),
				'list_info' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'list_address' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'list_documents' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'enable_controller' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_locations' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'level' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'location_code' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => False),
				'name' => array('type' => 'text', 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('location_code')
		),
		'fm_location1_category' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location1' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'location_code' => array('type' => 'varchar', 'precision' => '16', 'nullable' => False),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => False),
				'loc1_name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'part_of_town_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'status' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'mva' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'kostra_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'change_type' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'rental_area' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_gross' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_net' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_usable' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'delivery_address' => array('type' => 'text', 'nullable' => True),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp')
			),
			'pk' => array('loc1'),
			'fk' => array('fm_location1_category' => array('category' => 'id')),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_location1_history' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'location_code' => array('type' => 'varchar', 'precision' => '16', 'nullable' => False),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => False),
				'loc1_name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'part_of_town_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'status' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'mva' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'kostra_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'change_type' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'rental_area' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_gross' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_net' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_usable' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'delivery_address' => array('type' => 'text', 'nullable' => True),
				'exp_date' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp'),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location2_category' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location2' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'location_code' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => False),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => False),
				'loc2_name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'street_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'street_number' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True),
				'building_number' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
				'status' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'change_type' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'rental_area' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_gross' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_net' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_usable' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp')
			),
			'pk' => array('loc1', 'loc2'),
			'fk' => array(
				'fm_location1' => array('loc1' => 'loc1'),
				'fm_location2_category' => array('category' => 'id')
			),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_location2_history' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'location_code' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => False),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => False),
				'loc2_name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'street_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'street_number' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True),
				'building_number' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
				'status' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'change_type' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'rental_area' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_gross' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_net' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_usable' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'exp_date' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp'),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location3_category' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location3' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'location_code' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => False),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => False),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => False),
				'loc3_name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'status' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'change_type' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'rental_area' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_gross' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_net' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_usable' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp')
			),
			'pk' => array('loc1', 'loc2', 'loc3'),
			'fk' => array(
				'fm_location2' => array('loc1' => 'loc1', 'loc2' => 'loc2'),
				'fm_location3_category' => array('category' => 'id')
			),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_location3_history' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'location_code' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => False),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => False),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => False),
				'loc3_name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'status' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'change_type' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'rental_area' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_gross' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_net' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_usable' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'exp_date' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp'),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location4_category' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location4' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'location_code' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => False),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => False),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => False),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => False),
				'loc4_name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'tenant_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'status' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'change_type' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'rental_area' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_gross' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_net' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_usable' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp')
			),
			'pk' => array('loc1', 'loc2', 'loc3', 'loc4'),
			'fk' => array(
				'fm_location3' => array('loc1' => 'loc1', 'loc2' => 'loc2', 'loc3' => 'loc3'),
				'fm_location4_category' => array('category' => 'id')
			),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_location4_history' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'location_code' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => False),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => False),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => False),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => False),
				'loc4_name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'tenant_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'status' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'change_type' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'rental_area' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_gross' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_net' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'area_usable' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'exp_date' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp'),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location_config' => array(
			'fd' => array(
				'column_name' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'location_type' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'input_text' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'lookup_form' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'f_key' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'ref_to_category' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'query_value' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'reference_table' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'reference_id' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'datatype' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True),
				'precision_' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'scale' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'default_value' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'nullable' => array('type' => 'varchar', 'precision' => '5', 'nullable' => False,
					'default' => 'True')
			),
			'pk' => array('column_name'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_building_part' => array(
			'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => '5', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'filter_1' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'filter_2' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'filter_3' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'filter_4' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_b_account' => array(
			'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '100', 'nullable' => False),
				'mva' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'responsible' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'active' => array('type' => 'int', 'precision' => '2', 'nullable' => True, 'default' => '0'),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_b_account_category' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'active' => array('type' => 'int', 'precision' => '2', 'nullable' => True, 'default' => '0'),
				'external_project' => array('type' => 'int', 'precision' => '2', 'nullable' => True,
					'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_b_account_user' => array(
			'fd' => array(
				'b_account_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_on' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
			),
			'pk' => array('b_account_id', 'user_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_workorder' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'project_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'access' => array('type' => 'varchar', 'precision' => '7', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'chapter_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'start_date' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'end_date' => array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'tender_deadline' => array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'tender_received' => array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'inspection_on_completion' => array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'coordinator' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'vendor_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'status' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False,
					'default' => 'active'),
				'descr' => array('type' => 'text', 'nullable' => True),
				'title' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'budget' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
					'default' => '0.00'),
				'calculation' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'combined_cost' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'deviation' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True),
				'act_mtrl_cost' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'act_vendor_cost' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'actual_cost' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'addition' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'rig_addition' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'account_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'key_fetch' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'key_deliver' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'integration' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'charge_tenant' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'claim_issued' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'paid' => array('type' => 'int', 'precision' => '2', 'nullable' => True, 'default' => '1'),
				'ecodimb' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'p_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'p_cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => '150', 'nullable' => True),
				'tenant_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'contact_phone' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'paid_percent' => array('type' => 'int', 'precision' => 4, 'nullable' => True,
					'default' => 0),
				'event_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'billable_hours' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True),
				'contract_sum' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'approved' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'mail_recipients' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'continuous' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'fictive_periodization' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'contract_id' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'tax_code' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'unspsc_code' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'service_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'building_part' => array('type' => 'varchar', 'precision' => 4, 'nullable' => True),
				'order_dim1' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
 				'order_sent' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'order_received' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'order_received_amount' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True, 'default' => '0.00'),
				'delivery_address' => array('type' => 'text', 'nullable' => True),
				'file_attachments' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_workorder_status' => array(
			'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'approved' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'in_progress' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'delivered' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'closed' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'canceled' => array('type' => 'int', 'precision' => '2', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_workorder_budget' => array(
			'fd' => array(
				'order_id' => array('type' => 'int', 'precision' => 8, 'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'month' => array('type' => 'int', 'precision' => 2, 'nullable' => False, 'default' => 0),
				'budget' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
					'default' => '0.00'),
				'contract_sum' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'combined_cost' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'active' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('order_id', 'year', 'month'),
			'fk' => array('fm_workorder' => array('order_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'fm_activities' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => '25', 'nullable' => False),
				'base_descr' => array('type' => 'text', 'nullable' => True),
				'unit' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'ns3420' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'remarkreq' => array('type' => 'varchar', 'precision' => '5', 'nullable' => True,
					'default' => 'N'),
				'minperae' => array('type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True),
				'billperae' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'dim_d' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'descr' => array('type' => 'text', 'nullable' => True),
				'branch_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'agreement_group_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_agreement_group' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => '25', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'status' => array('type' => 'varchar', 'precision' => '15', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_agreement' => array(
			'fd' => array(
				'group_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'vendor_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'contract_id' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'name' => array('type' => 'varchar', 'precision' => '100', 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True),
				'status' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'start_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'end_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'termination_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('group_id', 'id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_agreement_status' => array(
			'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_activity_price_index' => array(
			'fd' => array(
				'activity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'agreement_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'index_count' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'current_index' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'this_index' => array('type' => 'decimal', 'precision' => '20', 'scale' => '4',
					'nullable' => True, 'default' => '0.00'),
				'm_cost' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
					'default' => '0.00'),
				'w_cost' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
					'default' => '0.00'),
				'total_cost' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'index_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('activity_id', 'agreement_id', 'index_count'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_branch' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_wo_hours' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'record' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'workorder_id' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'activity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'activity_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'grouping_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'grouping_descr' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'hours_descr' => array('type' => 'text', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'billperae' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'vendor_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'unit' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'ns3420_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'tolerance' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'building_part' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'quantity' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True),
				'cost' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True),
				'dim_d' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'cat_per_cent' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_wo_hours_category' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_wo_h_deviation' => array(
			'fd' => array(
				'workorder_id' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'hour_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'amount' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('workorder_id', 'hour_id', 'id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_template' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'owner' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'chapter_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_template_hours' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'template_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'record' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'activity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'activity_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'grouping_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'grouping_descr' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'hours_descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'billperae' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'vendor_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'unit' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'ns3420_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'tolerance' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'building_part' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'quantity' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True),
				'cost' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True),
				'dim_d' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_key_loc' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_chapter' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_authorities_demands' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_condition_survey_status' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'closed' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'in_progress' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'delivered' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'sorting' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_condition_survey' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'title' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'p_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'p_cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'descr' => array('type' => 'text', 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'status_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'coordinator_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'vendor_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'report_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'multiplier' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'default' => '1', 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_condition_survey_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_appname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'char', 'precision' => '2', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_old_value' => array('type' => 'text', 'nullable' => true),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_request' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'condition_survey_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'title' => array('type' => 'text', 'nullable' => True),
				'project_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'p_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'p_cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'descr' => array('type' => 'text', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'owner' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'access' => array('type' => 'varchar', 'precision' => '7', 'nullable' => True),
				'floor' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => '150', 'nullable' => True),
				'tenant_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'contact_phone' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'amount_investment' => array('type' => 'int', 'precision' => '4', 'default' => '0',
					'nullable' => True),
				'amount_operation' => array('type' => 'int', 'precision' => '4', 'default' => '0',
					'nullable' => True),
				'amount_potential_grants' => array('type' => 'int', 'precision' => '4', 'default' => '0',
					'nullable' => True),
				'status' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True),
				'branch_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'coordinator' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'responsible_unit' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'authorities_demands' => array('type' => 'int', 'precision' => '2', 'default' => '0',
					'nullable' => True),
				'score' => array('type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True),
				'recommended_year' => array('type' => 'int', 'precision' => '4', 'default' => '0',
					'nullable' => True),
				'start_date' => array('type' => 'int', 'precision' => '8', 'default' => '0',
					'nullable' => True),
				'end_date' => array('type' => 'int', 'precision' => '8', 'default' => '0', 'nullable' => True),
				'building_part' => array('type' => 'varchar', 'precision' => 4, 'nullable' => True),
				'closed_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'in_progress_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'delivered_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'regulations' => array('type' => 'varchar', 'precision' => 100, 'nullable' => True),
				'multiplier' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'default' => '1', 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_request_responsible_unit' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '2', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_request_condition' => array(
			'fd' => array(
				'request_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'condition_type' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'reference' => array('type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True),
				'degree' => array('type' => 'int', 'precision' => '4', 'default' => '0', 'nullable' => True),
				'probability' => array('type' => 'int', 'precision' => '4', 'default' => '0',
					'nullable' => True),
				'consequence' => array('type' => 'int', 'precision' => '4', 'default' => '0',
					'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('request_id', 'condition_type'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_request_status' => array(
			'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'closed' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'in_progress' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'delivered' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'sorting' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_request_consume' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'request_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'amount' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'date' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'descr' => array('type' => 'text', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array('fm_request' => array('request_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'fm_request_planning' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'request_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'amount' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'date' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'descr' => array('type' => 'text', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array('fm_request' => array('request_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'fm_ns3420' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'parent_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'enhet' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'tekst1' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'tekst2' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'tekst3' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'tekst4' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'tekst5' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'tekst6' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'type' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('num')
		),
		'fm_tts_status' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'color' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True),
				'closed' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'approved' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'in_progress' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'delivered' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'actual_cost' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'sorting' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tts_priority' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '100', 'nullable' => true),
			),
			'pk' => array('id'),
			'ix' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tts_tickets' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'group_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'priority' => array('type' => 'int', 'precision' => '2', 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'assignedto' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'subject' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'billable_hours' => array('type' => 'decimal', 'precision' => '8', 'scale' => '2',
					'nullable' => True),
				'billable_rate' => array('type' => 'decimal', 'precision' => '8', 'scale' => '2',
					'nullable' => True),
				'status' => array('type' => 'varchar', 'precision' => '2', 'nullable' => False),
				'details' => array('type' => 'text', 'nullable' => False),
				'location_code' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'p_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'p_cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'floor' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'contact_phone' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'contact_email' => array('type' => 'varchar', 'precision' => '64', 'nullable' => True),
				'tenant_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'finnish_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'finnish_date2' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'order_id' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'ordered_by' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'vendor_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'contract_id' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'tax_code' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'external_project_id' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True),
				'unspsc_code' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'service_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'order_descr' => array('type' => 'text', 'nullable' => True),
				'b_account_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'ecodimb' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'budget' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'actual_cost' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'actual_cost_year' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'order_cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'building_part' => array('type' => 'varchar', 'precision' => 4, 'nullable' => True),
				'order_dim1' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'publish_note' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'branch_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'order_sent' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'order_received' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'order_received_amount' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True, 'default' => '0.00'),
				'mail_recipients' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'file_attachments' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'delivery_address' => array('type' => 'text', 'nullable' => True),
				'continuous' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'order_deadline' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'order_deadline2' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'invoice_remark' => array('type' => 'text', 'nullable' => True),
				'external_ticket_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'document_required' => array('type' =>	'int', 'precision' => 4, 'nullable' => True),
				'handyman_checklist_id' => array('type' => 'int','precision' => 8, 'nullable' => true),
				'handyman_order_number' => array('type' =>	'int', 'precision' => 8, 'nullable' => true)
			),
			'pk' => array('id'),
			'ix' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_tts_views' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'account_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'time' => array('type' => 'int', 'precision' => '4', 'nullable' => False)
			),
			'pk' => array(),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_tts_payments' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'ticket_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'amount' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'default' => '0',
					'nullable' => false),
				'period' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'remark' => array('type' => 'text', 'nullable' => true),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array('fm_tts_tickets' => array('ticket_id' => 'id')),
			'uc' => array()
		),
		'fm_tts_budget' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'ticket_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'amount' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'default' => '0',
					'nullable' => false),
				'period' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'remark' => array('type' => 'text', 'nullable' => true),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array('fm_tts_tickets' => array('ticket_id' => 'id')),
			'uc' => array()
		),
		'fm_tts_external_communication_type' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '100', 'nullable' => true),
			),
			'pk' => array('id'),
			'ix' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tts_external_communication' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'ticket_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'order_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'type_id' => array('type' => 'int', 'precision' => 2, 'nullable' => False),
				'vendor_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'subject' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'mail_recipients' => array('type' => 'text', 'nullable' => True),
				'file_attachments' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'deadline' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'deadline2' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(
				'fm_tts_tickets' => array('ticket_id' => 'id')
				),
			'uc' => array()
		),
		'fm_tts_external_communication_msg' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'excom_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'message' => array('type' => 'text', 'nullable' => False),
				'timestamp_sent' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'mail_recipients' => array('type' => 'text', 'nullable' => True),
				'file_attachments' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'sender_email_address' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(
				'fm_tts_external_communication' => array('excom_id' => 'id')
				),
			'uc' => array()
		),
		'fm_org_unit' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'parent_id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'name' => array('type' => 'varchar', 'precision' => '200', 'nullable' => False),
				'active' => array('type' => 'int', 'precision' => '2', 'nullable' => True, 'default' => 1),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_eco_service' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'active' => array('type' => 'int', 'precision' => '2', 'nullable' => True, 'default' => 1),
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecoart' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '25', 'nullable' => False)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecoavvik' => array(
			'fd' => array(
				'bilagsnr' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'belop' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'default' => '0',
					'nullable' => False),
				'fakturadato' => array('type' => 'timestamp', 'nullable' => False),
				'forfallsdato' => array('type' => 'timestamp', 'nullable' => False),
				'artid' => array('type' => 'int', 'precision' => '2', 'nullable' => False),
				'godkjentbelop' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'default' => '0', 'nullable' => True),
				'spvend_code' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'oppsynsmannid' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
				'saksbehandlerid' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
				'budsjettansvarligid' => array('type' => 'varchar', 'precision' => '12', 'nullable' => False),
				'utbetalingid' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
				'oppsynsigndato' => array('type' => 'timestamp', 'nullable' => True),
				'saksigndato' => array('type' => 'timestamp', 'nullable' => True),
				'budsjettsigndato' => array('type' => 'timestamp', 'nullable' => True),
				'utbetalingsigndato' => array('type' => 'timestamp', 'nullable' => True),
				'overftid' => array('type' => 'timestamp', 'nullable' => True)
			),
			'pk' => array('bilagsnr'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecobilag_process_code' => array(
			'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => 10, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_ecobilag_process_log' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'bilagsnr' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'process_code' => array('type' => 'varchar', 'precision' => 10, 'nullable' => true),
				'process_log' => array('type' => 'text', 'nullable' => true),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_ecobilag' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'bilagsnr' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'bilagsnr_ut' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'kidnr' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'typeid' => array('type' => 'int', 'precision' => '2', 'nullable' => False),
				'kildeid' => array('type' => 'int', 'precision' => '2', 'nullable' => False),
				'project_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'kostra_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'pmwrkord_code' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'belop' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'default' => '0',
					'nullable' => False),
				'fakturadato' => array('type' => 'timestamp', 'nullable' => False),
				'periode' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'forfallsdato' => array('type' => 'timestamp', 'nullable' => False),
				'fakturanr' => array('type' => 'varchar', 'precision' => '15', 'nullable' => False),
				'spbudact_code' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'regtid' => array('type' => 'timestamp', 'nullable' => False),
				'artid' => array('type' => 'int', 'precision' => '2', 'nullable' => False),
				'godkjentbelop' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'default' => '0', 'nullable' => True),
				'spvend_code' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'dima' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'loc1' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True),
				'dimb' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'mvakode' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'dimd' => array('type' => 'varchar', 'precision' => '5', 'nullable' => True),
				'dime' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'oppsynsmannid' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
				'saksbehandlerid' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
				'budsjettansvarligid' => array('type' => 'varchar', 'precision' => '12', 'nullable' => False),
				'utbetalingid' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
				'oppsynsigndato' => array('type' => 'timestamp', 'nullable' => True),
				'saksigndato' => array('type' => 'timestamp', 'nullable' => True),
				'budsjettsigndato' => array('type' => 'timestamp', 'nullable' => True),
				'utbetalingsigndato' => array('type' => 'timestamp', 'nullable' => True),
				'merknad' => array('type' => 'text', 'nullable' => True),
				'splitt' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'kreditnota' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'pre_transfer' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'item_type' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'item_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'external_ref' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'currency' => array('type' => 'varchar', 'precision' => '3', 'nullable' => True),
				'process_log' => array('type' => 'text', 'nullable' => True),
				'process_code' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True),
				'periodization' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'periodization_start' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'line_text' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'external_voucher_id' => array('type' => 'int', 'precision' => '8', 'nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecobilagoverf' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'bilagsnr' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'bilagsnr_ut' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'kidnr' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'typeid' => array('type' => 'int', 'precision' => '2', 'nullable' => False),
				'kildeid' => array('type' => 'int', 'precision' => '2', 'nullable' => False),
				'project_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'kostra_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'pmwrkord_code' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'belop' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'default' => '0',
					'nullable' => False),
				'fakturadato' => array('type' => 'timestamp', 'nullable' => False),
				'periode' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'forfallsdato' => array('type' => 'timestamp', 'nullable' => False),
				'fakturanr' => array('type' => 'varchar', 'precision' => '15', 'nullable' => False),
				'spbudact_code' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'regtid' => array('type' => 'timestamp', 'nullable' => False),
				'artid' => array('type' => 'int', 'precision' => '2', 'nullable' => False),
				'godkjentbelop' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'default' => '0', 'nullable' => True),
				'spvend_code' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'dima' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'loc1' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True),
				'dimb' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'mvakode' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'dimd' => array('type' => 'varchar', 'precision' => '5', 'nullable' => True),
				'dime' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'oppsynsmannid' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
				'saksbehandlerid' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
				'budsjettansvarligid' => array('type' => 'varchar', 'precision' => '12', 'nullable' => False),
				'utbetalingid' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
				'oppsynsigndato' => array('type' => 'timestamp', 'nullable' => True),
				'saksigndato' => array('type' => 'timestamp', 'nullable' => True),
				'budsjettsigndato' => array('type' => 'timestamp', 'nullable' => True),
				'utbetalingsigndato' => array('type' => 'timestamp', 'nullable' => True),
				'overftid' => array('type' => 'timestamp', 'nullable' => True),
				'ordrebelop' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'default' => '0', 'nullable' => False),
				'merknad' => array('type' => 'text', 'nullable' => True),
				'splitt' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'filnavn' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'kreditnota' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'item_type' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'item_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'external_ref' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'currency' => array('type' => 'varchar', 'precision' => '3', 'nullable' => True),
				'process_log' => array('type' => 'text', 'nullable' => True),
				'process_code' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True),
				'periodization' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'periodization_start' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'manual_record' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'line_text' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'external_voucher_id' => array('type' => 'int', 'precision' => '8', 'nullable' => True),
				'external_updated' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'netto_belop' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True),
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecobilagkilde' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '2', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'description' => array('type' => 'text', 'nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecobilag_category' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '2', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '25', 'nullable' => False)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecodimb' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'org_unit_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'active' => array('type' => 'int', 'precision' => '2', 'nullable' => True, 'default' => 1),
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array('fm_org_unit' => array('org_unit_id' => 'id')),
			'uc' => array()
		),
		'fm_ecodimb_role' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '25', 'nullable' => False)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecodimb_role_user' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'ecodimb' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'role_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'default_user' => array('type' => 'int', 'precision' => '2', 'nullable' => true,
					'default' => 0),
				'active_from' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'active_to' => array('type' => 'int', 'precision' => 4, 'nullable' => True, 'default' => 0),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'expired_on' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'expired_by' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array('fm_ecodimb_role' => array('role_id' => 'id'), 'fm_ecodimb' => array(
					'ecodimb' => 'id'), 'phpgw_accounts' => array('user_id' => 'account_id')),
			'uc' => array()
		),
		'fm_ecodimb_role_user_substitute' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'substitute_user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'start_time' => array('type' => 'int', 'precision' => 8, 'nullable' => false),
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecodimd' => array(
			'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => '5', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '25', 'nullable' => False)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_eco_periodization' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'active' => array('type' => 'int', 'precision' => '2', 'nullable' => True, 'default' => '0'),
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_eco_periodization_outline' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'periodization_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'month' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'value' => array('type' => 'decimal', 'precision' => '20', 'scale' => '6', 'nullable' => false,
					'default' => '0.000000'),
				'dividend' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'divisor' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'remark' => array('type' => 'varchar', 'precision' => '60', 'nullable' => False),
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array('fm_eco_periodization' => array('periodization_id' => 'id')),
			'uc' => array('periodization_id', 'month')
		),
		'fm_eco_period_transition' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'month' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'day' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'hour' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'remark' => array('type' => 'varchar', 'precision' => '60', 'nullable' => true),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array('month')
		),
		'fm_order_dim1' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecomva' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'percent' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecologg' => array(
			'fd' => array(
				'batchid' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'ecobilagid' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'status' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'melding' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'tid' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp')
			),
			'pk' => array(),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_ecouser' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'lid' => array('type' => 'varchar', 'precision' => '25', 'nullable' => False),
				'initials' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True)
			),
			'pk' => array('id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'fm_event_action' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
				'action' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
				'data' => array('type' => 'text', 'nullable' => True),
				'descr' => array('type' => 'text', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_event' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'location_item_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'attrib_id' => array('type' => 'varchar', 'precision' => 50, 'default' => '0',
					'nullable' => true),
				'responsible_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'action_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'descr' => array('type' => 'text', 'nullable' => True),
				'start_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'end_date' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'repeat_type' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'repeat_day' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'repeat_interval' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'enabled' => array('type' => 'int', 'precision' => 2, 'nullable' => true),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('location_id', 'location_item_id', 'attrib_id')
		),
		'fm_event_exception' => array(
			'fd' => array(
				'event_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'exception_time' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('event_id', 'exception_time'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_event_schedule' => array(
			'fd' => array(
				'event_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'schedule_time' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('event_id', 'schedule_time'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_event_receipt' => array(
			'fd' => array(
				'event_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'receipt_time' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('event_id', 'receipt_time'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_request_condition_type' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '50', 'nullable' => true),
				'priority_key' => array('type' => 'int', 'precision' => '4', 'default' => '1',
					'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_projectbranch' => array(
			'fd' => array(
				'project_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'branch_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False)
			),
			'pk' => array('project_id', 'branch_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_project_status' => array(
			'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'approved' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'closed' => array('type' => 'int', 'precision' => '2', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_project' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'parent_id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'project_type_id' => array('type' => 'int', 'precision' => '2', 'nullable' => true),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'access' => array('type' => 'varchar', 'precision' => '7', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'start_date' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'end_date' => array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'coordinator' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'status' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False,
					'default' => 'active'),
				'descr' => array('type' => 'text', 'nullable' => True),
				'budget' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
					'default' => '0.00'),
				'reserve' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'p_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'p_cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => '150', 'nullable' => True),
				'tenant_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'contact_phone' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'key_fetch' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'key_deliver' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'other_branch' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'key_responsible' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'external_project_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'planned_cost' => array('type' => 'int', 'precision' => '4', 'nullable' => True,
					'default' => '0'),
				'account_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'ecodimb' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'account_group' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'b_account_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'inherit_location' => array('type' => 'int', 'precision' => 2, 'nullable' => True,
					'default' => 1),
				'periodization_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'delivery_address' => array('type' => 'text', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_project_budget' => array(
			'fd' => array(
				'project_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'month' => array('type' => 'int', 'precision' => 2, 'nullable' => False, 'default' => 0),
				'budget' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
					'default' => '0.00'),
				'order_amount' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'closed' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'active' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('project_id', 'year', 'month'),
			'fk' => array('fm_project' => array('project_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'fm_project_buffer_budget' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'month' => array('type' => 'int', 'precision' => 2, 'nullable' => False, 'default' => 0),
				'buffer_project_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'amount_in' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'from_project' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'amount_out' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'to_project' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'active' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'remark' => array('type' => 'text', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_external_project' => array(
			'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => '10', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'budget' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'active' => array('type' => 'int', 'precision' => 2, 'nullable' => True, 'default' => 1),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_unspsc_code' => array(
			'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => '15', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_investment' => array(
			'fd' => array(
				'entity_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'invest_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'entity_type' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'p_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'p_cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => '150', 'nullable' => True),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'writeoff_year' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('entity_id', 'invest_id'),
			'fk' => array(),
			'ix' => array('location_code', 'entity_id'),
			'uc' => array()
		),
		'fm_investment_value' => array(
			'fd' => array(
				'entity_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'invest_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'index_count' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'current_index' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'this_index' => array('type' => 'decimal', 'precision' => '20', 'scale' => '4',
					'default' => '0', 'nullable' => True),
				'initial_value' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'default' => '0', 'nullable' => True),
				'value' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'default' => '0',
					'nullable' => True),
				'index_date' => array('type' => 'timestamp', 'nullable' => True, 'default' => 'current_timestamp')
			),
			'pk' => array('entity_id', 'invest_id', 'index_count'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_idgenerator' => array(
			'fd' => array(
				'name' => array('type' => 'varchar', 'precision' => '30', 'nullable' => False),
				'start_date' => array('type' => 'int', 'precision' => '4', 'nullable' => False,
					'default' => '0'),
				'value' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'increment' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'descr' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
			),
			'pk' => array('name', 'start_date'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_document' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'title' => array('type' => 'varchar', 'precision' => '100', 'nullable' => True),
				'document_name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'link' => array('type' => 'text', 'nullable' => True),
				'descr' => array('type' => 'text', 'nullable' => True),
				'version' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True),
				'document_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'status' => array('type' => 'varchar', 'precision' => '10', 'nullable' => True),
				'p_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'p_cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => '150', 'nullable' => True),
				'coordinator' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'vendor_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'branch_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'access' => array('type' => 'varchar', 'precision' => '7', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_document_relation' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'document_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'location_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'location_item_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array('fm_document' => array('document_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'fm_document_status' => array(
			'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_request_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_appname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'char', 'precision' => '2', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_old_value' => array('type' => 'text', 'nullable' => true),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_workorder_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_appname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'char', 'precision' => '2', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_old_value' => array('type' => 'text', 'nullable' => true),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_project_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_appname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'char', 'precision' => '2', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_old_value' => array('type' => 'text', 'nullable' => true),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tts_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_appname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'varchar', 'precision' => '3', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_old_value' => array('type' => 'text', 'nullable' => true),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp'),
				'publish' => array('type' => 'int', 'precision' => 2, 'nullable' => True)
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_document_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_appname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'char', 'precision' => '2', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_old_value' => array('type' => 'text', 'nullable' => true),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tenant_claim_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_appname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'char', 'precision' => '2', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_old_value' => array('type' => 'text', 'nullable' => true),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_generic_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'char', 'precision' => '2', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_old_value' => array('type' => 'text', 'nullable' => true),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp'),
				'history_attrib_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'location_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'app_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_owner' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'abid' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'org_name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'contact_name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'member_of' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'remark' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True) // record owner
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_owner_category' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_cache' => array(
			'fd' => array(
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'value' => array('type' => 'text', 'nullable' => True)
			),
			'pk' => array('name'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_entity' => array(
			'fd' => array(
				'location_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'location_form' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'documentation' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'lookup_entity' => array('type' => 'text', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_entity_category' => array(
			'fd' => array(
				'location_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '100', 'nullable' => True),
				'descr' => array('type' => 'text', 'nullable' => True),
				'prefix' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'lookup_tenant' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'tracking' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'location_level' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'location_link_level' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'fileupload' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'loc_link' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'start_project' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'start_ticket' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'is_eav' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'enable_bulk' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'enable_controller' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'jasperupload' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'parent_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'level' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'org_unit' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'entity_group_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
			),
			'pk' => array('entity_id', 'id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_entity_lookup' => array(
			'fd' => array(
				'entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'location' => array('type' => 'varchar', 'precision' => '15', 'nullable' => False),
				'type' => array('type' => 'varchar', 'precision' => '15', 'nullable' => False)
			),
			'pk' => array('entity_id', 'location', 'type'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_entity_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_appname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'history_attrib_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'char', 'precision' => '2', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_old_value' => array('type' => 'text', 'nullable' => true),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_entity_group' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => true),
				'active' => array('type' => 'int', 'precision' => 2, 'nullable' => True, 'default' => 0),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'entry_date' => array('type' => 'int', 'precision' => 8, 'nullable' => False),
				'modified_date' => array('type' => 'int', 'precision' => 8, 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),/*
		'fm_entity_1_1' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'p_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'p_cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => '150', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'status' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'ext_system_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'maaler_nr' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'remark' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array('num')
		),
		'fm_entity_1_2' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => '16', 'nullable' => False),
				'p_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'p_cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => '150', 'nullable' => True),
				'tenant_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'contact_phone' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'status' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'attribute1' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
				'attribute2' => array('type' => 'timestamp', 'nullable' => True),
				'attribute3' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'attribute4' => array('type' => 'text', 'nullable' => True),
				'attribute5' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_entity_1_3' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => '16', 'nullable' => False),
				'p_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'p_cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => '150', 'nullable' => True),
				'tenant_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'contact_phone' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'status' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'attribute1' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
				'attribute2' => array('type' => 'timestamp', 'nullable' => True),
				'attribute3' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'attribute4' => array('type' => 'text', 'nullable' => True),
				'attribute5' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_entity_2_1' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => '16', 'nullable' => False),
				'p_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'p_cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => '150', 'nullable' => True),
				'tenant_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'contact_phone' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'status' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'attribute1' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
				'attribute2' => array('type' => 'timestamp', 'nullable' => True),
				'attribute3' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'attribute4' => array('type' => 'text', 'nullable' => True),
				'attribute5' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),
		'fm_entity_2_2' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => '16', 'nullable' => False),
				'p_num' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'p_cat_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => True),
				'loc1' => array('type' => 'varchar', 'precision' => '6', 'nullable' => True),
				'loc2' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc3' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'loc4' => array('type' => 'varchar', 'precision' => '4', 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => '150', 'nullable' => True),
				'tenant_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'contact_phone' => array('type' => 'varchar', 'precision' => '30', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'status' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'attribute1' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
				'attribute2' => array('type' => 'timestamp', 'nullable' => True),
				'attribute3' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'attribute4' => array('type' => 'text', 'nullable' => True),
				'attribute5' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('location_code'),
			'uc' => array()
		),*/
		'fm_custom' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '100', 'nullable' => False),
				'sql_text' => array('type' => 'text', 'nullable' => False),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_custom_cols' => array(
			'fd' => array(
				'custom_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '100', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'sorting' => array('type' => 'int', 'precision' => '4', 'nullable' => False)
			),
			'pk' => array('custom_id', 'id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_orders' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'type' => array('type' => 'varchar', 'precision' => 50, 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_order_template' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'content' => array('type' => 'text', 'nullable' => True),
				'public' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_response_template' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'content' => array('type' => 'text', 'nullable' => True),
				'public' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_s_agreement' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'vendor_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True),
				'status' => array('type' => 'varchar', 'precision' => 10, 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'member_of' => array('type' => 'text', 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'start_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'end_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'termination_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'actual_cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,
					'nullable' => True),
				'account_id' => array('type' => 'varchar', 'precision' => 20, 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_s_agreement_budget' => array(
			'fd' => array(
				'agreement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'budget_account' => array('type' => 'varchar', 'precision' => 15, 'nullable' => False),
				'ecodimb' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'budget' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => True,
					'default' => '0.00'),
				'actual_cost' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2',
					'nullable' => True, 'default' => '0.00'),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('agreement_id', 'year'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_s_agreement_category' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'descr' => array('type' => 'varchar', 'precision' => 50, 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_s_agreement_detail' => array(
			'fd' => array(
				'agreement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False,
					'default' => '0'),
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'location_code' => array('type' => 'varchar', 'precision' => 30, 'nullable' => True),
				'address' => array('type' => 'varchar', 'precision' => 150, 'nullable' => True),
				'p_num' => array('type' => 'varchar', 'precision' => 15, 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True,
					'default' => '0'),
				'p_cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True, 'default' => '0'),
				'descr' => array('type' => 'text', 'nullable' => True),
				'unit' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'quantity' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2, 'nullable' => True),
				'frequency' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'test' => array('type' => 'text', 'nullable' => True),
				'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2, 'nullable' => True)
			),
			'pk' => array('agreement_id', 'id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_s_agreement_pricing' => array(
			'fd' => array(
				'agreement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False,
					'default' => '0'),
				'item_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'current_index' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'this_index' => array('type' => 'decimal', 'precision' => 20, 'scale' => 4, 'nullable' => True),
				'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2, 'nullable' => True),
				'index_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('agreement_id', 'item_id', 'id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_s_agreement_history' => array(
			'fd' => array(
				'history_id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'history_record_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_appname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'history_detail_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_attrib_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_owner' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'history_status' => array('type' => 'char', 'precision' => '2', 'nullable' => False),
				'history_new_value' => array('type' => 'text', 'nullable' => False),
				'history_old_value' => array('type' => 'text', 'nullable' => true),
				'history_timestamp' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp')
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_async_method' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'data' => array('type' => 'text', 'nullable' => True),
				'descr' => array('type' => 'text', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_cron_log' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'cron' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'cron_date' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp'),
				'process' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'message' => array('type' => 'text', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tenant_claim' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'project_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'tenant_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'amount' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'default' => '0',
					'nullable' => True),
				'b_account_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'status' => array('type' => 'varchar', 'precision' => '8', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'entry_date' => array('type' => 'int', 'precision' => '4', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_tenant_claim_category' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_budget_basis' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'b_group' => array('type' => 'varchar', 'precision' => '4', 'nullable' => False),
				'district_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'revision' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'access' => array('type' => 'varchar', 'precision' => '7', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'budget_cost' => array('type' => 'int', 'precision' => 4, 'default' => '0', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'distribute_year' => array('type' => 'text', 'nullable' => True),
				'ecodimb' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('year', 'b_group', 'district_id', 'revision')
		),
		'fm_budget' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'b_account_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'district_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'revision' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'access' => array('type' => 'varchar', 'precision' => '7', 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'budget_cost' => array('type' => 'int', 'precision' => 4, 'default' => '0', 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True),
				'ecodimb' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'category' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('year', 'b_account_id', 'district_id', 'revision')
		),
		'fm_budget_period' => array(
			'fd' => array(
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'month' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'b_account_id' => array('type' => 'varchar', 'precision' => 4, 'nullable' => False),
				'per_cent' => array('type' => 'int', 'precision' => 4, 'default' => '0', 'nullable' => True), //'percent' is reserved for mssql
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True)
			),
			'pk' => array('year', 'month', 'b_account_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_budget_cost' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'year' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'month' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'b_account_id' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'amount' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'default' => '0',
					'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('year', 'month', 'b_account_id')
		),
		'fm_responsibility' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_responsibility_module' => array(
			'fd' => array(
				'responsibility_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'active' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
			),
			'pk' => array('responsibility_id', 'location_id', 'cat_id'),
			'fk' => array
					(
						'fm_responsibility' => array('responsibility_id' => 'id'),
						'phpgw_locations' 	=> array('location_id' => 'location_id'),
						'phpgw_categories'	=> array('cat_id' => 'cat_id')
					),
			'ix' => array(),
			'uc' => array()
		),
		'fm_responsibility_role' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'remark' => array('type' => 'text', 'nullable' => True),
				'location_level' => array('type' => 'varchar', 'precision' => 200),
				'responsibility_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'appname' => array('type' => 'varchar', 'precision' => 25, 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array('fm_responsibility' => array('responsibility_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'fm_responsibility_contact' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'responsibility_role_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'location_code' => array('type' => 'varchar', 'precision' => 20, 'nullable' => True),
				'p_num' => array('type' => 'varchar', 'precision' => 15, 'nullable' => True),
				'p_entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True,
					'default' => '0'),
				'p_cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True, 'default' => '0'),
				'priority' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'active_from' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'active_to' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'expired_on' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'expired_by' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(
				'fm_responsibility_role' => array('responsibility_role_id' => 'id'),
				'phpgw_contact' => array('contact_id' => 'contact_id')
			),
			'ix' => array('location_code'),
			'uc' => array()
 		),
		'fm_action_pending' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'item_id' => array('type' => 'int', 'precision' => 8, 'nullable' => False),
				'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'responsible' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'responsible_type' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'action_category' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'action_requested' => array('type' => 'int', 'precision' => 4, 'nullable' => True), //timestamp
				'action_deadline' => array('type' => 'int', 'precision' => 4, 'nullable' => True), //timestamp
				'action_performed' => array('type' => 'int', 'precision' => 4, 'nullable' => True), //timestamp
				'reminder' => array('type' => 'int', 'precision' => 4, 'nullable' => True, 'default' => '1'),
				'created_on' => array('type' => 'int', 'precision' => 4, 'nullable' => False), //timestamp
				'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'expired_on' => array('type' => 'int', 'precision' => 4, 'nullable' => True), //timestamp
				'expired_by' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'remark' => array('type' => 'text', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_action_pending_category' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'num' => array('type' => 'varchar', 'precision' => 25, 'nullable' => True),
				'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => True),
				'descr' => array('type' => 'text', 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('num')
		),
		'fm_jasper' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'title' => array('type' => 'varchar', 'precision' => 100, 'nullable' => true),
				'descr' => array('type' => 'varchar', 'precision' => 255, 'nullable' => true),
				'formats' => array('type' => 'varchar', 'precision' => 255, 'nullable' => true),
				'version' => array('type' => 'varchar', 'precision' => 10, 'nullable' => true),
				'access' => array('type' => 'varchar', 'precision' => 7, 'nullable' => true),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_jasper_input_type' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false), // i.e: date/ integer
				'descr' => array('type' => 'varchar', 'precision' => 255, 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_jasper_format_type' => array(
			'fd' => array(
				'id' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false), // i.e: pdf/xls
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_jasper_input' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'jasper_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'input_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'is_id' => array('type' => 'int', 'precision' => 2, 'nullable' => true),
				'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
				'descr' => array('type' => 'varchar', 'precision' => 255, 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(
				'fm_jasper_input_type' => array('input_type_id' => 'id'),
				'fm_jasper' => array('jasper_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'fm_custom_menu_items' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'parent_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'text' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'url' => array('type' => 'text', 'nullable' => True),
				'target' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
				'location' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'local_files' => array('type' => 'int', 'precision' => 2, 'nullable' => true),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_regulations' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'parent_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'descr' => array('type' => 'text', 'nullable' => True),
				'external_ref' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location_contact' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'location_code' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array('fm_locations' => array('location_code' => 'location_code'), 'phpgw_contact' => array(
					'contact_id' => 'contact_id')),
			'ix' => array(),
			'uc' => array('contact_id', 'location_code')
		),
		'fm_view_dataset' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'view_name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
				'dataset_name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
				'owner_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_view_dataset_report' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'dataset_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'report_name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => False),
				'report_definition' => array('type' => 'jsonb', 'nullable' => true),
				'owner_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array('fm_view_dataset' => array('dataset_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location_exception_severity' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location_exception_category' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'parent_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location_exception_category_text' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'category_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'content' => array('type' => 'text', 'nullable' => True),
				),
			'pk' => array('id'),
			'fk' => array('fm_location_exception_category' => array('category_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'fm_location_exception' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'location_code' => array('type' => 'varchar', 'precision' => 20, 'nullable' => False),
				'severity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'category_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'category_text_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'descr' => array('type' => 'text', 'nullable' => True),
				'start_date' => array('type' => 'int', 'precision' => 8, 'nullable' => False),
				'end_date' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
				'reference' => array('type' => 'text', 'nullable' => True),
				'alert_vendor' => array('type' => 'int', 'precision' => 2, 'nullable' => true),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array('fm_location_exception_severity' => array('severity_id' => 'id'),
				'fm_location_exception_category' => array('category_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		)
	);