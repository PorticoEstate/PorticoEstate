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

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_mediadb_cat VALUES (1,'default',2,
		'media_data.data_title,media_artist.artist_name,media_format.format_desc,media_data.data_date,media.media_idate,media_genre.genre_desc,media_rating.rating_desc,media_data.data_score,media.media_owner,media_data.data_comments,imdb,edit,avail',
		'1,1,1,1,0,1,1,1,1,0,2,1,1',
		'20,15,10,5,10,10,5,5,10,16,4,4,1',
		'1,0,0,0,0,0,0,0,0,0,0,0,0')"
	);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_mediadb_cat VALUES (2,'archives',1,
		'title,admin,media,year,date,genre,rated,scoring,owner,comment,imdb,edit,avail',
		'1,1,1,0,1,0,0,0,1,1,2,1,1',
		'20,15,10,5,10,10,5,5,10,16,4,4,1',
		'1,0,0,0,0,0,0,0,0,0,0,0,0')"
	);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_mediadb_cat VALUES (3,'books',1,
		'title,author,media,year,date,genre,rated,scoring,owner,comment,imdb,edit,avail',
		'1,1,1,1,0,1,1,1,1,0,2,1,1',
		'20,15,10,5,10,10,5,5,10,16,4,4,1',
		'1,0,0,0,0,0,0,0,0,0,0,0,0')"
	);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_mediadb_cat VALUES (4,'games',1,
		'title,developer,media,year,date,genre,rated,scoring,owner,comment,imdb,edit,avail',
		'1,1,1,1,0,1,1,1,1,0,2,1,1',
		'20,15,10,5,10,10,5,5,10,16,4,4,1',
		'1,0,0,0,0,0,0,0,0,0,0,0,0')"
	);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_mediadb_cat VALUES (5,'movies',1,
		'title,actor,media,year,date,genre,rated,scoring,owner,comment,imdb,edit,avail',
		'1,1,1,1,0,1,1,1,1,0,1,1,1',
		'20,15,5,5,10,10,5,5,10,16,4,4,1',
		'1,0,0,0,0,0,0,0,0,0,0,0,0')"
	);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_mediadb_cat VALUES (6,'music',1,
		'title,artist,media,year,date,genre,rated,scoring,owner,comment,imdb,edit,avail',
		'1,1,1,1,0,1,1,1,1,0,2,1,1',
		'20,15,10,5,10,10,5,5,10,16,4,4,1',
		'1,0,0,0,0,0,0,0,0,0,0,0,0')"
	);
