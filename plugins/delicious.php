<?php
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003, 2004 Marco Bonetti
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
# E-mail:      mbonetti at users dot sourceforge dot net
# Web page:    http://sourceforge.net/projects/gregarius
#
###############################################################################


/// Name: del.icio.us Tags
/// Author: Marco Bonetti
/// Description: Fetches tag suggestiongs from del.icio.us
/// Version: 0.2


function __delicious_js_register($js) {
	$js[] = getPath(). RSS_PLUGINS_DIR . "/delicious.php?deljs";
	return $js;
}

if (isset($_REQUEST['deljs'])) {
require_once('../init.php');
ETagHandler('$Revision: 845 $');
?>
function get_from_delicious(id) {
 x___exp__getFromDelicious(id,getFromDelicious_cb);
}

function getFromDelicious_cb(ret) {
 data=ret.split(',');
 id=data[0];
 tags=data[1].split(' ');
 var span=document.getElementById('dt'+id);
 html = '';
 for(i=0;i<tags.length;i++) {
  if (tags[i] != '') {
    html += "<a href=\"#\" onclick=\"addToTags(" + id +",'"
    +tags[i]
    +"'); return false;\">"+tags[i]+"</a>"
    if(i<tags.length -1) { html += "&nbsp;"; }
  }
 }
 if (html == '') {
  html = '<?= LBL_TAG_SUGGESTIONS_NONE ?>';
 }
 span.innerHTML = '(' + html + ')';
}

function addToTags(id,tag) {
 var fld = document.getElementById("tfield" + id);
 fld.value=fld.value+ " " + tag;
}

<?php			 
	flush();
	exit();
}

function __delicious_appendAJAXfunction($exp) {
	$exp[]='__exp__getFromDelicious';
	return $exp;
}

function __exp__getFromDelicious($id) {
    list($url)= rss_fetch_row(
       rss_query('select url from '  . getTable('item')  ." where id=$id"));
    $ret = array();
    $durl = "http://del.icio.us/url/" . md5($url);
    $bfr = getUrl($durl,2000);
    if ($bfr) {
			define ('RX','|<a href="/tag/([^"]+)">\\1</a>|U');
			if ($bfr && preg_match_all(RX,$bfr,$hits,PREG_SET_ORDER)) {
					$hits=array_slice($hits,0,MAX_TAGS_PER_ITEM);
					foreach($hits as $hit) {
						$ret[] = $hit[1];
					}
				}
   	}
   return "$id," .implode(" ",$ret);
}

function __delicious_edittag_js($dummy) {
?>
        // get tag suggestions from del.icio.us
        newspan = document.createElement("span");
        newspan.setAttribute("id","dt" + id);
        newspan.style.margin="0 0 0 0.5em";
        newspan.innerHTML = "<?= LBL_TAG_SUGGESTIONS ?>: (...) ]";
        actionSpan.appendChild(newspan);
        get_from_delicious(id);
<?php
	return null;
}


rss_set_hook('rss.plugins.javascript','__delicious_js_register');
rss_set_hook('rss.plugins.ajax.exports','__delicious_appendAJAXfunction');
rss_set_hook('rss.plugins.ajax.extrajs.edittag','__delicious_edittag_js');
?>
