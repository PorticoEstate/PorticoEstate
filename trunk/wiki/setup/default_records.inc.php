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


	$time = time();
	$oProc->query("INSERT INTO phpgw_wiki_pages (title,version,time,supercede,username,author,body,comment) VALUES ('RecentChanges',1,$time,$time,'setup','localhost','[[! *]]\n','')");
	$oProc->query("INSERT INTO phpgw_wiki_pages (title,version,time,supercede,username,author,body,comment) VALUES ('phpGroupWare',1,$time,$time,'setup','localhost','Welcome to " . $oProc->m_odb->db_addslashes("'''Wiki'''") . " - the phpGroupWare Version of " . $oProc->m_odb->db_addslashes("'''WikkiTikkiTavi'''") . ". Wikis are a revolutionary new form of collaboration and online community.\n\n" . $oProc->m_odb->db_addslashes("'''phpGroupWare'''") . " is the groupware suite you are useing right now. For further information see http://www.phpgroupware.org','')");
	$oProc->query("INSERT INTO phpgw_wiki_pages (title,version,time,supercede,username,author,body,comment) VALUES ('WikkiTikkiTavi',1,$time,$time,'setup','localhost','= WikkiTikkiTavi =\n\nWikkiTikkiTavi is the original version this documentation system.\nTheir documentation is usable for the ((phpGroupWare)) " . $oProc->m_odb->db_addslashes("'''Wiki'''") . " too.\n\nThe documentation of WikkiTikkiTavi is online availible at: http://tavi.sourceforge.net\nYou can learn about Wiki formatting at http://tavi.sourceforge.net/FormattingRules\n','')");
