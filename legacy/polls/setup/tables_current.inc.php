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

  // table array for polls
	$phpgw_baseline = array(
		'phpgw_polls_data' => array(
			'fd' => array(
				'poll_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'option_text' => array('type' => 'char', 'precision' => 50,'nullable' => False),
				'option_count' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'vote_id' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_polls_desc' => array(
			'fd' => array(
				'poll_id' => array('type' => 'auto','nullable' => False),
				'poll_title' => array('type' => 'char', 'precision' => 100,'nullable' => False),
				'poll_timestamp' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array('poll_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_polls_user' => array(
			'fd' => array(
				'poll_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'vote_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'vote_timestamp' => array('type' => 'int', 'precision' => 4,'nullable' => True)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_polls_settings' => array(
			'fd' => array(
				'setting_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'setting_value' => array('type' => 'varchar', 'precision' => 255,'nullable' => True)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
