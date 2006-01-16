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


rss_require('cls/alltags.php');
rss_require('cls/channels.php');

/**
 * A TagListItem represents a single tag in the tags sidecolumn
 */
class TagListItem extends FeedListItem {
	var $title;
	var $cnt;
	var $rlink;
	var $rdLbl = "";
	var $class_ = "";
	var $icon;
	function TagListItem($title,$cnt, $url) {
		$this -> title = $title;
		$this -> cnt = $cnt;
		$this -> rlink = $url;
		$this -> rdLbl = "($cnt)";
		$this->icon = getExternalThemeFile("media/noicon.png");
	}
	
	function render() {
		$GLOBALS['rss']->currentFeedsFeed = $this;
		eval($GLOBALS['rss'] ->getCachedTemplateFile("feedsfeed.php"));	
	}
}

/**
 * A TagList renders a list of all the tags
 */
class TagList extends FeedList{
	
	var $tags;
	var $folders = array();
	var $countTaggedItems = 0;
	var $tagCount = 0;
	
	function TagList() {
		$this -> populate();
		$this -> 	columnTitle = LBL_TAG_TAGS;
		$GLOBALS['rss']-> feedList = $this;
	}
	
	function populate() {
		$t = new Tags();
		$this -> tags = $t -> allTags;
		$this -> folders[0] = new FeedFolder(null , null ,$this);
		foreach ($this -> tags as $tag => $count) {	
			$this -> tagCount++;
			$this -> countTaggedItems += $count;
			$tt = new TagListItem($tag,$count, $t -> makeTagLink($tag) );
			$this->folders[0]->feeds[] = $tt;
		}
	}
	
	function getStats() {
		return sprintf(LBL_TAGCOUNT_PF, $this -> countTaggedItems, $this->tagCount);
	}
	
}
?>
