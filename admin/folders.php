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
# Web page:  http://gregarius.net/
#
###############################################################################

/*************** Folder management ************/

function folders() {
    echo "<h2 class=\"trigger\">".LBL_ADMIN_FOLDERS."</h2>\n"
    ."<div id=\"admin_folders\" class=\"trigger\">\n";

    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";

    echo "<p><input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_FOLDER."\"/>\n";

    echo "<label for=\"new_folder\">".LBL_ADMIN_FOLDERS_ADD."</label>\n"
    ."<input type=\"text\" id=\"new_folder\" name=\"new_folder\" value=\"\" />"
    ."<input type=\"hidden\" name=\"". CST_ADMIN_METAACTION ."\" value=\"LBL_ADMIN_ADD\"/>\n"
    ."<input type=\"submit\" name=\"action\" value=\"". LBL_ADMIN_ADD ."\"/>\n"
    ."</p></form>\n\n";

    echo "<table id=\"foldertable\">\n"
    ."<tr>\n"
    ."\t<th class=\"cntr\">". LBL_ADMIN_CHANNELS_HEADING_TITLE ."</th>\n";

    if (getConfig('rss.config.absoluteordering')) {
        echo "\t<th>".LBL_ADMIN_CHANNELS_HEADING_MOVE."</th>\n";
    }

    echo "\t<th>". LBL_ADMIN_CHANNELS_HEADING_ACTION ."</th>\n"
    ."</tr>\n";

    $sql = "select id,name from " .getTable("folders");

    if (getConfig('rss.config.absoluteordering')) {
        $sql .=" order by position asc";
    } else {
        $sql .=" order by name asc";
    }

    $res = rss_query($sql);
    $cntr = 0;
    while (list($id, $name) = rss_fetch_row($res)) {

        $name = $name == ''? LBL_HOME_FOLDER:$name;

        $class_ = (($cntr++ % 2 == 0)?"even":"odd");

        echo "<tr class=\"$class_\">\n"
        ."\t<td>$name</td>\n";

        if (getConfig('rss.config.absoluteordering')) {
            echo "\t<td>";

            if ($id > 0) {
                if ($cntr > 2) {
                    echo "<a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_FOLDER
                    ."&amp;action=". CST_ADMIN_MOVE_UP_ACTION. "&amp;fid=$id\">". LBL_ADMIN_MOVE_UP
                    ."</a>&nbsp;-&nbsp;";
                }
                echo "<a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_FOLDER
                ."&amp;action=". CST_ADMIN_MOVE_DOWN_ACTION ."&amp;fid=$id\">".LBL_ADMIN_MOVE_DOWN ."</a>";
            } else {
                echo "&nbsp;";
            }

            echo "</td>\n";
        }
        echo "\t<td><a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_FOLDER
        ."&amp;action=". CST_ADMIN_EDIT_ACTION. "&amp;fid=$id\">" . LBL_ADMIN_EDIT
        ."</a>";
        if ($id > 0) {
            echo "|<a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_FOLDER
            ."&amp;action=". CST_ADMIN_DELETE_ACTION ."&amp;fid=$id\">" . LBL_ADMIN_DELETE ."</a>";
        }
        echo "</td>\n"
        ."</tr>\n";

    }
    echo "</table>";

    echo "</div>\n";
}

function folder_edit($fid) {

    $sql = "select id, name from " . getTable("folders") ." where id=$fid";
    $res = rss_query($sql);
    list ($id, $name) = rss_fetch_row($res);

    echo "<div>\n";
    echo "\n\n<h2>Edit '$name'</h2>\n";
    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\" id=\"folderedit\">\n"


    ."<p><input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"". CST_ADMIN_DOMAIN_FOLDER."\"/>\n"
    ."<input type=\"hidden\" name=\"action\" value=\"".CST_ADMIN_SUBMIT_EDIT."\"/>\n"
    ."<input type=\"hidden\" name=\"fid\" value=\"$id\"/>\n"

    // Item name
    ."<label for=\"f_name\">". LBL_ADMIN_FOLDER_NAME ."</label>\n"
    ."<input type=\"text\" id=\"f_name\" name=\"f_name\" value=\"$name\"/></p>";

    echo "<p><input type=\"submit\" name=\"action_\" value=\"". LBL_ADMIN_SUBMIT_CHANGES ."\"/></p>"
    ."</form></div>\n";

}

function folder_combo($name, $selected = -1) {
    echo "\n<select name=\"$name\" id=\"$name\">\n";
    if (getConfig('rss.config.absoluteordering')) {
        $sql = " order by position asc";
    } else {
        $sql = " order by name asc";
    }
    $res = rss_query("select id, name from " .getTable("folders") . $sql);
    while (list($id, $name) = rss_fetch_row($res)) {
        echo "\t<option value=\"$id\""
        .($selected > -1 && $selected == $id ? " selected=\"selected\"":"")
        .">" .	(($name == "")?LBL_HOME_FOLDER:$name)  ."</option>\n";
    }
    echo "</select>\n";
}



