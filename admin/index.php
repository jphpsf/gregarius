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


define ('RSS_FILE_LOCATION','/admin');

require_once('../init.php');
require_once('../opml.php');
require_once('ds.php');


define ('ADMIN_DOMAIN','domain');
define ('ADMIN_DOMAIN_FOLDER','folders');
define ('ADMIN_DOMAIN_CHANNEL','feeds');
define ('ADMIN_DOMAIN_ITEM','items');
define ('ADMIN_DOMAIN_CONFIG','config');
define ('ADMIN_DOMAIN_OPML','opml');
define ('ADMIN_DOMAIN_NONE','none');
define ('ADMIN_DELETE_ACTION','delete');
define ('ADMIN_DEFAULT_ACTION','default');
define ('ADMIN_EDIT_ACTION','edit');
define ('ADMIN_MOVE_UP_ACTION','up');
define ('ADMIN_MOVE_DOWN_ACTION','down');
define ('ADMIN_SUBMIT_EDIT','submit_edit');
define ('ADMIN_VIEW','view');
define ('ADMIN_CONFIRMED','confirmed');
define ('ADMIN_PRUNE','prune');

$auth = true;


if (defined('ADMIN_USERNAME') && defined ('ADMIN_PASSWORD')) {
    if (!array_key_exists('PHP_AUTH_USER',$_SERVER) ||
      $_SERVER['PHP_AUTH_USER'] != ADMIN_USERNAME ||
      !array_key_exists('PHP_AUTH_PW',$_SERVER) ||
      $_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD ) {
         header('WWW-Authenticate: Basic realm="Gregarius Admin Authentication"');
         header('HTTP/1.0 401 Unauthorized');
         // mbi:tbd:Gotta do something here...
	   $auth = false;
    }
}

rss_header(TITLE_ADMIN,LOCATION_ADMIN,'',false,""/* 'domcollapse.js' */);

admin_main($auth);
rss_footer();

///////////////////////////////////////////////////////////////////////////////////////////

function admin_main($authorised) {

    echo "\n<div id=\"channel_admin\" class=\"frame\">";
    admin_menu();
    
    if ($authorised) {
	if (array_key_exists(ADMIN_DOMAIN,$_REQUEST)) {
	    switch($_REQUEST[ADMIN_DOMAIN]) {
	     case ADMIN_DOMAIN_FOLDER:
		$show = folder_admin();
		break;
	     case ADMIN_DOMAIN_CHANNEL:
		$show = channel_admin();
		break;
	     case ADMIN_DOMAIN_CONFIG:
		$show = config_admin();
		break;
		  case ADMIN_DOMAIN_ITEM:
		  $show = item_admin();
		  break;
	     default:
		break;
	    }
	}
	
	
	if (array_key_exists(ADMIN_VIEW,$_REQUEST) || isset($show)) {
	    if (!isset($show)) {
		$show = $_REQUEST[ADMIN_VIEW];
	    }
	    switch ($show) {
	     case ADMIN_DOMAIN_CONFIG:
         config();
         break;
           case ADMIN_DOMAIN_CHANNEL:
         channels();
         break;
           case ADMIN_DOMAIN_FOLDER:
         folders();
         break;
           case ADMIN_DOMAIN_OPML:
         opml();
         break;	    
           case ADMIN_DOMAIN_NONE:	     
         break;
		  case ADMIN_DOMAIN_ITEM:
		  items();
		  break;
	   default:
		 rss_error( "FIXME: admin unknown view: $show !\n" );
	   }
	} else {
	   channels();
	}
	
	echo "\n<div class=\"clearer\"></div>\n";

    } else {
	rss_error("I'm sorry, you are not authorised to access the administration interface.\n"
		  ."Please follow <a href=\"".getPath()."\">this link</a> back to the main page.\n"
		  ."Have  a nice day!");
    }
    echo "</div>\n";
}

/*************** Channel management ************/

function channels() {
    echo "<h2 class=\"trigger\">". ADMIN_CHANNELS ."</h2>\n";
    echo "<div id=\"admin_channels\">\n";
    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";
    echo "<p><input type=\"hidden\" name=\"". ADMIN_DOMAIN."\" value=\"".ADMIN_DOMAIN_CHANNEL."\"/>\n";
    echo "<label for=\"new_channel\">". ADMIN_CHANNELS_ADD ."</label>\n";
    echo "<input type=\"text\" name=\"new_channel\" id=\"new_channel\" value=\"http://\" onfocus=\"this.select()\"/>\n";

    echo "<label for=\"add_channel_to_folder\">". ADMIN_IN_FOLDER . "</label>\n";
    folder_combo('add_channel_to_folder');

    echo "<input type=\"submit\" name=\"action\" value=\"". ADMIN_ADD ."\"/></p>\n";
    echo "<p style=\"font-size:small\">".ADMIN_ADD_CHANNEL_EXPL."</p>";
    echo "</form>\n\n";

    echo "<table id=\"channeltable\">\n"
      ."<tr>\n"
      ."\t<th>". ADMIN_CHANNELS_HEADING_TITLE ."</th>\n"
      ."\t<th class=\"cntr\">". ADMIN_CHANNELS_HEADING_FOLDER ."</th>\n"
      ."\t<th>". ADMIN_CHANNELS_HEADING_DESCR ."</th>\n";

    if (getConfig('rss.config.absoluteordering')) {
	echo "\t<th>".ADMIN_CHANNELS_HEADING_MOVE."</th>\n";
    }

    echo "\t<th class=\"cntr\">". ADMIN_CHANNELS_HEADING_ACTION ."</th>\n"
      ."</tr>\n";

    $sql = "select "
      ." c.id, c.title, c.url, c.siteurl, d.name, c.descr, c.parent, c.icon "
      ." from " .getTable("channels") ." c, " . getTable("folders") ." d "
      ." where d.id = c.parent ";

   if (getConfig('rss.config.absoluteordering')) {
	  $sql .=" order by d.position asc, c.position asc";
   } else {
	  $sql .=" order by c.parent asc, c.title asc";
   }

    $res = rss_query($sql);
    $cntr = 0;
    while (list($id, $title, $url, $siteurl, $parent, $descr, $pid, $icon) = rss_fetch_row($res)) {

	if (getConfig('rss.output.usemodrewrite')) {
	    $outUrl = getPath() . preg_replace("/[^A-Za-z0-9\.]/","_","$title") ."/";
	} else {
	    $outUrl = getPath() . "feed.php?channel=$id";
	}

	$parentLabel = $parent == ''? HOME_FOLDER:$parent;

	$class_ = (($cntr++ % 2 == 0)?"even":"odd");

	echo "<tr class=\"$class_\">\n"
	  ."\t<td>"
	  .((getConfig('rss.output.showfavicons') && $icon != "")?
	    "<img src=\"$icon\" class=\"favicon\" alt=\"$title\" width=\"16\" height=\"16\" />":"")
	    ."<a href=\"$outUrl\">$title</a></td>\n"
	  ."\t<td class=\"cntr\">".preg_replace('/ /','&nbsp;',$parentLabel)."</td>\n"
	  ."\t<td>$descr</td>\n";

	if (getConfig('rss.config.absoluteordering')) {
	    echo "\t<td class=\"cntr\"><a href=\"".$_SERVER['PHP_SELF']. "?".ADMIN_DOMAIN."=". ADMIN_DOMAIN_CHANNEL
	      ."&amp;action=". ADMIN_MOVE_UP_ACTION. "&amp;cid=$id\">". ADMIN_MOVE_UP
	      ."</a>&nbsp;-&nbsp;<a href=\"".$_SERVER['PHP_SELF']. "?".ADMIN_DOMAIN."=". ADMIN_DOMAIN_CHANNEL
	      ."&amp;action=". ADMIN_MOVE_DOWN_ACTION ."&amp;cid=$id\">".ADMIN_MOVE_DOWN ."</a></td>\n";
	}
	echo "\t<td class=\"cntr\"><a href=\"".$_SERVER['PHP_SELF']. "?".ADMIN_DOMAIN."=". ADMIN_DOMAIN_CHANNEL
	  ."&amp;action=". ADMIN_EDIT_ACTION. "&amp;cid=$id\">" . ADMIN_EDIT
	  ."</a>|<a href=\"".$_SERVER['PHP_SELF']. "?".ADMIN_DOMAIN."=". ADMIN_DOMAIN_CHANNEL
	  ."&amp;action=". ADMIN_DELETE_ACTION ."&amp;cid=$id\">" . ADMIN_DELETE ."</a></td>\n"
	  ."</tr>\n";
    }

    echo "</table>\n</div>\n\n\n";

}

