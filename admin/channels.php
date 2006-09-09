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
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:	   mbonetti at gmail dot com
# Web page:	 http://gregarius.net/
#
###############################################################################

/*************** Channel management ************/

/**
 * renders the subscribe feed form, and the currently subscribed
 * feeds table
 */

define ('CST_ADMIN_MULTIEDIT','multiedit');


function channels() {
    echo "<h2>". LBL_ADMIN_CHANNELS ."</h2>\n";
    echo "<div id=\"admin_channels\">\n";
    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";
    echo "<p><input type=\"hidden\" name=\"". CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_CHANNEL."\" />\n";
    echo "<label for=\"new_channel\">". LBL_ADMIN_CHANNELS_ADD ."</label>\n";
    echo "<input type=\"text\" name=\"new_channel\" id=\"new_channel\" value=\"http://\" onmouseover=\"clearOnHover(this);\" onfocus=\"this.select()\" />\n";

    echo "<label for=\"add_channel_to_folder\">". LBL_ADMIN_IN_FOLDER . "</label>\n";
    echo rss_toolkit_folders_combo('add_channel_to_folder');
	echo "<label for=\"channel_tags\">" . LBL_TAG_FOLDERS . ":</label>\n";
	echo "<input type=\"text\" name=\"channel_tags\" id=\"channel_tags\" />\n";
    echo "<input type=\"hidden\" name=\"". CST_ADMIN_METAACTION ."\" value=\"ACT_ADMIN_ADD\" />\n";
    echo "<input type=\"submit\" name=\"action\" value=\"". LBL_ADMIN_ADD ."\" /></p>\n";
    echo "<p style=\"font-size:small\">".LBL_ADMIN_ADD_CHANNEL_EXPL."</p>";
    echo "</form>\n\n";

    // bookmarklet
    $b_url = guessTransportProto() . $_SERVER["HTTP_HOST"] . getPath() . "admin/index.php";
    $b_url .= "?domain=feeds&amp;add_channel_to_folder=0&amp;action=Add&amp;new_channel=";

    $bookmarklet = "javascript:void(document.location = "
                   ."('$b_url'.concat(escape(document.location).replace(/\s/,'%2520'))))";

    echo "<p style=\"font-size:small\">" . LBL_ADMIN_BOOKMARKET_LABEL . " <a class=\"bookmarklet\" href=\"$bookmarklet\">".LBL_ADMIN_BOOKMARKLET_TITLE."</a></p>\n";

    // feeds

    echo "<script type=\"text/javascript\">\n"
    ."//<!--\n"
    ."function cbtoggle() {\n"
    ."var c=document.getElementById('mastercb').checked;\n"
    ."var cs=document.getElementById('channeltable').getElementsByTagName('input');\n"
    ."for(i=0;i<cs.length;i++) {\n"
    ."if (cs[i].type == 'checkbox') cs[i].checked = c;\n"
    ."}\n"
    ."}\n"
    ."function clearOnHover(o) {\n"
    ."if (o.value && o.value=='http://') o.value='';\n"
    ."}\n"
    ."// -->\n"
    ."</script>\n";

    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";
    echo "<table id=\"channeltable\">\n"
    ."<tr>\n"
    ."\t<th><input type=\"checkbox\" id=\"mastercb\" onclick=\"cbtoggle();\" /></th>\n"
    ."\t<th>". LBL_ADMIN_CHANNELS_HEADING_TITLE ."</th>\n"
    ."\t<th class=\"cntr\">". LBL_ADMIN_CHANNELS_HEADING_FOLDER ."</th>\n"
    ."\t<th>". LBL_ADMIN_CHANNELS_HEADING_DESCR ."</th>\n"
    ."\t<th>". LBL_TAG_FOLDERS."</th>\n"
    ."\t<th>". LBL_ADMIN_CHANNELS_HEADING_FLAGS."</th>\n";

    if (getConfig('rss.config.absoluteordering')) {
        echo "\t<th>".LBL_ADMIN_CHANNELS_HEADING_MOVE."</th>\n";
    }

    echo "\t<th class=\"cntr\">". LBL_ADMIN_CHANNELS_HEADING_ACTION ."</th>\n"
    ."</tr>\n";

    $sql = "select "
           ." c.id, c.title, c.url, c.siteurl, d.name, c.descr, c.parent, c.icon, c.mode, c.daterefreshed "
           ." from " .getTable("channels") ." c "
           ." inner join " . getTable("folders") ." d "
           ."   on d.id = c.parent ";

    if (getConfig('rss.config.absoluteordering')) {
        $sql .=" order by d.position asc, c.position asc";
    } else {
        $sql .=" order by d.name asc, c.title asc";
    }

    $res = rss_query($sql);
    $cntr = 0;
    while (list($id, $title, $url, $siteurl, $parent, $descr, $pid, $icon, $mode, $daterefreshed) = rss_fetch_row($res)) {

        if (getConfig('rss.output.usemodrewrite')) {
            $outUrl = getPath() . preg_replace("/[^A-Za-z0-9\.]/","_","$title") ."/";
        } else {
            $outUrl = getPath() . "feed.php?channel=$id";
        }

        $parentLabel = $parent == ''? LBL_HOME_FOLDER:$parent;

        $class_ = (($cntr++ % 2 == 0)?"even":"odd");

        // get feed's tags
        $tags = "";
        $sql2 = "select t.id, t.tag from " . getTable('tag') . " t "
                . "inner join " . getTable('metatag') . " m "
                . "  on m.tid = t.id "
                . "where m.ttype = 'channel' and m.fid = $id";
        $res2 = rss_query($sql2);

        while(list ($id_, $name_) = rss_fetch_row($res2)) {
            if (getConfig('rss.output.usemodrewrite')) {
                $tags .= "<a href=\"".getPath()."$name_/\">$name_</a> ";
            } else {
                $tags .= "<a href=\"".getPath()."feed.php?vfolder=$id_\">$name_</a> ";
            }
        }
        
				if(NULL == $daterefreshed) {
					$dead = true;
				} else {
					$dead = (time() - strtotime($daterefreshed) > getConfig('rss.config.deadthreshhold')*60*60 ? true : false);
				}

        $fmode = array();
        if ($mode & RSS_MODE_PRIVATE_STATE) {
            $fmode[] = "P";
        }
        if ($mode & RSS_MODE_DELETED_STATE) {
            $fmode[] = "D";
						$dead = false;
        }

        $slabel = count($fmode)?implode(", ",$fmode):"&nbsp;";
        if ($icon && substr($icon,0,5) == 'blob:') {
            $icon = getPath() . "extlib/favicon.php?url=" .rss_real_escape_string(substr($icon,5));
        }
        echo "<tr class=\"$class_\" id=\"fa$id\">\n"
        ."\t<td><input type=\"checkbox\" name=\"fcb$id\" value=\"$id\" id=\"scb_$id\" /></td>\n"
        ."\t<td>"
        .((getConfig('rss.output.showfavicons') && $icon != "")?
          "<img src=\"$icon\" class=\"favicon\" alt=\"\" width=\"16\" height=\"16\" />":"")
        ."<label for=\"scb_$id\">".($dead ? "<strike>" : "").$title.($dead ? "</strike>" : "")."</label>"
        ."</td>\n"
        ."\t<td class=\"cntr\">".preg_replace('/ /','&nbsp;',$parentLabel)."</td>\n"
        ."\t<td>$descr</td>\n"
        ."\t<td>$tags</td>\n"
        ."\t<td class=\"cntr\">$slabel</td>\n";

        if (getConfig('rss.config.absoluteordering')) {
            echo "\t<td class=\"cntr\"><a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_CHANNEL
            ."&amp;action=". CST_ADMIN_MOVE_UP_ACTION. "&amp;cid=$id\">". LBL_ADMIN_MOVE_UP
            ."</a>&nbsp;-&nbsp;<a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_CHANNEL
            ."&amp;action=". CST_ADMIN_MOVE_DOWN_ACTION ."&amp;cid=$id\">".LBL_ADMIN_MOVE_DOWN ."</a></td>\n";
        }
        echo "\t<td class=\"cntr\"><a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_CHANNEL
        ."&amp;".CST_ADMIN_VIEW."=". CST_ADMIN_DOMAIN_CHANNEL
        ."&amp;action=". CST_ADMIN_EDIT_ACTION. "&amp;cid=$id\">" . LBL_ADMIN_EDIT
        ."</a>|<a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_CHANNEL
        ."&amp;".CST_ADMIN_VIEW."=". CST_ADMIN_DOMAIN_CHANNEL
        ."&amp;action=". CST_ADMIN_DELETE_ACTION ."&amp;cid=$id\">" . LBL_ADMIN_DELETE ."</a></td>\n"
        ."</tr>\n";
    }

    echo "</table>\n";

    echo "<fieldset>\n"
    ."<legend>Selected...</legend>\n"
    ."<p>\n"
    ."<label for=\"me_folder\">".LBL_ADMIN_CHANNEL_FOLDER."</label>\n"
    .rss_toolkit_folders_combo('me_folder',null);

    echo
    "<input type=\"submit\" id=\"me_move_to_folder\" name=\"me_move_to_folder\" value=\"".LBL_ADMIN_CHANNELS_HEADING_MOVE."\" />\n"

    ."<span class=\"vr\">&nbsp;</span>"

    ."<label>".LBL_ADMIN_STATE."</label>\n"
    ."<input type=\"checkbox\" name=\"me_deprecated\" id=\"me_deprecated\" value=\"".RSS_MODE_DELETED_STATE."\" />\n"
    ."<label for=\"me_deprecated\">".LBL_DEPRECATED."</label>\n"

    ."<input type=\"checkbox\" name=\"me_private\" id=\"me_private\" value=\"".RSS_MODE_PRIVATE_STATE."\" />\n"
    ."<label for=\"me_private\">".LBL_PRIVATE."</label>\n"

    ."<input type=\"submit\" id=\"me_state\" name=\"me_state\" value=\"".LBL_ADMIN_STATE_SET."\" />\n"

    ."<span class=\"vr\">&nbsp;</span>"

    ."<input type=\"submit\" id=\"me_delete\" name=\"me_delete\" value=\"".LBL_ADMIN_DELETE2."\" />\n"
    ."<input type=\"checkbox\" name=\"me_do_delete\" id=\"me_do_delete\" value=\"1\" />\n"
    ."<label for=\"me_do_delete\">".LBL_ADMIN_IM_SURE."</label>\n"


    ."<input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_CHANNEL."\" />\n"
    ."<input type=\"hidden\" name=\"action\" value=\"" .CST_ADMIN_MULTIEDIT ."\" />\n"
    ."</p>\n"
    ."</fieldset>\n";



    echo "</form></div>\n\n\n";
}

