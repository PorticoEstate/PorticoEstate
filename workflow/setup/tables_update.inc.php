<?php
	/**************************************************************************\
	* phpGroupWare - Setup                                                       *
	* http://www.phpgroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: tables_update.inc.php 23536 2007-03-27 07:58:23Z regis_leroy $ */

	$test[] = '1.0.1';
	function workflow_upgrade1_0_1()
	{
		# add an instance_supplements table
		$GLOBALS['phpgw_setup']->oProc->createTable('phpgw_wf_instance_supplements',
			array(
				'fd' => array(
					'wf_supplement_id' 	=> array('type' => 'auto', 'precision' => '4', 'nullable' => False),
					'wf_supplement_type'	=> array('type' => 'varchar', 'precision' => '50', 'nullable' => True),
					'wf_supplement_name'	=> array('type' => 'varchar', 'precision' => '100', 'nullable' => True),
					'wf_supplement_value'	=> array('type' => 'text', 'nullable' => True),
					'wf_workitem_id'	=> array('type' => 'int', 'precision' => '4', 'nullable' => True),
					'wf_supplement_blob'	=> array('type' => 'blob', 'nullable' => True)
				),
				'pk' => array('wf_supplement_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		#Add in activities table is_reassign_box, is_report, default_user and default group
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_wf_activities' ,'wf_is_reassign_box',array('type' => 'char', 'precision' => 1, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_wf_activities' ,'wf_is_report',array('type' => 'char', 'precision' => 1, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_wf_activities' ,'wf_default_user', array('type' => 'varchar', 'precision' => '200', 'nullable' => True, 'default' => '*'));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_wf_activities' ,'wf_default_group', array('type' => 'varchar', 'precision' => '200', 'nullable' => True, 'default' => '*'));

		#Add in instance_activities table the group field
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_wf_instance_activities' ,'wf_group',array('type' => 'varchar', 'precision' => 200, 'nullable' => True, 'default' => '*'));

		#Add in instance table the name, and the priority, we keep the properties for the moment
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_wf_instances' ,'wf_priority',array('type' => 'int', 'precision' => 4, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_wf_instances' ,'wf_name',array('type' => 'varchar', 'precision' => 120, 'nullable' => True));

		#Add in workitems table note and action
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_wf_workitems' ,'wf_note',array('type' => 'text', 'precision' => 50, 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_wf_workitems' ,'wf_action',array('type' => 'text', 'precision' => 50, 'nullable' => True));

		#Add in user_roles table the account type
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_wf_user_roles' ,'wf_account_type',array('type' => 'char', 'precision' => 1, 'nullable' => True, 'default' => 'u'));
			#modifying the sequence as well
			#we need a RefreshTable
		$GLOBALS['phpgw_setup']->oProc->RefreshTable('phpgw_wf_user_roles' ,array(
		 	'fd' => array(
				'wf_role_id'            => array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'wf_p_id'               => array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_user'               => array('type' => 'varchar', 'precision' => '200', 'nullable' => False),
				'wf_account_type'       => array('type' => 'char', 'precision' => '1', 'nullable' => True, 'default' => 'u'),
			 ),
			 'pk' => array('wf_role_id', 'wf_user', 'wf_account_type'),
			 'fk' => array(),
			 'ix' => array(),
			 'uc' => array()
		));

		#updating the current version
		$GLOBALS['setup_info']['workflow']['currentver'] = '1.1.00.000';
		return $GLOBALS['setup_info']['workflow']['currentver'];
	}

	$test[] = '1.1.00.000';
	function workflow_upgrade1_1_00_000()
	{
		# add a process_config table
		$GLOBALS['phpgw_setup']->oProc->createTable('phpgw_wf_process_config',
			array(
				'fd' => array(
					'wf_p_id'               => array('type' => 'int', 'precision' => '4', 'nullable' => False),
					'wf_config_name' 	=> array('type' => 'varchar', 'precision' => '255', 'nullable' => False),
					'wf_config_value'	=> array('type' => 'text', 'nullable' => True),
					'wf_config_value_int'	=> array('type' => 'int', 'precision' => '4', 'nullable' => True),
				),
				'pk' => array('wf_p_id','wf_config_name'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		//change de default value for priority


		#updating the current version
		$GLOBALS['setup_info']['workflow']['currentver'] = '1.1.01.000';
		return $GLOBALS['setup_info']['workflow']['currentver'];
	}

	$test[] = '1.1.01.000';
	function workflow_upgrade1_1_01_000()
	{
		#remove unused 'new' fields in activity and add a agent key
 		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_wf_activities', '', 'wf_is_reassign_box');
 		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_wf_activities', '', 'wf_is_report');
 		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_wf_activities', '', 'wf_default_group');
 		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_wf_activities' ,'wf_agent', array('type' => 'int', 'precision' => '4', 'nullable' => True));

		#add a readonly attribute to role/activty mapping
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_wf_activity_roles' ,'wf_readonly', array('type' => 'int', 'precision' => '1', 'nullable' => False, 'default'=> 0));

		#add a instance category attribute
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_wf_instances' ,'wf_category', array('type' => 'int', 'precision'=>'4', 'nullable' => True));

		#updating the current version
		$GLOBALS['setup_info']['workflow']['currentver'] = '1.1.02.000';
		return $GLOBALS['setup_info']['workflow']['currentver'];
	}


	$test[] = '1.1.02.000';
	function workflow_upgrade1_1_02_000()
	{
 		//drop the agent key in activity, we need something more complex in fact
 		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_wf_activities','','wf_agent');

		//add the agent table, link between activities and agents
		$GLOBALS['phpgw_setup']->oProc->createTable('phpgw_wf_activity_agents',
			array(
				'fd' => array(
					'wf_activity_id'	=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
					'wf_agent_id' 		=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
					'wf_agent_type'		=> array('type' => 'varchar', 'precision' => '15', 'nullable' => False),
				),
				'pk' => array('wf_activity_id', 'wf_agent_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		// add the mail_smtp agent table
		$GLOBALS['phpgw_setup']->oProc->createTable('phpgw_wf_agent_mail_smtp',
			array(
				'fd' => array(
					'wf_agent_id'		=> array('type' => 'auto', 'precision' => '4', 'nullable' => False),
					'wf_to' 		=> array('type' => 'varchar', 'precision' => '255', 'nullable' => False, 'default' => '%roles%'),
					'wf_cc'			=> array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
					'wf_bcc'		=> array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
					'wf_from'		=> array('type' => 'varchar', 'precision' => '255', 'nullable' => True, 'default' => '%user%'),
					'wf_replyTo'		=> array('type' => 'varchar', 'precision' => '255', 'nullable' => True, 'default' => '%user%'),
					'wf_subject'		=> array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
					'wf_message'		=> array('type' => 'text', 'nullable' => True),
					'wf_send_mode'		=> array('type' => 'int', 'precision' => '4', 'nullable' => True, 'default' => 0),
				),
				'pk' => array('wf_agent_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		#updating the current version
		$GLOBALS['setup_info']['workflow']['currentver'] = '1.1.03.000';
		return $GLOBALS['setup_info']['workflow']['currentver'];
	}

	$test[] = '1.1.03.000';
	function workflow_upgrade1_1_03_000()
	{
		//change type of wf_next_user to handle serialization -> multiple states if instance has multiple activities running
		//we will loose current wf_next_activity but the update should'nt be made when instances are running and this is
		//a field needed only at runtime, normally.
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_wf_instances','','wf_next_activity');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_wf_instances' ,'wf_next_activity', array('type' => 'blob', 'nullable' => True));

		#updating the current version
		$GLOBALS['setup_info']['workflow']['currentver'] = '1.1.04.000';
		return $GLOBALS['setup_info']['workflow']['currentver'];
	}

	$test[] = '1.1.04.000';
	function workflow_upgrade1_1_04_000()
	{
		//unused column. Notice I had to do this manually with MAIN versions of phpgwapi/class.schema_proc.inc.php
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_wf_instance_activities','','wf_group');

		//Adding some indexes on some tables:

		//we need a RefreshTable for that
		$GLOBALS['phpgw_setup']->oProc->RefreshTable('phpgw_wf_instance_activities' ,array(
			'fd' => array(
				'wf_instance_id'	=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_activity_id'	=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_started'		=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_ended'		=> array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'wf_user'		=> array('type' => 'varchar', 'precision' => '200', 'nullable' => True),
				'wf_status'		=> array('type' => 'varchar', 'precision' => '25', 'nullable' => True),
			),
			'pk' => array('wf_instance_id', 'wf_activity_id'),
			'fk' => array(),
			'ix' => array(array('wf_activity_id'),array('wf_instance_id'), array('wf_user')),
			'uc' => array()
		));
		// we change some indexes
		// we need a RefreshTable
		$GLOBALS['phpgw_setup']->oProc->RefreshTable('phpgw_wf_instances' ,array(
			'fd' => array(
				'wf_instance_id'	=> array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'wf_p_id'		=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_started'		=> array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'wf_owner'		=> array('type' => 'varchar', 'precision' => '200', 'nullable' => True),
				'wf_next_activity'	=> array('type' => 'blob', 'nullable' => True),
				'wf_next_user'		=> array('type' => 'varchar', 'precision' => '200', 'nullable' => True),
				'wf_ended'		=> array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'wf_status'		=> array('type' => 'varchar', 'precision' => '25', 'nullable' => True),
				'wf_priority'		=> array('type' => 'int', 'precision' => '4', 'nullable' => True, 'default'=> 0),
				'wf_properties'		=> array('type' => 'blob', 'nullable' => True),
				'wf_name'		=> array('type' => 'varchar', 'precision'=>'120', 'nullable' => True),
				'wf_category'		=> array('type' => 'int', 'precision'=>'4', 'nullable' => True),
			),
			'pk' => array('wf_instance_id'),
			'fk' => array(),
			'ix' => array(array('wf_owner'), array('wf_status')),
			'uc' => array()
		));
		// we change some indexes
		// we need a RefreshTable
		$GLOBALS['phpgw_setup']->oProc->RefreshTable('phpgw_wf_processes' ,array(
			'fd' => array(
				'wf_p_id'		=> array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'wf_name'		=> array('type' => 'varchar', 'precision' => '80', 'nullable' => True),
				'wf_is_valid'		=> array('type' => 'char', 'precision' => '1', 'nullable' => True),
				'wf_is_active'		=> array('type' => 'char', 'precision' => '1', 'nullable' => True),
				'wf_version'		=> array('type' => 'varchar', 'precision' => '12', 'nullable' => True),
				'wf_description'	=> array('type' => 'text', 'nullable' => True),
				'wf_last_modif'		=> array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'wf_normalized_name'	=> array('type' => 'varchar', 'precision' => '80', 'nullable' => True),
			),
			'pk' => array('wf_p_id'),
			'fk' => array(),
			'ix' => array(array('wf_p_id','wf_is_active')),
			'uc' => array()
		));

		#updating the current version
		$GLOBALS['setup_info']['workflow']['currentver'] = '1.1.05.000';
		return $GLOBALS['setup_info']['workflow']['currentver'];
	}

	$test[] = '1.1.05.000';
	function workflow_upgrade1_1_05_000()
	{
		#serialized data is now stored with a Base64 encoding to ensure it work in all case (even with \' for example)
		//We gonna make our updates manually here:
		//they were an error (quotes) in the first version of this upgrade, if your database is MySQL you should
		//set the version to 1.1.05.000 in egw_applications and rerun this upgrade for old properties
		$GLOBALS['phpgw']->ADOdb->SetFetchMode(ADODB_FETCH_ASSOC);
		$result = $GLOBALS['phpgw']->ADOdb->query('select * from phpgw_wf_instances');
		if (!(empty($result)))
		{
			while ($res = $result->fetchRow())
			{
				$new_props = base64_encode($res['wf_properties']);
				$new_next = base64_encode($res['wf_next_activity']);
				$ok =  $GLOBALS['phpgw']->ADOdb->query(
					'update phpgw_wf_instances set wf_properties = ?, wf_next_activity=? where wf_instance_id = ?',
					array($new_props,$new_next, (int)$res['wf_instance_id'])
				);
			}
		}
		$result = $GLOBALS['phpgw']->ADOdb->query('select * from phpgw_wf_workitems');
		if (!(empty($result)))
		{
			while ($res = $result->fetchRow())
			{
				$new_props = base64_encode($res['wf_properties']);
				$ok =  $GLOBALS['phpgw']->ADOdb->query(
					'update phpgw_wf_workitems set wf_properties = ?  where wf_item_id = ?',
					array($new_props, (int)$res['wf_item_id'])
				);
			}
		}

		#updating the current version
		$GLOBALS['setup_info']['workflow']['currentver'] = '1.2.00.000';
		return $GLOBALS['setup_info']['workflow']['currentver'];
	}

	$test[] = '1.2.00.000';
	function workflow_upgrade1_2_00_000()
	{
		#updating the current version
		$GLOBALS['setup_info']['workflow']['currentver'] = '1.2.00.001';
		return $GLOBALS['setup_info']['workflow']['currentver'];
	}
	$test[] = '1.2.00.001';
	function workflow_upgrade1_2_00_001()
	{
		#updating the current version
		$GLOBALS['setup_info']['workflow']['currentver'] = '1.2.00.002';
		return $GLOBALS['setup_info']['workflow']['currentver'];
	}
	$test[] = '1.2.00.002';
	function workflow_upgrade1_2_00_002()
	{
		#groups Ids are now negative in phpgroupware, we need to negative all user id which is positive and of type 'g'
		#code inspired by phpgwapi/setup/table_update.inc.php
		// convert all positive group id's to negative ones
		// this allows duplicate id for users and groups in ldap
		$where = false;
		list($table,$col,$where) = $data;
		$table = 'phpgw_wf_user_roles';
		$col = 'wf_user';
		$where = "wf_account_type='g' and (SUBSTR($col,1,1) <> '-')";
		$set = $col.'='.$GLOBALS['phpgw_setup']->db->concat("'-'",$col);
		$query = "UPDATE $table SET $set WHERE $where";
		//echo "<p>debug query: $query</p>\n";
		$GLOBALS['phpgw_setup']->db->query($query,__LINE__,__FILE__);

		#updating the current version
		$GLOBALS['setup_info']['workflow']['currentver'] = '1.2.01.000';
		return $GLOBALS['setup_info']['workflow']['currentver'];
	}

	$test[] = '1.2.01.000';
        function workflow_upgrade1_2_01_000()
        {
                #updating the current version
                $GLOBALS['setup_info']['workflow']['currentver'] = '1.2.01.001';
                return $GLOBALS['setup_info']['workflow']['currentver'];
        }

        $test[] = '1.2.01.001';
        function workflow_upgrade1_2_01_001()
        {
                #updating the current version
                $GLOBALS['setup_info']['workflow']['currentver'] = '1.2.01.002';
                return $GLOBALS['setup_info']['workflow']['currentver'];
        }

        $test[] = '1.2.01.002';
        function workflow_upgrade1_2_01_002()
        {
                #updating the current version
                $GLOBALS['setup_info']['workflow']['currentver'] = '1.2.01.003';
                return $GLOBALS['setup_info']['workflow']['currentver'];
        }

	$test[] = '1.2.01.003';
	function workflow_upgrade1_2_01_003()
	{
		#updating the current version
		$GLOBALS['setup_info']['workflow']['currentver'] = '1.2.01.004';
		return $GLOBALS['setup_info']['workflow']['currentver'];
	}

	$test[] = '1.2.01.004';
        function workflow_upgrade1_2_01_004()
        {
                #updating the current version
                $GLOBALS['setup_info']['workflow']['currentver'] = '1.2.01.005';
                return $GLOBALS['setup_info']['workflow']['currentver'];
        }

	$test[] = '1.2.01.005';
        function workflow_upgrade1_2_01_005()
        {
                #updating the current version
                $GLOBALS['setup_info']['workflow']['currentver'] = '1.2.01.006';
                return $GLOBALS['setup_info']['workflow']['currentver'];
        }

	$test[] = '1.2.01.006';
		function workflow_upgrade1_2_01_006()
		{
			#updating the current version
			$GLOBALS['setup_info']['workflow']['currentver'] = '1.2.01.007';
			return $GLOBALS['setup_info']['workflow']['currentver'];
		}

	$test[] = '1.2.01.007';
        function workflow_upgrade1_2_01_007()
        {
                #updating the current version
                $GLOBALS['setup_info']['workflow']['currentver'] = '1.3.00.000';
                return $GLOBALS['setup_info']['workflow']['currentver'];
        }

?>
