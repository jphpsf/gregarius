<?php
require_once('core.php');
rss_bootstrap();
require_once('init.php');

if (isset($_REQUEST['method'])) {
	switch ($_REQUEST['method']) {
		case 'update':
			$uc = getUnreadCount(null,null);
			die("|$uc||");
			break;
		case 'listsubs':
			blOPML();
			break;
		case 'getitems':
			$cid = (isset($_REQUEST['s'])?$_REQUEST['s']:null);
			$date = (isset($_REQUEST['d'])?$_REQUEST['d']:null);
			$markread = (isset($_REQUEST['n']) && $_REQUEST['n'] == '1');
			blGetItems($cid,$date,$markread);
			break;
	}
}


function blOPML() {
		// Unread count
		$ucres = rss_query ("select cid, count(*) from " .getTable("item")
        ." where unread & "  . FEED_MODE_UNREAD_STATE
        . " and not(unread & " . FEED_MODE_DELETED_STATE .") group by cid");
		$uc = array();
		while (list($uccid,$ucuc) = rss_fetch_row($ucres)) {
			$uc[$uccid]=$ucuc;
		}
    
    
    $sql = "select "
    ." c.id, c.title, c.url, c.siteurl, f.name "
    ." from ".getTable("channels")." c, "
		.getTable("folders")." f "." where f.id = c.parent";

		if (hidePrivate()) {
			$sql .= " and not(c.mode & ".FEED_MODE_PRIVATE_STATE.") ";
		}

		$sql .= " and not(c.mode & ".FEED_MODE_DELETED_STATE.") ";

		if (getConfig('rss.config.absoluteordering')) {
			$sql .= " order by f.position asc, c.position asc";
		} else {
			$sql .= " order by f.name, c.title asc";
		}
		$folders = array();
		$res = rss_query($sql);
		while (list($cid,$title,$xmlUrl,$siteUrl,$folder) = rss_fetch_row($res)) {
			if (!isset($folders[$folder])) {
				$folders[$folder] = array();
			}
			$folders[$folder][] = array(
				'title' => htmlspecialchars($title),
				'htmlUrl' => htmlspecialchars($siteUrl),
				'xmlUrl' => htmlspecialchars($xmlUrl),
				'BloglinesUnread'=>(isset($uc[$cid]) ? $uc[$cid]:0),
				'BloglinesSubId'=>$cid
			);
		}
		
		header('Content-Type: text/xml; charset=utf-8');
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		echo "<opml version=\"1.0\">\n"
				."<head>\n"
				."\t<title>Gregarius Subscriptions</title>\n"
				."\t<dateCreated>". date('D, j M Y H:i:s \G\M\T') ."</dateCreated>\n"
				."\t<ownerName></ownerName>\n"
				."</head>\n"
				."<body>\n";
				
	foreach ($folders as $fname => $folder) {
		echo "\t<outline title=\"$fname\">\n";
		foreach ($folder as $feed) {
			echo "\t\t<outline type=\"rss\"";
			foreach ($feed as $key => $value) {
				echo " $key=\"$value\"";
			}
			echo " />\n";
		}
		echo "\t</outline>\n";
	}
	echo "</body>\n"
			."</opml>\n";

}

function blGetItems($cid,$date,$markread) {
	if (hidePrivate()) {
		header('HTTP/1.x 401 Not Authorized'); // TODO
		exit();
	}
	
	if (!$cid) {
		header ('HTTP/1.x 403 Forbidden'); // TODO
		exit();
	}
	
	$sql = "select i.title as ititle, i.description as idescr, c.title as ctitle, "
	." c.descr as cdescr, c.url as curl, i.author as iauth, i.url as iurl, "
	." unix_timestamp(ifnull(i.pubdate, i.added)) as idate ,i.id as iid"
	." from ".getTable('item')." i, ".getTable('channels') ." c "
	." where i.cid=c.id and i.unread & ". FEED_MODE_UNREAD_STATE ." and c.id=$cid";
	
	if ($date) {
		$sql .= " and ifnull(i.pubdate, i.added) > $date ";
	}
	$rs = rss_query($sql);
	
	if (rss_num_rows($rs) == 0) {
		header('HTTP/1.x 304 ERROR'); // TODO
		exit();
	}
	$ids = array();
	header('Content-Type: text/xml; charset=utf-8');
	$hdr = false;
	while($row=rss_fetch_assoc($rs)) {
		if (!$hdr) {
			$hdr = true;		
			echo "<" ."?xml version=\"1.0\"?" .">\n"
		."<rss version=\"2.0\"\n"
		."xmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n"
		."xmlns:bloglines=\"http://www.bloglines.com/services/module\"\n"
		."xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\">\n"
																																									
		."<channel>\n"
		 ."\t<title>".htmlspecialchars($row['ctitle'])."</title>\n"
			."\t<link>".htmlspecialchars($row['curl'])."</link>\n"
			."\t<description>".htmlspecialchars($row['cdescr'])."</description>\n"
																																									
			."\t<language>en-us</language>\n"
			."\t<webMaster>support@bloglines.com</webMaster>\n"
			//."\t<bloglines:siteid>66</bloglines:siteid>\n"
			;
		}
	
		$ids[] = $row['iid'];
	
		echo "\t<item>\n"
        ."\t\t<title>".htmlspecialchars($row['ititle'])."</title>\n"
        ."\t\t<dc:creator>".htmlspecialchars($row['iauth'])."</dc:creator>\n"
        ."\t\t<guid isPermaLink=\"true\">".htmlspecialchars($row['iurl'])."</guid>\n"
        ."\t\t<link>".htmlspecialchars($row['iurl'])."</link>\n"
        ."\t\t<description><![CDATA[".$row['idescr']."]]></description>\n"
        ."\t\t<pubDate>".date('D, j M Y H:i:s \G\M\T',$row['idate'])."</pubDate>\n"
        ."\t\t<bloglines:itemid>".$row['iid']."</bloglines:itemid>\n"
    		."\t</item>\n";
	}
	echo "</channel>\n</rss>\n";

	if ($markread) {
		$sql = "update ".getTable('item')." set unread = unread & " .SET_MODE_READ_STATE 
		." where id in (" . implode(',',$ids) .")";
		rss_query($sql);
		rss_invalidate_cache();
	}

}
?>