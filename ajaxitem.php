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

# get an item content via ajax

require_once('init.php');

$id=(int)substr($_GET["item"],1);

if (substr($_GET["item"],0,1)=='i' && $id>0) {

	$sql="
	select
		title, unread, url,
		enclosure, author, description,
		unix_timestamp(ifnull(pubdate,added)) as ts,
		pubdate is not null as ispubdate, id, cid
	from".
		getTable("item")." i
	where
		id=$id";

	if (hidePrivate()) {
		$sql.= " and not(i.unread & ".RSS_MODE_PRIVATE_STATE.") ";
	}

	$res=$GLOBALS['rss_db']->rss_query($sql);
	$row=$GLOBALS['rss_db']->rss_fetch_row($res);

	$ititle_=$row[0];
	$iunread_=$row[1];
	$iurl_=$row[2];
	$ienclosure_=$row[3];
	$iauthor_=$row[4];
	$idescr_=$row[5];
	$its_=$row[6];
	$iispubdate_=$row[7];
	$iid_=$row[8];
	$cid_=$row[9];
	$ctitle_=FALSE;
	$cicon_=FALSE;
	$rrating_=FALSE;

	$i = new Item($iid_, $ititle_, $iurl_, $ienclosure_, $cid_, $iauthor_, $idescr_, $its_, $iispubdate_, $iunread_, $rrating_);

	$i->renderBodyOnly();


}
?>
