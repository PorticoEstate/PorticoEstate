CREATE TABLE phpgw_weather_admin (
  admin_gdlib_e    int DEFAULT '0' NOT NULL,
  admin_gdtype     int DEFAULT '0' NOT NULL,
  admin_imgsrc     int DEFAULT '0' NOT NULL,
  admin_remote_e   int DEFAULT '0' NOT NULL,
  admin_filesize   int DEFAULT '120000' NOT NULL
);

CREATE TABLE phpgw_weather (
  weather_id       serial,
  weather_owner    varchar(32) DEFAULT '' NOT NULL,
  weather_metar    varchar(255) DEFAULT '' NOT NULL,
  weather_links    varchar(255) DEFAULT '' NOT NULL,
  weather_title_e  int DEFAULT '0' NOT NULL,
  weather_observ_e int DEFAULT '0' NOT NULL,
  weather_foreca_e int DEFAULT '0' NOT NULL,
  weather_links_e  int DEFAULT '0' NOT NULL,
  weather_wunder_e int DEFAULT '0' NOT NULL,
  weather_fpage_e  int DEFAULT '0' NOT NULL,
  weather_template int DEFAULT '0' NOT NULL,
  weather_city     varchar(50) DEFAULT '' NOT NULL,
  weather_country  varchar(50) DEFAULT '' NOT NULL,
  weather_gstation varchar(25) DEFAULT '' NOT NULL,
  weather_sticker  int DEFAULT '0' NOT NULL,
  weather_tmetar   varchar(25) DEFAULT '' NOT NULL,
  weather_tsize    int DEFAULT '0' NOT NULL,
  weather_fpmetar  varchar(25) DEFAULT '' NOT NULL,
  weather_fpsize   int DEFAULT '1' NOT NULL,
  state_id         int DEFAULT '0' NOT NULL
);

CREATE TABLE phpgw_weather_links (
  links_id         serial,
  links_name       varchar(35) DEFAULT '' NOT NULL,
  links_timetag    int DEFAULT '0' NOT NULL,
  links_refresh    int DEFAULT '0' NOT NULL,
  links_linkurl    varchar(255) DEFAULT '' NOT NULL,
  links_baseurl    varchar(255) DEFAULT '' NOT NULL,
  links_parseurl   varchar(255) DEFAULT '' NOT NULL,
  links_parseexpr  varchar(255) DEFAULT '' NOT NULL,
  links_imageurl   varchar(255) DEFAULT '' NOT NULL,
  links_comment    varchar(255) DEFAULT '' NOT NULL,
  links_type       varchar(255) DEFAULT '' NOT NULL
);

CREATE TABLE phpgw_weather_metar (
  metar_id         serial,
  metar_weather    varchar(255) DEFAULT '' NOT NULL,
  metar_timestamp  int8,
  metar_station    varchar(4) DEFAULT '' NOT NULL,
  metar_city       varchar(128) DEFAULT '' NOT NULL,
  metar_forecast   varchar(6) DEFAULT '' NOT NULL,
  metar_map        char(3) DEFAULT '' NOT NULL,
  region_id        int DEFAULT '0' NOT NULL,
  UNIQUE (metar_station)
);

CREATE TABLE phpgw_weather_region (
  region_id        serial,
  region_name      varchar(50) DEFAULT '' NOT NULL
);

CREATE TABLE phpgw_us_states (
  state_id         serial,
  state_code       char(2) DEFAULT '' NOT NULL,
  state_name       varchar(50) DEFAULT '' NOT NULL
);
