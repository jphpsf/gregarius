<?

###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003, 2004 Marco Bonetti
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
define ('HIT_BEFORE',"<span class=\"searchhit\">");
define ('HIT_AFTER',"</span>");
define ('ALL_CHANNELS_ID', -1);

require_once("init.php");

if (array_key_exists(QUERY_PRM,$_REQUEST) && strlen($_REQUEST[QUERY_PRM]) > 1) {
    rss_header("Search",LOCATION_SEARCH);
    sideChannels(false);  
    $matchMode = (!array_key_exists(QUERY_MATCH_MODE, $_REQUEST)?SERCH_MATCH_AND:$_REQUEST[QUERY_MATCH_MODE]);
    $cid = (array_key_exists(QUERY_CHANNEL,$_REQUEST))?(int)$_REQUEST[QUERY_CHANNEL]:ALL_CHANNELS_ID;
    search($_REQUEST[QUERY_PRM], $matchMode, $cid);
} else {
    rss_header("Search",LOCATION_SEARCH,"document.getElementById('query').focus()");  
    sideChannels(false);
    list($cnt) = mysql_fetch_row(rss_query('select count(*) from item'));
    searchForm(sprintf(H2_SEARCH, $cnt));
}

rss_footer();

function searchForm($title,$addDiv = true) {
    
    if ($addDiv) {
	echo "\n\n<div id=\"search\" class=\"frame\">";
    } else {
	echo "\n\n<div class=\"frame\">";
    }
    echo "\n\t\t<h2>$title</h2>\n";

    echo
      "\n\t\t<form action=\"". getPath() ."search.php\" method=\"post\" id=\"srchfrm\">\n"
      ."\n\t\t<p><label for=\"query\">". SEARCH_SEARCH_QUERY ."</label><input type=\"text\" name=\"query\" "
      ." id=\"query\" value=\"". $_REQUEST[QUERY_PRM]."\"/></p>\n"

      ."\n\t\t<p><input type=\"radio\" id=\"qry_match_or\" name=\"". QUERY_MATCH_MODE 
      ."\" value=\"". SEARCH_MATCH_OR."\""
      .((array_key_exists(QUERY_MATCH_MODE,$_REQUEST) && 
	 $_REQUEST[QUERY_MATCH_MODE] == SEARCH_MATCH_OR)?" checked=\"checked\"":"")
	."/>\n"
      ."\t\t<label for=\"qry_match_or\">". SEARCH_MATCH_OR."</label>\n"

      ."\t\t<input type=\"radio\" id=\"qry_match_and\" name=\"". QUERY_MATCH_MODE 
      ."\" value=\"". SEARCH_MATCH_AND ."\""
      .((array_key_exists(QUERY_MATCH_MODE,$_REQUEST) &&
	 $_REQUEST[QUERY_MATCH_MODE] == SEARCH_MATCH_AND || 
	 !array_key_exists(QUERY_MATCH_MODE,$_REQUEST))?" checked=\"checked\"":"")
	."/>\n"
      ."\t\t<label for=\"qry_match_and\">". SEARCH_MATCH_AND."</label>\n"

      ."\t\t<input type=\"radio\" id=\"qry_match_exact\" name=\"". QUERY_MATCH_MODE
      ."\" value=\"". SEARCH_MATCH_EXACT ."\""
      .((array_key_exists(QUERY_MATCH_MODE,$_REQUEST) &&
	 $_REQUEST[QUERY_MATCH_MODE] == SEARCH_MATCH_EXACT)?" checked=\"checked\"":"")
	."/>\n"  
      ."\t\t<label for=\"qry_match_exact\">". SEARCH_MATCH_EXACT."</label></p>\n"
      
      
      ."\n\t\t<p><label for=\"". QUERY_CHANNEL ."\">". SEARCH_CHANNELS ."</label>\n"
      ."\t\t<select name=\"".QUERY_CHANNEL."\" id=\"".QUERY_CHANNEL."\">\n"
      ."\t\t\t<option value=\"". ALL_CHANNELS_ID ."\""
      .((!array_key_exists(QUERY_CHANNEL,$_REQUEST) || $_REQUEST[QUERY_CHANNEL] == ALL_CHANNELS_ID)?" selected=\"selected\"":"")
	.">" . ALL  . "</option>\n";

    $res = rss_query( "select "
		      ." c.id, c.title  "
		      ." from channels c "
		      ." order by parent asc, title asc" );
    
    
    
    while (list($id_,$title_) = mysql_fetch_row($res)) {
	echo "\t\t\t<option value=\"$id_\""
	  .((array_key_exists(QUERY_CHANNEL,$_REQUEST) && $_REQUEST[QUERY_CHANNEL] == $id_)?" selected=\"selected\"":"")
	    .">$title_</option>\n";
    }

    echo "\t\t</select></p>\n"

      ."\n\t\t<p><input id=\"search_go\" type=\"submit\" value=\"". SEARCH_GO ."\"/></p>\n"
      ."\t\t</form>\n";
    
    //if ($addDiv) {
	echo "</div>\n";
    //}
}

