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
$id=$_GET['id'];
if ($_POST['action'] != "") {
    $id = $_POST['cid'];
    
    $sql = "update item set unread=0 where cid=$id";
    rss_query($sql);
}

assert(is_numeric($id));


$res = rss_query("select title from channels where id = $id");
list($title) = mysql_fetch_row($res);

rss_header($title);
 sideChannels($id); 
 items($id,$title);
rss_footer();




function items($cid,$title) {
    echo "\n\n<div id=\"items\" class=\"frame\">";
    markReadForm($cid);
    echo "\n\n<h2>$title</h2>\n";
    
    
    
    echo"\n<ul>\n";

    
    $sql = "select id, cid, added, title, url, description, unread, pubdate "
      ." from item "
      ." where cid  = $cid ";
    
    if  (isset($_GET['unread']))
      $sql .= " and unread=1 ";
		
    $sql .=" order by added desc";
      
    if (!isset($_GET['all']) && !isset($_GET['unread'])) {
      $sql .= " limit ". ITEMS_ON_CHANNELVIEW;
    }
    
    $res = rss_query($sql);
    $cntr = 0;
    $lastid = 0;
    while (list($iid_, $cid_, $added_, $title_, $url_, $description_, $unread_, $pubdate_) =  mysql_fetch_row($res)) {
        $cls="item";
        if (($cntr++ % 2) == 0) {
            $cls .= " even";
        } else {
            $cls .= " odd";
        }
        
        if  ($unread_ == 1) {
            $cls .= " unread";
        }
        
        echo "\t<li class=\"$cls\">\n"
          ."\t\t<a  href=\"$url_\">$title_</a>\n";
        if ($description_ != "") {
            echo "\t\t<div class=\"content\">$description_</div>\n";
        }
        echo "\t</li>\n";       
        $lastid = $iid_;
    }
    
    
    echo "</ul>\n";
    
    
    
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
            echo "<a href=\"feed.php?id=$cid&amp;all&amp;unread\">" . sprintf(SEE_ALL_UNREAD,$unread_left) . "</a>";
        } else { 
            echo "<a href=\"feed.php?id=$cid&amp;all&amp;unread\">". sprintf(SEE_ONLY_UNREAD,$unread_left) ."</a>";
        }
    }
    
    if (!isset($_GET['all']) || isset($_GET['unread'])) {
        echo "<a href=\"feed.php?id=$cid&amp;all\">". sprintf(SEE_ALL,$allread)."</a>";
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
?>
