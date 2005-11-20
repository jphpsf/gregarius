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
# E-mail:      mbonetti at gmail dot com
# Web page:    http://gregarius.net/
#
###############################################################################

//
class Header {

	var $active;
	var $cidfid;
	var $onLoadAction;
	var $options;
	var $links;
	var $javascriptFiles = array();
	var $docTitle;
	var $redirectUrl="";
	var $redirectTimeout=0;
	var $rawTitle;
	var $extraHTML = "";
	
	
	function Header($title = "", $active = 0, $cidfid = null, $onLoadAction = "", $options = HDR_NONE, $links = NULL) {
		
		_pf('Header() ctor');
		$this -> docTitle = $title;
		$this -> active = $active;
		$this -> cidfid = $cidfid;
		$this -> onLoadAction = $onLoadAction;
		$this -> options = $options;
		$this -> rawTitle = $title;
		
		$this -> extraHeaders = array();



		$this -> docTitle = makeTitle($title);
		if (getConfig("rss.output.titleunreadcnt") && 
			is_array($cidfid) && 
			($uc = getUnreadCount($cidfid['cid'], $cidfid['fid']))) {
			$this->docTitle .= " ($uc ".LBL_UNREAD.")";
		}

		

		if ($active == 1 && (MINUTE * getConfig('rss.config.refreshafter')) >= (40 * MINUTE)) {
			$this->redirectUrl = guessTransportProto().$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
			if (substr($this->redirectUrl, -1) != "/") {
				$this->redirectUrl .= "/";
			}
			$this->redirectUrl .= "update.php";
			$this->redirectTimeout = MINUTE * getConfig('rss.config.refreshafter');
		}

		$this -> links = array();
		$this -> links[] = array('start','Home',getPath());
		$this -> links[] = array('search','Search',getPath() ."search.php");
		$this -> links[] = array('tags','Tags',getPath(). (getConfig('rss.output.usemodrewrite') ? "tag/" : "tags.php?alltags"));
		
		if ($links != NULL) {
			//var_dump($links);
			foreach ($links as $rel => $link) {
				$this -> links[] = array($rel,$link['title'],$link['href']);
			}
		}
		
		$this -> javascriptFiles[] = getPath()."ajax.php?js";		
		$this -> javascriptFiles[] = getPath()."extlib/md5.js";		

		if (getConfig('rss.output.channelcollapse')) {
			$this -> javascriptFiles[] = getPath()."extlib/fcollapse.js";
		}

		
		$GLOBALS['rss'] -> sideMenu = new SideMenu();
		$GLOBALS['rss'] -> sideMenu -> addMenu(LBL_H2_CHANNELS,'0' , "_side('0')");
		$GLOBALS['rss'] -> sideMenu -> addMenu(LBL_TAG_FOLDERS, '1', "_side('1')");
		$GLOBALS['rss'] -> sideMenu -> addMenu(LBL_TAG_TAGS, '2', "_side('2')");
	}

	function appendHeader($hdr) {
		$this ->extraHeaders[] = $hdr;
	}
	
	
	function preRender() {
		_pf('Header preRender()');
		
		if (!($this->options & HDR_NO_CACHECONTROL) && getConfig('rss.output.cachecontrol')) {
			$etag = getETag();
			$hdrs = rss_getallheaders();
			if (array_key_exists('If-None-Match', $hdrs) && $hdrs['If-None-Match'] == $etag) {
				header("HTTP/1.1 304 Not Modified");
				flush();
				exit ();
			} else {
				header('Last-Modified: '.gmstrftime("%a, %d %b %Y %T %Z", getLastModif()));
				header("ETag: $etag");
			}
		}
		

		
		if (count($this -> extraHeaders)) {
			foreach ($this -> extraHeaders as $hdr) {
				header($hdr);
			}
		}

		rss_plugin_hook('rss.plugins.bodystart', null);
		
	}
	
	function render() {
		$this -> javascriptFiles 
			= rss_plugin_hook('rss.plugins.javascript', $this -> javascriptFiles);	
		$GLOBALS['rss'] -> header = &$this;
		rss_require(RSS::getTemplateFile("header.php"));
		

		if ($this->extraHTML) {
			echo $this -> extraHTML;
		}
		
	}
}
?>
