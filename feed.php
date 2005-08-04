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

define ('LBL_NAV_PREV_PREFIX','&larr;&nbsp;');
define ('LBL_NAV_SUCC_POSTFIX','&nbsp;&rarr;');




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
		 $sqlid = rss_real_escape_string($_REQUEST['channel']);
		 $sql = "select id from " . getTable("channels") ." where title like '$sqlid'";

		if (hidePrivate()) {
			$sql .=" and !(mode & " . FEED_MODE_PRIVATE_STATE .") ";	      
		}		 
		// hide deprecated
		$sql .= " and !(mode & " . FEED_MODE_DELETED_STATE . ") ";

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
			$sql .= " and !(c.mode & " .  FEED_MODE_DELETED_STATE .") ";
	 
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

            $sql .=" and !(i.unread & " . FEED_MODE_DELETED_STATE  .") ";
			
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
		
		$y= (array_key_exists('y',$_REQUEST))?$_REQUEST['y']:"0";
		$m= (array_key_exists('m',$_REQUEST))?$_REQUEST['m']:"0";
		$d= (array_key_exists('d',$_REQUEST))?$_REQUEST['d']:"0";
		
		if ($fid) {		
				$sql = "select c.id from ". getTable('channels')." c "
					." where c.parent=$fid and c.parent > 0";
				$sql .= " and !(c.mode & " .  FEED_MODE_DELETED_STATE .") ";

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

    rss_redirect();
}
//echo ("cid=".(isset($cid)?"$cid":"") . " fid=" . (isset($fid)?"$fid":""));

if (array_key_exists ('metaaction', $_POST)) {

    switch ($_POST['metaaction']) {
    
		case  'LBL_MARK_CHANNEL_READ':
		 
			$first_unread_id=$next_unread_id='';
		 
		 // redirect to the next unread, if any.
		 $sql = "select c.id from " . getTable("item") . " i,"
		 . getTable("channels") . " c,"
		 . getTable("folders") . " f "
			  ." where i.unread & ".FEED_MODE_UNREAD_STATE
			." and !(i.unread & " . FEED_MODE_DELETED_STATE  .") ";
			
		if (hidePrivate()) {
			$sql .=" and !(i.unread & " . FEED_MODE_PRIVATE_STATE .") ";	      
		}
			
		 $sql .= " and i.cid=c.id and c.parent=f.id";
			if (getConfig('rss.config.absoluteordering')) {
			$sql .= " order by f.position asc, c.position asc";
		 } else {
			$sql .=" order by c.parent asc, c.title asc";
		 }
			
		 $res = rss_query($sql);
		 $next_ok=false;
		 while(list ($unread_id) = rss_fetch_row($res)) {
			if ($first_unread_id == '' && $unread_id != $cid) {
				$first_unread_id = $unread_id;
			}
			
			if ($unread_id != $cid && $next_ok) {
				$next_unread_id=$unread_id;
				break;
			}
			
			if ($unread_id == $cid) {
				$next_ok=true;
			}
		 }
		 if ($next_unread_id == '' && $first_unread_id != '') {
			$next_unread_id = $first_unread_id; 
		 }
		 
		 $sql = "update " .getTable("item") ." set unread = unread & ".SET_MODE_READ_STATE." where cid=$cid";
		 
		 
		if (hidePrivate()) {
			$sql .= " and !(unread & " . FEED_MODE_PRIVATE_STATE . ")";
		 }
		 
		 
		 rss_query($sql);
		 
		 //redirect
		 if ($next_unread_id == '') {	
            rss_redirect();
		 } else {
			$cid = $next_unread_id;
		 }
    
    break;
    
	// folder    
    case 'LBL_MARK_FOLDER_READ':
		$fid = $_REQUEST['folder'];
    	$sql = "update " .getTable('item') . " i, " . getTable('channels') . " c "
    	. " set i.unread = i.unread & ".SET_MODE_READ_STATE
    	. " where i.cid=c.id and c.parent=$fid";
    	//die($sql);
    	rss_query($sql);
    	$next_fid = 0;
    	$found = false;
    	$res = rss_query( " select id from " .getTable('folders') ." f order by "
		
			.(getConfig('rss.config.absoluteordering')?" f.position desc":"f.name desc")		
		);
		
    	while (list($fid__) = rss_fetch_row($res)) {
    		if ($fid__ == $fid && $next_fid > 0) {
    			$found = true;
    			break;
    		} elseif($fid__ == $fid) {			
    			$found = true;
    		}
    		$sql = "select count(*) from "
    			.getTable('item') ." i, "
    			.getTable('channels') ." c "
    			." where i.unread & " .FEED_MODE_UNREAD_STATE ." and i.cid = c.id and c.parent = $fid__";
    			if (hidePrivate()) {
					$sql .= " and !(i.unread & " . FEED_MODE_PRIVATE_STATE . ")";
		 		}
    			
    		list($c) = rss_fetch_row(rss_query($sql));
    		//echo "$fid__ -> $c\n";
    	
    		if ($c > 0) {
    			$next_fid = $fid__;
    			//echo "next -> $fid__\n";
    			if ($found) {
    				//echo "can break\n";
    				break;
    			}
    		}
    	}
    	
    	if ( $next_fid  && $found) {
    		$fid = $next_fid;
    		$sql = "select id from " . getTable('channels') ." where parent=$fid";
    		$res = rss_query($sql);
    		$cids = array();
    		while ( list($cid__) = rss_fetch_row($res)) {
    			$cids[] = $cid__;
    		}
    		
    	} else {
            rss_redirect();
    	}
			
    break;
    }

}
//echo ("cid=".(isset($cid)?"$cid":"") . " fid=" . (isset($fid)?"$fid":""));
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

