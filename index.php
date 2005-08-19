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

require_once("init.php");

// Show unread items on the front page?
// default to the config value, user can override this via a cookie
$show_what = (getConfig('rss.output.noreaditems') ?
	SHOW_UNREAD_ONLY : SHOW_READ_AND_UNREAD );
	
if (array_key_exists(SHOW_WHAT,$_POST)) {
	$show_what = $_POST[SHOW_WHAT];
	$period = time()+COOKIE_LIFESPAN;
	setcookie(SHOW_WHAT, $show_what , $period,getPath());  
} elseif (array_key_exists(SHOW_WHAT,$_COOKIE)) {
	$show_what = $_COOKIE[SHOW_WHAT];
}


if (array_key_exists('metaaction', $_POST)
    && $_POST['metaaction'] != ""
    && trim($_POST['metaaction']) == trim('LBL_MARK_READ') && 
	!hidePrivate()) {
    
    $sql = "update " .getTable("item") . " set unread=unread & "
     .SET_MODE_READ_STATE ." where unread  & " . FEED_MODE_UNREAD_STATE;
     
   if (hidePrivate()) {
	  	$sql .= " and !(unread & " . FEED_MODE_PRIVATE_STATE . ")";
	 }
	 
	 rss_query( $sql );
	  
}

if (array_key_exists('update',$_REQUEST)) {
    update("");
}


if (array_key_exists('logout',$_GET)) {
	logoutPrivateCookie();
}


$cntUnread = unreadItems($show_what);
if ($show_what != SHOW_UNREAD_ONLY || $cntUnread == 0) {
	readItems($cntUnread);
} 


$GLOBALS['rss'] -> header = new Header("",LOCATION_HOME,array('cid'=>null,'fid'=>null));
$GLOBALS['rss'] -> feedList = new FeedList(false);
$GLOBALS['rss'] -> renderWithTemplate('index.php');



function unreadCallback($show_what) {
    showViewForm($show_what);
	markAllReadForm();
}

function unreadItems($show_what) {

    _pf('populate unread items');
	$unreadItems = new ItemList();
	$numItems = getConfig('rss.output.numitemsonpage');
	
	
	
	if($numItems){
		$unreadItems -> populate("i.unread & " . FEED_MODE_UNREAD_STATE, "", 0, $numItems);
	}else{
		$unreadItems -> populate("i.unread & " . FEED_MODE_UNREAD_STATE);
	}
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

function readItems($cntUnread) {
    
    _pf('read items');
    $readItems = new ItemList();
	$readItems -> setRenderOptions(IL_TITLE_NO_ESCAPE);

	if (getConfig('rss.config.feedgrouping')) {
		$sql = "select "
          ." c.id"
          ." from " 
            .getTable("channels") . " c, " 
            .getTable("folders") ." f "
          ." where c.parent = f.id ";
    
        $sql .= " and !(c.mode & " . FEED_MODE_DELETED_STATE  .") ";
        
        if (getConfig('rss.config.absoluteordering')) {
        	$sql .= " order by f.position asc, c.position asc";
        } else {
        	$sql .=" order by f.name asc, c.title asc";
        }
    
    	$res1=rss_query($sql);
    	while (list($cid) = rss_fetch_row($res1)) {
			$readItems->populate(" !(i.unread & ". FEED_MODE_UNREAD_STATE  .") and i.cid= $cid", "", 0, 2);
		}  
		
		
	} else {
		///// BUGGY! //////
		$itemsOnPage = getConfig('rss.output.numitemsonpage');
		if (!$itemsOnPage) {
			// quite arbitrary: display 50 read items at most
			$limit = 50;
		} else {
			$limit =  $itemsOnPage - $cntUnread;
		}
		 
		if ($limit <= 0) {
			return;
		}
		$readItems -> populate("!(i.unread & " . FEED_MODE_UNREAD_STATE .")", "", 0, $limit);
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
	
   echo "<form action=\"". getPath() ."\" method=\"post\">"
      ."<p><input accesskey=\"m\" type=\"submit\" name=\"action\" value=\"". LBL_MARK_READ ." \"/></p>"
      ."<p><input type=\"hidden\" name=\"metaaction\" value=\"LBL_MARK_READ\"/></p>"
      ."</form>\n";
}


?>