/**
 * Performs all the feed-related admin actions
 */

function channel_admin() {


    // Fix for #16: Admin (et al.) should not rely on l10n labels for actions:
    // Look for a meta-action first, which should be the (untranslated) *name* of
    // the (translated) action constant.

    // Fixme: should replace 'action's with a constant
    if (array_key_exists(CST_ADMIN_METAACTION,$_REQUEST)) {
        $__action__ = $_REQUEST[CST_ADMIN_METAACTION];
    }
    elseif (array_key_exists('action',$_REQUEST)) {
        $__action__ = $_REQUEST['action'];
    }
    else {
        $__action__ = "";
    }


    $ret__ = CST_ADMIN_DOMAIN_NONE;
    switch ($__action__) {

    case LBL_ADMIN_ADD:
    case 'ACT_ADMIN_ADD':
    case 'Add':

        $label 	= trim(sanitize($_REQUEST['new_channel'], RSS_SANITIZER_URL));
        $fid 		= trim(sanitize($_REQUEST['add_channel_to_folder'], RSS_SANITIZER_NUMERIC));
        list($flabel) = rss_fetch_row(rss_query(
          "select name from " . getTable('folders') . " where id=$fid"));

        // handle "feed:" urls
        if (substr($label, 0,5) == 'feed:') {

            if (substr($label, 0, 11 ) == "feed://http") {
                $label = substr($label,5);
            } else { // handle feed://example.com/rss.xml urls
                $label = "http:" . substr($label,5);
            }
        }

        if ($label != 'http://' &&	substr($label, 0,4) == "http") {
            $tags = $_REQUEST['channel_tags'];
            $ret = add_channel($label,$fid,null,null,$tags);
            //var_dump($ret);
            if (is_array($ret) && $ret[0] > -1) {
                update($ret[0]);
                rss_invalidate_cache();

                // feedback
                $newCid = $ret[0];
                rss_error(sprintf(
                              LBL_ADMIN_OPML_IMPORT_FEED_INFO,
                              htmlentities($label),"/$flabel")
                          . LBL_ADMIN_OK
                          . "&nbsp;[<a href=\"".getPath()."admin/index.php?domain=".CST_ADMIN_DOMAIN_CHANNEL
                          ."&amp;action=edit&amp;cid=$newCid\">"
                          . LBL_ADMIN_EDIT
                          ."</a>]",
                          RSS_ERROR_ERROR,true);
                $ret__ = CST_ADMIN_DOMAIN_CHANNEL;
            }
            elseif  (is_array($ret) && $ret[0] > -2) {
                // okay, something went wrong, maybe thats a html url after all?
                // let's try and see if we can extract some feeds
                $feeds = extractFeeds($label);
                if (!is_array($feeds) || sizeof($feeds) == 0) {
                    rss_error($ret[1], RSS_ERROR_ERROR,true);
                    $ret__ = CST_ADMIN_DOMAIN_CHANNEL;
                } else {
                    //one single feed in the html doc, add that
                    if (is_array($feeds) && sizeof($feeds) == 1 && array_key_exists('href',$feeds[0])) {
                        $ret = add_channel($feeds[0]['href'],$fid);
                        if (is_array($ret) && $ret[0] > -1) {
                            update($ret[0]);
                            rss_invalidate_cache();

                            // feedback
                            $newCid = $ret[0];
                            rss_error(sprintf(
                                          LBL_ADMIN_OPML_IMPORT_FEED_INFO,
                                          htmlentities($label),"/$flabel")
                                      . LBL_ADMIN_OK
                                      . "&nbsp;[<a href=\"".getPath()."admin/index.php?domain="
                                      .CST_ADMIN_DOMAIN_CHANNEL
                                      ."&amp;action=edit&amp;cid=$newCid\">"
                                      . LBL_ADMIN_EDIT
                                      ."</a>]",
                                      RSS_ERROR_ERROR,true);


                            $ret__ = CST_ADMIN_DOMAIN_CHANNEL;
                        } else {
                            // failure
                            rss_error($ret[1], RSS_ERROR_ERROR,true);
                            $ret__ = CST_ADMIN_DOMAIN_CHANNEL;
                        }
                    } else {
                        // multiple feeds in the channel
                        echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
                        ."<p>".sprintf(LBL_ADMIN_FEEDS,$label,$label)."</p>\n";
                        $cnt = 0;
                        while(list($id,$feedarr) = each($feeds)) {
                            // we need an URL
                            if (!array_key_exists('href',$feedarr)) {
                                continue;
                            } else {
                                $href = $feedarr['href'];
                            }

                            if (array_key_exists('type',$feedarr)) {
                                $typeLbl = " [<a href=\"$href\">" . $feedarr['type']
                                           . "</a>]";
                            }

                            $cnt++;

                            if (array_key_exists('title',$feedarr)) {
                                $lbl = $feedarr['title'];
                            }
                            elseif (array_key_exists('type',$feedarr)) {
                                $lbl = $feedarr['type'];
                                $typeLbl = "";
                            }
                            elseif (array_key_exists('href',$feedarr)) {
                                $lbl = $feedarr['href'];
                            }
                            else {
                                $lbl = "Resource $cnt";
                            }

                            echo "<p>\n\t<input class=\"indent\" type=\"radio\" id=\"fd_$cnt\" name=\"new_channel\" "
                            ." value=\"$href\" />\n"
                            ."\t<label for=\"fd_$cnt\">$lbl $typeLbl</label>\n"
                            ."</p>\n";
                        }

                        echo "<p><input type=\"hidden\" name=\"add_channel_to_folder\" value=\"$fid\" />\n"
                        ."<input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_CHANNEL."\" />\n"
                        ."<input type=\"hidden\" name=\"".CST_ADMIN_METAACTION."\" value=\"ACT_ADMIN_ADD\" />\n"
                        ."<input type=\"submit\" class=\"indent\" name=\"action\" value=\"". LBL_ADMIN_ADD ."\" />\n"
                        ."</p>\n</form>\n\n";
                    }
                }
            }
            elseif (is_array($ret))  {
                rss_error($ret[1], RSS_ERROR_ERROR,true);
                $ret__ = CST_ADMIN_DOMAIN_CHANNEL;
            }
            else {
                rss_error(sprintf(LBL_ADMIN_BAD_RSS_URL,$label), RSS_ERROR_ERROR,true);
                $ret__ = CST_ADMIN_DOMAIN_CHANNEL;
            }
        } else {
            rss_error(sprintf(LBL_ADMIN_BAD_RSS_URL,$label), RSS_ERROR_ERROR,true);
            $ret__ = CST_ADMIN_DOMAIN_CHANNEL;
        }
        break;

    case CST_ADMIN_EDIT_ACTION:
        $id = sanitize($_REQUEST['cid'],RSS_SANITIZER_NUMERIC);
        channel_edit_form($id);
        break;


    case CST_ADMIN_DELETE_ACTION:
        $id = sanitize($_REQUEST['cid'],RSS_SANITIZER_NUMERIC);
        if (array_key_exists(CST_ADMIN_CONFIRMED,$_POST) && $_POST[CST_ADMIN_CONFIRMED] == LBL_ADMIN_YES) {
            $rs = rss_query("select distinct id from " .getTable("item") . " where cid=$id");
            $ids = array();
            while (list($did) = rss_fetch_row($rs)) {
                $ids[] = $did;
            }
            if (count($ids)) {
                $sqldel = "delete from " .getTable('metatag') . " where fid in ("
                          . implode(",",$ids)	.")";
                rss_query($sqldel);
            }

            $sql = "delete from " . getTable("item") ." where cid=$id";
            rss_query($sql);
            $sql = "delete from " . getTable("channels") ." where id=$id";
            rss_query($sql);

            // Delete properties
            deleteProperty($id,'rss.input.allowupdates');

            // Invalidate cache
            rss_invalidate_cache();

            $ret__ = CST_ADMIN_DOMAIN_CHANNEL;
        }
        elseif (array_key_exists(CST_ADMIN_CONFIRMED,$_REQUEST) && $_REQUEST[CST_ADMIN_CONFIRMED] == LBL_ADMIN_NO) {
            $ret__ = CST_ADMIN_DOMAIN_CHANNEL;
        }
        else {
            list($cname) = rss_fetch_row(rss_query("select title from " . getTable("channels") ." where id = $id"));

            echo "<form class=\"box\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
            ."<p class=\"error\">";
            printf(LBL_ADMIN_ARE_YOU_SURE,$cname);
            echo "</p>\n"
            ."<p><input type=\"submit\" name=\"".CST_ADMIN_CONFIRMED."\" value=\"". LBL_ADMIN_NO ."\" />\n"
            ."<input type=\"submit\" name=\"".CST_ADMIN_CONFIRMED."\" value=\"". LBL_ADMIN_YES ."\" />\n"
            ."<input type=\"hidden\" name=\"cid\" value=\"$id\" />\n"
            ."<input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_CHANNEL."\" />\n"
            ."<input type=\"hidden\" name=\"action\" value=\"". CST_ADMIN_DELETE_ACTION ."\" />\n"
            ."</p>\n</form>\n";
        }
        break;



    case LBL_ADMIN_IMPORT:
    case 'ACT_ADMIN_IMPORT':


        if (array_key_exists('opml',$_POST) && strlen(trim($_POST['opml'])) > 7) {
            $url = trim( sanitize($_POST['opml'],RSS_SANITIZER_NO_SPACES) );
        }
        elseif (array_key_exists('opmlfile',$_FILES) && $_FILES['opmlfile']['tmp_name']) {
            if (is_uploaded_file($_FILES['opmlfile']['tmp_name'])) {
                $url = $_FILES['opmlfile']['tmp_name'];
            } else {
                $url = '';
            }
        }
        else {
            $url = '';
        }

        if (!$url) {
            $ret__ = CST_ADMIN_DOMAIN_OPML;
            break;
        }


        if (array_key_exists('opml_import_option',$_POST)) {
            $import_opt = $_POST['opml_import_option'];
        } else {
            $import_opt = CST_ADMIN_OPML_IMPORT_MERGE;
        }

        if ($import_opt == CST_ADMIN_OPML_IMPORT_FOLDER) {
            $opmlfid = sanitize($_POST['opml_import_to_folder'], RSS_SANITIZER_NUMERIC);
        } else {
            $opmlfid = getRootFolder();
        }

        @set_time_limit(0);
        @ini_set('max_execution_time', 300);

        // Parse into and OPML object
        $opml=getOpml($url);

        if (sizeof($opml) > 0) {

            if ($import_opt == CST_ADMIN_OPML_IMPORT_WIPE) {
                rss_query("delete from " . getTable("metatag"));
                rss_query("delete from " . getTable("channels"));
                rss_query("delete from " . getTable("item"));
                rss_query("delete from " . getTable("folders") ." where id > 0");
            }

            if ($import_opt == CST_ADMIN_OPML_IMPORT_FOLDER) {
                $fid = $opmlfid;

                list($prev_folder) = rss_fetch_row(rss_query(
                                                 "select name from " .getTable('folders')
                                                 ." where id= $opmlfid "));

            } else {
                $prev_folder = LBL_HOME_FOLDER;
                $fid = 0;
            }


            echo "<div class=\"frame\" style=\"background-color:#eee;font-size:small\"><ul>\n";
            while (list($folder,$items) = each ($opml)) {
                if ($folder != $prev_folder && $import_opt != CST_ADMIN_OPML_IMPORT_FOLDER) {
                    $fid = create_folder(strip_tags($folder), false);
                    $prev_folder = strip_tags($folder);
                }


                for ($i=0;$i<sizeof($opml[$folder]);$i++) {
                    $url_ = isset($opml[$folder][$i]['XMLURL'])?
                            trim($opml[$folder][$i]['XMLURL']):null;
                    $title_ = isset($opml[$folder][$i]['TEXT'])?
                              trim($opml[$folder][$i]['TEXT']):null;
                    // support for title attribute (optional)
                    $title_ = isset($opml[$folder][$i]['TITLE'])?
                              trim($opml[$folder][$i]['TITLE']):$title_;
                    $descr_ = isset($opml[$folder][$i]['DESCRIPTION'])?
                              trim($opml[$folder][$i]['DESCRIPTION']):null;
                    $cats_  = isset($opml[$folder][$i]['CATEGORY'])?
                              trim($opml[$folder][$i]['CATEGORY']):"";
                    $t__ = strip_tags($title_);
                    $d__ = strip_tags($descr_);
                    $f__ = strip_tags($prev_folder);
                    $u__ = sanitize($url_,RSS_SANITIZER_URL);
					$c__ = preg_replace(ALLOWED_TAGS_REGEXP,' ',$cats_);
                    if ($u__) {

                        echo "<li><p>" . sprintf(LBL_ADMIN_OPML_IMPORT_FEED_INFO,$t__,$f__);
                        flush();
                        list($retcde, $retmsg) = add_channel($u__, $fid, $t__, $d__);
                        if ($retcde && count($c__)) {
                        	__exp__submitTag($retcde,$c__,"'channel'");
                        }
                        echo ($retcde<0 ?$retmsg:" OK")."</p></li>\n";
                        flush();
                    }
                }
            }

            echo "</ul>\n<p><b>".LBL_TITLE_UPDATING ."...</b></p>\n";
            echo "</div>\n";
            flush();

            //update all the feeds
            update("");
            rss_invalidate_cache();


        }
        $ret__ = CST_ADMIN_DOMAIN_CHANNEL;
        break;

    case CST_ADMIN_SUBMIT_EDIT:
        $cid = sanitize($_POST['cid'],RSS_SANITIZER_NUMERIC);
        rss_plugin_hook('rss.plugins.admin.feed.properties.submit', null);
        // TBD
        $title= strip_tags(rss_real_escape_string(real_strip_slashes($_POST['c_name'])));
        $url= rss_real_escape_string($_POST['c_url']);
        $siteurl= rss_real_escape_string($_POST['c_siteurl']);
        $parent= rss_real_escape_string($_POST['c_parent']);
        $descr= strip_tags(rss_real_escape_string(real_strip_slashes($_POST['c_descr'])));
        $icon = rss_real_escape_string($_POST['c_icon']);
        $priv = (array_key_exists('c_private',$_POST) && $_POST['c_private'] == '1');
        $tags = rss_real_escape_string($_POST['c_tags']);
        $old_priv = ($_POST['old_priv'] == '1');


        // Feed Properties
        $prop_rss_input_allowupdates = rss_real_escape_string($_POST['prop_rss_input_allowupdates']);
        if ($prop_rss_input_allowupdates == 'default') {
            deleteProperty($cid,'rss.input.allowupdates');
        } else {
            setProperty($cid, 'rss.input.allowupdates' , 'feed', ($prop_rss_input_allowupdates == 1));
        }


        if ($priv != $old_priv) {
            $mode = ", mode = mode ";
            if ($priv) {
                $mode .=  " | " . RSS_MODE_PRIVATE_STATE;
                rss_query ('update ' . getTable('item')
                           ." set unread = unread | " . RSS_MODE_PRIVATE_STATE
                           ." where cid=$cid");
            } else {
                $mode .= " & " .SET_MODE_PUBLIC_STATE;

                rss_query ('update ' . getTable('item')
                           ." set unread = unread & " . SET_MODE_PUBLIC_STATE
                           ." where cid=$cid");
            }

            rss_invalidate_cache();
        } else {
            $mode = "";
        }

        $del = (array_key_exists('c_deleted',$_POST) && $_POST['c_deleted'] == '1');
        $old_del = ($_POST['old_del'] == '1');
        if ($del != $old_del) {
            if ($mode == "") {
                $mode = ", mode = mode ";
            }
            if ($del) {
                $mode .=  " | " . RSS_MODE_DELETED_STATE;
            } else {
                $mode .= " & " . SET_MODE_AVAILABLE_STATE;
            }
        }


        if ($url == '' || substr($url,0,4) != "http") {
            rss_error(sprintf(LBL_ADMIN_BAD_RSS_URL,$url), RSS_ERROR_ERROR,true);
            $ret__ = CST_ADMIN_DOMAIN_CHANNEL;
            break;
        }

        if ($icon && cacheFavicon($icon)) {
            $icon = 'blob:'.$icon;
        }

        $sql = "update " .getTable("channels")
               ." set title='$title', url='$url', siteurl='$siteurl', "
               ." parent=$parent, descr='$descr', icon='$icon' "
               ." $mode where id=$cid";

        rss_query($sql);
        __exp__submitTag($cid,$tags,"'channel'");
        rss_invalidate_cache();
        $ret__ = CST_ADMIN_DOMAIN_CHANNEL;
        break;

    case CST_ADMIN_MOVE_UP_ACTION:
    case CST_ADMIN_MOVE_DOWN_ACTION:
        $id = sanitize($_REQUEST['cid'],RSS_SANITIZER_NUMERIC);
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
                    ($_REQUEST['action'] == CST_ADMIN_MOVE_UP_ACTION && $oposition < $switch_with_position)
                    ||
                    // move up: we look for a higher position
                    ($_REQUEST['action'] == CST_ADMIN_MOVE_DOWN_ACTION && $oposition > $switch_with_position)
                )
            ) {
                $switch_with_position = $oposition;
                $switch_with_id = $oid;
            }
        }
        // right, lets!
        if ($switch_with_position != $position) {
            rss_query( "update " .getTable("channels") ." set position = $switch_with_position where id=$id" );
            rss_query( "update " .getTable("channels") ." set position = $position where id=$switch_with_id" );
            rss_invalidate_cache();
        }
        $ret__ = CST_ADMIN_DOMAIN_CHANNEL;
        break;


    case CST_ADMIN_MULTIEDIT:
        $ret__ = CST_ADMIN_DOMAIN_CHANNEL;
        $ids = array();
        foreach($_REQUEST as $key => $val) {
            if (preg_match('/^fcb([0-9]+)$/',$key,$match)) {
                if (($id = (int) $_REQUEST[$key]) > 0) {
                    $ids[] = $id;
                }
            }
        }

        // no feed selected?
        if (count($ids) == 0) {
            break;
        } else {
            $sqlids=" (" .implode(',',$ids) .")";
        }

        // MOVE TO FOLDER
        if (array_key_exists('me_move_to_folder',$_REQUEST)) {
            $fid= sanitize($_REQUEST['me_folder'],RSS_SANITIZER_NUMERIC);
            $sql = "update " .getTable('channels') . " set parent=$fid where id in $sqlids";
            rss_query($sql);
            /// STATE
        }
        elseif (array_key_exists('me_state',$_REQUEST)) {
            $deprecated = array_key_exists('me_deprecated',$_REQUEST)?$_REQUEST['me_deprecated']:false;
            $private = array_key_exists('me_private',$_REQUEST)?$_REQUEST['me_private']:false;

            if ($private) {
                rss_query ('update ' . getTable('channels')
                           ." set mode = mode | " . RSS_MODE_PRIVATE_STATE
                           ." where id in $sqlids");
                rss_query ('update ' . getTable('item')
                           ." set unread = unread | " . RSS_MODE_PRIVATE_STATE
                           ." where cid in $sqlids");

            } else {
                rss_query ('update ' . getTable('channels')
                           ." set mode = mode & " . SET_MODE_PUBLIC_STATE
                           ." where id in $sqlids");
                rss_query ('update ' . getTable('item')
                           ." set unread = unread & " . SET_MODE_PUBLIC_STATE
                           ." where cid in $sqlids");
            }

            if ($deprecated) {
                rss_query ('update ' . getTable('channels')
                           ." set mode = mode | " . RSS_MODE_DELETED_STATE
                           ." where id in $sqlids");
            } else {
                rss_query ('update ' . getTable('channels')
                           ." set mode = mode & " . SET_MODE_AVAILABLE_STATE
                           ." where id in $sqlids");
            }

            // DELETE
        }
        elseif (array_key_exists('me_delete',$_REQUEST)) {
            if ( array_key_exists('me_do_delete',$_REQUEST) && $_REQUEST['me_do_delete'] == "1") {
                $sql = "delete from " .  getTable('channels')  ." where id in $sqlids";
                rss_query($sql);
            }
        }
        rss_invalidate_cache();
        break;

		case 'dump':
			// Make sure this is a POST
			if(!isset($_POST['dumpact'])) {
				die('Sorry, you can\'t access this via a GET');
			}
			$tbl = array('"','&quot;');
			error_reporting(E_ALL);
			rss_require('schema.php');
			$tables=getExpectedTables();
			unset($tables['cache']);
			//$tables=array('channels','tag','config');
			$bfr='';
			$bfr .= '<'.'?xml version="1.0" encoding="UTF-8"?'.'>'."\n";
			$bfr .= '<dump prefix="'.getTable('').'" date="'.date('r').'">'."\n";
			foreach($tables as $table => $prefixed) {
				$rs = rss_query("select * from $prefixed");
				$bfr .="<$table>\n";
				while($row=rss_fetch_assoc($rs)) {
					$r="<row ";
					foreach($row as $key => $val) {
						$val=htmlspecialchars($val);
						$r.=" $key=\"$val\" ";
					}
					$r .= "/>\n";
					$bfr .=$r;
				}
				$bfr .="</$table>\n";
			}
			$bfr .='</dump>'."\n";
			$gzdata = gzencode($bfr, 9);


			// Delete the output buffer. This is probably a bad thing to do, if the ob'ing is turned off.
			// e.g. data was already sent to the brwoser.
			while (@ob_end_clean());

			// Send the dump to the browser:
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Connection: close");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . strlen($gzdata));
			header('Content-type: application/x-gzip');
			header('Content-disposition: inline; filename="gregarius.dump.'.date('MjSY').'.xml.gz"');

			die($gzdata);

			break;
    default:
        break;
    }
    return $ret__;
}

