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
#
###############################################################################



require_once('init.php');
rss_require('extlib/rss_fetch.inc');

define ('ACT_NAV_PREV_PREFIX','&larr;&nbsp;');
define ('ACT_NAV_SUCC_POSTFIX','&nbsp;&rarr;');




// Show unread items on the front page?
// default to the config value, user can override this via a cookie
$show_what = (getConfig('rss.output.frontpage.mixeditems') ?
	SHOW_READ_AND_UNREAD : SHOW_UNREAD_ONLY);

if (array_key_exists(SHOW_WHAT,$_POST)) {
    $show_what = $_POST[SHOW_WHAT];
    $period = time()+COOKIE_LIFESPAN;
    setcookie(SHOW_WHAT, $show_what , $period, getPath());
}
elseif (array_key_exists(SHOW_WHAT,$_COOKIE)) {
    $show_what = $_COOKIE[SHOW_WHAT];
}

if (array_key_exists('chkPrivate', $_POST)) {
  $show_private = empty($_POST['chkPrivate']) ? 0 : $_POST['chkPrivate'];
  setcookie('chkPrivate', $show_private, time()+COOKIE_LIFESPAN, getPath());
} else {
  $show_private = empty($_COOKIE['chkPrivate']) ? 0 : $_COOKIE['chkPrivate'];
}

rss_user_set_show_private($show_private);

$y=$m=$d=0;
if (
    getConfig('rss.output.usemodrewrite')
    && array_key_exists('channel',$_REQUEST)
    // this is nasty because a numeric feed title could break it
    && !is_numeric($_REQUEST['channel'])
) {
	$sqlid = preg_replace('#'.RSS_URI_SEPARATOR.'#','_',
		sanitize($_REQUEST['channel'] , RSS_SANITIZER_SIMPLE_SQL )
		);

    $sql = "select id from " . getTable("channels") ." where title like '$sqlid'";
    if (hidePrivate()) {
        $sql .=" and not(mode & " . RSS_MODE_PRIVATE_STATE .") ";
    }

    // don't hide deprecated items becuase we want items of deprecated feeds to be accessible
    // $sql .= " and not(mode & " . RSS_MODE_DELETED_STATE . ") ";

//	die($sql);
    $res =  rss_query( $sql );
    //echo $sql;
    if ( rss_num_rows ( $res ) >= 1) {
        list($cid) = rss_fetch_row($res);
    } else {
        $cid = "";

        // is this a folder?
        $sql = "select c.id, c.parent from ". getTable('channels')." c "
               ." inner join " . getTable('folders') . " f on f.id = c.parent "
               ." where f.name like '$sqlid' and f.id > 0";

        if (hidePrivate()) {
            $sql .=" and not(c.mode & " . RSS_MODE_PRIVATE_STATE .") ";
        }
        $sql .= " and not(c.mode & " .  RSS_MODE_DELETED_STATE .") ";

        $res = rss_query( $sql );
        if ( rss_num_rows ( $res ) > 0) {
            $cids = array();
            while (list ($cid__,$fid__) = rss_fetch_row($res)) {
                $cids[] = $cid__;
                $fid = $fid__;
            }
        } else {
            // maybe it's a virtual folder?
            $sql = "select c.id, m.tid from ". getTable('channels')." c "
                   ."inner join " . getTable('metatag') . " m on m.fid = c.id "
                   ."inner join " . getTable('tag') . " t on t.id = m.tid "
                   . "where m.ttype = 'channel' "
                   . "and t.tag like '$sqlid'";

            if (hidePrivate()) {
                $sql .=" and not(c.mode & " . RSS_MODE_PRIVATE_STATE .") ";
            }
            $sql .= " and not(c.mode & " .  RSS_MODE_DELETED_STATE .") ";

            $res = rss_query( $sql );
            if ( rss_num_rows ( $res ) > 0) {
                $cids = array();
                while (list ($cid__,$vfid__) = rss_fetch_row($res)) {
                    $cids[] = $cid__;
                    $vfid = $vfid__;
                }
            }
        }
    }

    // date ?
    if ($cid != ""
            && array_key_exists('y',$_REQUEST) && $_REQUEST['y'] != "" && is_numeric($_REQUEST['y'])
            && array_key_exists('m',$_REQUEST) && $_REQUEST['m'] != "" && is_numeric($_REQUEST['m'])) {
        $y = (int) $_REQUEST['y'];
        if ($y < 1000)
            $y+=2000;

        $m =  (int)$_REQUEST['m'];
        if ($m > 12) {
            $m = date("m");
        }

        $d =  (int)$_REQUEST['d'];
        if ($d > 31) {
            $d = date("d");
        }
    }



    // lets see if theres an item id as well
    $iid = "";
    if ($cid != "" && array_key_exists('iid',$_REQUEST) && $_REQUEST['iid'] != "") {
        $sqlid =  preg_replace("/[_';]/","%",sanitize($_REQUEST['iid'],RSS_SANITIZER_SIMPLE_SQL|RSS_SANITIZER_NO_SPACES));
        $sql = "select id from " .getTable("item") ." i where i.title like '$sqlid' and i.cid=$cid";
        if ($m > 0 && $y > 0) {
            $sql .= " and month(ifnull(i.pubdate,i.added))= $m "
                    ." and year(ifnull(i.pubdate, i.added))= $y ";

            if ($d > 0) {
                $sql .= " and dayofmonth(ifnull(i.pubdate,i.added))= $d ";
            }

        }

        if (hidePrivate()) {
            $sql .=" and not(i.unread & " . RSS_MODE_PRIVATE_STATE .") ";
        }

        $sql .=" and not(i.unread & " . RSS_MODE_DELETED_STATE  .") ";
        $sql .=" order by i.added desc, i.id asc";

        $res =  rss_query( $sql );

        if ( rss_num_rows ( $res ) >0) {
            list($iid) = rss_fetch_row($res);
        }
    }
    // no mod rewrite: ugly but effective
}
elseif (array_key_exists('channel',$_REQUEST) || array_key_exists('folder',$_REQUEST) || array_key_exists('vfolder',$_REQUEST)) {
    $cid= (array_key_exists('channel',$_REQUEST))?sanitize($_REQUEST['channel'],RSS_SANITIZER_NUMERIC):"";
    $iid= (array_key_exists('iid',$_REQUEST))?sanitize($_REQUEST['iid'],RSS_SANITIZER_NUMERIC):"";
    $fid= (array_key_exists('folder',$_REQUEST))?sanitize($_REQUEST['folder'],RSS_SANITIZER_NUMERIC):"";
    $vfid= (array_key_exists('vfolder',$_REQUEST))?sanitize($_REQUEST['vfolder'],RSS_SANITIZER_NUMERIC):"";
	
    $y= (array_key_exists('y',$_REQUEST))?sanitize($_REQUEST['y'],RSS_SANITIZER_NUMERIC):"0";
    $m= (array_key_exists('m',$_REQUEST))?sanitize($_REQUEST['m'],RSS_SANITIZER_NUMERIC):"0";
    $d= (array_key_exists('d',$_REQUEST))?sanitize($_REQUEST['d'],RSS_SANITIZER_NUMERIC):"0";

    if ($fid) {
        $sql = "select c.id from ". getTable('channels')." c "
               ." where c.parent=$fid and c.parent > 0";
        $sql .= " and not(c.mode & " .  RSS_MODE_DELETED_STATE .") ";

        if (hidePrivate()) {
            $sql .=" and not(c.mode & " . RSS_MODE_PRIVATE_STATE .") ";
        }
        $res = rss_query( $sql );

        if ( rss_num_rows ( $res ) > 0) {
            $cids = array();
            while (list ($cid__) = rss_fetch_row($res)) {
                $cids[] = $cid__;
            }
        }
    }
    elseif ($vfid) {
        $sql = "select c.id, m.tid from ". getTable('channels')." c "
               ."inner join " . getTable('metatag') . " m on m.fid = c.id "
               ."inner join " . getTable('tag') . " t on t.id = m.tid "
               . "where m.ttype = 'channel'";
        // $vfid can be numeric (t.id) or alphabetic (t.tag)
        if(is_numeric($vfid)) {
            $sql .= "and t.id = $vfid";
        } else {
            $sql .= "and t.tag like '$vfid'";
        }
        $sql .= " and not(c.mode & " .  RSS_MODE_DELETED_STATE .") ";

        if (hidePrivate()) {
            $sql .=" and not(c.mode & " . RSS_MODE_PRIVATE_STATE .") ";
        }
        $res = rss_query( $sql );

        if ( rss_num_rows ( $res ) > 0) {
            $cids = array();
            while (list ($cid__,$vfid__) = rss_fetch_row($res)) {
                $cids[] = $cid__;
                $vfid = $vfid__;
            }
        }
    }
    elseif ($cid) {
        if (hidePrivate()) {
            $sql = "select id from ". getTable('channels')." where id=$cid ";
            $sql .=" and not(mode & " . RSS_MODE_PRIVATE_STATE .") ";
            list ($cid) = rss_fetch_row(rss_query($sql));
        }
    }
}
elseif (
    array_key_exists('y',$_REQUEST) && $_REQUEST['y'] != "" && is_numeric($_REQUEST['y'])
    && array_key_exists('m',$_REQUEST) && $_REQUEST['m'] != "" && is_numeric($_REQUEST['m'])
    && array_key_exists('d',$_REQUEST) && $_REQUEST['d'] != "" && is_numeric($_REQUEST['d'])
)   {

    $y = (int) sanitize($_REQUEST['y'],RSS_SANITIZER_NUMERIC);
    if ($y < 1000)
        $y+=2000;

    $m =  (int) sanitize($_REQUEST['m'],RSS_SANITIZER_NUMERIC);
    if ($m > 12) {
        $m = date("m");
    }

    $d =  (int) sanitize($_REQUEST['d'],RSS_SANITIZER_NUMERIC);
    if ($d > 31) {
        $d = date("d");
    }
    $iid = $cid  = null;
}




