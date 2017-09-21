<?php
	/**
	 * phpGroupWare - eventplanner.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package eventplanner
	 * @subpackage setup
	 * @version $Id: tables_update.inc.php 14728 2016-02-11 22:28:46Z sigurdne $
	 */
	/**
	 * Update eventplanner version from 0.9.17.000 to 0.9.17.001
	 */
	$test[] = '0.9.18.001';

	function eventplanner_upgrade0_9_18_001()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'eventplanner_booking_comment', array(
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
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplanner']['currentver'] = '0.9.18.002';
		}
		return $GLOBALS['setup_info']['eventplanner']['currentver'];
	}

	$test[] = '0.9.18.002';

	function eventplanner_upgrade0_9_18_002()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_application', 'num_granted_events', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True,'default' => 0));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplanner']['currentver'] = '0.9.18.003';
		}
		return $GLOBALS['setup_info']['eventplanner']['currentver'];
	}

	$test[] = '0.9.18.003';

	function eventplanner_upgrade0_9_18_003()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('eventplanner_vendor', 'vendor_organization_number', 'organization_number');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('eventplanner_customer', 'customer_organization_number', 'organization_number');

		$GLOBALS['phpgw_setup']->oProc->DropColumn('eventplanner_vendor', array(), 'vendor_identifier_type');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('eventplanner_vendor', array(), 'vendor_ssn');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('eventplanner_customer', array(), 'customer_identifier_type');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('eventplanner_customer', array(), 'customer_ssn');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplanner']['currentver'] = '0.9.18.004';
		}
		return $GLOBALS['setup_info']['eventplanner']['currentver'];
	}

	$test[] = '0.9.18.004';

	function eventplanner_upgrade0_9_18_004()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->DropTable('eventplanner_resource_comment');
		$GLOBALS['phpgw_setup']->oProc->DropTable('eventplanner_booking_resource');
		$GLOBALS['phpgw_setup']->oProc->DropTable('eventplanner_resource');

		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_vendor', 'owner_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_vendor', 'public', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_customer', 'owner_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_customer', 'public', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_booking', 'owner_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_booking', 'public', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_order', 'owner_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => false));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_application', 'public', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_booking_vendor_report', 'owner_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_booking_vendor_report', 'public', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_booking_customer_report', 'owner_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_booking_customer_report', 'public', array(
			'type' => 'int', 'precision' => 2, 'nullable' => True));

		$sql = "UPDATE eventplanner_vendor SET owner_id = 7";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$sql = "UPDATE eventplanner_customer SET owner_id = 7";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$sql = "UPDATE eventplanner_booking SET owner_id = 7";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$sql = "UPDATE eventplanner_booking_vendor_report SET owner_id = 7";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$sql = "UPDATE eventplanner_booking_customer_report SET owner_id = 7";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('eventplanner_vendor', 'owner_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => false));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('eventplanner_customer', 'owner_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => false));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('eventplanner_booking', 'owner_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => false));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('eventplanner_booking_vendor_report', 'owner_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => false));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('eventplanner_booking_customer_report', 'owner_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => false));


		$GLOBALS['phpgw']->locations->delete('eventplanner', '.resource');
		$GLOBALS['phpgw']->locations->add('.events', 'events', 'eventplanner', $allow_grant = true);


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplanner']['currentver'] = '0.9.18.005';
		}
		return $GLOBALS['setup_info']['eventplanner']['currentver'];
	}

	$test[] = '0.9.18.005';

	function eventplanner_upgrade0_9_18_005()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'eventplanner_permission', array(
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
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplanner']['currentver'] = '0.9.18.006';
		}
		return $GLOBALS['setup_info']['eventplanner']['currentver'];
	}

	$test[] = '0.9.18.006';
	function eventplanner_upgrade0_9_18_006()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_customer', 'contact2_name', array(
			'type' => 'text',
			'nullable' => true
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_customer', 'contact2_email', array(
			'type' => 'text',
			'nullable' => true
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_customer', 'contact2_phone', array(
			'type' => 'text',
			'nullable' => true
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_customer', 'max_events', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_customer', 'number_of_users', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('eventplanner_customer','account_number', array(
			'type' => 'varchar',
			'precision' => '20',
			'nullable' => true));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplanner']['currentver'] = '0.9.18.007';
		}
		return $GLOBALS['setup_info']['eventplanner']['currentver'];
	}

	$test[] = '0.9.18.007';
	function eventplanner_upgrade0_9_18_007()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.calendar', 'calendar', 'eventplanner', $allow_grant = true);
		$GLOBALS['phpgw']->locations->add('.calendar', 'calendar', 'eventplannerfrontend', $allow_grant = true);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'eventplanner_calendar',  array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => False),
					'application_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'from_' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
					'to_' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
					'active' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '1'),
					'completed' => array('type' => 'int', 'precision' => 4, 'nullable' => False,'default' => '0'),
					'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2, 'nullable' => True,'default' => '0.00'),
					'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
					'public' => array('type' => 'int', 'precision' => '2', 'nullable' => True),
					'created' => array('type' => 'int', 'precision' => '8',  'nullable' => False, 'default' => 'current_timestamp'),
				),
				'pk' => array('id'),
				'fk' => array(
					'eventplanner_application' => array('application_id' => 'id'),
					),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'eventplanner_calendar_comment',  array(
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
			));

			$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_booking', 'calendar_id',
			array
			(
				'type' => 'int',
				'precision' => 4,
				'nullable' => true
			)
		);

		$sql = "SELECT * FROM eventplanner_booking";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		
		$calendars = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$calendars[] = array(
				'id'	=> $GLOBALS['phpgw_setup']->oProc->f('id'),
				'application_id' => $GLOBALS['phpgw_setup']->oProc->f('application_id'),
				'customer_id' => $GLOBALS['phpgw_setup']->oProc->f('customer_id'),
				'from_' => $GLOBALS['phpgw_setup']->oProc->f('from_'),
				'to_' => $GLOBALS['phpgw_setup']->oProc->f('to_'),
				'active' => $GLOBALS['phpgw_setup']->oProc->f('active'),
				'completed' => $GLOBALS['phpgw_setup']->oProc->f('completed'),
				'cost' => $GLOBALS['phpgw_setup']->oProc->f('cost'),
				'customer_contact_name' => $GLOBALS['phpgw_setup']->oProc->f('customer_contact_name'),
				'customer_contact_email' => $GLOBALS['phpgw_setup']->oProc->f('customer_contact_email'),
				'customer_contact_phone' => $GLOBALS['phpgw_setup']->oProc->f('customer_contact_phone'),
				'location' => $GLOBALS['phpgw_setup']->oProc->f('location'),
				'reminder' => $GLOBALS['phpgw_setup']->oProc->f('reminder'),
				'secret' => $GLOBALS['phpgw_setup']->oProc->f('secret'),
				'sms_total' => $GLOBALS['phpgw_setup']->oProc->f('sms_total'),
				'owner_id' => $GLOBALS['phpgw_setup']->oProc->f('owner_id'),
				'public' => $GLOBALS['phpgw_setup']->oProc->f('public'),
				'created' => $GLOBALS['phpgw_setup']->oProc->f('created')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM eventplanner_booking WHERE customer_id IS NULL", __LINE__, __FILE__);

		foreach ($calendars as $calendar)
		{
			$value_set = array(
				'application_id' => $calendar['application_id'],
				'from_' => $calendar['from_'],
				'to_' => $calendar['to_'],
				'active' => $calendar['active'],
				'completed' => $calendar['completed'],
				'cost' => $calendar['cost'],
				'owner_id' => $calendar['owner_id'],
				'public' => $calendar['public'],
				'created' => $calendar['created'],
			);

			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO eventplanner_calendar (' . implode(',', array_keys($value_set)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($value_set)) . ')');
			$calendar_id = (int)$GLOBALS['phpgw_setup']->oProc->m_odb->get_last_insert_id('eventplanner_calendar', 'id');
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE eventplanner_booking SET calendar_id = {$calendar_id}"
			. " WHERE id = {$calendar['id']}", __LINE__, __FILE__);

		}

		$GLOBALS['phpgw_setup']->oProc->query("ALTER TABLE eventplanner_booking DROP CONSTRAINT eventplanner_booking_application_id_fkey", __LINE__, __FILE__);

		$GLOBALS['phpgw_setup']->oProc->DropColumn('eventplanner_booking', array(), 'from_');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('eventplanner_booking', array(), 'to_');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('eventplanner_booking', array(), 'cost');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('eventplanner_booking', array(), 'application_id');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('eventplanner_booking', array(), 'active');


		$sql = "ALTER TABLE public.eventplanner_booking"
			. " ADD CONSTRAINT eventplanner_booking_calendar_id_fkey FOREIGN KEY (calendar_id)"
			. " REFERENCES eventplanner_calendar (id) MATCH SIMPLE"
			. " ON UPDATE NO ACTION ON DELETE NO ACTION";
		
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplanner']['currentver'] = '0.9.18.008';
		}
		return $GLOBALS['setup_info']['eventplanner']['currentver'];
	}

	$test[] = '0.9.18.008';
	function eventplanner_upgrade0_9_18_008()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_application', 'summary', array(
			'type' => 'text',
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplanner']['currentver'] = '0.9.18.009';
		}
		return $GLOBALS['setup_info']['eventplanner']['currentver'];
	}

	$test[] = '0.9.18.009';
	function eventplanner_upgrade0_9_18_009()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_customer', 'grant_non_public', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_application', 'non_public', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplanner']['currentver'] = '0.9.18.010';
		}
		return $GLOBALS['setup_info']['eventplanner']['currentver'];
	}

	$test[] = '0.9.18.010';
	function eventplanner_upgrade0_9_18_010()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_application', 'agreement_1', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_application', 'agreement_2', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplanner']['currentver'] = '0.9.18.011';
		}
		return $GLOBALS['setup_info']['eventplanner']['currentver'];
	}

