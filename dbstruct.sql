# Tables dumped 2004-01-01 14:29:12 +0100
# Created by CocoaMySQL (Copyright (c) 2002-2003 Lorenz Textor)
#
# Host: localhost   Database: rss
# ******************************

# Dump of table channels
# ------------------------------

DROP TABLE IF EXISTS `channels`;

CREATE TABLE `channels` (
  `id` bigint(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `siteurl` varchar(255) default NULL,
  `parent` tinyint(4) default '0',
  `descr` varchar(255) default NULL,
  `dateadded` datetime default NULL,
  `icon` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;



# Dump of table folders
# ------------------------------

DROP TABLE IF EXISTS `folders`;

CREATE TABLE `folders` (
  `id` tinyint(11) NOT NULL auto_increment,
  `name` varchar(127) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM;


INSERT INTO `folders` (`id`,`name`) VALUES ("0","");
update folders set id=0 where id=1;


# Dump of table item
# ------------------------------

DROP TABLE IF EXISTS `item`;

CREATE TABLE `item` (
  `id` bigint(16) NOT NULL auto_increment,
  `cid` bigint(11) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `description` text,
  `unread` tinyint(4) default '1',
  `pubdate` datetime default NULL,
  PRIMARY KEY  (`id`),
--  UNIQUE KEY `url` (`url`),
  KEY `url` (`url`),
  KEY `cid` (`cid`)
) TYPE=MyISAM;



