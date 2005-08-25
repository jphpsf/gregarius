<?php

class CatFolder extends FeedFolder{
	var $isRootFolder = false;

	function CatFolder($name, $id, &$rootList) {
		$this->name = $name;
		$this->id = $id;
		if (getConfig('rss.output.usemodrewrite')) {
			$this->rlink = getPath().preg_replace("/[^a-zA-Z0-9_]/", "_", $name)."/";
		} else {
			$this->rlink = getPath()."feed.php?vfolder=$id";
		}
		$this->rootList = $rootList;
	}
	
	function render() {		
		$GLOBALS['rss']->currentFeedsFolder = $this;
		require($GLOBALS['rss'] ->getTemplateFile("feedsfolder.php"));
	}
}

class CatList extends FeedList {
	
	var $tagCnt = 0;
	var $taggedFeedCnt = 0;
	
	function CatList() {
		//parent::FeedList(null);
		$this -> populate();
		$this->loadCollapsedState();

		$GLOBALS['rss']-> feedList = $this;
		$this -> 	columnTitle = LBL_TAG_FOLDERS;
	}
	
	
	
	function getStats() {
		return sprintf(LBL_CATCNT_PF, $this -> taggedFeedCnt, $this -> tagCnt, 0);
	}
	
	
	function populate() {

		
		////// actual feeds ///////
		$this->folders = array();
		_pf('CatList->populate() ...');
		$sql = "select "
		 ." c.id, c.title, c.url, c.siteurl, t.tag, c.parent, c.icon, c.descr, c.mode, t.id "
		 ." from "
		 .getTable('channels') ." c, "
		 .getTable('metatag') ." m, "
		 .getTable('tag') . " t "
		 ." where m.fid = c.id and m.ttype = 'channel' "
		 ." and m.tid = t.id ";


		if (hidePrivate()) {
			$sql .= " and !(c.mode & ".FEED_MODE_PRIVATE_STATE.") ";
		}

		$sql .= " and !(c.mode & ".FEED_MODE_DELETED_STATE.") ";

		$sql .= " order by t.tag asc"; 
		
		
		$res = rss_query($sql);
		$this -> taggedFeedCnt = rss_num_rows($res);
		
		// get # of unread items for each feed
		$ucres = rss_query ("select cid, count(*) from " .getTable("item")
		 ." where unread & "  . FEED_MODE_UNREAD_STATE
		 . " and !(unread & " . FEED_MODE_DELETED_STATE
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