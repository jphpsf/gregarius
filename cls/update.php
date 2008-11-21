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

define('PUSH_BOUNDARY', "-------- =_aaaaaaaaaa0");
define('ERROR_NOERROR', "");
define('ERROR_WARNING', " warning");
define('ERROR_ERROR', " error");
define('NO_NEW_ITEMS', '-');
define ('UPDATING','...');
define ('DEFAULT_CID', -1);


define ('THIS_FILE',basename(__FILE__));

define ('GROUP_SPLITTER',',');
define ('SUB_SPLITTER','|');
define ('SUB_SUB_SPLITTER','.');

// Define the ajax parallel and batch size from the config options
if (getConfig('rss.config.ajaxbatchsize')) {
    define ('AJAX_BATCH_SIZE',getConfig('rss.config.ajaxbatchsize'));
} else {
    define ('AJAX_BATCH_SIZE',3);
}

if (getConfig('rss.config.ajaxparallelsize')) {
    define ('AJAX_PARALLEL_SIZE',getConfig('rss.config.ajaxparallelsize'));
} else {
    define ('AJAX_PARALLEL_SIZE',3);
}


/**
 * Generic Update. Note that this is an "abstract" class
 * (from the java perspective) as specific sub-classes must
 * override a couple (implicitly) abstract method, such as 
 * render()
 */
class Update {

    var $chans = array ();

    function Update($doPopulate = true, $updatePrivateAlso = false, $cid = DEFAULT_CID) {
        rss_plugin_hook('rss.plugins.updates.before', null);
        if($doPopulate) {
            $this->populate($updatePrivateAlso, $cid);
        }

        // Script timeout: ten seconds per feed should be a good upper limit
        @set_time_limit(0);
        @ini_set('max_execution_time', (10 * count($this->chans) + 300));
    }

    function populate($updatePrivateAlso = false, $cid) {
        $cid = (int)$cid;
        $sql = "select c.id, c.url, c.title from ".getTable("channels") . " c "
               . " inner join " . getTable('folders') . " f on f.id = c.parent "
               . " where not(c.mode & ".RSS_MODE_DELETED_STATE.") ";

        if (hidePrivate() && !$updatePrivateAlso) {
            $sql .= " and not(mode & ".RSS_MODE_PRIVATE_STATE.") ";
        }
        
        if(DEFAULT_CID != $cid) {
        	$sql .= " and c.id = " . $cid . " ";
				} else {
					if (getConfig('rss.config.absoluteordering')) {
							$sql .= " order by f.position asc, c.position asc";
					} else {
							$sql .= " order by f.name, c.title asc";
					}
				}

        $res = rss_query($sql);
        while (list ($cid, $url, $title) = rss_fetch_row($res)) {
            $this->chans[] = array ($cid, $url, $title);
        }
    }

    function cleanUp($newIds, $ignorePrivate = false) {
        if (!hidePrivate() || $ignorePrivate) {
            if (count($newIds) > 0 && getConfig('rss.config.markreadonupdate')) {
                rss_query("update ".getTable("item")." set unread = unread & "
                          .SET_MODE_READ_STATE." where unread & ".RSS_MODE_UNREAD_STATE
                          ." and id not in (".implode(",", $newIds).")");
            }
        }

        setProperty('__meta__','meta.lastupdate','misc',time());

        if (count($newIds) > 0) {
            rss_invalidate_cache();
        }
        rss_plugin_hook('rss.plugins.updates.after', null);
    }

    function magpieError($error) {
        if (is_numeric($error) && ($error & MAGPIE_FEED_ORIGIN_CACHE)) {
            if ($error & MAGPIE_FEED_ORIGIN_HTTP_304) {
                $label = __('OK (304 Not modified)');
                $cls = ERROR_NOERROR;
            }
            elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_TIMEOUT) {
                $label = __('HTTP Timeout (Local cache)');
                $cls = ERROR_ERROR;
            }
            elseif ($error & MAGPIE_FEED_ORIGIN_NOT_FETCHED) {
                $label = __('OK (Local cache)');
                $cls = ERROR_NOERROR;
            }
            elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_404) {
                $label = __('404 Not Found (Local cache)');
                $cls = ERROR_ERROR;
            }
            elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_403) {
                $label = __('403 Forbidden (Local cache)');
                $cls = ERROR_ERROR;
            }
            else {
                $label = $error;
                $cls = ERROR_ERROR;
            }
        }
        elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_200) {
            $label = __('OK (HTTP 200)');
            $cls = ERROR_NOERROR;
        }
        else {
            if (is_numeric($error)) {
                $label = __('ERROR') ." $error";
                $cls = ERROR_ERROR;
            } else {
                // shoud contain MagpieError at this point
                $label = $error;
                $cls = ERROR_ERROR;
            }
        }

        return array( $label, $cls);
    }
}