function folder_admin() {

    // Fix for #16: Admin (et al.) should not rely on l10n labels for actions:
    // Look for a meta-action first, which should be the (untranslated) *name* of
    // the (translated) action constant.

    // Fixme: should replace 'action's with a constant
    if (array_key_exists(CST_ADMIN_METAACTION,$_REQUEST)) {
        $__action__ = $_REQUEST[CST_ADMIN_METAACTION];
    }
    elseif (array_key_exists('action',$_REQUEST)) {
        $__action__ = $_REQUEST['action'];
    }
    else {
        $__action__ = "";
    }

    $ret__ = CST_ADMIN_DOMAIN_FOLDER;
    switch ($__action__) {

    case CST_ADMIN_EDIT_ACTION:
        folder_edit($_REQUEST['fid']);
        $ret__ = CST_ADMIN_DOMAIN_NONE;
        break;

    case CST_ADMIN_DELETE_ACTION:
        $id = $_REQUEST['fid'];
        assert(is_numeric($id));

        if ($id == 0) {
            rss_error(LBL_ADMIN_ERROR_CANT_DELETE_HOME_FOLDER, RSS_ERROR_ERROR,true);
            break;
        }

        if (array_key_exists(CST_ADMIN_CONFIRMED,$_POST) && $_POST[CST_ADMIN_CONFIRMED] == LBL_ADMIN_YES) {
            $sql = "delete from " . getTable("folders") ." where id=$id";
            rss_query($sql);
            $sql = "update " . getTable("channels") ." set parent=" . getRootFolder() . " where parent=$id";
            rss_query($sql);
        }
        elseif (array_key_exists(CST_ADMIN_CONFIRMED,$_REQUEST) && $_REQUEST[CST_ADMIN_CONFIRMED] == LBL_ADMIN_NO) {
            // nop;
        }
        else {
            list($fname) = rss_fetch_row(rss_query("select name from " .getTable("folders") ." where id = $id"));

            echo "<form class=\"box\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
            ."<p class=\"error\">";
            printf(LBL_ADMIN_ARE_YOU_SURE,$fname);
            echo "</p>\n"
            ."<p><input type=\"submit\" name=\"".CST_ADMIN_CONFIRMED."\" value=\"". LBL_ADMIN_NO ."\"/>\n"
            ."<input type=\"submit\" name=\"".CST_ADMIN_CONFIRMED."\" value=\"". LBL_ADMIN_YES ."\"/>\n"
            ."<input type=\"hidden\" name=\"fid\" value=\"$id\"/>\n"
            ."<input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_FOLDER."\"/>\n"
            ."<input type=\"hidden\" name=\"action\" value=\"". CST_ADMIN_DELETE_ACTION ."\"/>\n"
            ."</p>\n</form>\n";
            $ret__ = CST_ADMIN_DOMAIN_NONE;
        }
        break;

    case CST_ADMIN_SUBMIT_EDIT:
        $id = $_REQUEST['fid'];

        $new_label = rss_real_escape_string($_REQUEST['f_name']);
        if (is_numeric($id) && strlen($new_label) > 0) {

            $res = rss_query("select count(*) as cnt from " . getTable("folders") ." where binary name='$new_label'");
            list($cnt) = rss_fetch_row($res);
            if ($cnt > 0) {
                rss_error(sprintf(LBL_ADMIN_CANT_RENAME,$new_label), RSS_ERROR_ERROR,true);
                break;
            }
            rss_query("update " .getTable("folders") ." set name='$new_label' where id=$id");
        }
        break;

    case LBL_ADMIN_ADD:
    case 'LBL_ADMIN_ADD':
        $label=$_REQUEST['new_folder'];
        assert(strlen($label) > 0);
        create_folder($label);
        break;

    case CST_ADMIN_MOVE_UP_ACTION:
    case CST_ADMIN_MOVE_DOWN_ACTION:
        $id = $_REQUEST['fid'];

        if ($id == 0) {
            return;
        }

        $res = rss_query("select position from " .getTable("folders") ." where id=$id");
        list($position) = rss_fetch_row($res);

        $sql = "select id, position from " .getTable("folders")
               ." where	id != $id order by abs($position-position) limit 2";

        $res = rss_query($sql);

        // Let's look for a lower/higher position than the one we got.
        $switch_with_position=$position;

        while (list($oid,$oposition) = rss_fetch_row($res)) {
            if (
                // found none yet?
                ($switch_with_position == $position) &&
                (
                    // move up: we look for a lower position
                    ($_REQUEST['action'] == CST_ADMIN_MOVE_UP_ACTION && $oposition < $switch_with_position)
                    ||
                    // move up: we look for a higher position
                    ($_REQUEST['action'] == CST_ADMIN_MOVE_DOWN_ACTION && $oposition > $switch_with_position)
                )
            ) {
                $switch_with_position = $oposition;
                $switch_with_id = $oid;
            }
        }

        // right, lets!
        if ($switch_with_position != $position) {
            rss_query( "update " . getTable("folders") ." set position = $switch_with_position where id=$id" );
            rss_query( "update " . getTable("folders") ." set position = $position where id=$switch_with_id" );
        }
        break;

    default:
        break;
    }
    return $ret__;
}

/**
 * Creates a folder with the given name. Does some sanity check,
 * creates the folder, then returns the 
 */
function create_folder($label, $complainonerror=true) {
    $res = rss_query ("select count(*) from "
                      .getTable("folders") ." where name='"
                      .rss_real_escape_string($label). "'");

    list($exists) = rss_fetch_row($res);

    if ($exists > 0 && $complainonerror) {
        rss_error(sprintf(LBL_ADMIN_ERROR_CANT_CREATE, $label), RSS_ERROR_ERROR,true);
        return;
    }
    elseif ($exists == 0) {
        $res = rss_query("select 1+max(position) as np from " . getTable("folders"));
        list($np) = rss_fetch_row($res);

        if (!$np) {
            $np = "0";
        }
        rss_query("insert into " .getTable("folders") ." (name,position) values ('" . rss_real_escape_string($label) ."', $np)");
    }

    list($fid) = rss_fetch_row( rss_query("select id from " .getTable("folders") ." where name='". rss_real_escape_string($label) ."'"));
    return $fid;
}

?>
