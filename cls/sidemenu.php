<?php

class SideMenu {

	var $items;
	var $ctnr = "";
	var $activeElement;
	
	function SideMenu() {
		$this -> items = array();
		$this -> activeElement = isset($_COOKIE['side']) ? $_COOKIE['side']:"feeds";
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