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

  /* $Id: tables_current.inc.php,v 1.1.1.1 2005/08/23 05:04:15 skwashd Exp $ */


	$phpgw_baseline = array(
		'phpgw_communik8r_acct_types' => array(
			'fd' => array(
				'acct_type_id' => array('type' => 'auto','nullable' => False),
				'type_name' => array('type' => 'varchar','precision' => '10','nullable' => False),
				'type_descr' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'handler' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'is_active' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '1')
			),
			'pk' => array('acct_type_id'),
			'fk' => array(),
			'ix' => array('is_active'),
			'uc' => array()
		),
		'phpgw_communik8r_accts' => array(
			'fd' => array(
				'acct_id' => array('type' => 'auto','nullable' => False),
				'owner_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'acct_name' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'display_name' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'acct_uri' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'username' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'password' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'server' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'port' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'is_ssl' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'is_tls' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'acct_type_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'acct_options' => array('type' => 'text','nullable' => True),
				'signature_id' => array('type' => 'int','precision' => '4','nullable' => True,'default' => '0'),
				'org' => array('type' => 'varchar','precision' => '200','nullable' => True)
			),
			'pk' => array('acct_id'),
			'fk' => array(),
			'ix' => array('owner_id','acct_name','acct_type_id'),
			'uc' => array()
		),
		'phpgw_communik8r_email_msgs' => array(
			'fd' => array(
				'msg_id' => array('type' => 'auto','nullable' => False),
				'mbox_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'msg_uid' => array('type' => 'int','precision' => '4','nullable' => True,'default' => '0'),
				'msg_uidl' => array('type' => 'varchar','precision' => '75','nullable' => True),
				'subject' => array('type' => 'varchar','precision' => '250','nullable' => False),
				'sender' => array('type' => 'varchar','precision' => '250','nullable' => False),
				'date_sent' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'msg_size' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'flag_seen' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'flag_answered' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'flag_deleted' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'flag_flagged' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'flag_draft' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'structure' => array('type' => 'longtext','nullable' => True)
			),
			'pk' => array('msg_id'),
			'fk' => array(),
			'ix' => array('mbox_id','msg_uid','msg_uidl','subject','date_sent','flag_seen','flag_answered','flag_deleted','flag_flagged','flag_draft'),
			'uc' => array()
		),
		'phpgw_communik8r_email_mboxes' => array(
			'fd' => array(
				'mbox_id' => array('type' => 'auto','nullable' => False),
				'mbox_name' => array('type' => 'varchar','precision' => '250','nullable' => False),
				'seperator' => array('type' => 'char','precision' => '1','nullable' => False,'default' => '.'),
				'acct_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'subscribed' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '1'),
				'uidnext' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'uidvalidity' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'lastmod' => array('type' => 'int','precision' => '4','nullable' => True,'default' => '0'),
				'open_state' => array('type' => 'int','precision' => '4','nullable' => True,'default' => '0')
			),
			'pk' => array('mbox_id'),
			'fk' => array(),
			'ix' => array('acct_id','subscribed'),
			'uc' => array()
		),
		' phpgw_communik8r_email_headers' => array(
			'fd' => array(
				'header_key' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'msg_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'seq_no' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'header_val' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('header_key','msg_id','seq_no'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