if (!isset($fid)) {
	$fid=null;
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
	$cidfid = array();
	// "channel / folder mode"
	if ($cid) {
		$res = rss_query("select title,icon from " . getTable("channels") ." where id = $cid");
		list($title,$icon) = rss_fetch_row($res);
		if (isset($y) && $y > 0 && $m > 0 && $d == 0) {
			$dtitle =  (" " . TITLE_SEP ." " . rss_date('F Y',mktime(0,0,0,$m,1,$y),false));
		} elseif (isset($y) && $y > 0 && $m > 0 && $d > 0) {
			$dtitle =  (" " . TITLE_SEP ." " . rss_date('F jS, Y',mktime(0,0,0,$m,$d,$y),false));
		} else {
			$dtitle ="";
		}
		$cidfid ['cid']=$cid;
		$cidfid ['fid']=null;
	} elseif(isset($fid) && $fid) {
	
		list($title) = rss_fetch_row( rss_query("select name from " . getTable('folders') . " where id = $fid") );
		$dtitle ="";
		$cidfid ['cid']=null;
		$cidfid ['fid']=$fid;
	} else {
		$dtitle ="";
		$title = "";
		$cidfid ['cid']=null;
		$cidfid ['fid']=null;
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
   //rss_header( rss_htmlspecialchars( $title ) . $dtitle,0,$cidfid,"", HDR_NONE, $links);
   $GLOBALS['rss'] -> header = new Header( rss_htmlspecialchars( $title ) . $dtitle,0,$cidfid,"", HDR_NONE, $links);
} else {
    // "item mode"
    $res = rss_query ("select c.title, c.icon, i.title from " . getTable("channels") ." c, " 
		     .getTable("item") ." i where c.id = $cid and i.cid=c.id and i.id=$iid"
              ." and !(i.unread & " . FEED_MODE_DELETED_STATE  .") "
             );
             
    list($title,$icon,$ititle) = rss_fetch_row($res);
	
    $GLOBALS['rss'] -> header = new Header(
		 rss_htmlspecialchars($title) 
		 . " " . TITLE_SEP ." " 
		 .  rss_htmlspecialchars($ititle),
		 0,null,"", HDR_NONE, $links
		 );
}


$GLOBALS['rss'] -> feedList = new FeedList($cid);

if (getConfig('rss.meta.debug') && array_key_exists('dbg',$_REQUEST)) {
   require_once('cls/debugfeed.php');
	$dbg = new DebugFeed($cid);
	$GLOBALS['rss'] -> appendContentObject($dbg);	

} else {
	if ($cid && !(isset($cids) && is_array($cids) && count($cids))) {
   		$cids = array($cid); 
   	}
	doItems($cids,$fid,$title,$iid,$y,$m,$d,(isset($nv)?$nv:null),$show_what);
}

$GLOBALS['rss'] -> renderWithTemplate('index.php','items');

function doItems($cids,$fid,$title,$iid,$y,$m,$d,$nv,$show_what) {

		$do_show=$show_what;
	//should we honour unread-only?
	if ($show_what == SHOW_UNREAD_ONLY) {
	
		// permalink will always be printed
		if ($iid != "") {
			$do_show = SHOW_READ_AND_UNREAD;
		} else {
			// archives, folders, channels
			$sql = "select count(*) from " . getTable('item') . " where"
			." (unread & " . FEED_MODE_UNREAD_STATE .")"
		    ." and !(unread & " . FEED_MODE_DELETED_STATE  .") ";
			 
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
	
   $items = new ItemList();

	

	foreach ($cids as $cid) {
		$sqlWhere = "i.cid = $cid";
		if  ($do_show == SHOW_UNREAD_ONLY) {
			$sqlWhere .= " and (i.unread & " . FEED_MODE_UNREAD_STATE .") ";
		}		 
		if ($iid != "") {
			$sqlWhere .= " and i.id=$iid";
		}
		if ($m > 0 && $y > 0) {
			$sqlWhere .= " and if (i.pubdate is null, month(i.added)= $m , month(i.pubdate) = $m) "
			  ." and if (i.pubdate is null, year(i.added)= $y , year(i.pubdate) = $y) ";
		
			if ($d > 0) {
				 $sqlWhere .= " and if (i.pubdate is null, dayofmonth(i.added)= $d , dayofmonth(i.pubdate) = $d) ";
			}
		 }
		
		if ( $m==0 && $y==0 ) {		
			$sqlLimit = getConfig('rss.output.itemsinchannelview');
		} else {
			$sqlLimit = 9999;
		}
				  
		$sqlOrder = " order by i.unread & ".FEED_MODE_UNREAD_STATE." desc";
		if(getConfig('rss.config.datedesc')){
			$sqlOrder .= ", ts desc, i.id asc";
		} else {
			$sqlOrder .= ", ts asc, i.id asc";
		}
		
		
		$items -> populate($sqlWhere,$sqlOrder,0, $sqlLimit);
	}

	
   $severalFeeds = ($fid != null);
   if ($items -> unreadCount && $iid == "") {
    	 $items -> preRender[] = array("showViewForm",$show_what);
		 
    	 if (!$severalFeeds) {
			$items -> preRender[] = array("markReadForm",$cid);
			$title .= " " .strip_tags(sprintf(LBL_UNREAD_PF, "cid$cid","",$items -> unreadCount));
		 } else {
		 	list($fid) = rss_fetch_row(rss_query('select parent from ' .getTable('channels') . 'where id = ' .$cids[0]));
		 	$title .= " " .strip_tags(sprintf(LBL_UNREAD_PF, "cid$fid","",$items -> unreadCount));
			$items -> preRender[] = array("markFolderReadForm",$fid);
		 }
	 }
	 
	 
	
	 $items -> setTitle($title);
	 $items -> setRenderOptions(($severalFeeds? (IL_NO_COLLAPSE | IL_FOLDER_VIEW):(IL_CHANNEL_VIEW)));
	 

    if ($nv != null) {
		list($prev,$succ,$up) = $nv;
		
		$readMoreNav = "";
		if($prev != null) {
			$lbl = $prev['lbl'];
			if (strlen($lbl) > 40) {
				$lbl = substr($lbl,0,37) . "...";
			}
			$readMoreNav .= "<a href=\"".$prev['url']."\" class=\"fl\">".LBL_NAV_PREV_PREFIX ."$lbl</a>\n";
		}
		
		if($succ != null) {
			$lbl = $succ['lbl'];
			if (strlen($lbl) > 40) {
				$lbl = substr($lbl,0,37) . "...";
			}
			$readMoreNav .= "<a href=\"".$succ['url']."\" class=\"fr\">$lbl".LBL_NAV_SUCC_POSTFIX."</a>\n";
		}
		
		if ($readMoreNav != "") {
			 $items->afterList = "<div class=\"readmore\">$readMoreNav" 
				. "<hr class=\"clearer hidden\"/>\n</div>\n";
		}	
	}   
	$GLOBALS['rss'] -> appendContentObject($items);
  
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

	//echo "$cid,$iid,$y,$m,$d";
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
	} elseif ($cid) {
	   $currentView = "feed";
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
						  'lbl' => rss_date('F jS',$row['ts_']) . " (".$row['cnt_']." " . ($row['cnt_'] > 1? LBL_ITEMS:LBL_ITEM) .")"
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
						  'lbl' => rss_date('F Y',$row['ts_']) . " (".$row['cnt_']." " . ($row['cnt_'] > 1? LBL_ITEMS:ITEM) .")"
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
							  'lbl' => rss_date('F jS',$row['ts_']) . " (".$row['cnt_']." " . ($row['cnt_'] > 1? LBL_ITEMS:LBL_ITEM) .")"
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
							  'lbl' => rss_date('F Y',$row['ts_']) . " (".$row['cnt_']." ". ($row['cnt_'] > 1? LBL_ITEMS:LBL_ITEM) .")"
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
					'lbl' => rss_date('F Y',$ts)
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
		      
				if(getConfig('rss.config.datedesc')){
					$sql .= " order by ts_ desc, i.id asc";
				}else{
					$sql .= " order by ts_ asc, i.id asc";
				}
				  
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
									. (getConfig('rss.output.usemodrewrite') ? 
									preg_replace("/[^A-Za-z0-9\.]/","_",$ptitle_):
									"&amp;iid=$piid_"),
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
								  	. (getConfig('rss.output.usemodrewrite') ? 
								  	preg_replace("/[^A-Za-z0-9\.]/","_",$title_) :
								  	"&amp;iid=$iid_"),
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
					'lbl' => rss_date('F jS',$ts)
					);
			
				
			break;
			
			case 'feed':
			
				$sql = "select "
				  ." c.id, c.title "
				  ." from " 
				  .getTable("channels") ." c, " 
				  . getTable("folders") ." d "
				  ." where d.id = c.parent ";
		
						
				if (hidePrivate()) {
					$sql .=" and !(c.mode & " . FEED_MODE_PRIVATE_STATE .") ";	      
				}
				$sql .= " and !(c.mode & " .  FEED_MODE_DELETED_STATE .") ";
	 
				if (getConfig('rss.config.absoluteordering')) {
					$sql .=" order by d.position asc, c.position asc";
				} else {
					$sql .=" order by c.parent asc, c.title asc";
				}


				$res  = rss_query($sql);
				$pcid = $ptitile = null;
				$cidname=array();
				$cids=array();
				while (list ($cid_,$title_)=rss_fetch_row($res)) {
					$cids[]=$cid_;
					$cidname[]=array($cid_,$title_);
				}
				$key = array_search($cid,$cids);
				if ($key !== NULL && $key !== FALSE) {
					//echo "$key " .count($cidname);
					if ($key+1 < count($cidname)) {
						list($cid_,$title_) = $cidname[$key+1];
						$prev = array(
						   'url' => getPath(). 
						   ( getConfig('rss.output.usemodrewrite') ?
							preg_replace("/[^A-Za-z0-9\.]/","_",$title_) ."/"
							:"feed.php?channel=$cid_") ,
							'lbl' => htmlentities( $title_,ENT_COMPAT,"UTF-8" )
						);
					}
					if ($key > 0) {
						list($cid_,$title_) = $cidname[$key-1];
						$succ = array(
						   'url' => getPath(). 
						   ( getConfig('rss.output.usemodrewrite') ?
							preg_replace("/[^A-Za-z0-9\.]/","_",$title_) ."/"
							:"feed.php?channel=$cid_") ,
							'lbl' => htmlentities( $title_,ENT_COMPAT,"UTF-8" )						
						);
					}
					
				}
			
			break;
		}
		return array($prev,$succ, $up);
	}
	
    return null;

}

