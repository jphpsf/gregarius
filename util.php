<?php
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

function rss_header($title = "", $active = 0, $cidfid = null, $onLoadAction = "", $options = HDR_NONE, $links = NULL) {
	if (!($options & HDR_NO_CACHECONTROL) && getConfig('rss.output.cachecontrol')) {
		$etag = getETag();
		$hdrs = getallheaders();
		if (array_key_exists('If-None-Match', $hdrs) && $hdrs['If-None-Match'] == $etag) {
			header("HTTP/1.1 304 Not Modified");
			flush();
			exit ();
		} else {
			header('Last-Modified: '.gmstrftime("%a, %d %b %Y %T %Z", getLastModif()));
			header("ETag: $etag");
		}
	}

	if (!($options & HDR_NO_OUPUTBUFFERING)) {
		if (getConfig('rss.output.compression')) {
			ob_start('ob_gzhandler');
		} else {
			ob_start();
		}
		// force a content-type and a charset
		header('Content-Type: text/html; charset='
			. (getConfig('rss.output.encoding') ? getConfig('rss.output.encoding') : DEFAULT_OUTPUT_ENCODING));

	}

	$docTitle = "";
	if (getConfig("rss.output.titleunreadcnt") && is_array($cidfid) && ($uc = getUnreadCount($cidfid['cid'], $cidfid['fid']))) {
		$docTitle .= " ($uc ".LBL_UNREAD.")";
	}

	rss_plugin_hook('rss.plugins.bodystart', null);
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" "
		."\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
		."<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">\n"."<head>\n"
		."\t<meta http-equiv=\"Content-Type\" content=\"text/html; "
		."charset=". (getConfig('rss.output.encoding') ? getConfig('rss.output.encoding') : DEFAULT_OUTPUT_ENCODING).""."\" />\n"
		."\t<title>".makeTitle($title).$docTitle."</title>\n";

	if (getConfig('rss.config.robotsmeta')) {
		$meta = 	((array_key_exists('expand', $_REQUEST) || 
					array_key_exists('collapse', $_REQUEST) || 
					array_key_exists('fcollapse', $_REQUEST) || 
					array_key_exists('fexpand', $_REQUEST) || 
					array_key_exists('dbg', $_REQUEST)) ? 'noindex,follow' : getConfig('rss.config.robotsmeta'));
		echo "\t<meta name=\"robots\" content=\"$meta\" />\n";
	}

	echo "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"".getThemePath()."layout.css\" />\n"
	."\t<link rel=\"stylesheet\" type=\"text/css\" href=\"".getThemePath()."look.css\" />\n"
	."\t<link rel=\"stylesheet\" type=\"text/css\" href=\"".getPath()."css/print.css\" media=\"print\" />\n";

	rss_plugin_hook('rss.plugins.stylesheets', null);

	if ($active == 1 && (MINUTE * getConfig('rss.config.refreshafter')) >= (40 * MINUTE)) {

		$redirect = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);

		if (substr($redirect, -1) != "/") {
			$redirect .= "/";
		}

		$redirect .= "update.php";

		echo "\t<meta http-equiv=\"refresh\" "
		." content=\"".MINUTE * getConfig('rss.config.refreshafter').";url=$redirect\" />\n";
	}

	echo "\t<link rel=\"start\" title=\"Home\" href=\"".getPath()."\" />\n";
	echo "\t<link rel=\"search\" title=\"Search\" href=\"".getPath()."search.php\" />\n";
	echo "\t<link rel=\"tags\" title=\"Tags\" href=\"".getPath(). (getConfig('rss.output.usemodrewrite') ? "tag/" : "tags.php?alltags")."\" />\n";

	if ($links != NULL) {
		foreach ($links as $rel => $link) {
			echo "\t<link rel=\"$rel\" ";
			foreach ($link as $attr => $val) {
				echo "$attr=\"".$val."\" ";
			}
			echo "/>\n";
		}
	}

	echo "\t<script type=\"text/javascript\" src=\"".getPath()."ajax.php?js\"></script>\n";
	if (getConfig('rss.output.channelcollapse')) {
		echo "\t<script type=\"text/javascript\" src=\"".getPath()."extlib/fcollapse.js\"></script>\n";
	}

	rss_plugin_hook('rss.plugins.javascript', null);

	echo "</head>\n"."<body";
	if ($onLoadAction != "") {
		echo " onload=\"$onLoadAction\"";
	}
	echo ">\n";
	nav($title, $active);
}

