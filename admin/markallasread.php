<?php
/* This is a temporary file that will be removed once we have a mechanism to 
 * mark *all* items as read. You can run this script from the command line and also from the web
*/
require_once('../init.php');
$sql = "update " .getTable("item"). " set unread = unread & ". SET_MODE_READ_STATE;
rss_query($sql);
rss_invalidate_cache();
?>
