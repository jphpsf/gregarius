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

/// Name: Doubleclick to Read
/// Author: Marco Bonetti
/// Description: Marks an item as read when you doubleclick its whitespace
/// Version: 1.8

/**
 * Changelog:
 *
 * 1.5  Updated description
 * 1.6  Fixed a bug that would cause a Javascript error when user is not logged in
 * 1.7  Moved the EtagHandler to make the javascript load faster. -- Sameer
 * 1.8  Switched to the rss_bootstrap caching method 
 */

function __dblclicktoread_js_register($js) {
    $js[] = getPath(). RSS_PLUGINS_DIR . "/dblclicktoread.php?dcljs";
    return $js;
}

function __dblclickToRead_init_js($dummy) {
   if (!hidePrivate()) {
		 echo "\n<script type=\"text/javascript\">\n"
			."<!--\n"
			."__dbclickToRead_jsInit();\n"
			."-->\n"
			."</script>\n";
	}
   return $dummy;
}

if (isset($_REQUEST['dcljs'])) {
	 require_once('../core.php');
	 rss_bootstrap(false,'$Revision$',24);
    require_once('../init.php');


    if (hidePrivate()) {
		return "";
    }
    
    ?>

	var isIE=document.all?true:false;
	function __dblclickToRead_js_getId(o) {
		if (html = o.innerHTML) {
			if (r1 = new RegExp(".*es.([0-9]+),([0-9]+).*","gm").exec(html)) {
          		if (!isIE) {
                	c = unreadCnt(-1);
            	} else {
                	c = 1;
            	}
				id=r1[1];
				s =r1[2] & <?= SET_MODE_READ_STATE ?>;
				if ((sel = document.getElementById('<?= SHOW_WHAT ?>')) &&
			    	sel.options[sel.selectedIndex].value == <?= SHOW_UNREAD_ONLY ?>) {
                	setItemHide(id, (c == 0));
				} else{
			    	setItemClass(id, 'item even');
   					if (document.all) {
            			o.ondblclick = function() {return false;}
					} else {
						o.setAttribute("ondblclick","return false;");
					}
				}

				setState(id,s);
			}
		}
	}
	function __dbclickToRead_jsInit() {
		var isIE=document.all?true:false;
		var items = document.getElementsByTagName('li');
		for (var i=0; i<items.length; i++) {
			var item = items[i];
			if (item.className == "item unread") {
            	if (isIE) {
                	item.ondblclick = function() { __dblclickToRead_js_getId(this); return false;}
            	} else {
                	item.setAttribute("ondblclick","__dblclickToRead_js_getId(this); return false;");
            	}
			}
		}
	}

    <?php
    flush();
    exit();
}

rss_set_hook('rss.plugins.javascript','__dblclicktoread_js_register');
rss_set_hook('rss.plugins.bodyend','__dblclickToRead_init_js');
?>
