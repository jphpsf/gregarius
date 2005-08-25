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

require_once ('init.php');
rss_require('config.php');

define('PUSH_BOUNDARY', "-------- =_aaaaaaaaaa0");
define('ERROR_NOERROR', "");
define('ERROR_WARNING', " warning");
define('ERROR_ERROR', " error");
define('NO_NEW_ITEMS', '-');
define ('UPDATING','...');

define ('AJAX_BATCH_SIZE',3);

define ('THIS_FILE',basename(__FILE__));

define ('GROUP_SPLITTER',',');
define ('SUB_SPLITTER','|');
define ('SUB_SUB_SPLITTER','.');


/**
 * Generic Update. Note that this is an "abstract" class
 * (from the java perspective) as specific sub-classes must
 * override a couple (implicitly) abstract method, such as 
 * render()
 */
class Update {

	var $chans = array ();

	function Update() {

		set_time_limit(0);
		@ini_set('max_execution_time', 300);
		$this->populate();
	}

	function populate() {
		$sql = "select c.id, c.url, c.title from ".getTable("channels") . " c, "
		. getTable('folders') . " f ";
		$sql .= " where not(c.mode & ".FEED_MODE_DELETED_STATE.") ";
		$sql .= " and c.parent = f.id ";
		
		if (hidePrivate()) {
			$sql .= " and not(mode & ".FEED_MODE_PRIVATE_STATE.") ";
		}

		if (getConfig('rss.config.absoluteordering')) {
			$sql .= " order by f.position asc, c.position asc";
		} else {
			$sql .= " order by f.name, c.title asc";
		}
		
		$res = rss_query($sql);
		while (list ($cid, $url, $title) = rss_fetch_row($res)) {
			$this->chans[] = array ($cid, $url, $title);
		}
	}

	function cleanUp($newIds) {
		if (count($newIds) > 0 && getConfig('rss.config.markreadonupdate')) {
			rss_query("update ".getTable("item")." set unread = unread & ".SET_MODE_READ_STATE." where unread & ".FEED_MODE_UNREAD_STATE." and id not in (".implode(",", $newIds).")");
		}
	}
}


/**
 * HTTP Server Push update
 */
class HTTPServerPushUpdate extends Update {

	function HTTPServerPushUpdate() {
		parent::Update();

		$GLOBALS['rss']->header->appendHeader("Connection: close");
		$GLOBALS['rss']->header->appendHeader("Content-type: multipart/x-mixed-replace;boundary=\"".PUSH_BOUNDARY."\"");
		$GLOBALS['rss']->header->options |= HDR_NO_OUPUTBUFFERING;
		rss_set_hook('rss.plugins.bodystart', "pushHeaderCallBack");
		rss_set_hook('rss.plugins.bodyend', "pushFooterCallBack");
	}

	function render() {
		$newIds = array ();

		echo "<h2>".sprintf(LBL_UPDATE_H2, $GLOBALS['rss']->feedList->getFeedCount())."</h2>\n"."<table id=\"updatetable\">\n"."<tr>\n"."<th class=\"lc\">".LBL_UPDATE_CHANNEL."</th>\n"."<th class=\"mc\">".LBL_UPDATE_STATUS."</th>\n"."<th class=\"rc\">".LBL_UPDATE_UNREAD."</th>\n"."</tr>";

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

			if ($error & MAGPIE_FEED_ORIGIN_CACHE) {
				if ($error & MAGPIE_FEED_ORIGIN_HTTP_304) {
					$label = LBL_UPDATE_NOT_MODIFIED;
					$cls = ERROR_NOERROR;
				}
				elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_TIMEOUT) {
					$label = LBL_UPDATE_CACHE_TIMEOUT;
					$cls = ERROR_WARNING;
				}
				elseif ($error & MAGPIE_FEED_ORIGIN_NOT_FETCHED) {
					$label = LBL_UPDATE_STATUS_CACHED;
					$cls = ERROR_NOERROR;
				}
				elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_404) {
					$label = LBL_UPDATE_NOT_FOUND;
					$cls = ERROR_ERROR;
				} else {
					$label = $error;
					$cls = ERROR_ERROR;
				}
			}
			elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_200) {
				$label = LBL_UPDATE_STATUS_OK;
				$cls = ERROR_NOERROR;
			} else {
				if (is_numeric($error)) {
					$label = LBL_UPDATE_STATUS_ERROR;
					$cls = ERROR_ERROR;
				} else {
					// shoud contain MagpieError at this point
					$label = $error;
					$cls = ERROR_ERROR;
				}
			}
			
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

        if (!hidePrivate()) {
		  parent::cleanUp($newIds);
        }
	}
}


