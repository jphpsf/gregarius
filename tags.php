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

require_once('init.php');

define ('MAX_TAGS_PER_ITEM',5);


// these are the fontsizes on the weighted list at /tag/
define ('SMALLEST',12);
define ('LARGEST',45);
define ('UNIT','px');


function submit_tag($id,$tags) {
	$ftags = preg_replace('/[^a-zA-Z0-9\ _\.]/','', trim($tags));
	$tarr = array_slice(explode(" ",$ftags),0,MAX_TAGS_PER_ITEM);
	$ftags = implode(" ",updateTags($id,$tarr));
	return "$id,". $ftags;
}

function updateTags($fid,$tags) {
	rss_query("delete from " .getTable('metatag') . " where fid=$fid and ttype='item'");
	$ret = array();
	foreach($tags as $tag) {
		$ttag = trim($tag);
		if ($ttag == "" || in_array($ttag,$ret)) {
			continue;
		}
		rss_query( "insert into ". getTable('tag'). " (tag) values ('$ttag')", false );
		$tid = 0;
		if(rss_sql_error() == 1062) {
			list($tid)=rss_fetch_row(rss_query("select id from " .getTable('tag') . " where tag='$ttag'"));
		} else {
			$tid = rss_insert_id();
		}
		if ($tid) {
			rss_query( "insert into ". getTable('metatag'). " (fid,tid) values ($fid,$tid)" );
			if (rss_sql_error() == 0) {
				$ret[] = $ttag;
			}
		}
	}
	sort($ret);
	return $ret;
}

$sajax_request_type = "POST";
$sajax_debug_mode = 0;
$sajax_remote_uri = getPath() . "tags.php";
sajax_init();
sajax_export("submit_tag");

