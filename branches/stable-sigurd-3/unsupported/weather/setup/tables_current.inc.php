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

  /* table array for weather */
	$phpgw_baseline = array(
		'phpgw_weather_admin' => array(
			'fd' => array(
				'admin_gdlib_e' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'admin_gdtype' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'admin_imgsrc' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'admin_remote_e' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'admin_filesize' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '120000')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_weather' => array(
			'fd' => array(
				'weather_id' => array('type' => 'auto'),
				'weather_owner' => array('type' => 'varchar', 'precision' => 32,'nullable' => False),
				'weather_metar' => array('type' => 'varchar', 'precision' => 255,'nullable' => True,'default' => ''),
				'weather_links' => array('type' => 'varchar', 'precision' => 255,'nullable' => True,'default' => ''),
				'weather_title_e' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'weather_observ_e' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'weather_foreca_e' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'weather_links_e' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'weather_wunder_e' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'weather_fpage_e' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'weather_template' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'weather_city' => array('type' => 'varchar', 'precision' => 50,'nullable' => True,'default' => ''),
				'weather_country' => array('type' => 'varchar', 'precision' => 50,'nullable' => True,'default' => ''),
				'weather_gstation' => array('type' => 'varchar', 'precision' => 25,'nullable' => True,'default' => ''),
				'weather_sticker' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'weather_tmetar' => array('type' => 'varchar', 'precision' => 25,'nullable' => True,'default' => ''),
				'weather_tsize' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
				'weather_fpmetar' => array('type' => 'varchar', 'precision' => 25,'nullable' => True,'default' => ''),
				'weather_fpsize' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '1'),
				'state_id' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('weather_id')
		),
		'phpgw_weather_links' => array(
			'fd' => array(
				'links_id' => array('type' => 'auto'),
				'links_name' => array('type' => 'varchar', 'precision' => 35,'nullable' => False,'default' => ''),
				'links_timetag' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'links_refresh' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'links_linkurl' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'links_baseurl' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'links_parseurl' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'links_parseexpr' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'links_imageurl' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'links_comment' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'links_type' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => '')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('links_id')
		),
		'phpgw_weather_metar' => array(
			'fd' => array(
				'metar_id' => array('type' => 'auto'),
				'metar_weather' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'metar_timestamp' => array('type' => 'timestamp'),
				'metar_station' => array('type' => 'varchar', 'precision' => 4,'nullable' => False,'default' => ''),
				'metar_city' => array('type' => 'varchar', 'precision' => 128,'nullable' => False,'default' => ''),
				'metar_forecast' => array('type' => 'varchar', 'precision' => 6,'nullable' => False,'default' => ''),
				'metar_map' => array('type' => 'char', 'precision' => 3,'nullable' => False,'default' => ''),
				'region_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('metar_id','metar_station')
		),
		'phpgw_weather_region' => array(
			'fd' => array(
				'region_id' => array('type' => 'auto'),
				'region_name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False,'default' => '')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('region_id')
		),
		'phpgw_us_states' => array(
			'fd' => array(
				'state_id' => array('type' => 'auto'),
				'state_code' => array('type' => 'char', 'precision' => 2,'nullable' => False,'default' => ''),
				'state_name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False,'default' => '')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('state_id')
		)
	);
?>