/**
 * AJAXUpdate updates feeds via AJAX. It's a bit more server-intesive
 * than HTTP Server Push
 */
class AJAXUpdate extends Update {
	
	function AJAXUpdate() {
		parent::Update();
		$GLOBALS['rss']->header->extraHTML .= "<script type=\"text/javascript\" src=\""
			.getPath()."update.php?js\"></script>\n";
	}	
	
	function render() {

		echo "<h2 style=\"margin-bottom:1em;\">". sprintf(LBL_UPDATE_H2,$GLOBALS['rss']->feedList->getFeedCount()) ."</h2>\n";

		echo "<table id=\"updatetable\">\n"
		  ."<tr>\n"
		  ."<th class=\"lc\">".LBL_UPDATE_CHANNEL."</th>\n"
		  ."<th class=\"mc\">".LBL_UPDATE_STATUS."</th>\n"
		  ."<th class=\"rc\">".LBL_UPDATE_UNREAD."</th>\n"
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
		echo "doUpdate();\n</script>\n";
	}
}

/**
 * SilentUpdate updates the feeds silently for those lame 
 * browsers out there that do not support HTTP Server Push
 * or AJAX
 */
class SilentUpdate extends Update {
	function SilentUpdate() {
		parent::Update();
	}
	
	function render() {
		$ret = update("");
		if (is_array($ret)) {
			$newIds = $ret[1];
		}

        if (!hidePrivate()) {
		  parent::cleanUp($newIds);
        }

		
		if (!array_key_exists('silent', $_GET)) {
            rss_redirect();
		}
	}
}

function pushHeaderCallBack() {
	echo "WARNING: YOUR BROWSER DOESN'T SUPPORT THIS SERVER-PUSH TECHNOLOGY.";
	echo "\n".PUSH_BOUNDARY."\n";
	echo "Content-Type: text/html\n\n";
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
		if ($error & MAGPIE_FEED_ORIGIN_CACHE) {
			if ($error & MAGPIE_FEED_ORIGIN_HTTP_304) {
				$label = LBL_UPDATE_NOT_MODIFIED;
				$cls = ERROR_NOERROR;
			} elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_TIMEOUT) {
				$label = LBL_UPDATE_CACHE_TIMEOUT;
				$cls = ERROR_WARNING;
			 } elseif ($error & MAGPIE_FEED_ORIGIN_NOT_FETCHED) {
				$label = LBL_UPDATE_STATUS_CACHED;
				$cls = ERROR_NOERROR;
			 } elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_404) {
				$label = LBL_UPDATE_NOT_FOUND;
				$cls = ERROR_ERROR;
			 } else {
				$label =  $error;
				$cls = ERROR_ERROR;
			 }    	    
		} elseif ($error & MAGPIE_FEED_ORIGIN_HTTP_200) {
			$label = LBL_UPDATE_STATUS_OK;
			$cls = ERROR_NOERROR;
		} else {
			if (is_numeric($error)) {
				$label= LBL_UPDATE_STATUS_ERROR;
				$cls  = ERROR_ERROR;
			} else {
				// shoud contain MagpieError at this point
				$label= $error;
				$cls = ERROR_ERROR;
			}	      
		}
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

function ajaxUpdateJavacript () {
	echo sajax_get_javascript();
?>
/// End Sajax javascript
/// From here on: Copyright (C) 2003 - 2005 Marco Bonetti, gregarius.net
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
	for (j=0;(j+document.feedPointer) < document.feedCount && j < <?= AJAX_BATCH_SIZE ?>;j++) {
			batch[j]=ids[j+document.feedPointer];
			if (tdr = document.getElementById('u_r_' + batch[j])) {
					tdr.innerHTML = '<?= UPDATING ?>';
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
		superdarr = data.replace(/[^0-9a-zA-Z\(\)\|\s\.,]/gi,'').split('<?= GROUP_SPLITTER ?>');
		var lastId = 0;
		for (var i=0;i<superdarr.length;i++) {
			darr = superdarr[i].replace(/[^0-9a-zA-Z\(\)\|\s,\.]/gi,'').split('<?= SUB_SPLITTER ?>');
			
			// channel ID
			id=darr[0];
			lastId = id;
			// unread count
			if (darr[1] && (unread_ids = darr[1].split('<?= SUB_SUB_SPLITTER ?>'))) {
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
			doUpdate();
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
			sBatch += '<?= GROUP_SPLITTER ?>';
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
				ids += '<?= GROUP_SPLITTER ?>';
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
