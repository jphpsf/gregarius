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
# FITNESS FOR A PARTICULAR PURPOSE.	 See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA	02111-1307	USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:		mbonetti at users dot sourceforge dot net
# Web page:		http://sourceforge.net/projects/gregarius
#
###############################################################################

require_once('init.php');
rss_require ('extlib/browser.php');
rss_require ('config.php');

$browser = new Browser();

// decide wether we use server pushing or not
$doPush = true
  // configured
  && getConfig('rss.config.serverpush')
  // not a cron update
  && !array_key_exists('silent',$_GET)
  // browser supports it (Geckos and Opera)
  && $browser->supportsServerPush();


set_time_limit(0);
ini_set('max_execution_time',300);

$newIds = array();
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
	 
	 
	rss_header($title=LBL_TITLE_UPDATING, LOCATION_UPDATE, null, "", (HDR_NONE | HDR_NO_CACHECONTROL | HDR_NO_OUPUTBUFFERING));
	$cnt = sideChannels(false);
	 
	 
	echo "<div id=\"update\" class=\"frame\">\n"
		."<h2>". sprintf(LBL_UPDATE_H2,$cnt) ."</h2>\n"
		
		."<table id=\"updatetable\">\n"
		."<tr>\n"
		."<th class=\"lc\">".LBL_UPDATE_CHANNEL."</th>\n"
		."<th class=\"mc\">".LBL_UPDATE_STATUS."</th>\n"
		."<th class=\"rc\">".LBL_UPDATE_UNDREAD."</th>\n"
		."</tr>";

    // mbi/11.05.2005, patch by Alexandre ROSSI  <niol@sousmonlit.dyndns.org>
    // fixes nasty double-where sql bug.

	$sql = "select id, url, title from " .getTable("channels");
    	$sql .=" where !(mode & " . FEED_MODE_DELETED_STATE .") ";
    
	if (hidePrivate()) {
		$sql .=" and !(mode & " . FEED_MODE_PRIVATE_STATE .") ";			
	}



	 if (getConfig('rss.config.absoluteordering')) {
	$sql .= " order by parent, position"; 
	 } else {
	$sql .= " order by parent, title";
	 }
	 $res = rss_query($sql);
	while (list($cid, $url, $title) = rss_fetch_row($res)) {
		echo "<tr>\n";
		echo "<td class=\"lc\">$title</td>\n"; flush();
	
		$ret = update($cid);
	
		
		if (is_array($ret)) {
			list($error,$unreadIds) = $ret;
			$newIds = array_merge($newIds,$unreadIds);
		} else {
			$error = 0;
			$unreadIds = array();
		}
		$unread = count($unreadIds);
		
		if ($error & MAGPIE_FEED_ORIGIN_CACHE) {
			if ($error & MAGPIE_FEED_ORIGIN_HTTP_304) {
				$label = LBL_UPDATE_NOT_MODIFIED;
				$cls = ERROR_NOERROR;
			} elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_TIMEOUT) {
				$label = LBL_UPDATE_CACHE_TIMEOUT;
				$cls = ERROR_WARNING;
			} elseif ($error & MAGPIE_FEED_ORIGIN_NOT_FETCHED) {
				$label = LBL_UPDATE_STATUS_CACHED;
				$cls = ERROR_NOERROR;
			} elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_404) {
				$label = LBL_UPDATE_NOT_FOUND;
				$cls = ERROR_ERROR;
			} else {
				$label =	 $error;
				$cls = ERROR_ERROR;
			}
		} elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_200) {
			$label = LBL_UPDATE_STATUS_OK;
			$cls = ERROR_NOERROR;
		} else {
			if (is_numeric($error)) {
				$label= LBL_UPDATE_STATUS_ERROR;
				$cls	= ERROR_ERROR;
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
		$newIds = $ret[1];
	}
}

if (count($newIds) > 0 && getConfig ('rss.config.markreadonupdate')) {
	rss_query(
		"update " . getTable("item") 
		." set unread = unread & ".SET_MODE_READ_STATE
		." where unread & " .FEED_MODE_UNREAD_STATE
		." and id not in (" . implode(",",$newIds) .")"
	);
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
