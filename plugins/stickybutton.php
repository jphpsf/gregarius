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


/// Name: Sticky Button
/// Author: Martey Dodoo
/// Description: Adds an "Sticky" button to the navigation links.
/// Version: 1.1

function __stickybutton_add(){

    if (getConfig('rss.output.usemodrewrite')) {
	$url = getPath() . "sticky/";
    } else {
	$url = getPath() . "sticky.php";
    }
    
    
    $GLOBALS['rss']->nav->appendNavItem($url,'Stick<span>y</span>');
}

rss_set_hook("rss.plugins.navelements", "__stickybutton_add");

?>