function opml() {
    //opml import
    echo "<h2 class=\"trigger\">". ADMIN_OPML ."</h2>\n";
    echo "<div id=\"admin_opml\">\n";

    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";
    echo "<p><input type=\"hidden\" name=\"". ADMIN_DOMAIN ."\" value=\"".ADMIN_DOMAIN_CHANNEL."\"/>\n";
    echo "<label for=\"opml\">" . ADMIN_OPML_IMPORT ."</label>\n";
    echo "<input type=\"text\"  name=\"opml\" id=\"opml\" value=\"http://\" onfocus=\"this.select()\"/>\n";
    echo "<input type=\"submit\" name=\"action\" value=\"". ADMIN_IMPORT ."\"/></p>\n";

    echo "</form>\n";

    opml_export_form();
    echo "</div>\n";
}

function items() {
   
    echo "<h2 class=\"trigger\">". ADMIN_ITEM ."</h2>\n";
    echo "<div id=\"admin_items\">\n";

    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";
    echo "<fieldset class=\"prune\">\n"
	      ."<legend>Pruning</legend>\n";
    echo "<p><input type=\"hidden\" name=\"". ADMIN_DOMAIN ."\" value=\"".ADMIN_DOMAIN_ITEM."\"/>\n";
    echo "<label for=\"prune_older\">" . ADMIN_PRUNE_OLDER ."</label>\n";
    echo "<input type=\"text\" size=\"5\" name=\"prune_older\" id=\"prune_older\" value=\"\" />\n";
    echo "<select name=\"prune_period\" id=\"prune_period\">\n"
      ."<option>" . ADMIN_PRUNE_DAYS . "</option>\n"
      ."<option>" . ADMIN_PRUNE_MONTHS . "</option>\n"
      ."<option>" . ADMIN_PRUNE_YEARS . "</option>\n"
      ."</select></p>\n";
    
    /*
    echo "<p><label for=\"prune_keep\">" . PRUNE_KEEP ."</label>\n"
      ."<input type=\"text\" name=\"prune_keep\" id=\"prune_keep\" size=\"5\" />"
      ."</p>";
    */  
    echo "<p><input type=\"submit\" name=\"action\" value=\"". ADMIN_DELETE2 ."\"/></p>\n";

    echo "</fieldset>\n";
    echo "</form>\n";

    echo "</div>\n";
}

function item_admin() {
   $ret__ = ADMIN_DOMAIN_NONE; 
   switch ($_REQUEST['action']) {
      case ADMIN_DELETE2:
         $req = rss_query('select count(*) as cnt from ' .getTable('item'));
         list($cnt) = rss_fetch_row($req);
         $prune_older = (int) $_REQUEST['prune_older'];
         //$prune_keep = (int) $_REQUEST['prune_keep'];
         if ($prune_older) {
            switch ($_REQUEST['prune_period']) {
               case ADMIN_PRUNE_DAYS:
                  $period = 'day';
               break;
               
               case ADMIN_PRUNE_MONTHS:
                  $period = 'month';
               break;
               
               case ADMIN_PRUNE_YEARS:
                  $period = 'year';
               break;
               
               default:
                  rss_error('invalid pruning period');
                  return ADMIN_DOMAIN_ITEM;
               break;
            }
            
            $sql = " from ".getTable('item')." where added <  date_sub(now(), interval $prune_older $period)";

            if (array_key_exists(ADMIN_CONFIRMED,$_REQUEST)) {
               rss_query( 'delete ' . $sql);
               $ret__ = ADMIN_DOMAIN_ITEM; 

            } else {
               list($cnt_d) = rss_fetch_row(rss_query( 
                "select count(*) as cnt " . $sql));
               rss_error(sprintf(ADMIN_ABOUT_TO_DELETE,$cnt_d,$cnt));    
               
              echo "<form action=\"\" method=\"post\">\n"
               ."<p><input type=\"hidden\" name=\"".ADMIN_DOMAIN."\" value=\"".ADMIN_DOMAIN_ITEM."\" />\n"
               ."<input type=\"hidden\" name=\"prune_older\" value=\"".$_REQUEST['prune_older']."\" />\n"
               ."<input type=\"hidden\" name=\"prune_period\" value=\"".$_REQUEST['prune_period']."\" />\n"
               ."<input type=\"hidden\" name=\"".ADMIN_CONFIRMED."\" value=\"1\" />\n"
               ."<input type=\"submit\" name=\"action\" value=\"". ADMIN_DELETE2 ."\" />\n"
               ."<input type=\"submit\" name=\"action\" value=\"". ADMIN_CANCEL ."\"/>\n"
	           ."</p>\n"
	           ."</form>\n"; 
            }
         } else {
            rss_error('oops, no period specified');
            $ret__ = ADMIN_DOMAIN_ITEM; 
         }
      
      break;
      default:
         $ret__ = ADMIN_DOMAIN_ITEM; 
      break;   
   }

   return $ret__;
}


