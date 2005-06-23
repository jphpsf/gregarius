<?php

###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2005 Marco Bonetti
#
###############################################################################
# File: $Id: en.php 535 2005-06-17 22:43:47Z mbonetti $ $Name:  $
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
	
	/**
	 * ctor
	 */
	function Item($id, $title, $url, $parent, $description, $date, $isPubDate, $unread) {
		$this->id = $id;
		$this->flags = $unread;
		$this->title = $title?$title:"[nt]";		
		$this->escapedTitle = preg_replace("/[^A-Za-z0-9\.]/", "_", $title);
		$this->url = $url;
		$this->feed = $parent;
		$this->description = $description;
		$this->date = $date;
		$this->isPubDate = $isPubDate;
		
		$this ->isUnread 	= $unread & FEED_MODE_UNREAD_STATE;
		$this ->isPrivate	= $unread & FEED_MODE_PRIVATE_STATE;
		$this ->isDeleted	= $unread & FEED_MODE_DELETED_STATE;
		$this ->isSticky	= $unread & FEED_MODE_STICKY_STATE;
	
	}

	/**
	 * Renders a single RSS item
	 */
	function render($cntr, &$parent) {
		
		// tags:
		if (array_key_exists($this->id,$parent->tags)) {
			$tags = $parent->tags[$this->id];	
		} else {
			$tags = array();
		}
		
		$cls = "item";
		if (($cntr ++ % 2) == 0) {
			$cls .= " even";
		} else {
			$cls .= " odd";
		}
		
		if ($this-> isUnread) {
			$cls = "item unread";			
		} 
		
		// some url fields are juste guid's which aren't actual links
		$isUrl = (substr($this -> url, 0, 4) == "http");
		echo "\t<li class=\"$cls\">\n";
		
		if (getConfig('rss.output.usepermalinks')) {			
			list ($ply, $plm, $pld) = explode(":", rss_date("Y:m:d", $this->date, false));
			$ptitle = (LBL_PL_FOR."'" .$parent -> escapedTitle."/$ply/$plm/$pld/". $this->escapedTitle ."'");
			echo "\t\t<a class=\"plink\" title=\"$ptitle\" ";
		
			if ($this->escapedTitle != "" && getConfig('rss.output.usemodrewrite')) {
				echo "href=\"".getPath().$parent -> escapedTitle."/$ply/$plm/$pld/". $this->escapedTitle ."\">";
			} else {
				echo "href=\"".getPath()."feed.php?channel=".$parent->cid."&amp;iid=".$this->id."&amp;y=$ply&amp;m=$plm&amp;d=$pld\">";
			}
			echo "\n\t\t\t<img src=\"".getThemePath()."media/pl.gif\" alt=\"$ptitle\" />\n"."\t\t</a>\n";
		}
		
		if (!hidePrivate()) {
			echo "\t\t<a id=\"sa".$this->id."\" href=\"#\" onclick=\"_es(".$this->id.",".$this->flags."); return false;\">\n"
					."\t\t\t<img src=\"".getThemePath()."media/edit.gif\" alt=\"".LBL_ADMIN_EDIT."\" />\n"	
				 ."\t\t</a>\n";
		}
		
		echo "\t\t<h4>";

		if ($isUrl) {
			echo "<a href=\"".$this->url."\">".$this->title."</a>";
		} else {
			echo $this->title;
		}
		
		echo "</h4>\n";
		
		if (!hidePrivate()) {
			echo "\t\t<div id=\"sad".$this->id."\" style=\"display:none\" ></div>";
		}
		
		if ($this->date) {
			$date_lbl = rss_date(getConfig('rss.config.dateformat'), $this->date);
		
			// make a permalink url for the date (month)
			if (strpos(getConfig('rss.config.dateformat'), 'F') !== FALSE) {
				$mlbl = rss_date('F', $this->date, false);
				$murl = makeArchiveUrl($this->date, $parent->escapedTitle, $parent->cid, false);
				$date_lbl = str_replace($mlbl, "<a href=\"$murl\">$mlbl</a>", $date_lbl);
			}
		
			// make a permalink url for the date (day)
			if (strpos(getConfig('rss.config.dateformat'), 'jS') !== FALSE) {
				$dlbl = rss_date('jS', $this->date, false);
				$durl = makeArchiveUrl($this->date, $parent->escapedTitle, $parent->cid, true);
				$date_lbl = str_replace($dlbl, "<a href=\"$durl\">$dlbl</a>", $date_lbl);
			}
			echo "\t\t<h5>".($this->isPubDate?LBL_POSTED:LBL_FETCHED)."$date_lbl</h5>\n";			
		}
		
		/// tags		
		echo "\t\t<h5>";
		if (getConfig('rss.output.usemodrewrite')) {
			echo "<a href=\"".getPath()."tag/\">";
		} else {
			echo "<a href=\"".getPath()."tags.php?alltags\">";
		}
		echo LBL_TAG_TAGS."</a>:&nbsp;<span id=\"t".$this->id."\">";
		foreach ($tags as $tag_) {
			$tag_ = trim($tag_);
			if (getConfig('rss.output.usemodrewrite')) {
				echo "<a href=\"".getPath()."tag/$tag_\">$tag_</a> ";
			} else {
				echo "<a href=\"".getPath()."tags.php?tag=$tag_\">$tag_</a> ";
			}
		}
		
		echo "</span>&nbsp;[<span id=\"ta".$this->id."\">"."<a href=\"#\" onclick=\"_et(".$this->id."); return false;\">".LBL_TAG_EDIT."</a>"."</span>]</h5>\n\n";
		
		/// /tags
				
		if ($this->description != "" && trim(str_replace("&nbsp;", "", $this->description)) != "") {
			echo "\t\t<div class=\"content\">".$this->description."\n\t\t</div>";
		}
		echo "\n\t</li>\n";
	}
}

