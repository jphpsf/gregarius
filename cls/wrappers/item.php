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
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:      mbonetti at gmail dot com
# Web page:    http://gregarius.net/
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
  $url = $GLOBALS['rss'] -> currentItem -> url;
  if (substr($url,0,4) == 'http') {
    return $url;
  }
  return rss_item_pl_url();
}

function rss_item_title() {
	return $GLOBALS['rss'] -> currentItem -> title;  
}

function rss_item_escaped_title() {
	return $GLOBALS['rss'] -> currentItem -> escapedTitle;
}

function rss_item_id() {
	return $GLOBALS['rss'] -> currentItem -> id;
}

function rss_item_cid() {
	return $GLOBALS['rss'] -> currentItem -> parent -> cid;
}

function rss_item_flags() {
	
	return $GLOBALS['rss'] -> currentItem -> flags;
}


function rss_item_date() {
	
	if ($GLOBALS['rss']->currentItem->date) {
		$date_fmt=getConfig('rss.config.dateformat');
		
		//define all string format that we will change
		//key: date fmt, value=strftime fmt
		
		//month possible fmt
		$tabMonthFmt=array();
		$tabMonthFmt["F"]="%B";
		$tabMonthFmt["m"]="%m";
		$tabMonthFmt["M"]="%b";
		$tabMonthFmt["n"]="%m";
		
		//day possible fmt,order is important
		$tabDayFmt=array();
		$tabDayFmt["d"]="%d";
		$tabDayFmt["D"]="%a";
		$tabDayFmt["l"]="%A";
		$tabDayFmt["j"]="%e";
		
		/*
		if (!eregi("^en",getConfig('rss.output.lang'))) {
			$tabDayFmt["jS"]="%e";
			$tabDayFmt["S"]=""; //we remove this
		}
		else $tabDayFmt["j"]="%e";
		*/
		
		$tabFmt=$tabMonthFmt+$tabDayFmt;
		$arrReplace=array();
		$i=0;
		foreach ($tabFmt as $old_fmt => $new_fmt) {
			$isDay=isset($tabDayFmt["$old_fmt"]) && $tabDayFmt["$old_fmt"];
			if ($isDay && $new_fmt && strpos($date_fmt, "${old_fmt}S")!==FALSE ) {
				//we manage the S string format, to be sure to 
				//link it properly
				$i++;
				$key="#{$i}";
				$value=rss_locale_date($new_fmt, $GLOBALS['rss']->currentItem->date, false);
				if (eregi("en",getConfig('rss.output.lang'))) {
					//we add the english suffixe only for english language
					$value.=rss_date('S',$GLOBALS['rss']->currentItem->date, false);
				}
				$arrReplace["$key"]=array("value"=>$value,"isDay"=>true);
				$old_fmt.="S";
				$date_fmt=str_replace($old_fmt,$key,$date_fmt);
			}
			else if (strpos($date_fmt, $old_fmt) !== FALSE) {
				if ($new_fmt) {
					$i++;
					$key="#{$i}";
					$value=rss_locale_date($new_fmt, $GLOBALS['rss']->currentItem->date, false);
					$arrReplace["$key"]=array("value"=>$value,"isDay"=>$isDay);
				}
				else $key="";
				$date_fmt=str_replace($old_fmt,$key,$date_fmt);
			}
		}

		//now we do the replacement and make permalink urls
		$date_lbl=rss_date($date_fmt,$GLOBALS['rss']->currentItem->date);
		if (count($arrReplace)>0) foreach ($arrReplace as $search=>$data) {
			$durl = makeArchiveUrl(
				$GLOBALS['rss']->currentItem->date, 
				$GLOBALS['rss']->currentItem->parent->escapedTitle, 
				$GLOBALS['rss']->currentItem->parent->cid, 
				$data["isDay"]);
				
			$replace="<a href=\"$durl\">$data[value]</a>";
			$date_lbl = str_replace($search, $replace, $date_lbl);
		}

/*
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
*/
		return (($GLOBALS['rss']->currentItem->isPubDate?LBL_POSTED:LBL_FETCHED). $date_lbl);			
	}
}

function rss_item_date_ts() {
	return $GLOBALS['rss']->currentItem->date;
}


function rss_item_date_with_format($fmt) {
	return rss_date($fmt,$GLOBALS['rss']->currentItem->date); 
}

function rss_item_author() {
	if (($a = $GLOBALS['rss'] -> currentItem -> author) != null) {
		$ea = preg_replace('/[^a-zA-Z0-9]+/','_',$a);
		if (getConfig('rss.output.usemodrewrite')) {
			$a = "<a href=\"".getPath() ."author/$ea\">$a</a>";
		} else {
			$a = "<a href=\"".getPath() ."author.php?author=$ea\">$a</a>";
		}
  	return LBL_BY . $a;
  }
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

function rss_item_display_tags() {
	return (
		!hidePrivate() ||
		getConfig('rss.config.publictagging') || 
		count($GLOBALS['rss']->currentItem->tags));
}

function rss_item_can_edit_tags() {
	return (!hidePrivate() || getConfig('rss.config.publictagging'));
}

function rss_item_do_rating() {
 return false; //getConfig('rss.config.rating');
}

function rss_item_rating() {
 $iid = $GLOBALS['rss']->currentItem->id;
 $ret = "\n\t\t<ul class=\"rr\" id=\"rr$iid\">\n";
 for ($r = 1; $r <= 5; $r++) {

   $cls = ($GLOBALS['rss']->currentItem->rating == $r ? " class=\"current\" ":"");
   $act = (!hidePrivate() ? "_rt($iid,$r); return false;":"return false;");
	$ret .=
		"\t\t\t<li$cls><a href=\"#\" onclick=\"$act\" class=\"r$r\">$r</a></li>\n";
	}
	$ret .= "\t\t</ul>\n";
	return $ret;
}

function rss_item_has_enclosure() {
   return ! empty($GLOBALS['rss']->currentItem->enclosure);
}

function rss_item_enclosure() {
	if ( rss_item_has_enclosure() ) {
		return $GLOBALS['rss']->currentItem->enclosure;
  }
}


?>
