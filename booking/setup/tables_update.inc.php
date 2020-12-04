<?php
	# Important!!! Append new upgrade functions to the end of this this
	$test[] = '0.1.41';

	function booking_upgrade0_1_41()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN active int not null DEFAULT 1");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_group ADD COLUMN active int not null DEFAULT 1");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ADD COLUMN active int not null DEFAULT 1");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_season ADD COLUMN active int not null DEFAULT 1");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_allocation ADD COLUMN active int not null DEFAULT 1");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ADD COLUMN active int not null DEFAULT 1");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application ADD COLUMN active int not null DEFAULT 1");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.42';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.40';

	function booking_upgrade0_1_40()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		# BEGIN Evil
		$GLOBALS['phpgw_setup']->oProc->DropTable('bb_permission_building');
		$GLOBALS['phpgw_setup']->oProc->DropTable('bb_permission_resource');
		$GLOBALS['phpgw_setup']->oProc->DropTable('bb_permission_season');
		# END Evil

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_permission', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'subject_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'object_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'object_type' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'role' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_accounts' => array('subject_id' => 'account_id'),
			),
			'ix' => array(array('object_id', 'object_type'), array('object_type')),
			'uc' => array(),
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.41';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.39';

	function booking_upgrade0_1_39()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_contact_person ALTER COLUMN ssn TYPE varchar(12) USING NULL");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.40';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.38';

	function booking_upgrade0_1_38()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_permission_season', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'subject_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'object_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'role' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_accounts' => array('subject_id' => 'account_id'),
				'bb_season' => array('object_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array(),
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.39';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.37';

	function booking_upgrade0_1_37()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		# BEGIN Evil
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_permission_root");
		# END Evil

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_permission_root ADD COLUMN \"role\" character varying(255) NOT NULL");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.38';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.36';

	function booking_upgrade0_1_36()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_permission_root', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'subject_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_accounts' => array('subject_id' => 'account_id'),
			),
			'ix' => array(),
			'uc' => array(),
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.37';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.35';

	function booking_upgrade0_1_35()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_permission_building', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'subject_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'object_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'role' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_accounts' => array('subject_id' => 'account_id'),
				'bb_building' => array('object_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array(),
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_permission_resource', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'subject_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'object_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'role' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_accounts' => array('subject_id' => 'account_id'),
				'bb_resource' => array('object_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array(),
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.36';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.34';

	function booking_upgrade0_1_34()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		# BEGIN Evil
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_allocation_resource");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_allocation");
		# END Evil
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_allocation ADD COLUMN cost decimal(10,2) NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ADD COLUMN allocation_id integer");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ADD CONSTRAINT bb_booking_allocation_id_fkey FOREIGN KEY (allocation_id) REFERENCES bb_allocation(id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.35';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.33';

	function booking_upgrade0_1_33()
	{
		$documentOwners = array('building', 'resource');
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		foreach ($documentOwners as $owner)
		{
			$GLOBALS['phpgw_setup']->oProc->CreateTable(
				"bb_document_$owner", array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
					'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'category' => array('type' => 'varchar', 'precision' => '150', 'nullable' => false),
					'description' => array('type' => 'text', 'nullable' => true),
				),
				'pk' => array('id'),
				'fk' => array(
					"bb_$owner" => array('owner_id' => 'id'),
				),
				'ix' => array(),
				'uc' => array()
			));
		}

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.34';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.32';

	function booking_upgrade0_1_32()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN admin_primary int");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN admin_secondary int");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD CONSTRAINT bb_contact_person_primary_fkey FOREIGN KEY (admin_primary) REFERENCES bb_contact_person(id)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD CONSTRAINT bb_contact_person_secondary_fkey FOREIGN KEY (admin_secondary) REFERENCES bb_contact_person(id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.33';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.31';

	function booking_upgrade0_1_31()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_group ADD COLUMN description varchar(250) NOT NULL DEFAULT ''");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.32';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.30';

	function booking_upgrade0_1_30()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_group ADD CONSTRAINT bb_contact_person_primary_fkey FOREIGN KEY (contact_primary) REFERENCES bb_contact_person(id)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_group ADD CONSTRAINT bb_contact_person_secondary_fkey FOREIGN KEY (contact_secondary) REFERENCES bb_contact_person(id)");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.31';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.29';

	function booking_upgrade0_1_29()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_contact_person', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'ssn' => array('type' => 'int', 'precision' => '4', 'nullable' => True,),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'homepage' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'phone' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False,
					'default' => ''),
				'email' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False,
					'default' => ''),
				'description' => array('type' => 'varchar', 'precision' => '1000', 'nullable' => False,
					'default' => ''),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array(),
			)
		);
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_group ADD COLUMN contact_primary int");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_group ADD COLUMN contact_secondary int");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.30';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.28';

	function booking_upgrade0_1_28()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_booking_targetaudience', array(
			'fd' => array(
				'booking_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'targetaudience_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False)
			),
			'pk' => array('booking_id', 'targetaudience_id'),
			'fk' => array(
				'bb_booking' => array('booking_id' => 'id'),
				'bb_targetaudience' => array('targetaudience_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		));
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_booking_agegroup', array(
			'fd' => array(
				'booking_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'agegroup_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'male' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'female' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('booking_id', 'agegroup_id'),
			'fk' => array(
				'bb_booking' => array('booking_id' => 'id'),
				'bb_agegroup' => array('agegroup_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		));
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.29';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.27';

	function booking_upgrade0_1_27()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		# BEGIN Evil
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_application_date");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_application_resource");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_application");
		# END Evil
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application ADD COLUMN status text NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application ADD COLUMN created timestamp DEFAULT 'now' NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application ADD COLUMN modified timestamp DEFAULT 'now' NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application_date ALTER COLUMN from_ TYPE timestamp USING from_::timestamp");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application_date ALTER COLUMN to_ TYPE timestamp USING to_::timestamp");
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_application_targetaudience', array(
			'fd' => array(
				'application_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'targetaudience_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False)
			),
			'pk' => array('application_id', 'targetaudience_id'),
			'fk' => array(
				'bb_application' => array('application_id' => 'id'),
				'bb_targetaudience' => array('targetaudience_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		));
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_application_agegroup', array(
			'fd' => array(
				'application_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'agegroup_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'male' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'female' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('application_id', 'agegroup_id'),
			'fk' => array(
				'bb_application' => array('application_id' => 'id'),
				'bb_agegroup' => array('agegroup_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		));
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.28';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.26';

	function booking_upgrade0_1_26()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN phone varchar(250) not null DEFAULT ''");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN email varchar(250) not null DEFAULT ''");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN description varchar(1000) not null DEFAULT ''");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.27';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.25';

	function booking_upgrade0_1_25()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_application', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'activity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'description' => array('type' => 'text', 'nullable' => False),
				'contact_name' => array('type' => 'text', 'nullable' => False),
				'contact_email' => array('type' => 'text', 'nullable' => False),
				'contact_phone' => array('type' => 'text', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_activity' => array('activity_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		));
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_application_resource', array(
			'fd' => array(
				'application_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'resource_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('application_id', 'resource_id'),
			'fk' => array(
				'bb_application' => array('application_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		));
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_application_comment', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'application_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'time' => array('type' => 'text', 'nullable' => False),
				'author' => array('type' => 'text', 'nullable' => False),
				'comment' => array('type' => 'text', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_application' => array('application_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		));
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_application_date', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'application_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'from_' => array('type' => 'text', 'nullable' => False),
				'to_' => array('type' => 'text', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_application' => array('application_id' => 'id')),
			'ix' => array(),
			'uc' => array('application_id', 'from_', 'to_')
		));
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.26';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.24';

	function booking_upgrade0_1_24()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_allocation', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'organization_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'from_' => array('type' => 'timestamp', 'nullable' => False),
				'to_' => array('type' => 'timestamp', 'nullable' => False),
				'season_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_organization' => array('organization_id' => 'id'),
				'bb_season' => array('season_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		));
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_allocation_resource', array(
			'fd' => array(
				'allocation_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'resource_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('allocation_id', 'resource_id'),
			'fk' => array(
				'bb_allocation' => array('allocation_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		));
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.25';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.23';

	function booking_upgrade0_1_23()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource DROP COLUMN address");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource DROP COLUMN phone");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource DROP COLUMN email");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ADD COLUMN description varchar(1000) not null DEFAULT ''");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.24';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.22';

	function booking_upgrade0_1_22()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN active int not null DEFAULT 1");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.23';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.21';

	function booking_upgrade0_1_21()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query('ALTER TABLE bb_wtemplate_alloc DROP CONSTRAINT "bb_wtemplate_alloc_season_id_key"');
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.22';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.20';

	function booking_upgrade0_1_20()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ADD COLUMN address varchar(1000) not null DEFAULT ''");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ADD COLUMN phone varchar(250) not null DEFAULT ''");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ADD COLUMN email varchar(250) not null DEFAULT ''");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ADD COLUMN activity_id int");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ADD CONSTRAINT bb_resource_activity_id_fkey FOREIGN KEY (activity_id) REFERENCES bb_activity(id)");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.21';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.19';

	function booking_upgrade0_1_19()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		echo("1");
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_agegroup', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'name' => array('type' => 'text', 'nullable' => False),
				'description' => array('type' => 'text', 'nullable' => False),
				'active' => array('type' => 'int', 'nullable' => False, 'precision' => '4', 'default' => 1),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			echo("2");
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.20';
			echo("3");
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.18';

	function booking_upgrade0_1_18()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		echo("1");
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_targetaudience', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'name' => array('type' => 'text', 'nullable' => False),
				'description' => array('type' => 'text', 'nullable' => False),
				'active' => array('type' => 'int', 'nullable' => False, 'precision' => '4', 'default' => 1),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			echo("2");
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.19';
			echo("3");
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.17';

	function booking_upgrade0_1_17()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_wtemplate_alloc', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'season_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'organization_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wday' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'cost' => array('type' => 'decimal', 'precision' => '10', 'scale' => '2', 'nullable' => False),
				'from_' => array('type' => 'time', 'nullable' => False),
				'to_' => array('type' => 'time', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_season' => array('season_id' => 'id'),
				'bb_organization' => array('organization_id' => 'id')
			),
			'ix' => array(),
			'uc' => array('season_id', 'wday', 'from_')
			)
		);
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_wtemplate_alloc_resource', array(
			'fd' => array(
				'allocation_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'resource_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('allocation_id', 'resource_id'),
			'fk' => array(
				'bb_wtemplate_alloc' => array('allocation_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.18';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.16';

	function booking_upgrade0_1_16()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->DropTable('bb_season_wday');
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_season_boundary', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'season_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wday' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'from_' => array('type' => 'time', 'nullable' => False),
				'to_' => array('type' => 'time', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_season' => array('season_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.17';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.15';

	function booking_upgrade0_1_15()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_activity', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => FALSE),
				'parent_id' => array('type' => 'int', 'precision' => '4', 'nullable' => TRUE),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => FALSE),
				'description' => array('type' => 'varchar', 'precision' => '10000', 'nullable' => FALSE),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_activity' => array('parent_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.16';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.14';

	function booking_upgrade0_1_14()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking_resource ADD PRIMARY KEY (booking_id, resource_id)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_season_resource ADD PRIMARY KEY (season_id, resource_id)");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.15';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.13';

	function booking_upgrade0_1_13()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		# Using raw sql since AlterColumn doesn't support "using"
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_booking_resource");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_booking");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking DROP COLUMN date");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ALTER from_ TYPE timestamp USING NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ALTER to_ TYPE timestamp USING NULL");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.14';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.12';

	function booking_upgrade0_1_12()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_equipment', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'resource_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'description' => array('type' => 'varchar', 'precision' => '10000', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.13';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.11';

	function booking_upgrade0_1_11()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		# Using raw sql since AddColumn is buggy and ignores "default"
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN description varchar(1000) NOT NULL DEFAULT ''");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN address varchar(250) NOT NULL DEFAULT ''");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN phone varchar(50) NOT NULL DEFAULT ''");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN email varchar(50) NOT NULL DEFAULT ''");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.12';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.10';

	function booking_upgrade0_1_10()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		# Using raw sql since AlterColumn doesn't support "using"
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ALTER from_ TYPE time USING NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ALTER to_ TYPE time USING NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_season_wday ALTER from_ TYPE time USING NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_season_wday ALTER to_ TYPE time USING NULL");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.11';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.9';

	function booking_upgrade0_1_9()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_season_resource', array(
			'fd' => array(
				'season_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'resource_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('season_id', 'resource_id'),
			'fk' => array(
				'bb_season' => array('season_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.10';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.8';

	function booking_upgrade0_1_8()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->DropTable('bb_bookingrelations');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('bb_booking', array(), 'resources');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('bb_booking', array(), 'category');
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ADD CONSTRAINT bb_booking_group_id_fkey FOREIGN KEY (group_id) REFERENCES bb_group(id)");
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_booking_resource', array(
			'fd' => array(
				'booking_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'resource_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_booking' => array('booking_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.9';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.7';

	function booking_upgrade0_1_7()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_bookingrelations', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'bb_booking_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'bb_resource_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_booking' => array('bb_booking_id' => 'id'),
				'bb_resource' => array('bb_resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.8';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.6';

	function booking_upgrade0_1_6()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_booking', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'category' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'resources' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'group_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'from_' => array('type' => 'varchar', 'precision' => '5', 'nullable' => False),
				'to_' => array('type' => 'varchar', 'precision' => '5', 'nullable' => False),
				'date' => array('type' => 'date', 'precision' => '50', 'nullable' => False),
				'season_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_group' => array('group_id' => 'id'),
				'bb_season' => array('season_id' => 'id')),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.7';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.5';

	function booking_upgrade0_1_5()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_group', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'organization_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_organization' => array('organization_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.6';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.4';

	function booking_upgrade0_1_4()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_season_wday', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'season_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wday' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'from_' => array('type' => 'varchar', 'precision' => '5', 'nullable' => False),
				'to_' => array('type' => 'varchar', 'precision' => '5', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_season' => array('season_id' => 'id')
			),
			'ix' => array(),
			'uc' => array('season_id', 'wday')
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_season', 'status', array('type' => 'varchar',
			'precision' => 10, 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_season', 'from_', array('type' => 'date',
			'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_season', 'to_', array('type' => 'date',
			'nullable' => False));
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.5';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.3';

	function booking_upgrade0_1_3()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_season', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'building_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_building' => array('building_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.4';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.2';

	function booking_upgrade0_1_2()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_resource', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'building_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_building' => array('building_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.3';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.1';

	function booking_upgrade0_1_1()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_organization', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'homepage' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.2';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1';

	function booking_upgrade0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_booking');
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_building', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'homepage' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.1';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.42';

	function booking_upgrade0_1_42()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_permission ADD CONSTRAINT bb_permission_subject_id_key UNIQUE (subject_id, role, object_type, object_id)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_permission_root ADD CONSTRAINT bb_permission_root_subject_id_key UNIQUE (subject_id, role)");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.43';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.43';

	function booking_upgrade0_1_43()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_season ADD COLUMN officer_id int");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_season ADD CONSTRAINT bb_season_officer_id_fkey FOREIGN KEY (officer_id) REFERENCES phpgw_accounts(account_id)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_season set officer_id=(SELECT account_id FROM phpgw_accounts WHERE account_lid='admin' LIMIT 1)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_season ALTER COLUMN officer_id SET NOT NULL");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.44';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.44';

	function booking_upgrade0_1_44()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN street character varying(255) NOT NULL DEFAULT ''");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN zip_code character varying(255) NOT NULL DEFAULT ''");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN city character varying(255) NOT NULL DEFAULT ''");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN district character varying(255) NOT NULL DEFAULT ''");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.45';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.45';

	function booking_upgrade0_1_45()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		# BEGIN Evil
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_application_resource");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_application_date");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_application_comment");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_application_agegroup");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_application_targetaudience");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_application");
		# END Evil
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application_comment ALTER COLUMN time TYPE timestamp USING time::timestamp");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application ADD COLUMN secret TEXT NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application ADD COLUMN owner_id int NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application ADD CONSTRAINT bb_application_owner_id_fkey FOREIGN KEY (owner_id) REFERENCES phpgw_accounts(account_id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.46';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.46';

	function booking_upgrade0_1_46()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization DROP COLUMN admin_primary");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization DROP COLUMN admin_secondary");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_organization_contact', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false,
					'default' => ''),
				'ssn' => array('type' => 'varchar', 'precision' => '12', 'nullable' => false,
					'default' => ''),
				'phone' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false,
					'default' => ''),
				'email' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false,
					'default' => ''),
				'organization_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_organization' => array('organization_id' => 'id'),
			),
			'ix' => array('ssn'),
			'uc' => array(),
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.47';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.47';

	function booking_upgrade0_1_47()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_group DROP COLUMN contact_primary");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_group DROP COLUMN contact_secondary");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_group_contact', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false,
					'default' => ''),
				'phone' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false,
					'default' => ''),
				'email' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false,
					'default' => ''),
				'group_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_group' => array('group_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array(),
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.48';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.48';

	function booking_upgrade0_1_48()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_event', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'active' => array('type' => 'int', 'precision' => '4', 'nullable' => False, 'default' => '1'),
				'activity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'description' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false,
					'default' => ''),
				'from_' => array('type' => 'timestamp', 'nullable' => false),
				'to_' => array('type' => 'timestamp', 'nullable' => false),
				'cost' => array('type' => 'decimal', 'precision' => '10', 'scale' => '2', 'nullable' => False),
				'contact_name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false,
					'default' => ''),
				'contact_email' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false,
					'default' => ''),
				'contact_phone' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false,
					'default' => ''),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_activity' => array('activity_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array(),
			)
		);
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_event_resource', array(
			'fd' => array(
				'event_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'resource_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('event_id', 'resource_id'),
			'fk' => array(
				'bb_event' => array('event_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
			)
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.49';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.49';

	function booking_upgrade0_1_49()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_event_targetaudience', array(
			'fd' => array(
				'event_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'targetaudience_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False)
			),
			'pk' => array('event_id', 'targetaudience_id'),
			'fk' => array(
				'bb_event' => array('event_id' => 'id'),
				'bb_targetaudience' => array('targetaudience_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		));
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_event_agegroup', array(
			'fd' => array(
				'event_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'agegroup_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'male' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'female' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('event_id', 'agegroup_id'),
			'fk' => array(
				'bb_event' => array('event_id' => 'id'),
				'bb_agegroup' => array('agegroup_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		));
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.50';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.50';

	function booking_upgrade0_1_50()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking DROP COLUMN name");
		# BEGIN Evil
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_booking_resource");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_booking_targetaudience");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_booking_agegroup");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_booking");
		# END Evil
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ADD COLUMN activity_id integer NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ADD CONSTRAINT bb_booking_activity_id_fkey FOREIGN KEY (activity_id) REFERENCES bb_activity(id)");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.51';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.51';

	function booking_upgrade0_1_51()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ALTER COLUMN description TYPE text");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.52';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.52';

	function booking_upgrade0_1_52()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building DROP COLUMN address");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN street character varying(255) NOT NULL DEFAULT ''");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN zip_code character varying(255) NOT NULL DEFAULT ''");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN city character varying(255) NOT NULL DEFAULT ''");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN district character varying(255) NOT NULL DEFAULT ''");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.53';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.53';

	function booking_upgrade0_1_53()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ALTER COLUMN homepage TYPE text");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ALTER COLUMN homepage TYPE text");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_contact_person ALTER COLUMN homepage TYPE text");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.54';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.54';

	function booking_upgrade0_1_54()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ALTER COLUMN description TYPE text");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ALTER COLUMN description TYPE text");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ALTER COLUMN description TYPE text");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_group ALTER COLUMN description TYPE text");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.55';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.55';

	function booking_upgrade0_1_55()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		# BEGIN Evil
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_equipment");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DELETE FROM bb_permission WHERE object_type ='equipment'");
		# END Evil

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DROP TABLE bb_equipment");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ADD COLUMN type character varying(50)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_resource SET type = 'Location'");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ALTER COLUMN type SET NOT NULL");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.56';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.56';

	function booking_upgrade0_1_56()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN organization_number character varying(9) NOT NULL DEFAULT ''");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.57';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.57';

	function booking_upgrade0_1_57()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_completed_reservation', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'reservation_type' => array('type' => 'varchar', 'precision' => '70', 'nullable' => False),
				'reservation_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'season_id' => array('type' => 'int', 'precision' => '4'),
				'cost' => array('type' => 'decimal', 'precision' => '10', 'scale' => '2', 'nullable' => False),
				'from_' => array('type' => 'timestamp', 'nullable' => false),
				'to_' => array('type' => 'timestamp', 'nullable' => false),
				'organization_id' => array('type' => 'int', 'precision' => '4'),
				'customer_type' => array('type' => 'varchar', 'precision' => '70', 'nullable' => False),
				'customer_organization_number' => array('type' => 'varchar', 'precision' => '9'),
				'customer_ssn' => array('type' => 'varchar', 'precision' => '12'),
				'exported' => array('type' => 'int', 'precision' => '4', 'nullable' => False,
					'default' => 0),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_organization' => array('organization_id' => 'id'),
				'bb_season' => array('season_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_completed_reservation_resource', array(
			'fd' => array(
				'completed_reservation_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'resource_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('completed_reservation_id', 'resource_id'),
			'fk' => array(
				'bb_completed_reservation' => array('completed_reservation_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ADD COLUMN completed integer NOT NULL DEFAULT 0");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN completed integer NOT NULL DEFAULT 0");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_allocation ADD COLUMN completed integer NOT NULL DEFAULT 0");

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ADD COLUMN cost numeric(10,2) NOT NULL DEFAULT 0.0");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.58';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.58';

	function booking_upgrade0_1_58()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("TRUNCATE TABLE bb_completed_reservation, bb_completed_reservation_resource");
		//$GLOBALS['phpgw_setup']->oProc->m_odb->query("TRUNCATE TABLE bb_completed_reservation_resource");

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation ADD COLUMN description text NOT NULL");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.59';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.59';

	function booking_upgrade0_1_59()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("TRUNCATE TABLE bb_completed_reservation, bb_completed_reservation_resource");

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation ADD COLUMN building_name text NOT NULL");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.60';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.60';

	function booking_upgrade0_1_60()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN activity_id int");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD CONSTRAINT bb_organization_activity_id_fkey FOREIGN KEY (activity_id) REFERENCES bb_activity(id)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_group ADD COLUMN activity_id int");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_group ADD CONSTRAINT bb_group_activity_id_fkey FOREIGN KEY (activity_id) REFERENCES bb_activity(id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.61';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.61';

	function booking_upgrade0_1_61()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("TRUNCATE TABLE bb_completed_reservation, bb_completed_reservation_resource");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation ADD COLUMN article_description character varying(35) NOT NULL");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.62';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.62';

	function booking_upgrade0_1_62()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_completed_reservation_export', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'season_id' => array('type' => 'int', 'precision' => '4'),
				'building_id' => array('type' => 'int', 'precision' => '4'),
				'from_' => array('type' => 'timestamp', 'nullable' => True), /* Should be automatically filled in sometimes */
				'to_' => array('type' => 'timestamp', 'nullable' => True),
				'created_on' => array('type' => 'timestamp', 'nullable' => False),
				'filename' => array('type' => 'text', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_building' => array('building_id' => 'id'),
				'bb_season' => array('season_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array()
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.63';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.63';

	function booking_upgrade0_1_63()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("TRUNCATE TABLE bb_completed_reservation_export, bb_completed_reservation, bb_completed_reservation_resource");

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation ADD COLUMN building_id int NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation ADD CONSTRAINT bb_completed_reservation_building_id_fkey FOREIGN KEY (building_id) REFERENCES bb_building(id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.64';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.64';

	function booking_upgrade0_1_64()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation ADD CONSTRAINT bb_completed_reservation_exported_fkey FOREIGN KEY (exported) REFERENCES bb_completed_reservation_export(id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.65';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.65';

	function booking_upgrade0_1_65()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("TRUNCATE TABLE bb_completed_reservation_export, bb_completed_reservation, bb_completed_reservation_resource");

		//Do it over, do it right!
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation DROP CONSTRAINT bb_completed_reservation_exported_fkey");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation DROP COLUMN exported");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation ADD COLUMN exported int");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation ADD CONSTRAINT bb_completed_reservation_exported_fkey FOREIGN KEY (exported) REFERENCES bb_completed_reservation_export(id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.66';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.66';

	function booking_upgrade0_1_66()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query(
			"ALTER TABLE bb_completed_reservation RENAME COLUMN payee_type TO customer_type"
		);

		$GLOBALS['phpgw_setup']->oProc->m_odb->query(
			"ALTER TABLE bb_completed_reservation RENAME COLUMN payee_organization_number TO customer_organization_number"
		);

		$GLOBALS['phpgw_setup']->oProc->m_odb->query(
			"ALTER TABLE bb_completed_reservation RENAME COLUMN payee_ssn TO customer_ssn"
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.67';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.67';

	function booking_upgrade0_1_67()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_account_code_set', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'name' => array('type' => 'text', 'nullable' => False),
				'object_number' => array('type' => 'varchar', 'precision' => '8', 'nullable' => False),
				'responsible_code' => array('type' => 'varchar', 'precision' => '6', 'nullable' => False),
				'article' => array('type' => 'varchar', 'precision' => '15', 'nullable' => False),
				'service' => array('type' => 'varchar', 'precision' => '8', 'nullable' => False),
				'project_number' => array('type' => 'varchar', 'precision' => '12', 'nullable' => False),
				'unit_number' => array('type' => 'varchar', 'precision' => '12', 'nullable' => False),
				'unit_prefix' => array('type' => 'varchar', 'precision' => '1', 'nullable' => False),
				'invoice_instruction' => array('type' => 'varchar', 'precision' => '120'),
				'active' => array('type' => 'int', 'nullable' => False, 'precision' => '4', 'default' => 1),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);


		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_completed_reservation SET exported=null");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("TRUNCATE TABLE bb_completed_reservation_export CASCADE");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation_export ADD COLUMN account_code_set_id int NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation_export ADD CONSTRAINT bb_completed_reservation_export_account_code_set_id_fkey FOREIGN KEY (account_code_set_id) REFERENCES bb_account_code_set(id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.68';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.68';

	function booking_upgrade0_1_68()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("TRUNCATE TABLE bb_completed_reservation_export CASCADE");

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation_export ADD COLUMN created_by int NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation_export ADD CONSTRAINT bb_completed_reservation_export_created_by_fkey FOREIGN KEY (created_by) REFERENCES phpgw_accounts(account_id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.69';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.69';

	function booking_upgrade0_1_69()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation_export DROP COLUMN filename");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation_export DROP COLUMN account_code_set_id");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_completed_reservation_export_file', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'filename' => array('type' => 'text'),
				'type' => array('type' => 'text', 'nullable' => False),
				'export_id' => array('type' => 'int', 'precision' => '4'),
				'account_code_set_id' => array('type' => 'int', 'precision' => '4'),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_account_code_set' => array('account_code_set_id' => 'id'),
				'bb_completed_reservation_export' => array('export_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array()
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.70';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.70';

	function booking_upgrade0_1_70()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN customer_number text");

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN customer_identifier_type character varying(255)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN customer_organization_number character varying(9)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN customer_ssn character varying(12)");

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application ADD COLUMN customer_identifier_type character varying(255)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application ADD COLUMN customer_organization_number character varying(9)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application ADD COLUMN customer_ssn character varying(12)");

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN customer_identifier_type character varying(255)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN customer_organization_number character varying(9)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN customer_ssn character varying(12)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.71';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.71';

	function booking_upgrade0_1_71()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation ADD COLUMN customer_identifier_type character varying(255)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.72';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.72';

	function booking_upgrade0_1_72()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_allocation ADD COLUMN application_id int");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ADD COLUMN application_id int");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN application_id int");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_allocation ADD CONSTRAINT bb_allocation_application_id_fkey FOREIGN KEY (application_id) REFERENCES bb_application(id)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ADD CONSTRAINT bb_booking_application_id_fkey FOREIGN KEY (application_id) REFERENCES bb_application(id)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD CONSTRAINT bb_event_application_id_fkey FOREIGN KEY (application_id) REFERENCES bb_application(id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.73';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.73';

	function booking_upgrade0_1_73()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query(
			"CREATE VIEW bb_document_view " .
			"AS SELECT bb_document.id AS id, bb_document.name AS name, bb_document.owner_id AS owner_id, bb_document.category AS category, bb_document.description AS description, bb_document.type AS type " .
			"FROM " .
			"((SELECT *, 'building' as type from bb_document_building) UNION ALL (SELECT *, 'resource' as type from bb_document_resource)) " .
			"as bb_document;"
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.74';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.74';

	function booking_upgrade0_1_74()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query(
			"ALTER TABLE bb_activity ADD COLUMN active INT DEFAULT 1 NOT NULL"
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.75';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.75';

	function booking_upgrade0_1_75()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query(
			"CREATE OR REPLACE VIEW bb_application_association AS " .
			"SELECT 'booking' AS type, application_id, id, from_, to_ FROM bb_booking WHERE application_id IS NOT NULL " .
			"UNION " .
			"SELECT 'allocation' AS type, application_id, id, from_, to_ FROM bb_allocation  WHERE application_id IS NOT NULL " .
			"UNION " .
			"SELECT 'event' AS type, application_id, id, from_, to_ FROM bb_event  WHERE application_id IS NOT NULL"
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.76';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.76';

	function booking_upgrade0_1_76()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query(
			"ALTER TABLE bb_application ADD COLUMN display_in_dashboard INT DEFAULT 1 NOT NULL;" .
			"ALTER TABLE bb_application ADD COLUMN case_officer_id int;" .
			"ALTER TABLE bb_application ADD CONSTRAINT bb_case_officer_id_fkey FOREIGN KEY (case_officer_id) REFERENCES phpgw_accounts(account_id);"
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.77';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.77';

	function booking_upgrade0_1_77()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ADD COLUMN reminder INT NOT NULL DEFAULT 1");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ADD COLUMN secret TEXT");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_booking SET secret = substring(md5(from_::text || id::text || group_id::text) from 0 for 11)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ALTER COLUMN secret SET NOT NULL;");

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN reminder INT NOT NULL DEFAULT 1");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN secret TEXT");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_event SET secret = substring(md5(from_::text || id::text || activity_id::text) from 0 for 11)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ALTER COLUMN secret SET NOT NULL;");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.78';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.78';

	function booking_upgrade0_1_78()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query(
			"ALTER TABLE bb_building ADD COLUMN location_code TEXT;"
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.79';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.79';

	function booking_upgrade0_1_79()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application ADD COLUMN frontend_modified timestamp");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.80';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.80';

	function booking_upgrade0_1_80()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application_comment ADD COLUMN type TEXT NOT NULL DEFAULT 'comment'");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.81';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.81';

	function booking_upgrade0_1_81()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ALTER COLUMN reminder SET DEFAULT 0;");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ALTER COLUMN reminder SET DEFAULT 0;");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.82';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.82';

	function booking_upgrade0_1_82()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_targetaudience ADD COLUMN sort INT NOT NULL DEFAULT 0;");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_agegroup ADD COLUMN sort INT NOT NULL DEFAULT 0;");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.83';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.83';

	function booking_upgrade0_1_83()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$table = "bb_completed_reservation_export_file";

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DROP TABLE $table");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("DROP SEQUENCE seq_{$table}");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			$table, array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'filename' => array('type' => 'text'),
				'type' => array('type' => 'text', 'nullable' => False),
				'total_cost' => array('type' => 'decimal', 'precision' => '10', 'scale' => '2',
					'nullable' => False),
				'total_items' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'created_on' => array('type' => 'timestamp', 'nullable' => False),
				'created_by' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_accounts' => array('created_by' => 'account_id'),
			),
			'ix' => array(),
			'uc' => array()
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.84';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.84';

	function booking_upgrade0_1_84()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_completed_reservation_export_configuration', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'type' => array('type' => 'text', 'nullable' => False),
				'export_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'export_file_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'account_code_set_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_account_code_set' => array('account_code_set_id' => 'id'),
				'bb_completed_reservation_export' => array('export_id' => 'id'),
				'bb_completed_reservation_export_file' => array('export_file_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array()
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.85';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.85';

	function booking_upgrade0_1_85()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$table = "bb_completed_reservation_export";

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE $table ADD COLUMN total_cost decimal(10,2)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE $table ADD COLUMN total_items integer");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE $table SET total_cost=0.0");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE $table SET total_items=0");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE $table ALTER COLUMN total_items SET NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE $table ALTER COLUMN total_cost SET NOT NULL");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.86';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.86';

	function booking_upgrade0_1_86()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$table = "bb_billing_sequential_number_generator";

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_billing_sequential_number_generator', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'name' => array('type' => 'text', 'nullable' => False),
				'value' => array('type' => 'int', 'precision' => '4', 'nullable' => False, 'default' => 0),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('name')
			)
		);

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("INSERT INTO $table (name, value) VALUES('internal', 0)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("INSERT INTO $table (name, value) VALUES('external', 34500000)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.87';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.87';

	function booking_upgrade0_1_87()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$table = 'bb_completed_reservation';

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE $table ADD COLUMN export_file_id integer");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE $table ADD COLUMN invoice_file_order_id varchar(255)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE $table ADD CONSTRAINT {$table}_export_file_id_fkey FOREIGN KEY (export_file_id) REFERENCES bb_completed_reservation_export_file(id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.88';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.88';

	function booking_upgrade0_1_88()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN customer_internal INT NOT NULL DEFAULT 1");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN customer_internal INT NOT NULL DEFAULT 1");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.89';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.89';

	function booking_upgrade0_1_89()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ADD COLUMN sms_total INT");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN sms_total INT");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.90';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.90';

	function booking_upgrade0_1_90()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN is_public INT NOT NULL DEFAULT 1");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.91';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.91';

	function booking_upgrade0_1_91()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_account_code_set ADD COLUMN dim_4 varchar(8)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_account_code_set ADD COLUMN dim_value_4 varchar(12)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_account_code_set ADD COLUMN dim_value_5 varchar(12)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.92';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.92';

	function booking_upgrade0_1_92()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_event_comment', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'event_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'time' => array('type' => 'timestamp', 'nullable' => False),
				'author' => array('type' => 'text', 'nullable' => False),
				'comment' => array('type' => 'text', 'nullable' => False),
				'type' => array('type' => 'text', 'nullable' => False, 'default' => 'comment'),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_event' => array('event_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.93';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.93';

	function booking_upgrade0_1_93()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_event_date', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'event_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'from_' => array('type' => 'timestamp', 'nullable' => False),
				'to_' => array('type' => 'timestamp', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_event' => array('event_id' => 'id')),
			'ix' => array(),
			'uc' => array('event_id', 'from_', 'to_')
		));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.94';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.94';

	function booking_upgrade0_1_94()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$table = "bb_resource";

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE $table ADD COLUMN sort integer");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE $table SET sort = 0");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.95';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.95';

	function booking_upgrade0_1_95()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$table = "bb_organization";

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE $table ADD COLUMN shortname varchar(11)");

		$table = "bb_group";

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE $table ADD COLUMN shortname varchar(11)");


		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.96';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.96';

	function booking_upgrade0_1_96()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_system_message', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'title' => array('type' => 'text', 'nullable' => False),
				'created' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp'),
				'display_in_dashboard' => array('type' => 'int', 'nullable' => False, 'precision' => '4',
					'default' => 1),
				'building_id' => array('type' => 'int', 'precision' => '4'),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'phone' => array('type' => 'varchar', 'precision' => '50', 'nullable' => true),
				'email' => array('type' => 'varchar', 'precision' => '50', 'nullable' => true),
				'message' => array('type' => 'text', 'nullable' => False),
				'type' => array('type' => 'text', 'nullable' => False, 'default' => 'message'),
				'status' => array('type' => 'text', 'nullable' => False, 'default' => 'NEW'),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$table = "bb_application";

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE $table ADD COLUMN type varchar(11) NOT NULL DEFAULT 'application'");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE $table SET type = 'application'");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE $table SET status = 'ACCEPTED' WHERE status = 'CONFIRMED'");


		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.97';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.97';

	function booking_upgrade0_1_97()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN customer_organization_id integer");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN customer_organization_name varchar(50)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN building_name varchar(50) NOT NULL DEFAULT 'changeme'");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_event SET building_name = b2.name FROM bb_building b2 WHERE EXISTS (select 1 from bb_event e,bb_event_resource er,bb_resource r,bb_building b WHERE e.id=er.event_id AND er.resource_id=r.id AND r.building_id=b.id AND b2.id=b.id	AND bb_event.id=e.id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.98';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.98';

	function booking_upgrade0_1_98()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_booking ADD COLUMN building_name varchar(50) NOT NULL DEFAULT 'changeme'");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_booking SET building_name = b2.name FROM bb_building b2 WHERE EXISTS (SELECT 1 FROM bb_booking bo,bb_season s,bb_building b WHERE bo.season_id = s.id AND s.building_id = b.id AND b2.id=b.id AND bb_booking.id=bo.id)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_allocation ADD COLUMN building_name varchar(50) NOT NULL DEFAULT 'changeme'");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_allocation SET building_name = b2.name FROM bb_building b2 WHERE EXISTS (SELECT 1 FROM bb_allocation a,bb_season s,bb_building b WHERE s.id = a.season_id AND s.building_id = b.id AND b2.id=b.id AND bb_allocation.id=a.id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.99';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.1.99';

	function booking_upgrade0_1_99()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application ADD COLUMN building_name varchar(50) NOT NULL DEFAULT 'changeme'");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_application SET building_name = b2.name FROM bb_building b2 WHERE EXISTS (SELECT 1 FROM bb_building b, bb_application a, bb_application_resource ar,bb_resource r WHERE a.id = ar.application_id AND ar.resource_id = r.id AND r.building_id = b.id AND b2.id=b.id AND bb_application.id=a.id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.00';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.00';

	function booking_upgrade0_2_00()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_application SET building_name = b2.name FROM bb_building b2 WHERE EXISTS (SELECT 1 FROM bb_building b, bb_application a, bb_application_resource ar,bb_resource r WHERE a.id = ar.application_id AND ar.resource_id = r.id AND r.building_id = b.id AND b2.id=b.id AND bb_application.id=a.id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.01';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.01';

	function booking_upgrade0_2_01()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$table = "bb_building";

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE $table ADD COLUMN deactivate_calendar int NOT NULL DEFAULT 0");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE $table SET deactivate_calendar = 0");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE $table ADD COLUMN deactivate_application int NOT NULL DEFAULT 0");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE $table SET deactivate_application = 0");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.02';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.02';

	function booking_upgrade0_2_02()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$table = "bb_building";

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE $table ADD COLUMN deactivate_sendmessage int NOT NULL DEFAULT 0");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE $table SET deactivate_sendmessage = 0");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.03';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.03';

	/**
	 * Update booking version from 0.2.02 to 0.2.03
	 * Add custom fields to request
	 *
	 */
	function booking_upgrade0_2_03()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_completed_reservation', 'cost', array(
			'type' => 'decimal', 'precision' => 10, 'scale' => 2, 'nullable' => true, 'default' => '0.0'));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_wtemplate_alloc', 'cost', array(
			'type' => 'decimal', 'precision' => 10, 'scale' => 2, 'nullable' => true, 'default' => '0.0'));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_allocation', 'cost', array('type' => 'decimal',
			'precision' => 10, 'scale' => 2, 'nullable' => true, 'default' => '0.0'));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_booking', 'cost', array('type' => 'decimal',
			'precision' => 10, 'scale' => 2, 'nullable' => true, 'default' => '0.0'));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_event', 'cost', array('type' => 'decimal',
			'precision' => 10, 'scale' => 2, 'nullable' => true, 'default' => '0.0'));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.04';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.04';

	/**
	 * Update booking version from 0.2.03 to 0.2.04
	 * Add custom fields to request
	 *
	 */
	function booking_upgrade0_2_04()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_organization ADD COLUMN show_in_portal int NOT NULL DEFAULT 0");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_organization SET show_in_portal = 0");

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_group ADD COLUMN show_in_portal int NOT NULL DEFAULT 0");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_group SET show_in_portal = 0");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.05';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.05';

	/**
	 * Update booking version from 0.2.04 to 0.2.05
	 * Add custom fields to request
	 *
	 */
	function booking_upgrade0_2_05()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN id_string varchar(20) NOT NULL DEFAULT '0'");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_event SET id_string = cast(id AS varchar)");

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_allocation ADD COLUMN id_string varchar(20) NOT NULL DEFAULT '0'");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_allocation SET id_string = cast(id AS varchar)");

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application ADD COLUMN id_string varchar(20) NOT NULL DEFAULT '0'");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_application SET id_string = cast(id AS varchar)");

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_system_message ADD COLUMN building_name varchar(50) NOT NULL DEFAULT 'changeme'");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_system_message SET building_name = b2.name FROM bb_building b2 WHERE EXISTS (SELECT 1 FROM bb_building b, bb_system_message a WHERE a.building_id = b.id AND b2.id=b.id AND bb_system_message.id=a.id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.06';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.06';

	/**
	 * Update booking version from 0.2.06 to 0.2.07
	 * Add office and office/user relation (User is added as a custom value)
	 *
	 */
	function booking_upgrade0_2_06()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_office', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 200, 'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_office_user', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
				'office' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array('bb_office' => array('office' => 'id')),
			'ix' => array(),
			'uc' => array()
			)
		);

		$GLOBALS['phpgw']->locations->add('.office', 'office', 'booking');
		$GLOBALS['phpgw']->locations->add('.office.user', 'office/user relation', 'booking', false, 'bb_office_user');
		$GLOBALS['phpgw']->db = clone($GLOBALS['phpgw_setup']->oProc->m_odb);

		$attrib = array
			(
			'appname' => 'booking',
			'location' => '.office.user',
			'column_name' => 'account_id',
			'input_text' => 'User',
			'statustext' => 'System user',
			'search' => true,
			'list' => true,
			'column_info' => array
				(
				'type' => 'user',
				'nullable' => 'False',
				'custom' => 1
			)
		);

		$GLOBALS['phpgw']->custom_fields->add($attrib, 'bb_office_user');

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.07';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.07';

	/**
	 * Update booking version from 0.2.07 to 0.2.08
	 * Add custom fields to request
	 *
	 */
	function booking_upgrade0_2_07()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_documentation', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'category' => array('type' => 'varchar', 'precision' => '150', 'nullable' => false),
				'description' => array('type' => 'text', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.08';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.08';

	/**
	 * Update booking version from 0.2.08 to 0.2.09
	 * add log file name to completed_reservation_export_file
	 *
	 */
	function booking_upgrade0_2_08()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_completed_reservation_export_file ADD COLUMN log_filename text");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.09';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.09';

	/**
	 * Update booking version from 0.2.09 to 0.2.10
	 * add description to bb_office
	 *
	 */
	function booking_upgrade0_2_09()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_office', 'description', array(
			'type' => 'text',
			'nullable' => true
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.10';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.10';

	/**
	 * Update booking version from 0.2.10 to 0.2.11
	 * add description to bb_office
	 *
	 */
	function booking_upgrade0_2_10()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_application', 'building_name', array(
			'type' => 'varchar',
			'precision' => 100,
			'nullable' => False,
			'default' => 'changeme'
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_allocation', 'building_name', array(
			'type' => 'varchar',
			'precision' => 100,
			'nullable' => False,
			'default' => 'changeme'
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_booking', 'building_name', array(
			'type' => 'varchar',
			'precision' => 100,
			'nullable' => False,
			'default' => 'changeme'
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_event', 'building_name', array(
			'type' => 'varchar',
			'precision' => 100,
			'nullable' => False,
			'default' => 'changeme'
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_system_message', 'building_name', array(
			'type' => 'varchar',
			'precision' => 100,
			'nullable' => False,
			'default' => 'changeme'
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.11';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.11';

	/**
	 * Update booking version from 0.2.11 to 0.2.12
	 * alter lenght of name fields
	 *
	 */
	function booking_upgrade0_2_11()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_activity', 'name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => False
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_building', 'name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => False
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_contact_person', 'name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => False
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_organization', 'name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => False
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_resource', 'name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => False
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_group', 'name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => False
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_season', 'name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => False
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_organization_contact', 'name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => true
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_group_contact', 'name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_application', 'building_name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => False,
			'default' => 'changeme'
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_allocation', 'building_name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => False,
			'default' => 'changeme'
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_booking', 'building_name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => False,
			'default' => 'changeme'
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_event', 'building_name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => False,
			'default' => 'changeme'
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_event', 'contact_name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => false
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_event', 'customer_organization_name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_system_message', 'building_name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => false
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_system_message', 'name', array(
			'type' => 'varchar',
			'precision' => 150,
			'nullable' => false
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.12';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.12';

	/**
	 * Update booking version from 0.2.12 to 0.2.13
	 * add description to bb_office
	 *
	 */
	function booking_upgrade0_2_12()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN tilsyn_name varchar(50)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN tilsyn_email varchar(50)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN tilsyn_phone varchar(50)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.13';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.13';

	/**
	 * Update booking version from 0.2.13 to 0.2.14
	 *
	 *
	 */
	function booking_upgrade0_2_13()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query(
			"CREATE OR REPLACE VIEW bb_application_association AS " .
			"SELECT 'booking' AS type, application_id, id, from_, to_, active FROM bb_booking WHERE application_id IS NOT NULL " .
			"UNION " .
			"SELECT 'allocation' AS type, application_id, id, from_, to_, active FROM bb_allocation  WHERE application_id IS NOT NULL " .
			"UNION " .
			"SELECT 'event' AS type, application_id, id, from_, to_, active FROM bb_event  WHERE application_id IS NOT NULL"
		);
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.14';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.14';

	/**
	 * Update booking version from 0.2.14 to 0.2.15
	 * add description to bb_office
	 *
	 */
	function booking_upgrade0_2_14()
	{
		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('bb_building');
		if(isset($metadata['calendar_text']))
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.15';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN calendar_text varchar(50)");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.15';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.15';

	/**
	 * Update booking version from 0.2.15 to 0.2.16
	 * add another tilsynsvakt to buidling
	 *
	 */
	function booking_upgrade0_2_15()
	{
		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('bb_building');
		if(isset($metadata['tilsyn_name2']))
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.16';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN tilsyn_name2 varchar(50)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN tilsyn_email2 varchar(50)");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN tilsyn_phone2 varchar(50)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.16';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.16';

	/**
	 * Update booking version from 0.2.16 to 0.2.17
	 * add another tilsynsvakt to buidling
	 *
	 */
	function booking_upgrade0_2_16()
	{
		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('bb_building');
		if(isset($metadata['extra_kalendar']))
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.17';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN  extra_kalendar int NOT NULL DEFAULT 0");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_building SET extra_kalendar = 0");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.17';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.17';

	/**
	 * Update booking version from 0.2.17 to 0.2.18
	 * add another tilsynsvakt to buidling
	 *
	 */
	function booking_upgrade0_2_17()
	{
		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('bb_application');
		if(isset($metadata['equipment']))
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.18';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_application ADD COLUMN equipment text DEFAULT NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_application SET equipment = NULL");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.18';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.18';

	/**
	 * Update booking version from 0.2.18 to 0.2.19
	 * add another tilsynsvakt to buidling
	 *
	 */
	function booking_upgrade0_2_18()
	{
		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('bb_event');
		if(isset($metadata['building_id']))
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.19';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_event ADD COLUMN building_id int DEFAULT NULL");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("UPDATE bb_event SET building_id = br2.building_id FROM bb_resource br2 WHERE EXISTS (SELECT 1 FROM bb_event be, bb_event_resource ber, bb_resource br WHERE be.id = ber.event_id AND ber.resource_id = br.id AND br2.id = br.id AND bb_event.id=be.id )");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.19';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.19';

	/**
	 * Update booking version from 0.2.19 to 0.2.20
	 *
	 */
	function booking_upgrade0_2_19()
	{
		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('bb_resource');
		if(isset($metadata['organizations_ids']))
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.20';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ADD COLUMN organizations_ids varchar(50) DEFAULT NULL");
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.20';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.20';

	/**
	 * Update booking version from 0.2.20 to 0.2.21
	 *
	 */
	function booking_upgrade0_2_20()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
//- vi registrerer en systemlokasjon pr toppnivå av aktivitet som "resource.<activity_id>"
//- vi registrerer en systemlokasjon pr toppnivå av aktivitet som "application.<activity_id>"
		$boactivity = CreateObject('booking.boactivity');
		$activities = $boactivity->fetch_activities();
		$activities = $boactivity->so->read(array('sort' => 'name', 'dir' => 'ASC'));

		$top_level = array();
		foreach ($activities['results'] as $activity)
		{
			if (!$activity['parent_id'])
			{
				$top_level[] = $activity;
			}
		}
		unset($activity);

		foreach ($top_level as $activity)
		{
			$location = ".application.{$activity['id']}";
			$descr = $activity['name'];

			$GLOBALS['phpgw']->locations->add(
				$location, $descr, $appname = 'booking', false, //$allow_grant
	null, //$custom_tbl
	false, //$c_function
	true//$c_attrib
			);

			$location = ".resource.{$activity['id']}";
			$descr = $activity['name'];

			$GLOBALS['phpgw']->locations->add(
				$location, $descr, $appname = 'booking', false, //$allow_grant
	null, //$custom_tbl
	false, //$c_function
	true//$c_attrib
			);
		}

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.21';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.21';

	/**
	 * Update booking version from 0.2.21 to 0.2.22
	 *
	 */
	function booking_upgrade0_2_21()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_agegroup', 'activity_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_targetaudience', 'activity_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => true));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_agegroup', 'description', array(
			'type' => 'text', 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_targetaudience', 'description', array(
			'type' => 'text', 'nullable' => true));


		$GLOBALS['phpgw_setup']->oProc->query("SELECT id FROM bb_activity WHERE parent_id IS NULL");

		$activities = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$activities[] = $GLOBALS['phpgw_setup']->oProc->f('id');
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM bb_agegroup ORDER BY id");

		$agegroups = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$agegroups[] = array(
				'old_id' => $GLOBALS['phpgw_setup']->oProc->f('id'),
				'name' => $GLOBALS['phpgw_setup']->oProc->f('name'),
				'description' => $GLOBALS['phpgw_setup']->oProc->f('description'),
				'active' => $GLOBALS['phpgw_setup']->oProc->f('active'),
				'sort' => $GLOBALS['phpgw_setup']->oProc->f('sort')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM bb_targetaudience ORDER BY id");

		$targets = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$targets[] = array(
				'old_id' => $GLOBALS['phpgw_setup']->oProc->f('id'),
				'name' => $GLOBALS['phpgw_setup']->oProc->f('name'),
				'description' => $GLOBALS['phpgw_setup']->oProc->f('description'),
				'active' => $GLOBALS['phpgw_setup']->oProc->f('active'),
				'sort' => $GLOBALS['phpgw_setup']->oProc->f('sort')
			);
		}

		$first_run = true;
		foreach ($activities as $activity_id)
		{
			$GLOBALS['phpgw_setup']->oProc->query("SELECT id FROM bb_activity WHERE parent_id = $activity_id");

			$sub_activities = array($activity_id);
			while ($GLOBALS['phpgw_setup']->oProc->next_record())
			{
				$sub_activities[] = $GLOBALS['phpgw_setup']->oProc->f('id');
			}

			if ($first_run)
			{
				$GLOBALS['phpgw_setup']->oProc->query("UPDATE bb_agegroup SET activity_id = {$activity_id}");
				$GLOBALS['phpgw_setup']->oProc->query("UPDATE bb_targetaudience SET activity_id = {$activity_id}");
				$first_run = false;
			}
			else
			{
				foreach ($agegroups as &$agegroup)
				{
					$old_id = $agegroup['old_id'];
					$insert_values = $agegroup;
					unset($insert_values['old_id']);
					$insert_values['activity_id'] = $activity_id;
					$cols = implode(',', array_keys($insert_values));
					$values = $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($insert_values));
					$sql = "INSERT INTO bb_agegroup ({$cols}) VALUES ({$values})";
					$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
					$new_id = $GLOBALS['phpgw_setup']->oProc->get_last_insert_id('bb_agegroup');
//bb_application_agegroup
					$sql = "SELECT id FROM bb_application WHERE activity_id IN (" . implode(',', $sub_activities) . ')';
					$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
					$applications = array();
					while ($GLOBALS['phpgw_setup']->oProc->next_record())
					{
						$applications[] = $GLOBALS['phpgw_setup']->oProc->f('id');
					}

					if ($applications)
					{
						$sql = "UPDATE bb_application_agegroup SET agegroup_id = $new_id WHERE agegroup_id = $old_id AND application_id IN (" . implode(',', $applications) . ')';
						$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
					}
//bb_booking_agegroup
					$sql = "SELECT id FROM bb_booking WHERE activity_id IN (" . implode(',', $sub_activities) . ')';
					$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
					$bookings = array();
					while ($GLOBALS['phpgw_setup']->oProc->next_record())
					{
						$bookings[] = $GLOBALS['phpgw_setup']->oProc->f('id');
					}

					if ($bookings)
					{
						$sql = "UPDATE bb_booking_agegroup SET agegroup_id = $new_id WHERE agegroup_id = $old_id AND booking_id IN (" . implode(',', $bookings) . ')';
						$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
					}
//bb_event_agegroup
					$sql = "SELECT id FROM bb_event WHERE activity_id IN (" . implode(',', $sub_activities) . ')';
					$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
					$events = array();
					while ($GLOBALS['phpgw_setup']->oProc->next_record())
					{
						$events[] = $GLOBALS['phpgw_setup']->oProc->f('id');
					}

					if ($events)
					{
						$sql = "UPDATE bb_event_agegroup SET agegroup_id = $new_id WHERE agegroup_id = $old_id AND event_id IN (" . implode(',', $events) . ')';
						$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
					}
				}
				foreach ($targets as &$target)
				{
					$old_id = $target['old_id'];
					$insert_values = $target;
					unset($insert_values['old_id']);
					$insert_values['activity_id'] = $activity_id;
					$cols = implode(',', array_keys($insert_values));
					$values = $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($insert_values));
					$sql = "INSERT INTO bb_targetaudience ({$cols}) VALUES ({$values})";
					$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
					$new_id = $GLOBALS['phpgw_setup']->oProc->get_last_insert_id('bb_targetaudience');
//bb_application_targetaudience
					$sql = "SELECT id FROM bb_application WHERE activity_id IN (" . implode(',', $sub_activities) . ')';
					$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
					$applications = array();
					while ($GLOBALS['phpgw_setup']->oProc->next_record())
					{
						$applications[] = $GLOBALS['phpgw_setup']->oProc->f('id');
					}

					if ($applications)
					{
						$sql = "UPDATE bb_application_targetaudience SET targetaudience_id = $new_id WHERE targetaudience_id = $old_id AND application_id IN (" . implode(',', $applications) . ')';
						$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
					}
//bb_booking_targetaudience
					$sql = "SELECT id FROM bb_booking WHERE activity_id IN (" . implode(',', $sub_activities) . ')';
					$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
					$bookings = array();
					while ($GLOBALS['phpgw_setup']->oProc->next_record())
					{
						$bookings[] = $GLOBALS['phpgw_setup']->oProc->f('id');
					}

					if ($bookings)
					{
						$sql = "UPDATE bb_booking_targetaudience SET targetaudience_id = $new_id WHERE targetaudience_id = $old_id AND booking_id IN (" . implode(',', $bookings) . ')';
						$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
					}
//bb_event_targetaudience
					$sql = "SELECT id FROM bb_event WHERE activity_id IN (" . implode(',', $sub_activities) . ')';
					$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
					$events = array();
					while ($GLOBALS['phpgw_setup']->oProc->next_record())
					{
						$events[] = $GLOBALS['phpgw_setup']->oProc->f('id');
					}
					if ($events)
					{
						$sql = "UPDATE bb_event_targetaudience SET targetaudience_id = $new_id WHERE targetaudience_id = $old_id AND event_id IN (" . implode(',', $events) . ')';
						$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
					}
				}
			}
		}

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_booking_agegroup', 'male_actual', array(
			'type' => 'int', 'precision' => 4, 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_booking_agegroup', 'female_actual', array(
			'type' => 'int', 'precision' => 4, 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_event_agegroup', 'male_actual', array(
			'type' => 'int', 'precision' => 4, 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_event_agegroup', 'female_actual', array(
			'type' => 'int', 'precision' => 4, 'nullable' => true));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_agegroup', 'activity_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => false));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_targetaudience', 'activity_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => false));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.22';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.22';

	/**
	 * Update booking version from 0.2.22 to 0.2.23
	 *
	 */
	function booking_upgrade0_2_22()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_allocation_cost', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'allocation_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'time' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp'),
				'author' => array('type' => 'text', 'nullable' => False),
				'comment' => array('type' => 'text', 'nullable' => False),
				'cost' => array('type' => 'decimal', 'precision' => 10, 'scale' => 2, 'nullable' => True,
					'default' => '0.0'),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_allocation' => array('allocation_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_event_cost', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'event_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'time' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp'),
				'author' => array('type' => 'text', 'nullable' => False),
				'comment' => array('type' => 'text', 'nullable' => False),
				'cost' => array('type' => 'decimal', 'precision' => 10, 'scale' => 2, 'nullable' => True,
					'default' => '0.0'),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_event' => array('event_id' => 'id')),
			'ix' => array(),
			'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_booking_cost', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'booking_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'time' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp'),
				'author' => array('type' => 'text', 'nullable' => False),
				'comment' => array('type' => 'text', 'nullable' => False),
				'cost' => array('type' => 'decimal', 'precision' => 10, 'scale' => 2, 'nullable' => True,
					'default' => '0.0'),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_booking' => array('booking_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.23';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.23';

	/**
	 * Update booking version from 0.2.23 to 0.2.24
	 *
	 */
	function booking_upgrade0_2_23()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'json_representation', array(
			'type' => 'jsonb',
			'nullable' => true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query('CREATE OPERATOR ~@ (LEFTARG = jsonb, RIGHTARG = text, PROCEDURE = jsonb_exists)', __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->query('CREATE OPERATOR ~@| (LEFTARG = jsonb, RIGHTARG = text[], PROCEDURE = jsonb_exists_any)', __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->query('CREATE OPERATOR ~@& (LEFTARG = jsonb, RIGHTARG = text[], PROCEDURE = jsonb_exists_all)', __LINE__, __FILE__);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.24';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.24';

	/**
	 * Update booking version from 0.2.24 to 0.2.25
	 *
	 */
	function booking_upgrade0_2_24()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_document_application', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'category' => array('type' => 'varchar', 'precision' => '150', 'nullable' => false),
				'description' => array('type' => 'text', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(
				"bb_application" => array('owner_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array()
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.25';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.25';

	/**
	 * Update booking version from 0.2.25 to 0.2.26
	 *
	 */
	function booking_upgrade0_2_25()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_building', 'activity_id', array(
			'type' => 'int', 'precision' => 4, 'nullable' => true));
		$soactivity = createObject('booking.soactivity');

		$sql = "SELECT id FROM bb_activity WHERE parent_id = 0 OR parent_id IS NULL ORDER BY id";

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$top_levels = array();

		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$top_levels[] = $GLOBALS['phpgw_setup']->oProc->f('id');
		}

		$activities = array_merge(array($top_levels[0]), $soactivity->get_children($top_levels[0]));

		if ($activities)
		{
			$sql = "SELECT building_id FROM bb_resource WHERE activity_id IN (" . implode(',', $activities) . ')';
			$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
			$buildings = array();
			while ($GLOBALS['phpgw_setup']->oProc->next_record())
			{
				$buildings[] = $GLOBALS['phpgw_setup']->oProc->f('building_id');
			}

			if ($buildings)
			{
				$sql = "UPDATE bb_building SET activity_id = 1 WHERE id IN (" . implode(',', $buildings) . ')';
				$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
				if (isset($top_levels[1]))
				{
					$sql = "UPDATE bb_building SET activity_id = {$top_levels[1]} WHERE activity_id IS NULL";
					$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
				}

				$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_building', 'activity_id', array(
					'type' => 'int',
					'precision' => 4,
					'nullable' => false
					)
				);
			}
		}

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.26';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	$test[] = '0.2.26';

	/**
	 * Update booking version from 0.2.26 to 0.2.27
	 *
	 */
	function booking_upgrade0_2_26()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_building_resource', array(
			'fd' => array(
				'building_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'resource_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('building_id', 'resource_id'),
			'fk' => array(
				'bb_building' => array('building_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query('SELECT id, building_id FROM bb_resource', __LINE__, __FILE__);

		// using stored prosedures
		$sql = 'INSERT INTO bb_building_resource (building_id, resource_id)'
			. ' VALUES(?, ?)';
		$valueset = array();

		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$valueset[] = array
				(
				1 => array
					(
					'value' => $GLOBALS['phpgw_setup']->oProc->f('building_id'),
					'type' => 1 //PDO::PARAM_INT
				),
				2 => array
					(
					'value' => $GLOBALS['phpgw_setup']->oProc->f('id'),
					'type' => 1 //PDO::PARAM_INT
				)
			);
		}

		if ($valueset)
		{
			$GLOBALS['phpgw_setup']->oProc->m_odb->insert($sql, $valueset, __LINE__, __FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->DropColumn('bb_resource', array(), 'building_id');

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.27';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}


	/**
	 * Update booking version from 0.2.27 to 0.2.28
	 *
	 */
	$test[] = '0.2.27';
	function booking_upgrade0_2_27()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_delegate', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'active' => array('type' => 'int', 'nullable' => False, 'precision' => 2, 'default' => 1),
					'organization_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => '150', 'nullable' => False),
					'email' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
					'ssn' => array('type' => 'varchar', 'precision' => '115', 'nullable' => True),
					'phone' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
			),
				'pk' => array('id'),
				'fk' => array('bb_organization' => array('organization_id' => 'id')),
				'ix' => array(),
				'uc' => array('organization_id', 'ssn')
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.28';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.28 to 0.2.29
	 *
	 */
	$test[] = '0.2.28';
	function booking_upgrade0_2_28()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_document_organization', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
					'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'category' => array('type' => 'varchar', 'precision' => '150', 'nullable' => false),
					'description' => array('type' => 'text', 'nullable' => true),
				),
				'pk' => array('id'),
				'fk' => array(
					"bb_organization" => array('owner_id' => 'id'),
				),
				'ix' => array(),
				'uc' => array()
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.29';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.29 to 0.2.30
	 *
	 */
	$test[] = '0.2.29';
	function booking_upgrade0_2_29()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.admin', 'Admin section', 'booking');

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.30';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.30 to 0.2.31
	 *
	 */
	$test[] = '0.2.30';
	function booking_upgrade0_2_30()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_application', 'responsible_street',
			array('type' => 'varchar', 'precision' => '255', 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_application', 'responsible_zip_code',
			array('type' => 'varchar', 'precision' => '16', 'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_application', 'responsible_city',
			array('type' => 'varchar', 'precision' => '255', 'nullable' => True));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.31';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.31 to 0.2.32
	 *
	 */
	$test[] = '0.2.31';
	function booking_upgrade0_2_31()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_rescategory', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'name' => array('type' => 'varchar', 'precision' => '100', 'nullable' => false),
					'active' => array('type' => 'int', 'nullable' => false, 'precision' => '4', 'default' => 1),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array(),
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_rescategory_activity', array(
				'fd' => array(
					'rescategory_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
					'activity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				),
				'pk' => array('rescategory_id', 'activity_id'),
				'fk' => array(
					'bb_rescategory' => array('rescategory_id' => 'id'),
					'bb_activity' => array('activity_id' => 'id')
				),
				'ix' => array(),
				'uc' => array(),
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.32';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.32 to 0.2.33
	 *
	 */
	$test[] = '0.2.32';
	function booking_upgrade0_2_32()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ADD COLUMN rescategory_id int");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ADD CONSTRAINT bb_resource_rescategory_id_fkey FOREIGN KEY (rescategory_id) REFERENCES bb_rescategory(id)");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.33';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.33 to 0.2.34
	 *
	 */
	$test[] = '0.2.33';
	function booking_upgrade0_2_33()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_resource_activity', array(
				'fd' => array(
					'resource_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
					'activity_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				),
				'pk' => array('resource_id', 'activity_id'),
				'fk' => array(
					'bb_resource' => array('resource_id' => 'id'),
					'bb_activity' => array('activity_id' => 'id')
				),
				'ix' => array(),
				'uc' => array(),
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.34';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.34 to 0.2.35
	 *
	 */
	$test[] = '0.2.34';
	function booking_upgrade0_2_34()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_facility', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'name' => array('type' => 'varchar', 'precision' => '100', 'nullable' => false),
					'active' => array('type' => 'int', 'nullable' => false, 'precision' => '4', 'default' => 1),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array(),
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.35';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.35 to 0.2.36
	 *
	 */
	$test[] = '0.2.35';
	function booking_upgrade0_2_35()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_resource_facility', array(
				'fd' => array(
					'resource_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
					'facility_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				),
				'pk' => array('resource_id', 'facility_id'),
				'fk' => array(
					'bb_resource' => array('resource_id' => 'id'),
					'bb_facility' => array('facility_id' => 'id')
				),
				'ix' => array(),
				'uc' => array(),
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.36';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.36 to 0.2.37
	 *
	 */
	$test[] = '0.2.36';
	function booking_upgrade0_2_36()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_building ADD COLUMN opening_hours text");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ADD COLUMN opening_hours text");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_resource ADD COLUMN contact_info text");

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.37';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.37 to 0.2.38
	 *
	 */
	$test[] = '0.2.37';
	function booking_upgrade0_2_37()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_application', 'session_id',
			array('type' => 'varchar', 'precision' => '64', 'nullable' => True));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.38';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.38 to 0.2.39
	 *
	 */
	$test[] = '0.2.38';
	function booking_upgrade0_2_38()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_event', 'include_in_list',
			array('type' => 'int', 'precision' => '4', 'nullable' => False, 'default' => '0'));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.39';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.39 to 0.2.40
	 *
	 */
	$test[] = '0.2.39';
	function booking_upgrade0_2_39()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_application', 'name',
				array('type' => 'varchar', 'precision' => '255', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_application', 'organizer',
				array('type' => 'varchar', 'precision' => '255', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_event', 'name',
				array('type' => 'varchar', 'precision' => '255', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_event', 'organizer',
				array('type' => 'varchar', 'precision' => '255', 'nullable' => True));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.40';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.40 to 0.2.41
	 *
	 */
	$test[] = '0.2.40';
	function booking_upgrade0_2_40()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_application', 'homepage',
				array('type' => 'varchar', 'precision' => '255', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_event', 'homepage',
				array('type' => 'varchar', 'precision' => '255', 'nullable' => True));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.41';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.41 to 0.2.42
	 *
	 */
	$test[] = '0.2.41';
	function booking_upgrade0_2_41()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_event', 'equipment',
				array('type' => 'text', 'nullable' => True));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.42';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.42 to 0.2.43
	 *
	 */
	$test[] = '0.2.42';
	function booking_upgrade0_2_42()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_application', 'description', array(
			'type' => 'text', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_event', 'description', array(
			'type' => 'text', 'nullable' => True));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.43';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.43 to 0.2.44
	 *
	 */
	$test[] = '0.2.43';
	function booking_upgrade0_2_43()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

			$GLOBALS['phpgw_setup']->oProc->CreateTable(
				'bb_user',  array(
					'fd' => array(
						'id' => array('type' => 'auto', 'nullable' => false),
						'active' => array('type' => 'int', 'nullable' => False, 'precision' => '4', 'default' => 1),
						'name' => array('type' => 'varchar', 'precision' => '150', 'nullable' => False),
						'homepage' => array('type' => 'text', 'nullable' => True),
						'phone' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
						'email' => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
						'street' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
						'zip_code' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
						'city' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
						'customer_number' => array('type' => 'text', 'nullable' => True),
						'customer_ssn' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True)
					),
					'pk' => array('id'),
					'fk' => array(),
					'ix' => array(),
					'uc' => array()
				)
			);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.44';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.44 to 0.2.45
	 *
	 */
	$test[] = '0.2.44';
	function booking_upgrade0_2_44()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'direct_booking',
				array('type' => 'int', 'nullable' => true, 'precision' => 8));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.45';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.45 to 0.2.46
	 *
	 */
	$test[] = '0.2.45';
	function booking_upgrade0_2_45()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_group', 'parent_id',
			array('type' => 'int', 'precision' => 4, 'nullable' => true));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.46';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.46 to 0.2.47
	 *
	 */
	$test[] = '0.2.46';
	function booking_upgrade0_2_46()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'e_lock_system_id',
				array('type' => 'int', 'precision' => 4, 'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'e_lock_resource_id',
				array('type' => 'int', 'precision' => 4, 'nullable' => true));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.47';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	/**
	 * Update booking version from 0.2.47 to 0.2.48
	 *
	 */
	$test[] = '0.2.47';
	function booking_upgrade0_2_47()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_event', 'access_requested',
				array(
					'type' => 'int',
					'precision' => 4,
					'nullable' => False,
					'default' => '0'
				)
			);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.48';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.48 to 0.2.49
	 *
	 */
	$test[] = '0.2.48';
	function booking_upgrade0_2_48()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

			$GLOBALS['phpgw_setup']->oProc->CreateTable(
				'bb_resource_e_lock',  array(
					'fd' => array(
						'resource_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
						'e_lock_system_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
						'e_lock_resource_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
						'e_lock_name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => true),
						'access_code_format' => array('type' => 'varchar', 'precision' => 20, 'nullable' => true),
						'active' => array('type' => 'int', 'nullable' => False, 'precision' => 2, 'default' => 1),
						'modified_on' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp'),
						'modified_by' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
					),
					'pk' => array('resource_id', 'e_lock_system_id', 'e_lock_resource_id'),
					'fk' => array(
						'bb_resource' => array('resource_id' => 'id'),
					),
					'ix' => array(),
					'uc' => array(),
				)
			);


		$GLOBALS['phpgw_setup']->oProc->query('SELECT id, e_lock_system_id, e_lock_resource_id FROM bb_resource WHERE e_lock_system_id IS NOT NULL', __LINE__, __FILE__);

		// using stored prosedures
		$sql = 'INSERT INTO bb_resource_e_lock (resource_id, e_lock_system_id, e_lock_resource_id, modified_by )'
			. ' VALUES(?, ?, ?, ?)';
		$valueset = array();

		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$valueset[] = array
				(
				1 => array
					(
					'value' => $GLOBALS['phpgw_setup']->oProc->f('id'),
					'type' => 1 //PDO::PARAM_INT
				),
				2 => array
					(
					'value' => $GLOBALS['phpgw_setup']->oProc->f('e_lock_system_id'),
					'type' => 1 //PDO::PARAM_INT
				),
				3 => array
					(
					'value' => $GLOBALS['phpgw_setup']->oProc->f('e_lock_resource_id'),
					'type' => 1 //PDO::PARAM_INT
				),
				4 => array
					(
					'value' => (int)$GLOBALS['phpgw_info']['user']['account_id'],
					'type' => 1 //PDO::PARAM_INT
				)
			);
		}

		if ($valueset)
		{
			$GLOBALS['phpgw_setup']->oProc->m_odb->insert($sql, $valueset, __LINE__, __FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->DropColumn('bb_resource', array(), 'e_lock_system_id');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('bb_resource', array(), 'e_lock_resource_id');

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'booking_day_default_lenght',
			array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => True
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'booking_dow_default_start',
			array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => True
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'booking_dow_default_end',
			array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => True
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'booking_time_default_start',
			array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => True
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'booking_time_default_end',
			array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => True
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.49';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.49 to 0.2.50
	 *
	 */
	$test[] = '0.2.49';
	function booking_upgrade0_2_49()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'simple_booking',
				array(
					'type' => 'int',
					'precision' => 2,
					'nullable' => true
				)
			);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_resource', 'booking_day_default_lenght',
			array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => True,
				'default' => -1
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_resource', 'booking_dow_default_start',
			array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => True,
				'default' => -1
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_resource', 'booking_dow_default_end',
			array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => True,
				'default' => -1
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_resource', 'booking_time_default_start',
			array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => True,
				'default' => -1
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_resource', 'booking_time_default_end',
			array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => True,
				'default' => -1
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query('UPDATE bb_resource SET'
			. ' booking_day_default_lenght = -1,'
			. ' booking_dow_default_start = -1,'
			. ' booking_dow_default_end = -1,'
			. ' booking_time_default_start = -1,'
			. ' booking_time_default_end = -1'
			. ' WHERE booking_time_default_start IS NULL', __LINE__, __FILE__);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.50';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.50 to 0.2.51
	 *
	 */
	$test[] = '0.2.50';
	function booking_upgrade0_2_50()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'direct_booking_season_id',
				array(
					'type' => 'int',
					'precision' => 4,
					'nullable' => true
				)
			);


		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.51';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.51 to 0.2.52
	 *
	 */
	$test[] = '0.2.51';

	function booking_upgrade0_2_51()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("DROP VIEW bb_application_association");

		$GLOBALS['phpgw_setup']->oProc->query(
			"CREATE OR REPLACE VIEW bb_application_association AS " .
			"SELECT 'booking' AS type, application_id, id, from_, to_, cost, active FROM bb_booking WHERE application_id IS NOT NULL " .
			"UNION " .
			"SELECT 'allocation' AS type, application_id, id, from_, to_, cost, active FROM bb_allocation  WHERE application_id IS NOT NULL " .
			"UNION " .
			"SELECT 'event' AS type, application_id, id, from_, to_, cost, active FROM bb_event  WHERE application_id IS NOT NULL"
		);

		$GLOBALS['phpgw_setup']->oProc->DropColumn('bb_resource', array(), 'booking_dow_default_end');

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.52';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.52 to 0.2.53
	 *
	 */
	$test[] = '0.2.52';

	function booking_upgrade0_2_52()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'simple_booking_start_date',
				array(
					'type' => 'int',
					'precision' => 8,
					'nullable' => true
				)
			);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'booking_month_horizon',
				array(
					'type' => 'int',
					'precision' => 4,
					'nullable' => true
				)
			);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.53';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	/**
	 * Update booking version from 0.2.53 to 0.2.54
	 *
	 */
	$test[] = '0.2.53';

	function booking_upgrade0_2_53()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'simple_booking_end_date',
				array(
					'type' => 'int',
					'precision' => 8,
					'nullable' => true
				)
			);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_resource', 'booking_day_horizon',
				array(
					'type' => 'int',
					'precision' => 4,
					'nullable' => true
				)
			);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.54';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.54 to 0.2.55
	 *
	 */
	$test[] = '0.2.54';

	function booking_upgrade0_2_54()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_participant_log', array(
			'fd' => array(
				'id'				 => array('type' => 'auto', 'nullable' => false),
				'reservation_type'	 => array('type' => 'varchar', 'precision' => '70', 'nullable' => False),
				'reservation_id'	 => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'from_'				 => array('type' => 'timestamp', 'nullable' => true),
				'to_'				 => array('type' => 'timestamp', 'nullable' => true),
				'phone'				 => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
				'email'				 => array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.55';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}


	/**
	 * Update booking version from 0.2.55 to 0.2.56
	 *
	 */
	$test[] = '0.2.55';

	function booking_upgrade0_2_55()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_participant_log', 'quantity',
				array(
					'type' => 'int',
					'precision' => 4,
					'default' => 1,
					'nullable' => false
				)
			);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.56';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}


	/**
	 * Update booking version from 0.2.56 to 0.2.57
	 *
	 */
	$test[] = '0.2.56';

	function booking_upgrade0_2_56()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameTable('bb_participant_log', 'bb_participant');

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.57';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}


	/**
	 * Update booking version from 0.2.57 to 0.2.58
	 *
	 */
	$test[] = '0.2.57';

	function booking_upgrade0_2_57()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_participant', 'name',
				array(
					'type' => 'varchar',
					'precision' => 150,
					'nullable' => true
				)
			);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.58';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	/**
	 * Update booking version from 0.2.58 to 0.2.59
	 *
	 */
	$test[] = '0.2.58';

	function booking_upgrade0_2_58()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_event', 'participant_limit',
				array(
					'type' => 'int',
					'precision' => 4,
					'nullable' => true
				)
			);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.59';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.59 to 0.2.60
	 *
	 */
	$test[] = '0.2.59';
	function booking_upgrade0_2_59()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_application', 'agreement_requirements',
				array(
					'type' => 'text',
					'nullable' => true
				)
			);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.60';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.60 to 0.2.61
	 *
	 */
	$test[] = '0.2.60';
	function booking_upgrade0_2_60()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$custom_config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('booking', 'run'));

		$receipt_section_common = $custom_config->add_section(array
			(
				'name' => 'common_archive',
				'descr' => 'common archive config'
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_common['section_id'],
				'input_type'	=> 'listbox',
				'name'			=> 'method',
				'descr'			=> 'Export / import method',
				'choice'		=> array('public360'),
			//	'value'			=> '',
			)
		);

		$receipt_section_public360 = $custom_config->add_section(array
			(
				'name' => 'public360',
				'descr' => 'public360 archive config'
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_public360['section_id'],
				'input_type'	=> 'password',
				'name'			=> 'authkey',
				'descr'			=> 'authkey',
				'value'			=> '',
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_public360['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'webservicehost',
				'descr'			=> 'webservicehost',
				'value'			=> '',
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_public360['section_id'],
				'input_type'	=> 'listbox',
				'name'			=> 'debug',
				'descr'			=> 'debug',
				'choice'		=> array(1),
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_application', 'external_archive_key',
				array(
					'type' => 'varchar',
					'precision' => '64',
					'nullable' => True
				)
			);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.61';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.61 to 0.2.62
	 *
	 */
	$test[] = '0.2.61';
	function booking_upgrade0_2_61()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'bb_participant_limit',  array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'resource_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'from_' => array('type' => 'timestamp', 'nullable' => false),
				'quantity' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'modified_on' => array('type' => 'timestamp', 'nullable' => False, 'default' => 'current_timestamp'),
				'modified_by' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				),
			'pk' => array('id'),
			'fk' => array('bb_resource' => array('resource_id' => 'id')),
			'ix' => array(),
			'uc' => array()
			)
		);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.62';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
	/**
	 * Update booking version from 0.2.62 to 0.2.63
	 *
	 */
	$test[] = '0.2.62';
	function booking_upgrade0_2_62()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_account_code_set', 'dim_6',
				array(
					'type' => 'varchar',
					'precision' => '8',
					'nullable' => True
				)
			);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_account_code_set', 'dim_7',
				array(
					'type' => 'varchar',
					'precision' => '8',
					'nullable' => True
				)
			);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_account_code_set', 'dim_value_2',
				array(
					'type' => 'varchar',
					'precision' => '12',
					'nullable' => True
				)
			);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_account_code_set', 'dim_value_3',
				array(
					'type' => 'varchar',
					'precision' => '12',
					'nullable' => True
				)
			);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_account_code_set', 'dim_value_6',
				array(
					'type' => 'varchar',
					'precision' => '12',
					'nullable' => True
				)
			);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_account_code_set', 'dim_value_7',
				array(
					'type' => 'varchar',
					'precision' => '12',
					'nullable' => True
				)
			);

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.63';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}

	/**
	 * Update booking version from 0.2.62 to 0.2.63
	 *
	 */
	$test[] = '0.2.63';
	function booking_upgrade0_2_63()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_account_code_set', 'object_number', array('type' => 'varchar', 'precision' => '8', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('bb_account_code_set', 'responsible_code', array('type' => 'varchar', 'precision' => '6', 'nullable' => True));

		if ($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.2.64';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}