function channel_admin() {

    $ret__ = ADMIN_DOMAIN_NONE;
    switch ($_REQUEST['action']) {
     case ADMIN_ADD:
	$label = trim($_REQUEST['new_channel']);
	$fid = trim($_REQUEST['add_channel_to_folder']);
	if ($label != 'http://' &&  substr($label, 0,4) == "http") {
	    $ret = add_channel($label,$fid);
	    if (is_array($ret) && $ret[0] > -1) {
		update($ret[0]);
		$ret__ = ADMIN_DOMAIN_CHANNEL;
	    } else {
		// okay, something went wrong, maybe thats a html url after all?
		// let's try and see if we can extract some feeds
		$feeds = extractFeeds($label);
		if (!is_array($feeds) || sizeof($feeds) == 0) {
		    rss_error($ret[1]);
		    $ret__ = ADMIN_DOMAIN_CHANNEL; 
		} else {
		    //one single feed in the html doc, add that
		    if (is_array($feeds) && sizeof($feeds) == 1 && array_key_exists('href',$feeds[0])) {
			$ret = add_channel($feeds[0]['href'],$fid);
			if (is_array($ret) && $ret[0] > -1) {
			    update($ret[0]);
			    $ret__ = ADMIN_DOMAIN_CHANNEL; 
			} else {
			    // failure
			    rss_error($ret[1]);
			    $ret__ = ADMIN_DOMAIN_CHANNEL; 
			}
		    } else {
			// multiple feeds in the channel
			echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
			  ."<p>".sprintf(ADMIN_FEEDS,$label,$label)."</p>\n";
			$cnt = 0;
			while(list($id,$feedarr) = each($feeds)) {			    
			    // we need an URL
			    if (!array_key_exists('href',$feedarr)) {
				continue;
			    } else {
				$href = $feedarr['href']; 
			    }
			     
			    if (array_key_exists('type',$feedarr)) {
				$typeLbl = " [" . $feedarr['type'] . "]";
			    }
			    
			    $cnt++;
			    
			    if (array_key_exists('title',$feedarr)) {
				$lbl = $feedarr['title'];
			    } elseif (array_key_exists('type',$feedarr)) {
				$lbl = $feedarr['type'];
				$typeLbl = "";
			    } elseif (array_key_exists('href',$feedarr)) {
				$lbl = $feedarr['href'];
			    } else {
				$lbl = "Resource $cnt";
			    }
			    
			    
			    echo "<p>\n\t<input class=\"indent\" type=\"radio\" id=\"fd_$cnt\" name=\"new_channel\" "
			      ." value=\"$href\"/>\n"
			      ."\t<label for=\"fd_$cnt\"><a href=\"$href\">$lbl</a>$typeLbl</label>\n"
			      ."</p>\n";
			}
			
			echo "<p><input type=\"hidden\" name=\"add_channel_to_folder\" value=\"$fid\"/>\n"
			  ."<input type=\"hidden\" name=\"".ADMIN_DOMAIN."\" value=\"".ADMIN_DOMAIN_CHANNEL."\"/>\n"
			  ."<input type=\"submit\" class=\"indent\" name=\"action\" value=\"". ADMIN_ADD ."\"/>\n"
			  ."</p>\n</form>\n\n";
		    }
		}		
	    }
	} else {
	    rss_error("I'm sorry, I dont think I can handle this URL: '$label'");
	    $ret__ = ADMIN_DOMAIN_CHANNEL; 
	}
	break;

     case ADMIN_EDIT_ACTION:
	$id = $_REQUEST['cid'];
	channel_edit_form($id);
	break;

     case ADMIN_CREATE:
	$label=$_REQUEST['new_folder'];
	assert(strlen($label) > 0);

	$sql = "insert into " . getTable("folders") ." (name) values ('" . rss_real_escape_string($label) ."')";
	rss_query($sql);
	$ret__ = ADMIN_DOMAIN_FOLDER;
	break;

     case ADMIN_DELETE_ACTION:
	$id = $_REQUEST['cid'];
	if (array_key_exists(ADMIN_CONFIRMED,$_REQUEST) && $_REQUEST[ADMIN_CONFIRMED] == ADMIN_YES) {
	    $sql = "delete from " . getTable("item") ." where cid=$id";
	    rss_query($sql);
	    $sql = "delete from " . getTable("channels") ." where id=$id";
	    rss_query($sql);
	    $ret__ = ADMIN_DOMAIN_CHANNEL;
	} elseif (array_key_exists(ADMIN_CONFIRMED,$_REQUEST) && $_REQUEST[ADMIN_CONFIRMED] == ADMIN_NO) {
	    $ret__ = ADMIN_DOMAIN_CHANNEL;
	} else {
	    list($cname) = rss_fetch_row(rss_query("select title from " . getTable("channels") ." where id = $id"));

	    echo "<form class=\"box\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
	      ."<p class=\"error\">"; printf(ADMIN_ARE_YOU_SURE,$cname); echo "</p>\n"
	      ."<p><input type=\"submit\" name=\"".ADMIN_CONFIRMED."\" value=\"". ADMIN_NO ."\"/>\n"
	      ."<input type=\"submit\" name=\"".ADMIN_CONFIRMED."\" value=\"". ADMIN_YES ."\"/>\n"
	      ."<input type=\"hidden\" name=\"cid\" value=\"$id\"/>\n"
	      ."<input type=\"hidden\" name=\"".ADMIN_DOMAIN."\" value=\"".ADMIN_DOMAIN_CHANNEL."\"/>\n"
	      ."<input type=\"hidden\" name=\"action\" value=\"". ADMIN_DELETE ."\"/>\n"
	      ."</p>\n</form>\n";
	}
	break;

     case ADMIN_IMPORT:
	$url = $_REQUEST['opml'];
	$opml=getOpml($url);

	if (sizeof($opml) > 0) {
	    $sql = "delete from " . getTable("channels");
	    rss_query($sql);

	    $sql = "delete from " . getTable("item");
	    rss_query($sql);

	    $sql = "delete from " . getTable("folders") ." where id > 0";
	    rss_query($sql);

	    $prev_folder = HOME_FOLDER;
	    $fid = 0;
	    while (list($folder,$items) = each ($opml)) {
		if ($folder != $prev_folder) {
		    $fid = create_folder($folder);
		    $prev_folder = $folder;
		}

		for ($i=0;$i<sizeof($opml[$folder]);$i++){
		    $url_ = trim($opml[$folder][$i]['XMLURL']);
		    add_channel($url_, $fid);
		}

	    }

	    //update all the feeds
	    update("");
	    
	    // mark all items as read
	    rss_query( "update " . getTable("item") ." set unread=0" );

	}
	$ret__ = ADMIN_DOMAIN_CHANNEL;
	break;

     case ADMIN_SUBMIT_EDIT:
	$cid = $_REQUEST['cid'];
	$title= rss_real_escape_string(real_strip_slashes($_REQUEST['c_name']));
	$url= rss_real_escape_string($_REQUEST['c_url']);
	$siteurl= rss_real_escape_string($_REQUEST['c_siteurl']);
	$parent= rss_real_escape_string($_REQUEST['c_parent']);
	$descr= rss_real_escape_string(real_strip_slashes($_REQUEST['c_descr']));
	$icon = rss_real_escape_string($_REQUEST['c_icon']);

	if ($url == '' || substr($url,0,4) != "http") {
	    rss_error("I'm sorry, '$url' doesn't look like a valid RSS URL to me.");
	    $ret__ = ADMIN_DOMAIN_CHANNEL;
	    break;
	}

	$sql = "update " .getTable("channels") ." set title='$title', url='$url', siteurl='$siteurl', "
	  ." parent=$parent, descr='$descr', icon='$icon' where id=$cid";

	rss_query($sql);
	$ret__ = ADMIN_DOMAIN_CHANNEL;
	break;

     case ADMIN_MOVE_UP_ACTION:
     case ADMIN_MOVE_DOWN_ACTION:
	$id = $_REQUEST['cid'];
	$res = rss_query("select parent,position from " . getTable("channels") ." where id=$id");
	list($parent,$position) = rss_fetch_row($res);
	$res = rss_query(
			 "select id, position from " .getTable("channels") 
			 ." where parent=$parent and id != $id order by abs($position-position) limit 2"
			 );

	// Let's look for a lower/higher position than the one we got.
	$switch_with_position=$position;

	while (list($oid,$oposition) = rss_fetch_row($res)) {
	    if (
		// found none yet?
		($switch_with_position == $position) &&
		(
		 // move up: we look for a lower position
		 ($_REQUEST['action'] == ADMIN_MOVE_UP_ACTION && $oposition < $switch_with_position)
		 ||
		 // move up: we look for a higher position
		 ($_REQUEST['action'] == ADMIN_MOVE_DOWN_ACTION && $oposition > $switch_with_position)
		 )
		){
		$switch_with_position = $oposition;
		$switch_with_id = $oid;
	    }
	}
	// right, lets!
	if ($switch_with_position != $position) {
	    rss_query( "update " .getTable("channels") ." set position = $switch_with_position where id=$id" );
	    rss_query( "update " .getTable("channels") ." set position = $position where id=$switch_with_id" );
	}
	$ret__ = ADMIN_DOMAIN_CHANNEL;
	break;

     default: break;
    }
    return $ret__;
}

