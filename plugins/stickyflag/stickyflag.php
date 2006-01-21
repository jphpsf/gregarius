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


/// Name: StickyFlag
/// Author: Chris J. Friesen
/// Description: Adds Sticky item and Flagged item functionality.
/// Version: 1.0
/// Configuration: __StickyFlag_Config

define ('STICKYFLAG_CONFIG_OPTIONS', 'stickyflag.options');
define ('STICKYFLAG_CONFIG_FLAGICON', 'stickyflag.flagicon');

define ('STICKYFLAG_ENABLE_STICKY_MENU', 0x01);
define ('STICKYFLAG_ENABLE_FLAG_MENU', 0x02);
define ('STICKYFLAG_ENABLE_STICKY_SHORTCUT', 0x04);
define ('STICKYFLAG_ENABLE_FLAG_SHORTCUT', 0x08);

define ('STICKYFLAG_FLAG_BLUE', 0);
define ('STICKYFLAG_FLAG_GREEN', 1);
define ('STICKYFLAG_FLAG_ORANGE', 2);
define ('STICKYFLAG_FLAG_RED', 3);
define ('STICKYFLAG_FLAG_WHITE', 4);

define ('STICKYFLAG_EXT_FILES', getPath() . RSS_PLUGINS_DIR . "/stickyflag");
define ('STICKYFLAG_ICON_STICKY', STICKYFLAG_EXT_FILES . '/sticky.png');
define ('STICKYFLAG_ICON_NOSTICKY', STICKYFLAG_EXT_FILES . '/nosticky.png');
define ('STICKYFLAG_ICON_FLAG_BLUE', STICKYFLAG_EXT_FILES . '/flag_blue.png');
define ('STICKYFLAG_ICON_FLAG_GREEN', STICKYFLAG_EXT_FILES . '/flag_green.png');
define ('STICKYFLAG_ICON_FLAG_ORANGE', STICKYFLAG_EXT_FILES . '/flag_orange.png');
define ('STICKYFLAG_ICON_FLAG_RED', STICKYFLAG_EXT_FILES . '/flag_red.png');
define ('STICKYFLAG_ICON_FLAG_WHITE', STICKYFLAG_EXT_FILES . '/flag_white.png');
define ('STICKYFLAG_ICON_NOFLAG', STICKYFLAG_EXT_FILES . '/noflag.png');

function __stickyflag_Config() {
    $options    = rss_plugins_get_option(STICKYFLAG_CONFIG_OPTIONS);
    $flag_icon  = rss_plugins_get_option(STICKYFLAG_CONFIG_FLAGICON);

    if(null == $options) {
        $options = 0;
    }

    if(null == $flag_icon) {
        $flag_option = 0;
    }

    if(rss_plugins_is_submit()) {
        $options = 0;
        if(!empty($_REQUEST['ui_sm'])) {
            $options |= STICKYFLAG_ENABLE_STICKY_MENU;
        }
        if(!empty($_REQUEST['ui_fm'])) {
            $options |= STICKYFLAG_ENABLE_FLAG_MENU;
        }
        if(!empty($_REQUEST['ui_ss'])) {
            $options |= STICKYFLAG_ENABLE_STICKY_SHORTCUT;
        }
        if(!empty($_REQUEST['ui_fs'])) {
            $options |= STICKYFLAG_ENABLE_FLAG_SHORTCUT;
        }

        rss_plugins_add_option(STICKYFLAG_CONFIG_OPTIONS, $options, 'num');
        rss_plugins_add_option(STICKYFLAG_CONFIG_FLAGICON, $_REQUEST['flag_icon'], 'num');
        return;
    }

    print ("<fieldset>\n");
    print ("  <legend>" . LBL_STICKY . " " . LBL_ITEMS . "</legend>\n");
    print ("   <p><input id='ui_sm' type='checkbox' value='1' name='ui_sm'" . ($options & STICKYFLAG_ENABLE_STICKY_MENU ? "checked='1'" : "") . ">" 
               . "<label for='ui_sm'>Show Menu Item</label></br></p>\n");
    print ("   <p><input id='ui_ss' type='checkbox' value='1' name='ui_ss'" . ($options & STICKYFLAG_ENABLE_STICKY_SHORTCUT ? "checked='1'" : "") . ">" 
               . "<label for='ui_ss'>Show Shortcut</label></br></p>\n");
    print ("   <p>&nbsp;</p>\n");
    print ("</fieldset>\n");
    print ("<fieldset>\n");
    print ("  <legend>" . LBL_FLAG . " " . LBL_ITEMS . "</legend>\n");
    print ("    <p><input id='ui_fm' type='checkbox' value='1' name='ui_fm'" . ($options & STICKYFLAG_ENABLE_FLAG_MENU ? "checked='1'" : "") . ">"
                . "<label for='ui_fm'>Show Menu Item</label></br></p>\n");
    print ("    <p><input id='ui_fs' type='checkbox' value='1' name='ui_fs'" . ($options & STICKYFLAG_ENABLE_FLAG_SHORTCUT ? "checked='1'" : "") . ">"
                . "<label for='ui_fs'>Show Shortcut</label></br></p>\n");
    print ("    <p><input id='flag_icon' name='flag_icon' type='radio' value='" . STICKYFLAG_FLAG_BLUE . "'" . (STICKYFLAG_FLAG_BLUE == $flag_icon ? " checked='1'" : "") . ">"
                . "<img src='" . STICKYFLAG_ICON_FLAG_BLUE . "' alt='blue'>\n"
                . "<input id='flag_icon' name='flag_icon' type='radio' value='" . STICKYFLAG_FLAG_GREEN . "'" . (STICKYFLAG_FLAG_GREEN == $flag_icon ? " checked='1'" : "") . ">"
                . "<img src='" . STICKYFLAG_ICON_FLAG_GREEN . "' alt='green'>\n"
                . "<input id='flag_icon' name='flag_icon' type='radio' value='" . STICKYFLAG_FLAG_ORANGE . "'" . (STICKYFLAG_FLAG_ORANGE == $flag_icon ? " checked='1'" : "") . ">"
                . "<img src='" . STICKYFLAG_ICON_FLAG_ORANGE . "' alt='orange'>\n"
                . "<input id='flag_icon' name='flag_icon' type='radio' value='" . STICKYFLAG_FLAG_RED . "'" . (STICKYFLAG_FLAG_RED == $flag_icon ? " checked='1'" : "") . ">"
                . "<img src='" . STICKYFLAG_ICON_FLAG_RED . "' alt='red'>\n"
                . "<input id='flag_icon' name='flag_icon' type='radio' value='" . STICKYFLAG_FLAG_WHITE . "'" . (STICKYFLAG_FLAG_WHITE == $flag_icon ? " checked='1'" : "") . ">"
                . "<img src='" . STICKYFLAG_ICON_FLAG_WHITE . "' alt='white'></p>\n");
    print ("</fieldset>\n");
}

