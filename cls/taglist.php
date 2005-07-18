<?php

require_once('cls/alltags.php');
class TagList extends Tags{
	
	function TagList() {
		parent::Tags();
	}
	
	function render() {
		/*
		$spread = max($this->allTags) - min($this->allTags);
		if ($spread <= 0) {
			$spread = 1;
		};
		$fontspread = LARGEST - SMALLEST;
		$fontstep = $spread / $fontspread;
		if ($fontspread <= 0) {
			$fontspread = 1;
		}
		$ret = "";
		*/
		echo "<h2>".LBL_TAG_TAGS."</h2>\n";
		echo "<ul>";
		foreach ($this -> allTags as $tag => $cnt) {
			//$size = (SMALLEST + ($cnt / $fontstep)).UNIT;
			echo "<li style=\"font-size:medium\"><a href=\"".$this -> makeTagLink($tag) ."\">$tag</a> ($cnt)</li>";
		}
		echo "</ul>";
	}
}
?>