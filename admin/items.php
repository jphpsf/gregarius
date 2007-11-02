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

rss_require('cls/wrappers/toolkit.php');

/**
 * renders the pruning form
 */
function items() {

    echo  ""
    . "<h2 class=\"trigger\">". __('Items:') ."</h2>\n"
    . "<div id=\"admin_items\">\n"
    . "<form method=\"get\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
    . "<fieldset class=\"prune\">\n"
    . "<legend>".__('Pruning')."</legend>\n"
    . "<p><input type=\"hidden\" name=\"". CST_ADMIN_DOMAIN ."\" value=\"".CST_ADMIN_DOMAIN_ITEM."\"/>\n"
    . "<label for=\"prune_older\">" . __('Delete items older than ') ."</label>\n"
    . "<input type=\"text\" size=\"5\" name=\"prune_older\" id=\"prune_older\" value=\"\" />\n"
    . "<select name=\"prune_period\" id=\"prune_period\">\n"
    . "<option>" . __('days') . "</option>\n"
    . "<option>" . __('months') . "</option>\n"
    . "<option>" . __('years') . "</option>\n"
    . "</select></p>\n"
    . "<p><label for=\"prune_channel\">".__('...from these feeds').":</label>\n"
		. rss_toolkit_channels_combo("prune_channel", ALL_CHANNELS_ID, 0, true) . "\n"
    . "</p>\n"
    . "<p><label for=\"prune_include_sticky\">".__('Delete Sticky items too: ')."</label>\n"
    . "<input type=\"checkbox\" id=\"prune_include_sticky\" name=\"prune_include_sticky\" value=\"1\"/></p>\n"
    . "<p><label for=\"prune_include_flag\">".__('Delete Flag items too: ')."</label>\n"
    . "<input type=\"checkbox\" id=\"prune_include_flag\" name=\"prune_include_flag\" value=\"1\"/></p>\n"
    . "<p><label for=\"prune_include_unread\">".__('Delete Unread items too: ')."</label>\n"
    . "<input type=\"checkbox\" id=\"prune_include_unread\" name=\"prune_include_unread\" value=\"1\"/></p>\n"
    . "<p><label for=\"prune_exclude_tags\">".__('Do not delete items tagged... ')."</label>\n"
    . "<input type=\"text\" id=\"prune_exclude_tags\" name=\"prune_exclude_tags\" value=\"\"/></p>\n"
    . "<p style=\"font-size:small; padding:0;margin:0\">".__('(Enter <strong>*</strong> to keep all tagged items)')."</p>\n"
    . "<p class=\"cntr\"><input type=\"submit\" name=\"action\" value=\"". __('Delete') ."\"/></p>\n"
    . "</fieldset>\n"
    . "</form>\n"
    . "</div>\n"
    ;
}

/**
 * performs pruning action
 */
