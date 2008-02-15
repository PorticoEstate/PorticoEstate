<?php
  /**************************************************************************\
  * phpGroupWare                                                             *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	$phpgw_baseline = array(
		'phpgw_felamimail_cache' => array(
			'fd' => array(
				'accountid' 	=> array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'hostname' 	=> array('type' => 'varchar', 'precision' => 60, 'nullable' => false),
				'accountname' 	=> array('type' => 'varchar', 'precision' => 200, 'nullable' => false),
				'foldername' 	=> array('type' => 'varchar', 'precision' => 200, 'nullable' => false),
				'uid' 		=> array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'subject'	=> array('type' => 'text'),
				'striped_subject'=> array('type' => 'text'),
				'sender_name'	=> array('type' => 'varchar', 'precision' => 120),
				'sender_address'=> array('type' => 'varchar', 'precision' => 120),
				'to_name'	=> array('type' => 'varchar', 'precision' => 120),
				'to_address'	=> array('type' => 'varchar', 'precision' => 120),
				'date'		=> array('type' => 'varchar', 'precision' => 120),
				'size'		=> array('type' => 'int', 'precision' => 4),
				'attachments'	=> array('type' => 'varchar', 'precision' =>120)
			),
			'pk' => array('accountid','hostname','accountname','foldername','uid'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_felamimail_folderstatus' => array(
			'fd' => array(
				'accountid' 	=> array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'hostname' 	=> array('type' => 'varchar', 'precision' => 60, 'nullable' => false),
				'accountname' 	=> array('type' => 'varchar', 'precision' => 200, 'nullable' => false),
				'foldername' 	=> array('type' => 'varchar', 'precision' => 200, 'nullable' => false),
				'messages' 	=> array('type' => 'int', 'precision' => 4),
				'recent'	=> array('type' => 'int', 'precision' => 4),
				'unseen'	=> array('type' => 'int', 'precision' => 4),
				'uidnext'	=> array('type' => 'int', 'precision' => 4),
				'uidvalidity'	=> array('type' => 'int', 'precision' => 4)
			),
			'pk' => array('accountid','hostname','accountname','foldername'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_felamimail_displayfilter' => array(
			'fd' => array(
				'accountid' 	=> array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'filter' 	=> array('type' => 'text')
			),
			'pk' => array('accountid'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
