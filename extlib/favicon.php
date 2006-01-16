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
###############################################################################

require_once('../core.php');
// Cache expires after 24 hours
rss_bootstrap(true,'',24);

if(!isset($_GET['url'])) {
	exit();
}

$sql = "select data from " . getTable('cache') 
	. " where cachetype='icon' and cachekey='" 
	. rss_real_escape_string($_GET['url']) ."'";
	
list($blob) = rss_fetch_row(rss_query($sql));
if (!$blob) {
	exit();
} else {
	header('Content-Type: image/x-icon');
	echo $blob;
}
?>