function __stickyflag_GetFlagIcon() {
    $icon = rss_plugins_get_option(STICKYFLAG_CONFIG_FLAGICON);

    if(null == $icon) {
        $icon = 0;
    }

    switch($icon) {
        case STICKYFLAG_FLAG_BLUE   : $ret = STICKYFLAG_ICON_FLAG_BLUE;
                                      break;
        case STICKYFLAG_FLAG_GREEN  : $ret = STICKYFLAG_ICON_FLAG_GREEN;
                                      break;
        case STICKYFLAG_FLAG_ORANGE : $ret = STICKYFLAG_ICON_FLAG_ORANGE;
                                      break;
        case STICKYFLAG_FLAG_RED    : $ret = STICKYFLAG_ICON_FLAG_RED;
                                      break;
        case STICKYFLAG_FLAG_WHITE  : $ret = STICKYFLAG_ICON_FLAG_WHITE;
                                      break;
    }

    return $ret;
}

function __stickyflag_AddButtons(){
    $usemodrewrite = getConfig('rss.output.usemodrewrite');
    $options       = rss_plugins_get_option(STICKYFLAG_CONFIG_OPTIONS);
   
    if(null == $options) {
        return;
    }
 
    if($options & STICKYFLAG_ENABLE_STICKY_MENU) {
        if (true == $usemodrewrite) {
            $url = getPath() . "state/" . RSS_STATE_STICKY . "";
        } else {
            $url = getPath() . "state.php?state=" . RSS_STATE_STICKY . "";
        }

        $GLOBALS['rss']->nav->addNavItem($url,'Stick<span>y</span>');
    }

    if($options & STICKYFLAG_ENABLE_FLAG_MENU) {
        if (true == $usemodrewrite) {
            $url = getPath() . "state/" . RSS_STATE_FLAG . "";
        } else {
            $url = getPath() . "state.php?state=" . RSS_STATE_FLAG . "";
        }

        $GLOBALS['rss']->nav->addNavItem($url, '<span>F</span>lag');
    }
}

