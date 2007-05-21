<?php
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2006 Marco Bonetti
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
# E-mail:	   mbonetti at gmail dot com
# Web page:	   http://gregarius.net/
#
###############################################################################

require_once ('init.php');

function relatedTags($tags) {
	/* related tags */
	$twhere = "";
	foreach ($tags as $tag) {
		$twhere .= "t.tag='$tag' or ";
	}
	$twhere .= "1=0";

	$sql = "select fid,tid,m.tdate from ".getTable('metatag') ." m "
  ."inner join " . getTable('tag') . " t on t.id = m.tid  where m.ttype = 'item'"
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
		." m left join ".getTable('item')." i on (m.fid=i.id) "
		." inner join " . getTable('tag')." t on (t.id = m.tid) "
		." where m.fid in (".implode(",", $fids).")"
		." and t.id not in (".implode(",", $tids).")";

		if (hidePrivate()) {
			$sql .= " and not(i.unread & ".RSS_MODE_PRIVATE_STATE.") ";
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
	$tag = strip_tags($_GET['tag']);
	$twhere = "";
	$tarr = explode(" ", $tag);
	$hrTag = implode(" ".__('and')." ", $tarr);
	$urlTag = implode("+", $tarr);

	foreach ($tarr as $ttkn) {
		$twhere .= " t.tag='".trim(rss_real_escape_string($ttkn))."' or";
	}
	$twhere .= " 1=0";

	$sql = "select fid, count(*) as cnt from " . getTable('metatag')." m "
	."inner join " . getTable('tag')." t on t.id = m.tid "
	." where ($twhere) "
	." and m.ttype = 'item'"
	." group by fid order by 2 desc";

	$res = rss_query($sql);
	$ids = array ();
	while ((list ($id, $cnt) = rss_fetch_row($res)) && $cnt >= count($tarr)) {
		$ids[] = $id;
	}

	$gotsome = count($ids) > 0;
	$taggedItems = new PaginatedItemList();
	if ($gotsome) {

		$sqlWhere = " i.id in (".implode(",", $ids).") ";
		// include deprecated feeds while showing tags. 
		$taggedItems->populate($sqlWhere, "", 0, -1, ITEM_SORT_HINT_MIXED, true);

		$rtags = relatedTags($tarr);
		$related = array ();
		foreach ($rtags as $rtag => $cnt) {
			$relLbl = "<a href=\"".getPath().""
			. (getConfig('rss.output.usemodrewrite') ? "tag/$rtag" : "tags.php?tag=$rtag").""."\">$rtag</a>";

			$relPlus = array_key_exists($rtag, $taggedItems->allTags);
			if ($relPlus) {
				$relLbl .= "&nbsp;[<a "."title=\"$cnt ". ($cnt > 1 ? __('items') : __('item'))." ". ($cnt > 1 || $cnt == 0 ? __('tagged') : __('tagged'))." '".$hrTag." ".__('and')." $rtag'\" "."href=\"".getPath()."". (getConfig('rss.output.usemodrewrite') ? "tag/$rtag" : "tags.php?tag=$rtag").""."+".$urlTag."\">+</a>]";
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
		$title = _TITLE_." - ".__('Tags')." - ".$hrTag;
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
		$GLOBALS['rss']->header = new Header("Tags ".TITLE_SEP." ".$hrTag);
		$GLOBALS['rss']->feedList = new FeedList(false);

		//echo "\n\n<div id=\"items\" class=\"frame\">\n";

		if ($gotsome) {

			$title = $taggedItems->itemCount." ". ($taggedItems->itemCount > 1 ? __('items') : __('item'))." "
			. ($taggedItems->itemCount > 1 || $taggedItems->itemCount == 0 ? __('tagged') : __('tagged'))
			.""." \"".$hrTag."\"";

			if (count($related)) {
				$taggedItems->beforeList = "\n<p>".__('Related tags: ')."\n".implode(", \n", $related)."\n</p>\n";
			}

			$taggedItems->setTitle($title);
			$taggedItems->setRenderOptions(IL_NO_COLLAPSE|IL_TITLE_NO_ESCAPE);
			$GLOBALS['rss']->appendContentObject($taggedItems);

			$GLOBALS['rss']->renderWithTemplate('index.php', 'items');

		} else {
			$GLOBALS['rss']->renderWithTemplate('index.php', 'items');

//			echo "<p style=\"height: 10em; text-align:center\">";
//			printf(__('Oops! No items tagged &laquo;%s&raquo; were found.'), $hrTag);
//			echo "</p>";
		}
		//echo "</div>\n";
		//rss_footer();
	}

} elseif (array_key_exists('alltags', $_GET)) {
	rss_require('cls/alltags.php');

	$GLOBALS['rss']->header = new Header("Tags ".TITLE_SEP." ".__('All Tags'));
	$GLOBALS['rss']->feedList = new FeedList(false);
	$allTags = new Tags();
	$allTags->setRenderOptions(IL_TITLE_NO_ESCAPE);
	$GLOBALS['rss']->appendContentObject($allTags);
	$GLOBALS['rss']->renderWithTemplate('index.php', 'items');
}
?>