/**
 * HTTP Server Push update
 */
class HTTPServerPushUpdate extends Update {

    function HTTPServerPushUpdate($cid) {
        parent::Update($doPopulate = true, $updatePrivateAlso = false, $cid);

        $GLOBALS['rss']->header->appendHeader("Connection: close");
        $GLOBALS['rss']->header->appendHeader("Content-type: multipart/x-mixed-replace;boundary=\"".PUSH_BOUNDARY."\"");
        $GLOBALS['rss']->header->options |= HDR_NO_OUPUTBUFFERING;
        rss_set_hook('rss.plugins.bodystart', "pushHeaderCallBack");
        rss_set_hook('rss.plugins.bodyend', "pushFooterCallBack");

        ob_implicit_flush();
    }

    function render() {
        $newIds = array ();

        echo
        "<h2>".sprintf(__('Updating %d Feeds...'), count($this -> chans))."</h2>\n"
        ."<table id=\"updatetable\">\n"
        ."<tr>\n"
        ."<th class=\"lc\">".__('Feed')."</th>\n"
        ."<th class=\"mc\">".__('Status')."</th>\n"
        ."<th class=\"rc\">".__('New Items')."</th>\n"
        ."</tr>";

        foreach ($this->chans as $chan) {
            list ($cid, $url, $title) = $chan;
            echo "<tr>\n";
            echo "<td class=\"lc\">$title</td>\n";
            flush();

            $ret = update($cid);

            if (is_array($ret)) {
                list ($error, $unreadIds) = $ret;
                $newIds = array_merge($newIds, $unreadIds);
            } else {
                $error = 0;
                $unreadIds = array ();
            }
            $unread = count($unreadIds);

            list($label,$cls) = parent::magpieError($error);

            if ($cls == ERROR_ERROR && !defined("UPDATE_ERROR")) {
                define("UPDATE_ERROR", true);
            }
            echo "<td class=\"mc$cls\">$label</td>\n";
            echo "<td class=\"rc\">". ($unread > 0 ? $unread : NO_NEW_ITEMS)."</td>\n";
            echo "</tr>\n";
            flush();

        }

        echo "</table>\n";
        echo "<p><a href=\"".getPath()."\">Redirecting...</a></p>\n";
        flush();
        // Sleep two seconds
        sleep(2);

        parent::cleanUp($newIds);
    }
}

/**
 * AJAXUpdate updates feeds via AJAX. It's a bit more server-intesive
 * than HTTP Server Push
 */
class AJAXUpdate extends Update {

    function AJAXUpdate($cid) {
        parent::Update($doPopulate = true, $updatePrivateAlso = false, $cid);
        $GLOBALS['rss']->header->extraHTML .= "<script type=\"text/javascript\" src=\""
                                              .getPath()."update.php?js\"></script>\n";
    }

    function render() {

        echo "<h2 style=\"margin-bottom:1em;\">". sprintf(__('Updating %d Feeds...'),count($this -> chans)) ."</h2>\n";

        echo "<table id=\"updatetable\">\n"
        ."<tr>\n"
        ."<th class=\"lc\">".__('Feed')."</th>\n"
        ."<th class=\"mc\">".__('Status')."</th>\n"
        ."<th class=\"rc\">".__('New Items')."</th>\n"
        ."</tr>\n";


        foreach ($this->chans as $chan) {
            list ($cid, $url, $title) = $chan;
            echo "<tr id=\"tr_$cid\">\n";
            echo "<td class=\"lc\" id=\"u_l_$cid\">$title</td>\n";
            echo "<td class=\"mc\" id=\"u_m_$cid\">&nbsp;</td>\n";
            echo "<td class=\"rc\" id=\"u_r_$cid\">&nbsp;</td>\n";
            echo "</tr>\n";
        }

        echo "</table>\n";
        echo "<script type=\"text/javascript\">\n";
        echo "function runAjaxUpdate() { \n";
        echo "    for (k =0; k < " . AJAX_PARALLEL_SIZE . "; k++){\n";
        echo "    doUpdate();\n";
        echo "    }\n";
        echo "}\n";
        // Fix for IE's stupid "Operation Aborted" Error
        echo "   if (window.addEventListener) window.addEventListener(\"load\",runAjaxUpdate,false); else if (window.attachEvent) window.attachEvent(\"onload\",runAjaxUpdate);\n";
        echo "</script>\n";
    }
}

class CommandLineUpdate extends Update {
    function CommandLineUpdate($cid) {
        parent::Update($doPopulate = true, $updatePrivateAlso = true, $cid);
    }

