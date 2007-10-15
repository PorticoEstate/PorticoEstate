<?
	/**************************************************************************
	* phpGroupWare - ged
	* http://www.phpgroupware.org
	* Written by Pascal Vilarem <pascal.vilarem@steria.org>
	*
	* --------------------------------------------------------------------------
	*  This program is free software; you can redistribute it and/or modify it
	*  under the terms of the GNU General Public License as published by the
	*  Free Software Foundation; either version 2 of the License, or (at your
	*  option) any later version
	***************************************************************************/

	$test[]='0.9.16.000';
	$test[]='0.9.16.001';
	$test[]='0.9.18.001';
	$test[]='0.9.18.002';
	$test[]='0.9.18.003';
	$test[]='0.9.18.004';
	$test[]='0.9.18.005';
					
	function ged_upgrade0_9_16_000()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('ged_elements','validity_period',array('type'=>'int', 'precision'=>4, 'nullable'=>True, 'default'=>NULL));		
		
		$old_table_def=array(
			'fd'=>array(
				'url'=>array('type'=>'varchar', 'precision'=>100,'nullable'=>False),
				'size'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'status'=>array('type'=>'varchar', 'precision'=>100,'nullable'=>False),
				'creator_id'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'validation_date'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'validity_period'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'creation_date'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'minor'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'version_id'=>array('type'=>'auto','nullable'=>False),
				'element_id'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'description'=>array('type'=>'varchar', 'precision'=>255,'nullable'=>False),
				'file_extension'=>array('type'=>'varchar', 'precision'=>100,'nullable'=>False),
				'file_name'=>array('type'=>'varchar', 'precision'=>255,'nullable'=>False,'default'=>'0'),
				'major'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'stored_name'=>array('type'=>'varchar', 'precision'=>255,'nullable'=>False)
			),
			'pk'=>array('version_id'),
			'fk'=>array(),
			'ix'=>array(),
			'uc'=>array()
		);
		
		$GLOBALS['phpgw_setup']->oProc->DropColumn('ged_versions', $old_table_def, 'validity_period');
		
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('ged_versions','validation_date', array('type'=>'int', 'precision'=>4, 'nullable'=>True, 'default'=>NULL));
		
		/*
		'spcontrol_lifetimes'=>array(
			'fd'=>array(
				'lifetime'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'description'=>array('type'=>'varchar', 'precision'=>100,'nullable'=>False)
			)
			*/
			
		$GLOBALS['phpgw_setup']->oProc->CreateTable('ged_periods',
			array(
			'fd'=>array(
				'period'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'description'=>array('type'=>'varchar', 'precision'=>100,'nullable'=>False)
				),
			'pk'=>array('period'),
			'fk'=>array(),
			'ix'=>array(),
			'uc'=>array()
			)
			);
		
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 0, 'aeternel')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 3600, '1 hour')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 86400, '24 hours')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 2592000, '30 days')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 7776000, '90 days')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 15552000, '6 monthes')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 31104000, '1 year')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 93312000, '3 years')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 155520000, '5 years')" );

		
		$GLOBALS['setup_info']['ged']['currentver']='0.9.16.001';
		return $GLOBALS['setup_info']['ged']['currentver'];
	}

	function ged_upgrade0_9_16_001()
	{
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable( 'ged_relations' ,array(
			'fd' => array(
				'relation_id' => array('type' => 'auto','nullable' => False),
				'linked_version_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'linking_version_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'relation_type' => array('type' => 'varchar', 'precision' => 255,'nullable' => False)
			),
			'pk' => array('relation_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		) );
		
		$GLOBALS['phpgw_setup']->oProc->DropTable('ged_history');
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable('ged_history' , array(
			'fd' => array(
				'history_id' => array('type' => 'auto','nullable' => False),
				'account_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'element_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'version_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'status' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'action' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'ip' => array('type' => 'varchar', 'precision' => 16,'nullable' => True),
				'agent' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'logdate' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'comment' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)	);
		
		$GLOBALS['setup_info']['ged']['currentver']='0.9.18.001';
		return $GLOBALS['setup_info']['ged']['currentver'];
		
	}

	function ged_upgrade0_9_18_001()
	{	
		$GLOBALS['phpgw_setup']->oProc->AddColumn('ged_elements','project_name',
		array('type' => 'varchar', 'precision' => 255,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('ged_elements','project_root', 
		array('type' => 'int', 'precision' => 4,'nullable' => True));		
		
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('ged_versions','url', 
		array('type' => 'varchar', 'precision' => 100,'nullable' => True));
		
		$GLOBALS['setup_info']['ged']['currentver']='0.9.18.002';
		return $GLOBALS['setup_info']['ged']['currentver'];
	
	}

	function ged_upgrade0_9_18_002()
	{	
		$GLOBALS['phpgw_setup']->oProc->query ("UPDATE ged_versions SET status='refused' WHERE status='rejected'" );
		$GLOBALS['phpgw_setup']->oProc->query ("UPDATE ged_history SET status='refused', action='refused', comment='refused WHERE status='rejected'" );
		$GLOBALS['phpgw_setup']->oProc->query ("UPDATE ged_history SET action='approved', comment='approved' WHERE action='accepted'" );
		$GLOBALS['phpgw_setup']->oProc->query ("UPDATE ged_history SET action='accepted', comment='accepted' WHERE status='current'" );
		
		$GLOBALS['setup_info']['ged']['currentver']='0.9.18.003';
		return $GLOBALS['setup_info']['ged']['currentver'];
		
	}

	function ged_upgrade0_9_18_003()
	{	
		$GLOBALS['phpgw_setup']->oProc->query ("UPDATE ged_history SET status='pending_for_acceptation' WHERE status='pending_for_approval'" );
		$GLOBALS['phpgw_setup']->oProc->query ("UPDATE ged_versions SET status='pending_for_acceptation' WHERE status='pending_for_approval'" );
		
		$GLOBALS['setup_info']['ged']['currentver']='0.9.18.004';
		return $GLOBALS['setup_info']['ged']['currentver'];
		
	}
	
	function ged_upgrade0_9_18_004()
	{
		$old_ged_doc_types_table_def=array(
			'fd'=>array(
				'type_ref'=>array('type'=>'varchar', 'precision'=>255,'nullable'=>False),
				'type_desc'=>array('type'=>'varchar', 'precision'=>255,'nullable'=>True),
				'type_chrono'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'type_smq_ref'=>array('type'=>'varchar', 'precision'=>255,'nullable'=>True),
				'ged_parent_id'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0')
			),
			'pk'=>array(),
			'fk'=>array(),
			'ix'=>array(),
			'uc'=>array()
		);

		$GLOBALS['phpgw_setup']->oProc->DropColumn('ged_doc_types', $old_ged_doc_types_table_def, 'ged_parent_id');		

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('ged_doc_types', 'type_ref', 'type_id');
		
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('ged_doc_types', 'type_smq_ref', 'type_ref');
		
		
		$GLOBALS['setup_info']['ged']['currentver']='0.9.18.005';
		return $GLOBALS['setup_info']['ged']['currentver'];			

	}	

	function ged_upgrade0_9_18_005()
	{

		$GLOBALS['phpgw_setup']->oProc->CreateTable('ged_types_places',
			array(
			'fd'=>array(
				'type_id'=>array('type'=>'varchar', 'precision'=>255,'nullable'=>False),
				'project_root' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'element_id' => array('type' => 'int','precision' => 4,'nullable' => False)
				),
			'pk'=>array(),
			'fk'=>array(),
			'ix'=>array(),
			'uc'=>array()
			)
			);

		$GLOBALS['setup_info']['ged']['currentver']='0.9.18.006';
		return $GLOBALS['setup_info']['ged']['currentver'];			

	}	
	
?>