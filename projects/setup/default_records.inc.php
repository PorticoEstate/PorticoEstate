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

	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type,event_extra) values('hours limit','percent',90)",__LINE__,__FILE__);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type,event_extra) values('budget limit','percent',90)",__LINE__,__FILE__);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type,event_extra) values('project date due','limits',7)",__LINE__,__FILE__);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type,event_extra) values('milestone date due','limits',7)",__LINE__,__FILE__);

	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type) values('assignment to project','assignment')",__LINE__,__FILE__);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type) values('assignment to role','assignment')",__LINE__,__FILE__);

	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type) values('project dependencies','dependencies')",__LINE__,__FILE__);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type) values('changes of project data','dependencies')",__LINE__,__FILE__);

/*
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_locmap (p_group,p_location,p_locprojnum) values(103,1,'910186')",__LINE__,__FILE__); // Hannover
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_locmap (p_group,p_location,p_locprojnum) values(921,2,'910113')",__LINE__,__FILE__); // Berlin
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_locmap (p_group,p_location,p_locprojnum) values(123,3,'910166')",__LINE__,__FILE__); // Frankfurt / Böblingen 739
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_locmap (p_group,p_location,p_locprojnum) values(719,4,'910360')",__LINE__,__FILE__); // Düsseldorf
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_locmap (p_group,p_location,p_locprojnum) values(224,5,'910476')",__LINE__,__FILE__); // München
	// $GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_locmap (p_group,p_location,p_locprojnum) values(840,6,'')",__LINE__,__FILE__); // Hamburg
*/
/*
Hannover D04 01 910186
Berlin D04 01 910113
Frankfurt/Böblingen D04 01 910166
Düsseldorf D04 01 910360
München D04 01 910476
*/
