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


function rss_header($title="", $active=0, $onLoadAction="", $options = HDR_NONE) {

    
    if (!($options & HDR_NO_CACHECONTROL) && getConfig('rss.output.cachecontrol')) {
        $etag = getETag();
	$hdrs =  getallheaders();
	if (array_key_exists('If-None-Match',$hdrs) && $hdrs['If-None-Match'] == $etag) {
	    header("HTTP/1.1 304 Not Modified");
	    flush();
	    exit();	    
	} else {	    
	    header('Last-Modified: '.gmstrftime("%a, %d %b %Y %T %Z",getLastModif()));
	    header("ETag: $etag");
	}
    }
    
	if (!($options & HDR_NO_OUPUTBUFFERING)) {
	    if (getConfig('rss.output.compression')) {
		ob_start('ob_gzhandler');
	    } else {
		ob_start();
	    }
	}
    
    
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" "
	  ."\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
	  ."<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">\n"
	  ."<head>\n"
	  ."\t<meta http-equiv=\"Content-Type\" content=\"text/html; "
	  ."charset="
	  . (getConfig('rss.output.encoding')?getConfig('rss.output.encoding'):DEFAULT_OUTPUT_ENCODING) .""
	  ."\" />\n"
	  ."\t<title>".makeTitle($title)."</title>\n";

    if (getConfig('rss.config.robotsmeta')) {
	$meta = (
		 (
		  array_key_exists('expand',$_REQUEST) || 
		  array_key_exists('collapse',$_REQUEST) ||		  
		  array_key_exists('fcollapse',$_REQUEST) ||
		  array_key_exists('fexpand',$_REQUEST) ||
		  array_key_exists('dbg',$_REQUEST)
		 )?'noindex,follow':getConfig('rss.config.robotsmeta'));
	  echo "\t<meta name=\"robots\" content=\"$meta\"/>\n";
	}

	echo "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"". getPath() ."css/layout.css\"/>\n"
	  ."\t<link rel=\"stylesheet\" type=\"text/css\" href=\"". getPath() ."css/look.css\"/>\n"
	  ."\t<link rel=\"stylesheet\" type=\"text/css\" href=\"". getPath() ."css/print.css\" media=\"print\"/>\n";

	if ($active == 1 && (MINUTE * getConfig('rss.config.refreshafter')) >= (40*MINUTE)) {
	
	$redirect = "http://"
	  . $_SERVER['HTTP_HOST']
	  . dirname($_SERVER['PHP_SELF']);
	if (substr($redirect,-1) != "/") {
		$redirect .= "/";
	}
	$redirect .= "update.php";
	
	echo "\t<meta http-equiv=\"refresh\" "
	  ." content=\"" . MINUTE * getConfig('rss.config.refreshafter')
	  . ";url=$redirect\"/>\n";
	}

    
    echo "\t<script type=\"text/javascript\" src=\"".getPath()."tags.php?js\"></script>\n";


	echo "</head>\n"
	  ."<body";
	if ($onLoadAction != "" ) {
	echo " onload=\"$onLoadAction\"";
	}
	echo ">\n";
	nav($title,$active);
}

function rss_footer() {
	echo "\n</div>\n";
	
	echo "\n<div id=\"footer\" class=\"frame\">\n";
	echo "<span>\n\t<a href=\"#top\">TOP</a>\n</span>\n";

	echo "<span>\n\t<a href=\"http://devlog.gregarius.net/\">Gregarius</a> " ._VERSION_ . " " . FTR_POWERED_BY . "<a href=\"http://php.net\">PHP</a>, \n"
	  ."\t<a href=\"http://magpierss.sourceforge.net/\">MagpieRSS</a>, \n"
	  ."\t<a href=\"http://sourceforge.net/projects/kses\">kses</a>"
	  ."</span>\n";

	echo "<span>\n\tTentatively valid <a title=\"Tentatively valid XHTML: the layout"
	  ." validates, but the actual content coming from the feeds I can't do very much.\" "
	  ." href=\"http://validator.w3.org/check/referer\">XHTML1.0</a>, \n"
	  ."\t<a href=\"http://jigsaw.w3.org/css-validator/check/referer\">CSS2.0</a>\n</span>\n";

        $ts = getLastModif();
	echo "<span>\n\tLast update: "
		.($ts?date(getConfig('rss.config.dateformat'),$ts):"never")
	  ."\n</span>\n";

	echo "</div>\n\n";
	echo "</body>\n"
	  ."</html>\n";	  
}

