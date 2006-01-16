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


class DebugFeed {

	var $fid;

	function DebugFeed($fid) {
		$this -> fid = (int)rss_real_escape_string($fid);				
	}

	function render() {
		$res = rss_query("select url from " .getTable("channels") ." where id = " .$this -> fid);
    if (! defined('MAGPIE_DEBUG') || !MAGPIE_DEBUG) {
    	define ('MAGPIE_DEBUG',true);
    }
    list($url) = rss_fetch_row($res);
    define ('MAGPIE_CACHE_ON', false);
    $rss = fetch_rss($url);
		echo "<pre>\n";
    echo htmlentities(print_r($rss,1));
    echo "</pre>\n"; 
	}
}
?>
