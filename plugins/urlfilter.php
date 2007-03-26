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



/// Name: Url filter
/// Author: Marco Bonetti
/// Description: This plugin will try to make ugly URL links look better
/// Version: 1.9

/**
 * Replaces a link in the form <a href="http://www.test.com/a/b/c.html>http://www.test.com/a/b/c.html</a>
 * with a nicer <a href="http://www.test.com/a/b/c.html">[test.com]</a>
 */

 function __urlfilter_filter($in) {
     $match = '#<a[^>]+?href="(.*?)">\\1</a>#im';
     // matches non-linkified URLs
     $match2 = '#[^>"\'/=\?](http[^\s<$]+)[\s$]?#im';
     $ret= preg_replace_callback($match, '__filter_callback', $in);
     $ret2= preg_replace_callback($match2,'__filter_callback', $ret);
     return $ret2;
 }

 /**
  * We need a callback because for some obscure reason the /ie modifier wouldnt work 
  * in preg_replace alone. This basically formats the output
  */
 function __filter_callback($matches) {
     $ret = preg_match("/^(http:\/\/)?([^\/<]+)/i", $matches[1], $outmatches);
     if ($outmatches && isset ($outmatches[2])) {
         return " <a href=\"". $matches[1]."\">[" . $outmatches[2] . "]</a> ";
     }
     return " <a href=\"". $matches[1]."\">[" . $matches[1] . "]</a> ";
 } 


rss_set_hook('rss.plugins.import.description','__urlfilter_filter');


?>
