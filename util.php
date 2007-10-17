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



function getLastModif() {
    return getProperty('__meta__','meta.lastupdate');
}

function getETag() {
    return md5(getLastModif().$_SERVER['PHP_SELF']);
}


function rss_error($message, $severity = RSS_ERROR_ERROR, $render = false) {
    if ($render) {
        echo "<p class=\"error\">$message</p>\n";
        return;
    }

    if (!isset($GLOBALS['rss'])) {
        rss_require('cls/rss.php');
    }

    $GLOBALS['rss'] -> error($message, $severity);
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
 * Builds a title out of an already encoded string.
 */
function makeTitle($title) {
    // Let us find out if the user has set a title.
    $userTitle = _TITLE_;
    if (getConfig('rss.output.title')) {
        $userTitle = getConfig('rss.output.title');
    }
    $ret = "". $userTitle ."";
    if ($title) {
        if (is_array($title)) {
            foreach($title as $token) {
                $ret .= " ".TITLE_SEP." ".$token;
            }
        } else {
            $ret .= " ".TITLE_SEP." ".$title;
        }
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
        $sql .= " and not(mode & ".RSS_MODE_DELETED_STATE.") ";
    } else {
        $sql .= " where not(mode & ".RSS_MODE_DELETED_STATE.") ";
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
        elseif (!$rss || !($rss->rss_origin & MAGPIE_FEED_ORIGIN_HTTP_200) ) {
            continue; // no need to do anything if we do not get a 200 OK from the feed
        }

        // base URL for items in this feed.
        if (array_key_exists('link', $rss->channel)) {
            $baseUrl = $rss->channel['link'];
        } else {
            $baseUrl = $url; // The feed is invalid
        }

        // Keep track of guids we've handled, because some feeds (hello,
        // Technorati!) have this insane habit of serving the same item
        // twice in the same feed.
        $guids = array();

        // Allow updates in this feed?
        $allowUpdates = getProperty($cid,'rss.input.allowupdates');
        if ($allowUpdates === null) {
            $allowUpdates = getConfig('rss.input.allowupdates');
        }

        $itemIdsInFeed = array(); // This variable will store the item id's of the elements in the feed
        foreach ($rss->items as $item) {

            $item = rss_plugin_hook('rss.plugins.rssitem', $item);
            // a plugin might delete this item
            if(!isset($item))
                continue;

            // item title: strip out html tags
            $title = array_key_exists('title', $item) ? strip_tags($item['title']) : "";
            //$title = str_replace('& ', '&amp; ', $title);


            $description = "";
            // item content, if any
            if (array_key_exists('content', $item) && is_array($item['content']) && array_key_exists('encoded', $item['content'])) {
                $description = $item['content']['encoded'];
            }
            elseif (array_key_exists('description', $item)) {
                $description = $item['description'];
            }
            elseif (array_key_exists('atom_content', $item)) {
                $description = $item['atom_content'];
            }
            elseif (array_key_exists('summary', $item)) {
                $description = $item['summary'];
            }
            else {
                $description = "";
            }

            $md5sum = "";
            $guid = "";

            if(array_key_exists('guid', $item) && $item['guid'] != "") {
                $guid = $item['guid'];
            }
            elseif(array_key_exists('id', $item) && $item['id'] != "") {
                $guid = $item['id'];
            }
            $guid = trim($guid);
            $guid = rss_real_escape_string($guid);

            // skip this one if it's an  in-feed-dupe
            if ($guid && isset($guids[$guid])) {
                continue;
            }
            elseif($guid) {
                $guids[$guid] = true;
            }

            if ($description != "") {
                $md5sum = md5($description);
                $description = kses($description, $kses_allowed); // strip out tags

                if ($baseUrl != "") {
                    $description = relative_to_absolute($description, $baseUrl);
                }
            }

            // Now let plugins modify the description
            $description = rss_plugin_hook('rss.plugins.import.description', $description);


            // link
            if (array_key_exists('link', $item) && $item['link'] != "") {
                $url = $item['link'];
            }
            elseif (array_key_exists('guid', $item) && $item['guid'] != "") {
                $url = $item['guid'];
            }
            elseif (array_key_exists('link_', $item) && $item['link_'] != "") {
                $url = $item['link_'];
            }
            else {
                // fall back to something basic
                $url = md5($title);
            }

            // make sure the url is properly escaped
            $url = htmlentities($url, ENT_QUOTES );

            $url = rss_real_escape_string($url);

            // author
            if (array_key_exists('dc', $item) && array_key_exists('creator', $item['dc'])) {
                // RSS 1.0
                $author = $item['dc']['creator'];
            } else if (array_key_exists('author_name', $item)) {
                // Atom 0.3
                $author = $item['author_name'];
            } else {
                $author = "";
            }

            $author = trim(strip_tags($author));

            // pubdate
            $cDate = -1;
            if (array_key_exists('dc', $item) && array_key_exists('date', $item['dc'])) {
                // RSS 1.0
                $cDate = parse_w3cdtf($item['dc']['date']);
            }
            elseif (array_key_exists('pubdate', $item)) {
                // RSS 2.0 (?)
                // We use the second param of strtotime here as a workaround
                // of a PHP bug with strtotime. If the pubdate field doesn't
                // contain seconds, the strtotime function will use the current
                // time to fill in seconds in PHP4. This interferes with the
                // update mechanism of gregarius. See ticket #328 for the full
                // gory details. Giving a known date as a second param to
                // strtotime fixes this problem, hence the 0 here.
                $cDate = strtotime($item['pubdate'], 0);
            }
            elseif (array_key_exists('published',$item)) {
                // atom 1.0
                $cDate = parse_iso8601($item['published']);
            }
            elseif (array_key_exists('issued', $item)) {
                //Atom, alternative
                $cDate = parse_iso8601($item['issued']);
            }
            elseif (array_key_exists('updated', $item)) {
                //Atom, alternative
                $cDate = parse_iso8601($item['updated']);
            }
            elseif (array_key_exists('created', $item)) {
                // atom 0.3
                $cDate = parse_iso8601($item['created']);
            }

            // enclosure
            if (array_key_exists('enclosure@url', $item) ) {
                $enclosure = $item['enclosure@url'];
                // If the enclosure is an image, append it to the content
				// but only if it isn't there yet
                if ($enclosure && 
					array_key_exists('enclosure@type', $item) && 
					preg_match('#image/(png|gif|jpe?g)#', $item['enclosure@type']) &&
					(FALSE == strpos($description,$enclosure))) {
                    	$description = '<img src="'.$enclosure.'" alt="" />' . $description;
                    	$enclosure = '';
                }
            } else {
                $enclosure = "";
            }

            // drop items with an url exceeding our column length: we couldn't provide a
            // valid link back anyway.
            if (strlen($url) >= 255) {
                continue;
            }

            $dbtitle = rss_real_escape_string($title);
            if (strlen($dbtitle) >= 255) {
                $dbtitle=substr($dbtitle,0,254);
            }

            if ($cDate > 0) {
                $sec = "FROM_UNIXTIME($cDate)";
            } else {
                $sec = "null";
            }

            // check whether we already have this item
            if ($guid) {
                $sql = "select id,unread, md5sum, guid, pubdate from ".getTable("item")
                       ." where cid=$cid and guid='$guid'";
            } else {
                $sql = "select id,unread, md5sum, guid, pubdate from ".getTable("item")
                       ." where cid=$cid and url='$url' and title='$dbtitle'"
                       ." and (pubdate is NULL OR pubdate=$sec)";
            }

            $subres = rss_query($sql);
            list ($indb, $state, $dbmd5sum, $dbGuid, $dbPubDate) = rss_fetch_row($subres);

            if ($indb) {
                $itemIdsInFeed[] = $indb;
                if (!($state & RSS_MODE_DELETED_STATE) && $md5sum != $dbmd5sum) {
                    // the md5sums do not match.
                    if($allowUpdates) { // Are we allowed update items in the db?
                        list ($cid, $indb, $description) =
                            rss_plugin_hook('rss.plugins.items.updated', array ($cid, $indb, $description));

                        $sql = "update ".getTable("item")
                               ." set "." description='".rss_real_escape_string($description)."', "
                               ." unread = unread | ".RSS_MODE_UNREAD_STATE
                               .", md5sum='$md5sum'" . " where cid=$cid and id=$indb";

                        rss_query($sql);
                        $updatedIds[] = $indb;
                        continue;
                    }
                }
            } else { // $indb = "" . This must be new item then. In you go.

                list ($cid, $dbtitle, $url, $description) =
                    rss_plugin_hook('rss.plugins.items.new', array ($cid, $dbtitle, $url, $description));

                $sql = "insert into ".getTable("item")
                       ." (cid, added, title, url, enclosure,"
                       ." description, author, unread, pubdate, md5sum, guid) "
                       ." values ("."$cid, now(), '$dbtitle', "
                       ." '$url', '".rss_real_escape_string($enclosure)."', '"
                       .rss_real_escape_string($description)."', '"
                       .rss_real_escape_string($author)."', "
                       ."$mode, $sec, '$md5sum', '$guid')";

                rss_query($sql);

                $newIid = rss_insert_id();
                $itemIdsInFeed[] = $newIid;
                $updatedIds[] = $newIid;
                rss_plugin_hook('rss.plugins.items.newiid',array($newIid,$item,$cid));
            } // end handling of this item

        } // end handling of all the items in this feed
        $sql = "update " .getTable("channels") . " set "." itemsincache = '"
               . serialize($itemIdsInFeed) . "' where id=$cid";
        rss_query($sql);


    } // end handling all the feeds we were asked to handle

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

function getRootFolder() {
    $sql = "select id from ".getTable("folders")."where name = '' order by position asc limit 1";
    list($root) = rss_fetch_row(rss_query($sql));

    if (!$root) {
        $root = 0;
    }

    return $root;
}

function add_channel($url, $folderid = 0, $title_=null,$descr_=null,$tags = null) {
    if (!$url || strlen($url) <= 7) {
        return array (-2, "Invalid URL $url");
    }
    if (!is_numeric($folderid)) {
        return array (-2, "Invalid folderid $folderid");
    }

    $url = sanitize(str_replace('&amp;','&',$url), RSS_SANITIZER_URL);

    $urlDB = rss_real_escape_string($url); //htmlentities($url);

    $res = rss_query("select count(*) as channel_exists from ".getTable("channels")." where url='$urlDB'");
    list ($channel_exists) = rss_fetch_row($res);
    if ($channel_exists > 0) {
        // fatal
        return array (-2, "Looks like you are already subscribed to this channel");
    }

    $res = rss_query("select 1+max(position) as np from ".getTable("channels"));
    list ($np) = rss_fetch_row($res);

    if (!$np) {
        $np = "0";
    }
			
    // Here we go!
    //error_reporting(E_ALL);
    $old_level = error_reporting(E_ERROR);
    $rss = fetch_rss($url);
    error_reporting($old_level);

    if ($rss) {
        if ($title_) {
            $title = rss_real_escape_string($title_);
        }
        elseif (is_object($rss) && array_key_exists('title#', $rss->channel)) {
            if (array_key_exists('title', $rss->channel)) {
                $title = rss_real_escape_string($rss->channel['title']);
            } else {
                $title = " ";
            }
        }
        else {
            $title = "";
        }

        if (is_object($rss) && array_key_exists('link', $rss->channel)) {
            $siteurl = rss_real_escape_string(htmlentities($rss->channel['link']));
        } else {
            $siteurl = "";
        }

				$refreshinterval = 0;
				if(is_object($rss) && array_key_exists('syn', $rss->channel)) {
						$syn = $rss->channel['syn'];

						if(array_key_exists('updateperiod', $syn)) {
								if("hourly" == $syn['updateperiod']) {
										if(array_key_exists('updatefrequency', $syn)) {
												$refreshinterval = 60 * $syn['updatefrequency'];
										}
								}
						}
				}

        if ($descr_) {
            $descr = rss_real_escape_string($descr_);
        }
        elseif  (is_object($rss) && array_key_exists('description', $rss->channel)) {
            $descr = rss_real_escape_string($rss->channel['description']);
        }
        else {
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

        $private = preg_match('|(https?://)([^:]+:[^@]+@)(.+)$|',$url);

        if ($title != "") {
            $title = strip_tags($title);
            $descr = strip_tags($descr);

            // add channel to root folder by default
            if(!$folderid) {
                $folderid = getRootFolder();
            }

            list($title,$urlDB,$siteurl,$folderid,$descr,$icon) =
                rss_plugin_hook('rss.plugins.feed.new',
                                array ($title,$urlDB,$siteurl,$folderid,$descr,$icon));

            $mode = RSS_MODE_UNREAD_STATE;
            if ($private) {
                $mode |= RSS_MODE_PRIVATE_STATE;
            }

            $sql = "insert into ".getTable("channels")
                   ." (title, url, siteurl, parent, descr, dateadded, icon, position, mode, daterefreshed)"
                   ." values ('$title', '$urlDB', '$siteurl', $folderid, '$descr', now(), '$icon', $np, $mode, '0000-00-00 00:00:00')";

            rss_query($sql);
            $newid = rss_insert_id();

            if ($icon && cacheFavicon($icon)) {
                rss_query("update " . getTable("channels") . " set icon='blob:".$icon."'"
                          ." where id=$newid");
            }

            if($tags != "") {
                __exp__submitTag($newid,$tags,"'channel'");
            }
            
            if(false == empty($refreshinterval)) {
            	setProperty($newid, 'rss.config.refreshinterval', $refreshinterval);
						}

            return array ($newid, "");

        } else {
            // non-fatal, will look further
            return array (-1, "I'm sorry, I couldn't extract a valid RSS feed from <a href=\"$url\">$url</a>.");
        }
    } else {
        global $MAGPIE_ERROR;
        $retError = "I'm sorry, I couldn't retrieve <a href=\"$url\">$url</a>.";
        if ($MAGPIE_ERROR) {
            $retError .= "\n<br />$MAGPIE_ERROR\n";
        }
        // non-fatal, will look further
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

    if (isset($protocol[0])) {
        $new_content = preg_replace('/href="\//', 'href="'.$protocol[0].$server_url.'/', $content);
        $new_content = preg_replace('/src="\//', 'src="'.$protocol[0].$server_url.'/', $new_content);
    } else {
        $new_content = $content;
    }
    return $new_content;
}

/**
 * parse an ISO 8601 date, losely based on parse_w3cdtf from MagpieRSS
 */
function parse_iso8601($date_str) {
# regex to match wc3dtf
    $pat = "/(\d{4})-?(\d{2})-?(\d{2})T?(\d{2}):?(\d{2})(:?(\d{2}\.?\d*))?(?:([-+])(\d{2}):?(\d{2})|(Z))?/";

    if (preg_match($pat, $date_str, $match)) {
        list ($year, $month, $day, $hours, $minutes, $seconds)
        = array (@$match[1], @$match[2], @$match[3], @$match[4], @$match[5], @$match[6]);

        // calc epoch for current date assuming GMT

        $epoch = gmmktime($hours, $minutes, intval($seconds), $month, $day, $year);

        $offset = 0;
        if (@$match[10] == 'Z') {
            // zulu time, aka GMT

        } else {
            list ($tz_mod, $tz_hour, $tz_min) = array (@$match[8], @$match[9], @$match[10]);

            // zero out the variables

            if (!$tz_hour) {
                $tz_hour = 0;
            }
            if (!$tz_min) {
                $tz_min = 0;
            }

            $offset_secs = (($tz_hour * 60) + $tz_min) * 60;

            // is timezone ahead of GMT?     then subtract offset


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
function getPath($path='') {
    static $ret;
    if ($ret === NULL) {
        $ret = dirname($_SERVER['PHP_SELF']);
        if (defined('RSS_FILE_LOCATION') && eregi(RSS_FILE_LOCATION."\$", $ret)) {
            $ret = substr($ret, 0, strlen($ret) - strlen(RSS_FILE_LOCATION));
        }
        if (substr($ret, -1) == "\\") { // Take off trailing backslash
            $ret = substr($ret, 0, -1);
        }
        if (substr($ret, -1) != "/") {  // Add a frontslash
            $ret .= "/";
        }
    }
    return $ret . $path;

}
$dummy = getPath();

/**
 * builds an url for an archive link
 */
function makeArchiveUrl($ts, $channel, $cid, $dayView) {
    $ret = getPath();
    if (getConfig('rss.output.usemodrewrite')) {
        if ($channel) {
            $ret .= "$channel/";
        }
        $ret .= rss_date(($dayView ? 'Y/m/d/' : 'Y/m/'), $ts, false);
    } else {
        $ret .= "feed.php?";
        if ($cid) {
            $ret .= "channel=$cid&amp;";
        }
        $ret .= "y=".rss_date('Y', $ts, false)
                ."&amp;m=".rss_date('m', $ts, false)
                . ($dayView ? ("&amp;d=".rss_date('d', $ts, false)) : "");
    }
    return $ret;
}

/**
 * Fetches a remote URL and returns the content
 */
function getUrl($url, $maxlen = 0) {
    //Bug: in windows, scheme returned by parse_url contains the drive letter
    //of the file so a test like !isset(scheme) does not work
    //maybe it would be better to only use is_file() which only detect
    //local files?
    $urlParts = parse_url($url);
    if (@is_file($url) || (!isset($urlParts['scheme']) && !isset($urlParts['host'])) ) {
        //local file!
        $c = "";
        $h = @fopen($url, "r");
        if ($h) {
            while (!feof($h)) {
                $c .= @fread($h, 8192);
            }
        }
        @fclose($h);
        return $c;
    }

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
 * Feed Autodiscovery 
 * 
 * returns an array of all (hopefully) rss/atom/rdf feeds in the document,
 * pointed by $url. 
 * See http://diveintomark.org/archives/2002/06/02/important_change_to_the_link_tag
 *
 * @param string $url URL of a web document containing <link> elements
 * @return array Array of feed URLs
 */
function extractFeeds($url) {
    rss_require('extlib/uri_util.php');
    $cnt = getUrl($url);
    $ret = array ();
    //find all link tags
    if (preg_match_all('|<link\s+\w*=["\'][^"\']+["\']+[^>]*>|Uis', $cnt, $res)) {
        while (list ($id, $match) = each($res[0])) {
            // we only want '<link alternate=...'
            if (strpos(strtolower($match), 'alternate') &&
                    !strpos(strtolower($match), 'stylesheet')  && // extract the attributes
                    preg_match_all('|([a-zA-Z]*)=["\']([^"\']*)|', $match, $res2, PREG_SET_ORDER)) {
                $tmp = array ();
                //populate the return array: attr_name => attr_value
                while (list ($id2, $match2) = each($res2)) {
                    $attr = strtolower(trim($match2[1]));
                    $val = trim($match2[2]);
                    // make sure we have absolute URI's
                    if ($attr == "href") {
                        $val = absolute_uri($val, $url);
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

function firstNwords($text, $count=7) {
    $new = "";
    $expr = '/(.+?\s+){1,' . $count . '}/';
    if ( preg_match($expr, $text, $matches) ) {
        $result = $matches[0] . '...';
        $new = preg_replace('/(\r\n|\r|\n)/', ' ', $result);
        $new = strip_tags($new);
    }
    return $new;
}

/** Props: mr at bbp dot biz - http://ch2.php.net/substr */
function html_substr($posttext, $minimum_length = 200, $length_offset = 20, $cut_words = FALSE, $dots = TRUE) {

    // $minimum_length:
    // The approximate length you want the concatenated text to be


    // $length_offset:
    // The variation in how long the text can be in this example text
    // length will be between 200 and 200-20=180 characters and the
    // character where the last tag ends

    // Reset tag counter & quote checker
    $tag_counter = 0;
    $quotes_on = FALSE;
    // Check if the text is too long
    if (strlen($posttext) > $minimum_length) {
        // Reset the tag_counter and pass through (part of) the entire text
        $c = 0;
        for ($i = 0; $i < strlen($posttext); $i++) {
            // Load the current character and the next one
            // if the string has not arrived at the last character
            $current_char = substr($posttext,$i,1);
            if ($i < strlen($posttext) - 1) {
                $next_char = substr($posttext,$i + 1,1);
            } else {
                $next_char = "";
            }
            // First check if quotes are on
            if (!$quotes_on) {
                // Check if it's a tag
                // On a "<" add 3 if it's an opening tag (like <a href...)
                // or add only 1 if it's an ending tag (like </a>)
                if ($current_char == '<') {
                    if ($next_char == '/') {
                        $tag_counter += 1;
                    } else {
                        $tag_counter += 3;
                    }
                }
                // Slash signifies an ending (like </a> or ... />)
                // substract 2
                if ($current_char == '/' && $tag_counter <> 0)
                    $tag_counter -= 2;
                // On a ">" substract 1
                if ($current_char == '>')
                    $tag_counter -= 1;
                // If quotes are encountered, start ignoring the tags
                // (for directory slashes)
                if ($current_char == '"')
                    $quotes_on = TRUE;
            } else {
                // IF quotes are encountered again, turn it back off
                if ($current_char == '"')
                    $quotes_on = FALSE;
            }

            // Count only the chars outside html tags
            if($tag_counter == 2 || $tag_counter == 0) {
                $c++;
            }

            // Check if the counter has reached the minimum length yet,
            // then wait for the tag_counter to become 0, and chop the string there
            if ($c > $minimum_length - $length_offset && $tag_counter == 0 && ($next_char == ' ' || $cut_words == TRUE)) {
                $posttext = substr($posttext,0,$i + 1);
                if($dots) {
                    $posttext .= '...';
                }
                return $posttext;
            }
        }
    }
    return $posttext;
}


function showViewForm($args) { //$curValue, $show_private) {
    list($curValue, $show_private) = $args;

    // post back to self, we should be able to handle the request, shouldn't we.
    echo "\n<form action=\"".$_SERVER['REQUEST_URI'] ."\" method=\"post\" id=\"frmShow\">\n"
    ."<p><label for=\"".SHOW_WHAT."\">".__('Show items: ')."</label>\n"
    ."<select name=\"".SHOW_WHAT."\" id=\"".SHOW_WHAT."\" "." onchange=\"document.getElementById('frmShow').submit();\">\n"
    ."\t<option value=\"".SHOW_UNREAD_ONLY."\"" . (SHOW_UNREAD_ONLY == $curValue ? " selected=\"selected\"" : "") . ">".__('Unread only')."</option>\n"
    ."\t<option value=\"".SHOW_READ_AND_UNREAD."\"" . (SHOW_READ_AND_UNREAD == $curValue ? " selected=\"selected\"" : "") . ">".__('Read and unread')."</option>\n"
    ."</select>"
    ."</p>\n";
    /*
    		if(isLoggedIn()) {
    			echo "<p><label for=\"chkPrivate\">".__('Show Private:')."</label>\n"
    			."<input type=\"checkbox\" name=\"chkPrivate\" id=\"chkPrivate\" value=\"1\" onchange=\"if(false == document.getElementById('chkPrivate').checked) { document.getElementById('chkPrivate').value = 0; document.getElementById('chkPrivate').checked = true; } document.getElementById('frmShow').submit();\"" . (1 == $show_private ? " checked" : "") . ">\n"
    			."</p>\n";
    		}
    		*/
    echo "</form>\n";
}


function getUnreadCount($cid, $fid) {
    static $_uccache = array();
    $key_ = "key $cid $fid key";
    if (isset($_uccache[$key_])) {
        return $_uccache[$key_];
    }

    $sql = "select count(*) from " . getTable("item") ."i "
           ."inner join " . getTable('channels')." c on c.id = i.cid "
           ." where i.unread & ".RSS_MODE_UNREAD_STATE. " and not(i.unread & " .
           RSS_MODE_DELETED_STATE .") "
           ." and not(c.mode & ".RSS_MODE_DELETED_STATE.") ";

    if (hidePrivate()) {
        $sql .= " and not(i.unread & ".RSS_MODE_PRIVATE_STATE.")";
    }

    if ($cid) {
        $sql .= " and c.id=$cid ";
    }
    elseif ($fid) {
        $sql .= " and c.parent=$fid ";
    }

    $res = rss_query($sql);

    list ($_uccache[$key_]) = rss_fetch_row($res);
    return $_uccache[$key_];
}

function rss_locale_date ($fmt, $ts, $addTZOffset = true) {

    if (isset($_SERVER["WINDIR"])) {
        //%e doesnt' exists under windows!
        $fmt=str_replace("%e","%#d",$fmt);
    }

    if ($addTZOffset) {
        return utf8_encode(strftime($fmt, $ts +3600 * getConfig('rss.config.tzoffset')));
    }
    return utf8_encode(strftime($fmt, $ts));
}

function rss_date($fmt, $ts, $addTZOffset = true) {
    if ($addTZOffset) {
        return date($fmt, $ts +3600 * getConfig('rss.config.tzoffset'));
    }
    return date($fmt, $ts);

}

function _pf($msg) {
    if (defined('PROFILING') && PROFILING && isset($GLOBALS['rss']) && method_exists($GLOBALS['rss'], "_pf")) {
        $GLOBALS['rss'] -> _pf($msg);
    }
}


function guessTransportProto() {

    if (defined ('RSS_SERVER_PROTO')) {
        return RSS_SERVER_PROTO;
    }

    if (array_key_exists("SERVER_PORT",$_SERVER)) {
        if ($_SERVER["SERVER_PORT"] == 443) {
            $proto = "https://";
        } else {
            $proto = "http://";
        }
    } else {
        // best effort
        $proto = "http://";
    }
    return $proto;
}

function rss_redirect($url = "") {
    header("Location: " .
           (guessTransportProto() . $_SERVER['HTTP_HOST'] . getPath() . $url));
}

/*
fixes #117.
http://www.php.net/manual/en/function.getallheaders.php
*/
function rss_getallheaders() {
    $headers = array();
    foreach($_SERVER as $h=>$v) {
        if(ereg('HTTP_(.+)',$h,$hp)) {
            $headers[$hp[1]]=$v;
        }
    }
    return $headers;
}

// moved from ajax.php
function __exp__submitTag($id,$tags,$type = "'item'") {
    $tags = strip_tags($tags);
    $ftags = utf8_encode( preg_replace(ALLOWED_TAGS_REGEXP,'', trim($tags)));
    $tarr = array_slice(explode(" ",$ftags),0,MAX_TAGS_PER_ITEM);
    $ftags = implode(" ",__priv__updateTags($id,$tarr,$type));
    return "$id,". $ftags;
}

function __priv__updateTags($fid,$tags,$type) {
    rss_query("delete from " .getTable('metatag')
              . " where fid=$fid and ttype=$type");
    $ret = array();
    foreach($tags as $tag) {
        $ttag = trim($tag);
        if ($ttag == "" || in_array($ttag,$ret)) {
            continue;
        }
        $ttag = sanitize($ttag,
                         RSS_SANITIZER_NO_SPACES | RSS_SANITIZER_SIMPLE_SQL
                        );

        rss_query( "insert into ". getTable('tag')
                   . " (tag) values ('$ttag')", false );
        $tid = 0;
        if(rss_is_sql_error(RSS_SQL_ERROR_DUPLICATE_ROW)) {
            list($tid)=rss_fetch_row(rss_query("select id from "
                                               .getTable('tag') . " where tag='$ttag'"));
        } else {
            $tid = rss_insert_id();
        }
        if ($tid) {
            rss_query( "insert into ". getTable('metatag')
                       . " (fid,tid,ttype,tdate) values ($fid,$tid,$type,now())" );
            if (rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
                $ret[] = $ttag;
            }
        }
    }

    rss_invalidate_cache();

    sort($ret);
    return $ret;
}



/**
 * this was taken straight from WordPress
 */
function utf8_uri_encode( $utf8_string ) {
    $unicode = '';
    $values = array();
    $num_octets = 1;

    for ($i = 0; $i < strlen( $utf8_string ); $i++ ) {

        $value = ord( $utf8_string[ $i ] );

        if ( $value < 128 ) {
            $unicode .= chr($value);
        } else {
            if ( count( $values ) == 0 )
                $num_octets = ( $value < 224 ) ? 2 : 3;

            $values[] = $value;

            if ( count( $values ) == $num_octets ) {
                if ($num_octets == 3) {
                    $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
                } else {
                    $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
                }

                $values = array();
                $num_octets = 1;
            }
        }
    }

    return $unicode;
}

/*
// Deprecated in favor of the new core.php functionalities
function ETagHandler($key) {
    // This function should be used inline for speed. However if you have already
    // included util.php you might as well use it.
    if (array_key_exists('HTTP_IF_NONE_MATCH',$_SERVER) &&
            $_SERVER['HTTP_IF_NONE_MATCH'] == $key) {
        header("HTTP/1.1 304 Not Modified");
        flush();
        exit();
    } else {
        header("ETag: $key");
        // ob_start('ob_gzhandler');
    }
}
*/
//these two eval_ functions taken from the comments at http://us3.php.net/eval
/*
function eval_mixed_helper($arr) {
    return ("echo stripslashes(\"".addslashes($arr[1])."\");");
}
 
function eval_mixed($string) {
    $string = "<? ?>".$string."<? ?>";
    $string = preg_replace("/<\?=\s+(.*?)\s+\?>/", "<? echo $1; ?>", $string);
    $string = str_replace('?>', '', str_replace( array('<?php', '<?'), '', preg_replace_callback( "/\?>((.|\n)*?)<\?(php)?/","eval_mixed_helper",$string) ) );
    return $string;
}
*/

function rss_svn_rev($prefix='.') {
    static $ret;
    if ($ret != NULL) {
        return $ret;
    }
    if (file_exists(GREGARIUS_HOME .'.svn/dir-wcprops')) {
        $raw=getUrl(GREGARIUS_HOME .'.svn/dir-wcprops');
        if ($raw && preg_match('#ver/([0-9]+)/#',$raw,$matches) && isset($matches[1])) {
            $ret = $prefix . $matches[1];
        }
    } else {
        $ret = "";
    }
    return $ret;
}

function cacheFavicon($icon) {
    // Make sure only real favicons get fetched into the DB
    if (! preg_match('#^https?://.+$#',$icon)) {
        return false;
    }

    $icon_ = rss_real_escape_string($icon);
    $binIcon = getUrl($icon);
    if ($binIcon) {
        $sql = "delete from " . getTable('cache')
               ." where cachetype='icon' and cachekey='$icon_'";
        rss_query($sql);
        $sql = "insert into ". getTable('cache')
               ."(cachekey,timestamp,cachetype,data) values "
               ."('$icon_',now(),'icon','".rss_real_escape_string($binIcon)."')";
        rss_query($sql);
        return rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR);
    }
    return false;
}

function sanitize($input, $rules = 0) {
    $ret = $input;
    if ($rules & RSS_SANITIZER_SIMPLE_SQL) {
        $ret = rss_real_escape_string($ret);
    }
    if ($rules & RSS_SANITIZER_NO_SPACES) {
        $ret = preg_replace('#\s#','',$ret);
        // also strip out SQL comments
        $ret = preg_replace('#/\*.*\*/#','',$ret);
    }
    if ($rules & RSS_SANITIZER_NUMERIC) {
        $ret = preg_replace('#[^0-9\.-]#','',$ret);
    }
    if ($rules & RSS_SANITIZER_CHARACTERS) {
        $ret = preg_replace('#[^a-zA-Z]#','',$ret);
    }
    if ($rules & RSS_SANITIZER_CHARACTERS_EXT) {
        $ret = preg_replace('#[^a-zA-Z_]#','',$ret);
    }
    if ($rules & RSS_SANITIZER_WORDS) {
        $ret = preg_replace('#[^a-zA-Z0-9\-\._]#','',$ret);
    }
    if ($rules & RSS_SANITIZER_URL) {
        // filter out "unsafe" characters: {,},|,\,^,<,>
        $ret = preg_replace('#[{}\|\\\^<>]#','',$ret);
    }
    return $ret;
}

function hidePrivate() {
    $ret = 0;

    if(isLoggedIn()) {
        $ret = rss_user_show_private();
    }

    return !$ret;
}

function isLoggedIn() {
    if (!isset($GLOBALS['rssuser'])) {
        require_once('cls/user.php');
    }

    return rss_user_check_user_level(RSS_USER_LEVEL_PRIVATE);
}

// Send a crappy 404 (to save bandwidth) for webbots
function rss_404() {
    header("HTTP/1.1 404 Not Found");
    echo "404 Page Not Found\n";
}

function rss_uri($title, $sep=RSS_URI_SEPARATOR) {
    return utf8_uri_encode(preg_replace('#[ \#%&/\+\'"\?]#',$sep,$title));
}
?>
