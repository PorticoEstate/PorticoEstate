<?php
    /***************************************************************************\
    * phpGroupWare - Web Content Manager
    * http://www.phpgroupware.org                                               *
    * -----------------------------------------------                           *
    * This program is free software; you can redistribute it and/or modify it   *
    * under the terms of the GNU General Public License as published by the     *
    * Free Software Foundation; either version 2 of the License, or (at your    *
    * option) any later version.                                                *
    \***************************************************************************/
	/* $Id$ */

	function about_app($tpl,$handle)
	{
		$s = '<b>' . lang('Web Site Manager') . '</b><p>' .nl2br(' 
<u>Overview</u>
This program will generate a dynamic web site with discrete sections that various phpGroupWare users may edit, if the administrator gives them permission to do so.  In effect, the generated website can have sections which independent departments are in charge of maintaining.  The site administrator can choose a theme and create headers, footers, and sidebars to enforce a sitewide look and feel.  Site sections can be viewable public (viewable by anonymous users) or private (viewable by specified users and groups only).

<u>Background</u>
Team 10 in the UC Irvine Systems Design Course, ICS 125, chose this as their project.  Seek3r served as the project\'s "customer" and the team wrote extensive requirements and design documents followed by the actual coding of the project.  The course is ten weeks long, but coding doesn\'t start until part-way through week 6, so version 1.0 of sitemgr was programmed in an intensive 3 weeks.

<u>Credits</u>
ICS 125 Team 10:

Tina Alinaghian (tina -AT- checkyour6.net)
Austin Lee (anhjah -AT- hotmail.com)
Siu Leung (rurouni_master -AT- hotmail.com)
Fang Ming Lo (flo -AT- uci.edu)
Patrick Walsh (mr_e -AT- phpgroupware.org)

Professor:
Hadar Ziv (profziv -AT- aol.com)

TA:
Arijit Ghosh (arijitg -AT- uci.edu)
');
		return $s;
	}
?>
