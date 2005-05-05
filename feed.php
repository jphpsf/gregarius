<?
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



require_once('init.php');
rss_require('extlib/rss_fetch.inc');

define ('NAV_PREV_PREFIX','&larr;&nbsp;');
define ('NAV_SUCC_POSTFIX','&nbsp;&rarr;');




// Show unread items on the front page?
// default to the config value, user can override this via a cookie
$show_what = (getConfig('rss.output.noreaditems') ?
	SHOW_UNREAD_ONLY : SHOW_READ_AND_UNREAD );
	
if (array_key_exists(SHOW_WHAT,$_POST)) {
	$show_what = $_POST[SHOW_WHAT];
	$period = time()+COOKIE_LIFESPAN;
	setcookie(SHOW_WHAT, $show_what , $period, getPath());  
} elseif (array_key_exists(SHOW_WHAT,$_COOKIE)) {
	$show_what = $_COOKIE[SHOW_WHAT];
}



$y=$m=$d=0;
if (
    getConfig('rss.output.usemodrewrite')
    && array_key_exists('channel',$_REQUEST)    
    // this is nasty because a numeric feed title could break it
    && !is_numeric($_REQUEST['channel'])     
	) {
		 $sqlid =  preg_replace("/[^A-Za-z0-9\.]/","%",$_REQUEST['channel']);
		 $sql = "select id from " . getTable("channels") ." where title like '$sqlid'";

		if (hidePrivate()) {
			$sql .=" and !(mode & " . FEED_MODE_PRIVATE_STATE .") ";	      
		}		 
		 
		 $res =  rss_query( $sql );
		 //echo $sql;
		if ( rss_num_rows ( $res ) == 1) {
			list($cid) = rss_fetch_row($res);
		} else {
			$cid = "";
			
			// is this a folder?
			$sql = "select c.id, c.parent from ". getTable('channels')." c, "
				. getTable('folders') . " f "
				." where c.parent=f.id and f.name like '$sqlid' and f.id > 0";
				
			if (hidePrivate()) {
				$sql .=" and !(c.mode & " . FEED_MODE_PRIVATE_STATE .") ";	      
			}
			
			$res = rss_query( $sql );
			if ( rss_num_rows ( $res ) > 0) {
				$cids = array();
				while (list ($cid__,$fid__) = rss_fetch_row($res)) {
					$cids[] = $cid__;
					$fid = $fid__;
				}
			}
		}       
			  
		// date ?
		if ($cid != "" 
			&& array_key_exists('y',$_REQUEST) && $_REQUEST['y'] != "" && is_numeric($_REQUEST['y'])
			&& array_key_exists('m',$_REQUEST) && $_REQUEST['m'] != "" && is_numeric($_REQUEST['m'])) {	
				$y = (int) $_REQUEST['y'];
				if ($y < 1000) $y+=2000;
				
				$m =  $_REQUEST['m'];	
				if ($m > 12) {
					 $m = date("m");
				}
				
				$d =  $_REQUEST['d'];
				if ($d > 31) {
					 $d = date("d");
				} 
		 }
		 
		 
		 
		 // lets see if theres an item id as well
		 $iid = "";
		 if ($cid != "" && array_key_exists('iid',$_REQUEST) && $_REQUEST['iid'] != "") {
			$sqlid =  preg_replace("/[^A-Za-z0-9\.]/","%",$_REQUEST['iid']);
			$sql = "select id from " .getTable("item") ." i where i.title like '$sqlid' and i.cid=$cid";
			if ($m > 0 && $y > 0) {
				 $sql .= " and if (i.pubdate is null, month(i.added)= $m , month(i.pubdate) = $m) "
					." and if (i.pubdate is null, year(i.added)= $y , year(i.pubdate) = $y) ";
				 
				 if ($d > 0) {
					$sql .= " and if (i.pubdate is null, dayofmonth(i.added)= $d , dayofmonth(i.pubdate) = $d) ";
				 }
				 
			}
			
			if (hidePrivate()) {
				$sql .=" and !(i.unread & " . FEED_MODE_PRIVATE_STATE .") ";	      
			}
			
			$sql .=" order by i.added desc, i.id asc";
			
			$res =  rss_query( $sql );
			
			if ( rss_num_rows ( $res ) >0) {
				 list($iid) = rss_fetch_row($res);
			}
		 }  
	// no mod rewrite: ugly but effective
	} elseif (array_key_exists('channel',$_REQUEST) || array_key_exists('folder',$_REQUEST)) {
		$cid= (array_key_exists('channel',$_REQUEST))?$_REQUEST['channel']:"";
		$iid= (array_key_exists('iid',$_REQUEST))?$_REQUEST['iid']:"";
		$fid= (array_key_exists('folder',$_REQUEST))?$_REQUEST['folder']:"";
		
		if ($fid) {		
				$sql = "select c.id from ". getTable('channels')." c "
					." where c.parent=$fid and c.parent > 0";
					
				if (hidePrivate()) {
					$sql .=" and !(c.mode & " . FEED_MODE_PRIVATE_STATE .") ";	      
				}
				$res = rss_query( $sql );
				
				if ( rss_num_rows ( $res ) > 0) {
					$cids = array();
					while (list ($cid__) = rss_fetch_row($res)) {
						$cids[] = $cid__;
					}
				}	
		} elseif ($cid) {			
			if (hidePrivate()) {
				$sql = "select id from ". getTable('channels')." where id=$cid ";
				$sql .=" and !(mode & " . FEED_MODE_PRIVATE_STATE .") ";	      
				list ($cid) = rss_fetch_row(rss_query($sql));
			}
		}
		
}

