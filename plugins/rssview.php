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


/// Name: RSS View
/// Author: Marco Bonetti
/// Description: Adds a RSS link to the header and the footer of each page
/// Version: 0.7
/// Configuration: __rss_view_config

/**
 * Changes:
 * 0.4 - Properly escape the RSS url's entities.
 * 0.5 - Adapted to the new theme model
 * 0.6 - Don't put a link in admin and other locations
 * 0.7 - Show ATOM link as well.
 * 0.8 - Add configurable output count.
 */

define ('RSSVIEW_CONFIG_OUTPUTCOUNT', 'rssview.outputcount');
define ('RSSVIEW_DEFAULT_COUNT', 10);

function __rss_view_config () {
	$cnt = rss_plugins_get_option(RSSVIEW_CONFIG_OUTPUTCOUNT);
	
	if(empty($cnt)) {
		$cnt = RSSVIEW_DEFAULT_COUNT;
	}
	
  if(rss_plugins_is_submit()) {
  	if(!empty($_REQUEST['rv_oc'])) {
  		$cnt = $_REQUEST['rv_oc'];
		}
		
  	rss_plugins_add_option(RSSVIEW_CONFIG_OUTPUTCOUNT, $cnt, 'num');
  	return;
	}

	print ("<fieldset>\n");
	print ("  <legend>" . __('Output Count') . "</legend>\n");
	print ("   <p><label for='rv_oc'>" . __('Count') . "</label></br>");
	print ("   <input id='rv_oc' type='text' value='" . $cnt . "' name='rv_oc'></p>\n");
	print ("</fieldset>\n");
}

function __rss_view_url($type = "rss") {
    $url 	= guessTransportProto() . $_SERVER['HTTP_HOST'];
    $url .= $_SERVER["REQUEST_URI"];

		$cnt = rss_plugins_get_option(RSSVIEW_CONFIG_OUTPUTCOUNT);
		
		if(empty($cnt)) {
			$cnt = RSSVIEW_DEFAULT_COUNT;
		}
		
    if (strstr($_SERVER['REQUEST_URI'],"?") !== FALSE) {
        $url .= "&amp;media=$type&amp;count=$cnt";
    } else {
        $url .= "?media=$type&count=$cnt";
    }
    $url .= __rss_view_post2get();
    $url = str_replace('&amp;','&',$url);
    return str_replace('&','&amp;',$url);
}

/**
 * look for POST parameters prefixed with "rss_" and add them to the RSS url 
 */
function __rss_view_post2get() {
    $ret = "";
    foreach($_POST as $key => $val) {
        if (substr($key,0,4) == 'rss_') {
            $ret .= "&" ."$key=" . $_POST[$key];
        }
    }
    return $ret;
}

function __rss_view_footerlink($dummy) {
    if (!defined('RSS_FILE_LOCATION')) {
        echo "<span><a href=\"".__rss_view_url()."\">RSS</a></span>\n";
    	echo "<span><a href=\"".__rss_view_url("atom")."\">Atom</a></span>\n";
    }
    return $dummy;
}

function __rss_view_headerlink($dummy) {
    if (!defined('RSS_FILE_LOCATION')) {
        echo "\t<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"".__rss_view_url()."\" />\n";
        echo "\t<link rel=\"alternate\" type=\"application/atom+xml\" title=\"Atom\" href=\"".__rss_view_url("atom")."\" />\n";
    }
    return $dummy;
}

rss_set_hook("rss.plugins.footer.span", "__rss_view_footerlink");
rss_set_hook("rss.plugins.stylesheets",'__rss_view_headerlink');

?>
