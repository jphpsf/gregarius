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

/**
 * This class handles the weighted list of all tags, 
 * accessible at /tag/
 */
class Tags {

	/** 
	 * holds all the tags in the database.
	 * Structure: $tag => $count_of_tagged_items
	 */
	var $allTags = array ();
	
	/** Holds a reference to the superglobal RSS object */
	var $rss;
	
	/** Objects that the tags point to */
	var $type;

	/**
	 * holds unread item count of tags
	 * Structure: $tag => $unread_items
	 */
	 var $unreadItems = array();

	/**
	 * Constructor. Gets a reference of the RSS superglobal and fills
	 * the instance data
	 */
	function Tags($type = 'item') {
		$this -> rss = $GLOBALS['rss'];
		$this -> type = $type;
		$this -> populate();
	}
	
	
	/**
	 * Fills the instance data for this object: gets a hold 
	 * of all tags defined in the system.
	 */
	function populate() {
		// the all tags weighted list
		$sql = "select t.id, tag, count(*) as cnt from "
			.getTable('metatag');
		if($this -> type == 'channel'){
			$sql .= " left join " . getTable('channels') . " c on (fid=c.id),"
				.getTable('tag')." t "." where tid=t.id "
				. " and ttype = 'channel'";
		}else{
			$sql .= " left join ".getTable('item')." i on (fid=i.id),"
				.getTable('tag')." t "." where tid=t.id "
				." and ttype = 'item'";
		}


		// Don't count tags of private items
		if (hidePrivate()) {
			$sql .= " and !(i.unread & ".FEED_MODE_PRIVATE_STATE.") ";
		}
		
		$sql .= "group by tid order by tag";

		
		$res = rss_query($sql);
		$max = 0;
		$min = 100000;
		$cntr = 0;
		while (list ($tid, $tag, $cnt) = rss_fetch_row($res)) {
			$this->allTags[$tag] = $cnt;

			// list of unread items
			$cntUnread = 0;
			$sql = "select fid from " . getTable('metatag') . " where tid = $tid";
			$res2 = rss_query($sql);
			while(list($fid) = rss_fetch_row($res2)){
				if($this->type == 'channel'){
					$cntUnread += getUnreadCount($fid,null);
				}else{
					$sql = "select unread from " . getTable('item') . " where id = $fid"
						. " and (unread & ".FEED_MODE_UNREAD_STATE.") ";
					if (hidePrivate()) {
						$sql .= " and !(unread & ".FEED_MODE_PRIVATE_STATE.") ";
					}
					if(rss_num_rows(rss_query($sql))) $cntUnread++;
				}
			}
			$this->unreadItems[$tag] = $cntUnread;

			$cntr ++;
		}


	}
	
	/**
	 * Preformats a tag url, depending on whether mod_rewrite is enabled or not
	 */
	function makeTagLink($tag) {
		if($this -> type == 'channel'){
			return getPath(). (getConfig('rss.output.usemodrewrite') ? "$tag/" : "feed.php?vfolder=$tag");
		}else{
			return getPath(). (getConfig('rss.output.usemodrewrite') ? "tag/$tag" : "tags.php?tag=$tag");
		}
	}

	/**
	 * Gateway to the RSS superglobal rendering options
	 */
	function setRenderOptions($options) {
		$this-> rss -> renderOptions |= $options;
	}
	
	/**
	 * Does the actual rendering. Since this is a rather simple
	 * class we don't use a specific template. We'll have to the
	 * day we start supporting non-screen themes. (read: RSS output)
	 */
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
		// Spit out the markup
		echo "<div id=\"alltags\" class=\"frame\">$ret</div>\n";
	}

}
?>