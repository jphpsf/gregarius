<?php
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2006 Marco Bonetti
#
###############################################################################
# This program is free software and open source software; you can redistribute
# it and/or modify it under the terms of the GNU General Public License as
# published by the Free Software Foundation; either version 2 of the License,
# or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful, but WITHOUT
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:      mbonetti at gmail dot com
# Web page:    http://gregarius.net/
#
###############################################################################

define('DBSTRUCT', dirname(__FILE__) . '/dbstruct.sql');

require_once('util.php');
require_once('cls/l10n.php');
/**
 * Checks the db schema for the for all required tables, adds those which are missing.
 * Returns the number of added tables;
 */ 
function checkSchema() {
	
	$missing_tables = array();
	$actual_tables=array();
	$expected_tables = getExpectedTables();
	
	$rs = rss_query( "show tables", true, true );
	while(list($tbl) = rss_fetch_row($rs)) {
		$actual_tables[]=$tbl;
	}
	
	foreach ($expected_tables as $base => $tbl) {
		$exists = array_search($tbl,$actual_tables);
		if ($exists === FALSE || $exists === NULL) {
			$missing_tables[]=$base;
		}
	}
	
	$updated  = 0;
	if (count($missing_tables) > 0) {
		$msg = (count($actual_tables)?"Updating":"Creating")
			.' your database schema! This should be a one-time operation,'
			.' if you see this message over and over again please import your database schema'
			.' manually.';
		rss_error($msg, RSS_ERROR_WARNING);

		foreach($missing_tables as $table) {
			$updated += call_user_func("_init_$table"); 
		}
		
		if ($updated == count($missing_tables)) {
			rss_error(__("Successfully created $updated of $updated database tables!"), RSS_ERROR_NOTICE);
		} else {
			rss_error(
				(count($missing_tables) - $updated) . " out of "
			. count($missing_tables) ." tables could not be created!",RSS_ERROR_ERROR);
		}
	}
	
	if ($updated) {
		rss_invalidate_cache();
	}
	return $updated;
}

function getExpectedTables() {
$expected_tables = array (
		"channels" => trim(getTable("channels")),
		"config" => trim(getTable("config")),
		"folders" => trim(getTable("folders")),
		"item" => trim(getTable("item")),
		"metatag" => trim(getTable("metatag")),
		"tag" => trim(getTable("tag")),
		"rating" => trim(getTable("rating")),
		"cache" => trim(getTable("cache")),
		"users" => trim(getTable("users")),		
		"dashboard" => trim(getTable("dashboard")),
		"properties" => trim(getTable("properties")),

	);
	return $expected_tables;
}
function rss_query_wrapper($query, $dieOnError=true, $preventRecursion=false) {
	if (defined('DUMP_SCHEMA')) {
		global $out;
		$out .= ($query . ";\n");
	} else {
	    rss_query(trim($query),$dieOnError,$preventRecursion);
	}
}

/**
 * this function handles specific schema updates that occurred 
 * during version updates.
 *
 * @return the number of updated tables
 */
