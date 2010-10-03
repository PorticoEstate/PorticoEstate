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

  /* table array for chat */
	$phpgw_baseline = array(
		'phpgw_chat_channel' => array(
			'fd' => array(
				'con' => array('type' => 'auto'),
				'name' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
				'title' => array('type' => 'char', 'precision' => 50,'nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('con')
		),
		'phpgw_chat_messages' => array(
			'fd' => array(
				'con' => array('type' => 'auto'),
				'channel' => array('type' => 'char', 'precision' => 20,'nullable' => False),
				'loginid' => array('type' => 'varchar', 'precision' => 25,'nullable' => False),
				'message' => array('type' => 'text','nullable' => False),
				'messagetype' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'timesent' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('con')
		),
		'phpgw_chat_currentin' => array(
			'fd' => array(
				'con' => array('type' => 'auto'),
				'loginid' => array('type' => 'varchar', 'precision' => 25,'nullable' => False),
				'channel' => array('type' => 'char', 'precision' => 20,'nullable' => False),
				'lastmessage' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('con')
		),
		'phpgw_chat_privatechat' => array(
			'fd' => array(
				'con' => array('type' => 'auto'),
				'user1' => array('type' => 'varchar', 'precision' => 25,'nullable' => False),
				'user2' => array('type' => 'varchar', 'precision' => 25,'nullable' => False),
				'sentby' => array('type' => 'varchar', 'precision' => 25,'nullable' => False),
				'message' => array('type' => 'text','nullable' => False),
				'messagetype' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'timesent' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'closed' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('con')
		)
	);
?>
