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
# FITNESS FOR A PARTICULAR PURPOSE.	 See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:	   mbonetti at gmail dot com
# Web page:	   http://gregarius.net/
#
###############################################################################


function themes_admin() {
    return CST_ADMIN_DOMAIN_THEMES;
}


function themes() {
	$themes = getThemes();

	if (isset($_GET['theme']) && array_key_exists($_GET['theme'],$themes)) {
		$active_theme = sanitize($_GET['theme'], RSS_SANITIZER_SIMPLE_SQL |RSS_SANITIZER_NO_SPACES);
		
		$sql = "update " . getTable('config') . " set value_ = '$active_theme'"
			   ." where key_='rss.output.theme'";
		rss_query($sql);
		
		rss_invalidate_cache();
	}	 else {
		$active_theme= getConfig('rss.output.theme');
    }
    
	echo "<h2 class=\"trigger\">".LBL_ADMIN_THEMES."</h2>\n"
    ."<div id=\"admin_themes\" >\n";
    echo LBL_ADMIN_THEMES_GET_MORE;


	foreach ($themes as $entry => $theme) {

        extract($theme);
        if (!$name) {
            $name = $entry;
        }
        if ($url) {
            $author = "<a href=\"$url\">$author</a>";
        }
        $active = ($entry ==  $active_theme);
        if ($screenshot) {
        	$screenshotURL = "<img src=\"". getPath() . RSS_THEME_DIR . "/$fsname/$screenshot\"  />";
        } else {
        	$screenshotURL = "<img src=\"". getPath() . RSS_THEME_DIR . "/default/media/noscreenshot.png\" />";
        }
        $h4="$name"; 
        $h5="By&nbsp;$author | Version:&nbsp;$version";
        if ($htmltheme) {
        	$seturl = "index.php?view=themes&amp;theme=$entry";
        } else {
        	$seturl = "";
        }
        echo "<div class=\"themeframe".($active?" active":"")."\"><span>";
        if (!$active && $htmltheme) {
        	echo "<a href=\"$seturl\" class=\"bookmarklet\">Use this theme</a>";
        } elseif($active) {
        	echo "<p class=\"bookmarklet\">Active theme</p>";
        }
        echo "<h4>$h4</h4>\n"
        	."<h5>$h5</h5>\n"
        	."<p class=\"themescreenshot\">$screenshotURL</p>"
        	."<p>$description</p>&nbsp;"        	
        	."</span></div>\n";
    }

	echo "</div>\n";
}

function getThemes() {

    $d = dir('../'. RSS_THEME_DIR);
    $files = array();
    $ret = array();
    $activeIdx = "0";
    while (false !== ($theme = $d->read())) {
        if ($theme != "CVS" && !is_file("../".RSS_THEME_DIR."/$theme") && substr($theme,0,1) != ".") {
            $ret[$theme]=getThemeInfo($theme);
        }
    }
    $d->close();
    return $ret;
}

function getThemeInfo($theme) {

    $path = "../".RSS_THEME_DIR."/$theme/.themeinfo";
    $ret = array(
               'name' => '',
               'url' => '',
               'official' => false,
               'fsname' => $theme,
               'description' => '',
               'htmltheme' => true,
               'version' => "1.0",
               'author' => '',
               'screenshot' => ''
           );
    if (file_exists($path)) {
        $f = @fopen($path,'r');
        $contents = "";
        if ($f) {
            $contents .= fread($f, filesize($path));
            @fclose($f);
        } else {
            $contents = "";
        }

        if ($contents && preg_match_all("/^\s?([^:]+):(.*)$/m",$contents,$matches,PREG_SET_ORDER)) {
            foreach($matches as $match) {
                $key = trim(strtolower($match[1]));
                $val = trim($match[2]);
                if (array_key_exists($key,$ret)) {
                    if ($val == 'true') {
                        $ret[$key] = true;
                    }
                    elseif ($val == 'false') {
                        $ret[$key] = false;
                    }
                    else {
                        $ret[$key] = $val;
                    }
                }
            }
        }
    }
    return $ret;
}

?>