// If we have no channel-id somethign went terribly wrong.
// Redirect to index.php
if ((!isset($cid) || $cid == "") && 
	(!isset($cids) || !is_array($cids) || !count($cids))) {
    $red = "http://" . $_SERVER['HTTP_HOST'] . getPath();
    header("Location: $red");
}

if (isset($cid) && array_key_exists ('action', $_POST) && $_POST['action'] == MARK_CHANNEL_READ) {
    
    $sql = "update " .getTable("item") ." set unread = unread & ".SET_MODE_READ_STATE." where cid=$cid";
    rss_query($sql);
    
    // redirect to the next unread, if any.
    $sql = "select cid,title from " . getTable("item") 
    	." where unread & ".FEED_MODE_UNREAD_STATE;
    	
    	
		if (hidePrivate()) {
				$sql .=" and !(unread & " . FEED_MODE_PRIVATE_STATE .") ";	      
		}
    	
    	$sql .=" order by added desc limit 1";
    	
    $res = rss_query($sql);
    list ($next_unread_id, $next_unread_title) = rss_fetch_row($res);
    
    if ($next_unread_id == '') {	
		$redirect = "http://"
		  . $_SERVER['HTTP_HOST']     
		  . dirname($_SERVER['PHP_SELF']);   
		
		if (substr($redirect,-1) != "/") {
			 $redirect .= "/";
		} 
		
		header("Location: $redirect");
		exit();
    } else {
		$cid = $next_unread_id;
    }
}

assert(
	(isset($cid) && is_numeric($cid)) || 
	(isset($fid) && isset($cids) && is_array($cids) && count($cids))
);

$itemFound = true;
if ($iid != "" && !is_numeric($iid)) {
    //item was deleted
    $itemFound = false;
    $iid = "";
}

//precompute the navigation hints, which will be passed to the header as <link>s
$links = NULL;
if ($cid && ($nv = makeNav($cid,$iid,$y,$m,$d)) != null) {
    list($prev,$succ, $up) = $nv;
    $links =array();
    if ($prev != null) {
	$links['prev'] =
	  array('title' => $prev['lbl'],
		'href' => $prev['url']);
    }
    if ($succ != null) {
	$links['next'] =
	  array(
		'title' => $succ['lbl'],
		'href' => $succ['url']);	
    }
    if ($up != null) {
	$links['up'] = 
	  array(
		'title' => $up['lbl'],
		'href' => $up['url']
		);
    }
}



