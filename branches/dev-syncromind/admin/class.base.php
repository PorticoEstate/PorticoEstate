<?php
	/**************************************************************************\
	* phpGroupWare                                                             *
	* http://www.phpgroupware.org                                              *
	* This file written by Dan Kuykendall <dan@kuykendall.org>                 *
	* Copyright (C) 2003 Dan Kuykendall                                        *
	* -------------------------------------------------------------------------*
  * This program is free software; you can redistribute it and/or modify it  *
  * under the terms of the GNU General Public License as published by the    *
  * Free Software Foundation; either version 2 of the License, or (at your   *
  * option) any later version.                                               *
	\**************************************************************************/

	/* $Id$ */

	class admin_base
	{
		var $sec;
		function start()
		{
			$result['text'] = '|| admin app ||';
			$GLOBALS['phpgw']->add_xsl('admin.base');
			return $result;
		}
	}
