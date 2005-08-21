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
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:	   mbonetti at users dot sourceforge dot net
# Web page:	   http://sourceforge.net/projects/gregarius
#
###############################################################################

require_once ('init.php');

// an item should have this many tags, at most
define('MAX_TAGS_PER_ITEM', 5);

// This regexp is used both in php and javascript, basically
// it is used to filter out everything but the allowed tag
// characters, plus a whitespace
define('ALLOWED_TAGS_REGEXP', '/[^a-zA-Z0-9\ _\.]/');

function relatedTags($tags) {
	/* related tags */
	$twhere = "";
	foreach ($tags as $tag) {
		$twhere .= "t.tag='$tag' or ";
	}
	$twhere .= "1=0";

	$sql = "select fid,tid,m.tdate from ".getTable('metatag')
	." m, ".getTable('tag')." t  where m.tid=t.id and m.ttype = 'item'"
	." and ($twhere)";

	//echo $sql;
	$res = rss_query($sql);
	$fids = array ();
	$ctid = -1;
	while (list ($fid, $tid) = rss_fetch_row($res)) {
		$fids[] = $fid;
		$tids[] = $tid;
	}
	$fids = array_unique($fids);
	$tids = array_unique($tids);

	$rtags = array ();
	if (count($fids)) {
		$sql = "select t.tag, count(*) as cnt from ".getTable('metatag')
		." m left join ".getTable('item')." i on (m.fid=i.id), "
		.getTable('tag')." t "
		." where m.tid=t.id and m.fid in (".implode(",", $fids).")"
		." and t.id not in (".implode(",", $tids).")";

		if (hidePrivate()) {
			$sql .= " and !(i.unread & ".FEED_MODE_PRIVATE_STATE.") ";
		}

		$sql .= " group by t.tag order by cnt desc";

		//echo $sql;
		$res = rss_query($sql);
		while ((list ($rtag, $cnt) = rss_fetch_row($res))) {
			$rtags[$rtag] = $cnt;
		}
	}
	return $rtags;
}

if (array_key_exists('tag', $_GET)) {
	// while this one displays a list of items for the requested tag(s)
	$tag = $_GET['tag'];
	$twhere = "";
	$tarr = explode(" ", $tag);
	$hrTag = implode(" ".LBL_AND." ", $tarr);
	$urlTag = implode("+", $tarr);

	foreach ($tarr as $ttkn) {
		$twhere .= " t.tag='".trim(rss_real_escape_string($ttkn))."' or";
	}
	$twhere .= " 1=0";

	$sql = "select fid, count(*) as cnt from "
	.getTable('metatag')." m, "
	.getTable('tag')." t "
	." where m.tid=t.id and ($twhere) "
	." and m.ttype = 'item'"
	." group by fid order by 2 desc";

	$res = rss_query($sql);
	$ids = array ();
	while ((list ($id, $cnt) = rss_fetch_row($res)) && $cnt >= count($tarr)) {
		$ids[] = $id;
	}

	$gotsome = count($ids) > 0;
	if ($gotsome) {

		$taggedItems = new ItemList();
		$sqlWhere = " i.id in (".implode(",", $ids).") ";
		$taggedItems->populate($sqlWhere);

		$rtags = relatedTags($tarr);
		$related = array ();
		foreach ($rtags as $rtag => $cnt) {
			$relLbl = "<a href=\"".getPath().""
			. (getConfig('rss.output.usemodrewrite') ? "tag/$rtag" : "tags.php?tag=$rtag").""."\">$rtag</a>";

			$relPlus = array_key_exists($rtag, $taggedItems->allTags);
			if ($relPlus) {
				$relLbl .= "&nbsp;[<a "."title=\"$cnt ". ($cnt > 1 ? LBL_ITEMS : LBL_ITEM)." ". ($cnt > 1 || $cnt == 0 ? LBL_TAG_TAGGEDP : LBL_TAG_TAGGED)." '".$hrTag." ".LBL_AND." $rtag'\" "."href=\"".getPath()."". (getConfig('rss.output.usemodrewrite') ? "tag/$rtag" : "tags.php?tag=$rtag").""."+".$urlTag."\">+</a>]";
			}
			$idx = ($relPlus ? $taggedItems->allTags[$rtag] : 0);
			$related["$idx"."_"."$rtag"] = $relLbl."";
		}
		krsort($related);
	}

	// done! Render some stuff
	if (array_key_exists('rss', $_REQUEST)) {
		rss_require('cls/rdf.php');
		// RSS view
		$title = _TITLE_." - ".LBL_TAG_TAGS." - ".$hrTag;
		$baselink = guessTransportProto().$_SERVER['HTTP_HOST'].getPath()
		. (getConfig('rss.output.usemodrewrite') ? "tag/" : "tags.php?tag=");

		if ($gotsome) {
			$rdf = new RDFItemList($taggedItems);
		} else {
			$rdf = new RDFItemList(null);
		}
		$rdf->baselink = $baselink;
		$rdf->resource = $urlTag;
		$rdf->render($title);
		exit ();
	} else {
		// HTML view
		//rss_header("Tags " . TITLE_SEP . " " . $hrTag);
		//sideChannels(false);
		$GLOBALS['rss']->header = new Header("Tags ".TITLE_SEP." ".$hrTag);
		$GLOBALS['rss']->feedList = new FeedList(false);

		//echo "\n\n<div id=\"items\" class=\"frame\">\n";

		if ($gotsome) {

			$title = $taggedItems->itemCount." ". ($taggedItems->itemCount > 1 ? LBL_ITEMS : LBL_ITEM)." "
			. ($taggedItems->itemCount > 1 || $taggedItems->itemCount == 0 ? LBL_TAG_TAGGEDP : LBL_TAG_TAGGED)
			.""." \"".$hrTag."\"";

			if (count($related)) {
				$taggedItems->beforeList = "\n<p>".LBL_TAG_RELATED."\n".implode(", \n", $related)."\n</p>\n";
			}

			$taggedItems->setTitle($title);
			$taggedItems->setRenderOptions(IL_NO_COLLAPSE|IL_TITLE_NO_ESCAPE);
			$GLOBALS['rss']->appendContentObject($taggedItems);

			$GLOBALS['rss']->renderWithTemplate('index.php', 'items');

		} else {
			echo "<p style=\"height: 10em; text-align:center\">";
			printf(LBL_TAG_ERROR_NO_TAG, $hrTag);
			echo "</p>";
		}
		//echo "</div>\n";
		//rss_footer();
	}

} elseif (array_key_exists('alltags', $_GET)) {
	rss_require('cls/alltags.php');

	$GLOBALS['rss']->header = new Header("Tags ".TITLE_SEP." ".LBL_TAG_ALL_TAGS);
	$GLOBALS['rss']->feedList = new FeedList(false);
	$allTags = new Tags();
	$allTags->setRenderOptions(IL_TITLE_NO_ESCAPE);
	$GLOBALS['rss']->appendContentObject($allTags);
	$GLOBALS['rss']->renderWithTemplate('index.php', 'items');
}
?>
