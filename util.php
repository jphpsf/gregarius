<?
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003, 2004 Marco Bonetti
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

function rss_header($title="", $active=0) {
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" "
      ."\"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n"
      ."<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">\n"
      ."<head>\n"
      ."\t<meta http-equiv=\"Content-Type\" content=\"text/html; "
      ."charset=ISO-8859-1\" />\n"
      ."\t<title>".makeTitle($title)."</title>\n"
      ."\t<meta name=\"robots\" content=\"NOINDEX,NOFOLLOW\"/>\n"
      ."\t<link rel=\"stylesheet\" type=\"text/css\" href=\"css/css.css\"/>\n";

    if ($active == 1 && defined('RELOAD_AFTER') 
    /* && RELOAD_AFTER > (30*MINUTE) */) {
	echo "\t<meta http-equiv=\"refresh\" "
	  ." content=\"" . RELOAD_AFTER
	  . ";url=update.php\"/>\n";
    }

    echo "</head>\n"
      ."<body>\n";
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
    echo "\n<div id=\"nav\" class=\"frame\">"
      ."\n<a id=\"top\"></a>\n"
      ."\n<h1>".makeTitle($title)."</h1>\n"
      ."<ul id=\"navlist\">\n"
      ."\t<li".($active==1?" class=\"active\"":"")."><a href=\"index.php\">".NAV_HOME."</a></li>\n"
      . "\t<li".($active==2?" class=\"active\"":"")."><a href=\"update.php\">" . NAV_UPDATE. "</a></li>\n"
      . "\t<li".($active==3?" class=\"active\"":"")."><a href=\"channel_admin.php\">".NAV_CHANNEL_ADMIN ."</a></li>\n"
      . "\t<li".($active==4?" class=\"active\"":"")." id=\"srch\">" . searchForm($_POST["query"]) .  "\t</li>\n"
      . "</ul>\n</div>\n";
}

function searchForm($qry) {
    return 
        "\n\t\t<form action=\"search.php\" method=\"post\" id=\"srchfrm\">\n"
        ."\t\t<p><input type=\"text\" name=\"query\" id=\"query\" />\n"
        ."\t\t<input type=\"submit\" value=\"". SEARCH ."\"/></p>\n"
        ."\t\t</form>\n";
    
        
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

    echo "<span>\n\tPage rendered ". date("D M j Y, G:i:s T")."\n</span>\n";

    echo "</div>\n\n";
}

function rss_error($message) {
    echo "\n<p class=\"error\">$message</p>\n";
}

function add_channel($url) {
    assert($url != "" && strlen($url) > 7);
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
        if (_USE_FAVICONS_ && $rss->channel['link']  != "") {
            $match = get_host($rss->channel['link'], $host);
            $uri = "http://" . $host . "favicon.ico";
            if ($match && (getHttpResponseCode($uri)))  {
              $icon = $uri;
            }
        }
    
        if ($title != "") {
            $sql = "insert into channels (title, url, siteurl, parent, descr, dateadded, icon)"
              ." values ('$title', '$urlDB', '$siteurl', 0, '$descr', now(), '$icon')";
    
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
        $ret .= " &gt; " . $title;
    }

    return $ret;
}

/*** update the given feed(s) **/

function update($id) {
    global $kses_allowed;

    $sql = "select id, url, title from channels";
    if (is_numeric($id)) {
        $sql .= " where id=$id";
    }

    $res = rss_query($sql);
    while (list($cid, $url, $title) = mysql_fetch_row($res)) {
        $rss = fetch_rss( $url );
    
        /*
         echo "<pre>";
         var_dump($rss);
         echo "</pre>";
         die();
         */
    
        foreach ($rss->items as $item) {
    
            $title = $item['title'];
            $description = kses($item['description'],  $kses_allowed);
            $url =  $item['link'];
            $cDate = $item['dc']['date'];
            $sql = "select id from item where cid=$cid and url='$url'";
            $subres = rss_query($sql);
            list($indb) = mysql_fetch_row($subres);
    
            //$sec =  strtotime($cDate);
            //if ($sec == -1) {
            $sec = "now()";
            //}
    
            if ($indb == "") {
                $sql = "insert into item (cid, added, title, url, "
                  ." description, unread, pubdate) "
                  . " values ("
                  ."$cid, now(), '"
                  .mysql_real_escape_string($title) ."', '$url', '"
                  . mysql_real_escape_string($description)
                    ."', 1, $sec)";
        
                rss_query($sql);
            }
        }
    }
}

?>
