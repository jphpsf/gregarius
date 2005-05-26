<?
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2005 Marco Bonetti
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
# FITNESS FOR A PARTICULAR PURPOSE.	 See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:	   mbonetti at users dot sourceforge dot net
# Web page:	   http://sourceforge.net/projects/gregarius
#
###############################################################################
# $Log$
# Revision 1.29  2005/05/26 05:10:47  mbonetti
# fix: warningn when there are no tags
#
# Revision 1.28  2005/05/20 09:35:11  mbonetti
# 0.4.2
#
# Revision 1.27  2005/05/20 07:42:21  mbonetti
# CVS Log messages in the file header
#
#
###############################################################################

require_once('init.php');

// an item should have this many tags, at most
define ('MAX_TAGS_PER_ITEM',5);

// This regexp is used both in php and javascript, basically
// it is used to filter out everything but the allowed tag
// characters, plus a whitespace
define ('ALLOWED_TAGS_REGEXP', '/[^a-zA-Z0-9\ _\.]/');

// these are the fontsizes on the weighted list at /tag/
define ('SMALLEST',9);
define ('LARGEST',45);
define ('UNIT','px');


function relatedTags($tags) {
    /* related tags */
    $twhere = "";
    foreach($tags as $tag){
	$twhere .= "t.tag='$tag' or ";
    }
    $twhere .= "1=0";

    $sql="select fid,tid from "
      . getTable('metatag') ." m, "
      . getTable('tag') ." t  where m.tid=t.id "
      ." and ($twhere)";

    //echo $sql;
    $res = rss_query($sql);
    $fids=array();
    $ctid = -1;
    while (list($fid,$tid) = rss_fetch_row($res)) {
	$fids[] = $fid;
	$tids[] = $tid;
    }
    $fids=array_unique($fids);
    $tids=array_unique($tids);

    $rtags = array();
    if (count($fids)) {
	$sql = "select t.tag, count(*) from "
	  . getTable('metatag') ." m left join " .getTable('item')
	  ." i on (m.fid=i.id), "
	  . getTable('tag') ." t "
	  ." where m.tid=t.id and m.fid in ("
	  . implode(",",$fids). ")"
	  ." and t.id not in ("
	  . implode(",",$tids) . ")";

    if (hidePrivate()) {
		$sql .=" and !(i.unread & " . FEED_MODE_PRIVATE_STATE .") ";
	 }

	  $sql .= " group by 1 order by 2 desc";

	//echo $sql;
	$res = rss_query($sql);
	while ((list($rtag,$cnt) = rss_fetch_row($res))) {
	    $rtags[$rtag] = $cnt;
	}
    }
    return $rtags;
}


