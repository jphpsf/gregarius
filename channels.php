<?

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



/** displays the channel list **/
function sideChannels($activeId) {
    echo "\n\n<div id=\"channels\" class=\"frame\">\n";    
    echo "<h2>".H2_CHANNELS."</h2>";

    stats();
          
    $sql = "select "
      ." c.id, c.title, c.url, c.siteurl, f.name, c.parent, c.icon, c.descr "
      ." from " .getTable("channels") ." c, " .getTable("folders") ." f "
      ." where f.id = c.parent";
    if (getConfig('ABSOLUTE_ORDERING')) {
	$sql .= " order by f.position asc, c.position asc";
    } else {
	$sql .=" order by c.parent asc, c.title asc";
    }
    
    
    $res = rss_query($sql);
    $channelCount = rss_num_rows ( $res );
    
    $prev_parent = 0;
    echo "<ul>\n";
    while (list($cid, $ctitle, $curl, $csiteurl, $fname, $cparent, $cico, $cdescr) 
	   = rss_fetch_row($res)) {
	//echo "\n<!-- $title -->\n";	
	
	if ($cparent != $prev_parent) {
	    
	    
	    if ($prev_parent > 0) {
		echo tabs(2) ."</ul>\n" .tabs(1) ."</li>\n";
	    }
	    
	    echo tabs(1) . "<li class=\"folder\">\n"
	      . tabs(2) ."<span>$fname</span>\n";
	    
	    $prev_parent=$cparent;
	    echo tabs(2) . "<ul>\n";

	    
	}
	echo tabs( ($cid > 0)?3:1  ) . "<li" .  (($cid == $activeId)?" class=\"active\"":"") . ">";
	echo feed($cid, $ctitle, $curl, $csiteurl, $cico, $cdescr);
	echo "</li>\n";
    }
    
    if ($prev_parent > 0) {
	echo tabs(2) ."</ul>\n"
	  . tabs(1) ."</li>\n";
    }
    
    echo "</ul>\n";
    

    $rescnt=rss_query("select count(*) as cnt from " .getTable("item") ." where unread=1");
    list($unread_count) = rss_fetch_row($rescnt);

    echo "\n</div>\n";
    
    return $channelCount;
}


function tabs($count) {
    $ret = "";
    for ($i=0;$i<$count;$i++) 
	$ret.= "\t";    
    return $ret;
}


/** prints out a formatted channel item **/
function feed($cid, $title, $url, $siteurl, $ico, $description) {
    $res = rss_query ("select count(*) from " .getTable("item") ." where cid=$cid and unread=1");
    list($cnt) = rss_fetch_row($res);
    if ($cnt > 0) {
        $rdLbl= sprintf(UNREAD_PF, $cnt);	
	$class_= " class=\"unread\"";
    } else {
	$rdLbl=$class_="";
    }
    
    $ret = "";
    
    
    
    if (getConfig('USE_FAVICONS') && $ico != "") {
	// $ret .= "<img src=\"". getPath(). "imgwrp.php?url=$ico\" class=\"favicon\" alt=\"\" />";	
	$ret .= "<img src=\"$ico\" class=\"favicon\" alt=\"\" />";
    }
    
    if (getConfig('USE_MODREWRITE')) {
	$feedUrl = getPath() . preg_replace("/[^A-Za-z0-9\.]/","_","$title") ."/";
    } else {
	$feedUrl = getPath() . "feed.php?channel=$cid";
    }
    
    
    $ret .= 
      "<a" 
      .$class_
      . ($description!=""?" title=\"$description\"":"")
      ." href=\"$feedUrl\">" . $title ."</a> $rdLbl"
      ." [<a href=\"". htmlentities($url)."\">xml</a>";
    
    if ($siteurl != "" && substr($siteurl,0,4) == 'http') {
	$ret .= "|<a href=\"" . htmlentities($siteurl) ."\">www</a>";
    }
    
    if (getConfig('_DEBUG_')) {
	$ret .= "|<a href=\"". getPath() ."feed.php?channel=$cid&amp;dbg\">dbg</a>";	
    }
    
    $ret .= "]";
    
    return $ret;
}



function stats() {
    $res = rss_query( "select count(*) from " .getTable("item") ." where unread=1" );
    list($unread)= rss_fetch_row($res);
    
    $res = rss_query( "select count(*) from " . getTable("item") );
    list($total)= rss_fetch_row($res);
    
    $res = rss_query( "select count(*) from " .getTable("channels") );
    list($channelcount)= rss_fetch_row($res);
    
    
    printf ("\n<p class=\"stats\">" . ITEMCOUNT_PF . "</p>\n"
	    ,$total, $unread, $channelcount);
    
}
?>
