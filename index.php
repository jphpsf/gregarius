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

require_once("init.php");

if (array_key_exists('action', $_POST) 
    && $_POST['action'] != "" 
    && trim($_POST['action']) == trim(MARK_READ)) {
    
    rss_query( "update item set unread=0" );
}

rss_header("",LOCATION_HOME);

sideChannels(false);
items("last items");
rss_footer();

function items($title) {
    echo "\n\n<div id=\"items\" class=\"frame\">";



    // unread items first!
    $sql = "select i.title,  c.title, c.id, i.unread, "
      ." i.url, i.description, c.icon, unix_timestamp(i.pubdate) as ts, i.id  "
      ." from item i, channels c "
      ." where i.cid = c.id and i.unread=1 "
      ." order by c.parent asc, c.title asc, i.added desc, i.id asc"
      
      // Problem: to limit or not to limit?
      // Should the frontpage get the whole load of unread items
      // for a channel or not. And if not, should the user get them
      // all when he click the channel title?
      
      //." limit " . ITEMS_ON_CHANNELVIEW
      ;

    
    $res0=rss_query($sql);
    if (mysql_num_rows($res0) > 0) {
	
        while (list($title_,$ctitle_, $cid_, $unread_, $url_, $descr_,  $icon_, $ts_, $iid_) = mysql_fetch_row($res0)) {
            $items[] = array($cid_, $ctitle_, $icon_ , $title_ , 1 , $url_ , $descr_, $ts_, $iid_ );
        }

        itemsList ( sprintf(H2_UNREAD_ITEMS , mysql_num_rows($res0)),  $items );
    }

    // next: unread. Must find a better solution instead of iterating over the channels twice.
    $sql = "select "
      ." id, title, icon "
      ." from channels "
      ." order by parent asc, title asc";

    $res1=rss_query($sql);
    $items = array();
    while (list($cid,$ctitle, $icon) = mysql_fetch_row($res1)) {
	
      	$sql = "select cid, title, url, description, unread, unix_timestamp(pubdate) as ts, id  "
      	  ." from item "
      	  ." where cid  = $cid and unread = 0"
      	  ." order by added desc, id asc "
      	  ." limit 2";
	
	$res = rss_query($sql);
	
      	if (mysql_num_rows($res) > 0) {
      	    while (list($cid, $ititle, $url, $description, $unread, $ts, $iid) =  mysql_fetch_row($res)) {
		$items[] = array($cid,$ctitle,$icon,$ititle,$unread,$url,$description, $ts, $iid);
      	    }
      	 }
    }

    itemsList(H2_RECENT_ITEMS,$items);
    echo "</div>\n";
}

function markAllReadForm() {
    echo "<form action=\"". getPath() ."index.php\" method=\"post\" class=\"markallread\">"
      ."<input type=\"submit\" name=\"action\" value=\"". MARK_READ ." \"/>"
      ."</form>";
}

?>
