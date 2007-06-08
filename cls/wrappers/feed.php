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


///// FEED WRAPPERS /////
function rss_feed_class() {
	
	$ret ="";
	if ($GLOBALS['rss']->currentFeed->collapsed) {
		$ret .= "collapsed";
		if ($GLOBALS['rss']->currentFeed->hasUnreadItems) {
			$ret .= " unread";
		}
	}
	if ($ret) {
		return " class=\"$ret\" ";
	}
	
	return "";
}

function rss_feed_allow_collapsing() {
	
	return (
		!($GLOBALS['rss']->renderOptions & IL_NO_COLLAPSE) && 
		getConfig('rss.output.channelcollapse') && 
		!($GLOBALS['rss']->currentFeed->hasUnreadItems));
}

function rss_feed_collapsed() {	
	return $GLOBALS['rss'] -> currentFeed -> collapsed;
}

function rss_feed_expand_collapse_link() {

	return $_SERVER['PHP_SELF'] 
		. "?" .(rss_feed_collapsed()?"expand=":"collapse=")
		. $GLOBALS['rss'] -> currentFeed ->cid ."#".$GLOBALS['rss']->currentFeed->escapedTitle;

}

function rss_feed_expand_collapse_js() {
	return "_ftgl(".$GLOBALS['rss'] -> currentFeed ->cid.");return false;";
}

function rss_feed_do_favicon() {
	
	return (
		getConfig('rss.output.showfavicons') && 
		$GLOBALS['rss']->currentFeed->iconUrl && 
		(!($GLOBALS['rss']->currentFeed->hasUnreadItems) || ($GLOBALS['rss']->renderOptions & IL_FOLDER_VIEW)));
}

function rss_feed_favicon_url() {
	return $GLOBALS['rss']->currentFeed->iconUrl;
}

function rss_feed_url() {
	
	if (getConfig('rss.output.usemodrewrite')) {
		return getPath()."/feed/".$GLOBALS['rss']->currentFeed->escapedTitle ."/";
	} 
	return getPath()."feed.php?channel=".$GLOBALS['rss']->currentFeed->cid ;
}

function rss_feed_title() {
	
	return $GLOBALS['rss']->currentFeed->title;
}


function rss_feed_id($pf="f") {	
	if (!$GLOBALS['rss']->currentFeed->hasUnreadItems) {
		return " id=\"$pf".trim($GLOBALS['rss']->currentFeed->cid) ."\"";
	}
	return "";
}

function rss_feed_anchor_name () {
	if (rss_feed_allow_collapsing()) {
		return " name=\"".$GLOBALS['rss']->currentFeed->escapedTitle . "\"";
	}
}


function rss_feed_escaped_title () {
	return $GLOBALS['rss']->currentFeed->escapedTitle;
}

function rss_feed_items() {
	
	if ($GLOBALS['rss']->currentFeed->collapsed) 
		return;
		
	foreach($GLOBALS['rss']->currentFeed->items as $item) {
		$item->render();
	}
}

function rss_feed_do_title() {
	return ($GLOBALS['rss']->currentFeed -> title != "" && !($GLOBALS['rss']->renderOptions & IL_CHANNEL_VIEW));
}


?>
