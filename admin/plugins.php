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


function plugins_admin() {
    return CST_ADMIN_DOMAIN_PLUGINS;
}

function plugin_options_admin() {
    if (array_key_exists('plugin_name',$_REQUEST)) {
        return CST_ADMIN_DOMAIN_PLUGIN_OPTIONS;
    } else {
        return CST_ADMIN_DOMAIN_PLUGINS;
    }
}

function plugins() {

    // Submit changes
    if (isset($_POST['admin_plugin_submit_changes'])) {
        $active_plugins=array();
        foreach($_REQUEST as $rkey=>$rentry) {
            if (preg_match('/_gregarius_plugin.([a-zA-Z0-9_\/\-]+).php/',$rkey,$matches)) {
                $active_plugins[] = ($matches[1] .".php");
            }
        }
        $value = serialize($active_plugins);
        $sql = "update " . getTable('config') . " set value_='$value' where key_='rss.config.plugins'";
        rss_query($sql);
        rss_invalidate_cache();
    } else {
        $active_plugins= getConfig('rss.config.plugins');
    }

    // Check for updates
    $doUpdates = false;
    $updates = array();
    if  (isset($_POST['admin_plugin_check_for_updates'])) {
        $updates = plugins_check_for_updates();
        $doUpdates = true;
    }



    // Rendering
    echo "<h2 class=\"trigger\">".LBL_ADMIN_PLUGINS."</h2>\n"
    ."<div id=\"admin_plugins\">\n";


    echo LBL_ADMIN_PLUGINS_GET_MORE;

    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";
    echo "<p><input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_PLUGINS."\" /></p>\n";
    echo "\n<table id=\"plugintable\">\n<tr>\n"
    ."<th>".LBL_ADMIN_PLUGINS_HEADING_ACTION."</th>\n"
    ."<th>".LBL_ADMIN_PLUGINS_HEADING_NAME."</th>\n"
    ."<th>".LBL_ADMIN_PLUGINS_HEADING_VERSION."</th>\n"
    ."<th>".LBL_ADMIN_PLUGINS_HEADING_AUTHOR."</th>\n"
    ."<th>".LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION."</th>\n"
    ."<th>".LBL_ADMIN_PLUGINS_HEADING_OPTIONS."</th>\n";
    if ($doUpdates) {
        echo "<th>".LBL_ADMIN_PLUGINS_HEADING_UPDATES."</th>\n";
    }

    echo "</tr>\n";



    $rss_plugins = getPlugins();
    $cntr = 0;
    if ($rss_plugins) {
        foreach($rss_plugins as $entry => $info ) {
            $active= in_array($entry,$active_plugins);
            if (count($info)) {
                $updateDl = null;
                if (is_array($updates) && array_key_exists($info['file'],$updates)) {
                    $lastV = $updates[$info['file']][0];
                    $thisV = $info['version'];
                    if ($lastV > $thisV) {
                        $updateDl = str_replace("&","&amp;",$updates[$info['file']][1]);
                    }
                }

                $class = (($cntr++ % 2 == 0)?"even":"odd") .
                         ($updateDl? " hilite":($active?" active":""));

                echo "<tr class=\"$class\">\n";
                echo "<td class=\"cntr\">"
                ."<input type=\"checkbox\" name=\"_gregarius_plugin_$entry\" "
                ." id=\"_gregarius_plugin_$entry\" value=\"1\" "
                .($active?"checked=\"checked\"":"")." />\n"
                ."</td>\n";
                echo "<td><label
                for=\"_gregarius_plugin_$entry\">".(array_key_exists('name',$info)?$info['name']:"&nbsp").
                "</label></td>\n";
                echo "<td class=\"cntr\">"
                .(array_key_exists('version',$info)?$info['version']:"&nbsp"). "</td>\n";
                echo "<td>"	.(array_key_exists('author',$info)?$info['author']:"&nbsp"). "</td>\n";
                echo "<td>"	.(array_key_exists('description',$info)?$info['description']:"&nbsp"). "</td>\n";

                // output the column to call a plugin's config page.
                echo "<td  class=\"cntr\">";
                if(array_key_exists('configuration',$info)) {
                    $escaped_plugin_name = str_replace("/", "%2F", $entry);
                    echo "<a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=".
                    CST_ADMIN_DOMAIN_PLUGIN_OPTIONS
                    ."&amp;action=". CST_ADMIN_EDIT_ACTION. "&amp;plugin_name=".$escaped_plugin_name
                    ."&amp;" .CST_ADMIN_VIEW ."=" .CST_ADMIN_DOMAIN_PLUGIN_OPTIONS
                    ."\">" . LBL_ADMIN_EDIT
                    ."</a>";
                } else {
                    echo "&nbsp";
                }
                echo "</td>\n";

                if ($doUpdates && $updateDl) {
                    echo "<td class=\"cntr\">";
                    echo "<a href=\"$updateDl\">$lastV</a>";
                    echo "</td>";
                }
                elseif($doUpdates) {
                    echo "<td>&nbsp;</td>";
                }
                echo "</tr>\n";
            }
        }
    }
    echo "</table>\n";
    echo "<p><input type=\"hidden\" name=\"". CST_ADMIN_METAACTION ."\" value=\"LBL_ADMIN_SUBMIT_CHANGES\"/>\n";
    echo "<input type=\"submit\" name=\"admin_plugin_submit_changes\" value=\"".LBL_ADMIN_SUBMIT_CHANGES."\" />\n";
    echo "<input type=\"submit\" name=\"admin_plugin_check_for_updates\" value=\"".LBL_ADMIN_CHECK_FOR_UPDATES."\" /></p></form>\n";
    echo "</div>";
}

