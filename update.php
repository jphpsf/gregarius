<?
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003, 2004 Marco Bonetti
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
require_once('plugins/browser.php');
$browser = new Browser();

// decide wether we use server pushing or not
$doPush =
  // configured
  defined('DO_SERVER_PUSH') && DO_SERVER_PUSH
  // not a cron update
  && !array_key_exists('silent',$_GET)
  // browser supports it (Geckos and Opera)
  && $browser->supportsServerPush();

if ($doPush) {
    define('PUSH_BOUNDARY',"-------- =_aaaaaaaaaa0");
    header("Connection: close");
    header("Content-type: multipart/x-mixed-replace;boundary=\"".PUSH_BOUNDARY."\"");
    echo "WARNING: YOUR BROWSER DOESN'T SUPPORT THIS SERVER-PUSH TECHNOLOGY.";
    echo "\n" . PUSH_BOUNDARY ."\n";

    echo "Content-Type: text/html\n\n";
    
    rss_header($title="Updating", LOCATION_UPDATE, "", true);
    $cnt = sideChannels(false);
    
    
    echo "<div id=\"update\" class=\"frame\">\n"
      ."<h2>". sprintf(UPDATE_H2,$cnt) ."</h2>\n"
      
      ."<table id=\"updatetable\">\n"
      ."<tr>\n"
      ."<th class=\"lc\">".UPDATE_CHANNEL."</th>\n"
      ."<th class=\"mc\">".UPDATE_STATUS."</th>\n"
      ."<th class=\"rc\">".UPDATE_UNDREAD."</th>\n"
      ."</tr>";
    
    $sql = "select id, url, title from channels";    
    if (defined('ABSOLUTE_ORDERING') && ABSOLUTE_ORDERING) {
	$sql .= " order by parent, position"; 
    } else {
	$sql .= " order by parent, title";
    }
    $res = rss_query($sql);
    while (list($cid, $url, $title) = mysql_fetch_row($res)) {
	echo "<tr>\n";
	echo "<td class=\"lc\">$title</td>\n"; flush();
	echo "<td class=\"mc\">";
	$ret = update($cid);

	if (is_Array($ret)) {
	    $error = $ret[0];
	    $unread = $ret[1];
	} else {
	    $error = -1;
	    $unread = 0;
	}
	if ($error & MAGPIE_FEED_ORIGIN_CACHE) {
	    if ($error & MAGPIE_FEED_ORIGIN_HTTP_304) {
		echo UPDATE_NOT_MODIFIED;
	    } elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_TIMEOUT) {
		echo UPDATE_CACHE_TIMEOUT;
	    } elseif ($error & MAGPIE_FEED_ORIGIN_NOT_FETCHED) {
		echo UPDATE_STATUS_CACHED;
	    } else {
		echo $error;
	    }	    	    
	} elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_200) {
	    echo UPDATE_STATUS_OK;
	} else {
	    if (is_numeric($error)) {
		echo UPDATE_STATUS_ERROR;
	    } else {
		// shoud contain MagpieError at this point
		echo $error;
	    }	      
	}
	
	echo "</td>\n<td>" . ($unread >0?$unread:"&nbsp;") . "</td>\n";       
	echo "</tr>\n";
	flush();
    }
    
    echo "</table>\n";
    echo "<p><a href=\"".getPath()."\">Redirecting...</a></p>\n";
    echo "</div>\n";
    rss_footer();
    flush();
    
    // Sleep two seconds
    sleep(2);
} else {
    
    update("");
}


if ($doPush) {
    echo "\n" . PUSH_BOUNDARY ."\n";
    echo "Content-Type: text/html\n\n"
      ."<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">"
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