function rss_footer() {
	echo "\n</div>\n";

	echo "\n<div id=\"footer\" class=\"frame\">\n";
	echo "<span>\n\t<a href=\"#top\">TOP</a>\n</span>\n";

	echo "<span>\n\t<a href=\"http://devlog.gregarius.net/\">Gregarius</a> "._VERSION_." ".LBL_FTR_POWERED_BY."<a href=\"http://php.net\">PHP</a>, \n"."\t<a href=\"http://magpierss.sourceforge.net/\">MagpieRSS</a>, \n"."\t<a href=\"http://sourceforge.net/projects/kses\">kses</a>"."</span>\n";
	echo "<span>\n\tTentatively valid <a title=\"Tentatively valid XHTML: the layout"." validates, but the actual content coming from the feeds I can't do very much.\" "." href=\"http://validator.w3.org/check/referer\">XHTML1.0</a>, \n"."\t<a href=\"http://jigsaw.w3.org/css-validator/check/referer\">CSS2.0</a>\n</span>\n";

	$ts = getLastModif();
	echo "<span>\n\tLast update: ". ($ts ? rss_date(getConfig('rss.config.dateformat'), $ts) : "never")."\n</span>\n";

	echo "</div>\n\n";

	rss_plugin_hook('rss.plugins.bodyend', null);
	echo "</body>\n"."</html>\n";
}

function getLastModif() {
	static $ret;
	if ($ret == 0) {
		$res = rss_query("select unix_timestamp(max(added)) as max_added from ".getTable("item"));
		list ($ret) = rss_fetch_row($res);
	}
	return $ret;
}

function getETag() {
	return md5(getLastModif().$_SERVER['PHP_SELF']);
}

function nav($title, $active = 0) {
	echo "<div id=\"nav\" class=\"frame\">"."\n<a class=\"hidden\" href=\"#feedcontent\">skip to content</a>\n"
		."\n<h1 id=\"top\">".makeTitle($title)."</h1>\n"
		."<ul class=\"navlist\">\n"
		."\t<li". ($active == LOCATION_HOME ? " class=\"active\"" : "")."><a accesskey=\"h\" href=\"".getPath()."\">".LBL_NAV_HOME."</a></li>\n"
		."\t<li". ($active == LOCATION_UPDATE ? " class=\"active\"" : "")."><a accesskey=\"u\" href=\"".getPath()."update.php\">".LBL_NAV_UPDATE."</a></li>\n"
		."\t<li". ($active == LOCATION_SEARCH ? " class=\"active\"" : "")."><a accesskey=\"s\" href=\"".getPath()."search.php\">".LBL_NAV_SEARCH."</a></li>\n"
		."\t<li". ($active == LOCATION_ADMIN ? " class=\"active\"" : "")."><a accesskey=\"d\" href=\"".getPath()."admin/\">".LBL_NAV_CHANNEL_ADMIN."</a></li>\n";

	if (getConfig('rss.config.showdevloglink')) {
		echo "\t<li><a accesskey=\"l\" href=\"http://devlog.gregarius.net/\">".LBL_NAV_DEVLOG."</a></li>\n";
	}
	rss_plugin_hook('rss.plugins.navelements', null);
	echo "</ul>\n</div>\n";

	echo "<div id=\"ctnr\">\n";
}

function rss_error($message, $returnonly = false) {
	if (!$returnonly) {
		echo "\n<p class=\"error\">$message</p>\n";
		return;
	}
	return $message;
}

/** this functions checks whether a URI exists */
function getHttpResponseCode($forUri) {
	return getUrl($forUri, 255);
}

function getContentType($link, & $contentType) {
	$url_parts = @ parse_url($link);
	if (empty ($url_parts["host"])) {
		return (false);
	}
	if (!empty ($url_parts["path"])) {
		$documentpath = $url_parts["path"];
	} else {
		$documentpath = "/";
	}
	if (!empty ($url_parts["query"])) {
		$documentpath .= "?".$url_parts["query"];
	}
	$host = $url_parts["host"];
	$port = (array_key_exists('port', $url_parts) ? $url_parts["port"] : "80");

	$socket = @ fsockopen($host, $port, $errno, $errstr, 30);		
	if (!$socket) {
		return (false);
	}
	
	$ret = false; 
	fwrite($socket, "GET ".$documentpath." HTTP/1.0\r\nHost: $host\r\n\r\n");
	while (!feof($socket)) {
		$line = fgets($socket, 100);
		if (preg_match("/Content-Type: (.*)/i", $line, $matches)) {
			$contentType = $matches[1];
			$ret = true;
			break;
		}
	}
	
	return $ret;
}

