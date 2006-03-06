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
            echo "<input type=\"hidden\" name=\"". CST_ADMIN_METAACTION
                   ."\" value=\"LBL_ADMIN_SUBMIT_CHANGES\"/>\n";
            if( isset( $_REQUEST['mediaparam'] ) ) //pass it along
            {
                $mediaparam = sanitize($_REQUEST['mediaparam'], RSS_SANITIZER_CHARACTERS);
                echo( "<input type=\"hidden\" name=\"mediaparam\" value=\"$mediaparam\"\n" );
            }
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

// we take the array that's input, and return an array as if it had been selected 
// from the config table and dumped out using rss_fetch_assoc.  This means the 
// theme author does not have to pass anything more than key_ in his input array.
// If $key is not null we query and return only that item, otherwise we fill
// an array to match the entire input array
function theme_options_fill_override_array($theme, $media, $array_input, $key=null) {
    $ret = array();
    if( !is_array( $array_input ) ) {
        $array_input = split( ",", $array_input );
    }
    
    foreach( $array_input as $inp ) {
        if( !is_array( $inp ) && isset( $inp ) ) {
            $inp = array( 'key_' => $inp );
        }
        
        if( isset( $inp['key_'] ) ) {
            $thisret = array();
            if( $key === null || $key === $inp['key_'] ) {
                if($inp['key_'] == 'rss.output.theme.scheme') {
                    $thisret = $inp;
                    $schemes = loadSchemeList( true, $theme, $media );
                    if( !isset( $inp['default_'] ) )
                        $thisret['default_'] = implode(',', $schemes ) . ",0";
                    $thisret['type_'] = 'enum';
                    if( !isset( $inp['desc_'] ) )
                        $thisret['desc_'] = 'The color scheme to use.';
                    if( !isset( $inp['export_'] ) )
                        $thisret['export_'] = '';
                        
                    $value = rss_theme_config_override_option($thisret['key_'], $thisret['default_'], $theme, $media);
                    $value = array_pop( explode( ',', $value ) );
                    $thisret['value_'] = implode(',', $schemes ) . "," . $value;
                    
                } else {
                    $sql = "select * from " .getTable("config") ." where key_ like
                           '" . $inp['key_'] . "'";
                    $res = rss_query($sql);
                    if ($row = rss_fetch_assoc($res)) {
                        foreach( $row as $rowkey => $rowval ) {
                            if( $rowkey !== 'value_' ) {
                                if( !isset( $inp[$rowkey] ) ) {
                                    $thisret[$rowkey] = $rowval;
                                } else {
                                    $thisret[$rowkey] = $inp[$rowkey];
                                }
                            }
                        }
                    }
                    
                    $thisret['value_'] = rss_theme_config_override_option($thisret['key_'], $thisret['default_'], $theme, $media);
                }

                if( $key === null )
                    $ret[] = $thisret;
                else
                    $ret = $thisret;
            }
        } else {
            rss_error('rss_theme_options_configure_overrides was passed an item with no key_', RSS_ERROR_ERROR,true);
        }
    }
    
    return $ret;
}

