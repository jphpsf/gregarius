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


require_once('cls/alltags.php');

class TagList extends Tags{
	
	var $countTaggedItems=0;
	var $countUnreadItems=0;
	var $tagCount;
	var $type;
	
	function TagList($type = 'item') {
		parent::Tags($type);
		
		$sql = "select count(*) as cnt from " 
		. getTable('metatag') . " left join ";
		if($this -> type == 'channel'){
			$sql .= getTable('channels') . " c on (fid=c.id)"
				. " where ttype = 'channel'";
		}else{
			$sql .= getTable('item') ."  i on (fid=i.id) where ttype = 'item' ";
		}

		if (hidePrivate()) {
			$sql .= " and !(i.unread & ".FEED_MODE_PRIVATE_STATE.") ";
		}
	
		list($this -> countTaggedItems) = rss_fetch_row(rss_query($sql));

		$sql = "select count(distinct(fid)) as cnt from " 
		. getTable('metatag') . " left join ";
		
		if ($this -> type == 'channel') {
			$sql .= getTable('item') . " i on (fid=i.cid)"
				. " where ttype = 'channel'";
		} else {
			$sql .= getTable('item') ."  i on (fid=i.id) where ttype = 'item' ";
		}
		
		$sql .= " and (i.unread & ".FEED_MODE_UNREAD_STATE.") ";
		if (hidePrivate()) {
			$sql .= " and !(i.unread & ".FEED_MODE_PRIVATE_STATE.") ";
		}

		list($this -> countUnreadItems) = rss_fetch_row(rss_query($sql));

		$sql = "select  count(distinct(tid)) as cnt from "
		 . getTable('metatag') . " left join ";
		if($this -> type == 'channel'){
			$sql .= getTable('channels') . " c on (fid=c.id)"
				. " where ttype = 'channel'";
		}else{
			$sql .= getTable('item') ." i on (fid=i.id) where ttype = 'item'";
		}

		if (hidePrivate()) {
			$sql .= " and !(i.unread & ".FEED_MODE_PRIVATE_STATE.") ";
		}
		 
		list($this -> tagCount) = rss_fetch_row(rss_query($sql));
	}
	
	function render() {
		if($this -> type == 'channel'){
			echo "<h2>".LBL_TAG_FOLDERS."</h2>\n";
			echo "<p class=\"stats\">" .sprintf(LBL_UNREAD_PF, "", "", $this->countUnreadItems) . "</p>\n";
		}else{
			echo "<h2>".LBL_TAG_TAGS."</h2>\n";
			echo "<p class=\"stats\">" .sprintf(LBL_TAGCOUNT_PF, $this -> countTaggedItems, $this->tagCount) . "</p>\n";
		}
		echo "<ul id=\"taglist\">\n";
		foreach ($this -> allTags as $tag => $cnt) {
			echo "\t<li><img src=\"".rss_theme_path();
			if($this -> type == 'channel'){
				echo "/media/folder.gif";
			}else{
				echo "/media/noicon.png";
			}
			echo "\" class=\"favicon\" alt=\"\" />";
			if($this -> type == 'channel'){
				$unread = $this->unreadItems[$tag];
				echo " <a href=\"".$this -> makeTagLink($tag) ."\">$tag</a> "
					. sprintf(LBL_SIDE_UNREAD_FEEDS,$unread,$cnt) . "</li>\n";
			}else{
				echo " <a href=\"".$this -> makeTagLink($tag) ."\">$tag</a> " 
					. sprintf(LBL_SIDE_ITEMS,$cnt) . "</li>\n";
			}
		}
		echo "</ul>\n";
	}
}
?>
