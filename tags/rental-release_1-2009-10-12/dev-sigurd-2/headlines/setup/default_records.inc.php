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

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) VALUES('Slashdot','http://slashdot.org','/slashdot.rdf',0,'rdf',60,20)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) VALUES('Freshmeat','http://freshmeat.net','/backend/fm.rdf',0,'fm',60,20)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) VALUES('Linux&nbsp;Today','http://linuxtoday.com','/backend/linuxtoday.xml',0,'lt',60,20)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) VALUES('Linux&nbsp;Game&nbsp;Tome','http://happypenguin.org','/html/news.rdf',0,'rdf',60,20)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) VALUES('linux-at-work.de','http://linux-at-work.de','/backend.php',0,'rdf',60,20)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) VALUES('Segfault','http://segfault.org','/stories.xml',0,'sf',60,20)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) VALUES('KDE&nbsp;News','http://www.kde.org','/news/kdenews.rdf',0,'rdf',60,20)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) VALUES('Gnome&nbsp;News','http://news.gnome.org','/gnome-news/rdf',0,'rdf',60,20)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) VALUES('Gimp&nbsp;News','http://www.xach.com','/gimp/news/channel.rdf',0,'rdf-chan',60,20)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) VALUES('Mozilla','http://www.mozilla.org','/news.rdf',0,'rdf-chan',60,20)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) VALUES('MozillaZine','http://www.mozillazine.org','/contents.rdf',0,'rdf',60,20)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) VALUES('phpgw.de - deutsche PHPGroupware Seite','http://phpgw.de','/backend.php',0,'rdf',60,20)");
?>