/* spit out the javascript for this bugger */
if (array_key_exists('js',$_GET)) {
	sajax_show_javascript(); 
	
	// and here is s'more javascript for field editing...
?>


function submit_tag_cb(ret) {
	data= ret.split(',');
	id=data[0];
	tags=data[1].split(' ');
	var fld=document.getElementById("th" + id);
        var html = "<a href=\"<?= getPath() 
        . (getConfig('rss.output.usemodrewrite')?'tag/':'tags.php?alltags') 
        ?>\"><?= TAG_TAGS ?></a>:&nbsp;"
              + "<span id=\"t" + id + "\">";
        for (i=0;i<tags.length;i++) {
          html = html + "<a href=\"<?= getPath()
			 . (getConfig('rss.output.usemodrewrite')?'tag/':'tags.php?tag=') 
          ?>" + tags[i] + "\">" + tags[i] + "</a> ";
        }
        html = (html +"</span>&nbsp;<a id=\"tt"+id+"\" href=\"\" onmouseup=\"_et("+id+");\">"
                + "<?= TAG_EDIT ?></a>");
	fld.innerHTML = html;
}

function submit_tag(id,tags) {
	x_submit_tag(id, tags, submit_tag_cb);
}


function _et(id) {
	var toggle = document.getElementById("tt" + id);
	if (toggle.innerHTML == "<?= TAG_SUBMIT ?>") {
		var fld = document.getElementById("tfield" + id);
                toggle.innerHTML="<?= TAG_SUBMITTING ?>";
		submit_tag(id,fld.value);
	} else if (toggle.innerHTML == <? echo "\"" . TAG_EDIT . "\"" ?>) {
                var elem=document.getElementById("t"+id);
                
                <? /*
                newlink = document.createElement("a");
                newlink.setAttribute("href","#");
                newlink.setAttribute("onclick","cancel_edit(" 
                     + id + ",'" + elem.innerHTML +"');");
                newlink.innerHTML = "[cancel]";
                toggle.parentNode.appendChild(newlink);
		 */ ?>
		toggle.innerHTML="<?= TAG_SUBMIT ?>";
		

		//get rid of the hyperlinks
		var tags = elem.innerHTML.replace(/<\/?a[^>]*>(\ $)?/gi,"");
		elem.innerHTML = "<input class=\"tagedit\" id=\"tfield"+id+"\" type=\"text\" value=\"" + tags + "\" />";
		elem.firstChild.focus();
	} 
	return false;
}

<?
	exit();
} elseif(array_key_exists('rs',$_REQUEST)) {
    // this one handles the xmlhttprequest call from the above javascript
    sajax_handle_client_request();
    exit();
} elseif(array_key_exists('tag',$_GET)) {
    // while this one displays a list of items for the requested tag
    $tag = rss_real_escape_string($_GET['tag']);

    //first, find all the items which have been tagged with the sought tag.
    $sql = "select distinct(i.id) from ".getTable('item')." i, "
      .getTable('metatag')." m, ".getTable('tag')." t "
      ." where i.id=m.fid and t.id=m.tid and t.tag='$tag' and m.ttype='item'";
    
	$res = rss_query($sql);
   $ids = array();
   while (list($id) = rss_fetch_row($res)) {
		$ids[] = $id;
   }

	$gotsome = count($ids) > 0;
	if ($gotsome) {    
		 // ok now look up the fields for those items
		$sql = ""
			
			// standard fields
			."select i.title,  c.title, c.id, i.unread, "
			." i.url, i.description, c.icon, "
			." if (i.pubdate is null, unix_timestamp(i.added), unix_timestamp(i.pubdate)) as ts, "
			." i.pubdate is not null as ispubdate, "
			." i.id, t.tag  "
	
			// standard left-joins and normal joins
			." from ".getTable("item") ." i "
			." left join ".getTable('metatag') ." m on (i.id=m.fid and m.ttype='item') "
			." left join ".getTable('tag')." t on (m.tid=t.id) "
			. ", " .getTable("channels") ." c, " .getTable("folders") ." f "
			
			
			." where "
			." i.id in (".implode(",",$ids).") "
			." and i.cid = c.id  and f.id=c.parent "
			
			// order by unread first
			." order by i.unread desc, "
			
			."f.position asc, c.position asc, i.added desc, i.id asc, t.tag";
		$res = rss_query($sql);  
		
		$items = array();
   
		if (rss_num_rows($res) > 0) {
			$prevId = -1;
			while (list($title_,$ctitle_, $cid_, $unread_, $url_, $descr_,  $icon_, $ts_, $iispubdate_, $iid_, $tag_) = rss_fetch_row($res)) {
				if ($prevId != $iid_) {
					$items[] = array(
						$cid_,
						$ctitle_,
						$icon_ ,
						$title_ ,
						$unread_ ,
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
			
		}    
   }
   
    // done! Render some stuff
    rss_header("Tags " . TITLE_SEP . " " . $tag);
    sideChannels(false);
    
    echo "\n\n<div id=\"items\" class=\"frame\">\n";
    
    
    if ($gotsome) {
		 
		 echo "<h2>" . count($items) . " " . (count($items) > 1 ? ITEMS:ITEM)
			." " 
			. (count($items) > 1 || count($items) == 0? TAG_TAGGEDP:TAG_TAGGED) .""
			. " \"" . $tag . "\"</h2>\n";


		itemsList ( "",  $items, IL_NO_COLLAPSE );
	} else {
		echo "<p style=\"height: 10em; text-align:center\">";
		printf(TAG_ERROR_NO_TAG,$tag);
		echo "</p>";
	}
 	echo "</div>\n";
 	rss_footer();
    
    
    
} elseif(array_key_exists('alltags',$_GET)) {
    
    // the all tags weighted list
    $sql = "select tag,count(*) as cnt from "
      . getTable('metatag') . ","
      . getTable('tag') .""
      ." where tid=id group by tid order by 1";
    
    $res = rss_query($sql);
    $tags = array();
    $max = 0;
    $min = 100000;
    $cntr=0;
    while(list($tag,$cnt) = rss_fetch_row($res)) {
		$tags[$tag] = $cnt;
		$cntr++;
    }
    
    
    // Credits: Matt, http://www.hitormiss.org/about/
    // http://dev.wp-plugins.org/file/weighted-category-list/weighted_categories.php?format=txt
    $spread = max($tags) - min($tags);
    if ($spread <= 0) { $spread = 1; };
    $fontspread = LARGEST - SMALLEST;
    $fontstep = $spread / $fontspread;
    if ($fontspread <= 0) { $fontspread = 1; }
    $ret = "";
    foreach ($tags as $tag => $cnt) {
		$taglink = getPath() .
		(getConfig('rss.output.usemodrewrite')?"tag/$tag":"tags.php?tag=$tag");
		$ret .= "\t<a href=\"$taglink\" title=\"$cnt "
		  .($cnt > 1 || $cnt == 0 ? ITEMS:ITEM)."\" style=\"font-size: ".
		  (SMALLEST + ($cnt/$fontstep)). UNIT.";\">$tag</a> \n";
    }
  
    // done! Render some stuff
    rss_header("Tags " . TITLE_SEP . " " . TAG_ALL_TAGS);
    sideChannels(false);    
    echo "\n\n<div id=\"items\" class=\"frame\">\n"
    //."<h2>$cntr " . TAG_TAGS . "</h2>\n"
	."<div id=\"alltags\" class=\"frame\">$ret</div>\n"
    ."\n\n</div>\n";
    rss_footer();
}

?>
