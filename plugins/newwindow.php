<?php
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
# E-mail:      mbonetti at gmail dot com
# Web page:    http://gregarius.net/
#
###############################################################################


/// Name: New window
/// Author: Marco Bonetti
/// Description: When active, this plugin will open off-site links in a new window
/// Version: 1.7

function __new_window_js_register($js) {
    $js[] = getPath(). RSS_PLUGINS_DIR . "/newwindow.php?nwjs";
    return $js;
}

function __new_window_init_js($dummy) {
    echo "\n<script type=\"text/javascript\">\n"
      ."<!--\n"
      ."__new_window();\n"
      ."-->\n"
      ."</script>\n";
    return $dummy;
}

if (isset($_REQUEST['nwjs'])) {

	require_once('../util.php');
    ETagHandler(md5("newwindow".'$Revision$'));
    
    ?>
	function __new_window() {
    if (document.getElementsByTagName) {
        var items = document.getElementById("items");
        if (items) {
            var anchors = items.getElementsByTagName("a");
            for (var i=0; i<anchors.length; i++) {
                var anchor = anchors[i];
                if (anchor.href && (anchor.href.indexOf('<?php echo  $_SERVER['HTTP_HOST'] ?>') < 0)) {
                    anchor.target = '_blank';
                }
            }
        }
    }
	}
<?php
    flush();
    exit();
}

rss_set_hook('rss.plugins.javascript','__new_window_js_register');
rss_set_hook('rss.plugins.bodyend','__new_window_init_js');

?>
