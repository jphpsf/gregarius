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


///// FEEDS COLUMN WRAPPERS /////

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

function rss_feeds_folders_unread_count($label=null) {
	if ($label === null) {
		$label=__('<strong id="%s" style="%s">(%d unread)</strong>');
	}
	if (array_key_exists($GLOBALS['rss']->currentFeedsFolder->id,$GLOBALS['rss']->feedList->collapsed_folders)) { 
		$sCls = ($GLOBALS['rss']->currentFeedsFolder->isCollapsed?"display:inline":"display:none");
		$ret = sprintf($label, "fs".$GLOBALS['rss']->currentFeedsFolder->id, 
			$sCls, 
			$GLOBALS['rss']->feedList->collapsed_folders[$GLOBALS['rss']->currentFeedsFolder->id]);

		switch( $GLOBALS['rss']->feedList -> columnTitle ) {
			case __('Categories'):
				$ret = rss_plugin_hook("rss.plugins.sidemenu.categoryunreadlabel", $ret);
				break;
			case __('Feeds'):
				$ret = rss_plugin_hook("rss.plugins.sidemenu.folderunreadlabel", $ret);
				break;
		}
		
		return $ret;
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

function rss_feeds_feed_id() {
	return ($GLOBALS['rss']->currentFeedsFeed-> id);
}

function rss_feeds_feed_title_entities() {
	return htmlentities($GLOBALS['rss']->currentFeedsFeed-> title);
}

function rss_feeds_feed_description_entities() {
    if ($GLOBALS['rss']->currentFeedsFeed-> descr) {
	   return htmlspecialchars($GLOBALS['rss']->currentFeedsFeed-> descr);
  }
  return rss_feeds_feed_title_entities();
}

function rss_feeds_feed_title() {
	return htmlspecialchars($GLOBALS['rss']->currentFeedsFeed-> title);
}

function rss_feeds_feed_link() {
	return ($GLOBALS['rss']->currentFeedsFeed-> rlink);
}

function rss_feeds_feed_read_label() {
	return rss_plugin_hook( 'rss.plugins.sidemenu.feedunreadlabel', ($GLOBALS['rss']->currentFeedsFeed-> rdLbl) );
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

function rss_feeds_column_title() {
	if (!isset($GLOBALS['rss']->feedList)) {
		$GLOBALS['rss']->feedList = new FeedList(false);
	}
	return $GLOBALS['rss']->feedList->columnTitle;
}


function rss_feeds_stats() {
	if (!isset($GLOBALS['rss']->feedList)) {
		$GLOBALS['rss']->feedList = new FeedList(false);
	}
	return $GLOBALS['rss']->feedList->getStats();
}

function rss_feeds_onclickaction($what=null) {
	if (!getConfig('rss.output.channelcollapse')) {
		return "";
	}
	
	if ($what) {
		return "_tgl(". rss_feeds_folder_id() .",'$what'); return false;";
	} else {
		return "_tgl(". rss_feeds_folder_id() ."); return false;";
	}
	
}

?>
