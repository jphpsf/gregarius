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

define('ALL_CHANNELS_ID', -1);

function rss_toolkit_folders_combo($name, $selected = -1) { 
    $ret = "\n<select name=\"$name\" id=\"$name\">\n";
    if (getConfig('rss.config.absoluteordering')) {
        $sql = " order by position asc";
    } else {
        $sql = " order by name asc";
    }
    $res = rss_query("select id, name from " .getTable("folders") . $sql);
    while (list($id, $name) = rss_fetch_row($res)) {
        $ret .= "\t<option value=\"$id\""
        .($selected > -1 && $selected == $id ? " selected=\"selected\"":"")
        .">" .  (($name == "")?LBL_HOME_FOLDER:$name)  ."</option>\n";
    }
    $ret .= "</select>\n";

		return $ret;
}

function rss_toolkit_channels_combo($id, $all_channels_id = ALL_CHANNELS_ID, $selected = 0, $showDeprecated = false) {
  $ret = "\t\t<select name=\"$id\" id=\"$id\">\n"
      ."\t\t\t<option value=\"". $all_channels_id ."\""
      .(0 == $selected?" selected=\"selected\"":"")
    .">" . LBL_ALL  . "</option>\n";

  $sql = "select "
         ." c.id, c.title, f.name, f.id  "
         ." from " . getTable("channels") ." c " 
         ." inner join " . getTable("folders"). " f "
         ."   on f.id = c.parent ";

  if (hidePrivate()) {
    $sql .=" and not(c.mode & " . RSS_MODE_PRIVATE_STATE .") ";
  }

  if(false == $showDeprecated) {
	  $sql .=" and not(c.mode & " . RSS_MODE_DELETED_STATE .") ";
	}

  $sql .= " order by "
       .((getConfig('rss.config.absoluteordering'))?"f.position asc, c.position asc":"f.name asc, c.title asc");

  $res = rss_query($sql);
  $prev_parent = -1;

  while (list ($id_, $title_, $parent_, $parent_id_) = rss_fetch_row($res)) {
    if ($prev_parent != $parent_id_) {
      if ($prev_parent > -1) {
        $ret .="\t\t\t</optgroup>\n";
      }
      if ($parent_ == "") {
        $parent_ = LBL_HOME_FOLDER;
      }
      $ret .= "\t\t\t<optgroup label=\"$parent_ /\">\n";
      $prev_parent = $parent_id_;
    }

    if (strlen($title_) > 25) {
      $title_ = substr($title_, 0, 22)."...";
    }

    $ret .= "\t\t\t\t<option value=\"$id_\"".($selected == $id_ ? " selected=\"selected\"" : "").">$title_</option>\n";
  }

    if ($prev_parent != 0) {
        $ret .= "\t\t\t</optgroup>\n";
    }

    $ret .= "\t\t</select>\n";


  return $ret;
}

?>
