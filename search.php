<?
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2005 Marco Bonetti
#
###############################################################################
# File: $Id$ $Name$
#
##############################################################################
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

define ('QUERY_PRM','query');
define ('QUERY_MATCH_MODE', 'query_match');
define ('QUERY_CHANNEL', 'query_channel');
define ('QUERY_RESULTS','query_res_per_page');
define ('QUERY_CURRENT_PAGE','query_current_page');
define ('HIT_BEFORE',"<span class=\"searchhit\">");
define ('HIT_AFTER',"</span>");
define ('ALL_CHANNELS_ID', -1);
define ('INFINE_RESULTS',-1);
define ('QUERY_ORDER_BY','order');
define ('QUERY_ORDER_BY_DATE','date');
define ('QUERY_ORDER_BY_CHANNEL','channel');
define ('QUERY_MATCH_OR','or');
define ('QUERY_MATCH_AND','and');
define ('QUERY_MATCH_EXACT','exact');

require_once("init.php");

if (array_key_exists(QUERY_PRM,$_REQUEST) && strlen($_REQUEST[QUERY_PRM]) > 1) {
    rss_header("Search",LOCATION_SEARCH);
    sideChannels(false);  
    search();
} else {
    rss_header(TITLE_SEARCH,LOCATION_SEARCH,"document.getElementById('query').focus()");  
    sideChannels(false);
    list($cnt) = rss_fetch_row(rss_query('select count(*) from ' . getTable("item")));
    searchForm(sprintf(H2_SEARCH, $cnt));
}

rss_footer();

