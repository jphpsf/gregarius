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
    if (!$key) {
        return false;
    }
    $pKey = "plugins." . rss_real_escape_string($key);

    if (is_array($value) || $type == 'array') {
        $value = str_replace("'","\'",serialize($value));
    }
    $value = rss_real_escape_string($value);
    $default = $default? $default: $value;
    $ret =  rss_query("replace into " . getTable("config")
                      . " (key_,value_,default_,type_,desc_,export_) VALUES ("
                      . "'$pKey','$value','$default','$type','$desc','$export')" );
    configInvalidate();
    return $ret;

}

function rss_plugins_get_option($key) {
    if (!$key) {
        return false;
    }
    return getConfig("plugins.".rss_real_escape_string($key), false);

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

function rss_plugins_set_item_state($itemId, $bit_mask, $set
                                    , $sqlwhere = "", $entire_db = false) {
    $retvalue = false;
    if($itemId || $entire_db) { // Check to see if itemId is set or if we are allowed to fsck up the entire db
        // the bitmask has a one in the spot (field(s)) we want to change.
        if($set
          ) { // Set the value to the field to 1
            $sql = "update " .getTable("item") ." set unread = unread | ".  $bit_mask;
        }
        else { // set the value of the field to 0
            $sql = "update " .getTable("item") ." set unread = unread & ". ~ $bit_mask;
        }

        if($itemId) {
            if (is_array($itemId)) {
                $sql .= " where id  in (" . implode(',',$itemId) .")";
            } else { // assume it is a number or a string
                $sql .= " where id=" .$itemId;
            }
        } else {
            $sql .= " where 1";
        }

        if($sqlwhere) {
            $sql .= " and " . $sqlwhere;
        }

        $retvalue =  rss_query($sql);
        rss_invalidate_cache();

    } else {
        $retvalue = false;
    }
    return $retvalue;
}

function rss_plugins_get_plugins_http_path() {
    //returns http://example.com/rss/plugins/
    return guessTransportProto().$_SERVER['HTTP_HOST'] . getPath() . RSS_PLUGINS_DIR . "/";
}

/**
 * loads the active plugins from the config, instantiates
 * them
 */
foreach(getConfig('rss.config.plugins') as $pf) {
    if (defined('RSS_FILE_LOCATION')) {
        $prefix = GREGARIUS_HOME;
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
