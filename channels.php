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

/** displays the channel list **/
function sideChannels($activeId) {
    echo "\n\n<div id=\"channels\" class=\"frame\">\n";    
    echo "<h2>".H2_CHANNELS."</h2>";

    stats();
          
    $sql = "select "
      ." c.id, c.title, c.url, c.siteurl, d.name, c.parent, c.icon, c.descr "
      ." from channels c, folders d "
      ." where d.id = c.parent";
    if (defined('ABSOLUTE_ORDERING') && ABSOLUTE_ORDERING) {
	$sql .= " order by d.position asc, c.position asc";
    } else {
	$sql .=" order by c.parent asc, c.title asc";
    }
    
    
    $res = rss_query($sql);
    $prev_parent = 0;
    echo "<ul>\n";
    while (list($id, $title, $url, $siteurl, $parent, $pid, $ico, $description) = mysql_fetch_row($res)) {
	//echo "\n<!-- $title -->\n";	
	
	if ($pid != $prev_parent) {
	    
	    
	    if ($prev_parent > 0) {
		echo tabs(2) ."</ul>\n" .tabs(1) ."</li>\n";
	    }
	    
	    echo tabs(1) . "<li class=\"folder\">\n"
	      . tabs(2) ."<span>$parent</span>\n";
	    
	    $prev_parent=$pid;
	    echo tabs(2) . "<ul>\n";

	    
	}
	echo tabs( ($pid > 0)?3:1  ) . "<li" .  (($id == $activeId)?" class=\"active\"":"") . ">";
	echo feed($id, $title, $url, $siteurl, $ico, $description);
	echo "</li>\n";
    }
    
    if ($prev_parent > 0) {
	echo tabs(2) ."</ul>\n"
	  . tabs(1) ."</li>\n";
    }
    
    echo "</ul>\n";
    
    
    $res=rss_query("select count(*) from item where unread=1");
    list($unread_count) = mysql_fetch_row($res);

    /*
    if ($unread_count > 0)
      markChannelReadForm();
    */
    echo "\n</div>\n";
}


function tabs($count) {
    $ret = "";
    for ($i=0;$i<$count;$i++) 
	$ret.= "\t";    
    return $ret;
}


/** prints out a formatted channel item **/
function feed($cid, $title, $url, $siteurl, $ico, $description) {
    $res = rss_query ("select count(*) from item where cid=$cid and unread=1");
    list($cnt) = mysql_fetch_row($res);
    if ($cnt > 0) {
        $rdLbl= sprintf(UNREAD_PF, $cnt);	
	$class_= " class=\"unread\"";
    } else {
	$rdLbl=$class_="";
    }
    
    $ret = "";
    
    
    
    if (defined('USE_FAVICONS') && USE_FAVICONS && $ico != "") {
	// $ret .= "<img src=\"". getPath(). "imgwrp.php?url=$ico\" class=\"favicon\" alt=\"\" />";	
	$ret .= "<img src=\"$ico\" class=\"favicon\" alt=\"\" />";
    }
    
    if (defined('USE_MODREWRITE') && USE_MODREWRITE) {
	$feedUrl = getPath() . preg_replace("/[^A-Za-z0-9\.]/","_","$title") ."/";
    } else {
	$feedUrl = getPath() . "feed.php?channel=$cid";
    }
    
    
    $ret .= 
      "<a" 
      .$class_
      . ($description!=""?" title=\"$description\"":"")
      ." href=\"$feedUrl\">" .htmlentities($title) ."</a> $rdLbl"
      ." [<a href=\"". htmlentities($url)."\">xml</a>";
    
    if ($siteurl != "" && substr($siteurl,0,4) == 'http') {
	$ret .= "|<a href=\"$siteurl\">www</a>";
    }
    
    if (defined('_DEBUG_') && _DEBUG_ == true) {
	$ret .= "|<a href=\"". getPath() ."feed.php?channel=$cid&amp;dbg\">dbg</a>";	
    }
    
    $ret .= "]";
    
    return $ret;
}



function stats() {
    $res = rss_query( "select count(*) from item where unread=1" );
    list($unread)= mysql_fetch_row($res);
    
    $res = rss_query( "select count(*) from item" );
    list($total)= mysql_fetch_row($res);
    
    $res = rss_query( "select count(*) from channels");
    list($channelcount)= mysql_fetch_row($res);
    
    
    printf ("\n<p class=\"stats\">" . ITEMCOUNT_PF . "</p>\n"
	    ,$total, $unread, $channelcount);
    
}

?>
