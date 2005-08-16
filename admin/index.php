<?php
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2005 Marco Bonetti
#
###############################################################################
# This program is free software and open source software; you can redistribute
# it and/or modify it under the terms of the GNU General Public License as
# published by the Free Software Foundation; either version 2 of the License,
# or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful, but WITHOUT
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
# FITNESS FOR A PARTICULAR PURPOSE.	 See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:	   mbonetti at users dot sourceforge dot net
# Web page:	   http://sourceforge.net/projects/gregarius
#
###############################################################################

define ('RSS_FILE_LOCATION','/admin');
define ('THEME_OVERRIDE','default');

require_once('../init.php');
require_once('../opml.php');
require_once('ds.php');

require_once('channels.php');
require_once('items.php');
require_once('config.php');
require_once('folders.php');
require_once('opml.php');

define ('CST_ADMIN_DOMAIN','domain');
define ('CST_ADMIN_DOMAIN_NONE','none');
define ('CST_ADMIN_DELETE_ACTION','delete');
define ('CST_ADMIN_DEFAULT_ACTION','default');
define ('CST_ADMIN_EDIT_ACTION','edit');
define ('CST_ADMIN_MOVE_UP_ACTION','up');
define ('CST_ADMIN_MOVE_DOWN_ACTION','down');
define ('CST_ADMIN_SUBMIT_EDIT','submit_edit');
define ('CST_ADMIN_VIEW','view');
define ('CST_ADMIN_CONFIRMED','confirmed');
define ('CST_ADMIN_PRUNE','prune');
define ('CST_ADMIN_DOMAIN_SYSINFO','sysinfo');
define ('CST_ADMIN_METAACTION','metaaction');

define ('CST_ADMIN_DOMAIN_FOLDER','folders');
define ('CST_ADMIN_DOMAIN_CHANNEL','feeds');
define ('CST_ADMIN_DOMAIN_ITEM','items');
define ('CST_ADMIN_DOMAIN_CONFIG','config');
define ('CST_ADMIN_DOMAIN_OPML','opml');

// OPML import target
define ('CST_ADMIN_OPML_IMPORT_WIPE',1);
define ('CST_ADMIN_OPML_IMPORT_FOLDER',2);
define ('CST_ADMIN_OPML_IMPORT_MERGE',3);


$auth = true;

if (defined('ADMIN_USERNAME') && defined ('ADMIN_PASSWORD')) {
	if (!array_key_exists('PHP_AUTH_USER',$_SERVER) ||
		$_SERVER['PHP_AUTH_USER'] != ADMIN_USERNAME ||
		!array_key_exists('PHP_AUTH_PW',$_SERVER) ||
		$_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD ) {
	  		header('WWW-Authenticate: Basic realm="Gregarius Admin Authentication"');
	  		header('HTTP/1.0 401 Unauthorized');
	  		$auth = false;
	}
}

if ($auth) {
	setAdminCookie();
	
	if (array_key_exists('login',$_GET)) {
        rss_redirect();
	}
}
admin_header();
admin_main($auth);
admin_footer();


///////////////////////////////////////////////////////////////////////////////////////////

/**
 * main function. checks for authorization and renders the
 * required admin section.
 */
function admin_main($authorised) {

	echo "\n<div id=\"channel_admin\" class=\"frame\">";

    if ($authorised) {
      admin_menu();
      if (array_key_exists(CST_ADMIN_DOMAIN,$_REQUEST)) {
         switch($_REQUEST[CST_ADMIN_DOMAIN]) {
          case CST_ADMIN_DOMAIN_FOLDER:
         $show = folder_admin();
         break;
          case CST_ADMIN_DOMAIN_CHANNEL:
         $show = channel_admin();
         break;
          case CST_ADMIN_DOMAIN_CONFIG:
         $show = config_admin();
         break;
          case CST_ADMIN_DOMAIN_ITEM:
         $show = item_admin();
         break;
          default:
         break;
         }
      }
   
      if (array_key_exists(CST_ADMIN_VIEW,$_REQUEST) || isset($show)) {
         if (!isset($show)) {
         $show = $_REQUEST[CST_ADMIN_VIEW];
         }
         switch ($show) {
          case CST_ADMIN_DOMAIN_CONFIG:
         config();
         break;
          case CST_ADMIN_DOMAIN_CHANNEL:
         channels();
         break;
          case CST_ADMIN_DOMAIN_FOLDER:
         folders();
         break;
          case CST_ADMIN_DOMAIN_OPML:
         opml();
         break;
          case CST_ADMIN_DOMAIN_NONE:
         break;
          case CST_ADMIN_DOMAIN_ITEM:
         items();			
         break;
			 case CST_ADMIN_DOMAIN_SYSINFO:
			sysinfo();
			 break;
          default:
         }
      } else {
         channels();
      }
   
      echo "\n<div class=\"clearer\"></div>\n";
   
   } else {
		rss_error(sprintf(LBL_ADMIN_ERROR_NOT_AUTHORIZED,getPath()));
	}
	echo "</div>\n";
}

/////////

/**
 * Renders the admin sub-menu
 */
