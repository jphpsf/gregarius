<?php

function plugins_admin() {
    return CST_ADMIN_DOMAIN_PLUGINS;
}


function plugins() {
    if (isset($_REQUEST[CST_ADMIN_METAACTION]) && $_REQUEST[CST_ADMIN_METAACTION] == 'LBL_ADMIN_SUBMIT_CHANGES') {
        $active_plugins=array();
        foreach($_REQUEST as $rkey=>$rentry) {
            if (preg_match('/_gregarius_plugin.([a-zA-Z0-9_\/\-]+).php/',$rkey,$matches)) {
                $active_plugins[] = ($matches[1] .".php");
            }
        }
        $value = serialize($active_plugins);
        $sql = "update " . getTable('config') . " set value_='$value' where key_='rss.config.plugins'";
        rss_query($sql);
    } else {
        $active_plugins= getConfig('rss.config.plugins');
    }

    $doUpdates = false;
    $updates = array();
    if  (isset($_REQUEST[CST_ADMIN_METAACTION]) && $_REQUEST[CST_ADMIN_METAACTION] == 'LBL_ADMIN_PLUGINS_CHECK_UPDATES') {
        $updates = plugins_check_for_updates();
        $doUpdates = true;
    }

    echo "<h2 class=\"trigger\">".LBL_ADMIN_PLUGINS."</h2>\n"
    ."<div id=\"admin_plugins\">\n";

    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";
    echo "<p><input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_PLUGINS."\" /></p>\n";
    echo "\n<table id=\"plugintable\">\n<tr>\n"
    ."<th>".LBL_ADMIN_PLUGINS_HEADING_ACTION."</th>\n"
    ."<th>".LBL_ADMIN_PLUGINS_HEADING_NAME."</th>\n"
    ."<th>".LBL_ADMIN_PLUGINS_HEADING_VERSION."</th>\n"
    ."<th>".LBL_ADMIN_PLUGINS_HEADING_AUTHOR."</th>\n"
    ."<th>".LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION."</th>\n";
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
                if (array_key_exists($info['file'],$updates)) {
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
                echo "<td><label for=\"_gregarius_plugin_$entry\">".(array_key_exists('name',$info)?$info['name']:"&nbsp"). "</label></td>\n";
                echo "<td class=\"cntr\">"	.(array_key_exists('version',$info)?$info['version']:"&nbsp"). "</td>\n";
                echo "<td>"	.(array_key_exists('author',$info)?$info['author']:"&nbsp"). "</td>\n";
                echo "<td>"	.(array_key_exists('description',$info)?$info['description']:"&nbsp"). "</td>\n";

                if ($doUpdates && $updateDl) {
                    echo "<td class=\"cntr\">";
                    echo "<a href=\"$updateDl\">$lastV</a>";
                    echo "</td>";
                } else {
                    echo "<td>&nbsp;</td>";
                }
                echo "</tr>\n";
            }
        }
    }
    echo "</table>\n";
    echo "<p><input type=\"hidden\" name=\"". CST_ADMIN_METAACTION ."\" value=\"LBL_ADMIN_SUBMIT_CHANGES\"/>\n"
    ."<input type=\"submit\" value=\"".LBL_ADMIN_SUBMIT_CHANGES."\" /></p>\n";
    echo "\n</form>";

    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";
    echo "<p><input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_PLUGINS."\" />\n";
    echo "<input type=\"hidden\" name=\"". CST_ADMIN_METAACTION ."\" value=\"LBL_ADMIN_PLUGINS_CHECK_UPDATES\"/>\n";
    echo "<input type=\"submit\" value=\"".LBL_ADMIN_CHECK_FOR_UPDATES."\" /></p></form>\n";


    echo "</div>";
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

?>
