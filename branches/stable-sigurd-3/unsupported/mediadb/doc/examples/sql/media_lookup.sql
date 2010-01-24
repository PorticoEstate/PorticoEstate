#
# Table structure for table 'phpgw_mediadb_lookup'
#
DROP TABLE IF EXISTS phpgw_mediadb_lookup;
CREATE TABLE phpgw_mediadb_lookup (
  lookup_id        int(11) DEFAULT '0' NOT NULL auto_increment,
  lookup_url       varchar(255),
  lookup_block     varchar(255),
  lookup_component varchar(255),
  lookup_field     varchar(25),
  cat_id           smallint(5) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (lookup_id)
);

# "[a-zA-Z0-9:/ \.]*" ALT="cover" (media image) 
