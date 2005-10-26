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



/// Name: Tidy HTML filter
/// Author: Marco Bonetti
/// Description: Cleans content using the tidy php extension (required!)
/// Version: 1.4


function __tidy_Tidy($in) {
	// Specify configuration
	$config = array(
				  'indent'        => true,
				  'output-xhtml'  => true,
				  'alt-text' => "-",
				  'doctype' => 'omit',
				  'show-body-only' => true,
				  'wrap' => 0
	);

	if (function_exists('tidy_parse_string')) {
        if (phpversion('tidy') > 1.0) {
    		$tidy = tidy_parse_string($in, $config, 'utf8');
    		tidy_clean_repair($tidy);
    		return $tidy;
		} 
	} elseif (class_exists('tidy')) {
		$tidy = new tidy();
		$tidy -> cleanRepair($in, $config, 'utf8');
		return $tidy;
	}
	return $in;
}
rss_set_hook('rss.plugins.import.description','__tidy_Tidy');
?>