function searchForm($title) {
    

    echo "\n\n<div id=\"searchfrm\" class=\"frame\">";

    echo "\n\t\t<h2>$title</h2>\n";

    echo
      "\n\t\t<form action=\"". getPath() ."search.php\" method=\"post\" id=\"srchfrm\">\n"
      ."\n\t\t<p><label for=\"query\">". SEARCH_SEARCH_QUERY ."</label><input type=\"text\" name=\"query\" "
      ." id=\"query\" value=\"". (array_key_exists(QUERY_PRM,$_REQUEST)?$_REQUEST[QUERY_PRM]:"")
	."\"/></p>\n"
      
      ."\n\t\t<p><input type=\"radio\" id=\"qry_match_or\" name=\"". QUERY_MATCH_MODE 
      ."\" value=\"". QUERY_MATCH_OR."\""
      .((array_key_exists(QUERY_MATCH_MODE,$_REQUEST) && 
	 $_REQUEST[QUERY_MATCH_MODE] == QUERY_MATCH_OR)?" checked=\"checked\"":"")
	."/>\n"
      ."\t\t<label for=\"qry_match_or\">". SEARCH_MATCH_OR."</label>\n"

      ."\t\t<input type=\"radio\" id=\"qry_match_and\" name=\"". QUERY_MATCH_MODE 
      ."\" value=\"". QUERY_MATCH_AND ."\""
      .((array_key_exists(QUERY_MATCH_MODE,$_REQUEST) &&
	 $_REQUEST[QUERY_MATCH_MODE] == QUERY_MATCH_AND ||
	 !array_key_exists(QUERY_MATCH_MODE,$_REQUEST))?" checked=\"checked\"":"")
	."/>\n"
      ."\t\t<label for=\"qry_match_and\">". SEARCH_MATCH_AND."</label>\n"

      ."\t\t<input type=\"radio\" id=\"qry_match_exact\" name=\"". QUERY_MATCH_MODE
      ."\" value=\"". QUERY_MATCH_EXACT ."\""
      .((array_key_exists(QUERY_MATCH_MODE,$_REQUEST) &&
	 $_REQUEST[QUERY_MATCH_MODE] == QUERY_MATCH_EXACT)?" checked=\"checked\"":"")
	."/>\n"  
      ."\t\t<label for=\"qry_match_exact\">". SEARCH_MATCH_EXACT."</label></p>\n"
      
      
      ."\n\t\t<p><label for=\"". QUERY_CHANNEL ."\">". SEARCH_CHANNELS ."</label>\n"
      ."\t\t<select name=\"".QUERY_CHANNEL."\" id=\"".QUERY_CHANNEL."\">\n"
      ."\t\t\t<option value=\"". ALL_CHANNELS_ID ."\""
      .((!array_key_exists(QUERY_CHANNEL,$_REQUEST) || 
	 $_REQUEST[QUERY_CHANNEL] == ALL_CHANNELS_ID)?" selected=\"selected\"":"")
	.">" . ALL  . "</option>\n";

    $res = rss_query( "select "
		      ." c.id, c.title, f.name, f.id  "
		      ." from " . getTable("channels") ." c, " . getTable("folders"). " f "
		      ." where f.id=c.parent "
		      ." order by c.parent asc,"
		      .((getConfig('rss.config.absoluteordering'))?"c.position asc":"c.title asc"));
    
    
    
    $prev_parent = -1;
    while (list($id_,$title_, $parent_, $parent_id_) = rss_fetch_row($res)) {
        if ($prev_parent != $parent_id_) {
            if ($prev_parent != 0) {
                echo "\t\t\t</optgroup>\n";
            }
            if ($parent_ == "") { $parent_ = HOME_FOLDER; }
            echo "\t\t\t<optgroup label=\"$parent_ /\">\n";
            $prev_parent = $parent_id_;
        }
        
        if (strlen($title_ ) > 25) {
            $title_ = substr($title_,0,22) . "...";
        }
        echo "\t\t\t<option value=\"$id_\""
          .((array_key_exists(QUERY_CHANNEL,$_REQUEST) && 
             $_REQUEST[QUERY_CHANNEL] == $id_)?" selected=\"selected\"":"")
            .">$title_</option>\n";
    }


    if ($prev_parent != 0) {
        echo "\t\t\t</optgroup>\n";
    }
    
    echo "\t\t</select></p>\n";

    echo "\n\t\t<p><input type=\"radio\" id=\"qry_order_date\" name=\"". QUERY_ORDER_BY
      ."\" value=\"". QUERY_ORDER_BY_DATE ."\""
      .((array_key_exists(QUERY_ORDER_BY,$_REQUEST) &&
	 $_REQUEST[QUERY_ORDER_BY] == QUERY_ORDER_BY_DATE ||
	 !array_key_exists(QUERY_ORDER_BY,$_REQUEST)?" checked=\"checked\"":""))
	."/>\n"
      ."\t\t<label for=\"qry_order_date\">". SEARCH_ORDER_DATE_CHANNEL ."</label>\n"      
      ."\t\t<input type=\"radio\" id=\"qry_order_channel\" name=\"". QUERY_ORDER_BY
      ."\" value=\"". QUERY_ORDER_BY_CHANNEL ."\""
      .((array_key_exists(QUERY_ORDER_BY,$_REQUEST) &&
	 $_REQUEST[QUERY_ORDER_BY] == QUERY_ORDER_BY_CHANNEL)?" checked=\"checked\"":"")
	."/>\n"
      ."\t\t<label for=\"qry_order_channel\">". SEARCH_ORDER_CHANNEL_DATE ."</label></p>\n";
    
    
    
    echo "\n\t\t<p><label for=\"". QUERY_RESULTS ."\">". SEARCH_RESULTS_PER_PAGE ."</label>\n"
      ."\t\t<select name=\"".QUERY_RESULTS."\" id=\"".QUERY_RESULTS."\">\n"

      ."\t\t\t<option value=\"5\""
      .((array_key_exists(QUERY_RESULTS,$_REQUEST) && $_REQUEST[QUERY_RESULTS] == 5?" selected=\"selected\"":""))
	.">5</option>\n"
      
      ."\t\t\t<option value=\"15\""
      .((
	(array_key_exists(QUERY_RESULTS,$_REQUEST) && $_REQUEST[QUERY_RESULTS] == 15)
	|| !array_key_exists(QUERY_RESULTS,$_REQUEST)
	)?" selected=\"selected\"":"")
	.">15</option>\n"
      
      

    ."\t\t\t<option value=\"50\""
      .((array_key_exists(QUERY_RESULTS,$_REQUEST) && $_REQUEST[QUERY_RESULTS] == 50?" selected=\"selected\"":""))
	.">50</option>\n"
    
    ."\t\t\t<option value=\"".INFINE_RESULTS."\""
      .((array_key_exists(QUERY_RESULTS,$_REQUEST) && $_REQUEST[QUERY_RESULTS] == INFINE_RESULTS?" selected=\"selected\"":""))
	.">".ALL."</option>\n"
                                
    
      ."\t\t</select></p>\n"

      ."\n\t\t<p><input type=\"hidden\" name=\"".QUERY_CURRENT_PAGE."\" value=\"0\" />\n"
      ."<input id=\"search_go\" type=\"submit\" value=\"". SEARCH_GO ."\"/></p>\n"
      ."\t\t</form>\n";
    

    echo "</div>\n";    
}