// basically strips folder resources from URIs.
// http://pear.php.net/package/HTTP_Client/ --> http://pear.php.net/
function get_host($url, & $host) {
	$ret = preg_match("/^(http:\/\/)?([^\/]+)/i", $url, $matches);
	$host = $matches[2];

	//ensure we have a slash
	if (substr($host, -1) != "/") {
		$host .= "/";
	}

	return $ret;
}

/**
 * Builds a title out of an already encoded strings.
 */
function makeTitle($title) {

	$ret = ""._TITLE_."";
	if ($title != "") {
		$ret .= " ".TITLE_SEP." ".$title;
	}
	return $ret;
}

/*** update the given feed(s) **/
function update($id) {
	$kses_allowed = getConfig('rss.input.allowed'); //getAllowedTags();
	$updatedIds = array ();

	$sql = "select id, url, title, mode from ".getTable("channels");
	if ($id != "" && is_numeric($id)) {
		$sql .= " where id=$id";
	}

	if (getConfig('rss.config.absoluteordering')) {
		$sql .= " order by parent, position";
	} else {
		$sql .= " order by parent, title";
	}

	$res = rss_query($sql);
	while (list ($cid, $url, $title, $mode) = rss_fetch_row($res)) {

		// suppress warnings because Magpie is rather noisy
		$old_level = error_reporting(E_ERROR);
		$rss = fetch_rss($url);

		//reset
		error_reporting($old_level);

		if (!$rss && $id != "" && is_numeric($id)) {
			return array (magpie_error(), array ());
		}
		elseif (!$rss) {
			continue;
		}

		// base URL for items in this feed.
		if (array_key_exists('link', $rss->channel)) {
			$baseUrl = $rss->channel['link'];
		}

		foreach ($rss->items as $item) {

			$item = rss_plugin_hook('rss.plugins.rssitem', $item);
			// item title: strip out html tags, shouldn't supposed
			// to have any, should it?
			//$title = strip_tags($item['title']);
			$title = array_key_exists('title', $item) ? strip_tags($item['title']) : "";
			$title = str_replace('& ', '&amp; ', $title);
			// item content, if any
			if (array_key_exists('content', $item) && array_key_exists('encoded', $item['content'])) {
				$description = kses($item['content']['encoded'], $kses_allowed);
			}
			elseif (array_key_exists('description', $item)) {
				$description = kses($item['description'], $kses_allowed);
			} else {
				$description = "";
			}

			if ($description != "" && $baseUrl != "") {
				//$description = make_abs($description, $baseUrl);
				$description = relative_to_absolute($description, $baseUrl);
			}

			if ($description != "") {
				//require_once ('plugins/urlfilter.php');
				//$description = urlfilter_filter($description);
				$description = rss_plugin_hook('rss.plugins.import.description', $description);
			}

			// link
			if (array_key_exists('link', $item) && $item['link'] != "") {
				$url = $item['link'];
			} elseif (array_key_exists('guid', $item) && $item['guid'] != "") {
				$url = $item['guid'];
			} else {
				// fall back to something basic
				$url = md5($title);
			}

			// make sure the url is properly escaped
			$url = htmlentities(str_replace("'", "\\'", $url));

			// pubdate
			$cDate = -1;
			if (array_key_exists('dc', $item) && array_key_exists('date', $item['dc'])) {
				// RSS 1.0
				$cDate = parse_w3cdtf($item['dc']['date']);
			} elseif (array_key_exists('pubdate', $item)) {
				// RSS 2.0 (?)
				$cDate = strtotime($item['pubdate']);
			} elseif (array_key_exists('created', $item)) {
				// atom
				$cDate = parse_iso8601($item['created']);
			} elseif (array_key_exists('issued', $item)) {
				//Atom, alternative
				$cDate = parse_iso8601($item['issued']);
			}

			// drop items with an url exceeding our column length: we couldn't provide a
			// valid link back anyway.
			if (strlen($url) >= 255) {
				continue;
			}

			// check wether we already have this item
			$sql = "select id,description,unread from ".getTable("item")." where cid=$cid and url='$url'";
			$subres = rss_query($sql);
			list ($indb, $dbdesc, $state) = rss_fetch_row($subres);

			if ($cDate > 0) {
				$sec = "FROM_UNIXTIME($cDate)";
			} else {
				$sec = "null";
			}

			if ($indb == "") {

				list ($cid, $title, $url, $description) 
					= rss_plugin_hook('rss.plugins.items.new', array ($cid, $title, $url, $description));

				$sql = "insert into ".getTable("item")
				." (cid, added, title, url, "." description, unread, pubdate) "
				." values ("."$cid, now(), '".rss_real_escape_string($title)."', "
				." '$url', '".rss_real_escape_string($description)."', "."$mode, $sec)";

				rss_query($sql);
				
				
				$newIid = rss_insert_id();
				$updatedIds[] = $newIid;
				/* 
				 * Ticket #26: Add hook to modify the item just after it 
				 * has been inserted into the database
				 */
				rss_plugin_hook('rss.plugins.items.newiid',array($newIid,$item));
			}
			elseif (!($state & FEED_MODE_DELETED_STATE) && 
				getConfig('rss.input.allowupdates') && 
				strlen($description) > strlen($dbdesc)) {

				list ($cid, $indb, $description) = 
					rss_plugin_hook('rss.plugins.items.updated', array ($cid, $indb, $description));

				$sql = "update ".getTable("item")
					." set "." description='".rss_real_escape_string($description)."', "
					." unread = unread | ".FEED_MODE_UNREAD_STATE." where cid=$cid and id=$indb";

				rss_query($sql);
				$updatedIds[] = $indb;
			}
		}
	}

	if ($id != "" && is_numeric($id)) {
		if ($rss) {
			// when everything went well, return the error code
			// and numer of new items
			return array ($rss->rss_origin, $updatedIds);
		} else {
			return array (-1, array ());
		}
	} else {
		return array (-1, $updatedIds);
	}
}


