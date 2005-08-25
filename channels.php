<?php
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
# FITNESS FOR A PARTICULAR PURPOSE.	 See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA	02111-1307	USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail: mbonetti at users dot sourceforge dot net
# Web page:	http://sourceforge.net/projects/gregarius
#
###############################################################################

define ('COLLAPSED_FOLDERS_COOKIE','collapsedfolders');

/** displays the channel list **/
function sideChannels($activeId) {
    echo "\n\n<div id=\"channels\" class=\"frame\">\n";
    echo "<h2>".LBL_H2_CHANNELS."</h2>";

    stats();

    $collapsed_folders=array();
    $collapsed_ids = array();

    if (getConfig('rss.output.channelcollapse')) {

    	//read per-user stored collapsed folders
    	if (array_key_exists(COLLAPSED_FOLDERS_COOKIE, $_COOKIE)) {
    	    $collapsed_ids = explode(":",$_COOKIE[COLLAPSED_FOLDERS_COOKIE]);
    	}

    	//get unread count per folder
    	$sql = "select f.id, f.name, count(*) as cnt "
    	  ." from "
    	  .getTable('item') ." i, "
    	  .getTable('channels') . " c, "
    	  .getTable('folders') ." f "
    	  ." where i.unread & ". FEED_MODE_UNREAD_STATE
    	  ." and not(i.unread & ". FEED_MODE_DELETED_STATE .")";

    	if (hidePrivate()) {
    		$sql .=" and not(unread & " . FEED_MODE_PRIVATE_STATE .") ";
    	}

    	$sql .= " and not(c.mode & " . FEED_MODE_DELETED_STATE .") ";

    	 $sql .= " and i.cid=c.id and c.parent=f.id "
    	  ." group by 1";

    	$res  = rss_query($sql);

    	while (list($cid,$cname,$cuc) = rss_fetch_row($res)) {
    	    $collapsed_folders[$cid]=$cuc;
    	}


    	sort($collapsed_ids);
    }

    $sql = "select "
      ." c.id, c.title, c.url, c.siteurl, f.name, c.parent, c.icon, c.descr, c.mode "
      ." from " .getTable("channels") ." c, " .getTable("folders") ." f "
      ." where f.id = c.parent";
      
      if (hidePrivate()) {
			$sql .= " and not(c.mode & " . FEED_MODE_PRIVATE_STATE  .") ";
		}

	$sql .= " and not(c.mode & " . FEED_MODE_DELETED_STATE  .") ";

	
	if (getConfig('rss.config.absoluteordering')) {
		$sql .= " order by f.position asc, c.position asc";
	} else {
		$sql .= " order by f.name, c.title asc";
	}

    $res = rss_query($sql);
    $channelCount = rss_num_rows ( $res );

    $prev_parent = 0;
    echo "<ul>\n";
    while (list($cid, $ctitle, $curl, $csiteurl, $fname, $cparent, $cico, $cdescr)  = rss_fetch_row($res)) {
	//echo "\n<!-- $title -->\n";

    	$iscollapsed = in_array($cparent,$collapsed_ids) && ($cparent > 0);

    	if ($cparent != $prev_parent) {

    	    if ($prev_parent > 0) {
                echo tabs(2) ."</ul>\n";
        		echo tabs(1) ."</li>\n";
    	    }
    	    $ucLabel = "";
    	    if (getConfig('rss.output.channelcollapse')) {
                $flabel = "<a href=\"#\" onclick=\"_tgl($cparent); return false;\"><img src=\"".getThemePath()."media/folder.gif\" alt=\"$fname\" /></a>";
                if (array_key_exists($cparent,$collapsed_folders)) {
                    $sCls = ($iscollapsed?"display:inline":"display:none");
                    $ucLabel .= " " . sprintf(LBL_UNREAD_PF, "fs$cparent", $sCls, $collapsed_folders[$cparent]);
                }

    	    } else {
                $flabel = "<img src=\"".getThemePath()."media/folder.gif\" alt=\"$fname\" />";
    	    }

    	    if ( getConfig('rss.output.usemodrewrite')) {
                $rlink =  preg_replace("/[^a-zA-Z0-9_]/","_",$fname) . "/";
    	    } else {
                $rlink = "feed.php?folder=$cparent";
    	    }

    	    $flink = "<a href=\"" .getPath() . $rlink ."\">" .htmlentities($fname,ENT_COMPAT,'UTF-8') ."</a>";

    	    echo tabs(1) . "<li class=\"folder ". ($iscollapsed?"collapsed":"expanded")."\">\n"
    	      . tabs(2) . "<span>$flabel $flink $ucLabel</span>\n";


            echo tabs(2) . "<ul "
                ."id=\"fc$cparent\" "
                .($iscollapsed?" class=\"fcollapsed\" style=\"display:none\"":" class=\"fexpanded\" style=\"display:block\"")
                .">\n";
    	    $prev_parent=$cparent;
    	}


	    echo tabs( ($cparent > 0)?3:1	) . "<li" .	 (($cid == $activeId)?" class=\"active\"":"") . ">";
	    echo feed($cid, $ctitle, $curl, $csiteurl, $cico, $cdescr);
	    echo "</li>\n";


    }

    if ($prev_parent > 0) {
   	    echo tabs(2) ."</ul>\n";
    	echo tabs(1) ."</li>\n";
    }

    echo "</ul>\n";

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
    $res = rss_query ("select count(*) from " .getTable("item")
        ." where cid=$cid and unread & "  . FEED_MODE_UNREAD_STATE
        . " and not(unread & " . FEED_MODE_DELETED_STATE .")"
        );
    list($cnt) = rss_fetch_row($res);
    if ($cnt > 0) {
	$rdLbl= sprintf(LBL_UNREAD_PF, "cid$cid","",$cnt);
	$class_= " class=\"feed title unread\"";
    } else {
	$rdLbl= "";
	$class_= " class=\"feed title\" ";
    }

    $ret = "";

    if (getConfig('rss.output.showfavicons') && $ico != "") {
	// $ret .= "<img src=\"". getPath(). "imgwrp.php?url=$ico\" class=\"favicon\" alt=\"\" />";
	$ret .= "<img src=\"$ico\" class=\"favicon\" alt=\"\" />";
    }

    if (getConfig('rss.output.usemodrewrite')) {
	$feedUrl = getPath() . preg_replace("/[^A-Za-z0-9\.]/","_","$title") ."/";
    } else {
	$feedUrl = getPath() . "feed.php?channel=$cid";
    }

    // remove html from description
    $description = strip_tags($description);


    $ret .=
      "<a"
      .$class_
      . ($description!=""?" title=\"".htmlentities($description)."\"":"")
	." href=\"$feedUrl\">" . htmlspecialchars($title) ."</a> $rdLbl";

    // Display meta-information about the feed: w3 url, xml url, if active
    // debug link

    if (getConfig('rss.output.showfeedmeta') != NULL) {

        $ret .= " [<a href=\"". htmlentities($url)."\">xml</a>";

        if ($siteurl != "" && substr($siteurl,0,4) == 'http') {
            $ret .= "|<a href=\"" . htmlentities($siteurl) ."\">www</a>";
        }

        if (getConfig('rss.meta.debug')) {
            $ret .= "|<a href=\"". getPath() ."feed.php?channel=$cid&amp;dbg\">dbg</a>";
        }

        $ret .= "]";

    }

    return $ret;
}

function stats() {

    $unread = getUnreadCount(null,null);

    $res = rss_query( "select count(*) from " . getTable("item")
        ." where not(unread & " . FEED_MODE_DELETED_STATE  .") "
    );
    list($total)= rss_fetch_row($res);

    $res = rss_query( "select count(*) from " .getTable("channels")
    	." where not(mode & " .FEED_MODE_DELETED_STATE .")");
    list($channelcount)= rss_fetch_row($res);

    printf ("\n<p class=\"stats\">" . LBL_ITEMCOUNT_PF . "</p>\n"
	    ,$total, $unread, $channelcount);

}
?>
