<?php

	
	$test[] = '0.1.39';
	function booking_upgrade0_1_39()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
	
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_contact_person ALTER COLUMN ssn TYPE varchar(12) USING NULL");
		
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
		
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
		
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
		
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
		
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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

		foreach($documentOwners as $owner)
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

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'homepage' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'phone' => array('type' => 'varchar','precision' => '50','nullable' => False, 'default'=>''),
					'email' => array('type' => 'varchar','precision' => '50','nullable' => False, 'default'=>''),
					'description' => array('type' => 'varchar','precision' => '1000','nullable' => False, 'default'=>''),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array(),
			)
		);
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_group ADD COLUMN contact_primary int");
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE bb_group ADD COLUMN contact_secondary int");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'booking_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'targetaudience_id' => array('type' => 'int','precision' => '4','nullable' => False)
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
					'booking_id' => array('type' => 'int','precision' => '4', 'nullable' => False),
					'agegroup_id' => array('type' => 'int','precision' => '4', 'nullable' => False),
					'male' => array('type' => 'int','precision' => '4', 'nullable' => False),
					'female' => array('type' => 'int','precision' => '4', 'nullable' => False),
				),
				'pk' => array('booking_id', 'agegroup_id'),
				'fk' => array(
					'bb_booking' => array('booking_id' => 'id'),
					'bb_agegroup' => array('agegroup_id' => 'id')),
				'ix' => array(),
				'uc' => array()
		));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'application_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'targetaudience_id' => array('type' => 'int','precision' => '4','nullable' => False)
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
					'application_id' => array('type' => 'int','precision' => '4', 'nullable' => False),
					'agegroup_id' => array('type' => 'int','precision' => '4', 'nullable' => False),
					'male' => array('type' => 'int','precision' => '4', 'nullable' => False),
					'female' => array('type' => 'int','precision' => '4', 'nullable' => False),
				),
				'pk' => array('application_id', 'agegroup_id'),
				'fk' => array(
					'bb_application' => array('application_id' => 'id'),
					'bb_agegroup' => array('agegroup_id' => 'id')),
				'ix' => array(),
				'uc' => array()
		));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'activity_id' => array('type' => 'int','precision' => '4','nullable' => False),
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
					'application_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
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
					'application_id' => array('type' => 'int','precision' => '4','nullable' => False),
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
					'application_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'from_' => array('type' => 'text', 'nullable' => False),
					'to_' => array('type' => 'text', 'nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(
					'bb_application' => array('application_id' => 'id')),
				'ix' => array(),
				'uc' => array('application_id', 'from_', 'to_')
		));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'organization_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'from_' => array('type' => 'timestamp','nullable' => False),
					'to_' => array('type' => 'timestamp','nullable' => False),
					'season_id' => array('type' => 'int','precision' => '4','nullable' => False)
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
					'allocation_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
				),
				'pk' => array('allocation_id', 'resource_id'),
				'fk' => array(
					'bb_allocation' => array('allocation_id' => 'id'),
					'bb_resource' => array('resource_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
		));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'active' => array('type' => 'int', 'nullable' => False,'precision' => '4', 'default' => 1),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'active' => array('type' => 'int', 'nullable' => False,'precision' => '4', 'default' => 1),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'season_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'organization_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'wday' => array('type' => 'int','precision' => '4','nullable' => False),
					'cost' => array('type' => 'decimal','precision' => '10', 'scale'=>'2', 'nullable' => False),
					'from_' => array('type' => 'time','nullable' => False),
					'to_' => array('type' => 'time','nullable' => False),
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
					'allocation_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
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
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'season_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'wday' => array('type' => 'int','precision' => '4','nullable' => False),
					'from_' => array('type' => 'time','nullable' => False),
					'to_' => array('type' => 'time','nullable' => False),
				),
				'pk' => array('id'),
				'fk' => array(
					'bb_season' => array('season_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'parent_id' => array('type' => 'int','precision' => '4','nullable' => TRUE),
					'name' => array('type' => 'varchar','precision' => '50','nullable' => FALSE),
					'description' => array('type' => 'varchar','precision' => '10000','nullable' => FALSE),
				),
				'pk' => array('id'),
				'fk' => array(
					'bb_activity' => array('parent_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'description' => array('type' => 'varchar','precision' => '10000','nullable' => False),
				),
				'pk' => array('id'),
				'fk' => array(
					'bb_resource' => array('resource_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'season_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
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
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'booking_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
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
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'bb_booking_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'bb_resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
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
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'category' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'resources' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'group_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'from_' => array('type' => 'varchar','precision' => '5','nullable' => False),
					'to_' => array('type' => 'varchar','precision' => '5','nullable' => False),
					'date' => array('type' => 'date','precision' => '50','nullable' => False),
					'season_id' => array('type' => 'int','precision' => '4','nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(
					'bb_group' => array('group_id' => 'id'),
					'bb_season' => array('season_id' => 'id')),
				'ix' => array(),
				'uc' => array()
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'organization_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				),
				'pk' => array('id'),
				'fk' => array(
					'bb_organization' => array('organization_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'season_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'wday' => array('type' => 'int','precision' => '4','nullable' => False),
					'from_' => array('type' => 'varchar','precision' => '5', 'nullable' => False),
					'to_' => array('type' => 'varchar','precision' => '5','nullable' => False),
				),
				'pk' => array('id'),
				'fk' => array(
					'bb_season' => array('season_id' => 'id')
				),
				'ix' => array(),
				'uc' => array('season_id', 'wday')
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_season','status',array('type' => 'varchar','precision' => 10,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_season','from_',array('type' => 'date','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('bb_season','to_',array('type' => 'date','nullable' => False));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'building_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				),
				'pk' => array('id'),
				'fk' => array(
					'bb_building' => array('building_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'building_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				),
				'pk' => array('id'),
				'fk' => array(
					'bb_building' => array('building_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'homepage' => array('type' => 'varchar','precision' => '50','nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
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
					'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'homepage' => array('type' => 'varchar','precision' => '50','nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.1.1';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
