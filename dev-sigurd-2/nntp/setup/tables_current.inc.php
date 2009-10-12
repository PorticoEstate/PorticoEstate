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
		'newsgroups' => array(
			'fd' => array(
				'con' =>          array('type' => 'auto', 'nullable' => false),
				'name' =>         array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
				'messagecount' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'lastmessage' =>  array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'active' =>       array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'),
				'lastread' =>     array('type' => 'int', 'precision' => 4)
			),
			'pk' => array('con'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('name')
		),
		'news_msg' => array(
			'fd' => array(
				'con'      => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'msg'      => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				'uid'      => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'udate'    => array('type' => 'int', 'precision' => 4, 'default' => 0),
				'path'     => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'fromadd'  => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'toadd'    => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'ccadd'    => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'bccadd'   => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'reply_to' => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'sender'   => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'return_path'  => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'subject'      => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'message_id'   => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'reference'    => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'in_reply_to'  => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'follow_up_to' => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'nntp_posting_host' => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'nntp_posting_date' => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'x_complaints_to'   => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'x_trace'           => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'x_abuse_info'      => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'x_mailer'          => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'organization'      => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'content_type'      => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'content_description' => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'content_transfer_encoding' => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'mime_version'      => array('type' => 'varchar', 'precision' => 255, 'default' => ''),
				'msgsize'           => array('type' => 'int', 'precision' => 4, 'default' => 0),
				'msglines'          => array('type' => 'int', 'precision' => 4, 'default' => 0),
				'body'              => array('type' => 'longtext', 'nullable' => False)
			),
			'pk' => array('con,msg'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
