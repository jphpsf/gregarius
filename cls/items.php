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


/**
 * The Item class holds a single RSS item, mostly mimicking the 
 * structure of the item databse table
 */
class Item {

	
	var $flags;
	var $title;
	var $url;
	var $id;
	var $feed;
	var $description;
	var $date;
	var $isPubDate;

	var $isUnread;
	var $isPrivate;
	var $isDeleted;
	var $isSticky;
	
	var $escapedTitle;
	var $cntr;
	var $parent;
	var $tags;
	var $author;
	var $rating;
	var $rss;
	/**
	 * ctor
	 */
	function Item($id, $title, $url, $parent, $author, $description, $date, $isPubDate, $unread, $rating) {
		$this->rss = &$GLOBALS['rss'];
		$this->id = $id;
		$this->flags = $unread;
		$this->title = $title?$title:"[nt]";		
		$this->escapedTitle = preg_replace("/[^A-Za-z0-9%\.]/", "_", utf8_uri_encode($title));
		$this->url = $url;
		$this->feed = $parent;
		$this->author = $author;
		if ($description) {
			$this->description = $description;
		} elseif($title) {
			$this -> description = $title;
		}
		$this->date = $date;
		$this->isPubDate = $isPubDate;
		$this -> tags=array();
		$this -> rating = $rating;
		$this ->isUnread 	= $unread & FEED_MODE_UNREAD_STATE;
		$this ->isPrivate	= $unread & FEED_MODE_PRIVATE_STATE;
		$this ->isDeleted	= $unread & FEED_MODE_DELETED_STATE;
		$this ->isSticky	= $unread & FEED_MODE_STICKY_STATE;
		//$this -> key = md5(rand(0,10000));
		
	}
	
	function setParent(&$parent) {
		$this-> parent=$parent;
	}

	
	/**
	 * Renders a single RSS item
	 */
	function render() {
		$this-> rss -> currentItem = $this;
		require($this-> rss -> getTemplateFile("item.php"));
	}
}

/**
 * A feed mirrors the <code>channel</code> database table. It contains a list of Items
 */
class Feed {

	var $items = array ();
	//var $tags = array ();
	var $title = "";
	var $cid = 0;
	var $iconUrl = "";
	
	var $hasUnreadItems = false;
	var $collapsed = false;
	
	/**
	 * Feed constructor
	 */
	function Feed($title, $cid, $icon) {
		$this -> rss = &$GLOBALS['rss'];
		$this->title = rss_htmlspecialchars($title);
		$this->cid = $cid;
		$this->iconUrl = $icon;
		$this->escapedTitle = preg_replace("/[^A-Za-z0-9\.]/", "_", $title);
		
	} 
	
	function setCollapseState($options) {
		// Feed collapsion //
		$collapsed_ids = array ();
		if (getConfig('rss.output.channelcollapse')) {
			if (array_key_exists('collapsedfeeds', $_COOKIE)) {
				$collapsed_ids = explode(":", $_COOKIE['collapsedfeeds']);
			}
		}
		
		if (getConfig('rss.output.channelcollapse')) {
			$this->collapsed = in_array($this->cid, $collapsed_ids) 
							&& !($options & (IL_NO_COLLAPSE | IL_CHANNEL_VIEW)) 
							&& !($this->hasUnreadItems);
		
			if (array_key_exists('collapse', $_GET) && $_GET['collapse'] == $this -> cid) {
				// expanded -> collapsed
				$this->collapsed = true;
				if (!in_array($this -> cid, $collapsed_ids)) {
					$collapsed_ids[] = $this -> cid;
					$cookie = implode(":", $collapsed_ids);
					setcookie('collapsedfeeds', $cookie, time() + COOKIE_LIFESPAN);
					//echo $this->cid . " -> collapsed";
				}
			}
			elseif (array_key_exists('expand', $_GET) && $_GET['expand'] == $this -> cid && $this->collapsed) {
				//	collapsed -> expanded
				$this->collapsed = false;
				if (in_array($this -> cid, $collapsed_ids)) {
					$key = array_search($this -> cid, $collapsed_ids);
					unset ($collapsed_ids[$key]);
					$cookie = implode(":", $collapsed_ids);
					setcookie('collapsedfeeds', $cookie, time() + COOKIE_LIFESPAN);
				}
			}
		
		} else {
			$this->collapsed = false;
		}
		if ($this->hasUnreadItems) {
			$this->collapsed = false;
		}

		//echo $this-> collapsed?"Collapsed":"expanded";
		return $this->collapsed;
		
	}

