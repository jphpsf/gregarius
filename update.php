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
# 59 Temple Place, Suite 330, Boston, MA	02111-1307	USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:		mbonetti at gmail dot com
# Web page:		http://gregarius.net/
#
###############################################################################

define ('RSS_NO_CACHE',true);
require_once('init.php');

$cline = isset($argv) && !$_REQUEST && isset($argc) && $argc;
if (!$cline && getConfig('rss.config.restrictrefresh')) {
	die(__('Sorry, updating from the web is currently not allowed.'));
}
rss_require("cls/update.php");
rss_require("extlib/browser.php");

$sajax_request_type = "POST";
$sajax_debug_mode = 0;
$sajax_remote_uri = getPath() . "update.php";
$sajax_export_list = array("ajaxUpdate","ajaxUpdateCleanup");
sajax_init();

if (array_key_exists('js',$_GET)) {
	header('Content-Type: text/javascript');
	ajaxUpdateJavascript();
	exit();
} elseif(array_key_exists('rs',$_REQUEST)) {
    // this one handles the xmlhttprequest call from the above javascript
    sajax_handle_client_request();
    exit();
}

$browser = new Browser();
$silent = array_key_exists('silent', $_GET) || ($cline && in_array('--silent',$argv));
$newsonly = array_key_exists('newsonly', $_GET) || ($cline && in_array('--newsonly', $argv));
$mobile = array_key_exists('mobile',$_GET);

$cid = DEFAULT_CID;
if(array_key_exists('cid', $_GET)) {
	$cid = $_GET['cid'];
} else if ($cline && in_array('--update-only', $argv)) {
	foreach($argv as $k => $v) {
		if ('--update-only' == $v) {
			$cid = $argv[$k+1];
			break;
		}
	}
}

$GLOBALS['rss'] -> header = new Header(
			__('Updating'), 
			LOCATION_UPDATE, 
			null, 
			"", 
			(HDR_NONE | HDR_NO_CACHECONTROL )
		);
	
$GLOBALS['rss'] -> feedList = new FeedList(false);

// Instantiate a different Update object, depending on the client
if ($cline && !$silent && !$newsonly) {
	$update = new CommandLineUpdate($cid);

} elseif ($cline && !$silent && $newsonly) {
	$update = new CommandLineUpdateNews($cid);
	
} elseif (getConfig('rss.config.serverpush') && !$silent && $browser->supportsServerPush()) {
	$update = new HTTPServerPushUpdate($cid);	
	
} elseif(!$silent && $browser->supportsAJAX()) {
	$update = new AJAXUpdate($cid);	

} elseif($mobile) {
	$update = new MobileUpdate($cid);
	
} else {
    error_reporting(0);
    $update = new SilentUpdate($cid);
}

$GLOBALS['rss'] -> appendContentObject($update);
if (!$silent && !$cline) {
	$GLOBALS['rss'] -> renderWithTemplate('index.php','update');
} else {
	$update->render();
}

?>