function getLastModif() {
    static $ret;
    if ($ret == 0) {
	$res = rss_query("select unix_timestamp(max(added)) as max_added from " . getTable("item"));
	list($ret) = rss_fetch_row($res);
    }
    return $ret;
}

function getETag() {
    return md5(getLastModif() . $_SERVER['PHP_SELF']);    
}

function nav($title, $active=0) {
	echo "<div id=\"nav\" class=\"frame\">"
	  ."\n<a class=\"hidden\" href=\"#feedcontent\">skip to content</a>\n"
	  ."\n<h1 id=\"top\">".makeTitle($title)."</h1>\n"
	  ."<ul class=\"navlist\">\n"
	  . "\t<li".($active == LOCATION_HOME	?" class=\"active\"":"")."><a accesskey=\"h\" href=\"". getPath() ."\">".NAV_HOME."</a></li>\n"
	  . "\t<li".($active == LOCATION_UPDATE ?" class=\"active\"":"")."><a accesskey=\"u\" href=\"". getPath() ."update.php\">" . NAV_UPDATE. "</a></li>\n"
	  . "\t<li".($active == LOCATION_SEARCH ?" class=\"active\"":"")."><a accesskey=\"s\" href=\"". getPath() ."search.php\">".NAV_SEARCH."</a></li>\n"
	  . "\t<li".($active == LOCATION_ADMIN	?" class=\"active\"":"")."><a accesskey=\"d\" href=\"". getPath() ."admin/\">".NAV_CHANNEL_ADMIN ."</a></li>\n";
	
	
	if (getConfig('rss.config.showdevloglink')) {
	  echo "\t<li><a accesskey=\"l\" href=\"http://devlog.gregarius.net/\">". NAV_DEVLOG ."</a></li>\n";
	}
	echo "</ul>\n</div>\n";
	
	
	echo "<div id=\"ctnr\">\n";
}


