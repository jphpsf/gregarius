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

if (defined('_DEBUG_') && _DEBUG_ == true) {
    error_reporting(E_ALL);
} else {
    error_reporting(E_NONE);
}

function rss_require($file) {
    
    $path = dirname($_SERVER['SCRIPT_FILENAME']);
    if (defined('RSS_FILE_LOCATION') && eregi(RSS_FILE_LOCATION . "\$",$path)) {
	$path = substr($path,0,strlen($path) - strlen(RSS_FILE_LOCATION));
    }
    require_once($path . "/" . $file);
}


rss_require('constants.php');
rss_require('config.php');
rss_require('db.php');
rss_require('magpierss-0.61/rss_fetch.inc');
rss_require('magpierss-0.61/rss_utils.inc');
rss_require('kses-0.2.1/kses.php');
rss_require('util.php');

if (defined('LANG') && file_exists('intl/' . LANG . '.php')) {
    rss_require('intl/' . LANG . '.php');
} else {
    rss_require('intl/en.php');
}

rss_require("channels.php");
?>