// Display a configuration form similar to the form in the admin->config section
// except we will get the theme's overrides via rss_themes_get_option.  You will
// need to pass in the theme, the media (optional - pass null for no media) and
// an array.
// The array is where it gets complex.  Each member of the array can be a string
// representing the key of a configuration item, or it can be an array that specifies
// the various properties of the configuration item.  These properties are the
// same as the fields in the config table (key_, default_, descr_) and any that
// are missing will be loaded from that table.  The keys of the configuration
// items may match an entry in the config table, or you can create a custom one.
// In the later case, you *must* use the second form of the $config_items array
// and you must pass all the fields that this function uses (key_, default_, type_
// and desc_)  Note that there is no point to passing value_ as this is loaded
// via a call to rss_themes_get_option.
function rss_theme_options_configure_overrides($theme, $media, $config_items) {
    $action = null;

	if (isset($_REQUEST[CST_ADMIN_METAACTION])) {
		$action = $_REQUEST[CST_ADMIN_METAACTION];
	} else if (isset($_REQUEST['action'])) {
		$action = $_REQUEST['action'];
	}
	
	if( isset( $_REQUEST['mediaparam'] ) && $media === sanitize($_REQUEST['mediaparam'], RSS_SANITIZER_CHARACTERS) ) {
		if (array_key_exists(CST_ADMIN_CONFIRMED,$_POST) && $_POST[CST_ADMIN_CONFIRMED] == LBL_ADMIN_YES) {
			if (!array_key_exists('key',$_REQUEST)) {
				rss_error('Invalid config key specified.', RSS_ERROR_ERROR,true);
			} else {
				$key = sanitize($_REQUEST['key'],RSS_SANITIZER_NO_SPACES|RSS_SANITIZER_SIMPLE_SQL);
				rss_theme_delete_config_override_option($key,$theme,$media);
			}
			$action = null; //redirect to our theme's admin page
		} else if( rss_theme_options_is_submit() ) {
			switch ($action) {
			case LBL_ADMIN_SUBMIT_CHANGES:
			case 'LBL_ADMIN_SUBMIT_CHANGES':
				if (!array_key_exists('key',$_REQUEST)) {
					rss_error('Invalid config key specified.', RSS_ERROR_ERROR,true);
					break;
				}
				if (!array_key_exists('type',$_REQUEST)) {
					rss_error('Invalid config type specified.', RSS_ERROR_ERROR,true);
					break;
				}
				if (!array_key_exists('value',$_REQUEST)) {
					rss_error('Invalid config value specified.', RSS_ERROR_ERROR,true);
					break;
				}
				
				$key = sanitize($_REQUEST['key'],RSS_SANITIZER_NO_SPACES|RSS_SANITIZER_SIMPLE_SQL);
				$type = sanitize($_POST['type'],RSS_SANITIZER_CHARACTERS);
				$value = sanitize($_POST['value'], RSS_SANITIZER_SIMPLE_SQL);
	
				if( $type == 'enum' ) {
					$item = theme_options_fill_override_array($theme,$media,$config_items,$key);
					if( count( $item ) ) {
						$arr = explode(',',$item['default_']);
						$idx = array_pop($arr);
						$newkey = -1;
						foreach ($arr as $i => $val) {
							if ($val == $value) {
								$newkey = $i;
							}
						}
						reset($arr);
						if ($newkey > -1) {
							array_push($arr, $newkey);
							rss_theme_set_config_override_option($key, implode(',',$arr), $theme, $media);
						} else {
							rss_error("Oops, invalid value '$value' for this config key", RSS_ERROR_ERROR,true);
						}
					}
				} else {
					rss_theme_set_config_override_option($key, $value, $theme, $media);
				}
	
				break;
				
			default:
				rss_error('Invalid config action specified.', RSS_ERROR_ERROR,true);
				break;
			}
			$action = null; //redirect to our theme's admin page
		}
    }
    
    switch ($action) {
    case CST_ADMIN_DEFAULT_ACTION:
    case 'CST_ADMIN_DEFAULT_ACTION':
        if( isset( $_REQUEST['mediaparam'] ) && $media === sanitize($_REQUEST['mediaparam'], RSS_SANITIZER_CHARACTERS) ) {
			if (!array_key_exists('key',$_REQUEST)) {
				rss_error('Invalid config key specified.', RSS_ERROR_ERROR,true);
				break;
			}
			$key = sanitize($_REQUEST['key'],RSS_SANITIZER_NO_SPACES|RSS_SANITIZER_SIMPLE_SQL);
			$item = theme_options_fill_override_array($theme,$media,$config_items,$key);
			if( count( $item ) ) {
				extract( $item );
				config_default_form($key_, $type_, $default_, CST_ADMIN_DOMAIN_THEME_OPTIONS);
				rss_theme_options_form_class('box');
				rss_theme_options_rendered_buttons(true);
			}
		}
        break;
        
    case CST_ADMIN_EDIT_ACTION:
    case 'CST_ADMIN_EDIT_ACTION':
        if( isset( $_REQUEST['mediaparam'] ) && $media === sanitize($_REQUEST['mediaparam'], RSS_SANITIZER_CHARACTERS) ) {
			if (!array_key_exists('key',$_REQUEST)) {
				rss_error('Invalid config key specified.', RSS_ERROR_ERROR,true);
				break;
			}
			$key = sanitize($_REQUEST['key'],RSS_SANITIZER_NO_SPACES|RSS_SANITIZER_SIMPLE_SQL);
			$item = theme_options_fill_override_array($theme,$media,$config_items,$key);
			if( count( $item ) ) {
				extract( $item );
				$dummy = null;
				config_edit_form($key_,$value_,$default_,$type_,$desc_,$export_,$dummy);
			}
		}
		break;
        
    default:
        $caption = "Configuration overrides";
        if( isset( $media ) ) {
            $caption .= " for $media media";
        }
        config_table_header($caption);

        $cntr = 0;
        $items = theme_options_fill_override_array($theme,$media,$config_items);
        foreach( $items as $item ) {
            config_table_row( $item, (($cntr++ % 2 == 0)?"even":"odd"), CST_ADMIN_DOMAIN_THEME_OPTIONS, "&theme=$theme&mediaparam=$media" );
        }

        config_table_footer();
        
        //no buttons here
        rss_theme_options_rendered_buttons(true);

        break;
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
