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


class NavItem {
    var $href;
    var $label;
    var $accessKey = null;
    var $isActive = false;
    var $loc;
    
    function NavItem($href,$label,$loc) {
        $this->href = $href;
        $this->label = $label;
        $this->loc = $loc;
        if(preg_match('#.*<span>(.)</span>.*#',$label,$matches)) {
            $this->accessKey = strtolower($matches[1]);
        }
        if ($loc && $loc == $GLOBALS['rss']->header->active) {
            $this->isActive=true;
        }
    }
    
    function render() {
        $GLOBALS['rss'] -> currentNavItem = &$this;
        rss_require(RSS::getTemplateFile("navitem.php"), false);
    }
}

class Navigation {
    
    var $items = array();
    var $postRender = "";
    
    function Navigation() {
    
        $this->appendNavItem(getPath(),__('<span>H</span>ome'),LOCATION_HOME);
        if (!getConfig("rss.config.restrictrefresh")) {
        	$this->appendNavItem(getPath().'update.php',__('<span>R</span>efresh'),LOCATION_UPDATE);
        }
        $this->appendNavItem(getPath().'search.php',__('<span>S</span>earch'),LOCATION_SEARCH);
        $this->appendNavItem(getPath().'admin/',__('A<span>d</span>min'),LOCATION_ADMIN);
        
        if (($an = rss_plugin_hook('rss.plugins.afternav', null)) != null) {
        	$this -> postRender .= $an;
        }
        
        $GLOBALS['rss']->nav = $this;
        rss_plugin_hook('rss.plugins.navelements', null);
    }
        
        
    function appendNavItem($url,$label,$loc = null) {
        $this->items[] = new NavItem($url,$label,$loc);
    }

    function addNavItem($url,$label,$loc = null) {
        $item = array_pop($this->items);
        $this->appendNavItem($url,$label,$loc);
        $this->items[] = $item;
    }
    
	function render() {
		rss_require(RSS::getTemplateFile("nav.php"));
  	}
}

?>
