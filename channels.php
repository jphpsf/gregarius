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
define ('COLLAPSE_ACTION','fcollapse');
define ('EXPAND_ACTION','fexpand');

/** displays the channel list **/
function sideChannels($activeId) {
	 echo "\n\n<div id=\"channels\" class=\"frame\">\n";	  
	 echo "<h2>".H2_CHANNELS."</h2>";

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
		." from item i, channels c, folders f "
		." where i.unread=1 and i.cid=c.id and c.parent=f.id "
		." group by 1";	
		$res  = rss_query($sql);
		
		while (list($cid,$cname,$cuc) = rss_fetch_row($res)) {
			$collapsed_folders[$cid]=$cuc;
		}
	 	
			
		//collapse action?
		$setcookie=false;
		if (array_key_exists(COLLAPSE_ACTION, $_GET)) {
			$fid_to_collapse = (int)$_GET[COLLAPSE_ACTION];
			// expanded -> collapsed

			if (!in_array($fid_to_collapse, $collapsed_ids)) {
				$setcookie = true;
			}
			$collapsed_ids[]=$fid_to_collapse;
			
		} elseif (array_key_exists(EXPAND_ACTION, $_GET)) {
			$fid_to_expand= (int)$_GET[EXPAND_ACTION];
			//	collapsed -> expanded
			if (in_array($fid_to_expand, $collapsed_ids)) {
				$key = array_search($fid_to_expand,$collapsed_ids);
				unset($collapsed_ids[$key]);
				$setcookie = true;
			}
		}
		
		sort($collapsed_ids);
		if ($setcookie) {
			if (count($collapsed_ids) > 0) {
				$cookie = implode(":",$collapsed_ids);
				$period = time()+COOKIE_LIFESPAN;
			} else {
				$cookie = FALSE;
				$period = time() - 3600;
			}
			
			setcookie(COLLAPSED_FOLDERS_COOKIE, $cookie, $period);
		}
			
	}
	
	 $sql = "select "
		." c.id, c.title, c.url, c.siteurl, f.name, c.parent, c.icon, c.descr "
		." from " .getTable("channels") ." c, " .getTable("folders") ." f "
		." where f.id = c.parent";
	if (getConfig('rss.config.absoluteordering')) {
		$sql .= " order by f.position asc, c.position asc";
	} else {
		$sql .=" order by c.parent asc, c.title asc";
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
				if (!in_array($prev_parent,$collapsed_ids)) {
					echo tabs(2) ."</ul>\n";
				}
				echo tabs(1) ."</li>\n";
			}
		 
		 	if (getConfig('rss.output.channelcollapse')) {
		 		if ($iscollapsed) {
		 			$flabel = "<a href=\"index.php?".EXPAND_ACTION."=$cparent\">$fname</a>";
		 			if (array_key_exists($cparent,$collapsed_folders)) {
		 				$flabel .= " " . sprintf(UNREAD_PF,$collapsed_folders[$cparent]);
		 			}
		 		} else {
					$flabel = "<a href=\"index.php?".COLLAPSE_ACTION."=$cparent\">$fname</a>";
		 		}
		 	} else {
		 		$flabel = "$fname";
		 	}
		 	
			echo tabs(1) . "<li class=\"folder ". ($iscollapsed?"collapsed":"expanded")."\">\n"
				. tabs(2) ."<span>$flabel</span>\n";
		 	
		 	if (!$iscollapsed) {
				echo tabs(2) . "<ul>\n"; 
			}
			$prev_parent=$cparent;
		}
	
		if (!$iscollapsed) {
			echo tabs( ($cparent > 0)?3:1	) . "<li" .	 (($cid == $activeId)?" class=\"active\"":"") . ">";
			echo feed($cid, $ctitle, $curl, $csiteurl, $cico, $cdescr);
			echo "</li>\n";
		}
		
	 }
	 
	if ($prev_parent > 0) {
		if (!$iscollapsed) {
			echo tabs(2) ."</ul>\n";
		}		
	  	echo tabs(1) ."</li>\n";
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
	 
	 
	 
	if (getConfig('rss.output.showfavicons') && $ico != "") {
		// $ret .= "<img src=\"". getPath(). "imgwrp.php?url=$ico\" class=\"favicon\" alt=\"\" />";	
		$ret .= "<img src=\"$ico\" class=\"favicon\" alt=\"\" />";
	}
	 
	if (getConfig('rss.output.usemodrewrite')) {
	 	$feedUrl = getPath() . preg_replace("/[^A-Za-z0-9\.]/","_","$title") ."/";
	} else {
		$feedUrl = getPath() . "feed.php?channel=$cid";
	}
	 
	$ret .= 
		"<a" 
		.$class_
		. ($description!=""?" title=\"$description\"":"")
		." href=\"$feedUrl\">" . $title ."</a> $rdLbl";
		
		
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