function channel_edit_form($cid) {
    $sql = "select id, title, url, siteurl, parent, descr, icon from " .getTable("channels") ." where id=$cid";
    $res = rss_query($sql);
    list ($id, $title, $url, $siteurl, $parent, $descr, $icon) = rss_fetch_row($res);

    echo "<div>\n";
    echo "\n\n<h2>Edit '$title'</h2>\n";
    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\" id=\"channeledit\">\n"
      ."<p><input type=\"hidden\" name=\"".ADMIN_DOMAIN."\" value=\"". ADMIN_DOMAIN_CHANNEL."\"/>\n"
      ."<input type=\"hidden\" name=\"action\" value=\"". ADMIN_SUBMIT_EDIT ."\"/>\n"
      ."<input type=\"hidden\" name=\"cid\" value=\"$cid\"/>\n"

      // Item name
      ."<label for=\"c_name\">". ADMIN_CHANNEL_NAME ."</label>\n"
      ."<input type=\"text\" id=\"c_name\" name=\"c_name\" value=\"$title\"/></p>"

      // RSS URL
      ."<p><label for=\"c_url\">". ADMIN_CHANNEL_RSS_URL ."</label>\n"
      ."<a href=\"$url\">" . VISIT . "</a>\n"
      ."<input type=\"text\" id=\"c_url\" name=\"c_url\" value=\"$url\"/></p>"

      // Site URL
      ."<p><label for=\"c_siteurl\">". ADMIN_CHANNEL_SITE_URL ."</label>\n"
      ."<a href=\"$siteurl\">" . VISIT . "</a>\n"
      ."<input type=\"text\" id=\"c_siteurl\" name=\"c_siteurl\" value=\"$siteurl\"/></p>"

      // Folder
      ."<p><label for=\"c_parent\">". ADMIN_CHANNEL_FOLDER ."</label>\n";
    
    folder_combo('c_parent',$parent);
    
    echo "</p>\n";
    
    // Description
    echo "<p><label for=\"c_descr\">". ADMIN_CHANNEL_DESCR ."</label>\n"
      ."<input type=\"text\" id=\"c_descr\" name=\"c_descr\" value=\"$descr\"/></p>\n";

    // Icon
    if (getConfig('rss.output.showfavicons')) {
	echo "<p><label for=\"c_icon\">" . ADMIN_CHANNEL_ICON ."</label>\n";

	if (trim($icon) != "") {
	    echo "<img src=\"$icon\" alt=\"$title\" class=\"favicon\" width=\"16\" height=\"16\" />\n";
	    echo "<span>" . CLEAR_FOR_NONE ."</span>";
	}

	echo "<input type=\"text\" id=\"c_icon\" name=\"c_icon\" value=\"$icon\"/></p>\n";
    } else {
	echo "<p><input type=\"hidden\" name=\"c_icon\" id=\"c_icon\" value=\"$icon\"/></p>\n";
    }

    echo "<p><input type=\"submit\" name=\"action_\" value=\"". ADMIN_SUBMIT_CHANGES ."\"/></p>"
      ."</form></div>\n";
}


/*************** Folder management ************/

