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



// db
require_once('dbinit.php');
  
// Where should magpie store its temporary files? 
// Apache needs write permissions on this dir.
define('MAGPIE_CACHE_DIR', '/tmp/magpierss');

// Number of items shown on for a single channel
define ('ITEMS_ON_CHANNELVIEW', 10);

// Display the favicon for the channels that have one.
// Due to a IE bug, some icons do not render correctly on
// this browser. You can either change the URL to the icon
// in the admin screen, or turn the favicon displaying off
// globally here.
define ('USE_FAVICONS', true);

// if this option is set to true the channels can be addressed
// as http://yourserver.com/path/to/rss/channel_name/
// You must have mod_rewrite installed and configured for your
// apache install. See also the '.htaccess' file
define ('USE_MODREWRITE', true);

// If this option is set the feeds will be refreshed after x minutes
// of inactivity. Please respect the feed providers by not setting
// this value to anything lower than thirty minutes.
// Comment the line to turn this feature off.
define ('RELOAD_AFTER', 45*MINUTE);

// Format to use when displaying dates. See here for help on the format:
// http://ch.php.net/manual/en/function.date.php
define ('DATE_FORMAT', "F jS, Y, g:i a T");

// When in demo mode most of the admin actions cannot be performed.
define ('DEMO_MODE', false);

// When in debug mode some extra debug info is shown and the error 
// reporting is a bit more verbose
define ('_DEBUG_', false);

// Output compression is handled by most browsers. 
define ('OUTPUT_COMPRESSION', true);
  

// Allow collpasing of channels on the main page.
define ('ALLOW_CHANNEL_COLLAPSE', true);

// Display a permalink icon and allow linking to a given item.
define('USE_PERMALINKS',true);
  
// Leave this commented out, (WIP)
/* 
 define ('_ADMIN_USERNAME_','test');
 define ('_ADMIN_PASSWORD_','123');
*/

// Language pack to use. (As of today 'en' and 'fr' ar available)
define ('LANG', 'en');

// html filtering via kses. DO no modify this unless you know what 
// you do. See kses-0.2.1/README for more info.

$kses_allowed = 
  array(
	'img' => array('src' => 1, 'alt'=> 1 
		       //,  'align' => 1, 'vspace' => 1, 'hspace' => 1
		       ),
	'b' => array(),
	'i' => array(),
	'a' => array('href' => 1, 'title' => 1),
	'br' => array(),
	'p' => array(),
	'blockquote' => array(),
	'ul' => array(),	
	'li' => array(),
	'tt' => array(),
	'code' => array()
	);
?>
