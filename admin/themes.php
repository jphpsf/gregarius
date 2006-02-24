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
    }    else {
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
        echo "<h4>$h4</h4>\n";
        if( file_exists( "../" . RSS_THEME_DIR . "/$fsname/config.php" ) )
            echo "<a class=\"bookmarklet\" href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=".
                    CST_ADMIN_DOMAIN_THEME_OPTIONS
                    ."&amp;theme=".$entry
                    ."&amp;" .CST_ADMIN_VIEW ."=" .CST_ADMIN_DOMAIN_THEME_OPTIONS
                    ."\">" . LBL_ADMIN_CONFIGURE . "</a>";
        echo "<h5>$h5</h5>\n"
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

function theme_options_admin() {
    return CST_ADMIN_DOMAIN_THEME_OPTIONS;
}

function theme_options() {
    if (!array_key_exists('theme',$_REQUEST) ||
            array_key_exists('admin_theme_options_cancel_changes', $_REQUEST)) {
        themes();
        return;
    }

    $theme = $_REQUEST['theme'];
    $theme_output = "";
    if (preg_match('/([a-zA-Z0-9_\/\-]+)/',$theme,$matches)) {
        $theme = $matches[1]; // sanitize input
        $theme_info = getThemeInfo($theme);
        extract($theme_info);
        if( file_exists( "../" . RSS_THEME_DIR . "/$fsname/config.php" ) ) {
            ob_start();
            rss_theme_options_rendered_buttons( false );
            rss_require( RSS_THEME_DIR . "/$fsname/config.php" );
            $theme_output = ob_get_contents();
            ob_end_clean();
            rss_invalidate_cache();
        }
        if ($theme_output) { // Let us set up a form
            echo "<h2
            class=\"trigger\">".LBL_ADMIN_THEME_OPTIONS." ".TITLE_SEP." ". $name. "</h2>\n"
            ."<div id=\"admin_theme_options\">\n";
            echo "<form method=\"post\" ";
            if( rss_theme_options_form_class() !== null ) {
                echo "class='" . rss_theme_options_form_class() . "' ";
            }
            echo "action=\"" .$_SERVER['PHP_SELF'] ."\">\n";
            echo "<p><input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN
            ."\" value=\"".CST_ADMIN_DOMAIN_THEME_OPTIONS."\" /></p>\n";
            echo $theme_output;
            echo "<p><input type=\"hidden\" name=\"theme\" value=\"".$theme."\"/>\n";
            echo "<p><input type=\"hidden\" name=\"". CST_ADMIN_METAACTION
            ."\" value=\"LBL_ADMIN_SUBMIT_CHANGES\"/>\n";
            if( !rss_theme_options_rendered_buttons() )
            {
                echo "<input type=\"submit\" name=\"admin_theme_options_submit_changes\" value=\""
                .LBL_ADMIN_SUBMIT_CHANGES."\" />\n";
                echo "<input type=\"submit\" name=\"admin_theme_options_cancel_changes\"
                value=\"".LBL_ADMIN_CANCEL."\" />\n";
            }
            echo "</p></form>\n";
            echo "</div>";
        } else {
            themes();
        }
    }
}

function rss_theme_options_rendered_buttons($value=null) {
    static $__rss_theme_options_rendered_buttons;
    if( $value !== null )
        $__rss_theme_options_rendered_buttons = $value;
    return $__rss_theme_options_rendered_buttons;
}

function rss_theme_options_form_class($value=null) {
    static $__rss_theme_options_form_class;
    if( $value !== null )
        $__rss_theme_options_form_class = $value;
    return $__rss_theme_options_form_class;
}


function rss_theme_options_is_submit() {
    return array_key_exists("admin_theme_options_submit_changes", $_REQUEST);
}

?>
