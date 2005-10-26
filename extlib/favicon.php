<?php
require_once('../core.php');
rss_bootstrap(true,'',24);
if(!isset($_GET['url'])) {
	exit();
}
$sql = "select data from " . getTable('cache') 
	. " where cachetype='icon' and cachekey='" . rss_real_escape_string($_GET['url']) ."'";
list($blob) = rss_fetch_row(rss_query($sql));
if (!$blob) {
	exit();
} else {
	header('Content-Type: image/x-icon');
	echo $blob;
}
?>