/**
 * A feed mirrors the <code>channel</code> database table. It contains a list of Items
 */
class Feed {

	var $items = array ();
	var $tags = array ();
	var $title = "";
	var $cid = 0;
	var $iconUrl = "";
	
	var $hasUnreadItems = false;

	/**
	 * Feed constructor
	 */
	function Feed($title, $cid, $icon) {
		$this->title = $title;
		$this->cid = $cid;
		$this->iconUrl = $icon;
		$this->escapedTitle = preg_replace("/[^A-Za-z0-9\.]/", "_", $title);
	}

	/**
	 * Adds a single RSS item to this feed
	 */
	function addItem($item) {
		$this->items[] = $item;
		if ((!$this -> hasUnreadItems) && $item->flags & FEED_MODE_UNREAD_STATE) {
			$this -> hasUnreadItems = true;
		}
	}

	/**
	 * Specifies a tag for the given item in the items collection
	 */
	function setTag($iid, $tag) {
		if (!array_key_exists($iid, $this->tags)) {
			$this->tags[$iid] = array ();
		}
		$this->tags[$iid][] = $tag;
	}
	
	/**
	 * Renders a single Feed
	 */
	function render($options) {
		
		
		// Feed collapsion //
		$collapsed_ids = array ();
		if (getConfig('rss.output.channelcollapse')) {
			if (array_key_exists('collapsed', $_COOKIE)) {
				$collapsed_ids = explode(":", $_COOKIE['collapsed']);
			}
		}
		if (getConfig('rss.output.channelcollapse')) {
			$collapsed = in_array($this->cid, $collapsed_ids) 
							&& !($options & (IL_NO_COLLAPSE | IL_CHANNEL_VIEW)) 
							&& !($this->hasUnreadItems);
		
			if (array_key_exists('collapse', $_GET) && $_GET['collapse'] == $this -> cid) {
				// expanded -> collapsed
				$collapsed = true;
				if (!in_array($this -> cid, $collapsed_ids)) {
					$collapsed_ids[] = $this -> cid;
					$cookie = implode(":", $collapsed_ids);
					setcookie('collapsed', $cookie, time() + COOKIE_LIFESPAN);
				}
			}
			elseif (array_key_exists('expand', $_GET) && $_GET['expand'] == $this -> cid && $collapsed) {
				//	collapsed -> expanded
				$collapsed = false;
				if (in_array($this -> cid, $collapsed_ids)) {
					$key = array_search($this -> cid, $collapsed_ids);
					unset ($collapsed_ids[$key]);
					$cookie = implode(":", $collapsed_ids);
					setcookie('collapsed', $cookie, time() + COOKIE_LIFESPAN);
				}
			}
		
		} else {
			$collapsed = false;
		}
		
		
		
		// Channel collapsion //		
		$this -> title = rss_htmlspecialchars($this -> title);
		
		
		
		if ($this -> title != "" && !($options & IL_CHANNEL_VIEW)) {
			echo "\n<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->\n";
			echo "\n<h3" . ($collapsed ? " class=\"collapsed". (($this->hasUnreadItems & FEED_MODE_UNREAD_STATE) ? " unread" : "")."\"" : "").">\n";
	
			if (!($options & IL_NO_COLLAPSE) && getConfig('rss.output.channelcollapse') && !($this->hasUnreadItems & FEED_MODE_UNREAD_STATE)) {
				if ($collapsed) {
					$title = LBL_EXPAND." '".htmlentities($this->title)."'";
					echo "\t<a title=\"$title\" class=\"expand\" href=\"".$_SERVER['PHP_SELF']
						."?expand=" . $this ->cid ."#".$this->escapedTitle."\">\n"
						."\t<img src=\"".getThemePath()."media/plus.gif\" alt=\"$title\"/>"
						."</a>\n";
				} else {
					$title = LBL_COLLAPSE." '".htmlentities($this -> title)."'";
					echo "\t<a title=\"$title\" class=\"collapse\" href=\"".$_SERVER['PHP_SELF']
						."?collapse=" . $this->cid ."#".$this->escapedTitle."\">\n"
						."\t<img src=\"".getThemePath()."media/minus.gif\" alt=\"$title\"/>"
						."</a>\n";
				}
			}	elseif (getConfig('rss.output.showfavicons') && $this->iconUrl && (!($this->hasUnreadItems) || ($options & IL_FOLDER_VIEW))) {
				echo "\t<img src=\"". $this->iconUrl ."\" class=\"favicon\" alt=\"\"/>\n";
			}

			if (getConfig('rss.output.usemodrewrite')) {
				echo "\t<a name=\"".$this->escapedTitle."\" href=\"".getPath().$this->escapedTitle."/\">".$this -> title ."</a>\n";
			} else {
				echo "\t<a name=\"".$this->escapedTitle."\" href=\"".getPath()."feed.php?channel=".$this->cid ."\">".$this -> title."</a>\n";
			}	
			echo "</h3>\n";
		}
		echo "<ul>\n";
		$cntr = 0;
		if (!$collapsed) {
			foreach ($this -> items as $item) {
				
				$item->render($cntr++, $this);	
			}
		}
		echo "</ul>\n";
		
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
	
	var $__callbackFn = null;
	var $__callbackParams = null;
	
	var $unreadCount = 0;
	var $readCount = 0;
	
	var $itemCount = 0;

	var $rowCount = 0;
        var $actualCount = 0;
	var $allTags = array();
	
	
	function ItemList() {}

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
			."i.url, i.description, c.icon, "
			." if (i.pubdate is null, unix_timestamp(i.added), unix_timestamp(i.pubdate)) as ts, "
			." i.pubdate is not null as ispubdate, i.id  "
			." from ".getTable("item") ." i, "
			.getTable("channels")." c, "
			.getTable("folders") ." f "
			." where "
			." i.cid = c.id and "
			." f.id=c.parent and "." !(c.mode & ".FEED_MODE_DELETED_STATE.") and "
			." !(i.unread & ".FEED_MODE_DELETED_STATE.") and ";

		if (hidePrivate()) {
			$sql .= " !(i.unread & ".FEED_MODE_PRIVATE_STATE.") and ";
		}

		if ($sqlWhere) {
			$sql .= $sqlWhere ." and ";
		}
		$sql .= " 1=1 ";
		
		/// Order by 
		if ($sqlOrder == "") {
			if (getConfig('rss.config.absoluteordering')) {
				$sql .= " order by f.position asc, c.position asc";
			} else {
				$sql .= " order by c.parent asc, c.title asc";
			}
			$sql .= ", i.added desc, i.id asc";
		} else {
			$sql .= " $sqlOrder ";	
		}
		
		if ($this -> __callbackFn == null && $itemCount > 0) {		
			$sql .= " limit $startItem, $itemCount";
		}
		
		//echo $sql;		
		$iids = array();
		$res = rss_query($sql);
		$this -> rowCount = $this -> actualCount =  rss_num_rows($res);
		$skipItems = 0;
		while (list ($ititle_, $ctitle_, $cid_, $iunread_, $iurl_, $idescr_, $cicon_, $its_, $iispubdate_, $iid_) = rss_fetch_row($res)) {
			

			
			// Built a new Item
			$i = new Item($iid_, $ititle_, $iurl_, $cid_, $idescr_, $its_, $iispubdate_, $iunread_);
			
			// If a filter was defined, test the item agains it
			if ($this -> __callbackFn && function_exists($this -> __callbackFn)) {
			    $i=  call_user_func($this -> __callbackFn, array($i), $this->__callbackParams);
				if ($i == null) {
				    $this -> actualCount--;
				    continue;
				}
			}
			
			// Skip the $startItem first items if we are filtering
			if ($this -> __callbackFn) {
				if ($skipItems++ < $startItem) {
					continue;
				}
			}

		    // no dupes, please
		    if (in_array($iid_,$iids)) {
			$this -> rowCount--;
			continue;                                                                                                                                                   
		    }
		    
		    // See if we have a channel for it		
		    if (!array_key_exists($cid_, $this->feeds)) {
				$this->feeds[$cid_] = new Feed($ctitle_, $cid_, $cicon_);
			}
			
		    		    
			// Add it to the channel
			$iids[] = $iid_;
			$this->feeds[$cid_]->addItem($i);			
			
			// Some stats...
			$this -> itemCount++;			
			if ($iunread_ & FEED_MODE_UNREAD_STATE) {
				$this -> unreadCount++;	
			} else {
				$this -> readCount++;	
			}
			
			// If we are filtering we can't use the (faster) sql limit: we have to
			// count items ourselves and break when we're done!
			if ($this -> __callbackFn) {
			    if ($this -> itemCount > $itemCount) {
					break;
				}
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
			." where m.tid = t.id and i.id=m.fid and m.fid in (".implode(",", $iids).")";
			
			$res = rss_query($sql);
			while (list ($tag_, $iid_, $cid_) = rss_fetch_row($res)) {
				$this -> feeds[$cid_] -> setTag($iid_, $tag_);
				if (array_key_exists($tag_,$this -> allTags)) {
		    		$this -> allTags[ $tag_ ]++;
				} else {
		    		$this -> allTags[ $tag_ ]=1;
				}
			}
		}
	}

	function render($title, $options = IL_NONE) {
		
		if (($this->readCount + $this->unreadCount) == 0) {
			return;	
		}
		
		$anchor = "";		
		if (!defined('FEEDCONTENT_ANCHOR_SET')) {
			$anchor = " id=\"feedcontent\"";
			define('FEEDCONTENT_ANCHOR_SET', true);
		}

		if ($title) {
			if (($options & IL_CHANNEL_VIEW) && getConfig('rss.output.showfavicons') && count($this -> feeds)) {
				$key = array_keys($this->feeds);
				$cicon = $this -> feeds[$key[0]] -> iconUrl;
			}
			elseif (($options & IL_FOLDER_VIEW) && getConfig('rss.output.showfavicons')) {
				$cicon = getThemePath()."media/folder.gif";
			}

			echo "\n\n<h2$anchor>"
				. (isset ($cicon) && $cicon != "" ? "<img src=\"$cicon\" class=\"favicon\" alt=\"$title\"/>" : "")
				. ($options & IL_TITLE_NO_ESCAPE ? $title : rss_htmlspecialchars($title))."</h2>\n";
		
		} elseif ($anchor != "") {
			echo "\n\n<a$anchor></a>\n";
		}

		
		foreach ($this -> feeds as $feed) {
			$feed -> render($options);
		}
	}

	function setItemFilterCallback($fname,$params) {
		$this -> __callbackFn = $fname;
		$this -> __callbackParams = $params;
	}
}
?>
