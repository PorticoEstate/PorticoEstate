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

	$test[] = '0.9.13';
	function timetrack_upgrade0_9_13()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameTable('phpgw_ttrack_work_categories','phpgw_ttrack_wk_cat');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('phpgw_ttrack_employee_profiles','phpgw_ttrack_emplyprof');

		$GLOBALS['setup_info']['timetrack']['currentver'] = '0.9.13.001';
		return $GLOBALS['setup_info']['timetrack']['currentver'];
	}
?>
