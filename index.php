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

require_once("init.php");

if (array_key_exists('action', $_POST) 
    && $_POST['action'] != "" 
    && trim($_POST['action']) == trim(MARK_READ)) {
    
    rss_query( "update " .getTable("item") . " set unread=0 where unread=1" );
}

if (array_key_exists('update',$_REQUEST)) {
 update("");   
}

rss_header("",LOCATION_HOME);

sideChannels(false);
items("last items");
rss_footer();


function items($title) {
    echo "\n\n<div id=\"items\" class=\"frame\">";



    // unread items first!
    $sql = "select i.title,  c.title, c.id, i.unread, "
      ." i.url, i.description, c.icon, "      
      ." if (i.pubdate is null, unix_timestamp(i.added), unix_timestamp(i.pubdate)) as ts, "                                                                                  
      ." i.pubdate is not null as ispubdate, "    
      ." i.id, t.tag  "
      ." from ".getTable("item") ." i "
      ." left join ".getTable('metatag') ." m on (i.id=m.fid and m.ttype='item') "
      ." left join ".getTable('tag')." t on (m.tid=t.id) "
      . ", " .getTable("channels") ." c, " .getTable("folders") ." f "
      ." where i.cid = c.id and i.unread=1 and f.id=c.parent";

    if (getConfig('rss.config.absoluteordering')) {
	$sql .= " order by f.position asc, c.position asc";
    } else {
	$sql .=" order by c.parent asc, c.title asc";
    }    
    $sql .=", i.added desc, i.id asc, t.tag"
    
      
      // Problem: to limit or not to limit?
      // Should the frontpage get the whole load of unread items
      // for a channel or not. And if not, should the user get them
      // all when he click the channel title?
      
      //." limit " . getConfig('rss.output.itemsinchannelview')
      ;

    //echo $sql;
    $res0=rss_query($sql);
    if (rss_num_rows($res0) > 0) {

	markAllReadForm();
	
	     $prevId = -1;
        while (list($title_,$ctitle_, $cid_, $unread_, $url_, $descr_,  $icon_, $ts_, $iispubdate_, $iid_, $tag_) = rss_fetch_row($res0)) {
            if ($prevId != $iid_) {
               $items[] = array(
                  $cid_, 
                  $ctitle_, 
                  $icon_ , 
                  $title_ , 
                  1 , 
                  $url_ , 
                  $descr_, 
                  $ts_, 
                  $iispubdate_, 
                  $iid_, 
                  'tags' => array($tag_)
               );
               $prevId = $iid_;
            } else {
               end($items);
               $items[key($items)]['tags'][]=$tag_;
            }
        }
        itemsList ( sprintf(H2_UNREAD_ITEMS , count($items)),  $items );
    }

    // next: unread. Must find a better solution instead of iterating over the channels twice.
    $sql = "select "
      ." c.id, c.title, c.icon "
      ." from " .getTable("channels") . " c, " .getTable("folders") ." f "
      ." where c.parent = f.id ";
    
    if (getConfig('rss.config.absoluteordering')) {
	$sql .= " order by f.position asc, c.position asc";
    } else {
	$sql .=" order by c.parent asc, c.title asc";
    }
    
    

    $res1=rss_query($sql);
    $items = array();
    while (list($cid,$ctitle, $icon) = rss_fetch_row($res1)) {
      
       $sql = "select i.cid, i.title, i.url, i.description, i.unread, "
        ." if (i.pubdate is null, unix_timestamp(i.added), unix_timestamp(i.pubdate)) as ts, "
        ." i.pubdate is not null as ispubdate, "     
        ." i.id, t.tag  "
        ." from " . getTable("item") . " i "
        
        ." left join ".getTable('metatag') ." m on (i.id=m.fid and m.ttype='item') "
        ." left join ".getTable('tag')." t on (m.tid=t.id) "
      
        ." where i.cid  = $cid and i.unread = 0"
        ." order by added desc, id asc, t.tag ";
	     
	$res = rss_query($sql);
	$added = 0;
	$litems=array();
   if (rss_num_rows($res) > 0) {
 	    $prevId = -1;
       while ($added <=3 && list($cid, $ititle, $url, $description, $unread, $ts, $ispubdate, $iid, $tag_) =  rss_fetch_row($res)) {       
         $added++;
         if($prevId != $iid) {
            $litems[] = array(
               $cid,
               $ctitle,
               $icon,
               $ititle,
               $unread,
               $url,
               $description,
               $ts,
               $ispubdate,
               $iid,
               'tags' => array($tag_)
            );
            $prevId = $iid;
         } else {
            end($litems);
            $litems[key($litems)]['tags'][]=$tag_;
            $added--;
         }
       }
    }
    if (count($litems) == 2) {
       $litems = array_slice($litems,0,2);
       $items[] = $litems[0];
       $items[] = $litems[1];
    } elseif(count($litems)) {
       $litems = array_slice($litems,0,1);
       $items[] = $litems[0];
    }
 }
 /*
echo "<pre>";
var_dump($items);
echo "</pre>\n";
*/
 
 itemsList(H2_RECENT_ITEMS,$items);
 echo "</div>\n";
}

function markAllReadForm() {
    echo "<form action=\"". getPath() ."\" method=\"post\" class=\"markallread\">"
      ."<p><input type=\"submit\" name=\"action\" value=\"". MARK_READ ." \"/></p>"
      ."</form>";
}


?>
