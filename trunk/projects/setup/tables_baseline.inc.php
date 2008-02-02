<?php
	/**************************************************************************\
	* phpGroupWare - Setup                                                     *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id$ */

	$phpgw_baseline = array(
		'p_projects' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'num' => array('type' => 'varchar','precision' => 20,'nullable' => False),
				'owner' => array('type' => 'int','precision' => 4,'nullable' => True),
				'access' => array('type' => 'varchar','precision' => 10,'nullable' => True),
				'entry_date' => array('type' => 'int','precision' => 4,'nullable' => True),
				'date' => array('type' => 'int','precision' => 4,'nullable' => True),
				'end_date' => array('type' => 'int','precision' => 4,'nullable' => True),
				'coordinator' => array('type' => 'int','precision' => 4,'nullable' => True),
				'customer' => array('type' => 'int','precision' => 4,'nullable' => True),
				'status' => array('type' => 'varchar','precision' => 9,'default' => 'nonactive','nullable' => False),
				'descr' => array('type' => 'text','nullable' => True),
				'title' => array('type' => 'varchar','precision' => 50,'nullable' => True),
				'budget' => array('type' => 'decimal','precision' => 20,'scale' => 2,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('num')
		),
		'p_activities' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'num' => array('type' => 'varchar','precision' => 20,'nullable' => True),
				'descr' => array('type' => 'varchar','precision' => 50,'nullable' => True),
				'remarkreq' => array('type' => 'char','precision' => 1,'default' => 'N','nullable' => False),
				'minperae' => array('type' => 'decimal','precision' => 4,'scale' => 0,'nullable' => True),
				'billperae' => array('type' => 'decimal','precision' => 20,'scale' => 2,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'p_projectactivities' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'project_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'activity_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'billable' => array('type' => 'char','precision' => 1,'default' => 'Y','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'p_hours' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'employee' => array('type' => 'int','precision' => 4,'nullable' => False),
				'project_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'activity_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'entry_date' => array('type' => 'int','precision' => 4,'nullable' => True),
				'date' => array('type' => 'int','precision' => 4,'nullable' => True),
				'end_date' => array('type' => 'int','precision' => 4,'nullable' => True),
				'remark' => array('type' => 'text','nullable' => True),
				'minutes' => array('type' => 'int','precision' => 4,'nullable' => True),
				'minperae' => array('type' => 'decimal','precision' => 4,'scale' => 0,'nullable' => True),
				'billperae' => array('type' => 'decimal','precision' => 20,'scale' => 2,'nullable' => True),
				'status' => array('type' => 'varchar','precision' => 6,'default' => 'done','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'p_projectaddress' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'project_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'addressbook_id' => array('type' => 'int','precision' => 4,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'p_projectmembers' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'project_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'account_id' => array('type' => 'int','precision' => 4,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'p_invoice' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'num' => array('type' => 'varchar','precision' => 11,'nullable' => False),
				'date' => array('type' => 'int','precision' => 4,'nullable' => True),
				'project_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'customer' => array('type' => 'int','precision' => 4,'nullable' => True),
				'sum' => array('type' => 'decimal','precision' => 20,'scale' => 2,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('num')
		),
		'p_invoicepos' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'invoice_id' => array('type' => 'varchar','precision' => 11,'nullable' => False),
				'hours_id' => array('type' => 'int','precision' => 4,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'p_delivery' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'num' => array('type' => 'varchar','precision' => 11,'nullable' => False),
				'date' => array('type' => 'int','precision' => 4,'nullable' => True),
				'project_id' => array('type' => 'int','precision' => 4,'nullable' => True),
				'customer' => array('type' => 'int','precision' => 4,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('num')
		),
		'p_deliverypos' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'delivery_id' => array('type' => 'varchar','precision' => 11,'nullable' => False),
				'hours_id' => array('type' => 'int','precision' => 4,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
