
CREATE TABLE phonelog_entry (
  pl_id            serial,
  pl_callfrom_id   int DEFAULT '0' NOT NULL,
  pl_callfrom_txt  varchar(255),
  pl_callfor       int DEFAULT '0' NOT NULL,
  pl_calldate      int,
  pl_status        int DEFAULT '0' NOT NULL,
  pl_desc_short    varchar(255),
  pl_desc_long     text,
  PRIMARY KEY (pl_id)
);

INSERT INTO applications VALUES ('phonelog','Phone Log',1,30,'phonelog_entry','0.8.1pre1');
