-- phpMyAdmin SQL Dump
-- version 2.6.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: May 11, 2005 at 02:34 AM
-- Server version: 4.0.24
-- PHP Version: 4.3.10-13
-- 
-- Database: `phpgw16`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_communik8r_acct_types`
-- 

CREATE TABLE `phpgw_communik8r_acct_types` (
  `acct_type_id` int(11) NOT NULL auto_increment,
  `type_name` varchar(10) NOT NULL default '',
  `type_descr` varchar(100) NOT NULL default '',
  `handler` varchar(100) NOT NULL default '',
  `is_active` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`acct_type_id`),
  KEY `is_active` (`is_active`)
) TYPE=MyISAM COMMENT='phpGroupWare communik8r account types' AUTO_INCREMENT=5 ;


INSERT INTO `phpgw_communik8r_acct_types` VALUES (1, 'imap', 'IMAP Mail', 'email', 1);
INSERT INTO `phpgw_communik8r_acct_types` VALUES (2, 'pop3', 'POP3 Mail', 'email', 1);
INSERT INTO `phpgw_communik8r_acct_types` VALUES (3, 'jabber', 'Jabber IM', 'jabber', 1);
INSERT INTO `phpgw_communik8r_acct_types` VALUES (4, 'sms', 'SMS Text Message', 'sms', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_communik8r_accts`
-- 

CREATE TABLE `phpgw_communik8r_accts` (
  `acct_id` int(11) NOT NULL auto_increment,
  `owner_id` int(11) NOT NULL default '0',
  `acct_name` varchar(100) NOT NULL default '',
  `display_name` varchar(100) NOT NULL default '',
  `acct_uri` varchar(100) NOT NULL default '',
  `username` varchar(100) NOT NULL default '',
  `password` varchar(50) NOT NULL default '',
  `server` varchar(100) NOT NULL default '',
  `port` int(11) NOT NULL default '0',
  `ssl` smallint(6) NOT NULL default '0',
  `tls` smallint(6) NOT NULL default '0',
  `acct_type_id` int(11) NOT NULL default '0',
  `acct_options` text,
  PRIMARY KEY  (`acct_id`),
  KEY `owner_id` (`owner_id`),
  KEY `acct_name` (`acct_name`),
  KEY `acct_type_id` (`acct_type_id`)
) TYPE=MyISAM COMMENT='phpGroupWare communik8r accounts' ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_communik8r_email_headers`
-- 

CREATE TABLE `phpgw_communik8r_email_headers` (
  `header_key` varchar(100) NOT NULL default '',
  `msg_id` int(11) NOT NULL default '0',
  `seq_no` int(11) NOT NULL default '0',
  `header_val` text NOT NULL,
  PRIMARY KEY  (`header_key`,`msg_id`,`seq_no`),
  KEY `msg_id` (`msg_id`)
) TYPE=MyISAM PACK_KEYS=0;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_communik8r_email_mboxes`
-- 

CREATE TABLE `phpgw_communik8r_email_mboxes` (
  `mbox_id` int(11) NOT NULL auto_increment,
  `mbox_name` varchar(250) NOT NULL default '',
  `seperator` char(1) NOT NULL default '.',
  `acct_id` int(11) NOT NULL default '0',
  `subscribed` smallint(6) NOT NULL default '1',
  `uidnext` int(11) NOT NULL default '0',
  `uidvalidity` int(11) NOT NULL default '0',
  `lastmod` int(11) default '0',
  PRIMARY KEY  (`mbox_id`),
  KEY `acct_id` (`acct_id`),
  KEY `subscribed` (`subscribed`)
) TYPE=MyISAM PACK_KEYS=0 COMMENT='communik8r email mailboxes' ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_communik8r_email_msgs`
-- 

CREATE TABLE `phpgw_communik8r_email_msgs` (
  `msg_id` int(11) NOT NULL auto_increment,
  `mbox_id` int(11) NOT NULL default '0',
  `msg_uid` int(11) NOT NULL default '0',
  `msg_uidl` varchar(75) NOT NULL default '',
  `subject` varchar(100) NOT NULL default '',
  `sender` varchar(100) NOT NULL default '',
  `date_sent` int(11) NOT NULL default '0',
  `msg_size` int(11) NOT NULL default '0',
  `flag_seen` smallint(6) NOT NULL default '0',
  `flag_answered` smallint(6) NOT NULL default '0',
  `flag_deleted` smallint(6) NOT NULL default '0',
  `flag_flagged` smallint(6) NOT NULL default '0',
  `flag_draft` smallint(6) NOT NULL default '0',
  `structure` longtext NOT NULL,
  PRIMARY KEY  (`msg_id`),
  KEY `mailbox_id` (`mbox_id`),
  KEY `uid` (`msg_uid`),
  KEY `uidl` (`msg_uidl`),
  KEY `flag_draft` (`flag_draft`),
  KEY `flag_flagged` (`flag_flagged`),
  KEY `flag_answered` (`flag_answered`),
  KEY `flag_read` (`flag_seen`),
  KEY `date_sent` (`date_sent`),
  KEY `subject` (`subject`),
  KEY `flag_deleted` (`flag_deleted`)
) TYPE=MyISAM PACK_KEYS=0 COMMENT='communik8r email messages' ;
        