/**
 * Renders any array of $items formatted for itemsList into
 * a RDF feed.
 *
 * Now let's all cheer and have Gregarius subscribe to it's self-generated
 * feeds, feed the infinite self-referential loop, and gaze in awe as the
 * Internet vanishes in a puff of logic!
 */
function itemsListRDF($items, $title, $baselink, $resource = "") {

		// trash the output, just in case
			@ ob_end_clean();
	ob_start();
	header('Content-Type: text/xml');

	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n"
	."<rdf:RDF\n"."\txmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n"
	."\txmlns=\"http://purl.org/rss/1.0/\"\n"
	."\txmlns:taxo=\"http://purl.org/rss/1.0/modules/taxonomy/\"\n"
	."\txmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n"
	."\txmlns:syn=\"http://purl.org/rss/1.0/modules/syndication/\"\n"
	."\txmlns:admin=\"http://webns.net/mvcb/\"\n"
	.">\n\n";

	echo "<channel rdf:about=\"".$baselink.$resource."\">\n"
	."\t<title>".htmlentities($title, ENT_QUOTES, 'UTF-8')
	."</title>\n"."\t<link>".$baselink.$resource."</link>\n"
	."\t<description></description>\n"
	."</channel>\n\n";

	while (list ($row, $item) = each($items)) {
		list ($cid, $ctitle, $cicon, $ititle, $iunread, $iurl, $idescr, $ts, $ispubdate, $iid) = $item;

		if (array_key_exists('tags', $item)) {
			$tags = $item['tags'];
			if (count($tags) && $tags[0] == NULL) {
				$tags = array ();
			}
		} else {
			$tags = array ();
		}

		$xmlTitle = htmlentities($ititle, ENT_QUOTES, 'UTF-8');
		echo "<item rdf:about=\"$iurl\">\n"."\t<title>$xmlTitle</title>\n"."\t<link>$iurl</link>\n"
		// http://www.jschreiber.com/archives/2004/03/php_and_timesta_1.html
		."\t<dc:date>".rss_date('Y-m-d\TH:i:sO', $ts)."</dc:date>\n"
		."\t<dc:subject>$xmlTitle</dc:subject>\n";

		if (count($tags)) {
			echo "\t<taxo:topics>\n"."\t\t<rdf:Bag>\n";
			foreach ($tags as $tag) {
				echo "\t\t\t<rdf:li rdf:resource=\"".$baselink.$tag."\" />\n";
			}
			echo "\t\t</rdf:Bag>\n"."\t</taxo:topics>\n";

		}
		echo "</item>\n\n";
	}
	echo "</rdf:RDF>\n";
}

