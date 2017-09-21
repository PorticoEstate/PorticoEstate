<?php
	/**
	 * phpGroupWare - eventplanner.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package eventplanner
	 * @subpackage setup
	 * @version $Id: tables_current.inc.php 14728 2016-02-11 22:28:46Z sigurdne $
	 */
	$phpgw_baseline = array(
		'eventplanner_vendor_category' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'name' => array('type' => 'text', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_vendor' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'address_1' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'address_2' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'zip_code' => array('type' => 'varchar', 'precision' => '10', 'nullable' => False),
				'city' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'account_number' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False),
				'category_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'active' => array('type' => 'int', 'nullable' => False, 'precision' => '4', 'default' => 1),
				'contact_name' => array('type' => 'text', 'nullable' => False),
				'contact_email' => array('type' => 'text', 'nullable' => False),
				'contact_phone' => array('type' => 'text', 'nullable' => False),
				'description' => array('type' => 'text', 'nullable' => False),
				'remark' => array('type' => 'text', 'nullable' => True),
				'secret' => array('type' => 'text', 'nullable' => False),
				'organization_number' => array('type' => 'varchar', 'precision' => '9','nullable' => True),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'public' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'created' => array('type' => 'int', 'precision' => '8',  'nullable' => False, 'default' => 'current_timestamp'),
				'modified' => array('type' => 'int', 'precision' => '8', 'nullable' => False, 'default' => 'current_timestamp'),
				'json_representation' => array('type' => 'jsonb', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(
				'eventplanner_vendor_category' => array('category_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array('organization_number')
		),
		'eventplanner_vendor_comment' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'vendor_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'time' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'author' => array('type' => 'text', 'nullable' => False),
				'comment' => array('type' => 'text', 'nullable' => False),
				'type' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false,'default' => 'comment'),
			),
			'pk' => array('id'),
			'fk' => array(
				'eventplanner_vendor' => array('vendor_id' => 'id'),
				),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_customer_category' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'name' => array('type' => 'text', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_customer' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'address_1' => array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
				'address_2' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'zip_code' => array('type' => 'varchar', 'precision' => '10', 'nullable' => False),
				'city' => array('type' => 'varchar', 'precision' => '64', 'nullable' => False),
				'account_number' => array('type' => 'varchar', 'precision' => '20', 'nullable' => true),
				'category_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'active' => array('type' => 'int', 'nullable' => False, 'precision' => '4', 'default' => 1),
				'contact_name' => array('type' => 'text', 'nullable' => False),
				'contact_email' => array('type' => 'text', 'nullable' => False),
				'contact_phone' => array('type' => 'text', 'nullable' => False),
				'contact2_name' => array('type' => 'text', 'nullable' => true),
				'contact2_email' => array('type' => 'text', 'nullable' => true),
				'contact2_phone' => array('type' => 'text', 'nullable' => true),
				'number_of_users' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'max_events' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'description' => array('type' => 'text', 'nullable' => False),
				'grant_non_public' => array('type' => 'int', 'precision' => '2', 'nullable' => true),
				'remark' => array('type' => 'text', 'nullable' => True),
				'secret' => array('type' => 'text', 'nullable' => False),
				'organization_number' => array('type' => 'varchar', 'precision' => '9','nullable' => True),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'public' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'created' => array('type' => 'int', 'precision' => '8',  'nullable' => False, 'default' => 'current_timestamp'),
				'modified' => array('type' => 'int', 'precision' => '8', 'nullable' => False, 'default' => 'current_timestamp'),
				'json_representation' => array('type' => 'jsonb', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(
				'eventplanner_customer_category' => array('category_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_customer_comment' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'customer_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'time' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'author' => array('type' => 'text', 'nullable' => False),
				'comment' => array('type' => 'text', 'nullable' => False),
				'type' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false,'default' => 'comment'),
			),
			'pk' => array('id'),
			'fk' => array(
				'eventplanner_customer' => array('customer_id' => 'id'),
				),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_application' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'id_string' => array('type' => 'varchar', 'precision' => '20', 'nullable' => False,'default' => '0'),
				'category_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'vendor_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'active' => array('type' => 'int', 'nullable' => False, 'precision' => '4', 'default' => 1),
				'display_in_dashboard' => array('type' => 'int', 'nullable' => True, 'precision' => '4','default' => 1),
				'status' => array('type' => 'int', 'nullable' => False, 'precision' => 2, 'default' => 1),
				'created' => array('type' => 'int', 'precision' => '8',  'nullable' => False, 'default' => 'current_timestamp'),
				'date_start' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'date_end' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'modified' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'num_granted_events' => array('type' => 'int', 'precision' => '4', 'nullable' => True,'default' => '0'),
				'frontend_modified' => array('type' => 'int', 'precision' => '8', 'nullable' => True),
				'other_participants' => array('type' => 'text', 'nullable' => True),
				'title' => array('type' => 'text', 'nullable' => False),
				'description' => array('type' => 'text', 'nullable' => False),
				'non_public' => array('type' => 'int', 'precision' => '2', 'nullable' => true),
				'summary' => array('type' => 'text', 'nullable' => true),
				'remark' => array('type' => 'text', 'nullable' => True),
				'contact_name' => array('type' => 'text', 'nullable' => False),
				'contact_email' => array('type' => 'text', 'nullable' => False),
				'contact_phone' => array('type' => 'text', 'nullable' => False),
				'secret' => array('type' => 'text', 'nullable' => true),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'public' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'case_officer_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'charge_per_unit' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2','nullable' => true, 'default' => '0.00'),
				'number_of_units' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'timespan' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'stage_width' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2','nullable' => true, 'default' => '0.00'),
				'stage_depth' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2','nullable' => true, 'default' => '0.00'),
				'stage_requirement' => array('type' => 'text', 'nullable' => true),
				'wardrobe' => array('type' => 'int', 'precision' => '2', 'nullable' => true),
				'audience_limit' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'rig_up_min_before' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'rig_up_num_person' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'during_num_person' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'rig_down_num_person' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'rig_down_min_after' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'power' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'sound' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'light' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'piano' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'power_remark' => array('type' => 'text', 'nullable' => true),
				'sound_remark' => array('type' => 'text', 'nullable' => true),
				'light_remark' => array('type' => 'text', 'nullable' => true),
				'piano_remark' => array('type' => 'text', 'nullable' => true),
				'equipment_remark' => array('type' => 'text', 'nullable' => true),
				'raider' => array('type' => 'text', 'nullable' => true),
				'json_representation' => array('type' => 'jsonb', 'nullable' => true),
				'agreement_1' => array('type' => 'int', 'precision' => '2', 'nullable' => true),
				'agreement_2' => array('type' => 'int', 'precision' => '2', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_categories' => array('category_id' => 'cat_id'),
				'eventplanner_vendor' => array('vendor_id' => 'id'),
				'phpgw_accounts' => array('owner_id' => 'account_id'),
				'phpgw_accounts' => array('case_officer_id' => 'account_id'),
			),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_application_type' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'name' => array('type' => 'text', 'nullable' => False),
				'active' => array('type' => 'int', 'precision' => '4', 'nullable' => False, 'default' => 1),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_application_type_relation' => array(
			'fd' => array(
				'application_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'type_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False)
			),
			'pk' => array('application_id', 'type_id'),
			'fk' => array(
				'eventplanner_application' => array('application_id' => 'id'),
				'eventplanner_application_type' => array('type_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_application_comment' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'application_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'time' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'author' => array('type' => 'text', 'nullable' => False),
				'comment' => array('type' => 'text', 'nullable' => False),
				'type' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false,'default' => 'comment'),
			),
			'pk' => array('id'),
			'fk' => array(
				'eventplanner_application' => array('application_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_calendar' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'application_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
	//			'customer_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'from_' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'to_' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'active' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '1'),
				'completed' => array('type' => 'int', 'precision' => 4, 'nullable' => False,'default' => '0'),
				'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2, 'nullable' => True,'default' => '0.00'),
	//			'customer_contact_name' => array('type' => 'text', 'nullable' => True),
	//			'customer_contact_email' => array('type' => 'text', 'nullable' => True),
	//			'customer_contact_phone' => array('type' => 'text', 'nullable' => True),
	//			'location' => array('type' => 'text', 'nullable' => True),
	//			'reminder' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
	//			'secret' => array('type' => 'text', 'nullable' => False),
	//			'sms_total' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'public' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'created' => array('type' => 'int', 'precision' => '8',  'nullable' => False, 'default' => 'current_timestamp'),
			),
			'pk' => array('id'),
			'fk' => array(
	//			'eventplanner_customer' => array('customer_id' => 'id'),
				'eventplanner_application' => array('application_id' => 'id'),
				),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_calendar_comment' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'calendar_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'time' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'author' => array('type' => 'text', 'nullable' => False),
				'comment' => array('type' => 'text', 'nullable' => False),
				'type' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false,'default' => 'comment'),
			),
			'pk' => array('id'),
			'fk' => array(
				'eventplanner_calendar' => array('calendar_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_booking' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'calendar_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'customer_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
//				'from_' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
//				'to_' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
//				'active' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '1'),
				'completed' => array('type' => 'int', 'precision' => 4, 'nullable' => False,'default' => '0'),
//				'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2, 'nullable' => True,'default' => '0.00'),
//				'application_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'customer_contact_name' => array('type' => 'text', 'nullable' => True),
				'customer_contact_email' => array('type' => 'text', 'nullable' => True),
				'customer_contact_phone' => array('type' => 'text', 'nullable' => True),
				'location' => array('type' => 'text', 'nullable' => True),
				'reminder' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				'secret' => array('type' => 'text', 'nullable' => False),
				'sms_total' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'public' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
				'created' => array('type' => 'int', 'precision' => '8',  'nullable' => False, 'default' => 'current_timestamp'),
			),
			'pk' => array('id'),
			'fk' => array(
				'eventplanner_calendar' => array('calendar_id' => 'id'),
				'eventplanner_customer' => array('customer_id' => 'id'),
				),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_booking_comment' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'booking_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'time' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
				'author' => array('type' => 'text', 'nullable' => False),
				'comment' => array('type' => 'text', 'nullable' => False),
				'type' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false,'default' => 'comment'),
			),
			'pk' => array('id'),
			'fk' => array(
				'eventplanner_booking' => array('booking_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_booking_cost' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'booking_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'time' => array('type' => 'int', 'precision' => '8', 'nullable' => False, 'default' => 'current_timestamp'),
				'author' => array('type' => 'text', 'nullable' => False),
				'comment' => array('type' => 'text', 'nullable' => False),
				'cost' => array('type' => 'decimal', 'precision' => 10, 'scale' => 2, 'nullable' => True,'default' => '0.0'),
			),
			'pk' => array('id'),
			'fk' => array(
				'eventplanner_booking' => array('booking_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_order' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'booking_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'time' => array('type' => 'int', 'precision' => '8', 'nullable' => False, 'default' => 'current_timestamp'),
				'author' => array('type' => 'text', 'nullable' => False),
				'description' => array('type' => 'text', 'nullable' => False),
				'dim0' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false),
				'dim1' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false),
				'dim2' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false),
				'dim3' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false),
				'dim4' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false),
				'dim5' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false),
				'dim6' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false),
				'vendor_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'tax_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false),
				'unspsc_code' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false),
				),
			'pk' => array('id'),
			'fk' => array(
				'eventplanner_booking' => array('booking_id' => 'id'),
				'eventplanner_vendor' => array('vendor_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_booking_vendor_report' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'booking_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'created' => array('type' => 'int', 'precision' => '8',  'nullable' => False, 'default' => 'current_timestamp'),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'public' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
//				'author' => array('type' => 'text', 'nullable' => False),
				'json_representation' => array('type' => 'jsonb', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(
				'eventplanner_booking' => array('booking_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_booking_customer_report' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'booking_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'created' => array('type' => 'int', 'precision' => '8',  'nullable' => False, 'default' => 'current_timestamp'),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'public' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
//				'author' => array('type' => 'text', 'nullable' => False),
				'json_representation' => array('type' => 'jsonb', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(
				'eventplanner_booking' => array('booking_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'eventplanner_permission' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'subject_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'object_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'object_type' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'permission' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_accounts' => array('subject_id' => 'account_id'),
			),
			'ix' => array(array('object_id', 'object_type'), array('object_type')),
			'uc' => array('subject_id', 'permission', 'object_type', 'object_id'),
		),
	);
