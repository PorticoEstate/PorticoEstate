<?php
  /**************************************************************************\
  * E-GroupWare - Setup                                                      *
  * http://www.phpgroupware.org                                                *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: tables_current.inc.php 19356 2005-10-11 12:50:31Z regis_glc $ */

	$phpgw_baseline = array(
		'phpgw_wf_activities' => array(
			'fd' => array(
				'wf_activity_id'	=> array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'wf_name'		=> array('type' => 'varchar', 'precision' => '80', 'nullable' => True),
				'wf_normalized_name'	=> array('type' => 'varchar', 'precision' => '80', 'nullable' => True),
				'wf_p_id'		=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_type'		=> array('type' => 'varchar', 'precision' => '25', 'nullable' => True),
				'wf_is_autorouted'	=> array('type' => 'char', 'precision' => '1', 'nullable' => True),
				'wf_flow_num'		=> array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'wf_is_interactive'	=> array('type' => 'char', 'precision' => '1', 'nullable' => True),
				'wf_last_modif'		=> array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'wf_description'	=> array('type' => 'text', 'nullable' => True),
				'wf_default_user'	=> array('type' => 'varchar', 'precision' => '200', 'nullable' => True, 'default'=> '*'),
			),
			'pk' => array('wf_activity_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wf_activity_roles' => array(
			'fd' => array(
				'wf_activity_id'	=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_role_id'		=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_readonly'		=> array('type' => 'int', 'precision' => '2', 'nullable' => False, 'default'=> 0),
			),
			'pk' => array('wf_activity_id', 'wf_role_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wf_instance_activities' => array(
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
		),
		'phpgw_wf_instance_supplements' => array(
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
		),
		'phpgw_wf_instances' => array(
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
		),
		'phpgw_wf_processes' => array(
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
		),
		'phpgw_wf_roles' => array(
			'fd' => array(
				'wf_role_id'		=> array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'wf_p_id'		=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_last_modif'		=> array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'wf_name'		=> array('type' => 'varchar', 'precision' => '80', 'nullable' => True),
				'wf_description'	=> array('type' => 'text', 'nullable' => True),
			),
			'pk' => array('wf_role_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wf_transitions' => array(
			'fd' => array(
				'wf_p_id'		=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_act_from_id'	=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_act_to_id'		=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
			),
			'pk' => array('wf_act_from_id', 'wf_act_to_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wf_user_roles' => array(
			'fd' => array(
				'wf_role_id'		=> array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'wf_p_id'		=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_user'		=> array('type' => 'varchar', 'precision' => '200', 'nullable' => False),
				'wf_account_type'	=> array('type' => 'char', 'precision' => '1', 'nullable' => True, 'default' => 'u'),
			),
			'pk' => array('wf_role_id', 'wf_user', 'wf_account_type'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wf_workitems' => array(
			'fd' => array(
				'wf_item_id'		=> array('type' => 'auto', 'precision' => '4', 'nullable' => False),
				'wf_instance_id'	=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_order_id'		=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_properties'		=> array('type' => 'blob', 'nullable' => True),
				'wf_activity_id'	=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_started'		=> array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'wf_ended'		=> array('type' => 'int', 'precision' => '4', 'nullable' => True),
				'wf_user'		=> array('type' => 'varchar', 'precision' => '200', 'nullable' => True),
				'wf_note'		=> array('type' => 'text', 'nullable' => True),
				'wf_action'		=> array('type' => 'text', 'nullable' => True),
			),
			'pk' => array('wf_item_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wf_process_config' => array(
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
		),
		'phpgw_wf_activity_agents' => array(
			'fd' => array(
				'wf_activity_id'	=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_agent_id' 		=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'wf_agent_type'		=> array('type' => 'varchar', 'precision' => '15', 'nullable' => False),
			),
			'pk' => array('wf_activity_id', 'wf_agent_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wf_agent_mail_smtp' => array(
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
		),
	);