function plugin_options() {
    if (!array_key_exists('plugin_name',$_REQUEST) ||
            array_key_exists('admin_plugin_options_cancel_changes', $_REQUEST)) {
        plugins();
        return;
    }
    $plugin_filename = $_REQUEST['plugin_name'];
    $plugin_filename = str_replace("%2F", "/", $plugin_filename);
    $plugin_output = "";
    if (preg_match('/([a-zA-Z0-9_\/\-]+).php/',$plugin_filename,$matches)) {
        $plugin_filename = $matches[1] .".php"; // sanitize input
        $plugin_info = getPluginInfo($plugin_filename);
        if($plugin_info && array_key_exists('configuration', $plugin_info)) {
            $plugin_config_func = $plugin_info['configuration'];
            ob_start();
            require_once("../".RSS_PLUGINS_DIR. "/" . $plugin_filename);
            if(function_exists($plugin_config_func)) {
                call_user_func($plugin_config_func); // Are you happy now?
                $plugin_output = ob_get_contents();

            }
            ob_end_clean();
            rss_invalidate_cache();
        }
        if ($plugin_output) { // Let us set up a form
            echo "<h2
            class=\"trigger\">".LBL_ADMIN_PLUGINS_OPTIONS." ".TITLE_SEP." ". $plugin_info['name']. "</h2>\n"
            ."<div id=\"admin_plugin_options\">\n";
            echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";
            echo "<p><input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN
            ."\" value=\"".CST_ADMIN_DOMAIN_PLUGIN_OPTIONS."\" /></p>\n";
            echo $plugin_output;
            echo "<p><input type=\"hidden\" name=\"plugin_name\" value=\"".$plugin_filename."\"/>\n";
            echo "<p><input type=\"hidden\" name=\"". CST_ADMIN_METAACTION
            ."\" value=\"LBL_ADMIN_SUBMIT_CHANGES\"/>\n";
            echo "<input type=\"submit\" name=\"admin_plugin_options_submit_changes\" value=\""
            .LBL_ADMIN_SUBMIT_CHANGES."\" />\n";
            echo "<input type=\"submit\" name=\"admin_plugin_options_cancel_changes\"
            value=\"".LBL_ADMIN_CANCEL."\" /></p></form>\n";
            echo "</div>";
        } else {
            plugins();
        }
    }
}


/**
 * fetches information for the given plugin,
 * which should contain:
 *
 *	/// Name: Url filter
 *	/// Author: Marco Bonetti
 *	/// Description: This plugin will try to make ugly URL links look better
 *	/// Version: 1.0
 *
 */
