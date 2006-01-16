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
# Web page:	   http://gregarius.net/
#
###############################################################################


require_once('init.php');

$items = new ItemList();
$items -> setRenderOptions(IL_NO_COLLAPSE);

if(array_key_exists('state', $_GET) && RSS_STATE_FLAG == $_GET['state']) {
    $items -> populate( "i.unread & " . RSS_MODE_FLAG_STATE	);
    $GLOBALS['rss'] -> header = new Header(LBL_FLAG . " " . LBL_ITEMS);
} else if (array_key_exists('state', $_GET) && RSS_STATE_STICKY == $_GET['state']) {
    $items -> populate( "i.unread & " . RSS_MODE_STICKY_STATE	);
    $GLOBALS['rss'] -> header = new Header(LBL_STICKY . " " . LBL_ITEMS);
} else {
    $items -> populate( "i.unread & " . RSS_MODE_FLAG_STATE	. " OR i.unread & " . RSS_MODE_STICKY_STATE );
    $GLOBALS['rss'] -> header = new Header(LBL_ITEMS);
}

$GLOBALS['rss'] -> feedList = new FeedList(false);
$GLOBALS['rss'] -> appendContentObject($items);
$GLOBALS['rss'] -> renderWithTemplate('index.php');
?>