function channel_edit_form($cid) {
    $sql = "select id, title, url, siteurl, parent, descr, icon, mode, daterefreshed, dateadded from " .getTable("channels") ." where id=$cid";
    $res = rss_query($sql);
    list ($id, $title, $url, $siteurl, $parent, $descr, $icon, $mode, $daterefreshed, $dateadded) = rss_fetch_row($res);
    // get tags
    $sql = "select t.tag from " . getTable('tag')." t " 
         . "  inner join " . getTable('metatag') . " m "
         . "    on m.tid = t.id "
         . "where m.ttype = 'channel' and m.fid = $cid";
    $res = rss_query($sql);
    $tags = "";
    while($r = rss_fetch_assoc($res)) {
        $tags .= $r['tag'] . " ";
    }

    echo "<div>\n";
    echo "\n\n<h2>".LBL_ADMIN_CHANNEL_EDIT_CHANNEL." '$title'</h2>\n";

    echo "<form method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."#fa$cid\" id=\"channeledit\">\n";
    echo "<fieldset id=\"channeleditfs\">";
    // Timestamps
    if(!empty($daterefreshed)) {
      echo "<p><label>" . LBL_ADDED . ": " . date("M-d-Y H:i", strtotime($dateadded)) . "</label></p>"
					."<p><label>" . LBL_LAST_UPDATE . ": ".date("M-d-Y H:i", strtotime($daterefreshed))."</label></p>\n";
    } else {
      echo "<p><label>" . LBL_ADDED . ": " . date("M-d-Y H:i", strtotime($dateadded)) . "</label></p>"
					."<p><label>" . LBL_LAST_UPDATE . ": " . LBL_FOOTER_LAST_MODIF_NEVER . "</label></p>\n";
    }
    // Item name
    echo "<p><label for=\"c_name\">". LBL_ADMIN_CHANNEL_NAME ."</label>\n"
    ."<input type=\"text\" id=\"c_name\" name=\"c_name\" value=\"$title\" />"
    ."<input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"". CST_ADMIN_DOMAIN_CHANNEL."\" />\n"
    ."<input type=\"hidden\" name=\"action\" value=\"". CST_ADMIN_SUBMIT_EDIT ."\" />\n"
    ."<input type=\"hidden\" name=\"cid\" value=\"$cid\" /></p>\n"



    // RSS URL
    ."<p><label for=\"c_url\">". LBL_ADMIN_CHANNEL_RSS_URL ."</label>\n"
    ."<a href=\"$url\">" . LBL_VISIT . "</a>\n"
    ."<input type=\"text\" id=\"c_url\" name=\"c_url\" value=\"$url\" /></p>"

    // Site URL
    ."<p><label for=\"c_siteurl\">". LBL_ADMIN_CHANNEL_SITE_URL ."</label>\n"
    ."<a href=\"$siteurl\">" . LBL_VISIT . "</a>\n"
    ."<input type=\"text\" id=\"c_siteurl\" name=\"c_siteurl\" value=\"$siteurl\" /></p>"

    // Folder
    ."<p><label for=\"c_parent\">". LBL_ADMIN_CHANNEL_FOLDER ."</label>\n"

    .rss_toolkit_folders_combo('c_parent',$parent)
    ."</p>\n";

    // Tags
    echo "<p><label for=\"c_tags\">". LBL_TAG_FOLDERS . ":</label>\n"
    ."<input type=\"text\" id=\"c_tags\" name=\"c_tags\" value=\"$tags\" /></p>";

    // Items state
    if ($mode & RSS_MODE_PRIVATE_STATE) {
        $pchk = " checked=\"checked\" ";
        $old_priv = "1";
    } else {
        $pchk = "";
        $old_priv = "0";
    }

    if ($mode & RSS_MODE_DELETED_STATE) {
        $dchk = " checked=\"checked\" ";
        $old_del = "1";
    } else {
        $dchk = "";
        $old_del = "0";
    }


    echo "<p>\n"
    ."<input style=\"display:inline\" type=\"checkbox\" id=\"c_private\" "
    ." name=\"c_private\" value=\"1\"$pchk />\n"
    ."<label for=\"c_private\">". LBL_ADMIN_CHANNEL_PRIVATE ."</label>\n"
    ."<input type=\"hidden\" name=\"old_priv\" value=\"$old_priv\" />\n"
    ."</p>\n";


    echo "<p>\n"
    ."<input style=\"display:inline\" type=\"checkbox\" id=\"c_deleted\" "
    ." name=\"c_deleted\" value=\"1\"$dchk />\n"
    ."<label for=\"c_deleted\">". LBL_ADMIN_CHANNEL_DELETED ."</label>\n"
    ."<input type=\"hidden\" name=\"old_del\" value=\"$old_del\" />\n"
    ."</p>\n";


    // Description
    $descr = strip_tags($descr);
    echo "<p><label for=\"c_descr\">". LBL_ADMIN_CHANNEL_DESCR ."</label>\n"
    ."<input type=\"text\" id=\"c_descr\" name=\"c_descr\" value=\"$descr\" /></p>\n";

    // Icon
    if (getConfig('rss.output.showfavicons')) {
        echo "<p><label for=\"c_icon\">" . LBL_ADMIN_CHANNEL_ICON ."</label>\n";

        if (trim($icon) != "") {
            if (substr($icon,0,5) == 'blob:') {
                $icon = substr($icon,5);
            }
            echo "<img src=\"$icon\" alt=\"$title\" class=\"favicon\" width=\"16\" height=\"16\" />\n";
            echo "<span>" . LBL_CLEAR_FOR_NONE ."</span>";
        }

        echo "<input type=\"text\" id=\"c_icon\" name=\"c_icon\" value=\"$icon\" /></p>\n";
    } else {
        echo "<p><input type=\"hidden\" name=\"c_icon\" id=\"c_icon\" value=\"$icon\" /></p>\n";
    }

    rss_plugin_hook('rss.plugins.admin.feed.properties', $cid);
    echo "</fieldset>\n";


    // Feed properties
    echo "<fieldset id=\"channeleditpropfs\">";
    echo "<p>"
    ."<span style=\"float:left;\">Allow Gregarius to look for updates in existing items for this feed?</span>"
    ."<span style=\"float:right;\">[<a  href=\"index.php?domain=config&amp;action=edit&amp;key=rss.input.allowupdates&amp;view=config\">Edit the global option</a>]</span>\n"
    ."&nbsp;"
    ."</p>";

    $rss_input_allowupdates_default_current = getProperty($cid,'rss.input.allowupdates');

    $rss_input_allowupdates_default_value =
        $rss_input_allowupdates_default = ("Use global option (" . (getConfig('rss.input.allowupdates')?"Yes":"No") .")");

    echo "<p id=\"rss_input_allowupdates_options\">"

    ."<input type=\"radio\" "
    ."id=\"rss_input_allowupdates_yes\" "
    ."name=\"prop_rss_input_allowupdates\" value=\"1\"  "
    .(($rss_input_allowupdates_default_current === true)?" checked=\"checked\" ":"")
    ."/>\n"
    ."<label for=\"rss_input_allowupdates_yes\">Yes</label>\n"


    ."<input type=\"radio\" "
    ."id=\"rss_input_allowupdates_no\" "
    ."name=\"prop_rss_input_allowupdates\" value=\"0\"  "
    .(($rss_input_allowupdates_default_current === false)?" checked=\"checked\" ":"")
    ."/>\n"
    ."<label for=\"rss_input_allowupdates_no\">No</label>"


    ."<input type=\"radio\" "
    ."id=\"rss_input_allowupdates_default\" "
    ."name=\"prop_rss_input_allowupdates\" value=\"default\"  "
    .(($rss_input_allowupdates_default_current === null)?" checked=\"checked\" ":"")
    ."/>\n"
    ."<label for=\"rss_input_allowupdates_default\">$rss_input_allowupdates_default</label>"


    ."</p>\n";
    echo "</fieldset>\n";


    echo "<p style=\"clear:both; padding: 1em 0\"><input type=\"submit\" name=\"action_\" value=\"". LBL_ADMIN_SUBMIT_CHANGES ."\" /></p>";

    echo "</form></div>\n";
}

?>
