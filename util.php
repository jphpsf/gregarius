<?
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003, 2004 Marco Bonetti
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

function rss_header($title="", $active=0, $onLoadAction="") {


    if (defined('OUTPUT_COMPRESSION') && OUTPUT_COMPRESSION) {
	ob_start('ob_gzhandler');
    } else {
	ob_start();
    }

    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" "
      ."\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n"
    
//    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" "
//      ."\"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n"
      ."<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">\n"
      ."<head>\n"
      ."\t<meta http-equiv=\"Content-Type\" content=\"text/html; "
      ."charset=ISO-8859-1\" />\n"
      ."\t<title>".makeTitle($title)."</title>\n"    
      ."\t<meta name=\"robots\" content=\"NOINDEX,NOFOLLOW\"/>\n"
      ."\t<link rel=\"stylesheet\" type=\"text/css\" href=\"". getPath() ."css/css.css\"/>\n"
      ."\t<link rel=\"stylesheet\" type=\"text/css\" href=\"". getPath() ."css/print.css\" media=\"print\"/>\n";


    if ($active == 1 && defined('RELOAD_AFTER') && RELOAD_AFTER >= (30*MINUTE)) {
	echo "\t<meta http-equiv=\"refresh\" "
	  ." content=\"" . RELOAD_AFTER
	  . ";url=update.php\"/>\n";
    }

    echo "</head>\n"
      ."<body";
    if ($onLoadAction != "" ) {
	echo " onload=\"$onLoadAction\"";
    }
    echo ">\n";
    nav($title,$active);
}

function rss_footer() {
    ftr();
    echo "</body>\n"
      ."</html>\n";
}

function rss_query ($query) {
    $ret = mysql_query($query) or die ("failed to execute \"$query\": " .mysql_error());
    return $ret;
}

function nav($title, $active=0) {

    echo "<div id=\"nav\" class=\"frame\">"

      ."\n<h1 id=\"top\">".makeTitle($title)."</h1>\n"
      ."<ul id=\"navlist\">\n"
      . "\t<li".($active == LOCATION_HOME   ?" class=\"active\"":"")."><a accesskey=\"h\" href=\"". getPath() ."index.php\">".NAV_HOME."</a></li>\n"
      . "\t<li".($active == LOCATION_UPDATE ?" class=\"active\"":"")."><a accesskey=\"u\" href=\"". getPath() ."update.php\">" . NAV_UPDATE. "</a></li>\n"
      . "\t<li".($active == LOCATION_SEARCH ?" class=\"active\"":"")."><a accesskey=\"s\" href=\"". getPath() ."search.php\">".NAV_SEARCH."</a></li>\n"
      . "\t<li".($active == LOCATION_ADMIN  ?" class=\"active\"":"")."><a accesskey=\"d\" href=\"". getPath() ."channel_admin.php\">".NAV_CHANNEL_ADMIN ."</a></li>\n"
      . "</ul>\n</div>\n";
}

function ftr() {
    echo "\n<div id=\"footer\" class=\"frame\">\n";
    echo "<span>\n\t<a href=\"#top\">TOP</a>\n</span>\n";

    echo "<span>\n\t"._TITLE_ . " " ._VERSION_ . " " . FTR_POWERED_BY . "<a href=\"http://php.net\">PHP</a>, \n"
      ."\t<a href=\"http://magpierss.sourceforge.net/\">MagpieRSS</a>, \n"
      ."\t<a href=\"http://sourceforge.net/projects/kses\">kses</a> \n"
      ."\tand love\n</span>\n";

    echo "<span>\n\tBest effort valid <a href=\"http://validator.w3.org/check/referer\">XHTML1.1</a>, \n"
      ."\t<a href=\"http://jigsaw.w3.org/css-validator/check/referer\">CSS2.0</a>\n</span>\n";

    echo "<span>\n\tPage rendered ". date(DATE_FORMAT)."\n</span>\n";

    echo "</div>\n\n";
}

function rss_error($message) {
    echo "\n<p class=\"error\">$message</p>\n";
}

