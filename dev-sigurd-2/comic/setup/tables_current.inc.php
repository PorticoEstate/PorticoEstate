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

  // table array for comic
	$phpgw_baseline = array(
		'phpgw_comic' => array(
			'fd' => array(
				'comic_id' => array('type' => 'auto','nullable' => False),
				'comic_owner' => array('type' => 'varchar', 'default'=> '', 'precision' => 32,'nullable' => False),
				'comic_list' => array('type' => 'blob', 'nullable' => False),
				'comic_scale' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => 0),
				'comic_perpage' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => 4),
				'comic_frontpage' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => 0),
				'comic_fpscale' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => 0),
				'comic_censorlvl' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => 0),
				'comic_template' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => 0)
			),
			'pk' => array('comic_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_comic_admin' => array(
			'fd' => array(
				'admin_imgsrc' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => 0),
				'admin_rmtenabled' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => 0),
				'admin_censorlvl' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => 0),
				'admin_coverride' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => 0),
				'admin_filesize' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '120000')
			),
			'pk' => array('admin_imgsrc'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_comic_data' => array(
			'fd' => array(
				'data_id' => array('type' => 'auto','nullable' => False),
				'data_enabled' => array('type' => 'char','precision' => 1, 'nullable' => False,'default' => 'T'),
				'data_name' => array('type' => 'varchar', 'precision' => 25,'nullable' => False),
				'data_author' => array('type' => 'varchar', 'precision' => 128,'nullable' => False),
				'data_title' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'data_prefix' => array('type' => 'varchar', 'precision' => 25,'nullable' => False),
				'data_date' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'data_comicid' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'data_linkurl' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'data_baseurl' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'data_parseurl' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'data_parsexpr' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'data_imageurl' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'data_pubdays' => array('type' => 'varchar', 'precision' => 25,'nullable' => False,'default' => 'Su:Mo:Tu:We:Th:Fr:Sa'),
				'data_parser' => array('type' => 'varchar', 'precision' => 32, 'nullable' => False,'default' => 'None'),
				'data_class' => array('type' => 'varchar', 'precision' => 32, 'nullable' => False,'default' => 'General'),
				'data_censorlvl' => array('type' => 'int', 'precision' => 2,'nullable' => False),
				'data_resolve' => array('type' => 'varchar', 'precision' => 32, 'nullable' => False,'default' => 'Remote'),
				'data_daysold' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'data_width' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'data_swidth' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array('data_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
