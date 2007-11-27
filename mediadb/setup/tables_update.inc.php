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

  /* $Id: tables_update.inc.php 6023 2001-06-17 20:49:29Z milosch $ */

	$test[] = '0.0.0';
	function mediadb_upgrade0_0_0()
	{
		global $setup_info;
		$setup_info['mediadb']['currentver'] = '0.0.4';
		return $setup_info['mediadb']['currentver'];
	}
	$test[] = '0.0.1';
	function mediadb_upgrade0_0_1()
	{
		global $setup_info;
		$setup_info['mediadb']['currentver'] = '0.0.4';
		return $setup_info['mediadb']['currentver'];
	}
	$test[] = '0.0.2';
	function mediadb_upgrade0_0_2()
	{
		global $setup_info;
		$setup_info['mediadb']['currentver'] = '0.0.4';
		return $setup_info['mediadb']['currentver'];
	}

	$test[] = '0.0.3';
	function mediadb_upgrade0_0_3()
	{
		global $setup_info, $phpgw_setup;

		$phpgw_setup->oProc->RenameTable('media','phpgw_mediadb');
		$phpgw_setup->oProc->RenameTable('media_artist','phpgw_mediadb_artist');
		$phpgw_setup->oProc->RenameTable('media_cat','phpgw_mediadb_cat');
		$phpgw_setup->oProc->RenameTable('media_data','phpgw_mediadb_data');
		$phpgw_setup->oProc->RenameTable('media_feature','phpgw_mediadb_feature');
		$phpgw_setup->oProc->RenameTable('media_format','phpgw_mediadb_format');
		$phpgw_setup->oProc->RenameTable('media_genre','phpgw_mediadb_genre');
		$phpgw_setup->oProc->RenameTable('media_loan','phpgw_mediadb_loan');
		$phpgw_setup->oProc->RenameTable('media_lookup','phpgw_media_lookup');
		$phpgw_setup->oProc->RenameTable('media_publisher','phpgw_mediadb_publisher');
		$phpgw_setup->oProc->RenameTable('media_rating','phpgw_mediadb_rating');
		$phpgw_setup->oProc->RenameTable('media_region','phpgw_mediadb_region');
		$phpgw_setup->oProc->RenameTable('media_request','phpgw_mediadb_request');

		$setup_info['mediadb']['currentver'] = '0.0.4';
		return $setup_info['mediadb']['currentver'];
	}
?>