function checkSchemaColumns($column) {
	$updated = 0;
	switch($column) {
		case 'c.mode':
		case 'mode':
			// default feed mode, added in 0.4.1
			rss_query('alter table ' .getTable('channels') .' add column mode int(16) not null default 1');
			if (rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
				$updated++;
				rss_error("updated schema for table " . getTable('channels'), RSS_ERROR_NOTICE);
			} else {
				rss_error("Failed updating schema for table " . getTable('channels')
				.": " . rss_sql_error_message(), RSS_ERROR_ERROR
				);
			}
		break;
		case 'c.itemsincache':
		case 'itemsincache':
			// date feed was last refreshed, added in 0.5.3
			rss_query('alter table ' .getTable('channels') .' add column itemsincache text null');
			if (rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
				$updated++;
				rss_error("updated schema for table " . getTable('channels'), RSS_ERROR_NOTICE);
			} else {
				rss_error("Failed updating schema for table " . getTable('channels')
				.": " . rss_sql_error_message(), RSS_ERROR_ERROR
				);
			}
		break;
		case 'c.daterefreshed':
		case 'daterefreshed':
			// date feed was last refreshed, added in 0.5.3
			rss_query('alter table ' .getTable('channels') .' add column daterefreshed datetime null default 1');
			if (rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
				$updated++;
				rss_error("updated schema for table " . getTable('channels'), RSS_ERROR_NOTICE);
			} else {
				rss_error("Failed updating schema for table " . getTable('channels')
				.": " . rss_sql_error_message(), RSS_ERROR_ERROR
				);
			}
		// break; - fallthrough allowed on purpose because these are added at the same time
		case 'c.etag':
		case 'etag':
			// etag of the feed, (from HTTP header) added in 0.5.3
			rss_query('alter table ' .getTable('channels') .' add column etag varchar(255) default null');
			if (rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
				$updated++;
				rss_error("updated schema for table " . getTable('channels'), RSS_ERROR_NOTICE);
			} else {
				rss_error("Failed updating schema for table " . getTable('channels')
				.": " . rss_sql_error_message(), RSS_ERROR_ERROR
				);
			}
		// break; - fallthrough allowed on purpose because these are added at the same time
		case 'c.lastmodified':
		case 'lastmodified':
			// last modified code returned by the feed (from HTTP header), added in 0.5.3
			rss_query('alter table ' .getTable('channels') .' add column lastmodified varchar(255) default null');
			if (rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
				$updated++;
				rss_error("updated schema for table " . getTable('channels'), RSS_ERROR_NOTICE);
			} else {
				rss_error("Failed updating schema for table " . getTable('channels')
				.": " . rss_sql_error_message(), RSS_ERROR_ERROR
				);
			}
		break;
		case 'i.author':
		case 'author':
			// item's author
			rss_query('alter table ' . getTable('item') . ' add column author varchar(255) null');
			if (rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
				$updated++;
				rss_error('updated schema for table ' . getTable('item'), RSS_ERROR_NOTICE);
			} else {
				rss_error('Failed updating schema for table ' . getTable('item') . ': '
					. rss_sql_error_message(), RSS_ERROR_ERROR);
			}
		break;
		
		case 'm.tdate':
		case 'tdate':
			// tag date
			rss_query('alter table ' . getTable('metatag') . ' add column tdate datetime null');
			if (rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
                rss_query('update ' . getTable('metatag') . ' set tdate=now()');
				$updated++;
				rss_error('updated schema for table ' . getTable('metatag'), RSS_ERROR_NOTICE);
			} else {
				rss_error('Failed updating schema for table ' . getTable('metatag') . ': '
					. rss_sql_error_message(), RSS_ERROR_ERROR);
			}
		break;
		case 'i.enclosure':
		case 'enclosure':
			// enclosure for an item
			rss_query('alter table ' . getTable('item') . ' add column enclosure varchar(255) null');
			if (rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
				$updated++;
				rss_error('updated schema for table ' . getTable('item'), RSS_ERROR_NOTICE);
			} else {
				rss_error('Failed updating schema for table ' . getTable('item') . ': '
					. rss_sql_error_message(), RSS_ERROR_ERROR);
			}
		break;
		
		case 'userips':
		case 'i.userips':
		// users.userips: list of valid IP subnets the user has logged in from
		rss_query('alter table ' . getTable('users') . ' add column userips text default \'\'');
			if (rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
				$updated++;
				rss_error('updated schema for table ' . getTable('users'), RSS_ERROR_NOTICE);
			} else {
				rss_error('Failed updating schema for table ' . getTable('users') . ': '
					. rss_sql_error_message(), RSS_ERROR_ERROR);
			}
		break;
		
		
		case 'i.md5sum':
		case 'md5sum':
			// md5check on an item - added in 0.5.3
			rss_query('alter table ' . getTable('item') . ' add column md5sum varchar(32) null');
			if (rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
				$updated++;
				rss_error('updated schema for table ' . getTable('item'), RSS_ERROR_NOTICE);
			} else {
				rss_error('Failed updating schema for table ' . getTable('item') . ': '
					. rss_sql_error_message(), RSS_ERROR_ERROR);
			}
		// break; - fallthrough allowed on purpose because these are added at the same time
		case 'i.guid':
		case 'guid':
			// guid of an item - added in 0.5.3
			rss_query('alter table ' . getTable('item') . ' add column guid text null');
			rss_query('alter table ' . getTable('item') . ' add index `guid` (`guid`(10))');
			if (rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
				$updated++;
				rss_error('updated schema for table ' . getTable('item'), RSS_ERROR_NOTICE);
			} else {
				rss_error('Failed updating schema for table ' . getTable('item') . ': '
					. rss_sql_error_message(), RSS_ERROR_ERROR);
			}
		
		break;
	}
	return $updated;
}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

