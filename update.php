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

require_once('init.php');
rss_require ('plugins/browser.php');
rss_require ('config.php');

$browser = new Browser();

// decide wether we use server pushing or not
$doPush = true
  // configured
  && getConfig('DO_SERVER_PUSH')
  // not a cron update
  && !array_key_exists('silent',$_GET)
  // browser supports it (Geckos and Opera)
  && $browser->supportsServerPush();




if (getConfig ('MARK_READ_ON_UPDATE')) {
    $ts = time();
    $newItems = 0;
}

if ($doPush) {
    
    define('PUSH_BOUNDARY',"-------- =_aaaaaaaaaa0");
    define('ERROR_NOERROR',"");
    define('ERROR_WARNING'," warning");
    define('ERROR_ERROR'," error");
    define ('NO_NEW_ITEMS','-');
    
    header("Connection: close");
    header("Content-type: multipart/x-mixed-replace;boundary=\"".PUSH_BOUNDARY."\"");
    echo "WARNING: YOUR BROWSER DOESN'T SUPPORT THIS SERVER-PUSH TECHNOLOGY.";
    echo "\n" . PUSH_BOUNDARY ."\n";

    echo "Content-Type: text/html\n\n";
    
    rss_header($title=TITLE_UPDATING, LOCATION_UPDATE, "", true);
    $cnt = sideChannels(false);
    
    
    echo "<div id=\"update\" class=\"frame\">\n"
      ."<h2>". sprintf(UPDATE_H2,$cnt) ."</h2>\n"
      
      ."<table id=\"updatetable\">\n"
      ."<tr>\n"
      ."<th class=\"lc\">".UPDATE_CHANNEL."</th>\n"
      ."<th class=\"mc\">".UPDATE_STATUS."</th>\n"
      ."<th class=\"rc\">".UPDATE_UNDREAD."</th>\n"
      ."</tr>";
    
    $sql = "select id, url, title from " .getTable("channels");
    if (getConfig('ABSOLUTE_ORDERING')) {
	$sql .= " order by parent, position"; 
    } else {
	$sql .= " order by parent, title";
    }
    $res = rss_query($sql);
    while (list($cid, $url, $title) = rss_fetch_row($res)) {
	echo "<tr>\n";
	echo "<td class=\"lc\">$title</td>\n"; flush();

	$ret = update($cid);

	
	if (is_Array($ret)) {
	    $error = $ret[0];
	    $unread = $ret[1];
	} else {
	    $error = 0;
	    $unread = 0;
	}
	if ($error & MAGPIE_FEED_ORIGIN_CACHE) {
	    if ($error & MAGPIE_FEED_ORIGIN_HTTP_304) {
		$label = UPDATE_NOT_MODIFIED;
		$cls = ERROR_NOERROR;
	    } elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_TIMEOUT) {
		$label = UPDATE_CACHE_TIMEOUT;
		$cls = ERROR_WARNING;
	    } elseif ($error & MAGPIE_FEED_ORIGIN_NOT_FETCHED) {
		$label = UPDATE_STATUS_CACHED;
		$cls = ERROR_NOERROR;
	    } else {
		$label =  $error;
		$cls = ERROR_ERROR;
	    }	    	    
	} elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_200) {
	    $label = UPDATE_STATUS_OK;
	    $cls = ERROR_NOERROR;
	} else {
	    if (is_numeric($error)) {
		$label= UPDATE_STATUS_ERROR;
		$cls  = ERROR_ERROR;
	    } else {
		// shoud contain MagpieError at this point
		$label= $error;
		$cls = ERROR_ERROR;
	    }	      
	}
	echo "<td class=\"mc$cls\">$label</td>\n";       
	echo "<td class=\"rc\">" . ($unread >0?$unread:NO_NEW_ITEMS) . "</td>\n";       
	echo "</tr>\n";
	flush();
	
	$newItems += $unread;
    }
    
    echo "</table>\n";
    echo "<p><a href=\"".getPath()."\">Redirecting...</a></p>\n";
    echo "</div>\n";
    rss_footer();
    flush();
    
    // Sleep two seconds
    sleep(2);
} else {    
    $ret = update("");
    if (is_array($ret)) {
	$newItems = $ret[1];
    }
}

if ($newItems > 0 && getConfig ('MARK_READ_ON_UPDATE') && $ts > 0) {
    rss_query("update " . getTable("item") ." set unread = 0 where unread = 1 and unix_timestamp(added) < $ts");
}

if ($doPush) {
    echo "\n" . PUSH_BOUNDARY ."\n";
    echo "Content-Type: text/html\n\n"
      ."<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n"
      ."<html>\n"
      ."<head>\n"
      ."<title>Redirecting...</title>\n"
      ."<meta http-equiv=\"refresh\" content=\"0;url=index.php\"/>\n"
      ."</head>\n"
      ."<body/>\n"
      ."</html>";

    echo "\n" . PUSH_BOUNDARY ."\n";
    echo "WARNING: YOUR BROWSER DOESN'T SUPPORT THIS SERVER-PUSH TECHNOLOGY.\n";
} else {
    if (! array_key_exists('silent',$_GET)) {
	$redirect = "http://"
	  . $_SERVER['HTTP_HOST']
	  . dirname($_SERVER['PHP_SELF']);
	if (substr($redirect,-1) != "/") {
	    $redirect .= "/";
	}
	header("Location: $redirect");
    }
}
?>
