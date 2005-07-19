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
	var $tagCount;
	
	function TagList() {
		parent::Tags();
		
		$sql = "select count(*) as cnt from " 
		. getTable('metatag') . "  left join " 
		. getTable('item') ."  i on (fid=i.id) ";
		
		if (hidePrivate()) {
			$sql .= " where !(i.unread & ".FEED_MODE_PRIVATE_STATE.") ";
		}
		list($this -> countTaggedItems) = rss_fetch_row(rss_query($sql));
		
		$sql = "select  count(distinct(tid)) as cnt from "
		 . getTable('metatag') . " left join " . getTable('item')
		 ." i on (fid=i.id)";
		
		if (hidePrivate()) {
			$sql .= " where !(i.unread & ".FEED_MODE_PRIVATE_STATE.") ";
		}
		 
		list($this -> tagCount) = rss_fetch_row(rss_query($sql));
	}
	
	function render() {
		/*
		$spread = max($this->allTags) - min($this->allTags);
		if ($spread <= 0) {
			$spread = 1;
		};
		$fontspread = LARGEST - SMALLEST;
		$fontstep = $spread / $fontspread;
		if ($fontspread <= 0) {
			$fontspread = 1;
		}
		$ret = "";
		*/
		echo "<h2>".LBL_TAG_TAGS."</h2>\n";
		echo "<p class=\"stats\">" .sprintf(LBL_TAGCOUNT_PF, $this -> countTaggedItems, $this->tagCount) . "</p>\n";
		echo "<ul>";
		foreach ($this -> allTags as $tag => $cnt) {
			echo "<li style=\"font-size:medium\">"
			."<img src=\"".rss_theme_path() ."/media/noicon.png"."\" class=\"favicon\" alt=\"\" />"
			."<a href=\"".$this -> makeTagLink($tag) ."\">$tag</a> ($cnt)</li>";
		}
		echo "</ul>";
	}
}
?>