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


define ('QUERY_PRM',"query");

require_once("init.php");
rss_header("Search",3);
sideChannels(true);

if (array_key_exists(QUERY_PRM,$_POST)) {
    search($_POST[QUERY_PRM]);
} else {
    searchForm();
}

rss_footer();



function searchForm() {
    echo "\n\n<div id=\"search\" class=\"frame\">";    
    echo 
      "\n\t\t<form action=\"search.php\" method=\"post\" id=\"srchfrm\">\n"
      ."\t\t<p><input type=\"text\" name=\"query\" id=\"query\" />\n"
      ."\t\t<input type=\"submit\" value=\"". SEARCH ."\"/></p>\n"
      ."\t\t</form>\n";        
    echo "</div>\n";
}

  
  
function search($qry) {
    echo "\n\n<div id=\"items\" class=\"frame\">";
    //markReadForm($cid);

    
    $sql = "select i.id, i.title, c.title, c.id, i.unread, i.url, "
    ." i.description, c.icon "
    ." from item i, channels c "
    ." where i.cid=c.id and "
    ."   (i.description like '%$qry%' or i.title like '%$qry%') "
    ." order by c.title asc, i.added desc";

    $res0=rss_query($sql);
    $cnt = mysql_num_rows($res0);
    if ($cnt > 0) {
        echo "\n\n<h2>". sprintf(H2_SEARCH_RESULTS_FOR , $cnt, "'" . $qry . "'") ."</h2>\n";
        $ctnr=0;
        $prev_cid=0;
        while (list($iid_,$title_,$label_, $cid_, $unread_, $url_, $descr_,  $icon_) = mysql_fetch_row($res0)) {

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
              ."\t\t<a href=\"$url__\">". preg_replace("/($qry)/i", "<strong>\$1</strong>", $title_) ."</a>\n";
            
            if ($descr_ != "") {
                
                echo "\t\t<div class=\"content\">". preg_replace("/($qry)/i", "<strong>\$1</strong>", $descr_) ."</div>\n";
            }
            
            echo "\t</li>\n";
            
        }
        echo "</ul>\n";
    }
    echo "</div>\n";
}



?>
