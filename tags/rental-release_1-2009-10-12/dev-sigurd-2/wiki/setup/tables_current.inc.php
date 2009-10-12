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

  /* $Id$ */


	$phpgw_baseline = array(
		'phpgw_wiki_links' => array(
			'fd' => array(
				'page' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'link' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'count' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('page','link'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wiki_pages' => array(
			'fd' => array(
				'title' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'version' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '1'),
				'time' => array('type' => 'int','precision' => '4','nullable' => True),
				'supercede' => array('type' => 'int','precision' => '4','nullable' => True),
				'mutable' => array('type' => 'char','precision' => '3','nullable' => False,'default' => 'on'),
				'username' => array('type' => 'varchar','precision' => '80','nullable' => True),
				'author' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'comment' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'body' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('title','version'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wiki_rate' => array(
			'fd' => array(
				'ip' => array('type' => 'char','precision' => '20','nullable' => False,'default' => ''),
				'time' => array('type' => 'int','precision' => '4','nullable' => True),
				'viewLimit' => array('type' => 'int','precision' => '2','nullable' => True),
				'searchLimit' => array('type' => 'int','precision' => '2','nullable' => True),
				'editLimit' => array('type' => 'int','precision' => '2','nullable' => True)
			),
			'pk' => array('ip'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wiki_interwiki' => array(
			'fd' => array(
				'prefix' => array('type' => 'varchar','precision' => '80','nullable' => false,'default' => ''),
				'where_defined' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'url' => array('type' => 'varchar','precision' => '255','nullable' => False,'default' => '')
			),
			'pk' => array('prefix'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wiki_sisterwiki' => array(
			'fd' => array(
				'prefix' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'where_defined' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'url' => array('type' => 'varchar','precision' => '255','nullable' => False,'default' => '')
			),
			'pk' => array('prefix'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wiki_remote_pages' => array(
			'fd' => array(
				'page' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'site' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => '')
			),
			'pk' => array('page','site'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
