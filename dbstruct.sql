# Tables dumped 2004-01-01 14:29:12 +0100
# Created by CocoaMySQL (Copyright (c) 2002-2003 Lorenz Textor)
#
# ******************************

# Dump of table channels
# ------------------------------

DROP TABLE IF EXISTS `channels`;

CREATE TABLE channels (
  id bigint(11) NOT NULL auto_increment,
  title varchar(255) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  siteurl varchar(255) default NULL,
  parent tinyint(4) default '0',
  descr varchar(255) default NULL,
  dateadded datetime default NULL,
  icon varchar(255) default NULL,
  position int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
		    

# Dump of table folders
# ------------------------------

DROP TABLE IF EXISTS `folders`;

CREATE TABLE `folders` (
  `id` tinyint(11) NOT NULL auto_increment,
  `name` varchar(127) NOT NULL default '',
  position int(11) NOT NULL default '0',    
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM;


INSERT INTO `folders` (`id`,`name`) VALUES ("0","");

# *Sigh*
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
  KEY `url` (`url`),
  KEY `cid` (`cid`)
) TYPE=MyISAM;



# Dump of table config
# ------------------------------

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `key_` varchar(127) NOT NULL default '',
  `value_` text NOT NULL,
  `default_` text NOT NULL,
  `type_` enum('string','num','boolean','array','enum') NOT NULL default 'string',
  `desc_` text,
  `export_` tinyint(4) default '0',
  PRIMARY KEY  (`key_`)
) TYPE=MyISAM;

-- default config
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("MAGPIE_CACHE_DIR","/tmp/magpierss","/tmp/magpierss","string","Where should magpie store its temporary files? (Apache needs write permissions on this dir.)","1");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("MAGPIE_OUTPUT_ENCODING","UTF-8","UTF-8","string","Output encoding for the PHP XML parser.","1");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("ITEMS_ON_CHANNELVIEW","10","10","num","Number of items shown on for a single channel","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("USE_FAVICONS","true","true","boolean","Display the favicon for the channels that have one. Due to a IE bug, some icons do not render correctly on in the admin screen, or turn the favicon displaying offthis browser. You can either change the URL to the icon in the admin screen, or turn the favicon displaying off globally here.","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("USE_MODREWRITE","true","true","boolean","If this option is set the feeds will be refreshed after x minutes of inactivity. Please respect the feed providers by not setting this value to anything lower than thirty minutes. Comment the line to turn this feature off.","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("DATE_FORMAT","F jS, Y, g:ia T","F jS, Y, g:ia T","string","Format to use when displaying dates. See here for help on the format: http://ch.php.net/manual/en/function.date.php Note that direct access to a given feed\'s month and day archives more\n or less depends on the fact that this date format contains the \n \"F\" (Month) and \"jS\" (day) elements in this form. So feel free to change\n the order of the elements, but better leave those two tokens in :)","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("SHOW_DEVLOG_LINK","true","true","boolean","Show a link to the gregarius devlog. This is mainly useful on the actual\n live gregarius site (http://gregarius.net/).You can safely set this to \'false\'\n if you don\'t want to display a link back.","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("_DEBUG_","false","false","boolean"," When in debug mode some extra debug info is shown and the error \n reporting is a bit more verbose  ","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("OUTPUT_COMPRESSION","true","true","boolean","Output compression is handled by most browsers.","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("ALLOW_CHANNEL_COLLAPSE","true","true","boolean","Allow collpasing of channels on the main page. ","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("USE_PERMALINKS","true","true","boolean","Display a permalink icon and allow linking a given item directly","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("MARK_READ_ON_UPDATE","true","true","boolean","Mark all old unread feeds as read when updating, if new unread feeds\n are found ","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("LANG","en,fr,0","en,fr,0","enum","Language pack to use. (As of today \'en\' and \'fr\' ar available)","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("ABSOLUTE_ORDERING","true","true","boolean","Allow ordering of channels in the admin. \n (Uses channel title instead)","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("ROBOTS_META","index,follow","index,follow","string","How should spiders crawl us?\n (see http://www.robotstxt.org/wc/meta-user.html for more info)","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("DO_SERVER_PUSH","true","true","boolean","Use server push on update.php for a more user-friendly experience.\n This is only supported by Mozilla browser (Netscape, Mozilla, FireFox,...)\n and Opera. These brwosers will be autodetected.\n If you\'re not using one of these (you should) you can as well turn this off.","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("RELOAD_AFTER","45","45","num","Reload after (minutes)","0");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("KSES_ALLOWED_TAGS","a:17:{s:3:\"img\";a:2:{s:3:\"src\";i:1;s:3:\"alt\";i:1;}s:1:\"b\";a:0:{}s:1:\"i\";a:0:{}s:1:\"a\";a:2:{s:4:\"href\";i:1;s:5:\"title\";i:1;}s:2:\"br\";a:0:{}s:1:\"p\";a:0:{}s:10:\"blockquote\";a:0:{}s:2:\"ol\";a:0:{}s:2:\"ul\";a:0:{}s:2:\"li\";a:0:{}s:2:\"tt\";a:0:{}s:4:\"code\";a:0:{}s:3:\"pre\";a:0:{}s:5:\"table\";a:0:{}s:2:\"tr\";a:0:{}s:2:\"td\";a:0:{}s:2:\"th\";a:0:{}}","a:17:{s:3:\"img\";a:2:{s:3:\"src\";i:1;s:3:\"alt\";i:1;}s:1:\"b\";a:0:{}s:1:\"i\";a:0:{}s:1:\"a\";a:2:{s:4:\"href\";i:1;s:5:\"title\";i:1;}s:2:\"br\";a:0:{}s:1:\"p\";a:0:{}s:10:\"blockquote\";a:0:{}s:2:\"ol\";a:0:{}s:2:\"ul\";a:0:{}s:2:\"li\";a:0:{}s:2:\"tt\";a:0:{}s:4:\"code\";a:0:{}s:3:\"pre\";a:0:{}s:5:\"table\";a:0:{}s:2:\"tr\";a:0:{}s:2:\"td\";a:0:{}s:2:\"th\";a:0:{}}","array","Allowed kses tags","0");