if ($iid == "") {
	// "channel / folder mode"
	if ($cid) {
		$res = rss_query("select title,icon from " . getTable("channels") ." where id = $cid");
		list($title,$icon) = rss_fetch_row($res);
		if (isset($y) && $y > 0 && $m > 0 && $d == 0) {
			$dtitle =  (" " . TITLE_SEP ." " . date('F Y',mktime(0,0,0,$m,1,$y)));
		} elseif (isset($y) && $y > 0 && $m > 0 && $d > 0) {
			$dtitle =  (" " . TITLE_SEP ." " . date('F jS, Y',mktime(0,0,0,$m,$d,$y)));
		} else {
			$dtitle ="";
		}
	} elseif(isset($fid) && $fid) {
		list($title) = rss_fetch_row( rss_query("select name from " . getTable('folders') . " where id = $fid") );
		$dtitle ="";
	} else {
		$dtitle ="";
		$title = "";
	}
	
	
	if ($links) {
		foreach ($links as $rel => $val) {
			if (($lbl = $links[$rel]['title']) != "") {
				$links[$rel]['title'] = htmlentities( $title,ENT_COMPAT,"UTF-8" ) . " " . TITLE_SEP ." " . $lbl;
			} else {
				$links[$rel]['title'] = htmlentities( $title,ENT_COMPAT,"UTF-8" );
			}
		}
	}
   rss_header( rss_htmlspecialchars( $title ) . $dtitle,0,"", HDR_NONE, $links);
   
} else {
    // "item mode"
    $res = rss_query ("select c.title, c.icon, i.title from " . getTable("channels") ." c, " 
		     .getTable("item") ." i where c.id = $cid and i.cid=c.id and i.id=$iid");
    list($title,$icon,$ititle) = rss_fetch_row($res);

    rss_header(  
		 rss_htmlspecialchars($title) 
		 . " " . TITLE_SEP ." " 
		 .  rss_htmlspecialchars($ititle),
		 0,"", HDR_NONE, $links
		 );
}



sideChannels($cid); 
if (getConfig('rss.meta.debug') && array_key_exists('dbg',$_REQUEST)) {
    debugFeed($cid);
} else {
	if ($cid && !(isset($cids) && is_array($cids) && count($cids))) {
   		$cids = array($cid); 
   	}
	items($cids,$title,$iid,$y,$m,$d,(isset($nv)?$nv:null),$show_what);
}
rss_footer();


