<?php
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

require_once("init.php");

// Show unread items on the front page?
// default to the config value, user can override this via a cookie
$show_what = (getConfig('rss.output.noreaditems') ?
	SHOW_UNREAD_ONLY : SHOW_READ_AND_UNREAD );
	
if (array_key_exists(SHOW_WHAT,$_POST)) {
	$show_what = $_POST[SHOW_WHAT];
	$period = time()+COOKIE_LIFESPAN;
	setcookie(SHOW_WHAT, $show_what , $period,getPath());  
} elseif (array_key_exists(SHOW_WHAT,$_COOKIE)) {
	$show_what = $_COOKIE[SHOW_WHAT];
}


if (array_key_exists('metaaction', $_POST)
    && $_POST['metaaction'] != ""
    && trim($_POST['metaaction']) == trim('LBL_MARK_READ')) {
    
    $sql = "update " .getTable("item") . " set unread=unread & "
     .SET_MODE_READ_STATE ." where unread  & " . FEED_MODE_UNREAD_STATE;
     
   if (hidePrivate()) {
	  	$sql .= " and !(unread & " . FEED_MODE_PRIVATE_STATE . ")";
	 }
	 
	 rss_query( $sql );
	  
}

if (array_key_exists('update',$_REQUEST)) {
    update("");
}


if (array_key_exists('logout',$_GET)) {
	logoutPrivateCookie();
}

$title = "";

rss_header($title,LOCATION_HOME,array('cid'=>null,'fid'=>null));

sideChannels(false);
echo "\n\n<div id=\"items\" class=\"frame\">";
$cntUnread = unreadItems($show_what);

if ($show_what != SHOW_UNREAD_ONLY || $cntUnread == 0) {
   readItems();
} 


echo "</div>\n";    
rss_footer();


function unreadItems($show_what) {

	$unreadItems = new ItemList();
	$unreadItems -> populate("i.unread & " . FEED_MODE_UNREAD_STATE);
	if ($unreadItems ->unreadCount) {
		echo "\n<div id=\"feedaction\">\n";
	   showViewForm($show_what);
		markAllReadForm();
		echo "</div>\n";
	}

	 $ret = $unreadItems -> unreadCount;
	 $unreadItems -> render(sprintf(LBL_H2_UNREAD_ITEMS , $ret),  IL_TITLE_NO_ESCAPE);
	 rss_plugin_hook('rss.plugins.items.afteritems', null);
	 
    return $ret;
}

function readItems() {
	$sql = "select "
      ." c.id"
      ." from " 
		.getTable("channels") . " c, " 
		.getTable("folders") ." f "
      ." where c.parent = f.id ";

	$sql .= " and !(c.mode & " . FEED_MODE_DELETED_STATE  .") ";


   if (getConfig('rss.config.absoluteordering')) {
		$sql .= " order by f.position asc, c.position asc";
   } else {
		$sql .=" order by c.parent asc, c.title asc";
   }

   $res1=rss_query($sql);


	$readItems = new ItemList();
   while (list($cid) = rss_fetch_row($res1)) {
	 	 $readItems->populate(" !(i.unread & ". FEED_MODE_UNREAD_STATE  .") and i.cid= $cid", "", 0, 2);
   }
	$readItems ->render(LBL_H2_RECENT_ITEMS,IL_TITLE_NO_ESCAPE);
	
}

function markAllReadForm() {
	if (!defined('MARK_READ_ALL_FORM')) {
		define ('MARK_READ_ALL_FORM',null);
	}	
	
   echo "<form action=\"". getPath() ."\" method=\"post\">"
      ."<p><input accesskey=\"m\" type=\"submit\" name=\"action\" value=\"". LBL_MARK_READ ." \"/></p>"
      ."<p><input type=\"hidden\" name=\"metaaction\" value=\"LBL_MARK_READ\"/></p>"
      ."</form>\n";
}


?>
