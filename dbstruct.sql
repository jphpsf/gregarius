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
  mode int(16) NOT NULL default '1',
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
  `author` varchar(255) default NULL,
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
  `export_` varchar(127) default NULL,
  PRIMARY KEY  (`key_`)
) TYPE=MyISAM;

INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.output.cachedir","/tmp/magpierss","/tmp/magpierss","string","Where should magpie store its temporary files? (Apache needs write permissions on this dir.)","MAGPIE_CACHE_DIR");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.output.encoding","UTF-8","UTF-8","string","Output encoding for the PHP XML parser.","MAGPIE_OUTPUT_ENCODING");
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.output.itemsinchannelview","10","10","num","Number of read items shown on for a single channel.",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.output.showfavicons","true","true","boolean","Display the favicon for the channels that have one. Due to a IE bug, some icons do not render correctly. You can either change the URL to the icon in the admin screen, or turn the display of favicons off globally here.",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.output.usemodrewrite","true","true","boolean","Make use of apache\'s mod_rewrite module to return sexy urls. Turn this off if your host doesn\'t allow you to change this apache setting.",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.config.dateformat","F jS, Y, g:ia T","F jS, Y, g:ia T","string","Format to use when displaying dates. See here for help on the format: http://ch.php.net/manual/en/function.date.php \n\nNote that direct access to a given feed\'s month and day archives more or less depends on the fact that this date format contains the  \"F\" (Month) and \"jS\" (day) elements in this form. So feel free to change the order of the elements, but better leave those two tokens in :)",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.config.showdevloglink","false","false","boolean","Show a link to the gregarius devlog. This is mainly useful on the actual\n live gregarius site (http://gregarius.net/). You can safely set this to \'false\'\n if you don\'t want to display a link back.",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.meta.debug","false","false","boolean"," When in debug mode some extra debug info is shown and the error \n reporting is a bit more verbose.",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.output.compression","true","true","boolean","This variable turns output compression on and off. Output compression is handled by most browsers.",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.output.channelcollapse","true","true","boolean","Allow collapsing of channels on the main page. ",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.output.usepermalinks","true","true","boolean","Display a permalink icon and allow linking a given item directly.",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.config.markreadonupdate","false","false","boolean","Mark all old unread feeds as read when updating if new unread feeds\n are found.",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.output.lang","en,es,fr,dk,it,pt_BR,se,0","en,es,fr,dk,it,pt_BR,se,0","enum","Language pack to use.",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.config.absoluteordering","true","true","boolean","Allow ordering of channels and folders in the admin section. \n If false, channels and folders will be organized by their titles",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.config.robotsmeta","index,follow","index,follow","string","How should spiders crawl us?\n (see http://www.robotstxt.org/wc/meta-user.html for more info).",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.config.serverpush","true","true","boolean","Use server push on update.php for a more user-friendly experience.\n This is only supported by Mozilla browsers (Netscape, Mozilla, FireFox,...)\n and Opera. These browsers will be autodetected.\n If you\'re not using one of these (you should) you can as well turn this off.",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.config.refreshafter","45","45","num","If this option is set the feeds will be refreshed after x minutes of inactivity. Please respect the feed providers by not setting this value to anything lower than thirty minutes. \n\nSet this variable to 0 turn this option off.",NULL);
INSERT INTO `config` (`key_`,`value_`,`default_`,`type_`,`desc_`,`export_`) VALUES ("rss.input.allowed","a:18:{s:1:\"a\";a:2:{s:4:\"href\";i:1;s:5:\"title\";i:1;}s:1:\"b\";a:0:{}s:10:\"blockquote\";a:0:{}s:2:\"br\";a:0:{}s:4:\"code\";a:0:{}s:2:\"em\";a:0:{}s:1:\"i\";a:0:{}s:3:\"img\";a:2:{s:3:\"src\";i:1;s:3:\"alt\";i:1;}s:2:\"li\";a:0:{}s:2:\"ol\";a:0:{}s:1:\"p\";a:0:{}s:3:\"pre\";a:0:{}s:5:\"table\";a:0:{}s:2:\"td\";a:0:{}s:2:\"th\";a:0:{}s:2:\"tr\";a:0:{}s:2:\"tt\";a:0:{}s:2:\"ul\";a:0:{}}","a:18:{s:1:\"a\";a:2:{s:4:\"href\";i:1;s:5:\"title\";i:1;}s:1:\"b\";a:0:{}s:10:\"blockquote\";a:0:{}s:2:\"br\";a:0:{}s:4:\"code\";a:0:{}s:2:\"em\";a:0:{}s:1:\"i\";a:0:{}s:3:\"img\";a:2:{s:3:\"src\";i:1;s:3:\"alt\";i:1;}s:2:\"li\";a:0:{}s:2:\"ol\";a:0:{}s:1:\"p\";a:0:{}s:3:\"pre\";a:0:{}s:5:\"table\";a:0:{}s:2:\"td\";a:0:{}s:2:\"th\";a:0:{}s:2:\"tr\";a:0:{}s:2:\"tt\";a:0:{}s:2:\"ul\";a:0:{}}","array","This variable controls input filtering. HTML tags and their attributes, which are not in this list, get filtered out when new RSS items are imported.",NULL);
insert into config (key_,value_,default_,type_,desc_,export_) values ('rss.output.showfeedmeta','false','false','boolean','Display meta-information (like a web- and rss/rdf/xml url) about each feed in the feed side-column.',NULL);
insert into config (key_,value_,default_,type_,desc_,export_) values ("rss.input.tags.delicious",'false','false','boolean','Look up tag suggestions on del.icio.us when editing tags.',NULL);
insert into config (key_,value_,default_,type_,desc_,export_) values ("rss.output.noreaditems",'false','false','boolean','Show unread items only on the frontpage.',NULL);
insert into config (key_,value_,default_,type_,desc_,export_) values ("rss.output.theme",'default','default','string','The theme to use. Themes are subdirectories of themes/ which should contain at least the following elements:<ul><li>css/layout.css</li><li>css/look.css</li><ul>',NULL);
insert into config (key_,value_,default_,type_,desc_,export_) values ("rss.output.cachecontrol",'false','false','boolean','If true, Gregarius will negotiate with the browser and check whether it should get a fresh document or not.',NULL);
insert into config (key_,value_,default_,type_,desc_,export_) values ("rss.config.plugins",'a:1:{i:0;s:13:"urlfilter.php";}','a:1:{i:0;s:13:"urlfilter.php";}','array','Plugins are third-party scripts that offer extended functionalities.',NULL);
insert into config (key_,value_,default_,type_,desc_,export_) values ("rss.input.allowupdates",'true','true','boolean','Allow Gregarius to look for updates in existing items.',NULL);
insert into config (key_,value_,default_,type_,desc_,export_) values ("rss.output.titleunreadcnt",'false','false','boolean','Display unread count in the document title.',NULL);
insert into config (key_,value_,default_,type_,desc_,export_) values ("rss.config.tzoffset",'0','0','num','Timezone offset, in hours, between your local time and server time. Valid range: "-12" through "12"',NULL);
insert into config (key_,value_,default_,type_,desc_,export_) values ("rss.output.numitemsonpage","100","100","num","Maximum number of items displayed on the main page. Set this variable to 0 to turn this option off.",NULL);
insert into config (key_,value_,default_,type_,desc_,export_) values ("rss.config.feedgrouping",'false','false','boolean',"When true, Gregarius groups unread items per feed and sorts the feeds according to the <code>rss.config.absoluteordering</code> configuration switch. When false, unread items are not grouped by feed, but are sorted by date instead.",NULL);
insert into config (key_,value_,default_,type_,desc_,export_) values ("rss.config.datedesc",'true','true','boolean',"When true, Gregarius displays newer items first. If false, Gregarius will display older items first.",NULL);
insert into config (key_,value_,default_,type_,desc_,export_) values ("rss.config.autologout",'false','false','boolean','When true, Gregarius will automatically remove the "admin cookie" when the browser window is closed, effectively logging you out.',NULL);

DROP TABLE IF EXISTS `tag`;

CREATE TABLE tag (
 id bigint(16) NOT NULL auto_increment,
 tag varchar(63) NOT NULL default '',                    
 PRIMARY KEY  (id),
 UNIQUE KEY tag (tag),
 KEY id (id)
) type=MyISAM;                      

DROP TABLE IF EXISTS `metatag`;

CREATE TABLE metatag (
 fid bigint(16) NOT NULL default '0',                    
 tid bigint(16) NOT NULL default '0', 
 ttype enum('item','folder','channel') NOT NULL default 'item', 
 tdate datetime NULL,
 KEY fid (fid), 
 KEY tid (tid),
 KEY ttype (ttype)
) type=MyISAM;                                 