    function render() {
        $newIds = array();
        foreach ($this->chans as $chan) {
            list ($cid, $url, $title) = $chan;
            echo "$title ...\t";
            flush();
            $ret = update($cid);

            if (is_array($ret)) {
                list ($error, $unreadIds) = $ret;
                $newIds = array_merge($newIds, $unreadIds);
            } else {
                $error = 0;
                $unreadIds = array ();
            }
            $unread = count($unreadIds);

            list($label,$cls) = parent::magpieError($error);
            echo "\n$label, $unread " . __('New Items') . "\n\n";
            flush();

        }
        parent::cleanUp($newIds, $ignorePrivate = true);
    }
}

class MobileUpdate extends Update {
    function MobileUpdate($cid) {
        parent::Update($doPopulate = true, $updatePrivateAlso = false, $cid);
    }
    
    function render() {
        $newIds = array();
        foreach ($this->chans as $chan) {
            list ($cid, $url, $title) = $chan;
            echo "$title ...\t";
            flush();
            $ret = update($cid);

            if (is_array($ret)) {
                list ($error, $unreadIds) = $ret;
                $newIds = array_merge($newIds, $unreadIds);
            } else {
                $error = 0;
                $unreadIds = array ();
            }
            $unread = count($unreadIds);
            list($label,$cls) = parent::magpieError($error);
            echo "\n$label, $unread " . __('New Items') . "<br />";
            flush();
        }
    }
}

/**
 * CommandLineUpdateNews updates the feeds and displays only feeds with
 * errors or new items.
 */
class CommandLineUpdateNews extends CommandLineUpdate {
    function render() {
        $newIds = array();
        foreach ($this->chans as $chan) {
            list ($cid, $url, $title) = $chan;
            $ret = update($cid);

            if (is_array($ret)) {
                list ($error, $unreadIds) = $ret;
                $newIds = array_merge($newIds, $unreadIds);
            } else {
                $error = 0;
                $unreadIds = array();
            }
            $unread = count($unreadIds);

            list($label, $cls) = parent::magpieError($error);

            if (($cls != ERROR_NOERROR) || ($unread > 0)) {
                echo "$title ...\t";
                flush();
                echo "\n$label, $unread " . __('New Items') . "\n\n";
                flush();
            }
        }

        if (!hidePrivate()) {
            parent::cleanUp($newIds);
        }
    }
}

/**
 * SilentUpdate updates the feeds silently for those lame 
 * browsers out there that do not support HTTP Server Push
 * or AJAX
 */
class SilentUpdate extends Update {
    function SilentUpdate($cid) {
        parent::Update($doPopulate = false, $updatePrivateAlso = false, $cid);
    }

    function render() {
        $newIds = array();
        $ret = update("");
        if (is_array($ret)) {
            $newIds = $ret[1];
        }

        parent::cleanUp($newIds);

        if (!array_key_exists('silent', $_GET)) {
            rss_redirect();
        }

    }
}

function pushHeaderCallBack() {
    echo "WARNING: YOUR BROWSER DOESN'T SUPPORT THIS SERVER-PUSH TECHNOLOGY.";
    echo "\n".PUSH_BOUNDARY."\n";
    echo "Content-Type: text/html\n\n";
    flush();
}

function pushFooterCallBack() {
    if (defined("UPDATE_ERROR") && UPDATE_ERROR)  {
        sleep(10);
    }

    echo "\n".PUSH_BOUNDARY."\n";
    echo "Content-Type: text/html\n\n"
    ."<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n"
    ."<html>\n"."<head>\n"."<title>Redirecting...</title>\n"
    ."<meta http-equiv=\"refresh\" content=\"0;url=index.php\"/>\n"
    ."</head>\n"."<body/>\n"."</html>";

    echo "\n".PUSH_BOUNDARY."\n";
    echo "WARNING: YOUR BROWSER DOESN'T SUPPORT THIS SERVER-PUSH TECHNOLOGY.\n";

    flush();
}

/**
 * This is the function that will handle the server-side AJAX update request
 */
function ajaxUpdate($ids) {

    $aids = explode(GROUP_SPLITTER,$ids);
    $sret = array();
    foreach($aids as $id) {
        $ret = update($id);
        if (is_array($ret)) {
            $error = $ret[0];
            $unread = implode(SUB_SUB_SPLITTER,$ret[1]); //count($ret[1]);
        } else {
            $error = 0;
            $unread = 0;
        }
        list($label,$cls) = AJAXUpdate::magpieError($error);
        $sret[] = "$id".SUB_SPLITTER."$unread".SUB_SPLITTER."$label".SUB_SPLITTER."$cls";
    }
    // just cat the return elements together, as SAJAX
    // can't handle complex return types yet.
    return trim(implode(GROUP_SPLITTER,$sret));
}