if(array_key_exists('tag',$_GET)) {
    // while this one displays a list of items for the requested tag(s)
    $tag = $_GET['tag'];
    $twhere = "";
    $tarr = explode (" ",$tag);
    $hrTag = implode (" " . LBL_AND ." ",$tarr);
    $urlTag = implode("+",$tarr);
    
    foreach ($tarr as $ttkn) {
	$twhere .= " t.tag='" 
	  .trim(rss_real_escape_string($ttkn)) ."' or";
    }
    $twhere .= " 1=0";

    $sql = "select fid, count(*) as cnt from "
      .getTable('metatag')." m, ".getTable('tag')." t "
      ." where m.tid=t.id and ($twhere)  "
      ." group by fid order by 2 desc";
    
    $res = rss_query($sql);
    $ids = array();
    while ((list($id,$cnt) = rss_fetch_row($res)) && $cnt >= count($tarr)) {
	$ids[] = $id;
    }

    $gotsome = count($ids) > 0;
    if ($gotsome) {
	// ok now look up the fields for those items
	$sql = ""

	  // standard fields
	  ."select i.title,  c.title, c.id, i.unread, "
	  ." i.url, i.description, c.icon, "
	  ." if (i.pubdate is null, unix_timestamp(i.added), unix_timestamp(i.pubdate)) as ts, "
	  ." i.pubdate is not null as ispubdate, "
	  ." i.id, t.tag  "

	  // standard left-joins and normal joins
	  ." from ".getTable("item") ." i "
	  ." left join ".getTable('metatag') ." m on (i.id=m.fid) "
	  ." left join ".getTable('tag')." t on (m.tid=t.id) "
	  . ", " .getTable("channels") ." c, " .getTable("folders") ." f "

	  ." where "
	  ." i.id in (".implode(",",$ids).") "
	  ." and i.cid = c.id  and f.id=c.parent ";
	  
		if (hidePrivate()) {
			$sql .=" and !(i.unread & " . FEED_MODE_PRIVATE_STATE .") ";	      
		}
		// order by unread first
	  $sql .= " order by (i.unread & " .FEED_MODE_UNREAD_STATE." ) desc, "

	  ."f.position asc, c.position asc, i.added desc, i.id asc, t.tag";
	$res = rss_query($sql);

	$items = array();
	$allTags =array();

	if (rss_num_rows($res) > 0) {
	    $prevId = -1;
	    while (list($title_,$ctitle_, $cid_, $unread_, $url_, $descr_,  $icon_, $ts_, $iispubdate_, $iid_, $tag_) = rss_fetch_row($res)) {
		if (array_key_exists($tag_,$allTags)) {
		    $allTags[ $tag_ ]++;
		} else {
		    $allTags[ $tag_ ]=1;
		}
		if ($prevId != $iid_) {
		    $items[] = array(
				     $cid_,
				     $ctitle_,
				     $icon_ ,
				     $title_ ,
				     $unread_ ,
				     $url_ ,
				     $descr_,
				     $ts_,
				     $iispubdate_,
				     $iid_,
				     'tags' => array($tag_)
				     );
		    $prevId = $iid_;
		} else {
		    end($items);
		    $items[key($items)]['tags'][]=$tag_;
		}
	    }

	}
    }

    $rtags = relatedTags($tarr);
    $related = array();
    foreach($rtags as $rtag => $cnt) {
	$relLbl = "<a href=\""
	  .getPath() .""
	  .(getConfig('rss.output.usemodrewrite')?"tag/$rtag":"tags.php?tag=$rtag") .""
	  ."\">$rtag</a>";
	
	$relPlus=array_key_exists($rtag,$allTags);
	if ($relPlus) {
	    $relLbl .= "&nbsp;[<a "
	      ."title=\"$cnt " 
	      . ($cnt > 1 ? ITEMS:ITEM) ." "
	      . ($cnt > 1 || $cnt == 0? TAG_TAGGEDP:TAG_TAGGED) ." '"
	      . $hrTag ." " .LBL_AND .  " $rtag'\" "
	      . "href=\""
	      . getPath() .""
	      . (getConfig('rss.output.usemodrewrite')?"tag/$rtag":"tags.php?tag=$rtag") .""
	      . "+".$urlTag
	      . "\">+</a>]";
	}
	$idx = ($relPlus?$allTags[$rtag]:0);
	$related["$idx" . "_" ."$rtag"] = $relLbl ."";
    }
    krsort($related);
    // done! Render some stuff
    rss_header("Tags " . TITLE_SEP . " " . $hrTag);
    sideChannels(false);

    echo "\n\n<div id=\"items\" class=\"frame\">\n";

    if ($gotsome) {

	echo "<h2>" . count($items) . " " . (count($items) > 1 ? ITEMS:ITEM)
	  ." "
	  . (count($items) > 1 || count($items) == 0? TAG_TAGGEDP:TAG_TAGGED) .""
	  . " \"" . $hrTag . "\"</h2>\n";

	if (count($related)) {
	    echo "\n<p>" . TAG_RELATED ."\n". implode(", \n", $related) ."\n</p>\n";
	}

	itemsList ( "",  $items, IL_NO_COLLAPSE );
    } else {
	echo "<p style=\"height: 10em; text-align:center\">";
	printf(TAG_ERROR_NO_TAG,$hrTag);
	echo "</p>";
    }
    echo "</div>\n";
    rss_footer();

} elseif(array_key_exists('alltags',$_GET)) {

    // the all tags weighted list
    $sql = "select tag,count(*) as cnt from "
      . getTable('metatag') . " left join item i on (fid=i.id),"
      . getTable('tag') ." t "
      ." where tid=t.id ";
      
      if (hidePrivate()) {
			$sql .=" and !(i.unread & " . FEED_MODE_PRIVATE_STATE .") ";	      
		}
		
      $sql .= "group by tid order by 1";

	 
    $res = rss_query($sql);
    $tags = array();
    $max = 0;
    $min = 100000;
    $cntr=0;
    while(list($tag,$cnt) = rss_fetch_row($res)) {
	$tags[$tag] = $cnt;
	$cntr++;
    }

    // Credits: Matt, http://www.hitormiss.org/about/
    // http://dev.wp-plugins.org/file/weighted-category-list/weighted_categories.php?format=txt
    if (count($tags)) {
		$spread = max($tags) - min($tags);
		if ($spread <= 0) { $spread = 1; };
		$fontspread = LARGEST - SMALLEST;
		$fontstep = $spread / $fontspread;
		if ($fontspread <= 0) { $fontspread = 1; }
		$ret = "";
		foreach ($tags as $tag => $cnt) {
		$taglink = getPath() .
		  (getConfig('rss.output.usemodrewrite')?"tag/$tag":"tags.php?tag=$tag");
		$ret .= "\t<a href=\"$taglink\" title=\"$cnt "
		  .($cnt > 1 || $cnt == 0 ? ITEMS:ITEM)."\" style=\"font-size: ".
		  (SMALLEST + ($cnt/$fontstep)). UNIT.";\">$tag</a> \n";
		}
    } else {
    	$ret ="";
    }

    // done! Render some stuff
    rss_header("Tags " . TITLE_SEP . " " . TAG_ALL_TAGS);
    sideChannels(false);
    echo "\n\n<div id=\"items\" class=\"frame\">\n"
      //."<h2>$cntr " . TAG_TAGS . "</h2>\n"
      ."<div id=\"alltags\" class=\"frame\">$ret</div>\n"
      ."\n\n</div>\n";
    rss_footer();
}

?>