function rss_error($message, $returnonly=false) {
	if (!$returnonly) {
	echo "\n<p class=\"error\">$message</p>\n";
	return;
	}
	return $message;
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
	if ( !empty( $url_parts["path"] ) )	 {
	$documentpath = $url_parts["path"];
	} else {
	$documentpath = "/";
	}
	if ( !empty( $url_parts["query"] ) ) {
	$documentpath .= "?" . $url_parts["query"];
	}
	$host = $url_parts["host"];
	$port = (array_key_exists('port',$url_parts)?$url_parts["port"]:"80");

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


/**
 * Builds a title out of an already encoded strings.
 */
function makeTitle ($title) {

	$ret = ""._TITLE_ . "";
	if ($title != "") {
	$ret .= " ".TITLE_SEP." " .	 $title ;
	}
	return $ret;
}

/*** update the given feed(s) **/

function update($id) {
	$kses_allowed = getConfig('rss.input.allowed'); //getAllowedTags(); 
	$unreadCount = 0;
	
	$sql = "select id, url, title from ". getTable("channels");
	if ($id != "" && is_numeric($id)) {
	$sql .= " where id=$id";
	}
	
	if (getConfig('rss.config.absoluteordering')) {
	$sql .= " order by parent, position";
	} else {
	$sql .= " order by parent, title";
	}

	$res = rss_query($sql);
	while (list($cid, $url, $title) = rss_fetch_row($res)) {

	// suppress warnings because Magpie is rather noisy
	$old_level = error_reporting(E_ERROR);
	$rss = fetch_rss( $url );
	//reset
	error_reporting($old_level);

	
	if (!$rss && $id != "" && is_numeric($id)) {
		return array(magpie_error(),0);
	} elseif (!$rss) {	 
		continue;
	}

	// base URL for items in this feed.
	if (array_key_exists('link', $rss->channel)) {
		$baseUrl = $rss->channel['link'];
	}

	foreach ($rss->items as $item) {

		// item title: strip out html tags, shouldn't supposed
		// to have any, should it?
		//$title = strip_tags($item['title']);
		$title = array_key_exists('title',$item)?$item['title']:"";
		
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


		// drop items with an url exceeding our column length: we couldn't provide a
		// valid link back anyway.
		if (strlen($url) >= 255) {
		continue;
		}
		
		// check wether we already have this item		
		$sql = "select id from " .getTable("item") . " where cid=$cid and url='$url'";
		$subres = rss_query($sql);
		list($indb) = rss_fetch_row($subres);

		if ($cDate > 0) {
		$sec = "FROM_UNIXTIME($cDate)";
		} else {
		$sec = "null";
		}
				
		if ($indb == "") {
		
		$sql = "insert into " . getTable("item") . " (cid, added, title, url, "
		  ." description, unread, pubdate) "
		  . " values ("
		  ."$cid, now(), '"
		  .rss_real_escape_string($title) ."', "
		  ." '$url', '"
		  .rss_real_escape_string($description) ."', "
		  ."1, $sec)";
		
		rss_query($sql);
		$unreadCount++;
		}
	}
	}
	
	
	if ($id != "" && is_numeric($id)) {
	if ($rss) {
		// when everything went well, return the error code
		// and numer of new items
		return array($rss -> rss_origin,$unreadCount);
	} else {
		return array(-1,0);
	}
	} else {
	return array(-1,$unreadCount);
	}
}

/**
 * renders a list of items. Returns the number of items actually shown
 */
function itemsList($title,$items, $options = IL_NONE){

	
	if ($title) {
		if (($options & IL_CHANNEL_VIEW) && getConfig('rss.output.showfavicons')) {
			$cicon = $items[0][2];
		}
	    
	    $anchor = (!defined('FEEDCONTENT_ANCHOR_SET')?" id=\"feedcontent\"":"");
		echo "\n\n<h2$anchor>"
			.(isset($cicon) && $cicon !="" ?"<img src=\"$cicon\" class=\"favicon\" alt=\"\"/>":"")		
			.rss_htmlspecialchars($title)
			."</h2>\n";
	    define ('FEEDCONTENT_ANCHOR_SET',true);
	    
	} else {
	    echo "\n\n<a id=\"feedcontent\"/></a>\n";
	}
	
	$cntr=0;
	$prev_cid=0;

	$ret = 0;
	$lastAnchor = "";

	$collapsed_ids=array();
	if (getConfig('rss.output.channelcollapse')) {
		if (array_key_exists('collapsed',$_COOKIE)) {
			$collapsed_ids = explode(":",$_COOKIE['collapsed']);
		}
	}

   if ($options & IL_DO_STATS) {
      $stats = array();
      $stats_res = rss_query("select cid,unread,count(*) from " . getTable("item") . " group by 1,2 order by 1,2");
      while (list($s_cid,$s_unread,$s_count)=rss_fetch_row($stats_res)) {
         $stats[$s_cid][$s_unread] = $s_count;
      }
   }

	while (list($row, $item) = each($items)) {

	list($cid, $ctitle, $cicon, $ititle, $iunread, $iurl, $idescr, $ts, $ispubdate, $iid) = $item;
	if (array_key_exists('tags',$item)) {
		$tags = $item['tags'];
		if (count($tags) && $tags[0] == NULL) {
			$tags = array();
		}
	} else {
		$tags = array();
	}
	if (getConfig('rss.output.channelcollapse')) {
		$collapsed = in_array($cid,$collapsed_ids)
		  && !( $options & (IL_NO_COLLAPSE | IL_CHANNEL_VIEW))
		&& !$iunread;

		if (array_key_exists('collapse', $_GET) && $_GET['collapse'] == $cid) {
		// expanded -> collapsed
		$collapsed = true;
		if (!in_array($cid,$collapsed_ids)) {
			$collapsed_ids[] = $cid;
			$cookie = implode(":",$collapsed_ids);
			setcookie('collapsed',$cookie, time()+COOKIE_LIFESPAN);
		}
		} elseif (array_key_exists('expand', $_GET) &&$_GET['expand'] == $cid && $collapsed) {
		//	collapsed -> expanded
		$collapsed = false;
		if (in_array($cid,$collapsed_ids)) {
			$key = array_search($cid,$collapsed_ids);
			unset($collapsed_ids[$key]);
			$cookie = implode(":",$collapsed_ids);
			setcookie('collapsed',$cookie, time()+COOKIE_LIFESPAN);
		}
		}

	} else {
		$collapsed = false;
	}

	$escaped_title = preg_replace("/[^A-Za-z0-9\.]/","_","$ctitle");
	$ctitle = rss_htmlspecialchars($ctitle);

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
		if ($ctitle != "" && !($options & IL_CHANNEL_VIEW)) {
		echo "\n<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->\n";
		echo
		  "\n<h3"
		  //." id=\"$escaped_title\" "
		  . ($collapsed?" class=\"collapsed".($iunread?" unread":"")."\"":"")
			.">\n";

		if (!($options & IL_NO_COLLAPSE)
			&& getConfig('rss.output.channelcollapse')			
			&& !$iunread
			) {
			if ($collapsed) {
			$title = EXPAND . " '$ctitle'";
			echo "\t<a title=\"$title\" class=\"expand\" href=\"".$_SERVER['PHP_SELF'] ."?expand=$cid#$escaped_title\">\n"
			  ."\t<img src=\"". getPath()."css/media/plus.gif\" alt=\"$title\"/>"
			  //."&nbsp;+&nbsp;"
			  ."</a>\n";
			} else {
			$title = COLLAPSE . " '$ctitle'";
			echo "\t<a title=\"$title\" class=\"collapse\" href=\"".$_SERVER['PHP_SELF'] ."?collapse=$cid#$escaped_title\">\n"
			  ."\t<img src=\"". getPath()."css/media/minus.gif\" alt=\"$title\"/>"
			  //."&nbsp;-&nbsp;"
			  ."</a>\n";
			}
		} elseif (getConfig('rss.output.showfavicons') && $cicon != "" && !$iunread) {
			echo "\t<img src=\"$cicon\" class=\"favicon\" alt=\"\"/>\n";
		}

		$anchor = "";
		if ($options & IL_DO_NAV) {
			$lastAnchor = $ctitle . ($iunread==1?" (unread)":" (read)");
			$anchor = "name=\"$lastAnchor\"";
		} else { $anchor = "name=\"$escaped_title\""; }

		if (getConfig('rss.output.usemodrewrite')) {
			echo "\t<a $anchor href=\"" .getPath() ."$escaped_title/\">$ctitle</a>\n";
		} else {
			echo "\t<a $anchor href=\"". getPath() ."feed.php?channel=$cid\">$ctitle</a>\n";
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

		if	($iunread == 1) {
		$cls .= " unread";
		}

		// some url fields are juste guid's which aren't actual links
		$isUrl = (substr($iurl, 0,4) == "http");
		echo "\t<li class=\"$cls\">\n";

		if (getConfig('rss.output.usepermalinks')) {
		$escaped_ititle=preg_replace("/[^A-Za-z0-9\.]/","_","$ititle");
		list($ply,$plm,$pld) = explode(":",date("Y:m:d",$ts));
		$ptitle = PL_FOR. "'$escaped_title/$ply/$plm/$pld/$escaped_ititle'";
		echo "\t\t<a class=\"plink\" title=\"$ptitle\" ";
		
		if ($escaped_ititle != "" && getConfig('rss.output.usemodrewrite')) {
			echo "href=\"" .getPath() ."$escaped_title/$ply/$plm/$pld/$escaped_ititle\">";
		} else {
			echo "href=\"". getPath() ."feed.php?channel=$cid&amp;iid=$iid&amp;y=$ply&amp;m=$plm&amp;d=$pld\">";
		}
		echo "\n\t\t\t<img src=\"".getPath() . "css/media/pl.gif\" alt=\"$ptitle\"/>\n"
		  ."\t\t</a>\n";
		}

		echo "\t\t<h4>";
		if ($ititle == "") {
		$ititle = "[nt]";
		}
		if ($isUrl) {
		echo "<a href=\"$iurl\">$ititle</a>";
		} else {
		echo $ititle;
		}

		echo "</h4>\n";

		if ($ts != "") {
			$date_lbl = date(getConfig('rss.config.dateformat'), $ts);
			
			// make a permalink url for the date (month)
			if (strpos(getConfig('rss.config.dateformat'),'F') !== FALSE) {
				$mlbl = date('F',$ts);
				$murl = makeArchiveUrl($ts,$escaped_title,$cid,false);
				
				$date_lbl = 
				  str_replace($mlbl,
					  "<a href=\"$murl\">$mlbl</a>"
					  ,$date_lbl);
			}
			
			// make a permalink url for the date (day)
			if (strpos(getConfig('rss.config.dateformat'),'jS') !== FALSE) {
				$dlbl = date('jS',$ts);
				$durl = makeArchiveUrl($ts,$escaped_title,$cid,true);
				$date_lbl =
				  str_replace($dlbl,
					  "<a href=\"$durl\">$dlbl</a>" 
					  ,$date_lbl);
			}	
			
			if ($ispubdate) {
				echo "\t\t<h5>". POSTED . "$date_lbl</h5>\n";
			} else {
				echo "\t\t<h5>". FETCHED . "$date_lbl</h5>\n";
			}
		}
	
		/// tags

	    echo "\t\t<h5>";
	    if (getConfig('rss.output.usemodrewrite')) {
			 echo "<a href=\"".getPath()."tag/\">";
 	    } else {
			 echo "<a href=\"".getPath()."tags.php?alltags\">";
 	    }
	    echo TAG_TAGS ."</a>:&nbsp;<span id=\"t$iid\">";
	    foreach($tags as $tag_) {
	    	 $tag_ = trim($tag_);
			 if (getConfig('rss.output.usemodrewrite')) {
				echo "<a href=\"".getPath()."tag/$tag_\">$tag_</a> ";
			} else {
				echo "<a href=\"".getPath()."tags.php?tag=$tag_\">$tag_</a> ";
			}
	    }
	    
	    echo "</span>&nbsp;[<span id=\"ta$iid\">"
	      . "<a href=\"#\" onclick=\"_et($iid); return false;\">"
	      .TAG_EDIT."</a>"
	      ."</span>]</h5>\n\n";

		/// /tags
		
		

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

function add_channel($url, $folderid=0) {
    assert("" != $url && strlen($url) > 7);
    assert(is_numeric($folderid));
    
    $urlDB = htmlentities($url);
    
    
    $res = rss_query("select count(*) as channel_exists from " .getTable("channels") ." where url='$urlDB'");
    list ($channel_exists) = rss_fetch_row($res);
    if ($channel_exists > 0) {
	return array(-1,"Looks like you are already subscribed to this channel");
    }
    
    $res = rss_query("select 1+max(position) as np from " .getTable("channels"));
    list($np) = rss_fetch_row($res);
    
    if (!$np) {
	$np = "0";
    }
    
    // Here we go!
    $rss = fetch_rss( $url );
    if ( $rss ) {
	
	if (is_object($rss) && array_key_exists('title',$rss->channel)) {
	    $title= rss_real_escape_string ( $rss->channel['title'] );
	} else { 
	    $title = "";
	}
	
	if (is_object($rss) && array_key_exists('link',$rss->channel)) {
	    $siteurl= rss_real_escape_string (htmlentities($rss->channel['link'] ));
	} else {
	    $siteurl = "";
	}
	
	if (is_object($rss) && array_key_exists('description',$rss->channel)) {
	    $descr =  rss_real_escape_string ($rss->channel['description']);
	} else {
	    $descr = "";
	}
	
	//lets see if this server has a favicon
	$icon = "";
	if (getConfig('rss.output.showfavicons')) {
	    // if we got nothing so far, lets try to fall back to
	    // favicons
	    if ($icon == "" && $siteurl	 != "") {
		$match = get_host($siteurl, $host);
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
	    $sql = "insert into " .getTable("channels") ." (title, url, siteurl, parent, descr, dateadded, icon, position)"
	      ." values ('$title', '$urlDB', '$siteurl', $folderid, '$descr', now(), '$icon', $np)";
	    
	    rss_query($sql);
	    $newid = rss_insert_id();
	    return array($newid,"");
	    
	} else {
	    return array (-1, "I'm sorry, I couldn't extract a valid RSS feed from <a href=\"$url\">$url</a>.");		
	}
    } else {
	return array( -1, "I'm sorry, I couldn't retrieve <a href=\"$url\">$url</a>.");
    }
}

/**
 * renders links in $in absolute, based on $base.
 * The regexp probably needs tuning.
 */
function make_abs($in, $base) {
	return preg_replace(',<a([^>]+)href="([^>"\s]+)",ie',
			'"<a\1href=\"" . _absolute("\2", $base) . "\""',
			$in);  
}


/**
 * A better make_abs method. 
 * Props: http://www.php-faq.de/q/q-regexp-links-absolut.html
 */
function _absolute ($relative, $absolute) {
	
	// Link ist schon absolut
	if (preg_match(',^(https?://|ftp://|mailto:|news:),i', $relative))
	  return $relative;
	
	// parse_url() nimmt die URL auseinander
	$url = parse_url($absolute);
	
	// dirname() erkennt auf / endende URLs nicht
    if (array_key_exists('path', $url)) {
		if ($url['path']{strlen($url['path']) - 1} == '/')
            $dir = substr($url['path'], 0, strlen($url['path']) - 1);
		else
            $dir = dirname($url['path']);
	} else {
	   $dir ="";
	}
	// absoluter Link auf dem gleichen Server
	if ($relative{0} == '/') {
	   $relative = substr($relative, 1);
	   $dir = '';
	}
	
	// Link fängt mit ./ an
	elseif (substr($relative, 0, 2) == './')
	  $relative = substr($relative, 2);
	
	// Referenzen auf höher liegende Verzeichnisse auflösen
	else while (substr($relative, 0, 3) == '../') {
	$relative = substr($relative, 3);
	$dir = substr($dir, 0, strrpos($dir, '/'));
	}
	
	// volle URL zurückgeben
	return sprintf('%s://%s%s/%s', $url['scheme'], $url['host'], $dir, $relative);
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

		# is timezone ahead of GMT?	 then subtract offset
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

/**
 * Returns the relative path of the install dir, e.g:
 * http://host.com/thing/ -> "/thing/"
 * http://host.com/ -> "/"
 */
function getPath() {
	static $ret;
	$ret = dirname($_SERVER['PHP_SELF']);
	if (defined('RSS_FILE_LOCATION') && eregi(RSS_FILE_LOCATION . "\$",$ret)) {
	$ret = substr($ret,0,strlen($ret) - strlen(RSS_FILE_LOCATION));
	}	   
	if (substr($ret,-1) != "/") {  
	$ret .= "/";
	} 
	return $ret;  
}

/**
 * builds an url for an archive link
 */
function makeArchiveUrl($ts,$channel,$cid,$dayView ) {
	if (getConfig('rss.output.usemodrewrite')) {
	return ( getPath()
		 . "$channel/"
		 .date(($dayView?'Y/m/d/':'Y/m/'),$ts));
	} else {
	return 
	  (getPath() ."feed.php?channel=$cid&amp;y="
	   .date('Y',$ts)
	   ."&amp;m="
	   .date('m',$ts)
	   .($dayView?("&amp;d=".date('d',$ts)):""));		
	}	 
}

/**
 * Fetches a remote URL and returns the content
 */
function getUrl($url) {
	$handle = @fopen($url, "rb");
	if (!$handle) {
	  return "";
	}
	$contents = "";
	do {
	$data = @fread($handle, 8192);
	if (strlen($data) == 0) {
		break;
	}
	$contents .= $data;
	} while (true);
	@fclose($handle);
	return $contents;
}

/**
 * returns an array of all (hopefully) rss/atom/rdf feeds in the document,
 * pointed by $url
 */
function extractFeeds($url) {
	$cnt = getUrl($url);
	$ret = array();
	//find all link tags
	if (preg_match_all('|<link \w*="[^"]+"+[^>]*>|Ui',$cnt,$res)) {
	  while(list($id,$match)=each($res[0])) {
		 // we only want '<link alternate=...'
		 if (strpos($match,'alternate') &&
		 !strpos($match,'stylesheet') &&
		 // extract the attributes
		 preg_match_all('|([a-zA-Z]*)="([^"]*)|',$match,$res2,PREG_SET_ORDER)) {
		 $tmp = array();
		 //populate the return array: attr_name => attr_value
		 while(list($id2,$match2) = each($res2)) {
			   $attr = trim($match2[1]);
			   $val	 = trim($match2[2]);
			// make sure we have absolute URI's
			if (strcasecmp($attr,"href") == 0 &&
			strcasecmp(substr($val,0,4),"http") != 0) {
				$val =	($url . $val);
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
	return htmlspecialchars(
	   $in, ENT_NOQUOTES,
	   (getConfig('rss.output.encoding')?getConfig('rss.output.encoding'):DEFAULT_OUTPUT_ENCODING)
	);
}


//// profiling ////
$__init_timer=getmicrotime();
$__prev_timer=0;

function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

function timer($where) {
    global $__init_timer, $__prev_timer;
    $current_timer= getmicrotime();
    $ret = " t= "
      . 1000 * ( $current_timer - $__init_timer) . ""
      . " ms, delta= "
      . 1000 * ($current_timer - $__prev_timer ) .""
      ."ms ";
    $__prev_timer=$current_timer;
    return $ret;
}

function _pf($comment, $commentOnly=false) {
    echo "\n\n\n<!-- " . $comment;
    if (! $commentOnly)
      echo timer($comment);
    echo  "   -->\n\n\n";
}

?>