function getPluginInfo($file) {
    $info = array();
    $path = "../".RSS_PLUGINS_DIR."/$file";
    if (file_exists($path)) {
        $f = @fopen($path,'r');
        $contents = "";
        if ($f) {
            $contents .= fread($f, filesize($path));
            @fclose($f);
        } else {
            $contents = "";
        }

        if ($contents && preg_match_all("/\/\/\/\s?([^:]+):(.*)/",$contents,$matches,PREG_SET_ORDER)) {
            foreach($matches as $match) {
                $key = trim(strtolower($match[1]));
                $val = trim($match[2]);
                if ($key == 'version') {
                    $val=preg_replace('/[^0-9\.]+/','',$val);
                }

                $info[$key] = $val;
            }
        }

        $info['file'] = preg_replace('/\..+$/','',$file);
    }

    return $info;
}

/**
* This function returns an associative array with all the php files that are
* plugins and their plugin info. 
* 
* Following the wordpress model (and code) we search for plugins in the plugins
* directory and each subdirectory 1 level deep.
*/
function getPlugins() {

    $plugin_dir_files = array();
    $rss_plugins = array();
    $plugin_dir = '../' . RSS_PLUGINS_DIR;

    $d = @dir($plugin_dir);
    //Put all the *.php files in the plugin dir and 1 level below into $plugin_dir_files
    while (($file = $d->read()) !== false) {
        if ( $file != "CVS" && (substr($file,0,1) != ".")) {
            if(is_dir($plugin_dir . '/' . $file)) {
                $plugins_subdir = @dir($plugin_dir . '/' . $file);
                if ($plugins_subdir) {
                    while(($subfile = $plugins_subdir->read()) !== false) {
                        if ( preg_match('|^\.+$|', $subfile) ) {
                            continue;
                        }
                        if ( preg_match('|\.php$|', $subfile) ) {
                            $plugin_dir_files[] = "$file/$subfile";
                        }
                    }
                }
            } else {
                if ( preg_match('|\.php$|', $file) ) {
                    $plugin_dir_files[] =  $file;
                }
            }
        }
    }

    // See which of the php files in $plugin_dir_files are really plugins
    foreach($plugin_dir_files as $plugin_dir_file) {
        $info = getPluginInfo($plugin_dir_file);
        // $info will have the filename in it. Does it have anything else?
        if (count($info) > 1) {
            $rss_plugins[$plugin_dir_file] = $info;
        }
    }

    ksort($rss_plugins);

    //return an associative array with the plugin files and their info
    return $rss_plugins;
}

function plugins_check_for_updates() {
    $pluginsxml = array();
    global $pluginsxml;
    $xml = getUrl('http://plugins.gregarius.net/api.php');
    $xml = str_replace("\r", '', $xml);
    $xml = str_replace("\n", '', $xml);

    $xp = xml_parser_create() or rss_error("couldn't create parser");

    xml_set_element_handler($xp, 'plugins_xml_startElement', 'plugins_xml_endElement')
    or rss_error("couldnt set XML handlers");

    xml_parse($xp, $xml, true) or rss_error("failed parsing xml");
    xml_parser_free($xp) or rss_error("failed freeing the parser");
    return $pluginsxml;
}

function plugins_xml_startElement($xp, $element, $attr) {
    global $pluginsxml;

    if ($element == 'PLUGIN' &&
            array_key_exists('PID',$attr) &&
            array_key_exists('URL',$attr) &&
            array_key_exists('VERSION',$attr)) {

        $pluginsxml[$attr['PID']] = array($attr['VERSION'],$attr['URL']);
    }
}

function plugins_xml_endElement($xp, $element) {
    ///global $pluginsxml;
    return;
}


function rss_plugins_redirect_to_admin() {
    rss_redirect("/admin/index.php?" . CST_ADMIN_VIEW . "=" . CST_ADMIN_DOMAIN_PLUGINS);
}

function rss_plugins_redirect_to_plugin_config($filename) {
    rss_redirect("/admin/index.php" . "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_PLUGIN_OPTIONS ."&amp;action=". CST_ADMIN_EDIT_ACTION. "&amp;plugin_name=" . $filename);
}


function rss_plugins_is_submit() {
    return array_key_exists("admin_plugin_options_submit_changes", $_REQUEST);
}

?>
