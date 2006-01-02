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
# E-mail:	   mbonetti at gmail dot com
# Web page:	   http://gregarius.net/
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
    var $renderOptions = IL_NONE;
    var $mainDivId;
    var $feedList;
    var $currentFeedsFolder;
    var $currentFeedsFeed;
    var $nav;
    var $currentNavItem;
    var $sideMenu;
    var $profiler = null;
    var $db = null;
    var $error = null;


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
        static $cache;

        if (!$cache) {
            $cache = array();
        }
        elseif (isset($cache[$file])) {
            return $cache[$file];
        }


				$theme = getActualTheme();
				
        $ret=RSS_THEME_DIR."/$theme/$file";



        /*

        Patch submitted by rene ( at ) evo42 * net. 

        		if you use an new theme and also set the THEME_OVERRIDE to 
        		this theme name you get an 'invisible error' in the getTemplateFile 
        		function (cls/rss.php) at the file_exists function...


        I haven't tested this  :)

        PATH_TRANSLATED is not always populated (depends of PHP/Apache/OS version)
        see remarks about this here: http://www.php.net/reserved.variables
        We must use SCRIPT_FILENAME instead, because it always exists
        */
        $theme_check = RSS_THEME_DIR."/$theme/$file";
        if (isset($_SERVER['SCRIPT_FILENAME']) && ereg('admin', $_SERVER['SCRIPT_FILENAME']))   {
            $theme_path = substr($_SERVER['SCRIPT_FILENAME'],
                                 0, strpos($_SERVER['SCRIPT_FILENAME'], 'admin'));
            $theme_check = $theme_path.$ret;
        }


        if (!file_exists(GREGARIUS_HOME . $theme_check)) {
            $ret= RSS_THEME_DIR."/default/$file";
        }

        $cache[$file] = $ret;

        return $ret;
    }

    function getCachedTemplateFile($file) {
        static $templateCache = array();
        $filename = $this->getTemplateFile($file);
        if (array_key_exists($filename, $templateCache)) {
            return $templateCache[$filename];
        }
        $fileContent = file_get_contents(GREGARIUS_HOME . $filename);
        $modifiedFileContent = eval_mixed($fileContent);
        $templateCache[$filename] = $modifiedFileContent;

        return $modifiedFileContent;

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

        $file = $this->getTemplateFile($template)
                ;
        rss_require($file);

        $this->_pf('end rendering');

        if ($this->profiler) {
            $this->profiler->render();
        }
    }

    function appendContentObject(&$o) {
        $this -> mainObject[] = &$o;
    }

    function error($error, $severity) {
        if ($this -> error == null) {
            rss_require('cls/errorhandler.php');
            $this -> error = new ErrorHandler();
        }
        $this -> error -> appendError($error, $severity);
    }

    function getShownUnreadIds() {
        $ret = array();
        foreach($this->mainObject as $o) {
            if (isset($o->unreadIids)) {
                $ret = array_merge($ret,$o->unreadIids);
            }
        }
        return $ret;
    }

}

$GLOBALS['rss'] = new RSS();


_pf('Parsing class wrappers:');
rss_require('cls/wrappers/errors.php');
_pf(" ...errors.php");
rss_require('cls/wrappers/feed.php');
_pf(" ...feed.php");
rss_require('cls/wrappers/feeds.php');
_pf(" ...feeds.php");
rss_require('cls/wrappers/header.php');
_pf(" ...header.php");
rss_require('cls/wrappers/nav.php');
_pf(" ...nav.php");
rss_require('cls/wrappers/item.php');
_pf(" ...item.php");
rss_require('cls/wrappers/itemlist.php');
_pf(" ...itemlist.php");
rss_require('cls/wrappers/misc.php');
_pf(" ...misc.php");

_pf('Parsed classes');

?>