function add_channel($url, $folderid = 0) {
	assert("" != $url && strlen($url) > 7);
	assert(is_numeric($folderid));

	$urlDB = $url; //htmlentities($url);

	$res = rss_query("select count(*) as channel_exists from ".getTable("channels")." where url='$urlDB'");
	list ($channel_exists) = rss_fetch_row($res);
	if ($channel_exists > 0) {
		return array (-1, "Looks like you are already subscribed to this channel");
	}

	$res = rss_query("select 1+max(position) as np from ".getTable("channels"));
	list ($np) = rss_fetch_row($res);

	if (!$np) {
		$np = "0";
	}

	// Here we go!
	$old_level = error_reporting(E_ERROR);
	$rss = fetch_rss($url);
	error_reporting($old_level);

	if ($rss) {

		if (is_object($rss) && array_key_exists('title', $rss->channel)) {
			$title = rss_real_escape_string($rss->channel['title']);
		} else {
			$title = "";
		}

		if (is_object($rss) && array_key_exists('link', $rss->channel)) {
			$siteurl = rss_real_escape_string(htmlentities($rss->channel['link']));
		} else {
			$siteurl = "";
		}

		if (is_object($rss) && array_key_exists('description', $rss->channel)) {
			$descr = rss_real_escape_string($rss->channel['description']);
		} else {
			$descr = "";
		}

		//lets see if this server has a favicon
		$icon = "";
		if (getConfig('rss.output.showfavicons')) {
			// if we got nothing so far, lets try to fall back to
			// favicons
			if ($icon == "" && $siteurl != "") {
				$match = get_host($siteurl, $host);
				$uri = "http://".$host."favicon.ico";
				if ($match && getContentType($uri, $contentType)) {
					if (preg_match("/image\/x-icon/", $contentType)) {
						$icon = $uri;
					}
				}
			}
		}

		if ($title != "") {
			$sql = "insert into ".getTable("channels")
				." (title, url, siteurl, parent, descr, dateadded, icon, position)"
				." values ('$title', '$urlDB', '$siteurl', $folderid, '$descr', now(), '$icon', $np)";

			rss_query($sql);
			$newid = rss_insert_id();
			return array ($newid, "");

		} else {
			return array (-1, "I'm sorry, I couldn't extract a valid RSS feed from <a href=\"$url\">$url</a>.");
		}
	} else {
		global $MAGPIE_ERROR;
		$retError = "I'm sorry, I couldn't retrieve <a href=\"$url\">$url</a>.";
		if ($MAGPIE_ERROR) {
			$retError .= "\n<br />$MAGPIE_ERROR\n";
		}
		return array (-1, $retError);
	}
}

/**
 * Replaces relative urls with absolute ones for anchors and images
 * Credits: Julien Mudry
 */
function relative_to_absolute($content, $feed_url) {
	preg_match('/(http|https|ftp):\/\//', $feed_url, $protocol);
	$server_url = preg_replace("/(http|https|ftp|news):\/\//", "", $feed_url);
	$server_url = preg_replace("/\/.*/", "", $server_url);

	if ($server_url == '') {
		return $content;
	}

	$new_content = preg_replace('/href="\//', 'href="'.$protocol[0].$server_url.'/', $content);
	$new_content = preg_replace('/src="\//', 'src="'.$protocol[0].$server_url.'/', $new_content);
	return $new_content;
}

/**
 * parse an ISO 8601 date, losely based on parse_w3cdtf from MagpieRSS
 */
