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
# E-mail:      mbonetti at gmail dot com
# Web page:    http://gregarius.net/
#
###############################################################################


class SideMenu {

	var $items;
	var $ctnr = "";
	var $activeElement;
	
	function SideMenu() {
		$this -> items = array();
		$this -> activeElement = isset($_COOKIE['side']) ? $_COOKIE['side']:"0";
		$GLOBALS['rss']->sideMenu = $this;
	}
	
	function addMenu($label, $id, $action="") {
		$this -> items[] =
			array(
				"label" => $label,
				"id" => $id,
				"action" => $action,
				"class" => ($id == $this-> activeElement ? "active":"")
			);
	}
	
	function setContainer($ctnr) {
		$this -> ctnr = $ctnr;
	}
	
	function render() {
		rss_plugin_hook('rss.plugins.sidemenu', null);
		foreach ($this -> items as $item) {
			echo "<" . 
				$this -> ctnr 
				.' class="'.$item['class']. '"'
				.' id="sidemenu'.$item['id']. '"'
				.">"
				.'<a href="#" onclick="'. $item["action"] .'; return false;">'
				.$item["label"]
				.'</a>'
				."</" . $this -> ctnr . ">\n";
		}
	}
}
?>