function _init_channels() {
	$table = getTable('channels');
	rss_query_wrapper ('DROP TABLE IF EXISTS ' . $table, true, true);
	$sql_create = str_replace('__table__',$table, <<< _SQL_
		CREATE TABLE __table__ (
			id bigint(11) NOT NULL auto_increment,
  			title varchar(255) NOT NULL default '',
  			url varchar(255) NOT NULL default '',
  			siteurl varchar(255) default NULL,
  			parent tinyint(4) default '0',
  			descr varchar(255) default NULL,
  			dateadded datetime default NULL,
			daterefreshed datetime default NULL,
			refreshinterval int(16) NOT NULL default '60',
			itemsincache text default NULL,
			etag varchar(255) default NULL,
			lastmodified varchar(255) default NULL,
  			icon varchar(255) default NULL,
  			position int(11) NOT NULL default '0',
			mode int(16) NOT NULL default '1',
  			PRIMARY KEY  (id),
			KEY url (url)
		) TYPE=MyISAM;    
_SQL_
);

	rss_query_wrapper($sql_create, false, true);
	if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.', RSS_ERROR_ERROR);
		return 0;
	} else {
		return 1;
	}
}
///////////////////////////////////////////////////////////////////////////////

function _init_dashboard() {
	$table = getTable('dashboard');
	rss_query_wrapper ('DROP TABLE IF EXISTS ' . $table, true, true);
	$sql_create = str_replace('__table__',$table, <<< _SQL_
		CREATE TABLE __table__ (
			id bigint(11) NOT NULL auto_increment,
  			title text NOT NULL default '',
  			url text NOT NULL default '',
  			position tinyint(1) NOT NULL default 0,
  			obj text not NULL default '',
  			daterefreshed datetime default NULL,
  			itemcount tinyint(1) NOT NULL default 3,
  			PRIMARY KEY  (id)
		) TYPE=MyISAM;    
_SQL_
);

	rss_query_wrapper($sql_create, false, true);
	if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.', RSS_ERROR_ERROR);
		return 0;
	}
	
	
	$baseData = array(
		array ('Latest Gregarius News','http://devlog.gregarius.net/feed/?db=',0, 3),
		array ('Latest Plugins','http://plugins.gregarius.net/rss.php?db=',1, 5),
		array ('Latest Themes','http://themes.gregarius.net/rss.php?db=',1, 5),
		array ('Latest Forum posts','http://forums.gregarius.net/feeds/?Type=rss2&db=',1, 5),
//		array ('Technorati','http://www.technorati.com/watchlists/rss.html?wid=59610&db=',1, 5)
	);

	foreach ($baseData as $feed) {
		list($title,$url,$pos, $cnt) = $feed;
		rss_query_wrapper (
			"INSERT INTO $table (title, url, position, obj, daterefreshed, itemcount) VALUES "
			." ('$title', '$url', '$pos', '', null, $cnt)"	, false, true);	
		if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
			rss_error('The '  . $table .  ' table was created successfully, but I couldn\'t insert the default values. Please do so manually!', RSS_ERROR_ERROR);
			return 0;
		}	
	}
	
	return 1;
	
	
}


///////////////////////////////////////////////////////////////////////////////

