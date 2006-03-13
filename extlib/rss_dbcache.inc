<?php
/*
 * Project:     MagpieRSS: a simple RSS integration tool
 * File:        rss_dbcache.inc, a database version of magpie's
 *              cache for Gregarius, keyed on URL.
 * Author:      Sameer D'Costa <sameerdcosta@gmail.com>
 * Version:     0.1
 * License:     GPL
 *
 * The lastest version of MagpieRSS can be obtained from:
 * http://magpierss.sourceforge.net
 *
 * For questions, help, comments, discussion, etc., please join the
 * Gregarius dev mailing list.
 */


class RSSdbCache {
    var $MAX_AGE    = 60;         // when are files stale (in minutes), default one hour
    var $ERROR      = "";           // accumulate error messages
    var $translateUrltoCid = array(); // which URL correspondes to which channel ID

    function RSSdbCache ($base='', $age='') {
        // $base is kept for compatibility purposes only
        if ( $age ) {
            $this->MAX_AGE = $age;
        }
    }
    /*=======================================================================*\
        Function:   set
        Purpose:    add an item to the cache, keyed on url
        Input:      url from wich the rss file was fetched
        Output:     true on sucess  
    \*=======================================================================*/
    function set ($url, $rss) {
        $this->ERROR = "";
        if(!array_key_exists($url, $this -> translateUrltoCid) ) {
            $cid = $this->translateUrl($url);
        } else {
            $cid = $this -> translateUrltoCid[$url];
        }
        if ( ! $cid ) {
            $this->debug( "Cannot translate: $url (to cid)");
            return 0;
        }
        $sql = "update ".getTable("channels")
               ." set "." lastmodified='" . rss_real_escape_string(trim($rss->last_modified)) . "', "
               ." etag='" . rss_real_escape_string((trim($rss->etag))
               ."', daterefreshed = now()" . " where id=$cid";
        rss_query($sql);
        if (! rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR)) {
            $this->error( "Unable to write to db cache: $url : cid: $cid");
            return 0;
        }

        return true;
    }

    /*=======================================================================*\
        Function:   get
        Purpose:    fetch an item from the cache
        Input:      url from wich the rss file was fetched
        Output:     cached object on HIT, false on MISS 
    \*=======================================================================*/
    function get  ($url) {
        $this->ERROR = "";
        if(!array_key_exists($url, $this -> translateUrltoCid) ) {
            $cid = $this->translateUrl($url);
        } else {
            $cid = $this -> translateUrltoCid[$url];
        }
        if ( ! $cid ) {
            $this->debug( "Cannot translate: $url (to cid)");
            return 0;
        }

        $res = rss_query("select etag, lastmodified from " . getTable("channels")
                         . " where id=$cid");
        if ( ! $res ) {
            $this->error("Failed to read from db cache. channel id: $cid");
            return 0;
        }
        list($etag, $lastmodified) = rss_fetch_row($res);
        $rss = new FakeMagpieRSS();
        $rss ->etag = trim($etag);
        $rss ->last_modified  = trim($lastmodified);

        return $rss;

    }

    /*=======================================================================*\
        Function:   check_cache
        Purpose:    check a url for membership in the cache
                    and whether the object is older then MAX_AGE (ie. STALE)
        Input:      url from wich the rss file was fetched
        Output:     cached object on HIT, false on MISS 
    \*=======================================================================*/
    function check_cache ( $url ) {
        $this->ERROR = "";
        if(!array_key_exists($url, $this -> translateUrltoCid) ) {
            $cid = $this->translateUrl($url);
        } else {
            $cid = $this -> translateUrltoCid[$url];
        }
        if ( ! $cid ) {
            $this->debug( "Cannot translate: $url (to cid)");
            return 'MISS';
        }

        $res = rss_query("select UNIX_TIMESTAMP(daterefreshed) as ts, refreshinterval from " . getTable("channels")
                         . " where id=$cid");
        if ( ! $res ) {
            $this->error("Failed to read from db cache. channel id: $cid");
            return 'MISS';
        }

        list($dateRefreshed, $refreshInterval) = rss_fetch_row($res);

        // Lets hope that the web server and sql server have their clocks in sync
        $age = time() - $dateRefreshed;
        if (!$refreshInterval || ($refreshInterval < 10)) {
            // We should change the Gregarius UA if we allow refreshInterval to be small
            $refreshInterval = $this->MAX_AGE;
        }
        if ( $refreshInterval * 60  > $age ) {
            // object exists and is current
            return 'HIT';
        } else {
            // object exists but is old
            return 'STALE';
        }
    }

    /*=======================================================================*\
        Function:   translateUrl
        Purpose:    internally map url to channel id
        Input:      url from wich the rss file was fetched
        Output:     returns cid if it succeeds, else 0.
    \*=======================================================================*/
    function translateUrl ($url) {
        $url = rss_real_escape_string($url);
				$sql = "select id from " . getTable("channels") . " where url='$url'";
        $res = rss_query($sql);
        if(1 == rss_num_rows($res)) {
            list ($cid) = rss_fetch_row($res);
            $this -> translateUrltoCid[$url] = $cid;
            return $cid; // should always be positive
        } else {
            $this->error("Cache unable translate to cid: $url");
            return 0;
        }
    }

    /*=======================================================================*\
        Function:   error
        Purpose:    register error
    \*=======================================================================*/
    function error ($errormsg, $lvl=E_USER_WARNING) {
        // append PHP's error message if track_errors enabled
        if ( isset($php_errormsg) ) {
            $errormsg .= " ($php_errormsg)";
        }
        $this->ERROR = $errormsg;
        if ( MAGPIE_DEBUG ) {
            trigger_error( $errormsg, $lvl);
        } else {
            error_log( $errormsg, 0);
        }
    }

    function debug ($debugmsg, $lvl=E_USER_NOTICE) {
        if ( MAGPIE_DEBUG ) {
            $this->error("MagpieRSS [debug] $debugmsg", $lvl);
        }
    }

}

/*=======================================================================*\
    Class:   	Fake MapieRSS class
    Purpose:    to send a barebones rss object back from the db
\*=======================================================================*/
class FakeMagpieRSS extends MagpieRSS {

    function FakeMagpieRSS() {
        // just need to avoid calling the parent's constructor
        // because we have nothing to parse
    }
}

?>
