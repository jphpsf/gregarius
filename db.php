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

if (!@include_once('dbinit.php')) {
	header('Location: install.php');
	exit();
}

if (!defined('DBTYPE')) {
    define ('DBTYPE','mysql');
}

require_once('cls/db/db.'.DBTYPE.'.php');
$dbcls = ucfirst(DBTYPE)."DB";
$GLOBALS['rss_db'] = new $dbcls;


function rss_query ($query, $dieOnError=true, $preventRecursion=false) {
    $pf = defined('PROFILING_DB') && PROFILING_DB && function_exists('_pf');
    if ($pf) {
        list ($usec, $sec) = explode(" ", microtime());
    }
    $rs =  $GLOBALS['rss_db']->rss_query ($query, $dieOnError, $preventRecursion);
    if ($pf) {
        list ($usec2, $sec2) = explode(" ", microtime());
        _pf("Query took " . (($sec2-$sec) + ($usec2-$usec)) . " seconds:\n\t\t\t" 
        . preg_replace('/\'[a-z0-9]+\'/i','\'[removed]\'',$query));
    }
    return $rs;
}

function rss_fetch_row($rs) {
    return  $GLOBALS['rss_db']->rss_fetch_row($rs);
}

function rss_fetch_assoc($rs) {
    return $GLOBALS['rss_db']->rss_fetch_assoc($rs);
}
function rss_num_rows($rs) {
    return $GLOBALS['rss_db']->rss_num_rows($rs);
}

function rss_sql_error() {
    return $GLOBALS['rss_db']->rss_sql_error();
}

function rss_sql_error_message () {
    return $GLOBALS['rss_db']->rss_sql_error_message();
}

function rss_insert_id() {
    return $GLOBALS['rss_db']->rss_insert_id();
}

function rss_real_escape_string($string) {
    return $GLOBALS['rss_db']->rss_real_escape_string($string);
}

function getTable($tableName) {
    return $GLOBALS['rss_db']->getTable($tableName);
}

function rss_is_sql_error($kind) {
    return $GLOBALS['rss_db']-> rss_is_sql_error($kind);
}

function rss_invalidate_cache() {
    $sql = 'update ' . getTable('cache') . " set timestamp=now() where cachekey='data_ts'";
    $GLOBALS['rss_db']->rss_query ($sql, false, false);
    return true;
}

?>