function _init_folders() {
	$table = getTable('folders');
	rss_query_wrapper ('DROP TABLE IF EXISTS ' . $table, true, true);
	$sql_create = str_replace('__table__',$table, <<< _SQL_
		CREATE TABLE __table__ (
		  id tinyint(11) NOT NULL auto_increment,
		  name varchar(127) NOT NULL default '',
		  position int(11) NOT NULL default '0',    
		  PRIMARY KEY  (id),
		  UNIQUE KEY name (name)
		) TYPE=MyISAM;    
_SQL_
);

	rss_query_wrapper($sql_create, false, true);
	if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.', RSS_ERROR_ERROR);
		return 0;
	}
	
	
	rss_query_wrapper ("INSERT INTO $table (id,name) VALUES (0,'')", false, true);
	if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
		rss_error('The '  . $table .  ' table was created successfully, but I couldn\'t insert the default values. Please do so manually!', RSS_ERROR_ERROR);
		return 0;
	}
	rss_query_wrapper ("update $table set id=0 where id=1", false, true);
	if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
		rss_error('The '  . $table .  ' table was created successfully, but I couldn\'t insert the default values. Please do so manually!', RSS_ERROR_ERROR);
		return 0;
	}

	return 1;
}

///////////////////////////////////////////////////////////////////////////////

/** Config table */
function _init_config() {
	$cfg_table = getTable('config');
	
	rss_query_wrapper ('DROP TABLE IF EXISTS ' . $cfg_table, true, true);
	
	$sql_create = str_replace('__config__',$cfg_table, <<< _SQL_
		CREATE TABLE __config__ (
			     key_ varchar(127) NOT NULL default '',
			     value_ text NOT NULL,
			     default_ text NOT NULL,
			     type_ enum('string','num','boolean','array','enum') NOT NULL default 'string',
			     desc_ text,
			     export_ varchar(127) default NULL,
			     PRIMARY KEY  (key_)
			     ) TYPE=MyISAM;
_SQL_
);

	rss_query_wrapper($sql_create, false, true);
	if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
		rss_error('The ' .getTable('config') . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.', RSS_ERROR_ERROR);
		return 0;
	}

	
	return (setDefaults(null)?1:0);
}


