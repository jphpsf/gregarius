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


///// FEEDS COLUMN WRAPPERS /////

function rss_feeds_stats() {
	$unread = getUnreadCount(null, null);
	
	
	$res = rss_query("select count(*) from ".getTable("item")
		." where !(unread & ".FEED_MODE_DELETED_STATE.") "
		. (hidePrivate()? " and !(unread & ".FEED_MODE_PRIVATE_STATE.")":"")
		
		);
		
		
	list ($total) = rss_fetch_row($res);

	$res = rss_query("select count(*) from "
		.getTable("channels")." where !(mode & ".FEED_MODE_DELETED_STATE.")"
		. (hidePrivate()? " and !(mode & ".FEED_MODE_PRIVATE_STATE.")":"")
		);
		
	list ($channelcount) = rss_fetch_row($res);

	return array($total, $unread, $channelcount);
}

function rss_feeds_folder_is_root() {
	return $GLOBALS['rss']->currentFeedsFolder->isRootFolder;
}

function rss_feeds_folders() {
	if (!isset($GLOBALS['rss']->feedList)) {
		$GLOBALS['rss']->feedList = new FeedList(false);
	}
	foreach ($GLOBALS['rss']->feedList->folders as $folder) {
		$folder->render();
	}
	rss_plugin_hook('rss.plugins.feeds.after', null);
}

function rss_feeds_folder_class() {
	return $GLOBALS['rss']->currentFeedsFolder->isCollapsed ? "collapsed":"expanded";
}

function rss_feeds_folder_id() {
	return $GLOBALS['rss']->currentFeedsFolder->id;
}

function rss_feeds_folder_name() {
	return $GLOBALS['rss']->currentFeedsFolder->name;
}

function rss_feeds_folder_link() {
	return $GLOBALS['rss']->currentFeedsFolder->rlink;
}

function rss_feeds_folders_unread_count($label=LBL_UNREAD_PF) {
	if (array_key_exists($GLOBALS['rss']->currentFeedsFolder->id,$GLOBALS['rss']->feedList->collapsed_folders)) { 
		$sCls = ($GLOBALS['rss']->currentFeedsFolder->isCollapsed?"display:inline":"display:none");
		return sprintf($label, "fs".$GLOBALS['rss']->currentFeedsFolder->id, 
			$sCls, 
			$GLOBALS['rss']->feedList->collapsed_folders[$GLOBALS['rss']->currentFeedsFolder->id]);
	}
	return "";
}

function rss_feeds_ul_class() {
    if ($GLOBALS['rss']->currentFeedsFolder->isRootFolder) {
        return "froot";
    }
	return $GLOBALS['rss']->currentFeedsFolder->isCollapsed ? "fcollapsed" : "fexpanded";
}
function rss_feeds_ul_style() {
	return $GLOBALS['rss']->currentFeedsFolder->isCollapsed ? "none":"block";
}

function rss_feeds_folder_feeds() {
	foreach ($GLOBALS['rss']->currentFeedsFolder->feeds as $feed) {
		$feed -> render();
	}
}

function rss_feeds_feed_li_class() {
	return ($GLOBALS['rss']->currentFeedsFeed -> isActiveFeed ? " class=\"active\"":"");
}

function rss_feeds_feed_class() {
	return ($GLOBALS['rss']->currentFeedsFeed-> class_);
}

function rss_feeds_feed_icon() {
	return ($GLOBALS['rss']->currentFeedsFeed-> icon);
}

function rss_feeds_feed_title_entities() {
	return htmlentities($GLOBALS['rss']->currentFeedsFeed-> title);
}

function rss_feeds_feed_title() {
	return htmlspecialchars($GLOBALS['rss']->currentFeedsFeed-> title);
}

function rss_feeds_feed_link() {
	return ($GLOBALS['rss']->currentFeedsFeed-> rlink);
}

function rss_feeds_feed_read_label() {
	return ($GLOBALS['rss']->currentFeedsFeed-> rdLbl);
}

function rss_feeds_feed_meta() {
    if (getConfig('rss.output.showfeedmeta') != NULL) {
    
        $ret = " [<a href=\"". htmlentities($GLOBALS['rss']->currentFeedsFeed->publicUrl)."\">xml</a>";

        if ($GLOBALS['rss']->currentFeedsFeed->siteurl != "" && substr($GLOBALS['rss']->currentFeedsFeed->siteurl,0,4) == 'http') {
            $ret .= "|<a href=\"" . htmlentities($GLOBALS['rss']->currentFeedsFeed->siteurl) ."\">www</a>";
        }

        if (getConfig('rss.meta.debug')) {
            $ret .= "|<a href=\"". getPath() ."feed.php?channel=".$GLOBALS['rss']->currentFeedsFeed->id."&amp;dbg\">dbg</a>";
        }

        $ret .= "]";
		return $ret;
    }
}
?>