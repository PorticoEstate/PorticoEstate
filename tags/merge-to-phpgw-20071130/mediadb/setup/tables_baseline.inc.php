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

  /* $Id: tables_baseline.inc.php 6081 2001-06-19 16:36:32Z bettina $ */

  // table array for mediadb
	$phpgw_baseline = array(
		'media' => array(
			'fd' => array(
				'media_id' => array('type' => 'auto','nullable' => False),
				'media_owner' => array('type' => 'varchar', 'precision' => 25,'nullable' => True),
				'media_access' => array('type' => 'varchar', 'precision' => 10,'nullable' => True),
				'media_score' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'media_hscore' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'media_idate' => array('type' => 'timestamp','nullable' => False,'default' => '0000-00-00 00:00:00'),
				'media_avail' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '1'),
				'data_id' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array('media_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'media_artist' => array(
			'fd' => array(
				'artist_id' => array('type' => 'auto','nullable' => False),
				'artist_fname' => array('type' => 'varchar', 'precision' => 50,'nullable' => True),
				'artist_lname' => array('type' => 'varchar', 'precision' => 50,'nullable' => True)
			),
			'pk' => array('artist_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'media_cat' => array(
			'fd' => array(
				'cat_id' => array('type' => 'auto','nullable' => False),
				'cat_name' => array('type' => 'varchar', 'precision' => 50,'nullable' => True),
				'cat_enabled' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '1'),
				'cat_fname' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'cat_fenabled' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'cat_fwidth' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'cat_fsort' => array('type' => 'varchar', 'precision' => 255,'nullable' => True)
			),
			'pk' => array('cat_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'media_data' => array(
			'fd' => array(
				'data_id' => array('type' => 'auto','nullable' => False),
				'data_genre' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'data_feature' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'data_artist' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'data_publisher' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'data_code' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
				'data_title' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'data_date' => array('type' => 'date','precision' => 4,'nullable' => True),
				'data_comments' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'data_score' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'data_image' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => 'default.jpg'),
				'data_efile' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'data_pages' => array('type' => 'int', 'precision' => 2,'nullable' => True),
				'data_hscore' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'cat_id' => array('type' => 'int', 'precision' => 2,'nullable' => False),
				'rating_id' => array('type' => 'int', 'precision' => 2,'nullable' => False),
				'format_id' => array('type' => 'int', 'precision' => 2,'nullable' => False),
				'region_id' => array('type' => 'int', 'precision' => 2,'nullable' => True)
			),
			'pk' => array('data_id'),
			'fk' => array(),
			'ix' => array('data_code'),
			'uc' => array()
		),
		'media_feature' => array(
			'fd' => array(
				'feature_id' => array('type' => 'auto','nullable' => False),
				'feature_type' => array('type' => 'varchar', 'precision' => 50,'nullable' => True),
				'feature_desc' => array('type' => 'varchar', 'precision' => 255,'nullable' => True)
			),
			'pk' => array('feature_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'media_format' => array(
			'fd' => array(
				'format_id' => array('type' => 'auto','nullable' => False),
				'format_desc' => array('type' => 'varchar', 'precision' => 64,'nullable' => True),
				'format_efiles' => array('type' => 'int', 'precision' => 2,'nullable' => False),
				'format_pages' => array('type' => 'int', 'precision' => 2,'nullable' => False),
				'format_regions' => array('type' => 'int', 'precision' => 2,'nullable' => False),
				'format_hscores' => array('type' => 'int', 'precision' => 2,'nullable' => False),
				'cat_id' => array('type' => 'int', 'precision' => 2,'nullable' => False)
			),
			'pk' => array('format_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'media_genre' => array(
			'fd' => array(
				'genre_id' => array('type' => 'auto','nullable' => False),
				'genre_desc' => array('type' => 'varchar', 'precision' => 64,'nullable' => True),
				'cat_id' => array('type' => 'int', 'precision' => 2,'nullable' => False)
			),
			'pk' => array('genre_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'media_loan' => array(
			'fd' => array(
				'loan_id' => array('type' => 'auto','nullable' => False),
				'loan_owner' => array('type' => 'varchar', 'precision' => 25,'nullable' => False,'default' => 'Library'),
				'loan_borrower' => array('type' => 'varchar', 'precision' => 25,'nullable' => False),
				'loan_date' => array('type' => 'date','nullable' => False,'default' => '0000-00-00'),
				'loan_date_due' => array('type' => 'date','nullable' => False,'default' => '0000-00-00'),
				'loan_date_ret' => array('type' => 'date','nullable' => False,'default' => '0000-00-00'),
				'loan_returned' => array('type' => 'int', 'precision' => 2,'nullable' => False),
				'media_id' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array('loan_id'),
			'fk' => array(),
			'ix' => array('loan_owner','loan_borrower','media_id'),
			'uc' => array()
		),
		'media_lookup' => array(
			'fd' => array(
				'lookup_id' => array('type' => 'auto','nullable' => False),
				'lookup_url' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'lookup_block' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'lookup_component' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'lookup_field' => array('type' => 'varchar', 'precision' => 25,'nullable' => True),
				'cat_id' => array('type' => 'int', 'precision' => 2,'nullable' => False)
			),
			'pk' => array('lookup_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'media_publisher' => array(
			'fd' => array(
				'publisher_id' => array('type' => 'auto','nullable' => False),
				'publisher_name' => array('type' => 'varchar', 'precision' => 50,'nullable' => True)
			),
			'pk' => array('publisher_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'media_rating' => array(
			'fd' => array(
				'rating_id' => array('type' => 'auto','nullable' => False),
				'rating_desc' => array('type' => 'varchar', 'precision' => 64,'nullable' => True),
				'cat_id' => array('type' => 'int', 'precision' => 2,'nullable' => False)
			),
			'pk' => array('rating_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'media_region' => array(
			'fd' => array(
				'region_id' => array('type' => 'auto','nullable' => False),
				'region_code' => array('type' => 'int', 'precision' => 2,'nullable' => False),
				'region_desc' => array('type' => 'varchar', 'precision' => 25,'nullable' => False)
			),
			'pk' => array('region_id'),
			'fk' => array(),
			'ix' => array('region_code'),
			'uc' => array()
		),
		'media_request' => array(
			'fd' => array(
				'request_id' => array('type' => 'auto','nullable' => False),
				'request_owner' => array('type' => 'varchar', 'precision' => 25,'nullable' => False),
				'request_status' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => 'new'),
				'request_date' => array('type' => 'date','nullable' => False,'default' => '0000-00-00'),
				'request_date_r' => array('type' => 'date','nullable' => False,'default' => '0000-00-00'),
				'media_id' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array('request_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