function setDefaults($key) {
  	rss_error('inserting some default config values...', RSS_ERROR_NOTICE);
	$defaults = array (
		"rss.output.encoding"		=>		array("UTF-8","UTF-8","string","Output encoding for the PHP XML parser.","MAGPIE_OUTPUT_ENCODING"),
		"rss.output.itemsinchannelview"=>array("10","10","num","Number of read items shown on for a single channel.",NULL),
		"rss.output.showfavicons"	=>		array("true","true","boolean","Display the favicon for the channels that have one. Due to a IE bug, some icons do not render correctly. You can either change the URL to the icon in the admin screen, or turn the display of favicons off globally here.",NULL),
		"rss.output.usemodrewrite"	=>		array("true","true","boolean","Make use of apache's mod_rewrite module to return sexy urls. Turn this off if your host doesn't allow you to change this apache setting.",NULL),
		"rss.config.dateformat"		=>		array("F jS, Y, g:ia T","F jS, Y, g:ia T","string","Format to use when displaying dates. See here for help on the format: http://php.net/date Note that direct access to a given feed's month and day archives more or less depends on the fact that this date format contains the  \"F\" (Month) and \"jS\" (day) elements in this form. So feel free to change the order of the elements, but better leave those two tokens in :)",NULL),
		"rss.meta.debug"			=>		array("false","false","boolean"," When in debug mode some extra debug info is shown and the error reporting is a bit more verbose.",NULL),
		"rss.output.compression"	=>		array("true","true","boolean","This variable turns output compression on and off. Output compression is handled by most browsers.",NULL),
		"rss.output.channelcollapse"=>	array("true","true","boolean","Allow collapsing of channels on the main page. ",NULL),
		"rss.output.channelcollapsedefault"=>	array("false","false","boolean","Collapse the channels on the main page by default",NULL),
		"rss.output.usepermalinks"	=>		array("true","true","boolean","Display a permalink icon and allow linking a given item directly.",NULL),
		"rss.config.markreadonupdate"=>	array("false","false","boolean","Mark all old unread feeds as read when updating if new unread feeds are found.",NULL),
		"rss.output.lang"			=>		array("en_US,zh_CN,de,da,es,fr,he,it,ja,pt_BR,pt,ru,sv,0","en_US,zh_CN,de,da,es,fr,he,it,ja,pt_BR,pt,ru,sv,0","enum","Language pack to use.",NULL),
		"rss.output.lang.force"		=>		array("false","false",'boolean',"When false, Gregarius will negotiate the display language with the browser and will fall back to the language defined in rss.output.lang if the negotiation fails. When true, Gregarius won't negotiate and will always use the language defined in rss.output.lang.",NULL),
		"rss.config.absoluteordering"=>	array("true","true","boolean","Allow feeds and folders to be ordered by their order in the admin section. If this option is set to false, channels and folders will be organized alphabetically by their titles.",NULL),
		"rss.config.robotsmeta"		=>		array("noindex,follow","noindex,follow","string","How should spiders crawl us? (see http://www.robotstxt.org/wc/meta-user.html for more info).",NULL),
		"rss.config.serverpush"		=>		array("true","true","boolean","Use the server push method when updating your feeds in the browser. The browsers that support this (Mozilla and Opera) will be autodetected. Turn this option off if you do not use one of these browsers or if you would like to use the Ajax update method",NULL),
		"rss.config.refreshafter"	=>		array("45","45","num","If this option is set the feeds will be updated after keeping the browser open for x minutes. Please respect the feed providers by not setting this value to anything lower than thirty minutes. Set this variable to 0 turn this option off.",NULL),
		"rss.input.allowed"			=>		array('a:21:{s:1:"a";a:2:{s:4:"href";i:1;s:5:"title";i:1;}s:1:"b";a:0:{}s:10:"blockquote";a:0:{}s:2:"br";a:0:{}s:4:"code";a:0:{}s:3:"del";a:0:{}s:2:"em";a:0:{}s:1:"i";a:0:{}s:3:"img";a:2:{s:3:"src";i:1;s:3:"alt";i:1;}s:3:"ins";a:0:{}s:2:"li";a:0:{}s:2:"ol";a:0:{}s:1:"p";a:0:{}s:3:"pre";a:0:{}s:3:"sup";a:0:{}s:5:"table";a:0:{}s:2:"td";a:0:{}s:2:"th";a:0:{}s:2:"tr";a:0:{}s:2:"tt";a:0:{}s:2:"ul";a:0:{}}','a:21:{s:1:"a";a:2:{s:4:"href";i:1;s:5:"title";i:1;}s:1:"b";a:0:{}s:10:"blockquote";a:0:{}s:2:"br";a:0:{}s:4:"code";a:0:{}s:3:"del";a:0:{}s:2:"em";a:0:{}s:1:"i";a:0:{}s:3:"img";a:2:{s:3:"src";i:1;s:3:"alt";i:1;}s:3:"ins";a:0:{}s:2:"li";a:0:{}s:2:"ol";a:0:{}s:1:"p";a:0:{}s:3:"pre";a:0:{}s:3:"sup";a:0:{}s:5:"table";a:0:{}s:2:"td";a:0:{}s:2:"th";a:0:{}s:2:"tr";a:0:{}s:2:"tt";a:0:{}s:2:"ul";a:0:{}}',"array","This variable controls input filtering. HTML tags and their attributes, which are not in this list, get filtered out when new RSS items are imported.",NULL),
		"rss.output.showfeedmeta"	=>		array('false','false','boolean','Display meta-information (like a web- and rss/rdf/xml url) about each feed in the feed side-column.',NULL),
		//"rss.input.tags.delicious"	=>		array('false','false','boolean','Look up tag suggestions on del.icio.us when editing item tags.',NULL),
		"rss.output.frontpage.numitems"	=>		array("100","100","num","Maximum number of items displayed on the main page. Set this variable to 0 to show no items on the main page.",NULL),
		"rss.output.frontpage.mixeditems"	=> array('true','true','boolean','Show read items along with unread items on the front page?',NULL),
		"rss.output.frontpage.numreaditems"	=> array(-1,-1,'num','If there are no unread items then how many items to show on the frontpage. Set this to -1 if you want it to be the same as rss.output.numitemsonmainpage',NULL),
		"rss.output.theme"			=>		array('default','default','string','The theme to use. Download more themes from the <a href="http://themes.gregarius.net/">Gregarius Themes Repository</a>.',NULL),
		"rss.output.cachecontrol"	=>		array('false','false','boolean','If true, Gregarius will negotiate with the browser and check whether it should get a fresh document or not.',NULL),
		"rss.config.plugins"		=>		array('a:2:{i:0;s:13:"urlfilter.php";i:1;s:18:"roundedcorners.php";}','a:2:{i:0;s:13:"urlfilter.php";i:1;s:18:"roundedcorners.php";}','array','Plugins are third-party scripts that offer extended functionalities. More plugins can be found at the <a href="http://plugins.gregarius.net/">Plugin Repository</a>.' , NULL),
		"rss.input.allowupdates"	=>		array('true','true','boolean','Allow Gregarius to see if new items are updates of existing items.',NULL),
		"rss.output.titleunreadcnt"	=>		array('false','false','boolean','Display unread count in the document title.',NULL),
		"rss.config.tzoffset"		=>		array('0','0','num','Timezone offset, in hours, between your local time and server time. Valid range: "-12" through "12"',NULL),
		"rss.config.feedgrouping"	=>		array('false','false','boolean',"When true, Gregarius groups unread items per feed and sorts the feeds according to the <code>rss.config.absoluteordering</code> configuration switch. When false, unread items are not grouped by feed, but are sorted by date instead.",NULL),
		"rss.config.datedesc.unread"		=>		array('true','true','boolean',"When true, Gregarius displays newer <strong>unread</strong> items first. If false, Gregarius will display older unread items first.",NULL),
		"rss.config.datedesc.read"		=>		array('true','true','boolean',"When true, Gregarius displays newer <strong>read</strong> items first. If false, Gregarius will display older read items first.",NULL),
		"rss.config.autologout"		=>		array('false','false','boolean','When true, Gregarius will automatically remove the "admin cookie" when the browser window is closed, effectively logging you out.',NULL),
		"rss.config.publictagging"	=>		array('false','false','boolean','When true, every visitor to your Gregarius site will be allowed to tag items, when false only the Administrator (you) is allowed to tag.',NULL),
		"rss.config.rating"			=>		array('true','true','boolean','Enable the item rating system.',NULL),
		"rss.output.barefrontpage"			=>		array('false','false','boolean','Suppress the output of any read item on the front page.',NULL),
		"rss.output.title"			=> array('Gregarius','Gregarius','string','Sets the title of this feedreader.',NULL),
		"rss.config.ajaxparallelsize"			=> array('3','3','num','Sets the number of feeds to update in parallel. Remember to set rss.config.serverpush to false.',NULL),
		"rss.config.ajaxbatchsize"			=> array('3','3','num','Sets the number of feeds in a batch when using the ajax updater. Remember to set rss.config.serverpush to false.',NULL),
		"rss.config.defaultdashboard"	  		=> array('true','true','boolean','If the first page seen when entering the admin section should be the dashboard',NULL),
		"rss.config.deadthreshhold"	=>	array('24', '24', 'num', 'Sets the threshold for when a feed is marked as dead, in hours', NULL),
		"rss.search.maxitems" => array(500, 500, 'num', 'Sets the maximum number of items returned on a search', NULL),
		"rss.config.restrictrefresh"    => array("false","false","boolean","Restrict refresh to command line only (eg php -f update.php). Useful for busy sites with multiple users.",NULL),
		"rss.output.minimalchannellist" => array('false','false','boolean','Exclude folders and channels without unread items in channel list?',NULL), 
	);
	
	
	// just send in all config entry again, ignore duplicate row errors
	$atLeastOneIn = false;
	foreach($defaults as $k=>$vs) {
		list($v,$d,$t,$ds,$e) = $vs;
	    $ds=rss_real_escape_string($ds);
	    $e=rss_real_escape_string($e);
		rss_query_wrapper('insert into '. getTable('config') 
			. "(key_,value_,default_,type_,desc_,export_) VALUES ("
			. "'$k','$v','$d','$t','$ds'," .($e?"'$e'":"null") .")",false,true);
		if (rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {

			$atLeastOneIn = true;
		}
		
	}
	return $atLeastOneIn;
}

///////////////////////////////////////////////////////////////////////////////

function _init_item() {
	$table = getTable('item');
	rss_query_wrapper ('DROP TABLE IF EXISTS ' . $table, true, true);
	$sql_create = str_replace('__table__',$table, <<< _SQL_
		CREATE TABLE __table__ (
		  id bigint(16) NOT NULL auto_increment,
		  cid bigint(11) NOT NULL default '0',
		  md5sum varchar(32) default NULL,
		  guid text default NULL,
		  added datetime NOT NULL default '0000-00-00 00:00:00',
		  title varchar(255) default NULL,
		  url varchar(255) default NULL,
		  enclosure varchar(255) default NULL,
		  description text,
		  unread tinyint(4) default '1',
		  pubdate datetime default NULL,
		  author varchar(255) default NULL,		  
		  PRIMARY KEY  (id),
		  KEY url (url),
		  KEY guid(guid(10)),
		  KEY cid (cid),
		  KEY author (author)
		) TYPE=MyISAM;    
_SQL_
);

	rss_query_wrapper($sql_create, false, true);
	if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.', RSS_ERROR_ERROR);
		return 0;
	} else {
		return 1;
	}
}

