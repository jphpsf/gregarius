<?php
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2006 Marco Bonetti
#
##############################################################################
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


require_once("init.php");
rss_require('cls/search.php');
rss_require('cls/wrappers/searchform.php');

$GLOBALS['rss'] -> feedList = new FeedList(false);
$items = new SearchItemList();
$items->setRenderOptions(IL_NONE | IL_NO_COLLAPSE);

if (array_key_exists(QUERY_PRM,$_REQUEST) && strlen($_REQUEST[QUERY_PRM]) > 1) {
    $GLOBALS['rss'] -> header = new Header("Search",LOCATION_SEARCH,null);
    $cnt = $items->itemCount;
    $humanReadableQuery = $items->humanReadableQuery;
    $title = sprintf((($cnt > 1 || $cnt == 0) ?
                      LBL_H2_SEARCH_RESULTS_FOR : LBL_H2_SEARCH_RESULT_FOR), $cnt, "'".$humanReadableQuery."'");

    $items->setTitle($title);
} else {
    list($cnt) = rss_fetch_row(rss_query('select count(*) from ' . getTable("item")
                                         . " where "
                                         .   " not(unread & " . RSS_MODE_DELETED_STATE  .") "
                                        ));
    $items->setTitle(sprintf(LBL_H2_SEARCH, $cnt));
    $GLOBALS['rss'] -> header = new Header(LBL_TITLE_SEARCH,LOCATION_SEARCH,null,"document.getElementById('".QUERY_PRM."').focus()");
}
$GLOBALS['rss'] -> appendContentObject($items);
$GLOBALS['rss'] -> renderWithTemplate('index.php');

?>
