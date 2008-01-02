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
  /* $Id: tables_current.inc.php 5956 2001-06-16 03:53:26Z bettina $ */

	$phpgw_baseline = array(
		'phpgw_inv_products' => array(
			'fd' => array(
				'con' => array('type' => 'auto','nullable' => False),
				'id' => array('type' => 'varchar','precision' => 20,'nullable' => False),
				'serial' => array('type' => 'varchar','precision' => 64,'nullable' => False),
				'name' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'descr' => array('type' => 'text','nullable' => True),
				'category' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'status' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'weight' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'cost' => array('type' => 'decimal','precision' => 10,'scale' => 2,'default' => 0,'nullable' => False),
				'price' => array('type' => 'decimal','precision' => 10,'scale' => 2,'default' => 0,'nullable' => False),
				'retail' => array('type' => 'decimal','precision' => 10,'scale' => 2,'default' => 0,'nullable' => False),
				'stock' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'mstock' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'url' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'ftp' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'dist' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'pdate' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'sdate' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'bin' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'product_note' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('con'),
			'fk' => array(),
			'ix' => array('con','id'),
			'uc' => array()
		),
		'phpgw_inv_statuslist' => array(
			'fd' => array(
				'status_id' => array('type' => 'auto','nullable' => False),
				'status_name' => array('type' => 'varchar','precision' => 255,'nullable' => False)
			),
			'pk' => array('status_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_inv_orders' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'owner' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'access' => array('type' => 'varchar','precision' => 7,'nullable' => True),
				'num' => array('type' => 'varchar','precision' => 20,'nullable' => False),
				'date' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'customer' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'descr' => array('type' => 'text','nullable' => True),
				'status' => array('type' => 'varchar','precision' => 7,'default' => 'open','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('id','num'),
			'uc' => array('num')
		),
		'phpgw_inv_orderpos' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'order_id' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'product_id' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'piece' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'tax' => array('type' => 'decimal','precision' => 6,'scale' => 2,'default' => 0,'nullable' => False),
				'dstatus' => array('type' => 'char','precision' => 4,'default' => 'open','nullable' => False),
				'istatus' => array('type' => 'char','precision' => 4,'default' => 'open','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_inv_delivery' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'num' => array('type' => 'varchar','precision' => 20,'nullable' => False),
				'delivery_id' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'date' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'order_id' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('id','num'),
			'uc' => array('num')
		),
		'phpgw_inv_deliverypos' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'delivery_id' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'product_id' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_inv_invoice' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'num' => array('type' => 'varchar','precision' => 20,'nullable' => False),
				'date' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'order_id' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'sum' => array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('id','num'),
			'uc' => array('num')
		),
		'phpgw_inv_invoicepos' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'invoice_id' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'product_id' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_inv_stockrooms' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'room_owner' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'room_access' => array('type' => 'varchar','precision' => 7,'nullable' => True),
				'room_name' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'room_note' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