///////////////////////////////////////////////////////////////////////////////

function _init_tag() {
	$table = getTable('tag');
	rss_query_wrapper ('DROP TABLE IF EXISTS ' . $table, true, true);
	$sql_create = str_replace('__table__',$table, <<< _SQL_
		CREATE TABLE __table__ (
			id bigint(16) NOT NULL auto_increment,
			tag varchar(63) NOT NULL default '',                    
			PRIMARY KEY  (id),
		 	UNIQUE KEY tag (tag),
			KEY id (id)
		) TYPE=MyISAM;    
_SQL_
);

	rss_query_wrapper($sql_create, false, true);
	if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.', RSS_ERROR_ERROR);
		return 0;
	} else {
		return 1;
	}
}


///////////////////////////////////////////////////////////////////////////////

function _init_metatag() {
	$table = getTable('metatag');
	rss_query_wrapper ('DROP TABLE IF EXISTS ' . $table, true, true);
	$sql_create = str_replace('__table__',$table, <<< _SQL_
		CREATE TABLE __table__ (
			fid bigint(16) NOT NULL default '0',                    
			tid bigint(16) NOT NULL default '0', 
			ttype enum('item','folder','channel') NOT NULL default 'item', 
			KEY fid (fid), 
			KEY tid (tid),
			KEY ttype (ttype)
		) TYPE=MyISAM;    
_SQL_
);

	rss_query_wrapper($sql_create, false, true);
	if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.', RSS_ERROR_ERROR);
		return 0;
	} else {
		return 1;
	}
}

