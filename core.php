<?php

if (!defined('GREGARIUS_HOME')) {
	  define('GREGARIUS_HOME',dirname(__FILE__) . "/");
}
require_once(GREGARIUS_HOME . 'constants.php');
require_once(GREGARIUS_HOME . 'db.php');

function checkETag($withDB = true) {
	$key = '$Revision$'.$_SERVER["REQUEST_URI"];
	if ($withDB) {
		list($dt) = rss_fetch_row(rss_query(' select max(added) from ' .getTable('item')));
		$key .= $dt;
	}
	if (array_key_exists(PRIVATE_COOKIE,$_REQUEST)) {
		$key .= $_REQUEST[PRIVATE_COOKIE];
	}
	$key = md5($key);
	
	if (array_key_exists('HTTP_IF_NONE_MATCH',$_SERVER)  && $_SERVER['HTTP_IF_NONE_MATCH'] == $key) {
		header("HTTP/1.1 304 Not Modified");
		flush();
		exit();
	} else {
		header("ETag: $key");
	}
}
?>