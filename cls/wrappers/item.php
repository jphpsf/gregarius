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

///// ITEM WRAPPERS /////

function rss_item_css_class() {
	
	
	if ($GLOBALS['rss'] -> currentItem -> isUnread) {
		return "item unread";			
	} 
	if (! isset($GLOBALS['rss']->cntr) || $GLOBALS['rss'] -> cntr == NULL) {
	   $GLOBALS['rss'] -> cntr = 0;
	}
	
	return "item " . (($GLOBALS['rss'] -> cntr % 2 == 0)?"even":"odd");
}

function rss_item_pl_title($label=LBL_PL_FOR) {
	
	if (getConfig('rss.output.usepermalinks')) {
		list ($ply, $plm, $pld) = explode(":", rss_date("Y:m:d", $GLOBALS['rss'] -> currentItem -> date, false));
		$ptitle = ($label."'".$GLOBALS['rss'] -> currentItem -> parent -> escapedTitle
			."/$ply/$plm/$pld/".$GLOBALS['rss'] -> currentItem -> escapedTitle."'");
	}
	return $ptitle;
}

function rss_item_pl_url() {
	
	list ($ply, $plm, $pld) = explode(":", rss_date("Y:m:d", $GLOBALS['rss'] -> currentItem -> date, false));
	if ($GLOBALS['rss'] -> currentItem ->escapedTitle != "" && getConfig('rss.output.usemodrewrite')) {
		return getPath()
			.$GLOBALS['rss'] -> currentItem -> parent->escapedTitle
			."/$ply/$plm/$pld/"
			.$GLOBALS['rss'] -> currentItem -> escapedTitle;
	} 
	return getPath()."feed.php?channel="
		.$GLOBALS['rss'] -> currentItem -> parent->cid
		."&amp;iid=".$GLOBALS['rss'] -> currentItem ->id
		."&amp;y=$ply&amp;m=$plm&amp;d=$pld";
	
}

function rss_item_url() {
	
	return $GLOBALS['rss'] -> currentItem -> url;	
}

function rss_item_title() {
	return $GLOBALS['rss'] -> currentItem -> title;	
}


function rss_item_id() {
	
	return $GLOBALS['rss'] -> currentItem -> id;
}

function rss_item_flags() {
	
	return $GLOBALS['rss'] -> currentItem -> flags;
}


function rss_item_date() {
	
	if ($GLOBALS['rss']->currentItem->date) {
		$date_lbl = rss_date(getConfig('rss.config.dateformat'), $GLOBALS['rss']->currentItem->date);
		
		// make a permalink url for the date (month)
		if (strpos(getConfig('rss.config.dateformat'), 'F') !== FALSE) {
			$mlbl = rss_date('F', $GLOBALS['rss']->currentItem->date, false);
			$murl = makeArchiveUrl(
				$GLOBALS['rss']->currentItem->date, 
				$GLOBALS['rss']->currentItem->parent->escapedTitle, 
				$GLOBALS['rss']->currentItem->parent->cid, 
				false);
			$date_lbl = str_replace($mlbl, "<a href=\"$murl\">$mlbl</a>", $date_lbl);
		}
		
		// make a permalink url for the date (day)
		if (strpos(getConfig('rss.config.dateformat'), 'jS') !== FALSE) {
			$dlbl = rss_date('jS', $GLOBALS['rss']->currentItem->date, false);
			$durl = makeArchiveUrl(
				$GLOBALS['rss']->currentItem->date, 
				$GLOBALS['rss']->currentItem->parent->escapedTitle, 
				$GLOBALS['rss']->currentItem->parent->cid, 
				true);
			$date_lbl = str_replace($dlbl, "<a href=\"$durl\">$dlbl</a>", $date_lbl);
		}
		return (($GLOBALS['rss']->currentItem->isPubDate?LBL_POSTED:LBL_FETCHED). $date_lbl);			
	}
}

function rss_item_date_ts() {
	return $GLOBALS['rss']->currentItem->date;
}


function rss_item_date_with_format($fmt) {
	return date($fmt,$GLOBALS['rss']->currentItem->date); 
}

function rss_item_tags() {
	
	$ret = "";
	foreach ($GLOBALS['rss']->currentItem->tags as $tag_) {
		$tag_ = trim($tag_);
		if (getConfig('rss.output.usemodrewrite')) {
			$ret .= "<a href=\"".getPath()."tag/$tag_\">$tag_</a> ";
		} else {
			$ret .= "<a href=\"".getPath()."tags.php?tag=$tag_\">$tag_</a> ";
		}
	}
	return $ret;
}

function rss_item_content() {
	
	return $GLOBALS['rss'] -> currentItem -> description;
}

function rss_item_tagslink() {
    if (getConfig('rss.output.usemodrewrite')) {
        return getPath()."tag/";
    } else {
       return getPath()."tags.php?alltags";
    }
}

function rss_item_permalink() {
	return getConfig('rss.output.usepermalinks');
}
?>