<?
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2005 Marco Bonetti
#
###############################################################################
# File: $Id$ $Name$
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
# E-mail:      mbonetti at users dot sourceforge dot net
# Web page:    http://sourceforge.net/projects/gregarius
#
###############################################################################

/**
 * Checks the db schema for the for all required tables, adds those which are missing.
 * Returns the number of added tables;
 */ 
function checkSchema() {
	$rs = rss_query( "show tables" );
	$missing_tables = array();
	$actual_tables=array();
	
	$expected_tables = array (
		"channels" => trim(getTable("channels")),
		"config" => trim(getTable("config")),
		"folders" => trim(getTable("folders")),
		"item" => trim(getTable("item")),
		"metatag" => trim(getTable("metatag")),
		"tag" => trim(getTable("tag"))
	);
	
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
		rss_error($msg);
	
		$pf = (defined('DB_TABLE_PREFIX') && "" != DB_TABLE_PREFIX ?
			DB_TABLE_PREFIX:""
		);
		
		
		foreach($missing_tables as $table) {
			$tbl = substr($table,$pf);
			$updated += call_user_func("_init_$tbl"); 
		}
		
		if ($updated == count($missing_tables)) {
			rss_error("Successfully created $updated of $updated database tables!");
		} else {
			rss_error((count($missing_tables) - $updated) . " out of "
			. count($missing_tables) ." tables could not be created!");
		}
	}
	
	return $updated;
}

function _init_channels() {
	$table = getTable('channels');
	rss_query ('DROP TABLE IF EXISTS ' . $table);
	$sql_create = str_replace('__table__',$table, <<< _SQL_
		CREATE TABLE __table__ (
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
_SQL_
);

	rss_query($sql_create);
	if (rss_sql_error() > 0) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.');
		return 0;
	} else {
		return 1;
	}
}

function _init_folders() {
	$table = getTable('folders');
	rss_query ('DROP TABLE IF EXISTS ' . $table);
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

	rss_query($sql_create);
	if (rss_sql_error() > 0) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.');
		return 0;
	}
	
	
	rss_query ("INSERT INTO $table (id,name) VALUES (0,'')");
	if (rss_sql_error() > 0) {
		rss_error('The '  . $table .  ' table was created successfully, but I couldn\'t insert the default values. Please do so manually!');
		return 0;
	}
	rss_query ("update $table set id=0 where id=1");
	if (rss_sql_error() > 0) {
		rss_error('The '  . $table .  ' table was created successfully, but I couldn\'t insert the default values. Please do so manually!');
		return 0;
	}

	return 1;
}