function __stickyflag_BeforeTitle($id){
    $ret = "";
    $flags = rss_item_flags();

    if(! hidePrivate()) {
        $options = rss_plugins_get_option(STICKYFLAG_CONFIG_OPTIONS);

        if(null == $options) {
            return;
        }

        if($options & STICKYFLAG_ENABLE_STICKY_SHORTCUT) {
            if($flags & RSS_MODE_STICKY_STATE) {
                $ret .= "<a id='ms" . $id . "' href='#' onclick='_stickyflag_sticky(" . $id . ", " . $flags . "); return false;' title='Make Un-Sticky'>"
                     .  "<img id='sticky_img_" . $id . "' src='" . STICKYFLAG_ICON_STICKY . "' alt='S' /></a>&nbsp;";
            } else {
                $ret .= "<a id='ms" . $id . "' href='#' onclick='_stickyflag_sticky(" . $id . ", " . $flags . "); return false;' title='Make Sticky'>"
                     .  "<img id='sticky_img_" . $id . "' src='" . STICKYFLAG_ICON_NOSTICKY . "' alt='S' /></a>&nbsp;";
            }
        }

        if($options & STICKYFLAG_ENABLE_FLAG_SHORTCUT) {
            if($flags & RSS_MODE_FLAG_STATE) {
                $ret .= "<a id='mf" . $id . "' href='#' onclick='_stickyflag_flag(" . $id . ", " . $flags . "); return false;' title='Un-Flag Item'>"
                     .  "<img id='flag_img_" . $id . "' src='" . __stickyflag_GetFlagIcon() . "' alt='F' /></a>&nbsp;";
            } else {
                $ret .= "<a id='mf" . $id . "' href='#' onclick='_stickyflag_flag(" . $id . ", " . $flags . "); return false;' title='Flag Item'>"
                     .  "<img id='flag_img_" . $id . "' src='" . STICKYFLAG_ICON_NOFLAG . "' alt='F' /></a>&nbsp;";
            }
        }

        if(!empty($ret)) {
            print($ret);
        }
    }
    return $id;
}

function __stickyflag_js(){
    $options = rss_plugins_get_option(STICKYFLAG_CONFIG_OPTIONS);

    if(null == $options) {
        return;
    }

    $ret = "";

    if($options & STICKYFLAG_ENABLE_STICKY_SHORTCUT) {
        $ret .= "function _stickyflag_sticky(id, state) {\n"
             .  "  var sticky = " . RSS_MODE_STICKY_STATE . ";\n"
             .  "  var img    = document.getElementById('sticky_img_'+id);\n"
             .  "\n"
             .  "  if(!document.states[id]) {\n"
             .  "    document.states[id] = state;\n"
             .  "  }\n"
             .  "\n"
             .  "  if(document.states[id] & sticky) {\n"
             .  "    document.states[id] ^= sticky;\n"
             .  "    img.src = '" . STICKYFLAG_ICON_NOSTICKY . "';\n"
             .  "  } else {\n"
             .  "    document.states[id] |= sticky;\n"
             .  "    img.src = '" . STICKYFLAG_ICON_STICKY . "';\n"
             .  "  }\n"
             .  "\n"
             .  "  setState(id, document.states[id]);\n"
             .  "}\n";
    }

    if($options & STICKYFLAG_ENABLE_FLAG_SHORTCUT) {
        $ret .= "function _stickyflag_flag(id, state) {\n"
             .  "  var flag   = " . RSS_MODE_FLAG_STATE . ";\n"
             .  "  var img    = document.getElementById('flag_img_'+id);\n"
             .  "\n"
             .  "  if(!document.states[id]) {\n"
             .  "    document.states[id] = state;\n"
             .  "  }\n"
             .  "\n"
             .  "  if(document.states[id] & flag) {\n"
             .  "    document.states[id] ^= flag;\n"
             .  "    img.src = '" . STICKYFLAG_ICON_NOFLAG . "';\n"
             .  "  } else {\n"
             .  "    document.states[id] |= flag;\n"
             .  "    img.src = '" . __stickyflag_GetFlagIcon() . "';\n"
             .  "  }\n"
             .  "\n"
             .  "  setState(id, document.states[id]);\n"
             .  "}\n";
    }

    if(!empty($ret)) {
        print ($ret);
    }
}

function __stickyflag_OnOk(){
    $options = rss_plugins_get_option(STICKYFLAG_CONFIG_OPTIONS);

    if(null == $options) {
        return;
    }

    $ret = "";

    if($options & STICKYFLAG_ENABLE_STICKY_SHORTCUT) {
        $ret .= "if((sfs = document.getElementById(\'sf_ID_s\')) && sfs.checked) {"
             .  "  document.getElementById(\'sticky_img__ID_\').src = \'" . STICKYFLAG_ICON_STICKY . "\';"
             .  "} else {"
             .  "  document.getElementById(\'sticky_img__ID_\').src = \'" . STICKYFLAG_ICON_NOSTICKY . "\';"
             .  "}";
    }

    if($options & STICKYFLAG_ENABLE_FLAG_SHORTCUT) {
        $ret .= "if((sff = document.getElementById(\'sf_ID_f\')) && sff.checked) {"
             .  "  document.getElementById(\'flag_img__ID_\').src = \'" . __stickyflag_GetFlagIcon() . "\';"
             .  "} else {"
             .  "  document.getElementById(\'flag_img__ID_\').src = \'" . STICKYFLAG_ICON_NOFLAG . "\';"
             .  "}";
    }

    return $ret;
}

rss_set_hook("rss.plugins.navelements", "__stickyflag_AddButtons");
rss_set_hook("rss.plugins.items.beforetitle", "__stickyflag_BeforeTitle");
rss_set_hook("rss.plugins.ajax.extrajs.private", "__stickyflag_js");
rss_set_hook("rss.plugins.ajax.admindlg.onok", "__stickyflag_OnOk");
?>
