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



// db
require_once('dbinit.php');
  
// Where should magpie store its temporary files? 
// Apache needs write permissions on this dir.
define('MAGPIE_CACHE_DIR', '/tmp/magpierss');

// options
define ('ITEMS_ON_CHANNELVIEW', 10);
define ('_TITLE_', "Gregarius");
define ('_VERSION_', "0.1.5");
define ('_USE_FAVICONS_', true);
define ('_USE_MODREWRITE_', true);
define ('MAGPIE_USER_AGENT', "" . _TITLE_ . "/" . _VERSION_ . " (http://sourceforge.net/projects/gregarius)");
define ('MINUTE',60);
define ('RELOAD_AFTER', 30*MINUTE);
define ('DATE_FORMAT', "F jS, Y, g:i a");
define ('DEMO_MODE', false);
define ('_DEBUG_', false);

/* 
 define ('_ADMIN_USERNAME_','test');
 define ('_ADMIN_PASSWORD_','123');
*/

define ('LANG', 'en');

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



// html filtering
$kses_allowed = 
  array(
	'img' => array('src' => 1, 'alt'=> 1),
	'b' => array(),
	'i' => array(),
	'a' => array('href' => 1, 'title' => 1),
	'p' => array(),
	'blockquote' => array(),
	'ul' => array(),	
	'li' => array());
?>
