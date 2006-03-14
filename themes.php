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

function getThemePath($path=null) {
    list($theme,$media) = getActualTheme();
    if (null === $path)
        $path = getPath();
    return $path.RSS_THEME_DIR."/$theme/$media/";
}

/**
 * Returns an array holding the "main" theme to use,
 * as well as the detected "media" (@see getThemeMedia)
 */
function getActualTheme() {
    static $ret;

    if ($ret) {
        return $ret;
    }


    // Theme
    $theme = getConfig('rss.output.theme');
    if (null === $theme)
        $theme = 'default';
    if (defined('THEME_OVERRIDE')) {
        $theme = THEME_OVERRIDE;
    }
    elseif (isset($_GET['theme'])) {
        $theme = preg_replace('/[^a-zA-Z0-9_]/','',$_GET['theme']);
    }

    $theme = sanitize($theme,RSS_SANITIZER_CHARACTERS);
    
    // Media
    $media = getThemeMedia();
    
    if( !file_exists(GREGARIUS_HOME.RSS_THEME_DIR."/$theme/$media/") )
        $theme = 'default';

	$ret = array($theme,$media);
    return $ret;
}


/**
 * Returns the theme's "media" component, e.g. 'web', 
 * 'rss' or 'mobile'.
 */
function getThemeMedia() {
    static $media;

    if ($media) {
        return $media;
    }

    // Default to "web".
    $media = 'web';

    // Has the user specified anything?
    if (isset($_GET['rss'])) {
        $media = 'rss';
    }
    elseif (isset($_SESSION['mobile']) || isset($_REQUEST['mobile']) || isMobileDevice()) {
        $media = 'mobile';
    }

    // This is here so that auto-detected (e.g. mobile) medias
    // can be overridden.
    if (isset($_REQUEST['media'])) {
        $media = sanitize($_REQUEST['media'], RSS_SANITIZER_CHARACTERS);
    }

    // Finally: let plugins voice their opinion
    $media = rss_plugin_hook('rss.plugins.thememedia',$media);

    return $media;
}

/**
 * Dumb dunciton to detect mobile devices based on the passed user-agent.
 * This definitely needs some heavy tweaking.
 */
function isMobileDevice() {
    static $ret;
    if ($ret !== NULL) {
        return $ret;
    } else {
        $ret = false;
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = $_SERVER['HTTP_USER_AGENT'];
            $ua_lwr = strtolower( $ua );
            $ret = strpos($ua, 'SonyEricsson') !== FALSE
            		|| strpos($ua, 'Symbian') !== FALSE
                || strpos($ua, 'Nokia') !== FALSE
                || strpos($ua, 'Mobile') !== FALSE
                || strpos($ua, 'Windows CE') !== FALSE
                || strpos($ua, 'EPOC') !== FALSE
                || strpos($ua, 'Opera Mini') !== FALSE
                || strpos($ua_lwr, 'j2me') !== FALSE
                || strpos($ua, 'Netfront') !== FALSE;
            // if none of those matched, let's have a gander at grabbing the resolution...
            if (!$ret && eregi( "([0-9]{3})x([0-9]{3})", $ua, $matches ) ) {
                if ($matches[1]<600 || $matches[2]<600) {
                    $ret = true; //one of the screen dimensions is less than 600 - we'll call it a mobile device
                }
            }
        }

        return $ret;
    }
}

function rss_theme_option_ref_obj_from_theme($theme=null, $media=null) {
    if ($theme===null) {
        list($theme,$media) = getActualTheme();
    }
    
    $ref_obj = "theme.$theme";
    if( $media !== null )
        $ref_obj .= ".$media";
        
    return $ref_obj;
}

function rss_theme_get_option($option, $theme=null, $media=null) {
    return getProperty(rss_theme_option_ref_obj_from_theme($theme,$media), $option);
}

function rss_theme_set_option($option, $value, $theme, $media) {
    setProperty(rss_theme_option_ref_obj_from_theme($theme,$media), $option, 'theme', $value);
}