function parse_iso8601($date_str) {
	# regex to match wc3dtf
	$pat = "/(\d{4})-?(\d{2})-?(\d{2})T?(\d{2}):?(\d{2})(:?(\d{2}))?(?:([-+])(\d{2}):?(\d{2})|(Z))?/";

	if (preg_match($pat, $date_str, $match)) {
		list ($year, $month, $day, $hours, $minutes, $seconds) 
			= array ($match[1], $match[2], $match[3], $match[4], $match[5], $match[6]);

		# calc epoch for current date assuming GMT
		$epoch = gmmktime($hours, $minutes, $seconds, $month, $day, $year);

		$offset = 0;
		if ($match[10] == 'Z') {
			# zulu time, aka GMT
		} else {
			list ($tz_mod, $tz_hour, $tz_min) = array ($match[8], $match[9], $match[10]);

			# zero out the variables
			if (!$tz_hour) {
				$tz_hour = 0;
			}
			if (!$tz_min) {
				$tz_min = 0;
			}

			$offset_secs = (($tz_hour * 60) + $tz_min) * 60;

			# is timezone ahead of GMT?	 then subtract offset
			#
			if ($tz_mod == '+') {
				$offset_secs = $offset_secs * -1;
			}

			$offset = $offset_secs;
		}
		$epoch = $epoch + $offset;
		return $epoch;
	} else {
		return -1;
	}
}

/**
 * Returns the relative path of the install dir, e.g:
 * http://host.com/thing/ -> "/thing/"
 * http://host.com/ -> "/"
 */
function getPath() {
	static $ret;
	$ret = dirname($_SERVER['PHP_SELF']);
	if (defined('RSS_FILE_LOCATION') && eregi(RSS_FILE_LOCATION."\$", $ret)) {
		$ret = substr($ret, 0, strlen($ret) - strlen(RSS_FILE_LOCATION));
	}
	if (substr($ret, -1) != "/") {
		$ret .= "/";
	}
	return $ret;
}

/**
 * builds an url for an archive link
 */
function makeArchiveUrl($ts, $channel, $cid, $dayView) {
	if (getConfig('rss.output.usemodrewrite')) {
		return (getPath()."$channel/".rss_date(($dayView ? 'Y/m/d/' : 'Y/m/'), $ts, false));
	} else {
		return (getPath()."feed.php?channel=$cid&amp;y=".rss_date('Y', $ts, false)
			."&amp;m=".rss_date('m', $ts, false). ($dayView ? ("&amp;d=".rss_date('d', $ts, false)) : ""));
	}
}

/**
 * Fetches a remote URL and returns the content
 */
function getUrl($url, $maxlen = 0) {

	rss_require('extlib/Snoopy.class.inc');
	$client = new Snoopy();
	$client->agent = MAGPIE_USER_AGENT;
	$client->use_gzip = getConfig('rss.output.compression');

	if ($maxlen) {
		$client->maxlength = $maxlen;
	}
	@ $client->fetch($url);
	return $client->results;
}

/**
 * returns an array of all (hopefully) rss/atom/rdf feeds in the document,
 * pointed by $url
 */
function extractFeeds($url) {
	$cnt = getUrl($url);
	$ret = array ();
	//find all link tags
	if (preg_match_all('|<link \w*="[^"]+"+[^>]*>|Ui', $cnt, $res)) {
		while (list ($id, $match) = each($res[0])) {
			// we only want '<link alternate=...'
			if (strpos(strtolower($match), 'alternate') && 
				!strpos(strtolower($match), 'stylesheet')  && // extract the attributes
				preg_match_all('|([a-zA-Z]*)="([^"]*)|', $match, $res2, PREG_SET_ORDER)) {
				$tmp = array ();
				//populate the return array: attr_name => attr_value
				while (list ($id2, $match2) = each($res2)) {
					$attr = strtolower(trim($match2[1]));
					$val = trim($match2[2]);
					// make sure we have absolute URI's
					if (($attr == "href") && strcasecmp(substr($val, 0, 4), "http") != 0) {
						$val = ($url.$val);
					}
					$tmp[$attr] = $val;
				}
				$ret[] = $tmp;
			}
		}
	}
	return $ret;
}

function real_strip_slashes($string) {
	if (stripslashes($string) == $string) {
		return $string;
	}
	return real_strip_slashes(stripslashes($string));
}

function rss_htmlspecialchars($in) {
	return htmlspecialchars($in, ENT_NOQUOTES, 
	(getConfig('rss.output.encoding') ? getConfig('rss.output.encoding') : DEFAULT_OUTPUT_ENCODING));
}