function folders() {
    echo "<h2 class=\"trigger\">".ADMIN_FOLDERS."</h2>\n"
      ."<div id=\"admin_folders\" class=\"trigger\">\n";
    
    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";

    echo "<p><input type=\"hidden\" name=\"".ADMIN_DOMAIN."\" value=\"".ADMIN_DOMAIN_FOLDER."\"/>\n";

    echo "<label for=\"new_folder\">".ADMIN_FOLDERS_ADD."</label>\n"
      ."<input type=\"text\" id=\"new_folder\" name=\"new_folder\" value=\"\" />"
      ."<input type=\"submit\" name=\"action\" value=\"". ADMIN_ADD ."\"/>\n"
      ."</p></form>\n\n";

    echo "<table id=\"foldertable\">\n"
      ."<tr>\n"
      ."\t<th class=\"cntr\">". ADMIN_CHANNELS_HEADING_TITLE ."</th>\n";

    if (getConfig('rss.config.absoluteordering')) {
	echo "\t<th>".ADMIN_CHANNELS_HEADING_MOVE."</th>\n";
    }

    echo "\t<th>". ADMIN_CHANNELS_HEADING_ACTION ."</th>\n"
      ."</tr>\n";

    $sql = "select id,name from " .getTable("folders");

    if (getConfig('rss.config.absoluteordering')) {
	$sql .=" order by position asc";
    } else {
	$sql .=" order by id";
    }

    $res = rss_query($sql);
    $cntr = 0;
    while (list($id, $name) = rss_fetch_row($res)) {

	$name = $name == ''? HOME_FOLDER:$name;

	$class_ = (($cntr++ % 2 == 0)?"even":"odd");

	echo "<tr class=\"$class_\">\n"
	  ."\t<td>$name</td>\n";

	if (getConfig('rss.config.absoluteordering')) {
	    echo "\t<td>";

	    if ($id > 0) {
		if ($cntr > 2) {
		    echo "<a href=\"".$_SERVER['PHP_SELF']. "?".ADMIN_DOMAIN."=". ADMIN_DOMAIN_FOLDER
		      ."&amp;action=". ADMIN_MOVE_UP_ACTION. "&amp;fid=$id\">". ADMIN_MOVE_UP
		      ."</a>&nbsp;-&nbsp;";
		}
		echo "<a href=\"".$_SERVER['PHP_SELF']. "?".ADMIN_DOMAIN."=". ADMIN_DOMAIN_FOLDER
		  ."&amp;action=". ADMIN_MOVE_DOWN_ACTION ."&amp;fid=$id\">".ADMIN_MOVE_DOWN ."</a>";
	    } else {
		echo "&nbsp;";
	    }

	    echo "</td>\n";
	}
	echo "\t<td><a href=\"".$_SERVER['PHP_SELF']. "?".ADMIN_DOMAIN."=". ADMIN_DOMAIN_FOLDER
	  ."&amp;action=". ADMIN_EDIT_ACTION. "&amp;fid=$id\">" . ADMIN_EDIT
	  ."</a>";
	if ($id > 0) {
	    echo "|<a href=\"".$_SERVER['PHP_SELF']. "?".ADMIN_DOMAIN."=". ADMIN_DOMAIN_FOLDER
	      ."&amp;action=". ADMIN_DELETE_ACTION ."&amp;fid=$id\">" . ADMIN_DELETE ."</a>";
	}
	echo "</td>\n"
	  ."</tr>\n";

    }
    echo "</table>";

    echo "</div>\n";
}

function folder_edit($fid) {

    $sql = "select id, name from " . getTable("folders") ." where id=$fid";
    $res = rss_query($sql);
    list ($id, $name) = rss_fetch_row($res);

    echo "<div>\n";
    echo "\n\n<h2>Edit '$name'</h2>\n";
    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\" id=\"folderedit\">\n"
      ."<p><input type=\"hidden\" name=\"".ADMIN_DOMAIN."\" value=\"". ADMIN_DOMAIN_FOLDER."\"/>\n"
      ."<input type=\"hidden\" name=\"action\" value=\"".ADMIN_SUBMIT_EDIT."\"/>\n"
      ."<input type=\"hidden\" name=\"fid\" value=\"$id\"/>\n"

      // Item name
      ."<label for=\"f_name\">". ADMIN_FOLDER_NAME ."</label>\n"
      ."<input type=\"text\" id=\"f_name\" name=\"f_name\" value=\"$name\"/></p>";

    echo "<p><input type=\"submit\" name=\"action_\" value=\"". ADMIN_SUBMIT_CHANGES ."\"/></p>"
      ."</form></div>\n";

}

function folder_combo($name, $selected = -1) {
    echo "\n<select name=\"$name\" id=\"$name\">\n";
    $res = rss_query("select id, name from " .getTable("folders") ." order by id asc");
    while (list($id, $name) = rss_fetch_row($res)) {
	echo "\t<option value=\"$id\""
	  .($selected > -1 && $selected == $id ? " selected=\"selected\"":"")
	  .">" .  (($name == "")?HOME_FOLDER:$name)  ."</option>\n";
    }
    echo "</select>\n";
}

