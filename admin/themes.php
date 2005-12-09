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
/*
function plugin_options_admin() {
    if (array_key_exists('plugin_name',$_REQUEST)) {
        return CST_ADMIN_DOMAIN_PLUGIN_OPTIONS;
    } else {
        return CST_ADMIN_DOMAIN_PLUGINS;
    }
}
*/
function themes() {
	$themes = getThemes();
	if (isset($_REQUEST[CST_ADMIN_METAACTION]) && $_REQUEST[CST_ADMIN_METAACTION] == 'LBL_ADMIN_SUBMIT_CHANGES') {
		if (isset($_REQUEST['value'])) {
			$sql = "update " . getTable('config') . " set value_ = '". $_POST['value']."'"
			." where key_='rss.output.theme'";
			rss_query($sql);
			$active_theme = $_POST['value'];
		}	
	} else {
		$active_theme= getConfig('rss.output.theme');
	}
    echo "<h2 class=\"trigger\">".LBL_ADMIN_THEMES."</h2>\n"
    ."<div id=\"admin_plugins\">\n";


    echo LBL_ADMIN_THEMES_GET_MORE;

	
    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";	
//	echo "<input type=\"hidden\" name=\"value\" value=\"\" />\n";
	echo "<p><input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_THEMES."\" /></p>\n";

	echo "</p>\n<table id=\"plugintable\">\n<tr>\n"
	."<th>".LBL_ADMIN_PLUGINS_HEADING_ACTION."</th>\n"
	."<th>".LBL_ADMIN_PLUGINS_HEADING_NAME."</th>\n"
	."<th>".LBL_ADMIN_PLUGINS_HEADING_VERSION."</th>\n"
	."<th>".LBL_ADMIN_PLUGINS_HEADING_AUTHOR."</th>\n"
	."<th>".LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION."</th>\n"
	."</tr>\n";

	$cntr = 0;
	foreach ($themes as $entry => $theme) {

		extract($theme);
		if (!$name) {
			$name = $entry;
		}
		if ($url) {
			$author = "<a href=\"$url\">$author</a>";
		}
		$active = ($entry ==  $active_theme);
		echo "<tr class=\""
		.(($cntr++ % 2 == 0)?"even":"odd")
		.($active?" active":"")
		."\">\n";
		echo "<td class=\"cntr\">"
		."<input type=\"radio\" name=\"value\" "
		." id=\"_gregarius_theme_$entry\" value=\"$entry\" "
		.($active?" checked=\"checked\"":"")
		.(!$htmltheme?" disabled=\"disabled\"":"")
		." />\n"
		."</td>\n";
		echo "<td><label for=\"_gregarius_theme_$entry\">".($name?$name:"&nbsp"). "</label></td>\n";
		echo "<td class=\"cntr\">".($version?$version:"&nbsp"). "</td>\n";
		echo "<td>"	.($author?$author:"&nbsp") . "</td>\n";
		echo "<td>"	.($description?$description:"&nbsp"). "</td>\n";

		echo "</tr>\n";
	}
	echo "</table>\n";

    echo "<p><input type=\"hidden\" name=\"". CST_ADMIN_METAACTION ."\" value=\"LBL_ADMIN_SUBMIT_CHANGES\"/>\n";
    echo "<input type=\"submit\" name=\"admin_plugin_submit_changes\" value=\"".LBL_ADMIN_SUBMIT_CHANGES."\" /></p>\n";
	
	echo "</form>\n";
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