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



require_once("init.php");


if ($_POST['action'] != "" && trim($_POST['action']) == trim(MARK_READ)) {
    rss_query( "update item set unread=0" );
}


rss_header("",1);
sideChannels(true);
items("last items");
rss_footer();

function items($title) {
    echo "\n\n<div id=\"items\" class=\"frame\">";
    //markReadForm($cid);

    
    $sql = "select i.id, i.title,  c.title, c.id, i.unread, "
      ." i.url, i.description, c.icon "
      ." from item i, channels c "
      ." where i.cid = c.id and i.unread=1 "
      ." order by c.title asc, i.added desc";

    $res0=rss_query($sql);
    if (mysql_num_rows($res0) > 0) {
	echo "\n\n<h2>". sprintf(H2_UNREAD_ITEMS ,mysql_num_rows($res0)) ."</h2>\n";
	$ctnr=0;
	$prev_cid=0;
	while (list($iid_,$title_,$label_, $cid_, $unread_, $url_, $descr_,  $icon_) = mysql_fetch_row($res0)) {
	    
	    //echo "<!-- prev=$prev_cid, cid=$cid_ -->\n";
	    if ($prev_cid != $cid_) {
		
		
		$prev_cid = $cid_;
		if ($ctnr++ > 0)
		  echo "</ul>\n";
		
		echo "<h3>";
		if (_USE_FAVICONS_ && $icon_ != "") {
		    echo "<img src=\"$icon_\" class=\"favicon\" alt=\"\"/>";
		}
		echo "<a href=\"feed.php?id=$cid_\">$label_</a></h3>\n";
		echo "<ul>\n";
	    }

	    
	    $cls="item";
	    if (($cntr++ % 2) == 0) {
		$cls .= " even";
	    } else {
		$cls .= " odd";
	    }
	    
	    if  ($unread_ == 1) {
		$cls .= " unread";
	    }
	    $url__ = htmlentities($url_);
	    echo "\t<li class=\"$cls\">\n"
	      ."\t\t<a href=\"$url__\">$title_</a>\n";
	    
	    if ($descr_ != "") {
		echo "\t\t<div class=\"content\">$descr_</div>\n";
	    }
	    
	    echo "\t</li>\n";
	    
	}
	echo "</ul>\n";
    }
    
    echo "\n\n<h2>" .  H2_RECENT_ITEMS . "</h2>\n";
    
    $sql = "select "
      ." id, title, parent, icon "
      ." from channels "
      ." order by 3 asc, 2 asc";

    $res1=rss_query($sql);    
    while (list($cid,$title,$parent, $icon) = mysql_fetch_row($res1)) {
	echo "<h3>";
	if (_USE_FAVICONS_ && $icon != "") {
	    echo "<img src=\"$icon\" class=\"favicon\" alt=\"\"/>";
	}
	
	echo "<a href=\"feed.php?id=$cid\">$title</a></h3>\n";

	$sql = "select id, cid, added, title, url, description, unread, pubdate "
	  ." from item "
	  ." where cid  = $cid "
	  ." order by added desc"
	  ." limit 2";
	$res = rss_query($sql);
	
	if ($res && mysql_num_rows($res) > 0) {
	  echo "<ul>\n";
	}
	
	$cntr = 0;
	while (list($id, $cid, $added, $title, $url, $description, $unread, $pubdate) =  mysql_fetch_row($res)) {
	    $cls="item";
	    if (($cntr++ % 2) == 0) {
		$cls .= " even";
	    } else {
		$cls .= " odd";
	    }
	    
	    if  ($unread == 1) {
		$cls .= " unread";
	    }
	    
            $url__ = htmlentities($url);	    
	    echo "\t<li class=\"$cls\">\n"
	      ."\t\t<a href=\"$url__\">$title</a>\n";
	    
	    if ($description != "") 
	      echo "\t\t<div class=\"content\">$description</div>\n";
	    
	    echo "\t</li>\n";
	}
    
	if ($res && mysql_num_rows($res) > 0) {
	    echo "</ul>\n";
	}
	
	
	  
    }   
    
    echo "</div>\n";
}



function markAllReadForm() {
    echo "<form action=\"index.php\" method=\"post\" class=\"markallread\">"
      ."<input type=\"submit\" name=\"action\" value=\"". MARK_READ ." \"/>"
      ."</form>";
}


?>