function folder_admin() {

    $ret__ = ADMIN_DOMAIN_FOLDER;
    switch ($_REQUEST['action']) {
     case ADMIN_EDIT_ACTION:
	folder_edit($_REQUEST['fid']);
	$ret__ = ADMIN_DOMAIN_NONE;                                                                      
	break;

     case ADMIN_DELETE_ACTION:
	$id = $_REQUEST['fid'];
	assert(is_numeric($id));

	if ($id == 0) {
	    rss_error("You can't delete the " . HOME_FOLDER . " folder");
	    break;
	}

	if (array_key_exists(ADMIN_CONFIRMED,$_REQUEST) && $_REQUEST[ADMIN_CONFIRMED] == ADMIN_YES) {
	    $sql = "delete from " . getTable("folders") ." where id=$id";
	    rss_query($sql);
            $sql = "update " . getTable("channels") ." set parent=0 where parent=$id";
	    rss_query($sql);	    
	} elseif (array_key_exists(ADMIN_CONFIRMED,$_REQUEST) && $_REQUEST[ADMIN_CONFIRMED] == ADMIN_NO) {
	    // nop;
	} else {
	    list($fname) = rss_fetch_row(rss_query("select name from " .getTable("folders") ." where id = $id"));

	    echo "<form class=\"box\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
	      ."<p class=\"error\">"; printf(ADMIN_ARE_YOU_SURE,$fname); echo "</p>\n"
	      ."<p><input type=\"submit\" name=\"".ADMIN_CONFIRMED."\" value=\"". ADMIN_NO ."\"/>\n"
	      ."<input type=\"submit\" name=\"".ADMIN_CONFIRMED."\" value=\"". ADMIN_YES ."\"/>\n"
	      ."<input type=\"hidden\" name=\"fid\" value=\"$id\"/>\n"
	      ."<input type=\"hidden\" name=\"".ADMIN_DOMAIN."\" value=\"".ADMIN_DOMAIN_FOLDER."\"/>\n"
	      ."<input type=\"hidden\" name=\"action\" value=\"". ADMIN_DELETE_ACTION ."\"/>\n"
	      ."</p>\n</form>\n";
	    $ret__ = ADMIN_DOMAIN_NONE;
	}
	break;

     case ADMIN_SUBMIT_EDIT:
	$id = $_REQUEST['fid'];

	$new_label = rss_real_escape_string($_REQUEST['f_name']);
	if (is_numeric($id) && strlen($new_label) > 0) {

	    $res = rss_query("select count(*) as cnt from " . getTable("folders") ." where binary name='$new_label'");
	    list($cnt) = rss_fetch_row($res);
	    if ($cnt > 0) {
		rss_error("You can't rename this folder '$new_label' becuase such a folder already exists.");
		break;
	    }
	    rss_query("update " .getTable("folders") ." set name='$new_label' where id=$id");
	}
	break;

     case ADMIN_ADD:
	$label=$_REQUEST['new_folder'];
	assert(strlen($label) > 0);
	create_folder($label);
	break;

     case ADMIN_MOVE_UP_ACTION:
     case ADMIN_MOVE_DOWN_ACTION:
	$id = $_REQUEST['fid'];

	if ($id == 0) {
	    return;
	}

	$res = rss_query("select position from " .getTable("folders") ." where id=$id");
	list($position) = rss_fetch_row($res);

	$sql = "select id, position from " .getTable("folders")
	  ." where  id != $id order by abs($position-position) limit 2";

	$res = rss_query($sql);

	// Let's look for a lower/higher position than the one we got.
	$switch_with_position=$position;

	while (list($oid,$oposition) = rss_fetch_row($res)) {
	    if (
		// found none yet?
		($switch_with_position == $position) &&
		(
		 // move up: we look for a lower position
		 ($_REQUEST['action'] == ADMIN_MOVE_UP_ACTION && $oposition < $switch_with_position)
		 ||
		 // move up: we look for a higher position
		 ($_REQUEST['action'] == ADMIN_MOVE_DOWN_ACTION && $oposition > $switch_with_position)
		 )
		){
		$switch_with_position = $oposition;
		$switch_with_id = $oid;
	    }
	}

	// right, lets!
	if ($switch_with_position != $position) {
	    rss_query( "update " . getTable("folders") ." set position = $switch_with_position where id=$id" );
	    rss_query( "update " . getTable("folders") ." set position = $position where id=$switch_with_id" );
	}
	break;

     default: break;
    }
    return $ret__;
}

function create_folder($label) {
    $res = rss_query ("select count(*) from " .getTable("folders") ." where name='"
		      .rss_real_escape_string($label). "'");
    list($exists) = rss_fetch_row($res);

    if ($exists > 0) {
	rss_error("Looks like you already have a folder called '$label'");
	return;
    }

    $res = rss_query("select 1+max(position) as np from " . getTable("folders"));
    list($np) = rss_fetch_row($res);

    if (!$np) {
	$np = "0";
    }

    rss_query("insert into " .getTable("folders") ." (name,position) values ('" . rss_real_escape_string($label) ."', $np)");
    list($fid) = rss_fetch_row( rss_query("select id from " .getTable("folders") ." where name='". rss_real_escape_string($label) ."'"));
    return $fid;
}

/*************** OPML Export ************/

function opml_export_form() {
    if (getConfig('rss.output.usemodrewrite')) {
	$method ="post";
	$action = getPath() ."opml";
    } else {
	$method ="get";
	$action = getPath() ."opml.php";	
    }
      
    echo "<form method=\"$method\" action=\"$action\">\n"
      ."<p><label for=\"action\">". ADMIN_OPML_EXPORT. "</label>\n"
      ."<input type=\"submit\" name=\"action\" id=\"action\" value=\"". ADMIN_EXPORT ."\"/></p>\n</form>\n";
}


/*************** Config management ************/

function config() {
    echo "<h2 class=\"trigger\">".ADMIN_CONFIG."</h2>\n"
      ."<div id=\"admin_config\" class=\"trigger\">\n";
    
    echo "<table id=\"configtable\">\n"
      ."<tr>\n"
      ."\t<th>". ADMIN_CHANNELS_HEADING_KEY ."</th>\n"
      ."\t<th>". ADMIN_CHANNELS_HEADING_VALUE ."</th>\n"
      ."\t<th>". ADMIN_CHANNELS_HEADING_DESCR ."</th>\n"
      ."\t<th class=\"cntr\">". ADMIN_CHANNELS_HEADING_ACTION ."</th>\n"
      ."</tr>\n";

    $sql = "select * from " .getTable("config") ." order by key_ asc";

    $res = rss_query($sql);
    $cntr = 0;
    while ($row = rss_fetch_assoc($res)) {
	$value =  real_strip_slashes($row['value_']);  
	$class_ = (($cntr++ % 2 == 0)?"even":"odd");

	echo "<tr class=\"$class_\">\n"
	  ."\t<td>".$row['key_']."</td>\n";
	
	echo "\t<td>";

	switch($row['key_']) {
	    
	    //specific handling per key
	 case 'rss.config.dateformat':
	    echo $value
	      . " ("
	      . preg_replace('/ /','&nbsp;',date($value))
		.")";
	    break;
	 case 'rss.input.allowed':
	    
	    $arr = unserialize($value);
	    echo admin_kses_to_html($arr);
	    
	    break;
	 default:
	    
	    // generic handling per type:
	    switch ($row['type_']) {				
	     case 'string':		
	     case 'num':
	     case 'boolean':
	     default:
		echo $value;
		break;
	     case 'enum':
		$arr = explode(',',$value);
		
		echo admin_enum_to_html($arr);

		break;

	    }
	    break;
	}

	
	echo "</td>\n";
	
	echo "\t<td>" .
	  // source: http://ch2.php.net/manual/en/function.preg-replace.php
	  preg_replace('/\s(\w+:\/\/)(\S+)/',
		       ' <a href="\\1\\2">\\1\\2</a>',
		       $row['desc_']) 
	    . "</td>\n";
	
	echo "\t<td class=\"cntr\">"
	  ."<a href=\"".$_SERVER['PHP_SELF']. "?".ADMIN_DOMAIN."=". ADMIN_DOMAIN_CONFIG
	  ."&amp;action=". ADMIN_EDIT_ACTION. "&amp;key=".$row['key_']."\">" . ADMIN_EDIT
	  ."</a>";
	  
	if ($row['value_'] != $row['default_']) {
	    echo "|"
	  
	      ."<a href=\"".$_SERVER['PHP_SELF']. "?".ADMIN_DOMAIN."=". ADMIN_DOMAIN_CONFIG
	      ."&amp;action=". ADMIN_DEFAULT_ACTION. "&amp;key=".$row['key_']."\">" . ADMIN_DEFAULT
	      ."</a>";
	}
	
	echo "</td>\n"
	  ."</tr>\n";

    }
    echo "</table>";

    echo "</div>\n";
}


