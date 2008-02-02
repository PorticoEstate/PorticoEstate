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

	$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_config WHERE config_app='chora'");
	$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_config WHERE config_name='co' OR config_name='rcs' OR config_name='rcsdiff' OR config_name='rlog' OR config_name='cvs' OR config_name='adminname' OR config_name='adminemail' OR config_name='shortloglength'");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app,config_name,config_value)VALUES ('chora','co','/usr/bin/co')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app,config_name,config_value)VALUES ('chora','rcs','/usr/bin/rcs')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app,config_name,config_value)VALUES ('chora','rcsdiff','/usr/bin/rcsdiff')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app,config_name,config_value)VALUES ('chora','rlog','/usr/bin/rlog')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app,config_name,config_value)VALUES ('chora','cvs','/usr/bin/cvs')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app,config_name,config_value)VALUES ('chora','adminname','Site Admin')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app,config_name,config_value)VALUES ('chora','adminemail','admin@localhost')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app,config_name,config_value)VALUES ('chora','shortloglength','75')");
?>
