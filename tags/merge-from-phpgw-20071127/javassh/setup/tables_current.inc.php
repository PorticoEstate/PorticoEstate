<?php
  /**************************************************************************\
  * phpGroupWare - Setup                                                     *
  * http://www.phpgroupware.org                                              *
  * Created by eTemplates DB-Tools 					     *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: tables_current.inc.php 13409 2003-09-07 02:26:45Z skwashd $ */


	$phpgw_baseline = array(
		'phpgw_javassh_servers' => array(
			'fd' => array(
				'server_id' => array('type' => 'auto','nullable' => False),
				'host' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'port' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '22'),
				'protocol' => array('type' => 'varchar','precision' => '6','nullable' => False,'default' => 'ssh'),
				'active' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '1')
			),
			'pk' => array('server_id'),
			'fk' => array(),
			'ix' => array('active'),
			'uc' => array()
		)
	);
