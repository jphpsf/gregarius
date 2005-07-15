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


class RSS {
	var $itemList;
	var $currentItem;
	var $currentFeed;
	var $currentItemList;
	var $currentItemTags;
	var $cntr = 0;
	var $header;
	var $footer;
	var $mainObject = array();
	var $mainDivId;
	var $feedList;
	var $currentFeedsFolder;
	var $currentFeedsFeed;
	var $nav;
	var $currentNavItem;
	var $profiler = null;
	var $db = null;
	
	function RSS() {
	   if (defined('PROFILING') && PROFILING) {
	       rss_require('cls/profiler.php');
	       $this->profiler = new Profiler();
	   }
	}
	
	function _pf($msg) {
	   if ($this->profiler) {
	       $this->profiler->_pf($msg);
	   }
	}
	
	function getTemplateFile($file) {
		/*
		static $templateCache;
		$templateCache=array();
		
		if (array_key_exists($file, $templateCache)) {
			return $templateCache[$file];
		}
		*/
		
		$ret="themes/".getConfig('rss.output.theme')."/$file";

		if (!file_exists($ret)) {
			$ret= "themes/default/$file";
		}
		
		//$templateCache[$file] = $ret;
		return $ret;
	}
	
	function renderWithTemplate($template,$mainDivId="items") {
		$this->_pf('start rendering');
		
		if (!($this->header->options & HDR_NO_OUPUTBUFFERING)) {
			if (getConfig('rss.output.compression')) {
				ob_start('ob_gzhandler');
			} else {
				ob_start();
			}
			// force a content-type and a charset
			header('Content-Type: text/html; charset='
				. (getConfig('rss.output.encoding') ? getConfig('rss.output.encoding') : DEFAULT_OUTPUT_ENCODING));

		}
		
		
		$this -> mainDivId = $mainDivId;
		if (isset($this->header)) {
			$this->header->preRender(); 
		}
		
		$file = $this->getTemplateFile($template);
		require($file);
		
		$this->_pf('end rendering');
		
		if ($this->profiler) {
	       $this->profiler->render();
	   }
	}
	
	function appendContentObject(&$o) {
		$this -> mainObject[] = &$o;  
	}
}

$GLOBALS['rss'] = new RSS();

_pf('Parsing class wrappers:');

rss_require('cls/wrappers/feed.php');      _pf(" ...feed.php");
rss_require('cls/wrappers/feeds.php');     _pf(" ...tfeeds.php");
rss_require('cls/wrappers/header.php');    _pf(" ...header.php");
rss_require('cls/wrappers/nav.php');       _pf(" ...nav.php");
rss_require('cls/wrappers/item.php');      _pf(" ...item.php");
rss_require('cls/wrappers/itemlist.php');  _pf(" ...itemlist.php"); 
rss_require('cls/wrappers/misc.php');      _pf(" ...misc.php");

_pf('Parsed classes');

?>