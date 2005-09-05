<?php
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2005 Marco Bonetti
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

function rss_require($file,$once=true) {
   if (!defined('GREGARIUS_HOME')) {
	  define('GREGARIUS_HOME',dirname(__FILE__) . "/");
	}
 
   $required_file = GREGARIUS_HOME.  $file;
   if ($once) {
        require_once($required_file);
   } else {
        require($required_file);
   }
}

// my-hacks support
if (file_exists(dirname(__FILE__) . '/rss_extra.php')) {
    rss_require('rss_extra.php');
}

rss_require('constants.php');
rss_require('util.php');
rss_require('cls/rss.php');
rss_require('dbinit.php');
rss_require('db.php');
rss_require('config.php');

if (getConfig('rss.meta.debug')) {
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}   

if (!isset($GLOBALS['rss'])) {
	rss_require('cls/rss.php');
}

_pf('parsing classes:');
rss_require('cls/errorhandler.php'); _pf(' ... errorhandler.php');
rss_require('cls/items.php');        _pf(' ... items.php');
rss_require("cls/channels.php");     _pf(' ... channels.php');
rss_require('cls/sidemenu.php');	    _pf(' ... sidemenu.php');
rss_require("cls/header.php");       _pf(' ... header.php');
rss_require("cls/nav.php");          _pf(' ... nav.php');

_pf('parsing remaining files...');
rss_require('plugins.php');
rss_require('extlib/rss_fetch.inc');
rss_require('extlib/rss_utils.inc');
rss_require('extlib/kses.php');
rss_require('extlib/Sajax.php');
rss_require('tags.php');


$lang = getConfig('rss.output.lang');

if ($lang && file_exists(dirname(__FILE__) . "/" . "intl/$lang.php")) {
    rss_require("intl/$lang.php");
} else {
    rss_require('intl/en.php');
}



_pf('done');
?>
