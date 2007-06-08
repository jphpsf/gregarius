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


define('COLLAPSED_FOLDERS_COOKIE', 'collapsedfolders');

/**
 * A FeedListItem object holds a single item in the feeds sidecolumn
 */
class FeedListItem {

	/** Database id (e.g. item.cid, channels.id) for this feed */
	var $id;
	/** Feed title */
	var $title;
	/** URL of this feed */	
	var $url;
	/** URL of this feed, escaped from login/password information, if any */	
	var $publicUrl;
	/** URL of the website publishing this feed */	
	var $siteurl;
	/** Name of the folder holding this feed */	
	var $name;
	/** ID of the folder holding this feed (e.g. channels.parent) */	
	var $parent;
	/** URL of the icon for this feed, probably offsite */	
	var $icon;
	/** The "description" for this feed. (e.g. channel.description) */		
	var $descr = "";
	/** Feed "mode" (e.g. private, deprecated, ...)*/		
	var $mode;
	/** True when the user is reading the current feed in "feed mode" */	
	var $isActiveFeed;
	
	/**
	 * Constructor. Fills in the instance variables, escapes urls accordingly 
	 */
	function FeedListItem($id, $title, $url, $siteurl, $name, $parent, $icon, $descr, $mode, $unreadCount) {
		$this->id = $id;
		$this->title = $title;
		$this->url = $url;
		$this->publicUrl = preg_replace(
			'|(https?://)([^:]+:[^@]+@)(.+)$|','\1\3',$url
		);
		$this->siteurl = $siteurl;
		$this->name = $name;
		$this->parent = $parent;

		
		if ( getConfig('rss.output.showfavicons') && $icon){
			if (substr($icon,0,5) == 'blob:') {
				$this->icon = getPath() . "extlib/favicon.php?url=". rss_real_escape_string(substr($icon,5));
			} else {
				$this->icon = $icon;
			}
		} elseif (getConfig('rss.output.showfavicons')) {
            $this->icon = getExternalThemeFile("media/noicon.png");
		} else {
            $this->icon = false;
		}
		
		$this->descr = $descr;
		$this->mode = $mode;

		if (getConfig('rss.output.usemodrewrite')) {
			$this->rlink = getPath(rss_uri($title)) . "/";
		} else {
			$this->rlink = getPath()."feed.php?channel=$id";
		}


		if ($unreadCount > 0) {
			$this->rdLbl= sprintf(__('<strong id="%s" style="%s">(%d unread)</strong>'), "cid$id","",$unreadCount);
			$this->class_= "feed title unread";
		} else {
			$this->rdLbl= "";
			$this->class_= "feed title";
		}
	}


	/** 
	 * Renders this FeedListItem: Moves the superglobal "current feed" pointer
	 * to this, then includes the relevant template
	 */
	function render() {
		$GLOBALS['rss']->currentFeedsFeed = $this;
		include($GLOBALS['rss'] ->getTemplateFile("feedsfeed.php"));	
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
			$this->rlink = $this -> makeFolderUrl($name); // getPath().preg_replace("/[^a-zA-Z0-9_]/", "_", $name)."/";
		} else {
			$this->rlink = getPath()."feed.php?folder=$id";
		}
		$this->isRootFolder = ($id == 0);
		$this->rootList = $rootList;
	}
	
	function makeFolderUrl($fn) {
		return getPath(
			preg_replace('#\s#','_',sanitize($fn,RSS_SANITIZER_URL))
		) .'/';
	}
	
	function render() {
		if ((!getConfig('rss.output.minimalchannellist')) || ($this->feeds)) { 
		 	$GLOBALS['rss']->currentFeedsFolder = $this; 
		 	require($GLOBALS['rss'] ->getTemplateFile("feedsfolder.php")); 
		}
	}
}

class FeedList {

	var $collapsed_folders = array ();
	var $collapsed_ids = array ();
	var $folders = array ();
	var $activeId;
	var $feedCount = 0;
	var $columnTitle;
	var $stats;
	
	function FeedList($activeId) {
		_pf('FeedList() ctor');
		$this ->columnTitle= __('Feeds');
		$this->activeId = $activeId;
		$this->loadCollapsedState();
		$this->populate();
	}
	
