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

require_once("init.php");

// Show unread items on the front page?
// default to the config value, user can override this via a cookie
$show_what = (getConfig('rss.output.frontpage.mixeditems') ?
	SHOW_READ_AND_UNREAD : SHOW_UNREAD_ONLY);
	
if (array_key_exists(SHOW_WHAT,$_POST)) {
	$show_what = $_POST[SHOW_WHAT];
	$period = time()+COOKIE_LIFESPAN;
	setcookie(SHOW_WHAT, $show_what , $period,getPath());  
} elseif (array_key_exists(SHOW_WHAT,$_COOKIE)) {
	$show_what = $_COOKIE[SHOW_WHAT];
}


if (array_key_exists('metaaction', $_POST)
    && $_POST['metaaction'] != ""
    && trim($_POST['metaaction']) == trim('LBL_MARK_READ') 
    && !hidePrivate()) {
    
    $sql = "update " .getTable("item") . " set unread=unread & "
     .SET_MODE_READ_STATE ." where unread  & " . RSS_MODE_UNREAD_STATE;
     
   if (hidePrivate()) {
	  	$sql .= " and not(unread & " . RSS_MODE_PRIVATE_STATE . ")";
	 }

	if (array_key_exists('markreadids',$_POST)) {
		$sql .= " and id in (" . rss_real_escape_string($_POST['markreadids']) .")";
	}
	 rss_query( $sql );
	rss_invalidate_cache();  
}

if (array_key_exists('update',$_REQUEST)) {
    update("");
}


if (array_key_exists('logout',$_GET)) {
	logoutUserCookie();
}

$cntTotalItems = getConfig('rss.output.frontpage.numitems');

rss_plugin_hook('rss.plugins.frontpage.beforeunread', null);
$cntUnreadItems = unreadItems($show_what);

// Now we have to decide how many read items to display
$cntReadItems = getConfig('rss.output.frontpage.numreaditems');

rss_plugin_hook('rss.plugins.frontpage.beforeread', null);

if(($show_what == SHOW_UNREAD_ONLY) ) { 
	if (($cntUnreadItems == 0) && $cntTotalItems) { // we showed no unread items
		// Should we show some uread items?
		if($cntReadItems == -1) { 
			readItems($cntTotalItems);
		} else {
			readItems($cntReadItems);
		}
	}
} else { // We are showing read and unread items 
	if ($cntTotalItems){
		readItems($cntTotalItems - $cntUnreadItems);
	}
}

rss_plugin_hook('rss.plugins.frontpage.afterread', null);

$GLOBALS['rss'] -> header = new Header("",LOCATION_HOME,array('cid'=>null,'fid'=>null));
$GLOBALS['rss'] -> feedList = new FeedList(false);
$GLOBALS['rss'] -> renderWithTemplate('index.php');

/*
function getHiddenChannelIds() {
	static $hiddenIds;
	if ($hiddenIds == NULL) {
		$sql = "select fk_ref_object_id from " .getTable('properties')
		." where domain='feed' and property = 'Hide from front-page'";
		$rs = rss_query($sql);
		while (list($cid) = rss_fetch_row($rs)) {
			$hiddenIds[] = $cid;
		}
	}
	return $hiddenIds;
}
*/

function unreadCallback($show_what) {
    showViewForm($show_what);
	markAllReadForm();
}

function unreadItems($show_what) {

    _pf('populate unread items');
	$unreadItems = new ItemList();
	$numItems = getConfig('rss.output.frontpage.numitems');
	
	/*
	$hiddenIds = getHiddenChannelIds();
	if (count($hiddenIds)) {
		$sqlWhereHidden = " and c.id not in (" . implode(',',$hiddenIds) . ") ";
	} else {
		$sqlWhereHidden = "";
	}
	*/
	$sqlWhereHidden = "";
	
	$unreadItems -> populate("i.unread & " . RSS_MODE_UNREAD_STATE . $sqlWhereHidden, "", 0, $numItems,ITEM_SORT_HINT_UNREAD);
	
    _pf('end populate unread items');
	if ($unreadItems ->unreadCount) {
		$unreadItems -> preRender[] = array("unreadCallback",$show_what);
	}
	
	$ret = $unreadItems -> unreadCount;
	 
	 $unreadItems -> setTitle(sprintf(LBL_H2_UNREAD_ITEMS , $ret));
	 $unreadItems -> setRenderOptions(IL_TITLE_NO_ESCAPE);
	 $GLOBALS['rss'] -> appendContentObject($unreadItems);	
     _pf('appended unread items');
	 
    return $ret;
}

function readItems($limit) {
    
    _pf('read items');
   /*
   $hiddenIds = getHiddenChannelIds();
	if (count($hiddenIds)) {
		$sqlWhereHidden = " and c.id not in (" . implode(',',$hiddenIds) . ") ";
	} else {
		$sqlWhereHidden = "";
	}
	*/

	
    $readItems = new ItemList();
	$readItems -> setRenderOptions(IL_TITLE_NO_ESCAPE);

	if (getConfig('rss.config.feedgrouping')) {
	 	if ($limit <= 0) {
			return;
		}
  	$sql = "select "
          ." c.id"
          ." from " 
            .getTable("channels") . " c, " 
            .getTable("folders") ." f "
          ." where c.parent = f.id ";
          
    	 // $sql .= $sqlWhereHidden;
    
        $sql .= " and not(c.mode & " . RSS_MODE_DELETED_STATE  .") ";
        
        if (getConfig('rss.config.absoluteordering')) {
        	$sql .= " order by f.position asc, c.position asc";
        } else {
        	$sql .=" order by f.name asc, c.title asc";
        }
    	$res1=rss_query($sql);
    	while ($readItems->itemCount < $limit && (list($cid) = rss_fetch_row($res1))) {
    		$sqlWhere  = " not(i.unread & ". RSS_MODE_UNREAD_STATE  .") and i.cid= $cid";
    		$sqlWhere .= " and i.pubdate <= now() ";
    		
			$readItems->populate($sqlWhere, "", 0, 2, ITEM_SORT_HINT_READ);
			//what if we have less than 2 items. 
		}  
		
		
	} else {
		 
		if ($limit <= 0) {
			return;
		}
		$sqlWhere  = " not(i.unread & ". RSS_MODE_UNREAD_STATE  .")  ";
    	$sqlWhere .= " and i.pubdate <= now() ";
    //	$sqlWhere .= $sqlWhereHidden;
		$readItems -> populate($sqlWhere, "", 0, $limit, ITEM_SORT_HINT_READ);
		$readItems -> setRenderOptions(IL_NO_COLLAPSE | IL_TITLE_NO_ESCAPE);

	}

		
	$readItems -> setTitle(LBL_H2_RECENT_ITEMS);
	
	$GLOBALS['rss'] -> appendContentObject($readItems);	
	_pf('end read items');

}

function markAllReadForm() {
	if (hidePrivate()) {
		return;
	}
	
	if (!defined('MARK_READ_ALL_FORM')) {
		define ('MARK_READ_ALL_FORM',true);
	}	
	
   echo "<form action=\"". getPath() ."\" method=\"post\">\n"
      ."<p><input accesskey=\"m\" type=\"submit\" name=\"action\" value=\"". LBL_MARK_READ ." \"/></p>\n"
      ."<p><input type=\"hidden\" name=\"metaaction\" value=\"LBL_MARK_READ\"/>\n"
      ."<input type=\"hidden\" name=\"markreadids\" value=\"".implode(",",$GLOBALS['rss']->getShownUnreadIds())."\" />\n"
      ."</p></form>\n";
}


?>
