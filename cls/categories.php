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

define('COLLAPSED_CATEGORIES_COOKIE', 'collapsedcategories');

class CatFolder extends FeedFolder{
	var $isRootFolder = false;

	function CatFolder($name, $id, &$rootList) {
		$this->name = $name;
		$this->id = $id;
		if (getConfig('rss.output.usemodrewrite')) {
			$this->rlink = $this -> makeFolderUrl($name); //.preg_replace("/[^a-zA-Z0-9_]/", "_", $name)."/";
		} else {
			$this->rlink = getPath()."feed.php?vfolder=$id";
		}
		$this->rootList = $rootList;
	}
	
	function render() {		
		$GLOBALS['rss']->currentFeedsFolder = $this;
		include($GLOBALS['rss'] ->getTemplateFile("catfolder.php"));
	}
}

class CatList extends FeedList {
	
	var $tagCnt = 0;
	var $taggedFeedCnt = 0;
	var $feedCnt = 0;
	
	function CatList() {
		//parent::FeedList(null);
		$this->loadCollapsedState();
		$this -> populate();
		$this -> 	columnTitle = __('Categories');
		$GLOBALS['rss']-> feedList = $this;
	}
	
	function getFeedCount() {
		// cached?
		if ($this -> feedCnt > 0) {
			return $this -> feedCnt;
		}
	
		$sql = "select "
				." count(*) as cnt "
				." from ".getTable("channels")." c "
				." where  not(c.mode & ".RSS_MODE_DELETED_STATE.") ";

		if (hidePrivate()) {
			$sql .= " and not(c.mode & ".RSS_MODE_PRIVATE_STATE.") ";
		}


		list($this -> feedCnt) = rss_fetch_row(rss_query($sql));

		return $this -> feedCnt;
	}
	
	
	function getStats() {
		return sprintf(__('<strong>%d</strong> feeds in <strong>%d</strong> categories'), $this -> taggedFeedCnt, $this -> tagCnt, 0);
	}
	
	function loadCollapsedState() {
		_pf('CatList->loadCollapsedState()...');
		
		if (getConfig('rss.output.channelcollapse')) {

			//read per-user stored collapsed categories
			if (array_key_exists(COLLAPSED_CATEGORIES_COOKIE, $_COOKIE)) {
				$this->collapsed_ids = explode(":", $_COOKIE[COLLAPSED_CATEGORIES_COOKIE]);
			} elseif (empty($this->collapsed_ids) && getConfig("rss.output.channelcollapsedefault")) {
			   // Lets collapse all categories
			   $res = rss_query("select distinct(tid) from " . getTable('metatag') . " where ttype='channel'");
			   while (list ($this->collapsed_ids[]) = rss_fetch_row($res)) {
			   }
			   if (!headers_sent()) { // Sajax does not allow us to set cookies
			   	setcookie(COLLAPSED_CATEGORIES_COOKIE, 
				   implode(":", $this->collapsed_ids ) , time()+COOKIE_LIFESPAN,getPath());
			   }
			}

			//get unread count per folder
			$sql = "select m.tid, t.tag, count(*) as cnt "
			." from " . getTable('item') ." i "
			." inner join " . getTable('channels') . " c on c.id = i.cid "
			." inner join " . getTable('metatag') ." m on m.fid = c.id "
			." inner join " . getTable('tag') . " t on t.id = m.tid "
			." where i.unread & ". RSS_MODE_UNREAD_STATE
			." and not(i.unread & ". RSS_MODE_DELETED_STATE .")";
			if (hidePrivate()) {
				$sql .=" and not(unread & " . RSS_MODE_PRIVATE_STATE .") ";
			}
			$sql .= " and not(c.mode & " . RSS_MODE_DELETED_STATE .") "
			." group by m.tid";
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

	function populate() {

		
		////// actual feeds ///////
		$this->folders = array();
		_pf('CatList->populate() ...');
		$sql = "select "
		 ." c.id, c.title, c.url, c.siteurl, t.tag, c.parent, c.icon, c.descr, c.mode, t.id "
		 ." from " . getTable('channels') ." c "
		 ." inner join " . getTable('metatag') ." m on m.fid = c.id "
		 ." inner join " . getTable('tag') . " t on t.id = m.tid "
		 ." where m.ttype = 'channel' ";

		if (hidePrivate()) {
			$sql .= " and not(c.mode & ".RSS_MODE_PRIVATE_STATE.") ";
		}

		$sql .= " and not(c.mode & ".RSS_MODE_DELETED_STATE.") ";

		$sql .= " order by t.tag asc";
		
		if(!getConfig("rss.config.absoluteordering")){
			 $sql .= ", c.title asc";
		}
		
		$res = rss_query($sql);
		$this -> taggedFeedCnt = rss_num_rows($res);
		
		// get # of unread items for each feed
		$ucres = rss_query ("select cid, count(*) from " .getTable("item")
		 ." where unread & "  . RSS_MODE_UNREAD_STATE
		 . " and not(unread & " . RSS_MODE_DELETED_STATE
		 . ") group by cid");
		$uc = array();
		while (list($uccid,$ucuc) = rss_fetch_row($ucres)) {
			$uc[$uccid]=$ucuc;
		}
		
		while (list ($cid, $ctitle, $curl, $csiteurl, $fname, 
								$cparent, $cico, $cdescr, $cmode, $tid) = rss_fetch_row($res)) {
			
			$unread = 0;
			if(isset($uc[$cid])) $unread = $uc[$cid];
			$f = new FeedListItem($cid, $ctitle, $curl, $csiteurl, $fname, $cparent, $cico, $cdescr, $cmode, $unread);
			
			if (!array_key_exists($tid, $this->folders)) {
				$this->folders[$tid] = new CatFolder($fname, $tid,$this);
				$this -> tagCnt++;
			}
			
			$this->folders[$tid]->feeds[] = $f;
			$this->folders[$tid]->isCollapsed = in_array($tid, $this->collapsed_ids) && ($tid > 0);
			
		_pf('done');
		}
	}
	
}

?>