//// profiling ////
$__init_timer = getmicrotime();
$__prev_timer = 0;
function getmicrotime() {
	list ($usec, $sec) = explode(" ", microtime());
	return ((float) $usec + (float) $sec);
}

function timer() {
	global $__init_timer, $__prev_timer;
	$current_timer = getmicrotime();
	$ret = " t= "
		. (1000 * ($current_timer - $__init_timer))
		." ms, delta= "
		. (1000 * ($current_timer - $__prev_timer))."ms ";
	$__prev_timer = $current_timer;
	return $ret;
}

function _pf($comment, $commentOnly = false) {
	echo "\n\n\n<!-- ".$comment;
	if (!$commentOnly)
		echo timer();
	echo "   -->\n\n\n";
}

//////////////////////////////////////////////////////////

function showViewForm($curValue) {

	//default: read and unread!
	$readAndUndredaSelected = " selected=\"selected\"";
	$unreadOnlySelected = "";
	if ($curValue == SHOW_UNREAD_ONLY) {
		$readAndUndredaSelected = "";
		$unreadOnlySelected = " selected=\"selected\"";
	}

	// post back to self, we should be able to handle the request, shouldn't we.
	echo "\n<form action=\"".$_SERVER['REQUEST_URI']
		."\" method=\"post\" id=\"frmShow\">\n"
		."<p><label for=\"".SHOW_WHAT."\">".LBL_SHOW_UNREAD_ALL_SHOW."</label>\n"
		."<select name=\"".SHOW_WHAT."\" id=\"".SHOW_WHAT."\" "." onchange=\"document.getElementById('frmShow').submit();\">\n"
		."\t<option value=\"".SHOW_UNREAD_ONLY."\"$unreadOnlySelected>".LBL_SHOW_UNREAD_ALL_UNREAD_ONLY."</option>\n"
		."\t<option value=\"".SHOW_READ_AND_UNREAD."\"$readAndUndredaSelected>".LBL_SHOW_UNREAD_ALL_READ_AND_UNREAD."</option>\n"
		."</select></p>\n</form>\n";
}

define('PRIVATE_COOKIE', 'prv');
function getPrivateCookieVal($prefix = DBUNAME) {
	$val = $prefix.$_SERVER["SERVER_NAME"];
	if (defined('ADMIN_USERNAME') && defined('ADMIN_PASSWORD')) {
		$val .= ADMIN_USERNAME.ADMIN_PASSWORD;
	}
	return md5($val);
}

function logoutPrivateCookie() {
	if (array_key_exists(PRIVATE_COOKIE, $_COOKIE)) {
		unset($_COOKIE[PRIVATE_COOKIE]);
		setcookie(PRIVATE_COOKIE, "", -1, getPath());
	}
}

function hidePrivate() {
	static $ret;
	if ($ret == null) {
		if (!array_key_exists(PRIVATE_COOKIE, $_COOKIE)) {
			$ret = true;
		} else {
			$ret = !($_COOKIE[PRIVATE_COOKIE] == getPrivateCookieVal());
		}
	}

	return $ret;
}

function getThemePath() {
	$ret = getPath()."css/";
	if (($theme = getConfig('rss.output.theme')) != null) {
		$ret .= $theme."/";
	} else {
		$ret .= "default/";
	}
	return $ret;
}

function getUnreadCount($cid, $fid) {
	$sql = "select count(*) from "
	.getTable("item")	."i, ".getTable('channels')."c "
	." where i.unread & ".FEED_MODE_UNREAD_STATE."  and i.cid=c.id "
	." and !(c.mode & ".FEED_MODE_DELETED_STATE.") ";

	if (hidePrivate()) {
		$sql .= " and !(i.unread & ".FEED_MODE_PRIVATE_STATE.")";
	}

	if ($cid) {
		$sql .= " and c.id=$cid ";
	}
	elseif ($fid) {
		$sql .= " and c.parent=$fid ";
	}

	$res = rss_query($sql);
	list ($unread) = rss_fetch_row($res);
	return $unread;
}

function rss_date($fmt, $ts, $addTZOffset = true) {
	if ($addTZOffset) {
		return date($fmt, $ts +3600 * getConfig('rss.config.tzoffset'));
	}
	return date($fmt, $ts);

}
?>
