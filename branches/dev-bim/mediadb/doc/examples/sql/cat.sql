#
# Table structure for table 'phpgw_mediadb_type'
#
DROP TABLE IF EXISTS phpgw_mediadb_cat;
CREATE TABLE phpgw_mediadb_cat(
  cat_id         smallint(5) unsigned DEFAULT '0' NOT NULL auto_increment,
  cat_name       varchar(50),
  cat_enabled    smallint(5) unsigned DEFAULT '1' NOT NULL,
  cat_fname      varchar(255),
  cat_fenabled   varchar(255),
  cat_fwidth     varchar(255),
  cat_fsort      varchar(255),
  PRIMARY KEY (cat_id)
);

LOCK TABLES phpgw_mediadb_cat WRITE;
INSERT INTO phpgw_mediadb_cat VALUES (1,'default',2,
"phpgw_mediadb_data.data_title,phpgw_mediadb_artist.artist_name,phpgw_mediadb_format.format_desc,phpgw_mediadb_data.data_date,phpgw_mediadb.media_idate,phpgw_mediadb_genre.genre_desc,phpgw_mediadb_rating.rating_desc,phpgw_mediadb_data.data_score,phpgw_mediadb.media_owner,phpgw_mediadb_data.data_comments,imdb,edit,avail",
"1,1,1,1,0,1,1,1,1,0,2,1,1",
"20,15,10,5,10,10,5,5,10,16,4,4,1",
"1,0,0,0,0,0,0,0,0,0,0,0,0");
INSERT INTO phpgw_mediadb_cat VALUES (2,'archives',1,
"title,admin,media,year,date,genre,rated,scoring,owner,comment,imdb,edit,avail",
"1,1,1,0,1,0,0,0,1,1,2,1,1",
"20,15,10,5,10,10,5,5,10,16,4,4,1",
"1,0,0,0,0,0,0,0,0,0,0,0,0");
INSERT INTO phpgw_mediadb_cat VALUES (3,'books',1,
"title,author,media,year,date,genre,rated,scoring,owner,comment,imdb,edit,avail",
"1,1,1,1,0,1,1,1,1,0,2,1,1",
"20,15,10,5,10,10,5,5,10,16,4,4,1",
"1,0,0,0,0,0,0,0,0,0,0,0,0");
INSERT INTO phpgw_mediadb_cat VALUES (4,'games',1,
"title,developer,media,year,date,genre,rated,scoring,owner,comment,imdb,edit,avail",
"1,1,1,1,0,1,1,1,1,0,2,1,1",
"20,15,10,5,10,10,5,5,10,16,4,4,1",
"1,0,0,0,0,0,0,0,0,0,0,0,0");
INSERT INTO phpgw_mediadb_cat VALUES (5,'movies',1,
"title,actor,media,year,date,genre,rated,scoring,owner,comment,imdb,edit,avail",
"1,1,1,1,0,1,1,1,1,0,1,1,1",
"20,15,5,5,10,10,5,5,10,16,4,4,1",
"1,0,0,0,0,0,0,0,0,0,0,0,0");
INSERT INTO phpgw_mediadb_cat VALUES (6,'music',1,
"title,artist,media,year,date,genre,rated,scoring,owner,comment,imdb,edit,avail",
"1,1,1,1,0,1,1,1,1,0,2,1,1",
"20,15,10,5,10,10,5,5,10,16,4,4,1",
"1,0,0,0,0,0,0,0,0,0,0,0,0");
UNLOCK TABLES;
