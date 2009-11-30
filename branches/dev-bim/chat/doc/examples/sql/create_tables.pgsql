CREATE TABLE chat_channel ( 
 con serial, 
 name varchar(10) NOT NULL, 
 title char(50) 
); 

INSERT INTO chat_channel (name, title) values ('Main', 'WooHoo'); 
INSERT INTO chat_channel (name, title) values ('lounge', 'Lazy_Dayz'); 

CREATE TABLE chat_messages ( 
 con serial, 
 channel char(20) NOT NULL, 
 loginid varchar(25) NOT NULL, 
 message text, 
 messagetype int, 
 timesent int 
); 

CREATE TABLE chat_currentin ( 
 con serial, 
 loginid varchar(25) NOT NULL, 
 channel char(20), 
 lastmessage int 
); 

CREATE TABLE chat_privatechat ( 
 con serial, 
 user1 varchar(25) NOT NULL, 
 user2 varchar(25) NOT NULL, 
 sentby varchar(25), 
 message text, 
 messagetype int, 
 timesent int, 
 closed int 
); 