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
# E-mail:      mbonetti at users dot sourceforge dot net
# Web page:    http://sourceforge.net/projects/gregarius
#
###############################################################################

///// ITEMLIST WRAPPERS /////
function rss_itemlist_title() {
	
	return ($GLOBALS['rss'] -> currentItemList -> renderOptions & IL_TITLE_NO_ESCAPE ? 
		$GLOBALS['rss']->currentItemList ->title : rss_htmlspecialchars($GLOBALS['rss']->currentItemList ->title));
}

function rss_itemlist_anchor() {
	$anchor = "";		
	if (!defined('FEEDCONTENT_ANCHOR_SET')) {
		$anchor = " id=\"feedcontent\"";
		define('FEEDCONTENT_ANCHOR_SET', true);
	}
	return $anchor;
}

function rss_itemlist_icon() {
	
	if (($GLOBALS['rss']->renderOptions & IL_CHANNEL_VIEW) && 
		getConfig('rss.output.showfavicons') && 
		count($GLOBALS['rss'] -> currentItemList -> feeds)) {
			$key = array_keys($GLOBALS['rss'] -> currentItemList -> feeds);
			return $GLOBALS['rss'] -> currentItemList -> feeds[$key[0]] -> iconUrl;
	} elseif (($GLOBALS['rss']->renderOptions & IL_FOLDER_VIEW) && getConfig('rss.output.showfavicons')) {
		return getThemePath()."media/folder.gif";
	}
		
	return null;
	
}

function rss_itemlist_has_extractions() {
	return count(isset($GLOBALS['rss'] -> currentItemList-> preRender)) > 0;
}


function rss_itemlist_prerender_callback() {
	if (isset($GLOBALS['rss'] -> currentItemList->preRender) && 
		  count($GLOBALS['rss'] -> currentItemList->preRender)) {
		foreach($GLOBALS['rss'] -> currentItemList->preRender as $prAction) {
			list($prAfnct,$prAargs)=$prAction;
			call_user_func($prAfnct, $prAargs);
		}
	}	
}

function rss_itemlist_feeds() {
	
	foreach($GLOBALS['rss'] -> currentItemList->feeds as $feed) {
		$feed->render();
	}
}

function rss_itemlist_before_list() {
	return $GLOBALS['rss'] -> currentItemList-> beforeList;
}

function rss_itemlist_after_list() {
	return $GLOBALS['rss'] -> currentItemList-> afterList;
}

?>