function rss_theme_delete_option($option, $theme=null, $media=null) {
    return deleteProperty(rss_theme_option_ref_obj_from_theme($theme,$media), $option);
}

function rss_theme_config_override_option_name_mangle($config_key) {
    return preg_replace( '/^rss\./', 'rss.prop.theme.', $config_key ) . '.override';
}

function rss_theme_config_override_option($config_key, $default, $theme=null, $media=null) {
    $ret = getProperty(rss_theme_option_ref_obj_from_theme($theme,$media), rss_theme_config_override_option_name_mangle($config_key));
    if( $ret === null )
        $ret = $default;
    rss_config_override($config_key,$ret);
    return $ret;
}

function rss_theme_set_config_override_option($config_key, $value, $theme=null, $media=null) {
    setProperty(rss_theme_option_ref_obj_from_theme($theme,$media), rss_theme_config_override_option_name_mangle($config_key), 'theme', $value);
}

function rss_theme_delete_config_override_option($config_key, $theme=null, $media=null) {
    deleteProperty(rss_theme_option_ref_obj_from_theme($theme,$media), rss_theme_config_override_option_name_mangle($config_key));
}

function loadSchemeList($pretty, $theme=null, $media=null) {
    if ($theme===null) {
        list($theme,$media) = getActualTheme();
    }

    $ret = array( '(use default scheme)' );
    
	if( file_exists( GREGARIUS_HOME.RSS_THEME_DIR."/$theme/$media/schemes" ) )
	{
		if( $checkDir = opendir( GREGARIUS_HOME.RSS_THEME_DIR."/$theme/$media/schemes" ) ) {
			while($file = readdir($checkDir)){
				if($file != "." && $file != ".." && $file != ".svn"){
				   if(file_exists(GREGARIUS_HOME.RSS_THEME_DIR."/$theme/$media/schemes/" . $file) && is_dir(GREGARIUS_HOME.RSS_THEME_DIR."/$theme/$media/schemes/" . $file)){
					   if( $pretty )
					   		$theme_info = getThemeInfo( "$theme/$media/schemes/$file" );
					   if( $pretty && isset( $theme_info['name'] ) && $theme_info['name'] !== '' )
						   $ret[] = str_replace( ",", "_", $theme_info['name'] );
					   else
						   $ret[] = str_replace( ",", "_", $file );
				   }
				}
			}
		}
	}
    
    return $ret;
}


function rss_scheme_stylesheets($theme=null, $media=null) {
    if ($theme===null) {
        list($theme,$media) = getActualTheme();
    }

    $ret = getProperty(rss_theme_option_ref_obj_from_theme($theme,$media), rss_theme_config_override_option_name_mangle('rss.output.theme.scheme'));
    if( $ret === null )
		return "";

	$arr = explode(',',$ret);
	$ret = "";
	$idx = array_pop($arr);
	foreach (loadSchemeList( false, $theme, $media) as $i => $val) {
		if ($i == $idx) {
			if( $i > 0 ) {
				if( file_exists( GREGARIUS_HOME.RSS_THEME_DIR."/$theme/$media/schemes/$val" ) && is_dir( GREGARIUS_HOME.RSS_THEME_DIR."/$theme/$media/schemes/$val" ) ) {
                    foreach( glob( GREGARIUS_HOME.RSS_THEME_DIR."/$theme/$media/schemes/$val/*.css" ) as $file ) {
                        $file = substr( $file, strlen( GREGARIUS_HOME.RSS_THEME_DIR."/$theme/$media/schemes/$val/" ) );
                        $file = getPath().RSS_THEME_DIR."/$theme/$media/schemes/$val/$file";
                        $ret .= "	<link rel=\"stylesheet\" type=\"text/css\" href=\"$file\" />\n";
                    }
				}
			}
			break;
		}
	}
	
	return $ret;
}

?>
