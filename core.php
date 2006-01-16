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

if (!defined('GREGARIUS_HOME')) {
    define('GREGARIUS_HOME',dirname(__FILE__) . "/");
}

function rss_bootstrap($withDB = true, $etag_prefix= "", $cacheValidity=0) {
    require_once(GREGARIUS_HOME . 'constants.php');
    if ($withDB) {
        require_once(GREGARIUS_HOME . 'db.php');
    }
    if (!defined('RSS_NO_CACHE')) {
        checkETag($withDB,$etag_prefix,$cacheValidity);
    }
}


function checkETag($withDB = true, $keyPrefix= "", $cacheValidity = 0) {
    $key = $keyPrefix.'$Revision$'.$_SERVER["REQUEST_URI"];
    if ($withDB) {
        list($dt) = rss_fetch_row(rss_query('select timestamp from ' .getTable('cache') . " where cachekey='data_ts'"));
        $key .= $dt;
    }
    if (array_key_exists(RSS_USER_COOKIE,$_REQUEST)) {
        $key .= $_REQUEST[RSS_USER_COOKIE];
    }
    $key = md5($key);

    if (array_key_exists('HTTP_IF_NONE_MATCH',$_SERVER)  && $_SERVER['HTTP_IF_NONE_MATCH'] == $key) {
        header("HTTP/1.1 304 Not Modified");
        header("X-RSS-CACHE-STATUS: HIT");
        header("ETag: $key");
        flush();
        exit();
    } else {
        header("ETag: $key");
        header("X-RSS-CACHE-STATUS: MISS");
        if ($cacheValidity) {
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cacheValidity*3600) . 'GMT');
        }
    }
}
?>