	/**
	 * Adds a single RSS item to this feed
	 */
	function addItem(&$item) {
		$item-> setParent($this);
		$this->items[] = $item;
		
		if ((!$this -> hasUnreadItems) && $item->flags & FEED_MODE_UNREAD_STATE) {
			$this -> hasUnreadItems = true;
		}
	}

	/**
	 * Renders a single Feed
	 */
	function render() {
		
		$this-> rss -> currentFeed = &$this;
		//echo $GLOBALS['rss']->renderOptions;
		$this -> setCollapseState($this-> rss ->renderOptions);
		require($this-> rss ->getTemplateFile("feed.php"));
	}
}


/**
 * The ItemList is the main entry point for rendering items: one would:
 * <ul>
 * <li>Instantiate a new ItemList</li>
 * <li>Invoke the <code>populate</code> method, giving specific 
 * information on what should be fetched from the database via the sqlWhere parameter</li>
 * </li>Render the list</li>
 * </ul>
 */
class ItemList {

	var $feeds = array ();
	
	var $unreadCount = 0;
	var $readCount = 0;
	
	var $itemCount = 0;

	var $rowCount = 0;
	var $allTags = array();
	var $renderOptions = IL_NONE;
	var $title = "";
	
	var $preRender = array();
	var $beforeList = "";
	var $afterList = "";
	
	var $ORDER_BY_UNREAD_FIRST=null;
	
	var $iidInCid = array();
	var $rss;
	
	function ItemList() {
	
		$this -> rss = &$GLOBALS['rss'];
		
		// make sure we have  a default rendering options defined
		$this -> setRenderOptions( IL_NONE );
		
		// Predefined alternate ordering
		$this -> ORDER_BY_UNREAD_FIRST = " order by i.unread & " . FEED_MODE_UNREAD_STATE . " desc, ";
		if (getConfig('rss.config.absoluteordering')) {
			$this -> ORDER_BY_UNREAD_FIRST .= " f.position asc, c.position asc";
		} else {
			$this -> ORDER_BY_UNREAD_FIRST .= " f.name asc, c.title asc";
		}
		$this -> ORDER_BY_UNREAD_FIRST .= ", i.added desc, i.id asc";

	}

