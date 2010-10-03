<?php
  /**************************************************************************\
  * phpGroupWare - Setup                                                     *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /**************************************************************************\
  * This file should be generated for you by setup. It should not need to be *
  * edited by hand.                                                          *
  \**************************************************************************/

  /* $Id$ */

  /* table array for ged */
	$phpgw_baseline = array(
		'ged_comments' => array(
			'fd' => array(
				'comment_id' => array('type' => 'auto','nullable' => False),
				'element_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'account_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'comment_date'=>array('type'=>'timestamp','nullable'=>False,'default'=>'current_timestamp'),
				'comment' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('comment_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'ged_elements' => array(
			'fd' => array(
				'name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'owner_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'element_id' => array('type' => 'auto','nullable' => False),
				'parent_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'reference' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'type' => array('type' => 'varchar', 'precision' => 100,'nullable' => False,'default' => '0'),
				'creator_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'creation_date' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'validity_period' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'cat_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'lock_status' => array('type' => 'varchar', 'precision' => 100,'nullable' => False,'default' => '0'),
				'lock_user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'description' => array('type' => 'text','nullable' => False),
				'doc_type' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'project_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'project_root' => array('type' => 'int', 'precision' => 4,'nullable' => True)
			),
			'pk' => array('element_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'ged_history' => array(
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
		),
		'ged_relations' => array(
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
		),
		'ged_mimes' => array(
			'fd' => array(
				'file_extension' => array('type' => 'char', 'precision' => 10,'nullable' => False),
				'mime_type' => array('type' => 'char', 'precision' => 50,'nullable' => False)
			),
			'pk' => array('file_extension'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'ged_versions' => array(
			'fd' => array(
				'url' => array('type' => 'varchar', 'precision' => 100,'nullable' => true),
				'size' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'status' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'creator_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'validation_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'creation_date' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'minor' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'version_id' => array('type' => 'auto','nullable' => False),
				'element_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'description' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'file_extension' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'file_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => '0'),
				'major' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'stored_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False)
			),
			'pk' => array('version_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'ged_acl' => array(
			'fd' => array(
				'acl_id' => array('type' => 'auto','nullable' => False),
				'element_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'account_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'date_begin' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'date_expire' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'inherited' => array('type' => 'int', 'precision' => 2,'nullable' => True,'default' => '0'),
				'aclread' => array('type' => 'int', 'precision' => 2,'nullable' => True),
				'aclstatuses' => array('type' => 'text','nullable' => True),
				'aclwrite' => array('type' => 'int', 'precision' => 2,'nullable' => True),
				'acldelete' => array('type' => 'int', 'precision' => 2,'nullable' => True,'default' => '0'),
				'aclchangeacl' => array('type' => 'int', 'precision' => 2,'nullable' => True,'default' => '0')
			),
			'pk' => array('acl_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'ged_doc_types' => array(
			'fd' => array(
				'type_id' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'type_desc' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'type_chrono' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'type_ref' => array('type' => 'varchar', 'precision' => 255,'nullable' => True)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'ged_types_places' => array(
			'fd' => array(
				'type_id' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'project_root' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'element_id' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'ged_periods' => array(
			'fd' => array(
				'period' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'description' => array('type' => 'varchar', 'precision' => 100,'nullable' => False)
			),
			'pk' => array('period'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
				'phpgw_flows' => array(
			'fd' => array(
				'flow' => array('type' => 'auto','nullable' => False),
				'app' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'flow_name' => array('type' => 'varchar', 'precision' => 252,'nullable' => False)
			),
			'pk' => array('flow'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_flows_roles' => array(
			'fd' => array(
				'role' => array('type' => 'auto','nullable' => False),
				'transition' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'account_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'context' => array('type' => 'varchar', 'precision' => 255,'nullable' => True)
			),
			'pk' => array('role'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_flows_statuses' => array(
			'fd' => array(
				'status_id' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'app' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'status_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False)
			),
			'pk' => array('status_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_flows_transitions' => array(
			'fd' => array(
				'transition' => array('type' => 'auto','nullable' => False),
				'flow' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'from_status' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'to_status' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'action' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'method' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => 'set_status')
			),
			'pk' => array('transition'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_flows_transitions_custom_values' => array(
			'fd' => array(
				'custom_value_id' => array('type' => 'auto','nullable' => False),
				'transition' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'field_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'value' => array('type' => 'varchar', 'precision' => 255,'nullable' => False)
			),
			'pk' => array('custom_value_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_flows_triggers' => array(
			'fd' => array(
				'trigger_id' => array('type' => 'auto','nullable' => False),
				'transition' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '11'),
				'app' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'class' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => 'flow_client'),
				'method' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'context' => array('type' => 'varchar', 'precision' => 255,'nullable' => False)
			),
			'pk' => array('trigger_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_flows_conditions' => array(
			'fd' => array(
				'condition_id' => array('type' => 'auto','nullable' => False),
				'transition' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '11'),
				'app' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'class' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => 'flow_client'),
				'method' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'context' => array('type' => 'varchar', 'precision' => 255,'nullable' => False)
			),
			'pk' => array('condition_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
	);
?>
