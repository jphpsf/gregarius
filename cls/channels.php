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


define('COLLAPSED_FOLDERS_COOKIE', 'collapsedfolders');


class FeedListItem {
	var $id;
	var $title;
	var $url;
	var $siteurl;
	var $name;
	var $parent;
	var $icon;
	var $descr;
	var $mode;
	var $root;
	var $isActiveFeed;
	
	function FeedListItem($id, $title, $url, $siteurl, $name, $parent, $icon, $descr, $mode) {
		$this->id = $id;
		$this->title = $title;
		$this->url = $url;
		$this->siteurl = $siteurl;
		$this->name = $name;
		$this->parent = $parent;
		$this->icon = $icon;
		if (!$this -> icon) {
			$this->icon = rss_theme_path() ."/media/noicon.png";
		}
		
		$this->descr = $descr;
		$this->mode = $mode;

		if (getConfig('rss.output.usemodrewrite')) {
			$this->rlink = getPath().preg_replace("/[^a-zA-Z0-9_]/", "_", $title)."/";
		} else {
			$this->rlink = getPath()."feed.php?channel=$id";
		}

		$res = rss_query ("select count(*) from " .getTable("item")
        ." where cid=$id and unread & "  . FEED_MODE_UNREAD_STATE
        . " and !(unread & " . FEED_MODE_DELETED_STATE .")"
        );
    
		list($cnt) = rss_fetch_row($res);
		if ($cnt > 0) {
			$this->rdLbl= sprintf(LBL_UNREAD_PF, "cid$id","",$cnt);
			$this->class_= "feed title unread";
		} else {
			$this->rdLbl= "";
			$this->class_= "feed title";
		}
	}


	
	function render() {
		$GLOBALS['rss']->currentFeedsFeed = $this;
		require($GLOBALS['rss'] ->getTemplateFile("feedsfeed.php"));
		/*
		return;
		
		if ($this->icon) {
			echo "<img src=\"".$this->icon."\" class=\"favicon\" alt=\"\" />";
		}
		echo "<a class=\"".$this->class_."\" "." title=\"".htmlentities($this->title)."\" "
		." href=\"".$this->rlink."\">"
		.htmlspecialchars($this->title)."</a>";

		echo " " .$this -> rdLbl;

		//echo "<strong id="cid72" style="">(2 unread)</strong>";

		if (getConfig('rss.output.showfeedmeta') != NULL) {
			echo " [<a href=\"".htmlentities($this->url)."\">xml</a>";

			if ($this->siteurl != "" && substr($this->siteurl, 0, 4) == 'http') {
				echo "|<a href=\"".htmlentities($this->siteurl)."\">www</a>";
			}

			if (getConfig('rss.meta.debug')) {
				echo "|<a href=\"".getPath()."feed.php?channel=".$this->id."&amp;dbg\">dbg</a>";
			}
			echo "]";

		}

	*/
	}

}


class FeedFolder {
	var $feeds = array ();
	var $name;
	var $id;
	var $isCollapsed = false;
	var $rlink;
	var $isRootFolder = false;
	var $rootList;
	function FeedFolder($name, $id, &$rootList) {
		$this->name = $name;
		$this->id = $id;
		if (getConfig('rss.output.usemodrewrite')) {
			$this->rlink = getPath().preg_replace("/[^a-zA-Z0-9_]/", "_", $name)."/";
		} else {
			$this->rlink = getPath()."feed.php?folder=$id";
		}
		$this->isRootFolder = ($id == 0);
		$this->rootList = $rootList;
	}
	
	function render() {		
		$GLOBALS['rss']->currentFeedsFolder = $this;
		require($GLOBALS['rss'] ->getTemplateFile("feedsfolder.php"));
	}
}

class FeedList {

	var $collapsed_folders = array ();
	var $collapsed_ids = array ();
	var $folders = array ();
	var $activeId;
	var $feedCount = 0;
	
	function FeedList($activeId) {
		_pf('FeedList() ctor');
		$this->activeId = $activeId;
		$this->loadCollapsedState();
		$this->populate();
		
		
	}
	
	function loadCollapsedState() {
	    _pf('FeedList->loadCollapsedState()...');
	    
		if (getConfig('rss.output.channelcollapse')) {
	
			//read per-user stored collapsed folders
			if (array_key_exists(COLLAPSED_FOLDERS_COOKIE, $_COOKIE)) {
				$this->collapsed_ids = explode(":", $_COOKIE[COLLAPSED_FOLDERS_COOKIE]);
			}
	
			//get unread count per folder
	
			//get unread count per folder                                                                        
			$sql = "select f.id, f.name, count(*) as cnt "
			." from "
			.getTable('item') ." i, "
			.getTable('channels') . " c, "
			.getTable('folders') ." f "
			." where i.unread & ". FEED_MODE_UNREAD_STATE
			." and !(i.unread & ". FEED_MODE_DELETED_STATE .")";
			if (hidePrivate()) {
				$sql .=" and !(unread & " . FEED_MODE_PRIVATE_STATE .") ";
			}
			$sql .= " and !(c.mode & " . FEED_MODE_DELETED_STATE .") ";
			$sql .= " and i.cid=c.id and c.parent=f.id "
			." group by 1"; 
	        _pf('query');
			$res = rss_query($sql);
	        _pf('ok');	
			while (list ($cid, $cname, $cuc) = rss_fetch_row($res)) {
				$this->collapsed_folders[$cid] = $cuc;
			}
	       
			sort($this->collapsed_ids);
		}
	    _pf('done');

	}
	
	function getFeedCount() {
		return $this -> feedCount;
	}

	function populate() {
		_pf('FeedList->populate() ...');
		$sql = "select "." c.id, c.title, c.url, c.siteurl, f.name, c.parent, c.icon, c.descr, c.mode "." from ".getTable("channels")." c, ".getTable("folders")." f "." where f.id = c.parent";

		if (hidePrivate()) {
			$sql .= " and !(c.mode & ".FEED_MODE_PRIVATE_STATE.") ";
		}

		$sql .= " and !(c.mode & ".FEED_MODE_DELETED_STATE.") ";

		if (getConfig('rss.config.absoluteordering')) {
			$sql .= " order by f.position asc, c.position asc";
		} else {
			$sql .= " order by f.name, c.title asc";
		}
		$res = rss_query($sql);
		$this -> feedCount = rss_num_rows($res);

		while (list ($cid, $ctitle, $curl, $csiteurl, $fname, $cparent, $cico, $cdescr, $cmode) = rss_fetch_row($res)) {
			$f = new FeedListItem($cid, $ctitle, $curl, $csiteurl, $fname, $cparent, $cico, $cdescr, $cmode);
			$f -> isActiveFeed = ($this->activeId && $cid == $this->activeId ); 
			if (!array_key_exists($cparent, $this->folders)) {
				$this->folders[$cparent] = new FeedFolder($fname, $cparent,$this);
			}
			$this->folders[$cparent]->feeds[] = $f;
			$this->folders[$cparent]->isCollapsed = in_array($cparent, $this->collapsed_ids) && ($cparent > 0);

		}
		_pf('done');
	}

	function render() {
		_pf('FeedList->render() ...');
		require($GLOBALS['rss'] ->getTemplateFile("feeds.php"));
		_pf('done');		
	}
}
?>