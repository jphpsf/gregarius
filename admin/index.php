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



define ('ADMIN_DOMAIN','domain');
define ('ADMIN_DOMAIN_FOLDER','folder');
define ('ADMIN_DOMAIN_CHANNEL','channel');
define ('ADMIN_DELETE_ACTION','delete');
define ('ADMIN_EDIT_ACTION','edit');
define ('ADMIN_MOVE_UP_ACTION','up');
define ('ADMIN_MOVE_DOWN_ACTION','down');
define ('ADMIN_SUBMIT_EDIT','submit_edit');

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

rss_header(TITLE_ADMIN,LOCATION_ADMIN);

admin_main($auth);
rss_footer();

///////////////////////////////////////////////////////////////////////////////////////////

function admin_main($authorised) {
    echo "\n<div id=\"channel_admin\" class=\"frame\">";

    if ($authorised) {
	if (array_key_exists(ADMIN_DOMAIN,$_REQUEST)) {
	    switch($_REQUEST[ADMIN_DOMAIN]) {
	     case ADMIN_DOMAIN_FOLDER:
		folder_admin();
		break;
	     case ADMIN_DOMAIN_CHANNEL:
		channel_admin();
		break;
	     default:
		break;
	    }
	}

	channels();
	folders();
	opml();

	//echo "<hr class=\"clearer\"/>\n</div>\n";
	
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
    echo "\n\n<div id=\"admin_channels\">\n";
    echo "<h2>". ADMIN_CHANNELS ."</h2>\n";
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
      ."\t<th>". ADMIN_CHANNELS_HEADING_FOLDER ."</th>\n"
      ."\t<th>". ADMIN_CHANNELS_HEADING_DESCR ."</th>\n";

    if (getConfig('ABSOLUTE_ORDERING')) {
	echo "\t<th>".ADMIN_CHANNELS_HEADING_MOVE."</th>\n";
    }

    echo "\t<th>". ADMIN_CHANNELS_HEADING_ACTION ."</th>\n"
      ."</tr>\n";

    $sql = "select "
      ." c.id, c.title, c.url, c.siteurl, d.name, c.descr, c.parent, c.icon "
      ." from " .getTable("channels") ." c, " . getTable("folders") ." d "
      ." where d.id = c.parent ";

    if (getConfig('ABSOLUTE_ORDERING')) {
	$sql .=" order by d.position asc, c.position asc";
    } else {
	$sql .=" order by c.parent asc, c.title asc";
    }

    $res = rss_query($sql);
    $cntr = 0;
    while (list($id, $title, $url, $siteurl, $parent, $descr, $pid, $icon) = rss_fetch_row($res)) {

	if (getConfig('USE_MODREWRITE')) {
	    $outUrl = getPath() . preg_replace("/[^A-Za-z0-9\.]/","_","$title") ."/";
	} else {
	    $outUrl = getPath() . "feed.php?channel=$id";
	}

	$parentLabel = $parent == ''? HOME_FOLDER:$parent;

	$class_ = (($cntr++ % 2 == 0)?"even":"odd");

	echo "<tr class=\"$class_\">\n"
	  ."\t<td>"
	  .((getConfig('USE_FAVICONS') && $icon != "")?
	    "<img src=\"$icon\" class=\"favicon\" alt=\"$title\" width=\"16\" height=\"16\" />":"")
	    ."<a href=\"$outUrl\">$title</a></td>\n"
	  ."\t<td>$parentLabel</td>\n"
	  ."\t<td>$descr</td>\n";

	if (getConfig('ABSOLUTE_ORDERING')) {
	    echo "\t<td><a href=\"".$_SERVER['PHP_SELF']. "?".ADMIN_DOMAIN."=". ADMIN_DOMAIN_CHANNEL
	      ."&amp;action=". ADMIN_MOVE_UP_ACTION. "&amp;cid=$id\">". ADMIN_MOVE_UP
	      ."</a>&nbsp;-&nbsp;<a href=\"".$_SERVER['PHP_SELF']. "?".ADMIN_DOMAIN."=". ADMIN_DOMAIN_CHANNEL
	      ."&amp;action=". ADMIN_MOVE_DOWN_ACTION ."&amp;cid=$id\">".ADMIN_MOVE_DOWN ."</a></td>\n";
	}
	echo "\t<td><a href=\"".$_SERVER['PHP_SELF']. "?".ADMIN_DOMAIN."=". ADMIN_DOMAIN_CHANNEL
	  ."&amp;action=". ADMIN_EDIT_ACTION. "&amp;cid=$id\">" . ADMIN_EDIT
	  ."</a>|<a href=\"".$_SERVER['PHP_SELF']. "?".ADMIN_DOMAIN."=". ADMIN_DOMAIN_CHANNEL
	  ."&amp;action=". ADMIN_DELETE_ACTION ."&amp;cid=$id\">" . ADMIN_DELETE ."</a></td>\n"
	  ."</tr>\n";
    }

    echo "</table>\n</div>\n\n\n";

}

function opml() {
    //opml import
    echo "\n\n<div id=\"admin_opml\">\n";
    echo "<h2>". ADMIN_OPML ."</h2>\n";
    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";
    echo "<p><input type=\"hidden\" name=\"". ADMIN_DOMAIN ."\" value=\"".ADMIN_DOMAIN_CHANNEL."\"/>\n";
    echo "<label for=\"opml\">" . ADMIN_OPML_IMPORT ."</label>\n";
    echo "<input type=\"text\"  name=\"opml\" id=\"opml\" value=\"http://\" onfocus=\"this.select()\"/>\n";
    echo "<input type=\"submit\" name=\"action\" value=\"". ADMIN_IMPORT ."\"/></p>\n";

    echo "</form>\n";

    opml_export_form();
    echo "</div>\n";
}

function channel_admin() {

    switch ($_REQUEST['action']) {
     case ADMIN_ADD:
	$label = trim($_REQUEST['new_channel']);
	$fid = trim($_REQUEST['add_channel_to_folder']);
	if ($label != 'http://' &&  substr($label, 0,4) == "http") {
	    $ret = add_channel($label,$fid);
	    if (is_array($ret) && $ret[0] > -1) {
		update($ret[0]);
	    } else {
		// okay, something went wrong, maybe thats a html url after all?
		// let's try and see if we can extract some feeds
		$feeds = extractFeeds($label);
		if (!is_array($feeds) || sizeof($feeds) == 0) {
		    rss_error($ret[1]);
		} else {
		    //one single feed in the html doc, add that
		    if (is_array($feeds) && sizeof($feeds) == 1 && array_key_exists('href',$feeds[0])) {
			$ret = add_channel($feeds[0]['href'],$fid);
			if (is_array($ret) && $ret[0] > -1) {
			    update($ret[0]);
			} else {
			    // failure
			    rss_error($ret[1]);
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
	break;

     case ADMIN_DELETE_ACTION:
	$id = $_REQUEST['cid'];
	if (array_key_exists('confirmed',$_REQUEST) && $_REQUEST['confirmed'] == ADMIN_YES) {
	    $sql = "delete from " . getTable("item") ." where cid=$id";
	    rss_query($sql);
	    $sql = "delete from " . getTable("channels") ." where id=$id";
	    rss_query($sql);
	} elseif (array_key_exists('confirmed',$_REQUEST) && $_REQUEST['confirmed'] == ADMIN_NO) {
	    // nop;
	} else {
	    list($cname) = rss_fetch_row(rss_query("select title from " . getTable("channels") ." where id = $id"));

	    echo "<form class=\"box\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
	      ."<p class=\"error\">"; printf(ADMIN_ARE_YOU_SURE,$cname); echo "</p>\n"
	      ."<p><input type=\"submit\" name=\"confirmed\" value=\"". ADMIN_NO ."\"/>\n"
	      ."<input type=\"submit\" name=\"confirmed\" value=\"". ADMIN_YES ."\"/>\n"
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
	    break;
	}

	$sql = "update " .getTable("channels") ." set title='$title', url='$url', siteurl='$siteurl', "
	  ." parent=$parent, descr='$descr', icon='$icon' where id=$cid";

	rss_query($sql);
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
	break;

     default: break;
    }
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
      ."<p><label for=\"c_parent\">". ADMIN_CHANNEL_FOLDER ."</label>\n"
      ."<select name=\"c_parent\" id=\"c_parent\">\n";

    $sql = " select id, name from " . getTable("folders") ." order by id asc";
    $res = rss_query($sql);
    while (list($pid, $pname) = rss_fetch_row($res)) {
	if ($pid == $parent) {
	    $selected = " selected=\"selected\"";
	} else {
	    $selected = "";
	}

	$parentLabel = ($pname == "")?HOME_FOLDER:$pname;
	//if ($pid > 0) {
	echo "\t<option value=\"$pid\" $selected>$parentLabel</option>\n";
	//}
    }

    echo "</select></p>\n";

    // Description
    echo "<p><label for=\"c_descr\">". ADMIN_CHANNEL_DESCR ."</label>\n"
      ."<input type=\"text\" id=\"c_descr\" name=\"c_descr\" value=\"$descr\"/></p>\n";

    // Icon
    if (getConfig('USE_FAVICONS')) {
	echo "<p><label for=\"c_icon\">" . ADMIN_CHANNEL_ICON ."</label>\n";

	if (trim($icon) != "") {
	    echo "<img src=\"$icon\" alt=\"$c_name\" class=\"favicon\" width=\"16\" height=\"16\" />\n";
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
    echo "\n<div id=\"admin_folders\">\n<h2>".ADMIN_FOLDERS."</h2>\n";
    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";

    echo "<p><input type=\"hidden\" name=\"".ADMIN_DOMAIN."\" value=\"".ADMIN_DOMAIN_FOLDER."\"/>\n";

    echo "<label for=\"new_folder\">".ADMIN_FOLDERS_ADD."</label>\n"
      ."<input type=\"text\" id=\"new_folder\" name=\"new_folder\" value=\"\" />"
      ."<input type=\"submit\" name=\"action\" value=\"". ADMIN_ADD ."\"/>\n"
      ."</p></form>\n\n";

    echo "<table id=\"foldertable\">\n"
      ."<tr>\n"
      ."\t<th>". ADMIN_CHANNELS_HEADING_TITLE ."</th>\n";

    if (getConfig('ABSOLUTE_ORDERING')) {
	echo "\t<th>".ADMIN_CHANNELS_HEADING_MOVE."</th>\n";
    }

    echo "\t<th>". ADMIN_CHANNELS_HEADING_ACTION ."</th>\n"
      ."</tr>\n";

    $sql = "select id,name from " .getTable("folders");

    if (getConfig('ABSOLUTE_ORDERING')) {
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

	if (getConfig('ABSOLUTE_ORDERING')) {
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

function folder_combo($name) {
    echo "\n<select name=\"$name\" id=\"$name\">\n";
    $res = rss_query("select id, name from " .getTable("folders") ." order by id asc");
    while (list($id, $name) = rss_fetch_row($res)) {
	echo "\t<option value=\"$id\">" .  (($name == "")?HOME_FOLDER:$name)  ."</option>\n";
    }
    echo "</select>\n";
}

function folder_admin() {

    switch ($_REQUEST['action']) {
     case ADMIN_EDIT_ACTION:
	folder_edit($_REQUEST['fid']);
	break;

     case ADMIN_DELETE_ACTION:
	$id = $_REQUEST['fid'];
	assert(is_numeric($id));

	if ($id == 0) {
	    rss_error("You can't delete the " . HOME_FOLDER . " folder");
	    return;
	}

	if (array_key_exists('confirmed',$_REQUEST) && $_REQUEST['confirmed'] == ADMIN_YES) {
	    $sql = "delete from " . getTable("folders") ." where id=$id";
	    rss_query($sql);
            $sql = "update " . getTable("channels") ." set parent=0 where parent=$id";
	    rss_query($sql);
	} elseif (array_key_exists('confirmed',$_REQUEST) && $_REQUEST['confirmed'] == ADMIN_NO) {
	    // nop;
	} else {
	    list($fname) = rss_fetch_row(rss_query("select name from " .getTable("folders") ." where id = $id"));

	    echo "<form class=\"box\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
	      ."<p class=\"error\">"; printf(ADMIN_ARE_YOU_SURE,$fname); echo "</p>\n"
	      ."<p><input type=\"submit\" name=\"confirmed\" value=\"". ADMIN_NO ."\"/>\n"
	      ."<input type=\"submit\" name=\"confirmed\" value=\"". ADMIN_YES ."\"/>\n"
	      ."<input type=\"hidden\" name=\"fid\" value=\"$id\"/>\n"
	      ."<input type=\"hidden\" name=\"".ADMIN_DOMAIN."\" value=\"".ADMIN_DOMAIN_FOLDER."\"/>\n"
	      ."<input type=\"hidden\" name=\"action\" value=\"". ADMIN_DELETE_ACTION ."\"/>\n"
	      ."</p>\n</form>\n";
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
		return;
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

    if (!np) {
	$np = "0";
    }

    rss_query("insert into " .getTable("folders") ." (name,position) values ('" . rss_real_escape_string($label) ."', $np)");
    list($fid) = rss_fetch_row( rss_query("select id from " .getTable("folders") ." where name='". rss_real_escape_string($label) ."'"));
    return $fid;
}

/*************** OPML Export ************/

function opml_export_form() {
    if (getConfig('USE_MODREWRITE')) {
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

function real_strip_slashes($string) {
    if (stripslashes($string) == $string) {
	return $string;
    }
    return real_strip_slashes(stripslashes($string));
}
?>
