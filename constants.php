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

// Application title
define ('_TITLE_', "Gregarius");


// Application version
define ('_VERSION_', "0.2.9");
define ('MINUTE',60);

define ('COOKIE_LIFESPAN',60*60*24*999);
// The useragent used when retrieving the feeds
define ('MAGPIE_USER_AGENT', "" . _TITLE_ . "/" . _VERSION_ . " (http://sourceforge.net/projects/gregarius)"); 

// feedback
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 1);
assert_options(ASSERT_QUIET_EVAL, 0);


// Create a handler function
function my_assert_handler($file, $line, $code) {
    echo "<span class=\"error\">Assertion Failed: File '$file'; Line '$line'; Code '$code'";
}

// Set up the callback
assert_options(ASSERT_CALLBACK, 'my_assert_handler');

define ('TITLE_SEP', '&raquo;');

define ('LOCATION_HOME',1);
define ('LOCATION_UPDATE',2);
define ('LOCATION_SEARCH',3);
define ('LOCATION_ADMIN',4);
define ('LOCATION_ABOUT',5);


// Options passed to util::itemsList
define ('IL_NONE',   0x00);
define ('IL_DO_NAV', 0x01);
define ('IL_NO_COLLAPSE', 0x02);
define ('IL_DO_STATS',0x04);
define ('IL_CHANNEL_VIEW',0x08);


?>
