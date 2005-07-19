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


// these are the fontsizes on the weighted list at /tag/
define ('SMALLEST',9);
define ('LARGEST',45);
define ('UNIT','px');

class Tags {

	var $allTags = array ();
	var $renderOptions;
	var $rss;
	
	function Tags() {
		$this -> rss = $GLOBALS['rss'];
		$this -> populate();
	}
	
	function populate() {
		// the all tags weighted list
		$sql = "select tag,count(*) as cnt from "
			.getTable('metatag')
			." left join ".getTable('item')." i on (fid=i.id),"
			.getTable('tag')." t "." where tid=t.id ";

		if (hidePrivate()) {
			$sql .= " and !(i.unread & ".FEED_MODE_PRIVATE_STATE.") ";
		}

		
		$sql .= "group by tid order by 1";

		
		$res = rss_query($sql);
		$max = 0;
		$min = 100000;
		$cntr = 0;
		while (list ($tag, $cnt) = rss_fetch_row($res)) {
			$this->allTags[$tag] = $cnt;
			$cntr ++;
		}
	}
	
	function makeTagLink($tag) {
		return getPath(). (getConfig('rss.output.usemodrewrite') ? "tag/$tag" : "tags.php?tag=$tag");
	}

	function setRenderOptions($options) {
		$this-> rss -> renderOptions |= $options;
	}
	
	function render() {
		if (count($this->allTags)) {
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
			foreach ($this->allTags as $tag => $cnt) {
				$taglink = $this -> makeTagLink($tag);
				$ret .= "\t<a href=\"$taglink\" title=\"$cnt "
					. ($cnt > 1 || $cnt == 0 ? LBL_ITEMS : LBL_ITEM)."\" style=\"font-size: "
					. (SMALLEST + ($cnt / $fontstep)).UNIT.";\">$tag</a> \n";
			}
		} else {
			$ret = "";
		}
		
		echo "<div id=\"alltags\" class=\"frame\">$ret</div>\n";
	}

}
?>