<?php



function rss_search_title() {
	if (isset($GLOBALS['rss'] -> searchFormTitle)) {
		return $GLOBALS['rss'] -> searchFormTitle;
	}
	return "";
}

function rss_search_query() {
	return (array_key_exists(QUERY_PRM,$_REQUEST)?$_REQUEST[QUERY_PRM]:"");
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
		     ." from " . getTable("channels") ." c, " . getTable("folders"). " f "
		     ." where f.id=c.parent ";
		      
	if (hidePrivate()) {
		$sql .=" and not(c.mode & " . FEED_MODE_PRIVATE_STATE .") ";	      
	}
		      
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