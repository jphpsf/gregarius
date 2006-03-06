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

function getOpml($url) {
    $arr = parse_weblogsDotCom($url);

    if (!$arr) {
	return false;
    } else {
	return $arr;
    }
}

/**** mbi: from http://www.stargeek.com/php_scripts.php?script=20&cat=blog */
function parse_weblogsDotCom($url) {
    /*
     Grab weblogs.com list of recently updated RSS feeds
     $blogs is array() of feeds
     NAME    name
     URL        address
     WHEN        seconds since ping
     */

    global $blogs,$folder,$inOpmlfolder, $inOpmlItem;
    $folder = LBL_HOME_FOLDER;
    
    $inOpmlfolder = $inOpmlItem = false;
    $opml = getUrl($url);
    $opml = str_replace("\r", '', $opml);
    $opml = str_replace("\n", '', $opml);
    
    $xp = xml_parser_create() or xml_error("couldn't create parser");

    xml_set_element_handler($xp, '_xml_startElement', '_xml_endElement') 
      or xml_error("couldnt set XML handlers");
    
    xml_parse($xp, $opml, true) or xml_error("failed parsing xml at line ".xml_get_current_line_number().": " . xml_error_string());
    xml_parser_free($xp) or xml_error("failed freeing the parser");
    return $blogs;
}

function xml_error($error_msg){
	rss_error($error_msg, RSS_ERROR_ERROR,true);
	echo("\n</div>\n</body></html>\n");
	die();
}

function _xml_startElement($xp, $element, $attr) {
    global $blogs,$folder,$inOpmlfolder, $inOpmlItem;
    if (strcasecmp('outline', $element)) {
	return;
    }
    //$attr['__opml_folder__'] = $folder;
    if (!array_key_exists('XMLURL',$attr) && (array_key_exists('TEXT',$attr)||array_key_exists('TITLE',$attr)) ) {
		//some opml use title instead of text to define a folder (ex: newzcrawler)
		$folder = $attr['TEXT']?$attr['TEXT']:$attr['TITLE'];
		$inOpmlfolder = true;
		$inOpmlItem = false;
		//echo "start of folder $folder\n";
    } else {	
    	$inOpmlItem = true;
    	//echo "start of item $folder/".$attr['TEXT']."\n";
		if ($folder != '') {
	    	$blogs[$folder][] = $attr;
		} else {
		    $blogs[] = $attr;
		}
    }
}

function _xml_endElement($xp, $element) {
    global $blogs,$folder,$inOpmlfolder, $inOpmlItem;
	if (strcasecmp( $element, "outline") === 0) {
		if (!$inOpmlItem && $inOpmlfolder) {
			//echo "end of folder $folder\n";
			// end of folder element!
			$inOpmlfolder = false;
			$folder = LBL_HOME_FOLDER;
		} else {
			// end of item element
			$inOpmlItem = false;
			//echo "end of item\n";
		}	
	} 
    return;
}

/** OPML Export                                                          */
/** OPML2.0 RFC: http://techno-weenie.com/archives/2003/04/01/003067.php */
/** OPML1.0 Specs: http://opml.scripting.com/spec                        */

// This is a pretty lame opml 1.1 generation routine, in that it is not
// recursive, but instead relies on the fact that we only have one level
// of folders.
// Output should be valid xml. (*fingers crossed*)
if (array_key_exists('act',$_REQUEST)) {
    $sql = "select "
      ." c.id, c.title, c.url, c.siteurl, d.name, c.parent, c.descr "
      ." from ". getTable("channels") . " c, " .getTable("folders") ." d "
      ." where d.id = c.parent";
      
      
	if (hidePrivate()) {
		$sql .=" and not(c.mode & " . RSS_MODE_PRIVATE_STATE .") ";	      
	}
	
	// note: should we export deprecated feeds?
    
    if (getConfig('rss.config.absoluteordering')) {
		$sql .= " order by d.position asc, c.position asc";
    } else {
		$sql .=" order by d.name asc, c.title asc";
    }
    
    
    
    
    $res = rss_query($sql);

    $dateRes = rss_query("select max(dateadded) from " . getTable("channels"));
    list($dateModif) = rss_fetch_row($dateRes);
    $dateLabel = date("r", strtotime($dateModif));

    header("Content-Type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"" . getConfig('rss.output.encoding') . "\"?>\n"
      ."<?xml-stylesheet type=\"text/xsl\" href=\"".getPath()."css/opml.xsl\"?>\n"
      ."<!-- Generated by "._TITLE_. " " . _VERSION_ ." -->\n"            
      ."<opml version=\"1.1\">\n";
    
    echo "\t<head>\n"
      ."\t\t<title>"._TITLE_." OPML Feed</title>\n"
      ."\t\t<dateModified>$dateLabel</dateModified>\n"
      ."\t</head>\n"
      ."\t<body>\n";

    $prev_parent=0;
    while (list($id, $title, $url, $siteurl, $name, $parent, $descr) = rss_fetch_row($res)) {
		$descr_ = htmlspecialchars ($descr);
		$descr_ = trim(preg_replace('/(\r\n|\r|\n)/', ' ', $descr_));
		
		$title_ = htmlspecialchars($title);
		
		$url_ = preg_replace('|(https?://)([^:]+:[^@]+@)(.+)$|','\1\3',$url);
		$url_ = htmlspecialchars($url_);
		
		$siteurl_ = preg_replace('|(https?://)([^:]+:[^@]+@)(.+)$|','\1\3',$siteurl);
		$siteurl_ = htmlspecialchars($siteurl_);
		
		$name_ = htmlspecialchars($name);
	
		if ($parent != $prev_parent) {
			if ($prev_parent != 0) {
				echo "\t\t</outline>\n";
			}
			$prev_parent = $parent;
			echo "\t\t<outline text=\"$name_\">\n";
		}
	
		if ($parent > 0) {
		  echo "\t";
		}
		
		echo "\t\t<outline  text=\"$title_\" description=\"$descr_\" type=\"rss\"";
		if ($siteurl != "") {
			echo " htmlUrl=\"$siteurl_\"";
		}
	
		echo " xmlUrl=\"$url_\" />\n";
    }

    if ($prev_parent > 0) {
		echo "\t</outline>\n";
    }

    echo "\t</body>\n</opml>\n";
}
?>
