<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: default_records.inc.php 13837 2003-11-01 22:57:15Z skwashd $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	/* $Id: default_records.inc.php 13837 2003-11-01 22:57:15Z skwashd $ */

	/* Default FUDforum Data */
	$oProc->query("INSERT INTO phpgw_fud_cat (name,description,cat_opt,view_order) VALUES ('Test Category', ' - Just a test category', 1|2, 1)");
	$oProc->query("INSERT INTO phpgw_fud_forum (cat_id, name, date_created, max_attach_size, view_order) VALUES(1, 'TestForum', ".time().", 1024, 1)");
	$oProc->query("INSERT INTO phpgw_fud_fc_view (c, f) VALUES(1, 1)");
	$oProc->query("INSERT INTO phpgw_fud_groups (name, forum_id, groups_opt) VALUES('Global Anonymous Access', 0, 1|2|262144)");
	$oProc->query("INSERT INTO phpgw_fud_groups (name, forum_id, groups_opt) VALUES('Global Registered Access', 0, 1|2|4|8|128|256|512|1024|16384|32768|262144)");
	$oProc->query("INSERT INTO phpgw_fud_groups (name, forum_id, groups_opt) VALUES ('TestForum', 1, 1|2|4|8|16|32|64|128|256|512|1024|2048|4096|8192|16384|32768|262144)");
	$oProc->query("INSERT INTO phpgw_fud_group_resources (group_id, resource_id) VALUES(3, 1)");
	$oProc->query("INSERT INTO phpgw_fud_group_members (user_id, group_id, group_members_opt) VALUES (0, 3, 1|2|262144|65536)");
	$oProc->query("INSERT INTO phpgw_fud_group_members (user_id, group_id, group_members_opt) VALUES (2147483647, 3, 1|2|4|8|128|256|512|1024|16384|32768|262144|65536)");
	$oProc->query("INSERT INTO phpgw_fud_group_cache (user_id, resource_id, group_id, group_cache_opt) VALUES (0, 1, 3, 1|2|262144)");
	$oProc->query("INSERT INTO phpgw_fud_group_cache (user_id, resource_id, group_id, group_cache_opt) VALUES (2147483647, 1, 3, 1|2|4|8|128|256|512|1024|16384|32768|262144)");
	$oProc->query("INSERT INTO phpgw_fud_level (name,post_count) VALUES ('Senior Member',100)");
	$oProc->query("INSERT INTO phpgw_fud_level (name,post_count) VALUES ('Member',30)");
	$oProc->query("INSERT INTO phpgw_fud_level (name,post_count) VALUES ('Junior Member',0)");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('aiff','audio/x-aiff','AIFF File','sound.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('wav','audio/x-wav','Wave File','sound.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('midi','audio/midi','MIDI File','midi.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('au','audio/basic','Sun ulaw-Compressed Audio File','sound.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('mp3','audio/mpeg','MP3 File','sound.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('rm','audio/x-realaudio','Real Audio','real.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('gif','image/gif','GIF Image','image.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('ico','image/ico','Icon File','image.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('jpeg','image/jpeg','JPEG Image','image.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('jpg','image/jpeg','JPEG Image','image.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('tiff','image/tiff','TIFF Image','image.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('tif','image/tiff','TIFF Image','image.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('pict','image/x-pict','Macintosh PICT format','image.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('bmp','image/x-win-bmp','BMP Image','image.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('png','image/png','PNG Image','image.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('mpeg','video/mpeg','MPEG Movie','video.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('mpg','video/mpeg','MPEG Movie','video.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('qt','video/quicktime','QuickTime Movie','video.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('mov','video/quicktime','QuickTime Movie','video.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('vivo','video/vnd.vivo','VIVO Movie','video.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('avi','video/x-msvideo','Microsoft Video','video.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('gz','application/x-gzip','GZIP Archive','tgz.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('tar','application/x-tar','TAR Archive','tar.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('zip','application/zip','ZIP Archive','tgz.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('doc','application/msword','MS Word Document','word.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('dot','application/msword','MS Word Document','word.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('xls','application/vnd.ms-excel','MS Excel','excel.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('ppt','application/vnd.ms-powerpoint','MS PowerPoint','ppt.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('pdf','application/pdf','PDF Document','pdf.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('rtf','application/rtf','Rich Text Format','txt.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('htm','text/html','HTML Page','html.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('html','text/html','HTML Page','html.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('txt','text/plain','Plain Text','txt.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('ps','application/postscript','Postscript','postscript.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('swf','application/x-shockwave-flash','ShockWave Flash','flash.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('exe','application/application','Binary File','binary.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('bin','application/application','Binary File','binary.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('sh','application/sh','UNIX Shell Script','sh.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('hqx','application/mac-binhex40','Mac Binary','binary.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('','application/octet-stream','Unknown','unknown.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('pl','application/x-perl','Perl Script','source.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('pm','application/x-perl','Perl Module','source.gif')");
	$oProc->query("INSERT INTO phpgw_fud_mime (fl_ext,mime_hdr,descr,icon) VALUES('php','application/x-httpd-php','PHP Script','source.gif')");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_redface.gif','Embarassed',':blush:',1)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_razz.gif','Razz',':P~:-P~:razz:',2)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_mad.gif','Mad',':x~:-x~:mad:',3)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_lol.gif','Laughing',':lol:',4)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_cool.gif','Cool','8)~8-)~:cool:',5)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_confused.gif','Confused',':?~:-?~:???:',6)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_eek.gif','Shocked','8O~8-O~:shock:',7)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_surprised.gif','Surprised',':o~:-o~:eek:',8)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_sad.gif','Sad',':(~:-(~:sad:',9)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_smile.gif','Smile',':)~:-)~:smile:',10)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_biggrin.gif','Very Happy',':D~:-D~:grin:',11)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_cry.gif','Crying or Very Sad',':cry:~:((~:-((',12)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_evil.gif','Evil or Very Mad',':evil:',13)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_twisted.gif','Twisted Evil',':twisted:',14)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_rolleyes.gif','Rolling Eyes',':roll:',15)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_wink.gif','Wink',';)~;-)~:wink:',16)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_exclaim.gif','Exclamation',':!:',17)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_question.gif','Question',':?:',18)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_idea.gif','Idea',':idea:',19)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_arrow.gif','Arrow',':arrow:',20)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_neutral.gif','Neutral',':|~:-|~:neutral:',21)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_mrgreen.gif','Grin',':]~:-]~:brgin:',22)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_dead.gif','Dead','x(~:dead:',23)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_frown.gif','Frown',':frown:',24)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_nod.gif','Nod',':nod:',25)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_proud.gif','Proud',':proud:',26)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_smug.gif','Smug',':smug:',27)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_thumbsup.gif','Thumbs Up',':thumbup:',28)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_thumbdown.gif','Thumbs Down',':thumbdown:',29)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_uhoh.gif','Uh Oh',':uhoh:',30)");
	$oProc->query("INSERT INTO phpgw_fud_smiley (img, descr, code, vieworder) VALUES ('icon_yawn.gif','Bored',':yawn:',31)");
	$oProc->query("INSERT INTO phpgw_fud_stats_cache VALUES(0,0,0,0,0,0,0)");
	$oProc->query("INSERT INTO phpgw_fud_users (phpgw_id, login, alias, time_zone, theme, email, passwd, name, users_opt) VALUES(-1, 'Anonymous Coward', 'Anonymous Coward', 'America/Montreal', 1, 'dev@null', '1', 'Anonymous Coward', 1|4|16|32|128|256|512|2048|4096|8192|16384|262144|2097152|4194304)");

	if ($GLOBALS['phpgw_domain'][$GLOBALS['ConfigDomain']]['db_type'] == 'mysql') {
		$oProc->query("ALTER TABLE phpgw_fud_thread_view CHANGE pos pos INT NOT NULL AUTO_INCREMENT");
	}

	$path = dirname(realpath(__FILE__));
	if (!copy("$path/index.php", "$path/../index.php")) {
		$p = realpath("$path/../");
		echo '<font color="red">' 
			. lang("ERROR: Failed to copy '%1' to '%2'. Please copy this file manually!", "$path/index.php", $p)
			. '</font>';
	}
