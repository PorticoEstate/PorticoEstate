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

  /* $Id: tables_update.inc.php 4696 2010-02-01 22:48:22Z sigurd $ */

	$test[] = '0.9.14.512';
	function folders_upgrade0_9_14_512()
	{
		$GLOBALS['setup_info']['folders']['currentver'] = '0.9.17.000';
		return $GLOBALS['setup_info']['folders']['currentver'];
	}