function search($qry,$matchMode, $channelId) {

    $qWhere = "";
    $regMatch = "";
    
    if ($matchMode == SEARCH_MATCH_OR || $matchMode == SEARCH_MATCH_AND) {
	
	$logicSep = ($matchMode == SEARCH_MATCH_OR?"or":"and");
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
	
	$qWhere .= ($matchMode == SEARCH_MATCH_OR?" 1=0 ":" 1=1 ");
    } else {
	$logicSep = "";
	$terms[0] = $qry;
	$qWhere .= 
	  "(i.description like '%$term%' or "
	  ." i.title like '%$term%') ";
	$regMatch = $qry;
    }
    
    $sql = "select distinct i.title, c.title, c.id, i.unread, i.url, "
      ." i.description, c.icon, unix_timestamp(i.pubdate) as ts, i.id  "
      ." from item i, channels c, folders f "
      ." where i.cid=c.id and c.parent=f.id and "
      .$qWhere;

    if ($channelId != ALL_CHANNELS_ID) {
	$sql .= " and c.id = $channelId ";
    }
    

    
    if (defined('ABSOLUTE_ORDERING') && ABSOLUTE_ORDERING) {
	$sql .= " order by f.position asc, c.position asc";
    } else {
	$sql .= " order by c.parent asc, c.title asc";
    }
    

    $sql .=", i.added desc";

    //die($sql);
    
    $res0=rss_query($sql);
    $cnt = mysql_num_rows($res0);
    $items = array();

    if ($cnt > 0) {
	$items=array();
        while (list($ititle,$ctitle, $cid, $iunread, $iurl, $idescr,  $cicon, $its, $iid) = mysql_fetch_row($res0)) {

	    $descr_noTags = preg_replace("/<.+?>/","",$idescr);
	    $title_noTags = preg_replace("/<.+?>/","",$ititle);
	    	    
	    $match = false;
	    reset($terms);
	    $match = false;
	    foreach ($terms as $term) {
		$match = ($match || (stristr($descr_noTags,$term) || stristr($title_noTags, $term)));
	    }
	    
	    if ($match) {				
		if ($hits[$iid]) {
		    continue;
		}
		$hits[$iid] = true;
		
		$items[]=array($cid,$ctitle,$cicon,

			       // Credits for the regexp: mike at iaym dot com
			       // http://ch2.php.net/manual/en/function.preg-replace.php
			       $ititle,
			       $iunread,
			       $iurl,
			       preg_replace("'(?!<.*?)($regMatch)(?![^<>]*?>)'si",
					    HIT_BEFORE . "\\1" . HIT_AFTER,
					    $idescr),
			       $its,
			       $iid
			       );
	    }

        }
    }

    $cnt = count($items);

    $humanReadableQuery = implode(" ".strtoupper($logicSep)." ",$terms);
    
    $title = sprintf(
		     (($cnt > 1 || $cnt == 0)?H2_SEARCH_RESULTS_FOR:H2_SEARCH_RESULT_FOR), 
		     $cnt, "'" .$humanReadableQuery."'");

    // If we got not hit, offer the search form.
    if ($cnt > 0) {
	echo "\n\n<div id=\"items\" class=\"frame\">";
	searchForm($title,false);
	itemsList( "", $items, IL_NO_COLLAPSE);
	echo "</div>\n";
    } else {
	searchForm($title);
    }

}

?>
