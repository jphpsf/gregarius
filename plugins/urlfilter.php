<?php
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



/// Name: Url filter
/// Author: Marco Bonetti
/// Description: This plugin will try to make ugly URL links look better
/// Version: 1.4

/**
 * Replaces a link in the form <a href="http://www.test.com/a/b/c.html>http://www.test.com/a/b/c.html</a>
 * with a nicer <a href="http://www.test.com/a/b/c.html">[test.com]</a>
 */
function __urlfilter_filter($in) {
    $match = '|<a href="(.*)">\\1</a>|i';
    return preg_replace_callback($match, '__filter_callback', $in);
}

/**
 * We need a callback because for some obscure reason the /ie modifier wouldnt work 
 * in preg_replace alone. This basically formats the output
 */
function __filter_callback($matches) {
    $ret = preg_match("/^(http:\/\/)?(w*\.)?([^\/]+)/i", $matches[1], $outmatches);
    return "<a href=\"". $matches[1]."\">[" . $outmatches[3] . "]</a>";
} 


rss_set_hook('rss.plugins.import.description','__urlfilter_filter');


?>