	function getStats() {
		_pf('getStats()');
		_pf(' ... unreadCount');

		$unread = getUnreadCount(null, null);
		_pf(' ... done: unreadCount');		

		_pf(' ... totalCount');		
		$sql ="select count(*) from ".getTable("item") . "i " 
			   ."inner join " . getTable('channels') . " c "
         ."  on c.id = i.cid "
				 ." where not(i.unread & ".RSS_MODE_DELETED_STATE.") "
			   ." and not (c.mode & " .RSS_MODE_DELETED_STATE.")"
			. (hidePrivate()? " and not(unread & ".RSS_MODE_PRIVATE_STATE.")":"");
			
			//echo $sql;
			$res = rss_query($sql);
		list ($total) = rss_fetch_row($res);
		_pf(' ... done: totalCount');		
		

		_pf(' ... feedsCount');				
		$res = rss_query("select count(*) from "
			.getTable("channels")." where not(mode & ".RSS_MODE_DELETED_STATE.")"
			. (hidePrivate()? " and not(mode & ".RSS_MODE_PRIVATE_STATE.")":"")
			);
			
		list ($channelcount) = rss_fetch_row($res);
		_pf(' ... done: feedsCount');				
		
		$this ->stats = sprintf(__('<strong>%d</strong> items (<strong id="fucnt">%d</strong> unread) in <strong>%d</strong> feeds'), $total, $unread, $channelcount);
		_pf('done: getStats()');
		return $this -> stats;
	}
	
	
	function loadCollapsedState() {
	    _pf('FeedList->loadCollapsedState()...');
	    
		if (getConfig('rss.output.channelcollapse')) {
	
			//read per-user stored collapsed folders
			if (array_key_exists(COLLAPSED_FOLDERS_COOKIE, $_COOKIE)) {
				$this->collapsed_ids = explode(":", $_COOKIE[COLLAPSED_FOLDERS_COOKIE]);
			}  elseif (empty($this->collapsed_ids) && getConfig("rss.output.channelcollapsedefault")) {
			   // Lets collapse all folders
			   $res = rss_query("select id from " . getTable('folders') . " where id != 0");
			   while (list ($this->collapsed_ids[]) = rss_fetch_row($res)) {}
			   if (!headers_sent()) { // Sajax does not allow us to set cookies
			    setcookie(COLLAPSED_FOLDERS_COOKIE, 
				  implode(":", $this->collapsed_ids ) , time()+COOKIE_LIFESPAN,getPath());
			   }
			}
	
			//get unread count per folder                                                                        
			$sql = "select f.id, f.name, count(*) as cnt "
			." from " .getTable('item') ." i "
			." inner join " . getTable('channels') . " c on c.id = i.cid "
			." inner join " . getTable('folders') ." f on f.id = c.parent "
			." where i.unread & ". RSS_MODE_UNREAD_STATE
			." and not(i.unread & ". RSS_MODE_DELETED_STATE .")";
			if (hidePrivate()) {
				$sql .=" and not(unread & " . RSS_MODE_PRIVATE_STATE .") ";
			}
			$sql .= " and not(c.mode & " . RSS_MODE_DELETED_STATE .") ";
			$sql .= " group by f.id";
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
		$sql = "select "." c.id, c.title, c.url, c.siteurl, f.name, c.parent, c.icon, c.descr, c.mode "." from ".getTable("channels")." c "
		."inner join " . getTable("folders")." f on f.id = c.parent";

		if (hidePrivate()) {
			$sql .= " and not(c.mode & ".RSS_MODE_PRIVATE_STATE.") ";
		}

		$sql .= " and not(c.mode & ".RSS_MODE_DELETED_STATE.") ";

		if (getConfig('rss.config.absoluteordering')) {
			$sql .= " order by f.position asc, c.position asc";
		} else {
			$sql .= " order by f.name, c.title asc";
		}
		$res = rss_query($sql);
		$this -> feedCount = rss_num_rows($res);
		
		$ucres = rss_query ("select cid, count(*) from " .getTable("item")
        ." where unread & "  . RSS_MODE_UNREAD_STATE
        . " and not(unread & " . RSS_MODE_DELETED_STATE .") group by cid");
		$uc = array();
		while (list($uccid,$ucuc) = rss_fetch_row($ucres)) {
			$uc[$uccid]=$ucuc;
		}
		  
		while (list ($cid, $ctitle, $curl, $csiteurl, $fname, $cparent, $cico, $cdescr, $cmode) = rss_fetch_row($res)) {
			$ucc= 0;
			if (array_key_exists($cid,$uc)) {
				$ucc=$uc[$cid];
			} 
			$f = new FeedListItem($cid, $ctitle, $curl, $csiteurl, $fname, $cparent, $cico, $cdescr, $cmode, $ucc);
			$f -> isActiveFeed = ($this->activeId && $cid == $this->activeId ); 
			if (!array_key_exists($cparent, $this->folders)) {
				$this->folders[$cparent] = new FeedFolder($fname, $cparent,$this);
			}
			//$this->folders[$cparent]->feeds[] = $f;
			if(($ucc != 0) || (!getConfig('rss.output.minimalchannellist'))) { 
			 	$this->folders[$cparent]->feeds[] = $f; 
			}
			$this->folders[$cparent]->isCollapsed = in_array($cparent, $this->collapsed_ids) && ($cparent > 0);

		}
		_pf('done');
	}

	function render() {
		_pf('FeedList->render() ...');
		include($GLOBALS['rss'] ->getTemplateFile("feeds.php"));
		_pf('done');		
	}
}
?>