function item_admin() {
    $ret__ = CST_ADMIN_DOMAIN_NONE;
    switch ($_REQUEST['action']) {
    case __('Delete'):
            $req = rss_query('select count(*) as cnt from ' .getTable('item')
                             ." where not(unread & " . RSS_MODE_DELETED_STATE  .")"
                            );
        list($cnt) = rss_fetch_row($req);

        $prune_older = sanitize( $_REQUEST['prune_older'], RSS_SANITIZER_NUMERIC);
        if (array_key_exists('prune_older',$_REQUEST) &&
                strlen($_REQUEST['prune_older']) &&
                is_numeric($_REQUEST['prune_older'])) 	{
            switch ($_REQUEST['prune_period']) {
            case __('days'):
                $period = 'day';
                break;

            case __('months'):
                $period = 'month';
                break;

            case __('years'):
                $period = 'year';
                break;

            default:
                rss_error(__('Invalid pruning period'), RSS_ERROR_ERROR,true);
                return CST_ADMIN_DOMAIN_ITEM;
                break;
            }
            $sql = " from ".getTable('item') ." i inner join " .getTable('channels') . " c on c.id=i.cid "
                   ." where 1=1 ";

            if (array_key_exists('prune_channel', $_REQUEST)) {
                if(ALL_CHANNELS_ID != $_REQUEST['prune_channel']) {
                    $sql .= " and c.id = " . $_REQUEST['prune_channel'] . "";
                }
            }

            if ($prune_older > 0) {
                $prune_older_date=date("Y-m-d H:i:s",strtotime("-${prune_older} ${period}"));
                $sql .= " and ifnull(i.pubdate, i.added) <  '$prune_older_date'";
            }

            if (!array_key_exists('prune_include_sticky', $_REQUEST)
                    || $_REQUEST['prune_include_sticky'] != '1') {
                $sql .= " and not(unread & " .RSS_MODE_STICKY_STATE .") ";
            }

						if (!array_key_exists('prune_include_flag', $_REQUEST)
										|| $_REQUEST['prune_include_flag'] != '1') {
								$sql .= " and not(unread & " . RSS_MODE_FLAG_STATE . ") ";
						}


			if (!array_key_exists('prune_include_unread', $_REQUEST) 
					|| $_REQUEST['prune_include_unread'] != '1') {
			 	$sql .= " and not(unread & " .RSS_MODE_UNREAD_STATE .") "; 
			}
			
			 if (array_key_exists('prune_exclude_tags', $_REQUEST) && trim($_REQUEST['prune_exclude_tags'])) {

                if ( trim($_REQUEST['prune_exclude_tags']) == '*') {
                    $tsql = " select distinct fid from ". getTable('metatag');
                } else {
                    $exclude_tags = explode(" ",$_REQUEST['prune_exclude_tags']);

                    $trimmed_exclude_tags = array();
                    foreach($exclude_tags as $etag) {
                        if ($tetag = rss_real_escape_string(trim($etag))) {
                            $trimmed_exclude_tags[]=$tetag;
                        }
                    }

                    $tsql = " select distinct fid from ". getTable('metatag') . " m "
                            . " inner join " . getTable('tag') . " t"
                            . "   on t.id = m.tid "
                            . " where t.tag in ('"
                            . implode("', '", $trimmed_exclude_tags) ."')";
                }
                $tres = rss_query($tsql);
                $fids = array();
                while(list($fid) = rss_fetch_row($tres)) {
                    $fids[] = $fid;
                }

                if (count($fids)) {
                    $sql .= " and i.id not in (" . implode(",",$fids) .") ";
                }
            }

            if (array_key_exists(CST_ADMIN_CONFIRMED,$_REQUEST)) {
								// Possible fix for #207: max out execution time
								// to avoid timeouts
								@set_time_limit(0);
		        		@ini_set('max_execution_time', 60*10);

                //echo "<pre>\n";
                //delete the tags for these items
                $sqlids = "select distinct i.id,i.cid " . $sql
                          . " order by i.cid, i.id desc";

                $rs = rss_query($sqlids);
                $ids = array();
                $cids = array();
                //echo "to be deleted\n";
                while (list($id,$cid) = rss_fetch_row($rs)) {
                    $cids[$cid][]= $id;

                    //echo "cid=$cid, $id\n";
                }
                //echo "\n\n";

                if (count($cids)) {
                    // Righto. Lets check which of these ids still is in cache:

                    $cacheIds = array();

                    // now, sort the ids to be deleted into two lists: in cache / to trash
                    $in_cache = array();
                    $to_trash = array();
                    foreach ($cids as $cid => $ids) {
                        $rsCache = rss_query("select itemsincache from " . getTable('channels') . " where id=$cid");
                        list($idString) = rss_fetch_row($rsCache);
                        if ($idString) {
                            $cacheIds = unserialize($idString);
                        } else {
                            $cacheIds = array();
                        }
                        foreach ($ids as $iid) {
                            //echo "examining: $iid (cid $cid) ->";
                            if (array_search($iid, $cacheIds) !== FALSE) {
                                $in_cache[] = $iid;
                                //echo " in cache!\n";
                            } else {
                                $to_trash[] = $iid;
                                //echo " not in cache!\n";
                            }
                        }
                    }

                    // cheers, we're set. Now delete the metatag links for *all*
                    // items to be deleted
                    if (count($ids)) {
                        $sqldel = "delete from " .getTable('metatag') . " where fid in ("
                                  . implode(",",array_merge($in_cache,$to_trash))	.")";

                        rss_query($sqldel);
                    }
                    // finally, delete the actual items
                    if (count($to_trash)) {
                        rss_query( "delete from " . getTable('item') ." where id in ("
                                   . implode(", ", $to_trash)
                                   .")"
                                 );
                    }
                    if (count($in_cache)) {
                        rss_query( "update " . getTable('item')
                                   ." set unread = unread | " . RSS_MODE_DELETED_STATE
                                   .", description='' "
                                   ." where id in ("
                                   . implode(", ", $in_cache)
                                   .")"
                                 );
                    }
                    rss_invalidate_cache();
                }
                $ret__ = CST_ADMIN_DOMAIN_ITEM;

            } else {
                list($cnt_d) = rss_fetch_row(rss_query("select count(distinct(i.id)) as cnt " . $sql
                                                       . " and not(i.unread & " . RSS_MODE_DELETED_STATE .")"
                                                      ));
                rss_error(sprintf(__('Warning: you are about to delete %s items (of %s)'),$cnt_d,$cnt), RSS_ERROR_ERROR,true);

                echo "<form action=\"\" method=\"post\">\n"
                ."<p><input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_ITEM."\" />\n"
                ."<input type=\"hidden\" name=\"prune_older\" value=\"".$_REQUEST['prune_older']."\" />\n"
                ."<input type=\"hidden\" name=\"prune_period\" value=\"".$_REQUEST['prune_period']."\" />\n"
                ."<input type=\"hidden\" name=\"".CST_ADMIN_CONFIRMED."\" value=\"1\" />\n"
                ."<input type=\"submit\" name=\"action\" value=\"". __('Delete') ."\" />\n"
                ."<input type=\"submit\" name=\"action\" value=\"". __('Cancel') ."\"/>\n"
                ."</p>\n"
                ."</form>\n";
            }
        } else {
            rss_error(__('oops, no period specified'), RSS_ERROR_ERROR,true);
            $ret__ = CST_ADMIN_DOMAIN_ITEM;
        }

        break;
    default:
        $ret__ = CST_ADMIN_DOMAIN_ITEM;
        break;
    }

    return $ret__;
}

?>
