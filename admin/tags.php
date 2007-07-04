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
		if (array_key_exists(CST_ADMIN_CONFIRMED,$_POST) && $_POST[CST_ADMIN_CONFIRMED] == __('Yes')) {
			$sql = "delete from " . getTable("tag") ." where id=$tid";
			rss_query($sql);
			$sql = "delete from " . getTable("metatag") ." where tid=$tid";
			rss_query($sql);
			rss_invalidate_cache();
		} elseif (array_key_exists(CST_ADMIN_CONFIRMED,$_REQUEST) && $_REQUEST[CST_ADMIN_CONFIRMED] == __('No')) {
			// nop;
		} elseif (array_key_exists('me_delete', $_REQUEST)) {
			if(array_key_exists('me_do_delete', $_REQUEST) && "1" == $_REQUEST['me_do_delete']) {
				$ids = array();
				foreach($_REQUEST as $key => $val) {
					if(preg_match('/^tcb([0-9]+)$/', $key, $match)) {
						if(($id = (int) $_REQUEST[$key]) > 0) {
							$ids[] = $id;
						}
					}
				}

				if(count($ids) > 0)  {
					$sql = "delete from " . getTable("tag") . " where id in (".implode(',', $ids) . ")";
					rss_query($sql);
					$sql = "delete from " . getTable("metatag") . " where tid in (".implode(',', $ids) . ")";
					rss_query($sql);
					rss_invalidate_cache();
				}
			}
		} else {

			list($tname) = rss_fetch_row(rss_query("select tag from " .getTable("tag") ." where id = $tid"));

			echo "<form class=\"box\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
			."<p class=\"error\">";
			printf(__("Are you sure you wish to delete '%s'?"),$tname);
			echo "</p>\n"
			."<p><input type=\"submit\" name=\"".CST_ADMIN_CONFIRMED."\" value=\"". __('No') ."\"/>\n"
			."<input type=\"submit\" name=\"".CST_ADMIN_CONFIRMED."\" value=\"". __('Yes') ."\"/>\n"
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
			$res = rss_query("select count(*) as cnt from " . getTable("tag") ." where binary tag='".rss_real_escape_string($new_label)."'");
			list($cnt) = rss_fetch_row($res);
			if ($cnt > 0) {
				rss_error(sprintf(__("You can't rename this item '%s' because such an item already exists."),$new_label), RSS_ERROR_ERROR,true);
				break;
			}
			rss_query("update " .getTable("tag") ." set tag='".rss_real_escape_string($new_label)."' where id=$tid");
			rss_invalidate_cache();
		}
		break;
	default:
		break;
	}
  echo "<script type=\"text/javascript\">\n"
    ."//<!--\n"
    ."function cbtoggle() {\n"
    ."var c=document.getElementById('mastercb').checked;\n"
    ."var cs=document.getElementById('tagtable').getElementsByTagName('input');\n"
    ."for(i=0;i<cs.length;i++) {\n"
    ."if (cs[i].type == 'checkbox') cs[i].checked = c;\n"
    ."}\n"  
    ."}\n" 
		."</script>\n";

	echo "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">\n"
	."<h2 class=\"trigger\">".__('Tags')."</h2>\n"
	."<div id=\"admin_tags\" class=\"trigger\">"
	."<table id=\"tagtable\">\n"
	."<tr>\n"
  ."\t<th><input type=\"checkbox\" id=\"mastercb\" onclick=\"cbtoggle();\" /></th>\n"
	."\t<th class=\"cntr\">". __('Tags') ."</th>\n"
	."\t<th>". __('Action') ."</th>\n"
	."</tr>\n";

	$sql = sprintf("select id, tag from %s t left join %s m on (t.id = m.tid) where m.ttype = 'item'", getTable("tag"), getTable("metatag"));
	$res = rss_query($sql);
	$cntr = 0;
	while (list($id, $tag) = rss_fetch_row($res)) {
		$class_ = (($cntr++ % 2 == 0)?"even":"odd");
		echo "<tr class=\"$class_\">\n"
    ."\t<td><input type=\"checkbox\" name=\"tcb$id\" value=\"$id\" id=\"scb_$id\" /></td>\n"
		."\t<td><label for=\"scb_$id\">".htmlspecialchars($tag)."</label></td>\n"
		."\t<td><a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_TAGS
		."&amp;action=". CST_ADMIN_EDIT_ACTION. "&amp;id=$id\">" . __('edit')
		."</a>\n"
		."|<a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_TAGS
		."&amp;action=". CST_ADMIN_DELETE_ACTION ."&amp;id=$id\">" . __('delete') ."</a>\n"
		."|<a href=\"".getPath('tag/'.htmlspecialchars($tag))."\">" . __('view') ."</a>\n"		
		."</td>\n"
		."</tr>\n";
	}
	echo "</table>\n";
	echo "<fieldset>\n"
	."<legend>".__('Selected')."...</legend>\n"
	."<p>\n"
	."<input type=\"submit\" id=\"me_delete\" name=\"me_delete\" value=\"".__('Delete')."\" />\n"
	."<input type=\"checkbox\" name=\"me_do_delete\" id=\"me_do_delete\" value=\"1\" />\n"
	."<label for=\"me_do_delete\">".__("I'm sure!")."</label>\n"
	."<input type=\"hidden\" name=\"action\" value=\"".CST_ADMIN_DELETE_ACTION."\" />\n"
	."<input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_TAGS."\" />\n"
	."</fieldset>\n"
	."</form>\n"
	."</div>\n";
}

function tag_edit($tid){
	$sql = "select id, tag from " . getTable("tag") ." where id=$tid";
	$res = rss_query($sql);
	list ($id, $tag) = rss_fetch_row($res);

	echo "<div>\n"
	."<h2>".ucfirst(__('edit'))." '$tag'</h2>\n"
	."<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\" id=\"tagedit\">\n"

	."<div style=\"inline\"><input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"". CST_ADMIN_DOMAIN_TAGS."\"/>\n"
	."<input type=\"hidden\" name=\"action\" value=\"".CST_ADMIN_SUBMIT_EDIT."\"/>\n"
	."<input type=\"hidden\" name=\"id\" value=\"$tid\"/>\n"
	."<label for=\"t_name\">". __('Rename to...') ."</label>\n"
	."<input type=\"text\" id=\"t_name\" name=\"t_name\" value=\"$tag\"/>\n"
	."<input type=\"submit\" name=\"action_\" value=\"". __('Submit Changes') ."\"/></div>"
	."</form></div>\n";
}
