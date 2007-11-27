<?php
/**************************************************************************\
 * phpGroupWare - Antispam                                                  *
 * http://www.phpgroupware.org                                              *
 * This application written by:                                             *
 *                             Marco Andriolo-Stagno <stagno@prosa.it>      *
 *                             PROSA <http://www.prosa.it>                  *
 * -------------------------------------------------------------------------*
 * Funding for this program was provided by http://www.seeweb.com           *
 * -------------------------------------------------------------------------*
 *  This program is free software; you can redistribute it and/or modify it *
 *  under the terms of the GNU General Public License as published by the   *
 *  Free Software Foundation; either version 2 of the License, or (at your  *
 *  option) any later version.                                              *
 \**************************************************************************/

      /* $Id: default_records.inc.php 11580 2002-11-26 17:57:08Z ceb $ */

$oProc->query("INSERT INTO phpgw_antispam (username,preference,value,prefid) VALUES ('@GLOBAL','required_hits','5.0','')");
$oProc->query("INSERT INTO phpgw_antispam (username,preference,value,prefid) VALUES ('@GLOBAL','rewrite_subject','1','')");
$oProc->query("INSERT INTO phpgw_antispam (username,preference,value,prefid) VALUES ('@GLOBAL','defang_mime','0','')");
$oProc->query("INSERT INTO phpgw_antispam (username,preference,value,prefid) VALUES ('@GLOBAL','use_terse_report','1','')");
$oProc->query("INSERT INTO phpgw_antispam (username,preference,value,prefid) VALUES ('@GLOBAL','report_header','0','')");

