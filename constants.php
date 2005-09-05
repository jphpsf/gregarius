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

// Application title
define ('_TITLE_', "Gregarius");


// Application version
define ('_VERSION_', "0.5.1");
define ('MINUTE',60);

define ('COOKIE_LIFESPAN',60*60*24*999);
// The useragent used when retrieving the feeds
define ('MAGPIE_USER_AGENT', "" . _TITLE_ . "/" . _VERSION_ . " (+http://devlog.gregarius.net/docs/ua)");
ini_set('user_agent',MAGPIE_USER_AGENT);
	
// feedback
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 1);
assert_options(ASSERT_QUIET_EVAL, 0);


// default output encoding, can be overrided in the config.
define ('DEFAULT_OUTPUT_ENCODING', 'UTF-8');
  

// Create a handler function
function my_assert_handler($file, $line, $code) {
    echo "<span class=\"error\">Assertion Failed: File '$file'; Line '$line'; Code '$code'";
}


// Set up the callback
assert_options(ASSERT_CALLBACK, 'my_assert_handler');

define ('TITLE_SEP', '&#187;');

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
define ('IL_TITLE_NO_ESCAPE',0x10);
define ('IL_FOLDER_VIEW',0x20);

// Options for rss_header
define ('HDR_NONE', 0x00);
define ('HDR_NO_OUPUTBUFFERING', 0x01);
define ('HDR_NO_CACHECONTROL', 0x02);


// options for show unread-only/all
define ('SHOW_UNREAD_ONLY',1);
define ('SHOW_READ_AND_UNREAD',2);
define ('SHOW_WHAT','show');

// feed "modes": (default is 0001: unread)
// xxx0 read / xxx1: unread
define ('FEED_MODE_UNREAD_STATE', 0x01);
// xx0x not sticky / xx1x: sticky
define ('FEED_MODE_STICKY_STATE', 0x02);
// x0xx public / x1xx: private
define ('FEED_MODE_PRIVATE_STATE', 0x04);
// 0xxx available / 1xxx: deleted
define ('FEED_MODE_DELETED_STATE', 0x08);

// these are just helpers for the above
define ('SET_MODE_READ_STATE',
   FEED_MODE_STICKY_STATE  | 
   FEED_MODE_PRIVATE_STATE | 
   FEED_MODE_DELETED_STATE);
   
define ('SET_MODE_PUBLIC_STATE',
   FEED_MODE_UNREAD_STATE  | 
   FEED_MODE_STICKY_STATE  | 
   FEED_MODE_DELETED_STATE);

define ('SET_MODE_AVAILABLE_STATE',
   FEED_MODE_UNREAD_STATE  | 
   FEED_MODE_STICKY_STATE  | 
   FEED_MODE_PRIVATE_STATE);
   
// Where do themes and plugins reside?
define ('RSS_THEME_DIR','themes');
define ('RSS_PLUGINS_DIR','plugins');
   
   
// Error levels   
define ('RSS_ERROR_ERROR',0);
define ('RSS_ERROR_WARNING',1);
define ('RSS_ERROR_NOTICE',2);   
?>
