<?php
	/**************************************************************************\
	* phpGroupWare - Messenger                                                 *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$phpgw_baseline = array(
		'phpgw_messenger_messages' => array(
			'fd' => array(
				'message_id' => array('type' => 'auto', 'precision' => 4,'nullable' => False),
				'message_owner' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'message_from' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'message_status' => array(type => 'char', 'precision' => 1,'nullable' => False),
				'message_date' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'message_subject' => array('type' => 'text','nullable' => False),
				'message_content' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('message_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('message_id')
		),
	);
?>