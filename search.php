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
define ('HIT_BEFORE',"<span class=\"searchhit\">");
define ('HIT_AFTER',"</span>");


require_once("init.php");
rss_header("Search",3);
sideChannels(false);

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
    
    
    $sql = "select i.title, c.title, c.id, i.unread, i.url, "
    ." i.description, c.icon, unix_timestamp(i.pubdate) as ts  "
    ." from item i, channels c "
    ." where i.cid=c.id and "
    ."   (i.description like '%$qry%' or i.title like '%$qry%') "
    ." order by c.title asc, i.added desc";


    $res0=rss_query($sql);
    $cnt = mysql_num_rows($res0);
    if ($cnt > 0) {
	$items=array();
        while (list($ititle,$ctitle, $cid, $iunread, $iurl, $idescr,  $cicon, $its) = mysql_fetch_row($res0)) {
            
	    $descr_noTags = preg_replace("/<.+?>/","",$idescr);
	    $title_noTags = preg_replace("/<.+?>/","",$ititle);
	    
	    if (stristr($descr_noTags,$qry) || stristr($title_noTags, $qry)) {
		
		$items[]=array($cid,$ctitle,$cicon,

			       // Credits for the regexp: mike at iaym dot com 
			       // http://ch2.php.net/manual/en/function.preg-replace.php
			       preg_replace("'(?!<.*?)($qry)(?![^<>]*?>)'si", 
					    HIT_BEFORE . "\\1" . HIT_AFTER, 
					    $ititle),
			       $iunread,
			       $iurl,			   
			       preg_replace("'(?!<.*?)($qry)(?![^<>]*?>)'si", 
					    HIT_BEFORE . "\\1" . HIT_AFTER,
					    $idescr),
			       $its
			       );
	    }
            
        }
	$cnt = count($items);
	itemsList(
		  sprintf(H2_SEARCH_RESULTS_FOR, $cnt, "'" .$qry."'"),
		  $items
		  );
    }
    echo "</div>\n";
}



?>