function markReadForm($cid) {
	if (hidePrivate()) {
		return;
	}
	
	if (!defined('MARK_READ_FEED_FORM')) {
		define ('MARK_READ_FEED_FORM',$cid);
	}
  	echo "\n\n<form action=\"". getPath() ."feed.php\" method=\"post\">\n"
  	  ."\t<p><input type=\"submit\" name=\"action\" accesskey=\"m\" value=\"". LBL_MARK_CHANNEL_READ ."\"/>\n"
  	  ."\t<input type=\"hidden\" name=\"metaaction\" value=\"LBL_MARK_CHANNEL_READ\"/>\n"
  	  ."\t<input type=\"hidden\" name=\"channel\" value=\"$cid\"/></p>\n"
  	  ."</form>";
}

function markFolderReadForm($fid) {
	if (hidePrivate()) {
		return;
	}
	
	if (!defined('MARK_READ_FOLDER_FORM')) {
		define ('MARK_READ_FOLDER_FORM',$fid);
	}	
  	echo "\n\n<form action=\"". getPath() ."feed.php\" method=\"post\">\n"
  	  ."\t<p><input type=\"submit\" name=\"action\" accesskey=\"m\" value=\"". LBL_MARK_FOLDER_READ ."\"/>\n"
  	  ."\t<input type=\"hidden\" name=\"metaaction\" value=\"LBL_MARK_FOLDER_READ\"/>\n"
  	  ."\t<input type=\"hidden\" name=\"folder\" value=\"$fid\"/></p>\n"
  	  ."</form>";
  	  
}

function debugFeed($cid) {
}

?>
