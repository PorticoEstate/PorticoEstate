CREATE TABLE phpgw_eldaptir_servers (
   id serial,
   name varchar(32),
   type  varchar(32),
   basedn varchar(64),
   rootdn varchar(64),
   rootpw varchar(64),
   is_default int DEFAULT '0' NOT NULL,
   PRIMARY KEY (id)
);