// If we have no channel-id something went terribly wrong.
// Send a 404. 
if (
    // channel id:
    (
        // not set
        !isset($cid) 	||
        // ...or empty
        $cid == "" 		||
        // or not numeric while mod_rewrite is off
        (
            !getConfig('rss.output.usemodrewrite') && !is_numeric($cid)
        )
    )

    &&
    // folder id
    (
        // not set
        !isset($cids) ||
        // not an array of ids
        !is_array($cids) ||
        // zero elements
        !count($cids)
    )

    &&
    // virtual folder id
    (!isset($vfid))

    &&
    // date?
    ($d == 0 && $m == 0 && $y == 0)
) {
	rss_404();
	exit;
}
//echo ("cid=".(isset($cid)?"$cid":"") . " fid=" . (isset($fid)?"$fid":""));

if (isLoggedIn() && array_key_exists ('metaaction', $_REQUEST)) {

    if (array_key_exists('markreadids',$_POST)) {
        $IdsToMarkAsRead = explode(",",rss_real_escape_string($_POST['markreadids']));
        //var_dump($IdsToMarkAsRead);
    } else {
        $IdsToMarkAsRead = array();
    }
    switch ($_REQUEST['metaaction']) {

    case  'ACT_MARK_CHANNEL_READ':

        /** mark channel as read **/
        $sql = "update " .getTable("item")
               ." set unread = unread & ".SET_MODE_READ_STATE." where cid=$cid";
        if (hidePrivate()) {
            $sql .= " and not(unread & " . RSS_MODE_PRIVATE_STATE . ")";
        }
        if (count($IdsToMarkAsRead)) {
            $sql .= " and id in (" . implode(',',$IdsToMarkAsRead) .")";
        }
        rss_query($sql);

        rss_invalidate_cache();

        /* Redirect! If this feed has more unread items, self-redirect */

        $sql = "select count(*) from " .getTable("item") . " i "
               ." where i.unread & " .RSS_MODE_UNREAD_STATE
               ." and i.cid=$cid"
               ." and not(i.unread & " . RSS_MODE_DELETED_STATE  .") "
               ;
        if (hidePrivate()) {
            $sql .=" and not(i.unread & " . RSS_MODE_PRIVATE_STATE .") ";
        }
        list($hasMoreUnreads) = rss_fetch_row(rss_query($sql));


        //more unread items in this feed?
        if ($hasMoreUnreads) {
            $next_unread_id=$cid;

        } else {

            /*
            	Find where we should redirect
            	 - The next feed in the list with unread items, or, failing that:
            	 - The first feed in the list with unread items, or, faling that:
            	 - The main page
            */

            // 1: build a list of all feeds:
            $feeds = array();
            $sql = "select c.id from " . getTable('channels') . " c "
                   ."inner join " . getTable('folders') . " f on f.id = c.parent "
                   . "where not (c.mode & " . RSS_MODE_DELETED_STATE .") ";

            if (hidePrivate()) {
                $sql .= " and not (c.mode & " . RSS_MODE_PRIVATE_STATE . ") ";
            }
            if (getConfig('rss.config.absoluteordering')) {
                $sql .= " order by f.position asc, c.position asc";
            } else {
                $sql .=" order by f.name asc, c.title asc";
            }
            $res = rss_query($sql);
            while (list($cid__) = rss_fetch_row($res)) {
                $feeds[$cid__] = 0;
            }

            // 2: Get the unread count for each feed:
            $sql = "select cid, count(*) from " .getTable('item')
                   ." where (unread & ".RSS_MODE_UNREAD_STATE . ") "
                   ." and not (unread & " .RSS_MODE_DELETED_STATE . ") ";
            if (hidePrivate()) {
                $sql .= " and not (unread & " . RSS_MODE_PRIVATE_STATE . ") ";
            }
            $sql .= " group by cid";
            $res = rss_query($sql);
            while (list($cid__,$uc__) = rss_fetch_row($res)) {
                // this makes sure only the feeds that were gathered in the
                // last query are put into this array.. fixes #305
                if(array_key_exists($cid__, $feeds)) {
                    $feeds[$cid__] = $uc__;
                }
            }

            // 3: iterate over the feeds and see where we should redirect.
            $found = false;
            $first_unread_id = $next_unread_id = 0;
            foreach($feeds as $cid__ => $cnt) {
                // reached the feed we're coming from?
                if ($cid == $cid__) {
                    $found = true;
                }
                // if not yet, get a hold of the first in the list with unread items
                if ($cnt && !$first_unread_id) {
                    $first_unread_id = $cid__;
                }

                // passed the previous feed? We got a winner!
                if ($cnt && $found) {
                    $next_unread_id = $cid__;
                    break;
                }
            }

            // found none after the previous feed, but there is on on top
            if (!$next_unread_id && $first_unread_id) {
                $next_unread_id = $first_unread_id;
            }
        }

        //redirect
        if (!$next_unread_id) {
            rss_redirect();
        } else {
            $cid = $next_unread_id;
        }

        break;

        // folder
    case 'ACT_MARK_FOLDER_READ':
        $fid = sanitize($_REQUEST['folder'],RSS_SANITIZER_NUMERIC);
        $rs  = rss_query("select id from " .getTable('channels') . " where parent=$fid");
        $cids_ = array();
        while(list($cid_) = rss_fetch_row($rs)) {
        	$cids_[]=$cid_;
        }
        
        
        $sql = "update " .getTable('item')
               . " set unread = unread & ".SET_MODE_READ_STATE
               . " where cid  in (" .implode(',', $cids_) . ") ";
		  unset($cids_);
        if (count($IdsToMarkAsRead)) {
            $sql .= " and id in (" . implode(',',$IdsToMarkAsRead) .")";
        }

        //die($sql);
        rss_query($sql);

        rss_invalidate_cache();

        $next_fid = $first_fid = 0;
        $found = false;
        $res = rss_query( " select id from " .getTable('folders') ." f order by "
                          .(getConfig('rss.config.absoluteordering')?" f.position asc":"f.name asc")
                        );

        while (list($fid__) = rss_fetch_row($res)) {
            if($fid__ == $fid) {
                $found = true;
            }
			
			if( $found || !$first_fid ) {
				$sql = "select count(*) from " . getTable('item') ." i "
					   ."inner join " . getTable('channels') ." c on c.id = i.cid "
					   ." where i.unread & " .RSS_MODE_UNREAD_STATE ." and c.parent = $fid__";
				if (hidePrivate()) {
					$sql .= " and not(i.unread & " . RSS_MODE_PRIVATE_STATE . ")";
				}
	
				list($c) = rss_fetch_row(rss_query($sql));
				//echo "$fid__ -> $c\n";

				if ($c > 0) {
					if (!$first_fid)
						$first_fid = $fid__;
					if ($found) {
						$next_fid = $fid__;
						break;
					}
				}
			}
        }
		
		if( !$next_fid && $first_fid )
			$next_fid = $first_fid;

        if ( $next_fid ) {
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
        // virtual folder - code extremely similar to __('Mark These Items as Read')
    case 'ACT_MARK_VFOLDER_READ':
        $vfid = sanitize($_REQUEST['vfolder'],RSS_SANITIZER_NUMERIC);
        
        $rs  = rss_query(
        	"select fid from " .getTable('metatag') . "m  "
        	." where m.ttype = 'channel' and m.tid = $vfid");
        $fids_ = array();
        while(list($fid_) = rss_fetch_row($rs)) {
        	$fids_[]=$fid_;
        }
        
        $sql = "update " .getTable('item') 
	  . " set unread = unread & ".SET_MODE_READ_STATE
	  . " where cid in (" .implode(',',$fids_). ")";

        if (count($IdsToMarkAsRead)) {
            $sql .= " and id in (" . implode(',',$IdsToMarkAsRead) .")";
        }

        rss_query($sql);

        rss_invalidate_cache();

        // find next virtual folder to redirect to
        $next_vfid = $first_vfid = 0;
        $found = false;
        $res = rss_query("select distinct tid from " .getTable('metatag') ." m "
             . "inner join " . getTable('tag') ."t on t.id = m.tid order by t.tag asc");
        while (list($tid__) = rss_fetch_row($res)) {
            if ($tid__ == $vfid) {
                $found = true;
            }
			
			if( $found || !$first_vfid ) {
				// check for unread items in next virtual folder
				$sql = "select count(distinct(i.id)) as cnt from "
					   .getTable('metatag') ." left join "
					   .getTable('item') . "i on (i.cid=fid) "
					   ."where tid = $tid__ and ttype = 'channel' "
					   ." and (i.unread & ".RSS_MODE_UNREAD_STATE.") "
					   ."and not(i.unread & ".RSS_MODE_DELETED_STATE.")";
				if (hidePrivate()) {
					$sql .= " and not(i.unread & " . RSS_MODE_PRIVATE_STATE . ")";
				}
				list($c) = rss_fetch_row(rss_query($sql));
				if ($c > 0) {
					if (!$first_vfid)
						$first_vfid = $tid__;
					if ($found) {
						$next_vfid = $tid__;
						break;
					}
				}
			}
        }
		
		if( !$next_vfid && $first_vfid )
			$next_vfid = $first_vfid;

        if($next_vfid) {
            $vfid = $next_vfid;
            $sql = "select distinct(fid) from " . getTable('metatag') . " where tid = $vfid";
            $res = rss_query($sql);
            $cids = array();
            while(list($cid__) = rss_fetch_row($res)) {
                $cids[] = $cid__;
            }
        } else {
            rss_redirect();
        }
        break;
    }

	if( array_key_exists ('redirectto', $_REQUEST)) {
		header("Location: " .
			   $_REQUEST['redirectto']);
		exit();
	}
}
//echo ("cid=".(isset($cid)?"$cid":"") . " fid=" . (isset($fid)?"$fid":""));
assert(
    (isset($cid) && is_numeric($cid)) ||
    (isset($fid) && isset($cids) && is_array($cids) && count($cids)) ||
    (isset($vfid) && isset($cids) && is_array($cids) && count($cids)) ||
    (!isset($cid) && ($y || $m))
);

$itemFound = true;
if ($iid != "" && !is_numeric($iid)) {
    //item was deleted
    $itemFound = false;
    $iid = "";
}

// make sure the variables passed to makeName are initialized
if (!isset($fid)) {
    $fid=null;
}
if (!isset($vfid)) {
    $vfid=null;
}
if (!isset($cids)) {
    $cids=null;
}
//precompute the navigation hints, which will be passed to the header as <link>s
$links = NULL;
if (($cid || $fid || $vfid || ($y && $m && $d)) && ($nv = makeNav($cid,$iid,$y,$m,$d,$fid,$vfid,$cids)) != null) {
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
    $prepend = false;
    if ($cid) {
        $res = rss_query("select title,icon from " . getTable("channels") ." where id = $cid");
        list($title,$icon) = rss_fetch_row($res);
        if (isset($y) && $y > 0 && $m > 0 && $d == 0) {
            $dtitle =  (" " . TITLE_SEP ." " . rss_locale_date('%B %Y',mktime(0,0,0,$m,1,$y),false));
            $prepend=true;
        }
        elseif (isset($y) && $y > 0 && $m > 0 && $d > 0) {
            $prepend=true;
            $dtitle =  (" " . TITLE_SEP ." " . rss_locale_date('%B %e, %Y',mktime(0,0,0,$m,$d,$y),false));
        }
        else {
            $dtitle ="";
        }
        $cidfid ['cid']=$cid;
        $cidfid ['fid']=null;
    }
    elseif($fid) {
        list($title) = rss_fetch_row( rss_query("select name from " . getTable('folders') . " where id = $fid") );
        $dtitle ="";
        $cidfid ['cid']=null;
        $cidfid ['fid']=$fid;
    }
    elseif($vfid) {
        list($title) = rss_fetch_row(rss_query("select tag from " . getTable('tag') . " where id = $vfid"));
        $dtitle = "";
        $cidfid ['cid']=null;
        $cidfid ['fid']=null;
    }
    elseif($y && $m && $d) {
        $dtitle =  ( rss_locale_date('%B %e, %Y',mktime(0,0,0,$m,$d,$y),false));
        $cidfid ['cid']=null;
        $cidfid ['fid']=null;
        $title ="";
    }
    else {
        $dtitle ="";
        $title = "";
        $cidfid ['cid']=null;
        $cidfid ['fid']=null;
    }


    if ($links) {
        foreach ($links as $rel => $val) {
            if (($lbl = $links[$rel]['title']) != "" && $prepend) {
                $links[$rel]['title'] = htmlentities( $title,ENT_COMPAT,"UTF-8" ) . " " . TITLE_SEP ." " . $lbl;
            }
            elseif (($lbl = $links[$rel]['title']) != "" && !$prepend) {
                $links[$rel]['title'] = htmlentities( $lbl, ENT_COMPAT,"UTF-8" );
            }
            elseif ($title) {
                $links[$rel]['title'] = htmlentities( $title,ENT_COMPAT,"UTF-8" );
            }
        }
    }
    //rss_header( rss_htmlspecialchars( $title ) . $dtitle,0,$cidfid,"", HDR_NONE, $links);
    $GLOBALS['rss'] -> header = new Header( rss_htmlspecialchars( $title ) . $dtitle,0,$cidfid,"", HDR_NONE, $links);
} else {
    // "item mode"
    $res = rss_query ("select c.title, c.icon, i.title from " . getTable("channels") ." c "
                      ."inner join " . getTable("item") ." i on i.cid = c.id where c.id = $cid and i.id=$iid"
                      ." and not(i.unread & " . RSS_MODE_DELETED_STATE  .") "
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

if (array_key_exists('dbg',$_GET)) {
    require_once('cls/debugfeed.php');
    $dbg = new DebugFeed($cid);
    $GLOBALS['rss'] -> appendContentObject($dbg);

} else {
    if ($cid && !(isset($cids) && is_array($cids) && count($cids))) {
        $cids = array($cid);
    }
    if(!isset($vfid))
        $vfid = null;
    doItems($cids,$fid,$vfid,$title,$iid,$y,$m,$d,(isset($nv)?$nv:null),$show_what,$show_private);
}

$GLOBALS['rss'] -> renderWithTemplate('index.php','items');

function doItems($cids,$fid,$vfid,$title,$iid,$y,$m,$d,$nv,$show_what,$show_private) {

    $do_show=$show_what;

    //should we honour unread-only?
    if ($show_what == SHOW_UNREAD_ONLY) {

        // permalink will always be printed
        if ($iid != "") {
            $do_show = SHOW_READ_AND_UNREAD;
        } else {
            // archives, folders, channels
            $sql = "select count(*) from " . getTable('item') . " where"
                   ." (unread & " . RSS_MODE_UNREAD_STATE .")"
                   ." and not(unread & " . RSS_MODE_DELETED_STATE  .") ";

            //archive?
            if ($m > 0 && $y > 0) {
                $sql .= " and month(ifnull(pubdate,added))= $m "
                        ." and year(ifnull(pubdate, added))= $y ";
                if ($d > 0) {
                    $sql .= " and dayofmonth(ifnull(pubdate,added))= $d ";
                }
            }
            if ($cids && count($cids)) {
                $sql .= " and cid in (".implode(',',$cids).")";
            }
            list($unreadCount) = rss_fetch_row(rss_query($sql));
            if ($unreadCount == 0) {
                $do_show = SHOW_READ_AND_UNREAD;
            }

        }
		}

    $items = new ItemList();
    $severalFeeds = (($fid != null) || ($vfid != null));

    if ($severalFeeds && !getConfig('rss.config.feedgrouping')) {
        $sqlWhere = "(";
        foreach ($cids as $cid) {
            $sqlWhere .= " i.cid = $cid or ";
        }
        $sqlWhere .= " 1=0) ";
        $hint = ITEM_SORT_HINT_MIXED;
        if  ($do_show == SHOW_UNREAD_ONLY) {
            $sqlWhere .= " and (i.unread & " . RSS_MODE_UNREAD_STATE .") ";
            $hint = ITEM_SORT_HINT_UNREAD;
        }

        // how many items should we display in a folder view?
        // default to numitemsonpage.
        $cnt = getConfig('rss.output.frontpage.numitems');
        // if that is set to zero, use itemsinchannelview times the number of feeds in the folder
        if ($cnt == 0) {
            $cnt = count($cids) * getConfig('rss.output.itemsinchannelview');
        }
        // should that be zero too, go for a fixed value
        if ($cnt == 0) {
            // arbitrary!
            $cnt = 50;
        }
        $items -> populate($sqlWhere, "", 0, $cnt, $hint);

    } else {
        if (!isset($cids)) {
            $cids = array(-1);
        }
        foreach ($cids as $cid) {
            $hint = ITEM_SORT_HINT_MIXED;
            if ($cid > -1) {
                $sqlWhere = "i.cid = $cid";
                if  ($do_show == SHOW_UNREAD_ONLY) {
                    $sqlWhere .= " and (i.unread & " . RSS_MODE_UNREAD_STATE .") ";
                    $hint = ITEM_SORT_HINT_UNREAD;
                }
            } else {
                $sqlWhere = " 1 = 1 ";
            }
            if ($iid != "") {
                $sqlWhere .= " and i.id=$iid";
            }
            if ($m > 0 && $y > 0) {
                $sqlWhere .= " and month(ifnull(i.pubdate,i.added))= $m "
                             ." and year(ifnull(i.pubdate, i.added))= $y ";

                if ($d > 0) {
                    $sqlWhere .= " and dayofmonth(ifnull(i.pubdate,i.added))= $d ";
                }
            }
            if ( $m==0 && $y==0 ) {
                $sqlLimit = getConfig('rss.output.itemsinchannelview');
            } else {
		$sqlLimit =  RSS_DB_MAX_QUERY_RESULTS;
	    }
            /*
            $sqlOrder = " order by i.unread & ".RSS_MODE_UNREAD_STATE." desc";
            if(getConfig("rss.config.datedesc")){
            	$sqlOrder .= ", ts desc, i.id asc";
            } else {
            	$sqlOrder .= ", ts asc, i.id asc";
            }
            */
            $sqlOrder = "";
            $items -> populate($sqlWhere,$sqlOrder,0, $sqlLimit, $hint, true);
        }
    }


    if ($items -> unreadCount && $iid == "") {
        $items -> preRender[] = array("showViewForm",array($show_what,$show_private));

        if (!$severalFeeds) {
            $items -> preRender[] = array("markReadForm",$cid);
            $title .= " " .sprintf(__('<strong id="%s" style="%s">(%d unread)</strong>'), "ucnt","",$items -> unreadCount);
        } else {
            if(!$vfid) {
                list($fid) = rss_fetch_row(rss_query('select parent from ' .getTable('channels') . 'where id = ' .$cids[0]));
                $title .= " " .sprintf(__('<strong id="%s" style="%s">(%d unread)</strong>'), "ucnt","",$items -> unreadCount);
                $items -> preRender[] = array("markFolderReadForm",$fid);
            } else {
                list($fid) = $vfid;
                $title .= " " .sprintf(__('<strong id="%s" style="%s">(%d unread)</strong>'), "ucnt","",$items -> unreadCount);
                $items -> preRender[] = array("markVirtualFolderReadForm",$vfid);
            }
        }
    }



    $items -> setTitle($title);
    if ($severalFeeds) {
        $items -> setRenderOptions(IL_NO_COLLAPSE | IL_FOLDER_VIEW);
    }
    elseif ($cid && $cid > -1) {
        $items -> setRenderOptions(IL_CHANNEL_VIEW);
    }
    else {
        $items -> setRenderOptions(IL_NO_COLLAPSE | IL_FOLDER_VIEW);
    }

    $items -> setRenderOptions(IL_TITLE_NO_ESCAPE);

    if ($nv != null) {
        list($prev,$succ,$up) = $nv;

        $readMoreNav = "";
        if($prev != null) {
            $lbl = $prev['lbl'];
            if (function_exists('mb_strlen') && function_exists('mb_substr') && mb_strlen($lbl) > 40) {
            	$lbl = mb_substr($lbl,0,37) . "...";
            } elseif (strlen($lbl) > 40) {
                $lbl = substr($lbl,0,37) . "...";
            }
            $readMoreNav .= "<a href=\"".$prev['url']."\" class=\"fl\">".ACT_NAV_PREV_PREFIX ."$lbl</a>\n";
        }

        if($succ != null) {
            $lbl = $succ['lbl'];
            if (function_exists('mb_strlen') && function_exists('mb_substr') && mb_strlen($lbl) > 40) {
            	$lbl = mb_substr($lbl,0,37) . "...";
            } elseif (strlen($lbl) > 40) {
                $lbl = substr($lbl,0,37) . "...";
            }
            $readMoreNav .= "<a href=\"".$succ['url']."\" class=\"fr\">$lbl".ACT_NAV_SUCC_POSTFIX."</a>\n";
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
function makeNav($cid,$iid,$y,$m,$d,$fid,$vfid,$cids) {

    //echo "X-info: $cid,$iid,$y,$m,$d,$fid,$vfid,$cids";
    $currentView = null;
    $prev = $succ = $up = null;
    if (isset($_REQUEST['channel'])) {
        $escaped_title = rss_uri($_REQUEST['channel']); //preg_replace("/[^A-Za-z0-9\.]/","_",$_REQUEST['channel']);
    } else {
        $escaped_title = null;
    }
    // where are we anyway?
    if ($y > 0 && $m > 0 && $d > 0) {
        if ($iid != "") {
            $currentView = 'item';
        } else {
            $currentView = 'day';
        }
    }
    elseif ($y > 0 && $m > 0 && $d == 0) {
        $currentView = 'month';
    }
    elseif ($cids) {
        if ($fid) {
            $currentView = "folder";
        }
        elseif ($vfid) {
            $currentView = "cat";
        }
    }
    elseif ($cid) {
        $currentView = "feed";
    }

    if ($currentView) {

        switch ($currentView) {
        case 'month':
        case 'day':

            if ($currentView == 'day') {
                $ts_p = mktime(23,59,59,$m,$d-1,$y);
                $ts_s = mktime(0,0,0,$m,$d,$y);
            }
            elseif($currentView == 'month') {
                $ts_p = mktime(0,0,0,$m+1,0,$y);
                $ts_s = mktime(0,0,0,$m,1,$y);
            }

            $sql_succ = " select "
                        ." UNIX_TIMESTAMP( ifnull(i.pubdate, i.added)) as ts_, "
                        ." year( ifnull(i.pubdate, i.added)) as y_, "
                        ." month( ifnull(i.pubdate, i.added)) as m_, "
                        .(($currentView == 'day')?" dayofmonth( ifnull(i.pubdate, i.added)) as d_, ":"")
                        ." count(*) as cnt_ "
                        ." from " . getTable("item") . "i  where "
                        ." UNIX_TIMESTAMP(ifnull(i.pubdate, i.added)) > $ts_s ";

            if ($cid) {
                $sql_succ .= " and cid=$cid ";
            }


            if (hidePrivate()) {
                $sql_succ .=" and not(i.unread & " . RSS_MODE_PRIVATE_STATE .") ";
            }

            $sql_succ .= " group by y_,m_"
                         .(($currentView == 'day')?",d_ ":"")
                         ." order by ts_ asc limit 4";

            $sql_prev = " select "
                        ." UNIX_TIMESTAMP( ifnull(i.pubdate, i.added)) as ts_, "
                        ." year( ifnull(i.pubdate, i.added)) as y_, "
                        ." month( ifnull(i.pubdate, i.added)) as m_, "
                        .(($currentView == 'day')?" dayofmonth( ifnull(i.pubdate, i.added)) as d_, ":"")
                        ." count(*) as cnt_ "
                        ." from " . getTable("item") ." i  where "
                        ." UNIX_TIMESTAMP(ifnull(i.pubdate, i.added)) < $ts_p ";

            if ($cid) {
                $sql_prev .= " and cid=$cid ";
            }


            if (hidePrivate()) {
                $sql_prev .=" and not(i.unread & " . RSS_MODE_PRIVATE_STATE .") ";
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
                                    'lbl' => rss_locale_date('%B %e',$row['ts_']) . " (".$row['cnt_']." " . ($row['cnt_'] > 1? __('items'):__('item')) .")"
                                );
                    }
                }
                elseif($currentView == 'month') {
                    if (($row['m_'] + 12 * $row['y_']) > $mCount) {
                        $succ = array(
                                    'y' => $row['y_'],
                                    'm' => $row['m_'],
                                    'cnt' => $row['cnt_'],
                                    'ts' => $row['ts_'],
                                    'url' =>  makeArchiveUrl($row['ts_'],$escaped_title,$cid,($currentView == 'day')),
                                    'lbl' => rss_locale_date('%B %Y',$row['ts_']) . " (".$row['cnt_']." " . ($row['cnt_'] > 1? __('items'):ITEM) .")"
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
                                    'lbl' => rss_locale_date('%B %e',$row['ts_']) . " (".$row['cnt_']." " . ($row['cnt_'] > 1? __('items'):__('item')) .")"
                                );
                    }
                }
                elseif($currentView == 'month') {
                    if (($row['m_'] + 12 * $row['y_']) < $mCount) {
                        $prev = array(
                                    'y' => $row['y_'],
                                    'm' => $row['m_'],
                                    'cnt' => $row['cnt_'],
                                    'ts' => $row['ts_'],
                                    'url' =>  makeArchiveUrl($row['ts_'],$escaped_title,$cid,($currentView == 'day')),
                                    'lbl' => rss_locale_date('%B %Y',$row['ts_']) . " (".$row['cnt_']." ". ($row['cnt_'] > 1? __('items'):__('item')) .")"
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
                          'lbl' => rss_locale_date('%B %Y',$ts)
                      );
            }
            elseif ($currentView == 'month') {
                $up = array(

                          'url' => getPath().
                                 ( getConfig('rss.output.usemodrewrite') ?
                                   $escaped_title
                                   :"feed.php?channel=$cid") ,
                          'lbl' => $escaped_title,
                          'lbl' => '');
            }

            break;

        case 'item':

            $sql = " select i.title, i.id, "
                   ." UNIX_TIMESTAMP( ifnull(i.pubdate, i.added)) as ts_, "
                   ." year( ifnull(i.pubdate, i.added)) as y_, "
                   ." month( ifnull(i.pubdate, i.added)) as m_, "
                   ." dayofmonth( ifnull(i.pubdate, i.added)) as d_ "
                   ." from " .getTable("item") . " i "
                   ." where i.cid = $cid  ";

            if (hidePrivate()) {
                $sql .= " and not(i.unread & " . RSS_MODE_PRIVATE_STATE .") ";
            }

            if(getConfig('rss.config.datedesc.unread')) {
                $sql .= " order by ts_ desc, i.id asc";
            } else {
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
                                              rss_uri($ptitle_):
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
                                              rss_uri($title_) :
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
                      'lbl' => rss_locale_date('%B %e',$ts)
                  );


            break;

        case 'feed':
	
            $sql = "select "
                   ." c.id, c.title "
                   ." from " . getTable("channels") ." c "
                   ." inner join " . getTable("folders") ." d "
                   ."   on d.id = c.parent ";

            $sql .= " where not(c.mode & " .  RSS_MODE_DELETED_STATE .") ";
            if (hidePrivate()) {
                $sql .=" and not(c.mode & " . RSS_MODE_PRIVATE_STATE .") ";
            }

            if (getConfig('rss.config.absoluteordering')) {
                $sql .=" order by d.position asc, c.position asc";
            } else {
                $sql .=" order by d.name asc, c.title asc";
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
                    $succ = array(
                                'url' => getPath().
                                       ( getConfig('rss.output.usemodrewrite') ?
                                         rss_uri($title_) ."/"
                                         :"feed.php?channel=$cid_") ,
                                'lbl' => htmlentities( $title_,ENT_COMPAT,"UTF-8" )
                            );
                }
                if ($key > 0) {
                    list($cid_,$title_) = $cidname[$key-1];
                    $prev = array(
                                'url' => getPath().
                                       ( getConfig('rss.output.usemodrewrite') ?
                                         rss_uri($title_) ."/"
                                         :"feed.php?channel=$cid_") ,
                                'lbl' => htmlentities( $title_,ENT_COMPAT,"UTF-8" )
                            );
                }

            }

            break;

        case 'cat':
            $res = rss_query(" select t.tag,t.id from  "
                             .getTable('metatag') ." m "
                             ."inner join " . getTable('tag') . "t on t.id = m.tid "
                             ." where  m.ttype = 'channel' "
                             ." order by t.tag asc");

            $pp = null;
            $nn = null;
            $found = false;
            $stop = false;
            while (!$stop && list($tt_,$tid_) = rss_fetch_row($res)) {
                if ($vfid == $tid_) {
                    $found = true;
                }
                if (!$found) {
                    $pp = array('id' => $tid_, 	'title' => $tt_);
                }
                elseif ($vfid != $tid_) {
                    $nn = array('id' => $tid_ , 'title' => $tt_);
                    $stop = true;
                }
            }
            if ($pp) {
                $vftitle_ = $pp['title'];
                $vfid_ = 	$pp['id'];
                $prev = array(
                            'url' => getPath().
                                   ( getConfig('rss.output.usemodrewrite') ?
                                     rss_uri($vftitle_) ."/"
                                     :"feed.php?vfolder=$vfid_") ,
                            'lbl' => htmlentities( $vftitle_,ENT_COMPAT,"UTF-8" )
                        );
            }

            if ($nn) {
                $vftitle_ = $nn['title'];
                $vfid_ = 	$nn['id'];
                $succ = array(
                            'url' => getPath().
                                   ( getConfig('rss.output.usemodrewrite') ?
                                     rss_uri($vftitle_) ."/"
                                     :"feed.php?vfolder=$vfid_") ,
                            'lbl' => htmlentities( $vftitle_,ENT_COMPAT,"UTF-8" )
                        );
            }


            break;

        case 'folder':
            $sql = "select  f.id, f.name, count(*) from " . getTable('channels') . " c "
                   ."inner join " . getTable('folders') . " f on f.id = c.parent "
                   ." where f.name != '' ";

            if (hidePrivate()) {
                $sql .= " and not (c.mode & ".RSS_MODE_PRIVATE_STATE.")";
            }

            $sql .= " group by f.id ";
            if (getConfig('rss.config.absoluteordering')) {
                $sql .= " order by f.position asc, c.position asc";
            } else {
                $sql .= " order by f.name, c.title asc";
            }
            $res = rss_query($sql);
            $pp = null;
            $nn = null;
            $found = false;
            $stop = false;

            while (!$stop && list($fid_, $fn_,$fc_) = rss_fetch_row($res)) {
                if ($fc_ == 0) {
                    continue;
                }
                if ($fid == $fid_) {
                    $found = true;
                }
                if (!$found) {
                    $pp = array('id' => $fid_, 	'title' => $fn_);
                }
                elseif ($fid != $fid_) {
                    $nn = array('id' => $fid_ , 'title' => $fn_);
                    $stop = true;
                }
            }
            if ($pp) {
                $ftitle__ = $pp['title'];
                $fid__ = 	$pp['id'];
                $prev = array(
                            'url' => getPath().
                                   ( getConfig('rss.output.usemodrewrite') ?
                                     rss_uri($ftitle__) ."/"
                                     :"feed.php?folder=$fid__") ,
                            'lbl' => htmlentities( $ftitle__,ENT_COMPAT,"UTF-8" )
                        );
            }

            if ($nn) {
                $ftitle__ = $nn['title'];
                $fid__ = 	$nn['id'];
                $succ = array(
                            'url' => getPath().
                                   ( getConfig('rss.output.usemodrewrite') ?
                                    rss_uri($ftitle__) ."/"
                                     :"feed.php?folder=$fid__") ,
                            'lbl' => htmlentities( $ftitle__,ENT_COMPAT,"UTF-8" )
                        );
            }
            break;

        default:
            //echo "current view: $currentView";
            break;
        }
        return array($prev,$succ, $up);
    }

    return null;

}

function markReadForm($cid) {
    if (!isLoggedIn()) {
        return;
    }

    if (!defined('MARK_READ_FEED_FORM')) {
        define ('MARK_READ_FEED_FORM',$cid);
    }
    echo "\n\n<form action=\"". getPath() ."feed.php\" method=\"post\">\n"
    ."\t<p><input id=\"_markReadButton\" type=\"submit\" name=\"action\" accesskey=\"m\" value=\"". __('Mark These Items as Read') ."\"/>\n"
    ."\t<input type=\"hidden\" name=\"metaaction\" value=\"ACT_MARK_CHANNEL_READ\"/>\n"
    ."\t<input type=\"hidden\" name=\"channel\" value=\"$cid\"/>\n"
    ."\t<input type=\"hidden\" name=\"markreadids\" value=\"".implode(",",$GLOBALS['rss']->getShownUnreadIds())."\" />\n"
    ."</p>\n</form>";
}

function markFolderReadForm($fid) {
    if (!isLoggedIn()) {
        return;
    }

    if (!defined('MARK_READ_FOLDER_FORM')) {
        define ('MARK_READ_FOLDER_FORM',$fid);
    }
    echo "\n\n<form action=\"". getPath() ."feed.php\" method=\"post\">\n"
    ."\t<p><input id=\"_markReadButton\" type=\"submit\" name=\"action\" accesskey=\"m\" value=\"". __('Mark These Items as Read') ."\"/>\n"
    ."\t<input type=\"hidden\" name=\"metaaction\" value=\"ACT_MARK_FOLDER_READ\"/>\n"
    ."\t<input type=\"hidden\" name=\"folder\" value=\"$fid\"/>\n"
    ."\t<input type=\"hidden\" name=\"markreadids\" value=\"".implode(",",$GLOBALS['rss']->getShownUnreadIds())."\" />\n"
    ."</p></form>";

}

function markVirtualFolderReadForm($vfid) {
    if (!isLoggedIn()) {
        return;
    }

    if (!defined('MARK_READ_VFOLDER_FORM')) {
        define ('MARK_READ_VFOLDER_FORM',$vfid);
    }
    echo "\n\n<form action=\"". getPath() ."feed.php\" method=\"post\">\n"
    ."\t<p><input id=\"_markReadButton\" type=\"submit\" name=\"action\" accesskey=\"m\" value=\"". __('Mark These Items as Read') ."\"/>\n"
    ."\t<input type=\"hidden\" name=\"metaaction\" value=\"ACT_MARK_VFOLDER_READ\"/>\n"
    ."\t<input type=\"hidden\" name=\"vfolder\" value=\"$vfid\"/>\n"
    ."\t<input type=\"hidden\" name=\"markreadids\" value=\"".implode(",",$GLOBALS['rss']->getShownUnreadIds())."\" />\n"
    ."</p>\n</form>";
}



function debugFeed($cid) {}

?>
