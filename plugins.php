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

require_once('init.php');

/**
 * (private) 
 * Returns a reference to the hooks array, which holds
 * references of installed plugin's callback functions
 */
function & __getHooksArray() {
    static $__rss_hooks;
    if ($__rss_hooks == null) {
        $__rss_hooks = array();
    }
    return ($__rss_hooks);
}

/**
 * Allows a plugin to register itself for the 
 * given hook
 */
function rss_set_hook($hook,$fnct) {
    $hooks =& __getHooksArray();
    if (array_key_exists($hook, $hooks)) {
        $hooks[$hook][] = $fnct;
    } else {
        $hooks[$hook]=array($fnct);
    }
}

/**
 * Performs callbacks for the given hook,
 * based on the plugins registered functions
 */
function rss_plugin_hook($hook, $data) {
    $hooks =& __getHooksArray();
    if (array_key_exists($hook, $hooks)) {
        foreach($hooks[$hook] as $fnct) {
            if (function_exists($fnct)) {
                _pf("calling plugin func for $hook ...");
                $data = call_user_func($fnct,$data);
                _pf("done");
            }
        }
    }
    return $data;
}


/***** Plugin options ******/

/**
 * Wrapper functions for plugins
 */
function rss_plugins_add_option($key, $value, $type = "string", $default = "", $desc= "", $export = NULL) {
    if (!$key || !$value) {
        return false;
    }
    $pKey = "plugins." . rss_real_escape_string($key);


    if (is_array($value) || $type == 'array') {
        $value = str_replace("'","\'",serialize($value));
    }
    // first check for duplicates
    $res = rss_query("select value_,default_,type_ from " .getTable('config') . " where key_='$pKey'");
    if(!rss_num_rows($res)) { // Then insert the config value
        $value = rss_real_escape_string($value);

        $default = $default? $default: $value;
        $ret =  rss_query("insert into " . getTable("config")
                          . " (key_,value_,default_,type_,desc_,export_) VALUES ("
                          . "'$pKey','$value','$default','$type','$desc','$export')" );
    } else { // the key exists, so update the option
        $ret = rss_plugins_update_option($key, $value, "string" , $default, $desc, $export);
    }
    configInvalidate();
    return $ret;


}

function rss_plugins_update_option($key, $value, $type = "string", $default = "", $desc= "", $export = NULL) {
    $pKey = "plugins." . rss_real_escape_string($key);
    if (is_array($value) || $type == 'array') {
        $value = str_replace("'","\'",serialize($value));
    }
    $value = rss_real_escape_string($value);
    $ret=  rss_query("update " . getTable("config") . " set value_='" .
                     $value . "' where key_ ='$pKey'");
    configInvalidate();
    return $ret;
}

function rss_plugins_get_option($key) {
    if (!$key) {
        return false;
    }
    return getConfig("plugins.".rss_real_escape_string($key));

}

function rss_plugins_delete_option($key) {
    if (!$key) {
        return;
    }
    $pKey = "plugins." . rss_real_escape_string($key);
    $ret = rss_query("delete from " . getTable("config") . " where key_='$pKey'");
    configInvalidate();
    return $ret;

}


/**
 * loads the active plugins from the config, instantiates
 * them
 */
foreach(getConfig('rss.config.plugins') as $pf) {
    if (defined('RSS_FILE_LOCATION')) {
        $prefix = "../";
    } else {
        $prefix = "";
    }
    if (file_exists($prefix . RSS_PLUGINS_DIR.'/'.$pf)) {
        require_once($prefix . RSS_PLUGINS_DIR."/$pf");
    }
}

/*
 * autoload plugins specific to a theme without the
 * need to add them to config
 * all plugins def must be inside a unique file called plugins.php that
 * do all the works
 */
$mytheme = getActualTheme();
/*
 * If theme is set in the request then let it overide everything. 
 * Then it can have a plugins.php page. 
 */

$mythemeplugin=RSS_THEME_DIR."/$mytheme/plugins.php";
if (file_exists($mythemeplugin))
    require_once($mythemeplugin);

?>
