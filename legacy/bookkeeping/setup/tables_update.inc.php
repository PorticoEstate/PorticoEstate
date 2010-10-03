<?php
	/**************************************************************************\
	* phpGroupWare - Setup                                                     *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id$ */

	$test[] = '0.4';
	function bookkeeping_upgrade0_4()
	{
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_bk_a');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_bk_at');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_bk_ba');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_bk_bm');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_bk_c');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_bk_g');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_bk_gpa');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_bk_gr');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_bk_l');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_bk_m');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_bk_t');

		$GLOBALS['setup_info']['bookkeeping']['currentver'] = '0.5.1.001';
		return $GLOBALS['setup_info']['bookkeeping']['currentver'];
	}
?>
