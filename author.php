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

require_once ('init.php');

$a =  preg_replace("/[^A-Za-z0-9\.]/","%",rss_real_escape_string($_REQUEST['author']));

list ($ra) = rss_fetch_row(rss_query(
	"select distinct(author) from " .getTable('item') 
	." where author like '%$a%'"
));

if (!$ra) {
    rss_redirect();
}

$t = ucfirst(LBL_ITEMS) . " " . LBL_BY . " " . $ra;
$GLOBALS['rss']->header = new Header($t);
$GLOBALS['rss']->feedList = new FeedList(false);
$authorItems = new ItemList();
$sqlWhere = " i.author like '%$a%' ";
$authorItems->populate($sqlWhere);
$authorItems->setTitle($t);
$authorItems->setRenderOptions(IL_NO_COLLAPSE|IL_TITLE_NO_ESCAPE);
$GLOBALS['rss']->appendContentObject($authorItems);
$GLOBALS['rss']->renderWithTemplate('index.php', 'items');

?>
