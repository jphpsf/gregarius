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

function rss_home_dir() {
    if (!defined('GREGARIUS_HOME')) {
        define('GREGARIUS_HOME',dirname(__FILE__) . "/");
    }

    return GREGARIUS_HOME;
}

function rss_require($file,$once=true) {
    $required_file = rss_home_dir() .  $file;
    if ($once) {
        require_once($required_file);
    } else {
        require($required_file);
    }
}

////////////////////////////////////////////////////////////////////////////////
// my-hacks support
//

if (file_exists(dirname(__FILE__) . '/rss_extra.php')) {
    rss_require('rss_extra.php');
}

////////////////////////////////////////////////////////////////////////////////
// Bootstrap
//
rss_require('core.php');
rss_bootstrap();


////////////////////////////////////////////////////////////////////////////////
// Base includes
//
rss_require('util.php');
rss_require('cls/rss.php');
rss_require('cls/config.php');
rss_require('themes.php');
rss_require('plugins.php');
rss_require('cls/user.php');
//rss_require('config.php');




////////////////////////////////////////////////////////////////////////////////
// Error reporting
//

if (getConfig('rss.meta.debug')) {
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

if (!isset($GLOBALS['rss'])) {
    rss_require('cls/rss.php');
}

////////////////////////////////////////////////////////////////////////////////
// Classes
//

_pf('parsing classes:');
rss_require('cls/errorhandler.php');
_pf(' ... errorhandler.php');
rss_require('cls/items.php');
_pf(' ... items.php');
rss_require("cls/channels.php");
_pf(' ... channels.php');
rss_require('cls/sidemenu.php');
_pf(' ... sidemenu.php');
rss_require("cls/header.php");
_pf(' ... header.php');
rss_require("cls/nav.php");
_pf(' ... nav.php');

_pf('parsing remaining files...');

rss_require('extlib/rss_fetch.inc');
rss_require('extlib/rss_utils.inc');
rss_require('extlib/kses.php');
rss_require('extlib/Sajax.php');
rss_require('tags.php');

////////////////////////////////////////////////////////////////////////////////
// Localization
_pf('Loading l10n...');

require_once('cls/l10n.php');
$GLOBALS['rssl10n'] = new RSSl10n();
$lang = $GLOBALS['rssl10n']->getLocale();
_pf('done');

// Theme  specific l10n handling
list($theme,$media) = getActualTheme();

if (file_exists(RSS_THEME_DIR."/$theme/intl/$lang.php")) {
    rss_require(RSS_THEME_DIR."/$theme/intl/$lang.php");
}
elseif ($lang != "en" && file_exists(RSS_THEME_DIR."/$theme/intl/en.php")) {
    rss_require(RSS_THEME_DIR."/$theme/intl/en.php");
}


//
if (file_exists(getThemePath(GREGARIUS_HOME)."overrides.php")) {
	rss_require(getThemePath('')."overrides.php");
}

/*
// Load the right locale
if (defined('OVERRIDE_LOCALE')) {
    setlocale(LC_TIME,constant("OVERRIDE_LOCALE"));
}
elseif (isset($_SERVER["WINDIR"]) && defined("LOCALE_WINDOWS")) {
    setlocale(LC_TIME,constant("LOCALE_WINDOWS"));
}
elseif (defined("LOCALE_LINUX")) {
    setlocale(LC_TIME,constant("LOCALE_LINUX"));
}
else {
    //last chance, we try to guess it
    $mylocale=strtolower(getConfig('rss.output.lang'));
    $mylocale.="_".strtoupper($mylocale);
    if (!setlocale(LC_TIME,$mylocale)) {
        // very last resort: try to load the system locale
        setlocale(LC_TIME,"");
    }
}
*/


_pf('done');
?>