function add_channel($url, $folderid=0) {
    assert($url != "" && strlen($url) > 7);
    assert(is_numeric($folderid));

    $urlDB = htmlentities($url);

    $rss = fetch_rss( $url );
    /*
     *          echo "<pre>";
     *          var_dump($rss);
     *          echo "</pre>";
     * */
    if ( $rss ) {
        $title= mysql_real_escape_string ( $rss->channel['title'] );
        $siteurl= mysql_real_escape_string (htmlentities($rss->channel['link'] ));
        $descr =  mysql_real_escape_string ($rss->channel['description']);

        //lets see if this server has a favicon
        $icon = "";
        if (defined ('USE_FAVICONS') && USE_FAVICONS) {

	    // This actually works and display somethign valid,
	    // but these look more like site logos than icons. Wouldn't
	    // look good constrained to 16x16, would it? :/

	    /*
	     // first check whether this feed has an image tag
	     if (
	     array_key_exists('image',$rss) && // note: array_key_exists on an object var.
	     array_key_exists('url',$rss->image) &&
	     getHttpResponseCode($rss->image['url']))
	     *
	     {
	     *
	     // use this one by default, because it should be a
	     // format even retarded browsers like IE can dig.
	     $icon = $rss->image['url'];
	     }
	     */

	    // if we got nothing so far, lets try to fall back to
	    // favicons
	    if ($icon == "" && $rss->channel['link']  != "") {
		$match = get_host($rss->channel['link'], $host);
		$uri = "http://" . $host . "favicon.ico";
		//if ($match && (getHttpResponseCode($uri)))  {
		if ($match && getContentType($uri, $contentType)) {
		    if (preg_match("/image\/x-icon/", $contentType)) {
			$icon = $uri;
		    }

		}
	    }
        }

        if ($title != "") {
            $sql = "insert into channels (title, url, siteurl, parent, descr, dateadded, icon)"
              ." values ('$title', '$urlDB', '$siteurl', $folderid, '$descr', now(), '$icon')";

            rss_query($sql);
        } else {
            rss_error ("I'm sorry, I couldn't extract a valid RSS feed from <a href=\"$url\">$url</a>.");
        }
    } else {
        rss_error( "I'm sorry, I could'n retrieve <a href=\"$url\">$url</a>.");
    }
}

/** this functions checks wether a URI exists */
function getHttpResponseCode($forUri) {
    $fp = @fopen($forUri, "rb");
    return (($fp)?true:false);
}