function items($cids,$title,$iid,$y,$m,$d,$nv,$show_what) {

	$do_show=$show_what;
	//should we honour unread-only?
	if ($show_what == SHOW_UNREAD_ONLY) {
	
		// permalink will always be printed
		if ($iid != "") {
			$do_show = SHOW_READ_AND_UNREAD;
		} else {
			// archives, folders, channels
			$sql = "select count(*) from " . getTable('item') . " where"
			." (unread & " . FEED_MODE_UNREAD_STATE .")";
			//archive?
			if ($m > 0 && $y > 0) {
				$sql .= " and if (pubdate is null, month(added)= $m , month(pubdate) = $m) "
			  		." and if (pubdate is null, year(added)= $y , year(pubdate) = $y) ";
				if ($d > 0) {
					$sql .= " and if (pubdate is null, dayofmonth(added)= $d , dayofmonth(pubdate) = $d) ";
				}
		 	}
		 
		 	$sql .= " and cid in (".implode(',',$cids).")";
		 	list($unreadCount) = rss_fetch_row(rss_query($sql));
		 	if ($unreadCount == 0) {
		 		$do_show = SHOW_READ_AND_UNREAD;
		 	}
		}
	}


   echo "\n\n<div id=\"items\" class=\"frame\">";    
	$items = array();
	foreach ($cids as $cid) {
		 $sitems = array();
		 $sql = " select i.title, i.url, i.description, i.unread, "
			." if (i.pubdate is null, unix_timestamp(i.added), unix_timestamp(i.pubdate)) as ts, "
			." pubdate is not null as ispubdate, "
			." c.icon, c.title, i.id, t.tag "
			." from " .getTable("item") . " i " 
			
			." left join ".getTable('metatag') ." m on (i.id=m.fid) "
			." left join ".getTable('tag')." t on (m.tid=t.id) "
			  
			. ", " . getTable("channels") ." c "
			." where i.cid = $cid and c.id = $cid ";
			
			if (hidePrivate()) {
				$sql .= " and !(i.unread & " . FEED_MODE_PRIVATE_STATE . ") ";
			}
		 
		 if ($iid != "") {
			$sql .= " and i.id=$iid";
		 }
		 
	
		 
		 if  ($do_show == SHOW_UNREAD_ONLY) {
			$sql .= " and (i.unread & " . FEED_MODE_UNREAD_STATE .") ";
		 }
		 
		 if ($m > 0 && $y > 0) {
			$sql .= " and if (i.pubdate is null, month(i.added)= $m , month(i.pubdate) = $m) "
			  ." and if (i.pubdate is null, year(i.added)= $y , year(i.pubdate) = $y) ";
		
			if ($d > 0) {
				 $sql .= " and if (i.pubdate is null, dayofmonth(i.added)= $d , dayofmonth(i.pubdate) = $d) ";
			}
		 }
		 
		 $sql .=" order by i.unread & ".FEED_MODE_UNREAD_STATE." desc, i.added desc, i.id asc";
	
		 //echo $sql;
	
		 if ( $m==0 && $y==0 ) {
			//$sql .= " limit " . getConfig('rss.output.itemsinchannelview');
			$limit = getConfig('rss.output.itemsinchannelview');
		 } else {
			$limit = 9999;
		 }
	
		 
	
		 $res = rss_query($sql);
		 
		 $iconAdded = false;
		 $hasUnreadItems = false;  
		 $added = 0;
		 $prevId = -1;
		 while ($added <= $limit && list($ititle, $iurl, $idescription, $iunread, $its, $iispubdate, $cicon, $ctitle, $iid_, $tag_) =  rss_fetch_row($res)) {
			
			$hasUnreadItems |= ($iunread & FEED_MODE_UNREAD_STATE);
			
			$added++;
			if($prevId != $iid_) {
				 $sitems[] = array(
						$cid,
						$ctitle,
						$cicon,
						$ititle,
						$iunread,
						$iurl,
						$idescription,
						$its,
						$iispubdate,
						$iid_,
						'tags' => array($tag_)
				 );
				 $prevId = $iid_;
			 } else {
				end($sitems);
				$sitems[key($sitems)]['tags'][]=$tag_;
				$added--;
			 }
		 }
		 
		

	
		 $sitems = array_slice($sitems,0,$limit);
		 foreach($sitems as $sitem) {
			$items[] = $sitem;
		 }
	 }


	 $severalFeeds = count($cids) > 1;
    if ($hasUnreadItems && $iid == "") {
		 echo "\n<div id=\"feedaction\" class=\"withmargin\">";

    	 showViewForm($show_what);
    	 
    	 if (!$severalFeeds) {
		 	markReadForm($cid);
		 }
		 
		 echo "\n</div>\n";
	 }
	 
	 
    $shown = itemsList($title, $items, ($severalFeeds? (IL_NO_COLLAPSE | IL_FOLDER_VIEW):IL_CHANNEL_VIEW));


    if ($nv != null) {
		list($prev,$succ,$up) = $nv;
		$readMoreNav = "";
		if($prev != null) {
			$lbl = $prev['lbl'];
			if (strlen($lbl) > 40) {
				$lbl = substr($lbl,0,37) . "...";
			}
			//$lbl = htmlentities($lbl,ENT_COMPAT,"UTF-8");
			$readMoreNav .= "<a href=\"".$prev['url']."\" class=\"fl\">".NAV_PREV_PREFIX ."$lbl</a>\n";
		}
		if($succ != null) {
			$lbl = $succ['lbl'];
			if (strlen($lbl) > 40) {
				$lbl = substr($lbl,0,37) . "...";
			}
			//$lbl = htmlentities($lbl,ENT_COMPAT,"UTF-8");
			$readMoreNav .= "<a href=\"".$succ['url']."\" class=\"fr\">$lbl".NAV_SUCC_POSTFIX."</a>\n";
		}
		
		if ($readMoreNav != "") {
			 echo "<div class=\"readmore\">$readMoreNav";
			 echo "<hr class=\"clearer hidden\"/>\n</div>\n";
		}
   }        
   echo "</div>\n";
}