function search() {

    
    $qry = $_REQUEST[QUERY_PRM];
    $matchMode = (!array_key_exists(QUERY_MATCH_MODE, $_REQUEST)?QUERY_MATCH_AND:$_REQUEST[QUERY_MATCH_MODE]);
    $channelId = (array_key_exists(QUERY_CHANNEL,$_REQUEST))?(int)$_REQUEST[QUERY_CHANNEL]:ALL_CHANNELS_ID;
    $resultsPerPage = (array_key_exists(QUERY_RESULTS,$_REQUEST))?(int)$_REQUEST[QUERY_RESULTS]:INFINE_RESULTS;   
    $currentPage = (array_key_exists(QUERY_CURRENT_PAGE,$_REQUEST)?(int)$_REQUEST[QUERY_CURRENT_PAGE]:0);
    $orderBy = (array_key_exists(QUERY_ORDER_BY, $_REQUEST)?$_REQUEST[QUERY_ORDER_BY]:QUERY_ORDER_BY_DATE);
    $qWhere = "";
    $regMatch = "";
    $term = "";
    if ($matchMode == QUERY_MATCH_OR || $matchMode == QUERY_MATCH_AND) {
	
	$logicSep = ($matchMode == QUERY_MATCH_OR?"or":"and");
	$terms = explode(" ",$qry);    
	foreach($terms as $term) {
	    $term = trim($term);
	    if ($term != "") {	    
		$qWhere .= 
		  "(i.description like '%$term%' or "
		  ." i.title like '%$term%') " .$logicSep;
	    }
	    // this will be used later for the highliting regexp
	    if ($regMatch != "") {
		$regMatch .= "|";
	    }
	    $regMatch .= $term;
	}
	
	$qWhere .= ($matchMode == QUERY_MATCH_OR?" 1=0 ":" 1=1 ");
    } else {
	$logicSep = "";
	$terms[0] = $qry;
	$qWhere .= 
	  "(i.description like '%$term%' or "
	  ." i.title like '%$term%') ";
	$regMatch = $qry;
    }
    
    $sql = "select distinct i.title, c.title, c.id, i.unread, i.url, "
      ." i.description, c.icon, "
      ." if (i.pubdate is null, unix_timestamp(i.added), unix_timestamp(i.pubdate)) as ts, "
      ." i.pubdate is not null as ispubdate, " 
      ." i.id  "
      ." from " . getTable("item") ." i, " . getTable("channels") ." c, " . getTable("folders") ." f "
      ." where i.cid=c.id and c.parent=f.id and ("
      .$qWhere .") ";

    if ($channelId != ALL_CHANNELS_ID) {
	$sql .= " and c.id = $channelId ";
    }
    
    if ($orderBy == QUERY_ORDER_BY_DATE) {
	$sql .= " order by 8 desc";
    } else {
	if (getConfig('rss.config.absoluteordering')) {
	    $sql .= " order by f.position asc, c.position asc";
	} else {
	    $sql .= " order by c.parent asc, c.title asc";
	}
    }
    

    $sql .=", i.added desc";
    
    //echo $sql;
    
    $res0=rss_query($sql);
    $cnt = rss_num_rows($res0);
    $items = array();

                
    $start__ = $resultsPerPage * $currentPage;
    $end__  = $start__ + $resultsPerPage -1;
    if ($resultsPerPage == INFINE_RESULTS) {       
	$end__ = 99999999;
    }
      
    if ($cnt > 0) {

	$crnt = 0;
	
	$items=array();
        while (list($ititle,$ctitle, $cid, $iunread, $iurl, $idescr,  $cicon, $its, $iispubdate, $iid) = rss_fetch_row($res0)) {

	    $descr_noTags = strip_tags($idescr);
	    $title_noTags = strip_tags($ititle);
	    
	    
	    $match = false;
	    reset($terms);
	    $match = ($matchMode == QUERY_MATCH_AND || $matchMode == QUERY_MATCH_EXACT);
	    foreach ($terms as $term) {
		if ($matchMode == QUERY_MATCH_AND || $matchMode == QUERY_MATCH_EXACT) {		   
		    $match =  ((stristr($descr_noTags,$term) || stristr($title_noTags, $term)) && $match);
		} else {
		    $match = ($match || (stristr($descr_noTags,$term) || stristr($title_noTags, $term)));
		}
		    
	    }
	    
	    if ($match) {				
		if (isset($hits) && is_array($hits) && array_key_exists($iid,$hits) && $hits[$iid]) {
		    continue;
		}
		
		
		if ($crnt >= $start__ && $crnt <= $end__) {
		    $hits[$iid] = true;		
		    $items[]=
		      array($cid,$ctitle,$cicon,
			    
			    // Credits for the regexp: mike at iaym dot com
			    // http://ch2.php.net/manual/en/function.preg-replace.php
			    "$ititle",
			    $iunread,
			    $iurl,
			    preg_replace("'(?!<.*?)($regMatch)(?![^<>]*?>)'si",
					 HIT_BEFORE . "\\1" . HIT_AFTER,
					 $idescr),
			    $its,
			    $iispubdate,
			    $iid
			    );
		    
		} 
		
		
		$crnt++;
	    }	    
        }
    }
    
    $cnt = $crnt;

    $humanReadableQuery = implode(" ".strtoupper($logicSep)." ",$terms);
    
    $title = sprintf(
		     (($cnt > 1 || $cnt == 0)?H2_SEARCH_RESULTS_FOR:H2_SEARCH_RESULT_FOR), 
		     $cnt, "'" .$humanReadableQuery."'");

    // If we got no hit, offer the search form.
    if ($cnt > 0) {
	searchForm($title,false);
	
	$nav = "";
	if ($resultsPerPage != INFINE_RESULTS && $cnt > $resultsPerPage) {
	    $nav .= "<div class=\"readmore\">";
	    $nav .= SEARCH_RESULTS;
	    
	    
	    // first page
	    $fp = 0;
	    //last page
	    $lp = floor(($cnt -1) / $resultsPerPage);
	    // current page
	    $cp = $currentPage;
	    //shown pages
	    $pages = array();

	    for ($i=0;$i<4;$i++) {
		if ($cp - $i >= 0) {
		    $pages[$cp - $i] = true;
		} else {
		    if ($cp + $i < $lp) {
			$pages[$cp + $i] = true;
		    }
		}
		if ($cp + $i < $lp) {
		    $pages[$cp + $i] = true;
		} else {
		    if ($cp - $i >= 0) {
			$pages[$cp - $i] = true;
		    }
		}
	    }
	    
	    $pages[0] = true;
	    $pages[$lp] = true;
	    
	    
	    for ($p=$fp;$p<$lp;$p++) {
		if (!array_key_exists($p,$pages)) {
		    if (array_key_exists($p - 1 ,$pages)) {
			$nav .= " ... ";
		    } 
		    continue;
		}
		
		$cpp = ($p * $resultsPerPage == $start__);
		if (!$cpp) {
		    $nav .= " <a href=\""
		      .$_SERVER['PHP_SELF'] . "?"
		      .QUERY_PRM . "=$qry&amp;"
		      .QUERY_MATCH_MODE . "=$matchMode&amp;"
		      .QUERY_CHANNEL . "=$channelId&amp;"
		      .QUERY_RESULTS . "=$resultsPerPage&amp;"
		      .QUERY_ORDER_BY . "=$orderBy&amp;"
		      .QUERY_CURRENT_PAGE ."=$p"
		      ."\">";
		} else {
		    $nav .= HIT_BEFORE;
		}
		
		$nav .= "". (1+$p * $resultsPerPage) ."-" . ((1+$p) * $resultsPerPage) ."";
		
		if (!$cpp) {
		    $nav .= "</a>";
		} else {
		    $nav .= HIT_AFTER;
		}
		if ((1+$p) * $resultsPerPage < $cnt) {
		    $nav .= ", \n";
		}
	    }
	    
	    if ($p * $resultsPerPage >= $end__) {
		$nav .= " <a href=\""
		  .$_SERVER['PHP_SELF'] . "?"
		  .QUERY_PRM . "=$qry&amp;"
		  .QUERY_MATCH_MODE . "=$matchMode&amp;"
		  .QUERY_CHANNEL . "=$channelId&amp;"
		  .QUERY_RESULTS . "=$resultsPerPage&amp;"
		  .QUERY_ORDER_BY . "=$orderBy&amp;"
		  .QUERY_CURRENT_PAGE ."=$p"
		  ."\">";
		$nav .=  (1+ $p * $resultsPerPage) ."-$cnt";
		$nav .= "</a> \n";
	    } else {
		$nav .= HIT_BEFORE . (1+ $p * $resultsPerPage) ."-$cnt" .HIT_AFTER;
	    }
	    $nav .= "<hr class=\"clearer hidden\"/>\n</div>\n";
	}
	
	echo "<div id=\"items\" class=\"frame\">";
	if ($nav) {
	    echo $nav;
	}
	itemsList( "", $items, IL_NO_COLLAPSE);
	if ($nav) {
	    echo $nav;
	}
	echo "</div>\n";
    } else {
	searchForm($title,true);
    }

}

?>
