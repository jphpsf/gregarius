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

rss_require('util.php');

function getConfig($key) {
    static $config;
    if ($config == null) {
	$cfgQry = "select key_,value_,default_,type_,desc_,export_ "
	  ." from " .getTable("config");
	
	$res = rss_query($cfgQry, false);

	if (rss_sql_error() == 1146 || rss_num_rows($res) == 0) {
	    rss_error("Updating your database schema. This should be a one-time operation.\n");
	    rss_error("If you see this message overand over, please import the database schema manually.");
	    initConfig();
	    insertDefaults();
	    $res = rss_query($cfgQry);
	} 
	
	
	$config = array();
	while (list($key_,$value_,$default_,$type_,$description,$export_) = rss_fetch_row($res)) {
	    $value_ = real_strip_slashes($value_);
	    switch ($type_) {
	     case 'boolean':
		$real_value = ($value_ == 'true');
		break;
		
	     case 'array':
		$real_value=unserialize($value_);
		break;
		
	     case 'enum':
		$tmp = explode(',',$value_);
		$idx = array_pop($tmp);
		$real_value = $tmp[$idx];		
		break;
		
	     case 'num':
	     case 'string':
	     default:
		$real_value = $value_;
		break;		
	    }
	    
	    $config[$key_] =
	      array(
		    'value' => $real_value,
		    'default' => $default_,
		    'type' => $type_,
		    'description' => $description_
		    );
	    if ($export_ != '') {
		define ($export_,(string)$real_value);
	    }
	}
    }
    
    if (array_key_exists($key,$config)) {
	return $config[$key]['value'];
    }
    
    return null;
}

/**
 * update the db on the fly if user is upgrading and has no config table yet.
 */
function initConfig() {
    if (defined('DB_TABLE_PREFIX') && "" != DB_TABLE_PREFIX) {
		$cfg_table =  DB_TABLE_PREFIX . "_config";
    } else {    
    	$cfg_table =  "config";
    }

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
		rss_error('The ' .getTable('config') . ' doesn\'t exist and I couldn\'t create it! Please create it manually.');
		die();
	}

}

function insertDefaults() {
    if (defined('DB_TABLE_PREFIX') && "" != DB_TABLE_PREFIX) {
		$cfg_table =  DB_TABLE_PREFIX . "_config";
    } else {    
    	$cfg_table =  "config";
    }

	
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
_SQL_
);

	foreach(explode(';;',$sql_default) as $tok) {
		if ($tok) {
			rss_query("$tok", false);
		}
		if (rss_sql_error() > 0) {
			rss_error(The  .getTable('config') .  ' was created successfully, but I couldn\'t insert the default values. Please do so manually!');
			die();
		}
	}
	
}
?>