function ajaxUpdateCleanup($ids) {
    $aids = explode(GROUP_SPLITTER,$ids);
    Update::cleanUp($aids);
    return 0;
}

function ajaxUpdateJavascript () {
    echo sajax_get_javascript();
    ?>
    /// End Sajax javascript
    /// From here on: Copyright (C) 2003 - 2006 Marco Bonetti, gregarius.net
    /// Released under GPL


    document.cdata = new Array();
    document.new_ids = new Array();
    document.returnedUpdates = 0;
    document.feedCount = 0;
    document.feedPointer=0;


    /**
     * main entry point: fetch the different channel ids and launch the 
     * channel updates. Might want to have this synchronous to be nice on
     * the webserver, not sure whether it can be done with Sajax, though
     */
    function doUpdate() {

        var ids = new Array();
        kids = document.getElementById('updatetable').getElementsByTagName("tr");
        var i=0;
        var added = 0;
        //collect feed ids, titles
        for (i=0; i < kids.length; i++) {
            var id = kids[i].id.replace(/[^0-9]/gi,'');
            if (id) {
                ids[added++] = id;
                if (!document.cdata[id])  {
                    var title = '';
                    if ( tdl = document.getElementById('u_l_' + id)) {
                        title = tdl.innerHTML;
                        if (title) {
                            document.cdata[id] = title;
                        }
                    }
                }
            }
        }

        document.feedCount = added;
        var batch = new Array();
        var j=0;
        for (j=0;(j+document.feedPointer) < document.feedCount && j < <?php echo  AJAX_BATCH_SIZE ?>;j++) {
            batch[j]=ids[j+document.feedPointer];
            if (tdr = document.getElementById('u_r_' + batch[j])) {
                tdr.innerHTML = '<?php echo  UPDATING ?>';
            }
        }
        document.feedPointer += j;

        ajaxUpdate(batch);

    }


    /**
     * AJAX Callback function: split the returned data into variables
     * and play some DOM tricks
     */
    function ajaxUpdate_cb(data) {
        //alert(data);
        superdarr = data.replace(/[^0-9a-zA-Z\(\)\|\s\.,]/gi,'').split('<?php echo  GROUP_SPLITTER ?>');
        var lastId = 0;
        for (var i=0;i<superdarr.length;i++) {
            darr = superdarr[i].replace(/[^0-9a-zA-Z\(\)\|\s,\.]/gi,'').split('<?php echo  SUB_SPLITTER ?>');

            // channel ID
            id=darr[0];
            lastId = id;
            // unread count
            if (darr[1] && (unread_ids = darr[1].split('<?php echo  SUB_SUB_SPLITTER ?>'))) {
                //alert('unread ids: ' + unread_ids);
                document.new_ids=document.new_ids.concat(unread_ids);
                //alert('document ids: ' + document.new_ids);
                unread = unread_ids.length;
            } else {
                unread=0;
            }

            // an error/result-label
            label=darr[2];
            // class to be applied to the label, unused for now
            cls=darr[3];

            // update the table for this row
            if (mtd = document.getElementById('u_m_'+id)) {
                //alert(cls);
                mtd.innerHTML = '<span class="' + cls + '">' + label + '</span>';
            }
            if (rtd = document.getElementById('u_r_'+id)) {
                rtd.innerHTML = unread;
            }

            // hoorray, we got a result back.
            document.returnedUpdates++;

        }

        if (document.feedPointer < document.feedCount) {
            if (document.returnedUpdates >= document.feedPointer -
                               <?php echo AJAX_BATCH_SIZE * (AJAX_PARALLEL_SIZE - 1) ?>) {
                doUpdate();
            }
        } else {
            ajaxUpdateCleanup();
            window.setTimeout('redirect()', 3000);
        }

    }
    function ajaxUpdateCleanup_cb(dummy) {}

    function redirect() {
        document.location = "index.php";
    }

    function ajaxUpdate(batch) {
        sBatch='';
        //alert(batch.length);
        for(var i=0;i<batch.length;i++) {
            sBatch += batch[i];
            if (i<batch.length-1) {
                sBatch += '<?php echo  GROUP_SPLITTER ?>';
            }
        }
        x_ajaxUpdate(sBatch,ajaxUpdate_cb);
    }

    function ajaxUpdateCleanup() {
        ids = '';
        for(var i=0;i< document.new_ids.length;i++) {
            var id = Number(document.new_ids[i]);
            if (id > 0) {
                ids += id ;
                if (i<document.new_ids.length-1) {
                    ids += '<?php echo  GROUP_SPLITTER ?>';
                }
            }
        }
        //alert(ids);
        if (ids != '') {
            x_ajaxUpdateCleanup(ids,ajaxUpdateCleanup_cb);
        }
    }

    <?php
    flush();
}



?>
