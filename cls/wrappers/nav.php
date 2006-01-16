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

function rss_nav() {
    $nav = new Navigation();
    $nav -> render();
}

function rss_nav_items() {
    foreach ($GLOBALS['rss']->nav->items as $item) {
        $item -> render();
    }
}

function rss_nav_item_class() {
    return ($GLOBALS['rss'] -> currentNavItem -> isActive ?
        " class=\"active\"":"");
}

function rss_nav_item_accesskey() {
    return ($GLOBALS['rss'] -> currentNavItem -> accessKey ?
      " accesskey=\"".$GLOBALS['rss'] -> currentNavItem -> accessKey ."\" " : "");
}

function rss_nav_item_label() {
    return $GLOBALS['rss'] -> currentNavItem -> label;
}

function rss_nav_item_href() {
    return $GLOBALS['rss'] -> currentNavItem -> href;
}

function rss_nav_afternav() {
	return $GLOBALS['rss'] -> nav -> postRender;
}
?>