function getContentType( $link,&$contentType ) {
    $url_parts = @parse_url( $link );
    if ( empty( $url_parts["host"] ) ) {
	return( false );
    }
    if ( !empty( $url_parts["path"] ) )  {
	$documentpath = $url_parts["path"];
    } else {
	$documentpath = "/";
    }
    if ( !empty( $url_parts["query"] ) ) {
	$documentpath .= "?" . $url_parts["query"];
    }
    $host = $url_parts["host"];
    $port = $url_parts["port"];

    if (empty( $port )) {
	$port = "80";
    }
    $socket = @fsockopen( $host, $port, $errno, $errstr, 30 );
    $ret = false;
    if (!$socket) {
	return(false);
    } else {
	fwrite ($socket, "GET ".$documentpath." HTTP/1.0\r\nHost: $host\r\n\r\n");
	while (! feof($socket)) {
	    $line = fgets( $socket, 100 );
	    if (preg_match("/Content-Type: (.*)/i", $line, $matches)) {
		$contentType = $matches[1];
		$ret = true;
		break;
	    }
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
    if (substr($host,-1) != "/") {
	$host .= "/";
    }

    return $ret;
}

function makeTitle ($title) {

    $ret = ""._TITLE_ . "";
    if ($title != "") {
	$ret .= " &raquo; " . $title;
    }
    return $ret;
}

/*** update the given feed(s) **/

function update($id) {
    global $kses_allowed;

    $sql = "select id, url, title from channels";
    if ($id !="" && is_numeric($id)) {
	$sql .= " where id=$id";
    }

    $res = rss_query($sql);
    while (list($cid, $url, $title) = mysql_fetch_row($res)) {
	$rss = fetch_rss( $url );

	if (!$rss && $id != "" && is_numeric($id)) {
	    return magpie_error();
	} elseif (!$rss) {
	    //die ($cid . " " . $url . ": " .magpie_error());
	    continue;
	}

	// base URL for items in this feed.
	if (array_key_exists('link', $rss->channel)) {
	    $baseUrl = $rss->channel['link'];
	}

	foreach ($rss->items as $item) {

	    // item title
	    $title = $item['title'];

	    // item content, if any
	    if (array_key_exists('content',$item) && array_key_exists('encoded', $item['content'])) {
		$description = kses($item['content']['encoded'], $kses_allowed);
	    } elseif (array_key_exists ('description', $item)) {
		$description = kses($item['description'], $kses_allowed);
	    } else {
		$description = "";
	    }

	    if ($description != "" && $baseUrl != "") {
		$description = make_abs($description, $baseUrl);
	    }

	    if ($description != "") {
		require_once ('plugins/urlfilter.php');
		//require_once ('plugins/newwindow.php');
		$description = urlfilter_filter($description);
		//$description = newwindow_filter($description);
	    }

	    // link
	    if (array_key_exists('link',$item) && $item['link'] != "") {
		$url = $item['link'];
	    } elseif (array_key_exists('guid',$item) && $item['guid'] != "") {
		$url = $item['guid'];
	    } else {
		// fall back to something basic
		$url = md5($title);
	    }

	    // make sure the url is properly escaped
	    $url = htmlentities(str_replace("'","\\'",$url));

	    // pubdate
	    $cDate = -1;
	    if (array_key_exists('dc',$item) && array_key_exists('date',$item['dc'])) {
		// RSS 1.0
		$cDate =  parse_w3cdtf($item['dc']['date']);
	    } elseif (array_key_exists('pubdate',$item)) {
		// RSS 2.0 (?)
		$cDate = strtotime ($item['pubdate']);
	    } elseif (array_key_exists('created',$item)) {
		// atom
		$cDate = parse_iso8601 ($item['created']);
	    }

	    // check wether we already have this item
	    $sql = "select id from item where cid=$cid and url='$url'";
	    $subres = rss_query($sql);
	    list($indb) = mysql_fetch_row($subres);

	    if ($cDate > 0) {
		$sec = "FROM_UNIXTIME($cDate)";
	    } else {
		$sec = "null";
	    }

	    if ($indb == "") {
		$sql = "insert into item (cid, added, title, url, "
		  ." description, unread, pubdate) "
		  . " values ("
		  ."$cid, now(), '"
		  .mysql_real_escape_string(htmlentities($title)) ."', "
		  ." '$url', '"
		  . mysql_real_escape_string($description)
		    ."', 1, $sec)";

		rss_query($sql);
	    }
	}
    }

    if ($id != "" && is_numeric($id)) {
	return "";
    }
}

/**
 * renders a list of items. Returns the number of items actually shown
 */
function itemsList ($title,$items, $options = IL_NONE){

    echo "\n\n<h2>$title</h2>\n";

    $cntr=0;
    $prev_cid=0;

    $ret = 0;
    $lastAnchor = "";

    $collapsed_ids=array();
    if (defined('ALLOW_CHANNEL_COLLAPSE') && ALLOW_CHANNEL_COLLAPSE) {
	if (array_key_exists('collapsed',$_COOKIE)) {
	    $collapsed_ids = explode(":",$_COOKIE['collapsed']);
	}
    } 
        
    

    if ($options & IL_DO_STATS) {
	$stats = array();
	$stats_res = rss_query("select cid,unread,count(*) from item group by 1,2 order by 1,2");
	while (list($s_cid,$s_unread,$s_count)=mysql_fetch_row($stats_res)) {
	    $stats[$s_cid][$s_unread] = $s_count;
	}
    }
    
    
    while (list($row, $item) = each($items)) {

	list($cid, $ctitle,  $cicon, $ititle, $iunread, $iurl, $idescr, $ts) = $item;

	if (defined('ALLOW_CHANNEL_COLLAPSE') && ALLOW_CHANNEL_COLLAPSE) {
	    $collapsed = in_array($cid,$collapsed_ids) && !( $options & IL_NO_COLLAPSE);
	    	    
	    if (array_key_exists('collapse', $_GET) && $_GET['collapse'] == $cid) {
		// expanded -> collapsed
		$collapsed = true;
		if (!in_array($cid,$collapsed_ids)) {
		    $collapsed_ids[] = $cid;
		    $cookie = implode(":",$collapsed_ids);
		    setcookie('collapsed',$cookie, time()+60*60*24*999);
		}	    
	    } elseif (array_key_exists('expand', $_GET) &&$_GET['expand'] == $cid && $collapsed) {
		//  collapsed -> expanded
		$collapsed = false;
		if (in_array($cid,$collapsed_ids)) {
		    $key = array_search($cid,$collapsed_ids);
		    unset($collapsed_ids[$key]);
		    $cookie = implode(":",$collapsed_ids);
		    setcookie('collapsed',$cookie, time()+60*60*24*999);
		}		
	    }

	}

	$escaped_title = preg_replace("/[^A-Za-z0-9\.]/","_","$ctitle");

	if ($prev_cid != $cid) {
	    $prev_cid = $cid;
	    if ($cntr++ > 0) {		
		if (($options & IL_DO_NAV) && $lastAnchor != "") {
		    // link to start of channel
		    echo "<li class=\"upnav\">\n"
		      ."\t<a href=\"#$lastAnchor\">up</a>\n"
		      ."\t<a href=\"#top\">upup</a>\n"
		      ."</li>\n";
		}
		echo "</ul>\n";
	    }
	    if ($ctitle != "" && $cid > -1) {
		echo "\n<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->\n";
		echo
		  "\n<h3"
		  ." id=\"$escaped_title\" "
		  . ($collapsed?" class=\"collapsed".($iunread?" unread":"")."\"":"")
		    .">\n";

		
		if (!($options & IL_NO_COLLAPSE) && defined('ALLOW_CHANNEL_COLLAPSE') && ALLOW_CHANNEL_COLLAPSE) {
		    if ($collapsed) {
			$title = "expand '$ctitle'";
			echo "\t<a title=\"$title\" class=\"expand\" href=\"".$_SERVER['PHP_SELF'] ."?expand=$cid\">"
			  ."<img src=\"". getPath()."css/media/plus.gif\" alt=\"$title\"/>"
			  //."&nbsp;+&nbsp;"
			  ."</a>\n";
		    } else {
			$title = "collapse '$ctitle'";
			echo "\t<a title=\"$title\" class=\"collapse\" href=\"".$_SERVER['PHP_SELF'] ."?collapse=$cid\">"
			  ."<img src=\"". getPath()."css/media/minus.gif\" alt=\"$title\"/>"
			  //."&nbsp;-&nbsp;"
			  ."</a>\n";
		    }
		} elseif (defined ('USE_FAVICONS') && USE_FAVICONS && $cicon != "") {
		    echo "\t<img src=\"$cicon\" class=\"favicon\" alt=\"\"/>\n";
		}

		$anchor = "";
		if ($options & IL_DO_NAV) {
		    $lastAnchor = $ctitle . ($iunread==1?" (unread)":" (read)");
		    $anchor = "name=\"$lastAnchor\"";
		}

		if (defined('USE_MODREWRITE') && USE_MODREWRITE) {
		    echo "\t<a $anchor href=\"" .getPath() ."$escaped_title/\">$ctitle</a>\n";
		} else {
		    echo "\t<a $anchor href=\"". getPath() ."feed.php?cid=$cid\">$ctitle</a>\n";
		}
				

		
		if ($options & IL_DO_STATS) {
		    $s_unread = (int)( array_key_exists("1", $stats[$cid])?$stats[$cid][1]:0);
		    $s_total  = (int)$stats[$cid][0] + $s_unread;
		    echo "<span>"
		      .sprintf(H5_READ_UNREAD_STATS, 
			       $s_total, $s_unread
			       )
		      ."</span>\n";
		}
		
		echo "</h3>\n";
	    }

	    if (!$collapsed) {
		echo "<ul>\n";
	    }
	    
	    // reset the items per channel counter too
	    $cntr = 0;
	}

	if (!$collapsed) {
	    $cls="item";
	    if (($cntr++ % 2) == 0) {
		$cls .= " even";
	    } else {
		$cls .= " odd";
	    }

	    if  ($iunread == 1) {
		$cls .= " unread";
	    }

	    // some url fields are juste guid's which aren't actual links
	    $isUrl = (substr($iurl, 0,4) == "http");
	    echo "\t<li class=\"$cls\">\n"
	      ."\t\t<h4>";

	    if ($isUrl) {
		echo "<a href=\"$iurl\">$ititle</a>";
	    } else {
		echo $ititle;
	    }

	    echo "</h4>\n";

	    if ($ts != "") {
		echo "\t\t<h5>". POSTED . date(DATE_FORMAT, $ts). "</h5>\n";
	    }

	    if ($idescr != "" && trim(str_replace("&nbsp;","",$idescr)) != "") {
		echo "\t\t<div class=\"content\">$idescr\n\t\t</div>";
	    }
	    echo "\n\t</li>\n";
	    $ret++;
	}
    }

    if ($ret > 0) {

	if (($options & IL_DO_NAV) && $lastAnchor != "" && !$collapsed) {
	    // link to start of channel
	    
	    echo "<li class=\"upnav\">\n"
	      ."\t<a href=\"#$lastAnchor\">up</a>\n"
	      ."\t<a href=\"#top\">upup</a>\n"
	      ."</li>\n";
	}
	
	if (!$collapsed) {
	    echo "</ul>\n";
	}
    }

    return $ret;
}

/**
 * renders links in $in absolute, based on $base.
 * The regexp probably needs tuning.
 */
function make_abs($in, $base) {
    $pattern = "/(<a href=\")([^http](\/)?.*?)\">/im";
    $repl = "<a href=\"". $base. "\\2\">";
    return preg_replace($pattern, $repl, $in);
}

/**
 * parse an ISO 8601 date, losely based on parse_w3cdtf from MagpieRSS
 */
function parse_iso8601 ( $date_str ) {
    # regex to match wc3dtf
    $pat = "/(\d{4})-?(\d{2})-?(\d{2})T?(\d{2}):?(\d{2})(:?(\d{2}))?(?:([-+])(\d{2}):?(\d{2})|(Z))?/";

    if ( preg_match( $pat, $date_str, $match ) ) {
	list( $year, $month, $day, $hours, $minutes, $seconds) =
	  array( $match[1], $match[2], $match[3], $match[4], $match[5], $match[6]);

	# calc epoch for current date assuming GMT
	$epoch = gmmktime( $hours, $minutes, $seconds, $month, $day, $year);

	$offset = 0;
	if ( $match[10] == 'Z' ) {
	    # zulu time, aka GMT
	}
	else {
	    list( $tz_mod, $tz_hour, $tz_min ) =
	      array( $match[8], $match[9], $match[10]);

	    # zero out the variables
	    if ( ! $tz_hour ) { $tz_hour = 0; }
	    if ( ! $tz_min ) { $tz_min = 0; }

	    $offset_secs = (($tz_hour*60)+$tz_min)*60;

	    # is timezone ahead of GMT?  then subtract offset
	    #
	    if ( $tz_mod == '+' ) {
		$offset_secs = $offset_secs * -1;
	    }

	    $offset = $offset_secs;
	}
	$epoch = $epoch + $offset;
	return $epoch;
    }
    else {
	return -1;
    }
}

function getPath() {
    static $ret;
    $ret = dirname($_SERVER['PHP_SELF']) . "/";

    return $ret;
}
?>