/** Config table */
function _init_config() {
	$cfg_table = getTable('config');
	
	rss_query ('DROP TABLE IF EXISTS ' . $cfg_table);
	
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

	rss_query($sql_create);
	if (rss_sql_error() > 0) {
		rss_error('The ' .getTable('config') . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.');
		return 0;
	}

	// defaults for config	
	$sql_default = str_replace('__config__',$cfg_table, <<< _SQL_
  		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.output.cachedir","/tmp/magpierss","/tmp/magpierss","string","Where should magpie store its temporary files? (Apache needs write permissions on this dir.)","MAGPIE_CACHE_DIR");;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.output.encoding","UTF-8","UTF-8","string","Output encoding for the PHP XML parser.","MAGPIE_OUTPUT_ENCODING");;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.output.itemsinchannelview","10","10","num","Number of read items shown on for a single channel",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.output.showfavicons","true","true","boolean","Display the favicon for the channels that have one. Due to a IE bug, some icons do not render correctly on in the admin screen, or turn the favicon displaying offthis browser. You can either change the URL to the icon in the admin screen, or turn the favicon displaying off globally here.",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.output.usemodrewrite","true","true","boolean","Make use of apache\'s mod_rewrite module to return sexy urls. Turn this off if your host doesn\'t allow you to change this apache setting",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.config.dateformat","F jS, Y, g:ia T","F jS, Y, g:ia T","string","Format to use when displaying dates. See here for help on the format: http://ch.php.net/manual/en/function.date.php Note that direct access to a given feed\'s month and day archives more or less depends on the fact that this date format contains the  \"F\" (Month) and \"jS\" (day) elements in this form. So feel free to change the order of the elements, but better leave those two tokens in :)",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.config.showdevloglink","true","true","boolean","Show a link to the gregarius devlog. This is mainly useful on the actual live gregarius site (http://gregarius.net/).You can safely set this to \'false\' if you don\'t want to display a link back.",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.meta.debug","false","false","boolean"," When in debug mode some extra debug info is shown and the error reporting is a bit more verbose  ",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.output.compression","true","true","boolean","This variable turns output compression on and off. Output compression is handled by most browsers.",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.output.channelcollapse","true","true","boolean","Allow collpasing of channels on the main page. ",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.output.usepermalinks","true","true","boolean","Display a permalink icon and allow linking a given item directly",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.config.markreadonupdate","true","true","boolean","Mark all old unread feeds as read when updating, if new unread feeds are found ",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.output.lang","en,fr,0","en,fr,0","enum","Language pack to use. (As of today \'en\' and \'fr\' ar available)",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.config.absoluteordering","true","true","boolean","Allow ordering of channels in the admin. (Uses channel title instead)",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.config.robotsmeta","index,follow","index,follow","string","How should spiders crawl us? (see http://www.robotstxt.org/wc/meta-user.html for more info)",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.config.serverpush","true","true","boolean","Use server push on update.php for a more user-friendly experience. This is only supported by Mozilla browser (Netscape, Mozilla, FireFox,...) and Opera. These brwosers will be autodetected. If you\'re not using one of these (you should) you can as well turn this off.",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.config.refreshafter","45","45","num","If this option is set the feeds will be refreshed after x minutes of inactivity. Please respect the feed providers by not setting this value to anything lower than thirty minutes. Set this variable to 0 turn this option off",NULL);;
		INSERT INTO __config__ (key_,value_,default_,type_,desc_,export_) VALUES ("rss.input.allowed",'a:17:{s:1:"a";a:2:{s:4:"href";i:1;s:5:"title";i:1;}s:1:"b";a:0:{}s:10:"blockquote";a:0:{}s:2:"br";a:0:{}s:4:"code";a:0:{}s:1:"i";a:0:{}s:3:"img";a:2:{s:3:"src";i:1;s:3:"alt";i:1;}s:2:"li";a:0:{}s:2:"ol";a:0:{}s:1:"p";a:0:{}s:3:"pre";a:0:{}s:5:"table";a:0:{}s:2:"td";a:0:{}s:2:"th";a:0:{}s:2:"tr";a:0:{}s:2:"tt";a:0:{}s:2:"ul";a:0:{}}','a:17:{s:1:"a";a:2:{s:4:"href";i:1;s:5:"title";i:1;}s:1:"b";a:0:{}s:10:"blockquote";a:0:{}s:2:"br";a:0:{}s:4:"code";a:0:{}s:1:"i";a:0:{}s:3:"img";a:2:{s:3:"src";i:1;s:3:"alt";i:1;}s:2:"li";a:0:{}s:2:"ol";a:0:{}s:1:"p";a:0:{}s:3:"pre";a:0:{}s:5:"table";a:0:{}s:2:"td";a:0:{}s:2:"th";a:0:{}s:2:"tr";a:0:{}s:2:"tt";a:0:{}s:2:"ul";a:0:{}}',"array","This variable controls input filtering. HTML tags and their attributes, which are not in this list, get filtered out when new RSS items are imported",NULL);;
		insert into __config__ (key_,value_,default_,type_,desc_,export_) values ("rss.output.showfeedmeta",'false','false','boolean','Display meta-information (like a web- and rss/rdf/xml url) about each feed in the feed side-column',NULL);;
_SQL_
);

	foreach(explode(';;',$sql_default) as $tok) {
		if ($tok) {
			rss_query("$tok", false);
		}
		if (rss_sql_error() > 0) {
			rss_error('The '  .getTable('config') .  'table was created successfully, but I couldn\'t insert the default values. Please do so manually!');
			return 0;
		}
	}
	return 1;
}


function _init_item() {
	$table = getTable('item');
	rss_query ('DROP TABLE IF EXISTS ' . $table);
	$sql_create = str_replace('__table__',$table, <<< _SQL_
		CREATE TABLE __table__ (
		  id bigint(16) NOT NULL auto_increment,
		  cid bigint(11) NOT NULL default '0',
		  added datetime NOT NULL default '0000-00-00 00:00:00',
		  title varchar(255) default NULL,
		  url varchar(255) default NULL,
		  description text,
		  unread tinyint(4) default '1',
		  pubdate datetime default NULL,
		  PRIMARY KEY  (id),
		  KEY url (url),
		  KEY cid (cid)
		) TYPE=MyISAM;    
_SQL_
);

	rss_query($sql_create);
	if (rss_sql_error() > 0) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.');
		return 0;
	} else {
		return 1;
	}
}


function _init_tag() {
	$table = getTable('tag');
	rss_query ('DROP TABLE IF EXISTS ' . $table);
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

	rss_query($sql_create);
	if (rss_sql_error() > 0) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.');
		return 0;
	} else {
		return 1;
	}
}

function _init_metatag() {
	$table = getTable('metatag');
	rss_query ('DROP TABLE IF EXISTS ' . $table);
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

	rss_query($sql_create);
	if (rss_sql_error() > 0) {
		rss_error('The ' . $table . 'table doesn\'t exist and I couldn\'t create it! Please create it manually.');
		return 0;
	} else {
		return 1;
	}
}


?>
