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
  * This file should be generated for you. It should never be edited by hand *
  \**************************************************************************/

  /* $Id$ */

  // table array for timetrack
	$phpgw_baseline = array(
		'phpgw_ttrack_customers' => array(
			'fd' => array(
				'company_id' => array('type' => 'auto','nullable' => False),
				'company_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'website' => array('type' => 'varchar', 'precision' => 80,'nullable' => True),
				'ftpsite' => array('type' => 'varchar', 'precision' => 80,'nullable' => True),
				'industry_type' => array('type' => 'varchar', 'precision' => 50,'nullable' => True),
				'status' => array('type' => 'varchar', 'precision' => 30,'nullable' => True),
				'software' => array('type' => 'varchar', 'precision' => 40,'nullable' => True),
				'lastjobnum' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'lastjobfinished' => array('type' => 'date','nullable' => True),
				'busrelationship' => array('type' => 'varchar', 'precision' => 30,'nullable' => True),
				'notes' => array('type' => 'text','nullable' => True),
				'active' => array('type' => 'char', 'precision' => 1,'nullable' => False,'default' => 'N')
			),
			'pk' => array('company_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_ttrack_emplyprof' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'lid' => array('type' => 'varchar', 'precision' => 20,'nullable' => True),
				'title' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'phone_number' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'comments' => array('type' => 'text','nullable' => True),
				'mobilephn' => array('type' => 'varchar', 'precision' => 24,'nullable' => True),
				'pager' => array('type' => 'varchar', 'precision' => 20,'nullable' => True),
				'hire_date' => array('type' => 'date','nullable' => True),
				'yearly_vacation_hours' => array('type' => 'int', 'precision' => 2,'nullable' => True),
				'vacation_hours_used_todate' => array('type' => 'int', 'precision' => 2,'nullable' => True),
				'location_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'inorout' => array('type' => 'char', 'precision' => 1,'nullable' => False,'default' => 'O')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_ttrack_job_details' => array(
			'fd' => array(
				'detail_id' => array('type' => 'auto','nullable' => False),
				'job_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'account_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'work_catagory_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'work_date' => array('type' => 'date','nullable' => True),
				//'start_time' => array('type' => 'time','nullable' => True),
				//'end_time' => array('type' => 'time','nullable' => True),
				'start_time' => array('type' => 'char', 'precision' => 10, 'nullable' => True),
				'end_time' => array('type' => 'char', 'precision' => 10, 'nullable' => True),
				'num_hours' => array('type' => 'float', 'precision' => 4,'nullable' => True),
				'detail_billable' => array('type' => 'char', 'precision' => 1,'nullable' => True),
				'comments' => array('type' => 'text','nullable' => True),
				'exported' => array('type' => 'char', 'precision' => 1,'nullable' => True),
				'export_timestamp' => array('type' => 'int', 'precision' => 4,'nullable' => True)
			),
			'pk' => array('detail_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_ttrack_job_status' => array(
			'fd' => array(
				'status_id' => array('type' => 'auto','nullable' => False),
				'status_name' => array('type' => 'varchar', 'precision' => 20,'nullable' => True)
			),
			'pk' => array('status_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_ttrack_jobs' => array(
			'fd' => array(
				'job_id' => array('type' => 'auto','nullable' => False),
				'company_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'contact_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'account_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'job_number' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '100'),
				'job_revision' => array('type' => 'varchar', 'precision' => 5,'nullable' => True),
				'description' => array('type' => 'text','nullable' => True),
				'quote_date' => array('type' => 'date','nullable' => True),
				'quoted_hours' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'opened_date' => array('type' => 'date','nullable' => True),
				'deadline' => array('type' => 'date','nullable' => True),
				'approved_by' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'status_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'billable' => array('type' => 'char', 'precision' => 1,'nullable' => True),
				'summary' => array('type' => 'varchar', 'precision' => 60,'nullable' => True),
				'completed_date' => array('type' => 'date','nullable' => True),
				'paid_date' => array('type' => 'date','nullable' => True),
				'cancelled_date' => array('type' => 'date','nullable' => True),
				'total_hours' => array('type' => 'float', 'precision' => 4,'nullable' => True),
				'exported' => array('type' => 'char', 'precision' => 1,'nullable' => True),
				'export_timestamp' => array('type' => 'int', 'precision' => 4,'nullable' => True)
			),
			'pk' => array('job_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_ttrack_locations' => array(
			'fd' => array(
				'location_id' => array('type' => 'auto','nullable' => False),
				'location_name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False)
			),
			'pk' => array('location_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_ttrack_wk_cat' => array(
			'fd' => array(
				'work_catagory_id' => array('type' => 'auto','nullable' => False),
				'catagory_desc' => array('type' => 'varchar', 'precision' => 50,'nullable' => True)
			),
			'pk' => array('work_catagory_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