function config_admin() {

    $ret__ = ADMIN_DOMAIN_CONFIG;

    switch ($_REQUEST['action']) {
	
	
     case ADMIN_DEFAULT_ACTION:
	if (!array_key_exists('key',$_REQUEST)) {
	    rss_error('Invalid config key specified.');
	    break;
	}
	$key = $_REQUEST['key'];
	$res = rss_query("select value_,default_,type_ from " .getTable('config') . " where key_='$key'");
	list($value,$default,$type) = rss_fetch_row($res);
	$value = real_strip_slashes($value);
	$default = real_strip_slashes($default);
	
	if ($value == $default) {
	    rss_error("The value for '$key' is the same as its default value!");
	    break;
	}
	
	switch ($type) {
	 case 'enum':
	    $html_default = admin_enum_to_html(explode(',',$default));
	    break;
	 case 'array':
	    $html_default = admin_kses_to_html(unserialize($default));
	    break;
	 default:
	    $html_default = $default;
	    break;
	}
	
	if (array_key_exists(ADMIN_CONFIRMED,$_REQUEST) && $_REQUEST[ADMIN_CONFIRMED] == ADMIN_YES) {
	    rss_query("update " . getTable('config') ." set value_=default_ where key_='$key'" );	    
	} elseif (array_key_exists(ADMIN_CONFIRMED,$_REQUEST) && $_REQUEST[ADMIN_CONFIRMED] == ADMIN_NO) {
	    //nop
	} else {

	    echo "<form class=\"box\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
	      ."<p class=\"error\">"; printf(ADMIN_ARE_YOU_SURE_DEFAULT,$key,$html_default); echo "</p>\n"
	      ."<p><input type=\"submit\" name=\"".ADMIN_CONFIRMED."\" value=\"". ADMIN_NO ."\"/>\n"
	      ."<input type=\"submit\" name=\"".ADMIN_CONFIRMED."\" value=\"". ADMIN_YES ."\"/>\n"
	      ."<input type=\"hidden\" name=\"key\" value=\"$key\"/>\n"
	      ."<input type=\"hidden\" name=\"".ADMIN_DOMAIN."\" value=\"".ADMIN_DOMAIN_CONFIG."\"/>\n"
	      ."<input type=\"hidden\" name=\"action\" value=\"". ADMIN_DEFAULT_ACTION ."\"/>\n"
	      ."</p>\n</form>\n";
	    
	    $ret =  ADMIN_DOMAIN_NONE;
	} 	
	break;
	
	
     case ADMIN_EDIT_ACTION:
	$key_ = $_REQUEST['key'];
	$res = rss_query("select * from ". getTable('config') . " where key_ ='$key_'");
	list($key,$value,$default,$type,$desc,$export) =  rss_fetch_row($res);
	$value = real_strip_slashes($value);
	
	echo "<div>\n";
	echo "\n\n<h2>Edit '$key'</h2>\n";
	echo "<form id=\"cfg\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
	  ."<p>\n<input type=\"hidden\" name=\"".ADMIN_DOMAIN."\" value=\"". ADMIN_DOMAIN_CONFIG."\"/>\n"
	  ."<input type=\"hidden\" name=\"key\" value=\"$key\"/>\n"
	  ."<input type=\"hidden\" name=\"type\" value=\"$type\"/>\n"
	  
	  .preg_replace('/\s(\w+:\/\/)(\S+)/',
		       ' <a href="\\1\\2">\\1\\2</a>',
		       $desc)   
	    
	  ."\n</p>\n"
	  ."<p>\n";

	switch($key) {

	    
	 case 'rss.input.allowed':
	    
	    $arr = unserialize($value);
	    
	    	    
	    echo "</p>\n"
	      ."<fieldset class=\"tags\">\n"
	      ."<legend>Tags</legend>\n"
	      ."<select size=\"8\" name=\"first\" onchange=\"populate2()\">\n"
	      ."<option>Your browser doesn't support javascript</option>\n"
	      ."</select>\n"
	      ."<input type=\"text\" name=\"newtag\" id=\"newtag\" />\n"
	      ."<input type=\"button\" onclick=\"add1(); return false;\" value=\"add tag\" />\n"
	      ."<input type=\"button\" onclick=\"delete1(); return false;\" value=\"delete tag\" />\n"
	      ."</fieldset><fieldset class=\"tags\">\n"
	      ."<legend>Attributes</legend>\n"
	      ."<select size=\"8\" name=\"second\">\n"
	      ."<option>Your browser doesn't support javascript</option>\n"                                                           
	      ."</select>\n"
	      ."<input type=\"text\" name=\"newattr\" id=\"newattr\" />\n"
	      ."<input type=\"button\" onclick=\"add2(); return false;\" value=\"add attr\" />"
	      . "<input type=\"button\" onclick=\"delete2(); return false;\" value=\"delete attr\" />"
	      ."</fieldset>\n"
	      ."<p><input type=\"hidden\" name=\"value\" id=\"packed\" value=\"\" />\n"
	      ;
	    
	    $onclickaction = "pack(); return true";
	    //$preview = true;
	    
	    echo "<script type=\"text/javascript\">\n"
	      ."<!--\n";
	    jsCode($arr);
	    echo "\n// -->\n";
	    echo "</script>\n";   
	    
	    break;
	 default:
	    
	    // generic handling per type:
	    switch ($type) {
	     case 'string':
	     case 'num':
		echo "<label for=\"c_value\">". ADMIN_CONFIG_VALUE ." for $key:</label>\n"
		  ."<input type=\"text\" id=\"c_value\" name=\"value\" value=\"$value\"/>";
		break;
	     case 'boolean':
		echo ADMIN_CONFIG_VALUE ." for $key:</p><p>";
		echo "<input type=\"radio\" id=\"c_value_true\" name=\"value\""
		  .($value == 'true' ? " checked=\"checked\"":"") .""
		  ." value=\"".ADMIN_TRUE."\" "
		  ."/>\n"
		  ."<label for=\"c_value_true\">" . ADMIN_TRUE . "</label>\n";

		echo "<input type=\"radio\" id=\"c_value_false\" name=\"value\""
		  .($value != 'true' ? " checked=\"checked\"":"") .""
		  ." value=\"".ADMIN_FALSE."\" "
		  ."/>\n"
		  ."<label for=\"c_value_false\">" . ADMIN_FALSE . "</label>\n";
		break;
	     case 'enum':		
		echo "<label for=\"c_value\">". ADMIN_CONFIG_VALUE ." for $key:</label>\n"
		  ."\t\t<select name=\"value\" id=\"c_value\">\n";
		$arr = explode(',',$value);
		$idx = array_pop($arr);
		foreach ($arr as $i => $val) {
		    echo "<option value=\"$val\"";
		    if ($i == $idx)
		      echo " selected=\"selected\"";
		    echo ">$val</option>\n";
		}   		
		echo "</select>\n";
		break;
	    }
	}
	
	echo "</p><p>\n";
	echo (isset($preview)?"<input type=\"submit\" name=\"action\" value=\"". ADMIN_PREVIEW_CHANGES ."\""
	      .(isset($onclickaction)?" onclick=\"$onclickaction\"":"") ." />\n":"");
	
	echo "<input type=\"submit\" name=\"action\" value=\"". ADMIN_SUBMIT_CHANGES ."\""
	  .(isset($onclickaction)?" onclick=\"$onclickaction\"":"")
	  ." />\n";
	

	echo "<input type=\"submit\" name=\"action\" value=\"". ADMIN_CANCEL ."\"/>\n"
	  ."</p>\n"
	  ."</form>\n\n</div>\n";
	
	
	$ret__ = ADMIN_DOMAIN_NONE;
	break;
	
     case ADMIN_PREVIEW_CHANGES:
	rss_error('fixme: preview not yet implemented');
	break;
     case ADMIN_SUBMIT_CHANGES:
	$key = $_REQUEST['key'];
	$type = $_REQUEST['type'];
	$value = rss_real_escape_string($_REQUEST['value']);
	
	switch ($key) {
	    
	 case 'rss.input.allowed':
	    $ret = array();
	    $tmp = explode(' ',$value);
	    foreach ($tmp as $key__) {
		if (preg_match('|^[a-zA-Z]+$|',$key__)) {
		    $ret[$key__] = array();
		} else {
		    $tmp2 = array();
		    $attrs = explode(',',$key__);
		    $key__ = array_shift($attrs);
		    foreach($attrs as $attr) {
			$tmp2[$attr] = 1;
		    }
		    $ret[$key__] = $tmp2;
		}
	    }

	    $sql = "update " . getTable('config') . " set value_='"
	      .serialize($ret)
	      ."' where key_='$key'";
	    
	    break;
	 default:
	    switch($type) {
	     case 'string':
		$sql = "update " . getTable('config') . " set value_='$value' where key_='$key'";
		break;
	     case 'num':
		if (!is_numeric($value)) {
		    rss_error("Oops, I was expecting a numeric value, got '$value' instead!");
		    break;
		}
		$sql = "update " . getTable('config') . " set value_='$value' where key_='$key'";
		break;
	     case 'boolean':
		if ($value != ADMIN_TRUE && $value != ADMIN_FALSE) {
		    rss_error('Oops, invalid value for ' . $key .": " . $value);
		    break;
		}
		$sql = "update " . getTable('config') . " set value_='"
		  .($value == ADMIN_TRUE ? 'true':'false') ."'"
		  ." where key_='$key'";
		break;
	     case 'enum':
		$res  = rss_query( "select value_ from " . getTable('config') . " where key_='$key'" );
		list($oldvalue) = rss_fetch_row($res);
		if (strstr($oldvalue,$value) === FALSE) {
		    rss_error("Oops, invalid value '$value' for this config key");
		    break;
		}
		$arr = explode(',',$oldvalue);
		$idx = array_pop($arr);
		$newkey = -1;
		foreach ($arr as $i => $val) {
		    if ($val == $value) {
			$newkey = $i;
		    }
		}    
		reset($arr);
		if ($newkey > -1) {
		    array_push($arr, $newkey);
		    $sql =  "update " . getTable('config') . " set value_='"
		      .implode(',',$arr) ."'"
		      ." where key_='$key'";
		} else {
		    rss_error("Oops, invalid value '$value' for this config key");
		}
		break;
	     default:
		rss_error('Ooops, unknown config type: ' . $type);
		break;
	    }	    
	}
	
	if (isset($sql)) {
	    rss_query( $sql );
	}
	break;
     default: break;
    }
    return $ret__;
}


