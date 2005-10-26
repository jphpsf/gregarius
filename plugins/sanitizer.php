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


/// Name: HTML filter
/// Author: Marco Bonetti
/// Description: This plugin fixes some common xhtml markup errors
/// Version: 1.2

function __endslash($in) {
	$wrk = trim($in);
	if (substr($wrk,-1) == '/') {
		return $wrk;
	} else {
		return ($wrk ." /");
	}
}

function __escape_ampersands($in) {
	return str_replace('& ','&amp; ',$in);
}

function __sanitize($in) {
	
	$output= __escape_ampersands($in);
	//<br />
	$output = preg_replace('/<br\s?\/?>/i','<br />',$output);
	//<img>
	$output = preg_replace("/<img([^>]*)alt=([^>]*)>/im", "<img$1`alt=$2>", $output);
	$output = preg_replace("/<img([^`|>]*)>/im", "<img alt=\"\"$1>", $output);
	$output = preg_replace("/<img([^>]*)`alt=([^>]*)>/im", "<img$1alt=$2>", $output);
	$output = preg_replace('/<img([^>]+)>/eim',"'<img '.__endslash('\\1').'>'",$output);

	//lowercase tags
	// Removed: Fix for #111.
	//$output = preg_replace('/<([^\s]+)/eim',"'<'.strtolower('\\1')",$output);
	
	//blockquotes
	$output = str_replace('<blockquote><p>','<blockquote>',$output);
	$output = str_replace('</p></blockquote>','</blockquote>',$output);
	$output = str_replace('<blockquote>','<blockquote><p>',$output);
	$output = str_replace('</blockquote>','</p></blockquote>',$output);
	
	return stripslashes($output);
}

rss_set_hook('rss.plugins.import.description','__sanitize');

//$in = "<img src=\"http://ok\" alt=\"alt\" /><BR><img src=\"http://nok\">";
//echo __sanitize($in) . "\n";
?>