	/**
	 * Populates a an ItemList with items from the Database. Note that this methdo
	 * can be invoked several times on the same ItemList object instance: upon each
	 * call the new items will be aggregated to the existing ones.
	 * 
	 * @param sqlWhere specifies what should be fetched
	 * @param sqlOrder (optional) specifies a different item ordering
	 * @param sqlLimit (optional) specifies how many items should be fetched
	 */
	function populate($sqlWhere, $sqlOrder="", $startItem = 0, $itemCount = -1) {

		$sql = "select i.title,  c.title, c.id, i.unread, "
			."i.url, i.author, i.description, c.icon, "
			." unix_timestamp(ifnull(i.pubdate,i.added)) as ts, "
			." i.pubdate is not null as ispubdate, i.id, r.rating  "
			." from ".getTable("item") ." i "
			." left join "
			. getTable("rating") ." r on (i.id = r.iid), "
			.getTable("channels")." c, "
			.getTable("folders") ." f "

			." where "
			." i.cid = c.id and "
			." f.id=c.parent and "." not(c.mode & ".FEED_MODE_DELETED_STATE.") and "
			." not(i.unread & ".FEED_MODE_DELETED_STATE.") and ";

		if (hidePrivate()) {
			$sql .= " not(i.unread & ".FEED_MODE_PRIVATE_STATE.") and ";
		}

		if ($sqlWhere) {
			$sql .= $sqlWhere ." and ";
		}
		$sql .= " 1=1 ";
		
		/// Order by
		$sqlOrder = rss_plugin_hook("rss.plugins.items.order",$sqlOrder);
		if ($sqlOrder == "") {
			$sql .= " order by  ";
			if (!getConfig('rss.config.feedgrouping')) {
				if(getConfig('rss.config.datedesc')){
					$sql .= " ts desc, f.position asc, c.position asc ";
				}else{
					$sql .= " ts asc, f.position asc, c.position asc ";
				}
			} elseif (getConfig('rss.config.absoluteordering')) {
				$sql .= " f.position asc, c.position asc";
			} else {
				$sql .= " f.name asc, c.title asc";
			}
			if(getConfig('rss.config.datedesc')){
				$sql .= ", ts desc, i.id asc";
			}else{
				$sql .= ", ts asc, i.id asc";
			}
		} else {
			$sql .= " $sqlOrder ";	
		}
		
		if ($itemCount > 0) {		
			$sql .= " limit $startItem, $itemCount";
		}
		//echo $sql;		
		$iids = array();
		$res = $this->rss->db->rss_query($sql);
		$this -> rowCount = $this->rss->db->rss_num_rows($res);
		$prevCid = -1;
		$curIdx = 0;
		$f=null;
		while (list ($ititle_, $ctitle_, $cid_, $iunread_, $iurl_, $iauthor_, $idescr_, $cicon_, $its_, $iispubdate_, $iid_, $rrating_) = $this->rss->db->rss_fetch_row($res)) {
			
			// Built a new Item
			$i = new Item($iid_, $ititle_, $iurl_, $cid_, $iauthor_, $idescr_, $its_, $iispubdate_, $iunread_, $rrating_);
			
		    // no dupes, please
		    if (in_array($iid_,$iids)) {
				$this -> rowCount--;
				continue;                                                                                                                                                   
		    }

			// Allow for some item filtering before it is rendered
			if (($i = rss_plugin_hook('rss.plugins.items.beforerender', $i)) == null) {
				$this -> rowCount--;
				continue;
			}
					    
		    // See if we have a channel for it		
		    if ($cid_ != $prevCid) {
				$f = new Feed($ctitle_, $cid_, $cicon_);
				$this->feeds[] = $f;
				$curIdx = count($this->feeds)-1;
				$prevCid = $cid_;
			}
		   
			
			$this -> iidInCid[$iid_] = $curIdx;
			
					    
			// Add it to the channel
			$iids[] = $iid_;
			$this -> feeds[$curIdx] ->addItem($i);
			
			// Some stats...
			$this -> itemCount++;			
			if ($iunread_ & FEED_MODE_UNREAD_STATE) {
				$this -> unreadCount++;	
			} else {
				$this -> readCount++;	
			}
			
		}

		
		// Tags!
		if (count($iids)) {
			// fetch the tags for the items;
			$sql = "select t.tag,m.fid,i.cid "
			." from "
			.getTable('tag')." t, "
			.getTable('metatag')." m, "
			.getTable('item')." i "
			." where m.tid = t.id and i.id=m.fid and m.ttype = 'item' and m.fid in (".implode(",", $iids).")";
			
			$res = $this->rss->db->rss_query($sql);
			while (list ($tag_, $iid_, $cid_) = $this->rss->db->rss_fetch_row($res)) {
				if (array_key_exists($iid_, $this -> iidInCid)) {
					$idx = $this->iidInCid[$iid_];
					while (list($key, $item) = each($this ->feeds[$idx] -> items)) {
						if ($item -> id == $iid_) {
							$this ->feeds[$idx] -> items[$key] -> tags[] = $tag_;
							break;
						}				
					}
					reset($this ->feeds[$idx] -> items);
				} 
				

				if (array_key_exists($tag_,$this -> allTags)) {
		    		$this -> allTags[ $tag_ ]++;
				} else {
		    		$this -> allTags[ $tag_ ]=1;
				}
			}
		}
	}

	function removeItem($feedId,$itemId,$uncount) {
		unset($this->feeds[$feedId]->items[$itemId]);
		if ($uncount) {
		  $this ->itemCount--;
		}
		if (count($this->feeds[$feedId]->items) == 0) {
			unset($this->feeds[$feedId]);
		}
		
	}

	function setTitle($title) {
		$this->title=$title;
	}
	
	function setRenderOptions($options) {
		$this-> rss -> renderOptions |= $options;
		$this -> renderOptions = $options;
	}
	
	function render() {
		
		_pf("ItemList -> render()");
		
		if (($this->readCount + $this->unreadCount) == 0 && $this->beforeList == "") {
			return;	
		}
		
		$this-> rss -> currentItemList = $this;
		require($this-> rss ->getTemplateFile("itemlist.php"));
		
		_pf("done: ItemList -> render()");
		
		rss_plugin_hook('rss.plugins.items.afteritems', null);
	}

}


?>