/////////

function admin_menu() {
    $active = array_key_exists(ADMIN_VIEW,$_REQUEST)?$_REQUEST[ADMIN_VIEW]:null;

    $use_mod_rewrite=	  
      getConfig('rss.output.usemodrewrite');
    
    if (function_exists("apache_get_modules")) {
	$use_mod_rewrite = $use_mod_rewrite && in_array( 'mod_rewrite',apache_get_modules());
    }
    
    echo "\n<ul class=\"navlist\">\n";
    foreach( 
	     array (ADMIN_DOMAIN_CHANNEL,
	       ADMIN_DOMAIN_ITEM,
		    ADMIN_DOMAIN_CONFIG,
		    ADMIN_DOMAIN_FOLDER,
		    ADMIN_DOMAIN_OPML
		    ) as $item) {
	
	if ($use_mod_rewrite) {
	    $link = $item;
	} else {
	    $link = "index.php?view=$item";
	}
	
	$cls = ($item==$active?" class=\"active\"":"");
	echo "\t<li$cls><a href=\"$link\">" .ucfirst($item) ."</a></li>\n";	
    }
    echo "</ul>\n";
}

function admin_kses_to_html($arr) {
    $ret = "";
    foreach ($arr as $tag => $attr) {   
	$ret .= "&lt;$tag";
	foreach ($attr as $nm => $val) {
	    $ret .= "&nbsp;$nm=\"...\"&nbsp;";
	}
	$ret .= "&gt;\n";
    }  
    return $ret;
}
function admin_enum_to_html($arr) {			   
    $idx = array_pop($arr);
    $ret = "";
    foreach ($arr as $i => $val) {
	if ($i == $idx)
	//  $ret .= "<em>";
	  $ret .= "$val";
	//if ($i == $idx)
	//  $ret .= "</em>";
    }
    return $ret;
}

?>
