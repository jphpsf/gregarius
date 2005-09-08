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


/// Name: Rounded Corners
/// Author: Marco Bonetti
/// Description: Rounded corners in some GUI elements. Enabling this plugin breaks the CSS validation.
/// Version: 0.1

function __rc_CSS($dummy) {
	$url = getPath(). RSS_PLUGINS_DIR . "/roundedcorners.php?rc-css";
    return "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"$url\" />\n";
}

if (isset($_GET['rc-css'])) {
	$css = "
/* bad bad bad */
.frame,.item,h3.collapsed,table,div.content img,#sidemenu li,
ul.navlist li,a.bookmarklet, fieldset, div#pbholder, div.ief,
div.ief p a, #loginfo, input[type=\"submit\"] { -moz-border-radius: 5px }
";
	$key = md5($css);
  	if (array_key_exists('HTTP_IF_NONE_MATCH',$_SERVER) &&
    	$_SERVER['HTTP_IF_NONE_MATCH'] == $key) {
		  header("HTTP/1.1 304 Not Modified");
		  flush();
    } else {
		  header("ETag: $key");
		  header('Content-Type: text/css');
		  echo $css;
	}
	exit();
}


rss_set_hook("rss.plugins.stylesheets",'__rc_CSS');
?>
