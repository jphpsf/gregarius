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
# Web page:	 http://gregarius.net/
#
###############################################################################

function tags_admin(){
	return CST_ADMIN_DOMAIN_TAGS;
}

function tags(){

	// Fix for #16: Admin (et al.) should not rely on l10n labels for actions:
	// Look for a meta-action first, which should be the (untranslated) *name* of
	// the (translated) action constant.

	// Fixme: should replace 'action's with a constant
	if (array_key_exists(CST_ADMIN_METAACTION,$_REQUEST)) {
		$__action__ = $_REQUEST[CST_ADMIN_METAACTION];
	} elseif (array_key_exists('action',$_REQUEST)) {
		$__action__ = $_REQUEST['action'];
	} else {
		$__action__ = "";
	}

	if (isset($_REQUEST['id'])) {
		$tid = sanitize($_REQUEST['id'],RSS_SANITIZER_NUMERIC);
	}

	$ret__ = CST_ADMIN_DOMAIN_TAGS;
	switch ($__action__) {

	case CST_ADMIN_EDIT_ACTION:
		tag_edit($tid);
		$ret__ = CST_ADMIN_DOMAIN_NONE;
		break;

	case CST_ADMIN_DELETE_ACTION:
		if (array_key_exists(CST_ADMIN_CONFIRMED,$_POST) && $_POST[CST_ADMIN_CONFIRMED] == LBL_ADMIN_YES) {
			$sql = "delete from " . getTable("tag") ." where id=$tid";
			rss_query($sql);
			$sql = "delete from " . getTable("metatag") ." where tid=$tid";
			rss_query($sql);
			rss_invalidate_cache();
		} elseif (array_key_exists(CST_ADMIN_CONFIRMED,$_REQUEST) && $_REQUEST[CST_ADMIN_CONFIRMED] == LBL_ADMIN_NO) {
			// nop;
		} else {
			list($tname) = rss_fetch_row(rss_query("select tag from " .getTable("tag") ." where id = $tid"));

			echo "<form class=\"box\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
			."<p class=\"error\">";
			printf(LBL_ADMIN_ARE_YOU_SURE,$tname);
			echo "</p>\n"
			."<p><input type=\"submit\" name=\"".CST_ADMIN_CONFIRMED."\" value=\"". LBL_ADMIN_NO ."\"/>\n"
			."<input type=\"submit\" name=\"".CST_ADMIN_CONFIRMED."\" value=\"". LBL_ADMIN_YES ."\"/>\n"
			."<input type=\"hidden\" name=\"id\" value=\"$tid\"/>\n"
			."<input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_TAGS."\"/>\n"
			."<input type=\"hidden\" name=\"action\" value=\"". CST_ADMIN_DELETE_ACTION ."\"/>\n"
			."</p>\n</form>\n";
			$ret__ = CST_ADMIN_DOMAIN_NONE;
		}
		break;
	case CST_ADMIN_SUBMIT_EDIT:
		// TBD
		$new_label = preg_replace(ALLOWED_TAGS_REGEXP,'', $_REQUEST['t_name']);
		// also replace whitespaces
		$new_label = str_replace(' ','',$new_label);
		if (is_numeric($tid) && strlen($new_label) > 0) {
			$res = rss_query("select count(*) as cnt from " . getTable("tag") ." where binary tag='$new_label'");
			list($cnt) = rss_fetch_row($res);
			if ($cnt > 0) {
				rss_error(sprintf(LBL_ADMIN_CANT_RENAME,$new_label), RSS_ERROR_ERROR,true);
				break;
			}
			rss_query("update " .getTable("tag") ." set tag='$new_label' where id=$tid");
			rss_invalidate_cache();
		}
		break;
	default:
		break;
	}

	echo "<h2 class=\"trigger\">".LBL_TAG_TAGS."</h2>\n"
	."<div id=\"admin_tags\" class=\"trigger\">"
	."<table id=\"tagtable\">\n"
	."<tr>\n"
	."\t<th class=\"cntr\">". LBL_TAG_TAGS ."</th>\n"
	."\t<th>". LBL_ADMIN_CHANNELS_HEADING_ACTION ."</th>\n"
	."</tr>\n";

	$sql = "select id,tag from " .getTable("tag") . " order by tag asc";
	$res = rss_query($sql);
	$cntr = 0;
	while (list($id, $tag) = rss_fetch_row($res)) {
		$class_ = (($cntr++ % 2 == 0)?"even":"odd");
		echo "<tr class=\"$class_\">\n"
		."\t<td>$tag</td>\n"
		."\t<td><a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_TAGS
		."&amp;action=". CST_ADMIN_EDIT_ACTION. "&amp;id=$id\">" . LBL_ADMIN_EDIT
		."</a>\n"
		."|<a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_TAGS
		."&amp;action=". CST_ADMIN_DELETE_ACTION ."&amp;id=$id\">" . LBL_ADMIN_DELETE ."</a>\n"
		."</td>\n"
		."</tr>\n";
	}
	echo "</table></div>\n";
}

function tag_edit($tid){
	$sql = "select id, tag from " . getTable("tag") ." where id=$tid";
	$res = rss_query($sql);
	list ($id, $tag) = rss_fetch_row($res);

	echo "<div>\n"
	."<h2>Edit '$tag'</h2>\n"
	."<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\" id=\"tagedit\">\n"

	."<div style=\"inline\"><input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"". CST_ADMIN_DOMAIN_TAGS."\"/>\n"
	."<input type=\"hidden\" name=\"action\" value=\"".CST_ADMIN_SUBMIT_EDIT."\"/>\n"
	."<input type=\"hidden\" name=\"id\" value=\"$tid\"/>\n"
	."<label for=\"t_name\">". LBL_ADMIN_RENAME ."</label>\n"
	."<input type=\"text\" id=\"t_name\" name=\"t_name\" value=\"$tag\"/>\n"
	."<input type=\"submit\" name=\"action_\" value=\"". LBL_ADMIN_SUBMIT_CHANGES ."\"/></div>"
	."</form></div>\n";
}
