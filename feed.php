<?
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003, 2004 Marco Bonetti
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
$cid= (array_key_exists('cid',$_REQUEST))?$_REQUEST['cid']:"";


if (array_key_exists ('action', $_POST) && $_POST['action'] != "") {
    
    $sql = "update item set unread=0 where cid=$cid";
    rss_query($sql);
    
    // redirect to the next unread, if any.
    $sql = "select cid from item where unread=1 order by added desc limit 1";
    $res = rss_query($sql);
    list ($next_unread) = mysql_fetch_row($res);
    if ($next_unread == '') {
	header("Location: http://"
	       . $_SERVER['HTTP_HOST']
	       . dirname($_SERVER['PHP_SELF'])
	       . "/index.php"	       
	       );
    } else {
	$cid = $next_unread;
    }
    
}

assert(is_numeric($cid));

$res = rss_query("select title,icon from channels where id = $cid");
list($title,$icon) = mysql_fetch_row($res);

rss_header($title);
sideChannels($cid); 
if (defined('_DEBUG_') && array_key_exists('dbg',$_REQUEST)) {
    debugFeed($cid);
} else {
    items($cid,$title);
}
rss_footer();


function items($cid,$title) {
    echo "\n\n<div id=\"items\" class=\"frame\">";

    markReadForm($cid);
        
    $sql = " select i.title, i.url, i.description, i.unread, "
      ." unix_timestamp(i.pubdate) as ts, c.icon, c.title "
      ." from item i, channels c "
      ." where i.cid = $cid and c.id = $cid ";

    if  (isset($_GET['unread']))
      $sql .= " and unread=1 ";
		
    $sql .=" order by added desc";
      
    if (!isset($_GET['all']) && !isset($_GET['unread'])) {
      $sql .= " limit ". ITEMS_ON_CHANNELVIEW;
    }
    
    $res = rss_query($sql);    
    $items = array();
    
    while (list($title_, $url_, $description_, $unread_, $ts_, $ctitle, $cicon) =  mysql_fetch_row($res)) {
	$items[]=array(-1, $ctitle, $cicon,$title_,$unread_,$url_,$description_, $ts_);
    }
    
    itemsList($title,$items);
    
    
    $sql = "select count(*) from item where cid=$cid and unread=1";
    $res2 = rss_query($sql);
    list($unread_left) = mysql_fetch_row($res2);

    $sql = "select count(*) from item where cid=$cid";
    $res2 = rss_query($sql);
    list($allread) = mysql_fetch_row($res2);
    
    /** read more navigation **/
    echo "<span class=\"readmore\">";    
    if ($unread_left > 0 && !isset($_GET['unread'])) {
        if (ITEMS_ON_CHANNELVIEW < $unread_left) {
            echo "<a href=\"feed.php?cid=$cid&amp;all&amp;unread\">" . sprintf(SEE_ALL_UNREAD,$unread_left) . "</a>";
        } else { 
            echo "<a href=\"feed.php?cid=$cid&amp;all&amp;unread\">". sprintf(SEE_ONLY_UNREAD,$unread_left) ."</a>";
        }
    }
    
    if (!isset($_GET['all']) || isset($_GET['unread'])) {
        echo "<a href=\"feed.php?cid=$cid&amp;all\">". sprintf(SEE_ALL,$allread)."</a>";
    }
      
    echo "</span>\n";    
    echo "</div>\n";
}

function markReadForm($cid) {
    $sql = "select count(*)  from item where cid=$cid and unread=1";
    $res=rss_query($sql);
    list($cnt) = mysql_fetch_row($res);
    if($cnt > 0) {
      echo "<form action=\"feed.php\" method=\"post\" class=\"markread\">\n"
      ."\t<p><input type=\"submit\" name=\"action\" value=\"". MARK_CHANNEL_READ ."\"/>\n"
      ."\t<input type=\"hidden\" name=\"cid\" value=\"$cid\"/></p>\n"
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
