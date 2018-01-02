<?php
	/*	 * ************************************************************************\
	 * phpGroupWare - Setup                                                     *
	 * http://www.phpgroupware.org                                              *
	 * --------------------------------------------                             *
	 *  This program is free software; you can redistribute it and/or modify it *
	 *  under the terms of the GNU General Public License as published by the   *
	 *  Free Software Foundation; either version 2 of the License, or (at your  *
	 *  option) any later version.                                              *
	  \************************************************************************* */

	/*	 * ************************************************************************\
	 * This file should be generated for you. It should never be edited by hand *
	  \************************************************************************* */

	/* $Id$ */

	// table array for registration
	$phpgw_baseline = array(
		'phpgw_reg_accounts' => array(
			'fd' => array(
				'reg_id' => array('type' => 'char', 'precision' => 32, 'nullable' => False),
				'reg_lid' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'reg_info' => array('type' => 'text', 'nullable' => False),
				'reg_dla' => array('type' => 'int', 'precision' => 4, 'nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);