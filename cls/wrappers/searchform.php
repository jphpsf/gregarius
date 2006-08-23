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

function rss_search_title() {
	if (isset($GLOBALS['rss'] -> searchFormTitle)) {
		return $GLOBALS['rss'] -> searchFormTitle;
	}
	return "";
}

function rss_search_query() {
	return (isset($GLOBALS['rss'] -> mainObject[0] -> query) ?
		$GLOBALS['rss'] -> mainObject[0] -> query : "");
}

function rss_search_or_checked() {
	return ((array_key_exists(QUERY_MATCH_MODE,$_REQUEST) &&
	 $_REQUEST[QUERY_MATCH_MODE] == QUERY_MATCH_OR)?" checked=\"checked\"":"");
}

function rss_search_and_checked() {
	return ((array_key_exists(QUERY_MATCH_MODE,$_REQUEST) &&
	 $_REQUEST[QUERY_MATCH_MODE] == QUERY_MATCH_AND ||
	 !array_key_exists(QUERY_MATCH_MODE,$_REQUEST))?" checked=\"checked\"":"");
}

function rss_search_exact_checked() {
	return ((array_key_exists(QUERY_MATCH_MODE,$_REQUEST) &&
	 $_REQUEST[QUERY_MATCH_MODE] == QUERY_MATCH_EXACT)?" checked=\"checked\"":"");
}

function rss_search_order_date_checked() {
	return ((array_key_exists(QUERY_ORDER_BY,$_REQUEST) &&
	$_REQUEST[QUERY_ORDER_BY] == QUERY_ORDER_BY_DATE ||
	!array_key_exists(QUERY_ORDER_BY,$_REQUEST)?" checked=\"checked\"":""));
}

function rss_search_order_channel_checked() {
	return ((array_key_exists(QUERY_ORDER_BY,$_REQUEST) &&
	 $_REQUEST[QUERY_ORDER_BY] == QUERY_ORDER_BY_CHANNEL)?" checked=\"checked\"":"");
}

function rss_search_results_per_page_combo($id) {
	return "<select name=\"$id\" id=\"$id\">\n"
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
	.">".LBL_ALL."</option>\n"
    ."\t\t</select>";
}


function rss_search_channels_combo($id) {
	$ret = "\t\t<select name=\"$id\" id=\"$id\">\n"
      ."\t\t\t<option value=\"". ALL_CHANNELS_ID ."\""
      .((!array_key_exists(QUERY_CHANNEL,$_REQUEST) || $_REQUEST[QUERY_CHANNEL] == ALL_CHANNELS_ID)?" selected=\"selected\"":"")
	  .">" . LBL_ALL  . "</option>\n";

	$sql = "select "
		     ." c.id, c.title, f.name, f.id  "
		     ." from " . getTable("channels") ." c " 
         ." inner join " . getTable("folders"). " f "
		     ."   on f.id = c.parent ";
		      
	if (hidePrivate()) {
		$sql .=" and not(c.mode & " . RSS_MODE_PRIVATE_STATE .") ";	      
	}

	
	$sql .=" and not(c.mode & " . RSS_MODE_DELETED_STATE .") ";	      
	
	$sql .= " order by "
	     .((getConfig('rss.config.absoluteordering'))?"f.position asc, c.position asc":"f.name asc, c.title asc");

    $res = rss_query($sql);
    $prev_parent = -1;
	while (list ($id_, $title_, $parent_, $parent_id_) = rss_fetch_row($res)) {
		if ($prev_parent != $parent_id_) {
			if ($prev_parent > -1) {
				$ret .="\t\t\t</optgroup>\n";
			}
			if ($parent_ == "") {
				$parent_ = LBL_HOME_FOLDER;
			}
			$ret .= "\t\t\t<optgroup label=\"$parent_ /\">\n";
			$prev_parent = $parent_id_;
		}

		if (strlen($title_) > 25) {
			$title_ = substr($title_, 0, 22)."...";
		}
		$ret .= "\t\t\t\t<option value=\"$id_\"". 
			((array_key_exists(QUERY_CHANNEL, $_REQUEST) && $_REQUEST[QUERY_CHANNEL] == $id_) ? 
			" selected=\"selected\"" : "").">$title_</option>\n";
	}

    if ($prev_parent != 0) {
        $ret .= "\t\t\t</optgroup>\n";
    }

    $ret .= "\t\t</select>\n";
	
	return $ret;
}


?>
