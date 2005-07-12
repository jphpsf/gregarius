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
# FITNESS FOR A PARTICULAR PURPOSE.	 See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:	   mbonetti at users dot sourceforge dot net
# Web page:	   http://sourceforge.net/projects/gregarius
#
###############################################################################

/**
 * renders the pruneing form
 */
function items() {

    echo  ""
      . "<h2 class=\"trigger\">". LBL_ADMIN_ITEM ."</h2>\n"
      . "<div id=\"admin_items\">\n"
      . "<form method=\"get\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
      . "<fieldset class=\"prune\">\n"
      . "<legend>".LBL_ADMIN_PRUNING."</legend>\n"
      . "<p><input type=\"hidden\" name=\"". CST_ADMIN_DOMAIN ."\" value=\"".CST_ADMIN_DOMAIN_ITEM."\"/>\n"
      . "<label for=\"prune_older\">" . LBL_ADMIN_PRUNE_OLDER ."</label>\n"
      . "<input type=\"text\" size=\"5\" name=\"prune_older\" id=\"prune_older\" value=\"\" />\n"
      . "<select name=\"prune_period\" id=\"prune_period\">\n"
      . "<option>" . LBL_ADMIN_PRUNE_DAYS . "</option>\n"
      . "<option>" . LBL_ADMIN_PRUNE_MONTHS . "</option>\n"
      . "<option>" . LBL_ADMIN_PRUNE_YEARS . "</option>\n"
      . "</select></p>\n"
      . "<p><label for=\"prune_include_sticky\">".LBL_ADMIN_PRUNE_INCLUDE_STICKY."</label>\n"
      . "<input type=\"checkbox\" id=\"prune_include_sticky\" name=\"prune_include_sticky\" value=\"1\"/></p>\n"
      . "<p><label for=\"prune_exclude_tags\">".LBL_ADMIN_PRUNE_EXCLUDE_TAGS."</label>\n"
      . "<input type=\"text\" id=\"prune_exclude_tags\" name=\"prune_exclude_tags\" value=\"\"/></p>\n"
		. "<p style=\"font-size:small; padding:0;margin:0\">".LBL_ADMIN_ALLTAGS_EXPL."</p>\n"
      . "<p class=\"cntr\"><input type=\"submit\" name=\"action\" value=\"". LBL_ADMIN_DELETE2 ."\"/></p>\n"
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
	 case LBL_ADMIN_DELETE2:
		$req = rss_query('select count(*) as cnt from ' .getTable('item')
         ." where !(unread & " . FEED_MODE_DELETED_STATE  .")"
        );
		list($cnt) = rss_fetch_row($req);
		
		$prune_older = (int) $_REQUEST['prune_older'];
		//$prune_keep = (int) $_REQUEST['prune_keep'];
		if (array_key_exists('prune_older',$_REQUEST) && 
			 strlen($_REQUEST['prune_older']) && 
			 is_numeric($_REQUEST['prune_older'])) 	{
			switch ($_REQUEST['prune_period']) {
		 		case LBL_ADMIN_PRUNE_DAYS:
					$period = 'day';
					break;

				 case LBL_ADMIN_PRUNE_MONTHS:
					$period = 'month';
					break;

				 case LBL_ADMIN_PRUNE_YEARS:
					$period = 'year';
					break;

				 default:
				 	rss_error(LBL_ADMIN_ERROR_PRUNING_PERIOD);
					return CST_ADMIN_DOMAIN_ITEM;
				break;
			}

			$sql = " from ".getTable('item') ." i, " .getTable('channels') . " c "
            ." where 1=1 ";
            
            if ($prune_older > 0) {
                $sql .= " and added <  date_sub(now(), interval $prune_older $period) ";
            }
			 
            if (!array_key_exists('prune_include_sticky', $_REQUEST)
                || $_REQUEST['prune_include_sticky'] != '1') {

                $sql .= " and !(unread & " .FEED_MODE_STICKY_STATE .") ";
            }
            
            if (array_key_exists('prune_exclude_tags', $_REQUEST)
                && trim($_REQUEST['prune_exclude_tags'])) {

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
						
						$tsql = " select distinct fid from ". getTable('metatag') . " m, "
						 . getTable('tag') . " t"
						 ." where m.tid=t.id and t.tag in ('"
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
                //echo "<pre>\n";
				//delete the tags for these items
				$sqlids = "select distinct i.id,i.cid,c.url,i.url " . $sql
                    . " and i.cid=c.id order by 2, 1 desc";
                    

				$rs = rss_query($sqlids);
				$ids = array();
				//echo "to be deleted\n";
				while (list($id,$cid,$curl,$iurl) = rss_fetch_row($rs)) {

                    $curls[$cid] = $curl;
                    $cids[$cid][]= array($id,$iurl);
                    
                    //echo "cid=$cid, $id, $iurl\n";
				}
                //echo "\n\n";
                
                if (count($cids)) {
    				// Righto. Lets check which of these ids still is in cache:

    				//$cache = new RSSCache(MAGPIE_CACHE_DIR, MAGPIE_CACHE_AGE);
    				$cacheUrls = array();

    				// extract all the urls for each cached item, keep them sorted
    				// by feed
    				foreach($curls as $cid => $curl) {
                        //$rss = $cache->get($curl .  MAGPIE_OUTPUT_ENCODING);
                	    // suppress warnings because Magpie is rather noisy
                        $old_level = error_reporting(E_ERROR);
            		    $rss = fetch_rss( $curl );
            		    //reset
            		    error_reporting($old_level);

                        $cacheUrls[$cid]= array();
                        //echo "Feed: $cid\n";
                        if ($rss) {
                          foreach($rss->items as $item) {
                            // this comes from util.php:update()

    				        if (array_key_exists('link',$item) && $item['link'] != "") {
    					       $url = $item['link'];
    				        } elseif (array_key_exists('guid',$item) && $item['guid'] != "") {
    					       $url = $item['guid'];
                            } else {
    					       // fall back to something basic
    					       $url =  md5($item['title']);
    				        }
                            $cacheUrls[$cid][] = htmlentities($url);
                            //echo "in cache: $url\n";
                          }
                        }
                    }
                    

                   // now, sort the ids to be deleted into two lists: in chache / to trash
                   $in_cache = array();
                   $to_trash = array();
                   //var_dump($cacheUrls);
                   foreach ($cids as $cid => $ids) {
                        foreach ($ids as $arr) {
                            list($iid,$iurl) = $arr;
                            //echo "examining: $iid (cid $cid) -> $iurl ->";
                            if (array_search($iurl, $cacheUrls[$cid]) !== FALSE) {
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
                         ." set unread = unread | " . FEED_MODE_DELETED_STATE
                         .", description='' "
                        ." where id in ("
                            . implode(", ", $in_cache)
                            .")"
                        );
                    }
                }
				$ret__ = CST_ADMIN_DOMAIN_ITEM;
				
			} else {
				list($cnt_d) = rss_fetch_row(rss_query("select count(distinct(i.id)) as cnt " . $sql
                 . " and !(i.unread & " . FEED_MODE_DELETED_STATE .")"
                 ));
				rss_error(sprintf(LBL_ADMIN_ABOUT_TO_DELETE,$cnt_d,$cnt));

				echo "<form action=\"\" method=\"post\">\n"
		  		."<p><input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_ITEM."\" />\n"
		  		."<input type=\"hidden\" name=\"prune_older\" value=\"".$_REQUEST['prune_older']."\" />\n"
			  	."<input type=\"hidden\" name=\"prune_period\" value=\"".$_REQUEST['prune_period']."\" />\n"
				."<input type=\"hidden\" name=\"".CST_ADMIN_CONFIRMED."\" value=\"1\" />\n"
				."<input type=\"submit\" name=\"action\" value=\"". LBL_ADMIN_DELETE2 ."\" />\n"
				."<input type=\"submit\" name=\"action\" value=\"". LBL_ADMIN_CANCEL ."\"/>\n"
		  		."</p>\n"
				."</form>\n";
			}
	} else {
		rss_error(LBL_ADMIN_ERROR_NO_PERIOD);
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