///////////////////////////////////////////////////////////////////////////////

function _init_rating() {
	$table = getTable('rating');
	rss_query_wrapper ('DROP TABLE IF EXISTS ' . $table, true, true);
	$sql_create = str_replace('__table__',$table, <<< _SQL_
		CREATE TABLE __table__ (
			iid bigint(16)  NOT NULL,
  			rating tinyint(4) default '0'
		) TYPE=MyISAM;
_SQL_
);

	rss_query_wrapper($sql_create, false, true);
	if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.', RSS_ERROR_ERROR);
		return 0;
	} else {
		return 1;
	}
}


///////////////////////////////////////////////////////////////////////////////

function _init_cache() {
	$table = getTable('cache');
	rss_query_wrapper ('DROP TABLE IF EXISTS ' . $table, true, true);
	$sql_create = str_replace('__table__',$table, <<< _SQL_
		CREATE TABLE __table__ (
		cachekey VARCHAR( 128 ) NOT NULL ,
		timestamp DATETIME NOT NULL ,
		cachetype ENUM( 'ts', 'icon', 'feed' ) NOT NULL ,
		data BLOB,
		PRIMARY KEY ( cachekey )
		) TYPE=MYISAM;
_SQL_
);

	rss_query_wrapper($sql_create, false, true);
	

	
	if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.', RSS_ERROR_ERROR);
		return 0;
	} else {

		rss_query_wrapper ("INSERT INTO $table (cachekey,timestamp,cachetype,data) VALUES ('data_ts',now(),'ts',null)", false, true);	
		if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
			rss_error('The '  . $table .  ' table was created successfully, but I couldn\'t insert the default values. Please do so manually!', RSS_ERROR_ERROR);
			return 0;
		}
	
		return 1;
	}
}