function admin_menu() {
	$active = array_key_exists(CST_ADMIN_VIEW, $_REQUEST) ? $_REQUEST[CST_ADMIN_VIEW] : null;

	/*
	$use_mod_rewrite = getConfig('rss.output.usemodrewrite');

	if (function_exists("apache_get_modules")) {
		$use_mod_rewrite = $use_mod_rewrite && in_array('mod_rewrite', apache_get_modules());
	}
	*/
	$use_mod_rewrite = false;
	
	echo "\n<ul class=\"navlist\">\n";
	foreach (array (
	/* url/id -- internationalized label, defined in intl/* */
	array (CST_ADMIN_DOMAIN_CHANNEL, LBL_ADMIN_DOMAIN_CHANNEL_LBL), 
	array (CST_ADMIN_DOMAIN_ITEM, LBL_ADMIN_DOMAIN_ITEM_LBL), 
	array (CST_ADMIN_DOMAIN_CONFIG, LBL_ADMIN_DOMAIN_CONFIG_LBL), 
	array (CST_ADMIN_DOMAIN_FOLDER, LBL_ADMIN_DOMAIN_FOLDER_LBL), 
	array (CST_ADMIN_DOMAIN_OPML, LBL_ADMIN_DOMAIN_LBL_OPML_LBL)) as $item) {

		if ($use_mod_rewrite) {
			$link = $item[0];
		} else {
			$link = "index.php?view=".$item[0];
		}
		$lbl = $item[1];
		$cls = ($item[0] == $active ? " class=\"active\"" : "");
		echo "\t<li$cls><a href=\"".getPath()."admin/$link\">".ucfirst($lbl)."</a></li>\n";
	}
	echo "\t<li><a href=\"".getPath()."?logout\">".LBL_ADMIN_LOGOUT."</a></li>\n";
	echo "</ul>\n";
}

function admin_kses_to_html($arr) {
	$ret = "";
	foreach ($arr as $tag => $attr) {
	$ret .= "&lt;$tag";
	foreach ($attr as $nm => $val) {
		$ret .= "&nbsp;$nm=\"...\"&nbsp;";
	}
	$ret .= "&gt;\n";
	}
	return $ret;
}

function admin_plugins_mgmnt($arr) {
	$ret = "<ul>\n";
	foreach($arr as $plugin) {
		$info = getPluginInfo($plugin);
		if (count($info)) {
			$ret .= "\t<li>";
			if (array_key_exists('name',$info)) {
				$ret .= $info['name'];
			}
			if (array_key_exists('version',$info)) {
				$ret .= " v".$info['version'];
			}
			$ret .="</li>\n";
		}
	}
	$ret .= "</ul>\n";
	return $ret;
}


/**
 * fetches information for the given plugin,
 * which should contain:
 *
 *	/// Name: Url filter
 *	/// Author: Marco Bonetti
 *	/// Description: This plugin will try to make ugly URL links look better
 *	/// Version: 1.0
 *
 */
function getPluginInfo($file) {
	$info = array();
	$path = "../plugins/$file";
	if (file_exists($path)) {
		$f = @fopen($path,'r');
		$contents = "";
		if ($f) {
  			$contents .= fread($f, filesize($path));
			@fclose($f);
		} else {
			$contents = "";
		}

		if ($contents && preg_match_all("/\/\/\/\s?([^:]+):(.*)/",$contents,$matches,PREG_SET_ORDER)) {
			foreach($matches as $match) {
				$key = trim(strtolower($match[1]));
				$val = trim($match[2]);
				if ($key == 'version') {
					$val=preg_replace('/[^0-9\.]+/','',$val);
				}
				
				$info[$key] = $val;
			}
		}
	} 
	
	return $info;
}


function getLanguages() {
  	$cntr = 0;
    $d = dir('../intl');
    $files = array();
    $ret = array();
    $cntr = 0;
    $activeIdx = "0";
    while (false !== ($entry = $d->read())) {
       if (
        $entry != "CVS" &&
        substr($entry,0,1) != "."
       ) {
       		$info = getLanguageInfo($entry);
             if (count($info) && array_key_exists('language',$info)) {
                $shortL=  preg_replace('|\.php.*$|','',$entry);
                $ret[$shortL] = $info['language'];
             }
       }
    }
    $d->close();
    return $ret;
}
function getLanguageInfo($file) {
	$info = array();
	$path = "../intl/$file";
	if (file_exists($path)) {
		$f = @fopen($path,'r');
		$contents = "";
		if ($f) {
  			$contents .= fread($f, filesize($path));
			@fclose($f);
		} else {
			$contents = "";
		}

		if ($contents && preg_match_all("/\/\/\/\s?([^:]+):(.*)/",$contents,$matches,PREG_SET_ORDER)) {
			foreach($matches as $match) {
				$key = trim(strtolower($match[1]));
				$val = trim($match[2]);
				if ($key == 'version') {
					$val=preg_replace('/[^0-9\.]+/','',$val);
				}

				$info[$key] = $val;
			}
		}
	}

	return $info;
}

function admin_enum_to_html($arr) {
	$idx = array_pop($arr);
	$ret = "";
	foreach ($arr as $i => $val) {
		if ($i == $idx) 
			$ret .= "$val";
	}
	return $ret;
}

/**
 * this function will set an admin cookie, which doesn't play a role in
 * authentication, but only allows access to Private items
 */
function setAdminCookie() {
    if (getConfig('rss.config.autologout')) {
        $t = 0;
    } else {
        $t =time()+COOKIE_LIFESPAN;
    }
    setcookie(PRIVATE_COOKIE, getPrivateCookieVal(), $t, getPath());
}


function admin_header() {

	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
	echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">\n";
	echo "<head>";
	
	$header = new Header(LBL_TITLE_ADMIN, LOCATION_ADMIN, null, '', (HDR_NONE | HDR_NO_CACHECONTROL | HDR_NO_OUPUTBUFFERING));
	$header -> render();

	echo "</head>";
	echo "<body>\n";
	
	echo ""
    ."<div id=\"nav\" class=\"frame\">"
    ."<h1 id=\"top\">" .rss_main_title() ."</h1>";    
	$nav = new Navigation();
	$nav->render();
	echo "</div>";
}

function admin_footer() {
	echo "<div id=\"footer\" class=\"frame\">\n";
	rss_main_footer();
	echo "</div>\n\n</body>\n</html>\n";
}
?>
