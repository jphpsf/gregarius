<?
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003, 2004 Marco Bonetti
#
###############################################################################
# File: $Id$ $Name$
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



require_once('init.php');

if (
    defined('USE_MODREWRITE') 
    && USE_MODREWRITE 
    && array_key_exists('channel',$_REQUEST)    
    // this is nasty because a numeric feed title could break this
    && !is_numeric($_REQUEST['channel'])     
    ) {
    $sqlid =  preg_replace("/[^A-Za-z0-9\.]/","%",$_REQUEST['channel']);
    $res =  rss_query( "select id from channels where title like '$sqlid'" );
    
    if ( mysql_num_rows ( $res ) != 1) {
      rss_error("I'm having troubles fetching the channel " . $_REQUEST['channel'] . "! <!-- $sqlid -->");
    }
    
    
    list($cid) = mysql_fetch_row($res);
    
    // lets see if theres an item id as well

    $sqlid =  preg_replace("/[^A-Za-z0-9\.]/","%",$_REQUEST['iid']);
    $res =  rss_query( "select id from item where title like '$sqlid' and cid=$cid" );

    
    if ( mysql_num_rows ( $res ) >0) {
	list($iid) = mysql_fetch_row($res);  
    } else { 
	$iid = "";
    }
    

    

// no mod rewrite: ugly but effective
} elseif (array_key_exists('channel',$_REQUEST)) {
    $cid= (array_key_exists('channel',$_REQUEST))?$_REQUEST['channel']:"";
    $iid= (array_key_exists('iid',$_REQUEST))?$_REQUEST['iid']:"";
}

// If we have no channel-id somethign went terribly wrong.
// Redirect to index.php
if (!$cid) {
      header("Location: http://"
	     . $_SERVER['HTTP_HOST'] 
	     . dirname($_SERVER['PHP_SELF']) 
	     . "/"
	     );    
}

if (array_key_exists ('action', $_POST) && $_POST['action'] == MARK_CHANNEL_READ) {
    
    $sql = "update item set unread=0 where cid=$cid";
    rss_query($sql);
    
    // redirect to the next unread, if any.
    $sql = "select cid,title from item where unread=1 order by added desc limit 1";
    $res = rss_query($sql);
    list ($next_unread_id, $next_unread_title) = mysql_fetch_row($res);
    

    if ($next_unread_id == '') {
      header("Location: http://"
        . $_SERVER['HTTP_HOST']
	      . dirname($_SERVER['PHP_SELF'])
	      . "/"
      );
    } else {
	$cid = $next_unread_id;
    }
}

assert(is_numeric($cid));
assert(is_numeric($iid) || $iid=="");

if ($iid == "") {
    $res = rss_query("select title,icon from channels where id = $cid");
    list($title,$icon) = mysql_fetch_row($res);
} else {
   $res = rss_query ("select c.title, c.icon, i.title from channels c,item i where c.id = $cid and i.cid=c.id and i.id=$iid");
    list($title,$icon,$ititle) = mysql_fetch_row($res);
}


rss_header($title . " " . html_entity_decode(TITLE_SEP) ." " .html_entity_decode($ititle));
sideChannels($cid); 
if (defined('_DEBUG_') && array_key_exists('dbg',$_REQUEST)) {
    debugFeed($cid);
} else {
    items($cid,$title,$iid);
}
rss_footer();


function items($cid,$title,$iid) {
    echo "\n\n<div id=\"items\" class=\"frame\">";

    markReadForm($cid);
        
    $sql = " select i.title, i.url, i.description, i.unread, "
      ." unix_timestamp(i.pubdate) as ts, c.icon, c.title, i.id "
      ." from item i, channels c "
      ." where i.cid = $cid and c.id = $cid ";

    
    if ($iid != "") {
	$sql .= " and i.id=$iid";
    }
    

    
    if  (isset($_REQUEST['unread']) && $iid == "") {
      $sql .= " and unread=1 ";
    }
    
    $sql .=" order by i.added desc, i.id asc";
    //$sql .= " order by i.id asc";
      
    if (!array_key_exists('all', $_REQUEST) && !array_key_exists('unread', $_REQUEST)) {
      $sql .= " limit ". ITEMS_ON_CHANNELVIEW;
    }
    
    $res = rss_query($sql);    
    $items = array();
        
    $iconAdded = false;
      
    while (list($ititle, $iurl, $idescription, $iunread, $its, $cicon, $ctitle, $iid) =  mysql_fetch_row($res)) {
      	$items[]=array($cid, $ctitle, $cicon, $ititle,$iunread,$iurl,$idescription, $its, $iid);
      	if (! $iconAdded && defined('USE_FAVICON') && USE_FAVICONS && $cicon != "") {
      	    $iconAdded = true;
      	     $title = ("<img src=\"$cicon\" class=\"favicon\" alt=\"\"/>" . $title);
      	}
    }
    
    
    
    $shown = itemsList($title, $items, IL_CHANNEL_VIEW);
    
    
    $sql = "select count(*) from item where cid=$cid and unread=1";
    $res2 = rss_query($sql);
    list($unread_left) = mysql_fetch_row($res2);

    $sql = "select count(*) from item where cid=$cid";
    $res2 = rss_query($sql);
    list($allread) = mysql_fetch_row($res2);
    
    /** read more navigation **/
    $readMoreNav = "";
    if ($unread_left > 0 && !isset($_REQUEST['unread']) && $shown < $unread_left) {
        if (ITEMS_ON_CHANNELVIEW < $unread_left) {
            $readMoreNav .= "<a href=\"". getPath() . "feed.php?cid=$cid&amp;all&amp;unread\">" . sprintf(SEE_ALL_UNREAD,$unread_left) . "</a>";
        } else { 
            $readMoreNav .=  "<a href=\"". getPath() . "feed.php?cid=$cid&amp;all&amp;unread\">". sprintf(SEE_ONLY_UNREAD,$unread_left) ."</a>";
        }
    }
    
    if ((!isset($_REQUEST['all']) || isset($_REQUEST['unread'])) && $shown < $allread) {
        $readMoreNav .= "<a href=\"". getPath() ."feed.php?cid=$cid&amp;all\">". sprintf(SEE_ALL,$allread)."</a>";
    }
      
    if ($readMoreNav != "") {
	echo "<span class=\"readmore\">$readMoreNav</span>\n";    
    }
    
    
    echo "</div>\n";
}

function markReadForm($cid) {
    $sql = "select count(*)  from item where cid=$cid and unread=1";
    $res=rss_query($sql);
    list($cnt) = mysql_fetch_row($res);
    if($cnt > 0) {
    	echo "<form action=\"". getPath() ."feed.php\" method=\"post\" class=\"markread\">\n"
    	  ."\t<p><input type=\"submit\" name=\"action\" value=\"". MARK_CHANNEL_READ ."\"/>\n"
    //	  ."\t<input type=\"hidden\" name=\"all\"/>\n"
    //	  ."\t<input type=\"hidden\" name=\"unread\">\n"
    	  ."\t<input type=\"hidden\" name=\"channel\" value=\"$cid\"/></p>\n"
    	  ."</form>";
     }
}


function debugFeed($cid) {
    echo "<div id=\"items\" class=\"frame\">\n";
    $res = rss_query("select url from channels where id = $cid");
    list($url) = mysql_fetch_row($res);
    $rss = fetch_rss ($url);
    echo "<pre>\n";
    echo htmlentities(print_r($rss,1));
    echo "</pre>\n";    
    echo "</div>\n";
}




?>