///////////////////////////////////////////////////////////////////////////////

function _init_users() {
	$table = getTable('users');
	rss_query_wrapper ('DROP TABLE IF EXISTS ' . $table, true, true);
	$sql_create = str_replace('__table__',$table, <<< _SQL_
		CREATE TABLE __table__ (
		  uid bigint(16) NOT NULL auto_increment,
		  uname varchar(255) NOT NULL,		  
		  password varchar(255) NOT NULL,		  
		  ulevel bigint(11) NOT NULL default '1',		  
		  realname varchar(255) default NULL,		  		  
		  lastip varchar(255) default NULL,		  		  		  
		  userips TEXT default '',		  
		  lastlogin datetime NULL default '0000-00-00 00:00:00',
		  PRIMARY KEY  (uid),
		  KEY (uname)
		) TYPE=MyISAM;  
_SQL_
);

	rss_query_wrapper($sql_create, false, true);
	

	
	if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.', RSS_ERROR_ERROR);
		return 0;
	} else {

		rss_query_wrapper ("INSERT INTO $table (uname,password,ulevel,realname) VALUES ('admin','',99,'Administrator')", false, true);	
		if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
			rss_error('The '  . $table .  ' table was created successfully, but I couldn\'t insert the default values. Please do so manually!', RSS_ERROR_ERROR);
			return 0;
		}
	
		return 1;
	}
}

///////////////////////////////////////////////////////////////////////////////

function _init_properties() {
	$table = getTable('properties');
	rss_query_wrapper ('DROP TABLE IF EXISTS ' . $table, true, true);
	$sql_create = str_replace('__table__',$table, <<< _SQL_
		CREATE TABLE __table__ (
		  fk_ref_object_id text NOT NULL,
		  proptype enum('item','feed','folder','category','plugin','tag','theme','misc') NOT NULL default 'item',
		  property varchar(128) NOT NULL default '',
		  value text NOT NULL
		) TYPE=MyISAM;
		
_SQL_
);

	rss_query_wrapper($sql_create, false, true);
	if (!rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.', RSS_ERROR_ERROR);
		return 0;
	} else {
		$idSql = "alter table $table add UNIQUE KEY uniq (fk_ref_object_id(180),property,proptype)";
		rss_query_wrapper($idSql, false, true);
		return rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR);
	}
}


///////////////////////////////////////////////////////////////////////////////

if (isset($argv) && in_array('--dump',$argv)) {
	$stdOut = false;
	foreach ($argv as $idx => $arg) {
		if (substr($arg,0,9) == '--prefix=') {
			define ('DB_TABLE_PREFIX',substr($arg,9));
		} elseif ($arg == '--stdout') {
			$stdOut = true;
		}
	}
	define ('DUMP_SCHEMA', true);
	define ('RSS_NO_CACHE',true);
	define ('RSS_NO_DB',true);	
	if (!defined('PROFILING')) {define ('PROFILING', false);}
	require_once('db.php');
	if (!function_exists('rss_require')) {
		function rss_require($file,$once=true) {
		    $required_file = dirname(__FILE__) . '/'. $file;
		    if ($once) {
		        require_once($required_file);
		    } else {
		        require($required_file);
		    }
		}
	}
	$out = "################
# Gregarius " . _VERSION_ . "
# database structure
#################

";



	foreach (getExpectedTables() as $tbl => $dummy) {
	 	call_user_func("_init_$tbl"); 
	}
	// shamelessly copied from install.php
		
		$fp = $stdOut ? FALSE : @fopen(DBSTRUCT, 'w');
		
		if(!$fp) {
			// unable to open file for writing
			echo($out);
			exit();
		} else {
			// write the file
			fwrite($fp, $out);
			fclose($fp);
			echo "The database structure has been written to the file 'dbstruct.sql'.\n";
			exit();
		}
}

?>
