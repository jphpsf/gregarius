<?
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2005 Marco Bonetti
#
###############################################################################
# File: $Id$ $Name$
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
# E-mail:	   mbonetti at users dot sourceforge dot net
# Web page:	   http://sourceforge.net/projects/gregarius
#
###############################################################################

require_once('init.php');

define ('MAX_TAGS_PER_ITEM',5);

function submit_tag($id,$tags) {
	$ftags = preg_replace('/[^a-zA-Z0-9\ _]/','',trim($tags));
	$tarr = array_slice(explode(" ",$ftags),0,MAX_TAGS_PER_ITEM);
	$ftags = implode(" ",updateTags($id,$tarr));
	return "$id,". $ftags;
}

function updateTags($fid,$tags) {
	rss_query("delete from " .getTable('metatag') . " where fid=$fid and ttype='item'");
	$ret = array();
	foreach($tags as $tag) {
		$ttag = trim($tag);
		if ($ttag == "" || in_array($ttag,$ret)) {
			continue;
		}
		rss_query( "insert into ". getTable('tag'). " (tag) values ('$ttag')", false );
		$tid = 0;
		if(rss_sql_error() == 1062) {
			list($tid)=rss_fetch_row(rss_query("select id from " .getTable('tag') . " where tag='$ttag'"));
		} else {
			$tid = rss_insert_id();
		}
		if ($tid) {
			rss_query( "insert into ". getTable('metatag'). " (fid,tid) values ($fid,$tid)" );
			if (rss_sql_error() == 0) {
				$ret[] = $ttag;
			}
		}
	}
	sort($ret);
	return $ret;
}

$sajax_request_type = "GET";
$sajax_debug_mode = 0;
$sajax_remote_uri = getPath() . "tag.php";
sajax_init();
sajax_export("submit_tag");

/* spit out the javascript for this bugger */
if (array_key_exists('js',$_GET)) {
	sajax_show_javascript(); 
	
	// and here is s'more javascript for field editing...
?>


function submit_tag_cb(ret) {
	data= ret.split(',');
	id=data[0];
	tags=data[1];
	var fld=document.getElementById("th" + id);
	fld.innerHTML = 
		"Tags: <span id=\"t" + id + "\">" + tags + "</span>" 
		+  "&nbsp;<a id=\"tt"+id+"\" href=\"\" onmouseup=\"edit_tag("+id+");\">(edit)</a>";
}

function submit_tag(id,tags) {
	x_submit_tag(id, tags, submit_tag_cb);
}

function edit_tag(id) {
	var toggle = document.getElementById("tt" + id);
	if (toggle.innerHTML == "(submit)") {

		var fld = document.getElementById("tfield" + id);
		if (fld.value!="") {
			toggle.innerHTML="(...)";
			submit_tag(id,fld.value);
		}
	} else if (toggle.innerHTML == "(edit)") {
		toggle.innerHTML="(submit)";
		var elem=document.getElementById("t"+id);
		elem.innerHTML = "<input class=\"tagedit\" id=\"tfield"+id+"\" type=\"text\" value=\"" + elem.innerHTML + "\" />";
		elem.firstChild.focus();
	} 
	return false;
}

<?
	exit();
} elseif(array_key_exists('rs',$_REQUEST)) {
	sajax_handle_client_request();
}



?>