/**
 * This function will return an array for the previous, next and up
 * navigation elements, based on the current location
 *
 * @return: array (
 	('prev'|'next'|'up')* => array (
 		 'y' => year of the prev,next,up item
		 'm' => month of the prev,next,up item
		 'd' => day of the prev,next,up item
		 'cnt' => count of the prev,next,up items for this date
		 'ts' => unix timestamp of the above 
		 'url' =>  precomputed uri for the link
		 'lbl' => precomupted label to be used in the links
 	)
 )
 */
function makeNav($cid,$iid,$y,$m,$d) {

	$currentView = null;
	$prev = $succ = $up = null;
	$escaped_title = preg_replace("/[^A-Za-z0-9\.]/","_",$_REQUEST['channel']);	

	// where are we anyway?
	if ($y > 0 && $m > 0 && $d > 0) {
		if ($iid != "") {
	 		$currentView = 'item';
	 	} else {
	 		$currentView = 'day';
	 	}
	} elseif ($y > 0 && $m > 0 && $d == 0) {
   	$currentView = 'month';
	}
    
	if ($currentView) {
	
		switch ($currentView) {
			case 'month':
			case 'day':
			
			if ($currentView == 'day') {
				$ts_p = mktime(23,59,59,$m,$d-1,$y);
				$ts_s = mktime(0,0,0,$m,$d,$y);
			} elseif($currentView == 'month') {
				$ts_p = mktime(0,0,0,$m+1,0,$y);
				$ts_s = mktime(0,0,0,$m,1,$y);			
			}
			
			$sql_succ = " select "
			  ." UNIX_TIMESTAMP( if (i.pubdate is null, i.added, i.pubdate)) as ts_, "
			  ." year( if (i.pubdate is null, i.added, i.pubdate)) as y_, "
			  ." month( if (i.pubdate is null, i.added, i.pubdate)) as m_, "
			  .(($currentView == 'day')?" dayofmonth( if (i.pubdate is null, i.added, i.pubdate)) as d_, ":"")
			  ." count(*) as cnt_ "
			  ." from " . getTable("item") . "i  "
			  ." where cid=$cid "
			  ." and UNIX_TIMESTAMP(if (i.pubdate is null, i.added, i.pubdate)) > $ts_s ";
			  
			  	if (hidePrivate()) {
					$sql_succ .=" and !(i.unread & " . FEED_MODE_PRIVATE_STATE .") ";	      
		      }
			  
			  $sql_succ .= " group by y_,m_"
			  .(($currentView == 'day')?",d_ ":"")
			  ." order by ts_ asc limit 4";
			
			$sql_prev = " select "
			  ." UNIX_TIMESTAMP( if (i.pubdate is null, i.added, i.pubdate)) as ts_, "
			  ." year( if (i.pubdate is null, i.added, i.pubdate)) as y_, "
			  ." month( if (i.pubdate is null, i.added, i.pubdate)) as m_, "
			  .(($currentView == 'day')?" dayofmonth( if (i.pubdate is null, i.added, i.pubdate)) as d_, ":"")
			  ." count(*) as cnt_ "
			  ." from " . getTable("item") ." i  "
			  ." where cid=$cid "
			  ." and UNIX_TIMESTAMP(if (i.pubdate is null, i.added, i.pubdate)) < $ts_p ";
			  
			  
			  	if (hidePrivate()) {
					$sql_prev .=" and !(i.unread & " . FEED_MODE_PRIVATE_STATE .") ";	      
		      }
		      
			  $sql_prev .= " group by y_,m_"
			  .(($currentView == 'day')?",d_ ":"")
			  ." order by ts_ desc limit 4";
		
			//echo "<!-- $sql_prev -->\n";
			$res_prev = rss_query($sql_prev);
			$res_succ = rss_query($sql_succ);
			
			$mCount = (12 * $y + $m);
			
			// next
			while ($succ == null && $row=rss_fetch_assoc($res_succ)) {
				 if ($currentView == 'day') {
				if (mktime(0,0,0,$row['m_'],$row['d_'],$row['y_']) > $ts_s) {
					 $succ = array(
						  'y' => $row['y_'], 
						  'm' => $row['m_'], 
						  'd' => $row['d_'], 
						  'cnt' => $row['cnt_'], 
						  'ts' => $row['ts_'],
						  'url' =>  makeArchiveUrl($row['ts_'],$escaped_title,$cid,($currentView == 'day')),
						  'lbl' => date('F jS',$row['ts_']) . " (".$row['cnt_']." " . ($row['cnt_'] > 1? ITEMS:ITEM) .")"
						  );
				}
				 } elseif($currentView == 'month') {		
				if (($row['m_'] + 12 * $row['y_']) > $mCount) {		    
					 $succ = array(
						  'y' => $row['y_'],
						  'm' => $row['m_'],
						  'cnt' => $row['cnt_'],
						  'ts' => $row['ts_'],
						  'url' =>  makeArchiveUrl($row['ts_'],$escaped_title,$cid,($currentView == 'day')),
						  'lbl' => date('F Y',$row['ts_']) . " (".$row['cnt_']." " . ($row['cnt_'] > 1? ITEMS:ITEM) .")"
						  );
				}
				
				 }
			}
			
			// prev
			while ($prev == null && $row=rss_fetch_assoc($res_prev)) {
				 if ($currentView == 'day') {
					if (mktime(0,0,0,$row['m_'],$row['d_'],$row['y_']) < $ts_p) {
						 $prev = array(
							  'y' => $row['y_'],
							  'm' => $row['m_'],
							  'd' => $row['d_'],
							  'cnt' => $row['cnt_'],
							  'ts' => $row['ts_'],
							  'url' =>  makeArchiveUrl($row['ts_'],$escaped_title,$cid,($currentView == 'day')),
							  'lbl' => date('F jS',$row['ts_']) . " (".$row['cnt_']." " . ($row['cnt_'] > 1? ITEMS:ITEM) .")"
							  );
					}
				 } elseif($currentView == 'month') {
					if (($row['m_'] + 12 * $row['y_']) < $mCount) {
						 $prev = array(
							  'y' => $row['y_'],
							  'm' => $row['m_'],
							  'cnt' => $row['cnt_'],
							  'ts' => $row['ts_'],
							  'url' =>  makeArchiveUrl($row['ts_'],$escaped_title,$cid,($currentView == 'day')),
							  'lbl' => date('F Y',$row['ts_']) . " (".$row['cnt_']." ". ($row['cnt_'] > 1? ITEMS:ITEM) .")"
							  );
					}				
				 }
			}
			// up
			if ($currentView == 'day') {
				 $ts = mktime(0,0,0,$m,10,$y);
				 $up = array(
					'y' => $y,
					'm' => $m,
					'url' => makeArchiveUrl($ts,$escaped_title,$cid,false),
					'lbl' => date('F Y',$ts)
					);
			} elseif ($currentView == 'month') {
				 $up = array(
					'url' => getPath() . $escaped_title ."/",
					'lbl' => '');
			}
			
			break;
			
			case 'item':
			
				$sql = " select i.title, i.id, "
				  ." UNIX_TIMESTAMP( if (i.pubdate is null, i.added, i.pubdate)) as ts_, "
				  ." year( if (i.pubdate is null, i.added, i.pubdate)) as y_, "
				  ." month( if (i.pubdate is null, i.added, i.pubdate)) as m_, "
				  ." dayofmonth( if (i.pubdate is null, i.added, i.pubdate)) as d_ "
				  ." from " .getTable("item") . " i " 
				  ." where i.cid = $cid  ";
				  
					if (hidePrivate()) {
						$sql .= " and !(i.unread & " . FEED_MODE_PRIVATE_STATE .") ";	      
					}
		      
				  $sql .= " order by i.added desc, i.id asc";
				  
				$rs = rss_query($sql);
				$found = false;
				$stop = false;
				$prev__ = null;
				$fCounter = 0;
				while (!$stop && list($title_,$iid_,$ts_,$y_,$m_,$d_) = rss_fetch_row($rs)) {
					if  ($iid_ == $iid) {
					 	//this is the "current" item, get a hold on the previous one
						$found = true;
					     if ($prev__) {
							 list($ptitle_,$piid_,$pts_,$py_,$pm_,$pd_) = $prev__;
							 $succ = array(
								  'y' => $py_,
								  'm' => $pm_,
								  'cnt' => 0,
								  'ts' => $pts_,
								  'url' =>  makeArchiveUrl($pts_,$escaped_title,$cid,true)
									. preg_replace("/[^A-Za-z0-9\.]/","_",$ptitle_),
								  'lbl' => htmlentities( $ptitle_,ENT_COMPAT,"UTF-8" )
							);
						}
					}
					
					if ($found) {
						// okay, this is the next item, then.
						$fCounter++;
						if ($fCounter == 2) {
							$prev = array(
								  'y' => $y_,
								  'm' => $m_,
								  'cnt' => 0,
								  'ts' => $ts_,
								  'url' =>  makeArchiveUrl($ts_,$escaped_title,$cid,true)
								  	. preg_replace("/[^A-Za-z0-9\.]/","_",$title_),
								  'lbl' => htmlentities($title_,ENT_COMPAT,"UTF-8")
							);	
							$stop = true;
						}
					}
					
					
					$prev__ = array($title_,$iid_,$ts_,$y_,$m_,$d_);
					
				}
	
			// up
			
			$ts = mktime(0,0,0,$m,$d,$y);
			$up = array(
					'y' => $y,
					'm' => $m,
					'd' => $d,
					'url' => makeArchiveUrl($ts,$escaped_title,$cid,true),
					'lbl' => date('F jS',$ts)
					);
			
				
			break;
		}

		
	
		return array($prev,$succ, $up);
	}
	
    return null;

}

function markReadForm($cid) {
	
  	echo "\n\n<form action=\"". getPath() ."feed.php\" method=\"post\">\n"
  	  ."\t<p><input type=\"submit\" name=\"action\" value=\"". MARK_CHANNEL_READ ."\"/>\n"
  	  ."\t<input type=\"hidden\" name=\"channel\" value=\"$cid\"/></p>\n"
  	  ."</form>";
}


function debugFeed($cid) {
    echo "<div id=\"items\" class=\"frame\">\n";
    $res = rss_query("select url from " .getTable("channels") ." where id = $cid");
    if (! defined('MAGPIE_DEBUG') || !MAGPIE_DEBUG) {
    	define ('MAGPIE_DEBUG',true);
    }
    list($url) = rss_fetch_row($res);
    $rss = fetch_rss($url);
    echo "<pre>\n";
    echo htmlentities(print_r($rss,1));
    echo "</pre>\n";    
    echo "</div>\n";
}

?>
